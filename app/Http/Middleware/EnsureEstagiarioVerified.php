<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEstagiarioVerified
{
    /**
     * Garante que estagiário tenha e-mail verificado antes de acessar.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (($user->nivel ?? null) === 'estagiario' && empty($user->email_verified_at)) {
            // Deixa a tela de verificação decidir reenvio se necessário
            return redirect()->route('verification.show', ['user' => $user->id]);
        }

        return $next($request);
    }
}
