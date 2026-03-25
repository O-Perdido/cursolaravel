<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_processos', function (Blueprint $table) {
            $table->integer('id_processo', true);
            $table->string('numero_processo', 50)->nullable()->unique();
            $table->string('numero_edital', 100);
            $table->string('titulo');
            $table->string('tipo_processo', 30);
            $table->integer('fk_id_empresa');
            $table->string('status', 30);
            $table->text('resumo')->nullable();
            $table->longText('descricao')->nullable();
            $table->text('requisitos_gerais')->nullable();
            $table->text('observacoes')->nullable();
            $table->dateTime('data_publicacao')->nullable();
            $table->dateTime('data_inicio_inscricoes')->nullable();
            $table->dateTime('data_fim_inscricoes')->nullable();
            $table->dateTime('data_prova')->nullable();
            $table->dateTime('data_resultado_final')->nullable();
            $table->json('fases')->nullable();
            $table->boolean('exige_aceite_edital')->default(true);
            $table->boolean('permite_escolha_local_prova')->default(false);
            $table->boolean('possui_taxa_inscricao')->default(false);
            $table->decimal('valor_taxa_padrao', 10, 2)->nullable();
            $table->boolean('permite_ampla_concorrencia')->default(true);
            $table->boolean('permite_pcd')->default(true);

            $table->foreign('fk_id_empresa')
                ->references('id_empresa')
                ->on('sigeconcursos_tb_empresas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_processos');
    }
};