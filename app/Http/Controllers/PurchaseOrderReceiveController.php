<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseOrderArticleExport;
use App\Models\PODeliveryOrder;
use App\Models\ProductLocationSetup;
use App\Models\PurchaseOrderInvoiceImage;
use App\Models\PurchaseOrderReceiveImportExcel;
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
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderReceiveController extends Controller
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
            'ps_id' => ProductSupplier::where('ps_delete', '!=', '1')->orderByDesc('id')->pluck('ps_name', 'id'),
            'st_id' => Store::selectRaw('ts_stores.id as sid, CONCAT(st_name) as store')
                ->where('st_delete', '!=', '1')
                ->orderByDesc('sid')->pluck('store', 'sid'),
            'br_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
            'mc_id' => MainColor::where('mc_delete', '!=', '1')->orderByDesc('id')->pluck('mc_name', 'id'),
            'sz_id' => Size::where('sz_delete', '!=', '1')->orderByDesc('id')->pluck('sz_name', 'id'),
            'stkt_id' => StockType::where('stkt_delete', '!=', '1')->orderByDesc('id')->pluck('stkt_name', 'id'),
            'tax_id' => Tax::where('tx_delete', '!=', '1')->orderByDesc('id')->pluck('tx_code', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.purchase_order_receive.purchase_order_receive', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        $user = new User;
        $select = ['u_name', 'u_email', 'u_phone', 'g_name'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();

        if (request()->ajax()) {
            return datatables()->of(
                PurchaseOrder::select('purchase_orders.id as po_id', 'st_name', 'ps_name', 'po_invoice', 'po_description', 'po_draft', 'purchase_orders.created_at as po_created_at')
                    ->leftJoin('stores', 'stores.id', '=', 'purchase_orders.st_id')
                    ->leftJoin('product_suppliers', 'product_suppliers.id', '=', 'purchase_orders.ps_id')
                    ->leftJoin('purchase_order_articles', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
                    ->join('product_suppliers', 'product_suppliers.id', '=', 'purchase_orders.ps_id')
                    ->where('po_delete', '!=', '1')
                    ->where(function ($w) use ($user_data) {
                        if ($user_data->g_name != 'administrator') {
                            $w->where('purchase_orders.st_id', '=', Auth::user()->st_id);
                        }
                    }))
                ->editColumn('po_created_at_show', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->po_created_at));
                })
                ->editColumn('po_code', function ($data) {
                    return '#' . $data->po_code;
                })
                ->editColumn('po_total', function ($data) {
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
                ->editColumn('po_status', function ($data) {
                    $poa = PurchaseOrderArticle::where(['po_id' => $data->po_id])->get();
                    if (!empty($poa)) {
                        $total_qty = 0;
                        foreach ($poa as $poa_row) {
                            $poad = PurchaseOrderArticleDetail::where(['poa_id' => $poa_row->id])->get();
                            if (!empty($poad)) {
                                foreach ($poad as $poad_row) {
                                    $total_qty += $poad_row->poad_qty;
                                }
                            }
                        }
                    }
                    if ($data->po_draft == '1') {
                        return '<a class="btn btn-sm btn-warning">Draft</a>';
                    } else {
                        return '<a class="btn btn-sm btn-primary">0/' . $total_qty . '</a>';
                    }
                })
                ->rawColumns(['po_status'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('po_invoice', 'LIKE', "%$search%")
                                ->orWhere('st_name', 'LIKE', "%$search%")
                                ->orWhere('ps_name', 'LIKE', "%$search%")
                                ->orWhere('po_description', 'LIKE', "%$search%")
                                ->orWhere('article_id', 'LIKE', "%$search%")
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
        $save = $product_category->storeData($id);
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

    public function poInvoiceExists($number)
    {
        return PurchaseOrder::where(['po_invoice' => $number])->exists();
    }

    public function createPo()
    {
        $check = PurchaseOrder::where(['po_draft' => '1'])->exists();
        if ($check) {
            $draft = PurchaseOrder::where(['po_draft' => '1'])->get()->first();
            $r['status'] = '219';
            $r['po_id'] = $draft->id;
            $r['st_id'] = $draft->st_id;
            $r['ps_id'] = $draft->ps_id;
            $r['po_invoice'] = $draft->po_invoice;
        } else {
            $po_id = DB::table('purchase_orders')->insertGetId([
                'po_invoice' => $this->generatePoInvoice(),
                'po_draft' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'po_delete' => '0',
            ]);
            if (!empty($po_id)) {
                $r['status'] = '200';
                $r['po_id'] = $po_id;
                $r['po_invoice'] = DB::table('purchase_orders')->select('po_invoice')->where(['id' => $po_id])->get()->first()->po_invoice;
            } else {
                $r['status'] = '400';
            }
        }
        return json_encode($r);
    }

    public function cancelPo(Request $request)
    {
        $poa = DB::table('purchase_order_articles')->where(['po_id' => $request->_id])->get();
        if (!empty($poa)) {
            foreach ($poa as $poa_row) {
                $poad = DB::table('purchase_order_article_details')->where(['poa_id' => $poa_row->id])->get();
                foreach ($poad as $poad_row) {
                    DB::table('purchase_order_article_details')->where(['id' => $poad_row->id])->delete();
                }
                DB::table('purchase_order_articles')->where(['id' => $poa_row->id])->delete();
            }
            $check = DB::table('purchase_orders')->where(['id' => $request->_id])->delete();
            if (!empty($check)) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } else {
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

    public function checkPoReceiveDetail(Request $request)
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
            $po_st_id = $draft->st_id;
            $po_id = $draft->id;
            $poa_data = PurchaseOrderArticle::select(
                'purchase_order_articles.id as poa_id',
                'po_id',
                'products.id as pid',
                'p_price_tag',
                'br_name',
                'p_name',
                'p_color',
                'poa_discount',
                'poa_extra_discount', 'poa_reminder', 'products.article_id as articleid')
                ->leftJoin('products', 'products.id', '=', 'purchase_order_articles.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->where(['po_id' => $po_id])->get();
            if (!empty($poa_data)) {
                $get_product = array();
                foreach ($poa_data as $poa) {
                    $poad_data = PurchaseOrderArticleDetail::select(
                        'purchase_order_article_details.id as poad_id', 'pst_id', 'sz_name', 'ps_qty', 'ps_running_code',
                        'ps_sell_price', 'ps_price_tag', 'poad_qty', DB::raw('SUM(poads_qty) As poads_qty'),
                        'poad_purchase_price', 'poad_total_price', DB::raw('SUM(poads_total_price) As poads_total_price'),
                        'product_stocks.ps_barcode')
                        ->join('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                        ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                        ->groupBy('purchase_order_article_details.id')
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

                    // create to sql
                    if (!empty($poad_data)) {

                        if (!empty($request->excelData)) {
                            foreach ($poad_data as $key => $poad) {
                                foreach ($request->excelData as $excel) {
                                    if ($poad->ps_barcode == $excel['barcode']) {
                                        $poad['qty_import'] = $excel['qty'];
                                    }

                                    if ($poad->ps_barcode != $excel['barcode']) {
                                        $poad['barcode_missing_import'] = $excel['barcode'];
                                    }
                                }
                            }
                        }

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

//        return $data['product']['0']['subitem'][0]['total_pls_qty'];
        return view('app.purchase_order_receive._purchase_order_article_detail', compact('data'));
    }

    public function checkBarcodeImport(Request $request)
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
            $po_st_id = $draft->st_id;
            $po_id = $draft->id;
            $poa_data = PurchaseOrderArticle::select(
                'purchase_order_articles.id as poa_id',
                'po_id',
                'products.id as pid',
                'p_price_tag',
                'br_name',
                'p_name',
                'p_color',
                'poa_discount',
                'poa_extra_discount', 'poa_reminder')
                ->leftJoin('products', 'products.id', '=', 'purchase_order_articles.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->where(['po_id' => $po_id])->get();
            $get_product = array();
            $missingBarcode = array(); // Moved outside the loop

            if (!empty($poa_data)) {
                // Step 1: Collect poad_data
                foreach ($poa_data as $poa) {
                    $poad_data = PurchaseOrderArticleDetail::select(
                        'purchase_order_article_details.id as poad_id', 'pst_id', 'sz_name', 'ps_qty', 'ps_running_code',
                        'ps_sell_price', 'ps_price_tag', 'poad_qty', DB::raw('SUM(poads_qty) As poads_qty'),
                        'poad_purchase_price', 'poad_total_price', DB::raw('SUM(poads_total_price) As poads_total_price'),
                        'product_stocks.ps_barcode',
                    )
                        ->join('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                        ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                        ->groupBy('purchase_order_article_details.id')
                        ->where(['poa_id' => $poa->poa_id])
                        ->get();

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

                    $poa->subitem = $poad_data;

                    array_push($get_product, $poa);
                }

                // After the loop, process missingBarcode and update quantities
                if (!empty($request->excelData)) {
                    // Index poad_data by barcode for easier access
                    $poad_data_indexed = collect($get_product)->flatMap(function ($poa) {
                        return $poa->subitem;
                    })->keyBy('ps_barcode')->all();

                    foreach ($request->excelData as $excel_row) {
                        $excelBarcode = $excel_row['barcode'];

                        // Add all barcodes to missingBarcode initially
                        if (!in_array($excelBarcode, $missingBarcode)) {
                            $missingBarcode[] = $excelBarcode;
                        }

                        // Check if the barcode exists in $poad_data_indexed
                        if (array_key_exists($excelBarcode, $poad_data_indexed)) {
                            // If the barcode exists, update the quantity
                            $poad_data_indexed[$excelBarcode]->qty_import = $excel_row['qty'];

                            // Remove the barcode from $missingBarcode if it exists
                            $key = array_search($excelBarcode, $missingBarcode);
                            if ($key !== false) {
                                unset($missingBarcode[$key]);
                            }
                        }
                    }
                }
            } else {
                $get_product = null;
            }
        } else {
            $get_product = null;
        }


        return json_encode($missingBarcode);
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

    public function poReceiveDetail(Request $request)
    {
        $po_id = $request->_po_id;
        $check = PurchaseOrder::where(['id' => $po_id])->exists();
        if ($check) {
            $draft = PurchaseOrder::where(['id' => $po_id])->get()->first();
            $r['status'] = '200';
            $r['po_id'] = $draft->id;
            $r['st_id'] = $draft->st_id;
            $r['ps_id'] = $draft->ps_id;
            $r['stkt_id'] = $draft->stkt_id;
            $r['tax_id'] = $draft->tax_id;
            $r['po_description'] = $draft->po_description;
            $r['po_shipping_cost'] = $draft->po_shipping_cost;
            $r['po_invoice'] = $draft->po_invoice;
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function poExport(Request $request)
    {
        $po_date = $request->input('po_date');
        $po_status = $request->input('po_status');
        $st_id = $request->input('st_id');
        $data = array();
        $start = null;
        $end = null;
        $range = null;
        if (!empty($po_date)) {
            $exp = explode('|', $po_date);
            if (count($exp) > 1) {
                $start = $exp[0];
                $end = $exp[1];
                $range = 'true';
            } else {
                $start = $po_date;
                $end = $po_date;
                $range = 'false';
            }
        }

        $get_po = PurchaseOrderArticleDetail::select('purchase_order_article_details.id as poad_id', 'po_invoice', 'st_name', 'purchase_orders.created_at as po_created', 'br_name', 'p_name', 'p_color', 'sz_name', 'poad_qty', 'poad_purchase_price')
            ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
            ->leftJoin('stores', 'stores.id', '=', 'purchase_orders.st_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where(function ($w) use ($start, $end, $range, $st_id) {
                if ($range == 'true') {
                    $w->whereDate('purchase_orders.created_at', '>=', $start)
                        ->whereDate('purchase_orders.created_at', '<=', $end);
                } else {
                    $w->whereDate('purchase_orders.created_at', '=', $start);
                }
                if (!empty($st_id)) {
                    $w->where('purchase_orders.st_id', '=', $st_id);
                }
            })->get();
        if (!empty($get_po)) {
            foreach ($get_po as $row) {
                $receive = PurchaseOrderArticleDetailStatus::select('poads_qty')->where('poad_id', '=', $row->poad_id)->sum('poads_qty');
                if ($po_status == 'full') {
                    if ($receive >= $row->poad_qty) {
                        $data[] = [
                            'po_created' => date('d/m/Y H:i:s', strtotime($row->po_created)),
                            'po_invoice' => $row->po_invoice,
                            'store' => $row->st_name,
                            'brand' => $row->br_name,
                            'article' => $row->p_name,
                            'color' => $row->p_color,
                            'size' => $row->sz_name,
                            'order' => $row->poad_qty,
                            'hpp' => $row->poad_purchase_price,
                            'total' => $row->poad_qty * $row->poad_purchase_price,
                            'receive' => $receive,
                        ];
                    } else {
                        continue;
                    }
                } else {
                    if ($receive < $row->poad_qty) {
                        $data[] = [
                            'po_created' => date('d/m/Y H:i:s', strtotime($row->po_created)),
                            'po_invoice' => $row->po_invoice,
                            'store' => $row->st_name,
                            'brand' => $row->br_name,
                            'article' => $row->p_name,
                            'color' => $row->p_color,
                            'size' => $row->sz_name,
                            'order' => $row->poad_qty,
                            'hpp' => $row->poad_purchase_price,
                            'total' => $row->poad_qty * $row->poad_purchase_price,
                            'receive' => $receive,
                        ];
                    } else {
                        continue;
                    }
                }
            }
            sort($data);
            $r['status'] = '200';
            $r['data'] = $data;
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function getImageInvoiceDatatables(Request $request)
    {
        if ($request->ajax()) {
            $po_id = PurchaseOrderInvoiceImage::where('purchase_order_id', '=', $request->get('_po_id'))->exists();
            if ($po_id) {
                $images = PurchaseOrderInvoiceImage::select('id', 'invoice_image')
                    ->where('purchase_order_id', '=', $request->get('_po_id'));

                return datatables()->of($images)
                    ->addColumn('image', function ($row) {
                        if (empty($row->invoice_image)) {
                            return '<img src="' . asset('upload/image/no_image.png') . '"/>';
                        } else {
//                            return '<a href="'.asset('upload/purchase_order_invoice/'.$row->invoice_image).' target=_blank>$row->invoice_image</a>';
                            return '<a href="' . asset('upload/purchase_order_invoice/' . $row->invoice_image) . '" target="_blank">' . $row->invoice_image . '</a>';
                        }
                    })
                    ->addColumn('action', function ($row) {
                        return '<a href="#" class="btn btn-danger btn-sm " id="delete-image-invoice" data-id="' . $row->id . '">Delete</a>';
                    })
                    ->rawColumns(['image', 'action'])
                    ->addIndexColumn()
                    ->make(true);
            } else {
                return datatables()->of([])
                    ->addIndexColumn()
                    ->make(true);
            }
        }
    }

    public function getImageDeliveryOrdersDatatables(Request $request)
    {
        if ($request->ajax()) {
            $po_id = PODeliveryOrder::where('purchase_order_id', '=', $request->get('_po_id'))->exists();
            if ($po_id) {
                $images = PODeliveryOrder::select('id', 'delivery_orders_image')
                    ->where('purchase_order_id', '=', $request->get('_po_id'));

                return datatables()->of($images)
                    ->addColumn('image', function ($row) {
                        if (empty($row->delivery_orders_image)) {
                            return '<img src="' . asset('upload/image/no_image.png') . '"/>';
                        } else {
                            return '<img src="' . asset('upload/purchase_order_delivery_order/' . $row->delivery_orders_image) . '" width="400px" height="400px">';
                        }
                    })
                    ->addColumn('action', function ($row) {
                        return '<a href="#" class="btn btn-danger btn-sm" id="delete-image-po-surat-jalan" data-id="' . $row->id . '">Delete</a>';
                    })
                    ->rawColumns(['image', 'action'])
                    ->addIndexColumn()
                    ->make(true);
            } else {
                return datatables()->of([])
                    ->addIndexColumn()
                    ->make(true);
            }
        }
    }

    public function uploadDeliveryOrdersImage(Request $request)
    {

        $po_id = $request->po_id;

        $check = PurchaseOrder::where(['id' => $po_id])->exists();

        if ($check) {
            if ($request->has('deliveryOrderImage')) {
                $image_parts = explode(";base64,", $request->input('deliveryOrderImage'));
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];

                $image_base64 = base64_decode($image_parts[1]);

                // Save the decoded image to the server
                $name = time() . '.' . $image_type;
                $destinationPath = public_path('/upload/purchase_order_delivery_order');
                file_put_contents($destinationPath . '/' . $name, $image_base64);

                // Save the image information to the database
                PODeliveryOrder::create([
                    'purchase_order_id' => $po_id,
                    'delivery_orders_image' => $name,
                ]);
            }
        }

        $response = ['status' => $check ? '200' : '400'];
        return json_encode($response);
    }

    public function deleteImageInvoice(Request $request)
    {


        $delete = PurchaseOrderInvoiceImage::where(['id' => $request->id])->first();

        if ($delete) {
            unlink(public_path('upload/purchase_order_invoice/' . $delete->invoice_image));


            $delete = PurchaseOrderInvoiceImage::where(['id' => $request->id])->delete();
        }

        $response = ['status' => $delete ? '200' : '400'];
        return json_encode($response);
    }


    public function deleteImagePOSuratJalan(Request $request)
    {
//        return $request->all();
        $delete = PODeliveryOrder::where(['id' => $request->id])->first();

        if ($delete) {
            unlink(public_path('upload/purchase_order_delivery_order/' . $delete->delivery_orders_image));

            $delete = PODeliveryOrder::where(['id' => $request->id])->delete();
        }

        $response = ['status' => $delete ? '200' : '400'];

        return json_encode($response);
    }
}
