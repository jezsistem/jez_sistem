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

class DiscountImport implements ToCollection, WithStartRow
{
    private $rows = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $row)
    {
        ++$this->rows;
        $data_id = [];

        foreach ($row as $r) {
            if ($r[0] === null) {
                continue; // Skip rows with null pd_id
            }

            $pd_id = $r[0];
            $article_id = $r[1];

            $article = ProductStock::select('product_stocks.id as pst_id')
                ->join('products', 'products.id', '=', 'product_stocks.p_id')
                ->where('products.article_id', $article_id)->get();

            foreach ($article as $a) {
                $pdd_id = DB::table('product_discount_details')->insertGetId([
                    'pd_id' => $pd_id,
                    'pst_id' => $a->pst_id,
                    'pdd_type' => '1',
                ]);
            }
            // Uncomment and ensure $pst_id is defined if using this


            $data_id[] = $pdd_id;
        }

        if (!empty($data_id)) {
//            dd($data_id); // Debugging output
            return '200';
        }

        return '400'; // If no valid data was found
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}
