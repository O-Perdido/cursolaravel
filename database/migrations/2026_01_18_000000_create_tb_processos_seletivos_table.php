<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $table = 'tb_processos_seletivos';

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->integer('id_processo')->autoIncrement();
            $table->string('numero_processo', 20)->unique(); // Ex: 2026-0001
            $table->string('titulo', 200);
            $table->integer('fk_id_empresa');
            $table->enum('status', [
                'rascunho',
                'aberto',
                'inscricoes',
                'encerrado',
                'finalizado'
            ])->default('rascunho');
            $table->datetime('data_abertura')->nullable();
            $table->datetime('data_fechamento_inscricoes')->nullable();
            $table->text('descricao_fases')->nullable();
            $table->json('cursos_destino')->nullable();
            $table->text('requisitos')->nullable();
            $table->text('observacoes')->nullable();
            $table->text('aviso_inscricao')->nullable();
            $table->timestamps();

            $table->foreign('fk_id_empresa')
                ->references('id_empresa')
                ->on('tb_empresas');
            
            $table->index('status');
            $table->index('fk_id_empresa');
            $table->index('data_abertura');
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
};
