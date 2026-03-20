<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_empresas', function (Blueprint $table) {
            $table->integer('id_empresa', true);
            $table->string('nome_razao_social');
            $table->string('numero_cnpj', 14)->unique();
            $table->string('numero_telefone', 11)->nullable();
            $table->string('numero_celular', 11)->nullable();
            $table->string('email');
            $table->string('numero_cep', 8);
            $table->string('endereco');
            $table->string('numero_endereco', 20);
            $table->string('complemento_endereco')->nullable();
            $table->string('bairro');
            $table->integer('fk_id_cidade');
            $table->string('nome_representante');
            $table->string('cargo_representante');
            $table->string('cpf_representante', 11);
            $table->text('dados_bancarios')->nullable();

            $table->foreign('fk_id_cidade')
                ->references('id_cidade')
                ->on('tb_cidade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_empresas');
    }
};