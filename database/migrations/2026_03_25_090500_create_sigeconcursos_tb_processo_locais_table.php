<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_processo_locais', function (Blueprint $table) {
            $table->integer('id_processo_local', true);
            $table->integer('fk_id_processo');
            $table->integer('fk_id_local_prova');
            $table->text('observacoes')->nullable();

            $table->foreign('fk_id_processo')
                ->references('id_processo')
                ->on('sigeconcursos_tb_processos')
                ->onDelete('cascade');

            $table->foreign('fk_id_local_prova')
                ->references('id_local_prova')
                ->on('sigeconcursos_tb_locais_prova');

            $table->unique(['fk_id_processo', 'fk_id_local_prova'], 'sc_processo_local_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_processo_locais');
    }
};