<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termo de Compromisso de Estágio</title>
    <style>
        /* Estilos gerais da tabela */
        .titulo1 {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
        }

        .titulo2 {
            text-align: left;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        p {
            font-size: 11pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        /* Estilos para células (th e td) */
        td {
            padding: 2px;
            text-align: left;
            font-size: 11pt;
            padding-left: 5px;
        }

        /* Estilo para cabeçalhos (th) */
        th {
            padding: 2px;
            text-align: right;
            font-weight: bold;
            font-size: 11pt;
        }

        /* Estilo para células específicas (opcional) */
        .destaque {
            background-color: #e0f7fa;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">

    <div class="center" style="margin-top: -40px; margin-bottom: -10px;">
        <img src="{{ $linklogo }}" alt="Logo" height="85px">
    </div>

    <p class="titulo1">TERMO DE ALTERAÇÃO DE ESTÁGIOS REFERENTE AO TERMO DE COMPROMISSO
        DE ESTÁGIO – Nº
        {{ $alteracao->termo->numero_termo }}/{{ $alteracao->termo->ano_termo }}
    </p>
    <P style="text-align: center;">(instrumento jurídico de que trata o art. 3º. da Lei nº 11.788, de 25 de setembro de
        2008)</P>

    <br>

    <!-- Tabela de Dados Pessoais -->
    <p class="titulo2">ESTAGIÁRIO:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">DADOS BÁSICOS</th>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">NOME:</th>
            <td>{{ $alteracao->termo->estagiario->nome_estagiario }}</td>
            <th rowspan="2" style="width: 10%; border-right: 1px solid black;">DATA DE NASCIMENTO:</th>
            <td rowspan="2" style="width: 40%;">{{ $alteracao->termo->estagiario->data_nascimento }}</td>
        </tr>
        <tr>
            <th style="width: 10%; border-right: 1px solid black;">CPF:</th>
            <td style="width: 40%;">{{ $alteracao->termo->estagiario->numero_cpf }}</td>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center; border: 2px solid black;">ENDEREÇO</th>
            <th colspan="2" style="text-align: center; border: 2px solid black;">CONTATO</th>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">ENDEREÇO:</th>
            <td>{{ $alteracao->termo->estagiario->endereco }}, {{ $alteracao->termo->estagiario->numero_endereco }}
                {{ $alteracao->termo->estagiario->complemento_endereco }} - {{ $alteracao->termo->estagiario->bairro }}
            </td>
            <th style="border-right: 1px solid black;">TELEFONE OU CELULAR:</th>
            <td>{{ $alteracao->termo->estagiario->numero_celular }} <br>
                {{ $alteracao->termo->estagiario->numero_telefone }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CIDADE:</th>
            <td>{{ $alteracao->termo->estagiario->cidade->nm_cidade }}</td>
            <th style="border-right: 1px solid black;" rowspan="2">E-MAIL:</th>
            <td rowspan="2" style="min-width: 100px;">{{ $alteracao->termo->estagiario->email }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">UF:</th>
            <td>{{ $alteracao->termo->estagiario->cidade->estado->uf_estado }}</td>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">CURSO</th>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">FORMAÇÃO:</th>
            <td>{{ $alteracao->termo->estagiario->curso }}</td>
            <th style="border-right: 1px solid black;">NÍVEL:</th>
            <td>{{ $alteracao->termo->estagiario->nivel_curso }}</td>
        </tr>
    </table>

    <!-- Tabela de Dados da Unidade Concedente do Estágio -->

    <p class="titulo2">UNIDADE CONCEDENTE DO ESTÁGIO:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">DADOS BÁSICOS</th>
        </tr>
        <tr rowspan="2">
            <th rowspan="2" style="width: 10%; border-right: 1px solid black;">NOME/RAZÃO SOCIAL</th>
            <td rowspan="2" style="width: 40%;">{{ $alteracao->termo->empresa->nome_empresa }}</td>
            <th style="width: 15%; border-right: 1px solid black;">CNPJ:</th>
            <td style="width: 35%;">{{ $alteracao->termo->empresa->numero_cnpj }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">TELEFONE OU CELULAR:</th>
            <td>{{ $alteracao->termo->empresa->numero_celular }} <br> {{ $alteracao->termo->empresa->numero_telefone}}
            </td>
        </tr>

        <tr>
            <th colspan="2" style="text-align: center; border: 2px solid black;">ENDEREÇO</th>
            <th colspan="2" style="text-align: center; border: 2px solid black;">REPRESENTANTE</th>
        </tr>
        <tr>
            <th style="width: 10%; border-right: 1px solid black;">ENDEREÇO:</th>
            <td style="width: 40%;">{{ $alteracao->termo->empresa->endereco }},
                {{ $alteracao->termo->empresa->numero_endereco }}
                {{ $alteracao->termo->empresa->complemento_endereco }} - {{ $alteracao->termo->empresa->bairro }}
            </td>
            <th style="width: 10%; border-right: 1px solid black;">NOME:</th>
            <td style="width: 40%;">{{ $alteracao->termo->empresa->nome_representante }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CIDADE:</th>
            <td>{{ $alteracao->termo->empresa->cidade->nm_cidade }}</td>
            <th style="border-right: 1px solid black;">CARGO:</th>
            <td>{{ $alteracao->termo->empresa->cargo_representante }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">UF:</th>
            <td>{{ $alteracao->termo->empresa->cidade->estado->uf_estado }}</td>
            <th style="border-right: 1px solid black;" rowspan="2">CPF:</th>
            <td rowspan="2">{{ $alteracao->termo->empresa->cpf_representante }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CEP:</th>
            <td>{{ $alteracao->termo->empresa->numero_cep }}</td>
        </tr>
    </table>

    <!-- Tabela de Dados da Instituição de Ensino -->

    <p class="titulo2">INSTITUIÇÃO DE ENSINO:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">DADOS BÁSICOS</th>
        </tr>
        <tr rowspan="2">
            <th rowspan="2" style="width: 10%; border-right: 1px solid black;">NOME:</th>
            <td rowspan="2" style="width: 40%;">{{ $alteracao->termo->escola->nome_escola }}</td>
            <th style="width: 15%; border-right: 1px solid black;">CNPJ:</th>
            <td style="width: 40%;">{{ $alteracao->termo->escola->numero_cnpj }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">TELEFONE OU CELULAR:</th>
            <td>{{ $alteracao->termo->escola->numero_celular }} <br>
                {{ $alteracao->termo->escola->numero_telefone }}
            </td>
        </tr>

        <tr>
            <th colspan="2" style="text-align: center; border: 2px solid black;">ENDEREÇO</th>
            <th colspan="2" style="text-align: center; border: 2px solid black;">RESPONSAVEL</th>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">ENDEREÇO:</th>
            <td colspan="1">{{ $alteracao->termo->escola->endereco }},
                {{ $alteracao->termo->escola->numero_endereco }}
                {{ $alteracao->termo->escola->complemento_endereco }}
                - {{ $alteracao->termo->escola->bairro }}
            </td>
            <th style="border-right: 1px solid black;">NOME:</th>
            <td>{{ $alteracao->termo->escola->nome_representante }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CIDADE:</th>
            <td>{{ $alteracao->termo->escola->cidade->nm_cidade }}</td>
            <th style="border-right: 1px solid black;">CARGO:</th>
            <td>{{ $alteracao->termo->escola->cargo_representante }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">UF:</th>
            <td>{{ $alteracao->termo->escola->cidade->estado->uf_estado }}</td>
            <th style="border-right: 1px solid black;" rowspan="2">CPF:</th>
            <td rowspan="2">{{ $alteracao->termo->escola->cpf_representante }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CEP:</th>
            <td>{{ $alteracao->termo->escola->numero_cep }}</td>
        </tr>
    </table>

    <!-- Tabela de Dados do Estágio -->

    <p class="titulo2">DADOS DO ESTÁGIO:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th style="width: 10%; border-right: 1px solid black;">INÍCIO DO ESTÁGIO:</th>
            <td style="width: 40%;">{{ \Carbon\Carbon::parse($alteracao->termo->data_inicio_estagio)->format('d/m/Y') }}
            </td>
            <th style="width: 10%; border-right: 1px solid black;">FIM DO ESTÁGIO:</th>
            <td style="width: 40%;">{{ \Carbon\Carbon::parse($alteracao->termo->data_fim_estagio)->format('d/m/Y') }}
            </td>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">HORÁRIO DE ESTÁGIO</th>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center;">{{ $alteracao->termo->horario }}</td>
        </tr>
    </table>

    <!-- Tabela de Dados do Supervisor -->

    <p class="titulo2">DADOS DO(A) SUPERVISOR(A) U. C.:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th style="width: 10%; border-right: 1px solid black;">NOME DO SUPERVISOR(A):</th>
            <td style="width: 40%;">{{ $alteracao->termo->supervisor->nome_supervisor }}</td>
            <th rowspan="2" style="width: 10%; border-right: 1px solid black;">TEMPO DE EXPERIÊNCIA:</th>
            <td rowspan="2" style="width: 40%;">
                {{ $alteracao->termo->supervisor->tempo_experiencia ?? 'CONFORME INDICAÇÃO DA INSTITUIÇÃO DE ENSINO' }}
            </td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">ÁREA DE FORMAÇÃO:</th>
            <td>{{ $alteracao->termo->supervisor->area_formacao }}</td>
        </tr>
    </table>

    <!-- Tabela de Dados do Orientador -->

    <p class="titulo2">DADOS DO(A) ORIENTADOR(A) I. E.:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th style="width: 10%; border-right: 1px solid black;">NOME DO ORIENTADOR(A):</th>
            <td colspan="3" style="width: 40%;">
                {{ $alteracao->termo->nome_orientador}}
            </td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CARGO:</th>
            <td colspan="3">{{ $alteracao->termo->cargo_orientador}}</td>
        </tr>
    </table>

    <!-- Tabela de Dados das Atividades do Estágio -->

    <p class="titulo2" style="text-align: center;">PRINCIPAIS ATIVIDADES:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th style="text-align: center;">{{ $alteracao->termo->desc_atividades ?? 'NÃO HÁ DESCRIÇÃO' }}</th>
        </tr>
    </table>
    <br>
    <p>
        {!! nl2br(e($alteracao->descricao)) !!}
    </p>

    @if(isset($paraZapSign) && $paraZapSign && isset($signatarios))
        {{-- Versão dinâmica para ZapSign --}}
        <div style="margin-top: 30px; padding: 8px 12px; background-color: #f8f9fa; border-left: 3px solid #0d6efd;">
            <p style="margin: 0 0 5px 0; color: #0d6efd; font-size: 11px; font-weight: bold;">
                📋 ASSINATURAS DIGITAIS
            </p>
            <p style="margin: 0 0 5px 0; font-size: 9px; line-height: 1.45;">
                Este documento será assinado digitalmente através da plataforma ZapSign em conformidade com a Lei nº
                14.063/2020.
            </p>
            <p style="margin: 5px 0 3px 0; font-size: 9px; font-weight: bold;">Signatários:</p>
            <ol style="margin: 0; padding-left: 15px; font-size: 9px; line-height: 1.5;">
                @foreach($signatarios as $index => $signatario)
                    <li>
                        <strong>{{ $signatario['tipo'] }}</strong>
                        @if(!empty($signatario['nome']))
                            - {{ $signatario['nome'] }}
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>

        {{-- Espaço reservado para as assinaturas do ZapSign --}}
        <div style="margin-top: 180px;"></div>
    @else
        {{-- Versão estática padrão (para impressão/download normal) --}}
        <table style="padding-top: 100px; width: 100%;">
            <tr>
                <td style="text-align: center;">______________________________</td>
                <td></td>
                <td style="text-align: center;">______________________________</td>
            </tr>
            <tr>
                <td style="text-align: center;">Pela Concedente</td>
                <td></td>
                <td style="text-align: center;">Pela Instituição de Ensino</td>
            </tr>
        </table>
        <table style="padding-top: 100px; width: 100%;">
            <tr>
                <td style="text-align: center;">______________________________</td>
                <td></td>
                <td style="text-align: center;">______________________________</td>
            </tr>
            <tr>
                <td style="text-align: center;">Estagiário/Representante Legal</td>
                <td></td>
                <td style="text-align: center;">EBCP Consultoria <br> (Agente de Integração)</td>
            </tr>
        </table>
    @endif

</body>

</html>