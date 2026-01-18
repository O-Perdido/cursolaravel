<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $table = 'tb_processos_arquivos';

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->integer('id_arquivo')->autoIncrement();
            $table->integer('fk_id_processo');
            $table->string('nome_exibicao', 150);
            $table->string('caminho_arquivo');
            $table->enum('tipo_arquivo', [
                'edital',
                'retificacao',
                'resultado',
                'outro'
            ])->default('outro');
            $table->timestamps();

            $table->foreign('fk_id_processo')
                ->references('id_processo')
                ->on('tb_processos_seletivos')
                ->onDelete('cascade');
            
            $table->index('fk_id_processo');
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
};
