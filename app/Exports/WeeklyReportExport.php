<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WeeklyReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $startDate, $endDate)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        // Data sudah dalam format yang benar dari controller
        return collect($this->data);
    }

    public function headings(): array
    {
        // Get the first row to determine column count
        if (empty($this->data)) {
            return ['No Data'];
        }
        
        $firstRow = $this->data[0];
        $columnCount = count($firstRow);
        
        // Return array with column letters (A, B, C, etc.)
        $headings = [];
        // for ($i = 0; $i < $columnCount; $i++) {
        //     $headings[] = chr(65 + $i); // A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z, AA, AB, etc.
        // }
        
        return $headings;
    }

    public function map($row): array
    {
        // Return the row data as is (already in correct format)
        return array_values($row);
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
                'startColor' => ['rgb' => 'E3F2FD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
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
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
        
                ]);
            }
        }

        // Style all cells with borders
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Auto-size columns
        foreach (range('A', $highestColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add title row
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:' . $highestColumn . '1');
        $sheet->setCellValue('A1', 'WEEKLY SCHEDULE REPORT');
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
