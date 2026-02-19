<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Só cria se não existir (pode já ter sido criada manualmente)
        if (!Schema::hasTable('tb_empresa_configuracoes')) {
            Schema::create('tb_empresa_configuracoes', function (Blueprint $table) {
                $table->id('id_empresa_configuracao');
                $table->integer('fk_id_empresa');
                $table->string('chave', 255); // Ex: 'processos_empresa_pode_ver_inscritos'
                $table->text('valor')->nullable();
                $table->string('descricao', 255)->nullable();
                $table->string('tipo', 50)->default('texto'); // texto, numero, decimal, boolean
                $table->timestamps();

                // Índices
                $table->foreign('fk_id_empresa')
                    ->references('id_empresa')
                    ->on('tb_empresas')
                    ->onDelete('cascade');
                
                // Garantir unicidade: uma empresa não pode ter 2 configs com mesma chave
                $table->unique(['fk_id_empresa', 'chave']);
                
                // Índice para buscas rápidas por empresa
                $table->index('fk_id_empresa');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_empresa_configuracoes');
    }
};
