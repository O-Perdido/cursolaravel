<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sigeconcursos_tb_inscricao_locais')) {
            return;
        }

        Schema::create('sigeconcursos_tb_inscricao_locais', function (Blueprint $table) {
            $table->integer('id_inscricao_local')->autoIncrement();
            $table->integer('fk_id_inscricao');
            $table->integer('fk_id_processo_local');

            $table->unique('fk_id_inscricao', 'sc_inscr_local_unique');

            $table->foreign('fk_id_inscricao', 'sc_inscr_local_inscricao_fk')
                ->references('id_inscricao')
                ->on('sigeconcursos_tb_inscricoes')
                ->onDelete('cascade');

            $table->foreign('fk_id_processo_local', 'sc_inscr_local_proclocal_fk')
                ->references('id_processo_local')
                ->on('sigeconcursos_tb_processo_locais')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_inscricao_locais');
    }
};
