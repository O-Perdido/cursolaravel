<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Termo;
use App\Models\Rescisao;
use App\Models\AlteracaoTermo;
use App\Models\ZapSignWebhookLog;

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
            $statusClean = trim($status ?? '');
            $statusFinal = $statusClean ?: 'desconhecido';
            
            $termo = Termo::where('zapsign_doc_token', $docToken)->first();
            if ($termo) {
                $termo->zapsign_status = $statusFinal;
                $termo->save();
                Log::info("Webhook ZapSign: Status '{$statusFinal}' atualizado para Termo ID {$termo->id_termo}");
            } else {
                // Verificar se é uma rescisão
                $rescisao = Rescisao::where('zapsign_doc_token', $docToken)->first();
                if ($rescisao) {
                    $rescisao->zapsign_status = $statusFinal;
                    $rescisao->save();
                    Log::info("Webhook ZapSign: Status '{$statusFinal}' atualizado para Rescisão ID {$rescisao->id_rescisao}");
                } else {
                    // Verificar se é uma alteração
                    $alteracao = AlteracaoTermo::where('zapsign_doc_token', $docToken)->first();
                    if ($alteracao) {
                        $alteracao->zapsign_status = $statusFinal;
                        $alteracao->save();
                        Log::info("Webhook ZapSign: Status '{$statusFinal}' atualizado para Alteração ID {$alteracao->id_alteracao}");
                    } else {
                        Log::warning("Webhook ZapSign: Documento não encontrado para token {$docToken}");
                    }
                }
            }
        }

        // Return 200 to avoid retries
        return response()->json(['ok' => true]);
    }
}
 
