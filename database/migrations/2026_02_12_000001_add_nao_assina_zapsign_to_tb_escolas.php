<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_escolas', function (Blueprint $table) {
            $table->boolean('nao_assina_zapsign')
                ->default(false)
                ->after('nome_seguradora');
        });
    }

    public function down(): void
    {
        Schema::table('tb_escolas', function (Blueprint $table) {
            $table->dropColumn('nao_assina_zapsign');
        });
    }
};
