<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;


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
            $p_id = DB::table('products')->where('article_id', $row[0])->value('id');
            
            // Mendapatkan ID store berdasarkan store code
            $st_id = DB::table('stores')->where('st_name', $row[2])->value('id');
            
            if (!$p_id || !$st_id) {
                continue; // Lewati jika tidak ditemukan
            }


            // Konversi tanggal dari Excel jika dalam format angka
            $dateStart = is_numeric($row[3]) ? Date::excelToDateTimeObject($row[3])->format('Y-m-d') : date('Y-m-d', strtotime($row[3]));
            $dateEnd = is_numeric($row[4]) ? Date::excelToDateTimeObject($row[4])->format('Y-m-d') : date('Y-m-d', strtotime($row[4]));

            // Pastikan promo_disc adalah angka
            $promoDisc = is_numeric($row[5]) ? (int) $row[5] : 0;

            $insert[] = [
                'p_id' => $p_id,
                'st_id' => $st_id,
                'promo_name' => $row[1],
                'date_start' => $dateStart,
                'date_end' => $dateEnd,
                'promo_disc' => $promoDisc,
                'promo_note' => $row[6],
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