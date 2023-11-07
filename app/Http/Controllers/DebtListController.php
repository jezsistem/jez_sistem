<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\DebtList;
use App\Models\DebtListPayment;
use App\Models\ProductSupplier;
use App\Models\Store;
use App\Models\Brand;
use App\Models\UserActivity;
use App\Imports\DebtListImport;
use App\Exports\DebtExport;
use Maatwebsite\Excel\Facades\Excel;

class DebtListController extends Controller
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
            'br_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'ps_id' => ProductSupplier::where('ps_delete', '!=', '1')->orderByDesc('id')->pluck('ps_name', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.debt_list.debt_list', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DebtList::select('debt_lists.id as dl_id', 'brands.id as br_id', 'st_id', 'product_suppliers.id as ps_id', 'ps_name', 'st_name', 'br_name', 'dl_invoice', 'dl_invoice_date', 'dl_invoice_due_date', 'dl_value', 'dl_total', 'dl_vat')
            ->leftJoin('product_suppliers', 'product_suppliers.id', '=', 'debt_lists.ps_id')
            ->leftJoin('brands', 'brands.id', '=', 'debt_lists.br_id')
            ->leftJoin('stores', 'stores.id', '=', 'debt_lists.st_id')
            ->where('dl_delete', '!=', '1')
            ->where('st_id', $request->st_id))
            ->editColumn('dl_invoice_date_show', function($data){ 
                return date('d/m/Y', strtotime($data->dl_invoice_date));
            })
            ->editColumn('st_name', function($data){ 
                return '<span style="white-space:nowrap;">'.$data->st_name.'</span>';
            })
            ->editColumn('dl_invoice_due_date_show', function($data){ 
                $date1_remain = $data->dl_invoice_date;
                $date2_remain = $data->dl_invoice_due_date;
                $diff_remain = abs(strtotime($date1_remain) - strtotime($date2_remain));
                if ($date1_remain>$date2_remain) {
                    $diff_remain = -($diff_remain);
                }
                $days_remain = round($diff_remain/86400);
                return date('d/m/Y', strtotime($data->dl_invoice_due_date)).' ['.$days_remain.' Hari]';
            })
            ->editColumn('dl_value_show', function($data){ 
                $dl_value = $data->dl_value;
                return '<span class="btn btn-sm btn-primary">'.number_format($dl_value).'</span>';
            })
            ->editColumn('dl_total_show', function($data){ 
                $dl_total = $data->dl_total;
                return '<span class="btn btn-sm btn-primary">'.number_format($dl_total).'</span>';
            })
            ->editColumn('payment_value', function($data){ 
                $payment = DebtListPayment::select('dlp_value')->where('dlp_delete', '!=', '1')->where('dl_id', $data->dl_id)->sum('dlp_value');
                if ($data->dl_total == $payment) {
                    return '<span class="btn btn-sm btn-primary" data-dl_value="'.$data->dl_total.'" data-payment="'.$payment.'" data-dl_id="'.$data->dl_id.'" id="payment_total_btn">'.number_format($payment).'</span>';
                } else {
                    return '<span class="btn btn-sm btn-success" data-dl_value="'.$data->dl_total.'" data-payment="'.$payment.'" data-dl_id="'.$data->dl_id.'" id="payment_total_btn">'.number_format($payment).'</span>';
                }
            })
            ->rawColumns(['st_name', 'dl_value_show', 'payment_total', 'payment_value', 'dl_total_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('ps_name', 'LIKE', "%$search%")
                        ->orWhere('br_name', 'LIKE', "%$search%")
                        ->orWhere('dl_invoice', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }
    
    public function brandDebtDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Brand::select('brands.id as id', 'br_name')
            ->rightJoin('debt_lists', 'debt_lists.br_id', '=', 'brands.id')
            ->groupBy('id')
            ->where('br_delete', '!=', '1'))
            ->editColumn('brand_debt', function($data){ 
                $brand_debt = DebtList::select('id', 'dl_total')
                ->where('dl_delete', '!=', '1')->where('br_id', $data->id)->sum('dl_total');

                $brand_debt_payment = DebtListPayment::select('id', 'dlp_value')
                ->join('debt_lists', 'debt_lists.id', '=', 'debt_list_payments.dl_id')
                ->where('dlp_delete', '!=', '1')->where('br_id', $data->id)->sum('dlp_value');
                $brand_debt_total = $brand_debt - $brand_debt_payment;
                return number_format($brand_debt_total);
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function paymentDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DebtListPayment::select('id', 'dl_id', 'dlp_date', 'dlp_value')
            ->where('dl_id', $request->dl_id))
            ->editColumn('dlp_date_show', function($data){ 
                return date('d/m/Y', strtotime($data->dlp_date));
            })
            ->editColumn('dlp_value', function($data){ 
                return number_format($data->dlp_value);
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $debt_list = new DebtList;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'ps_id' => $request->input('ps_id'),
            'st_id' => $request->input('st_id_data'),
            'br_id' => $request->input('br_id'),
            'dl_invoice' => $request->input('dl_invoice'),
            'dl_invoice_date' => $request->input('dl_invoice_date'),
            'dl_invoice_due_date' => $request->input('dl_invoice_due_date'),
            'dl_value' => $request->input('dl_value'),
            'dl_vat' => $request->input('dl_vat'),
            'dl_total' => $request->input('dl_total'),
            'dl_delete' => '0',
        ];

        $save = $debt_list->storeData($mode, $id, $data);
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('menambah data hutang '.strtoupper($request->input('dl_invoice')).' '.date('d-m-Y', strtotime($request->input('dl_invoice_date'))).' '.$request->input('dl_total'));
            } else {
                $this->UserActivity('mengubah data hutang '.strtoupper($request->input('dl_invoice')).' '.date('d-m-Y', strtotime($request->input('dl_invoice_date'))).' '.$request->input('dl_total'));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $debt_list = new DebtList;
        $id = $request->input('_id');
        $item_name = DebtList::select('dl_invoice')->where('id', $id)->get()->first()->dl_invoice;
        $save = $debt_list->deleteData($id);
        if ($save) {
            $this->UserActivity('menghapus data hutang dengan invoice '.$item_name);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function storeDataPayment(Request $request)
    {
        $debt_list_payment = new DebtListPayment;
        $mode = $request->input('_mode_payment');
        $id = $request->input('_id_payment');

        $data = [
            'dl_id' => $request->input('dl_id'),
            'dlp_date' => $request->input('dlp_date'),
            'dlp_value' => $request->input('dlp_value'),
            'dlp_delete' => '0',
        ];

        $save = $debt_list_payment->storeData($mode, $id, $data);
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('menambah data pembayaran hutang ['.date('d-m-Y', strtotime($request->input('dlp_date'))).'] '.$request->input('dlp_value'));
            } else {
                $this->UserActivity('mengubah data pembayaran hutang ['.date('d-m-Y', strtotime($request->input('dlp_date'))).'] '.$request->input('dlp_value'));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteDataPayment(Request $request)
    {
        $debt_list_payment = new DebtListPayment;
        $id = $request->input('_id');
        $item_name = DebtListPayment::select('dlp_date', 'dlp_value')->where('id', $id)->get()->first();
        $save = $debt_list_payment->deleteData($id);
        if ($save) {
            $this->UserActivity('menghapus data pembayaran hutang ['.date('d-m-Y', strtotime($item_name->dlp_date)).'] '.$item_name->dlp_value);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function importData(Request $request)
    {
        if (request()->hasFile('p_template')) {
            $import = new DebtListImport;
            Excel::import($import, request()->file('p_template'));
            if ($import->getRowCount() >= 0) {
                $this->UserActivity('melakukan import data hutang');
                $r['status'] = '200';
            } else {
                $this->UserActivity('melakukan import data hutang namun gagal');
                $r['status'] = '419';
            }
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function exportData()
	{
		return Excel::download(new DebtExport, 'daftar_hutang.xlsx');
	}
}
