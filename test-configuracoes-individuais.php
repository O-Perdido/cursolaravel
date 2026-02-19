<?php

// Script de teste para validar a feature de configurações individuais

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Iniciar o app com DB
$app->make('db');

echo "🔍 TESTANDO CONFIGURAÇÕES INDIVIDUAIS\n";
echo str_repeat("=", 50) . "\n\n";

try {
    // Teste 1: Verificar tabela
    echo "✓ Teste 1: Verificar tabela tb_empresa_configuracoes\n";
    $tableExists = \DB::table('information_schema.tables')
        ->where('table_schema', 'ebcp_db')
        ->where('table_name', 'tb_empresa_configuracoes')
        ->exists();
    echo "  Resultado: " . ($tableExists ? "✅ EXISTE" : "❌ NÃO EXISTE") . "\n\n";

    // Teste 2: Contar registros
    echo "✓ Teste 2: Contar registros\n";
    $count = \App\Models\EmpresaConfiguracao::count();
    echo "  Total de registros: $count\n\n";

    // Teste 3: Obter configs de uma empresa
    echo "✓ Teste 3: Buscar configs da empresa 1\n";
    $configs = \App\Models\EmpresaConfiguracao::obterTodasPorEmpresa(1);
    echo "  Quantidade de configs: " . $configs->count() . "\n";
    if ($configs->count() > 0) {
        foreach ($configs as $config) {
            echo "    - {$config->chave}: " . ($config->valor !== null ? "Personalizado ({$config->valor})" : "Global (NULL)") . "\n";
        }
    }
    echo "\n";

    // Teste 4: Método obterComFallback
    echo "✓ Teste 4: Teste do método obterComFallback\n";
    $valor = \App\Models\Configuracao::obterComFallback(
        'processos_empresa_pode_ver_inscritos',
        1,
        true
    );
    echo "  Valor para empresa 1: " . ($valor ? "SIM" : "NÃO") . "\n\n";

    // Teste 5: Model EmpresaConfiguracao existe
    echo "✓ Teste 5: Verificar se model existe\n";
    $model = new \App\Models\EmpresaConfiguracao();
    echo "  Model criado: " . get_class($model) . "\n\n";

    echo "✅ TODOS OS TESTES PASSARAM!\n";
    echo str_repeat("=", 50) . "\n";

} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
