<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Redefinir senha - SIGE')
            ->greeting('Olá, ' . ($notifiable->name ?? 'usuário') . '!')
            ->line('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para a sua conta.')
            ->action('Redefinir senha', $url)
            ->line('Este link de redefinição de senha irá expirar em ' . config('auth.passwords.users.expire') . ' minutos.')
            ->line('Se você não solicitou a redefinição de senha, nenhuma ação adicional é necessária.')
            ->salutation('Atenciosamente, Equipe EBCP');
    }
}
