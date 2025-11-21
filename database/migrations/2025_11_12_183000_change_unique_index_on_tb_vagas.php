<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            // Remove unique atual apenas em numero_vaga
            $table->dropUnique('tb_vagas_numero_vaga_unique');
            // Cria unique composto por empresa + numero_vaga
            $table->unique(['fk_id_empresa', 'numero_vaga'], 'tb_vagas_empresa_numero_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->dropUnique('tb_vagas_empresa_numero_unique');
            $table->unique('numero_vaga', 'tb_vagas_numero_vaga_unique');
        });
    }
};
