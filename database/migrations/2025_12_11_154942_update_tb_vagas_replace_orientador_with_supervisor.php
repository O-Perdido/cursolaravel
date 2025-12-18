<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adicionar coluna e FK somente se não existirem
        if (!Schema::hasColumn('tb_vagas', 'fk_id_supervisor')) {
            Schema::table('tb_vagas', function (Blueprint $table) {
                $table->unsignedInteger('fk_id_supervisor')->nullable()->after('fk_id_local');
            });
        }
        // Tentar criar a FK se a coluna existir e a constraint não existir
        try {
            Schema::table('tb_vagas', function (Blueprint $table) {
                $table->foreign('fk_id_supervisor', 'tb_vagas_fk_id_supervisor_foreign')
                      ->references('id_supervisor')
                      ->on('tb_supervisores')
                      ->onDelete('restrict');
            });
        } catch (\Throwable $e) {
            // Ignorar se já existir
        }
        
        // Remover colunas nome_orientador e cargo_orientador
        Schema::table('tb_vagas', function (Blueprint $table) {
            if (Schema::hasColumn('tb_vagas', 'nome_orientador')) {
                $table->dropColumn('nome_orientador');
            }
            if (Schema::hasColumn('tb_vagas', 'cargo_orientador')) {
                $table->dropColumn('cargo_orientador');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_vagas', function (Blueprint $table) {
            // Adicionar colunas de volta
            $table->string('nome_orientador')->nullable();
            $table->string('cargo_orientador')->nullable();
        });
        
        Schema::table('tb_vagas', function (Blueprint $table) {
            // Remover foreign key e coluna
            $table->dropForeign('tb_vagas_fk_id_supervisor_foreign');
            $table->dropColumn('fk_id_supervisor');
        });
    }
};
