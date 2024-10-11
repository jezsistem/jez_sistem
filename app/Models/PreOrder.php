<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrder extends Model
{
    use HasFactory;

    protected $table = 'pre_orders';

    protected $fillable = [
        'st_id',
        'br_id',
        'ss_id',
        'ps_id',
        'pre_order_code',
        'po_delete',
        'po_draft',
    ];

    public static function getAllDataPO()
    {
        return PreOrder::leftJoin('pre_order_articles', 'pre_order_articles.po_id', '=', 'pre_orders.id')
            ->leftJoin('products', 'products.id', '=', 'pre_order_articles.pr_id')
            ->join('stores', 'stores.id', '=', 'pre_orders.st_id')
            ->join('product_suppliers', 'product_suppliers.id', '=', 'pre_orders.ps_id')
            ->where('po_delete', '!=', '1')->pluck('pre_order_code', 'pre_orders.id');
    }
}
