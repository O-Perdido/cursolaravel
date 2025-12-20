<?php

namespace App\Http\Controllers;

use App\Models\Chamado;
use App\Models\TipoChamado;
use App\Models\Termo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
                ->where('fk_id_empresa', $user->empresa->id_empresa)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Admin e operador veem todos
            $chamados = Chamado::with(['tipoChamado', 'empresa', 'termo.estagiario', 'solicitante', 'responsavel'])
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
                ->map(function($termo) {
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
        
        $query = Chamado::with(['tipoChamado', 'empresa', 'termo.estagiario', 'solicitante', 'responsavel']);
        
        // Empresa só vê seus próprios chamados
        if ($user->nivel === 'empresa') {
            $query->where('fk_id_empresa', $user->empresa->id_empresa);
        }
        
        $chamado = $query->findOrFail($id);
        
        return view('chamados.show', compact('chamado'));
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
            $query->where(function($q) use ($search) {
                $q->where('numero_termo', 'like', "%{$search}%")
                    ->orWhere('ano_termo', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(numero_termo, '/', ano_termo) LIKE ?", ["%{$search}%"])
                    ->orWhereHas('estagiario', function($subq) use ($search) {
                        $subq->where('nome_estagiario', 'like', "%{$search}%")
                             ->orWhere('numero_cpf', 'like', "%{$search}%");
                    });
            });
        }
        
        $termos = $query->orderBy('numero_termo', 'desc')
            ->limit(20)
            ->get()
            ->map(function($termo) {
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
            $query->where(function($q) use ($numero) {
                $q->where('numero_termo', 'like', "%{$numero}%")
                  ->orWhere('ano_termo', 'like', "%{$numero}%")
                  ->orWhereRaw("CONCAT(numero_termo, '/', ano_termo) LIKE ?", ["%{$numero}%"]);
            });
        }

        if ($nome !== '') {
            $query->whereHas('estagiario', function($q) use ($nome) {
                $q->where('nome_estagiario', 'like', "%{$nome}%");
            });
        }

        if ($cpf !== '') {
            $query->whereHas('estagiario', function($q) use ($cpf) {
                $q->where('numero_cpf', 'like', "%{$cpf}%");
            });
        }

        $termos = $query->orderBy('ano_termo', 'desc')
            ->orderBy('numero_termo', 'desc')
            ->limit(50)
            ->get()
            ->map(function($termo) {
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
}
