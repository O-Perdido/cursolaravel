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

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between gap-3">
                    <div class="d-flex gap-3 align-items-start">
                        @if($processo->empresa->logo_empresa)
                            <img src="{{ Storage::url($processo->empresa->logo_empresa) }}" alt="{{ $processo->empresa->nome_empresa }}" class="rounded" style="width: 56px; height: 56px; object-fit: cover;">
                        @else
                            <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="fas fa-building text-muted"></i>
                            </div>
                        @endif
                        <div>
                            <h4 class="mb-1">{{ $processo->titulo }}</h4>
                            <p class="text-muted small mb-1">Processo {{ $processo->numero_processo }}</p>
                            <span class="badge @switch($processo->status) @case('aberto') bg-success @break @case('inscricoes') bg-info @break @case('encerrado') bg-warning @break @case('finalizado') bg-dark @break @default bg-secondary @endswitch">{{ ucfirst($processo->status) }}</span>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-md-row gap-2">
                        @if($processo->data_abertura)
                            <div class="p-2 border rounded text-center flex-fill">
                                <div class="text-muted small">Abertura</div>
                                <div class="fw-semibold">{{ $processo->data_abertura->format('d/m H:i') }}</div>
                            </div>
                        @endif
                        @if($processo->data_fechamento_inscricoes)
                            <div class="p-2 border rounded text-center flex-fill">
                                <div class="text-muted small">Fecha inscrições</div>
                                <div class="fw-semibold">{{ $processo->data_fechamento_inscricoes->format('d/m H:i') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($processo->descricao_fases)
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light"><h6 class="mb-0"><i class="fas fa-list me-2"></i>Fases do Processo</h6></div>
                    <div class="card-body"><div style="white-space: pre-wrap; line-height: 1.6;">{{ $processo->descricao_fases }}</div></div>
                </div>
            @endif

            @if($processo->cursos_destino && count($processo->cursos_destino) > 0)
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
                <div class="card shadow-sm">
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
        </div>

        <div class="col-12 col-lg-4">
            <div class="card shadow-sm h-100" id="inscricaoSidebar">
                <div class="card-body d-flex flex-column gap-2">
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

                const urlInscricao = "{{ route('processos-seletivos.inscrever', $processo->id_processo) }}";

                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';

                fetch(urlInscricao, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('inscricaoModal'));
                        modal.hide();
                        showToast('Sucesso!', data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('Erro!', data.error, 'danger');
                    }
                })
                .catch(() => {
                    showToast('Erro!', 'Ocorreu um erro ao processar sua inscrição', 'danger');
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
