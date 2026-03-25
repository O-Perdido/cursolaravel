<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_processo_arquivos', function (Blueprint $table) {
            $table->integer('id_arquivo', true);
            $table->integer('fk_id_processo');
            $table->string('nome_exibicao');
            $table->string('tipo_arquivo', 50)->default('outro');
            $table->string('caminho_arquivo');
            $table->integer('ordem_exibicao')->nullable();

            $table->foreign('fk_id_processo')
                ->references('id_processo')
                ->on('sigeconcursos_tb_processos')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_processo_arquivos');
    }
};