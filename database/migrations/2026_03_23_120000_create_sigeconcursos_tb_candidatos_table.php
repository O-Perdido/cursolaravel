<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_candidatos', function (Blueprint $table) {
            $table->integer('id_candidato', true);
            $table->string('nome_completo');
            $table->string('numero_cpf', 11)->unique();
            $table->date('data_nascimento');
            $table->string('sexo', 20);
            $table->string('email')->unique();
            $table->string('numero_rg', 30);
            $table->string('orgao_expedidor_rg', 50);
            $table->string('uf_rg', 2);
            $table->string('nome_mae');
            $table->string('nacionalidade', 100);
            $table->string('naturalidade_cidade', 150);
            $table->string('naturalidade_estado', 150);
            $table->string('canhoto', 3);
            $table->string('numero_cep', 8);
            $table->string('endereco');
            $table->string('numero_endereco', 20);
            $table->string('complemento_endereco')->nullable();
            $table->string('bairro');
            $table->integer('fk_id_cidade');
            $table->string('numero_telefone', 11)->nullable();
            $table->string('numero_celular', 11);

            $table->foreign('fk_id_cidade')
                ->references('id_cidade')
                ->on('tb_cidade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_candidatos');
    }
};