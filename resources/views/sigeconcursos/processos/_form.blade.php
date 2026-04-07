@php
    $processo = $processo ?? null;
    $fasesData = old('fases', $processo?->fases ?? [['descricao' => '', 'periodo' => '']]);
    $cargosData = old('cargos', $processo?->processoCargos?->map(function ($item) {
        return [
            'fk_id_cargo' => $item->fk_id_cargo,
            'quantidade_vagas' => $item->quantidade_vagas,
            'is_cadastro_reserva' => (bool) $item->quantidade_cadastro_reserva,
            'valor_remuneracao' => $item->valor_remuneracao,
            'valor_taxa_inscricao' => $item->valor_taxa_inscricao,
            'carga_horaria' => $item->carga_horaria,
            'requisitos_especificos' => $item->requisitos_especificos,
        ];
    })->all() ?? [[
        'fk_id_cargo' => '',
        'quantidade_vagas' => '',
        'is_cadastro_reserva' => false,
        'valor_remuneracao' => '',
        'valor_taxa_inscricao' => '',
        'carga_horaria' => '',
        'requisitos_especificos' => '',
    ]]);
    $locaisData = old('locais', $processo?->processoLocais?->map(function ($item) {
        return [
            'fk_id_local_prova' => $item->fk_id_local_prova,
            'observacoes' => $item->observacoes,
        ];
    })->all() ?? [['fk_id_local_prova' => '', 'observacoes' => '']]);
    $isencoesData = old('isencoes', $processo?->isencoes?->map(function ($item) {
        return [
            'titulo' => $item->titulo,
            'descricao' => $item->descricao,
            'data_inicio' => optional($item->data_inicio)->format('Y-m-d\\TH:i'),
            'data_fim' => optional($item->data_fim)->format('Y-m-d\\TH:i'),
            'exige_comprovacao' => $item->exige_comprovacao,
        ];
    })->all() ?? [['titulo' => '', 'descricao' => '', 'data_inicio' => '', 'data_fim' => '', 'exige_comprovacao' => false]]);
    $documentosExigidosData = old('documentos_exigidos', $processo?->documentosExigidos?->map(function ($item) {
        return [
            'titulo' => $item->titulo,
            'descricao' => $item->descricao,
            'obrigatorio' => $item->obrigatorio,
        ];
    })->all() ?? [['titulo' => '', 'descricao' => '', 'obrigatorio' => true]]);
    $cargoOptionsData = $cargos->map(function ($cargo) {
        return [
            'id' => $cargo->id_cargo,
            'nome' => $cargo->nome_cargo,
        ];
    })->values();
    $localOptionsData = $locais->map(function ($local) {
        return [
            'id' => $local->id_local_prova,
            'nome' => $local->nome_local,
            'cidade' => $local->cidade?->nm_cidade,
            'uf' => $local->cidade?->estado?->uf_estado,
        ];
    })->values();
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Revise os campos abaixo.</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if($processo && $processo->documentosExigidos->count() > 0)
    <div class="card shadow-sm mb-3">
        <div class="card-header">Documentos Exigidos Atuais</div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                @foreach($processo->documentosExigidos as $documento)
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="fw-semibold">{{ $documento->titulo }}</div>
                            <div class="small text-muted">{{ $documento->obrigatorio ? 'Obrigatório' : 'Opcional' }}</div>
                            @if($documento->descricao)
                                <div class="small text-muted">{{ $documento->descricao }}</div>
                            @endif
                        </div>
                        <form action="{{ route('sigeconcursos.processos.documentos-exigidos.destroy', $documento->id_documento_exigido) }}" method="POST"
                            onsubmit="return confirm('Remover este documento exigido?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Excluir</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@if($processo && $processo->arquivos->count() > 0)
    <div class="card shadow-sm mb-3">
        <div class="card-header">Arquivos Atuais</div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                @foreach($processo->arquivos as $arquivo)
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="fw-semibold">{{ $arquivo->nome_exibicao }}</div>
                            <div class="small text-muted">{{ ucfirst($arquivo->tipo_arquivo) }}</div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ asset('storage/' . $arquivo->caminho_arquivo) }}" target="_blank" class="btn btn-outline-primary btn-sm">Abrir</a>
                            <form action="{{ route('sigeconcursos.processos.arquivos.destroy', $arquivo->id_arquivo) }}" method="POST"
                                onsubmit="return confirm('Remover este arquivo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Excluir</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-header">Informações Básicas</div>
        <div class="card-body">
            @if($processo)
                <div class="mb-3">
                    <label class="form-label text-muted">Número do Processo</label>
                    <p class="h6 mb-0">{{ $processo->numero_processo }}</p>
                </div>
            @endif

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="tipo_processo" class="form-label">Tipo do Processo</label>
                    <select class="form-select @error('tipo_processo') is-invalid @enderror" id="tipo_processo" name="tipo_processo" required>
                        <option value="concurso_publico" {{ old('tipo_processo', $processo?->tipo_processo) === 'concurso_publico' ? 'selected' : '' }}>Concurso Público</option>
                        <option value="processo_seletivo" {{ old('tipo_processo', $processo?->tipo_processo) === 'processo_seletivo' ? 'selected' : '' }}>Processo Seletivo</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="fk_id_empresa" class="form-label">Órgão Responsável</label>
                    <select class="form-select @error('fk_id_empresa') is-invalid @enderror" id="fk_id_empresa" name="fk_id_empresa" required>
                        <option value="">Selecione</option>
                        @foreach($orgaos as $orgao)
                            <option value="{{ $orgao->id_empresa }}" {{ (string) old('fk_id_empresa', $processo?->fk_id_empresa) === (string) $orgao->id_empresa ? 'selected' : '' }}>
                                {{ $orgao->nome_razao_social }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="numero_edital" class="form-label">Número do Edital</label>
                    <input type="text" class="form-control @error('numero_edital') is-invalid @enderror" id="numero_edital"
                        name="numero_edital" value="{{ old('numero_edital', $processo?->numero_edital) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        @foreach(['rascunho', 'publicado', 'inscricoes_abertas', 'inscricoes_encerradas', 'em_andamento', 'finalizado', 'suspenso'] as $status)
                            <option value="{{ $status }}" {{ old('status', $processo?->status ?? 'rascunho') === $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Use manualmente apenas para rascunho, publicado, suspenso ou finalizado. Durante a operacao normal o sistema sincroniza esse campo conforme o andamento real.</small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <div class="rounded-3 border p-3 bg-light h-100">
                        <div class="fw-semibold mb-2">Como a jornada funciona agora</div>
                        <div class="small text-muted mb-2">O processo passa a ser guiado principalmente pelo andamento operacional:</div>
                        <div class="small text-muted">1. Publicado ou inscricoes abertas mantem a etapa de inscricoes.</div>
                        <div class="small text-muted">2. Quando as inscricoes terminam e ha candidaturas, o processo entra em homologacao.</div>
                        <div class="small text-muted">3. Distribuicoes por local e por sala atualizam a jornada automaticamente.</div>
                        <div class="small text-muted">4. Suspenso e finalizado continuam como controles administrativos manuais.</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="etapa_fluxo_atual" class="form-label">Etapa Operacional Atual</label>
                    <select class="form-select @error('etapa_fluxo_atual') is-invalid @enderror" id="etapa_fluxo_atual" name="etapa_fluxo_atual" required>
                        @foreach(\App\Models\SigeConcursoProcesso::etapasFluxoDefinicoes() as $chave => $etapa)
                            <option value="{{ $chave }}" {{ old('etapa_fluxo_atual', $processo?->etapa_fluxo_atual ?? 'cadastro') === $chave ? 'selected' : '' }}>
                                {{ $etapa['titulo'] }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Use como ajuste fino apenas em excecoes. O hub e as paginas operacionais priorizam a etapa calculada automaticamente.</small>
                </div>
            </div>

            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo"
                    value="{{ old('titulo', $processo?->titulo) }}" required>
            </div>

            <div class="mb-3">
                <label for="icone_processo" class="form-label">Ícone / Imagem do Processo</label>
                @if($processo?->icone_processo)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $processo->icone_processo) }}" alt="Ícone atual"
                            style="max-height: 80px; border-radius: 8px; border: 1px solid #dee2e6;">
                        <small class="text-muted d-block mt-1">Ícone atual. Envie uma nova imagem para substituir.</small>
                    </div>
                @endif
                <input type="file" class="form-control @error('icone_processo') is-invalid @enderror" id="icone_processo"
                    name="icone_processo" accept="image/*">
                @error('icone_processo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Opcional. Imagem PNG/JPG, máx. 2 MB.</small>
            </div>

            <div class="mb-3">
                <label for="resumo" class="form-label">Resumo</label>
                <textarea class="form-control @error('resumo') is-invalid @enderror" id="resumo" name="resumo" rows="3">{{ old('resumo', $processo?->resumo) }}</textarea>
            </div>

            <div class="mb-0">
                <label for="descricao" class="form-label">Descrição Completa</label>
                <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao" rows="5">{{ old('descricao', $processo?->descricao) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header">Datas e Cronograma</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="data_publicacao" class="form-label">Data de Publicação</label>
                    <input type="datetime-local" class="form-control" id="data_publicacao" name="data_publicacao"
                        value="{{ old('data_publicacao', $processo?->data_publicacao?->format('Y-m-d\\TH:i')) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="data_inicio_inscricoes" class="form-label">Início das Inscrições</label>
                    <input type="datetime-local" class="form-control" id="data_inicio_inscricoes" name="data_inicio_inscricoes"
                        value="{{ old('data_inicio_inscricoes', $processo?->data_inicio_inscricoes?->format('Y-m-d\\TH:i')) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="data_fim_inscricoes" class="form-label">Fim das Inscrições</label>
                    <input type="datetime-local" class="form-control" id="data_fim_inscricoes" name="data_fim_inscricoes"
                        value="{{ old('data_fim_inscricoes', $processo?->data_fim_inscricoes?->format('Y-m-d\\TH:i')) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="data_prova" class="form-label">Data da Prova</label>
                    <input type="datetime-local" class="form-control" id="data_prova" name="data_prova"
                        value="{{ old('data_prova', $processo?->data_prova?->format('Y-m-d\\TH:i')) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="data_resultado_final" class="form-label">Resultado Final</label>
                    <input type="datetime-local" class="form-control" id="data_resultado_final" name="data_resultado_final"
                        value="{{ old('data_resultado_final', $processo?->data_resultado_final?->format('Y-m-d\\TH:i')) }}">
                </div>
            </div>

            <div class="mt-2">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0">Fases do Processo</label>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="adicionar-fase">
                        <i class="fas fa-plus me-1"></i> Adicionar Fase
                    </button>
                </div>
                <div id="fases-container"></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Cargos e Vagas</span>
            <button type="button" class="btn btn-outline-primary btn-sm" id="adicionar-cargo">
                <i class="fas fa-plus me-1"></i> Adicionar Cargo
            </button>
        </div>
        <div class="card-body">
            <div id="cargos-container"></div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Locais de Prova</span>
            <button type="button" class="btn btn-outline-primary btn-sm" id="adicionar-local">
                <i class="fas fa-plus me-1"></i> Adicionar Local
            </button>
        </div>
        <div class="card-body">
            <div id="locais-container"></div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Casos de Isenção</span>
            <button type="button" class="btn btn-outline-primary btn-sm" id="adicionar-isencao">
                <i class="fas fa-plus me-1"></i> Adicionar Isenção
            </button>
        </div>
        <div class="card-body">
            <div id="isencoes-container"></div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header">Configurações Futuras de Inscrição</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="exige_aceite_edital" name="exige_aceite_edital" value="1"
                            {{ old('exige_aceite_edital', $processo?->exige_aceite_edital ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="exige_aceite_edital">Exigir aceite do edital</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="permite_ampla_concorrencia" name="permite_ampla_concorrencia" value="1"
                            {{ old('permite_ampla_concorrencia', $processo?->permite_ampla_concorrencia ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="permite_ampla_concorrencia">Habilitar ampla concorrência</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="permite_pcd" name="permite_pcd" value="1"
                            {{ old('permite_pcd', $processo?->permite_pcd ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="permite_pcd">Habilitar modalidade PCD</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="permite_condicao_especial" name="permite_condicao_especial" value="1"
                            {{ old('permite_condicao_especial', $processo?->permite_condicao_especial ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="permite_condicao_especial">Permitir solicitação de condição especial de aplicação</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="exige_documento_condicao_especial" name="exige_documento_condicao_especial" value="1"
                            {{ old('exige_documento_condicao_especial', $processo?->exige_documento_condicao_especial ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="exige_documento_condicao_especial">Exigir documento/laudo para condição especial</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="possui_taxa_inscricao" name="possui_taxa_inscricao" value="1"
                            {{ old('possui_taxa_inscricao', $processo?->possui_taxa_inscricao) ? 'checked' : '' }}>
                        <label class="form-check-label" for="possui_taxa_inscricao">Possui taxa de inscrição</label>
                    </div>
                    <div>
                        <label for="valor_taxa_padrao" class="form-label">Valor padrão da taxa</label>
                        <input type="text" class="form-control" id="valor_taxa_padrao" name="valor_taxa_padrao"
                            value="{{ old('valor_taxa_padrao', $processo?->valor_taxa_padrao) }}" placeholder="0,00">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Documentos Exigidos na Inscrição</span>
            <button type="button" class="btn btn-outline-primary btn-sm" id="adicionar-documento-exigido">
                <i class="fas fa-plus me-1"></i> Adicionar Documento
            </button>
        </div>
        <div class="card-body">
            <div class="alert alert-light border mb-3">
                Cadastre aqui os documentos que o candidato precisará enviar no momento da inscrição, conforme o edital.
            </div>
            <div id="documentos-exigidos-container"></div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header">Requisitos e Observações</div>
        <div class="card-body">
            <div class="mb-3">
                <label for="requisitos_gerais" class="form-label">Requisitos Gerais</label>
                <textarea class="form-control" id="requisitos_gerais" name="requisitos_gerais" rows="4">{{ old('requisitos_gerais', $processo?->requisitos_gerais) }}</textarea>
            </div>
            <div class="mb-0">
                <label for="observacoes" class="form-label">Observações</label>
                <textarea class="form-control" id="observacoes" name="observacoes" rows="4">{{ old('observacoes', $processo?->observacoes) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Arquivos do Processo</span>
            <button type="button" class="btn btn-outline-primary btn-sm" id="adicionar-arquivo">
                <i class="fas fa-plus me-1"></i> Adicionar Arquivo
            </button>
        </div>
        <div class="card-body">
            <div id="arquivos-container">
                <div class="arquivo-item border rounded p-3 mb-3 bg-light">
                    <div class="row">
                        <div class="col-md-5 mb-2">
                            <label class="form-label small">Nome para Exibição</label>
                            <input type="text" class="form-control form-control-sm" name="nome_exibicao[]" placeholder="Ex: Edital de abertura">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">Tipo</label>
                            <select class="form-select form-select-sm" name="tipo_arquivo[]">
                                <option value="edital">Edital</option>
                                <option value="retificacao">Retificação</option>
                                <option value="anexo">Anexo</option>
                                <option value="conteudo_programatico">Conteúdo programático</option>
                                <option value="resultado">Resultado</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 remover-arquivo" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <label class="form-label small">Arquivo</label>
                    <input type="file" class="form-control form-control-sm" name="arquivos[]">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-3">
        <a href="{{ route('sigeconcursos.processos.index') }}" class="btn btn-outline-secondary">Voltar</a>
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fasesData = @json($fasesData);
        const cargosData = @json($cargosData);
        const locaisData = @json($locaisData);
        const isencoesData = @json($isencoesData);
        const documentosExigidosData = @json($documentosExigidosData);
        const cargoOptions = @json($cargoOptionsData);
        const localOptions = @json($localOptionsData);
        const fasesContainer = document.getElementById('fases-container');
        const cargosContainer = document.getElementById('cargos-container');
        const locaisContainer = document.getElementById('locais-container');
        const isencoesContainer = document.getElementById('isencoes-container');
        const documentosExigidosContainer = document.getElementById('documentos-exigidos-container');
        const arquivosContainer = document.getElementById('arquivos-container');
        const adicionarArquivo = document.getElementById('adicionar-arquivo');
        const valorTaxaPadrao = document.getElementById('valor_taxa_padrao');
        const permiteCondicaoEspecial = document.getElementById('permite_condicao_especial');
        const exigeDocumentoCondicaoEspecial = document.getElementById('exige_documento_condicao_especial');

        function applyMoneyMask(value) {
            value = (value || '').replace(/\D/g, '');
            if (!value) {
                return '';
            }
            value = (parseInt(value, 10) / 100).toFixed(2) + '';
            value = value.replace('.', ',');
            return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function bindMoneyMask(input) {
            if (!input) {
                return;
            }
            input.value = applyMoneyMask(input.value);
            input.addEventListener('input', function () {
                this.value = applyMoneyMask(this.value);
            });
        }

        function optionsHtml(options, selected, withLocation = false) {
            let html = '<option value="">Selecione</option>';
            options.forEach(option => {
                const label = withLocation ? `${option.nome} - ${option.cidade || ''}/${option.uf || ''}` : option.nome;
                html += `<option value="${option.id}" ${String(selected) === String(option.id) ? 'selected' : ''}>${label}</option>`;
            });
            return html;
        }

        function renderFase(data = { descricao: '', periodo: '' }) {
            const index = fasesContainer.querySelectorAll('.fase-item').length;
            const item = document.createElement('div');
            item.className = 'fase-item border rounded p-3 mb-2 bg-light';
            item.innerHTML = `
                <div class="row align-items-end">
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">Descrição</label>
                        <input type="text" class="form-control form-control-sm" name="fases[${index}][descricao]" value="${data.descricao ?? ''}">
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="form-label small">Período/Data</label>
                        <input type="text" class="form-control form-control-sm" name="fases[${index}][periodo]" value="${data.periodo ?? ''}">
                    </div>
                    <div class="col-md-1 mb-2 d-grid">
                        <button type="button" class="btn btn-outline-danger btn-sm remover-item"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            bindRemove(item, fasesContainer, 'fases', '.fase-item');
            fasesContainer.appendChild(item);
        }

        function renderCargo(data = {}) {
            const index = cargosContainer.querySelectorAll('.cargo-item').length;
            const item = document.createElement('div');
            item.className = 'cargo-item border rounded p-3 mb-3 bg-light';
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Cargo Vinculado</h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remover-item"><i class="fas fa-trash"></i></button>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label small">Cargo</label>
                        <select class="form-select form-select-sm" name="cargos[${index}][fk_id_cargo]">${optionsHtml(cargoOptions, data.fk_id_cargo)}</select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label small">Vagas</label>
                        <input type="number" min="0" class="form-control form-control-sm vagas-input" name="cargos[${index}][quantidade_vagas]" value="${data.quantidade_vagas ?? ''}">
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <div class="form-check mb-1">
                            <input class="form-check-input cadastro-reserva-check" type="checkbox" name="cargos[${index}][is_cadastro_reserva]" value="1" id="cr_${index}" ${data.is_cadastro_reserva ? 'checked' : ''}>
                            <label class="form-check-label small" for="cr_${index}">Cadastro Reserva (opcional)</label>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label small">Remuneração</label>
                        <input type="text" class="form-control form-control-sm money-field" name="cargos[${index}][valor_remuneracao]" value="${data.valor_remuneracao ?? ''}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label small">Taxa</label>
                        <input type="text" class="form-control form-control-sm money-field" name="cargos[${index}][valor_taxa_inscricao]" value="${data.valor_taxa_inscricao ?? ''}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label small">Carga Horária</label>
                        <input type="text" class="form-control form-control-sm" name="cargos[${index}][carga_horaria]" value="${data.carga_horaria ?? ''}">
                    </div>
                    <div class="col-md-9 mb-2">
                        <label class="form-label small">Requisitos Específicos</label>
                        <input type="text" class="form-control form-control-sm" name="cargos[${index}][requisitos_especificos]" value="${data.requisitos_especificos ?? ''}">
                    </div>
                </div>
            `;
            bindRemove(item, cargosContainer, 'cargos', '.cargo-item');
            cargosContainer.appendChild(item);
            item.querySelectorAll('.money-field').forEach(bindMoneyMask);
        }

        function renderLocal(data = {}) {
            const index = locaisContainer.querySelectorAll('.local-item').length;
            const item = document.createElement('div');
            item.className = 'local-item border rounded p-3 mb-3 bg-light';
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Local Vinculado</h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remover-item"><i class="fas fa-trash"></i></button>
                </div>
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <label class="form-label small">Local</label>
                        <select class="form-select form-select-sm" name="locais[${index}][fk_id_local_prova]">${optionsHtml(localOptions, data.fk_id_local_prova, true)}</select>
                    </div>
                    <div class="col-md-7 mb-2">
                        <label class="form-label small">Observações</label>
                        <input type="text" class="form-control form-control-sm" name="locais[${index}][observacoes]" value="${data.observacoes ?? ''}">
                    </div>
                </div>
            `;
            bindRemove(item, locaisContainer, 'locais', '.local-item');
            locaisContainer.appendChild(item);
        }

        function renderIsencao(data = {}) {
            const index = isencoesContainer.querySelectorAll('.isencao-item').length;
            const item = document.createElement('div');
            item.className = 'isencao-item border rounded p-3 mb-3 bg-light';
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Caso de Isenção</h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remover-item"><i class="fas fa-trash"></i></button>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label small">Título</label>
                        <input type="text" class="form-control form-control-sm" name="isencoes[${index}][titulo]" value="${data.titulo ?? ''}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label small">Data Início</label>
                        <input type="datetime-local" class="form-control form-control-sm" name="isencoes[${index}][data_inicio]" value="${data.data_inicio ?? ''}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label small">Data Fim</label>
                        <input type="datetime-local" class="form-control form-control-sm" name="isencoes[${index}][data_fim]" value="${data.data_fim ?? ''}">
                    </div>
                    <div class="col-md-10 mb-2">
                        <label class="form-label small">Descrição</label>
                        <input type="text" class="form-control form-control-sm" name="isencoes[${index}][descricao]" value="${data.descricao ?? ''}">
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="isencoes[${index}][exige_comprovacao]" value="1" ${data.exige_comprovacao ? 'checked' : ''}>
                            <label class="form-check-label small">Comprovação</label>
                        </div>
                    </div>
                </div>
            `;
            bindRemove(item, isencoesContainer, 'isencoes', '.isencao-item');
            isencoesContainer.appendChild(item);
        }

        function renderDocumentoExigido(data = {}) {
            const index = documentosExigidosContainer.querySelectorAll('.documento-exigido-item').length;
            const item = document.createElement('div');
            item.className = 'documento-exigido-item border rounded p-3 mb-3 bg-light';
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Documento Exigido</h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remover-item"><i class="fas fa-trash"></i></button>
                </div>
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <label class="form-label small">Título</label>
                        <input type="text" class="form-control form-control-sm" name="documentos_exigidos[${index}][titulo]" value="${data.titulo ?? ''}" placeholder="Ex: Comprovante de residência">
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="form-label small">Descrição/Orientação</label>
                        <input type="text" class="form-control form-control-sm" name="documentos_exigidos[${index}][descricao]" value="${data.descricao ?? ''}" placeholder="Ex: arquivo legível em PDF">
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="documentos_exigidos[${index}][obrigatorio]" value="1" ${data.obrigatorio ? 'checked' : ''}>
                            <label class="form-check-label small">Obrigatório</label>
                        </div>
                    </div>
                </div>
            `;
            bindRemove(item, documentosExigidosContainer, 'documentos_exigidos', '.documento-exigido-item');
            documentosExigidosContainer.appendChild(item);
        }

        function bindRemove(item, container, prefix, selector) {
            item.querySelector('.remover-item').addEventListener('click', function () {
                item.remove();
                reindex(container, prefix, selector);
            });
        }

        function reindex(container, prefix, selector) {
            container.querySelectorAll(selector).forEach((item, index) => {
                item.querySelectorAll('input, select, textarea').forEach(field => {
                    field.name = field.name.replace(new RegExp(`${prefix}\\[\\d+\\]`), `${prefix}[${index}]`);
                });
            });
        }

        function updateRemoveArquivoButtons() {
            const items = arquivosContainer.querySelectorAll('.arquivo-item');
            arquivosContainer.querySelectorAll('.remover-arquivo').forEach(button => {
                button.style.display = items.length > 1 ? 'block' : 'none';
            });
        }

        function bindArquivoRemove(button) {
            button.addEventListener('click', function () {
                button.closest('.arquivo-item').remove();
                updateRemoveArquivoButtons();
            });
        }

        adicionarArquivo.addEventListener('click', function () {
            const firstItem = arquivosContainer.querySelector('.arquivo-item');
            const newItem = firstItem.cloneNode(true);
            newItem.querySelectorAll('input').forEach(input => {
                if (input.type === 'file') {
                    input.value = '';
                } else {
                    input.value = '';
                }
            });
            newItem.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0;
            });
            bindArquivoRemove(newItem.querySelector('.remover-arquivo'));
            arquivosContainer.appendChild(newItem);
            updateRemoveArquivoButtons();
        });

        arquivosContainer.querySelectorAll('.remover-arquivo').forEach(bindArquivoRemove);
        updateRemoveArquivoButtons();
        bindMoneyMask(valorTaxaPadrao);

        function syncCondicaoEspecialState() {
            exigeDocumentoCondicaoEspecial.disabled = !permiteCondicaoEspecial.checked;
            if (!permiteCondicaoEspecial.checked) {
                exigeDocumentoCondicaoEspecial.checked = false;
            }
        }

        permiteCondicaoEspecial.addEventListener('change', syncCondicaoEspecialState);
        syncCondicaoEspecialState();

        (fasesData && fasesData.length ? fasesData : [{ descricao: '', periodo: '' }]).forEach(renderFase);
        (cargosData && cargosData.length ? cargosData : [{}]).forEach(renderCargo);
        (locaisData && locaisData.length ? locaisData : [{}]).forEach(renderLocal);
        (isencoesData && isencoesData.length ? isencoesData : [{}]).forEach(renderIsencao);
        (documentosExigidosData && documentosExigidosData.length ? documentosExigidosData : [{}]).forEach(renderDocumentoExigido);

        document.getElementById('adicionar-fase').addEventListener('click', () => renderFase());
        document.getElementById('adicionar-cargo').addEventListener('click', () => renderCargo());
        document.getElementById('adicionar-local').addEventListener('click', () => renderLocal());
        document.getElementById('adicionar-isencao').addEventListener('click', () => renderIsencao());
        document.getElementById('adicionar-documento-exigido').addEventListener('click', () => renderDocumentoExigido({ obrigatorio: true }));
    });
</script>