<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LeaveRequestExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $leaveRequests;

    public function __construct($leaveRequests)
    {
        $this->leaveRequests = $leaveRequests;
    }

    public function collection()
    {
        \Log::info('LeaveRequestExport collection called', [
            'data_count' => $this->leaveRequests->count(),
            'data_type' => gettype($this->leaveRequests),
            'first_item' => $this->leaveRequests->first()
        ]);
        return $this->leaveRequests;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Durasi',
            'Jenis Leave',
            'Status',
            'Alasan',
            'Tanggal Request'
        ];
    }

    public function map($item): array
    {
        static $no = 1;
        
        return [
            $no++,
            $item->start_date ? date('d/m/Y', strtotime($item->start_date)) : '-',
            $item->end_date ? date('d/m/Y', strtotime($item->end_date)) : '-',
            ($item->duration ?? 0) . ' ' . ($item->duration_type ?? ''),
            $item->leave_type_name ?? '-',
            ucfirst($item->status ?? ''),
            $item->reason ?? '-',
            $item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header
        $sheet->getStyle('A1:H1')->applyFromArray([
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
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders to all cells
        $sheet->getStyle('A1:H' . ($this->leaveRequests->count() + 1))->applyFromArray([
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
