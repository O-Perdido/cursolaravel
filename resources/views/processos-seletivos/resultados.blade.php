@extends('layouts.main')

@section('title', 'Resultados do Processo Seletivo')

@section('content')
    <div class="container-fluid py-3">
        <div class="card shadow-sm mb-3">
            <div class="card-body pb-3 pt-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h4 class="mb-1">{{ $processo->titulo }}</h4>
                        <div class="text-muted small">Processo {{ $processo->numero_processo }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#novoResultadoModal">
                            <i class="fas fa-plus me-1"></i> Publicar Resultado
                        </button>
                        <a href="{{ route('processos-seletivos.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($resultados->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Resultados Publicados</h6>
                    <span class="text-muted small">{{ $resultados->count() }} registro(s)</span>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($resultados as $resultado)
                            <div
                                class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                <div>
                                    <h6 class="mb-1">{{ $resultado->numero_resultado }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i> Publicado em
                                        {{ $resultado->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <div class="d-flex gap-2">
                                    @if($resultado->arquivo_resultado)
                                        <a href="{{ Storage::url($resultado->arquivo_resultado) }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-download me-1"></i> Download
                                        </a>
                                    @else
                                        <span class="text-muted small">Sem arquivo</span>
                                    @endif
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-delete-resultado" title="Remover Resultado"
                                        data-action="{{ route('processos-seletivos.resultados.destroy', $resultado->id_resultado) }}"
                                        data-nome="{{ $resultado->numero_resultado }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="fas fa-info-circle me-2"></i> Nenhum resultado publicado ainda.
            </div>
        @endif
    </div>

    <div class="modal fade" id="novoResultadoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Publicar Resultado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('processos-seletivos.publicar-resultado', $processo->id_processo) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="numero_resultado" class="form-label">Identificação do Resultado <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('numero_resultado') is-invalid @enderror"
                                id="numero_resultado" name="numero_resultado"
                                placeholder="Ex: Resultado Final, Lista de Aprovados, etc" required>
                            @error('numero_resultado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="arquivo_resultado" class="form-label">Arquivo do Resultado (PDF/Excel)</label>
                            <input type="file" class="form-control @error('arquivo_resultado') is-invalid @enderror"
                                id="arquivo_resultado" name="arquivo_resultado" accept=".pdf,.xls,.xlsx">
                            <small class="form-text text-muted">Tamanho máximo: 10MB</small>
                            @error('arquivo_resultado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Publicar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Deletar resultado com confirmação
        const deleteButtons = document.querySelectorAll('.btn-delete-resultado');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.getAttribute('data-action');
                const nome = this.getAttribute('data-nome');
                
                Swal.fire({
                    title: 'Confirmar exclusão?',
                    html: `Deseja realmente remover o resultado:<br><strong>${nome}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, remover!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = action;
                        form.style.display = 'none';
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        
                        form.appendChild(csrfInput);
                        form.appendChild(methodInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection