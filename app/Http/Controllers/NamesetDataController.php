<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use Illuminate\Support\Facades\DB;

class NameSetDataController extends Controller
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
        return view('app.nameset_data.nameset_data', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PosTransactionDetail::select('pos_transaction_details.id as ptd_id', 'p_name', 'br_name', 'sz_name', 'p_color', 'pos_invoice', 'stt_name', 'pos_transaction_details.created_at as pos_created')
            ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'pos_transactions.stt_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where('pos_td_nameset', '=', '1')
            ->where('pos_td_nameset_price', '!=', null))
            ->editColumn('pos_invoice', function($data){
                return '<span class="btn btn-sm btn-primary">'.$data->pos_invoice.'</span>';
            })
            ->editColumn('stt_name', function($data){
                if (strtolower($data->stt_name) == 'offline') {
                    return '<span class="btn btn-sm btn-warning" style="white-space: nowrap;">'.$data->stt_name.'</span>';
                } else {
                    return '<span class="btn btn-sm btn-light-warning" style="white-space: nowrap;">'.$data->stt_name.'</span>';
                }
            })
            ->editColumn('article', function($data){
                return '<span class="btn btn-sm btn-primary" style="white-space: nowrap;">['.$data->br_name.'] '.$data->p_name.' '.$data->p_color.' '.$data->sz_name.'</span>';
            })
            ->editColumn('pos_created', function($data){
                return '<span style="white-space: nowrap;">'.$data->pos_created.'</span>';
            })
            ->editColumn('action', function($data){
                return '<span data-ptd_id="'.$data->ptd_id.'" class="btn btn-sm btn-success" id="nameset_finish_btn">Selesai</span>';
            })
            ->rawColumns(['pos_invoice', 'stt_name', 'article', 'action', 'pos_created'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('pos_invoice', 'LIKE', "%$search%")
                        ->orWhereRaw('CONCAT(p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function updateData(Request $request)
    {
        $ptd_id = $request->_ptd_id;
        $type = $request->_type;

        if ($type == 'finish_nameset') {
            $get_ptd = DB::table('pos_transaction_details')
            ->select('pt_id', 'pst_id', 'pl_id')
            ->where('id', '=', $ptd_id)->get()->first();
            $pls_id = DB::table('product_location_setups')
            ->select('id')
            ->where('pst_id', '=', $get_ptd->pst_id)
            ->where('pl_id', '=', $get_ptd->pl_id)->get()->first()->id;
            $update = DB::table('pos_transaction_details')->where('id', '=', $ptd_id)->update([
                'pos_td_nameset' => '0'
            ]);
            if(!empty($update)) {
                $division = DB::table('pos_transaction_details')->select('stt_name')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('store_types', 'store_types.id', '=', 'pos_transactions.stt_id')
                ->where('pos_transaction_details.id', '=', $ptd_id)
                ->get()->first()->stt_name;
                if (strtoupper($division) == 'ONLINE') {
                    if (Auth::user()->st_id == '2') {
                        $status = 'WAITING FOR PACKING';
                    } else {
                        $status = 'SHIPPING NUMBER';
                    }
                    $pos_status = 'SHIPPING NUMBER';
                } else {
                    $status = 'DONE';
                    $pos_status = 'DONE';
                }
                $update_plst = DB::table('product_location_setup_transactions')
                ->where('pls_id', '=', $pls_id)
                ->where('pt_id', '=', $get_ptd->pt_id)->update([
                    'plst_status' => $status
                ]);
                $check_nameset = DB::table('pos_transactions')
                ->join('pos_transaction_details', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
                ->where('pos_transactions.id', '=', $get_ptd->pt_id)
                ->where('pos_transaction_details.pos_td_nameset', '=', '1')->exists();
                if (!$check_nameset) {
                    $update_invoice = DB::table('pos_transactions')
                    ->where('id', '=', $get_ptd->pt_id)->update([
                        'pos_status' => $pos_status
                    ]);
                }
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        }
        return json_encode($r);
    }
}
