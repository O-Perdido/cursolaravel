<?php

namespace App\Http\Controllers;


use App\Exports\TermosExport;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\ConcessaoRecesso;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Escola;
use App\Models\Rescisao;
use Illuminate\Http\Request;
use App\Models\Termo;
use App\Models\Estagiario;
use App\Models\Empresa;
use App\Models\Supervisor;
use App\Models\Local;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Ebcp;
use Illuminate\Database\QueryException;
use App\Services\ZapSignService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;


class TermoController extends Controller
{

    public function index(Request $request)
    {
        $query = Termo::query();

        // Filtros
        if ($request->filled('escola')) {
            $query->where('fk_id_escola', $request->input('escola'));
        }

        if ($request->filled('estagiario')) {
            $query->whereHas('estagiario', function ($q) use ($request) {
                $q->where('nome_estagiario', 'like', '%' . $request->input('estagiario') . '%');
            });
        }

        if ($request->filled('numero_termo')) {
            $query->where('numero_termo', $request->input('numero_termo'));
        }

        if ($request->filled('ano_termo')) {
            $query->where('ano_termo', $request->input('ano_termo'));
        }

        if ($request->filled('empresa')) {
            $query->where('fk_id_empresa', $request->input('empresa'));
        }

        if ($request->filled('local')) {
            $query->where('fk_id_local', $request->input('local'));
        }

        if ($request->filled('data_inicial')) {
            $query->whereDate('data_fim_estagio', '>=', $request->input('data_inicial'));
        }

        if ($request->filled('data_final')) {
            $query->whereDate('data_fim_estagio', '<=', $request->input('data_final'));
        }

        // Filtrar os termos que tem rescisão
        if ($request->has('status') && $request->input('status') == 'rescindidos') {
            $rescisaoIds = Rescisao::pluck('fk_id_termo')->toArray();
            $query->whereIn('id_termo', $rescisaoIds);
        }
        // Filtrar os termos que não tem rescisão
        if ($request->has('status') && $request->input('status') == 'ativos') {
            $rescisaoIds = Rescisao::pluck('fk_id_termo')->toArray();
            $query->whereNotIn('id_termo', $rescisaoIds);
        }
        // Filtrar os termos que estão vencidos
        if ($request->has('status') && $request->input('status') == 'vencidos') {
            $query->where('data_fim_estagio', '<', now())
                ->whereDoesntHave('rescisao');
        }

        // Se usuário for do tipo "empresa", restringe a listagem à sua unidade
        if (Auth::check() && Auth::user()->nivel === 'empresa') {
            $query->where('fk_id_empresa', Auth::user()->fk_id_empresa);
        }

        // Ordenação padrão (mais recentes primeiro)
        $query->orderByDesc('id_termo');

        // Carrega dados para os selects
        $escolas = Escola::orderBy('nome_escola', 'asc')->get();

        // Itens por página (25, 50, 100, 200, "all")
        $perPageParam = $request->input('per_page');
        $allowed = ['25', '50', '100', '200', 'all'];
        if (!in_array((string)($perPageParam ?? ''), $allowed, true)) {
            $perPageParam = '25';
        }

        if ($perPageParam === 'all') {
            // Paginar tudo em uma única página mantendo a API do paginator
            $total = (clone $query)->count();
            $perPage = max(1, (int)$total);
        } else {
            $perPage = (int)$perPageParam;
        }

        // Eager loading essencial para evitar N+1 na view
        $query->with(['estagiario', 'empresa.representantes', 'escola.representantes']);

        $termos = $query->paginate($perPage)->appends($request->query());
        $num_termos = $termos->total();

        $empresas = Empresa::orderBy('nome_empresa', 'asc')->get();
        return view('termos.index', compact('termos', 'empresas', 'escolas', 'num_termos'));
    }

    public function gerarPdfRelatorioTermo(Request $request, ?int $id_empresa = null)
    {
        $query = Termo::query();

        // Filtros
        if ($request->filled('escola')) {
            $query->where('fk_id_escola', $request->input('escola'));
        }

        if ($request->filled('estagiario')) {
            $query->whereHas('estagiario', function ($q) use ($request) {
                $q->where('nome_estagiario', 'like', '%' . $request->input('estagiario') . '%');
            });
        }

        if ($id_empresa != null) {
            $query->where('fk_id_empresa', $id_empresa);
        } else if ($request->filled('empresa')) {
            $query->where('fk_id_empresa', $request->input('empresa'));
        }

        if ($request->filled('local')) {
            $query->where('fk_id_local', $request->input('local'));
        }

        if ($request->filled('data_inicial')) {
            $query->whereDate('data', '>=', $request->input('data_inicial'));
        }

        if ($request->filled('data_final')) {
            $query->whereDate('data', '<=', $request->input('data_final'));
        }

        // Filtrar os termos que tem rescisão
        if ($request->has('status') && $request->input('status') == 'rescindidos') {
            $rescisaoIds = Rescisao::pluck('fk_id_termo')->toArray();
            $query->whereIn('id_termo', $rescisaoIds);
        }
        // Filtrar os termos que não tem rescisão
        if ($request->has('status') && $request->input('status') == 'ativos') {
            $rescisaoIds = Rescisao::pluck('fk_id_termo')->toArray();
            $query->whereNotIn('id_termo', $rescisaoIds);
        }
        // Filtrar os termos que estão vencidos
        if ($request->has('status') && $request->input('status') == 'vencidos') {
            $query->where('data_fim_estagio', '<', now())
                ->whereDoesntHave('rescisao');
        }

        $termos = $query->with('estagiario')->get();
        $empresas = Empresa::all();
        $escolas = Escola::all();

        $ebcp = EBCP::findOrFail(1);
        $linklogo = public_path('images/logo_com_informacoes.png');

        $pdf = Pdf::loadView('termos.gerarPdfRelatorioTermo', [
            'termos' => $termos,
            'linklogo' => $linklogo,
            'ebcp' => $ebcp,
            'request' => $request,
            'empresas' => $empresas,
            'escolas' => $escolas
        ])->setPaper([0, 0, 595.28, 841.89], 'landscape');
        $dompdf = $pdf->getDOMPdf();
        $options = $dompdf->getOptions();
        $options->set('isPhpEnabled', true);
        $dompdf->setOptions($options);

        return $pdf->stream('relatorio_termos.pdf');
    }

    public function create($id_estagiario = null)
    {
        $estagiarios = Estagiario::orderBy('nome_estagiario', 'asc')->get();
        $escolas = Escola::orderBy('nome_escola', 'asc')->get();
        $empresas = Empresa::orderBy('nome_empresa', 'asc')->get();
        $supervisores = Supervisor::orderBy('nome_supervisor', 'asc')->get();

        return view('termos.create', [
            'estagiarios' => $estagiarios,
            'empresas' => $empresas,
            'escolas' => $escolas,
            'supervisores' => $supervisores,
            'id_estagiario' => $id_estagiario
        ]);
    }

    // Buscar vagas disponíveis de uma empresa
    public function buscarVagasPorEmpresa(Request $request)
    {
        $empresaId = $request->input('empresa_id');
        if (!$empresaId) {
            return response()->json([]);
        }
        
        try {
            // Verificar se a tabela tb_vagas existe
            if (!Schema::hasTable('tb_vagas')) {
                return response()->json([]);
            }
            
            $vagas = \App\Models\Vaga::where('fk_id_empresa', $empresaId)
                ->where('status', 'disponivel')
                ->whereDate('data_termino', '>=', now())
                ->with(['local', 'supervisor'])
                ->orderBy('numero_vaga', 'desc')
                ->get();
            
            // Verificar se alguma vaga expirou (alertar no frontend)
            $vagas->each(function($vaga) {
                $vaga->expirada = strtotime($vaga->data_termino) < strtotime(date('Y-m-d'));
            });
            
            return response()->json($vagas);
        } catch (\Exception $e) {
            // Em caso de erro (tabela não existe, model não existe, etc), retornar array vazio
            return response()->json([]);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fk_id_estagiario' => 'required|integer',
            'fk_id_empresa' => 'required|integer|exists:tb_empresas,id_empresa',
            'fk_id_local' => 'nullable|integer|exists:tb_local,id_local',
            'fk_id_local_fixo' => 'nullable|integer|exists:tb_local,id_local',
            'fk_id_supervisor' => 'required|integer',
            'fk_id_supervisor_fixo' => 'required|integer',
            'fk_id_escola' => 'required|integer',
            'desc_atividades' => 'required|string',
            'desc_atividades_fixo' => 'required|string',
            'nome_orientador' => 'required|string',
            'nome_orientador_fixo' => 'required|string',
            'cargo_orientador' => 'required|string',
            'cargo_orientador_fixo' => 'required|string',
            'data_inicio_estagio' => 'required|date',
            'data_fim_estagio' => 'required|date',
            'data_fim_estagio_fixo' => 'required|date',
            'horario' => 'required|string',
            'horario_fixo' => 'required|string',
            'valor_bolsa' => 'required',
            'valor_bolsa_fixo' => 'required',
            'auxilio_transporte' => '',
            'auxilio_transporte_fixo' => '',
            'lotacao' => 'required|string',
            'lotacao_fixo' => 'required|string',
            'fk_id_vaga' => 'nullable|integer|exists:tb_vagas,id_vaga',
        ]);

        // Verificar se o estagiário já possui termo ativo (sem rescisão)
        $termoAtivo = Termo::where('fk_id_estagiario', $validatedData['fk_id_estagiario'])
            ->whereDoesntHave('rescisao')
            ->first();

        if ($termoAtivo) {
            $estagiario = Estagiario::find($validatedData['fk_id_estagiario']);
            $nomeEstagiario = $estagiario ? $estagiario->nome_estagiario : 'Este estagiário';
            $numeroTermo = $termoAtivo->numero_termo . '/' . $termoAtivo->ano_termo;
            
            return back()
                ->withErrors(['fk_id_estagiario' => $nomeEstagiario . ' já possui um termo de estágio ativo (Termo nº ' . $numeroTermo . '). É necessário rescindir o termo atual antes de cadastrar um novo.'])
                ->withInput();
        }

        // Garantir que o local pertence à empresa selecionada (somente se informado)
        if (!empty($validatedData['fk_id_local'])) {
            $local = Local::find($validatedData['fk_id_local']);
            if (!$local || (int)$local->fk_id_empresa !== (int)$validatedData['fk_id_empresa']) {
                return back()
                    ->withErrors(['fk_id_local' => 'O local selecionado não pertence à unidade concedente escolhida.'])
                    ->withInput();
            }
        }

        $validatedData['data'] = now()->toDateString();
        $validatedData['hora'] = now()->toTimeString();
        $validatedData['ano_termo'] = now()->year;

        $validatedData['valor_bolsa'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor_bolsa']));
        $validatedData['valor_bolsa_fixo'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor_bolsa_fixo']));

        $validatedData['auxilio_transporte'] = str_replace(',', '.', str_replace('.', '', $validatedData['auxilio_transporte']));
        $validatedData['auxilio_transporte_fixo'] = str_replace(',', '.', str_replace('.', '', $validatedData['auxilio_transporte_fixo']));

        if ($validatedData['auxilio_transporte'] == '') {
            $validatedData['auxilio_transporte'] = 0;
            $validatedData['auxilio_transporte_fixo'] = 0;
        }

        // Conta quantos termos já existem no ano_termo igual ao ano do campo data, se for 0, coloca 1 no campo numero_termo, se for maior que 0, conta o maior número de termo e incrementa mais 1
        $numeroTermo = Termo::where('ano_termo', $validatedData['ano_termo'])
            ->count();
        if ($numeroTermo == 0) {
            $validatedData['numero_termo'] = 1;
        } else {
            $ultimoTermo = Termo::where('ano_termo', $validatedData['ano_termo'])
                ->orderBy('numero_termo', 'desc')
                ->first();
            $validatedData['numero_termo'] = $ultimoTermo->numero_termo + 1;
        }

        $termo = Termo::create($validatedData);
        
        // Se vaga foi selecionada, vincular e atualizar status
        if (!empty($validatedData['fk_id_vaga'])) {
            $vaga = \App\Models\Vaga::find($validatedData['fk_id_vaga']);
            if ($vaga) {
                $vaga->update([
                    'status' => 'preenchida',
                    'fk_id_termo' => $termo->id_termo,
                    'vinculo_tipo' => 'vinculado'
                ]);
                $termo->update(['vinculo' => 'vinculado']);
            }
        } else {
            $termo->update(['vinculo' => 'nao_vinculado']);
        }
        
        return redirect('/termos')->with('success', 'Termo criado com sucesso!');
    }

    public function show($id)
    {
        $termo = Termo::with('rescisao')->findOrFail($id);
        return view('termos.show', compact('termo'));
    }

    /*public function edit($id)
    {
        $termo = Termo::findOrFail($id);
        return view('termos.edit', compact('termo'));
    }*/

    public function destroy($id)
    {

        $termo = Termo::findOrFail($id);

        if ($termo->rescisao()->exists()) {
            return redirect()->route('termos.index')
                ->with('error', 'Não é possível excluir o termo pois ele está vinculado a uma recisão!');
        }

        if ($termo->alteracaoTermo()->exists()) {
            return redirect()->route('termos.index')
                ->with('error', 'Não é possível excluir o termo pois ele está vinculado a uma alteração!');
        }

        if ($termo->folhaTermo()->exists()) {
            return redirect()->route('termos.index')
                ->with('error', 'Não é possível excluir o termo pois ele está vinculado a uma folha!');
        }

        try {
            // Se termo está vinculado a vaga, desvincular e tornar disponível
            if ($termo->fk_id_vaga) {
                $vaga = \App\Models\Vaga::find($termo->fk_id_vaga);
                if ($vaga) {
                    $vaga->update([
                        'status' => 'disponivel',
                        'fk_id_termo' => null,
                        'vinculo_tipo' => null
                    ]);
                }
            }
            
            $termo->delete();
            return redirect()->route('termos.index')
                ->with('success', 'Termo excluído com sucesso!');
        } catch (QueryException $e) {
            return redirect()->route('termos.index')
                ->with('error', 'Erro inesperado ao tentar excluir o termo!');
        }
    }

    public function gerarPdf($id)
    {

        // Buscar o registro com ID 1 da tabela EBCP
        $ebcp = EBCP::findOrFail(1);
        $termo = Termo::findOrFail($id);
        $linklogo = public_path('images/logo_pdf_padrao.png');

        //return view('termos.gerarPdf', compact('termo'));
        $pdf = Pdf::loadView('termos.gerarPdfTermo', ['termo' => $termo, 'linklogo' => $linklogo, 'ebcp' => $ebcp])
            ->setPaper([0, 0, 595.28, 841.89], 'portrait');

        return $pdf->stream('TCE ' . $termo->id_termo . '-' . Carbon::parse($termo->data)->format('Y') . '-' . $termo->estagiario->nome_estagiario . '.pdf');
        //return $pdf->download('TCE'.'.pdf');

    }

    public function downloadPdf($id)
    {

        // Buscar o registro com ID 1 da tabela EBCP
        $ebcp = EBCP::findOrFail(1);
        $termo = Termo::findOrFail($id);
        $linklogo = public_path('images/logo_pdf_padrao.png');

        //return view('termos.gerarPdf', compact('termo'));
        $pdf = Pdf::loadView('termos.gerarPdfTermo', ['termo' => $termo, 'linklogo' => $linklogo, 'ebcp' => $ebcp])
            ->setPaper([0, 0, 595.28, 841.89], 'portrait');

        return $pdf->download('TCE ' . $termo->id_termo . '-' . Carbon::parse($termo->data)->format('Y') . '-' . $termo->estagiario->nome_estagiario . '.pdf');
        //return $pdf->download('TCE'.'.pdf');

    }



    public function export(Request $request): mixed
    {
        return Excel::download(new TermosExport($request), 'relatorio_termos.xlsx');
    }

    /**
     * Enviar termo para assinatura no ZapSign
     */
    public function enviarParaZapSign($id)
    {
        try {
            $termo = Termo::with(['estagiario', 'empresa', 'escola'])->findOrFail($id);
            $zapSignService = new ZapSignService();

            // Buscar EBCP para o PDF
            $ebcp = EBCP::findOrFail(1);
            $linklogo = public_path('images/logo_pdf_padrao.png');

            // Preparar signatários ANTES de gerar o PDF
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

            // 4. Moacir Aguiar (Agente de Integração - EBCP) - FIXO
            $signatarios[] = [
                'name' => 'Moacir Aguiar',
                'email' => 'moacirecetista@hotmail.com',
            ];
            $signatariosParaPdf[] = [
                'nome' => $ebcp->nome_ebcp,
                'tipo' => 'Agente de Integração'
            ];

            // Gerar PDF com os signatários dinâmicos
            $pdf = Pdf::loadView('termos.gerarPdfTermo', [
                'termo' => $termo, 
                'linklogo' => $linklogo, 
                'ebcp' => $ebcp,
                'paraZapSign' => true, // Flag indicando que é para ZapSign
                'signatarios' => $signatariosParaPdf // Signatários para renderizar no PDF
            ])->setPaper([0, 0, 595.28, 841.89], 'portrait');

            // Converter PDF para base64 (mais seguro - não expõe arquivo publicamente)
            $pdfOutput = $pdf->output();
            $pdfBase64 = base64_encode($pdfOutput);

            // Contar número de páginas do PDF (usando DomPDF)
            $numPages = $this->contarPaginasPDF($pdfOutput);

            $documentName = "Termo de Estágio {$termo->numero_termo}/{$termo->ano_termo} - {$termo->estagiario->nome_estagiario}";

            // Usar método base64 (mais seguro)
            $resultado = $zapSignService->criarDocumentoBase64($pdfBase64, $documentName, $signatarios);

            if ($resultado['success']) {
                $docToken = $resultado['data']['token'];
                $signers = $resultado['data']['signers'] ?? [];

                // Posicionar assinaturas dinamicamente
                // IMPORTANTE: Usar a ordem original dos signatários, não a ordem retornada pelo ZapSign
                if (count($signers) > 0) {
                    // Criar mapeamento de email para token do signer
                    $emailToToken = [];
                    foreach ($signers as $signer) {
                        $emailToToken[$signer['email']] = $signer['token'];
                    }
                    
                    // Reordenar signers na mesma ordem que enviamos
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

                // Salvar o doc_token do ZapSign no banco de dados
                $termo->zapsign_doc_token = $docToken;
                $termo->zapsign_status = 'enviado';
                $termo->zapsign_enviado_em = now();
                $termo->save();

                return redirect()->back()->with('success', 'Documento enviado para assinatura no ZapSign com sucesso!');
            }

            return redirect()->back()->with('error', 'Erro ao enviar documento: ' . $resultado['message']);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao processar solicitação: ' . $e->getMessage());
        }
    }

    /**
     * Verificar status de assinatura no ZapSign
     */
    public function verificarStatusZapSign($id)
    {
        try {
            $termo = Termo::findOrFail($id);
            
            // Você precisará salvar o doc_token quando criar o documento
            if (!isset($termo->zapsign_doc_token)) {
                return redirect()->back()->with('warning', 'Este termo não foi enviado para o ZapSign ainda.');
            }

            $zapSignService = new ZapSignService();
            $resultado = $zapSignService->detalharDocumento($termo->zapsign_doc_token);

            if ($resultado['success']) {
                $data = $resultado['data'];
                $status = strtolower($data['status'] ?? 'desconhecido');

                // Persistir status para refletir na lista e nos detalhes
                $termo->zapsign_status = $status;
                $termo->save();

                return redirect()->back()->with('success', "Status do documento: {$status}");
            }

            return redirect()->back()->with('error', 'Erro ao verificar status: ' . $resultado['message']);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao verificar status: ' . $e->getMessage());
        }
    }

    /**
     * Calcular posições dinâmicas das assinaturas na última página
     * Distribui horizontalmente em até 2 colunas
     * 
     * @param array $signers Array de signatários retornado pelo ZapSign
     * @param int $numPages Número total de páginas do PDF
     * @return array Array de rubricas com coordenadas
     */
    private function calcularPosicoesAssinaturas(array $signers, int $numPages = 1)
    {
        $rubricas = [];
        $totalSigners = count($signers);
        
        // Última página (índice começa em 0)
        $page = max(0, $numPages - 1);
        
        // Tamanhos recomendados pela ZapSign para A4 vertical
        // Fonte: https://docs.zapsign.com.br/documentos/opcional-posicionar-assinaturas
        $signatureWidth = 19.55;
        $signatureHeight = 9.42;
        
    // Layout em 2 colunas mais à esquerda com gap ainda maior para evitar qualquer sobreposição
    $columns = min(2, max(1, $totalSigners));
    $gapBetweenColumns = 30.0; // aumentar ainda mais o espaço horizontal entre colunas
        
    // Trazer conjunto de colunas mais à esquerda (quase encostado)
    $leftFirstColumn = 1.0; // margem esquerda mínima
        
        // Altura por linha (assinatura + pequeno gap visual)
    $verticalGap = 0.5; // gap vertical ainda menor (mais compacto)
        $lineHeight = $signatureHeight + $verticalGap;
        
        // Começar baixo para não pegar o corpo do documento
        $startBottom = 4.0; // 4% do rodapé
        
        foreach ($signers as $index => $signer) {
            $row = intdiv($index, $columns);
            $col = $index % $columns;
            
            $posLeft = $leftFirstColumn + ($col * ($signatureWidth + $gapBetweenColumns));
            $posBottom = $startBottom + ($row * $lineHeight);
            
            // Garantir limites (não ultrapassar bordas)
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
     * Contar número de páginas do PDF gerado
     * 
     * @param string $pdfContent Conteúdo do PDF
     * @return int Número de páginas
     */
    private function contarPaginasPDF(string $pdfContent): int
    {
        // Método simples: contar ocorrências de "/Type /Page" no PDF
        $count = preg_match_all("/\/Page\W/", $pdfContent, $matches);
        return max(1, $count); // Retornar no mínimo 1 página
    }

    /**
     * Dados de cálculo do recesso (acumulado, disponível etc.)
     */
    private function calcularRecessoDados(Termo $termo): array
    {
        $hoje = Carbon::today();
        $inicio = Carbon::parse($termo->data_inicio_estagio);
        // Dias trabalhados entre a data de início e hoje (base 360). Considera 0 se início futuro
        $diasTrabalhados = max(0, $inicio->diffInDays($hoje));

        // Recesso acumulado proporcional
        $recessoAcumulado = (30 * $diasTrabalhados) / 360.0;

        $jaUsado = 30 - (int)($termo->saldo_recesso ?? 30);
        $recessoDisponivel = $recessoAcumulado - $jaUsado;

        // Normalizações
        if ($recessoDisponivel < 0) $recessoDisponivel = 0.0;

        return [
            'dias_trabalhados' => $diasTrabalhados,
            'recesso_acumulado' => $recessoAcumulado,
            'ja_usado' => $jaUsado,
            'saldo_recesso' => (int)($termo->saldo_recesso ?? 30),
            'recesso_disponivel' => $recessoDisponivel,
            'recesso_disponivel_inteiro' => (int)floor($recessoDisponivel),
        ];
    }

    /**
     * Conceder recesso: valida, abate do saldo e gera PDF de concessão
     */
    public function gerarPdfRecesso(Request $request, $id)
    {
        $request->validate([
            'data_inicio_recesso' => 'required|date',
            'data_fim_recesso' => 'required|date',
        ]);

        $termo = Termo::with(['estagiario', 'empresa', 'escola'])->findOrFail($id);

        $inicio = Carbon::parse($request->input('data_inicio_recesso'));
        $fim = Carbon::parse($request->input('data_fim_recesso'));

        if ($fim->lt($inicio)) {
            return redirect()->route('termos.show', $termo->id_termo)
                ->with('error', 'A data final do recesso não pode ser anterior à data inicial.');
        }

        // Quantidade de dias corridos no intervalo (inclusivo)
        $totalDias = $inicio->diffInDays($fim) + 1;

        // Cálculos do recesso disponível
        $calc = $this->calcularRecessoDados($termo);
        $disponivelInt = (int)$calc['recesso_disponivel_inteiro'];

        if ($totalDias > $disponivelInt) {
            return redirect()->route('termos.show', $termo->id_termo)
                ->with('error', 'Quantidade de dias selecionada ('. $totalDias .') é maior que o disponível ('. $disponivelInt .').');
        }

        // Verificar conflito de períodos com concessões já registradas para este termo (intervalo inclusivo)
        $conflitos = ConcessaoRecesso::where('fk_id_termo', $termo->id_termo)
            ->whereDate('data_inicio_recesso', '<=', $fim)
            ->whereDate('data_fim_recesso', '>=', $inicio)
            ->get();

        if ($conflitos->isNotEmpty()) {
            // Montar mensagem com períodos conflitantes
            $detalhes = $conflitos->map(function ($c) {
                return $c->data_inicio_recesso->format('d/m/Y') . ' a ' . $c->data_fim_recesso->format('d/m/Y');
            })->implode('; ');

            return redirect()->route('termos.show', $termo->id_termo)
                ->with('error', 'O período selecionado conflita com concessões já registradas: ' . $detalhes . '.');
        }

        // Registrar concessão no histórico
        ConcessaoRecesso::create([
            'fk_id_termo' => $termo->id_termo,
            'data_inicio_recesso' => $inicio,
            'data_fim_recesso' => $fim,
            'total_dias' => $totalDias,
            'data_concessao' => now(),
            'fk_id_usuario' => Auth::id(),
            'status' => 'ativo',
        ]);

        // Abater do saldo_recesso (não deixar negativo)
        $novoSaldo = max(0, (int)($termo->saldo_recesso ?? 30) - $totalDias);
        $termo->saldo_recesso = $novoSaldo;
        $termo->save();

        // Dados EBCP e logo
        $ebcp = EBCP::findOrFail(1);
        $linklogo = public_path('images/logo_pdf_padrao.png');

        // Montar PDF
        $pdf = Pdf::loadView('termos.gerarPdfRecesso', [
            'termo' => $termo,
            'ebcp' => $ebcp,
            'linklogo' => $linklogo,
            'inicio_recesso' => $inicio,
            'fim_recesso' => $fim,
            'total_dias_recesso' => $totalDias,
            'calc' => $calc,
        ])->setPaper([0, 0, 595.28, 841.89], 'portrait');

        $nome = 'Termo de Concessão de Recesso de Estágio - ' . ($termo->estagiario->nome_estagiario ?? 'Estagiario') . '.pdf';
        return $pdf->download($nome);
    }

    /**
     * Excluir (reverter) uma concessão de recesso
     */
    public function excluirConcessaoRecesso(Request $request, $id_concessao)
    {
        $concessao = ConcessaoRecesso::findOrFail($id_concessao);

        $termo = $concessao->termo;

        // Devolver os dias ao saldo de recesso apenas se ainda está ativa
        if ($concessao->status === 'ativo') {
            $termo->saldo_recesso = min(30, ($termo->saldo_recesso ?? 0) + $concessao->total_dias);
            $termo->save();
        }

        // Exclusão definitiva do registro
        $concessao->delete();

        return redirect()->route('termos.show', $termo->id_termo)
            ->with('success', 'Concessão excluída com sucesso! ' . $concessao->total_dias . ' dia(s) devolvido(s) ao saldo de recesso.');
    }

    /**
     * Imprimir/visualizar o PDF de uma concessão de recesso já registrada
     */
    public function imprimirPdfRecesso($id_concessao)
    {
        // Carrega a concessão com o respectivo termo e relações necessárias
        $concessao = ConcessaoRecesso::with(['termo.estagiario', 'termo.empresa', 'termo.escola'])->findOrFail($id_concessao);

        $termo = $concessao->termo;

        // Dados institucionais e logo usados no PDF
        $ebcp = EBCP::findOrFail(1);
        $linklogo = public_path('images/logo_pdf_padrao.png');

        // Reaproveita o mesmo template de PDF, injetando as datas da concessão
        $pdf = Pdf::loadView('termos.gerarPdfRecesso', [
            'termo' => $termo,
            'ebcp' => $ebcp,
            'linklogo' => $linklogo,
            'inicio_recesso' => $concessao->data_inicio_recesso,
            'fim_recesso' => $concessao->data_fim_recesso,
            'total_dias_recesso' => $concessao->total_dias,
            'calc' => $this->calcularRecessoDados($termo),
        ])->setPaper([0, 0, 595.28, 841.89], 'portrait');

        $nome = 'Termo de Concessão de Recesso de Estágio - ' . ($termo->estagiario->nome_estagiario ?? 'Estagiario') . '.pdf';
        // Stream para abrir em nova guia (navegador), facilitando a impressão
        return $pdf->stream($nome);
    }
}
