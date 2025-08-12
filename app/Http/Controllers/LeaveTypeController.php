<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveType;
use Illuminate\Support\Facades\DB;

class LeaveTypeController extends Controller
{
    protected function validateAccess()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
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
        $this->validateAccess();
        
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
        $this->validateAccess();

        $request->validate([
            'lt_code' => 'required|unique:leave_types,lt_code',
            'lt_name' => 'required|string|max:255',
            'lt_description' => 'nullable|string',
            'lt_default_days' => 'nullable|integer|min:0',
            'lt_default_hours' => 'nullable|integer|min:0',
            'lt_unit' => 'required|in:days,hours',
            'lt_requires_approval' => 'boolean',
            'lt_is_active' => 'boolean',
            'lt_color' => 'required|string|max:7'
        ]);

        try {
            $leaveType = new LeaveType();
            
            // Filter out unwanted fields
            $data = $request->only([
                'lt_code', 'lt_name', 'lt_description', 'lt_default_days',
                'lt_default_hours', 'lt_unit', 'lt_requires_approval', 'lt_is_active', 'lt_color'
            ]);
            
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
        $this->validateAccess();
        
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
        $this->validateAccess();
        
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
        $this->validateAccess();

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
            'lt_is_active' => 'boolean',
            'lt_color' => 'required|string|max:7'
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
            $leaveType->lt_color = $request->lt_color;
            $leaveType->updated_by = auth()->user()->u_name ?? 'system';
            
            \Log::info('Leave type after update', [
                'id' => $leaveType->id,
                'updated_data' => $leaveType->toArray()
            ]);
            
            $result = $leaveType->save();

            if ($result) {
                \Log::info('Leave type update successful', ['id' => $id]);
                return redirect()->route('leave-types.index')->with('success', 'Leave type updated successfully');
            } else {
                \Log::error('Leave type update failed', ['id' => $id]);
                return back()->with('error', 'Failed to update leave type')->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('Leave type update error: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e
            ]);
            return back()->with('error', 'Failed to update leave type: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $this->validateAccess();

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
                    $btn = '<div class="btn-group btn-group-sm">';
                    $btn .= '<a href="'.route('leave-types.show', $row->id).'" class="btn btn-info btn-xs" title="View"><i class="ki-outline ki-eye"></i></a>';
                    $btn .= '<a href="'.route('leave-types.edit', $row->id).'" class="btn btn-warning btn-xs" title="Edit"><i class="ki-outline ki-notepad-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-xs" onclick="deleteLeaveType('.$row->id.')" title="Delete"><i class="ki-outline ki-trash-square"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'lt_status'])
                ->make(true);
        }
    }
}
