<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductLocationSetup extends Model
{
    use HasFactory;
    protected $table = 'product_location_setups';
    protected $fillable = [
        'pst_id',
        'pl_id',
        'pls_qty',
        'created_at',
    ];

    public function checkData($select, $where)
    {
        $affected = DB::table($this->table)
            ->select($select)
            ->where($where)
            ->get()->first();
        return $affected;
    }

    public function storeData($mode, $id, $data)
    {
        $created = [
			'created_at' => date('Y-m-d H:i:s')
		];
        $updated = [
			'updated_at' => date('Y-m-d H:i:s')
		];
        if ($mode == 'add') {
            $store = DB::table($this->table)->insert(array_merge($data, $created));
            return $store;
        } else if ($mode == 'edit') {
            try {
                $store = DB::table($this->table)->where('id', $id)->update(array_merge($data, $updated));
                return $store;
            } catch (\Illuminate\Database\QueryException $ex) {
                if($ex->getCode() === '23000') {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    public function deleteData($id)
    {
        try {
            $delete = DB::table($this->table)->where('id', $id)->delete();
            if ($delete) {
                return true;
            } else {
                return false;
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return false;
            }
        }
    }
    
    public static function getExport($st_id)
    {

        $export = DB::table('product_location_setups')
        ->selectRaw("
        CONCAT(pl_name, ' (', pl_code, ')') as location,
        article_id,
        p_name as article,
        br_name,
        p_color,
        sz_name,
        ps_barcode,
        pls_qty,
        AVG(COALESCE(ts_products.p_purchase_price, 0)) as hpp,
        p_sell_price 
    ")
    ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
    ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
    ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
    ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
    ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
    ->where('product_locations.st_id', '=', $st_id)
    ->groupBy(
        'product_location_setups.id',
        'article_id',
        'p_name',
        'br_name',
        'p_color',
        'sz_name',
        'ps_barcode',
        'pls_qty',
        'p_sell_price'
    )
    ->get()->toArray();

        return $export;


    }
}
