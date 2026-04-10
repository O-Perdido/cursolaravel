<?php

namespace App\Http\Controllers;

use App\Models\SigeConcursoCargo;
use App\Models\SigeConcursoEmpresa;
use App\Models\SigeConcursoInscricao;
use App\Models\SigeConcursoInscricaoLocal;
use App\Models\SigeConcursoInscricaoSala;
use App\Models\SigeConcursoLocalProva;
use App\Models\SigeConcursoProcesso;
use App\Models\SigeConcursoProcessoArquivo;
use App\Models\SigeConcursoProcessoDocumentoExigido;
use App\Models\SigeConcursoProcessoLocal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class SigeConcursoProcessoController extends Controller
{
    public function index(Request $request)
    {
        $query = SigeConcursoProcesso::with(['empresa', 'processoCargos.cargo', 'processoLocais.localProva']);

        if ($request->filled('titulo')) {
            $query->where('titulo', 'like', '%' . $request->titulo . '%');
        }

        if ($request->filled('numero_edital')) {
            $query->where('numero_edital', 'like', '%' . $request->numero_edital . '%');
        }

        if ($request->filled('fk_id_empresa')) {
            $query->where('fk_id_empresa', $request->fk_id_empresa);
        }

        if ($request->filled('tipo_processo')) {
            $query->where('tipo_processo', $request->tipo_processo);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_publicacao_inicio')) {
            $query->where('data_publicacao', '>=', $request->data_publicacao_inicio . ' 00:00:00');
        }

        if ($request->filled('data_publicacao_fim')) {
            $query->where('data_publicacao', '<=', $request->data_publicacao_fim . ' 23:59:59');
        }

        if ($request->boolean('ordem_cadastro')) {
            $query->orderByDesc('id_processo');
        } else {
            $query->orderByRaw('data_publicacao IS NULL, data_publicacao DESC')
                ->orderByDesc('id_processo');
        }

        $perPage = $this->resolvePerPage($request->input('per_page'), $query->count());
        $processos = $query->paginate($perPage)->appends($request->query());
        $orgaos = SigeConcursoEmpresa::orderBy('nome_razao_social')->get(['id_empresa', 'nome_razao_social']);

        return view('sigeconcursos.processos.index', compact('processos', 'orgaos'));
    }

    public function create()
    {
        $orgaos = SigeConcursoEmpresa::orderBy('nome_razao_social')->get();
        $cargos = SigeConcursoCargo::where('ativo', true)->orderBy('nome_cargo')->get();
        $locais = SigeConcursoLocalProva::with('cidade.estado')->where('ativo', true)->orderBy('nome_local')->get();

        return view('sigeconcursos.processos.create', compact('orgaos', 'cargos', 'locais'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $fases = $this->formatarFases($request->input('fases', []));
        $cargos = $this->formatarCargos($request->input('cargos', []));
        $locais = $this->formatarLocais($request->input('locais', []));
        $isencoes = $this->formatarIsencoes($request->input('isencoes', []));
        $documentosExigidos = $this->formatarDocumentosExigidos($request->input('documentos_exigidos', []));

        if (empty($cargos)) {
            throw ValidationException::withMessages([
                'cargos' => 'Adicione ao menos um cargo ao processo.',
            ]);
        }

        $processo = DB::transaction(function () use ($data, $fases, $cargos, $locais, $isencoes, $documentosExigidos) {
            $processo = SigeConcursoProcesso::create(array_merge($data, [
                'fases' => !empty($fases) ? $fases : null,
                'numero_processo' => null,
            ]));

            $processo->update([
                'numero_processo' => SigeConcursoProcesso::formatarNumeroProcesso($processo->id_processo),
            ]);

            $processo->processoCargos()->createMany($cargos);
            $processo->processoLocais()->createMany($locais);
            $processo->isencoes()->createMany($isencoes);
            $processo->documentosExigidos()->createMany($documentosExigidos);

            return $processo;
        });

        $this->sincronizarFluxoProcesso($processo);

        $this->salvarArquivos($request, $processo);
        $this->salvarIcone($request, $processo);

        return redirect()->route('sigeconcursos.processos.index')
            ->with('success', 'Processo cadastrado com sucesso!');
    }

    public function show($id)
    {
        $processo = SigeConcursoProcesso::with([
            'empresa.cidade.estado',
            'processoCargos.cargo',
            'processoLocais.localProva.cidade.estado',
            'processoLocais.localProva.salas',
            'isencoes',
            'arquivos',
            'documentosExigidos',
        ])->findOrFail($id);

        return view('sigeconcursos.processos.show', compact('processo'));
    }

    public function edit($id)
    {
        $processo = SigeConcursoProcesso::with(['processoCargos.cargo', 'processoLocais.localProva', 'isencoes', 'arquivos', 'documentosExigidos'])
            ->findOrFail($id);
        $orgaos = SigeConcursoEmpresa::orderBy('nome_razao_social')->get();
        $cargos = SigeConcursoCargo::where('ativo', true)->orderBy('nome_cargo')->get();
        $locais = SigeConcursoLocalProva::with('cidade.estado')->where('ativo', true)->orderBy('nome_local')->get();

        return view('sigeconcursos.processos.edit', compact('processo', 'orgaos', 'cargos', 'locais'));
    }

    public function update(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);
        $data = $this->validateData($request);
        $fases = $this->formatarFases($request->input('fases', []));
        $cargos = $this->formatarCargos($request->input('cargos', []));
        $locais = $this->formatarLocais($request->input('locais', []));
        $isencoes = $this->formatarIsencoes($request->input('isencoes', []));
        $documentosExigidos = $this->formatarDocumentosExigidos($request->input('documentos_exigidos', []));

        if (empty($cargos)) {
            throw ValidationException::withMessages([
                'cargos' => 'Adicione ao menos um cargo ao processo.',
            ]);
        }

        DB::transaction(function () use ($processo, $data, $fases, $cargos, $locais, $isencoes, $documentosExigidos) {
            $processo->update(array_merge($data, [
                'fases' => !empty($fases) ? $fases : null,
            ]));

            $processo->processoCargos()->delete();
            $processo->processoLocais()->delete();
            $processo->isencoes()->delete();
            $processo->documentosExigidos()->delete();

            $processo->processoCargos()->createMany($cargos);
            $processo->processoLocais()->createMany($locais);
            $processo->isencoes()->createMany($isencoes);
            $processo->documentosExigidos()->createMany($documentosExigidos);
        });

        $this->sincronizarFluxoProcesso($processo);

        $this->salvarArquivos($request, $processo);
        $this->salvarIcone($request, $processo);

        return redirect()->route('sigeconcursos.processos.index')
            ->with('success', 'Processo atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $processo = SigeConcursoProcesso::with('arquivos')->findOrFail($id);

        try {
            foreach ($processo->arquivos as $arquivo) {
                if (Storage::disk('public')->exists($arquivo->caminho_arquivo)) {
                    Storage::disk('public')->delete($arquivo->caminho_arquivo);
                }
            }

            if ($processo->icone_processo && Storage::disk('public')->exists($processo->icone_processo)) {
                Storage::disk('public')->delete($processo->icone_processo);
            }

            $processo->delete();

            return redirect()->route('sigeconcursos.processos.index')
                ->with('success', 'Processo excluído com sucesso!');
        } catch (QueryException $exception) {
            return redirect()->route('sigeconcursos.processos.index')
                ->with('error', 'Não foi possível excluir o processo porque ele possui vínculos no sistema.');
        }
    }

    public function removerArquivo($id)
    {
        $arquivo = SigeConcursoProcessoArquivo::findOrFail($id);

        if (Storage::disk('public')->exists($arquivo->caminho_arquivo)) {
            Storage::disk('public')->delete($arquivo->caminho_arquivo);
        }

        $arquivo->delete();

        return back()->with('success', 'Arquivo removido com sucesso!');
    }

    public function removerDocumentoExigido($id)
    {
        $documento = SigeConcursoProcessoDocumentoExigido::findOrFail($id);
        $documento->delete();

        return back()->with('success', 'Documento exigido removido com sucesso!');
    }

    public function inscricoes(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::with(['empresa'])->findOrFail($id);

        $query = $this->aplicarFiltrosInscricoes($request, $this->montarConsultaInscricoes($processo));

        $inscricoes = $query->orderByDesc('created_at')->paginate(25)->appends($request->query());
        $resumo = $this->montarResumoInscricoes(clone $query);

        return view('sigeconcursos.processos.inscricoes', compact('processo', 'inscricoes', 'resumo'));
    }

    public function exportarInscricoes(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::with(['empresa'])->findOrFail($id);

        $validated = $request->validate([
            'formato' => ['nullable', 'in:pdf,excel'],
            'cpf_censurado' => ['nullable', 'boolean'],
            'nome' => ['nullable', 'string', 'max:255'],
            'cpf' => ['nullable', 'string', 'max:20'],
            'modalidade_concorrencia' => ['nullable', 'in:ampla_concorrencia,pcd'],
            'status_inscricao' => ['nullable', 'in:inscrito,deferido,indeferido'],
            'status_isencao' => ['nullable', 'in:nao_solicitada,pendente,deferida,indeferida'],
            'status_pagamento' => ['nullable', 'in:nao_aplicavel,pendente,aguardando_isencao,isento,pago'],
        ]);

        $formato = $validated['formato'] ?? 'pdf';
        $cpfCensurado = $request->boolean('cpf_censurado', true);
        $query = $this->aplicarFiltrosInscricoes($request, $this->montarConsultaInscricoes($processo));
        $inscricoes = $query->orderByDesc('created_at')->get();

        if ($inscricoes->isEmpty()) {
            return back()->with('error', 'Nenhuma inscrição encontrada para exportar com os filtros atuais.');
        }

    $linhas = $this->montarLinhasExportacaoInscricoes($inscricoes, $cpfCensurado);
        $filtrosAplicados = $this->montarFiltrosAplicadosInscricoes($validated);
        $sufixoFiltro = $validated['status_inscricao'] ?? 'filtrado';
        $nomeArquivo = 'inscricoes_' . Str::slug($processo->titulo ?: 'processo') . '_' . Str::slug($sufixoFiltro) . '_' . now()->format('Ymd_His');

        if ($formato === 'excel') {
            return Excel::download(
                new \App\Exports\SigeConcursoInscricoesExport($linhas),
                $nomeArquivo . '.xlsx'
            );
        }

        $pdf = Pdf::loadView('sigeconcursos.processos.exports.inscricoes-pdf', [
            'processo' => $processo,
            'linhas' => $linhas,
            'cpfCensurado' => $cpfCensurado,
            'filtrosAplicados' => $filtrosAplicados,
            'dataExportacao' => now()->format('d/m/Y H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($nomeArquivo . '.pdf');
    }

    private function montarConsultaInscricoes(SigeConcursoProcesso $processo): Builder
    {
        return SigeConcursoInscricao::with([
            'candidato',
            'documentos.documentoExigido',
            'isencao',
            'documentosIsencao',
            'localAtribuido.processoLocal.localProva',
            'salaAtribuida.sala.localProva',
        ])->where('fk_id_processo', $processo->id_processo);
    }

    private function aplicarFiltrosInscricoes(Request $request, Builder $query): Builder
    {
        if ($request->filled('nome')) {
            $nome = trim((string) $request->input('nome'));
            $query->whereHas('candidato', function ($candidatoQuery) use ($nome) {
                $candidatoQuery->where('nome_completo', 'like', '%' . $nome . '%');
            });
        }

        if ($request->filled('cpf')) {
            $cpf = preg_replace('/\D/', '', (string) $request->input('cpf'));
            $query->whereHas('candidato', function ($candidatoQuery) use ($cpf) {
                $candidatoQuery->where('numero_cpf', 'like', '%' . $cpf . '%');
            });
        }

        if ($request->filled('modalidade_concorrencia')) {
            $query->where('modalidade_concorrencia', $request->input('modalidade_concorrencia'));
        }

        if ($request->filled('status_inscricao')) {
            $query->where('status_inscricao', $request->input('status_inscricao'));
        }

        if ($request->filled('status_isencao')) {
            $query->where('status_isencao', $request->input('status_isencao'));
        }

        if ($request->filled('status_pagamento')) {
            $query->where('status_pagamento', $request->input('status_pagamento'));
        }

        return $query;
    }

    private function montarResumoInscricoes(Builder $query): array
    {
        return [
            'total' => (clone $query)->count(),
            'deferidas' => (clone $query)->where('status_inscricao', 'deferido')->count(),
            'indeferidas' => (clone $query)->where('status_inscricao', 'indeferido')->count(),
            'pendentes' => (clone $query)->where('status_inscricao', 'inscrito')->count(),
            'aptos' => (clone $query)
                ->where('status_inscricao', 'inscrito')
                ->whereIn('status_pagamento', ['pago', 'isento', 'nao_aplicavel'])
                ->count(),
        ];
    }

    private function montarLinhasExportacaoInscricoes(Collection $inscricoes, bool $cpfCensurado = true): Collection
    {
        return $inscricoes->map(function (SigeConcursoInscricao $inscricao) use ($cpfCensurado) {
            $candidato = $inscricao->candidato;
            $sala = $inscricao->salaAtribuida?->sala;
            $local = $sala?->localProva ?: $inscricao->localAtribuido?->processoLocal?->localProva;

            $partesLocalProva = array_values(array_filter([
                $local?->nome_local,
                $sala?->nome_sala ? 'Sala ' . $sala->nome_sala : null,
                $sala?->bloco ? 'Bloco ' . $sala->bloco : null,
            ]));

            $localProva = !empty($partesLocalProva)
                ? implode(' - ', $partesLocalProva)
                : '-';

            return [
                'numero_inscricao' => $inscricao->numero_inscricao ?: '-',
                'nome' => $candidato?->nome_completo ?: '-',
                'cpf' => $cpfCensurado
                    ? $this->mascararCpf($candidato?->numero_cpf)
                    : $this->formatarCpf($candidato?->numero_cpf),
                'modalidade' => $inscricao->modalidadeLabel(),
                'local_prova' => $localProva,
                'status_inscricao' => $this->humanizarStatus($inscricao->status_inscricao),
                'status_isencao' => $this->humanizarStatus($inscricao->status_isencao),
            ];
        })->values();
    }

    private function montarFiltrosAplicadosInscricoes(array $validated): array
    {
        $filtros = [];

        if (!empty($validated['nome'])) {
            $filtros['Nome'] = trim((string) $validated['nome']);
        }

        if (!empty($validated['cpf'])) {
            $filtros['CPF'] = preg_replace('/\D/', '', (string) $validated['cpf']);
        }

        if (!empty($validated['modalidade_concorrencia'])) {
            $filtros['Modalidade'] = $validated['modalidade_concorrencia'] === 'pcd' ? 'PCD' : 'Ampla Concorrência';
        }

        if (!empty($validated['status_inscricao'])) {
            $filtros['Status da inscrição'] = $this->humanizarStatus($validated['status_inscricao']);
        }

        if (!empty($validated['status_isencao'])) {
            $filtros['Status da isenção'] = $this->humanizarStatus($validated['status_isencao']);
        }

        if (!empty($validated['status_pagamento'])) {
            $filtros['Status do pagamento'] = $this->humanizarStatus($validated['status_pagamento']);
        }

        return $filtros;
    }

    private function humanizarStatus(?string $valor): string
    {
        if (!$valor) {
            return '-';
        }

        return Str::of($valor)
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    private function mascararCpf(?string $cpf): string
    {
        $digitos = preg_replace('/\D/', '', (string) $cpf);

        if (strlen($digitos) !== 11) {
            return $cpf ?: '-';
        }

        return substr($digitos, 0, 3) . '.***.***-' . substr($digitos, -2);
    }

    private function formatarCpf(?string $cpf): string
    {
        $digitos = preg_replace('/\D/', '', (string) $cpf);

        if (strlen($digitos) !== 11) {
            return $cpf ?: '-';
        }

        return substr($digitos, 0, 3) . '.' . substr($digitos, 3, 3) . '.' . substr($digitos, 6, 3) . '-' . substr($digitos, 9, 2);
    }

    private function formatarTelefone(?string $telefone): string
    {
        $digitos = preg_replace('/\D/', '', (string) $telefone);

        if (strlen($digitos) === 11) {
            return sprintf('(%s) %s-%s', substr($digitos, 0, 2), substr($digitos, 2, 5), substr($digitos, 7, 4));
        }

        if (strlen($digitos) === 10) {
            return sprintf('(%s) %s-%s', substr($digitos, 0, 2), substr($digitos, 2, 4), substr($digitos, 6, 4));
        }

        return $telefone ?: '-';
    }

    public function listaPresencaSalasPdf($id)
    {
        $processo = SigeConcursoProcesso::with(['empresa'])->findOrFail($id);

        $salas = SigeConcursoInscricaoSala::with([
            'inscricao.candidato',
            'sala.localProva',
        ])
            ->whereHas('inscricao', function ($query) use ($processo) {
                $query->where('fk_id_processo', $processo->id_processo);
            })
            ->get()
            ->groupBy('fk_id_sala')
            ->map(function (Collection $atribuicoes) {
                $sala = $atribuicoes->first()?->sala;

                return [
                    'sala' => $sala,
                    'local' => $sala?->localProva,
                    'atribuicoes' => $atribuicoes->sortBy('numero_assento')->values(),
                ];
            })
            ->sortBy([
                fn(array $item) => Str::lower((string) ($item['local']?->nome_local ?? '')),
                fn(array $item) => Str::lower((string) ($item['sala']?->nome_sala ?? '')),
            ])
            ->values();

        if ($salas->isEmpty()) {
            return back()->with('error', 'Nenhuma sala com candidatos distribuídos foi encontrada para gerar a lista de presença.');
        }

        $pdf = Pdf::loadView('sigeconcursos.processos.exports.lista-presenca-salas-pdf', [
            'processo' => $processo,
            'salas' => $salas,
            'dataGeracao' => now()->format('d/m/Y H:i:s'),
        ])->setPaper('a4', 'landscape');

        $nomeArquivo = 'lista_presenca_salas_' . Str::slug($processo->titulo ?: 'processo') . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($nomeArquivo);
    }

    public function atualizarStatusInscricao(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        $validated = $request->validate([
            'inscricao_id' => ['required', 'integer', 'exists:sigeconcursos_tb_inscricoes,id_inscricao'],
            'novo_status' => ['required', 'in:inscrito,deferido,indeferido'],
            'observacoes' => ['nullable', 'string'],
        ]);

        $inscricao = SigeConcursoInscricao::where('id_inscricao', $validated['inscricao_id'])
            ->where('fk_id_processo', $processo->id_processo)
            ->firstOrFail();

        $inscricao->update([
            'status_inscricao' => $validated['novo_status'],
            'observacoes' => trim((string) ($validated['observacoes'] ?? '')) ?: null,
        ]);

        $this->sincronizarFluxoProcesso($processo);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Status da inscrição atualizado com sucesso.',
                'inscricao' => [
                    'id_inscricao' => $inscricao->id_inscricao,
                    'status_inscricao' => $inscricao->status_inscricao,
                    'observacoes' => $inscricao->observacoes,
                ],
            ]);
        }

        return back()->with('success', 'Status da inscrição atualizado com sucesso.');
    }

    public function atualizarStatusInscricaoLote(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        $validated = $request->validate([
            'updates' => ['required', 'array', 'min:1'],
            'updates.*.inscricao_id' => ['required', 'integer', 'exists:sigeconcursos_tb_inscricoes,id_inscricao'],
            'updates.*.novo_status' => ['required', 'in:inscrito,deferido,indeferido'],
            'updates.*.observacoes' => ['nullable', 'string'],
        ]);

        $atualizadas = [];

        DB::transaction(function () use ($validated, $processo, &$atualizadas) {
            foreach ($validated['updates'] as $update) {
                $inscricao = SigeConcursoInscricao::where('id_inscricao', $update['inscricao_id'])
                    ->where('fk_id_processo', $processo->id_processo)
                    ->firstOrFail();

                $inscricao->update([
                    'status_inscricao' => $update['novo_status'],
                    'observacoes' => trim((string) ($update['observacoes'] ?? '')) ?: null,
                ]);

                $atualizadas[] = [
                    'id_inscricao' => $inscricao->id_inscricao,
                    'status_inscricao' => $inscricao->status_inscricao,
                    'observacoes' => $inscricao->observacoes,
                ];
            }
        });

        $this->sincronizarFluxoProcesso($processo);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Alterações em lote salvas com sucesso.',
                'total' => count($atualizadas),
                'inscricoes' => $atualizadas,
            ]);
        }

        return back()->with('success', 'Alterações em lote salvas com sucesso.');
    }
    
    public function atualizarStatusIsencao(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        $validated = $request->validate([
            'inscricao_id' => ['required', 'integer', 'exists:sigeconcursos_tb_inscricoes,id_inscricao'],
            'novo_status_isencao' => ['required', 'in:nao_solicitada,pendente,deferida,indeferida'],
            'parecer_isencao' => ['nullable', 'string'],
        ]);

        $inscricao = SigeConcursoInscricao::where('id_inscricao', $validated['inscricao_id'])
            ->where('fk_id_processo', $processo->id_processo)
            ->firstOrFail();

        $novoStatusIsencao = $validated['novo_status_isencao'];
        $novoStatusPagamento = $inscricao->status_pagamento;

        if ($processo->possui_taxa_inscricao) {
            if ($novoStatusIsencao === 'deferida') {
                $novoStatusPagamento = 'isento';
            } elseif ($novoStatusIsencao === 'indeferida' || $novoStatusIsencao === 'nao_solicitada') {
                $novoStatusPagamento = 'pendente';
            } elseif ($novoStatusIsencao === 'pendente') {
                $novoStatusPagamento = 'aguardando_isencao';
            }
        } else {
            $novoStatusPagamento = 'nao_aplicavel';
        }

        $inscricao->update([
            'status_isencao' => $novoStatusIsencao,
            'solicitou_isencao' => $novoStatusIsencao !== 'nao_solicitada',
            'parecer_isencao' => trim((string) ($validated['parecer_isencao'] ?? '')) ?: null,
            'status_pagamento' => $novoStatusPagamento,
        ]);

        $this->sincronizarFluxoProcesso($processo);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Status da isenção atualizado com sucesso.',
                'inscricao' => [
                    'id_inscricao' => $inscricao->id_inscricao,
                    'status_isencao' => $inscricao->status_isencao,
                    'parecer_isencao' => $inscricao->parecer_isencao,
                    'status_pagamento' => $inscricao->status_pagamento,
                ],
            ]);
        }

        return back()->with('success', 'Status da isenção atualizado com sucesso.');
    }

    public function atualizarStatusIsencaoLote(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        $validated = $request->validate([
            'updates' => ['required', 'array', 'min:1'],
            'updates.*.inscricao_id' => ['required', 'integer', 'exists:sigeconcursos_tb_inscricoes,id_inscricao'],
            'updates.*.novo_status_isencao' => ['required', 'in:nao_solicitada,pendente,deferida,indeferida'],
            'updates.*.parecer_isencao' => ['nullable', 'string'],
        ]);

        $atualizadas = [];

        DB::transaction(function () use ($validated, $processo, &$atualizadas) {
            foreach ($validated['updates'] as $update) {
                $inscricao = SigeConcursoInscricao::where('id_inscricao', $update['inscricao_id'])
                    ->where('fk_id_processo', $processo->id_processo)
                    ->firstOrFail();

                $novoStatusIsencao = $update['novo_status_isencao'];
                $novoStatusPagamento = $inscricao->status_pagamento;

                if ($processo->possui_taxa_inscricao) {
                    if ($novoStatusIsencao === 'deferida') {
                        $novoStatusPagamento = 'isento';
                    } elseif ($novoStatusIsencao === 'indeferida' || $novoStatusIsencao === 'nao_solicitada') {
                        $novoStatusPagamento = 'pendente';
                    } elseif ($novoStatusIsencao === 'pendente') {
                        $novoStatusPagamento = 'aguardando_isencao';
                    }
                } else {
                    $novoStatusPagamento = 'nao_aplicavel';
                }

                $inscricao->update([
                    'status_isencao' => $novoStatusIsencao,
                    'solicitou_isencao' => $novoStatusIsencao !== 'nao_solicitada',
                    'parecer_isencao' => trim((string) ($update['parecer_isencao'] ?? '')) ?: null,
                    'status_pagamento' => $novoStatusPagamento,
                ]);

                $atualizadas[] = [
                    'id_inscricao' => $inscricao->id_inscricao,
                    'status_isencao' => $inscricao->status_isencao,
                    'parecer_isencao' => $inscricao->parecer_isencao,
                    'status_pagamento' => $inscricao->status_pagamento,
                ];
            }
        });

        $this->sincronizarFluxoProcesso($processo);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Alterações de isenção salvas com sucesso.',
                'total' => count($atualizadas),
                'inscricoes' => $atualizadas,
            ]);
        }

        return back()->with('success', 'Alterações de isenção salvas com sucesso.');
    }

    public function destroyInscricao(Request $request, $id, $inscricao)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        $validated = $request->validate([
            'password_confirm' => ['required', 'string'],
        ]);

        if (!Hash::check($validated['password_confirm'], Auth::user()->password)) {
            return back()->with('error', 'A senha informada não confere com o usuário logado.');
        }

        $inscricaoModel = SigeConcursoInscricao::with([
            'documentos',
            'documentosIsencao',
            'localAtribuido',
            'salaAtribuida',
        ])
            ->where('id_inscricao', $inscricao)
            ->where('fk_id_processo', $processo->id_processo)
            ->firstOrFail();

        try {
            DB::transaction(function () use ($inscricaoModel) {
                foreach ($inscricaoModel->documentos as $documento) {
                    Storage::disk('public')->delete($documento->caminho_arquivo);
                }

                foreach ($inscricaoModel->documentosIsencao as $documentoIsencao) {
                    Storage::disk('public')->delete($documentoIsencao->caminho_arquivo);
                }

                if ($inscricaoModel->caminho_documento_condicao_especial) {
                    Storage::disk('public')->delete($inscricaoModel->caminho_documento_condicao_especial);
                }

                $inscricaoModel->salaAtribuida()?->delete();
                $inscricaoModel->localAtribuido()?->delete();
                $inscricaoModel->delete();
            });

            $this->sincronizarFluxoProcesso($processo);

            return back()->with('success', 'Inscrição excluída com sucesso.');
        } catch (QueryException $exception) {
            return back()->with('error', 'Não foi possível excluir a inscrição por causa de vínculos existentes no sistema.');
        }
    }

    public function isencoes(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::with(['empresa'])->findOrFail($id);

        $query = SigeConcursoInscricao::with([
            'candidato',
            'isencao',
            'documentosIsencao',
        ])->where('fk_id_processo', $processo->id_processo)
            ->where('solicitou_isencao', true);

        if ($request->filled('nome')) {
            $nome = trim((string) $request->nome);
            $query->whereHas('candidato', function ($candidatoQuery) use ($nome) {
                $candidatoQuery->where('nome_completo', 'like', '%' . $nome . '%');
            });
        }

        if ($request->filled('cpf')) {
            $cpf = preg_replace('/\D/', '', (string) $request->cpf);
            $query->whereHas('candidato', function ($candidatoQuery) use ($cpf) {
                $candidatoQuery->where('numero_cpf', 'like', '%' . $cpf . '%');
            });
        }

        if ($request->filled('status_isencao')) {
            $query->where('status_isencao', $request->status_isencao);
        }

        $isencoes = $query->orderByDesc('created_at')->paginate(25)->appends($request->query());

        $resumo = [
            'total' => (clone $query)->count(),
            'pendentes' => (clone $query)->where('status_isencao', 'pendente')->count(),
            'deferidas' => (clone $query)->where('status_isencao', 'deferida')->count(),
            'indeferidas' => (clone $query)->where('status_isencao', 'indeferida')->count(),
        ];

        return view('sigeconcursos.processos.isencoes', compact('processo', 'isencoes', 'resumo'));
    }

    public function distribuicaoLocais(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::with([
            'processoLocais.localProva',
        ])->findOrFail($id);

        $locais = $processo->processoLocais()->with([
            'localProva',
            'inscricoesAtribuidas.inscricao.candidato',
        ])->get();

        $totalDeferidos = SigeConcursoInscricao::where('fk_id_processo', $processo->id_processo)
            ->where('status_inscricao', 'deferido')
            ->count();

        $totalDistribuidos = SigeConcursoInscricaoLocal::whereHas('processoLocal', function ($q) use ($processo) {
            $q->where('fk_id_processo', $processo->id_processo);
        })->count();

        return view('sigeconcursos.processos.distribuicao-locais', compact(
            'processo',
            'locais',
            'totalDeferidos',
            'totalDistribuidos'
        ));
    }

    public function distribuirPorLocais(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        $locais = SigeConcursoProcessoLocal::where('fk_id_processo', $processo->id_processo)
            ->orderBy('id_processo_local')
            ->get();

        if ($locais->isEmpty()) {
            return back()->with('error', 'O processo não possui locais de prova cadastrados.');
        }

        $inscricoesDeferidas = SigeConcursoInscricao::with('candidato')
            ->where('fk_id_processo', $processo->id_processo)
            ->where('status_inscricao', 'deferido')
            ->whereHas('candidato')
            ->join('sigeconcursos_tb_candidatos', 'sigeconcursos_tb_inscricoes.fk_id_candidato', '=', 'sigeconcursos_tb_candidatos.id_candidato')
            ->orderBy('sigeconcursos_tb_candidatos.nome_completo')
            ->select('sigeconcursos_tb_inscricoes.*')
            ->get();

        if ($inscricoesDeferidas->isEmpty()) {
            return back()->with('error', 'Não há candidatos deferidos para distribuir.');
        }

        $totalCandidatos = $inscricoesDeferidas->count();
        $totalLocais = $locais->count();
        $porLocal = (int) floor($totalCandidatos / $totalLocais);
        $resto = $totalCandidatos % $totalLocais;

        DB::transaction(function () use ($inscricoesDeferidas, $locais, $porLocal, $resto) {
            // Limpa distribuição anterior dos candidatos deferidos deste processo
            $idsInscricoes = $inscricoesDeferidas->pluck('id_inscricao');
            SigeConcursoInscricaoLocal::whereIn('fk_id_inscricao', $idsInscricoes)->delete();

            $offset = 0;
            foreach ($locais as $indice => $processoLocal) {
                // Último local recebe o resto
                $quantidade = $porLocal + ($indice === $locais->count() - 1 ? $resto : 0);
                $fatia = $inscricoesDeferidas->slice($offset, $quantidade);

                foreach ($fatia as $inscricao) {
                    SigeConcursoInscricaoLocal::create([
                        'fk_id_inscricao' => $inscricao->id_inscricao,
                        'fk_id_processo_local' => $processoLocal->id_processo_local,
                    ]);
                }

                $offset += $quantidade;
            }
        });

        $this->sincronizarFluxoProcesso($processo);

        return back()->with('success', "Distribuição realizada: {$totalCandidatos} candidatos distribuídos entre {$totalLocais} local(is).");
    }

    public function limparDistribuicaoLocais($id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        $idsProcessoLocais = SigeConcursoProcessoLocal::where('fk_id_processo', $processo->id_processo)
            ->pluck('id_processo_local');

        $removidos = SigeConcursoInscricaoLocal::whereIn('fk_id_processo_local', $idsProcessoLocais)->delete();

        $this->sincronizarFluxoProcesso($processo);

        return back()->with('success', "Distribuição por locais removida ({$removidos} registro(s) excluído(s)).");
    }

    public function distribuicaoSalas(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        // Locais do processo, cada um com suas salas e as inscrições atribuídas a cada sala
        $locais = SigeConcursoProcessoLocal::with([
            'localProva.salas' => function ($q) {
                $q->where('ativo', true)->orderBy('nome_sala');
            },
            'localProva.salas.inscricoesAtribuidas.inscricao.candidato',
        ])->where('fk_id_processo', $processo->id_processo)->get();

        $totalDistribuidosLocal = SigeConcursoInscricaoLocal::whereHas('processoLocal', function ($q) use ($processo) {
            $q->where('fk_id_processo', $processo->id_processo);
        })->count();

        $totalDistribuidosSala = SigeConcursoInscricaoSala::whereHas('sala.localProva.processos', function ($q) use ($processo) {
            $q->where('fk_id_processo', $processo->id_processo);
        })->count();

        return view('sigeconcursos.processos.distribuicao-salas', compact(
            'processo',
            'locais',
            'totalDistribuidosLocal',
            'totalDistribuidosSala'
        ));
    }

    public function distribuirPorSalas(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        // Busca inscrições já atribuídas a locais, com candidato, ordenadas alfabeticamente
        $atribuicoesLocais = SigeConcursoInscricaoLocal::with(['inscricao.candidato', 'processoLocal.localProva.salas' => function ($q) {
            $q->where('ativo', true)->orderBy('nome_sala');
        }])
            ->whereHas('processoLocal', function ($q) use ($processo) {
                $q->where('fk_id_processo', $processo->id_processo);
            })
            ->get()
            ->groupBy('fk_id_processo_local');

        if ($atribuicoesLocais->isEmpty()) {
            return back()->with('error', 'Execute primeiro a distribuição por locais.');
        }

        $idsInscricoes = SigeConcursoInscricaoLocal::whereHas('processoLocal', function ($q) use ($processo) {
            $q->where('fk_id_processo', $processo->id_processo);
        })->pluck('fk_id_inscricao');

        DB::transaction(function () use ($atribuicoesLocais, $idsInscricoes) {
            // Limpa distribuição anterior por salas para este processo
            SigeConcursoInscricaoSala::whereIn('fk_id_inscricao', $idsInscricoes)->delete();

            foreach ($atribuicoesLocais as $idProcessoLocal => $atribuicoes) {
                $salas = $atribuicoes->first()?->processoLocal?->localProva?->salas ?? collect();

                if ($salas->isEmpty()) {
                    continue; // local sem salas cadastradas — pula
                }

                // Ordena candidatos deste local alfabeticamente
                $candidatosLocal = $atribuicoes
                    ->sortBy(fn($a) => $a->inscricao?->candidato?->nome_completo ?? '')
                    ->values();

                $totalCandidatos = $candidatosLocal->count();
                [$salasSelecionadas, $metasPorSala] = $this->montarPlanoDistribuicaoSalas($salas, $totalCandidatos);

                if ($salasSelecionadas->isEmpty()) {
                    continue;
                }

                $salaIndex = 0;
                $salaAtual = $salasSelecionadas->get(0);
                $ocupacaoPorSala = [];
                $assentoPorSala = [];

                foreach ($candidatosLocal as $atribuicao) {
                    // Avança para a próxima sala quando a meta balanceada da sala atual for atingida.
                    while (
                        $salaAtual !== null &&
                        ($ocupacaoPorSala[$salaAtual->id_sala] ?? 0) >= ($metasPorSala[$salaAtual->id_sala] ?? 0)
                    ) {
                        $salaIndex++;
                        $salaAtual = $salasSelecionadas->get($salaIndex);
                    }

                    if ($salaAtual === null) {
                        break; // sem mais salas disponíveis
                    }

                    $proximoAssento = ($assentoPorSala[$salaAtual->id_sala] ?? 0) + 1;

                    SigeConcursoInscricaoSala::create([
                        'fk_id_inscricao' => $atribuicao->fk_id_inscricao,
                        'fk_id_sala' => $salaAtual->id_sala,
                        'numero_assento' => $proximoAssento,
                    ]);

                    $ocupacaoPorSala[$salaAtual->id_sala] = ($ocupacaoPorSala[$salaAtual->id_sala] ?? 0) + 1;
                    $assentoPorSala[$salaAtual->id_sala] = $proximoAssento;
                }
            }
        });

        $this->sincronizarFluxoProcesso($processo);

        return back()->with('success', 'Distribuição por salas realizada com sucesso.');
    }

    private function montarPlanoDistribuicaoSalas($salas, int $totalCandidatos): array
    {
        if ($totalCandidatos <= 0) {
            return [collect(), []];
        }

        $salasSelecionadas = collect();
        $capacidadeAcumulada = 0;
        $possuiSalaSemLimite = false;

        foreach ($salas as $sala) {
            $salasSelecionadas->push($sala);

            $capacidadeSala = (int) $sala->capacidade_maxima;

            if ($capacidadeSala <= 0) {
                $possuiSalaSemLimite = true;
                break;
            }

            $capacidadeAcumulada += $capacidadeSala;

            if ($capacidadeAcumulada >= $totalCandidatos) {
                break;
            }
        }

        if ($salasSelecionadas->isEmpty()) {
            return [collect(), []];
        }

        $totalParaDistribuir = $possuiSalaSemLimite
            ? $totalCandidatos
            : min($totalCandidatos, $capacidadeAcumulada);

        $metasPorSala = [];
        $restanteCandidatos = $totalParaDistribuir;
        $salasRestantes = $salasSelecionadas->count();

        foreach ($salasSelecionadas as $sala) {
            $metaIdeal = (int) ceil($restanteCandidatos / max($salasRestantes, 1));
            $capacidadeSala = (int) $sala->capacidade_maxima;

            $metaSala = $capacidadeSala > 0
                ? min($metaIdeal, $capacidadeSala)
                : $metaIdeal;

            $metasPorSala[$sala->id_sala] = $metaSala;
            $restanteCandidatos -= $metaSala;
            $salasRestantes--;
        }

        if ($restanteCandidatos > 0) {
            foreach ($salasSelecionadas as $sala) {
                if ($restanteCandidatos <= 0) {
                    break;
                }

                $capacidadeSala = (int) $sala->capacidade_maxima;
                $ocupacaoPlanejada = $metasPorSala[$sala->id_sala] ?? 0;
                $folga = $capacidadeSala > 0
                    ? max(0, $capacidadeSala - $ocupacaoPlanejada)
                    : $restanteCandidatos;

                if ($folga <= 0) {
                    continue;
                }

                $incremento = min($folga, $restanteCandidatos);
                $metasPorSala[$sala->id_sala] += $incremento;
                $restanteCandidatos -= $incremento;
            }
        }

        return [$salasSelecionadas, $metasPorSala];
    }

    public function limparDistribuicaoSalas($id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        $idsInscricoes = SigeConcursoInscricaoLocal::whereHas('processoLocal', function ($q) use ($processo) {
            $q->where('fk_id_processo', $processo->id_processo);
        })->pluck('fk_id_inscricao');

        $removidos = SigeConcursoInscricaoSala::whereIn('fk_id_inscricao', $idsInscricoes)->delete();

        $this->sincronizarFluxoProcesso($processo);

        return back()->with('success', "Distribuição por salas removida ({$removidos} registro(s) excluído(s)).");
    }

    public function publicarLocalProva($id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        $totalDistribuidosSala = SigeConcursoInscricaoSala::whereHas('sala.localProva.processos', function ($q) use ($processo) {
            $q->where('fk_id_processo', $processo->id_processo);
        })->count();

        if ($totalDistribuidosSala === 0) {
            return back()->with('error', 'Não é possível publicar: nenhuma distribuição por salas foi encontrada.');
        }

        $processo->update([
            'etapa_fluxo_atual' => 'local_prova_liberado',
        ]);

        $this->sincronizarFluxoProcesso($processo);

        return back()->with('success', 'Local de prova publicado para os candidatos com inscrição deferida.');
    }

    public function publicarEdital(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        if (in_array($processo->status, ['suspenso', 'finalizado'], true)) {
            return back()->with('error', 'Não é possível publicar edital para um processo suspenso ou finalizado.');
        }

        $processo->update([
            'status' => 'publicado',
            'etapa_fluxo_atual' => 'cadastro',
            'data_publicacao' => $processo->data_publicacao ?: now(),
        ]);

        $this->sincronizarFluxoProcesso($processo);

        $redirectTo = (string) $request->input('redirect_to', '');

        if ($redirectTo !== '' && str_starts_with($redirectTo, '/')) {
            return redirect($redirectTo)->with('success', 'Edital publicado com sucesso.');
        }

        return back()->with('success', 'Edital publicado com sucesso.');
    }

    public function iniciarInscricoes(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        if (in_array($processo->status, ['suspenso', 'finalizado'], true)) {
            return back()->with('error', 'Não é possível iniciar inscrições para um processo suspenso ou finalizado.');
        }

        $processo->update([
            'status' => 'inscricoes_abertas',
            'etapa_fluxo_atual' => 'inscricoes',
            'data_publicacao' => $processo->data_publicacao ?: now(),
        ]);

        $this->sincronizarFluxoProcesso($processo);

        $processo->refresh();

        $mensagem = $processo->inscricoesAbertasAgora()
            ? 'Fluxo atualizado para inscrições e etapa operacional sincronizada.'
            : 'Etapa de inscrições ativada. O processo ficará com inscrições abertas somente dentro do período configurado.';

        $redirectTo = (string) $request->input('redirect_to', '');

        if ($redirectTo !== '' && str_starts_with($redirectTo, '/')) {
            return redirect($redirectTo)->with('success', $mensagem);
        }

        return redirect()->route('sigeconcursos.processos.inscricoes', $processo->id_processo)
            ->with('success', $mensagem);
    }

    public function homologarInscricoesComArquivo(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        if (in_array($processo->status, ['suspenso', 'finalizado'], true)) {
            return back()->with('error', 'Não é possível homologar inscrições para um processo suspenso ou finalizado.');
        }

        $validated = $request->validate([
            'arquivo_homologacao' => ['required', 'file', 'max:10240'],
            'nome_exibicao_homologacao' => ['nullable', 'string', 'max:255'],
            'redirect_to' => ['nullable', 'string', 'max:500'],
        ]);

        $arquivo = $request->file('arquivo_homologacao');
        $caminho = $arquivo->store('sigeconcursos/processos/' . $processo->id_processo, 'public');
        $ordem = ((int) $processo->arquivos()->max('ordem_exibicao')) + 1;

        $processo->arquivos()->create([
            'nome_exibicao' => trim((string) ($validated['nome_exibicao_homologacao'] ?? '')) ?: $arquivo->getClientOriginalName(),
            'tipo_arquivo' => 'homologacao_inscricoes',
            'caminho_arquivo' => $caminho,
            'ordem_exibicao' => $ordem,
        ]);

        $processo->update([
            'status' => 'em_andamento',
            'etapa_fluxo_atual' => 'homologacao_inscricoes',
        ]);

        $this->sincronizarFluxoProcesso($processo);

        $redirectTo = (string) ($validated['redirect_to'] ?? '');

        if ($redirectTo !== '' && str_starts_with($redirectTo, '/')) {
            return redirect($redirectTo)->with('success', 'Arquivo anexado e homologação iniciada com sucesso.');
        }

        return redirect()->route('sigeconcursos.processos.inscricoes', $processo->id_processo)
            ->with('success', 'Arquivo anexado e homologação iniciada com sucesso.');
    }

    public function adicionarArquivoEtapasFinais(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        if (in_array($processo->status, ['suspenso', 'finalizado'], true)) {
            return back()->with('error', 'Não é possível adicionar arquivo para um processo suspenso ou finalizado.');
        }

        $validated = $request->validate([
            'arquivo_etapa_final' => ['required', 'file', 'max:10240'],
            'nome_exibicao_etapa_final' => ['nullable', 'string', 'max:255'],
            'tipo_arquivo_etapa_final' => ['nullable', 'string', 'max:50'],
            'redirect_to' => ['nullable', 'string', 'max:500'],
        ]);

        $arquivo = $request->file('arquivo_etapa_final');
        $caminho = $arquivo->store('sigeconcursos/processos/' . $processo->id_processo, 'public');
        $ordem = ((int) $processo->arquivos()->max('ordem_exibicao')) + 1;

        $tipoArquivo = trim((string) ($validated['tipo_arquivo_etapa_final'] ?? '')) ?: 'resultado_preliminar';

        $processo->arquivos()->create([
            'nome_exibicao' => trim((string) ($validated['nome_exibicao_etapa_final'] ?? '')) ?: $arquivo->getClientOriginalName(),
            'tipo_arquivo' => $tipoArquivo,
            'caminho_arquivo' => $caminho,
            'ordem_exibicao' => $ordem,
        ]);

        if (!in_array($processo->status, ['finalizado', 'suspenso'], true)) {
            $processo->update([
                'status' => 'em_andamento',
                'etapa_fluxo_atual' => 'etapas_finais',
            ]);

            $this->sincronizarFluxoProcesso($processo);
        }

        $redirectTo = (string) ($validated['redirect_to'] ?? '');

        if ($redirectTo !== '' && str_starts_with($redirectTo, '/')) {
            return redirect($redirectTo)->with('success', 'Arquivo adicionado na etapa final com sucesso.');
        }

        return redirect()->route('sigeconcursos.processos.show', $processo->id_processo)
            ->with('success', 'Arquivo adicionado na etapa final com sucesso.');
    }

    public function encerrarProcesso(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);

        if ($processo->status === 'finalizado') {
            return back()->with('success', 'O processo já está finalizado.');
        }

        if ($processo->status === 'suspenso') {
            return back()->with('error', 'Não é possível encerrar um processo suspenso.');
        }

        $processo->update([
            'status' => 'finalizado',
            'etapa_fluxo_atual' => 'etapas_finais',
            'data_resultado_final' => $processo->data_resultado_final ?: now(),
        ]);

        $this->sincronizarFluxoProcesso($processo);

        $redirectTo = (string) $request->input('redirect_to', '');

        if ($redirectTo !== '' && str_starts_with($redirectTo, '/')) {
            return redirect($redirectTo)->with('success', 'Processo encerrado e marcado como finalizado.');
        }

        return redirect()->route('sigeconcursos.processos.show', $processo->id_processo)
            ->with('success', 'Processo encerrado e marcado como finalizado.');
    }

    private function validateData(Request $request): array
    {
        $request->merge([
            'valor_taxa_padrao' => $this->normalizeMoney($request->input('valor_taxa_padrao')),
            'etapa_fluxo_atual' => $this->resolverEtapaFluxoEntrada($request),
        ]);

        $data = $request->validate([
            'numero_edital' => ['required', 'string', 'max:100'],
            'titulo' => ['required', 'string', 'max:255'],
            'tipo_processo' => ['required', 'in:concurso_publico,processo_seletivo'],
            'fk_id_empresa' => ['required', 'exists:sigeconcursos_tb_empresas,id_empresa'],
            'status' => ['required', 'in:rascunho,publicado,inscricoes_abertas,inscricoes_encerradas,em_andamento,finalizado,suspenso'],
            'etapa_fluxo_atual' => ['required', 'in:cadastro,inscricoes,homologacao_inscricoes,distribuicao_locais,distribuicao_salas,local_prova_liberado,etapas_finais'],
            'resumo' => ['nullable', 'string'],
            'descricao' => ['nullable', 'string'],
            'requisitos_gerais' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string'],
            'data_publicacao' => ['nullable', 'date'],
            'data_inicio_inscricoes' => ['nullable', 'date'],
            'data_fim_inscricoes' => ['nullable', 'date', 'after_or_equal:data_inicio_inscricoes'],
            'data_prova' => ['nullable', 'date'],
            'data_resultado_final' => ['nullable', 'date'],
            'valor_taxa_padrao' => ['nullable', 'numeric', 'min:0'],
            'fases' => ['nullable', 'array'],
            'fases.*.descricao' => ['nullable', 'string', 'max:255'],
            'fases.*.periodo' => ['nullable', 'string', 'max:255'],
            'cargos' => ['nullable', 'array'],
            'cargos.*.fk_id_cargo' => ['nullable', 'integer', 'exists:sigeconcursos_tb_cargos,id_cargo'],
            'cargos.*.quantidade_vagas' => ['nullable', 'integer', 'min:0'],
            'cargos.*.quantidade_cadastro_reserva' => ['nullable', 'integer', 'min:0'],
            'cargos.*.valor_remuneracao' => ['nullable'],
            'cargos.*.valor_taxa_inscricao' => ['nullable'],
            'cargos.*.carga_horaria' => ['nullable', 'string', 'max:100'],
            'cargos.*.requisitos_especificos' => ['nullable', 'string'],
            'locais' => ['nullable', 'array'],
            'locais.*.fk_id_local_prova' => ['nullable', 'integer', 'exists:sigeconcursos_tb_locais_prova,id_local_prova'],
            'locais.*.observacoes' => ['nullable', 'string'],
            'isencoes' => ['nullable', 'array'],
            'isencoes.*.titulo' => ['nullable', 'string', 'max:255'],
            'isencoes.*.descricao' => ['nullable', 'string'],
            'isencoes.*.data_inicio' => ['nullable', 'date'],
            'isencoes.*.data_fim' => ['nullable', 'date'],
            'documentos_exigidos' => ['nullable', 'array'],
            'documentos_exigidos.*.titulo' => ['nullable', 'string', 'max:255'],
            'documentos_exigidos.*.descricao' => ['nullable', 'string'],
            'documentos_exigidos.*.obrigatorio' => ['nullable'],
            'arquivos' => ['nullable', 'array'],
            'arquivos.*' => ['nullable', 'file', 'max:5120'],
            'nome_exibicao' => ['nullable', 'array'],
            'nome_exibicao.*' => ['nullable', 'string', 'max:255'],
            'tipo_arquivo' => ['nullable', 'array'],
            'tipo_arquivo.*' => ['nullable', 'string', 'max:50'],
            'icone_processo' => ['nullable', 'image', 'max:2048'],
        ], [
            'fk_id_empresa.exists' => 'Selecione um órgão/empresa válido.',
        ]);

        $data['exige_aceite_edital'] = $request->boolean('exige_aceite_edital');
        $data['permite_condicao_especial'] = $request->boolean('permite_condicao_especial');
        $data['exige_documento_condicao_especial'] = $data['permite_condicao_especial']
            ? $request->boolean('exige_documento_condicao_especial')
            : false;
        $data['possui_taxa_inscricao'] = $request->boolean('possui_taxa_inscricao');
        $data['permite_ampla_concorrencia'] = $request->boolean('permite_ampla_concorrencia');
        $data['permite_pcd'] = $request->boolean('permite_pcd');

        foreach ($request->input('isencoes', []) as $index => $isencao) {
            $dataInicio = $isencao['data_inicio'] ?? null;
            $dataFim = $isencao['data_fim'] ?? null;

            if ($dataInicio && $dataFim && strtotime($dataFim) < strtotime($dataInicio)) {
                throw ValidationException::withMessages([
                    "isencoes.$index.data_fim" => 'A data final da isenção não pode ser menor que a data inicial.',
                ]);
            }
        }

        unset($data['fases'], $data['cargos'], $data['locais'], $data['isencoes'], $data['documentos_exigidos'], $data['arquivos'], $data['nome_exibicao'], $data['tipo_arquivo'], $data['icone_processo']);

        return $data;
    }

    private function resolverEtapaFluxoEntrada(Request $request): string
    {
        $status = (string) $request->input('status', 'rascunho');

        if (in_array($status, ['suspenso', 'finalizado'], true)) {
            return (string) $request->input('etapa_fluxo_atual', 'etapas_finais');
        }

        return match ($status) {
            'rascunho' => 'cadastro',
            'publicado', 'inscricoes_abertas' => 'inscricoes',
            'inscricoes_encerradas', 'em_andamento' => (string) $request->input('etapa_fluxo_atual', 'homologacao_inscricoes'),
            default => 'cadastro',
        };
    }

    private function sincronizarFluxoProcesso(SigeConcursoProcesso $processo): void
    {
        $processo->refresh();
        $processo->update($processo->sincronizacaoFluxo());
        $processo->refresh();
    }

    private function formatarFases(array $fases): array
    {
        return collect($fases)->map(function ($fase) {
            $descricao = trim($fase['descricao'] ?? '');
            $periodo = trim($fase['periodo'] ?? '');

            if ($descricao === '' && $periodo === '') {
                return null;
            }

            return [
                'descricao' => $descricao,
                'periodo' => $periodo,
            ];
        })->filter()->values()->all();
    }

    private function formatarCargos(array $cargos): array
    {
        return collect($cargos)->map(function ($cargo) {
            $cargoId = $cargo['fk_id_cargo'] ?? null;

            if (!$cargoId) {
                return null;
            }

            return [
                'fk_id_cargo' => (int) $cargoId,
                'quantidade_vagas' => $this->nullableInteger($cargo['quantidade_vagas'] ?? null),
                'quantidade_cadastro_reserva' => !empty($cargo['is_cadastro_reserva']) ? 1 : 0,
                'valor_remuneracao' => $this->normalizeMoney($cargo['valor_remuneracao'] ?? null),
                'valor_taxa_inscricao' => $this->normalizeMoney($cargo['valor_taxa_inscricao'] ?? null),
                'carga_horaria' => trim($cargo['carga_horaria'] ?? '') ?: null,
                'requisitos_especificos' => trim($cargo['requisitos_especificos'] ?? '') ?: null,
            ];
        })->filter()->unique('fk_id_cargo')->values()->all();
    }

    private function formatarLocais(array $locais): array
    {
        return collect($locais)->map(function ($local) {
            $localId = $local['fk_id_local_prova'] ?? null;

            if (!$localId) {
                return null;
            }

            return [
                'fk_id_local_prova' => (int) $localId,
                'observacoes' => trim($local['observacoes'] ?? '') ?: null,
            ];
        })->filter()->unique('fk_id_local_prova')->values()->all();
    }

    private function formatarIsencoes(array $isencoes): array
    {
        return collect($isencoes)->map(function ($isencao) {
            $titulo = trim($isencao['titulo'] ?? '');
            $descricao = trim($isencao['descricao'] ?? '');

            if ($titulo === '' && $descricao === '') {
                return null;
            }

            return [
                'titulo' => $titulo !== '' ? $titulo : 'Isenção',
                'descricao' => $descricao !== '' ? $descricao : null,
                'data_inicio' => $isencao['data_inicio'] ?? null,
                'data_fim' => $isencao['data_fim'] ?? null,
                'exige_comprovacao' => filter_var($isencao['exige_comprovacao'] ?? false, FILTER_VALIDATE_BOOL),
            ];
        })->filter()->values()->all();
    }

    private function formatarDocumentosExigidos(array $documentosExigidos): array
    {
        return collect($documentosExigidos)->map(function ($documento, $index) {
            $titulo = trim($documento['titulo'] ?? '');
            $descricao = trim($documento['descricao'] ?? '');

            if ($titulo === '' && $descricao === '') {
                return null;
            }

            return [
                'titulo' => $titulo !== '' ? $titulo : 'Documento complementar',
                'descricao' => $descricao !== '' ? $descricao : null,
                'obrigatorio' => filter_var($documento['obrigatorio'] ?? false, FILTER_VALIDATE_BOOL),
                'ordem_exibicao' => $index + 1,
            ];
        })->filter()->values()->all();
    }

    private function salvarIcone(Request $request, SigeConcursoProcesso $processo): void
    {
        if (!$request->hasFile('icone_processo')) {
            return;
        }

        if ($processo->icone_processo && Storage::disk('public')->exists($processo->icone_processo)) {
            Storage::disk('public')->delete($processo->icone_processo);
        }

        $iconePath = $request->file('icone_processo')->store('sigeconcursos/processos/icones', 'public');
        $processo->update(['icone_processo' => $iconePath]);
    }

    private function salvarArquivos(Request $request, SigeConcursoProcesso $processo): void
    {
        if (!$request->hasFile('arquivos')) {
            return;
        }

        foreach ($request->file('arquivos') as $index => $arquivo) {
            if (!$arquivo || !$arquivo->isValid()) {
                continue;
            }

            $caminho = $arquivo->store('sigeconcursos/processos/' . $processo->id_processo, 'public');

            $processo->arquivos()->create([
                'nome_exibicao' => $request->input("nome_exibicao.$index") ?: $arquivo->getClientOriginalName(),
                'tipo_arquivo' => $request->input("tipo_arquivo.$index") ?: 'outro',
                'caminho_arquivo' => $caminho,
                'ordem_exibicao' => $index + 1,
            ]);
        }
    }

    private function normalizeMoney($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (str_contains($value, ',')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } else {
            $value = str_replace(',', '', $value);
        }

        return is_numeric($value) ? $value : null;
    }

    private function nullableInteger($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function resolvePerPage(?string $perPageParam, int $total): int
    {
        $allowed = ['25', '50', '100', '200', 'all'];

        if (!in_array((string) ($perPageParam ?? ''), $allowed, true)) {
            return 25;
        }

        if ($perPageParam === 'all') {
            return max(1, $total);
        }

        return (int) $perPageParam;
    }
}