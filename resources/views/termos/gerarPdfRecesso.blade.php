<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termo de Concessão de Recesso de Estágio</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10pt;
        }

        .center {
            text-align: center;
        }

        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 6px 0 2px;
        }

        .subtitle {
            text-align: center;
            font-size: 10pt;
            margin: 0 0 10px;
        }

        < !DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Termo de Concessão de Recesso de Estágio</title><style>

        /* Tipografia e Reset simples */
        body {
            font-family: DejaVu Sans, Arial, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10pt;
            color: #1f2937;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
            padding: 0;
        }

        .mt-1 {
            margin-top: 6px;
        }

        .mt-2 {
            margin-top: 12px;
        }

        .mt-3 {
            margin-top: 18px;
        }

        .mb-1 {
            margin-bottom: 6px;
        }

        .mb-2 {
            margin-bottom: 12px;
        }

        .mb-3 {
            margin-bottom: 18px;
        }

        .small {
            font-size: 8.5pt;
            color: #4b5563;
        }

        .muted {
            color: #6b7280;
        }

        /* Cabeçalho */
        .header {
            width: 100%;
            border-bottom: 3px solid #0f3a78;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header-table {
            width: 100%;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo {
            height: 70px;
        }

        .org-name {
            font-size: 12pt;
            font-weight: 700;
            color: #0f3a78;
        }

        .org-sub {
            font-size: 9pt;
            color: #1f2937;
        }

        /* Título do documento */
        .doc-title {
            text-align: center;
            margin: 6px 0 2px;
        }

        .doc-title h1 {
            font-size: 14pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .doc-title .subtitle {
            font-size: 9pt;
            color: #374151;
        }

        /* Seções estilizadas */
        .section {
            width: 100%;
            border: 1.6px solid #c7d2fe;
            border-radius: 4px;
            margin: 10px 0 8px;
        }

        .section .section-title {
            background: #eef2ff;
            color: #0f3a78;
            font-weight: 700;
            text-align: left;
            padding: 6px 8px;
            border-bottom: 1px solid #c7d2fe;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            width: 28%;
            text-align: right;
            padding: 6px 8px;
            font-weight: 700;
            color: #374151;
            background: #fafafa;
            border-right: 1px solid #e5e7eb;
        }

        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #f3f4f6;
            word-wrap: break-word;
        }

        .data-table tr:last-child td,
        .data-table tr:last-child th {
            border-bottom: none;
        }

        /* Cartão Resumo */
        .summary {
            border: 1.6px solid #d1fae5;
            background: #ecfdf5;
            border-radius: 4px;
            padding: 8px 10px;
        }

        .badge {
            display: inline-block;
            background: #0f3a78;
            color: #fff;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8.5pt;
        }

        /* Assinaturas */
        .sign-table {
            width: 100%;
            margin-top: 150px;
        }

        .sign-cell {
            width: 50%;
            text-align: center;
            padding: 20px 10px 0;
        }

        .sign-line {
            border-top: 1px solid #111827;
            margin: 0 auto;
            width: 85%;
            height: 1px;
        }

        .sign-label {
            margin-top: 6px;
            font-size: 9pt;
            color: #111827;
        }

        /* Rodapé */
        .footer {
            margin-top: 16px;
            font-size: 8pt;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Cabeçalho -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 90px; text-align: left;">
                    <img class="logo" src="{{ $linklogo }}" alt="Logo">
                </td>
                <td style="text-align: left;">
                    <div class="org-name">{{ $ebcp->nome_ebcp }}</div>
                    <div class="org-sub">CNPJ: {{ $ebcp->cnpj_ebcp }} · {{ $ebcp->contato_ebcp }}</div>
                    <div class="small">{{ $ebcp->endereço_ebcp }} · CEP {{ $ebcp->cep_ebcp }}</div>
                </td>
                <td style="text-align: right; font-size: 9pt; color: #374151;">
                    <div><strong>Documento</strong></div>
                    <div>Termo de Concessão de Recesso</div>
                    <div class="small mt-1">Gerado em {{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Título -->
    <div class="doc-title">
        <h1>Termo de Concessão de Recesso de Estágio</h1>
        <div class="subtitle">Base legal: Lei nº 11.788/2008 (art. 13)</div>
    </div>

    <!-- Seção: Estagiário -->
    <div class="section mt-2">
        <div class="section-title">Dados do Estagiário</div>
        <table class="data-table">
            <tr>
                <th>Nome</th>
                <td>{{ $termo->estagiario->nome_estagiario }}</td>
                <th>CPF</th>
                <td>{{ $termo->estagiario->numero_cpf }}</td>
            </tr>
            <tr>
                <th>Curso</th>
                <td>{{ $termo->estagiario->curso }}</td>
                <th>Instituição de Ensino</th>
                <td>{{ $termo->escola->nome_escola }}</td>
            </tr>
        </table>
    </div>

    <!-- Seção: Termo de Estágio -->
    <div class="section">
        <div class="section-title">Dados do Termo de Estágio</div>
        <table class="data-table">
            <tr>
                <th>Nº / Ano</th>
                <td>{{ $termo->numero_termo }}/{{ $termo->ano_termo }}</td>
                <th>Unidade Concedente</th>
                <td>{{ $termo->empresa->nome_empresa }}</td>
            </tr>
            <tr>
                <th>Início do Estágio</th>
                <td>{{ \Carbon\Carbon::parse($termo->data_inicio_estagio)->format('d/m/Y') }}</td>
                <th>Fim do Estágio</th>
                <td>{{ \Carbon\Carbon::parse($termo->data_fim_estagio)->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Seção: Concessão de Recesso -->
    <div class="section">
        <div class="section-title">Concessão de Recesso</div>
        <table class="data-table">
            <tr>
                <th style="width: 30%;">Período do Recesso</th>
                <td colspan="3">{{ $inicio_recesso->format('d/m/Y') }} a {{ $fim_recesso->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Total de Dias Concedidos</th>
                <td colspan="3"><strong style="font-size: 11pt;">{{ $total_dias_recesso }} dia(s)</strong></td>
            </tr>
        </table>
    </div>

    <!-- Texto legal -->
    <p class="mt-2" style="line-height: 1.45;">
        Pelo presente instrumento, a <strong>{{ $termo->empresa->nome_empresa }}</strong> concede ao(à) estagiário(a)
        <strong>{{ $termo->estagiario->nome_estagiario }}</strong> o recesso remunerado no período acima indicado, nos
        termos da
        legislação vigente. O recesso deverá ser gozado preferencialmente durante as férias escolares, sem prejuízo da
        bolsa-auxílio e demais benefícios, quando aplicáveis.
    </p>

    <!-- Local e data -->
    <p class="mt-3">Local e data: __________________________________________</p>

    <!-- Assinaturas -->
    <table class="sign-table">
        <tr>
            <td class="sign-cell">
                <div class="sign-line"></div>
                <div class="sign-label">Pela Concedente</div>
            </td>
            <td class="sign-cell">
                <div class="sign-line"></div>
                <div class="sign-label">Estagiário(a) / Representante Legal</div>
            </td>
        </tr>
    </table>
</body>

</html>