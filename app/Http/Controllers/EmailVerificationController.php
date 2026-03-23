<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerificationCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    public function show(Request $request)
    {
        $userId = $request->query('user');
        $user = User::findOrFail($userId);
        // Não expor email completo
        $maskedEmail = $this->maskEmail($user->email);
        return view('auth.verify_email_code', compact('user', 'maskedEmail'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string|size:6',
        ]);
        $user = User::findOrFail($request->user_id);

        if (!$user->checkEmailVerificationCode($request->code)) {
            return back()->withErrors(['code' => 'Código inválido ou expirado.'])->withInput();
        }

        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->email_verification_expires_at = null;
        $user->save();

        return redirect()->route($this->routeForNivel((string) ($user->nivel ?? '')))
            ->with('success', 'E-mail verificado com sucesso! Você já pode acessar.');
    }

    public function resend(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        $user = User::findOrFail($request->user_id);

        $code = $user->startEmailVerification();
        Mail::to($user->email)->send(new EmailVerificationCode($code, $user->name ?? ''));

        return back()->with('status', 'Novo código enviado para seu e-mail.');
    }

    private function maskEmail(string $email): string
    {
        [$name, $domain] = explode('@', $email);
        $maskedName = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 2, 0));
        $domainParts = explode('.', $domain);
        $domainParts[0] = substr($domainParts[0], 0, 1) . str_repeat('*', max(strlen($domainParts[0]) - 1, 0));
        return $maskedName . '@' . implode('.', $domainParts);
    }

    private function routeForNivel(string $nivel): string
    {
        return match ($nivel) {
            'candidato' => 'sigeconcursos.candidato.login',
            default => 'login',
        };
    }
}
