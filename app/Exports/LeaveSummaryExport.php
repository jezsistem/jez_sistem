<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LeaveSummaryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
            'Nama',
            'Posisi',
            'Divisi',
            'Jam Kerja/User Type'
        ];

        // Get leave types for dynamic columns
        $leaveTypes = \DB::table('leave_types')
            ->where('lt_is_active', true)
            ->orderBy('lt_name')
            ->get();

        foreach ($leaveTypes as $leaveType) {
            $headings[] = $leaveType->lt_name;
        }

        $headings[] = 'Total Leave';
        $headings[] = 'Sisa Cuti Tahunan';

        return $headings;
    }

    public function map($item): array
    {
        $row = [
            $item->DT_RowIndex ?? 1,
            $item->u_nip ?? '-',
            $item->u_name ?? '-',
            $item->position_name ?? '-',
            $item->division_name ?? '-',
            $item->work_type ?? '-'
        ];

        // Get leave types for dynamic columns
        $leaveTypes = \DB::table('leave_types')
            ->where('lt_is_active', true)
            ->orderBy('lt_name')
            ->get();

        foreach ($leaveTypes as $leaveType) {
            $leaveValue = $item->{'leave_' . strtolower($leaveType->lt_code)} ?? 0;
            $row[] = $leaveValue;
        }

        $row[] = ($item->total_days ?? 0) + ($item->total_hours ?? 0);
        $row[] = $item->annual_leave_balance ?? 0;

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        // Style header
        $sheet->getStyle('A1:Z1')->applyFromArray([
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
        foreach (range('A', 'Z') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders to all cells
        $sheet->getStyle('A1:Z' . ($this->summaryData->count() + 1))->applyFromArray([
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
