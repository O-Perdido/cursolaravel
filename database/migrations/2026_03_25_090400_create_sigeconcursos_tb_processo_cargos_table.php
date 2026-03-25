<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_processo_cargos', function (Blueprint $table) {
            $table->integer('id_processo_cargo', true);
            $table->integer('fk_id_processo');
            $table->integer('fk_id_cargo');
            $table->integer('quantidade_vagas')->nullable();
            $table->integer('quantidade_cadastro_reserva')->nullable();
            $table->decimal('valor_remuneracao', 10, 2)->nullable();
            $table->decimal('valor_taxa_inscricao', 10, 2)->nullable();
            $table->string('carga_horaria', 100)->nullable();
            $table->text('requisitos_especificos')->nullable();

            $table->foreign('fk_id_processo')
                ->references('id_processo')
                ->on('sigeconcursos_tb_processos')
                ->onDelete('cascade');

            $table->foreign('fk_id_cargo')
                ->references('id_cargo')
                ->on('sigeconcursos_tb_cargos');

            $table->unique(['fk_id_processo', 'fk_id_cargo'], 'sc_processo_cargo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_processo_cargos');
    }
};