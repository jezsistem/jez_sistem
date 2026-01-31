<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\StockType;
use App\Models\Account;

class StockTypeController extends Controller
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
            'a_id' => Account::selectRaw('id, CONCAT(a_name," (",a_code,")") as account_name')
            ->where('a_delete', '!=', '1')
            ->orderBy('a_code')->pluck('account_name', 'id'),
        ];
        return view('app.stock_type.stock_type', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(StockType::select('stock_types.id as stktid', 'accounts.id as a_id', 'stkt_name', 'stkt_description', 'a_name', 'a_code')
            ->join('accounts', 'accounts.id', '=', 'stock_types.a_id')
            ->where('stkt_delete', '!=', '1'))
            ->editColumn('a_name', function($data){ 
                return '['.$data->a_code.'] '.$data->a_name;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('a_name', 'LIKE', "%$search%")
                        ->orWhere('stkt_name', 'LIKE', "%$search%")
                        ->orWhere('stkt_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $stock_type = new StockType;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'a_id' => $request->input('a_id'),
            'stkt_name' => $request->input('stkt_name'),
            'stkt_description' => $request->input('stkt_description'),
            'stkt_delete' => '0',
        ];

        $save = $stock_type->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $stock_type = new StockType;
        $id = $request->input('_id');
        $save = $stock_type->deleteData($id);
        if ($save) {
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

    public function checkExistsStockType(Request $request)
    {
        $check = StockType::where(['stkt_name' => strtoupper($request->_stkt_name)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function indexUpdated()
    {
        $this->validateAccess('tipe_stok');
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'tipe_stok')->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'a_id' => Account::selectRaw('id, CONCAT(a_name," (",a_code,")") as account_name')
            ->where('a_delete', '!=', '1')
            ->orderBy('a_code')->pluck('account_name', 'id'),
        ];
        return view('app.updated_stock_type.stock_type', compact('data'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');

            $query = StockType::select('stock_types.id as stktid', 'accounts.id as a_id', 'stkt_name', 'stkt_description', 'a_name', 'a_code')
                ->join('accounts', 'accounts.id', '=', 'stock_types.a_id')
                ->where('stkt_delete', '!=', '1');

            if (!empty($search)) {
                $query->where(function ($w) use ($search) {
                    $w->where('a_name', 'LIKE', "%$search%")
                      ->orWhere('stkt_name', 'LIKE', "%$search%")
                      ->orWhere('stkt_description', 'LIKE', "%$search%");
                });
            }

            $total = $query->count();
            $totalPages = ceil($total / $perPage);

            $query->orderBy('stock_types.id', 'desc');
            $query->skip(($page - 1) * $perPage)->take($perPage);

            $results = $query->get();

            $data = [];
            $no = ($page - 1) * $perPage + 1;
            foreach ($results as $row) {
                $data[] = [
                    'no' => $no++,
                    'stktid' => $row->stktid,
                    'a_id' => $row->a_id,
                    'stkt_name' => $row->stkt_name ?? '-',
                    'a_name' => '['.$row->a_code.'] '.$row->a_name,
                    'stkt_description' => $row->stkt_description ?? '-',
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
