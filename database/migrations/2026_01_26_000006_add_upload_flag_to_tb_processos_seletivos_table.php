<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $table = 'tb_processos_seletivos';

    public function up(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->boolean('solicitar_upload_inscricao')->default(false)->after('aviso_inscricao');
        });
    }

    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn('solicitar_upload_inscricao');
        });
    }
};
