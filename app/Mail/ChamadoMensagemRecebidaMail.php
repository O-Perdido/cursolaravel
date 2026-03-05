<?php

namespace App\Mail;

use App\Models\Chamado;
use App\Models\ChamadoMensagem;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChamadoMensagemRecebidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public Chamado $chamado;
    public ChamadoMensagem $mensagem;
    public string $remetenteNome;
    public string $urlChamado;

    public function __construct(Chamado $chamado, ChamadoMensagem $mensagem, string $remetenteNome, string $urlChamado)
    {
        $this->chamado = $chamado;
        $this->mensagem = $mensagem;
        $this->remetenteNome = $remetenteNome;
        $this->urlChamado = $urlChamado;
    }

    public function build()
    {
        return $this->subject('Novo retorno no chamado ' . $this->chamado->protocolo)
            ->view('emails.chamado_mensagem_recebida')
            ->with([
                'chamado' => $this->chamado,
                'mensagem' => $this->mensagem,
                'remetenteNome' => $this->remetenteNome,
                'urlChamado' => $this->urlChamado,
            ]);
    }
}
