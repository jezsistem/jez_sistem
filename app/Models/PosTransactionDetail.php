<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PosTransactionDetail extends Model
{
    use HasFactory;
    protected $table = 'pos_transaction_details';
    protected $fillable = [
        'pt_id',
        'pl_id',
        'pst_id',
        'pos_td_qty',
        'pos_td_draft',
        'pos_td_sell_price',
        'pos_td_total_price',
        'pos_td_discount',
        'pos_td_discount_number',
        'pos_td_discount_price',
        'pos_td_marketplace_price',
        'pos_td_nameset_price',
        'pos_td_nameset',
        'created_at'
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
