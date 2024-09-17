<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductLocationSetup;
use App\Models\ProductStock;
use App\Models\ProductLocationSetupTransaction;
use App\Models\ProductLocationSetupTransactionStatus;
use App\Models\PosTransaction;
use App\Models\ProductSubCategory;
use App\Models\Store;
use App\Models\StoreTypeDivision;
use App\Models\UserActivity;

class StockTrackingController extends Controller
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
            'segment' => request()->segment(1),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'std_id' => StoreTypeDivision::where('dv_delete', '!=', '1')->orderBy('dv_name')->pluck('dv_name', 'id'),
            'psc_id' => ProductSubCategory::where('psc_delete', '!=', '1')->orderByDesc('id')->pluck('psc_name', 'id'),
            'br_id' => DB::table('brands')->where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
        ];
        return view('app.stock_tracking.stock_tracking', compact('data'));
    }

    public function deleteData(Request $request) {
        $id = $request->post('id');
        $delete = DB::table('product_location_setup_transactions')->where('id', '=', $id)->delete();
        if (!empty($delete)) {
            $r['status'] = 200;
        } else {
            $r['status'] = 400;
        }
        return json_encode($r);
    }

    public function getDatatables(Request $request)
    {
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = Auth::user()->st_id;
        }
        $date = $request->get('date');
        $start = null;
        $end = null;
        $exp = explode('|', $date);
        $total = count($exp);
        if ($total > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            if (!empty($date)) {
                $start = $date;
            } else {
                $start = date('Y-m-d');
            }
        }
        if(request()->ajax()) {
            return datatables()->of(DB::table('product_location_setup_transactions')->select('product_location_setup_transactions.id as plst_id', 'pos_invoice', 'rt_id', 'pt_id', 'pt_id_ref', 'pos_transactions.stt_id', 'pos_note', 'is_website', 'pos_transactions.u_id as pos_user', 'product_location_setup_transactions.u_id as u_id', 'cross_order', 'u_id_helper', 'u_id_packer', 'u_id_refund', 'p_price_tag', 'ps_price_tag', 'p_sell_price', 'ps_sell_price', 'pt_id', 'br_name', 'cust_name', 'u_name', 'p_name', 'p_color', 'sz_name', 'pl_code', 'pl_name', 'pl_description', 'plst_status', 'plst_qty', 'product_location_setup_transactions.created_at as plst_created', 'product_location_setup_transactions.updated_at as plst_updated')
            ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'product_location_setup_transactions.pt_id')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('users', 'users.id', '=', 'product_location_setup_transactions.u_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where('product_locations.st_id', '=', $st_id)
            ->where(function($w) use ($start, $end) {
                if (!empty($end)) {
                    $w->whereDate('product_location_setup_transactions.created_at', '>=', $start)
                    ->whereDate('product_location_setup_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('product_location_setup_transactions.created_at', '=', $start);
                }
            }))
            ->editColumn('datetime', function($data){
                return '<span style="white-space: nowrap; font-weight:bold;">'.date('d/m/Y H:i:s', strtotime($data->plst_created)).'</span>';
            })
            ->editColumn('article', function($data){
                $price_tag = 0;
                $sell_price = 0;
                if (!empty($data->ps_price_tag)) {
                    $price_tag = $data->ps_price_tag;
                } else {
                    $price_tag = $data->p_price_tag;
                }
                if (!empty($data->ps_sell_price)) {
                    $sell_price = $data->ps_sell_price;
                } else {
                    $sell_price = $data->p_sell_price;
                }

                $backend = $data->plst_id;
                $picker = null;
                $cashier = null;
                $customer = null;
                $helper = null;
                $packer = null;
                $invoice = null;
                $updated = null;
                $pos_note = null;
                $refund_exchange_note = null;

                if (!empty($data->pos_invoice)) {
                    $invoice = $data->pos_invoice;
                }
                $article = '['.$data->br_name.'] '.$data->p_name.' '.$data->p_color.' ['.$data->sz_name.'] | '.$price_tag.' | '.$sell_price;
                // picker
                if (!empty($data->u_name)) {
                    $picker = strtoupper($data->u_name);
                }
                // kasir
                if (!empty($data->pos_user)) {
                    $cashier = strtoupper(User::select('u_name')->where('id', $data->pos_user)->get()->first()->u_name);
                }
                // customer
                $customer = strtoupper($data->cust_name);
                // helper
                if (!empty($data->u_id_helper)) {
                    $helper = strtoupper(User::select('u_name')->where('id', $data->u_id_helper)->get()->first()->u_name);
                }
                // packer
                if (!empty($data->u_id_packer)) {
                    $packer = strtoupper(User::select('u_name')->where('id', $data->u_id_packer)->get()->first()->u_name);
                }
                if (!empty($data->u_id_refund)) {
                    $u_name = User::select('u_name')->where('id', $data->u_id_refund)->get()->first();
                    $pos_invoice = PosTransaction::select('pos_invoice')->where('pt_id_ref', '=', $data->pt_id)->get()->first();
                    if (!empty($u_name)) {
                        if (!empty($pos_invoice->pos_invoice)) {
                        $refund_exchange_note = "Telah direfund/exchange oleh ".$u_name->u_name." dengan invoice ".$pos_invoice->pos_invoice;
                        }
                    }
                }
                if (!empty($data->plst_updated) AND $data->plst_updated != '0000-00-00') {
                    $updated = date('d/m/Y H:i:s', strtotime($data->plst_updated));
                }
                if (!empty($data->pos_note)) {
                    $pos_note = $data->pos_note;
                }
                return '<span style="white-space: nowrap; text-align:left; font-weight:bold;" updated="'.$updated.'" pos_note="'.$pos_note.'" backend="'.$backend.'" refund_exchange_note="'.$refund_exchange_note.'" status="'.$data->plst_status.'" invoice="'.$invoice.'" picker="'.$picker.'" cashier="'.$cashier.'" customer="'.$customer.'" helper="'.$helper.'" packer="'.$packer.'" class="btn btn-sm btn-primary" id="user_detail_btn">'.$article.'</span>';
            })
            ->editColumn('invoice', function($data){
                if (!empty($data->pos_invoice)) {
                    if ($data->cross_order != '1') {
                        if ($data->stt_id == '2') {
                            if (!empty($data->pt_id_ref)) {
                                return '<span class="btn btn-sm btn-warning"><a class="text-white" href="'.url('').'/print_offline_invoice/'.$data->pos_invoice.'" target="_blank" style="white-space:nowrap; font-weight:bold;">'.$data->pos_invoice.'</a></span>';
                            } else {
                                return '<span class="btn btn-sm btn-primary"><a class="text-white" href="'.url('').'/print_offline_invoice/'.$data->pos_invoice.'" target="_blank" style="white-space:nowrap; font-weight:bold;">'.$data->pos_invoice.'</a></span>';
                            }
                        } else {
                            if ($data->is_website == '1') {
                                return '<span class="btn btn-sm btn-success" style="white-space:nowrap; font-weight:bold;"><a class="text-white" style="white-space:nowrap;">'.$data->pos_invoice.'</a></span>';
                            } else {
                                if (!empty($data->pt_id_ref)) {
                                    return '<span class="btn btn-sm btn-warning" style="white-space:nowrap; font-weight:bold;"><a class="text-white" href="'.url('').'/print_invoice/'.$data->pos_invoice.'" target="_blank" style="white-space:nowrap;">'.$data->pos_invoice.'</a></span>';
                                } else {
                                    return '<span class="btn btn-sm btn-primary" style="white-space:nowrap; font-weight:bold;"><a class="text-white" href="'.url('').'/print_invoice/'.$data->pos_invoice.'" target="_blank" style="white-space:nowrap;">'.$data->pos_invoice.'</a></span>';
                                }
                            }
                        }
                    } else {
                        return '<span class="btn btn-sm btn-info" style="white-space:nowrap; font-weight:bold;"><a class="text-white" href="'.url('').'/print_invoice/'.$data->pos_invoice.'" target="_blank" style="white-space:nowrap;">'.$data->pos_invoice.'</a></span>';
                    }
                } else {
                    if (!empty($data->rt_id)) {
                        $rt_invoice = DB::table('reseller_transactions')->select('rt_invoice')->where('id', '=', $data->rt_id)->first();
                        if (!empty($rt_invoice)) {
                            return '<span class="btn btn-sm btn-success" style="white-space:nowrap; font-weight:bold;"><a class="text-white" style="white-space:nowrap;">'.$rt_invoice->rt_invoice.'</a></span>';
                        } else {
                            return '-';
                        }
                    } else {
                        return '-';
                    }
                }
            })
            ->editColumn('qty', function($data){
                return '<span class="btn btn-sm btn-primary">'.$data->plst_qty.'</span>';
            })
            ->editColumn('product_location', function($data){
                return '<span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm btn-primary" title="['.$data->pl_name.'] '.$data->pl_description.'">['.$data->pl_code.']</span>';
            })
            ->editColumn('status', function($data){
                $btn = '';
                if ($data->plst_status == 'WAITING OFFLINE') {
                    $btn = 'btn-warning';
                    $u_name = '';
                }
                if ($data->plst_status == 'REJECT') {
                    $btn = 'btn-danger';
                    $u_name = '';
                }
                if ($data->plst_status == 'WAITING ONLINE') {
                    $btn = 'btn-light-warning';
                    $u_name = '';
                }
                if ($data->plst_status == 'WAITING FOR PACKING') {
                    $btn = 'btn-info';
                    $u_name = '';
                }
                if ($data->plst_status == 'WAITING FOR CHECKOUT') {
                    $btn = 'btn-info';
                    $u_name = '';
                }
                if ($data->plst_status == 'WAITING TO TAKE') {
                    $btn = 'btn-info';
                    $u_name = '';
                }
                if ($data->plst_status == 'WAITING FOR CONFIRMATION') {
                    $btn = 'btn-warning';
                    $u_name = '';
                }
                if ($data->plst_status == 'WAITING FOR NAMESET') {
                    $btn = 'btn-info';
                    $u_name = '';
                }
                if ($data->plst_status == 'DRAFT OFFLINE') {
                    $btn = 'btn-warning';
                    $u_name = '';
                }
                if ($data->plst_status == 'COMPLAINT') {
                    $btn = 'btn-danger';
                    $u_name = '';
                }
                if ($data->plst_status == 'EXCHANGE') {
                    $btn = 'btn-danger';
                    $u_name = '';
                }
                if ($data->plst_status == 'DONE') {
                    $btn = 'btn-success';
                    $u_name = '';
                }
                if ($data->plst_status == 'REFUND') {
                    $btn = 'btn-danger';
                    $u_name = '';
                }
                if ($data->plst_status == 'INSTOCK') {
                    $btn = 'btn-primary';
                    $u_name = '';
                }
                if ($data->plst_status == 'INSTOCK APPROVAL') {
                    $btn = 'btn-info';
                    $u_name = '';
                }
                if ($data->plst_status == 'CANCEL') {
                    $btn = 'btn-danger';
                    $u_name = '';
                }
                return '<span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm '.$btn.'">'.$data->plst_status.'</span>';
            })
            ->rawColumns(['article', 'invoice', 'qty', 'product_location', 'datetime', 'status'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('status'))) {
                    $instance->where('plst_status', $request->get('status'));
                }
                if (!empty($request->get('br_id'))) {
                    $instance->where('products.br_id', $request->get('br_id'));
                }
                if (!empty($request->get('psc_id'))) {
                    $instance->where('products.psc_id', $request->get('psc_id'));
                }
                if (!empty($request->get('std_id'))) {
                    $instance->where('pos_transactions.std_id', $request->get('std_id'));
                }
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%")
                        ->orWhere('cust_name', 'LIKE', "%$search%")
                        ->orWhere('pos_invoice', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getPickupDatatables(Request $request)
    {
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = Auth::user()->st_id;
        }
        $status = array();
        $status = ['WAITING TO TAKE', 'INSTOCK APPROVAL'];
        if(request()->ajax()) {
            return datatables()->of(ProductLocationSetupTransaction::select('product_location_setup_transactions.id as plst_id', 'plst_qty', 'plst_status', 'pls_id', 'pst_id', 'pl_id','u_name', 'p_name', 'p_color', 'sz_name', 'pl_code', 'pl_name', 'pl_description', 'product_location_setup_transactions.created_at as plst_created')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('users', 'users.id', '=', 'product_location_setup_transactions.u_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->whereIn('plst_status', $status)
            ->where('product_locations.st_id', '=', $st_id)
            ->where('users.stt_id', '=', Auth::user()->stt_id))
            ->editColumn('article', function($data){
                return '<span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm btn-primary">'.$data->p_name.' '.$data->p_color.' ['.$data->sz_name.']</span>';
            })
            ->editColumn('qty', function($data){
                return $data->plst_qty;
            })
            ->editColumn('bin', function($data){
                if (!empty($data->pl_description)) {
                    return '<span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm btn-primary">['.$data->pl_code.'] '.$data->pl_name.' '.$data->pl_description.'</span>';
                } else {
                    return '<span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm btn-primary">['.$data->pl_code.'] '.$data->pl_name.'</span>';
                }
            })
            ->editColumn('datetime', function($data){
                return '<span style="white-space: nowrap;">'.date('d-m-Y H:i:s', strtotime($data->plst_created)).'</span>';
            })
            ->editColumn('user', function($data){
                return '<span style="white-space: nowrap;">'.strtoupper($data->u_name).'</span>';
            })
            ->editColumn('status', function($data){
                if ($data->plst_status == 'WAITING OFFLINE') {
                    $btn = 'btn-warning';
                } else if ($data->plst_status == 'WAITING TO TAKE') {
                    $btn = 'btn-info';
                } else {
                    $btn = 'btn-light-success';
                }
                return '<span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm '.$btn.'">'.$data->plst_status.'</span>';
            })
            ->editColumn('action', function($data){
                if ($data->plst_status == 'INSTOCK APPROVAL') {
                    return '<a class="btn btn-sm btn-success" data-p_name="'.$data->p_name.' '.$data->p_color.' '.$data->sz_name.'" data-plst_id="'.$data->plst_id.'" data-pls_id="'.$data->pls_id.'" data-pst_id="'.$data->pst_id.'" data-bin="'.$data->pl_code.'" id="pickup_approval_item">PickUp</a>';
                } else {
                    return '<a class="btn btn-sm btn-danger" data-p_name="'.$data->p_name.' '.$data->p_color.' '.$data->sz_name.'" data-plst_id="'.$data->plst_id.'" data-pls_id="'.$data->pls_id.'" data-pst_id="'.$data->pst_id.'" data-pl_code="'.$data->pl_code.'" data-pl_id="'.$data->pl_id.'" id="cancel_pickup_btn">Batal</a>';
                }
            })
            ->rawColumns(['article', 'qty', 'bin', 'datetime', 'user', 'status', 'action'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function pickupApprovalItem(Request $request) {
        $plst_id = $request->post('plst_id');

        $update = DB::table('product_location_setup_transactions')
        ->where('id', '=', $plst_id)
        ->where('plst_status', '=', 'INSTOCK APPROVAL')->update([
            'plst_status' => 'WAITING TO TAKE',
            'is_approval' => '1'
        ]);

        if (!empty($update)) {
            $r['status'] = 200;
        } else {
            $r['status'] = 400;
        }
        return json_encode($r);
    }

    public function pickupItem(Request $request)
    {
        $pls_id = $request->_pls_id;
        $pst_id = $request->_pst_id;
        $pl_id = $request->_pl_id;
        $pl_code = $request->_pl_code;
        $pls = ProductLocationSetup::select('pst_id', 'pls_qty')->where('id', $pls_id)->get()->first();

//        if (!empty($pls->pls_qty)) {
            $update = DB::table('product_location_setups')->where('id', $pls_id)->update([
                'pls_qty' => ($pls->pls_qty-1),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
//        }
        if (!empty($update)) {
            $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where('product_stocks.id', $pst_id)
            ->get()->first();
            $this->UserActivity('melakukan pickup ['.$item->br_name.'] '.$item->p_name.' '.$item->p_color.' '.$item->sz_name.' pada BIN '.$pl_code);

            $st_user = Auth::user()->st_id;
            $st_name = Store::select('st_name', 'st_code')->where('id', $st_user)->first();
            $st_city = $st_name->st_code;

            if (strtoupper($pl_code) == 'TOKO' && stripos($st_name->st_name, 'ONLINE') === true) {
                $insert = DB::table('product_location_setup_transactions')->insert([
                    'pls_id' => $pls_id,
                    'u_id' => Auth::user()->id,
                    'plst_qty' => '1',
                    'plst_type' => 'OUT',
                    'plst_status' => 'WAITING OFFLINE',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                if ($st_name && stripos($st_name->st_name, 'ONLINE') === false) {
                    $insert = DB::table('product_location_setup_transactions')->insert([
                        'pls_id' => $pls_id,
                        'u_id' => Auth::user()->id,
                        'plst_qty' => '1',
                        'plst_type' => 'OUT',
                        'plst_status' => 'WAITING TO TAKE',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    $insert = DB::table('product_location_setup_transactions')->insert([
                        'pls_id' => $pls_id,
                        'u_id' => Auth::user()->id,
                        'plst_qty' => '1',
                        'plst_type' => 'OUT',
                        'plst_status' => 'WAITING ONLINE',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
            if (!empty($insert)) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function cancelPickupItem(Request $request)
    {
        $plst_id = $request->_plst_id;
        $pls_id = $request->_pls_id;
        $pst_id = $request->_pst_id;
        $pl_id = $request->_pl_id;
        $pl_code = $request->_pl_code;

        $is_approval = DB::table('product_location_setup_transactions')
        ->where('id', '=', $plst_id)
        ->where('is_approval', '=', '1')->first();
        if (!empty($is_approval)) {
            $update = DB::table('product_location_setup_transactions')
            ->where('id', '=', $plst_id)
            ->update([
                'plst_status' => 'INSTOCK APPROVAL',
                'is_approval' => '0'
            ]);
            $r['status'] = 200;
            return json_encode($r);
        }


        $pls = ProductLocationSetup::select('pst_id', 'pls_qty')->where('id', $pls_id)->get()->first();
        $update = DB::table('product_location_setups')->where('id', $pls_id)->update([
            'pls_qty' => ($pls->pls_qty+1),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        if (!empty($update)) {
            $update_plst = DB::table('product_location_setup_transactions')
            ->where('id', $plst_id)->where('pls_id', $pls_id)
            ->whereIn('plst_status', ['WAITING TO TAKE', 'WAITING ONLINE', 'WAITING OFFLINE', 'EXCHANGE', 'REFUND'])->update([
                'u_id' => Auth::user()->id,
                'plst_type' => 'IN',
                'plst_status' => 'INSTOCK',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where('product_stocks.id', $pst_id)
            ->get()->first();
            $this->UserActivity('membatalkan pickup ['.$item->br_name.'] '.$item->p_name.' '.$item->p_color.' '.$item->sz_name.' pada BIN '.$pl_code);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function getNotice(Request $request)
    {
        $st_id = $request->post('st_id');

        $plst = DB::table('product_location_setup_transactions')
        ->select('plst_status', 'product_location_setup_transactions.created_at')
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->where('product_locations.st_id', '=', $st_id)
        ->groupBy('product_location_setup_transactions.id')->get();
        $problem_arr = array();
        $offline_arr = array();
        $online_arr = array();
        $problem = 0;
        $offline = 0;
        $online = 0;
        if (!empty($plst->first())) {
            foreach ($plst as $row) {
                if ($row->plst_status == 'WAITING ONLINE') {
                    $online_arr[] = [
                        'plst_status' => $row->plst_status,
                        'created_at' => $row->created_at
                    ];
                }
                if ($row->plst_status == 'WAITING OFFLINE') {
                    $offline_arr[] = [
                        'plst_status' => $row->plst_status,
                        'created_at' => $row->created_at
                    ];
                }
                if ($row->plst_status == 'EXCHANGE' || $row->plst_status == 'REFUND' || $row->plst_status == 'COMPLAINT') {
                    $problem_arr[] = [
                        'plst_status' => $row->plst_status,
                        'created_at' => $row->created_at
                    ];
                }
            }
            asort($online_arr);
            asort($offline_arr);
            asort($problem_arr);
        }
        $r['offline'] = count($offline_arr);
        if (!empty(count($offline_arr))) {
            if ($r['offline'] > 1) {
                $r['offline_notice'] = 'terdapat waiting offline pada range tanggal '.date('d M Y', strtotime($offline_arr[0]['created_at'])).' hingga '.date('d M Y', strtotime($offline_arr[($r['offline']-1)]['created_at']));
            } else {
                $r['offline_notice'] = 'terdapat waiting offline pada range tanggal '.date('d M Y', strtotime($offline_arr[0]['created_at']));
            }
        }
        $r['online'] = count($online_arr);
        if (!empty(count($online_arr))) {
            if ($r['online'] > 1) {
                $r['online_notice'] = 'terdapat waiting online pada range tanggal '.date('d M Y', strtotime($online_arr[0]['created_at'])).' hingga '.date('d M Y', strtotime($online_arr[($r['online']-1)]['created_at']));
            } else {
                $r['online_notice'] = 'terdapat waiting online pada range tanggal '.date('d M Y', strtotime($online_arr[0]['created_at']));
            }
        }
        $r['problem'] = count($problem_arr);
        if (!empty(count($problem_arr))) {
            if ($r['problem'] > 1) {
                $r['problem_notice'] = 'terdapat refund/exchange/complaint pada range tanggal '.date('d M Y', strtotime($problem_arr[0]['created_at'])).' hingga '.date('d M Y', strtotime($problem_arr[($r['problem']-1)]['created_at']));
            } else {
                $r['problem_notice'] = 'terdapat refund/exchange/complaint pada range tanggal '.date('d M Y', strtotime($problem_arr[0]['created_at']));
            }
        }
        $r['status'] = '200';
        return json_encode($r);
    }

    public function getGraph(Request $request)
    {
        $st_id = $request->post('st_id');
        $date = $request->post('date');
        $label = $request->post('label');
        $start = null;
        $end = null;
        $exp = explode('|', $date);
        $total = count($exp);
        if ($total > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            if (!empty($date)) {
                $start = $date;
            } else {
                $start = date('Y-m-d');
            }
        }

        if ($label == 'offline') {
            $offline_item = DB::table('product_location_setup_transactions')
            ->selectRaw("br_name, sum(ts_product_location_setup_transactions.plst_qty) as total")
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('product_locations.st_id', '=', $st_id)
            ->where(function($w) use ($start, $end) {
                if (!empty($end)) {
                    $w->whereDate('product_location_setup_transactions.created_at', '>=', $start)
                    ->whereDate('product_location_setup_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('product_location_setup_transactions.created_at', '=', $start);
                }
                $w->where('product_location_setup_transactions.plst_status', '=', 'WAITING OFFLINE');
            })
            ->having('total', '>', '0')
            ->groupBy('brands.br_name')->get();
            $data = [
                'label' => $label,
                'offline_item' => $offline_item,
            ];
            return view('app.stock_tracking._load_graph', compact('data'));
        }
        if ($label == 'online') {
            $online_item = DB::table('product_location_setup_transactions')
            ->selectRaw("br_name, sum(ts_product_location_setup_transactions.plst_qty) as total")
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('product_locations.st_id', '=', $st_id)
            ->where(function($w) use ($start, $end) {
                if (!empty($end)) {
                    $w->whereDate('product_location_setup_transactions.created_at', '>=', $start)
                    ->whereDate('product_location_setup_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('product_location_setup_transactions.created_at', '=', $start);
                }
                $w->where('product_location_setup_transactions.plst_status', '=', 'WAITING ONLINE');
            })
            ->having('total', '>', '0')
            ->groupBy('brands.br_name')->get();
            $data = [
                'label' => $label,
                'online_item' => $online_item,
            ];
            return view('app.stock_tracking._load_graph', compact('data'));
        }
        if ($label == 'instock') {
            $instock_item = DB::table('product_location_setup_transactions')
            ->selectRaw("br_name, sum(ts_product_location_setup_transactions.plst_qty) as total")
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('product_locations.st_id', '=', $st_id)
            ->where(function($w) use ($start, $end) {
                if (!empty($end)) {
                    $w->whereDate('product_location_setup_transactions.created_at', '>=', $start)
                    ->whereDate('product_location_setup_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('product_location_setup_transactions.created_at', '=', $start);
                }
                $w->where('product_location_setup_transactions.plst_status', '=', 'INSTOCK')
                ->whereNull('product_location_setup_transactions.pt_id');
            })
            ->having('total', '>', '0')
            ->groupBy('brands.br_name')->get();
            $data = [
                'label' => $label,
                'instock_item' => $instock_item,
            ];
            return view('app.stock_tracking._load_graph', compact('data'));
        }
        if ($label == 'sales_instock') {
            $sales_instock_item = DB::table('product_location_setup_transactions')
            ->selectRaw("br_name, sum(ts_product_location_setup_transactions.plst_qty) as total")
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('product_locations.st_id', '=', $st_id)
            ->where(function($w) use ($start, $end) {
                if (!empty($end)) {
                    $w->whereDate('product_location_setup_transactions.created_at', '>=', $start)
                    ->whereDate('product_location_setup_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('product_location_setup_transactions.created_at', '=', $start);
                }
                $w->where('product_location_setup_transactions.plst_status', '=', 'INSTOCK')
                ->whereNotNull('product_location_setup_transactions.pt_id');
            })
            ->having('total', '>', '0')
            ->groupBy('brands.br_name')->get();
            $data = [
                'label' => $label,
                'sales_instock_item' => $sales_instock_item,
            ];
            return view('app.stock_tracking._load_graph', compact('data'));
        }

        if ($label == 'sales') {
            $sales_instock_item = DB::table('product_location_setup_transactions')
            ->selectRaw("br_name, sum(ts_product_location_setup_transactions.plst_qty) as total")
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('product_locations.st_id', '=', $st_id)
            ->where(function($w) use ($start, $end) {
                if (!empty($end)) {
                    $w->whereDate('product_location_setup_transactions.created_at', '>=', $start)
                    ->whereDate('product_location_setup_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('product_location_setup_transactions.created_at', '=', $start);
                }
                $w->where('product_location_setup_transactions.plst_status', '=', 'DONE')
                ->whereNotNull('product_location_setup_transactions.pt_id');
            })
            ->having('total', '>', '0')
            ->groupBy('brands.br_name')->get();
            $data = [
                'label' => $label,
                'sales_item' => $sales_instock_item,
            ];
            return view('app.stock_tracking._load_graph', compact('data'));
        }
    }
}