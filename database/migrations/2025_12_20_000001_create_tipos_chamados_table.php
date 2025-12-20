<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_tipos_chamados', function (Blueprint $table) {
            $table->id('id_tipo_chamado');
            $table->string('nome', 100);
            $table->string('slug', 100)->unique(); // rescisao, alteracao, outros, etc
            $table->text('descricao')->nullable();
            $table->boolean('sistema')->default(false); // true para Rescisão e Alteração (não podem ser deletados)
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0); // para ordenar na exibição
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_tipos_chamados');
    }
};
