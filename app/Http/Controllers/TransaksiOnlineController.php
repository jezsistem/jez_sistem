<?php

namespace App\Http\Controllers;

use App\Imports\StockLocationImport;
use App\Imports\TransactionOnlineImport;
use App\Models\ProductStock;
use App\Models\Size;
use App\Models\TransaksiOnline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        return view('app.online_transaction.online_transaction', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        $sz_id = $request->sz_id;
        if (request()->ajax()) {
            return datatables()->of(TransaksiOnline::select(
                'id',
                'st_id',
                'platform_type',
                'order_number',
                'order_status',
                'reason_cancellation',
                'resi_number',
                'shipping_method',
                'ship_deadline',
                'ship_delivery_date',
                'order_date_created',
                'payment_date',
                'payment_method',
                'SKU',
                'original_price',
                'price_after_discount',
                'quantity',
                'return_quantity',
                'seller_note',
                'total_price',
                'total_discount',
                'shipping_fee',
                'voucher_seller',
                'cashback_coin',
                'voucher',
                'voucher_platform',
                'discount_seller',
                'discount_platform',
                'shopee_coin_pieces',
                'credit_card_discounts',
                'shipping_costs',
                'total_payment',
                'city',
                'province',
                'order_complete_at',
                'created_at',
                'updated_at'
            ))
//                ->filter(function ($instance) use ($request) {
//                    if (!empty($request->get('search'))) {
//                        $instance->where(function ($w) use ($request) {
//                            $search = $request->get('search');
//                            $w->orWhere('sz_name', 'LIKE', "%$search%")
//                                ->orWhere('sz_description', 'LIKE', "%$search%")
//                                ->orWhere('sz_schema', 'LIKE', "%$search%");
//                        });
//                    }
//                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function importData(Request $request)
    {
        try {
            if ($request->hasFile('importFile')) {
                $file = $request->file('importFile');

                $original_nama_file = $file->getClientOriginalName();

                $nama_file = rand() . $original_nama_file;

                $file->move('excel', $nama_file);

                $import = new TransactionOnlineImport();
//                $data = Excel::toArray($import, public_path('excel/' . $nama_file));

                $spreadsheet = IOFactory::load(public_path('excel/' . $nama_file));
                $sheet = $spreadsheet->getActiveSheet();
                $data = [];

                foreach ($sheet->getRowIterator(2) as $row) {
                    $rowData = [];
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);

                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getValue();
                    }

                    $data[] = $rowData;
                }


                if (count($data) >= 0) {
                    $processData = $this->processImportData($data, $original_nama_file);
                    $r['data'] = $processData;
                    $r['status'] = '200';
                } else {
                    $r['status'] = '419';
                }
            } else {
                $r['status'] = '400';
            }

            unlink(public_path('excel/' . $nama_file));

            return json_encode($r);
        } catch (\Exception $e) {
            unlink(public_path('excel/' . $nama_file));
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
            return json_encode($r);
        }
    }

    private function processImportData($data, $originalName)
    {

        $missingBarcode = array();
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
                // get id from barcode
//
//                $product_id = ProductStock::where('ps_barcode', 'LIKE', '%' . $sku . '%')->first();
//                $stock = $product_id->ps_qty;

                $rowData = [
                    'platform_type' => $type,
                    'order_number' => $orderNum,
                    'order_status' => $orderStatus,
                    'reason_cancellation' => $cancelReason,
                    'resi_number' => $resiNo,
                    'shipping_method' => $shippingMethod,
                    'ship_deadline' => $shipDeadline,
                    'ship_delive' => $shipDelive,
                    'order_created' => $orderCreated,
                    'payment_date' => $paymentDate,
                    'payment_method' => $paymentMethod,
                    'sku' => $sku,
                    'original_price' => $originalPrice,
                    'price_after' => $PriceAfter,
                    'qty' => $qty,
                    'return_qty' => $returnQty,
                    'total' => $total,
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

                TransaksiOnline::create($rowData);
            }
//        } else { //TikTok
//            foreach ($data as $item) {
//                $orderNum = $item[0];
//                $orderStatus = $item[1];
//                $orderSubStatus = $item[2];
//                $cancelType = $item[3];
//                $cancelBy = $item[30];
//                $resiNo = $item[34];
//                $sku = $item[6];
//                $cancelReason = $item[31];
//                $shippingMethod = $item[36];
//                $orderCreated = $item[24];
//                $paymentDate = $item[25];
//                $paymentMethod = $item[49];
//                $originalPrice = $item[11];
//                $PriceAfter = $item[13];
//                $qty = $item[9];
//                $returnQty = $item[10];
//                $returnQty = $item[10];
//                $sellerNote = $item[53];
//                $sellerNote = $item[53];
//                $totalDicount = $item[14];
//                $shippingFee = $item[16];
//                $discountSeller = $item[16];
//                $discountPlatform = $item[17];
//                $totalPayment = $item[22];
//                $city = $item[44];
//                $province = $item[43];
//                // get id from barcode
//
//                $product_id = ProductStock::where('ps_barcode', 'LIKE', '%' . $sku . '%')->first();
//                $stock = $product_id->ps_qty;
//
//                $rowData = [
//                    'platform_type' => $type,
//                    'order_number'  => $orderNum,
//                    'order_status'  => $orderStatus,
//                    'reason_cancellation' => $cancelReason,
//                    'resi_number'  => $resiNo,
//                    'shipping_method' => $shippingMethod,
//                    'ship_delivery_date'  => $shipDeadline,
//                    'order_date_created' => $orderCreated,
//                    'payment_date' => $paymentDate,
//                    'payment_method' => $paymentMethod,
//                    'SKU' => $sku,
//                    'original_price' => $originalPrice,
//                    'price_after_discount' => $originalPrice,
//                    'quantity' => $qty,
//                    'return_quantity' => $returnQty,
//                    'seller_note' => $sellerNote,
//                    'total_price' => $total,
//                    'total_discount' => $totalDicount,
//                    'shipping_fee' => $shippingCosts,
//                    'discount_seller' => $discountSeller,
//                    'discount_platform' => $discountPlatform,
//                    'total_payment' => $totalPayment,
//                    'city' => $city,
//                    'province' => $province,
//                    'order_complete_at',
//                    'created_at',
//                    'updated_at'
//                ];
//                $processedData[] = $rowData;
//
//                TransaksiOnline::create($rowData);
//            }
        }

        return [
            'processedData' => $processedData,
            'missingBarcode' => $missingBarcode
        ];
    }
}
