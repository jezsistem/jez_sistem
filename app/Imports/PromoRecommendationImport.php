<?php

namespace App\Imports;

use App\Models\PromoRecommendation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class PromoRecommendationImport implements ToCollection, WithStartRow
{
    public function collection(Collection $collection)
    {
        $insert = [];
        
        foreach ($collection as $row) {
            if ($row[0] == null) {
                continue;
            }

            // Mendapatkan ID produk berdasarkan article_id
            $p_id = DB::table('products')->where('article_id', $row[0])->value('id');
            
            if (!$p_id ) {
                continue; // Lewati jika tidak ditemukan
            }

            // Pastikan promo_disc adalah angka
            $promoDisc = is_numeric($row[2]) ? (float) $row[2] : 0;

            $insert[] = [
                'p_id' => $p_id,
                'channel' => $row[1],
                'discount' => $promoDisc,
                'notes' => $row[3] ?? null, // Gunakan null jika tidak ada catatan
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s')
            ];
        }

        if (!empty($insert)) {
            foreach ($insert as $data) {
            PromoRecommendation::updateOrCreate(
                [
                'p_id' => $data['p_id']
                ],
                $data
            );
            }
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}