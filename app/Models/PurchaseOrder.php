<?php

namespace App\Models;

use App\Traits\Lockable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory, Lockable;
    protected $table = 'purchase_orders';

    protected $fillable = [
        'st_id',
        'ps_id',
        'stkt_id',
        'tax_id',
        'dp_id',
        'acc_id',
        'po_invoice',
        'po_shipping_cost',
        'notes',
        'po_discount',
        'po_extra_discount',
        'po_sub_discount',
        'po_total_purchase',
        'po_total_qty',
        'po_payment_amount',
        'po_description',
        'dispute',
        'status_dispute',
        'dispute_description',
        'bank_general',
        'pay_date',
        'due_date',
        'po_delete',
        'po_draft',
        'putaway',
        'is_receivable',
        'claim_amount',
        'po_status',
        'finance_status',
        'created_by',
        'updated_by',
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
