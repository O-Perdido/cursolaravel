<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $table = 'tb_inscricoes_processo';

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->integer('id_inscricao')->autoIncrement();
            $table->integer('fk_id_processo');
            $table->integer('fk_id_estagiario');
            $table->enum('status_inscricao', [
                'inscrito',
                'deferido',
                'indeferido'
            ])->default('inscrito');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->foreign('fk_id_processo')
                ->references('id_processo')
                ->on('tb_processos_seletivos')
                ->onDelete('cascade');
            
            $table->foreign('fk_id_estagiario')
                ->references('id_estagiario')
                ->on('tb_estagiarios')
                ->onDelete('cascade');
            
            // Evita duplicação de inscrições
            $table->unique(['fk_id_processo', 'fk_id_estagiario']);
            
            $table->index('fk_id_processo');
            $table->index('fk_id_estagiario');
            $table->index('status_inscricao');
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
};
