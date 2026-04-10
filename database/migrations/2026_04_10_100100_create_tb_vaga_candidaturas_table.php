<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_vaga_candidaturas', function (Blueprint $table) {
            $table->integer('id_candidatura')->autoIncrement();
            $table->integer('fk_id_vaga');
            $table->integer('fk_id_estagiario');
            $table->string('status_candidatura', 30)->default('enviada');
            $table->string('curriculo_arquivo', 255);
            $table->text('observacoes_estagiario')->nullable();
            $table->text('observacoes_internas')->nullable();
            $table->dateTime('analisado_em')->nullable();
            $table->integer('fk_id_usuario_analisou')->nullable();
            $table->dateTime('notificado_em')->nullable();
            $table->timestamps();

            $table->unique(['fk_id_vaga', 'fk_id_estagiario'], 'tb_vaga_candidaturas_vaga_estagiario_unique');
            $table->index('status_candidatura', 'tb_vaga_candidaturas_status_idx');
            $table->foreign('fk_id_vaga')->references('id_vaga')->on('tb_vagas')->cascadeOnDelete();
            $table->foreign('fk_id_estagiario')->references('id_estagiario')->on('tb_estagiarios')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_vaga_candidaturas');
    }
};