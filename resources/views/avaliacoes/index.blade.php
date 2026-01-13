@extends('layouts.main')

@section('title', 'Avaliações')

@section('content')

    @include('components.modal-sistema')

    <style>
        .filtro-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .filtro-card h5 {
            margin-bottom: 1rem;
            color: #333;
            font-weight: 600;
        }

        .filtro-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge-info {
            background-color: #17a2b8;
            color: #fff;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-respondida {
            background-color: #d4edda;
            color: #155724;
        }

        .tabela-avaliacoes {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            overflow: hidden;
        }

        .tabela-avaliacoes thead {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .tabela-avaliacoes th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
        }

        .tabela-avaliacoes td {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .tabela-avaliacoes tbody tr:hover {
            background-color: #f8f9fa;
        }

        .acoes-cell {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
    </style>
    <!-- Card de Filtros -->
    <div class="filtro-card">
        <h5><i class="fas fa-filter"></i> Filtros</h5>
        <form method="GET">
            <div class="filtro-row">
                <div>
                    <label for="search" class="form-label mb-1 fw-semibold">Buscar</label>
                    <input type="text" name="search" id="search" class="form-control form-control-sm"
                        placeholder="Termo ou estagiário..." value="{{ request('search') }}">
                </div>

                <div>
                    <label for="tipo_avaliacao" class="form-label mb-1 fw-semibold">Tipo de Avaliação</label>
                    <select name="tipo_avaliacao" id="tipo_avaliacao" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="seis_meses" {{ request('tipo_avaliacao') == 'seis_meses' ? 'selected' : '' }}>
                            6 Meses
                        </option>
                        <option value="finalizacao" {{ request('tipo_avaliacao') == 'finalizacao' ? 'selected' : '' }}>
                            Finalização
                        </option>
                    </select>
                </div>

                <div>
                    <label for="status" class="form-label mb-1 fw-semibold">Status</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>
                            Pendente
                        </option>
                        <option value="respondida" {{ request('status') == 'respondida' ? 'selected' : '' }}>
                            Respondida
                        </option>
                        <option value="revisada" {{ request('status') == 'revisada' ? 'selected' : '' }}>
                            Revisada
                        </option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="{{ route('avaliacoes.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-redo"></i> Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Mensagens de Feedback -->
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tabela de Avaliações -->
    @if ($avaliacoes->count() > 0)
        <table class="tabela-avaliacoes">
            <thead>
                <tr>
                    <th>Nº Termo</th>
                    <th>Estagiário</th>
                    <th>Supervisor</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Criada em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($avaliacoes as $avaliacao)
                    <tr>
                        <td>
                            <strong>{{ $avaliacao->termo->numero_termo }}/{{ $avaliacao->termo->ano_termo }}</strong>
                        </td>
                        <td>
                            {{ $avaliacao->termo->estagiario->nome_estagiario ?? 'N/A' }}
                        </td>
                        <td>
                            {{ $avaliacao->supervisor->nome ?? $avaliacao->termo->supervisor->nome_supervisor ?? 'N/A' }}
                        </td>
                        <td>
                            @if ($avaliacao->tipo_avaliacao === 'seis_meses')
                                <span class="badge badge-info">6 Meses</span>
                            @else
                                <span class="badge badge-secondary">Finalização</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge status-{{ $avaliacao->status }}">
                                @if ($avaliacao->status === 'pendente')
                                    Pendente
                                @elseif ($avaliacao->status === 'respondida')
                                    Respondida
                                @else
                                    Revisada
                                @endif
                            </span>
                        </td>
                        <td>
                            {{ $avaliacao->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <div class="acoes-cell">
                                <!-- Botão Visualizar -->
                                <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="btn btn-sm btn-info"
                                    title="Visualizar avaliação">
                                    <i class="fas fa-eye"></i> Ver
                                </a>

                                <!-- Botão Ver Outras Avaliações do Termo -->
                                <a href="{{ route('avaliacoes.por-termo', $avaliacao->termo) }}" class="btn btn-sm btn-secondary"
                                    title="Ver todas as avaliações deste termo">
                                    <i class="fas fa-list"></i> Termo
                                </a>

                                <!-- Botão Compartilhar Link (apenas se pendente) -->
                                @if ($avaliacao->status === 'pendente')
                                    <button class="btn btn-sm btn-success btn-compartilhar"
                                        data-avaliacao-id="{{ $avaliacao->id_avaliacao }}" title="Copiar link de compartilhamento">
                                        <i class="fas fa-share-alt"></i> Link
                                    </button>
                                @elseif ($avaliacao->status === 'respondida')
                                    <!-- Botões para avaliação respondida -->
                                    <a href="{{ route('avaliacoes.pdf', $avaliacao) }}" class="btn btn-sm btn-outline-primary"
                                        title="Baixar PDF da avaliação">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                    <button class="btn btn-sm btn-warning" title="Limpar avaliação para nova resposta"
                                        onclick="confirmarLimpar({{ $avaliacao->id_avaliacao }}, '{{ route('avaliacoes.limpar', $avaliacao) }}')">
                                        <i class="fas fa-redo"></i> Limpar
                                    </button>
                                @endif

                                <!-- Botão Excluir -->
                                <button class="btn btn-sm btn-danger" title="Excluir avaliação"
                                    onclick="confirmarExcluir({{ $avaliacao->id_avaliacao }}, '{{ route('avaliacoes.destroy', $avaliacao) }}')">
                                    <i class="fas fa-trash"></i> Del
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginação -->
        <div class="mt-4">
            {{ $avaliacoes->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Nenhuma avaliação pendente no momento.
        </div>
    @endif
    </div>

    <!-- Modal para compartilhamento de link -->
    <div class="modal fade" id="modalCompartilhar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Compartilhar Link de Avaliação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Copie o link abaixo e envie para o supervisor responder:</p>
                    <div class="input-group">
                        <input type="text" class="form-control" id="linkCompartilhamento" readonly>
                        <button class="btn btn-primary" type="button" onclick="copiarLink()">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn-compartilhar').forEach(btn => {
            btn.addEventListener('click', function () {
                const avaliacaoId = this.getAttribute('data-avaliacao-id');
                const url = `/avaliacoes/${avaliacaoId}/link-compartilhamento`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('linkCompartilhamento').value = data.link;
                        const modal = new bootstrap.Modal(document.getElementById('modalCompartilhar'));
                        modal.show();
                    })
                    .catch(error => {
                        mostrarErro('Erro ao Gerar Link', 'Não conseguimos gerar o link: ' + error.message);
                    });
            });
        });

        function confirmarLimpar(avaliacaoId, url) {
            mostrarConfirmacao(
                'Limpar Avaliação',
                'Tem certeza que deseja limpar esta avaliação?',
                function () {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `@csrf`;
                    document.body.appendChild(form);
                    form.submit();
                }
            );
        }

        function confirmarExcluir(avaliacaoId, url) {
            mostrarConfirmacao(
                'Excluir Avaliação',
                'Tem certeza que deseja excluir esta avaliação?',
                function () {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `@csrf
                            @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            );
        }

        function copiarLink() {
            const input = document.getElementById('linkCompartilhamento');
            input.select();

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(input.value).then(() => {
                    mostrarSucesso('Link Copiado', 'O link foi copiado para a área de transferência!');
                }).catch(err => {
                    document.execCommand('copy');
                    mostrarSucesso('Link Copiado', 'O link foi copiado para a área de transferência!');
                });
            } else {
                document.execCommand('copy');
                mostrarSucesso('Link Copiado', 'O link foi copiado para a área de transferência!');
            }
        }
    </script>

@endsection