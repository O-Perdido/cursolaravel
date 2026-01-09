@extends('layouts.main')

@section('title', 'Editar Folha de Pagamento')

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
        .modal-desconto {
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content-desconto {
            background: #fff;
            padding: 20px 20px 15px 20px;
            border-radius: 8px;
            min-width: 250px;
            box-shadow: 0 2px 10px #0002;
        }
    </style>

    <!-- Botão Voltar e Salvar no topo -->
    <div class="mb-3 d-flex justify-content-between">
        <button onclick="window.NavigationHistory?.goBack('{{ route('folhas.index') }}')" class="btn btn-secondary"
            title="Voltar para a página anterior com filtros preservados">
            <i class="fas fa-arrow-left"></i> Voltar
        </button>
        <button type="button" id="btn-salvar-folha-topo" class="btn btn-primary">
            <i class="fas fa-save"></i> Salvar
        </button>
    </div>

    <!-- Formulário de edição de folha de pagamento -->
    <form id="form-editar-folha" action="{{ route('folhas.update', $folha->id_folha_pagamento) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <h1>Editar Folha de Pagamento -
                    {{ $folha->numero_folha }}/{{ \Carbon\Carbon::parse($folha->data_folha)->format('Y') }}
                </h1>
                <h5><span style="font-weight: bold;">Cliente:</span> {{ $folha->empresa->nome_empresa }}</h5>
                <p>
                    <span style="font-weight: bold;">CNPJ:</span> {{ $folha->empresa->numero_cnpj }} <br>
                    <span style="font-weight: bold;">Endereço:</span> {{ $folha->empresa->endereco }},
                    {{ $folha->empresa->numero_endereco }},
                    {{ $folha->empresa->complemento_endereco ? $folha->empresa->complemento_endereco . ', ' : '' }}{{ $folha->empresa->bairro }},
                    {{  $folha->empresa->cidade->nm_cidade }}, {{ $folha->empresa->cidade->estado->uf_estado }}
                </p>


            </div>
            <div class="col-md-6">
                <!-- Conteúdo da segunda coluna -->
                <h2 style="text-align: right;">Mês Referência:
                    {{ $folha->getMesReferenciaFormatado() }}/{{ $folha->ano_referencia }}
                </h2>
                <p style="text-align: right;">
                    Data da Fatura: {{ \Carbon\Carbon::parse($folha->data_folha)->format('d/m/Y') }} <br>
                    <span style="font-weight: bold;">Data de Vencimento:
                        {{ \Carbon\Carbon::parse($folha->vencimento_folha)->format('d/m/Y') }}</span>
                </p>
                <p style="text-align: right;">
                    <span style="font-weight: bold;">Tipo de Cálculo de Auxílio Transporte:</span>
                    {{ $folha->tipo_calculo_auxilio_transporte === 'diario' ? 'Diário' : 'Mensal' }} <br>
                    <span style="font-weight: bold;">Dias Úteis no Mês:</span> {{ $folha->dias_uteis }}
                </p>
                <p style="text-align: right;">
                    <span style="font-weight: bold;">Tipo de Cálculo de Recesso:</span>
                    {{ $folha->tipo_calculo_recesso === 'com_saldo' ? 'Com Saldo de Recesso' : 'Original' }}
                </p>
                <p style="text-align: right;">
                    <span style="font-weight: bold;">Local:</span>
                    {{ $folha->local ? $folha->local->descricao : 'Todos os locais / Não especificado' }}
                </p>
            </div>
        </div>

        <h5 style="text-align: center; margin-top: 15px;"> FATURA
            {{ $folha->numero_folha }}/{{ Carbon\Carbon::parse($folha->data_folha)->format('Y') }} - DESCRITIVO VALORES
            FOLHA
            ESTAGIÁRIOS
        </h5>
        <div>
            <table class="table">
                <thead style="font-size: small; background-color: #e9ecef;">
                    <tr style="text-align: center; vertical-align: middle;">
                        <th style="background-color: #e9ecef;">Estagiário(a)</th>
                        <th style="text-align: center; background-color: #e9ecef;">Dias Trabalhados</th>
                        <th style="text-align: center; background-color: #e9ecef;">Bolsa (R$)</th>
                        <th style="text-align: center; background-color: #e9ecef;">Aux. Transp. (R$)</th>
                        <th style="text-align: center; background-color: #e9ecef;">Bolsa Mês (R$)</th>
                        <th style="text-align: center; background-color: #e9ecef;">Aux. Transp. Mês (R$)</th>
                        <th style="text-align: center; background-color: #e9ecef;">Recesso (R$)</th>
                        <th style="text-align: center; background-color: #e9ecef;">Taxa Adm (R$)</th>
                        <th style="text-align: center; background-color: #e9ecef;">Inicio Contrato</th>
                        <th style="text-align: center; background-color: #e9ecef;">Fim Contrato</th>
                        <th style="text-align: center; background-color: #e9ecef;">Acertos (R$)</th>
                        <th style="text-align: center; background-color: #e9ecef;">Total (R$)</th>
                        <th style="text-align: center; background-color: #e9ecef;">Status</th>
                    </tr>
                </thead>
                <tbody style="font-size: small;">
                    @php
                        $conteudoFolha = $conteudoFolha->sortBy(function ($item) {
                            $nome = $item->termo->estagiario->nome_estagiario;
                            // Remove acentos para ordenação
                            $nome = mb_strtoupper($nome, 'UTF-8');
                            $nome = str_replace(
                                ['Á', 'À', 'Â', 'Ã', 'Ä', 'É', 'È', 'Ê', 'Ë', 'Í', 'Ì', 'Î', 'Ï', 'Ó', 'Ò', 'Ô', 'Õ', 'Ö', 'Ú', 'Ù', 'Û', 'Ü', 'Ç'],
                                ['A', 'A', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'C'],
                                $nome
                            );
                            return $nome;
                        })->values();
                    @endphp
                    @foreach($conteudoFolha as $conteudo)
                        <tr style="vertical-align: middle;">
                            <td>{{ $conteudo->termo->estagiario->nome_estagiario }}</td>
                            <td style="text-align: center; vertical-align: middle;">
                                <div style="display: flex; justify-content: center; align-items: center; height: 100%;">
                                    <input type="number" class="form-control" name="dias_trabalhados_{{ $conteudo->id }}"
                                        value="{{ $conteudo->dias_trabalhados }}"
                                        style="width: 60px; height: 30px; text-align: center; appearance: textfield;" min="0"
                                        max="{{ $diasPadraoCalculo ?? 30 }}"
                                        oninput="if(this.value > {{ $diasPadraoCalculo ?? 30 }}) this.value={{ $diasPadraoCalculo ?? 30 }}; if(this.value < 0) this.value=0;">
                                </div>
                            </td>
                            <td style="text-align: center;">
                                {{ number_format($conteudo->termo->valor_bolsa, 2, ',', '.') }}
                            </td>
                            <td style="text-align: center;">
                                {{ number_format($conteudo->termo->auxilio_transporte, 2, ',', '.') }}
                            </td>
                            <td style="text-align: center;">
                                <span id="bolsa_mes_{{ $conteudo->id }}"></span>
                                <input type="hidden" name="bolsa_mes_{{ $conteudo->id }}"
                                    id="input_bolsa_mes_{{ $conteudo->id }}" value="">
                            </td>
                            <td style="text-align: center;">

                                <span id="auxilio_transporte_mes_{{ $conteudo->id }}"></span>
                                <input type="hidden" name="auxilio_transporte_mes_{{ $conteudo->id }}"
                                    id="input_auxilio_transporte_mes_{{ $conteudo->id }}" value="">
                            </td>
                            <td style="text-align: center;">
                                {{ number_format($conteudo->valor_recesso, 2, ',', '.') }}
                            </td>
                            <td style="text-align: center;">

                                <span id="taxa_adm_{{ $conteudo->id }}"></span>
                                <input type="hidden" name="taxa_adm_{{ $conteudo->id }}" id="input_taxa_adm_{{ $conteudo->id }}"
                                    value="">
                            </td>
                            <td style="text-align: center;">
                                {{ \Carbon\Carbon::parse($conteudo->termo->data_inicio_estagio)->format('d/m/Y') }}
                            </td>
                            <td style="text-align: center;">
                                {{ \Carbon\Carbon::parse($conteudo->termo->data_fim_estagio)->format('d/m/Y') }}
                            </td>
                            <td style="text-align: center;">
                                <span id="descontos_span_{{ $conteudo->id }}" style="cursor:pointer; color: #0d6efd;"
                                    title="Clique para editar">
                                    {{ number_format($conteudo->descontos, 2, ',', '.') }}
                                </span>
                                <input type="hidden" name="descontos_{{ $conteudo->id }}"
                                    id="input_descontos_{{ $conteudo->id }}"
                                    value="{{ number_format($conteudo->descontos, 2, '.', '') }}">
                                <!-- Modal de edição de desconto -->
                                <div id="modal_descontos_{{ $conteudo->id }}" class="modal-desconto" style="display:none;">
                                    <div class="modal-content-desconto">
                                        <span class="close-desconto" id="close_descontos_{{ $conteudo->id }}"
                                            style="float:right; cursor:pointer;">&times;</span>
                                        <h5>Editar valor de acerto</h5>
                                        <input class="descontos_folha" type="number" step="0.01"
                                            id="input_modal_descontos_{{ $conteudo->id }}"
                                            style="width:100%; margin-bottom:10px;">
                                        <button type="button" class="btn btn-primary btn-sm"
                                            id="btn_salvar_descontos_{{ $conteudo->id }}">Salvar</button>
                                    </div>
                                </div>
                                <script>
                                    (function () {
                                        const span = document.getElementById('descontos_span_{{ $conteudo->id }}');
                                        const input = document.getElementById('input_descontos_{{ $conteudo->id }}');
                                        const modal = document.getElementById('modal_descontos_{{ $conteudo->id }}');
                                        const closeBtn = document.getElementById('close_descontos_{{ $conteudo->id }}');
                                        const inputModal = document.getElementById('input_modal_descontos_{{ $conteudo->id }}');
                                        const btnSalvar = document.getElementById('btn_salvar_descontos_{{ $conteudo->id }}');

                                        span.addEventListener('click', function () {
                                            inputModal.value = parseFloat(input.value || 0).toFixed(2);
                                            modal.style.display = 'flex';
                                            setTimeout(() => inputModal.select(), 100);
                                        });

                                        closeBtn.addEventListener('click', function () {
                                            modal.style.display = 'none';
                                        });

                                        btnSalvar.addEventListener('click', function () {
                                            // aceita sinal negativo, trata vírgula como separador decimal
                                            let raw = String(inputModal.value || '').trim().replace(',', '.');
                                            let valor = parseFloat(raw);
                                            if (!Number.isFinite(valor)) valor = 0; // fallback quando input vazio ou inválido
                                            span.textContent = valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                            input.value = valor.toFixed(2);
                                            modal.style.display = 'none';
                                            input.dispatchEvent(new Event('input'));
                                            if (typeof atualizarTotais === 'function') atualizarTotais();
                                        });

                                        // Fecha modal ao clicar fora
                                        modal.addEventListener('click', function (e) {
                                            if (e.target === modal) modal.style.display = 'none';
                                        });

                                        // Enter para salvar
                                        inputModal.addEventListener('keydown', function (e) {
                                            if (e.key === 'Enter') btnSalvar.click();
                                        });
                                    })();
                                </script>
                            </td>
                            <td style="text-align: center; font-weight: bold;">
                                <span id="total_{{ $conteudo->id }}">0,00</span>
                                <input type="hidden" name="total_{{ $conteudo->id }}" id="input_total_{{ $conteudo->id }}"
                                    value="0.00">
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                @if ($conteudo->termo->rescisao)
                                    <span class="badge bg-danger">R</span>
                                @else
                                    <span class="badge bg-success">A</span>
                                @endif
                            </td>
                        </tr>
                        <script>
                            (function () {
                                const diasInput = document.querySelector('input[name="dias_trabalhados_{{ $conteudo->id }}"]');
                                const bolsaMesSpan = document.getElementById('bolsa_mes_{{ $conteudo->id }}');
                                const bolsaMesInput = document.getElementById('input_bolsa_mes_{{ $conteudo->id }}');
                                const auxTranspMesSpan = document.getElementById('auxilio_transporte_mes_{{ $conteudo->id }}');
                                const auxTranspMesInput = document.getElementById('input_auxilio_transporte_mes_{{ $conteudo->id }}');
                                const taxaAdmSpan = document.getElementById('taxa_adm_{{ $conteudo->id }}');
                                const taxaAdmInput = document.getElementById('input_taxa_adm_{{ $conteudo->id }}');
                                const descontosInput = document.getElementById('input_descontos_{{ $conteudo->id }}');
                                const totalSpan = document.getElementById('total_{{ $conteudo->id }}');
                                const totalInput = document.getElementById('input_total_{{ $conteudo->id }}');
                                const valorBolsa = @json($conteudo->termo->valor_bolsa);
                                const valorAuxTransp = @json($conteudo->termo->auxilio_transporte);
                                const tipoTaxa = @json($conteudo->termo->empresa->tipo_taxa);
                                const taxaFixa = @json($conteudo->termo->empresa->taxa_fixa);
                                const taxaPercentual = @json($conteudo->termo->empresa->taxa_percentual);
                                const tipoCalculoAuxTransp = @json($folha->tipo_calculo_auxilio_transporte);
                                const diasUteis = @json($folha->dias_uteis);
                                const diasPadraoCalculo = @json($diasPadraoCalculo ?? 30);

                                function updateValores() {
                                    let dias = parseInt(diasInput.value) || 0;
                                    let bolsaMes = (Number(valorBolsa) / diasPadraoCalculo) * dias;
                                    let auxMes;
                                    if (tipoCalculoAuxTransp === 'diario') {
                                        auxMes = Number(valorAuxTransp) * diasUteis;
                                    } else {
                                        auxMes = (Number(valorAuxTransp) / diasPadraoCalculo) * dias;
                                    }
                                    bolsaMesSpan.textContent = bolsaMes.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    auxTranspMesSpan.textContent = auxMes.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    bolsaMesInput.value = bolsaMes.toFixed(2);
                                    auxTranspMesInput.value = auxMes.toFixed(2);

                                    // Lógica para taxa administrativa
                                    let valorTaxa = 0;
                                    if (tipoTaxa === 'fixa') {
                                        valorTaxa = Number(taxaFixa);
                                    } else if (tipoTaxa === 'percentual') {
                                        valorTaxa = (Number(taxaPercentual) / 100) * bolsaMes;
                                    }
                                    taxaAdmSpan.textContent = valorTaxa.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    taxaAdmInput.value = valorTaxa.toFixed(2);

                                    // Cálculo do total individual da linha (agora incluindo recesso)
                                    let descontos = parseFloat(descontosInput.value) || 0;
                                    let valorRecesso = Number({{ $conteudo->valor_recesso }}) || 0;
                                    let total = bolsaMes + auxMes + valorRecesso + descontos;
                                    totalSpan.textContent = total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    totalInput.value = total.toFixed(2);

                                    // Atualiza totais gerais
                                    if (typeof atualizarTotais === 'function') atualizarTotais();
                                }

                                diasInput.addEventListener('input', updateValores);
                                descontosInput.addEventListener('input', updateValores);
                                // Caso descontos seja alterado pelo modal, dispare o evento input
                                descontosInput.addEventListener('change', updateValores);

                                // Atualiza ao carregar
                                updateValores();
                            })();
                        </script>
                    @endforeach
                </tbody>
                <tfoot style="background-color: #e9ecef; font-weight: bold; border-top: 2px solid #dee2e6;">
                    <tr>
                        <td colspan="2" style="text-align: center; background-color: #e9ecef;">Total de Estagiários:
                            {{ $conteudoFolha->count() }}
                        </td>
                        <td style="text-align: center; background-color: #e9ecef;">Totais:</td>
                        <td style="text-align: center; background-color: #e9ecef;">R$</td>
                        <td style="text-align: center; background-color: #e9ecef;">
                            <span id="total_bolsa_mes">0,00</span>
                            <input type="hidden" name="total_bolsa_mes" id="input_total_bolsa_mes" value="0">
                        </td>
                        <td style="text-align: center; background-color: #e9ecef;">
                            <span id="total_auxilio_transporte_mes">0,00</span>
                            <input type="hidden" name="total_auxilio_transporte_mes" id="input_total_auxilio_transporte_mes"
                                value="0">
                        </td>
                        <td style="text-align: center; background-color: #e9ecef;">
                            {{ number_format($conteudoFolha->sum('valor_recesso'), 2, ',', '.') }}
                        </td>
                        <td style="text-align: center; background-color: #e9ecef;">
                            <span id="total_taxa_adm">0,00</span>
                            <input type="hidden" name="total_taxa_adm" id="input_total_taxa_adm" value="0">
                        </td>
                        <td colspan="2" style="text-align: right; background-color: #e9ecef;"></td>
                        <td style="text-align: center; background-color: #e9ecef;">
                            <span id="total_descontos">0,00</span>
                            <input type="hidden" name="total_descontos" id="input_total_descontos" value="0">
                        </td>
                        <td style="text-align: right; background-color: #e9ecef;">R$</td>
                        <td style="text-align: center; background-color: #e9ecef;">
                            <span id="total_geral">0,00</span>
                            <input type="hidden" name="total_geral" id="input_total_geral" value="0">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="13"
                            style="text-align: center; background-color: #e9ecef; font-weight: bold; font-size: 16px;">
                            TOTAL GERAL (Valor Bolsa + Taxa ADM): R$ <span id="total_geral_destaque">0,00</span>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <!-- Botões para voltar e salvar -->
            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('folhas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="button" id="btn-salvar-folha" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar Folha de Pagamento
                </button>
            </div>

            <!-- Modal de progresso -->
            <div id="modal-progresso"
                style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
                <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; text-align: center;">
                    <h4>Atualizando Folha de Pagamento</h4>
                    <div style="margin: 20px 0;">
                        <div style="width: 100%; background: #e0e0e0; border-radius: 10px; overflow: hidden;">
                            <div id="barra-progresso"
                                style="width: 0%; height: 30px; background: #4CAF50; transition: width 0.3s; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                0%
                            </div>
                        </div>
                    </div>
                    <p id="texto-progresso">Preparando...</p>
                    <small id="detalhes-progresso" style="color: #666;">Aguarde...</small>
                </div>
            </div>

            <script>
                function salvarFolha() {
                    const btnSalvar = document.getElementById('btn-salvar-folha');
                    const btnSalvarTopo = document.getElementById('btn-salvar-folha-topo');
                    btnSalvar.disabled = true;
                    btnSalvarTopo.disabled = true;

                    const modal = document.getElementById('modal-progresso');
                    const barra = document.getElementById('barra-progresso');
                    const texto = document.getElementById('texto-progresso');
                    const detalhes = document.getElementById('detalhes-progresso');

                    modal.style.display = 'flex';

                    (async function () {
                        try {
                            // Coletar todos os dados
                            const todosRegistros = [];
                            @foreach($conteudoFolha as $conteudo)
                                todosRegistros.push({
                                    id: {{ $conteudo->id }},
                                    dias_trabalhados: document.getElementById('input_total_{{ $conteudo->id }}') ?
                                        (parseFloat(document.querySelector('input[name="dias_trabalhados_{{ $conteudo->id }}"]').value) || 0) : 0,
                                    valor_bolsa_mes: parseFloat(document.getElementById('input_bolsa_mes_{{ $conteudo->id }}').value) || 0,
                                    valor_auxilio_transporte_mes: parseFloat(document.getElementById('input_auxilio_transporte_mes_{{ $conteudo->id }}').value) || 0,
                                    taxa_adm: parseFloat(document.getElementById('input_taxa_adm_{{ $conteudo->id }}').value) || 0,
                                    descontos: parseFloat(document.getElementById('input_descontos_{{ $conteudo->id }}').value) || 0,
                                    total: parseFloat(document.getElementById('input_total_{{ $conteudo->id }}').value) || 0
                                });
                            @endforeach

                                                        const totalRegistros = todosRegistros.length;
                            const TAMANHO_LOTE = 50; // Envia 50 registros por vez
                            const totalLotes = Math.ceil(totalRegistros / TAMANHO_LOTE);

                            texto.textContent = `Atualizando ${totalRegistros} registros em ${totalLotes} lote(s)...`;

                            // Enviar em lotes
                            for (let i = 0; i < totalLotes; i++) {
                                const inicio = i * TAMANHO_LOTE;
                                const fim = Math.min(inicio + TAMANHO_LOTE, totalRegistros);
                                const lote = todosRegistros.slice(inicio, fim);

                                detalhes.textContent = `Lote ${i + 1} de ${totalLotes} (${fim}/${totalRegistros} registros)`;

                                const response = await fetch('{{ route("folhas.storeallBatch", $folha->id_folha_pagamento) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        registros: lote
                                    })
                                });

                                if (!response.ok) {
                                    throw new Error(`Erro no lote ${i + 1}`);
                                }

                                const progresso = Math.round((fim / totalRegistros) * 90); // 90% para os lotes
                                barra.style.width = progresso + '%';
                                barra.textContent = progresso + '%';
                            }

                            // Finalizar com os totais
                            texto.textContent = 'Finalizando...';
                            detalhes.textContent = 'Salvando totais da folha';

                            const responseFinal = await fetch('{{ route("folhas.finalize", $folha->id_folha_pagamento) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    total_bolsa_mes: parseFloat(document.getElementById('input_total_bolsa_mes').value) || 0,
                                    total_auxilio_transporte_mes: parseFloat(document.getElementById('input_total_auxilio_transporte_mes').value) || 0,
                                    total_taxa_adm: parseFloat(document.getElementById('input_total_taxa_adm').value) || 0,
                                    total_geral: parseFloat(document.getElementById('input_total_geral').value) || 0
                                })
                            });

                            if (!responseFinal.ok) {
                                throw new Error('Erro ao finalizar folha');
                            }

                            const resultado = await responseFinal.json();

                            barra.style.width = '100%';
                            barra.textContent = '100%';
                            texto.textContent = 'Concluído!';
                            detalhes.textContent = `${totalRegistros} registros atualizados com sucesso`;

                            setTimeout(() => {
                                window.location.href = resultado.redirect_url;
                            }, 1000);

                        } catch (error) {
                            console.error(error);
                            alert('Erro ao atualizar a folha de pagamento: ' + error.message);
                            modal.style.display = 'none';
                            btnSalvar.disabled = false;
                            btnSalvarTopo.disabled = false;
                        }
                    })();
                }

                document.getElementById('btn-salvar-folha').addEventListener('click', salvarFolha);
                document.getElementById('btn-salvar-folha-topo').addEventListener('click', salvarFolha);
            </script>

        </div>

        <script>
            function atualizarTotais() {
                let totalBolsa = 0;
                let totalAuxTransp = 0;
                let totalTaxaAdm = 0;
                let totalDescontos = 0;
                let totalGeral = 0;
                let totalGeralDestaque = 0;

                @foreach($conteudoFolha as $conteudo)
                    totalBolsa += Number(document.getElementById('input_bolsa_mes_{{ $conteudo->id }}').value) || 0;
                    totalAuxTransp += Number(document.getElementById('input_auxilio_transporte_mes_{{ $conteudo->id }}').value) || 0;
                    totalTaxaAdm += Number(document.getElementById('input_taxa_adm_{{ $conteudo->id }}').value) || 0;
                    totalDescontos += Number(document.getElementById('input_descontos_{{ $conteudo->id }}').value) || 0;
                    totalGeral += (
                        (Number(document.getElementById('input_bolsa_mes_{{ $conteudo->id }}').value) || 0) +
                        (Number(document.getElementById('input_auxilio_transporte_mes_{{ $conteudo->id }}').value) || 0) +
                        (Number({{ $conteudo->valor_recesso }}) || 0) +
                        (Number(document.getElementById('input_descontos_{{ $conteudo->id }}').value) || 0)
                    );
                    totalGeralDestaque += (
                        (Number(document.getElementById('input_bolsa_mes_{{ $conteudo->id }}').value) || 0) +
                        (Number(document.getElementById('input_auxilio_transporte_mes_{{ $conteudo->id }}').value) || 0) +
                        (Number({{ $conteudo->valor_recesso }}) || 0) +
                        (Number(document.getElementById('input_taxa_adm_{{ $conteudo->id }}').value) || 0) +
                        (Number(document.getElementById('input_descontos_{{ $conteudo->id }}').value) || 0)
                    );
                @endforeach

                document.getElementById('total_bolsa_mes').textContent = totalBolsa.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('input_total_bolsa_mes').value = totalBolsa.toFixed(2);

                document.getElementById('total_auxilio_transporte_mes').textContent = totalAuxTransp.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('input_total_auxilio_transporte_mes').value = totalAuxTransp.toFixed(2);

                document.getElementById('total_taxa_adm').textContent = totalTaxaAdm.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('input_total_taxa_adm').value = totalTaxaAdm.toFixed(2);

                document.getElementById('total_descontos').textContent = totalDescontos.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('input_total_descontos').value = totalDescontos.toFixed(2);

                document.getElementById('total_geral').textContent = totalGeral.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('input_total_geral').value = totalGeral.toFixed(2);

                // Atualiza o destaque
                document.getElementById('total_geral_destaque').textContent = totalGeralDestaque.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            // Chame atualizarTotais sempre que algum valor mudar
            @foreach($conteudoFolha as $conteudo)
                document.querySelector('input[name="dias_trabalhados_{{ $conteudo->id }}"]').addEventListener('input', atualizarTotais);
            @endforeach

            // Atualiza ao carregar a página
            window.addEventListener('DOMContentLoaded', atualizarTotais);
        </script>

    </form>

    <script>
        document.querySelector('form').addEventListener('keydown', function (e) {
            // Se pressionar Enter e não estiver em um textarea ou botão, previne o submit
            if (e.key === 'Enter' && e.target.type !== 'textarea' && e.target.type !== 'submit' && e.target.type !== 'button') {
                e.preventDefault();
                return false;
            }
        });
    </script>
@endsection