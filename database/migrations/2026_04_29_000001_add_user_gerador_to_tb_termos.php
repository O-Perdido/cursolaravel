<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_termos', function (Blueprint $table) {
            $table->unsignedBigInteger('fk_id_user_gerador')->nullable()->after('saldo_recesso')
                ->comment('Usuário que gerou/cadastrou o termo');
            $table->foreign('fk_id_user_gerador')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tb_termos', function (Blueprint $table) {
            $table->dropForeign(['fk_id_user_gerador']);
            $table->dropColumn('fk_id_user_gerador');
        });
    }
};
