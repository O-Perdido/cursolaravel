<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Comprovante de Inscricao</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2933;
            line-height: 1.45;
        }

        .header {
            border-bottom: 2px solid #0f4c5c;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .title {
            font-size: 20px;
            font-weight: 700;
            color: #0f4c5c;
            margin: 0;
        }

        .subtitle {
            margin: 4px 0 0;
            color: #52606d;
            font-size: 12px;
        }

        .box {
            border: 1px solid #d9e2ec;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .box h3 {
            margin: 0 0 8px;
            font-size: 13px;
            color: #0f4c5c;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .row {
            margin-bottom: 5px;
        }

        .label {
            font-weight: 700;
        }

        .muted {
            color: #7b8794;
            font-size: 11px;
        }

        .footer {
            margin-top: 18px;
            border-top: 1px solid #d9e2ec;
            padding-top: 8px;
            font-size: 11px;
            color: #52606d;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 class="title">Comprovante de Inscricao</h1>
        <p class="subtitle">SIGE Concursos</p>
    </div>

    <div class="box">
        <h3>Dados da Inscricao</h3>
        <div class="row"><span class="label">Numero da inscricao:</span> {{ $inscricao->numero_inscricao ?: '-' }}</div>
        <div class="row"><span class="label">Status da inscricao:</span> {{ ucfirst($inscricao->status_inscricao) }}
        </div>
        <div class="row"><span class="label">Modalidade:</span> {{ $inscricao->modalidadeLabel() }}</div>
        <div class="row"><span class="label">Data da inscricao:</span>
            {{ $inscricao->created_at?->format('d/m/Y H:i') ?: '-' }}</div>
        <div class="row"><span class="label">Taxa aplicada:</span>
            {{ $inscricao->valor_taxa_aplicada !== null ? 'R$ ' . number_format((float) $inscricao->valor_taxa_aplicada, 2, ',', '.') : 'Sem taxa' }}
        </div>
        <div class="row"><span class="label">Status do pagamento:</span>
            {{ ucfirst(str_replace('_', ' ', $inscricao->status_pagamento)) }}</div>
        <div class="row"><span class="label">Status da isencao:</span>
            {{ ucfirst(str_replace('_', ' ', $inscricao->status_isencao)) }}</div>
        @if($inscricao->isencao)
            <div class="row"><span class="label">Caso de isencao:</span> {{ $inscricao->isencao->titulo }}</div>
        @endif
    </div>

    <div class="box">
        <h3>Dados do Processo</h3>
        <div class="row"><span class="label">Titulo:</span> {{ $inscricao->processo?->titulo ?: '-' }}</div>
        <div class="row"><span class="label">Edital:</span> {{ $inscricao->processo?->numero_edital ?: '-' }}</div>
        <div class="row"><span class="label">Orgao:</span>
            {{ $inscricao->processo?->empresa?->nome_razao_social ?: '-' }}</div>
        @if($inscricao->processo?->data_prova)
            <div class="row"><span class="label">Data da prova:</span>
                {{ $inscricao->processo->data_prova->format('d/m/Y') }}</div>
        @endif
    </div>

    <div class="box">
        <h3>Dados do Candidato</h3>
        <div class="row"><span class="label">Nome completo:</span> {{ $candidato->nome_completo }}</div>
        <div class="row"><span class="label">CPF:</span>
            {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $candidato->numero_cpf) }}</div>
        <div class="row"><span class="label">E-mail:</span> {{ $candidato->email }}</div>
        <div class="row"><span class="label">Celular:</span> {{ $candidato->numero_celular ?: '-' }}</div>
    </div>

    <div class="footer">
        Documento emitido em {{ $emitidoEm->format('d/m/Y H:i:s') }}. Este comprovante confirma o registro da inscricao
        no sistema.
    </div>
</body>

</html>