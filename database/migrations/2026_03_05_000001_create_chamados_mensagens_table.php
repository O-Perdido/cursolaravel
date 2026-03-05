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
        Schema::create('tb_chamados_mensagens', function (Blueprint $table) {
            $table->id('id_chamado_mensagem');

            $table->foreignId('fk_id_chamado')
                ->constrained('tb_chamados', 'id_chamado')
                ->onDelete('cascade');

            $table->foreignId('fk_id_user_remetente')->nullable()
                ->constrained('users', 'id')
                ->onDelete('set null');

            $table->enum('remetente_nivel', ['empresa', 'operador']);
            $table->text('mensagem');
            $table->timestamp('lido_empresa_em')->nullable();
            $table->timestamp('lido_operador_em')->nullable();
            $table->timestamps();

            $table->index('fk_id_chamado');
            $table->index('remetente_nivel');
            $table->index('lido_empresa_em');
            $table->index('lido_operador_em');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_chamados_mensagens');
    }
};
