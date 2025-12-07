<?php

namespace App\Http\Controllers;

use App\Exports\UserInformationExport;
use App\Models\User;
use App\Models\UserPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class StaffInformationController extends Controller
{
    protected function validateAccess()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user_position = auth()->user()->up_id;

        $user_group_is_admin = DB::table('user_groups')->join('groups', 'groups.id', '=', 'user_groups.group_id')
            ->where('user_groups.user_id', auth()->user()->id)
            ->where('g_name', 'administrator')
            ->exists();

        $is_human_resource = DB::table('users')->join('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->where('users.id', auth()->user()->id)
            ->where('user_divisions.ud_code', 'HUMANRESOU')
            ->exists();

        if (!$user_group_is_admin && !$is_human_resource) {
            $validate = DB::table('position_access')
                ->leftJoin('user_positions', 'user_positions.id', '=', 'position_access.position_id')->where([
                    'position_access.position_id' => $user_position,
                    //                    'position_access.route' => request()->path()
                ])->exists();

            if (!$validate) {
                dd("Anda tidak memiliki akses ke menu ini, level Anda tidak dizinkan, hubungi Administrator");
            }
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
        $title = 'Staff Information';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        $sidebar = $this->sidebar();
        $subtitle = 'Staff Information';

        $userPosition = new UserPosition();
        $positions = $userPosition->getActivePositions();
        $divisions = DB::table('user_divisions')
            ->where('ud_status', 'active')
            ->orderBy('ud_name')
            ->get();

        $data = array(
            'title' => $title,
            'user' => $user_data,
            'sidebar' => $sidebar,
            'subtitle' => $subtitle,
        );
        return view('app.staff_information.index', compact('data', 'positions', 'divisions'));
    }

    public function getDatatables(Request $request)
    {

        if ($request->ajax()) {
            $staff = User::select(
                'users.id',
                'users.u_name',
                'users.u_ktp',
                'users.u_ktp_image',
                'users.u_npwp',
                'users.u_npwp_image',
                'users.u_birthday',
                'users.u_address',
                'users.u_photo',
                'users.u_bpjs_kes_number',
                'users.u_bpjs_kes_image',
                'users.u_bpjs_tk_number',
                'users.u_bpjs_tk_image',
                'users.u_bank_name',
                'users.u_bank_account_number',
                'users.u_bank_account_holder',
                'user_positions.up_name',
                'user_divisions.ud_name'
            )
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->where('users.u_delete', '0');

            return datatables()->of($staff)
                ->addIndexColumn()
                ->editColumn('u_birthday', function ($row) {
                    return date('d M Y', strtotime($row->u_birthday));
                })
                ->editColumn('u_name', function ($row) {
                    return $row->u_photo ? '<a href="' . Storage::disk('s3')->url($row->u_photo) . '" target="_blank" class="btn btn-sm btn-primary">' . $row->u_name . '</a>' : '<span>' . $row->u_name . '</span>';
                })
                ->editColumn('u_ktp', function ($row) {
                    return $row->u_ktp_image ? '<a href="' . Storage::disk('s3')->url($row->u_ktp_image) . '" target="_blank" class="btn btn-sm btn-info">' . ($row->u_ktp ?? 'View KTP') . '</a>' : ($row->u_ktp ?? '-');
                })
                ->editColumn('u_npwp', function ($row) {
                    return $row->u_npwp_image ? '<a href="' . Storage::disk('s3')->url($row->u_npwp_image) . '" target="_blank" class="btn btn-sm btn-info">' . ($row->u_npwp ?? 'View NPWP') . '</a>' : ($row->u_npwp ?? '-');
                })
                ->editColumn('u_bpjs_kes_number', function ($row) {
                    return $row->u_bpjs_kes_image ? '<a href="' . Storage::disk('s3')->url($row->u_bpjs_kes_image) . '" target="_blank" class="btn btn-sm btn-info">' . ($row->u_bpjs_kes_number ?? 'View BPJS Kesehatan') . '</a>' : ($row->u_bpjs_kes_number ?? '-');
                })
                ->editColumn('u_bpjs_tk_number', function ($row) {
                    return $row->u_bpjs_tk_image ? '<a href="' . Storage::disk('s3')->url($row->u_bpjs_tk_image) . '" target="_blank" class="btn btn-sm btn-info">' . ($row->u_bpjs_tk_number ?? 'View BPJS Ketenagakerjaan') . '</a>' : ($row->u_bpjs_tk_number ?? '-');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('staff-information.show', $row->id) . '" class="btn btn-sm btn-primary">View Details</a>';
                    return $btn;
                })
                ->rawColumns(['u_name', 'u_ktp', 'u_npwp', 'u_bpjs_kes_number', 'u_bpjs_tk_number', 'action'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('position'))) {
                        $instance->where('users.up_id', $request->get('position'));
                    }
                    if (!empty($request->get('division'))) {
                        $instance->where('users.ud_id', $request->get('division'));
                    }
                    if (!empty($request->get('search'))) {
                        $search = $request->get('search');
                        $instance->where(function ($w) use ($search) {
                            $w->orWhere('users.u_name', 'LIKE', "%$search%")
                                ->orWhere('users.u_ktp', 'LIKE', "%$search%")
                                ->orWhere('users.u_npwp', 'LIKE', "%$search%")
                                ->orWhere('users.u_bank_account_number', 'LIKE', "%$search%");
                        });
                    }
                })
                ->make(true);
        }
    }

    public function show($id) {
        $staff = User::select(
                'users.id',
                'users.u_name',
                'users.u_ktp',
                'users.u_ktp_image',
                'users.u_npwp',
                'users.u_npwp_image',
                'users.u_birthday',
                'users.u_address',
                'users.u_photo',
                'users.u_bpjs_kes_number',
                'users.u_bpjs_kes_image',
                'users.u_bpjs_tk_number',
                'users.u_bpjs_tk_image',
                'users.u_bank_name',
                'users.u_bank_account_number',
                'users.u_bank_account_holder',
                'user_positions.up_name',
                'user_divisions.ud_name'
            )
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->where('users.u_delete', '0')
                ->where('users.id', $id)
                ->first();

        if (!$staff) {
            abort(404);
        }

        $data = array(
            'title' => 'Staff Information Detail',
            'user' => auth()->user(),
            'sidebar' => $this->sidebar(),
            'subtitle' => 'Staff Information Detail',
        );

        return view('app.staff_information.show', compact('staff', 'data'));
    }

    public function export(Request $request) {
        $users = User::select(
                'users.id',
                'users.u_name',
                'users.u_ktp',
                'users.u_ktp_image',
                'users.u_npwp',
                'users.u_npwp_image',
                'users.u_birthday',
                'users.u_address',
                'users.u_photo',
                'users.u_bpjs_kes_number',
                'users.u_bpjs_kes_image',
                'users.u_bpjs_tk_number',
                'users.u_bpjs_tk_image',
                'users.u_bank_name',
                'users.u_bank_account_number',
                'users.u_bank_account_holder',
                'user_positions.up_name',
                'user_divisions.ud_name'
            )
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->where('users.u_delete', '0');

        if ($request->has('position') && !empty($request->get('position'))) {
            $users->where('users.up_id', $request->get('position'));
        }

        if ($request->has('division') && !empty($request->get('division'))) {
            $users->where('users.ud_id', $request->get('division'));
        }

        if ($request->has('search') && !empty($request->get('search'))) {
            $search = $request->get('search');
            $users->where(function ($w) use ($search) {
                $w->orWhere('users.u_name', 'LIKE', "%$search%")
                    ->orWhere('users.u_ktp', 'LIKE', "%$search%")
                    ->orWhere('users.u_npwp', 'LIKE', "%$search%")
                    ->orWhere('users.u_bank_account_number', 'LIKE', "%$search%");
            });
        }

        $users = $users->get();

        return Excel::download(new UserInformationExport($users), 'staff_information.xlsx');
    }
}
