<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\StoreType;
use App\Models\Account;

class PaymentMethodController extends Controller
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
            'stt_id' => StoreType::where('stt_delete', '!=', '1')->orderByDesc('id')->pluck('stt_name', 'id'),
            'a_id' => Account::selectRaw('id, CONCAT(a_name," (",a_code,")") as account_name')
            ->where('a_delete', '!=', '1')
            ->orderBy('a_code')->pluck('account_name', 'id'),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id')
        ];
        return view('app.payment_method.payment_method', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PaymentMethod::select('payment_methods.id as pm_id', 'store_types.id as stt_id', 'accounts.id as a_id', 'stores.st_name as st_name','pm_name', 'pm_description', 'stt_name', 'a_name', 'a_code')
            ->join('store_types', 'store_types.id', '=', 'payment_methods.stt_id')
            ->join('accounts', 'accounts.id', '=', 'payment_methods.a_id')
            ->leftJoin('stores', 'stores.id', '=', 'payment_methods.st_id')
            ->where('pm_delete', '!=', '1'))
            ->editColumn('a_name', function($data){ 
                return '['.$data->a_code.'] '.$data->a_name;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('pm_name', 'LIKE', "%$search%")
                        ->orWhere('stt_name', 'LIKE', "%$search%")
                        ->orWhere('a_name', 'LIKE', "%$search%")
                        ->orWhere('pm_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $payment_method = new PaymentMethod;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'pm_name' => strtoupper($request->input('pm_name')),
            'st_id' => $request->input('st_id'),
            'stt_id' => $request->input('stt_id'),
            'a_id' => $request->input('a_id'),
            'pm_description' => $request->input('pm_description'),
            'pm_delete' => '0',
        ];

        $save = $payment_method->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $payment_method = new PaymentMethod;
        $id = $request->input('_id');
        $save = $payment_method->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsPm(Request $request)
    {
        $check = PaymentMethod::where(['pm_name' => strtoupper($request->_pm_name), 'stt_id' => $request->_stt_id])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
