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

        // Carregar tipos de chamado para gestão rápida na tela de configurações
        $tipos = \App\Models\TipoChamado::orderBy('ordem')->orderBy('nome')->get();

        return view('configuracoes.index', compact('configuracoes', 'tipos'));
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
            'dias_padrao_calculo_folha' => 'required|integer|min:1|max:31',
            // Configurações do módulo de chamados
            'chamados_max_anexos' => 'nullable|integer|min:0',
            'chamados_max_tamanho_anexo_mb' => 'nullable|numeric|min:0',
            'chamados_permitir_outros_empresa' => 'nullable|boolean',
        ]);

        Configuracao::definir(
            'limite_diario_remessa',
            $validated['limite_diario_remessa'],
            'Limite diário de valor para arquivo de remessa bancária',
            'decimal'
        );

        Configuracao::definir(
            'dias_padrao_calculo_folha',
            $validated['dias_padrao_calculo_folha'],
            'Número de dias padrão usado como base para cálculo proporcional nas folhas de pagamento',
            'numero'
        );

        // Salvar configurações adicionais do módulo de chamados (se presentes)
        if ($request->has('chamados_max_anexos')) {
            Configuracao::definir(
                'chamados_max_anexos',
                $validated['chamados_max_anexos'] ?? 0,
                'Quantidade máxima de anexos por chamado',
                'numero'
            );
        }

        if ($request->has('chamados_max_tamanho_anexo_mb')) {
            Configuracao::definir(
                'chamados_max_tamanho_anexo_mb',
                $validated['chamados_max_tamanho_anexo_mb'] ?? 0,
                'Tamanho máximo de cada anexo (MB)',
                'decimal'
            );
        }

        // Checkbox pode não vir no request quando desmarcado; garantir persistência
        $permitirOutros = $request->boolean('chamados_permitir_outros_empresa');
        Configuracao::definir(
            'chamados_permitir_outros_empresa',
            $permitirOutros ? '1' : '0',
            'Permitir empresas abrirem chamados do tipo "Outros" (genérico)',
            'boolean'
        );

        return redirect()->route('configuracoes.index')->with('success', 'Configurações atualizadas com sucesso!');
    }
}
