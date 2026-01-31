<?php

namespace App\Services\ai;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JezyService
{

    private function resolvePeriod($intent)
    {
        switch ($intent['period']) {

            case 'last_week':
                return [
                    now()->subWeek()->startOfWeek(),
                    now()->subWeek()->endOfWeek()
                ];

            case 'this_week':
                return [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ];

            case 'this_month':
                return [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ];

            case 'last_month':
                return [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ];

            case 'month_named':
                return [
                    Carbon::create($intent['year'], $intent['month'], 1)->startOfMonth(),
                    Carbon::create($intent['year'], $intent['month'], 1)->endOfMonth()
                ];

            default:
                return [
                    now()->startOfDay(),
                    now()->endOfDay()
                ];
        }
    }

    public function getRekapAbsensi(array $intent)
    {
        [$start, $end] = $this->resolvePeriod($intent);

        $data = DB::table('attendance')
            ->join('users','attendance.user_id','=','users.id')
            ->whereBetween('attendance.at_date', [$start, $end])
            ->whereRaw('UPPER(ts_users.u_name) LIKE ?', ["%{$intent['user']}%"])
            ->get();

        if ($data->isEmpty()) {
            return ['reply' => "📭 Tidak ada data absensi pada periode tersebut."];
        }

        $totalHari = $data->count();
        $ontime = $data->where('at_late_minutes', 0)->count();
        $telat  = $data->where('at_late_minutes', '>', 0)->count();
        $alpha  = $data->whereNull('at_time_in')->count();

        return [
            'user'        => $intent['user'],
            'start'       => $start,
            'end'         => $end,
            'total_days'  => $totalHari,
            'ontime'      => $ontime,
            'late'        => $telat,
            'alpha'       => $alpha,
        ];
    }

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

    public function deteksiStokMinus(array $stokData): array
    {
        $minus = [];

        foreach ($stokData as $row) {
            if ($row->total_qty < 0) {
                $minus[] = $row;
            }
        }

        if (empty($minus)) {
            return [
                'status' => false,
                'message' => null
            ];
        }

        $lines = [];
        foreach ($minus as $m) {
            $lines[] = "- {$m->lokasi} : {$m->total_qty}";
        }

        return [
            'status' => true,
            'message' => "⚠️ Ditemukan stok minus:\n" . implode("\n", $lines)
        ];
    }

    public function rekomTransferCabang(array $stokData): ?string
    {
        $sumber = [];
        $tujuan = [];

        foreach ($stokData as $row) {
            if ($row->total_qty >= 5) {
                $sumber[] = $row;
            } elseif ($row->total_qty <= 1) {
                $tujuan[] = $row;
            }
        }

        if (empty($sumber) || empty($tujuan)) {
            return null;
        }

        $s = $sumber[0];
        $t = $tujuan[0];

        return "🔁 Rekomendasi transfer stok:\n" .
            "- Dari {$s->lokasi} (stok {$s->total_qty})\n" .
            "- Ke {$t->lokasi} (stok {$t->total_qty})";
    }

    public function ringkasanStokSku(string $sku, array $stokData): string
    {
        $total = 0;
        $tersedia = 0;

        foreach ($stokData as $row) {
            $total += $row->total_qty;
            if ($row->total_qty > 0) {
                $tersedia++;
            }
        }

        return
            "📊 Ringkasan stok SKU {$sku}:\n" .
            "- Total stok: {$total}\n" .
            "- Cabang tersedia: {$tersedia}\n" .
            "- Cabang kosong: " . (count($stokData) - $tersedia);
    }

    public function buildAiPromptFromData(string $sku, array $stokData): string
    {
        $lines = [];

        foreach ($stokData as $row) {
            $lines[] = "{$row->lokasi}: {$row->total_qty}";
        }

        return "
            Kamu adalah AI ERP Retail.
            DILARANG menambah produk, SKU, atau angka.
            
            DATA STOK SKU {$sku}:
            " . implode("\n", $lines) . "
            
            Tugas:
            - Rangkum kondisi stok
            - Jelaskan risiko jika stok rendah
            - Jika stok hampir habis, beri saran operasional
            ";
    }

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

    public function getAbsensi(array $intent): array
    {
        $user = strtoupper($intent['user']);
        $date = now()->toDateString();

        if ($intent['range'] === 'yesterday') {
            $date = now()->subDay()->toDateString();
        } elseif ($intent['range'] === 'tomorrow') {
            $date = now()->addDay()->toDateString();
        }

        $schedule = DB::table('daily_schedules')
            ->join('users', 'daily_schedules.user_id', '=', 'users.id')
            ->join('shift_codes', 'daily_schedules.sc_id', '=', 'shift_codes.id')
            ->select(
                'shift_codes.sc_shift_name',
                'shift_codes.sc_start_time',
            )
            ->whereDate('daily_schedules.ds_date', $date)
            ->whereRaw('UPPER(ts_users.u_name) LIKE ?', ["%{$user}%"])
            ->first();

        if (!$schedule) {
            return ['reply' => "📅 {$user} LIBUR Atau BELUM DI SET"];
        }

        $attendance = DB::table('attendance')
            ->join('users', 'attendance.user_id', '=', 'users.id')
            ->whereDate('attendance.at_date', $date)
            ->whereRaw('UPPER(ts_users.u_name) LIKE ?', ["%{$user}%"])
            ->first();

        if (!$attendance) {
            return ['reply' => "❌ {$user} ALPHA (tidak ada absensi)"];
        }

        // 3️⃣ Status manual
        if ($attendance->at_status === 'leave_SICK') {
            return ['reply' => "🤒 {$user} SAKIT"];
        }

        if ($attendance->at_status === 'leave_ANNUAL') {
            return ['reply' => "🏖️ {$user} CUTI"];
        }

        $shiftStart = strtotime($schedule->sc_start_time);
        $checkIn = strtotime($attendance->at_time_in);
//        $tolerance = $schedule->sc_late_tolerance * 60;

        if ($checkIn <= $shiftStart) {
//            return ['reply' => "✅ {$user} ONTIME"];

            return [
                'status' => 'ONTIME',
                'user'   => $user,
                'shift'  => $schedule->sc_shift_name,
                'shift_start' => $schedule->sc_start_time,
                'check_in' => $attendance->at_time_in,
                'late_minutes' => 0,
            ];
        }

        $lateMinutes = round(($checkIn - $shiftStart) / 60);

        return [
            'status' => 'TELAT',
            'user'   => $user,
            'shift'  => $schedule->sc_shift_name,
            'shift_start' => $schedule->sc_start_time,
            'check_in' => $attendance->at_time_in,
            'late_minutes' => $lateMinutes,
        ];
    }
}