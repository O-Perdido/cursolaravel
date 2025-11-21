<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ZapSign API Token
    |--------------------------------------------------------------------------
    |
    | Token de autenticação da API do ZapSign.
    | Obtenha em: Configurações > Integrações > API Zapsign > Token de Acesso
    |
    */
    'api_token' => env('ZAPSIGN_API_TOKEN', '2af49f29-9e90-4381-be11-8532f7f48c8c8bbaffde-bac3-47bf-a964-3439fc12061a'),

    /*
    |--------------------------------------------------------------------------
    | ZapSign API Base URL
    |--------------------------------------------------------------------------
    |
    | URL base da API do ZapSign
    |
    */
    'api_url' => env('ZAPSIGN_API_URL', 'https://api.zapsign.com.br/api/v1'),

    /*
    |--------------------------------------------------------------------------
    | Configurações de Documento
    |--------------------------------------------------------------------------
    */
    'sandbox' => env('ZAPSIGN_SANDBOX', false),
    
    /*
    |--------------------------------------------------------------------------
    | Configurações de Webhook (opcional)
    |--------------------------------------------------------------------------
    */
    'webhook_url' => env('ZAPSIGN_WEBHOOK_URL', null),
    'webhook_secret' => env('ZAPSIGN_WEBHOOK_SECRET', null),
    'webhook_header' => env('ZAPSIGN_WEBHOOK_HEADER', 'Authorization'),
];
