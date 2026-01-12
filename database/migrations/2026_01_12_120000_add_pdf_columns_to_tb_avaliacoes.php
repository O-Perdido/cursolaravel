<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tb_avaliacoes', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('questoes_respostas');
            $table->timestamp('pdf_gerado_em')->nullable()->after('pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('tb_avaliacoes', function (Blueprint $table) {
            $table->dropColumn(['pdf_path', 'pdf_gerado_em']);
        });
    }
};
