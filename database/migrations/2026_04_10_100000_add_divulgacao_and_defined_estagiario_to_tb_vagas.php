<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->boolean('divulgada_publicamente')->default(false)->after('tem_estagiario_definido');
            $table->integer('fk_id_estagiario_definido')->nullable()->after('divulgada_publicamente');

            $table->index('divulgada_publicamente', 'tb_vagas_divulgada_publicamente_idx');
            $table->index('fk_id_estagiario_definido', 'tb_vagas_fk_id_estagiario_definido_idx');
        });
    }

    public function down(): void
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->dropIndex('tb_vagas_divulgada_publicamente_idx');
            $table->dropIndex('tb_vagas_fk_id_estagiario_definido_idx');
            $table->dropColumn(['divulgada_publicamente', 'fk_id_estagiario_definido']);
        });
    }
};