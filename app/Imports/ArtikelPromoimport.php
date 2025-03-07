<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ArtikelPromoImport implements ToCollection, WithStartRow
{
    public function collection(Collection $collection)
    {
        $insert = [];
        
        foreach ($collection as $row) {
            if ($row[0] == null) {
                return null;
            }

            // Mendapatkan ID produk berdasarkan article_id
            $p_id = DB::table('ts_products')->where('article_id', $row[0])->value('id');
            
            // Mendapatkan ID store berdasarkan store code
            $st_id = DB::table('ts_stores')->where('st_code', $row[1])->value('id');
            
            if (!$p_id || !$st_id) {
                continue; // Lewati jika tidak ditemukan
            }

            $insert[] = [
                'p_id' => $p_id,
                'st_id' => $st_id,
                'promo_name' => $row[2],
                'start_date' => date('Y-m-d', strtotime($row[3])),
                'end_date' => date('Y-m-d', strtotime($row[4])),
                'promo_type' => $row[5],
                'promo_disc' => (int) $row[6],
                'promo_note' => $row[7],
                'created_at' => now(),
            ];
        }

        if (!empty($insert)) {
            DB::table('articles_promo')->insert($insert);
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}