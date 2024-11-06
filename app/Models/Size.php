<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Size extends Model
{
    use HasFactory;
    protected $table = 'sizes';
    protected $fillable = [
        'sz_name',
        'sz_schema',
        'sz_description',
        'sz_delete',
    ];

    //ini yang lama, kalau error kembalikan keisni ya cantik ^-^
    // public function getAllData($select, $where)
    // {
    //     $affected = DB::table('sizes')
    //         ->select($select)
    //         //            ->leftjoin('product_sub_categories', 'product_sub_categories.id', '=', 'sizes.psc_id')
    //         //            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'sizes.psc_id')
    //         ->where($where)
    //         ->where('sz_delete', '!=', '1')
    //         ->orderBy('sz_name')
    //         ->get();
    //     return $affected;
    // }

//new 5-11-24
    public function getAllData($select, $articleId)
    {
        $affected = DB::table('ts_products as T1')
            ->select($select) 
            ->leftJoin('ts_product_sub_categories as T2', 'T1.psc_id', '=', 'T2.id')
            ->leftJoin('ts_sizes as T3', 'T2.id', '=', 'T3.psc_id')
            ->where('T1.article_id', '=', $articleId) 
            ->where('T3.sz_schema', '=', 'T1.schema_size') 
            ->where('T3.sz_delete', '!=', '1')
            ->orderBy('T3.sz_name')
            ->get();

        return $affected;
    }



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
                if ($ex->getCode() === '23000') {
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
            if ($ex->getCode() === '23000') {
                return false;
            }
        }
    }
}
