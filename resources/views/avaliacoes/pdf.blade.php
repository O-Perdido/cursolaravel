<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Avaliação de Estágio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #102e6c;
            padding: 10px;
        }

        .page {
            max-width: 210mm;
            margin: 0 auto;
        }

        /* Cabeçalho */
        .header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dfe6f3;
        }

        .header-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 2px;
            text-transform: uppercase;
            color: #102e6c;
            letter-spacing: 0.2px;
        }

        .header-subtitle {
            font-size: 11px;
            color: #19b755;
            margin-bottom: 7px;
            font-weight: 600;
        }

        /* Infos em linha */
        .header-info {
            font-size: 11px;
            color: #0a1f4d;
            display: grid;
            grid-template-columns: repeat(3, auto);
            justify-content: center;
            align-items: center;
            column-gap: 16px;
            row-gap: 4px;
            padding: 4px 0;
        }

        .header-info span {
            font-weight: 700;
            color: #102e6c;
        }

        /* Seção */
        .section {
            margin-bottom: 10px;
        }

        .section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #ffffff;
            background: #102e6c;
            padding: 7px 12px;
            margin-bottom: 9px;
            letter-spacing: 0.4px;
            border-radius: 4px;
            border-left: 4px solid #ecd00b;
        }

        /* Grid de Dados (tabela para compatibilidade no PDF) */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 11px;
        }

        .info-table td {
            width: 50%;
            vertical-align: top;
            padding: 6px 8px;
            border: 1px solid #dfe6f3;
            background: #f7f9fc;
        }

        .info-label {
            font-weight: 700;
            color: #102e6c;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin-bottom: 3px;
            display: block;
        }

        .info-value {
            color: #0a1f4d;
            word-break: break-word;
            font-size: 12px;
            font-weight: 600;
        }

        /* Questões */
        .questoes-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .questao-bloco {
            border: 1px solid #dfe6f3;
            border-left: 3px solid #102e6c;
            padding: 9px;
            page-break-inside: avoid;
            background: #ffffff;
            border-radius: 4px;
        }

        .questao-numero {
            background: #102e6c;
            color: #ffffff;
            display: inline-block;
            width: 24px;
            height: 24px;
            text-align: center;
            line-height: 24px;
            border-radius: 5px;
            font-weight: 700;
            font-size: 11px;
            margin-bottom: 6px;
        }

        .questao-texto {
            font-weight: 700;
            font-size: 12.5px;
            color: #0a1f4d;
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .resposta-titulo {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #19b755;
            margin-bottom: 4px;
            letter-spacing: 0.2px;
        }

        .resposta-conteudo {
            background: #f9fafc;
            padding: 8px;
            border: 1px solid #e6ecf5;
            border-left: 3px solid #19b755;
            border-radius: 4px;
            font-size: 12px;
            line-height: 1.5;
            color: #0a1f4d;
        }

        /* Respostas */
        .resposta-vazia {
            color: #7f8c8d;
            font-style: italic;
            font-size: 11px;
        }

        .resposta-escala {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .badge {
            background: #102e6c;
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge.muito-ruim { background: #c0392b; }
        .badge.ruim { background: #e67e22; }
        .badge.regular { background: #ecd00b; color: #102e6c; }
        .badge.bom { background: #19b755; }
        .badge.muito-bom { background: #148a3b; }

        .rating {
            font-weight: 700;
            color: #102e6c;
            font-size: 12px;
        }

        /* Rodapé */
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #dfe6f3;
            font-size: 9px;
            color: #0a1f4d;
            text-align: center;
            line-height: 1.4;
        }

        @media print {
            body {
                padding: 6px;
            }
            .page {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="header-title">Relatório de Avaliação de Desempenho</div>
            <div class="header-subtitle">Termo de Estágio Nº {{ $avaliacao->termo->numero_termo }}/{{ $avaliacao->termo->ano_termo }}</div>
            <div class="header-info">
                <div><span>Status:</span> 
                    @if ($avaliacao->status === 'respondida')
                        ✓ Respondida
                    @elseif ($avaliacao->status === 'revisada')
                        ✓ Revisada
                    @else
                        Pendente
                    @endif
                </div>
                <div><span>Tipo:</span> 
                    @if ($avaliacao->tipo_avaliacao === 'seis_meses')
                        Avaliação 6 Meses
                    @else
                        Avaliação de Finalização
                    @endif
                </div>
                @if ($avaliacao->respondida_em)
                    <div><span>Data:</span> {{ $avaliacao->respondida_em->format('d/m/Y H:i') }}</div>
                @endif
            </div>
        </div>

        <!-- Dados Principais -->
        <div class="section">
            <div class="section-label">Informações do Estágio</div>
            <table class="info-table">
                <tr>
                    <td>
                        <span class="info-label">Estagiário</span>
                        <div class="info-value">{{ optional($avaliacao->termo->estagiario)->nome_estagiario ?? '-' }}</div>
                    </td>
                    <td>
                        <span class="info-label">Empresa</span>
                        <div class="info-value">{{ optional($avaliacao->termo->empresa)->nome_empresa ?? '-' }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="info-label">Supervisor</span>
                        <div class="info-value">{{ optional($avaliacao->supervisor)->nome ?? optional($avaliacao->termo->supervisor)->nome_supervisor ?? '-' }}</div>
                    </td>
                    <td>
                        <span class="info-label">Respondida por</span>
                        <div class="info-value">{{ $avaliacao->respondida_por ?? '-' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Questões e Respostas -->
        <div class="section">
            <div class="section-label">Questões e Respostas</div>
            <div class="questoes-container">
                @forelse(($avaliacao->questoes_respostas ?? []) as $index => $qr)
                    <div class="questao-bloco">
                        <div>
                            <span class="questao-numero">{{ $qr['ordem'] ?? ($index + 1) }}</span>
                        </div>
                        <div class="questao-texto">{{ $qr['questao'] ?? '' }}</div>
                        <div class="resposta-titulo">Resposta:</div>
                        <div class="resposta-conteudo">
                            @php
                                $resposta = $qr['resposta'] ?? '';
                                $respostaLower = strtolower(trim($resposta));
                                
                                $escalaNumericaMap = [
                                    '1' => ['classe' => 'muito-ruim', 'texto' => 'Muito Ruim'],
                                    '2' => ['classe' => 'ruim', 'texto' => 'Ruim'],
                                    '3' => ['classe' => 'regular', 'texto' => 'Regular'],
                                    '4' => ['classe' => 'bom', 'texto' => 'Bom'],
                                    '5' => ['classe' => 'muito-bom', 'texto' => 'Muito Bom'],
                                ];
                                
                                $escalaTextoMap = [
                                    'muito ruim' => ['classe' => 'muito-ruim', 'texto' => 'Muito Ruim', 'valor' => 1],
                                    'ruim' => ['classe' => 'ruim', 'texto' => 'Ruim', 'valor' => 2],
                                    'regular' => ['classe' => 'regular', 'texto' => 'Regular', 'valor' => 3],
                                    'bom' => ['classe' => 'bom', 'texto' => 'Bom', 'valor' => 4],
                                    'muito bom' => ['classe' => 'muito-bom', 'texto' => 'Muito Bom', 'valor' => 5],
                                    'excelente' => ['classe' => 'muito-bom', 'texto' => 'Excelente', 'valor' => 5],
                                    'péssimo' => ['classe' => 'muito-ruim', 'texto' => 'Péssimo', 'valor' => 1],
                                    'ótimo' => ['classe' => 'muito-bom', 'texto' => 'Ótimo', 'valor' => 5],
                                ];
                                
                                $isEscala = false;
                                $escalaInfo = null;
                                $valor = null;
                                
                                if (is_numeric($resposta) && $resposta >= 1 && $resposta <= 5) {
                                    $isEscala = true;
                                    $escalaInfo = $escalaNumericaMap[(string)$resposta];
                                    $valor = (int)$resposta;
                                } else {
                                    foreach ($escalaTextoMap as $key => $info) {
                                        if (str_contains($respostaLower, $key)) {
                                            $isEscala = true;
                                            $escalaInfo = $info;
                                            $valor = $info['valor'];
                                            break;
                                        }
                                    }
                                }
                            @endphp

                            @if (!empty($resposta))
                                @if ($isEscala && $escalaInfo)
                                    <div class="resposta-escala">
                                        <span class="badge {{ $escalaInfo['classe'] }}">{{ $escalaInfo['texto'] }}</span>
                                        <span class="rating">{{ $valor }}/5</span>
                                    </div>
                                @else
                                    {{ $resposta }}
                                @endif
                            @else
                                <span class="resposta-vazia">Sem resposta</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: #999; padding: 20px;">
                        Nenhuma questão registrada
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Rodapé -->
        <div class="footer">
            <div>Documento gerado automaticamente pelo Sistema SIGE</div>
            <div>{{ now()->format('d/m/Y H:i') }} • ID: {{ $avaliacao->id_avaliacao }}</div>
        </div>
    </div>
</body>
</html>
