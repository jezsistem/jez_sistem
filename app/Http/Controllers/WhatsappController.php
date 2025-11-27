<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessBroadcastJob;
use App\Models\Store;
use App\Models\WaBroadcastJob;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class WhatsappController extends Controller
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
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
//        $customer = DB::table('customers')->get();
//        dd($customer);
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
//            'customers' => $customer,
            'segment' => request()->segment(1),
        ];
        return view('app.whatsapp.whatsapp', compact('data'));
    }

    public function datatable(Request $request)
    {
        $query = WaBroadcastJob::select([
            'id',
            'job_name',
            'start_at',
            'end_at',
            'interval_hours',
            'batch_size',
            'status',
        ]);

        return DataTables::of($query)

            ->editColumn('start_at', function ($row) {
                return date('d-m-Y H:i', strtotime($row->start_at));
            })
            ->editColumn('end_at', function ($row) {
                return date('d-m-Y H:i', strtotime($row->end_at));
            })

            ->editColumn('status', function ($row) {
                $color = $row->status === 'running' ? 'success' :
                    ($row->status === 'pending' ? 'warning' : 'danger');

                return "<span class='badge badge-$color'>$row->status</span>";
            })

            ->addColumn('action', function ($row) {
                return '
                <button class="btn btn-sm btn-warning editJob" data-id="'.$row->id.'">Edit</button>
                <button class="btn btn-sm btn-danger deleteJob" data-id="'.$row->id.'">Hapus</button>
            ';
            })

            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_name'       => 'required|string',
            'start_at'       => 'required|date',
            'end_at'         => 'required|date|after:start_at',
            'interval_hours' => 'required|integer|min:1',
            'batch_size'     => 'required|integer|min:1',
            'message'        => 'required|string',
        ]);

        $job = WaBroadcastJob::create([
            'job_name'       => $request->job_name,
            'start_at'       => $request->start_at,
            'end_at'         => $request->end_at,
            'interval_hours' => $request->interval_hours,
            'batch_size'     => $request->batch_size,
            'message'        => $request->message,
            'status'         => 'pending',
        ]);

//        ProcessBroadcastJob::dispatch($job);

        return response()->json([
            'status'  => true,
            'message' => 'Job berhasil ditambahkan!',
            'data'    => $job
        ]);
    }

    public function send_whatsapp_nota(Request $request)
    {
        $pos_invoice = $request->pos_invoice;

        $trx_target = DB::table('pos_transactions')->where('pos_invoice', '=', $pos_invoice)->first();

        dd($trx_target);

        $customer = DB::table('customers')->where('id', '=', $trx_target->cust_id)->first();

        $st_id = Auth::user()->st_id;

        $store = Store::where('id', $st_id)->first(); // Assuming you want the store object
        $store_name = $store->st_name;

        $client = new Client();
        $nohp = $customer->cust_phone;
        $receipt_url = url('/e_receipt/' . $pos_invoice);
        $pesan = "Terima kasih telah berbelanja di $store_name.\n".
            "Total transaksi Anda sebesar Rp " . number_format($trx_target->pos_real_price, 0, ',', '.') . ".\n".
            "Silakan cek detail transaksi di: $receipt_url\n\n".
            "---\n".
            "Pesan ini dikirim otomatis, mohon tidak membalas.";


        // ini bagian kirimnya y
        $response = Http::post('http://jezpro.com:3000/send-message', [
            'phone' => $nohp,
            'message' => $pesan
        ]);

        Log::info('WA API Response:', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);


//        Http::post('http://jezpro.com:3000/send-message', [
//            'phone' => $cust->cust_phone,
//            'message' => 'Terima kasih telah berbelanja...'
//        ]);

        return response()->json(['status' => 'ok']);
    }


    public function executeBlast(Request $request)
    {
        $phone = $request->post('wa_phone');
        $type = $request->post('wa_type');
        $message = $request->post('wa_message');
        if ($type == 'all') {
          $customer = DB::table('customers')->select('cust_name', 'cust_phone')
          ->whereNotNull('cust_phone')->get();
          if (!empty($customer)) {
            $data = array();
            $i = 1;
            foreach ($customer as $row) {
              $notif = $this->waSend($row->cust_phone, $message.' - '.$i);
              if ($notif == 'Success') {
                $data[] = [
                  'wa_receiver' => $row->cust_name,
                  'wa_phone' => $row->cust_phone,
                  'wa_status' => 'Terkirim',
                  'created_at' => date('Y-m-d H:i:s')
                ];
              } else {
                $data[] = [
                  'wa_receiver' => $row->cust_name,
                  'wa_phone' => $row->cust_phone,
                  'wa_status' => 'Gagal',
                  'created_at' => date('Y-m-d H:i:s')
                ];
              }
              $i++;
            }
            $save = DB::table('whatsapps')->insert($data);
            if (!empty($save)) {
              $r['status'] = 200;
            } else {
              $r['status'] = 400;
            }
          }
        } else {
          $notif = $this->waSend($phone, $message);
          if ($notif == 'Success') {
            $save = DB::table('whatsapps')->insert([
              'wa_receiver' => $phone,
              'wa_phone' => $phone,
              'wa_status' => 'Terkirim',
              'created_at' => date('Y-m-d H:i:s')
            ]);
          } else {
            $save = DB::table('whatsapps')->insert([
              'wa_receiver' => $phone,
              'wa_phone' => $phone,
              'wa_status' => 'Gagal',
              'created_at' => date('Y-m-d H:i:s')
            ]);
          }
          if (!empty($save)) {
            $r['status'] = 200;
          } else {
            $r['status'] = 400;
          }
        }
        return json_encode($r);
    }
    
    private function waSend($phone, $message)
    {
      $phone = str_replace('+', '', $phone);
        $check = substr($phone, 0, 1);
        if ($check == '0' || $check == 0) {
            $phone = preg_replace('/^0/', '62', $phone);
        }
        $wablas_endpoint = DB::table('web_configs')->select('config_value')
        ->where('config_name', 'wablas_endpoint')->first()->config_value;

        $wablas_api = DB::table('web_configs')->select('config_value')
        ->where('config_name', 'wablas_api')->first()->config_value;

        $curl = curl_init();
        $data = [
            'phone' => $phone,
            'message' => $message,
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "Authorization: $wablas_api",
            )
        );
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, $wablas_endpoint."/api/send-message");
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        curl_close($curl);
        $getdata = json_decode($result);
        if ($getdata->status=='pending') {
            return 'Success';
        } else {
            return 'Fail';
        }
    }
}
