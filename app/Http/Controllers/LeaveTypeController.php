<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveTypeController extends Controller
{
    protected function validateAccess($slug = null)
    {
        $segment = $slug ?? request()->segment(1);
        $slugToCheck = str_replace('_v2', '', $segment);

        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => $slugToCheck
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

    public function index(Request $request)
    {
        $this->validateAccess();
        
        $title = 'Leave Types';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        
        $leaveType = new LeaveType();
        $leaveTypes = $leaveType->getActiveLeaveTypes();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.leave_type.index', compact('leaveTypes', 'data'));
    }

    public function create()
    {
//        $this->validateAccess();
        
        $title = 'Create Leave Type';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $data = [
            'title' => $title,
            'subtitle' => 'Create Leave Type',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.leave_type.create', compact('data'));
    }

    public function store(Request $request)
    {
//        $this->validateAccess();

        $request->validate([
            'lt_code' => 'required|unique:leave_types,lt_code',
            'lt_name' => 'required|string|max:255',
            'lt_description' => 'nullable|string',
            'lt_default_days' => 'nullable|integer|min:0',
            'lt_default_hours' => 'nullable|integer|min:0',
            'lt_unit' => 'required|in:days,hours',
            'lt_requires_approval' => 'boolean',
            'lt_is_active' => 'boolean'
        ]);

        try {
            $leaveType = new LeaveType();
            
            // Filter out unwanted fields
            $data = $request->only([
                'lt_code', 'lt_name', 'lt_description', 'lt_default_days',
                'lt_default_hours', 'lt_unit', 'lt_requires_approval', 'lt_is_active'
            ]);
            
            // Set default values
            $data['lt_requires_approval'] = $request->input('lt_requires_approval', 0) == 1;
            $data['lt_is_active'] = $request->input('lt_is_active', 0) == 1;

            \Log::info('Creating leave type', $data);

            $result = $leaveType->storeData('add', null, $data);

            if ($result) {
                return redirect()->route('leave-types.index')->with('success', 'Leave type created successfully');
            } else {
                \Log::error('Failed to create leave type');
                return back()->with('error', 'Failed to create leave type')->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('Error creating leave type: ' . $e->getMessage());
            return back()->with('error', 'Error creating leave type: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
//        $this->validateAccess();
        
        $title = 'Leave Type Detail';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $leaveType = LeaveType::findOrFail($id);

        $data = [
            'title' => $title,
            'subtitle' => 'Leave Type Detail',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.leave_type.show', compact('leaveType', 'data'));
    }

    public function edit($id)
    {
//        $this->validateAccess();
        
        $title = 'Edit Leave Type';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $leaveType = LeaveType::findOrFail($id);

        $data = [
            'title' => $title,
            'subtitle' => 'Edit Leave Type',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.leave_type.edit', compact('leaveType', 'data'));
    }

    public function update(Request $request, $id)
    {
//        $this->validateAccess();

        \Log::info('Leave type update request', [
            'id' => $id,
            'request_data' => $request->all(),
            'method' => $request->method()
        ]);

        $request->validate([
            'lt_code' => 'required|unique:leave_types,lt_code,' . $id,
            'lt_name' => 'required|string|max:255',
            'lt_description' => 'nullable|string',
            'lt_default_days' => 'nullable|integer|min:0',
            'lt_default_hours' => 'nullable|integer|min:0',
            'lt_unit' => 'required|in:days,hours',
            'lt_requires_approval' => 'boolean',
            'lt_is_active' => 'boolean'
        ]);

        try {
            $leaveType = LeaveType::findOrFail($id);
            
            \Log::info('Leave type before update', [
                'id' => $leaveType->id,
                'current_data' => $leaveType->toArray()
            ]);
            
            $leaveType->lt_code = $request->lt_code;
            $leaveType->lt_name = $request->lt_name;
            $leaveType->lt_description = $request->lt_description;
            $leaveType->lt_default_days = $request->lt_default_days;
            $leaveType->lt_default_hours = $request->lt_default_hours;
            $leaveType->lt_unit = $request->lt_unit;
            $leaveType->lt_requires_approval = $request->input('lt_requires_approval', 0) == 1;
            $leaveType->lt_is_active = $request->input('lt_is_active', 0) == 1;
            // Preserve existing color or set default
            $leaveType->lt_color = $leaveType->lt_color ?? '#007bff';
            $leaveType->updated_by = auth()->user()->u_name ?? 'system';
            
            \Log::info('Leave type after update', [
                'id' => $leaveType->id,
                'updated_data' => $leaveType->toArray()
            ]);
            
            $result = $leaveType->save();

            if ($result) {
                \Log::info('Leave type update successful', ['id' => $id]);
                
                if (request()->ajax()) {
                    return response()->json(['success' => true, 'message' => 'Leave type updated successfully']);
                } else {
                    return redirect()->route('leave-types.index')->with('success', 'Leave type updated successfully');
                }
            } else {
                \Log::error('Leave type update failed', ['id' => $id]);
                
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Failed to update leave type'], 500);
                } else {
                    return back()->with('error', 'Failed to update leave type')->withInput();
                }
            }
        } catch (\Exception $e) {
            \Log::error('Leave type update error: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e
            ]);
            
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to update leave type: ' . $e->getMessage()], 500);
            } else {
                return back()->with('error', 'Failed to update leave type: ' . $e->getMessage())->withInput();
            }
        }
    }

    public function destroy($id)
    {
//        $this->validateAccess();

        $leaveType = new LeaveType();
        $result = $leaveType->deleteData($id);

        if (request()->ajax()) {
            if ($result) {
                return response()->json(['success' => true, 'message' => 'Leave type deleted successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete leave type'], 500);
            }
        } else {
            if ($result) {
                return redirect()->route('leave-types.index')->with('success', 'Leave type deleted successfully');
            } else {
                return back()->with('error', 'Failed to delete leave type');
            }
        }
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            $query = DB::table('leave_types')
                ->select([
                    'id',
                    'lt_code',
                    'lt_name',
                    'lt_description',
                    'lt_default_days',
                    'lt_default_hours',
                    'lt_unit',
                    'lt_requires_approval',
                    'lt_is_active',
                    'lt_color'
                ]);
            
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('lt_code', 'like', '%' . $search . '%')
                      ->orWhere('lt_name', 'like', '%' . $search . '%')
                      ->orWhere('lt_description', 'like', '%' . $search . '%');
                });
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('lt_duration', function($row) {
                    if ($row->lt_unit == 'days') {
                        return $row->lt_default_days ? $row->lt_default_days . ' days' : '-';
                    } else {
                        return $row->lt_default_hours ? $row->lt_default_hours . ' hours' : '-';
                    }
                })
                ->addColumn('lt_status', function($row) {
                    $statusClass = $row->lt_is_active == 1 ? 'badge badge-success' : 'badge badge-danger';
                    $statusText = $row->lt_is_active == 1 ? 'Active' : 'Inactive';
                    return '<span class="' . $statusClass . '">' . $statusText . '</span>';
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="dropdown">';
                    $btn .= '    <!--begin::Toggle-->';
                    $btn .= '    <button type="button" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start">';
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

                    // View → cek akses read
                    if (hasAccess(auth()->user()->up_id, 'read')) {
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="'.route('leave-types.show', $row->id).'" class="menu-link px-3">View</a>';
                        $btn .= '        </div>';
                    }

                    // Edit → cek akses update
                    if (hasAccess(auth()->user()->up_id, 'update')) {
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="'.route('leave-types.edit', $row->id).'" class="menu-link px-3">Edit</a>';
                        $btn .= '        </div>';
                    }

                    // Delete → cek akses delete
                    if (hasAccess(auth()->user()->up_id, 'delete')) {
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="javascript:void(0)" onclick="deleteLeaveType('.$row->id.')" class="menu-link px-3 text-danger">Delete</a>';
                        $btn .= '        </div>';
                    }

                    $btn .= '    </div>';
                    $btn .= '    <!--end::Menu-->';
                    $btn .= '</div>';

                    return $btn;
                })
                ->rawColumns(['action', 'lt_status'])
                ->make(true);

        }
    }

    /**
     * Leave Types V2 - Updated version with Tailwind CSS
     */
    public function indexUpdated(Request $request)
    {
        $this->validateAccess('leave-types');
        
        $title = 'Leave Types';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        
        $leaveType = new LeaveType();
        $leaveTypes = $leaveType->getActiveLeaveTypes();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'leave-types')->first()->ma_title ?? 'Leave Types',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => 'leave-types'
        ];

        return view('app.updated_leave_type.index', compact('leaveTypes', 'data'));
    }

    /**
     * Get datatables for simple datatable (V2)
     */
    public function getDatatablesForSimple(Request $request)
    {
        $query = DB::table('leave_types')
            ->select([
                'id',
                'lt_code',
                'lt_name',
                'lt_description',
                'lt_default_days',
                'lt_default_hours',
                'lt_unit',
                'lt_requires_approval',
                'lt_is_active',
                'lt_color'
            ]);
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('lt_code', 'like', '%' . $search . '%')
                  ->orWhere('lt_name', 'like', '%' . $search . '%')
                  ->orWhere('lt_description', 'like', '%' . $search . '%');
            });
        }

        // Get total count before pagination
        $totalRecords = $query->count();

        // Apply pagination
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $offset = ($page - 1) * $perPage;

        $leaveTypes = $query->offset($offset)->limit($perPage)->get();

        // Format data
        $formattedData = $leaveTypes->map(function($row) use ($request) {
            $duration = '-';
            if ($row->lt_unit == 'days') {
                $duration = $row->lt_default_days ? $row->lt_default_days . ' days' : '-';
            } else {
                $duration = $row->lt_default_hours ? $row->lt_default_hours . ' hours' : '-';
            }

            // Build action buttons HTML
            $actionHtml = '<div class="flex items-center justify-center gap-2 flex-wrap">';
            
            // View button
            if (hasAccess(auth()->user()->up_id ?? 0, 'read')) {
                $actionHtml .= '<button onclick="viewLeaveType(' . $row->id . ')" class="px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200">View</button>';
            }
            
            // Edit button
            if (hasAccess(auth()->user()->up_id ?? 0, 'update')) {
                $actionHtml .= '<button onclick="editLeaveType(' . $row->id . ')" class="px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded hover:bg-yellow-200">Edit</button>';
            }
            
            // Toggle status button
            $toggleText = $row->lt_is_active == 1 ? 'Deactivate' : 'Activate';
            $toggleClass = $row->lt_is_active == 1 ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200';
            $actionHtml .= '<button onclick="toggleLeaveTypeStatus(' . $row->id . ')" class="px-2 py-1 text-xs font-medium rounded ' . $toggleClass . '">' . $toggleText . '</button>';
            
            // Delete button
            if (hasAccess(auth()->user()->up_id ?? 0, 'delete')) {
                $actionHtml .= '<button onclick="deleteLeaveType(' . $row->id . ')" class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200">Delete</button>';
            }
            
            $actionHtml .= '</div>';

            return [
                'id' => $row->id,
                'lt_code' => $row->lt_code,
                'lt_name' => $row->lt_name,
                'lt_description' => $row->lt_description ?? '-',
                'lt_duration' => $duration,
                'lt_status' => $row->lt_is_active == 1 ? 'Active' : 'Inactive',
                'lt_is_active' => $row->lt_is_active,
                'lt_requires_approval' => $row->lt_requires_approval,
                'lt_unit' => $row->lt_unit,
                'lt_color' => $row->lt_color,
                'action' => $actionHtml
            ];
        });

        return response()->json([
            'data' => $formattedData,
            'total' => $totalRecords,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($totalRecords / $perPage)
        ]);
    }

    /**
     * Show single leave type for V2 (JSON API)
     */
    public function showForSimple($id)
    {
        $leaveType = DB::table('leave_types')
            ->where('id', $id)
            ->first();

        if (!$leaveType) {
            return response()->json(['success' => false, 'message' => 'Leave type not found'], 404);
        }

        $duration = '-';
        if ($leaveType->lt_unit == 'days') {
            $duration = $leaveType->lt_default_days ? $leaveType->lt_default_days . ' days' : '-';
        } else {
            $duration = $leaveType->lt_default_hours ? $leaveType->lt_default_hours . ' hours' : '-';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $leaveType->id,
                'lt_code' => $leaveType->lt_code,
                'lt_name' => $leaveType->lt_name,
                'lt_description' => $leaveType->lt_description ?? '',
                'lt_default_days' => $leaveType->lt_default_days,
                'lt_default_hours' => $leaveType->lt_default_hours,
                'lt_unit' => $leaveType->lt_unit,
                'lt_requires_approval' => $leaveType->lt_requires_approval,
                'lt_is_active' => $leaveType->lt_is_active,
                'lt_color' => $leaveType->lt_color,
                'lt_duration' => $duration
            ]
        ]);
    }

    /**
     * Show leave type detail page for V2
     */
    public function showUpdated($id)
    {
        $this->validateAccess('leave-types');
        
        $title = 'Leave Type Detail';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $leaveType = LeaveType::findOrFail($id);

        $data = [
            'title' => $title,
            'subtitle' => 'Leave Type Detail',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => 'leave-types'
        ];

        return view('app.updated_leave_type.show', compact('leaveType', 'data'));
    }

    /**
     * Store leave type for V2
     */
    public function storeForSimple(Request $request)
    {
        $request->validate([
            'lt_code' => 'required|unique:leave_types,lt_code',
            'lt_name' => 'required|string|max:255',
            'lt_description' => 'nullable|string',
            'lt_default_days' => 'nullable|integer|min:0',
            'lt_default_hours' => 'nullable|integer|min:0',
            'lt_unit' => 'required|in:days,hours',
            'lt_requires_approval' => 'boolean',
            'lt_is_active' => 'boolean'
        ]);

        try {
            $leaveType = new LeaveType();
            
            $data = $request->only([
                'lt_code', 'lt_name', 'lt_description', 'lt_default_days',
                'lt_default_hours', 'lt_unit', 'lt_requires_approval', 'lt_is_active'
            ]);
            
            $data['lt_requires_approval'] = $request->input('lt_requires_approval', 0) == 1;
            $data['lt_is_active'] = $request->input('lt_is_active', 0) == 1;

            $result = $leaveType->storeData('add', null, $data);

            if ($result) {
                return response()->json(['success' => true, 'message' => 'Leave type created successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to create leave type'], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error creating leave type: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating leave type: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update leave type for V2
     */
    public function updateForSimple(Request $request, $id)
    {
        $request->validate([
            'lt_code' => 'required|unique:leave_types,lt_code,' . $id,
            'lt_name' => 'required|string|max:255',
            'lt_description' => 'nullable|string',
            'lt_default_days' => 'nullable|integer|min:0',
            'lt_default_hours' => 'nullable|integer|min:0',
            'lt_unit' => 'required|in:days,hours',
            'lt_requires_approval' => 'boolean',
            'lt_is_active' => 'boolean'
        ]);

        try {
            $leaveType = LeaveType::findOrFail($id);
            
            $leaveType->lt_code = $request->lt_code;
            $leaveType->lt_name = $request->lt_name;
            $leaveType->lt_description = $request->lt_description;
            $leaveType->lt_default_days = $request->lt_default_days;
            $leaveType->lt_default_hours = $request->lt_default_hours;
            $leaveType->lt_unit = $request->lt_unit;
            $leaveType->lt_requires_approval = $request->input('lt_requires_approval', 0) == 1;
            $leaveType->lt_is_active = $request->input('lt_is_active', 0) == 1;
            $leaveType->lt_color = $leaveType->lt_color ?? '#007bff';
            $leaveType->updated_by = auth()->user()->u_name ?? 'system';
            
            $result = $leaveType->save();

            if ($result) {
                return response()->json(['success' => true, 'message' => 'Leave type updated successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update leave type'], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error updating leave type: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating leave type: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete leave type for V2
     */
    public function destroyForSimple($id)
    {
        try {
            $leaveType = new LeaveType();
            $result = $leaveType->deleteData($id);

            if ($result) {
                return response()->json(['success' => true, 'message' => 'Leave type deleted successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete leave type'], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error deleting leave type: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error deleting leave type: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle status for V2
     */
    public function toggleStatusForSimple($id)
    {
        try {
            $leaveType = LeaveType::findOrFail($id);
            $leaveType->lt_is_active = $leaveType->lt_is_active == 1 ? 0 : 1;
            $leaveType->updated_by = auth()->user()->u_name ?? 'system';
            $result = $leaveType->save();

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully',
                    'lt_is_active' => $leaveType->lt_is_active
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update status'], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error toggling status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()], 500);
        }
    }
}
