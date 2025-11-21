<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZapSignWebhookLog extends Model
{
    use HasFactory;

    protected $table = 'zapsign_webhook_logs';
    protected $fillable = [
        'document_token',
        'status',
        'payload',
        'headers',
    ];

    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
    ];
}
