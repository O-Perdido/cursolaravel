@extends('layouts.main')

@section('title', 'Acumulado Financeiro')

@section('content')
    @php
        $nomesMeses = [
            1 => 'Jan',
            2 => 'Fev',
            3 => 'Mar',
            4 => 'Abr',
            5 => 'Mai',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ago',
            9 => 'Set',
            10 => 'Out',
            11 => 'Nov',
            12 => 'Dez',
        ];
        $saldoAnual = $totalReceitas - $totalDespesas;
    @endphp

    <div class="container-fluid mt-4">

        {{-- Cabeçalho + filtro --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-chart-column"></i> Acumulado Anual</h4>
                <a href="{{ route('financeiro.index') }}" class="btn btn-sm btn-light">
                    <i class="fas fa-arrow-left"></i> Financeiro
                </a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('financeiro.acumulado.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="ano" class="form-label fw-bold">Ano</label>
                        <input type="number" name="ano" id="ano" class="form-control" value="{{ $anoSelecionado }}"
                            min="2000" max="2100">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Cards de totais --}}
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
                <div class="card shadow-sm border-0 {{ $saldoAnual >= 0 ? 'bg-info-subtle' : 'bg-warning-subtle' }}">
                    <div class="card-body">
                        <div class="small text-muted">Saldo Anual</div>
                        <div class="h4 mb-0 {{ $saldoAnual >= 0 ? 'text-info' : 'text-warning' }}">
                            R$ {{ number_format((float) $saldoAnual, 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabela pivot --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Consolidado por conta — {{ $anoSelecionado }}</strong>
                <span class="text-muted small">Valores em R$</span>
            </div>
            <div class="card-body p-0">
                @if($contas->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        Nenhum lançamento encontrado para {{ $anoSelecionado }}.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0 text-nowrap">
                            <thead class="table-dark">
                                <tr>
                                    <th style="min-width:180px">Conta</th>
                                    @foreach($nomesMeses as $num => $abrev)
                                        <th class="text-end">{{ $abrev }}</th>
                                    @endforeach
                                    <th class="text-end">Total Ano</th>
                                </tr>
                            </thead>
                            <tbody>

                                {{-- ===== RECEITAS ===== --}}
                                @if($contasReceita->isNotEmpty())
                                    <tr class="table-success">
                                        <td colspan="14" class="fw-bold text-uppercase small py-1 ps-2">
                                            <i class="fas fa-arrow-up"></i> Receitas
                                        </td>
                                    </tr>
                                    @foreach($contasReceita as $conta)
                                        <tr>
                                            <td class="ps-3">{{ $conta->nome_conta }}</td>
                                            @foreach($meses as $m)
                                                @php $val = $pivot[$conta->id_financeiro_conta][$m] ?? 0; @endphp
                                                <td class="text-end {{ $val > 0 ? '' : 'text-muted' }}">
                                                    {{ $val > 0 ? number_format($val, 2, ',', '.') : '—' }}
                                                </td>
                                            @endforeach
                                            <td class="text-end fw-bold text-success">
                                                {{ number_format($totalAnualPorConta[$conta->id_financeiro_conta] ?? 0, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-success fw-bold border-top border-2">
                                        <td class="ps-2">Total Receitas</td>
                                        @foreach($meses as $m)
                                            <td class="text-end">{{ number_format($totalReceitaPorMes[$m], 2, ',', '.') }}</td>
                                        @endforeach
                                        <td class="text-end">{{ number_format($totalReceitas, 2, ',', '.') }}</td>
                                    </tr>
                                @endif

                                {{-- ===== DESPESAS ===== --}}
                                @if($contasDespesa->isNotEmpty())
                                    <tr class="table-danger">
                                        <td colspan="14" class="fw-bold text-uppercase small py-1 ps-2">
                                            <i class="fas fa-arrow-down"></i> Despesas
                                        </td>
                                    </tr>
                                    @foreach($contasDespesa as $conta)
                                        <tr>
                                            <td class="ps-3">{{ $conta->nome_conta }}</td>
                                            @foreach($meses as $m)
                                                @php $val = $pivot[$conta->id_financeiro_conta][$m] ?? 0; @endphp
                                                <td class="text-end {{ $val > 0 ? '' : 'text-muted' }}">
                                                    {{ $val > 0 ? number_format($val, 2, ',', '.') : '—' }}
                                                </td>
                                            @endforeach
                                            <td class="text-end fw-bold text-danger">
                                                {{ number_format($totalAnualPorConta[$conta->id_financeiro_conta] ?? 0, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-danger fw-bold border-top border-2">
                                        <td class="ps-2">Total Despesas</td>
                                        @foreach($meses as $m)
                                            <td class="text-end">{{ number_format($totalDespesaPorMes[$m], 2, ',', '.') }}</td>
                                        @endforeach
                                        <td class="text-end">{{ number_format($totalDespesas, 2, ',', '.') }}</td>
                                    </tr>
                                @endif

                                {{-- ===== SALDO POR MÊS ===== --}}
                                <tr class="table-dark fw-bold">
                                    <td class="ps-2">Saldo</td>
                                    @foreach($meses as $m)
                                        @php $saldoMes = ($totalReceitaPorMes[$m] ?? 0) - ($totalDespesaPorMes[$m] ?? 0); @endphp
                                        <td class="text-end {{ $saldoMes >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($saldoMes, 2, ',', '.') }}
                                        </td>
                                    @endforeach
                                    <td class="text-end {{ $saldoAnual >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($saldoAnual, 2, ',', '.') }}
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection