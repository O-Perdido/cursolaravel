<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InscricoesProcessoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $inscricoes;
    protected $colunas;
    protected $colunasLabels;

    public function __construct($inscricoes, $colunas)
    {
        $this->inscricoes = $inscricoes;
        $this->colunas = $colunas;
        
        // Mapeamento de colunas para labels legíveis
        $this->colunasLabels = [
            'numero_inscricao' => 'Nº Inscrição',
            'nome' => 'Nome Completo',
            'email' => 'E-mail',
            'telefone' => 'Telefone',
            'cpf' => 'CPF',
            'curso' => 'Curso',
            'instituicao' => 'Instituição de Ensino',
            'status' => 'Status',
            'data_inscricao' => 'Data da Inscrição',
            'data_nascimento' => 'Data de Nascimento',
            'idade' => 'Idade'
        ];
    }

    public function collection()
    {
        return $this->inscricoes;
    }

    public function headings(): array
    {
        $headings = [];
        
        foreach ($this->colunas as $coluna) {
            $headings[] = $this->colunasLabels[$coluna] ?? $coluna;
        }
        
        return $headings;
    }

    public function map($inscricao): array
    {
        $row = [];
        
        foreach ($this->colunas as $coluna) {
            switch ($coluna) {
                case 'numero_inscricao':
                    $row[] = $inscricao->numero_inscricao ?? 'N/A';
                    break;
                case 'nome':
                    $row[] = $inscricao->estagiario->nome_estagiario ?? 'N/A';
                    break;
                case 'email':
                    $row[] = $inscricao->estagiario->email ?? 'N/A';
                    break;
                case 'telefone':
                    $row[] = $inscricao->estagiario->numero_celular ?? $inscricao->estagiario->numero_telefone ?? 'N/A';
                    break;
                case 'cpf':
                    $row[] = $inscricao->estagiario->numero_cpf ?? 'N/A';
                    break;
                case 'curso':
                    $row[] = $inscricao->estagiario->curso ?? 'N/A';
                    break;
                case 'instituicao':
                    $row[] = $inscricao->estagiario->instituicao_ensino ?? 'N/A';
                    break;
                case 'status':
                    $row[] = ucfirst($inscricao->status_inscricao);
                    break;
                case 'data_inscricao':
                    $row[] = \Carbon\Carbon::parse($inscricao->created_at)->format('d/m/Y H:i');
                    break;
                case 'data_nascimento':
                    $row[] = $inscricao->estagiario->data_nascimento ?? 'N/A';
                    break;
                case 'idade':
                    $nascimento = $inscricao->estagiario->data_nascimento ?? null;
                    $row[] = $nascimento ? \Carbon\Carbon::createFromFormat('d/m/Y', $nascimento)->age . ' anos' : 'N/A';
                    break;
                default:
                    $row[] = '';
            }
        }
        
        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        // Estilizar cabeçalho com cores do sistema SIGE
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '102E6C'], // Azul principal do SIGE
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $sheet->getStyle('1:1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Aplicar zebra striping nas linhas
        $totalRows = $this->inscricoes->count() + 1; // +1 para incluir cabeçalho
        
        for ($i = 2; $i <= $totalRows; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:" . $this->getLastColumn() . "{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F7F9FC'], // Azul muito claro
                    ],
                ]);
            }
        }

        // Bordas em todas as células
        $sheet->getStyle("A1:" . $this->getLastColumn() . $totalRows)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'DFE6F3'],
                ],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        $widths = [];
        
        foreach ($this->colunas as $index => $coluna) {
            $letter = chr(65 + $index); // A, B, C, etc.
            
            switch ($coluna) {
                case 'numero_inscricao':
                    $widths[$letter] = 18;
                    break;
                case 'nome':
                    $widths[$letter] = 35;
                    break;
                case 'email':
                    $widths[$letter] = 30;
                    break;
                case 'telefone':
                    $widths[$letter] = 18;
                    break;
                case 'cpf':
                    $widths[$letter] = 16;
                    break;
                case 'curso':
                    $widths[$letter] = 30;
                    break;
                case 'instituicao':
                    $widths[$letter] = 35;
                    break;
                case 'status':
                    $widths[$letter] = 15;
                    break;
                case 'data_inscricao':
                    $widths[$letter] = 18;
                    break;
                case 'data_nascimento':
                    $widths[$letter] = 18;
                    break;
                case 'idade':
                    $widths[$letter] = 10;
                    break;
                default:
                    $widths[$letter] = 15;
            }
        }
        
        return $widths;
    }

    private function getLastColumn()
    {
        return chr(64 + count($this->colunas)); // A, B, C, etc.
    }
}
