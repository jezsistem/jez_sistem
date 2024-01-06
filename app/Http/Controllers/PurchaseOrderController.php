<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseOrderArticleExport;
use App\Models\Account;
use App\Models\ProductSubCategory;
use App\Models\PurchaseOrderInvoiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderArticle;
use App\Models\PurchaseOrderArticleDetail;
use App\Models\PurchaseOrderArticleDetailStatus;
use App\Models\ProductSupplier;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\Brand;
use App\Models\MainColor;
use App\Models\Size;
use App\Models\StockType;
use App\Models\Tax;
use App\Models\UserActivity;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderController extends Controller
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
            'mc_id' => MainColor::where('mc_delete', '!=', '1')->orderByDesc('id')->pluck('mc_name', 'id'),
            'sz_id' => Size::where('sz_delete', '!=', '1')->orderByDesc('id')->pluck('sz_name', 'id'),
            'stkt_id' => StockType::where('stkt_delete', '!=', '1')->orderByDesc('id')->pluck('stkt_name', 'id'),
            'tax_id' => Tax::where('tx_delete', '!=', '1')->orderByDesc('id')->pluck('tx_code', 'id'),
            'psc_id' => ProductSubCategory::where('psc_delete', '!=', '1')->orderByDesc('id')->pluck('psc_name', 'id'),
            'acc_id' => Account::where('a_delete', '!=', '1')->orderByDesc('id')->pluck('a_code', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.purchase_order.purchase_order', compact('data'));
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
            return datatables()->of(PurchaseOrder::select('purchase_orders.id as po_id', 'st_name', 'ps_name', 'po_invoice', 'po_description', 'po_draft', 'purchase_orders.created_at as po_created_at')
            ->leftJoin('purchase_order_articles', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
            ->leftJoin('products', 'products.id', '=', 'purchase_order_articles.p_id')
            ->join('stores', 'stores.id', '=', 'purchase_orders.st_id')
            ->join('product_suppliers', 'product_suppliers.id', '=', 'purchase_orders.ps_id')
            ->where('po_delete', '!=', '1')
            ->where(function($w) use ($user_data, $st_id) {
                if ($user_data->g_name != 'administrator') {
                    $w->where('purchase_orders.st_id', '=', Auth::user()->st_id);
                } else {
                    if (!empty($st_id)) {
                        $w->where('purchase_orders.st_id', '=',$st_id);
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
                $poa = PurchaseOrderArticle::where(['po_id' => $data->po_id])->get();
                if (!empty($poa)) {
                    $total_price = 0;
                    foreach ($poa as $poa_row) {
                        $poad = PurchaseOrderArticleDetail::where(['poa_id' => $poa_row->id])->get();
                        if (!empty($poad)) {
                            foreach ($poad as $poad_row) {
                                $total_price += $poad_row->poad_total_price;
                            }
                        }
                    }
                }
                return number_format($total_price);
            })
            ->editColumn('po_status', function($data){ 
                $poa = PurchaseOrderArticle::where(['po_id' => $data->po_id])->get();
                if (!empty($poa)) {
                    $total_qty = 0;
                    $total_qty_receive = 0;
                    foreach ($poa as $poa_row) {
                        $poad = PurchaseOrderArticleDetail::where(['poa_id' => $poa_row->id])->get();
                        if (!empty($poad)) {
                            foreach ($poad as $poad_row) {
                                $total_qty += $poad_row->poad_qty;
                                $poads = PurchaseOrderArticleDetailStatus::where(['poad_id' => $poad_row->id, 'poads_type' => 'IN'])->get();
                                if (!empty($poads)) {
                                    foreach ($poads as $poads_row) {
                                        $total_qty_receive += $poads_row->poads_qty;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($data->po_draft == '1') {
                    return '<a class="btn btn-sm btn-warning">Draft</a>';
                } else {
                    return '<a class="btn btn-sm btn-primary">'.$total_qty_receive.'/'.$total_qty.'</a>';
                }
            })
            ->rawColumns(['po_status'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('po_invoice', 'LIKE', "%$search%")
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

    public function storeData(Request $request)
    {
        $product_category = new ProductCategory;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'pc_name' => $request->input('pc_name'),
            'pc_description' => $request->input('pc_description'),
            'pc_delete' => '0',
        ];

        $save = $product_category->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $product_category = new ProductCategory;
        $id = $request->input('_id');
        $save = $product_category->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
    
    public function checkProductPo(Request $request)
    {
        $check = ProductStock::where(['p_id' => $request->_p_id])->join('purchase_orders', 'purchase_orders.ps_id', '=', 'product_stocks.id')->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function generatePoInvoice()
    {
        $invoice = date('YmdHis');
        if ($this->poInvoiceExists($invoice)) {
            return generatePoInvoice();
        }
        return $invoice;
    }

    public function poInvoiceExists($number) {
        return PurchaseOrder::where(['po_invoice' => $number])->exists();
    }

    public function createPo()
    {
        $po_id = DB::table('purchase_orders')->insertGetId([
            'po_invoice' => $this->generatePoInvoice(),
            'po_draft' => '0',
            'created_at' => date('Y-m-d H:i:s'),
            'po_delete' => '0',
        ]);
        if (!empty($po_id)) {
            $r['status'] = '200';
            $r['po_id'] = $po_id;
            $r['po_invoice'] = DB::table('purchase_orders')->select('po_invoice')->where(['id' => $po_id])->get()->first()->po_invoice;
            $this->UserActivity('membuat PO '.$r['po_invoice']);
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function cancelPo(Request $request)
    {
        $poa = DB::table('purchase_order_articles')->where(['po_id' => $request->_id])->get();
        if (!empty($poa)) {
            foreach ($poa as $poa_row) {
                $poad = DB::table('purchase_order_article_details')->where(['poa_id' => $poa_row->id])->get();
                foreach ($poad as $poad_row){
                    DB::table('purchase_order_article_details')->where(['id' => $poad_row->id])->delete();
                }
                DB::table('purchase_order_articles')->where(['id' => $poa_row->id])->delete();
            }
            $item_name = PurchaseOrder::select('po_invoice')->where('id', $request->_id)->get()->first()->po_invoice;
            $this->UserActivity('menghapus PO '.$item_name);
            $check = DB::table('purchase_orders')->where(['id' => $request->_id])->delete();
            if (!empty($check)) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } else {
            $item_name = PurchaseOrder::select('po_invoice')->where('id', $request->_id)->get()->first()->po_invoice;
            $this->UserActivity('menghapus PO '.$item_name);
            $check = DB::table('purchase_orders')->where(['id' => $request->_id])->delete();
            if (!empty($check)) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        }
        return json_encode($r);
    }

    public function chooseStorePo(Request $request)
    {
        $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['st_id' => $request->_st_id]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function chooseTaxPo(Request $request)
    {
        $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['tax_id' => $request->_tax_id]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function choosePaymentPo(Request $request)
    {
        $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['acc_id' => $request->_acc_id]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function chooseSupplierPo(Request $request)
    {
        $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['ps_id' => $request->_ps_id]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function chooseStockType(Request $request)
    {
        $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])
        ->update(['stkt_id' => $request->_stkt_id]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function descriptionPo(Request $request)
    {
        $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['po_description' => $request->_po_description]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function shippingCostPo(Request $request)
    {
        $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['po_shipping_cost' => $request->_po_shipping_cost]);
        if (!empty($check)) {
            $r['status'] = '200';
            $r['po_shipping_cost'] = $request->_po_shipping_cost;
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function createPoDetail(Request $request)
    {
        $poid = $request->_poid;
        $poaid = $request->_poaid;
        $pid = $request->_pid;
        $psid = $request->_psid;
        $status = $request->_status;

        $check_poa = PurchaseOrderArticle::where(['po_id' => $poid, 'p_id' => $pid])->exists();
        if (!$check_poa) {
            $poa_id = DB::table('purchase_order_articles')->insertGetId([
                'po_id' => $poid,
                'p_id' => $pid,
            ]);
        } else {
            $poa_id = DB::table('purchase_order_articles')->select('id')->where([
                'po_id' => $poid,
                'p_id' => $pid,
            ])->get()->first()->id;
        }

        $check_poad = PurchaseOrderArticleDetail::where(['poa_id' => $poa_id, 'pst_id' => $psid])->exists();
        if (!$check_poad) {
            $status_poad = DB::table('purchase_order_article_details')->insert([
                'poa_id' => $poa_id,
                'pst_id' => $psid,
                'poad_draft' => '1'
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

    public function checkPoDetail(Request $request)
    {
        $po_id = $request->_po_id;
        if (!empty($po_id)) {
            $check = PurchaseOrder::where(['id' => $po_id])->exists();
        } else {
            $check = PurchaseOrder::where(['po_draft' => '1'])->exists();
        }
        if ($check) {
            if (!empty($po_id)) {
                $draft = PurchaseOrder::where(['id' => $po_id])->get()->first();
            } else {
                $draft = PurchaseOrder::where(['po_draft' => '1'])->get()->first();
            }
            $po_id = $draft->id;
            $poa_data = PurchaseOrderArticle::select('purchase_order_articles.id as poa_id', 'po_id', 'products.id as pid', 'br_name', 'p_price_tag', 'p_purchase_price', 'p_name', 'p_color', 'poa_discount', 'poa_extra_discount', 'poa_reminder')
            ->leftJoin('products', 'products.id', '=', 'purchase_order_articles.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where(['po_id' => $po_id])->get();
            if (!empty($poa_data)) {
                $get_product = array();
                foreach ($poa_data as $poa) {
                    $poad_data = PurchaseOrderArticleDetail::select('purchase_order_article_details.id as poad_id', 'sz_name', 'ps_qty', 'ps_running_code', 'ps_sell_price', 'ps_price_tag', 'ps_purchase_price', 'poad_qty', 'poad_purchase_price', 'poad_total_price')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->where(['poa_id' => $poa->poa_id])->get();
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
        $data = [
            'product' => $get_product,
        ];
        return view('app.purchase_order._purchase_order_article_detail', compact('data'));
    }

    public function reloadPoDetail(Request $request)
    {
        $po_id = $request->_po_id;
        $poa_data = PurchaseOrderArticle::select('purchase_order_articles.id as poa_id')
        ->where(['po_id' => $po_id])->get();
        $total_po = 0;
        if (!empty($poa_data)) {
            foreach ($poa_data as $poa) {
                $poad_data = PurchaseOrderArticleDetail::select('poad_total_price')
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

    public function poSaveDraft(Request $request)
    {
        $poad_id = DB::table('purchase_order_article_details')->where(['poad_draft' => '1'])->update(['poad_draft' => '0']);
        $poa_id = DB::table('purchase_order_articles')->where(['poa_draft' => '1'])->update(['poa_draft' => '0']);
        $po_id = DB::table('purchase_orders')->where(['id' => $request->_id, 'po_draft' => '1'])->update(['po_draft' => '0']);
        if (!empty($po_id)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function poDetail(Request $request)
    {
        $po_id = $request->_po_id;
        $check = PurchaseOrder::where(['id' => $po_id])->exists();
        if ($check) {
            $draft = PurchaseOrder::where(['id' => $po_id])->get()->first();
            $r['status'] = '200';
            $r['po_id'] = $draft->id;
            $r['st_id'] = $draft->st_id;
            $r['ps_id'] = $draft->ps_id;
            $r['tax_id'] = $draft->tax_id;
            $r['stkt_id'] = $draft->stkt_id;
            $r['po_description'] = $draft->po_description;
            $r['po_invoice'] = $draft->po_invoice;
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function upladImageInvoice(Request $request)
    {

        $po_id = $request->_po_id;
        $check = PurchaseOrder::where(['id' => $po_id])->exists();
        if ($check)
        {
            if ($request->hasFile('imageInvoices'))
            {
                foreach($request->file('imageInvoices') as $file)
                {
                    $image = $file;
                    $name = time().'.'.$image->getClientOriginalExtension();
                    $destinationPath = public_path('/upload/purchase_order_invoice');
                    $img = Image::make($image->getRealPath());
                    $img->resize(400, 400, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($destinationPath.'/'.$name);

                    PurchaseOrderInvoiceImage::create([
                        'purchase_order_id' => $po_id,
                        'invoice_image' => $name,
                    ]);
                }
            }
        }

        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function exportPurchaseOrderArticleData(Request $request)
    {
        $po_id = $request->get('po_id');

        $export = new PurchaseOrderArticleExport($po_id);

        return Excel::download($export, 'purchase_order_article.xlsx');
    }
}
