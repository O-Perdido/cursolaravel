<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $table = 'tb_processos_seletivos';

    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->string('icone_processo')->nullable()->after('titulo');
            $table->json('vagas_por_nivel')->nullable()->after('cursos_destino');
            $table->json('fases')->nullable()->after('descricao_fases');
        });
    }

    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn(['icone_processo', 'vagas_por_nivel', 'fases']);
        });
    }
};
