<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_financeiro_lancamentos', function (Blueprint $table) {
            $table->id('id_financeiro_lancamento');

            $table->foreignId('fk_id_financeiro_conta')
                ->constrained('tb_financeiro_contas', 'id_financeiro_conta')
                ->onDelete('restrict');

            $table->foreignId('fk_id_usuario_criacao')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();

            $table->foreignId('fk_id_usuario_atualizacao')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();

            $table->integer('ano_referencia');
            $table->tinyInteger('mes_referencia');
            $table->decimal('valor', 12, 2);
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['ano_referencia', 'mes_referencia']);
            $table->index('fk_id_financeiro_conta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_financeiro_lancamentos');
    }
};