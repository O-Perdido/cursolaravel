<?php

namespace App\Jobs;

use App\Models\Termo;
use App\Services\AvaliacaoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GerarAvaliacoesAutomaticasJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AvaliacaoService $avaliacaoService): void
    {
        try {
            $contador = $avaliacaoService->gerarAvaliacoesAutomaticas();
            
            Log::info("Avaliações de 6 meses geradas automaticamente: {$contador}");
        } catch (\Exception $e) {
            Log::error("Erro ao gerar avaliações automáticas: " . $e->getMessage());
        }
    }
}
