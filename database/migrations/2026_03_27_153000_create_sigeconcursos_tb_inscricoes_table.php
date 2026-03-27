<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_inscricoes', function (Blueprint $table) {
            $table->integer('id_inscricao', true);
            $table->integer('fk_id_processo');
            $table->integer('fk_id_candidato');
            $table->string('numero_inscricao', 50)->nullable()->unique();
            $table->string('modalidade_concorrencia', 30);
            $table->string('status_inscricao', 20)->default('inscrito');
            $table->boolean('aceite_edital')->default(false);
            $table->boolean('solicitou_condicao_especial')->default(false);
            $table->text('descricao_condicao_especial')->nullable();
            $table->string('caminho_documento_condicao_especial')->nullable();
            $table->boolean('solicitou_isencao')->default(false);
            $table->string('status_isencao', 20)->default('nao_solicitada');
            $table->decimal('valor_taxa_aplicada', 10, 2)->nullable();
            $table->string('status_pagamento', 20)->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->foreign('fk_id_processo')
                ->references('id_processo')
                ->on('sigeconcursos_tb_processos')
                ->onDelete('cascade');

            $table->foreign('fk_id_candidato')
                ->references('id_candidato')
                ->on('sigeconcursos_tb_candidatos')
                ->onDelete('cascade');

            $table->unique(['fk_id_processo', 'fk_id_candidato'], 'sc_inscricao_processo_candidato_unique');
            $table->index(['fk_id_processo', 'status_inscricao'], 'sc_inscricao_processo_status_idx');
            $table->index(['fk_id_candidato', 'created_at'], 'sc_inscricao_candidato_data_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_inscricoes');
    }
};