<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDivision;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

// ai service nih
use App\Services\ai\JezyService;

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

    public function chat(Request $request, JezyService $service)
    {
        $message = $request->input('message');

        if (preg_match('/\b(reset|ganti topik|clear|mulai baru|hapus context|mantap|oke jez|terima kasih)\b/i', $message)) {
            session()->forget(['last_sku', 'last_stock_data', 'last_ai_answer']);
            session()->regenerate();

            return response()->json([
                'reply' => "Baik. Jika ada yang ingin di tanyakan lagi silahkan jez, dengan senang hati JEZY akan membantu kamu semaksimal mungkin 😊."
            ]);
        }

        $intent = $this->detectIntent($message);
        $queryType = $intent['query_type'] ?? 'general';
        $sku = $intent['sku'] ?? null;

//        dd($intent, $queryType, $sku);

        $lastSku = session('last_sku');
        $lastData = session('last_stock_data');
        $lastAi = session('last_ai_answer');

        $contextData = "";
        $data = null;

        if ($queryType === 'stok_sku' && $sku) {

            $result = $service->stokBySku($sku);
            $stokData = $result['data'];

            $reply = $result['reply'];

            $minus = $service->deteksiStokMinus($stokData);
            if ($minus['status']) {
                $reply .= "\n\n" . $minus['message'];
            }

            $transfer = $service->rekomTransferCabang($stokData);
            if ($transfer) {
                $reply .= "\n\n" . $transfer;
            }

            $reply .= "\n\n" . $service->ringkasanStokSku($sku, $stokData);

            $prompt = $service->buildAiPromptFromData($sku, $stokData);
            $aiSummary = $this->callAI($prompt);

            if ($aiSummary) {
                $reply .= "\n\nInsight AI:\n" . $aiSummary;
            }

            session([
                'last_sku' => $sku,
                'last_stock_data' => $stokData,
                'last_ai_answer' => $reply
            ]);

            return response()->json([
                'reply' => $reply
            ]);
        }

        if ($queryType === 'rekom_sepatu') {
            $result = $service->rekomSepatu($message, $intent);

            session([
                'last_sku' => null,
                'last_stock_data' => $result['data'],
                'last_ai_answer' => $result['reply']
            ]);

            return response()->json([
                'reply' => $result['reply']
            ]);
        }

        if ($queryType === 'jadwal') {

            if (empty($intent['user'])) {
                $intent['user'] = Auth::user()->u_name;
            }

            $result = $service->getJadwal($intent);

            return response()->json([
                'reply' => $result['reply']
            ]);
        }

        if ($queryType === 'absensi') {

            if (empty($intent['user'])) {
                $intent['user'] = Auth::user()->u_name;
            }

            $result = $service->getAbsensi($intent);

            $prompt = "
                Kamu adalah HR Assistant.
                JANGAN mengubah data.
                JANGAN menambah angka.
                Gunakan bahasa profesional dan ramah.
                
                DATA ABSENSI:
                Nama: {$result['user']}
                Status: {$result['status']}
                Shift: {$result['shift']}
                Jam Shift: {$result['shift_start']}
                Jam Masuk: {$result['check_in']}
                Telat: {$result['late_minutes']} menit
            ";

            $reply = $this->callAi($prompt);

            return response()->json([
                'reply' => $reply
            ]);
        }

        if ($queryType === 'rekap_absensi') {

            if (empty($intent['user'])) {
                $intent['user'] = Auth::user()->u_name;
            }

            $result = $service->getRekapAbsensi($intent);

            $prompt = "
                Kamu adalah HR Assistant profesional.
                
                ATURAN KETAT:
                - Jangan mengubah angka
                - Jangan menambah data
                - Jangan membuat asumsi
                - Gunakan bahasa formal namun ramah
                - Fokus pada ringkasan kinerja
                
                DATA REKAP ABSENSI:
                Nama: {$result['user']}
                Periode: {$result['start']} s/d {$result['end']}
                Total Hari Kerja: {$result['total_days']}
                Tepat Waktu: {$result['ontime']}
                Terlambat: {$result['late']}
                Alpha: {$result['alpha']}
                
                OUTPUT WAJIB:
                - Paragraf pembuka singkat
                - Ringkasan poin (bullet)
                - Catatan singkat (jika ada keterlambatan)
                - Catatan Jika sering pulang molor
             ";

            $reply = $this->callAi($prompt);

            return response()->json([
                'reply' => $reply
            ]);
        }

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

        $prompt = $systemPrompt
            . "\n\nCONTEXT SEBELUMNYA:\n{$followupContext}"
            . "\n\nDATA QUERY SEKARANG:\n{$contextData}"
            . "\n\nPERTANYAAN USER:\n{$message}";

        $finalAiResponse = $this->callAI($prompt);

        session([
            'last_sku' => $sku ?: $lastSku,
            'last_stock_data' => $data ?: $lastData,
            'last_ai_answer' => $finalAiResponse
        ]);

        return response()->json([
            'reply' => $finalAiResponse ?: "(AI tidak merespon)"
        ]);
    }

    private function detectIntent($message)
    {
        $skuMatch = [];
        $branchMatch = [];

        if (preg_match('/\b(?=[A-Z0-9]{7,}\b)(?=[A-Z0-9]*\d)[A-Z0-9]+\b/i', $message, $skuMatch)) {

            preg_match('/malang|surabaya|sidoarjo|kediri|jember|semarang/i', $message, $branchMatch);

            return [
                'query_type' => 'stok_sku',
                'sku' => strtoupper($skuMatch[0]),
                'branch' => isset($branchMatch[0]) ? strtoupper($branchMatch[0]) : null,
            ];
        }

//        dd($skuMatch[0]);

        if (
            preg_match('/rekomendasi|sarankan|cari|sepatu/i', $message) &&
            preg_match('/running|lari|jogging|futsal|basket|casual|sneakers/i', $message)
        ) {
            preg_match('/(\d[\d\.]{2,})\s*-\s*(\d[\d\.]{2,})/', $message, $priceMatch);
            preg_match('/size\s*(\d{2})/', $message, $sizeMatch);

            return [
                'query_type' => 'rekom_sepatu',
                'category' => preg_match('/running|lari|jogging/i', $message) ? 'running' : null,
                'min_price' => isset($priceMatch[1]) ? (int)str_replace('.', '', $priceMatch[1]) : null,
                'max_price' => isset($priceMatch[2]) ? (int)str_replace('.', '', $priceMatch[2]) : null,
                'size' => $sizeMatch[1] ?? null,
            ];
        }

        if (preg_match('/absen|absensi|kehadiran|telat|terlambat|ontime/i', $message)) {

            $range = 'today';
            if (preg_match('/besok/i', $message)) {
                $range = 'tomorrow';
            } elseif (preg_match('/kemarin/i', $message)) {
                $range = 'yesterday';
            }

            return [
                'query_type' => 'absensi',
                'user' => $this->extractHrisUser($message),
                'range' => $range,
            ];
        }

        $monthMap = [
            'januari' => 1, 'februari' => 2, 'maret' => 3,
            'april' => 4, 'mei' => 5, 'juni' => 6,
            'juli' => 7, 'agustus' => 8, 'september' => 9,
            'oktober' => 10, 'november' => 11, 'desember' => 12,
        ];

        $month = null;

        foreach ($monthMap as $name => $num) {
            if (preg_match("/{$name}/i", $message)) {
                $month = $num;
                break;
            }
        }

        if (preg_match('/rekap|laporan|summary|ringkasan/i', $message)
            && preg_match('/absen|absensi|kehadiran|telat/i', $message)
        ) {

            $period = 'today';

            if (preg_match('/minggu kemarin/i', $message)) {
                $period = 'last_week';
            } elseif (preg_match('/minggu ini/i', $message)) {
                $period = 'this_week';
            } elseif (preg_match('/bulan ini/i', $message)) {
                $period = 'this_month';
            } elseif (preg_match('/bulan kemarin/i', $message)) {
                $period = 'last_month';
            } elseif (preg_match('/desember/i', $message)) {
                $period = 'month_named';
            }

            return [
                'query_type' => 'rekap_absensi',
                'user'   => null,
                'period' => $period,
                'month'  => 12, // kalau disebut
                'year'   => now()->year,
            ];
        }



        if (preg_match('/jadwal|shift|masuk apa|masuk jam|kerja/i', $message)) {

            $range = 'today';
            if (preg_match('/besok/i', $message)) {
                $range = 'tomorrow';
            } elseif (preg_match('/kemarin/i', $message)) {
                $range = 'yesterday';
            } elseif (preg_match('/lusa/i', $message)) {
                $range = 'day_after_tomorrow';
            }

            return [
                'query_type' => 'jadwal',
                'user' => $this->extractHrisUser($message),
                'range' => $range,
            ];
        }


        $apiKey = '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';

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $apiKey,
            "Content-Type" => "application/json",
        ])->post("https://api.groq.com/openai/v1/chat/completions", [
            "model" => "llama-3.1-8b-instant",
            "temperature" => 0,
            "messages" => [
                [
                    "role" => "system",
                    "content" => "
                    Kamu adalah intent classifier ERP retail.
                    Balas HANYA dengan 1 JSON OBJECT VALID.
                    TIDAK BOLEH ADA TEKS LAIN.
                    "
                ],
                [
                    "role" => "user",
                    "content" => "
                    FORMAT WAJIB:
                    {
                      \"query_type\": \"stok_sku | rekom_sepatu | general\",
                      \"sku\": null | string,
                      \"branch\": null | string
                    }
                    
                    ATURAN:
                    - Jika ada kata 'stok' dan ada SKU → stok_sku
                    - rekomendasi sepatu → rekom_sepatu
                    - Jika ragu → general
                    
                    PESAN USER:
                    {$message}
                    "
                ]
            ]
        ]);

        if ($response->failed()) {
            return ["query_type" => "general"];
        }

        $raw = $response->json()['choices'][0]['message']['content'] ?? '';

        \Log::info("RAW INTENT STRING = " . $raw);

        // 3️⃣ HARD JSON EXTRACTION (ANTI NACAL)
        if (preg_match('/\{.*\}/s', $raw, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return ["query_type" => "general"];
    }

    private function detectIdntent($message)
    {
        $apiKey = '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';
        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $apiKey,
        ])->post("https://api.groq.com/openai/v1/chat/completions", [
            "model" => "llama-3.1-8b-instant",
            "response_format" => ["type" => "json_object"],
            "messages" => [
                [
                    "role" => "user",
                    "content" => "
                Buatkan intent JSON.

                FORMAT WAJIB:
                {
                  \"query_type\": \"stok_sku | rekom_sepatu | general\",
                  \"sku\": null | string,
                  \"branch\": null | string,
                  \"category\": null | string,
                  \"size\": null | string,
                  \"min_price\": null | number,
                  \"max_price\": null | number
                }

                ATURAN KETAT:
                1. Jika user menanyakan JUMLAH STOK atau KATA 'stok' dan ADA SKU → query_type = stok_sku
                2. Jika query_type = stok_sku → SKU WAJIB ADA
                3. Jika disebut nama kota → isi branch (MALANG, SURABAYA, dll)
                4. Jika tidak yakin → general
                5. JANGAN mengarang data
                6. Jika data kosong → jawab Tidak ada produk dan berikan layanan lainnya
                
                TUGASMU: mengembalikan 1 OBJECT JSON SAJA. 

                Pesan user:
                $message
                "
                ]
            ]
        ]);

        // 🔥 DEBUG RAW RESPONSE
        \Log::info("RAW GROQ RESPONSE = " . json_encode($response->json()));

        if ($response->failed()) {
            return ["query_type" => "general"];
        }

        $json = $response->json()['choices'][0]['message']['content'] ?? "{}";

        \Log::info("PARSED INTENT JSON = " . $json);

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
            "Content-Type" => "application/json"
        ])->post($url, [
            "model" => "llama-3.3-70b-versatile", // MODEL BARU
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ],
            "temperature" => 0.2
        ]);

        if ($response->failed()) {
            return "(GROQ ERROR: " . $response->status() . " - " . $response->body() . ")";
        }

        return $response->json()['choices'][0]['message']['content'] ?? "(NO RESPONSE)";
    }

    private function extractHrisUser(string $message): ?string
    {
        $message = strtolower($message);
        $message = preg_replace('/[^\w\s]/', ' ', $message);

        $stopWords = [
            'apakah', 'cek', 'lihat', 'tolong', 'info',
            'absen', 'absensi', 'kehadiran',
            'telat', 'terlambat', 'ontime',
            'jadwal', 'shift', 'masuk', 'kerja',
            'hari', 'ini', 'besok', 'kemarin', 'lusa',
            'yang', 'di', 'ke', 'dari', 'apa',
            'saya', 'aku', 'gue'
        ];

        $tokens = array_filter(explode(' ', $message));

        $candidates = [];

        foreach ($tokens as $token) {
            if (strlen($token) >= 3 && !in_array($token, $stopWords)) {
                $candidates[] = $token;
            }
        }

        if (empty($candidates)) {
            return null;
        }

        return strtoupper(implode(' ', $candidates));
    }



}
