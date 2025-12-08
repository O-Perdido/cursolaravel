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
        Schema::create('zapsign_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('document_token')->nullable();
            $table->string('status')->nullable();
            $table->json('payload')->nullable();
            $table->json('headers')->nullable();
            $table->timestamps();
            
            $table->index('document_token');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zapsign_webhook_logs');
    }
};
