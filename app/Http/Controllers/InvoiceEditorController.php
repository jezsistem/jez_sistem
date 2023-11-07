<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\BuyOneGetOne;

class InvoiceEditorController extends Controller
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

    protected function getLabel($table, $field, $id)
    {
        $label = DB::table($table)->select($field)->where('id', '=', $id)->get()->first();
        if (!empty($label)) {
            return $label->$field;
        } else {
            return '[field not found]';
        }
    }

    private function checkAccess() {
        $r = 0;
        $check = DB::table('invoice_editor_permissions')
        ->where('u_id', '=', Auth::user()->id)
        ->exists();
        if ($check) {
            $r = 1;
        }
        return $r;
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
        if ($user_data->g_name != 'administrator') {
            if ($this->checkAccess() != 1) {
                dd("Anda tidak memiliki akses ke fitur ini");
            }
        }
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'st_id' => DB::table('stores')->where('st_delete', '!=', '1')
            ->orderBy('st_name')->pluck('st_name', 'id'),
            'stt_id' => DB::table('store_types')->where('stt_delete', '!=', '1')
            ->orderBy('stt_name')->pluck('stt_name', 'id'),
            'u_id' => DB::table('users')->where('u_delete', '!=', '1')
            ->selectRaw("CONCAT(u_name,' [',st_name,']') as u_name, ts_users.id as id")
            ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
            ->orderBy('u_name')->pluck('u_name', 'id'),
        ];
        return view('app.invoice_editor.invoice_editor', compact('data'));
    }

    public function getPermissionDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('invoice_editor_permissions')
            ->select('invoice_editor_permissions.id', 'invoice_editor_permissions.st_id', 'invoice_editor_permissions.stt_id', 'u_id', 'st_name', 'stt_name', 'u_name')
            ->leftJoin('stores', 'stores.id', '=', 'invoice_editor_permissions.st_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'invoice_editor_permissions.stt_id')
            ->leftJoin('users', 'users.id', '=', 'invoice_editor_permissions.u_id'))
            ->editColumn('action', function($d) {
                return "<a class='btn btn-sm btn-danger' data-id='".$d->id."' id='delete_btn'><i class='fa fa-trash'></i></a>";
            })
            ->rawColumns(['action'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('st_name', 'LIKE', "%$search%")
                        ->orWhere('u_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getInvoiceDatatables(Request $request)
    {
        $pt_id = $request->get('pt_id');
        if(request()->ajax()) {
            return datatables()->of(DB::table('pos_transactions')
            ->select('pos_transactions.id', 'pos_invoice', 'u_id', 'stt_id', 'std_id', 'pm_id', 'pos_admin_cost', 'pos_real_price', 'pos_status', 'created_at')
            ->where(function($w) use ($pt_id) {
                if (!empty($pt_id)) {
                    $w->where('pos_transactions.id', '=', $pt_id);
                } else {
                    $w->where('pos_transactions.id', '=', '!@@#$%');
                }
            }))
            ->editColumn('cashier', function($d) {
                $cash = '';
                $user = DB::table('users')->select('id', 'u_name')
                ->where('u_delete', '!=', '1')->where('stt_id', '=', Auth::user()->stt_id)->get();
                $cash .= "<select data-pt_id='".$d->id."' id='cashier'>";
                if (!empty($user->first())) {
                    foreach ($user as $row) {
                        if ($d->u_id == $row->id) {
                            $cash .= "<option value='".$row->id."' selected>".$row->u_name."</option>";
                        } else {
                            $cash .= "<option value='".$row->id."'>".$row->u_name."</option>";
                        }
                    }
                }
                $cash .= "</select>";
                return $cash;
            })
            ->editColumn('division', function($d) {
                $div = '';
                $div .= "<select data-pt_id='".$d->id."' id='division'>";
                if ($d->stt_id == '1') {
                    $div .= "<option value='1' selected>ONLINE</option>";
                    $div .= "<option value='2'>OFFLINE</option>";
                } else if ($d->stt_id == '2') {
                    $div .= "<option value='2' selected>OFFLINE</option>";
                    $div .= "<option value='1'>ONLINE</option>";
                }
                $div .= "</select>";
                return $div;
            })
            ->editColumn('subdivision', function($d) {
                $subdiv = '';
                $sd = DB::table('store_type_divisions')->select('id', 'dv_name')
                ->where('dv_delete', '!=', '1')->get();
                $subdiv .= "<select data-pt_id='".$d->id."' id='subdivision'>";
                if (!empty($sd->first())) {
                    foreach ($sd as $row){
                        if ($row->id == $d->std_id) {
                            $subdiv .= "<option value='".$row->id."' selected>".$row->dv_name."</option>";
                        } else {
                            $subdiv .= "<option value='".$row->id."'>".$row->dv_name."</option>";
                        }
                    }
                }
                $subdiv .= "</select>";
                return $subdiv;
            })
            ->editColumn('method', function($d) {
                $method = '';
                $mtd = DB::table('payment_methods')->select('id', 'pm_name')
                ->where('pm_delete', '!=', '1')
                ->get();
                $method .= "<select data-pt_id='".$d->id."' id='method'>";
                if (!empty($mtd->first())) {
                    foreach ($mtd as $row) {
                        if ($d->pm_id == $row->id) {
                            $method .= "<option value='".$row->id."' selected>".$row->pm_name."</option>";
                        } else {
                            $method .= "<option value='".$row->id."'>".$row->pm_name."</option>";
                        }
                    }
                }
                $method .= "</select>";
                return $method;
            })
            ->editColumn('admin', function($d) {
                return "<input type'number' data-pt_id='".$d->id."' id='admin' value='".$d->pos_admin_cost."'/>";
            })
            ->editColumn('created_at', function($d) {
                return "<input type='text' value='".$d->created_at."' data-pt_id='".$d->id."' id='date'/>";
            })
            ->editColumn('action', function($d) {
                return "<a class='btn btn-sm btn-danger' data-pt_id='".$d->id."' id='cancel_btn'>Batalkan</a>";
            })
            ->rawColumns(['cashier', 'division', 'subdivision', 'method', 'admin', 'created_at', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getDetailDatatables(Request $request)
    {
        $pt_id = $request->get('pt_id');
        if(request()->ajax()) {
            return datatables()->of(DB::table('pos_transaction_details')
            ->selectRaw("ts_pos_transaction_details.id as id, CONCAT(br_name,' ',p_name,' ',p_color,' ',sz_name) as article,
            pt_id, pos_td_qty, pst_id, pl_id, pos_td_discount_price, pos_td_marketplace_price, pos_td_nameset_price, pos_td_total_price")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where(function($w) use ($pt_id) {
                if (!empty($pt_id)) {
                    $w->where('pos_transaction_details.pt_id', '=', $pt_id);
                } else {
                    $w->where('pos_transaction_details.pt_id', '=', '!@@#$%');
                }
            }))
            ->editColumn('pos_td_qty', function($d) {
                return $d->pos_td_qty;
            })
            ->editColumn('price', function($d) {
                $price = 0;
                if (!empty($d->pos_td_marketplace_price)) {
                    $price = $d->pos_td_marketplace_price;
                } else {
                    $price = $d->pos_td_discount_price;
                }
                return "<input type='number' data-pt_id='".$d->pt_id."' data-qty='".$d->pos_td_qty."' data-ptd_id='".$d->id."' data-nameset='".$d->pos_td_nameset_price."' value='".$price."' id='price'/>";
            })
            ->editColumn('nameset', function($d) {
                $price = 0;
                if (!empty($d->pos_td_marketplace_price)) {
                    $price = $d->pos_td_marketplace_price;
                } else {
                    $price = $d->pos_td_discount_price;
                }
                return "<input type='number' data-pt_id='".$d->pt_id."' data-qty='".$d->pos_td_qty."' data-ptd_id='".$d->id."' data-price='".$price."' value='".$d->pos_td_nameset_price."' id='nameset'/>";
            })
            ->editColumn('action', function($d) use ($pt_id) {
                $status = null;
                if (!empty($pt_id)) {
                    $status = DB::table('pos_transactions')->select('pos_status')->where('id', '=', $pt_id)->first()->pos_status;
                }
                if ($status != 'WAITING FOR CONFIRMATION') {
                    return "<a class='btn btn-sm btn-danger' data-pst_id='".$d->pst_id."' data-pl_id='".$d->pl_id."' data-pt_id='".$d->pt_id."' data-ptd_id='".$d->id."' id='cancel_item_btn'>Batalkan</a>";
                }
            })
            ->rawColumns(['price', 'nameset', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getTrackingDatatables(Request $request)
    {
        $pt_id = $request->get('pt_id');
        if(request()->ajax()) {
            return datatables()->of(DB::table('product_location_setup_transactions')
            ->selectRaw("ts_product_location_setup_transactions.id as id, CONCAT(br_name,' ',p_name,' ',p_color,' ',sz_name) as article,
            pl_code, plst_qty, plst_status")
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where(function($w) use ($pt_id) {
                if (!empty($pt_id)) {
                    $w->where('product_location_setup_transactions.pt_id', '=', $pt_id);
                } else {
                    $w->where('product_location_setup_transactions.pt_id', '=', '!@@#$%');
                }
            }))
            ->editColumn('status', function($d) {
                $status = ['DONE', 'REFUND', 'EXCHANGE', 'WAITING FOR PACKING', 'WAITING OFFLINE', 'WAITING ONLINE', 'COMPLAINT'];
                $stts = '';
                $stts .= "<select data-id='".$d->id."' id='status'>";
                for ($i = 0; $i < count($status); $i ++) {
                    if ($d->plst_status == $status[$i]) {
                        $stts .= "<option value='".$status[$i]."' selected>".$status[$i]."</option>";
                    } else {
                        $stts .= "<option value='".$status[$i]."'>".$status[$i]."</option>";
                    }
                }
                $stts .= "</select>";
                return $stts;
            })
            ->rawColumns(['status'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function cancelInvoice(Request $request) {
        $auto_instock = BuyOneGetOne::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'buy_one_get_ones.pl_id')->get()->toArray();
        array_push($auto_instock, ['pl_code' => 'TOKO']);
        array_push($auto_instock, ['pl_code' => 'WGN1']);

        $pt_id = $request->post('pt_id');
        $stt_id = DB::table('pos_transactions')->select('stt_id')
        ->where('id', '=', $pt_id)->first()->stt_id;

        if ($stt_id == '1') {
            $status = 'WAITING ONLINE';
        } else {
            $status = 'WAITING OFFLINE';
        }

        $cek_ref = DB::table('pos_transactions')->where('pt_id_ref', '=', $pt_id)->first();
        if (!empty($cek_ref)) {
            $r['invoice'] = $cek_ref->pos_invoice;
            $r['status'] = 400;
            return json_encode($r);
        }

        $statusx = DB::table('pos_transactions')->select('pos_status')->where('id', '=', $pt_id)->first()->pos_status;
        if ($statusx == 'WAITING FOR CONFIRMATION') {
            $delete = DB::table('product_location_setup_transactions')->where('pt_id', '=', $pt_id)->delete();
            $delete = DB::table('pos_transaction_details')->where('pt_id', '=', $pt_id)->delete();
            $delete = DB::table('invoice_editors')->where('pt_id', '=', $pt_id)->delete();
            $delete = DB::table('pos_transactions')->where('id', '=', $pt_id)->delete();
            $r['status'] = 200;
            return json_encode($r);
        }

        $get = DB::table('pos_transactions')->select('id', 'pt_id_ref')->where([
            'id' => $pt_id 
        ])->first();
        if (!empty($get->pt_id_ref)) {
            $update = DB::table('pos_transactions')
            ->where('id', '=', $get->pt_id_ref)->update([
                'pos_refund' => '0',
                'pos_status' => 'DONE'
            ]);
            $delete = DB::table('product_location_setup_transactions')
            ->where('pt_id', '=', $get->pt_id_ref)
            ->whereIn('plst_status', ['REFUND', 'EXCHANGE'])
            ->delete();

            $instock = DB::table('product_location_setup_transactions')
            ->where('pt_id', '=', $get->pt_id_ref)
            ->where('plst_status', '=', 'INSTOCK')->get();
            if (!empty($instock->first())) {
                foreach ($instock as $row) {
                    $pls = DB::table('product_location_setups')->select('pls_qty')->where('id', '=', $row->pls_id)->first();
                    $update = DB::table('product_location_setups')->where('id', '=', $row->pls_id)
                    ->update([
                        'pls_qty' => ($pls->pls_qty-$row->plst_qty)
                    ]);
                }
                $delete = DB::table('product_location_setup_transactions')
                ->where('pt_id', '=', $get->pt_id_ref)
                ->where('plst_status', '=', 'INSTOCK')
                ->delete();
            }
            //////////////////////////////////
            $plst = DB::table('product_location_setup_transactions')
            ->select('product_location_setup_transactions.id as id', 'pls_id', 'pl_code', 'plst_qty', 'plst_status', 'pls_qty')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->where('pt_id', '=', $pt_id)->get();
            if (!empty($plst->first())) {
                foreach ($plst as $row) {
                    if (in_array(['pl_code' => $row->pl_code], $auto_instock)) {
                        $update = DB::table('product_location_setups')
                        ->where('id', '=', $row->pls_id)->update([
                            'pls_qty' => $row->pls_qty+$row->plst_qty
                        ]);
                        $update = DB::table('product_location_setup_transactions')
                        ->where('id', '=', $row->id)
                        ->update([
                            // 'pt_id' => null, 
                            'plst_status' => 'INSTOCK'
                        ]);
                    } else {
                        if ($row->plst_status == 'WAITING ONLINE') {
                            $update = DB::table('product_location_setups')
                            ->where('id', '=', $row->pls_id)->update([
                                'pls_qty' => $row->pls_qty+$row->plst_qty
                            ]);
                            $update = DB::table('product_location_setup_transactions')
                            ->where('id', '=', $row->id)
                            ->update([
                                // 'pt_id' => null, 
                                'plst_status' => 'INSTOCK'
                            ]);
                        } else {
                            $update = DB::table('product_location_setup_transactions')
                            ->where('id', '=', $row->id)
                            ->update([
                                // 'pt_id' => null, 
                                'plst_status' => $status
                            ]);
                        }
                    }
                }
            }
            ///////////////////////////////////
            // $delete = DB::table('pos_transaction_details')->where('pt_id', '=', $pt_id)->delete();
            // $delete = DB::table('invoice_editors')->where('pt_id', '=', $pt_id)->delete();
            // $delete = DB::table('pos_transactions')->where('id', '=', $pt_id)->delete();
            $delete = DB::table('pos_transactions')->where('id', '=', $pt_id)->update([
                'pt_id_ref' => null,
                'pos_status' => 'CANCEL'
            ]);
            $r['status'] = 200;
        } else {
            $plst = DB::table('product_location_setup_transactions')
            ->select('product_location_setup_transactions.id as id', 'pls_id', 'pl_code', 'plst_qty', 'plst_status', 'pls_qty')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->where('pt_id', '=', $pt_id)->get();
            if (!empty($plst->first())) {
                foreach ($plst as $row) {
                    if (in_array(['pl_code' => $row->pl_code], $auto_instock)) {
                        $update = DB::table('product_location_setups')
                        ->where('id', '=', $row->pls_id)->update([
                            'pls_qty' => $row->pls_qty+$row->plst_qty
                        ]);
                        $update = DB::table('product_location_setup_transactions')
                        ->where('id', '=', $row->id)
                        ->update([
                            // 'pt_id' => null, 
                            'plst_status' => 'INSTOCK'
                        ]);
                    } else {
                        if ($row->plst_status == 'WAITING ONLINE') {
                            $update = DB::table('product_location_setups')
                            ->where('id', '=', $row->pls_id)->update([
                                'pls_qty' => $row->pls_qty+$row->plst_qty
                            ]);
                            $update = DB::table('product_location_setup_transactions')
                            ->where('id', '=', $row->id)
                            ->update([
                                // 'pt_id' => null, 
                                'plst_status' => 'INSTOCK'
                            ]);
                        } else {
                            $update = DB::table('product_location_setup_transactions')
                            ->where('id', '=', $row->id)
                            ->update([
                                // 'pt_id' => null, 
                                'plst_status' => $status
                            ]);
                        }
                    }
                }
            }
            // $delete = DB::table('pos_transaction_details')->where('pt_id', '=', $pt_id)->delete();
            // $delete = DB::table('invoice_editors')->where('pt_id', '=', $pt_id)->delete();
            // $delete = DB::table('pos_transactions')->where('id', '=', $pt_id)->delete();
            $delete = DB::table('pos_transactions')->where('id', '=', $pt_id)->update([
                'pos_status' => 'CANCEL'
            ]);
            $r['status'] = 200;
        }
        return json_encode($r);
    }

    public function cancelItem(Request $request) {
        $auto_instock = BuyOneGetOne::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'buy_one_get_ones.pl_id')->get()->toArray();
        array_push($auto_instock, ['pl_code' => 'TOKO']);
        array_push($auto_instock, ['pl_code' => 'WGN1']);

        $pt_id = $request->post('pt_id');
        $ptd_id = $request->post('ptd_id');
        $pl_id = $request->post('pl_id');
        $pst_id = $request->post('pst_id');

        $pls_id = DB::table('product_location_setups')->select('id')
        ->where([
            'pst_id' => $pst_id,
            'pl_id' => $pl_id, 
        ])->first()->id;

        $stt_id = DB::table('pos_transactions')->select('stt_id')
        ->where('id', '=', $pt_id)->first()->stt_id;
        if ($stt_id == '1') {
            $status = 'WAITING ONLINE';
        } else {
            $status = 'WAITING OFFLINE';
        }

        $plst = DB::table('product_location_setup_transactions')
        ->select('product_location_setup_transactions.id as id', 'pls_id', 'pl_code', 'pls_qty', 'plst_status', 'plst_qty')
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->where([
            'pt_id' => $pt_id, 
            'pls_id' => $pls_id
        ])->first();
        if (in_array(['pl_code' => $plst->pl_code], $auto_instock)) {
            $update = DB::table('product_location_setups')->where('id', '=', $plst->pls_id)
            ->update([
                'pls_qty' => $plst->pls_qty+$plst->plst_qty
            ]);
            $plst = DB::table('product_location_setup_transactions')
            ->where('id', '=', $plst->id)->update([
                'pt_id' => null, 
                'plst_status' => 'INSTOCK'
            ]);
        } else {
            if ($plst->plst_status == 'WAITING ONLINE') {
                $update = DB::table('product_location_setups')
                ->where('id', '=', $plst->pls_id)->update([
                    'pls_qty' => $plst->pls_qty+$plst->plst_qty
                ]);
                $update = DB::table('product_location_setup_transactions')
                ->where('id', '=', $plst->id)
                ->update([
                    'pt_id' => null, 
                    'plst_status' => 'INSTOCK'
                ]);
            } else {
                $plst = DB::table('product_location_setup_transactions')
                ->where('id', '=', $plst->id)->update([
                    'pt_id' => null, 
                    'plst_status' => $status
                ]);
            }
        }

        $delete = DB::table('pos_transaction_details')->where('id', '=', $ptd_id)->delete();

        $total = DB::table('pos_transaction_details')
        ->where('pt_id', '=', $pt_id)
        ->sum('pos_td_total_price');
        $admin = DB::table('pos_transactions')->select('pos_admin_cost')->where('id', '=', $pt_id)->first()->pos_admin_cost;
        $update = DB::table('pos_transactions')->where('id', '=', $pt_id)
        ->update([
            'pos_real_price' => ($total-$admin),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $r['status'] = 200;
        return json_encode($r);
    }

    public function getHistoryDatatables(Request $request)
    {
        $user = new User;
        $select = ['g_name'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();

        if(request()->ajax()) {
            return datatables()->of(DB::table('invoice_editors')
            ->select("invoice_editors.id", "pos_invoice", "u_name", "activity", "note", "invoice_editors.created_at", "invoice_editors.updated_at")
            ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'invoice_editors.pt_id')
            ->leftJoin('users', 'users.id', '=', 'invoice_editors.u_id')
            ->where(function($w) use ($user_data) {
                if ($user_data->g_name != 'administrator') {
                    $w->where('invoice_editors.u_id', '=', Auth::user()->id);
                }
            }))
            ->editColumn('created_at', function($d) {
                return date('d/m/Y H:i:s', strtotime($d->created_at));
            })
            ->editColumn('updated_at', function($d) {
                return date('d/m/Y H:i:s', strtotime($d->updated_at));
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('u_name', 'LIKE', "%$search%")
                        ->orWhere('pos_invoice', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function doEdit(Request $request) {
        $type = $request->post('type');
        $id = $request->post('id');
        $pt_id = $request->post('pt_id');
        $qty = $request->post('qty');
        $price = $request->post('price');
        $nameset = $request->post('nameset');
        $value = $request->post('value');

        if ($type == 'cashier') {
            $update = DB::table('pos_transactions')->where('id', '=', $id)
            ->update([
                'u_id' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else if ($type == 'division') {
            $update = DB::table('pos_transactions')->where('id', '=', $id)
            ->update([
                'stt_id' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else if ($type == 'subdivision') {
            $update = DB::table('pos_transactions')->where('id', '=', $id)
            ->update([
                'std_id' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else if ($type == 'method') {
            $update = DB::table('pos_transactions')->where('id', '=', $id)
            ->update([
                'pm_id' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else if ($type == 'admin') {
            $total = DB::table('pos_transaction_details')
            ->where('pt_id', '=', $id)
            ->sum('pos_td_total_price');
            $update = DB::table('pos_transactions')->where('id', '=', $id)
            ->update([
                'pos_admin_cost' => $value,
                'pos_real_price' => ($total-$value),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else if ($type == 'date') {
            $update = DB::table('pos_transactions')->where('id', '=', $id)
            ->update([
                'created_at' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else if ($type == 'price') {
            $update = DB::table('pos_transaction_details')->where('id', '=', $id)
            ->update([
                'pos_td_discount_price' => ($qty*$value),
                'pos_td_marketplace_price' => ($qty*$value),
                'pos_td_sell_price' => $value,
                'pos_td_total_price' => (($qty*$value)+$nameset),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $total = DB::table('pos_transaction_details')
            ->where('pt_id', '=', $pt_id)
            ->sum('pos_td_total_price');
            $admin = DB::table('pos_transactions')->select('pos_admin_cost')->where('id', '=', $pt_id)->first()->pos_admin_cost;
            $update = DB::table('pos_transactions')->where('id', '=', $pt_id)
            ->update([
                'pos_real_price' => ($total-$admin),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else if ($type == 'nameset') {
            if ($value > 0) {
                $update = DB::table('pos_transaction_details')->where('id', '=', $id)
                ->update([
                    'pos_td_nameset_price' => $value,
                    'pos_td_total_price' => (($qty*$price)+$value),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $update = DB::table('pos_transaction_details')->where('id', '=', $id)
                ->update([
                    'pos_td_nameset' => '0',
                    'pos_td_nameset_price' => $value,
                    'pos_td_total_price' => ($qty*$price),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            $total = DB::table('pos_transaction_details')
            ->where('pt_id', '=', $pt_id)
            ->sum('pos_td_total_price');
            $admin = DB::table('pos_transactions')->select('pos_admin_cost')->where('id', '=', $pt_id)->first()->pos_admin_cost;
            $update = DB::table('pos_transactions')->where('id', '=', $pt_id)
            ->update([
                'pos_real_price' => ($total-$admin),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else if ($type == 'status') {
            $update = DB::table('product_location_setup_transactions')->where('id', '=', $id)
            ->update([
                'plst_status' => $value
            ]);
        }

        if (!empty($update)) {
            $r['status'] = 200;
        } else {
            $r['status'] = 400;
        }
        return json_encode($r);
    }

    public function doneEdit(Request $request) {
        $pt_id = $request->post('pt_id');
        $note = $request->post('note');

        $update = DB::table('invoice_editors')->where('pt_id', '=', $pt_id)
        ->update([
            'note' => $note,
            'status' => '1',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if (!empty($update)) {
            $r['status'] = 200;
        } else {
            $r['status'] = 400;
        }

        return json_encode($r);
    }

    public function checkInvoice(Request $request) {
        $pos_invoice = ltrim($request->post('pos_invoice'));
        $check = DB::table('pos_transactions')
        ->select('id')
        ->where('pos_invoice', '=', $pos_invoice)
        ->where('pos_status', '!=', 'CANCEL')
        ->where(function($w) {
            $user = new User;
            $select = ['g_name'];
            $where = [
                'users.id' => Auth::user()->id
            ];
            $user_data = $user->checkJoinData($select, $where)->first();
            if ($user_data->g_name != 'administrator') {
                $w->whereRaw('ts_pos_transactions.created_at  >= now() - INTERVAL 30 DAY');
            }
        })->first();
        if (!empty($check)) {
            $is_edited = DB::table('invoice_editors')->where([
                'u_id' => Auth::user()->id, 
                'pt_id' => $check->id,
            ])->exists();
            if (!$is_edited) {
                DB::table('invoice_editors')->insert([
                    'pt_id' => $check->id,
                    'u_id' => Auth::user()->id,
                    'activity' => 'Mengedit invoice penjualan '.$pos_invoice, 
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'status' => '0'
                ]);
            }
            $r['id'] = $check->id;
            $r['status'] = 200;
        } else {
            $r['status'] = 400;
        }
        return json_encode($r);
    }

    public function checkActiveEdit(Request $request) {
        $check = DB::table('invoice_editors')
        ->select('pos_invoice')
        ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'invoice_editors.pt_id')
        ->where('invoice_editors.u_id', '=', Auth::user()->id)
        ->where('invoice_editors.status', '=', '0')
        ->where('pos_status', '!=', 'CANCEL')
        ->first();
        if (!empty($check)) {
            $r['invoice'] = $check->pos_invoice;
            $r['status'] = 200;
        } else {
            $r['status'] = 400;
        }
        return json_encode($r);
    }

    public function storePermissionData(Request $request)
    {
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'st_id' => $request->post('st_id'),
            'stt_id' => $request->post('stt_id'),
            'u_id' => $request->post('u_id'),
        ];

        $created = [
            'created_at' => date('Y-m-d H:i:s')
        ];

        $updated = [
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($mode == 'add') {
            $data = array_merge($data, $created, $updated);
            $save = DB::table('invoice_editor_permissions')->insert($data);
        } else {
            $data = array_merge($data, $updated);
            $save = DB::table('invoice_editor_permissions')->where('id', '=', $id)->update($data);
        }

        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deletePermissionData(Request $request)
    {
        $id = $request->post('id');
        $delete = DB::table('invoice_editor_permissions')->where('id', '=', $id)->delete();
        if ($delete) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
    
}
