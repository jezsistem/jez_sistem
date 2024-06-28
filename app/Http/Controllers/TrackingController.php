<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductLocation;
use App\Models\ProductLocationSetup;
use App\Models\ProductLocationSetupTransaction;
use App\Models\ProductStock;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\UserActivity;

class TrackingController extends Controller
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

    protected function UserActivity($u_id, $activity)
    {
        UserActivity::create([
            'user_id' => $u_id,
            'ua_description' => $activity,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function index()
    {
        $this->validateAccess();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'segment' => request()->segment(1),
            'pl_id' => ProductLocation::where('pl_delete', '!=', '1')->orderByDesc('id')->pluck('pl_name', 'id'),
        ];
        return view('auth.scanner.scanner', compact('data'));
    }

    public function checkBarcodeTracking(Request $request)
    {
        $barcode = $request->_barcode;
        $check_barcode = ProductLocationSetup::join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->where('ps_barcode', '=', $barcode)
            ->where('pls_qty', '>', '0')->exists();
        if ($check_barcode) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkSecretCode(Request $request)
    {
        $u_secret_code = $request->_u_secret_code;
        $check = User::where('u_secret_code', '=', $u_secret_code)->exists();
        if ($check) {
            $r['level'] = User::select('g_name')
                ->join('user_groups', 'user_groups.user_id', '=', 'users.id')
                ->join('groups', 'groups.id', '=', 'user_groups.group_id')
                ->where('u_secret_code', '=', $u_secret_code)->get()->first()->g_name;
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function reloadBinByBarcode(Request $request)
    {
        $barcode = $request->_barcode;
        $check_bin_location = ProductLocationSetup::join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->where('product_stocks.ps_barcode', $barcode)->exists();
        if ($check_bin_location) {
            $data = [
                'pl_id' => ProductLocationSetup::select('product_locations.id as pl_id', 'pl_name')->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')->where('product_stocks.ps_barcode', $barcode)->where('pl_delete', '!=', '1')->orderBy('pl_id')->pluck('pl_name', 'pl_id'),
            ];
        } else {
            $data = [
                'pl_id' => null
            ];
        }
        return view('auth.tracking._tracking_bin', compact('data'));
    }

    public function saveInActivity(Request $request)
    {
        $plst_id = $request->_plst_id;
        $pls_id = $request->_pls_id;
        $secret_code = $request->_secret_code;
        $qty = $request->_qty;
        $u_id = Auth::user()->id;

        $status = 'INSTOCK';

        // if ($this->instockApproval() != 1) {

        // } else {
        //     $status = 'INSTOCK APPROVAL';
        // }

        $update_plst = DB::table('product_location_setup_transactions')
            ->where('id', $plst_id)->where('pls_id', $pls_id)
            ->whereIn('plst_status', ['WAITING OFFLINE', 'WAITING ONLINE', 'EXCHANGE', 'REFUND'])->update([
                'u_id_helper' => $u_id,
                'plst_type' => 'IN',
                'plst_status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        if (!empty($update_plst)) {
            $pls = ProductLocationSetup::select('pst_id', 'pls_qty', 'pl_id')->where('id', $pls_id)->get()->first();
            if ($status == 'INSTOCK') {
                $update = DB::table('product_location_setups')->where('id', $pls_id)->update([
                    'pls_qty' => ($pls->pls_qty + $qty),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where('product_stocks.id', $pls->pst_id)
                ->get()->first();
            $pl_code = ProductLocation::select('pl_code')->where('id', $pls->pl_id)->get()->first()->pl_code;
            $this->UserActivity($u_id, 'memasukkan artikel [' . $item->br_name . '] ' . $item->p_name . ' ' . $item->p_color . ' ' . $item->sz_name . ' pada BIN ' . $pl_code);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveOutActivity(Request $request)
    {
        $pls_id = $request->_pls_id;
        $plst_id = $request->_plst_id;
        $secret_code = $request->_secret_code;
        $u_id = Auth::user()->id;
        $update = DB::table('product_location_setup_transactions')->where('id', $plst_id)
            ->whereIn('plst_status', ['WAITING TO TAKE'])->update([
                'u_id_helper' => $u_id,
                'plst_type' => 'OUT',
                'plst_status' => 'WAITING OFFLINE',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        if (!empty($update)) {
            $pls = ProductLocationSetup::select('pst_id', 'pl_id')->where('id', $pls_id)->get()->first();
            $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where('product_stocks.id', $pls->pst_id)
                ->get()->first();
            $pl_code = ProductLocation::select('pl_code')->where('id', $pls->pl_id)->get()->first()->pl_code;
            $this->UserActivity($u_id, 'mengeluarkan artikel [' . $item->br_name . '] ' . $item->p_name . ' ' . $item->p_color . ' ' . $item->sz_name . ' dari BIN ' . $pl_code);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }

        return json_encode($r);
    }

    public function scanSaveOutActivity(Request $request)
    {
        $pls_id = $request->_pls_id;
        $plst_id = $request->_plst_id;
        $secret_code = $request->_secret_code;
        $u_id = Auth::user()->id;
        $update = DB::table('product_location_setup_transactions')->where('id', $plst_id)
            ->whereIn('plst_status', ['WAITING TO TAKE'])->update([
                'u_id_helper' => $u_id,
                'plst_type' => 'OUT',
                'plst_status' => 'WAITING OFFLINE',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        if (!empty($update)) {
            $pls = ProductLocationSetup::select('pst_id', 'pl_id')->where('id', $pls_id)->get()->first();
            $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where('product_stocks.id', $pls->pst_id)
                ->get()->first();
            $pl_code = ProductLocation::select('pl_code')->where('id', $pls->pl_id)->get()->first()->pl_code;
            $this->UserActivity($u_id, 'mengeluarkan artikel [' . $item->br_name . '] ' . $item->p_name . ' ' . $item->p_color . ' ' . $item->sz_name . ' dari BIN ' . $pl_code);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveTrackingActivity(Request $request)
    {
        $ptd_id = $request->_ptd_id;
        $invoice = $request->_invoice;
        $pst_id = $request->_pst_id;
        $pos_td_qty = $request->_pos_td_qty;
        $pl_id = $request->_pl_id;
        $secret_code = $request->_secret_code;
        $pls_id = ProductLocationSetup::select('id')->where('pst_id', $pst_id)->where('pl_id', $pl_id)->get()->first()->id;
        $u_id = Auth::user()->id;
        $pt_id = PosTransaction::select('pos_transactions.id as pt_id')->leftJoin('store_types', 'store_types.id', '=', 'pos_transactions.stt_id')
            ->where('stt_name', '=', 'online')->where('pos_invoice', $invoice)->get()->first()->pt_id;
        $current_qty_ptd = PosTransactionDetail::select('pos_td_qty_pickup')->where('id', $ptd_id)->get()->first()->pos_td_qty_pickup;
        if (!empty($current_qty_ptd)) {
            $current_qty_ptd = $current_qty_ptd;
        } else {
            $current_qty_ptd = 0;
        }
        $update_ptd = PosTransactionDetail::where('id', $ptd_id)->update([
            'pos_td_qty_pickup' => ($current_qty_ptd + $pos_td_qty)
        ]);
        $nameset = PosTransactionDetail::select('pos_td_nameset')->where('id', $ptd_id)->get()->first();
        if ($nameset->pos_td_nameset == '1') {
            $status = 'WAITING FOR NAMESET';
        } else {
            $status = 'WAITING FOR PACKING';
        }
        $update = DB::table('product_location_setup_transactions')->where('pls_id', $pls_id)
            ->where('pt_id', $pt_id)->update([
                'u_id' => $u_id,
                'plst_status' => $status,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        if (!empty($update)) {
            $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where('product_stocks.id', $pst_id)
                ->get()->first();
            $pl_code = ProductLocation::select('pl_code')->where('id', $pl_id)->get()->first()->pl_code;
            $this->UserActivity($u_id, 'mengeluarkan artikel [' . $item->br_name . '] ' . $item->p_name . ' ' . $item->p_color . ' ' . $item->sz_name . ' berdasarkan invoice ' . $invoice . ' dari BIN ' . $pl_code);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    private function instockApproval()
    {
        $r = 0;
        $check = DB::table('instock_exception_approvals')
            ->where('st_id', '=', Auth::user()->st_id)->exists();
        if ($check) {
            $r = 1;
        }
        return $r;
    }

    public function cancelTrackingActivity(Request $request)
    {
        $ptd_id = $request->_ptd_id;
        $invoice = $request->_invoice;
        $pst_id = $request->_pst_id;
        $pos_td_qty = $request->_pos_td_qty;
        $pl_id = $request->_pl_id;

        $pls = ProductLocationSetup::select('id', 'pls_qty')->where('pst_id', $pst_id)->where('pl_id', $pl_id)->get()->first();
        // if ($this->instockApproval() != 1) {
        $pls_update = ProductLocationSetup::where('id', '=', $pls->id)
            ->update([
                'pls_qty' => ($pls->pls_qty + $pos_td_qty)
            ]);
        $status = 'INSTOCK';
        // } else {
        //     $status = 'INSTOCK APPROVAL';
        // }

        $u_id = Auth::user()->id;
        $update_ptd = PosTransactionDetail::where('id', $ptd_id)->update([
            'pos_td_reject' => '1'
        ]);
        $pt_id = PosTransaction::select('pos_transactions.id as pt_id')->where('pos_invoice', $invoice)->get()->first()->pt_id;
        $update = DB::table('product_location_setup_transactions')->where('pls_id', $pls->id)
            ->where('product_location_setup_transactions.plst_status', '=', 'WAITING ONLINE')
            ->where('pt_id', $pt_id)->update([
                'u_id' => $u_id,
                'plst_status' => $status,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        if (!empty($update)) {
            $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where('product_stocks.id', $pst_id)
                ->get()->first();
            $pl_code = ProductLocation::select('pl_code')->where('id', $pl_id)->get()->first()->pl_code;
            $this->UserActivity($u_id, 'MEMBATALKAN artikel [' . $item->br_name . '] ' . $item->p_name . ' ' . $item->p_color . ' ' . $item->sz_name . ' berdasarkan invoice ' . $invoice . ' dari BIN ' . $pl_code);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function savePackingActivity(Request $request)
    {
        $plst_id = $request->_plst_id;
        $secret_code = $request->_secret_code;
        $u_id = Auth::user()->id;
        $update = ProductLocationSetupTransaction::where('id', $plst_id)->update([
            'plst_status' => 'DONE',
            'u_id_packer' => $u_id
        ]);
        if (!empty($update)) {
            $plst = ProductLocationSetupTransaction::select('product_location_setups.pst_id', 'product_location_setups.pl_id')
                ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->where('product_location_setup_transactions.id', $plst_id)
                ->get()->first();
            $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where('product_stocks.id', $plst->pst_id)
                ->get()->first();
            $pl_code = ProductLocation::select('pl_code')->where('id', $plst->pl_id)->get()->first()->pl_code;
            $this->UserActivity($u_id, 'melakukan DONE packing artikel [' . $item->br_name . '] ' . $item->p_name . ' ' . $item->p_color . ' ' . $item->sz_name . ' yang diambil dari BIN ' . $pl_code);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveRejectActivity(Request $request)
    {
        $plst_id = $request->_plst_id;
        $secret_code = $request->_secret_code;
        $u_id = Auth::user()->id;
        $update = ProductLocationSetupTransaction::where('id', $plst_id)->update([
            'plst_status' => 'REJECT',
            'u_id_packer' => $u_id
        ]);
        if (!empty($update)) {
            $plst = ProductLocationSetupTransaction::select('product_location_setups.pst_id', 'product_location_setups.pl_id')
                ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->where('product_location_setup_transactions.id', $plst_id)
                ->get()->first();
            $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where('product_stocks.id', $plst->pst_id)
                ->get()->first();
            $pl_code = ProductLocation::select('pl_code')->where('id', $plst->pl_id)->get()->first()->pl_code;
            $this->UserActivity($u_id, 'melakukan REJECT artikel [' . $item->br_name . '] ' . $item->p_name . ' ' . $item->p_color . ' ' . $item->sz_name . ' yang diambil dari BIN ' . $pl_code);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function outDatatables(Request $request)
    {
        if (request()->ajax()) {
                return datatables()->of(ProductLocationSetupTransaction::select(
                    'product_location_setup_transactions.id as plst_id',
                    'pls_id',
                    'pst_id',
                    'pls_qty',
                    'plst_qty',
                    'plst_status',
                    'pl_id',
                    'u_name',
                    'p_name',
                    'br_name',
                    'p_color',
                    'sz_name',
                    'pl_code',
                    'pl_name',
                    'pl_description',
                    'product_location_setup_transactions.created_at as plst_created',
                    'ps_barcode'
                )
                    ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                    ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    ->leftJoin('users', 'users.id', '=', 'product_location_setup_transactions.u_id')
                    ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                    ->where(function ($w) {
                        $w->whereIn('product_locations.st_id', [Auth::user()->st_id]);
                    })
                    ->where('plst_status', '=', 'WAITING TO TAKE'))
                    ->editColumn('article', function ($data) {
                        $p_name =  $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name;
                        return '
                    <span class="btn btn-sm btn-primary" style="white-space: nowrap; font-weight:bold;">' . $data->plst_status . '</span>
                    <span style="white-space: nowrap; font-weight:bold;">[' . $data->br_name . ']<br/>' . $data->ps_barcode. ' - '. $data->p_name . '<br/>' . $data->p_color . ' (' . $data->sz_name . ')</span><br/>
                    <span style="white-space: nowrap; font-weight:bold;">Jml Bin : '. $data->pls_qty .'</span><br/>
                    <span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm btn-primary">Jml : ' . $data->plst_qty . '</span>
                    <span class="btn btn-sm btn-primary" style="white-space: nowrap; font-weight:bold;">[' . $data->pl_code . ']</span>                    
                    <a class="btn btn-sm btn-success" data-status="pickup" data-plst_id="' . $data->plst_id . '" data-p_name="' . $p_name . '" data-qty="' . $data->pls_qty . '" data-pls_id="' . $data->pls_id . '" id="get_out_btn" style="font-weight:bold;">Keluar</a>';
                    })
                    ->rawColumns(['article', 'bin', 'status', 'qty', 'action'])
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->where(function ($w) use ($request) {
                                $search = $request->get('search');
                                $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
                                // create search by ps_barcode from product_stocks
                                $w->orWhereRaw('ts_product_stocks.ps_barcode LIKE ?', "%$search%");
                            });
                        }
                    })
                    ->addIndexColumn()
                    ->make(true);
        }
    }

    public function scanOutDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(ProductLocationSetupTransaction::select('product_location_setup_transactions.id as plst_id', 'pls_id', 'pst_id', 'pls_qty', 'plst_qty', 'plst_status', 'pl_id', 'u_name', 'p_name', 'br_name', 'p_color', 'sz_name', 'pl_code', 'pl_name', 'pl_description', 'product_location_setup_transactions.created_at as plst_created')
                ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('users', 'users.id', '=', 'product_location_setup_transactions.u_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->where(function ($w) {
                    $w->whereIn('product_locations.st_id', [Auth::user()->st_id]);
                })
                ->where('plst_status', '=', 'WAITING TO TAKE'))
                ->editColumn('article', function ($data) {
                    $p_name = $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name;
                    return '
                <span class="btn btn-sm btn-primary" style="white-space: nowrap; font-weight:bold;">' . $data->plst_status . '</span>
                <span style="white-space: nowrap; font-weight:bold;">[' . $data->br_name . ']<br/>' . $data->p_name . '<br/>' . $data->p_color . ' (' . $data->sz_name . ')</span><br/>
                <span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm btn-primary">Jml : ' . $data->plst_qty . '</span>
                <span class="btn btn-sm btn-primary" style="white-space: nowrap; font-weight:bold;">[' . $data->pl_code . ']</span>
                <a class="btn btn-sm btn-success" data-status="pickup" data-plst_id="' . $data->plst_id . '" data-p_name="' . $p_name . '" data-qty="' . $data->pls_qty . '" data-pls_id="' . $data->pls_id . '" id="scan_get_out_btn" style="font-weight:bold;">Keluar</a>';
                })
                ->rawColumns(['article', 'bin', 'status', 'qty', 'action'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
                            $w->orWhereRaw('ts_product_stocks.ps_barcode LIKE ?', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function inDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(ProductLocationSetupTransaction::select('product_location_setup_transactions.id as plst_id', 'pls_id', 'plst_qty', 'plst_status', 'p_name', 'br_name', 'p_color', 'pl_code', 'pl_name', 'sz_name')
                ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where(function ($w) {
                    $w->whereIn('product_locations.st_id', [Auth::user()->st_id]);
                })
                ->whereIn('plst_status', ['WAITING OFFLINE', 'WAITING ONLINE', 'REJECT', 'EXCHANGE', 'REFUND']))
                ->editColumn('article', function ($data) {
                    $p_name = $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name;
                    if ($data->plst_status == 'WAITING OFFLINE') {
                        $btn = 'btn-warning';
                    } else if ($data->plst_status == 'WAITING ONLINE') {
                        $btn = 'btn-light-warning';
                    } else if ($data->plst_status == 'WAITING FOR CHECKOUT') {
                        $btn = 'btn-info';
                    } else if ($data->plst_status == 'WAITING TO TAKE') {
                        $btn = 'btn-info';
                    } else if ($data->plst_status == 'REJECT') {
                        $btn = 'btn-danger';
                    } else if ($data->plst_status == 'EXCHANGE') {
                        $btn = 'btn-danger';
                    } else if ($data->plst_status == 'REFUND') {
                        $btn = 'btn-danger';
                    }
                    return '<span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm ' . $btn . '">' . $data->plst_status . '</span> <span style="white-space: nowrap; font-weight:bold;"> [' . $data->br_name . ']<br/>' . $data->p_name . '<br/>' . $data->p_color . ' [' . $data->sz_name . ']</span><br/>
                <a class="btn btn-sm btn-primary" style="white-space: nowrap; font-weight:bold;">Jml : ' . $data->plst_qty . '</a>
                <span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm btn-primary">' . $data->pl_code . '</span>
                <a class="btn btn-sm btn-success" data-bin="' . $data->pl_code . ' ' . $data->pl_name . '" data-p_name="' . $p_name . '" data-qty="' . $data->plst_qty . '" data-pls_id="' . $data->pls_id . '" data-plst_id="' . $data->plst_id . '" id="get_in_btn" style="font-weight:bold;">Masuk</a>';
                })
                ->rawColumns(['article', 'status', 'bin', 'qty', 'action'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
                        });
                    }
                    if (!empty($request->get('waiting'))) {
                        $instance->where(function ($w) use ($request) {
                            $w->where('plst_status', '=', $request->get('waiting'));
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function scanInDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(ProductLocationSetupTransaction::select('product_location_setup_transactions.id as plst_id', 'pls_id', 'plst_qty', 'plst_status', 'p_name', 'br_name', 'p_color', 'pl_code', 'pl_name', 'sz_name')
                ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where(function ($w) {
                    $w->whereIn('product_locations.st_id', [Auth::user()->st_id]);
                })
                ->whereIn('plst_status', ['WAITING OFFLINE', 'WAITING ONLINE', 'REJECT', 'EXCHANGE', 'REFUND']))
                ->editColumn('article', function ($data) {
                    $p_name = $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name;
                    if ($data->plst_status == 'WAITING OFFLINE') {
                        $btn = 'btn-warning';
                    } else if ($data->plst_status == 'WAITING ONLINE') {
                        $btn = 'btn-light-warning';
                    } else if ($data->plst_status == 'WAITING FOR CHECKOUT') {
                        $btn = 'btn-info';
                    } else if ($data->plst_status == 'WAITING TO TAKE') {
                        $btn = 'btn-info';
                    } else if ($data->plst_status == 'REJECT') {
                        $btn = 'btn-danger';
                    } else if ($data->plst_status == 'EXCHANGE') {
                        $btn = 'btn-danger';
                    } else if ($data->plst_status == 'REFUND') {
                        $btn = 'btn-danger';
                    }
                    return '<span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm ' . $btn . '">' . $data->plst_status . '</span> <span style="white-space: nowrap; font-weight:bold;"> [' . $data->br_name . ']<br/>' . $data->p_name . '<br/>' . $data->p_color . ' [' . $data->sz_name . ']</span><br/>
                <a class="btn btn-sm btn-primary" style="white-space: nowrap; font-weight:bold;">Jml : ' . $data->plst_qty . '</a>
                <span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm btn-primary">' . $data->pl_code . '</span>
                <a class="btn btn-sm btn-success" data-bin="' . $data->pl_code . ' ' . $data->pl_name . '" data-p_name="' . $p_name . '" data-qty="' . $data->plst_qty . '" data-pls_id="' . $data->pls_id . '" data-plst_id="' . $data->plst_id . '" id="scan_get_in_btn" style="font-weight:bold;">Masuk</a>';
                })
                ->rawColumns(['article', 'status', 'bin', 'qty', 'action'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            //                            $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
                            $w->where('product_stocks.ps_barcode', $search);
                        });
                    }
                    if (!empty($request->get('waiting'))) {
                        $instance->where(function ($w) use ($request) {
                            $w->where('plst_status', '=', $request->get('waiting'));
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }
}
