<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Termo;
use App\Models\Rescisao;
use App\Models\AlteracaoTermo;
use App\Models\ZapSignWebhookLog;
use App\Services\ZapSignService;

class ZapSignWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Validate secret header (configure this same header in ZapSign webhook settings)
        $expectedHeader = config('zapsign.webhook_header', 'Authorization');
        $expectedSecret = config('zapsign.webhook_secret');

        if ($expectedSecret) {
            $received = $request->header($expectedHeader);
            // Accept either the raw secret or Bearer {secret}
            $isValid = $received === $expectedSecret || $received === ('Bearer ' . $expectedSecret);
            if (!$isValid) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized webhook'], 401);
            }
        }

        $payload = $request->all();

        // Extract document token and status as robustly as possible
        $docToken = data_get($payload, 'document.token')
            ?? data_get($payload, 'document_token')
            ?? data_get($payload, 'token')
            ?? data_get($payload, 'doc_token');

        $status = data_get($payload, 'document.status')
            ?? data_get($payload, 'status')
            ?? data_get($payload, 'event');
        
        // Se recebemos um objeto 'document' completo, extrair status dele também
        if (isset($payload['document']) && is_array($payload['document'])) {
            $status = $payload['document']['status'] ?? $status;
            if (!$docToken && isset($payload['document']['token'])) {
                $docToken = $payload['document']['token'];
            }
        }

        // Persist webhook log for audit
        try {
            ZapSignWebhookLog::create([
                'document_token' => $docToken,
                'status' => $status,
                'payload' => $payload,
                'headers' => $request->headers->all(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('ZapSign webhook log insert failed: ' . $e->getMessage());
        }

        if ($docToken) {
            // Tentar identificar o tipo de documento (termo, rescisão ou alteração)
            $statusClean = strtolower(trim($status ?? ''));
            $statusFinal = $statusClean ?: 'enviado';
            
            Log::info("Webhook ZapSign: Documento processado", [
                'token' => $docToken,
                'status_recebido' => $status,
                'status_final' => $statusFinal,
            ]);
            
            $termo = Termo::where('zapsign_doc_token', $docToken)->first();
            if ($termo) {
                $termo->zapsign_status = $statusFinal;
                $termo->save();
                Log::info("Webhook ZapSign: Status '{$statusFinal}' atualizado para Termo ID {$termo->id_termo}");
                
                // Atualizar status consultando a API do ZapSign para garantir precisão
                $this->atualizarStatusViaAPI($termo, 'termo');
            } else {
                // Verificar se é uma rescisão
                $rescisao = Rescisao::where('zapsign_doc_token', $docToken)->first();
                if ($rescisao) {
                    $rescisao->zapsign_status = $statusFinal;
                    $rescisao->save();
                    Log::info("Webhook ZapSign: Status '{$statusFinal}' atualizado para Rescisão ID {$rescisao->id_rescisao}");
                    
                    // Atualizar status consultando a API do ZapSign para garantir precisão
                    $this->atualizarStatusViaAPI($rescisao, 'rescisao');
                } else {
                    // Verificar se é uma alteração
                    $alteracao = AlteracaoTermo::where('zapsign_doc_token', $docToken)->first();
                    if ($alteracao) {
                        $alteracao->zapsign_status = $statusFinal;
                        $alteracao->save();
                        Log::info("Webhook ZapSign: Status '{$statusFinal}' atualizado para Alteração ID {$alteracao->id_alteracao}");
                        
                        // Atualizar status consultando a API do ZapSign para garantir precisão
                        $this->atualizarStatusViaAPI($alteracao, 'alteracao');
                    } else {
                        Log::warning("Webhook ZapSign: Documento não encontrado para token {$docToken}");
                    }
                }
            }
        }

        // Return 200 to avoid retries
        return response()->json(['ok' => true]);
    }

    /**
     * Atualizar status do documento consultando a API do ZapSign
     * Isso garante que sempre temos o status mais recente
     */
    private function atualizarStatusViaAPI($model, $tipo)
    {
        try {
            if (!$model->zapsign_doc_token) {
                return;
            }

            $zapSignService = new ZapSignService();
            $resultado = $zapSignService->detalharDocumento($model->zapsign_doc_token);

            if ($resultado['success']) {
                $data = $resultado['data'];
                $statusAPI = strtolower($data['status'] ?? 'desconhecido');

                // Atualizar apenas se o status da API for diferente
                if ($statusAPI && $statusAPI !== strtolower($model->zapsign_status ?? '')) {
                    $model->zapsign_status = $statusAPI;
                    $model->save();
                    Log::info("Webhook ZapSign: Status atualizado via API para $tipo", [
                        'id' => $model->getKey(),
                        'tipo' => $tipo,
                        'status_api' => $statusAPI,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning("Erro ao atualizar status via API: " . $e->getMessage());
        }
    }
}
