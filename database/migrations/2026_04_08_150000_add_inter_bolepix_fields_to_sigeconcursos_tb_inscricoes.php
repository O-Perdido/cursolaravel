<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sigeconcursos_tb_inscricoes', function (Blueprint $table) {
            if (!Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_codigo_solicitacao')) {
                $table->string('inter_codigo_solicitacao', 60)->nullable()->after('status_pagamento');
                $table->index('inter_codigo_solicitacao', 'sc_inscricao_inter_codigo_solicitacao_idx');
            }

            if (!Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_seu_numero')) {
                $table->string('inter_seu_numero', 15)->nullable()->after('inter_codigo_solicitacao');
            }

            if (!Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_nosso_numero')) {
                $table->string('inter_nosso_numero', 30)->nullable()->after('inter_seu_numero');
            }

            if (!Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_situacao')) {
                $table->string('inter_situacao', 30)->nullable()->after('inter_nosso_numero');
            }

            if (!Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_linha_digitavel')) {
                $table->string('inter_linha_digitavel', 120)->nullable()->after('inter_situacao');
            }

            if (!Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_codigo_barras')) {
                $table->string('inter_codigo_barras', 120)->nullable()->after('inter_linha_digitavel');
            }

            if (!Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_pix_copia_cola')) {
                $table->text('inter_pix_copia_cola')->nullable()->after('inter_codigo_barras');
            }

            if (!Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_data_vencimento')) {
                $table->date('inter_data_vencimento')->nullable()->after('inter_pix_copia_cola');
            }

            if (!Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_ultima_sincronizacao_em')) {
                $table->dateTime('inter_ultima_sincronizacao_em')->nullable()->after('inter_data_vencimento');
            }

            if (!Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_payload_cobranca')) {
                $table->longText('inter_payload_cobranca')->nullable()->after('inter_ultima_sincronizacao_em');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sigeconcursos_tb_inscricoes', function (Blueprint $table) {
            if (Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_payload_cobranca')) {
                $table->dropColumn('inter_payload_cobranca');
            }

            if (Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_ultima_sincronizacao_em')) {
                $table->dropColumn('inter_ultima_sincronizacao_em');
            }

            if (Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_data_vencimento')) {
                $table->dropColumn('inter_data_vencimento');
            }

            if (Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_pix_copia_cola')) {
                $table->dropColumn('inter_pix_copia_cola');
            }

            if (Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_codigo_barras')) {
                $table->dropColumn('inter_codigo_barras');
            }

            if (Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_linha_digitavel')) {
                $table->dropColumn('inter_linha_digitavel');
            }

            if (Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_situacao')) {
                $table->dropColumn('inter_situacao');
            }

            if (Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_nosso_numero')) {
                $table->dropColumn('inter_nosso_numero');
            }

            if (Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_seu_numero')) {
                $table->dropColumn('inter_seu_numero');
            }

            if (Schema::hasColumn('sigeconcursos_tb_inscricoes', 'inter_codigo_solicitacao')) {
                $table->dropIndex('sc_inscricao_inter_codigo_solicitacao_idx');
                $table->dropColumn('inter_codigo_solicitacao');
            }
        });
    }
};
