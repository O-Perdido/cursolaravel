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
        // Adicionar campos ZapSign na tabela tb_rescisao
        Schema::table('tb_rescisao', function (Blueprint $table) {
            $table->string('zapsign_doc_token')->nullable()->after('motivo');
            $table->string('zapsign_status')->nullable()->after('zapsign_doc_token');
            $table->timestamp('zapsign_enviado_em')->nullable()->after('zapsign_status');
        });

        // Adicionar campos ZapSign na tabela tb_alteracao_termo
        Schema::table('tb_alteracao_termo', function (Blueprint $table) {
            $table->string('zapsign_doc_token')->nullable()->after('old_desc_atividades');
            $table->string('zapsign_status')->nullable()->after('zapsign_doc_token');
            $table->timestamp('zapsign_enviado_em')->nullable()->after('zapsign_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_rescisao', function (Blueprint $table) {
            $table->dropColumn(['zapsign_doc_token', 'zapsign_status', 'zapsign_enviado_em']);
        });

        Schema::table('tb_alteracao_termo', function (Blueprint $table) {
            $table->dropColumn(['zapsign_doc_token', 'zapsign_status', 'zapsign_enviado_em']);
        });
    }
};
