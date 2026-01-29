<?php
namespace App\Http\Controllers;

use App\Models\ProcessoSeletivo;
use App\Models\ProcessoArquivo;
use App\Models\InscricaoProcesso;
use App\Models\Empresa;
use App\Exports\InscricoesProcessoExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessoSeletivoController extends Controller
{
    // Listagem de processos (admin/operador vê todos, empresa vê só os seus)
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ProcessoSeletivo::query();

        if ($user->nivel === 'empresa') {
            $query->where('fk_id_empresa', $user->fk_id_empresa);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('empresa') && $user->nivel !== 'empresa') {
            $query->where('fk_id_empresa', $request->input('empresa'));
        }

        $processos = $query->orderByDesc('created_at')->paginate(20);
        $empresas = Empresa::orderBy('nome_empresa', 'asc')->get();

        return view('processos-seletivos.index', compact('processos', 'empresas'));
    }

    // Formulário de criação
    public function create()
    {
        $user = Auth::user();
        $empresas = [];
        $empresaSelecionada = null;

        if ($user->nivel === 'empresa') {
            $empresaSelecionada = $user->fk_id_empresa;
        } else {
            $empresas = Empresa::orderBy('nome_empresa', 'asc')->get(['id_empresa', 'nome_empresa']);
        }

        return view('processos-seletivos.create', compact('empresas', 'empresaSelecionada'));
    }

    // Salvar novo processo
    public function store(Request $request)
    {
        $user = Auth::user();
        $empresaId = $user->nivel === 'empresa' ? $user->fk_id_empresa : $request->input('fk_id_empresa');
        $request->merge(['fk_id_empresa' => $empresaId]);

        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'icone_processo' => 'nullable|image|max:2048',
            'fk_id_empresa' => 'required|integer|exists:tb_empresas,id_empresa',
            'status' => 'required|in:rascunho,aberto,inscricoes,encerrado,finalizado',
            'data_abertura' => 'nullable|date',
            'data_inicio_inscricoes' => 'nullable|date',
            'data_fechamento_inscricoes' => 'nullable|date|after_or_equal:data_inicio_inscricoes',
            'descricao_fases' => 'nullable|string',
            'fases' => 'nullable|array',
            'fases.*.descricao' => 'nullable|string|max:500',
            'fases.*.periodo' => 'nullable|string|max:200',
            'cursos_destino' => 'nullable|string',
            'vagas' => 'nullable|array',
            'vagas.*.nivel' => 'nullable|string|max:120',
            'vagas.*.itens' => 'nullable|array',
            'vagas.*.itens.*.curso' => 'nullable|string|max:200',
            'vagas.*.itens.*.vagas' => 'nullable|string|max:50',
            'requisitos' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'aviso_inscricao' => 'nullable|string',
            'solicitar_upload_inscricao' => 'nullable|boolean',
        ]);

        // Transação para garantir atomicidade
        $processo = DB::transaction(function () use ($request, $validated, $empresaId) {
            $numeroProcesso = ProcessoSeletivo::gerarNumeroProcesso();
            
            $vagasFormatadas = $this->formatarVagas($request->input('vagas', []));
            $fasesFormatadas = $this->formatarFases($request->input('fases', []));

            $listaCursos = $this->extrairCursos($vagasFormatadas, $validated['cursos_destino'] ?? null);

            $validated['cursos_destino'] = $listaCursos;
            $validated['vagas_por_nivel'] = !empty($vagasFormatadas) ? $vagasFormatadas : null;
            $validated['fases'] = !empty($fasesFormatadas) ? $fasesFormatadas : null;
            $validated['descricao_fases'] = !empty($fasesFormatadas)
                ? collect($fasesFormatadas)->map(fn ($fase) => trim(($fase['descricao'] ?? '') . ' ' . ($fase['periodo'] ? '(' . $fase['periodo'] . ')' : '')))->implode(' | ')
                : ($validated['descricao_fases'] ?? null);
            $validated['solicitar_upload_inscricao'] = $request->boolean('solicitar_upload_inscricao');
            unset($validated['vagas']);

            return ProcessoSeletivo::create(array_merge($validated, [
                'numero_processo' => $numeroProcesso,
            ]));
        });

        // Processar uploads de arquivos
        if ($request->hasFile('arquivos')) {
            foreach ($request->file('arquivos') as $index => $arquivo) {
                if ($arquivo->isValid()) {
                    $nomeExibicao = $request->input("nome_exibicao.$index", $arquivo->getClientOriginalName());
                    $tipoArquivo = $request->input("tipo_arquivo.$index", 'outro');
                    
                    $caminhoArmazenado = $arquivo->store('processos-seletivos/' . $processo->id_processo, 'public');
                    
                    ProcessoArquivo::create([
                        'fk_id_processo' => $processo->id_processo,
                        'nome_exibicao' => $nomeExibicao,
                        'caminho_arquivo' => $caminhoArmazenado,
                        'tipo_arquivo' => $tipoArquivo,
                    ]);
                }
            }
        }

        // Ícone do processo
        if ($request->hasFile('icone_processo')) {
            $iconePath = $this->armazenarIcone($request, $processo);
            $processo->update(['icone_processo' => $iconePath]);
        }

        return redirect()->route('processos-seletivos.index')
            ->with('success', 'Processo seletivo cadastrado com sucesso!');
    }

    // Formulário de edição
    public function edit($id)
    {
        $processo = ProcessoSeletivo::findOrFail($id);

        $user = Auth::user();
        $empresas = [];
        $empresaSelecionada = null;

        if ($user->nivel === 'empresa') {
            $empresaSelecionada = $user->fk_id_empresa;
        } else {
            $empresas = Empresa::orderBy('nome_empresa', 'asc')->get(['id_empresa', 'nome_empresa']);
        }

        return view('processos-seletivos.edit', compact('processo', 'empresas', 'empresaSelecionada'));
    }

    // Atualizar processo
    public function update(Request $request, $id)
    {
        $processo = ProcessoSeletivo::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'icone_processo' => 'nullable|image|max:2048',
            'status' => 'required|in:rascunho,aberto,inscricoes,encerrado,finalizado',
            'data_abertura' => 'nullable|date',
            'data_inicio_inscricoes' => 'nullable|date',
            'data_fechamento_inscricoes' => 'nullable|date|after_or_equal:data_inicio_inscricoes',
            'descricao_fases' => 'nullable|string',
            'fases' => 'nullable|array',
            'fases.*.descricao' => 'nullable|string|max:500',
            'fases.*.periodo' => 'nullable|string|max:200',
            'cursos_destino' => 'nullable|string',
            'vagas' => 'nullable|array',
            'vagas.*.nivel' => 'nullable|string|max:120',
            'vagas.*.itens' => 'nullable|array',
            'vagas.*.itens.*.curso' => 'nullable|string|max:200',
            'vagas.*.itens.*.vagas' => 'nullable|string|max:50',
            'requisitos' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'aviso_inscricao' => 'nullable|string',
            'solicitar_upload_inscricao' => 'nullable|boolean',
        ]);

        $vagasFormatadas = $this->formatarVagas($request->input('vagas', []));
        $fasesFormatadas = $this->formatarFases($request->input('fases', []));

        $listaCursos = $this->extrairCursos($vagasFormatadas, $validated['cursos_destino'] ?? null);

        $validated['cursos_destino'] = $listaCursos;
        $validated['vagas_por_nivel'] = !empty($vagasFormatadas) ? $vagasFormatadas : null;
        $validated['fases'] = !empty($fasesFormatadas) ? $fasesFormatadas : null;
        $validated['descricao_fases'] = !empty($fasesFormatadas)
            ? collect($fasesFormatadas)->map(fn ($fase) => trim(($fase['descricao'] ?? '') . ' ' . ($fase['periodo'] ? '(' . $fase['periodo'] . ')' : '')))->implode(' | ')
            : ($validated['descricao_fases'] ?? null);
        $validated['solicitar_upload_inscricao'] = $request->boolean('solicitar_upload_inscricao');
        unset($validated['vagas']);

        $processo->update($validated);

        // Ícone do processo
        if ($request->hasFile('icone_processo')) {
            $iconePath = $this->armazenarIcone($request, $processo);
            $processo->update(['icone_processo' => $iconePath]);
        }

        // Processar uploads de novos arquivos
        if ($request->hasFile('arquivos')) {
            foreach ($request->file('arquivos') as $index => $arquivo) {
                if ($arquivo->isValid()) {
                    $nomeExibicao = $request->input("nome_exibicao.$index", $arquivo->getClientOriginalName());
                    $tipoArquivo = $request->input("tipo_arquivo.$index", 'outro');
                    
                    $caminhoArmazenado = $arquivo->store('processos-seletivos/' . $processo->id_processo, 'public');
                    
                    ProcessoArquivo::create([
                        'fk_id_processo' => $processo->id_processo,
                        'nome_exibicao' => $nomeExibicao,
                        'caminho_arquivo' => $caminhoArmazenado,
                        'tipo_arquivo' => $tipoArquivo,
                    ]);
                }
            }
        }

        return redirect()->route('processos-seletivos.index')
            ->with('success', 'Processo seletivo atualizado com sucesso!');
    }

    // Deletar processo
    public function destroy($id)
    {
        $processo = ProcessoSeletivo::findOrFail($id);

        // Deletar arquivos do armazenamento
        foreach ($processo->arquivos as $arquivo) {
            if (Storage::disk('public')->exists($arquivo->caminho_arquivo)) {
                Storage::disk('public')->delete($arquivo->caminho_arquivo);
            }
        }

        if ($processo->icone_processo && Storage::disk('public')->exists($processo->icone_processo)) {
            Storage::disk('public')->delete($processo->icone_processo);
        }

        $processo->delete();

        return redirect()->route('processos-seletivos.index')
            ->with('success', 'Processo seletivo deletado com sucesso!');
    }

    private function armazenarIcone(Request $request, ProcessoSeletivo $processo): string
    {
        if ($processo->icone_processo && Storage::disk('public')->exists($processo->icone_processo)) {
            Storage::disk('public')->delete($processo->icone_processo);
        }

        return $request->file('icone_processo')
            ->store('processos-seletivos/' . $processo->id_processo . '/icone', 'public');
    }

    private function formatarVagas(array $vagas): array
    {
        return collect($vagas)->map(function ($nivel) {
            $nivelNome = trim($nivel['nivel'] ?? '');
            $itens = collect($nivel['itens'] ?? [])->map(function ($item) {
                $curso = trim($item['curso'] ?? '');
                $vagasValor = trim($item['vagas'] ?? '');
                if ($curso === '' && $vagasValor === '') {
                    return null;
                }
                return [
                    'curso' => $curso,
                    'vagas' => $vagasValor !== '' ? $vagasValor : 'CR',
                ];
            })->filter()->values()->all();

            if ($nivelNome === '' && empty($itens)) {
                return null;
            }

            return [
                'nivel' => $nivelNome !== '' ? $nivelNome : 'Nível',
                'itens' => $itens,
            ];
        })->filter()->values()->all();
    }

    private function formatarFases(array $fases): array
    {
        return collect($fases)->map(function ($fase) {
            $descricao = trim($fase['descricao'] ?? '');
            $periodo = trim($fase['periodo'] ?? '');

            if ($descricao === '' && $periodo === '') {
                return null;
            }

            return [
                'descricao' => $descricao,
                'periodo' => $periodo,
            ];
        })->filter()->values()->all();
    }

    private function extrairCursos(array $vagasFormatadas, $cursosTexto)
    {
        $listaCursos = collect($vagasFormatadas)
            ->flatMap(fn ($nivel) => collect($nivel['itens'] ?? [])->pluck('curso'))
            ->filter()
            ->values()
            ->all();

        if (empty($listaCursos) && $cursosTexto) {
            $listaCursos = array_filter(array_map('trim', preg_split('/[,\n]/', $cursosTexto)));
        }

        return !empty($listaCursos) ? $listaCursos : null;
    }

    // Remover um arquivo específico do processo
    public function removerArquivo($id)
    {
        $arquivo = ProcessoArquivo::with('processo')->findOrFail($id);
        $user = Auth::user();

        if ($user->nivel === 'empresa' && $arquivo->processo->fk_id_empresa !== $user->fk_id_empresa) {
            abort(403, 'Você não tem permissão para excluir este arquivo');
        }

        if (!in_array($user->nivel, ['admin', 'operador', 'empresa'])) {
            abort(403, 'Ação não permitida');
        }

        if (Storage::disk('public')->exists($arquivo->caminho_arquivo)) {
            Storage::disk('public')->delete($arquivo->caminho_arquivo);
        }

        $arquivo->delete();

        return back()->with('success', 'Arquivo removido com sucesso!');
    }

    // Listar inscrições
    public function listarInscricoes($id)
    {
        $processo = ProcessoSeletivo::findOrFail($id);

        $inscricoes = $processo->inscricoes()
            ->with(['estagiario'])
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('processos-seletivos.inscricoes', compact('processo', 'inscricoes'));
    }

    // Atualizar status de uma inscrição
    public function atualizarStatusInscricao(Request $request, $id)
    {
        $processo = ProcessoSeletivo::findOrFail($id);
        
        $validated = $request->validate([
            'inscricao_id' => 'required|integer|exists:tb_inscricoes_processo,id_inscricao',
            'novo_status' => 'required|in:inscrito,deferido,indeferido',
        ]);

        $inscricao = InscricaoProcesso::where('id_inscricao', $validated['inscricao_id'])
            ->where('fk_id_processo', $id)
            ->firstOrFail();

        $inscricao->update([
            'status_inscricao' => $validated['novo_status'],
        ]);

        $statusLabel = [
            'inscrito' => 'Inscrito',
            'deferido' => 'Deferido',
            'indeferido' => 'Indeferido',
        ];

        return back()->with('success', "Status atualizado para: {$statusLabel[$validated['novo_status']]}");
    }

    // Exportar inscrições (PDF/Excel)
    public function exportarInscricoes(Request $request, $id)
    {
        $processo = ProcessoSeletivo::findOrFail($id);

        $format = $request->input('format', 'pdf');
        $statusFiltro = $request->input('status_filter', 'todos');
        $colunas = $request->input('colunas', []);

        // Se nenhuma coluna selecionada, usar todas
        if (empty($colunas)) {
            $colunas = [
                'numero_inscricao',
                'nome',
                'email',
                'telefone',
                'cpf',
                'curso',
                'instituicao',
                'status',
                'data_inscricao'
            ];
        }

        // Buscar inscrições com filtro de status
        $query = $processo->inscricoes()->with(['estagiario']);
        
        if ($statusFiltro !== 'todos') {
            $query->where('status_inscricao', $statusFiltro);
        }
        
        $inscricoes = $query->get();

        if ($format === 'pdf') {
            return $this->exportarInscricoesPDF($processo, $inscricoes, $colunas, $statusFiltro);
        } else {
            return $this->exportarInscricoesExcel($processo, $inscricoes, $colunas, $statusFiltro);
        }
    }

    private function exportarInscricoesPDF($processo, $inscricoes, $colunas, $statusFiltro)
    {
        // Mapeamento de colunas para labels legíveis
        $colunasLabels = [
            'numero_inscricao' => 'Nº Inscrição',
            'nome' => 'Nome',
            'email' => 'E-mail',
            'telefone' => 'Telefone',
            'cpf' => 'CPF',
            'curso' => 'Curso',
            'instituicao' => 'Instituição',
            'status' => 'Status',
            'data_inscricao' => 'Data Inscrição'
        ];

        $statusLabels = [
            'todos' => 'Todas as Inscrições',
            'inscrito' => 'Apenas Inscritos',
            'deferido' => 'Apenas Deferidos',
            'indeferido' => 'Apenas Indeferidos'
        ];

        $dados = [
            'processo' => $processo,
            'inscricoes' => $inscricoes,
            'colunas' => $colunas,
            'colunasLabels' => $colunasLabels,
            'statusFiltro' => $statusLabels[$statusFiltro] ?? 'Todas',
            'dataExportacao' => now()->format('d/m/Y H:i:s')
        ];

        $pdf = \PDF::loadView('processos-seletivos.exports.inscricoes-pdf', $dados);
        $pdf->setPaper('A4', 'landscape');

        $nomeArquivo = 'inscricoes_' . \Str::slug($processo->titulo) . '_' . now()->format('Ymd_His') . '.pdf';
        
        return $pdf->download($nomeArquivo);
    }

    private function exportarInscricoesExcel($processo, $inscricoes, $colunas, $statusFiltro)
    {
        $nomeArquivo = 'inscricoes_' . \Str::slug($processo->titulo) . '_' . now()->format('Ymd_His') . '.xlsx';
        
        return Excel::download(
            new InscricoesProcessoExport($inscricoes, $colunas), 
            $nomeArquivo
        );
    }

    // Gerenciar resultados
    public function resultados($id)
    {
        $processo = ProcessoSeletivo::findOrFail($id);

        $resultados = $processo->resultados()->orderByDesc('created_at')->get();

        return view('processos-seletivos.resultados', compact('processo', 'resultados'));
    }

    // Publicar resultado
    public function publicarResultado(Request $request, $id)
    {
        $processo = ProcessoSeletivo::findOrFail($id);

        $validated = $request->validate([
            'numero_resultado' => 'required|string|max:150',
            'arquivo_resultado' => 'nullable|file|max:10240',
        ]);

        $caminhoResultado = null;
        if ($request->hasFile('arquivo_resultado')) {
            $caminhoResultado = $request->file('arquivo_resultado')
                ->store('processos-seletivos/' . $processo->id_processo . '/resultados', 'public');
        }

        \App\Models\ResultadoProcesso::create([
            'fk_id_processo' => $processo->id_processo,
            'numero_resultado' => $validated['numero_resultado'],
            'arquivo_resultado' => $caminhoResultado,
        ]);

        return redirect()->route('processos-seletivos.resultados', $id)
            ->with('success', 'Resultado publicado com sucesso!');
    }
}
