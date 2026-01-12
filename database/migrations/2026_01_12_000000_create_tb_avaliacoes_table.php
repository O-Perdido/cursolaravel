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
        Schema::create('tb_avaliacoes', function (Blueprint $table) {
            $table->increments('id_avaliacao');
            
            $table->integer('fk_id_termo');
            $table->integer('fk_id_supervisor')->nullable();
            
            $table->enum('tipo_avaliacao', ['seis_meses', 'finalizacao'])->default('seis_meses');
            $table->enum('status', ['pendente', 'respondida', 'revisada'])->default('pendente');
            
            $table->string('token_compartilhamento')->nullable()->unique();
            
            $table->longText('questoes_respostas')->nullable(); // JSON
            
            $table->dateTime('respondida_em')->nullable();
            $table->string('respondida_por')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign Keys
            $table->foreign('fk_id_termo')
                ->references('id_termo')
                ->on('tb_termos')
                ->onDelete('cascade');
                
            $table->foreign('fk_id_supervisor')
                ->references('id_supervisor')
                ->on('tb_supervisores')
                ->onDelete('set null');
            
            // Índices para performance
            $table->index('fk_id_termo');
            $table->index('fk_id_supervisor');
            $table->index('status');
            $table->index('token_compartilhamento');
            $table->index('tipo_avaliacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_avaliacoes');
    }
};
