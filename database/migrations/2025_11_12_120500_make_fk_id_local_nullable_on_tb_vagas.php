<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Tornar fk_id_local anulável sem exigir doctrine/dbal
        DB::statement('ALTER TABLE tb_vagas MODIFY fk_id_local INT NULL');
    }

    public function down(): void
    {
        // Reverter para NOT NULL
        DB::statement('ALTER TABLE tb_vagas MODIFY fk_id_local INT NOT NULL');
    }
};
