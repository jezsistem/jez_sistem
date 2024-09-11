<?php

namespace App\Http\Controllers;

use App\Imports\PurchaseOrderExcelImport;
use App\Imports\StockLocationImport;
use App\Imports\TransactionOnlineImport;
use App\Models\OnlineTransactionDetails;
use App\Models\OnlineTransactions;
use App\Models\PaymentMethod;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\ProductLocationSetup;
use App\Models\ProductLocationSetupTransaction;
use App\Models\ProductStock;
use App\Models\Size;
use App\Models\Store;
use App\Models\StoreTypeDivision;
use App\Models\TempMutasi;
use App\Models\TransaksiOnline;
use App\Models\TransaksiOnlineDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class TransaksiOnlineController extends Controller
{
    protected function validateAccess()
    {
        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => request()->segment(1),
                'status' => TransaksiOnline::select('order_status')
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
        $user = new User();
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => 'Transaksi Online',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'std_id' => StoreTypeDivision::where('dv_delete', '!=', '1')->orderByDesc('id')->pluck('dv_name', 'id'),
        ];
        return view('app.online_transaction.online_transaction_v2', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        //echo
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = Auth::user()->st_id;
        }
        if (request()->ajax()) {
            return DataTables::of(
                OnlineTransactions::select([
                    'online_transactions.id as to_id',
                    'online_transactions.order_number as to_order_number',
                    'no_resi',
                    'platform_name',
                    'order_date_created',
                    'sku',
                    'shipping_fee',
                    'total_payment',
                    'order_status',
                    'online_print'
                ])
                    ->leftJoin('online_transaction_details', 'online_transactions.id', '=', 'online_transaction_details.to_id')
                    ->leftJoin('product_stocks', 'product_stocks.ps_barcode', '=', 'online_transaction_details.sku')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->where('no_resi', '!=', '')
                    ->where('st_id', '=', $st_id)
                    ->orderBy('online_transactions.created_at', 'DESC')
                    ->groupBy('to_id')
            )
                ->editColumn('order_number', function ($data) {
                    return '<a class="text-white" href="#" data-to_id="' . $data->to_id . '" data-status="' . $data->order_status . '" data-num_order="' . $data->to_order_number . '" id="detail_btn"><span class="btn btn-sm btn-primary" >' . $data->to_order_number . '</span></a><br>';
                })
                ->editColumn('no_resi', function ($data) {
                    return $data->no_resi . '<br>' . ($data->online_print ? '<span style="color: red;" class="text-center">SUDAH CETAK</span>' : '');
                })
                ->editColumn('total_item', function ($data) {
                    $total_item = OnlineTransactionDetails::where('to_id', $data->to_id)->count();
                    return $total_item ? $total_item : '-';
                })
                ->editColumn('order_status', function ($data) {
                    return '<a class="text-white" href="#" data-pt_id="' . $data->order_status . '" id="detail_btn"><span class="btn btn-sm btn-primary" title="wsad">' . $data->order_status . '</span></a>';
                })
                ->rawColumns(['order_number', 'no_resi', 'total_item', 'order_status'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('no_resi', 'LIKE', "%$search%")
                                ->orWhere('online_transactions.order_number', 'LIKE', "%$search%");
                        });
                    }

                    if (!empty($request->get('status'))) {
                        $instance->where(function ($w) use ($request) {
                            $status = $request->get('status');

                            if ($status == 0) {
                                $w->orWhere('online_print', '=', "0");
                            } else if ($status == 1) {
                                $w->orWhere('online_print', '=', "1");
                            }
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function detailDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(OnlineTransactionDetails::select('online_transaction_details.id as otd_id', 'to_id', 'products.p_name', 'ps_barcode', 'online_transaction_details.sku', 'brands.br_name', 'p_color', 'sz_name', 'online_transaction_details.sku', 'online_transaction_details.qty as to_qty', 'original_price as shopee_price', 'products.p_sell_price as jez_price', 'total_discount', 'price_after_discount as final_price')
                ->Join('product_stocks', 'product_stocks.ps_barcode', '=', 'online_transaction_details.sku')
                ->Join('online_transactions', 'online_transactions.id', '=', 'online_transaction_details.to_id')
                ->Join('products', 'products.id', '=', 'product_stocks.p_id')
                ->Join('brands', 'brands.id', '=', 'products.br_id')
                ->Join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where('online_transactions.id', '=', $request->to_id))
                ->editColumn('article', function ($data) {
                    return '<span class="btn btn-primary">[' . $data->br_name . '] ' . $data->p_name . ' ' . $data->p_color . ' [' . $data->sz_name . ']</span>';
                })
                ->editColumn('gap_price', function ($data) {
                    return $data->jez_price - $data->shopee_price;
                })
                ->rawColumns(['article'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function cetak_invoice(Request $request)
    {
        $invoice = $request->orderNumber;
        $check = PosTransaction::where(['pos_invoice' => $invoice])->exists();
        $get_invoice = array();
        $dropshipper = null;

        if ($check) {
            $trx = PosTransaction::select(
                'pos_transactions.id as pt_id', 'cust_id', 'pos_cc_charge', 'cust_province', 'cust_city',
                'cust_subdistrict', 'sub_cust_id', 'u_name', 'pm_name', 'pm_id_partial', 'dv_name', 'cr_name',
                'pos_another_cost', 'pos_payment', 'pos_payment_partial', 'pos_ref_number', 'pos_card_number',
                'cust_name', 'cust_phone', 'cust_address', 'pos_invoice', 'st_name', 'st_phone', 'st_address',
                'pos_shipping', 'cr_id', 'pos_transactions.created_at as pos_created',
                'pos_transactions.pos_total_vouchers', 'pos_total_discount', 'cust_name')
                ->leftJoin('stores', 'stores.id', '=', 'pos_transactions.st_id')
                ->leftJoin('couriers', 'couriers.id', '=', 'pos_transactions.cr_id')
                ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pos_transactions.pm_id')
                ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
                ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
                ->leftJoin('users', 'users.id', '=', 'pos_transactions.u_id')
                ->where(['pos_invoice' => $invoice])
                ->first();

            $check_transaction_detail = PosTransactionDetail::
            leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where(['pt_id' => $trx->pt_id])->get();
            if (!empty($check_transaction_detail)) {
                $trx->subitem = $check_transaction_detail;
                if (!empty($trx->pm_id_partial)) {
                    $trx->pm_name_partial = PaymentMethod::select('pm_name')
                        ->where('id', $trx->pm_id_partial)->get()->first()->pm_name;
                }
                array_push($get_invoice, $trx);
            }
        }
        $stores = Auth::user()->st_id;

        $data_stores = Store::where('id', $stores)->get()->first();

        $stores_code = $data_stores->st_code;


        $online_transactions = [];


        $sku_current_print = OnlineTransactionDetails::where('order_number', $invoice)->get();

//        dd($sku_current_print);

        foreach ($sku_current_print as $key => $data) {
            $ps_barcode_id = ProductStock::where('ps_barcode', $data->ps_barcode)->first()->id;
            $pls_qty_current = ProductLocationSetup::where('pst_id', $ps_barcode_id)->where('pl_id', 'NOT LIKE', '%001%')->get()->first();

            if ($pls_qty_current) {
                $new_qty = $pls_qty_current->pls_qty - $data->qty;

                $paramsUpdate = [
                    'pls_qty' => $new_qty
                ];

                ProductLocationSetup::where('id', $pls_qty_current->id)->update($paramsUpdate);
            }

            $cek_keep_online = ProductLocationSetupTransaction::join('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->where('product_stocks.ps_barcode' , '=', $data->sku)
                ->where(function($query) {
                    $query->whereDate('product_location_setup_transactions.created_at', date('Y-m-d'))
                        ->orWhereDate('product_location_setup_transactions.created_at', Carbon::now()->subDay());
                })
//                ->where('plst_status', 'IN', ['WAITING ONLINE', 'DONE AMP'])
                ->count();

            

            $data_keep_online = ProductLocationSetupTransaction::select('product_location_setup_transactions.id as plst_id')
                ->join('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->where('product_stocks.ps_barcode' , '=', $data->sku)
                ->whereDate('product_location_setup_transactions.created_at', date('Y-m-d'))
                ->where(function($query) {
                    $query->whereDate('product_location_setup_transactions.created_at', date('Y-m-d'))
                        ->orWhereDate('product_location_setup_transactions.created_at', Carbon::now()->subDay());
                })
//                ->where('plst_status', 'IN', ['WAITING ONLINE', 'DONE AMP'])
                ->get()->first();
            
            dd($cek_keep_online);

            if ($cek_keep_online > 0) {
                $online_transactions[] = [
                    'ps_barcode' => $data->ps_barcode,
                    'qty' => $data->qty,
                    'id' => $data_keep_online->plst_id,
                    'online_id' => $data->to_id
                ];
            }
        }
        $sku_count = OnlineTransactionDetails::where('order_number', $invoice)->count();

        if($sku_count == count($online_transactions) || $sku_count < count($online_transactions)){
            foreach ($online_transactions as $transaction) {
                // Access individual fields from the $transaction array
                $paramsPlst = [
                    'plst_status' => 'DONE AMP',
                    'updated_at'  => date('Y-m-d H:i:s'),
                    'u_id_packer' => Auth::user()->id,
                    'pt_id'       => $transaction['online_id']
                ];


                ProductLocationSetupTransaction::where('id', $transaction['id'])->update($paramsPlst);
            }

            $params = [
                'online_print' => TRUE,
                'time_print'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ];
            OnlineTransactions::where('order_number', $invoice)->update($params);

            $response['status'] = 200;
        }
        else {
            $response['status'] = $sku_count . ' ' . count($online_transactions);
        }

        return $response;
    }

    public function cetak_nota($orderNumber)
    {
        $get_invoice = array();
        $check = OnlineTransactions::where(['order_number' => $orderNumber])->exists();

        if ($check) {
            $trx = OnlineTransactions::where(['order_number' => $orderNumber])->first();

            $check_transaction_detail = OnlineTransactionDetails::leftJoin('product_stocks', 'product_stocks.ps_barcode', '=', 'online_transaction_details.sku')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where(['to_id' => $trx->id])->get();
            if (!empty($check_transaction_detail)) {
                $trx->subitem = $check_transaction_detail;
                array_push($get_invoice, $trx);
            }
        }

        $stores = Auth::user()->st_id;

        $data_stores = Store::where('id', $stores)->get()->first();

        $stores_code = $data_stores->st_code;

        $data = [
            'title' => 'Invoice ' . $orderNumber,
            'invoice' => $orderNumber,
            'invoice_data' => $get_invoice,
            'store_code' => $stores_code,
            'segment' => request()->segment(1)
        ];
        return view('app.invoice.print_invoice_online', compact('data'));
    }

    public function importData(Request $request)
    {
        try {
            if ($request->hasFile('importFile')) {
                $file = $request->file('importFile');

                $nama_file = rand() . $file->getClientOriginalName();

                $original_name = $file->getClientOriginalName();

                $file->move('online', $nama_file);

                $st_id_form = $request->input('st_id_form');

                $import = new TransactionOnlineImport();
                $data = Excel::toArray($import, public_path('online/' . $nama_file));

                if (count($data) >= 0) {
                    $processData = $this->processImportData($data[0], $original_name, $st_id_form);
                    $r['data'] = $file->getClientOriginalName();;
                    $r['status'] = '200';

                    if ($r['status'] = '200') {

                    }
                } else {
                    $r['status'] = '419';
                }
            } else {
                $r['status'] = '400';
            }
            return json_encode($r);
        } catch (\Exception $e) {
            unlink(public_path('online/' . $nama_file));
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
            return json_encode($r);
        }
    }

    private function processImportData($data, $original_name, $st_id_form)
    {
        $processedData = [];
        $type = strpos($original_name, 'Shopee') !== false ? 'Shopee' : 'TikTok';
        $platform = $type;

        if ($type == 'Shopee') {
            foreach ($data as $item) {
                $order_number = $item[0];
                $order_status = $item[1];
                $reason_cancellation = $item[2];
                $no_resi = $item[3];
                $shipping_method = $item[4];
                $order_date_created = $item[5];
                $payment_date = $item[6];
                $payment_method = $item[7];

                $shipping_fee = str_replace(['IDR ', '.'], '', $item[15]);
                $total_payment = str_replace(['IDR ', '.'], '', $item[16]);
                $city = $item[17];
                $province = $item[18];

                $rowData = [
                    'st_id' => $st_id_form,
                    'order_number' => $order_number,
                    'order_status' => $order_status,
                    'reason_cancellation' => $reason_cancellation,
                    'no_resi' => $no_resi,
                    'platform_name' => 'Shopee',
                    'shipping_method' => $shipping_method,
                    'shipping_fee' => $shipping_fee,
                    'order_date_created' => $order_date_created,
                    'payment_date' => $payment_date,
                    'payment_method' => $payment_method,
                    'total_payment' => $total_payment,
                    'city' => $city,
                    'province' => $province,
                ];

                try {
                    $get_order_number = OnlineTransactions::where('order_number', $order_number)->count();

                    if ($get_order_number == 0) {
                        $processedData[] = $rowData;
                        if ($rowData['order_status'] != 'Cancel' && $rowData['order_status'] != 'Batal') {
                            OnlineTransactions::create($rowData);
                        }
                    } else {
                        $rowUpdate = [
                            'order_number' => $order_number,
                            'order_status' => $order_status,
                            'reason_cancellation' => $reason_cancellation,
                            'no_resi' => $no_resi,
                            'shipping_method' => $shipping_method,
                            'shipping_fee' => $shipping_fee,
                            'payment_method' => $payment_method,
                            'total_payment' => $total_payment,
                            'city' => $city,
                            'province' => $province,
                        ];

                        $id_trx = OnlineTransactions::select('id', 'order_number')
                            ->where('order_number', $order_number)
                            ->first();
                        OnlineTransactions::where('id', $id_trx->id)->update($rowUpdate);
                        $insert_id = $id_trx->id;
                    }
                } catch (\Exception $e) {
                    // Log the exception message
                    \Log::error('Error processing TikTok data: ' . $e->getMessage());
                }
            }

            foreach ($data as $item) {
                $order_number = $item[0];
                $order_status = $item[1];
                $original_price = str_replace('.', '', $item[8]);
                $price_after_discount = str_replace('.', '', $item[9]);
                $qty = $item[10];
                $sku = ltrim($item[11], 'X');
                $return_qty = $item[12];
                $total_discount = str_replace('.', '', $item[13]);
                $discount_seller = str_replace('.', '', $item[13]);
                $discount_platform = str_replace('.', '', $item[14]);

                try {
                    $to_id = OnlineTransactions::where('order_number', $order_number)->get()->first();

                    //cek current status
                    if ($order_status != 'Batal' || $order_status != 'Cancel') {
                        if ($to_id != null) {
                            $sku_exists = OnlineTransactionDetails::where('order_number', '=', $order_number)->where('sku', '=', $sku)->where('to_id', '=', $to_id->id)->exists();

                            $rowSku = [
                                'order_number' => $order_number,
                                'to_id' => $to_id->id,
                                'sku' => $sku,
                                'original_price' => $original_price,
                                'price_after_discount' => $price_after_discount,
                                'qty' => $qty,
                                'return_qty' => $return_qty,
                                'total_discount' => $total_discount,
                                'discount_seller' => $discount_seller,
                                'discount_platform' => $discount_platform,
                            ];

                            if (!$sku_exists) {
                                OnlineTransactionDetails::create($rowSku);
                            } else {
                                OnlineTransactionDetails::where('to_id', '=', $to_id->id)
                                    ->where('sku', $sku)
                                    ->update($rowSku);
                            }
                        }
                        // Delete duplicates using Eloquent
                        $duplicateRecords = OnlineTransactionDetails::where('order_number', $order_number)
                            ->where('sku', $sku)
                            ->where('qty', $qty)
                            ->where('to_id', $to_id->id)
                            ->orderBy('id', 'asc') // Order by ID to keep the first one
                            ->get();

                        if ($duplicateRecords->count() > 1) {
                            // Keep the first record and delete the rest
                            $idsToDelete = $duplicateRecords->pluck('id')->slice(1); // Get all except the first
                            OnlineTransactionDetails::whereIn('id', $idsToDelete)->delete();
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error processing TikTok SKU data: ' . $e->getMessage());
                }
            }

        } else { // TikTok
            foreach ($data as $item) {
                $order_number = $item[0];
                $order_status = $item[1];
                $reason_cancellation = $item[2];
                $no_resi = $item[3];
                $shipping_method = $item[4];
                $order_date_created = $item[5];
                $payment_date = $item[6];
                $payment_method = $item[7];

                $shipping_fee = str_replace(['IDR ', '.'], '', $item[15]);
                $total_payment = str_replace(['IDR ', '.'], '', $item[16]);
                $city = $item[17];
                $province = $item[18];

                $rowData = [
                    'st_id' => $st_id_form,
                    'order_number' => $order_number,
                    'order_status' => $order_status,
                    'reason_cancellation' => $reason_cancellation,
                    'no_resi' => $no_resi,
                    'platform_name' => 'TikTok',
                    'shipping_method' => $shipping_method,
                    'shipping_fee' => $shipping_fee,
                    'order_date_created' => $order_date_created,
                    'payment_date' => $payment_date,
                    'payment_method' => $payment_method,
                    'total_payment' => $total_payment,
                    'city' => $city,
                    'province' => $province,
                ];

                try {
                    $get_order_number = OnlineTransactions::where('order_number', $order_number)->count();

                    if ($get_order_number == 0) {
                        $processedData[] = $rowData;
                        if ($rowData['order_status'] != 'Canceled' && $rowData['order_status'] != 'Batal') {
                            OnlineTransactions::create($rowData);
                        }
                    } else {
                        $rowUpdate = [
                            'order_number' => $order_number,
                            'order_status' => $order_status,
                            'reason_cancellation' => $reason_cancellation,
                            'no_resi' => $no_resi,
                            'shipping_method' => $shipping_method,
                            'shipping_fee' => $shipping_fee,
                            'payment_method' => $payment_method,
                            'total_payment' => $total_payment,
                            'city' => $city,
                            'province' => $province,
                        ];

                        $id_trx = OnlineTransactions::select('id', 'order_number')
                            ->where('order_number', $order_number)
                            ->first();
                        OnlineTransactions::where('id', $id_trx->id)->update($rowUpdate);
                        $insert_id = $id_trx->id;
                    }
                } catch (\Exception $e) {
                    // Log the exception message
                    \Log::error('Error processing TikTok data: ' . $e->getMessage());
                }
            }

            foreach ($data as $item) {
                $order_number = $item[0];
                $order_status = $item[1];
                $original_price = str_replace(['IDR ', '.'], '', $item[8]);
                $price_after_discount = str_replace(['IDR ', '.'], '', $item[9]);
                $qty = $item[10];
                $sku = $sku = ltrim($item[11], 'X');
                $return_qty = $item[12];
                $total_discount = str_replace(['IDR ', '.'], '', $item[13]);
                $discount_seller = 0;
                $discount_platform = str_replace('.', '', $item[14]);

                try {
                    $to_id = OnlineTransactions::where('order_number', $order_number)->get()->first();

                    if ($order_status != 'Batal' || $order_status != 'Canceled') {
                        if ($to_id != null) {
                            $sku_exists = OnlineTransactionDetails::where('order_number', '=', $order_number)->where('sku', '=', $sku)->exists();

                            $rowSku = [
                                'order_number' => $order_number,
                                'to_id' => $to_id->id,
                                'sku' => $sku,
                                'original_price' => $original_price,
                                'price_after_discount' => $price_after_discount,
                                'qty' => $qty,
                                'return_qty' => $return_qty,
                                'total_discount' => $total_discount,
                                'discount_seller' => $discount_seller,
                                'discount_platform' => $discount_platform,
                            ];

                            if (!$sku_exists) {
                                OnlineTransactionDetails::create($rowSku);
                            } else {
                                OnlineTransactionDetails::where('to_id', $to_id->id)
                                    ->where('sku', $sku)
                                    ->update($rowSku);
                            }
                        }

                        $duplicateRecords = OnlineTransactionDetails::where('order_number', $order_number)
                            ->where('sku', $sku)
                            ->where('qty', $qty)
                            ->where('to_id', $to_id->id)
                            ->orderBy('id', 'asc') // Order by ID to keep the first one
                            ->get();

                        if ($duplicateRecords->count() > 1) {
                            // Keep the first record and delete the rest
                            $idsToDelete = $duplicateRecords->pluck('id')->slice(1); // Get all except the first
                            OnlineTransactionDetails::whereIn('id', $idsToDelete)->delete();
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error processing TikTok SKU data: ' . $e->getMessage());
                }
            }
        }

        return [
            'processedData' => $processedData
        ];

    }
}
