<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;

class WhatsappController extends Controller
{
  protected function validateAccess($slug = null)
    {
        $ma_slug = $slug ?? request()->segment(1);
        $validate = DB::table('user_menu_accesses')
        ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
            'u_id' => Auth::user()->id,
            'ma_slug' => $ma_slug
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
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        return view('app.whatsapp.whatsapp', compact('data'));
    }

    public function indexUpdated()
    {
        $this->validateAccess('whatsapp'); // Gunakan akses yang sama dengan halaman lama
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'whatsapp')->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        return view('app.updated_whatsapp.whatsapp', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('whatsapps')->select('id', 'wa_receiver', 'wa_phone', 'wa_status', 'created_at'))
            ->editColumn('created_at_show', function($data){
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(wa_receiver) LIKE ?', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');

            $query = DB::table('whatsapps')->select('id', 'wa_receiver', 'wa_phone', 'wa_status', 'created_at');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('wa_receiver', 'LIKE', "%$search%")
                        ->orWhere('wa_phone', 'LIKE', "%$search%");
                });
            }

            $total = $query->count();
            $totalPages = ceil($total / $perPage);

            $results = $query->orderBy('created_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            $data = [];
            $no = ($page - 1) * $perPage + 1;
            foreach ($results as $row) {
                $statusClass = $row->wa_status == 'Terkirim' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                $data[] = [
                    'no' => $no++,
                    'id' => $row->id,
                    'wa_receiver' => $row->wa_receiver ?? '-',
                    'wa_phone' => $row->wa_phone ?? '-',
                    'wa_status' => $row->wa_status,
                    'wa_status_class' => $statusClass,
                    'created_at' => date('d/m/Y H:i:s', strtotime($row->created_at)),
                ];
            }

            return response()->json([
                'data' => $data,
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => (int) $page,
                'per_page' => (int) $perPage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => [],
                'total' => 0,
                'total_pages' => 0,
                'current_page' => 1,
                'per_page' => 25
            ], 500);
        }
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
