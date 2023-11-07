<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;

class WebConfigController extends Controller
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
            'segment' => request()->segment(1),
        ];
        return view('app.web_config.web_config', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('web_configs')->select('*'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('config_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $web_config = new WebConfig;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'config_name' => $request->input('config_name'),
            'config_value' => $request->input('config_value'),
        ];

        $save = $web_config->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $web_config = new WebConfig;
        $id = $request->input('_id');
        $save = $web_config->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function resetERP() {
        DB::table('user_activities')->truncate();
        DB::table('article_reports')->truncate();
        DB::table('a_i_histories')->truncate();
        DB::table('activity_notes')->truncate();
        DB::table('activities')->delete();
        DB::table('bin_adjustments')->truncate();
        DB::table('blog_contents')->truncate();
        DB::table('blog_categories')->truncate();
        DB::table('brand_information')->truncate();
        DB::table('buy_one_get_ones')->truncate();
        DB::table('carts')->truncate();
        DB::table('checkout_sessions')->truncate();
        DB::table('confirmations')->truncate();
        DB::table('dashboard_information')->truncate();
        DB::table('client_credential_transaction_details')->truncate();
        DB::table('client_credential_transactions')->delete();
        DB::table('client_credentials')->delete();
        DB::table('debt_list_payments')->truncate();
        DB::table('debt_lists')->delete();
        DB::table('exception_locations')->truncate();
        DB::table('free_shipping_costs')->truncate();
        DB::table('qty_exceptions')->truncate();
        DB::table('group_levels')->truncate();
        DB::table('instock_lists')->truncate();
        DB::table('instock_exception_approvals')->truncate();
        DB::table('investors')->truncate();
        DB::table('invoice_editors')->truncate();
        DB::table('invoice_editor_permissions')->truncate();
        DB::table('mass_adjustment_details')->truncate();
        DB::table('mass_adjustments')->delete();
        DB::table('no_symbol_articles')->truncate();
        DB::table('marketplace_managers')->truncate();
        DB::table('online_cross_agings')->truncate();
        DB::table('p_o_receive_invoices')->truncate();
        DB::table('pos_images')->truncate();
        DB::table('pos_shipping_information')->truncate();
        DB::table('product_discount_details')->truncate();
        DB::table('product_discounts')->delete();
        DB::table('product_location_setup_transactions')->delete();
        DB::table('user_ratings')->truncate();
        DB::table('voucher_transactions')->truncate();
        DB::table('pos_transaction_details')->truncate();
        DB::table('pos_transactions')->delete();
        DB::table('stock_transfer_detail_statuses')->truncate();
        DB::table('stock_transfer_details')->truncate();
        DB::table('stock_transfers')->delete();
        DB::table('product_mutations')->truncate();
        DB::table('scan_adjustment_brands')->truncate();
        DB::table('scan_adjustment_sub_categories')->truncate();
        DB::table('scan_adjustment_details')->truncate();
        DB::table('scan_adjustments')->delete();
        DB::table('stock_exports')->truncate();
        DB::table('store_aging_details')->truncate();
        DB::table('store_agings')->delete();
        DB::table('sub_sub_targets')->truncate();
        DB::table('sub_targets')->truncate();
        DB::table('targets')->truncate();
        DB::table('template_groups')->truncate();
        DB::table('top_deal_details')->truncate();
        DB::table('topdeals')->truncate();
        DB::table('user_menu_accesses')->where('u_id', '!=', '1')->delete();
        DB::table('user_reminders')->truncate();
        DB::table('webinar_registrations')->truncate();
        DB::table('whatsapps')->truncate();
        DB::table('vouchers')->truncate();
        DB::table('purchase_order_article_detail_statuses')->truncate();
        DB::table('purchase_order_article_details')->delete();
        DB::table('purchase_order_articles')->delete();
        DB::table('purchase_orders')->delete();
        DB::table('product_location_setups')->delete();
        DB::table('product_locations')->delete();
        DB::table('product_stocks')->delete();
        DB::table('sizes')->delete();
        DB::table('products')->delete();
        DB::table('user_groups')->where('user_id', '!=', '1')->delete();
        DB::table('users')->where('id', '!=', '1')->delete();
        DB::table('stores')->where('id', '!=', '1')->delete();
        DB::table('product_suppliers')->where('id', '!=', '1')->delete();
        DB::table('customers')->where('id', '!=', '1')->delete();
        DB::table('customers_old')->where('id', '!=', '1')->delete();
        DB::table('banner_brand_details')->truncate();
        DB::table('banner_brands')->delete();
        DB::table('banners')->delete();

        // DB::table('product_sub_sub_categories')->delete();
        // DB::table('product_sub_categories')->delete();
        // DB::table('product_categories')->delete();
        // DB::table('brands')->delete();

        $r['status'] = '200';
        return json_encode($r);
    }
}
