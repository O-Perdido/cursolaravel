<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Termo;
use App\Models\Rescisao;
use App\Models\Ebcp;
use App\Models\Vaga;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ZapSignService;
use App\Services\AvaliacaoService;
use Illuminate\Support\Facades\Auth;

class RescisaoController extends Controller
{
    protected $avaliacaoService;

    public function __construct(AvaliacaoService $avaliacaoService)
    {
        $this->avaliacaoService = $avaliacaoService;
    }

    public function store(Request $request, $id_termo)
    {
        $validatedData = $request->validate([
            'fk_id_termo' => 'required',
            'data_rescisao' => 'required|date',
            'motivo' => 'required'
        ]);

        $termo = Termo::findOrFail($id_termo);

        //Atualiza a data_fim_estagio do termo com a mesma data da rescisão
        $termo->update(['data_fim_estagio' => $validatedData['data_rescisao']]);

        $rescisao = Rescisao::create($validatedData);

        // Gera automaticamente avaliação de finalização ao rescindir
        $this->avaliacaoService->gerarAvaliacaoFinalizacao($termo);

        // Desvincula a vaga caso o termo esteja vinculado a uma
        if ($termo->fk_id_vaga) {
            $vaga = Vaga::find($termo->fk_id_vaga, ['*']);
            if ($vaga) {
                // Marca a vaga como suspensa (não volta a ficar disponível automaticamente)
                $vaga->status = 'suspensa';
                $vaga->fk_id_termo = null;
                $vaga->save();
            }

            // Remove a vinculação do termo com a vaga
            $termo->fk_id_vaga = null;
            $termo->vinculo = 'nao_vinculado';
            $termo->save();
        }

        return redirect('/termos/' . $id_termo . '/show')->with('success', 'Rescisão criada com sucesso!');
    }

    public function gerarPdf($id)
    {
        $rescisao = Rescisao::with(['termo.estagiario', 'termo.empresa.cidade'])->findOrFail($id);
        $user = Auth::user();

        if ($user && $user->nivel === 'empresa' && (int) $rescisao->termo->fk_id_empresa !== (int) $user->fk_id_empresa) {
            abort(403, 'Você não tem permissão para visualizar este documento.');
        }

        $linklogo = public_path('images/logo_pdf_padrao.png');



        //return view('termos.gerarPdf', compact('termo'));
        $pdf = Pdf::loadView('termos.gerarPdfRescisao', ['rescisao' => $rescisao, 'linklogo' => $linklogo])
            ->setPaper([0, 0, 595.28, 841.89], 'portrait');


        return $pdf->stream('TRE ' . $rescisao->termo->id_termo . '-' . \Carbon\Carbon::parse($rescisao->termo->data)->format('Y') . '-' . $rescisao->termo->estagiario->nome_estagiario . '.pdf');
        //return $pdf->download('TCE'.'.pdf');

    }

    /**
     * Enviar rescisão para assinatura no ZapSign
     */
    public function enviarParaZapSign(Request $request, $id)
    {
        try {
            $request->validate([
                'remover_destinatarios' => 'nullable|array',
                'remover_destinatarios.*' => 'nullable|email',
            ]);

            $emailsRemovidos = collect($request->input('remover_destinatarios', []))
                ->map(fn($email) => strtolower(trim((string) $email)))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $rescisao = Rescisao::with(['termo.estagiario', 'termo.empresa', 'termo.escola'])->findOrFail($id);
            $termo = $rescisao->termo;
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
            } elseif ($termo->empresa && $termo->empresa->nome_representante && $termo->empresa->email) {
                $signatarios[] = [
                    'name' => $termo->empresa->nome_representante,
                    'email' => $termo->empresa->email,
                ];
                $signatariosParaPdf[] = [
                    'nome' => $termo->empresa->nome_representante,
                    'tipo' => 'Pela Concedente'
                ];
            }

            // 2. Representantes da Instituição de Ensino (Escola)
            if ($termo->escola && !$termo->escola->nao_assina_zapsign) {
                if ($termo->escola->representantes->count() > 0) {
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
                } elseif ($termo->escola->nome_representante && $termo->escola->email) {
                    $signatarios[] = [
                        'name' => $termo->escola->nome_representante,
                        'email' => $termo->escola->email,
                    ];
                    $signatariosParaPdf[] = [
                        'nome' => $termo->escola->nome_representante,
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

            [$signatarios, $signatariosParaPdf] = $this->filtrarSignatariosRemovidos(
                $signatarios,
                $signatariosParaPdf,
                $emailsRemovidos
            );

            if (empty($signatarios)) {
                return redirect()->back()->with('error', 'Nenhum destinatário válido foi selecionado para envio ao ZapSign.');
            }

            // Gerar PDF com signatários
            $pdf = Pdf::loadView('termos.gerarPdfRescisao', [
                'rescisao' => $rescisao,
                'linklogo' => $linklogo,
                'paraZapSign' => true,
                'signatarios' => $signatariosParaPdf
            ])->setPaper([0, 0, 595.28, 841.89], 'portrait');

            // Converter para base64
            $pdfOutput = $pdf->output();
            $pdfBase64 = base64_encode($pdfOutput);
            $numPages = $this->contarPaginasPDF($pdfOutput);

            $documentName = "Termo de Rescisão {$termo->numero_termo}/{$termo->ano_termo} - {$termo->estagiario->nome_estagiario}";

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
                $rescisao->zapsign_doc_token = $docToken;
                $rescisao->zapsign_status = 'enviado';
                $rescisao->zapsign_enviado_em = now();
                $rescisao->save();

                return redirect()->back()->with('success', 'Rescisão enviada para assinatura no ZapSign com sucesso!');
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

    private function filtrarSignatariosRemovidos(array $signatarios, array $signatariosParaPdf, array $emailsRemovidos): array
    {
        if (empty($emailsRemovidos)) {
            return [$signatarios, $signatariosParaPdf];
        }

        $emailsRemovidosLookup = array_flip($emailsRemovidos);
        $signatariosFiltrados = [];
        $signatariosParaPdfFiltrados = [];

        foreach ($signatarios as $index => $signatario) {
            $email = strtolower(trim((string) ($signatario['email'] ?? '')));

            if ($email !== '' && isset($emailsRemovidosLookup[$email])) {
                continue;
            }

            $signatariosFiltrados[] = $signatario;

            if (isset($signatariosParaPdf[$index])) {
                $signatariosParaPdfFiltrados[] = $signatariosParaPdf[$index];
            }
        }

        return [$signatariosFiltrados, $signatariosParaPdfFiltrados];
    }

    /**
     * Verificar status da rescisão no ZapSign
     */
    public function verificarStatusZapSign($id)
    {
        try {
            $rescisao = Rescisao::findOrFail($id);
            
            if (!$rescisao->zapsign_doc_token) {
                return redirect()->back()->with('warning', 'Esta rescisão não foi enviada para o ZapSign ainda.');
            }

            $zapSignService = new ZapSignService();
            $resultado = $zapSignService->detalharDocumento($rescisao->zapsign_doc_token);

            if ($resultado['success']) {
                $data = $resultado['data'];
                $status = strtolower($data['status'] ?? 'desconhecido');

                // Persistir status para refletir nos detalhes
                $rescisao->zapsign_status = $status;
                $rescisao->save();

                return redirect()->back()->with('success', "Status da rescisão: {$status}");
            }

            return redirect()->back()->with('error', 'Erro ao verificar status: ' . $resultado['message']);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao verificar status: ' . $e->getMessage());
        }
    }

    public function excluirDocumentoZapSign($id)
    {
        try {
            $rescisao = Rescisao::findOrFail($id);

            if (!$rescisao->zapsign_doc_token) {
                return redirect()->back()->with('warning', 'Esta rescisao nao possui documento no ZapSign.');
            }

            $zapSignService = new ZapSignService();
            $resultado = $zapSignService->excluirDocumento($rescisao->zapsign_doc_token);

            if ($resultado['success']) {
                $rescisao->zapsign_doc_token = null;
                $rescisao->zapsign_status = null;
                $rescisao->zapsign_enviado_em = null;
                $rescisao->save();

                return redirect()->back()->with('success', 'Documento do ZapSign excluido com sucesso.');
            }

            return redirect()->back()->with('error', 'Erro ao excluir documento: ' . $resultado['message']);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao excluir documento: ' . $e->getMessage());
        }
    }

}