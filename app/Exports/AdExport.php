<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class AdExport implements FromCollection , withHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $st_id;
    protected $start;
    protected $end;
    protected $data;
    protected $article;

    function __construct($st_id, $start, $end, $data, $article)
    {
        $this->st_id = $st_id;
        $this->start = $start;
        $this->end = $end;
        $this->data = $data;
        $this->article = $article;
    }

    public function headings(): array
    {
        return ["Tgl Mulai", "Tgl Akhir", "Store", "Brand", "Beg. Qty", "Beg", "Purc. Qty", "Purc", "Trs.In. Qty", "Trs.In", "Trs.Out. Qty", "Trs.Out", "GID. Qty", "GID", "GIT. Qty", "GIT", "Sales. Qty", "Sales", "Profit", "COGS", "End. Qty", "End"];
    }

    public function collection()
    {
        $export = array();
        
        return collect($export);
    }
}
