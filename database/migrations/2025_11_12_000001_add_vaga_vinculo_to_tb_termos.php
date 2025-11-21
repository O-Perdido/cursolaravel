<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tb_termos', function (Blueprint $table) {
            $table->integer('fk_id_vaga')->nullable()->after('fk_id_supervisor_fixo');
            $table->enum('vinculo', ['vinculado', 'nao_vinculado'])->default('nao_vinculado')->after('fk_id_vaga');
            $table->foreign('fk_id_vaga')->references('id_vaga')->on('tb_vagas')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('tb_termos', function (Blueprint $table) {
            $table->dropForeign(['fk_id_vaga']);
            $table->dropColumn('fk_id_vaga');
            $table->dropColumn('vinculo');
        });
    }
};
