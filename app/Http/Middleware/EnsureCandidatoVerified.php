<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCandidatoVerified
{
    /**
     * Garante que candidato tenha e-mail verificado antes de acessar.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('sigeconcursos.candidato.login');
        }

        if (($user->nivel ?? null) === 'candidato' && empty($user->email_verified_at)) {
            return redirect()->route('verification.show', ['user' => $user->id]);
        }

        return $next($request);
    }
}