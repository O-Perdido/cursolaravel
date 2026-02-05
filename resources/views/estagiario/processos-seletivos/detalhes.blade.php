@extends('layouts.main')

@section('title', 'Detalhes do Processo Seletivo')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('processos-seletivos.abertos') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
        <div class="d-flex align-items-center gap-2 text-muted small">
            <i class="fas fa-building"></i>
            <span>{{ $processo->empresa->nome_empresa }}</span>
        </div>
    </div>

    @php
        $inicioInscricoes = $processo->data_inicio_inscricoes ?? $processo->data_abertura;
        $fimInscricoes = $processo->data_fechamento_inscricoes;
        $inscricaoAbertaAgora = $processo->periodoInscricoesAberto();
        $inscricaoEmBreve = $processo->inscricoesEmBreve();
        $inscricaoEncerrada = $processo->inscricoesEncerradas();
        $processoFinalizado = $processo->status === 'finalizado';
    @endphp

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-3 overflow-hidden">
                <div class="position-relative bg-light d-flex align-items-center justify-content-between gap-3 p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 160px;">
                    <div class="flex-grow-1">
                        <h3 class="mb-2 text-white">{{ $processo->titulo }}</h3>
                        <p class="text-white-50 mb-1 small">Processo {{ $processo->numero_processo }} • {{ $processo->empresa->nome_empresa }}</p>
                        @php
                            $statusDinamico = $processo->getStatusDinamico();
                        @endphp
                        <span class="badge @switch($statusDinamico) @case('aberto') bg-success @break @case('inscricoes') bg-info @break @case('encerrado') bg-warning @break @case('finalizado') bg-dark @break @default bg-secondary @endswitch">{{ ucfirst($statusDinamico) }}</span>
                    </div>
                    @if($processo->icone_processo)
                        <img src="{{ asset('storage/' . $processo->icone_processo) }}" alt="{{ $processo->titulo }}" class="rounded" style="width: 120px; height: 120px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                    @elseif($processo->empresa->logo_empresa)
                        <img src="{{ Storage::url($processo->empresa->logo_empresa) }}" alt="{{ $processo->empresa->nome_empresa }}" class="rounded" style="width: 120px; height: 120px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                    @else
                        <div class="rounded bg-white d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                            <i class="fas fa-briefcase text-muted" style="font-size: 48px;"></i>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($processo->data_abertura || $inicioInscricoes || $fimInscricoes)
                        <div class="row g-2 mb-3">
                            @if($processo->data_abertura)
                                <div class="col-md-4">
                                    <div class="p-2 border rounded text-center h-100">
                                        <div class="text-muted small"><i class="fas fa-bullhorn me-1"></i>Publicação</div>
                                        <div class="fw-semibold">{{ $processo->data_abertura->format('d/m H:i') }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($inicioInscricoes)
                                <div class="col-md-4">
                                    <div class="p-2 border rounded text-center h-100">
                                        <div class="text-muted small"><i class="fas fa-play me-1"></i>Início das Inscrições</div>
                                        <div class="fw-semibold">{{ $inicioInscricoes->format('d/m H:i') }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($fimInscricoes)
                                <div class="col-md-4">
                                    <div class="p-2 border rounded text-center h-100">
                                        <div class="text-muted small"><i class="fas fa-stop me-1"></i>Fim das Inscrições</div>
                                        <div class="fw-semibold">{{ $fimInscricoes->format('d/m H:i') }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @php
                $fases = $processo->fases ?? [];
                $vagasNiveis = $processo->vagas_por_nivel ?? [];
            @endphp

            @if($fases && count($fases) > 0)
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light"><h6 class="mb-0"><i class="fas fa-list me-2"></i>Fases do Processo</h6></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th>Descrição</th><th style="width: 35%">Período/Data</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($fases as $fase)
                                        <tr>
                                            <td>{{ $fase['descricao'] ?? '-' }}</td>
                                            <td>{{ $fase['periodo'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif($processo->descricao_fases)
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light"><h6 class="mb-0"><i class="fas fa-list me-2"></i>Fases do Processo</h6></div>
                    <div class="card-body"><div style="white-space: pre-wrap; line-height: 1.6;">{{ $processo->descricao_fases }}</div></div>
                </div>
            @endif

            @if($vagasNiveis && count($vagasNiveis) > 0)
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light"><h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Vagas por Nível</h6></div>
                    <div class="card-body">
                        @foreach($vagasNiveis as $nivel)
                            <div class="mb-3">
                                <h6 class="mb-2">{{ $nivel['nivel'] ?? 'Nível' }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light"><tr><th>Curso</th><th style="width: 25%">Vagas</th></tr></thead>
                                        <tbody>
                                            @foreach(($nivel['itens'] ?? []) as $curso)
                                                <tr>
                                                    <td>{{ $curso['curso'] ?? '-' }}</td>
                                                    <td>{{ $curso['vagas'] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($processo->cursos_destino && count($processo->cursos_destino) > 0)
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light"><h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Cursos Destinados</h6></div>
                    <div class="card-body">
                        <div class="row row-cols-1 row-cols-sm-2 g-2">
                            @foreach($processo->cursos_destino as $curso)
                                <div class="col">
                                    <div class="border rounded p-2 h-100 small">{{ $curso }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($processo->requisitos)
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light"><h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Requisitos</h6></div>
                    <div class="card-body"><div style="white-space: pre-wrap; line-height: 1.6;">{{ $processo->requisitos }}</div></div>
                </div>
            @endif

            @if($processo->observacoes)
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light"><h6 class="mb-0"><i class="fas fa-note me-2"></i>Observações</h6></div>
                    <div class="card-body"><div style="white-space: pre-wrap; line-height: 1.6;">{{ $processo->observacoes }}</div></div>
                </div>
            @endif

            @if($processo->arquivos->count() > 0)
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light"><h6 class="mb-0"><i class="fas fa-file me-2"></i>Documentos do Edital</h6></div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($processo->arquivos as $arquivo)
                                <a href="{{ route('processos-seletivos.arquivos.download', $arquivo->id_arquivo) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $arquivo->nome_exibicao }}</h6>
                                        <small class="text-muted"><i class="fas fa-tag me-1"></i>{{ ucfirst($arquivo->tipo_arquivo) }}</small>
                                    </div>
                                    <i class="fas fa-download text-primary"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @php
                $resultados = $processo->resultados()->orderByDesc('created_at')->get();
            @endphp
            @if($resultados->count() > 0)
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-trophy me-2"></i>Resultados Publicados</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($resultados as $resultado)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-1">{{ $resultado->numero_resultado }}</h6>
                                        <small class="text-muted">
                                            {{ $resultado->created_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                    @if($resultado->arquivo_resultado)
                                        <a href="{{ Storage::url($resultado->arquivo_resultado) }}" target="_blank"
                                            class="btn btn-success btn-sm w-100">
                                            <i class="fas fa-download me-1"></i> Baixar Resultado
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-12 col-lg-4">
            <div class="card shadow-sm h-100" id="inscricaoSidebar">
                <div class="card-body d-flex flex-column gap-2">
                    @if($inscricaoAbertaAgora)
                        @if(!$jaInscrito)
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#inscricaoModal">
                                <i class="fas fa-pen-to-square me-1"></i> Se Inscrever
                            </button>
                            <p class="text-muted text-center small mb-0">Confirme para registrar sua participação.</p>
                        @else
                            <div class="alert alert-success mb-0" role="alert">
                                <i class="fas fa-check-circle me-1"></i> Você já está inscrito. Acompanhe em "Minhas Inscrições".
                            </div>
                        @endif
                    @elseif($inscricaoEmBreve)
                        <div class="alert alert-warning mb-0" role="alert">
                            <i class="fas fa-clock me-1"></i> Inscrições em breve @if($inicioInscricoes) (a partir de {{ $inicioInscricoes->format('d/m H:i') }}) @endif.
                        </div>
                    @elseif($processoFinalizado)
                        <div class="alert alert-secondary mb-0" role="alert">
                            <i class="fas fa-flag-checkered me-1"></i> Processo concluído.
                        </div>
                    @elseif($inscricaoEncerrada)
                        <div class="alert alert-warning mb-0" role="alert">
                            <i class="fas fa-hourglass-end me-1"></i> Inscrições encerradas.
                        </div>
                    @elseif($processo->status === 'inscricoes')
                        <div class="alert alert-info mb-0" role="alert">
                            <i class="fas fa-sync-alt me-1"></i> Inscrições abertas, tente novamente em instantes.
                        </div>
                    @else
                        <div class="alert alert-danger mb-0" role="alert">
                            <i class="fas fa-times-circle me-1"></i> Inscrições indisponíveis para este processo.
                        </div>
                    @endif

                    <div class="border-top pt-2">
                        <a href="{{ route('processos-seletivos.minhas-inscricoes') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-list me-1"></i> Minhas Inscrições
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="inscricaoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Inscrição</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($processo->aviso_inscricao)
                    <div class="alert alert-info mb-3">
                        <div style="white-space: pre-wrap; line-height: 1.6;">{{ $processo->aviso_inscricao }}</div>
                    </div>
                @else
                    <p>Você está prestes a se inscrever no processo seletivo <strong>{{ $processo->titulo }}</strong>.</p>
                    <p class="text-muted">Após confirmar, sua inscrição será registrada no sistema.</p>
                @endif

                @if($processo->solicitar_upload_inscricao)
                    <div class="mb-3">
                        <label for="arquivo_inscricao" class="form-label">Anexe o arquivo para confirmar</label>
                        <input type="file" class="form-control" id="arquivo_inscricao" name="arquivo_inscricao"
                            accept=".pdf,image/*" required>
                        <small class="text-muted">Envie um PDF ou imagem conforme orientações do processo.</small>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmarInscricao">
                    <i class="fas fa-check"></i> Confirmar Inscrição
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmarBtn = document.getElementById('confirmarInscricao');
        if (confirmarBtn) {
            confirmarBtn.addEventListener('click', function() {
                const processoId = {{ $processo->id_processo }};
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                const fileInput = document.getElementById('arquivo_inscricao');

                const urlInscricao = "{{ route('processos-seletivos.inscrever', $processo->id_processo) }}";

                if (fileInput && fileInput.required && (!fileInput.files || fileInput.files.length === 0)) {
                    showToast('Atenção', 'Envie o arquivo solicitado para concluir a inscrição.', 'warning');
                    return;
                }

                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';

                const formData = new FormData();
                formData.append('_token', token ?? '');
                if (fileInput && fileInput.files.length > 0) {
                    formData.append('arquivo_inscricao', fileInput.files[0]);
                }

                fetch(urlInscricao, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok || data.success === false) {
                        const message = data.error || (data.message ?? 'Não foi possível concluir a inscrição.');
                        throw new Error(message);
                    }
                    return data;
                })
                .then(data => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('inscricaoModal'));
                    modal.hide();
                    
                    let message = data.message;
                    if (data.numero_inscricao) {
                        message += `\n\nNúmero de inscrição: ${data.numero_inscricao}`;
                    }
                    
                    showToast('Sucesso!', message, 'success');
                    setTimeout(() => location.reload(), 1500);
                })
                .catch((error) => {
                    showToast('Erro!', error.message || 'Ocorreu um erro ao processar sua inscrição', 'danger');
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-check"></i> Confirmar Inscrição';
                });
            });
        }
    });

    function showToast(title, message, type) {
        const toastHtml = `
            <div class="toast" role="alert" aria-live="assertive">
                <div class="toast-header bg-${type} text-white">
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>`;

        const toastContainer = document.createElement('div');
        toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
        toastContainer.innerHTML = toastHtml;
        document.body.appendChild(toastContainer);

        const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
        toast.show();
        setTimeout(() => toastContainer.remove(), 5000);
    }
</script>

<style>
    @media (min-width: 992px) {
        #inscricaoSidebar { position: sticky; top: 20px; }
    }
</style>
@endsection
