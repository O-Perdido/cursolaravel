<?php

namespace App\Http\Controllers;

use App\Models\SigeConcursoInscricao;
use App\Models\SigeConcursoProcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SigeConcursoPublicoController extends Controller
{
    public function index(Request $request)
    {
        $query = SigeConcursoProcesso::with(['empresa', 'isencoes'])
            ->where('status', '!=', 'rascunho');

        $allowedStatus = [
            'publicado',
            'inscricoes_abertas',
            'inscricoes_encerradas',
            'em_andamento',
            'finalizado',
            'suspenso',
        ];

        if ($request->filled('status') && in_array($request->status, $allowedStatus, true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipo_processo') && in_array($request->tipo_processo, ['processo_seletivo', 'concurso_publico'], true)) {
            $query->where('tipo_processo', $request->tipo_processo);
        }

        if ($request->filled('busca')) {
            $termo = trim((string) $request->busca);

            $query->where(function ($builder) use ($termo) {
                $builder->where('titulo', 'like', '%' . $termo . '%')
                    ->orWhere('numero_edital', 'like', '%' . $termo . '%')
                    ->orWhere('numero_processo', 'like', '%' . $termo . '%')
                    ->orWhereHas('empresa', function ($empresaQuery) use ($termo) {
                        $empresaQuery->where('nome_razao_social', 'like', '%' . $termo . '%');
                    });
            });
        }

        $processos = $query
            ->orderByRaw('data_publicacao IS NULL, data_publicacao DESC')
            ->orderByDesc('id_processo')
            ->paginate(12)
            ->appends($request->query());

        return view('sigeconcursos.publico.processos.index', compact('processos'));
    }

    public function show(int $id)
    {
        $processo = SigeConcursoProcesso::with([
            'empresa',
            'processoCargos.cargo',
            'isencoes',
            'arquivos',
            'documentosExigidos',
        ])->findOrFail($id);

        if ($processo->status === 'rascunho') {
            abort(404);
        }

        $inscricaoExistente = null;

        if (Auth::check() && Auth::user()->nivel === 'candidato') {
            $candidatoId = (int) (Auth::user()->fk_id_candidato ?? 0);

            if ($candidatoId > 0) {
                $inscricaoExistente = SigeConcursoInscricao::where('fk_id_processo', $processo->id_processo)
                    ->where('fk_id_candidato', $candidatoId)
                    ->first();
            }
        }

        $podeInscrever = $this->processoComInscricoesAbertas($processo);

        return view('sigeconcursos.publico.processos.show', compact('processo', 'inscricaoExistente', 'podeInscrever'));
    }

    private function processoComInscricoesAbertas(SigeConcursoProcesso $processo): bool
    {
        return $processo->inscricoesAbertasAgora();
    }
}
