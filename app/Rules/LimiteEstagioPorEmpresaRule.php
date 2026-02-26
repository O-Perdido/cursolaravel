<?php

namespace App\Rules;

use App\Services\LimiteEstagioPorEmpresaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LimiteEstagioPorEmpresaRule implements ValidationRule
{
    public function __construct(
        private readonly ?int $estagiarioId,
        private readonly ?int $empresaId,
        private readonly ?string $dataInicioEstagio,
        private readonly ?int $ignorarTermoId = null
    ) {
    }

    /**
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->estagiarioId || !$this->empresaId || !$this->dataInicioEstagio || !$value) {
            return;
        }

        $service = app(LimiteEstagioPorEmpresaService::class);

        $resultado = $service->validarNovoPeriodo(
            (int) $this->estagiarioId,
            (int) $this->empresaId,
            (string) $this->dataInicioEstagio,
            (string) $value,
            $this->ignorarTermoId
        );

        if (!$resultado['excede']) {
            return;
        }

        $modoDescricao = $resultado['modo'] === 'dias'
            ? number_format($resultado['valor_configurado'], 0, ',', '.') . ' dias'
            : number_format($resultado['valor_configurado'], 0, ',', '.') . ' ano(s)';

        $fail(
            'Este estagiário excede o limite de permanência na mesma unidade concedente. '
            . 'Total acumulado para esta empresa: '
            . number_format($resultado['total_dias'], 0, ',', '.')
            . ' dia(s). Limite configurado: '
            . $modoDescricao
            . ' ('
            . number_format($resultado['limite_dias'], 0, ',', '.')
            . ' dia(s)).'
        );
    }
}
