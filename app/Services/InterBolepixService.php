<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InterBolepixService
{
    public function integrationEnabled(): bool
    {
        return (bool) config('inter_bolepix.enabled', false);
    }

    public function emitirCobranca(array $payload): array
    {
        try {
            $token = $this->oauthToken((string) config('inter_bolepix.scope_write', 'boleto-cobranca.write'));

            $response = $this->baseRequest($token)
                ->post($this->chargeUrl(), $payload);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'status' => $response->status(),
                    'message' => 'Falha ao emitir cobrança no Inter.',
                    'body' => $response->json() ?: $response->body(),
                ];
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];
        } catch (\Throwable $exception) {
            Log::error('Falha na emissao de cobranca Inter', [
                'message' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro de comunicação ao emitir cobrança no Banco Inter.',
            ];
        }
    }

    public function recuperarCobranca(string $codigoSolicitacao): array
    {
        try {
            $token = $this->oauthToken((string) config('inter_bolepix.scope_read', 'boleto-cobranca.read'));

            $response = $this->baseRequest($token)
                ->get($this->chargeUrl() . '/' . $codigoSolicitacao);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'status' => $response->status(),
                    'message' => 'Falha ao consultar cobrança no Inter.',
                    'body' => $response->json() ?: $response->body(),
                ];
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];
        } catch (\Throwable $exception) {
            Log::error('Falha na consulta de cobranca Inter', [
                'message' => $exception->getMessage(),
                'codigo_solicitacao' => $codigoSolicitacao,
            ]);

            return [
                'success' => false,
                'message' => 'Erro de comunicação ao consultar cobrança no Banco Inter.',
            ];
        }
    }

    public function recuperarPdfCobranca(string $codigoSolicitacao): array
    {
        try {
            $token = $this->oauthToken((string) config('inter_bolepix.scope_read', 'boleto-cobranca.read'));

            $response = $this->baseRequest($token)
                ->get($this->chargeUrl() . '/' . $codigoSolicitacao . '/pdf');

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'status' => $response->status(),
                    'message' => 'Falha ao recuperar PDF da cobrança no Inter.',
                    'body' => $response->json() ?: $response->body(),
                ];
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];
        } catch (\Throwable $exception) {
            Log::error('Falha na recuperacao de PDF de cobranca Inter', [
                'message' => $exception->getMessage(),
                'codigo_solicitacao' => $codigoSolicitacao,
            ]);

            return [
                'success' => false,
                'message' => 'Erro de comunicação ao recuperar PDF da cobrança no Banco Inter.',
            ];
        }
    }

    private function oauthToken(string $scope): string
    {
        $cacheKey = 'inter_bolepix_token_' . md5($scope);

        return Cache::remember($cacheKey, now()->addMinutes(55), function () use ($scope) {
            $baseUrl = rtrim((string) config('inter_bolepix.base_url'), '/');
            $tokenPath = (string) config('inter_bolepix.oauth_token_path', '/oauth/v2/token');

            $response = $this->baseRequestWithoutAuth()
                ->asForm()
                ->post($baseUrl . $tokenPath, [
                    'grant_type' => 'client_credentials',
                    'client_id' => (string) config('inter_bolepix.client_id'),
                    'client_secret' => (string) config('inter_bolepix.client_secret'),
                    'scope' => $scope,
                ]);

            if (!$response->successful()) {
                throw new \RuntimeException('Falha ao obter token OAuth Inter: ' . $response->body());
            }

            $json = $response->json();
            $token = (string) ($json['access_token'] ?? '');

            if ($token === '') {
                throw new \RuntimeException('Token OAuth Inter não retornado.');
            }

            return $token;
        });
    }

    private function baseRequest(string $token): PendingRequest
    {
        return $this->baseRequestWithoutAuth()
            ->withToken($token)
            ->withHeader('x-conta-corrente', (string) config('inter_bolepix.account_number'));
    }

    private function baseRequestWithoutAuth(): PendingRequest
    {
        $request = Http::acceptJson()
            ->timeout((int) config('inter_bolepix.timeout', 30));

        $certPath = $this->resolvePath((string) config('inter_bolepix.cert_path', ''));
        $keyPath = $this->resolvePath((string) config('inter_bolepix.key_path', ''));

        $options = [
            'verify' => (bool) config('inter_bolepix.verify_ssl', true),
        ];

        if ($certPath !== null) {
            $options['cert'] = $certPath;
        }

        if ($keyPath !== null) {
            $options['ssl_key'] = $keyPath;
        }

        return $request->withOptions($options);
    }

    private function chargeUrl(): string
    {
        $baseUrl = rtrim((string) config('inter_bolepix.base_url'), '/');
        $path = (string) config('inter_bolepix.charge_path', '/cobranca/v3/cobrancas');

        return $baseUrl . $path;
    }

    private function resolvePath(string $path): ?string
    {
        $trimmed = trim($path);

        if ($trimmed === '') {
            return null;
        }

        if (str_starts_with($trimmed, '/') || preg_match('/^[A-Za-z]:\\\\/', $trimmed)) {
            return $trimmed;
        }

        return base_path($trimmed);
    }
}
