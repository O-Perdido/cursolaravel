<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_financeiro_contas', function (Blueprint $table) {
            $table->id('id_financeiro_conta');
            $table->enum('tipo_conta', ['receita', 'despesa']);
            $table->string('nome_conta', 150)->unique();
            $table->boolean('ativo')->default(true);
            $table->integer('ordem_exibicao')->default(99);
            $table->timestamps();

            $table->index('tipo_conta');
            $table->index('ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_financeiro_contas');
    }
};