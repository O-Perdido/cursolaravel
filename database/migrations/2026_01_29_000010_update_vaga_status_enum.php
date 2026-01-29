<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::statement("UPDATE tb_vagas SET status = 'suspensa' WHERE status = 'expirada'");
        DB::statement("ALTER TABLE tb_vagas MODIFY status ENUM('disponivel','preenchida','suspensa') NOT NULL DEFAULT 'disponivel'");
    }

    public function down()
    {
        DB::statement("UPDATE tb_vagas SET status = 'expirada' WHERE status = 'suspensa'");
        DB::statement("ALTER TABLE tb_vagas MODIFY status ENUM('disponivel','preenchida','expirada') NOT NULL DEFAULT 'disponivel'");
    }
};
