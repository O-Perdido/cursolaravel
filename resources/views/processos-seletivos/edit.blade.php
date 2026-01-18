@extends('layouts.main')

@section('title', 'Editar Processo Seletivo')

@section('content')
    <div class="container-fluid py-3">
        <div class="card shadow-sm mb-3">
            <div class="card-body pb-2 pt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="mb-0">
                    <i class="fas fa-pen me-2 text-primary"></i>Editar Processo Seletivo
                </h4>
                <a href="{{ route('processos-seletivos.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>Erros na Validação</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($processo->arquivos->count() > 0)
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0"><i class="fas fa-file me-2 text-primary"></i>Arquivos Atuais</h6>
                    <span class="text-muted small">Exclusão não remove o processo</span>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($processo->arquivos as $arquivo)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $arquivo->nome_exibicao }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i>{{ ucfirst($arquivo->tipo_arquivo) }} •
                                        <i class="fas fa-calendar me-1"></i>{{ $arquivo->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('processos-seletivos.arquivos.download', $arquivo->id_arquivo) }}"
                                        target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                    <form action="{{ route('processos-seletivos.arquivos.destroy', $arquivo->id_arquivo) }}"
                                        method="POST" onsubmit="return confirm('Remover este arquivo?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('processos-seletivos.update', $processo->id_processo) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informações Básicas</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Número do Processo</label>
                        <p class="h6 mb-0">{{ $processo->numero_processo }}</p>
                    </div>

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título do Processo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo"
                            name="titulo" value="{{ old('titulo', $processo->titulo) }}" required>
                        @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <p class="text-muted mb-3"><strong>Empresa:</strong> {{ $processo->empresa->nome_empresa }}</p>

                    <div class="mb-0">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                            required>
                            <option value="rascunho" {{ old('status', $processo->status) === 'rascunho' ? 'selected' : '' }}>
                                Rascunho</option>
                            <option value="aberto" {{ old('status', $processo->status) === 'aberto' ? 'selected' : '' }}>
                                Aberto</option>
                            <option value="inscricoes" {{ old('status', $processo->status) === 'inscricoes' ? 'selected' : '' }}>Inscrições</option>
                            <option value="encerrado" {{ old('status', $processo->status) === 'encerrado' ? 'selected' : '' }}>Encerrado</option>
                            <option value="finalizado" {{ old('status', $processo->status) === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-calendar me-2 text-primary"></i>Datas Importantes</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_abertura" class="form-label">Data de Abertura</label>
                            <input type="datetime-local" class="form-control @error('data_abertura') is-invalid @enderror"
                                id="data_abertura" name="data_abertura"
                                value="{{ old('data_abertura', $processo->data_abertura?->format('Y-m-d\TH:i')) }}">
                            @error('data_abertura')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="data_fechamento_inscricoes" class="form-label">Data de Fechamento de
                                Inscrições</label>
                            <input type="datetime-local"
                                class="form-control @error('data_fechamento_inscricoes') is-invalid @enderror"
                                id="data_fechamento_inscricoes" name="data_fechamento_inscricoes"
                                value="{{ old('data_fechamento_inscricoes', $processo->data_fechamento_inscricoes?->format('Y-m-d\TH:i')) }}">
                            @error('data_fechamento_inscricoes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-align-left me-2 text-primary"></i>Descrição e Informações</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="descricao_fases" class="form-label">Fases do Processo</label>
                        <textarea class="form-control @error('descricao_fases') is-invalid @enderror" id="descricao_fases"
                            name="descricao_fases"
                            rows="4">{{ old('descricao_fases', $processo->descricao_fases) }}</textarea>
                        @error('descricao_fases')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="cursos_destino" class="form-label">Cursos Destinados</label>
                        <textarea class="form-control @error('cursos_destino') is-invalid @enderror" id="cursos_destino"
                            name="cursos_destino"
                            rows="3">{{ old('cursos_destino', is_array($processo->cursos_destino) ? implode("\n", $processo->cursos_destino) : $processo->cursos_destino) }}</textarea>
                        <small class="text-muted">Separe cada curso por linha.</small>
                        @error('cursos_destino')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="requisitos" class="form-label">Requisitos</label>
                        <textarea class="form-control @error('requisitos') is-invalid @enderror" id="requisitos"
                            name="requisitos" rows="4">{{ old('requisitos', $processo->requisitos) }}</textarea>
                        @error('requisitos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes"
                            name="observacoes" rows="3">{{ old('observacoes', $processo->observacoes) }}</textarea>
                        @error('observacoes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-0">
                        <label for="aviso_inscricao" class="form-label">Aviso para Inscrição</label>
                        <textarea class="form-control @error('aviso_inscricao') is-invalid @enderror" id="aviso_inscricao"
                            name="aviso_inscricao"
                            rows="3">{{ old('aviso_inscricao', $processo->aviso_inscricao) }}</textarea>
                        @error('aviso_inscricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-file-upload me-2 text-primary"></i>Adicionar Novos Arquivos</h6>
                </div>
                <div class="card-body">
                    <div id="arquivos-container">
                        <div class="arquivo-item mb-3 p-3 border border-light rounded" style="background-color: #f8f9fa;">
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small">Nome para Exibição</label>
                                    <input type="text" class="form-control form-control-sm" name="nome_exibicao[]"
                                        placeholder="Ex: Edital, Retificação 1">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Tipo</label>
                                    <select class="form-select form-select-sm" name="tipo_arquivo[]">
                                        <option value="edital">Edital</option>
                                        <option value="retificacao">Retificação</option>
                                        <option value="resultado">Resultado</option>
                                        <option value="outro">Outro</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100 remover-arquivo"
                                        style="display: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small">Arquivo</label>
                                <input type="file" class="form-control form-control-sm" name="arquivos[]">
                            </div>
                        </div>
                    </div>
                    <button type="button" id="adicionar-arquivo" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> Adicionar Arquivo
                    </button>
                </div>
            </div>

            <div class="d-flex gap-2 mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Atualizar Processo
                </button>
                <a href="{{ route('processos-seletivos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('arquivos-container');
            const addBtn = document.getElementById('adicionar-arquivo');
            let itemCount = 1;

            function updateRemoveButtons() {
                const items = container.querySelectorAll('.arquivo-item');
                document.querySelectorAll('.remover-arquivo').forEach(btn => {
                    btn.style.display = items.length > 1 ? 'block' : 'none';
                });
            }

            addBtn.addEventListener('click', function () {
                const firstItem = container.querySelector('.arquivo-item');
                const newItem = firstItem.cloneNode(true);

                newItem.querySelectorAll('input, select').forEach(el => {
                    if (el.type !== 'file') el.value = '';
                    if (el.name) el.name = el.name.replace(/\[\d*\]/, `[${itemCount}]`);
                });

                container.appendChild(newItem);
                attachRemoveListener(newItem.querySelector('.remover-arquivo'));
                updateRemoveButtons();
                itemCount++;
            });

            function attachRemoveListener(btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    this.closest('.arquivo-item').remove();
                    updateRemoveButtons();
                });
            }

            document.querySelectorAll('.remover-arquivo').forEach(attachRemoveListener);
            updateRemoveButtons();
        });
    </script>
@endsection