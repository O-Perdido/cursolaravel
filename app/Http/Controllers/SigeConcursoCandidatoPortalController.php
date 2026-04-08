<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\EmailVerificationCode;
use App\Models\Cidade;
use App\Models\Estado;
use App\Models\SigeConcursoCandidato;
use App\Models\SigeConcursoInscricao;
use App\Models\SigeConcursoInscricaoDocumento;
use App\Models\SigeConcursoInscricaoIsencaoDocumento;
use App\Models\SigeConcursoProcessoLocal;
use App\Models\SigeConcursoProcesso;
use App\Services\InterBolepixService;
use App\Services\InterBolepixManagerService;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SigeConcursoCandidatoPortalController extends Controller
{
    public function showCadastro()
    {
        if (Auth::check()) {
            return redirect()->route($this->routeForNivel((string) (Auth::user()->nivel ?? '')));
        }

        $estados = Estado::orderBy('nm_estado')->get();
        $cidades = $this->resolveCidadesFromOldInput();
        $orgaosExpedidores = $this->orgaoExpedidorOptions();
        $ufs = $this->ufOptions();

        return view('sigeconcursos.candidato.cadastro', compact('estados', 'cidades', 'orgaosExpedidores', 'ufs'));
    }

    public function storeCadastro(Request $request)
    {
        $data = $this->validateCandidato($request, true);

        [$candidato, $user] = DB::transaction(function () use ($data) {
            $candidato = SigeConcursoCandidato::create($data['candidato']);

            $user = new User();
            $strongPassword = $user->validatePassword($data['password']);
            $user->name = $candidato->nome_completo;
            $user->email = $candidato->email;
            $user->nivel = 'candidato';
            $user->fk_id_candidato = $candidato->id_candidato;
            $user->password = Hash::make($strongPassword);
            $user->senha = Crypt::encryptString($strongPassword);
            $user->save();

            return [$candidato, $user];
        });

        $code = $user->startEmailVerification();

        try {
            Mail::to($user->email)->send(new EmailVerificationCode($code, $user->name ?? ''));
        } catch (\Throwable $exception) {
            // O cadastro não deve falhar se houver indisponibilidade momentânea no envio do e-mail.
        }

        return redirect()->route('verification.show', ['user' => $user->id])
            ->with('status', 'Cadastro realizado com sucesso. Enviamos um código para validar seu e-mail.');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route($this->routeForNivel((string) (Auth::user()->nivel ?? '')));
        }

        return view('sigeconcursos.candidato.login');
    }

    public function buscarPorCpf(Request $request)
    {
        $request->validate([
            'cpf' => ['required', 'string'],
        ]);

        $cpf = preg_replace('/\D/', '', $request->cpf);

        if (!$this->validarCpf($cpf)) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'CPF informado é inválido.',
            ], 422);
        }

        $candidato = SigeConcursoCandidato::with('user')
            ->where('numero_cpf', $cpf)
            ->first();

        if (!$candidato) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Nenhum candidato foi encontrado para este CPF.',
                'cadastro_url' => route('sigeconcursos.candidato.cadastro'),
            ]);
        }

        if (!$candidato->user) {
            return response()->json([
                'status' => 'without_user',
                'message' => 'O cadastro foi encontrado, mas não possui usuário vinculado.',
            ], 409);
        }

        return response()->json([
            'status' => 'login_ready',
            'message' => 'Cadastro localizado. Informe seu e-mail e senha para continuar.',
            'email' => $candidato->user->email,
            'nome' => $candidato->nome_completo,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'cpf' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $cpf = preg_replace('/\D/', '', $request->cpf);

        $candidato = SigeConcursoCandidato::with('user')
            ->where('numero_cpf', $cpf)
            ->first();

        if (!$candidato || !$candidato->user) {
            return back()->withInput($request->except('password'))
                ->with('error', 'Não foi possível localizar um acesso de candidato para o CPF informado.');
        }

        if (strcasecmp($candidato->user->email, $request->email) !== 0) {
            return back()->withInput($request->except('password'))
                ->with('error', 'O e-mail informado não corresponde ao cadastro localizado por CPF.');
        }

        // Mantido em e-mail + senha por decisão do projeto. Caso necessário no futuro,
        // este fluxo pode ser adaptado para autenticação direta por CPF + senha.
        if (!Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            return back()->withInput($request->except('password'))
                ->with('error', 'Login inválido.');
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        if (($user->nivel ?? null) !== 'candidato' || (int) $user->fk_id_candidato !== (int) $candidato->id_candidato) {
            Auth::logout();

            return back()->withInput($request->except('password'))
                ->with('error', 'O acesso informado não pertence a um candidato válido.');
        }

        if (empty($user->email_verified_at)) {
            $expiresAt = $user->email_verification_expires_at ?? null;
            $missingOrExpired = empty($user->email_verification_token) || ($expiresAt && now()->greaterThan($expiresAt));

            if ($missingOrExpired) {
                try {
                    $code = $user->startEmailVerification();
                    Mail::to($user->email)->send(new EmailVerificationCode($code, $user->name ?? ''));
                } catch (\Throwable $exception) {
                    // Não impedir o login por indisponibilidade do envio.
                }
            }

            return redirect()->route('verification.show', ['user' => $user->id]);
        }

        return redirect()->route('sigeconcursos.candidato.dashboard');
    }

    public function dashboard()
    {
        $candidato = $this->getCandidatoAutenticado();
        $totalInscricoes = $candidato->inscricoes()->count();
        $inscricoesRecentes = $candidato->inscricoes()
            ->with('processo')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('sigeconcursos.candidato.dashboard', compact('candidato', 'totalInscricoes', 'inscricoesRecentes'));
    }

    public function processos()
    {
        $candidato = $this->getCandidatoAutenticado();

        $query = SigeConcursoProcesso::with(['empresa', 'processoCargos.cargo'])
            ->where('status', '!=', 'rascunho')
            ->orderByDesc('data_publicacao')
            ->orderByDesc('id_processo');

        $filtroInscricao = (string) request('filtro_inscricao', 'todos');

        if ($filtroInscricao === 'abertas') {
            $query->where('status', 'inscricoes_abertas')
                ->where(function ($builder) {
                    $builder->whereNull('data_inicio_inscricoes')
                        ->orWhere('data_inicio_inscricoes', '<=', now());
                })
                ->where(function ($builder) {
                    $builder->whereNull('data_fim_inscricoes')
                        ->orWhere('data_fim_inscricoes', '>=', now());
                });
        }

        if (request()->filled('busca')) {
            $termo = trim((string) request('busca'));
            $query->where(function ($builder) use ($termo) {
                $builder->where('titulo', 'like', '%' . $termo . '%')
                    ->orWhere('numero_edital', 'like', '%' . $termo . '%')
                    ->orWhereHas('empresa', function ($empresaQuery) use ($termo) {
                        $empresaQuery->where('nome_razao_social', 'like', '%' . $termo . '%');
                    });
            });
        }

        $processos = $query->paginate(12)->appends(request()->query());
        $inscricoesDoCandidato = $candidato->inscricoes()
            ->pluck('id_inscricao', 'fk_id_processo')
            ->toArray();

        return view('sigeconcursos.candidato.processos.index', compact('processos', 'inscricoesDoCandidato', 'filtroInscricao'));
    }

    public function processoDetalhes(int $id)
    {
        $candidato = $this->getCandidatoAutenticado();

        $processo = SigeConcursoProcesso::with([
            'empresa',
            'processoCargos.cargo',
            'isencoes',
            'arquivos',
            'documentosExigidos',
        ])->findOrFail($id);

        $inscricaoExistente = SigeConcursoInscricao::with(['documentos.documentoExigido', 'isencao', 'documentosIsencao'])
            ->where('fk_id_processo', $processo->id_processo)
            ->where('fk_id_candidato', $candidato->id_candidato)
            ->first();

        $podeInscrever = $this->processoComInscricoesAbertas($processo);

        return view('sigeconcursos.candidato.processos.show', compact('processo', 'inscricaoExistente', 'podeInscrever'));
    }

    public function inscreverProcesso(Request $request, int $id)
    {
        $candidato = $this->getCandidatoAutenticado();
        $processo = SigeConcursoProcesso::with(['documentosExigidos', 'isencoes', 'processoCargos'])->findOrFail($id);

        if (!$this->processoComInscricoesAbertas($processo)) {
            return back()->with('error', 'Este processo não está com inscrições abertas neste momento.');
        }

        $inscricaoJaExiste = SigeConcursoInscricao::where('fk_id_processo', $processo->id_processo)
            ->where('fk_id_candidato', $candidato->id_candidato)
            ->exists();

        if ($inscricaoJaExiste) {
            return back()->with('error', 'Você já possui inscrição neste processo.');
        }

        $modalidadesPermitidas = [];
        if ($processo->permite_ampla_concorrencia) {
            $modalidadesPermitidas[] = 'ampla_concorrencia';
        }
        if ($processo->permite_pcd) {
            $modalidadesPermitidas[] = 'pcd';
        }

        if (empty($modalidadesPermitidas)) {
            return back()->with('error', 'Este processo está sem modalidade de concorrência habilitada.');
        }

        $rules = [
            'modalidade_concorrencia' => ['required', Rule::in($modalidadesPermitidas)],
            'solicitou_nome_social' => ['nullable'],
            'nome_social' => ['nullable', 'string', 'max:255'],
            'solicitou_condicao_especial' => ['nullable'],
            'descricao_condicao_especial' => ['nullable', 'string', 'max:5000'],
            'documento_condicao_especial' => ['nullable', 'file', 'max:5120'],
            'solicitou_isencao' => ['nullable'],
            'fk_id_isencao' => ['nullable', 'integer', 'exists:sigeconcursos_tb_processo_isencoes,id_isencao'],
            'justificativa_isencao' => ['nullable', 'string', 'max:5000'],
            'isencao_documentos' => ['nullable', 'array'],
            'isencao_documentos.*' => ['nullable', 'file', 'max:5120'],
            'aceite_edital' => [$processo->exige_aceite_edital ? 'accepted' : 'nullable'],
        ];

        foreach ($processo->documentosExigidos as $documentoExigido) {
            $campo = 'documentos_exigidos.' . $documentoExigido->id_documento_exigido;
            $rules[$campo] = [
                $documentoExigido->obrigatorio ? 'required' : 'nullable',
                'file',
                'max:5120',
            ];
        }

        $validated = $request->validate($rules, [
            'aceite_edital.accepted' => 'Você precisa confirmar a leitura do edital para concluir a inscrição.',
        ]);

        $solicitouNomeSocial = $request->boolean('solicitou_nome_social');
        $nomeSocial = trim((string) ($validated['nome_social'] ?? ''));

        if ($solicitouNomeSocial && $nomeSocial === '') {
            throw ValidationException::withMessages([
                'nome_social' => 'Informe o nome social a ser utilizado nesta inscrição.',
            ]);
        }

        $solicitouCondicaoEspecial = $request->boolean('solicitou_condicao_especial');
        $descricaoCondicaoEspecial = trim((string) ($validated['descricao_condicao_especial'] ?? ''));

        if ($solicitouCondicaoEspecial && $descricaoCondicaoEspecial === '') {
            throw ValidationException::withMessages([
                'descricao_condicao_especial' => 'Descreva a condição especial de aplicação solicitada.',
            ]);
        }

        if (!$processo->permite_condicao_especial) {
            $solicitouCondicaoEspecial = false;
            $descricaoCondicaoEspecial = '';
        }

        if ($solicitouCondicaoEspecial && $processo->exige_documento_condicao_especial && !$request->hasFile('documento_condicao_especial')) {
            throw ValidationException::withMessages([
                'documento_condicao_especial' => 'Este processo exige laudo/documento para a condição especial.',
            ]);
        }

        $caminhoDocumentoCondicaoEspecial = null;

        if ($solicitouCondicaoEspecial && $request->hasFile('documento_condicao_especial')) {
            $caminhoDocumentoCondicaoEspecial = $request->file('documento_condicao_especial')
                ->store('sigeconcursos/inscricoes/condicao-especial/processo_' . $processo->id_processo . '/candidato_' . $candidato->id_candidato, 'public');
        }

        $solicitouIsencao = $request->boolean('solicitou_isencao');
        $idIsencaoSelecionada = $request->input('fk_id_isencao');
        $justificativaIsencao = trim((string) $request->input('justificativa_isencao'));

        if (!$processo->possui_taxa_inscricao) {
            $solicitouIsencao = false;
            $idIsencaoSelecionada = null;
            $justificativaIsencao = '';
        }

        if ($solicitouIsencao && $processo->isencoes->isEmpty()) {
            throw ValidationException::withMessages([
                'solicitou_isencao' => 'Este processo não possui casos de isenção cadastrados.',
            ]);
        }

        if ($solicitouIsencao && !$idIsencaoSelecionada) {
            throw ValidationException::withMessages([
                'fk_id_isencao' => 'Selecione o caso de isenção desejado.',
            ]);
        }

        if ($solicitouIsencao && $justificativaIsencao === '') {
            throw ValidationException::withMessages([
                'justificativa_isencao' => 'Descreva a justificativa da solicitação de isenção.',
            ]);
        }

        if ($solicitouIsencao && !$processo->isencoes->contains('id_isencao', (int) $idIsencaoSelecionada)) {
            throw ValidationException::withMessages([
                'fk_id_isencao' => 'Caso de isenção inválido para este processo.',
            ]);
        }

        $statusIsencao = $solicitouIsencao ? 'pendente' : 'nao_solicitada';

        $valorTaxaAplicada = null;
        $statusPagamento = 'nao_aplicavel';

        if ($processo->possui_taxa_inscricao) {
            $valorTaxaAplicada = $this->resolveValorTaxaAplicada($processo, $validated['modalidade_concorrencia']);
            $statusPagamento = $solicitouIsencao ? 'aguardando_isencao' : 'pendente';
        }

        try {
            DB::transaction(function () use (
                $processo,
                $candidato,
                $validated,
                $solicitouNomeSocial,
                $nomeSocial,
                $solicitouCondicaoEspecial,
                $descricaoCondicaoEspecial,
                $caminhoDocumentoCondicaoEspecial,
                $solicitouIsencao,
                $idIsencaoSelecionada,
                $justificativaIsencao,
                $statusIsencao,
                $valorTaxaAplicada,
                $statusPagamento,
                $request
            ) {
                SigeConcursoInscricao::where('fk_id_processo', $processo->id_processo)
                    ->lockForUpdate()
                    ->first();

                $inscricaoExistente = SigeConcursoInscricao::where('fk_id_processo', $processo->id_processo)
                    ->where('fk_id_candidato', $candidato->id_candidato)
                    ->lockForUpdate()
                    ->first();

                if ($inscricaoExistente) {
                    throw ValidationException::withMessages([
                        'modalidade_concorrencia' => 'Você já possui inscrição para este processo.',
                    ]);
                }

                $inscricao = SigeConcursoInscricao::create([
                    'fk_id_processo' => $processo->id_processo,
                    'fk_id_candidato' => $candidato->id_candidato,
                    'numero_inscricao' => SigeConcursoInscricao::gerarNumeroInscricao($processo->id_processo),
                    'modalidade_concorrencia' => $validated['modalidade_concorrencia'],
                    'solicitou_nome_social' => $solicitouNomeSocial,
                    'nome_social' => $solicitouNomeSocial ? $nomeSocial : null,
                    'status_inscricao' => 'inscrito',
                    'aceite_edital' => $request->boolean('aceite_edital'),
                    'solicitou_condicao_especial' => $solicitouCondicaoEspecial,
                    'descricao_condicao_especial' => $solicitouCondicaoEspecial ? $descricaoCondicaoEspecial : null,
                    'caminho_documento_condicao_especial' => $caminhoDocumentoCondicaoEspecial,
                    'solicitou_isencao' => $solicitouIsencao,
                    'fk_id_isencao' => $solicitouIsencao ? (int) $idIsencaoSelecionada : null,
                    'justificativa_isencao' => $solicitouIsencao ? $justificativaIsencao : null,
                    'status_isencao' => $statusIsencao,
                    'valor_taxa_aplicada' => $valorTaxaAplicada,
                    'status_pagamento' => $statusPagamento,
                ]);

                foreach ($processo->documentosExigidos as $documentoExigido) {
                    $arquivo = $request->file('documentos_exigidos.' . $documentoExigido->id_documento_exigido);

                    if (!$arquivo) {
                        continue;
                    }

                    $caminho = $arquivo->store('sigeconcursos/inscricoes/documentos/processo_' . $processo->id_processo . '/inscricao_' . $inscricao->id_inscricao, 'public');

                    SigeConcursoInscricaoDocumento::create([
                        'fk_id_inscricao' => $inscricao->id_inscricao,
                        'fk_id_documento_exigido' => $documentoExigido->id_documento_exigido,
                        'titulo_documento' => $documentoExigido->titulo,
                        'caminho_arquivo' => $caminho,
                    ]);
                }

                if ($solicitouIsencao && $request->hasFile('isencao_documentos')) {
                    foreach ($request->file('isencao_documentos') as $arquivoIsencao) {
                        if (!$arquivoIsencao || !$arquivoIsencao->isValid()) {
                            continue;
                        }

                        $caminhoIsencao = $arquivoIsencao->store('sigeconcursos/inscricoes/isencao/processo_' . $processo->id_processo . '/inscricao_' . $inscricao->id_inscricao, 'public');

                        SigeConcursoInscricaoIsencaoDocumento::create([
                            'fk_id_inscricao' => $inscricao->id_inscricao,
                            'nome_documento' => $arquivoIsencao->getClientOriginalName(),
                            'caminho_arquivo' => $caminhoIsencao,
                        ]);
                    }
                }
            });
        } catch (QueryException $exception) {
            return back()->withInput()->with('error', 'Não foi possível concluir sua inscrição neste momento. Tente novamente.');
        }

        return redirect()->route('sigeconcursos.candidato.minhas-inscricoes')
            ->with('success', 'Inscrição realizada com sucesso!');
    }

    public function minhasInscricoes()
    {
        $candidato = $this->getCandidatoAutenticado();

        $inscricoes = $candidato->inscricoes()
            ->with(['processo.empresa', 'documentos', 'isencao', 'documentosIsencao'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('sigeconcursos.candidato.inscricoes.index', compact('inscricoes'));
    }

    public function minhasIsencoes(Request $request)
    {
        $candidato = $this->getCandidatoAutenticado();

        $query = $candidato->inscricoes()
            ->with(['processo.empresa', 'isencao', 'documentosIsencao'])
            ->where('solicitou_isencao', true)
            ->orderByDesc('created_at');

        if ($request->filled('status_isencao')) {
            $query->where('status_isencao', $request->status_isencao);
        }

        $isencoes = $query->paginate(20)->appends($request->query());

        return view('sigeconcursos.candidato.isencoes.index', compact('isencoes'));
    }

    public function meuLocalProva(int $idInscricao)
    {
        $candidato = $this->getCandidatoAutenticado();

        $inscricao = SigeConcursoInscricao::with([
            'processo',
            'localAtribuido.processoLocal.localProva',
            'salaAtribuida.sala.localProva',
        ])->where('id_inscricao', $idInscricao)
            ->where('fk_id_candidato', $candidato->id_candidato)
            ->firstOrFail();

        // Só exibe se o processo está na etapa de local liberado
        if ($inscricao->processo->etapa_fluxo_atual !== 'local_prova_liberado') {
            return back()->with('error', 'As informações de local de prova ainda não foram divulgadas para este processo.');
        }

        // Também precisa estar deferido
        if ($inscricao->status_inscricao !== 'deferido') {
            return back()->with('error', 'Apenas inscrições deferidas possuem local de prova atribuído.');
        }

        return view('sigeconcursos.candidato.local-prova', compact('inscricao'));
    }

    public function comprovanteInscricaoPdf(int $idInscricao)
    {
        $candidato = $this->getCandidatoAutenticado();

        $inscricao = SigeConcursoInscricao::with([
            'processo.empresa',
            'isencao',
        ])->where('id_inscricao', $idInscricao)
            ->where('fk_id_candidato', $candidato->id_candidato)
            ->firstOrFail();

        $pdf = Pdf::loadView('sigeconcursos.candidato.pdf.comprovante-inscricao', [
            'inscricao' => $inscricao,
            'candidato' => $candidato,
            'emitidoEm' => now(),
        ])->setPaper('a4', 'portrait');

        $nomeArquivo = sprintf(
            'comprovante-inscricao-%s-%s.pdf',
            $inscricao->numero_inscricao ?: $inscricao->id_inscricao,
            now()->format('YmdHis')
        );

        return $pdf->download($nomeArquivo);
    }

    public function comprovanteLocalProvaPdf(int $idInscricao)
    {
        $candidato = $this->getCandidatoAutenticado();

        $inscricao = SigeConcursoInscricao::with([
            'processo.empresa',
            'localAtribuido.processoLocal.localProva',
            'salaAtribuida.sala.localProva',
        ])->where('id_inscricao', $idInscricao)
            ->where('fk_id_candidato', $candidato->id_candidato)
            ->firstOrFail();

        if ($inscricao->processo->etapa_fluxo_atual !== 'local_prova_liberado') {
            return back()->with('error', 'As informações de local de prova ainda não foram divulgadas para este processo.');
        }

        if ($inscricao->status_inscricao !== 'deferido') {
            return back()->with('error', 'Apenas inscrições deferidas possuem local de prova atribuído.');
        }

        $localAtribuido = $inscricao->localAtribuido?->processoLocal?->localProva;
        $salaAtribuida = $inscricao->salaAtribuida?->sala;

        if (!$localAtribuido && !$salaAtribuida) {
            return back()->with('error', 'Seu local/sala de prova ainda não foi atribuído.');
        }

        $pdf = Pdf::loadView('sigeconcursos.candidato.pdf.comprovante-local-prova', [
            'inscricao' => $inscricao,
            'candidato' => $candidato,
            'emitidoEm' => now(),
        ])->setPaper('a4', 'portrait');

        $nomeArquivo = sprintf(
            'comprovante-local-prova-%s-%s.pdf',
            $inscricao->numero_inscricao ?: $inscricao->id_inscricao,
            now()->format('YmdHis')
        );

        return $pdf->download($nomeArquivo);
    }

    public function gerarBoletoInscricao(int $idInscricao, InterBolepixManagerService $manager)
    {
        $candidato = $this->getCandidatoAutenticado();
        $inscricao = $this->carregarInscricaoCandidato($candidato, $idInscricao);

        $emissao = $manager->emitirParaInscricao($inscricao);

        if (!($emissao['success'] ?? false)) {
            return back()->with('error', $emissao['message'] ?? 'Não foi possível emitir o boleto no Inter no momento.');
        }

        return $this->sincronizarBoletoInscricao($idInscricao, $manager);
    }

    public function sincronizarBoletoInscricao(int $idInscricao, InterBolepixManagerService $manager)
    {
        $candidato = $this->getCandidatoAutenticado();
        $inscricao = $this->carregarInscricaoCandidato($candidato, $idInscricao);

        $resultado = $manager->sincronizarInscricao($inscricao, 'candidato');

        if (!($resultado['success'] ?? false)) {
            return back()->with('error', $resultado['message'] ?? 'Não foi possível atualizar o status do pagamento no momento.');
        }

        return back()->with('success', $resultado['message'] ?? 'Situação do boleto atualizada com sucesso.');
    }

    public function baixarBoletoInscricaoPdf(int $idInscricao, InterBolepixManagerService $manager)
    {
        $candidato = $this->getCandidatoAutenticado();
        $inscricao = $this->carregarInscricaoCandidato($candidato, $idInscricao);

        $resultado = $manager->recuperarPdf($inscricao);

        if (!($resultado['success'] ?? false)) {
            return back()->with('error', $resultado['message'] ?? 'Não foi possível recuperar o PDF do boleto no momento.');
        }

        $nomeArquivo = sprintf(
            'boleto-inscricao-%s.pdf',
            $inscricao->numero_inscricao ?: $inscricao->id_inscricao
        );

        return response($resultado['pdf'], 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"',
        ]);
    }

    public function perfil()
    {
        $candidato = $this->getCandidatoAutenticado();

        return view('sigeconcursos.candidato.perfil', compact('candidato'));
    }

    public function editarPerfil()
    {
        $candidato = $this->getCandidatoAutenticado();
        $estados = Estado::orderBy('nm_estado')->get();
        $cidades = $this->resolveCidadesFromOldInput($candidato?->cidade?->fk_id_estado);
        $orgaosExpedidores = $this->orgaoExpedidorOptions();
        $ufs = $this->ufOptions();

        return view('sigeconcursos.candidato.editar', compact('candidato', 'estados', 'cidades', 'orgaosExpedidores', 'ufs'));
    }

    public function atualizarPerfil(Request $request)
    {
        $candidato = $this->getCandidatoAutenticado();
        $data = $this->validateCandidato($request, false, $candidato);

        DB::transaction(function () use ($candidato, $data) {
            $candidato->update($data['candidato']);

            if ($candidato->user && $candidato->user->email !== $data['candidato']['email']) {
                $candidato->user->update([
                    'email' => $data['candidato']['email'],
                    'name' => $data['candidato']['nome_completo'],
                ]);
            } elseif ($candidato->user && $candidato->user->name !== $data['candidato']['nome_completo']) {
                $candidato->user->update([
                    'name' => $data['candidato']['nome_completo'],
                ]);
            }
        });

        return redirect()->route('sigeconcursos.candidato.perfil')
            ->with('success', 'Dados atualizados com sucesso!');
    }

    private function getCandidatoAutenticado(): SigeConcursoCandidato
    {
        $user = Auth::user();

        return SigeConcursoCandidato::with(['cidade.estado', 'user'])
            ->findOrFail($user->fk_id_candidato);
    }

    private function carregarInscricaoCandidato(SigeConcursoCandidato $candidato, int $idInscricao): SigeConcursoInscricao
    {
        return SigeConcursoInscricao::with([
            'processo.empresa',
            'candidato.cidade.estado',
        ])->where('id_inscricao', $idInscricao)
            ->where('fk_id_candidato', $candidato->id_candidato)
            ->firstOrFail();
    }


    private function validateCandidato(Request $request, bool $creating = true, ?SigeConcursoCandidato $candidato = null): array
    {
        $diaNascimento = $request->input('data_nascimento_dia');
        $mesNascimento = $request->input('data_nascimento_mes');
        $anoNascimento = $request->input('data_nascimento_ano');

        if ($diaNascimento !== null || $mesNascimento !== null || $anoNascimento !== null) {
            if (is_numeric($diaNascimento) && is_numeric($mesNascimento) && is_numeric($anoNascimento)) {
                $request->merge([
                    'data_nascimento' => sprintf(
                        '%04d-%02d-%02d',
                        (int) $anoNascimento,
                        (int) $mesNascimento,
                        (int) $diaNascimento
                    ),
                ]);
            }
        }

        $request->merge([
            'numero_cpf' => $this->onlyDigits($request->input('numero_cpf')),
            'numero_cep' => $this->onlyDigits($request->input('numero_cep')),
            'numero_telefone' => $this->onlyDigits($request->input('numero_telefone')),
            'numero_celular' => $this->onlyDigits($request->input('numero_celular')),
            'uf_rg' => strtoupper((string) $request->input('uf_rg')),
            'nome_completo' => trim((string) $request->input('nome_completo')),
            'nome_mae' => trim((string) $request->input('nome_mae')),
            'nacionalidade' => trim((string) $request->input('nacionalidade')),
            'naturalidade_cidade' => trim((string) $request->input('naturalidade_cidade')),
            'naturalidade_estado' => trim((string) $request->input('naturalidade_estado')),
            'orgao_expedidor_rg' => trim((string) $request->input('orgao_expedidor_rg')),
        ]);

        $candidateId = $candidato?->id_candidato;
        $userId = $candidato?->user?->id;

        $rules = [
            'nome_completo' => ['required', 'string', 'max:255'],
            'data_nascimento_dia' => ['required', 'integer', 'between:1,31'],
            'data_nascimento_mes' => ['required', 'integer', 'between:1,12'],
            'data_nascimento_ano' => ['required', 'integer', 'between:1900,' . now()->year],
            'data_nascimento' => ['required', 'date'],
            'sexo' => ['required', Rule::in(['Masculino', 'Feminino', 'Não declarar'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('sigeconcursos_tb_candidatos', 'email')->ignore($candidateId, 'id_candidato')],
            'numero_rg' => ['required', 'string', 'max:30'],
            'orgao_expedidor_rg' => ['required', Rule::in(array_keys($this->orgaoExpedidorOptions()))],
            'uf_rg' => ['required', Rule::in(array_keys($this->ufOptions()))],
            'nome_mae' => ['required', 'string', 'max:255'],
            'nacionalidade' => ['required', 'string', 'max:100'],
            'naturalidade_cidade' => ['required', 'string', 'max:150'],
            'naturalidade_estado' => ['required', 'string', 'max:150'],
            'canhoto' => ['required', Rule::in(['sim', 'nao'])],
            'numero_cep' => ['required', 'digits:8'],
            'endereco' => ['required', 'string', 'max:255'],
            'numero_endereco' => ['required', 'string', 'max:20'],
            'complemento_endereco' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'fk_id_cidade' => ['required', 'exists:tb_cidade,id_cidade'],
            'numero_telefone' => ['nullable', 'digits_between:10,11'],
            'numero_celular' => ['required', 'digits_between:10,11'],
        ];

        if ($creating) {
            $rules['numero_cpf'] = ['required', 'digits:11', Rule::unique('sigeconcursos_tb_candidatos', 'numero_cpf')];
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules, [
            'numero_cpf.unique' => 'Já existe um candidato cadastrado com este CPF.',
            'email.unique' => 'Já existe um candidato cadastrado com este e-mail.',
        ]);

        if (!checkdate((int) $validated['data_nascimento_mes'], (int) $validated['data_nascimento_dia'], (int) $validated['data_nascimento_ano'])) {
            throw ValidationException::withMessages([
                'data_nascimento' => 'Informe uma data de nascimento válida.',
            ]);
        }

        $validated['data_nascimento'] = sprintf(
            '%04d-%02d-%02d',
            (int) $validated['data_nascimento_ano'],
            (int) $validated['data_nascimento_mes'],
            (int) $validated['data_nascimento_dia']
        );

        if ($creating && !$this->validarCpf($validated['numero_cpf'])) {
            throw ValidationException::withMessages([
                'numero_cpf' => 'O CPF informado é inválido.',
            ]);
        }

        $this->ensureEmailAvailableForCandidate($validated['email'], $userId);

        $dadosCandidato = [
            'nome_completo' => mb_strtoupper($validated['nome_completo']),
            'data_nascimento' => $validated['data_nascimento'],
            'sexo' => $validated['sexo'],
            'email' => mb_strtolower($validated['email']),
            'numero_rg' => $validated['numero_rg'],
            'orgao_expedidor_rg' => mb_strtoupper($validated['orgao_expedidor_rg']),
            'uf_rg' => mb_strtoupper($validated['uf_rg']),
            'nome_mae' => mb_strtoupper($validated['nome_mae']),
            'nacionalidade' => mb_strtoupper($validated['nacionalidade']),
            'naturalidade_cidade' => mb_strtoupper($validated['naturalidade_cidade']),
            'naturalidade_estado' => mb_strtoupper($validated['naturalidade_estado']),
            'canhoto' => $validated['canhoto'],
            'numero_cep' => $validated['numero_cep'],
            'endereco' => mb_strtoupper($validated['endereco']),
            'numero_endereco' => $validated['numero_endereco'],
            'complemento_endereco' => $validated['complemento_endereco'] ? mb_strtoupper($validated['complemento_endereco']) : null,
            'bairro' => mb_strtoupper($validated['bairro']),
            'fk_id_cidade' => $validated['fk_id_cidade'],
            'numero_telefone' => $validated['numero_telefone'] ?? null,
            'numero_celular' => $validated['numero_celular'],
        ];

        if ($creating) {
            $dadosCandidato['numero_cpf'] = $validated['numero_cpf'];
        }

        return [
            'candidato' => $dadosCandidato,
            'password' => $validated['password'] ?? null,
        ];
    }

    private function onlyDigits(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value);

        return $digits === '' ? null : $digits;
    }

    private function validarCpf(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($digit = 9; $digit < 11; $digit++) {
            $sum = 0;

            for ($index = 0; $index < $digit; $index++) {
                $sum += (int) $cpf[$index] * (($digit + 1) - $index);
            }

            $check = ((10 * $sum) % 11) % 10;

            if ((int) $cpf[$digit] !== $check) {
                return false;
            }
        }

        return true;
    }

    private function routeForNivel(string $nivel): string
    {
        return match ($nivel) {
            'admin', 'operador' => 'welcome.admin',
            'empresa' => 'welcome.empresa',
            'estagiario' => 'welcome.estagiario',
            'candidato' => 'sigeconcursos.candidato.dashboard',
            default => 'login',
        };
    }

    private function resolveCidadesFromOldInput(?int $fallbackEstadoId = null)
    {
        $estadoId = session()->getOldInput('fk_id_estado', $fallbackEstadoId);

        if (!$estadoId) {
            return collect();
        }

        return Cidade::where('fk_id_estado', $estadoId)
            ->orderBy('nm_cidade')
            ->get();
    }

    private function ensureEmailAvailableForCandidate(string $email, ?int $currentUserId = null): void
    {
        $normalizedEmail = mb_strtolower($email);

        $existingUser = User::whereRaw('LOWER(email) = ?', [$normalizedEmail])->first();

        if (!$existingUser || ($currentUserId && (int) $existingUser->id === (int) $currentUserId)) {
            return;
        }

        if (($existingUser->nivel ?? null) !== 'candidato') {
            session()->flash('candidate_email_platform_conflict', [
                'message' => 'Este e-mail já está cadastrado na plataforma de estágios do SIGE.',
                'url' => 'https://api.whatsapp.com/send?phone=5548991468761&text=Ol%C3%A1%21%20Preciso%20de%20ajuda%20com%20o%20cadastro%20de%20candidato%20no%20SIGE%20Concursos%20porque%20meu%20e-mail%20j%C3%A1%20est%C3%A1%20em%20uso%20na%20plataforma.',
            ]);

            throw ValidationException::withMessages([
                'email' => 'Este e-mail já está cadastrado na plataforma de estágios do SIGE. Informe um e-mail diferente. Caso precise manter este mesmo endereço, entre em contato com o suporte.',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => 'Já existe um acesso de candidato cadastrado com este e-mail.',
        ]);
    }

    private function processoComInscricoesAbertas(SigeConcursoProcesso $processo): bool
    {
        return $processo->inscricoesAbertasAgora();
    }

    private function resolveValorTaxaAplicada(SigeConcursoProcesso $processo, string $modalidade): ?float
    {
        $taxaPorCargo = $processo->processoCargos()
            ->whereNotNull('valor_taxa_inscricao')
            ->where('valor_taxa_inscricao', '>', 0)
            ->orderBy('valor_taxa_inscricao')
            ->value('valor_taxa_inscricao');

        if ($taxaPorCargo !== null) {
            return (float) $taxaPorCargo;
        }

        if ($processo->valor_taxa_padrao !== null) {
            return (float) $processo->valor_taxa_padrao;
        }

        return $modalidade === 'pcd' ? 0.0 : null;
    }

    private function orgaoExpedidorOptions(): array
    {
        return [
            'SSP' => 'Secretaria de Segurança Pública.',
            'SESP' => 'Secretaria de Estado de Segurança Pública.',
            'PC' => 'Polícia Civil.',
            'IITB' => 'Instituto de Identificação Tavares Buril (Pernambuco).',
            'IIPR' => 'Instituto de Identificação do Paraná.',
            'IIRGD' => 'Instituto de Identificação Ricardo G. D. de A. (São Paulo).',
            'IFP' => 'Instituto Félix Pacheco (Rio de Janeiro).',
            'IC' => 'Instituto de Criminalística.',
            'DETRAN' => 'Departamento de Trânsito',
            'MD/PC' => 'Ministério da Defesa - Polícia Civil.',
            'PM/PC' => 'Polícia Militar - Polícia Civil.',
        ];
    }

    private function ufOptions(): array
    {
        return [
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapá',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceará',
            'DF' => 'Distrito Federal',
            'ES' => 'Espírito Santo',
            'GO' => 'Goiás',
            'MA' => 'Maranhão',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Pará',
            'PB' => 'Paraíba',
            'PR' => 'Paraná',
            'PE' => 'Pernambuco',
            'PI' => 'Piauí',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondônia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'São Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins',
        ];
    }
}