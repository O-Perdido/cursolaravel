<?php

namespace App\Http\Controllers;

use App\Models\Supervisor;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class SupervisorController extends Controller
{
    public function index(Request $request)
    {
        $query = Supervisor::query();

        // Filtros

        if ($request->filled('empresa')) {
            $query->where('fk_id_empresa', $request->empresa);
        }

        // Filtro por nome
        if ($request->filled('nome_supervisor')) {
            $query->where('nome_supervisor', 'like', '%' . $request->nome_supervisor . '%');
        }

        // Filtro por CPF
        if ($request->filled('cpf')) {
            // limpar o CNPJ para garantir que não haja caracteres especiais
            $request->merge(['cpf' => preg_replace('/\D/', '', $request->cpf)]);

            $query->where('cpf_supervisor', 'like', '%' . $request->cpf . '%');
        }

        // Filtro por ordem de cadastro 
        if ($request->has('ordem_cadastro')) {
            $query->orderBy('id_supervisor', 'desc');
        } else {
            $query->orderBy('nome_supervisor', 'asc');
        }


        // Paginação profissional
        $perPage = $request->input('per_page', 25);
        if ($perPage === 'all') {
            $supervisores = $query->get();
        } else {
            $supervisores = $query->paginate((int) $perPage)->withQueryString();
        }
        $empresas = Empresa::orderBy('nome_empresa', 'asc')->get();
        return view('supervisores.index', compact('supervisores', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::all();
        return view('supervisores.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome_supervisor' => 'required|string',
            'fk_id_empresa' => 'required|integer',
            'cpf_supervisor' => 'required|string',
            'area_formacao' => 'nullable|string',
            'tempo_experiencia' => 'nullable|string',
        ]);

        // Limpar o CPF para garantir que não haja caracteres especiais
        $validatedData['cpf_supervisor'] = preg_replace('/\D/', '', $validatedData['cpf_supervisor']);

        Supervisor::create($validatedData);
        return redirect()->route('supervisores.index')->with('success', 'Supervisor cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $supervisor = Supervisor::find($id);
        $empresas = Empresa::all();
        return view('supervisores.edit', compact('supervisor', 'empresas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome_supervisor' => 'required|string',
            'fk_id_empresa' => 'required|integer',
            'cpf_supervisor' => 'required|string',
            'area_formacao' => 'nullable|string',
            'tempo_experiencia' => 'nullable|string',
        ]);

        // Limpar o CPF para garantir que não haja caracteres especiais
        $request->merge(['cpf_supervisor' => preg_replace('/\D/', '', $request->cpf_supervisor)]);

        $supervisor = Supervisor::find($id);
        $supervisor->update($request->all());
        return redirect()->route('supervisores.index')->with('success', 'Supervisor atualizado com sucesso');
    }

    public function destroy($id)
    {

        $supervisor = Supervisor::findOrFail($id);

        // Verifica se está vinculado a termos
        if ($supervisor->termos()->exists()) {
            return redirect()->route('supervisores.index')
                ->with('error', 'Não é possível excluir este supervisor pois ele está vinculado a um termo.');
        }

        // Verifica se está vinculado a alterações
        if ($supervisor->alteracoes()->exists()) {
            return redirect()->route('supervisores.index')
                ->with('error', 'Não é possível excluir este supervisor pois ele está vinculado a uma alteração.');
        }

        // Se não houver vínculos, tenta excluir
        try {
            $supervisor->delete();
            return redirect()->route('supervisores.index')
                ->with('success', 'Supervisor excluído com sucesso!');
        } catch (QueryException $e) {
            return redirect()->route('supervisores.index')
                ->with('error', 'Erro inesperado ao tentar excluir o supervisor.');
        }

        //QueryException é um tipo específico de erro (chamado de exceção) que ocorre quando o Laravel tenta fazer alguma operação com o banco de dados e falha
        //Como apagar algo que está ligado com fk. Então no trecho de codigo acima "QueryException $e" ele verifica se tem um erro e atribui ele direto na variavel $e



        // Supervisor::find($id)->delete();
        // return redirect()->route('supervisores.index')->with('success', 'Supervisor excluído com sucesso');
    }
}
