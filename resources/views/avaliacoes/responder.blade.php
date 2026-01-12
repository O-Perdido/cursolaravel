@extends('layouts.main')

@section('title', 'Responder Avaliação de Desempenho')

@section('content')

    <style>
        .responder-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            margin-left: -2rem;
            margin-right: -2rem;
            margin-top: -2rem;
        }

        .form-header h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        .form-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.95;
            font-size: 0.95rem;
        }

        .info-boxes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.15);
            padding: 1rem;
            border-radius: 4px;
            border-left: 3px solid white;
        }

        .info-box label {
            display: block;
            font-size: 0.8rem;
            opacity: 0.9;
            margin-bottom: 0.3rem;
        }

        .info-box p {
            margin: 0;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .instrucoes {
            background: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 4px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            color: #1565c0;
        }

        .instrucoes h5 {
            margin-top: 0;
            color: #1565c0;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h4 {
            color: #333;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #667eea;
        }

        .questao-group {
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

        .questao-label {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
            margin: 0.5rem 0;
            line-height: 1.5;
        }

        .questao-input {
            margin-top: 1rem;
        }

        .form-control {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 0.75rem;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .email-section {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .email-section label {
            font-weight: 600;
            color: #856404;
        }

        .email-section .form-control {
            margin-top: 0.5rem;
        }

        .botoes-acao {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #f0f0f0;
        }

        .btn-enviador {
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
        }

        .btn-enviar {
            background: #28a745;
            color: white;
        }

        .btn-enviar:hover {
            background: #218838;
        }

        .btn-cancelar {
            background: #6c757d;
            color: white;
            text-decoration: none;
        }

        .btn-cancelar:hover {
            background: #5a6268;
        }

        .loading-indicator {
            display: none;
            text-align: center;
            padding: 1rem;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .escala-opcoes {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }

        .escala-radio {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .escala-radio input[type="radio"] {
            cursor: pointer;
        }

        .escala-radio label {
            margin: 0;
            cursor: pointer;
            font-weight: normal;
        }
    </style>

    <div class="responder-container">
        <!-- Header -->
        <div class="form-header">
            <h1>Avaliação de Desempenho do Estagiário</h1>
            <p>Por favor, preencha todos os campos com suas observações e avaliações sinceras.</p>
            <div class="info-boxes">
                <div class="info-box">
                    <label>Estagiário</label>
                    <p>{{ $avaliacao->termo->estagiario->nome }}</p>
                </div>
                <div class="info-box">
                    <label>Empresa</label>
                    <p>{{ $avaliacao->termo->empresa->nome }}</p>
                </div>
                <div class="info-box">
                    <label>Período</label>
                    <p>
                        @if ($avaliacao->termo->data_inicio_estagio)
                            {{ \Carbon\Carbon::parse($avaliacao->termo->data_inicio_estagio)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div class="info-box">
                    <label>Tipo de Avaliação</label>
                    <p>
                        @if ($avaliacao->tipo_avaliacao === 'seis_meses')
                            6 Meses
                        @else
                            Finalização
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Instruções -->
        <div class="instrucoes">
            <h5><i class="fas fa-info-circle"></i> Instruções</h5>
            <ul style="margin: 0; padding-left: 1.5rem;">
                <li>Avalie o desempenho do estagiário de forma honesta e construtiva.</li>
                <li>Todos os campos devem ser preenchidos antes do envio.</li>
                <li>Suas respostas são importantes para o desenvolvimento do estagiário.</li>
                <li>Este link de resposta será desativado após o envio da avaliação.</li>
            </ul>
        </div>

        <!-- Formulário -->
        <form id="formAvaliacao" @submit.prevent="enviarAvaliacao">
            @csrf

            <!-- Email do Supervisor -->
            <div class="email-section">
                <label for="email_supervisor">
                    <i class="fas fa-envelope"></i> Email do Supervisor
                </label>
                <input type="email" id="email_supervisor" name="email_supervisor" class="form-control"
                    placeholder="seu.email@example.com" required>
                <small class="text-muted">Seu email será registrado como quem respondeu esta avaliação.</small>
            </div>

            <!-- Questões -->
            <div class="form-section">
                <h4>Questões de Avaliação</h4>

                @if ($avaliacao->questoes_respostas && count($avaliacao->questoes_respostas) > 0)
                    @foreach ($avaliacao->questoes_respostas as $index => $questao)
                        <div class="questao-group">
                            <div>
                                <span class="questao-numero">{{ $questao['ordem'] ?? ($index + 1) }}</span>
                                <span class="questao-label">{{ $questao['questao'] }}</span>
                            </div>

                            <div class="questao-input">
                                @if ($questao['tipo'] === 'escala_1_5')
                                    <!-- Campo de Escala -->
                                    <div class="escala-opcoes">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <div class="escala-radio">
                                                <input type="radio" id="resp_{{ $index }}_{{ $i }}" name="respostas[{{ $index }}]"
                                                    value="{{ $i }}" required>
                                                <label for="resp_{{ $index }}_{{ $i }}">
                                                    @if ($i === 1)
                                                        Insuficiente
                                                    @elseif ($i === 2)
                                                        Ruim
                                                    @elseif ($i === 3)
                                                        Regular
                                                    @elseif ($i === 4)
                                                        Bom
                                                    @else
                                                        Excelente
                                                    @endif
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                @else
                                    <!-- Campo de Texto Longo -->
                                    <textarea name="respostas[{{ $index }}]" class="form-control"
                                        placeholder="Digite sua resposta aqui..." required></textarea>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        Erro: Nenhuma questão foi carregada. Entre em contato com o suporte.
                    </div>
                @endif
            </div>

            <!-- Botões de Ação -->
            <div class="botoes-acao">
                <button type="submit" class="btn btn-enviador btn-enviar">
                    <i class="fas fa-check"></i> Enviar Avaliação
                </button>
            </div>

            <!-- Indicador de Carregamento -->
            <div class="loading-indicator" id="loadingIndicator">
                <div class="spinner"></div>
                <p>Enviando sua avaliação...</p>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('formAvaliacao').addEventListener('submit', async function (e) {
            e.preventDefault();

            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.style.display = 'block';

            const formData = new FormData(this);
            const dados = {
                email_supervisor: formData.get('email_supervisor'),
                respostas: {}
            };

            // Coleta as respostas
            formData.forEach((value, key) => {
                if (key.startsWith('respostas[')) {
                    const index = key.match(/\[(\d+)\]/)[1];
                    dados.respostas[index] = value;
                }
            });

            try {
                const response = await fetch('{{ route("avaliacoes.salvar-respostas", ["token" => $avaliacao->token_compartilhamento]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(dados)
                });

                if (response.ok) {
                    const result = await response.json();
                    alert(result.message);
                    window.location.href = '{{ route("avaliacoes.sucesso") }}';
                } else {
                    alert('Erro ao enviar avaliação. Tente novamente.');
                    loadingIndicator.style.display = 'none';
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao enviar avaliação: ' + error.message);
                loadingIndicator.style.display = 'none';
            }
        });
    </script>

@endsection