<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class DataPerusahaanExport implements FromCollection, withHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $type;

    function __construct($type)
    {
        $this->type = $type;
//        $this->dp_npwp = $dp_npwp;
//        $this->dp_dp_description = $dp_description;
    }

    public function headings(): array
    {
        return ["No","Name", "NPWP", "Description"];
    }

    public function collection()
    {
        $export = array();

//        $export[] = [date('d/m/Y H:i:s', strtotime($row->ptd_created)), $row->st_name, $row->pos_invoice, $row->cross_order, $row->cust_name, $row->u_name, $row->dv_name, $row->stkt_name, $row->br_name, $row->ps_barcode, $row->p_name, $row->p_color, $row->sz_name, $row->pc_name, $row->psc_name, $row->pssc_name, $row->pos_td_qty, $bandrol, $row->ps_purchase_price, $row->pos_td_sell_price, $row->pos_td_sell_price - $row->pos_td_discount_price,  $row->pos_td_discount_price, $total_price, $b1g1];
        if ($this->type == 'npwp') {

            $data = DB::table('data_perusahaan')->select('dp_name', 'dp_npwp', 'dp_description')->get();

            if (!empty($data)) {
                $no = 1;
                foreach ($data as $row) {
                    $export[] = [$no++, $row->dp_name, $row->dp_npwp, $row->dp_description];
                }
            }
        }

        return collect($export);
    }
}
