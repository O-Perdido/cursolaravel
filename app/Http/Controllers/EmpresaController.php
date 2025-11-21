<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Cidade;
use App\Models\Estado;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class EmpresaController extends Controller
{
    public function create()
    {
        $estados = Estado::all();
        return view('empresas.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $tipoTaxa = $request->input('tipo_taxa');
        $valorTaxa = $request->input('valor_taxa');

        Empresa::create([
            'nome_empresa' => $request->nome_empresa,
            'numero_cnpj' => $request->numero_cnpj,
            'numero_telefone' => $request->numero_telefone,
            'numero_celular' => $request->numero_celular,
            'email' => $request->email,
            'numero_cep' => $request->numero_cep,
            'endereco' => $request->endereco,
            'numero_endereco' => $request->numero_endereco,
            'complemento_endereco' => $request->complemento_endereco,
            'bairro' => $request->bairro,
            'fk_id_cidade' => $request->fk_id_cidade,
            'nome_representante' => $request->nome_representante,
            'cargo_representante' => $request->cargo_representante,
            'cpf_representante' => $request->cpf_representante,
            'tipo_taxa' => $tipoTaxa,
            'taxa_fixa' => $tipoTaxa === 'fixa' ? $valorTaxa : null,
            'taxa_percentual' => $tipoTaxa === 'percentual' ? $valorTaxa : null,
        ]);

        return redirect()->route('empresas.index')->with('success', 'Empresa criada com sucesso');
    }

    public function edit($id)
    {
        $empresa = Empresa::with('cidade')->find($id);

        if (!$empresa) {
            return redirect()->route('empresas.index')->with('error', 'Empresa não encontrada');
        }

        $estados = Estado::all();

        // Verifica se a empresa tem cidade e carrega as cidades do estado correspondente
        if ($empresa->cidade) {
            $cidades = Cidade::where('fk_id_estado', $empresa->cidade->fk_id_estado)->get();
        } else {
            $cidades = collect(); // Retorna uma collection vazia
        }

        return view('empresas.edit', compact('empresa', 'estados', 'cidades'));
    }

    public function update(Request $request, $id)
    {
        /* $request->validate([
             'nome_empresa' => 'required|string|max:255',
             'numero_cnpj' => 'required|string|max:14|numeric',
             'numero_telefone' => 'nullable|string|max:15|numeric',
             'numero_celular' => 'nullable|string|max:15|numeric',
             'email' => 'nullable|email|max:255',
             'numero_cep' => 'nullable|string|max:9|numeric',
             'endereco' => 'nullable|string|max:255',
             'numero_endereco' => 'nullable|string|max:10|numeric',
             'complemento_endereco' => 'nullable|string|max:255',
             'bairro' => 'nullable|string|max:255',
             'fk_id_cidade' => 'required|integer',
             'nome_representante' => 'nullable|string|max:255',
             'cargo_representante' => 'nullable|string|max:255',
             'cpf_representante' => 'nullable|string|max:11|numeric',
             'tipo_taxa' => 'required|in:fixa,percentual',
             'valor_taxa' => 'required|numeric',
         ]); */

        $empresa = Empresa::findOrFail($id); // Usa findOrFail para retornar 404 se não encontrar

        $tipoTaxa = $request->input('tipo_taxa');
        $valorTaxa = $request->input('valor_taxa');

        $empresa->update([
            'nome_empresa' => $request->nome_empresa,
            'numero_cnpj' => $request->numero_cnpj,
            'numero_telefone' => $request->numero_telefone,
            'numero_celular' => $request->numero_celular,
            'email' => $request->email,
            'numero_cep' => $request->numero_cep,
            'endereco' => $request->endereco,
            'numero_endereco' => $request->numero_endereco,
            'complemento_endereco' => $request->complemento_endereco,
            'bairro' => $request->bairro,
            'fk_id_cidade' => $request->fk_id_cidade,
            'nome_representante' => $request->nome_representante,
            'cargo_representante' => $request->cargo_representante,
            'cpf_representante' => $request->cpf_representante,
            'tipo_taxa' => $tipoTaxa,
            'taxa_fixa' => $tipoTaxa === 'fixa' ? $valorTaxa : null,
            'taxa_percentual' => $tipoTaxa === 'percentual' ? $valorTaxa : null,
        ]);

        return redirect()->route('empresas.index')->with('success', 'Empresa atualizada com sucesso');
    }

    public function show($id)
    {
        $empresa = Empresa::with('cidade')->find($id);
        return view('empresas.show', compact('empresa'));
    }

    public function destroy($id)
    {

        $empresa = Empresa::findOrFail($id);

        if ($empresa->termo()->exists()) {
            return redirect()->route('empresas.index')
                ->with('error', 'Não é possível excluir esta empresa pois ela está vinculada a um termo!');
        }

        if ($empresa->supervisor()->exists()) {
            return redirect()->route('empresas.index')
                ->with('error', 'Não é possível excluir esta empresa pois ela está vinculada a um supervisor!');
        }

        if ($empresa->usuario()->exists()) {
            return redirect()->route('empresas.index')
                ->with('error', 'Não é possível excluir esta empresa pois ela está vinculada a um usuário!');
        }

        if ($empresa->folhaPagamento()->exists()) {
            return redirect()->route('empresa.index')
                ->with('erro', 'Não é possível excluir esta empresa pois ela está vinculada a uma folha de pagamento!');
        }

        try {
            $empresa->delete();
            return redirect()->route('empresas.index')
                ->with('success', 'Empresa excluída com sucesso!');
        } catch (QueryException $e) {
            return redirect()->route('empresas.index')
                ->with('error', 'Erro inesperado ao tentar excluir empresa!');
        }
    }

    public function getCidadesByEstado($id)
    {
        $cidades = Cidade::where('fk_id_estado', $id)->get();
        return response()->json($cidades);
    }

    public function index(Request $request)
    {

        $query = Empresa::query();

        // Filtros

        // Filtro por nome
        if ($request->filled('nome_empresa')) {
            $query->where('nome_empresa', 'like', '%' . $request->nome_empresa . '%');
        }

        // Filtro por CNPJ
        if ($request->filled('cnpj')) {
            // limpar o CNPJ para garantir que não haja caracteres especiais
            $request->merge(['cnpj' => preg_replace('/\D/', '', $request->cnpj)]);

            $query->where('numero_cnpj', 'like', '%' . $request->cnpj . '%');
        }

        // Filtro por ordem de cadastro 
        if ($request->has('ordem_cadastro')) {
            $query->orderBy('id_empresa', 'desc');
        } else {
            $query->orderBy('nome_empresa', 'asc');
        }

        // Itens por página (25, 50, 100, 200, "all")
        $perPageParam = $request->input('per_page');
        $allowed = ['25', '50', '100', '200', 'all'];
        if (!in_array((string)($perPageParam ?? ''), $allowed, true)) {
            $perPageParam = '25';
        }

        if ($perPageParam === 'all') {
            $total = (clone $query)->count();
            $perPage = max(1, (int)$total);
        } else {
            $perPage = (int)$perPageParam;
        }

        $empresas = $query->paginate($perPage)->appends($request->query());

        return view('empresas.index', compact('empresas'));
    }

}
