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
        // Transform grouped data to flat collection for export
        $exportData = [];
        
        foreach ($this->data as $divisionName => $users) {
            foreach ($users as $user) {
                $exportData[] = (object) [
                    'division_name' => $divisionName,
                    'user_id' => $user['user_id'],
                    'u_nip' => $user['u_nip'],
                    'u_name' => $user['u_name'],
                    'ud_name' => $user['ud_name'],
                    'schedules' => $user['schedules']
                ];
            }
        }
        
        return collect($exportData);
    }

    public function headings(): array
    {
        $headings = ['NAMA'];
        
        // Add date headers for each day of the week (same as weekly report table)
        $currentDate = strtotime($this->startDate);
        $endDate = strtotime($this->endDate);
        
        while ($currentDate <= $endDate) {
            $dayName = date('l', $currentDate);
            $dayDate = date('d M', $currentDate);
            $headings[] = $dayName . ' (' . $dayDate . ') - Shift Name';
            $headings[] = $dayName . ' (' . $dayDate . ') - Start Shift';
            $currentDate = strtotime('+1 day', $currentDate);
        }
        
        return $headings;
    }

    public function map($row): array
    {
        $mapped = [
            // NAMA column with NIP below (same format as weekly report table)
            $row->u_name . ' (' . $row->u_nip . ')'
        ];
        
        // Add schedule data for each date (same structure as weekly report table)
        $currentDate = strtotime($this->startDate);
        $endDate = strtotime($this->endDate);
        
        while ($currentDate <= $endDate) {
            $dateStr = date('Y-m-d', $currentDate);
            $schedule = $row->schedules[$dateStr] ?? null;
            
            if ($schedule) {
                // Shift Name Column (same logic as weekly report table)
                $shiftName = $schedule['sc_shift_name'] ?? '';
                $shiftCode = $schedule['sc_code'] ?? '';
                
                if ($shiftName) {
                    $mapped[] = $shiftName . ' (' . $shiftCode . ')';
                } else {
                    $mapped[] = $shiftCode ?: '-';
                }
                
                // Start Shift Column (same logic as weekly report table)
                $startTime = $schedule['sc_start_time'] ?? '';
                $endTime = $schedule['sc_end_time'] ?? '';
                
                if ($startTime && $endTime) {
                    $mapped[] = date('H:i', strtotime($startTime)) . ' - ' . date('H:i', strtotime($endTime));
                } elseif ($startTime) {
                    $mapped[] = date('H:i', strtotime($startTime));
                } else {
                    $mapped[] = '-';
                }
            } else {
                $mapped[] = '-'; // Shift Name
                $mapped[] = '-'; // Start Shift
            }
            
            $currentDate = strtotime('+1 day', $currentDate);
        }
        
        return $mapped;
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
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

        // Style all cells
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->applyFromArray([
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
