@extends('layouts.main')

@section('title', 'SIGE Concursos | Auditoria de Cobrancas Inter')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Auditoria de Cobrancas Inter</h2>
            <p class="text-muted mb-0">Acompanhe falhas, sincronizacoes e reprocessamento das cobrancas de taxa.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sigeconcursos.dashboard') }}" class="btn btn-outline-secondary btn-sm">Voltar ao
                dashboard</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Com cobranca</div>
                    <div class="h3 mb-0">{{ $resumo['com_cobranca'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Pagas</div>
                    <div class="h3 mb-0 text-success">{{ $resumo['pagas'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Pendentes</div>
                    <div class="h3 mb-0 text-warning">{{ $resumo['pendentes'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Falhas</div>
                    <div class="h3 mb-0 text-danger">{{ $resumo['falhas'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label mb-1">Busca</label>
                    <input type="text" name="busca" class="form-control form-control-sm" value="{{ request('busca') }}"
                        placeholder="Inscricao, codigo ou candidato">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Status pagamento</label>
                    <select name="status_pagamento" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach(['pendente', 'pago', 'isento', 'nao_aplicavel'] as $status)
                            <option value="{{ $status }}" {{ request('status_pagamento') === $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Situacao Inter</label>
                    <select name="inter_situacao" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach(['A_RECEBER', 'RECEBIDO', 'ATRASADO', 'CANCELADO', 'EXPIRADO', 'FALHA_EMISSAO', 'EM_PROCESSAMENTO'] as $situacao)
                            <option value="{{ $situacao }}" {{ request('inter_situacao') === $situacao ? 'selected' : '' }}>
                                {{ ucfirst(strtolower(str_replace('_', ' ', $situacao))) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-check pt-4">
                    <input class="form-check-input" type="checkbox" name="somente_falhas" value="1" id="somente_falhas" {{ request()->boolean('somente_falhas') ? 'checked' : '' }}>
                    <label class="form-check-label" for="somente_falhas">Somente falhas</label>
                </div>
                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    <a href="{{ route('sigeconcursos.cobrancas.index') }}"
                        class="btn btn-outline-secondary btn-sm">Limpar</a>
                </div>
            </div>
        </div>
    </form>

    <form method="POST" action="{{ route('sigeconcursos.cobrancas.reprocessar-lote') }}"
        class="card border-0 shadow-sm mb-4">
        @csrf
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Cobrancas</strong>
            <button type="submit" class="btn btn-sm btn-warning">Reprocessar selecionadas</button>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selecionar-todas"></th>
                        <th>Inscricao</th>
                        <th>Candidato</th>
                        <th>Processo</th>
                        <th>Pagamento</th>
                        <th>Inter</th>
                        <th>Ult. sync</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inscricoes as $inscricao)
                        <tr>
                            <td><input type="checkbox" name="inscricao_ids[]" value="{{ $inscricao->id_inscricao }}"
                                    class="chk-inscricao"></td>
                            <td>
                                <div class="fw-semibold">{{ $inscricao->numero_inscricao ?: '-' }}</div>
                                <div class="small text-muted">
                                    {{ $inscricao->inter_codigo_solicitacao ?: 'Sem cobranca emitida' }}
                                </div>
                            </td>
                            <td>{{ $inscricao->candidato?->nome_completo }}</td>
                            <td>{{ $inscricao->processo?->titulo }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $inscricao->status_pagamento === 'pago' ? 'success' : ($inscricao->status_pagamento === 'pendente' ? 'warning text-dark' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $inscricao->status_pagamento)) }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $inscricao->inter_situacao ?: '-' }}</div>
                                @if($inscricao->inter_linha_digitavel)
                                    <div class="small text-muted">Linha digitavel disponivel</div>
                                @endif
                            </td>
                            <td>{{ $inscricao->inter_ultima_sincronizacao_em?->format('d/m/Y H:i') ?: '-' }}</td>
                            <td>
                                <form method="POST"
                                    action="{{ route('sigeconcursos.cobrancas.sincronizar', $inscricao->id_inscricao) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Sincronizar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Nenhuma cobranca encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    @if($inscricoes->hasPages())
        <div class="mt-4 d-flex justify-content-center">{{ $inscricoes->links() }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><strong>Logs recentes</strong></div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Quando</th>
                        <th>Evento</th>
                        <th>Codigo</th>
                        <th>Inscricao</th>
                        <th>Status</th>
                        <th>HTTP</th>
                        <th>Mensagem</th>
                        <th>Detalhe tecnico</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logsRecentes as $log)
                        @php
                            $payloadResponse = $log->payload_response;
                            if (is_string($payloadResponse)) {
                                $decoded = json_decode($payloadResponse, true);
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    $payloadResponse = $decoded;
                                }
                            }

                            $technicalMessage = is_array($payloadResponse)
                                ? ($payloadResponse['technical_message'] ?? null)
                                : null;
                        @endphp
                        <tr>
                            <td>{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $log->tipo_evento }}</td>
                            <td>{{ $log->codigo_solicitacao ?: '-' }}</td>
                            <td>{{ $log->inscricao?->numero_inscricao ?: '-' }}</td>
                            <td>
                                <span
                                    class="badge {{ $log->sucesso ? 'bg-success' : 'bg-danger' }}">{{ $log->sucesso ? 'Sucesso' : 'Falha' }}</span>
                            </td>
                            <td>{{ $log->status_http ?: '-' }}</td>
                            <td>{{ $log->mensagem ?: '-' }}</td>
                            <td class="small text-muted" style="max-width: 360px; white-space: normal;">
                                {{ $technicalMessage ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Sem logs recentes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const master = document.getElementById('selecionar-todas');
            master?.addEventListener('change', function () {
                document.querySelectorAll('.chk-inscricao').forEach((el) => {
                    el.checked = master.checked;
                });
            });
        });
    </script>
@endsection