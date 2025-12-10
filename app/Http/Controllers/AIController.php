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

        // 1. DETECT INTENT DARI USER
        $intent = $this->detectIntent($message);

        $queryType = $intent['query_type'] ?? 'general';
        $sku = $intent['sku'] ?? null;

        // LOAD MEMORY
        $lastSku = session('last_sku');
        $lastData = session('last_stock_data');
        $lastAi = session('last_ai_answer');

        // 2. HANDLE QUERY STOK SKU
        $contextData = "";
        $data = null;

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
                SELECT s.lokasi,
                       COALESCE(t.total_qty, 0) AS total_qty
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

        // 3. FOLLOW-UP CONTEXT MEMORY
        $followupContext = "";

        if ($lastSku && !$sku) {
            $followupContext .= "SKU sebelumnya adalah: $lastSku.\n";
        }

        if ($lastData) {
            $followupContext .= "Data stok sebelumnya:\n";
            foreach ($lastData as $d) {
                $followupContext .= "- {$d->lokasi}: {$d->total_qty}\n";
            }
        }

        if ($lastAi) {
            $followupContext .= "Jawaban AI sebelumnya: $lastAi\n";
        }

        // 4. SYSTEM PROMPT (BIAR AI TETAP DI TOPIK RETAIL)
        $systemPrompt = "
Kamu adalah AI untuk sistem ERP retail. 
Jawaban HARUS relevan dengan stok, inventory, rekomendasi operasional retail, perpindahan stok, dan analisis SKU.

PERATURAN:
1. Jika stok minus → berikan solusi retail (stock opname, adjustment, mutasi, dll)
2. Jika user follow-up → gunakan context sebelumnya.
3. Jangan bahas cuaca, kesehatan, atau hal di luar inventory.
4. Jika user tanya solusi → berikan langkah nyata.
5. Jangan keluar konteks retail.
";

        // 5. FINAL PROMPT KE OLLAMA
        $prompt =
            $systemPrompt
            . "\n\nCONTEXT SEBELUMNYA:\n"
            . $followupContext
            . "\n\nDATA QUERY SEKARANG:\n"
            . $contextData
            . "\n\nPERTANYAAN USER:\n$message";


        // 6. KIRIM KE OLLAMA STREAMING
        $stream = Http::withOptions(['stream' => true])
            ->post('http://localhost:11434/api/generate', [
                'model' => 'llama3.1',
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

        // 7. SIMPAN MEMORY KE SESSION
        session([
            'last_sku' => $sku ?: $lastSku,
            'last_stock_data' => $data ?: $lastData,
            'last_ai_answer' => $finalAiResponse
        ]);

        // 8. RETURN RESPONSE KE FRONTEND
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
Balas HANYA JSON valid!!

Contoh:
{ \"query_type\": \"stok_sku\", \"sku\": \"ASAVO06BLA\" }
{ \"query_type\": \"general\" }

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

        // Ambil {...}
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

}
