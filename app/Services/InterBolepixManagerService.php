<?php

namespace App\Services;

use App\Models\SigeConcursoCandidato;
use App\Models\SigeConcursoInscricao;
use App\Models\SigeConcursoInterCobrancaLog;

class InterBolepixManagerService
{
    public function __construct(
        private readonly InterBolepixService $interBolepixService,
    ) {
    }

    public function integrationEnabled(): bool
    {
        return $this->interBolepixService->integrationEnabled();
    }

    public function emitirParaInscricao(SigeConcursoInscricao $inscricao): array
    {
        $inscricao->loadMissing(['processo', 'candidato.cidade.estado']);

        if (!$this->integrationEnabled()) {
            return ['success' => false, 'message' => 'Integração de cobrança com Banco Inter não está habilitada neste ambiente.'];
        }

        if (!$inscricao->processo?->possui_taxa_inscricao) {
            return ['success' => false, 'message' => 'Este processo não possui taxa de inscrição.'];
        }

        if (in_array($inscricao->status_pagamento, ['isento', 'nao_aplicavel', 'pago'], true)) {
            return ['success' => false, 'message' => 'Não é necessário gerar boleto para esta inscrição.'];
        }

        if ($inscricao->inter_codigo_solicitacao) {
            return ['success' => true, 'message' => 'Cobrança já existente para esta inscrição.', 'already_exists' => true];
        }

        $valorTaxa = $inscricao->valor_taxa_aplicada !== null
            ? (float) $inscricao->valor_taxa_aplicada
            : (float) ($inscricao->processo->valor_taxa_padrao ?? 0);

        if ($valorTaxa < 2.5) {
            return ['success' => false, 'message' => 'A taxa de inscrição precisa ser maior ou igual a R$ 2,50 para emissão no Inter.'];
        }

        $dataVencimento = $this->definirDataVencimentoBoleto($inscricao);
        $seuNumero = str_pad('SCI' . (string) $inscricao->id_inscricao, 15, '0', STR_PAD_LEFT);
        $payload = [
            'seuNumero' => $seuNumero,
            'valorNominal' => round($valorTaxa, 2),
            'dataVencimento' => $dataVencimento,
            'numDiasAgenda' => 60,
            'pagador' => $this->montarPagadorInter($inscricao->candidato),
            'formasRecebimento' => ['BOLETO', 'PIX'],
            'mensagem' => [
                'linha1' => 'Taxa de inscricao SIGE Concursos',
                'linha2' => 'Inscricao ' . ($inscricao->numero_inscricao ?: $inscricao->id_inscricao),
            ],
        ];

        $resultado = $this->interBolepixService->emitirCobranca($payload);
        $payloadResposta = $resultado['data'] ?? ($resultado['body'] ?? []);
        
        // Garantir que payloadResposta é um array
        if (!is_array($payloadResposta)) {
            $payloadResposta = ['resposta_raw' => (string) $payloadResposta];
        }
        
        // Sempre adicionar technical_message quando disponível
        if (!empty($resultado['technical_message'])) {
            $payloadResposta['technical_message'] = (string) $resultado['technical_message'];
        }

        $this->registrarLog($inscricao, 'emissao', $resultado['success'] ?? false, $resultado['status'] ?? null, $resultado['message'] ?? null, $payload, $payloadResposta);

        if (!($resultado['success'] ?? false)) {
            return ['success' => false, 'message' => 'Não foi possível emitir o boleto no Inter no momento.'];
        }

        $codigoSolicitacao = (string) ($resultado['data']['codigoSolicitacao'] ?? '');

        if ($codigoSolicitacao === '') {
            return ['success' => false, 'message' => 'O Inter não retornou código da cobrança para esta emissão.'];
        }

        $inscricao->update([
            'inter_codigo_solicitacao' => $codigoSolicitacao,
            'inter_seu_numero' => $seuNumero,
            'inter_data_vencimento' => $dataVencimento,
            'inter_payload_cobranca' => json_encode($resultado['data'], JSON_UNESCAPED_UNICODE),
        ]);

        return ['success' => true, 'message' => 'Cobrança emitida com sucesso.', 'codigo_solicitacao' => $codigoSolicitacao];
    }

    public function sincronizarInscricao(SigeConcursoInscricao $inscricao, string $origem = 'manual'): array
    {
        $inscricao->loadMissing(['processo', 'candidato.cidade.estado']);

        if (!$this->integrationEnabled()) {
            return ['success' => false, 'message' => 'Integração de cobrança com Banco Inter não está habilitada neste ambiente.'];
        }

        if (!$inscricao->inter_codigo_solicitacao) {
            return ['success' => false, 'message' => 'Ainda não existe cobrança emitida para esta inscrição.'];
        }

        $resultado = $this->interBolepixService->recuperarCobranca((string) $inscricao->inter_codigo_solicitacao);
        $payloadResposta = $resultado['data'] ?? ($resultado['body'] ?? []);
        
        // Garantir que payloadResposta é um array
        if (!is_array($payloadResposta)) {
            $payloadResposta = ['resposta_raw' => (string) $payloadResposta];
        }
        
        // Sempre adicionar technical_message quando disponível
        if (!empty($resultado['technical_message'])) {
            $payloadResposta['technical_message'] = (string) $resultado['technical_message'];
        }

        $this->registrarLog($inscricao, 'consulta_' . $origem, $resultado['success'] ?? false, $resultado['status'] ?? null, $resultado['message'] ?? null, ['codigoSolicitacao' => $inscricao->inter_codigo_solicitacao], $payloadResposta);

        if (!($resultado['success'] ?? false)) {
            return ['success' => false, 'message' => 'Não foi possível atualizar o status do pagamento no momento.'];
        }

        $this->aplicarDadosDaCobranca($inscricao, $resultado['data'] ?? []);

        return ['success' => true, 'message' => 'Situação do boleto atualizada com sucesso.', 'inscricao' => $inscricao->fresh()];
    }

    public function recuperarPdf(SigeConcursoInscricao $inscricao): array
    {
        if (!$this->integrationEnabled()) {
            return ['success' => false, 'message' => 'Integração de cobrança com Banco Inter não está habilitada neste ambiente.'];
        }

        if (!$inscricao->inter_codigo_solicitacao) {
            return ['success' => false, 'message' => 'Ainda não existe cobrança emitida para esta inscrição.'];
        }

        $resultado = $this->interBolepixService->recuperarPdfCobranca((string) $inscricao->inter_codigo_solicitacao);
        $payloadResposta = $resultado['data'] ?? ($resultado['body'] ?? []);
        
        // Garantir que payloadResposta é um array
        if (!is_array($payloadResposta)) {
            $payloadResposta = ['resposta_raw' => (string) $payloadResposta];
        }
        
        // Sempre adicionar technical_message quando disponível
        if (!empty($resultado['technical_message'])) {
            $payloadResposta['technical_message'] = (string) $resultado['technical_message'];
        }

        $this->registrarLog($inscricao, 'pdf', $resultado['success'] ?? false, $resultado['status'] ?? null, $resultado['message'] ?? null, ['codigoSolicitacao' => $inscricao->inter_codigo_solicitacao], $payloadResposta);

        if (!($resultado['success'] ?? false)) {
            return ['success' => false, 'message' => 'Não foi possível recuperar o PDF do boleto no momento.'];
        }

        $pdfBase64 = (string) ($resultado['data']['pdf'] ?? '');

        if ($pdfBase64 === '') {
            return ['success' => false, 'message' => 'O Inter não retornou o PDF do boleto.'];
        }

        $pdfBinario = base64_decode($pdfBase64, true);

        if ($pdfBinario === false) {
            return ['success' => false, 'message' => 'Falha ao decodificar o PDF retornado pelo Inter.'];
        }

        return ['success' => true, 'pdf' => $pdfBinario];
    }

    public function processarCallback(array $callback): array
    {
        $callbackNormalizado = $this->normalizarCallbackInter($callback);
        $codigoSolicitacao = (string) ($callbackNormalizado['codigoSolicitacao'] ?? '');

        if ($codigoSolicitacao === '') {
            $this->registrarLog(null, 'webhook', false, null, 'Callback recebido sem codigoSolicitacao.', null, $callback);

            return ['success' => false, 'message' => 'Callback sem código de solicitação.', 'codigo_solicitacao' => null];
        }

        $inscricao = SigeConcursoInscricao::where('inter_codigo_solicitacao', $codigoSolicitacao)->first();

        $this->registrarLog($inscricao, 'webhook', !empty($inscricao), null, $inscricao ? 'Callback recebido.' : 'Inscrição não localizada pelo código da cobrança.', null, $callback, $codigoSolicitacao);

        if (!$inscricao) {
            return ['success' => false, 'message' => 'Inscrição não localizada para o callback.', 'codigo_solicitacao' => $codigoSolicitacao];
        }

        $this->aplicarDadosWebhook($inscricao, $callbackNormalizado);

        return ['success' => true, 'message' => 'Callback processado com sucesso.', 'codigo_solicitacao' => $codigoSolicitacao];
    }

    public function mapearSituacaoInterParaStatusPagamento(?string $situacaoInter, string $statusAtual): string
    {
        $situacao = strtoupper((string) $situacaoInter);

        if ($situacao === '') {
            return $statusAtual;
        }

        return match ($situacao) {
            'RECEBIDO', 'MARCADO_RECEBIDO' => 'pago',
            'A_RECEBER', 'ATRASADO', 'EM_PROCESSAMENTO', 'FALHA_EMISSAO', 'PROTESTO', 'EXPIRADO', 'CANCELADO' => 'pendente',
            default => $statusAtual,
        };
    }

    private function aplicarDadosDaCobranca(SigeConcursoInscricao $inscricao, array $data): void
    {
        $cobranca = $data['cobranca'] ?? [];
        $boleto = $data['boleto'] ?? [];
        $pix = $data['pix'] ?? [];
        $situacaoInter = (string) ($cobranca['situacao'] ?? '');

        $inscricao->update([
            'inter_situacao' => $situacaoInter ?: null,
            'inter_nosso_numero' => $boleto['nossoNumero'] ?? null,
            'inter_linha_digitavel' => $boleto['linhaDigitavel'] ?? null,
            'inter_codigo_barras' => $boleto['codigoBarras'] ?? null,
            'inter_pix_copia_cola' => $pix['pixCopiaECola'] ?? null,
            'inter_ultima_sincronizacao_em' => now(),
            'inter_payload_cobranca' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'status_pagamento' => $this->mapearSituacaoInterParaStatusPagamento($situacaoInter, $inscricao->status_pagamento),
        ]);
    }

    private function aplicarDadosWebhook(SigeConcursoInscricao $inscricao, array $callback): void
    {
        $situacaoInter = (string) ($callback['situacao'] ?? '');

        $inscricao->update([
            'inter_situacao' => $situacaoInter ?: $inscricao->inter_situacao,
            'inter_nosso_numero' => $callback['nossoNumero'] ?? $inscricao->inter_nosso_numero,
            'inter_linha_digitavel' => $callback['linhaDigitavel'] ?? $inscricao->inter_linha_digitavel,
            'inter_codigo_barras' => $callback['codigoBarras'] ?? $inscricao->inter_codigo_barras,
            'inter_pix_copia_cola' => $callback['pixCopiaECola'] ?? $inscricao->inter_pix_copia_cola,
            'inter_ultima_sincronizacao_em' => now(),
            'inter_payload_cobranca' => json_encode($callback, JSON_UNESCAPED_UNICODE),
            'status_pagamento' => $this->mapearSituacaoInterParaStatusPagamento($situacaoInter, $inscricao->status_pagamento),
        ]);
    }

    private function normalizarCallbackInter(array $callback): array
    {
        $cobranca = is_array($callback['cobranca'] ?? null) ? $callback['cobranca'] : [];
        $boleto = is_array($callback['boleto'] ?? null) ? $callback['boleto'] : [];
        $pix = is_array($callback['pix'] ?? null) ? $callback['pix'] : [];

        return [
            'codigoSolicitacao' => (string) ($callback['codigoSolicitacao'] ?? $cobranca['codigoSolicitacao'] ?? ''),
            'situacao' => (string) ($callback['situacao'] ?? $cobranca['situacao'] ?? ''),
            'nossoNumero' => $callback['nossoNumero'] ?? $boleto['nossoNumero'] ?? null,
            'linhaDigitavel' => $callback['linhaDigitavel'] ?? $boleto['linhaDigitavel'] ?? null,
            'codigoBarras' => $callback['codigoBarras'] ?? $boleto['codigoBarras'] ?? null,
            'pixCopiaECola' => $callback['pixCopiaECola'] ?? $pix['pixCopiaECola'] ?? null,
        ];
    }

    private function definirDataVencimentoBoleto(SigeConcursoInscricao $inscricao): string
    {
        $defaultDays = (int) config('inter_bolepix.default_due_days', 3);
        $dataPadrao = now()->addDays(max(1, $defaultDays))->toDateString();
        $dataFimInscricao = optional($inscricao->processo?->data_fim_inscricoes)->toDateString();

        if (!$dataFimInscricao || $dataFimInscricao < now()->toDateString()) {
            return $dataPadrao;
        }

        return $dataFimInscricao;
    }

    private function montarPagadorInter(?SigeConcursoCandidato $candidato): array
    {
        $telefone = preg_replace('/\D/', '', (string) ($candidato?->numero_celular ?: $candidato?->numero_telefone));
        $ddd = strlen($telefone) >= 10 ? substr($telefone, 0, 2) : '00';
        $numeroTelefone = strlen($telefone) >= 10 ? substr($telefone, 2) : '999999999';

        return [
            'cpfCnpj' => preg_replace('/\D/', '', (string) ($candidato?->numero_cpf ?: '')), 
            'tipoPessoa' => 'FISICA',
            'nome' => (string) ($candidato?->nome_completo ?: 'CANDIDATO SIGE'),
            'email' => (string) ($candidato?->email ?: ''),
            'ddd' => $ddd,
            'telefone' => $numeroTelefone,
            'endereco' => (string) ($candidato?->endereco ?: 'NAO INFORMADO'),
            'numero' => (string) ($candidato?->numero_endereco ?: 'S/N'),
            'complemento' => (string) ($candidato?->complemento_endereco ?: ''),
            'bairro' => (string) ($candidato?->bairro ?: 'NAO INFORMADO'),
            'cidade' => (string) ($candidato?->cidade?->nm_cidade ?: 'NAO INFORMADO'),
            'uf' => (string) ($candidato?->cidade?->estado?->uf_estado ?: 'MG'),
            'cep' => preg_replace('/\D/', '', (string) ($candidato?->numero_cep ?: '00000000')),
        ];
    }

    private function registrarLog(?SigeConcursoInscricao $inscricao, string $tipoEvento, bool $sucesso, ?int $statusHttp = null, ?string $mensagem = null, mixed $payloadRequest = null, mixed $payloadResponse = null, ?string $codigoSolicitacao = null): void
    {
        $payloadRequestNormalizado = $this->normalizarPayloadLog($payloadRequest);
        $payloadResponseNormalizado = $this->normalizarPayloadLog($payloadResponse);

        SigeConcursoInterCobrancaLog::create([
            'fk_id_inscricao' => $inscricao?->id_inscricao,
            'codigo_solicitacao' => $codigoSolicitacao ?: $inscricao?->inter_codigo_solicitacao,
            'tipo_evento' => $tipoEvento,
            'sucesso' => $sucesso,
            'status_http' => $statusHttp,
            'mensagem' => $mensagem,
            'payload_request' => $payloadRequestNormalizado !== null ? json_encode($payloadRequestNormalizado, JSON_UNESCAPED_UNICODE) : null,
            'payload_response' => $payloadResponseNormalizado !== null ? json_encode($payloadResponseNormalizado, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }

    private function normalizarPayloadLog(mixed $payload): mixed
    {
        if (!is_string($payload)) {
            return $payload;
        }

        $trimmed = trim($payload);
        if ($trimmed === '') {
            return $payload;
        }

        $decoded = json_decode($trimmed, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $payload;
    }
}
