<?php

namespace App\Imports;

use App\Models\ProductDiscountDetail;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Size;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class MassDiscountImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        unset($rows[0]); // skip header

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {

                $pdName      = $row[0];
                $pdType      = strtolower($row[1]);
                $store       = $row[2];
                $division    = $row[3];
                $articleId   = $row[4];
                $value       = $row[5];
//                $dateStart   = $row[6];
//                $dateEnd     = $row[7];

                $dateStart = ExcelDate::excelToDateTimeObject($row[6])
                    ->format('Y-m-d');

                $dateEnd = ExcelDate::excelToDateTimeObject($row[7])
                    ->format('Y-m-d');

//                dd($dateEnd, $dateStart);

                $st_id = DB::table('stores')->where('st_name', $store)->value('id');
                $std_id = DB::table('store_type_divisions')->where('dv_name', $division)->value('id');

                $p_id = DB::table('products')->where('article_id', $articleId)->value('id');

                $price = DB::table('product_stocks')
                    ->where('p_id', $p_id)->first();

                if (!$price) continue;

                if ($pdType === 'percent') {
                    $pdValue = $price->ps_price_tag - ($price->ps_price_tag * $value / 100);
                } else {
                    $pdValue = $price->ps_price_tag - $value;
                }

                $discount = DB::table('product_discounts')
                    ->where([
                        'pd_name' => $pdName,
                        'pd_type' => $pdType,
                        'st_id' => $st_id,
                        'std_id' => $std_id,
                        'pd_date_start' => $dateStart,
                        'pd_date' => $dateEnd,
                        'pd_value' => $pdValue,
                    ])->first();

                // 🔹 Insert master jika belum ada
                if (!$discount) {
                    $pdId = DB::table('product_discounts')->insertGetId([
                        'pd_name' => $pdName,
                        'pd_type' => $pdType,
                        'st_id' => $st_id,
                        'std_id' => $std_id,
                        'pd_date_start' => $dateStart,
                        'pd_date' => $dateEnd,
                        'pd_value' => $pdValue,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $pdId = $discount->id;
                }


                $pst_id = DB::table('product_stocks')->where('p_id', $p_id)->get();

                foreach ($pst_id as $pst) {
                    DB::table('product_discount_details')->insert([
                        'pd_id' => $pdId,
                        'pst_id' => $pst->id,
                        'pdd_type' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
