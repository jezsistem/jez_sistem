<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DailySchedule;
use App\Models\ShiftCode;
use App\Models\User;
use App\Models\UserDivision;
use App\Models\WebConfig;
use App\Models\UserActivity;
use Carbon\Carbon;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyScheduleController extends Controller
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
        $user = Auth::user();
        if (!$user) {
            return [];
        }
        
        $ma_id = DB::table('user_menu_accesses')->select('ma_id')
        ->where('u_id', $user->id)->get();
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
        // Temporarily comment out for testing
        // $this->validateAccess();
        
        $title = 'Daily Schedules';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();

        $dailySchedule = new DailySchedule();
        $schedules = $dailySchedule->getScheduleByDateRange(
            $request->get('start_date', date('Y-m-d')),
            $request->get('end_date', date('Y-m-d')),
            $request->get('user_id'),
            $request->get('division_id'),
            $request->get('status')
        );

        $users = DB::table('users')->where('u_delete', '!=', '1')->get();
        $divisions = DB::table('user_divisions')->where('ud_status', 'active')->get();
        $shiftCodes = DB::table('shift_codes')->where('sc_status', '!=', 'deleted')->get();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title ?? 'Daily Schedules',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.daily_schedule.index', compact('schedules', 'users', 'divisions', 'shiftCodes', 'data'));
    }

    public function create()
    {
        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $users = User::where('u_delete', '0')->orderBy('u_name')->get();
        $shiftCode = new ShiftCode();
        $shiftCodes = $shiftCode->getActiveShiftCodes();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        return view('app.daily_schedule.create', compact('users', 'shiftCodes', 'data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'sc_id' => 'required|exists:shift_codes,id',
            'ds_date' => 'required|date',
            'ds_start_time' => 'nullable|date_format:H:i',
            'ds_end_time' => 'nullable|date_format:H:i',
            'ds_notes' => 'nullable|string',
        ]);

        // Get user's division
        $user = DB::table('users')->where('id', $request->user_id)->first();
        $ud_id = $user->ud_id ?? null;

        // Check if schedule already exists for this user and date
        $existingSchedule = DailySchedule::where('user_id', $request->user_id)
            ->where('ds_date', $request->ds_date)
            ->first();

        if ($existingSchedule) {
            return back()->with('error', 'Jadwal untuk karyawan ini pada tanggal tersebut sudah ada');
        }

        $data = [
            'user_id' => $request->user_id,
            'ud_id' => $ud_id,
            'sc_id' => $request->sc_id,
            'ds_date' => $request->ds_date,
            'ds_start_time' => $request->ds_start_time,
            'ds_end_time' => $request->ds_end_time,
            'ds_status' => 'scheduled',
            'ds_notes' => $request->ds_notes,
            'created_by' => auth()->user()->u_name ?? 'system',
        ];

        $dailySchedule = new DailySchedule();
        $result = $dailySchedule->storeData('add', null, $data);

        if ($result) {
            return redirect()->route('daily-schedules.index')->with('success', 'Jadwal harian berhasil ditambahkan');
        } else {
            return back()->with('error', 'Gagal menambahkan jadwal harian');
        }
    }

    public function show($id)
    {
        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $schedule = DailySchedule::with(['user', 'userDivision', 'shiftCode'])->findOrFail($id);

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        return view('app.daily_schedule.show', compact('schedule', 'data'));
    }

    public function edit($id)
    {
        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $schedule = DailySchedule::findOrFail($id);
        $users = User::where('u_delete', '0')->orderBy('u_name')->get();
        $userDivision = new UserDivision();
        $divisions = $userDivision->getActiveDivisions();
        $shiftCode = new ShiftCode();
        $shiftCodes = $shiftCode->getActiveShiftCodes();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        return view('app.daily_schedule.edit', compact('schedule', 'users', 'divisions', 'shiftCodes', 'data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ud_id' => 'nullable|exists:user_divisions,id',
            'sc_id' => 'required|exists:shift_codes,id',
            'ds_date' => 'required|date',
            'ds_start_time' => 'nullable|date_format:H:i',
            'ds_end_time' => 'nullable|date_format:H:i',
            'ds_notes' => 'nullable|string',
        ]);

        // Check if schedule already exists for this user and date (excluding current record)
        $existingSchedule = DailySchedule::where('user_id', $request->user_id)
            ->where('ds_date', $request->ds_date)
            ->where('id', '!=', $id)
            ->first();

        if ($existingSchedule) {
            return back()->with('error', 'Jadwal untuk karyawan ini pada tanggal tersebut sudah ada');
        }

        $data = [
            'user_id' => $request->user_id,
            'ud_id' => $request->ud_id,
            'sc_id' => $request->sc_id,
            'ds_date' => $request->ds_date,
            'ds_start_time' => $request->ds_start_time,
            'ds_end_time' => $request->ds_end_time,
            'ds_notes' => $request->ds_notes,
            'updated_by' => auth()->user()->u_name ?? 'system',
        ];

        $dailySchedule = new DailySchedule();
        $result = $dailySchedule->storeData('edit', $id, $data);

        if ($result) {
            return redirect()->route('daily-schedules.index')->with('success', 'Jadwal harian berhasil diperbarui');
        } else {
            return back()->with('error', 'Gagal memperbarui jadwal harian');
        }
    }

    public function destroy($id)
    {
        $this->validateAccess();

        try {
            $dailySchedule = DailySchedule::findOrFail($id);
        $result = $dailySchedule->deleteData($id);

        if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Schedule deleted successfully'
                ]);
        } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete schedule'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function bulkCreate()
    {
        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $users = User::where('u_delete', '0')->orderBy('u_name')->get();
        $shiftCode = new ShiftCode();
        $shiftCodes = $shiftCode->getActiveShiftCodes();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        return view('app.daily_schedule.bulk_create', compact('users', 'shiftCodes', 'data'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'sc_id' => 'required|exists:shift_codes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'ds_notes' => 'nullable|string',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $schedules = [];

        // Get users with their divisions
        $users = DB::table('users')->whereIn('id', $request->user_ids)->get();

        // Generate schedules for each user and date in range
        foreach ($users as $user) {
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Skip weekends if requested
            if ($request->has('skip_weekends') && in_array($date->dayOfWeek, [0, 6])) {
                continue;
            }

            // Check if schedule already exists for this user and date
                $existingSchedule = DailySchedule::where('user_id', $user->id)
                ->where('ds_date', $date->format('Y-m-d'))
                ->first();

            if (!$existingSchedule) {
                $schedules[] = [
                        'user_id' => $user->id,
                        'ud_id' => $user->ud_id, // Get division from user data
                    'sc_id' => $request->sc_id,
                    'ds_date' => $date->format('Y-m-d'),
                    'ds_start_time' => $request->ds_start_time,
                    'ds_end_time' => $request->ds_end_time,
                    'ds_status' => 'scheduled',
                    'ds_notes' => $request->ds_notes,
                    'created_by' => auth()->user()->u_name ?? 'system',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                }
            }
        }

        if (empty($schedules)) {
            return back()->with('error', 'Semua jadwal untuk periode tersebut sudah ada');
        }

        $dailySchedule = new DailySchedule();
        $result = $dailySchedule->bulkInsertSchedules($schedules);

        if ($result) {
            return redirect()->route('daily-schedules.index')->with('success', 'Jadwal harian berhasil ditambahkan secara massal');
        } else {
            return back()->with('error', 'Gagal menambahkan jadwal harian secara massal');
        }
    }

    public function createRange()
    {
        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $users = User::where('u_delete', '0')->orderBy('u_name')->get();
        $shiftCode = new ShiftCode();
        $shiftCodes = $shiftCode->getActiveShiftCodes();

        $data = [
            'title' => $title,
            'subtitle' => 'Create Daily Schedule Range',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        return view('app.daily_schedule.create_range', compact('users', 'shiftCodes', 'data'));
    }

    public function storeRange(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'sc_id' => 'required|exists:shift_codes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'ds_start_time' => 'nullable|date_format:H:i',
            'ds_end_time' => 'nullable|date_format:H:i',
            'ds_notes' => 'nullable|string',
        ]);

        // Get user's division
        $user = DB::table('users')->where('id', $request->user_id)->first();
        $ud_id = $user->ud_id ?? null;

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $dates = [];

        // Generate dates between start and end date
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($dates as $date) {
            // Check if schedule already exists for this user and date
            $existingSchedule = DailySchedule::where('user_id', $request->user_id)
                ->where('ds_date', $date)
                ->first();

            if ($existingSchedule) {
                $errorCount++;
                continue;
            }

            $data = [
                'user_id' => $request->user_id,
                'ud_id' => $ud_id,
                'sc_id' => $request->sc_id,
                'ds_date' => $date,
                'ds_start_time' => $request->ds_start_time,
                'ds_end_time' => $request->ds_end_time,
                'ds_status' => 'scheduled',
                'ds_notes' => $request->ds_notes,
                'created_by' => auth()->user()->u_name ?? 'system',
            ];

            $dailySchedule = new DailySchedule();
            $result = $dailySchedule->storeData('add', null, $data);

            if ($result) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        if ($successCount > 0) {
            $message = "Berhasil menambahkan {$successCount} jadwal harian";
            if ($errorCount > 0) {
                $message .= " ({$errorCount} jadwal gagal karena sudah ada)";
            }
            return redirect()->route('daily-schedules.index')->with('success', $message);
        } else {
            return back()->with('error', 'Gagal menambahkan jadwal harian');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'ds_status' => 'required|in:scheduled,completed,absent,late,early_leave',
        ]);

        $dailySchedule = new DailySchedule();
        $result = $dailySchedule->updateScheduleStatus($id, $request->ds_status);

        if ($result) {
            return redirect()->route('daily-schedules.index')->with('success', 'Status jadwal berhasil diperbarui');
        } else {
            return back()->with('error', 'Gagal memperbarui status jadwal');
        }
    }

    public function export(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d', strtotime('+7 days')));
        $userId = $request->get('user_id');
        $divisionId = $request->get('division_id');

        $dailySchedule = new DailySchedule();
        $schedules = $dailySchedule->getScheduleByDateRange($startDate, $endDate, $userId, $divisionId);

        // Export logic here
        return response()->json($schedules);
    }

    public function getDatatables(Request $request)
    {
        if($request->ajax()) {
        $query = DB::table('daily_schedules')
            ->select([
                'daily_schedules.id',
                'daily_schedules.ds_date',
                'daily_schedules.ds_start_time',
                'daily_schedules.ds_end_time',
                'daily_schedules.ds_status',
                    'daily_schedules.ds_notes',
                'users.u_name',
                'users.u_nip',
                'user_divisions.ud_name',
                'shift_codes.sc_code',
                    'shift_codes.sc_shift_name',
                    'shift_codes.sc_start_time as shift_start_time',
                    'shift_codes.sc_end_time as shift_end_time'
            ])
            ->leftJoin('users', 'users.id', '=', 'daily_schedules.user_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
            ->where('daily_schedules.ds_status', '!=', 'deleted');

            // Apply filters
            if ($request->filled('employee_filter')) {
                $query->where('daily_schedules.user_id', $request->employee_filter);
            }
            if ($request->filled('division_filter')) {
                $query->where('users.ud_id', $request->division_filter);
            }
            if ($request->filled('shift_filter')) {
                $query->where('daily_schedules.sc_id', $request->shift_filter);
            }
            if ($request->filled('status_filter')) {
                $query->where('daily_schedules.ds_status', $request->status_filter);
            }
            if ($request->filled('start_date_filter')) {
                $query->where('daily_schedules.ds_date', '>=', $request->start_date_filter);
            }
            if ($request->filled('end_date_filter')) {
                $query->where('daily_schedules.ds_date', '<=', $request->end_date_filter);
            }
            
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('users.u_name', 'like', '%' . $search . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $search . '%');
                });
            }

            // Order by division, then date, then employee name
            $query->orderBy('user_divisions.ud_name')
                  ->orderBy('daily_schedules.ds_date')
                  ->orderBy('users.u_name');

        return datatables()->of($query)
            ->addIndexColumn()
                ->addColumn('employee_name', function($row) {
                    return $row->u_name ?: '-';
                })
                ->addColumn('division_name', function($row) {
                    return $row->ud_name ?: '-';
                })
                ->addColumn('shift_name', function($row) {
                    return $row->sc_code ?: '-';
                })
                ->addColumn('shift_time', function($row) {
                    if ($row->shift_start_time && $row->shift_end_time) {
                        return date('H:i', strtotime($row->shift_start_time)) . ' - ' . date('H:i', strtotime($row->shift_end_time));
                    }
                    return '-';
                })
            ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm">';
                    $btn .= '<a href="'.route('daily-schedules.show', $row->id).'" class="btn btn-info btn-xs" title="View Details"><i class="ki-outline ki-eye"></i></a>';
                    $btn .= '<a href="'.route('daily-schedules.edit', $row->id).'" class="btn btn-warning btn-xs" title="Edit Schedule"><i class="ki-outline ki-notepad-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-xs" onclick="deleteSchedule('.$row->id.')" title="Delete Schedule"><i class="ki-outline ki-trash-square"></i></button>';
                    $btn .= '</div>';
                return $btn;
            })
                ->editColumn('ds_date', function($row) {
                    return date('d/m/Y', strtotime($row->ds_date));
                })
                ->editColumn('ds_start_time', function($row) {
                    // If shift has time, show shift time, otherwise show schedule time
                    if ($row->shift_start_time) {
                        return date('H:i', strtotime($row->shift_start_time));
                    }
                    return $row->ds_start_time ? date('H:i', strtotime($row->ds_start_time)) : '-';
                })
                ->editColumn('ds_end_time', function($row) {
                    // If shift has time, show shift time, otherwise show schedule time
                    if ($row->shift_end_time) {
                        return date('H:i', strtotime($row->shift_end_time));
                    }
                    return $row->ds_end_time ? date('H:i', strtotime($row->ds_end_time)) : '-';
                })
                ->editColumn('ds_status', function($row) {
                    $statusClass = '';
                    $statusText = '';
                    
                    switch($row->ds_status) {
                        case 'scheduled':
                            $statusClass = 'badge badge-primary';
                            $statusText = 'Scheduled';
                            break;
                        case 'completed':
                            $statusClass = 'badge badge-success';
                            $statusText = 'Completed';
                            break;
                        case 'absent':
                            $statusClass = 'badge badge-danger';
                            $statusText = 'Absent';
                            break;
                        case 'late':
                            $statusClass = 'badge badge-warning';
                            $statusText = 'Late';
                            break;
                        default:
                            $statusClass = 'badge badge-secondary';
                            $statusText = ucfirst($row->ds_status);
                    }
                    
                    return '<span class="' . $statusClass . '">' . $statusText . '</span>';
                })
                ->rawColumns(['action', 'ds_status'])
            ->make(true);
        }
    }

    public function getStatistics(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d', strtotime('+7 days')));
        
        $dailySchedule = new DailySchedule();
        $schedules = $dailySchedule->getScheduleByDateRange($startDate, $endDate);
        
        $totalSchedules = $schedules->count();
        $scheduledSchedules = $schedules->where('ds_status', 'scheduled')->count();
        $completedSchedules = $schedules->where('ds_status', 'completed')->count();
        
        return response()->json([
            'total' => $totalSchedules,
            'scheduled' => $scheduledSchedules,
            'completed' => $completedSchedules
        ]);
    }

    /**
     * Menampilkan halaman input jadwal mingguan
     */
    public function weeklySchedule(Request $request)
    {
        $this->validateAccess();
        Log::info('Weekly Schedule');
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        
        // Get current user's position and division
        $currentUser = DB::table('users')
            ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->select('users.*', 'user_positions.up_code', 'user_positions.up_level', 'user_divisions.ud_name as current_division_name')
            ->where('users.id', Auth::user()->id)
            ->first();
        
        // Get parameters from request
        $divisionId = $request->get('division_id');
        $search = $request->get('search'); // Search by name or NIP
        
        // Get divisions (only show if user is director/manager)
        $divisions = collect();
        if (in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER'])) {
            $divisions = DB::table('user_divisions')
                ->where('ud_status', 'active')
                ->orderBy('ud_name')
                ->get();
        }
            
        // Get shift codes
        $shiftCodes = DB::table('shift_codes')
            ->where('sc_status', 'active')
            ->orderBy('sc_code')
            ->get();
            
        // Get users with their divisions and user types
        $users = DB::table('users')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
            ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
            ->select('users.id', 'users.u_nip', 'users.u_name', 'user_divisions.ud_name', 'user_types.ut_name', 'user_positions.up_level')
            ->where('users.u_delete', '0')
            ->whereNotNull('users.u_nip');
            
        // Apply division filter based on user position
        if (in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER'])) {
            // Director/Manager can see all staff with division filter
            if ($divisionId) {
                $users->where('users.ud_id', $divisionId);
            }
        } else {
            // Supervisor/Staff can only see staff from same division
            $users->where('users.ud_id', $currentUser->ud_id);
            
            // Filter staff based on position hierarchy
            if ($currentUser->up_code === 'SUPERVISOR') {
                // Supervisor can only see staff (level 1) and other supervisors (level 2)
                $users->where('user_positions.up_level', '<=', 2);
            } elseif ($currentUser->up_code === 'STAFF') {
                // Staff can only see other staff (level 1)
                $users->where('user_positions.up_level', '=', 1);
            }
        }
        
        // Apply search filter if provided
        if ($search) {
            $users->where(function($query) use ($search) {
                $query->where('users.u_name', 'like', '%' . $search . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $search . '%');
            });
        }
        
        $users = $users->orderBy('user_divisions.ud_name')
            ->orderBy('users.u_name')
            ->get();
            
        // Group shift codes by user type for easier frontend access
        $shiftCodesByType = [
            'ALL' => DB::table('shift_codes')
                ->where('sc_status', 'active')
                ->where('sc_type', 'ALL')
                ->orderBy('sc_code')
                ->get(),
            'FULL TIME' => DB::table('shift_codes')
                ->where('sc_status', 'active')
                ->whereIn('sc_type', ['ALL', 'Full Time'])
                ->orderBy('sc_code')
                ->get(),
            'PART TIME' => DB::table('shift_codes')
                ->where('sc_status', 'active')
                ->whereIn('sc_type', ['ALL', 'Part Time'])
                ->orderBy('sc_code')
                ->get(),
            'PART FULL' => DB::table('shift_codes')
                ->where('sc_status', 'active')
                ->whereIn('sc_type', ['ALL', 'Part Full'])
                ->orderBy('sc_code')
                ->get(),
        ];
        
        // Get existing schedules for this week to ensure compatibility
        $existingSchedules = DB::table('daily_schedules')
            ->leftJoin('users', 'users.id', '=', 'daily_schedules.user_id')
            ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
            ->select('daily_schedules.user_id', 'daily_schedules.sc_id', 'shift_codes.sc_code', 'shift_codes.sc_type')
            ->whereBetween('daily_schedules.ds_date', [
                date('Y-m-d', strtotime('monday this week')),
                date('Y-m-d', strtotime('sunday this week'))
            ])
            ->get();
            
        $data = [
            'title' => 'JEZ SYSTEM',
            'subtitle' => 'Weekly Schedule Input',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        
        return view('app.daily_schedule.weekly_schedule', compact(
            'divisions', 
            'shiftCodes', 
            'shiftCodesByType', 
            'existingSchedules', 
            'users', 
            'data',
            'currentUser',
            'search'
        ));
    }

    /**
     * Menampilkan laporan jadwal mingguan
     */
    public function weeklyReport(Request $request)
    {
        $this->validateAccess();
        
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        
        // Get parameters from request
        $startDate = $request->get('start_date', date('Y-m-d', strtotime('monday this week')));
        $endDate = $request->get('end_date', date('Y-m-d', strtotime('sunday this week')));
        $divisionId = $request->get('division_id');
        $shiftId = $request->get('shift_id');
        $userName = $request->get('user_name');
        $dateFilter = $request->get('date_filter', 'this_week');
        
        // Process date filter if provided
        if ($dateFilter && $dateFilter !== 'custom') {
            $dateRange = $this->getDateRangeFromFilter($dateFilter);
            $startDate = $dateRange['startDate'];
            $endDate = $dateRange['endDate'];
        } else {
            // Ensure we have a full week (Monday to Sunday)
            $startDate = date('Y-m-d', strtotime('monday', strtotime($startDate)));
            $endDate = date('Y-m-d', strtotime('sunday', strtotime($startDate)));
        }
        
        // Get divisions for filter
        $divisions = DB::table('user_divisions')
            ->where('ud_status', 'active')
            ->orderBy('ud_name')
            ->get();
            
        // Get shift codes for filter
        $shiftCodes = DB::table('shift_codes')
            ->where('sc_status', 'active')
            ->orderBy('sc_code')
            ->get();
        
        // Get schedule data grouped by division
        $scheduleQuery = DB::table('daily_schedules')
            ->leftJoin('users', 'users.id', '=', 'daily_schedules.user_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
            ->select([
                'daily_schedules.*',
                'users.u_nip',
                'users.u_name',
                'user_divisions.ud_name',
                'shift_codes.sc_code',
                'shift_codes.sc_shift_name',
                'shift_codes.sc_start_time',
                'shift_codes.sc_end_time'
            ])
            ->whereBetween('daily_schedules.ds_date', [$startDate, $endDate])
            ->where('users.u_delete', '0')
            ->whereNotNull('users.u_nip');
            
        // Apply filters
        if ($divisionId) {
            $scheduleQuery->where('users.ud_id', $divisionId);
        }
        
        if ($shiftId) {
            $scheduleQuery->where('daily_schedules.sc_id', $shiftId);
        }
        
        if ($userName) {
            $scheduleQuery->where('users.u_name', 'like', '%' . $userName . '%');
        }
        
        $schedules = $scheduleQuery->orderBy('user_divisions.ud_name')
            ->orderBy('users.u_name')
            ->orderBy('daily_schedules.ds_date')
            ->get();
        
        // Group schedules by division and user
        $groupedSchedules = [];
        foreach ($schedules as $schedule) {
            $divisionName = $schedule->ud_name ?: 'No Division';
            $userId = $schedule->user_id;
            $date = $schedule->ds_date;
            
            if (!isset($groupedSchedules[$divisionName])) {
                $groupedSchedules[$divisionName] = [];
            }
            
            if (!isset($groupedSchedules[$divisionName][$userId])) {
                $groupedSchedules[$divisionName][$userId] = [
                    'user_id' => $userId,
                    'u_nip' => $schedule->u_nip,
                    'u_name' => $schedule->u_name,
                    'ud_name' => $schedule->ud_name,
                    'schedules' => []
                ];
            }
            
            $groupedSchedules[$divisionName][$userId]['schedules'][$date] = [
                'sc_code' => $schedule->sc_code,
                'sc_shift_name' => $schedule->sc_shift_name,
                'sc_start_time' => $schedule->sc_start_time,
                'sc_end_time' => $schedule->sc_end_time,
                'ds_start_time' => $schedule->ds_start_time,
                'ds_end_time' => $schedule->ds_end_time
            ];
        }
        
        // Generate dates for the week
        $weekDates = [];
        for ($i = 0; $i < 7; $i++) {
            $weekDates[] = date('Y-m-d', strtotime($startDate . " +{$i} days"));
        }
        
        $data = [
            'title' => 'JEZ SYSTEM',
            'subtitle' => 'Weekly Schedule Report',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        
        return view('app.daily_schedule.weekly_report', compact(
            'groupedSchedules', 
            'weekDates', 
            'startDate', 
            'endDate',
            'divisions', 
            'shiftCodes',
            'data',
            'divisionId',
            'shiftId',
            'userName'
        ));
    }
    
    /**
     * Get users by division for AJAX
     */
    public function getUsersByDivision(Request $request)
    {
        $this->validateAccess();
        
        Log::info('getUsersByDivision called', [
            'user' => Auth::user() ? Auth::user()->id : 'not authenticated',
            'division_id' => $request->get('division_id')
        ]);
        
        $divisionId = $request->get('division_id');
        
        $users = DB::table('users')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->select('users.id', 'users.u_nip', 'users.u_name', 'user_divisions.ud_name')
            ->where('users.u_delete', '0')
            ->whereNotNull('users.u_nip');
            
        if ($divisionId) {
            $users->where('users.ud_id', $divisionId);
        }
        
        $users = $users->orderBy('users.u_name')->get();
        
        Log::info('getUsersByDivision result', [
            'users_count' => $users->count()
        ]);
        
        return response()->json([
            'success' => true,
            'users' => $users,
            'message' => 'Data berhasil dimuat'
        ]);
    }
    
    /**
     * Save weekly schedule
     */
    public function saveWeeklySchedule(Request $request)
    {
        $this->validateAccess();
        
        $request->validate([
            'start_date' => 'required|date',
            'schedules' => 'required|array',
            'schedules.*.user_id' => 'required|exists:users,id',
            'schedules.*.dates' => 'required|array',
            'schedules.*.dates.*.date' => 'required|date',
            'schedules.*.dates.*.shift_code_id' => 'nullable|exists:shift_codes,id'
        ]);
        
        $startDate = $request->input('start_date');
        $schedules = $request->input('schedules');
        
        DB::beginTransaction();
        
        try {
            foreach ($schedules as $schedule) {
                $userId = $schedule['user_id'];
                
                foreach ($schedule['dates'] as $dateData) {
                    $date = $dateData['date'];
                    $shiftCodeId = $dateData['shift_code_id'] ?? null;
                    
                                        // Check if schedule already exists for this user and date
                    $existingSchedule = DB::table('daily_schedules')
                        ->where('user_id', $userId)
                        ->where('ds_date', $date)
                        ->first();
                    
                    if ($existingSchedule) {
                        // Update existing schedule
                        if ($shiftCodeId) {
                            // Get shift code details
                            $shiftCode = DB::table('shift_codes')
                                ->where('id', $shiftCodeId)
                                ->first();
                            
                            DB::table('daily_schedules')
                                ->where('id', $existingSchedule->id)
                                ->update([
                                    'sc_id' => $shiftCodeId,
                                    'ds_start_time' => $shiftCode->sc_start_time,
                                    'ds_end_time' => $shiftCode->sc_end_time,
                                    'ds_status' => 'scheduled',
                                    'updated_by' => Auth::user()->id,
                                    'updated_at' => now()
                                ]);
                        } else {
                            // Delete schedule if no shift code selected
                            DB::table('daily_schedules')
                                ->where('id', $existingSchedule->id)
                                ->delete();
                        }
                    } else if ($shiftCodeId) {
                        // Create new schedule
                        $shiftCode = DB::table('shift_codes')
                            ->where('id', $shiftCodeId)
                            ->first();
                            
                        $user = DB::table('users')
                            ->where('id', $userId)
                            ->first();
                        
                        DB::table('daily_schedules')->insert([
                            'user_id' => $userId,
                            'ud_id' => $user->ud_id,
                            'sc_id' => $shiftCodeId,
                            'ds_date' => $date,
                            'ds_start_time' => $shiftCode->sc_start_time,
                            'ds_end_time' => $shiftCode->sc_end_time,
                            'ds_status' => 'scheduled',
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Weekly schedule saved successfully!'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving schedule: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Test method to check database data
     */
    public function testData()
    {
        // Check if there are any schedules at all
        $totalSchedules = DB::table('daily_schedules')->count();
        
        // Check schedules for this week
        $thisWeekStart = date('Y-m-d', strtotime('monday this week'));
        $thisWeekEnd = date('Y-m-d', strtotime('sunday this week'));
        
        $thisWeekSchedules = DB::table('daily_schedules')
            ->whereBetween('ds_date', [$thisWeekStart, $thisWeekEnd])
            ->count();
            
        // Check users table
        $totalUsers = DB::table('users')->where('u_delete', '0')->count();
        
        // Check current user info
        $currentUser = DB::table('users')
            ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->select('users.*', 'user_positions.up_code', 'user_positions.up_level', 'user_divisions.ud_name as current_division_name')
            ->where('users.id', Auth::user()->id)
            ->first();
            
        return response()->json([
            'totalSchedules' => $totalSchedules,
            'thisWeekSchedules' => $thisWeekSchedules,
            'thisWeekStart' => $thisWeekStart,
            'thisWeekEnd' => $thisWeekEnd,
            'totalUsers' => $totalUsers,
            'currentUser' => $currentUser
        ]);
    }

    /**
     * Get existing schedules for weekly view
     */
    public function getWeeklySchedules(Request $request)
    {
        $startDate = $request->get('start_date');
        $divisionId = $request->get('division_id');
        $search = $request->get('search');
        
        if (!$startDate) {
            return response()->json([]);
        }
        
        // Get current user's position and division for filtering
        $currentUser = DB::table('users')
            ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->select('users.*', 'user_positions.up_code', 'user_positions.up_level', 'user_divisions.ud_name as current_division_name')
            ->where('users.id', Auth::user()->id)
            ->first();
        
        // Calculate end date (7 days from start date)
        $endDate = date('Y-m-d', strtotime($startDate . ' +6 days'));
        
        $query = DB::table('daily_schedules')
            ->select([
                'daily_schedules.user_id',
                'daily_schedules.ds_date',
                'daily_schedules.sc_id',
                'users.u_nip',
                'users.u_name',
                'user_divisions.ud_name',
                'shift_codes.sc_code'
            ])
            ->leftJoin('users', 'users.id', '=', 'daily_schedules.user_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
            ->whereBetween('ds_date', [$startDate, $endDate]);
            
        if ($divisionId) {
            // Filter by division using users table
            $query->where('users.ud_id', $divisionId);
        } else {
            // If no division filter, apply position-based filtering for supervisor/staff
            if (!in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER'])) {
                $query->where('users.ud_id', $currentUser->ud_id);
                
                // Filter staff based on position hierarchy
                if ($currentUser->up_code === 'SUPERVISOR') {
                    // Supervisor can only see staff (level 1) and other supervisors (level 2)
                    $query->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
                          ->where('user_positions.up_level', '<=', 2);
                } elseif ($currentUser->up_code === 'STAFF') {
                    // Staff can only see other staff (level 1)
                    $query->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
                          ->where('user_positions.up_level', '=', 1);
                }
            }
        }
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('users.u_name', 'like', '%' . $search . '%')
                  ->orWhere('users.u_nip', 'like', '%' . $search . '%');
            });
        }
        
        $schedules = $query->get();
        
        // Debug information
        \Log::info('DailyScheduleController - getWeeklySchedules Debug', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'divisionId' => $divisionId,
            'search' => $search,
            'currentUserCode' => $currentUser->up_code ?? 'unknown',
            'currentUserDivision' => $currentUser->ud_id ?? 'unknown',
            'queryCount' => $schedules->count(),
            'rawQuery' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        // Group by user and date
        $groupedSchedules = [];
        foreach ($schedules as $schedule) {
            $userId = $schedule->user_id;
            $date = $schedule->ds_date;
            
            if (!isset($groupedSchedules[$userId])) {
                $groupedSchedules[$userId] = [
                    'user_id' => $userId,
                    'u_nip' => $schedule->u_nip,
                    'u_name' => $schedule->u_name,
                    'ud_name' => $schedule->ud_name,
                    'dates' => []
                ];
            }
            
            $groupedSchedules[$userId]['dates'][$date] = [
                'sc_id' => $schedule->sc_id,
                'sc_code' => $schedule->sc_code
            ];
        }
        
        return response()->json([
            'success' => true,
            'schedules' => array_values($groupedSchedules),
            'message' => 'Data jadwal berhasil dimuat'
        ]);
    }

    public function exportWeekly(Request $request)
    {
        try {
            $currentUser = DB::table('users')
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->select('users.*', 'user_positions.up_code', 'user_positions.up_level', 'user_divisions.ud_name as current_division_name')
                ->where('users.id', Auth::user()->id)
                ->first();

            $divisionId = $request->get('division_id');
            $search = $request->get('search');
            $dateFilter = $request->get('date_filter', 'this_week');

            $startDate = $this->getDateRangeFromFilter($dateFilter)['startDate'];
            $endDate = $this->getDateRangeFromFilter($dateFilter)['endDate'];

            // Build query
            $query = DB::table('daily_schedules')
                ->select([
                    'daily_schedules.user_id',
                    'daily_schedules.ds_date',
                    'daily_schedules.sc_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'shift_codes.sc_code',
                    'shift_codes.sc_shift_name',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time'
                ])
                ->leftJoin('users', 'users.id', '=', 'daily_schedules.user_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
                ->whereBetween('ds_date', [$startDate, $endDate]);

            // Apply filters
            if ($divisionId) {
                $query->where('users.ud_id', $divisionId);
            } else {
                if (!in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER'])) {
                    $query->where('users.ud_id', $currentUser->ud_id);
                    
                    if ($currentUser->up_code === 'SUPERVISOR') {
                        $query->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
                              ->where('user_positions.up_level', '<=', 2);
                    } elseif ($currentUser->up_code === 'STAFF') {
                        $query->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
                              ->where('user_positions.up_level', '=', 1);
                    }
                }
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('users.u_name', 'like', '%' . $search . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $search . '%');
                });
            }

            $schedules = $query->get();

            \Log::info('Export Weekly Schedule - Query Results', [
                'schedules_count' => $schedules->count(),
                'first_schedule' => $schedules->first(),
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            // Generate filename
            $filename = 'weekly-schedule-' . $dateFilter . '-' . date('Y-m-d') . '.xlsx';
            
            \Log::info('Export Weekly Schedule - Using Excel Facade', [
                'filename' => $filename,
                'schedules_count' => $schedules->count()
            ]);

            return Excel::download(
                new \App\Exports\WeeklyScheduleExport($schedules, $startDate, $endDate), 
                $filename
            );

        } catch (\Exception $e) {
            \Log::error('Export Weekly Schedule Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat export: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export weekly schedule to PDF
     */
    public function exportWeeklyPDF(Request $request)
    {
        try {
            $currentUser = DB::table('users')
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->select('users.*', 'user_positions.up_code', 'user_positions.up_level', 'user_divisions.ud_name as current_division_name')
                ->where('users.id', Auth::user()->id)
                ->first();

            $divisionId = $request->get('division_id');
            $search = $request->get('search');
            $dateFilter = $request->get('date_filter', 'this_week');

            $startDate = $this->getDateRangeFromFilter($dateFilter)['startDate'];
            $endDate = $this->getDateRangeFromFilter($dateFilter)['endDate'];

            // Build query to get users with their schedules
            $users = DB::table('users')
                ->select([
                    'users.id as user_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'user_types.ut_name'
                ])
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->where('users.u_delete', '!=', '1');

            // Apply filters
            if ($divisionId) {
                $users->where('users.ud_id', $divisionId);
            } else {
                if (!in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER'])) {
                    $users->where('users.ud_id', $currentUser->ud_id);
                    
                    if ($currentUser->up_code === 'SUPERVISOR') {
                        $users->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
                              ->where('user_positions.up_level', '<=', 2);
                    } elseif ($currentUser->up_code === 'STAFF') {
                        $users->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
                              ->where('user_positions.up_level', '=', 1);
                    }
                }
            }

            if ($search) {
                $users->where(function($q) use ($search) {
                    $q->where('users.u_name', 'like', '%' . $search . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $search . '%');
                });
            }

            $users = $users->get();

            // Get schedules for the date range
            $schedules = DB::table('daily_schedules')
                ->select([
                    'daily_schedules.user_id',
                    'daily_schedules.ds_date',
                    'daily_schedules.sc_id',
                    'shift_codes.sc_code',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time',
                    'shift_codes.sc_shift_name'
                ])
                ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
                ->whereBetween('ds_date', [$startDate, $endDate]);

            // Generate HTML content untuk dimasukkan ke PDF
            $htmlContent = $this->generateWeeklyScheduleHTML($users, $schedules, $startDate, $endDate);

            // Generate PDF
            $pdf = Pdf::loadHTML($htmlContent)
                      ->setPaper('a4', 'landscape'); // Use landscape for better table fit

            // Nama file
            $filename = 'weekly-schedule-' . $dateFilter . '-' . date('Y-m-d') . '.pdf';

            // Download PDF
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Export Weekly Schedule PDF Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat export PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export weekly report to Excel
     */
    public function exportWeeklyReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date', date('Y-m-d', strtotime('monday this week')));
            $endDate = $request->get('end_date', date('Y-m-d', strtotime('sunday this week')));
            $divisionId = $request->get('division_id');
            $dateFilter = $request->get('date_filter', 'this_week');

            // If date filter is provided, calculate dates
            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }

            // Build query for report export
            $query = DB::table('daily_schedules')
                ->select([
                    'daily_schedules.user_id',
                    'daily_schedules.ds_date',
                    'daily_schedules.sc_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'shift_codes.sc_code',
                    'shift_codes.sc_shift_name',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time'
                ])
                ->leftJoin('users', 'users.id', '=', 'daily_schedules.user_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
                ->whereBetween('ds_date', [$startDate, $endDate]);

            if ($divisionId) {
                $query->where('users.ud_id', $divisionId);
            }

            $schedules = $query->get();

            \Log::info('Export Weekly Report - Query Results', [
                'schedules_count' => $schedules->count(),
                'first_schedule' => $schedules->first(),
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            // Generate filename
            $filename = 'weekly-schedule-report-' . $startDate . '-' . $endDate . '.xlsx';
            
            \Log::info('Export Weekly Report - Using Excel Facade', [
                'filename' => $filename,
                'schedules_count' => $schedules->count()
            ]);

            // Use Excel facade to export
            return Excel::download(
                new \App\Exports\WeeklyReportExport($schedules, $startDate, $endDate), 
                $filename
            );

        } catch (\Exception $e) {
            \Log::error('Export Weekly Report Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat export report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export weekly report to PDF
     */
    public function exportWeeklyReportPDF(Request $request)
    {
        try {
            $startDate = $request->get('start_date', date('Y-m-d', strtotime('monday this week')));
            $endDate = $request->get('end_date', date('Y-m-d', strtotime('sunday this week')));
            $divisionId = $request->get('division_id');
            $dateFilter = $request->get('date_filter', 'this_week');

            // If date filter is provided, calculate dates
            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }

            // Build query to get users with their schedules
            $users = DB::table('users')
                ->select([
                    'users.id as user_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'user_types.ut_name'
                ])
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->where('users.u_delete', '!=', '1');

            // Apply filters
            if ($divisionId) {
                $users->where('users.ud_id', $divisionId);
            }

            $users = $users->get();

            // Get schedules for the date range
            $schedules = DB::table('daily_schedules')
                ->select([
                    'daily_schedules.user_id',
                    'daily_schedules.ds_date',
                    'daily_schedules.sc_id',
                    'shift_codes.sc_code',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time',
                    'shift_codes.sc_shift_name'
                ])
                ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
                ->whereBetween('ds_date', [$startDate, $endDate]);

            // Generate HTML content for report
            $htmlContent = $this->generateWeeklyReportHTML($users, $schedules, $startDate, $endDate);
            
            // Generate PDF
            $pdf = Pdf::loadHTML($htmlContent)
                      ->setPaper('a4', 'landscape'); // Use landscape for better table fit
            
            // Generate filename
            $filename = 'weekly-schedule-report-' . $startDate . '-' . $endDate . '.pdf';
            
            // Download PDF
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Export Weekly Report PDF Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat export report PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export weekly schedule to Excel (Public - No Auth Required)
     */
    public function exportWeeklyPublic(Request $request)
    {
        try {
            \Log::info('Public Export Weekly Schedule Started', [
                'request_data' => $request->all(),
                'filters_applied' => [
                    'division_id' => $request->get('division_id'),
                    'search' => $request->get('search'),
                    'date_filter' => $request->get('date_filter', 'this_week')
                ]
            ]);

            $divisionId = $request->get('division_id');
            $search = $request->get('search');
            $dateFilter = $request->get('date_filter', 'this_week');

            $startDate = $this->getDateRangeFromFilter($dateFilter)['startDate'];
            $endDate = $this->getDateRangeFromFilter($dateFilter)['endDate'];

            \Log::info('Date range calculated', [
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            // Build query to get users with their schedules
            $users = DB::table('users')
                ->select([
                    'users.id as user_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'user_types.ut_name'
                ])
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->where('users.u_delete', '!=', '1');

            // Apply filters
            if ($divisionId) {
                $users->where('users.ud_id', $divisionId);
                \Log::info('Division filter applied', ['division_id' => $divisionId]);
            }

            if ($search) {
                $users->where(function($q) use ($search) {
                    $q->where('users.u_name', 'like', '%' . $search . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $search . '%');
                });
                \Log::info('Search filter applied', ['search' => $search]);
            }

            $users = $users->get();

            \Log::info('Users query result', [
                'users_count' => $users->count(),
                'query_success' => true
            ]);

            // Get schedules for the date range
            $schedules = DB::table('daily_schedules')
                ->select([
                    'daily_schedules.user_id',
                    'daily_schedules.ds_date',
                    'daily_schedules.sc_id',
                    'shift_codes.sc_code',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time',
                    'shift_codes.sc_shift_name'
                ])
                ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
                ->whereBetween('ds_date', [$startDate, $endDate]);

            \Log::info('Schedules query result', [
                'schedules_count' => $schedules->count(),
                'date_range' => [$startDate, $endDate]
            ]);

            // Combine users with their schedules
            $exportData = [];
            
            // Group schedules by division and user (same structure as weeklyReport)
            $groupedSchedules = [];
            
            // First, create a map of user data for easy access
            $userMap = [];
            foreach ($users as $user) {
                $userMap[$user->user_id] = $user;
            }
            
            foreach ($schedules as $schedule) {
                $userId = $schedule->user_id;
                $user = $userMap[$userId] ?? null;
                
                if ($user) {
                    $divisionName = $user->ud_name ?: 'No Division';
                    $date = $schedule->ds_date;
                    
                    if (!isset($groupedSchedules[$divisionName])) {
                        $groupedSchedules[$divisionName] = [];
                    }
                    
                    if (!isset($groupedSchedules[$divisionName][$userId])) {
                        $groupedSchedules[$divisionName][$userId] = [
                            'user_id' => $userId,
                    'u_nip' => $user->u_nip,
                    'u_name' => $user->u_name,
                    'ud_name' => $user->ud_name,
                            'schedules' => []
                        ];
                    }
                    
                    $groupedSchedules[$divisionName][$userId]['schedules'][$date] = [
                        'sc_code' => $schedule->sc_code,
                        'sc_shift_name' => $schedule->sc_shift_name,
                        'sc_start_time' => $schedule->sc_start_time,
                        'sc_end_time' => $schedule->sc_end_time
                    ];
                }
            }
            
            // Add users without schedules
            foreach ($users as $user) {
                $divisionName = $user->ud_name ?: 'No Division';
                $userId = $user->user_id;
                
                if (!isset($groupedSchedules[$divisionName])) {
                    $groupedSchedules[$divisionName] = [];
                }
                
                if (!isset($groupedSchedules[$divisionName][$userId])) {
                    $groupedSchedules[$divisionName][$userId] = [
                        'user_id' => $userId,
                        'u_nip' => $user->u_nip,
                        'u_name' => $user->u_name,
                        'ud_name' => $user->ud_name,
                        'schedules' => []
                    ];
                }
            }

            \Log::info('Public Export Weekly Schedule - Data Prepared', [
                'users_count' => $users->count(),
                'schedules_count' => $schedules->count(),
                'export_data_count' => count($exportData),
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'filters_summary' => [
                    'division_filtered' => $divisionId ? 'Yes' : 'No',
                    'search_filtered' => $search ? 'Yes' : 'No',
                    'date_filtered' => $dateFilter
                ]
            ]);

            // Generate filename
            $filename = 'weekly-schedule-' . $dateFilter . '-' . date('Y-m-d') . '.xlsx';
            
            \Log::info('Public Export Weekly Schedule - Using Excel Facade', [
                'filename' => $filename,
                'export_data_count' => count($exportData)
            ]);

            // Use Excel facade to export
            return Excel::download(
                new \App\Exports\WeeklyScheduleExport($exportData, $startDate, $endDate), 
                $filename
            );

        } catch (\Exception $e) {
            \Log::error('Public Export Weekly Schedule Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat export: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export weekly report to Excel (Public - No Auth Required)
     */
    public function exportWeeklyReportPublic(Request $request)
    {
        try {
            \Log::info('Public Export Weekly Report Started', [
                'request_data' => $request->all(),
                'filters_applied' => [
                    'division_id' => $request->get('division_id'),
                    'shift_id' => $request->get('shift_id'),
                    'user_name' => $request->get('user_name'),
                    'date_filter' => $request->get('date_filter', 'this_week'),
                    'start_date' => $request->get('start_date'),
                    'end_date' => $request->get('end_date')
                ]
            ]);

            $startDate = $request->get('start_date', date('Y-m-d', strtotime('monday this week')));
            $endDate = $request->get('end_date', date('Y-m-d', strtotime('sunday this week')));
            $divisionId = $request->get('division_id');
            $shiftId = $request->get('shift_id');
            $userName = $request->get('user_name');
            $dateFilter = $request->get('date_filter', 'this_week');

            // If date filter is provided, calculate dates
            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }

            \Log::info('Date range calculated for report', [
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            // Build query to get users with their schedules
            $users = DB::table('users')
                ->select([
                    'users.id as user_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'user_types.ut_name'
                ])
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->where('users.u_delete', '!=', '1');

            // Apply filters
            if ($divisionId) {
                $users->where('users.ud_id', $divisionId);
                \Log::info('Division filter applied', ['division_id' => $divisionId]);
            }

            if ($userName) {
                $users->where('users.u_name', 'like', '%' . $userName . '%');
                \Log::info('User name filter applied', ['user_name' => $userName]);
            }

            $users = $users->get();

            \Log::info('Users query result', [
                'users_count' => $users->count(),
                'query_success' => true
            ]);

            // Get schedules for the date range
            $schedules = DB::table('daily_schedules')
                ->select([
                    'daily_schedules.user_id',
                    'daily_schedules.ds_date',
                    'daily_schedules.sc_id',
                    'shift_codes.sc_code',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time',
                    'shift_codes.sc_shift_name'
                ])
                ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
                ->whereBetween('ds_date', [$startDate, $endDate]);

            // Apply shift filter if provided
            if ($shiftId) {
                $schedules->where('daily_schedules.sc_id', $shiftId);
                \Log::info('Shift filter applied', ['shift_id' => $shiftId]);
            }

            $schedules = $schedules->get();

            \Log::info('Schedules query result', [
                'schedules_count' => $schedules->count(),
                'date_range' => [$startDate, $endDate]
            ]);

            // Combine users with their schedules
            $exportData = [];
            
            // Group schedules by division and user (same structure as weeklyReport)
            $groupedSchedules = [];
            
            // First, create a map of user data for easy access
            $userMap = [];
            foreach ($users as $user) {
                $userMap[$user->user_id] = $user;
            }
            
            foreach ($schedules as $schedule) {
                $userId = $schedule->user_id;
                $user = $userMap[$userId] ?? null;
                
                if ($user) {
                    $divisionName = $user->ud_name ?: 'No Division';
                    $date = $schedule->ds_date;
                    
                    if (!isset($groupedSchedules[$divisionName])) {
                        $groupedSchedules[$divisionName] = [];
                    }
                    
                    if (!isset($groupedSchedules[$divisionName][$userId])) {
                        $groupedSchedules[$divisionName][$userId] = [
                            'user_id' => $userId,
                    'u_nip' => $user->u_nip,
                    'u_name' => $user->u_name,
                    'ud_name' => $user->ud_name,
                            'schedules' => []
                        ];
                    }
                    
                    $groupedSchedules[$divisionName][$userId]['schedules'][$date] = [
                        'sc_code' => $schedule->sc_code,
                        'sc_shift_name' => $schedule->sc_shift_name,
                        'sc_start_time' => $schedule->sc_start_time,
                        'sc_end_time' => $schedule->sc_end_time
                    ];
                }
            }
            
            // Add users without schedules
            foreach ($users as $user) {
                $divisionName = $user->ud_name ?: 'No Division';
                $userId = $user->user_id;
                
                if (!isset($groupedSchedules[$divisionName])) {
                    $groupedSchedules[$divisionName] = [];
                }
                
                if (!isset($groupedSchedules[$divisionName][$userId])) {
                    $groupedSchedules[$divisionName][$userId] = [
                        'user_id' => $userId,
                        'u_nip' => $user->u_nip,
                        'u_name' => $user->u_name,
                        'ud_name' => $user->ud_name,
                        'schedules' => []
                    ];
                }
            }

            \Log::info('Public Export Weekly Report - Data Prepared', [
                'users_count' => $users->count(),
                'schedules_count' => $schedules->count(),
                'export_data_count' => count($exportData),
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'filters_summary' => [
                    'division_filtered' => $divisionId ? 'Yes' : 'No',
                    'shift_filtered' => $shiftId ? 'Yes' : 'No',
                    'user_name_filtered' => $userName ? 'Yes' : 'No',
                    'date_filtered' => $dateFilter
                ]
            ]);

            // Generate filename
            $filename = 'weekly-schedule-report-' . $startDate . '-' . $endDate . '.xlsx';
            
            \Log::info('Public Export Weekly Report - Using Excel Facade', [
                'filename' => $filename,
                'export_data_count' => count($exportData)
            ]);

            // Use Excel facade to export
            return Excel::download(
                new \App\Exports\WeeklyReportExport($exportData, $startDate, $endDate), 
                $filename
            );

        } catch (\Exception $e) {
            \Log::error('Public Export Weekly Report Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat export report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export weekly schedule to PDF (Public - No Auth Required)
     */
    public function exportWeeklyPDFPublic(Request $request)
    {
        try {
            \Log::info('Public Export Weekly Schedule PDF Started', [
                'request_data' => $request->all(),
                'filters_applied' => [
                    'division_id' => $request->get('division_id'),
                    'search' => $request->get('search'),
                    'date_filter' => $request->get('date_filter', 'this_week')
                ]
            ]);

            $divisionId = $request->get('division_id');
            $search = $request->get('search');
            $dateFilter = $request->get('date_filter', 'this_week');

            $startDate = $this->getDateRangeFromFilter($dateFilter)['startDate'];
            $endDate = $this->getDateRangeFromFilter($dateFilter)['endDate'];

            \Log::info('Date range calculated for PDF', [
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            // Build query to get users with their schedules
            $users = DB::table('users')
                ->select([
                    'users.id as user_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'user_types.ut_name'
                ])
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->where('users.u_delete', '!=', '1');

            // Apply filters
            if ($divisionId) {
                $users->where('users.ud_id', $divisionId);
                \Log::info('Division filter applied for PDF', ['division_id' => $divisionId]);
            }

            if ($search) {
                $users->where(function($q) use ($search) {
                    $q->where('users.u_name', 'like', '%' . $search . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $search . '%');
                });
                \Log::info('Search filter applied for PDF', ['search' => $search]);
            }

            $users = $users->get();

            \Log::info('Users query result for PDF', [
                'users_count' => $users->count(),
                'query_success' => true
            ]);

            // Get schedules for the date range
            $schedules = DB::table('daily_schedules')
                ->select([
                    'daily_schedules.user_id',
                    'daily_schedules.ds_date',
                    'daily_schedules.sc_id',
                    'shift_codes.sc_code',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time',
                    'shift_codes.sc_shift_name'
                ])
                ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
                ->whereBetween('ds_date', [$startDate, $endDate]);

            \Log::info('Schedules query result for PDF', [
                'schedules_count' => $schedules->count(),
                'date_range' => [$startDate, $endDate]
            ]);

            \Log::info('Public Export Weekly Schedule PDF - Data Prepared', [
                'users_count' => $users->count(),
                'schedules_count' => $schedules->count(),
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'filters_summary' => [
                    'division_filtered' => $divisionId ? 'Yes' : 'No',
                    'search_filtered' => $search ? 'Yes' : 'No',
                    'date_filtered' => $dateFilter
                ]
            ]);

            // Generate HTML content untuk dimasukkan ke PDF
            $htmlContent = $this->generateWeeklyScheduleHTML($users, $schedules, $startDate, $endDate);

            // Generate PDF
            $pdf = Pdf::loadHTML($htmlContent)
                      ->setPaper('a4', 'landscape'); // Use landscape for better table fit

            // Nama file
            $filename = 'weekly-schedule-' . $dateFilter . '-' . date('Y-m-d') . '.pdf';

            \Log::info('Public Export Weekly Schedule PDF - Using PDF Facade', [
                'filename' => $filename
            ]);

            // Download PDF
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Public Export Weekly Schedule PDF Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat export PDF: ' . $e->getMessage()
            ], 500);
        }
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
     * Helper method to generate HTML content for weekly schedule PDF export
     */
    private function generateWeeklyScheduleHTML($users, $schedules, $startDate, $endDate): string
    {
        $html = "<html><head><title>Weekly Schedule</title>";
        $html .= "<style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #333; text-align: center; }
            .date-range { text-align: center; margin-bottom: 20px; color: #666; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
            th { background-color: #f5f5f5; font-weight: bold; }
            .user-info { background-color: #f9f9f9; }
            .shift-cell { min-width: 80px; }
        </style></head><body>";
        
        $html .= "<h1>Weekly Schedule Report</h1>";
        $html .= "<div class='date-range'>Date Range: " . date('d M Y', strtotime($startDate)) . " to " . date('d M Y', strtotime($endDate)) . "</div>";
        
        $html .= "<table>";
        
        // Header row
        $html .= "<tr>";
        $html .= "<th>NIP</th>";
        $html .= "<th>Nama Staff</th>";
        $html .= "<th>Divisi</th>";
        $html .= "<th>User Type</th>";
        
        // Daily headers
        $currentDate = \Carbon\Carbon::parse($startDate);
        while ($currentDate->lte(\Carbon\Carbon::parse($endDate))) {
            $html .= "<th class='shift-cell'>" . $currentDate->format('D d-M') . "</th>";
            $currentDate->addDay();
        }
        $html .= "</tr>";
        
        // Data rows
        foreach ($users as $user) {
            $html .= "<tr>";
            $html .= "<td class='user-info'>" . ($user->u_nip ?? '-') . "</td>";
            $html .= "<td class='user-info'>" . ($user->u_name ?? '-') . "</td>";
            $html .= "<td class='user-info'>" . ($user->ud_name ?? '-') . "</td>";
            $html .= "<td class='user-info'>" . ($user->ut_name ?? 'FULL TIME') . "</td>";
            
            // Daily shift data
            $currentDate = \Carbon\Carbon::parse($startDate);
            while ($currentDate->lte(\Carbon\Carbon::parse($endDate))) {
                $dateStr = $currentDate->format('Y-m-d');
                
                // Find schedule for this date
                $dailySchedule = $schedules->where('user_id', $user->user_id)
                                       ->where('ds_date', $dateStr)
                                       ->first();
                
                if ($dailySchedule && $dailySchedule->sc_code) {
                    $html .= "<td class='shift-cell'>" . $dailySchedule->sc_code . "</td>";
                } else {
                    $html .= "<td class='shift-cell'>-</td>";
                }
                
                $currentDate->addDay();
            }
            $html .= "</tr>";
        }
        
        $html .= "</table>";
        $html .= "</body></html>";
        return $html;
    }

    /**
     * Helper method to generate HTML content for weekly report PDF export
     */
    private function generateWeeklyReportHTML($users, $schedules, $startDate, $endDate)
    {
        $html = "<html><head><title>Weekly Schedule Report</title>";
        $html .= "<style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #333; text-align: center; }
            .date-range { text-align: center; margin-bottom: 20px; color: #666; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
            th { background-color: #f5f5f5; font-weight: bold; }
            .user-info { background-color: #f9f9f9; }
            .shift-cell { min-width: 80px; }
            .shift-name { background-color: #e3f2fd; }
            .start-shift { background-color: #fff3e0; }
        </style></head><body>";
        
        $html .= "<h1>Weekly Schedule Report</h1>";
        $html .= "<div class='date-range'>Date Range: " . date('d M Y', strtotime($startDate)) . " to " . date('d M Y', strtotime($endDate)) . "</div>";
        
        $html .= "<table>";
        
        // First header row - Date headers with colspan=2
        $html .= "<tr>";
        $html .= "<th rowspan='2' style='width: 250px;'>NAMA</th>";
        
        // Daily headers with colspan=2
        $currentDate = \Carbon\Carbon::parse($startDate);
        while ($currentDate->lte(\Carbon\Carbon::parse($endDate))) {
            $html .= "<th colspan='2' class='shift-cell' style='min-width: 240px;'>";
            $html .= "<div style='font-weight: bold;'>" . $currentDate->format('l') . "</div>";
            $html .= "<div style='font-size: 12px;'>" . $currentDate->format('d M') . "</div>";
            $html .= "</th>";
            $currentDate->addDay();
        }
        $html .= "</tr>";
        
        // Second header row - Shift Name and Start Shift
        $html .= "<tr>";
        $currentDate = \Carbon\Carbon::parse($startDate);
        while ($currentDate->lte(\Carbon\Carbon::parse($endDate))) {
            $html .= "<th class='shift-name' style='width: 120px;'><small>Shift Name</small></th>";
            $html .= "<th class='start-shift' style='width: 120px;'><small>Start Shift</small></th>";
            $currentDate->addDay();
        }
        $html .= "</tr>";
        
        // Data rows
        foreach ($users as $user) {
            $html .= "<tr>";
            
            // NAMA column with NIP below (same format as weekly report table)
            $html .= "<td class='user-info' style='font-size: 14px; width: 250px;'>";
            $html .= "<div style='font-weight: bold;'>" . ($user->u_name ?? '-') . "</div>";
            $html .= "<small style='color: #666;'>" . ($user->u_nip ?? '-') . "</small>";
            $html .= "</td>";
            
            // Daily shift data - 2 columns per day
            $currentDate = \Carbon\Carbon::parse($startDate);
            while ($currentDate->lte(\Carbon\Carbon::parse($endDate))) {
                $dateStr = $currentDate->format('Y-m-d');
                
                // Find schedule for this date
                $dailySchedule = $schedules->where('user_id', $user->user_id)
                                       ->where('ds_date', $dateStr)
                                       ->first();
                
                if ($dailySchedule) {
                    // Shift Name Column
                    $shiftName = $dailySchedule->sc_shift_name ?? '';
                    $shiftCode = $dailySchedule->sc_code ?? '';
                    
                    if ($shiftName) {
                        $html .= "<td class='shift-cell shift-name'>" . $shiftName . " (" . $shiftCode . ")" . "</td>";
                } else {
                        $html .= "<td class='shift-cell shift-name'>" . ($shiftCode ?: '-') . "</td>";
                    }
                    
                    // Start Shift Column
                    $startTime = $dailySchedule->sc_start_time ?? '';
                    $endTime = $dailySchedule->sc_end_time ?? '';
                    
                    if ($startTime && $endTime) {
                        $html .= "<td class='shift-cell start-shift'>" . date('H:i', strtotime($startTime)) . " - " . date('H:i', strtotime($endTime)) . "</td>";
                    } elseif ($startTime) {
                        $html .= "<td class='shift-cell start-shift'>" . date('H:i', strtotime($startTime)) . "</td>";
                    } else {
                        $html .= "<td class='shift-cell start-shift'>-</td>";
                    }
                } else {
                    $html .= "<td class='shift-cell shift-name'>-</td>";
                    $html .= "<td class='shift-cell start-shift'>-</td>";
                }
                
                $currentDate->addDay();
            }
            $html .= "</tr>";
        }
        
        $html .= "</table>";
        $html .= "</body></html>";
        return $html;
    }

    /**
     * Test export method for debugging
     */
    public function testExport()
    {
        try {
            // Create simple test data
            $testData = collect([
                (object) [
                    'u_nip' => 'TEST001',
                    'u_name' => 'Test User',
                    'ud_name' => 'Test Division',
                    'ds_date' => '2025-08-12',
                    'sc_code' => 'SHIFT1',
                ]
            ]);
            
            $filename = 'test-export-' . date('Y-m-d') . '.xlsx';
            
            \Log::info('Test export started', ['filename' => $filename]);
            
            // Return Excel file download
            return Excel::download(new WeeklyScheduleExport($testData, '2025-08-12', '2025-08-12'), $filename);
            
        } catch (\Exception $e) {
            \Log::error('Test Export Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Export error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Simple test export method without Excel for debugging
     */
    public function testExportSimple()
    {
        try {
            \Log::info('Simple test export started');
            
            // Return simple CSV response
            $csvContent = "NIP,Nama,Divisi,Tanggal,Shift\n";
            $csvContent .= "TEST001,Test User,Test Division,2025-08-12,SHIFT1\n";
            
            $filename = 'test-export-simple-' . date('Y-m-d') . '.csv';
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            \Log::error('Simple Test Export Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Simple export error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Excel export functionality
     */
    public function testExcelExport()
    {
        try {
            \Log::info('Testing Excel Export...');
            
            // Create test data
            $testData = collect([
                (object) [
                    'u_nip' => '123456',
                    'u_name' => 'Test User',
                    'ud_name' => 'IT Division',
                    'ds_date' => '2025-01-20',
                    'sc_code' => 'SHIFT1',
                    'sc_shift_name' => 'Morning Shift',
                    'sc_start_time' => '08:00:00',
                    'sc_end_time' => '17:00:00'
                ]
            ]);
            
            \Log::info('Test data created', ['count' => $testData->count()]);
            
            // Test Excel export
            $filename = 'test-excel-export-' . date('Y-m-d-H-i-s') . '.xlsx';
            
            \Log::info('Attempting Excel export', ['filename' => $filename]);
            
            return Excel::download(
                new \App\Exports\WeeklyScheduleExport($testData, '2025-01-20', '2025-01-20'), 
                $filename
            );
            
        } catch (\Exception $e) {
            \Log::error('Test Excel Export Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Excel Export Test Failed: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Export weekly report to PDF (Public - No Auth Required)
     */
    public function exportWeeklyReportPDFPublic(Request $request)
    {
        try {
            \Log::info('Public Export Weekly Report PDF Started', [
                'request_data' => $request->all(),
                'filters_applied' => [
                    'division_id' => $request->get('division_id'),
                    'shift_id' => $request->get('shift_id'),
                    'user_name' => $request->get('user_name'),
                    'date_filter' => $request->get('date_filter', 'this_week'),
                    'start_date' => $request->get('start_date'),
                    'end_date' => $request->get('end_date')
                ]
            ]);

            $startDate = $request->get('start_date', date('Y-m-d', strtotime('monday this week')));
            $endDate = $request->get('end_date', date('Y-m-d', strtotime('sunday this week')));
            $divisionId = $request->get('division_id');
            $shiftId = $request->get('shift_id');
            $userName = $request->get('user_name');
            $dateFilter = $request->get('date_filter', 'this_week');

            // If date filter is provided, calculate dates
            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }

            \Log::info('Date range calculated for PDF report', [
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            // Build query to get users with their schedules
            $users = DB::table('users')
                ->select([
                    'users.id as user_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'user_types.ut_name'
                ])
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->where('users.u_delete', '!=', '1');

            // Apply filters
            if ($divisionId) {
                $users->where('users.ud_id', $divisionId);
                \Log::info('Division filter applied for PDF report', ['division_id' => $divisionId]);
            }

            if ($userName) {
                $users->where('users.u_name', 'like', '%' . $userName . '%');
                \Log::info('User name filter applied for PDF report', ['user_name' => $userName]);
            }

            $users = $users->get();

            \Log::info('Users query result for PDF report', [
                'users_count' => $users->count(),
                'query_success' => true
            ]);

            // Get schedules for the date range
            $schedules = DB::table('daily_schedules')
                ->select([
                    'daily_schedules.user_id',
                    'daily_schedules.ds_date',
                    'daily_schedules.sc_id',
                    'shift_codes.sc_code',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time',
                    'shift_codes.sc_shift_name'
                ])
                ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
                ->whereBetween('ds_date', [$startDate, $endDate]);

            // Apply shift filter if provided
            if ($shiftId) {
                $schedules->where('daily_schedules.sc_id', $shiftId);
                \Log::info('Shift filter applied for PDF report', ['shift_id' => $shiftId]);
            }

            $schedules = $schedules->get();

            \Log::info('Schedules query result for PDF report', [
                'schedules_count' => $schedules->count(),
                'date_range' => [$startDate, $endDate]
            ]);

            \Log::info('Public Export Weekly Report PDF - Data Prepared', [
                'users_count' => $users->count(),
                'schedules_count' => $schedules->count(),
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'filters_summary' => [
                    'division_filtered' => $divisionId ? 'Yes' : 'No',
                    'shift_filtered' => $shiftId ? 'Yes' : 'No',
                    'user_name_filtered' => $userName ? 'Yes' : 'No',
                    'date_filtered' => $dateFilter
                ]
            ]);

            // Generate HTML content for report
            $htmlContent = $this->generateWeeklyReportHTML($users, $schedules, $startDate, $endDate);
            
            // Generate PDF
            $pdf = Pdf::loadHTML($htmlContent)
                      ->setPaper('a4', 'landscape'); // Use landscape for better table fit
            
            // Generate filename
            $filename = 'weekly-schedule-report-' . $startDate . '-' . $endDate . '.pdf';
            
            \Log::info('Public Export Weekly Report PDF - Using PDF Facade', [
                'filename' => $filename
            ]);

            // Download PDF
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Public Export Weekly Report PDF Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat export report PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
