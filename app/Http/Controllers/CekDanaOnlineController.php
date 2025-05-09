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
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
            $st_id = -1;
        }

        $data = DB::table('pos_transactions')
            ->select(
            DB::raw('COALESCE(ts_pos_transactions.pos_invoice, ts_online_funds.order_number) as order_number'),
            'online_funds.order_number as online_order_number',
            'online_transactions.order_number as import_trx_order_number',
            'pos_transactions.pos_real_price',
            'stores.st_name',
            'online_funds.platform_name',
            'online_funds.final_price',
            'online_funds.total_online_cut',
            'online_funds.seller_voucher_discount',
            'online_funds.affiliate_cut',
            'online_funds.marketplace_commision_fee',
            'online_funds.service_fee',
            'online_funds.voucher_xtra_service_fee',
            'online_funds.cashback_service_fee',
            'online_funds.cashout_date',
            'pos_transactions.created_at',
            'online_funds.total_disburshed_amount',
            'pos_transaction_details.created_at as jezpro_transaction_date',
            'online_transactions.online_print'
            )
            ->leftJoin('pos_transaction_details', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
            ->leftJoin('online_transactions', 'online_transactions.order_number', '=', 'pos_transactions.pos_invoice')
            ->rightJoin('online_funds', 'pos_transactions.pos_invoice', '=', 'online_funds.order_number')
            ->leftJoin('stores', function ($join) {
            $join->on('pos_transactions.st_id', '=', 'stores.id')
                 ->orOn('online_funds.st_id', '=', 'stores.id');
            })
            ->where(function ($query) use ($st_id) {
                $query->whereNotNull('pos_transactions.st_id')
                      ->where('pos_transactions.st_id', $st_id)
                      ->orWhere(function ($query) use ($st_id) {
                          $query->whereNotNull('online_funds.st_id')
                                ->where('online_funds.st_id', $st_id);
                      });
            });

        $d = $request->all();
        if (!empty($d['search'])) {
            $data->where(function ($query) use ($d) {
            $query->where('pos_transactions.pos_invoice', 'like', '%' . $d['search'] . '%')
                  ->orWhere('online_funds.order_number', 'like', '%' . $d['search'] . '%');
            });
        }
        if (!empty($d['status'])) {
            $data->where('pos_transactions.pos_status', $d['status']);
        }
        if (!empty($d['date_filter'])) {
            $dateRange = explode('|', $d['date_filter']);
            if (count($dateRange) == 2) {
            $data->whereBetween('pos_transactions.created_at', [$dateRange[0], $dateRange[1]])
                 ->orWhereBetween('online_funds.transaction_date', [$dateRange[0], $dateRange[1]]);
            } else {
                $data->whereDate('pos_transactions.created_at', $d['date_filter'])
                     ->orWhereDate('online_funds.transaction_date', $d['date_filter']);
            }
        }
        if (!empty($d['platform'])) {
            $data->where('online_funds.platform_name', $d['platform']);
        }

        $data = $data->union(
            DB::table('online_funds')
            ->select(
                DB::raw('COALESCE(ts_pos_transactions.pos_invoice, ts_online_funds.order_number) as order_number'),
                'online_funds.order_number as online_order_number',
                'online_transactions.order_number as import_trx_order_number',
                'pos_transactions.pos_real_price',
                'stores.st_name',
                'online_funds.platform_name',
                'online_funds.final_price',
                'online_funds.total_online_cut',
                'online_funds.seller_voucher_discount',
                'online_funds.affiliate_cut',
                'online_funds.marketplace_commision_fee',
                'online_funds.service_fee',
                'online_funds.voucher_xtra_service_fee',
                'online_funds.cashback_service_fee',
                'online_funds.cashout_date',
                'pos_transactions.created_at',
                'online_funds.total_disburshed_amount',
                'pos_transaction_details.created_at as jezpro_transaction_date',
                'online_transactions.online_print'
            )
            ->rightJoin('pos_transactions', 'online_funds.order_number', '=', 'pos_transactions.pos_invoice')
            ->leftJoin('pos_transaction_details', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
            ->leftJoin('online_transactions', 'online_transactions.order_number', '=', 'pos_transactions.pos_invoice')
            ->leftJoin('stores', function ($join) {
                $join->on('pos_transactions.st_id', '=', 'stores.id')
                 ->orOn('online_funds.st_id', '=', 'stores.id');
            })
            ->where(function ($query) use ($st_id) {
                $query->whereNotNull('pos_transactions.st_id')
                      ->where('pos_transactions.st_id', $st_id)
                      ->orWhere(function ($query) use ($st_id) {
                          $query->whereNotNull('online_funds.st_id')
                                ->where('online_funds.st_id', $st_id);
                      });
            })
            ->when(!empty($d['search']), function ($query) use ($d) {
                $query->where(function ($query) use ($d) {
                $query->where('pos_transactions.pos_invoice', 'like', '%' . $d['search'] . '%')
                      ->orWhere('online_funds.order_number', 'like', '%' . $d['search'] . '%');
                });
            })
            ->when(!empty($d['status']), function ($query) use ($d) {
                $query->where('pos_transactions.pos_status', $d['status']);
            })
            ->when(!empty($d['date_filter']), function ($query) use ($d) {
                $dateRange = explode('|', $d['date_filter']);
                if (count($dateRange) == 2) {
                $query->whereBetween('pos_transactions.created_at', [$dateRange[0], $dateRange[1]])
                      ->orWhereBetween('online_funds.transaction_date', [$dateRange[0], $dateRange[1]]);
                }
            })
            ->when(!empty($d['platform']), function ($query) use ($d) {
                $query->where('online_funds.platform_name', $d['platform']);
            })
        );

        // dd($data->first());
        return DataTables::of($data)
            ->addColumn('fee_persentage', function ($data) {
                if ($data->final_price && $data->total_online_cut && $data->total_online_cut != 0) {
                    return number_format(($data->total_online_cut / $data->final_price * 100), 2) . '%';
                }
                return '0.00%';
            })
            ->addColumn('seller_voucher_persentage', function ($data) {
                if ($data->final_price && $data->seller_voucher_discount && $data->seller_voucher_discount != 0) {
                    return number_format(($data->seller_voucher_discount / $data->final_price * 100), 2) . '%';
                }
                return '0.00%';
            })
            ->addColumn('diff_jezpro_mp', function ($data) {
                return $data->pos_real_price - $data->final_price;
            })
            ->addColumn('status', function ($data) {
                if (
                    $data->order_number &&
                    $data->online_order_number &&
                    $data->import_trx_order_number &&
                    $data->order_number == $data->online_order_number &&
                    $data->order_number == $data->import_trx_order_number &&
                    $data->online_print != 0
                ) {
                    return '<button class="btn btn-sm btn-success">Done</button>';
                }

                // if (!$data->order_number && $data->online_order_number) {
                //     return '<button class="btn btn-sm btn-danger">Belum di Trx</button>';
                // }

                if ($data->order_number && !$data->online_order_number) {
                    return '<button class="btn btn-sm btn-warning">Belum Cair</button>';
                }
                if ($data->online_print == 0) {
                    return '<button class="btn btn-sm btn-warning">Belum Trx</button>';
                }
                return '<button class="btn btn-sm btn-secondary">Unknown</button>';
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

    public function getDetail($order_number)
    {
        $data = DB::table('pos_transactions')
        ->select('pos_transactions.pos_invoice as order_number', 'pos_transactions.pos_real_price', 'stores.st_name', 'online_funds.platform_name', 'online_funds.final_price', 'online_funds.total_online_cut' ,'online_funds.seller_voucher_discount', 'online_funds.affiliate_cut', 'online_funds.marketplace_commision_fee', 'online_funds.service_fee', 'online_funds.voucher_xtra_service_fee', 'online_funds.cashback_service_fee', 'online_funds.cashout_date', 'online_funds.final_price', 'pos_transactions.created_at', 'online_funds.total_disburshed_amount','pos_transaction_details.created_at as jezpro_transaction_date')
        ->join('pos_transaction_details', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
        ->leftJoin('online_funds', 'pos_transactions.pos_invoice', '=', 'online_funds.order_number')
        ->leftJoin('stores', 'pos_transactions.st_id', '=', 'stores.id')
        ->where('pos_transactions.pos_invoice', $order_number);

        if (empty($data->first())) {
            $data = DB::table('online_funds')
            ->select('online_funds.order_number', 'pos_transactions.pos_real_price', 'stores.st_name', 'online_funds.platform_name', 'online_funds.final_price', 'online_funds.total_online_cut' ,'online_funds.seller_voucher_discount', 'online_funds.affiliate_cut', 'online_funds.marketplace_commision_fee', 'online_funds.service_fee', 'online_funds.voucher_xtra_service_fee', 'online_funds.cashback_service_fee', 'online_funds.cashout_date', 'online_funds.final_price', 'pos_transactions.created_at', 'online_funds.total_disburshed_amount','pos_transaction_details.created_at as jezpro_transaction_date')
            ->leftJoin('pos_transactions', 'pos_transactions.pos_invoice', '=', 'online_funds.order_number')
            ->leftJoin('pos_transaction_details', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
            ->leftJoin('stores', 'online_funds.st_id', '=', 'stores.id')
            ->where('online_funds.order_number', $order_number);
        }

        $data = $data->latest()->first();
        if (!$data) {

            return response()->json(['error' => 'Data not found'], 404);
        }

        $data->diff = isset($data->pos_real_price, $data->final_price) ? $data->pos_real_price - $data->final_price : null;
        $data->fee_persentage = isset($data->total_online_cut, $data->final_price) && $data->final_price != 0 
            ? number_format(($data->total_online_cut / $data->final_price * 100), 2) . '%' 
            : '0.00%';
        $data->seller_voucher_persentage = isset($data->seller_voucher_discount, $data->final_price) && $data->final_price != 0 
            ? number_format(($data->seller_voucher_discount / $data->final_price * 100), 2) . '%' 
            : '0.00%';
        $data->status = DB::table('pos_transactions')->where('pos_invoice', $order_number)->value('pos_status') ?? 'Unknown';

        return response()->json($data);

    }

    public function importData(Request $request)
    {
        try {
            if ($request->hasFile('importFile')) {
                $file = $request->file('importFile');

                $nama_file = rand() . '_cek_dana_' .$file->getClientOriginalName();

                $original_name = $file->getClientOriginalName();

                $st_id_form = $request->input('st_id_form');
                $platform_name = $request->input('platform_name_form');

                $file->move('online/cek_dana/', $nama_file);

                $import = new CekDanaOnlineImport();
                $data = Excel::toArray($import, public_path('online/cek_dana/' . $nama_file));

                if (count($data) >= 0) {
                    $processData = $this->processImportData($data[0], $platform_name, $st_id_form);

                    // Unlink (delete) the file after successful import
                    unlink(public_path('online/cek_dana/' . $nama_file));

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
                unlink(public_path('online/cek_dana/' . $nama_file));
            }
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
            return json_encode($r);
        }
    }

    private function processImportData($data, $platform_name, $st_id_form)
    {

        $st_id = $st_id_form;
        $type = $platform_name;

        foreach ($data as $index => $item) {
            if ($index === 0) continue;

            $order_number = trim($item[0]);

            $exists = DB::table('online_funds')
                ->where('order_number', $order_number)
                ->exists();

            if ($exists) {
                continue;
            }

            try {
                if (is_numeric($item[1])) {
                    $cashout_date = Carbon::instance(Date::excelToDateTimeObject($item[1]))->format('Y-m-d');
                } else {
                    $cashout_date = Carbon::createFromFormat('d/m/Y', $item[1])->format('Y-m-d');
                }
            } catch (\Exception $e) {
                $cashout_date = null;
            }
            $order_number = $item[0];
//            $cashout_date = \Carbon\Carbon::createFromFormat('d/m/Y', $item[1])->format('Y-m-d');
            $final_price = (double) $item[2];
            $total_disburshed_amount = (double) $item[3];
            $seller_voucher_discount = (double) $item[4];
            $affiliate_cut = (double) $item[5];
            $marketplace_commision_fee = (double) $item[6];
            $service_fee = (double) $item[7];
            $voucher_xtra_service_fee = (double) $item[8];
            $cashback_service_fee = (double) $item[9];
            $total_online_cut = $affiliate_cut + $marketplace_commision_fee + $service_fee + $voucher_xtra_service_fee + $cashback_service_fee;

            DB::table('online_funds')->insert([
                'st_id' => $st_id_form, // assuming $st_id_form passed from controller
                'platform_name' => 'Shopee', // example static value; replace if dynamic
                'order_number' => $order_number,
                'total_disburshed_amount' => $total_disburshed_amount,
                'final_price' => $final_price,
                'total_online_cut' => $total_online_cut,
                'seller_voucher_discount' => $seller_voucher_discount,
                'affiliate_cut' => $affiliate_cut,
                'marketplace_commision_fee' => $marketplace_commision_fee,
                'service_fee' => $service_fee,
                'voucher_xtra_service_fee' => $voucher_xtra_service_fee,
                'cashback_service_fee' => $cashback_service_fee,
                'cashout_date' => $cashout_date,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        return [
            'processedData' => 'success'
        ];

    }
}

