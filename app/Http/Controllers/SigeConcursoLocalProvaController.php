<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Estado;
use App\Models\SigeConcursoLocalProva;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SigeConcursoLocalProvaController extends Controller
{
    public function index(Request $request)
    {
        $query = SigeConcursoLocalProva::with(['cidade.estado', 'salas']);

        if ($request->filled('nome_local')) {
            $query->where('nome_local', 'like', '%' . $request->nome_local . '%');
        }

        if ($request->filled('cidade')) {
            $query->whereHas('cidade', function ($cidadeQuery) use ($request) {
                $cidadeQuery->where('nm_cidade', 'like', '%' . $request->cidade . '%');
            });
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === '1');
        }

        if ($request->boolean('ordem_cadastro')) {
            $query->orderByDesc('id_local_prova');
        } else {
            $query->orderBy('nome_local');
        }

        $perPage = $this->resolvePerPage($request->input('per_page'), $query->count());
        $locais = $query->paginate($perPage)->appends($request->query());

        return view('sigeconcursos.locais-prova.index', compact('locais'));
    }

    public function create()
    {
        $estados = Estado::orderBy('nm_estado')->get();

        return view('sigeconcursos.locais-prova.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $salas = $this->formatarSalas($request->input('salas', []));

        $local = SigeConcursoLocalProva::create($data);
        $local->salas()->createMany($salas);

        return redirect()->route('sigeconcursos.locais-prova.index')
            ->with('success', 'Local de prova cadastrado com sucesso!');
    }

    public function show($id)
    {
        $local = SigeConcursoLocalProva::with(['cidade.estado', 'salas', 'processos.processo'])->findOrFail($id);

        return view('sigeconcursos.locais-prova.show', compact('local'));
    }

    public function edit($id)
    {
        $local = SigeConcursoLocalProva::with(['cidade.estado', 'salas'])->findOrFail($id);
        $estados = Estado::orderBy('nm_estado')->get();
        $cidades = $local->cidade
            ? Cidade::where('fk_id_estado', $local->cidade->fk_id_estado)->orderBy('nm_cidade')->get()
            : collect();

        return view('sigeconcursos.locais-prova.edit', compact('local', 'estados', 'cidades'));
    }

    public function update(Request $request, $id)
    {
        $local = SigeConcursoLocalProva::findOrFail($id);
        $local->update($this->validateData($request));

        $local->salas()->delete();
        $local->salas()->createMany($this->formatarSalas($request->input('salas', [])));

        return redirect()->route('sigeconcursos.locais-prova.index')
            ->with('success', 'Local de prova atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $local = SigeConcursoLocalProva::findOrFail($id);

        try {
            $local->delete();

            return redirect()->route('sigeconcursos.locais-prova.index')
                ->with('success', 'Local de prova excluído com sucesso!');
        } catch (QueryException $exception) {
            return redirect()->route('sigeconcursos.locais-prova.index')
                ->with('error', 'Não foi possível excluir o local de prova porque ele possui vínculos no sistema.');
        }
    }

    private function validateData(Request $request): array
    {
        $request->merge([
            'numero_cep' => $this->onlyDigits($request->input('numero_cep')),
        ]);

        $data = $request->validate([
            'nome_local' => ['required', 'string', 'max:255'],
            'numero_cep' => ['required', 'digits:8'],
            'endereco' => ['required', 'string', 'max:255'],
            'numero_endereco' => ['required', 'string', 'max:20'],
            'complemento_endereco' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'fk_id_cidade' => ['required', 'exists:tb_cidade,id_cidade'],
            'observacoes' => ['nullable', 'string'],
            'salas' => ['nullable', 'array'],
            'salas.*.nome_sala' => ['nullable', 'string', 'max:120'],
            'salas.*.bloco' => ['nullable', 'string', 'max:120'],
            'salas.*.capacidade_maxima' => ['nullable', 'integer', 'min:1'],
            'salas.*.observacoes' => ['nullable', 'string'],
        ], [
            'fk_id_cidade.exists' => 'Selecione uma cidade válida.',
        ]);

        $data['ativo'] = $request->boolean('ativo', true);

        unset($data['salas']);

        return $data;
    }

    private function formatarSalas(array $salas): array
    {
        return collect($salas)->map(function ($sala) {
            $nomeSala = trim($sala['nome_sala'] ?? '');
            $capacidade = $sala['capacidade_maxima'] ?? null;

            if ($nomeSala === '' && ($capacidade === null || $capacidade === '')) {
                return null;
            }

            return [
                'nome_sala' => $nomeSala,
                'bloco' => trim($sala['bloco'] ?? '') ?: null,
                'capacidade_maxima' => $capacidade !== '' && $capacidade !== null ? (int) $capacidade : 1,
                'observacoes' => trim($sala['observacoes'] ?? '') ?: null,
                'ativo' => true,
            ];
        })->filter()->values()->all();
    }

    private function onlyDigits(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value);

        return $digits === '' ? null : $digits;
    }

    private function resolvePerPage(?string $perPageParam, int $total): int
    {
        $allowed = ['25', '50', '100', '200', 'all'];

        if (!in_array((string) ($perPageParam ?? ''), $allowed, true)) {
            return 25;
        }

        if ($perPageParam === 'all') {
            return max(1, $total);
        }

        return (int) $perPageParam;
    }
}