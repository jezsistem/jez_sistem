<?php

namespace App\Services\ai;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockRecommendationService
{
    public function rekomSepatu(string $message, array $intent): array
    {
        $category = strtolower(trim($intent['category'] ?? 'running'));
        $mapKategori = [
            'running'   => 'running',
            'lari'      => 'running',
            'jogging'   => 'running',
            'sneakers'  => 'sneakers',
            'casual'    => 'casual',
            'daily'     => 'casual',
            'futsal'    => 'futsal',
            'basket'    => 'basket',
        ];
        $category = $mapKategori[$category] ?? $category;

        $branch = strtolower(trim($intent['branch'] ?? 'malang'));

        $size = null;
        if (!empty($intent['size'])) {
            $size = preg_replace('/[^0-9]/', '', $intent['size']);
        }
        if (!$size && preg_match('/\bsize\s*:? ?(\d{2})\b/i', $message, $m)) {
            $size = $m[1];
        }

        $min = $intent['min_price'] ?? 0;
        $max = $intent['max_price'] ?? null;

        if (!$max) {
            preg_match_all('/(\d[\d\.]+)/', $message, $nums);
            $nums = array_map(fn($v) => intval(str_replace('.', '', $v)), $nums[1] ?? []);

            if (count($nums) >= 2) {
                $min = min($nums);
                $max = max($nums);
            } elseif (count($nums) == 1) {
                $max = $nums[0];
            }
        }
        if (!$max) $max = 99999999;

        $params = [
            "%{$category}%",
            $min,
            $max,
            "%{$branch}%"
        ];

        $sizeQuery = "";
        if ($size) {
            $sizeQuery = " AND T7.sz_name = ? ";
            $params[] = $size;
        }

        $sql = "
            SELECT
                T5.p_name,
                T5.p_color,
                T7.sz_name,
                T6.pssc_name,
                T4.st_name,
                T3.ps_barcode AS sku,
                T2.pl_description,
                SUM(T1.pls_qty) AS total_qty,
                MIN(T3.ps_sell_price) AS harga
            FROM ts_product_location_setups T1
            LEFT JOIN ts_product_locations T2 ON T1.pl_id = T2.id
            LEFT JOIN ts_product_stocks T3 ON T3.id = T1.pst_id
            LEFT JOIN ts_stores T4 ON T4.id = T2.st_id
            LEFT JOIN ts_products T5 ON T5.id = T3.p_id
            LEFT JOIN ts_product_sub_sub_categories T6 ON T6.id = T5.pssc_id
            LEFT JOIN ts_sizes T7 ON T7.id = T3.sz_id
            WHERE
                   LOWER(T6.pssc_name) LIKE ?
               AND T1.pls_qty > 0
               AND T3.ps_sell_price BETWEEN ? AND ?
               AND LOWER(T2.pl_description) LIKE ?
               $sizeQuery
            GROUP BY
                T5.p_name, T5.p_color, T7.sz_name,
                T6.pssc_name, T4.st_name,
                T3.ps_barcode, T2.pl_description
            ORDER BY harga ASC
            LIMIT 20
        ";

        try {
            $data = DB::select($sql, $params);
        } catch (\Exception $e) {
            \Log::error("REKOM QUERY ERROR: ".$e->getMessage());
            return [
                'reply' => "Terjadi kesalahan saat mengambil data.",
                'data'  => []
            ];
        }

        if (empty($data)) {
            return [
                'reply' => "Tidak ada produk yang sesuai.",
                'data'  => []
            ];
        }

        $list = [];
        $i = 1;
        foreach ($data as $d) {
            $harga = number_format($d->harga, 0, ',', '.');
            $list[] = "{$i}. {$d->p_name} - Harga: Rp {$harga}, Size: {$d->sz_name}, Stok: {$d->total_qty}";
            $i++;
        }

        return [
            'reply' => implode("\n", $list),
            'data'  => $data
        ];
    }
}