<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProductMutation;
use App\Models\ProductStock;

class SetupHistoryExport implements FromCollection , withHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $st_id;
    protected $start;
    protected $end;

    function __construct($st_id, $start, $end)
    {
        $this->st_id = $st_id;
        $this->start = $start; 
        $this->end = $end;
    }

    public function headings(): array
    {
        return ["Tanggal", "Store", "User", "Brand", "Artikel", "Warna", "Size", "BIN Awal", "QBIN Awal", "Qty Mts", "BIN Awal Setelah Mts", "BIN Tujuan"];
    }

    public function collection()
    {
        $st_id = $this->st_id;
        $start = $this->start;
        $end = $this->end;

        $export = array();

        $data = ProductMutation::select('st_name', 'u_name', 'pmt_old_qty', 'pmt_qty', 'pl_code', 'pls_id', 'product_mutations.created_at')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_mutations.pl_id')
        ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
        ->leftJoin('users', 'users.id', '=', 'product_mutations.u_id')
        ->where(function($w) use ($st_id, $start, $end) {
            if (!empty($st_id)) {
                $w->where('product_locations.st_id', '=', $st_id);
            }
            if (!empty($end)) {
                $w->whereDate('product_mutations.created_at', '>=', $start)
                ->whereDate('product_mutations.created_at', '<=', $end);
            } else {
                $w->whereDate('product_mutations.created_at', $start);
            }
        })
        ->get();
        if (!empty($data->first())) {
            foreach ($data as $row) {
                $ar = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color', 'pl_code')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_location_setups.id', $row->pls_id)
                ->get()->first();

                $date = date('d/m/Y H:i:s', strtotime($row->created_at));
                $export[] = [$date, $row->st_name, $row->u_name, $ar->br_name, $ar->p_name, $ar->p_color, $ar->sz_name, $ar->pl_code, $row->pmt_old_qty, $row->pmt_qty, ($row->pmt_old_qty-$row->pmt_qty), $row->pl_code];
            }
        }
        return collect($export);
    }
}