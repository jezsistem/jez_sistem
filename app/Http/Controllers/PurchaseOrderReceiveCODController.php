<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderInvoiceImage;
use App\Models\PurchaseOrderTransferImage;
use App\Models\User;
use App\Models\Tax;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderReceiveCODController extends Controller
{
    protected function validateAccess()
    {
        $segment = request()->segment(1);
        $slug = str_replace('_v2', '', $segment); // handle _v2 routes
        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')
            ->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => $slug
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
        $user = new User();
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
            'tax_id' => Tax::where('tx_delete', '!=', '1')->orderByDesc('id')->pluck('tx_code', 'id'),
            'segment' => request()->segment(1),
            'acc_id' => Account::where('a_delete', '!=', '1')->orderByDesc('id')->pluck('a_name', 'id'),
        ];
        return view('app.purchase_order_receive_cod.purchase_order_receive_cod', compact('data'));
    }

    public function indexUpdated()
    {
        $this->validateAccess();
        $user = new User();
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;

        $segmentSlug = str_replace('_v2', '', request()->segment(1));

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', $segmentSlug)->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'tax_id' => Tax::where('tx_delete', '!=', '1')->orderByDesc('id')->pluck('tx_code', 'id'),
            'segment' => request()->segment(1),
            'acc_id' => Account::where('a_delete', '!=', '1')->orderByDesc('id')->pluck('a_name', 'id'),
        ];
        return view('app.updated_purchase_order_receive_cod.purchase_order_receive_cod', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('purchase_order_article_detail_statuses')
                ->selectRaw("ts_purchase_order_article_detail_statuses.id as id, st_name, po_invoice, poads_invoice, invoice_date, ts_purchase_order_article_detail_statuses.created_at, u_name, u_id_approve, ts_purchase_order_article_detail_statuses.received_date,
                sum(ts_purchase_order_article_detail_statuses.poads_qty) as qty, acc_id, is_paid, ts_stores.id as st_id, ts_purchase_orders.id as po_id, ts_purchase_orders.bank_general as bank_general, ps_name, po_description, po_shipping_cost, pay_date, due_date,
                    ts_purchase_orders.stkt_id,
                    ts_purchase_orders.tax_id,
                    ts_stock_types.stkt_name,
                    ts_taxes.tx_name, ts_purchase_orders.po_payment_amount as payment_amount") // Ensure all fields are included
                ->leftJoin('users', 'users.id', '=', 'purchase_order_article_detail_statuses.u_id_receive')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
                ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
                ->leftJoin('product_suppliers', 'product_suppliers.id', '=', 'purchase_orders.ps_id')
                ->leftJoin('stores', 'stores.id', '=', 'purchase_orders.st_id')
                ->leftJoin('stock_types', 'stock_types.id', '=', 'purchase_orders.stkt_id') // Join for stkt_id
                ->leftJoin('taxes', 'taxes.id', '=', 'purchase_orders.tax_id') // Join for tax_id
                ->whereNotNull('poads_invoice')
                ->where('acc_id', 93)
                ->where('is_paid', 0)
                ->groupBy('poads_invoice');

            // Apply search filter for `po_invoice` and `st_code`
            if (!empty($request->search)) {
                $search = $request->search;
                $query->where(function ($instance) use ($search) {
                    $instance->orWhere('po_invoice', 'LIKE', "%$search%")
                        ->orWhere('st_name', 'LIKE', "%$search%")
                        ->orWhere('ps_name', 'LIKE', "%$search%")
                        ->orWhere('poads_invoice', 'LIKE', "%$search%");
                });
            }

            return datatables()->of($query)
                ->editColumn('poads_invoice_show', function ($d) {
                    return "<a class='btn btn-primary'>" . $d->poads_invoice . "</a>";
                })
                ->editColumn('invoice_date_show', function ($data) {
                    return date('d/m/Y', strtotime($data->invoice_date));
                })
                ->editColumn('receive_date_show', function ($data) {
                    if (empty($data->received_date)) {
                        return date('d/m/Y', strtotime($data->created_at));

                    }
                    return date('d/m/Y', strtotime($data->received_date));

                })
                ->editColumn('u_receive', function ($data) {
                    $check_invoice_cod = PurchaseOrderInvoiceImage::where('purchase_order_id', '=', $data->po_id)->where('invoice_image', 'LIKE', '%COD%')->count();
                    if (!empty($data->u_id_approve) && $data->acc_id == 93 && $data->is_paid == 0) {
                        return 'Diterima, Belum Dibayar';
                    } else if (!empty($data->u_id_approve) && $data->acc_id == 93 && $data->is_paid == 0 && $check_invoice_cod > 0){
                        return 'Diterima, Sudah Dibayar';
                    } else if (!empty($data->u_id_approve)) {
                        $name = DB::table('users')->where('id', '=', $data->u_id_approve)->first()->u_name;
                        return $name . '<br/>' . date('d/m/Y H:i:s', strtotime($data->created_at));
                    } else {
                        return 'Menunggu Approval';
                    }
                })
                ->rawColumns(['poads_invoice_show', 'u_receive'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getDetailDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(DB::table('purchase_order_article_detail_statuses')
                ->selectRaw("ts_purchase_order_article_detail_statuses.id, poads_invoice, 
                    u_id_approve, br_name, p_name, sz_name, p_color, stkt_name, poads_qty, 
                    poad_purchase_price, ts_product_stocks.ps_barcode, ts_product_stocks.ps_qty,
                    poad_total_price, ts_purchase_order_article_detail_statuses.created_at, 
                    ts_purchase_orders.id as po_id, ts_product_suppliers.ps_name as ps_name, poads_purchase_price, poads_total_price, 
                    ts_purchase_orders.stkt_id, ts_purchase_orders.tax_id, ts_purchase_orders.bank_general, ts_purchase_orders.acc_id") // Ensure all fields are included
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                ->join('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
                ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('product_suppliers', 'product_suppliers.id', '=', 'purchase_orders.ps_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('stock_types', 'stock_types.id', '=', 'purchase_order_article_detail_statuses.stkt_id')
                ->leftJoin('accounts', 'accounts.id', '=', 'purchase_orders.acc_id')
                ->where('poads_invoice', '=', $request->get('poads_invoice')))
                ->editColumn('delete', function ($d) {
                    if (empty($d->u_id_approve)) {
                        return "<a class='btn btn-danger' data-id='" . $d->id . "' id='delete_poads'>X</a>";
                    } else {
                        return '';
                    }
                })
                ->editColumn('created_at_show', function ($d) {
                    return date('d/m/Y H:i:s', strtotime($d->created_at));
                })
                ->rawColumns(['delete'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function uploadImageTransfer(Request $request)
    {

        $po_id = $request->_po_id;
        $mode = 'COD';
        $check = PurchaseOrder::where(['id' => $po_id])->exists();
        if ($check) {
            if ($request->hasFile('imageTransfers')) {
                foreach ($request->file('imageTransfers') as $file) {
                    $image = $file;

                    if ($mode == 'COD') {
                        $name = 'COD_' . pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $image->getClientOriginalExtension();
                    } else {
                        $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $image->getClientOriginalExtension();
                    }

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
                    // ->addColumn('action', function ($row) {
                    //     return '<a href="#" class="btn btn-danger btn-sm " id="delete-image-invoice" data-id="' . $row->id . '">Delete</a>';
                    // })
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

    public function getImageTransferDatatables(Request $request)
    {
        if ($request->ajax()) {
            $po_id = PurchaseOrderTransferImage::where('purchase_order_id', '=', $request->get('_po_id'))->exists();
            if ($po_id) {
                $images = PurchaseOrderTransferImage::select('id', 'transfer_image')
                    ->where('purchase_order_id', '=', $request->get('_po_id'));

                return datatables()->of($images)
                    ->addColumn('image', function ($row) {
                        if (empty($row->transfer_image)) {
                            return '<img src="' . asset('upload/image/no_image.png') . '"/>';
                        } else {
//                            return '<a href="'.asset('upload/purchase_order_transfer/'.$row->transfer_image).' target=_blank>$row->transfer_image</a>';
                            return '<a href="' . asset('upload/purchase_order_transfer/' . $row->transfer_image) . '" target="_blank">' . $row->transfer_image . '</a>';
                        }
                    })
                    ->addColumn('action', function ($row) {
                        return '<a href="#" class="btn btn-danger btn-sm " id="delete-image-transfer" data-id="' . $row->id . '">Delete</a>';
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


//    public function uploadImageInvoice(Request $request)
//    {
//
//        $po_id = $request->_po_id;
//        $check = PurchaseOrder::where(['id' => $po_id])->exists();
//        if ($check) {
//            if ($request->hasFile('imageInvoices')) {
//                foreach ($request->file('imageInvoices') as $file) {
//                    $image = $file;
//                    $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $image->getClientOriginalExtension();
//                    $destinationPath = public_path('/upload/purchase_order_invoice');
//
//                    // save destination path
//                    $image->move($destinationPath, $name);
//
//                    PurchaseOrderInvoiceImage::create([
//                        'purchase_order_id' => $po_id,
//                        'invoice_image' => $name,
//                    ]);
//                }
//            }
//        }
//
//        if (!empty($check)) {
//            $r['status'] = '200';
//        } else {
//            $r['status'] = '400';
//        }
//        return json_encode($r);
//    }

    // update Purchase Order Article Detail Status is_paid to true
    public function updateIsPaid(Request $request)
    {
        $affected = DB::table('purchase_order_article_detail_statuses')
            ->where('poads_invoice', $request->invoice)
            ->update(['is_paid' => 1]);


        if ($affected) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }

        return json_encode($r);
    }

    public function changePayDate(Request $request)
    {
        $po_id = $request->po_id;
        $pay_date = $request->pay_date;
        $check = PurchaseOrder::where(['id' => $po_id])->update(['pay_date' => $pay_date]);
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
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

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $search = $request->get('search');
            $date = $request->get('date');

            $query = DB::table('purchase_order_article_detail_statuses')
                ->selectRaw("
                    MAX(ts_purchase_order_article_detail_statuses.id) as id,
                    MAX(st_name) as st_name,
                    MAX(po_invoice) as po_invoice,
                    poads_invoice,
                    MAX(invoice_date) as invoice_date,
                    MAX(ts_purchase_order_article_detail_statuses.created_at) as created_at,
                    MAX(u_name) as u_name,
                    MAX(u_id_approve) as u_id_approve,
                    MAX(ts_purchase_order_article_detail_statuses.received_date) as received_date,
                    SUM(ts_purchase_order_article_detail_statuses.poads_qty) as qty,
                    MAX(acc_id) as acc_id,
                    MAX(is_paid) as is_paid,
                    MAX(ts_stores.id) as st_id,
                    MAX(ts_purchase_orders.id) as po_id,
                    MAX(ts_purchase_orders.bank_general) as bank_general,
                    MAX(ps_name) as ps_name,
                    MAX(po_description) as po_description,
                    MAX(po_shipping_cost) as po_shipping_cost,
                    MAX(pay_date) as pay_date,
                    MAX(due_date) as due_date,
                    MAX(ts_purchase_orders.stkt_id) as stkt_id,
                    MAX(ts_purchase_orders.tax_id) as tax_id,
                    MAX(ts_stock_types.stkt_name) as stkt_name,
                    MAX(ts_taxes.tx_name) as tx_name
                ")
                ->leftJoin('users', 'users.id', '=', 'purchase_order_article_detail_statuses.u_id_receive')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
                ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
                ->leftJoin('product_suppliers', 'product_suppliers.id', '=', 'purchase_orders.ps_id')
                ->leftJoin('stores', 'stores.id', '=', 'purchase_orders.st_id')
                ->leftJoin('stock_types', 'stock_types.id', '=', 'purchase_orders.stkt_id')
                ->leftJoin('taxes', 'taxes.id', '=', 'purchase_orders.tax_id')
                ->whereNotNull('purchase_order_article_detail_statuses.poads_invoice')
                ->where('purchase_orders.acc_id', 93)
                ->where('purchase_order_article_detail_statuses.is_paid', 0)
                ->groupBy('purchase_order_article_detail_statuses.poads_invoice');

            // Search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('purchase_orders.po_invoice', 'LIKE', "%$search%")
                        ->orWhere('stores.st_name', 'LIKE', "%$search%")
                        ->orWhere('product_suppliers.ps_name', 'LIKE', "%$search%")
                        ->orWhere('purchase_order_article_detail_statuses.poads_invoice', 'LIKE', "%$search%");
                });
            }

            // Date filter
            if (!empty($date)) {
                $dateParts = explode('|', $date);
                if (count($dateParts) == 2) {
                    $query->whereBetween('purchase_order_article_detail_statuses.created_at', [$dateParts[0], $dateParts[1]]);
                } elseif (count($dateParts) == 1) {
                    $query->whereDate('purchase_order_article_detail_statuses.created_at', $dateParts[0]);
                }
            }

            // Get total count before pagination
            $baseQuery = clone $query;
            $totalRecords = $baseQuery->get()->count();

            // Pagination parameters
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $offset = ($page - 1) * $perPage;

            // Apply pagination
            $data = $query->orderByRaw('MAX(ts_purchase_order_article_detail_statuses.id) DESC')
                ->offset($offset)
                ->limit($perPage)
                ->get()
                ->map(function ($item, $index) use ($offset) {
                    // Check if invoice image COD exists
                    $check_invoice_cod = PurchaseOrderInvoiceImage::where('purchase_order_id', '=', $item->po_id)
                        ->where('invoice_image', 'LIKE', '%COD%')
                        ->count();

                    // Format u_receive status
                    $u_receive = '';
                    if (!empty($item->u_id_approve) && $item->acc_id == 93 && $item->is_paid == 0 && $check_invoice_cod > 0) {
                        $u_receive = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Diterima, Sudah Dibayar</span>';
                    } elseif (!empty($item->u_id_approve) && $item->acc_id == 93 && $item->is_paid == 0) {
                        $u_receive = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Diterima, Belum Dibayar</span>';
                    } elseif (!empty($item->u_id_approve)) {
                        $name = DB::table('users')->where('id', '=', $item->u_id_approve)->first()->u_name ?? '';
                        $u_receive = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">' . $name . '<br/>' . date('d/m/Y H:i:s', strtotime($item->created_at)) . '</span>';
                    } else {
                        $u_receive = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu Approval</span>';
                    }

                    return [
                        'no' => $offset + $index + 1,
                        'st_name' => $item->st_name ?? '-',
                        'po_invoice' => $item->po_invoice ?? '-',
                        'ps_name' => $item->ps_name ?? '-',
                        'poads_invoice' => $item->poads_invoice ?? '-',
                        'invoice_date' => $item->invoice_date ? date('d/m/Y', strtotime($item->invoice_date)) : '-',
                        'receive_date' => $item->received_date ? date('d/m/Y', strtotime($item->received_date)) : ($item->created_at ? date('d/m/Y', strtotime($item->created_at)) : '-'),
                        'u_name' => $item->u_name ?? '-',
                        'u_receive' => $u_receive,
                        'qty' => number_format($item->qty ?? 0),
                        'created_at' => $item->created_at ? date('d-m-Y H:i:s', strtotime($item->created_at)) : '-',
                        'id' => $item->id ?? '',
                        'po_id' => $item->po_id ?? '',
                        'stkt_id' => $item->stkt_id ?? '',
                        'tax_id' => $item->tax_id ?? '',
                        'stkt_name' => $item->stkt_name ?? '',
                        'tx_name' => $item->tx_name ?? '',
                        'u_id_approve' => $item->u_id_approve ?? '',
                        'po_description' => $item->po_description ?? '',
                        'po_shipping_cost' => $item->po_shipping_cost ?? 0,
                        'pay_date' => $item->pay_date ?? '',
                        'due_date' => $item->due_date ?? '',
                        'bank_general' => $item->bank_general ?? '',
                        'acc_id' => $item->acc_id ?? ''
                    ];
                });

            return response()->json([
                'data' => $data,
                'total' => $totalRecords,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($totalRecords / $perPage)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
