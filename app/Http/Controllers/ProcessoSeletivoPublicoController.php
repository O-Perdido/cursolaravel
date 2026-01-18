<?php
namespace App\Http\Controllers;

use App\Models\ProcessoSeletivo;
use App\Models\InscricaoProcesso;
use App\Models\ProcessoArquivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessoSeletivoPublicoController extends Controller
{
    // Listar processos abertos para estagiários
    public function listarAbertos()
    {
        $processos = ProcessoSeletivo::where('status', '!=', 'rascunho')
            ->with(['empresa'])
            ->orderByDesc('data_abertura')
            ->get();

        return view('estagiario.processos-seletivos.listar', compact('processos'));
    }

    // Detalhes de um processo
    public function detalhes($id)
    {
        $processo = ProcessoSeletivo::findOrFail($id);
        $user = Auth::user();
        $estagiarioId = $user->fk_id_estagiario ?? null;

        $jaInscrito = false;
        if ($estagiarioId) {
            $jaInscrito = InscricaoProcesso::where('fk_id_processo', $id)
                ->where('fk_id_estagiario', $estagiarioId)
                ->exists();
        }

        return view('estagiario.processos-seletivos.detalhes', compact('processo', 'jaInscrito'));
    }

    // Realizar inscrição (AJAX)
    public function inscrever(Request $request, $id)
    {
        $user = Auth::user();

        // Validar se é estagiário
        if ($user->nivel !== 'estagiario') {
            return response()->json(['error' => 'Apenas estagiários podem se inscrever'], 403);
        }

        $estagiarioId = $user->fk_id_estagiario;
        $processo = ProcessoSeletivo::findOrFail($id);

        // Validar se o período de inscrições está aberto
        if (!$processo->periodiInscricoesAberto()) {
            return response()->json(['error' => 'Período de inscrições encerrado'], 422);
        }

        // Verificar se já está inscrito
        if (InscricaoProcesso::where('fk_id_processo', $id)
            ->where('fk_id_estagiario', $estagiarioId)
            ->exists()) {
            return response()->json(['error' => 'Você já está inscrito neste processo'], 422);
        }

        // Criar inscrição
        $inscricao = InscricaoProcesso::create([
            'fk_id_processo' => $id,
            'fk_id_estagiario' => $estagiarioId,
            'status_inscricao' => 'inscrito',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inscrição realizada com sucesso!',
        ]);
    }

    // Download seguro de arquivos do edital
    public function downloadArquivo($id)
    {
        $arquivo = ProcessoArquivo::with('processo')->findOrFail($id);
        $user = Auth::user();

        if ($arquivo->processo->status === 'rascunho' && !in_array($user->nivel, ['admin', 'operador', 'empresa'])) {
            abort(403, 'Arquivo indisponível para este perfil');
        }

        if ($user->nivel === 'empresa' && $arquivo->processo->fk_id_empresa !== $user->fk_id_empresa) {
            abort(403, 'Arquivo pertencente a outra empresa');
        }

        if (!Storage::disk('public')->exists($arquivo->caminho_arquivo)) {
            abort(404, 'Arquivo não encontrado');
        }

        $extensao = pathinfo($arquivo->caminho_arquivo, PATHINFO_EXTENSION);
        $nomeBase = trim(preg_replace('~[\\\\/]+~', '-', $arquivo->nome_exibicao));
        $nomeBase = $nomeBase !== '' ? $nomeBase : 'arquivo';
        $nomeDownload = $extensao ? $nomeBase . '.' . $extensao : $nomeBase;

        return Storage::disk('public')->download($arquivo->caminho_arquivo, $nomeDownload);
    }

    // Listar inscrições do estagiário
    public function minhasInscricoes()
    {
        $user = Auth::user();
        $estagiarioId = $user->fk_id_estagiario;

        $inscricoes = InscricaoProcesso::where('fk_id_estagiario', $estagiarioId)
            ->with(['processo', 'processo.empresa'])
            ->orderByDesc('created_at')
            ->get();

        return view('estagiario.processos-seletivos.minhas-inscricoes', compact('inscricoes'));
    }
}
