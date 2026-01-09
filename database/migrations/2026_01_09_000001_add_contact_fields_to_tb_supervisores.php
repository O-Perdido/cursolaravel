<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_supervisores', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_supervisores', 'celular_supervisor')) {
                $table->string('celular_supervisor', 20)->nullable()->after('cpf_supervisor');
            }

            if (!Schema::hasColumn('tb_supervisores', 'email_supervisor')) {
                $table->string('email_supervisor', 150)->nullable()->after('celular_supervisor');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_supervisores', function (Blueprint $table) {
            if (Schema::hasColumn('tb_supervisores', 'email_supervisor')) {
                $table->dropColumn('email_supervisor');
            }

            if (Schema::hasColumn('tb_supervisores', 'celular_supervisor')) {
                $table->dropColumn('celular_supervisor');
            }
        });
    }
};
