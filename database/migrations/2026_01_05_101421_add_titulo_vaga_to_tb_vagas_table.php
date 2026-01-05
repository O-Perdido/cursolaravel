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
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->string('titulo_vaga', 150)->after('numero_vaga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->dropColumn('titulo_vaga');
        });
    }
};
