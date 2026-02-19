<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations - Popula configurações iniciais para empresas existentes
     * Isso garante que cada empresa já tenha referência às mesmas configurações globais
     */
    public function up(): void
    {
        // Chaves de configuração para processos seletivos
        $chavesProcessosConfig = [
            'processos_empresa_pode_ver_inscritos',
            'processos_empresa_apenas_deferidos',
            'processos_empresa_pode_exportar',
        ];

        // Obter todas as empresas
        $empresas = DB::table('tb_empresas')->select('id_empresa')->get();

        // Para cada empresa, criar entradas vazias na tabela de configurações
        // (NULL significa "usar valor global")
        foreach ($empresas as $empresa) {
            foreach ($chavesProcessosConfig as $chave) {
                DB::table('tb_empresa_configuracoes')->insertOrIgnore([
                    'fk_id_empresa' => $empresa->id_empresa,
                    'chave' => $chave,
                    'valor' => null, // NULL = usa valor global
                    'descricao' => "Override do valor global para esta empresa",
                    'tipo' => 'boolean',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não deletamos os registros, apenas limpamos se necessário
        // ou deixamos como está. Aqui deixamos intacto.
    }
};
