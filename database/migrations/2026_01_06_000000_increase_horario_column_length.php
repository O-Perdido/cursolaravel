<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->string('horario', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->string('horario', 50)->change();
        });
    }
};
