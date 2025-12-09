<?php

namespace App\Http\Controllers;

use App\Models\ExternalAssignmentRequest;
use App\Models\LeaveType;
use App\Models\OvertimeRequest;
use App\Models\OvertimeType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;
use Yajra\DataTables\Facades\DataTables;

class OvertimeRequestController extends Controller
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

        $summary = [
            'total'     => OvertimeRequest::count(),
            'pending'   => OvertimeRequest::where('status', 'Pending')->count(),
            'approved'  => OvertimeRequest::where('status', 'Approved')->count(),
            'hr_check'  => OvertimeRequest::where('status', 'HR Check')->count(),
            'done'      => OvertimeRequest::where('status', 'Done')->count(),
        ];


        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'divisions' => DB::table('user_divisions')->orderBy('ud_name')->get(),
            'segment' => request()->segment(1),
        ];

        return view('app.overtime.index', compact('data', 'summary'));
    }

    public function create()
    {
        $title = 'Overtime Requests';
        $user = auth()->user();

        // ambil data user dan departemen
        $user_data = DB::table('users')
            ->join('user_divisions', 'users.ud_id', '=', 'user_divisions.id')
            ->where('users.id', $user->id)
            ->select('users.*', 'user_divisions.ud_name', 'user_divisions.id as ud_id')
            ->first();

        // ambil staff lain di departemen yang sama
        $staff = DB::table('users')
            ->where('ud_id', $user_data->ud_id)
            ->pluck('u_name', 'id');

        $overtime_types = DB::table('overtime_types')
            ->pluck('ot_name', 'id');

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')
                ->where('ma_slug', '=', request()->segment(1))
                ->value('ma_title'),
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];

        return view('app.overtime.create', compact('staff', 'data', 'overtime_types'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'submission_date' => 'required|date',
                'department' => 'required|integer',
                'assigned_staff' => 'required|array|min:1',
                'start_date' => 'required|date',
                'start_time' => 'required',
                'end_date' => 'required|date',
                'end_time' => 'required',
                'details' => 'required|string',
                'attachment' => 'nullable|file|max:2048',
                'claim' => 'required',
            ]);

            // Combine date & time
            $startDateTime = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
            $endDateTime   = Carbon::parse($validated['end_date'] . ' ' . $validated['end_time']);

            // Validate end > start
            if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal & jam selesai harus lebih besar dari tanggal & jam mulai'
                ], 422);
            }

            // File upload
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('attachments/overtime', 'public');
            }

            DB::table('overtime_requests')->insert([
                'submission_date' => $validated['submission_date'],
                'ud_id' => $validated['department'],
                'assigned_staff' => json_encode($validated['assigned_staff']),
                'start_date' => $validated['start_date'],
                'start_time' => $validated['start_time'],
                'end_date' => $validated['end_date'],
                'end_time' => $validated['end_time'],
                'details' => trim($validated['details']),
                'attachment' => $attachmentPath,
                'ot_id' => $validated['claim'] ?? null,
                'status' => 'Pending',
                'request_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Overtime request submitted successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    // ⚡ Server-side DataTables
    public function getData(Request $request)
    {
        $data = DB::table('overtime_requests as o')
            ->leftJoin('users as u', 'o.request_by', '=', 'u.id')
            ->leftJoin('user_divisions as d', 'o.ud_id', '=', 'd.id')
            ->leftJoin('overtime_types as ot', 'o.ot_id', '=', 'ot.id')
            ->select(
                'o.id',
                'o.submission_date',
                'd.ud_name as department_name',
                'o.assigned_staff',
                'o.start_date',
                'o.start_time',
                'o.end_date',
                'o.end_time',
                'ot.ot_name as claim',
                'o.status',
                'o.attachment',
                'o.approved_by',
                'o.approved_at',
                'u.u_name as request_by_name',
                'o.created_at'
            )
            ->orderByDesc('o.id');

        // =============================
        // 🔍 Filter Section
        // =============================
        if ($request->status) {
            $data->where('o.status', $request->status);
        }

        if ($request->start_date) {
            $data->whereDate('o.start_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $data->whereDate('o.end_date', '<=', $request->end_date);
        }
        if ($request->division) {
            $data->where('o.ud_id', $request->division);
        }

        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                if ($request->has('staff') && !empty($request->staff)) {
                    $staffName = $request->staff;
                    $query->where(function ($q) use ($staffName) {
                        $q->whereRaw("EXISTS (
                        SELECT 1 FROM ts_users u
                        WHERE JSON_CONTAINS(ts_o.assigned_staff, CONCAT('\"', u.id, '\"'))
                        AND u.u_name LIKE ?
                    )", ["%{$staffName}%"]);
                    });
                }
            })
            ->addIndexColumn()
            ->addColumn('assigned_staff', function ($row) {
                $staffIds = json_decode($row->assigned_staff, true) ?? [];

                if (empty($staffIds)) {
                    return json_encode([]);
                }

                // Ambil nama staff berdasarkan ID
                $staffNames = DB::table('users')
                    ->whereIn('id', $staffIds)
                    ->pluck('u_name')
                    ->toArray();

                // kirim dalam bentuk JSON string ke front-end
                return json_encode($staffNames);
            })
            ->addColumn('start', function ($row) {
                return $row->start_date . ' ' . $row->start_time;
            })
            ->addColumn('end', function ($row) {
                return $row->end_date . ' ' . $row->end_time;
            })
            ->addColumn('approved_info', function ($row) {
                if ($row->approved_by) {
                    $approver = DB::table('users')->where('id', $row->approved_by)->value('u_name');
                    $approvedAt = $row->approved_at ? date('d M Y H:i', strtotime($row->approved_at)) : '-';
                    return "$approver<br><small>$approvedAt</small>";
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('status', function ($row) {
                switch ($row->status) {
                    case 'Pending':
                        $badge = '<span class="badge bg-secondary">Pending</span>';
                        break;
                    case 'Approved':
                        $badge = '<span class="badge bg-success">Approved</span>';
                        break;
                    case 'Rejected':
                        $badge = '<span class="badge bg-danger">Rejected</span>';
                        break;
                    case 'HR Check':
                        $badge = '<span class="badge bg-warning text-dark">HR Check</span>';
                        break;
                    case 'Done':
                        $badge = '<span class="badge success">Done</span>';
                        break;
                    default:
                        $badge = '<span class="badge bg-light text-dark">-</span>';
                        break;
                }

                return $badge;
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="dropdown">';
                $btn .= '    <!--begin::Toggle-->';
                $btn .= '    <button type="button" class="btn btn-sm btn-light btn-active-light-primary" 
                        data-kt-menu-trigger="click" 
                        data-kt-menu-placement="bottom-start">
                    Actions
                </button>';
                $btn .= '    <!--end::Toggle-->';

                $btn .= '    <!--begin::Menu-->';
                $btn .= '    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded 
                        menu-gray-800 menu-state-bg-light-primary fw-semibold w-auto min-w-150px" 
                        data-kt-menu="true">';

                // VIEW
                $btn .= '        <div class="menu-item px-3">
                        <a href="' . route('overtime.show', $row->id) . '" class="menu-link px-3">
                            View
                        </a>
                    </div>';

                // COPY LINK
                $btn .= '        <div class="menu-item px-3">
                        <a href="javascript:void(0)" onclick="copyOvertimeLink(' . $row->id . ')" 
                            class="menu-link px-3">
                            Copy Link
                        </a>
                    </div>';

                $btn .= '    </div>';
                $btn .= '    <!--end::Menu-->';

                $btn .= '</div>';

                return $btn;
            })
            ->rawColumns(['approved_info', 'action', 'assigned_staff', 'status'])
            ->make(true);
    }

    public function show($id)
    {
        // ambil row overtime + requester + department
        $row = DB::table('overtime_requests as o')
            ->leftJoin('users as requester', 'o.request_by', '=', 'requester.id')
            ->leftJoin('user_divisions as d', 'o.ud_id', '=', 'd.id')
            ->leftJoin('overtime_types as ot', 'o.ot_id', '=', 'ot.id')
            ->select(
                'o.*',
                'requester.u_name as request_by_name',
                'd.ud_name as department_name',
                'ot.ot_name as claim',
            )
            ->where('o.id', $id)
            ->first();

        if (! $row) {
            abort(404, 'Overtime request not found');
        }

        $staffIds = json_decode($row->assigned_staff, true) ?? [];
        $staffNames = [];
        if (!empty($staffIds)) {
            $staffNames = DB::table('users')
                ->whereIn('id', $staffIds)
                ->pluck('u_name')
                ->toArray();
        }

        $detail = (object) $row;
        $detail->assigned_staff = $staffNames;
        $detail->start = ($row->start_date ?? '') . ' ' . ($row->start_time ?? '');
        $detail->end   = ($row->end_date ?? '') . ' ' . ($row->end_time ?? '');

        // tambahan: jika butuh nama approver
        $detail->approver_name = null;
        if (!empty($row->approved_by)) {
            $detail->approver_name = DB::table('users')->where('id', $row->approved_by)->value('u_name');
        }

        // tambahan: jika butuh nama HR checker
        $detail->hr_checked_name = null;
        if (!empty($row->hr_checked_by)) {
            $detail->hr_checked_name = DB::table('users')->where('id', $row->hr_checked_by)->value('u_name');
        }

        $approvalLogs = [];
        if (!empty($row->approved_by)) {
            $approvalLogs[] = [
            'role' => 'Approver',
            'name' => $detail->approver_name,
            'date' => $row->approved_at ? date('d M Y H:i', strtotime($row->approved_at)) : null,
            'note' => null
            ];
        }

        // tambahkan log HR check jika ada
        if (!empty($row->hr_checked_by)) {
            $approvalLogs[] = [
            'role' => 'HR',
            'name' => $detail->hr_checked_name,
            'date' => $row->hr_checked_at ? date('d M Y H:i', strtotime($row->hr_checked_at)) : null,
            'note' => null
            ];
        }

        $detail->approval_logs = $approvalLogs;

        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $userId = Auth::id();
        $isManager = DB::table('users as u')
            ->leftJoin('user_positions as p', 'u.up_id', '=', 'p.id')
            ->where('u.id', $userId)
            ->where('p.up_name', 'MANAGER')
            ->exists();

        // Cek apakah user HR
        $isHR = false;
        if ($user && $user->ud_id) {
            $isHR = \DB::table('user_divisions')
                ->where('id', $user->ud_id)
                ->where('ud_code', 'HUMANRESOU')
                ->exists();
        }

        $data = [
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'title' => 'Overtime Request',
            'user' => $user_data
        ];

        // kirim ke view
        return view('app.overtime.show', compact('detail', 'data', 'isManager', 'isHR'));
    }

    public function approve(Request $request, $id)
    {
        $userId = Auth::id();

        DB::table('overtime_requests')
            ->where('id', $id)
            ->update([
                'approved_by' => $userId,
                'approved_at' => now(),
                'status' => $request->action
            ]);

        return response()->json(['success' => true]);
    }


    public function reportSubmit(Request $request, $id)
    {
        try {
            $request->validate([
                'report_desc' => 'nullable|string',
                'report_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xlsx|max:5120', // max 5MB
            ]);

            $overtime = OvertimeRequest::findOrFail($id);

            $filePath = $overtime->report_attachment;
            if ($request->hasFile('report_attachment')) {
                $file = $request->file('report_attachment');

                $directory = storage_path('app/public/overtime_reports');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $fileName = 'report_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('overtime_reports', $fileName, 'public');

                // Simpan path ke database
                $data['report_attachment'] = $filePath;
            }

            $overtime->update([
                'report_desc' => $request->report_desc,
                'report_attachment' => $filePath,
                'status' => 'HR Check',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report berhasil disimpan dan dikirim ke HR.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error reportSubmit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan report.',
            ], 500);
        }
    }

    public function approveHr($id)
    {
        try {
            $overtime = OvertimeRequest::findOrFail($id);

            //            // Pastikan hanya HR yang bisa approve
            //            $user = auth()->user();
            //            $isHR = $user->userDivision && $user->userDivision->ud_code === 'HUMANRESRC';

            //            if (!$isHR) {
            //                return response()->json([
            //                    'success' => false,
            //                    'message' => 'Anda tidak memiliki izin untuk approve HR.'
            //                ]);
            //            }

            // Update status dan kolom HR approval
            $overtime->update([
                'status' => 'Done',
                'hr_checked_by' => Auth::user()->id,
                'hr_checked_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Overtime berhasil disetujui oleh HR.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    public function exportToExcel(Request $request)
    {
        // Implementasi export ke Excel
        $filter_status = $request->input('status');
        $filter_division = $request->input('division');
        $filter_start_date = $request->input('start_date');
        $filter_end_date = $request->input('end_date');
        $filter_staff = $request->input('staff');


        $overtimeExport = new \App\Exports\OvertimeRequestsExport(
            $filter_status,
            $filter_division,
            $filter_start_date,
            $filter_end_date,
            $filter_staff
        );

        return \Maatwebsite\Excel\Facades\Excel::download($overtimeExport, 'overtime_requests.xlsx');
    }

    public function summaryReport(Request $request)
    {
        $this->validateAccess();

        $title = 'Overtime Summary Report';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $summary = [
            'total'     => OvertimeRequest::count(),
            'pending'   => OvertimeRequest::where('status', 'Pending')->count(),
            'approved'  => OvertimeRequest::where('status', 'Approved')->count(),
            'hr_check'  => OvertimeRequest::where('status', 'HR Check')->count(),
            'done'      => OvertimeRequest::where('status', 'Done')->count(),
        ];


        // Get date range from request or default to current month
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));
        $dateFilter = $request->get('date_filter', 'this_month');

        if ($dateFilter && $dateFilter !== 'custom') {
            $dateRange = $this->getDateRangeFromFilter($dateFilter);
            $startDate = $dateRange['startDate'];
            $endDate = $dateRange['endDate'];
        }


        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'divisions' => DB::table('user_divisions')->orderBy('ud_name')->get(),
            'segment' => request()->segment(1),
            'overtime_types' => OvertimeType::orderBy('ot_name')->get(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateFilter' => $dateFilter,
        ];

        return view('app.overtime.summary_report', compact('data', 'summary'));
    }

    public function getOvertimeSummaryDatatables(Request $request)
    {
        if (request()->ajax()) {
            try {
                \Log::info('Overtime Summary Report Datatables Request', [
                    'request_data' => $request->all()
                ]);

                // Get date range
                $startDate = $request->get('start_date', date('Y-m-01'));
                $endDate = $request->get('end_date', date('Y-m-t'));

                \Log::info('Overtime Summary Report - Initial date values', [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                ]);

                $divisionId = $request->get('division');
                $staffName = $request->get('staff');

                \Log::info('Overtime Summary Report - Getting data', [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'divisionId' => $divisionId,
                    'staffName' => $staffName
                ]);

                // Use the same method to get data
                $summaryData = $this->getOvertimeSummary($startDate, $endDate, $divisionId, $staffName);


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
                \Log::error('Overtime Summary Report Datatables Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['error' => 'Server error'], 500);
            }
        } else {
            return response()->json(['error' => 'Invalid request'], 400);
        }
    }

    public function exportSummaryToExcel(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));
        $divisionId = $request->get('division');
        $staffName = $request->get('staff');

        $summaryData = $this->getOvertimeSummary($startDate, $endDate, $divisionId, $staffName);

        $overtimeSummaryExport = new \App\Exports\OvertimeSummaryExport($summaryData);

        return \Maatwebsite\Excel\Facades\Excel::download($overtimeSummaryExport, 'overtime_summary_report.xlsx');
    }

    private function getOvertimeSummary($startDate = null, $endDate = null, $divisionId = null, $staffName = null)
    {
        if (!$startDate) {
            $startDate = date('2025-01-01');
        }
        if (!$endDate) {
            $endDate = date('Y-m-t');
        }
        try {
            // Get all active users with NIP only
            $usersQuery = DB::table('users as u')
                ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
                ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
                ->leftJoin('user_types as ut', 'u.ut_id', '=', 'ut.id')
                ->where('u.u_delete', '!=', '1')
                ->whereNotNull('u.u_nip')
                ->where('u.u_nip', '!=', '')
                ->where('u.u_name', 'like', '%' . $staffName . '%')
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

            if ($staffName) {
                $users = $users->filter(function ($user) use ($staffName) {
                    return stripos($user->u_name, $staffName) !== false;
                })->values();
            }
            // Get overtime types for dynamic columns
            $overtimeTypes = DB::table('overtime_types')
                ->orderBy('ot_name')
                ->get();

            // Process each user to add overtime data
            foreach ($users as $user) {
                // Get overtime requests for this user in date range
                $overtimeRequests = DB::table('overtime_requests as o')
                    ->leftJoin('overtime_types as ot', 'o.ot_id', '=', 'ot.id')
                    ->where('o.status', 'Done')
                    ->whereBetween('o.start_date', [$startDate, $endDate])
                    ->whereRaw("JSON_CONTAINS(ts_o.assigned_staff, ?)", [json_encode((string)$user->user_id)])
                    ->select([
                        'ot.ot_name',
                        'o.start_date',
                        'o.start_time',
                        'o.end_date',
                        'o.end_time'
                    ])
                    ->get();

                // Calculate totals
                $user->total_overtime_requests = $overtimeRequests->count();
                $totalHours = 0;

                foreach ($overtimeRequests as $overtime) {
                    $startDateTime = Carbon::parse($overtime->start_date . ' ' . $overtime->start_time);
                    $endDateTime = Carbon::parse($overtime->end_date . ' ' . $overtime->end_time);
                    $totalHours += $endDateTime->diffInHours($startDateTime, true);
                }

                $user->total_hours = round($totalHours, 2);

                // Add overtime type specific data
                foreach ($overtimeTypes as $overtimeType) {
                    $overtimeData = $overtimeRequests->where('ot_name', $overtimeType->ot_name);
                    $typeHours = 0;

                    foreach ($overtimeData as $overtime) {
                        $startDateTime = Carbon::parse($overtime->start_date . ' ' . $overtime->start_time);
                        $endDateTime = Carbon::parse($overtime->end_date . ' ' . $overtime->end_time);
                        $typeHours += $endDateTime->diffInHours($startDateTime, true);
                    }

                    $columnName = 'overtime_type_' . $overtimeType->id;
                    
                    // Check if this is "Uang Tunai" overtime type
                    // Adjust the condition based on your overtime type identifier
                    if (stripos($overtimeType->ot_name, 'Uang Tunai') !== false) {
                        // Define overtime fee rates
                        $overtimeFeeRates = [
                            3 => 50000,
                            5 => 100000,
                            9 => 150000,
                        ];
                        
                        $fee = 0;
                        
                        if ($typeHours < 3) {
                            $fee = 0; // Under 3 hours, no payment
                        } elseif ($typeHours < 5) {
                            $fee = $overtimeFeeRates[3]; // Use 3 hour rate
                        } elseif ($typeHours < 9) {
                            $fee = $overtimeFeeRates[5]; // Use 5 hour rate
                        } else {
                            $fee = $overtimeFeeRates[9]; // Use 9 hour rate
                        }
                        
                        $user->{$columnName} = round($typeHours, 2);
                        $user->overtime_fee = ($user->overtime_fee ?? 0) + $fee;
                    } else {
                        // For other overtime types, just store the hours
                        $user->{$columnName} = round($typeHours, 2);
                    }
                }
                
            }

            return $users;
        } catch (\Exception $e) {
            \Log::error('Error in getOvertimeSummary: ' . $e->getMessage(), [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'divisionId' => $divisionId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

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
}
