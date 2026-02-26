<?php

namespace App\Services;

use App\Models\Configuracao;
use App\Models\Termo;
use Carbon\CarbonImmutable;

class LimiteEstagioPorEmpresaService
{
    /**
     * Valida se o novo período excede o limite configurado de estágio para
     * o mesmo estagiário na mesma unidade concedente (empresa),
     * considerando histórico acumulado sem sobreposição.
     *
     * @param int $estagiarioId
     * @param int $empresaId
     * @param string $novoInicio
     * @param string $novoFim
     * @param int|null $ignorarTermoId
     * @return array{excede: bool, total_dias: int, limite_dias: int, modo: string, valor_configurado: int}
     */
    public function validarNovoPeriodo(
        int $estagiarioId,
        int $empresaId,
        string $novoInicio,
        string $novoFim,
        ?int $ignorarTermoId = null
    ): array {
        $intervalos = [];

        $inicioNovo = CarbonImmutable::parse($novoInicio)->startOfDay();
        $fimNovo = CarbonImmutable::parse($novoFim)->startOfDay();

        if ($fimNovo->lt($inicioNovo)) {
            return [
                'excede' => false,
                'total_dias' => 0,
                'limite_dias' => $this->obterLimiteDias(),
                'modo' => Configuracao::obterModoLimiteEstagioPorEmpresa(),
                'valor_configurado' => $this->obterValorConfiguradoAtual(),
            ];
        }

        $query = Termo::query()
            ->select(['id_termo', 'data_inicio_estagio', 'data_fim_estagio'])
            ->where('fk_id_estagiario', $estagiarioId)
            ->where('fk_id_empresa', $empresaId)
            ->whereNotNull('data_inicio_estagio')
            ->whereNotNull('data_fim_estagio');

        if ($ignorarTermoId) {
            $query->where('id_termo', '!=', $ignorarTermoId);
        }

        $termos = $query->get();

        foreach ($termos as $termo) {
            $inicio = CarbonImmutable::parse($termo->data_inicio_estagio)->startOfDay();
            $fim = CarbonImmutable::parse($termo->data_fim_estagio)->startOfDay();

            if ($fim->lt($inicio)) {
                continue;
            }

            $intervalos[] = [
                'inicio' => $inicio,
                'fim' => $fim,
            ];
        }

        $intervalos[] = [
            'inicio' => $inicioNovo,
            'fim' => $fimNovo,
        ];

        $intervalosConsolidados = $this->consolidarIntervalos($intervalos);
        $totalDias = $this->somarDias($intervalosConsolidados);

        $limiteDias = $this->obterLimiteDias();

        return [
            'excede' => $totalDias > $limiteDias,
            'total_dias' => $totalDias,
            'limite_dias' => $limiteDias,
            'modo' => Configuracao::obterModoLimiteEstagioPorEmpresa(),
            'valor_configurado' => $this->obterValorConfiguradoAtual(),
        ];
    }

    /**
     * @param array<int, array{inicio: CarbonImmutable, fim: CarbonImmutable}> $intervalos
     * @return array<int, array{inicio: CarbonImmutable, fim: CarbonImmutable}>
     */
    private function consolidarIntervalos(array $intervalos): array
    {
        if (empty($intervalos)) {
            return [];
        }

        usort($intervalos, function ($a, $b) {
            if ($a['inicio']->equalTo($b['inicio'])) {
                return $a['fim']->lessThan($b['fim']) ? -1 : 1;
            }

            return $a['inicio']->lessThan($b['inicio']) ? -1 : 1;
        });

        $consolidados = [];
        $atual = $intervalos[0];

        for ($i = 1; $i < count($intervalos); $i++) {
            $proximo = $intervalos[$i];

            if ($proximo['inicio']->lte($atual['fim']->addDay())) {
                if ($proximo['fim']->gt($atual['fim'])) {
                    $atual['fim'] = $proximo['fim'];
                }

                continue;
            }

            $consolidados[] = $atual;
            $atual = $proximo;
        }

        $consolidados[] = $atual;

        return $consolidados;
    }

    /**
     * @param array<int, array{inicio: CarbonImmutable, fim: CarbonImmutable}> $intervalos
     */
    private function somarDias(array $intervalos): int
    {
        $total = 0;

        foreach ($intervalos as $intervalo) {
            $total += $intervalo['inicio']->diffInDays($intervalo['fim']) + 1;
        }

        return $total;
    }

    private function obterLimiteDias(): int
    {
        $modo = Configuracao::obterModoLimiteEstagioPorEmpresa();

        if ($modo === 'dias') {
            return Configuracao::obterLimiteEstagioPorEmpresaDias();
        }

        $anos = Configuracao::obterLimiteEstagioPorEmpresaAnos();
        $base = CarbonImmutable::create(2021, 1, 1)->startOfDay();

        return $base->diffInDays($base->addYears($anos));
    }

    private function obterValorConfiguradoAtual(): int
    {
        $modo = Configuracao::obterModoLimiteEstagioPorEmpresa();

        return $modo === 'dias'
            ? Configuracao::obterLimiteEstagioPorEmpresaDias()
            : Configuracao::obterLimiteEstagioPorEmpresaAnos();
    }
}
