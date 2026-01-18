<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $table = 'tb_resultados_processo';

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->integer('id_resultado')->autoIncrement();
            $table->integer('fk_id_processo');
            $table->string('numero_resultado', 150);
            $table->string('arquivo_resultado')->nullable();
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
