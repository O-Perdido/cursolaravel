<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotaasService
{
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('services.notaas.api_key');
        $this->apiUrl = (string) config('services.notaas.api_url', 'https://platform.notaas.com.br/api/v1');
    }

    /**
     * Envia os dados para emitir uma NFS-e
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function emitirNfse(array $payload): array
    {
        Log::info('NotaasService: Enviando payload de emissão de NFS-e', ['payload' => $payload]);

        if (empty($this->apiKey)) {
            throw new \Exception('Chave de API do Notaas (NOTAAS_API_KEY) não configurada no arquivo .env');
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/emitir', $payload);

        Log::info('NotaasService: Resposta recebida da emissão', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        if ($response->failed()) {
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? $errorData['error'] ?? $errorData['errorMessage'] ?? 'Erro desconhecido na API da Notaas.';
            
            if (isset($errorData['campos']) && is_array($errorData['campos'])) {
                $errorMessage .= ' (Campos: ' . implode(', ', $errorData['campos']) . ')';
            }
            
            throw new \Exception($errorMessage);
        }

        return $response->json() ?? [];
    }

    /**
     * Consulta o status de uma nota fiscal pelo ID
     *
     * @param string $invoiceId
     * @return array
     * @throws \Exception
     */
    public function consultarStatus(string $invoiceId): array
    {
        Log::info('NotaasService: Consultando status do invoice', ['invoiceId' => $invoiceId]);

        if (empty($this->apiKey)) {
            throw new \Exception('Chave de API do Notaas (NOTAAS_API_KEY) não configurada no arquivo .env');
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Accept' => 'application/json',
        ])->get($this->apiUrl . '/invoices/' . $invoiceId . '/status');

        Log::info('NotaasService: Resposta recebida do status', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        if ($response->failed()) {
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? $errorData['error'] ?? $errorData['errorMessage'] ?? 'Erro ao consultar status na API da Notaas.';
            if (isset($errorData['campos']) && is_array($errorData['campos'])) {
                $errorMessage .= ' (Campos: ' . implode(', ', $errorData['campos']) . ')';
            }
            throw new \Exception($errorMessage);
        }

        return $response->json() ?? [];
    }

    /**
     * Solicita o cancelamento de uma NFS-e
     *
     * @param string $invoiceId
     * @param string|null $motivo
     * @return array
     * @throws \Exception
     */
    public function cancelarNfse(string $invoiceId, ?string $motivo = null): array
    {
        Log::info('NotaasService: Solicitando cancelamento de NFS-e', [
            'invoiceId' => $invoiceId,
            'motivo' => $motivo,
        ]);

        if (empty($this->apiKey)) {
            throw new \Exception('Chave de API do Notaas (NOTAAS_API_KEY) não configurada no arquivo .env');
        }

        $payload = ['invoiceId' => $invoiceId];
        if (!empty($motivo)) {
            $payload['motivo'] = substr($motivo, 0, 255);
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/cancelar', $payload);

        Log::info('NotaasService: Resposta recebida do cancelamento', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        if ($response->failed()) {
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? $errorData['error'] ?? $errorData['errorMessage'] ?? 'Erro ao solicitar cancelamento na API da Notaas.';
            if (isset($errorData['campos']) && is_array($errorData['campos'])) {
                $errorMessage .= ' (Campos: ' . implode(', ', $errorData['campos']) . ')';
            }
            throw new \Exception($errorMessage);
        }

        return $response->json() ?? [];
    }
}
