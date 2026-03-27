<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_inscricao_isencao_documentos', function (Blueprint $table) {
            $table->integer('id_inscricao_isencao_documento', true);
            $table->integer('fk_id_inscricao');
            $table->string('nome_documento');
            $table->string('caminho_arquivo');
            $table->timestamps();

            $table->foreign('fk_id_inscricao', 'sc_insc_isdoc_inscricao_fk')
                ->references('id_inscricao')
                ->on('sigeconcursos_tb_inscricoes')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_inscricao_isencao_documentos');
    }
};