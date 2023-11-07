<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Account;
use App\Models\AccountClassification;
use App\Models\UserActivity;

class AccountController extends Controller
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
        $path = "
        <li class='breadcrumb-item'>
            <a href='' class='text-muted'>Accounting</a>
        </li>
        <li class='breadcrumb-item'>
            <a href='' class='text-muted'>Data Akun</a>
        </li>";
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'path' => $path,
            'user' => $user_data,
            'segment' => request()->segment(1),
            'ac_id' => AccountClassification::where('ac_delete', '!=', '1')->orderByDesc('id')->pluck('ac_name', 'id'),
        ];
        return view('app.account.account', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Account::select('accounts.id as aid', 'account_classifications.id as acid', 'a_code', 'a_name', 'a_description', 'at_name', 'ac_name')
            ->join('account_classifications', 'account_classifications.id', '=', 'accounts.ac_id')
            ->join('account_types', 'account_types.id', '=', 'account_classifications.at_id')
            ->where('a_delete', '!=', '1')
            ->orderBy('a_code'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('a_code', 'LIKE', "%$search%")
                        ->orWhere('a_name', 'LIKE', "%$search%")
                        ->orWhere('at_name', 'LIKE', "%$search%")
                        ->orWhere('ac_name', 'LIKE', "%$search%")
                        ->orWhere('a_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $account = new Account;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'a_code' => $request->input('a_code'),
            'a_name' => $request->input('a_name'),
            'ac_id' => $request->input('ac_id'),
            'a_description' => $request->input('a_description'),
            'a_delete' => '0',
        ];

        $save = $account->storeData($mode, $id, $data);
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('menambah data akun '.strtoupper($request->input('a_code')).' '.strtoupper($request->input('a_name')));
            } else {
                $this->UserActivity('mengubah data akun '.strtoupper($request->input('a_code')).' '.strtoupper($request->input('a_name')));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $account = new Account;
        $id = $request->input('_id');
        $item_name = Account::select('a_code', 'a_name')->where('id', $id)->get()->first();
        $save = $account->deleteData($id);
        if ($save) {
            $this->UserActivity('menghapus data akun '.$item_name->a_code.' '.$item_name->a_name);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsAccount(Request $request)
    {
        $check = Account::where(['a_name' => strtoupper($request->_a_name)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsAccountCode(Request $request)
    {
        $check = Account::where(['a_code' => strtoupper($request->_a_code)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
