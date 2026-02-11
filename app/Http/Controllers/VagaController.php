<?php
namespace App\Http\Controllers;

use App\Models\Vaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VagaController extends Controller
{
    // Listagem de vagas (admin/operador vê todas, empresa vê só as suas)
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Vaga::query();
        if ($user->nivel === 'empresa') {
            $query->where('fk_id_empresa', $user->fk_id_empresa);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('empresa') && $user->nivel !== 'empresa') {
            $query->where('fk_id_empresa', $request->input('empresa'));
        }
        
        // Itens por página (25, 50, 100, 200, "all")
        $perPageParam = $request->input('per_page');
        $allowed = ['25', '50', '100', '200', 'all'];
        if (!in_array((string)($perPageParam ?? ''), $allowed, true)) {
            $perPageParam = '25';
        }

        if ($perPageParam === 'all') {
            // Paginar tudo em uma única página mantendo a API do paginator
            $total = (clone $query)->count();
            $perPage = max(1, (int)$total);
        } else {
            $perPage = (int)$perPageParam;
        }
        
        $vagas = $query->orderByDesc('created_at')->paginate($perPage)->appends($request->query());
        $empresas = \App\Models\Empresa::orderBy('nome_empresa', 'asc')->get();
        return view('vagas.index', compact('vagas', 'empresas'));
    }

    // Formulário de criação
    public function create()
    {
        $user = Auth::user();
        $empresas = [];
        $locais = collect();
        $empresaSelecionada = null;

        if ($user->nivel === 'empresa') {
            $empresaSelecionada = $user->fk_id_empresa;
            $locais = \App\Models\Local::where('fk_id_empresa', $empresaSelecionada)
                ->orderBy('descricao')
                ->get();
        } else {
            $empresas = \App\Models\Empresa::orderBy('nome_empresa')->get(['id_empresa','nome_empresa']);
        }

        return view('vagas.create', compact('locais', 'empresas', 'empresaSelecionada'));
    }

    // Salvar nova vaga
    public function store(Request $request)
    {
        $user = Auth::user();
        $empresaId = $user->nivel === 'empresa' ? $user->fk_id_empresa : $request->input('fk_id_empresa');
        $request->merge(['fk_id_empresa' => $empresaId]);
        
        $this->normalizarEstagiarioDefinido($request);
        
        $validated = $request->validate([
            'titulo_vaga' => 'required|string|max:150',
            'atividades' => 'required|string',
            'fk_id_supervisor' => 'required|integer|exists:tb_supervisores,id_supervisor',
            'data_inicio' => 'required|date',
            'data_termino' => 'required|date|after:data_inicio',
            'horario' => 'required|string|max:255',
            'fk_id_local' => 'nullable|exists:tb_local,id_local',
            'fk_id_empresa' => 'required|exists:tb_empresas,id_empresa',
            'lotacao' => 'required|string',
            'valor_bolsa' => 'required|numeric',
            'valor_auxilio_transporte' => 'nullable|numeric',
            'tem_estagiario_definido' => 'required|boolean',
            'nome_estagiario' => 'nullable|string|max:150',
            'contato_whatsapp' => 'nullable|string|max:20',
            'contato_email' => 'nullable|email',
        ]);
        
        // Validar se data_termino não está no passado
        if (strtotime($validated['data_termino']) < strtotime(date('Y-m-d'))) {
            return back()->withErrors(['data_termino' => 'A data de término não pode estar no passado.'])->withInput();
        }
        // Geração transacional do número da vaga por empresa/ano
        $vaga = DB::transaction(function () use ($validated, $empresaId) {
            $ano = date('Y');
            // Bloqueia as linhas da empresa no ano corrente para calcular o próximo sequencial
            $lastSeq = Vaga::where('fk_id_empresa', $empresaId)
                ->whereYear('created_at', $ano)
            ->select(DB::raw("MAX(CAST(SUBSTRING_INDEX(numero_vaga,'-',-1) AS UNSIGNED)) as max_seq"))
                ->lockForUpdate()
                ->value('max_seq');
            $seq = ($lastSeq ? intval($lastSeq) : 0) + 1;
            $numeroVaga = sprintf('%s-%04d', $ano, $seq);

            return Vaga::create(array_merge($validated, [
                'numero_vaga' => $numeroVaga,
                'status' => 'disponivel',
                'publicada_em' => now(),
            ]));
        });
        return redirect()->route('vagas.index')->with('success', 'Vaga cadastrada com sucesso!');
    }

    // Formulário de edição
    public function edit($id)
    {
        $vaga = Vaga::findOrFail($id);
        $locais = \App\Models\Local::where('fk_id_empresa', $vaga->fk_id_empresa)
            ->orderBy('descricao')
            ->get();
        return view('vagas.edit', compact('vaga', 'locais'));
    }

    // Atualizar vaga
    public function update(Request $request, $id)
    {
        $vaga = Vaga::findOrFail($id);
        if ($vaga->fk_id_termo) {
            return back()->withErrors(['msg' => 'Não é possível editar vaga vinculada a termo.']);
        }
        
        $this->normalizarEstagiarioDefinido($request);
        
        $rules = [
            'titulo_vaga' => 'required|string|max:150',
            'atividades' => 'required|string',
            'fk_id_supervisor' => 'required|integer|exists:tb_supervisores,id_supervisor',
            'data_inicio' => 'required|date',
            'data_termino' => 'required|date|after:data_inicio',
            'horario' => 'required|string|max:255',
            'fk_id_local' => 'nullable|exists:tb_local,id_local',
            'lotacao' => 'required|string',
            'valor_bolsa' => 'required|numeric',
            'valor_auxilio_transporte' => 'nullable|numeric',
            'tem_estagiario_definido' => 'required|boolean',
            'nome_estagiario' => 'nullable|string|max:150',
            'contato_whatsapp' => 'nullable|string|max:20',
            'contato_email' => 'nullable|email',
        ];

        $user = Auth::user();
        if ($user->nivel === 'empresa') {
            $rules['status'] = 'required|in:disponivel,suspensa';
        }

        $validated = $request->validate($rules);
        
        // Validar se data_termino não está no passado
        if (strtotime($validated['data_termino']) < strtotime(date('Y-m-d'))) {
            return back()->withErrors(['data_termino' => 'A data de término não pode estar no passado.'])->withInput();
        }
        
        $vaga->update($validated);
        return redirect()->route('vagas.index')->with('success', 'Vaga atualizada com sucesso!');
    }

    // Excluir vaga
    public function destroy($id)
    {
        $vaga = Vaga::findOrFail($id);
        if ($vaga->fk_id_termo) {
            return back()->withErrors(['msg' => 'Não é possível excluir vaga vinculada a termo.']);
        }
        $vaga->delete();
        return redirect()->route('vagas.index')->with('success', 'Vaga excluída com sucesso!');
    }

    // AJAX: lista de locais por empresa
    public function getLocaisPorEmpresa(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:tb_empresas,id_empresa'
        ]);
        $locais = \App\Models\Local::where('fk_id_empresa', $request->input('empresa_id'))
            ->orderBy('descricao')
            ->get(['id_local as id', 'descricao']);
        return response()->json($locais);
    }

    // AJAX: lista de supervisores por empresa
    public function getSupervisoresPorEmpresa(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:tb_empresas,id_empresa'
        ]);
        $supervisores = \App\Models\Supervisor::where('fk_id_empresa', $request->input('empresa_id'))
            ->orderBy('nome_supervisor')
            ->get(['id_supervisor as id', 'nome_supervisor']);
        return response()->json($supervisores);
    }

    // AJAX: obter informações da vaga (incluindo dados do estagiário)
    public function getVagaInfo(Request $request, $id)
    {
        $request->validate([
            'vaga_id' => 'nullable|integer|exists:tb_vagas,id_vaga'
        ]);
        
        $vagaId = $request->input('vaga_id', $id);
        $vaga = Vaga::find($vagaId);
        
        if (!$vaga) {
            return response()->json(['error' => 'Vaga não encontrada'], 404);
        }
        
        return response()->json([
            'id_vaga' => $vaga->id_vaga,
            'nome_estagiario' => $vaga->nome_estagiario,
            'contato_whatsapp' => $vaga->contato_whatsapp,
            'contato_email' => $vaga->contato_email,
            'tem_estagiario_definido' => $vaga->tem_estagiario_definido,
            'fk_id_supervisor' => $vaga->fk_id_supervisor,
            'data_inicio' => $vaga->data_inicio,
            'data_termino' => $vaga->data_termino,
            'horario' => $vaga->horario,
            'lotacao' => $vaga->lotacao,
            'valor_bolsa' => $vaga->valor_bolsa,
            'valor_auxilio_transporte' => $vaga->valor_auxilio_transporte,
        ]);
    }

    private function normalizarEstagiarioDefinido(Request $request): void
    {
        $nome = trim((string) $request->input('nome_estagiario', ''));
        $whatsapp = trim((string) $request->input('contato_whatsapp', ''));
        $email = trim((string) $request->input('contato_email', ''));

        $dadosPreenchidos = $nome !== '' || $whatsapp !== '' || $email !== '';
        $flagSolicitada = in_array($request->input('tem_estagiario_definido'), ['sim', '1', 1, true, 'true'], true);
        $temEstagiario = $dadosPreenchidos || $flagSolicitada;

        $request->merge([
            'nome_estagiario' => $nome,
            'contato_whatsapp' => $whatsapp,
            'contato_email' => $email,
            'tem_estagiario_definido' => $temEstagiario,
        ]);
    }
}
