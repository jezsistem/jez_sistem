<?php

namespace App\Http\Controllers;

use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Store;
use App\Models\ProductLocation;
use App\Models\ProductLocationSetup;
use App\Models\Brand;
use App\Models\Size;
use App\Models\StockTransfer;
use App\Models\StockTransferDetail;
use App\Models\PosTransaction;
use App\Imports\TransferImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\WebConfig;

class StockTransferController extends Controller
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
    
    private $table_row = 0;
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
            'sz_id' => Size::where('sz_delete', '!=', '1')->orderByDesc('id')->pluck('sz_name', 'id'),
        ];
        return view('app.stock_transfer.stock_transfer', compact('data'));
    }

    public function transferBinDatatables(Request $request)
    {
        if(request()->ajax()) {
            $unmatchBarcodes = array();
            return datatables()->of(ProductLocationSetup::select('product_location_setups.id as pls_id', 'products.id as p_id', 'br_name', 'p_name', 'p_color', 'sz_name', 'mc_name', 'pls_qty', 'ps_barcode')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
            ->where('pl_id', '=', $request->pl_id)
//            ->where('pls_qty', '>', '0')
            ->groupBy('products.id'))
            ->editColumn('article', function($data){
                return '<span style="white-space: nowrap;">'.$data->p_name.'<br/>'.$data->p_color.'</span>';
            })
            ->editColumn('qty', function($data) use ($request) {
                $check_pst = ProductLocationSetup::select('product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_location_setups.pl_id', '=', $request->pl_id)
                ->where('product_stocks.p_id', '=', $data->p_id)
//                ->where('pls_qty', '>', 0)
                ->get();
                if (!empty($check_pst)){
                    $sz_name = '';
                    foreach ($check_pst as $row) {
                        $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-6" style="white-space: nowrap;">'.$row->sz_name.'</a> <a class="btn btn-sm btn-primary col-6" onclick="return mutation('.$row->pst_id.', '.$request->_pl_id.', \''.$data->p_name.'\', \''.$data->p_color.'\', \''.$row->sz_name.'\', '.$row->pls_qty.')">'.$row->pls_qty.'</a></div>';
                    }
                    return $sz_name;
                } else {
                    return 'Data belum disetup';
                }
            })
            ->editColumn('transfer', function($data) use ($request) {
                $check_pst = ProductLocationSetup::select('product_location_setups.id as pls_id', 'product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_location_setups.pl_id', '=', $request->pl_id)
                ->where('product_stocks.p_id', '=', $data->p_id)
//                ->where('pls_qty', '>', 0)
                ->get();
                if (!empty($check_pst)){
                    $transfer = '';
                    foreach ($check_pst as $row) {
                        $this->table_row += 1;
                        $qtyData = 0;
                        if (!empty($request->excelImport))
                        {
                            foreach ($request->excelImport as $dataImport)
                            {
                                if ($dataImport['barcode'] == $row->ps_barcode)
                                {
                                    $qtyData = $dataImport['qty'];
                                } else {
                                    $unmatchBarcodes[] = $dataImport['barcode'];
                                }
                            }
                        }

                        if($qtyData == 0)
                        {
                            $qtyData = '';
                        }

                        $transfer .= '
                        <input
                        data-transfer-qty
                        data-qty="'.$row->pls_qty.'"
                        data-pls_id = "'.$row->pls_id.'"
                        data-table_row = "'.$this->table_row.'"
                        data-pst_id = "'.$row->pst_id.'"
                        data-ps_barcode = "'.$row->ps_barcode.'"
                        data-pls_qty = "'.$row->pls_qty.'"
                        data-import_qty = "'.$qtyData.'"
                        id="transfer_qty"
                        type="number"
                        class="form-control col-12 transfer_qty"
                        style="padding:10px; margin-bottom:2px;"                        
                        value="'.$qtyData.'"
                        title="'.$data->p_name.' '.$data->p_color.' '.$row->sz_name.'"/>
                        <i class="fa fa-eye d-none" onclick="return saveTransfer('.$row->pls_id.', '.$this->table_row.', '.$row->pst_id.', '.$row->pls_qty.')" id="saveTransfer'.$this->table_row.'"></i>';
                    }
                    return $transfer;
                } else {
                    return '-';
                }
            })
            ->rawColumns(['article', 'qty', 'transfer'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(p_name," ", p_color) LIKE ?', "%$search%")
                        ->orWhere('p_name', 'LIKE', "%$search%")
                        ->orWhere('br_name', 'LIKE', "%$search%")
                        ->orWhere('p_color', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function transferHistoryDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(StockTransfer::select('stock_transfers.id as stf_id', 'st_id_start', 'st_id_end', 'stf_code', 'u_name', 'u_id_receive', 'stock_transfers.created_at as stf_created', 'stf_status')
            ->leftJoin('users', 'users.id', '=', 'stock_transfers.u_id')
            ->leftJoin('stock_transfer_details', 'stock_transfer_details.stf_id', '=', 'stock_transfers.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->groupBy('stock_transfers.id')
            ->whereIn('stf_status', ['1', '2', '3']))
            ->editColumn('stf_code', function($data){
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
                  return '<span class="btn-sm btn-warning text-white" style="white-space:nowrap;" data-code="'.$data->stf_code.'" id="view_btn">IN PROGRESS</span>';
                } else if ($data->stf_status == '2') {
                  return '<span class="btn-sm btn-success" data-code="'.$data->stf_code.'" id="view_btn">DONE</span>';
                } else {
                  return '<span class="btn-sm btn-info" data-code="'.$data->stf_code.'" id="draft_btn">DRAFT</span>';
                }
            })
            ->rawColumns(['stf_code', 'qty', 'stf_status'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('u_name', 'LIKE', "%$search%")
                        ->orWhere('stf_code', 'LIKE', "%$search%")
                        ->orWhereRaw('CONCAT(br_name," ", p_name," ",p_color," ",sz_name) LIKE ?', "%$search%");
                    });
                }
                if (!empty($request->get('status'))) {
                  $instance->where(function($w) use($request){
                      $status = $request->get('status');
                      $w->where('stf_status', '=', $status);
                  });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function inTransferBinDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(StockTransferDetail::select('stock_transfer_details.id as stfd_id', 'pst_id', 'pl_id', 'st_id_start', 'st_id_end', 'stf_code', 'br_name', 'p_name', 'p_color', 'sz_name', 'stfd_qty', 'stfd_status', 'pl_code')
            ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'stock_transfer_details.pl_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('stf_code', '=', $request->stf_code))
            ->editColumn('article', function($data){
                return '<span data-stfd_id="'.$data->stfd_id.'" data-pst_id="'.$data->pst_id.'" data-pl_id="'.$data->pl_id.'" data-stfd_qty="'.$data->stfd_qty.'" id="cancel_transfer_item" style="white-space:nowrap;">'.$data->p_name.'<br/>['.$data->br_name.'] '.$data->p_color.' ['.$data->sz_name.'] <i class="badge badge-sm badge-danger">X</i></span>';
            })
            ->editColumn('st_start', function($data){
              $st_name = DB::table('stores')->select('st_name')->where('id', '=', $data->st_id_start)->get()->first()->st_name;
              return $st_name;
            })
            ->editColumn('st_end', function($data){
              $st_name = DB::table('stores')->select('st_name')->where('id', '=', $data->st_id_end)->get()->first()->st_name;
              return $st_name;
            })
            ->editColumn('status', function($data){
              if ($data->stfd_status == '0') {
                return '<span class="btn-sm btn-warning text-white" style="white-space:nowrap;">Belum Diambil</span>';
              } else {
                return '<span class="btn-sm btn-success">Diambil</span>';
              }
            })
            ->rawColumns(['article', 'status'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(p_name," ", p_color) LIKE ?', "%$search%")
                        ->orWhere('p_name', 'LIKE', "%$search%")
                        ->orWhere('br_name', 'LIKE', "%$search%")
                        ->orWhere('p_color', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function transferListDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(StockTransferDetail::select('stock_transfer_details.id as stfd_id', 'st_id_start', 'st_id_end', 'stf_code', 'br_name', 'p_name', 'p_color', 'sz_name', 'stfd_qty', 'pl_code', 'product_stocks.ps_barcode as ps_barcode')
            ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'stock_transfer_details.pl_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('stf_code', '=', $request->invoice)
            ->where(function($w) use ($request){
                if ($request->mode == 'get') {
                  $w->where('stfd_status', '=', '0');
                } else {
                  $w->where('stfd_status', '=', '1');
                }
            }))
            ->editColumn('article', function($data) use ($request){
                if ($request->mode == 'get') {
                  return '['.$data->br_name.']<br/><span style="white-space:nowrap;">'.$data->p_name.' '.$data->p_color.' '.$data->sz_name.'</span><br/>
                  <a class="btn btn-sm btn-primary">Jml : '.$data->stfd_qty.'</a>
                  <a class="btn btn-sm btn-primary">'.$data->pl_code.'</a>
                  <a class="btn btn-sm btn-success" style="font-weight:bold;" data-p_name="'.$data->p_name.' '.$data->p_color.' '.$data->sz_name.'" data-bin="'.$data->pl_code.'" data-stfd_id="'.$data->stfd_id.'" data-ps-barcode="'.$data->ps_barcode.'" id="get_transfer_item">Ambil</a>
                  ';
                } else {
                  return '['.$data->br_name.']<br/><span style="white-space:nowrap;">'.$data->p_name.' '.$data->p_color.' '.$data->sz_name.'</span><br/>
                  <a class="btn btn-sm btn-primary">Jml : '.$data->stfd_qty.'</a>
                  <a class="btn btn-sm btn-primary">'.$data->pl_code.'</a>
                  ';
                }
            })
            ->rawColumns(['article'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%")
                        ->orWhere('product_stocks.ps_barcode', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getTransferItem(Request $request)
    {
      $stfd_id = $request->_stfd_id;
      $update = StockTransferDetail::where('id', '=', $stfd_id)->update([
        'stfd_status' => '1'
      ]);
      if (!empty($update)) {
        $r['status'] = '200';
      } else {
        $r['status'] = '400';
      }
      return json_encode($r);
    }

    public function cancelTransferItem(Request $request)
    {
      $stfd_id = $request->_stfd_id;
      $pst_id = $request->_pst_id;
      $pl_id = $request->_pl_id;
      $stfd_qty = $request->_stfd_qty;
      $get_pls = ProductLocationSetup::select('pls_qty', 'id')->where([
        'pst_id' => $pst_id,
        'pl_id' => $pl_id,
      ])->get()->first();
      if (!empty($get_pls)) {
        $update = ProductLocationSetup::where([
          'id' => $get_pls->id
        ])->update([
          'pls_qty' => ($get_pls->pls_qty + $stfd_qty)
        ]);
        if (!empty($update)) {
          $delete = StockTransferDetail::where('id', '=', $stfd_id)->delete();
          if (!empty($delete)) {
            $r['status'] = '200';
          } else {
            $r['status'] = '400';
          }
        } else {
          $r['status'] = '400';
        }
      } else {
        $r['status'] = '400';
      }
      return json_encode($r);
    }

    public function reloadTransferBin(Request $request)
    {
        $st_id = $request->_st_id;
        $data = [
            'pl_id' => ProductLocation::selectRaw('ts_product_locations.id as pl_id, CONCAT(pl_code," (",st_name,")") as location')
            ->join('stores', 'stores.id', '=', 'product_locations.st_id')
            ->where('pl_delete', '!=', '1')
            ->where('st_id', '=', $st_id)
            ->orderByDesc('pl_code')->pluck('location', 'pl_id'),
        ];
        return view('app.stock_transfer._transfer_bin', compact('data'));
    }

    public function reloadTransferInvoice()
    {
      $data = [
        'invoice' => StockTransfer::whereIn('stf_status', ['0', '3'])->orderByDesc('id')->pluck('stf_code', 'id'),
      ];
      return view('app.dashboard.helper._reload_transfer_invoice', compact('data'));
    }

    public function reloadScanTransferInvoice()
    {
        $data = [
            'invoice' => StockTransfer::whereIn('stf_status', ['0', '3'])->orderByDesc('id')->pluck('stf_code', 'id'),
        ];
        return view('app.dashboard.helper._reload_scan_transfer_invoice', compact('data'));
    }

    public function reloadTransferInvoiceCheck()
    {
      $data = [
        'invoice' => StockTransfer::where('stf_status', '=', '1')->orderByDesc('id')->pluck('stf_code', 'id'),
      ];


      return view('app.dashboard.helper._reload_transfer_invoice', compact('data'));
    }

    public function reloadOrderInvoice()
    {
      $data = [
        'invoice' => PosTransaction::select('pos_invoice', 'plst_status')
        ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
        ->whereIn('plst_status', ['WAITING ONLINE', 'WAITING FOR PACKING'])
        ->groupBy('pos_invoice')
        ->orderByDesc('pos_invoice')->pluck('pos_invoice', 'pos_invoice'),
      ];
      return view('app.dashboard.helper._reload_order_invoice', compact('data'));
    }

    public function stockTransferDone(Request $request)
    {
      $stf_code = $request->_stf_code;
      $check = StockTransferDetail::leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
        ->where('stock_transfers.stf_code', '=', $stf_code)
        ->where('stock_transfer_details.stfd_status', '=', '0')
        ->exists();
      if (!$check) {
        $update = StockTransfer::where('stf_code', '=', $stf_code)->update([
          'stf_status' => '1'
        ]);
        $r['status'] = '200';
      } else {
        $r['status'] = '400';
      }
      return json_encode ($r);
    }

    public function stockTransferExec(Request $request)
    {
        $main_validate = $request->validate([
          '_st_start' => 'required|integer',
          '_st_end' => 'required|integer',
          '_bin' => 'required|integer',
        ]);
        $check_stf = StockTransfer::select('id')->where('stf_status', '=', '0')->where('u_id', '=', Auth::user()->id)->get()->first();
        $stf_code = 'TF'.date('YmdHis').str_pad(rand(0, pow(10, 3)-1), 3, '0', STR_PAD_LEFT);
        if (!empty($check_stf)) {
            $stf_id = $check_stf->id;
        } else {
            $stf_id = DB::table('stock_transfers')->insertGetId([
              'u_id' => Auth::user()->id,
              'st_id_start' => $request->_st_start,
              'st_id_end' => $request->_st_end,
              'stf_code' => $stf_code,
              'stf_status' => '0',
              'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        if (!empty($stf_id)) {
          $insert = array();
          $pls_id = array();
          $pls_qty = array();
          foreach ($request->_arr as $row) {
            $insert[] = [
              'stf_id' => $stf_id,
              'pst_id' => $row[1],
              'pl_id' => $request->_bin,
              'stfd_qty' => $row[3],
              'stfd_status' => '0',
              'created_at' => date('Y-m-d H:i:s'),
            ];
            $pls_update = ProductLocationSetup::where([
              'id' => $row[0],
            ])->update([
              'pls_qty' => ($row[2] - $row[3])
            ]);
          }
          $stfd = StockTransferDetail::insert($insert);
          if (!empty($stfd)) {
            $r['status'] = '200';
            $r['code'] = $stf_code;
          } else {
            $r['status'] = '400';
          }
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function stockTransferDraft(Request $request)
    {
        $inv = $request->post('inv');
        $save = DB::table('stock_transfers')
        ->where('stf_code', '=', $inv)->update([
          'stf_status' => '3'
        ]);
        $r['status'] = '200';
        return json_encode($r);
    }

    public function stockTransferCancel(Request $request)
    {
        $inv = $request->post('inv');
        $check = StockTransferDetail::leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
        ->where('stock_transfers.stf_code', '=', $inv)
        ->where('stock_transfer_details.stfd_status', '=', '1')
        ->exists();
        if (!$check) {
          $data = StockTransferDetail::select('stock_transfer_details.id as id', 'pst_id', 'pl_id', 'stfd_qty')
          ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
          ->where('stock_transfers.stf_code', '=', $inv)
          ->get();
          foreach($data as $row) {
            $get_pls = ProductLocationSetup::select('pls_qty', 'id')->where([
              'pst_id' => $row->pst_id,
              'pl_id' => $row->pl_id,
            ])->get()->first();
            $update = ProductLocationSetup::where([
              'id' => $get_pls->id
            ])->update([
              'pls_qty' => ($get_pls->pls_qty + $row->stfd_qty)
            ]);
            StockTransferDetail::where('id', '=', $row->id)->delete();
          }
          StockTransfer::where('stf_code', '=', $inv)->delete();
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function getPendingStfCode()
    {
        $check = StockTransfer::select('stf_code')->where('u_id', '=', Auth::user()->id)->where('stf_status', '=', '0')->get()->first();
        if (!empty($check)) {
            $r['stf_code'] = $check->stf_code;
        } else {
            $r['stf_code'] = '';
        }
        return json_encode($r);
    }

    public function importData(Request $request)
    {
        try {
            if ($request->hasFile('importFile')) {

                $file = $request->file('importFile');
                // membuat nama file unik
                $nama_file = rand() . $file->getClientOriginalName();

                // upload ke folder file_siswa di dalam folder public
                $file->move('excel', $nama_file);

                $import = new TransferImport;
                $data = Excel::toArray($import, public_path('excel/' . $nama_file));


                if (count($data) >= 0) {
                    $processData = $this->processImportData($data[0]);
                    $r['data'] = $processData;
                    $r['status'] = '200';
                } else {
                    $r['status'] = '419';
                }
            } else {

                $r['status'] = '400';
            }

//            delete file
            unlink(public_path('excel/' . $nama_file));

            return json_encode($r);
        } catch (\Exception $e) {
            unlink(public_path('excel/' . $nama_file));
            $r['status'] = '400';
            return json_encode($r);
        }
    }

    private function processImportData($data)
    {

        $processedData = [];
        $missingBarcode = array();

        foreach ($data as $item) {
            $barcode = $item[0];
            $qty = $item[1];
            // get id from barcode
            $product_id = ProductStock::where('ps_barcode', '=', $barcode)->get()->first();

            if (!empty($product_id))
            {
                // Check if barcode already exists in processedData
                $existingKey = array_search($barcode, array_column($processedData, 'barcode'));

                if ($existingKey !== false) {
                    // If barcode exists, add the quantity to the existing entry
                    $processedData[$existingKey]['qty'] += $qty;
                } else {
                    // If barcode doesn't exist, create a new entry
                    $rowData = [
                        'product_stock_id' => $product_id->id,
                        'barcode' => $barcode,
                        'qty' => $qty,
                    ];
                    $processedData[] = $rowData;
                }
            }
            else
            {
                $missingBarcode[] = $barcode;
            }
        }
        return [
            'processedData' => $processedData,
            'missingBarcode' => $missingBarcode
        ];
    }
}
