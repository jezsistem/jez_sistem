<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BreakTime;
use App\Models\DailySchedule;
use App\Models\User;
use App\Models\UserDivision;
use App\Models\WebConfig;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BreakTimeExport;
use Barryvdh\DomPDF\Facade\Pdf;

class BreakTimeController extends Controller
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

    protected function getUserData()
    {
        return DB::table('users')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->select('users.*', 'user_divisions.ud_name')
            ->where('users.id', auth()->user()->id)
            ->first();
    }

    public function index(Request $request)
    {
        // Temporarily comment out for testing
        // $this->validateAccess();
        
        $title = 'Break Times';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();

        $breakTime = new BreakTime();
        $breakTimes = $breakTime->getBreakTimesByDateRange(
            $request->get('start_date', date('Y-m-d')),
            $request->get('end_date', date('Y-m-d')),
            $request->get('user_id'),
            $request->get('division_id'),
            $request->get('status')
        );

        $users = DB::table('users')->where('u_delete', '!=', '1')->get();
        $divisions = DB::table('user_divisions')->where('ud_status', 'active')->get();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title ?? 'Break Times',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.break_time.index', compact('data'));
    }

    public function report(Request $request)
    {
        // Temporarily comment out for testing
        // $this->validateAccess();
        
        $title = 'Break Time Report';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();

        $breakTime = new BreakTime();
        $breakTimes = $breakTime->getBreakTimesByDateRange(
            $request->get('start_date', date('Y-m-d')),
            $request->get('end_date', date('Y-m-d')),
            $request->get('user_id'),
            $request->get('division_id'),
            $request->get('status')
        );

        // Get stats for the report
        $stats = DB::table('break_times')
            ->select('bt_status', DB::raw('count(*) as total'))
            ->where('bt_date', '>=', $request->get('start_date', date('Y-m-d')))
            ->where('bt_date', '<=', $request->get('end_date', date('Y-m-d')))
            ->groupBy('bt_status')
            ->get();

        $users = DB::table('users')->where('u_delete', '!=', '1')->get();
        $divisions = DB::table('user_divisions')->where('ud_status', 'active')->get();

        $data = [
            'title' => $title,
            'subtitle' => 'Break Time Report',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.break_time.report', compact('breakTimes', 'users', 'divisions', 'data', 'stats'));
    }

    public function getDatatables(Request $request)
    {
        \Log::info('BreakTimeController getDatatables called', [
            'ajax' => request()->ajax(),
            'authenticated' => Auth::check(),
            'user' => Auth::check() ? Auth::user()->id : 'not authenticated',
            'session_id' => session()->getId()
        ]);
        
        if(request()->ajax()) {
            $query = DB::table('break_times')
                ->select([
                    'break_times.*',
                    'users.u_name',
                    'users.u_nip',
                    'user_divisions.ud_name'
                ])
                ->leftJoin('users', 'users.id', '=', 'break_times.user_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id');

            // Apply filters
            if ($request->filled('start_date')) {
                $query->where('break_times.bt_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->where('break_times.bt_date', '<=', $request->end_date);
            }
            if ($request->filled('user_id')) {
                $query->where('break_times.user_id', $request->user_id);
            }
            if ($request->filled('division_id')) {
                $query->where('users.ud_id', $request->division_id);
            }
            if ($request->filled('status')) {
                $query->where('break_times.bt_status', $request->status);
            }
            
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('users.u_name', 'like', '%' . $search . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $search . '%');
                });
            }

            $result = datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<div class="dropdown">';
                    $btn .= '<a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">';
                    $btn .= 'Actions';
                    $btn .= '<i class="ki-duotone ki-down fs-5 ms-1"></i>';
                    $btn .= '</a>';
                    $btn .= '<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">';
                    $btn .= '<div class="menu-item px-3">';
                    $btn .= '<a href="'.route('break-times.show', $row->id).'" class="menu-link px-3">View</a>';
                    $btn .= '</div>';
                    $btn .= '<div class="menu-item px-3">';
                    $btn .= '<a href="'.route('break-times.edit', $row->id).'" class="menu-link px-3">Edit</a>';
                    $btn .= '</div>';
                    $btn .= '<div class="menu-item px-3">';
                    $btn .= '<a href="#" class="menu-link px-3 text-danger" onclick="deleteBreakTime('.$row->id.')">Delete</a>';
                    $btn .= '</div>';
                    $btn .= '</div>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->editColumn('bt_date', function($row) {
                    return date('d/m/Y', strtotime($row->bt_date));
                })
                ->editColumn('bt_start_time', function($row) {
                    return $row->bt_start_time ? date('H:i', strtotime($row->bt_start_time)) : '-';
                })
                ->editColumn('bt_end_time', function($row) {
                    return $row->bt_end_time ? date('H:i', strtotime($row->bt_end_time)) : '-';
                })
                ->editColumn('bt_duration_minutes', function($row) {
                    if ($row->bt_duration_minutes) {
                        $hours = floor($row->bt_duration_minutes / 60);
                        $minutes = $row->bt_duration_minutes % 60;
                        return sprintf('%02d:%02d', $hours, $minutes);
                    }
                    return '-';
                })
                ->editColumn('bt_type', function($row) {
                    $typeClass = $row->bt_type === 'break_1' ? 'badge badge-primary' : 'badge badge-info';
                    $typeText = $row->bt_type === 'break_1' ? 'Break 1' : 'Break 2';
                    return '<span class="' . $typeClass . '">' . $typeText . '</span>';
                })
                ->editColumn('bt_status', function($row) {
                    $statusClass = '';
                    $statusText = '';
                    
                    switch($row->bt_status) {
                        case 'active':
                            $statusClass = 'badge badge-warning';
                            $statusText = 'Active';
                            break;
                        case 'completed':
                            $statusClass = 'badge badge-success';
                            $statusText = 'Completed';
                            break;
                        case 'cancelled':
                            $statusClass = 'badge badge-danger';
                            $statusText = 'Cancelled';
                            break;
                        default:
                            $statusClass = 'badge badge-secondary';
                            $statusText = ucfirst($row->bt_status);
                    }
                    
                    return '<span class="' . $statusClass . '">' . $statusText . '</span>';
                })
                ->rawColumns(['action', 'bt_type', 'bt_status'])
                ->make(true);
                
            \Log::info('BreakTimeController getDatatables response', [
                'data_count' => count($result->getData()->data ?? [])
            ]);
            
            return $result;
        }
    }

    // API for clock in/out
    public function clockIn(Request $request)
    {
        $userId = Auth::user()->id;
        $breakType = $request->get('break_type', 'break_1');

        $breakTime = new BreakTime();
        $result = $breakTime->startBreak($userId, $breakType);

        if ($result) {
            // Get break duration from allowance
            $today = date('Y-m-d');
            $dailySchedule = \App\Models\DailySchedule::where('user_id', $userId)
                ->where('ds_date', $today)
                ->with('shiftCode')
                ->first();
            
            $breakDuration = 30; // default
            if ($dailySchedule && $dailySchedule->shiftCode) {
                $allowance = $breakTime->getBreakAllowance($dailySchedule->shiftCode->sc_type);
                $breakDuration = $allowance[$breakType]['duration'] ?? 30;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Break started successfully',
                'break_id' => $result,
                'start_time' => date('H:i:s'),
                'duration_minutes' => $breakDuration
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cannot start break. Please check your schedule or existing breaks.'
            ], 400);
        }
    }

    public function clockOut(Request $request)
    {
        $userId = Auth::user()->id;
        $breakType = $request->get('break_type', 'break_1');

        $breakTime = new BreakTime();
        $result = $breakTime->endBreak($userId, $breakType);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Break ended successfully',
                'end_time' => date('H:i:s')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cannot end break. No active break found.'
            ], 400);
        }
    }

    public function getCurrentBreak()
    {
        // Try to get user from session or request (similar to getBreakAllowance)
        $userId = null;
        $userNip = null;
        
        if (Auth::check()) {
            $userId = Auth::user()->id;
            $userNip = Auth::user()->u_nip;
        } else {
            // Try to get user from session or request parameters
            $userNip = request('user_nip', '25040202'); // Default to test user
            $user = DB::table('users')->where('u_nip', $userNip)->first();
            if ($user) {
                $userId = $user->id;
            }
        }

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        \Log::info('getCurrentBreak called', [
            'user_id' => $userId,
            'user_nip' => $userNip,
            'authenticated' => Auth::check()
        ]);

        $breakTime = new BreakTime();
        $activeBreak = $breakTime->getCurrentUserActiveBreak($userId);

        if ($activeBreak) {
            $startTime = Carbon::parse($activeBreak->bt_start_time);
            $elapsedMinutes = $startTime->diffInMinutes(Carbon::now());
            
            // Get break duration from allowance
            $today = date('Y-m-d');
            $dailySchedule = \App\Models\DailySchedule::where('user_id', $userId)
                ->where('ds_date', $today)
                ->with('shiftCode')
                ->first();
            
            $breakDuration = 30; // default
            if ($dailySchedule && $dailySchedule->shiftCode) {
                $allowance = $breakTime->getBreakAllowance($dailySchedule->shiftCode->sc_type);
                $breakDuration = $allowance[$activeBreak->bt_type]['duration'] ?? 30;
            }

            return response()->json([
                'success' => true,
                'break' => $activeBreak,
                'duration_minutes' => $elapsedMinutes,
                'total_duration_minutes' => $breakDuration,
                'remaining_minutes' => max(0, $breakDuration - $elapsedMinutes),
                'duration_formatted' => sprintf('%02d:%02d', floor($elapsedMinutes / 60), $elapsedMinutes % 60)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No active break found'
            ]);
        }
    }

    public function getBreakAllowance()
    {
        // Try to get user from session or request
        $userId = null;
        $userNip = null;
        
        if (Auth::check()) {
            $userId = Auth::user()->id;
            $userNip = Auth::user()->u_nip;
        } else {
            // Try to get user from session or request parameters
            $userNip = request('user_nip', '25040202'); // Default to test user
            $user = DB::table('users')->where('u_nip', $userNip)->first();
            if ($user) {
                $userId = $user->id;
            }
        }

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $today = date('Y-m-d');

        \Log::info('Break allowance request', [
            'user_id' => $userId,
            'user_nip' => $userNip,
            'today' => $today,
            'authenticated' => Auth::check()
        ]);

        // Get user's daily schedule
        $dailySchedule = DB::table('daily_schedules')
            ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
            ->select('daily_schedules.*', 'shift_codes.sc_type', 'shift_codes.sc_code')
            ->where('daily_schedules.user_id', $userId)
            ->where('daily_schedules.ds_date', $today)
            ->first();

        \Log::info('Daily schedule found', [
            'schedule' => $dailySchedule,
            'shift_type' => $dailySchedule ? $dailySchedule->sc_type : null
        ]);

        if (!$dailySchedule || !$dailySchedule->sc_type) {
            return response()->json([
                'success' => false,
                'message' => 'No schedule found for today'
            ]);
        }

        $shiftType = $dailySchedule->sc_type;

        // Define break allowance based on shift type
        $breakAllowance = 0;
        switch ($shiftType) {
            case 'Full Time':
                $breakAllowance = 1; // 1 break for full time (60 minutes)
                break;
            case 'Part Full':
                $breakAllowance = 2; // 2 breaks for part full (30 minutes each)
                break;
            case 'Part Time':
                $breakAllowance = 1; // 1 break for part time (30 minutes)
                break;
            case 'ALL':
                $breakAllowance = 1; // 1 break for ALL type
                break;
            default:
                $breakAllowance = 1; // default 1 break
        }
        
        // Count completed breaks today
        $completedBreaks = DB::table('break_times')
            ->where('user_id', $userId)
            ->where('bt_date', $today)
            ->where('bt_status', 'completed')
            ->count();

        \Log::info('Returning break allowance', [
            'shift_type' => $shiftType,
            'break_allowance' => $breakAllowance,
            'completed_breaks' => $completedBreaks
        ]);

        return response()->json([
            'success' => true,
            'shift_type' => $shiftType,
            'break_allowance' => $breakAllowance,
            'completed_breaks' => $completedBreaks
        ]);
    }

    public function create()
    {
        $this->validateAccess();
        
        $title = 'Break Times';
        $user_data = $this->getUserData();
        
        $users = DB::table('users')->where('u_delete', '!=', '1')->get();
        $divisions = DB::table('user_divisions')->where('ud_status', 'active')->get();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title ?? 'Break Times',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.break_time.create', compact('users', 'divisions', 'data'));
    }

    public function store(Request $request)
    {
        $this->validateAccess();
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bt_date' => 'required|date',
            'bt_start_time' => 'required',
            'bt_end_time' => 'required|after:bt_start_time',
            'bt_type' => 'required|in:break_1,break_2',
            'bt_status' => 'required|in:active,completed,cancelled',
            'bt_notes' => 'nullable|string'
        ]);

        $breakTime = DB::table('break_times')->insert([
            'user_id' => $request->user_id,
            'bt_date' => $request->bt_date,
            'bt_start_time' => $request->bt_start_time,
            'bt_end_time' => $request->bt_end_time,
            'bt_type' => $request->bt_type,
            'bt_status' => $request->bt_status,
            'bt_notes' => $request->bt_notes,
            'bt_duration_minutes' => $this->calculateDuration($request->bt_start_time, $request->bt_end_time),
            'created_by' => auth()->user()->u_name,
            'created_at' => now()
        ]);

        return redirect()->route('break-times.index')->with('success', 'Break time created successfully');
    }

    public function show($id)
    {
        $this->validateAccess();
        
        $title = 'Break Times';
        $user_data = $this->getUserData();
        
        $breakTime = DB::table('break_times')
            ->leftJoin('users', 'users.id', '=', 'break_times.user_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->select('break_times.*', 'users.u_name', 'users.u_nip', 'user_divisions.ud_name')
            ->where('break_times.id', $id)
            ->first();

        if (!$breakTime) {
            return redirect()->route('break-times.index')->with('error', 'Break time not found');
        }

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title ?? 'Break Times',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.break_time.show', compact('breakTime', 'data'));
    }

    public function edit($id)
    {
        $this->validateAccess();
        
        $title = 'Break Times';
        $user_data = $this->getUserData();
        
        $breakTime = DB::table('break_times')->where('id', $id)->first();
        
        if (!$breakTime) {
            return redirect()->route('break-times.index')->with('error', 'Break time not found');
        }

        $users = DB::table('users')->where('u_delete', '!=', '1')->get();
        $divisions = DB::table('user_divisions')->where('ud_status', 'active')->get();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title ?? 'Break Times',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.break_time.edit', compact('breakTime', 'users', 'divisions', 'data'));
    }

    public function update(Request $request, $id)
    {
        $this->validateAccess();
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bt_date' => 'required|date',
            'bt_start_time' => 'required',
            'bt_end_time' => 'required|after:bt_start_time',
            'bt_type' => 'required|in:break_1,break_2',
            'bt_status' => 'required|in:active,completed,cancelled',
            'bt_notes' => 'nullable|string'
        ]);

        $breakTime = DB::table('break_times')->where('id', $id)->first();
        
        if (!$breakTime) {
            return redirect()->route('break-times.index')->with('error', 'Break time not found');
        }

        DB::table('break_times')->where('id', $id)->update([
            'user_id' => $request->user_id,
            'bt_date' => $request->bt_date,
            'bt_start_time' => $request->bt_start_time,
            'bt_end_time' => $request->bt_end_time,
            'bt_type' => $request->bt_type,
            'bt_status' => $request->bt_status,
            'bt_notes' => $request->bt_notes,
            'bt_duration_minutes' => $this->calculateDuration($request->bt_start_time, $request->bt_end_time),
            'updated_by' => auth()->user()->u_name,
            'updated_at' => now()
        ]);

        return redirect()->route('break-times.index')->with('success', 'Break time updated successfully');
    }

    public function destroy($id)
    {
        $this->validateAccess();
        
        $breakTime = DB::table('break_times')->where('id', $id)->first();
        
        if (!$breakTime) {
            return response()->json(['success' => false, 'message' => 'Break time not found']);
        }

        DB::table('break_times')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Break time deleted successfully'        ]);
    }

    public function getCurrentBreakList(Request $request)
    {
        try {
            \Log::info('getCurrentBreakList called', [
                'authenticated' => Auth::check(),
                'user_id' => Auth::check() ? Auth::user()->id : null,
                'today' => date('Y-m-d'),
                'session_id' => session()->getId(),
                'ip' => request()->ip(),
                'division_id' => $request->get('division_id')
            ]);
            
            // This endpoint is public (no auth required) for displaying current break list
            
            $query = DB::table('break_times')
            ->select([
                'break_times.id',
                'break_times.bt_start_time',
                'break_times.bt_type',
                'users.u_name as user_name',
                'users.u_nip',
                'user_divisions.ud_name as division_name',
                'shift_codes.sc_code as shift_code',
                DB::raw('TIMESTAMPDIFF(MINUTE, ts_break_times.bt_start_time, NOW()) as duration_minutes')
            ])
            ->leftJoin('users', 'users.id', '=', 'break_times.user_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->leftJoin('daily_schedules', function($join) {
                $join->on('daily_schedules.user_id', '=', 'break_times.user_id')
                     ->where('daily_schedules.ds_date', date('Y-m-d'));
            })
            ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
            ->where('break_times.bt_status', 'active')
            ->where('break_times.bt_date', date('Y-m-d'));
            
            // Add division filter if provided
            $divisionId = $request->get('division_id');
            \Log::info('Division filter check', [
                'has_division_id' => $request->has('division_id'),
                'division_id_value' => $divisionId,
                'division_id_type' => gettype($divisionId),
                'is_empty' => empty($divisionId),
                'is_null' => is_null($divisionId),
                'is_empty_string' => $divisionId === ''
            ]);
            
            // Only apply division filter if division_id is not empty and not null
            if ($divisionId && $divisionId !== '' && $divisionId !== null) {
                $query->where('users.ud_id', $divisionId);
                \Log::info('Division filter applied', ['division_id' => $divisionId]);
            } else {
                \Log::info('No division filter applied - showing all divisions');
            }
            
            $currentBreaks = $query->orderBy('break_times.bt_start_time', 'desc')->get();

        $data = [];
        foreach ($currentBreaks as $break) {
            $data[] = [
                'id' => $break->id,
                'user_name' => $break->user_name,
                'user_nip' => $break->u_nip,
                'division_name' => $break->division_name,
                'shift_code' => $break->shift_code,
                'start_time' => date('H:i', strtotime($break->bt_start_time)),
                'duration_minutes' => $break->duration_minutes,
                'break_type' => $break->bt_type
            ];
        }

        \Log::info('getCurrentBreakList result', [
            'count' => count($data),
            'data' => $data,
            'query_sql' => $query->toSql(),
            'query_bindings' => $query->getBindings()
        ]);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
        
        } catch (\Exception $e) {
            \Log::error('getCurrentBreakList error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading current break list: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testFilter(Request $request)
    {
        $divisionId = $request->get('division_id');
        
        return response()->json([
            'success' => true,
            'debug' => [
                'has_division_id' => $request->has('division_id'),
                'division_id_value' => $divisionId,
                'division_id_type' => gettype($divisionId),
                'is_empty' => empty($divisionId),
                'is_null' => is_null($divisionId),
                'is_empty_string' => $divisionId === '',
                'request_all' => $request->all()
            ]
        ]);
    }
    
    public function debugCurrentBreakList(Request $request)
    {
        $divisionId = $request->get('division_id');
        
        // Build the same query as getCurrentBreakList
        $query = DB::table('break_times')
            ->select([
                'break_times.id',
                'break_times.bt_start_time',
                'break_times.bt_type',
                'users.u_name as user_name',
                'users.u_nip',
                'user_divisions.ud_name as division_name',
                'shift_codes.sc_code as shift_code',
                DB::raw('TIMESTAMPDIFF(MINUTE, ts_break_times.bt_start_time, NOW()) as duration_minutes')
            ])
            ->leftJoin('users', 'users.id', '=', 'break_times.user_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->leftJoin('daily_schedules', function($join) {
                $join->on('daily_schedules.user_id', '=', 'break_times.user_id')
                     ->where('daily_schedules.ds_date', date('Y-m-d'));
            })
            ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
            ->where('break_times.bt_status', 'active')
            ->where('break_times.bt_date', date('Y-m-d'));
        
        // Add division filter if provided
        if ($divisionId && $divisionId !== '' && $divisionId !== null) {
            $query->where('users.ud_id', $divisionId);
        }
        
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        $count = $query->count();
        
        return response()->json([
            'success' => true,
            'debug' => [
                'division_id' => $divisionId,
                'sql' => $sql,
                'bindings' => $bindings,
                'count' => $count,
                'filter_applied' => ($divisionId && $divisionId !== '' && $divisionId !== null)
            ]
        ]);
    }
    
    public function startBreak(Request $request)
    {
        try {
            $userNip = $request->get('user_nip');
            $breakType = $request->get('break_type', 'break_1');
            
            // Find user by NIP
            $user = DB::table('users')->where('u_nip', $userNip)->first();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found']);
            }
            
            // Check if user is already on break
            $existingBreak = DB::table('break_times')
                ->where('user_id', $user->id)
                ->where('bt_date', date('Y-m-d'))
                ->where('bt_status', 'active')
                ->first();
            
            if ($existingBreak) {
                return response()->json(['success' => false, 'message' => 'User is already on break']);
            }
            
            // Start break
            $breakTimeId = DB::table('break_times')->insertGetId([
                'user_id' => $user->id,
                'bt_date' => date('Y-m-d'),
                'bt_start_time' => now(),
                'bt_type' => $breakType,
                'bt_status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Break started successfully',
                'break_duration' => 30 // Default 30 minutes
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error starting break: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error starting break: ' . $e->getMessage()]);
        }
    }
    
    public function endBreak(Request $request)
    {
        try {
            $userNip = $request->get('user_nip');
            
            // Find user by NIP
            $user = DB::table('users')->where('u_nip', $userNip)->first();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found']);
            }
            
            // Find active break
            $activeBreak = DB::table('break_times')
                ->where('user_id', $user->id)
                ->where('bt_date', date('Y-m-d'))
                ->where('bt_status', 'active')
                ->first();
            
            if (!$activeBreak) {
                return response()->json(['success' => false, 'message' => 'No active break found']);
            }
            
            // End break
            DB::table('break_times')
                ->where('id', $activeBreak->id)
                ->update([
                    'bt_end_time' => now(),
                    'bt_status' => 'completed',
                    'updated_at' => now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Break ended successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error ending break: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error ending break: ' . $e->getMessage()]);
        }
    }
    
    private function calculateDuration($startTime, $endTime)
    {
        $start = \Carbon\Carbon::parse($startTime);
        $end = \Carbon\Carbon::parse($endTime);
        return $start->diffInMinutes($end);
    }

    // Export methods for break times report
    public function exportToExcel(Request $request)
    {
        try {
            \Log::info('BreakTimeController exportToExcel called with request:', $request->all());
            
            // Build query directly without using buildBreakTimeQuery method
            $query = DB::table('break_times')
                ->join('users', 'break_times.user_id', '=', 'users.id')
                ->join('user_divisions', 'users.ud_id', '=', 'user_divisions.id')
                ->select([
                    'break_times.*',
                    'users.u_name',
                    'users.u_nip',
                    'user_divisions.ud_name'
                ]);

            // Apply filters
            if ($request->filled('start_date')) {
                $query->where('break_times.bt_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->where('break_times.bt_date', '<=', $request->end_date);
            }
            if ($request->filled('user_id') && $request->user_id !== '') {
                $query->where('break_times.user_id', $request->user_id);
            }
            if ($request->filled('division_id') && $request->division_id !== '') {
                $query->where('users.ud_id', $request->division_id);
            }
            if ($request->filled('status') && $request->status !== '') {
                $query->where('break_times.bt_status', $request->status);
            }
            if ($request->filled('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('users.u_name', 'like', '%' . $search . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $search . '%');
                });
            }

            $breakTimes = $query->orderBy('break_times.bt_date', 'desc')->get();
            
            \Log::info('BreakTimeController exportToExcel query result:', [
                'count' => $breakTimes->count(),
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            if ($breakTimes->isEmpty()) {
                \Log::warning('No data found for Excel export');
                return response()->json(['error' => 'No data found'], 404);
            }

            // Generate filename with filters like attendance
            $filename = 'break_times_' . date('Y-m-d_H-i-s');
            if ($request->get('division_id')) {
                $division = DB::table('user_divisions')->find($request->get('division_id'));
                $filename .= '_' . ($division ? str_replace(' ', '_', $division->ud_name) : 'all');
            }
            if ($request->get('start_date') && $request->get('end_date')) {
                $filename .= '_' . $request->get('start_date') . '_to_' . $request->get('end_date');
            }
            $filename .= '.xlsx';
            
            \Log::info('Starting Excel download with filename:', ['filename' => $filename]);
            
            $response = Excel::download(new BreakTimeExport($breakTimes), $filename);
            
            \Log::info('Excel download response created successfully');
            
            return $response;
        } catch (\Exception $e) {
            \Log::error('Error exporting break times to Excel: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Request data: ' . json_encode($request->all()));
            
            // Return JSON error for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Error exporting data: ' . $e->getMessage(),
                    'details' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ], 500);
            }
            
            return back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }

    public function exportToPDF(Request $request)
    {
        try {
            \Log::info('BreakTimeController exportToPDF called with request:', $request->all());
            
            $query = $this->buildBreakTimeQuery($request);
            $breakTimes = $query->get();
            
            \Log::info('BreakTimeController exportToPDF query result:', [
                'count' => $breakTimes->count(),
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $html = $this->generateBreakTimeHTML($breakTimes, $request);
            
            \Log::info('BreakTimeController exportToPDF HTML generated:', [
                'html_length' => strlen($html),
                'html_preview' => substr($html, 0, 500)
            ]);
            
            // Generate filename
            $filename = 'break_times_report_' . date('Y-m-d_H-i-s');
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
            \Log::error('Error exporting break times to PDF: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }

    private function buildBreakTimeQuery(Request $request)
    {
        \Log::info('BreakTimeController buildBreakTimeQuery called with filters:', $request->all());
        
        $query = DB::table('break_times')
            ->join('users', 'break_times.user_id', '=', 'users.id')
            ->join('user_divisions', 'users.ud_id', '=', 'user_divisions.id')
            ->select([
                'break_times.*',
                'users.u_name',
                'users.u_nip',
                'user_divisions.ud_name'
            ]);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('break_times.bt_date', '>=', $request->start_date);
            \Log::info('Applied start_date filter:', ['start_date' => $request->start_date]);
        }
        if ($request->filled('end_date')) {
            $query->where('break_times.bt_date', '<=', $request->end_date);
            \Log::info('Applied end_date filter:', ['end_date' => $request->end_date]);
        }
        if ($request->filled('user_id') && $request->user_id !== '') {
            $query->where('break_times.user_id', $request->user_id);
            \Log::info('Applied user_id filter:', ['user_id' => $request->user_id]);
        }
        if ($request->filled('division_id') && $request->division_id !== '') {
            $query->where('users.ud_id', $request->division_id);
            \Log::info('Applied division_id filter:', ['division_id' => $request->division_id]);
        }
        if ($request->filled('status') && $request->status !== '') {
            $query->where('break_times.bt_status', $request->status);
            \Log::info('Applied status filter:', ['status' => $request->status]);
        }
        if ($request->filled('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.u_name', 'like', '%' . $search . '%')
                  ->orWhere('users.u_nip', 'like', '%' . $search . '%');
            });
            \Log::info('Applied search filter:', ['search' => $search]);
        }

        $finalQuery = $query->orderBy('break_times.bt_date', 'desc');
        
        \Log::info('BreakTimeController buildBreakTimeQuery final query:', [
            'sql' => $finalQuery->toSql(),
            'bindings' => $finalQuery->getBindings()
        ]);
        
        return $finalQuery;
    }

    private function generateBreakTimeHTML($breakTimes, $request)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Break Times Report</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h1 { margin: 0; color: #2E75B6; }
                .header p { margin: 5px 0; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #4472C4; color: white; font-weight: bold; }
                .text-center { text-align: center; }
                .status-active { background-color: #fff3cd; }
                .status-completed { background-color: #d4edda; }
                .status-cancelled { background-color: #f8d7da; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN BREAK TIME</h1>
                <p>Periode: ' . date('d/m/Y', strtotime($request->get('start_date', date('Y-m-d')))) . ' - ' . date('d/m/Y', strtotime($request->get('end_date', date('Y-m-d')))) . '</p>
                <p>Dibuat pada: ' . date('d/m/Y H:i:s') . '</p>';

        // Add filter information
        $filterInfo = [];
        if ($request->filled('user_id') && $request->user_id !== '') {
            $user = DB::table('users')->where('id', $request->user_id)->first();
            if ($user) {
                $filterInfo[] = 'Karyawan: ' . $user->u_name . ' (' . $user->u_nip . ')';
            }
        }
        if ($request->filled('division_id') && $request->division_id !== '') {
            $division = DB::table('user_divisions')->where('id', $request->division_id)->first();
            if ($division) {
                $filterInfo[] = 'Divisi: ' . $division->ud_name;
            }
        }
        if ($request->filled('status') && $request->status !== '') {
            $filterInfo[] = 'Status: ' . ucfirst($request->status);
        }
        if ($request->filled('search') && $request->search !== '') {
            $filterInfo[] = 'Pencarian: ' . $request->search;
        }
        
        if (!empty($filterInfo)) {
            $html .= '<p><strong>Filter yang Diterapkan:</strong> ' . implode(' | ', $filterInfo) . '</p>';
        }
        
        $html .= '<p><strong>Total Data:</strong> ' . $breakTimes->count() . ' record</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Karyawan</th>
                        <th>NIP</th>
                        <th>Divisi</th>
                        <th>Tipe Break</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Durasi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';

        $no = 1;
        foreach ($breakTimes as $breakTime) {
            $breakType = $breakTime->bt_type === 'break_1' ? 'Break 1' : 'Break 2';
            $startTime = $breakTime->bt_start_time ? date('H:i', strtotime($breakTime->bt_start_time)) : '-';
            $endTime = $breakTime->bt_end_time ? date('H:i', strtotime($breakTime->bt_end_time)) : '-';
            $duration = $breakTime->bt_duration_minutes ? 
                sprintf('%02d:%02d', floor($breakTime->bt_duration_minutes / 60), $breakTime->bt_duration_minutes % 60) : '-';
            
            $statusClass = 'status-' . ($breakTime->bt_status ?? 'unknown');
            $statusText = $this->getStatusTextForPDF($breakTime->bt_status);

            $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td>' . date('d/m/Y', strtotime($breakTime->bt_date)) . '</td>
                        <td>' . ($breakTime->u_name ?? '-') . '</td>
                        <td>' . ($breakTime->u_nip ?? '-') . '</td>
                        <td>' . ($breakTime->ud_name ?? '-') . '</td>
                        <td class="text-center">' . $breakType . '</td>
                        <td class="text-center">' . $startTime . '</td>
                        <td class="text-center">' . $endTime . '</td>
                        <td class="text-center">' . $duration . '</td>
                        <td class="' . $statusClass . '">' . $statusText . '</td>
                    </tr>';
            $no++;
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        \Log::info('BreakTimeController generateBreakTimeHTML completed:', [
            'total_rows' => $no - 1,
            'html_length' => strlen($html)
        ]);

        return $html;
    }

    private function getStatusTextForPDF($status)
    {
        switch($status) {
            case 'active':
                return 'Active';
            case 'completed':
                return 'Completed';
            case 'cancelled':
                return 'Cancelled';
            default:
                return ucfirst($status);
        }
    }
} 