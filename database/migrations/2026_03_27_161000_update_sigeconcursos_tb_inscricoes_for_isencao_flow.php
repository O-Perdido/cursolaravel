<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $temFkIdIsencao = Schema::hasColumn('sigeconcursos_tb_inscricoes', 'fk_id_isencao');
        $temJustificativaIsencao = Schema::hasColumn('sigeconcursos_tb_inscricoes', 'justificativa_isencao');
        $temParecerIsencao = Schema::hasColumn('sigeconcursos_tb_inscricoes', 'parecer_isencao');

        Schema::table('sigeconcursos_tb_inscricoes', function (Blueprint $table) use ($temFkIdIsencao, $temJustificativaIsencao, $temParecerIsencao) {
            if (!$temFkIdIsencao) {
                $table->integer('fk_id_isencao')->nullable()->after('solicitou_isencao');
            }

            if (!$temJustificativaIsencao) {
                $table->text('justificativa_isencao')->nullable()->after('fk_id_isencao');
            }

            if (!$temParecerIsencao) {
                $table->text('parecer_isencao')->nullable()->after('status_isencao');
            }
        });

        Schema::table('sigeconcursos_tb_inscricoes', function (Blueprint $table) {
            $table->foreign('fk_id_isencao', 'sc_insc_isencao_fk')
                ->references('id_isencao')
                ->on('sigeconcursos_tb_processo_isencoes')
                ->onDelete('set null');
        });

        Schema::table('sigeconcursos_tb_inscricoes', function (Blueprint $table) {
            $table->index(['fk_id_processo', 'status_isencao'], 'sc_inscricao_processo_isencao_idx');
        });
    }

    public function down(): void
    {
        Schema::table('sigeconcursos_tb_inscricoes', function (Blueprint $table) {
            $table->dropIndex('sc_inscricao_processo_isencao_idx');
            $table->dropForeign('sc_insc_isencao_fk');
            $table->dropColumn(['fk_id_isencao', 'justificativa_isencao', 'parecer_isencao']);
        });
    }
};