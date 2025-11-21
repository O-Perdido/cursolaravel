<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $table = 'tb_vagas';

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->integer('id_vaga')->autoIncrement();
            $table->string('numero_vaga', 20)->unique(); // Ex: 2025-001
            $table->text('atividades');
            $table->string('nome_orientador', 100);
            $table->string('cargo_orientador', 100);
            $table->date('data_inicio');
            $table->date('data_termino');
            $table->string('horario', 50);
            $table->integer('fk_id_local');
            $table->integer('fk_id_empresa');
            $table->string('lotacao', 150);
            $table->decimal('valor_bolsa', 10, 2);
            $table->decimal('valor_auxilio_transporte', 10, 2);
            $table->enum('status', ['disponivel', 'preenchida', 'expirada'])->default('disponivel');
            $table->integer('fk_id_termo')->nullable();
            $table->string('vinculo_tipo', 20)->nullable(); // vinculado/nao_vinculado
            $table->text('descricao')->nullable();
            $table->date('publicada_em')->nullable();
            $table->boolean('remunerada')->default(true);
            $table->string('tipo_vaga', 30)->nullable();
            $table->timestamps();

            $table->foreign('fk_id_local')->references('id_local')->on('tb_local');
            $table->foreign('fk_id_empresa')->references('id_empresa')->on('tb_empresas');
            $table->foreign('fk_id_termo')->references('id_termo')->on('tb_termos')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
};
