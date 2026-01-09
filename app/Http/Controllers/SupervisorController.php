<?php

namespace App\Http\Controllers;

use App\Models\Supervisor;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    public function index(Request $request)
    {
        $query = Supervisor::query();

        // Filtros
        // Escopo por empresa quando usuário é do nível "empresa"
        $user = Auth::user();
        if ($user && $user->nivel === 'empresa') {
            $query->where('fk_id_empresa', $user->fk_id_empresa);
        } elseif ($request->filled('empresa')) {
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
        // Lista de empresas para filtros/inputs: usuário empresa só enxerga a própria
        if ($user && $user->nivel === 'empresa') {
            $empresas = Empresa::where('id_empresa', $user->fk_id_empresa)->get();
        } else {
            $empresas = Empresa::orderBy('nome_empresa', 'asc')->get();
        }
        return view('supervisores.index', compact('supervisores', 'empresas'));
    }

    public function create()
    {
        $user = Auth::user();
        // Usuário empresa só pode criar para sua própria empresa
        if ($user && $user->nivel === 'empresa') {
            $empresas = Empresa::where('id_empresa', $user->fk_id_empresa)->get();
        } else {
            $empresas = Empresa::all();
        }
        return view('supervisores.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        // Sanitiza CPF antes de validar para garantir unicidade correta
        if ($request->filled('cpf_supervisor')) {
            $request->merge(['cpf_supervisor' => preg_replace('/\D/', '', $request->cpf_supervisor)]);
        }

        if ($request->filled('celular_supervisor')) {
            $request->merge(['celular_supervisor' => preg_replace('/\D/', '', $request->celular_supervisor)]);
        }

        $user = Auth::user();
        if ($user && $user->nivel === 'empresa') {
            // Garante vínculo antes da validação
            $request->merge(['fk_id_empresa' => $user->fk_id_empresa]);
        }

        $validatedData = $request->validate([
            'nome_supervisor' => 'required|string',
            'fk_id_empresa' => 'required|integer',
            'cpf_supervisor' => 'required|string|unique:tb_supervisores,cpf_supervisor',
            'area_formacao' => 'nullable|string',
            'tempo_experiencia' => 'nullable|string',
            'celular_supervisor' => 'nullable|string|max:20',
            'email_supervisor' => 'nullable|email|max:150',
        ], [
            'cpf_supervisor.unique' => 'Já existe um supervisor cadastrado com este CPF.',
        ]);

        // Forçar vínculo à empresa do usuário quando nível for "empresa"
        if ($user && $user->nivel === 'empresa') {
            $validatedData['fk_id_empresa'] = $user->fk_id_empresa;
        }

        Supervisor::create($validatedData);

        // Se for requisição AJAX (modal), retornar JSON
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Supervisor cadastrado com sucesso!',
                'supervisor' => $validatedData
            ], 201);
        }

        $routeIndex = ($user && $user->nivel === 'empresa')
            ? 'empresa.supervisores.index'
            : 'supervisores.index';

        return redirect()->route($routeIndex)->with('success', 'Supervisor cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $supervisor = Supervisor::find($id);
        $user = Auth::user();
        if ($user && $user->nivel === 'empresa') {
            // Bloquear edição de supervisor de outra empresa
            if (!$supervisor || $supervisor->fk_id_empresa != $user->fk_id_empresa) {
                return redirect()->route('supervisores.index')->with('error', 'Acesso negado para este supervisor.');
            }
            $empresas = Empresa::where('id_empresa', $user->fk_id_empresa)->get();
        } else {
            $empresas = Empresa::all();
        }
        return view('supervisores.edit', compact('supervisor', 'empresas'));
    }

    public function update(Request $request, $id)
    {
        // Sanitiza CPF antes de validar
        if ($request->filled('cpf_supervisor')) {
            $request->merge(['cpf_supervisor' => preg_replace('/\D/', '', $request->cpf_supervisor)]);
        }

        if ($request->filled('celular_supervisor')) {
            $request->merge(['celular_supervisor' => preg_replace('/\D/', '', $request->celular_supervisor)]);
        }

        $user = Auth::user();
        if ($user && $user->nivel === 'empresa') {
            // Garante vínculo antes da validação
            $request->merge(['fk_id_empresa' => $user->fk_id_empresa]);
        }

        $validatedData = $request->validate([
            'nome_supervisor' => 'required|string',
            'fk_id_empresa' => 'required|integer',
            'cpf_supervisor' => 'required|string|unique:tb_supervisores,cpf_supervisor,' . $id . ',id_supervisor',
            'area_formacao' => 'nullable|string',
            'tempo_experiencia' => 'nullable|string',
            'celular_supervisor' => 'nullable|string|max:20',
            'email_supervisor' => 'nullable|email|max:150',
        ], [
            'cpf_supervisor.unique' => 'Já existe um supervisor cadastrado com este CPF.',
        ]);

        $supervisor = Supervisor::find($id);
        if ($user && $user->nivel === 'empresa') {
            if (!$supervisor || $supervisor->fk_id_empresa != $user->fk_id_empresa) {
                return redirect()->route('supervisores.index')->with('error', 'Acesso negado para este supervisor.');
            }
            // Garantir que o vínculo de empresa não seja alterado para outra empresa
            $validatedData['fk_id_empresa'] = $user->fk_id_empresa;
            $supervisor->update($validatedData);
        } else {
            $supervisor->update($validatedData);
        }
        $routeIndex = ($user && $user->nivel === 'empresa')
            ? 'empresa.supervisores.index'
            : 'supervisores.index';

        return redirect()->route($routeIndex)->with('success', 'Supervisor atualizado com sucesso');
    }

    public function destroy($id)
    {

        $supervisor = Supervisor::findOrFail($id);
        $user = Auth::user();
        if ($user && $user->nivel === 'empresa' && $supervisor->fk_id_empresa != $user->fk_id_empresa) {
            return redirect()->route('supervisores.index')->with('error', 'Acesso negado para este supervisor.');
        }

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

        // Define a rota antes do try-catch para estar disponível em ambos os blocos
        $routeIndex = ($user && $user->nivel === 'empresa')
            ? 'empresa.supervisores.index'
            : 'supervisores.index';

        // Se não houver vínculos, tenta excluir
        try {
            $supervisor->delete();

            return redirect()->route($routeIndex)
                ->with('success', 'Supervisor excluído com sucesso!');
        } catch (QueryException $e) {
            return redirect()->route($routeIndex)
                ->with('error', 'Erro inesperado ao tentar excluir o supervisor.');
        }

        //QueryException é um tipo específico de erro (chamado de exceção) que ocorre quando o Laravel tenta fazer alguma operação com o banco de dados e falha
        //Como apagar algo que está ligado com fk. Então no trecho de codigo acima "QueryException $e" ele verifica se tem um erro e atribui ele direto na variavel $e



        // Supervisor::find($id)->delete();
        // return redirect()->route('supervisores.index')->with('success', 'Supervisor excluído com sucesso');
    }
}
