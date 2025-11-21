<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuracao;
use Illuminate\Support\Facades\Auth;

class ConfiguracaoController extends Controller
{
    /**
     * Exibe o formulário de configurações (apenas admin)
     */
    public function index()
    {
        // Verificar se o usuário é admin
        if (Auth::user()->nivel !== 'admin') {
            return redirect()->back()->with('error', 'Acesso negado. Apenas administradores podem acessar as configurações.');
        }

        $configuracoes = Configuracao::orderBy('chave')->get();

        return view('configuracoes.index', compact('configuracoes'));
    }

    /**
     * Atualiza as configurações
     */
    public function update(Request $request)
    {
        // Verificar se o usuário é admin
        if (Auth::user()->nivel !== 'admin') {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $validated = $request->validate([
            'limite_diario_remessa' => 'required|numeric|min:0',
        ]);

        Configuracao::definir(
            'limite_diario_remessa',
            $validated['limite_diario_remessa'],
            'Limite diário de valor para arquivo de remessa bancária',
            'decimal'
        );

        return redirect()->route('configuracoes.index')->with('success', 'Configurações atualizadas com sucesso!');
    }
}
