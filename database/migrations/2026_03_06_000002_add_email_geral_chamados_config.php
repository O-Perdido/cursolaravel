<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adicionar configurações para email geral de notificações
        DB::table('configuracoes')->insertOrIgnore([
            [
                'chave' => 'chamados_email_geral',
                'valor' => 'contato@ebcpconsultoria.com.br',
                'descricao' => 'Email geral que recebe cópia das notificações de chamados',
                'tipo' => 'texto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chave' => 'chamados_incluir_email_geral_quando_responsavel',
                'valor' => 'false',
                'descricao' => 'Se true, inclui email geral nas notificações mesmo quando há responsável definido',
                'tipo' => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('configuracoes')
            ->whereIn('chave', [
                'chamados_email_geral',
                'chamados_incluir_email_geral_quando_responsavel',
            ])
            ->delete();
    }
};
