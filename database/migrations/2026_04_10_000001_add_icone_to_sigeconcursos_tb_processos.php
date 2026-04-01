<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sigeconcursos_tb_processos', 'icone_processo')) {
            Schema::table('sigeconcursos_tb_processos', function (Blueprint $table) {
                $table->string('icone_processo')->nullable()->after('titulo');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sigeconcursos_tb_processos', 'icone_processo')) {
            Schema::table('sigeconcursos_tb_processos', function (Blueprint $table) {
                $table->dropColumn('icone_processo');
            });
        }
    }
};
