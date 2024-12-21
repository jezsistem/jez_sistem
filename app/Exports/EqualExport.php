<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EqualExport implements FromCollection, WithHeadings
{
    protected $equal;

    public function __construct(array $equal)
    {
        $this->equal = $equal;
    }

    /**
     * Return a collection of data to be exported.
     */
    public function collection()
    {
        return collect($this->equal);
    }

    /**
     * Define the headings for the Excel file.
     */
    public function headings(): array
    {
        return [
            'MA ID',
            'PLS ID',
            'Quantity Export',
            'Quantity SO',
            'Type',
            'Difference',
            'Created At',
            'Updated At'
        ];
    }
}