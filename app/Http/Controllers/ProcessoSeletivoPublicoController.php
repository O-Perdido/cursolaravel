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
    public function listarAbertos(Request $request)
    {
        $query = ProcessoSeletivo::where('status', '!=', 'rascunho')
            ->with(['empresa']);

        $allowedStatus = ['aberto', 'inscricoes', 'encerrado', 'finalizado'];
        $defaultStatus = ['aberto', 'inscricoes'];

        if ($request->filled('status') && in_array($request->status, $allowedStatus)) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', $defaultStatus);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('numero_processo', 'like', "%{$search}%")
                    ->orWhereHas('empresa', function ($q) use ($search) {
                        $q->where('nome_empresa', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('curso')) {
            $curso = $request->curso;
            $query->where(function ($q) use ($curso) {
                $q->whereJsonContains('cursos_destino', $curso)
                    ->orWhereRaw("JSON_SEARCH(cursos_destino, 'one', ?, NULL) IS NOT NULL", ["%{$curso}%"]);
            });
        }

        if ($request->filled('nivel')) {
            $nivel = $request->nivel;
            $query->where(function ($q) use ($nivel) {
                $q->whereRaw("JSON_SEARCH(JSON_EXTRACT(vagas_por_nivel, '$[*].nivel'), 'one', ?, NULL) IS NOT NULL", [$nivel])
                    ->orWhereRaw("JSON_SEARCH(JSON_EXTRACT(vagas_por_nivel, '$[*].nivel'), 'one', ?, NULL) IS NOT NULL", ["%{$nivel}%"])
                    ->orWhereRaw("JSON_SEARCH(vagas_por_nivel, 'one', ?, NULL) IS NOT NULL", ["%{$nivel}%"]);
            });
        }

        $processos = $query
            ->orderByDesc(DB::raw('COALESCE(data_inicio_inscricoes, data_abertura, created_at)'))
            ->paginate(12);

        $todosCursos = [];
        ProcessoSeletivo::where('status', '!=', 'rascunho')->get()->each(function ($p) use (&$todosCursos) {
            if ($p->cursos_destino && is_array($p->cursos_destino)) {
                foreach ($p->cursos_destino as $curso) {
                    if (is_array($curso) && isset($curso['nome'])) {
                        $todosCursos[$curso['nome']] = $curso['nome'];
                    } elseif (is_string($curso)) {
                        $todosCursos[$curso] = $curso;
                    }
                }
            }
        });
        ksort($todosCursos);

        $todosNiveis = [];
        ProcessoSeletivo::where('status', '!=', 'rascunho')->get()->each(function ($p) use (&$todosNiveis) {
            if ($p->vagas_por_nivel && is_array($p->vagas_por_nivel)) {
                foreach ($p->vagas_por_nivel as $vaga) {
                    if (is_array($vaga) && isset($vaga['nivel'])) {
                        $todosNiveis[$vaga['nivel']] = $vaga['nivel'];
                    }
                }
            }
        });
        ksort($todosNiveis);

        return view('estagiario.processos-seletivos.listar', compact('processos', 'todosCursos', 'todosNiveis'));
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
        // Se não está logado, redirecionar para login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('redirect', route('processos-seletivos.detalhes.publico', $id));
        }

        $user = Auth::user();

        // Validar se é estagiário
        if ($user->nivel !== 'estagiario') {
            return response()->json(['error' => 'Apenas estagiários podem se inscrever'], 403);
        }

        $estagiarioId = $user->fk_id_estagiario;
        $processo = ProcessoSeletivo::findOrFail($id);

        $agora = now();
        $inicio = $processo->inicioInscricoes();
        $fim = $processo->data_fechamento_inscricoes;

        if ($processo->status !== 'inscricoes') {
            return response()->json(['error' => 'Inscrições não estão disponíveis no momento'], 422);
        }

        if ($inicio && $agora->lt($inicio)) {
            return response()->json(['error' => 'Inscrições ainda não iniciaram'], 422);
        }

        if ($fim && $agora->gt($fim)) {
            return response()->json(['error' => 'Período de inscrições encerrado'], 422);
        }

        if (!$processo->periodoInscricoesAberto()) {
            return response()->json(['error' => 'Inscrições indisponíveis'], 422);
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

    // ========== ROTAS PÚBLICAS ==========

    // Landing page - página inicial pública
    public function landing()
    {
        // Landing page agora é só CTA e informações
        // Processos estão em /processos-publicos
        return view('landing');
    }

    // Listar processos públicos (sem autenticação)
    public function listarPublicos(Request $request)
    {
        $query = ProcessoSeletivo::where('status', '!=', 'rascunho')
            ->with(['empresa']);

        $allowedStatus = ['aberto', 'inscricoes', 'encerrado', 'finalizado'];
        $defaultStatus = ['aberto', 'inscricoes'];

        if ($request->filled('status') && in_array($request->status, $allowedStatus)) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', $defaultStatus);
        }

        // Filtro por busca
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('numero_processo', 'like', "%{$search}%")
                  ->orWhereHas('empresa', function ($q) use ($search) {
                      $q->where('nome_empresa', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por curso (usando JSON)
        if ($request->has('curso') && !empty($request->curso)) {
            $curso = $request->curso;
            $query->where(function ($q) use ($curso) {
                $q->whereJsonContains('cursos_destino', $curso)
                  ->orWhereRaw("JSON_SEARCH(cursos_destino, 'one', ?, NULL) IS NOT NULL", ["%{$curso}%"]);
            });
        }

        // Filtro por nível (técnico, graduação, pós)
        if ($request->has('nivel') && !empty($request->nivel)) {
            $nivel = $request->nivel;
            $query->where(function ($q) use ($nivel) {
                $q->whereRaw("JSON_SEARCH(JSON_EXTRACT(vagas_por_nivel, '$[*].nivel'), 'one', ?, NULL) IS NOT NULL", [$nivel])
                    ->orWhereRaw("JSON_SEARCH(JSON_EXTRACT(vagas_por_nivel, '$[*].nivel'), 'one', ?, NULL) IS NOT NULL", ["%{$nivel}%"])
                    ->orWhereRaw("JSON_SEARCH(vagas_por_nivel, 'one', ?, NULL) IS NOT NULL", ["%{$nivel}%"]);
            });
        }

        $processos = $query
            ->orderByDesc(DB::raw('COALESCE(data_inicio_inscricoes, data_abertura, created_at)'))
            ->paginate(12);

        // Coletar todos os cursos únicos para o filtro
        $todosCursos = [];
        ProcessoSeletivo::where('status', '!=', 'rascunho')->get()->each(function ($p) use (&$todosCursos) {
            if ($p->cursos_destino && is_array($p->cursos_destino)) {
                foreach ($p->cursos_destino as $curso) {
                    if (is_array($curso) && isset($curso['nome'])) {
                        $todosCursos[$curso['nome']] = $curso['nome'];
                    } elseif (is_string($curso)) {
                        $todosCursos[$curso] = $curso;
                    }
                }
            }
        });
        ksort($todosCursos);

        // Coletar todos os níveis únicos
        $todosNiveis = [];
        ProcessoSeletivo::where('status', '!=', 'rascunho')->get()->each(function ($p) use (&$todosNiveis) {
            if ($p->vagas_por_nivel && is_array($p->vagas_por_nivel)) {
                foreach ($p->vagas_por_nivel as $vaga) {
                    if (is_array($vaga) && isset($vaga['nivel'])) {
                        $todosNiveis[$vaga['nivel']] = $vaga['nivel'];
                    }
                }
            }
        });
        ksort($todosNiveis);

        return view('processos-seletivos.publicos', compact('processos', 'todosCursos', 'todosNiveis'));
    }

    // Detalhes de um processo - versão pública
    public function detalhesPublico($id)
    {
        $processo = ProcessoSeletivo::findOrFail($id);
        $jaInscrito = false;

        // Se está logado, verificar se já está inscrito
        if (Auth::check() && Auth::user()->nivel === 'estagiario') {
            $estagiarioId = Auth::user()->fk_id_estagiario;
            $jaInscrito = InscricaoProcesso::where('fk_id_processo', $id)
                ->where('fk_id_estagiario', $estagiarioId)
                ->exists();
        }

        return view('processos-seletivos.detalhes-publico', compact('processo', 'jaInscrito'));
    }
}
