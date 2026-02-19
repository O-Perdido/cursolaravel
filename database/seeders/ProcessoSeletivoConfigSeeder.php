<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracao;

class ProcessoSeletivoConfigSeeder extends Seeder
{
    /**
     * Seed das configurações do módulo de processos seletivos
     */
    public function run(): void
    {
        // Configuração: Empresas podem visualizar inscritos
        Configuracao::updateOrCreate(
            ['chave' => 'processos_empresa_pode_ver_inscritos'],
            [
                'valor' => '1',
                'descricao' => 'Permitir unidades concedentes visualizarem inscritos dos processos seletivos',
                'tipo' => 'boolean',
            ]
        );

        // Configuração: Empresas veem apenas deferidos
        Configuracao::updateOrCreate(
            ['chave' => 'processos_empresa_apenas_deferidos'],
            [
                'valor' => '0',
                'descricao' => 'Restringir visualização de empresas apenas para inscritos deferidos',
                'tipo' => 'boolean',
            ]
        );

        // Configuração: Empresas podem exportar relatórios
        Configuracao::updateOrCreate(
            ['chave' => 'processos_empresa_pode_exportar'],
            [
                'valor' => '1',
                'descricao' => 'Permitir empresas exportarem relatórios de inscritos (PDF/Excel)',
                'tipo' => 'boolean',
            ]
        );

        $this->command->info('✅ Configurações de Processos Seletivos criadas/atualizadas com sucesso!');
    }
}
