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
        return ["Tanggal", "Store", "User", "SKU", "Brand", "Artikel", "Warna", "Size", "BIN Awal", "QBIN Awal", "Qty Mts", "BIN Awal Setelah Mts", "BIN Tujuan", "Catatan"];
    }

    public function collection()
    {
        $st_id = $this->st_id;
        $start = $this->start;
        $end = $this->end;
        $search = $this->search;
        $history_bin_start = $this->history_bin_start;
        $history_bin_end = $this->history_bin_end;
        
        $data = DB::table('product_mutations')
            ->select([
                'product_mutations.created_at',
                'stores.st_name',
                'users.u_name',
                'product_stocks.ps_barcode',
                'brands.br_name',
                'products.p_name',
                'products.p_color',
                'sizes.sz_name',
                'pl_start.pl_code as bin_start',
                'product_mutations.pmt_old_qty',
                'product_mutations.pmt_qty',
                DB::raw('(ts_product_mutations.pmt_old_qty - ts_product_mutations.pmt_qty) as bin_awal_setelah_mts'),
                'pl_target.pl_code as bin_tujuan',
                'product_mutations.notes'
            ])
            ->leftJoin('product_location_setups', 'product_mutations.pls_id', '=', 'product_location_setups.id')
            ->leftJoin('product_locations as pl_target', 'product_mutations.pl_id', '=', 'pl_target.id') // BIN tujuan
            ->leftJoin('product_locations as pl_start', 'product_location_setups.pl_id', '=', 'pl_start.id') // BIN awal
            ->leftJoin('stores', 'stores.id', '=', 'pl_target.st_id')
            ->leftJoin('users', 'users.id', '=', 'product_mutations.u_id')
            ->leftJoin('product_stocks', 'product_location_setups.pst_id', '=', 'product_stocks.id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->when(!empty($st_id), function ($query) use ($st_id) {
                $query->where('pl_target.st_id', $st_id);
            })
            ->when(!empty($start), function ($query) use ($start, $end) {
                if (!empty($end)) {
                    $query->whereBetween(DB::raw('DATE(ts_product_mutations.created_at)'), [$start, $end]);
                } else {
                    $query->whereDate('product_mutations.created_at', $start);
                }
            })
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('product_stocks.ps_barcode', 'like', "%$search%")
                        ->orWhere('users.u_name', 'like', "%$search%")
                        ->orWhere('products.p_name', 'like', "%$search%");
                });
            })
            ->when(!empty($history_bin_start), function ($query) use ($history_bin_start) {
                $query->where('product_location_setups.pl_id', '=', $history_bin_start);
            })
            ->when(!empty($history_bin_end), function ($query) use ($history_bin_end) {
                $query->where('product_mutations.pl_id', '=', $history_bin_end);
            })
            ->get();

        $export = [];

        foreach ($data as $row) {
            $export[] = [
                date('d/m/Y H:i:s', strtotime($row->created_at)),
                $row->st_name,
                $row->u_name,
                $row->ps_barcode,
                $row->br_name,
                $row->p_name,
                $row->p_color,
                $row->sz_name,
                $row->bin_start,
                $row->pmt_old_qty,
                $row->pmt_qty,
                $row->bin_awal_setelah_mts,
                $row->bin_tujuan,
                $row->notes,
            ];
        }

        return collect($export);
    }
}
