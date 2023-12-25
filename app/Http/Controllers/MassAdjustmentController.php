<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\ExceptionLocation;
use App\Imports\MassImport;
use App\Exports\MassExport;
use App\Exports\MassResult;
use Maatwebsite\Excel\Facades\Excel;

class MassAdjustmentController extends Controller
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
            'st_id' => DB::table('stores')->where('st_delete', '!=', '1')->orderBy('st_name')->pluck('st_name', 'id'),
            'psc_id' => DB::table('product_sub_categories')->where('psc_delete', '!=', '1')->orderBy('psc_name')->pluck('psc_name', 'id'),
            'br_id' => DB::table('brands')->where('br_delete', '!=', '1')->orderBy('br_name')->pluck('br_name', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.mass_adjustment.mass_adjustment', compact('data'));
    }

    public function stockDatatables(Request $request)
    {
        $exception = ExceptionLocation::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();
        if(request()->ajax()) {
            return datatables()->of(DB::table('product_location_setups')->selectRaw(
                "ts_product_location_setups.id as id, ts_product_stocks.ps_barcode as ps_barcode,pl_code, br_name, p_name, p_color, sz_name, psc_name,
            pls_qty, avg(ts_purchase_order_article_details.poad_purchase_price) as purchase_2, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase_1, ps_sell_price, p_sell_price, ps_purchase_price, p_purchase_price")
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_stocks.id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where(function($w) use ($exception, $request) {
                $st_id = $request->get('st_id');
                $psc_id = $request->get('psc_id');
                $br_id = $request->get('br_id');
                $pl_id = $request->get('pl_id');
                $qty_filter = $request->get('qty_filter');
                $w->whereNotIn('product_locations.pl_code', $exception);
                if ($st_id != 'all') {
                    $w->where('product_locations.st_id', $st_id);
                }
                if ($psc_id != 'all') {
                    $w->where('products.psc_id', $psc_id);
                }
                if ($br_id != 'all') {
                    $w->where('products.br_id', $br_id);
                }
                if (!empty($pl_id)) {
                    $w->whereIn('product_locations.id', $pl_id);
                }
                if ($qty_filter == '1') {
                    $w->where('product_location_setups.pls_qty', '>', '0');
                }
            })
            ->groupBy('product_location_setups.id'))
            ->editColumn('purchase', function($data) {
                if (!empty($data->purchase_1)) {
                    return number_format($data->purchase_1);
                } else if (!empty($data->purchase_2)) {
                    return number_format($data->purchase_2);
                } else if (!empty($data->ps_purchase_price)) {
                    return number_format($data->ps_purchase_price);
                } else {
                    return number_format($data->p_purchase_price);
                }
            })
            ->editColumn('sell', function($data) {
                if (!empty($data->ps_sell_price)) {
                    return number_format($data->ps_sell_price);
                } else {
                    return number_format($data->p_sell_price);
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('pl_code', 'LIKE', "%$search%")
                        ->orWhere('ps_barcode', 'LIKE', "%$search%")
                        ->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function adjustmentDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('mass_adjustments')->select('mass_adjustments.id as id', 'ma_code', 'ma_approve', 'ma_editor', 'ma_executor', 'ma_status', 'st_name', 'u_name', 'mass_adjustments.created_at', 'mass_adjustments.updated_at')
            ->leftJoin('stores', 'stores.id', '=', 'mass_adjustments.st_id')
            ->leftJoin('users', 'users.id', '=', 'mass_adjustments.u_id'))
            ->editColumn('ma_code_show', function ($d) {
                return "<a class='btn btn-primary' id='madj_btn' data-id='".$d->id."'>".$d->ma_code."</a>";
            })
            ->editColumn('approve', function ($d) {
                if (!empty($d->ma_approve)) {
                    return DB::table('users')->where('id', '=', $d->ma_approve)->first()->u_name;
                } else {
                    return 'Menunggu Approval';
                }
            })
            ->editColumn('editor', function ($d) {
                if (!empty($d->ma_editor)) {
                    return DB::table('users')->where('id', '=', $d->ma_editor)->first()->u_name;
                } else {
                    return '-';
                }
            })
            ->editColumn('executor', function ($d) {
                if (!empty($d->ma_executor)) {
                    return DB::table('users')->where('id', '=', $d->ma_executor)->first()->u_name;
                } else {
                    return '-';
                }
            })
            ->editColumn('created_at', function ($d) {
                return date('d/m/Y H:i:s', strtotime($d->created_at));
            })
            ->editColumn('updated_at', function ($d) {
                return date('d/m/Y H:i:s', strtotime($d->updated_at));
            })
            ->editColumn('ma_status', function ($d) {
                if ($d->ma_status == '0') {
                    return 'Menunggu Eksekusi';
                } else {
                    return 'Selesai';
                }
            })
            ->rawColumns(['ma_code_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('ma_code', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function adjustmentDetailDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('mass_adjustment_details')->selectRaw("ts_mass_adjustment_details.id as id, br_name, psc_name, p_name, p_color, sz_name, pl_code, qty_export, qty_so, mad_type, mad_diff,
            avg(ts_purchase_order_article_details.poad_purchase_price) as purchase_2, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase_1, ps_sell_price, p_sell_price, ps_purchase_price, p_purchase_price")
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'mass_adjustment_details.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_stocks.id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where('mass_adjustment_details.ma_id', '=', $request->get('ma_id'))
            ->groupBy('mass_adjustment_details.id'))
            ->editColumn('purchase', function($data) {
                if (!empty($data->purchase_1)) {
                    return number_format($data->purchase_1);
                } else if (!empty($data->purchase_2)) {
                    return number_format($data->purchase_2);
                } else if (!empty($data->ps_purchase_price)) {
                    return number_format($data->ps_purchase_price);
                } else {
                    return number_format($data->p_purchase_price);
                }
            })
            ->editColumn('sell', function($data) {
                if (!empty($data->ps_sell_price)) {
                    return number_format($data->ps_sell_price);
                } else {
                    return number_format($data->p_sell_price);
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('pl_code', 'LIKE', "%$search%")
                        ->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function loadAsset(Request $req)
    {
        $st_id = $req->post('st_id');
        $psc_id = $req->post('psc_id');
        $br_id = $req->post('br_id');
        $pl_id = $req->post('pl_id');
        $qty_filter = $req->post('qty_filter');

        $cc_qty = null;
        $c_qty = null;
        $cc_value = null;
        $c_value = null;

        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();

        $asset_cc = DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, br_name, p_name, p_color, sz_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereNotIn('pl_code', $exception)
            ->where(function($w) use ($st_id, $psc_id, $br_id, $pl_id, $qty_filter) {
                if ($st_id != 'all') {
                    $w->where('product_locations.st_id', '=', $st_id);
                }
                if ($psc_id != 'all') {
                    $w->where('products.psc_id', '=', $psc_id);
                }
                if ($br_id != 'all') {
                    $w->where('products.br_id', '=', $br_id);
                }
                if (!empty($pl_id)) {
                    $w->whereIn('product_locations.id', $pl_id);
                }
                if ($qty_filter == '1') {
                    $w->where('product_location_setups.pls_qty', '>', '0');
                }
            })
//            ->where('product_location_setups.pls_qty', '>', '0')
            ->whereIn('stkt_id', ['1', '3'])
            ->groupBy('product_location_setups.id')
            ->get();
        if (!empty($asset_cc->first())) {
            foreach ($asset_cc as $row) {
                $purchase = 0;
                if (!empty ($row->purchase)) {
                    $purchase = round($row->purchase);
                } else {
                    if (!empty($row->ps_purchase_price)) {
                        $purchase = $row->ps_purchase_price;
                    } else {
                        $purchase = $row->p_purchase_price;
                    }
                }
                $cc_qty += $row->qty;
                $cc_value += ($row->qty * $purchase);
            }
        }

        $asset_c = DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, br_name, p_name, p_color, sz_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereNotIn('pl_code', $exception)
            ->where(function($w) use ($st_id, $psc_id, $br_id, $pl_id, $qty_filter) {
                if ($st_id != 'all') {
                    $w->where('product_locations.st_id', '=', $st_id);
                }
                if ($psc_id != 'all') {
                    $w->where('products.psc_id', '=', $psc_id);
                }
                if ($br_id != 'all') {
                    $w->where('products.br_id', '=', $br_id);
                }
                if (!empty($pl_id)) {
                    $w->whereIn('product_locations.id', $pl_id);
                }
                if ($qty_filter == '1') {
                    $w->where('product_location_setups.pls_qty', '>', '0');
                }
            })
//            ->where('product_location_setups.pls_qty', '>', '0')
            ->where('stkt_id', '=', '2')
            ->groupBy('product_location_setups.id')
            ->get();
        if (!empty($asset_c->first())) {
            foreach ($asset_c as $row) {
                $purchase = 0;
                if (!empty ($row->purchase)) {
                    $purchase = round($row->purchase);
                } else {
                    if (!empty($row->ps_purchase_price)) {
                        $purchase = $row->ps_purchase_price;
                    } else {
                        $purchase = $row->p_purchase_price;
                    }
                }
                $c_qty += $row->qty;
                $c_value += ($row->qty * $purchase);
            }
        }

        $r['status'] = '200';
        $r['cc_qty'] = number_format($cc_qty);
        $r['c_qty'] = number_format($c_qty);
        $r['cc_value'] = number_format($cc_value);
        $r['c_value'] = number_format($c_value);
        return json_encode($r);
    }

    public function loadLocation(Request $req)
    {
        $st_id = $req->post('st_id');
        $pl_id = $req->post('pl_id');
        $data = [
            'pl_id' => DB::table('product_locations')->where('st_id', '=', $st_id)
            ->where('pl_delete', '!=', '1')
            ->where(function($w) use ($pl_id) {
                if (!empty($pl_id)) {
                    $w->whereNotIn('id', $pl_id);
                }
            })->orderBy('pl_code')->pluck('pl_code', 'id')
        ];
        return view('app.mass_adjustment._load_bin', compact('data'));
    }

    public function exportData(Request $req)
    {
        $st_id = $req->get('st_id');
        $psc_id = $req->get('psc_id');
        $br_id = $req->get('br_id');
        $pl_id = $req->get('pl_id');
        $qty_filter = $req->get('qty_filter');
        return Excel::download(new MassExport($st_id, $psc_id, $br_id, $pl_id, $qty_filter), 'mass_adjustment_template.xlsx');
    }

    public function exportResult(Request $req)
    {
        $ma_id = $req->post('ma_id');
        return Excel::download(new MassResult($ma_id), 'mass_adjustment_results.xlsx');
    }

    public function importData(Request $req)
    {
        $st_id = $req->post('st_id');
        $psc_id = $req->post('psc_id');
        $br_id = $req->post('br_id');
        $pl_id = $req->post('pl_id');
        $qty_filter = $req->post('qty_filter');

        if (request()->hasFile('template')) {
            $import = new MassImport($st_id, $psc_id, $br_id, $pl_id, $qty_filter);
            Excel::import($import, request()->file('template'));
            $r['ma_id'] = $import->getRowCount()['ma_id'];
            $r['ma_code'] = $import->getRowCount()['ma_code'];
            $r['status'] = '200';
        } else {
            $r['status'] = '500';
        }
        return json_encode($r);
    }

    public function loadApproval(Request $req)
    {
        $ma_id = $req->post('ma_id');
        $get = DB::table('mass_adjustments')->where('id', '=', $ma_id)->first();
        if (!empty($get)) {
            $approval = '';
            $approval_label = '';
            if (!empty($get->ma_approve)) {
                $approval = $get->ma_approve;
                $approval_label = DB::table('users')->select('u_name')->where('id', '=', $approval)->first()->u_name;

            }
            $r['approval'] = $approval;
            $r['approval_label'] = $approval_label;
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function approvalData(Request $req)
    {
        $ma_id = $req->post('ma_id');
        $update = DB::table('mass_adjustments')->where('id', '=', $ma_id)->update([
            'ma_approve' => Auth::user()->id,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function execData(Request $req)
    {
        $ma_id = $req->post('ma_id');
        $check = DB::table('mass_adjustments')->where('id', '=', $ma_id)
        ->whereNotNull('ma_approve')
        ->where('ma_status', '=', '0')->exists();
        if (!$check) {
            $r['status'] = '400';
            return json_encode($r);
        }
        $get = DB::table('mass_adjustment_details')->select('pls_id', 'qty_so')->where('ma_id', '=', $ma_id)->get();
        if (!empty($get->first())) {
            $update = DB::table('mass_adjustments')->where('id', '=', $ma_id)->update([
                'ma_executor' => Auth::user()->id,
                'ma_status' => '1',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            foreach ($get as $row) {
                DB::table('product_location_setups')->where('id', '=', $row->pls_id)->update([
                    'pls_qty' => $row->qty_so
                ]);
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
