<?php

namespace App\Http\Controllers;

use App\Exports\ArticleReportExport;
use App\Exports\OnlineReportExport;
use App\Imports\CekDanaOnlineImport;
use App\Imports\PurchaseOrderExcelImport;
use App\Imports\StockLocationImport;
use App\Imports\TransactionOnlineImport;
use App\Models\OnlineTransactionDetails;
use App\Models\OnlineTransactions;
use App\Models\CekDanaOnline;
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

class CekDanaOnlineController extends Controller
{
    protected function validateAccess()
    {
        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => request()->segment(1),
                'status' => CekDanaOnline::select('order_status')
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

//        $store_onl = Store::where('st_name', 'like', '%ONLINE%')->get();
        $data = [
            'title' => $title,
            'subtitle' => 'Cek Dana Online',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'st_id' => Store::where('st_delete', '!=', '1')->where('st_name', 'like', '%ONLINE%')->orderByDesc('id')->pluck('st_name', 'id'),
            'std_id' => StoreTypeDivision::where('dv_delete', '!=', '1')->orderByDesc('id')->pluck('dv_name', 'id'),
        ];
        return view('app.cekdanaonline.cek_dana_online', compact('data'));
    }

    public function getDatatables(Request $request)
    {

        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = Auth::user()->st_id;
        }

        $data = DB::table('online_funds')
            ->select('online_funds.order_number as order_number', 'stores.st_name', 'platform_name', 'total_disburshed_amount', 'seller_voucher_discount', 'affiliate_cut', 'marketplace_commision_fee', 'service_fee', 'voucher_xtra_service_fee', 'cashback_service_fee', 'cashout_date', 'final_price', 'transaction_date')
            ->leftJoin('pos_transactions', 'online_funds.order_number', '=', 'pos_transactions.pos_invoice')
            ->leftJoin('stores', 'pos_transactions.st_id', '=', 'stores.id')
            ->union(
                DB::table('online_funds')
                    ->select('pos_transactions.pos_order_number as order_number', 'stores.st_name', 'platform_name', 'total_disburshed_amount', 'seller_voucher_discount', 'affiliate_cut', 'marketplace_commision_fee', 'service_fee', 'voucher_xtra_service_fee', 'cashback_service_fee', 'cashout_date', 'final_price', 'transaction_date')
                    ->leftJoin('pos_transactions', 'online_funds.order_number', '=', 'pos_transactions.pos_invoice')
                    ->leftJoin('stores', 'pos_transactions.st_id', '=', 'stores.id')
            );

        // dd($data->first());
        return DataTables::of($data)
            ->addColumn('admin_persentage', function ($data) {
                if ($data->final_price && $data->marketplace_commision_fee) {
                    return ($data->marketplace_commision_fee / $data->final_price * 100) . '%';
                }
            })
            ->addColumn('gox_persentage', function ($data) {
                if ($data->final_price && $data->voucher_xtra_service_fee) {
                    return ($data->voucher_xtra_service_fee / $data->final_price * 100) . '%';
                }
            })
            ->addColumn('status', function ($data) {
                return null;
            })
            ->rawColumns(['status'])
            ->addIndexColumn()
            ->make(true);
    }
//     public function exportDataOnline(Request $request)
//     {
//         try {
//             $branch = $request->get('branch');
//             $status = $request->get('status');
//             $date = $request->get('date');
//             $changeplatform = $request->get('changeplatform');
//             $exp = explode('|', $date);
//             $start = null;
//             $end = null;
//             if (!empty($exp[1])) {
//                 $start = $exp[0];
//                 $end = $exp[1];
//             } else {
//                 $start = $request->get('date');
//             }
//             // Mendapatkan tanggal dan waktu saat ini
//             $now = new \DateTime();
//             $timestamp = $now->format('d-m-Y_H.i.s');
//             $fileName = 'item_online_details' . $timestamp . '.xlsx';

//             return Excel::download(new OnlineReportExport($branch, $start, $end, $status, $changeplatform), $fileName);
//         } catch (\Exception $e) {
//             return $e->getMessage();
//         }
//     }

    public function getDetailDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(OnlineTransactionDetails::select('online_transaction_details.id as otd_id', 'to_id', 'products.p_name', 'ps_barcode', 'online_transaction_details.sku', 'brands.br_name', 'p_color', 'sz_name', 'online_transaction_details.sku', 'online_transaction_details.qty as to_qty', 'original_price as shopee_price', 'products.p_sell_price as jez_price', 'total_discount', 'price_after_discount as final_price', 'discount_seller', 'platform_name')
                ->join('product_stocks', 'product_stocks.ps_barcode', '=', 'online_transaction_details.sku')
                ->join('online_transactions', 'online_transactions.id', '=', 'online_transaction_details.to_id')
                ->join('products', 'products.id', '=', 'product_stocks.p_id')
                ->join('brands', 'brands.id', '=', 'products.br_id')
                ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where('online_transactions.id', '=', $request->to_id))
                ->editColumn('gap_price', function ($data) {
                    return $data->jez_price - $data->shopee_price;
                })
                ->editColumn('ns_before_admin', function ($data) {
                    return $data->shopee_price - $data->discount_seller;

                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function importData(Request $request)
    {
        try {
            if ($request->hasFile('importFile')) {
                $file = $request->file('importFile');

                $nama_file = rand() . '_cek_dana_' .$file->getClientOriginalName();

                $original_name = $file->getClientOriginalName();

                $st_id_form = $request->input('st_id_form');

                $file->move('online', $nama_file);

                $import = new CekDanaOnlineImport();
                $data = Excel::toArray($import, public_path('online/' . $nama_file));

                if (count($data) >= 0) {
                    $processData = $this->processImportData($data[0], $original_name, $st_id_form);

                    // Unlink (delete) the file after successful import
                    unlink(public_path('online/' . $nama_file));

                    $r['data'] = $file->getClientOriginalName();
                    $r['status'] = '200';

                } else {
                    $r['status'] = '419';
                }
            } else {
                $r['status'] = '400';
            }
            return json_encode($r);
        } catch (\Exception $e) {
            if (isset($nama_file)) {
                unlink(public_path('online/' . $nama_file));
            }
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
            return json_encode($r);
        }
    }

    private function processImportData($data, $original_name, $st_id_form)
    {
        $processedData = [];
        $type = strpos($original_name, 'SHOPEE') !== false ? 'SHOPEE' : 'TIKTOK';
        $platform = $type;

        $st_id = $st_id_form;

        if ($type === 'Shopee') {
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
                    'st_id' => 20,
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
                            $transaction = OnlineTransactions::create($rowData);

                            OnlineTransactions::where('id', $transaction->id)->update(['st_id' => Auth::user()->st_id]);
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

                        $id_trx = OnlineTransactions::select('id', 'order_number', 'time_print')
                            ->where('order_number', $order_number)
                            ->first();

                        if ($id_trx->time_print == NULL) {
                            OnlineTransactions::where('id', $id_trx->id)->update($rowUpdate);
                            $insert_id = $id_trx->id;
                        }
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
//                                'ns_before_admin' => $ns_before_admin,
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
                    'st_id' => '20',
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
                            $transaction = OnlineTransactions::create($rowData);

                            OnlineTransactions::where('id', $transaction->id)->update(['st_id' => Auth::user()->st_id]);
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

                        $id_trx = OnlineTransactions::select('id', 'order_number', 'time_print')
                            ->where('order_number', $order_number)
                            ->first();

                        if ($id_trx->time_print == NULL) {
                            OnlineTransactions::where('id', $id_trx->id)->update($rowUpdate);
                            $insert_id = $id_trx->id;
                        }
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
                $discount_seller = str_replace('.', '', $item[13]);
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
//                                'ns_before_admin' => $ns_before_admin,
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

