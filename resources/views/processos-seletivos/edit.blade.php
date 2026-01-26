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
                                Rascunho — não aparece para candidatos</option>
                            <option value="aberto" {{ old('status', $processo->status) === 'aberto' ? 'selected' : '' }}>
                                Publicado — inscrições ainda não iniciaram</option>
                            <option value="inscricoes" {{ old('status', $processo->status) === 'inscricoes' ? 'selected' : '' }}>Inscrições Abertas — botão de inscrição habilitado</option>
                            <option value="encerrado" {{ old('status', $processo->status) === 'encerrado' ? 'selected' : '' }}>Encerrado — inscrições fechadas, fases em andamento</option>
                            <option value="finalizado" {{ old('status', $processo->status) === 'finalizado' ? 'selected' : '' }}>Concluído — processo finalizado</option>
                        </select>
                        <small class="text-muted">Use "Publicado" quando só quer divulgar o edital e abrir inscrições
                            depois.</small>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mt-3">
                        <label for="icone_processo" class="form-label">Ícone do Processo</label>
                        @if($processo->icone_processo)
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img src="{{ asset('storage/' . $processo->icone_processo) }}" alt="Ícone atual"
                                    class="rounded border" style="width: 64px; height: 64px; object-fit: cover;">
                                <span class="text-muted small">Envie um novo arquivo para substituir o ícone atual.</span>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('icone_processo') is-invalid @enderror"
                            id="icone_processo" name="icone_processo" accept="image/*">
                        <small class="text-muted">PNG/JPG quadrado (recomendado 256x256). Mostrado aos candidatos.</small>
                        @error('icone_processo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-calendar me-2 text-primary"></i>Datas Importantes</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="data_abertura" class="form-label">Publicação do Edital</label>
                            <input type="datetime-local" class="form-control @error('data_abertura') is-invalid @enderror"
                                id="data_abertura" name="data_abertura"
                                value="{{ old('data_abertura', $processo->data_abertura?->format('Y-m-d\TH:i')) }}">
                            <small class="text-muted">Informativo para o edital (não libera o botão).</small>
                            @error('data_abertura')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="data_inicio_inscricoes" class="form-label">Início das Inscrições</label>
                            <input type="datetime-local"
                                class="form-control @error('data_inicio_inscricoes') is-invalid @enderror"
                                id="data_inicio_inscricoes" name="data_inicio_inscricoes"
                                value="{{ old('data_inicio_inscricoes', $processo->data_inicio_inscricoes?->format('Y-m-d\TH:i')) }}">
                            <small class="text-muted">Libera o botão de inscrição a partir desta data/hora.</small>
                            @error('data_inicio_inscricoes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="data_fechamento_inscricoes" class="form-label">Fim das Inscrições</label>
                            <input type="datetime-local"
                                class="form-control @error('data_fechamento_inscricoes') is-invalid @enderror"
                                id="data_fechamento_inscricoes" name="data_fechamento_inscricoes"
                                value="{{ old('data_fechamento_inscricoes', $processo->data_fechamento_inscricoes?->format('Y-m-d\TH:i')) }}">
                            <small class="text-muted">Após este horário o botão é bloqueado automaticamente.</small>
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
                        <label class="form-label">Fases do Processo</label>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-2" id="tabela-fases">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 55%">Descrição da fase</th>
                                        <th style="width: 35%">Período/Data</th>
                                        <th style="width: 10%" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-fase">
                            <i class="fas fa-plus me-1"></i> Adicionar fase
                        </button>
                        @error('fases')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
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
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="solicitar_upload_inscricao"
                                name="solicitar_upload_inscricao" {{ old('solicitar_upload_inscricao', $processo->solicitar_upload_inscricao) ? 'checked' : '' }}>
                            <label class="form-check-label" for="solicitar_upload_inscricao">
                                Solicitar upload de arquivo na inscrição (opcional)
                            </label>
                            <small class="text-muted d-block">Se marcado, o candidato deverá anexar um arquivo no popup
                                antes de confirmar.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-list-check me-2 text-primary"></i>Vagas por Nível</h6>
                    <small class="text-muted">Separe cursos por nível e quantidade (use CR para cadastro reserva)</small>
                </div>
                <div class="card-body">
                    <div id="vagas-niveis"></div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-nivel">
                        <i class="fas fa-plus me-1"></i> Adicionar nível
                    </button>
                    @error('vagas')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
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

    @php
        $fallbackVagas = $processo->vagas_por_nivel ?? ($processo->cursos_destino ? [
            [
                'nivel' => 'Cursos',
                'itens' => collect($processo->cursos_destino)->map(fn($curso) => ['curso' => $curso, 'vagas' => 'CR'])->values()->all()
            ]
        ] : []);
        $fallbackFases = $processo->fases ?? ($processo->descricao_fases ? [
            [
                'descricao' => $processo->descricao_fases,
                'periodo' => ''
            ]
        ] : []);
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Arquivos
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

            // Fases
            const fasesData = @json(old('fases', $fallbackFases ?: [['descricao' => '', 'periodo' => '']]));
            const tabelaFases = document.querySelector('#tabela-fases tbody');
            const addFaseBtn = document.getElementById('add-fase');

            function renderFaseRow(data = { descricao: '', periodo: '' }) {
                const idx = tabelaFases.querySelectorAll('tr').length;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                                                            <td><input type="text" name="fases[${idx}][descricao]" class="form-control form-control-sm" placeholder="Ex: Divulgação do edital" value="${data.descricao ?? ''}"></td>
                                                            <td><input type="text" name="fases[${idx}][periodo]" class="form-control form-control-sm" placeholder="Ex: 10/02 a 20/02" value="${data.periodo ?? ''}"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm btn-remove-fase"><i class="fas fa-trash"></i></button></td>
                                                        `;
                tabelaFases.appendChild(tr);
                tr.querySelector('.btn-remove-fase').addEventListener('click', () => {
                    tr.remove();
                    reindexFases();
                });
            }

            function reindexFases() {
                tabelaFases.querySelectorAll('tr').forEach((tr, idx) => {
                    tr.querySelectorAll('input').forEach(input => {
                        input.name = input.name.replace(/fases\[\d+\]/, `fases[${idx}]`);
                    });
                });
            }

            (fasesData && fasesData.length ? fasesData : [{ descricao: '', periodo: '' }]).forEach(item => renderFaseRow(item));
            addFaseBtn.addEventListener('click', () => renderFaseRow());

            // Vagas por nível
            const vagasContainer = document.getElementById('vagas-niveis');
            const addNivelBtn = document.getElementById('add-nivel');
            const fallbackVagasData = {!! json_encode($fallbackVagas ?? [
        ['nivel' => 'Nível Médio', 'itens' => [['curso' => '', 'vagas' => 'CR']]]
    ]) !!};
            const vagasData = @json(old('vagas')) || fallbackVagasData;

            function createNivelCard(data = { nivel: '', itens: [] }) {
                const idxNivel = vagasContainer.querySelectorAll('.nivel-card').length;
                const card = document.createElement('div');
                card.className = 'nivel-card border rounded p-3 mb-3 bg-light';
                const nivelHtml = '<div class="d-flex justify-content-between align-items-start gap-2 mb-2 flex-wrap">' +
                    '<div class="flex-grow-1">' +
                    '<label class="form-label small">Nível</label>' +
                    '<input type="text" class="form-control form-control-sm" name="vagas[' + idxNivel + '][nivel]" value="' + (data.nivel ?? '') + '" placeholder="Ex: Nível Técnico">' +
                    '</div>' +
                    '<button type="button" class="btn btn-outline-danger btn-sm align-self-end remove-nivel"><i class="fas fa-trash"></i></button>' +
                    '</div>' +
                    '<div class="table-responsive mb-2">' +
                    '<table class="table table-sm align-middle mb-1">' +
                    '<thead class="table-light">' +
                    '<tr>' +
                    '<th style="width: 70%">Curso</th>' +
                    '<th style="width: 20%">Vagas</th>' +
                    '<th style="width: 10%" class="text-center">Ações</th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody></tbody>' +
                    '</table>' +
                    '</div>' +
                    '<button type="button" class="btn btn-outline-primary btn-sm add-curso"><i class="fas fa-plus me-1"></i>Adicionar curso</button>';
                card.innerHTML = nivelHtml;

                const tbody = card.querySelector('tbody');
                const addCursoBtn = card.querySelector('.add-curso');

                function addCursoRow(item = { curso: '', vagas: 'CR' }) {
                    const idxCurso = tbody.querySelectorAll('tr').length;
                    const tr = document.createElement('tr');
                    const cursoHtml = '<td><input type="text" class="form-control form-control-sm" name="vagas[' + idxNivel + '][itens][' + idxCurso + '][curso]" value="' + (item.curso ?? '') + '" placeholder="Ex: Administração"></td>' +
                        '<td><input type="text" class="form-control form-control-sm" name="vagas[' + idxNivel + '][itens][' + idxCurso + '][vagas]" value="' + (item.vagas ?? 'CR') + '" placeholder="Ex: 2 ou CR"></td>' +
                        '<td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-curso"><i class="fas fa-trash"></i></button></td>';
                    tr.innerHTML = cursoHtml;
                    tbody.appendChild(tr);
                    tr.querySelector('.remove-curso').addEventListener('click', () => {
                        tr.remove();
                        reindexCursos();
                    });
                }

                function reindexCursos() {
                    tbody.querySelectorAll('tr').forEach((tr, idx) => {
                        tr.querySelectorAll('input').forEach(input => {
                            input.name = input.name.replace(/vagas\[\d+\]\[itens\]\[\d+\]/, 'vagas[' + idxNivel + '][itens][' + idx + ']');
                        });
                    });
                }

                (data.itens && data.itens.length ? data.itens : [{ curso: '', vagas: 'CR' }]).forEach(addCursoRow);
                addCursoBtn.addEventListener('click', () => addCursoRow());

                card.querySelector('.remove-nivel').addEventListener('click', () => {
                    card.remove();
                    reindexNiveis();
                });

                vagasContainer.appendChild(card);
            }

            function reindexNiveis() {
                vagasContainer.querySelectorAll('.nivel-card').forEach((card, nivelIdx) => {
                    card.querySelectorAll('input').forEach(input => {
                        input.name = input.name.replace(/vagas\[\d+\]/, `vagas[${nivelIdx}]`);
                    });

                    card.querySelectorAll('tbody tr').forEach((tr, cursoIdx) => {
                        tr.querySelectorAll('input').forEach(input => {
                            input.name = input.name.replace(/itens\]\[\d+\]/, `itens][${cursoIdx}]`);
                        });
                    });
                });
            }

            (vagasData && vagasData.length ? vagasData : [{ nivel: 'Nível Médio', itens: [{ curso: '', vagas: 'CR' }] }]).forEach(createNivelCard);
            addNivelBtn.addEventListener('click', () => createNivelCard());
        });
    </script>
@endsection