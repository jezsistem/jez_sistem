<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WeeklyScheduleExport implements FromCollection, WithHeadings, WithStyles
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
        return collect($this->data);
    }

    public function headings(): array
    {
        // Since data is already pre-formatted, we don't need to generate headings
        // The first few rows contain the actual headers
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header rows (division headers)
        $lastRow = $sheet->getHighestRow();
        
        // Style division header rows (rows with Division, Staff, NIP, Nama, Divisi, User Type)
        for ($row = 1; $row <= $lastRow; $row++) {
            $firstCellValue = $sheet->getCell('A' . $row)->getValue();
            
            if ($firstCellValue === 'Division' || $firstCellValue === '') {
                // Style division header row
                $sheet->getStyle('A' . $row . ':' . $sheet->getHighestColumn() . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            } elseif (is_numeric($firstCellValue) || $firstCellValue === '') {
                // Style date header row (rows with date numbers)
                $sheet->getStyle('A' . $row . ':' . $sheet->getHighestColumn() . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E7E6E6'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }
        }

        // Style all cells with borders
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $lastRow)->applyFromArray([
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
        foreach (range('A', $sheet->getHighestColumn()) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }
}
