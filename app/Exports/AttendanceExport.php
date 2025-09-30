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

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama',
            'Divisi',
            'Tanggal',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
            'Shift',
            'Keterangan',
            'Sumber',
            'Dibuat Oleh',
            'Tanggal Dibuat'
        ];
    }

    public function map($row): array
    {
        return [
            $row->u_nip ?? '-',
            $row->u_name ?? '-',
            $row->ud_name ?? '-',
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
        // Header styling
        $sheet->getStyle('A1:L1')->applyFromArray([
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
        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders
        $sheet->getStyle('A1:L' . ($this->data->count() + 1))->applyFromArray([
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
