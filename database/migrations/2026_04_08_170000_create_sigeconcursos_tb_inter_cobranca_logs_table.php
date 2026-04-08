<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sigeconcursos_tb_inter_cobranca_logs', function (Blueprint $table) {
            $table->bigIncrements('id_log');
            $table->integer('fk_id_inscricao')->nullable();
            $table->string('codigo_solicitacao', 60)->nullable();
            $table->string('tipo_evento', 40);
            $table->boolean('sucesso')->default(false);
            $table->integer('status_http')->nullable();
            $table->text('mensagem')->nullable();
            $table->longText('payload_request')->nullable();
            $table->longText('payload_response')->nullable();
            $table->timestamps();

            $table->foreign('fk_id_inscricao', 'sc_inter_log_inscricao_fk')
                ->references('id_inscricao')
                ->on('sigeconcursos_tb_inscricoes')
                ->nullOnDelete();

            $table->index('codigo_solicitacao', 'sc_inter_log_codigo_idx');
            $table->index(['tipo_evento', 'sucesso'], 'sc_inter_log_tipo_sucesso_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sigeconcursos_tb_inter_cobranca_logs');
    }
};
