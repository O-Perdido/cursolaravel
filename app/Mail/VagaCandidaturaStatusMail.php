<?php

namespace App\Mail;

use App\Models\VagaCandidatura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VagaCandidaturaStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public VagaCandidatura $candidatura,
        public string $statusLabel,
    ) {
    }

    public function build(): self
    {
        return $this->subject('Atualização da sua candidatura em vaga de estágio')
            ->view('emails.vagas.candidatura-status');
    }
}