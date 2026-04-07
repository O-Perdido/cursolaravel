<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sigeconcursos_tb_inscricoes', function (Blueprint $table) {
            if (!Schema::hasColumn('sigeconcursos_tb_inscricoes', 'solicitou_nome_social')) {
                $table->boolean('solicitou_nome_social')->default(false)->after('modalidade_concorrencia');
            }

            if (!Schema::hasColumn('sigeconcursos_tb_inscricoes', 'nome_social')) {
                $table->string('nome_social', 255)->nullable()->after('solicitou_nome_social');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sigeconcursos_tb_inscricoes', function (Blueprint $table) {
            if (Schema::hasColumn('sigeconcursos_tb_inscricoes', 'nome_social')) {
                $table->dropColumn('nome_social');
            }

            if (Schema::hasColumn('sigeconcursos_tb_inscricoes', 'solicitou_nome_social')) {
                $table->dropColumn('solicitou_nome_social');
            }
        });
    }
};