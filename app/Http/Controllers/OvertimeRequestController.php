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
                return '
                <a href="' . route('overtime.show', $row->id) . '" class="btn btn-sm btn-info">
                    <i class="fa fa-eye"></i> View
                </a>
            ';
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

        $approvalLogs = [];
        if (!empty($row->approved_by)) {
            $approvalLogs[] = [
                'role' => 'Approver',
                'name' => $detail->approver_name,
                'date' => $row->approved_at ? date('d M Y H:i', strtotime($row->approved_at)) : null,
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

    public function exportToExcel (Request $request) {
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
}
