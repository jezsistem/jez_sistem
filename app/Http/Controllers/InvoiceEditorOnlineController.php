<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\BuyOneGetOne;

class InvoiceEditorOnlineController extends Controller
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

    private function checkAccess()
    {
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
        return view('app.invoice_editor_online.invoice_editor_online', compact('data'));
    }

    public function getPermissionDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(DB::table('invoice_editor_permissions')
                ->select('invoice_editor_permissions.id', 'invoice_editor_permissions.st_id', 'invoice_editor_permissions.stt_id', 'u_id', 'st_name', 'stt_name', 'u_name')
                ->leftJoin('stores', 'stores.id', '=', 'invoice_editor_permissions.st_id')
                ->leftJoin('store_types', 'store_types.id', '=', 'invoice_editor_permissions.stt_id')
                ->leftJoin('users', 'users.id', '=', 'invoice_editor_permissions.u_id'))
                ->editColumn('action', function ($d) {
                    return "<a class='btn btn-sm btn-danger' data-id='" . $d->id . "' id='delete_btn'><i class='fa fa-trash'></i></a>";
                })
                ->rawColumns(['action'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
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
        if (request()->ajax()) {
            return datatables()->of(DB::table('online_transactions')
                ->select('online_transactions.id', 'order_number', 'no_resi', 'stores.st_name', 'users.u_name', 'order_status', 'platform_name', 'order_date_created', 'shipping_method', 'shipping_fee', 'payment_method', 'total_payment', 'time_print')
                ->leftJoin('stores', 'stores.id', '=', 'online_transactions.st_id')
                ->leftJoin('users', 'users.id', '=', 'online_transactions.u_print')
                ->where(function ($w) use ($pt_id) {
                    if (!empty($pt_id)) {
                        $w->where('online_transactions.id', '=', $pt_id);
                    } else {
                        $w->where('online_transactions.id', '=', '!@@#$%');
                    }
                }))
                ->editColumn('action', function ($d) {
                    return "<a class='btn btn-sm btn-danger' data-pt_id='" . $d->id . "' id='cancel_btn'>Batalkan</a>";
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getDetailDatatables(Request $request)
    {
        $pt_id = $request->get('pt_id');
        if (request()->ajax()) {
            return datatables()->of(DB::table('online_transaction_details')
                ->selectRaw("ts_online_transaction_details.id as id, CONCAT(br_name,' ',p_name,' ',p_color,' ',sz_name) as article, to_id, sku,qty, original_price, discount_seller, total_discount, price_after_discount as final_price")
                ->leftJoin('product_stocks', 'product_stocks.ps_barcode', '=', 'online_transaction_details.sku')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where(function ($w) use ($pt_id) {
                    if (!empty($pt_id)) {
                        $w->where('online_transaction_details.to_id', '=', $pt_id);
                    } else {
                        $w->where('online_transaction_details.to_id', '=', '!@@#$%');
                    }
                }))
                ->editColumn('qty', function ($d) {
                    return $d->qty;
                })
                ->editColumn('sku', function ($d) {
                    $sku = '';
                    if (!empty($d->sku)) {
                        $sku = $d->sku;
                    }
                    return "<input type='text' data-tod_id='" . $d->id . "' data-sku='" . $d->sku . "' value='" . $sku . "' id='sku'/>";
                })
                ->editColumn('action', function ($d) use ($pt_id) {
                    $status = null;
                    if (!empty($pt_id)) {
                        $status = DB::table('online_transactions')->select('order_status')->where('id', '=', $pt_id)->first()->order_status;
                    }
                    if ($status != '0') {
//                        return "<a class='btn btn-sm btn-danger' data-pst_id='" . $d->pst_id . "' data-pl_id='" . $d->pl_id . "' data-pt_id='" . $d->pt_id . "' data-ptd_id='" . $d->id . "' id='cancel_item_btn'>Batalkan</a>";
                        return "<a class='btn btn-sm btn-danger' data-tod_id='" . $d->id . "'  data-to_id='" . $d->to_id . "'  id='cancel_item_btn'>Batalkan</a>";
                    }
                })
                ->rawColumns(['sku', 'action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getTrackingDatatables(Request $request)
    {
        $pt_id = $request->get('pt_id');
        if (request()->ajax()) {
            return datatables()->of(DB::table('product_location_setup_transactions')
                ->selectRaw("ts_product_location_setup_transactions.id as id, CONCAT(br_name,' ',p_name,' ',p_color,' ',sz_name) as article,
            pl_code, plst_qty, plst_status")
                ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where(function ($w) use ($pt_id) {
                    if (!empty($pt_id)) {
                        $w->where('product_location_setup_transactions.pt_id', '=', $pt_id);
                    } else {
                        $w->where('product_location_setup_transactions.pt_id', '=', '!@@#$%');
                    }
                }))
                ->editColumn('status', function ($d) {
                    $status = ['DONE', 'REFUND', 'EXCHANGE', 'WAITING FOR PACKING', 'WAITING OFFLINE', 'WAITING ONLINE', 'COMPLAINT'];
                    $stts = '';
                    $stts .= "<select data-id='" . $d->id . "' id='status'>";
                    for ($i = 0; $i < count($status); $i++) {
                        if ($d->plst_status == $status[$i]) {
                            $stts .= "<option value='" . $status[$i] . "' selected>" . $status[$i] . "</option>";
                        } else {
                            $stts .= "<option value='" . $status[$i] . "'>" . $status[$i] . "</option>";
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

    public function cancelInvoice(Request $request)
    {
        $pt_id = $request->post('pt_id');


        $delete = DB::table('online_transactions')
            ->where('id', '=', $pt_id)
            ->delete();

        if ($delete) {

            $r['status'] = 200;
        } else {
            $r['status'] = 400;
        }

        return json_encode($r);
    }

    public function cancelItem(Request $request)
    {
        $tod_id = $request->tod_id;
        $to_id = $request->to_id;
        $delete = DB::table('online_transaction_details')->where('id', '=', $tod_id)->delete();


        if ($delete) {
            $total = DB::table('online_transactions')
                ->where('pt_id', '=', $to_id)
                ->sum('price_after_discount');

            DB::table('online_transactions')->where('id', '=', $to_id)
                ->update([
                    'total_payment' => $total,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            $r['status'] = 200;
        } else {
            $r['status'] = 400;
        }

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

        if (request()->ajax()) {
            return datatables()->of(DB::table('invoice_editor_onlines')
                ->select("invoice_editor_onlines.id", "order_number", "u_name", "activity", "note", "invoice_editor_onlines.created_at", "invoice_editor_onlines.updated_at")
                ->leftJoin('online_transactions', 'online_transactions.id', '=', 'invoice_editor_onlines.to_id')
                ->leftJoin('users', 'users.id', '=', 'invoice_editor_onlines.u_id')
                ->where(function ($w) use ($user_data) {
                    if ($user_data->g_name != 'administrator') {
                        $w->where('invoice_editor_onlines.u_id', '=', Auth::user()->id);
                    }
                }))
                ->editColumn('created_at', function ($d) {
                    return date('d/m/Y H:i:s', strtotime($d->created_at));
                })
                ->editColumn('updated_at', function ($d) {
                    return date('d/m/Y H:i:s', strtotime($d->updated_at));
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('u_name', 'LIKE', "%$search%")
                                ->orWhere('order_number', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function doEdit(Request $request)
    {
        $type = $request->post('type');
        $id = $request->post('id');
        $pt_id = $request->post('pt_id');
        $qty = $request->post('qty');
        $price = $request->post('price');
        $nameset = $request->post('nameset');
        $value = $request->post('value');
        $payment_other = $request->post('payment_other');


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
        } else if ($type == 'pos_status_change') {
            $update = DB::table('pos_transactions')->where('id', '=', $id)
                ->update([
                    'pos_status' => $value,
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
                    'pos_real_price' => ($total - $value),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        } else if ($type == 'pos_payment') {
            $total = DB::table('pos_transaction_details')
                ->where('pt_id', '=', $id)
                ->sum('pos_td_total_price');
            $update = DB::table('pos_transactions')->where('id', '=', $id)
                ->update([
                    'pos_payment' => $value,
                    'pos_real_price' => ($value + $payment_other),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        } else if ($type == 'pos_payment_partial') {
            $total = DB::table('pos_transaction_details')
                ->where('pt_id', '=', $id)
                ->sum('pos_td_total_price');
            $update = DB::table('pos_transactions')->where('id', '=', $id)
                ->update([
                    'pos_payment_partial' => $value,
                    'pos_real_price' => ($value + $payment_other),
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
                    'pos_td_discount_price' => ($qty * $value),
                    'pos_td_marketplace_price' => ($qty * $value),
                    'pos_td_sell_price' => $value,
                    'pos_td_total_price' => (($qty * $value) + $nameset),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            $total = DB::table('pos_transaction_details')
                ->where('pt_id', '=', $pt_id)
                ->sum('pos_td_total_price');
            $admin = DB::table('pos_transactions')->select('pos_admin_cost')->where('id', '=', $pt_id)->first()->pos_admin_cost;
            $update = DB::table('pos_transactions')->where('id', '=', $pt_id)
                ->update([
                    'pos_real_price' => ($total - $admin),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        } else if ($type == 'nameset') {
            if ($value > 0) {
                $update = DB::table('pos_transaction_details')->where('id', '=', $id)
                    ->update([
                        'pos_td_nameset_price' => $value,
                        'pos_td_total_price' => (($qty * $price) + $value),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            } else {
                $update = DB::table('pos_transaction_details')->where('id', '=', $id)
                    ->update([
                        'pos_td_nameset' => '0',
                        'pos_td_nameset_price' => $value,
                        'pos_td_total_price' => ($qty * $price),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            }
            $total = DB::table('pos_transaction_details')
                ->where('pt_id', '=', $pt_id)
                ->sum('pos_td_total_price');
            $admin = DB::table('pos_transactions')->select('pos_admin_cost')->where('id', '=', $pt_id)->first()->pos_admin_cost;
            $update = DB::table('pos_transactions')->where('id', '=', $pt_id)
                ->update([
                    'pos_real_price' => ($total - $admin),
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

    public function doEditSku(Request $request)
    {
        $type = $request->post('type');
        $id = $request->post('id');
        $sku = $request->post('sku');

        if ($type == 'sku') {
            $update = DB::table('online_transaction_details')->where('id', '=', $id)
                ->update([
                    'sku' => $sku,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        }
        if (!empty($update)) {
            $r['status'] = 200;
        } else {
            $r['status'] = 400;
        }
        return json_encode($r);
    }

    public function doneEdit(Request $request)
    {
        $pt_id = $request->post('pt_id');
        $note = $request->post('note');

        $update = DB::table('invoice_editor_onlines')->where('to_id', '=', $pt_id)
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

    public function checkInvoice(Request $request)
    {
        $pos_invoice = ltrim($request->post('pos_invoice'));

        $check = DB::table('online_transactions')
            ->select('id')
            ->where(function ($query) use ($pos_invoice) {
                $query->where('no_resi', '=', $pos_invoice)
                    ->orWhere('order_number', '=', $pos_invoice);
            })
//            ->where('time_print', '!=', null)
            ->first();


        if (!empty($check)) {
            $is_edited = DB::table('invoice_editor_onlines')->where([
                'u_id' => Auth::user()->id,
                'to_id' => $check->id,
            ])->exists();
            if (!$is_edited) {
                DB::table('invoice_editor_onlines')->insert([
                    'to_id' => $check->id,
                    'u_id' => Auth::user()->id,
                    'activity' => 'Mengedit invoice penjualan ' . $pos_invoice,
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

    public function checkActiveEdit(Request $request)
    {
        $check = DB::table('invoice_editor_onlines')
            ->select('order_number')
            ->leftJoin('online_transactions', 'online_transactions.id', '=', 'invoice_editor_onlines.to_id')
            ->where('invoice_editor_onlines.u_id', '=', Auth::user()->id)
            ->where('invoice_editor_onlines.status', '=', '0')
            ->first();
        if (!empty($check)) {
            $r['invoice'] = $check->order_number;
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
