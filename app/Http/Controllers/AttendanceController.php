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
            'segment' => request()->segment(1)
        ];

        if ($request->ajax() && $request->has('ajax_stats')) {
            return response()->json(['stats' => $stats]);
        }

        return view('app.attendance.index', compact('attendances', 'users', 'divisions', 'leaveTypes', 'stats', 'data'));
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
            ->where('users.u_delete', '!=', '1')
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
                'action' => $this->getActionButtons($row->id)
            ];
        }
        
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
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('users.u_name', 'like', '%' . $search . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $search . '%');
                });
            }

            $result = datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('user_id', function($row) {
                return $row->user_id;
            })
            ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm">';
                    $btn .= '<a href="'.route('attendance.show', $row->id).'" class="btn btn-info btn-xs" title="View"><i class="ki-outline ki-eye"></i></a>';
                    $btn .= '<a href="'.route('attendance.edit', $row->id).'" class="btn btn-warning btn-xs" title="View"><i class="ki-outline ki-notepad-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-xs" onclick="deleteAttendance('.$row->id.')" title="Delete"><i class="ki-outline ki-trash-square"></i></button>';
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
}
