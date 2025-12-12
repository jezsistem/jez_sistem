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

        if (preg_match('/\b(reset|ganti topik|clear|mulai baru|hapus context|mantap|oke jez|terima kasih)\b/i', $message)) {
            session()->forget(['last_sku','last_stock_data','last_ai_answer']);

            return response()->json([
                'reply' => "Baik. Jika ada yang ingin di tanyakan lagi silahkan jez, dengan senang hati JEZY akan membantu kamu semaksimal mungkin 😊."
            ]);
        }

        $intent    = $this->detectIntent($message);
        $queryType = $intent['query_type'] ?? 'general';
        $sku       = $intent['sku'] ?? null;

//        dd($intent, $queryType, $sku);

        // Load Memory (per-session per-user)
        $lastSku  = session('last_sku');
        $lastData = session('last_stock_data');
        $lastAi   = session('last_ai_answer');

        $contextData = "";
        $data        = null;

        /* ---------------------------------------------------------
 * 2B. QUERY REKOMENDASI SEPATU
 * --------------------------------------------------------- */
        if ($queryType === 'rekom_sepatu') {

            // 1. Normalisasi kategori
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

            // 2. Branch
            $branch = strtolower(trim($intent['branch'] ?? 'malang'));

            // 3. Size
            $size = null;
            if (!empty($intent['size'])) {
                $size = preg_replace('/[^0-9]/', '', $intent['size']);
            }
            if (!$size && preg_match('/\bsize\s*:? ?(\d{2})\b/i', $message, $m)) {
                $size = $m[1];
            }

            // 4. Harga
            $min = $intent['min_price'] ?? 0;
            $max = $intent['max_price'] ?? null;

            // parsing angka manual
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

            // 5. Query
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
                $data = [];
            }

            /* ---------------------------------------------------------
             * ⛔ STOP — TIDAK MENGIRIM KE AI
             * Jika data ada → balikan langsung
             * Jika tidak ada → balas tanpa AI
             * --------------------------------------------------------- */

            if (!empty($data)) {
                $list = [];
                $i = 1;
                foreach ($data as $d) {
                    $harga = number_format($d->harga, 0, ',', '.');
                    $list[] = "{$i}. {$d->p_name} - Harga: Rp {$harga}, Size: {$d->sz_name}, Stok: {$d->total_qty}";
                    $i++;
                }

                $reply = implode("\n", $list);

                // Save memory
                session([
                    'last_sku'        => null,
                    'last_stock_data' => $data,
                    'last_ai_answer'  => $reply
                ]);

                return response()->json([
                    'reply' => $reply
                ]);
            }

            // Jika kosong → jangan panggil AI (supaya tidak mengarang)
            $reply = "Tidak ada produk yang sesuai.";

            // Debug SQL
//            dd([
//                'raw_query' => vsprintf(
//                    str_replace(['?'], ["'%s'"], $sql),
//                    array_map(fn($v) => is_null($v) ? 'NULL' : $v, $params)
//                ),
//                'sql'    => $sql,
//                'params' => $params
//            ]);


            session([
                'last_sku'        => null,
                'last_stock_data' => [],
                'last_ai_answer'  => $reply
            ]);



            return response()->json([
                'reply' => $reply
            ]);
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
    Kamu adalah AI ERP retail Bernama Jezy Assistant kamu dibuat oleh Muhammad Royyan Zamzami (pembuat jangan disebutkan di perkenalan kecuali di tanyakan).
     jangan ada kata query jika membalas dan usahaakan berdasarkan data saja, dan boleh menanyakan apa saja selain yang ada di dalam perusahaan

    Aturan:
    1. Jika stok minus → berikan solusi retail.
    2. Follow-up harus menggunakan context sebelumnya.
    3. Jangan bahas hal non-retail.
    4. Semua jawaban harus 100% berdasarkan DATA QUERY SEKARANG.
    5. Jika data kosong → jawab 'Tidak ada produk yang sesuai.'
    6. Jangan mengarang produk/SKU.
    7. Jika Stok sudah hampir habis berikan rekomendasi untuk pengguna.
    8. Jika membalas tolong berikan penjelasan yang menarik


    Nama Perusahaan yaitu PT Zona Karya Nusantara mencangkup Sneakerzone dan JerseyZone, Direktur Perusahaan yaitu Triastana Anang Wibawa

    Sejarah :
    1. 17 Agustus 2013 di jalan Soekarno Hatta no 23 kav 2 dengan nama jerzeyzone (Produk Jersey)
    2. 1 Februari 2017 Pembukaan Sneakerzone Jember
    3. 17 Agustus 2018 Berdiri Sneakerzone yang focus ke produk sepatu Di kota malang
    4. 26 April 2019 Berdiri Cabang Sneakerzone Surabaya
    5. 30 April 2022 berdiri Cabang Sneakerzone Kediri
    6. 4 April 2025 Berdiri cabang Sneakerzone Sidoarjo 
    7. Semarang Berdiri pada tanggal 15 November 2025 

    dari sejarah itu bahwa Jerseyzone di cabang lain masih ada dan di lebur menjadi 1 dengan sneakerzone

    untuk jerzeyzone di cabang di jadikan 1 dengan store sneakerzone
    ";

        /* ---------------------------------------------------------
         * 5. Kirim ke AI (callAI = wrapper kamu untuk Groq/OpenRouter/Ollama)
         * --------------------------------------------------------- */
        $prompt = $systemPrompt
            . "\n\nCONTEXT SEBELUMNYA:\n{$followupContext}"
            . "\n\nDATA QUERY SEKARANG:\n{$contextData}"
            . "\n\nPERTANYAAN USER:\n{$message}";

        $finalAiResponse = $this->callAI($prompt);

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

    private function detectIntent($message)
    {
        $apiKey = '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';
        $response = Http::withHeaders([
            "Authorization" => "Bearer ".$apiKey,
        ])->post("https://api.groq.com/openai/v1/chat/completions", [
            "model" => "llama-3.1-8b-instant",
            "response_format" => [ "type" => "json_object" ],
            "messages" => [
                [
                    "role" => "user",
                    "content" => "
                Buatkan intent JSON.

                Format Wajib:
                {
                   \"query_type\": \"stok_sku | rekom_sepatu | general\",
                   \"sku\": \"optional\",
                   \"category\": \"optional\",
                   \"size\": \"optional\",
                   \"min_price\": \"optional\",
                   \"max_price\": \"optional\",
                   \"branch\": \"optional\"
                }

                Aturan:
                1. Jika mencari stok barcode → query_type = \"stok_sku\"
                2. Jika minta rekomendasi sepatu → query_type = \"rekom_sepatu\"
                3. Selain itu → general

                Pesan user:
                $message
                "
                ]
            ]
        ]);

//        dd(json_encode($response->json()));

        // 🔥 DEBUG RAW RESPONSE
        \Log::info("RAW GROQ RESPONSE = " . json_encode($response->json()));

        if ($response->failed()) {
            return ["query_type" => "general"];
        }

        $json = $response->json()['choices'][0]['message']['content'] ?? "{}";

        \Log::info("PARSED INTENT JSON = ".$json);

        return json_decode($json, true) ?: ["query_type" => "general"];
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

    private function callAi($prompt)
    {
        $apiKey = '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';

//        dd(env('GROQ_API_KEY'));

        if (!$apiKey) {
            return "(GROQ ERROR: API key tidak ditemukan)";
        }

        $url = "https://api.groq.com/openai/v1/chat/completions";

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $apiKey,
            "Content-Type"  => "application/json"
        ])->post($url, [
            "model"    => "llama-3.3-70b-versatile", // MODEL BARU
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ],
            "temperature" => 0.2
        ]);

        if ($response->failed()) {
            return "(GROQ ERROR: ".$response->status()." - ".$response->body().")";
        }

        return $response->json()['choices'][0]['message']['content'] ?? "(NO RESPONSE)";
    }

//    private function callGemini($prompt)
//    {
//        $apiKey = 'AIzaSyBuo87ikW2WRnmjo3g0dallifNtMuvRe5Q';
//
//        if (!$apiKey) {
//            return "(GEMINI ERROR: API key tidak ditemukan)";
//        }
//
//        $url = "https://api.google.ai/v1beta/models/gemini-2.5-flash-lite:generateContent";
//
//        $payload = Http::withHeaders([
//            "Content-Type" => "application/json",
//            "x-goog-api-key" => $apiKey
//        ])->post($url, [
//            "contents" => [
//                [
//                    "parts" => [
//                        ["text" => $prompt]
//                    ]
//                ]
//            ]
//        ]);
//
//        $response = Http::withHeaders([
//            "Content-Type" => "application/json"
//        ])->post($url, $payload);
//
//        if ($response->failed()) {
//            return "(GEMINI ERROR: " . $response->status() . " - " . $response->body() . ")";
//        }
//
//        $json = $response->json();
//
//        return $json['candidates'][0]['content']['parts'][0]['text']
//            ?? "(NO RESPONSE)";
//    }

}
