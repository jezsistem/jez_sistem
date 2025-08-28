<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceSummaryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $summaryData;

    public function __construct($summaryData)
    {
        $this->summaryData = $summaryData;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->summaryData;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No.',
            'NIP',
            'Nama',
            'Posisi',
            'Divisi',
            'Jam Kerja',
            'Total Shift',
            'Total Libur',
            'Hadir',
            'Sakit',
            'Cuti',
            'Terlambat',
            'Alpha/Tidak Hadir'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->u_nip ?? '-',
            $row->u_name ?? '-',
            $row->position_name ?? '-',
            $row->division_name ?? '-',
            $row->work_type ?? '-',
            $row->total_shifts ?? 0,
            $row->total_libur ?? 0,
            $row->present_days ?? 0,
            $row->sick_days ?? 0,
            $row->leave_days ?? 0,
            $row->late_days ?? 0,
            $row->alpha_days ?? 0
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header styling
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ],
            // Auto-size columns
            'A:M' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]
        ];
    }
}
