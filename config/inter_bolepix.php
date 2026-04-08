<?php

return [
    'enabled' => env('INTER_BOLEPIX_ENABLED', false),
    'sandbox' => env('INTER_BOLEPIX_SANDBOX', true),

    'base_url' => env(
        'INTER_BOLEPIX_BASE_URL',
        env('INTER_BOLEPIX_SANDBOX', true)
            ? 'https://cdpj-sandbox.partners.uatinter.co'
            : 'https://cdpj.partners.bancointer.com.br'
    ),

    'oauth_token_path' => env('INTER_BOLEPIX_OAUTH_TOKEN_PATH', '/oauth/v2/token'),
    'charge_path' => env('INTER_BOLEPIX_CHARGE_PATH', '/cobranca/v3/cobrancas'),

    'client_id' => env('INTER_BOLEPIX_CLIENT_ID'),
    'client_secret' => env('INTER_BOLEPIX_CLIENT_SECRET'),
    'account_number' => env('INTER_BOLEPIX_ACCOUNT_NUMBER'),
    'webhook_url' => env('INTER_BOLEPIX_WEBHOOK_URL'),

    'scope_write' => env('INTER_BOLEPIX_SCOPE_WRITE', 'boleto-cobranca.write'),
    'scope_read' => env('INTER_BOLEPIX_SCOPE_READ', 'boleto-cobranca.read'),

    'cert_path' => env('INTER_BOLEPIX_CERT_PATH'),
    'key_path' => env('INTER_BOLEPIX_KEY_PATH'),

    'timeout' => (int) env('INTER_BOLEPIX_TIMEOUT', 30),
    'verify_ssl' => env('INTER_BOLEPIX_VERIFY_SSL', true),

    'default_due_days' => (int) env('INTER_BOLEPIX_DEFAULT_DUE_DAYS', 3),
];
