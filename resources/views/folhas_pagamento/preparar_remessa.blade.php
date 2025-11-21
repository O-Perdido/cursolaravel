@extends('layouts.main')

@section('title', 'Preparar Remessa Bancária')

@section('content')

<style>
.info-card { border-left: 4px solid #667eea; background: #f8f9fc; padding: 1rem; margin-bottom: 1rem; border-radius: 0.5rem; }
.lote-card { border: 2px solid #e3e6f0; border-radius: 0.5rem; margin-bottom: 1.5rem; transition: all 0.3s ease; }
.lote-card:hover { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1); }
.lote-header { background: #f8f9fc; border-bottom: 2px solid #e3e6f0; padding: 1.25rem; border-radius: 0.5rem 0.5rem 0 0; }
.badge-lote { font-size: 1rem; padding: 0.5rem 1rem; }
.details-table thead th { background: #f8f9fc; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; color: #6c757d; border-bottom: 2px solid #e3e6f0; }
.details-table tbody tr:hover { background: #f8f9fc; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="fas fa-file-export text-primary"></i> Preparar Remessa Bancária</h4>
        <small class="text-muted">Folha {{ $folha->numero_folha }}/{{ $folha->ano_referencia }} - {{ $folha->empresa->nome_empresa }}</small>
    </div>
    <a href="{{ route('folhas.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="info-card">
            <h6 class="text-muted mb-1" style="font-size: 0.875rem;"><i class="fas fa-money-bill-wave"></i> Valor Total da Folha</h6>
            <h3 class="mb-0 text-primary font-weight-bold">R$ {{ number_format($totalGeral, 2, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-card">
            <h6 class="text-muted mb-1" style="font-size: 0.875rem;"><i class="fas fa-shield-alt"></i> Limite Diário Configurado</h6>
            <h3 class="mb-0 text-info font-weight-bold">R$ {{ number_format($limiteDiario, 2, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-card">
            <h6 class="text-muted mb-1" style="font-size: 0.875rem;"><i class="fas fa-users"></i> Total de Estagiários</h6>
            <h3 class="mb-0 text-success font-weight-bold">{{ $quantidadeTotal }}</h3>
        </div>
    </div>
</div>

@if(count($lotes) > 1)
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
        <div><strong>Atenção!</strong> O valor total da folha (R$ {{ number_format($totalGeral, 2, ',', '.') }}) ultrapassa o limite diário de R$ {{ number_format($limiteDiario, 2, ',', '.') }}.<br><small>A remessa foi dividida automaticamente em <strong>{{ count($lotes) }} lotes</strong>.</small></div>
    </div>
@else
    <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle fa-2x me-3"></i>
        <div><strong>Tudo certo!</strong> O valor total está dentro do limite diário.<br><small>Será gerado apenas 1 arquivo de remessa.</small></div>
    </div>
@endif

<h5 class="mb-3"><i class="fas fa-layer-group"></i> Arquivos de Remessa ({{ count($lotes) }})</h5>

@foreach($lotes as $lote)
<div class="lote-card">
    <div class="lote-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1"><span class="badge bg-primary badge-lote">Lote {{ $lote['numero'] }}</span> <span class="text-muted" style="font-size: 0.9rem;">de {{ count($lotes) }}</span></h5>
                <p class="mb-0 text-muted"><i class="fas fa-user-friends"></i> {{ $lote['quantidade'] }} estagiários <span class="mx-2"></span> <i class="fas fa-dollar-sign"></i> <strong>R$ {{ number_format($lote['total'], 2, ',', '.') }}</strong></p>
            </div>
            <div>
                <form action="{{ route('folha_pagamento.gerarRemessaLote', $folha->id_folha_pagamento) }}" method="POST">
                    @csrf
                    <input type="hidden" name="numero_lote" value="{{ $lote['numero'] }}">
                    @foreach($lote['ids'] as $id)
                        <input type="hidden" name="ids_itens[]" value="{{ $id }}">
                    @endforeach
                    <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-download"></i> Gerar Arquivo {{ $lote['numero'] }}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="p-3">
        <button class="btn btn-link btn-sm text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#detalhesLote{{ $lote['numero'] }}" aria-expanded="false"><i class="fas fa-chevron-down"></i> Ver detalhes dos {{ $lote['quantidade'] }} pagamentos</button>
        <div class="collapse" id="detalhesLote{{ $lote['numero'] }}">
            <div class="table-responsive mt-3">
                <table class="table table-sm table-hover details-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 50%;">Estagiário</th>
                            <th style="width: 25%;">CPF</th>
                            <th style="width: 20%; text-align: right;">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lote['items'] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->termo->estagiario->nome_estagiario }}</td>
                            <td>{{ $item->termo->estagiario->numero_cpf }}</td>
                            <td style="text-align: right;"><strong>R$ {{ number_format($item->total, 2, ',', '.') }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8f9fc; font-weight: bold;">
                            <td colspan="3" style="text-align: right; padding: 0.75rem;">Subtotal do Lote {{ $lote['numero'] }}:</td>
                            <td style="text-align: right; padding: 0.75rem;"><span class="text-primary">R$ {{ number_format($lote['total'], 2, ',', '.') }}</span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endforeach

@if(count($lotes) > 1)
<div class="alert alert-info mt-4">
    <i class="fas fa-lightbulb"></i> <strong>Dica:</strong> Você pode gerar os arquivos em dias diferentes para respeitar o limite bancário, ou ajustar o limite em @if (Auth::user()->nivel == 'admin')<a href="{{ route('configuracoes.index') }}" class="alert-link">Configurações do Sistema</a>.@else<strong>Configurações do Sistema</strong> (solicite ao administrador).@endif
</div>
@endif

@endsection
