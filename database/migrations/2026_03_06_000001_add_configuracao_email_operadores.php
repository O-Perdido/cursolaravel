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
        // Criar tabela de configurações globais do sistema
        if (!Schema::hasTable('configuracoes')) {
            Schema::create('configuracoes', function (Blueprint $table) {
                $table->id('id_configuracao');
                $table->string('chave', 100)->unique();
                $table->text('valor')->nullable();
                $table->string('descricao', 255)->nullable();
                $table->enum('tipo', ['texto', 'numero', 'decimal', 'boolean'])->default('texto');
                $table->timestamps();
            });
        }

        // Inserir configuração para notificação de e-mail operadores
        DB::table('configuracoes')->insertOrIgnore([
            [
                'chave' => 'chamados_notificar_operadores_email',
                'valor' => 'true',
                'descricao' => 'Habilitar notificações por e-mail para operadores quando empresa responde chamados',
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
        // Remove a configuração específica
        DB::table('configuracoes')
            ->where('chave', 'chamados_notificar_operadores_email')
            ->delete();

        // Não remove a tabela inteira pois pode haver outras configs
        // Schema::dropIfExists('configuracoes');
    }
};
