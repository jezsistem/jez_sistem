<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use App\Exports\ScanExport;
use App\Exports\ScanBINExport;
use Maatwebsite\Excel\Facades\Excel;

class ScanAdjustmentController extends Controller
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
            'st_id' => DB::table('stores')->where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.scan_adjustment.scan_adjustment', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('scan_adjustments')->select('scan_adjustments.id as id', 'sa_custom', 'sa_approve', 'sa_executor', 'st_name', 'scan_adjustments.st_id', 'sa_code', 'u_name', 'sa_description', 'sa_status', 'scan_adjustments.created_at', 'scan_adjustments.updated_at')
            ->leftJoin('users', 'users.id', '=', 'scan_adjustments.u_id')
            ->leftJoin('stores', 'stores.id', '=', 'scan_adjustments.st_id')
            ->where('sa_status', '!=', '2'))
            ->editColumn('created_at', function($d) {
                return date('d/m/Y H:i:s', strtotime($d->created_at));
            })
            ->editColumn('sa_status', function($d) {
                if ($d->sa_status == '1') {
                    return 'Selesai';
                } else if ($d->sa_status == '0') {
                    return 'Proses';
                } else {
                    return 'Cancel';
                }
            })
            ->editColumn('approve', function ($d) {
                if (!empty($d->sa_approve)) {
                    return DB::table('users')->where('id', '=', $d->sa_approve)->first()->u_name;
                } else {
                    return 'Menunggu Approval';
                }
            })
            ->editColumn('executor', function ($d) {
                if (!empty($d->sa_executor)) {
                    return DB::table('users')->where('id', '=', $d->sa_executor)->first()->u_name.'<br/>'.date('d/m/Y H:i:s', strtotime($d->updated_at));
                } else {
                    return '-';
                }
            })
            ->editColumn('sa_custom', function ($d) {
                if ($d->sa_custom == '1') {
                    $br = '';
                    $sab = DB::table('scan_adjustment_brands')->select('br_name')
                    ->leftJoin('brands', 'brands.id', '=', 'scan_adjustment_brands.br_id')->where('scan_adjustment_brands.sa_id', '=', $d->id)
                    ->groupBy('scan_adjustment_brands.id')->get();
                    if (!empty($sab->first())) {
                        foreach ($sab as $row) {
                            if ($br == '') {
                                $br .= $row->br_name;
                            } else {
                                $br .= ', '.$row->br_name;
                            }
                        }
                    }
                    $psc = '';
                    $sasc = DB::table('scan_adjustment_sub_categories')->select('psc_name')
                    ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'scan_adjustment_sub_categories.psc_id')
                    ->groupBy('scan_adjustment_sub_categories.id')->where('scan_adjustment_sub_categories.sa_id', '=', $d->id)->get();
                    if (!empty($sasc->first())) {
                        foreach ($sasc as $row) {
                            if ($psc == '') {
                                $psc .= $row->psc_name;
                            } else {
                                $psc .= ', '.$row->psc_name;
                            }
                        }
                    }
                    return "
                        <div class='row'>
                        <button class='btn btn-sm btn-primary col-6' title='".$br."' data-status='".$d->sa_status."' data-id='".$d->id."' id='brand_btn'>B</button>
                        <button class='btn btn-sm btn-info col-6' title='".$psc."' data-status='".$d->sa_status."' data-id='".$d->id."' id='psc_btn' style='background:#007bff;'>SK</button>
                        <button class='btn btn-sm btn-inventory col-12' data-id='".$d->id."' id='bin_btn'><i class='fa fa-download text-white'></i> BIN</button>
                        </div>
                    ";
                } else {
                    return 'All';
                }
            })
            ->editColumn('action', function($d) {
                if ($d->sa_status != '2') {
                    $approval = '';
                    $start = '';
                    $finish = '';
                    $report = '';
                    if ($d->sa_status == '0' AND Auth::user()->id == '52') {
                        $prop = 'col-6 col-md-4 col-xl-4';
                    } else {
                        $prop = 'col-12';
                    }
                    if ($d->sa_status == '0') {
                        if (empty($d->sa_approve)) {
                            $approval = "<button class='btn btn-sm btn-warning ".$prop."' data-id='".Crypt::encryptstring($d->id)."' id='approve_btn'><i class='fa fa-check'></i></button>";
                        }
                        if (!empty($d->sa_approve)) {
                            $finish = "<button class='btn btn-sm btn-primary col-12' data-id='".Crypt::encryptstring($d->id)."' id='finish_btn'><i class='fa fa-pen'></i> Eksekusi</button>";
                        }
                    }
                    if (!empty($d->sa_approve)) {
                        $start = "<button href='".url('start_scan_adjustment')."/".Crypt::encryptstring($d->id)."' data-approve='".$d->u_name."' class='btn btn-sm btn-info ".$prop."' style='background:#007bff;' id='start_btn'><i class='fa fa-play'></i></button>";
                    }
                    $report = "<button data-id='".$d->id."' class='btn btn-sm btn-info ".$prop."' id='report_btn'><i class='fa fa-upload'></i></button>";
                    $summary = "<button data-id='".$d->id."' class='btn btn-sm btn-inventory ".$prop."' id='summary_btn'><i class='fa fa-eye text-white'></i></button>";
                    $btn = '<div class="row d-flex">'.$approval.' '.$start.' '.$report.' '.$summary.' '.$finish.'</div>';
                    return $btn;
                }
                return "-";
            })
            ->rawColumns(['sa_custom', 'action', 'executor'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('sa_code', 'LIKE', "%$search%")
                        ->orWhere('u_name', 'LIKE', "%$search%")
                        ->orWhere('st_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function approvalData(Request $req)
    {
        $id = Crypt::decryptstring($req->post('id'));
        $update = DB::table('scan_adjustments')->where('id', '=', $id)->update([
            'sa_approve' => Auth::user()->id,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function finishData(Request $req)
    {
        $id = Crypt::decryptstring($req->post('id'));
        $check = DB::table('scan_adjustments')->where('id', '=', $id)
        ->whereNotNull('sa_approve')
        ->where('sa_status', '=', '0')->exists();
        if (!$check) {
            $r['status'] = '400';
            return json_encode($r);
        }
        $get = DB::table('scan_adjustment_details')->select('pls_id', 'qty_so')->where('sa_id', '=', $id)->get();
        if (!empty($get->first())) {
            $update = DB::table('scan_adjustments')->where('id', '=', $id)->update([
                'sa_executor' => Auth::user()->id,
                'sa_status' => '1',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            foreach ($get as $row) {
                DB::table('product_location_setups')->where('id', '=', $row->pls_id)->update([
                    'pls_qty' => $row->qty_so
                ]);
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function storeData(Request $request)
    {
        $mode = $request->input('_mode');
        $id = $request->input('_id');
        $sa_code = 'SADJ'.date('YmdHis');
        $custom = '0';

        if (!empty($request->input('br_id'))) {
            $custom = '1';
        }

        if (!empty($request->input('psc_id'))) {
            $custom = '1';
        }

        $data = [
            'st_id' => $request->input('st_id'),
            'sa_description' => $request->input('sa_description')
        ];

        $customa = [
            'sa_custom' => $custom
        ];
        $u_id = [
            'u_id' => Auth::user()->id
        ];
        $code = [
            'sa_code' => $sa_code
        ];
        $status = [
            'sa_status' => '0'
        ];
        $created = [
            'created_at' => date('Y-m-d H:i:s')
        ];
        $updated = [
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($mode == 'add') {
            $data = array_merge($data, $customa, $u_id, $code, $status, $created, $updated);
            $save = DB::table('scan_adjustments')->insertGetId($data);
            $br_id = $request->input('br_id');
            if (!empty($br_id)) {
                $insert = array();
                $exp = explode(',', $br_id);
                for ($i = 0; $i < count($exp); $i++) {
                    if (empty($exp[$i])) {
                        continue;
                    }
                    $insert[] = [
                        'sa_id' => $save,
                        'br_id' => $exp[$i]
                    ];
                }
                DB::table('scan_adjustment_brands')->insert($insert);
            }
            $psc_id = $request->input('psc_id');
            if (!empty($psc_id)) {
                $insert = array();
                $exp = explode(',', $psc_id);
                for ($i = 0; $i < count($exp); $i++) {
                    if (empty($exp[$i])) {
                        continue;
                    }
                    $insert[] = [
                        'sa_id' => $save,
                        'psc_id' => $exp[$i]
                    ];
                }
                DB::table('scan_adjustment_sub_categories')->insert($insert);
            }
        } else {
            $data = array_merge($data, $updated);
            $save = DB::table('scan_adjustments')->where('id', '=', $id)->update($data);
        }
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $id = $request->input('_id');
        $delete = DB::table('scan_adjustment_brands')->where('sa_id', '=', $id)->delete();
        $delete = DB::table('scan_adjustment_sub_categories')->where('sa_id', '=', $id)->delete();
        $delete = DB::table('scan_adjustments')->where('id', '=', $id)->delete();
        if ($delete) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function scanPanel(Request $req)
    {
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;

        $id = Crypt::decryptstring($req->id);
        $get = DB::table('scan_adjustments')->where('scan_adjustments.id', '=', $id)->select('scan_adjustments.id as id', 'sa_code', 'st_id', 'st_name', 'sa_custom', 'sa_status')
        ->leftJoin('stores', 'stores.id', '=', 'scan_adjustments.st_id')->first();

        $bin_custom = null;

        if ($get->sa_custom == '1') {
            $st_id = $get->st_id;
            $br_id = array();
            $sab = DB::table('scan_adjustment_brands')->where('sa_id', '=', $get->id)->get();
            if (!empty($sab->first())) {
                foreach ($sab as $row) {
                    $br_id[] = [$row->br_id];
                }
            }
            $psc_id = array();
            $sab = DB::table('scan_adjustment_sub_categories')->where('sa_id', '=', $get->id)->get();
            if (!empty($sab->first())) {
                foreach ($sab as $row) {
                    $psc_id[] = [$row->psc_id];
                }
            }

            $bin_custom = DB::table('product_locations')->selectRaw('ts_product_locations.id as id, pl_code, sum(ts_product_location_setups.pls_qty) as qty')
            ->leftJoin('product_location_setups', 'product_location_setups.pl_id', '=', 'product_locations.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->where(function($w) use ($br_id, $psc_id, $st_id) {
                if (count($br_id) > 0) {
                    $w->whereIn('products.br_id', $br_id);
                }
                if (count($psc_id) > 0) {
                    $w->whereIn('products.psc_id', $psc_id);
                }
                $w->where('product_locations.st_id', '=', $st_id);

            })
            ->having('qty', '>', '0')
            ->groupBy('product_locations.id')
            ->orderBy('pl_code')
            ->get();
        }
        if (!empty($get)) {
            $data = [
                'title' => $title,
                'sa_code' => $get->sa_code,
                'st_name' => $get->st_name,
                'sa_custom' => $get->sa_custom,
                'sa_status' => $get->sa_status,
                'bin_custom' => $bin_custom,
                'st_id' => $get->st_id,
                'user' => $user_data,
                'segment' => request()->segment(1),
            ];
            return view('app.scan_adjustment.do_scan.do_scan', compact('data'));
        } else {
            return "Sesi sudah berakhir";
        }
    }

    public function getScanDatatables(Request $request)
    {
        $sa_code = $request->get('sa_code');
        $scan_filter = $request->get('scan_filter');
        $sa_id = DB::table('scan_adjustments')
            ->where('sa_code', '=', $sa_code)->first()->id;
        $sa_custom = $request->get('sa_custom');
        $br_id = array();
        $psc_id = array();
        if ($sa_custom == '1') {
            $sab = DB::table('scan_adjustment_brands')->where('sa_id', '=', $sa_id)->get();
            if (!empty($sab->first())) {
                foreach ($sab as $row) {
                    $br_id[] = [$row->br_id];
                }
            }
            $sab = DB::table('scan_adjustment_sub_categories')->where('sa_id', '=', $sa_id)->get();
            if (!empty($sab->first())) {
                foreach ($sab as $row) {
                    $psc_id[] = [$row->psc_id];
                }
            }
        }
        if(request()->ajax()) {
            return datatables()->of(DB::table('product_location_setups')->select('scan_adjustment_details.id as id', 'sa_status', 'br_name', 'p_name', 'p_color', 'sz_name', 'pls_qty', 'qty_so', 'ps_barcode', 'p_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('scan_adjustment_details', 'scan_adjustment_details.pls_id', '=', 'product_location_setups.id')
            ->leftJoin('scan_adjustments', 'scan_adjustments.id', '=', 'scan_adjustment_details.sa_id')
            ->where('product_locations.st_id', '=', $request->get('st_id'))
            ->where('product_location_setups.pl_id', '=', $request->get('pl_id'))
            ->where(function($w) use ($scan_filter, $sa_id, $br_id, $psc_id) {
                if ($scan_filter == 'scanned') {
                    $w->where('scan_adjustment_details.sa_id', '=', $sa_id);
                } else {
                    $w->whereNull('scan_adjustment_details.pls_id');
                }
            })
            ->groupBy('product_location_setups.id')
            ->orderByDesc('qty_so'))
            ->editColumn('article', function($d) use ($scan_filter) {
                if ($scan_filter == 'scanned') {
                    if ($d->sa_status != 1) {
                        return '<span style="font-weight:bold;">['.$d->br_name.'] '.$d->p_name.' '.$d->p_color.' ['.$d->sz_name.']</span><br/>
                            <a class="btn btn-sm btn-danger" data-id="'.$d->id.'" data-qty_so="'.$d->qty_so.'" id="min_btn">-</a> <a class="btn btn-sm btn-success" data-id="'.$d->id.'" data-qty_so="'.$d->qty_so.'" id="plus_btn">+</a> <input class="col-8 edit_qty_so" data-id="'.$d->id.'" value="'.$d->qty_so.'" type="text"/>';
                    } else {
                        return '['.$d->br_name.'] '.$d->p_name.' '.$d->p_color.' ['.$d->sz_name.']';
                    }
                } else {
                    return '['.$d->br_name.'] '.$d->p_name.' '.$d->p_color.' ['.$d->sz_name.']';
                }
            })
            ->editColumn('barcode', function($d) {
                return $d->ps_barcode;
            })
            ->editColumn('action', function($d) {
                $qty_so = $d->qty_so;
                $type = '';
                $cq = "<a class='btn btn-sm btn-primary'>".$d->pls_qty."</a>";
                if ($qty_so >= '0') {
                    if ($qty_so > $d->pls_qty) {
                        $type = "<a class='btn btn-sm btn-info'>+</a>";
                    } else if ($qty_so < $d->pls_qty) {
                        $type = "<a class='btn btn-sm btn-danger'>-</a>";
                    } else {
                        $type = "<a class='btn btn-sm btn-info' style='background:#007bff;'>=</a>";
                    }
                    $sq = "<a class='btn btn-sm btn-success'>".$qty_so."</a>";
                } else {
                    $sq = "<a class='btn btn-sm btn-success'>-</a>";
                }
                $btn = $cq.' '.$sq.' '.$type;
                return $btn;
            })
            ->rawColumns(['article', 'action'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%")
                        ->orWhere('ps_barcode', 'like', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function updateQty(Request $request)
    {
        $id = $request->post('id');
        $qty = $request->post('qty');
        $update = DB::table('scan_adjustment_details')->where('id', '=', $id)->update([
            'qty_so' => $qty
        ]);
        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function fetchBIN(Request $request)
    {
        if($request->post('query'))
        {
            $query = $request->post('query');
            $st_id = $request->post('st_id');
            $data = DB::table('product_locations')->select("id", "pl_code")
            ->whereRaw('pl_code LIKE ?', "%$query%")
            ->where('product_locations.st_id', '=', $st_id)
            ->orderBy('pl_code')
            ->limit(10)
            ->get();
            $output = '';
            if (!empty($data)) {
                foreach($data as $row) {
                    $output .= '
                    <a class="btn btn-sm btn-primary col-12" data-id="'.$row->id.'" data-pl_code="'.$row->pl_code.'" id="add_to_item_list">'.$row->pl_code.'</a>
                    ';
                }
            } else {
                $output .= '<a class="btn btn-sm btn-primary">Tidak ditemukan</a>';
            }
            echo $output;
        }
    }

    public function doScan(Request $request)
    {
        $barcode = $request->post('barcode');
        $st_id = $request->post('st_id');
        $pl_id = $request->post('pl_id');
        $sa_code = $request->post('sa_code');
        $sa_id = DB::table('scan_adjustments')
        ->where('sa_code', '=', $sa_code)->first()->id;
        $check = DB::table('product_location_setups')->selectRaw("ts_product_location_setups.id as id, sum(ts_product_location_setups.pls_qty) as qty")
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->where('product_locations.st_id', '=', $st_id)
        ->where('product_location_setups.pl_id', '=', $pl_id)
        ->where('product_stocks.ps_barcode', '=', $barcode)
        ->groupBy('product_location_setups.id')->first();
        if (!empty($check)) {
            $sad = DB::table('scan_adjustment_details')
            ->where([
                'sa_id' => $sa_id,
                'u_id' => Auth::user()->id,
                'pls_id' => $check->id
            ])->select('id', 'qty_so')->first();
            if (!empty($sad)) {
                $qty_so = ($sad->qty_so+1);
                if ($qty_so > $check->qty) {
                    $diff = ($qty_so - $check->qty);
                    $type = '+';
                } else if ($qty_so < $check->qty) {
                    $diff = ($check->qty - $qty_so);
                    $type = '-';
                } else {
                    $diff = 0;
                    $type = '=';
                }
                DB::table('scan_adjustment_details')->where('id', '=', $sad->id)->update([
                    'qty_so' => $qty_so,
                    'qty' => $check->qty,
                    'mad_type' => $type,
                    'mad_diff' => $diff,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $qty_so = 1;
                if ($qty_so > $check->qty) {
                    $diff = ($qty_so - $check->qty);
                    $type = '+';
                } else if ($qty_so < $check->qty) {
                    $diff = ($check->qty - $qty_so);
                    $type = '-';
                } else {
                    $diff = 0;
                    $type = '=';
                }
                $insert = [
                    'sa_id' => $sa_id,
                    'u_id' => Auth::user()->id,
                    'pls_id' => $check->id,
                    'qty_so' => $qty_so,
                    'qty' => $check->qty,
                    'mad_type' => $type,
                    'mad_diff' => $diff,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                DB::table('scan_adjustment_details')->insert($insert);
            }
            $r['status'] = '200';
        } else {
            $pst = DB::table('product_stocks')->select('id', 'ps_barcode')->where('ps_barcode', '=', $barcode)->first();
            if (!empty($pst)) {
                $pst_id = $pst->id;
                $insert = [
                    'pls_qty' => '0',
                    'pl_id' => $pl_id,
                    'pst_id' => $pst_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $pls_id = DB::table('product_location_setups')->insertGetId($insert);

                $do = [
                    'sa_id' => $sa_id,
                    'u_id' => Auth::user()->id,
                    'pls_id' => $pls_id,
                    'qty_so' => '1',
                    'qty' => '0',
                    'mad_type' => '+',
                    'mad_diff' => '1',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $manual = DB::table('scan_adjustment_details')->insert($do);
                if (!empty($manual)) {
                    $r['status'] = '200';
                } else {
                    $r['status'] = '400';
                }
            } else {
                $r['barcode'] = $barcode;
                $r['status'] = '400';
            }
        }
        return json_encode($r);
    }

    public function doReset(Request $request)
    {
        $sa_code = $request->post('sa_code');
        $u_id = Auth::user()->id;
        $pl_id = $request->post('pl_id');

        $sa_id = DB::table('scan_adjustments')
            ->where('sa_code', '=', $sa_code)->first()->id;
        $data = [
            'u_id' => $u_id,
            'pl_id' => $pl_id,
            'sa_id' => $sa_id
        ];
        $delete = DB::table('scan_adjustment_details')
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'scan_adjustment_details.pls_id')
        ->where('scan_adjustment_details.sa_id', '=', $sa_id)
        ->where('product_location_setups.pl_id', '=', $pl_id)
        ->where('scan_adjustment_details.u_id', '=', $u_id)->delete();
        if (!empty($delete)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function doSync(Request $request)
    {
        $sa_code = $request->post('sa_code');
        $u_id = Auth::user()->id;
        $pl_id = $request->post('pl_id');
        $sa_custom = $request->post('sa_custom');
        $sa_id = DB::table('scan_adjustments')
            ->where('sa_code', '=', $sa_code)->first()->id;

        $br_id = array();
        $psc_id = array();
        if ($sa_custom == '1') {
            $sab = DB::table('scan_adjustment_brands')->where('sa_id', '=', $sa_id)->get();
            if (!empty($sab->first())) {
                foreach ($sab as $row) {
                    $br_id[] = [$row->br_id];
                }
            }
            $sab = DB::table('scan_adjustment_sub_categories')->where('sa_id', '=', $sa_id)->get();
            if (!empty($sab->first())) {
                foreach ($sab as $row) {
                    $psc_id[] = [$row->psc_id];
                }
            }
        }

        $get = DB::table('scan_adjustment_details')->select('pls_id')
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'scan_adjustment_details.pls_id')
        ->where('scan_adjustment_details.sa_id', '=', $sa_id)
        ->where('product_location_setups.pl_id', '=', $pl_id)
        ->where('scan_adjustment_details.u_id', '=', $u_id)->get();
        $pls_id = array();
        if (!empty($get->first())) {
            foreach ($get as $row) {
                $pls_id[] = [$row->pls_id];
            }
        }
        $pls = DB::table('product_location_setups')->select('product_location_setups.id', 'pls_qty')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->where(function($w) use ($br_id, $psc_id) {
            if (count($br_id) > 0) {
                $w->whereIn('products.br_id', $br_id);
            }
            if (count($psc_id) > 0) {
                $w->whereIn('products.psc_id', $psc_id);
            }
        })
        ->where('product_location_setups.pl_id', '=', $pl_id)
        ->whereNotIn('product_location_setups.id', $pls_id)
        ->where('pls_qty', '>', '0')
        ->groupBy('product_location_setups.id')->get();
        if (!empty($pls->first())) {
            foreach ($pls as $row) {
                $qty_so = 0;
                $diff = ($row->pls_qty - $qty_so);
                $type = '-';
                $insert = [
                    'sa_id' => $sa_id,
                    'u_id' => Auth::user()->id,
                    'pls_id' => $row->id,
                    'qty_so' => $qty_so,
                    'qty' => $row->pls_qty,
                    'mad_type' => $type,
                    'mad_diff' => $diff,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                DB::table('scan_adjustment_details')->insert($insert);
            }
        }
        $r['status'] = '200';
        return json_encode($r);
    }

    public function loadQty(Request $request)
    {
        $sa_code = $request->post('sa_code');
        $pl_id = $request->post('pl_id');
        $sa_id = DB::table('scan_adjustments')
            ->where('sa_code', '=', $sa_code)->first()->id;
        $r['current_qty'] = '';
        $r['scanned_qty'] = '';

        $current_qty = DB::table('product_location_setups')->selectRaw("sum(ts_product_location_setups.pls_qty) as qty")
        ->where('product_location_setups.pl_id', '=', $pl_id)->first();
        $scanned_qty = DB::table('scan_adjustment_details')->selectRaw("sum(ts_scan_adjustment_details.qty_so) as qty")
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'scan_adjustment_details.pls_id')
        ->where('scan_adjustment_details.sa_id', '=', $sa_id)
        ->where('product_location_setups.pl_id', '=', $pl_id)->first();
        if (!empty($current_qty)) {
            $r['current_qty'] = $current_qty->qty;
        }
        if (!empty($scanned_qty)) {
            $r['scanned_qty'] = $scanned_qty->qty;
        }
        $r['status'] = '200';
        return json_encode($r);
    }

    public function doExport(Request $request)
    {
        $id = $request->get('id');
        return Excel::download(new ScanExport($id), 'scan_adjustment_report.xlsx');
    }

    public function getProductDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('product_stocks')->select('product_stocks.id as id', 'br_name', 'p_name', 'p_color', 'sz_name', 'ps_barcode')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id'))
            ->editColumn('ps_barcode_show', function($d) {
                return "<input data-id='".$d->id."' id='input_barcode' value='".$d->ps_barcode."'/>";
            })
            ->rawColumns(['ps_barcode_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%")
                        ->orWhere('ps_barcode', 'like', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function updateBarcode(Request $request)
    {
        $id = $request->post('id');
        $barcode = $request->post('barcode');
        $update = DB::table('product_stocks')->where('id', '=', $id)->update([
            'ps_barcode' => $barcode
        ]);
        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function fetchBrand(Request $request)
    {
        if($request->post('query'))
        {
            $query = $request->post('query');
            $data = DB::table('brands')->select("id", "br_name")
            ->whereRaw('br_name LIKE ?', "%$query%")
            ->orderBy('br_name')
            ->limit(10)
            ->get();
            $output = '';
            if (!empty($data)) {
                foreach($data as $row) {
                    $output .= '
                    <a class="btn btn-sm btn-primary col-12" data-id="'.$row->id.'" data-br_name="'.$row->br_name.'" id="add_brand_to_list">'.$row->br_name.'</a>
                    ';
                }
            } else {
                $output .= '<a class="btn btn-sm btn-primary">Tidak ditemukan</a>';
            }
            echo $output;
        }
    }

    public function fetchSubCategory(Request $request)
    {
        if($request->post('query'))
        {
            $query = $request->post('query');
            $data = DB::table('product_sub_categories')->select("id", "psc_name")
            ->whereRaw('psc_name LIKE ?', "%$query%")
            ->orderBy('psc_name')
            ->limit(10)
            ->get();
            $output = '';
            if (!empty($data)) {
                foreach($data as $row) {
                    $output .= '
                    <a class="btn btn-sm btn-primary col-12" data-id="'.$row->id.'" data-psc_name="'.$row->psc_name.'" id="add_sub_category_to_list">'.$row->psc_name.'</a>
                    ';
                }
            } else {
                $output .= '<a class="btn btn-sm btn-primary">Tidak ditemukan</a>';
            }
            echo $output;
        }
    }

    public function fetchArticle(Request $request)
    {
        if($request->post('query'))
        {
            $query = $request->post('query');
            $data = DB::table('product_stocks')->selectRaw('ts_product_stocks.id as id, CONCAT("[",br_name,"] ", p_name," ",p_color," ",sz_name) as p_name')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('p_delete', '!=', '1')
            ->whereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$query%")
            ->orderBy('p_name')
            ->limit(7)
            ->get();
            $output = '';
            if (!empty($data)) {
                foreach($data as $row) {
                    $output .= '
                    <a class="btn btn-sm btn-inventory col-12" data-id="'.$row->id.'" id="add_manual_article">'.$row->p_name.'</a>
                    ';
                }
            } else {
                $output .= '<a class="btn btn-sm btn-inventory">Tidak ditemukan</a>';
            }
            echo $output;
        }
    }

    public function fetchArticleBarcode(Request $request)
    {
        if($request->post('query'))
        {
            $query = $request->post('query');
            $data = DB::table('product_stocks')->selectRaw('ts_product_stocks.id as id, CONCAT("[",br_name,"] ", p_name," ",p_color," ",sz_name) as p_name')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('p_delete', '!=', '1')
            ->whereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$query%")
            ->orderBy('p_name')
            ->limit(7)
            ->get();
            $output = '';
            if (!empty($data)) {
                foreach($data as $row) {
                    $output .= '
                    <a class="btn btn-sm btn-inventory col-12" data-id="'.$row->id.'" id="add_article_barcode">'.$row->p_name.'</a>
                    ';
                }
            } else {
                $output .= '<a class="btn btn-sm btn-inventory">Tidak ditemukan</a>';
            }
            echo $output;
        }
    }

    public function minPlus(Request $request)
    {
        $type = $request->post('type');
        $qty = $request->post('qty');
        $sad_id = $request->post('sad_id');
        $total = 0;
        if ($type == '-') {
            $total = ((int)$qty-1);
        } else {
            $total = ((int)$qty+1);
        }
        if ($total <= 0) {
            $update = DB::table('scan_adjustment_details')->where('id', '=', $sad_id)->delete();
        } else {
            $update = DB::table('scan_adjustment_details')->where('id', '=', $sad_id)->update([
                'qty_so' => $total
            ]);
        }
        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function manual(Request $request)
    {
        $sa_code = $request->post('sa_code');
        $pl_id = $request->post('pl_id');
        $sa_id = DB::table('scan_adjustments')
            ->where('sa_code', '=', $sa_code)->first()->id;
        $pst_id = $request->post('pst_id');
        $check = DB::table('product_location_setups')->select('id', 'pls_qty')->where([
            'pl_id' => $pl_id,
            'pst_id' => $pst_id
        ])->first();
        if (!empty($check)) {
            $qty_so = 1;
            if ($qty_so > $check->pls_qty) {
                $diff = ($qty_so - $check->pls_qty);
                $type = '+';
            } else if ($qty_so < $check->pls_qty) {
                $diff = ($check->pls_qty - $qty_so);
                $type = '-';
            } else {
                $diff = 0;
                $type = '=';
            }
            $insert = [
                'sa_id' => $sa_id,
                'u_id' => Auth::user()->id,
                'pls_id' => $check->id,
                'qty_so' => $qty_so,
                'qty' => $check->pls_qty,
                'mad_type' => $type,
                'mad_diff' => $diff,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $manual = DB::table('scan_adjustment_details')->insert($insert);
        } else {
            $insert = [
                'pls_qty' => '0',
                'pl_id' => $pl_id,
                'pst_id' => $pst_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $pls_id = DB::table('product_location_setups')->insertGetId($insert);

            $do = [
                'sa_id' => $sa_id,
                'u_id' => Auth::user()->id,
                'pls_id' => $pls_id,
                'qty_so' => '1',
                'qty' => '0',
                'mad_type' => '+',
                'mad_diff' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $manual = DB::table('scan_adjustment_details')->insert($do);
        }
        if (!empty($manual)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function getCustomDatatables(Request $request)
    {
        if(request()->ajax()) {
            $type = $request->get('type');
            $id = $request->get('id');
            if ($type == 'brand') {
                return datatables()->of(DB::table('scan_adjustment_brands')->select('scan_adjustment_brands.id as id', 'br_name')
                ->leftJoin('brands', 'brands.id', '=', 'scan_adjustment_brands.br_id')
                ->where('scan_adjustment_brands.sa_id', '=', $id)
                ->groupBy('scan_adjustment_brands.id'))
                ->editColumn('action', function($d) {
                    $btn = "<a class='btn btn-sm btn-danger' data-id='".$d->id."' id='delete_br_custom'><i class='fa fa-trash'></i></a>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
            } else if ($type == 'psc') {
                return datatables()->of(DB::table('scan_adjustment_sub_categories')->select('scan_adjustment_sub_categories.id as id', 'psc_name')
                ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'scan_adjustment_sub_categories.psc_id')
                ->where('scan_adjustment_sub_categories.sa_id', '=', $id)
                ->groupBy('scan_adjustment_sub_categories.id'))
                ->editColumn('action', function($d) {
                    $btn = "<a class='btn btn-sm btn-danger' data-id='".$d->id."' id='delete_psc_custom'><i class='fa fa-trash'></i></a>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
            } else {
                return datatables()->of(DB::table('scan_adjustment_details')->selectRaw("ts_brands.id as id, br_name, sum(ts_scan_adjustment_details.qty) as qty_before, sum(ts_scan_adjustment_details.qty_so) as qty_after")
                ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'scan_adjustment_details.pls_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->where('scan_adjustment_details.sa_id', '=', $id)
                ->groupBy('brands.id'))
                ->editColumn('qty_before', function($d) {
                    return number_format($d->qty_before);
                })
                ->editColumn('qty_after', function($d) {
                    return number_format($d->qty_after);
                })
                ->editColumn('value_before', function($d) use ($id) {
                    $total = 0;
                    $vb = DB::table('scan_adjustment_details')
                    ->selectRaw("qty, avg(ts_purchase_order_article_details.poad_purchase_price) as purchase_2, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase_1, ps_purchase_price, p_purchase_price")
                    ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'scan_adjustment_details.pls_id')
                    ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
                    ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                    ->where('scan_adjustment_details.sa_id', '=', $id)
                    ->where('products.br_id', '=', $d->id)
                    ->groupBy('scan_adjustment_details.id')
                    ->get();
                    if (!empty($vb->first())) {
                        foreach ($vb as $row) {
                            $purchase = null;
                            if (!empty($row->purchase_1)) {
                                $purchase = $row->purchase_1;
                            } else if (!empty($row->purchase_2)) {
                                $purchase = $row->purchase_2;
                            } else if (!empty($row->ps_purchase_price)) {
                                $purchase = $row->ps_purchase_price;
                            } else {
                                $purchase = $row->p_purchase_price;
                            }
                            $total += ($row->qty * $purchase);
                        }
                    }
                    return number_format($total);
                })
                ->editColumn('value_after', function($d) use ($id) {
                    $total = 0;
                    $vb = DB::table('scan_adjustment_details')
                    ->selectRaw("qty_so, avg(ts_purchase_order_article_details.poad_purchase_price) as purchase_2, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase_1, ps_purchase_price, p_purchase_price")
                    ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'scan_adjustment_details.pls_id')
                    ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
                    ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                    ->where('scan_adjustment_details.sa_id', '=', $id)
                    ->where('products.br_id', '=', $d->id)
                    ->groupBy('scan_adjustment_details.id')
                    ->get();
                    if (!empty($vb->first())) {
                        foreach ($vb as $row) {
                            $purchase = null;
                            if (!empty($row->purchase_1)) {
                                $purchase = $row->purchase_1;
                            } else if (!empty($row->purchase_2)) {
                                $purchase = $row->purchase_2;
                            } else if (!empty($row->ps_purchase_price)) {
                                $purchase = $row->ps_purchase_price;
                            } else {
                                $purchase = $row->p_purchase_price;
                            }
                            $total += ($row->qty_so * $purchase);
                        }
                    }
                    return number_format($total);
                })
                ->addIndexColumn()
                ->make(true);
            }
        }
    }

    public function fetchCustom(Request $request)
    {
        $type = $request->post('type');
        if ($type == 'brand') {
            if($request->post('query'))
            {
                $query = $request->post('query');
                $data = DB::table('brands')->select("id", "br_name")
                ->whereRaw('br_name LIKE ?', "%$query%")
                ->orderBy('br_name')
                ->limit(10)
                ->get();
                $output = '';
                if (!empty($data)) {
                    foreach($data as $row) {
                        $output .= '
                        <a class="btn btn-sm btn-primary col-12" data-id="'.$row->id.'" id="add_custom_brand">'.$row->br_name.'</a>
                        ';
                    }
                } else {
                    $output .= '<a class="btn btn-sm btn-primary">Tidak ditemukan</a>';
                }
                echo $output;
            }
        } else {
            if($request->post('query'))
            {
                $query = $request->post('query');
                $data = DB::table('product_sub_categories')->select("id", "psc_name")
                ->whereRaw('psc_name LIKE ?', "%$query%")
                ->orderBy('psc_name')
                ->limit(10)
                ->get();
                $output = '';
                if (!empty($data)) {
                    foreach($data as $row) {
                        $output .= '
                        <a class="btn btn-sm btn-primary col-12" data-id="'.$row->id.'" id="add_custom_psc">'.$row->psc_name.'</a>
                        ';
                    }
                } else {
                    $output .= '<a class="btn btn-sm btn-primary">Tidak ditemukan</a>';
                }
                echo $output;
            }
        }
    }

    public function addCustom(Request $request)
    {
        $id = $request->post('id');
        $type = $request->post('type');
        $sa_id = $request->post('sa_id');
        if ($type == 'brand') {
            $save = DB::table('scan_adjustment_brands')->insert([
                'sa_id' => $sa_id,
                'br_id' => $id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $save = DB::table('scan_adjustment_sub_categories')->insert([
                'sa_id' => $sa_id,
                'psc_id' => $id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        if (!empty($save)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteCustom(Request $request)
    {
        $id = $request->post('id');
        $type = $request->post('type');
        if ($type == 'brand') {
            $delete = DB::table('scan_adjustment_brands')->where('id', '=', $id)->delete();
        } else {
            $delete = DB::table('scan_adjustment_sub_categories')->where('id', '=', $id)->delete();
        }
        if (!empty($delete)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function exportBIN(Request $request)
    {
        $id = $request->get('id');
        return Excel::download(new ScanBINExport($id), 'custom_bin.xlsx');
    }
}
