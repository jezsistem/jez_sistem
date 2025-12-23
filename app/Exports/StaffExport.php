<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class StaffExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'NIP',
            'Position ID',
            'Division ID',
            'Type ID',
            'Position',
            'Division',
            'Type',
            'Leave Balance'
        ];
    }
}
