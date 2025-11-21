<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class LocalController extends Controller
{
    public function index(Request $request)
    {
        $query = Local::with('empresa');

        if ($request->filled('empresa')) {
            $query->where('fk_id_empresa', $request->empresa);
        }

        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        $locais = $query->orderBy('descricao', 'asc')->get();

        // Sem views por enquanto: retornar JSON simples
        return response()->json($locais);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'fk_id_empresa' => 'required|integer',
        ]);

        $local = Local::create($validated);
        return response()->json($local, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'descricao' => 'sometimes|required|string|max:255',
            'fk_id_empresa' => 'sometimes|required|integer',
        ]);

        $local = Local::findOrFail($id);
        $local->update($validated);

        return response()->json($local);
    }

    public function destroy($id)
    {
        $local = Local::findOrFail($id);

        try {
            $local->delete();
            return response()->json(['message' => 'Local excluído com sucesso.']);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Não foi possível excluir o local.'], 409);
        }
    }

    // Endpoints para usuários do tipo "unidade concedente" (empresa)
    public function meusLocais(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->nivel !== 'empresa' || empty($user->fk_id_empresa)) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $query = Local::query()->where('fk_id_empresa', $user->fk_id_empresa);
        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }
        $locais = $query->orderBy('descricao', 'asc')->get();
        return response()->json($locais);
    }

    public function atualizarMeuLocal(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->nivel !== 'empresa' || empty($user->fk_id_empresa)) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $validated = $request->validate([
            'descricao' => 'sometimes|required|string|max:255',
        ]);

        $local = Local::where('id_local', $id)
            ->where('fk_id_empresa', $user->fk_id_empresa)
            ->firstOrFail();

        $local->update($validated);
        return response()->json($local);
    }

    public function criarMeuLocal(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->nivel !== 'empresa' || empty($user->fk_id_empresa)) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
        ]);

        $local = Local::create([
            'descricao' => $validated['descricao'],
            'fk_id_empresa' => $user->fk_id_empresa,
        ]);

        return response()->json($local, 201);
    }
}
