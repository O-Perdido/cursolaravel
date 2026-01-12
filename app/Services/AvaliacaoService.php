<?php

namespace App\Services;

use App\Models\Avaliacao;
use App\Models\Termo;
use Carbon\Carbon;

class AvaliacaoService
{
    /**
     * Questões padrão da avaliação de estágio
     * Baseado no formulário em anexo
     */
    public function obterQuestoesBase(): array
    {
        return [
            [
                'id' => 1,
                'questao' => 'Como você avalia o desempenho geral do estagiário?',
                'tipo' => 'texto_longo',
                'ordem' => 1,
                'resposta' => '',
            ],
            [
                'id' => 2,
                'questao' => 'O estagiário demonstra conhecimento técnico adequado para suas atribuições?',
                'tipo' => 'escala_1_5',
                'ordem' => 2,
                'resposta' => '',
            ],
            [
                'id' => 3,
                'questao' => 'Como é a pontualidade e assiduidade do estagiário?',
                'tipo' => 'escala_1_5',
                'ordem' => 3,
                'resposta' => '',
            ],
            [
                'id' => 4,
                'questao' => 'O estagiário trabalha bem em equipe?',
                'tipo' => 'escala_1_5',
                'ordem' => 4,
                'resposta' => '',
            ],
            [
                'id' => 5,
                'questao' => 'Como você avalia a capacidade de iniciativa e autonomia?',
                'tipo' => 'escala_1_5',
                'ordem' => 5,
                'resposta' => '',
            ],
            [
                'id' => 6,
                'questao' => 'O estagiário demonstra interesse em aprender e se desenvolver?',
                'tipo' => 'escala_1_5',
                'ordem' => 6,
                'resposta' => '',
            ],
            [
                'id' => 7,
                'questao' => 'Apresenta facilidade em comunicação (oral e escrita)?',
                'tipo' => 'escala_1_5',
                'ordem' => 7,
                'resposta' => '',
            ],
            [
                'id' => 8,
                'questao' => 'Como é a organização e planejamento de suas atividades?',
                'tipo' => 'escala_1_5',
                'ordem' => 8,
                'resposta' => '',
            ],
            [
                'id' => 9,
                'questao' => 'Qual é sua observação geral sobre o estagiário? Pontos fortes e a melhorar.',
                'tipo' => 'texto_longo',
                'ordem' => 9,
                'resposta' => '',
            ],
        ];
    }

    /**
     * Cria uma avaliação para um termo
     */
    public function criarAvaliacao(
        Termo $termo,
        string $tipoAvaliacao = 'seis_meses',
        ?int $supervisorId = null
    ): Avaliacao {
        $avaliacao = Avaliacao::create([
            'fk_id_termo' => $termo->id_termo,
            'fk_id_supervisor' => $supervisorId ?? $termo->fk_id_supervisor,
            'tipo_avaliacao' => $tipoAvaliacao,
            'status' => 'pendente',
            'token_compartilhamento' => Avaliacao::gerarTokenCompartilhamento(),
            'questoes_respostas' => $this->obterQuestoesBase(),
            'criada_em' => now(),
            'atualizada_em' => now(),
        ]);

        return $avaliacao;
    }

    /**
     * Verifica se um termo está ativo
     */
    public function termoEstaAtivo(Termo $termo): bool
    {
        // Um termo está ativo se:
        // 1. Não possui rescisão
        // 2. Está dentro do período (data_inicio <= hoje <= data_fim)
        // 3. Não foi finalizado manualmente

        if ($termo->rescisao) {
            return false;
        }

        $hoje = Carbon::today();
        $dataInicio = $termo->data_inicio_estagio ?? $termo->data_inicio_estagio;
        $dataFim = $termo->data_fim_estagio ?? $termo->data_fim_estagio_fixo;

        // Se não tem data fim definida, consideramos ativo
        if (!$dataFim) {
            return true;
        }

        // Ainda está no período do estágio
        return $hoje <= $dataFim;
    }

    /**
     * Verifica se um termo atingiu 6 meses desde o início
     */
    public function atingiuSeisMeses(Termo $termo): bool
    {
        $dataInicio = $termo->data_inicio_estagio;

        if (!$dataInicio) {
            return false;
        }

        $dataAval = Carbon::parse($dataInicio)->addMonths(6);

        return Carbon::today() >= $dataAval;
    }

    /**
     * Cria avaliações automáticas para termos que atingiram 6 meses
     * Chamado por scheduled task
     * Filtrado apenas para termos de 2026 em diante
     */
    public function gerarAvaliacoesAutomaticas(): int
    {
        $termos = Termo::whereDoesntHave('rescisao')            
            ->where('data_inicio_estagio', '<=', Carbon::today()->subMonths(6))
            ->get();

        $contador = 0;

        foreach ($termos as $termo) {
            // Verifica se já existe avaliação de 6 meses pendente
            $avaliacaoExistente = $termo->avaliacoes()
                ->where('tipo_avaliacao', 'seis_meses')
                ->where('status', 'pendente')
                ->first();

            if (!$avaliacaoExistente) {
                $this->criarAvaliacao($termo, 'seis_meses');
                $contador++;
            }
        }

        return $contador;
    }

    /**
     * Cria avaliações ao finalizar um termo (rescisão)
     */
    public function gerarAvaliacaoFinalizacao(Termo $termo): Avaliacao
    {
        return $this->criarAvaliacao($termo, 'finalizacao');
    }
}
