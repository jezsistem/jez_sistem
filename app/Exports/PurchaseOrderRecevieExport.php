<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class PurchaseOrderRecevieExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return [
            "TANGGAL",
            "STORE",
            "SUPPLIER",
            "PO INVOICE",
            "ARTIKEL",
            "COLOUR",
            "SKU",
            "SIZE",
            "QTY",
            "COGS",
            "TOTAL",
            "DISPUTE",
            "STATUS DISPUTE",
            "STATUS APPROVAL",
            "TANGGAL APPROVE",
        ];
    }

    public function collection()
    {
        $query = DB::table('purchase_orders')
            ->leftJoin('purchase_order_articles', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
            ->leftJoin('product_stocks', 'purchase_order_article_details.pst_id', '=', 'product_stocks.id')
            ->leftJoin('products', 'product_stocks.p_id', '=', 'products.id')
            ->leftJoin('product_suppliers', 'purchase_orders.ps_id', '=', 'product_suppliers.id')
            ->leftJoin('stores', 'stores.id', '=', 'purchase_orders.st_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id');

        // 🔹 Filter Date
        if (!empty($this->request->get('date'))) {
            $date = $this->request->get('date');
            $exp = explode('|', $date);

            if (count($exp) > 1) {
                $query->whereDate('purchase_orders.created_at', '>=', $exp[0])
                    ->whereDate('purchase_orders.created_at', '<=', $exp[1]);
            } else {
                $query->whereDate('purchase_orders.created_at', $date);
            }
        }

        // 🔹 Filter Status (full / unfull)
        if ($this->request->get('status') == 'full') {
            $query->whereNotNull('purchase_order_article_detail_statuses.u_id_approve');
        } elseif ($this->request->get('status') == 'unfull') {
            $query->whereNull('purchase_order_article_detail_statuses.u_id_approve');
        }

        // 🔹 Filter Dispute
        if ($this->request->get('dispute') !== null && $this->request->get('dispute') !== '') {
            $query->where('purchase_orders.dispute', $this->request->get('dispute'));
        }

        $data = $query->select([
            'purchase_orders.created_at',
            'stores.st_name',
            'product_suppliers.ps_name',
            'purchase_orders.po_invoice',
            'products.p_name',
            'products.p_color',
            'product_stocks.ps_barcode',
            'purchase_order_article_details.poad_qty',
            'purchase_order_article_details.poad_purchase_price',
            'purchase_order_article_details.poad_total_price',
            DB::raw("CASE 
                        WHEN ts_purchase_orders.dispute = 1 THEN 'Yes' 
                        WHEN ts_purchase_orders.dispute = 0 THEN 'No' 
                        ELSE '-' END as dispute"),
            DB::raw("CASE 
                        WHEN ts_purchase_orders.status_dispute = 1 THEN 'Progress' 
                        WHEN ts_purchase_orders.status_dispute = 0 THEN 'Closed' 
                        ELSE '-' END as status_dispute"),
            'purchase_order_article_detail_statuses.u_id_approve',
            'sizes.sz_name',
            'purchase_order_article_detail_statuses.updated_at',
            'purchase_orders.acc_id',
            'purchase_order_article_detail_statuses.is_paid',
        ])->get();

        $export = [];
        foreach ($data as $row) {

            $approvalStatus = 'MENUNGGU APPROVAL';

            if (!empty($row->u_id_approve) && $row->acc_id == 93 && $row->is_paid == 0) {
                $name = DB::table('users')->where('id', $row->u_id_approve)->value('u_name');
                $approvalStatus = 'APPROVAL, BELUM DIBAYAR';
            } elseif (!empty($row->u_id_approve)) {
                $name = DB::table('users')->where('id', $row->u_id_approve)->value('u_name');
                $approvalStatus = 'APPROVAL';
            }

            $export[] = [
                date('d/m/Y H:i:s', strtotime($row->created_at)),
                $row->st_name,
                $row->ps_name,
                $row->po_invoice,
                $row->p_name,
                $row->p_color,
                $row->ps_barcode,
                $row->sz_name,
                $row->poad_qty,
                $row->poad_purchase_price,
                $row->poad_total_price,
                $row->dispute,
                $row->status_dispute,
                $approvalStatus,
                !empty($row->updated_at) ? date('d/m/Y H:i:s', strtotime($row->updated_at)) : '-',
            ];
        }

        return collect($export);
    }
}
