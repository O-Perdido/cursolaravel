<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\NotaasService;
use Illuminate\Support\Facades\Http;

class NotaasIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Define credenciais mockadas no config
        config(['services.notaas.api_key' => 'mock-api-key']);
        config(['services.notaas.api_url' => 'https://mock.platform.notaas.com.br/api/v1']);
    }

    /** @test */
    public function it_can_enqueue_nfse_successfully()
    {
        Http::fake([
            'https://mock.platform.notaas.com.br/api/v1/emitir' => Http::response([
                'queued' => true,
                'invoiceId' => 'inv_test123',
                'status' => 'queued',
                'pollUrl' => '/api/v1/invoices/inv_test123/status'
            ], 202)
        ]);

        $service = new NotaasService();
        $payload = [
            'tomador' => ['nome' => 'Empresa Teste', 'cnpj' => '12345678000195'],
            'servico' => ['descricao' => 'Serviços de agenciamento'],
            'valores' => ['total' => 150.00]
        ];

        $response = $service->emitirNfse($payload);

        $this->assertTrue($response['queued']);
        $this->assertEquals('inv_test123', $response['invoiceId']);
        $this->assertEquals('queued', $response['status']);

        Http::assertSent(function ($request) use ($payload) {
            return $request->url() === 'https://mock.platform.notaas.com.br/api/v1/emitir'
                && $request->header('x-api-key')[0] === 'mock-api-key'
                && $request['valores']['total'] === 150.00;
        });
    }

    /** @test */
    public function it_can_query_invoice_status()
    {
        Http::fake([
            'https://mock.platform.notaas.com.br/api/v1/invoices/inv_test123/status' => Http::response([
                'status' => 'issued',
                'numeroNfe' => '1234',
                'pdfUrl' => 'https://cdn.notaas.com.br/inv_test123.pdf',
                'xmlUrl' => 'https://cdn.notaas.com.br/inv_test123.xml',
                'emittedAt' => '2026-06-16T17:00:00Z'
            ], 200)
        ]);

        $service = new NotaasService();
        $response = $service->consultarStatus('inv_test123');

        $this->assertEquals('issued', $response['status']);
        $this->assertEquals('1234', $response['numeroNfe']);
        $this->assertEquals('https://cdn.notaas.com.br/inv_test123.pdf', $response['pdfUrl']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://mock.platform.notaas.com.br/api/v1/invoices/inv_test123/status'
                && $request->header('x-api-key')[0] === 'mock-api-key';
        });
    }

    /** @test */
    public function it_can_request_invoice_cancellation()
    {
        Http::fake([
            'https://mock.platform.notaas.com.br/api/v1/cancelar' => Http::response([
                'status' => 'cancelled'
            ], 200)
        ]);

        $service = new NotaasService();
        $response = $service->cancelarNfse('inv_test123', 'Erro na digitação do valor.');

        $this->assertEquals('cancelled', $response['status']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://mock.platform.notaas.com.br/api/v1/cancelar'
                && $request->header('x-api-key')[0] === 'mock-api-key'
                && $request['invoiceId'] === 'inv_test123'
                && $request['motivo'] === 'Erro na digitação do valor.';
        });
    }
}
