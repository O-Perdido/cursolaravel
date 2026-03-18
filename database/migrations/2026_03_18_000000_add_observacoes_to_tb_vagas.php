<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->text('observacoes')->nullable()->after('atividades');
        });
    }

    public function down()
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->dropColumn('observacoes');
        });
    }
};