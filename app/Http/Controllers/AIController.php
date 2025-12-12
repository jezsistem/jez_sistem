<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDivision;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    protected function validateAccess()
    {
        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => request()->segment(1)
            ])->exists();
        if (!$validate) {
            dd("Anda tidak memiliki akses ke menu ini, hubungi Administrator");
        }
    }

    protected function sidebar()
    {
        $ma_id = DB::table('user_menu_accesses')->select('ma_id')
            ->where('u_id', Auth::user()->id)->get();
        $ma_id_arr = array();
        if (!empty($ma_id)) {
            foreach ($ma_id as $row) {
                array_push($ma_id_arr, $row->ma_id);
            }
        }

        $sidebar = array();
        $mt = DB::table('menu_titles')->orderBy('mt_sort')->get();
        if (!empty($mt->first())) {
            foreach ($mt as $row) {
                $ma = DB::table('menu_accesses')
                    ->where('mt_id', '=', $row->id)
                    ->whereIn('id', $ma_id_arr)
                    ->orderBy('ma_sort')->get();
                if (!empty($ma->first())) {
                    $row->ma = $ma;
                    array_push($sidebar, $row);
                }
            }
        }
        return $sidebar;
    }

    public function index()
    {
        $this->validateAccess();
        $user = new User();
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        $divisions = UserDivision::orderBy('ud_name')->get();
        return view('app.ai.ai', compact('data', 'divisions'));
    }

    public function chat(Request $request)
    {
        $message = $request->input('message');

        /* ---------------------------------------------------------
         * 0. RESET CHAT / GANTI TOPIK
         * --------------------------------------------------------- */
        if (preg_match('/\b(reset|ganti topik|clear|mulai baru|hapus context|mantap|oke jez|terima kasih)\b/i', $message)) {
            session()->forget(['last_sku','last_stock_data','last_ai_answer']);

            return response()->json([
                'reply' => "Topik telah direset. Silakan mulai percakapan baru."
            ]);
        }

        /* ---------------------------------------------------------
         * 1. DETECT INTENT
         * --------------------------------------------------------- */
        $intent    = $this->detectIntent($message);
        $queryType = $intent['query_type'] ?? 'general';
        $sku       = $intent['sku'] ?? null;

        // Load Memory
        $lastSku  = session('last_sku');
        $lastData = session('last_stock_data');
        $lastAi   = session('last_ai_answer');

        $contextData = "";
        $data        = null;

        /* ---------------------------------------------------------
         * 2A. QUERY STOK SKU
         * --------------------------------------------------------- */
        if ($queryType === 'stok_sku' && $sku) {

            $data = DB::select("
            WITH store_list AS (
              SELECT 'MALANG' AS lokasi UNION ALL
              SELECT 'SURABAYA' UNION ALL
              SELECT 'SIDOARJO' UNION ALL
              SELECT 'KEDIRI' UNION ALL
              SELECT 'JEMBER' UNION ALL
              SELECT 'SEMARANG'
            )
            SELECT s.lokasi, COALESCE(t.total_qty, 0) AS total_qty
            FROM store_list s
            LEFT JOIN (
                SELECT
                    UPPER(pl_description) AS lokasi,
                    COALESCE(SUM(T1.pls_qty), 0) AS total_qty
                FROM ts_product_location_setups T1
                LEFT JOIN ts_product_locations T2 ON T2.id = T1.pl_id
                LEFT JOIN ts_stores T3 ON T3.id = T2.st_id
                LEFT JOIN ts_product_stocks T4 ON T4.id = T1.pst_id
                LEFT JOIN ts_products T5 ON T5.id = T4.p_id
                WHERE T4.ps_barcode = ?
                  AND (
                        (T5.p_turnoverclass LIKE 'SLOW MOVING%' AND pl_allowed_update_stock = TRUE)
                    OR  (T5.p_turnoverclass LIKE 'NON MOVING%' AND pl_allowed_update_stock = TRUE)
                    OR  (T5.schema_size NOT LIKE '%Footwear%' AND pl_freeze = FALSE)
                    OR  (T5.schema_size LIKE '%Footwear%' AND pl_allowed_update_stock = TRUE)
                      )
                GROUP BY UPPER(pl_description)
            ) t ON t.lokasi = s.lokasi
            ORDER BY s.lokasi
        ", [$sku]);

            $contextData .= "DATA STOK SKU $sku:\n";
            foreach ($data as $d) {
                $contextData .= "- {$d->lokasi}: {$d->total_qty}\n";
            }
        }

        /* ---------------------------------------------------------
         * 2B. QUERY REKOMENDASI SEPATU
         * --------------------------------------------------------- */
        if ($queryType === 'rekom_sepatu') {

            // 1. AI Extraction
            $category = strtolower($intent['category'] ?? 'running');
            $branch   = strtolower($intent['branch'] ?? 'malang');
            $size     = $intent['size'] ?? null;

            // 2. Range harga AI
            $min = $intent['min_price'] ?? 0;
            $max = $intent['max_price'] ?? null;

            // 3. Ekstraksi angka manual dari pesan
            $pesan = strtolower($message);
            $angka1 = $this->normalizeHarga($pesan);
            $angka2 = null;

            if (preg_match('/(\d+)\s*(?:-|to|sampai|s\/d|–)\s*(\d+)/', $pesan, $mm)) {
                $angka1 = $this->normalizeHarga($mm[1]);
                $angka2 = $this->normalizeHarga($mm[2]);
            }

            if ($angka1 && !$angka2) $max = $angka1;
            if ($angka1 && $angka2) {
                $min = min($angka1, $angka2);
                $max = max($angka1, $angka2);
            }
            if (!$max) $max = 99999999;

            // 4. Bind Params
            $params = [
                "%{$category}%",
                $min,
                $max,
                "%{$branch}%",
            ];

            $sizeQuery = "";
            if (!empty($size)) {
                $sizeQuery = " AND T7.sz_name = ? ";
                $params[]  = $size;
            }

            // 5. Query
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

            $data = DB::select($sql, $params);

            // 6. Format context
            $contextData .= "Tampilkan hanya data berikut tanpa menambah merek lain, dan jangan mengarang jika data tidak ditemukan.\n";
            $contextData .= "REKOMENDASI SEPATU '{$category}' | Harga {$min} - {$max} | Cabang: {$branch}\n";

            if (empty($data)) {
                $contextData .= "- Tidak ada produk ditemukan.\n";
            } else {
                foreach ($data as $d) {
                    $contextData .= "- {$d->p_name} ({$d->pssc_name}, Size {$d->sz_name}) | Rp {$d->harga} | Lokasi: {$d->pl_description} | Stock Total: {$d->total_qty}\n";
                }
            }
        }

        /* ---------------------------------------------------------
         * 3. MEMORY CONTEXT
         * --------------------------------------------------------- */
        $followupContext = "";

        if ($lastSku && !$sku) {
            $followupContext .= "SKU sebelumnya adalah: $lastSku.\n";
        }

        if ($lastData) {
            $followupContext .= "Data stok sebelumnya:\n";
            foreach ($lastData as $d) {
                $followupContext .= "- {$d->total_qty}\n";
            }
        }

        if ($lastAi) {
            $followupContext .= "Jawaban AI sebelumnya: $lastAi\n";
        }

        /* ---------------------------------------------------------
         * 4. SYSTEM PROMPT
         * --------------------------------------------------------- */
        $systemPrompt = "
        Kamu adalah AI ERP retail.
        Jawaban HARUS terkait inventory, stok, SKU, rekomendasi sepatu, dsb.
        Tidak boleh keluar topik retail. dan ketika membalas pertama kali kasih sapaan dengan 'Halo Jez'

        Aturan:
        1. Jika stok minus → berikan solusi retail.
        2. Follow-up harus menggunakan context sebelumnya.
        3. Jangan bahas hal non-retail.
        4. Semua jawaban harus 100% berdasarkan DATA QUERY SEKARANG.
        5. Jika data kosong → jawab 'Tidak ada produk yang sesuai.'
        6. Jangan mengarang produk/SKU.
        
        Sejarah :
        1. 17 Agustus 2013 di jalan Soekarno Hatta no 23 kav 2 dengan nama jerzeyzone (Produk Jersey)
        2. 1 Februari 2017 Pembukaan Sneakerzone Jember
        3. 17 Agustus 2018 Berdiri Sneakerzone yang focus ke produk sepatu Di kota malang
        4. 26 April 2019 Berdiri Cabang Sneakerzone Surabaya
        5. 30 April 2022 berdiri Cabang Sneakerzone Kediri
        6. 4 April 2025 Berdiri cabang Sneakerzone Sidoarjo 
        7. Semarang Berdiri pada tanggal 15 November 2025 
        
        Direktur Perusahaan Triastana Anang Wibawa
        
        untuk jerzeyzone di cabang di jadikan 1 dengan store sneakerzone
    ";

        /* ---------------------------------------------------------
         * 5. Kirim ke Ollama
         * --------------------------------------------------------- */
        $prompt = $systemPrompt
            . "\n\nCONTEXT SEBELUMNYA:\n{$followupContext}"
            . "\n\nDATA QUERY SEKARANG:\n{$contextData}"
            . "\n\nPERTANYAAN USER:\n{$message}";

        $stream = Http::withOptions(['stream' => true])
            ->post('http://localhost:11434/api/generate', [
                'model'  => 'llama3.1',
                'prompt' => $prompt
            ]);

        $raw = $stream->body();
        $lines = explode("\n", $raw);

        $finalAiResponse = "";
        foreach ($lines as $line) {
            $row = json_decode($line, true);
            if (isset($row['response'])) {
                $finalAiResponse .= $row['response'];
            }
        }

        /* ---------------------------------------------------------
         * 6. SAVE MEMORY
         * --------------------------------------------------------- */
        session([
            'last_sku'        => $sku ?: $lastSku,
            'last_stock_data' => $data ?: $lastData,
            'last_ai_answer'  => $finalAiResponse
        ]);

        /* ---------------------------------------------------------
         * 7. RETURN
         * --------------------------------------------------------- */
        return response()->json([
            'reply' => $finalAiResponse ?: "(AI tidak merespon)"
        ]);
    }


    // --------------------------------------------
    // DETECT INTENT DENGAN LLM
    // --------------------------------------------
    private function detectIntent($message)
    {
        $prompt = "
        Kamu adalah AI intent classifier.
        Balas HANYA JSON valid!! Jangan memberikan kalimat lain.

        Tugas:
        - Identifikasi apakah user meminta rekomendasi sepatu.
        - Ekstrak angka harga (contoh: '300 ribuan' → 300000).
        - Jika ada range harga (contoh: '300-500') → min_price & max_price.
        - Jika hanya 1 angka (contoh: '500 ribu') → min_price = 0, max_price = 500000.
        - Jika user tidak menyebut angka → tetap query_type 'general'.

        Format JSON contoh:
        { \"query_type\": \"stok_sku\", \"sku\": \"ASAVO06BLA\" }
        { \"query_type\": \"general\" }
        { \"query_type\": \"rekom_sepatu\", \"min_price\": 300000, \"max_price\": 500000 }

        Ingat:
        - Balas hanya JSON.
        - Tidak boleh ada kata lain di luar JSON.

        Pesan user:
        $message
    ";

        $response = Http::withOptions(['stream' => true])
            ->post('http://localhost:11434/api/generate', [
                'model' => 'llama3.1',
                'prompt' => $prompt
            ]);

        $raw = $response->body();
        $lines = explode("\n", $raw);

        $jsonText = "";
        foreach ($lines as $line) {
            $row = json_decode($line, true);
            if (isset($row['response'])) {
                $jsonText .= $row['response'];
            }
        }

        // Ambil JSON {...}
        preg_match('/\{.*\}/s', $jsonText, $m);

        if (!isset($m[0])) {
            return ['query_type' => 'general'];
        }

        $parsed = json_decode($m[0], true);

        if (!is_array($parsed)) {
            return ['query_type' => 'general'];
        }

        return $parsed;
    }


    private function normalizeHarga($text)
    {
        $text = strtolower($text);

        if (preg_match('/(\d+)\s*(ribu|rb|k)/', $text, $m)) {
            return intval($m[1]) * 1000;
        }

        if (preg_match('/\d{5,7}/', $text, $m)) {
            return intval($m[0]);
        }

        if (preg_match('/\b(\d{2,3})\b/', $text, $m)) {
            return intval($m[1]) * 1000;
        }

        return null;
    }

}
