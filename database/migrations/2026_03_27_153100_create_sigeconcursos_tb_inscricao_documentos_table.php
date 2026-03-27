<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sigeconcursos_tb_inscricao_documentos')) {
            Schema::create('sigeconcursos_tb_inscricao_documentos', function (Blueprint $table) {
                $table->integer('id_inscricao_documento', true);
                $table->integer('fk_id_inscricao');
                $table->integer('fk_id_documento_exigido')->nullable();
                $table->string('titulo_documento');
                $table->string('caminho_arquivo');
                $table->text('observacoes')->nullable();
                $table->timestamps();

                $table->foreign('fk_id_inscricao', 'sc_insc_doc_inscricao_fk')
                    ->references('id_inscricao')
                    ->on('sigeconcursos_tb_inscricoes')
                    ->onDelete('cascade');

                $table->foreign('fk_id_documento_exigido', 'sc_insc_doc_docexig_fk')
                    ->references('id_documento_exigido')
                    ->on('sigeconcursos_tb_processo_documentos_exigidos')
                    ->onDelete('set null');

                $table->index(['fk_id_inscricao', 'fk_id_documento_exigido'], 'sc_inscricao_documento_vinculo_idx');
            });

            return;
        }

        if (!$this->foreignKeyExists('sigeconcursos_tb_inscricao_documentos', 'sc_insc_doc_docexig_fk')) {
            Schema::table('sigeconcursos_tb_inscricao_documentos', function (Blueprint $table) {
                $table->foreign('fk_id_documento_exigido', 'sc_insc_doc_docexig_fk')
                    ->references('id_documento_exigido')
                    ->on('sigeconcursos_tb_processo_documentos_exigidos')
                    ->onDelete('set null');
            });
        }

        if (!$this->indexExists('sigeconcursos_tb_inscricao_documentos', 'sc_inscricao_documento_vinculo_idx')) {
            Schema::table('sigeconcursos_tb_inscricao_documentos', function (Blueprint $table) {
                $table->index(['fk_id_inscricao', 'fk_id_documento_exigido'], 'sc_inscricao_documento_vinculo_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_inscricao_documentos');
    }

    private function foreignKeyExists(string $tableName, string $constraintName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $database)
            ->where('TABLE_NAME', $tableName)
            ->where('CONSTRAINT_NAME', $constraintName)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }

    private function indexExists(string $tableName, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $tableName)
            ->where('INDEX_NAME', $indexName)
            ->exists();
    }
};