<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;

class POReceiveApprovalController extends Controller
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
            'segment' => request()->segment(1)
        ];
        return view('app.po_approval.po_approval', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('purchase_order_article_detail_statuses')
            ->selectRaw("ts_purchase_order_article_detail_statuses.id as id, st_name, po_invoice, poads_invoice, invoice_date, ts_purchase_order_article_detail_statuses.created_at, u_name, u_id_approve,
            sum(ts_purchase_order_article_detail_statuses.poads_qty) as qty")
            ->leftJoin('users', 'users.id', '=', 'purchase_order_article_detail_statuses.u_id_receive')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
            ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
            ->leftJoin('stores', 'stores.id', '=', 'purchase_orders.st_id')
            ->whereNotNull('poads_invoice')
            ->groupBy('poads_invoice'))
            ->editColumn('poads_invoice_show', function($d){
                return "<a class='btn btn-primary'>".$d->poads_invoice."</a>";
            })
            ->editColumn('invoice_date_show', function($data){
                return date('d/m/Y', strtotime($data->invoice_date));
            })
            ->editColumn('receive_date_show', function($data){
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->editColumn('u_receive', function($data){
                if (!empty($data->u_id_approve)) {
                    $name = DB::table('users')->where('id', '=', $data->u_id_approve)->first()->u_name;
                    return $name.'<br/>'.date('d/m/Y H:i:s', strtotime($data->created_at));
                } else {
                    return 'Menunggu Approval';
                }
            })
            ->rawColumns(['poads_invoice_show', 'u_receive'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('poads_invoice', 'LIKE', "%$search%")
                        ->orWhere('po_invoice', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getDetailDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('purchase_order_article_detail_statuses')
            ->selectRaw("ts_purchase_order_article_detail_statuses.id, poads_invoice, 
            u_id_approve, br_name, p_name, sz_name, p_color, stkt_name, poads_qty, poads_purchase_price, 
            ts_product_stocks.ps_qty,poads_total_price, ts_purchase_order_article_detail_statuses.created_at")
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('stock_types', 'stock_types.id', '=', 'purchase_order_article_detail_statuses.stkt_id')
            ->where('poads_invoice', '=', $request->get('poads_invoice')))
            ->editColumn('delete', function($d){
                if (empty($d->u_id_approve)) {
                    return "<a class='btn btn-danger' data-id='".$d->id."' id='delete_poads'>X</a>";
                } else {
                    return '';
                }
            })
            ->editColumn('created_at_show', function($d){
                return date('d/m/Y H:i:s', strtotime($d->created_at));
            })
            ->rawColumns(['delete'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function approveData(Request $request)
    {
        $invoice = $request->post('invoice');
        $poads = DB::table('purchase_order_article_detail_statuses')->select('purchase_order_article_detail_statuses.id as id', 'poads_qty', 'pst_id', 'st_id', 'poads_invoice')
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
        ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
        ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
        ->where('poads_invoice', '=', $invoice)->get();
        if (!empty($poads->first())) {
            foreach ($poads as $row) {
                $bin = DB::table('product_locations')->select('id')->where('st_id', '=', $row->st_id)->where('pl_default', '=', '1')->get()->first()->id;
                $check_product_stock = DB::table('product_stocks')->where('id', $row->pst_id)->get()->first();
                DB::table('products')->where('id', '=', $check_product_stock->p_id)->update([
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $check_pl = DB::table('product_location_setups')->select('id', 'pls_qty', 'pl_id')
                ->where('pst_id', '=', $row->pst_id)
                ->where('pl_id', '=', $bin)->first();
                if (!empty($check_pl)) {
                    $pls_qty = $check_pl->pls_qty;
                    if ($pls_qty < 0) {
                        $pls_qty = 0;
                    }
                    $pl_id = $check_pl->pl_id;
                    $update_setup = DB::table('product_location_setups')->where('id', '=', $check_pl->id)->update([
                        'pls_qty' => ($pls_qty+$row->poads_qty)
                    ]);
                    if (!empty($update_setup)) {
                        DB::table('purchase_order_article_detail_statuses')->where('id', '=', $row->id)->update([
                            'u_id_approve' => Auth::user()->id,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                } else {
                    $update_setup = DB::table('product_location_setups')->insert([
                        'pst_id' => $row->pst_id,
                        'pl_id' => $bin,
                        'pls_qty' => $row->poads_qty,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    if (!empty($update_setup)) {
                        DB::table('purchase_order_article_detail_statuses')->where('id', '=', $row->id)->update([
                            'u_id_approve' => Auth::user()->id,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }
        $r['status'] = '200';
        return json_encode($r);
    }
}
