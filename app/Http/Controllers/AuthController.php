<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerificationCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::user()) {
            $nivel = Auth::user()->nivel ?? '';
            return redirect()->route($this->routeForNivel($nivel));
        }
        return view('login');
    }

    public function loginAttempt(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Se for estagiário e não tiver e-mail verificado, redireciona para verificação
            if (in_array(($user->nivel ?? null), ['estagiario', 'candidato'], true) && empty($user->email_verified_at)) {
                // Garante que exista um código válido (gera e envia se estiver faltando ou expirado)
                $expiresAt = $user->email_verification_expires_at ?? null;
                $missingOrExpired = empty($user->email_verification_token) || ($expiresAt && now()->greaterThan($expiresAt));
                if ($missingOrExpired) {
                    try {
                        $code = $user->startEmailVerification();
                        Mail::to($user->email)->send(new EmailVerificationCode($code, $user->name ?? ''));
                    } catch (\Throwable $e) {
                        // Não bloquear o login por falha no envio; o usuário pode pedir reenvio na tela
                    }
                }
                return redirect()->route('verification.show', ['user' => $user->id]);
            }

            return redirect()->route($this->routeForNivel($user->nivel ?? ''));
        }

        return back()->with('error', 'Login inválido');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');

    }

    private function routeForNivel(string $nivel): string
    {
        return match ($nivel) {
            'admin', 'operador' => 'welcome.admin',
            'empresa' => 'welcome.empresa',
            'estagiario' => 'welcome.estagiario',
            'candidato' => 'sigeconcursos.candidato.dashboard',
            default => 'welcome',
        };
    }
}

