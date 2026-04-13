<?php

namespace App\Http\Controllers;

use App\Models\FinanceiroConta;
use App\Models\FinanceiroLancamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceiroLancamentoController extends Controller
{
    public function store(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->nivel === 'admin', 403);

        $validated = $request->validate([
            'fk_id_financeiro_conta' => ['required', 'integer', 'exists:tb_financeiro_contas,id_financeiro_conta'],
            'ano_referencia'         => ['required', 'integer', 'min:2000', 'max:2100'],
            'mes_referencia'         => ['required', 'integer', 'min:1', 'max:12'],
            'valor'                  => ['required', 'numeric', 'min:0.01'],
            'observacao'             => ['nullable', 'string', 'max:500'],
        ], [
            'fk_id_financeiro_conta.required' => 'Selecione uma conta.',
            'fk_id_financeiro_conta.exists'   => 'Conta inválida.',
            'ano_referencia.required'         => 'Informe o ano.',
            'mes_referencia.required'         => 'Informe o mês.',
            'valor.required'                  => 'Informe o valor.',
            'valor.min'                       => 'O valor deve ser maior que zero.',
        ]);

        FinanceiroLancamento::create([
            'fk_id_financeiro_conta'       => $validated['fk_id_financeiro_conta'],
            'fk_id_usuario_criacao'        => Auth::id(),
            'fk_id_usuario_atualizacao'    => Auth::id(),
            'ano_referencia'               => $validated['ano_referencia'],
            'mes_referencia'               => $validated['mes_referencia'],
            'valor'                        => $validated['valor'],
            'observacao'                   => $validated['observacao'] ?? null,
        ]);

        return redirect()
            ->route('financeiro.lancamentos.index', [
                'ano' => $validated['ano_referencia'],
                'mes' => $validated['mes_referencia'],
            ])
            ->with('success', 'Lançamento adicionado com sucesso.');
    }

    public function update(Request $request, int $id)
    {
        abort_unless(Auth::check() && Auth::user()->nivel === 'admin', 403);

        $lancamento = FinanceiroLancamento::findOrFail($id);

        $validated = $request->validate([
            'fk_id_financeiro_conta' => ['required', 'integer', 'exists:tb_financeiro_contas,id_financeiro_conta'],
            'valor'                  => ['required', 'numeric', 'min:0.01'],
            'observacao'             => ['nullable', 'string', 'max:500'],
        ], [
            'fk_id_financeiro_conta.required' => 'Selecione uma conta.',
            'fk_id_financeiro_conta.exists'   => 'Conta inválida.',
            'valor.required'                  => 'Informe o valor.',
            'valor.min'                       => 'O valor deve ser maior que zero.',
        ]);

        $lancamento->update([
            'fk_id_financeiro_conta'    => $validated['fk_id_financeiro_conta'],
            'fk_id_usuario_atualizacao' => Auth::id(),
            'valor'                     => $validated['valor'],
            'observacao'                => $validated['observacao'] ?? null,
        ]);

        return redirect()
            ->route('financeiro.lancamentos.index', [
                'ano' => $lancamento->ano_referencia,
                'mes' => $lancamento->mes_referencia,
            ])
            ->with('success', 'Lançamento atualizado com sucesso.');
    }

    public function destroy(int $id)
    {
        abort_unless(Auth::check() && Auth::user()->nivel === 'admin', 403);

        $lancamento = FinanceiroLancamento::findOrFail($id);
        $ano = $lancamento->ano_referencia;
        $mes = $lancamento->mes_referencia;

        $lancamento->delete();

        return redirect()
            ->route('financeiro.lancamentos.index', ['ano' => $ano, 'mes' => $mes])
            ->with('success', 'Lançamento excluído com sucesso.');
    }
}
