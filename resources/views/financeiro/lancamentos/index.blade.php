@extends('layouts.main')

@section('title', 'Lançamentos Financeiros')

@section('content')
    <div class="container mt-4">

        {{-- Cabeçalho + filtro de período --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-calendar-days"></i> Lançamentos Mensais</h4>
                <a href="{{ route('financeiro.index') }}" class="btn btn-sm btn-light">
                    <i class="fas fa-arrow-left"></i> Financeiro
                </a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('financeiro.lancamentos.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="ano" class="form-label fw-bold">Ano</label>
                        <input type="number" name="ano" id="ano" class="form-control" value="{{ $anoSelecionado }}"
                            min="2000" max="2100">
                    </div>
                    <div class="col-md-3">
                        <label for="mes" class="form-label fw-bold">Mês</label>
                        <select name="mes" id="mes" class="form-select">
                            @php
                                $nomesMeses = [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
                                               7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];
                            @endphp
                            @foreach($nomesMeses as $num => $nome)
                                <option value="{{ $num }}" {{ $mesSelecionado === $num ? 'selected' : '' }}>
                                    {{ $nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Alertas de sessão --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Cards de totais --}}
        @php $saldo = $totalReceitas - $totalDespesas; @endphp
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-success-subtle">
                    <div class="card-body">
                        <div class="small text-muted">Total de Receitas</div>
                        <div class="h4 mb-0 text-success">R$ {{ number_format((float) $totalReceitas, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-danger-subtle">
                    <div class="card-body">
                        <div class="small text-muted">Total de Despesas</div>
                        <div class="h4 mb-0 text-danger">R$ {{ number_format((float) $totalDespesas, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 {{ $saldo >= 0 ? 'bg-info-subtle' : 'bg-warning-subtle' }}">
                    <div class="card-body">
                        <div class="small text-muted">Saldo</div>
                        <div class="h4 mb-0 {{ $saldo >= 0 ? 'text-info' : 'text-warning' }}">
                            R$ {{ number_format((float) $saldo, 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulário de novo lançamento --}}
        @if($contasReceita->isEmpty() && $contasDespesa->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-triangle-exclamation"></i>
                Nenhuma conta financeira ativa cadastrada. <a href="{{ route('financeiro.contas.create') }}">Cadastre contas</a> para lançar valores.
            </div>
        @else
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-plus-circle text-primary"></i> Novo Lançamento —
                    {{ $nomesMeses[$mesSelecionado] }}/{{ $anoSelecionado }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('financeiro.lancamentos.store') }}" class="row g-3 align-items-end">
                        @csrf
                        <input type="hidden" name="ano_referencia" value="{{ $anoSelecionado }}">
                        <input type="hidden" name="mes_referencia" value="{{ $mesSelecionado }}">

                        <div class="col-md-5">
                            <label for="fk_id_financeiro_conta" class="form-label fw-bold">Conta</label>
                            <select name="fk_id_financeiro_conta" id="fk_id_financeiro_conta"
                                class="form-select @error('fk_id_financeiro_conta') is-invalid @enderror" required>
                                <option value="">— Selecione —</option>
                                @if($contasReceita->isNotEmpty())
                                    <optgroup label="Receitas">
                                        @foreach($contasReceita as $conta)
                                            <option value="{{ $conta->id_financeiro_conta }}"
                                                {{ old('fk_id_financeiro_conta') == $conta->id_financeiro_conta ? 'selected' : '' }}>
                                                {{ $conta->nome_conta }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if($contasDespesa->isNotEmpty())
                                    <optgroup label="Despesas">
                                        @foreach($contasDespesa as $conta)
                                            <option value="{{ $conta->id_financeiro_conta }}"
                                                {{ old('fk_id_financeiro_conta') == $conta->id_financeiro_conta ? 'selected' : '' }}>
                                                {{ $conta->nome_conta }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                            @error('fk_id_financeiro_conta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label for="valor" class="form-label fw-bold">Valor (R$)</label>
                            <input type="number" name="valor" id="valor" step="0.01" min="0.01"
                                class="form-control @error('valor') is-invalid @enderror"
                                value="{{ old('valor') }}" placeholder="0,00" required>
                            @error('valor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="observacao" class="form-label fw-bold">Observação</label>
                            <input type="text" name="observacao" id="observacao" maxlength="500"
                                class="form-control @error('observacao') is-invalid @enderror"
                                value="{{ old('observacao') }}" placeholder="Opcional">
                            @error('observacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Adicionar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Tabela de lançamentos do período --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>
                    Lançamentos de {{ $nomesMeses[$mesSelecionado] }}/{{ $anoSelecionado }}
                </strong>
                <span class="badge bg-secondary">{{ $lancamentos->count() }} registro(s)</span>
            </div>
            <div class="card-body p-0">
                @if($lancamentos->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        Nenhum lançamento para {{ $nomesMeses[$mesSelecionado] }}/{{ $anoSelecionado }}.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Conta</th>
                                    <th>Tipo</th>
                                    <th class="text-end">Valor</th>
                                    <th>Observação</th>
                                    <th class="text-center" style="width:130px">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lancamentos as $lancamento)
                                    <tr id="row-{{ $lancamento->id_financeiro_lancamento }}">
                                        {{-- Modo visualização --}}
                                        <td class="view-mode">{{ $lancamento->conta?->nome_conta ?? 'Conta removida' }}</td>
                                        <td class="view-mode">
                                            @if($lancamento->conta?->tipo_conta === 'receita')
                                                <span class="badge bg-success">Receita</span>
                                            @elseif($lancamento->conta?->tipo_conta === 'despesa')
                                                <span class="badge bg-danger">Despesa</span>
                                            @else
                                                <span class="badge bg-secondary">—</span>
                                            @endif
                                        </td>
                                        <td class="view-mode text-end">R$ {{ number_format((float) $lancamento->valor, 2, ',', '.') }}</td>
                                        <td class="view-mode">{{ $lancamento->observacao ?: '—' }}</td>
                                        <td class="view-mode text-center">
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                onclick="abrirEdicao({{ $lancamento->id_financeiro_lancamento }})">
                                                <i class="fas fa-pencil"></i>
                                            </button>
                                            <form method="POST"
                                                action="{{ route('financeiro.lancamentos.destroy', $lancamento->id_financeiro_lancamento) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('Excluir este lançamento?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>

                                        {{-- Modo edição (oculto por padrão) --}}
                                        <td class="edit-mode d-none" colspan="4">
                                            <form method="POST"
                                                action="{{ route('financeiro.lancamentos.update', $lancamento->id_financeiro_lancamento) }}"
                                                class="row g-2 align-items-end py-1">
                                                @csrf
                                                @method('PUT')
                                                <div class="col-md-4">
                                                    <select name="fk_id_financeiro_conta" class="form-select form-select-sm" required>
                                                        @if($contasReceita->isNotEmpty())
                                                            <optgroup label="Receitas">
                                                                @foreach($contasReceita as $conta)
                                                                    <option value="{{ $conta->id_financeiro_conta }}"
                                                                        {{ $lancamento->fk_id_financeiro_conta == $conta->id_financeiro_conta ? 'selected' : '' }}>
                                                                        {{ $conta->nome_conta }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endif
                                                        @if($contasDespesa->isNotEmpty())
                                                            <optgroup label="Despesas">
                                                                @foreach($contasDespesa as $conta)
                                                                    <option value="{{ $conta->id_financeiro_conta }}"
                                                                        {{ $lancamento->fk_id_financeiro_conta == $conta->id_financeiro_conta ? 'selected' : '' }}>
                                                                        {{ $conta->nome_conta }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="valor" step="0.01" min="0.01"
                                                        class="form-control form-control-sm"
                                                        value="{{ number_format((float)$lancamento->valor, 2, '.', '') }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" name="observacao" maxlength="500"
                                                        class="form-control form-control-sm"
                                                        value="{{ $lancamento->observacao }}" placeholder="Observação">
                                                </div>
                                                <div class="col-md-3 d-flex gap-1">
                                                    <button type="submit" class="btn btn-sm btn-success flex-fill">
                                                        <i class="fas fa-check"></i> Salvar
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-secondary flex-fill"
                                                        onclick="fecharEdicao({{ $lancamento->id_financeiro_lancamento }})">
                                                        Cancelar
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="edit-mode d-none"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <script>
        function abrirEdicao(id) {
            const row = document.getElementById('row-' + id);
            row.querySelectorAll('.view-mode').forEach(el => el.classList.add('d-none'));
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('d-none'));
        }
        function fecharEdicao(id) {
            const row = document.getElementById('row-' + id);
            row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('d-none'));
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('d-none'));
        }
    </script>
@endsection