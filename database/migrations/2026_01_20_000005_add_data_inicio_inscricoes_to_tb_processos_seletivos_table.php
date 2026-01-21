<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected string $table = 'tb_processos_seletivos';

    public function up(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->datetime('data_inicio_inscricoes')->nullable()->after('data_abertura');
            $table->index('data_inicio_inscricoes');
        });
    }

    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropIndex('tb_processos_seletivos_data_inicio_inscricoes_index');
            $table->dropColumn('data_inicio_inscricoes');
        });
    }
};
