@extends('layouts.main')

@section('title', 'Painel de Chamados')

@section('content')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid;
            cursor: pointer;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .stats-card.pendentes {
            border-left-color: #ff6b6b;
            background: linear-gradient(135deg, #fff5f5 0%, #fff 100%);
        }

        .stats-card.em_analise {
            border-left-color: #ffd93d;
            background: linear-gradient(135deg, #fffbf0 0%, #fff 100%);
        }

        .stats-card.em_andamento {
            border-left-color: #4dabf7;
            background: linear-gradient(135deg, #f0f7ff 0%, #fff 100%);
        }

        .stats-card.concluidos {
            border-left-color: #51cf66;
            background: linear-gradient(135deg, #f0fdf4 0%, #fff 100%);
        }

        .stats-card.cancelados {
            border-left-color: #868e96;
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
        }

        .chamado-card {
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
            margin-bottom: 15px;
        }

        .chamado-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-color: #007bff;
        }

        .status-badge {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .status-pendente {
            background-color: #ffe0e0;
            color: #c92a2a;
        }

        .status-em_analise {
            background-color: #fff3c0;
            color: #e67700;
        }

        .status-em_andamento {
            background-color: #d0ebff;
            color: #0066cc;
        }

        .status-concluido {
            background-color: #d3f9d8;
            color: #2b8a3e;
        }

        .status-cancelado {
            background-color: #e9ecef;
            color: #495057;
        }

        .tipo-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-right: 8px;
        }

        .tipo-rescisao {
            background-color: #ffe0e0;
            color: #862e2e;
        }

        .tipo-alteracao {
            background-color: #e0f2f1;
            color: #004d40;
        }

        .tipo-outro {
            background-color: #f3e5f5;
            color: #4a148c;
        }

        .filter-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .chamado-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .chamado-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            font-size: 0.9rem;
            color: #666;
            margin: 10px 0;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-item strong {
            color: #333;
        }

        .responsavel-select {
            font-size: 0.85rem;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .action-buttons form {
            display: contents;
        }

        .btn-sm-custom {
            padding: 5px 10px;
            font-size: 0.8rem;
            border-radius: 4px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Estatísticas -->
    <div class="mb-5">
        <h3 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Visão Geral</h3>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-3">
                <a href="{{ route('chamados.painel', ['filtro' => 'pendente']) }}" class="text-decoration-none">
                    <div class="card stats-card pendentes h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Pendentes</h6>
                                    <h3 class="mb-0" style="color: #ff6b6b;">{{ $stats['pendentes'] }}</h3>
                                </div>
                                <i class="fas fa-clock" style="color: #ff6b6b; font-size: 1.5rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4 mb-3">
                <a href="{{ route('chamados.painel', ['filtro' => 'em_analise']) }}" class="text-decoration-none">
                    <div class="card stats-card em_analise h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Em Análise</h6>
                                    <h3 class="mb-0" style="color: #ffd93d;">{{ $stats['em_analise'] }}</h3>
                                </div>
                                <i class="fas fa-search" style="color: #ffd93d; font-size: 1.5rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4 mb-3">
                <a href="{{ route('chamados.painel', ['filtro' => 'em_andamento']) }}" class="text-decoration-none">
                    <div class="card stats-card em_andamento h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Em Andamento</h6>
                                    <h3 class="mb-0" style="color: #4dabf7;">{{ $stats['em_andamento'] }}</h3>
                                </div>
                                <i class="fas fa-spinner" style="color: #4dabf7; font-size: 1.5rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4 mb-3">
                <a href="{{ route('chamados.painel', ['filtro' => 'concluido']) }}" class="text-decoration-none">
                    <div class="card stats-card concluidos h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Concluídos</h6>
                                    <h3 class="mb-0" style="color: #51cf66;">{{ $stats['concluidos'] }}</h3>
                                </div>
                                <i class="fas fa-check-circle" style="color: #51cf66; font-size: 1.5rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4 mb-3">
                <a href="{{ route('chamados.painel', ['filtro' => 'cancelado']) }}" class="text-decoration-none">
                    <div class="card stats-card cancelados h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Cancelados</h6>
                                    <h3 class="mb-0" style="color: #868e96;">{{ $stats['cancelados'] }}</h3>
                                </div>
                                <i class="fas fa-ban" style="color: #868e96; font-size: 1.5rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4 mb-3">
                <a href="{{ route('chamados.painel', ['filtro' => 'todos']) }}" class="text-decoration-none">
                    <div class="card stats-card h-100" style="border-left-color: #007bff; background: linear-gradient(135deg, #f0f8ff 0%, #fff 100%);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Total</h6>
                                    <h3 class="mb-0" style="color: #007bff;">{{ $stats['pendentes'] + $stats['em_analise'] + $stats['em_andamento'] + $stats['concluidos'] + $stats['cancelados'] }}</h3>
                                </div>
                                <i class="fas fa-list-check" style="color: #007bff; font-size: 1.5rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filtrar Chamados</h5>
        <form method="GET" action="{{ route('chamados.painel') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Buscar</label>
                <input type="text" name="busca" class="form-control" placeholder="Protocolo, empresa ou estagiário..."
                    value="{{ $busca }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="filtro" class="form-select">
                    <option value="todos" {{ $filtro === 'todos' ? 'selected' : '' }}>Todos</option>
                    <option value="pendente" {{ $filtro === 'pendente' ? 'selected' : '' }}>Pendentes</option>
                    <option value="em_analise" {{ $filtro === 'em_analise' ? 'selected' : '' }}>Em Análise</option>
                    <option value="em_andamento" {{ $filtro === 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                    <option value="concluido" {{ $filtro === 'concluido' ? 'selected' : '' }}>Concluídos</option>
                    <option value="cancelado" {{ $filtro === 'cancelado' ? 'selected' : '' }}>Cancelados</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos os tipos</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t->id_tipo_chamado }}" {{ $tipo == $t->id_tipo_chamado ? 'selected' : '' }}>
                            {{ $t->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i>Filtrar
                </button>
                <a href="{{ route('chamados.painel') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-1"></i>Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Lista de Chamados -->
    <div class="mb-4">
        <h3 class="mb-3"><i class="fas fa-headset me-2"></i>Chamados
            <span class="badge bg-secondary">{{ $chamados->total() }}</span>
        </h3>

        @if($chamados->count() > 0)
            <div>
                @foreach($chamados as $chamado)
                    <div class="card chamado-card">
                        <div class="card-body p-4">
                            <div class="chamado-header">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                        <h5 class="mb-0">
                                            <strong>#{{ $chamado->protocolo }}</strong>
                                        </h5>
                                        @if(($chamado->mensagens_nao_lidas_count ?? 0) > 0)
                                            <span class="badge bg-danger position-relative" 
                                                style="animation: pulse 1.5s ease-in-out infinite; font-size: 0.95rem;" 
                                                title="Mensagens novas da unidade concedente">
                                                <i class="fas fa-bell me-1"></i>{{ $chamado->mensagens_nao_lidas_count }} nova(s)
                                            </span>
                                        @endif
                                        <span class="status-badge status-{{ $chamado->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $chamado->status)) }}
                                        </span>
                                        <span class="tipo-badge tipo-{{ $chamado->tipoChamado->slug }}">
                                            <i class="fas fa-tag me-1"></i>{{ $chamado->tipoChamado->nome }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end" style="font-size: 0.85rem; color: #999;">
                                    {{ $chamado->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            <div class="chamado-info">
                                <div class="info-item">
                                    <i class="fas fa-building" style="color: #007bff;"></i>
                                    <strong>Empresa:</strong> {{ $chamado->empresa->nome_empresa }}
                                </div>

                                <div class="info-item">
                                    <i class="fas fa-user" style="color: #007bff;"></i>
                                    <strong>Solicitante:</strong> {{ $chamado->solicitante->name }}
                                </div>

                                @if($chamado->termo)
                                    <div class="info-item">
                                        <i class="fas fa-id-card" style="color: #007bff;"></i>
                                        <strong>Estagiário:</strong> {{ $chamado->termo->estagiario->nome_estagiario }}
                                    </div>

                                    <div class="info-item">
                                        <i class="fas fa-file-contract" style="color: #007bff;"></i>
                                        <strong>Termo:</strong> {{ $chamado->termo->numero_termo }}/{{ $chamado->termo->ano_termo }}
                                    </div>
                                @endif

                                @if($chamado->responsavel)
                                    <div class="info-item">
                                        <i class="fas fa-user-tie" style="color: #28a745;"></i>
                                        <strong>Responsável:</strong> {{ $chamado->responsavel->name }}
                                    </div>
                                @else
                                    <div class="info-item">
                                        <i class="fas fa-user-tie" style="color: #ccc;"></i>
                                        <strong style="color: #999;">Responsável:</strong> <em>Não atribuído</em>
                                    </div>
                                @endif
                            </div>

                            <!-- Resumo do chamado -->
                            <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin: 12px 0; font-size: 0.9rem;">
                                @if($chamado->isRescisao())
                                    <strong>Data da Rescisão:</strong> {{ $chamado->data_rescisao->format('d/m/Y') }}<br>
                                    <strong>Motivo:</strong> {{ Str::limit($chamado->motivo_rescisao, 100) }}
                                @elseif($chamado->isAlteracao())
                                    <strong>Descrição:</strong> {{ Str::limit($chamado->descricao_alteracao, 150) }}
                                @else
                                    <strong>Título:</strong> {{ $chamado->titulo }}<br>
                                    <strong>Detalhes:</strong> {{ Str::limit($chamado->detalhes, 100) }}
                                @endif
                            </div>

                            <!-- Observação interna -->
                            @if($chamado->observacoes_internas)
                                <div style="background: #fff3cd; padding: 12px; border-radius: 6px; margin-bottom: 12px; border-left: 4px solid #ffc107; font-size: 0.9rem;">
                                    <strong><i class="fas fa-sticky-note me-1"></i>Observação:</strong>
                                    <p class="mb-0 mt-1">{{ $chamado->observacoes_internas }}</p>
                                </div>
                            @endif

                            <!-- Ações -->
                            <div class="action-buttons">
                                <!-- Mudar Status -->
                                <form method="POST" action="{{ route('chamados.atualizar-status', $chamado->id_chamado) }}" class="d-inline js-status-form">
                                    @csrf
                                    @method('PUT')
                                    <div class="d-flex gap-2 align-items-center">
                                        <select name="status" class="form-select form-select-sm responsavel-select js-status-select" style="width: auto;">
                                            <option value="">Alterar Status</option>
                                            <option value="em_analise" {{ $chamado->status === 'em_analise' ? 'disabled' : '' }}>Em Análise</option>
                                            <option value="em_andamento" {{ $chamado->status === 'em_andamento' ? 'disabled' : '' }}>Em Andamento</option>
                                            <option value="concluido" {{ $chamado->status === 'concluido' ? 'disabled' : '' }}>Concluído</option>
                                            <option value="cancelado" {{ $chamado->status === 'cancelado' ? 'disabled' : '' }}>Cancelado</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary btn-sm-custom" title="Atualizar status">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </form>

                                <!-- Atribuir Responsável -->
                                <form method="POST" action="{{ route('chamados.atribuir-responsavel', $chamado->id_chamado) }}" class="d-inline js-responsavel-form">
                                    @csrf
                                    @method('PUT')
                                    <div class="d-flex gap-2 align-items-center">
                                        <select name="fk_id_user_responsavel" class="form-select form-select-sm responsavel-select js-responsavel-select" style="width: auto;">
                                            <option value="">Atribuir a...</option>
                                            @foreach($operadores as $op)
                                                <option value="{{ $op->id }}" {{ $chamado->fk_id_user_responsavel === $op->id ? 'selected' : '' }}>
                                                    {{ $op->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-success btn-sm-custom" title="Atribuir responsável">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                    </div>
                                </form>

                                <!-- Ver Detalhes -->
                                <a href="{{ route('chamados.show', $chamado->id_chamado) }}"
                                    class="btn btn-sm btn-outline-info btn-sm-custom" title="Ver detalhes">
                                    <i class="fas fa-eye"></i> Detalhes
                                </a>
                            </div>

                            <!-- Modal para adicionar observação -->
                            <button type="button" class="btn btn-sm btn-outline-warning btn-sm-custom mt-2" data-bs-toggle="modal"
                                data-bs-target="#modalObservacao{{ $chamado->id_chamado }}" title="Adicionar observação">
                                <i class="fas fa-comment-dots"></i> Observação
                            </button>

                            <div class="modal fade" id="modalObservacao{{ $chamado->id_chamado }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Observação - Chamado #{{ $chamado->protocolo }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('chamados.adicionar-observacao', $chamado->id_chamado) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Observação (Máx. 2000 caracteres)</label>
                                                    <textarea name="observacoes_internas" class="form-control" rows="5"
                                                        maxlength="2000">{{ $chamado->observacoes_internas }}</textarea>
                                                    <small class="form-text text-muted">
                                                        <span id="contador{{ $chamado->id_chamado }}">{{ strlen($chamado->observacoes_internas) }}</span>/2000
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Salvar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center">
                {{ $chamados->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h5 class="mt-3">Nenhum chamado encontrado</h5>
                <p>Não há chamados {{ $filtro !== 'todos' ? "com status '{$filtro}'" : '' }} para exibir.</p>
                <a href="{{ route('chamados.painel') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-redo me-1"></i>Limpar Filtros
                </a>
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalConfirmacaoStatusChamado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg confirmacao-status-modal">
                <div class="modal-header border-0 pb-0">
                    <div class="confirmacao-status-header">
                        <div class="confirmacao-status-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-1" id="confirmacaoStatusTitulo">Confirmar alteração</h5>
                            <p class="text-muted mb-0 small">Essa ação enviará uma atualização automática para a unidade concedente.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="confirmacao-status-box">
                        <div class="confirmacao-status-badge" id="confirmacaoStatusBadge">Status</div>
                        <p class="mb-0" id="confirmacaoStatusMensagem"></p>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Voltar</button>
                    <button type="button" class="btn btn-primary" id="confirmacaoStatusBotao">
                        <i class="fas fa-paper-plane me-2"></i>Confirmar e enviar
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <style>
        @keyframes pulse {
            0%, 100% { 
                opacity: 1; 
                transform: scale(1);
            }
            50% { 
                opacity: 0.8; 
                transform: scale(1.05);
            }
        }

        .confirmacao-status-modal {
            border-radius: 18px;
            overflow: hidden;
        }

        .confirmacao-status-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .confirmacao-status-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1d4ed8;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .confirmacao-status-box {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            border: 1px solid #dbeafe;
            border-radius: 16px;
            padding: 18px;
        }

        .confirmacao-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 12px;
        }

        .confirmacao-status-badge.concluido {
            background: #dcfce7;
            color: #166534;
        }

        .confirmacao-status-badge.cancelado {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
    <script>
        function obterConteudoConfirmacaoStatus(status) {
            const mensagens = {
                concluido: {
                    titulo: 'Concluir chamado',
                    badge: 'Concluído',
                    mensagem: 'Ao marcar esse chamado como concluído, será enviada uma mensagem para a unidade concedente informando que o atendimento foi finalizado.'
                },
                cancelado: {
                    titulo: 'Cancelar chamado',
                    badge: 'Cancelado',
                    mensagem: 'Ao marcar esse chamado como cancelado, será enviada uma mensagem para a unidade concedente informando que o chamado foi cancelado.'
                }
            };

            return mensagens[status] ?? null;
        }

        // Contador de caracteres nas observações
        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('modalConfirmacaoStatusChamado');
            const modalStatus = modalElement ? new bootstrap.Modal(modalElement) : null;
            const tituloStatus = document.getElementById('confirmacaoStatusTitulo');
            const badgeStatus = document.getElementById('confirmacaoStatusBadge');
            const mensagemStatus = document.getElementById('confirmacaoStatusMensagem');
            const botaoConfirmacao = document.getElementById('confirmacaoStatusBotao');
            let formPendente = null;

            const textareas = document.querySelectorAll('textarea[name="observacoes_internas"]');
            textareas.forEach(textarea => {
                const chamadoId = textarea.closest('.modal-content').querySelector('.modal-title').textContent.match(/\d+/)[0];
                const contador = document.getElementById('contador' + chamadoId);

                if (contador) {
                    textarea.addEventListener('input', function() {
                        contador.textContent = this.value.length;
                    });
                }
            });

            const statusForms = document.querySelectorAll('.js-status-form');
            statusForms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (this.dataset.confirmado === 'true') {
                        delete this.dataset.confirmado;
                        return;
                    }

                    const select = this.querySelector('.js-status-select');

                    if (!select || !select.value) {
                        event.preventDefault();
                        return;
                    }

                    const conteudo = obterConteudoConfirmacaoStatus(select.value);
                    if (!conteudo || !modalStatus) {
                        return;
                    }

                    event.preventDefault();
                    formPendente = this;

                    tituloStatus.textContent = conteudo.titulo;
                    badgeStatus.textContent = conteudo.badge;
                    badgeStatus.className = 'confirmacao-status-badge ' + select.value;
                    mensagemStatus.textContent = conteudo.mensagem;

                    modalStatus.show();
                });
            });

            if (modalElement) {
                modalElement.addEventListener('hidden.bs.modal', function() {
                    formPendente = null;
                });
            }

            if (botaoConfirmacao) {
                botaoConfirmacao.addEventListener('click', function() {
                    if (!formPendente) {
                        return;
                    }

                    formPendente.dataset.confirmado = 'true';
                    modalStatus.hide();
                    formPendente.submit();
                });
            }

            const statusSelects = document.querySelectorAll('.js-status-select');
            statusSelects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.value) {
                        this.form?.requestSubmit();
                    }
                });
            });

            const responsavelSelects = document.querySelectorAll('.js-responsavel-select');
            responsavelSelects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.value) {
                        this.form?.requestSubmit();
                    }
                });
            });
        });
    </script>
@endsection
