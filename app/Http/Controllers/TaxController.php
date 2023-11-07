<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Tax;
use App\Models\Account;

class TaxController extends Controller
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
            'a_id' => Account::selectRaw('id, CONCAT(a_name," (",a_code,")") as account_name')
            ->where('a_delete', '!=', '1')
            ->orderBy('a_code')->pluck('account_name', 'id'),
        ];
        return view('app.tax.tax', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Tax::select('taxes.id as tid', 'tx_code', 'tx_name', 'tx_npwp', 'tx_non_npwp', 'a_id_purchase', 'a_id_sell')
            ->where('tx_delete', '!=', '1'))
            ->editColumn('tx_name', function($data){ 
                return '<span style="white-space: nowrap;">'.$data->tx_name.'</span>';
            })
            ->editColumn('a_id_purchase_name', function($data){ 
                $account = Account::where(['id' => $data->a_id_purchase])->get();
                if (!empty($account)) {
                    return '['.$account->first()->a_code.'] '.$account->first()->a_name;
                } else {
                    return '-';
                }
            })
            ->editColumn('a_id_sell_name', function($data){ 
                $account = Account::where(['id' => $data->a_id_sell])->get();
                if (!empty($account)) {
                    return '['.$account->first()->a_code.'] '.$account->first()->a_name;
                } else {
                    return '-';
                }
            })
            ->rawColumns(['tx_name'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('a_name', 'LIKE', "%$search%")
                        ->orWhere('tx_code', 'LIKE', "%$search%")
                        ->orWhere('tx_name', 'LIKE', "%$search%")
                        ->orWhere('tx_npwp', 'LIKE', "%$search%")
                        ->orWhere('tx_non_npwp', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $tax = new Tax;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'a_id_purchase' => $request->input('a_id_purchase'),
            'a_id_sell' => $request->input('a_id_sell'),
            'tx_code' => $request->input('tx_code'),
            'tx_name' => $request->input('tx_name'),
            'tx_npwp' => $request->input('tx_npwp'),
            'tx_non_npwp' => $request->input('tx_non_npwp'),
            'tx_delete' => '0',
        ];

        $save = $tax->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $tax = new Tax;
        $id = $request->input('_id');
        $save = $tax->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsTax(Request $request)
    {
        $check = Tax::where(['tx_code' => strtoupper($request->_tx_code)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
