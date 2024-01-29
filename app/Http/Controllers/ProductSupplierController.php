<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductSupplier;
use App\Imports\ProductSupplierImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductSupplierController extends Controller
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
        return view('app.product_supplier.product_supplier', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(ProductSupplier::select(
                'id', 'ps_name', 'ps_email', 'ps_pkp', 'ps_phone', 'ps_address', 'ps_rekening', 'ps_npwp','ps_description')
            ->where('ps_delete', '!=', '1'))
            ->editColumn('ps_pkp_show', function($data){ 
                if ($data->ps_pkp == '1') {
                    return 'Ya';
                } else {
                    return '-';
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('ps_name', 'LIKE', "%$search%")
                        ->orWhere('ps_email', 'LIKE', "%$search%")
                        ->orWhere('ps_phone', 'LIKE', "%$search%")
                        ->orWhere('ps_rekening', 'LIKE', "%$search%")
                        ->orWhere('ps_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $product_supplier = new ProductSupplier;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'ps_name' => strtoupper(ltrim($request->input('ps_name'))),
            'ps_pkp' => $request->input('ps_pkp'),
            'ps_due_day' => $request->input('ps_due_day'),
            'ps_email' => $request->input('ps_email'),
            'ps_phone' => $request->input('ps_phone'),
            'ps_address' => $request->input('ps_address'),
            'ps_description' => $request->input('ps_description'),
            'ps_npwp' => $request->input('ps_npwp'),
            'ps_rekening' => $request->input('ps_rekening'),
            'ps_delete' => '0',
        ];

        $save = $product_supplier->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $product_supplier = new ProductSupplier;
        $id = $request->input('_id');
        $save = $product_supplier->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function importData()
    {
        if (request()->hasFile('ps_template')) {
            Excel::import(new ProductSupplierImport, request()->file('ps_template')); 
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsSupplier(Request $request)
    {
        $check = ProductSupplier::where(['ps_name' => strtoupper($request->_ps_name)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
