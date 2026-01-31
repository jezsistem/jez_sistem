<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;


class OvertimeSummaryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $summaryData;
    protected $overtimeTypes;

    public function __construct($summaryData)
    {
        $this->summaryData = $summaryData;
        $this->overtimeTypes = DB::table('overtime_types')->orderBy('ot_name')->get();
    }

    public function collection()
    {
        return collect($this->summaryData);
    }

    public function headings(): array
    {
        $headings = [
            '#',
            'NIP',
            'Nama',
            'Divisi'
        ];

        // Add overtime type columns
        foreach ($this->overtimeTypes as $type) {
            $headings[] = 'Tipe ' . $type->ot_name;
        }

        // Add overtime fee column
        $headings[] = 'Fee Lembur';

        return $headings;
    }

    public function map($user): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $row = [
            $rowNumber,
            $user->u_nip ?? '',
            $user->u_name ?? '',
            $user->division_name ?? ''
        ];

        // Add overtime type hours
        foreach ($this->overtimeTypes as $type) {
            $columnName = 'overtime_type_' . $type->id;
            $row[] = $user->{$columnName} ?? 0;
        }

        // Add overtime fee
        $row[] = $user->overtime_fee ?? 0;

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
