<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $temEtapaFluxoAtual = Schema::hasColumn('sigeconcursos_tb_processos', 'etapa_fluxo_atual');
        $temPermiteCondicaoEspecial = Schema::hasColumn('sigeconcursos_tb_processos', 'permite_condicao_especial');
        $temExigeDocumentoCondicaoEspecial = Schema::hasColumn('sigeconcursos_tb_processos', 'exige_documento_condicao_especial');
        $temPermiteEscolhaLocalProva = Schema::hasColumn('sigeconcursos_tb_processos', 'permite_escolha_local_prova');

        Schema::table('sigeconcursos_tb_processos', function (Blueprint $table) use ($temEtapaFluxoAtual, $temPermiteCondicaoEspecial, $temExigeDocumentoCondicaoEspecial, $temPermiteEscolhaLocalProva) {
            if (!$temEtapaFluxoAtual) {
                $table->string('etapa_fluxo_atual', 50)->default('cadastro')->after('status');
            }

            if (!$temPermiteCondicaoEspecial) {
                $table->boolean('permite_condicao_especial')->default(true)->after('exige_aceite_edital');
            }

            if (!$temExigeDocumentoCondicaoEspecial) {
                $table->boolean('exige_documento_condicao_especial')->default(true)->after('permite_condicao_especial');
            }

            if ($temPermiteEscolhaLocalProva) {
                $table->dropColumn('permite_escolha_local_prova');
            }
        });

        DB::table('sigeconcursos_tb_processos')
            ->whereIn('status', ['inscricoes_abertas', 'inscricoes_encerradas'])
            ->update(['etapa_fluxo_atual' => 'inscricoes']);

        DB::table('sigeconcursos_tb_processos')
            ->where('status', 'em_andamento')
            ->update(['etapa_fluxo_atual' => 'homologacao_inscricoes']);

        DB::table('sigeconcursos_tb_processos')
            ->where('status', 'finalizado')
            ->update(['etapa_fluxo_atual' => 'etapas_finais']);

        if (!Schema::hasTable('sigeconcursos_tb_processo_documentos_exigidos')) {
            Schema::create('sigeconcursos_tb_processo_documentos_exigidos', function (Blueprint $table) {
                $table->integer('id_documento_exigido', true);
                $table->integer('fk_id_processo');
                $table->string('titulo');
                $table->text('descricao')->nullable();
                $table->boolean('obrigatorio')->default(true);
                $table->integer('ordem_exibicao')->nullable();

                $table->foreign('fk_id_processo')
                    ->references('id_processo')
                    ->on('sigeconcursos_tb_processos')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sigeconcursos_tb_processo_documentos_exigidos')) {
            Schema::dropIfExists('sigeconcursos_tb_processo_documentos_exigidos');
        }

        $temPermiteEscolhaLocalProva = Schema::hasColumn('sigeconcursos_tb_processos', 'permite_escolha_local_prova');
        $temEtapaFluxoAtual = Schema::hasColumn('sigeconcursos_tb_processos', 'etapa_fluxo_atual');
        $temPermiteCondicaoEspecial = Schema::hasColumn('sigeconcursos_tb_processos', 'permite_condicao_especial');
        $temExigeDocumentoCondicaoEspecial = Schema::hasColumn('sigeconcursos_tb_processos', 'exige_documento_condicao_especial');

        Schema::table('sigeconcursos_tb_processos', function (Blueprint $table) use ($temPermiteEscolhaLocalProva, $temEtapaFluxoAtual, $temPermiteCondicaoEspecial, $temExigeDocumentoCondicaoEspecial) {
            if (!$temPermiteEscolhaLocalProva) {
                $table->boolean('permite_escolha_local_prova')->default(false)->after('exige_aceite_edital');
            }

            $colunasParaRemover = [];

            if ($temEtapaFluxoAtual) {
                $colunasParaRemover[] = 'etapa_fluxo_atual';
            }

            if ($temPermiteCondicaoEspecial) {
                $colunasParaRemover[] = 'permite_condicao_especial';
            }

            if ($temExigeDocumentoCondicaoEspecial) {
                $colunasParaRemover[] = 'exige_documento_condicao_especial';
            }

            if (!empty($colunasParaRemover)) {
                $table->dropColumn($colunasParaRemover);
            }
        });
    }
};