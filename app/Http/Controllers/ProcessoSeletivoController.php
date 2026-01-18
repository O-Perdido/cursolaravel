<?php
namespace App\Http\Controllers;

use App\Models\ProcessoSeletivo;
use App\Models\ProcessoArquivo;
use App\Models\InscricaoProcesso;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'fk_id_empresa' => 'required|integer|exists:tb_empresas,id_empresa',
            'status' => 'required|in:rascunho,aberto,inscricoes,encerrado,finalizado',
            'data_abertura' => 'nullable|date',
            'data_fechamento_inscricoes' => 'nullable|date|after_or_equal:data_abertura',
            'descricao_fases' => 'nullable|string',
            'cursos_destino' => 'nullable|string',
            'requisitos' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'aviso_inscricao' => 'nullable|string',
        ]);

        // Transação para garantir atomicidade
        $processo = DB::transaction(function () use ($validated, $empresaId) {
            $numeroProcesso = ProcessoSeletivo::gerarNumeroProcesso($empresaId);
            
            // Converter cursos para JSON (split por quebra de linha ou vírgula)
            if (isset($validated['cursos_destino']) && !empty($validated['cursos_destino'])) {
                $cursos = array_filter(array_map('trim', preg_split('/[,\n]/', $validated['cursos_destino'])));
                $validated['cursos_destino'] = $cursos;
            } else {
                $validated['cursos_destino'] = null;
            }

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
            'status' => 'required|in:rascunho,aberto,inscricoes,encerrado,finalizado',
            'data_abertura' => 'nullable|date',
            'data_fechamento_inscricoes' => 'nullable|date|after_or_equal:data_abertura',
            'descricao_fases' => 'nullable|string',
            'cursos_destino' => 'nullable|string',
            'requisitos' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'aviso_inscricao' => 'nullable|string',
        ]);

        // Converter cursos para JSON
        if (isset($validated['cursos_destino']) && !empty($validated['cursos_destino'])) {
            $cursos = array_filter(array_map('trim', preg_split('/[,\n]/', $validated['cursos_destino'])));
            $validated['cursos_destino'] = $cursos;
        } else {
            $validated['cursos_destino'] = null;
        }

        $processo->update($validated);

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

        $processo->delete();

        return redirect()->route('processos-seletivos.index')
            ->with('success', 'Processo seletivo deletado com sucesso!');
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

    // Exportar inscrições (PDF/Excel)
    public function exportarInscricoes(Request $request, $id)
    {
        $processo = ProcessoSeletivo::findOrFail($id);

        $format = $request->input('format', 'excel');
        $inscricoes = $processo->inscricoes()
            ->with(['estagiario'])
            ->get();

        if ($format === 'pdf') {
            // Implementar export PDF
            return $this->exportarInscricoesPDF($processo, $inscricoes);
        } else {
            // Implementar export Excel
            return $this->exportarInscricoesExcel($processo, $inscricoes);
        }
    }

    private function exportarInscricoesPDF($processo, $inscricoes)
    {
        // Será implementado com Barryvdh\DomPDF
        // Por enquanto, placeholder
        return response()->json(['message' => 'Export PDF será implementado']);
    }

    private function exportarInscricoesExcel($processo, $inscricoes)
    {
        // Será implementado com Maatwebsite\Excel
        // Por enquanto, placeholder
        return response()->json(['message' => 'Export Excel será implementado']);
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
