@extends('layouts.main')

@section('title', 'Chamado #' . $chamado->protocolo)

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-headset me-2"></i>Chamado #{{ $chamado->protocolo }}
                        </h5>
                        {!! $chamado->getStatusBadge() !!}
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">Tipo de Chamado</h6>
                        <p><span class="badge bg-info">{{ $chamado->tipoChamado->nome }}</span></p>
                    </div>

                    @if($chamado->isRescisao())
                        <!-- Detalhes de Rescisão -->
                        <div class="mb-3">
                            <h6 class="text-muted">Termo de Estágio</h6>
                            @if($chamado->termo)
                                <p>
                                    <strong>Número:</strong>
                                    {{ $chamado->termo->numero_termo }}/{{ $chamado->termo->ano_termo }}<br>
                                    <strong>Estagiário:</strong> {{ $chamado->termo->estagiario->nome_estagiario }}<br>
                                    <strong>CPF:</strong> {{ $chamado->termo->estagiario->numero_cpf }}
                                </p>
                            @else
                                <p class="text-danger">Termo não encontrado</p>
                            @endif
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">Data da Rescisão</h6>
                            <p>{{ $chamado->data_rescisao ? $chamado->data_rescisao->format('d/m/Y') : '-' }}</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">Motivo da Rescisão</h6>
                            <p class="text-break">{{ $chamado->motivo_rescisao }}</p>
                        </div>

                    @elseif($chamado->isAlteracao())
                        <!-- Detalhes de Alteração -->
                        <div class="mb-3">
                            <h6 class="text-muted">Termo de Estágio</h6>
                            @if($chamado->termo)
                                <p>
                                    <strong>Número:</strong>
                                    {{ $chamado->termo->numero_termo }}/{{ $chamado->termo->ano_termo }}<br>
                                    <strong>Estagiário:</strong> {{ $chamado->termo->estagiario->nome_estagiario }}<br>
                                    <strong>CPF:</strong> {{ $chamado->termo->estagiario->numero_cpf }}
                                </p>
                            @else
                                <p class="text-danger">Termo não encontrado</p>
                            @endif
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">Descrição da Alteração</h6>
                            <p class="text-break">{{ $chamado->descricao_alteracao }}</p>
                        </div>

                    @else
                        <!-- Detalhes Genéricos -->
                        <div class="mb-3">
                            <h6 class="text-muted">Título</h6>
                            <p>{{ $chamado->titulo }}</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">Detalhes</h6>
                            <p class="text-break">{{ $chamado->detalhes }}</p>
                        </div>

                        @if($chamado->anexos && count($chamado->anexos) > 0)
                            <div class="mb-3">
                                <h6 class="text-muted">Anexos</h6>
                                <ul class="list-group">
                                    @foreach($chamado->anexos as $anexo)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-file me-2"></i>
                                                {{ basename($anexo) }}
                                            </span>
                                            <a href="{{ asset('storage/' . $anexo) }}" class="btn btn-sm btn-primary" target="_blank"
                                                download>
                                                <i class="fas fa-download"></i> Baixar
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endif

                    @if($chamado->observacoes_internas && Auth::user()->nivel !== 'empresa')
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-lock me-2"></i>Observações Internas (uso operador/admin)</h6>
                            <p class="mb-0 text-break">{{ $chamado->observacoes_internas }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informações do Chamado -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Informações</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Protocolo</small>
                        <p class="mb-0"><strong>{{ $chamado->protocolo }}</strong></p>
                    </div>

                    @if(Auth::user()->nivel !== 'empresa')
                        <div class="mb-2">
                            <small class="text-muted">Empresa</small>
                            <p class="mb-0">{{ $chamado->empresa->nome_empresa }}</p>
                        </div>
                    @endif

                    <div class="mb-2">
                        <small class="text-muted">Solicitante</small>
                        <p class="mb-0">{{ $chamado->solicitante->name }}</p>
                    </div>

                    <div class="mb-2">
                        <small class="text-muted">Aberto em</small>
                        <p class="mb-0">{{ $chamado->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($chamado->responsavel)
                        <div class="mb-2">
                            <small class="text-muted">Responsável</small>
                            <p class="mb-0">{{ $chamado->responsavel->name }}</p>
                        </div>
                    @endif

                    @if($chamado->data_conclusao)
                        <div class="mb-2">
                            <small class="text-muted">Concluído em</small>
                            <p class="mb-0">{{ $chamado->data_conclusao->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ações -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Ações</h6>
                </div>
                <div class="card-body">
                    <button onclick="window.NavigationHistory?.goBack('{{ route('chamados.index') }}')"
                        class="btn btn-secondary w-100 mb-2" title="Voltar para a página anterior com filtros preservados">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </button>

                    @if(Auth::user()->nivel === 'empresa' && !in_array($chamado->status, ['concluido', 'cancelado']))
                        <form action="{{ route('chamados.cancelar', $chamado->id_chamado) }}" method="POST"
                            onsubmit="return confirm('Tem certeza que deseja cancelar este chamado?');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times me-2"></i>Cancelar Chamado
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-comments me-2"></i>Conversa do Chamado</h6>
                    <span class="badge bg-secondary">{{ $chamado->mensagens->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="border rounded p-2 mb-3" style="max-height: 360px; overflow-y: auto; background: #f8fafc;">
                        @forelse($chamado->mensagens as $mensagem)
                            @php
                                $isEmpresa = $mensagem->remetente_nivel === 'empresa';
                            @endphp
                            <div class="mb-2 d-flex {{ $isEmpresa ? 'justify-content-start' : 'justify-content-end' }}">
                                <div class="p-2 rounded"
                                    style="max-width: 90%; background: {{ $isEmpresa ? '#e2e8f0' : '#dbeafe' }};">
                                    <div class="small text-muted mb-1">
                                        {{ $mensagem->remetente?->name ?? 'Usuário removido' }}
                                        ({{ $isEmpresa ? 'Unidade concedente' : 'Equipe SIGE' }})
                                    </div>
                                    <div class="small">{!! nl2br(e($mensagem->mensagem)) !!}</div>
                                    @if($mensagem->anexos && count($mensagem->anexos) > 0)
                                        <div class="small mt-2">
                                            <i class="fas fa-paperclip me-1"></i>
                                            @foreach($mensagem->anexos as $anexo)
                                                <a href="{{ asset('storage/' . $anexo) }}" target="_blank"
                                                    class="badge bg-light text-dark me-1" title="Baixar anexo">
                                                    <i class="fas fa-download me-1"></i>{{ basename($anexo) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="small text-muted mt-1">{{ $mensagem->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">Nenhuma mensagem ainda. Use o campo abaixo para iniciar a conversa.
                            </p>
                        @endforelse
                    </div>

                    @php
                        $empresaBloqueada = Auth::user()->nivel === 'empresa' && in_array($chamado->status, ['concluido', 'cancelado']);
                    @endphp

                    @if($empresaBloqueada)
                        <div class="alert alert-warning mb-0">
                            Este chamado foi finalizado. Se precisar de novo atendimento, abra um novo chamado.
                        </div>
                    @else
                        <form action="{{ route('chamados.enviar-mensagem', $chamado->id_chamado) }}" method="POST"
                            enctype="multipart/form-data" id="formEnviarMensagem">
                            @csrf
                            <div class="mb-2">
                                <textarea name="mensagem" rows="3" class="form-control" maxlength="2000"
                                    placeholder="Escreva sua mensagem para a equipe..."
                                    required>{{ old('mensagem') }}</textarea>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Anexar arquivos (opcional)</label>
                                <input type="file" name="anexos[]" class="form-control form-control-sm" multiple
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" max="5">
                                <small class="text-muted">Máx. 5 arquivos de 5MB cada</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100" id="btnEnviarMensagem">
                                <span class="spinner-border spinner-border-sm me-1 d-none" id="loadingMensagem"></span>
                                <i class="fas fa-paper-plane me-1" id="iconEnviar"></i>
                                <span id="textoEnviar">Enviar mensagem</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formEnviarMensagem');
            if (form) {
                form.addEventListener('submit', function () {
                    const btn = document.getElementById('btnEnviarMensagem');
                    const loading = document.getElementById('loadingMensagem');
                    const icon = document.getElementById('iconEnviar');
                    const texto = document.getElementById('textoEnviar');

                    btn.disabled = true;
                    loading.classList.remove('d-none');
                    icon.classList.add('d-none');
                    texto.textContent = 'Enviando...';
                });
            }
        });
    </script>
@endsection