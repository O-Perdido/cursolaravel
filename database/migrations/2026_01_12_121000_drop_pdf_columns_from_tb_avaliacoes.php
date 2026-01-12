<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tb_avaliacoes', function (Blueprint $table) {
            if (Schema::hasColumn('tb_avaliacoes', 'pdf_path')) {
                $table->dropColumn('pdf_path');
            }
            if (Schema::hasColumn('tb_avaliacoes', 'pdf_gerado_em')) {
                $table->dropColumn('pdf_gerado_em');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_avaliacoes', function (Blueprint $table) {
            $table->string('pdf_path')->nullable();
            $table->timestamp('pdf_gerado_em')->nullable();
        });
    }
};
