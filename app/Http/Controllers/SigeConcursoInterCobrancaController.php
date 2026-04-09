<?php

namespace App\Http\Controllers;

use App\Models\SigeConcursoInscricao;
use App\Models\SigeConcursoInterCobrancaLog;
use App\Services\InterBolepixManagerService;
use Illuminate\Http\Request;

class SigeConcursoInterCobrancaController extends Controller
{
    public function index(Request $request)
    {
        $query = SigeConcursoInscricao::with(['processo.empresa', 'candidato'])
            ->whereHas('processo', fn ($q) => $q->where('possui_taxa_inscricao', true));

        if ($request->boolean('somente_falhas')) {
            $query->where(function ($builder) {
                $builder->whereIn('inter_situacao', ['FALHA_EMISSAO', 'CANCELADO', 'EXPIRADO'])
                    ->orWhere(function ($sub) {
                        $sub->whereNotNull('inter_codigo_solicitacao')
                            ->whereNull('inter_ultima_sincronizacao_em');
                    })
                    ->orWhere(function ($sub) {
                        $sub->whereNull('inter_codigo_solicitacao')
                            ->where('status_pagamento', 'pendente');
                    });
            });
        }

        if ($request->filled('busca')) {
            $termo = trim((string) $request->busca);
            $query->where(function ($builder) use ($termo) {
                $builder->where('numero_inscricao', 'like', '%' . $termo . '%')
                    ->orWhere('inter_codigo_solicitacao', 'like', '%' . $termo . '%')
                    ->orWhereHas('candidato', fn ($q) => $q->where('nome_completo', 'like', '%' . $termo . '%'));
            });
        }

        if ($request->filled('status_pagamento')) {
            $query->where('status_pagamento', $request->status_pagamento);
        }

        if ($request->filled('inter_situacao')) {
            $query->where('inter_situacao', $request->inter_situacao);
        }

        $inscricoes = $query->orderByDesc('id_inscricao')->paginate(25)->appends($request->query());

        $resumoBase = SigeConcursoInscricao::whereHas('processo', fn ($q) => $q->where('possui_taxa_inscricao', true));
        $resumo = [
            'com_cobranca' => (clone $resumoBase)->whereNotNull('inter_codigo_solicitacao')->count(),
            'pagas' => (clone $resumoBase)->where('status_pagamento', 'pago')->count(),
            'pendentes' => (clone $resumoBase)->where('status_pagamento', 'pendente')->count(),
            'falhas' => (clone $resumoBase)->whereIn('inter_situacao', ['FALHA_EMISSAO', 'CANCELADO', 'EXPIRADO'])->count(),
        ];

        $logsRecentes = SigeConcursoInterCobrancaLog::with('inscricao.candidato')
            ->latest()
            ->limit(20)
            ->get();

        return view('sigeconcursos.cobrancas.index', compact('inscricoes', 'resumo', 'logsRecentes'));
    }

    public function sincronizar(Request $request, int $id, InterBolepixManagerService $manager)
    {
        $inscricao = SigeConcursoInscricao::with(['processo', 'candidato.cidade.estado'])->findOrFail($id);
        $resultado = $manager->sincronizarInscricao($inscricao, 'admin');

        return back()->with($resultado['success'] ? 'success' : 'error', $resultado['message']);
    }

    public function reprocessarLote(Request $request, InterBolepixManagerService $manager)
    {
        $validated = $request->validate([
            'inscricao_ids' => ['required', 'array', 'min:1'],
            'inscricao_ids.*' => ['integer', 'exists:sigeconcursos_tb_inscricoes,id_inscricao'],
        ]);

        $inscricoes = SigeConcursoInscricao::with(['processo', 'candidato.cidade.estado'])
            ->whereIn('id_inscricao', $validated['inscricao_ids'])
            ->get();

        /** @var \Illuminate\Database\Eloquent\Collection<int, SigeConcursoInscricao> $inscricoes */

        $sucessos = 0;
        $falhas = 0;

        foreach ($inscricoes as $inscricao) {
            $resultado = $manager->sincronizarInscricao($inscricao, 'reprocessamento_admin');
            if ($resultado['success']) {
                $sucessos++;
            } else {
                $falhas++;
            }
        }

        return back()->with('success', "Reprocessamento concluído. Sucessos: {$sucessos}. Falhas: {$falhas}.");
    }

    public function webhook(Request $request, InterBolepixManagerService $manager)
    {
        $expectedHeader = (string) config('inter_bolepix.webhook_header', 'Authorization');
        $expectedSecret = (string) config('inter_bolepix.webhook_secret', '');

        if ($expectedSecret !== '') {
            $received = (string) $request->header($expectedHeader, '');
            $isValid = $received === $expectedSecret || $received === ('Bearer ' . $expectedSecret);

            if (!$isValid) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized webhook'], 401);
            }
        }

        $payload = $request->all();
        $callbacks = array_is_list($payload) ? $payload : [$payload];

        foreach ($callbacks as $callback) {
            if (!is_array($callback)) {
                continue;
            }

            $manager->processarCallback($callback);
        }

        return response()->noContent();
    }
}
