<?php

namespace Database\Seeders;

use App\Models\Avaliacao;
use App\Models\Termo;
use Illuminate\Database\Seeder;

class AvaliacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtém alguns termos para criar avaliações de teste
        $termos = Termo::whereDoesntHave('rescisao')
            ->take(3)
            ->get();

        foreach ($termos as $termo) {
            // Cria uma avaliação de 6 meses
            Avaliacao::create([
                'fk_id_termo' => $termo->id_termo,
                'fk_id_supervisor' => $termo->fk_id_supervisor,
                'tipo_avaliacao' => 'seis_meses',
                'status' => 'pendente',
                'token_compartilhamento' => Avaliacao::gerarTokenCompartilhamento(),
                'questoes_respostas' => app(\App\Services\AvaliacaoService::class)->obterQuestoesBase(),
            ]);

            // Cria uma avaliação de finalizacao
            Avaliacao::create([
                'fk_id_termo' => $termo->id_termo,
                'fk_id_supervisor' => $termo->fk_id_supervisor,
                'tipo_avaliacao' => 'finalizacao',
                'status' => 'respondida',
                'respondida_em' => now(),
                'respondida_por' => 'supervisor@example.com',
                'questoes_respostas' => [
                    ['id' => 1, 'questao' => 'Como você avalia o desempenho geral do estagiário?', 'tipo' => 'texto_longo', 'ordem' => 1, 'resposta' => 'Muito bom desempenho'],
                    ['id' => 2, 'questao' => 'O estagiário demonstra conhecimento técnico adequado?', 'tipo' => 'escala_1_5', 'ordem' => 2, 'resposta' => '5'],
                ],
            ]);
        }

        $this->command->info('Avaliações de teste criadas com sucesso!');
    }
}
