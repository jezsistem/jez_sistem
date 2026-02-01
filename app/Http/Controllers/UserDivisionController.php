<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UserDivision;
use App\Models\User;
use App\Models\WebConfig;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Editor\Fields\Select;

class UserDivisionController extends Controller
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
        
        $title = 'User Divisions';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        
        $divisions = UserDivision::where('ud_status', 'active')->orderBy('ud_name')->get();


        $data = [
            'title' => $title,
            'subtitle' => 'User Divisions',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_division.index', compact('divisions', 'data'));
    }

    public function create()
    {
        $this->validateAccess();
        
        $title = 'Create User Division';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $leader = DB::table('users')
            ->select('users.id as user_id', 'users.u_name')
            ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
            ->whereIn('user_positions.up_code', ['SUPERVISOR', 'DIREKTUR'])
            ->get();

        $manager = DB::table('users')
            ->select('users.id as user_id', 'users.u_name')
            ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
            ->whereIn('user_positions.up_code', ['MANAGER', 'DIREKTUR'])
            ->get();



        $data = [
            'title' => $title,
            'subtitle' => 'Create User Division',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'leader' => $leader,
            'manager' => $manager,
            'segment' => request()->segment(1)
        ];

        return view('app.user_division.create', compact('data'));
    }

    public function store(Request $request)
    {
        $this->validateAccess();

        $request->validate([
            'ud_code' => 'required|unique:user_divisions,ud_code',
            'ud_name' => 'required|string|max:255',
            'ud_description' => 'nullable|string',
            'ud_status' => 'required|in:active,inactive',
            'division_type' => 'nullable|string|in:FRONTLINE,BACKOFFICE'
        ]);

        $data = [
            'ud_code' => strtoupper($request->ud_code),
            'ud_name' => $request->ud_name,
            'lead_id' => $request->lead_id,
            'manager_id' => $request->manager_id,
            'ud_description' => $request->ud_description,
            'ud_status' => $request->ud_status,
            'division_type' => $request->division_type
        ];

        $userDivision = new UserDivision();
        $result = $userDivision->storeData('add', null, $data);

        if (request()->ajax()) {
            if ($result) {
                return response()->json(['success' => true, 'message' => 'User division created successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to create user division'], 500);
            }
        } else {
            if ($result) {
                return redirect()->route('user-divisions.index')->with('success', 'User division created successfully');
            } else {
                return back()->with('error', 'Failed to create user division')->withInput();
            }
        }
    }

    public function show($id)
    {
        $this->validateAccess();
        
        $division = UserDivision::find($id);

        if (!$division) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Division not found'
                ], 404);
            }
            return redirect()->route('user-divisions.index')->with('error', 'Division not found');
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'division' => $division
            ]);
        }
        
        $title = 'User Division Detail';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $data = [
            'title' => $title,
            'subtitle' => 'User Division Detail',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_division.show', compact('division', 'data'));
    }

    public function edit($id)
    {
        $this->validateAccess();
        
        $title = 'Edit User Division';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $division = UserDivision::find($id);

        if (!$division) {
            return redirect()->route('user-divisions.index')->with('error', 'Division not found');
        }

        $leader = DB::table('users')
            ->select('users.id as user_id', 'users.u_name')
            ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
            ->whereIn('user_positions.up_code', ['SUPERVISOR', 'DIREKTUR'])
            ->get();

        $manager = DB::table('users')
            ->select('users.id as user_id', 'users.u_name')
            ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
            ->whereIn('user_positions.up_code', ['MANAGER', 'DIREKTUR'])
            ->get();


        $data = [
            'title' => $title,
            'subtitle' => 'Edit User Division',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'leader' => $leader,
            'manager' => $manager,
            'segment' => request()->segment(1)
        ];

        return view('app.user_division.edit', compact('division', 'data'));
    }

    public function update(Request $request, $id)
    {
        $this->validateAccess();

        $request->validate([
            'ud_code' => 'required|unique:user_divisions,ud_code,' . $id,
            'ud_name' => 'required|string|max:255',
            'ud_description' => 'nullable|string',
            'ud_status' => 'required|in:active,inactive',
            'division_type' => 'nullable|string|in:FRONTLINE,BACKOFFICE'
        ]);

        $data = [
            'ud_code' => strtoupper($request->ud_code),
            'ud_name' => $request->ud_name,
            'ud_description' => $request->ud_description,
            'lead_id' => $request->lead_id,
            'manager_id' => $request->manager_id,
            'ud_status' => $request->ud_status,
            'division_type' => $request->division_type
        ];

        $userDivision = new UserDivision();
        $result = $userDivision->storeData('edit', $id, $data);

        if (request()->ajax()) {
            if ($result) {
                return response()->json(['success' => true, 'message' => 'User division updated successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update user division'], 500);
            }
        } else {
            if ($result) {
                return redirect()->route('user-divisions.index')->with('success', 'User division updated successfully');
            } else {
                return back()->with('error', 'Failed to update user division')->withInput();
            }
        }
    }

    public function destroy($id)
    {
        $this->validateAccess();

        $userDivision = new UserDivision();
        $result = $userDivision->deleteData($id);

        if (request()->ajax()) {
            if ($result) {
                return response()->json(['success' => true, 'message' => 'User division deleted successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete user division'], 500);
            }
        } else {
            if ($result) {
                return redirect()->route('user-divisions.index')->with('success', 'User division deleted successfully');
            } else {
                return back()->with('error', 'Failed to delete user division');
            }
        }
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            $query = DB::table('user_divisions')
                ->select([
                    'user_divisions.id',
                    'ud_name',
                    'ud_code',
                    'ud_description',
                    'leader_users.u_name as leader_name',
                    'manager_users.u_name as manager_name',
                    'ud_status',
                    'division_type'
                ])
                ->leftJoin('users as leader_users', 'leader_users.id', '=', 'user_divisions.lead_id')
                ->leftJoin('users as manager_users', 'manager_users.id', '=', 'user_divisions.manager_id')
                ->where('ud_status', '!=', 'inactive');
            
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('ud_name', 'like', '%' . $search . '%')
                      ->orWhere('ud_code', 'like', '%' . $search . '%')
                      ->orWhere('ud_description', 'like', '%' . $search . '%');
                });
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('ud_status', function($row) {
                    if ($row->ud_status == 'active') {
                        return '<span class="badge badge-success">Active</span>';
                    } else {
                        return '<span class="badge badge-danger">Inactive</span>';
                    }
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="dropdown">';
                    $btn .= '    <!--begin::Toggle-->';
                    $btn .= '    <button type="button" class="btn btn-sm text-dark btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start">';
                    $btn .= '        Actions';
                    $btn .= '        <span class="svg-icon fs-5 m-0">';
                    $btn .= '            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
                    $btn .= '                <rect opacity="0.5" x="11" y="18" width="12" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"/>';
                    $btn .= '                <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>';
                    $btn .= '            </svg>';
                    $btn .= '        </span>';
                    $btn .= '    </button>';
                    $btn .= '    <!--end::Toggle-->';
                    
                    $btn .= '    <!--begin::Menu-->';
                    $btn .= '    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-auto min-w-150px" data-kt-menu="true">';
                    $btn .= '        <!--begin::Menu item-->';
                    $btn .= '        <div class="menu-item px-3">';
                    $btn .= '            <a href="'.route('user-divisions.show', $row->id).'" class="menu-link px-3">View</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';
                    
                    $btn .= '        <!--begin::Menu item-->';
                    $btn .= '        <div class="menu-item px-3">';
                    $btn .= '            <a href="'.route('user-divisions.edit', $row->id).'" class="menu-link px-3">Edit</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';
                    
                    $btn .= '        <!--begin::Menu item-->';
                    $btn .= '        <div class="menu-item px-3">';
                    $btn .= '            <a href="javascript:void(0)" onclick="deleteUserDivision('.$row->id.')" class="menu-link px-3 text-danger">Delete</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';
                    $btn .= '    </div>';
                    $btn .= '    <!--end::Menu-->';
                    $btn .= '</div>';
                    
                    return $btn;
                })
                ->editColumn('leader_name', function ($row) {
                    return $row->leader_name ?? '-';
                })
                ->editColumn('manager_name', function ($row) {
                    return $row->manager_name ?? '-';
                })
                ->rawColumns(['action', 'ud_status'])
                ->make(true);
        }
    }

    public function indexUpdated()
    {
        $this->validateAccess();
        
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value ?? 'User Divisions';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'user-divisions')->first()->ma_title ?? 'User Divisions',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.updated_user_division.user_division', compact('data'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');

            $query = DB::table('user_divisions')
                ->select([
                    'user_divisions.id',
                    'ud_name',
                    'ud_code',
                    'ud_description',
                    'leader_users.u_name as leader_name',
                    'manager_users.u_name as manager_name',
                    'ud_status'
                ])
                ->leftJoin('users as leader_users', 'leader_users.id', '=', 'user_divisions.lead_id')
                ->leftJoin('users as manager_users', 'manager_users.id', '=', 'user_divisions.manager_id')
                ->where('ud_status', '!=', 'inactive');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('ud_name', 'LIKE', "%$search%")
                      ->orWhere('ud_code', 'LIKE', "%$search%")
                      ->orWhere('ud_description', 'LIKE', "%$search%");
                });
            }

            $total = $query->count();
            $totalPages = ceil($total / $perPage);

            $results = $query->orderBy('user_divisions.id', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            $data = [];
            $no = ($page - 1) * $perPage + 1;
            foreach ($results as $row) {
                $data[] = [
                    'no' => $no++,
                    'id' => $row->id,
                    'ud_name' => $row->ud_name ?? '-',
                    'ud_code' => $row->ud_code ?? '-',
                    'ud_description' => $row->ud_description ?? '-',
                    'leader_name' => $row->leader_name ?? '-',
                    'manager_name' => $row->manager_name ?? '-',
                    'ud_status' => $row->ud_status ?? 'inactive',
                    'ud_status_display' => $row->ud_status == 'active' 
                        ? '<span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">Active</span>'
                        : '<span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">Inactive</span>',
                    'action' => '<div class="flex gap-2 justify-center">
                        <button type="button" class="btn-detail px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-red-600" data-id="' . $row->id . '">Detail</button>
                        <button type="button" class="delete-user-division-btn px-3 py-1 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-700" data-id="' . $row->id . '">Hapus</button>
                    </div>'
                ];
            }

            return response()->json([
                'data' => $data,
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => (int) $page,
                'per_page' => (int) $perPage
            ]);
        } catch (\Exception $e) {
            \Log::error('UserDivisionController getDatatablesForSimple error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage(),
                'data' => [],
                'total' => 0,
                'total_pages' => 0,
                'current_page' => 1,
                'per_page' => 25
            ], 500);
        }
    }

    public function getLeaderOptions()
    {
        $leaders = DB::table('users')
            ->select('users.id', 'users.u_name')
            ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
            ->whereIn('user_positions.up_code', ['SUPERVISOR', 'DIREKTUR'])
            ->where('users.u_delete', '!=', '1')
            ->get();
        
        return response()->json($leaders);
    }

    public function getManagerOptions()
    {
        $managers = DB::table('users')
            ->select('users.id', 'users.u_name')
            ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
            ->whereIn('user_positions.up_code', ['MANAGER', 'DIREKTUR'])
            ->where('users.u_delete', '!=', '1')
            ->get();
        
        return response()->json($managers);
    }
}
