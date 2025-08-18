<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Attendance;
use App\Models\UserDivision;
use App\Models\DailySchedule;
use App\Exports\AttendanceExport;
use App\Exports\StaffAttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AttendanceController extends Controller
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
        ->where('u_id', Auth::user()->id)->get();
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
        
        $title = 'Attendance';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();
        
        // Handle date filter
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $dateFilter = $request->get('date_filter', 'this_week');
        
        // If date filter is provided, calculate dates
        if ($dateFilter && $dateFilter !== 'custom') {
            $dateRange = $this->getDateRangeFromFilter($dateFilter);
            $startDate = $dateRange['startDate'];
            $endDate = $dateRange['endDate'];
        }
        
        $attendance = new Attendance();
        $attendances = $attendance->getAttendanceByDateRange(
            $startDate,
            $endDate,
            $request->get('user_id'),
            $request->get('division_id'),
            $request->get('status')
        );

        $users = DB::table('users')->where('u_delete', '!=', '1')->get();
        $divisions = DB::table('user_divisions')->where('ud_status', 'active')->get();
        $leaveTypes = DB::table('leave_types')->where('lt_is_active', 1)->get();

        $stats = $attendance->getAttendanceStats(
            $startDate,
            $endDate,
            $request->get('user_id')
        );

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title ?? 'Attendance',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateFilter' => $dateFilter
        ];

        if ($request->ajax() && $request->has('ajax_stats')) {
            return response()->json(['stats' => $stats]);
        }

        return view('app.attendance.index', compact('attendances', 'users', 'divisions', 'leaveTypes', 'stats', 'data'));
    }

    public function create()
    {
        $this->validateAccess();
        
        $title = 'Create Attendance';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();
        
        $users = DB::table('users')->where('u_delete', '!=', '1')->get();
        $dailySchedules = DB::table('daily_schedules')->get();
        
        $data = [
            'title' => $title,
            'subtitle' => 'Create New Attendance',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.attendance.create', compact('users', 'dailySchedules', 'data'));
    }

    public function store(Request $request)
    {
        $this->validateAccess();
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'at_date' => 'required|date',
            'at_time_in' => 'nullable|date_format:H:i',
            'at_time_out' => 'nullable|date_format:H:i|after:at_time_in',
            'at_status' => 'required|in:present,late,absent,early_leave,scan_once',
            'at_notes' => 'nullable|string|max:500',
            'daily_schedule_id' => 'nullable|exists:daily_schedules,id'
        ]);

        try {
            $attendance = new Attendance();
            $data = [
                'user_id' => $request->user_id,
                'at_date' => $request->at_date,
                'at_time_in' => $request->at_time_in,
                'at_time_out' => $request->at_time_out,
                'at_status' => $request->at_status,
                'at_notes' => $request->at_notes,
                'daily_schedule_id' => $request->daily_schedule_id,
                'at_source' => 'manual',
                'created_by' => Auth::user()->id
            ];

            $result = $attendance->storeData('add', null, $data);
            
            if ($result) {
                return redirect()->route('attendance.index')->with('success', 'Attendance created successfully');
            } else {
                return back()->with('error', 'Failed to create attendance')->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function upload()
    {
        $this->validateAccess();
        
        $title = 'Upload Attendance';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();
        
        $data = [
            'title' => $title,
            'subtitle' => 'Upload Attendance Data',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.attendance.upload', compact('data'));
    }

    public function processUpload(Request $request)
    {
        $this->validateAccess();
        
        \Log::info('Attendance upload process started', [
            'request_data' => $request->all(),
            'files' => $request->allFiles()
        ]);
        
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $file = $request->file('excel_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            \Log::info('File upload details', [
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension()
            ]);
            
            $file->move(storage_path('app/public/uploads'), $fileName);
            
            // Process the file based on type
            $extension = $file->getClientOriginalExtension();
            $data = [];
            
            if (in_array($extension, ['xlsx', 'xls'])) {
                $data = Excel::toArray([], storage_path('app/public/uploads/' . $fileName))[0];
                \Log::info('Excel file processed', ['rows_count' => count($data)]);
                
                // Log first few rows for debugging
                if (count($data) > 0) {
                    \Log::info('First row (header):', ['header' => $data[0]]);
                    if (count($data) > 1) {
                        \Log::info('Second row (sample data):', ['sample_data' => $data[1]]);
                    }
                }
            } else {
                // Handle CSV
                $handle = fopen(storage_path('app/public/uploads/' . $fileName), 'r');
                while (($row = fgetcsv($handle)) !== false) {
                    $data[] = $row;
                }
                fclose($handle);
                \Log::info('CSV file processed', ['rows_count' => count($data)]);
            }
            
            // Remove header row
            array_shift($data);
            \Log::info('Header removed, data rows count', ['data_rows_count' => count($data)]);
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            // Group data by user and date to handle multiple entries per day
            $groupedData = [];
            foreach ($data as $index => $row) {
                try {
                    \Log::info('Processing row', ['row_index' => $index + 2, 'row_data' => $row]);
                    
                    if (count($row) >= 7) {
                        $cloudId = $row[0];
                        $employeeId = $row[1]; // NIP
                        $employeeName = $row[2];
                        $date = $row[3];
                        $time = $row[4];
                        $verification = $row[5];
                        $type = $row[6]; // Absensi Masuk/Absensi Pulang
                        
                        \Log::info('Raw data extracted', [
                            'cloud_id' => $cloudId,
                            'employee_id' => $employeeId,
                            'employee_name' => $employeeName,
                            'date' => $date,
                            'time' => $time,
                            'verification' => $verification,
                            'type' => $type
                        ]);
                        
                        // Smart date validation - only normalize if needed
                        $originalDate = $date;
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                            // Date is already in correct format, use as is
                            \Log::info('Date format already correct', ['date' => $date]);
                        } else {
                            // Date needs normalization
                            $normalizedDate = $this->normalizeDate($date);
                            if (!$normalizedDate) {
                                \Log::warning('Invalid date format', ['date' => $date, 'row_index' => $index + 2]);
                                $errorCount++;
                                $errors[] = "Row " . ($index + 2) . ": Invalid date format '{$date}' (expected YYYY-MM-DD)";
                                continue;
                            }
                            $date = $normalizedDate;
                            \Log::info('Date normalized', ['original' => $originalDate, 'normalized' => $normalizedDate]);
                        }
                        
                        // Smart time validation - only normalize if needed
                        $originalTime = $time;
                        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
                            // Time is already in correct format, use as is
                            \Log::info('Time format already correct', ['time' => $time]);
                        } else {
                            // Time needs normalization
                            $normalizedTime = $this->normalizeTime($time);
                            if (!$normalizedTime) {
                                \Log::warning('Invalid time format', ['time' => $time, 'row_index' => $index + 2]);
                                $errorCount++;
                                $errors[] = "Row " . ($index + 2) . ": Invalid time format '{$time}' (expected HH:MM)";
                                continue;
                            }
                            $time = $normalizedTime;
                            \Log::info('Time normalized', ['original' => $originalTime, 'normalized' => $normalizedTime]);
                        }
                        
                        // Find user by NIP
                        $user = DB::table('users')->where('u_nip', $employeeId)->first();
                        
                        if (!$user) {
                            \Log::warning('User not found by NIP', ['nip' => $employeeId, 'row_index' => $index + 2]);
                            $errorCount++;
                            $errors[] = "Row " . ($index + 2) . ": User with NIP {$employeeId} not found";
                            continue;
                        }
                        
                        $key = $user->id . '_' . $date;
                        
                        if (!isset($groupedData[$key])) {
                            $groupedData[$key] = [
                                'user_id' => $user->id,
                                'at_date' => $date,
                                'at_time_in' => null,
                                'at_time_out' => null,
                                'at_status' => 'present',
                                'at_notes' => "Uploaded from fingerprint: {$employeeName}",
                                'at_source' => 'upload',
                                'created_by' => Auth::user()->id
                            ];
                        }
                        
                        // Set time based on type
                        if (strpos(strtolower($type), 'masuk') !== false) {
                            $groupedData[$key]['at_time_in'] = $time;
                        } elseif (strpos(strtolower($type), 'pulang') !== false) {
                            $groupedData[$key]['at_time_out'] = $time;
                        }
                        
                        \Log::info('Row processed successfully', [
                            'row_index' => $index + 2,
                            'user_id' => $user->id,
                            'nip' => $employeeId,
                            'date' => $date,
                            'type' => $type,
                            'time' => $time
                        ]);
                        
                    } else {
                        \Log::warning('Row skipped - insufficient columns', ['row_index' => $index + 2, 'columns_count' => count($row)]);
                        $errorCount++;
                        $errors[] = "Row " . ($index + 2) . ": Insufficient columns (expected 7, got " . count($row) . ")";
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                    \Log::error('Row processing error', ['row_index' => $index + 2, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                }
            }
            
            // Now save the grouped data
            foreach ($groupedData as $key => $attendanceData) {
                try {
                    \Log::info('Saving grouped attendance data', ['attendance_data' => $attendanceData]);
                    
                    $attendance = new Attendance();
                    $result = $attendance->storeData('add', null, $attendanceData);
                    
                    if ($result) {
                        $successCount++;
                        \Log::info('Grouped data saved successfully', ['key' => $key, 'result_id' => $result]);
                    } else {
                        $errorCount++;
                        $errors[] = "Failed to save data for user {$attendanceData['user_id']} on {$attendanceData['at_date']}";
                        \Log::error('Grouped data save failed', ['key' => $key, 'attendance_data' => $attendanceData]);
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Error saving data for user {$attendanceData['user_id']} on {$attendanceData['at_date']}: " . $e->getMessage();
                    \Log::error('Grouped data save error', ['key' => $key, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                }
            }
            
            // Clean up uploaded file
            unlink(storage_path('app/public/uploads/' . $fileName));
            
            $message = "Upload completed. Success: {$successCount}, Errors: {$errorCount}";
            if ($errorCount > 0) {
                $message .= ". Check logs for details.";
            }
            
            \Log::info('Upload process completed', [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors,
                'message' => $message
            ]);
            
            \Log::info('Redirecting to attendance.index with success message', [
                'route' => 'attendance.index',
                'message' => $message
            ]);
            
            // Store message in session before redirect
            session()->flash('success', $message);
            \Log::info('Session message stored', ['session_id' => session()->getId(), 'message' => $message]);
            
            return redirect()->route('attendance.index');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error processing upload: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $this->validateAccess();
        
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        
        $attendance = new Attendance();
        $attendances = $attendance->getAttendanceByDateRange(
            $startDate,
            $endDate,
            $request->get('user_id'),
            $request->get('division_id'),
            $request->get('status')
        );
        
        $fileName = 'attendance_report_' . $startDate . '_' . $endDate . '_' . date('YmdHis') . '.xlsx';
        
        return Excel::download(new \App\Exports\AttendanceExport($attendances), $fileName);
    }

    public function show($id)
    {
        $this->validateAccess();
        
        $title = 'Attendance Detail';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();
        
        $attendance = DB::table('attendance')
            ->leftJoin('users', 'attendance.user_id', '=', 'users.id')
            ->leftJoin('user_divisions', 'users.ud_id', '=', 'user_divisions.id')
            ->leftJoin('daily_schedules', 'attendance.daily_schedule_id', '=', 'daily_schedules.id')
            ->leftJoin('shift_codes', 'daily_schedules.sc_id', '=', 'shift_codes.id')
            ->select([
                'attendance.*',
                'users.u_name',
                'users.u_nip',
                'user_divisions.ud_name',
                'shift_codes.sc_code',
                'shift_codes.sc_shift_name',
                'daily_schedules.ds_start_time',
                'daily_schedules.ds_end_time'
            ])
            ->where('attendance.id', $id)
            ->first();
            
        if (!$attendance) {
            abort(404, 'Attendance not found');
        }

        // Get daily schedule data if exists
        $dailySchedule = null;
        if ($attendance->daily_schedule_id) {
            $dailySchedule = DB::table('daily_schedules')
                ->select([
                    'daily_schedules.*',
                    'user_divisions.ud_name',
                    'shift_codes.sc_code',
                    'shift_codes.sc_shift_name',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time'
                ])
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'daily_schedules.ud_id')
                ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
                ->where('daily_schedules.id', $attendance->daily_schedule_id)
                ->first();
        }
        
        $data = [
            'title' => $title,
            'subtitle' => 'Detail Attendance: ' . $attendance->u_name . ' - ' . date('d/m/Y', strtotime($attendance->at_date)),
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.attendance.show', compact('attendance', 'dailySchedule', 'data'));
    }

    public function edit($id)
    {
        $this->validateAccess();
        
        $title = 'Edit Attendance';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();
        
        $attendance = DB::table('attendance')
            ->select([
                'attendance.*',
                'users.u_name',
                'users.u_nip',
                'user_divisions.ud_name'
            ])
            ->leftJoin('users', 'users.id', '=', 'attendance.user_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->where('attendance.id', $id)
            ->first();

        if (!$attendance) {
            return redirect()->route('attendance.index')->with('error', 'Attendance record not found');
        }

        // Debug: Log attendance data
        \Log::info('Attendance Edit Debug', [
            'id' => $attendance->id,
            'user_id' => $attendance->user_id,
            'at_date' => $attendance->at_date,
            'at_status' => $attendance->at_status,
            'attendance_data' => $attendance
        ]);

        $users = DB::table('users')
            ->where(function($query) use ($attendance) {
                $query->where('u_delete', '!=', '1')
                      ->orWhere('id', $attendance->user_id); // Include current user even if deleted
            })
            ->get();

        // Debug: Log users data
        \Log::info('Users for Edit Debug', [
            'total_users' => $users->count(),
            'current_user_id' => $attendance->user_id,
            'users_ids' => $users->pluck('id')->toArray(),
            'current_user_in_list' => $users->where('id', $attendance->user_id)->first()
        ]);

        $dailySchedules = DB::table('daily_schedules')->get();
        
        $data = [
            'title' => $title,
            'subtitle' => 'Edit Attendance: ' . $attendance->u_name . ' - ' . date('d/m/Y', strtotime($attendance->at_date)),
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.attendance.edit', compact('attendance', 'users', 'dailySchedules', 'data'));
    }

    public function update(Request $request, $id)
    {
        $this->validateAccess();
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'at_date' => 'required|date',
            'at_time_in' => 'nullable|date_format:H:i',
            'at_time_out' => 'nullable|date_format:H:i|after:at_time_in',
            'at_status' => 'required|in:present,late,absent,early_leave,scan_once',
            'at_notes' => 'nullable|string|max:500',
            'daily_schedule_id' => 'nullable|exists:daily_schedules,id'
        ]);

        try {
            $attendance = Attendance::findOrFail($id);
            $data = [
                'user_id' => $request->user_id,
                'at_date' => $request->at_date,
                'at_time_in' => $request->at_time_in,
                'at_time_out' => $request->at_time_out,
                'at_status' => $request->at_status,
                'at_notes' => $request->at_notes,
                'daily_schedule_id' => $request->daily_schedule_id,
                'updated_by' => Auth::user()->id
            ];

            $result = $attendance->storeData('edit', $id, $data);
            
            if ($result) {
                return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully');
            } else {
                return back()->with('error', 'Failed to update attendance')->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $this->validateAccess();
        
        try {
            $attendance = Attendance::findOrFail($id);
            $result = $attendance->deleteData($id);
            
            if ($result) {
                return response()->json(['success' => true, 'message' => 'Attendance deleted successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete attendance'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function processStatus($id)
    {
        $this->validateAccess();
        
        try {
            $attendance = new Attendance();
            $result = $attendance->processAttendanceStatus($id);
            
            if ($result) {
                return redirect()->back()->with('success', 'Attendance status processed successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to process attendance status');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function reprocessAll(Request $request)
    {
        $this->validateAccess();
        
        try {
            $startDate = $request->get('start_date', date('Y-m-d'));
            $endDate = $request->get('end_date', date('Y-m-d'));
            
            $attendances = DB::table('attendance')
                ->whereBetween('at_date', [$startDate, $endDate])
                ->get();
            
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($attendances as $attendance) {
                try {
                    $attendanceModel = new Attendance();
                    $result = $attendanceModel->processAttendanceStatus($attendance->id);
                    if ($result) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }
            
            $message = "Reprocess completed. Success: {$successCount}, Errors: {$errorCount}";
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function staffDetail($user_id)
    {
        $this->validateAccess();
        
        $title = 'Staff Attendance Detail';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();
        
        $staff = DB::table('users')
            ->leftJoin('user_divisions', 'users.ud_id', '=', 'user_divisions.id')
            ->select('users.*', 'user_divisions.ud_name')
            ->where('users.id', $user_id)
            ->first();
            
        if (!$staff) {
            abort(404, 'Staff not found');
        }
        
        $data = [
            'title' => $title,
            'subtitle' => 'Detail Absensi: ' . $staff->u_name,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.attendance.staff_detail', compact('staff', 'data'));
    }

    public function staffDatatables($user_id)
    {
        $this->validateAccess();
        
        // Debug: Log request parameters
        \Log::info('Staff Datatables Request', [
            'user_id' => $user_id,
            'start_date' => request('start_date'),
            'end_date' => request('end_date'),
            'status' => request('status'),
            'search' => request('search'),
            'draw' => request('draw'),
            'start' => request('start'),
            'length' => request('length')
        ]);
        
        $query = DB::table('attendance')
            ->leftJoin('users', 'attendance.user_id', '=', 'users.id')
            ->leftJoin('daily_schedules', 'attendance.daily_schedule_id', '=', 'daily_schedules.id')
            ->leftJoin('shift_codes', 'daily_schedules.sc_id', '=', 'shift_codes.id')
            ->select([
                'attendance.id',
                'attendance.at_date',
                'attendance.at_time_in',
                'attendance.at_time_out',
                'attendance.at_status',
                'attendance.at_notes',
                'shift_codes.sc_code',
                'shift_codes.sc_start_time',
                'shift_codes.sc_end_time'
            ])
            ->where('attendance.user_id', $user_id);
        
        if (request('start_date')) {
            $query->where('attendance.at_date', '>=', request('start_date'));
        }
        if (request('end_date')) {
            $query->where('attendance.at_date', '<=', request('end_date'));
        }
        if (request('status')) {
            $query->where('attendance.at_status', request('status'));
        }
        
        $total = $query->count();
        
        if (request('search')['value']) {
            $searchValue = request('search')['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('attendance.at_date', 'like', "%{$searchValue}%")
                  ->orWhere('shift_codes.sc_code', 'like', "%{$searchValue}%")
                  ->orWhere('attendance.at_status', 'like', "%{$searchValue}%")
                  ->orWhere('attendance.at_notes', 'like', "%{$searchValue}%");
            });
        }
        
        $filtered = $query->count();
        
        if (request('order')) {
            $columnIndex = request('order')[0]['column'];
            $columnName = request('columns')[$columnIndex]['data'];
            $columnDirection = request('order')[0]['dir'];
            
            if ($columnName && in_array($columnName, ['at_date', 'sc_code', 'at_time_in', 'at_time_out', 'at_status'])) {
                $query->orderBy($columnName, $columnDirection);
            }
        }
        
        $start = request('start', 0);
        $length = request('length', 10);
        $query->skip($start)->take($length);
        
        $data = $query->get();
        
        $formattedData = [];
        foreach ($data as $index => $row) {
            $formattedData[] = [
                'DT_RowIndex' => $start + $index + 1,
                'id' => $row->id,
                'at_date' => date('d/m/Y', strtotime($row->at_date)),
                'sc_code' => $row->sc_code ?? '-',
                'at_time_in' => $row->at_time_in ?? '-',
                'at_time_out' => $row->at_time_out ?? '-',
                'at_status' => $row->at_status,
                'at_notes' => $row->at_notes ?? '-',
                'action' => $this->getStaffActionButtons($row->id)
            ];
        }
        
        // Debug: Log response data
        \Log::info('Staff Datatables Response', [
            'draw' => request('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data_count' => count($formattedData),
            'sample_data' => array_slice($formattedData, 0, 2) // Log first 2 records
        ]);
        
        return response()->json([
            'draw' => request('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $formattedData
        ]);
    }

    public function staffStats($user_id)
    {
        $this->validateAccess();
        
        // Debug: Log request parameters
        \Log::info('Staff Stats Request', [
            'user_id' => $user_id,
            'start_date' => request('start_date'),
            'end_date' => request('end_date'),
            'status' => request('status')
        ]);
        
        $query = DB::table('attendance')
            ->where('user_id', $user_id);
        
        if (request('start_date')) {
            $query->where('at_date', '>=', request('start_date'));
        }
        if (request('end_date')) {
            $query->where('at_date', '<=', request('end_date'));
        }
        if (request('status')) {
            $query->where('at_status', request('status'));
        }
        
        // Debug: Log query before execution
        \Log::info('Staff Stats Query', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        $stats = $query->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN at_status = "present" THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN at_status = "late" THEN 1 ELSE 0 END) as late,
            SUM(CASE WHEN at_status = "absent" THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN at_status = "early_leave" THEN 1 ELSE 0 END) as early_leave,
            SUM(CASE WHEN at_status = "scan_once" THEN 1 ELSE 0 END) as scan_once,
            SUM(CASE WHEN at_status LIKE "leave_%" THEN 1 ELSE 0 END) as leave_total,
            SUM(CASE WHEN at_status = "leave_ANNUAL" THEN 1 ELSE 0 END) as leave_annual,
            SUM(CASE WHEN at_status = "leave_SICK" THEN 1 ELSE 0 END) as leave_sick,
            SUM(CASE WHEN at_status = "leave_MATERNITY" THEN 1 ELSE 0 END) as leave_maternity,
            SUM(CASE WHEN at_status = "leave_EMERGENCY" THEN 1 ELSE 0 END) as leave_emergency,
            SUM(CASE WHEN at_status = "leave_HALF_DAY" THEN 1 ELSE 0 END) as leave_half_day,
            SUM(CASE WHEN at_status = "leave_SPECIAL" THEN 1 ELSE 0 END) as leave_special
        ')->first();
        
        // Debug: Log response data
        \Log::info('Staff Stats Response', [
            'user_id' => $user_id,
            'stats' => $stats
        ]);
        
        return response()->json(['stats' => $stats]);
    }

    private function getActionButtons($id)
    {
        return '<div class="btn-group btn-group-sm">
                    <a href="' . route('attendance.show', $id) . '" class="btn btn-info btn-xs" title="View">
                        <i class="ki-outline ki-eye"></i>
                    </a>
                    <a href="' . route('attendance.edit', $id) . '" class="btn btn-warning btn-xs" title="Edit">
                        <i class="ki-outline ki-notepad-edit"></i>
                    </a>
                </div>';
    }

    private function getStaffActionButtons($id)
    {
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
        $btn .= '            <a href="'.route('attendance.show', $id).'" class="menu-link px-3">View</a>';
        $btn .= '        </div>';
        $btn .= '        <!--end::Menu item-->';
        
        $btn .= '        <!--begin::Menu item-->';
        $btn .= '        <div class="menu-item px-3">';
        $btn .= '            <a href="'.route('attendance.edit', $id).'" class="menu-link px-3">Edit</a>';
        $btn .= '        </div>';
        $btn .= '        <!--end::Menu item-->';
        $btn .= '    </div>';
        $btn .= '    <!--end::Menu-->';
        $btn .= '</div>';
        
        return $btn;
    }


    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            $query = DB::table('attendance')
                ->select([
                    'attendance.*',
                    'users.u_name',
                    'users.u_nip',
                    'user_divisions.ud_name',
                    'shift_codes.sc_code',
                    'shift_codes.sc_shift_name'
                ])
                ->leftJoin('users', 'users.id', '=', 'attendance.user_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('daily_schedules', 'daily_schedules.id', '=', 'attendance.daily_schedule_id')
                ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id');

            // Debug: Log query conditions
            \Log::info('Attendance Datatable Query Debug', [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'user_id' => $request->user_id,
                'division_id' => $request->division_id,
                'status' => $request->status,
                'search' => $request->search
            ]);

            if ($request->filled('start_date')) {
                $query->where('attendance.at_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->where('attendance.at_date', '<=', $request->end_date);
            }
            if ($request->filled('user_id')) {
                $query->where('attendance.user_id', $request->user_id);
            }
            if ($request->filled('division_id')) {
                $query->where('users.ud_id', $request->division_id);
            }
            if ($request->filled('status')) {
                $query->where('attendance.at_status', $request->status);
                \Log::info('Status filter applied', ['status' => $request->status]);
            } else {
                \Log::info('No status filter applied - showing all statuses');
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('users.u_name', 'like', '%' . $search . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $search . '%');
                });
            }

            // Debug: Log final query
            \Log::info('Attendance Datatable Final Query', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $result = datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('user_id', function($row) {
                return $row->user_id;
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
                    $btn .= '            <a href="'.route('attendance.show', $row->id).'" class="menu-link px-3">View</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';
                    
                    $btn .= '        <!--begin::Menu item-->';
                    $btn .= '        <div class="menu-item px-3">';
                    $btn .= '            <a href="'.route('attendance.edit', $row->id).'" class="menu-link px-3">Edit</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';
                    
                    $btn .= '        <!--begin::Menu item-->';
                    $btn .= '        <div class="menu-item px-3">';
                    $btn .= '            <a href="javascript:void(0)" onclick="deleteAttendance('.$row->id.')" class="menu-link px-3 text-danger">Delete</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';
                    $btn .= '    </div>';
                    $btn .= '    <!--end::Menu-->';
                    $btn .= '</div>';
                    
                return $btn;
            })
                ->editColumn('at_date', function($row) {
                    return date('d/m/Y', strtotime($row->at_date));
                })
                ->editColumn('at_time_in', function($row) {
                    return $row->at_time_in ? date('H:i', strtotime($row->at_time_in)) : '-';
                })
                ->editColumn('at_time_out', function($row) {
                    return $row->at_time_out ? date('H:i', strtotime($row->at_time_out)) : '-';
                })
                ->editColumn('sc_code', function($row) {
                    if ($row->sc_code) {
                        return $row->sc_code . ' (' . $row->sc_shift_name . ')';
                    }
                    return '-';
                })
                ->editColumn('at_status', function($row) {
                    $status = $row->at_status;
                    $badgeClass = '';
                    $statusText = '';
                    
                    switch($status) {
                        case 'present':
                            $badgeClass = 'badge-success';
                            $statusText = 'Hadir';
                            break;
                        case 'late':
                            $badgeClass = 'badge-warning';
                            $statusText = 'Terlambat';
                            break;
                        case 'absent':
                            $badgeClass = 'badge-danger';
                            $statusText = 'Tidak Hadir';
                            break;
                        case 'early_leave':
                            $badgeClass = 'badge-info';
                            $statusText = 'Pulang Awal';
                            break;
                        case 'scan_once':
                            $badgeClass = 'badge-secondary';
                            $statusText = 'Scan 1 Kali';
                            break;
                        default:
                            if (strpos($status, 'leave_') === 0) {
                                $leaveType = str_replace('leave_', '', $status);
                                $badgeClass = 'badge-primary';
                                $statusText = 'Cuti ' . $leaveType;
                            } else {
                                $badgeClass = 'badge-dark';
                                $statusText = ucfirst($status);
                            }
                    }
                    
                    return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
                })
                ->rawColumns(['action', 'at_status'])
            ->make(true);

            return $result;
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }
    
    /**
     * Helper method to get date range from filter
     */
    private function getDateRangeFromFilter($filter)
    {
        $today = Carbon::now();
        
        switch ($filter) {
            case 'this_week':
                $startDate = $today->copy()->startOfWeek()->format('Y-m-d');
                $endDate = $today->copy()->endOfWeek()->format('Y-m-d');
                break;
            case 'past_week':
                $startDate = $today->copy()->subWeek()->startOfWeek()->format('Y-m-d');
                $endDate = $today->copy()->subWeek()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $startDate = $today->copy()->startOfMonth()->format('Y-m-d');
                $endDate = $today->copy()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $startDate = $today->copy()->subMonth()->startOfMonth()->format('Y-m-d');
                $endDate = $today->copy()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            default:
                $startDate = $today->copy()->startOfWeek()->format('Y-m-d');
                $endDate = $today->copy()->endOfWeek()->format('Y-m-d');
        }
        
        return [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }
    
    /**
     * Normalize date from various formats to Y-m-d
     */
    private function normalizeDate($date)
    {
        // If it's already in Y-m-d format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }
        
        // If it's an Excel serial number (like 45884)
        if (is_numeric($date) && $date > 1000) {
            // Excel serial number to date conversion
            try {
                $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
                return $excelDate->format('Y-m-d');
            } catch (\Exception $e) {
                \Log::warning('Failed to convert Excel serial number', ['date' => $date, 'error' => $e->getMessage()]);
                return false;
            }
        }
        
        // Try to parse various date formats
        try {
            $parsedDate = \Carbon\Carbon::parse($date);
            return $parsedDate->format('Y-m-d');
        } catch (\Exception $e) {
            \Log::warning('Failed to parse date', ['date' => $date, 'error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Normalize time to H:i format
     */
    private function normalizeTime($time)
    {
        // If it's already in H:i format
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return $time;
        }
        
        // Try to parse various time formats
        try {
            $parsedTime = \Carbon\Carbon::parse($time);
            return $parsedTime->format('H:i');
        } catch (\Exception $e) {
            \Log::warning('Failed to parse time', ['time' => $time, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Export attendance data to Excel
     */
    public function exportToExcel(Request $request)
    {
        $this->validateAccess();
        
        try {
            // Get the same data as the index page with filters
            $startDate = $request->get('start_date', date('Y-m-d'));
            $endDate = $request->get('end_date', date('Y-m-d'));
            $dateFilter = $request->get('date_filter', 'this_week');
            
            // If date filter is provided, calculate dates
            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }
            
            $attendance = new Attendance();
            $attendances = $attendance->getAttendanceByDateRange(
                $startDate,
                $endDate,
                $request->get('user_id'),
                $request->get('division_id'),
                $request->get('status')
            );

            // Generate filename with filters
            $filename = 'attendance_' . date('Y-m-d_H-i-s');
            if ($request->get('division_id')) {
                $division = DB::table('user_divisions')->find($request->get('division_id'));
                $filename .= '_' . ($division ? str_replace(' ', '_', $division->ud_name) : 'all');
            }
            if ($request->get('date_filter') && $request->get('date_filter') !== 'custom') {
                $filename .= '_' . $request->get('date_filter');
            }
            $filename .= '.xlsx';

            return Excel::download(new AttendanceExport($attendances), $filename);
            
        } catch (\Exception $e) {
            \Log::error('Export attendance error: ' . $e->getMessage());
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export attendance data to PDF
     */
    public function exportToPDF(Request $request)
    {
        $this->validateAccess();
        
        try {
            // Get the same data as the index page with filters
            $startDate = $request->get('start_date', date('Y-m-d'));
            $endDate = $request->get('end_date', date('Y-m-d'));
            $dateFilter = $request->get('date_filter', 'this_week');
            
            // If date filter is provided, calculate dates
            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }
            
            $attendance = new Attendance();
            $attendances = $attendance->getAttendanceByDateRange(
                $startDate,
                $endDate,
                $request->get('user_id'),
                $request->get('division_id'),
                $request->get('status')
            );

            // Generate HTML for PDF
            $html = $this->generateAttendanceHTML($attendances, $request);
            
            // Generate filename
            $filename = 'attendance_' . date('Y-m-d_H-i-s');
            if ($request->get('division_id')) {
                $division = DB::table('user_divisions')->find($request->get('division_id'));
                $filename .= '_' . ($division ? str_replace(' ', '_', $division->ud_name) : 'all');
            }
            if ($request->get('date_filter') && $request->get('date_filter') !== 'custom') {
                $filename .= '_' . $request->get('date_filter');
            }
            $filename .= '.pdf';

            $pdf = \PDF::loadHTML($html);
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Export attendance PDF error: ' . $e->getMessage());
            return back()->with('error', 'Export PDF failed: ' . $e->getMessage());
        }
    }

    /**
     * Export staff attendance detail to Excel
     */
    public function exportStaffToExcel($user_id, Request $request)
    {
        $this->validateAccess();
        
        try {
            // Get staff info
            $staff = DB::table('users')
                ->leftJoin('user_divisions', 'users.ud_id', '=', 'user_divisions.id')
                ->select('users.*', 'user_divisions.ud_name')
                ->where('users.id', $user_id)
                ->first();

            if (!$staff) {
                return back()->with('error', 'Staff not found');
            }

            // Get attendance data for this staff
            $startDate = $request->get('start_date', date('Y-m-d', strtotime('-30 days')));
            $endDate = $request->get('end_date', date('Y-m-d'));
            
            $attendance = new Attendance();
            $attendances = $attendance->getAttendanceByDateRange(
                $startDate,
                $endDate,
                $user_id,
                null,
                null
            );

            // Generate filename
            $filename = 'staff_attendance_' . $staff->u_nip . '_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new StaffAttendanceExport($attendances, $staff->u_name), $filename);
            
        } catch (\Exception $e) {
            \Log::error('Export staff attendance error: ' . $e->getMessage());
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export staff attendance detail to PDF
     */
    public function exportStaffToPDF($user_id, Request $request)
    {
        $this->validateAccess();
        
        try {
            // Get staff info
            $staff = DB::table('users')
                ->leftJoin('user_divisions', 'users.ud_id', '=', 'user_divisions.id')
                ->select('users.*', 'user_divisions.ud_name')
                ->where('users.id', $user_id)
                ->first();

            if (!$staff) {
                return back()->with('error', 'Staff not found');
            }

            // Get attendance data for this staff
            $startDate = $request->get('start_date', date('Y-m-d', strtotime('-30 days')));
            $endDate = $request->get('end_date', date('Y-m-d'));
            
            $attendance = new Attendance();
            $attendances = $attendance->getAttendanceByDateRange(
                $startDate,
                $endDate,
                $user_id,
                null,
                null
            );

            // Generate HTML for PDF
            $html = $this->generateStaffAttendanceHTML($attendances, $staff, $request);
            
            // Generate filename
            $filename = 'staff_attendance_' . $staff->u_nip . '_' . date('Y-m-d_H-i-s') . '.pdf';

            $pdf = \PDF::loadHTML($html);
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Export staff attendance PDF error: ' . $e->getMessage());
            return back()->with('error', 'Export PDF failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate HTML for attendance PDF
     */
    private function generateAttendanceHTML($attendances, $request)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Attendance Report</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h1 { margin: 0; color: #2E75B6; }
                .header p { margin: 5px 0; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #4472C4; color: white; font-weight: bold; }
                .status-present { background-color: #d4edda; }
                .status-late { background-color: #fff3cd; }
                .status-absent { background-color: #f8d7da; }
                .status-early_leave { background-color: #ffeaa7; }
                .status-scan_once { background-color: #e2e3e5; }
                .status-leave { background-color: #cce5ff; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN KEHADIRAN</h1>
                <p>Periode: ' . date('d/m/Y', strtotime($request->get('start_date', date('Y-m-d')))) . ' - ' . date('d/m/Y', strtotime($request->get('end_date', date('Y-m-d')))) . '</p>
                <p>Dibuat pada: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Divisi</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Shift</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($attendances as $attendance) {
            $statusClass = 'status-' . ($attendance->at_status ?? 'unknown');
            $statusText = $this->getStatusTextForPDF($attendance->at_status);
            
            $html .= '
                    <tr>
                        <td>' . ($attendance->u_nip ?? '-') . '</td>
                        <td>' . ($attendance->u_name ?? '-') . '</td>
                        <td>' . ($attendance->ud_name ?? '-') . '</td>
                        <td>' . ($attendance->at_date ? date('d/m/Y', strtotime($attendance->at_date)) : '-') . '</td>
                        <td>' . ($attendance->at_time_in ? date('H:i', strtotime($attendance->at_time_in)) : '-') . '</td>
                        <td>' . ($attendance->at_time_out ? date('H:i', strtotime($attendance->at_time_out)) : '-') . '</td>
                        <td class="' . $statusClass . '">' . $statusText . '</td>
                        <td>' . ($attendance->sc_code ?? '-') . '</td>
                        <td>' . ($attendance->at_notes ?? '-') . '</td>
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
     * Generate HTML for staff attendance PDF
     */
    private function generateStaffAttendanceHTML($attendances, $staff, $request)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Staff Attendance Report</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h1 { margin: 0; color: #2E75B6; }
                .header h2 { margin: 10px 0; color: #4472C4; }
                .header p { margin: 5px 0; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #4472C4; color: white; font-weight: bold; }
                .status-present { background-color: #d4edda; }
                .status-late { background-color: #fff3cd; }
                .status-absent { background-color: #f8d7da; }
                .status-early_leave { background-color: #ffeaa7; }
                .status-scan_once { background-color: #e2e3e5; }
                .status-leave { background-color: #cce5ff; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN KEHADIRAN STAFF</h1>
                <h2>' . $staff->u_name . ' (' . $staff->u_nip . ')</h2>
                <p>Divisi: ' . ($staff->ud_name ?? '-') . '</p>
                <p>Periode: ' . date('d/m/Y', strtotime($request->get('start_date', date('Y-m-d', strtotime('-30 days'))))) . ' - ' . date('d/m/Y', strtotime($request->get('end_date', date('Y-m-d')))) . '</p>
                <p>Dibuat pada: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Shift</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($attendances as $attendance) {
            $statusClass = 'status-' . ($attendance->at_status ?? 'unknown');
            $statusText = $this->getStatusTextForPDF($attendance->at_status);
            
            $html .= '
                    <tr>
                        <td>' . ($attendance->at_date ? date('d/m/Y', strtotime($attendance->at_date)) : '-') . '</td>
                        <td>' . ($attendance->at_time_in ? date('H:i', strtotime($attendance->at_time_in)) : '-') . '</td>
                        <td>' . ($attendance->at_time_out ? date('H:i', strtotime($attendance->at_time_out)) : '-') . '</td>
                        <td class="' . $statusClass . '">' . $statusText . '</td>
                        <td>' . ($attendance->sc_code ?? '-') . '</td>
                        <td>' . ($attendance->at_notes ?? '-') . '</td>
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
     * Get status text for PDF
     */
    private function getStatusTextForPDF($status)
    {
        $statusMap = [
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Tidak Hadir',
            'early_leave' => 'Pulang Awal',
            'scan_once' => 'Scan Sekali',
            'leave' => 'Cuti'
        ];

        return $statusMap[$status] ?? $status;
    }
}
