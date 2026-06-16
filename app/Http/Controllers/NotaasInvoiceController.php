<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotaasInvoice;
use App\Models\Empresa;
use App\Models\FolhaPagamento;
use App\Services\NotaasService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class NotaasInvoiceController extends Controller
{
    /**
     * Exibe o painel de notas fiscais
     */
    public function index(Request $request)
    {
        $query = NotaasInvoice::with(['folhaPagamento', 'empresa']);

        // Filtros
        if ($request->filled('tomador')) {
            $query->where('tomador_nome', 'like', '%' . $request->input('tomador') . '%');
        }
        if ($request->filled('cnpj')) {
            $cnpjClean = preg_replace('/\D/', '', $request->input('cnpj'));
            $query->where('tomador_cnpj', 'like', '%' . $cnpjClean . '%');
        }
        if ($request->filled('status')) {
            $query->where('notaas_status', $request->input('status'));
        }
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->input('data_inicio'));
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->input('data_fim'));
        }

        $invoices = $query->orderByDesc('id')->paginate(25)->appends($request->query());

        // Carrega todas as empresas ordenadas para auto-preenchimento
        $empresas = Empresa::with('cidade.estado')->orderBy('nome_empresa', 'asc')->get();

        $statuses = [
            'queued' => 'Na Fila',
            'processing' => 'Processando',
            'issued' => 'Emitida',
            'error' => 'Erro',
            'cancelled' => 'Cancelada'
        ];

        return view('notaas.index', compact('invoices', 'empresas', 'statuses'));
    }

    /**
     * Emite NFS-e atrelada a uma Folha de Pagamento
     */
    public function emitirNfseNotaas(Request $request, $id_folha_pagamento)
    {
        $folha = FolhaPagamento::findOrFail($id_folha_pagamento);
        $empresa = $folha->empresa;

        if (!$empresa) {
            return redirect()->back()->with('error', 'Unidade Concedente (empresa) não encontrada para esta folha.');
        }

        // Validar dados da empresa tomadora exigidos pela SEFAZ / Notaas
        $errors = [];
        if (empty($empresa->numero_cnpj)) {
            $errors[] = 'CNPJ da empresa não cadastrado.';
        }
        if (empty($empresa->nome_empresa)) {
            $errors[] = 'Razão Social da empresa não cadastrada.';
        }
        if (empty($empresa->endereco)) {
            $errors[] = 'Endereço da empresa não cadastrado.';
        }

        $cidade = $empresa->cidade;
        if (!$cidade) {
            $errors[] = 'Cidade/UF da empresa não vinculada no cadastro.';
        } else {
            if (empty($cidade->nm_cidade)) {
                $errors[] = 'Nome da cidade da empresa não preenchido.';
            }
            if (!$cidade->estado || empty($cidade->estado->uf_estado)) {
                $errors[] = 'Estado (UF) da empresa não preenchido.';
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->with('error', 'Não foi possível emitir a NFS-e devido a pendências no cadastro da empresa: ' . implode(' ', $errors));
        }

        // Validar parâmetros vindos do formulário/modal
        $request->validate([
            'valor_nota' => 'required|numeric|min:0.01',
            'descricao_servico' => 'required|string|min:5|max:2000',
            'codigo_servico' => 'nullable|string',
            'aliquota_iss' => 'required|numeric|min:0|max:100',
            'iss_retido' => 'nullable|boolean',
        ]);

        $cnpjLimpo = preg_replace('/\D/', '', $empresa->numero_cnpj);
        $cepLimpo = preg_replace('/\D/', '', $empresa->numero_cep ?? '');

        // Montagem do payload
        $tomador = [
            'nome' => $empresa->nome_empresa,
            'cnpj' => $cnpjLimpo,
        ];

        if (!empty($empresa->email)) {
            $tomador['email'] = $empresa->email;
        }
        if (!empty($empresa->numero_telefone)) {
            $tomador['telefone'] = preg_replace('/\D/', '', $empresa->numero_telefone);
        }

        $tomador['endereco'] = [
            'logradouro' => $empresa->endereco,
            'numero' => $empresa->numero_endereco ?: 'S/N',
            'bairro' => $empresa->bairro ?: 'Centro',
            'cidade' => $cidade->nm_cidade,
            'uf' => $cidade->estado->uf_estado,
            'cep' => $cepLimpo ?: '00000000',
        ];

        if (!empty($empresa->complemento_endereco)) {
            $tomador['endereco']['complemento'] = $empresa->complemento_endereco;
        }

        $servico = [
            'descricao' => $request->input('descricao_servico'),
        ];

        if ($request->filled('codigo_servico')) {
            $servico['codigo'] = preg_replace('/\D/', '', $request->input('codigo_servico'));
        }

        $valores = [
            'total' => (float) $request->input('valor_nota'),
        ];

        if ($request->filled('aliquota_iss')) {
            $valores['aliquotaIss'] = (float) $request->input('aliquota_iss');
        }

        if ($request->boolean('iss_retido', false)) {
            $valores['issRetido'] = true;
        }

        $payload = [
            'tomador' => $tomador,
            'servico' => $servico,
            'valores' => $valores,
            'competencia' => sprintf('%04d-%02d', $folha->ano_referencia, $folha->mes_referencia),
            'referencia' => 'FOLHA-' . $folha->id_folha_pagamento . '-' . uniqid(),
        ];

        $service = new NotaasService();

        try {
            $res = $service->emitirNfse($payload);

            // Grava na tabela tb_notas_fiscais
            NotaasInvoice::updateOrCreate(
                ['fk_id_folha' => $folha->id_folha_pagamento],
                [
                    'fk_id_empresa' => $folha->fk_id_empresa,
                    'notaas_invoice_id' => $res['invoiceId'] ?? null,
                    'notaas_status' => $res['status'] ?? 'queued',
                    'notaas_error_message' => null,
                    'tomador_nome' => $empresa->nome_empresa,
                    'tomador_cnpj' => $cnpjLimpo,
                    'tomador_email' => $empresa->email,
                    'tomador_telefone' => preg_replace('/\D/', '', $empresa->numero_telefone),
                    'tomador_endereco' => $empresa->endereco,
                    'tomador_numero' => $empresa->numero_endereco ?: 'S/N',
                    'tomador_bairro' => $empresa->bairro ?: 'Centro',
                    'tomador_cidade' => $cidade->nm_cidade,
                    'tomador_uf' => $cidade->estado->uf_estado,
                    'tomador_cep' => $cepLimpo,
                    'valor' => (float)$request->input('valor_nota'),
                    'descricao' => $request->input('descricao_servico'),
                    'codigo_servico' => $request->filled('codigo_servico') ? preg_replace('/\D/', '', $request->input('codigo_servico')) : null,
                    'aliquota_iss' => (float)$request->input('aliquota_iss'),
                    'iss_retido' => $request->boolean('iss_retido', false),
                    'competencia' => sprintf('%04d-%02d', $folha->ano_referencia, $folha->mes_referencia),
                    'referencia' => $payload['referencia'],
                ]
            );

            // Mantém os campos na tb_folhas_pagamento para compatibilidade retroativa
            $folha->update([
                'notaas_invoice_id' => $res['invoiceId'] ?? null,
                'notaas_status' => $res['status'] ?? 'queued',
                'notaas_error_message' => null,
            ]);

            return redirect()->route('folhas.show', $folha->id_folha_pagamento)
                ->with('success', 'Nota Fiscal enviada para fila do Notaas com sucesso! Clique em "Sincronizar Status" para obter o PDF e XML.');

        } catch (\Exception $e) {
            Log::error('Erro ao emitir NFS-e Notaas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro retornado pela API Notaas: ' . $e->getMessage());
        }
    }

    /**
     * Emite uma NFS-e 100% personalizada (Avulsa)
     */
    public function storeCustom(Request $request)
    {
        $request->validate([
            'tomador_nome' => 'required|string|min:3|max:255',
            'tomador_cnpj' => 'required|string',
            'tomador_email' => 'nullable|email|max:255',
            'tomador_telefone' => 'nullable|string',
            'tomador_endereco' => 'required|string|max:255',
            'tomador_numero' => 'required|string|max:20',
            'tomador_bairro' => 'required|string|max:100',
            'tomador_cidade' => 'required|string|max:100',
            'tomador_uf' => 'required|string|size:2',
            'tomador_cep' => 'required|string',
            'valor_nota' => 'required|numeric|min:0.01',
            'descricao_servico' => 'required|string|min:5|max:2000',
            'codigo_servico' => 'nullable|string',
            'aliquota_iss' => 'required|numeric|min:0|max:100',
            'iss_retido' => 'nullable|boolean',
            'fk_id_empresa' => 'nullable|integer|exists:tb_empresas,id_empresa',
        ]);

        $cnpjLimpo = preg_replace('/\D/', '', $request->input('tomador_cnpj'));
        $cepLimpo = preg_replace('/\D/', '', $request->input('tomador_cep'));
        $telefoneLimpo = preg_replace('/\D/', '', $request->input('tomador_telefone') ?? '');

        // Montagem do payload para a API
        $tomador = [
            'nome' => $request->input('tomador_nome'),
            'cnpj' => $cnpjLimpo,
        ];

        if ($request->filled('tomador_email')) {
            $tomador['email'] = $request->input('tomador_email');
        }
        if (!empty($telefoneLimpo)) {
            $tomador['telefone'] = $telefoneLimpo;
        }

        $tomador['endereco'] = [
            'logradouro' => $request->input('tomador_endereco'),
            'numero' => $request->input('tomador_numero'),
            'bairro' => $request->input('tomador_bairro'),
            'cidade' => $request->input('tomador_cidade'),
            'uf' => strtoupper($request->input('tomador_uf')),
            'cep' => $cepLimpo,
        ];

        $servico = [
            'descricao' => $request->input('descricao_servico'),
        ];

        if ($request->filled('codigo_servico')) {
            $servico['codigo'] = preg_replace('/\D/', '', $request->input('codigo_servico'));
        }

        $valores = [
            'total' => (float) $request->input('valor_nota'),
            'aliquotaIss' => (float) $request->input('aliquota_iss'),
        ];

        if ($request->boolean('iss_retido', false)) {
            $valores['issRetido'] = true;
        }

        $referencia = 'AVULSA-' . uniqid();

        $payload = [
            'tomador' => $tomador,
            'servico' => $servico,
            'valores' => $valores,
            'competencia' => date('Y-m'),
            'referencia' => $referencia,
        ];

        $service = new NotaasService();

        try {
            $res = $service->emitirNfse($payload);

            NotaasInvoice::create([
                'fk_id_empresa' => $request->input('fk_id_empresa'),
                'notaas_invoice_id' => $res['invoiceId'] ?? null,
                'notaas_status' => $res['status'] ?? 'queued',
                'notaas_error_message' => null,
                'tomador_nome' => $request->input('tomador_nome'),
                'tomador_cnpj' => $cnpjLimpo,
                'tomador_email' => $request->input('tomador_email'),
                'tomador_telefone' => $telefoneLimpo ?: null,
                'tomador_endereco' => $request->input('tomador_endereco'),
                'tomador_numero' => $request->input('tomador_numero'),
                'tomador_bairro' => $request->input('tomador_bairro'),
                'tomador_cidade' => $request->input('tomador_cidade'),
                'tomador_uf' => strtoupper($request->input('tomador_uf')),
                'tomador_cep' => $cepLimpo,
                'valor' => (float)$request->input('valor_nota'),
                'descricao' => $request->input('descricao_servico'),
                'codigo_servico' => $request->filled('codigo_servico') ? preg_replace('/\D/', '', $request->input('codigo_servico')) : null,
                'aliquota_iss' => (float)$request->input('aliquota_iss'),
                'iss_retido' => $request->boolean('iss_retido', false),
                'competencia' => date('Y-m'),
                'referencia' => $referencia,
            ]);

            return redirect()->route('notaas.index')
                ->with('success', 'Nota Fiscal Avulsa transmitida com sucesso para o Notaas!');

        } catch (\Exception $e) {
            Log::error('Erro ao emitir NFS-e Avulsa Notaas: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erro retornado pela API Notaas: ' . $e->getMessage());
        }
    }

    /**
     * Consulta o status da nota na Notaas e atualiza no banco
     */
    public function sincronizarStatusNfse($id)
    {
        $nota = NotaasInvoice::findOrFail($id);

        if (empty($nota->notaas_invoice_id)) {
            return redirect()->back()->with('error', 'Não há código de nota fiscal para esta fatura.');
        }

        $service = new NotaasService();

        try {
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
                $dataToUpdate['notaas_error_message'] = $res['errorMessage'] ?? 'Erro desconhecido retornado pela SEFAZ.';
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

            return redirect()->back()
                ->with('success', 'Status da Nota Fiscal sincronizado com sucesso! (Status: ' . ($res['status'] ?? 'N/A') . ')');

        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar status NFS-e Notaas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao sincronizar com Notaas: ' . $e->getMessage());
        }
    }

    /**
     * Solicita o cancelamento da NFS-e
     */
    public function cancelarNfseNotaas(Request $request, $id)
    {
        $nota = NotaasInvoice::findOrFail($id);

        if (empty($nota->notaas_invoice_id)) {
            return redirect()->back()->with('error', 'Esta nota fiscal não possui código de transmissão vinculada.');
        }

        $request->validate([
            'motivo_cancelamento' => 'required|string|min:5|max:255',
        ]);

        $service = new NotaasService();

        try {
            $res = $service->cancelarNfse($nota->notaas_invoice_id, $request->input('motivo_cancelamento'));

            $nota->update([
                'notaas_status' => $res['status'] ?? 'cancelled',
                'notaas_error_message' => null,
            ]);

            // Atualiza na folha de pagamento atrelada se houver
            if ($nota->fk_id_folha) {
                $folha = FolhaPagamento::find($nota->fk_id_folha);
                if ($folha) {
                    $folha->update([
                        'notaas_status' => $nota->notaas_status,
                        'notaas_error_message' => null,
                    ]);
                }
            }

            return redirect()->back()
                ->with('success', 'Pedido de cancelamento enviado com sucesso! Sincronize o status para confirmar.');

        } catch (\Exception $e) {
            Log::error('Erro ao cancelar NFS-e Notaas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao solicitar cancelamento: ' . $e->getMessage());
        }
    }
}
