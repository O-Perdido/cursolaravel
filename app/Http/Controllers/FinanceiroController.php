<?php

namespace App\Http\Controllers;

use App\Models\FinanceiroConta;
use App\Models\FinanceiroLancamento;
use Illuminate\Support\Facades\Auth;

class FinanceiroController extends Controller
{
    public function index()
    {
        $this->garantirAdmin();

        $totais = [
            'contas_receita' => FinanceiroConta::doTipo('receita')->count(),
            'contas_despesa' => FinanceiroConta::doTipo('despesa')->count(),
            'contas_ativas' => FinanceiroConta::ativas()->count(),
            'lancamentos' => FinanceiroLancamento::count(),
        ];

        $ultimasContas = FinanceiroConta::ordenadas()->limit(8)->get();

        return view('financeiro.index', compact('totais', 'ultimasContas'));
    }

    public function lancamentos()
    {
        $this->garantirAdmin();

        $anoSelecionado = (int) request('ano', now()->year);
        $mesSelecionado = (int) request('mes', now()->month);

        $contasReceita = FinanceiroConta::ativas()->doTipo('receita')->ordenadas()->get();
        $contasDespesa = FinanceiroConta::ativas()->doTipo('despesa')->ordenadas()->get();

        $lancamentos = FinanceiroLancamento::with('conta')
            ->where('ano_referencia', $anoSelecionado)
            ->where('mes_referencia', $mesSelecionado)
            ->orderByDesc('created_at')
            ->get();

        $totalReceitas = $lancamentos->filter(fn ($lancamento) => $lancamento->conta?->tipo_conta === 'receita')->sum('valor');
        $totalDespesas = $lancamentos->filter(fn ($lancamento) => $lancamento->conta?->tipo_conta === 'despesa')->sum('valor');

        return view('financeiro.lancamentos.index', compact(
            'anoSelecionado',
            'mesSelecionado',
            'contasReceita',
            'contasDespesa',
            'lancamentos',
            'totalReceitas',
            'totalDespesas'
        ));
    }

    public function acumulado()
    {
        $this->garantirAdmin();

        $anoSelecionado = (int) request('ano', now()->year);
        $meses = range(1, 12);

        // Busca totais por conta+mês
        $rows = FinanceiroLancamento::query()
            ->selectRaw('fk_id_financeiro_conta, mes_referencia, SUM(valor) as total')
            ->where('ano_referencia', $anoSelecionado)
            ->groupBy('fk_id_financeiro_conta', 'mes_referencia')
            ->get();

        // Monta pivot: [conta_id][mes] = total
        $pivot = [];
        foreach ($rows as $row) {
            $pivot[$row->fk_id_financeiro_conta][$row->mes_referencia] = (float) $row->total;
        }

        // Contas que aparecem no pivot, ordenadas por tipo + ordem + nome
        $contaIds = array_keys($pivot);
        $contas = FinanceiroConta::whereIn('id_financeiro_conta', $contaIds)
            ->get()
            ->sortBy(fn ($c) => $c->tipo_conta . '|' . $c->nome_conta)
            ->values();

        $contasReceita = $contas->where('tipo_conta', 'receita')->values();
        $contasDespesa = $contas->where('tipo_conta', 'despesa')->values();

        // Total anual por conta
        $totalAnualPorConta = [];
        foreach ($pivot as $contaId => $mesTotais) {
            $totalAnualPorConta[$contaId] = array_sum($mesTotais);
        }

        // Totais de receita e despesa por mês
        $totalReceitaPorMes = [];
        $totalDespesaPorMes = [];
        foreach ($meses as $m) {
            $totalReceitaPorMes[$m] = $contasReceita->sum(fn ($c) => $pivot[$c->id_financeiro_conta][$m] ?? 0.0);
            $totalDespesaPorMes[$m] = $contasDespesa->sum(fn ($c) => $pivot[$c->id_financeiro_conta][$m] ?? 0.0);
        }

        $totalReceitas = array_sum($totalReceitaPorMes);
        $totalDespesas = array_sum($totalDespesaPorMes);

        return view('financeiro.acumulado.index', compact(
            'anoSelecionado',
            'meses',
            'contas',
            'contasReceita',
            'contasDespesa',
            'pivot',
            'totalAnualPorConta',
            'totalReceitaPorMes',
            'totalDespesaPorMes',
            'totalReceitas',
            'totalDespesas'
        ));
    }

    private function garantirAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()->nivel === 'admin', 403);
    }
}