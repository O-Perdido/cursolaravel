@extends('layouts.main')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contadores de caracteres
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="data_rescisao" class="form-label">
                            Último dia trabalhado <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control @error('data_rescisao') is-invalid @enderror" id="data_rescisao"
                            name="data_rescisao" value="{{ old('data_rescisao') }}" required>
                        @error('data_rescisao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="motivo_rescisao" class="form-label">
                            Motivo da Rescisão <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('motivo_rescisao') is-invalid @enderror" id="motivo_rescisao"
                            name="motivo_rescisao" rows="4" maxlength="1000" required>{{ old('motivo_rescisao') }}</textarea>
                        <small class="form-text text-muted">
                            <span id="contador_motivo">0</span>/1000 caracteres
                        </small>
                        @error('motivo_rescisao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                @elseif($tipoChamado->isAlteracao())
                    <!-- Formulário de Alteração -->
                    <div class="mb-3">
                        <label class="form-label">Termo de Estágio <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="fk_id_termo" id="fk_id_termo" value="{{ old('fk_id_termo') }}" required>
                            <input type="text" class="form-control" id="termo_selecionado_texto" placeholder="Nenhum termo selecionado" readonly>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalBuscarTermo">
                                <i class="fas fa-search me-1"></i> Buscar Termo
                            </button>
                        </div>
                        @error('fk_id_termo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="descricao_alteracao" class="form-label">
                            Descrição da Alteração <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('descricao_alteracao') is-invalid @enderror"
                            id="descricao_alteracao" name="descricao_alteracao" rows="5" maxlength="2000"
                            required>{{ old('descricao_alteracao') }}</textarea>
                        <small class="form-text text-muted">
                            Descreva detalhadamente a alteração que precisa ser realizada no termo de estágio.
                            <span id="contador_descricao">0</span>/2000 caracteres
                        </small>
                        @error('descricao_alteracao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                @else
                    <!-- Formulário Genérico (Outros e tipos cadastrados) -->
                    <div class="mb-3">
                        <label for="titulo" class="form-label">
                            Título do Chamado <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo"
                            maxlength="200" value="{{ old('titulo') }}" placeholder="Resumo objetivo do problema ou solicitação"
                            required>
                        <small class="form-text text-muted">
                            <span id="contador_titulo">0</span>/200 caracteres
                        </small>
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="detalhes" class="form-label">
                            Detalhes <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('detalhes') is-invalid @enderror" id="detalhes" name="detalhes"
                            rows="6" maxlength="5000" required>{{ old('detalhes') }}</textarea>
                        <small class="form-text text-muted">
                            Descreva com detalhes o problema ou a sua solicitação.
                            <span id="contador_detalhes">0</span>/5000 caracteres
                        </small>
                        @error('detalhes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="anexos" class="form-label">
                            Anexos (Opcional)
                        </label>
                        <input type="file" class="form-control @error('anexos.*') is-invalid @enderror" id="anexos"
                            name="anexos[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <small class="form-text text-muted">
                            Você pode anexar prints, documentos ou arquivos relevantes.
                            Formatos aceitos: PDF, JPG, PNG, DOC, DOCX. Tamanho máximo: 5MB por arquivo.
                        </small>
                        @error('anexos.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                @endif

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('welcome') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Abrir Chamado
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializa Select2 para busca de termos
            @if($tipoChamado->isRescisao() || $tipoChamado->isAlteracao())
                $('.select2-termo').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Busque pelo número do termo, nome ou CPF do estagiário...',
                    allowClear: true,
                    ajax: {
                        url: '{{ route("api.chamados.buscar-termos") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                page: params.page || 1
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0,
                    language: {
                        inputTooShort: function () {
                            return 'Digite para buscar';
                        },
                        noResults: function () {
                            return 'Nenhum termo encontrado';
                        },
                        searching: function () {
                            return 'Buscando...';
                        }
                    }
                });
            @endif

        // Contadores de caracteres
        const contadores = [
                { campo: 'motivo_rescisao', contador: 'contador_motivo' },
                { campo: 'descricao_alteracao', contador: 'contador_descricao' },
                { campo: 'titulo', contador: 'contador_titulo' },
                { campo: 'detalhes', contador: 'contador_detalhes' }
            ];

            contadores.forEach(item => {
                const campo = document.getElementById(item.campo);
                const contador = document.getElementById(item.contador);
                if (campo && contador) {
                    // Atualiza no carregamento
                    contador.textContent = campo.value.length;

                    // Atualiza ao digitar
                    campo.addEventListener('input', function () {
                        contador.textContent = this.value.length;
                    });
                }
            });

            // Validação de tamanho de arquivos
            const inputAnexos = document.getElementById('anexos');
            if (inputAnexos) {
                inputAnexos.addEventListener('change', function () {
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    let totalSize = 0;
                    let arquivosGrandes = [];

                    Array.from(this.files).forEach(file => {
                        totalSize += file.size;
                        if (file.size > maxSize) {
                            arquivosGrandes.push(file.name);
                        }
                    });

                    if (arquivosGrandes.length > 0) {
                        alert('Os seguintes arquivos excedem o tamanho máximo de 5MB:\n' + arquivosGrandes.join('\n'));
                        this.value = '';
                    }
                });
            }

            // Modal de buscar termo
            const modalEl = document.getElementById('modalBuscarTermo');
            const formFiltros = document.getElementById('formFiltrosTermo');
            const tabelaBody = document.getElementById('listaTermosBody');
            const campoHiddenTermo = document.getElementById('fk_id_termo');
            const campoTextoTermo = document.getElementById('termo_selecionado_texto');

            function carregarTermos() {
                const params = new URLSearchParams({
                    numero: document.getElementById('filtro_numero').value || '',
                    nome: document.getElementById('filtro_nome').value || '',
                    cpf: document.getElementById('filtro_cpf').value || '',
                });
                tabelaBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Carregando...</td></tr>';
                fetch('{{ route('api.chamados.termos-lista') }}' + '?' + params.toString())
                    .then(r => {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(data => {
                        const itens = data.results || [];
                        if (itens.length === 0) {
                            tabelaBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Nenhum termo encontrado</td></tr>';
                            return;
                        }
                        tabelaBody.innerHTML = '';
                        itens.forEach(item => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><strong>${item.numero}/${item.ano}</strong></td>
                                <td>${item.estagiario}</td>
                                <td>${item.cpf}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-primary btnSelecionarTermo" data-id="${item.id}" data-texto="${item.text}">
                                        Selecionar
                                    </button>
                                </td>
                            `;
                            tabelaBody.appendChild(tr);
                        });
                    })
                    .catch(err => {
                        tabelaBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Erro ao carregar termos</td></tr>';
                        console.error('Erro ao carregar termos:', err);
                    });
            }

            if (modalEl) {
                modalEl.addEventListener('shown.bs.modal', carregarTermos);
            }
            if (formFiltros) {
                formFiltros.addEventListener('submit', function(e) {
                    e.preventDefault();
                    carregarTermos();
                });
            }
            if (tabelaBody) {
                tabelaBody.addEventListener('click', function(e) {
                    const btn = e.target.closest('.btnSelecionarTermo');
                    if (!btn) return;
                    const id = btn.getAttribute('data-id');
                    const texto = btn.getAttribute('data-texto');
                    campoHiddenTermo.value = id;
                    campoTextoTermo.value = texto;
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    modalInstance && modalInstance.hide();
                });
            }
        });
    </script>
@endpush

        @push('scripts')
        <script>
        // Opcional: máscara simples de CPF (somente números)
        document.addEventListener('DOMContentLoaded', function() {
            const cpfInput = document.getElementById('filtro_cpf');
            if (cpfInput) {
                cpfInput.addEventListener('input', function() {
                    this.value = this.value.replace(/\D+/g, '');
                });
            }
        });
        </script>
        @endpush

        <!-- Modal: Buscar Termo -->
        <div class="modal fade" id="modalBuscarTermo" tabindex="-1" aria-labelledby="modalBuscarTermoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalBuscarTermoLabel">Selecionar Termo de Estágio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formFiltrosTermo" class="row g-2 mb-2">
                            <div class="col-md-4">
                                <label class="form-label mb-1">Número/Ano</label>
                                <input type="text" class="form-control form-control-sm" id="filtro_numero" placeholder="Ex: 1380 ou 1380/2025">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label mb-1">Nome do Estagiário</label>
                                <input type="text" class="form-control form-control-sm" id="filtro_nome" placeholder="Digite o nome">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label mb-1">CPF do Estagiário</label>
                                <input type="text" class="form-control form-control-sm" id="filtro_cpf" placeholder="Somente números">
                            </div>
                            <div class="col-12 d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-filter me-1"></i> Filtrar
                                </button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th class="w-25">Número/Ano</th>
                                        <th class="w-50">Estagiário</th>
                                        <th class="w-25">CPF</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="listaTermosBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
    
        </div>