<?php

namespace App\Http\Controllers;

use App\Models\TipoChamado;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class TipoChamadoController extends Controller
{
    /**
     * Lista tipos de chamados (para configuração admin)
     */
    public function index()
    {
        $tipos = TipoChamado::orderBy('ordem')->orderBy('nome')->get();
        return view('admin.tipos-chamados.index', compact('tipos'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        return view('admin.tipos-chamados.create');
    }

    /**
     * Armazena novo tipo
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100|unique:tb_tipos_chamados,nome',
            'descricao' => 'nullable|string|max:500',
            'ordem' => 'nullable|integer|min:0',
        ], [
            'nome.required' => 'Nome é obrigatório.',
            'nome.unique' => 'Já existe um tipo de chamado com este nome.',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        TipoChamado::create([
            'nome' => $request->nome,
            'slug' => Str::slug($request->nome),
            'descricao' => $request->descricao,
            'sistema' => false,
            'ativo' => true,
            'ordem' => $request->ordem ?? 99,
        ]);
        
        return redirect()->route('admin.tipos-chamados.index')
            ->with('success', 'Tipo de chamado criado com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $tipo = TipoChamado::findOrFail($id);
        return view('admin.tipos-chamados.edit', compact('tipo'));
    }

    /**
     * Atualiza tipo
     */
    public function update(Request $request, $id)
    {
        $tipo = TipoChamado::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100|unique:tb_tipos_chamados,nome,' . $id . ',id_tipo_chamado',
            'descricao' => 'nullable|string|max:500',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'required|boolean',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $tipo->update([
            'nome' => $request->nome,
            'slug' => Str::slug($request->nome),
            'descricao' => $request->descricao,
            'ordem' => $request->ordem ?? $tipo->ordem,
            'ativo' => $request->ativo,
        ]);
        
        return redirect()->route('admin.tipos-chamados.index')
            ->with('success', 'Tipo de chamado atualizado com sucesso!');
    }

    /**
     * Remove tipo (apenas se não for do sistema)
     */
    public function destroy($id)
    {
        $tipo = TipoChamado::findOrFail($id);
        
        if ($tipo->sistema) {
            return back()->with('error', 'Tipos de chamado do sistema não podem ser removidos.');
        }
        
        if ($tipo->chamados()->count() > 0) {
            return back()->with('error', 'Este tipo possui chamados associados e não pode ser removido.');
        }
        
        $tipo->delete();
        
        return back()->with('success', 'Tipo de chamado removido com sucesso!');
    }

    /**
     * API para listar tipos ativos (para o modal)
     */
    public function tiposAtivos()
    {
        $tipos = TipoChamado::ativo()->ordenado()->get();
        return response()->json($tipos);
    }
}
