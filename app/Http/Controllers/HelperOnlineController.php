<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\WarehouseIndex;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HelperOnlineController extends Controller
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
            'segment' => request()->segment(1),
            'st_id' => Auth::user()->st_id,
            'warehouse' => WarehouseIndex::query()->where('st_id', Auth::user()->st_id)->first()->w_code,
        ];
        return view('app.helper_online.helper_online', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        $st_id = $request->get('st_id');
        $query = DB::table('product_location_setup_transactions')
            ->join('online_transaction_details', 'product_location_setup_transactions.otd_id', '=', 'online_transaction_details.id')
            ->join('online_transactions', 'online_transaction_details.to_id', '=', 'online_transactions.id')
            ->join('stores', 'online_transactions.st_id', '=', 'stores.id')
            ->select(
                'online_transactions.order_number',
                'platform_name AS platform',
                'st_name AS store',
                DB::raw("GROUP_CONCAT(DISTINCT CONCAT(ts_online_transaction_details.sku, ' (', ts_online_transaction_details.qty, ')') ORDER BY ts_online_transaction_details.sku ASC SEPARATOR ', ') AS sku"),
                'online_transactions.order_date_created AS created_at',
                'online_transactions.internal_order_status AS internal_order_status'
            )
            ->where('product_location_setup_transactions.warehouse_st_id', $st_id)
            ->groupBy('online_transactions.order_number', 'platform_name', 'st_name', 'online_transactions.order_date_created')
            ->orderBy('online_transactions.order_date_created', 'DESC');
    }

    public function getListPickedOnline(Request $request) {
        $st_id = $request->get('st_id');
        $status_filter = $request->get('status_filter');
        $order_number = $request->get('order_number');
        $transactions = DB::table('product_location_setup_transactions')
            ->join('online_transaction_details', 'product_location_setup_transactions.otd_id', '=', 'online_transaction_details.id')
            ->join('online_transactions', 'online_transaction_details.to_id', '=', 'online_transactions.id')
            ->join('stores', 'online_transactions.st_id', '=', 'stores.id')
            ->select(
                'online_transactions.id as transaction_id',
                'online_transactions.order_number',
                'platform_name AS platform',
                'st_name AS store',
                DB::raw("GROUP_CONCAT(DISTINCT CONCAT(ts_online_transaction_details.sku, ' (', ts_online_transaction_details.qty, ')') ORDER BY ts_online_transaction_details.sku ASC SEPARATOR ', ') AS sku"),
                'online_transactions.order_date_created AS created_at',
                'online_transactions.internal_order_status AS internal_order_status',
                DB::raw('MAX(ts_product_location_setup_transactions.created_at) AS picked_time'),
                'no_resi',
                DB::raw('SUM(ts_online_transaction_details.qty) AS total_picked'),
                DB::raw('(select COUNT(*)
                from ts_online_transaction_chat_history where is_amp=1 and is_readed=0 and ot_id=ts_online_transactions.id) AS unreaded_chat'))
            ->where('product_location_setup_transactions.warehouse_st_id', $st_id)
            ->when($status_filter, function ($query, $status_filter) {
                $query->where('online_transactions.internal_order_status', $status_filter);
            })
            ->when($order_number, function ($query, $order_number) {
                $query->where('online_transactions.order_number', 'like', '%' . $order_number . '%')
                ->orWhere('no_resi', 'like', '%' . $order_number . '%');
            })
            ->groupBy('online_transactions.order_number', 'platform_name', 'st_name', 'online_transactions.order_date_created')
            ->orderBy('picked_time', 'asc')
            ->get();        

        return response()->json($transactions);
    }
}
