@extends('layouts.main')

@section('title', 'Folhas de Pagamento')

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('delete'))
        <div class="alert alert-danger">
            {{ session('delete') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <style>
        /* Botões de ação compactos */
        .btn-action {
            padding: 0.45rem 0.65rem;
            line-height: 1.1;
        }

        .btn-action i {
            font-size: 1.05rem;
        }

        .actions-cell {
            white-space: nowrap;
            text-align: center;
        }

        /* Select de itens por página inline */
        .per-page-selector {
            display: inline-block;
            width: auto;
            margin-left: 10px;
            font-size: 0.875rem;
        }

        /* Ocultar textos extras da paginação padrão */
        .pagination-info {
            display: none !important;
        }

        nav[role="navigation"]>p,
        nav[role="navigation"] .hidden,
        nav[aria-label="Pagination"]>p,
        nav[aria-label="Pagination"] .hidden {
            display: none !important;
        }

        nav[role="navigation"] {
            display: flex !important;
            justify-content: center !important;
        }

        nav[role="navigation"] ul.pagination {
            margin: 0 !important;
        }

        /* Cabeçalho sticky melhorado */
        .table thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 10;
        }

        .table thead {
            box-shadow: 0 2px 3px rgba(0, 0, 0, .05);
        }

        /* Botões de filtro maiores e consistentes */
        .filter-actions .btn {
            min-width: 120px;
        }

        /* Dropdown de empresas sobreposto corretamente */
        #empresa {
            background: #fff;
            z-index: 2000 !important;
        }
    </style>

    <h4 class="mb-3 d-flex align-items-center gap-2">
        <i class="fas fa-file-invoice-dollar text-primary"></i>
        Lista de Folhas de Pagamento
    </h4>
    <!-- Botão para abrir o modal -->
    @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#gerarFolhaModal">
            Gerar Folha de Pagamento
        </button>

        <!-- Modal -->
        <div class="modal fade" id="gerarFolhaModal" tabindex="-1" aria-labelledby="gerarFolhaModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('folhas.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="gerarFolhaModalLabel">Gerar uma nova Folha de Pagamento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3" style="position: relative;">
                                <label for="fk_id_empresa" class="form-label">Selecione a Unidade Concedente</label>
                                <input type="text" class="form-control" id="empresa_search" placeholder="Digite para buscar..."
                                    autocomplete="off">
                                <select class="form-control mt-2" id="fk_id_empresa" name="fk_id_empresa" size="5" required
                                    style="display:none; position: absolute; top: 60px; left: 0; width: 700px; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa->id_empresa }}">{{ $empresa->nome_empresa }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const searchInput = document.getElementById('empresa_search');
                                    const select = document.getElementById('fk_id_empresa');
                                    const options = Array.from(select.options);
                                    const localContainer = document.getElementById('local_container');
                                    const localSelect = document.getElementById('fk_id_local');
                                    searchInput.addEventListener('focus', function () {
                                        select.style.display = 'block';
                                        setTimeout(() => {
                                            searchInput.select();
                                        }, 0);
                                    });

                                    searchInput.addEventListener('input', function () {
                                        const value = this.value.toLowerCase();
                                        select.innerHTML = '';
                                        options.forEach(option => {
                                            if (option.text.toLowerCase().includes(value)) {
                                                select.appendChild(option.cloneNode(true));
                                            }
                                        });
                                        select.style.display = 'block';
                                    });

                                    select.addEventListener('change', function () {
                                        const selected = select.options[select.selectedIndex];
                                        searchInput.value = selected.text;
                                        select.style.display = 'none';

                                        // Ao selecionar a empresa, carrega os locais relacionados
                                        const empresaId = selected.value;
                                        if (!empresaId) {
                                            // Sem empresa, esconde e limpa os locais
                                            localSelect.innerHTML = '';
                                            localContainer.style.display = 'none';
                                            return;
                                        }

                                        // Busca locais por empresa via endpoint /locais?empresa={id}
                                        fetch(`/locais?empresa=${empresaId}`)
                                            .then(res => res.ok ? res.json() : [])
                                            .then(locais => {
                                                // Limpa e preenche o select de locais
                                                localSelect.innerHTML = '';
                                                // Opção vazia para considerar TODOS os locais (opcional)
                                                const optTodos = document.createElement('option');
                                                optTodos.value = '';
                                                optTodos.textContent = '— Todos os locais —';
                                                localSelect.appendChild(optTodos);

                                                if (Array.isArray(locais) && locais.length > 0) {
                                                    locais.forEach(local => {
                                                        const opt = document.createElement('option');
                                                        opt.value = local.id_local;
                                                        opt.textContent = local.descricao;
                                                        localSelect.appendChild(opt);
                                                    });
                                                    localContainer.style.display = 'block';
                                                } else {
                                                    // Nenhum local: mantém oculto
                                                    localContainer.style.display = 'none';
                                                }
                                            })
                                            .catch(() => {
                                                // Em caso de erro, oculta o campo
                                                localSelect.innerHTML = '';
                                                localContainer.style.display = 'none';
                                            });
                                    });

                                    document.addEventListener('click', function (e) {
                                        if (!searchInput.contains(e.target) && !select.contains(e.target)) {
                                            select.style.display = 'none';
                                        }
                                    });
                                });
                            </script>
                            @php
                                $meses = [
                                    1 => 'Janeiro',
                                    2 => 'Fevereiro',
                                    3 => 'Março',
                                    4 => 'Abril',
                                    5 => 'Maio',
                                    6 => 'Junho',
                                    7 => 'Julho',
                                    8 => 'Agosto',
                                    9 => 'Setembro',
                                    10 => 'Outubro',
                                    11 => 'Novembro',
                                    12 => 'Dezembro',
                                ];
                            @endphp

                            <div class="mb-3">
                                <label for="mes_referencia" class="form-label">Selecione o Mês de Referência</label>
                                <select class="form-control" id="mes_referencia" name="mes_referencia" required>
                                    @foreach($meses as $num => $nome)
                                        <option value="{{ $num }}">{{ $nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Campo opcional de Local (dependente da empresa selecionada) -->
                            <div class="mb-3" id="local_container" style="display: none;">
                                <label for="fk_id_local" class="form-label">Selecione o Local (opcional)</label>
                                <select class="form-control" id="fk_id_local" name="fk_id_local">
                                    <!-- Opções serão carregadas dinamicamente conforme a empresa selecionada -->
                                </select>
                                <small class="form-text text-muted">Se não selecionar, serão incluídos termos de todos os locais
                                    da unidade concedente.</small>
                            </div>
                            <div class="mb-3">
                                <label for="ano_referencia" class="form-label">Ano de Referência</label>
                                <select class="form-control" id="ano_referencia" name="ano_referencia" required>
                                    @php
                                        $anosModal = ($anosDisponiveis ?? collect([now()->year]));
                                    @endphp
                                    @foreach($anosModal as $ano)
                                        <option value="{{ $ano }}" {{ $loop->first ? 'selected' : '' }}>{{ $ano }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Campo para data de vencimento -->
                            <div class="mb-3">
                                <label for="vencimento_folha" class="form-label">Data de Vencimento</label>
                                <input type="date" class="form-control" id="vencimento_folha" name="vencimento_folha" required>
                            </div>
                            <!-- Campo para selecionar o tipo de cálculo de auxilio transporte (se é mensal ou diario), caso selecionar diario, mostra o campo de dias uteis -->
                            <div class="mb-3">
                                <label for="tipo_calculo_auxilio_transporte" class="form-label">Tipo de Cálculo de Auxílio
                                    Transporte</label>
                                <select class="form-control" id="tipo_calculo_auxilio_transporte"
                                    name="tipo_calculo_auxilio_transporte" required>
                                    <option value="mensal">Mensal</option>
                                    <option value="diario">Diário</option>
                                </select>
                            </div>
                            <div class="mb-3" id="dias_uteis_container" style="display: none;">
                                <label for="dias_uteis" class="form-label">Dias Úteis no Mês</label>
                                <input type="number" class="form-control" id="dias_uteis" name="dias_uteis" min="0" max="31"
                                    placeholder="Digite o número de dias úteis">
                            </div>
                            <!-- Campo para selecionar o tipo de cálculo de recesso
                                                                                                                                                                                                                                                                                                                                                                        - original: mantém a regra antiga (não considera saldo de recesso)
                                                                                                                                                                                                                                                                                                                                                                        - com_saldo: paga apenas os dias NÃO utilizados (saldo_recesso)
                                                                                                                                                                                                                                                                                                                                                                        Ex.: saldo_recesso=30 => paga 100%; saldo_recesso=0 => paga 0%; saldo_recesso=15 => paga 50% -->
                            <div class="mb-3">
                                <label class="form-label d-block">Tipo de Cálculo de Recesso</label>
                                <!-- Switch Bootstrap 5 para escolher o modo de cálculo
                                                                                                                                                                                                                                                                                                                                                                        - Desligado: original (não considera saldo)
                                                                                                                                                                                                                                                                                                                                                                        - Ligado: com_saldo (paga dias NÃO utilizados)
                                                                                                                                                                                                                                                                                                                                                                        -->
                                <div class="d-flex align-items-center">
                                    <label for="tipo_calculo_recesso_switch" class="me-2 mb-0">Cálculo Antigo</label>
                                    <div class="form-check form-switch m-0 mx-2 ps-0">
                                        <input style="cursor: pointer" class="form-check-input ms-0" type="checkbox"
                                            id="tipo_calculo_recesso_switch" aria-label="Alternar tipo de cálculo de recesso">
                                    </div>
                                    <label for="tipo_calculo_recesso_switch" class="ms-2 mb-0">Cálculo Novo</label>
                                </div>
                                <!-- Campo escondido enviado no POST com o valor efetivo -->
                                <input type="hidden" name="tipo_calculo_recesso" id="tipo_calculo_recesso" value="original">
                                <small class="form-text text-muted">
                                    <strong>Antigo:</strong> Calcula o recesso sem considerar o saldo disponível.<br>
                                    <strong>Novo:</strong> Paga os dias de recesso <u>não utilizados</u> (saldo_recesso),
                                    proporcionalmente ao direito.
                                </small>
                            </div>
                            <script>
                                document.getElementById('tipo_calculo_auxilio_transporte').addEventListener('change', function () {
                                    const diasUteisContainer = document.getElementById('dias_uteis_container');
                                    if (this.value === 'diario') {
                                        diasUteisContainer.style.display = 'block';
                                        // Tornar ele obrigatório se for diário
                                        document.getElementById('dias_uteis').setAttribute('required', 'required');
                                    } else {
                                        diasUteisContainer.style.display = 'none';
                                        // Remover a obrigatoriedade se não for diário
                                        document.getElementById('dias_uteis').removeAttribute('required');
                                    }
                                });

                                // Atualiza o campo hidden conforme o estado do switch
                                (function () {
                                    const sw = document.getElementById('tipo_calculo_recesso_switch');
                                    const hidden = document.getElementById('tipo_calculo_recesso');
                                    // Estado inicial: desligado => 'original'
                                    hidden.value = sw.checked ? 'com_saldo' : 'original';
                                    sw.addEventListener('change', function () {
                                        hidden.value = this.checked ? 'com_saldo' : 'original';
                                    });
                                })();
                            </script>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Gerar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Card de Filtro e Ações -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body pb-2 pt-3">
            <form method="GET" action="{{ route('folhas.index') }}">
                <div class="row g-3 align-items-end">
                    @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                        <div class="col-md-4" style="position: relative;">
                            <label for="empresa_search_filtro" class="form-label mb-1 fw-semibold">Unidade Concedente</label>
                            <input type="text" class="form-control form-control-sm" id="empresa_search_filtro"
                                placeholder="Digite para buscar..." autocomplete="off"
                                value="{{ $empresas->firstWhere('id_empresa', request('empresa'))?->nome_empresa }}">
                            <select name="empresa" id="empresa" class="form-select form-select-sm mt-2" size="5"
                                style="display:none; position:absolute; z-index:1050; background:#fff; border:1px solid #ced4da; width:700px;">
                                <option value="">Todas</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id_empresa }}" {{ request('empresa') == $empresa->id_empresa ? 'selected' : '' }}>
                                        {{ $empresa->nome_empresa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="localFilterCol" style="{{ request('empresa') ? '' : 'display:none;' }}">
                            <label for="local" class="form-label mb-1 fw-semibold">Local</label>
                            <select name="local" id="local" class="form-select form-select-sm">
                                <option value="">Todos</option>
                            </select>
                        </div>
                    @endif

                    <div class="col-md-2">
                        <label for="mes" class="form-label mb-1 fw-semibold">Mês Ref.</label>
                        <select name="mes" id="mes" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            @foreach(($meses ?? []) as $num => $nome)
                                <option value="{{ $num }}" {{ request('mes') == (string) $num ? 'selected' : '' }}>{{ $nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="ano" class="form-label mb-1 fw-semibold">Ano Ref.</label>
                        <select name="ano" id="ano" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            @foreach(($anosDisponiveis ?? []) as $ano)
                                <option value="{{ $ano }}" {{ request('ano') == (string) $ano ? 'selected' : '' }}>{{ $ano }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1 fw-semibold">Vencimento (Período)</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="vencimento_inicial" id="vencimento_inicial"
                                class="form-control form-control-sm" value="{{ request('vencimento_inicial') }}"
                                placeholder="Inicial">
                            <span class="input-group-text px-2">até</span>
                            <input type="date" name="vencimento_final" id="vencimento_final"
                                class="form-control form-control-sm" value="{{ request('vencimento_final') }}"
                                placeholder="Final">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1 fw-semibold">Emissão (Período)</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="emissao_inicial" id="emissao_inicial"
                                class="form-control form-control-sm" value="{{ request('emissao_inicial') }}"
                                placeholder="Inicial">
                            <span class="input-group-text px-2">até</span>
                            <input type="date" name="emissao_final" id="emissao_final" class="form-control form-control-sm"
                                value="{{ request('emissao_final') }}" placeholder="Final">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex justify-content-end align-items-end gap-2 filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                        <a href="{{ route('folhas.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-eraser me-1"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Topo: total e paginação -->
    <script>
        // Função global para alterar itens por página (igual termos)
        function changePerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page'); // Reset para página 1
            window.location.href = url.toString();
        }
    </script>
    @if (method_exists($folhas, 'links'))
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($folhas->total() > 0)
                        Mostrando {{ $folhas->firstItem() }}–{{ $folhas->lastItem() }} de {{ $folhas->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                @php $pp = request('per_page', '25'); @endphp
                <select id="perPageSelectorFolhasTop" class="form-select form-select-sm per-page-selector"
                    onchange="changePerPage(this.value)">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $folhas->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <div class="mb-2" style="font-weight:bold; font-size:1.05em;">Total de folhas:
        {{ method_exists($folhas, 'total') ? $folhas->total() : $folhas->count() }}
    </div>
    <div style="max-height: 600px; overflow-y: auto; border: 1px solid #ddd; padding-bottom: 10px; border-radius: 6px;">
        <table class="table">
            <thead>
                <tr style="vertical-align: middle;">
                    <th style="margin-left: 5px">Data</th>
                    <th style="width: 300px">Unidade concedente</th>
                    <th style="width: 250px">Local</th>
                    <th style="text-align: center;">Mês de Referência</th>
                    <th style="text-align: center; width: 150px">Total da Folha</th>
                    <th style="text-align: center; width: 150px">Total Taxa Administrativa</th>
                    <th style="text-align: center; width: 160px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Verifica se o usuario logado é admin ou operador, caso não seja nenhum dos dois, ou seja, é uma unidade Concedente, filtra as folhas pelo id da unidade concedente -->
                @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                    {{-- Mostra todas as folhas para admin e operador --}}
                    @php
                        $folhas = $folhas->sortByDesc('id_folha_pagamento');
                    @endphp
                @else
                    @php
                        $folhas = $folhas->where('fk_id_empresa', Auth::user()->fk_id_empresa)->sortByDesc('id_folha_pagamento'); // Filtra as folhas pela unidade concedente do usuário
                    @endphp
                @endif

                @foreach ($folhas as $folha)
                    @php
                        $mesesExtenso = [
                            1 => 'Janeiro',
                            2 => 'Fevereiro',
                            3 => 'Março',
                            4 => 'Abril',
                            5 => 'Maio',
                            6 => 'Junho',
                            7 => 'Julho',
                            8 => 'Agosto',
                            9 => 'Setembro',
                            10 => 'Outubro',
                            11 => 'Novembro',
                            12 => 'Dezembro',
                        ];
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($folha->data_folha)->format('d/m/Y') }}</td>
                        <td>{{ $folha->empresa->nome_empresa }}</td>
                        <td>
                            @if($folha->local)
                                <span class="badge bg-secondary" style="text-wrap: initial;">{{ $folha->local->descricao }}</span>
                            @else
                                <span class="text-muted">Todos / Não especificado</span>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $mesesExtenso[$folha->mes_referencia] }}/{{ $folha->ano_referencia }}
                        </td>
                        <td style="text-align: center;">R$ {{ number_format($folha->total_folha, 2, ',', '.') }}</td>
                        <td style="text-align: center;">R$ {{ number_format($folha->total_taxa_adm, 2, ',', '.') }}</td>
                        <td class="actions-cell">
                            <div class="btn-group" role="group" aria-label="Ações">
                                <a href="{{ route('folhas.show', $folha->id_folha_pagamento) }}"
                                    class="btn btn-outline-primary btn-action" data-bs-toggle="tooltip" title="Detalhes"
                                    aria-label="Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                                    <a href="{{ route('folha_pagamento.prepararRemessa', $folha->id_folha_pagamento) }}"
                                        class="btn btn-outline-success btn-action" data-bs-toggle="tooltip" title="Preparar Remessa"
                                        aria-label="Preparar Remessa">
                                        <i class="fas fa-file-export"></i>
                                    </a>
                                    <a href="{{ route('folhas.edit', $folha->id_folha_pagamento) }}"
                                        class="btn btn-outline-warning btn-action" data-bs-toggle="tooltip" title="Editar"
                                        aria-label="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-action" data-bs-toggle="modal"
                                        data-bs-target="#deleteFolhaModal{{ $folha->id_folha_pagamento }}" title="Excluir"
                                        aria-label="Excluir">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endif
                            </div>

                            <!-- Modal de confirmação -->
                            <div class="modal fade" id="deleteFolhaModal{{ $folha->id_folha_pagamento }}" tabindex="-1"
                                aria-labelledby="deleteFolhaModalLabel{{ $folha->id_folha_pagamento }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteFolhaModalLabel{{ $folha->id_folha_pagamento }}">
                                                Confirmar Exclusão
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Fechar"></button>
                                        </div>
                                        <div class="modal-body">
                                            Você tem certeza que deseja excluir esta folha de pagamento? Esta ação não poderá
                                            ser desfeita.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('folhas.destroy', $folha->id_folha_pagamento) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Excluir</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (method_exists($folhas, 'links'))
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($folhas->total() > 0)
                        Mostrando {{ $folhas->firstItem() }}–{{ $folhas->lastItem() }} de {{ $folhas->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                @php $pp = request('per_page', '25'); @endphp
                <select id="perPageSelectorFolhasBottom" class="form-select form-select-sm per-page-selector"
                    onchange="changePerPage(this.value)">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $folhas->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
    <script>
        function changePerPage(value) {
            try {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', value);
                url.searchParams.delete('page');
                window.location.assign(url.toString());
            } catch (e) { console.error('Erro ao mudar per_page', e); }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInputFiltro = document.getElementById('empresa_search_filtro');
            const selectFiltro = document.getElementById('empresa');
            if (searchInputFiltro && selectFiltro) {
                const originalOptions = Array.from(selectFiltro.options);

                function filtrar(texto) {
                    const value = texto.toLowerCase();
                    selectFiltro.innerHTML = '';
                    originalOptions.forEach(opt => {
                        if (opt.text.toLowerCase().includes(value)) {
                            selectFiltro.appendChild(opt.cloneNode(true));
                        }
                    });
                    selectFiltro.style.display = 'block';
                }

                searchInputFiltro.addEventListener('focus', () => {
                    filtrar(searchInputFiltro.value || '');
                    setTimeout(() => searchInputFiltro.select(), 0);
                });
                searchInputFiltro.addEventListener('input', () => filtrar(searchInputFiltro.value));
                selectFiltro.addEventListener('change', () => {
                    const sel = selectFiltro.options[selectFiltro.selectedIndex];
                    if (sel) searchInputFiltro.value = sel.text;
                    selectFiltro.style.display = 'none';
                    carregarLocais(sel?.value || '');
                });
                selectFiltro.addEventListener('click', e => {
                    if (e.target.tagName === 'OPTION') {
                        selectFiltro.value = e.target.value;
                        selectFiltro.dispatchEvent(new Event('change'));
                    }
                });
                document.addEventListener('mousedown', e => {
                    if (!searchInputFiltro.contains(e.target) && !selectFiltro.contains(e.target)) {
                        selectFiltro.style.display = 'none';
                    }
                });
                // Preencher inicialmente o campo se já houver seleção
                if (selectFiltro.value) {
                    const optSel = originalOptions.find(o => o.value === selectFiltro.value);
                    if (optSel) searchInputFiltro.value = optSel.text;
                }
            }

            function carregarLocais(empresaId) {
                const localSelect = document.getElementById('local');
                const localFilterCol = document.getElementById('localFilterCol');
                if (!localSelect) return;
                if (!empresaId) {
                    if (localFilterCol) localFilterCol.style.display = 'none';
                    localSelect.innerHTML = '<option value="">Todos</option>';
                    return;
                }
                if (localFilterCol) localFilterCol.style.display = '';
                fetch('/locais?empresa=' + empresaId)
                    .then(r => r.ok ? r.json() : [])
                    .then(locais => {
                        localSelect.innerHTML = '<option value="">Todos</option>';
                        locais.forEach(local => {
                            const opt = document.createElement('option');
                            opt.value = local.id_local;
                            opt.textContent = local.descricao;
                            if ('{{ request('local') }}' == local.id_local) opt.selected = true;
                            localSelect.appendChild(opt);
                        });
                    })
                    .catch(() => {
                        localSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                    });
            }
            const empresaSelecionada = document.getElementById('empresa')?.value;
            if (empresaSelecionada) carregarLocais(empresaSelecionada);

            try {
                if (window.bootstrap && bootstrap.Tooltip) {
                    [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(el => new bootstrap.Tooltip(el));
                }
            } catch (e) { console.warn('Tooltips não inicializados:', e); }
        });
    </script>
@endsection