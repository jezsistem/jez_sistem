<?php

namespace App\Http\Controllers;

use App\Exports\ArticleReportExport;
use App\Exports\OnlineReportExport;
use App\Exports\TransactionOnlineSettleExport;
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
        $this->runStoredProcedureCekDanaOnline();
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = -1;
        }

        if (!$request->filter_trx_date) {
            $request_filter_trx_date = date('Y-m-d') . '|' . date('Y-m-d');
        } else {
            $request_filter_trx_date = $request->filter_trx_date;
        }

        if (!$request->filter_cash_out_date) {
            $request_filter_cash_out_date = date('Y-m-d') . '|' . date('Y-m-d');
        } else {
            $request_filter_cash_out_date = $request->filter_cash_out_date;
        }

        $filter_order_number = '%'.$request->search.'%' ?? '%%';
        $filter_st_id = $st_id;
        $filter_platform_name = '%'.$request->platform.'%' ?? '%%';
        $filter_status = (int) $request->status;
        
        $exp_trx_date = explode('|', $request_filter_trx_date);
        $filter_trx_date_start = $exp_trx_date[0];
        $filter_trx_date_end = $exp_trx_date[1];

        $exp_cash_out_date = explode('|', $request_filter_cash_out_date);
        $filter_cash_out_date_start = $exp_cash_out_date[0];
        $filter_cash_out_date_end = $exp_cash_out_date[1];

        $data = DB::select("CALL cek_dana_online(?,?,?,?,?,?,?,?)",[
            $filter_order_number,
            $filter_st_id,
            $filter_platform_name,
            $filter_status,
            $filter_trx_date_start,
            $filter_trx_date_end,
            $filter_cash_out_date_start,
            $filter_cash_out_date_end
        ]);

        $collection = collect($data);

        return DataTables::of($collection)
            ->addColumn('fee_persentage', function ($collection) {
                if ($collection->revenue && $collection->total_fee && $collection->total_fee != 0) {
                    return number_format(($collection->total_fee / $collection->revenue * 100), 2) . '%';
                }
                return '0.00%';
            })
            ->addColumn('seller_voucher_persentage', function ($collection) {
                if ($collection->revenue && $collection->seller_discount && $collection->seller_discount != 0) {
                    return number_format(($collection->seller_discount / $collection->revenue * 100), 2) . '%';
                }
                return '0.00%';
            })
            ->addColumn('diff_jezpro_mp', function ($collection) {
                return $collection->jezpro_price - $collection->revenue;
            })
            ->addColumn('status', function ($collection) {
                if (
                    $collection->settle_date && $collection->status_print == 1
                ) {
                    return '<button class="btn btn-sm btn-success">Done</button>';
                }

                if ($collection->status_print == 1 && !$collection->settle_date) {
                    return '<button class="btn btn-sm btn-warning">Belum Cair</button>';
                }
                if ($collection->status_print == 0 && $collection->settle_date) {
                    return '<button class="btn btn-sm btn-warning">Belum Trx</button>';
                }
                if ($collection->status_print == 0 && !$collection->settle_date) {
                    return '<button class="btn btn-sm btn-dark">Belum Cair & Belum Trx</button>';
                }
                return '<button class="btn btn-sm btn-secondary">Unknown</button>';
            })
            ->addColumn('status_refund', function ($collection) {
                if ($collection->status_trx === 'REFUND') {
                    return '<button class="btn btn-sm btn-danger">Refund</button>';
                } else {
                    return '<button class="btn btn-sm btn-dark">Not Refund</button>';
                }
            })
            ->rawColumns(['status','status_refund'])
            ->addIndexColumn()
            ->make(true);
    }

    public function exportExcel(Request $request)
    {
//        $filters = $request->all();
//        return Excel::download(new TransactionOnlineSettleExport($filters), 'transactions.xlsx');

        $data = $this->getQueryForExport($request);

        if ($data->isEmpty()) {
            return back()->with('error', 'Data kosong untuk diekspor');
        }

        $store_name = $data->first()->st_name ?? 'All Stores';
        $platform_name = $request->platform_name ?? 'All Platforms';
        $file_name = 'Cek Dana Online - ' . $store_name . ' - ' . $platform_name . ' - ' . date('Y-m-d') . '.xlsx';

        return Excel::download(new TransactionOnlineSettleExport($data), $file_name);
    }

    public function getQueryForExport(Request $request) {
        $this->runStoredProcedureCekDanaOnline();
        
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = -1;
        }

        if (!$request->filter_trx_date) {
            $request_filter_trx_date = date('Y-m-d') . '|' . date('Y-m-d');
        } else {
            $request_filter_trx_date = $request->filter_trx_date;
        }

        if (!$request->filter_cash_out_date) {
            $request_filter_cash_out_date = date('Y-m-d') . '|' . date('Y-m-d');
        } else {
            $request_filter_cash_out_date = $request->filter_cash_out_date;
        }

        $filter_order_number = '%' . $request->search . '%' ?? '%%';
        $filter_st_id = $st_id;
        $filter_platform_name = '%' . $request->platform . '%' ?? '%%';
        $filter_status = (int) $request->status;

        $exp_trx_date = explode('|', $request_filter_trx_date);
        $filter_trx_date_start = $exp_trx_date[0];
        $filter_trx_date_end = $exp_trx_date[1];

        $exp_cash_out_date = explode('|', $request_filter_cash_out_date);
        $filter_cash_out_date_start = $exp_cash_out_date[0];
        $filter_cash_out_date_end = $exp_cash_out_date[1];

        $data = DB::select("CALL cek_dana_online(?,?,?,?,?,?,?,?)", [
            $filter_order_number,
            $filter_st_id,
            $filter_platform_name,
            $filter_status,
            $filter_trx_date_start,
            $filter_trx_date_end,
            $filter_cash_out_date_start,
            $filter_cash_out_date_end
        ]);

        $collection = collect($data)->map(function ($item) {
            $item->fee_persentage = isset($item->revenue, $item->total_fee) && $item->total_fee != 0
            ? number_format(($item->total_fee / $item->revenue * 100), 2) . '%'
            : '0.00%';
            $item->seller_voucher_persentage = isset($item->revenue, $item->seller_discount) && $item->seller_discount != 0
            ? number_format(($item->seller_discount / $item->revenue * 100), 2) . '%'
            : '0.00%';
            $item->diff_jezpro_mp = isset($item->jezpro_price, $item->revenue)
            ? $item->jezpro_price - $item->revenue
            : null;
            $item->status = $item->settle_date && $item->status_print == 1
            ? 'Done'
            : ($item->status_print == 1 && !$item->settle_date
                ? 'Belum Cair'
                : ($item->status_print == 0 && $item->settle_date
                ? 'Belum Trx'
                : ($item->status_print == 0 && !$item->settle_date
                    ? 'Belum Cair & Belum Trx'
                    : 'Unknown')));
            $item->status_refund = $item->status_trx === 'REFUND' ? 'Refund' : 'Not Refund';
            return $item;
        });

        return $collection;
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

    public function getDetail($order_number, $store_id) {
        $data = DB::select("CALL cek_dana_online(?,?,?,?,?,?,?,?)", [
            $order_number,
            $store_id,
            '%%',
            '0',
            null,
            null,
            null,
            null
        ]);

        $collection = collect($data)->map(function ($item) {
            $item->diff = isset($item->jezpro_price, $item->revenue) ? $item->jezpro_price - $item->revenue : null;
            $item->fee_persentage = isset($item->revenue, $item->total_fee) && $item->total_fee != 0
            ? number_format(($item->total_fee / $item->revenue * 100), 2) . '%'
            : '0.00%';
            $item->seller_voucher_persentage = isset($item->revenue, $item->seller_discount) && $item->seller_discount != 0
            ? number_format(($item->seller_discount / $item->revenue * 100), 2) . '%'
            : '0.00%';
            return $item;
        });

        return response()->json($collection->first());
    }

    public function importData(Request $request)
    {
        try {
            if ($request->hasFile('importFile')) {
                $file = $request->file('importFile');

                $nama_file = rand() . '_cek_dana_' . $file->getClientOriginalName();

                $original_name = $file->getClientOriginalName();

                $st_id_form = $request->input('st_id_form');
                $platform_name = $request->input('platform_name');

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
            $final_price = (float) $item[2];
            $total_disburshed_amount = (float) $item[3];
            $seller_voucher_discount = (float) $item[4];
            $affiliate_cut = (float) $item[5];
            $marketplace_commision_fee = (float) $item[6];
            $service_fee = (float) $item[7];
            $voucher_xtra_service_fee = (float) $item[8];
            $cashback_service_fee = (float) $item[9];
            $total_online_cut = $affiliate_cut + $marketplace_commision_fee + $service_fee + $voucher_xtra_service_fee + $cashback_service_fee;

            DB::table('online_funds')->insert([
                'st_id' => $st_id_form, // assuming $st_id_form passed from controller
                'platform_name' => $type, // example static value; replace if dynamic
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

    public function runStoredProcedureCekDanaOnline() {
        DB::raw('
        DROP PROCEDURE IF EXISTS cek_dana_online;

        DELIMITER //

        CREATE PROCEDURE cek_dana_online(
            IN filter_order_number VARCHAR(225),
            IN filter_st_id INT,
            IN filter_platform_name VARCHAR(50),
            IN filter_status INT,
            IN filter_trx_date_start date,
            IN filter_trx_date_end date,
            IN filter_cash_out_start date,
            IN filter_cash_out_end date
        )
        BEGIN

            DROP TEMPORARY TABLE IF EXISTS temp_lastest_pos_transaction;
            CREATE TEMPORARY TABLE temp_lastest_pos_transaction
            (
                order_number              VARCHAR(225),
                st_id                     INT NULL,
                platform_name             VARCHAR(50),
                settle_date               DATE,
                revenue                   FLOAT,
                total_settle              FLOAT,
                seller_discount           FLOAT,
                total_fee                 FLOAT,
                trx_date                  DATETIME,
                jezpro_price              FLOAT,
                status_trx                VARCHAR(40),
                status_print              BOOLEAN,

                affiliate_cut             FLOAT,
                marketplace_commision_fee FLOAT,
                service_fee               FLOAT,
                voucher_xtra_service_fee  FLOAT,
                cashback_service_fee      FLOAT
            );

            -- Insert latest POS transaction data
            -- This populates: order_number, st_id, jezpro_price, trx_date, status
            -- Other columns are NULL
            INSERT INTO temp_lastest_pos_transaction (order_number, st_id, platform_name, settle_date, revenue,
                                                    total_settle, seller_discount, total_fee, trx_date, jezpro_price,
                                                    status_trx, status_print, affiliate_cut, marketplace_commision_fee,
                                                    service_fee,
                                                    voucher_xtra_service_fee, cashback_service_fee)
            WITH ranked_messages AS (SELECT pos_order_number,
                                            st_id,
                                            created_at,
                                            pos_real_price,
                                            pos_status,
                                            ROW_NUMBER() OVER (PARTITION BY pos_order_number ORDER BY id DESC) AS rn
                                    FROM ts_pos_transactions
                                    where st_id = filter_st_id)
            SELECT r.pos_order_number                   AS order_number,
                r.st_id,
                ts_online_transactions.platform_name AS platform_name,
                NULL                                 AS settle_date,
                NULL                                 AS revenue,
                NULL                                 AS total_settle,
                NULL                                 AS seller_discount,
                NULL                                 AS total_fee,
                r.created_at                         AS trx_date,
                r.pos_real_price                     AS jezpro_price,
                r.pos_status                         AS status_trx,
                ts_online_transactions.online_print  AS status_print,

                NULL                                 AS affiliate_cut,
                NULL                                 AS marketplace_commision_fee,
                NULL                                 AS service_fee,
                NULL                                 AS voucher_xtra_service_fee,
                NULL                                 AS cashback_service_fee

            FROM ranked_messages r
                    JOIN ts_online_transactions ON r.pos_order_number = ts_online_transactions.order_number
            WHERE r.rn = 1
            and platform_name like filter_platform_name;

            -- Insert online funds data
            -- This populates: order_number, st_id, platform_name, settle_date, revenue, total_settle, seller_discount,
            -- total_fee, fee_percentage, seller_discount_percentage
            -- Other columns are NULL
            INSERT INTO temp_lastest_pos_transaction (order_number, st_id, platform_name, settle_date, revenue,
                                                    total_settle, seller_discount, total_fee, trx_date, jezpro_price,
                                                    status_trx, status_print, affiliate_cut, marketplace_commision_fee,
                                                    service_fee,
                                                    voucher_xtra_service_fee, cashback_service_fee)
            SELECT order_number              AS order_number,
                st_id                     AS st_id,
                platform_name             AS platform_name,
                cashout_date              AS settle_date,
                final_price               AS revenue,
                total_disburshed_amount   AS total_settle,
                seller_voucher_discount   AS seller_discount,
                total_online_cut          AS total_fee,
                NULL                      AS trx_date,
                NULL                      AS jezpro_price,
                NULL                      AS status_print,
                NULL                      AS status_print,

                affiliate_cut             AS affiliate_cut,
                marketplace_commision_fee AS marketplace_commision_fee,
                service_fee               AS service_fee,
                voucher_xtra_service_fee  AS voucher_xtra_service_fee,
                cashback_service_fee      AS cashback_service_fee
            FROM ts_online_funds
            where st_id = filter_st_id
            and platform_name like filter_platform_name;

            -- Select from the temporary table, grouping by order_number
            -- and using MAX() to merge values.
            SELECT *
            from (SELECT order_number,
                        MAX(st_id)              AS st_id,
                        MAX(st_name)              AS st_name,
                        MAX(platform_name)        AS platform_name,
                        MAX(settle_date)          AS settle_date,
                        MAX(revenue)              AS revenue,
                        MAX(total_settle)         AS total_settle,
                        MAX(seller_discount)      AS seller_discount,
                        MAX(total_fee)            AS total_fee,
                        MAX(trx_date)             AS trx_date,
                        MAX(jezpro_price)         AS jezpro_price,
                        MAX(status_trx)           AS status_trx,
                        MAX(status_print)         AS status_print,
                        MAX(affiliate_cut)             AS affiliate_cut,
                        MAX(marketplace_commision_fee) AS marketplace_commision_fee,
                        MAX(service_fee)               AS service_fee,
                        MAX(voucher_xtra_service_fee)  AS voucher_xtra_service_fee,
                        MAX(cashback_service_fee)      AS cashback_service_fee
                FROM temp_lastest_pos_transaction t
                        JOIN ts_stores s ON t.st_id = s.id
                GROUP BY order_number
                ORDER BY trx_date) AS grouped_data
            WHERE order_number like filter_order_number
            AND (IF(filter_trx_date_start is null and filter_trx_date_end is null, trx_date < CURDATE(),
                    trx_date between filter_trx_date_start and filter_trx_date_end)
                OR IF(filter_cash_out_start is null and filter_cash_out_end is null, settle_date < CURDATE(),
                    settle_date between filter_cash_out_start and filter_cash_out_end))
            AND CASE
                    WHEN filter_status = 0 THEN order_number is not null
                    WHEN filter_status = 1 THEN settle_date IS NOT NULL AND status_print = 1 #done
                    WHEN filter_status = 2 THEN status_print = 1 AND settle_date IS NULL #belum_cair
                    WHEN filter_status = 3 THEN status_print = 0 AND settle_date IS NOT NULL #belum_trx
                END;

        END //

        DELIMITER ;
        ');
    }
}
