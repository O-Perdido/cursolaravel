<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users') || Schema::hasColumn('users', 'fk_id_candidato')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->integer('fk_id_candidato')->nullable();
            $table->foreign('fk_id_candidato')
                ->references('id_candidato')
                ->on('sigeconcursos_tb_candidatos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'fk_id_candidato')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['fk_id_candidato']);
            $table->dropColumn('fk_id_candidato');
        });
    }
};