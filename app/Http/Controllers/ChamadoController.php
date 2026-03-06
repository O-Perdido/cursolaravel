<?php

namespace App\Http\Controllers;

use App\Models\Chamado;
use App\Models\ChamadoMensagem;
use App\Models\TipoChamado;
use App\Models\Termo;
use App\Models\User;
use App\Mail\ChamadoMensagemRecebidaMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ChamadoController extends Controller
{
    /**
     * Lista todos os chamados (para empresas veem apenas seus próprios)
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->nivel === 'empresa') {
            $chamados = Chamado::with(['tipoChamado', 'termo.estagiario', 'responsavel'])
                ->withCount([
                    'mensagens as mensagens_nao_lidas_count' => function ($query) {
                        $query->where('remetente_nivel', 'operador')
                            ->whereNull('lido_empresa_em');
                    },
                ])
                ->where('fk_id_empresa', $user->empresa->id_empresa)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Admin e operador veem todos
            $chamados = Chamado::with(['tipoChamado', 'empresa', 'termo.estagiario', 'solicitante', 'responsavel'])
                ->withCount([
                    'mensagens as mensagens_nao_lidas_count' => function ($query) {
                        $query->where('remetente_nivel', 'empresa')
                            ->whereNull('lido_operador_em');
                    },
                ])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('chamados.index', compact('chamados'));
    }

    /**
     * Exibe o formulário de criação
     */
    public function create(Request $request)
    {
        $tipoId = $request->get('tipo');
        $tipoChamado = TipoChamado::findOrFail($tipoId);

        $user = Auth::user();
        $termos = [];

        // Se for Rescisão ou Alteração, buscar termos ativos da empresa
        if ($tipoChamado->isRescisao() || $tipoChamado->isAlteracao()) {
            $termos = Termo::where('fk_id_empresa', $user->empresa->id_empresa)
                ->whereDoesntHave('rescisao')
                ->with('estagiario')
                ->get()
                ->map(function ($termo) {
                    return [
                        'id' => $termo->id_termo,
                        'text' => sprintf('%s/%s - %s', $termo->numero_termo, $termo->ano_termo, $termo->estagiario->nome_estagiario),
                        'cpf' => $termo->estagiario->numero_cpf,
                        'numero' => $termo->numero_termo,
                        'ano' => $termo->ano_termo,
                        'nome' => $termo->estagiario->nome_estagiario,
                    ];
                });
        }

        return view('chamados.create_clean', compact('tipoChamado', 'termos'));
    }

    /**
     * Armazena um novo chamado
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $tipoChamado = TipoChamado::findOrFail($request->fk_id_tipo_chamado);

        // Validação dinâmica baseada no tipo
        $rules = [
            'fk_id_tipo_chamado' => 'required|exists:tb_tipos_chamados,id_tipo_chamado',
        ];

        if ($tipoChamado->isRescisao()) {
            $rules['fk_id_termo'] = 'required|exists:tb_termos,id_termo';
            $rules['data_rescisao'] = 'required|date';
            $rules['motivo_rescisao'] = 'required|string|max:1000';
        } elseif ($tipoChamado->isAlteracao()) {
            $rules['fk_id_termo'] = 'required|exists:tb_termos,id_termo';
            $rules['descricao_alteracao'] = 'required|string|max:2000';
        } else {
            // Chamados gerais
            $rules['titulo'] = 'required|string|max:200';
            $rules['detalhes'] = 'required|string|max:5000';
            $rules['anexos.*'] = 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120'; // 5MB
        }

        $validator = Validator::make($request->all(), $rules, [
            'fk_id_tipo_chamado.required' => 'Tipo de chamado é obrigatório.',
            'fk_id_termo.required' => 'Selecione um termo.',
            'data_rescisao.required' => 'Data de rescisão é obrigatória.',
            'motivo_rescisao.required' => 'Motivo da rescisão é obrigatório.',
            'descricao_alteracao.required' => 'Descrição da alteração é obrigatória.',
            'titulo.required' => 'Título é obrigatório.',
            'detalhes.required' => 'Detalhes são obrigatórios.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Processa anexos se houver
        $anexosPaths = [];
        if ($request->hasFile('anexos')) {
            foreach ($request->file('anexos') as $anexo) {
                $path = $anexo->store('chamados/anexos', 'public');
                $anexosPaths[] = $path;
            }
        }

        // Cria o chamado
        $chamado = Chamado::create([
            'fk_id_tipo_chamado' => $request->fk_id_tipo_chamado,
            'fk_id_empresa' => $user->empresa->id_empresa,
            'fk_id_user_solicitante' => $user->id,
            'fk_id_termo' => $request->fk_id_termo,
            'data_rescisao' => $request->data_rescisao,
            'motivo_rescisao' => $request->motivo_rescisao,
            'descricao_alteracao' => $request->descricao_alteracao,
            'titulo' => $request->titulo,
            'detalhes' => $request->detalhes,
            'anexos' => !empty($anexosPaths) ? $anexosPaths : null,
            'status' => 'pendente',
        ]);

        return redirect()->route('chamados.show', $chamado->id_chamado)
            ->with('success', 'Chamado aberto com sucesso! Protocolo: ' . $chamado->protocolo);
    }

    /**
     * Exibe detalhes de um chamado
     */
    public function show($id)
    {
        $user = Auth::user();

        $query = Chamado::with([
            'tipoChamado',
            'empresa',
            'termo.estagiario',
            'solicitante',
            'responsavel',
            'mensagens.remetente',
        ]);

        // Empresa só vê seus próprios chamados
        if ($user->nivel === 'empresa') {
            $query->where('fk_id_empresa', $user->empresa->id_empresa);
        }

        $chamado = $query->findOrFail($id);

        $this->marcarMensagensComoLidas($chamado, $user);
        $chamado->load('mensagens.remetente');

           // Renderiza view diferente para admin/operador
           if (in_array($user->nivel, ['admin', 'operador'])) {
               // Busca operadores/admin para seleção de responsável
               $operadores = User::whereIn('nivel', ['admin', 'operador'])
                   ->orderBy('name')
                   ->get();
               
               return view('chamados.detalhes-admin', compact('chamado', 'operadores'));
           }

           return view('chamados.show', compact('chamado'));
    }

    /**
     * Envia mensagem no chat do chamado
     */
    public function enviarMensagem(Request $request, $id)
    {
        $request->validate([
            'mensagem' => 'required|string|max:2000',
            'anexos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $user = Auth::user();
        $query = Chamado::query();

        if ($user->nivel === 'empresa') {
            $query->where('fk_id_empresa', $user->empresa->id_empresa);
        }

        $chamado = $query->findOrFail($id);

        if (in_array($chamado->status, ['concluido', 'cancelado']) && $user->nivel === 'empresa') {
            return back()->with('error', 'Este chamado foi finalizado e não pode receber novas mensagens da unidade concedente.');
        }

        $remetenteNivel = $user->nivel === 'empresa' ? 'empresa' : 'operador';

        // Processa anexos se houver
        $anexosPaths = [];
        if ($request->hasFile('anexos')) {
            $count = 0;
            foreach ($request->file('anexos') as $anexo) {
                if ($count >= 5) break;
                $path = $anexo->store('chamados/mensagens/anexos', 'public');
                $anexosPaths[] = $path;
                $count++;
            }
        }

        $mensagem = ChamadoMensagem::create([
            'fk_id_chamado' => $chamado->id_chamado,
            'fk_id_user_remetente' => $user->id,
            'remetente_nivel' => $remetenteNivel,
            'mensagem' => trim($request->mensagem),
            'anexos' => !empty($anexosPaths) ? $anexosPaths : null,
            'lido_empresa_em' => $remetenteNivel === 'empresa' ? now() : null,
            'lido_operador_em' => $remetenteNivel === 'operador' ? now() : null,
        ]);

        $this->notificarNovaMensagem($chamado, $mensagem, $user);

        return back()->with('success', 'Mensagem enviada com sucesso.');
    }

    /**
     * API para buscar termos (usado no Select2)
     */
    public function buscarTermos(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('q', '');

        $query = Termo::where('fk_id_empresa', $user->empresa->id_empresa)
            ->whereDoesntHave('rescisao')
            ->with('estagiario');

        // Se houver termo de busca, aplicar filtros
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_termo', 'like', "%{$search}%")
                    ->orWhere('ano_termo', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(numero_termo, '/', ano_termo) LIKE ?", ["%{$search}%"])
                    ->orWhereHas('estagiario', function ($subq) use ($search) {
                        $subq->where('nome_estagiario', 'like', "%{$search}%")
                            ->orWhere('numero_cpf', 'like', "%{$search}%");
                    });
            });
        }

        $termos = $query->orderBy('numero_termo', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($termo) {
                return [
                    'id' => $termo->id_termo,
                    'text' => sprintf('%s/%s - %s', $termo->numero_termo, $termo->ano_termo, $termo->estagiario->nome_estagiario),
                ];
            });

        return response()->json(['results' => $termos]);
    }

    /**
     * API para listagem de termos com filtros (modal de seleção)
     */
    public function listarTermosModal(Request $request)
    {
        $user = Auth::user();
        $numero = trim((string) $request->get('numero', ''));
        $nome = trim((string) $request->get('nome', ''));
        $cpf = preg_replace('/\D+/', '', (string) $request->get('cpf', ''));

        $query = Termo::where('fk_id_empresa', $user->empresa->id_empresa)
            ->whereDoesntHave('rescisao')
            ->with('estagiario');

        if ($numero !== '') {
            $query->where(function ($q) use ($numero) {
                $q->where('numero_termo', 'like', "%{$numero}%")
                    ->orWhere('ano_termo', 'like', "%{$numero}%")
                    ->orWhereRaw("CONCAT(numero_termo, '/', ano_termo) LIKE ?", ["%{$numero}%"]);
            });
        }

        if ($nome !== '') {
            $query->whereHas('estagiario', function ($q) use ($nome) {
                $q->where('nome_estagiario', 'like', "%{$nome}%");
            });
        }

        if ($cpf !== '') {
            $query->whereHas('estagiario', function ($q) use ($cpf) {
                $q->where('numero_cpf', 'like', "%{$cpf}%");
            });
        }

        $termos = $query->orderBy('ano_termo', 'desc')
            ->orderBy('numero_termo', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($termo) {
                return [
                    'id' => $termo->id_termo,
                    'numero' => $termo->numero_termo,
                    'ano' => $termo->ano_termo,
                    'estagiario' => $termo->estagiario->nome_estagiario,
                    'cpf' => $termo->estagiario->numero_cpf,
                    'text' => sprintf('%s/%s - %s', $termo->numero_termo, $termo->ano_termo, $termo->estagiario->nome_estagiario),
                ];
            });

        return response()->json(['results' => $termos]);
    }

    /**
     * Cancela um chamado (apenas empresa pode cancelar seus próprios)
     */
    public function cancelar($id)
    {
        $user = Auth::user();

        $chamado = Chamado::where('fk_id_empresa', $user->empresa->id_empresa)
            ->findOrFail($id);

        if (in_array($chamado->status, ['concluido', 'cancelado'])) {
            return back()->with('error', 'Este chamado já foi finalizado e não pode ser cancelado.');
        }

        $chamado->update(['status' => 'cancelado']);

        return back()->with('success', 'Chamado cancelado com sucesso.');
    }

    /**
     * Painel de gerenciamento de chamados (admin/operador)
     */
    public function painel(Request $request)
    {
        $filtro = $request->get('filtro', 'pendente');
        $busca = $request->get('busca', '');
        $tipo = $request->get('tipo', '');

        $query = Chamado::with('tipoChamado', 'empresa', 'solicitante', 'responsavel', 'termo.estagiario');
        $query->withCount([
            'mensagens as mensagens_nao_lidas_count' => function ($subQuery) {
                $subQuery->where('remetente_nivel', 'empresa')
                    ->whereNull('lido_operador_em');
            },
        ]);

        // Filtrar por status
        if ($filtro !== 'todos') {
            $query->where('status', $filtro);
        }

        // Filtrar por busca (protocolo, empresa ou estagiário)
        if ($busca) {
            $query->where(function ($q) use ($busca) {
                $q->where('protocolo', 'like', "%{$busca}%")
                    ->orWhereHas('empresa', fn($q) => $q->where('nome_empresa', 'like', "%{$busca}%"))
                    ->orWhereHas('termo.estagiario', fn($q) => $q->where('nome_estagiario', 'like', "%{$busca}%"));
            });
        }

        // Filtrar por tipo
        if ($tipo) {
            $query->where('fk_id_tipo_chamado', $tipo);
        }

        $chamados = $query->orderBy('created_at', 'desc')->paginate(15);

        // Dados estatísticos
        $stats = [
            'pendentes' => Chamado::where('status', 'pendente')->count(),
            'em_analise' => Chamado::where('status', 'em_analise')->count(),
            'em_andamento' => Chamado::where('status', 'em_andamento')->count(),
            'concluidos' => Chamado::where('status', 'concluido')->count(),
            'cancelados' => Chamado::where('status', 'cancelado')->count(),
        ];

        $tipos = TipoChamado::where('ativo', true)->get();
        $operadores = User::where('nivel', 'operador')->orWhere('nivel', 'admin')->get();

        return view('chamados.painel', compact('chamados', 'stats', 'tipos', 'operadores', 'filtro', 'busca', 'tipo'));
    }

    /**
     * Atualiza o status de um chamado
     */
    public function atualizarStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pendente,em_analise,em_andamento,concluido,cancelado',
        ]);

        $chamado = Chamado::findOrFail($id);
        $statusAnterior = $chamado->status;

        $chamado->update([
            'status' => $request->status,
            'data_conclusao' => in_array($request->status, ['concluido', 'cancelado']) ? now() : null,
        ]);

        // Log da alteração
        Log::info("Chamado #{$chamado->protocolo} - Status alterado de '{$statusAnterior}' para '{$request->status}' por " . Auth::user()->name);

        return back()->with('success', "Status do chamado atualizado para '{$request->status}'");
    }

    /**
     * Atribui um responsável ao chamado
     */
    public function atribuirResponsavel(Request $request, $id)
    {
        $request->validate([
            'fk_id_user_responsavel' => 'nullable|exists:users,id',
        ]);

        $chamado = Chamado::findOrFail($id);
        $responsavelAnterior = $chamado->responsavel?->name ?? 'Não atribuído';

        $chamado->update([
            'fk_id_user_responsavel' => $request->fk_id_user_responsavel,
        ]);

        $responsavelNovo = $chamado->responsavel?->name ?? 'Não atribuído';

        Log::info("Chamado #{$chamado->protocolo} - Responsável alterado de '{$responsavelAnterior}' para '{$responsavelNovo}' por " . Auth::user()->name);

        return back()->with('success', 'Responsável atualizado com sucesso');
    }

    /**
     * Adiciona observação interna ao chamado
     */
    public function adicionarObservacao(Request $request, $id)
    {
        $request->validate([
            'observacoes_internas' => 'required|string|max:2000',
        ]);

        $chamado = Chamado::findOrFail($id);
        $chamado->update([
            'observacoes_internas' => $request->observacoes_internas,
        ]);

        return back()->with('success', 'Observação adicionada com sucesso');
    }

    /**
     * Normaliza o caminho salvo do anexo para o disco public
     */
    protected function normalizarCaminhoAnexo(string $raw): string
    {
        $raw = str_replace('\\', '/', $raw);
        if (str_starts_with($raw, 'storage/')) {
            $raw = substr($raw, 8);
        }
        $basename = basename($raw);
        if (!str_starts_with($raw, 'chamados/anexos/')) {
            $raw = 'chamados/anexos/' . $basename;
        }
        return $raw;
    }

    /**
     * Visualiza um anexo inline (imagens, pdf, etc.)
     */
    public function visualizarAnexo($id, $index)
    {
        $user = Auth::user();

        $query = Chamado::query();
        if ($user->nivel === 'empresa') {
            $query->where('fk_id_empresa', $user->empresa->id_empresa);
        }
        $chamado = $query->findOrFail($id);

        $anexos = is_array($chamado->anexos) ? $chamado->anexos : [];
        if (!isset($anexos[$index])) {
            abort(404);
        }

        $pathRelativo = $this->normalizarCaminhoAnexo((string) $anexos[$index]);
        if (!Storage::disk('public')->exists($pathRelativo)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($pathRelativo);
        return response()->file($fullPath);
    }

    /**
     * Faz o download de um anexo
     */
    public function downloadAnexo($id, $index)
    {
        $user = Auth::user();

        $query = Chamado::query();
        if ($user->nivel === 'empresa') {
            $query->where('fk_id_empresa', $user->empresa->id_empresa);
        }
        $chamado = $query->findOrFail($id);

        $anexos = is_array($chamado->anexos) ? $chamado->anexos : [];
        if (!isset($anexos[$index])) {
            abort(404);
        }

        $pathRelativo = $this->normalizarCaminhoAnexo((string) $anexos[$index]);
        if (!Storage::disk('public')->exists($pathRelativo)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($pathRelativo);
        return response()->download($fullPath);
    }

    /**
     * Marca mensagens recebidas como lidas para o perfil atual
     */
    protected function marcarMensagensComoLidas(Chamado $chamado, User $user): void
    {
        if ($user->nivel === 'empresa') {
            ChamadoMensagem::where('fk_id_chamado', $chamado->id_chamado)
                ->where('remetente_nivel', 'operador')
                ->whereNull('lido_empresa_em')
                ->update(['lido_empresa_em' => now()]);

            return;
        }

        ChamadoMensagem::where('fk_id_chamado', $chamado->id_chamado)
            ->where('remetente_nivel', 'empresa')
            ->whereNull('lido_operador_em')
            ->update(['lido_operador_em' => now()]);
    }

    /**
     * Dispara e-mail para os destinatários do outro lado da conversa
     */
    protected function notificarNovaMensagem(Chamado $chamado, ChamadoMensagem $mensagem, User $remetente): void
    {
        $emails = [];

        if ($mensagem->remetente_nivel === 'operador') {
            // Operador respondeu → notificar empresa
            $emails = User::where('fk_id_empresa', $chamado->fk_id_empresa)
                ->where('nivel', 'empresa')
                ->whereNotNull('email')
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->all();
        } else {
            // Empresa respondeu → verificar configuração de notificação para operadores
            $notificarOperadores = \App\Models\Configuracao::obter('chamados_notificar_operadores_email', true);
            
            if (!$notificarOperadores) {
                // Configuração desabilitada, não envia e-mail para operadores
                return;
            }

            // Se tiver responsável vinculado, notificar ele (e possivelmente email geral)
            if ($chamado->fk_id_user_responsavel) {
                $responsavel = User::find($chamado->fk_id_user_responsavel);
                
                if ($responsavel && $responsavel->email) {
                    $emails = [$responsavel->email];
                    $logEmails = [$responsavel->email];
                    
                    // Verificar se deve incluir também email geral
                    // Converter para boolean explicitamente para evitar valores string '0' / '1'
                    $incluirEmailGeralConfig = \App\Models\Configuracao::obter('chamados_incluir_email_geral_quando_responsavel', false);
                    $incluirEmailGeral = (bool) $incluirEmailGeralConfig;
                    $emailGeral = trim((string) \App\Models\Configuracao::obter('chamados_email_geral', ''));
                    
                    \Log::info('Debug - Incluir Email Geral ao Responsável', [
                        'chamado' => $chamado->protocolo,
                        'incluirEmailGeralConfig' => $incluirEmailGeralConfig,
                        'incluirEmailGeral (bool)' => $incluirEmailGeral,
                        'emailGeral' => $emailGeral,
                        'temEmail' => !empty($emailGeral),
                        'emailValido' => filter_var($emailGeral, FILTER_VALIDATE_EMAIL),
                    ]);
                    
                    // APENAS se checkbox está MARCADO (true) E email válido
                    if ($incluirEmailGeral === true && !empty($emailGeral) && filter_var($emailGeral, FILTER_VALIDATE_EMAIL)) {
                        $emails[] = $emailGeral;
                        $logEmails[] = $emailGeral;
                        
                        \Log::info("Email geral INCLUÍDO nas notificações", [
                            'chamado' => $chamado->protocolo,
                            'emailGeral' => $emailGeral,
                        ]);
                    } else {
                        \Log::info("Email geral NÃO incluído nas notificações", [
                            'chamado' => $chamado->protocolo,
                            'motivo' => !$incluirEmailGeral ? 'checkbox desmarcado' : (!empty($emailGeral) ? 'email vazio/inválido' : 'outro motivo'),
                        ]);
                    }
                    
                    Log::info("Notificação de chamado enviada para responsável" . (count($logEmails) > 1 ? " + email geral" : ""), [
                        'chamado' => $chamado->protocolo,
                        'responsavel' => $responsavel->name,
                        'emails' => $logEmails,
                        'total_emails' => count($logEmails),
                    ]);
                }
            } else {
                // Sem responsável → notificar todos operadores/admin (comportamento padrão)
                $emails = User::whereIn('nivel', ['admin', 'operador'])
                    ->whereNotNull('email')
                    ->pluck('email')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
                    
                Log::info("Notificação de chamado enviada para todos operadores (sem responsável definido)", [
                    'chamado' => $chamado->protocolo,
                    'total_emails' => count($emails),
                ]);
            }
        }

        if (empty($emails)) {
            return;
        }

        $urlChamado = route('chamados.show', $chamado->id_chamado);

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new ChamadoMensagemRecebidaMail(
                    $chamado,
                    $mensagem,
                    $remetente->name,
                    $urlChamado
                ));
            } catch (\Throwable $e) {
                Log::warning('Falha ao enviar e-mail de mensagem de chamado', [
                    'chamado' => $chamado->id_chamado,
                    'email' => $email,
                    'erro' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Exclui chamado completamente (com mensagens e anexos)
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!in_array($user->nivel, ['admin', 'operador'])) {
            abort(403, 'Acesso negado');
        }

        $chamado = Chamado::findOrFail($id);

        // Remove anexos do chamado principal
        if ($chamado->anexos && is_array($chamado->anexos)) {
            foreach ($chamado->anexos as $anexo) {
                $path = str_replace('\\', '/', $anexo);
                if (str_starts_with($path, 'storage/')) {
                    $path = substr($path, 8);
                }
                if (!str_starts_with($path, 'chamados/anexos/')) {
                    $path = 'chamados/anexos/' . basename($path);
                }
                Storage::disk('public')->delete($path);
            }
        }

        // Remove anexos de todas as mensagens
        $mensagens = ChamadoMensagem::where('fk_id_chamado', $chamado->id_chamado)->get();
        foreach ($mensagens as $mensagem) {
            if ($mensagem->anexos && is_array($mensagem->anexos)) {
                foreach ($mensagem->anexos as $anexo) {
                    Storage::disk('public')->delete($anexo);
                }
            }
        }

        // Exclui mensagens (cascade vai apagar automaticamente via DB, mas garantimos aqui)
        ChamadoMensagem::where('fk_id_chamado', $chamado->id_chamado)->delete();

        // Exclui o chamado
        $protocolo = $chamado->protocolo;
        $chamado->delete();

        Log::info("Chamado #{$protocolo} excluído por " . $user->name);

        return redirect()->route('chamados.painel')
            ->with('success', "Chamado #{$protocolo} excluído com sucesso.");
    }
}

