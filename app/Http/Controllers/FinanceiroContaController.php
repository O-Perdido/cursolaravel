<?php

namespace App\Http\Controllers;

use App\Models\FinanceiroConta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FinanceiroContaController extends Controller
{
    public function index()
    {
        $this->garantirAdmin();

        $contasReceita = FinanceiroConta::doTipo('receita')->ordenadas()->get();
        $contasDespesa = FinanceiroConta::doTipo('despesa')->ordenadas()->get();

        return view('financeiro.contas.index', compact('contasReceita', 'contasDespesa'));
    }

    public function create()
    {
        $this->garantirAdmin();

        return view('financeiro.contas.create');
    }

    public function store(Request $request)
    {
        $this->garantirAdmin();

        $validator = Validator::make($request->all(), [
            'tipo_conta' => 'required|in:receita,despesa',
            'nome_conta' => 'required|string|max:150|unique:tb_financeiro_contas,nome_conta',
            'ativo' => 'nullable|boolean',
        ], [
            'tipo_conta.required' => 'O tipo da conta é obrigatório.',
            'nome_conta.required' => 'O nome da conta é obrigatório.',
            'nome_conta.unique' => 'Já existe uma conta com este nome.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        FinanceiroConta::create([
            'tipo_conta' => $request->input('tipo_conta'),
            'nome_conta' => trim((string) $request->input('nome_conta')),
            'ativo' => $request->boolean('ativo', true),
        ]);

        return redirect()->route('financeiro.contas.index')
            ->with('success', 'Conta financeira criada com sucesso.');
    }

    public function edit($id)
    {
        $this->garantirAdmin();

        $conta = FinanceiroConta::findOrFail($id);

        return view('financeiro.contas.edit', compact('conta'));
    }

    public function update(Request $request, $id)
    {
        $this->garantirAdmin();

        $conta = FinanceiroConta::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipo_conta' => 'required|in:receita,despesa',
            'nome_conta' => 'required|string|max:150|unique:tb_financeiro_contas,nome_conta,' . $id . ',id_financeiro_conta',
            'ativo' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $conta->update([
            'tipo_conta' => $request->input('tipo_conta'),
            'nome_conta' => trim((string) $request->input('nome_conta')),
            'ativo' => (bool) $request->input('ativo'),
        ]);

        return redirect()->route('financeiro.contas.index')
            ->with('success', 'Conta financeira atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $this->garantirAdmin();

        $conta = FinanceiroConta::withCount('lancamentos')->findOrFail($id);

        if ($conta->lancamentos_count > 0) {
            return back()->with('error', 'Esta conta possui lançamentos vinculados e não pode ser removida. Inative a conta em vez de excluir.');
        }

        $conta->delete();

        return back()->with('success', 'Conta financeira removida com sucesso.');
    }

    private function garantirAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()->nivel === 'admin', 403);
    }
}