<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseOrderArticleDetailStatus extends Model
{
    use HasFactory;
    protected $table = 'purchase_order_article_detail_statuses';
    protected $fillable = [
        'poad_id',
        'stkt_id',
        'tax_id',
        'u_id_receive',
        'u_id_approve',
        'u_id_reject',
        'is_paid',
        'por_id',
        'poads_invoice',
        'invoice_date',
        'arrived_at',
        'poads_qty',
        'poads_discount',
        'poads_extra_discount',
        'poads_sub_discount',
        'poads_purchase_price',
        'shipping_cost',
        'COGS',
        'poads_total_price',
        'poads_type',
        'notes',
        'invoice_image',
        'packet_image',
        'received_date',
        'created_at',
        'updated_at'
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
}
