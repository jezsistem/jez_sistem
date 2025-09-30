<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MonthlyScheduleExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $schedules;
    protected $startDate;
    protected $endDate;

    public function __construct($schedules, $startDate, $endDate)
    {
        $this->schedules = $schedules;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        // Data sudah dalam format yang benar dari controller
        return collect($this->schedules);
    }

    public function headings(): array
    {
        // Get the first row to determine column count
        if (empty($this->schedules)) {
            return ['No Data'];
        }
        
        $firstRow = $this->schedules[0];
        $columnCount = count($firstRow);
        
        // Return array with column letters (A, B, C, etc.)
        $headings = [];
        // for ($i = 0; $i < $columnCount; $i++) {
        //     $headings[] = chr(65 + $i); // A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z, AA, AB, etc.
        // }
        
        return $headings;
    }

    public function map($schedule): array
    {
        // Return the row data as is (already in correct format)
        return array_values($schedule);
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Style division header rows (background color)
        for ($row = 2; $row <= $highestRow; $row++) {
            $divisionCell = $sheet->getCell('A' . $row)->getValue();
            if ($divisionCell && $divisionCell !== '' && !is_numeric($divisionCell)) {
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E3F2FD'],
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            }
        }

        // Style all cells with borders
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Auto-size columns
        $sheet->getColumnDimension('A')->setWidth(100);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(25);
        $sheet->getColumnDimension('J')->setWidth(25);
        $sheet->getColumnDimension('K')->setWidth(25);
        $sheet->getColumnDimension('L')->setWidth(25);
        $sheet->getColumnDimension('M')->setWidth(25);
        $sheet->getColumnDimension('N')->setWidth(25);
        $sheet->getColumnDimension('O')->setWidth(25);
        $sheet->getColumnDimension('P')->setWidth(25);
        $sheet->getColumnDimension('Q')->setWidth(25);
        $sheet->getColumnDimension('R')->setWidth(25);
        $sheet->getColumnDimension('S')->setWidth(25);
        $sheet->getColumnDimension('T')->setWidth(25);
        $sheet->getColumnDimension('U')->setWidth(25);
        $sheet->getColumnDimension('V')->setWidth(25);
        $sheet->getColumnDimension('W')->setWidth(25);
        $sheet->getColumnDimension('X')->setWidth(25);
        $sheet->getColumnDimension('Y')->setWidth(25);
        $sheet->getColumnDimension('Z')->setWidth(25);
        $sheet->getColumnDimension('AA')->setWidth(25);
        $sheet->getColumnDimension('AB')->setWidth(25);
        $sheet->getColumnDimension('AC')->setWidth(25);
        $sheet->getColumnDimension('AD')->setWidth(25);
        $sheet->getColumnDimension('AE')->setWidth(25);
        $sheet->getColumnDimension('AF')->setWidth(25);
        $sheet->getColumnDimension('AG')->setWidth(25);
        $sheet->getColumnDimension('AH')->setWidth(25);
        $sheet->getColumnDimension('AI')->setWidth(25);
        $sheet->getColumnDimension('AJ')->setWidth(25);
        $sheet->getColumnDimension('AK')->setWidth(25);
        $sheet->getColumnDimension('AL')->setWidth(25);

        // Add title row
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:' . $highestColumn . '1');
        $sheet->setCellValue('A1', 'MONTHLY SCHEDULE REPORT');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        $sheet->mergeCells('A2:' . $highestColumn . '2');
        $sheet->setCellValue('A2', 'Period: ' . date('d F Y', strtotime($this->startDate)) . ' - ' . date('d F Y', strtotime($this->endDate)));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'size' => 12
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        return $sheet;
    }
}
