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

class BreakTimeStaffExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $staff;

    public function __construct($data, $staff)
    {
        $this->data = $data;
        $this->staff = $staff;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Tanggal',
            'Tipe Break',
            'Jam Mulai',
            'Jam Selesai',
            'Durasi',
            'Status',
            'Shift Start',
            'Shift End',
            'Catatan'
        ];
    }

    public function map($item): array
    {
        static $no = 1;
        
        $breakType = $item->bt_type === 'break_1' ? 'Break 1' : 'Break 2';
        $startTime = $item->bt_start_time ? date('H:i', strtotime($item->bt_start_time)) : '-';
        $endTime = $item->bt_end_time ? date('H:i', strtotime($item->bt_end_time)) : '-';
        
        // Calculate duration manually if bt_duration_minutes is empty, null, or 0
        $durationMinutes = $item->bt_duration_minutes;
        if ((empty($durationMinutes) || $durationMinutes == 0) && !empty($item->bt_start_time) && !empty($item->bt_end_time)) {
            try {
                $startTimeCarbon = \Carbon\Carbon::parse($item->bt_start_time);
                $endTimeCarbon = \Carbon\Carbon::parse($item->bt_end_time);
                $durationMinutes = $endTimeCarbon->diffInMinutes($startTimeCarbon);
            } catch (\Exception $e) {
                $durationMinutes = 0;
            }
        }
        
        $duration = $durationMinutes > 0 ? 
            sprintf('%02d:%02d', floor($durationMinutes / 60), $durationMinutes % 60) : '-';
        
        $shiftStart = $item->shift_start ? date('H:i', strtotime($item->shift_start)) : '-';
        $shiftEnd = $item->shift_end ? date('H:i', strtotime($item->shift_end)) : '-';
        
        return [
            $no++,
            date('d/m/Y', strtotime($item->bt_date)),
            $breakType,
            $startTime,
            $endTime,
            $duration,
            ucfirst($item->bt_status ?? '-'),
            $shiftStart,
            $shiftEnd,
            $item->bt_notes ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header
        $sheet->getStyle('A1:J1')->applyFromArray([
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
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders to all cells
        $sheet->getStyle('A1:J' . ($this->data->count() + 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Center align numeric and time columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return $sheet;
    }
}
