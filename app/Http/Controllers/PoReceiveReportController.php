<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderArticle;
use App\Models\PurchaseOrderArticleDetail;
use App\Models\PurchaseOrderArticleDetailStatus;
use App\Exports\PoReceiveExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class PoReceiveReportController extends Controller
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
            'st_id' => DB::table('stores')->selectRaw('ts_stores.id as id, CONCAT(st_name) as store')
            ->where('st_delete', '!=', '1')
            ->orderByDesc('id')->pluck('store', 'id'),
        ];
        return view('app.report.po_receive.po_receive', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PurchaseOrder::selectRaw('ts_purchase_orders.id as po_id, po_invoice, st_name, ts_purchase_orders.created_at as po_created
            , max(ts_purchase_order_article_detail_statuses.created_at) as poads_created, sum(ts_purchase_order_article_details.poad_qty) as poad_qty, sum(ts_purchase_order_article_detail_statuses.poads_qty) as poads_qty
            , sum(ts_purchase_order_article_details.poad_total_price) as poad_total_price, sum(ts_purchase_order_article_detail_statuses.poads_total_price) as poads_total_price')
            ->leftJoin('purchase_order_articles', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.poa_id', '=', 'purchase_order_articles.id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('stores', 'stores.id', '=', 'purchase_orders.st_id')
            ->where('poads_qty', '!=', '0')
            ->where('po_delete', '!=', '1')
            ->where('po_draft', '!=', '1')
            ->groupBy('purchase_orders.id'))
            ->editColumn('po_created_show', function($data){
                return date('d/m/Y H:i:s', strtotime($data->po_created));
            })
            ->editColumn('poads_created_show', function($data){
                if (!empty($data->poads_created)) {
                  return date('d/m/Y H:i:s', strtotime($data->poads_created));
                } else {
                  return '-';
                }
            })
            ->editColumn('poad_qty_show', function($data){
                return '<span class="btn btn-sm btn-success text-dark font-weight-bold">'.$data->poad_qty.'</span>';
            })
            ->editColumn('poads_qty_show', function($data){
                if ($data->poads_qty >= $data->poad_qty) {
                  return '<span class="btn btn-sm btn-success text-dark" data-po_invoice="'.$data->po_invoice.'" data-po_id="'.$data->po_id.'" id="poads_qty_btn">'.$data->poads_qty.'</span>';
                } else {
                  return '<span class="btn btn-sm btn-primary" data-po_invoice="'.$data->po_invoice.'" data-po_id="'.$data->po_id.'" id="poads_qty_btn">'.$data->poads_qty.'</span>';
                }
            })
            ->editColumn('po_created_show', function($data){
                return date('d/m/Y H:i:s', strtotime($data->po_created));
            })
            ->editColumn('poad_total_price_show', function($data){
                return number_format($data->poad_total_price);
            })
            ->editColumn('poads_total_price_show', function($data){
                return number_format($data->poads_total_price);
            })
            ->rawColumns(['poad_qty_show', 'poads_qty_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('purchase_orders.po_invoice', 'LIKE', "%$search%");
                    });
                }
                if (!empty($request->get('report_date'))) {
                    $instance->where(function($w) use($request){
                        $date = $request->get('report_date');
                        $date_filter = $request->get('date_filter');
                        $start = null;
                        $end = null;
                        $exp = explode('|', $date);
                        if (count($exp) > 1) {
                            if ($exp[0] != $exp[1]) {
                                $start = $exp[0];
                                $end = $exp[1];
                            } else {
                                $start = $exp[0];
                            }
                        } else {
                            $start = $date;
                        }
                        if ($date_filter == 1) {
                            if (!empty($end)) {
                                $w->whereDate('purchase_orders.created_at', '>=', $start)
                                ->whereDate('purchase_orders.created_at', '<=', $end);
                            } else {
                                $w->whereDate('purchase_orders.created_at', $start);
                            }
                        }
                        
                    });
                }
                if (!empty($request->get('status'))) {
                    $instance->where(function($w) use($request){
                        $status = $request->get('status');
                        if ($status == 'full') {
                          $w->whereRaw('poads_qty >= poad_qty');
                        } else {
                          $w->whereRaw('poads_qty < poad_qty');
                        }
                    });
                }
                if (!empty($request->get('st_id'))) {
                    $instance->where(function($w) use($request){
                        $st_id = $request->get('st_id');
                        $w->where('purchase_orders.st_id', '=', $st_id);
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
            return datatables()->of(PurchaseOrderArticleDetail::selectRaw("ts_purchase_order_article_details.id as poad_id, CONCAT('[', br_name,'] ',p_name,' ',p_color,' ',sz_name) as article, sum(ts_purchase_order_article_details.poad_qty) as poad_qty, sum(ts_purchase_order_article_detail_statuses.poads_qty) as poads_qty
            , sum(ts_purchase_order_article_details.poad_total_price) as poad_total, sum(ts_purchase_order_article_detail_statuses.poads_total_price) as poads_total")
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('purchase_order_articles.po_id', '=', $request->po_id)
            ->groupBy('purchase_order_article_details.pst_id'))
            ->editColumn('poad_qty_show', function($data){
                return '<span class="btn btn-sm btn-success text-dark font-weight-bold">'.$data->poad_qty.'</span>';
            })
            ->editColumn('poads_qty_show', function($data){
                if ($data->poads_qty >= $data->poad_qty) {
                  return '<span class="btn btn-sm btn-success text-dark" data-po_invoice="'.$data->po_invoice.'" data-po_id="'.$data->po_id.'">'.$data->poads_qty.'</span>';
                } else {
                  return '<span class="btn btn-sm btn-primary" data-po_invoice="'.$data->po_invoice.'" data-po_id="'.$data->po_id.'">'.$data->poads_qty.'</span>';
                }
            })
            ->editColumn('poad_total_show', function($data){
                return number_format($data->poad_total);
            })
            ->editColumn('poads_total_show', function($data){
                return number_format($data->poads_total);
            })
            ->rawColumns(['poad_qty_show', 'poads_qty_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw("CONCAT(br_name,' ',p_name,' ',p_color,' ',sz_name) LIKE ?", "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function exportData(Request $request) {
        $st_id = $request->get('st_id');
        $date_filter = $request->get('date_filter');
        $status_filter = $request->get('status_filter');
        $date = $request->get('date');

        $start = null;
        $end = null;
        $exp = explode('|', $date);
        if (count($exp) > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            $start = $date;
        }
        return Excel::download(
            new PoReceiveExport($st_id, $start, $end, $status_filter, $date_filter), 
            'laporan_datang_barang_' . Carbon::now()->format('dmY_Hi') . '.xlsx');
}
}
