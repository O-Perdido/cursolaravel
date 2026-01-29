<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Estagiário</title>

    <style>
        /* Estilos gerais da tabela */
        .titulo1 {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
        }

        .titulo2 {
            text-align: left;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        p {
            font-size: 10pt;
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
            font-size: 9pt;
            padding-left: 5px;
        }

        /* Estilo para cabeçalhos (th) */
        th {
            padding: 2px;
            text-align: right;
            font-weight: bold;
            font-size: 10pt;
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

    <p class="titulo1">TERMO DE COMPROMISSO DE ESTÁGIO NÃO OBRIGATÓRIO -
        {{ $termo->numero_termo }}/{{ $termo->ano_termo }}
    </p>
    <P style="text-align: center;">(instrumento jurídico de que trata o art. 3º. da Lei nº 11.788, de 25 de setembro de
        2008)</P>
    <P style="text-align: left;"> As partes abaixo qualificadas, firmam o presente TERMO DE COMPROMISSO DE ESTÁGIO,
        mediante as seguintes cláusulas e condições:
    </P>


    <p class="titulo2">A) EMPRESA DE INTERMEDIAÇÃO DO ESTÁGIO</p>
    <table style="border: 2px solid black;">
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">DADOS BÁSICOS</th>
        </tr>
        <tr>
            <th style="width: 10%; border-right: 1px solid black;">NOME:</th>
            <td style="width: 40%;">{{ $ebcp->nome_ebcp }}</td>
            <th style="width: 15%; border-right: 1px solid black;">CNPJ:</th>
            <td style="width: 35%;">{{ $ebcp->cnpj_ebcp }}</td>
        </tr>
        <tr>
            <td colspan="4" style="border-top: 1px solid black;"></td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">TELEFONE:</th>
            <td colspan="3">{{ $ebcp->contato_ebcp }}</td>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">ENDEREÇO</th>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">ENDEREÇO:</th>
            <td colspan="3">{{ $ebcp->endereço_ebcp }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CEP:</th>
            <td colspan="3">{{ $ebcp->cep_ebcp }}</td>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">REPRESENTANTE LEGAL</th>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">NOME COMPLETO:</th>
            <td colspan="3">{{ $ebcp->nome_representante }}</td>
        </tr>
    </table>

    <!-- Tabela de Dados Pessoais -->
    <p class="titulo2">B) ESTAGIÁRIO:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">DADOS BÁSICOS</th>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">NOME:</th>
            <td>{{ $termo->estagiario->nome_estagiario }}</td>
            <th rowspan="2" style="width: 10%; border-right: 1px solid black;">DATA DE NASCIMENTO:</th>
            <td rowspan="2" style="width: 40%;">{{ $termo->estagiario->data_nascimento }}</td>
        </tr>

        <tr>
            <th style="width: 10%; border-right: 1px solid black;">CPF:</th>
            <td style="width: 40%;">{{ $termo->estagiario->numero_cpf }}</td>
        </tr>
        @if ($termo->lotacao_fixo)
            <tr>
                <th colspan="4" style="text-align: center; border: 2px solid black;">LOCAL DE ESTÁGIO</th>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center;">{{ $termo->lotacao_fixo }}</td>
            </tr>
        @endif
        <tr>
            <th colspan="2" style="text-align: center; border: 2px solid black;">ENDEREÇO</th>
            <th colspan="2" style="text-align: center; border: 2px solid black;">CONTATO</th>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">ENDEREÇO:</th>
            <td>{{ $termo->estagiario->endereco }}, {{ $termo->estagiario->numero_endereco }}
                {{ $termo->estagiario->complemento_endereco }} - {{ $termo->estagiario->bairro }}
            </td>
            <th style="border-right: 1px solid black;">TELEFONE OU CELULAR:</th>
            <td>{{ $termo->estagiario->numero_celular }} <br> {{ $termo->estagiario->numero_telefone }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CIDADE:</th>
            <td>{{ $termo->estagiario->cidade->nm_cidade }}</td>
            <th style="border-right: 1px solid black;" rowspan="2">E-MAIL:</th>
            <td rowspan="2" style="min-width: 100px;">{{ $termo->estagiario->email }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">UF:</th>
            <td>{{ $termo->estagiario->cidade->estado->uf_estado }}</td>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">CURSO</th>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">FORMAÇÃO:</th>
            <td>{{ $termo->estagiario->curso }}</td>
            <th style="border-right: 1px solid black;">NÍVEL:</th>
            <td>{{ $termo->estagiario->nivel_curso }}</td>
        </tr>
    </table>

    <!-- Tabela de Dados da Unidade Concedente do Estágio -->

    <p class="titulo2">C) UNIDADE CONCEDENTE DO ESTÁGIO:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">DADOS BÁSICOS</th>
        </tr>
        <tr rowspan="2">
            <th rowspan="2" style="width: 10%; border-right: 1px solid black;">NOME/RAZÃO SOCIAL</th>
            <td rowspan="2" style="width: 40%;">{{ $termo->empresa->nome_empresa }}</td>
            <th style="width: 15%; border-right: 1px solid black;">CNPJ:</th>
            <td style="width: 35%;">{{ $termo->empresa->numero_cnpj }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">TELEFONE OU CELULAR:</th>
            <td>{{ $termo->empresa->numero_celular }} <br> {{ $termo->empresa->numero_telefone}} </td>
        </tr>

        <tr>
            <th colspan="2" style="text-align: center; border: 2px solid black;">ENDEREÇO</th>
            <th colspan="2" style="text-align: center; border: 2px solid black;">REPRESENTANTE</th>
        </tr>
        <tr>
            <th style="width: 10%; border-right: 1px solid black;">ENDEREÇO:</th>
            <td style="width: 40%;">{{ $termo->empresa->endereco }}, {{ $termo->empresa->numero_endereco }}
                {{ $termo->empresa->complemento_endereco }} - {{ $termo->empresa->bairro }}
            </td>
            <th style="width: 10%; border-right: 1px solid black;">NOME:</th>
            <td style="width: 40%;">{{ $termo->empresa->nome_representante }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CIDADE:</th>
            <td>{{ $termo->empresa->cidade->nm_cidade }}</td>
            <th style="border-right: 1px solid black;">CARGO:</th>
            <td>{{ $termo->empresa->cargo_representante }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">UF:</th>
            <td>{{ $termo->empresa->cidade->estado->uf_estado }}</td>
            <th style="border-right: 1px solid black;" rowspan="2">CPF:</th>
            <td rowspan="2">{{ $termo->empresa->cpf_representante }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CEP:</th>
            <td>{{ $termo->empresa->numero_cep }}</td>
        </tr>
    </table>

    <br>
    <!-- Tabela de Dados da Instituição de Ensino -->
    <div style="page-break-before: always;"></div>
    <p class="titulo2">D) INSTITUIÇÃO DE ENSINO:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">DADOS BÁSICOS</th>
        </tr>
        <tr rowspan="2">
            <th rowspan="2" style="width: 10%; border-right: 1px solid black;">NOME:</th>
            <td rowspan="2" style="width: 40%;">{{ $termo->escola->nome_escola }}</td>
            <th style="width: 15%; border-right: 1px solid black;">CNPJ:</th>
            <td style="width: 40%;">{{ $termo->escola->numero_cnpj }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">TELEFONE OU CELULAR:</th>
            <td>{{ $termo->escola->numero_celular }} <br> {{ $termo->escola->numero_telefone }}
            </td>
        </tr>

        <tr>
            <th colspan="2" style="text-align: center; border: 2px solid black;">ENDEREÇO</th>
            <th colspan="2" style="text-align: center; border: 2px solid black;">RESPONSAVEL</th>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">ENDEREÇO:</th>
            <td colspan="1">{{ $termo->escola->endereco }},
                {{ $termo->escola->numero_endereco }} {{ $termo->escola->complemento_endereco }}
                - {{ $termo->escola->bairro }}
            </td>
            <th style="border-right: 1px solid black;">NOME:</th>
            <td>{{ $termo->escola->nome_representante }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CIDADE:</th>
            <td>{{ $termo->escola->cidade->nm_cidade }}</td>
            <th style="border-right: 1px solid black;">CARGO:</th>
            <td>{{ $termo->escola->cargo_representante }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">UF:</th>
            <td>{{ $termo->escola->cidade->estado->uf_estado }}</td>
            <th style="border-right: 1px solid black;" rowspan="2">CPF:</th>
            <td rowspan="2">{{ $termo->escola->cpf_representante }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CEP:</th>
            <td>{{ $termo->escola->numero_cep }}</td>
        </tr>
    </table>

    <p>&nbsp; Cláusula 1ª - Este instrumento tem por objetivo formalizar as condições para a realização de ESTÁGIO DE
        ESTUDANTE e particularizar a relação
        jurídica especial existente entre o ESTUDANTE, a CONCEDENTE e a INSTITUIÇÃO DE ENSINO/AGENTE DE INTEGRAÇÃO, nos
        termos da legislação
        vigente, com interveniência da {{ $ebcp->nome_ebcp }} sob amparo do art. 5º. da Lei nº. 11.788/08.
    </p>

    <p>&nbsp; Cláusula 2ª - O ESTÁGIO, como ato educativo supervisionado não obrigatório faz parte do Projeto Pedagógico
        do Curso, nos termos
        da Lei nº. 11.788/08, e da Lei nº. 9394/96 (Diretrizes e Bases da Educação Nacional) e tem por objetivo a
        preparação para o trabalho produtivo de
        educandos que estejam frequentando o ensino regular.
    </p>

    <p>&nbsp; Cláusula 3ª - O estágio não obrigatório não cria vínculo empregatício de qualquer
        natureza,
        desde que observados os requisitos do
        artigo 3º. da Lei nº. 11.788/08 e do presente Termo de Compromisso.
    </p>

    <p>&nbsp; Cláusula 4ª - Na conformidade da Lei nº. 11.788/08, as partes convencionam e estabelecem:</p>

    <p>
        &nbsp;&nbsp; a) Plano de Atividades do Estagiário:
    </p>

    <!-- Tabela de Dados do Estágio -->

    <p class="titulo2">DADOS DO ESTÁGIO:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th style="width: 10%; border-right: 1px solid black;">INÍCIO DO ESTÁGIO:</th>
            <td style="width: 40%;">{{ \Carbon\Carbon::parse($termo->data_inicio_estagio)->format('d/m/Y') }}</td>
            <th style="width: 10%; border-right: 1px solid black;">FIM DO ESTÁGIO:</th>
            <td style="width: 40%;">{{ \Carbon\Carbon::parse($termo->data_fim_estagio_fixo)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; border: 2px solid black;">HORÁRIO DE ESTÁGIO</th>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center;">{{ $termo->horario_fixo }}</td>
        </tr>
    </table>

    <!-- Tabela de Dados do Supervisor -->

    <p class="titulo2">DADOS DO(A) SUPERVISOR(A) U. C.:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th style="width: 10%; border-right: 1px solid black;">NOME DO SUPERVISOR(A):</th>
            <td style="width: 40%;">{{ $termo->supervisorFixo->nome_supervisor }}</td>
            <th rowspan="2" style="width: 10%; border-right: 1px solid black;">TEMPO DE EXPERIÊNCIA:</th>
            <td rowspan="2" style="width: 40%;">{{ $termo->supervisorFixo->tempo_experiencia ?? 'CONFORME INDICAÇÃO DA
                INSTITUIÇÃO DE ENSINO' }}</td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">ÁREA DE FORMAÇÃO:</th>
            <td>{{ $termo->supervisor->area_formacao }}</td>
        </tr>
    </table>

    <!-- Tabela de Dados do Orientador -->

    <p class="titulo2">DADOS DO(A) ORIENTADOR(A) I. E.:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th style="width: 10%; border-right: 1px solid black;">NOME DO ORIENTADOR(A):</th>
            <td colspan="3" style="width: 40%;">
                {{ $termo->nome_orientador_fixo ?? 'CONFORME INDICAÇÃO DA INSTITUIÇÃO DE ENSINO' }}
            </td>
        </tr>
        <tr>
            <th style="border-right: 1px solid black;">CARGO:</th>
            <td colspan="3">{{ $termo->cargo_orientador_fixo ?? '' }}</td>
            </td>
        </tr>
    </table>

    <!-- Tabela de Dados das Atividades do Estágio -->

    <p class="titulo2" style="text-align: center;">PRINCIPAIS ATIVIDADES:</p>
    <table style="border: 2px solid black;">
        <tr>
            <th style="text-align: center;">{{ $termo->desc_atividades_fixo ?? 'NÃO HÁ DESCRIÇÃO' }}</th>
        </tr>
    </table>

    <?php
function numeroPorExtenso($numero)
{
    $unidades = ['', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove'];
    $dezenas = ['', 'dez', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa'];
    $centenas = ['', 'cem', 'duzentos', 'trezentos', 'quatrocentos', 'quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos'];
    $especiais = [11 => 'onze', 12 => 'doze', 13 => 'treze', 14 => 'quatorze', 15 => 'quinze', 16 => 'dezesseis', 17 => 'dezessete', 18 => 'dezoito', 19 => 'dezenove'];

    if ($numero == 0) {
        return 'zero';
    }

    $extenso = '';

    // Tratar a parte inteira
    $parte_inteira = floor($numero);
    $parte_decimal = round(($numero - $parte_inteira) * 100);

    if ($parte_inteira >= 1000) {
        $milhar = floor($parte_inteira / 1000);
        $extenso .= ' ' . numeroPorExtenso($milhar) . ' mil';
        $parte_inteira %= 1000;
    }

    if ($parte_inteira >= 100) {
        $centena = floor($parte_inteira / 100);
        $extenso .= ' ' . $centenas[$centena];
        $parte_inteira %= 100;
    }

    if ($parte_inteira >= 11 && $parte_inteira <= 19) {
        $extenso .= ' ' . $especiais[$parte_inteira];
        $parte_inteira = 0;
    } else {
        if ($parte_inteira >= 10) {
            $dezena = floor($parte_inteira / 10);
            $extenso .= ' ' . $dezenas[$dezena];
            $parte_inteira %= 10;
        }
    }

    if ($parte_inteira > 0) {
        $extenso .= ' ' . $unidades[$parte_inteira];
    }

    // Tratar a parte decimal
    if ($parte_decimal > 0) {
        $extenso .= ' e ' . numeroPorExtenso($parte_decimal);
    }


    return trim($extenso);
}
    ?>

    <p>
        &nbsp;&nbsp; b) Bolsa auxílio estágio: Será obrigatoriamente paga ao estudante estagiário, pela concedente do
        estágio, bolsa
        auxílio mensal de R$ {{ number_format($termo->valor_bolsa_fixo, 2, ',', '.') }}
        ({{ numeroPorExtenso($termo->valor_bolsa_fixo) }}).
    </p>
    <p>
        &nbsp;&nbsp; c) O valor da BAE poderá variar de acordo com a frequência do estudante ao estágio e está sujeito à
        retenção do
        Imposto de Renda, conforme tabela de incidência fixada pelo Ministério da Fazenda que estiver em vigor.
    </p>
    <p>
        @if($termo->auxilio_transporte_fixo == 0)
            &nbsp;&nbsp; d) Poderá ser fornecido ao estudante estagiário, pela concedente do estágio, auxílio transporte.
        @else
            &nbsp;&nbsp; d) Será fornecido ao estudante estagiário, pela concedente do estágio, auxílio transporte no valor
            de R$
            {{ number_format($termo->auxilio_transporte_fixo, 2, ',', '.') }}
            ({{ numeroPorExtenso($termo->auxilio_transporte_fixo) }}).
        @endif
    </p>
    <p>
        &nbsp;&nbsp; e) Recesso: será concedido recesso remunerado de 30 dias ao estagiário com estágio de duração igual
        ou superior a um ano ou conforme disposição do art. 13 parágrafos 1º e 2º da lei 11788/2008.
    </p>

    <p>&nbsp; Cláusula 5ª – Nos termos da Lei nº. 11.788/08, são obrigações específicas das partes abaixo declinadas:
    </p>

    <p>&nbsp;&nbsp; 5.1 – INSTITUIÇÃO DE ENSINO:</p>
    <p>
        &nbsp;&nbsp;&nbsp; a) Celebrar o Termo de Compromisso de Estágio com seu educando e com a concedente do estágio,
        considerando as
        condições de sua adequação à proposta pedagógica do curso, à etapa e modalidade da formação escolar do
        estagiário e ao horário e calendário escolar.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; b) Estabelecer o Plano de Atividades do Estagiário que consubstancie as condições/requisitos
        suficientes à
        exigência legal de adequação à etapa e modalidade da formação escolar do ESTAGIÁRIO.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; c) Avaliar e aprovar as instalações da CONCEDENTE.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; d) Indicar um responsável pelo acompanhamento e avaliação das atividades do ESTAGIÁRIO em
        conformidade com a
        legislação vigente.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; e) Comunicar à parte concedente do estágio, no início do período letivo, as datas de
        realização das avaliações
        escolares ou acadêmicas.
    </p>

    <p>&nbsp;&nbsp; 5.2 – CONCEDENTE DO ESTÁGIO:</p>
    <p>
        &nbsp;&nbsp;&nbsp; a) Celebrar o Termo de Compromisso de Estágio com a Instituição de Ensino e o educando
        estagiário, zelando seu
        fiel cumprimento.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; b) Proporcionar ao ESTAGIÁRIO condições para o exercício das atividades práticas compatíveis
        com o seu Plano de
        Atividades.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; c) Designar um supervisor que seja funcionário de seu quadro de pessoal, com formação ou
        experiência
        profissional na área de conhecimento desenvolvida no curso do ESTAGIÁRIO, para orientá-lo e acompanhá-lo no
        desenvolvimento das atividades do estágio.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; d) Conceder auxílio-transporte de acordo com a legislação do município e período de recesso a
        ser gozado
        preferencialmente durante as férias escolares, nos termos da legislação vigente.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; e) Adaptar a jornada de estágio nos períodos de avaliação escolar ou acadêmica, previamente
        informados pela
        Instituição de Ensino.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; f) Encaminhar para a Instituição de Ensino o relatório individual de atividades, assinado
        pelo seu Supervisor,
        com periodicidade mínima de 06 (seis) meses com vista obrigatória do ESTAGIÁRIO.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; g) Entregar, por ocasião do desligamento, termo de realização do estágio com indicação
        resumida das atividades
        desenvolvidas, dos períodos e da avaliação de desempenho.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; h) Manter em arquivo e à disposição da fiscalização os documentos firmados que comprovem a
        relação de estágio.
    </p>

    <p>
        &nbsp;&nbsp;&nbsp; i) Informar à {{ $ebcp->nome_ebcp }} a rescisão antecipada deste instrumento, para as devidas
        providências
        administrativas que se fizerem necessárias.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; j) Permitir o início das atividades de estágio somente após o recebimento deste instrumento
        assinado pelas
        partes signatárias.
    </p>
    <p>&nbsp;&nbsp; 5.3- ESTUDANTE ESTAGIÁRIO:</p>
    <p>
        &nbsp;&nbsp;&nbsp; a) Cumprir, com todo empenho e interesse, toda programação estabelecida para seu ESTÁGIO;
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; b) Observar, obedecer e cumprir as normas internas da CONCEDENTE, preservando o sigilo e a
        confidencialidade das
        informações que tiver acesso;
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; c) Apresentar documentos comprobatórios da regularidade da sua situação escolar, sempre que
        solicitado pela
        CONCEDENTE;
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; d) Manter rigorosamente atualizados seus dados cadastrais e escolares, junto à Concedente e
        ao Agente de
        Integração;
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; e) Informar de imediato, qualquer alteração na sua situação escolar, tais como: trancamento
        de matrícula,
        abandono, conclusão de curso ou transferência de Instituição de Ensino;
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; f) Entregar, obrigatoriamente, à Instituição de Ensino, à Concedente e à
        {{ $ebcp->nome_ebcp }}, uma via do
        presente instrumento,
        devidamente assinado pelas partes;
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; g) Preencher e entregar, obrigatoriamente, os Relatórios de Atividades na periodicidade
        mínima de 06 (seis)
        meses e, inclusive, sempre que solicitado.
    </p>
    <p>
        &nbsp;&nbsp;&nbsp; h) Não possuir outro estágio em vigor que, juntamente com este contrato, venha a descumprir o
        disposto no artigo
        10° da Lei 11.788/2008.
    </p>
    <p>&nbsp; Cláusula 6ª - Cabe à {{ $ebcp->nome_ebcp }}, como auxiliar no processo de aperfeiçoamento do instituto de
        estágio:
    </p>
    <p>
        &nbsp;&nbsp; a) identificar oportunidades de estágio;
    </p>
    <p>
        &nbsp;&nbsp; b) ajustar suas condições de realização;
    </p>
    <p>
        &nbsp;&nbsp; c) fazer o acompanhamento administrativo;
    </p>
    <p>
        &nbsp;&nbsp; d) encaminhar negociação de seguros contra acidentes pessoais;
    </p>
    <p>
        &nbsp;&nbsp; e) cadastrar os estudantes.
    </p>
    <p>
        &nbsp;&nbsp; f) solicitar ao ESTAGIÁRIO, a qualquer tempo, documentos comprobatórios da regularidade da situação
        escolar, uma
        vez que trancamento de matrícula, abandono, conclusão de curso ou transferência de Instituição de Ensino
        constituem motivos de imediata rescisão.
    </p>
    <p>&nbsp; Cláusula 7ª - Em cumprimento ao Art. 9º da Lei 11.788/08 e na vigência do presente TERMO DE COMPROMISSO DE
        ESTÁGIO, o ESTAGIÁRIO
        estará incluído na cobertura do SEGURO CONTRA ACIDENTES PESSOAIS de até R$ 10.000,00, além de despesas
        médico-hospitalares respeitados os limites segurados na apólice nº. 0982.00.67225.173-8
        da PORTO SEGURO,
        sob a responsabilidade da {{ $ebcp->nome_ebcp }}.
    </p>
    <p>&nbsp; Cláusula 8ª - A concedente do estágio pagará o valor da Bolsa Auxílio de Estágio mencionada na alínea “b”
        da
        cláusula 4ª até o quinto dia útil do
        mês subsequente.
    </p>
    <p>&nbsp; Cláusula 9ª - Constituem motivos para a interrupção automática do presente TCE:</p>
    <p>
        &nbsp;&nbsp; a) A falta de frequência às aulas, o abandono ou a conclusão do curso pelo estagiário.
    </p>
    <p>
        &nbsp;&nbsp; b) O não cumprimento do convencionado neste TCE.
    </p>
    <p>
        &nbsp;&nbsp; c) Desistência do estágio ou rescisão do TCE, por decisão voluntária de quaisquer das partes.
    </p>
    <p>&nbsp; Cláusula 10ª - As partes declaram-se cientes dos direitos, obrigações e penalidades aplicáveis constantes
        da Lei
        Geral de Proteção de Dados
        Pessoais (Lei 13.709/2018) ("LGPD") e obrigam-se a adotar todas as medidas razoáveis de governança, para
        garantirem, por si própria , bem como
        através de seus colaboradores, subcontratados, prestadores de serviço ou terceiros que utilizem estas
        informações protegidas, apenas na forma e
        extensão autorizada pela referida lei. Em decorrência do presente instrumento e com a finalidade única de
        atendê-lo, todo o tratamento de dados
        realizado observará, por ambas as partes, os princípios, as exigências legais e direitos dos titulares de dados
        previstos na LGPD, sem prejuízo a
        qualquer outra obrigação legal necessária para o fiel cumprimento do objeto deste termo. Em caso de qualquer
        incidente no tratamento dos dados
        pessoais, que são objeto deste acordo , a Parte que sofreu o incidente deverá enviar comunicação à outra, por
        escrito, em formato eletrônico, no prazo
        máximo de até 24 (vinte e quatro) horas contados a partir da ciência do mesmo. As partes garantem que os dados
        pessoais tratados serão mantidos
        tão somente pelo prazo de vigência deste contrato e/ou pelo prazo legal de guarda previsto na legislação
        vigente.
    </p>
    <p>&nbsp; Cláusula 11ª - Todas as partes se comprometem a observar a Política de Enfrentamento ao Assédio Moral,
        Assédio
        Sexual e a Discriminação.
    </p>
    <p>&nbsp; Cláusula 12ª - Fica compromissado entre as partes as seguintes condições básicas:</p>
    <p>
        &nbsp;&nbsp; a) Zelar pelo fiel cumprimento deste TERMO DE COMPROMISSO DE ESTÁGIO E PLANO DE ESTÁGIO;
    </p>
    <p>
        &nbsp;&nbsp; b) Este TERMO DE COMPROMISSO DE ESTÁGIO E PLANO DE ESTÁGIO vigorarão a partir de sua assinatura,
        podendo ser
        denunciado a qualquer tempo, unilateralmente, mediante comunicação escrita às demais partes, ou ser prorrogado
        por meio de ADITIVOS, respeitando o limite máximo de vigência de 2(dois) anos, exceto quando se tratar de
        estagiário portador de deficiência.
    </p>
    <p>
        &nbsp;&nbsp; c) As atividades a serem desenvolvidas pelo ESTAGIÁRIO estão de acordo com a programação curricular
        estabelecida
        para cada curso e com o itinerário formativo do educando.
    </p>
    <p>
        &nbsp;&nbsp; d) O PLANO DE ESTÁGIO, elaborado de acordo entre o ESTAGIÁRIO, a Parte Concedente e a Instituição
        de Ensino, é
        incorporado, na sua primeira fase, a este TERMO DE COMPROMISSO DE ESTÁGIO, e por meio de PLANOS DE ESTÁGIO
        ADITIVOS, incorporados às fases seguintes.
    </p>
    <p> &nbsp; Cláusula 13ª - As partes declaram-se cientes e de acordo que a assinatura do presente Termo de
        Compromisso de
        Estágio será realizada eletronicamente, nos termos da Medida Provisória 2200-2 de 2001, em seu artigo 2.º e 10º,
        dando, assim, as devidas autenticidades as assinaturas exaradas neste documento, validando todas as cláusulas
        contratuais dispostas no presente Instrumento.
    </p>
    <p>
        &nbsp;&nbsp; Parágrafo primeiro: Declaram-se cientes as partes, que os procedimentos para a assinatura
        eletrônica serão
        encaminhados pela própria ferramenta para os e-mails informados por estas.
    </p>
    <p>
        &nbsp;&nbsp; Parágrafo segundo: Declaram, ainda, de forma inequívoca, que os e-mails informados pelas partes,
        são de uso
        pessoal e particular, reconhecendo que o acesso a estes somente é feito mediante utilização de senha pessoal e
        intransferível, e diante disso, tornam-se a partir de então responsáveis pela adoção dos procedimentos
        supracitados.
    </p>
    <p>
        &nbsp;&nbsp; Parágrafo terceiro: Tendo as partes assinado o presente Termo de Compromisso de Estágio, optando
        pela adoção dos
        procedimentos supracitados, cada uma destas, receberá em seu e-mail o comprovante de sua assinatura eletrônica.
    </p>
    <p>
        Após a adoção pelas partes dos procedimentos supracitados, a assinatura do presente Termo de Compromisso de
        Estágio estará completa. Ato contínuo, cada uma das partes receberá por e-mail uma via do presente Termo
        devidamente assinada, via esta que estará acompanhada dos respectivos registros que comprovam as assinaturas
        eletrônicas.
    </p>
    <p>E, por estarem as partes certas e compromissadas, assinam o presente instrumento de forma eletrônica de acordo
        com a
        lei 14063/2020 que dispõe sobre assinaturas eletrônicas.
    </p>

    @if(isset($paraZapSign) && $paraZapSign && isset($signatarios))
        {{-- Versão dinâmica para ZapSign - Bloco compacto --}}
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
        <table style="padding-top: 125px;">
            <tr>
                <td style="text-align: center; font-family: 'Courier New', Courier, monospace;">
                    ______________________________</td>
                <td></td>
                <td style="text-align: center; font-family: 'Courier New', Courier, monospace;">
                    ______________________________</td>
            </tr>
            <tr>
                <td style="text-align: center;">Pela Concedente</td>
                <td></td>
                <td style="text-align: center;">Pela Instituição de Ensino</td>
            </tr>
        </table>
        <table style="padding-top: 125px;">
            <tr>
                <td style="text-align: center; font-family: 'Courier New', Courier, monospace;">
                    ______________________________</td>
                <td></td>
                <td style="text-align: center; font-family: 'Courier New', Courier, monospace;">
                    ______________________________</td>
            </tr>
            <tr>
                <td style="text-align: center;">Estagiário/Representante Legal</td>
                <td></td>
                <td style="text-align: center;">{{ $ebcp->nome_ebcp }} <br> (Agente de Integração)</td>
            </tr>
        </table>
    @endif

</body>

</html>