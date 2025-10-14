<?php

namespace App\Http\Controllers;

use App\Models\ExternalAssignmentRequest;
use App\Models\LeaveType;
use App\Models\OvertimeRequest;
use App\Models\OvertimeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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


        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];

        return view('app.overtime.index', compact('data'));
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
                'attachment' => 'nullable|file|max:2048', // max 2MB
                'claim' => 'required',
            ]);

            // handle file upload
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
                'details' => $validated['details'],
                'attachment' => $attachmentPath,
                'ot_id' => $validated['claim'] ?? null,
                'status' => 'Pending',
                'request_by' => auth()->id(),
                'approved_by' => null,
                'approved_at' => null,
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
                'o.attachment',
                'o.approved_by',
                'o.approved_at',
                'u.u_name as request_by_name',
                'o.created_at'
            )
            ->orderByDesc('o.id');

        return DataTables::of($data)
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
                return '<span class="text-muted">Pending</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                <a href="'.route('overtime.show', $row->id).'" class="btn btn-sm btn-info">View</a>
            ';
            })
            ->rawColumns(['approved_info', 'action', 'assigned_staff'])
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

        $data =[
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'title' => 'Overtime Request',
            'user' => $user_data
        ];

        // kirim ke view
        return view('app.overtime.show', compact('detail', 'data', 'isManager', 'isHR'));
    }

    public function approve($id)
    {
        $userId = Auth::id();

        DB::table('overtime_requests')
            ->where('id', $id)
            ->update([
                'approved_by' => $userId,
                'approved_at' => now(),
                'status' => 'Approved'
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
}