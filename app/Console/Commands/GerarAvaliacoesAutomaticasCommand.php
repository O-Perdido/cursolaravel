<?php

namespace App\Console\Commands;

use App\Services\AvaliacaoService;
use Illuminate\Console\Command;

class GerarAvaliacoesAutomaticasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avaliacoes:gerar-automaticas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera automaticamente avaliações para termos que completaram 6 meses';

    /**
     * Execute the console command.
     */
    public function handle(AvaliacaoService $avaliacaoService): int
    {
        $this->info('Gerando avaliações automáticas...');

        try {
            $contador = $avaliacaoService->gerarAvaliacoesAutomaticas();
            
            $this->info("✓ Avaliações geradas com sucesso: {$contador}");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Erro ao gerar avaliações: " . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
