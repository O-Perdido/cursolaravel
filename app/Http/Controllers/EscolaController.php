<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use Illuminate\Http\Request;
use App\Models\Estado;
use App\Models\Cidade;
use Illuminate\Database\QueryException;

class EscolaController extends Controller
{

    public function index(Request $request)
    {

        $query = Escola::query();

        // Filtros

        // Filtro por nome
        if ($request->filled('nome_escola')) {
            $query->where('nome_escola', 'like', '%' . $request->nome_escola . '%');
        }

        // Filtro por CNPJ
        if ($request->filled('cnpj')) {
            // limpar o CNPJ para garantir que não haja caracteres especiais
            $request->merge(['cnpj' => preg_replace('/\D/', '', $request->cnpj)]);

            $query->where('numero_cnpj', 'like', '%' . $request->cnpj . '%');
        }

        // Filtro por ordem de cadastro 
        if ($request->has('ordem_cadastro')) {
            $query->orderBy('id_escola', 'desc');
        } else {
            $query->orderBy('nome_escola', 'asc');
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

        $escolas = $query->paginate($perPage)->appends($request->query());
        return view('escolas.index', compact('escolas'));
    }


    public function create()
    {
        $estados = Estado::all();
        return view('escolas.create', compact('estados'));
    }

    public function store(Request $request)
    {
        // Validação dos dados

        $request->validate([

            'nome_escola' => 'required|string',
            'numero_cnpj' => 'required|string',
            'numero_telefone' => 'nullable|string',
            'numero_celular' => 'nullable|string',
            'email' => 'nullable|email',
            'numero_cep' => 'nullable|string',
            'endereco' => 'nullable|string',
            'numero_endereco' => 'nullable|string',
            'complemento_endereco' => 'nullable|string',
            'bairro' => 'nullable|string',
            'fk_id_estado' => 'required|integer',
            'fk_id_cidade' => 'required|integer',
            'nome_representante' => 'nullable|string',
            'cargo_representante' => 'nullable|string',
            'cpf_representante' => 'nullable|string',
            'numero_apolice' => 'nullable|string',
            'nome_seguradora' => 'nullable|string',
            'nao_assina_zapsign' => 'nullable|boolean',

        ]);

        // Criação do registro da escola
        Escola::create([
            'nome_escola' => $request->nome_escola,
            'numero_cnpj' => $request->numero_cnpj,
            'numero_telefone' => $request->numero_telefone,
            'numero_celular' => $request->numero_celular,
            'email' => $request->email,
            'numero_cep' => $request->numero_cep,
            'endereco' => $request->endereco,
            'numero_endereco' => $request->numero_endereco,
            'complemento_endereco' => $request->complemento_endereco,
            'bairro' => $request->bairro,
            'fk_id_estado' => $request->fk_id_estado,
            'fk_id_cidade' => $request->fk_id_cidade,
            'nome_representante' => $request->nome_representante,
            'cargo_representante' => $request->cargo_representante,
            'cpf_representante' => $request->cpf_representante,
            'numero_apolice' => $request->numero_apolice,
            'nome_seguradora' => $request->nome_seguradora,
            'nao_assina_zapsign' => $request->boolean('nao_assina_zapsign'),
        ]);

        // Redirecionamento com sucesso
        return redirect()->route('escolas.index')->with('success', 'Escola cadastrada com sucesso!');

    }

    public function show($id)
    {
        // Encontrar a escola pelo ID
        $escola = Escola::with(['cidade', 'estado'])->findOrFail($id);

        // Retornar a view 'show' com os dados da escola
        return view('escolas.show', compact('escola'));
    }

    public function destroy($id)
    {
        $escola = Escola::findOrFail($id);

        if ($escola->termo()->exists()) {
            return redirect()->route('escolas.index')
                ->with('error', 'Esta instituição de ensino não pode ser excluída pois ela está vinculada a um termo!');
        }

        try {
            $escola->delete();
        } catch (QueryException $e) {
            return redirect()->route('escolas.index')
                ->with('error', 'Erro inesperado ao tentar excluir instituição de ensino!');
        }

        // Redirecionar com sucesso
        return redirect()->route('escolas.index')
            ->with('success', 'Escola excluída com sucesso!');
    }

    public function edit($id)
    {
        $escola = Escola::with('cidade')->findOrFail($id);

        if (!$escola) {
            return redirect()->route('escolas.index')->with('error', 'Escola não encontrada');
        }

        $estados = Estado::all();

        // Verifica se a escola tem cidade e carrega as cidades do estado correspondente
        if ($escola->cidade) {
            $cidades = Cidade::where('fk_id_estado', $escola->cidade->fk_id_estado)->get();
        } else {
            $cidades = collect(); // Retorna uma collection vazia
        }

        return view('escolas.edit', compact('escola', 'estados', 'cidades'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            /*
            'nome_escola' => 'required|string',
            'numero_cnpj' => 'required|string|max:14',
            'numero_telefone' => 'nullable|string|max:15',
            'numero_celular' => 'nullable|string|max:15',
            'email' => 'nullable|email',
            'numero_cep' => 'nullable|string|max:9',
            'endereco' => 'nullable|string',
            'numero_endereco' => 'nullable|string|max:10',
            'complemento_endereco' => 'nullable|string',
            'bairro' => 'nullable|string',
            'fk_id_cidade' => 'required|integer',
            'nome_representante' => 'nullable|string',
            'cargo_representante' => 'nullable|string',
            'cpf_representante' => 'nullable|string|max:11',
            */
        ]);

        $request->merge([
            'nao_assina_zapsign' => $request->boolean('nao_assina_zapsign'),
        ]);

        $escola = Escola::findOrFail($id);
        $escola->update($request->all());

        return redirect()->route('escolas.index')->with('success', 'Escola atualizada com sucesso');
    }


}