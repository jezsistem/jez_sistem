<?php

namespace App\Imports;

use App\Models\PromoRecommendation;
use App\Models\PromoRecommendationDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class PromoRecommendationImport implements ToCollection, WithStartRow
{
    public function collection(Collection $collection)
    {
        $lates_promo_recom_name = PromoRecommendation::query()->latest('created_at')->value('pr_code');

        // Jika tidak ada data sebelumnya, mulai dari 1
        $next_promo_recom_number = $lates_promo_recom_name ? (int) substr($lates_promo_recom_name, 6) + 1 : 1;
        $pr_code = 'RECPRO' . $next_promo_recom_number;

        $insert = [];

        $promo_recom = PromoRecommendation::create([
            'u_id' => auth()->user()->id,
            'pr_code' => $pr_code,
            'channel' => $collection->first()[1] ?? null, // Ambil channel dari baris pertama
            'created_at' => now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('Y-m-d H:i:s')
        ]);

        $promo_recom_id = $promo_recom->id;

        foreach ($collection as $row) {
            if ($row[0] == null) {
                continue;
            }

            // Mendapatkan ID produk berdasarkan article_id
            $p_id = DB::table('products')->where('article_id', $row[0])->value('id');

            if (!$p_id) {
                continue; // Lewati jika tidak ditemukan
            }

            // Pastikan promo_disc adalah angka
            $promoDisc = is_numeric($row[2]) ? (float) $row[2] : 0;

            $insert[] = [
                'pr_id' => $promo_recom_id,
                'p_id' => $p_id,
                'discount' => $promoDisc,
                'notes' => $row[3] ?? null, // Gunakan null jika tidak ada catatan
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s')
            ];
        }

        if (!empty($insert)) {
            foreach ($insert as $data) {
                PromoRecommendationDetail::create($data);
            }
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
