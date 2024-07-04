<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderReceiveCODController extends Controller
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
            'segment' => request()->segment(1)
        ];
        return view('app.purchase_order_receive_cod.purchase_order_receive_cod', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(DB::table('purchase_order_article_detail_statuses')
                ->selectRaw("ts_purchase_order_article_detail_statuses.id as id, st_name, po_invoice, poads_invoice, invoice_date, ts_purchase_order_article_detail_statuses.created_at, u_name, u_id_approve,
            sum(ts_purchase_order_article_detail_statuses.poads_qty) as qty, acc_id, is_paid")
                ->leftJoin('users', 'users.id', '=', 'purchase_order_article_detail_statuses.u_id_receive')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
                ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
                ->leftJoin('stores', 'stores.id', '=', 'purchase_orders.st_id')
                ->whereNotNull('poads_invoice')
                ->where('acc_id', 93)
                ->where('is_paid', 0)
                ->groupBy('poads_invoice'))
                ->editColumn('poads_invoice_show', function ($d) {
                    return "<a class='btn btn-primary'>" . $d->poads_invoice . "</a>";
                })
                ->editColumn('invoice_date_show', function ($data) {
                    return date('d/m/Y', strtotime($data->invoice_date));
                })
                ->editColumn('receive_date_show', function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('u_receive', function ($data) {
                    if (!empty($data->u_id_approve) && $data->acc_id == 93 && $data->is_paid == 0) {
                        return 'Diterima, Belum Dibayar';
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
    }

}
