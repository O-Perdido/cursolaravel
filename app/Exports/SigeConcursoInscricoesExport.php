<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SigeConcursoInscricoesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly Collection $linhas)
    {
    }

    public function collection(): Collection
    {
        return $this->linhas;
    }

    public function headings(): array
    {
        return [
            'Nº inscrição',
            'Nome',
            'CPF',
            'Modalidade',
            'Local de prova',
            'Status inscrição',
            'Status isenção',
        ];
    }

    public function map($linha): array
    {
        return [
            $linha['numero_inscricao'],
            $linha['nome'],
            $linha['cpf'],
            $linha['modalidade'],
            $linha['local_prova'],
            $linha['status_inscricao'],
            $linha['status_isencao'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $ultimaColuna = 'G';
        $ultimaLinha = $this->linhas->count() + 1;

        $sheet->freezePane('A2');
        $sheet->getStyle("A1:{$ultimaColuna}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '11313A'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("A1:{$ultimaColuna}{$ultimaLinha}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9E1E5'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ]);

        return [];
    }
}