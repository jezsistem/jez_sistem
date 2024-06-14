<?php

namespace App\Http\Controllers;

use App\Models\CustomerRating;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserRating;
use App\Models\Wilayah;
use App\Models\Customer;

class UserRatingController extends Controller
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
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        return view('app.user_rating.user_rating', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(UserRating::selectRaw('ts_user_ratings.id as ur_id, ts_users.id as u_id, u_name, st_name, stt_name, sum(ur_value) as value, count(ts_user_ratings.id) as qty, ts_user_ratings.created_at as ur_created')
            ->leftJoin('users', 'users.id', '=', 'user_ratings.user_id')
            ->leftJoin('stores', 'stores.id', '=', 'user_ratings.st_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'user_ratings.stt_id')
            ->groupBy('users.id'))
            ->editColumn('rating_qty', function($data) use ($request){
                $exp = explode('|', $request->rating_date);
                if (count($exp) > 1) {
                  $start = $exp[0];
                  $end = $exp[1];
                } else {
                  $start = $exp[0];
                  $end = $exp[0];
                }
                if ($start != $end) {
                  $range = 'true';
                } else {
                  $range = 'false';
                }
                if ($range == 'true') {
                  $qty = UserRating::select('id', 'created_at')->where('user_id', '=', $data->u_id)
                  ->whereBetween('created_at', [$start, $end])->count('id');
                } else {
                  $qty = UserRating::select('id')->where('user_id', '=', $data->u_id)
                  ->whereDate('created_at', '=', $start)->count('id');
                }
                return $qty;
            })
            ->editColumn('rating_total', function($data){
                $rate = 5 * $data->qty;
                $total_rate = $data->value / $rate * 100;
                return round($total_rate).' / 100';
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('u_name', 'LIKE', "%$search%");
                    });
                }
                if (!empty($request->get('rating_date'))) {
                    $instance->where(function($w) use($request){
                        $date = $request->get('rating_date');
                        if (!empty($date)) {
                          $exp = explode('|', $date);
                          if (count($exp) > 1) {
                            $start = $exp[0];
                            $end = $exp[1];
                          } else {
                            $start = $exp[0];
                            $end = $exp[0];
                          }
                          if ($start != $end) {
                            $range = 'true';
                          } else {
                            $range = 'false';
                          }
                          if ($range == 'true') {
                            $w->whereBetween('user_ratings.created_at', [$start, $end]);
                          } else {
                            $w->whereDate('user_ratings.created_at', $start);
                          }
                        }

                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getHistoryDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(UserRating::selectRaw('ts_user_ratings.id as ur_id, ts_pos_transactions.id as pt_id, u_name, st_name, stt_name, pos_invoice, cust_name, ur_value, ur_description, ts_user_ratings.created_at as ur_created')
            ->leftJoin('users', 'users.id', '=', 'user_ratings.user_id')
            ->leftJoin('stores', 'stores.id', '=', 'user_ratings.st_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'user_ratings.stt_id')
            ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'user_ratings.pt_id')
            ->leftJoin('customers', 'customers.id', '=', 'user_ratings.cust_id'))
            ->editColumn('pos_invoice', function($data){
                return '<span class="btn btn-sm btn-primary" id="sales_item_detail_btn" data-pt_id="'.$data->pt_id.'">'.$data->pos_invoice.'</span>';
            })
            ->rawColumns(['pos_invoice'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('u_name', 'LIKE', "%$search%");
                    });
                }
                if (!empty($request->get('rating_date'))) {
                    $instance->where(function($w) use($request){
                        $date = $request->get('rating_date');
                        if (!empty($date)) {
                          $exp = explode('|', $date);
                          if (count($exp) > 1) {
                            $start = $exp[0];
                            $end = $exp[1];
                          } else {
                            $start = $exp[0];
                            $end = $exp[0];
                          }
                          if ($start != $end) {
                            $range = 'true';
                          } else {
                            $range = 'false';
                          }
                          if ($range == 'true') {
                            $w->whereBetween('user_ratings.created_at', [$start, $end]);
                          } else {
                            $w->whereDate('user_ratings.created_at', $start);
                          }
                        }

                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function customerIndex()
    {
        $user = new User;
        $select = ['u_name', 'u_email', 'u_phone', 'g_name'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $path = "
        <li class='breadcrumb-item'>
            <a href='' class='text-muted'>User Performance</a>
        </li>
        <li class='breadcrumb-item'>
            <a href='' class='text-muted'>User Rating</a>
        </li>";
        $data = [
            'title' => $title,
            'subtitle' => 'Rating By Customer',
            'path' => $path,
            'user' => $user_data,
            'segment' => request()->segment(1),
            'store_name' => Store::select('st_name')->where('id', Auth::user()->st_id)->get()->first()->st_name
        ];
        return view('app.rating_by_customer.rating_by_customer', compact('data'));
    }

    function fetchSubdistrict(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');
            $type = $request->get('type');
            $data = Wilayah::selectRaw('CONCAT(kode) as kode, CONCAT(nama) as nama')
            ->whereRaw('LENGTH(kode) = 8')
            ->where('nama', 'LIKE', "%$query%")
            ->orderBy('nama')
            ->limit(6)
            ->get();
            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach($data as $row) {
                    $exp = explode('.', $row->kode);
                    $province = Wilayah::select('nama')->where('kode', '=', $exp[0])->get()->first()->nama;
                    $city = Wilayah::select('nama')->where('kode', '=', $exp[0].'.'.$exp[1])->get()->first()->nama;
                    $output .= '
                    <li><a class="btn btn-sm btn-default col-12" data-kode="'.$row->kode.'" data-nama="'.strtoupper($province).', '.strtoupper($city).', '.strtoupper($row->nama).'" id="add_to_subdistrict_list"><span style="color:black; font-size:15px; text-align:left;">'.strtoupper($province).', '.strtoupper($city).', '.strtoupper($row->nama).'</span></a></li>
                    ';
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }
    
    public function fetchCity(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');
            $type = $request->get('type');
            $data = DB::table('ro_cities')->selectRaw('city_id, city_name')
            ->where('city_name', 'LIKE', "%$query%")
            ->orderBy('city_name')
            ->limit(5)
            ->get();
            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach($data as $row) {
                    $output .= '
                    <li><a class="btn btn-sm btn-primary col-12 text-white" data-city_ro="'.$row->city_id.'" data-nama="'.strtoupper($row->city_name).'" id="add_to_city_list"><span style="color:#fff; font-size:15px; float:left;">'.strtoupper($row->city_name).'</span></a></li>
                    ';
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function checkWaitingForReview()
    {
        $check = UserRating::select('id')->where([
            'st_id' => Auth::user()->st_id,
            'stt_id' => Auth::user()->stt_id,
            'ur_status' => 'WAITING FOR REVIEW'
        ])->get()->first();
        if (!empty($check)) {
          $r['ur_id'] = $check->id;
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkCustomerPhone(Request $request)
    {
        $phone = $request->_cust_phone;
        $check = Customer::select('id', 'cust_name', 'cust_subdistrict')->where('cust_phone', '=', $phone)->get()->first();
        if (!empty($check)) {
          $exp = explode('.', $check->cust_subdistrict);
          $province = Wilayah::select('nama')->where('kode', '=', $exp[0])->get()->first()->nama;
          $city = Wilayah::select('nama')->where('kode', '=', $exp[0].'.'.$exp[1])->get()->first()->nama;
          $subdistrict = Wilayah::select('nama')->where('kode', '=', $check->cust_subdistrict)->get()->first()->nama;
          $cust_subdistrict_label = strtoupper($province.', '.$city.', '.$subdistrict);

          $r['cust_id'] = $check->id;
          $r['cust_name'] = strtoupper($check->cust_name);
          $r['cust_subdistrict'] = $check->cust_subdistrict;
          $r['cust_subdistrict_label'] = $cust_subdistrict_label;
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function storeData(Request $request)
    {
        $ur_id = $request->_ur_id;
        $ur_description = $request->_ur_description;
        $rating = $request->_rating;
        $cust_id = $request->_cust_id;
        $cust_name = $request->_cust_name;
        $cust_phone = $request->_cust_phone;
        $cust_subdistrict = $request->_cust_subdistrict;

        DB::table('customer_ratings')->insertGetId([
            'st_id'             => Auth::user()->st_id,
            'ur_value'          => $rating,
            'ur_description'    => $ur_description
        ]);
//        if (!empty($cust_id)) {
//          $update = Customer::where('id', '=', $cust_id)->update([
//            'cust_name' => $cust_name,
//            'cust_phone' => $cust_phone,
//            'cust_province' => $province,
//            'cust_city' => $city,
//            'cust_subdistrict' => $cust_subdistrict,
//          ]);
//        } else {
//          $cust_id = DB::table('customers')->insertGetId([
//            'ct_id' => '1',
//            'st_id' => Auth::user()->st_id,
//            'stt_id' => Auth::user()->stt_id,
//            'cust_name' => $cust_name,
//            'cust_phone' => $cust_phone,
//            'cust_province' => $province,
//            'cust_city' => $city,
//            'cust_subdistrict' => $cust_subdistrict,
//            'cust_delete' => '0',
//            'created_at' => date('Y-m-d H:i:s')
//          ]);
//        }
//        $update_rating = UserRating::where('id', '=', $ur_id)->update([
//          'cust_id' => $cust_id,
//          'ur_value' => $rating,
//          'ur_description' => $ur_description,
//          'ur_status' => 'WAITING FOR CHECKOUT'
//        ]);
        if (!empty($update_rating)) {
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkWaitingForCheckout()
    {
        $check = UserRating::select('id', 'cust_id')->where([
            'st_id' => Auth::user()->st_id,
            'ur_status' => 'WAITING FOR CHECKOUT'
        ])->get()->first();
        if (!empty($check)) {
          $cust_name = Customer::select('cust_name')->where('id', '=', $check->cust_id)->get()->first()->cust_name;
          $r['ur_id'] = $check->id;
          $r['cust_id'] = $check->cust_id;
          $r['cust_name'] = strtoupper($cust_name);
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }
}
