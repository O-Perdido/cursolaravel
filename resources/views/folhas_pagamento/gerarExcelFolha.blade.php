<table>
    <tr>
        <td colspan="13"><strong>Folha de Pagamento -
                {{ $folha->numero_folha }}/{{ \Carbon\Carbon::parse($folha->data_folha)->format('Y') }}</strong></td>
    </tr>
    <tr>
        <td colspan="7"><strong>Cliente:</strong> {{ $folha->empresa->nome_empresa }}</td>
        <td colspan="6"><strong>CNPJ:</strong> {{ $folha->empresa->numero_cnpj }}</td>
    </tr>
    <tr>
        <td colspan="13"><strong>Local:</strong>
            {{ $folha->local ? $folha->local->descricao : 'Todos os locais / Não especificado' }}</td>
    </tr>
    <tr>
        <td colspan="7">
            <strong>Endereço:</strong> {{ $folha->empresa->endereco }},
            {{ $folha->empresa->numero_endereco }},
            {{ $folha->empresa->complemento_endereco ? $folha->empresa->complemento_endereco . ', ' : '' }}{{ $folha->empresa->bairro }},
            {{ $folha->empresa->cidade->nm_cidade }}, {{ $folha->empresa->cidade->estado->uf_estado }}
        </td>
        <td colspan="6">
            <strong>Mês Referência:</strong> {{ $folha->getMesReferenciaFormatado() }}/{{ $folha->ano_referencia }}<br>
            <strong>Data da Fatura:</strong> {{ \Carbon\Carbon::parse($folha->data_folha)->format('d/m/Y') }}<br>
            <strong>Data de Vencimento:</strong> {{ \Carbon\Carbon::parse($folha->vencimento_folha)->format('d/m/Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="13" style="text-align: right;">
            <span style="font-weight: bold;">Tipo de Cálculo de Auxílio Transporte: </span>
            {{ $folha->tipo_calculo_auxilio_transporte === 'diario' ? 'Diário' : 'Mensal' }}
            @if ($folha->tipo_calculo_auxilio_transporte === 'diario')
                <br>
                <span style="font-weight: bold;">Dias Úteis no Mês: </span> {{ $folha->dias_uteis }}
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="13"></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th width="35">Estagiário(a)</th>
            <th width="20">Local</th>
            <th width="10" style="text-align:center;">Dias Trab.</th>
            <th width="12" style="text-align:center;">Bolsa</th>
            <th width="12" style="text-align:center;">Aux. Transp.</th>
            <th width="14" style="text-align:center;">Bolsa Mês</th>
            <th width="14" style="text-align:center;">Aux. Transp. Mês</th>
            <th width="12" style="text-align:center;">Recesso</th>
            <th width="12" style="text-align:center;">Taxa Adm</th>
            <th width="16" style="text-align:center;">Inicio Contrato</th>
            <th width="16" style="text-align:center;">Fim Contrato</th>
            <th width="12" style="text-align:center;">Acertos</th>
            <th width="16" style="text-align:center;">Total</th>
            <th width="8" style="text-align:center;">Status</th>
        </tr>
    </thead>

    <tbody>
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
            <tr>
                <td style="white-space: normal; word-break: break-word;">{{ $conteudo->termo->estagiario->nome_estagiario }}
                </td>
                <td>{{ optional($conteudo->termo->local)->descricao }}</td>
                <td style="text-align:center;">{{ $conteudo->dias_trabalhados }}</td>
                <td style="text-align:center;">{{ number_format($conteudo->valor_bolsa, 2, ',', '.') }}</td>
                <td style="text-align:center;">{{ number_format($conteudo->valor_auxilio_transporte, 2, ',', '.') }}</td>
                <td style="text-align:center;">{{ number_format($conteudo->valor_bolsa_mes, 2, ',', '.') }}</td>
                <td style="text-align:center;">{{ number_format($conteudo->valor_auxilio_transporte_mes, 2, ',', '.') }}
                </td>
                <td style="text-align:center;">{{ number_format($conteudo->valor_recesso, 2, ',', '.') }}</td>
                <td style="text-align:center;">{{ number_format($conteudo->taxa_adm, 2, ',', '.') }}</td>
                <td style="text-align:center;">
                    {{ \Carbon\Carbon::parse($conteudo->termo->data_inicio_estagio)->format('d/m/Y') }}
                </td>
                <td style="text-align:center;">
                    {{ \Carbon\Carbon::parse($conteudo->termo->data_fim_estagio)->format('d/m/Y') }}
                </td>
                <td style="text-align:center;">{{ number_format($conteudo->descontos, 2, ',', '.') }}</td>
                <td style="text-align:center;">{{ number_format($conteudo->total, 2, ',', '.') }}</td>
                <td style="text-align:center;">
                    @if ($conteudo->termo->rescisao)
                        R
                    @else
                        A
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><strong>Total de Estagiários: {{ $conteudoFolha->count() }}</strong></td>
            <td colspan="2"><strong>Totais:</strong></td>
            <td style="text-align:center;">
                <strong>{{ number_format($conteudoFolha->sum('valor_bolsa_mes'), 2, ',', '.') }}</strong>
            </td>
            <td style="text-align:center;">
                <strong>{{ number_format($conteudoFolha->sum('valor_auxilio_transporte_mes'), 2, ',', '.') }}</strong>
            </td>
            <td style="text-align:center;">
                <strong>{{ number_format($conteudoFolha->sum('valor_recesso'), 2, ',', '.') }}</strong>
            </td>
            <td style="text-align:center;">
                <strong>{{ number_format($conteudoFolha->sum('taxa_adm'), 2, ',', '.') }}</strong>
            </td>
            <td colspan="2"></td>
            <td style="text-align:center;">
                <strong>{{ number_format($conteudoFolha->sum('descontos'), 2, ',', '.') }}</strong>
            </td>
            <td colspan="2" style="text-align:center;">
                <strong>{{ number_format($conteudoFolha->sum('total'), 2, ',', '.') }}</strong>
            </td>
        </tr>
        <tr>
            <td colspan="13" style="text-align: center; font-weight: bold; font-size: 12px;">
                TOTAL GERAL (Valor Bolsa + Taxa ADM): R$
                {{ number_format($conteudoFolha->sum('total') + $conteudoFolha->sum('taxa_adm'), 2, ',', '.') }}
            </td>
        </tr>
    </tfoot>
</table>