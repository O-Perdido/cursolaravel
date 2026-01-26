<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $table = 'tb_inscricoes_processo';

    public function up(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->string('numero_inscricao')->unique()->nullable()->after('id_inscricao');
        });
    }

    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn('numero_inscricao');
        });
    }
};
