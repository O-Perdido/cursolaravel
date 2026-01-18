@extends('layouts.main')

@section('title', 'Novo Processo Seletivo')

@section('content')

    {{-- Cabeçalho --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body pb-2 pt-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>
                    Novo Processo Seletivo
                </h4>
                <a href="{{ route('processos-seletivos.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
    </div>

    {{-- Erros de validação --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-circle me-2"></i>Erros na Validação
            </h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('processos-seletivos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Seção 1: Informações Básicas --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informações Básicas</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título do Processo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo"
                        value="{{ old('titulo') }}" required placeholder="Ex: Processo Seletivo 2026">
                    @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @if($empresaSelecionada === null)
                    <div class="mb-3">
                        <label for="fk_id_empresa" class="form-label">Empresa <span class="text-danger">*</span></label>
                        <select class="form-select @error('fk_id_empresa') is-invalid @enderror" id="fk_id_empresa"
                            name="fk_id_empresa" required>
                            <option value="">-- Selecione uma empresa --</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id_empresa }}" {{ old('fk_id_empresa') == $empresa->id_empresa ? 'selected' : '' }}>{{ $empresa->nome_empresa }}</option>
                            @endforeach
                        </select>
                        @error('fk_id_empresa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @else
                    <input type="hidden" name="fk_id_empresa" value="{{ $empresaSelecionada }}">
                    <p class="text-muted mb-3">
                        <strong>Empresa:</strong>
                        {{ \App\Models\Empresa::find($empresaSelecionada, ['id_empresa', 'nome_empresa'])->nome_empresa ?? 'Empresa não encontrada' }}
                    </p>
                @endif

                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="rascunho" {{ old('status', 'rascunho') === 'rascunho' ? 'selected' : '' }}>Rascunho
                        </option>
                        <option value="aberto" {{ old('status') === 'aberto' ? 'selected' : '' }}>Aberto</option>
                        <option value="inscricoes" {{ old('status') === 'inscricoes' ? 'selected' : '' }}>Inscrições</option>
                        <option value="encerrado" {{ old('status') === 'encerrado' ? 'selected' : '' }}>Encerrado</option>
                        <option value="finalizado" {{ old('status') === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Seção 2: Datas --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-calendar me-2 text-primary"></i>Datas Importantes</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="data_abertura" class="form-label">Data de Abertura</label>
                        <input type="datetime-local" class="form-control @error('data_abertura') is-invalid @enderror"
                            id="data_abertura" name="data_abertura" value="{{ old('data_abertura') }}">
                        @error('data_abertura')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="data_fechamento_inscricoes" class="form-label">Data de Fechamento de Inscrições</label>
                        <input type="datetime-local"
                            class="form-control @error('data_fechamento_inscricoes') is-invalid @enderror"
                            id="data_fechamento_inscricoes" name="data_fechamento_inscricoes"
                            value="{{ old('data_fechamento_inscricoes') }}">
                        @error('data_fechamento_inscricoes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Seção 3: Descrição e Informações --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-align-left me-2 text-primary"></i>Descrição e Informações</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="descricao_fases" class="form-label">Fases do Processo</label>
                    <textarea class="form-control @error('descricao_fases') is-invalid @enderror" id="descricao_fases"
                        name="descricao_fases" rows="3"
                        placeholder="Descreva as fases do processo seletivo...">{{ old('descricao_fases') }}</textarea>
                    @error('descricao_fases')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="cursos_destino" class="form-label">Cursos Destinados</label>
                    <textarea class="form-control @error('cursos_destino') is-invalid @enderror" id="cursos_destino"
                        name="cursos_destino" rows="2"
                        placeholder="Liste os cursos (um por linha)">{{ old('cursos_destino') }}</textarea>
                    <small class="text-muted">Separe cada curso por uma nova linha</small>
                    @error('cursos_destino')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="requisitos" class="form-label">Requisitos</label>
                    <textarea class="form-control @error('requisitos') is-invalid @enderror" id="requisitos"
                        name="requisitos" rows="3"
                        placeholder="Descreva os requisitos necessários...">{{ old('requisitos') }}</textarea>
                    @error('requisitos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes"
                        name="observacoes" rows="2"
                        placeholder="Observações adicionais...">{{ old('observacoes') }}</textarea>
                    @error('observacoes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-0">
                    <label for="aviso_inscricao" class="form-label">Aviso para Inscrição</label>
                    <textarea class="form-control @error('aviso_inscricao') is-invalid @enderror" id="aviso_inscricao"
                        name="aviso_inscricao" rows="2"
                        placeholder="Mensagem personalizada que será exibida ao se inscrever">{{ old('aviso_inscricao') }}</textarea>
                    @error('aviso_inscricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Seção 4: Arquivos do Edital --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-file-upload me-2 text-primary"></i>Arquivos do Edital</h6>
            </div>
            <div class="card-body">
                <div id="arquivos-container">
                    <div class="arquivo-item mb-3 p-3 border border-light rounded" style="background-color: #f8f9fa;">
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label small">Nome para Exibição <span
                                        class="text-danger">*</span></label>
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
                            <label class="form-label small">Arquivo <span class="text-danger">*</span></label>
                            <input type="file" class="form-control form-control-sm" name="arquivos[]" required>
                        </div>
                    </div>
                </div>
                <button type="button" id="adicionar-arquivo" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-plus me-1"></i> Adicionar Arquivo
                </button>
            </div>
        </div>

        {{-- Botões de ação --}}
        <div class="d-flex gap-2 mb-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Salvar Processo
            </button>
            <a href="{{ route('processos-seletivos.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
        </div>
    </form>
    </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('arquivos-container');
            const addBtn = document.getElementById('adicionar-arquivo');
            let itemCount = 1;

            function updateRemoveButtons() {
                const items = container.querySelectorAll('.arquivo-item');
                document.querySelectorAll('.remover-arquivo').forEach((btn, index) => {
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