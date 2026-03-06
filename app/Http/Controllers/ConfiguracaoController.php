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
        // Log de entrada
        \Log::info('ConfiguracaoController@update chamado', [
            'aba' => $request->input('aba'),
            'all_inputs' => $request->except('_token'),
        ]);

        // Verificar se o usuário é admin
        if (Auth::user()->nivel !== 'admin') {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $aba = $request->input('aba', 'remessa');

        // Validar apenas os campos da aba específica
        if ($aba === 'remessa') {
            $validated = $request->validate([
                'limite_diario_remessa' => 'required|numeric|min:0',
                'dias_padrao_calculo_folha' => 'required|integer|min:1|max:31',
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
        } elseif ($aba === 'chamados') {
            \Log::info('Processando aba chamados na configuração');
            
            // Validação especial para email geral (pode ser vazio)
            $emailGeral = trim($request->input('chamados_email_geral', ''));
            \Log::info('Email geral validado', ['email' => $emailGeral]);
            
            if (!empty($emailGeral) && !filter_var($emailGeral, FILTER_VALIDATE_EMAIL)) {
                \Log::warning('Email geral inválido', ['email' => $emailGeral]);
                return redirect()
                    ->route('configuracoes.index', ['tab' => 'chamados'])
                    ->withErrors(['chamados_email_geral' => 'Por favor, digite um email válido ou deixe em branco.'])
                    ->withInput();
            }

            // Converter valores "on" dos checkboxes para "1" antes de validar
            if ($request->has('chamados_permitir_outros_empresa')) {
                $request->merge(['chamados_permitir_outros_empresa' => '1']);
            }
            if ($request->has('chamados_notificar_operadores_email')) {
                $request->merge(['chamados_notificar_operadores_email' => '1']);
            }
            if ($request->has('chamados_incluir_email_geral_quando_responsavel')) {
                $request->merge(['chamados_incluir_email_geral_quando_responsavel' => '1']);
            }

            try {
                \Log::info('Antes de validate - inputs:', $request->all());
                
                $validated = $request->validate([
                    'chamados_max_anexos' => 'nullable|integer|min:0',
                    'chamados_max_tamanho_anexo_mb' => 'nullable|numeric|min:0',
                    'chamados_permitir_outros_empresa' => 'nullable|boolean',
                    'chamados_notificar_operadores_email' => 'nullable|boolean',
                    'chamados_incluir_email_geral_quando_responsavel' => 'nullable|boolean',
                ]);
                
                \Log::info('Validação da aba chamados passou', ['validated' => $validated]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Erro de validação chamados', [
                    'errors' => $e->errors()
                ]);
                throw $e;
            }

            // Salvar configurações do módulo de chamados
            Configuracao::definir(
                'chamados_max_anexos',
                $validated['chamados_max_anexos'] ?? 0,
                'Quantidade máxima de anexos por chamado',
                'numero'
            );
            \Log::info('Salvou chamados_max_anexos');

            Configuracao::definir(
                'chamados_max_tamanho_anexo_mb',
                $validated['chamados_max_tamanho_anexo_mb'] ?? 0,
                'Tamanho máximo de cada anexo (MB)',
                'decimal'
            );
            \Log::info('Salvou chamados_max_tamanho_anexo_mb');

            // Checkbox pode não vir no request quando desmarcado; garantir persistência
            $permitirOutros = $request->boolean('chamados_permitir_outros_empresa');
            Configuracao::definir(
                'chamados_permitir_outros_empresa',
                $permitirOutros ? '1' : '0',
                'Permitir empresas abrirem chamados do tipo "Outros" (genérico)',
                'boolean'
            );
            \Log::info('Salvou chamados_permitir_outros_empresa', ['value' => $permitirOutros ? '1' : '0']);

            // Notificar operadores por email
            $notificarOperadores = $request->boolean('chamados_notificar_operadores_email');
            Configuracao::definir(
                'chamados_notificar_operadores_email',
                $notificarOperadores ? '1' : '0',
                'Habilitar notificações por e-mail para operadores/admin',
                'boolean'
            );
            \Log::info('Salvou chamados_notificar_operadores_email', ['value' => $notificarOperadores ? '1' : '0']);

            // Email geral/administrativo - já validado acima
            Configuracao::definir(
                'chamados_email_geral',
                $emailGeral,
                'Email geral que recebe cópia das notificações de chamados',
                'texto'
            );
            \Log::info('Salvou chamados_email_geral', ['value' => $emailGeral]);

            // Incluir email geral quando há responsável
            // Usar (bool) para converter corretamente o valor do checkbox
            $incluirEmailGeral = (bool) $request->input('chamados_incluir_email_geral_quando_responsavel', false);
            
            Configuracao::definir(
                'chamados_incluir_email_geral_quando_responsavel',
                $incluirEmailGeral ? '1' : '0',
                'Se true, inclui email geral nas notificações mesmo quando há responsável definido',
                'boolean'
            );
            \Log::info('Salvou chamados_incluir_email_geral_quando_responsavel', ['value' => $incluirEmailGeral ? '1' : '0']);
        } elseif ($aba === 'processos') {
            $validated = $request->validate([
                'processos_empresa_pode_ver_inscritos' => 'nullable|boolean',
                'processos_empresa_apenas_deferidos' => 'nullable|boolean',
                'processos_empresa_pode_exportar' => 'nullable|boolean',
            ]);

            // Configurações de Processos Seletivos para Empresas
            $empresaPodeVerInscritos = $request->boolean('processos_empresa_pode_ver_inscritos');
            Configuracao::definir(
                'processos_empresa_pode_ver_inscritos',
                $empresaPodeVerInscritos ? '1' : '0',
                'Permitir unidades concedentes visualizarem inscritos dos processos seletivos',
                'boolean'
            );

            $empresaApenasDeferidos = $request->boolean('processos_empresa_apenas_deferidos');
            Configuracao::definir(
                'processos_empresa_apenas_deferidos',
                $empresaApenasDeferidos ? '1' : '0',
                'Restringir visualização de empresas apenas para inscritos deferidos',
                'boolean'
            );

            $empresaPodeExportar = $request->boolean('processos_empresa_pode_exportar');
            Configuracao::definir(
                'processos_empresa_pode_exportar',
                $empresaPodeExportar ? '1' : '0',
                'Permitir empresas exportarem relatórios de inscritos (PDF/Excel)',
                'boolean'
            );
        } elseif ($aba === 'estagio_limite') {
            $validated = $request->validate([
                'estagio_limite_empresa_modo' => 'required|in:anos,dias',
                'estagio_limite_empresa_anos' => 'required|integer|min:1|max:20',
                'estagio_limite_empresa_dias' => 'required|integer|min:1|max:10000',
            ]);

            Configuracao::definir(
                'estagio_limite_empresa_modo',
                $validated['estagio_limite_empresa_modo'],
                'Modo de cálculo do limite de permanência de estágio por empresa (anos ou dias)',
                'texto'
            );

            Configuracao::definir(
                'estagio_limite_empresa_anos',
                $validated['estagio_limite_empresa_anos'],
                'Limite de permanência de estágio por empresa em anos (quando modo = anos)',
                'numero'
            );

            Configuracao::definir(
                'estagio_limite_empresa_dias',
                $validated['estagio_limite_empresa_dias'],
                'Limite de permanência de estágio por empresa em dias (quando modo = dias)',
                'numero'
            );
        }

        // Se for aba de chamados, redirecionar pra ela com query param
        if ($aba === 'chamados') {
            return redirect()
                ->route('configuracoes.index', ['tab' => 'chamados'])
                ->with('success', 'Configurações atualizadas com sucesso!');
        }

        
        \Log::info('Todas as configurações foram salvas com sucesso para aba: ' . $aba);
        return redirect()->route('configuracoes.index', ['tab' => $aba])->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Exibe a página de configurações individuais por empresa
     */
    public function empresas(Request $request)
    {
        if (Auth::user()->nivel !== 'admin') {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $query = \App\Models\Empresa::query();

        // Filtro por nome
        if ($request->filled('nome')) {
            $query->where('nome_empresa', 'like', '%' . $request->input('nome') . '%');
        }

        // Filtro por email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        // Filtro por CNPJ
        if ($request->filled('cnpj')) {
            $query->where('numero_cnpj', 'like', '%' . $request->input('cnpj') . '%');
        }

        $empresas = $query->orderBy('nome_empresa')->paginate(15);

        return view('configuracoes.empresas', compact('empresas'));
    }

    /**
     * Exibe o formulário de configurações para uma empresa específica
     */
    public function editarEmpresa($idEmpresa)
    {
        if (Auth::user()->nivel !== 'admin') {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $empresa = \App\Models\Empresa::findOrFail($idEmpresa);
        
        // Obter configurações específicas desta empresa
        $configsEmpresa = \App\Models\EmpresaConfiguracao::obterTodasPorEmpresa($idEmpresa);
        
        // Obter configurações globais para referência
        $configsGlobais = Configuracao::orderBy('chave')->get();

        // Montar array de configurações com valores específicos e globais
        $configsProcessos = [];
        foreach (['processos_empresa_pode_ver_inscritos', 'processos_empresa_apenas_deferidos', 'processos_empresa_pode_exportar'] as $chave) {
            $configEmpresa = $configsEmpresa->where('chave', $chave)->first();
            $configGlobal = $configsGlobais->where('chave', $chave)->first();
            
            $configsProcessos[$chave] = [
                'empresa' => $configEmpresa?->valor !== null ? filter_var($configEmpresa->valor, FILTER_VALIDATE_BOOLEAN) : null,
                'global' => $configGlobal ? filter_var($configGlobal->valor, FILTER_VALIDATE_BOOLEAN) : false,
                'ativo' => $configEmpresa?->valor !== null ? filter_var($configEmpresa->valor, FILTER_VALIDATE_BOOLEAN) : filter_var($configGlobal?->valor, FILTER_VALIDATE_BOOLEAN),
            ];
        }

        return view('configuracoes.editar-empresa', compact('empresa', 'configsProcessos', 'configsGlobais'));
    }

    /**
     * Salva as configurações de uma empresa específica
     */
    public function atualizarEmpresa(Request $request, $idEmpresa)
    {
        if (Auth::user()->nivel !== 'admin') {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $empresa = \App\Models\Empresa::findOrFail($idEmpresa);

        $validated = $request->validate([
            'processos_empresa_pode_ver_inscritos' => 'nullable|in:global,sim,nao',
            'processos_empresa_apenas_deferidos' => 'nullable|in:global,sim,nao',
            'processos_empresa_pode_exportar' => 'nullable|in:global,sim,nao',
        ]);

        // Processar cada configuração
        $chavesConfig = [
            'processos_empresa_pode_ver_inscritos',
            'processos_empresa_apenas_deferidos',
            'processos_empresa_pode_exportar',
        ];

        foreach ($chavesConfig as $chave) {
            $valor = $validated[$chave] ?? 'global';

            if ($valor === 'global') {
                // Remove a configuração específica, volta a usar global
                \App\Models\EmpresaConfiguracao::removerPorEmpresa($idEmpresa, $chave);
            } else {
                // Define o valor específico
                $valorBooleano = $valor === 'sim' ? '1' : '0';
                \App\Models\EmpresaConfiguracao::definirPorEmpresa(
                    $idEmpresa,
                    $chave,
                    $valorBooleano,
                    null,
                    'boolean'
                );
            }
        }

        return redirect()->route('configuracoes.empresas')
            ->with('success', "Configurações da empresa '{$empresa->nome_empresa}' atualizadas com sucesso!");
    }
}
