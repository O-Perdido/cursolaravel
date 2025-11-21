<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOuOperador
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador') {
            return $next($request);
        }

        return redirect()->route('welcome')->with('error', 'Acesso negado, somente administradores podem acessar essa página.');

    }
}
