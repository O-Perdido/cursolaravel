<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->string('nome_estagiario', 150)->nullable()->after('fk_id_supervisor');
            $table->string('contato_whatsapp', 20)->nullable()->after('nome_estagiario');
            $table->string('contato_email', 100)->nullable()->after('contato_whatsapp');
            $table->boolean('tem_estagiario_definido')->default(false)->after('contato_email');
        });
    }

    public function down()
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            $table->dropColumn('nome_estagiario');
            $table->dropColumn('contato_whatsapp');
            $table->dropColumn('contato_email');
            $table->dropColumn('tem_estagiario_definido');
        });
    }
};
