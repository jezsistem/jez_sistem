<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NamesetExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            "NO", 
            "INVOICE", 
            "DIVISI", 
            "ARTIKEL", 
            "TGL TRANSAKSI", 
            "NOTE", 
            "DIPROSES OLEH", 
            "SELESAI PADA"
        ];
    }
    
    public function collection()
    {
        $data = $this->data;
        $no = 1;

        return $data->map(function ($item) use (&$no) {
            return [
                $no++,
                $item->pos_invoice,
                $item->stt_name,
                $item->p_name . ' ' . $item->p_color . ' ' . $item->sz_name,
                $item->pos_created,
                $item->pos_note,
                $item->nameset_by,
                $item->pos_td_nameset_at
            ];
        });
    }
}
