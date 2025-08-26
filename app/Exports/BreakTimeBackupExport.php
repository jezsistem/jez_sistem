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

class BreakTimeBackupExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
            'No',
            'Date',
            'Employee Name',
            'NIP',
            'Division',
            'Break Type',
            'Start Time',
            'End Time',
            'Duration',
            'Status'
        ];
    }

    public function map($row): array
    {
        static $no = 1;
        
        // Dynamic break type handling
        $breakNumber = str_replace('break_', '', $row->bt_type);
        $breakType = 'Break ' . ucfirst($breakNumber);
        
        $startTime = $row->bt_start_time ? date('H:i', strtotime($row->bt_start_time)) : '-';
        $endTime = $row->bt_end_time ? date('H:i', strtotime($row->bt_end_time)) : '-';
        
        // Calculate duration manually if bt_duration_minutes is empty, null, or 0
        $durationMinutes = $row->bt_duration_minutes;
        if ((empty($durationMinutes) || $durationMinutes == 0) && !empty($row->bt_start_time) && !empty($row->bt_end_time)) {
            try {
                $startTimeCarbon = \Carbon\Carbon::parse($row->bt_start_time);
                $endTimeCarbon = \Carbon\Carbon::parse($row->bt_end_time);
                $durationMinutes = $endTimeCarbon->diffInMinutes($startTimeCarbon);
            } catch (\Exception $e) {
                $durationMinutes = 0;
            }
        }
        
        $duration = $durationMinutes > 0 ? 
            sprintf('%02d:%02d', floor($durationMinutes / 60), $durationMinutes % 60) : '-';
        
        $status = $this->getStatusText($row->bt_status);

        return [
            $no++,
            date('d/m/Y', strtotime($row->bt_date)),
            $row->u_name,
            $row->u_nip,
            $row->ud_name,
            $breakType,
            $startTime,
            $endTime,
            $duration,
            $status
        ];
    }

    private function getStatusText($status)
    {
        switch($status) {
            case 'active':
                return 'Active';
            case 'completed':
                return 'Completed';
            case 'cancelled':
                return 'Cancelled';
            default:
                return ucfirst($status);
        }
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:J1')->applyFromArray([
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

        // Auto-size columns
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders to all cells
        $sheet->getStyle('A1:J' . ($this->data->count() + 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Center align specific columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J:J')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return $sheet;
    }
}
