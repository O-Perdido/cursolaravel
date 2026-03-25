<?php

namespace App\Http\Controllers;

use App\Models\SigeConcursoCargo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SigeConcursoCargoController extends Controller
{
    public function index(Request $request)
    {
        $query = SigeConcursoCargo::query();

        if ($request->filled('nome_cargo')) {
            $query->where('nome_cargo', 'like', '%' . $request->nome_cargo . '%');
        }

        if ($request->filled('escolaridade_minima')) {
            $query->where('escolaridade_minima', 'like', '%' . $request->escolaridade_minima . '%');
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === '1');
        }

        $query->orderBy('nome_cargo');

        $perPage = $this->resolvePerPage($request->input('per_page'), $query->count());
        $cargos = $query->paginate($perPage)->appends($request->query());

        return view('sigeconcursos.cargos.index', compact('cargos'));
    }

    public function create()
    {
        return view('sigeconcursos.cargos.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        SigeConcursoCargo::create($data);

        return redirect()->route('sigeconcursos.cargos.index')
            ->with('success', 'Cargo cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $cargo = SigeConcursoCargo::findOrFail($id);

        return view('sigeconcursos.cargos.edit', compact('cargo'));
    }

    public function update(Request $request, $id)
    {
        $cargo = SigeConcursoCargo::findOrFail($id);
        $cargo->update($this->validateData($request));

        return redirect()->route('sigeconcursos.cargos.index')
            ->with('success', 'Cargo atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $cargo = SigeConcursoCargo::findOrFail($id);

        try {
            $cargo->delete();

            return redirect()->route('sigeconcursos.cargos.index')
                ->with('success', 'Cargo excluído com sucesso!');
        } catch (QueryException $exception) {
            return redirect()->route('sigeconcursos.cargos.index')
                ->with('error', 'Não foi possível excluir o cargo porque ele possui vínculos no sistema.');
        }
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'nome_cargo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'escolaridade_minima' => ['nullable', 'string', 'max:120'],
        ]);

        $data['ativo'] = $request->boolean('ativo', true);

        return $data;
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