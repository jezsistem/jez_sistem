<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequestComment;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LeaveSummaryExport;
use App\Exports\LeaveRequestExport;
use Barryvdh\DomPDF\Facade\Pdf;

class LeaveRequestController extends Controller
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

    public function index(Request $request)
    {
        $this->validateAccess();

        $title = 'Leave Requests';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        // Get filters
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));
        $dateFilter = $request->get('date_filter', 'this_month');
        $userId = $request->get('user_id');
        $status = $request->get('status');
        $leaveTypeId = $request->get('leave_type_id');

        // Apply date filter if not custom
        if ($dateFilter && $dateFilter !== 'custom') {
            $dateRange = $this->getDateRangeFromFilter($dateFilter);
            $startDate = $dateRange['startDate'];
            $endDate = $dateRange['endDate'];
        }

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
            'segment' => request()->segment(1),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateFilter' => $dateFilter
        ];

        return view('app.leave_request.index', compact('leaveRequests', 'users', 'leaveTypes', 'startDate', 'endDate', 'dateFilter', 'userId', 'status', 'leaveTypeId', 'data'));
    }

    public function create()
    {
        // $this->validateAccess();

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
        // $this->validateAccess();

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'lr_start_date' => 'required|date',
            'lr_end_date' => 'nullable|date|after_or_equal:lr_start_date',
            'lr_start_time' => 'nullable|date_format:H:i',
            'lr_end_time' => 'nullable|date_format:H:i|after:lr_start_time',
            'lr_unit' => 'required|in:days,hours',
            'lr_reason' => 'required|string',
            'lr_attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240' // Max 10MB per file
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


        // cut saldo
        $leaveType = DB::table('leave_types')->where('id', $request->leave_type_id)->first()->lt_code;

//        dd($leaveRequest);

        if ($leaveType == 'ANNUAL') {
            $leaveRemaining = LeaveBalance::with('user')
                ->where('user_id', $userId)
                ->first();

            $newLeaveRemaining = $leaveRemaining->lb_remaining_balance - $totalDays;

            $leaveUsedBalance = $leaveRemaining->lb_used_balance + $totalDays;

            // update leave balance
            $leaveRemaining->update([
                'lb_remaining_balance' => $newLeaveRemaining,
                'lb_used_balance' => $leaveUsedBalance,
            ]);
        }


        // Handle multiple file uploads
        $attachmentData = null;
        if ($request->hasFile('lr_attachments')) {
            $files = $request->file('lr_attachments');
            $attachmentData = [];

            foreach ($files as $file) {
                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('leave_attachments', $fileName, 'public');

                $attachmentData[] = [
                    'file_path' => $filePath,
                    'original_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize()
                ];
            }
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

        // Use Eloquent to create leave request and get the ID
        $newLeaveRequest = LeaveRequest::create($data);

        // Handle attachments if leave request was created successfully
        if ($newLeaveRequest && $attachmentData) {
            foreach ($attachmentData as $attachment) {
                DB::table('leave_request_attachments')->insert([
                    'leave_request_id' => $newLeaveRequest->id,
                    'file_path' => $attachment['file_path'],
                    'original_name' => $attachment['original_name'],
                    'file_type' => $attachment['file_type'],
                    'file_size' => $attachment['file_size'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $result = $newLeaveRequest ? true : false;

        if ($result) {
            // Send notification to supervisors and managers in the same division
            $this->sendLeaveRequestNotification($userId, $data);

            return redirect()->route('leave-requests.index')->with('success', 'Leave request submitted successfully');
        } else {
            return back()->with('error', 'Failed to submit leave request')->withInput();
        }
    }

    public function approve(Request $request, $id)
    {
        $currentUser = auth()->user();

        // Ambil leave request + relasi user
        $leaveRequest = LeaveRequest::with('user')->findOrFail($id);
        $leaveRequester = $leaveRequest->user;

        $start = Carbon::parse($leaveRequest->lr_start_date);
        $end = Carbon::parse($leaveRequest->lr_end_date);
        $totalDays = $start->diffInDays($end) + 1;

//        $leaveType = DB::table('leave_types')->where('id', $leaveRequest->leave_type_id)->first()->lt_code;

//        dd($leaveRequest);

//        if ($leaveType == 'ANNUAL') {
//            $leaveRemaining = LeaveBalance::with('user')
//                ->where('user_id', $leaveRequest->user_id)
//                ->first();
//
//            $newLeaveRemaining = $leaveRemaining->lb_remaining_balance - $totalDays;
//
//            $leaveUsedBalance = $leaveRemaining->lb_used_balance + $totalDays;
//
//            // update leave balance
//            $leaveRemaining->update([
//                'lb_remaining_balance' => $newLeaveRemaining,
//                'lb_used_balance' => $leaveUsedBalance,
//            ]);
//        }

        $leaveRequest->update([
            'lr_status' => 'APPROVED',
            'lr_approved_by' => $currentUser->id,
            'lr_approved_at' => now(),
        ]);

//        $leaveRequestType =

        if ($currentUser->id === $leaveRequester->id) {
            \Log::warning("User {$currentUser->id} mencoba self-approve");
            return $this->deny($request, 'You cannot approve your own leave request');
        }

        $currentUserLevel = $currentUser->position->up_level ?? 0;
        $requesterLevel = $leaveRequester->position->up_level ?? 0;

        $requesterDivision = DB::table('user_divisions')
            ->where('id', $leaveRequester->ud_id)
            ->first();

//        if (!$requesterDivision || $currentUser->ud_id != $leaveRequester->ud_id) {
//            \Log::warning("Divisi berbeda: User {$currentUser->id} mencoba approve {$leaveRequester->id}");
//            return $this->deny($request, 'You can only approve leave requests within your division');
//        }

        $isDivisionLead = $requesterDivision && $currentUser->id == $requesterDivision->lead_id;
        $isDivisionManager = $requesterDivision && $currentUser->id == $requesterDivision->manager_id;

        if ($isDivisionLead) {
            \Log::info("User {$currentUser->id} adalah LEADER divisi, boleh approve");
        } elseif ($isDivisionManager) {
            \Log::info("User {$currentUser->id} adalah MANAGER divisi, boleh approve");
        } else {

            if ($currentUserLevel <= $requesterLevel) {
                \Log::warning("User {$currentUser->id} mencoba approve level >= dirinya ({$currentUserLevel} <= {$requesterLevel})");
                return $this->deny($request, 'You cannot approve leave requests from someone with higher or equal position level');
            }
        }


        $leaveRequest->update([
            'lr_status' => 'APPROVED',
            'lr_approved_by' => $currentUser->id,
            'lr_approved_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Leave request approved successfully',
            ]);
        }

        return back()->with('success', 'Leave request approved successfully');
    }

    public function show($id)
    {
        // $this->validateAccess();

        $title = 'Leave Request Detail';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $leaveRequest = LeaveRequest::with(['attachments', 'user', 'leaveType', 'approver'])
            ->findOrFail($id);

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
        // $this->validateAccess();

        $title = 'Edit Leave Request';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $leaveRequest = LeaveRequest::with('attachments')->findOrFail($id);

        // Debug: Log the leave request data
        \Log::info('Leave Request Edit Debug', [
            'id' => $leaveRequest->id,
            'lr_start_date' => $leaveRequest->lr_start_date,
            'lr_end_date' => $leaveRequest->lr_end_date,
            'lr_start_date_formatted' => $leaveRequest->lr_start_date ? $leaveRequest->lr_start_date->format('Y-m-d') : null,
            'lr_end_date_formatted' => $leaveRequest->lr_end_date ? $leaveRequest->lr_end_date->format('Y-m-d') : null,
            'attachments_count' => $leaveRequest->attachments->count()
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
        // $this->validateAccess();

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
            'lr_reason' => 'required|string',
            'lr_attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // Max 10MB per file
            'remove_attachments.*' => 'nullable|integer|exists:leave_request_attachments,id'
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

        // Handle removal of existing attachments
        if ($request->has('remove_attachments')) {
            foreach ($request->remove_attachments as $attachmentId) {
                $attachment = DB::table('leave_request_attachments')->where('id', $attachmentId)->first();
                if ($attachment) {
                    // Delete file from storage
                    $filePath = storage_path('app/public/' . $attachment->file_path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    // Delete from database
                    DB::table('leave_request_attachments')->where('id', $attachmentId)->delete();
                }
            }
        }

        // Handle new file uploads
        $attachmentData = null;
        if ($request->hasFile('lr_attachments')) {
            $files = $request->file('lr_attachments');
            $attachmentData = [];

            foreach ($files as $file) {
                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('leave_attachments', $fileName, 'public');

                $attachmentData[] = [
                    'file_path' => $filePath,
                    'original_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize()
                ];
            }
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

        // Use direct Eloquent update instead of custom storeData method
        try {
            $leaveRequest->update($data);

            // Handle new attachments if any
            if ($attachmentData) {
                foreach ($attachmentData as $attachment) {
                    DB::table('leave_request_attachments')->insert([
                        'leave_request_id' => $id,
                        'file_path' => $attachment['file_path'],
                        'original_name' => $attachment['original_name'],
                        'file_type' => $attachment['file_type'],
                        'file_size' => $attachment['file_size'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            \Log::info('Leave request updated successfully', [
                'id' => $id,
                'data' => $data,
                'user_id' => auth()->user()->id
            ]);

            return redirect()->route('leave-requests.index')->with('success', 'Leave request updated successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update leave request', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage(),
                'user_id' => auth()->user()->id
            ]);

            return back()->with('error', 'Failed to update leave request: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        \Log::info('Leave Request Delete Attempt', [
            'request_id' => $id,
            'user_id' => auth()->user()->id,
            'method' => request()->method(),
            'is_ajax' => request()->ajax(),
            'headers' => request()->headers->all()
        ]);

        // $this->validateAccess();

        $leaveRequest = LeaveRequest::findOrFail($id);

        // Check if user can delete this request
        if ($leaveRequest->user_id != auth()->user()->id) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own leave requests'
                ], 403);
            }
            return redirect()->route('leave-requests.index')->with('error', 'You can only delete your own leave requests');
        }

        // Check if request can be deleted
        if ($leaveRequest->lr_status != 'pending') {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be deleted'
                ], 403);
            }
            return redirect()->route('leave-requests.index')->with('error', 'Only pending requests can be deleted');
        }

        $result = $leaveRequest->deleteData($id);

        if ($result) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Leave request deleted successfully'
                ]);
            }
            return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted successfully');
        } else {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete leave request'
                ], 500);
            }
            return back()->with('error', 'Failed to delete leave request');
        }
    }

    // Admin approval methods
    //    public function approve(Request $request, $id)
    //    {
    //        \Log::info('Leave Request Approval Attempt', [
    //            'request_id' => $id,
    //            'user_id' => auth()->user()->id,
    //            'request_data' => $request->all(),
    //            'is_ajax' => $request->ajax()
    //        ]);
    //
    //        // $this->validateAccess();
    //
    //        $leaveRequest = LeaveRequest::findOrFail($id);
    //
    //        // Check if user can approve (must be supervisor or higher in same division)
    //        $currentUser = auth()->user();
    //
    ////        dd($approver_div);
    //        \Log::info('Current user info', [
    //            'user_id' => $currentUser->id,
    //            'position_id' => $currentUser->up_id,
    //            'division_id' => $currentUser->ud_id
    //        ]);
    //
    //        // Get leave requester info first to check if they're trying to approve their own request
    //        $leaveRequester = DB::table('users')->where('id', $leaveRequest->user_id)->first();
    //
    //        $approver_div = DB::table('store_types')->where('id', $leaveRequester->ud_id)->first();
    //
    //
    //        // Check if user is trying to approve their own leave request
    //        if ($currentUser->id == $leaveRequest->user_id) {
    //            \Log::warning('User trying to approve their own leave request');
    //            if ($request->ajax()) {
    //                return response()->json([
    //                    'success' => false,
    //                    'message' => 'You cannot approve your own leave request'
    //                ]);
    //            }
    //            return back()->with('error', 'You cannot approve your own leave request');
    //        }
    //
    //        $currentUserPosition = DB::table('user_positions')
    //            ->where('id', $currentUser->up_id ?? 0)
    //            ->where('up_is_active', true)
    //            ->where('up_can_approve_leave', true)
    //            ->where('up_level', '>=', 2) // Level 2 = Supervisor and above
    //            ->first();
    //
    //        \Log::info('Position check result', [
    //            'position_found' => $currentUserPosition ? true : false,
    //            'position_data' => $currentUserPosition,
    //            'required_level' => 'Supervisor (level 2) or above'
    //        ]);
    //
    //        if (!$currentUserPosition) {
    //            \Log::warning('User does not have approval permission - must be Supervisor or above');
    //            if ($request->ajax()) {
    //                return response()->json([
    //                    'success' => false,
    //                    'message' => 'Only Supervisor level and above can approve leave requests'
    //                ]);
    //            }
    //            return back()->with('error', 'Only Supervisor level and above can approve leave requests');
    //        }
    //
    //        $countSpvOnDivision = DB::table('users')
    //            ->join('user_divisions', 'users.ud_id', '=', 'user_divisions.id')
    //            ->join('user_positions', 'users.up_id', '=', 'user_positions.id')
    //            ->where('users.ud_id', $leaveRequester->ud_id)
    //            ->where('user_positions.up_level', '>=', 2)
    //            ->where('user_positions.up_can_approve_leave', true)
    //            ->where('user_positions.up_is_active', true)
    //            ->count();
    //
    //        if ($countSpvOnDivision > 0) {
    //            // Leave requester already retrieved above for self-approval check
    //
    //            \Log::info('Division check', [
    //                'current_user_division' => $currentUser->ud_id,
    //                'requester_division' => $leaveRequester->ud_id,
    //                'same_division' => $currentUser->ud_id == $leaveRequester->ud_id
    //            ]);
    //
    //            // Check if user is in same division as the leave requester
    //            if ($currentUser->ud_id != $leaveRequester->ud_id) {
    //                \Log::warning('User not in same division as requester');
    //                if ($request->ajax()) {
    //                    return response()->json([
    //                        'success' => false,
    //                        'message' => 'You can only approve leave requests from your division'
    //                    ]);
    //                }
    //                return back()->with('error', 'You can only approve leave requests from your division');
    //            }
    //        }
    //        // Check hierarchy - user cannot approve someone with higher or equal level
    //        $requesterPosition = DB::table('user_positions')
    //            ->where('id', $leaveRequester->up_id ?? 0)
    //            ->where('up_is_active', true)
    //            ->first();
    //
    //        if ($requesterPosition) {
    //            $currentUserLevel = $currentUserPosition->up_level;
    //            $requesterLevel = $requesterPosition->up_level;
    //
    //            \Log::info('Hierarchy check', [
    //                'current_user_level' => $currentUserLevel,
    //                'requester_level' => $requesterLevel,
    //                'current_user_position' => $currentUserPosition->up_name ?? 'Unknown',
    //                'requester_position' => $requesterPosition->up_name ?? 'Unknown'
    //            ]);
    //
    //            // User can only approve someone with lower level
    //            if ($currentUserLevel <= $requesterLevel) {
    //                \Log::warning('User trying to approve someone with higher or equal level');
    //                if ($request->ajax()) {
    //                    return response()->json([
    //                        'success' => false,
    //                        'message' => 'You cannot approve leave requests from someone with higher or equal position level'
    //                    ]);
    //                }
    //                return back()->with('error', 'You cannot approve leave requests from someone with higher or equal position level');
    //            }
    //        }
    //
    //        // Check if leave request is pending
    //        \Log::info('Leave request status check', [
    //            'current_status' => $leaveRequest->lr_status,
    //            'is_pending' => $leaveRequest->lr_status === 'pending'
    //        ]);
    //
    //        if ($leaveRequest->lr_status !== 'pending') {
    //            \Log::warning('Leave request is not pending');
    //            if ($request->ajax()) {
    //                return response()->json([
    //                    'success' => false,
    //                    'message' => 'Only pending leave requests can be approved'
    //                ]);
    //            }
    //            return back()->with('error', 'Only pending leave requests can be approved');
    //        }
    //
    //        $request->validate([
    //            'lr_admin_notes' => 'nullable|string'
    //        ]);
    //
    //        try {
    //            DB::beginTransaction();
    //
    //            \Log::info('Updating leave request', [
    //                'old_status' => $leaveRequest->lr_status,
    //                'new_status' => 'approved',
    //                'admin_notes' => $request->lr_admin_notes,
    //                'approved_by' => $currentUser->id,
    //                'approved_at' => date('Y-m-d H:i:s')
    //            ]);
    //
    //            // Update leave request status
    //            $leaveRequest->lr_status = 'approved';
    //            $leaveRequest->lr_admin_notes = $request->lr_admin_notes;
    //            $leaveRequest->lr_approved_by = $currentUser->id;
    //            $leaveRequest->lr_approved_at = date('Y-m-d H:i:s');
    //            $leaveRequest->save();
    //
    //            \Log::info('Leave request updated successfully', [
    //                'leave_request_id' => $leaveRequest->id,
    //                'new_status' => $leaveRequest->lr_status
    //            ]);
    //
    //            // If it's annual leave, update leave balance
    //            if ($leaveRequest->leaveType->lt_code === 'ANNUAL') {
    //                $leaveBalance = LeaveBalance::where('user_id', $leaveRequest->user_id)
    //                    ->where('leave_type_id', $leaveRequest->leave_type_id)
    //                    ->where('lb_year', date('Y'))
    //                    ->first();
    //
    //                if ($leaveBalance) {
    //                    $leaveBalance->lb_used_balance += $leaveRequest->lr_total_days;
    //                    $leaveBalance->lb_remaining_balance = $leaveBalance->lb_initial_balance - $leaveBalance->lb_used_balance;
    //                    $leaveBalance->save();
    //                }
    //            }
    //
    //            // Create attendance records for each day of leave
    //            \Log::info('Creating attendance records for leave', [
    //                'leave_request_id' => $leaveRequest->id,
    //                'user_id' => $leaveRequest->user_id,
    //                'start_date' => $leaveRequest->lr_start_date,
    //                'end_date' => $leaveRequest->lr_end_date,
    //                'leave_type_code' => $leaveRequest->leaveType->lt_code
    //            ]);
    //
    //            $startDate = Carbon::parse($leaveRequest->lr_start_date);
    //            $endDate = $leaveRequest->lr_end_date ? Carbon::parse($leaveRequest->lr_end_date) : $startDate;
    //
    //            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
    //                // Check if attendance record already exists for this date
    //                $existingAttendance = DB::table('attendance')
    //                    ->where('user_id', $leaveRequest->user_id)
    //                    ->where('at_date', $date->format('Y-m-d'))
    //                    ->first();
    //
    //                if (!$existingAttendance) {
    //                    // Create attendance record for leave
    //                    $attendanceId = DB::table('attendance')->insertGetId([
    //                        'user_id' => $leaveRequest->user_id,
    //                        'at_date' => $date->format('Y-m-d'),
    //                        'at_status' => 'leave_' . $leaveRequest->leaveType->lt_code,
    //                        'at_notes' => $leaveRequest->lr_reason,
    //                        'at_source' => 'system',
    //                        'created_by' => $currentUser->id,
    //                        'created_at' => now(),
    //                        'updated_at' => now()
    //                    ]);
    //
    //                    \Log::info('Attendance record created for leave', [
    //                        'attendance_id' => $attendanceId,
    //                        'user_id' => $leaveRequest->user_id,
    //                        'date' => $date->format('Y-m-d'),
    //                        'status' => 'leave_' . $leaveRequest->leaveType->lt_code
    //                    ]);
    //                } else {
    //                    \Log::info('Attendance record already exists for date', [
    //                        'user_id' => $leaveRequest->user_id,
    //                        'date' => $date->format('Y-m-d'),
    //                        'existing_status' => $existingAttendance->at_status
    //                    ]);
    //                }
    //            }
    //
    //            DB::commit();
    //            \Log::info('Database transaction committed successfully');
    //
    //            // Send notification to the requester
    //            $this->sendLeaveStatusChangeNotification($leaveRequest->id, 'approved', $currentUser->u_name);
    //
    //            if ($request->ajax()) {
    //                \Log::info('Sending AJAX response', ['success' => true]);
    //                return response()->json([
    //                    'success' => true,
    //                    'message' => 'Leave request approved successfully'
    //                ]);
    //            }
    //
    //            \Log::info('Sending redirect response');
    //            return redirect()->route('leave-requests.index')->with('success', 'Leave request approved successfully');
    //        } catch (\Exception $e) {
    //            \Log::error('Error approving leave request', [
    //                'error' => $e->getMessage(),
    //                'trace' => $e->getTraceAsString()
    //            ]);
    //
    //            DB::rollback();
    //
    //            if ($request->ajax()) {
    //                return response()->json([
    //                    'success' => false,
    //                    'message' => 'Failed to approve leave request: ' . $e->getMessage()
    //                ]);
    //            }
    //
    //            return back()->with('error', 'Failed to approve leave request: ' . $e->getMessage());
    //        }
    //    }


    protected function deny(Request $request, string $message)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ]);
        }

        return back()->with('error', $message);
    }

    public function reject(Request $request, $id)
    {
        \Log::info('Leave Request Rejection Attempt', [
            'request_id' => $id,
            'user_id' => auth()->user()->id,
            'request_data' => $request->all(),
            'is_ajax' => $request->ajax()
        ]);

        $currentUser = auth()->user();

        // Ambil leave request + relasi user
        $leaveRequest = LeaveRequest::with('user')->findOrFail($id);
        $leaveRequester = $leaveRequest->user;

        if ($currentUser->id === $leaveRequester->id) {
            \Log::warning("User {$currentUser->id} mencoba self-reject");
            return $this->deny($request, 'You cannot reject your own leave request');
        }

        $currentUserLevel = $currentUser->position->up_level ?? 0;
        $requesterLevel = $leaveRequester->position->up_level ?? 0;

        $requesterDivision = DB::table('user_divisions')
            ->where('id', $leaveRequester->ud_id)
            ->first();

        // Cek apakah current user leader/manager dari divisi ini
        $isDivisionLead = $requesterDivision && $currentUser->id == $requesterDivision->lead_id;
        $isDivisionManager = $requesterDivision && $currentUser->id == $requesterDivision->manager_id;

        if ($isDivisionLead) {
            \Log::info("User {$currentUser->id} adalah LEADER divisi, boleh reject");
        } elseif ($isDivisionManager) {
            \Log::info("User {$currentUser->id} adalah MANAGER divisi, boleh reject");
        } else {
            if ($currentUserLevel <= $requesterLevel) {
                \Log::warning("User {$currentUser->id} mencoba reject level >= dirinya ({$currentUserLevel} <= {$requesterLevel})");
                return $this->deny($request, 'You cannot reject leave requests from someone with higher or equal position level');
            }
        }

        // Check if leave request is pending
        if ($leaveRequest->lr_status !== 'pending') {
            return $this->deny($request, 'Only pending leave requests can be rejected');
        }

        try {
            $request->validate([
                'lr_admin_notes' => 'required|string'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation failed for reject', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors()))
                ]);
            }

            return back()->withErrors($e->errors())->withInput();
        }

        try {

            $leaveType = DB::table('leave_types')->where('id', $leaveRequest->leave_type_id)->first()->lt_code;

//            dd($leaveRequest);

            $start = Carbon::parse($leaveRequest->lr_start_date);
            $end = Carbon::parse($leaveRequest->lr_end_date);
            $totalDays = $start->diffInDays($end) + 1;

            if ($leaveType == 'ANNUAL') {
                $leaveRemaining = LeaveBalance::with('user')
                    ->where('user_id', $leaveRequest->user_id)
                    ->first();

                $newLeaveRemaining = $leaveRemaining->lb_remaining_balance + $totalDays;

                $leaveUsedBalance = $leaveRemaining->lb_used_balance - $totalDays;

                // update leave balance
                $leaveRemaining->update([
                    'lb_remaining_balance' => $newLeaveRemaining,
                    'lb_used_balance' => $leaveUsedBalance,
                ]);
            }


            \Log::info('Updating leave request for rejection', [
                'old_status' => $leaveRequest->lr_status,
                'new_status' => 'rejected',
                'admin_notes' => $request->lr_admin_notes,
                'approved_by' => $currentUser->id,
                'approved_at' => date('Y-m-d H:i:s')
            ]);

            $leaveRequest->update([
                'lr_status' => 'rejected',
                'lr_admin_notes' => $request->lr_admin_notes,
                'lr_approved_by' => $currentUser->id,
                'lr_approved_at' => now(),
            ]);

            \Log::info('Leave request rejected successfully', [
                'leave_request_id' => $leaveRequest->id,
                'new_status' => $leaveRequest->lr_status
            ]);

            // Send notification to the requester
            $this->sendLeaveStatusChangeNotification($leaveRequest->id, 'rejected', $currentUser->u_name);

            if ($request->ajax()) {
                \Log::info('Sending AJAX response for rejection', ['success' => true]);
                return response()->json([
                    'success' => true,
                    'message' => 'Leave request rejected successfully'
                ]);
            }

            \Log::info('Sending redirect response for rejection');
            return redirect()->route('leave-requests.index')->with('success', 'Leave request rejected successfully');
        } catch (\Exception $e) {
            \Log::error('Error rejecting leave request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject leave request: ' . $e->getMessage()
                ]);
            }

            return back()->with('error', 'Failed to reject leave request: ' . $e->getMessage());
        }
    }

    // Get leave balance for AJAX
    public function getLeaveBalance($leaveTypeId)
    {
        // $this->validateAccess();

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
        if (request()->ajax()) {
            $startDate = $request->get('start_date', date('Y-m-d'));
            $endDate = $request->get('end_date', date('Y-m-d'));
            $dateFilter = $request->get('date_filter', 'this_month');
            $userId = $request->get('user_id');
            $status = $request->get('status');
            $leaveTypeId = $request->get('leave_type_id');

            // Apply date filter if not custom
            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }

            $query = LeaveRequest::with(['attachments', 'user', 'leaveType', 'approver'])
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
                ->leftJoin('users as approvers', 'approvers.id', '=', 'leave_requests.lr_approved_by')
                ->orderBy('leave_requests.created_at', 'desc');

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
                $query->where(function ($q) use ($search) {
                    $q->where('users.u_name', 'like', '%' . $search . '%')
                        ->orWhere('users.u_nip', 'like', '%' . $search . '%')
                        ->orWhere('leave_types.lt_name', 'like', '%' . $search . '%')
                        ->orWhere('leave_types.lt_code', 'like', '%' . $search . '%');
                });
            }
//teees
            return datatables()->eloquent($query)
                ->addIndexColumn()
                ->addColumn('lr_date', function ($row) {
                    $requestDate = date('d/m/Y', strtotime($row->created_at));
                    $requestTime = date('H:i', strtotime($row->created_at));
                    return '<span title="Request submitted on ' . $requestDate . ' at ' . $requestTime . '">' . $requestDate . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">';
                    $btn .= '    <!--begin::Toggle-->';
                    $btn .= '    <button type="button" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start">';
                    $btn .= '        Actions';
                    $btn .= '    </button>';
                    $btn .= '    <!--end::Toggle-->';

                    $btn .= '    <!--begin::Menu-->';
                    $btn .= '    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-auto min-w-150px" data-kt-menu="true">';
                    $btn .= '        <!--begin::Menu item-->';
                    $btn .= '        <div class="menu-item px-3">';
                    $btn .= '            <a href="' . route('leave-requests.show', $row->id) . '" class="menu-link px-3">View</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';

                    $btn .= '        <!--begin::Menu item-->';
                    $btn .= '        <div class="menu-item px-3">';
                    $btn .= '            <a href="' . route('leave-requests.edit', $row->id) . '" class="menu-link px-3">Edit</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';

                    if ($row->lr_status == 'pending') {
                        $btn .= '        <!--begin::Menu item-->';
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="javascript:void(0)" onclick="showApprovalModal(' . $row->id . ', \'approve\')" class="menu-link px-3 text-success">Approve</a>';
                        $btn .= '        </div>';
                        $btn .= '        <!--end::Menu item-->';

                        $btn .= '        <!--begin::Menu item-->';
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="javascript:void(0)" onclick="showApprovalModal(' . $row->id . ', \'reject\')" class="menu-link px-3 text-danger">Reject</a>';
                        $btn .= '        </div>';
                        $btn .= '        <!--end::Menu item-->';
                    }

                    // Add delete button only for the request owner
                    if (auth()->check() && auth()->id() == $row->user_id) {
                        $btn .= '        <!--begin::Menu item-->';
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="javascript:void(0)" onclick="deleteLeaveRequest(' . $row->id . ', \'' . $row->u_name . '\')" class="menu-link px-3 text-danger">Delete</a>';
                        $btn .= '        </div>';
                        $btn .= '        <!--end::Menu item-->';
                    }
                    $btn .= '    </div>';
                    $btn .= '    <!--end::Menu-->';
                    $btn .= '</div>';

                    return $btn;
                })
                ->addColumn('lr_attachment', function ($row) {
                    // Use Eloquent relationship to get attachments
                    if ($row->attachments && $row->attachments->count() > 0) {
                        $attachmentHtml = '<div class="text-center">';

                        foreach ($row->attachments as $index => $attachment) {
                            $fileIcon = '';
                            $fileType = strtolower($attachment->file_type ?? '');

                            if (strpos($fileType, 'pdf') !== false) {
                                $fileIcon = '<i class="fas fa-file-pdf text-danger"></i>';
                            } elseif (strpos($fileType, 'image') !== false) {
                                $fileIcon = '<i class="fas fa-file-image text-primary"></i>';
                            } elseif (strpos($fileType, 'word') !== false) {
                                $fileIcon = '<i class="fas fa-file-word text-info"></i>';
                            } else {
                                $fileIcon = '<i class="fas fa-file text-secondary"></i>';
                            }

                            $fileName = $attachment->original_name ?: 'Attachment';
                            $fileSize = $attachment->file_size ? $this->formatFileSize($attachment->file_size) : '';

                            $attachmentHtml .= '<div class="mb-1">' .
                                '<button type="button" class="btn btn-sm btn-light-primary" onclick="viewAttachment(' . $row->id . ', \'' . $attachment->file_path . '\', \'' . $attachment->original_name . '\', \'' . $attachment->file_type . '\')">' .
                                $fileIcon . ' ' . substr($fileName, 0, 5) . (strlen($fileName) > 5 ? '...' : '') . '</button>' .
                                '</div>';
                        }

                        $attachmentHtml .= '</div>';
                        return $attachmentHtml;
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->editColumn('lr_start_date', function ($row) {
                    return date('d/m/Y', strtotime($row->lr_start_date));
                })
                ->editColumn('lr_end_date', function ($row) {
                    return $row->lr_end_date ? date('d/m/Y', strtotime($row->lr_end_date)) : '-';
                })
                ->editColumn('lr_start_time', function ($row) {
                    return $row->lr_start_time ? date('H:i', strtotime($row->lr_start_time)) : '-';
                })
                ->editColumn('lr_end_time', function ($row) {
                    return $row->lr_end_time ? date('H:i', strtotime($row->lr_end_time)) : '-';
                })
                ->editColumn('lr_total_days', function ($row) {
                    if ($row->lr_unit == 'days') {
                        return $row->lr_total_days . ' hari';
                    } else {
                        return $row->lr_total_hours . ' jam';
                    }
                })
                ->editColumn('lr_status', function ($row) {
                    $statusClass = '';
                    $statusText = '';

                    switch ($row->lr_status) {
                        case 'pending':
                            $statusClass = 'badge badge-primary';
                            $statusText = 'PENDING';
                            break;
                        case 'approved':
                            $statusClass = 'badge bg-success';
                            $statusText = 'APPROVED';
                            break;
                        case 'rejected':
                            $statusClass = 'badge badge-danger';
                            $statusText = 'REJECTED';
                            break;
                        case 'cancelled':
                            $statusClass = 'badge badge-warning';
                            $statusText = 'CANCELLED';
                            break;
                        default:
                            $statusClass = 'badge badge-secondary';
                            $statusText = ucfirst($row->lr_status);
                    }

                    return '<span class="' . $statusClass . '">' . $statusText . '</span>';
                })
                ->editColumn('lt_name', function ($row) {
                    $color = $row->lt_color ?: '#007bff';
                    return '<span style="color: ' . $color . ';">' . $row->lt_name . '</span>';
                })
                ->rawColumns(['action', 'lr_status', 'lt_name', 'lr_attachment', 'lr_date'])
                ->make(true);
        }
    }

    /**
     * Show leave summary report
     */
    public function summaryReport(Request $request)
    {
        // $this->validateAccess();

        $title = 'Leave Summary Report';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        // Get date range from request or default to current month
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));
        $dateFilter = $request->get('date_filter', 'this_month');

        if ($dateFilter && $dateFilter !== 'custom') {
            $dateRange = $this->getDateRangeFromFilter($dateFilter);
            $startDate = $dateRange['startDate'];
            $endDate = $dateRange['endDate'];
        }

        // Get divisions for filter
        $divisions = DB::table('user_divisions')
            ->where('ud_status', 'active')
            ->orderBy('ud_name')
            ->get();

        // Get leave types for dynamic columns
        $leaveTypes = DB::table('leave_types')
            ->where('lt_is_active', true)
            ->orderBy('lt_name')
            ->get();

        // Calculate statistics for the date range
        $stats = $this->getLeaveStatistics($startDate, $endDate);

        $data = [
            'title' => $title,
            'subtitle' => 'Leave Summary Report',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => 'leave-requests',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateFilter' => $dateFilter,
            'divisions' => $divisions,
            'leaveTypes' => $leaveTypes,
            'stats' => $stats
        ];

        return view('app.leave_request.summary_report', compact('data'));
    }

    /**
     * Get leave statistics for the given date range
     */
    private function getLeaveStatistics($startDate, $endDate)
    {
        try {
            \Log::info('Getting leave statistics', [
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);

            // Get leave requests count by status
            $statusStats = DB::table('leave_requests')
                ->select('lr_status', DB::raw('count(*) as total'))
                ->whereBetween('lr_start_date', [$startDate, $endDate])
                ->groupBy('lr_status')
                ->get();

            \Log::info('Status stats retrieved', [
                'count' => $statusStats->count(),
                'data' => $statusStats->toArray()
            ]);

            // Get leave requests count by leave type
            $typeStats = DB::table('leave_requests as lr')
                ->leftJoin('leave_types as lt', 'lr.leave_type_id', '=', 'lt.id')
                ->select('lt.lt_code', 'lt.lt_name', DB::raw('count(*) as total'))
                ->whereBetween('lr.lr_start_date', [$startDate, $endDate])
                ->where('lr.lr_status', 'approved')
                ->groupBy('lt.id', 'lt.lt_code', 'lt.lt_name')
                ->get();

            \Log::info('Type stats retrieved', [
                'count' => $typeStats->count(),
                'data' => $typeStats->toArray()
            ]);

            $stats = [];

            // Add status-based statistics
            foreach ($statusStats as $stat) {
                $stats[] = (object)[
                    'lr_status' => $stat->lr_status,
                    'total' => $stat->total
                ];
            }

            // Add leave type statistics
            foreach ($typeStats as $stat) {
                $stats[] = (object)[
                    'lr_status' => 'leave_' . strtolower($stat->lt_code),
                    'total' => $stat->total
                ];
            }

            \Log::info('Final stats array', [
                'count' => count($stats),
                'data' => $stats
            ]);

            return $stats;
        } catch (\Exception $e) {
            \Log::error('Error getting leave statistics: ' . $e->getMessage(), [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get date range from filter
     */
    private function getDateRangeFromFilter($filter)
    {
        $today = now();

        switch ($filter) {
            case 'this_week':
                $startDate = $today->copy()->startOfWeek();
                $endDate = $today->copy()->endOfWeek();
                break;
            case 'past_week':
                $startDate = $today->copy()->subWeek()->startOfWeek();
                $endDate = $today->copy()->subWeek()->endOfWeek();
                break;
            case 'next_week':
                $startDate = $today->copy()->addWeek()->startOfWeek();
                $endDate = $today->copy()->addWeek()->endOfWeek();
                break;
            case 'this_month':
                $startDate = $today->copy()->startOfMonth();
                $endDate = $today->copy()->endOfMonth();
                break;
            case 'last_month':
                $startDate = $today->copy()->subMonth()->startOfMonth();
                $endDate = $today->copy()->subMonth()->endOfMonth();
                break;
            case 'next_month':
                $startDate = $today->copy()->addMonth()->startOfMonth();
                $endDate = $today->copy()->addMonth()->endOfMonth();
                break;
            default:
                $startDate = $today->copy()->startOfMonth();
                $endDate = $today->copy()->endOfMonth();
        }

        return [
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d')
        ];
    }

    /**
     * Get leave summary data
     */
    private function getLeaveSummary($startDate, $endDate, $divisionId = null)
    {
        try {
            // Get all active users with NIP only
            $usersQuery = DB::table('users as u')
                ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
                ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
                ->leftJoin('user_types as ut', 'u.ut_id', '=', 'ut.id')
                ->where('u.u_delete', '!=', '1')
                ->whereNotNull('u.u_nip')
                ->where('u.u_nip', '!=', '')
                ->select([
                    'u.id as user_id',
                    'u.u_nip',
                    'u.u_name',
                    'up.up_name as position_name',
                    'ud.ud_name as division_name',
                    'ut.ut_name as work_type'
                ]);

            if ($divisionId) {
                $usersQuery->where('u.ud_id', $divisionId);
            }

            $users = $usersQuery->orderBy('u.u_name')->get();

            // Get leave types for dynamic columns
            $leaveTypes = DB::table('leave_types')
                ->where('lt_is_active', true)
                ->orderBy('lt_name')
                ->get();

            // Process each user to add leave data and balance
            foreach ($users as $user) {
                // Get leave requests for this user in date range
                $leaveRequests = DB::table('leave_requests as lr')
                    ->leftJoin('leave_types as lt', 'lr.leave_type_id', '=', 'lt.id')
                    ->where('lr.user_id', $user->user_id)
                    ->whereBetween('lr.lr_start_date', [$startDate, $endDate])
                    ->where('lr.lr_status', 'approved')
                    ->select([
                        'lt.lt_code',
                        'lt.lt_name',
                        'lr.lr_unit',
                        'lr.lr_total_days',
                        'lr.lr_total_hours'
                    ])
                    ->get();

                // Calculate totals
                $user->total_leave_requests = $leaveRequests->count();
                $user->total_days = $leaveRequests->where('lr_unit', 'days')->sum('lr_total_days');
                $user->total_hours = $leaveRequests->where('lr_unit', 'hours')->sum('lr_total_hours');

                // Add leave type specific data
                foreach ($leaveTypes as $leaveType) {
                    $leaveData = $leaveRequests->where('lt_code', $leaveType->lt_code);
                    $totalDays = $leaveData->where('lr_unit', 'days')->sum('lr_total_days');
                    $totalHours = $leaveData->where('lr_unit', 'hours')->sum('lr_total_hours');

                    $user->{'leave_' . strtolower($leaveType->lt_code)} = $totalDays > 0 ? $totalDays : $totalHours;
                }

                // Get leave balance for annual leave
                $annualLeaveBalance = DB::table('leave_balances as lb')
                    ->leftJoin('leave_types as lt', 'lb.leave_type_id', '=', 'lt.id')
                    ->where('lb.user_id', $user->user_id)
                    ->where('lt.lt_code', 'ANNUAL')
                    ->where('lb.lb_year', date('Y'))
                    ->select(['lb.lb_remaining_balance'])
                    ->first();

                $user->annual_leave_balance = $annualLeaveBalance ? $annualLeaveBalance->lb_remaining_balance : 0;
            }

            return $users;
        } catch (\Exception $e) {
            \Log::error('Error in getLeaveSummary: ' . $e->getMessage(), [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'divisionId' => $divisionId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get leave summary report datatables
     */
    public function getSummaryReportDatatables(Request $request)
    {
        if (request()->ajax()) {
            try {
                // $this->validateAccess();

                \Log::info('Leave Summary Report Datatables Request', [
                    'request_data' => $request->all()
                ]);

                // Get date range
                $startDate = $request->get('start_date', date('Y-m-01'));
                $endDate = $request->get('end_date', date('Y-m-t'));
                $dateFilter = $request->get('date_filter', 'this_month');

                \Log::info('Leave Summary Report - Initial date values', [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'dateFilter' => $dateFilter
                ]);

                if ($dateFilter && $dateFilter !== 'custom') {
                    $dateRange = $this->getDateRangeFromFilter($dateFilter);
                    $startDate = $dateRange['startDate'];
                    $endDate = $dateRange['endDate'];

                    \Log::info('Leave Summary Report - Date range calculated', [
                        'startDate' => $startDate,
                        'endDate' => $endDate
                    ]);
                }

                $divisionId = $request->get('division_id');

                \Log::info('Leave Summary Report - Getting data', [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'divisionId' => $divisionId
                ]);

                // Use the same method as summaryReport to get data
                $summaryData = $this->getLeaveSummary($startDate, $endDate, $divisionId);

                // Apply search filter if provided
                if ($request->filled('search')) {
                    $search = $request->get('search');
                    $summaryData = $summaryData->filter(function ($item) use ($search) {
                        return stripos($item->u_name, $search) !== false ||
                            stripos($item->u_nip, $search) !== false;
                    });
                }

                // Convert to array for DataTables
                $data = [];
                foreach ($summaryData as $index => $item) {
                    $data[] = (array)$item;
                    $data[$index]['DT_RowIndex'] = $index + 1;
                }

                // Apply pagination manually for server-side processing
                $totalRecords = count($data);
                $start = $request->get('start', 0);
                $length = $request->get('length', 25);
                $paginatedData = array_slice($data, $start, $length);

                return response()->json([
                    'draw' => $request->get('draw', 1),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $totalRecords,
                    'data' => $paginatedData
                ]);
            } catch (\Exception $e) {
                \Log::error('Leave Summary Report Datatables Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['error' => 'Server error'], 500);
            }
        } else {
            return response()->json(['error' => 'Invalid request'], 400);
        }
    }

    /**
     * Export summary report to Excel
     */
    public function exportSummaryToExcel(Request $request)
    {
        // $this->validateAccess();

        try {
            \Log::info('Export Excel started', [
                'request_data' => $request->all()
            ]);

            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $dateFilter = $request->get('date_filter', 'this_month');

            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }

            \Log::info('Export Excel - Date range', [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'divisionId' => $request->get('division_id')
            ]);

            $summaryData = $this->getLeaveSummary($startDate, $endDate, $request->get('division_id'));

            \Log::info('Export Excel - Data retrieved', [
                'data_count' => $summaryData->count()
            ]);

            // Apply search filter if provided
            if ($request->filled('search')) {
                $search = $request->get('search');
                $summaryData = $summaryData->filter(function ($item) use ($search) {
                    return stripos($item->u_name, $search) !== false ||
                        stripos($item->u_nip, $search) !== false;
                });
            }

            $filename = 'leave_summary_' . date('Y-m-d_H-i-s');
            if ($request->get('division_id')) {
                $division = DB::table('user_divisions')->find($request->get('division_id'));
                $filename .= '_' . ($division ? str_replace(' ', '_', $division->ud_name) : 'all');
            }
            if ($request->get('start_date') && $request->get('end_date')) {
                $filename .= '_' . $request->get('start_date') . '_to_' . $request->get('end_date');
            }
            $filename .= '.xlsx';

            \Log::info('Export Excel - Creating export', [
                'filename' => $filename,
                'export_class' => 'LeaveSummaryExport'
            ]);

            // Create export instance
            $export = new LeaveSummaryExport($summaryData);

            \Log::info('Export Excel - Export instance created successfully');

            // Use simple Excel download like BreakTimeController
            return Excel::download($export, $filename);
        } catch (\Exception $e) {
            \Log::error('Export Excel Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }

    /**
     * Export summary report to PDF
     */
    public function exportSummaryToPDF(Request $request)
    {
        // $this->validateAccess();

        try {
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $dateFilter = $request->get('date_filter', 'this_month');

            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }

            $summaryData = $this->getLeaveSummary($startDate, $endDate, $request->get('division_id'));

            // Apply search filter if provided
            if ($request->filled('search')) {
                $search = $request->get('search');
                $summaryData = $summaryData->filter(function ($item) use ($search) {
                    return stripos($item->u_name, $search) !== false ||
                        stripos($item->u_nip, $search) !== false;
                });
            }

            // Generate HTML for PDF
            $html = $this->generateSummaryReportHTML($summaryData, $request);

            // Generate filename
            $filename = 'leave_summary_' . date('Y-m-d_H-i-s');
            if ($request->get('division_id')) {
                $division = DB::table('user_divisions')->find($request->get('division_id'));
                $filename .= '_' . ($division ? str_replace('e', '_', $division->ud_name) : 'all');
            }
            if ($request->get('start_date') && $request->get('end_date')) {
                $filename .= '_' . $request->get('start_date') . '_to_' . $request->get('end_date');
            }
            $filename .= '.pdf';

            $pdf = \PDF::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Export leave summary PDF error: ' . $e->getMessage());
            return back()->with('error', 'Export PDF failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate HTML for summary report PDF
     */
    private function generateSummaryReportHTML($summaryData, $request)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Leave Summary Report</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h1 { margin: 0; color: #2E75B6; }
                .header p { margin: 5px 0; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #4472C4; color: white; font-weight: bold; }
                .text-center { text-align: center; }
                .summary-stats { margin-bottom: 20px; }
                .summary-stats .stat-item { 
                    display: inline-block; 
                    margin: 10px; 
                    padding: 10px; 
                    background-color: #f8f9fa; 
                    border: 1px solid #dee2e6; 
                    border-radius: 5px; 
                    text-align: center; 
                    min-width: 120px; 
                }
                .summary-stats .stat-number { 
                    font-size: 18px; 
                    font-weight: bold; 
                    color: #2E75B6; 
                }
                .summary-stats .stat-label { 
                    font-size: 11px; 
                    color: #666; 
                    margin-top: 5px; 
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN REKAPITULASI CUTI</h1>
                <p>Periode: ' . date('d/m/Y', strtotime($request->get('start_date', date('Y-m-d')))) . ' - ' . date('d/m/Y', strtotime($request->get('end_date', date('Y-m-d')))) . '</p>
                <p>Dibuat pada: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-number">' . $summaryData->count() . '</div>
                    <div class="stat-label">Total Staff</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . $summaryData->sum('total_leave_requests') . '</div>
                    <div class="stat-label">Total Leave Requests</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . $summaryData->sum('total_days') . '</div>
                    <div class="stat-label">Total Days</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . $summaryData->sum('total_hours') . '</div>
                    <div class="stat-label">Total Hours</div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Posisi</th>
                        <th>Divisi</th>
                        <th>Jam Kerja/User Type</th>';

        // Add dynamic leave type columns
        $leaveTypes = DB::table('leave_types')
            ->where('lt_is_active', true)
            ->orderBy('lt_name')
            ->get();

        foreach ($leaveTypes as $leaveType) {
            $html .= '<th class="text-center">' . $leaveType->lt_name . '</th>';
        }

        $html .= '
                        <th class="text-center">Total Leave</th>
                        <th class="text-center">Sisa Cuti Tahunan</th>
                    </tr>
                </thead>
                <tbody>';

        $no = 1;
        foreach ($summaryData as $item) {
            $html .= '
                    <tr>
                        <td class="text-center">' . $no++ . '</td>
                        <td>' . ($item->u_nip ?? '-') . '</td>
                        <td>' . ($item->u_name ?? '-') . '</td>
                        <td>' . ($item->position_name ?? '-') . '</td>
                        <td>' . ($item->division_name ?? '-') . '</td>
                        <td>' . ($item->work_type ?? '-') . '</td>';

            // Add dynamic leave type data
            foreach ($leaveTypes as $leaveType) {
                $leaveValue = $item->{'leave_' . strtolower($leaveType->lt_code)} ?? 0;
                $html .= '<td class="text-center">' . $leaveValue . '</td>';
            }

            $html .= '
                        <td class="text-center">' . ($item->total_days + $item->total_hours) . '</td>
                        <td class="text-center">' . ($item->annual_leave_balance ?? 0) . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }

    /**
     * Show staff detail page for leave requests
     */
    public function staffDetail(Request $request, $userId)
    {
        // $this->validateAccess();

        try {
            \Log::info('Staff Detail Request', [
                'userId' => $userId,
                'request_data' => $request->all()
            ]);

            // Get user information
            $user = DB::table('users as u')
                ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
                ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
                ->leftJoin('user_types as ut', 'u.ut_id', '=', 'ut.id')
                ->select(
                    'u.*',
                    'up.up_name as position_name',
                    'ud.ud_name as division_name',
                    'ut.ut_name as work_type'
                )
                ->where('u.id', $userId)
                ->where('u.u_delete', '!=', '1')
                ->first();

            \Log::info('Staff Detail - User query result', [
                'userId' => $userId,
                'user_found' => !!$user,
                'user_data' => $user
            ]);

            if (!$user) {
                \Log::warning('Staff Detail - User not found', ['userId' => $userId]);
                return redirect()->back()->with('error', 'Staff not found');
            }

            // Get date range from request
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $dateFilter = $request->get('date_filter', 'this_month');

            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }

            // Get divisions for filter
            $divisions = DB::table('user_divisions')
                ->where('ud_status', 'active')
                ->orderBy('ud_name')
                ->get();

            // Get leave types
            $leaveTypes = DB::table('leave_types')
                ->where('lt_is_active', true)
                ->orderBy('lt_name')
                ->get();

            $data = [
                'title' => 'Staff Leave Detail',
                'subtitle' => 'Staff Leave Detail',
                'sidebar' => $this->sidebar(),
                'user' => $user,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'dateFilter' => $dateFilter,
                'divisions' => $divisions,
                'leaveTypes' => $leaveTypes
            ];

            return view('app.leave_request.staff_detail', compact('data'));
        } catch (\Exception $e) {
            \Log::error('Staff Detail Error', [
                'userId' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Error loading staff detail');
        }
    }

    /**
     * Get staff leave requests datatables
     */
    public function staffDatatables(Request $request, $userId)
    {
        if (request()->ajax()) {
            try {
                // $this->validateAccess();

                \Log::info('Staff Datatables Request', [
                    'userId' => $userId,
                    'request_data' => $request->all()
                ]);

                // Get date range
                $startDate = $request->get('start_date', date('Y-m-01'));
                $endDate = $request->get('end_date', date('Y-m-t'));
                $dateFilter = $request->get('date_filter', 'this_month');

                if ($dateFilter && $dateFilter !== 'custom') {
                    $dateRange = $this->getDateRangeFromFilter($dateFilter);
                    $startDate = $dateRange['startDate'];
                    $endDate = $dateRange['endDate'];
                }

                $status = $request->get('status');

                // Get staff leave requests
                $query = DB::table('leave_requests as lr')
                    ->leftJoin('leave_types as lt', 'lr.leave_type_id', '=', 'lt.id')
                    ->leftJoin('users as u', 'lr.user_id', '=', 'u.id')
                    ->select(
                        'lr.id',
                        'lr.lr_start_date as start_date',
                        'lr.lr_end_date as end_date',
                        'lr.lr_total_days as duration',
                        'lr.lr_unit as duration_type',
                        'lr.lr_reason as reason',
                        'lr.lr_status as status',
                        'lr.created_at',
                        'lt.lt_name as leave_type_name',
                        'u.u_name as user_name',
                        'u.u_nip as user_nip'
                    )
                    ->where('lr.user_id', $userId)
                    ->whereBetween('lr.lr_start_date', [$startDate, $endDate]);

                if ($status) {
                    $query->where('lr.lr_status', $status);
                }

                $leaveRequests = $query->orderBy('lr.lr_start_date', 'desc')->get();

                // Convert to array for DataTables
                $data = [];
                foreach ($leaveRequests as $index => $item) {
                    $data[] = [
                        'DT_RowIndex' => $index + 1,
                        'id' => $item->id,
                        'start_date' => $item->start_date,
                        'end_date' => $item->end_date,
                        'duration' => $item->duration,
                        'duration_type' => $item->duration_type,
                        'reason' => $item->reason,
                        'status' => $item->status,
                        'created_at' => $item->created_at,
                        'leave_type_name' => $item->leave_type_name,
                        'user_name' => $item->user_name,
                        'user_nip' => $item->user_nip
                    ];
                }

                // Apply pagination manually
                $totalRecords = count($data);
                $start = $request->get('start', 0);
                $length = $request->get('length', 25);
                $paginatedData = array_slice($data, $start, $length);

                return response()->json([
                    'draw' => $request->get('draw', 1),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $totalRecords,
                    'data' => $paginatedData
                ]);
            } catch (\Exception $e) {
                \Log::error('Staff Datatables Error', [
                    'userId' => $userId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['error' => 'Server error'], 500);
            }
        } else {
            return response()->json(['error' => 'Invalid request'], 400);
        }
    }

    /**
     * Get staff leave statistics
     */
    public function staffStats(Request $request, $userId)
    {
        try {
            // $this->validateAccess();

            // Get date range
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $dateFilter = $request->get('date_filter', 'this_month');

            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }

            // Get status filter from request
            $status = $request->get('status');

            \Log::info('Staff Stats Request', [
                'userId' => $userId,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'dateFilter' => $dateFilter,
                'status' => $status
            ]);

            // First, let's check all leave requests for this user
            $allLeaveRequests = DB::table('leave_requests as lr')
                ->leftJoin('leave_types as lt', 'lr.leave_type_id', '=', 'lt.id')
                ->select('lr.*', 'lt.lt_name')
                ->where('lr.user_id', $userId)
                ->get();

            \Log::info('All Leave Requests for User', [
                'userId' => $userId,
                'total_count' => $allLeaveRequests->count(),
                'requests' => $allLeaveRequests->toArray()
            ]);

            // Now get statistics with filters - use simple approach
            $leaveRequests = DB::table('leave_requests as lr')
                ->leftJoin('leave_types as lt', 'lr.leave_type_id', '=', 'lt.id')
                ->select('lr.*', 'lt.lt_name')
                ->where('lr.user_id', $userId)
                ->whereBetween('lr.lr_start_date', [$startDate, $endDate]);

            // Apply status filter only if provided
            if ($status) {
                $leaveRequests->where('lr.lr_status', $status);
            }

            $allRequests = $leaveRequests->get();

            // Group and calculate manually
            $statsArray = [];
            foreach ($allRequests as $request) {
                $leaveTypeName = $request->lt_name;

                if (!isset($statsArray[$leaveTypeName])) {
                    $statsArray[$leaveTypeName] = [
                        'leave_type_name' => $leaveTypeName,
                        'total_requests' => 0,
                        'total_days' => 0,
                        'total_hours' => 0
                    ];
                }

                $statsArray[$leaveTypeName]['total_requests']++;

                // Try to get duration - check what columns exist
                if (property_exists($request, 'lr_total_days') && $request->lr_total_days) {
                    $statsArray[$leaveTypeName]['total_days'] += $request->lr_total_days;
                }
                if (property_exists($request, 'lr_total_hours') && $request->lr_total_hours) {
                    $statsArray[$leaveTypeName]['total_hours'] += $request->lr_total_hours;
                }
                // Fallback - if no specific duration columns, use a generic approach
                if (property_exists($request, 'lr_duration') && $request->lr_duration) {
                    $statsArray[$leaveTypeName]['total_days'] += $request->lr_duration;
                }
            }

            $stats = collect(array_values($statsArray));

            \Log::info('Filtered Stats Result', [
                'userId' => $userId,
                'stats_count' => $stats->count(),
                'stats' => $stats->toArray()
            ]);

            // Let's also try without status filter to see if that's the issue
            $statsWithoutStatus = DB::table('leave_requests as lr')
                ->leftJoin('leave_types as lt', 'lr.leave_type_id', '=', 'lt.id')
                ->select(
                    'lt.lt_name as leave_type_name',
                    'lr.lr_status as status',
                    DB::raw('COUNT(*) as total_requests')
                )
                ->where('lr.user_id', $userId)
                ->whereBetween('lr.lr_start_date', [$startDate, $endDate])
                ->groupBy('lt.id', 'lt.lt_name', 'lr.lr_status')
                ->orderBy('lt.lt_name')
                ->get();

            \Log::info('Stats Without Status Filter', [
                'userId' => $userId,
                'stats_without_status_count' => $statsWithoutStatus->count(),
                'stats_without_status' => $statsWithoutStatus->toArray()
            ]);

            return response()->json($stats);
        } catch (\Exception $e) {
            \Log::error('Staff Stats Error', [
                'userId' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Export staff leave requests to Excel
     */
    public function exportStaffToExcel(Request $request, $userId)
    {
        // $this->validateAccess();

        try {
            // Get date range
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $dateFilter = $request->get('date_filter', 'this_month');

            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }

            $status = $request->get('status');

            // Get staff leave requests
            $query = DB::table('leave_requests as lr')
                ->leftJoin('leave_types as lt', 'lr.leave_type_id', '=', 'lt.id')
                ->leftJoin('users as u', 'lr.user_id', '=', 'u.id')
                ->select(
                    'lr.lr_start_date as start_date',
                    'lr.lr_end_date as end_date',
                    'lr.lr_total_days as duration',
                    'lr.lr_unit as duration_type',
                    'lr.lr_reason as reason',
                    'lr.lr_status as status',
                    'lr.created_at',
                    'lt.lt_name as leave_type_name'
                )
                ->where('lr.user_id', $userId)
                ->whereBetween('lr.lr_start_date', [$startDate, $endDate]);

            if ($status) {
                $query->where('lr.lr_status', $status);
            }

            $leaveRequests = $query->orderBy('lr.lr_start_date', 'desc')->get();

            \Log::info('Export Staff Excel - Data retrieved', [
                'data_count' => $leaveRequests->count(),
                'first_item' => $leaveRequests->first()
            ]);

            // Get user info for filename
            $user = DB::table('users as u')->where('u.id', $userId)->first();
            $filename = 'leave_requests_' . ($user->u_nip ?? $userId) . '_' . date('Y-m-d') . '.xlsx';

            \Log::info('Export Staff Excel - Creating export', [
                'filename' => $filename,
                'export_class' => 'LeaveRequestExport'
            ]);

            // Create export instance
            $export = new LeaveRequestExport($leaveRequests);

            \Log::info('Export Staff Excel - Export instance created successfully');

            // Use simple Excel download like summary report
            return Excel::download($export, $filename);
        } catch (\Exception $e) {
            \Log::error('Export Staff Excel Error', [
                'userId' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Error exporting data');
        }
    }

    /**
     * Export staff leave requests to PDF
     */
    public function exportStaffToPDF(Request $request, $userId)
    {
        // $this->validateAccess();

        try {
            // Get date range
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $dateFilter = $request->get('date_filter', 'this_month');

            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }

            $status = $request->get('status');

            // Get staff leave requests
            $query = DB::table('leave_requests as lr')
                ->leftJoin('leave_types as lt', 'lr.leave_type_id', '=', 'lt.id')
                ->leftJoin('users as u', 'lr.user_id', '=', 'u.id')
                ->select(
                    'lr.lr_start_date as start_date',
                    'lr.lr_end_date as end_date',
                    'lr.lr_total_days as duration',
                    'lr.lr_unit as duration_type',
                    'lr.lr_reason as reason',
                    'lr.lr_status as status',
                    'lr.created_at',
                    'lt.lt_name as leave_type_name'
                )
                ->where('lr.user_id', $userId)
                ->whereBetween('lr.lr_start_date', [$startDate, $endDate]);

            if ($status) {
                $query->where('lr.lr_status', $status);
            }

            $leaveRequests = $query->orderBy('lr.lr_start_date', 'desc')->get();

            // Get user info for filename
            $user = DB::table('users as u')->where('u.id', $userId)->first();
            $filename = 'leave_requests_' . ($user->u_nip ?? $userId) . '_' . date('Y-m-d') . '.pdf';

            // Generate HTML
            $html = $this->generateStaffReportHTML($leaveRequests, $request, $user);

            // Generate PDF
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Export Staff PDF Error', [
                'userId' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Error exporting data');
        }
    }

    /**
     * Generate staff report HTML for PDF
     */
    private function generateStaffReportHTML($leaveRequests, $request, $user)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Staff Leave Report</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h1 { color: #2E75B6; margin-bottom: 5px; }
                .user-info { margin-bottom: 20px; }
                .user-info table { width: 100%; border-collapse: collapse; }
                .user-info td { padding: 5px; border: 1px solid #ddd; }
                .user-info td:first-child { font-weight: bold; width: 150px; background-color: #f5f5f5; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
                th { background-color: #4472C4; color: white; font-weight: bold; }
                .text-center { text-align: center; }
                .status-approved { color: #28a745; font-weight: bold; }
                .status-pending { color: #ffc107; font-weight: bold; }
                .status-rejected { color: #dc3545; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN LEAVE STAFF</h1>
                <p>Periode: ' . date('d/m/Y', strtotime($request->get('start_date', date('Y-m-d')))) . ' - ' . date('d/m/Y', strtotime($request->get('end_date', date('Y-m-d')))) . '</p>
                <p>Dibuat pada: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            
            <div class="user-info">
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>' . ($user->u_name ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>NIP</td>
                        <td>' . ($user->u_nip ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>' . ($user->u_email ?? '-') . '</td>
                    </tr>
                </table>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Durasi</th>
                        <th>Jenis Leave</th>
                        <th>Status</th>
                        <th>Alasan</th>
                        <th>Tanggal Request</th>
                    </tr>
                </thead>
                <tbody>';

        if ($leaveRequests->count() > 0) {
            $no = 1;
            foreach ($leaveRequests as $item) {
                $statusClass = 'status-' . strtolower($item->status);
                $html .= '
                    <tr>
                        <td class="text-center">' . $no++ . '</td>
                        <td>' . date('d/m/Y', strtotime($item->start_date)) . '</td>
                        <td>' . date('d/m/Y', strtotime($item->end_date)) . '</td>
                        <td class="text-center">' . $item->duration . ' hari</td>
                        <td>' . ($item->leave_type_name ?? '-') . '</td>
                        <td class="' . $statusClass . '">' . ucfirst($item->status) . '</td>
                        <td>' . ($item->reason ?? '-') . '</td>
                        <td>' . date('d/m/Y H:i', strtotime($item->created_at)) . '</td>
                    </tr>';
            }
        } else {
            $html .= '
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data leave request</td>
                </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }

    /**
     * Send notification to supervisors and managers when a leave request is submitted
     */
    private function sendLeaveRequestNotification($userId, $leaveData)
    {
        try {
            // Get the requesting user details
            $requestingUser = User::find($userId);
            if (!$requestingUser) {
                return;
            }

            // Get leave type name
            $leaveType = DB::table('leave_types')->where('id', $leaveData['leave_type_id'])->first();
            $leaveTypeName = $leaveType ? $leaveType->lt_name : 'Leave';

            // Find users who can approve leave requests in the same division
            $approvers = User::where('ud_id', $requestingUser->ud_id)
                ->whereHas('userPosition', function ($query) {
                    $query->where('up_level', '>=', 2) // Supervisor level or higher
                    ->where('up_can_approve_leave', true);
                })
                ->where('id', '!=', $userId) // Don't notify the requester
                ->get();

            foreach ($approvers as $approver) {
                $message = "New leave request from {$requestingUser->u_name} ({$requestingUser->u_nip}) for {$leaveTypeName} from {$leaveData['lr_start_date']} to {$leaveData['lr_end_date']}";

                $notificationData = [
                    'leave_request_id' => null, // Will be set when we have the actual ID
                    'requester_name' => $requestingUser->u_name,
                    'requester_nip' => $requestingUser->u_nip,
                    'leave_type' => $leaveTypeName,
                    'start_date' => $leaveData['lr_start_date'],
                    'end_date' => $leaveData['lr_end_date'],
                    'reason' => $leaveData['lr_reason']
                ];

                Notification::createHRNotification(
                    $approver->ud_id,
                    $approver->id,
                    $message,
                    'leave_request',
                    $notificationData
                );
            }

            \Log::info('Leave request notifications sent', [
                'requester_id' => $userId,
                'approvers_count' => $approvers->count(),
                'division_id' => $requestingUser->ud_id
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send leave request notifications', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification when leave request status changes
     */
    private function sendLeaveStatusChangeNotification($leaveRequestId, $newStatus, $approverName)
    {
        try {
            $leaveRequest = LeaveRequest::find($leaveRequestId);
            if (!$leaveRequest) {
                return;
            }

            $requestingUser = $leaveRequest->user;
            if (!$requestingUser) {
                return;
            }

            $statusText = $newStatus === 'approved' ? 'approved' : 'rejected';
            $message = "Your leave request has been {$statusText} by {$approverName}";

            $notificationData = [
                'leave_request_id' => $leaveRequestId,
                'status' => $newStatus,
                'approver_name' => $approverName,
                'start_date' => $leaveRequest->lr_start_date,
                'end_date' => $leaveRequest->lr_end_date
            ];

            Notification::createHRNotification(
                $requestingUser->ud_id,
                $requestingUser->id,
                $message,
                'leave_status_change',
                $notificationData
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send leave status change notification', [
                'leave_request_id' => $leaveRequestId,
                'new_status' => $newStatus,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Format file size to human readable format
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);

        $comment = LeaveRequestComment::create([
            'lr_id' => $id,
            'user_id' => auth()->id(),
            'comment' => $request->comment
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $comment->user->u_name,
                'datetime' => $comment->created_at->format('d/m/Y H:i'),
                'comment' => $comment->comment
            ]
        ]);
    }

    /**
     * Get leave request stats for AJAX request
     */
    public function getStats(Request $request)
    {
        // $this->validateAccess();

        // Get filters
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $dateFilter = $request->get('date_filter');
        $userId = $request->get('user_id');
        $status = $request->get('status');
        $leaveTypeId = $request->get('leave_type_id');

        // Apply date filter if not custom
        if ($dateFilter && $dateFilter !== 'custom') {
            $dateRange = $this->getDateRangeFromFilter($dateFilter);
            $startDate = $dateRange['startDate'];
            $endDate = $dateRange['endDate'];
        }

        $leaveRequest = new LeaveRequest();
        $leaveRequests = $leaveRequest->getLeaveRequestsByFilters($startDate, $endDate, $userId, $status, $leaveTypeId);

        $stats = [
            'total' => $leaveRequests->count(),
            'pending' => $leaveRequests->where('lr_status', 'pending')->count(),
            'approved' => $leaveRequests->where('lr_status', 'approved')->count(),
            'rejected' => $leaveRequests->where('lr_status', 'rejected')->count(),
        ];

        return response()->json($stats);
    }
}
