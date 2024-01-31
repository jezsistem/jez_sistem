<?php

namespace App\Http\Controllers;

use App\Imports\TargetDetailImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Target;
use App\Models\SubTarget;
use App\Models\SubSubTarget;
use App\Models\Store;
use App\Models\StoreType;
use App\Models\PosTransactionDetail;
use App\Models\UserActivity;
use Maatwebsite\Excel\Facades\Excel;

class TargetController extends Controller
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
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'stt_id' => StoreType::where('stt_delete', '!=', '1')->orderByDesc('id')->pluck('stt_name', 'id'),
        ];
        return view('app.target.target', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Target::select('targets.id as tr_id', 'tr_date'))
            ->editColumn('tr_date_show', function($data){
                $date = date('F Y', strtotime($data->tr_date));
                return $date;
            })
            ->editColumn('tr_amount_show', function($data){
                $amount = SubSubTarget::select('sstr_amount')->where('targets.id', '=', $data->tr_id)
                ->leftJoin('sub_targets', 'sub_targets.id', '=', 'sub_sub_targets.str_id')
                ->leftJoin('targets', 'targets.id', '=', 'sub_targets.tr_id')
                ->sum('sstr_amount');
                return '<span class="btn btn-sm btn-primary">'.number_format($amount).'</span>';
            })
            ->editColumn('tr_amount_get', function($data){
                $get = 0;
                $exp = explode('-', $data->tr_date);
                $month = $exp[1];
                $year = $exp[0];
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET'])
                ->whereMonth('pos_transaction_details.created_at', '=', $month)
                ->whereYear('pos_transaction_details.created_at', '=', $year)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $get += $ptd->pos_td_marketplace_price;
                        } else {
                            $get += $ptd->pos_td_discount_price;
                        }
                    }
                }
                return '<span class="btn btn-sm btn-success">'.number_format($get).'</span>';
            })
            ->editColumn('tr_progress', function($data){
                return '<span class="btn btn-sm btn-success" style="white-space: nowrap;">%</span>';
            })
            ->editColumn('tr_total_sale', function($data){
                $exp = explode('-', $data->tr_date);
                $month = $exp[1];
                $year = $exp[0];

                $total_sale = DB::table('pos_transactions')
                
                ->whereMonth('created_at', '=', $month)
                ->whereYear('created_at', '=', $year)
                ->count('id');

                $total_item = DB::table('pos_transaction_details')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                
                ->whereMonth('pos_transaction_details.created_at', '=', $month)
                ->whereYear('pos_transaction_details.created_at', '=', $year)
                ->count('pos_transaction_details.id');
                $date = date('m-Y', strtotime($data->tr_date));
                return '<span class="btn btn-sm btn-primary" style="white-space: nowrap;" data-tr_id="'.$data->tr_id.'" data-target_date="'.$date.'" id="sub_target_detail">['.$total_sale.'] ['.$total_item.']</span>';
            })
            ->rawColumns(['tr_date_show', 'tr_amount_show', 'tr_amount_get', 'tr_progress', 'tr_total_sale'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('tr_date', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getDetailDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(SubTarget::select('sub_targets.id as str_id', 'tr_id', 'st_name', 'stt_id', 'st_id', 'stt_name', 'tr_date')
            ->leftJoin('targets', 'targets.id', '=', 'sub_targets.tr_id')
            ->leftJoin('stores', 'stores.id', '=', 'sub_targets.st_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'sub_targets.stt_id')
            ->where('targets.id', '=', $request->tr_id))
            ->editColumn('st_name', function($data){
                return '<span style="white-space:nowrap;">'.$data->st_name.'</span>';
            })
            ->editColumn('tr_amount_show', function($data){
                $amount = SubSubTarget::select('sstr_amount')->where('sub_targets.id', '=', $data->str_id)
                ->leftJoin('sub_targets', 'sub_targets.id', '=', 'sub_sub_targets.str_id')
                ->sum('sstr_amount');
                return '<span class="btn btn-sm btn-primary" data-id="'.$data->str_id.'" id="tr_amount_btn">'.number_format($amount).'</span>';
            })
            ->editColumn('tr_amount_get', function($data){
                $get = 0;
                $exp = explode('-', $data->tr_date);
                $month = $exp[1];
                $year = $exp[0];
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET'])
                ->whereMonth('pos_transaction_details.created_at', '=', $month)
                ->whereYear('pos_transaction_details.created_at', '=', $year)
                ->where('pos_transactions.stt_id', '=', $data->stt_id)
                ->where('pos_transactions.st_id', '=', $data->st_id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $get += $ptd->pos_td_marketplace_price;
                        } else {
                            $get += $ptd->pos_td_discount_price;
                        }
                    }
                }
                return '<span class="btn btn-sm btn-success">'.number_format($get).'</span>';
            })
            ->editColumn('tr_progress', function($data){
                return '<span class="btn btn-sm btn-success" style="white-space: nowrap;">%</span>';
            })
            ->editColumn('tr_total_sale', function($data){
                $exp = explode('-', $data->tr_date);
                $month = $exp[1];
                $year = $exp[0];

                $total_sale = DB::table('pos_transactions')
                ->where('pos_transactions.stt_id', '=', $data->stt_id)
                ->where('pos_transactions.st_id', '=', $data->st_id)
                
                ->whereMonth('created_at', '=', $month)
                ->whereYear('created_at', '=', $year)
                ->count('id');

                $total_item = DB::table('pos_transaction_details')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                
                ->whereMonth('pos_transaction_details.created_at', '=', $month)
                ->whereYear('pos_transaction_details.created_at', '=', $year)
                ->where('pos_transactions.stt_id', '=', $data->stt_id)
                ->where('pos_transactions.st_id', '=', $data->st_id)
                ->count('pos_transaction_details.id');
                $date = date('m-Y', strtotime($data->tr_date));
                return '<span class="btn btn-sm btn-primary" style="white-space: nowrap;" data-tr_id="'.$data->tr_id.'" data-target_date="'.$date.'" id="sub_target_detail">['.$total_sale.'] ['.$total_item.']</span>';
            })
            ->editColumn('action', function($data){
                return '<span class="btn btn-sm btn-danger" style="white-space: nowrap;" data-str_id="'.$data->str_id.'" id="delete_sub_target">Hapus</span>';
            })
            ->rawColumns(['st_name', 'tr_date_show', 'tr_amount_show', 'tr_amount_get', 'tr_progress', 'tr_total_sale', 'action'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('tr_date', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function saveTargetDetail(Request $request)
    {
        $tr_id = $request->_tr_id;
        $st_id = $request->_st_id;
        $stt_id = $request->_stt_id;
        $arr = $request->_arr;
        $str_id = DB::table('sub_targets')->insertGetId([
          'tr_id' => $tr_id,
          'st_id' => $st_id,
          'stt_id' => $stt_id,
          'created_at' => date('Y-m-d H:i:s')
        ]);
        $insert = array();
        foreach ($arr as $row) {
          $insert[] = [
              'str_id' => $str_id,
              'sstr_date' => $row[0],
              'sstr_amount' => $row[1],
              'created_at' => date('Y-m-d H:i:s')
          ];
        }
        $save = DB::table('sub_sub_targets')->insert($insert);
        if (!empty($save)) {
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteSubTarget(Request $request)
    {
        $str_id = $request->_str_id;
        $delete = SubSubTarget::where('str_id', '=', $str_id)->delete();
        if(SubTarget::where('id', '=', $str_id)->delete()) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode ($r);
    }

    public function storeData(Request $request)
    {
        $target = new Target;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'tr_date' => $request->input('tr_date'),
        ];

        $save = $target->storeData($mode, $id, $data);
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('menambah data target '.strtoupper(date('m-Y', strtotime($request->input('tr_date')))).' '.strtoupper($request->input('tr_amount')));
            } else {
                $this->UserActivity('mengubah data target '.strtoupper(date('m-Y', strtotime($request->input('tr_date')))).' '.strtoupper($request->input('tr_amount')));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $target = new Target;
        $id = $request->input('_id');
        $item_name = Target::select('tr_date', 'tr_amount')->where('id', $id)->get()->first();
        $save = $target->deleteData($id);
        if ($save) {
            $this->UserActivity('menghapus data target '.date('m-Y', strtotime($item_name->tr_date)).' '.$item_name->tr_amount);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsTarget(Request $request)
    {
        $check = Target::where(['tr_date' => $request->_tr_date])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkStr(Request $request)
    {
        $str_id = $request->input('_str_id');
        $check = SubSubTarget::where('str_id', '=', $str_id)->get();
        if (!empty($check)) {
          $r['status'] = '200';
          $r['sstr'] = json_decode($check);
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function editTarget(Request $request)
    {
        $sstr_id = $request->input('_sstr_id');
        $value = $request->input('_value');
        $update = SubSubTarget::where('id', '=', $sstr_id)->update([
          'sstr_amount' => $value
        ]);
        if (!empty($update)) {
          $this->UserActivity('mengubah data target menjadi '.$value);
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveTargetDetailImport(Request $request)
    {
        try {
            if ($request->hasFile('importFile')) {

                $file = $request->file('importFile');
                // membuat nama file unik
                $nama_file = rand() . $file->getClientOriginalName();

                // upload ke folder file_siswa di dalam folder public
                $file->move('excel', $nama_file);

                $tr_id = $request->_tr_id;
                $st_id = $request->_st_id;
                $stt_id = $request->_stt_id;
                $import = new TargetDetailImport($tr_id, $st_id, $stt_id);
                Excel::import($import, public_path('/excel/' . $nama_file));

                $r['status'] = '200';
                unlink(public_path('/excel/' . $nama_file));
                } else {
                $r['status'] = '400';
            }

        return json_encode($r);
        } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
    }

//    public function saveTargetDetail(Request $request)
//    {
//        $tr_id = $request->_tr_id;
//        $st_id = $request->_st_id;
//        $stt_id = $request->_stt_id;
//        $arr = $request->_arr;
//        $str_id = DB::table('sub_targets')->insertGetId([
//            'tr_id' => $tr_id,
//            'st_id' => $st_id,
//            'stt_id' => $stt_id,
//            'created_at' => date('Y-m-d H:i:s')
//        ]);
//        $insert = array();
//        foreach ($arr as $row) {
//            $insert[] = [
//                'str_id' => $str_id,
//                'sstr_date' => $row[0],
//                'sstr_amount' => $row[1],
//                'created_at' => date('Y-m-d H:i:s')
//            ];
//        }
//        $save = DB::table('sub_sub_targets')->insert($insert);
//        if (!empty($save)) {
//            $r['status'] = '200';
//        } else {
//            $r['status'] = '400';
//        }
//        return json_encode($r);
//    }

}
