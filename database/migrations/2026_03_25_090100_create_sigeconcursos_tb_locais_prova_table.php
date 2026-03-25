<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_locais_prova', function (Blueprint $table) {
            $table->integer('id_local_prova', true);
            $table->string('nome_local');
            $table->string('numero_cep', 8);
            $table->string('endereco');
            $table->string('numero_endereco', 20);
            $table->string('complemento_endereco')->nullable();
            $table->string('bairro');
            $table->integer('fk_id_cidade');
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);

            $table->foreign('fk_id_cidade')
                ->references('id_cidade')
                ->on('tb_cidade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_locais_prova');
    }
};