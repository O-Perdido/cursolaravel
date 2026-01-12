@extends('layouts.main')

@section('title', 'Visualizar Avaliação')

@section('content')

    <style>
        .avaliacao-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .avaliacao-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }

        .info-box {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            border-left: 4px solid #667eea;
        }

        .info-box label {
            font-weight: 600;
            color: #555;
            font-size: 0.85rem;
            display: block;
            margin-bottom: 0.3rem;
        }

        .info-box p {
            margin: 0;
            color: #333;
            font-size: 0.95rem;
        }

        .questoes-container {
            margin-top: 2rem;
        }

        .questao-item {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
        }

        .questao-numero {
            display: inline-block;
            background: #667eea;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            font-weight: bold;
            margin-right: 0.5rem;
            font-size: 0.9rem;
        }

        .questao-texto {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
            margin: 1rem 0;
            line-height: 1.5;
        }

        .resposta-label {
            font-weight: 600;
            color: #555;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin: 0.5rem 0;
            display: block;
        }

        .resposta-box {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 1rem;
            min-height: 60px;
            line-height: 1.6;
            color: #333;
        }

        .resposta-vazia {
            color: #999;
            font-style: italic;
        }

        .status-section {
            background: #f0f7ff;
            border: 1px solid #b3d9ff;
            border-radius: 4px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 600;
            margin: 0.5rem 0;
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-respondida {
            background-color: #d4edda;
            color: #155724;
        }

        .botoes-acoes {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #f0f0f0;
        }

        .btn-voltar {
            align-self: center;
        }
    </style>

    <div class="container-fluid">
        <a href="{{ route('avaliacoes.index') }}" class="btn btn-outline-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>

        <div class="avaliacao-container">
            <!-- Header da Avaliação -->
            <div class="avaliacao-header">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <h1 style="margin: 0; margin-bottom: 0.5rem;">Avaliação de Desempenho</h1>
                        <p style="margin: 0; color: #666; font-size: 0.95rem;">
                            Avaliação de estágio do estagiário
                        </p>
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
            </div>

            <!-- Status Section -->
            <div class="status-section">
                <h5 style="margin-top: 0;">Informações da Avaliação</h5>
                <div class="info-grid">
                    <div class="info-box">
                        <label>Termo</label>
                        <p>{{ $avaliacao->termo->numero_termo }}/{{ $avaliacao->termo->ano_termo }}</p>
                    </div>
                    <div class="info-box">
                        <label>Estagiário</label>
                        <p>{{ $avaliacao->termo->estagiario->nome_estagiario }}</p>
                    </div>
                    <div class="info-box">
                        <label>Empresa</label>
                        <p>{{ $avaliacao->termo->empresa->nome_empresa }}</p>
                    </div>
                    <div class="info-box">
                        <label>Supervisor Responsável</label>
                        <p>{{ $avaliacao->supervisor->nome ?? $avaliacao->termo->supervisor->nome_supervisor ?? 'Não informado' }}</p>
                    </div>
                    <div class="info-box">
                        <label>Tipo de Avaliação</label>
                        <p>
                            @if ($avaliacao->tipo_avaliacao === 'seis_meses')
                                Avaliação de 6 Meses
                            @else
                                Avaliação de Finalização
                            @endif
                        </p>
                    </div>
                    <div class="info-box">
                        <label>Criada em</label>
                        <p>{{ $avaliacao->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if ($avaliacao->respondida_em)
                        <div class="info-box">
                            <label>Respondida em</label>
                            <p>{{ $avaliacao->respondida_em->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="info-box">
                            <label>Respondida por</label>
                            <p>{{ $avaliacao->respondida_por }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Questões e Respostas -->
            <div class="questoes-container">
                <h3 style="margin-bottom: 1.5rem; color: #333;">Questões e Respostas</h3>

                @if ($avaliacao->questoes_respostas && count($avaliacao->questoes_respostas) > 0)
                    @foreach ($avaliacao->questoes_respostas as $questao)
                        <div class="questao-item">
                            <div>
                                <span class="questao-numero">{{ $questao['ordem'] ?? $loop->iteration }}</span>
                                <span class="questao-texto">{{ $questao['questao'] }}</span>
                            </div>

                            <label class="resposta-label">Resposta:</label>
                            <div class="resposta-box">
                                @if (!empty($questao['resposta']))
                                    {{ $questao['resposta'] }}
                                @else
                                    <span class="resposta-vazia">Sem resposta</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Nenhuma questão foi carregada. Avaliação sem dados.
                    </div>
                @endif
            </div>

            <!-- Botões de Ação -->
            <div class="botoes-acoes">
                @if ($avaliacao->status === 'pendente')
                    <button class="btn btn-success" id="btnCompartilhar"
                        onclick="gerarECompartilhar({{ $avaliacao->id_avaliacao }})">
                        <i class="fas fa-share-alt"></i> Compartilhar Link
                    </button>
                @elseif ($avaliacao->status === 'respondida')
                    <a href="{{ route('avaliacoes.pdf', $avaliacao) }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-pdf"></i> Baixar PDF
                    </a>
                    <form action="{{ route('avaliacoes.limpar', $avaliacao) }}" method="POST" style="display: inline;"
                        onsubmit="return confirm('Tem certeza que deseja limpar esta avaliação para nova resposta?');">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-redo"></i> Limpar para Nova Resposta
                        </button>
                    </form>
                @endif

                <a href="{{ route('avaliacoes.por-termo', $avaliacao->termo) }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Ver Outras Avaliações
                </a>

                <form action="{{ route('avaliacoes.destroy', $avaliacao) }}" method="POST" style="display: inline;"
                    onsubmit="return confirm('Tem certeza que deseja excluir esta avaliação?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
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
                    <p>Copie o link abaixo e envie para o supervisor responder a avaliação:</p>
                    <div class="input-group">
                        <input type="text" class="form-control" id="linkCompartilhamento" readonly>
                        <button class="btn btn-primary" type="button" onclick="copiarLink()">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2">
                        O link expira após o supervisor responder a avaliação.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script>
        function gerarECompartilhar(avaliacaoId) {
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
                    alert('Erro ao gerar link: ' + error.message);
                });
        }

        function copiarLink() {
            const input = document.getElementById('linkCompartilhamento');
            input.select();
            document.execCommand('copy');
            alert('Link copiado para a área de transferência!');
        }
    </script>

@endsection