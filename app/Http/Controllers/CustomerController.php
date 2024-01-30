<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExport;
use App\Models\CustomerTraffic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\StoreTypeDivision;
use App\Models\UserActivity;
use App\Models\Wilayah;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use Hash;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
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
    
    protected function UserActivity($activity)
    {
        UserActivity::create([
            'user_id' => Auth::user()->id,
            'ua_description' => $activity,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    protected function getLabel($table, $field, $id)
    {
        $label = DB::table($table)->select($field)->where('id', '=', $id)->get()->first();
        if (!empty($label)) {
            return $label->$field;
        } else {
            return '[field not found]';
        }
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
            'ct_id' => CustomerType::where('ct_delete', '!=', '1')->orderByDesc('id')->pluck('ct_name', 'id'),
            'cust_province' => DB::table('wilayah')->select('kode', 'nama')->whereRaw('length(kode) = 2')->orderBy('nama')->pluck('nama', 'kode'),
        ];
        return view('app.customer.customer', compact('data'));
    }

    public function reloadCity(Request $request)
    {
        $province = $request->_province;
        $data = [
            'cust_city' => DB::table('wilayah')->select('kode', 'nama')->whereRaw("length(kode) = 5 AND substring(kode, 1, 2) = $province")->orderBy('nama')->pluck('nama', 'kode'),
        ];
        return view('app.customer._reload_city', compact('data'));
    }

    public function reloadSubdistrict(Request $request)
    {
        $city = $request->_city;
        $data = [
            'cust_subdistrict' => DB::table('wilayah')->select('kode', 'nama')->whereRaw("length(kode) = 8")->whereRaw("substring(kode, 1, 5) = $city")->orderBy('nama')->pluck('nama', 'kode'),
        ];
        return view('app.customer._reload_subdistrict', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        $date = $request->post('date');
        $date_filter = $request->post('date_filter');
        $start = null;
        $end = null;
        $exp = explode('|', $date);
        $total = count($exp);
        if ($total > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            if (!empty($date)) {
                $start = $date;
            } else {
                $start = date('Y-m-d');
            }
        }

        if(request()->ajax()) {
            return datatables()->of(Customer::selectRaw("ts_customers.id as cid, ct_name, ct_id,
             cust_name, cust_store, pos_status, cust_province, cust_city, cust_username, cust_subdistrict, cust_phone, cust_email, cust_address,
             ts_customers.created_at as cust_created, count(ts_pos_transactions.id) as cust_shopping")
            ->leftJoin('pos_transactions', 'pos_transactions.cust_id', '=', 'customers.id')
            ->leftJoin('customer_types', 'customer_types.id', '=', 'customers.ct_id')
            ->where('cust_delete', '!=', '1')
            ->where(function($w) use ($date_filter, $start, $end) {
                if ($date_filter == '1') {
                    if (!empty($end)) {
                        $w->whereDate('customers.created_at', '>=', $start)
                        ->whereDate('customers.created_at', '<=', $end);
                    } else {
                        $w->whereDate('customers.created_at', '=', $start);
                    }
                }
            })
            ->groupBy('customers.id'))
            ->editColumn('cust_shopping_show', function($data){
                return '<span class="btn btn-sm btn-primary" data-cust_id="'.$data->cid.'" id="cust_shopping">'.$data->cust_shopping.'</span>';
            })
            ->editColumn('cust_created', function($data){
                return date('d/m/Y H:i:s', strtotime($data->cust_created));
            })
            ->editColumn('cust_address_show', function($data){
                if (!empty($data->cust_province) AND !empty($data->cust_city) AND !empty($data->cust_subdistrict)) {
                    $province = DB::table('wilayah')->select('nama')->where('kode', $data->cust_province)->get()->first()->nama;
                    $city = DB::table('wilayah')->select('nama')->where('kode', $data->cust_city)->get()->first()->nama;
                    $subdistrict = DB::table('wilayah')->select('nama')->where('kode', $data->cust_subdistrict)->get()->first()->nama;
                    return $data->cust_address.', '.$subdistrict.', '.$city.', '.$province;
                } else {
                    return $data->cust_address;
                }
            })
            ->rawColumns(['cust_shopping_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('cust_name', 'LIKE', "%$search%")
                        ->orWhere('ct_name', 'LIKE', "%$search%")
                        ->orWhere('cust_store', 'LIKE', "%$search%")
                        ->orWhere('cust_phone', 'LIKE', "%$search%")
                        ->orWhere('cust_email', 'LIKE', "%$search%")
                        ->orWhere('cust_address', 'LIKE', "%$search%");
                    });
                }
                if (!empty($request->get('stt_filter'))) {
                   $division = $request->get('stt_filter');
                   if ($division == 'offline') {
                     $instance->where('customers.stt_id', '=', '2');
                   } else {
                     $instance->where('customers.stt_id', '!=', '2');
                   }
                }
                if (!empty($request->get('cust_type_filter'))) {
                    $instance->where('customers.ct_id', '=', $request->get('cust_type_filter'));
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getDetailDatatables(Request $request)
    {
        $date = $request->post('date');
        $date_filter = $request->post('date_filter');
        $start = null;
        $end = null;
        $exp = explode('|', $date);
        $total = count($exp);
        if ($total > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            if (!empty($date)) {
                $start = $date;
            } else {
                $start = date('Y-m-d');
            }
        }

        if(request()->ajax()) {
            return datatables()->of(Customer::selectRaw("ts_customers.id as cid, ct_name, pos_status, ct_id, cust_name, cust_store, cust_province, cust_city, cust_subdistrict, cust_phone, cust_email, cust_address, ts_customers.created_at as cust_created, count(ts_pos_transactions.id) as cust_shopping")
            ->leftJoin('pos_transactions', 'pos_transactions.cust_id', '=', 'customers.id')
            ->leftJoin('customer_types', 'customer_types.id', '=', 'customers.ct_id')
            ->where('cust_delete', '!=', '1')
            ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
            ->where(function($w) use ($request){
                if ($request->code_type == 'province') {
                    $w->where('cust_province', '=', $request->code);
                } else if ($request->code_type == 'city') {
                    $w->where('cust_city', '=', $request->code);
                } else {
                    $w->where('cust_subdistrict', '=', $request->code);
                }
            })
            ->where(function($w) use ($date_filter, $start, $end) {
                if ($date_filter == '1') {
                    if (!empty($end)) {
                        $w->whereDate('customers.created_at', '>=', $start)
                        ->whereDate('customers.created_at', '<=', $end);
                    } else {
                        $w->whereDate('customers.created_at', '=', $start);
                    }
                }
            })
            ->groupBy('customers.id'))
            ->editColumn('cust_shopping_show', function($data){
                return '<span class="btn btn-sm btn-primary" data-cust_id="'.$data->cid.'" id="cust_shopping">'.$data->cust_shopping.'</span>';
            })
            ->editColumn('cust_created', function($data){
                return date('d/m/Y H:i:s', strtotime($data->cust_created));
            })
            ->editColumn('cust_address_show', function($data){
                if (!empty($data->cust_province) AND !empty($data->cust_city) AND !empty($data->cust_subdistrict)) {
                    $province = DB::table('wilayah')->select('nama')->where('kode', $data->cust_province)->get()->first()->nama;
                    $city = DB::table('wilayah')->select('nama')->where('kode', $data->cust_city)->get()->first()->nama;
                    $subdistrict = DB::table('wilayah')->select('nama')->where('kode', $data->cust_subdistrict)->get()->first()->nama;
                    return $data->cust_address.', '.$subdistrict.', '.$city.', '.$province;
                } else {
                    return $data->cust_address;
                }
            })
            ->rawColumns(['cust_shopping_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('stt_filter'))) {
                   $division = $request->get('stt_filter');
                   if ($division == 'offline') {
                     $instance->where('customers.stt_id', '=', '2');
                   } else {
                     $instance->where('customers.stt_id', '!=', '2');
                   }
                }
                if (!empty($request->get('cust_type_filter'))) {
                    $instance->where('customers.ct_id', '=', $request->get('cust_type_filter'));
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function customerTransactionDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PosTransaction::select('pos_transactions.id as pt_id', 'pos_transactions.created_at as pos_created', 'pos_invoice')
            ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
            ->where('pos_transactions.cust_id', '=', $request->cust_id)
            ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']))
            ->editColumn('pos_invoice', function($data){
                return '<span class="btn btn-sm btn-primary" data-pt_id="'.$data->pt_id.'" id="sales_item_detail_btn">'.$data->pos_invoice.'</span>';
            })
            ->editColumn('pos_created', function($data){
                return date('d/m/Y H:i:s', strtotime($data->pos_created));
            })
            ->editColumn('qty', function($data){
                $total_item = PosTransactionDetail::select('pos_td_qty')
                ->where('pt_id', '=', $data->pt_id)->sum('pos_td_qty');
                return $total_item;
            })
            ->editColumn('total', function($data){
                $total = 0;
                $ptd = PosTransactionDetail::select('pos_td_discount_price', 'pos_td_marketplace_price', 'pos_td_total_price')
                ->where('pt_id', '=', $data->pt_id)
                ->get();
                foreach ($ptd as $row) {
                    if (!empty($row->pos_td_marketplace_price)) {
                        $total += $row->pos_td_marketplace_price;
                    } else if (!empty($row->pos_td_discount_price)) {
                        $total += $row->pos_td_discount_price;
                    } else {
                        $total += $row->pos_td_total_price;
                    }
                }
                return number_format($total);
            })
            ->rawColumns(['pos_invoice'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('stt_filter'))) {
                   $division = $request->get('stt_filter');
                   if ($division == 'offline') {
                     $instance->where('customers.stt_id', '=', '2');
                   } else {
                     $instance->where('customers.stt_id', '!=', '2');
                   }
                }
                if (!empty($request->get('cust_type_filter'))) {
                    $instance->where('customers.ct_id', '=', $request->get('cust_type_filter'));
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function provinceDatatables(Request $request)
    {
        $date = $request->post('date');
        $date_filter = $request->post('date_filter');
        $start = null;
        $end = null;
        $exp = explode('|', $date);
        $total = count($exp);
        if ($total > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            if (!empty($date)) {
                $start = $date;
            } else {
                $start = date('Y-m-d');
            }
        }

        if(request()->ajax()) {
            return datatables()->of(Wilayah::select('kode', 'nama', DB::raw("count(ts_customers.id) as customer"))
            ->leftJoin('customers', 'customers.cust_province', '=', 'wilayah.kode')
            ->whereRaw("length(kode) = 2")
            ->where(function($w) use ($date_filter, $start, $end) {
                if ($date_filter == '1') {
                    if (!empty($end)) {
                        $w->whereDate('customers.created_at', '>=', $start)
                        ->whereDate('customers.created_at', '<=', $end);
                    } else {
                        $w->whereDate('customers.created_at', '=', $start);
                    }
                }
            })
            ->groupBy('wilayah.kode'))
            ->editColumn('nama_show', function($data) {
                return '<span class="btn btn-sm btn-primary" data-kode="'.$data->kode.'" id="province_detail_btn">'.$data->nama.'</span>';
            })
            ->editColumn('customer_show', function($data) {
                return '<span class="btn btn-sm btn-primary" data-kode="'.$data->kode.'" data-type="province" id="customer_detail_btn">'.$data->customer.'</span>';
            })
            ->rawColumns(['nama_show', 'customer_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('stt_filter'))) {
                   $division = $request->get('stt_filter');
                   if ($division == 'offline') {
                     $instance->where('customers.stt_id', '=', '2');
                   } else {
                     $instance->where('customers.stt_id', '!=', '2');
                   }
                }
                if (!empty($request->get('cust_type_filter'))) {
                    $instance->where('customers.ct_id', '=', $request->get('cust_type_filter'));
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function cityDatatables(Request $request)
    {
        $date = $request->post('date');
        $date_filter = $request->post('date_filter');
        $start = null;
        $end = null;
        $exp = explode('|', $date);
        $total = count($exp);
        if ($total > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            if (!empty($date)) {
                $start = $date;
            } else {
                $start = date('Y-m-d');
            }
        }

        if(request()->ajax()) {
            return datatables()->of(Wilayah::select('kode', 'nama', DB::raw("count(ts_customers.id) as customer"))
            ->leftJoin('customers', 'customers.cust_city', '=', 'wilayah.kode')
            ->whereRaw("length(kode) = 5")
            ->whereRaw("substring(kode, 1, 2) = $request->province_code")
            ->where(function($w) use ($date_filter, $start, $end) {
                if ($date_filter == '1') {
                    if (!empty($end)) {
                        $w->whereDate('customers.created_at', '>=', $start)
                        ->whereDate('customers.created_at', '<=', $end);
                    } else {
                        $w->whereDate('customers.created_at', '=', $start);
                    }
                }
            })
            ->groupBy('wilayah.kode'))
            ->editColumn('nama_show', function($data) {
                return '<span class="btn btn-sm btn-primary" data-kode="'.$data->kode.'" id="city_detail_btn">'.$data->nama.'</span>';
            })
            ->editColumn('customer_show', function($data) {
                return '<span class="btn btn-sm btn-primary" data-kode="'.$data->kode.'" data-type="city" id="customer_detail_btn">'.$data->customer.'</span>';
            })
            ->rawColumns(['nama_show', 'customer_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('stt_filter'))) {
                   $division = $request->get('stt_filter');
                   if ($division == 'offline') {
                     $instance->where('customers.stt_id', '=', '2');
                   } else {
                     $instance->where('customers.stt_id', '!=', '2');
                   }
                }
                if (!empty($request->get('cust_type_filter'))) {
                    $instance->where('customers.ct_id', '=', $request->get('cust_type_filter'));
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function cityRankDatatables(Request $request)
    {
        $date = $request->post('date');
        $date_filter = $request->post('date_filter');
        $start = null;
        $end = null;
        $exp = explode('|', $date);
        $total = count($exp);
        if ($total > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            if (!empty($date)) {
                $start = $date;
            } else {
                $start = date('Y-m-d');
            }
        }

        if(request()->ajax()) {
            return datatables()->of(Wilayah::select('kode', 'nama', DB::raw("count(ts_customers.id) as customer"))
            ->leftJoin('customers', 'customers.cust_city', '=', 'wilayah.kode')
            ->whereRaw("length(kode) = 5")
            ->where(function($w) use ($date_filter, $start, $end) {
                if ($date_filter == '1') {
                    if (!empty($end)) {
                        $w->whereDate('customers.created_at', '>=', $start)
                        ->whereDate('customers.created_at', '<=', $end);
                    } else {
                        $w->whereDate('customers.created_at', '=', $start);
                    }
                }
            })
            ->groupBy('wilayah.kode'))
            ->editColumn('nama_show', function($data) {
                return '<span class="btn btn-sm btn-primary" data-kode="'.$data->kode.'" id="city_detail_btn">'.$data->nama.'</span>';
            })
            ->editColumn('customer_show', function($data) {
                return '<span class="btn btn-sm btn-primary" data-kode="'.$data->kode.'" data-type="city" id="customer_detail_btn">'.$data->customer.'</span>';
            })
            ->rawColumns(['nama_show', 'customer_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('stt_filter'))) {
                   $division = $request->get('stt_filter');
                   if ($division == 'offline') {
                     $instance->where('customers.stt_id', '=', '2');
                   } else {
                     $instance->where('customers.stt_id', '!=', '2');
                   }
                }
                if (!empty($request->get('cust_type_filter'))) {
                    $instance->where('customers.ct_id', '=', $request->get('cust_type_filter'));
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function subdistrictDatatables(Request $request)
    {
        $date = $request->post('date');
        $date_filter = $request->post('date_filter');
        $start = null;
        $end = null;
        $exp = explode('|', $date);
        $total = count($exp);
        if ($total > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            if (!empty($date)) {
                $start = $date;
            } else {
                $start = date('Y-m-d');
            }
        }

        if(request()->ajax()) {
            return datatables()->of(Wilayah::select('kode', 'nama', DB::raw("count(ts_customers.id) as customer"))
            ->leftJoin('customers', 'customers.cust_subdistrict', '=', 'wilayah.kode')
            ->whereRaw("length(kode) = 8")
            ->where(function($w) use ($date_filter, $start, $end) {
                if ($date_filter == '1') {
                    if (!empty($end)) {
                        $w->whereDate('customers.created_at', '>=', $start)
                        ->whereDate('customers.created_at', '<=', $end);
                    } else {
                        $w->whereDate('customers.created_at', '=', $start);
                    }
                }
            })
            ->whereRaw("substring(kode, 1, 5) = $request->city_code")
            ->groupBy('wilayah.kode'))
            ->editColumn('nama_show', function($data) {
                return '<span class="btn btn-sm btn-primary" data-kode="'.$data->kode.'">'.$data->nama.'</span>';
            })
            ->editColumn('customer_show', function($data) {
                return '<span class="btn btn-sm btn-primary" data-kode="'.$data->kode.'" data-type="subdistrict" id="customer_detail_btn">'.$data->customer.'</span>';
            })
            ->rawColumns(['nama_show', 'customer_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('stt_filter'))) {
                   $division = $request->get('stt_filter');
                   if ($division == 'offline') {
                     $instance->where('customers.stt_id', '=', '2');
                   } else {
                     $instance->where('customers.stt_id', '!=', '2');
                   }
                }
                if (!empty($request->get('cust_type_filter'))) {
                    $instance->where('customers.ct_id', '=', $request->get('cust_type_filter'));
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $customer = new Customer;
        $mode = $request->input('_mode');
        $id = $request->input('_id');
        if (Auth::user()->st_id == '1') {
          $stt_id = '1';
        } else {
          $stt_id = Auth::user()->stt_id;
        }
        $data = [
            'ct_id' => $request->input('ct_id'),
            'st_id' => Auth::user()->st_id,
            'stt_id' => $stt_id,
            'cust_name' => $request->input('cust_name'),
            'cust_store' => $request->input('cust_store'),
            'cust_phone' => $request->input('cust_phone'),
            'cust_username' => $request->input('cust_username'),
            'cust_email' => $request->input('cust_email'),
            'cust_province' => $request->input('cust_province'),
            'cust_city' => $request->input('cust_city'),
            'cust_subdistrict' => $request->input('cust_subdistrict'),
            'cust_address' => $request->input('cust_address'),
            'cust_delete' => '0',
        ];
        if (!empty($request->input('password'))) {
            $data = array_merge($data, ['password' => Hash::make($request->input('password'))]);
        }

        $save = $customer->storeData($mode, $id, $data);
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('menambah data customer '.strtoupper($request->input('cust_name')).' '.$request->input('cust_phone'));
            } else {
                $this->UserActivity('mengubah data customer '.strtoupper($request->input('cust_name')).' '.$request->input('cust_phone'));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $customer = new Customer;
        $id = $request->input('_id');
        $item_name = Customer::select('cust_name', 'cust_phone')->where('id', $id)->get()->first();
        $save = $customer->deleteData($id);
        if ($save) {
            $this->UserActivity('menghapus data customer '.$item_name->cust_name.' '.$item_name->cust_phone);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsCustomer(Request $request)
    {
        $check = Customer::where(['cust_phone' => strtoupper($request->_cust_phone)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkCustomer(Request $request)
    {
        $check = Customer::where(['id' => $request->_cust_id])->exists();
        if ($check) {
            $cust = Customer::where(['id' => $request->_cust_id])->get()->first();
            $r['ct_id'] = $cust->ct_id;
            $r['cust_name'] = $cust->cust_name;
            $r['cust_store'] = $cust->cust_store;
            $r['cust_phone'] = $cust->cust_phone;
            $r['cust_email'] = $cust->cust_email;
            $r['cust_province'] = $cust->cust_province;
            $r['cust_city'] = $cust->cust_city;
            $r['cust_subdistrict'] = $cust->cust_subdistrict;
            $r['cust_address'] = $cust->cust_address;
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function reloadCustomer()
    {
        $data = [
            'cust_id' => Customer::selectRaw('id, CONCAT(cust_name," (",cust_phone,")") as name')
            ->where('cust_delete', '!=', '1')
            ->orderBy('cust_name')->pluck('name', 'id'),
		];
        return view('app.pos._reload_customer', compact('data'));
    }

    public function reloadCustomerByDivision(Request $request)
    {
        $std_id = $request->_std_id;
        $division = StoreTypeDivision::select('dv_name')->where('id', $std_id)->get()->first()->dv_name;
        if (strtoupper($division) == 'RESELLER') {
            $data = [
                'type' => 'RESELLER',
                'cust_id' => Customer::selectRaw('ts_customers.id as cid, CONCAT(cust_name," (",cust_phone,") (",ct_name,")") as name')
                ->leftJoin('customer_types', 'customer_types.id', '=', 'customers.ct_id')
                ->where('cust_delete', '!=', '1')
                ->where('ct_name', 'RESELLER')
                ->orderBy('cust_name')->pluck('name', 'cid'),
            ];
        } else if (strtoupper($division) == 'DROPSHIPPER') {
            $data = [
                'type' => 'DROPSHIPPER',
                'cust_id' => Customer::selectRaw('ts_customers.id as cid, CONCAT(cust_name," (",cust_phone,") (",ct_name,")") as name')
                ->leftJoin('customer_types', 'customer_types.id', '=', 'customers.ct_id')
                ->where('cust_delete', '!=', '1')
                ->where('ct_name', 'DROPSHIPPER')
                ->orderBy('cust_name')->pluck('name', 'cid'),
                'sub_cust_id' => Customer::selectRaw('ts_customers.id as cid, CONCAT(cust_name," (",cust_phone,") (",ct_name,")") as name')
                ->leftJoin('customer_types', 'customer_types.id', '=', 'customers.ct_id')
                ->where('cust_delete', '!=', '1')
                ->where('ct_name', '!=', 'DROPSHIPPER')
                ->where('ct_name', '!=', 'RESELLER')
                ->orderBy('cust_name')->pluck('name', 'cid'),
            ];
        } else {
            $check = Customer::selectRaw('ts_customers.id as cid, CONCAT(cust_name," (",cust_phone,") (",ct_name,")") as name')
            ->leftJoin('customer_types', 'customer_types.id', '=', 'customers.ct_id')
            ->where('cust_delete', '!=', '1')
            ->where('ct_name', '!=', 'RESELLER');
            if (!empty($check)) {
                $data = [
                    'type' => '',
                    'cust_id' => Customer::selectRaw('ts_customers.id as cid, CONCAT(cust_name," (",cust_phone,") (",ct_name,")") as name')
                    ->leftJoin('customer_types', 'customer_types.id', '=', 'customers.ct_id')
                    ->where('cust_delete', '!=', '1')
                    ->where('ct_name', '!=', 'RESELLER')
                    ->orderBy('cust_name')->pluck('name', 'cid'),
                ];
            } else {
                $data = [
                    'type' => '',
                    'cust_id' => null
                ];
            }
        }
        return view('app.pos._reload_customer_by_division', compact('data'));
    }

    public function reloadSubCustomerByDivision(Request $request)
    {
        $data = [
            'cust_id' => Customer::selectRaw('ts_customers.id as cid, CONCAT(cust_name," (",cust_phone,") (",ct_name,")") as name')
            ->leftJoin('customer_types', 'customer_types.id', '=', 'customers.ct_id')
            ->where('cust_delete', '!=', '1')
            ->orderBy('cust_name')->pluck('name', 'cid'),
        ];
        return view('app.pos._reload_sub_customer_by_division', compact('data'));
    }

    function fetchCustomer(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');
            $data = Customer::select('customers.id as cust_id', 'cust_name', 'cust_phone', 'ct_name')
                    ->leftJoin('customer_types', 'customer_types.id', '=', 'customers.ct_id')
                    ->whereRaw('CONCAT(cust_name," ", ct_name) LIKE ?', "%$query%")
                    ->orWhereRaw('CONCAT(cust_phone) LIKE ?', "%$query%")
                    ->limit(10)
                    ->get();
            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach($data as $row) {
                    $cust_name = strtoupper($row->cust_name).' '.strtoupper($row->cust_phone).' '.strtoupper($row->ct_name);
                    if ($request->get('type') == 'cust') {
                      $output .= '
                      <li><a class="btn btn-sm btn-inventory col-12" data-id="'.$row->cust_id.'" id="add_to_item_list_cust">'.$cust_name.'</a></li>
                      ';
                    } else {
                      $output .= '
                      <li><a class="btn btn-sm btn-inventory col-12" data-id="'.$row->cust_id.'" id="add_to_item_list_sub_cust">'.$cust_name.'</a></li>
                      ';
                    }
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function loadGraph(Request $request)
    {
        $stt_label = $request->post('stt_label');
        $stt_id = null;
        if ($stt_label == 'online') {
            $stt_id = '1';
        } else if ($stt_label == 'offline') {
            $stt_id = '2';
        }
        $ct_id = $request->post('ct_id');
        $date = $request->post('date');
        $start = null;
        $end = null;
        $exp = explode('|', $date);
        $total = count($exp);
        if ($total > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            if (!empty($date)) {
                $start = $date;
            } else {
                $start = date('Y-m-d');
            }
        }

        $province_item = DB::table('customers')->selectRaw("nama, count(ts_customers.id) as total")
        ->leftJoin('wilayah', 'wilayah.kode', '=', 'customers.cust_province')
        ->where(function($w) use ($stt_id, $ct_id, $start, $end) {
            if (!empty($stt_id)) {
                $w->where('customers.stt_id', '=', $stt_id);
            }
            if (!empty($ct_id)) {
                $w->where('customers.ct_id', '=', $ct_id);
            }
            if (!empty($end)) {
                $w->whereDate('customers.created_at', '>=', $start)
                ->whereDate('customers.created_at', '<=', $end);
            } else {
                $w->whereDate('customers.created_at', '=', $start);
            }
        })
        ->orderBy('wilayah.nama')
        ->groupBy('wilayah.nama')
        ->get();

        $date_item = DB::table('customers')->select("id",
        DB::raw("(count(id)) as total"),
        DB::raw("(DATE_FORMAT(created_at, '%d-%m-%Y')) as date"))
        ->where(function($w) use ($stt_id, $ct_id, $start, $end) {
            if (!empty($stt_id)) {
                $w->where('customers.stt_id', '=', $stt_id);
            }
            if (!empty($ct_id)) {
                $w->where('customers.ct_id', '=', $ct_id);
            }
            if (!empty($end)) {
                $w->whereDate('customers.created_at', '>=', $start)
                ->whereDate('customers.created_at', '<=', $end);
            } else {
                $w->whereDate('customers.created_at', '=', $start);
            }
        })
        ->orderBy('created_at')
        ->groupBy(DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))
        ->get();

        $data = [
            'province_item' => $province_item,
            'date_item' => $date_item,
        ];
        return view('app.customer._load_graph', compact('data'));
    }

    public function storeTraffic()
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
            'ct_id' => CustomerType::where('ct_delete', '!=', '1')->orderByDesc('id')->pluck('ct_name', 'id'),
            'cust_province' => DB::table('wilayah')->select('kode', 'nama')->whereRaw('length(kode) = 2')->orderBy('nama')->pluck('nama', 'kode'),
        ];

        $counts = CustomerTraffic::select('type', DB::raw('count(*) as total'))
            ->whereDate('created_at', Carbon::today())
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();
        $countsTotal = CustomerTraffic::select(DB::raw('count(*) as total'))
            ->whereDate('created_at', Carbon::today())
            ->get();

        return view('app.customer.customer_traffic',[
            'data' => $data,
            'male' => $counts['male'] ?? 0,
            'female' => $counts['female'] ?? 0,
            'child' => $counts['child'] ?? 0,
            'countsTotal' => $countsTotal[0]->total ?? 0,
        ]);
    }

    public function updateTrafficCustomer(Request $request)
    {
        $type = $request->input('gender');

        CustomerTraffic::create([
           'type' => $type
        ]);

        // count grouping by type and count total from today
        $counts = CustomerTraffic::select('type', DB::raw('count(*) as total'))
            ->whereDate('created_at', Carbon::today())
            ->groupBy('type')
            ->get();

        $countsTotal = CustomerTraffic::select(DB::raw('count(*) as total'))
            ->whereDate('created_at', Carbon::today())
            ->get();
        return response()->json([
            'counts' => $counts,
            'countsTotal' => $countsTotal
        ]);
    }

    public function exportData(Request $request)
    {
        try {
           return Excel::download(new CustomerExport(), 'customer.xlsx');
        }catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
