<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Termo;
use App\Models\Empresa;
use App\Models\FolhaPagamento;
use App\Models\FolhasTermos;
use App\Models\Rescisao;
use App\Models\Local;
use App\Models\Configuracao;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FolhaPagamentoExport;
use Illuminate\Support\Facades\Auth;



class FolhaPagamentoController extends Controller
{
    /**
     * Prepara a remessa dividindo em lotes se necessário
     */
    public function prepararRemessa($id_folha_pagamento)
    {
        $folha = FolhaPagamento::findOrFail($id_folha_pagamento);
        $conteudoFolha = FolhasTermos::where('fk_id_folha', $id_folha_pagamento)
            ->with('termo.estagiario')
            ->get();

        // Buscar limite configurado
        $limiteDiario = Configuracao::obterLimiteDiarioRemessa();
        
        $totalGeral = $conteudoFolha->sum('total');

        // Dividir em lotes
        $lotes = $this->dividirEmLotes($conteudoFolha, $limiteDiario);

        return view('folhas_pagamento.preparar_remessa', [
            'folha' => $folha,
            'lotes' => $lotes,
            'limiteDiario' => $limiteDiario,
            'totalGeral' => $totalGeral,
            'quantidadeTotal' => $conteudoFolha->count(),
        ]);
    }

    /**
     * Divide os pagamentos em lotes respeitando o limite de valor
     */
    private function dividirEmLotes($items, $limiteValor)
    {
        $lotes = [];
        $loteAtual = [];
        $valorAtual = 0;
        $numeroLote = 1;

        foreach ($items as $item) {
            $valorItem = (float)$item->total;

            // Se adicionar este item ultrapassar o limite E já tiver itens no lote atual, criar novo lote
            if ($valorAtual + $valorItem > $limiteValor && !empty($loteAtual)) {
                $lotes[] = [
                    'numero' => $numeroLote++,
                    'items' => $loteAtual,
                    'total' => $valorAtual,
                    'quantidade' => count($loteAtual),
                    'ids' => collect($loteAtual)->pluck('id')->toArray(),
                ];
                $loteAtual = [];
                $valorAtual = 0;
            }

            $loteAtual[] = $item;
            $valorAtual += $valorItem;
        }

        // Adicionar último lote
        if (!empty($loteAtual)) {
            $lotes[] = [
                'numero' => $numeroLote,
                'items' => $loteAtual,
                'total' => $valorAtual,
                'quantidade' => count($loteAtual),
                'ids' => collect($loteAtual)->pluck('id')->toArray(),
            ];
        }

        return $lotes;
    }

    /**
     * Gera arquivo de remessa para um lote específico
     */
    public function gerarRemessaLote(Request $request, $id_folha_pagamento)
    {
        $validated = $request->validate([
            'ids_itens' => 'required|array',
            'ids_itens.*' => 'integer|exists:tb_folhas_termos,id',
            'numero_lote' => 'required|integer|min:1',
        ]);

        $folha = FolhaPagamento::findOrFail($id_folha_pagamento);
        $idsItens = $validated['ids_itens'];
        $numeroLote = $validated['numero_lote'];

        // Buscar apenas os itens selecionados
        $conteudoFolha = FolhasTermos::whereIn('id', $idsItens)
            ->where('fk_id_folha', $id_folha_pagamento)
            ->with('termo.estagiario')
            ->get();

        if ($conteudoFolha->isEmpty()) {
            return response()->json([
                'error' => true,
                'message' => 'Nenhum item encontrado para gerar a remessa.'
            ], 400);
        }

        // Gerar remessa com os itens selecionados
        return $this->gerarRemessaComItens($folha, $conteudoFolha, $numeroLote);
    }

    /**
     * Método auxiliar para gerar remessa com itens específicos
     */
    private function gerarRemessaComItens($folha, $conteudoFolha, $numeroLote = 1)
    {
        try {
            $ebcp = \App\Models\Ebcp::first();

            $sequencialArquivo = str_pad($folha->id_folha_pagamento . $numeroLote, 7, '0', STR_PAD_LEFT);
            $dataHoje = date('dmY');
            $horaAgora = date('His');

            $linhas = [];

            // HEADER DO ARQUIVO
            $linhas[] =
                '077' . // Código do banco Inter
                '0000' . // Lote de serviço
                '0' . // Tipo de registro
                str_repeat(' ', 9) . // Em branco
                '2' . // Tipo de inscrição (2 = CNPJ)
                str_pad(preg_replace('/\D/', '', $ebcp->cnpj_ebcp), 14, '0', STR_PAD_LEFT) . // CNPJ da empresa
                str_repeat(' ', 20) . // Em branco
                str_repeat('0', 5) . // Agência
                '0' . // Dígito agência
                str_pad('0', 12, '0', STR_PAD_LEFT) . // Conta
                '0' . // Dígito conta
                str_repeat(' ', 1) . // Em branco
                str_pad($ebcp->nome_ebcp, 30, ' ', STR_PAD_RIGHT) . // Nome da empresa
                str_pad('BANCO INTER', 30, ' ', STR_PAD_RIGHT) . // Nome do banco
                str_repeat(' ', 10) . // Em branco
                '1' . // Código remessa
                $dataHoje . // Data de geração
                $horaAgora . // Hora de geração
                $sequencialArquivo . // Número sequencial do arquivo
                '107' . // Versão do layout
                '01600' . // Densidade de gravação
                str_repeat(' ', 69); // Em branco até 240

            // HEADER DO LOTE
            $linhas[] =
                '077' . // Código do banco
                '0001' . // Lote de serviço
                '1' . // Tipo de registro
                'C' . // Tipo de operação
                '33' . // Tipo de serviço
                '45' . // Forma de lançamento
                '046' . // Número da versão do layout do lote
                str_repeat(' ', 1) . // Em branco
                '2' . // Tipo de inscrição (2 = CNPJ)
                str_pad(preg_replace('/\D/', '', $ebcp->cnpj_ebcp), 14, '0', STR_PAD_LEFT) . // CNPJ
                str_repeat('0', 20) . // Em branco
                '00001' . // Agência
                '9' . // Dígito agência
                str_pad('17666888', 12, '0', STR_PAD_LEFT) . // Conta
                '8' . // Dígito conta
                str_repeat(' ', 1) . // Em branco
                str_pad($ebcp->nome_ebcp, 30, ' ', STR_PAD_RIGHT) . // Nome da empresa
                str_repeat(' ', 40) . // Informação genérica/opcional
                str_repeat(' ', 30) . // Endereço
                str_pad('0', 5, '0', STR_PAD_LEFT) . // Número local
                str_repeat(' ', 15) . // Bairro
                str_repeat(' ', 20) . // Cidade
                str_pad('0', 8, '0', STR_PAD_LEFT) . // CEP
                str_repeat(' ', 5) . // Complemento CEP
                str_repeat(' ', 2) . // UF
                str_repeat(' ', 8) . // Em branco
                str_repeat(' ', 10) . // Em branco
                str_repeat(' ', 31); // Em branco até 240

            $sequencialRegistro = 1;
            $totalPagamentos = 0;
            $totalValor = 0;

            $errors = [];
            foreach ($conteudoFolha as $item) {
                $termo = $item->termo;
                $estagiario = $termo->estagiario;

                // Validações
                $tipoChave = isset($estagiario->tipo_chave_pix) ? trim((string)$estagiario->tipo_chave_pix) : '';
                $chavePixRaw = isset($estagiario->chave_pix) ? trim((string)$estagiario->chave_pix) : '';

                if (empty($tipoChave)) {
                    $errors[] = [
                        'id' => $termo->id_termo ?? ($estagiario->id ?? 'N/A'),
                        'nome' => $estagiario->nome_estagiario ?? 'N/D',
                        'mensagem' => "Tipo de chave PIX não especificado."
                    ];
                } else {
                    if (empty($chavePixRaw)) {
                        $errors[] = [
                            'id' => $termo->id_termo ?? ($estagiario->id ?? 'N/A'),
                            'nome' => $estagiario->nome_estagiario ?? 'N/D',
                            'mensagem' => "Tipo de chave PIX '{$tipoChave}' selecionado, mas a chave PIX não foi informada."
                        ];
                    }
                }
                
                $valor = number_format($item->total, 2, '', '');
                $totalPagamentos++;
                $totalValor += (float)($item->total);

                // SEGMENTO A (PIX)
                $nome = $estagiario->nome_estagiario;
                $nome_iso = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $nome);
                $nome_padded = str_pad(substr($nome_iso, 0, 30), 30, ' ', STR_PAD_RIGHT);
                
                $linhaA =
                    '077' . // Código do banco
                    '0001' . // Lote
                    '3' . // Tipo de registro
                    str_pad($sequencialRegistro, 5, '0', STR_PAD_LEFT) . // Número sequencial
                    'A' . // Código segmento
                    '0' . // Tipo de movimento
                    '00' . // Código da instrução
                    str_repeat('0', 3) . // Câmara centralizadora
                    str_repeat('0', 3) . // Código do banco favorecido
                    str_repeat('0', 5) . // Agência favorecido
                    '0' . // Dígito agência
                    str_pad('0', 12, '0', STR_PAD_LEFT) . // Conta favorecido
                    '0' . // Dígito conta
                    str_repeat(' ', 1) . // Em branco                
                    str_pad($nome_padded, 30, ' ', STR_PAD_RIGHT) . // Nome favorecido
                    str_repeat(' ', 20) . // Informação 2
                    date('dmY') . // Data pagamento
                    'BRL' . // Moeda
                    str_pad('000000000000000', 15, '0', STR_PAD_LEFT) . // Quantidade moeda
                    str_pad($valor, 15, '0', STR_PAD_LEFT) . // Valor pagamento
                    str_repeat(' ', 20) . // Nº doc banco
                    str_repeat(' ', 8) . // Data real efetivação
                    str_pad('0', 15, '0', STR_PAD_LEFT) . // Valor real efetivação
                    str_repeat(' ', 22) . // Em branco
                    str_repeat('0', 14) . // CPF/CNPJ favorecido
                    str_repeat('0', 8) . // ISPB favorecido
                    '  ' . // Tipo de conta favorecido
                    str_repeat(' ', 2) . // Em branco
                    str_repeat(' ', 29) . // Cód. ocorrências
                    str_repeat(' ', 10); // Em branco até 240
                $linhaA = mb_substr($linhaA, 0, 240, 'UTF-8');
                $linhas[] = $linhaA;

                // SEGMENTO B (PIX - chave)
                if ($estagiario->tipo_chave_pix == 'CPF') {
                    $forma_iniciacao = ' 03';
                } elseif ($estagiario->tipo_chave_pix == 'TELEFONE') {
                    $forma_iniciacao = ' 01';
                } elseif ($estagiario->tipo_chave_pix == 'EMAIL') {
                    $forma_iniciacao = ' 02';
                } elseif ($estagiario->tipo_chave_pix == 'ALEATORIA') {
                    $forma_iniciacao = ' 04';
                } else {
                    $forma_iniciacao = ' 03';
                }

                $linhaB =
                    '077' . // Código do banco
                    '0001' . // Lote
                    '3' . // Tipo de registro
                    str_pad($sequencialRegistro, 5, '0', STR_PAD_LEFT) . // Número sequencial
                    'B' . // Código segmento
                    $forma_iniciacao . // Forma de iniciação
                    '1' . // Tipo de documento favorecido
                    (
                        $forma_iniciacao === ' 03'
                            ? str_pad(preg_replace('/\D/', '', $estagiario->numero_cpf), 14, '0', STR_PAD_LEFT)
                            : str_repeat(' ', 14)
                    );

                // Chave PIX
                $chavePix = isset($estagiario->chave_pix) ? $estagiario->chave_pix : '';

                if ($forma_iniciacao === ' 01') { // telefone
                    $digits = preg_replace('/\D/', '', $chavePix);
                    if (str_starts_with($digits, '55')) {
                        $digits = preg_replace('/^55/', '', $digits);
                    }
                    if (preg_match('/(\d{11})$/', $digits, $m)) {
                        $num11 = $m[1];
                    } else {
                        $num11 = substr($digits, -11);
                        $num11 = str_pad($num11, 11, '0', STR_PAD_LEFT);
                    }
                    $chavePix = '+' . '55' . $num11;
                } elseif ($forma_iniciacao === ' 02') { // email
                    $chavePix = substr($chavePix, 0, 77);
                } elseif ($forma_iniciacao === ' 03') { // CPF
                    $chavePix = '';
                } elseif ($forma_iniciacao === ' 04') { // chave aleatória
                    $chavePix = trim($chavePix);
                }

                // TX ID
                $linhaB .= str_repeat(' ', 35);
                // Campo em branco de 60 caracteres
                $linhaB .= str_repeat(' ', 60);
                $linhaB .= str_pad($chavePix, 99, ' ', STR_PAD_RIGHT);

                // Completar até 240 caracteres
                $tamanhoAtual = mb_strlen($linhaB, 'UTF-8');
                if ($tamanhoAtual < 240) {
                    $linhaB .= str_repeat(' ', 240 - $tamanhoAtual);
                } else if ($tamanhoAtual > 240) {
                    $linhaB = mb_substr($linhaB, 0, 240, 'UTF-8');
                }
                $linhas[] = $linhaB;
                
                $sequencialRegistro++;
            }

            // TRAILER DO LOTE
            $trailerLote =
                '077' .
                '0001' .
                '5' .
                str_repeat(' ', 9) .
                str_pad($totalPagamentos * 2 + 2, 6, '0', STR_PAD_LEFT) . // Qtd registros
                str_pad(number_format($totalValor, 2, '', ''), 18, '0', STR_PAD_LEFT) . // Soma dos valores
                str_pad('0', 18, '0', STR_PAD_LEFT); // Soma quantidade de moedas
            $trailerLote = str_pad($trailerLote, 240, ' ');
            $linhas[] = $trailerLote;

            // TRAILER DO ARQUIVO
            $linhas[] =
                '077' .
                '9999' .
                '9' .
                str_repeat(' ', 9) .
                '000001' . // Qtd de lotes
                str_pad($totalPagamentos * 2 + 4, 6, '0', STR_PAD_LEFT) . // Qtd de registros
                str_repeat(' ', 211); // Em branco até 240

            // Se houver erros, retorna HTML
            /*if (!empty($errors)) {
                sort($errors);
                $formattedErrors = "<div style='font-family: Arial, sans-serif; padding: 20px;'>";
                $formattedErrors .= "<h3 style='color: #dc3545;'>⚠️ Atenção: Foram encontrados os seguintes problemas:</h3>";
                $formattedErrors .= "<table style='border-collapse: collapse; width: 100%;'>";
                $formattedErrors .= "<thead><tr>";
                $formattedErrors .= "<th style='border: 1px solid #ddd; padding: 8px; text-align: left; background: #f8d7da;'>Nº</th>";
                $formattedErrors .= "<th style='border: 1px solid #ddd; padding: 8px; text-align: left; background: #f8d7da;'>Nome</th>";
                $formattedErrors .= "<th style='border: 1px solid #ddd; padding: 8px; text-align: left; background: #f8d7da;'>Problema</th>";
                $formattedErrors .= "</tr></thead><tbody>";

                $i = 1;
                foreach ($errors as $error) {
                    $formattedErrors .= "<tr>";
                    $colId = is_array($error) && isset($error['id']) ? htmlentities($error['id'], ENT_QUOTES, 'UTF-8') : $i;
                    $colNome = is_array($error) && isset($error['nome']) ? htmlentities($error['nome'], ENT_QUOTES, 'UTF-8') : '';
                    $colMsg = is_array($error) && isset($error['mensagem']) ? htmlentities((string)$error['mensagem'], ENT_QUOTES, 'UTF-8') : htmlentities((string)$error, ENT_QUOTES, 'UTF-8');
                    $formattedErrors .= "<td style='border: 1px solid #ddd; padding: 8px; vertical-align: top;'>" . $colId . "</td>";
                    $formattedErrors .= "<td style='border: 1px solid #ddd; padding: 8px; vertical-align: top;'>" . $colNome . "</td>";
                    $formattedErrors .= "<td style='border: 1px solid #ddd; padding: 8px; vertical-align: top;'>" . $colMsg . "</td>";
                    $formattedErrors .= "</tr>";
                    $i++;
                }

                $formattedErrors .= "</tbody></table>";
                $formattedErrors .= "<p style='margin-top: 20px; color: #666;'>Total de estagiários com pendências: " . count($errors) . "</p>";
                $formattedErrors .= "</div>";

                return response($formattedErrors)->header('Content-Type', 'text/html; charset=utf-8');
            }*/

            // Gerar arquivo
            $linhas240 = array_map(function ($linha) {
                return mb_substr($linha, 0, 240, 'UTF-8');
            }, $linhas);
            $conteudo = implode("\r\n", $linhas240) . "\r\n";
            
            $nomeArquivo = 'CI240_' . str_pad($numeroLote, 3, '0', STR_PAD_LEFT) . '_' . $sequencialArquivo . '.REM';
            
            return response($conteudo)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="' . $nomeArquivo . '"');
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function gerarRemessa($id_folha_pagamento)
    {
        try {
            $folha = FolhaPagamento::findOrFail($id_folha_pagamento);
            $conteudoFolha = FolhasTermos::where('fk_id_folha', $id_folha_pagamento)->get();
            $ebcp = \App\Models\Ebcp::first();

        $sequencialArquivo = str_pad($folha->id_folha_pagamento, 7, '0', STR_PAD_LEFT);
        $dataHoje = date('dmY');
        $horaAgora = date('His');

        $linhas = [];

        // HEADER DO ARQUIVO
        $linhas[] =
            '077' . // Código do banco Inter
            '0000' . // Lote de serviço
            '0' . // Tipo de registro
            str_repeat(' ', 9) . // Em branco
            '2' . // Tipo de inscrição (2 = CNPJ)
            str_pad(preg_replace('/\D/', '', $ebcp->cnpj_ebcp), 14, '0', STR_PAD_LEFT) . // CNPJ da empresa
            str_repeat(' ', 20) . // Em branco
            str_repeat('0', 5) . // Agência (ajustar se necessário)
            '0' . // Dígito agência
            str_pad('0', 12, '0', STR_PAD_LEFT) . // Conta (ajustar se necessário)
            '0' . // Dígito conta
            str_repeat(' ', 1) . // Em branco
            str_pad($ebcp->nome_ebcp, 30, ' ', STR_PAD_RIGHT) . // Nome da empresa
            str_pad('BANCO INTER', 30, ' ', STR_PAD_RIGHT) . // Nome do banco
            str_repeat(' ', 10) . // Em branco
            '1' . // Código remessa
            $dataHoje . // Data de geração
            $horaAgora . // Hora de geração
            $sequencialArquivo . // Número sequencial do arquivo
            '107' . // Versão do layout
            '01600' . // Densidade de gravação
            str_repeat(' ', 69); // Em branco até 240

        // HEADER DO LOTE
        $linhas[] =
            '077' . // Código do banco
            '0001' . // Lote de serviço
            '1' . // Tipo de registro
            'C' . // Tipo de operação
            '33' . // Tipo de serviço
            '45' . // Forma de lançamento
            '046' . //Número da versão do layout do lote
            str_repeat(' ', 1) . // Em branco
            '2' . // Tipo de inscrição (2 = CNPJ)
            str_pad(preg_replace('/\D/', '', $ebcp->cnpj_ebcp), 14, '0', STR_PAD_LEFT) . // CNPJ
            str_repeat('0', 20) . // Em branco
            '00001' . // Agência (ajustar se necessário)
            '9' . // Dígito agência
            str_pad('17666888', 12, '0', STR_PAD_LEFT) . // Conta (ajustar se necessário)
            '8' . // Dígito conta
            str_repeat(' ', 1) . // Em branco
            str_pad($ebcp->nome_ebcp, 30, ' ', STR_PAD_RIGHT) . // Nome da empresa
            str_repeat(' ', 40) . // Informação genérica/opcional
            str_repeat(' ', 30) . // Endereço
            str_pad('0', 5, '0', STR_PAD_LEFT) . // Número local
            str_repeat(' ', 15) . // Bairro
            str_repeat(' ', 20) . // Cidade
            str_pad('0', 8, '0', STR_PAD_LEFT) . // CEP
            str_repeat(' ', 5) . // Complemento CEP
            str_repeat(' ', 2) . // UF
            str_repeat(' ', 8) . // Em branco
            str_repeat(' ', 10) . // Em branco
            str_repeat(' ', 31); // Em branco até 240

        $sequencialRegistro = 1;
        $totalPagamentos = 0;
        $totalValor = 0;

        $errors = [];
        foreach ($conteudoFolha as $item) {
            $termo = $item->termo;
            $estagiario = $termo->estagiario;

            // Validações simplificadas: verificar se existe tipo de chave PIX; se houver tipo, checar apenas se a chave PIX está preenchida
            $tipoChave = isset($estagiario->tipo_chave_pix) ? trim((string)$estagiario->tipo_chave_pix) : '';
            $chavePixRaw = isset($estagiario->chave_pix) ? trim((string)$estagiario->chave_pix) : '';

            if (empty($tipoChave)) {
                $errors[] = [
                    'id' => $termo->id_termo ?? ($estagiario->id ?? 'N/A'),
                    'nome' => $estagiario->nome_estagiario ?? 'N/D',
                    'mensagem' => "Tipo de chave PIX não especificado."
                ];
            } else {
                // Se o tipo existe, apenas assegura que a chave PIX em si não está vazia
                if (empty($chavePixRaw)) {
                    $errors[] = [
                        'id' => $termo->id_termo ?? ($estagiario->id ?? 'N/A'),
                        'nome' => $estagiario->nome_estagiario ?? 'N/D',
                        'mensagem' => "Tipo de chave PIX '{$tipoChave}' selecionado, mas a chave PIX não foi informada."
                    ];
                }
            }
            
            $valor = number_format($item->total, 2, '', '');
            $totalPagamentos++;
            $totalValor += (float)($item->total);

            // SEGMENTO A (PIX)
            $nome = $estagiario->nome_estagiario;
            $nome_iso = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $nome);
            $nome_padded = str_pad(substr($nome_iso, 0, 30), 30, ' ', STR_PAD_RIGHT);
            
            $linhaA =
                '077' . // Código do banco
                '0001' . // Lote
                '3' . // Tipo de registro
                str_pad($sequencialRegistro, 5, '0', STR_PAD_LEFT) . // Mesmo número sequencial para A e B
                'A' . // Código segmento
                '0' . // Tipo de movimento
                '00' . // Código da instrução
                str_repeat('0', 3) . // Câmara centralizadora
                str_repeat('0', 3) . // Código do banco favorecido (não usado para PIX)
                str_repeat('0', 5) . // Agência favorecido
                '0' . // Dígito agência
                str_pad('0', 12, '0', STR_PAD_LEFT) . // Conta favorecido
                '0' . // Dígito conta
                str_repeat(' ', 1) . // Em branco                
                str_pad($nome_padded, 30, ' ', STR_PAD_RIGHT) . // Nome favorecido
                str_repeat(' ', 20) . // Informação 2
                date('dmY') . // Data pagamento no formato DDMMAAAA
                'BRL' . // Moeda
                str_pad('000000000000000', 15, '0', STR_PAD_LEFT) . // Quantidade moeda
                str_pad($valor, 15, '0', STR_PAD_LEFT) . // Valor pagamento
                str_repeat(' ', 20) . // Nº doc atribuído pelo banco
                str_repeat(' ', 8) . // Data real efetivação (retorno)
                str_pad('0', 15, '0', STR_PAD_LEFT) . // Valor real efetivação (retorno)
                str_repeat(' ', 22) . // Em branco
                str_repeat('0', 14) . // CPF/CNPJ favorecido (opcional)
                str_repeat('0', 8) . // ISPB favorecido (opcional)
                '  ' . // Tipo de conta favorecido (01 = corrente)
                str_repeat(' ', 2) . // Em branco
                str_repeat(' ', 29) . // Cód. ocorrências para retorno
                str_repeat(' ', 10); // Em branco até 240
            $linhaA = mb_substr($linhaA, 0, 240, 'UTF-8');
            $linhas[] = $linhaA;

            // SEGMENTO B (PIX - chave)
            //Função para gerar a forma de iniciação baseada no tipo da chave PIX
            if ($estagiario->tipo_chave_pix == 'CPF') {
                $forma_iniciacao = ' 03';
            } elseif ($estagiario->tipo_chave_pix == 'TELEFONE') {
                $forma_iniciacao = ' 01';
            } elseif ($estagiario->tipo_chave_pix == 'EMAIL') {
                $forma_iniciacao = ' 02';
            } elseif ($estagiario->tipo_chave_pix == 'ALEATORIA') {
                $forma_iniciacao = ' 04';
            } else {
                // Usar um valor padrão para permitir que o loop continue
                $forma_iniciacao = ' 03';
            }

            $linhaB =
                '077' . // Código do banco
                '0001' . // Lote
                '3' . // Tipo de registro
                str_pad($sequencialRegistro, 5, '0', STR_PAD_LEFT) . // Mesmo número sequencial para A e B
                'B' . // Código segmento
                $forma_iniciacao . // Forma de iniciação (03 = CPF/CNPJ, 01 = telefone, 02 = email, 04 = aleatória)
                '1' . // Tipo de documento favorecido (1 = CPF, 2 = CNPJ)
                (
                    $forma_iniciacao === ' 03'
                        ? str_pad(preg_replace('/\D/', '', $estagiario->numero_cpf), 14, '0', STR_PAD_LEFT)
                        : str_repeat(' ', 14)
                ); // CPF/CNPJ favorecido (preenche apenas quando forma_iniciacao == '03')*/
                //str_pad(preg_replace('/\D/', '', $estagiario->numero_cpf), 14, '0', STR_PAD_LEFT);

            // Chave PIX (até 99 caracteres, alinhada à esquerda)
            $chavePix = isset($estagiario->chave_pix) ? $estagiario->chave_pix : '';

            if ($forma_iniciacao === ' 01') { // telefone
                // Normaliza várias formas de entrada e produz +55XXXXXXXXXXX (11 dígitos após o 55)
                $digits = preg_replace('/\D/', '', $chavePix); // remove tudo que não é dígito
                // Remove código do país se já vier (55)
                if (str_starts_with($digits, '55')) {
                    $digits = preg_replace('/^55/', '', $digits);
                }
                // Pega os últimos 11 dígitos (DDD + 9 dígitos). Se houver menos, pad com zeros à esquerda.
                if (preg_match('/(\d{11})$/', $digits, $m)) {
                    $num11 = $m[1];
                } else {
                    $num11 = substr($digits, -11);
                    $num11 = str_pad($num11, 11, '0', STR_PAD_LEFT);
                }
                $chavePix = '+' . '55' . $num11;
            } elseif ($forma_iniciacao === ' 02') { // email
                // Limita email a 77 caracteres
                $chavePix = substr($chavePix, 0, 77);
            } elseif ($forma_iniciacao === ' 03') { // CPF
                // Se for CPF, deixa vazio já que o CPF foi incluído anteriormente
                $chavePix = '';
            } elseif ($forma_iniciacao === ' 04') { // chave aleatória
                // Mantém formato UUID XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
                $chavePix = trim($chavePix);
            }
            // TX ID (em branco, opcional)
            $linhaB .= str_repeat(' ', 35);

            // Campo em branco de 60 caracteres
            $linhaB .= str_repeat(' ', 60);

            $linhaB .= str_pad($chavePix, 99, ' ', STR_PAD_RIGHT);

            // Demais campos em branco até 240 caracteres
            $tamanhoAtual = mb_strlen($linhaB, 'UTF-8');
            if ($tamanhoAtual < 240) {
                $linhaB .= str_repeat(' ', 240 - $tamanhoAtual);
            } else if ($tamanhoAtual > 240) {
                $linhaB = mb_substr($linhaB, 0, 240, 'UTF-8');
            }
            $linhas[] = $linhaB;
            
            // Incrementa o sequencial apenas APÓS adicionar tanto A quanto B
            $sequencialRegistro++;
        }

        // TRAILER DO LOTE

        $trailerLote =
            '077' .
            '0001' .
            '5' .
            str_repeat(' ', 9) .
            str_pad($totalPagamentos * 2 + 2, 6, '0', STR_PAD_LEFT) . // Qtd registros do lote (2 por pagamento + header + trailer)
            str_pad(number_format($totalValor, 2, '', ''), 18, '0', STR_PAD_LEFT) . // Soma dos valores
            str_pad('0', 18, '0', STR_PAD_LEFT); // Soma quantidade de moedas
        // Trailer do lote deve ter 240 caracteres
        $trailerLote = str_pad($trailerLote, 240, ' ');
        $linhas[] = $trailerLote;

        // TRAILER DO ARQUIVO
        $linhas[] =
            '077' .
            '9999' .
            '9' .
            str_repeat(' ', 9) .
            '000001' . // Qtd de lotes (1)
            str_pad($totalPagamentos * 2 + 4, 6, '0', STR_PAD_LEFT) . // Qtd de registros do arquivo
            str_repeat(' ', 211); // Em branco até 240

            // Se houver erros, retorna todos eles formatados em uma tabela HTML
        /*if (!empty($errors)) {
            // Ordena os nomes alfabeticamente
            sort($errors);

            // Começa a construir a tabela HTML
            $formattedErrors = "<div style='font-family: Arial, sans-serif; padding: 20px;'>";
            $formattedErrors .= "<h3 style='color: #dc3545;'>⚠️ Atenção: Foram encontrados os seguintes problemas:</h3>";
            $formattedErrors .= "<table style='border-collapse: collapse; width: 100%;'>";
            $formattedErrors .= "<thead>";
            $formattedErrors .= "<tr>";
            $formattedErrors .= "<th style='border: 1px solid #ddd; padding: 8px; text-align: left; background: #f8d7da;'>Nº</th>";
            $formattedErrors .= "<th style='border: 1px solid #ddd; padding: 8px; text-align: left; background: #f8d7da;'>Nome</th>";
            $formattedErrors .= "<th style='border: 1px solid #ddd; padding: 8px; text-align: left; background: #f8d7da;'>Problema</th>";
            $formattedErrors .= "</tr>";
            $formattedErrors .= "</thead>";
            $formattedErrors .= "<tbody>";

            $i = 1;
            foreach ($errors as $error) {
                // Se o item for um array com id/nome/mensagem, renderiza as colunas correspondentes
                $formattedErrors .= "<tr>";
                $colId = is_array($error) && isset($error['id']) ? htmlentities($error['id'], ENT_QUOTES, 'UTF-8') : $i;
                $colNome = is_array($error) && isset($error['nome']) ? htmlentities($error['nome'], ENT_QUOTES, 'UTF-8') : '';
                $colMsg = is_array($error) && isset($error['mensagem']) ? htmlentities((string)$error['mensagem'], ENT_QUOTES, 'UTF-8') : htmlentities((string)$error, ENT_QUOTES, 'UTF-8');

                $formattedErrors .= "<td style='border: 1px solid #ddd; padding: 8px; vertical-align: top;'>" . $colId . "</td>";
                $formattedErrors .= "<td style='border: 1px solid #ddd; padding: 8px; vertical-align: top;'>" . $colNome . "</td>";
                $formattedErrors .= "<td style='border: 1px solid #ddd; padding: 8px; vertical-align: top;'>" . $colMsg . "</td>";
                $formattedErrors .= "</tr>";
                $i++;
            }

            $formattedErrors .= "</tbody>";
            $formattedErrors .= "</table>";
            $formattedErrors .= "<p style='margin-top: 20px; color: #666;'>Total de estagiários com pendências: " . count($errors) . "</p>";
            $formattedErrors .= "</div>";

            return response($formattedErrors)
                ->header('Content-Type', 'text/html; charset=utf-8');
        }
*/
        // Se não houver erros, continua com a geração do arquivo
        // Garante que cada linha tenha exatamente 240 caracteres
        $linhas240 = array_map(function ($linha) {
            return mb_substr($linha, 0, 240, 'UTF-8');
        }, $linhas);
        $conteudo = implode("\r\n", $linhas240) . "\r\n";
        // O número sequencial de remessa é o mesmo do header (campo $sequencialArquivo)
        $nomeArquivo = 'CI240_001_' . $sequencialArquivo . '.REM';
        return response($conteudo)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $nomeArquivo . '"');
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    //
    public function index(Request $request)
    {
        // Query base com eager loading essencial
        $query = FolhaPagamento::with(['empresa', 'local']);

        // Filtros
        if ($request->filled('empresa')) {
            $query->where('fk_id_empresa', $request->input('empresa'));
        }
        if ($request->filled('local')) {
            $query->where('fk_id_local', $request->input('local'));
        }
        if ($request->filled('mes')) {
            $query->where('mes_referencia', $request->input('mes'));
        }
        if ($request->filled('ano')) {
            $query->where('ano_referencia', $request->input('ano'));
        }
        if ($request->filled('vencimento_inicial')) {
            $query->whereDate('vencimento_folha', '>=', $request->input('vencimento_inicial'));
        }
        if ($request->filled('vencimento_final')) {
            $query->whereDate('vencimento_folha', '<=', $request->input('vencimento_final'));
        }
        // Período de emissão (data_folha)
        if ($request->filled('emissao_inicial')) {
            $query->whereDate('data_folha', '>=', $request->input('emissao_inicial'));
        }
        if ($request->filled('emissao_final')) {
            $query->whereDate('data_folha', '<=', $request->input('emissao_final'));
        }

        // Se usuário for de nível empresa, restringe às folhas da própria unidade
        if (Auth::check() && Auth::user()->nivel === 'empresa') {
            $query->where('fk_id_empresa', Auth::user()->fk_id_empresa);
        }

        // Ordenação (mais recentes primeiro pelo id)
        $query->orderByDesc('id_folha_pagamento');

        // Itens por página (permitidos)
        $perPageParam = $request->input('per_page');
        $allowed = ['25', '50', '100', '200', 'all'];
        if (!in_array((string)($perPageParam ?? ''), $allowed, true)) {
            $perPageParam = '25';
        }
        if ($perPageParam === 'all') {
            $total = (clone $query)->count();
            $perPage = max(1, (int)$total);
        } else {
            $perPage = (int)$perPageParam;
        }

        $folhas = $query->paginate($perPage)->appends($request->query());

        // Empresas ordenadas por nome para select
        $empresas = Empresa::orderBy('nome_empresa', 'asc')->get();

        // Anos disponíveis a partir das folhas existentes (fallback para ano atual)
        $anosDisponiveis = FolhaPagamento::select('ano_referencia')->distinct()->orderBy('ano_referencia','desc')->pluck('ano_referencia');
        if ($anosDisponiveis->isEmpty()) {
            $anosDisponiveis = collect([now()->year]);
        }

        // Meses (array simples para view)
        $meses = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
            7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];

        return view('folhas_pagamento.index', [
            'folhas' => $folhas,
            'empresas' => $empresas,
            'anosDisponiveis' => $anosDisponiveis,
            'meses' => $meses,
        ]);
    }

    public function show($id)
    {
        // Lógica para mostrar os detalhes de uma folha de pagamento específica
        $folha = FolhaPagamento::findOrFail($id);
        $conteudoFolha = FolhasTermos::where('fk_id_folha', $id)->get();

        return view('folhas_pagamento.show', compact('folha', 'conteudoFolha'));
    }

    public function create($id_folha_pagamento)
    {
        // Lógica para exibir o formulário de criação de uma nova folha de pagamento
        $folha = FolhaPagamento::find($id_folha_pagamento);

        $conteudoFolha = FolhasTermos::where('fk_id_folha', $folha->id_folha_pagamento)->get();

        $diasPadraoCalculo = \App\Models\Configuracao::obter('dias_padrao_calculo_folha', 30);

        return view('folhas_pagamento.create', compact('folha', 'conteudoFolha', 'diasPadraoCalculo'));
    }

    public function edit($id_folha_pagamento)
    {
        // Lógica para exibir o formulário de edição de uma folha de pagamento
        $folha = FolhaPagamento::findOrFail($id_folha_pagamento);
        $conteudoFolha = FolhasTermos::where('fk_id_folha', $folha->id_folha_pagamento)->get();

        $diasPadraoCalculo = \App\Models\Configuracao::obter('dias_padrao_calculo_folha', 30);

        return view('folhas_pagamento.edit', compact('folha', 'conteudoFolha', 'diasPadraoCalculo'));
    }

    public function store(Request $request)
    {
        // Lógica para armazenar uma nova folha de pagamento
        // 1) Recebemos os parâmetros do formulário da modal de geração de folha
        // 2) Entre eles, agora aceitamos também 'tipo_calculo_recesso', que define o modo de cálculo do recesso:
        //    - 'original': mantém a regra antiga (sem olhar saldo de recesso)
        //    - 'com_saldo': paga apenas os dias NÃO utilizados de recesso (saldo_recesso)
        $data = $request->validate([
            'numero_folha' => 'nullable|integer',
            'data_folha' => 'nullable|date',
            'vencimento_folha' => 'nullable|date',
            'ano_referencia' => 'nullable|integer',
            'mes_referencia' => 'nullable|integer',
            'fk_id_empresa' => 'nullable|integer',
            'fk_id_local' => 'nullable|integer',
            'tipo_calculo_auxilio_transporte' => 'nullable|in:mensal,diario',
            'dias_uteis' => 'nullable|integer',
            'tipo_calculo_recesso' => 'nullable|in:original,com_saldo',
        ]);

        // Se vier local, garante que pertence à empresa selecionada
        if (!empty($data['fk_id_local'])) {
            $localPertence = Local::where('id_local', $data['fk_id_local'] ?? 0)
                ->where('fk_id_empresa', $data['fk_id_empresa'] ?? 0)
                ->exists();
            if (!$localPertence) {
                return redirect()->back()->with('error', 'O local selecionado não pertence à unidade concedente escolhida.');
            }
        }

        // Conta quantas folhas já existem para o ano selecionado se for 0, coloca 1 no campo numero_folha, se for maior que 0, incrementa mais 1
        $numeroFolha = FolhaPagamento::where('ano_referencia', $data['ano_referencia'])
            ->count();
        if ($numeroFolha == 0) {
            $data['numero_folha'] = 1;
        } else {
            $data['numero_folha'] = $numeroFolha + 1;
        }

        // Define o campo data_folha como a data atual (data de emissão da folha)
        $data['data_folha'] = now();

        // Define tipo de cálculo de recesso padrão caso não informado pelo formulário
        // (back-compat: se a UI antiga não enviar, permanecemos no comportamento "original")
        if (!isset($data['tipo_calculo_recesso']) || empty($data['tipo_calculo_recesso'])) {
            $data['tipo_calculo_recesso'] = 'original';
        }

        $folha = FolhaPagamento::create($data);

        // Lógica para exibir o formulário de criação de uma nova folha de pagamento
        $folha = FolhaPagamento::find($folha->id_folha_pagamento);

        // Seleciona os termos (contratos) da empresa no intervalo do mês de referência da folha.
        // Critério: termos que tenham iniciado até o último dia do mês e não terminados antes do primeiro dia do mês.
        // Assim, pegamos contratos que estiveram vigentes em algum momento dentro do mês/ano de referência.
        // Calcula o primeiro e o último dia do mês/ano de referência
        $primeiroDiaMes = \Carbon\Carbon::create($folha->ano_referencia, $folha->mes_referencia, 1)->toDateString();
        $ultimoDiaMes = \Carbon\Carbon::create($folha->ano_referencia, $folha->mes_referencia, 1)->endOfMonth()->toDateString();

        $termosQuery = Termo::where('fk_id_empresa', $folha->fk_id_empresa)
            ->where('data_fim_estagio', '>=', $primeiroDiaMes)
            ->where('data_inicio_estagio', '<=', $ultimoDiaMes);

        // Filtro opcional por local (se informado no formulário)
        if (!empty($data['fk_id_local'])) {
            $termosQuery->where('fk_id_local', $data['fk_id_local']);
        }

        $termos = $termosQuery->get();


        //Cria os registros na tabela folhas_termos para cada termo relacionado à folha
        if ($termos->isEmpty()) {
            return redirect()->back()->with('error', 'Nenhum termo encontrado para os filtros selecionados (empresa/local).');
        } else {
            $total_recesso = 0; // Acumulador do total de recesso desta folha
            foreach ($termos as $termo) {
                // Para cada termo, calculamos o valor de recesso
                $rescisao = Rescisao::where('fk_id_termo', $termo->id_termo)->first();
                $valorRecesso = 0; // Inicializa o valor padrão

                // SEPARAÇÃO ENTRE OS MODOS DE CÁLCULO
                // -----------------------------------
                // Modo "original":
                //   - Mantém a regra antiga. Não considera saldo de recesso. Fator = 1 (100% do que seria devido).
                // Modo "com_saldo":
                //   - Considera o saldo de recesso do termo (dias não utilizados).
                //   - O pagamento é proporcional ao saldo: fator = saldo_recesso / 30.
                //   - Exemplos: saldo=30 => fator=1 (paga tudo); saldo=0 => fator=0 (não paga nada); saldo=15 => fator=0.5.
                $fatorDiasNaoUtilizados = 1; // Padrão para cálculo original (100%)
                if ($folha->tipo_calculo_recesso === 'com_saldo') {
                    // Cálculo considerando o saldo de recesso DISPONÍVEL (dias NÃO utilizados)
                    // Se saldo = 30 (nenhum dia tirado), paga 100% => fator 1
                    // Se saldo = 0 (todos os 30 dias tirados), não paga nada => fator 0
                    $saldo = (int) ($termo->saldo_recesso ?? 0);
                    $saldo = max(0, min(30, $saldo));
                    $fatorDiasNaoUtilizados = $saldo / 30;
                }

                // CASOS EM QUE HÁ DIREITO AO RECEBIMENTO DE RECESSO NA FOLHA
                // 1) Rescisão: paga o proporcional ao período trabalhado até a rescisão
                // 2) Completar 1 ano dentro do mês de referência: paga o "cheio" do ano, ponderado pelo fator acima
                if (
                    $rescisao &&
                    !empty($termo->data_inicio_estagio) &&
                    !empty($rescisao->data_rescisao) &&
                    strtotime($termo->data_inicio_estagio) !== false &&
                    strtotime($rescisao->data_rescisao) !== false
                ) {
                    // RESCISÃO: calcula os dias trabalhados (inclusivo) entre o início e a data de rescisão
                    $diasContrato = \Carbon\Carbon::parse($termo->data_inicio_estagio)
                        ->diffInDays(\Carbon\Carbon::parse($rescisao->data_rescisao)) + 1; // inclui o dia inicial

                    // Valor devido proporcional aos dias trabalhados, ponderado pelo fator (modo original ou com saldo)
                    $valorRecesso = ($termo->valor_bolsa / 360) * $diasContrato * $fatorDiasNaoUtilizados;
                }
                // verifica se o termo completou um ano até o ultimo dia do mês de referência da folha e calcula o valor de recesso
                else if (
                    !empty($termo->data_inicio_estagio) &&
                    strtotime($termo->data_inicio_estagio) !== false &&
                    \Carbon\Carbon::parse($termo->data_inicio_estagio)->addYear()->isSameMonth(\Carbon\Carbon::create($folha->ano_referencia, $folha->mes_referencia, 1))
                ) {
                    // 1 ANO COMPLETO: paga o "cheio" do ano, ponderado pelo fator (modo original ou com saldo)
                    $valorRecesso = $termo->valor_bolsa * $fatorDiasNaoUtilizados; // 360 dias de referência
                } else {
                    $valorRecesso = 0; // Se não houver rescisão ou não completar um ano, o valor de recesso é 0
                }

                // Acumula o total de recesso da folha
                $total_recesso += $valorRecesso;


                // Cria o registro na tabela folhas_termos
                FolhasTermos::create([
                    'fk_id_termo' => $termo->id_termo,
                    'fk_id_folha' => $folha->id_folha_pagamento,
                    'valor_recesso' => $valorRecesso,
                    'valor_bolsa' => $termo->valor_bolsa,
                    'valor_auxilio_transporte' => $termo->auxilio_transporte
                ]);
            }

            // Atualiza e persiste o total de recesso da folha de pagamento
            $folha->total_recesso = $total_recesso;
            $folha->save();
        }

        return redirect()->route('folhas.create', ['id_folha_pagamento' => $folha->id_folha_pagamento]);
    }

    public function storeall(Request $request, $id_folha_pagamento)
    {
        // Lógica para salvar o resto dos dados da folha de pagamento

        $conteudoFolha = FolhasTermos::where('fk_id_folha', $request->id_folha_pagamento)->get();

        foreach ($conteudoFolha as $item) {
            // Salvar os dados necessários (dias_trabalhados, bolsa_mes, auxilio_transporte_mes, taxa_adm, descontos e total)
            FolhasTermos::where('id', $item->id)->update([
                'dias_trabalhados' => $request->input('dias_trabalhados_' . $item->id),
                'valor_bolsa_mes' => $request->input('bolsa_mes_' . $item->id),
                'valor_auxilio_transporte_mes' => $request->input('auxilio_transporte_mes_' . $item->id),
                'taxa_adm' => $request->input('taxa_adm_' . $item->id),
                'descontos' => $request->input('descontos_' . $item->id),
                'total' => $request->input('total_' . $item->id),
            ]);
        }

        // Salvar o resto dos dados da folha de pagamento
        FolhaPagamento::where('id_folha_pagamento', $id_folha_pagamento)->update([
            'total_bolsa_mes' => $request->input('total_bolsa_mes'),
            'total_auxilio_transporte_mes' => $request->input('total_auxilio_transporte_mes'),
            'total_taxa_adm' => $request->input('total_taxa_adm'),
            'total_folha' => $request->input('total_geral'),
        ]);

        return redirect()->route('folhas.index')->with('success', 'Folha de pagamento gerada com sucesso!');
    }

    public function storeallBatch(Request $request, $id_folha_pagamento)
    {
        // Endpoint AJAX para salvar dados em lotes
        try {
            $registros = $request->input('registros', []);
            
            foreach ($registros as $registro) {
                FolhasTermos::where('id', $registro['id'])->update([
                    'dias_trabalhados' => $registro['dias_trabalhados'] ?? 0,
                    'valor_bolsa_mes' => $registro['valor_bolsa_mes'] ?? 0,
                    'valor_auxilio_transporte_mes' => $registro['valor_auxilio_transporte_mes'] ?? 0,
                    'taxa_adm' => $registro['taxa_adm'] ?? 0,
                    'descontos' => $registro['descontos'] ?? 0,
                    'total' => $registro['total'] ?? 0,
                ]);
            }

            return response()->json([
                'success' => true,
                'processados' => count($registros)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function finalizeFolha(Request $request, $id_folha_pagamento)
    {
        // Endpoint AJAX para finalizar e salvar totais da folha
        try {
            FolhaPagamento::where('id_folha_pagamento', $id_folha_pagamento)->update([
                'total_bolsa_mes' => $request->input('total_bolsa_mes', 0),
                'total_auxilio_transporte_mes' => $request->input('total_auxilio_transporte_mes', 0),
                'total_taxa_adm' => $request->input('total_taxa_adm', 0),
                'total_folha' => $request->input('total_geral', 0),
            ]);

            return response()->json([
                'success' => true,
                'redirect_url' => route('folhas.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id_folha_pagamento)
    {
        // Lógica para atualizar uma folha de pagamento existente
        $folha = FolhaPagamento::findOrFail($id_folha_pagamento);
        $conteudoFolha = FolhasTermos::where('fk_id_folha', $id_folha_pagamento)->get();

        foreach ($conteudoFolha as $item) {
            // Atualizar os dados necessários (dias_trabalhados, bolsa_mes, auxilio_transporte_mes, taxa_adm, descontos e total)
            FolhasTermos::where('id', $item->id)->update([
                'dias_trabalhados' => $request->input('dias_trabalhados_' . $item->id),
                'valor_bolsa_mes' => $request->input('bolsa_mes_' . $item->id),
                'valor_auxilio_transporte_mes' => $request->input('auxilio_transporte_mes_' . $item->id),
                'taxa_adm' => $request->input('taxa_adm_' . $item->id),
                'descontos' => $request->input('descontos_' . $item->id),
                'total' => $request->input('total_' . $item->id),
            ]);
        }

        // Atualizar os totais da folha de pagamento
        $folha->update([
            'total_bolsa_mes' => $request->input('total_bolsa_mes'),
            'total_auxilio_transporte_mes' => $request->input('total_auxilio_transporte_mes'),
            'total_taxa_adm' => $request->input('total_taxa_adm'),
            'total_folha' => $request->input('total_geral'),
        ]);

        return redirect()->route('folhas.index')->with('success', 'Folha de pagamento atualizada com sucesso!');
    }

    public function destroy($id_folha_pagamento)
    {
        // Lógica para excluir uma folha de pagamento
        $folha = FolhaPagamento::findOrFail($id_folha_pagamento);

        // Excluir os registros relacionados na tabela folhas_termos
        FolhasTermos::where('fk_id_folha', $id_folha_pagamento)->delete();

        $folha->delete();

        return redirect()->route('folhas.index')->with('success', 'Folha de pagamento excluída com sucesso!');
    }

    public function gerarPdf($id_folha_pagamento)
    {
        // Lógica para gerar o PDF da folha de pagamento
        $folha = FolhaPagamento::findOrFail($id_folha_pagamento);
        $conteudoFolha = FolhasTermos::where('fk_id_folha', $id_folha_pagamento)->get();
        $linklogo = public_path('images/logo_com_informacoes.png');

        // Gerar o PDF usando DomPDF
        $pdf = PDF::loadView('folhas_pagamento.gerarPdfFolhaPagamento', compact('folha', 'conteudoFolha', 'linklogo'));
        $pdf->setPaper([0, 0, 595.28, 841.89], 'landscape');
        $pdf->getDOMPdf()->set_option('isPhpEnabled', true);

        $numero_folha = $folha->numero_folha;
        return $pdf->stream("folha_pagamento_$numero_folha.pdf");


        // $pdf = Pdf::loadView('termos.gerarPdfRelatorioTermo', ['termos' => $termos, 'linklogo' => $linklogo, 'ebcp' => $ebcp, 'request' => $request, 'empresas' => $empresas, 'escolas' => $escolas])
        //     ->setPaper([0, 0, 595.28, 841.89], 'landscape');
        // $pdf->getDOMPdf()->set_option('isPhpEnabled', true);

        // return $pdf->stream('relatorio_termos.pdf');
    }


    public function export($id_folha_pagamento): mixed
    {
        return Excel::download(new FolhaPagamentoExport($id_folha_pagamento), 'relatorio_termos.xlsx');
    }

    public function gerarRecibo($id_folha_pagamento, $id_conteudo)
    {
        // Lógica para gerar o PDF do recibo de um estagiário de uma folha de pagamento específica
        $folha = FolhaPagamento::findOrFail($id_folha_pagamento);
        $conteudo = FolhasTermos::findOrFail($id_conteudo);
        $linklogo = public_path('images/logo_com_informacoes.png');

        // Gerar o PDF usando DomPDF
        $pdf = PDF::loadView('folhas_pagamento.gerarPdfRecibo', compact('folha', 'conteudo', 'linklogo'));
        $pdf->setPaper([0, 0, 595.28, 841.89], 'portrait');
        $pdf->getDOMPdf()->set_option('isPhpEnabled', true);

        return $pdf->stream("recibo_{$folha->mes_referencia}_de_{$folha->ano_referencia}_estagiario_{$conteudo->termo->estagiario->nome_estagiario}.pdf");
    }
}
