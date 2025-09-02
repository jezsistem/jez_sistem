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

    /**
     * Get user positions based on current user's level and position
     */
    protected function getUserPositionsForFilter()
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return collect();
        }

        // Get current user's position info
        $userPosition = DB::table('user_positions')
            ->where('id', $currentUser->up_id)
            ->first();

        if (!$userPosition) {
            return collect();
        }

        $query = DB::table('user_positions')
            ->where('up_is_active', 1)
            ->orderBy('up_name');

        // Condition 1: If user has up_level = 1 (bukan KOORDINATOR), show ALL level 1 positions
        if ($userPosition->up_level == 1 && $userPosition->up_code != 'KOORDINATOR') {
            $query->where('up_level', 1);
        }
        // Condition 2: If user has up_level = 2, show level 2, 3, 4 and level 1 with up_code = KOORDINATOR
        elseif ($userPosition->up_level == 2) {
            $query->where(function($q) {
                $q->whereIn('up_level', [2, 3, 4])
                  ->orWhere(function($subQ) {
                      $subQ->where('up_level', 1)
                           ->where('up_code', 'KOORDINATOR');
                  });
            });
        }
        // Condition 3: If user has up_level = 1 with up_code = KOORDINATOR, show level 2, 3, 4 and level 1 with up_code = KOORDINATOR
        elseif ($userPosition->up_level == 1 && $userPosition->up_code == 'KOORDINATOR') {
            $query->where(function($q) {
                $q->whereIn('up_level', [2, 3, 4])
                  ->orWhere(function($subQ) {
                      $subQ->where('up_level', 1)
                           ->where('up_code', 'KOORDINATOR');
                  });
            });
        }
        // Condition 4: If user has up_level = 3, show level 2, 3, 4 and level 1 with up_code = KOORDINATOR
        elseif ($userPosition->up_level == 3) {
            $query->where(function($q) {
                $q->whereIn('up_level', [2, 3, 4])
                  ->orWhere(function($subQ) {
                      $subQ->where('up_level', 1)
                           ->where('up_code', 'KOORDINATOR');
                  });
            });
        }
        // Condition 5: If user has up_level = 4, show level 2, 3, 4 and level 1 with up_code = KOORDINATOR
        elseif ($userPosition->up_level == 4) {
            $query->where(function($q) {
                $q->whereIn('up_level', [2, 3, 4])
                  ->orWhere(function($subQ) {
                      $subQ->where('up_level', 1)
                           ->where('up_code', 'KOORDINATOR');
                  });
            });
        }
        // Default: show all positions
        else {
            // No additional filtering
        }

        return $query->get();
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
        $schedule = DailySchedule::with(['user', 'userDivision', 'shiftCode.userTypes'])->findOrFail($id);

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
        
        // If no division filter specified, default to current user's division for non-DIRECTOR/MANAGER
        if (!$divisionId && !in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER'])) {
            $divisionId = $currentUser->ud_id;
        }
        
        $search = $request->get('search'); // Search by name or NIP
        $dateFilter = $request->get('date_filter', 'this_week');
        $startDate = $request->get('start_date');
        
        // Calculate date range for weekly input with two-way synchronization (same as weekly report)
        $originalStartDate = $request->get('start_date');
        
        if ($originalStartDate && $originalStartDate !== '') {
            // User manually selected a week range - PRIORITY HIGH
            // Calculate Monday of the week containing the selected date
            $selectedDate = Carbon::parse($originalStartDate);
            $dayOfWeek = $selectedDate->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            $daysToSubtract = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1; // Convert to Monday-based (0 = Monday)
            
            $startDate = $selectedDate->copy()->subDays($daysToSubtract)->format('Y-m-d');
            $endDate = $selectedDate->copy()->addDays(6 - $daysToSubtract)->format('Y-m-d');
            
            // Auto-detect if this matches any predefined filter
            $detectedFilter = $this->detectFilterFromDateRange($startDate, $endDate);
            if ($detectedFilter !== 'custom') {
                $dateFilter = $detectedFilter;
            } else {
                $dateFilter = 'custom';
            }
            
            // Log for debugging
            \Log::info('Weekly Input - Week Range Selected', [
                'original_start_date' => $originalStartDate,
                'selected_date_day_of_week' => $dayOfWeek,
                'days_to_subtract' => $daysToSubtract,
                'calculated_start_date' => $startDate,
                'calculated_end_date' => $endDate,
                'detected_filter' => $detectedFilter,
                'final_date_filter' => $dateFilter
            ]);
            
        } elseif ($dateFilter && $dateFilter !== 'custom') {
            // User selected a predefined date filter - PRIORITY MEDIUM
            $dateRange = $this->getDateRangeFromFilter($dateFilter);
            $startDate = $dateRange['startDate'];
            $endDate = $dateRange['endDate'];
            
            // Log for debugging
            \Log::info('Weekly Input - Date Filter Selected', [
                'date_filter' => $dateFilter,
                'calculated_start_date' => $startDate,
                'calculated_end_date' => $endDate
            ]);
            
        } else {
            // Default: this week - PRIORITY LOW
            $dateRange = $this->getDateRangeFromFilter('this_week');
            $startDate = $dateRange['startDate'];
            $endDate = $dateRange['endDate'];
            $dateFilter = 'this_week';
            
            // Log for debugging
            \Log::info('Weekly Input - Default Filter', [
                'default_filter' => 'this_week',
                'calculated_start_date' => $startDate,
                'calculated_end_date' => $endDate
            ]);
        }
        
        // Get divisions based on user role
        $divisions = collect();
        if (in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER'])) {
            // DIRECTOR/MANAGER can see all divisions
            $divisions = DB::table('user_divisions')
                ->where('ud_status', 'active')
                ->orderBy('ud_name')
                ->get();
        } elseif ($currentUser->up_code === 'SUPERVISOR') {
            // SUPERVISOR can only see their own division
            $divisions = DB::table('user_divisions')
                ->where('ud_status', 'active')
                ->where('id', $currentUser->ud_id)
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
        $shiftCodesByType = [];
        
        // Get all active user types from database
        $userTypes = DB::table('user_types')
            ->where('ut_status', 'active')
            ->orderBy('ut_name')
            ->get();
        
        // Add 'ALL' option first - show all active shift codes
        $shiftCodesByType['ALL'] = DB::table('shift_codes')
            ->where('sc_status', 'active')
            ->orderBy('sc_code')
            ->get();
        
        // Add dynamic user types using new pivot table logic
        foreach ($userTypes as $userType) {
            // Use the new ShiftCode model method for compatibility
            $shiftCodesByType[$userType->ut_name] = \App\Models\ShiftCode::getCompatibleShiftCodes($userType->ut_name);
        }
        
        // Get existing schedules for this week to ensure compatibility
        $existingSchedules = DB::table('daily_schedules')
            ->leftJoin('users', 'users.id', '=', 'daily_schedules.user_id')
            ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
            ->select('daily_schedules.user_id', 'daily_schedules.sc_id', 'shift_codes.sc_code', 'shift_codes.sc_type')
            ->whereBetween('daily_schedules.ds_date', [$startDate, $endDate])
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
            'search',
            'startDate',
            'endDate',
            'dateFilter',
            'divisionId'
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
        $positionId = $request->get('position_id');
        $shiftId = $request->get('shift_id');
        $userName = $request->get('user_name');
        $dateFilter = $request->get('date_filter', 'this_week');
        
        // Process date filter and start_date with two-way synchronization
        $originalStartDate = $request->get('start_date');
        
        if ($originalStartDate && $originalStartDate !== '' && $dateFilter !== 'now') {
            // User manually selected a week range - PRIORITY HIGH
            // Skip auto-detection for 'now' filter to preserve the selection
            $selectedDate = Carbon::parse($originalStartDate);
            $dayOfWeek = $selectedDate->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            $daysToSubtract = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1; // Convert to Monday-based (0 = Monday)
            
            $startDate = $selectedDate->copy()->subDays($daysToSubtract)->format('Y-m-d');
            $endDate = $selectedDate->copy()->addDays(6 - $daysToSubtract)->format('Y-m-d');
            
            // Auto-detect if this matches any predefined filter
            $detectedFilter = $this->detectFilterFromDateRange($startDate, $endDate);
            if ($detectedFilter !== 'custom') {
                $dateFilter = $detectedFilter;
            } else {
                $dateFilter = 'custom';
            }
            
            // Log for debugging
            \Log::info('Weekly Report - Week Range Selected', [
                'original_start_date' => $originalStartDate,
                'selected_date_day_of_week' => $dayOfWeek,
                'days_to_subtract' => $daysToSubtract,
                'calculated_start_date' => $startDate,
                'calculated_end_date' => $endDate,
                'detected_filter' => $detectedFilter,
                'final_date_filter' => $dateFilter
            ]);
            
        } elseif ($dateFilter && $dateFilter !== 'custom') {
            // User selected a predefined date filter - PRIORITY MEDIUM
            $dateRange = $this->getDateRangeFromFilter($dateFilter);
            $startDate = $dateRange['startDate'];
            $endDate = $dateRange['endDate'];
            
            // Log for debugging
            \Log::info('Weekly Report - Date Filter Selected', [
                'date_filter' => $dateFilter,
                'calculated_start_date' => $startDate,
                'calculated_end_date' => $endDate
            ]);
            
        } else {
            // Default: this week - PRIORITY LOW
            $dateRange = $this->getDateRangeFromFilter('this_week');
            $startDate = $dateRange['startDate'];
            $endDate = $dateRange['endDate'];
            $dateFilter = 'this_week';
            
            // Log for debugging
            \Log::info('Weekly Report - Default Filter', [
                'default_filter' => 'this_week',
                'calculated_start_date' => $startDate,
                'calculated_end_date' => $endDate
            ]);
        }
        
        // Get divisions for filter
        $divisions = DB::table('user_divisions')
            ->where('ud_status', 'active')
            ->orderBy('ud_name')
            ->get();
            
        // Get user positions for filter based on current user's level
        $userPositions = $this->getUserPositionsForFilter();
            
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
            
        // Special handling for NOW filter - only show staff who are currently working
        if ($dateFilter === 'now') {
            $currentTime = Carbon::now()->format('H:i:s');
            $currentDate = Carbon::now()->format('Y-m-d');
            
            // Get all schedules for today first, then filter in PHP for night shifts
            $allTodaySchedules = DB::table('daily_schedules')
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
                ->where('daily_schedules.ds_date', $currentDate)
                ->where('users.u_delete', '0')
                ->whereNotNull('users.u_nip');
                
            // Apply other filters
            if ($divisionId) {
                $allTodaySchedules->where('users.ud_id', $divisionId);
            }
            if ($shiftId) {
                $allTodaySchedules->where('daily_schedules.sc_id', $shiftId);
            }
            if ($positionId) {
                $allTodaySchedules->where('users.up_id', $positionId);
            }
            if ($userName) {
                $allTodaySchedules->where('users.u_name', 'like', '%' . $userName . '%');
            }
            
            $todaySchedules = $allTodaySchedules->get();
            
            // Filter for currently working staff
            $activeSchedules = $todaySchedules->filter(function($schedule) use ($currentTime) {
                $startTime = $schedule->sc_start_time ?: $schedule->ds_start_time;
                $endTime = $schedule->sc_end_time ?: $schedule->ds_end_time;
                
                if (!$startTime || !$endTime) {
                    return false;
                }
                
                // Convert times to Carbon for comparison
                $start = Carbon::createFromFormat('H:i:s', $startTime);
                $end = Carbon::createFromFormat('H:i:s', $endTime);
                $current = Carbon::createFromFormat('H:i:s', $currentTime);
                
                // Check if it's a night shift (end time is next day)
                if ($start->greaterThan($end)) {
                    // Night shift: current >= start OR current <= end
                    return $current->greaterThanOrEqualTo($start) || $current->lessThanOrEqualTo($end);
                } else {
                    // Regular shift: start <= current <= end
                    return $current->greaterThanOrEqualTo($start) && $current->lessThanOrEqualTo($end);
                }
            });
            
            // Convert back to query builder compatible format
            $activeScheduleIds = $activeSchedules->pluck('id')->toArray();
            
            if (empty($activeScheduleIds)) {
                // No active schedules, return empty result
                $scheduleQuery->whereRaw('1 = 0');
            } else {
                // Filter to only active schedules
                $scheduleQuery->whereIn('daily_schedules.id', $activeScheduleIds);
            }
                
            \Log::info('NOW Filter Applied', [
                'current_time' => $currentTime,
                'current_date' => $currentDate,
                'total_today_schedules' => $todaySchedules->count(),
                'active_schedules' => count($activeScheduleIds),
                'filter_conditions' => 'showing only staff currently working'
            ]);
        } else {
            // Apply filters for non-NOW cases
            if ($divisionId) {
                $scheduleQuery->where('users.ud_id', $divisionId);
            }
            
            if ($shiftId) {
                $scheduleQuery->where('daily_schedules.sc_id', $shiftId);
            }
            
            if ($positionId) {
                $scheduleQuery->where('users.up_id', $positionId);
            }
            if ($userName) {
                $scheduleQuery->where('users.u_name', 'like', '%' . $userName . '%');
            }
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
            'userPositions',
            'shiftCodes',
            'data',
            'divisionId',
            'positionId',
            'shiftId',
            'userName',
            'dateFilter'
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
                        
                        try {
                            // Try using Eloquent first
                            $newSchedule = DailySchedule::create([
                                'user_id' => $userId,
                                'ud_id' => $user->ud_id,
                                'sc_id' => $shiftCodeId,
                                'ds_date' => $date,
                                'ds_start_time' => $shiftCode->sc_start_time,
                                'ds_end_time' => $shiftCode->sc_end_time,
                                'ds_status' => 'scheduled',
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id
                            ]);
                            
                            \Log::info('Schedule created successfully with Eloquent', [
                                'schedule_id' => $newSchedule->id,
                                'user_id' => $userId,
                                'date' => $date
                            ]);
                            
                        } catch (\Exception $eloquentError) {
                            \Log::warning('Eloquent create failed, trying DB facade', [
                                'error' => $eloquentError->getMessage(),
                                'user_id' => $userId,
                                'date' => $date
                            ]);
                            
                            // Fallback to DB facade with explicit ID handling
                            $scheduleId = DB::table('daily_schedules')->insertGetId([
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
                            
                            \Log::info('Schedule created successfully with DB facade', [
                                'schedule_id' => $scheduleId,
                                'user_id' => $userId,
                                'date' => $date
                            ]);
                        }
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
            
            \Log::error('Weekly schedule save error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::user()->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving schedule: ' . $e->getMessage(),
                'debug_info' => [
                    'error_type' => get_class($e),
                    'error_code' => $e->getCode(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine()
                ]
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
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            // Process date filter and start_date with two-way synchronization (same as other methods)
            $originalStartDate = $request->get('start_date');
            
            if ($originalStartDate && $originalStartDate !== '') {
                // User manually selected a week range - PRIORITY HIGH
                // Calculate Monday of the week containing the selected date
                $selectedDate = Carbon::parse($originalStartDate);
                $dayOfWeek = $selectedDate->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
                $daysToSubtract = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1; // Convert to Monday-based (0 = Monday)
                
                $startDate = $selectedDate->copy()->subDays($daysToSubtract)->format('Y-m-d');
                $endDate = $selectedDate->copy()->addDays(6 - $daysToSubtract)->format('Y-m-d');
                
                // Auto-detect if this matches any predefined filter
                $detectedFilter = $this->detectFilterFromDateRange($startDate, $endDate);
                if ($detectedFilter !== 'custom') {
                    $dateFilter = $detectedFilter;
                } else {
                    $dateFilter = 'custom';
                }
                
                \Log::info('PDF Export - Week Range Selected', [
                    'original_start_date' => $originalStartDate,
                    'selected_date_day_of_week' => $dayOfWeek,
                    'days_to_subtract' => $daysToSubtract,
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate,
                    'detected_filter' => $detectedFilter,
                    'final_date_filter' => $dateFilter
                ]);
                
            } elseif ($dateFilter && $dateFilter !== 'custom') {
                // User selected a predefined date filter - PRIORITY MEDIUM
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
                
                \Log::info('PDF Export - Date Filter Selected', [
                    'date_filter' => $dateFilter,
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate
                ]);
                
            } else {
                // Default: this week - PRIORITY LOW
                $dateRange = $this->getDateRangeFromFilter('this_week');
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
                $dateFilter = 'this_week';
                
                \Log::info('PDF Export - Default Filter', [
                    'default_filter' => 'this_week',
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate
                ]);
            }

            // Build query to get users with their schedules
            $users = DB::table('users')
                ->select([
                    'users.id as user_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'user_types.ut_name',
                    'user_positions.up_name as position_name'  // Position name ditambahkan
                ])
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')  // JOIN dengan user_positions
                ->where('users.u_delete', '!=', '1')
                ->whereNotNull('users.u_nip'); // Hanya user dengan NIP

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
                ->whereBetween('daily_schedules.ds_date', [$startDate, $endDate])
                ->get();

            // Generate HTML content untuk dimasukkan ke PDF
            $htmlContent = $this->generateWeeklyScheduleHTML($users, $schedules, $startDate, $endDate);

            // Generate PDF
            $pdf = Pdf::loadHTML($htmlContent)
                      ->setPaper('a4', 'landscape'); // Use landscape for better table fit

            // Nama file dengan date range yang benar
            $filename = 'weekly-schedule-' . date('Y-m-d', strtotime($startDate)) . '-to-' . date('Y-m-d', strtotime($endDate)) . '.pdf';

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
            // Get parameters from request - EXPORT WEEKLY EXCEL METHOD
            $startDate = $request->get('start_date', date('Y-m-d', strtotime('monday this week')));
            $endDate = $request->get('end_date', date('Y-m-d', strtotime('sunday this week')));
            $divisionId = $request->get('division_id');
            $positionId = $request->get('position_id');  // Position filter ditambahkan
            $shiftId = $request->get('shift_id');        // Shift filter ditambahkan
            $userName = $request->get('user_name');      // User name filter ditambahkan
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
                    'user_positions.up_name as position_name',  // Position name ditambahkan
                    'shift_codes.sc_code',
                    'shift_codes.sc_shift_name',
                    'shift_codes.sc_start_time',
                    'shift_codes.sc_end_time'
                ])
                ->join('users', 'users.id', '=', 'daily_schedules.user_id')  // JOIN (bukan leftJoin) untuk hanya user dengan schedule
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')  // JOIN dengan user_positions
                ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
                ->where('users.u_delete', '!=', '1')  // Filter user yang tidak dihapus
                ->whereNotNull('users.u_nip')  // Hanya user dengan NIP
                ->whereExists(function($query) use ($startDate, $endDate) {
                    $query->select(DB::raw(1))
                          ->from('daily_schedules')
                          ->whereRaw('daily_schedules.user_id = users.id')
                          ->whereBetween('daily_schedules.ds_date', [$startDate, $endDate]);
                }); // Hanya user yang memiliki schedule
                
            if ($divisionId) {
                $query->where('users.ud_id', $divisionId);
            }
            
            if ($positionId) {  // Position filter ditambahkan
                $query->where('users.up_id', $positionId);
            }
            
            if ($shiftId) {  // Shift filter ditambahkan
                $query->where('daily_schedules.sc_id', $shiftId);
            }
            
            if ($userName) {  // User name filter ditambahkan
                $query->where('users.u_name', 'like', '%' . $userName . '%');
            }

            $schedules = $query->get();

            \Log::info('Export Weekly Report - Query Results', [
                'schedules_count' => $schedules->count(),
                'first_schedule' => $schedules->first(),
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'division_id' => $divisionId,
                'position_id' => $positionId,
                'shift_id' => $shiftId,
                'user_name' => $userName,
                'filters_applied' => [
                    'division' => $divisionId ? 'yes' : 'no',
                    'position' => $positionId ? 'yes' : 'no',
                    'shift' => $shiftId ? 'yes' : 'no',
                    'user_name' => $userName ? 'yes' : 'no'
                ]
            ]);

            // Generate filename
            $filename = 'weekly-schedule-report-' . $startDate . '-' . $endDate . '.xlsx';
            
            \Log::info('Export Weekly Report - Using Excel Facade', context: [
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
            // Get parameters from request - EXPORT WEEKLY PDF METHOD
            $startDate = $request->get('start_date', date('Y-m-d', strtotime('monday this week')));
            $endDate = $request->get('end_date', date('Y-m-d', strtotime('sunday this week')));
            $divisionId = $request->get('division_id');
            $positionId = $request->get('position_id');  // Position filter ditambahkan
            $shiftId = $request->get('shift_id');        // Shift filter ditambahkan
            $userName = $request->get('user_name');      // User name filter ditambahkan
            $dateFilter = $request->get('date_filter', 'this_week');

            // If date filter is provided, calculate dates
            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }

            // Build query to get users with their schedules (only users with NIP)
            $users = DB::table('users')
                ->select([
                    'users.id as user_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'user_types.ut_name',
                    'user_positions.up_name as position_name'  // Position name ditambahkan
                ])
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')  // JOIN dengan user_positions
                ->where('users.u_delete', '!=', '1')
                ->whereNotNull('users.u_nip') // Hanya user dengan NIP
                ->whereExists(function($query) use ($startDate, $endDate) {
                    $query->select(DB::raw(1))
                          ->from('daily_schedules')
                          ->whereRaw('daily_schedules.user_id = users.id')
                          ->whereBetween('daily_schedules.ds_date', [$startDate, $endDate]);
                }); // Hanya user yang memiliki schedule

            // Apply filters
            if ($divisionId) {
                $users->where('users.ud_id', $divisionId);
            }
            
            if ($positionId) {  
                $users->where('users.up_id', $positionId);
            }
            \Log::info('Export Weekly Report PDF : ' . $positionId);

            if ($shiftId) {  
                $users->whereExists(function($query) use ($shiftId, $startDate, $endDate) {
                    $query->select(DB::raw(1))
                          ->from('daily_schedules')
                          ->whereRaw('daily_schedules.user_id = users.id')
                          ->where('daily_schedules.sc_id', $shiftId)
                          ->whereBetween('daily_schedules.ds_date', [$startDate, $endDate]);
                });
            }
            
            if ($userName) {  // User name filter ditambahkan
                $users->where('users.u_name', 'like', '%' . $userName . '%');
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
            $originalStartDate = $request->get('start_date');

            // Use same synchronization logic as weekly input (updated to match other methods)
            if ($originalStartDate && $originalStartDate !== '') {
                // User manually selected a week range - PRIORITY HIGH
                // Calculate Monday of the week containing the selected date
                $selectedDate = Carbon::parse($originalStartDate);
                $dayOfWeek = $selectedDate->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
                $daysToSubtract = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1; // Convert to Monday-based (0 = Monday)
                
                $startDate = $selectedDate->copy()->subDays($daysToSubtract)->format('Y-m-d');
                $endDate = $selectedDate->copy()->addDays(6 - $daysToSubtract)->format('Y-m-d');
                
                // Auto-detect if this matches any predefined filter
                $detectedFilter = $this->detectFilterFromDateRange($startDate, $endDate);
                if ($detectedFilter !== 'custom') {
                    $dateFilter = $detectedFilter;
                } else {
                    $dateFilter = 'custom';
                }
                
                \Log::info('Export - Week Range Selected', [
                    'original_start_date' => $originalStartDate,
                    'selected_date_day_of_week' => $dayOfWeek,
                    'days_to_subtract' => $daysToSubtract,
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate,
                    'detected_filter' => $detectedFilter,
                    'final_date_filter' => $dateFilter
                ]);
                
            } elseif ($dateFilter && $dateFilter !== 'custom') {
                // User selected a predefined date filter - PRIORITY MEDIUM
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
                
                \Log::info('Export - Date Filter Selected', [
                    'date_filter' => $dateFilter,
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate
                ]);
                
            } else {
                // Default: this week - PRIORITY LOW
                $dateRange = $this->getDateRangeFromFilter('this_week');
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
                $dateFilter = 'this_week';
                
                \Log::info('Export - Default Filter', [
                    'default_filter' => 'this_week',
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate
                ]);
            }

            \Log::info('Date range calculated', [
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            // Build query to get users with their schedules (only users with NIP)
            $users = DB::table('users')
                ->select([
                    'users.id as user_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'user_types.ut_name',
                    'user_positions.up_name as position_name'  // Position name ditambahkan
                ])
                ->join('daily_schedules as ds', 'user_id', '=', 'ds.user_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')  // JOIN dengan user_positions
                ->where('users.u_delete', '!=', '1')
                ->whereNotNull('users.u_nip') // ✅ Hanya user dengan NIP
                ->whereBetween('ds.ds_date', [$startDate, $endDate]); // Only users with schedules in the month

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
                ->whereBetween('daily_schedules.ds_date', [$startDate, $endDate])
                ->get();

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

            // Convert groupedSchedules to exportData format (same as weeklyReport)
            $exportData = [];
            
            foreach ($groupedSchedules as $divisionName => $divisionUsers) {
                // Add division header row
                $exportData[] = [
                    'Division' => $divisionName,
                    'Staff' => count($divisionUsers),
                    'NIP' => '',
                    'Nama' => '',
                    'Divisi' => '',
                    'User Type' => ''
                ];
                
                // Add date header row
                $dateHeader = ['Division', 'Staff', 'NIP', 'Nama', 'Divisi', 'User Type'];
                $currentDate = \Carbon\Carbon::parse($startDate);
                $endDateCarbon = \Carbon\Carbon::parse($endDate);
                
                while ($currentDate->lte($endDateCarbon)) {
                    $dateHeader[] = $currentDate->format('d/m (D)');
                    $currentDate = $currentDate->copy()->addDay();
                }
                $exportData[] = $dateHeader;
                
                // Add user data rows
                foreach ($divisionUsers as $userId => $userData) {
                    $userRow = [
                        'Division' => '',
                        'Staff' => '',
                        'NIP' => $userData['u_nip'] ?? '-',
                        'Nama' => $userData['u_name'] ?? '-',
                        'Divisi' => $userData['ud_name'] ?? '-',
                        'User Type' => 'FULL TIME' // Default value
                    ];
                    
                    // Add schedule data for each day
                    $currentDate = \Carbon\Carbon::parse($startDate);
                    while ($currentDate->lte($endDateCarbon)) {
                        $dateStr = $currentDate->format('Y-m-d');
                        $schedule = $userData['schedules'][$dateStr] ?? null;
                        
                        if ($schedule && $schedule['sc_code']) {
                            $userRow[] = $schedule['sc_code'];
                        } else {
                            $userRow[] = '-';
                        }
                        
                        $currentDate = $currentDate->copy()->addDay();
                    }
                    
                    $exportData[] = $userRow;
                }
                
                // Add empty row between divisions
                $exportData[] = [];
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

            // Generate filename dengan date range yang benar
            $filename = 'weekly-schedule-' . date('Y-m-d', strtotime($startDate)) . '-to-' . date('Y-m-d', strtotime($endDate)) . '.xlsx';
            
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
     * Menampilkan laporan jadwal bulanan dengan calendar view
     */
    public function monthlyReport(Request $request)
    {
        $this->validateAccess();
        
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        
        // Get parameters from request
        $month = $request->get('month', date('Y-m'));
        $divisionId = $request->get('division_id');
        $positionId = $request->get('position_id');
        $shiftId = $request->get('shift_id');
        $userName = $request->get('user_name');
        $monthFilter = $request->get('month_filter', 'this_month');
        
        // Process month filter if provided AND month is not explicitly set
        if ($monthFilter && $monthFilter !== 'custom' && !$request->has('month')) {
            $month = $this->getMonthFromFilter($monthFilter);
        }
        
        // Parse month to get start and end dates
        $startDate = date('Y-m-01', strtotime($month . '-01'));
        $endDate = date('Y-m-t', strtotime($month . '-01'));
        
        // Get divisions for filter
        $divisions = DB::table('user_divisions')
            ->where('ud_status', 'active')
            ->orderBy('ud_name')
            ->get();
            
        // Get user positions for filter based on current user's level
        $userPositions = $this->getUserPositionsForFilter();
            
        // Get shift codes for filter
        $shiftCodes = DB::table('shift_codes')
            ->where('sc_status', '!=', 'deleted')
            ->orderBy('sc_code')
            ->get();
        
        // Get users based on filters (only users with NIP)
        $usersQuery = DB::table('users as u')
            ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
            ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
            ->select([
                'u.id as user_id',
                'u.u_nip',
                'u.u_name',
                'ud.ud_name',
                'up.up_name as position_name'
            ])
            ->where('u.u_delete', '!=', '1')
            ->whereNotNull('u.u_nip'); // ✅ Hanya user dengan NIP
        
        if ($divisionId) {
            $usersQuery->where('u.ud_id', $divisionId);
        }
        
        if ($positionId) {
            $usersQuery->where('u.up_id', $positionId);
        }
        if ($userName) {
            $usersQuery->where('u.u_name', 'like', '%' . $userName . '%');
        }
        
        $users = $usersQuery->orderBy('ud.ud_name')->orderBy('u.u_name')->get();
        
        // Ensure we always have division grouping even if no division filter
        if ($users->count() > 0) {
            // If no division filter, group by actual divisions
            // If division filter applied, still group by division name for consistency
            // Don't change ud_name to 'No Division' - keep original division names
        }
        
        // Get schedules for the month with user and division info
        $schedulesQuery = DB::table('daily_schedules as ds')
            ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
            ->leftJoin('users as u', 'ds.user_id', '=', 'u.id')
            ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
            ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
            ->select([
                'ds.user_id',
                'ds.ds_date',
                'ds.sc_id',
                'sc.sc_code',
                'sc.sc_shift_name',
                'sc.sc_start_time',
                'sc.sc_end_time',
                'u.u_nip',
                'u.u_name',
                'ud.ud_name',
                'up.up_name as position_name'
            ])
            ->whereBetween('ds.ds_date', [$startDate, $endDate])
            ->where('u.u_delete', '!=', '1');
        
        // Apply division filter to schedules query
        if ($divisionId) {
            $schedulesQuery->where('u.ud_id', $divisionId);
        }
        
        // Apply position filter to schedules query
        if ($positionId) {
            $schedulesQuery->where('u.up_id', $positionId);
        }
        
        // Apply shift filter to schedules query
        if ($shiftId) {
            $schedulesQuery->where('ds.sc_id', $shiftId);
        }
        
        // Apply user name filter to schedules query
        if ($userName) {
            $schedulesQuery->where('u.u_name', 'like', '%' . $userName . '%');
        }
        
        $schedules = $schedulesQuery->orderBy('ud.ud_name')
            ->orderBy('u.u_name')
            ->orderBy('ds.ds_date')
            ->get();
        
        // Get only users that have schedules in the month (filtered by existing filters)
        $usersWithSchedulesQuery = DB::table('users as u')
            ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
            ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
            ->join('daily_schedules as ds', 'u.id', '=', 'ds.user_id')
            ->select([
                'u.id as user_id',
                'u.u_nip',
                'u.u_name',
                'ud.ud_name',
                'up.up_name as position_name'
            ])
            ->where('u.u_delete', '!=', '1')
            ->whereNotNull('u.u_nip') // Only users with NIP
            ->whereBetween('ds.ds_date', [$startDate, $endDate]); // Only users with schedules in the month
        
        // Apply the same filters to users query
        if ($divisionId) {
            $usersWithSchedulesQuery->where('u.ud_id', $divisionId);
        }
        
        if ($positionId) {
            $usersWithSchedulesQuery->where('u.up_id', $positionId);
        }
        
        if ($userName) {
            $usersWithSchedulesQuery->where('u.u_name', 'like', '%' . $userName . '%');
        }
        
        if ($shiftId) {
            $usersWithSchedulesQuery->where('ds.sc_id', $shiftId);
        }
        
        $allUsers = $usersWithSchedulesQuery->distinct()->orderBy('ud.ud_name')->orderBy('u.u_name')->get();
        
        // Group schedules by division and user (same logic as weekly report)
        $groupedSchedules = [];
        
        // First, create structure for all users
        foreach ($allUsers as $user) {
            $divisionName = $user->ud_name ?: 'No Division';
            $userId = $user->user_id;
            
            if (!isset($groupedSchedules[$divisionName])) {
                $groupedSchedules[$divisionName] = [];
            }
            
            $groupedSchedules[$divisionName][$userId] = [
                'user_id' => $userId,
                'u_nip' => $user->u_nip,
                'u_name' => $user->u_name,
                'ud_name' => $user->ud_name,
                'position_name' => $user->position_name,
                'schedules' => []
            ];
        }
        
        // Then, add schedules for users who have them
        foreach ($schedules as $schedule) {
            $divisionName = $schedule->ud_name ?: 'No Division';
            $userId = $schedule->user_id;
            $date = $schedule->ds_date;
            
            if (isset($groupedSchedules[$divisionName][$userId])) {
                $groupedSchedules[$divisionName][$userId]['schedules'][$date] = [
                    'sc_code' => $schedule->sc_code,
                    'sc_shift_name' => $schedule->sc_shift_name,
                    'sc_start_time' => $schedule->sc_start_time,
                    'sc_end_time' => $schedule->sc_end_time
                ];
            }
        }
        
        // Generate calendar dates for the month
        $calendarDates = $this->generateCalendarDates($startDate, $endDate);
        
        $data = [
            'title' => 'JEZ SYSTEM',
            'subtitle' => 'Monthly Schedule Report',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        
        return view('app.daily_schedule.monthly_report', compact(
            'groupedSchedules', 
            'calendarDates', 
            'startDate', 
            'endDate',
            'month',
            'divisions', 
            'userPositions',
            'shiftCodes',
            'data',
            'divisionId',
            'positionId',
            'shiftId',
            'userName',
            'users'
        ));
    }
    
    /**
     * Generate calendar dates for a month
     */
    private function generateCalendarDates($startDate, $endDate)
    {
        $dates = [];
        
        // Find the Monday of the week containing the start date
        $startTimestamp = strtotime($startDate);
        $dayOfWeek = date('N', $startTimestamp); // 1 (Monday) to 7 (Sunday)
        
        // Calculate days to subtract to get to Monday
        $daysToSubtract = $dayOfWeek - 1; // If start date is Monday (1), subtract 0; if Tuesday (2), subtract 1, etc.
        
        // Start from Monday of the week containing start date
        $calendarStartDate = strtotime('-' . $daysToSubtract . ' days', $startTimestamp);
        
        // End date should be Sunday of the week containing end date
        $endTimestamp = strtotime($endDate);
        $endDayOfWeek = date('N', $endTimestamp);
        $daysToAdd = 7 - $endDayOfWeek; // If end date is Sunday (7), add 0; if Saturday (6), add 1, etc.
        
        $calendarEndDate = strtotime('+' . $daysToAdd . ' days', $endTimestamp);
        
        $currentDate = $calendarStartDate;
        
        while ($currentDate <= $calendarEndDate) {
            $date = date('Y-m-d', $currentDate);
            $dayOfWeek = date('N', $currentDate); // 1 (Monday) to 7 (Sunday)
            
            $dates[] = [
                'date' => $date,
                'day' => date('d', $currentDate),
                'day_name' => date('D', $currentDate),
                'day_of_week' => $dayOfWeek,
                'is_weekend' => $dayOfWeek >= 6,
                'is_current_month' => $currentDate >= $startTimestamp && $currentDate <= $endTimestamp
            ];
            
            $currentDate = strtotime('+1 day', $currentDate);
        }
        
        return $dates;
    }
    
    /**
     * Get month from filter
     */
    private function getMonthFromFilter($filter)
    {
        $today = now();
        
        switch ($filter) {
            case 'this_month':
                return $today->format('Y-m');
            case 'last_month':
                return $today->subMonth()->format('Y-m');
            case 'next_month':
                return $today->addMonth()->format('Y-m');
            default:
                return $today->format('Y-m');
        }
    }
    
    /**
     * Export monthly report to Excel
     */
    public function exportMonthlyExcel(Request $request)
    {
        try {
            $this->validateAccess();
            
            // Get parameters from request - EXPORT EXCEL METHOD
            $month = $request->get('month', date('Y-m'));
            $divisionId = $request->get('division_id');
            $positionId = $request->get('position_id');  // Position filter ditambahkan
            $shiftId = $request->get('shift_id');
            $userName = $request->get('user_name');
            $monthFilter = $request->get('month_filter', 'this_month');
            
            // Process month filter if provided AND month is not explicitly set
            if ($monthFilter && $monthFilter !== 'custom' && !$request->has('month')) {
                $month = $this->getMonthFromFilter($monthFilter);
            }
            
            // Parse month to get start and end dates
            $startDate = date('Y-m-01', strtotime($month . '-01'));
            $endDate = date('Y-m-t', strtotime($month . '-01'));
            
            // Get only users that have schedules in the month (filtered by existing filters) - EXPORT EXCEL
            $usersQuery = DB::table('users as u')
                ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
                ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
                ->join('daily_schedules as ds', 'u.id', '=', 'ds.user_id')  // JOIN dengan daily_schedules
                ->select([
                    'u.id as user_id',
                    'u.u_nip',
                    'u.u_name',
                    'ud.ud_name',
                    'up.up_name as position_name'
                ])
                ->where('u.u_delete', '!=', '1')
                ->whereNotNull('u.u_nip') // Only users with NIP
                ->whereBetween('ds.ds_date', [$startDate, $endDate]); // Hanya user dengan schedule di bulan tersebut
            
            if ($divisionId) {
                $usersQuery->where('u.ud_id', $divisionId);
            }
            
            if ($positionId) {  // Position filter ditambahkan
                $usersQuery->where('u.up_id', $positionId);
            }
            
            if ($userName) {
                $usersQuery->where('u.u_name', 'like', '%' . $userName . '%');
            }
            
            $users = $usersQuery->distinct()->orderBy('ud.ud_name')->orderBy('u.u_name')->get();
            
            // Get schedules for the month
            $schedulesQuery = DB::table('daily_schedules as ds')
                ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
                ->select([
                    'ds.user_id',
                    'ds.ds_date',
                    'sc.sc_code',
                    'sc.sc_shift_name',
                    'sc.sc_start_time',
                    'sc.sc_end_time'
                ])
                ->whereBetween('ds.ds_date', [$startDate, $endDate]);
            
            if ($shiftId) {
                $schedulesQuery->where('ds.sc_id', $shiftId);
            }
            
            $schedules = $schedulesQuery->get();
            
            // Group schedules by user and date
            $groupedSchedules = [];
            foreach ($schedules as $schedule) {
                $userId = $schedule->user_id;
                $date = $schedule->ds_date;
                
                if (!isset($groupedSchedules[$userId])) {
                    $groupedSchedules[$userId] = [];
                }
                
                $groupedSchedules[$userId][$date] = [
                    'sc_code' => $schedule->sc_code,
                    'sc_shift_name' => $schedule->sc_shift_name,
                    'sc_start_time' => $schedule->sc_start_time,
                    'sc_end_time' => $schedule->sc_end_time
                ];
            }
            
            // Prepare export data in table format (similar to weekly report but with more columns)
            $exportData = [];
            
            // Generate calendar dates for the month
            $calendarDates = $this->generateCalendarDates($startDate, $endDate);
            
            // Group users by division
            $usersByDivision = $users->groupBy('ud_name');

            foreach ($usersByDivision as $divisionName => $divisionUsers) {
                // Add division header row (same format as weekly input - only division name and staff count)
                $exportData[] = [
                    "Division: {$divisionName}    " . count($divisionUsers) . " Staff"
                ];
                
                // Add date header row (same format as weekly input)
                $dateHeaders = ['NIP', 'Nama', 'Divisi'];
                foreach ($calendarDates as $dateInfo) {
                    $dateHeaders[] = date('d/m', strtotime($dateInfo['date'])) . ' (' . date('D', strtotime($dateInfo['date'])) . ')';
                }
                $exportData[] = $dateHeaders;
                
                // Add user data rows (keep original monthly report format)
                foreach ($divisionUsers as $user) {
                    $row = [
                        $user->u_nip,
                        $user->u_name,
                        $user->ud_name
                    ];

                    // Add schedule data for each day of the month (keep original format)
                    foreach ($calendarDates as $dateInfo) {
                        $date = $dateInfo['date'];
                        $schedule = $groupedSchedules[$user->user_id][$date] ?? null;
                        
                        if ($schedule) {
                            $row[] = $schedule['sc_code'] . ' - ' . $schedule['sc_shift_name'] . 
                                   ' (' . date('H:i', strtotime($schedule['sc_start_time'])) . '-' . date('H:i', strtotime($schedule['sc_end_time'])) . ')';
                        } else {
                            $row[] = 'No Schedule';
                        }
                    }
                    
                    $exportData[] = $row;
                }
                
                // Add empty row between divisions (same as weekly input)
                $exportData[] = [];
            }
            
            // Generate filename
            $filename = 'monthly-schedule-report-' . $startDate . '-' . $endDate . '.xlsx';
            
            // Debug: Log export data structure (same format as weekly report)
            \Log::info('Export Monthly Data Structure Debug', [
                'export_data_count' => count($exportData),
                'first_row' => $exportData[0] ?? 'No data',
                'data_structure' => array_keys($exportData[0] ?? []),
                'export_class' => 'MonthlyScheduleExport',
                'calendar_dates_sample' => array_slice($calendarDates, 0, 3),
                'sample_user_row' => $exportData[1] ?? 'No user data',
                'month_filter' => $monthFilter,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            
            // Use Excel facade to export
            try {
                $export = new \App\Exports\MonthlyScheduleExport($exportData, $startDate, $endDate);
                \Log::info('Export object created successfully', [
                    'export_class' => get_class($export),
                    'data_count' => count($exportData)
                ]);
                
                return Excel::download($export, $filename);
            } catch (\Exception $e) {
                \Log::error('Excel download error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
            
        } catch (\Exception $e) {
            \Log::error('Export Monthly Report Excel Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat export report Excel: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export monthly report to PDF
     */
    public function exportMonthlyPDF(Request $request)
    {
        try {
            $this->validateAccess();
            
            // Get parameters from request - EXPORT PDF METHOD
            $month = $request->get('month', date('Y-m'));
            $divisionId = $request->get('division_id');
            $positionId = $request->get('position_id');
            $shiftId = $request->get('shift_id');
            $userName = $request->get('user_name');
            $monthFilter = $request->get('month_filter', 'this_month');
            
            // Process month filter if provided AND month is not explicitly set
            if ($monthFilter && $monthFilter !== 'custom' && !$request->has('month')) {
                $month = $this->getMonthFromFilter($monthFilter);
            }
            
            // Parse month to get start and end dates
            $startDate = date('Y-m-01', strtotime($month . '-01'));
            $endDate = date('Y-m-t', strtotime($month . '-01'));
            
            // Get only users that have schedules in the month (filtered by existing filters) - EXPORT PDF
            $usersQuery = DB::table('users as u')
                ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
                ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
                ->join('daily_schedules as ds', 'u.id', '=', 'ds.user_id')  // JOIN dengan daily_schedules
                ->select([
                    'u.id as user_id',
                    'u.u_nip',
                    'u.u_name',
                    'ud.ud_name',
                    'up.up_name as position_name'
                ])
                ->where('u.u_delete', '!=', '1')
                ->whereNotNull('u.u_nip') // Only users with NIP
                ->whereBetween('ds.ds_date', [$startDate, $endDate]); 
            
            if ($divisionId) {
                $usersQuery->where('u.ud_id', $divisionId);
            }
            
            if ($positionId) {  // Position filter ditambahkan
                $usersQuery->where('u.up_id', $positionId);
            }
            
            if ($userName) {
                $usersQuery->where('u.u_name', 'like', '%' . $userName . '%');
            }
            
            $users = $usersQuery->distinct()->orderBy('ud.ud_name')->orderBy('u.u_name')->get();
            
            // Get schedules for the month
            $schedulesQuery = DB::table('daily_schedules as ds')
                ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
                ->select([
                    'ds.user_id',
                    'ds.ds_date',
                    'sc.sc_code',
                    'sc.sc_shift_name',
                    'sc.sc_start_time',
                    'sc.sc_end_time'
                ])
                ->whereBetween('ds.ds_date', [$startDate, $endDate]);
            
            if ($shiftId) {
                $schedulesQuery->where('ds.sc_id', $shiftId);
            }
            
            $schedules = $schedulesQuery->get();
            
            // Group schedules by user and date
            $groupedSchedules = [];
            foreach ($schedules as $schedule) {
                $userId = $schedule->user_id;
                $date = $schedule->ds_date;
                
                if (!isset($groupedSchedules[$userId])) {
                    $groupedSchedules[$userId] = [];
                }
                
                $groupedSchedules[$userId][$date] = [
                    'sc_code' => $schedule->sc_code,
                    'sc_shift_name' => $schedule->sc_shift_name,
                    'sc_start_time' => $schedule->sc_start_time,
                    'sc_end_time' => $schedule->sc_end_time
                ];
            }
            
            // Generate HTML content for report
            $htmlContent = $this->generateMonthlyReportHTML($users, $groupedSchedules, $startDate, $endDate);
            
            // Generate PDF
            $pdf = Pdf::loadHTML($htmlContent)
                      ->setPaper('a4', 'landscape'); // Use landscape for better table fit
            
            // Generate filename
            $filename = 'monthly-schedule-report-' . $startDate . '-' . $endDate . '.pdf';
            
            // Download PDF
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Export Monthly Report PDF Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat export report PDF: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate HTML content for monthly report PDF
     */
    private function generateMonthlyReportHTML($users, $groupedSchedules, $startDate, $endDate)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Monthly Schedule Report</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h1 { margin: 0; color: #333; }
                .header p { margin: 5px 0; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .division-header { background-color: #e3f2fd; font-weight: bold; padding: 10px; margin: 10px 0; }
                .user-header { background-color: #f5f5f5; font-weight: bold; }
                .weekend { background-color: #fff3cd; }
                .no-schedule { color: #999; font-style: italic; }
                .other-month { background-color: #f8f9fa; opacity: 0.7; }
                .other-month .no-schedule { color: #ccc; }
                
                /* Shift Code Colors - Following View Colors */
                .shift-S { background-color: #f8e8e6; color: #333; } /* Sakit - Light Red */
                .shift-NA { background-color: #f8e8e6; color: #333; } /* N/A - Light Red */
                .shift-LPH { background-color: #f8e8e6; color: #333; } /* Libur - Light Red */
                .shift-LL { background-color: #f8e8e6; color: #333; } /* Libur Cuti - Light Red */
                .shift-L { background-color: #f8e8e6; color: #333; } /* Libur - Light Red */
                .shift-I { background-color: #f8e8e6; color: #333; } /* Izin - Light Red */
                
                /* Shift 1 & Shift 0 */
                .shift-FS1 { background-color: #eaf5fb; color: #333; } /* Full Shift 1 - Light Blue */
                .shift-FS0 { background-color: #eaf5fb; color: #333; } /* Full Shift 0 - Light Blue */
                .shift-PL1 { background-color: #eaf5fb; color: #333; } /* Part Libur 1 - Shift 1 (Light Blue) */
                .shift-PL2 { background-color: #eaf5fb; color: #333; } /* Part Libur 2 - Shift 1 (Light Blue) */
                
                /* Shift 2 */
                .shift-FS2 { background-color: #e5f6f3; color: #333; } /* Full Shift 2 - Light Green */
                
                /* Full & Full Shift 0 */
                .shift-FSE { background-color: #FFF9ED; color: #333; } /* Full Shift - Light Yellow */
                .shift-FM2 { background-color: #FFF9ED; color: #333; } /* Full Morning 2 - Light Yellow */
                .shift-FM1 { background-color: #FFF9ED; color: #333; } /* Full Morning 1 - Light Yellow */
                .shift-PFM { background-color: #FFF9ED; color: #333; } /* Part Full Morning - Light Yellow */
                .shift-PF0 { background-color: #FFF9ED; color: #333; } /* Part Full 0 - Light Yellow */
                .shift-PF { background-color: #FFF9ED; color: #333; } /* Part Full - Light Yellow */
                
                /* Part Shifts */
                .shift-SM2 { background-color: #eaf5fb; color: #333; } /* Shift Morning 2 - Light Blue */
                .shift-SM1 { background-color: #eaf5fb; color: #333; } /* Shift Morning 1 - Light Blue */
                .shift-PS2 { background-color: #e5f6f3; color: #333; } /* Part Shift 2 - Light Green */
                .shift-PS1 { background-color: #e5f6f3; color: #333; } /* Part Shift 1 - Light Green */
                .shift-PS0 { background-color: #e5f6f3; color: #333; } /* Part Shift 0 - Light Green */
                
                /* Color Legend Styles */
                .color-legend { 
                    background-color: #f8f9fa; 
                    padding: 15px; 
                    border-radius: 8px; 
                    border: 1px solid #e1e5e9; 
                    margin-bottom: 20px;
                }
                .color-legend h6 { 
                    margin: 0 0 15px 0; 
                    color: #6c757d; 
                    font-size: 14px; 
                }
                .legend-items { 
                    display: flex; 
                    flex-wrap: wrap; 
                    gap: 15px; 
                }
                .legend-item { 
                    display: flex; 
                    align-items: center; 
                    background-color: white; 
                    padding: 8px 12px; 
                    border-radius: 6px; 
                    border: 1px solid #e1e5e9; 
                    box-shadow: 0 1px 2px rgba(0,0,0,0.1); 
                }
                .legend-color { 
                    width: 20px; 
                    height: 20px; 
                    border-radius: 4px; 
                    margin-right: 8px; 
                    border: 1px solid #dee2e6; 
                }
                .legend-text { 
                    font-size: 12px; 
                    color: #333; 
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Monthly Schedule Report</h1>
                <p>Period: ' . date('d F Y', strtotime($startDate)) . ' - ' . date('d F Y', strtotime($endDate)) . '</p>
                <p>Generated on: ' . date('d F Y H:i:s') . '</p>
            </div>
            
            ';
        
        // Group users by division
        $usersByDivision = $users->groupBy('ud_name');
        
        foreach ($usersByDivision as $divisionName => $divisionUsers) {
            // Filter users who have schedules in the month
            $usersWithSchedules = [];
            foreach ($divisionUsers as $user) {
                $userSchedules = $groupedSchedules[$user->user_id] ?? [];
                if (count($userSchedules) > 0) {
                    $usersWithSchedules[] = $user;
                }
            }
            
            if (count($usersWithSchedules) == 0) {
                continue; // Skip division if no users have schedules
            }
            
            $html .= '<div class="division-header">Division: ' . $divisionName . ' (' . count($usersWithSchedules) . ' staff)</div>';
            
            foreach ($usersWithSchedules as $user) {
                $html .= '<table>
                    <tr class="user-header">
                        <td colspan="7">' . $user->u_name . ' (' . $user->u_nip . ') - ' . $user->position_name . '</td>
                    </tr>
                    <tr>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                        <th>Sun</th>
                    </tr>';
                
                // Generate weeks for this user
                $userSchedules = $groupedSchedules[$user->user_id] ?? [];
                $weeks = $this->generateWeeksForUser($startDate, $endDate, $userSchedules);
                
                foreach ($weeks as $week) {
                    $html .= '<tr>';
                    foreach ($week as $dateInfo) {
                        $cellClass = '';
                        if (!$dateInfo['is_current_month']) $cellClass .= ' other-month';
                        
                        // Add shift color class to the entire cell
                        if ($dateInfo['schedule']) {
                            $shiftCode = $dateInfo['schedule']['sc_code'];
                            $shiftClass = 'shift-' . str_replace(['/', ' '], ['', ''], $shiftCode);
                            $cellClass .= ' ' . $shiftClass;
                        }
                        
                        $html .= '<td class="' . $cellClass . '">';
                        $html .= '<div style="font-weight: bold;' . (!$dateInfo['is_current_month'] ? ' color: #999;' : '') . '">' . $dateInfo['day'] . '</div>';
                        
                        if ($dateInfo['schedule']) {
                            $html .= '<div>' . $dateInfo['schedule']['sc_code'] . '</div>';
                            $html .= '<div>' . $dateInfo['schedule']['sc_shift_name'] . '</div>';
                            $html .= '<div style="font-size: 10px;">' . date('H:i', strtotime($dateInfo['schedule']['sc_start_time'])) . ' - ' . date('H:i', strtotime($dateInfo['schedule']['sc_end_time'])) . '</div>';
                        } else {
                            $html .= '<div class="no-schedule"' . (!$dateInfo['is_current_month'] ? ' style="color: #ccc;"' : '') . '>No Schedule</div>';
                        }
                        
                        $html .= '</td>';
                    }
                    $html .= '</tr>';
                }
                
                $html .= '</table><br>';
            }
        }
        
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * Generate weeks for a specific user
     */
    private function generateWeeksForUser($startDate, $endDate, $userSchedules)
    {
        $weeks = [];
        
        // Find the Monday of the week containing the start date
        $startTimestamp = strtotime($startDate);
        $dayOfWeek = date('N', $startTimestamp); // 1 (Monday) to 7 (Sunday)
        
        // Calculate days to subtract to get to Monday
        $daysToSubtract = $dayOfWeek - 1; // If start date is Monday (1), subtract 0; if Tuesday (2), subtract 1, etc.
        
        // Start from Monday of the week containing start date
        $calendarStartDate = strtotime('-' . $daysToSubtract . ' days', $startTimestamp);
        
        // End date should be Sunday of the week containing end date
        $endTimestamp = strtotime($endDate);
        $endDayOfWeek = date('N', $endTimestamp);
        $daysToAdd = 7 - $endDayOfWeek; // If end date is Sunday (7), add 0; if Saturday (6), add 1, etc.
        
        $calendarEndDate = strtotime('+' . $daysToAdd . ' days', $endTimestamp);
        
        $currentDate = $calendarStartDate;
        $currentWeek = [];
        
        while ($currentDate <= $calendarEndDate) {
            $date = date('Y-m-d', $currentDate);
            $dayOfWeek = date('N', $currentDate);
            $schedule = $userSchedules[$date] ?? null;
            $isCurrentMonth = $currentDate >= $startTimestamp && $currentDate <= $endTimestamp;
            
            $currentWeek[] = [
                'day' => date('d', $currentDate),
                'date' => $date,
                'is_weekend' => $dayOfWeek >= 6,
                'schedule' => $schedule,
                'is_current_month' => $isCurrentMonth
            ];
            
            if ($dayOfWeek == 7) { // Sunday
                $weeks[] = $currentWeek;
                $currentWeek = [];
            }
            
            $currentDate = strtotime('+1 day', $currentDate);
        }
        
        // Add remaining days if any
        if (!empty($currentWeek)) {
            $weeks[] = $currentWeek;
        }
        
        return $weeks;
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
                    'position_id' => $request->get('position_id'),
                    'shift_id' => $request->get('shift_id'),
                    'user_name' => $request->get('user_name'),
                    'date_filter' => $request->get('date_filter', 'this_week'),
                    'start_date' => $request->get('start_date'),
                    'end_date' => $request->get('end_date')
                ]
            ]);

            $divisionId = $request->get('division_id');
            $positionId = $request->get('position_id');
            $shiftId = $request->get('shift_id');
            $userName = $request->get('user_name');
            $dateFilter = $request->get('date_filter', 'this_week');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            // Process date filter and start_date with two-way synchronization (same as weeklyReport)
            $originalStartDate = $request->get('start_date');
            
            if ($originalStartDate && $originalStartDate !== '') {
                // User manually selected a week range - PRIORITY HIGH
                // Calculate Monday of the week containing the selected date
                $selectedDate = Carbon::parse($originalStartDate);
                $dayOfWeek = $selectedDate->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
                $daysToSubtract = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1; // Convert to Monday-based (0 = Monday)
                
                $startDate = $selectedDate->copy()->subDays($daysToSubtract)->format('Y-m-d');
                $endDate = $selectedDate->copy()->addDays(6 - $daysToSubtract)->format('Y-m-d');
                
                // Auto-detect if this matches any predefined filter
                $detectedFilter = $this->detectFilterFromDateRange($startDate, $endDate);
                if ($detectedFilter !== 'custom') {
                    $dateFilter = $detectedFilter;
                } else {
                    $dateFilter = 'custom';
                }
                
                \Log::info('Excel Export - Week Range Selected', [
                    'original_start_date' => $originalStartDate,
                    'selected_date_day_of_week' => $dayOfWeek,
                    'days_to_subtract' => $daysToSubtract,
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate,
                    'detected_filter' => $detectedFilter,
                    'final_date_filter' => $dateFilter
                ]);
                
            } elseif ($dateFilter && $dateFilter !== 'custom') {
                // User selected a predefined date filter - PRIORITY MEDIUM
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
                
                \Log::info('Excel Export - Date Filter Selected', [
                    'date_filter' => $dateFilter,
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate
                ]);
                
            } else {
                // Default: this week - PRIORITY LOW
                $dateRange = $this->getDateRangeFromFilter('this_week');
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
                $dateFilter = 'this_week';
                
                \Log::info('Excel Export - Default Filter', [
                    'default_filter' => 'this_week',
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate
                ]);
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
                    'user_types.ut_name',
                    'user_positions.up_name as position_name'  // Position name ditambahkan
                ])
                ->join('daily_schedules as ds', 'users.id', '=', 'ds.user_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')  // JOIN dengan user_positions
                ->where('users.u_delete', '!=', '1')
                ->whereNotNull('users.u_nip')
                ->whereBetween('ds.ds_date', [$startDate, $endDate]); // Hanya user dengan schedule di bulan tersebut

            // Apply filters
            if ($divisionId) {
                $users->where('users.ud_id', $divisionId);
                \Log::info('Division filter applied', ['division_id' => $divisionId]);
            }


            if ($positionId) {
                $users->where('users.up_id', $positionId);
                \Log::info('Position filter applied', ['position_id' => $positionId]);
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

                        $weekDates = [];
                        for ($i = 0; $i < 7; $i++) {
                            $weekDates[] = date('Y-m-d', strtotime($startDate . " +{$i} days"));
                        }

                        $exportData = [];

            foreach ($groupedSchedules as $divisionName => $divisionUsers) {
                // === Baris judul division (hanya 1 kolom, nanti bisa di-merge di AfterSheet) ===
                $exportData[] = [
                    "Division: {$divisionName}    " . count($divisionUsers) . " Staff"
                ];

                // Header tabel
                $dateHeaders = ['NIP', 'Nama', 'Divisi'];
                foreach ($weekDates as $date) {
                    $dateHeaders[] = date('d/m', strtotime($date)) . ' (' . date('l', strtotime($date)) . ')';
                }
                $exportData[] = $dateHeaders;

                // Data user
                foreach ($divisionUsers as $userId => $userData) {
                    $row = [
                        $userData['u_nip'],
                        $userData['u_name'],
                        $userData['ud_name']
                    ];

                    foreach ($weekDates as $date) {
                        $schedule = $userData['schedules'][$date] ?? null;
                        if ($schedule) {
                            $row[] = $schedule['sc_code'] . ' - ' . $schedule['sc_shift_name'] .
                                    ' (' . date('H:i', strtotime($schedule['sc_start_time'])) .
                                    '-' . date('H:i', strtotime($schedule['sc_end_time'])) . ')';
                        } else {
                            $row[] = 'No Schedule';
                        }
                    }

                    $exportData[] = $row;
                }

                // Baris kosong antar division
                $exportData[] = [];
            }




            // Generate filename
            $filename = 'weekly-schedule-report-' . $startDate . '-' . $endDate . '.xlsx';
            
            \Log::info('Public Export Weekly Reportsssss - Using Excel Facade', [
                'filename' => $filename,
                'export_data_count' => count($exportData)
            ]);

            // Debug: Log export data structure
            \Log::info('Export Data Structure Debug', [
                'export_data_count' => count($exportData),
                'first_row' => $exportData[0] ?? 'No data',
                'data_structure' => array_keys($exportData[0] ?? []),
                'export_class' => 'WeeklyReportExport',
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            // Use Excel facade to export
            try {
                $export = new \App\Exports\WeeklyReportExport($exportData, $startDate, $endDate);
                \Log::info('Export object created successfully', [
                    'export_class' => get_class($export),
                    'data_count' => count($exportData)
                ]);
                
                return Excel::download($export, $filename);
            } catch (\Exception $e) {
                \Log::error('Excel download error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

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
                    'position_id' => $request->get('position_id'),
                    'shift_id' => $request->get('shift_id'),
                    'user_name' => $request->get('user_name'),
                    'date_filter' => $request->get('date_filter', 'this_week'),
                    'start_date' => $request->get('start_date')
                ]
            ]);

            $divisionId = $request->get('division_id');
            $positionId = $request->get('position_id');
            $shiftId = $request->get('shift_id');
            $userName = $request->get('user_name');
            $dateFilter = $request->get('date_filter', 'this_week');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            // Process date filter and start_date with two-way synchronization (same as weeklyReport)
            $originalStartDate = $request->get('start_date');
            
            if ($originalStartDate && $originalStartDate !== '') {
                // User manually selected a week range - PRIORITY HIGH
                // Calculate Monday of the week containing the selected date
                $selectedDate = Carbon::parse($originalStartDate);
                $dayOfWeek = $selectedDate->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
                $daysToSubtract = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1; // Convert to Monday-based (0 = Monday)
                
                $startDate = $selectedDate->copy()->subDays($daysToSubtract)->format('Y-m-d');
                $endDate = $selectedDate->copy()->addDays(6 - $daysToSubtract)->format('Y-m-d');
                
                // Auto-detect if this matches any predefined filter
                $detectedFilter = $this->detectFilterFromDateRange($startDate, $endDate);
                if ($detectedFilter !== 'custom') {
                    $dateFilter = $detectedFilter;
                } else {
                    $dateFilter = 'custom';
                }
                
                \Log::info('PDF Export - Week Range Selected', [
                    'original_start_date' => $originalStartDate,
                    'selected_date_day_of_week' => $dayOfWeek,
                    'days_to_subtract' => $daysToSubtract,
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate,
                    'detected_filter' => $detectedFilter,
                    'final_date_filter' => $dateFilter
                ]);
                
            } elseif ($dateFilter && $dateFilter !== 'custom') {
                // User selected a predefined date filter - PRIORITY MEDIUM
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
                
                \Log::info('PDF Export - Date Filter Selected', [
                    'date_filter' => $dateFilter,
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate
                ]);
                
            } else {
                // Default: this week - PRIORITY LOW
                $dateRange = $this->getDateRangeFromFilter('this_week');
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
                $dateFilter = 'this_week';
                
                \Log::info('PDF Export - Default Filter', [
                    'default_filter' => 'this_week',
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate
                ]);
            }

            \Log::info('Date range calculated for PDF', [
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            $users = DB::table('users')
                ->select([
                    'users.id as user_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'user_types.ut_name',
                    'user_positions.up_name as position_name'  // Position name ditambahkan
                ])
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')  // JOIN dengan user_positions
                ->where('users.u_delete', '!=', '1')
                ->whereNotNull('users.u_nip'); // Hanya user dengan NIP

            // Apply filters
            if ($divisionId) {
                $users->where('users.ud_id', $divisionId);
                \Log::info('Division filter applied for PDF', ['division_id' => $divisionId]);
            }

            if ($positionId) {
                $users->where('users.up_id', $positionId);
                \Log::info('Position filter applied for PDF', ['position_id' => $positionId]);
            }

            if ($userName) {
                $users->where(function($q) use ($userName) {
                    $q->where('users.u_name', 'like', '%' . $userName . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $userName . '%');
                });
                \Log::info('User name filter applied for PDF', ['user_name' => $userName]);
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
                ->whereBetween('daily_schedules.ds_date', [$startDate, $endDate])
                ->get();

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
                    'shift_filtered' => $shiftId ? 'Yes' : 'No',
                    'user_name_filtered' => $userName ? 'Yes' : 'No',
                    'date_filtered' => $dateFilter
                ]
            ]);

            // Log the dates being sent to HTML generator
            \Log::info('PDF Export - Dates sent to HTML generator', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'start_date_formatted' => date('d M Y', strtotime($startDate)),
                'end_date_formatted' => date('d M Y', strtotime($endDate))
            ]);

            // Generate HTML content untuk dimasukkan ke PDF
            $htmlContent = $this->generateWeeklyScheduleHTML($users, $schedules, $startDate, $endDate);

            // Generate PDF
            $pdf = Pdf::loadHTML($htmlContent)
                      ->setPaper('a4', 'landscape'); // Use landscape for better table fit

            // Nama file dengan date range yang benar
            $filename = 'weekly-schedule-' . date('Y-m-d', strtotime($startDate)) . '-to-' . date('Y-m-d', strtotime($endDate)) . '.pdf';

            \Log::info('Public Export Weekly Schedule PDF - Using PDF Facade', [
                'filename' => $filename,
                'start_date' => $startDate,
                'end_date' => $endDate
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
            case 'next_week':
                $startDate = $today->copy()->addWeek()->startOfWeek()->format('Y-m-d');
                $endDate = $today->copy()->addWeek()->endOfWeek()->format('Y-m-d');
                break;
            case 'past_week':
                $startDate = $today->copy()->subWeek()->startOfWeek()->format('Y-m-d');
                $endDate = $today->copy()->subWeek()->endOfWeek()->format('Y-m-d');
                break;
            case 'now':
                // For NOW filter, we use current week but will apply time filter later
                $startDate = $today->copy()->startOfWeek()->format('Y-m-d');
                $endDate = $today->copy()->endOfWeek()->format('Y-m-d');
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
        // Log the parameters received by this method
        \Log::info('generateWeeklyScheduleHTML - Parameters received', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_date_formatted' => date('d M Y', strtotime($startDate)),
            'end_date_formatted' => date('d M Y', strtotime($endDate)),
            'users_count' => $users->count(),
            'schedules_count' => $schedules->count()
        ]);
        
        $html = "<html><head><title>Weekly Schedule</title>";
        $html .= "<style>
            body { font-family: Arial, sans-serif; margin: 10px; font-size: 10px; }
            h1 { color: #333; text-align: center; font-size: 16px; margin: 10px 0; }
            .date-range { text-align: center; margin-bottom: 15px; color: #666; font-size: 11px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; font-size: 9px; }
            th, td { border: 1px solid #ddd; padding: 4px; text-align: center; font-size: 9px; }
            th { background-color: #f5f5f5; font-weight: bold; }
            .user-info { background-color: #f9f9f9; }
            .shift-cell { min-width: 60px; max-width: 80px; }
            
            /* Shift Code Colors - Following View Colors */
            .shift-S { background-color: #f8e8e6; color: #333; } /* Sakit - Light Red */
            .shift-NA { background-color: #f8e8e6; color: #333; } /* N/A - Light Red */
            .shift-LPH { background-color: #f8e8e6; color: #333; } /* Libur - Light Red */
            .shift-LL { background-color: #f8e8e6; color: #333; } /* Libur Cuti - Light Red */
            .shift-L { background-color: #f8e8e6; color: #333; } /* Libur - Light Red */
            .shift-I { background-color: #f8e8e6; color: #333; } /* Izin - Light Red */
            
            /* Shift 1 & Shift 0 */
            .shift-FS1 { background-color: #eaf5fb; color: #333; } /* Full Shift 1 - Light Blue */
            .shift-FS0 { background-color: #eaf5fb; color: #333; } /* Full Shift 0 - Light Blue */
            
            /* Shift 2 */
            .shift-FS2 { background-color: #e5f6f3; color: #333; } /* Full Shift 2 - Light Green */
            
            /* Full & Full Shift 0 */
            .shift-FSE { background-color: #FFF9ED; color: #333; } /* Full Shift - Light Yellow */
            .shift-FM2 { background-color: #FFF9ED; color: #333; } /* Full Morning 2 - Light Yellow */
            .shift-FM1 { background-color: #FFF9ED; color: #333; } /* Full Morning 1 - Light Yellow */
            .shift-PFM { background-color: #FFF9ED; color: #333; } /* Part Full Morning - Light Yellow */
            .shift-PF0 { background-color: #FFF9ED; color: #333; } /* Part Full 0 - Light Yellow */
            .shift-PF { background-color: #FFF9ED; color: #333; } /* Part Full - Light Yellow */
            
            /* Part Shifts */
            .shift-SM2 { background-color: #eaf5fb; color: #333; } /* Shift Morning 2 - Light Blue */
            .shift-SM1 { background-color: #eaf5fb; color: #333; } /* Shift Morning 1 - Light Blue */
            .shift-PS2 { background-color: #e5f6f3; color: #333; } /* Part Shift 2 - Light Green */
            .shift-PS1 { background-color: #e5f6f3; color: #333; } /* Part Shift 1 - Light Green */
            .shift-PS0 { background-color: #e5f6f3; color: #333; } /* Part Shift 0 - Light Green */
            .shift-PL2 { background-color: #eaf5fb; color: #333; } /* Part Libur 2 - Shift 1 (Light Blue) */
            .shift-PL1 { background-color: #eaf5fb; color: #333; } /* Part Libur 1 - Shift 1 (Light Blue) */
            
            /* Default shift cell styling */
            .shift-cell { font-weight: bold; border-radius: 3px; }
            
            /* Color Legend Styles */
            .color-legend { 
                background-color: #f8f9fa; 
                padding: 15px; 
                border-radius: 8px; 
                border: 1px solid #e1e5e9; 
                margin-bottom: 20px;
            }
            .color-legend h6 { 
                margin: 0 0 15px 0; 
                color: #6c757d; 
                font-size: 14px; 
            }
            .legend-items { 
                display: flex; 
                flex-wrap: wrap; 
                gap: 15px; 
            }
            .legend-item { 
                display: flex; 
                align-items: center; 
                background-color: white; 
                padding: 8px 12px; 
                border-radius: 6px; 
                border: 1px solid #e1e5e9; 
                box-shadow: 0 1px 2px rgba(0,0,0,0.1); 
            }
            .legend-color { 
                width: 20px; 
                height: 20px; 
                border-radius: 4px; 
                margin-right: 8px; 
                border: 1px solid #dee2e6; 
            }
            .legend-text { 
                font-size: 12px; 
                color: #333; 
            }
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
        $endDateCarbon = \Carbon\Carbon::parse($endDate);
        while ($currentDate->lte($endDateCarbon)) {
            $html .= "<th class='shift-cell'>" . $currentDate->format('D d-M') . "</th>";
            $currentDate = $currentDate->copy()->addDay();
        }
        $html .= "</tr>";
        
        // Data rows - Filter users who have schedules
        foreach ($users as $user) {
            // Check if user has any schedules in the date range
            $userSchedules = $schedules->where('user_id', $user->user_id);
            if ($userSchedules->count() == 0) {
                continue; // Skip users without schedules
            }
            
            $html .= "<tr>";
            $html .= "<td class='user-info'>" . ($user->u_nip ?? '-') . "</td>";
            $html .= "<td class='user-info'>" . ($user->u_name ?? '-') . "</td>";
            $html .= "<td class='user-info'>" . ($user->ud_name ?? '-') . "</td>";
            $html .= "<td class='user-info'>" . ($user->ut_name ?? 'FULL TIME') . "</td>";
            
            // Log user data for debugging
            \Log::info('Processing user for PDF', [
                'user_id' => $user->user_id,
                'user_name' => $user->u_name,
                'user_schedules_count' => $schedules->where('user_id', $user->user_id)->count()
            ]);
            
            // Daily shift data
            $currentDate = \Carbon\Carbon::parse($startDate);
            while ($currentDate->lte($endDateCarbon)) {
                $dateStr = $currentDate->format('Y-m-d');
                
                // Find schedule for this date using collection filter
                $dailySchedule = $schedules->filter(function($schedule) use ($user, $dateStr) {
                    return $schedule->user_id == $user->user_id && $schedule->ds_date == $dateStr;
                })->first();
                
                if ($dailySchedule && $dailySchedule->sc_code) {
                    $shiftCode = $dailySchedule->sc_code;
                    $shiftClass = 'shift-' . str_replace(['/', ' '], ['', ''], $shiftCode);
                    $html .= "<td class='shift-cell {$shiftClass}'>" . $shiftCode . "</td>";
                } else {
                    $html .= "<td class='shift-cell'>-</td>";
                }
                
                $currentDate = $currentDate->copy()->addDay();
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
            body { font-family: Arial, sans-serif; margin: 10px; font-size: 10px; }
            h1 { color: #333; text-align: center; font-size: 16px; margin: 10px 0; }
            .date-range { text-align: center; margin-bottom: 15px; color: #666; font-size: 11px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; font-size: 9px; }
            th, td { border: 1px solid #ddd; padding: 4px; text-align: center; font-size: 9px; }
            th { background-color: #f5f5f5; font-weight: bold; }
            .user-info { background-color: #f9f9f9; }
            .shift-cell { min-width: 60px; max-width: 80px; }
            .nama-column { width: 120px; }
            
            /* Shift Code Colors - Following View Colors */
            .shift-S { background-color: #f8e8e6; color: #333; } /* Sakit - Light Red */
            .shift-NA { background-color: #f8e8e6; color: #333; } /* N/A - Light Red */
            .shift-LPH { background-color: #f8e8e6; color: #333; } /* Libur - Light Red */
            .shift-LL { background-color: #f8e8e6; color: #333; } /* Libur Cuti - Light Red */
            .shift-L { background-color: #f8e8e6; color: #333; } /* Libur - Light Red */
            .shift-I { background-color: #f8e8e6; color: #333; } /* Izin - Light Red */
            
            /* Shift 1 & Shift 0 */
            .shift-FS1 { background-color: #eaf5fb; color: #333; } /* Full Shift 1 - Light Blue */
            .shift-FS0 { background-color: #eaf5fb; color: #333; } /* Full Shift 0 - Light Blue */
            
            /* Shift 2 */
            .shift-FS2 { background-color: #e5f6f3; color: #333; } /* Full Shift 2 - Light Green */
            
            /* Full & Full Shift 0 */
            .shift-FSE { background-color: #FFF9ED; color: #333; } /* Full Shift - Light Yellow */
            .shift-FM2 { background-color: #FFF9ED; color: #333; } /* Full Morning 2 - Light Yellow */
            .shift-FM1 { background-color: #FFF9ED; color: #333; } /* Full Morning 1 - Light Yellow */
            .shift-PFM { background-color: #FFF9ED; color: #333; } /* Part Full Morning - Light Yellow */
            .shift-PF0 { background-color: #FFF9ED; color: #333; } /* Part Full 0 - Light Yellow */
            .shift-PF { background-color: #FFF9ED; color: #333; } /* Part Full - Light Yellow */
            
            /* Part Shifts */
            .shift-SM2 { background-color: #eaf5fb; color: #333; } /* Shift Morning 2 - Light Blue */
            .shift-SM1 { background-color: #eaf5fb; color: #333; } /* Shift Morning 1 - Light Blue */
            .shift-PS2 { background-color: #e5f6f3; color: #333; } /* Part Shift 2 - Light Green */
            .shift-PS1 { background-color: #e5f6f3; color: #333; } /* Part Shift 1 - Light Green */
            .shift-PS0 { background-color: #e5f6f3; color: #333; } /* Part Shift 0 - Light Green */
            .shift-PL2 { background-color: #eaf5fb; color: #333; } /* Part Libur 2 - Shift 1 (Light Blue) */
            .shift-PL1 { background-color: #eaf5fb; color: #333; } /* Part Libur 1 - Shift 1 (Light Blue) */
            
            /* Default shift cell styling */
            .shift-cell { font-weight: bold; border-radius: 3px; }
            
            /* Color Legend Styles */
            .color-legend { 
                background-color: #f8f9fa; 
                padding: 15px; 
                border-radius: 8px; 
                border: 1px solid #e1e5e9; 
                margin-bottom: 20px;
            }
            .color-legend h6 { 
                margin: 0 0 15px 0; 
                color: #6c757d; 
                font-size: 14px; 
            }
            .legend-items { 
                display: flex; 
                flex-wrap: wrap; 
                gap: 15px; 
            }
            .legend-item { 
                display: flex; 
                align-items: center; 
                background-color: white; 
                padding: 8px 12px; 
                border-radius: 6px; 
                border: 1px solid #e1e5e9; 
                box-shadow: 0 1px 2px rgba(0,0,0,0.1); 
            }
            .legend-color { 
                width: 20px; 
                height: 20px; 
                border-radius: 4px; 
                margin-right: 8px; 
                border: 1px solid #dee2e6; 
            }
            .legend-text { 
                font-size: 12px; 
                color: #333; 
            }
        </style></head><body>";
        
        $html .= "<h1>Weekly Schedule Report</h1>";
        $html .= "<div class='date-range'>Date Range: " . date('d M Y', strtotime($startDate)) . " to " . date('d M Y', strtotime($endDate)) . "</div>";
        
        $html .= "<table>";
        
        // First header row - Date headers with colspan=2
        $html .= "<tr>";
        $html .= "<th rowspan='2' class='nama-column'>NAMA</th>";
        
        // Daily headers with colspan=2 (more compact)
        $currentDate = \Carbon\Carbon::parse($startDate);
        while ($currentDate->lte(\Carbon\Carbon::parse($endDate))) {
            $html .= "<th colspan='2' class='shift-cell'>";
            $html .= "<div style='font-weight: bold; font-size: 10px;'>" . $currentDate->format('l') . "</div>";
            $html .= "<div style='font-size: 9px;'>" . $currentDate->format('d M') . "</div>";
            $html .= "</th>";
            $currentDate->addDay();
        }
        $html .= "</tr>";
        
        // Second header row - Shift Name and Start Shift (more compact)
        $html .= "<tr>";
        $currentDate = \Carbon\Carbon::parse($startDate);
        while ($currentDate->lte(\Carbon\Carbon::parse($endDate))) {
            $html .= "<th><small style='font-size: 8px;'>Shift Name</small></th>";
            $html .= "<th><small style='font-size: 8px;'>Start Shift</small></th>";
            $currentDate->addDay();
        }
        $html .= "</tr>";
        
        // Data rows - Filter users who have schedules
        foreach ($users as $user) {
            // Check if user has any schedules in the date range
            $userSchedules = $schedules->where('user_id', $user->user_id);
            if ($userSchedules->count() == 0) {
                continue; // Skip users without schedules
            }
            
            $html .= "<tr>";
            
            // NAMA column with NIP below (more compact)
            $html .= "<td class='user-info nama-column'>";
            $html .= "<div style='font-weight: bold; font-size: 10px;'>" . ($user->u_name ?? '-') . "</div>";
            $html .= "<small style='color: #666; font-size: 8px;'>" . ($user->u_nip ?? '-') . "</small>";
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
                        $shiftClass = 'shift-' . str_replace(['/', ' '], ['', ''], $shiftCode);
                        $html .= "<td class='shift-cell shift-name {$shiftClass}'>" . $shiftName . " (" . $shiftCode . ")" . "</td>";
                    } else {
                        $shiftClass = $shiftCode ? 'shift-' . str_replace(['/', ' '], ['', ''], $shiftCode) : '';
                        $html .= "<td class='shift-cell shift-name {$shiftClass}'>" . ($shiftCode ?: '-') . "</td>";
                    }
                    
                    // Start Shift Column - Same color as Shift Name, and handle Libur case
                    $startTime = $dailySchedule->sc_start_time ?? '';
                    $endTime = $dailySchedule->sc_end_time ?? '';
                    
                    // Check if it's a holiday/leave shift
                    $isHoliday = false;
                    if ($shiftName && (strpos(strtolower($shiftName), 'libur') !== false || 
                                     strpos(strtolower($shiftName), 'sakit') !== false || 
                                     strpos(strtolower($shiftName), 'izin') !== false)) {
                        $isHoliday = true;
                    }
                    
                    if ($isHoliday) {
                        // For holiday shifts, show "-" with same color as shift name
                        $html .= "<td class='shift-cell start-shift {$shiftClass}'>-</td>";
                    } elseif ($startTime && $endTime) {
                        // For regular shifts, show time with same color as shift name
                        $html .= "<td class='shift-cell start-shift {$shiftClass}'>" . date('H:i', strtotime($startTime)) . " - " . date('H:i', strtotime($endTime)) . "</td>";
                    } elseif ($startTime) {
                        // For shifts with only start time
                        $html .= "<td class='shift-cell start-shift {$shiftClass}'>" . date('H:i', strtotime($startTime)) . "</td>";
                    } else {
                        // For shifts without time
                        $html .= "<td class='shift-cell start-shift {$shiftClass}'>-</td>";
                    }
                } else {
                    $html .= "<td class='shift-cell'>-</td>";
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

            $divisionId = $request->get('division_id');
            $positionId = $request->get('position_id');
            $shiftId = $request->get('shift_id');
            $userName = $request->get('user_name');
            $dateFilter = $request->get('date_filter', 'this_week');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            // Process date filter and start_date with two-way synchronization (same as exportWeeklyPDFPublic)
            $originalStartDate = $request->get('start_date');
            
            if ($originalStartDate && $originalStartDate !== '') {
                // User manually selected a week range - PRIORITY HIGH
                // Calculate Monday of the week containing the selected date
                $selectedDate = Carbon::parse($originalStartDate);
                $dayOfWeek = $selectedDate->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
                $daysToSubtract = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1; // Convert to Monday-based (0 = Monday)
                
                $startDate = $selectedDate->copy()->subDays($daysToSubtract)->format('Y-m-d');
                $endDate = $selectedDate->copy()->addDays(6 - $daysToSubtract)->format('Y-m-d');
                
                // Auto-detect if this matches any predefined filter
                $detectedFilter = $this->detectFilterFromDateRange($startDate, $endDate);
                if ($detectedFilter !== 'custom') {
                    $dateFilter = $detectedFilter;
                } else {
                    $dateFilter = 'custom';
                }
                
                \Log::info('PDF Report Export - Week Range Selected', [
                    'original_start_date' => $originalStartDate,
                    'selected_date_day_of_week' => $dayOfWeek,
                    'days_to_subtract' => $daysToSubtract,
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate,
                    'detected_filter' => $detectedFilter,
                    'final_date_filter' => $dateFilter
                ]);
                
            } elseif ($dateFilter && $dateFilter !== 'custom') {
                // User selected a predefined date filter - PRIORITY MEDIUM
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
                
                \Log::info('PDF Report Export - Date Filter Selected', [
                    'date_filter' => $dateFilter,
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate
                ]);
                
            } else {
                // Default: this week - PRIORITY LOW
                $dateRange = $this->getDateRangeFromFilter('this_week');
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
                $dateFilter = 'this_week';
                
                \Log::info('PDF Report Export - Default Filter', [
                    'default_filter' => 'this_week',
                    'calculated_start_date' => $startDate,
                    'calculated_end_date' => $endDate
                ]);
            }

            \Log::info('Date range calculated for PDF report', [
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'start_date_formatted' => date('d M Y', strtotime($startDate)),
                'end_date_formatted' => date('d M Y', strtotime($endDate))
            ]);

            // Build query to get users with their schedules
            $users = DB::table('users')
                ->select([
                    'users.id as user_id',
                    'users.u_nip',
                    'users.u_name',
                    'user_divisions.ud_name',
                    'user_types.ut_name',
                    'user_positions.up_name as position_name'  // Position name ditambahkan
                ])
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')  // JOIN dengan user_positions
                ->where('users.u_delete', '!=', '1')
                ->whereNotNull('users.u_nip');
                

            // Apply filters
            if ($divisionId) {
                $users->where('users.ud_id', $divisionId);
                \Log::info('Division filter applied for PDF report', ['division_id' => $divisionId]);
            }

            if ($positionId) {
                $users->where('users.up_id', $positionId);
                \Log::info('Position filter applied for PDF report', ['position_id' => $positionId]);
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

            // Log the dates being sent to HTML generator
            \Log::info('PDF Report Export - Dates sent to HTML generator', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'start_date_formatted' => date('d M Y', strtotime($startDate)),
                'end_date_formatted' => date('d M Y', strtotime($endDate))
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
    
    /**
     * Helper method to detect date filter based on date range
     */
    private function detectFilterFromDateRange($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $today = Carbon::now();
        
        // Get Monday of current week
        $mondayThisWeek = $today->copy()->startOfWeek();
        
        // Get Monday of next week
        $mondayNextWeek = $today->copy()->addWeek()->startOfWeek();
        
        // Get Monday of previous week
        $mondayLastWeek = $today->copy()->subWeek()->startOfWeek();
        
        // Compare date ranges
        if ($start->format('Y-m-d') === $mondayThisWeek->format('Y-m-d') && 
            $end->format('Y-m-d') === $mondayThisWeek->copy()->endOfWeek()->format('Y-m-d')) {
            return 'this_week';
        } elseif ($start->format('Y-m-d') === $mondayNextWeek->format('Y-m-d') && 
                   $end->format('Y-m-d') === $mondayNextWeek->copy()->endOfWeek()->format('Y-m-d')) {
            return 'next_week';
        } elseif ($start->format('Y-m-d') === $mondayLastWeek->format('Y-m-d') && 
                   $end->format('Y-m-d') === $mondayLastWeek->copy()->endOfWeek()->format('Y-m-d')) {
            return 'past_week';
        } else {
            return 'custom';
        }
    }

    /**
     * Import weekly schedules from Excel file
     */
    public function importWeeklyExcel(Request $request)
    {
        $this->validateAccess();
        
        \Log::info('Weekly Schedule Excel Import started', [
            'request_data' => $request->all(),
            'files' => $request->allFiles()
        ]);
        
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            'start_date' => 'required|date'
        ]);

        try {
            $file = $request->file('excel_file');
            $startDate = $request->get('start_date');
            
            // Calculate end date (7 days from start date)
            $endDate = Carbon::parse($startDate)->addDays(6)->format('Y-m-d');
            
            \Log::info('Import parameters', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'file_name' => $file->getClientOriginalName()
            ]);
            
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(storage_path('app/public/uploads'), $fileName);
            
            // Process Excel file
            $extension = $file->getClientOriginalExtension();
            $data = [];
            
            if (in_array($extension, ['xlsx', 'xls'])) {
                $data = Excel::toArray([], storage_path('app/public/uploads/' . $fileName))[0];
            } else {
                // Handle CSV
                $handle = fopen(storage_path('app/public/uploads/' . $fileName), 'r');
                while (($row = fgetcsv($handle)) !== false) {
                    $data[] = $row;
                }
                fclose($handle);
            }
            
            \Log::info('Excel file processed', ['rows_count' => count($data)]);
            
            // Remove header row
            array_shift($data);
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            $processedUsers = [];
            
            // Get all active shift codes
            $shiftCodes = DB::table('shift_codes')
                ->where('sc_status', 'active')
                ->get()
                ->keyBy('sc_code');
            
            // Get all active user types
            $userTypes = DB::table('user_types')
                ->where('ut_status', 'active')
                ->get()
                ->keyBy('ut_name');
            
            // Process each row
            foreach ($data as $index => $row) {
                try {
                    if (count($row) < 10) {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": Jumlah kolom tidak cukup (harus 10 kolom, hanya ada " . count($row) . " kolom)";
                        continue;
                    }
                    
                    $nip = trim($row[0]);
                    $nama = trim($row[1]);
                    $userType = trim($row[2]);
                    $senin = trim($row[3]);
                    $selasa = trim($row[4]);
                    $rabu = trim($row[5]);
                    $kamis = trim($row[6]);
                    $jumat = trim($row[7]);
                    $sabtu = trim($row[8]);
                    $minggu = trim($row[9]);
                    
                    \Log::info('Processing row', [
                        'row_index' => $index + 2,
                        'nip' => $nip,
                        'nama' => $nama,
                        'user_type' => $userType
                    ]);
                    
                    // Validate NIP
                    if (empty($nip)) {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": NIP wajib diisi";
                        continue;
                    }
                    
                    // Find user by NIP
                    $userQuery = DB::table('users')
                        ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                        ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                        ->select('users.*', 'user_divisions.ud_name', 'user_types.ut_name')
                        ->where('users.u_nip', $nip)
                        ->where('users.u_delete', '0')
                        ->whereNotNull('users.u_nip');
                    
                    $user = $userQuery->first();
                    
                    if (!$user) {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": Staff dengan NIP {$nip} tidak ditemukan dalam sistem";
                        continue;
                    }
                    
                    // Validate user type - make it more flexible
                    if (!empty($userType)) {
                        // Check if the user type from Excel matches the database
                        if ($user->ut_name !== $userType) {
                            // Try to find a similar user type or suggest alternatives
                            $suggestedUserTypes = $userTypes->pluck('ut_name')->toArray();
                            $errorCount++;
                            $errors[] = "Baris " . ($index + 2) . ": Jenis karyawan tidak sesuai - Excel: '{$userType}', Database: '{$user->ut_name}'. Jenis yang tersedia: " . implode(', ', $suggestedUserTypes);
                            continue;
                        }
                    } else {
                        // If user type is empty in Excel, use the one from database
                        $userType = $user->ut_name;
                        \Log::info('Auto-detected user type from database', [
                            'row_index' => $index + 2,
                            'nip' => $nip,
                            'auto_detected_type' => $userType
                        ]);
                    }
                    
                    // Process daily schedules
                    $dailySchedules = [
                        ['date' => $startDate, 'shift_code' => $senin],
                        ['date' => Carbon::parse($startDate)->addDay()->format('Y-m-d'), 'shift_code' => $selasa],
                        ['date' => Carbon::parse($startDate)->addDays(2)->format('Y-m-d'), 'shift_code' => $rabu],
                        ['date' => Carbon::parse($startDate)->addDays(3)->format('Y-m-d'), 'shift_code' => $kamis],
                        ['date' => Carbon::parse($startDate)->addDays(4)->format('Y-m-d'), 'shift_code' => $jumat],
                        ['date' => Carbon::parse($startDate)->addDays(5)->format('Y-m-d'), 'shift_code' => $sabtu],
                        ['date' => Carbon::parse($startDate)->addDays(6)->format('Y-m-d'), 'shift_code' => $minggu]
                    ];
                    
                    foreach ($dailySchedules as $schedule) {
                        $date = $schedule['date'];
                        $shiftCode = $schedule['shift_code'];
                        
                        if (empty($shiftCode) || $shiftCode === '-') {
                            continue; // Skip empty schedules
                        }
                        
                        // Validate shift code
                        if (!$shiftCodes->has($shiftCode)) {
                            $errorCount++;
                            $errors[] = "Baris " . ($index + 2) . " ({$date}): Kode shift '{$shiftCode}' tidak dikenali dalam sistem";
                            continue;
                        }
                        
                        $shiftCodeData = $shiftCodes->get($shiftCode);
                        
                        // Check if shift code is compatible with user type using new compatibility method
                        $shiftCodeModel = \App\Models\ShiftCode::find($shiftCodeData->id);
                        if ($shiftCodeModel && !$shiftCodeModel->isCompatibleWithUserTypeLegacy($user->ut_name)) {
                            $errorCount++;
                            $errors[] = "Baris " . ($index + 2) . " ({$date}): Kode shift '{$shiftCode}' tidak kompatibel dengan jenis karyawan '{$user->ut_name}'";
                            continue;
                        }
                        
                        // Check if schedule already exists
                        $existingSchedule = DB::table('daily_schedules')
                            ->where('user_id', $user->id)
                            ->where('ds_date', $date)
                            ->first();
                        
                        if ($existingSchedule) {
                            // Update existing schedule
                            DB::table('daily_schedules')
                                ->where('id', $existingSchedule->id)
                                ->update([
                                    'sc_id' => $shiftCodeData->id,
                                    'ds_start_time' => $shiftCodeData->sc_start_time,
                                    'ds_end_time' => $shiftCodeData->sc_end_time,
                                    'ds_status' => 'scheduled',  // ✅ Update status ke 'scheduled'
                                    'ud_id' => $user->ud_id,     // ✅ Update division ID
                                    'updated_at' => now(),
                                    'updated_by' => Auth::user()->id
                                ]);
                        } else {
                            // Create new schedule
                            DB::table('daily_schedules')->insert([
                                'user_id' => $user->id,
                                'ud_id' => $user->ud_id,         // ✅ Tambah division ID dari user
                                'sc_id' => $shiftCodeData->id,
                                'ds_date' => $date,
                                'ds_start_time' => $shiftCodeData->sc_start_time,
                                'ds_end_time' => $shiftCodeData->sc_end_time,
                                'ds_status' => 'scheduled',      // ✅ Status yang benar
                                'created_at' => now(),
                                'created_by' => Auth::user()->id,
                                'updated_at' => now(),
                                'updated_by' => Auth::user()->id
                            ]);
                        }
                    }
                    
                    $successCount++;
                    $processedUsers[] = $user->u_name;
                    
                    \Log::info('Row processed successfully', [
                        'row_index' => $index + 2,
                        'user_id' => $user->id,
                        'nip' => $nip,
                        'name' => $user->u_name
                    ]);
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Baris " . ($index + 2) . ": Terjadi kesalahan - " . $e->getMessage();
                    \Log::error('Row processing error', [
                        'row_index' => $index + 2,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            // Clean up uploaded file
            if (file_exists(storage_path('app/public/uploads/' . $fileName))) {
                unlink(storage_path('app/public/uploads/' . $fileName));
            }
            
            \Log::info('Weekly Schedule Excel Import completed', [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'processed_users' => $processedUsers
            ]);
            
            if ($errorCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Import selesai dengan {$errorCount} kesalahan. {$successCount} staff berhasil diproses.",
                    'errors' => $errors,
                    'success_count' => $successCount,
                    'error_count' => $errorCount
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimport jadwal mingguan untuk {$successCount} staff dari tanggal {$startDate} sampai {$endDate}",
                'success_count' => $successCount,
                'processed_users' => $processedUsers
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Weekly Schedule Excel Import Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat import Excel: ' . $e->getMessage()
            ], 500);
        }
    }
}
