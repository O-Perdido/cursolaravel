@extends('layouts.main')

@section('title', 'Abrir Chamado - ' . $tipoChamado->nome)

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

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-headset me-2"></i>Abrir Chamado - {{ $tipoChamado->nome }}
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('chamados.store') }}" enctype="multipart/form-data" id="formChamado">
                @csrf
                <input type="hidden" name="fk_id_tipo_chamado" value="{{ $tipoChamado->id_tipo_chamado }}">

                @if($tipoChamado->isRescisao())
                    <div class="mb-3">
                        <label class="form-label">Termo de Estágio <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="fk_id_termo" id="fk_id_termo" value="{{ old('fk_id_termo') }}" required>
                            <input type="text" class="form-control" id="termo_selecionado_texto"
                                placeholder="Nenhum termo selecionado" readonly>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#modalBuscarTermo">
                                <i class="fas fa-search me-1"></i> Buscar Termo
                            </button>
                        </div>
                        @error('fk_id_termo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="data_rescisao" class="form-label">Último dia trabalhado <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('data_rescisao') is-invalid @enderror" id="data_rescisao"
                            name="data_rescisao" value="{{ old('data_rescisao') }}" required>
                        @error('data_rescisao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="motivo_rescisao" class="form-label">Motivo da Rescisão <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('motivo_rescisao') is-invalid @enderror" id="motivo_rescisao"
                            name="motivo_rescisao" rows="4" maxlength="1000" required>{{ old('motivo_rescisao') }}</textarea>
                        <small class="form-text text-muted"><span id="contador_motivo">0</span>/1000 caracteres</small>
                        @error('motivo_rescisao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                @elseif($tipoChamado->isAlteracao())
                    <div class="mb-3">
                        <label class="form-label">Termo de Estágio <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="fk_id_termo" id="fk_id_termo" value="{{ old('fk_id_termo') }}" required>
                            <input type="text" class="form-control" id="termo_selecionado_texto"
                                placeholder="Nenhum termo selecionado" readonly>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#modalBuscarTermo">
                                <i class="fas fa-search me-1"></i> Buscar Termo
                            </button>
                        </div>
                        @error('fk_id_termo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="descricao_alteracao" class="form-label">Descrição da Alteração <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('descricao_alteracao') is-invalid @enderror"
                            id="descricao_alteracao" name="descricao_alteracao" rows="5" maxlength="2000"
                            required>{{ old('descricao_alteracao') }}</textarea>
                        <small class="form-text text-muted">Descreva detalhadamente a alteração que precisa ser realizada no
                            termo de estágio. <span id="contador_descricao">0</span>/2000 caracteres</small>
                        @error('descricao_alteracao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                @else
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título do Chamado <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo"
                            maxlength="200" value="{{ old('titulo') }}" placeholder="Resumo objetivo do problema ou solicitação"
                            required>
                        <small class="form-text text-muted"><span id="contador_titulo">0</span>/200 caracteres</small>
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="detalhes" class="form-label">Detalhes <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('detalhes') is-invalid @enderror" id="detalhes" name="detalhes"
                            rows="6" maxlength="5000" required>{{ old('detalhes') }}</textarea>
                        <small class="form-text text-muted">Descreva com detalhes o problema ou a sua solicitação. <span
                                id="contador_detalhes">0</span>/5000 caracteres</small>
                        @error('detalhes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="anexos" class="form-label">Anexos (Opcional)</label>
                        <input type="file" class="form-control @error('anexos.*') is-invalid @enderror" id="anexos"
                            name="anexos[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <small class="form-text text-muted">Você pode anexar prints, documentos ou arquivos relevantes. Formatos
                            aceitos: PDF, JPG, PNG, DOC, DOCX. Tamanho máximo: 5MB por arquivo.</small>
                        @error('anexos.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                @endif

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('welcome') }}" class="btn btn-secondary"><i
                            class="fas fa-arrow-left me-2"></i>Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Abrir
                        Chamado</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                    contador.textContent = campo.value.length;
                    campo.addEventListener('input', function () { contador.textContent = this.value.length; });
                }
            });

            const inputAnexos = document.getElementById('anexos');
            if (inputAnexos) {
                inputAnexos.addEventListener('change', function () {
                    const maxSize = 5 * 1024 * 1024;
                    let arquivosGrandes = [];
                    Array.from(this.files).forEach(file => { if (file.size > maxSize) arquivosGrandes.push(file.name); });
                    if (arquivosGrandes.length > 0) { alert('Os seguintes arquivos excedem 5MB:\n' + arquivosGrandes.join('\n')); this.value = ''; }
                });
            }

            const cpfInput = document.getElementById('filtro_cpf');
            if (cpfInput) {
                cpfInput.addEventListener('input', function () {
                    let cpf = this.value.replace(/\D+/g, '').substring(0, 11);
                    if (cpf.length > 0) {
                        cpf = cpf.substring(0, 3) + '.' +
                            (cpf.length > 3 ? cpf.substring(3, 6) : '') +
                            (cpf.length > 6 ? '.' + cpf.substring(6, 9) : '') +
                            (cpf.length > 9 ? '-' + cpf.substring(9, 11) : '');
                    }
                    this.value = cpf;
                });
            }

            const modalEl = document.getElementById('modalBuscarTermo');
            const formFiltros = document.getElementById('formFiltrosTermo');
            const tabelaBody = document.getElementById('listaTermosBody');
            const campoHiddenTermo = document.getElementById('fk_id_termo');
            const campoTextoTermo = document.getElementById('termo_selecionado_texto');

            function carregarTermos() {
                const params = new URLSearchParams({
                    numero: document.getElementById('filtro_numero').value || '',
                    nome: document.getElementById('filtro_nome').value || '',
                    cpf: document.getElementById('filtro_cpf').value.replace(/\D+/g, '') || '',
                });
                tabelaBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Carregando...</td></tr>';
                fetch('{{ route('api.chamados.termos-lista') }}' + '?' + params.toString())
                    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(data => {
                        const itens = data.results || [];
                        if (itens.length === 0) { tabelaBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Nenhum termo encontrado</td></tr>'; return; }
                        tabelaBody.innerHTML = '';
                        itens.forEach(item => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><strong>${item.numero}/${item.ano}</strong></td>
                                <td>${item.estagiario}</td>
                                <td>${item.cpf}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-primary btnSelecionarTermo" data-id="${item.id}" data-texto="${item.text}">Selecionar</button>
                                </td>
                            `;
                            tabelaBody.appendChild(tr);
                        });
                    })
                    .catch(err => { tabelaBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Erro ao carregar termos</td></tr>'; console.error(err); });
            }

            if (modalEl) { modalEl.addEventListener('shown.bs.modal', carregarTermos); }
            if (formFiltros) { formFiltros.addEventListener('submit', function (e) { e.preventDefault(); carregarTermos(); }); }
            const btnLimpar = document.getElementById('btnLimparFiltros');
            if (btnLimpar) {
                btnLimpar.addEventListener('click', function () {
                    document.getElementById('filtro_numero').value = '';
                    document.getElementById('filtro_nome').value = '';
                    document.getElementById('filtro_cpf').value = '';
                    carregarTermos();
                });
            }
            if (tabelaBody) {
                tabelaBody.addEventListener('click', function (e) {
                    const btn = e.target.closest('.btnSelecionarTermo');
                    if (!btn) return;
                    campoHiddenTermo.value = btn.getAttribute('data-id');
                    campoTextoTermo.value = btn.getAttribute('data-texto');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                });
            }
        });
    </script>
@endsection

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
                        <input type="text" class="form-control form-control-sm" id="filtro_numero"
                            placeholder="Ex: 1380 ou 1380/2025">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">Nome do Estagiário</label>
                        <input type="text" class="form-control form-control-sm" id="filtro_nome"
                            placeholder="Digite o nome">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">CPF do Estagiário</label>
                        <input type="text" class="form-control form-control-sm" id="filtro_cpf"
                            placeholder="Somente números">
                    </div>
                    <div class="col-12 d-flex justify-content-end mt-2 gap-2">
                        <button type="button" id="btnLimparFiltros" class="btn btn-sm btn-secondary">
                            <i class="fas fa-eraser me-1"></i> Limpar
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter me-1"></i>
                            Filtrar</button>
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