@extends('layouts.main')

@section('title', 'Financeiro')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-11">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-wallet"></i> Módulo Financeiro</h4>
                        <a href="{{ route('welcome') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-0">
                            Gerencie as finanças da organização em um só lugar. Cadastre contas de receita e despesa,
                            registre os lançamentos mês a mês e acompanhe o consolidado anual —
                            com totais por conta, saldo mensal e visão completa do ano em uma única tabela.
                        </p>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100 border-0 bg-success-subtle">
                            <div class="card-body">
                                <div class="text-muted small">Contas de Receita</div>
                                <div class="display-6 fw-semibold">{{ $totais['contas_receita'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100 border-0 bg-danger-subtle">
                            <div class="card-body">
                                <div class="text-muted small">Contas de Despesa</div>
                                <div class="display-6 fw-semibold">{{ $totais['contas_despesa'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100 border-0 bg-info-subtle">
                            <div class="card-body">
                                <div class="text-muted small">Contas Ativas</div>
                                <div class="display-6 fw-semibold">{{ $totais['contas_ativas'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100 border-0 bg-warning-subtle">
                            <div class="card-body">
                                <div class="text-muted small">Lançamentos</div>
                                <div class="display-6 fw-semibold">{{ $totais['lancamentos'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5><i class="fas fa-list me-2 text-primary"></i>Contas</h5>
                                <p class="text-muted small">Cadastre e organize receitas e despesas reutilizáveis no ano
                                    todo.</p>
                                <a href="{{ route('financeiro.contas.index') }}" class="btn btn-primary btn-sm">Gerenciar
                                    Contas</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5><i class="fas fa-calendar-alt me-2 text-success"></i>Lançamentos Mensais</h5>
                                <p class="text-muted small">Estrutura pronta para visualizar os lançamentos por mês e ano.
                                </p>
                                <a href="{{ route('financeiro.lancamentos.index') }}"
                                    class="btn btn-outline-success btn-sm">Abrir Lançamentos</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5><i class="fas fa-chart-bar me-2 text-dark"></i>Acumulado Anual</h5>
                                <p class="text-muted small">Consolidado anual por conta, com totais de receitas, despesas e
                                    saldo.</p>
                                <a href="{{ route('financeiro.acumulado.index') }}" class="btn btn-outline-dark btn-sm">Ver
                                    Acumulado</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <strong><i class="fas fa-clock me-2 text-secondary"></i>Contas cadastradas recentemente</strong>
                    </div>
                    <div class="card-body">
                        @if($ultimasContas->isEmpty())
                            <div class="alert alert-info mb-0">
                                Nenhuma conta financeira foi cadastrada ainda.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Conta</th>
                                            <th>Tipo</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ultimasContas as $conta)
                                            <tr>
                                                <td>{{ $conta->nome_conta }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $conta->tipo_conta === 'receita' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ ucfirst($conta->tipo_conta) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $conta->ativo ? 'bg-primary' : 'bg-secondary' }}">
                                                        {{ $conta->ativo ? 'Ativa' : 'Inativa' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection