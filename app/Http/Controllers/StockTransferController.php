<?php

namespace App\Http\Controllers;

use App\Exports\StdExport;
use App\Exports\StdExportDraft;
use App\Imports\TransferDoneCompareImport;
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
use App\Models\TempStockTransferReceive;
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

    public function indexUpdated()
    {
        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => 'transfer_stok'
            ])->exists();
        if (!$validate) {
            dd("Anda tidak memiliki akses ke menu ini, hubungi Administrator");
        }
        
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => 'Transfer Stock',
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
        return view('app.updated_transfer_stok.transfer_stok', compact('data'));
    }

    public function transferBinDatatables(Request $request)
    {
        if (request()->ajax()) {
            $unmatchBarcodes = array();
            return datatables()->of(ProductLocationSetup::select('product_location_setups.id as pls_id', 'products.id as p_id', 'br_name', 'p_name', 'p_color', 'sz_name', 'mc_name', 'pls_qty', 'ps_barcode', 'products.article_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
                ->where('pl_id', '=', $request->pl_id)
                ->where('pls_qty', '>', '0')
                ->groupBy('products.id'))
                ->editColumn('article', function ($data) {
                    return '<span style="color:red;font-weight:bold;">' . $data->article_id . '</span> ' . '<span style="white-space: nowrap;">' . $data->p_name . '<br/>' . $data->p_color . '</span>';
                })
                ->editColumn('pls_qty', function ($data) use ($request) {
                    $check_pst = ProductLocationSetup::select('product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('product_location_setups.pl_id', '=', $request->pl_id)
                        ->where('product_stocks.p_id', '=', $data->p_id)
                        ->where('pls_qty', '>', 0)
                        ->get();
                    if (!empty($check_pst)) {
                        $sz_name = '';
                        foreach ($check_pst as $row) {
                            $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-6" style="white-space: nowrap;">' . $row->sz_name . '</a> <a class="btn btn-sm btn-primary col-6" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->pls_qty . '</a></div>';
                        }
                        return $sz_name;
                    } else {
                        return 'Data belum disetup';
                    }
                })
                ->editColumn('transfer', function ($data) use ($request) {
                    $check_pst = ProductLocationSetup::select('product_location_setups.id as pls_id', 'product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('product_location_setups.pl_id', '=', $request->pl_id)
                        ->where('product_stocks.p_id', '=', $data->p_id)
                        ->where('pls_qty', '>', 0)
                        ->get();
                    if (!empty($check_pst)) {
                        $transfer = '';
                        foreach ($check_pst as $row) {
                            $this->table_row += 1;
                            $qtyData = 0;
                            if (!empty($request->excelImport)) {
                                foreach ($request->excelImport as $dataImport) {
                                    if ($dataImport['barcode'] == $row->ps_barcode) {
                                        $qtyData = $dataImport['qty'];
                                    } else {
                                        $unmatchBarcodes[] = $dataImport['barcode'];
                                    }
                                }
                            }

                            if ($qtyData == 0) {
                                $qtyData = '';
                            }

                            $transfer .= '
                        <input
                        data-transfer-qty
                        data-qty="' . $row->pls_qty . '"
                        data-pls_id = "' . $row->pls_id . '"
                        data-table_row = "' . $this->table_row . '"
                        data-pst_id = "' . $row->pst_id . '"
                        data-ps_barcode = "' . $row->ps_barcode . '"
                        data-pls_qty = "' . $row->pls_qty . '"
                        data-import_qty = "' . $qtyData . '"
                        id="transfer_qty"
                        type="number"
                        class="form-control col-12 transfer_qty"
                        style="padding:10px; margin-bottom:2px;"                        
                        value="' . $qtyData . '"
                        title="' . $data->p_name . ' ' . $data->p_color . ' ' . $row->sz_name . '"/>
                        <i class="fa fa-eye d-none" onclick="return saveTransfer(' . $row->pls_id . ', ' . $this->table_row . ', ' . $row->pst_id . ', ' . $row->pls_qty . ')" id="saveTransfer' . $this->table_row . '"></i>';
                        }
                        return $transfer;
                    } else {
                        return '-';
                    }
                })
                ->rawColumns(['article', 'pls_qty', 'transfer'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhereRaw('CONCAT(p_name," ", p_color) LIKE ?', "%$search%")
                                ->orWhere('p_name', 'LIKE', "%$search%")
                                ->orWhere('br_name', 'LIKE', "%$search%")
                                ->orWhere('ps_barcode', 'LIKE', "%$search%")
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
        if (request()->ajax()) {
            return datatables()->of(StockTransfer::select('stock_transfers.id as stf_id', 'st_id_start', 'st_id_end', 'stf_code', 'u_name', 'u_id_receive', 'stock_transfers.created_at as stf_created', 'stf_status')
                ->leftJoin('users', 'users.id', '=', 'stock_transfers.u_id')
                ->leftJoin('stock_transfer_details', 'stock_transfer_details.stf_id', '=', 'stock_transfers.id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->groupBy('stock_transfers.id')
                ->whereIn('stf_status', ['1', '2', '3']))
                ->editColumn('stf_code', function ($data) {
                    return '<span class="btn-sm btn-primary">' . $data->stf_code . '</span>';
                })
                ->editColumn('qty_send', function ($data) {
                    $qty = StockTransferDetail::select('stfd_qty')->where('stf_id', '=', $data->stf_id)->sum('stfd_qty');
                    return '<span class="btn-sm btn-success">' . $qty . '</span>';
                })
                ->addColumn('qty_receive', function ($data) {
                    $qty_receive = StockTransferDetail::select('stfds_qty')->where('stf_id', '=', $data->stf_id)
                    ->join('stock_transfer_detail_statuses', 'stock_transfer_detail_statuses.stfd_id', '=', 'stock_transfer_details.id')
                    ->sum('stfds_qty');
                    return '<span class="btn-sm btn-info">' . $qty_receive . '</span>';
                })
                ->editColumn('start_store', function ($data) {
                    $store = Store::select('st_name')->where('id', $data->st_id_start)->get()->first()->st_name;
                    return $store;
                })
                ->editColumn('end_store', function ($data) {
                    $store = Store::select('st_name')->where('id', $data->st_id_end)->get()->first()->st_name;
                    return $store;
                })
                ->editColumn('stf_created', function ($data) {
                    return date('d-m-Y H:i:s', strtotime($data->stf_created));
                })
                ->editColumn('u_name_receive', function ($data) {
                    if (!empty($data->u_id_receive)) {
                        $u_name_receive = User::select('u_name')->where('id', '=', $data->u_id_receive)->get()->first()->u_name;
                        return $u_name_receive;
                    } else {
                        return '-';
                    }
                })
                ->editColumn('stf_status', function ($data) {
                    if ($data->stf_status == '1') {
                        return '<span class="btn-sm btn-warning text-white" style="white-space:nowrap;" data-code="' . $data->stf_code . '" id="view_btn">IN PROGRESS</span>';
                    } else if ($data->stf_status == '2') {
                        return '<span class="btn-sm btn-success" data-code="' . $data->stf_code . '" id="done_btn">DONE</span>';
                    } else {
                        return '<span class="btn-sm btn-info" data-code="' . $data->stf_code . '" id="draft_btn">DRAFT</span>';
                    }
                })
                ->rawColumns(['stf_code', 'qty_send', 'qty_receive', 'stf_status'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('u_name', 'LIKE', "%$search%")
                                ->orWhere('stf_code', 'LIKE', "%$search%")
                                ->orWhereRaw('CONCAT(br_name," ", p_name," ",p_color," ",sz_name) LIKE ?', "%$search%");
                        });
                    }
                    if (!empty($request->get('status'))) {
                        $instance->where(function ($w) use ($request) {
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
        if (request()->ajax()) {
            return datatables()->of(StockTransferDetail::select('stock_transfer_details.id as stfd_id', 'pst_id', 'pl_id', 'st_id_start', 'st_id_end', 'stf_status', 'stf_code', 'br_name', 'p_name', 'p_color', 'sz_name', 'stfd_qty', 'stfd_status', 'pl_code', 'ps_barcode')
                ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'stock_transfer_details.pl_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->where('stf_code', '=', $request->stf_code))
                ->editColumn('article', function ($data) {
                    return '<span data-stfd_id="' . $data->stfd_id . '" data-pst_id="' . $data->pst_id . '" data-pl_id="' . $data->pl_id . '" data-stfd_qty="' . $data->stfd_qty . '" id="cancel_transfer_item" style="white-space:nowrap;">
                        <span style="color:red;font-weight:bold;">' . $data->ps_barcode . '</span> ' . $data->p_name . '<br/>[' . $data->br_name . '] ' . $data->p_color . ' [' . $data->sz_name . '] <i class="badge badge-sm badge-danger">X</i>
                    </span>';
                })
                ->editColumn('stfd_qty', function ($data) {
                    // Get stf_status for this row
                    if ($data->stfd_status == '1') {
                        return '<input type="number" class="form-control form-control-sm" style="width:80px; display:inline-block;" value="' . $data->stfd_qty . '" data-stfd_id="' . $data->stfd_id . '" id="edit_stfd_qty" disabled />';
                    } else if ($data->stf_status == '0' || $data->stf_status == '3') {
                        return '<input type="number" class="form-control form-control-sm" style="width:80px; display:inline-block;" value="' . $data->stfd_qty . '" data-stfd_id="' . $data->stfd_id . '" id="edit_stfd_qty" />';
                    } else {
                        return '<span>' . $data->stfd_qty . '</span>';
                    }
                })
                ->editColumn('st_start', function ($data) {
                    if (!$data->st_id_start) {
                        return 'Store Tidak Ditemukan';
                    }
                    $st_name = DB::table('stores')->select('st_name')->where('id', '=', $data->st_id_start)->get()->first()->st_name;

                    return $st_name;
                })
                ->editColumn('st_end', function ($data) {
                    if (!$data->st_id_end) {
                        return 'Store Tidak Ditemukan';
                    }
                    $st_name = DB::table('stores')->select('st_name')->where('id', '=', $data->st_id_end)->get()->first()->st_name;

                    return $st_name;
                })
                ->editColumn('status', function ($data) {
                    if ($data->stfd_status == '0') {
                        return '<span class="btn-sm btn-warning text-white" style="white-space:nowrap;">Belum Diambil</span>';
                    } else {
                        return '<span class="btn-sm btn-success">Diambil</span>';
                    }
                })
                ->addColumn('pls_qty', function ($data) {
                    $pls_qty = ProductLocationSetup::query()->where('pl_id', $data->pl_id)->where('pst_id', $data->pst_id)->get()->pluck('pls_qty');
                    return $pls_qty;
                })
                ->rawColumns(['article', 'status', 'stfd_qty'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
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
        if (request()->ajax()) {
            return datatables()->of(StockTransferDetail::select('stock_transfer_details.id as stfd_id', 'st_id_start', 'st_id_end', 'stf_code', 'br_name', 'p_name', 'p_color', 'sz_name', 'stfd_qty', 'pl_code', 'product_location_setups.pls_qty',  'product_stocks.ps_barcode as ps_barcode')
                ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'stock_transfer_details.pl_id')
                ->leftJoin('product_location_setups', function ($join) {
                    $join->on('product_location_setups.pst_id', '=', 'product_stocks.id')
                        ->on('product_location_setups.pl_id', '=', 'product_locations.id');
                })
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->where('stf_code', '=', $request->invoice)
                ->where(function ($w) use ($request) {
                    if ($request->mode == 'get') {
                        $w->where('stfd_status', '=', '0');
                    } else {
                        $w->where('stfd_status', '=', '1');
                    }
                }))
                ->editColumn('article', function ($data) use ($request) {
                    if ($request->mode == 'get') {
                        return '[' . $data->br_name . ']<br/><span style="white-space:nowrap;">' . $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name . '</span><br/>
                  <a class="btn btn-sm btn-primary" data-p_name="' . $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name . '" data-stfd_id="' . $data->stfd_id . '" data-stfd_qty="' . $data->stfd_qty . '" data-current_stock="' . $data->pls_qty . '" id="change_transfer_quantity">Req : ' . $data->stfd_qty . '</a>
                  <a class="btn btn-sm btn-primary">Stock : ' . $data->pls_qty . '</a>
                  <a class="btn btn-sm btn-primary">' . $data->pl_code . '</a>
                  <a class="btn btn-sm btn-success" style="font-weight:bold;" data-p_name="' . $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name . '" data-bin="' . $data->pl_code . '" data-stfd_id="' . $data->stfd_id . '" data-stfd_qty="' . $data->stfd_qty . '" data-ps-barcode="' . $data->ps_barcode . '" id="get_transfer_item">Ambil</a>
                  ';
                    } else {
                        return '[' . $data->br_name . ']<br/><span style="white-space:nowrap;">' . $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name . '</span><br/>
                  <a class="btn btn-sm btn-primary" data-p_name="' . $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name . '" data-stfd_id="' . $data->stfd_id . '" data-stfd_qty="' . $data->stfd_qty . '" id="change_transfer_quantity">Jml : ' . $data->stfd_qty . '</a>
                  <a class="btn btn-sm btn-primary">' . $data->pl_code . '</a>
                  ';
                    }
                })
                ->rawColumns(['article'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%")
                                ->orWhere('product_stocks.ps_barcode', 'LIKE', "%$search%")
                                ->orWhere('pl_code', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getTransferItem(Request $request)
    {
        $helper_app_version = null;
        if ($request->stfd_id) {
            $stfd_id = $request->stfd_id;
            $helper_app_version = 2;
        } else if ($request->_stfd_id) {
            $stfd_id = $request->_stfd_id;
            $helper_app_version = 1;
        } else {
            $r['status'] = '400';
            $r['message'] = 'ID Transfer Item tidak ditemukan';
            return json_encode($r);
        }

        $bin_code = isset($request->bin) ? $request->bin : null;
        $sku = isset($request->sku) ? $request->sku : null;
        $qty = isset($request->qty) ? $request->qty : null;

        if ($helper_app_version == 2) {
            $bin_id = ProductLocation::where('pl_code', '=', $bin_code)->pluck('id')->first();
            $product_id = ProductStock::where('ps_barcode', '=', $sku)->pluck('id')->first();

            $check_stfd_exist = StockTransferDetail::where('id', '=', $stfd_id)->where('pst_id', $product_id)->where('pl_id', $bin_id)->where('stfd_qty', $qty)->exists();
            if (!$check_stfd_exist) {
                $r['status'] = '400';
                $r['message'] = 'Transfer Item tidak ditemukan';
                return json_encode($r);
            }
        }

        if (empty($stfd_id)) {
            $r['status'] = '400';
            $r['message'] = 'ID Transfer Item tidak ditemukan';
            return json_encode($r);
        }
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
        return json_encode($r);
    }

    public function stockTransferExec(Request $request)
    {
        // Validasi input
        $main_validate = $request->validate([
            '_st_start' => 'required|integer',
            '_st_end' => 'required|integer',
            '_bin' => 'required|integer',
        ]);

        DB::beginTransaction(); // Mulai transaksi

        try {
            // Validasi Transfer yang dibuka
            $check_opened_stf = StockTransfer::query()->where('stf_code', $request->_stf_code)->get()->first();

            // Validasi transfer yang sedang aktif
            if ($check_opened_stf) {

                // Cek apakah transfer yang sedang aktif berstatus In Progress atau Done
                if (!in_array($check_opened_stf->stf_status, ['0', '3'])) {
                    $r['status'] = '400';
                    $r['message'] = 'Tidak dapat melakukan transfer, karena ada transfer aktif yang berstatus In Progress atau Done.';
                    return json_encode($r);
                }

                // Cek apakah lokasi awal dan tujuan sesuai dengan transfer yang sedang aktif
                if ($check_opened_stf->st_id_start != $request->_st_start || $check_opened_stf->st_id_end != $request->_st_end) {
                    $r['status'] = '400';
                    $r['message'] = 'Tidak dapat melakukan transfer karena lokasi awal atau tujuan tidak sesuai dengan transfer yang sedang aktif.';
                    return json_encode($r);
                }

                // Jika semua validasi berhasil, gunakan stf_code dan id dari transfer yang sedang aktif
                $stf_code = $check_opened_stf->stf_code;
                $stf_id = $check_opened_stf->id;
            } else {

                // Jika tidak ada transfer yang sedang aktif, buat transfer baru
                $check_stf = StockTransfer::select('id')->where('stf_status', '=', '0')->where('u_id', '=', Auth::user()->id)->get()->first();
                $stf_code = 'TF' . date('YmdHis') . str_pad(rand(0, pow(10, 3) - 1), 3, '0', STR_PAD_LEFT);

                // Jika ada transfer tanpa status yang sedang aktif, gunakan id-nya
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
            }

            if (!empty($stf_id)) {
                $insert = array();
                foreach ($request->_arr as $row) {
                    $existingDetail = StockTransferDetail::where([
                        'stf_id' => $stf_id,
                        'pst_id' => $row[1],
                        'pl_id' => $request->_bin,
                        'stfd_status' => '0',
                    ])->first();

                    if ($existingDetail) {
                        // Update the existing record
                        $existingDetail->update([
                            'stfd_qty' => $existingDetail->stfd_qty + $row[3],
                        ]);
                    } else {
                        // Prepare new record for insertion
                        $insert[] = [
                            'stf_id' => $stf_id,
                            'pst_id' => $row[1],
                            'pl_id' => $request->_bin,
                            'stfd_qty' => $row[3],
                            'stfd_status' => '0',
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                    }

                    // Update the ProductLocationSetup quantity
                    $pls_update = ProductLocationSetup::where([
                        'id' => $row[0],
                    ])->update([
                        'pls_qty' => ($row[2] - $row[3]),
                    ]);
                }

                // Insert new records if any
                if (!empty($insert)) {
                    StockTransferDetail::insert($insert);
                }

                DB::commit(); // Commit transaction if successful
                $r['status'] = '200';
                $r['code'] = $stf_code;
            } else {
                DB::rollBack(); // Rollback transaction if failed
                $r['status'] = '400';
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi exception
            $r['status'] = '500';
            $r['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
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
            foreach ($data as $row) {
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
        $check_unprocessed_transfer = StockTransfer::where('u_id', Auth::user()->id)
            ->where('stf_status', '0')
            ->exists();

        if ($check_unprocessed_transfer) {
            $r['status'] = '400';
            $r['message'] = 'Anda sudah memiliki transfer yang belum menggantung. Silakan selesaikan terlebih dahulu.';
            return json_encode($r);
        }

        $store_start_id = $request->input('st_start');
        $store_end_id = $request->input('st_end');

        if (!$store_start_id || !$store_end_id) {
            $r['status'] = '400';
            $r['message'] = 'Store awal dan store akhir harus dipilih.';
            return json_encode($r);
        }


        $store_start_exists = Store::where('id', $store_start_id)->exists();
        if (!$store_start_exists) {
            $r['status'] = '400';
            $r['message'] = 'Store awal tidak ditemukan.';
            return json_encode($r);
        }

        $store_end_exists = Store::where('id', $store_end_id)->exists();
        if (!$store_end_exists) {
            $r['status'] = '400';
            $r['message'] = 'Store tujuan tidak ditemukan.';
            return json_encode($r);
        }

        $u_id = Auth::user()->id;

        try {
            DB::beginTransaction();

            if ($request->hasFile('importFile')) {
                $file = $request->file('importFile');
                // membuat nama file unik
                $nama_file = rand() . $file->getClientOriginalName();

                // upload ke folder file di dalam folder public
                $file->move('excel', $nama_file);

                $import = new TransferImport;
                $data = Excel::toArray($import, public_path('excel/' . $nama_file));

                if (count($data[0]) > 0) {
                    $processData = $this->processImportData(array_slice($data[0], 1), $store_start_id);

                    if (!empty($processData['missingBarcode'])) {
                        $r['status'] = '400';
                        $notesCount = array_count_values(array_column($processData['missingBarcode'], 'note'));
                        $messageParts = [];
                        if (!empty($notesCount['Stok tidak mencukupi'])) {
                            $messageParts[] = $notesCount['Stok tidak mencukupi'] . ' stok tidak mencukupi';
                        }
                        if (!empty($notesCount['Bin not found'])) {
                            $messageParts[] = $notesCount['Bin not found'] . ' bin tidak ditemukan';
                        }
                        if (!empty($notesCount['Product not found'])) {
                            $messageParts[] = $notesCount['Product not found'] . ' produk tidak ditemukan';
                        }
                        $r['message'] = 'Terdapat: ' . implode(', ', $messageParts);
                        $r['missingBarcode'] = $processData['missingBarcode'];
                        return json_encode($r);
                    }

                    if (empty($processData['processedData'])) {
                        $r['status'] = '400';
                        $r['message'] = 'Tidak ada data yang valid untuk diproses.';
                        return json_encode($r);
                    }
                    // Get the stock transfer ID based on the provided stf_code
                    $check_pending_stf = StockTransfer::query()
                        ->where('u_id', '=', Auth::user()->id)
                        ->where('stf_status', '=', '0')
                        ->first();

                    if (!$check_pending_stf) {
                        // Create a new stock transfer if it doesn't exist
                        $stf_code = 'TF' . date('YmdHis') . str_pad(rand(0, pow(10, 3) - 1), 3, '0', STR_PAD_LEFT);

                        $stf_id = StockTransfer::create([
                            'u_id' => $u_id,
                            'st_id_start' => $store_start_id,
                            'st_id_end' => $store_end_id,
                            'stf_code' => $stf_code,
                            'stf_status' => '0',
                            'created_at' => now(),
                        ])->id;

                        // Prepare the data for insertion
                        $insertData = [];
                        foreach ($processData['processedData'] as $item) {
                            $insertData[] = [
                                'stf_id' => $stf_id,
                                'pst_id' => $item['product_stock_id'],
                                'pl_id' => $item['bin_id'],
                                'stfd_qty' => $item['tf_qty'],
                                'stfd_status' => '0',
                                'created_at' => now(),
                            ];
                        }

                        // Insert all data at once
                        StockTransferDetail::insert($insertData);

                        foreach ($processData['processedData'] as $item) {
                            $pls_update = ProductLocationSetup::where([
                                'pst_id' => $item['product_stock_id'],
                                'pl_id' => $item['bin_id'],
                            ])->update([
                                'pls_qty' => ($item['pls_qty'] - $item['tf_qty'])
                            ]);

                            if (!$pls_update) {
                                throw new \Exception('Failed to update product location setup for barcode: ' . $item['barcode']);
                            }
                        }

                        $r['data'] = $processData;
                        $r['status'] = '200';
                    } else {
                        // Use the existing stock transfer ID
                        $r['status'] = '400';
                        $r['message'] = 'Anda sudah memiliki transfer yang belum selesai. Silakan selesaikan terlebih dahulu.';
                    }
                } else {
                    $r['status'] = '419';
                }
            } else {
                $r['status'] = '400';
            }

            // delete file
            unlink(public_path('excel/' . $nama_file));

            DB::commit();
            $r['message'] = 'Data berhasil diimpor.';
            return json_encode($r);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($nama_file)) {
                unlink(public_path('excel/' . $nama_file));
            }
            $r['status'] = '400';
            $r['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            return json_encode($r);
        }
    }

    private function processImportData($data, $st_id_start)
    {
        $processedData = [];
        $missingBarcode = [];

        foreach ($data as $item) {
            $bin = $item[0];
            $barcode = $item[1];
            $qty = $item[2];

            $product_id = ProductStock::where('ps_barcode', '=', $barcode)->first();
            $bin_id = ProductLocation::where('pl_code', '=', $bin)->where('st_id', '=', $st_id_start)->first();

            if (!$product_id) {
                $missingBarcode[] = ['barcode' => $barcode, 'bin' => $bin, 'qty_req' => $qty, 'qty_stock' => 0, 'note' => 'Product not found'];
                continue;
            }

            if (!$bin_id) {
                $missingBarcode[] = ['barcode' => $barcode, 'bin' => $bin, 'qty_req' => $qty, 'qty_stock' => 0, 'note' => 'Bin not found'];
                continue;
            }

            $pls_id = ProductLocationSetup::where('pst_id', '=', $product_id->id)
                ->where('pl_id', '=', $bin_id->id)
                ->first();

            if ($pls_id && $pls_id->pls_qty >= $qty) {
                $key = $barcode . '_' . $bin;
                if (!isset($processedData[$key])) {
                    $processedData[$key] = [
                        'bin' => $bin,
                        'bin_id' => $bin_id->id,
                        'product_stock_id' => $product_id->id,
                        'barcode' => $barcode,
                        'tf_qty' => $qty,
                        'pls_qty' => $pls_id->pls_qty,
                    ];
                }
            } else {
                $missingBarcode[] = ['barcode' => $barcode, 'bin' => $bin, 'qty_req' => $qty, 'qty_stock' => $pls_id ? $pls_id->pls_qty : 0, 'note' => 'Stok tidak mencukupi'];
                continue;
            }
        }

        return [
            'processedData' => array_values($processedData),
            'missingBarcode' => $missingBarcode,
        ];
    }

    public function importCompareDoneTransfer(Request $request)
    {
        $stf_code = $request->stf_code_modal;
        $check = StockTransfer::select('id')->where('stf_code', '=', $stf_code)->get()->first();

        if (!$check) {
            $r['status'] = '404';
            $r['message'] = 'Stock transfer tidak ditemukan';
            return response()->json($r);
        }

        if (!$request->file('importFile')) {
            $r['status'] = '400';
            $r['message'] = 'File tidak ditemukan';
            return response()->json($r);
        }

        $file = $request->file('importFile');
        // membuat nama file unik
        $nama_file = rand() . $file->getClientOriginalName();

        // upload ke folder di dalam folder public
        $file->move('excel/import_transfer_compare/', $nama_file);

        $import = new TransferDoneCompareImport;

        if (!$import) {
            $r['status'] = '400';
            $r['message'] = 'Format file tidak sesuai';
            return response()->json($r);
        }

        $data = Excel::toArray($import, public_path('excel/import_transfer_compare/' . $nama_file));

        if ($data[0][0][0] != 'SKU' || $data[0][0][1] != 'Quantity') {
            $r['status'] = '400';
            $r['message'] = 'Format file tidak sesuai';
            return response()->json($r);
        }

        $transfer_data = DB::table('stock_transfers')
            ->join('stock_transfer_details', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
            ->leftJoin('stock_transfer_detail_statuses', 'stock_transfer_details.id', '=', 'stock_transfer_detail_statuses.stfd_id')
            ->join('product_stocks', 'stock_transfer_details.pst_id', '=', 'product_stocks.id')
            ->select('product_stocks.ps_barcode', 'stock_transfer_detail_statuses.stfd_id', 'stock_transfer_detail_statuses.stfds_qty', 'stock_transfer_details.stfd_qty')
            ->where('stock_transfers.id', $check->id)
            ->groupBy('product_stocks.ps_barcode', 'stock_transfer_detail_statuses.stfd_id', 'stock_transfer_detail_statuses.stfds_qty')
            ->get();

        // Start from the second array (skip header)
        $unmatchedData = [];

        // Step 1: Akumulasi Quantity per SKU dari file import
        $importedSummary = [];
        foreach (array_slice($data[0], 1) as $item) {
            $barcode = trim((string) $item[0]);
            $qty = intval($item[1]);

            if (!isset($importedSummary[$barcode])) {
                $importedSummary[$barcode] = 0;
            }
            $importedSummary[$barcode] += $qty;
        }

        // Step 2: Bandingkan hasil akumulasi dengan transfer data
        foreach ($importedSummary as $barcode => $qty) {
            $matchingTransfer = $transfer_data->firstWhere('ps_barcode', $barcode);

            if (!$matchingTransfer || intval($matchingTransfer->stfds_qty) !== $qty) {
                $unmatchedData[] = [
                    'barcode' => $barcode,
                    'qty' => $qty,
                    'expected_qty' => $matchingTransfer ? $matchingTransfer->stfd_qty : null,
                    'type' => 'import',
                ];
            }
        }

        // Step 3: Cek jika ada barcode di transfer yang tidak ada di import
        foreach ($transfer_data as $transfer) {
            if (!array_key_exists($transfer->ps_barcode, $importedSummary)) {
                $unmatchedData[] = [
                    'barcode' => $transfer->ps_barcode,
                    'qty' => null,
                    'expected_qty' => $transfer->stfd_qty,
                    'type' => 'transfer',
                ];
            }
        }


        return response()->json([
            'status' => '200',
            'unmatched' => $unmatchedData,
        ]);
    }

    public function exportData(Request $request)
    {
        $tf_code = $request->stf_code;
        return Excel::download(new StdExportDraft($tf_code), 'stock_transfer_draft_' . $tf_code . '.xlsx');
    }

    public function changeTransferQty(Request $request)
    {
        $stfd_id = $request->id;
        $qty = $request->qty;

        try {
            DB::beginTransaction();

            $stfd = StockTransferDetail::find($stfd_id);
            if (!$stfd) {
                return response()->json(['status' => '404', 'message' => 'Detail transfer stok tidak ditemukan']);
            }

            $pls = ProductLocationSetup::where('pst_id', $stfd->pst_id)
                ->where('pl_id', $stfd->pl_id)
                ->first();

            if (!$pls) {
                return response()->json(['status' => '404', 'message' => 'Data Bin tidak ditemukan']);
            }

            if ($qty > ($pls->pls_qty + $stfd->stfd_qty)) {
                return response()->json(['status' => '400', 'message' => 'Jumlah transfer melebihi stok tersedia']);
            }

            // Update qty di StockTransferDetail
            $old_qty = $stfd->stfd_qty;
            $stfd->update(['stfd_qty' => $qty]);

            // Update qty di ProductLocationSetup
            $selisih = $qty - $old_qty;
            $pls->update(['pls_qty' => $pls->pls_qty - $selisih]);

            DB::commit();
            return response()->json(['status' => '200', 'message' => 'Jumlah transfer berhasil diubah']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => '500', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
