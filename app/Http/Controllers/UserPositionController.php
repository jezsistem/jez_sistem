<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPosition;
use App\Models\User;
use App\Models\WebConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserPositionController extends Controller
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
                    'position_access.route' => request()->path()
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
        
        $title = 'User Positions';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        
        $userPosition = new UserPosition();
        $positions = $userPosition->getActivePositions();

        $data = [
            'title' => $title,
            'subtitle' => 'User Positions',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_position.index', compact('positions', 'data'));
    }

    public function create()
    {
        $this->validateAccess();
        
        $title = 'Create User Position';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $data = [
            'title' => $title,
            'subtitle' => 'Create User Position',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_position.create', compact('data'));
    }

    public function store(Request $request)
    {
        $this->validateAccess();

        $request->validate([
            'up_code' => 'required|unique:user_positions,up_code',
            'up_name' => 'required|string|max:255',
            'up_description' => 'nullable|string',
            'up_level' => 'required|integer|min:1|max:10',
            'up_can_approve_leave' => 'boolean',
            'up_is_active' => 'boolean',
            'up_color' => 'required|string|max:7'
        ]);

        try {
            $userPosition = new UserPosition();
            
            // Filter out unwanted fields
            $data = $request->only([
                'up_code', 'up_name', 'up_description', 'up_level',
                'up_can_approve_leave', 'up_is_active', 'up_color'
            ]);
            
            $data['up_can_approve_leave'] = $request->input('up_can_approve_leave', 0) == 1;
            $data['up_is_active'] = $request->input('up_is_active', 0) == 1;

            \Log::info('Creating user position', $data);

            $result = $userPosition->storeData('add', null, $data);

            if ($result) {
                return redirect()->route('user-positions.index')->with('success', 'User position created successfully');
            } else {
                \Log::error('Failed to create user position');
                return back()->with('error', 'Failed to create user position')->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('Error creating user position: ' . $e->getMessage());
            return back()->with('error', 'Error creating user position: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $this->validateAccess();
        
        $position = UserPosition::findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'position' => $position
            ]);
        }
        
        $title = 'User Position Detail';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $data = [
            'title' => $title,
            'subtitle' => 'User Position Detail',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_position.show', compact('position', 'data'));
    }

    public function edit($id)
    {
        $this->validateAccess();
        
        $title = 'Edit User Position';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $position = UserPosition::findOrFail($id);

        $data = [
            'title' => $title,
            'subtitle' => 'Edit User Position',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_position.edit', compact('position', 'data'));
    }

    public function update(Request $request, $id)
    {
        $this->validateAccess();

        $request->validate([
            'up_code' => 'required|unique:user_positions,up_code,' . $id,
            'up_name' => 'required|string|max:255',
            'up_description' => 'nullable|string',
            'up_level' => 'required|integer|min:1|max:10',
            'up_can_approve_leave' => 'nullable|in:on',
            'up_is_active' => 'nullable|in:on', 
            'up_color' => 'nullable|string|max:7'
        ]);

        try {
            $userPosition = new UserPosition();
            
            // Filter out unwanted fields
            $data = $request->only([
                'up_code', 'up_name', 'up_description', 'up_level'
            ]);
            
            // Handle checkboxes properly (convert 'on' to 1, missing to 0)
            $data['up_can_approve_leave'] = $request->input('up_can_approve_leave') === 'on' ? 1 : 0;
            $data['up_is_active'] = $request->input('up_is_active') === 'on' ? 1 : 0;
            
            // Preserve existing color or set default if not exists
            $existingPosition = UserPosition::find($id);
            $data['up_color'] = $existingPosition->up_color ?? '#007bff';

            \Log::info('Updating user position', ['id' => $id, 'data' => $data]);

            $result = $userPosition->storeData('edit', $id, $data);

            if ($result) {
                \Log::info('User position updated successfully', ['id' => $id]);
                
                if (request()->ajax()) {
                    return response()->json(['success' => true, 'message' => 'User position updated successfully']);
                } else {
                    return redirect()->route('user-positions.index')->with('success', 'User position updated successfully');
                }
            } else {
                \Log::error('Failed to update user position', ['id' => $id]);
                
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Failed to update user position'], 500);
                } else {
                    return back()->with('error', 'Failed to update user position')->withInput();
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error updating user position: ' . $e->getMessage(), ['id' => $id]);
            
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error updating user position: ' . $e->getMessage()], 500);
            } else {
                return back()->with('error', 'Error updating user position: ' . $e->getMessage())->withInput();
            }
        }
    }

    public function destroy($id)
    {
        $this->validateAccess();

        $userPosition = new UserPosition();
        $result = $userPosition->deleteData($id);

        if (request()->ajax()) {
            if ($result) {
                return response()->json(['success' => true, 'message' => 'User position deleted successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete user position'], 500);
            }
        } else {
            if ($result) {
                return redirect()->route('user-positions.index')->with('success', 'User position deleted successfully');
            } else {
                return back()->with('error', 'Failed to delete user position');
            }
        }
    }

    public function getDatatables(Request $request)
    {
        try {
        if(request()->ajax()) {
            $query = DB::table('user_positions')
                ->select([
                    'id',
                    'up_code',
                    'up_name',
                    'up_description',
                    'up_level',
                    'up_can_approve_leave',
                    'up_is_active'
                    ]);
            
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('up_code', 'like', '%' . $search . '%')
                      ->orWhere('up_name', 'like', '%' . $search . '%')
                      ->orWhere('up_description', 'like', '%' . $search . '%');
                });
            }

                // Get total count for debugging
                $totalCount = $query->count();
                \Log::info('UserPositions total count: ' . $totalCount);

                $result = datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('up_is_active', function($row) {
                    if ($row->up_is_active == 1) {
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
                        $btn .= '    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-auto min-w-150px" data-kt-menu-placement="top-start" data-kt-menu="true" style="z-index: 99999 !important;">';
                        $btn .= '        <!--begin::Menu item-->';
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="'.route('user-positions.show', $row->id).'" class="menu-link px-3">View</a>';
                        $btn .= '        </div>';
                        $btn .= '        <!--end::Menu item-->';
                        
                        $btn .= '        <!--begin::Menu item-->';
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="'.route('user-positions.edit', $row->id).'" class="menu-link px-3">Edit</a>';
                        $btn .= '        </div>';
                        $btn .= '        <!--end::Menu item-->';
                        
                        $btn .= '        <!--begin::Menu item-->';
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="javascript:void(0)" onclick="deleteUserPosition('.$row->id.')" class="menu-link px-3 text-danger">Delete</a>';
                        $btn .= '        </div>';
                        $btn .= '        <!--end::Menu item-->';
                        $btn .= '    </div>';
                        $btn .= '    <!--end::Menu-->';
                    $btn .= '</div>';
                        
                    return $btn;
                })
                ->rawColumns(['action', 'up_is_active'])
                ->make(true);

                \Log::info('UserPositions datatables response generated successfully');
                return $result;
            }
        } catch (\Exception $e) {
            \Log::error('Error in getDatatables: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return error response
            return response()->json([
                'error' => 'Failed to load data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexUpdated()
    {
        $this->validateAccess();
        
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value ?? 'User Positions';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'user-positions')->first()->ma_title ?? 'User Positions',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.updated_user_position.user_position', compact('data'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');

            $query = DB::table('user_positions')
                ->select([
                    'id',
                    'up_code',
                    'up_name',
                    'up_description',
                    'up_level',
                    'up_can_approve_leave',
                    'up_is_active'
                ]);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('up_code', 'LIKE', "%$search%")
                      ->orWhere('up_name', 'LIKE', "%$search%")
                      ->orWhere('up_description', 'LIKE', "%$search%");
                });
            }

            $total = $query->count();
            $totalPages = ceil($total / $perPage);

            $data = $query->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get()
                ->map(function ($row) {
                    $row->up_is_active_display = $row->up_is_active == 1 
                        ? '<span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">Active</span>'
                        : '<span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">Inactive</span>';
                    $row->action = '<div class="flex gap-2 justify-center">
                        <button type="button" class="btn-detail px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700" data-id="' . $row->id . '">Detail</button>
                        <button type="button" class="delete-user-position-btn px-3 py-1 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-700" data-id="' . $row->id . '">Hapus</button>
                    </div>';
                    return $row;
                })
                ->values()
                ->all();

            $no = ($page - 1) * $perPage + 1;
            foreach ($data as &$row) {
                $row->DT_RowIndex = $no++;
            }

            return response()->json([
                'data' => $data,
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => (int) $page,
                'per_page' => (int) $perPage
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load data: ' . $e->getMessage()], 500);
        }
    }
}
