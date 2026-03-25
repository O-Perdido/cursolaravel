<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_salas', function (Blueprint $table) {
            $table->integer('id_sala', true);
            $table->integer('fk_id_local_prova');
            $table->string('nome_sala', 120);
            $table->string('bloco', 120)->nullable();
            $table->integer('capacidade_maxima');
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);

            $table->foreign('fk_id_local_prova')
                ->references('id_local_prova')
                ->on('sigeconcursos_tb_locais_prova')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_salas');
    }
};