@extends('layouts.main')

@section('title', 'Visualizar Avaliação')

@section('content')

    <style>
        .avaliacao-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card-principal {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .header-avaliacao {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: start;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-info h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-info h1 i {
            font-size: 1.4rem;
        }

        .header-details {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            font-size: 0.9rem;
            opacity: 0.95;
        }

        .header-detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-detail-item i {
            opacity: 0.8;
        }

        .header-badges {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-end;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
        }

        .badge-tipo {
            background: rgba(255, 255, 255, 0.15);
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .info-section {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e9ecef;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .info-item-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .info-item-content label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #6c757d;
            font-weight: 600;
            letter-spacing: 0.3px;
            display: block;
            margin-bottom: 0.2rem;
        }

        .info-item-content p {
            margin: 0;
            color: #212529;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .questoes-section {
            padding: 2rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #212529;
            margin: 0 0 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .section-title i {
            color: #667eea;
        }

        .questao-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            transition: all 0.2s ease;
        }

        .questao-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }

        .questao-header {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .questao-numero {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .questao-texto {
            flex: 1;
            font-weight: 600;
            color: #212529;
            font-size: 0.95rem;
            line-height: 1.6;
            padding-top: 0.4rem;
        }

        .resposta-container {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px dashed #dee2e6;
        }

        .resposta-escala {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .resposta-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .resposta-badge.muito-ruim {
            background: #dc3545;
        }

        .resposta-badge.ruim {
            background: #fd7e14;
        }

        .resposta-badge.regular {
            background: #ffc107;
            color: #212529;
        }

        .resposta-badge.bom {
            background: #20c997;
        }

        .resposta-badge.muito-bom,
        .resposta-badge.excelente {
            background: #28a745;
        }

        .resposta-numerica {
            background: #f0f0f0;
            border: 2px solid #dee2e6;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
            color: #495057;
        }

        .resposta-estrelas {
            display: flex;
            gap: 0.25rem;
            font-size: 1.2rem;
        }

        .resposta-estrelas i {
            color: #ffc107;
        }

        .resposta-vazia {
            color: #6c757d;
            font-style: italic;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            background: white;
            border: 1px dashed #dee2e6;
            border-radius: 8px;
        }

        .resposta-vazia i {
            color: #adb5bd;
        }

        .acoes-footer {
            padding: 1.25rem 2rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-decoration: none;
        }

        .btn i {
            font-size: 1rem;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            background: white;
        }

        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #212529;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .alert-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-left: 4px solid #2196f3;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-info i {
            font-size: 1.25rem;
            color: #1976d2;
        }

        @media (max-width: 768px) {
            .header-avaliacao {
                flex-direction: column;
            }

            .header-badges {
                align-items: flex-start;
                width: 100%;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .resposta-escala {
                flex-direction: column;
                align-items: flex-start;
            }

            .acoes-footer {
                flex-direction: column;
            }

            .acoes-footer .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="container-fluid">
        <a href="{{ route('avaliacoes.index') }}" class="btn btn-outline-secondary mb-4">
            <i class="fas fa-arrow-left"></i> Voltar para Avaliações
        </a>

        <div class="avaliacao-container">
            <div class="card-principal">
                <!-- Header -->
                <div class="header-avaliacao">
                    <div class="header-info">
                        <h1>
                            <i class="fas fa-clipboard-check"></i>
                            Avaliação de Desempenho
                        </h1>
                        <div class="header-details">
                            <div class="header-detail-item">
                                <i class="fas fa-user-graduate"></i>
                                <span>{{ $avaliacao->termo->estagiario->nome_estagiario }}</span>
                            </div>
                            <div class="header-detail-item">
                                <i class="fas fa-file-contract"></i>
                                <span>Termo {{ $avaliacao->termo->numero_termo }}/{{ $avaliacao->termo->ano_termo }}</span>
                            </div>
                            @if ($avaliacao->respondida_em)
                                <div class="header-detail-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Respondida em {{ $avaliacao->respondida_em->format('d/m/Y \à\s H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="header-badges">
                        <span class="badge-status">
                            @if ($avaliacao->status === 'pendente')
                                <i class="fas fa-clock"></i> Pendente
                            @elseif ($avaliacao->status === 'respondida')
                                <i class="fas fa-check-circle"></i> Respondida
                            @else
                                <i class="fas fa-star"></i> Revisada
                            @endif
                        </span>
                        <span class="badge-tipo">
                            <i class="fas fa-tag"></i>
                            @if ($avaliacao->tipo_avaliacao === 'seis_meses')
                                Avaliação de 6 Meses
                            @else
                                Avaliação de Finalização
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Informações -->
                <div class="info-section">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-item-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="info-item-content">
                                <label>Empresa</label>
                                <p>{{ $avaliacao->termo->empresa->nome_empresa }}</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-item-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="info-item-content">
                                <label>Supervisor</label>
                                <p>{{ $avaliacao->supervisor->nome ?? $avaliacao->termo->supervisor->nome_supervisor ?? 'Não informado' }}</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item-icon">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <div class="info-item-content">
                                <label>Criada em</label>
                                <p>{{ $avaliacao->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if ($avaliacao->respondida_por)
                            <div class="info-item">
                                <div class="info-item-icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="info-item-content">
                                    <label>Respondida por</label>
                                    <p>{{ $avaliacao->respondida_por }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Questões e Respostas -->
                <div class="questoes-section">
                    <h2 class="section-title">
                        <i class="fas fa-comments"></i>
                        Questões e Respostas
                    </h2>

                    @if ($avaliacao->questoes_respostas && count($avaliacao->questoes_respostas) > 0)
                        @foreach ($avaliacao->questoes_respostas as $questao)
                            <div class="questao-card">
                                <div class="questao-header">
                                    <div class="questao-numero">{{ $questao['ordem'] ?? $loop->iteration }}</div>
                                    <div class="questao-texto">{{ $questao['questao'] }}</div>
                                </div>

                                <div class="resposta-container">
                                    @php
                                        $resposta = $questao['resposta'] ?? '';
                                        $respostaLower = strtolower(trim($resposta));
                                        
                                        // Mapa de valores numéricos para escala
                                        $escalaNumericaMap = [
                                            '1' => ['classe' => 'muito-ruim', 'texto' => 'Muito Ruim', 'valor' => 1],
                                            '2' => ['classe' => 'ruim', 'texto' => 'Ruim', 'valor' => 2],
                                            '3' => ['classe' => 'regular', 'texto' => 'Regular', 'valor' => 3],
                                            '4' => ['classe' => 'bom', 'texto' => 'Bom', 'valor' => 4],
                                            '5' => ['classe' => 'muito-bom', 'texto' => 'Muito Bom', 'valor' => 5],
                                        ];
                                        
                                        // Mapa de respostas em texto para escala
                                        $escalaTextoMap = [
                                            'muito ruim' => ['classe' => 'muito-ruim', 'texto' => 'Muito Ruim', 'valor' => 1],
                                            'ruim' => ['classe' => 'ruim', 'texto' => 'Ruim', 'valor' => 2],
                                            'regular' => ['classe' => 'regular', 'texto' => 'Regular', 'valor' => 3],
                                            'bom' => ['classe' => 'bom', 'texto' => 'Bom', 'valor' => 4],
                                            'muito bom' => ['classe' => 'muito-bom', 'texto' => 'Muito Bom', 'valor' => 5],
                                            'excelente' => ['classe' => 'excelente', 'texto' => 'Excelente', 'valor' => 5],
                                            'péssimo' => ['classe' => 'muito-ruim', 'texto' => 'Péssimo', 'valor' => 1],
                                            'ótimo' => ['classe' => 'muito-bom', 'texto' => 'Ótimo', 'valor' => 5],
                                        ];
                                        
                                        $isEscala = false;
                                        $escalaInfo = null;
                                        
                                        // Verificar se é um número de 1 a 5
                                        if (is_numeric($resposta) && $resposta >= 1 && $resposta <= 5) {
                                            $isEscala = true;
                                            $escalaInfo = $escalaNumericaMap[(string)$resposta];
                                        } 
                                        // Verificar se contém texto de escala
                                        else {
                                            foreach ($escalaTextoMap as $key => $info) {
                                                if (str_contains($respostaLower, $key)) {
                                                    $isEscala = true;
                                                    $escalaInfo = $info;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp

                                    @if (!empty($resposta))
                                        @if ($isEscala && $escalaInfo)
                                            <div class="resposta-escala">
                                                <span class="resposta-badge {{ $escalaInfo['classe'] }}">
                                                    {{ $escalaInfo['texto'] }}
                                                </span>
                                                <span class="resposta-numerica">
                                                    {{ $escalaInfo['valor'] }}/5
                                                </span>
                                                <div class="resposta-estrelas">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $escalaInfo['valor'])
                                                            <i class="fas fa-star"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                        @else
                                            <div class="resposta-texto">{{ $resposta }}</div>
                                        @endif
                                    @else
                                        <div class="resposta-vazia">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Sem resposta
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert-info">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Nenhuma questão encontrada</strong>
                                <p style="margin: 0.25rem 0 0 0; font-size: 0.9rem;">Esta avaliação ainda não possui questões cadastradas.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Ações -->
                <div class="acoes-footer">
                    @if ($avaliacao->status === 'pendente')
                        <button class="btn btn-success" id="btnCompartilhar"
                            onclick="gerarECompartilhar({{ $avaliacao->id_avaliacao }})">
                            <i class="fas fa-share-alt"></i> Compartilhar Link
                        </button>
                    @elseif ($avaliacao->status === 'respondida')
                        <a href="{{ route('avaliacoes.pdf', $avaliacao) }}" class="btn btn-primary">
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
    </div>

    <!-- Modal para compartilhamento de link -->
    <div class="modal fade" id="modalCompartilhar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                    <h5 class="modal-title" style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-share-alt"></i>
                        Compartilhar Link de Avaliação
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <p style="margin: 0; display: flex; align-items: center; gap: 0.5rem; color: #495057;">
                            <i class="fas fa-info-circle" style="color: #667eea;"></i>
                            Copie o link abaixo e envie para o supervisor responder a avaliação:
                        </p>
                    </div>
                    
                    <div class="input-group" style="margin-bottom: 1rem;">
                        <input type="text" class="form-control" id="linkCompartilhamento" readonly 
                            style="border: 2px solid #dee2e6; border-radius: 8px 0 0 8px; padding: 0.75rem;">
                        <button class="btn btn-primary" type="button" onclick="copiarLink()"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 0 8px 8px 0; padding: 0.75rem 1.5rem;">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                    </div>
                    
                    <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 0.75rem 1rem; border-radius: 4px;">
                        <small style="color: #856404; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                            O link expira após o supervisor responder a avaliação.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function gerarECompartilhar(avaliacaoId) {
            const btn = document.getElementById('btnCompartilhar');
            const originalText = btn.innerHTML;
            
            // Mostrar loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gerando link...';
            
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
                    
                    // Restaurar botão
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao gerar link: ' + error.message);
                    
                    // Restaurar botão
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        }

        function copiarLink() {
            const input = document.getElementById('linkCompartilhamento');
            input.select();
            input.setSelectionRange(0, 99999); // Para dispositivos móveis
            
            // Tentar usar a API moderna primeiro
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(input.value).then(() => {
                    mostrarMensagemSucesso();
                }).catch(err => {
                    // Fallback para método antigo
                    document.execCommand('copy');
                    mostrarMensagemSucesso();
                });
            } else {
                // Fallback para navegadores antigos
                document.execCommand('copy');
                mostrarMensagemSucesso();
            }
        }
        
        function mostrarMensagemSucesso() {
            // Criar toast de sucesso
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                box-shadow: 0 4px 16px rgba(40, 167, 69, 0.3);
                z-index: 9999;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-weight: 600;
                animation: slideIn 0.3s ease;
            `;
            toast.innerHTML = '<i class="fas fa-check-circle"></i> Link copiado para a área de transferência!';
            document.body.appendChild(toast);
            
            // Remover após 3 segundos
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Adicionar animações CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>

@endsection
