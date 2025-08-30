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
use App\Exports\AttendanceSummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected function validateAccess()
    {
        try {
            \Log::info('validateAccess - Starting', [
                'user_id' => Auth::user()->id,
                'segment_1' => request()->segment(1),
                'segment_2' => request()->segment(2)
            ]);
            
        $validate = DB::table('user_menu_accesses')
        ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
            'u_id' => Auth::user()->id,
            'ma_slug' => request()->segment(1)
        ])->exists();
            
            \Log::info('validateAccess - Result', [
                'validate' => $validate
            ]);
            
        if (!$validate) {
            dd("Anda tidak memiliki akses ke menu ini, hubungi Administrator");
            }
        } catch (\Exception $e) {
            \Log::error('validateAccess - Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
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
            
            // Now save the grouped data with duplicate handling
            \Log::info('Starting to save grouped attendance data', ['total_records' => count($groupedData)]);
            foreach ($groupedData as $key => $attendanceData) {
                try {
                    \Log::info('Saving grouped attendance data', ['attendance_data' => $attendanceData]);
                    
                    // Cek apakah data sudah ada
                    $existingAttendance = DB::table('attendance')
                        ->where('user_id', $attendanceData['user_id'])
                        ->where('at_date', $attendanceData['at_date'])
                        ->first();
                    
                    // Find daily schedule for this user and date
                    $dailySchedule = DB::table('daily_schedules')
                        ->leftJoin('shift_codes', 'daily_schedules.sc_id', '=', 'shift_codes.id')
                        ->leftJoin('users', 'users.id', '=', 'daily_schedules.user_id')
                        ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                        ->where('daily_schedules.user_id', $attendanceData['user_id'])
                        ->where('daily_schedules.ds_date', $attendanceData['at_date'])
                        ->where('daily_schedules.ds_status', 'scheduled')
                        ->select([
                            'daily_schedules.id',
                            'daily_schedules.sc_id',
                            'shift_codes.sc_code',
                            'shift_codes.sc_shift_name',
                            'user_types.ut_name as user_type_name'
                        ])
                        ->first();
                    
                    \Log::info('Daily schedule lookup during import', [
                        'user_id' => $attendanceData['user_id'],
                        'date' => $attendanceData['at_date'],
                        'schedule_found' => !is_null($dailySchedule),
                        'schedule_data' => $dailySchedule ? [
                            'id' => $dailySchedule->id,
                            'sc_id' => $dailySchedule->sc_id,
                            'sc_code' => $dailySchedule->sc_code,
                            'sc_shift_name' => $dailySchedule->sc_shift_name,
                            'user_type' => $dailySchedule->user_type_name
                        ] : null
                    ]);
                    
                    // Check shift code compatibility with user type if schedule found
                    if ($dailySchedule && $dailySchedule->user_type_name) {
                        $shiftCodeModel = \App\Models\ShiftCode::find($dailySchedule->sc_id);
                        if ($shiftCodeModel) {
                            // Check compatibility using new pivot table relationship
                            $isCompatible = $shiftCodeModel->userTypes()
                                ->where('ut_name', $dailySchedule->user_type_name)
                                ->exists();
                            
                            // Also check legacy compatibility for backward compatibility
                            if (!$isCompatible) {
                                $isCompatible = $shiftCodeModel->isCompatibleWithUserTypeLegacy($dailySchedule->user_type_name);
                            }
                            
                            if (!$isCompatible) {
                                \Log::warning('Shift code not compatible with user type during import', [
                                    'user_id' => $attendanceData['user_id'],
                                    'date' => $attendanceData['at_date'],
                                    'shift_code_id' => $dailySchedule->sc_id,
                                    'shift_code' => $dailySchedule->sc_code,
                                    'user_type' => $dailySchedule->user_type_name
                                ]);
                            } else {
                                \Log::info('Shift code compatible with user type during import', [
                                    'user_id' => $attendanceData['user_id'],
                                    'shift_code' => $dailySchedule->sc_code,
                                    'user_type' => $dailySchedule->user_type_name
                                ]);
                            }
                        }
                    }
                    
                    if ($existingAttendance) {
                        // Data sudah ada, UPDATE
                        \Log::info('Updating existing attendance data', [
                            'existing_id' => $existingAttendance->id,
                            'key' => $key
                        ]);
                        
                        $updateData = [
                            'at_time_in' => $attendanceData['at_time_in'] ?: $existingAttendance->at_time_in,
                            'at_time_out' => $attendanceData['at_time_out'] ?: $existingAttendance->at_time_out,
                            'at_notes' => $attendanceData['at_notes'],
                            'at_source' => $attendanceData['at_source'],
                            'updated_by' => Auth::user()->id,
                            'updated_at' => now()
                        ];
                        
                        // Add daily_schedule_id if schedule found
                        if ($dailySchedule) {
                            $updateData['daily_schedule_id'] = $dailySchedule->id;
                            \Log::info('Adding daily_schedule_id to existing attendance update', [
                                'attendance_id' => $existingAttendance->id,
                                'daily_schedule_id' => $dailySchedule->id,
                                'shift_code' => $dailySchedule->sc_code
                            ]);
                        }
                        
                        $result = DB::table('attendance')
                            ->where('id', $existingAttendance->id)
                            ->update($updateData);
                            
                        if ($result) {
                            $successCount++;
                            \Log::info('Existing data updated successfully', [
                                'key' => $key, 
                                'attendance_id' => $existingAttendance->id,
                                'daily_schedule_id_updated' => isset($updateData['daily_schedule_id'])
                            ]);
                        } else {
                            $errorCount++;
                            $errors[] = "Failed to update data for user {$attendanceData['user_id']} on {$attendanceData['at_date']}";
                            \Log::error('Update failed', ['key' => $key, 'attendance_data' => $attendanceData]);
                        }
                    } else {
                        // Data baru, INSERT
                        $attendance = new Attendance();
                        
                        // Add daily_schedule_id if schedule found
                        if ($dailySchedule) {
                            $attendanceData['daily_schedule_id'] = $dailySchedule->id;
                            \Log::info('Adding daily_schedule_id to new attendance data', [
                                'daily_schedule_id' => $dailySchedule->id,
                                'shift_code' => $dailySchedule->sc_code
                            ]);
                        }
                        
                        $result = $attendance->storeData('add', null, $attendanceData);
                        
                        if ($result) {
                            $successCount++;
                            \Log::info('New data inserted successfully', [
                                'key' => $key, 
                                'result_id' => $result,
                                'daily_schedule_id_added' => isset($attendanceData['daily_schedule_id'])
                            ]);
                        } else {
                            $errorCount++;
                            $errors[] = "Failed to save data for user {$attendanceData['user_id']} on {$attendanceData['at_date']}";
                            \Log::error('Insert failed', ['key' => $key, 'attendance_data' => $attendanceData]);
                        }
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Error saving data for user {$attendanceData['user_id']} on {$attendanceData['at_date']}: " . $e->getMessage();
                    \Log::error('Save error', ['key' => $key, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                }
            }
            
            // Log summary of save operations
            \Log::info('Attendance data save operations completed', [
                'total_records' => count($groupedData),
                'success_count' => $successCount,
                'error_count' => $errorCount
            ]);
            
            // Process attendance status based on daily schedule
            \Log::info('Starting attendance status processing after upload');
            
            // Initialize message variable
            $message = "Upload completed. Success: {$successCount}, Errors: {$errorCount}";
            if ($errorCount > 0) {
                $message .= ". Check logs for details.";
            }
            
            \Log::info('Upload summary before status processing', [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'total_records' => count($groupedData)
            ]);
            
            // Determine date range from uploaded data
            $uploadedDates = array_unique(array_column($groupedData, 'at_date'));
            if (!empty($uploadedDates)) {
                $minDate = min($uploadedDates);
                $maxDate = max($uploadedDates);
                
                \Log::info('Processing attendance status for date range', [
                    'min_date' => $minDate,
                    'max_date' => $maxDate
                ]);
                
                $statusProcessResult = $this->processAttendanceStatusAfterUpload($minDate, $maxDate);
                
                if ($statusProcessResult['success']) {
                    \Log::info('Attendance status processing completed successfully', [
                        'processed_count' => $statusProcessResult['processed_count'],
                        'error_count' => $statusProcessResult['error_count']
                    ]);
                    
                    $statusMessage = "Status processing: {$statusProcessResult['processed_count']} processed, {$statusProcessResult['error_count']} errors";
                    $message .= ". " . $statusMessage;
                } else {
                    \Log::warning('Attendance status processing failed', [
                        'error' => $statusProcessResult['error']
                    ]);
                    
                    $message .= ". Status processing failed: " . $statusProcessResult['error'];
                }
            }
            
            // Clean up uploaded file
            unlink(storage_path('app/public/uploads/' . $fileName));
            
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
            
            \Log::info('Reprocess all attendance requested', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => Auth::id()
            ]);
            
            // Use the new comprehensive status processing logic
            \Log::info('Starting processAttendanceStatusAfterUpload', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            
            $result = $this->processAttendanceStatusAfterUpload($startDate, $endDate);
            
            if ($result['success']) {
                $message = "Reprocess completed successfully. Processed: {$result['processed_count']}, Errors: {$result['error_count']}";
                \Log::info('Reprocess all completed', $result);
                
            return redirect()->back()->with('success', $message);
            } else {
                $message = "Reprocess failed: " . $result['error'];
                \Log::error('Reprocess all failed', $result);
                
                return redirect()->back()->with('error', $message);
            }
            
        } catch (\Exception $e) {
            $message = "Error during reprocess: " . $e->getMessage();
            \Log::error('Reprocess all exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', $message);
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
            'subtitle' => 'Attendance Details',
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
        
        // Get daily schedules count for total shifts (excluding libur)
        $totalShifts = DB::table('daily_schedules as ds')
            ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
            ->where('ds.user_id', $user_id)
            ->where('ds.ds_status', 'scheduled')
            ->whereNotIn('sc.sc_code', ['L', 'LPH']); // Exclude libur (L) and libur per hari (LPH)
        
        if (request('start_date')) {
            $totalShifts->where('ds.ds_date', '>=', request('start_date'));
        }
        if (request('end_date')) {
            $totalShifts->where('ds.ds_date', '<=', request('end_date'));
        }
        
        $totalShiftsCount = $totalShifts->count();
        
        // Get total libur count
        $totalLibur = DB::table('daily_schedules as ds')
            ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
            ->where('ds.user_id', $user_id)
            ->where('ds.ds_status', 'scheduled')
            ->whereIn('sc.sc_code', ['L', 'LPH']); // Only libur (L) and libur per hari (LPH)
        
        if (request('start_date')) {
            $totalLibur->where('ds.ds_date', '>=', request('start_date'));
        }
        if (request('end_date')) {
            $totalLibur->where('ds.ds_date', '<=', request('end_date'));
        }
        
        $totalLiburCount = $totalLibur->count();
        
        $stats = $query->selectRaw('
            SUM(CASE WHEN at_status = "present" THEN 1 ELSE 0 END) as present_days,
            SUM(CASE WHEN at_status = "late" THEN 1 ELSE 0 END) as late_days,
            SUM(CASE WHEN at_status = "leave_SICK" THEN 1 ELSE 0 END) as sick_days,
            SUM(CASE WHEN at_status LIKE "leave_%" AND at_status != "leave_SICK" AND at_status != "leave_HALF_DAY" THEN 1 ELSE 0 END) as leave_days
        ')->first();
        
        // Calculate alpha days (excluding libur)
        $presentDays = $stats->present_days ?? 0;
        $leaveDays = $stats->leave_days ?? 0;
        $sickDays = $stats->sick_days ?? 0;
        $alphaDays = $totalShiftsCount - $presentDays - ($leaveDays + $sickDays);
        
        // Create final stats object
        $finalStats = (object) [
            'total_shifts' => $totalShiftsCount,
            'total_libur' => $totalLiburCount,
            'present_days' => $presentDays,
            'sick_days' => $sickDays,
            'leave_days' => $leaveDays,
            'late_days' => $stats->late_days ?? 0,
            'alpha_days' => $alphaDays
        ];
        
        // Debug: Log response data
        \Log::info('Staff Stats Response', [
            'user_id' => $user_id,
            'stats' => $finalStats
        ]);
        
        return response()->json(['stats' => $finalStats]);
    }

    /**
     * Get alpha dates for specific staff
     */
    public function getStaffAlphaDates($user_id, Request $request)
    {
        $this->validateAccess();
        
        try {
            $startDate = $request->get('start_date', date('Y-m-d', strtotime('-30 days')));
            $endDate = $request->get('end_date', date('Y-m-d'));
            
            \Log::info('Getting alpha dates for staff', [
                'user_id' => $user_id,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            
            // Get dates where user has schedule (excluding libur/L) but no attendance or leave
            $alphaDates = DB::table('daily_schedules as ds')
                ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
                ->leftJoin('attendance as a', function($join) use ($user_id) {
                    $join->on('ds.user_id', '=', 'a.user_id')
                         ->on('ds.ds_date', '=', 'a.at_date');
                })
                ->leftJoin('leave_requests as lr', function($join) use ($user_id) {
                    $join->on('ds.user_id', '=', 'lr.user_id')
                         ->where('lr.lr_status', '=', 'approved')
                         ->whereRaw('ts_ds.ds_date BETWEEN ts_lr.lr_start_date AND ts_lr.lr_end_date');
                })
                ->select([
                    'ds.ds_date',
                    'sc.sc_code',
                    'sc.sc_start_time',
                    'sc.sc_end_time',
                    'sc.sc_shift_name',
                    DB::raw('CASE WHEN ts_a.id IS NOT NULL THEN "HADIR" WHEN ts_lr.id IS NULL THEN "CUTI" ELSE "ALFA" END as status_type'),
                    DB::raw('CASE 
                        WHEN ts_a.id IS NULL AND ts_lr.id IS NULL THEN "Tidak ada fingerprint attendance atau cuti yang disetujui"
                        WHEN ts_a.id IS NOT NULL THEN "Sudah hadir"
                        WHEN ts_lr.id IS NOT NULL THEN "Sedang cuti"
                        ELSE "Tidak ada data"
                    END as keterangan')
                ])
                ->where('ds.user_id', $user_id)
                ->where('ds.ds_status', 'scheduled')
                ->where('ds.ds_date', '>=', $startDate)
                ->where('ds.ds_date', '<=', $endDate)
                ->whereNotIn('sc.sc_code', ['L', 'LPH']) // Exclude libur (L) and libur per hari (LPH)
                ->whereNull('a.id') // No attendance record
                ->whereNull('lr.id') // No approved leave
                ->orderBy('ds.ds_date', 'desc')
                ->get();
            
            \Log::info('Alpha dates query result', [
                'alpha_dates_count' => $alphaDates->count(),
                'sample_dates' => $alphaDates->take(5)->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $alphaDates
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting alpha dates', [
                'user_id' => $user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
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
                            $badgeClass = 'badge-light-blue';
                            $statusText = 'Hadir';
                            break;
                        case 'late':
                            $badgeClass = 'badge-light-yellow';
                            $statusText = 'Terlambat';
                            break;
                        case 'absent':
                            $badgeClass = 'badge-light-red';
                            $statusText = 'Tidak Hadir';
                            break;
                        case 'early_leave':
                            $badgeClass = 'badge-light-purple';
                            $statusText = 'Pulang Awal';
                            break;
                        case 'scan_once':
                            $badgeClass = 'badge-light-green';
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
            case 'this_quarter':
                $startDate = $today->copy()->startOfQuarter()->format('Y-m-d');
                $endDate = $today->copy()->endOfQuarter()->format('Y-m-d');
                break;
            case 'this_year':
                $startDate = $today->copy()->startOfYear()->format('Y-m-d');
                $endDate = $today->copy()->endOfYear()->format('Y-m-d');
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
     * Show attendance summary report
     */
    public function summaryReport(Request $request)
    {
        try {
            \Log::info('Summary Report - Starting method');
            $this->validateAccess();
            \Log::info('Summary Report - Access validated');
            
            $title = 'Attendance Summary Report';
            $user = auth()->user();
            $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();
            
            // Handle date filter
            $dateFilter = $request->get('date_filter', 'this_week');
            
            if ($dateFilter === 'custom') {
                // Use visible date inputs for custom range
                $startDate = $request->get('start_date', date('Y-m-01'));
                $endDate = $request->get('end_date', date('Y-m-t'));
            } else {
                // Use date inputs (which are set by JavaScript)
                $startDate = $request->get('start_date');
                $endDate = $request->get('end_date');
                
                // If date inputs are empty, calculate from filter
                if (empty($startDate) || empty($endDate)) {
                    $dateRange = $this->getDateRangeFromFilter($dateFilter);
                    $startDate = $dateRange['startDate'];
                    $endDate = $dateRange['endDate'];
                }
            }
            
            // Get summary data
            $summaryData = $this->getAttendanceSummary($startDate, $endDate, $request->get('division_id'));
            
            // Apply search filter if provided
            if ($request->filled('search')) {
                $search = $request->get('search');
                $summaryData = $summaryData->filter(function($item) use ($search) {
                    return stripos($item->u_name, $search) !== false || 
                           stripos($item->u_nip, $search) !== false;
                });
            }
            
            $users = DB::table('users')->where('u_delete', '!=', '1')->get();
            $divisions = DB::table('user_divisions')->where('ud_status', 'active')->get();

            $data = [
                'title' => $title,
                'subtitle' => 'Attendance Summary Report',
                'sidebar' => $this->sidebar(),
                'user' => $user_data,
                'segment' => 'attendance', // Use 'attendance' instead of 'summary-report'
                'startDate' => $startDate,
                'endDate' => $endDate,
                'dateFilter' => $dateFilter
            ];
            
            try {
                \Log::info('Summary Report - Data structure', [
                    'data_type' => gettype($data),
                    'data_keys' => array_keys($data),
                    'sidebar_type' => gettype($data['sidebar']),
                    'user_type' => gettype($data['user'])
                ]);
                
                \Log::info('Summary Report - Summary data sample', [
                    'first_item' => $summaryData->first(),
                    'total_count' => $summaryData->count()
                ]);
                
                return view('app.attendance.summary_report', compact('summaryData', 'divisions', 'data'));
            } catch (\Exception $e) {
                \Log::error('Summary Report - Error in view', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Summary Report - Error in method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get attendance summary data
     */
    private function getAttendanceSummary($startDate, $endDate, $divisionId = null)
    {
        try {
            \Log::info('getAttendanceSummary - Starting query', [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'divisionId' => $divisionId
            ]);
            
            $query = DB::table('users as u')
                ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
                ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
                ->leftJoin('user_types as ut', 'u.ut_id', '=', 'ut.id')
                ->select([
                    'u.id as user_id',
                    'u.u_nip',
                    'u.u_name',
                    'up.up_name as position_name',
                    'ud.ud_name as division_name',
                    'ut.ut_name as work_type',
                    DB::raw('COUNT(DISTINCT ts_daily_schedules.id) as total_shifts'),
                    DB::raw('COUNT(DISTINCT CASE WHEN ts_shift_codes.sc_code IN ("L", "LPH") THEN ts_daily_schedules.id END) as total_libur'),
                    DB::raw('COUNT(DISTINCT CASE WHEN ts_attendance.at_status = "present" THEN ts_attendance.id END) as present_days'),
                    DB::raw('COUNT(DISTINCT CASE WHEN ts_attendance.at_status = "late" THEN ts_attendance.id END) as late_days'),
                    DB::raw('COUNT(DISTINCT CASE WHEN ts_attendance.at_status = "leave_SICK" THEN ts_attendance.id END) as sick_days'),
                    DB::raw('COUNT(DISTINCT CASE WHEN ts_attendance.at_status LIKE "leave_%" AND ts_attendance.at_status != "leave_SICK" AND ts_attendance.at_status != "leave_HALF_DAY" THEN ts_attendance.id END) as leave_days'),
                    DB::raw('(COUNT(DISTINCT ts_daily_schedules.id) - COUNT(DISTINCT CASE WHEN ts_shift_codes.sc_code IN ("L", "LPH") THEN ts_daily_schedules.id END) - COUNT(DISTINCT CASE WHEN ts_attendance.at_status = "present" THEN ts_attendance.id END) - COUNT(DISTINCT CASE WHEN ts_attendance.at_status LIKE "leave_%" THEN ts_attendance.id END)) as alpha_days')
                ])
                ->leftJoin('attendance', function($join) use ($startDate, $endDate) {
                    $join->on('u.id', '=', 'attendance.user_id')
                         ->whereBetween('attendance.at_date', [$startDate, $endDate]);
                })
                ->leftJoin('daily_schedules', function($join) use ($startDate, $endDate) {
                    $join->on('u.id', '=', 'daily_schedules.user_id')
                         ->whereBetween('daily_schedules.ds_date', [$startDate, $endDate])
                         ->where('daily_schedules.ds_status', 'scheduled');
                })
                ->leftJoin('shift_codes', 'daily_schedules.sc_id', '=', 'shift_codes.id')
                ->where('u.u_delete', '!=', '1')
                ->whereNotNull('u.u_nip')
                ->where('u.u_nip', '!=', '')
                ->groupBy('u.id', 'u.u_nip', 'u.u_name', 'up.up_name', 'ud.ud_name', 'ut.ut_name');

            if ($divisionId) {
                $query->where('u.ud_id', $divisionId);
            }

            $result = $query->orderBy('u.u_name')->get();
            
            \Log::info('getAttendanceSummary - Query completed', [
                'result_count' => $result->count(),
                'first_item' => $result->first()
            ]);
            
            return $result;
        } catch (\Exception $e) {
            \Log::error('getAttendanceSummary - Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Export summary report to Excel
     */
    public function exportSummaryToExcel(Request $request)
    {
        $this->validateAccess();
        
        try {
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $dateFilter = $request->get('date_filter', 'this_month');
            
            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }
            
            $summaryData = $this->getAttendanceSummary($startDate, $endDate, $request->get('division_id'));
            
            $filename = 'attendance_summary_' . date('Y-m-d_H-i-s');
            if ($request->get('division_id')) {
                $division = DB::table('user_divisions')->find($request->get('division_id'));
                $filename .= '_' . ($division ? str_replace(' ', '_', $division->ud_name) : 'all');
            }
            if ($request->get('start_date') && $request->get('end_date')) {
                $filename .= '_' . $request->get('start_date') . '_to_' . $request->get('end_date');
            }
            $filename .= '.xlsx';
            
            return Excel::download(new AttendanceSummaryExport($summaryData), $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Error exporting data: ' . $e->getMessage());
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

    /**
     * Process attendance status based on daily schedule after Excel upload
     */
    private function processAttendanceStatusAfterUpload($startDate, $endDate)
    {
        \Log::info('Processing attendance status after upload', [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $processedCount = 0;
        $errorCount = 0;

        try {
            // Get all attendance records from upload source within date range
            $attendanceRecords = DB::table('attendance')
                ->where('at_source', 'upload')
                ->whereBetween('at_date', [$startDate, $endDate])
                ->get();
                
            \Log::info('Found attendance records to process', [
                'count' => $attendanceRecords->count(),
                'date_range' => [$startDate, $endDate],
                'sample_records' => $attendanceRecords->take(3)->map(function($record) {
                    return [
                        'id' => $record->id,
                        'user_id' => $record->user_id,
                        'date' => $record->at_date,
                        'daily_schedule_id' => $record->daily_schedule_id
                    ];
                })->toArray()
            ]);

            \Log::info('Found attendance records to process', [
                'count' => $attendanceRecords->count(),
                'date_range' => [$startDate, $endDate]
            ]);

            foreach ($attendanceRecords as $attendance) {
                try {
                    $result = $this->processSingleAttendanceStatus($attendance);
                    if ($result) {
                        $processedCount++;
                    } else {
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    \Log::error('Error processing attendance status', [
                        'attendance_id' => $attendance->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            \Log::info('Attendance status processing completed', [
                'processed_count' => $processedCount,
                'error_count' => $errorCount
            ]);

            return [
                'success' => true,
                'processed_count' => $processedCount,
                'error_count' => $errorCount
            ];

        } catch (\Exception $e) {
            \Log::error('Error in batch attendance status processing', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process status for single attendance record
     */
    private function processSingleAttendanceStatus($attendance)
    {
        try {
            \Log::info('Processing single attendance status', [
                'attendance_id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'date' => $attendance->at_date,
                'time_in' => $attendance->at_time_in,
                'time_out' => $attendance->at_time_out,
                'current_status' => $attendance->at_status,
                'current_notes' => $attendance->at_notes
            ]);

            // Get daily schedule for this user and date with shift codes
            \Log::info('Looking up daily schedule', [
                'attendance_id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'date' => $attendance->at_date
            ]);
            
            $dailySchedule = DB::table('daily_schedules')
                ->leftJoin('shift_codes', 'daily_schedules.sc_id', '=', 'shift_codes.id')
                ->leftJoin('users', 'users.id', '=', 'daily_schedules.user_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->where('daily_schedules.user_id', $attendance->user_id)
                ->where('daily_schedules.ds_date', $attendance->at_date)
                ->where('daily_schedules.ds_status', 'scheduled')
                ->select([
                    'daily_schedules.*',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time',
                    'shift_codes.sc_code',
                    'shift_codes.sc_shift_name',
                    'users.ut_id',
                    'user_types.ut_name as user_type_name'
                ])
                ->first();
                
            \Log::info('Daily schedule lookup result', [
                'attendance_id' => $attendance->id,
                'schedule_found' => !is_null($dailySchedule),
                'schedule_data' => $dailySchedule ? [
                    'id' => $dailySchedule->id,
                    'sc_id' => $dailySchedule->sc_id,
                    'sc_code' => $dailySchedule->sc_code,
                    'sc_shift_name' => $dailySchedule->sc_shift_name,
                    'user_type' => $dailySchedule->user_type_name
                ] : null
            ]);

            \Log::info('Daily schedule lookup result', [
                'user_id' => $attendance->user_id,
                'date' => $attendance->at_date,
                'schedule_found' => !is_null($dailySchedule),
                'schedule_data' => $dailySchedule ? [
                    'ds_id' => $dailySchedule->id,
                    'sc_id' => $dailySchedule->sc_id,
                    'sc_start_time' => $dailySchedule->sc_start_time,
                    'sc_end_time' => $dailySchedule->sc_end_time,
                    'sc_code' => $dailySchedule->sc_code,
                    'sc_shift_name' => $dailySchedule->sc_shift_name
                ] : null
            ]);

            // Check if user has both time_in and time_out
            $hasTimeIn = !empty($attendance->at_time_in);
            $hasTimeOut = !empty($attendance->at_time_out);

            // Case 1: No schedule found
            if (!$dailySchedule) {
                $status = 'present';
                $notes = 'schedule unset - no daily schedule found for this date';
                
                \Log::info('No schedule found, setting status to present with notes: schedule unset', [
                    'attendance_id' => $attendance->id,
                    'user_id' => $attendance->user_id,
                    'date' => $attendance->at_date
                ]);
            }
            // Case 2: Only one time record (in or out)
            elseif (!$hasTimeIn || !$hasTimeOut) {
                $status = 'present';
                $notes = 'scan once - incomplete attendance record';
                
                \Log::info('Only one time record found, setting status to present with notes: scan once', [
                    'attendance_id' => $attendance->id,
                    'user_id' => $attendance->user_id,
                    'date' => $attendance->at_date,
                    'has_time_in' => $hasTimeIn,
                    'has_time_out' => $hasTimeOut
                ]);
            }
            // Case 3: Has schedule and both time records
            else {
                // Check shift code compatibility with user type using new pivot table structure
                if ($dailySchedule->sc_id && $dailySchedule->user_type_name) {
                    $shiftCodeModel = \App\Models\ShiftCode::find($dailySchedule->sc_id);
                    if ($shiftCodeModel) {
                        // Check compatibility using new pivot table relationship
                        $isCompatible = $shiftCodeModel->userTypes()
                            ->where('ut_name', $dailySchedule->user_type_name)
                            ->exists();
                        
                        // Also check legacy compatibility for backward compatibility
                        if (!$isCompatible) {
                            $isCompatible = $shiftCodeModel->isCompatibleWithUserTypeLegacy($dailySchedule->user_type_name);
                        }
                        
                        if (!$isCompatible) {
                            \Log::warning('Shift code not compatible with user type', [
                                'attendance_id' => $attendance->id,
                                'shift_code_id' => $dailySchedule->sc_id,
                                'shift_code' => $dailySchedule->sc_code,
                                'user_type' => $dailySchedule->user_type_name
                            ]);
                        }
                    }
                }
                
                $status = $this->calculateAttendanceStatus($attendance, $dailySchedule);
                $notes = $this->generateAttendanceNotes($attendance, $dailySchedule, $status);
                
                \Log::info('Schedule found and both times present, calculated status', [
                    'attendance_id' => $attendance->id,
                    'user_id' => $attendance->user_id,
                    'date' => $attendance->at_date,
                    'calculated_status' => $status,
                    'notes' => $notes,
                    'schedule_start' => $dailySchedule->sc_start_time,
                    'schedule_end' => $dailySchedule->sc_end_time,
                    'user_time_in' => $attendance->at_time_in,
                    'user_time_out' => $attendance->at_time_out
                ]);
            }

            // Update attendance record
            $updateData = [
                'at_status' => $status,
                'at_notes' => $notes,
                'updated_at' => now()
            ];
            
            // Update daily_schedule_id if schedule found
            if ($dailySchedule) {
                $updateData['daily_schedule_id'] = $dailySchedule->id;
                \Log::info('Adding daily_schedule_id to update data', [
                    'attendance_id' => $attendance->id,
                    'daily_schedule_id' => $dailySchedule->id,
                    'shift_code' => $dailySchedule->sc_code,
                    'shift_name' => $dailySchedule->sc_shift_name
                ]);
            } else {
                \Log::warning('No daily schedule found for attendance', [
                    'attendance_id' => $attendance->id,
                    'user_id' => $attendance->user_id,
                    'date' => $attendance->at_date
                ]);
            }
            
            \Log::info('Updating attendance record', [
                'attendance_id' => $attendance->id,
                'update_data' => $updateData
            ]);
            
            $updateResult = DB::table('attendance')
                ->where('id', $attendance->id)
                ->update($updateData);

            if ($updateResult) {
                \Log::info('Attendance status updated successfully', [
                    'attendance_id' => $attendance->id,
                    'new_status' => $status,
                    'new_notes' => $notes,
                    'daily_schedule_id_updated' => isset($updateData['daily_schedule_id']),
                    'new_daily_schedule_id' => $updateData['daily_schedule_id'] ?? null
                ]);
                return true;
            } else {
                \Log::warning('Failed to update attendance status', [
                    'attendance_id' => $attendance->id,
                    'update_data' => $updateData
                ]);
                return false;
            }

        } catch (\Exception $e) {
            \Log::error('Error processing single attendance status', [
                'attendance_id' => $attendance->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Calculate attendance status based on schedule
     */
    private function calculateAttendanceStatus($attendance, $dailySchedule)
    {
        try {
            $timeIn = $attendance->at_time_in;
            $timeOut = $attendance->at_time_out;
            $shiftStart = $dailySchedule->sc_start_time;
            $shiftEnd = $dailySchedule->sc_end_time;

            \Log::info('Calculating attendance status', [
                'time_in' => $timeIn,
                'time_out' => $timeOut,
                'shift_start' => $shiftStart,
                'shift_end' => $shiftEnd
            ]);

            // Convert times to minutes for comparison
            $timeInMinutes = $this->timeToMinutes($timeIn);
            $timeOutMinutes = $this->timeToMinutes($timeOut);
            $shiftStartMinutes = $this->timeToMinutes($shiftStart);
            $shiftEndMinutes = $this->timeToMinutes($shiftEnd);

            \Log::info('Time comparison details', [
                'time_in' => $timeIn,
                'time_in_minutes' => $timeInMinutes,
                'shift_start' => $shiftStart,
                'shift_start_minutes' => $shiftStartMinutes,
                'is_late' => $timeInMinutes > $shiftStartMinutes,
                'difference_minutes' => $timeInMinutes - $shiftStartMinutes
            ]);

            // Check for late arrival
            if ($timeInMinutes > $shiftStartMinutes) {
                \Log::info('User is LATE', [
                    'late_minutes' => $timeInMinutes - $shiftStartMinutes,
                    'time_in' => $timeIn,
                    'schedule_start' => $shiftStart
                ]);
                return 'late';
            }

            // Check for early leave
            if ($timeOutMinutes < $shiftEndMinutes) {
                return 'early_leave';
            }

            // Check for both late and early leave
            if ($timeInMinutes > $shiftStartMinutes && $timeOutMinutes < $shiftEndMinutes) {
                return 'late_early_leave';
            }

            // On time
            return 'present';

        } catch (\Exception $e) {
            \Log::error('Error calculating attendance status', [
                'error' => $e->getMessage()
            ]);
            return 'present'; // Default fallback
        }
    }

    /**
     * Generate attendance notes based on status
     */
    private function generateAttendanceNotes($attendance, $dailySchedule, $status)
    {
        try {
            $timeIn = $attendance->at_time_in;
            $timeOut = $attendance->at_time_out;
            $shiftStart = $dailySchedule->sc_start_time;
            $shiftEnd = $dailySchedule->sc_end_time;

            $baseNotes = "Uploaded from fingerprint";
            
            switch ($status) {
                case 'late':
                    $lateMinutes = $this->timeToMinutes($timeIn) - $this->timeToMinutes($shiftStart);
                    return "{$baseNotes} - Late {$lateMinutes} minutes (In: {$timeIn}, Schedule: {$shiftStart})";
                
                case 'early_leave':
                    $earlyMinutes = $this->timeToMinutes($shiftEnd) - $this->timeToMinutes($timeOut);
                    return "{$baseNotes} - Early leave {$earlyMinutes} minutes (Out: {$timeOut}, Schedule: {$shiftEnd})";
                
                case 'late_early_leave':
                    $lateMinutes = $this->timeToMinutes($timeIn) - $this->timeToMinutes($shiftStart);
                    $earlyMinutes = $this->timeToMinutes($shiftEnd) - $this->timeToMinutes($timeOut);
                    return "{$baseNotes} - Late {$lateMinutes} min, Early leave {$earlyMinutes} min";
                
                case 'present':
                default:
                    return "{$baseNotes} - On time (In: {$timeIn}, Out: {$timeOut})";
            }

        } catch (\Exception $e) {
            \Log::error('Error generating attendance notes', [
                'error' => $e->getMessage()
            ]);
            return "Uploaded from fingerprint - Status calculation error";
        }
    }

    /**
     * Convert time string to minutes for comparison
     */
    private function timeToMinutes($time)
    {
        if (empty($time)) return 0;
        
        $parts = explode(':', $time);
        // Handle both HH:MM and HH:MM:SS formats
        if (count($parts) < 2 || count($parts) > 3) return 0;
        
        return (int)$parts[0] * 60 + (int)$parts[1];
    }

    /**
     * Reprocess single attendance status
     */
    public function reprocessSingleAttendance(Request $request)
    {
        $this->validateAccess();
        
        try {
            $attendanceId = $request->get('attendance_id');
            $userId = $request->get('user_id');
            $date = $request->get('date');
            
            if (!$attendanceId && !$userId) {
                return response()->json(['error' => 'Please provide attendance_id or user_id and date']);
            }
            
            $query = DB::table('attendance');
            if ($attendanceId) {
                $query->where('id', $attendanceId);
            } else {
                $query->where('user_id', $userId)->where('at_date', $date);
            }
            
            $attendance = $query->first();
            
            if (!$attendance) {
                return response()->json(['error' => 'Attendance record not found']);
            }
            
            // Get daily schedule
            $dailySchedule = DB::table('daily_schedules')
                ->leftJoin('shift_codes', 'daily_schedules.sc_id', '=', 'shift_codes.id')
                ->leftJoin('users', 'users.id', '=', 'daily_schedules.user_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->where('daily_schedules.user_id', $attendance->user_id)
                ->where('daily_schedules.ds_date', $attendance->at_date)
                ->where('daily_schedules.ds_status', 'scheduled')
                ->select([
                    'daily_schedules.*',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time',
                    'shift_codes.sc_code',
                    'shift_codes.sc_shift_name',
                    'users.ut_id',
                    'user_types.ut_name as user_type_name'
                ])
                ->first();
            
            // Check shift code compatibility with user type using new pivot table structure
            if ($dailySchedule && $dailySchedule->sc_id && $dailySchedule->user_type_name) {
                $shiftCodeModel = \App\Models\ShiftCode::find($dailySchedule->sc_id);
                if ($shiftCodeModel) {
                    // Check compatibility using new pivot table relationship
                    $isCompatible = $shiftCodeModel->userTypes()
                        ->where('ut_name', $dailySchedule->user_type_name)
                        ->exists();
                    
                    // Also check legacy compatibility for backward compatibility
                    if (!$isCompatible) {
                        $isCompatible = $shiftCodeModel->isCompatibleWithUserTypeLegacy($dailySchedule->user_type_name);
                    }
                    
                    if (!$isCompatible) {
                        \Log::warning('Shift code not compatible with user type in reprocessSingleAttendance', [
                            'attendance_id' => $attendance->id,
                            'shift_code_id' => $dailySchedule->sc_id,
                            'shift_code' => $dailySchedule->sc_code,
                            'user_type' => $dailySchedule->user_type_name
                        ]);
                    }
                }
            }
            
            // Calculate status manually
            $calculatedStatus = $this->calculateAttendanceStatus((object)$attendance, $dailySchedule);
            $notes = $this->generateAttendanceNotes((object)$attendance, $dailySchedule, $calculatedStatus);
            
            // Update attendance record
            $updateResult = DB::table('attendance')
                ->where('id', $attendance->id)
                ->update([
                    'at_status' => $calculatedStatus,
                    'at_notes' => $notes,
                    'updated_at' => now()
                ]);
            
            if ($dailySchedule) {
                DB::table('attendance')
                    ->where('id', $attendance->id)
                    ->update(['daily_schedule_id' => $dailySchedule->id]);
            }
            
            $debugInfo = [
                'attendance' => [
                    'id' => $attendance->id,
                    'user_id' => $attendance->user_id,
                    'date' => $attendance->at_date,
                    'time_in' => $attendance->at_time_in,
                    'time_out' => $attendance->at_time_out,
                    'old_status' => $attendance->at_status,
                    'new_status' => $calculatedStatus,
                    'old_notes' => $attendance->at_notes,
                    'new_notes' => $notes
                ],
                'daily_schedule' => $dailySchedule ? [
                    'id' => $dailySchedule->id,
                    'sc_id' => $dailySchedule->sc_id,
                    'sc_start_time' => $dailySchedule->sc_start_time,
                    'sc_end_time' => $dailySchedule->sc_end_time,
                    'sc_code' => $dailySchedule->sc_code,
                    'sc_shift_name' => $dailySchedule->sc_shift_name
                ] : null,
                'calculation' => [
                    'time_in_minutes' => $this->timeToMinutes($attendance->at_time_in),
                    'shift_start_minutes' => $dailySchedule ? $this->timeToMinutes($dailySchedule->sc_start_time) : null,
                    'is_late' => $dailySchedule ? ($this->timeToMinutes($attendance->at_time_in) > $this->timeToMinutes($dailySchedule->sc_start_time)) : null,
                    'late_minutes' => $dailySchedule ? ($this->timeToMinutes($attendance->at_time_in) - $this->timeToMinutes($dailySchedule->sc_start_time)) : null,
                    'update_result' => $updateResult
                ]
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Attendance status reprocessed successfully',
                'data' => $debugInfo
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error reprocessing single attendance status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Debug method to test attendance status calculation
     */
    public function debugAttendanceStatus(Request $request)
    {
        $this->validateAccess();
        
        try {
            $attendanceId = $request->get('attendance_id');
            $userId = $request->get('user_id');
            $date = $request->get('date');
            
            if (!$attendanceId && !$userId) {
                return response()->json(['error' => 'Please provide attendance_id or user_id and date']);
            }
            
            $query = DB::table('attendance');
            if ($attendanceId) {
                $query->where('id', $attendanceId);
            } else {
                $query->where('user_id', $userId)->where('at_date', $date);
            }
            
            $attendance = $query->first();
            
            if (!$attendance) {
                return response()->json(['error' => 'Attendance record not found']);
            }
            
            // Get daily schedule
            $dailySchedule = DB::table('daily_schedules')
                ->leftJoin('shift_codes', 'daily_schedules.sc_id', '=', 'shift_codes.id')
                ->leftJoin('users', 'users.id', '=', 'daily_schedules.user_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->where('daily_schedules.user_id', $attendance->user_id)
                ->where('daily_schedules.ds_date', $attendance->at_date)
                ->where('daily_schedules.ds_status', 'scheduled')
                ->select([
                    'daily_schedules.*',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time',
                    'shift_codes.sc_code',
                    'shift_codes.sc_shift_name',
                    'users.ut_id',
                    'user_types.ut_name as user_type_name'
                ])
                ->first();
            
            // Calculate status manually
            $calculatedStatus = $this->calculateAttendanceStatus($attendance, $dailySchedule);
            $notes = $this->generateAttendanceNotes($attendance, $dailySchedule, $calculatedStatus);
            
            $debugInfo = [
                'attendance' => [
                    'id' => $attendance->id,
                    'user_id' => $attendance->user_id,
                    'date' => $attendance->at_date,
                    'time_in' => $attendance->at_time_in,
                    'time_out' => $attendance->at_time_out,
                    'current_status' => $attendance->at_status,
                    'current_notes' => $attendance->at_notes
                ],
                'daily_schedule' => $dailySchedule ? [
                    'id' => $dailySchedule->id,
                    'sc_id' => $dailySchedule->sc_id,
                    'sc_start_time' => $dailySchedule->sc_start_time,
                    'sc_end_time' => $dailySchedule->sc_end_time,
                    'sc_code' => $dailySchedule->sc_code,
                    'sc_shift_name' => $dailySchedule->sc_shift_name
                ] : null,
                'calculation' => [
                    'time_in_minutes' => $this->timeToMinutes($attendance->at_time_in),
                    'shift_start_minutes' => $dailySchedule ? $this->timeToMinutes($dailySchedule->sc_start_time) : null,
                    'is_late' => $dailySchedule ? ($this->timeToMinutes($attendance->at_time_in) > $this->timeToMinutes($dailySchedule->sc_start_time)) : null,
                    'late_minutes' => $dailySchedule ? ($this->timeToMinutes($attendance->at_time_in) - $this->timeToMinutes($dailySchedule->sc_start_time)) : null,
                    'calculated_status' => $calculatedStatus,
                    'calculated_notes' => $notes
                ]
            ];
            
            return response()->json($debugInfo);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Debug failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Reprocess attendance status for existing records
     */
    public function reprocessAttendanceStatus(Request $request)
    {
        $this->validateAccess();
        
        try {
            $startDate = $request->get('start_date', date('Y-m-d'));
            $endDate = $request->get('end_date', date('Y-m-d'));
            
            \Log::info('Reprocessing attendance status', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            
            $result = $this->processAttendanceStatusAfterUpload($startDate, $endDate);
            
            if ($result['success']) {
                $message = "Reprocessing completed. Processed: {$result['processed_count']}, Errors: {$result['error_count']}";
                \Log::info('Reprocessing completed successfully', $result);
                return back()->with('success', $message);
            } else {
                $message = "Reprocessing failed: " . $result['error'];
                \Log::error('Reprocessing failed', $result);
                return back()->with('error', $message);
            }
            
        } catch (\Exception $e) {
            \Log::error('Error reprocessing attendance status: ' . $e->getMessage());
            return back()->with('error', 'Error reprocessing: ' . $e->getMessage());
        }
    }

    /**
     * Export summary report to PDF
     */
    public function exportSummaryToPDF(Request $request)
    {
        $this->validateAccess();
        
        try {
            $startDate = $request->get('start_date', date('Y-m-d'));
            $endDate = $request->get('end_date', date('Y-m-d'));
            $dateFilter = $request->get('date_filter', 'this_month');
            
            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }
            
            $summaryData = $this->getAttendanceSummary($startDate, $endDate, $request->get('division_id'));
            
            // Apply search filter if provided
            if ($request->filled('search')) {
                $search = $request->get('search');
                $summaryData = $summaryData->filter(function($item) use ($search) {
                    return stripos($item->u_name, $search) !== false || 
                           stripos($item->u_nip, $search) !== false;
                });
            }
            
            // Generate HTML for PDF
            $html = $this->generateSummaryReportHTML($summaryData, $request);
            
            // Generate filename
            $filename = 'attendance_summary_' . date('Y-m-d_H-i-s');
            if ($request->get('division_id')) {
                $division = DB::table('user_divisions')->find($request->get('division_id'));
                $filename .= '_' . ($division ? str_replace(' ', '_', $division->ud_name) : 'all');
            }
            if ($request->get('start_date') && $request->get('end_date')) {
                $filename .= '_' . $request->get('start_date') . '_to_' . $request->get('end_date');
            }
            $filename .= '.pdf';

            $pdf = \PDF::loadHTML($html);
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Export summary report PDF error: ' . $e->getMessage());
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
            <title>Attendance Summary Report</title>
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
                <h1>LAPORAN REKAPITULASI KEHADIRAN</h1>
                <p>Periode: ' . date('d/m/Y', strtotime($request->get('start_date', date('Y-m-d')))) . ' - ' . date('d/m/Y', strtotime($request->get('end_date', date('Y-m-d')))) . '</p>
                <p>Dibuat pada: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-number">' . $summaryData->count() . '</div>
                    <div class="stat-label">Total Staff</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . $summaryData->sum('total_shifts') . '</div>
                    <div class="stat-label">Total Shift</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . $summaryData->sum('total_libur') . '</div>
                    <div class="stat-label">Total Libur</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . $summaryData->sum('late_days') . '</div>
                    <div class="stat-label">Total Terlambat</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . $summaryData->sum('alpha_days') . '</div>
                    <div class="stat-label">Total Alpha</div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Divisi</th>
                        <th>Jenis Kerja</th>
                        <th class="text-center">Total Shift</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Libur</th>
                        <th class="text-center">Cuti</th>
                        <th class="text-center">Sakit</th>
                        <th class="text-center">Terlambat</th>
                        <th class="text-center">Alpha</th>
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
                        <td>' . ($item->work_type ?? '-') . '</td>
                        <td class="text-center">' . ($item->total_shifts ?? 0) . '</td>
                        <td class="text-center">' . ($item->present_days ?? 0) . '</td>
                        <td class="text-center">' . ($item->total_libur ?? 0) . '</td>
                        <td class="text-center">' . ($item->leave_days ?? 0) . '</td>
                        <td class="text-center">' . ($item->sick_days ?? 0) . '</td>
                        <td class="text-center">' . ($item->late_days ?? 0) . '</td>
                        <td class="text-center">' . ($item->alpha_days ?? 0) . '</td>
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
     * Get summary report datatables
     */
    public function getSummaryReportDatatables(Request $request)
    {
        \Log::info('Summary Report Datatables - Method called', [
            'is_ajax' => request()->ajax(),
            'method' => $request->method(),
            'url' => $request->url(),
            'headers' => $request->headers->all()
        ]);
        
        if(request()->ajax()) {
            try {
                $this->validateAccess();
                \Log::info('Summary Report Datatables - Access validated');
                
                // Debug: Log request parameters
                \Log::info('Summary Report Datatables Request', [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'division_id' => $request->division_id,
                    'search' => $request->search,
                    'draw' => $request->draw,
                    'start' => $request->start,
                    'length' => $request->length
                ]);
            
            } catch (\Exception $e) {
                \Log::error('Summary Report Datatables - Error in access validation', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['error' => 'Access denied'], 403);
            }
            
            // Get date range
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $divisionId = $request->get('division_id');
            
            // Use the same method as summaryReport to get data
            $summaryData = $this->getAttendanceSummary($startDate, $endDate, $divisionId);
            
            // Apply search filter if provided
            if ($request->filled('search')) {
                $search = $request->get('search');
                $summaryData = $summaryData->filter(function($item) use ($search) {
                    return stripos($item->u_name, $search) !== false || 
                           stripos($item->u_nip, $search) !== false;
                });
            }
            
            // Debug: Log data before DataTables processing
            \Log::info('Summary Report Data Before DataTables', [
                'total_count' => $summaryData->count(),
                'first_item' => $summaryData->first()
            ]);
            
            \Log::info('Summary Report - About to create DataTable result');
            
            $result = datatables()->of($summaryData)
                ->addIndexColumn()
                ->editColumn('u_nip', function($row) {
                    return $row->u_nip ?? '-';
                })
                ->editColumn('u_name', function($row) {
                    return $row->u_name ?? '-';
                })
                ->editColumn('position_name', function($row) {
                    return $row->position_name ?? '-';
                })
                ->editColumn('division_name', function($row) {
                    return $row->division_name ?? '-';
                })
                ->editColumn('work_type', function($row) {
                    $workType = $row->work_type ?? '-';
                    $badgeClass = 'secondary';
                    if ($workType === 'Full Time') $badgeClass = 'primary';
                    else if ($workType === 'Part Time') $badgeClass = 'warning';
                    return '<span class="badge bg-' . $badgeClass . '">' . $row->work_type . '</span>';
                })
                ->editColumn('total_shifts', function($row) {
                    return $row->total_shifts ?? 0;
                })
                ->editColumn('present_days', function($row) {
                    return $row->present_days ?? 0;
                })
                ->editColumn('leave_days', function($row) {
                    return $row->leave_days ?? 0;
                })
                ->editColumn('sick_days', function($row) {
                    return $row->sick_days ?? 0;
                })
                ->editColumn('late_days', function($row) {
                    return $row->late_days ?? 0;
                })
                ->editColumn('alpha_days', function($row) {
                    return $row->alpha_days ?? 0;
                })
                ->rawColumns(['work_type'])
                ->make(true);

            return $result;
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }

}
