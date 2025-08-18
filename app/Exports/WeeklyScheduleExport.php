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

class WeeklyScheduleExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        $headings = ['NIP', 'Nama', 'Divisi', 'Jabatan'];
        
        // Add date headers
        $currentDate = strtotime($this->startDate);
        $endDate = strtotime($this->endDate);
        
        while ($currentDate <= $endDate) {
            $headings[] = date('d/m/Y', $currentDate);
            $currentDate = strtotime('+1 day', $currentDate);
        }
        
        return $headings;
    }

    public function map($row): array
    {
        $mapped = [
            $row->u_nip ?? '-',
            $row->u_name ?? '-',
            $row->ud_name ?? '-',
            $row->ut_name ?? '-'
        ];
        
        // Add schedule data for each date
        $currentDate = strtotime($this->startDate);
        $endDate = strtotime($this->endDate);
        
        while ($currentDate <= $endDate) {
            $dateStr = date('Y-m-d', $currentDate);
            $schedule = $row->schedules->where('ds_date', $dateStr)->first();
            
            if ($schedule) {
                $mapped[] = $schedule->sc_code ?? '-';
            } else {
                $mapped[] = '-';
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
