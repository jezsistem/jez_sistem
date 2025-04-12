<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class MassExportByDate implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            "No",
            "Kode",
            "Store",
            "BIN",
            "BRAND",
            "SKU",
            "ARTIKEL",
            "WARNA",
            "SIZE",
            "Sub Kategori",
            "HB",
            "HJ",
            "Qty System",
            "Qty SO",
            "Type",
            "Diff"
        ];
    }

    public function collection()
    {
        $export = [];
        foreach ($this->data as $index => $row) {
            $export[] = [
                $index + 1,
                $row->ma_code,
                $row->pl_code,
                $row->st_name,
                $row->br_name,
                $row->ps_barcode,
                $row->p_name,
                $row->p_color,
                $row->sz_name,
                $row->psc_name,
                $row->purchase,
                $row->sell,
                $row->qty_export,
                $row->qty_so,
                $row->mad_type,
                $row->mad_diff,
            ];
        }
        return collect($export);
    }
}
