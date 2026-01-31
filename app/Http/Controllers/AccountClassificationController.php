<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\AccountType;
use App\Models\AccountClassification;
use App\Models\UserActivity;

class AccountClassificationController extends Controller
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
            <a href='' class='text-muted'>Klasifikasi Akun</a>
        </li>";
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'path' => $path,
            'user' => $user_data,
            'segment' => request()->segment(1),
            'at_id' => AccountType::where('at_delete', '!=', '1')->orderByDesc('id')->pluck('at_name', 'id'),
        ];
        return view('app.account_classification.account_classification', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(AccountClassification::select('account_classifications.id as acid', 'account_types.id as atid', 'ac_name', 'ac_description', 'at_name')
            ->join('account_types', 'account_types.id', '=', 'account_classifications.at_id')
            ->where('ac_delete', '!=', '1'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('ac_name', 'LIKE', "%$search%")
                        ->orWhere('at_name', 'LIKE', "%$search%")
                        ->orWhere('ac_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $account_classification = new AccountClassification;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'ac_name' => $request->input('ac_name'),
            'at_id' => $request->input('at_id'),
            'ac_description' => $request->input('ac_description'),
            'ac_delete' => '0',
        ];

        $save = $account_classification->storeData($mode, $id, $data);
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('menambah data klasifikasi akun '.strtoupper($request->input('ac_name')));
            } else {
                $this->UserActivity('mengubah data klasifikasi akun '.strtoupper($request->input('ac_name')));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $account_classification = new AccountClassification;
        $id = $request->input('_id');
        $item_name = AccountClassification::select('ac_name')->where('id', $id)->get()->first()->ac_name;
        $save = $account_classification->deleteData($id);
        if ($save) {
            $this->UserActivity('menghapus data klasifikasi akun '.$item_name);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsAccountClassification(Request $request)
    {
        $check = AccountClassification::where(['ac_name' => strtoupper($request->_ac_name)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function indexUpdated()
    {
        $this->validateAccess('klasifikasi_akun');
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'klasifikasi_akun')->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'at_id' => AccountType::where('at_delete', '!=', '1')->orderByDesc('id')->pluck('at_name', 'id'),
        ];
        return view('app.updated_account_classification.account_classification', compact('data'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');

            $query = AccountClassification::select('account_classifications.id as acid', 'account_types.id as atid', 'ac_name', 'ac_description', 'at_name')
                ->join('account_types', 'account_types.id', '=', 'account_classifications.at_id')
                ->where('ac_delete', '!=', '1');

            if (!empty($search)) {
                $query->where(function ($w) use ($search) {
                    $w->where('ac_name', 'LIKE', "%$search%")
                      ->orWhere('at_name', 'LIKE', "%$search%")
                      ->orWhere('ac_description', 'LIKE', "%$search%");
                });
            }

            $total = $query->count();
            $totalPages = ceil($total / $perPage);

            $query->orderBy('account_classifications.id', 'desc');
            $query->skip(($page - 1) * $perPage)->take($perPage);

            $results = $query->get();

            $data = [];
            $no = ($page - 1) * $perPage + 1;
            foreach ($results as $row) {
                $data[] = [
                    'no' => $no++,
                    'acid' => $row->acid,
                    'atid' => $row->atid,
                    'ac_name' => $row->ac_name ?? '-',
                    'at_name' => $row->at_name ?? '-',
                    'ac_description' => $row->ac_description ?? '-',
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
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage(),
                'data' => [],
                'total' => 0,
                'total_pages' => 0,
                'current_page' => 1,
                'per_page' => 25
            ], 500);
        }
    }
}
