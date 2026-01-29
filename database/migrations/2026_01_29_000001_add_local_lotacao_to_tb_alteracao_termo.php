<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona campos para alteração de local e lotação em tb_alteracao_termo
     */
    public function up(): void
    {
        Schema::table('tb_alteracao_termo', function (Blueprint $table) {
            // Novos valores de local e lotação
            $table->integer('fk_id_local')->nullable()->after('auxilio_transporte_alteracao');
            $table->string('lotacao_alteracao', 150)->nullable()->after('fk_id_local');
            
            // Valores antigos (para histórico e rollback)
            $table->integer('old_fk_id_local')->nullable()->after('old_desc_atividades');
            $table->string('old_lotacao', 150)->nullable()->after('old_fk_id_local');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_alteracao_termo', function (Blueprint $table) {
            $table->dropColumn(['fk_id_local', 'lotacao_alteracao', 'old_fk_id_local', 'old_lotacao']);
        });
    }
};
