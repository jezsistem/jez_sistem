<?php

namespace App\Http\Controllers;

use App\Models\OvertimeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerV2Controller extends Controller
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
            ->where('u_id', auth()->user()->id)->get();
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

        $title = 'External Assignment Requests';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

//        $summary = [
//            'total'     => OvertimeRequest::count(),
//            'pending'   => OvertimeRequest::where('status', 'Pending')->count(),
//            'approved'  => OvertimeRequest::where('status', 'Approved')->count(),
//            'hr_check'  => OvertimeRequest::where('status', 'HR Check')->count(),
//            'done'      => OvertimeRequest::where('status', 'Done')->count(),
//        ];


        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'divisions' => DB::table('user_divisions')->orderBy('ud_name')->get(),
            'segment' => request()->segment(1),
        ];

        return view('app.customer_v2.index', compact('data'));
    }

    public function getData(Request $request)
    {
        $data = DB::table('customers')
            ->select(
                'id',
                'cust_name',
                'cust_phone',
                'cust_tier',
                'cust_coin',
                'cust_member_card',
                'cust_coin_active',
                'created_at'
            )
            ->where('cust_delete', '0')
            ->orderByDesc('cust_coin');

        // =============================
        // 🔍 FILTER
        // =============================
        if ($request->tier) {
            $data->where('cust_tier', $request->tier);
        }

        if ($request->coin_active !== null && $request->coin_active !== '') {
            $data->where('cust_coin_active', $request->coin_active);
        }

        if ($request->phone) {
            $data->where('cust_phone', 'like', "%{$request->phone}%");
        }

        return DataTables::of($data)
            ->addIndexColumn()

            ->addColumn('cust_tier', function ($row) {
                switch ($row->cust_tier) {
                    case 'elite':
                        return '<span class="badge bg-warning text-dark">Elite</span>';
                    case 'pro':
                        return '<span class="badge bg-primary">Pro</span>';
                    default:
                        return '<span class="badge bg-secondary">Academy</span>';
                }
            })

            ->addColumn('coin_status', function ($row) {
                if ($row->cust_coin_active == '1') {
                    return '<span class="badge bg-success text-white">Activated</span>';
                }
                return '<span class="badge bg-danger text-white">Not Active</span>';
            })

            ->addColumn('trx_value', function ($row) {
               $first_trx = DB::table('crm_point_logs')->where('cust_id', $row->id)->orderBy('id', 'ASC')->first();

//               dd($first_trx)   ;
               if ($first_trx) {
                   $trx_value = DB::table('pos_transactions')
                       ->where('cust_id', $row->id)
                       ->where('created_at', '>=', $first_trx->created_at)
                       ->sum('pos_real_price');

//                   dd(trim($trx_value));
               } else {
                   $trx_value = 0;
               }
               return 'Rp ' . number_format($trx_value, 0, ',', '.');
            })


            ->addColumn('action', function ($row) {

                $btn  = '<div class="dropdown">';
                $btn .= '<button class="btn btn-sm btn-light btn-active-light-primary"
                        data-kt-menu-trigger="click"
                        data-kt-menu-placement="bottom-start">
                        Actions
                     </button>';

                $btn .= '<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded
                        menu-gray-800 fw-semibold min-w-150px"
                        data-kt-menu="true">';

                $btn .= '<div class="menu-item px-3">
                        <a href="'.route('customers-v2.show',$row->id).'" class="menu-link px-3">
                            View
                        </a>
                     </div>';

                if ($row->cust_coin_active == '0') {
                    $btn .= '<div class="menu-item px-3">
                            <a href="javascript:void(0)"
                               onclick="activateCustomer('.$row->id.')"
                               class="menu-link px-3 text-success">
                                Activate Coin
                            </a>
                         </div>';
                }

                $btn .= '</div></div>';

                return $btn;
            })

            ->rawColumns(['cust_tier', 'coin_status', 'trx_value','action'])
            ->make(true);
    }
}
