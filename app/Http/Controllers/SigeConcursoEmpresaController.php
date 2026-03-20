<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Estado;
use App\Models\SigeConcursoEmpresa;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SigeConcursoEmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = SigeConcursoEmpresa::query();

        if ($request->filled('nome_razao_social')) {
            $query->where('nome_razao_social', 'like', '%' . $request->nome_razao_social . '%');
        }

        if ($request->filled('cnpj')) {
            $cnpj = preg_replace('/\D/', '', $request->cnpj);
            $query->where('numero_cnpj', 'like', '%' . $cnpj . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->boolean('ordem_cadastro')) {
            $query->orderBy('id_empresa', 'desc');
        } else {
            $query->orderBy('nome_razao_social', 'asc');
        }

        $perPageParam = $request->input('per_page');
        $allowed = ['25', '50', '100', '200', 'all'];

        if (!in_array((string) ($perPageParam ?? ''), $allowed, true)) {
            $perPageParam = '25';
        }

        if ($perPageParam === 'all') {
            $total = (clone $query)->count();
            $perPage = max(1, (int) $total);
        } else {
            $perPage = (int) $perPageParam;
        }

        $orgaos = $query->paginate($perPage)->appends($request->query());

        return view('sigeconcursos.orgaos.index', compact('orgaos'));
    }

    public function create()
    {
        $estados = Estado::orderBy('nm_estado')->get();

        return view('sigeconcursos.orgaos.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        SigeConcursoEmpresa::create($data);

        return redirect()->route('sigeconcursos.orgaos.index')
            ->with('success', 'Órgão público/empresa cadastrado com sucesso!');
    }

    public function show($id)
    {
        $orgao = SigeConcursoEmpresa::with('cidade.estado')->findOrFail($id);

        return view('sigeconcursos.orgaos.show', compact('orgao'));
    }

    public function edit($id)
    {
        $orgao = SigeConcursoEmpresa::with('cidade.estado')->findOrFail($id);
        $estados = Estado::orderBy('nm_estado')->get();

        if ($orgao->cidade) {
            $cidades = Cidade::where('fk_id_estado', $orgao->cidade->fk_id_estado)
                ->orderBy('nm_cidade')
                ->get();
        } else {
            $cidades = collect();
        }

        return view('sigeconcursos.orgaos.edit', compact('orgao', 'estados', 'cidades'));
    }

    public function update(Request $request, $id)
    {
        $orgao = SigeConcursoEmpresa::findOrFail($id);
        $data = $this->validateData($request, $orgao->id_empresa);

        $orgao->update($data);

        return redirect()->route('sigeconcursos.orgaos.index')
            ->with('success', 'Órgão público/empresa atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $orgao = SigeConcursoEmpresa::findOrFail($id);

        try {
            $orgao->delete();

            return redirect()->route('sigeconcursos.orgaos.index')
                ->with('success', 'Órgão público/empresa excluído com sucesso!');
        } catch (QueryException $exception) {
            return redirect()->route('sigeconcursos.orgaos.index')
                ->with('error', 'Não foi possível excluir o cadastro porque ele possui vínculos no sistema.');
        }
    }

    private function validateData(Request $request, ?int $orgaoId = null): array
    {
        $request->merge([
            'numero_cnpj' => $this->onlyDigits($request->input('numero_cnpj')),
            'numero_telefone' => $this->onlyDigits($request->input('numero_telefone')),
            'numero_celular' => $this->onlyDigits($request->input('numero_celular')),
            'numero_cep' => $this->onlyDigits($request->input('numero_cep')),
            'cpf_representante' => $this->onlyDigits($request->input('cpf_representante')),
        ]);

        $data = $request->validate([
            'nome_razao_social' => ['required', 'string', 'max:255'],
            'numero_cnpj' => ['required', 'digits:14', Rule::unique('sigeconcursos_tb_empresas', 'numero_cnpj')->ignore($orgaoId, 'id_empresa')],
            'numero_telefone' => ['nullable', 'digits_between:10,11'],
            'numero_celular' => ['nullable', 'digits_between:10,11'],
            'email' => ['required', 'email', 'max:255'],
            'numero_cep' => ['required', 'digits:8'],
            'endereco' => ['required', 'string', 'max:255'],
            'numero_endereco' => ['required', 'string', 'max:20'],
            'complemento_endereco' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'fk_id_cidade' => ['required', 'exists:tb_cidade,id_cidade'],
            'nome_representante' => ['required', 'string', 'max:255'],
            'cargo_representante' => ['required', 'string', 'max:255'],
            'cpf_representante' => ['required', 'digits:11'],
            'dados_bancarios' => ['nullable', 'string'],
        ], [
            'numero_cnpj.unique' => 'Já existe um cadastro com este CNPJ.',
            'fk_id_cidade.exists' => 'Selecione uma cidade válida.',
        ]);

        if (!$this->isValidCnpj($data['numero_cnpj'])) {
            throw ValidationException::withMessages([
                'numero_cnpj' => 'Informe um CNPJ válido.',
            ]);
        }

        if (!$this->isValidCpf($data['cpf_representante'])) {
            throw ValidationException::withMessages([
                'cpf_representante' => 'Informe um CPF válido para o representante.',
            ]);
        }

        return $data;
    }

    private function onlyDigits(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value);

        return $digits === '' ? null : $digits;
    }

    private function isValidCpf(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($digit = 9; $digit < 11; $digit++) {
            $sum = 0;

            for ($index = 0; $index < $digit; $index++) {
                $sum += (int) $cpf[$index] * (($digit + 1) - $index);
            }

            $check = ((10 * $sum) % 11) % 10;

            if ((int) $cpf[$digit] !== $check) {
                return false;
            }
        }

        return true;
    }

    private function isValidCnpj(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $weightsFirst = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weightsSecond = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($index = 0; $index < 12; $index++) {
            $sum += (int) $cnpj[$index] * $weightsFirst[$index];
        }

        $digit = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
        if ((int) $cnpj[12] !== $digit) {
            return false;
        }

        $sum = 0;
        for ($index = 0; $index < 13; $index++) {
            $sum += (int) $cnpj[$index] * $weightsSecond[$index];
        }

        $digit = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

        return (int) $cnpj[13] === $digit;
    }
}