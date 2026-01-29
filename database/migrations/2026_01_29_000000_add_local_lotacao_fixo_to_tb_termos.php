<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona campos fixos para local e lotação em tb_termos
     * para manter valores originais no PDF
     */
    public function up(): void
    {
        Schema::table('tb_termos', function (Blueprint $table) {
            // Campo fixo para local (para manter o local original do cadastro)
            $table->integer('fk_id_local_fixo')->nullable()->after('fk_id_local');
            
            // Campo fixo para lotação (para manter a lotação original do cadastro)
            $table->string('lotacao_fixo', 150)->nullable()->after('lotacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_termos', function (Blueprint $table) {
            $table->dropColumn(['fk_id_local_fixo', 'lotacao_fixo']);
        });
    }
};
