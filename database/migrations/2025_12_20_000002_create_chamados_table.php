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
        Schema::create('tb_chamados', function (Blueprint $table) {
            $table->id('id_chamado');
            $table->string('protocolo', 20)->unique(); // Gerado automaticamente
            
            // Relacionamentos
            $table->foreignId('fk_id_tipo_chamado')
                ->constrained('tb_tipos_chamados', 'id_tipo_chamado')
                ->onDelete('restrict');
            
            $table->integer('fk_id_empresa');
            $table->foreign('fk_id_empresa')
                ->references('id_empresa')
                ->on('tb_empresas')
                ->onDelete('cascade');
            
            $table->foreignId('fk_id_user_solicitante')
                ->constrained('users', 'id')
                ->onDelete('cascade');
            
            // Para chamados de Rescisão e Alteração
            $table->integer('fk_id_termo')->nullable();
            $table->foreign('fk_id_termo')
                ->references('id_termo')
                ->on('tb_termos')
                ->onDelete('set null');
            
            // Campos específicos para Rescisão
            $table->date('data_rescisao')->nullable();
            $table->text('motivo_rescisao')->nullable();
            
            // Campos específicos para Alteração
            $table->text('descricao_alteracao')->nullable();
            
            // Campos para chamados gerais (Outros e cadastrados)
            $table->string('titulo', 200)->nullable();
            $table->text('detalhes')->nullable();
            $table->json('anexos')->nullable(); // Array de caminhos dos arquivos
            
            // Status e controle
            $table->enum('status', ['pendente', 'em_analise', 'em_andamento', 'concluido', 'cancelado'])
                ->default('pendente');
            $table->text('observacoes_internas')->nullable(); // Para operadores/admin
            $table->foreignId('fk_id_user_responsavel')->nullable()
                ->constrained('users', 'id')
                ->onDelete('set null');
            $table->timestamp('data_conclusao')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para performance
            $table->index('protocolo');
            $table->index('status');
            $table->index('fk_id_empresa');
            $table->index('fk_id_tipo_chamado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_chamados');
    }
};
