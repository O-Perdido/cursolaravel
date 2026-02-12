<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ZapSignService
{
    protected $apiToken;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiToken = config('zapsign.api_token');
        $this->apiUrl = config('zapsign.api_url');
    }

    /**
     * Criar documento no ZapSign via Upload de PDF
     * 
     * @param string $pdfUrl URL pública do PDF
     * @param string $documentName Nome do documento
     * @param array $signatarios Array de signatários com name, email, phone, etc
     * @return array Resposta da API
     */
    public function criarDocumento($pdfUrl, $documentName, array $signatarios)
    {
        try {
            $payload = [
                'name' => $documentName,
                'url_pdf' => $pdfUrl,
                'lang' => 'pt-br',
                'sandbox' => config('zapsign.sandbox', false),
                'signers' => $this->formatarSignatarios($signatarios),
            ];

            $webhookUrl = config('zapsign.webhook_url');
            if ($webhookUrl) {
                $payload['webhook_url'] = $webhookUrl;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])
            ->withOptions([
                'verify' => false, // Desabilitar verificação SSL (apenas para desenvolvimento)
            ])
            ->post($this->apiUrl . '/docs/', $payload);

            if ($response->successful()) {
                Log::info('Documento criado no ZapSign', ['response' => $response->json()]);
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Erro ao criar documento no ZapSign', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao criar documento: ' . $response->body(),
            ];

        } catch (Exception $e) {
            Log::error('Exceção ao criar documento no ZapSign', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Criar documento no ZapSign via Base64 (mais seguro)
     * 
     * @param string $pdfBase64 PDF em base64
     * @param string $documentName Nome do documento
     * @param array $signatarios Array de signatários com name, email, phone, etc
     * @return array Resposta da API
     */
    public function criarDocumentoBase64($pdfBase64, $documentName, array $signatarios)
    {
        try {
            $payload = [
                'name' => $documentName,
                'base64_pdf' => $pdfBase64,
                'lang' => 'pt-br',
                'sandbox' => config('zapsign.sandbox', false),
                'signers' => $this->formatarSignatarios($signatarios),
            ];

            $webhookUrl = config('zapsign.webhook_url');
            if ($webhookUrl) {
                $payload['webhook_url'] = $webhookUrl;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])
            ->withOptions([
                'verify' => false, // Desabilitar verificação SSL (apenas para desenvolvimento)
            ])
            ->post($this->apiUrl . '/docs/', $payload);

            if ($response->successful()) {
                Log::info('Documento criado no ZapSign via Base64', ['response' => $response->json()]);
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Erro ao criar documento no ZapSign via Base64', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao criar documento: ' . $response->body(),
            ];

        } catch (Exception $e) {
            Log::error('Exceção ao criar documento no ZapSign via Base64', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Formatar array de signatários para o formato esperado pela API ZapSign
     */
    private function formatarSignatarios(array $signatarios)
    {
        return array_map(function ($signatario) {
            return [
                'name' => $signatario['name'],
                'email' => $signatario['email'] ?? null,
                'phone_country' => $signatario['phone_country'] ?? '55',
                'phone_number' => $signatario['phone_number'] ?? null,
                'auth_mode' => $signatario['auth_mode'] ?? 'assinaturaTela',
                'send_automatic_email' => $signatario['send_automatic_email'] ?? true,
                'send_automatic_whatsapp' => $signatario['send_automatic_whatsapp'] ?? false,
                'lock_email' => $signatario['lock_email'] ?? false,
                'lock_phone' => $signatario['lock_phone'] ?? false,
            ];
        }, $signatarios);
    }

    /**
     * Detalhar documento específico
     * 
     * @param string $docToken Token do documento
     * @return array
     */
    public function detalharDocumento($docToken)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])
            ->withOptions(['verify' => false])
            ->get($this->apiUrl . '/docs/' . $docToken . '/');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Erro ao detalhar documento: ' . $response->body(),
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Listar documentos
     * 
     * @return array
     */
    public function listarDocumentos()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])
            ->withOptions(['verify' => false])
            ->get($this->apiUrl . '/docs/');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Erro ao listar documentos: ' . $response->body(),
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Excluir documento
     * 
     * @param string $docToken Token do documento
     * @return array
     */
    public function excluirDocumento($docToken)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])
            ->withOptions(['verify' => false])
            ->delete($this->apiUrl . '/docs/' . $docToken . '/');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Documento excluído com sucesso',
                ];
            }

            return [
                'success' => false,
                'message' => 'Erro ao excluir documento: ' . $response->body(),
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Posicionar assinaturas no documento (place-signatures)
     * 
     * @param string $docToken Token do documento
     * @param array $rubricas Array de objetos com page, relative_position_bottom, relative_position_left, etc
     * @return array
     */
    public function posicionarAssinaturas($docToken, array $rubricas)
    {
        try {
            $payload = [
                'rubricas' => $rubricas
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])
            ->withOptions(['verify' => false])
            ->post($this->apiUrl . '/docs/' . $docToken . '/place-signatures/', $payload);

            if ($response->successful()) {
                Log::info('Assinaturas posicionadas no ZapSign', ['doc_token' => $docToken]);
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Erro ao posicionar assinaturas no ZapSign', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao posicionar assinaturas: ' . $response->body(),
            ];

        } catch (Exception $e) {
            Log::error('Exceção ao posicionar assinaturas no ZapSign', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
            ];
        }
    }

}