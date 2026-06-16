<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\NotaasInvoice;
use App\Models\FolhaPagamento;
use App\Services\NotaasService;

class NotaasWebhookController extends Controller
{
    /**
     * Recebe e processa os webhooks da Notaas
     */
    public function handle(Request $request)
    {
        Log::info('NotaasWebhook: Recebendo notificação', [
            'headers' => $request->headers->all(),
            'payload' => $request->all()
        ]);

        // Validação da Assinatura (Opcional - se configurado)
        $secret = config('services.notaas.webhook_secret');
        if ($secret) {
            $signature = $request->header('X-Notaas-Signature');
            $payloadRaw = $request->getContent();
            $computed = hash_hmac('sha256', $payloadRaw, $secret);

            if (!hash_equals(strtolower($signature ?? ''), strtolower($computed))) {
                Log::warning('NotaasWebhook: Assinatura inválida', [
                    'recebida' => $signature,
                    'computada' => $computed
                ]);
                return response()->json(['ok' => false, 'message' => 'Invalid signature'], 401);
            }
        }

        // Extrai invoiceId do payload
        // O formato Notaas usa data.invoiceId para o objeto do evento
        $invoiceId = data_get($request, 'data.invoiceId') 
            ?? data_get($request, 'invoiceId')
            ?? data_get($request, 'data.id');

        if (!$invoiceId) {
            Log::warning('NotaasWebhook: invoiceId não encontrado no payload');
            return response()->json(['ok' => false, 'message' => 'invoiceId not found'], 400);
        }

        $nota = NotaasInvoice::where('notaas_invoice_id', $invoiceId)->first();
        if (!$nota) {
            Log::warning("NotaasWebhook: Nota fiscal não encontrada no banco de dados para o ID: {$invoiceId}");
            return response()->json(['ok' => false, 'message' => 'Invoice not found'], 404);
        }

        // Sincroniza via API oficial para garantir a consistência e segurança dos dados
        try {
            $service = new NotaasService();
            $res = $service->consultarStatus($nota->notaas_invoice_id);

            $dataToUpdate = [
                'notaas_status' => $res['status'] ?? $nota->notaas_status,
            ];

            if (isset($res['pdfUrl'])) {
                $dataToUpdate['notaas_pdf_url'] = $res['pdfUrl'];
            }
            if (isset($res['xmlUrl'])) {
                $dataToUpdate['notaas_xml_url'] = $res['xmlUrl'];
            }
            if (isset($res['emittedAt'])) {
                $dataToUpdate['notaas_emitted_at'] = \Carbon\Carbon::parse($res['emittedAt']);
            }

            if (($res['status'] ?? '') === 'error') {
                $dataToUpdate['notaas_error_message'] = $res['errorMessage'] ?? 'Erro retornado pela SEFAZ.';
            } else {
                $dataToUpdate['notaas_error_message'] = null;
            }

            $nota->update($dataToUpdate);

            // Atualiza na folha de pagamento atrelada se houver
            if ($nota->fk_id_folha) {
                $folha = FolhaPagamento::find($nota->fk_id_folha);
                if ($folha) {
                    $folha->update([
                        'notaas_status' => $nota->notaas_status,
                        'notaas_pdf_url' => $nota->notaas_pdf_url,
                        'notaas_xml_url' => $nota->notaas_xml_url,
                        'notaas_error_message' => $nota->notaas_error_message,
                        'notaas_emitted_at' => $nota->notaas_emitted_at,
                    ]);
                }
            }

            Log::info("NotaasWebhook: Nota fiscal ID {$nota->id} sincronizada para status '{$nota->notaas_status}'");

            return response()->json(['ok' => true, 'status' => $nota->notaas_status]);

        } catch (\Exception $e) {
            Log::error('NotaasWebhook: Erro ao sincronizar status: ' . $e->getMessage());
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
