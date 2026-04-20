<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->string('nome_social_estagiario', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->dropColumn('nome_social_estagiario');
        });
    }
};
