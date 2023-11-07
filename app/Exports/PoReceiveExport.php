<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PoReceiveExport implements FromCollection , withHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $st_id;
    protected $start;
    protected $end;
    protected $status;
    protected $date_filter;

    function __construct($st_id, $start, $end, $status, $date_filter)
    {
        $this->st_id = $st_id;
        $this->start = $start; 
        $this->end = $end;
        $this->status = $status;
        $this->date_filter = $date_filter;
    }

    public function headings(): array
    {
        return ["Start Date", "End Date", "No PO", "Store", "Tgl PO", "Brand", "Artikel", "Warna", "Size", "Qty PO", "Qty Terima", "Tgl Terima", "Total PO", "Total Terima"];
    }

    public function collection()
    {
        $st_id = $this->st_id;
        $start = $this->start;
        $end = $this->end;
        $date_filter = $this->date_filter;
        $status = $this->status;
        $export = array();
        $data = DB::table('purchase_order_article_details')
        ->selectRaw("ts_purchase_order_article_details.id, ts_purchase_order_article_details.pst_id, st_name, br_name, p_name, p_color, sz_name, po_invoice, ts_purchase_orders.created_at as po_date, max(ts_purchase_order_article_detail_statuses.created_at) as receive_date
        , poad_qty, poad_total_price")
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
        ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
        ->leftJoin('stores', 'stores.id', '=', 'purchase_orders.st_id')
        ->where(function($w) use ($st_id, $start, $end, $date_filter) {
            if (!empty($st_id)) {
                $w->where('purchase_orders.st_id', '=', $st_id);
            }
            if ($date_filter == 1) {
                if (!empty($end)) {
                    $w->whereDate('purchase_orders.created_at', '>=', $start)
                    ->whereDate('purchase_orders.created_at', '<=', $end);
                } else {
                    $w->whereDate('purchase_orders.created_at', $start);
                }
            }
        })
        ->groupBy('purchase_order_article_details.id')
        ->get();
        if (!empty($data->first())) {
            foreach ($data as $row) {
                $poads_qty = DB::table('purchase_order_article_detail_statuses')->where('poad_id', '=', $row->id)
                ->sum('poads_qty');
                $poads_total_price = DB::table('purchase_order_article_detail_statuses')->where('poad_id', '=', $row->id)
                ->sum('poads_total_price');
                if ($status == 'full') {
                    if ($poads_qty >= $row->poad_qty) {
                        $export[] = [$start, $end, $row->po_invoice, $row->st_name, $row->po_date, $row->br_name, $row->p_name, $row->p_color, $row->sz_name, $row->poad_qty, $poads_qty, $row->receive_date, $row->poad_total_price, $poads_total_price];
                    }
                } else {
                    if ($poads_qty < $row->poad_qty) {
                        $export[] = [$start, $end, $row->po_invoice, $row->st_name, $row->po_date, $row->br_name, $row->p_name, $row->p_color, $row->sz_name, $row->poad_qty, $poads_qty, $row->receive_date, $row->poad_total_price, $poads_total_price];
                    }
                }
                // $export[] = [$start, $end, $row->po_invoice, $row->st_name, $row->po_date, $row->br_name, $row->p_name, $row->p_color, $row->sz_name, $row->poad_qty, $row->poads_qty, $row->receive_date, $row->poad_total_price, $row->poads_total_price];
                   
            }
        }
        return collect($export);
    }
}