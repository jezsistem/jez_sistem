<?php

namespace App\Http\Controllers;

use App\Imports\PurchaseOrderExcelImport;
use App\Imports\StockLocationImport;
use App\Imports\TransactionOnlineImport;
use App\Models\OnlineTransactionDetails;
use App\Models\OnlineTransactions;
use App\Models\PosTransactionDetail;
use App\Models\ProductStock;
use App\Models\Size;
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
//            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'subtitle' => 'tes',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        return view('app.online_transaction.online_transaction_v2', compact('data'));
    }

    public function getDatatables(Request $request)
    {
//        if (!empty($request->st_id)) {
//            $st_id = $request->st_id;
//        } else {
//            $st_id = Auth::user()->st_id;
//        }
        if(request()->ajax()) {
            return DataTables::of(
                OnlineTransactions::select([
                    'online_transactions.id as to_id',
                    'online_transactions.order_number as to_order_number',
                    'no_resi',
                    'platform_name',
                    'order_date_created',
                    'sku',
                    'total_payment',
                    'order_status'
                ])
                    ->leftJoin('online_transaction_details', 'online_transactions.id', '=', 'online_transaction_details.to_id')
                    ->leftJoin('product_stocks', 'product_stocks.ps_barcode', '=', 'online_transaction_details.sku')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->groupBy('to_id')
            )
                ->editColumn('order_number', function ($data) {
                    return '<a class="text-white" href="#" data-to_id="'.$data->to_id.'" data-status="'.$data->order_status.'" data-num_order="'.$data->to_order_number.'" id="detail_btn"><span class="btn btn-sm btn-primary" >'.$data->to_order_number.'</span></a>';
                })
                ->editColumn('total_item', function ($data) {
                    $total_item = OnlineTransactionDetails::where('to_id', $data->to_id)->count();
                    return $total_item ? $total_item : '-';
                })
                ->editColumn('order_status', function ($data) {
                    return '<a class="text-white" href="#" data-pt_id="'.$data->order_status.'" id="detail_btn"><span class="btn btn-sm btn-primary" title="wsad">'.$data->order_status.'</span></a>';
                })
                ->rawColumns(['order_number', 'total_item', 'order_status'])
                ->addIndexColumn()
                ->make(true);
        }

    }

    public function detailDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(OnlineTransactionDetails::select('online_transaction_details.id as otd_id', 'to_id', 'products.p_name', 'brands.br_name', 'p_color', 'sz_name', 'ps_barcode', 'online_transaction_details.qty as to_qty', 'original_price as shopee_price', 'products.p_sell_price as jez_price', 'total_discount', 'price_after_discount as final_price')
                ->leftJoin('product_stocks', 'product_stocks.ps_barcode', '=', 'online_transaction_details.sku')
                ->leftJoin('online_transactions', 'online_transactions.id', '=', 'online_transaction_details.to_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
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

    public function importData(Request $request)
    {
        try {
            if ($request->hasFile('importFile')) {
                $file = $request->file('importFile');

                $nama_file = rand() . $file->getClientOriginalName();

                $original_name = $file->getClientOriginalName();

                $file->move('excel', $nama_file);

                $import = new TransactionOnlineImport();
                $data = Excel::toArray($import, public_path('excel/' . $nama_file));


                if (count($data) >= 0) {
                    $processData = $this->processImportData($data[0], $original_name);
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

            if (file_exists(public_path('excel/' . $nama_file))) {
                unlink(public_path('excel/' . $nama_file));
            }


            return json_encode($r);
        } catch (\Exception $e) {
            unlink(public_path('excel/' . $nama_file));
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
            return json_encode($r);
        }
    }

    private function processImportData($data, $original_name)
    {
        $processedData = [];
        $type = strpos($original_name, 'Order') !== false ? 'Shopee' : 'Tiktok';
        $platform = $type;

        if ($type == 'Shopee'){
            foreach ($data as $item) {
                $order_number = $item[0];
                $order_status = $item[1];
                $reason_cancellation = $item[2];
                $no_resi = $item[4];
                $shipping_method = $item[5];
                $order_date_created = $item[9];
                $payment_date = $item[10];
                $payment_method = $item[11];

                $shipping_fee = $item[35];
                $total_payment = $item[38];
                $city = $item[46];
                $province = $item[47];

                $rowData = [
                    'order_number' => $order_number,
                    'order_status' => $order_status,
                    'reason_cancellation' => $reason_cancellation,
                    'no_resi' => $no_resi,
                    'platform_name' => $platform,
                    'shipping_method' => $shipping_method,
                    'shipping_fee' => $shipping_fee,
                    'order_date_created' => $order_date_created,
                    'payment_date' => $payment_date,
                    'payment_method' => $payment_method,
                    'total_payment' => $total_payment,
                    'city' => $city,
                    'province' => $province
                ];

                $get_order_number = OnlineTransactions::where('order_number', $order_number)->count();

                if ($get_order_number == 0) {
                    $processedData[] = $rowData;
                    if ($rowData['order_status'] != 'Cancel' || $rowData['order_status'] != 'Batal' || $rowData['order_status'] != 'Cancel')
                    OnlineTransactions::create($rowData);
                } else {
                    $id_trx = OnlineTransactions::select('id', 'order_number')
                        ->where('order_number', $order_number)
                        ->first();
                    OnlineTransactions::where('id', $id_trx->id)->update($rowData);
                    $insert_id = $id_trx->id;
                }
            }

            foreach ($data as $item){
                $order_number = $item[0];
                $original_price = $item[16];
                $price_after_discount = $item[20];
                $qty = $item[18];
                $sku = $item[14];
                $return_qty = $item[19];
                $total_discount = $item[21];
                $discount_seller = $item[22];
                $discount_platform = $item[23];

                $to_id = OnlineTransactions::select('id')->where('order_number', $order_number)->get()->first();
                $rowSku = [
                    'order_number' => $order_number,
                    'to_id' => $to_id->id,
                    'sku' => $sku,
                    'original_price' => str_replace('.', '', $original_price),
                    'price_after_discount' =>  str_replace('.', '', $price_after_discount),
                    'qty' => $qty,
                    'return_qty' => $return_qty,
                    'total_discount' =>  str_replace('.', '', $total_discount),
                    'discount_seller' =>  str_replace('.', '', $discount_seller),
                    'discount_platform' =>  str_replace('.', '', $discount_platform),
                ];

                if ($get_order_number == 0) {
                    OnlineTransactionDetails::create($rowSku);
                } else {
                    $id_trx_sku = OnlineTransactionDetails::select('id')->where('order_number', $order_number)->where('sku', $sku)->get()->first();
                    OnlineTransactionDetails::where('id', $id_trx_sku->id)->update($rowSku);
                }
            }
        }
        return [
            'processedData' => $processedData
        ];
    }


    private function processImportData2(array $data, $originalName)
    {
        $processedData = [];
        $type = strpos($originalName, 'Order') !== false ? 'Shopee' : 'Tiktok';

        //shopee
        if ($type == 'Shopee') {
            foreach ($data as $item) {
                $orderNum = $item[0];
                $orderStatus = $item[1];
                $cancelReason = $item[2];
                $resiNo = $item[4];
                $shippingMethod = $item[5];
                $shipDeadline = $item[7];
                $shipDelive = $item[8];
                $orderCreated = $item[9];
                $paymentDate = $item[10];
                $paymentMethod = $item[11];
                $sku = $item[14];
                $originalPrice = $item[16];
                $PriceAfter = $item[17];
                $qty = $item[18];
                $returnQty = $item[19];
                $total = $item[20];
                $totalDicount = $item[21];
                $discountSeller = $item[22];
                $voucherSeller = $item[27];
                $cashbackCoin = $item[28];
                $voucherPlatform = $item[29];
                $discountPlatform = $item[27];
                $shopeeCoinPieces = $item[31];
                $creditCardDiscounts = $item[34];
                $shippingCosts = $item[35];
                $totalPayment = $item[38];
                $city = $item[46];
                $province = $item[47];
                $orderCompleteAt = $item[48];

                $transaksi = TransaksiOnline::where('order_number', $orderNum)->get();

                $rowCount = $transaksi->count();

                $rowData = [
                    'platform_type' => $type,
                    'order_number' => $orderNum,
                    'resi_number' => $resiNo,
                    'shipping_method' => $shippingMethod,
                    'ship_deadline' => $shipDeadline,
                    'ship_delivery_date' => $shipDelive,
                    'order_date_created' => $orderCreated,
                    'payment_method' => $paymentMethod,
                    'SKU' => $sku,
                    'original_price' => $originalPrice,
                    'price_after_discount' => $PriceAfter,
                    'quantity' => $qty,
                    'total_price' => $total,
                    'total_discount' => $totalDicount,
                    'discount_seller' => $discountSeller,
                    'voucher_seller' => $voucherSeller,
                    'cashback_coin' => $cashbackCoin,
                    'voucher_platform' => $voucherPlatform,
                    'discount_platform' => $discountPlatform,
                    'shopee_coin_pieces' => $shopeeCoinPieces,
                    'credit_card_discounts' => $creditCardDiscounts,
                    'shipping_costs' => $shippingCosts,
                    'total_payment' => $totalPayment,
                    'city' => $city,
                    'province' => $province,
                    'order_complete_at' => $orderCompleteAt
                ];
                $processedData[] = $rowData;


                if ($rowCount > 0) {
                    $newTransaksi = TransaksiOnline::where('order_number', $orderNum)->update($rowData);

                    $rawDetail = [
                        'to_id' => $newTransaksi->id,
                        'order_status' => $orderStatus,
                        'reason_cancellation' => $cancelReason,
                        'payment_date' => $paymentDate,
                        'return_quantity' => $returnQty,
                    ];
                    $processedDetail[] = $rawDetail;
                    TransaksiOnlineDetail::create($rawDetail);
                } else {
                    if (!empty($rowData['resi_number'])) {
                        $newTransaksi = TransaksiOnline::create($rowData);

                        $rawDetail = [
                            'to_id' => $newTransaksi->id,
                            'order_status' => $orderStatus,
                            'reason_cancellation' => $cancelReason,
                            'payment_date' => $paymentDate,
                            'return_quantity' => $returnQty,
                        ];
                        $processedDetail[] = $rawDetail;

                        TransaksiOnlineDetail::create($rawDetail);
                    }
                }

                $product_id = ProductStock::where('ps_barcode', 'LIKE', '%' . $sku . '%')->first();
                $stock = $product_id->ps_qty;

                $newStock = $stock - $qty;
                ProductStock::where('ps_barcode', 'LIKE', '%' . $sku . '%')->update(['ps_qty' => $newStock]);
            }
        }

        return [
            'processedData' => $processedDetail
        ];
    }
}
