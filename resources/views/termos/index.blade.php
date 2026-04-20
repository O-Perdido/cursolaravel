@extends('layouts.main')

@section('title', 'Termos')

@section('content')

    <style>
        /* Tabela de termos: larguras e melhor usabilidade */
        .table-termos th:nth-child(1),
        .table-termos td:nth-child(1) {
            /*width: 130px;*/
        }

        .table-termos th:nth-child(2),
        .table-termos td:nth-child(2) {
            /*width: 120px;*/
        }

        .table-termos th:nth-child(3),
        .table-termos td:nth-child(3) {
            /*width: 220px;*/
        }

        .table-termos th:nth-child(4),
        .table-termos td:nth-child(4) {
            /*width: 220px;*/
        }

        .table-termos th:nth-child(5),
        .table-termos td:nth-child(5) {
            /*width: 220px;*/
        }

        .table-termos th:nth-child(6),
        .table-termos td:nth-child(6) {
            /*width: 150px;*/
        }

        .table-termos th:nth-child(7),
        .table-termos td:nth-child(7),
        .table-termos td.actions-cell {
            width: 150px;
            white-space: nowrap;
            text-align: center;
        }

        /* Botões maiores para melhor alvo de clique */
        .btn-action {
            padding: 0.5rem 0.7rem;
            line-height: 1.2;
        }

        .btn-action i {
            font-size: 1.05rem;
        }

        /* Ajustes de modal: garantir quebra e sobreposição correta */
        .modal,
        .modal .modal-content,
        .modal .modal-body,
        .modal .modal-header,
        .modal .modal-footer,
        .modal .modal-title {
            white-space: normal;
            /* Important: evita herdar nowrap da célula da tabela */
        }

        .modal .modal-body {
            word-break: normal;
            overflow-wrap: break-word;
        }

        .modal-backdrop {
            z-index: 1990;
        }

        .modal {
            z-index: 2000;
        }

        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 600px;
            }
        }

        /* Tabela de destinatários no modal */
        .table-recipients td,
        .table-recipients th {
            vertical-align: middle;
        }

        .table-recipients {
            table-layout: auto;
        }

        .table-recipients th {
            white-space: nowrap;
            word-break: normal;
        }

        .table-recipients td {
            word-break: normal;
            overflow-wrap: break-word;
        }

        /* Ajustes na paginação - botões um pouco menores */
        .pagination .page-link {
            padding: 0.35rem 0.65rem;
            font-size: 0.9rem;
        }

        /* Select de itens por página inline */
        .per-page-selector {
            display: inline-block;
            width: auto;
            margin-left: 10px;
            font-size: 0.875rem;
        }

        /* Ocultar texto padrão do Laravel na paginação (ex: "Showing 1 to 25 of 1670 results") */
        .pagination-info {
            display: none !important;
        }

        /* Ocultar qualquer texto adicional que o Laravel possa adicionar */
        nav[role="navigation"]>p,
        nav[role="navigation"] .hidden,
        nav[aria-label="Pagination"]>p,
        nav[aria-label="Pagination"] .hidden {
            display: none !important;
        }

        /* Ocultar texto dentro do nav de paginação que não seja a ul */
        nav[role="navigation"]>*:not(ul) {
            display: none !important;
        }

        nav[aria-label*="Pagination"]>*:not(ul) {
            display: none !important;
        }

        /* Garantir que apenas os botões de paginação sejam visíveis */
        nav[role="navigation"] {
            display: flex !important;
            justify-content: center !important;
        }

        nav[role="navigation"] ul.pagination {
            margin: 0 !important;
        }
    </style>

    @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('delete'))
            <div class="alert alert-danger">
                {{ session('delete') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Card de Filtro e Título -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body pb-2 pt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="fas fa-file-alt me-2 text-primary"></i>
                        Lista de Termos
                    </h4>
                    <a href="{{ route('termos.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Adicionar Termo
                    </a>
                </div>
                <form method="GET" action="{{ route('termos.index') }}">
                    <div class="row g-3">
                        <!-- Linha 1: Filtros principais -->
                        <div class="col-md-3">
                            <label for="escola_search" class="form-label mb-1 fw-semibold">Instituição</label>
                            <input type="text" class="form-control form-control-sm" id="escola_search"
                                placeholder="Digite para buscar..." autocomplete="off"
                                value="{{ $escolas->firstWhere('id_escola', request('escola'))?->nome_escola }}">
                            <select name="escola" id="escola" class="form-select form-select-sm mt-2" size="5"
                                style="display:none; position:absolute; z-index:1050; background:#fff; border:1px solid #ced4da; width:700px;">
                                <option value="">Todas</option>
                                @foreach ($escolas as $escola)
                                    <option value="{{ $escola->id_escola ?? '' }}" {{ request('escola') == ($escola->id_escola ?? '') ? 'selected' : '' }}>
                                        {{ $escola->nome_escola ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="estagiario" class="form-label mb-1 fw-semibold">Estagiário</label>
                            <input type="text" name="estagiario" id="estagiario" class="form-control form-control-sm"
                                value="{{ request('estagiario') }}" placeholder="Nome do estagiário">
                        </div>

                        <div class="col-md-3">
                            <label for="numero_termo" class="form-label mb-1 fw-semibold">Número/Ano do Termo</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="numero_termo" id="numero_termo" class="form-control form-control-sm"
                                    value="{{ request('numero_termo') }}" placeholder="Número" min="1">
                                <span class="input-group-text px-2">/</span>
                                <input type="number" name="ano_termo" id="ano_termo" class="form-control form-control-sm"
                                    value="{{ request('ano_termo') }}" placeholder="Ano" min="2000" max="9999">
                            </div>
                        </div>

                        @if (Auth::user()->nivel != 'empresa')
                            <div class="col-md-3">
                                <label for="empresa_search" class="form-label mb-1 fw-semibold">Unidade Concedente</label>
                                <input type="text" class="form-control form-control-sm" id="empresa_search"
                                    placeholder="Digite para buscar..." autocomplete="off"
                                    value="{{ $empresas->firstWhere('id_empresa', request('empresa'))?->nome_empresa }}">
                                <div id="empresa_select_wrapper"
                                    style="display:none; position:absolute; z-index:1050; resize:horizontal; overflow:auto; border:1px solid #ced4da; min-width:700px;">
                                    <select name="empresa" id="empresa" class="form-select form-select-sm" size="5"
                                        style="width:100%; border:none; margin:0; padding:0;">
                                        <option value="">Todas</option>
                                        @foreach ($empresas as $empresa)
                                            <option value="{{ $empresa->id_empresa }}" {{ request('empresa') == $empresa->id_empresa ? 'selected' : '' }}>
                                                {{ $empresa->nome_empresa }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3" id="localFilterCol" style="{{ request('empresa') ? '' : 'display:none;' }}">
                                <label for="local" class="form-label mb-1 fw-semibold">Local</label>
                                <select name="local" id="local" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                        @endif

                        <!-- Linha 2: Período e Status -->
                        <div class="col-md-4">
                            <label class="form-label mb-1 fw-semibold">Período de Término do Estágio</label>
                            <div class="input-group input-group-sm">
                                <input type="date" name="data_inicial" id="data_inicial" class="form-control form-control-sm"
                                    value="{{ request('data_inicial') }}" placeholder="Data inicial">
                                <span class="input-group-text px-2">até</span>
                                <input type="date" name="data_final" id="data_final" class="form-control form-control-sm"
                                    value="{{ request('data_final') }}" placeholder="Data final">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label for="status" class="form-label mb-1 fw-semibold">Status</label>
                            <select name="status" id="status" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="ativos" {{ request('status') == 'ativos' ? 'selected' : '' }}>Ativos</option>
                                <option value="rescindidos" {{ request('status') == 'rescindidos' ? 'selected' : '' }}>Rescindidos
                                </option>
                                <option value="vencidos" {{ request('status') == 'vencidos' ? 'selected' : '' }}>Vencidos</option>
                            </select>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="col-md-6 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-search me-1"></i> Filtrar
                            </button>
                            <a href="{{ route('termos.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="fas fa-eraser me-1"></i> Limpar
                            </a>
                            <a href="{{ route('termos.gerarPdfRelatorioTermo', request()->query()) }}"
                                class="btn btn-outline-danger btn-sm flex-fill" target="_blank" data-bs-toggle="tooltip"
                                title="Gerar PDF">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </a>
                            <a href="{{ route('termos.export', request()->query()) }}"
                                class="btn btn-outline-success btn-sm flex-fill" data-bs-toggle="tooltip" title="Gerar Excel">
                                <i class="fas fa-file-excel me-1"></i> Excel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- FIM DO CARD DE FILTRO E TÍTULO -->

        <!-- Total de termos -->
        <div class="mb-2" style="font-weight: bold; font-size: 1.1em;">
            Total de termos: {{ method_exists($termos, 'total') ? $termos->total() : $termos->count() }}
        </div>

        @if (method_exists($termos, 'links'))
            <!-- Paginação (topo) -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <span class="text-muted small">
                        @if($termos->total() > 0)
                            Mostrando {{ $termos->firstItem() }}–{{ $termos->lastItem() }} de {{ $termos->total() }}
                        @else
                            Nenhum registro encontrado
                        @endif
                    </span>
                    @php $pp = request('per_page', '25'); @endphp
                    <select id="perPageSelectorAdmin" class="form-select form-select-sm per-page-selector"
                        onchange="changePerPage(this.value)">
                        <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                        <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                        <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                    </select>
                </div>
                <div>
                    {{ $termos->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif

        <div style="max-height: 600px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 10px;">
            <table class="table table-termos align-middle">
                <thead style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 1;">
                    <tr>
                        <th>Número do Termo</th>
                        <!--
                        <th>Data</th>
                        -->
                        <th style="min-width: 250px">Nome Estagiario</th>
                        <th>Unidade Concedente</th>
                        <th>Instituição de Ensino</th>
                        <th style="text-align: center;">Status do Contrato</th>
                        <th style="text-align: center;">Assinatura ZapSign</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($termos as $termo)
                        <tr>
                            <td style="vertical-align: middle;">
                                {{ $termo->numero_termo }}/{{ $termo->ano_termo }}
                            </td>
                            <!--
                            <td style="vertical-align: middle;">
                                {{ $termo->data ? date('d/m/Y', strtotime($termo->data)) : 'Data não disponível' }}
                            </td>
                            -->
                            <td style="vertical-align: middle;">
                                <div>{{ $termo->estagiario->nome_estagiario }}</div>
                                @if(!empty($termo->estagiario->nome_secundario))
                                    <div class="text-muted small">Nome civil: {{ $termo->estagiario->nome_secundario }}</div>
                                @endif
                            </td>
                            <td style="vertical-align: middle;">{{ $termo->empresa->nome_empresa }}</td>
                            <td style="vertical-align: middle;">{{ $termo->escola->nome_escola }}</td>
                            <td style="text-align: center; vertical-align: middle;">
                                @php
                                    $isVencido = \Carbon\Carbon::parse($termo->data_fim_estagio)->isPast() && !$termo->rescisao;
                                @endphp
                                @if ($termo->rescisao)
                                    <span class="badge bg-danger">Rescindido</span>
                                @elseif ($isVencido)
                                    <span class="badge bg-warning text-dark">Contrato Vencido</span>
                                @else
                                    <span class="badge bg-success">Ativo</span>
                                @endif
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                @php
                                    $zsRaw = strtolower($termo->zapsign_status ?? '');
                                    $zsMap = [
                                        '' => ['Não enviado', 'secondary'],
                                        'enviado' => ['Enviado', 'info'],
                                        'pending' => ['Pendente', 'secondary'],
                                        'waiting' => ['Pendente', 'secondary'],
                                        'waiting_signature' => ['Pendente', 'secondary'],
                                        'processing' => ['Processando', 'secondary'],
                                        'partially_signed' => ['Parcialmente assinado', 'warning'],
                                        'partial' => ['Parcialmente assinado', 'warning'],
                                        'finished' => ['Assinado', 'success'],
                                        'signed' => ['Assinado', 'success'],
                                        'concluded' => ['Assinado', 'success'],
                                        'completed' => ['Assinado', 'success'],
                                        'canceled' => ['Cancelado', 'dark'],
                                        'cancelled' => ['Cancelado', 'dark'],
                                        'refused' => ['Recusado', 'danger'],
                                        'rejected' => ['Recusado', 'danger'],
                                        'declined' => ['Recusado', 'danger'],
                                        'error' => ['Erro', 'danger'],
                                        'failed' => ['Erro', 'danger'],
                                    ];
                                    $zsLabel = $zsMap[$zsRaw][0] ?? ucfirst($zsRaw);
                                    $zsClass = $zsMap[$zsRaw][1] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $zsClass }}">
                                    {{ $zsLabel }}
                                </span>
                            </td>
                            <td class="actions-cell">
                                <div class="btn-group" role="group" aria-label="Ações">
                                    <a href="{{ route('termos.downloadPdf', $termo->id_termo) }}"
                                        class="btn btn-outline-danger btn-action" target="_blank" data-bs-toggle="tooltip"
                                        title="Baixar PDF" aria-label="Baixar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('termos.show', $termo->id_termo) }}"
                                        class="btn btn-outline-primary btn-action" data-bs-toggle="tooltip" title="Detalhes"
                                        aria-label="Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <!-- Botão ZapSign -->
                                    <button type="button" class="btn btn-outline-success btn-action" data-bs-toggle="modal"
                                        data-bs-target="#zapSignModal{{ $termo->id_termo }}" title="Enviar para Assinatura ZapSign"
                                        aria-label="ZapSign">
                                        <i class="fas fa-file-signature"></i>
                                    </button>
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-outline-danger btn-action" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $termo->id_termo }}" title="Excluir" aria-label="Excluir">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>

                                <!-- Modal ZapSign -->
                                <div class="modal fade" id="zapSignModal{{ $termo->id_termo }}" tabindex="-1"
                                    aria-labelledby="zapSignModalLabel{{ $termo->id_termo }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="zapSignModalLabel{{ $termo->id_termo }}">
                                                    <i class="fas fa-file-signature me-2"></i>
                                                    Enviar para Assinatura ZapSign
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-break">
                                                <p>Deseja enviar este termo para assinatura eletrônica via ZapSign?</p>
                                                <p><strong>Termo:</strong> {{ $termo->numero_termo }}/{{ $termo->ano_termo }}</p>
                                                <p>
                                                    <strong>Estagiário:</strong> {{ $termo->estagiario->nome_estagiario }}
                                                    @if(!empty($termo->estagiario->nome_secundario))
                                                        <br><span class="text-muted small">Nome civil: {{ $termo->estagiario->nome_secundario }}</span>
                                                    @endif
                                                </p>
                                                @if($termo->estagiario->email)
                                                    <p><strong>Email:</strong> {{ $termo->estagiario->email }}</p>
                                                @else
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        Atenção: Este estagiário não possui email cadastrado!
                                                    </div>
                                                @endif

                                                <hr class="my-2">
                                                <form action="{{ route('termos.enviarZapSign', $termo->id_termo) }}" method="POST"
                                                    style="display:inline-block; width: 100%;">
                                                    @csrf
                                                <p class="mb-1">
                                                    <strong>Destinatários</strong>
                                                    <span class="text-muted small">(clique nas setas para reordenar)</span>
                                                </p>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered mb-2 table-recipients"
                                                        style="font-size: 9pt " id="tabelaDestinatarios{{ $termo->id_termo }}">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width: 90px;">Remover?</th>
                                                                <th style="width: 50px;">Ordem</th>
                                                                <th style="width: 120px;">Tipo</th>
                                                                <th>Nome</th>
                                                                <th style="width: 35%;">E-mail</th>
                                                                <th style="width: 20%;">Representante</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbodyDestinatarios{{ $termo->id_termo }}">
                                                            <tr data-ordem="1" data-tipo="estagiario">
                                                                <td class="text-center">
                                                                    @if(!empty($termo->estagiario->email))
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="remover_destinatarios[]"
                                                                            value="{{ $termo->estagiario->email }}">
                                                                    @else
                                                                        —
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-sm btn-link p-0"
                                                                        onclick="moverLinha(this, -1, {{ $termo->id_termo }})"
                                                                        title="Mover para cima">
                                                                        <i class="fas fa-arrow-up text-primary"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-link p-0"
                                                                        onclick="moverLinha(this, 1, {{ $termo->id_termo }})"
                                                                        title="Mover para baixo">
                                                                        <i class="fas fa-arrow-down text-primary"></i>
                                                                    </button>
                                                                </td>
                                                                <td><i class="fas fa-user text-primary me-1"></i> Estagiário</td>
                                                                <td>
                                                                    {{ $termo->estagiario->nome_estagiario }}
                                                                    @if(!empty($termo->estagiario->nome_secundario))
                                                                        <br><span class="text-muted small">Nome civil: {{ $termo->estagiario->nome_secundario }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $termo->estagiario->email ?? '—' }}</td>
                                                                <td>—</td>
                                                            </tr>
                                                            <tr data-ordem="2" data-tipo="ebcp">
                                                                <td class="text-center">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="remover_destinatarios[]"
                                                                        value="moacirecetista@hotmail.com">
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-sm btn-link p-0"
                                                                        onclick="moverLinha(this, -1, {{ $termo->id_termo }})"
                                                                        title="Mover para cima">
                                                                        <i class="fas fa-arrow-up text-primary"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-link p-0"
                                                                        onclick="moverLinha(this, 1, {{ $termo->id_termo }})"
                                                                        title="Mover para baixo">
                                                                        <i class="fas fa-arrow-down text-primary"></i>
                                                                    </button>
                                                                </td>
                                                                <td><i class="fas fa-handshake text-info me-1"></i> Ag. Integração
                                                                </td>
                                                                <td>EBCP CONSULTORIA LTDA</td>
                                                                <td>moacirecetista@hotmail.com</td>
                                                                <td>Moacir Aguiar</td>
                                                            </tr>

                                                            {{-- Representantes da Empresa --}}
                                                            @php $ordem = 3; @endphp
                                                            @if(isset($termo->empresa) && $termo->empresa->representantes->count() > 0)
                                                                @foreach($termo->empresa->representantes as $rep)
                                                                    <tr data-ordem="{{ $ordem++ }}" data-tipo="empresa_rep">
                                                                        <td class="text-center">
                                                                            @if(!empty($rep->email))
                                                                                <input type="checkbox" class="form-check-input"
                                                                                    name="remover_destinatarios[]"
                                                                                    value="{{ $rep->email }}">
                                                                            @else
                                                                                —
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <button type="button" class="btn btn-sm btn-link p-0"
                                                                                onclick="moverLinha(this, -1, {{ $termo->id_termo }})"
                                                                                title="Mover para cima">
                                                                                <i class="fas fa-arrow-up text-primary"></i>
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm btn-link p-0"
                                                                                onclick="moverLinha(this, 1, {{ $termo->id_termo }})"
                                                                                title="Mover para baixo">
                                                                                <i class="fas fa-arrow-down text-primary"></i>
                                                                            </button>
                                                                        </td>
                                                                        <td><i class="fas fa-building text-secondary me-1"></i> Unidade</td>
                                                                        <td>{{ $termo->empresa->nome_empresa }}</td>
                                                                        <td>{{ $rep->email }}</td>
                                                                        <td>{{ $rep->nome }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @elseif(isset($termo->empresa))
                                                                <tr data-ordem="{{ $ordem++ }}" data-tipo="empresa_legado">
                                                                    <td class="text-center">
                                                                        @if(!empty($termo->empresa->email))
                                                                            <input type="checkbox" class="form-check-input"
                                                                                name="remover_destinatarios[]"
                                                                                value="{{ $termo->empresa->email }}">
                                                                        @else
                                                                            —
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-sm btn-link p-0"
                                                                            onclick="moverLinha(this, -1, {{ $termo->id_termo }})"
                                                                            title="Mover para cima">
                                                                            <i class="fas fa-arrow-up text-primary"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn-sm btn-link p-0"
                                                                            onclick="moverLinha(this, 1, {{ $termo->id_termo }})"
                                                                            title="Mover para baixo">
                                                                            <i class="fas fa-arrow-down text-primary"></i>
                                                                        </button>
                                                                    </td>
                                                                    <td><i class="fas fa-building text-secondary me-1"></i> Unidade</td>
                                                                    <td>{{ $termo->empresa->nome_empresa }}</td>
                                                                    <td>{{ $termo->empresa->email ?? '—' }}</td>
                                                                    <td>{{ $termo->empresa->nome_representante ?? '—' }}</td>
                                                                </tr>
                                                            @endif

                                                            {{-- Representantes da Escola --}}
                                                            @if(isset($termo->escola) && $termo->escola->representantes->count() > 0)
                                                                @foreach($termo->escola->representantes as $rep)
                                                                    <tr data-ordem="{{ $ordem++ }}" data-tipo="escola_rep">
                                                                        <td class="text-center">
                                                                            @if(!empty($rep->email))
                                                                                <input type="checkbox" class="form-check-input"
                                                                                    name="remover_destinatarios[]"
                                                                                    value="{{ $rep->email }}">
                                                                            @else
                                                                                —
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <button type="button" class="btn btn-sm btn-link p-0"
                                                                                onclick="moverLinha(this, -1, {{ $termo->id_termo }})"
                                                                                title="Mover para cima">
                                                                                <i class="fas fa-arrow-up text-primary"></i>
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm btn-link p-0"
                                                                                onclick="moverLinha(this, 1, {{ $termo->id_termo }})"
                                                                                title="Mover para baixo">
                                                                                <i class="fas fa-arrow-down text-primary"></i>
                                                                            </button>
                                                                        </td>
                                                                        <td><i class="fas fa-school text-success me-1"></i> Instituição</td>
                                                                        <td>{{ $termo->escola->nome_escola }}</td>
                                                                        <td>{{ $rep->email }}</td>
                                                                        <td>{{ $rep->nome }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @elseif(isset($termo->escola))
                                                                <tr data-ordem="{{ $ordem++ }}" data-tipo="escola_legado">
                                                                    <td class="text-center">
                                                                        @if(!empty($termo->escola->email))
                                                                            <input type="checkbox" class="form-check-input"
                                                                                name="remover_destinatarios[]"
                                                                                value="{{ $termo->escola->email }}">
                                                                        @else
                                                                            —
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-sm btn-link p-0"
                                                                            onclick="moverLinha(this, -1, {{ $termo->id_termo }})"
                                                                            title="Mover para cima">
                                                                            <i class="fas fa-arrow-up text-primary"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn-sm btn-link p-0"
                                                                            onclick="moverLinha(this, 1, {{ $termo->id_termo }})"
                                                                            title="Mover para baixo">
                                                                            <i class="fas fa-arrow-down text-primary"></i>
                                                                        </button>
                                                                    </td>
                                                                    <td><i class="fas fa-school text-success me-1"></i> Instituição</td>
                                                                    <td>{{ $termo->escola->nome_escola }}</td>
                                                                    <td>{{ $termo->escola->email ?? '—' }}</td>
                                                                    <td>{{ $termo->escola->nome_representante ?? '—' }}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <p class="text-muted small mb-0">
                                                    <strong>Observação:</strong> Marque em <strong>Remover?</strong> quem não deve
                                                    receber esse envio específico. Essa ação vale somente para este envio.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-paper-plane me-1"></i>
                                                    Enviar
                                                </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="deleteModal{{ $termo->id_termo }}" tabindex="-1"
                                    aria-labelledby="deleteModalLabel{{ $termo->id_termo }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $termo->id_termo }}">Confirmar
                                                    Exclusão</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Você tem certeza que deseja excluir este termo? Esta ação não poderá ser desfeita.
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <form action="{{ route('termos.destroy', $termo->id_termo) }}" method="POST"
                                                    style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Excluir</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if (method_exists($termos, 'links'))
            <!-- Paginação (rodapé) -->
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex align-items-center">
                    <span class="text-muted small">
                        @if($termos->total() > 0)
                            Mostrando {{ $termos->firstItem() }}–{{ $termos->lastItem() }} de {{ $termos->total() }}
                        @else
                            Nenhum registro encontrado
                        @endif
                    </span>
                    @php $pp = request('per_page', '25'); @endphp
                    <select id="perPageSelectorAdminBottom" class="form-select form-select-sm per-page-selector"
                        onchange="changePerPage(this.value)">
                        <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                        <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                        <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                    </select>
                </div>
                <div>
                    {{ $termos->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    @elseif (Auth::user()->nivel == 'empresa')

        <!-- Card de Filtro e Título -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body pb-2 pt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="fas fa-file-alt me-2 text-primary"></i>
                        Lista de Termos
                    </h4>
                </div>
                <form method="GET" action="{{ route('termos.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-10">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label for="escola" class="form-label mb-1">Instituição</label>
                                    <select name="escola" id="escola" class="form-select form-select-sm">
                                        <option value="">Todas</option>
                                        @foreach ($escolas as $escola)
                                            <option value="{{ $escola->id_escola ?? '' }}" {{ request('escola') == ($escola->id_escola ?? '') ? 'selected' : '' }}>
                                                {{ $escola->nome_escola ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="estagiario" class="form-label mb-1">Estagiário</label>
                                    <input type="text" name="estagiario" id="estagiario" class="form-control form-control-sm"
                                        value="{{ request('estagiario') }}" placeholder="Nome">
                                </div>
                                <div class="col-md-3">
                                    <label for="data_inicial" class="form-label mb-1">Data Inicial</label>
                                    <input type="date" name="data_inicial" id="data_inicial"
                                        class="form-control form-control-sm" value="{{ request('data_inicial') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="data_final" class="form-label mb-1">Data Final</label>
                                    <input type="date" name="data_final" id="data_final" class="form-control form-control-sm"
                                        value="{{ request('data_final') }}">
                                </div>
                                <!-- Campo para listar somente termos ativos ou rescindidos -->
                                <div class="col-md-2">
                                    <label for="status" class="form-label mb-1">Listar somente:</label>
                                    <select name="status" id="status" class="form-select form-select-sm">
                                        <option value="">
                                            Todos
                                        </option>
                                        <option value="ativos" {{ request('status') == 'ativos' ? 'selected' : '' }}>
                                            Ativos
                                        </option>
                                        <option value="rescindidos" {{ request('status') == 'rescindidos' ? 'selected' : '' }}>
                                            Rescindidos
                                        </option>
                                        <option value="vencidos" {{ request('status') == 'vencidos' ? 'selected' : '' }}>
                                            Vencidos
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex flex-column align-items-end justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-search"></i> Filtrar
                                    </button>
                                </div>
                                <div class="col-md-2 d-flex flex-column align-items-end justify-content-end gap-2">
                                    <a href="{{ route('termos.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fas fa-eraser"></i> Limpar
                                    </a>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-2 d-flex flex-column align-items-end justify-content-end gap-2">
                            <a href="{{ route('termos.gerarPdfRelatorioTermo', array_merge(['id_empresa' => Auth::user()->fk_id_empresa], request()->query())) }}"
                                class="btn btn-outline-danger btn-sm w-100 mt-1" target="_blank" data-bs-toggle="tooltip"
                                title="Gerar PDF">
                                <i class="fas fa-file-pdf"></i> Gerar PDF
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- FIM DO CARD DE FILTRO E TÍTULO -->

        <!-- Total de termos -->
        <div class="mb-2" style="font-weight: bold; font-size: 1.1em;">
            Total de termos: {{ method_exists($termos, 'total') ? $termos->total() : $termos->count() }}
        </div>

        @if (method_exists($termos, 'links'))
            <!-- Paginação (topo) -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <span class="text-muted small">
                        @if($termos->total() > 0)
                            Mostrando {{ $termos->firstItem() }}–{{ $termos->lastItem() }} de {{ $termos->total() }}
                        @else
                            Nenhum registro encontrado
                        @endif
                    </span>
                    @php $pp = request('per_page', '25'); @endphp
                    <select id="perPageSelectorEmpresa" class="form-select form-select-sm per-page-selector"
                        onchange="changePerPage(this.value)">
                        <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                        <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                        <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                    </select>
                </div>
                <div>
                    {{ $termos->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif

        <div style="max-height: 600px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 10px;">
            <table class="table table-termos align-middle">
                <thead style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 1;">
                    <tr>
                        <th>Número do Termo</th>
                        <th>Data</th>
                        <th>Nome Estagiario</th>
                        <th>Unidade Concedente</th>
                        <th>Instituição de Ensino</th>
                        <th>Status do Contrato</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($termos as $termo)
                        <tr>
                            <td>{{ $termo->numero_termo }}/{{ $termo->ano_termo }}</td>
                            <td>{{ $termo->data }}</td>
                            <td>{{ $termo->estagiario->nome_estagiario }}</td>
                            <td>{{ $termo->empresa->nome_empresa }}</td>
                            <td>{{ $termo->escola->nome_escola }}</td>
                            <td>
                                @php
                                    $isVencido = \Carbon\Carbon::parse($termo->data_fim_estagio)->isPast() && !$termo->rescisao;
                                @endphp
                                @if ($termo->rescisao)
                                    <span class="badge bg-danger">Rescindido</span>
                                @elseif ($isVencido)
                                    <span class="badge bg-warning text-dark">Contrato Vencido</span>
                                @else
                                    <span class="badge bg-success">Ativo</span>
                                @endif
                            </td>
                            <td class="actions-cell">
                                <div class="btn-group" role="group" aria-label="Ações">
                                    <a href="{{ route('termos.gerarPdf', $termo->id_termo) }}"
                                        class="btn btn-outline-danger btn-action" target="_blank" data-bs-toggle="tooltip"
                                        title="Gerar PDF" aria-label="Gerar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('termos.show', $termo->id_termo) }}"
                                        class="btn btn-outline-primary btn-action" data-bs-toggle="tooltip" title="Detalhes"
                                        aria-label="Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if (method_exists($termos, 'links'))
            <!-- Paginação (rodapé) -->
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex align-items-center">
                    <span class="text-muted small">
                        @if($termos->total() > 0)
                            Mostrando {{ $termos->firstItem() }}–{{ $termos->lastItem() }} de {{ $termos->total() }}
                        @else
                            Nenhum registro encontrado
                        @endif
                    </span>
                    @php $pp = request('per_page', '25'); @endphp
                    <select id="perPageSelectorEmpresaBottom" class="form-select form-select-sm per-page-selector"
                        onchange="changePerPage(this.value)">
                        <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                        <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                        <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                    </select>
                </div>
                <div>
                    {{ $termos->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    @endif

    <script>
        // Função para alterar itens por página
        function changePerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page'); // Reset para página 1
            window.location.href = url.toString();
        }

        // Função para mover linha para cima ou para baixo
        function moverLinha(botao, direcao, termoId) {
            const tr = botao.closest('tr');
            const tbody = document.getElementById('tbodyDestinatarios' + termoId);
            const linhas = Array.from(tbody.querySelectorAll('tr'));
            const indexAtual = linhas.indexOf(tr);

            // Calcular novo índice
            const novoIndex = indexAtual + direcao;

            // Validar limites
            if (novoIndex < 0 || novoIndex >= linhas.length) {
                return; // Não pode mover mais
            }

            // Trocar linhas
            if (direcao === -1) {
                // Mover para cima: inserir antes do anterior
                tbody.insertBefore(tr, linhas[novoIndex]);
            } else {
                // Mover para baixo: inserir depois do próximo
                if (novoIndex + 1 < linhas.length) {
                    tbody.insertBefore(tr, linhas[novoIndex + 1]);
                } else {
                    tbody.appendChild(tr);
                }
            }

            // Atualizar atributos data-ordem
            atualizarOrdens(termoId);
        }

        // Atualizar os atributos data-ordem após reordenação
        function atualizarOrdens(termoId) {
            const tbody = document.getElementById('tbodyDestinatarios' + termoId);
            const linhas = tbody.querySelectorAll('tr');
            linhas.forEach((tr, index) => {
                tr.setAttribute('data-ordem', index + 1);
            });
        }
    </script>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function setupFilter(searchId, selectId) {
            const searchInput = document.getElementById(searchId);
            const select = document.getElementById(selectId);
            const wrapper = document.getElementById(selectId + '_select_wrapper');
            const options = Array.from(select.options);

            searchInput.addEventListener('focus', function () {
                if (wrapper) {
                    wrapper.style.display = 'block';
                } else {
                    select.style.display = 'block';
                }
                setTimeout(() => searchInput.select(), 0);
            });

            searchInput.addEventListener('input', function () {
                const value = this.value.toLowerCase();
                select.innerHTML = '';
                options.forEach(option => {
                    if (option.text.toLowerCase().includes(value)) {
                        select.appendChild(option.cloneNode(true));
                    }
                });
                if (wrapper) {
                    wrapper.style.display = 'block';
                } else {
                    select.style.display = 'block';
                }
            });

            select.addEventListener('change', function () {
                const selected = select.options[select.selectedIndex];
                searchInput.value = selected.text;
                if (wrapper) {
                    wrapper.style.display = 'none';
                } else {
                    select.style.display = 'none';
                }

                // Se for o select de empresa, carregar os locais
                if (selectId === 'empresa') {
                    carregarLocais(this.value);
                }
            });

            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !select.contains(e.target) && (!wrapper || !wrapper.contains(e.target))) {
                    if (wrapper) {
                        wrapper.style.display = 'none';
                    } else {
                        select.style.display = 'none';
                    }
                }
            });
        }

        function carregarLocais(empresaId) {
            const localSelect = document.getElementById('local');
            const localFilterCol = document.getElementById('localFilterCol');

            if (!empresaId) {
                if (localFilterCol) localFilterCol.style.display = 'none';
                localSelect.innerHTML = '<option value="">Todos</option>';
                return;
            }

            // Mostrar o filtro de local
            if (localFilterCol) localFilterCol.style.display = '';

            // Buscar locais da empresa
            fetch('/locais?empresa=' + empresaId)
                .then(response => response.json())
                .then(locais => {
                    localSelect.innerHTML = '<option value="">Todos</option>';
                    locais.forEach(local => {
                        const option = document.createElement('option');
                        option.value = local.id_local;
                        option.textContent = local.descricao;
                        // Manter selecionado se estava no filtro
                        if ('{{ request("local") }}' == local.id_local) {
                            option.selected = true;
                        }
                        localSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Erro ao carregar locais:', error);
                    localSelect.innerHTML = '<option value="">Erro ao carregar locais</option>';
                });
        } setupFilter('escola_search', 'escola');
        if (document.getElementById('empresa_search')) {
            setupFilter('empresa_search', 'empresa');

            // Carregar locais se empresa já estiver selecionada
            const empresaSelecionada = document.getElementById('empresa').value;
            if (empresaSelecionada) {
                carregarLocais(empresaSelecionada);
            }
        }

        // Habilitar tooltips Bootstrap 5 nos botões de ação
        try {
            if (window.bootstrap && bootstrap.Tooltip) {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        } catch (e) {
            console.warn('Tooltips não inicializados:', e);
        }
    });
</script>