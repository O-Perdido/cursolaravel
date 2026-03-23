<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureNivel
{
    /**
     * Handle an incoming request.
     * Permite acesso apenas se o nível do usuário estiver na lista permitida.
     * Caso contrário, redireciona para a dashboard adequada ao nível atual do usuário.
     */
    public function handle(Request $request, Closure $next, string ...$niveis)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $nivel = (string) ($user->nivel ?? '');
        if (in_array($nivel, $niveis, true)) {
            return $next($request);
        }

        // Redireciona para a rota correta de acordo com o nível do usuário
        return redirect()->route($this->routeForNivel($nivel));
    }

    private function routeForNivel(string $nivel): string
    {
        return match ($nivel) {
            'admin', 'operador' => 'welcome.admin',
            'empresa' => 'welcome.empresa',
            'estagiario' => 'welcome.estagiario',
            'candidato' => 'sigeconcursos.candidato.dashboard',
            default => 'login',
        };
    }
}
