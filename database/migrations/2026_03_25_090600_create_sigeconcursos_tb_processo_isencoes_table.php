<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_processo_isencoes', function (Blueprint $table) {
            $table->integer('id_isencao', true);
            $table->integer('fk_id_processo');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->dateTime('data_inicio')->nullable();
            $table->dateTime('data_fim')->nullable();
            $table->boolean('exige_comprovacao')->default(false);

            $table->foreign('fk_id_processo')
                ->references('id_processo')
                ->on('sigeconcursos_tb_processos')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_processo_isencoes');
    }
};