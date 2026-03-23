<?php

namespace App\Http\Controllers;

use App\Models\SigeConcursoCandidato;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SigeConcursoCandidatoController extends Controller
{
    public function index(Request $request)
    {
        $query = SigeConcursoCandidato::with(['cidade.estado', 'user']);

        if ($request->filled('nome')) {
            $query->where('nome_completo', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('cpf')) {
            $cpf = preg_replace('/\D/', '', $request->cpf);
            $query->where('numero_cpf', 'like', '%' . $cpf . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->boolean('ordem_cadastro')) {
            $query->orderBy('id_candidato', 'desc');
        } else {
            $query->orderBy('nome_completo');
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

        $candidatos = $query->paginate($perPage)->appends($request->query());

        return view('sigeconcursos.candidatos.index', compact('candidatos'));
    }

    public function show($id)
    {
        $candidato = SigeConcursoCandidato::with(['cidade.estado', 'user'])->findOrFail($id);

        return view('sigeconcursos.candidatos.show', compact('candidato'));
    }

    public function destroy(Request $request, $id)
    {
        $request->validate([
            'password_confirm' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password_confirm, Auth::user()->password)) {
            return redirect()->route('sigeconcursos.candidatos.index')
                ->with('error', 'A senha informada não confere com o usuário logado.');
        }

        $candidato = SigeConcursoCandidato::with('user')->findOrFail($id);

        try {
            DB::transaction(function () use ($candidato) {
                if ($candidato->user) {
                    $candidato->user->delete();
                }

                $candidato->delete();
            });

            return redirect()->route('sigeconcursos.candidatos.index')
                ->with('success', 'Candidato excluído com sucesso!');
        } catch (QueryException $exception) {
            return redirect()->route('sigeconcursos.candidatos.index')
                ->with('error', 'Não foi possível excluir o candidato porque ele possui vínculos no sistema.');
        }
    }
}