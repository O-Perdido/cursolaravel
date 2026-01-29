<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Copia dados existentes de fk_id_local e lotacao para os campos _fixo
     */
    public function up(): void
    {
        // Copiar fk_id_local para fk_id_local_fixo onde o campo fixo estiver vazio
        DB::statement('
            UPDATE tb_termos 
            SET fk_id_local_fixo = fk_id_local 
            WHERE fk_id_local_fixo IS NULL 
            AND fk_id_local IS NOT NULL
        ');

        // Copiar lotacao para lotacao_fixo onde o campo fixo estiver vazio
        DB::statement('
            UPDATE tb_termos 
            SET lotacao_fixo = lotacao 
            WHERE lotacao_fixo IS NULL 
            AND lotacao IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Limpar os campos fixos (reverter a cópia)
        DB::statement('UPDATE tb_termos SET fk_id_local_fixo = NULL');
        DB::statement('UPDATE tb_termos SET lotacao_fixo = NULL');
    }
};
