<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExternalAssignmentSummaryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $summaryData;

    public function __construct($summaryData)
    {
        $this->summaryData = $summaryData;
    }

    public function collection()
    {
        return $this->summaryData;
    }

    public function headings(): array
    {
        $headings = [
            'No.',
            'NIP',
            'Requester',
            'Division',
            'Assignment Type',
            'Start Date',
            'End Date',
            'Location',
            'Cash Advance',
            'Cash Detail',
            'Cash Report',
            'SPV Approve',
            'HR Approve',
            'Finance',
            'Finance File',
            'Status'
        ];

        return $headings;
    }

    public function map($item): array
    {
        $row = [
            $item->DT_RowIndex ?? 1,
            $item->nip ?? '-',
            $item->requester ?? '-',
            $item->division ?? '-',
            $item->assignment_type ?? '-',
            $item->start ?? '-',
            $item->end ?? '-',
            $item->location ?? '-',
            $item->ear_cash_advance ?? 0,
            $item->cash_detail_sum ?? 0,
            $item->report_cash_sum ?? 0,
            $item->approver ?? '-',
            $item->hr ?? '-',
            $item->finance ?? '-',
            $item->file_url ?? '-',
            $item->ear_status ?? '-'
        ];

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        // Style header
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);

        // Auto-size columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders to all cells
        $sheet->getStyle('A1:P' . ($this->summaryData->count() + 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        return $sheet;
    }
}
