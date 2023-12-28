<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Store;
use App\Models\ProductStock;
use App\Models\ProductLocation;
use App\Models\ProductLocationSetup;
use App\Models\Brand;
use App\Models\MainColor;
use App\Models\Size;
use App\Models\StockTransfer;
use App\Models\StockTransferDetail;
use App\Models\StockTransferDetailStatus;
use App\Models\ProductMutation;
use App\Models\WebConfig;
use App\Exports\StdExport;
use Maatwebsite\Excel\Facades\Excel;

class StockTransferDataController extends Controller
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
          'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
          'pl_id' => ProductLocation::selectRaw('ts_product_locations.id as pl_id, CONCAT(pl_code," (",st_name,")") as location')
          ->join('stores', 'stores.id', '=', 'product_locations.st_id')
          ->where('pl_delete', '!=', '1')
          ->orderByDesc('pl_code')->pluck('location', 'pl_id'),
          'br_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
          'mc_id' => MainColor::where('mc_delete', '!=', '1')->orderByDesc('id')->pluck('mc_name', 'id'),
          'sz_id' => Size::where('sz_delete', '!=', '1')->orderByDesc('id')->pluck('sz_name', 'id'),
      ];
      return view('app.stock_transfer_data.stock_transfer_data', compact('data'));
  }

  public function getDatatables(Request $request)
  {
      $user = new User;
        $select = ['u_name', 'u_email', 'u_phone', 'g_name'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();

      if(request()->ajax()) {
          return datatables()->of(StockTransfer::select('stock_transfers.id as stf_id', 'st_id_start', 'st_id_end', 'stf_code', 'u_name', 'u_id_receive', 'stock_transfers.created_at as stf_created', 'stf_status')
          ->leftJoin('users', 'users.id', '=', 'stock_transfers.u_id')
          ->leftJoin('stock_transfer_details', 'stock_transfer_details.stf_id', '=', 'stock_transfers.id')
          ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
          ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
          ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
          ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
          ->groupBy('stock_transfers.id')
          ->whereIn('stf_status', ['1', '2'])
          ->where(function($w) use ($user_data) {
            if ($user_data->g_name != 'administrator') {
                $w->where('stock_transfers.st_id_end', '=', Auth::user()->st_id);
            }
          }))
          ->editColumn('stf_code_show', function($data){
              return '<span class="btn-sm btn-primary">'.$data->stf_code.'</span>';
          })
          ->editColumn('qty', function($data){
              $qty = StockTransferDetail::select('stfd_qty')->where('stf_id', '=', $data->stf_id)->sum('stfd_qty');
              return '<span class="btn-sm btn-success">'.$qty.'</span>';
          })
          ->editColumn('start_store', function($data) {
              $store = Store::select('st_name')->where('id', $data->st_id_start)->get()->first()->st_name;
              return $store;
          })
          ->editColumn('end_store', function($data) {
              $store = Store::select('st_name')->where('id', $data->st_id_end)->get()->first()->st_name;
              return $store;
          })
          ->editColumn('stf_created', function($data) {
              return date('d-m-Y H:i:s', strtotime($data->stf_created));
          })
          ->editColumn('u_name_receive', function($data) {
              if (!empty($data->u_id_receive)) {
                $u_name_receive = User::select('u_name')->where('id', '=', $data->u_id_receive)->get()->first()->u_name;
                return $u_name_receive;
              } else {
                return '-';
              }
          })
          ->editColumn('stf_status', function($data) {
              if ($data->stf_status == '1') {
                return '<span class="btn-sm btn-warning text-white" style="white-space: nowrap;">IN PROGRESS</span>';
              } else {
                return '<span class="btn-sm btn-success">DONE</span>';
              }
          })
          ->rawColumns(['stf_code_show', 'qty', 'stf_status'])
          ->filter(function ($instance) use ($request) {
              if (!empty($request->get('search'))) {
                  $instance->where(function($w) use($request){
                      $search = $request->get('search');
                      $w->orWhere('u_name', 'LIKE', "%$search%")
                      ->orWhere('stf_code', 'LIKE', "%$search%")
                      ->orWhereRaw('CONCAT(br_name," ", p_name," ",p_color," ",sz_name) LIKE ?', "%$search%");
                  });
              }
          })
          ->addIndexColumn()
          ->make(true);
      }
  }

  public function getAcceptDatatables(Request $request)
  {
      if(request()->ajax()) {
          return datatables()->of(StockTransferDetail::select('stock_transfer_details.id as stfd_id', 'pst_id', 'pl_id', 'st_id_start', 'st_id_end', 'stf_code', 'br_name', 'p_name', 'p_color', 'sz_name', 'stfd_qty', 'stfd_status')
          ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
          ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
          ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
          ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
          ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
          ->where('stf_code', '=', $request->stf_code))
          ->editColumn('article', function($data){
              return '<span class="btn-sm btn-primary" style="white-space:nowrap;">'.$data->p_name.' '.$data->p_color.' ['.$data->sz_name.']</span>';
          })
          ->editColumn('accept_total', function($data){
              $accept_qty = StockTransferDetailStatus::where('stfd_id', '=', $data->stfd_id)->sum('stfds_qty');
              return '<span class="btn btn-primary">'.$accept_qty.'/'.$data->stfd_qty.'</span>';
          })
          ->editColumn('accept', function($data){
              $accept_qty = StockTransferDetailStatus::where('stfd_id', '=', $data->stfd_id)->sum('stfds_qty');
              if ($accept_qty < $data->stfd_qty) {
                return '<input class="form-control accept_qty" data-stfd_id="'.$data->stfd_id.'" data-pst_id="'.$data->pst_id.'" data-pl_id="'.$data->pl_id.'" data-stfd_qty="'.$data->stfd_qty.'" id="accept_qty" type="number">';
              } else {
                return '<span class="btn btn-success">Full</span>';
              }
          })
          ->rawColumns(['article', 'accept_total', 'accept'])
          ->filter(function ($instance) use ($request) {
              if (!empty($request->get('search'))) {
                  $instance->where(function($w) use($request){
                      $search = $request->get('search');
                      $w->orWhereRaw('CONCAT(br_name," ", p_name," ",p_color," ",sz_name) LIKE ?', "%$search%");
                  });
              }
          })
          ->addIndexColumn()
          ->make(true);
      }
  }

  public function getHistoryDatatables(Request $request)
  {
      $user = new User;
      $select = ['g_name'];
      $where = [
          'users.id' => Auth::user()->id
      ];
      $user_data = $user->checkJoinData($select, $where)->first();

      if(request()->ajax()) {
          return datatables()->of(DB::table('stock_transfer_detail_statuses')
          ->select('stock_transfer_detail_statuses.id', 'st_name', 'stf_code', 'st_id_start', 'br_name', 'ps_sell_price', 'p_sell_price', 'p_name', 'p_color', 'sz_name', 'stfds_qty', 'stock_transfer_details.pst_id', 'stock_transfer_detail_statuses.created_at', 'u_id_receive', 'stock_transfer_detail_statuses.u_id')
          ->leftJoin('stock_transfer_details', 'stock_transfer_details.id', '=', 'stock_transfer_detail_statuses.stfd_id')
          ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
          ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
          ->leftJoin('products', 'products.id' , '=', 'product_stocks.p_id')
          ->leftJoin('brands', 'brands.id' , '=', 'products.br_id')
          ->leftJoin('sizes', 'sizes.id' , '=', 'product_stocks.sz_id')
          ->leftJoin('users', 'users.id' , '=', 'stock_transfer_detail_statuses.u_id')
          ->leftJoin('stores', 'stores.id' , '=', 'stock_transfers.st_id_end')
          ->where(function($w) use ($user_data) {
              if ($user_data->g_name != 'administrator') {
                $w->where('stock_transfers.st_id_end', '=', Auth::user()->st_id);
              }
          })
          ->orderByDesc('stock_transfer_detail_statuses.id')
          ->groupBy('stock_transfer_detail_statuses.id'))
          ->editColumn('st_start', function($d) {
              $store = '';
              if (!empty($d->st_id_start)) {
                $store = DB::table('stores')->select('st_name')->where('id', '=', $d->st_id_start)->first()->st_name;
              }
              return $store;
          })
          ->editColumn('u_name', function($d){
              $name = '';
              $u_name = DB::table('users')->select('u_name')
              ->where(function($w) use ($d) {
                if (!empty($d->u_id)) {
                  $w->where('users.id', '=', $d->u_id);
                } else {
                  $w->where('users.id', '=', $d->u_id_receive);
                }
              })->first();
              if (!empty($u_name)) {
                $name = $u_name->u_name;
              }
              return $name;
          })
          ->editColumn('created_at', function($d) {
              return date('d/m/Y H:i:s', strtotime($d->created_at));
          })
          ->editColumn('hb', function($d) {
              $hpp = 0;
              $poads = DB::table('purchase_order_article_detail_statuses')
              ->select('poads_purchase_price', 'ps_purchase_price')
              ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
              ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
              ->where('product_stocks.id', '=', $d->pst_id)
              ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
              ->orderByDesc('purchase_order_article_detail_statuses.id')
              ->groupBy('poads_purchase_price')
              ->get()->first();
              if (!empty($poads)) {
                  if (!empty($poads->poads_purchase_price)) {
                    $hpp = $poads->poads_purchase_price;
                  } else {
                    $hpp = $poads->ps_purchase_price;
                  }
              }
              return $hpp;
          })
          ->editColumn('hj', function($d) {
              $sell_price = 0;
              if (!empty($d->ps_sell_price)) {
                $sell_price = $d->ps_sell_price;
              } else {
                $sell_price = $d->p_sell_price;
              }
              return $sell_price;
          })
          ->filter(function ($instance) use ($request) {
              if (!empty($request->get('search'))) {
                  $instance->where(function($w) use($request){
                      $search = $request->get('search');
                      $w->orWhere('stf_code', 'like', "%$search%")
                      ->orWhereRaw('CONCAT(br_name," ", p_name," ",p_color," ",sz_name) LIKE ?', "%$search%");
                  });
              }
          })
          ->addIndexColumn()
          ->make(true);
      }
  }

  public function acceptTransfer(Request $request)
  {
    $data = $request->_arr;
    $stf_id = $request->_stf_id;
    $st_id_end = $request->_st_id_end;
    $st_id_end -= 1;

    $default_bin = ProductLocation::select('id')->where([
      'st_id' => $st_id_end,
      'pl_default' => '1'
    ])->get()->first()->id;

    $insert = array();
    foreach ($data as $row) {
      $insert[] = [
        'stfd_id' => $row[0],
        'stfds_qty' => $row[2],
        'u_id' => Auth::user()->id,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ];
      $check = ProductLocationSetup::select('id', 'pls_qty')->where([
        'pl_id' => $default_bin,
        'pst_id' => $row[1]
        ])->get()->first();
      if (!empty($check)) {
        $save = ProductLocationSetup::where('id', '=', $check->id)->update([
          'pls_qty' => ($check->pls_qty + $row[2])
        ]);
      } else {
        $save = DB::table('product_location_setups')->insert([
          'pl_id' => $default_bin,
          'pst_id' => $row[1],
          'pls_qty' => $row[2]
        ]);
      }
    }
    if (!empty($save)) {
      $saveTransfer = StockTransferDetailStatus::insert($insert);
      if (!empty($saveTransfer)) {
        $transfer_qty = StockTransferDetail::select('stfd_qty')->where('stf_id', '=', $stf_id)->sum('stfd_qty');
        $accept_qty = StockTransferDetailStatus::select('stfds_qty')->leftJoin('stock_transfer_details', 'stock_transfer_details.id', '=', 'stock_transfer_detail_statuses.stfd_id')
        ->where('stf_id', '=', $stf_id)->sum('stfds_qty');
        if ($transfer_qty == $accept_qty) {
          StockTransfer::where('id', '=', $stf_id)->update([
            'u_id_receive' => Auth::user()->id,
            'stf_status' => '2'
          ]);
        } else {
          StockTransfer::where('id', '=', $stf_id)->update([
            'u_id_receive' => Auth::user()->id
          ]);
        }
        $r['status'] = '200';
      } else {
        $r['status'] = '400';
      }
    } else {
      $r['status'] = '400';
    }
    return json_encode($r);
  }
  
  public function exportData(Request $request)
  {
      $start = $request->start;
      $end = $request->end;
      return Excel::download(new StdExport($start, $end), 'stock_transfer_terima.xlsx');
  }
}
