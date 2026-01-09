<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseOrderArticleExport;
use App\Exports\PurchaseOrderRecevieExport;
use App\Models\Account;
use App\Models\PreOrder;
use App\Models\ProductLocationSetup;
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
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\PurchaseOrderTransferImage;
use App\Models\Size;
use App\Models\StockType;
use App\Models\Tax;
use App\Models\UserActivity;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

use App\Models\DataPerusahaan;
use App\Models\PurchaseOrderLog;

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
            'st_id' => Store::selectRaw('ts_stores.id as sid, CONCAT(st_name) as store')->where('st_name', 'NOT LIKE', '%ONLINE%')
                ->where('st_delete', '!=', '1')
                ->orderByDesc('sid')->pluck('store', 'sid'),
            'br_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
            'mc_id' => MainColor::where('mc_delete', '!=', '1')->orderByDesc('id')->pluck('mc_name', 'id'),
            'sz_id' => Size::where('sz_delete', '!=', '1')->orderByDesc('id')->pluck('sz_name', 'id'),
            'stkt_id' => StockType::where('stkt_delete', '!=', '1')->orderByDesc('id')->pluck('stkt_name', 'id'),
            'tax_id' => Tax::where('tx_delete', '!=', '1')->orderByDesc('id')->pluck('tx_code', 'id'),
            'dp_id' => DataPerusahaan::orderByDesc('id')->pluck('dp_name', 'id'),
            'psc_id' => ProductSubCategory::where('psc_delete', '!=', '1')->orderByDesc('id')->pluck('psc_name', 'id'),
            'acc_id' => Account::where('a_delete', '!=', '1')->orderByDesc('id')->pluck('a_name', 'id'),
            'pro_id' => PreOrder::getAllDataPO(),
            'segment' => request()->segment(1),
        ];
        return view('app.purchase_order.purchase_order', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        $st_id = $request->st_id;
        $status_finance = $request->status_finance;
        $filter_dispute = $request->filter_dispute;
        $filter_status_dispute = $request->filter_status_dispute;

        $user = new User;
        $select = ['u_name', 'u_email', 'u_phone', 'g_name'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        if (request()->ajax()) {
            return datatables()->of(PurchaseOrder::select(
                'purchase_orders.id as po_id',
                'st_name',
                'ps_name',
                'po_invoice',
                'po_description',
                'status_dispute',
                'po_draft',
                'purchase_order_article_detail_statuses.u_id_approve',
                'purchase_order_article_detail_statuses.created_at as status_created_at',
                'purchase_order_article_detail_statuses.updated_at as status_updated_at',
                'purchase_orders.created_at as po_created_at',
                'po_total_purchase',
                'po_payment_amount',
                'po_total_qty',
                'purchase_orders.finance_status'
            )
                ->leftJoin('purchase_order_articles', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
                ->leftJoin('products', 'products.id', '=', 'purchase_order_articles.p_id')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.poa_id', '=', 'purchase_order_articles.id')
                ->leftJoin('purchase_order_article_detail_statuses', function ($join) {
                    $join->on('purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                        ->whereNull('purchase_order_article_detail_statuses.u_id_reject');
                })
                ->join('stores', 'stores.id', '=', 'purchase_orders.st_id')
                ->join('product_suppliers', 'product_suppliers.id', '=', 'purchase_orders.ps_id')
                ->leftJoin('purchase_order_file_delivery_note', 'purchase_order_file_delivery_note.purchase_order_id', '=', 'purchase_orders.id')
                ->where('po_delete', '!=', '1')
                ->where(function ($w) use ($user_data, $st_id) {
                    if ($user_data->g_name != 'administrator') {
                        $w->where('purchase_orders.st_id', '=', Auth::user()->st_id);
                    } else {
                        if (!empty($st_id)) {
                            $w->where('purchase_orders.st_id', '=', $st_id);
                        }
                    }
                })
                ->when($request->get('filter_delivery_note') === "1", function ($query) {
                    $query->whereNotNull('purchase_order_file_delivery_note.id')
                        ->where('purchase_order_file_delivery_note.id', '!=', '')
                        ->whereRaw('CAST(ts_purchase_order_file_delivery_note.id AS UNSIGNED) > 0');
                }, function ($query) use ($request) {
                    if ($request->get('filter_delivery_note') === "0") {
                        $query->where(function ($q) {
                            $q->whereNull('purchase_order_file_delivery_note.id')
                                ->orWhere('purchase_order_file_delivery_note.id', '')
                                ->orWhereRaw('CAST(ts_purchase_order_file_delivery_note.id AS UNSIGNED) = 0');
                        });
                    }
                })
                ->when($status_finance, function ($query) use ($request) {
                    $query->where('purchase_orders.finance_status', '=', $request->get('status_finance'));
                })
                ->when(isset($filter_dispute), function ($query) use ($filter_dispute) {
                    $query->where('purchase_orders.dispute', '=', $filter_dispute);
                })
                ->when(isset($filter_status_dispute), function ($query) use ($filter_status_dispute) {
                    $query->where('purchase_orders.status_dispute', '=', $filter_status_dispute);
                })
                ->groupBy('po_id'))
                ->editColumn('po_created_at_show', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->po_created_at));
                })
                ->editColumn('po_code', function ($data) {
                    return '#' . $data->po_code;
                })
                ->editColumn('po_total', function ($data) {
                    $poa = PurchaseOrderArticle::where(['po_id' => $data->po_id])->get();

                    $custom_sku_po = ProductStock::where('ps_barcode', 'CUSTOMPO')->get();

                    if (empty($custom_sku_po)) {
                        return false;
                    }

                    # Get PO with CUSPO item
                    if (!empty($poa)) {
                        $poa_ids = $poa->pluck('id');
                        $has_custom_po_item = PurchaseOrderArticleDetail::whereIn('poa_id', $poa_ids)
                            ->where('pst_id', $custom_sku_po->first()->id)
                            ->exists();
                    }

                    if ($has_custom_po_item) {
                        return number_format($data->po_total_purchase);
                    }

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
                    $custom_sku_po = ProductStock::where('ps_barcode', 'CUSTOMPO')->get();

                    if (empty($custom_sku_po)) {
                        return false;
                    }

                    # Get PO with CUSPO item
                    if (!empty($poa)) {
                        $poa_ids = $poa->pluck('id');
                        $has_custom_po_item = PurchaseOrderArticleDetail::whereIn('poa_id', $poa_ids)
                            ->where('pst_id', $custom_sku_po->first()->id)
                            ->exists();
                    }

                    if (!empty($poa)) {
                        $total_qty = 0;
                        $total_qty_receive = 0;
                        foreach ($poa as $poa_row) {
                            $poad = PurchaseOrderArticleDetail::where(['poa_id' => $poa_row->id])->get();
                            if (!empty($poad)) {
                                foreach ($poad as $poad_row) {
                                    $total_qty += $poad_row->poad_qty;
                                    $poads = PurchaseOrderArticleDetailStatus::where(['poad_id' => $poad_row->id, 'poads_type' => 'IN'])
                                        ->whereNull('u_id_reject')
                                        ->get();
                                    if (!empty($poads)) {
                                        foreach ($poads as $poads_row) {
                                            $total_qty_receive += $poads_row->poads_qty;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($has_custom_po_item) {
                        return '<a class="btn btn-sm btn-primary">' . $total_qty_receive . '/' . $data->po_total_qty . '</a>';
                    }

                    if ($data->po_draft == '1') {
                        return '<a class="btn btn-sm btn-warning">Draft</a>';
                    } else {
                        if ($total_qty_receive == $total_qty) {
                            return '<a class="btn btn-sm btn-light-success">' . $total_qty_receive . '/' . $total_qty . '</a>';
                        } else {
                            return '<a class="btn btn-sm btn-primary">' . $total_qty_receive . '/' . $total_qty . '</a>';
                        }
                    }
                })
                ->editColumn('u_receive', function ($data) {
                    if (!empty($data->u_id_approve) && $data->acc_id == 93 && $data->is_paid == 0) {
                        $name = DB::table('users')->where('id', '=', $data->u_id_approve)->first()->u_name;
                        return '<span class="badge badge-primary">' . $name . '<br/> Diterima, Belum Dibayar</span>';
                    } else if (!empty($data->u_id_approve)) {
                        $name = DB::table('users')->where('id', '=', $data->u_id_approve)->first()->u_name;
                        return '<span class="badge text-white" style="background-color: #16C47F;">' . $name . '<br/>' . date('d/m/Y H:i:s', strtotime($data->status_updated_at)) . '</span>';
                    } else {
                        return '<span class="badge badge-warning">Menunggu Approval</span>';
                    }
                })
                ->editColumn('finance_status', function ($data) {
                    $statusColors = [
                        'lunas' => '#28a745',
                        'hutang' => '#ffc107',
                        'piutang' => '#17a2b8',
                        'piutang (overpayment partial)' => '#6f42c1',
                        'hutang (partial receive)' => '#fd7e14',
                        'draft / pending' => '#6c757d',
                        'consignment' => '#20c997'
                    ];

                    $status = strtolower($data->finance_status ?? 'draft / pending');
                    $color = $statusColors[$status] ?? '#6c757d';

                    return '<span class="badge" style="background-color: ' . $color . '; color: white;">' . strtoupper($status) . '</span>';
                })
                ->addColumn('is_no_item', function ($data) {

                    $poa = PurchaseOrderArticle::where(['po_id' => $data->po_id])->get();

                    $custom_sku_po = ProductStock::where('ps_barcode', 'CUSTOMPO')->get();

                    if (empty($custom_sku_po)) {
                        return false;
                    }

                    # Get PO with CUSPO item
                    if (!empty($poa)) {
                        $poa_ids = $poa->pluck('id');
                        $has_custom_po_item = PurchaseOrderArticleDetail::whereIn('poa_id', $poa_ids)
                            ->where('pst_id', $custom_sku_po->first()->id)
                            ->exists();
                    }

                    if ($has_custom_po_item) {
                        return true;
                    }
                })
                ->rawColumns(['po_status', 'u_receive', 'finance_status'])
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
                    if ($request->has('filter_dispute')) {
                        $filter = $request->get('filter_dispute');

                        if ($filter === '1') {
                            $instance->where('dispute', 1);
                        } elseif ($filter === '0') {
                            $instance->where('dispute', 0);
                        }
                    }
                    if ($request->has('po_status_filter')) {
                        $filter = $request->get('po_status_filter');

                        if ($filter === 'unfull') {
                            $instance->whereNull('u_id_approve');
                        } elseif ($filter === 'full') {
                            $instance->whereNotNull('u_id_approve');
                        }
                    }
                    if (!empty($request->get('date'))) {
                        $instance->where(function ($w) use ($request) {
                            $date = $request->get('date');
                            $start = null;
                            $end = null;
                            $exp = explode('|', $date);
                            if (count($exp) > 1) {
                                if ($exp[0] != $exp[1]) {
                                    $start = $exp[0];
                                    $end = $exp[1];
                                } else {
                                    $start = $exp[0];
                                }
                            } else {
                                $start = $date;
                            }
                            if (!empty($end)) {
                                $w->whereDate('purchase_orders.created_at', '>=', $start)
                                    ->whereDate('purchase_orders.created_at', '<=', $end);
                            } else {
                                $w->whereDate('purchase_orders.created_at', $start);
                            }
                        });
                    }
                    if ($request->has('status_purchase')) {
                        $filter = $request->get('status_purchase');

                        if ($filter === 'in_progress') {
                            //get qty receive
                            $instance->whereRaw('(SELECT COUNT(*) FROM ts_purchase_order_article_detail_statuses WHERE ts_purchase_order_article_detail_statuses.poad_id IN (SELECT id FROM ts_purchase_order_article_details WHERE poa_id IN (SELECT id FROM ts_purchase_order_articles WHERE po_id = ts_purchase_orders.id)) AND poads_type = "IN") = 0');
                        } elseif ($filter === 'partial') {
                            //count the approved row and not approved yet
                            $instance->whereExists(function ($query) {
                                $query->selectRaw('1')
                                    ->from('purchase_order_articles')
                                    ->leftJoin('purchase_order_article_details', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
                                    ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                                    ->whereColumn('purchase_order_articles.po_id', 'purchase_orders.id')
                                    ->havingRaw('COUNT(CASE WHEN ts_purchase_order_article_detail_statuses.u_id_approve IS NULL THEN 1 END) > 0')
                                    ->havingRaw('COUNT(ts_purchase_order_article_detail_statuses.u_id_approve) > 0');
                            });
                        } elseif ($filter === 'done') {
                            $instance->whereNotNull('u_id_approve')
                                ->whereRaw('(SELECT COALESCE(SUM(poads_qty), 0) FROM ts_purchase_order_article_detail_statuses WHERE ts_purchase_order_article_detail_statuses.poad_id IN (SELECT id FROM ts_purchase_order_article_details WHERE poa_id IN (SELECT id FROM ts_purchase_order_articles WHERE po_id = ts_purchase_orders.id)) AND poads_type = "IN") = ts_purchase_orders.po_total_qty');
                        }
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $product_category = new ProductCategory();
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
            return $this->generatePoInvoice();
        }
        return $invoice;
    }

    public function poInvoiceExists($number)
    {
        return PurchaseOrder::where(['po_invoice' => $number])->exists();
    }

    public function createPo(Request $request)
    {
        DB::beginTransaction();
        try {
            $type = $request->input('_type');

            $po_id = DB::table('purchase_orders')->insertGetId([
                'po_invoice' => $this->generatePoInvoice(),
                'po_draft' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'po_delete' => '0',
            ]);

            $poa_id = null;
            $poad_id = null;

            if ($type == 'without_item') {
                $product_stock = ProductStock::where('ps_barcode', 'CUSTOMPO')->first();
                if (!$product_stock) {
                    throw new \Exception('CUSTOMPO stock not found');
                }
                $product = Product::where('id', $product_stock->p_id)->first();
                if (!$product) {
                    throw new \Exception('Product for CUSTOMPO not found');
                }

                $poa_id = DB::table('purchase_order_articles')->insertGetId([
                    'po_id' => $po_id,
                    'p_id' => $product->id,
                    'poa_reminder' => $product->p_aging,
                ]);

                $poad_id = DB::table('purchase_order_article_details')->insertGetId([
                    'poa_id' => $poa_id,
                    'pst_id' => $product_stock->id,
                    'poad_draft' => '0',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            DB::commit();

            if (empty($poad_id) && !empty($po_id)) {
                $r['status'] = '200';
                $r['po_id'] = $po_id;
                $r['po_invoice'] = DB::table('purchase_orders')->select('po_invoice')->where(['id' => $po_id])->get()->first()->po_invoice;
                $this->UserActivity('membuat PO ' . $r['po_invoice']);
            } elseif (!empty($poa_id) && !empty($poad_id) && !empty($po_id)) {
                $r['status'] = '200';
                $r['po_id'] = $po_id;
                $r['po_invoice'] = DB::table('purchase_orders')->select('po_invoice')->where(['id' => $po_id])->get()->first()->po_invoice;
                $this->UserActivity('membuat PO Tanpa Item ' . $r['po_invoice']);
            } else {
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['error'] = $e->getMessage();
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
            $item_name = PurchaseOrder::select('po_invoice')->where('id', $request->_id)->get()->first()->po_invoice;
            $this->UserActivity('menghapus PO ' . $item_name);
            $check = DB::table('purchase_orders')->where(['id' => $request->_id])->delete();
            if (!empty($check)) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } else {
            $item_name = PurchaseOrder::select('po_invoice')->where('id', $request->_id)->get()->first()->po_invoice;
            $this->UserActivity('menghapus PO ' . $item_name);
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
        DB::beginTransaction();
        try {
            $data_before = DB::table('purchase_orders')->where(['id' => $request->_po_id])->select('st_id')->first()->st_id;

            $store_before = DB::table('stores')->where('id', $data_before)->select('st_name')->first();
            $store_before = $store_before ? $store_before->st_name : null;

            $store_after = DB::table('stores')->where('id', $request->_st_id)->select('st_name')->first();
            $store_after = $store_after ? $store_after->st_name : null;

            $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['st_id' => $request->_st_id]);

            if (!empty($check)) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($request->_po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'store', $store_before, $store_after, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function chooseTaxPo(Request $request)
    {
        DB::beginTransaction();
        try {
            $data_before = DB::table('purchase_orders')->where(['id' => $request->_po_id])->select('tax_id')->first()->tax_id;

            $tax_before = DB::table('taxes')->where('id', $data_before)->select('tx_code')->first();
            $tax_before = $tax_before ? $tax_before->tx_code : null;

            $tax_after = DB::table('taxes')->where('id', $request->_tax_id)->select('tx_code')->first();
            $tax_after = $tax_after ? $tax_after->tx_code : null;

            $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['tax_id' => $request->_tax_id]);

            if (!empty($check)) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($request->_po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'tax', $tax_before, $tax_after, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function chooseDataPerusahaan(Request $request)
    {
        DB::beginTransaction();
        try {
            $data_before = DB::table('purchase_orders')->where(['id' => $request->_po_id])->select('dp_id')->first()->dp_id;

            $dp_before = DB::table('data_perusahaan')->where('id', $data_before)->select('dp_name')->first();
            $dp_before = $dp_before ? $dp_before->dp_name : null;

            $dp_after = DB::table('data_perusahaan')->where('id', $request->_dp_id)->select('dp_name')->first();
            $dp_after = $dp_after ? $dp_after->dp_name : null;

            $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['dp_id' => $request->_dp_id]);

            if (!empty($check)) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($request->_po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'data_perusahaan', $dp_before, $dp_after, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function choosePaymentPo(Request $request)
    {
        DB::beginTransaction();
        try {
            $data_before = DB::table('purchase_orders')->where(['id' => $request->_po_id])->select('acc_id')->first()->acc_id;

            $acc_before = DB::table('accounts')->where('id', $data_before)->select('a_name')->first();
            $acc_before = $acc_before ? $acc_before->a_name : null;

            $acc_after = DB::table('accounts')->where('id', $request->_acc_id)->select('a_name')->first();
            $acc_after = $acc_after ? $acc_after->a_name : null;

            $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['acc_id' => $request->_acc_id]);

            if (!empty($check)) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($request->_po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'payment_account', $acc_before, $acc_after, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function chooseSupplierPo(Request $request)
    {
        DB::beginTransaction();
        try {
            $data_before = DB::table('purchase_orders')->where(['id' => $request->_po_id])->select('ps_id')->first()->ps_id;

            $supplier_before = DB::table('product_suppliers')->where('id', $data_before)->select('ps_name')->first();
            $supplier_before = $supplier_before ? $supplier_before->ps_name : null;
            $supplier_after = DB::table('product_suppliers')->where('id', $request->_ps_id)->select('ps_name')->first();
            $supplier_after = $supplier_after ? $supplier_after->ps_name : null;

            $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['ps_id' => $request->_ps_id]);

            if (!empty($check)) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($request->_po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'supplier', $supplier_before, $supplier_after, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function chooseStockType(Request $request)
    {
        DB::beginTransaction();
        try {
            $data_before = DB::table('purchase_orders')->where(['id' => $request->_po_id])->select('stkt_id')->first()->stkt_id;

            $stock_type_before = DB::table('stock_types')->where('id', $data_before)->select('stkt_name')->first();
            $stock_type_before = $stock_type_before ? $stock_type_before->stkt_name : null;

            $stock_type_after = DB::table('stock_types')->where('id', $request->_stkt_id)->select('stkt_name')->first();
            $stock_type_after = $stock_type_after ? $stock_type_after->stkt_name : null;

            $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])
                ->update(['stkt_id' => $request->_stkt_id]);

            if (!empty($check)) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($request->_po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'stock_type', $stock_type_before, $stock_type_after, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function descriptionPo(Request $request)
    {
        DB::beginTransaction();
        try {
            $description_before = DB::table('purchase_orders')->where(['id' => $request->_po_id])->select('po_description')->first()->po_description;
            $description_before = !empty($description_before) ? $description_before : null;

            $description_after = !empty($request->_po_description) ? $request->_po_description : null;

            $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['po_description' => $request->_po_description]);

            if (!empty($check)) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($request->_po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'description', $description_before, $description_after, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function shippingCostPo(Request $request)
    {
        DB::beginTransaction();
        try {
            $shipping_cost_before = DB::table('purchase_orders')->where(['id' => $request->_po_id])->select('po_shipping_cost')->first()->po_shipping_cost;
            $shipping_cost_before = !empty($shipping_cost_before) ? $shipping_cost_before : null;

            $shipping_cost_after = !empty($request->_po_shipping_cost) ? $request->_po_shipping_cost : null;

            $check = DB::table('purchase_orders')->where(['id' => $request->_po_id])->update(['po_shipping_cost' => $request->_po_shipping_cost]);
            if (!empty($check)) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($request->_po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'shipping_cost', $shipping_cost_before, $shipping_cost_after, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
                $r['po_shipping_cost'] = $request->_po_shipping_cost;
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
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
        $get_aging = Product::select('p_aging')->where('id', $pid)->get()->first()->p_aging;

        if (!$check_poa) {
            $poa_id = DB::table('purchase_order_articles')->insertGetId([
                'po_id' => $poid,
                'p_id' => $pid,
                'poa_reminder' => $get_aging
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

    //disini
    public function checkPoDetail(Request $request)
    {
        $po_id = $request->_po_id;
        $date_now = Carbon::now();
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
            $po_st_id = $draft->st_id;

            $poa_data = PurchaseOrderArticle::select('purchase_order_articles.id as poa_id', 'po_id', 'products.id as pid', 'br_name', 'p_price_tag', 'p_purchase_price', 'p_name', 'p_color', 'poa_discount', 'poa_extra_discount', 'poa_sub_discount', 'poa_reminder', 'article_id', 'article_id', 'products.created_at as item_added')
                ->leftJoin('products', 'products.id', '=', 'purchase_order_articles.p_id')
                //                ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->where(['po_id' => $po_id])->get();


            if (!empty($poa_data)) {
                $get_product = array();
                foreach ($poa_data as $poa) {
                    $poad_data = PurchaseOrderArticleDetail::select(
                        'purchase_order_article_details.id as poad_id',
                        'sz_name',
                        'ps_qty',
                        'ps_running_code',
                        'ps_sell_price',
                        'ps_price_tag',
                        'ps_purchase_price',
                        'poad_qty',
                        'poad_purchase_price',
                        'poad_total_price',
                        'pst_id',
                        'ps_barcode',
                        'p_id',
                        DB::raw('MAX(CASE WHEN ts_purchase_order_article_detail_statuses.u_id_approve IS NOT NULL THEN 1 ELSE 0 END) AS is_approved'),
                        DB::raw('SUM(CASE WHEN ts_purchase_order_article_detail_statuses.u_id_approve IS NOT NULL THEN poads_total_price ELSE 0 END) AS total_approved_price'),
                    )
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                        //                        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->where(['poa_id' => $poa->poa_id])
                        ->groupBy('purchase_order_article_details.id')
                        ->get();

                    // Step 2: Retrieve pls_qty from product_location_setups
                    $pstIds = $poad_data->pluck('pst_id'); // Get all unique pst_ids from the $poad_data
                    $pIds = $poad_data->pluck('p_id'); // Get all unique pst_ids from the $poad_data

                    $plsQtyData = ProductLocationSetup::whereIn('pst_id', $pstIds)
                        ->select('pst_id', DB::raw('SUM(pls_qty) as total_pls_qty'), 'p_id')
                        ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->join('stores', 'stores.id', '=', 'product_locations.st_id')
                        ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                        ->join('products', 'products.id', '=', 'product_stocks.p_id')
                        ->where(['stores.id' => $po_st_id])
                        ->groupBy('pst_id')
                        ->get();
                    $poad_data = $poad_data->map(function ($item) use ($plsQtyData) {
                        $item['total_pls_qty'] = $plsQtyData->where('pst_id', $item['pst_id'])->first()['total_pls_qty'] ?? 0;
                        //                        $item['total_pls_qty_all'] = $plsQtyDataAll->where('pst_id', $item['pst_id'])->first()['total_pls_qty_all'] ?? 0;
                        return $item;
                    });
                    if (!empty($poad_data)) {
                        // Define custom size order for T-shirt sizes
                        $tshirtSizesOrder = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];

                        $poa->subitem = $poad_data->sortBy(function ($item) use ($tshirtSizesOrder) {
                            $szName = $item->sz_name;

                            // Check if it's a T-shirt size by looking for it in the custom size order
                            if (in_array($szName, $tshirtSizesOrder)) {
                                // Return the index of the T-shirt size in the predefined order
                                return array_search($szName, $tshirtSizesOrder);
                            } elseif (is_numeric($szName)) {
                                // For numeric sizes, convert to integer for natural sorting
                                return (int) $szName;
                            } else {
                                // If sz_name doesn't match any known format, return a large value to sort it to the end
                                return PHP_INT_MAX;
                            }
                        });

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
        $user = auth()->user();
        $is_fintech = DB::table('user_divisions')
                ->where('id', $user->ud_id)
                ->where('ud_code', 'FINANCETEC')
                ->exists();
        $data = [
            'product' => $get_product,
            'is_fintech' => $is_fintech
        ];

        // dd($data);

        return view('app.purchase_order._purchase_order_article_detail', compact('data', 'is_fintech'));
    }

    public function reloadPoDetail(Request $request)
    {
        $r = array();
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
            $total_po = 0;
            //get total purchase order
            $total_po = PurchaseOrderArticle::query()
                ->where('po_id', $draft->id)
                ->join('purchase_order_article_details', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
                ->select(DB::raw('SUM(ts_purchase_order_article_details.poad_total_price) as total_purchase'))
                ->first()
                ->total_purchase;

            $r['status'] = '200';
            $r['po_id'] = $draft->id;
            $r['st_id'] = $draft->st_id;
            $r['ps_id'] = $draft->ps_id;
            $r['tax_id'] = $draft->tax_id;
            $r['dp_id'] = $draft->dp_id;
            $r['stkt_id'] = $draft->stkt_id;
            $r['po_description'] = $draft->po_description;
            $r['shipping_cost'] = $draft->po_shipping_cost;
            $r['po_invoice'] = $draft->po_invoice;
            $r['acc_id'] = $draft->acc_id;
            $r['dispute'] = $draft->dispute;
            $r['dispute_description'] = $draft->dispute_description;
            $r['status_dispute'] = $draft->status_dispute;
            $r['pay_date'] = $draft->pay_date;
            $r['due_date'] = $draft->due_date;
            $r['po_total_purchase'] = $draft->po_total_purchase;
            $r['po_total_qty'] = $draft->po_total_qty;
            $r['po_payment_amount'] = $draft->po_payment_amount;
            $r['bank_general'] = $draft->bank_general;
            $r['is_receivable'] = $draft->is_receivable;
            $r['claim_amount'] = $draft->claim_amount;
            $r['total_po'] = $total_po;
            $r['adjustment_amount'] = $draft->adjustment_amount;
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function uploadImageInvoice(Request $request)
    {

        $po_id = $request->_po_id;
        $mode = $request->_mode;
        $check = PurchaseOrder::where(['id' => $po_id])->exists();
        if ($check) {
            if ($request->hasFile('imageInvoices')) {
                foreach ($request->file('imageInvoices') as $file) {
                    $image = $file;

                    if ($mode == 'COD') {
                        $name = 'COD_' . pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $image->getClientOriginalExtension();
                    } else {
                        $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $image->getClientOriginalExtension();
                    }

                    $destinationPath = public_path('/upload/purchase_order_invoice');

                    // save destination path
                    $image->move($destinationPath, $name);

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

    public function uploadPembayaranInvoice(Request $request)
    {

        $po_id = $request->_po_id;
        $check = PurchaseOrder::where(['id' => $po_id])->exists();
        if ($check) {
            if ($request->hasFile('imageInvoices')) {
                foreach ($request->file('imageInvoices') as $file) {
                    $image = $file;
                    $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '_COD_' . time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('/upload/purchase_order_invoice');

                    // save destination path
                    $image->move($destinationPath, $name);

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

    public function uploadImageTransfer(Request $request)
    {
        $po_id = $request->_po_id;
        $check = PurchaseOrder::where(['id' => $po_id])->exists();
        if ($check) {
            if ($request->hasFile('imageTransfers')) {
                foreach ($request->file('imageTransfers') as $file) {
                    $image = $file;
                    $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('/upload/purchase_order_transfer');

                    // save destination path
                    $image->move($destinationPath, $name);

                    PurchaseOrderTransferImage::create([
                        'purchase_order_id' => $po_id,
                        'transfer_image' => $name,
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
        $st_id = $request->get('st_id');

        $timestamp = date('Ymd_Hi');

        $export = new PurchaseOrderArticleExport($po_id, $st_id);

        // Get current date and time (format: YYYYMMDD_HHmm)

        $fileName = 'purchase_order_article_' . $timestamp . '.xlsx';
        return Excel::download($export, $fileName);
    }

    public function deleteImageTransfer(Request $request)
    {
        $delete = PurchaseOrderTransferImage::where(['id' => $request->id])->first();

        if ($delete) {
            unlink(public_path('upload/purchase_order_transfer/' . $delete->transfer_image));
            $delete = PurchaseOrderTransferImage::where(['id' => $request->id])->delete();
        }

        $response = ['status' => $delete ? '200' : '400'];
        return response()->json($response);
    }

    public function totalPurchasePo(Request $request)
    {
        DB::beginTransaction();
        try {
            $po_id = $request->_po_id;
            $total_purchase = $request->_total_purchase;

            $before = DB::table('purchase_orders')->where(['id' => $po_id])->select('po_total_purchase')->first();
            $before = $before ? $before->po_total_purchase : null;

            $check = DB::table('purchase_orders')->where(['id' => $po_id])->update(['po_total_purchase' => $total_purchase]);

            if (!empty($check)) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'total_purchase', $before, $total_purchase, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function totalQtyPo(Request $request)
    {
        DB::beginTransaction();
        try {
            $po_id = $request->_po_id;
            $total_qty = $request->_total_qty;

            $before = DB::table('purchase_orders')->where(['id' => $po_id])->select('po_total_qty')->first();
            $before = $before ? $before->po_total_qty : null;

            $check = DB::table('purchase_orders')->where(['id' => $po_id])->update(['po_total_qty' => $total_qty]);

            if (!empty($check)) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'total_qty', $before, $total_qty, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function paymentAmountPo(Request $request)
    {
        DB::beginTransaction();
        try {
            $po_id = $request->_po_id;
            $payment_amount = $request->_payment_amount;

            $before = DB::table('purchase_orders')->where(['id' => $po_id])->select('po_payment_amount')->first();
            $before = $before ? $before->po_payment_amount : null;

            $check = DB::table('purchase_orders')->where(['id' => $po_id])->update(['po_payment_amount' => $payment_amount]);

            if (!empty($check)) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'payment_amount', $before, $payment_amount, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function statusdisputeSave(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'po_invoice' => 'required|string',
                'status_dispute' => 'required|in:0,1',
            ]);

            $po = PurchaseOrder::where('po_invoice', $request->po_invoice)->first();

            if (!$po) {
                DB::rollBack();
                return response()->json(['message' => 'PO tidak ditemukan'], 404);
            }

            $before = $po->status_dispute;
            $after = $request->status_dispute;

            $po->status_dispute = $after;
            $po->save();

            $purchaseOrderLog = new PurchaseOrderLog();
            $purchaseOrderLog->storePOLog($po->id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'status_dispute', $before, $after, date('Y-m-d H:i:s'));

            DB::commit();
            return response()->json(['message' => 'Status Dispute berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan Status Dispute: ' . $e->getMessage()], 500);
        }
    }

    public function exportpurchaseorderexport(Request $request)
    {
        return Excel::download(new PurchaseOrderRecevieExport($request), 'purchase_order_receive.xlsx');
    }

    public function changeBankGeneral(Request $request)
    {
        DB::beginTransaction();
        try {
            $po_id = $request->po_id;
            $bank_general = $request->bg_id;

            $before = DB::table('purchase_orders')->where(['id' => $po_id])->select('bank_general')->first();
            $before = $before ? $before->bank_general : null;

            $check = DB::table('purchase_orders')->where(['id' => $po_id])->update(['bank_general' => $bank_general]);

            if ($check) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'bank_general', $before, $bank_general, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function changeIsReceivable(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                '_po_id' => 'required|exists:purchase_orders,id',
                '_is_receivable' => 'required|in:0,1',
            ]);

            $po = PurchaseOrder::where('id', $request->_po_id)->first();

            if (!$po) {
                DB::rollBack();
                return response()->json(['message' => 'PO tidak ditemukan'], 404);
            }

            $before = $po->is_receivable;
            $after = $request->_is_receivable;

            $po->is_receivable = $after;
            $po->save();

            $purchaseOrderLog = new PurchaseOrderLog();
            $purchaseOrderLog->storePOLog($po->id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'is_receivable', $before, $after, date('Y-m-d H:i:s'));

            DB::commit();
            return response()->json(['message' => 'Status Receivable berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan Status Receivable: ' . $e->getMessage()], 500);
        }
    }

    public function changeClaimAmount(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                '_po_id' => 'required|exists:purchase_orders,id',
                '_claim_amount' => 'required|numeric|min:0',
            ]);

            $po = PurchaseOrder::where('id', $request->_po_id)->first();

            if (!$po) {
                DB::rollBack();
                return response()->json(['message' => 'PO tidak ditemukan'], 404);
            }

            $before = $po->claim_amount;
            $after = $request->_claim_amount;

            $po->claim_amount = $after;
            $po->save();

            $purchaseOrderLog = new PurchaseOrderLog();
            $purchaseOrderLog->storePOLog($po->id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'claim_amount', $before, $after, date('Y-m-d H:i:s'));

            DB::commit();
            return response()->json(['message' => 'Claim Amount berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan Claim Amount: ' . $e->getMessage()], 500);
        }
    }

    public function changeFinanceStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                '_po_id' => 'required|exists:purchase_orders,id',
            ]);

            $po = PurchaseOrder::where('id', $request->_po_id)->first();

            //parameters
            $has_payment_date = false;
            $has_stock_in_date = false;

            $payment_amount = 0;
            $claim_amount = 0;
            $po_qty = 0;

            $po_receive_amount = 0;
            $po_receive_qty = 0;

            $po_receive_amount_approved = 0;
            $po_receive_qty_approved = 0;

            $is_consigment = false;

            //query date and payment amount
            $query1 = DB::table('purchase_orders as po')
                ->join('purchase_order_articles as poa', 'poa.po_id', '=', 'po.id')
                ->join('purchase_order_article_details as poad', 'poad.poa_id', '=', 'poa.id')
                ->leftJoin('purchase_order_article_detail_statuses as poads', 'poads.poad_id', '=', 'poad.id')
                ->leftJoin('stock_types as stkt', 'stkt.id', '=', 'po.stkt_id')
                ->select(
                    DB::raw('MAX(ts_po.pay_date) as pay_date'),
                    DB::raw('MAX(CASE WHEN ts_poads.u_id_approve IS NOT NULL THEN ts_poads.created_at END) as stock_in_date'),
                    'po.po_payment_amount as payment_amount',
                    DB::raw('COALESCE(ts_po.claim_amount, 0) as claim_amount'),
                    'stkt.stkt_name as stock_type',
                    DB::raw('COALESCE(ts_po.adjustment_amount, 0) as adjustment_amount')
                )
                ->where('po.id', $request->_po_id)
                ->groupBy('po.id')
                ->first();

            //query qty and price pembelian
            $query2 = DB::table('purchase_orders as po')
                ->join('purchase_order_articles as poa', 'poa.po_id', '=', 'po.id')
                ->join('purchase_order_article_details as poad', 'poad.poa_id', '=', 'poa.id')
                ->select(
                    DB::raw('SUM(ts_poad.poad_qty) as total_qty'),
                    DB::raw('SUM(ts_poad.poad_total_price) as total_price')
                )
                ->where('po.id', $request->_po_id)
                ->groupBy('po.id')
                ->first();

            //query qty and price terima
            $query3 = DB::table('purchase_orders as po')
                ->join('purchase_order_articles as poa', 'poa.po_id', '=', 'po.id')
                ->join('purchase_order_article_details as poad', 'poad.poa_id', '=', 'poa.id')
                ->join('purchase_order_article_detail_statuses as poads', 'poads.poad_id', '=', 'poad.id')
                ->select(
                    DB::raw('SUM(CASE WHEN ts_poads.u_id_approve IS NULL THEN ts_poads.poads_qty ELSE 0 END) as qty_not_approve'),
                    DB::raw('SUM(CASE WHEN ts_poads.u_id_approve IS NULL THEN ts_poads.poads_total_price ELSE 0 END) as price_not_approve'),
                    DB::raw('SUM(CASE WHEN ts_poads.u_id_approve IS NOT NULL THEN ts_poads.poads_qty ELSE 0 END) as qty_approve'),
                    DB::raw('SUM(CASE WHEN ts_poads.u_id_approve IS NOT NULL THEN ts_poads.poads_total_price ELSE 0 END) as price_approve')
                )
                ->where('po.id', $request->_po_id)
                ->groupBy('po.id')
                ->first();

            //assign query result to variables
            $has_payment_date = !is_null($query1->pay_date);
            $has_stock_in_date = !is_null($query1->stock_in_date);
            $payment_amount = $query1->payment_amount ?? 0;
            $claim_amount = $query1->claim_amount ?? 0;

            $is_consigment = strtolower($query1->stock_type) == strtolower('CONSIGNMENT') ? true : false;

            $po_qty = $query2->total_qty ?? 0;
            $po_total_price = $query2->total_price ?? 0;

            $po_receive_amount_not_approved = $query3->price_not_approve ?? 0;
            $po_receive_qty_not_approved = $query3->qty_not_approve ?? 0;

            $po_receive_amount_approved = $query3->price_approve ?? 0;
            $po_receive_qty_approved = $query3->qty_approve ?? 0;

            $po_receive_amount = $po_receive_amount_approved;
            $po_receive_qty = $po_receive_qty_not_approved + $po_receive_qty_approved;

            $adjustment_amount = $query1->adjustment_amount ?? 0;

            $tolerance = 1000;

            $before = $po->finance_status;
            $after = null;

            //check and update is consigment
            if ($is_consigment) {
                $after = 'CONSIGNMENT';
                $po->finance_status = $after;
                $po->save();

                if ($before != $after) {
                    $purchaseOrderLog = new PurchaseOrderLog();
                    $purchaseOrderLog->storePOLog($po->id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'finance_status', $before, $after, date('Y-m-d H:i:s'));
                }

                DB::commit();
                return response()->json(['message' => 'Status Finance berhasil disimpan', 'status' => 200]);
            }

            //draft / pending
            if (!$has_payment_date && !$has_stock_in_date) {
                $after = 'DRAFT / PENDING';
                $po->finance_status = $after;
                $po->save();

                if ($before != $after) {
                    $purchaseOrderLog = new PurchaseOrderLog();
                    $purchaseOrderLog->storePOLog($po->id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'finance_status', $before, $after, date('Y-m-d H:i:s'));
                }

                DB::commit();
                return response()->json(['message' => 'Status Finance berhasil disimpan', 'status' => 200]);
            }

            //hutang partial receive
            if (!$has_payment_date && $has_stock_in_date && ($po_receive_qty < $po_qty)) {
                $after = 'HUTANG (PARTIAL RECEIVE)';
                $po->finance_status = $after;
                $po->save();

                if ($before != $after) {
                    $purchaseOrderLog = new PurchaseOrderLog();
                    $purchaseOrderLog->storePOLog($po->id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'finance_status', $before, $after, date('Y-m-d H:i:s'));
                }

                DB::commit();
                return response()->json(['message' => 'Status Finance berhasil disimpan', 'status' => 200]);
            }

            //hutang full receive
            if (!$has_payment_date && $has_stock_in_date && ($po_receive_qty == $po_qty)) {
                $after = 'HUTANG';
                $po->finance_status = $after;
                $po->save();

                if ($before != $after) {
                    $purchaseOrderLog = new PurchaseOrderLog();
                    $purchaseOrderLog->storePOLog($po->id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'finance_status', $before, $after, date('Y-m-d H:i:s'));
                }

                DB::commit();
                return response()->json(['message' => 'Status Finance berhasil disimpan', 'status' => 200]);
            }

            //piutang overpayment partial receive
            if ($has_payment_date && $has_stock_in_date && ($po_receive_qty < $po_qty)) {
                $after = 'PIUTANG (OVERPAYMENT PARTIAL)';
                $po->finance_status = $after;
                $po->save();

                if ($before != $after) {
                    $purchaseOrderLog = new PurchaseOrderLog();
                    $purchaseOrderLog->storePOLog($po->id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'finance_status', $before, $after, date('Y-m-d H:i:s'));
                }

                DB::commit();
                return response()->json(['message' => 'Status Finance berhasil disimpan', 'status' => 200]);
            }

            //piutang 0 receive
            if ($has_payment_date && !$has_stock_in_date && ($po_receive_qty == 0)) {
                $after = 'PIUTANG';
                $po->finance_status = $after;
                $po->save();

                if ($before != $after) {
                    $purchaseOrderLog = new PurchaseOrderLog();
                    $purchaseOrderLog->storePOLog($po->id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'finance_status', $before, $after, date('Y-m-d H:i:s'));
                }

                DB::commit();
                return response()->json(['message' => 'Status Finance berhasil disimpan', 'status' => 200]);
            }

            //lunas with tolerance condition
            //has payment date and stock in date and payment + claim amount equals total purchase amount with tolerance

            if ($po_receive_amount != 0 && $po_qty == $po_receive_qty_approved) {
                $difference = ($payment_amount + $claim_amount) - ($po_receive_amount + $po->adjustment_amount);
            } else {
                $difference = ($payment_amount + $claim_amount) - ($po_total_price + $po->adjustment_amount);
            }

            if ($has_payment_date && $has_stock_in_date && $difference <= $tolerance && $difference >= -$tolerance && ($po_qty == $po_receive_qty)) {
                $after = 'LUNAS';
                $po->finance_status = $after;
                $po->save();

                if ($before != $after) {
                    $purchaseOrderLog = new PurchaseOrderLog();
                    $purchaseOrderLog->storePOLog($po->id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'finance_status', $before, $after, date('Y-m-d H:i:s'));
                }

                DB::commit();
                return response()->json(['message' => 'Status Finance berhasil disimpan', 'status' => 200]);
            }

            if ($has_payment_date && $has_stock_in_date && $difference <= -$tolerance) {
                $after = 'HUTANG';
                $po->finance_status = $after;
                $po->save();

                if ($before != $after) {
                    $purchaseOrderLog = new PurchaseOrderLog();
                    $purchaseOrderLog->storePOLog($po->id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'finance_status', $before, $after, date('Y-m-d H:i:s'));
                }

                DB::commit();
                return response()->json(['message' => 'Status Finance berhasil disimpan', 'status' => 200]);
            }

            if ($has_payment_date && $has_stock_in_date && $difference > $tolerance) {
                $after = 'PIUTANG';
                $po->finance_status = $after;
                $po->save();

                if ($before != $after) {
                    $purchaseOrderLog = new PurchaseOrderLog();
                    $purchaseOrderLog->storePOLog($po->id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'finance_status', $before, $after, date('Y-m-d H:i:s'));
                }

                DB::commit();
                return response()->json(['message' => 'Status Finance berhasil disimpan', 'status' => 200]);
            }

            $after = 'UNKNOWN';
            $po->finance_status = $after;
            $po->save();

            DB::commit();
            return response()->json(['message' => 'Status Finance gagal disimpan', 'status' => 400]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan Status Finance: ' . $e->getMessage()], 500);
        }
    }

    public function updateAllFinanceStatus()
    {
        $purchaseOrders = PurchaseOrder::all();

        foreach ($purchaseOrders as $po) {
            $request = new Request();
            $request->merge(['_po_id' => $po->id]);
            $this->changeFinanceStatus($request);
        }

        return response()->json(['message' => 'Update semua status finance berhasil']);
    }

    public function getLogDatatables(Request $request)
    {
        if (request()->ajax()) {
            $po_id = $request->po_id;

            // Query for purchase_order type
            $purchaseOrderLogs = DB::table('purchase_order_logs')
                ->leftJoin('users', 'purchase_order_logs.user_id', '=', 'users.id')
                ->select(
                    'users.u_name',
                    DB::raw('NULL as article_id'),
                    DB::raw('NULL as p_name'),
                    DB::raw('NULL as p_color'),
                    DB::raw('NULL as sz_name'),
                    'purchase_order_logs.target_column',
                    'purchase_order_logs.before',
                    'purchase_order_logs.after',
                    'purchase_order_logs.created_at',
                    DB::raw('NULL as br_name'),
                    DB::raw('"purchase_order" as type')
                )
                ->where('purchase_order_logs.type', 'purchase_order')
                ->where('purchase_order_logs.po_id', $po_id);

            // Query for article type
            $articleLogs = DB::table('purchase_order_logs')
                ->leftJoin('users', 'purchase_order_logs.user_id', '=', 'users.id')
                ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_logs.target_item')
                ->leftJoin('products', 'products.id', '=', 'purchase_order_articles.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->select(
                    'users.u_name',
                    'products.article_id',
                    'products.p_name',
                    'products.p_color',
                    DB::raw('NULL as sz_name'),
                    'purchase_order_logs.target_column',
                    'purchase_order_logs.before',
                    'purchase_order_logs.after',
                    'purchase_order_logs.created_at',
                    'brands.br_name',
                    DB::raw('"article" as type')
                )
                ->where('purchase_order_logs.type', 'article')
                ->where('purchase_order_logs.po_id', $po_id);

            // Query for items type
            $itemsLogs = DB::table('purchase_order_logs')
                ->leftJoin('users', 'purchase_order_logs.user_id', '=', 'users.id')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_logs.target_item')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
                ->select(
                    'users.u_name',
                    'products.article_id',
                    'products.p_name',
                    'products.p_color',
                    'sizes.sz_name',
                    'purchase_order_logs.target_column',
                    'purchase_order_logs.before',
                    'purchase_order_logs.after',
                    'purchase_order_logs.created_at',
                    'brands.br_name',
                    DB::raw('"items" as type')
                )
                ->where('purchase_order_logs.type', 'items')
                ->where('purchase_order_logs.po_id', $po_id);

            // Union all queries and sort by created_at desc
            $allLogs = $purchaseOrderLogs
                ->union($articleLogs)
                ->union($itemsLogs)
                ->orderBy('created_at', 'desc');

            return datatables()->of($allLogs)
                ->editColumn('created_at', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('type', function ($data) {
                    if ($data->type == 'purchase_order') {
                        return 'Purchase Order';
                    } elseif ($data->type == 'article') {
                        return 'Article';
                    } elseif ($data->type == 'items') {
                        return 'SKU';
                    }
                    return $data->type;
                })
                ->editColumn('target_column', function ($data) {
                    $column = str_replace('poad', '', $data->target_column);
                    $column = str_replace('_', ' ', $column);
                    return ucwords($column);
                })
                ->addColumn('item_detail', function ($data) {
                    return trim(implode(' - ', array_filter([$data->article_id, $data->p_name, $data->p_color, $data->sz_name])));
                })
                ->editColumn('before', function ($data) {
                    if ($data->target_column == 'status_dispute') {
                        return $data->before == '1' ? 'Progress' : 'Closed';
                    }

                    if ($data->target_column == 'is_dispute') {
                        return $data->before == '1' ? 'Yes' : 'No';
                    }

                    if ($data->target_column == 'putaway') {
                        return $data->before == '1' ? 'Yes' : 'No';
                    }

                    if ($data->target_column == 'is_receivable') {
                        return $data->before == '1' ? 'Yes' : 'No';
                    }

                    return $data->before;
                })
                ->editColumn('after', function ($data) {
                    if ($data->target_column == 'status_dispute') {
                        return $data->after == '1' ? 'Progress' : 'Closed';
                    }

                    if ($data->target_column == 'is_dispute') {
                        return $data->after == '1' ? 'Yes' : 'No';
                    }

                    if ($data->target_column == 'putaway') {
                        return $data->after == '1' ? 'Yes' : 'No';
                    }

                    if ($data->target_column == 'is_receivable') {
                        return $data->after == '1' ? 'Yes' : 'No';
                    }

                    return $data->after;
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getRemainingPayment(Request $request)
    {
        $no_po = $request->po_invoice;
        //query qty and price pembelian
        //query date and payment amount
        $query1 = DB::table('purchase_orders as po')
            ->join('purchase_order_articles as poa', 'poa.po_id', '=', 'po.id')
            ->join('purchase_order_article_details as poad', 'poad.poa_id', '=', 'poa.id')
            ->leftJoin('purchase_order_article_detail_statuses as poads', 'poads.poad_id', '=', 'poad.id')
            ->leftJoin('stock_types as stkt', 'stkt.id', '=', 'po.stkt_id')
            ->select(
                DB::raw('MAX(ts_po.pay_date) as pay_date'),
                DB::raw('MAX(CASE WHEN ts_poads.u_id_approve IS NOT NULL THEN ts_poads.created_at END) as stock_in_date'),
                'po.po_payment_amount as payment_amount',
                DB::raw('COALESCE(ts_po.claim_amount, 0) as claim_amount'),
                'stkt.stkt_name as stock_type',
                DB::raw('COALESCE(ts_po.adjustment_amount, 0) as adjustment_amount'),
                DB::raw('COALESCE(ts_po.po_total_purchase, 0) as purchase_amount_no_item')
            )
            ->where('po.po_invoice', $no_po)
            ->groupBy('po.id')
            ->first();

        $query2 = DB::table('purchase_orders as po')
            ->join('purchase_order_articles as poa', 'poa.po_id', '=', 'po.id')
            ->join('purchase_order_article_details as poad', 'poad.poa_id', '=', 'poa.id')
            ->select(
                DB::raw('SUM(ts_poad.poad_qty) as total_qty'),
                DB::raw('SUM(ts_poad.poad_total_price) as total_price')
            )
            ->where('po.po_invoice', $no_po)
            ->groupBy('po.id')
            ->first();

        //query qty and price terima
        $query3 = DB::table('purchase_orders as po')
            ->join('purchase_order_articles as poa', 'poa.po_id', '=', 'po.id')
            ->join('purchase_order_article_details as poad', 'poad.poa_id', '=', 'poa.id')
            ->join('purchase_order_article_detail_statuses as poads', 'poads.poad_id', '=', 'poad.id')
            ->select(
                DB::raw('SUM(CASE WHEN ts_poads.u_id_approve IS NULL THEN ts_poads.poads_qty ELSE 0 END) as qty_not_approve'),
                DB::raw('SUM(CASE WHEN ts_poads.u_id_approve IS NULL THEN ts_poads.poads_total_price ELSE 0 END) as price_not_approve'),
                DB::raw('SUM(CASE WHEN ts_poads.u_id_approve IS NOT NULL THEN ts_poads.poads_qty ELSE 0 END) as qty_approve'),
                DB::raw('SUM(CASE WHEN ts_poads.u_id_approve IS NOT NULL THEN ts_poads.poads_total_price ELSE 0 END) as price_approve')
            )
            ->where('po.po_invoice', $no_po)
            ->groupBy('po.id')
            ->first();

        $poads_total_price = (int)$query3->price_approve ?? 0;

        // //convert to integer
        $poads_total_price_value = $poads_total_price;

        if ($query1) {
            $claim_amount = $query1->claim_amount;
            $payment_amount = $query1->payment_amount;
            $purchase_amount = $query1->purchase_amount_no_item;
            $total_po = $query2->total_price;
            // Calculate total_po based on whether total_po (with item) > 0
            $calculated_total_po = ($total_po > 0) ? $total_po : $purchase_amount;

            // dd(($poads_total_price_value + $po->adjustment_amount) == $calculated_total_po);

            if ($poads_total_price_value != 0 && $query2->total_qty == $query3->qty_approve) {
                $difference = ($payment_amount + $claim_amount) - ($poads_total_price_value + $query1->adjustment_amount);
            } else {
                $difference = ($payment_amount + $claim_amount) - ($calculated_total_po + $query1->adjustment_amount);
            }

            return response()->json([
                'status' => '200',
                'claim_amount' => $claim_amount,
                'payment_amount' => $payment_amount,
                'total_po' => $calculated_total_po,
                'remaining_payment' => $difference
            ]);
        }

        return response()->json([
            'status' => '400',
            'message' => 'PO not found'
        ]);
    }

    public function adjustmentAmountPo(Request $request)
    {
        DB::beginTransaction();
        try {
            $po_id = $request->_po_id;
            $adjustment_amount = $request->_adjustment_amount;

            $before = DB::table('purchase_orders')->where(['id' => $po_id])->select('adjustment_amount')->first();
            $before = $before ? $before->adjustment_amount : null;

            $check = DB::table('purchase_orders')->where(['id' => $po_id])->update(['adjustment_amount' => $adjustment_amount]);

            if (!empty($check)) {
                $purchaseOrderLog = new PurchaseOrderLog();
                $purchaseOrderLog->storePOLog($po_id, auth()->id(), PurchaseOrderLog::TYPE_PURCHASE_ORDER, 'adjustment_amount', $before, $adjustment_amount, date('Y-m-d H:i:s'));

                DB::commit();
                $r['status'] = '200';
            } else {
                DB::rollBack();
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }
}
