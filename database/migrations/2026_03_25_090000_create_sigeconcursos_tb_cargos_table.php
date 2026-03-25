<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_cargos', function (Blueprint $table) {
            $table->integer('id_cargo', true);
            $table->string('nome_cargo');
            $table->string('descricao')->nullable();
            $table->string('escolaridade_minima', 120)->nullable();
            $table->boolean('ativo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_cargos');
    }
};