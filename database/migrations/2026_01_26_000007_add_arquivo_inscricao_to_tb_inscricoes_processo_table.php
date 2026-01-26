<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $table = 'tb_inscricoes_processo';

    public function up(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->string('arquivo_inscricao')->nullable()->after('observacoes');
        });
    }

    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn('arquivo_inscricao');
        });
    }
};
