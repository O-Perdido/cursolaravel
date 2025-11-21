<?php

namespace App\Http\Controllers;

use App\Models\Representante;
use App\Models\Escola;
use App\Models\Empresa;
use Illuminate\Http\Request;

class RepresentanteController extends Controller
{
    /**
     * Store a new representante
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cargo' => 'required|string|max:100',
            'cpf' => 'nullable|string|size:14',
            'email' => 'required|email|max:255',
            'representavel_type' => 'required|in:App\\Models\\Escola,App\\Models\\Empresa',
            'representavel_id' => 'required|integer',
        ]);

        // Validar se a entidade existe
        $modelClass = $validated['representavel_type'];
        $primaryKey = $modelClass === 'App\\Models\\Escola' ? 'id_escola' : 'id_empresa';
        
        if (!$modelClass::where($primaryKey, $validated['representavel_id'])->exists()) {
            return redirect()->back()->with('error', 'Entidade não encontrada.');
        }

        Representante::create($validated);

        return redirect()->back()->with('success', 'Representante cadastrado com sucesso!');
    }

    /**
     * Update representante
     */
    public function update(Request $request, $id)
    {
        $representante = Representante::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cargo' => 'required|string|max:100',
            'cpf' => 'nullable|string|size:14',
            'email' => 'required|email|max:255',
        ]);

        $representante->update($validated);

        return redirect()->back()->with('success', 'Representante atualizado com sucesso!');
    }

    /**
     * Delete representante
     */
    public function destroy($id)
    {
        try {
            $representante = Representante::findOrFail($id);
            $representante->delete();

            return redirect()->back()->with('success', 'Representante excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao excluir representante: ' . $e->getMessage());
        }
    }

    /**
     * Get representantes by entity (for AJAX)
     */
    public function getByEntity(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        $representantes = Representante::where('representavel_type', $type)
            ->where('representavel_id', $id)
            ->get();

        return response()->json($representantes);
    }
}
