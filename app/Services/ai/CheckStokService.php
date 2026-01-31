<?php

namespace App\Services\ai;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckStokService
{
    public function stokBySku(string $sku): array
    {
        $sql = "
            WITH store_list AS (
                SELECT 'MALANG' AS lokasi UNION ALL
                SELECT 'SURABAYA' UNION ALL
                SELECT 'SIDOARJO' UNION ALL
                SELECT 'KEDIRI' UNION ALL
                SELECT 'JEMBER' UNION ALL
                SELECT 'SEMARANG'
            )
            SELECT 
                s.lokasi,
                COALESCE(t.total_qty, 0) AS total_qty
            FROM store_list s
            LEFT JOIN (
                SELECT
                    UPPER(T2.pl_description) AS lokasi,
                    COALESCE(SUM(T1.pls_qty), 0) AS total_qty
                FROM ts_product_location_setups T1
                LEFT JOIN ts_product_locations T2 ON T2.id = T1.pl_id
                LEFT JOIN ts_stores T3 ON T3.id = T2.st_id
                LEFT JOIN ts_product_stocks T4 ON T4.id = T1.pst_id
                LEFT JOIN ts_products T5 ON T5.id = T4.p_id
                WHERE T4.ps_barcode = ?
                  AND (
                        (T5.p_turnoverclass LIKE 'SLOW MOVING%' AND T2.pl_allowed_update_stock = TRUE)
                     OR (T5.p_turnoverclass LIKE 'NON MOVING%'  AND T2.pl_allowed_update_stock = TRUE)
                     OR (T5.schema_size NOT LIKE '%Footwear%' AND T2.pl_freeze = FALSE)
                     OR (T5.schema_size LIKE '%Footwear%' AND T2.pl_allowed_update_stock = TRUE)
                  )
                GROUP BY UPPER(T2.pl_description)
            ) t ON t.lokasi = s.lokasi
            ORDER BY s.lokasi
        ";

        try {
            $data = DB::select($sql, [$sku]);
        } catch (\Exception $e) {
            Log::error("STOK SKU QUERY ERROR: " . $e->getMessage());

            return [
                'reply' => "Terjadi kesalahan saat mengambil data stok.",
                'data'  => []
            ];
        }

        if (empty($data)) {
            return [
                'reply' => "SKU {$sku} tidak ditemukan.",
                'data'  => []
            ];
        }

        // Format jawaban
        $lines = [];
        foreach ($data as $row) {
            $lines[] = "- {$row->lokasi} : {$row->total_qty}";
        }

        $reply = "Stok SKU {$sku} per cabang:\n" . implode("\n", $lines);

        // Insight tambahan (opsional tapi keren)
        $total = array_sum(array_map(fn($r) => $r->total_qty, $data));
        if ($total <= 3) {
            $reply .= "\n\n⚠️ Stok hampir habis, disarankan segera replenishment.";
        }

        return [
            'reply' => $reply,
            'data'  => $data
        ];
    }
}