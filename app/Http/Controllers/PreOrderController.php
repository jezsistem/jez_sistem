<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Brand;
use App\Models\MainColor;
use App\Models\PreOrder;
use App\Models\PreOrderArticle;
use App\Models\PreOrderArticleDetails;
use App\Models\ProductLocationSetup;
use App\Models\ProductSubCategory;
use App\Models\ProductSupplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderArticle;
use App\Models\PurchaseOrderArticleDetail;
use App\Models\PurchaseOrderArticleDetailStatus;
use App\Models\Season;
use App\Models\Size;
use App\Models\StockType;
use App\Models\Store;
use App\Models\Tax;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreOrderController extends Controller
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
            'ps_id' => ProductSupplier::where('ps_delete', '!=', '1')->orderByDesc('id')->pluck('ps_name', 'id'),
            'st_id' => Store::selectRaw('ts_stores.id as sid, CONCAT(st_name) as store')
                ->where('st_delete', '!=', '1')
                ->orderByDesc('sid')->pluck('store', 'sid'),
            'br_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
            'br_po_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
            'mc_id' => MainColor::where('mc_delete', '!=', '1')->orderByDesc('id')->pluck('mc_name', 'id'),
            'sz_id' => Size::where('sz_delete', '!=', '1')->orderByDesc('id')->pluck('sz_name', 'id'),
            'stkt_id' => StockType::where('stkt_delete', '!=', '1')->orderByDesc('id')->pluck('stkt_name', 'id'),
            'tax_id' => Tax::where('tx_delete', '!=', '1')->orderByDesc('id')->pluck('tx_code', 'id'),
            'psc_id' => ProductSubCategory::where('psc_delete', '!=', '1')->orderByDesc('id')->pluck('psc_name', 'id'),
            'acc_id' => Account::where('a_delete', '!=', '1')->orderByDesc('id')->pluck('a_code', 'id'),
            'ss_id' => Season::where('ss_delete', '!=', '1')->orderByDesc('id')->pluck('ss_name', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.pre_order.pre_order', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        $st_id = $request->st_id;

        $user = new User;
        $select = ['u_name', 'u_email', 'u_phone', 'g_name'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        if(request()->ajax()) {
            return datatables()->of(PreOrder::select('pre_orders.id as po_id', 'st_name', 'ps_name', 'pre_order_code', 'po_draft', 'pre_orders.created_at as po_created_at')
                ->leftJoin('pre_order_articles', 'pre_order_articles.po_id', '=', 'pre_orders.id')
                ->leftJoin('products', 'products.id', '=', 'pre_order_articles.pr_id')
                ->join('stores', 'stores.id', '=', 'pre_orders.st_id')
                ->join('product_suppliers', 'product_suppliers.id', '=', 'pre_orders.ps_id')
                ->where('po_delete', '!=', '1')
                ->where(function($w) use ($user_data, $st_id) {
                    if ($user_data->g_name != 'administrator') {
                        $w->where('pre_orders.st_id', '=', Auth::user()->st_id);
                    } else {
                        if (!empty($st_id)) {
                            $w->where('pre_orders.st_id', '=',$st_id);
                        }
                    }
                })
                ->groupBy('po_id'))
                ->editColumn('po_created_at_show', function($data){
                    return date('d/m/Y H:i:s', strtotime($data->po_created_at));
                })
                ->editColumn('po_code', function($data){
                    return '#'.$data->po_code;
                })
                ->editColumn('po_total', function($data){
                    $poa = PreOrderArticle::where(['po_id' => $data->po_id])->get();
                    if (!empty($poa)) {
                        $total_price = 0;
                        foreach ($poa as $poa_row) {
                            $poad = PreOrderArticleDetails::where(['poa_id' => $poa_row->id])->get();
                            if (!empty($poad)) {
                                foreach ($poad as $poad_row) {
                                    $total_price += $poad_row->poad_total_price;
                                }
                            }
                        }
                    }
                    return number_format($total_price);
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function($w) use($request){
                            $search = $request->get('search');
                            $w->orWhere('pre_order_code', 'LIKE', "%$search%")
                                ->orWhere('st_name', 'LIKE', "%$search%")
                                ->orWhere('ps_name', 'LIKE', "%$search%")
                                ->orWhereRaw('CONCAT(p_name," ",p_color) LIKE ?', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function createPreOrder()
    {
        $po_id = DB::table('pre_orders')->insertGetId([
            'pre_order_code' => $this->generatePoInvoice(),
            'po_draft' => '0',
            'created_at' => date('Y-m-d H:i:s'),
            'po_delete' => '0',
        ]);
        if (!empty($po_id)) {
            $r['status'] = '200';
            $r['po_id'] = $po_id;
            $r['pre_order_code'] = DB::table('pre_orders')->select('pre_order_code')->where(['id' => $po_id])->get()->first()->pre_order_code;
            $this->UserActivity('membuat Pre Order '.$r['pre_order_code']);
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function createPreOrderDetail(Request  $request)
    {
        $poid = $request->_poid;
        $poaid = $request->_poaid;
        $prid = $request->_pid;
        $psid = $request->_psid;
        $status = $request->_status;

        $check_poa = PreOrderArticle::where(['po_id' => $poid, 'pr_id' => $prid])->exists();

        if (!$check_poa) {
            $poa_id = DB::table('pre_order_articles')->insertGetId([
               'po_id' => $poid,
                'pr_id' => $prid,
            ]);
        } else {
            $poa_id = DB::table('pre_order_articles')
                ->select('id')
                ->where(['po_id' => $poid, 'pr_id' => $prid])
                ->get()->first()->id;
        }

        $check_poad = DB::table('pre_order_article_details')
            ->where(['poa_id' => $poa_id, 'pst_id' => $psid])
            ->exists();

        if (!$check_poad) {
            $status_poad = DB::table('pre_order_article_details')->insert([
                'poa_id' => $poa_id,
                'pst_id' => $psid,
                'poad_draft' => '1',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($status_poad) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } else {
            $r['status'] = '400';
        }

        return json_encode($r);
    }

    public function checkPreOrderDetail(Request $request)
    {
        try{
            $po_id = $request->_po_id;

            if (!empty($po_id)) {
                $check = PreOrder::where(['id' => $po_id])->exists();
            } else {
                $check = PreOrder::where(['po_draft' => '1'])->exists();
            }

            if ($check) {
                if (!empty($po_id)) {
                    $draft = PreOrder::where(['id' => $po_id])->get()->first();
                } else {
                    $draft = PreOrder::where(['po_draft' => $po_id])->get()->first();
                }

                $po_id = $draft->id;
                $po_st_id = $draft->st_id;

                $poa_data = PreOrderArticle::select('pre_order_articles.id as poa_id', 'po_id', 'products.id as pid', 'br_name', 'p_price_tag', 'p_purchase_price', 'p_name', 'p_color')
                    ->leftJoin('products', 'products.id', '=', 'pre_order_articles.pr_id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                    ->where(['po_id' => $po_id])->get();

                if (!empty($poa_data)) {
                    $get_product = array();

                    foreach ($poa_data as $poa) {
                        $poad_data = PreOrderArticleDetails::select('pre_order_article_details.id as poad_id', 'sz_name', 'ps_qty', 'ps_running_code', 'ps_sell_price', 'ps_price_tag', 'ps_purchase_price', 'poad_qty', 'poad_purchase_price', 'poad_total_price', 'pst_id')
                            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pre_order_article_details.pst_id')
                            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                            ->where(['poa_id' => $poa->poa_id])->get();

                        // Step 2: Retrieve pls_qty from product_location_setups
                        $pstIds = $poad_data->pluck('pst_id'); // Get all unique pst_ids from the $poad_data

                        $plsQtyData = ProductLocationSetup::whereIn('pst_id', $pstIds)
                            ->select('pst_id', DB::raw('SUM(pls_qty) as total_pls_qty'))
                            ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                            ->join('stores', 'stores.id', '=', 'product_locations.st_id')
                            ->where(['stores.id' => $po_st_id])
                            ->groupBy('pst_id')
                            ->get();

                        // Step 3: Merge data with $poad_data
                        $poad_data = $poad_data->map(function ($item) use ($plsQtyData) {
                            $item['total_pls_qty'] = $plsQtyData->where('pst_id', $item['pst_id'])->first()['total_pls_qty'] ?? 0;
                            return $item;
                        });

                        if (!empty($poad_data)) {
                            $poa->subitem = $poad_data;
                            array_push($get_product, $poa);
                        } else {
                            $get_product = null;
                        }
                    }
                } else {
                    $get_product = null;
                }
            } else {
                $get_product = null;
            }

            $data =[
                'product' =>  $get_product
            ];

            return view('app.pre_order._purchase_order_article_detail', compact('data'));
        }catch (\Exception $e) {
            return json_encode($e->getMessage());
        }

    }

    public function reloadPreOrderDetail(Request $request)
    {
        $po_id = $request->_po_id;
        $poa_data = PreOrderArticle::select('pre_order_articles.id as poa_id')
            ->where(['po_id' => $po_id])->get();
        $total_po = 0;
        if (!empty($poa_data)) {
            foreach ($poa_data as $poa) {
                $poad_data = PreOrderArticleDetails::select('poad_total_price')
                    ->where(['poa_id' => $poa->poa_id])->get();
                if (!empty($poad_data)) {
                    foreach ($poad_data as $poad) {
                        $total_po += $poad->poad_total_price;
                    }
                    $r['total_po'] = $total_po;
                    $r['status'] = '200';
                } else {
                    $r['status'] = '400';
                }
            }
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function chooseStorePo(Request $request)
    {
        $check = DB::table('pre_orders')->where(['id' => $request->_po_id])->update(['st_id' => $request->_st_id]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function chooseProductSupplierPo(Request $request)
    {
        $check = DB::table('pre_orders')->where(['id' => $request->_po_id])->update(['ps_id' => $request->_ps_id]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function chooseBrandePo(Request $request)
    {
        $check = DB::table('pre_orders')->where(['id' => $request->_po_id])->update(['br_id' => $request->_br_id]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function chooseSeasonPo(Request $request)
    {
        $check = DB::table('pre_orders')->where(['id' => $request->_po_id])->update(['ss_id' => $request->_ss_id]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function poDetail(Request $request)
    {
        $po_id = $request->_po_id;
        $check = PreOrder::where(['id' => $po_id])->exists();
        if ($check) {
            $draft = PreOrder::where(['id' => $po_id])->get()->first();
            $r['status'] = '200';
            $r['po_id'] = $draft->id;
            $r['st_id'] = $draft->st_id;
            $r['ps_id'] = $draft->ps_id;
            $r['br_id'] = $draft->br_id;
            $r['ss_id'] = $draft->ss_id;
            $r['pre_order_code'] = $draft->pre_order_code;
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function cancelPreOrder(Request $request)
    {
        $poa = DB::table('pre_order_articles')->where(['po_id' => $request->_id])->get();
        if (!empty($poa)) {
            foreach ($poa as $poa_row) {
                $poad = DB::table('pre_order_article_details')->where(['poa_id' => $poa_row->id])->get();
                foreach ($poad as $poad_row){
                    DB::table('pre_order_article_details')->where(['id' => $poad_row->id])->delete();
                }
                DB::table('pre_order_articles')->where(['id' => $poa_row->id])->delete();
            }
            $item_name = PreOrder::select('pre_order_code')->where('id', $request->_id)->get()->first()->pre_order_code;
            $this->UserActivity('menghapus PO '.$item_name);
            $check = DB::table('pre_orders')->where(['id' => $request->_id])->delete();
            if (!empty($check)) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } else {
            $item_name = PreOrder::select('pre_order_code')->where('id', $request->_id)->get()->first()->pre_order_code;
            $this->UserActivity('menghapus PO '.$item_name);
            $check = DB::table('pre_orders')->where(['id' => $request->_id])->delete();
            if (!empty($check)) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        }
        return json_encode($r);
    }

    public function poSaveDraft(Request $request)
    {
        $poad_id = DB::table('pre_order_article_details')->where(['poad_draft' => '1'])->update(['poad_draft' => '0']);
        $poa_id = DB::table('pre_order_articles')->where(['poa_draft' => '1'])->update(['poa_draft' => '0']);
        $po_id = DB::table('pre_orders')->where(['id' => $request->_id, 'po_draft' => '1'])->update(['po_draft' => '0']);
        if (!empty($po_id)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkPreOrderPurchaseOrder(Request $request)
    {
        try {
            if ($request->_pro_id == null)
            {
                return ;
            }
            // get p_id from pr_order_articles
            $proa = DB::table('pre_order_articles')->where(['po_id' => $request->_pro_id])->pluck('id');

            // get id from pr_order_articles then get from pre_order_article details, if have same id then get pst_id and poad_qty
            $proad = DB::table('pre_order_article_details')->whereIn('poa_id', $proa)->get(['pst_id', 'poad_qty']);

            $poa = DB::table('purchase_order_articles')->where(['po_id' => $request->_po_id])->pluck('id');

            $poad = DB::table('purchase_order_article_details')->whereIn('poa_id', $poa)->get(['pst_id', 'poad_qty']);

            // check same pst_id from pre_order_article_details and purchase_order_article_details
            // if have same id , then check qty, and transfer qty from pre_order_article_details to purchase_order_article_details
            foreach ($proad as $proad_row) {
                foreach ($poad as $poad_row) {
                    if ($proad_row->pst_id == $poad_row->pst_id) {
                        // get price tag from product stock who has same pst_id
                        $pst = DB::table('product_stocks')->where(['id' => $proad_row->pst_id])->first();
                        // calculate total price
                        $qty = $proad_row->poad_qty + $poad_row->poad_qty;
                        $total_price = $pst->ps_price_tag * $qty;
                        DB::table('purchase_order_article_details')->where(['pst_id' => $proad_row->pst_id])->update(['poad_qty' => $qty, 'poad_total_price' => $total_price]);
                        //calculate qty from pre_order_article_details
                        $qty = $proad_row->poad_qty - $poad_row->poad_qty;
                        DB::table('pre_order_article_details')->where(['pst_id' => $proad_row->pst_id])->update(['poad_qty' => $qty]);
                    }
                }
            }

            $r['status'] = '200';

            return json_encode($r);

        }catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    private function generatePoInvoice(): string
    {
        $invoice = date('YmdHis');
        if ($this->poInvoiceExists($invoice)) {
            return $this->generatePoInvoice();
        }
        return $invoice;
    }

    private function poInvoiceExists($number): bool
    {
        return PreOrder::where(['pre_order_code' => $number])->exists();
    }
}
