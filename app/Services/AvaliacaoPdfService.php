<?php

namespace App\Services;

use App\Models\Avaliacao;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class AvaliacaoPdfService
{
    /**
     * Gera o PDF da avaliação respondida e devolve a resposta de download.
     */
    public function download(Avaliacao $avaliacao)
    {
        if ($avaliacao->status !== 'respondida') {
            return null;
        }

        $fileName = sprintf('%d-avaliacao-%s.pdf', $avaliacao->id_avaliacao, Str::slug($avaliacao->tipo_avaliacao));

        return Pdf::loadView('avaliacoes.pdf', [
            'avaliacao' => $avaliacao,
        ])->setPaper('a4', 'portrait')->download($fileName);
    }
}
