<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Avaliação de Estágio #{{ $avaliacao->id_avaliacao }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #222;
            margin: 0;
            padding: 0;
        }
        h1, h2, h3 { margin: 0; padding: 0; }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
        }
        .header .subtitle {
            font-size: 10px;
            color: #666;
            margin-bottom: 12px;
        }
        .badges {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-tipo { background: #e8f4f8; color: #0066cc; }
        .badge-status { background: #e8f5e9; color: #2e7d32; }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            margin: 16px 0 8px 0;
            padding: 6px 8px;
            background: #f5f5f5;
            border-left: 3px solid #0066cc;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-block {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
            background: #fafafa;
            overflow: hidden;
        }
        .info-row {
            display: flex;
            border-bottom: 1px solid #ddd;
        }
        .info-row:last-child { border-bottom: none; }
        .info-cell {
            flex: 1;
            padding: 8px 10px;
            border-right: 1px solid #ddd;
        }
        .info-cell:last-child { border-right: none; }
        .info-label {
            font-size: 9px;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 11px;
            color: #222;
        }
        table.questions {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            background: white;
        }
        table.questions thead { background: #f5f5f5; }
        table.questions th {
            padding: 8px 6px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #ddd;
            color: #555;
            text-transform: uppercase;
        }
        table.questions td {
            padding: 8px 6px;
            border: 1px solid #ddd;
            font-size: 10px;
            line-height: 1.4;
            vertical-align: top;
        }
        table.questions tbody tr:nth-child(even) { background: #fafafa; }
        .q-number {
            width: 5%;
            text-align: center;
            font-weight: bold;
            color: #666;
        }
        .q-text {
            width: 40%;
            font-weight: 600;
            color: #333;
        }
        .q-answer { width: 55%; }
        .answer-empty {
            color: #999;
            font-style: italic;
            font-size: 10px;
        }
        .scale-badge {
            background: #f0f4f8;
            color: #0066cc;
            padding: 2px 6px;
            border-radius: 2px;
            font-weight: bold;
            font-size: 10px;
            display: inline-block;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #999;
            text-align: center;
            line-height: 1.4;
        }
        @media print {
            body { margin: 0; padding: 0; }
            .section-title { page-break-after: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Avaliação de Desempenho</h1>
        <div class="subtitle">Termo de Estágio Nº {{ $avaliacao->termo->numero_termo }}/{{ $avaliacao->termo->ano_termo }}</div>
    </div>

    <div class="badges">
        @if ($avaliacao->tipo_avaliacao === 'seis_meses')
            <span class="badge badge-tipo">Avaliação 6 Meses</span>
        @else
            <span class="badge badge-tipo">Avaliação de Finalização</span>
        @endif
        @if ($avaliacao->status === 'respondida')
            <span class="badge badge-status">✓ Respondida</span>
        @elseif ($avaliacao->status === 'revisada')
            <span class="badge badge-status">✓ Revisada</span>
        @else
            <span class="badge badge-status">Pendente</span>
        @endif
    </div>

    <div class="section-title">Dados do Estágio</div>
    <div class="info-block">
        <div class="info-row">
            <div class="info-cell">
                <div class="info-label">Estagiário</div>
                <div class="info-value">{{ optional($avaliacao->termo->estagiario)->nome ?? '-' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label">Empresa</div>
                <div class="info-value">{{ optional($avaliacao->termo->empresa)->razao_social ?? '-' }}</div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-cell">
                <div class="info-label">Supervisor</div>
                <div class="info-value">{{ optional($avaliacao->supervisor)->nome ?? optional($avaliacao->termo->supervisor)->nome_supervisor ?? '-' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label">Tipo de Avaliação</div>
                <div class="info-value">{{ $avaliacao->tipo_avaliacao === 'seis_meses' ? 'Avaliação de 6 Meses' : 'Avaliação de Finalização' }}</div>
            </div>
        </div>
    </div>

    @if ($avaliacao->respondida_em)
        <div class="section-title">Informações de Resposta</div>
        <div class="info-block">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Respondida por</div>
                    <div class="info-value">{{ $avaliacao->respondida_por ?? '-' }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Data/Hora</div>
                    <div class="info-value">{{ $avaliacao->respondida_em->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    @endif

    <div class="section-title">Avaliação</div>
    <table class="questions">
        <thead>
            <tr>
                <th class="q-number">Nº</th>
                <th class="q-text">Pergunta</th>
                <th class="q-answer">Resposta</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($avaliacao->questoes_respostas ?? []) as $index => $qr)
                <tr>
                    <td class="q-number">{{ $qr['ordem'] ?? ($index + 1) }}</td>
                    <td class="q-text">{{ $qr['questao'] ?? '' }}</td>
                    <td class="q-answer">
                        @php $resp = $qr['resposta'] ?? ''; @endphp
                        @if(($qr['tipo'] ?? '') === 'escala_1_5')
                            @if($resp !== '')
                                <span class="scale-badge">{{ $resp }}/5</span>
                            @else
                                <span class="answer-empty">Não respondida</span>
                            @endif
                        @else
                            @if($resp !== '')
                                {!! nl2br(e($resp)) !!}
                            @else
                                <span class="answer-empty">Sem resposta</span>
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 12px; color: #999;">
                        Nenhuma questão registrada
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div>Documento gerado automaticamente pelo SIGE</div>
        <div>{{ now()->format('d/m/Y H:i') }} | ID: {{ $avaliacao->id_avaliacao }}</div>
    </div>
</body>
</html>