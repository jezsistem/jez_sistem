<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveRequestController extends Controller
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
        
        $title = 'Leave Requests';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        
        // Get filters
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));
        $userId = $request->get('user_id');
        $status = $request->get('status');
        $leaveTypeId = $request->get('leave_type_id');
        
        $leaveRequest = new LeaveRequest();
        $leaveRequests = $leaveRequest->getLeaveRequestsByFilters($startDate, $endDate, $userId, $status, $leaveTypeId);
        
        // Get users for filter
        $users = DB::table('users')->where('u_delete', '0')->orderBy('u_name')->get();
        
        // Get leave types for filter
        $leaveType = new LeaveType();
        $leaveTypes = $leaveType->getActiveLeaveTypes();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.leave_request.index', compact('leaveRequests', 'users', 'leaveTypes', 'startDate', 'endDate', 'userId', 'status', 'leaveTypeId', 'data'));
    }

    public function create()
    {
        $this->validateAccess();
        
        $title = 'Create Leave Request';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        // Get active leave types
        $leaveType = new LeaveType();
        $leaveTypes = $leaveType->getActiveLeaveTypes();

        $data = [
            'title' => $title,
            'subtitle' => 'Create Leave Request',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.leave_request.create', compact('leaveTypes', 'data'));
    }

    public function store(Request $request)
    {
        $this->validateAccess();

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'lr_start_date' => 'required|date',
            'lr_end_date' => 'nullable|date|after_or_equal:lr_start_date',
            'lr_start_time' => 'nullable|date_format:H:i',
            'lr_end_time' => 'nullable|date_format:H:i|after:lr_start_time',
            'lr_unit' => 'required|in:days,hours',
            'lr_reason' => 'required|string'
        ]);

        $userId = auth()->user()->id;
        $startDate = $request->lr_start_date;
        $endDate = $request->lr_end_date ?: $startDate;

        // Check if user can request leave for this period
        $leaveRequest = new LeaveRequest();
        if (!$leaveRequest->canRequestLeave($userId, $startDate, $endDate)) {
            return back()->with('error', 'You already have a leave request for this period')->withInput();
        }

        // Calculate total days/hours
        $startDateObj = Carbon::parse($startDate);
        $endDateObj = Carbon::parse($endDate);
        
        if ($request->lr_unit == 'hours') {
            $startTime = $request->lr_start_time ? Carbon::parse($request->lr_start_time) : Carbon::parse('00:00:00');
            $endTime = $request->lr_end_time ? Carbon::parse($request->lr_end_time) : Carbon::parse('23:59:59');
            
            $totalHours = $startDateObj->diffInDays($endDateObj) * 24;
            $totalHours += $startTime->diffInHours($endTime);
            $totalDays = 0;
        } else {
            $totalDays = $startDateObj->diffInDays($endDateObj) + 1;
            $totalHours = 0;
        }

        $data = [
            'user_id' => $userId,
            'leave_type_id' => $request->leave_type_id,
            'lr_start_date' => $startDate,
            'lr_end_date' => $endDate,
            'lr_start_time' => $request->lr_start_time,
            'lr_end_time' => $request->lr_end_time,
            'lr_total_days' => $totalDays,
            'lr_total_hours' => $totalHours,
            'lr_unit' => $request->lr_unit,
            'lr_reason' => $request->lr_reason,
            'lr_status' => 'pending'
        ];

        $result = $leaveRequest->storeData('add', null, $data);

        if ($result) {
            return redirect()->route('leave-requests.index')->with('success', 'Leave request submitted successfully');
        } else {
            return back()->with('error', 'Failed to submit leave request')->withInput();
        }
    }

    public function show($id)
    {
        $this->validateAccess();
        
        $title = 'Leave Request Detail';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $leaveRequest = DB::table('leave_requests')
            ->select([
                'leave_requests.*',
                'users.u_name',
                'users.u_nip',
                'users.ud_id',
                'user_divisions.ud_name',
                'leave_types.lt_name',
                'leave_types.lt_code',
                'leave_types.lt_color',
                'approvers.u_name as approver_name'
            ])
            ->leftJoin('users', 'users.id', '=', 'leave_requests.user_id')
            ->leftJoin('daily_schedules', function($join) {
                $join->on('daily_schedules.user_id', '=', 'leave_requests.user_id')
                     ->on('daily_schedules.ds_date', '=', 'leave_requests.lr_start_date');
            })
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'daily_schedules.ud_id')
            ->leftJoin('leave_types', 'leave_types.id', '=', 'leave_requests.leave_type_id')
            ->leftJoin('users as approvers', 'approvers.id', '=', 'leave_requests.lr_approved_by')
            ->where('leave_requests.id', $id)
            ->first();

        if (!$leaveRequest) {
            return redirect()->route('leave-requests.index')->with('error', 'Leave request not found');
        }

        // Get related daily schedules
        $dailySchedules = DB::table('daily_schedules')
            ->select([
                'daily_schedules.*',
                'shift_codes.sc_code',
                'shift_codes.sc_shift_name',
                'user_divisions.ud_name'
            ])
            ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'daily_schedules.ud_id')
            ->where('daily_schedules.user_id', $leaveRequest->user_id)
            ->whereBetween('daily_schedules.ds_date', [$leaveRequest->lr_start_date, $leaveRequest->lr_end_date])
            ->get();

        $data = [
            'title' => $title,
            'subtitle' => 'Leave Request Detail',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.leave_request.show', compact('leaveRequest', 'dailySchedules', 'data'));
    }

    public function edit($id)
    {
        $this->validateAccess();
        
        $title = 'Edit Leave Request';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $leaveRequest = LeaveRequest::findOrFail($id);
        
        // Debug: Log the leave request data
        \Log::info('Leave Request Edit Debug', [
            'id' => $leaveRequest->id,
            'lr_start_date' => $leaveRequest->lr_start_date,
            'lr_end_date' => $leaveRequest->lr_end_date,
            'lr_start_date_formatted' => $leaveRequest->lr_start_date ? $leaveRequest->lr_start_date->format('Y-m-d') : null,
            'lr_end_date_formatted' => $leaveRequest->lr_end_date ? $leaveRequest->lr_end_date->format('Y-m-d') : null,
        ]);
        
        // Check if user can edit this request
        if ($leaveRequest->user_id != auth()->user()->id) {
            return redirect()->route('leave-requests.index')->with('error', 'You can only edit your own leave requests');
        }

        // Check if request can be edited
        if ($leaveRequest->lr_status != 'pending') {
            return redirect()->route('leave-requests.index')->with('error', 'Only pending requests can be edited');
        }

        // Get active leave types
        $leaveType = new LeaveType();
        $leaveTypes = $leaveType->getActiveLeaveTypes();

        $data = [
            'title' => $title,
            'subtitle' => 'Edit Leave Request',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.leave_request.edit', compact('leaveRequest', 'leaveTypes', 'data'));
    }

    public function update(Request $request, $id)
    {
        $this->validateAccess();

        $leaveRequest = LeaveRequest::findOrFail($id);
        
        // Check if user can edit this request
        if ($leaveRequest->user_id != auth()->user()->id) {
            return redirect()->route('leave-requests.index')->with('error', 'You can only edit your own leave requests');
        }

        // Check if request can be edited
        if ($leaveRequest->lr_status != 'pending') {
            return redirect()->route('leave-requests.index')->with('error', 'Only pending requests can be edited');
        }

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'lr_start_date' => 'required|date',
            'lr_end_date' => 'nullable|date|after_or_equal:lr_start_date',
            'lr_start_time' => 'nullable|date_format:H:i',
            'lr_end_time' => 'nullable|date_format:H:i|after:lr_start_time',
            'lr_unit' => 'required|in:days,hours',
            'lr_reason' => 'required|string'
        ]);

        // Calculate total days/hours
        $startDate = $request->lr_start_date;
        $endDate = $request->lr_end_date ?: $startDate;
        
        $startDateObj = Carbon::parse($startDate);
        $endDateObj = Carbon::parse($endDate);
        
        if ($request->lr_unit == 'hours') {
            $startTime = $request->lr_start_time ? Carbon::parse($request->lr_start_time) : Carbon::parse('00:00:00');
            $endTime = $request->lr_end_time ? Carbon::parse($request->lr_end_time) : Carbon::parse('23:59:59');
            
            $totalHours = $startDateObj->diffInDays($endDateObj) * 24;
            $totalHours += $startTime->diffInHours($endTime);
            $totalDays = 0;
        } else {
            $totalDays = $startDateObj->diffInDays($endDateObj) + 1;
            $totalHours = 0;
        }

        $data = [
            'leave_type_id' => $request->leave_type_id,
            'lr_start_date' => $startDate,
            'lr_end_date' => $endDate,
            'lr_start_time' => $request->lr_start_time,
            'lr_end_time' => $request->lr_end_time,
            'lr_total_days' => $totalDays,
            'lr_total_hours' => $totalHours,
            'lr_unit' => $request->lr_unit,
            'lr_reason' => $request->lr_reason
        ];

        $result = $leaveRequest->storeData('edit', $id, $data);

        if ($result) {
            return redirect()->route('leave-requests.index')->with('success', 'Leave request updated successfully');
        } else {
            return back()->with('error', 'Failed to update leave request')->withInput();
        }
    }

    public function destroy($id)
    {
        $this->validateAccess();

        $leaveRequest = LeaveRequest::findOrFail($id);
        
        // Check if user can delete this request
        if ($leaveRequest->user_id != auth()->user()->id) {
            return redirect()->route('leave-requests.index')->with('error', 'You can only delete your own leave requests');
        }

        // Check if request can be deleted
        if ($leaveRequest->lr_status != 'pending') {
            return redirect()->route('leave-requests.index')->with('error', 'Only pending requests can be deleted');
        }

        $result = $leaveRequest->deleteData($id);

        if ($result) {
            return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted successfully');
        } else {
            return back()->with('error', 'Failed to delete leave request');
        }
    }

    // Admin approval methods
    public function approve(Request $request, $id)
    {
        $this->validateAccess();

        $leaveRequest = LeaveRequest::findOrFail($id);
        
        // Check if user can approve (must be supervisor or higher in same division)
        $currentUser = auth()->user();
        $currentUserPosition = DB::table('user_positions')
            ->where('id', $currentUser->up_id ?? 0)
            ->where('up_is_active', true)
            ->where('up_can_approve_leave', true)
            ->first();
        
        if (!$currentUserPosition) {
            return back()->with('error', 'You do not have permission to approve leave requests');
        }

        // Get leave requester's division
        $leaveRequester = DB::table('users')->where('id', $leaveRequest->user_id)->first();
        
        // Check if user is in same division as the leave requester
        if ($currentUser->ud_id != $leaveRequester->ud_id) {
            return back()->with('error', 'You can only approve leave requests from your division');
        }

        // Check if leave request is pending
        if ($leaveRequest->lr_status !== 'pending') {
            return back()->with('error', 'Only pending leave requests can be approved');
        }

        $request->validate([
            'lr_admin_notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Update leave request status
            $leaveRequest->lr_status = 'approved';
            $leaveRequest->lr_admin_notes = $request->lr_admin_notes;
            $leaveRequest->lr_approved_by = $currentUser->id;
            $leaveRequest->lr_approved_at = date('Y-m-d H:i:s');
            $leaveRequest->save();

            // If it's annual leave, update leave balance
            if ($leaveRequest->leaveType->lt_code === 'ANNUAL') {
                $leaveBalance = LeaveBalance::where('user_id', $leaveRequest->user_id)
                    ->where('leave_type_id', $leaveRequest->leave_type_id)
                    ->where('lb_year', date('Y'))
                    ->first();

                if ($leaveBalance) {
                    $leaveBalance->lb_used_balance += $leaveRequest->lr_total_days;
                    $leaveBalance->lb_remaining_balance = $leaveBalance->lb_initial_balance - $leaveBalance->lb_used_balance;
                    $leaveBalance->save();
                }
            }

            DB::commit();
            return redirect()->route('leave-requests.index')->with('success', 'Leave request approved successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to approve leave request: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $this->validateAccess();

        $leaveRequest = LeaveRequest::findOrFail($id);
        
        // Check if user can approve (must be supervisor or higher in same division)
        $currentUser = auth()->user();
        $currentUserPosition = DB::table('user_positions')
            ->where('id', $currentUser->up_id ?? 0)
            ->where('up_is_active', true)
            ->where('up_can_approve_leave', true)
            ->first();
        
        if (!$currentUserPosition) {
            return back()->with('error', 'You do not have permission to reject leave requests');
        }

        // Get leave requester's division
        $leaveRequester = DB::table('users')->where('id', $leaveRequest->user_id)->first();
        
        // Check if user is in same division as the leave requester
        if ($currentUser->ud_id != $leaveRequester->ud_id) {
            return back()->with('error', 'You can only reject leave requests from your division');
        }

        // Check if leave request is pending
        if ($leaveRequest->lr_status !== 'pending') {
            return back()->with('error', 'Only pending leave requests can be rejected');
        }

        $request->validate([
            'lr_admin_notes' => 'required|string'
        ]);

        try {
            $leaveRequest->lr_status = 'rejected';
            $leaveRequest->lr_admin_notes = $request->lr_admin_notes;
            $leaveRequest->lr_approved_by = $currentUser->id;
            $leaveRequest->lr_approved_at = date('Y-m-d H:i:s');
            $leaveRequest->save();

            return redirect()->route('leave-requests.index')->with('success', 'Leave request rejected successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject leave request: ' . $e->getMessage());
        }
    }

    // Get leave balance for AJAX
    public function getLeaveBalance($leaveTypeId)
    {
        $this->validateAccess();
        
        $userId = auth()->user()->id;
        $year = date('Y');
        
        $leaveBalance = new LeaveBalance();
        $balance = $leaveBalance->getLeaveBalance($userId, $leaveTypeId, $year);
        
        if ($balance) {
            return response()->json([
                'success' => true,
                'initial_balance' => $balance->lb_initial_balance,
                'used_balance' => $balance->lb_used_balance,
                'remaining_balance' => $balance->lb_remaining_balance
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No balance found for this leave type'
            ]);
        }
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            $startDate = $request->get('start_date', date('Y-m-d'));
            $endDate = $request->get('end_date', date('Y-m-d'));
            $userId = $request->get('user_id');
            $status = $request->get('status');
            $leaveTypeId = $request->get('leave_type_id');

            $query = DB::table('leave_requests')
                ->select([
                    'leave_requests.*',
                    'users.u_name',
                    'users.u_nip',
                    'user_divisions.ud_name',
                    'leave_types.lt_name',
                    'leave_types.lt_code',
                    'leave_types.lt_color',
                    'approvers.u_name as approver_name'
                ])
                ->leftJoin('users', 'users.id', '=', 'leave_requests.user_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('leave_types', 'leave_types.id', '=', 'leave_requests.leave_type_id')
                ->leftJoin('users as approvers', 'approvers.id', '=', 'leave_requests.lr_approved_by');

            // Apply filters
            if ($request->filled('start_date')) {
                $query->where('leave_requests.lr_start_date', '>=', $startDate);
            }
            if ($request->filled('end_date')) {
                $query->where('leave_requests.lr_start_date', '<=', $endDate);
            }
            if ($request->filled('user_id')) {
                $query->where('leave_requests.user_id', $userId);
            }
            if ($request->filled('status')) {
                $query->where('leave_requests.lr_status', $status);
            }
            if ($request->filled('leave_type_id')) {
                $query->where('leave_requests.leave_type_id', $leaveTypeId);
            }
            
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('users.u_name', 'like', '%' . $search . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $search . '%')
                      ->orWhere('leave_types.lt_name', 'like', '%' . $search . '%')
                      ->orWhere('leave_types.lt_code', 'like', '%' . $search . '%');
                });
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('lr_date', function($row) {
                    return date('d/m/Y', strtotime($row->lr_start_date));
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
                    $btn .= '        <!--begin::Menu item-->';
                    $btn .= '        <div class="menu-item px-3">';
                    $btn .= '            <a href="'.route('leave-requests.show', $row->id).'" class="menu-link px-3">View</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';
                    
                    $btn .= '        <!--begin::Menu item-->';
                    $btn .= '        <div class="menu-item px-3">';
                    $btn .= '            <a href="'.route('leave-requests.edit', $row->id).'" class="menu-link px-3">Edit</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';
                    
                    if ($row->lr_status == 'pending') {
                        $btn .= '        <!--begin::Menu item-->';
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="javascript:void(0)" onclick="showApprovalModal('.$row->id.', \'approve\')" class="menu-link px-3 text-success">Approve</a>';
                        $btn .= '        </div>';
                        $btn .= '        <!--end::Menu item-->';
                        
                        $btn .= '        <!--begin::Menu item-->';
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="javascript:void(0)" onclick="showApprovalModal('.$row->id.', \'reject\')" class="menu-link px-3 text-danger">Reject</a>';
                        $btn .= '        </div>';
                        $btn .= '        <!--end::Menu item-->';
                    }
                    $btn .= '    </div>';
                    $btn .= '    <!--end::Menu-->';
                    $btn .= '</div>';
                    
                    return $btn;
                })
                ->editColumn('lr_start_date', function($row) {
                    return date('d/m/Y', strtotime($row->lr_start_date));
                })
                ->editColumn('lr_end_date', function($row) {
                    return $row->lr_end_date ? date('d/m/Y', strtotime($row->lr_end_date)) : '-';
                })
                ->editColumn('lr_start_time', function($row) {
                    return $row->lr_start_time ? date('H:i', strtotime($row->lr_start_time)) : '-';
                })
                ->editColumn('lr_end_time', function($row) {
                    return $row->lr_end_time ? date('H:i', strtotime($row->lr_end_time)) : '-';
                })
                ->editColumn('lr_total_days', function($row) {
                    if ($row->lr_unit == 'days') {
                        return $row->lr_total_days . ' hari';
                    } else {
                        return $row->lr_total_hours . ' jam';
                    }
                })
                ->editColumn('lr_status', function($row) {
                    $statusClass = '';
                    $statusText = '';
                    
                    switch($row->lr_status) {
                        case 'pending':
                            $statusClass = 'badge badge-danger';
                            $statusText = 'Menunggu';
                            break;
                        case 'approved':
                            $statusClass = 'badge badge-success';
                            $statusText = 'Disetujui';
                            break;
                        case 'rejected':
                            $statusClass = 'badge badge-danger';
                            $statusText = 'Ditolak';
                            break;
                        case 'cancelled':
                            $statusClass = 'badge badge-secondary';
                            $statusText = 'Dibatalkan';
                            break;
                        default:
                            $statusClass = 'badge badge-secondary';
                            $statusText = ucfirst($row->lr_status);
                    }
                    
                    return '<span class="' . $statusClass . '">' . $statusText . '</span>';
                })
                ->editColumn('lt_name', function($row) {
                    $color = $row->lt_color ?: '#007bff';
                    return '<span style="color: ' . $color . ';">' . $row->lt_name . '</span>';
                })
                ->rawColumns(['action', 'lr_status', 'lt_name'])
                ->make(true);
        }
    }
}
