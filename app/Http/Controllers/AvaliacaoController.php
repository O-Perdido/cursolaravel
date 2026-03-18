<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
use App\Models\Termo;
use App\Services\AvaliacaoService;
use App\Services\AvaliacaoPdfService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AvaliacaoController extends Controller
{
    protected $avaliacaoService;
    protected $avaliacaoPdfService;

    public function __construct(AvaliacaoService $avaliacaoService, AvaliacaoPdfService $avaliacaoPdfService)
    {
        $this->avaliacaoService = $avaliacaoService;
        $this->avaliacaoPdfService = $avaliacaoPdfService;
    }

    private function usuarioEhEstagiario(): bool
    {
        return Auth::user()?->nivel === 'estagiario';
    }

    private function usuarioPodeAcessarTermo(Termo $termo): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if (in_array($user->nivel, ['admin', 'operador'], true)) {
            return true;
        }

        if ($user->nivel === 'estagiario') {
            return (int) $user->fk_id_estagiario === (int) $termo->fk_id_estagiario;
        }

        return false;
    }

    private function usuarioPodeAcessarAvaliacao(Avaliacao $avaliacao): bool
    {
        $avaliacao->loadMissing('termo');

        return $this->usuarioPodeAcessarTermo($avaliacao->termo);
    }

    private function respostaAcessoNegado(Request $request, string $mensagem)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $mensagem], Response::HTTP_FORBIDDEN);
        }

        return redirect()->back()->with('error', $mensagem);
    }

    private function respostaAcaoInvalida(Request $request, string $mensagem)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $mensagem], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return redirect()->back()->with('error', $mensagem);
    }

    /**
     * Lista todas as avaliações (pendentes e respondidas)
     */
    public function index(Request $request)
    {
        $query = Avaliacao::with(['termo', 'supervisor']);

        // Filtrar por status se especificado, caso contrário mostrar todas
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtros
        if ($request->filled('fk_id_termo')) {
            $query->where('fk_id_termo', $request->fk_id_termo);
        }

        if ($request->filled('tipo_avaliacao')) {
            $query->where('tipo_avaliacao', $request->tipo_avaliacao);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('termo', function ($q) use ($search) {
                $q->where('numero_termo', 'like', "%{$search}%")
                  ->orWhereHas('estagiario', function ($q2) use ($search) {
                      $q2->where('nome_estagiario', 'like', "%{$search}%");
                  });
            });
        }

        $avaliacoes = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('avaliacoes.index', compact('avaliacoes'));
    }

    /**
     * Exibe a página com todas as avaliações de um termo específico
     */
    public function porTermo(Request $request, Termo $termo)
    {
        if (!$this->usuarioPodeAcessarTermo($termo)) {
            return $this->respostaAcessoNegado($request, 'Você não tem permissão para visualizar as avaliações deste termo.');
        }

        $avaliacoes = $termo->avaliacoes()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('avaliacoes.por-termo', compact('termo', 'avaliacoes'));
    }

    /**
     * Exibe detalhes de uma avaliação (visualização)
     */
    public function show(Request $request, Avaliacao $avaliacao)
    {
        if (!$this->usuarioPodeAcessarAvaliacao($avaliacao)) {
            return $this->respostaAcessoNegado($request, 'Você não tem permissão para visualizar esta avaliação.');
        }

        return view('avaliacoes.show', compact('avaliacao'));
    }

    /**
     * Gera um token de compartilhamento e retorna o link
     */
    public function gerarLinkCompartilhamento(Request $request, Avaliacao $avaliacao)
    {
        if (!$this->usuarioPodeAcessarAvaliacao($avaliacao)) {
            return $this->respostaAcessoNegado($request, 'Você não tem permissão para compartilhar esta avaliação.');
        }

        // Apenas avaliações pendentes podem gerar links
        if ($avaliacao->status !== 'pendente') {
            return $this->respostaAcaoInvalida($request, 'Apenas avaliações pendentes podem gerar links de compartilhamento.');
        }

        // Se já tem token, retorna o existente; se não, gera novo
        if (!$avaliacao->token_compartilhamento) {
            $avaliacao->token_compartilhamento = Avaliacao::gerarTokenCompartilhamento();
            $avaliacao->save();
        }

        $link = route('avaliacoes.responder', ['token' => $avaliacao->token_compartilhamento]);

        return response()->json([
            'link' => $link,
            'message' => 'Link copiado para a área de transferência'
        ]);
    }

    /**
     * Regenera o token de compartilhamento e invalida o link anterior.
     */
    public function regenerarLinkCompartilhamento(Request $request, Avaliacao $avaliacao)
    {
        if (!$this->usuarioPodeAcessarAvaliacao($avaliacao)) {
            return $this->respostaAcessoNegado($request, 'Você não tem permissão para regenerar o link desta avaliação.');
        }

        if ($avaliacao->status !== 'pendente') {
            return $this->respostaAcaoInvalida($request, 'Somente avaliações pendentes podem receber um novo link.');
        }

        $avaliacao->token_compartilhamento = Avaliacao::gerarTokenCompartilhamento();
        $avaliacao->save();

        return response()->json([
            'link' => route('avaliacoes.responder', ['token' => $avaliacao->token_compartilhamento]),
            'message' => 'Novo link gerado com sucesso.'
        ]);
    }

    /**
     * Página de preenchimento da avaliação (acesso público via token)
     */
    public function responder($token)
    {
        $avaliacao = Avaliacao::where('token_compartilhamento', $token)
            ->first();

        if (!$avaliacao || !$avaliacao->podeSerAcessada()) {
            return view('avaliacoes.acesso-negado');
        }

        // Carrega as questões padrão se ainda não houver questões_respostas
        if (!$avaliacao->questoes_respostas) {
            $avaliacao->questoes_respostas = $this->avaliacaoService->obterQuestoesBase();
        }

        return view('avaliacoes.responder', compact('avaliacao'));
    }

    /**
     * Salva as respostas da avaliação
     */
    public function salvarRespostas(Request $request, $token)
    {
        $avaliacao = Avaliacao::where('token_compartilhamento', $token)
            ->first();

        if (!$avaliacao || !$avaliacao->podeSerAcessada()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        // Valida email do supervisor
        $request->validate([
            'email_supervisor' => 'required|email',
            'respostas' => 'required|array',
        ]);

        // Processa e salva as respostas
        $questoes_respostas = $avaliacao->questoes_respostas ?? [];
        
        foreach ($questoes_respostas as $index => $questao) {
            if (isset($request->respostas[$index])) {
                $questoes_respostas[$index]['resposta'] = $request->respostas[$index];
            }
        }

        $avaliacao->update([
            'questoes_respostas' => $questoes_respostas,
            'status' => 'respondida',
            'respondida_em' => now(),
            'respondida_por' => $request->email_supervisor,
            'token_compartilhamento' => null, // Invalida o link
        ]);

        return response()->json([
            'message' => 'Avaliação respondida com sucesso!',
        ]);
    }

    /**
     * Gera uma avaliação manualmente
     */
    public function gerarManual(Request $request)
    {
        $request->validate([
            'fk_id_termo' => 'required|exists:tb_termos,id_termo',
            'tipo_avaliacao' => 'required|in:seis_meses,finalizacao',
        ]);

        $termo = Termo::findOrFail($request->fk_id_termo);

        if (!$this->usuarioPodeAcessarTermo($termo)) {
            return redirect()->back()->with('error', 'Você não tem permissão para gerar avaliações para este termo.');
        }

        $tipoAvaliacao = $request->tipo_avaliacao;
        $tipoTexto = $tipoAvaliacao === 'seis_meses' ? '6 meses' : 'finalização';

        // Verifica se termo está ativo
        // EXCEÇÃO: Avaliações de finalização podem ser criadas mesmo em termos rescindidos
        if (!$this->avaliacaoService->termoEstaAtivo($termo) && $tipoAvaliacao !== 'finalizacao') {
            if ($termo->rescisao) {
                return redirect()->back()->with('error', 'Este termo foi rescindido. Apenas avaliações de finalização podem ser criadas para termos rescindidos.');
            }
            
            $dataFim = $termo->data_fim_estagio ?? $termo->data_fim_estagio_fixo;
            if ($dataFim && now()->gt($dataFim)) {
                return redirect()->back()->with('error', 'Este termo já foi finalizado em ' . \Carbon\Carbon::parse($dataFim)->format('d/m/Y') . '. Não é possível criar avaliação de ' . $tipoTexto . '.');
            }
            
            return redirect()->back()->with('error', 'Apenas termos ativos podem ter avaliações de ' . $tipoTexto . '.');
        }

        if ($this->usuarioEhEstagiario()) {
            $avaliacaoPendente = $termo->avaliacoes()
                ->where('status', 'pendente')
                ->first();

            if ($avaliacaoPendente) {
                return redirect()->back()->with('error', 'Você só pode gerar uma avaliação por vez para este contrato. Aguarde a resposta da avaliação pendente antes de criar outra.');
            }
        } else {
            $avaliacaoExistente = $termo->avaliacoes()
                ->where('tipo_avaliacao', $tipoAvaliacao)
                ->where('status', 'pendente')
                ->first();

            if ($avaliacaoExistente) {
                return redirect()->back()->with('error', 'Já existe uma avaliação de ' . $tipoTexto . ' pendente para este termo. Por favor, finalize ou exclua a avaliação existente antes de criar uma nova.');
            }
        }

        // Verifica se já existe avaliação do tipo respondida (caso queira evitar duplicatas)
        $avaliacaoRespondida = $termo->avaliacoes()
            ->where('tipo_avaliacao', $tipoAvaliacao)
            ->where('status', 'respondida')
            ->count();

        if (!$this->usuarioEhEstagiario() && $avaliacaoRespondida > 0 && $tipoAvaliacao === 'seis_meses') {
            return redirect()->back()->with('warning', 'Atenção: Já existe(m) ' . $avaliacaoRespondida . ' avaliação(ões) de ' . $tipoTexto . ' respondida(s) para este termo.');
        }

        $avaliacao = $this->avaliacaoService->criarAvaliacao(
            $termo,
            $tipoAvaliacao,
            $termo->fk_id_supervisor
        );

        if ($this->usuarioEhEstagiario()) {
            return redirect()->route('estagiario.termo.detalhes', $termo->id_termo)
                ->with('success', 'Avaliação de ' . $tipoTexto . ' criada com sucesso!');
        }

        return redirect()->route('avaliacoes.show', $avaliacao)
            ->with('success', 'Avaliação de ' . $tipoTexto . ' criada com sucesso!');
    }

    /**
     * Limpa/reseta uma avaliação respondida para edição novamente
     */
    public function limpar(Request $request, Avaliacao $avaliacao)
    {
        if (!$this->usuarioPodeAcessarAvaliacao($avaliacao)) {
            return $this->respostaAcessoNegado($request, 'Você não tem permissão para limpar esta avaliação.');
        }

        if ($avaliacao->status !== 'respondida') {
            return redirect()->back()->with('error', 'Apenas avaliações respondidas podem ser limpas.');
        }

        $avaliacao->update([
            'status' => 'pendente',
            'questoes_respostas' => null,
            'respondida_em' => null,
            'respondida_por' => null,
            'token_compartilhamento' => Avaliacao::gerarTokenCompartilhamento(),
        ]);

        return redirect()->back()->with('success', 'Avaliação limpa e disponível para nova resposta.');
    }

    /**
     * Exclui uma avaliação
     */
    public function destroy(Request $request, Avaliacao $avaliacao)
    {
        if (!$this->usuarioPodeAcessarAvaliacao($avaliacao)) {
            return $this->respostaAcessoNegado($request, 'Você não tem permissão para excluir esta avaliação.');
        }

        $avaliacao->delete();

        return redirect()->back()->with('success', 'Avaliação removida com sucesso!');
    }

    /**
     * Retorna contagem de avaliações pendentes (para navbar)
     */
    public function contadorPendentes()
    {
        $count = Avaliacao::where('status', 'pendente')->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Download do PDF da avaliação (apenas admin/operador via painel)
     */
    public function pdf(Request $request, Avaliacao $avaliacao)
    {
        if (!$this->usuarioPodeAcessarAvaliacao($avaliacao)) {
            return $this->respostaAcessoNegado($request, 'Você não tem permissão para gerar o PDF desta avaliação.');
        }

        if ($avaliacao->status !== 'respondida') {
            return redirect()->back()->with('error', 'Somente avaliações respondidas podem gerar PDF.');
        }

        $response = $this->avaliacaoPdfService->download($avaliacao);
        if (!$response) {
            return redirect()->back()->with('error', 'Não foi possível gerar o PDF desta avaliação.');
        }
        return $response;
    }
}
