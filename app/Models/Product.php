<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'br_id',
        'pc_id',
        'psc_id',
        'pssc_id',
        'mc_id',
        'ps_id',
        'pu_id',
        'gn_id',
        'ss_id',
        'p_color',
        'p_name',
        'p_aging',
        'p_price_tag',
        'p_sell_price',
        'p_purchase_price',
        'p_description',
        'p_image',
        'p_delete',
        'schema_size',
        'subcategory1',
        'subcategory2',
        'mp_best_seller',
        'mp_stock_masking',
        'complement',
        'consignment',
        'is_everlast',
        'is_supersale',
        'is_reguler',
        'created_at',
        'p_turnoverclass',
        'updated_at',
        'mark_down'
    ];

    public const readableColumns = [
        'br_id' => 'Brand',
        'pc_id' => 'Category',
        'psc_id' => 'Sub Category',
        'pssc_id' => 'Sub Sub Category',
        'mc_id' => 'Main Color',
        'ps_id' => 'Supplier',
        'pu_id' => 'Unit',
        'gn_id' => 'Gender',
        'ss_id' => 'Season',
        'p_color' => 'Color',
        'p_name' => 'Article Name',
        'p_aging' => 'Aging',
        'p_price_tag' => 'Price Tag',
        'p_sell_price' => 'Sell Price',
        'p_purchase_price' => 'Purchase Price',
        'p_description' => 'Description',
        'p_image' => 'Image',
        'schema_size' => 'Schema Size',
        'subcategory1' => 'Subcategory 1',
        'subcategory2' => 'Subcategory 2',
        'mp_best_seller' => 'MP Best Seller',
        'mp_stock_masking' => 'MP Stock Masking',
        'complement' => 'Complement',
        'consignment' => 'Consignment',
        'is_everlast' => 'Is Everlast',
        'is_supersale' => 'Is Supersale',
        'is_reguler' => 'Is Reguler',
        'p_turnoverclass' => 'Turnover Class',
        'mark_down' => 'Mark Down',
        'ps_price_tag' => 'SKU Price Tag',
        'ps_sell_price' => 'SKU Sell Price',
        'ps_purchase_price' => 'SKU Purchase Price',
    ];


    public function getJoinData($select, $where)
    {
        $affected = DB::table($this->table)
            ->select($select)
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_categories', 'product_categories.id', '=', 'products.pc_id')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
            ->leftJoin('product_sub_sub_categories', 'product_sub_sub_categories.id', '=', 'products.pssc_id')
            ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
            ->leftJoin('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
            ->leftJoin('product_units', 'product_units.id', '=', 'products.pu_id')
            ->leftJoin('genders', 'genders.id', '=', 'products.gn_id')
            ->leftJoin('seasons', 'seasons.id', '=', 'products.ss_id')
            ->where($where)
            ->get()->first();
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
            // 			'created_at' => date('Y-m-d H:i:s')
        ];
        $updated = [
            // 			'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($mode == 'add') {
            $store = DB::table($this->table)->insertGetId(array_merge($data, $created));
            return  $store;
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

    public static function getExport()
    {
        $export = DB::table('products')
            ->select('p_name', 'ps_qty', 'pc_name', 'psc_name', 'pssc_name', 'br_name', 'ps_name', 'pu_name', 'gn_name', 'ss_name', 'p_aging', 'mc_name', 'p_color', 'p_price_tag', 'p_purchase_price', 'p_sell_price', 'sz_name', 'ps_barcode')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_categories', 'product_categories.id', '=', 'products.pc_id')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
            ->leftJoin('product_sub_sub_categories', 'product_sub_sub_categories.id', '=', 'products.pssc_id')
            ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
            ->leftJoin('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
            ->leftJoin('product_units', 'product_units.id', '=', 'products.pu_id')
            ->leftJoin('genders', 'genders.id', '=', 'products.gn_id')
            ->leftJoin('seasons', 'seasons.id', '=', 'products.ss_id')
            ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->get();
        //                    ->get()->toArray();
        return $export;
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'p_id');
    }

    public static function getArticleExport()
    {
        $export = DB::table('products')
            ->select('article_id', 'p_name', 'p_color', 'br_name', 'ps_name', 'p_aging', 'p_price_tag', 'p_purchase_price', 'p_sell_price')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
            ->get();
        return $export;
    }
    
    public static $massUpdateColumns = [
        'schema_size',
        'subcategory1',
        'subcategory2',
        'p_color',
        'p_name',
        'p_aging',
        'p_price_tag',
        'p_sell_price',
        'p_purchase_price',
        'bestseller',
        'mp_best_seller',
        'complement',
        'consignment',
        'mp_stock_masking',
        'is_everlast',
        'is_supersale',
        'is_reguler',
        'p_turnoverclass',
        'mark_down'
    ];

    public static $massUpdateSKUColumns = [
        'ps_price_tag',
        'ps_sell_price',
    ];

    public static $fintechCanChange = [
        'is_everlast',
        'p_purchase_price',
        'ps_purchase_price'
    ];

    public static $mdcxCanChange = [
        'p_price_tag',
        'p_sell_price',
        'ps_price_tag',
        'ps_sell_price'
    ];
}
