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
        Schema::table('tb_folhas_pagamento', function (Blueprint $table) {
            $table->string('notaas_invoice_id')->nullable()->after('total_folha');
            $table->string('notaas_status')->nullable()->after('notaas_invoice_id');
            $table->text('notaas_pdf_url')->nullable()->after('notaas_status');
            $table->text('notaas_xml_url')->nullable()->after('notaas_pdf_url');
            $table->text('notaas_error_message')->nullable()->after('notaas_xml_url');
            $table->timestamp('notaas_emitted_at')->nullable()->after('notaas_error_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_folhas_pagamento', function (Blueprint $table) {
            $table->dropColumn([
                'notaas_invoice_id',
                'notaas_status',
                'notaas_pdf_url',
                'notaas_xml_url',
                'notaas_error_message',
                'notaas_emitted_at'
            ]);
        });
    }
};
