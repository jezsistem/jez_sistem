<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DebtList extends Model
{
    use HasFactory;
    protected $table = 'debt_lists';

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

    public static function getExport()
    {
        $debt = DB::table('debt_lists')
                    ->select('debt_lists.id as dl_id', 'st_name', 'ps_name', 'br_name', 'dl_invoice', 'dl_invoice_date', 'dl_invoice_due_date', 'dl_value', 'dl_vat', 'dl_total')
                    ->leftJoin('product_suppliers', 'product_suppliers.id', '=', 'debt_lists.ps_id')
                    ->leftJoin('stores', 'stores.id', '=', 'debt_lists.st_id')
                    ->leftJoin('brands', 'brands.id', '=', 'debt_lists.br_id')
                    ->get();
        if (!empty($debt)) {
            $export = array();
            $payment = 0;
            foreach ($debt as $row) {
                $payment = DB::table('debt_list_payments')
                ->select('dlp_value')
                ->where('dl_id', '=', $row->dl_id)
                ->sum('dlp_value');
                $export[] = [$row->st_name, $row->ps_name, $row->br_name, $row->dl_invoice, date('d/m/Y', strtotime($row->dl_invoice_date)), date('d/m/Y', strtotime($row->dl_invoice_due_date)), number_format($row->dl_value), $row->dl_vat, number_format($row->dl_total), number_format($payment)];
            }
        }
        return $export;
    }
}
