<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherTransaction;

class VoucherController extends Controller
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
        return view('app.voucher.voucher', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Voucher::select('id', 'vc_code',  'vc_discount', 'vc_min_order', 'vc_type', 'vc_reuse', 'vc_status', 'vc_cashback', 'vc_platform', 'vc_due_date', 'vc_pst_id'))
            ->editColumn('vc_type_show', function($data) {
                if ($data->vc_type == 'amount') {
                  $type = 'Nominal';
                } else if ($data->vc_type == 'percent') {
                  $type = 'Persen';
                } else {
                    $type = 'Gift';
                }
                return $type;
            })
            ->editColumn('vc_reuse_show', function($data) {
                if ($data->vc_reuse == '0') {
                  $reuse = 'Tidak';
                } else if ($data->vc_reuse == '1') {
                  $reuse = 'Ya';
                } else {
                    $reuse = '1 Bulan Sekali';
                }
                return $reuse;
            })
            ->editColumn('vc_status_show', function($data) {
                if ($data->vc_status == '0') {
                  $status = 'Nonaktif';
                } else {
                  $status = 'Aktif';
                }
                return $status;
            })
            ->editColumn('vc_cashback_show', function($data) {
                if ($data->vc_cashback == '0') {
                  $cashback = 'Tidak';
                } else {
                  $cashback = 'Ya';
                }
                return $cashback;
            })
            ->editColumn('vc_due_date_show', function($data) {
                return date('d-m-Y', strtotime($data->vc_due_date));
            })
            ->editColumn('item', function($data) {
                return '-';
            })
            ->rawColumns(['vc_type_show', 'vc_reuse_show', 'vc_status_show', 'vc_cashback_show', 'vc_due_date_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('vc_code', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    private function checkExists($code)
    {
        $check = Voucher::where('vc_code', '=', $code)->exists();
        if ($check) {
            return $this->generateRandomVoucher();
        } else {
            return $code;
        }
    }

    private function generateRandomCode()
    {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $code = '';
        for ($i = 0; $i < 10; $i++) {
            $code .= $chars[mt_rand(0, strlen($chars)-1)];
        }
        return $this->checkExists($code);
    }

    public function storeData(Request $request)
    {
        $voucher = new Voucher;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        if (!empty($request->input('vc_qty'))) {
            $data = array();
            for ($i = 1; $i <= $request->input('vc_qty'); $i++) {
                $data[] = [
                    'vc_code' => strtoupper($this->generateRandomCode()),
                    'vc_discount' => $request->input('vc_discount'),
                    'vc_min_order' => $request->input('vc_min_order'),
                    'vc_type' => $request->input('vc_type'),
                    'vc_reuse' => $request->input('vc_reuse'),
                    'vc_status' => $request->input('vc_status'),
                    'vc_cashback' => $request->input('vc_cashback'),
                    'vc_platform' => $request->input('vc_platform'),
                    'vc_due_date' => $request->input('vc_due_date'),
                ];
            }
            $save = Voucher::insert($data);
        } else {
            $data = [
                'vc_code' => strtoupper($request->input('vc_code')),
                'vc_discount' => $request->input('vc_discount'),
                'vc_min_order' => $request->input('vc_min_order'),
                'vc_type' => $request->input('vc_type'),
                'vc_reuse' => $request->input('vc_reuse'),
                'vc_status' => $request->input('vc_status'),
                'vc_cashback' => $request->input('vc_cashback'),
                'vc_platform' => $request->input('vc_platform'),
                'vc_due_date' => $request->input('vc_due_date'),
            ];

            $save = $voucher->storeData($mode, $id, $data);
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
        $save = Voucher::where(['id' => $id])->delete();
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsVoucher(Request $request)
    {
        $check = Voucher::where(['vc_code' => strtoupper($request->vc_code)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
