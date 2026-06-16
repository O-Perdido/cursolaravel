<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\Operador;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            '/webhooks/zapsign',
            '/sigeconcursos/inter/webhook',
            '/webhooks/notaas',
        ]);

        $middleware->alias([
            'admin' => App\Http\Middleware\Admin::class,
            'operador' => App\Http\Middleware\Operador::class,
            'empresa' => App\Http\Middleware\Empresa::class,
            'admin_ou_operador' => App\Http\Middleware\AdminOuOperador::class,
            'nivel' => App\Http\Middleware\EnsureNivel::class,
            'estagiario_verified' => App\Http\Middleware\EnsureEstagiarioVerified::class,
            'candidato_verified' => App\Http\Middleware\EnsureCandidatoVerified::class,
        ]);

        $middleware->prependToGroup('admin_operador', [
            
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
