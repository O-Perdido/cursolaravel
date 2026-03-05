@extends('layouts.main')

@section('title', 'Detalhes do Chamado - ' . $chamado->protocolo)

@section('content')
    <style>
        .detalhes-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .detalhes-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: white;
            border-left: 4px solid #007bff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .info-card.status-pendente {
            border-left-color: #ff6b6b;
        }

        .info-card.status-em_analise {
            border-left-color: #ffd93d;
        }

        .info-card.status-em_andamento {
            border-left-color: #4dabf7;
        }

        .info-card.status-concluido {
            border-left-color: #51cf66;
        }

        .info-card.status-cancelado {
            border-left-color: #868e96;
        }

        .info-card h6 {
            color: #999;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .info-card p {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            color: #333;
        }

        .section-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .section-card h3 {
            color: #333;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-card h3 i {
            color: #007bff;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-item label {
            color: #999;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .detail-item strong {
            color: #333;
            font-size: 1rem;
        }

        .badge-status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .badge-status.pendente {
            background-color: #ffe0e0;
            color: #c92a2a;
        }

        .badge-status.em_analise {
            background-color: #fff3c0;
            color: #e67700;
        }

        .badge-status.em_andamento {
            background-color: #d0ebff;
            color: #0066cc;
        }

        .badge-status.concluido {
            background-color: #d3f9d8;
            color: #2b8a3e;
        }

        .badge-status.cancelado {
            background-color: #e9ecef;
            color: #495057;
        }

        .tipo-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 600;
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

        .conteudo-chamado {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #007bff;
            line-height: 1.6;
        }

        .observacao-interna {
            background: #fff3cd;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #ffc107;
            margin: 15px 0;
        }

        .observacao-interna strong {
            color: #856404;
        }

        .anexos-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .anexo-item {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .anexo-item:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
        }

        .anexo-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .anexo-icon.pdf {
            color: #dc3545;
        }

        .anexo-icon.image {
            color: #28a745;
        }

        .anexo-icon.doc {
            color: #0056b3;
        }

        .anexo-name {
            font-size: 0.85rem;
            color: #333;
            margin-bottom: 10px;
            word-break: break-word;
            font-weight: 500;
        }

        .anexo-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-anexo {
            padding: 5px 10px;
            font-size: 0.8rem;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }

        .btn-anexo:hover {
            transform: translateY(-2px);
        }

        .btn-visualizar {
            background-color: #28a745;
            color: white;
            border: none;
        }

        .btn-visualizar:hover {
            background-color: #218838;
        }

        .btn-download {
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border: none;
        }

        .btn-download:hover {
            background-color: #0056b3;
        }

        .actions-footer {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .timeline-marker {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-weight: 700;
        }

        .timeline-content {
            flex-grow: 1;
        }

        .timeline-content .label {
            color: #999;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .timeline-content .value {
            color: #333;
            font-weight: 600;
            margin-top: 4px;
        }

        .back-button {
            margin-bottom: 20px;
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

    <!-- Botão Voltar -->
    <div class="back-button">
        <a href="{{ route('chamados.painel') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Painel
        </a>
    </div>

    <!-- Header -->
    <div class="detalhes-header">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h1>#{{ $chamado->protocolo }}</h1>
                <p style="margin: 10px 0 0 0; opacity: 0.9;">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Aberto em {{ $chamado->created_at->format('d/m/Y \à\s H:i') }}
                </p>
            </div>
            <div style="text-align: right;">
                <span class="badge-status {{ $chamado->status }}">
                    {{ ucfirst(str_replace('_', ' ', $chamado->status)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="info-grid">
        <div class="info-card status-{{ $chamado->status }}">
            <h6><i class="fas fa-check-square me-2"></i>Tipo</h6>
            <span class="tipo-badge tipo-{{ $chamado->tipoChamado->slug }}">
                {{ $chamado->tipoChamado->nome }}
            </span>
        </div>

        <div class="info-card">
            <h6><i class="fas fa-building me-2"></i>Unidade Concedente</h6>
            <p>{{ $chamado->empresa->nome_empresa }}</p>
        </div>

        <div class="info-card">
            <h6><i class="fas fa-user me-2"></i>Solicitante</h6>
            <p>{{ $chamado->solicitante->name }}</p>
        </div>

        @if($chamado->responsavel)
            <div class="info-card">
                <h6><i class="fas fa-user-tie me-2"></i>Responsável</h6>
                <p>{{ $chamado->responsavel->name }}</p>
            </div>
        @else
            <div class="info-card">
                <h6><i class="fas fa-user-tie me-2"></i>Responsável</h6>
                <p style="color: #999;">Não atribuído</p>
            </div>
        @endif
    </div>

    <!-- Informações do Termo (se aplicável) -->
    @if($chamado->termo)
        <div class="section-card">
            <h3><i class="fas fa-file-contract"></i> Termo de Estágio</h3>
            <div class="detail-row">
                <div class="detail-item">
                    <label>Número do Termo</label>
                    <strong>{{ $chamado->termo->numero_termo }}/{{ $chamado->termo->ano_termo }}</strong>
                </div>
                <div class="detail-item">
                    <label>Estagiário</label>
                    <strong>{{ $chamado->termo->estagiario->nome_estagiario }}</strong>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-item">
                    <label>CPF do Estagiário</label>
                    <strong>{{ $chamado->termo->estagiario->numero_cpf }}</strong>
                </div>
                <div class="detail-item">
                    <label>Data de Início</label>
                    <strong>{{ $chamado->termo->data_inicio_estagio->format('d/m/Y') }}</strong>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-item">
                    <label>Data de Término</label>
                    <strong>{{ $chamado->termo->data_fim_estagio->format('d/m/Y') }}</strong>
                </div>
                <div class="detail-item">
                    <label>Saldo de Recesso</label>
                    <strong>{{ $chamado->termo->saldo_recesso }} dias</strong>
                </div>
            </div>
        </div>
    @endif

    <!-- Detalhes Específicos do Chamado -->
    <div class="section-card">
        <h3><i class="fas fa-clipboard"></i> Detalhes do Chamado</h3>

        @if($chamado->isRescisao())
            <div class="detail-row">
                <div class="detail-item">
                    <label>Data da Rescisão</label>
                    <strong>{{ $chamado->data_rescisao->format('d/m/Y') }}</strong>
                </div>
            </div>
            <div class="detail-item">
                <label>Motivo da Rescisão</label>
                <div class="conteudo-chamado">
                    {{ $chamado->motivo_rescisao }}
                </div>
            </div>

        @elseif($chamado->isAlteracao())
            <div class="detail-item">
                <label>Descrição da Alteração</label>
                <div class="conteudo-chamado">
                    {{ $chamado->descricao_alteracao }}
                </div>
            </div>

        @else
            <div class="detail-row">
                <div class="detail-item">
                    <label>Título</label>
                    <strong>{{ $chamado->titulo }}</strong>
                </div>
            </div>
            <div class="detail-item">
                <label>Detalhes</label>
                <div class="conteudo-chamado">
                    {{ $chamado->detalhes }}
                </div>
            </div>
        @endif
    </div>

    <!-- Anexos (com visualização de imagens) -->
    @if($chamado->anexos && count($chamado->anexos) > 0)
        <div class="section-card">
            <h3><i class="fas fa-paperclip"></i> Anexos</h3>
            <div class="anexos-container">
                @foreach($chamado->anexos as $anexo)
                    @php
                        // Normaliza caminho e URL pública de forma robusta
                        $raw = is_string($anexo) ? $anexo : '';
                        $raw = str_replace('\\', '/', $raw); // barras invertidas -> barras
                        if (\Illuminate\Support\Str::startsWith($raw, 'storage/')) {
                            $raw = substr($raw, 8); // remove prefixo storage/
                        }
                        $basename = basename($raw);
                        if (!\Illuminate\Support\Str::startsWith($raw, 'chamados/anexos/')) {
                            $raw = 'chamados/anexos/' . $basename;
                        }
                        $pathNormalizado = $raw;
                        $urlPublica = Storage::url($pathNormalizado);

                        $nomeArquivo = $basename;
                        $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
                        $isImagem = in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

                        $icon = match ($extensao) {
                            'pdf' => 'fas fa-file-pdf',
                            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'fas fa-image',
                            'doc', 'docx' => 'fas fa-file-word',
                            default => 'fas fa-file',
                        };
                        $iconType = match ($extensao) {
                            'pdf' => 'pdf',
                            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'image',
                            'doc', 'docx' => 'doc',
                            default => 'file',
                        };
                    @endphp

                    <div class="anexo-item">
                        <div class="anexo-icon {{ $iconType }}">
                            <i class="{{ $icon }}"></i>
                        </div>
                        <div class="anexo-name" title="{{ $nomeArquivo }}">
                            {{ Str::limit($nomeArquivo, 20, '...') }}
                        </div>
                        <div class="anexo-actions">
                            @if($isImagem)
                                <button type="button" class="btn-anexo btn-visualizar" data-bs-toggle="modal"
                                    data-bs-target="#modalImagem{{ $loop->index }}" title="Visualizar imagem">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                            @endif
                            <a href="{{ route('chamados.anexo.download', [$chamado->id_chamado, $loop->index]) }}"
                                class="btn-anexo btn-download" title="Baixar arquivo">
                                <i class="fas fa-download"></i> Baixar
                            </a>
                        </div>
                    </div>

                    <!-- Modal para visualizar imagens -->
                    @if($isImagem)
                        <div class="modal fade" id="modalImagem{{ $loop->index }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ $nomeArquivo }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img src="{{ route('chamados.anexo.ver', [$chamado->id_chamado, $loop->index]) }}"
                                            class="img-fluid rounded" alt="{{ $nomeArquivo }}" style="max-height: 600px;">
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ route('chamados.anexo.download', [$chamado->id_chamado, $loop->index]) }}"
                                            class="btn btn-primary">
                                            <i class="fas fa-download me-2"></i>Baixar
                                        </a>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Observação Interna -->
    <div class="section-card">
        <h3><i class="fas fa-sticky-note"></i> Observação Interna</h3>
        @if($chamado->observacoes_internas)
            <div class="observacao-interna">
                {{ $chamado->observacoes_internas }}
            </div>
        @else
            <p style="color: #999; margin: 0;">
                <em>Nenhuma observação adicionada ainda</em>
            </p>
        @endif
    </div>

    <!-- Timeline de Atividades -->
    <div class="section-card">
        <h3><i class="fas fa-history"></i> Histórico</h3>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-marker">1</div>
                <div class="timeline-content">
                    <div class="label">Criado</div>
                    <div class="value">
                        {{ $chamado->created_at->format('d/m/Y \à\s H:i') }}
                        <span style="color: #999; margin-left: 10px;">por {{ $chamado->solicitante->name }}</span>
                    </div>
                </div>
            </div>

            @if($chamado->data_conclusao)
                <div class="timeline-item">
                    <div class="timeline-marker">✓</div>
                    <div class="timeline-content">
                        <div class="label">Concluído/Cancelado</div>
                        <div class="value">{{ $chamado->data_conclusao->format('d/m/Y \à\s H:i') }}</div>
                    </div>
                </div>
            @endif

            @if($chamado->updated_at != $chamado->created_at)
                <div class="timeline-item">
                    <div class="timeline-marker">↻</div>
                    <div class="timeline-content">
                        <div class="label">Última Atualização</div>
                        <div class="value">{{ $chamado->updated_at->format('d/m/Y \à\s H:i') }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="section-card">
        <h3><i class="fas fa-comments"></i> Chat do Chamado</h3>

        <div
            style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; max-height: 380px; overflow-y: auto; padding: 12px; margin-bottom: 16px;">
            @forelse($chamado->mensagens as $mensagem)
                @php
                    $isEmpresa = $mensagem->remetente_nivel === 'empresa';
                @endphp
                <div
                    style="display: flex; {{ $isEmpresa ? 'justify-content: flex-start;' : 'justify-content: flex-end;' }} margin-bottom: 10px;">
                    <div
                        style="max-width: 85%; background: {{ $isEmpresa ? '#e2e8f0' : '#dbeafe' }}; border-radius: 8px; padding: 10px 12px;">
                        <div style="font-size: 0.8rem; color: #666; margin-bottom: 6px;">
                            {{ $mensagem->remetente?->name ?? 'Usuário removido' }}
                            ({{ $isEmpresa ? 'Unidade concedente' : 'Equipe SIGE' }})
                        </div>
                        <div style="white-space: pre-wrap; word-break: break-word;">{{ $mensagem->mensagem }}</div>
                        @if($mensagem->anexos && count($mensagem->anexos) > 0)
                            <div style="margin-top: 8px; font-size: 0.85rem;">
                                <i class="fas fa-paperclip" style="margin-right: 4px;"></i>
                                @foreach($mensagem->anexos as $anexo)
                                    <a href="{{ asset('storage/' . $anexo) }}" 
                                        target="_blank" 
                                        style="display: inline-block; background: #fff; color: #333; text-decoration: none; padding: 2px 8px; border-radius: 4px; margin-right: 4px; border: 1px solid #ddd;" 
                                        title="Baixar anexo">
                                        <i class="fas fa-download" style="font-size: 0.75rem; margin-right: 3px;"></i>{{ basename($anexo) }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        <div style="font-size: 0.78rem; color: #888; margin-top: 6px;">
                            {{ $mensagem->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            @empty
                <p style="margin: 0; color: #666;">Nenhuma mensagem registrada ainda.</p>
            @endforelse
        </div>

        <form action="{{ route('chamados.enviar-mensagem', $chamado->id_chamado) }}" method="POST" 
            enctype="multipart/form-data" id="formEnviarRespostaAdmin">
            @csrf
            <div class="mb-3">
                <label class="form-label">Responder chamado</label>
                <textarea name="mensagem" rows="4" class="form-control" maxlength="2000"
                    placeholder="Digite uma resposta para a unidade concedente..." required>{{ old('mensagem') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Anexar arquivos (opcional)</label>
                <input type="file" name="anexos[]" class="form-control" 
                    multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" max="5">
                <small class="text-muted">Máx. 5 arquivos de 5MB cada</small>
            </div>
            <button type="submit" class="btn btn-primary" id="btnEnviarRespostaAdmin">
                <span class="spinner-border spinner-border-sm me-2 d-none" id="loadingRespostaAdmin"></span>
                <i class="fas fa-paper-plane me-2" id="iconEnviarAdmin"></i>
                <span id="textoEnviarAdmin">Enviar resposta</span>
            </button>
        </form>
    </div>

    <!-- Ações -->
    <div class="actions-footer">
        <a href="{{ route('chamados.painel') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Painel
        </a>
        <a href="javascript:window.print()" class="btn btn-outline-info">
            <i class="fas fa-print me-2"></i>Imprimir
        </a>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalExcluirChamado">
            <i class="fas fa-trash me-2"></i>Excluir Chamado
        </button>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="modalExcluirChamado" tabindex="-1" aria-labelledby="modalExcluirChamadoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title d-flex align-items-center" id="modalExcluirChamadoLabel">
                        <i class="fas fa-exclamation-triangle me-2 fa-lg"></i>
                        Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="text-center mb-4">
                        <div class="warning-icon-circle mb-3">
                            <i class="fas fa-trash-alt fa-3x text-danger"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-3">Tem certeza que deseja excluir este chamado?</h5>
                        <p class="text-muted mb-0">Esta ação é <strong class="text-danger">irreversível</strong> e não pode ser desfeita.</p>
                    </div>
                    
                    <div class="alert alert-warning border-0 bg-warning bg-opacity-10">
                        <h6 class="alert-heading mb-2">
                            <i class="fas fa-info-circle me-2"></i>O que será excluído:
                        </h6>
                        <ul class="mb-0 ps-3">
                            <li>O chamado <strong>{{ $chamado->protocolo }}</strong></li>
                            <li>Todo o histórico de mensagens</li>
                            <li>Todos os arquivos anexados</li>
                            <li>Informações e dados relacionados</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <form action="{{ route('chamados.destroy', $chamado->id_chamado) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-2"></i>Sim, Excluir Permanentemente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formEnviarRespostaAdmin');
        if (form) {
            form.addEventListener('submit', function() {
                const btn = document.getElementById('btnEnviarRespostaAdmin');
                const loading = document.getElementById('loadingRespostaAdmin');
                const icon = document.getElementById('iconEnviarAdmin');
                const texto = document.getElementById('textoEnviarAdmin');
                
                btn.disabled = true;
                loading.classList.remove('d-none');
                icon.classList.add('d-none');
                texto.textContent = 'Enviando...';
            });
        }
    });
</script>
@endsection

@section('styles')
<style>
    .warning-icon-circle {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
    }
    
    #modalExcluirChamado .modal-content {
        border-radius: 12px;
        overflow: hidden;
    }
    
    #modalExcluirChamado .modal-header {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        padding: 1.25rem 1.5rem;
    }
    
    #modalExcluirChamado .modal-body {
        padding: 2rem 1.5rem;
    }
    
    #modalExcluirChamado .modal-footer {
        padding: 1rem 1.5rem;
    }
    
    #modalExcluirChamado .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    #modalExcluirChamado .btn-danger:hover {
        background-color: #991b1b;
        border-color: #991b1b;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    
    #modalExcluirChamado .btn-secondary:hover {
        background-color: #4b5563;
        border-color: #4b5563;
    }
</style>
@endsection