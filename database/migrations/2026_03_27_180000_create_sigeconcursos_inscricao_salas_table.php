<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sigeconcursos_tb_inscricao_salas')) {
            return;
        }

        Schema::create('sigeconcursos_tb_inscricao_salas', function (Blueprint $table) {
            $table->integer('id_inscricao_sala')->autoIncrement();
            $table->integer('fk_id_inscricao');
            $table->integer('fk_id_sala');
            $table->integer('numero_assento')->nullable();

            $table->unique('fk_id_inscricao', 'sc_inscr_sala_unique');

            $table->foreign('fk_id_inscricao', 'sc_inscr_sala_inscricao_fk')
                ->references('id_inscricao')
                ->on('sigeconcursos_tb_inscricoes')
                ->onDelete('cascade');

            $table->foreign('fk_id_sala', 'sc_inscr_sala_sala_fk')
                ->references('id_sala')
                ->on('sigeconcursos_tb_salas')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_inscricao_salas');
    }
};
