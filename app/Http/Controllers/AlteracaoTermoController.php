<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Termo;
use App\Models\Supervisor;
use App\Models\AlteracaoTermo;
use App\Models\Ebcp;
use App\Models\Local;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ZapSignService;

class AlteracaoTermoController extends Controller
{
    public function index($id)
    {
        $termo = Termo::find($id);
        $alteracoesTermo = AlteracaoTermo::all();
        return view('termos.alteracoes.index', compact('alteracoesTermo', 'termo'));
    }

    public function create($id)
    {
        $termo = Termo::all();
        $supervisores = Supervisor::orderBy('nome_supervisor', 'asc')->get();
        $id_termo = $id;
        $termo_selecionado = Termo::find($id);

        // Buscar locais da empresa do termo
        $locais = [];
        if ($termo_selecionado && $termo_selecionado->fk_id_empresa) {
            $locais = Local::where('fk_id_empresa', $termo_selecionado->fk_id_empresa)
                ->orderBy('descricao')
                ->get();
        }

        return view('termos.alteracoes.create')->with([
            'id_termo' => $id_termo,
            'termo' => $termo,
            'supervisores' => $supervisores,
            'termo_selecionado' => $termo_selecionado,
            'locais' => $locais
        ]);
    }

    public function store(Request $request, $id)
    {
        $validatedData = $request->validate([
            'id_alteracao' => 'nullable|integer',
            'fk_id_termo' => 'nullable|integer',
            'fk_id_supervisor' => 'nullable|integer',
            'desc_atividades_alteracao' => 'nullable|string',
            'nome_orientador_alteracao' => 'nullable|string',
            'cargo_orientador_alteracao' => 'nullable|string',
            'data_fim_estagio_alteracao' => 'nullable|date',
            'data_alteracao' => 'nullable|date',
            'horario_alteracao' => 'nullable|string',
            'valor_bolsa_alteracao' => 'nullable',
            'auxilio_transporte_alteracao' => 'nullable',
            'fk_id_local' => 'nullable|integer',
            'lotacao_alteracao' => 'nullable|string|max:150',
            'descricao' => 'nullable|string',
        ]);

        $validatedData['data_alteracao'] = now()->toDateString();
        $validatedData['fk_id_termo'] = $id;

        // Fazer o update na tabela original
        $termo = Termo::findOrFail($id);

        // Armazenar os valores antigos
        $validatedData['old_fk_id_supervisor'] = $termo->fk_id_supervisor;
        $validatedData['old_nome_orientador'] = $termo->nome_orientador;
        $validatedData['old_cargo_orientador'] = $termo->cargo_orientador;
        $validatedData['old_data_fim_estagio'] = $termo->data_fim_estagio;
        $validatedData['old_horario'] = $termo->horario;
        $validatedData['old_valor_bolsa'] = $termo->valor_bolsa;
        $validatedData['old_auxilio_transporte'] = $termo->auxilio_transporte;
        $validatedData['old_desc_atividades'] = $termo->desc_atividades;
        $validatedData['old_fk_id_local'] = $termo->fk_id_local;
        $validatedData['old_lotacao'] = $termo->lotacao;


        // Atualiza os campos alterados, mantendo os valores antigos caso não venham no request
        $updateData = [
            'fk_id_supervisor' => $validatedData['fk_id_supervisor'] ?? $termo->fk_id_supervisor,
            'nome_orientador' => $validatedData['nome_orientador_alteracao'] ?? $termo->nome_orientador,
            'cargo_orientador' => $validatedData['cargo_orientador_alteracao'] ?? $termo->cargo_orientador,
            'data_fim_estagio' => $validatedData['data_fim_estagio_alteracao'] ?? $termo->data_fim_estagio,
            'valor_bolsa' => $validatedData['valor_bolsa_alteracao'] ?? $termo->valor_bolsa,
            'auxilio_transporte' => $validatedData['auxilio_transporte_alteracao'] ?? $termo->auxilio_transporte,
            'horario' => $validatedData['horario_alteracao'] ?? $termo->horario,
            'desc_atividades' => $validatedData['desc_atividades_alteracao'] ?? $termo->desc_atividades,
            'fk_id_local' => $validatedData['fk_id_local'] ?? $termo->fk_id_local,
            'lotacao' => $validatedData['lotacao_alteracao'] ?? $termo->lotacao,
        ];

        // Formatar os valores monetários campos valor_bolsa e auxilio_transporte
        //Teste se o valor_bolsa não é nulo
        if (isset($updateData['valor_bolsa']) && $updateData['valor_bolsa'] != null) {
            $updateData['valor_bolsa'] = str_replace(',', '.', str_replace('.', '', $updateData['valor_bolsa']));
        }
        //Teste se o auxilio_transporte não é nulo
        if (isset($updateData['auxilio_transporte']) && $updateData['auxilio_transporte'] != null) {
            $updateData['auxilio_transporte'] = str_replace(',', '.', str_replace('.', '', $updateData['auxilio_transporte']));
        }


        // Se houver atualizações, aplica o update na tabela tb_termos
        if (!empty($updateData)) {
            $termo->update($updateData);
        }
        // Formatar os valores monetários campos valor_bolsa_alteracao e auxilio_transporte_alteracao
        //Teste se o valor_bolsa_alteracao não é nulo
        if (isset($validatedData['valor_bolsa_alteracao']) && $validatedData['valor_bolsa_alteracao'] != null) {
            $validatedData['valor_bolsa_alteracao'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor_bolsa_alteracao']));
        }
        //Teste se o auxilio_transporte_alteracao não é nulo
        if (isset($validatedData['auxilio_transporte_alteracao']) && $validatedData['auxilio_transporte_alteracao'] != null) {
            $validatedData['auxilio_transporte_alteracao'] = str_replace(',', '.', str_replace('.', '', $validatedData['auxilio_transporte_alteracao']));
        }


        AlteracaoTermo::create($validatedData);
        return redirect('/termos/' . $id . '/alteracoes')->with('success', 'Alteração criada com sucesso!');
    }

    public function destroy($id, $id_alteracao)
    {
        $alteracaoTermo = AlteracaoTermo::findOrFail($id_alteracao);
        $termo = Termo::findOrFail($id);

        // Verificar se a alteração é a mais recente
        $alteracaoMaisRecente = AlteracaoTermo::where('fk_id_termo', $id)
            ->orderBy('id_alteracao', 'desc')
            ->first();

        if ($alteracaoMaisRecente->id_alteracao != $id_alteracao) {
            return redirect('/termos/' . $id . '/alteracoes')->with('error', 'Você só pode excluir a alteração mais recente.');
        }

        // Restaurar os valores antigos
        $restoreData = [
            'fk_id_supervisor' => $alteracaoTermo->old_fk_id_supervisor,
            'nome_orientador' => $alteracaoTermo->old_nome_orientador,
            'cargo_orientador' => $alteracaoTermo->old_cargo_orientador,
            'data_fim_estagio' => $alteracaoTermo->old_data_fim_estagio,
            'valor_bolsa' => $alteracaoTermo->old_valor_bolsa,
            'auxilio_transporte' => $alteracaoTermo->old_auxilio_transporte,
            'horario' => $alteracaoTermo->old_horario,
            'desc_atividades' => $alteracaoTermo->old_desc_atividades,
            'fk_id_local' => $alteracaoTermo->old_fk_id_local,
            'lotacao' => $alteracaoTermo->old_lotacao,
        ];

        // Atualizar apenas os campos que não são nulos
        foreach ($restoreData as $key => $value) {
            if (!is_null($value)) {
                $termo->$key = $value;
            }
        }

        $termo->save();
        $alteracaoTermo->delete();

        return redirect('/termos/' . $id . '/alteracoes')->with('success', 'Alteração excluída com sucesso!');
    }

    public function gerarPdf($id, $id_alteracao)
    {

        $alteracao = AlteracaoTermo::findOrFail($id_alteracao);
        $linklogo = public_path('images/logo_pdf_padrao.png');



        //return view('termos.gerarPdf', compact('termo'));
        $pdf = Pdf::loadView('termos.alteracoes.gerarPdfAlteracao', ['alteracao' => $alteracao, 'linklogo' => $linklogo])
            ->setPaper([0, 0, 595.28, 841.89], 'portrait');


        return $pdf->stream('TAE ' . $alteracao->id_alteracao . '-' . \Carbon\Carbon::parse($alteracao->data_alteracao)->format('d-m-Y') . '-' . $alteracao->termo->estagiario->nome_estagiario . '.pdf');
        //return $pdf->download('TCE'.'.pdf');

    }

    /**
     * Enviar alteração de termo para assinatura no ZapSign
     */
    public function enviarParaZapSign($id, $id_alteracao)
    {
        try {
            $alteracao = AlteracaoTermo::with(['termo.estagiario', 'termo.empresa', 'termo.escola'])->findOrFail($id_alteracao);
            $termo = $alteracao->termo;
            $zapSignService = new ZapSignService();

            // Buscar EBCP para o PDF
            $ebcp = EBCP::findOrFail(1);
            $linklogo = public_path('images/logo_pdf_padrao.png');

            // Preparar signatários completos
            $signatarios = [];
            $signatariosParaPdf = [];
            
            // 1. Representantes da Unidade Concedente (Empresa)
            if ($termo->empresa && $termo->empresa->representantes->count() > 0) {
                foreach ($termo->empresa->representantes as $rep) {
                    $signatarios[] = [
                        'name' => $rep->nome,
                        'email' => $rep->email,
                    ];
                    $signatariosParaPdf[] = [
                        'nome' => $rep->nome,
                        'tipo' => 'Pela Concedente'
                    ];
                }
            }

            // 2. Representantes da Instituição de Ensino (Escola)
            if ($termo->escola && $termo->escola->representantes->count() > 0) {
                foreach ($termo->escola->representantes as $rep) {
                    $signatarios[] = [
                        'name' => $rep->nome,
                        'email' => $rep->email,
                    ];
                    $signatariosParaPdf[] = [
                        'nome' => $rep->nome,
                        'tipo' => 'Pela Instituição de Ensino'
                    ];
                }
            }
            
            // 3. Estagiário
            if ($termo->estagiario) {
                $signatarios[] = [
                    'name' => $termo->estagiario->nome_estagiario,
                    'email' => $termo->estagiario->email ?? null,
                    'phone_number' => $termo->estagiario->numero_celular ?? null,
                ];
                $signatariosParaPdf[] = [
                    'nome' => $termo->estagiario->nome_estagiario,
                    'tipo' => 'Estagiário/Representante Legal'
                ];
            }

            // 4. Agente de Integração (EBCP)
            $signatarios[] = [
                'name' => 'Moacir Aguiar',
                'email' => 'moacirecetista@hotmail.com',
            ];
            $signatariosParaPdf[] = [
                'nome' => $ebcp->nome_ebcp,
                'tipo' => 'Agente de Integração'
            ];

            // Gerar PDF com signatários
            $pdf = Pdf::loadView('termos.alteracoes.gerarPdfAlteracao', [
                'alteracao' => $alteracao,
                'linklogo' => $linklogo,
                'paraZapSign' => true,
                'signatarios' => $signatariosParaPdf
            ])->setPaper([0, 0, 595.28, 841.89], 'portrait');

            // Converter para base64
            $pdfOutput = $pdf->output();
            $pdfBase64 = base64_encode($pdfOutput);
            $numPages = $this->contarPaginasPDF($pdfOutput);

            $documentName = "Termo de Alteração {$termo->numero_termo}/{$termo->ano_termo} - {$termo->estagiario->nome_estagiario}";

            // Enviar para ZapSign
            $resultado = $zapSignService->criarDocumentoBase64($pdfBase64, $documentName, $signatarios);

            if ($resultado['success']) {
                $docToken = $resultado['data']['token'];
                $signers = $resultado['data']['signers'] ?? [];

                // Posicionar assinaturas
                if (count($signers) > 0) {
                    $emailToToken = [];
                    foreach ($signers as $signer) {
                        $emailToToken[$signer['email']] = $signer['token'];
                    }
                    
                    $signersOrdenados = [];
                    foreach ($signatarios as $sig) {
                        $email = $sig['email'] ?? null;
                        if ($email && isset($emailToToken[$email])) {
                            $signersOrdenados[] = [
                                'token' => $emailToToken[$email],
                                'email' => $email
                            ];
                        }
                    }
                    
                    $rubricas = $this->calcularPosicoesAssinaturas($signersOrdenados, $numPages);
                    $zapSignService->posicionarAssinaturas($docToken, $rubricas);
                }

                // Salvar dados ZapSign
                $alteracao->zapsign_doc_token = $docToken;
                $alteracao->zapsign_status = 'enviado';
                $alteracao->zapsign_enviado_em = now();
                $alteracao->save();

                return redirect()->back()->with('success', 'Alteração enviada para assinatura no ZapSign com sucesso!');
            }

            return redirect()->back()->with('error', 'Erro ao enviar documento: ' . $resultado['message']);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao processar solicitação: ' . $e->getMessage());
        }
    }

    /**
     * Calcular posições dinâmicas das assinaturas
     */
    private function calcularPosicoesAssinaturas(array $signers, int $numPages = 1)
    {
        $rubricas = [];
        $totalSigners = count($signers);
        $page = max(0, $numPages - 1);
        
        $signatureWidth = 19.55;
        $signatureHeight = 9.42;
        $columns = min(2, max(1, $totalSigners));
        $gapBetweenColumns = 30.0;
        $leftFirstColumn = 1.0;
        $verticalGap = 0.5;
        $lineHeight = $signatureHeight + $verticalGap;
        $startBottom = 4.0;
        
        foreach ($signers as $index => $signer) {
            $row = intdiv($index, $columns);
            $col = $index % $columns;
            
            $posLeft = $leftFirstColumn + ($col * ($signatureWidth + $gapBetweenColumns));
            $posBottom = $startBottom + ($row * $lineHeight);
            
            if ($posLeft + $signatureWidth > 100.0) {
                $posLeft = max(0.0, 100.0 - $signatureWidth);
            }
            if ($posBottom + $signatureHeight > 100.0) {
                $posBottom = max(0.0, 100.0 - $signatureHeight);
            }
            
            $rubricas[] = [
                'page' => $page,
                'relative_position_bottom' => $posBottom,
                'relative_position_left' => $posLeft,
                'relative_size_x' => $signatureWidth,
                'relative_size_y' => $signatureHeight,
                'type' => 'signature',
                'signer_token' => $signer['token']
            ];
        }
        
        return $rubricas;
    }

    /**
     * Contar número de páginas do PDF
     */
    private function contarPaginasPDF(string $pdfContent): int
    {
        $count = preg_match_all("/\/Page\W/", $pdfContent, $matches);
        return max(1, $count);
    }

    /**
     * Verificar status da alteração no ZapSign
     */
    public function verificarStatusZapSign($id, $id_alteracao)
    {
        try {
            $alteracaoTermo = AlteracaoTermo::findOrFail($id_alteracao);
            
            if (!$alteracaoTermo->zapsign_doc_token) {
                return redirect()->back()->with('warning', 'Esta alteração não foi enviada para o ZapSign ainda.');
            }

            $zapSignService = new ZapSignService();
            $resultado = $zapSignService->detalharDocumento($alteracaoTermo->zapsign_doc_token);

            if ($resultado['success']) {
                $data = $resultado['data'];
                $status = strtolower($data['status'] ?? 'desconhecido');

                // Persistir status
                $alteracaoTermo->zapsign_status = $status;
                $alteracaoTermo->save();

                return redirect()->back()->with('success', "Status da alteração: {$status}");
            }

            return redirect()->back()->with('error', 'Erro ao verificar status: ' . $resultado['message']);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao verificar status: ' . $e->getMessage());
        }
    }
}

