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
        $vagas = $query->orderByDesc('created_at')->paginate(20);
        return view('vagas.index', compact('vagas'));
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
        $validated = $request->validate([
            'atividades' => 'required|string',
            'nome_orientador' => 'required|string',
            'cargo_orientador' => 'required|string',
            'data_inicio' => 'required|date',
            'data_termino' => 'required|date|after:data_inicio',
            'horario' => 'required|string',
            'fk_id_local' => 'nullable|exists:tb_local,id_local',
            'fk_id_empresa' => 'required|exists:tb_empresas,id_empresa',
            'lotacao' => 'required|string',
            'valor_bolsa' => 'required|numeric',
            'valor_auxilio_transporte' => 'required|numeric',
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
            $numeroVaga = sprintf('%s-%03d', $ano, $seq);

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
        $validated = $request->validate([
            'atividades' => 'required|string',
            'nome_orientador' => 'required|string',
            'cargo_orientador' => 'required|string',
            'data_inicio' => 'required|date',
            'data_termino' => 'required|date|after:data_inicio',
            'horario' => 'required|string',
            'fk_id_local' => 'nullable|exists:tb_local,id_local',
            'lotacao' => 'required|string',
            'valor_bolsa' => 'required|numeric',
            'valor_auxilio_transporte' => 'required|numeric',
        ]);
        
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
}
