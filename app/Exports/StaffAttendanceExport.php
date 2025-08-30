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

class StaffAttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $staffName;

    public function __construct($data, $staffName = '')
    {
        $this->data = $data;
        $this->staffName = $staffName;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        $title = $this->staffName ? "Data Kehadiran: {$this->staffName}" : "Data Kehadiran Staff";
        
        return [
            [$title],
            [''],
            [
                'Tanggal',
                'Jam Masuk',
                'Jam Keluar',
                'Status',
                'Shift',
                'Keterangan',
                'Sumber',
                'Dibuat Oleh',
                'Tanggal Dibuat'
            ]
        ];
    }

    public function map($row): array
    {
        return [
            $row->at_date ? date('d/m/Y', strtotime($row->at_date)) : '-',
            $row->at_time_in ? date('H:i', strtotime($row->at_time_in)) : '-',
            $row->at_time_out ? date('H:i', strtotime($row->at_time_out)) : '-',
            $this->getStatusText($row->at_status),
            $row->sc_code ?? '-',
            $row->at_notes ?? '-',
            $row->at_source ?? '-',
            $row->created_by_name ?? '-',
            $row->created_at ? date('d/m/Y H:i', strtotime($row->created_at)) : '-'
        ];
    }

    private function getStatusText($status)
    {
        $statusMap = [
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Tidak Hadir',
            'early_leave' => 'Pulang Awal',
            'scan_once' => 'Scan Sekali',
            'leave' => 'Cuti'
        ];

        return $statusMap[$status] ?? $status;
    }

    public function styles(Worksheet $sheet)
    {
        // Title styling (row 1)
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E75B6']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Merge title cell
        $sheet->mergeCells('A1:I1');

        // Header styling (row 3)
        $sheet->getStyle('A3:I3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Auto-size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders
        $lastRow = $this->data->count() + 3;
        $sheet->getStyle('A3:I' . $lastRow)->applyFromArray([
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
