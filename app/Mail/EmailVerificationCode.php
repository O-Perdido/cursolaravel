<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationCode extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $userName;

    public function __construct(string $code, string $userName)
    {
        $this->code = $code;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('Seu código de verificação')
            ->view('emails.verify_code')
            ->with([
                'code' => $this->code,
                'userName' => $this->userName,
            ]);
    }
}
