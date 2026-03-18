@extends('layouts.main')

@section('title', 'Avaliações - Termo ' . $termo->numero_termo . '/' . $termo->ano_termo)

@section('content')

    @include('components.modal-sistema')

    <style>
        .termo-info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .termo-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .info-item h6 {
            margin: 0;
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .info-item p {
            margin: 0.3rem 0 0 0;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .avaliacoes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .avaliacao-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .avaliacao-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .tipo-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .tipo-seis-meses {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .tipo-finalizacao {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-respondida {
            background-color: #d4edda;
            color: #155724;
        }

        .card-meta {
            font-size: 0.85rem;
            color: #666;
            margin: 0.5rem 0;
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
    </style>

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Avaliações do Termo</h1>
            <a href="{{ route('avaliacoes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

        <!-- Informações do Termo -->
        <div class="termo-info-card">
            <div class="termo-info">
                <div class="info-item">
                    <h6>Número do Termo</h6>
                    <p>{{ $termo->numero_termo }}/{{ $termo->ano_termo }}</p>
                </div>
                <div class="info-item">
                    <h6>Estagiário</h6>
                    <p>{{ $termo->estagiario->nome }}</p>
                </div>
                <div class="info-item">
                    <h6>Empresa</h6>
                    <p>{{ $termo->empresa->nome }}</p>
                </div>
                <div class="info-item">
                    <h6>Supervisor</h6>
                    <p>{{ $termo->supervisor->nome ?? 'Não informado' }}</p>
                </div>
                <div class="info-item">
                    <h6>Período</h6>
                    <p>
                        @if ($termo->data_inicio_estagio)
                            {{ \Carbon\Carbon::parse($termo->data_inicio_estagio)->format('d/m/Y') }}
                            até
                            @if ($termo->data_fim_estagio)
                                {{ \Carbon\Carbon::parse($termo->data_fim_estagio)->format('d/m/Y') }}
                            @else
                                Indeterminado
                            @endif
                        @else
                            Não informado
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Mensagens de Feedback -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($message = Session::get('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Avaliações -->
        @if ($avaliacoes->count() > 0)
            <div class="avaliacoes-grid">
                @foreach ($avaliacoes as $avaliacao)
                    <div class="avaliacao-card">
                        <div class="card-header">
                            <div>
                                <span class="tipo-badge tipo-{{ $avaliacao->tipo_avaliacao }}">
                                    @if ($avaliacao->tipo_avaliacao === 'seis_meses')
                                        6 Meses
                                    @else
                                        Finalização
                                    @endif
                                </span>
                            </div>
                            <span class="status-badge status-{{ $avaliacao->status }}">
                                @if ($avaliacao->status === 'pendente')
                                    Pendente
                                @elseif ($avaliacao->status === 'respondida')
                                    Respondida
                                @else
                                    Revisada
                                @endif
                            </span>
                        </div>

                        <div class="card-meta">
                            <p><strong>Criada em:</strong> {{ $avaliacao->created_at->format('d/m/Y H:i') }}</p>
                            @if ($avaliacao->respondida_em)
                                <p><strong>Respondida em:</strong> {{ $avaliacao->respondida_em->format('d/m/Y H:i') }}</p>
                                <p><strong>Por:</strong> {{ $avaliacao->respondida_por }}</p>
                            @endif
                        </div>

                        <div class="card-actions">
                            <!-- Ver Avaliação -->
                            <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="btn-small btn btn-info">
                                <i class="fas fa-eye"></i> Ver
                            </a>

                            <!-- Compartilhar (apenas pendentes) -->
                            @if ($avaliacao->status === 'pendente')
                                <button class="btn-small btn btn-success btn-compartilhar"
                                    data-avaliacao-id="{{ $avaliacao->id_avaliacao }}">
                                    <i class="fas fa-share-alt"></i> Link
                                </button>
                                <button class="btn-small btn btn-outline-warning btn-regenerar-link"
                                    data-avaliacao-id="{{ $avaliacao->id_avaliacao }}">
                                    <i class="fas fa-sync-alt"></i> Novo Link
                                </button>
                            @elseif ($avaliacao->status === 'respondida')
                                <a href="{{ route('avaliacoes.pdf', $avaliacao) }}" class="btn-small btn btn-outline-primary">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                                <button class="btn-small btn btn-warning"
                                    onclick="confirmarLimpar({{ $avaliacao->id_avaliacao }}, '{{ route('avaliacoes.limpar', $avaliacao) }}')">
                                    <i class="fas fa-redo"></i> Limpar
                                </button>
                            @endif

                            <!-- Excluir -->
                            <button class="btn-small btn btn-danger"
                                onclick="confirmarExcluir({{ $avaliacao->id_avaliacao }}, '{{ route('avaliacoes.destroy', $avaliacao) }}')">
                                <i class="fas fa-trash"></i> Del
                            </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginação -->
            @if ($avaliacoes->hasPages())
                <div class="mt-4">
                    {{ $avaliacoes->links() }}
                </div>
            @endif
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Nenhuma avaliação criada ainda para este termo.
            </div>
        @endif

        <!-- Botão para criar avaliação manual -->
        <div class="mt-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalGerarAvaliacao">
                <i class="fas fa-plus"></i> Gerar Avaliação Manual
            </button>
        </div>
    </div>

    <!-- Modal para compartilhamento de link -->
    <div class="modal fade" id="modalCompartilhar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Compartilhar Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Copie o link abaixo:</p>
                    <div class="input-group">
                        <input type="text" class="form-control" id="linkCompartilhamento" readonly>
                        <button class="btn btn-primary" type="button" onclick="copiarLink()">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para gerar avaliação manual -->
    <div class="modal fade" id="modalGerarAvaliacao" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gerar Avaliação Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('avaliacoes.gerar-manual') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="fk_id_termo" value="{{ $termo->id_termo }}">

                        <div class="form-group">
                            <label for="tipo_avaliacao" class="form-label">Tipo de Avaliação</label>
                            <select name="tipo_avaliacao" id="tipo_avaliacao" class="form-control" required>
                                <option value="">-- Selecione --</option>
                                <option value="seis_meses">6 Meses</option>
                                <option value="finalizacao">Finalização</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Gerar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function processarLinkCompartilhamento(avaliacaoId, endpoint, tituloErro) {
            fetch(`/avaliacoes/${avaliacaoId}/${endpoint}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        throw new Error(data.message || 'Não foi possível processar a solicitação.');
                    }
                    return data;
                })
                .then(data => {
                    document.getElementById('linkCompartilhamento').value = data.link;
                    const modal = new bootstrap.Modal(document.getElementById('modalCompartilhar'));
                    modal.show();
                    if (data.message) {
                        mostrarSucesso('Link pronto', data.message);
                    }
                })
                .catch(error => {
                    mostrarErro(tituloErro, 'Não conseguimos processar o link: ' + error.message);
                });
        }

        document.querySelectorAll('.btn-compartilhar').forEach(btn => {
            btn.addEventListener('click', function () {
                processarLinkCompartilhamento(this.getAttribute('data-avaliacao-id'), 'link-compartilhamento', 'Erro ao Gerar Link');
            });
        });

        document.querySelectorAll('.btn-regenerar-link').forEach(btn => {
            btn.addEventListener('click', function () {
                processarLinkCompartilhamento(this.getAttribute('data-avaliacao-id'), 'regenerar-link', 'Erro ao Gerar Novo Link');
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