<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProductMutation;
use App\Models\ProductStock;

class SetupHistoryExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $st_id;
    protected $start;
    protected $end;
    protected $search;
    protected $history_bin_start;
    protected $history_bin_end;

    function __construct($st_id, $start, $end, $search, $history_bin_start, $history_bin_end)
    {
        $this->st_id = $st_id;
        $this->start = $start;
        $this->end = $end;
        $this->search = $search;
        $this->history_bin_start = $history_bin_start;
        $this->history_bin_end = $history_bin_end;
    }

    public function headings(): array
    {
        return ["Tanggal", "Store", "User", "SKU", "Brand", "Artikel", "Warna", "Size", "BIN Awal", "QBIN Awal", "Qty Mts", "BIN Awal Setelah Mts", "BIN Tujuan"];
    }

    public function collection()
    {
        $st_id = $this->st_id;
        $start = $this->start;
        $end = $this->end;
        $search = $this->search;
        $history_bin_start = $this->history_bin_start;
        $history_bin_end = $this->history_bin_end;

        $export = array();

        $data = ProductMutation::select('st_name', 'u_name', 'pmt_old_qty', 'pmt_qty', 'pl_code', 'pls_id', 'product_mutations.created_at')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_mutations.pl_id')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->leftJoin('users', 'users.id', '=', 'product_mutations.u_id')
            ->join('product_location_setups', 'product_mutations.pls_id', '=',  'product_location_setups.id')
            ->join('product_stocks', 'product_location_setups.pst_id', '=', 'product_stocks.id')
            ->join('products', 'products.id', '=', 'product_stocks.p_id')
            ->where(function ($w) use ($st_id) {
            if (!empty($st_id)) {
                $w->where('product_locations.st_id', '=', $st_id);
            }
            })
            ->where(function ($w) use ($start, $end) {
            if (!empty($end)) {
                $w->whereDate('product_mutations.created_at', '>=', $start)
                  ->whereDate('product_mutations.created_at', '<=', $end);
            } else {
                $w->whereDate('product_mutations.created_at', $start);
            }
            })
            ->where(function ($w) use ($search) {
            if (!empty($search)) {
                $w->orWhere('product_stocks.ps_barcode', 'LIKE', "%$search%")
                  ->orWhere('users.u_name', 'LIKE', "%$search%")
                  ->orWhere('products.p_name', 'LIKE', "%$search%");
            }
            })
            ->where(function ($w) use ($history_bin_start, $history_bin_end) {
            if (!empty($history_bin_start)) {
                $w->where('product_location_setups.pl_id', '=', $history_bin_start);
            }
            if (!empty($history_bin_end)) {
                $w->where('product_mutations.pl_id', '=', $history_bin_end);
            }
            })
            ->get();
        if (!empty($data->first())) {
            foreach ($data as $row) {
                $ar = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color', 'pl_code', 'ps_barcode as sku')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                    ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                    ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                    ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    ->where('product_location_setups.id', $row->pls_id)
                    ->first();

                if (!$ar) {
                    // If no result is found, assign default values or skip
                    $br_name = $p_name = $p_color = $sz_name = $pl_code = $sku = 'N/A';
                } else {
                    $br_name = $ar->br_name;
                    $p_name = $ar->p_name;
                    $p_color = $ar->p_color;
                    $sz_name = $ar->sz_name;
                    $pl_code = $ar->pl_code;
                    $sku = $ar->sku;
                }

                $date = date('d/m/Y H:i:s', strtotime($row->created_at));
                $export[] = [
                    $date,
                    $row->st_name,
                    $row->u_name,
                    $sku,
                    $br_name,
                    $p_name,
                    $p_color,
                    $sz_name,
                    $pl_code,
                    $row->pmt_old_qty,
                    $row->pmt_qty,
                    ($row->pmt_old_qty - $row->pmt_qty),
                    $row->pl_code
                ];
            }
        }

        return collect($export);
    }
}
