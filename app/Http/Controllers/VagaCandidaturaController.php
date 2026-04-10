<?php

namespace App\Http\Controllers;

use App\Mail\VagaCandidaturaStatusMail;
use App\Models\User;
use App\Models\Vaga;
use App\Models\VagaCandidatura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VagaCandidaturaController extends Controller
{
    public function candidatar(Request $request, $vagaId)
    {
        $user = Auth::user();

        abort_unless($user && $user->nivel === 'estagiario' && $user->fk_id_estagiario, 403);

        $vaga = Vaga::findOrFail($vagaId);

        if (!$vaga->divulgada_publicamente || $vaga->status !== 'disponivel' || $vaga->fk_id_termo) {
            return back()->with('error', 'Esta vaga não está disponível para candidatura no momento.');
        }

        $existe = VagaCandidatura::where('fk_id_vaga', $vaga->id_vaga)
            ->where('fk_id_estagiario', $user->fk_id_estagiario)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Você já se candidatou para esta vaga.');
        }

        $validated = $request->validate([
            'curriculo_arquivo' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'observacoes_estagiario' => ['nullable', 'string'],
        ]);

        $arquivo = $request->file('curriculo_arquivo');
        $nomeBase = Str::slug(pathinfo($arquivo->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'curriculo';
        $extensao = $arquivo->getClientOriginalExtension();
        $caminho = $arquivo->storeAs(
            'vagas/candidaturas/vaga_' . $vaga->id_vaga . '/estagiario_' . $user->fk_id_estagiario,
            $nomeBase . '-' . now()->format('YmdHis') . '.' . $extensao,
            'public'
        );

        VagaCandidatura::create([
            'fk_id_vaga' => $vaga->id_vaga,
            'fk_id_estagiario' => $user->fk_id_estagiario,
            'status_candidatura' => VagaCandidatura::STATUS_ENVIADA,
            'curriculo_arquivo' => $caminho,
            'observacoes_estagiario' => $validated['observacoes_estagiario'] ?? null,
        ]);

        return redirect()->route('vagas.publicas.minhas-candidaturas')
            ->with('success', 'Candidatura enviada com sucesso.');
    }

    public function minhasCandidaturas()
    {
        $user = Auth::user();

        abort_unless($user && $user->nivel === 'estagiario' && $user->fk_id_estagiario, 403);

        $candidaturas = VagaCandidatura::with(['vaga.empresa', 'vaga.local'])
            ->where('fk_id_estagiario', $user->fk_id_estagiario)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('vagas.publicas.minhas-candidaturas', compact('candidaturas'));
    }

    public function indexInterno($vagaId)
    {
        $vaga = Vaga::with(['empresa', 'local', 'estagiarioDefinido'])
            ->withCount('candidaturas')
            ->findOrFail($vagaId);

        $this->autorizarAcessoInterno($vaga);

        $candidaturas = $vaga->candidaturas()
            ->with(['estagiario'])
            ->orderByRaw("FIELD(status_candidatura, 'definido', 'aprovado', 'entrevista', 'em_analise', 'enviada', 'nao_selecionado', 'desistente')")
            ->orderByDesc('created_at')
            ->paginate(20);

        $statusDisponiveis = VagaCandidatura::statusDisponiveis();

        return view('vagas.candidaturas.index', compact('vaga', 'candidaturas', 'statusDisponiveis'));
    }

    public function atualizarStatus(Request $request, $vagaId)
    {
        $vaga = Vaga::findOrFail($vagaId);
        $this->autorizarAcessoInterno($vaga);

        $validated = $request->validate([
            'candidatura_id' => 'required|integer|exists:tb_vaga_candidaturas,id_candidatura',
            'novo_status' => 'required|string|in:' . implode(',', array_keys(VagaCandidatura::statusDisponiveis())),
            'observacoes_internas' => 'nullable|string',
            'enviar_email' => 'nullable|boolean',
        ]);

        $candidatura = VagaCandidatura::where('id_candidatura', $validated['candidatura_id'])
            ->where('fk_id_vaga', $vaga->id_vaga)
            ->with(['estagiario', 'vaga.empresa'])
            ->firstOrFail();

        $novoStatus = $validated['novo_status'];
        $statusAnterior = $candidatura->status_candidatura;

        $candidatura->update([
            'status_candidatura' => $novoStatus,
            'observacoes_internas' => $validated['observacoes_internas'] ?? $candidatura->observacoes_internas,
            'analisado_em' => now(),
            'fk_id_usuario_analisou' => Auth::id(),
        ]);

        if ($novoStatus === VagaCandidatura::STATUS_DEFINIDO) {
            $this->definirEstagiarioNaVaga($vaga, $candidatura);
        }

        $enviarEmail = $request->boolean('enviar_email');
        if ($enviarEmail) {
            $this->enviarEmailStatus($candidatura);
        }

        $statusLabel = $candidatura->status_label;
        $sufixoEmail = $enviarEmail ? ' E-mail enviado ao estagiário.' : '';

        return back()->with('success', "Status alterado de {$statusAnterior} para {$statusLabel}.{$sufixoEmail}");
    }

    public function definir(Request $request, $vagaId, $candidaturaId)
    {
        $request->merge([
            'candidatura_id' => $candidaturaId,
            'novo_status' => VagaCandidatura::STATUS_DEFINIDO,
        ]);

        return $this->atualizarStatus($request, $vagaId);
    }

    public function downloadCurriculo($candidaturaId)
    {
        $candidatura = VagaCandidatura::with(['vaga', 'estagiario'])->findOrFail($candidaturaId);
        $user = Auth::user();

        $autorizado = in_array($user->nivel, ['admin', 'operador'], true)
            || ($user->nivel === 'empresa' && (int) $user->fk_id_empresa === (int) $candidatura->vaga->fk_id_empresa)
            || ($user->nivel === 'estagiario' && (int) $user->fk_id_estagiario === (int) $candidatura->fk_id_estagiario);

        abort_unless($autorizado, 403);

        abort_unless(Storage::disk('public')->exists($candidatura->curriculo_arquivo), 404);

        $extensao = pathinfo($candidatura->curriculo_arquivo, PATHINFO_EXTENSION);
        $nomeBase = 'curriculo-' . Str::slug($candidatura->estagiario->nome_estagiario ?? 'estagiario');
        $nomeDownload = $extensao ? $nomeBase . '.' . $extensao : $nomeBase;

        return response()->download(storage_path('app/public/' . $candidatura->curriculo_arquivo), $nomeDownload);
    }

    private function autorizarAcessoInterno(Vaga $vaga): void
    {
        $user = Auth::user();

        if ($user->nivel === 'empresa' && (int) $user->fk_id_empresa !== (int) $vaga->fk_id_empresa) {
            abort(403);
        }

        abort_unless(in_array($user->nivel, ['admin', 'operador', 'empresa'], true), 403);
    }

    private function definirEstagiarioNaVaga(Vaga $vaga, VagaCandidatura $candidatura): void
    {
        $vaga->candidaturas()
            ->where('status_candidatura', VagaCandidatura::STATUS_DEFINIDO)
            ->where('id_candidatura', '!=', $candidatura->id_candidatura)
            ->update(['status_candidatura' => VagaCandidatura::STATUS_APROVADO]);

        $estagiario = $candidatura->estagiario;

        $vaga->update([
            'fk_id_estagiario_definido' => $estagiario->id_estagiario,
            'tem_estagiario_definido' => true,
            'nome_estagiario' => $estagiario->nome_estagiario,
            'contato_whatsapp' => $estagiario->numero_celular ?? $estagiario->numero_telefone,
            'contato_email' => $estagiario->email,
        ]);
    }

    private function enviarEmailStatus(VagaCandidatura $candidatura): void
    {
        $destinatario = trim((string) ($candidatura->estagiario->email ?? ''));

        if ($destinatario === '') {
            $usuario = User::where('nivel', 'estagiario')
                ->where('fk_id_estagiario', $candidatura->fk_id_estagiario)
                ->first();

            $destinatario = trim((string) ($usuario?->email ?? ''));
        }

        if ($destinatario === '') {
            return;
        }

        Mail::to($destinatario)->send(new VagaCandidaturaStatusMail($candidatura, $candidatura->status_label));

        $candidatura->forceFill([
            'notificado_em' => now(),
        ])->save();
    }
}