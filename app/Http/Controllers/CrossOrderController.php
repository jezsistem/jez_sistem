<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\PosShippingInformation;
use App\Models\StoreTypeDivision;
use App\Models\ProductLocationSetup;
use App\Models\ProductLocationSetupTransaction;
use App\Models\ProductStock;
use App\Models\Courier;
use App\Models\Store;
use App\Models\Customer;
use App\Models\Wilayah;
use App\Models\UserActivity;

class CrossOrderController extends Controller
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
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'std_id' => StoreTypeDivision::where('dv_delete', '!=', '1')->orderByDesc('id')->pluck('dv_name', 'id'),
        ];
        return view('app.cross_order.cross_order', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = Auth::user()->st_id;
        }
        if(request()->ajax()) {
            return datatables()->of(PosTransaction::select('pos_transactions.id as pt_id', 'u_name', 'u_id_cross', 'cust_name', 'pos_invoice', 'cust_id', 'st_id_ref', 'std_id', 'stt_name', 'dv_name', 'cr_id', 'pt_id_ref', 'pos_shipping_number', 'psi_description', 'pos_transactions.created_at as pos_created', 'pos_status')
            ->leftJoin('store_types', 'store_types.id', '=', 'pos_transactions.stt_id')
            ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
            ->leftJoin('pos_shipping_information', 'pos_shipping_information.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('pos_transaction_details', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
            ->leftJoin('users', 'users.id', '=', 'pos_transactions.u_id')
            ->where('pos_transactions.cross_order', '=', '1')
            ->where(function($w) use ($st_id) {
                $w->where('pos_transactions.st_id_ref', '=', $st_id);
            })
            ->groupBy('pos_invoice'))
            ->editColumn('pos_invoice', function($data){
                if (!empty($data->pt_id_ref)) {
                    $invoice = TransaksiOnline::select('pos_invoice')->where('id', $data->pt_id_ref)->get()->first()->pos_invoice;
                    if (strtoupper($data->stt_name) == 'ONLINE') {
                        return '<a class="text-white" href="#" data-pt_id="'.$data->pt_id.'" id="detail_btn"><span class="btn btn-sm btn-warning" title="'.$invoice.'">'.$data->pos_invoice.'</span></a>';
                    } else {
                        return '<a class="text-white" href="#" data-pt_id="'.$data->pt_id.'" id="detail_btn"><span class="btn btn-sm btn-warning" title="'.$invoice.'">'.$data->pos_invoice.'</span></a>';
                    }
                } else {
                    if (strtoupper($data->stt_name) == 'ONLINE') {
                        return '<a class="text-white" href="#" data-pt_id="'.$data->pt_id.'" id="detail_btn"><span class="btn btn-sm btn-primary">'.$data->pos_invoice.'</span></a>';
                    } else {
                        return '<a class="text-white" href="#" data-pt_id="'.$data->pt_id.'" id="detail_btn"><span class="btn btn-sm btn-primary">'.$data->pos_invoice.'</span></a>';
                    }
                    return '';
                }
            })
            ->editColumn('st_name_end', function($data){
                $store = Store::select('st_name')->where('id', '=', $data->st_id_ref)->get()->first()->st_name;
                return $store;
            })
            ->editColumn('u_name_end', function($data){
                if (!empty($data->u_id_cross)) {
                  $u_name_end = User::select('u_name')->where('id', '=', $data->u_id_cross)->get()->first()->u_name;
                } else {
                  $u_name_end = '-';
                }
                return $u_name_end;
            })
            ->editColumn('total_item_reject', function($data){
                $total_item = PosTransactionDetail::where('pt_id', $data->pt_id)->where('pos_td_reject', '=', '1')->sum('pos_td_qty');
                return $total_item;
            })
            ->editColumn('pos_created', function($data){
                return '<span style="white-space: nowrap;">'.date('d-m-Y H:i:s', strtotime($data->pos_created)).'</span>';
            })
            ->editColumn('total_item', function($data){
                $total_item = PosTransactionDetail::where('pt_id', $data->pt_id)->sum('pos_td_qty');
                if (!empty($total_item)) {
                    return $total_item;
                } else {
                    return '-';
                }
            })
            ->editColumn('total_price', function($data){
                $total_price = PosTransactionDetail::where('pt_id', $data->pt_id)->sum('pos_td_total_price');
                if (!empty($total_price)) {
                    return number_format($total_price);
                } else {
                    return '-';
                }
            })
            ->editColumn('pos_status', function($data){
                $ref_invoice = '';
                if ($data->pos_status == 'DONE') {
                    if (!empty($data->pt_id_ref)) {
                        $ref_invoice = PosTransaction::select('pos_invoice')->where('id', $data->pt_id_ref)->get()->first()->pos_invoice;
                        $btn = 'btn-warning';
                    } else {
                        $btn = 'btn-success';
                    }
                } else if ($data->pos_status == 'REFUND') {
                    $btn = 'btn-danger';
                } else if ($data->pos_status == 'EXCHANGE') {
                    $btn = 'btn-danger';
                } else if ($data->pos_status == 'NAMESET') {
                    $btn = 'btn-info';
                } else if ($data->pos_status == 'SHIPPING NUMBER') {
                    $btn = 'btn-warning';
                } else if ($data->pos_status == 'IN DELIVERY') {
                    $btn = 'btn-info';
                } else if ($data->pos_status == 'WAITING FOR CONFIRMATION') {
                    $btn = 'btn-warning';
                } else if ($data->pos_status == 'IN PROGRESS') {
                    $btn = 'btn-primary';
                } else if ($data->pos_status == 'CANCEL') {
                    $btn = 'btn-danger';
                }
                if ($data->pos_status == 'SHIPPING NUMBER' || $data->pos_status == 'IN DELIVERY') {
                    return '<span style="white-space: nowrap;" data-pt_id="'.$data->pt_id.'" data-cust_id="'.$data->cust_id.'" class="btn btn-sm '.$btn.'" id="shipping_number_btn">'.$data->pos_status.' '.$ref_invoice.'</span>
                            <span style="white-space: nowrap;" class="btn btn-sm btn-success" data-pt_id="'.$data->pt_id.'" id="print_btn">Print</span>';
                } if ($data->pos_status == 'WAITING FOR CONFIRMATION') {
                    if ($data->st_id_ref != Auth::user()->st_id) {
                        if (strtolower(Auth::user()->u_name) == 'aufa kenshi') {
                          return '<span style="white-space: nowrap;" data-pt_id="'.$data->pt_id.'" class="btn btn-sm '.$btn.'" id="confirmation_btn">'.$data->pos_status.'</span>';
                        } else {
                          return '<span style="white-space: nowrap;" data-pt_id="'.$data->pt_id.'" class="btn btn-sm '.$btn.'">'.$data->pos_status.'</span>';
                        }
                    } else {
                        return '<span style="white-space: nowrap;" data-pt_id="'.$data->pt_id.'" class="btn btn-sm '.$btn.'" id="confirmation_btn">'.$data->pos_status.'</span>';
                    }
                } else {
                    if ($data->st_id_ref == Auth::user()->st_id) {
                      return '
                        <span style="white-space: nowrap;" title="'.$ref_invoice.'" class="btn btn-sm '.$btn.'">'.$data->pos_status.'</span>
                        <span style="white-space: nowrap;" class="btn btn-sm btn-success" data-pt_id="'.$data->pt_id.'" id="print_btn">Print</span>';
                    } else {
                      return '
                        <span style="white-space: nowrap;" title="'.$ref_invoice.'" class="btn btn-sm '.$btn.'">'.$data->pos_status.'</span>';
                    }
                }
            })
            ->rawColumns(['pos_invoice', 'pos_status', 'pos_created'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('status'))) {
                    $instance->where('pos_status', $request->get('status'));
                }
                if (!empty($request->get('division'))) {
                    $instance->where('std_id', $request->get('division'));
                }
                if (!empty($request->get('st_id'))) {
                    $instance->where('st_id_ref', $request->get('st_id'));
                }
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('pos_invoice', 'LIKE', "%$search%")
                        ->orWhere('pos_shipping_number', 'LIKE', "%$search%")
                        ->orWhere('cust_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function confirmationDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PosTransactionDetail::select('pos_transaction_details.id as ptd_id', 'p_name', 'br_name', 'pl_code', 'p_color', 'sz_name', 'pos_td_qty', 'pos_td_description', 'pos_td_reject')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'pos_transaction_details.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where('pt_id', '=', $request->pt_id))
            ->editColumn('article', function($data){
              return '<span class="btn btn-primary">['.$data->br_name.'] '.$data->p_name.' '.$data->p_color.' ['.$data->sz_name.']</span>';
            })
            ->editColumn('ready', function($data){
              if ($data->pos_td_reject == '1') {
                return '<input class="form-control" type="checkbox" data-ptd_id="'.$data->ptd_id.'" id="ready_check" />';
              } else {
                return '<input class="form-control" type="checkbox" data-ptd_id="'.$data->ptd_id.'" id="ready_check" checked/>';
              }
            })
            ->editColumn('note', function($data){
              return '
                <textarea class="form-control" data-ptd_id="'.$data->ptd_id.'" id="note">
                  '.$data->pos_td_description.'
                </textarea>';
            })
            ->rawColumns(['article', 'ready', 'note'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function detailDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PosTransactionDetail::select('pos_transaction_details.id as ptd_id', 'p_name', 'br_name', 'pl_code', 'p_color', 'sz_name', 'pos_td_qty', 'pos_td_description', 'pos_td_reject')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'pos_transaction_details.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where('pt_id', '=', $request->pt_id))
            ->editColumn('article', function($data){
              return '<span class="btn btn-primary">['.$data->br_name.'] '.$data->p_name.' '.$data->p_color.' ['.$data->sz_name.']</span>';
            })
            ->editColumn('ready', function($data){
              if ($data->pos_td_reject == '1') {
                return '<input class="form-control" type="checkbox" data-ptd_id="'.$data->ptd_id.'" id="ready_check" disabled/>';
              } else {
                return '<input class="form-control" type="checkbox" data-ptd_id="'.$data->ptd_id.'" id="ready_check" checked disabled/>';
              }
            })
            ->editColumn('note', function($data){
              return '
                <textarea class="form-control" data-ptd_id="'.$data->ptd_id.'" id="note" readonly>
                  '.$data->pos_td_description.'
                </textarea>';
            })
            ->rawColumns(['article', 'ready', 'note'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function takeConfirmationDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PosTransactionDetail::select('pos_transaction_details.id as ptd_id', 'p_name', 'br_name', 'pl_code', 'p_color', 'sz_name', 'pos_td_qty', 'pos_td_qty_pickup', 'pos_td_description', 'pos_td_reject')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'pos_transaction_details.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where('pt_id', '=', $request->pt_id)
            ->where('pos_td_reject', '!=', '1')
            ->whereNull('pos_td_qty_pickup'))
            ->editColumn('article', function($data){
              return '<span class="btn btn-primary">['.$data->br_name.'] '.$data->p_name.' '.$data->p_color.' ['.$data->sz_name.']</span>';
            })
            ->editColumn('action', function($data){
              return '<a class="btn btn-sm btn-success" data-ptd_qty="'.$data->pos_td_qty.'" data-ptd_id="'.$data->ptd_id.'" id="get_cross_item_btn" style="font-weight:bold;">Ambil</a>';
            })
            ->rawColumns(['article', 'ready', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function saveData(Request $request)
    {
        $pt_id = $request->_pt_id;
        $get_article = PosTransactionDetail::where('pt_id', '=', $pt_id)
        ->where('pos_td_reject', '!=', '1')->get();
        foreach ($get_article as $row) {
          if ($row->pos_td_qty < 0) {
            continue;
          }
          $pls = ProductLocationSetup::select('id', 'pls_qty')->where('pst_id' , $row->pst_id)->where('pl_id' , $row->pl_id)->get()->first();
          $product_setup_location = ProductLocationSetup::where('id' , $pls->id)->update([
              'pls_qty' => ($pls->pls_qty-$row->pos_td_qty)
          ]);
        }
        $exists = PosTransactionDetail::where('pt_id', '=', $pt_id)
        ->where('pos_td_reject', '!=', '1')->exists();
        if ($exists) {
            $update = PosTransaction::where('id', '=', $pt_id)->update([
                'pos_status' => 'IN PROGRESS',
                'u_id_cross' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $update = PosTransactionDetail::where('pt_id', '=', $pt_id)->update([
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $update = PosTransaction::where('id', '=', $pt_id)->update([
              'pos_status' => 'CANCEL',
              'u_id_cross' => Auth::user()->id
            ]);
        }
        if (!empty($update)) {
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkCrossOrder()
    {
        $total_order = PosTransaction::select('id', 'st_id_ref')
        ->where('cross_order', '=', '1')
        ->where('st_id_ref', '=', Auth::user()->st_id)->where('pos_status', '=', 'WAITING FOR CONFIRMATION')->count('id');
        $check = PosTransaction::select('id', 'st_id_ref')
        ->where('cross_order', '=', '1')
        ->where('st_id_ref', '=', Auth::user()->st_id)->where('pos_status', '=', 'WAITING FOR CONFIRMATION')->exists();
        if ($check) {
          $r['total_order'] = $total_order;
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
         return json_encode($r);
    }

    public function reloadCrossOrderInvoice()
    {
        $data = [
            'invoice' => PosTransaction::where('cross_order', '=', '1')
            ->where('pos_status', '=', 'IN PROGRESS')->orderByDesc('id')
            ->where('st_id_ref', '=', Auth::user()->st_id)->pluck('pos_invoice', 'id'),
        ];
        return view('app.dashboard.helper._reload_cross_invoice', compact('data'));
    }

    public function saveNote(Request $request)
    {
        $ptd_id = $request->_ptd_id;
        $note = $request->_note;
        $update = PosTransactionDetail::where('id', '=', $ptd_id)->update([
          'pos_td_description' => $note
        ]);
        if (!empty($update)) {
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function getCrossItem(Request $request)
    {
        $ptd_id = $request->_ptd_id;
        $ptd_qty = $request->_ptd_qty;
        $update = PosTransactionDetail::where('id', '=', $ptd_id)->update([
            'pos_td_qty_pickup' => $ptd_qty
        ]);
        if (!empty($update)) {
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveStatus(Request $request)
    {
        $ptd_id = $request->_ptd_id;
        $check = PosTransactionDetail::select('pos_td_reject', 'pst_id', 'pl_id', 'pt_id')->where('id', '=', $ptd_id)->get()->first();
        $pls_id = ProductLocationSetup::select('id')->where('pst_id' , $check->pst_id)->where('pl_id' , $check->pl_id)->get()->first()->id;
        if ($check->pos_td_reject == '0') {
          $reject = '1';
          $update = ProductLocationSetupTransaction::where([
              'pls_id' => $pls_id,
              'pt_id' => $check->pt_id,
          ])->update([
              'plst_status' => 'INSTOCK',
              'u_id_packer' => Auth::user()->id,
          ]);
        } else {
          $reject = '0';
          $update = ProductLocationSetupTransaction::where([
              'pls_id' => $pls_id,
              'pt_id' => $check->pt_id,
          ])->update([
              'plst_status' => 'WAITING ONLINE',
          ]);
        }
        $update = PosTransactionDetail::where('id', '=', $ptd_id)->update([
          'pos_td_reject' => $reject
        ]);
        if (!empty($update)) {
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkPrint(Request $request)
    {
        $pt_id = $request->_pt_id;
        $check = PosTransactionDetail::where('pt_id', '=', $pt_id)
        ->where('pos_td_reject', '!=', '1')
        ->whereNull('pos_td_qty_pickup')->exists();
        if ($check) {
            $r['status'] = '400';
        } else {
            $check_pos = PosTransaction::where('id', '=', $pt_id)->where('pos_status', '=', 'CANCEL')->exists();
            if ($check_pos) {
                $pos = PosTransaction::where('id', '=', $pt_id)->where('pos_status', '=', 'CANCEL')->update([
                    'pos_status' => 'CANCEL'
                ]);
                ProductLocationSetupTransaction::where('pt_id', '=', $pt_id)->where('plst_status', '=', 'WAITING ONLINE')->update([
                    'plst_status' => 'INSTOCK'
                ]);
            } else {
                $pos = PosTransaction::where('id', '=', $pt_id)->where('pos_status', '!=', 'CANCEL')->update([
                    'pos_status' => 'SHIPPING NUMBER'
                ]);
                ProductLocationSetupTransaction::where('pt_id', '=', $pt_id)->where('plst_status', '=', 'WAITING ONLINE')->update([
                    'plst_status' => 'DONE'
                ]);
            }
            $pos_invoice = PosTransaction::select('pos_invoice')->where('id', '=', $pt_id)->get()->first()->pos_invoice;
            $r['invoice'] = $pos_invoice;
            $r['status'] = '200';
        }
        return json_encode($r);
    }

    public function printInvoice(Request $request)
    {
        $invoice = $request->invoice;
        $check = PosTransaction::where(['pos_invoice' => $invoice])->exists();
        $dropshipper = null;
        $customer = null;
        $cust_province = null;
        $cust_city = null;
        $cust_subdistrict = null;
        $transaction = null;
        $transaction_detail = null;
         $cust_province = '';
                  $cust_city = '';
                  $cust_subdistrict = '';

        if ($check) {
            $transaction = PosTransaction::select('pos_transactions.id as pt_id', 'cust_id', 'cust_province', 'cust_city', 'cust_subdistrict', 'sub_cust_id', 'u_name', 'pm_name', 'dv_name', 'pos_another_cost', 'pos_ref_number', 'pos_card_number', 'cust_name', 'cust_phone', 'cust_address', 'pos_invoice', 'pos_order_number', 'st_name', 'st_phone', 'st_address', 'pos_shipping', 'cr_id', 'pos_discount' , 'pos_transactions.created_at as pos_created')
            ->leftJoin('stores', 'stores.id', '=', 'pos_transactions.st_id')
            ->leftJoin('couriers', 'couriers.id', '=', 'pos_transactions.cr_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pos_transactions.pm_id')
            ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
            ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
            ->leftJoin('users', 'users.id', '=', 'pos_transactions.u_id')
            ->where(['pos_invoice' => $invoice])
            ->groupBy('pos_transactions.id')->get()->first();
            if (!empty($transaction)) {
                if (!empty($transaction->cust_id) AND !empty($transaction->sub_cust_id)) {
                    $dropshipper = Customer::select('cust_name', 'cust_address', 'cust_phone', 'cust_store', 'cust_province', 'cust_city', 'cust_subdistrict')->where('id', $transaction->cust_id)->get()->first();
                    $customer = Customer::select('cust_name', 'cust_address', 'cust_phone', 'cust_store', 'cust_province', 'cust_city', 'cust_subdistrict')->where('id', $transaction->sub_cust_id)->get()->first();
                } else {
                    $customer = Customer::select('cust_name', 'cust_address', 'cust_phone', 'cust_store', 'cust_province', 'cust_city', 'cust_subdistrict')->where('id', $transaction->cust_id)->get()->first();
                }
                if (!empty($customer->cust_subdistrict)) {
                  $cust_province = Wilayah::select('nama')->where('kode', $customer->cust_province)->get()->first()->nama;
                  $cust_city = Wilayah::select('nama')->where('kode', $customer->cust_city)->get()->first()->nama;
                  $cust_subdistrict = Wilayah::select('nama')->where('kode', $customer->cust_subdistrict)->get()->first()->nama;
                }
                $transaction_detail = PosTransactionDetail::
                leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where([
                  'pt_id' => $transaction->pt_id,
                  'pos_td_reject' => '0'
                ])->get();
            }
        }
        $data = [
            'title' => 'Invoice '.$invoice,
            'invoice' => $invoice,
            'dropshipper' => $dropshipper,
            'customer' => $customer,
            'cust_province'=> $cust_province,
            'cust_city' => $cust_city,
            'cust_subdistrict' => $cust_subdistrict,
            'transaction' => $transaction,
            'transaction_detail' => $transaction_detail,
            'segment' => request()->segment(1)
        ];
        return view('app.invoice.print_invoice', compact('data'));
    }

    public function updateData(Request $request)
    {
        $shipping_number = $request->pos_shipping_number;
        $pt_id = $request->_id;
        $courier = $request->courier;
        $update = PosTransaction::where('id', $pt_id)->update([
            'pos_shipping_number' => str_replace(' ', '', $shipping_number)
        ]);
        if (!empty($update)) {
            $check = PosShippingInformation::where('pt_id', $pt_id)->exists();
            $waybill = $this->shipmentracking(str_replace(' ', '', $shipping_number), $courier, $pt_id);
            if ($waybill['status'] == '200') {
                $description = $waybill['data']['summary']['status'];
                if ($description == 'DELIVERED') {
                    PosTransaction::where('id', $pt_id)
                    ->whereNotIn('pos_status', ['DONE', 'EXCHANGE', 'REFUND'])->update([
                        'pos_status' => 'DONE'
                    ]);
                } else {
                    PosTransaction::where('id', $pt_id)
                    ->whereNotIn('pos_status', ['DONE', 'EXCHANGE', 'REFUND'])->update([
                        'pos_status' => 'DONE'
                    ]);
                }
                if ($check) {
                    PosShippingInformation::where('pt_id', $pt_id)->update([
                        'psi_courier' => $courier,
                        'psi_description' => $description
                    ]);
                } else {
                    DB::table('pos_shipping_information')->insert([
                        'pt_id' => $pt_id,
                        'psi_courier' => $courier,
                        'psi_description' => $description
                    ]);
                }
                $r['status'] = '200';
            } else {
                PosTransaction::where('id', $pt_id)
                ->whereNotIn('pos_status', ['DONE', 'EXCHANGE', 'REFUND'])->update([
                    'pos_status' => 'DONE'
                ]);
                $r['status'] = '200';
            }
        } else {
            $r['status'] = '400';
        }
        return json_encode ($r);
    }

    public function waybillTracking(Request $request)
    {
        $waybill_number = $request->_waybill_number;
        $id = $request->_id;
        $courier = PosShippingInformation::select('psi_courier')
        ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_shipping_information.pt_id')->where('pos_shipping_number', '=', $waybill_number)->get()->first();
        if (!empty($courier)) {
            $waybill = $this->shipmentracking($waybill_number, $courier->psi_courier, $id);
        } else {
            $waybill = $this->shipmentracking($waybill_number, '', $id);
        }
        $trace = '';
        if ($waybill['status'] == '200') {
            $description = $waybill['data']['summary']['status'];
            $pt_id = PosTransaction::select('id')->where('pos_shipping_number', $waybill_number)->get()->first()->id;
            if ($description == 'DELIVERED') {
                PosTransaction::where('id', $pt_id)
                ->whereNotIn('pos_status', ['DONE', 'EXCHANGE', 'REFUND'])->update([
                    'pos_status' => 'DONE'
                ]);
            } else {
                PosTransaction::where('id', $pt_id)
                ->whereNotIn('pos_status', ['DONE', 'EXCHANGE', 'REFUND'])->update([
                    'pos_status' => 'DONE'
                ]);
            }
            PosShippingInformation::leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_shipping_information.pt_id')
            ->where('pos_shipping_number', '=', $waybill_number)->update([
                'psi_description' => $description
            ]);
            $trace .= '<table class="table table-hover table-checkable">';
            $trace .= '<tbody>';
            $trace .= '<tr>';
            $trace .= '<td>Resi</td><td>'.$waybill['data']['summary']['awb'].'</td>';
            $trace .= '</tr>';
            $trace .= '<tr>';
            $trace .= '<td>Pengirim</td><td>'.$waybill['data']['detail']['shipper'].'</td>';
            $trace .= '</tr>';
            $trace .= '<tr>';
            $trace .= '<td>Lokasi Pengirim</td><td>'.$waybill['data']['detail']['origin'].'</td>';
            $trace .= '</tr>';
            $trace .= '<tr>';
            $trace .= '<td>Penerima</td><td>'.$waybill['data']['detail']['receiver'].'</td>';
            $trace .= '</tr>';
            $trace .= '<tr>';
            $trace .= '<td>Tujuan</td><td>'.$waybill['data']['detail']['destination'].'</td>';
            $trace .= '</tr>';
            $trace .= '<tr>';
            $trace .= '<td>Status</td><td>'.$waybill['data']['summary']['status'].'</td>';
            $trace .= '</tr>';
            $trace .= '</tbody>';

            $trace .= '<table class="table table-hover table-checkable">';
            $trace .= '<thead>';
            $trace .= '<th>Tanggal/Waktu</th>';
            $trace .= '<th>Deskripsi</th>';
            $trace .= '<th>Lokasi</th>';
            $trace .= '</thead>';
            $trace .= '<tbody>';
            foreach ($waybill['data']['history'] as $row) {
                $trace .= '<tr>';
                $trace .= '<td style="white-space:nowrap;">'.date('d/m/Y H:i:s', strtotime($row['date'])).'</td><td>'.$row['desc'].'</td><td style="white-space:nowrap;">'.$row['location'].'</td>';
                $trace .= '</tr>';
            }
            $trace .= '</tbody>';
            $trace .= '</table>';
            echo $trace;
        } else {
            echo "Terjadi error, atau resi kurir belum support";
        }
    }

    private function shipmentracking($shipping_number, $courier, $id)
	{
        $binderbyte_api = DB::table('web_configs')->select('config_value')
        ->where('config_name', 'binderbyte_api')->first()->config_value;
        
        if (!empty($courier)) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.binderbyte.com/v1/track?api_key=$binderbyte_api&courier=$courier&awb=$shipping_number",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            } else {
                return json_decode($response, true);
            }
        } else {
            $arr = ['jne', 'pos', 'jnt', 'sicepat', 'tiki', 'anteraja', 'wahana', 'ninja', 'lion', 'pcp', 'jet', 'rex', 'sap', 'jxe', 'rpx', 'first', 'ide', 'spx', 'kgx'];
            for ($i = 0; $i < count($arr); $i ++) {$status = null;
                $status = null;
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.binderbyte.com/v1/track?api_key=$binderbyte_api&courier=$arr[$i]&awb=$shipping_number",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET"));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    $status = 'error';
                } else {
                    $status = json_decode($response, true);
                }
                if ($status != 'error') {
                    if ($status['status'] == '200') {
                        $description = $status['data']['summary']['status'];
                        DB::table('pos_shipping_information')->insert([
                            'pt_id' => $id,
                            'psi_courier' => $arr[$i],
                            'psi_description' => $description
                        ]);
                        return json_decode($response, true);
                        break;
                    }
                } else {
                    return '400';
                }
            }
        }
	}
}
