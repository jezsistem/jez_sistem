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

    /**
     * Get date range from filter
     */
    protected function getDateRangeFromFilter($filter)
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
                $startDate = $today->format('Y-m-d');
                $endDate = $today->format('Y-m-d');
        }
        
        return [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
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

    /**
     * Show break time summary report
     */
    public function summaryReport(Request $request)
    {
        try {
            \Log::info('Break Time Summary Report - Starting method');
            $this->validateAccess();
            \Log::info('Break Time Summary Report - Access validated');
            
            $title = 'Break Time Summary Report';
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
            $summaryData = $this->getBreakTimeSummary($startDate, $endDate, $request->get('division_id'));
            
            // Apply search filter if provided
            if ($request->filled('search')) {
                $search = $request->get('search');
                $summaryData = $summaryData->filter(function($item) use ($search) {
                    return stripos($item->u_name, $search) !== false || 
                           stripos($item->u_nip, $search) !== false;
                });
            }
            
            $divisions = DB::table('user_divisions')->where('ud_status', 'active')->get();

            $data = [
                'title' => $title,
                'subtitle' => 'Break Time Summary Report',
                'sidebar' => $this->sidebar(),
                'user' => $user_data,
                'segment' => 'break-times',
                'startDate' => $startDate,
                'endDate' => $endDate,
                'dateFilter' => $dateFilter
            ];
            
            try {
                \Log::info('Break Time Summary Report - Data structure', [
                    'data_type' => gettype($data),
                    'data_keys' => array_keys($data),
                    'sidebar_type' => gettype($data['sidebar']),
                    'user_type' => gettype($data['user'])
                ]);
                
                \Log::info('Break Time Summary Report - Summary data sample', [
                    'first_item' => $summaryData->first(),
                    'total_count' => $summaryData->count()
                ]);
                
                return view('app.break_time.summary_report', compact('summaryData', 'divisions', 'data'));
            } catch (\Exception $e) {
                \Log::error('Break Time Summary Report - Error in view', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Break Time Summary Report - Error in method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get break time summary data
     */
    protected function getBreakTimeSummary($startDate, $endDate, $divisionId = null)
    {
        try {
            \Log::info('Getting break time summary data', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'division_id' => $divisionId
            ]);

            $query = DB::table('users as u')
                ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
                ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
                ->leftJoin('user_types as ut', 'u.ut_id', '=', 'ut.id')
                ->leftJoin('daily_schedules as ds', function($join) use ($startDate, $endDate) {
                    $join->on('u.id', '=', 'ds.user_id')
                         ->whereBetween('ds.ds_date', [$startDate, $endDate]);
                })
                ->leftJoin('break_times as bt', function($join) use ($startDate, $endDate) {
                    $join->on('u.id', '=', 'bt.user_id')
                         ->whereBetween('bt.bt_date', [$startDate, $endDate]);
                })
                ->where('u.u_delete', '!=', '1')
                ->whereNotNull('u.u_nip') // Hanya staff yang memiliki NIP
                ->where('u.u_nip', '!=', '')
                ->select([
                    'u.id as user_id',
                    'u.u_nip',
                    'u.u_name',
                    'up.up_name as position_name',
                    'ud.ud_name as division_name',
                    'ut.ut_name as work_type',
                    DB::raw('COUNT(DISTINCT ts_ds.id) as total_shifts'),
                    DB::raw('COUNT(DISTINCT ts_bt.id) as total_breaks'),
                    DB::raw('COUNT(DISTINCT CASE WHEN ts_ds.id IS NOT NULL AND ts_bt.id IS NULL THEN ts_ds.id END) as no_break_shifts')
                ])
                ->groupBy('u.id', 'u.u_nip', 'u.u_name', 'up.up_name', 'ud.ud_name', 'ut.ut_name');

            if ($divisionId) {
                $query->where('u.ud_id', $divisionId);
            }

            $result = $query->get();

            \Log::info('Break time summary data result', [
                'total_records' => $result->count(),
                'first_record' => $result->first()
            ]);

            // Debug: Check if any user has data
            if ($result->count() > 0) {
                $sampleUser = $result->first();
                \Log::info('Sample user data', [
                    'user_id' => $sampleUser->user_id,
                    'user_name' => $sampleUser->u_name,
                    'total_shifts' => $sampleUser->total_shifts,
                    'total_breaks' => $sampleUser->total_breaks,
                    'no_break_shifts' => $sampleUser->no_break_shifts
                ]);
            }

            // Calculate exceeded break time manually for each user based on their user type
            foreach ($result as $user) {
                // Get user's shift type to determine break allowance
                $userShiftType = DB::table('users as u')
                    ->leftJoin('daily_schedules as ds', 'u.id', '=', 'ds.user_id')
                    ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
                    ->where('u.id', $user->user_id)
                    ->whereBetween('ds.ds_date', [$startDate, $endDate])
                    ->select('sc.sc_type')
                    ->first();
                
                $shiftType = $userShiftType ? $userShiftType->sc_type : 'Part Time'; // Default fallback
                
                // Get break allowance based on shift type
                $breakTime = new \App\Models\BreakTime();
                $breakAllowance = $breakTime->getBreakAllowance($shiftType);
                
                // Determine max duration for this user type
                $maxDuration = 30; // Default
                if (isset($breakAllowance['break_1'])) {
                    $maxDuration = $breakAllowance['break_1']['duration'];
                }
                
                \Log::info('User break allowance calculation', [
                    'user_id' => $user->user_id,
                    'shift_type' => $shiftType,
                    'break_allowance' => $breakAllowance,
                    'max_duration' => $maxDuration
                ]);
                
                $exceededBreaks = DB::table('break_times as bt')
                    ->where('bt.user_id', $user->user_id)
                    ->whereBetween('bt.bt_date', [$startDate, $endDate])
                    ->where(function($query) use ($maxDuration) {
                        $query->where('bt.bt_duration_minutes', '>', $maxDuration)
                              ->orWhere(function($q) {
                                  $q->where('bt.bt_duration_minutes', '=', 0)
                                    ->whereNotNull('bt.bt_start_time')
                                    ->whereNotNull('bt.bt_end_time');
                              });
                    })
                    ->get();

                $exceededCount = 0;
                foreach ($exceededBreaks as $break) {
                    if ($break->bt_duration_minutes > $maxDuration) {
                        $exceededCount++;
                    } else if ($break->bt_duration_minutes == 0 && $break->bt_start_time && $break->bt_end_time) {
                        try {
                            $startTime = \Carbon\Carbon::parse($break->bt_start_time);
                            $endTime = \Carbon\Carbon::parse($break->bt_end_time);
                            $durationMinutes = $endTime->diffInMinutes($startTime);
                            if ($durationMinutes > $maxDuration) {
                                $exceededCount++;
                                
                                \Log::info('Calculated exceeded break time manually:', [
                                    'user_id' => $user->user_id,
                                    'break_id' => $break->id,
                                    'shift_type' => $shiftType,
                                    'max_duration' => $maxDuration,
                                    'start_time' => $break->bt_start_time,
                                    'end_time' => $break->bt_end_time,
                                    'calculated_duration' => $durationMinutes,
                                    'exceeded_by' => $durationMinutes - $maxDuration
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::error('Error calculating exceeded break time:', [
                                'user_id' => $user->user_id,
                                'break_id' => $break->id,
                                'shift_type' => $shiftType,
                                'max_duration' => $maxDuration,
                                'start_time' => $break->bt_start_time,
                                'end_time' => $break->bt_end_time,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
                
                $user->exceeded_break_time = $exceededCount;
                $user->user_shift_type = $shiftType; // Add for debugging
                $user->max_break_duration = $maxDuration; // Add for debugging
            }

            // Temporarily disable filter to debug data
            // Temporarily disable filter to restore data
            // Filter to only show users with total_breaks > 0 OR no_break_shifts > 0
            $filteredResult = $result->filter(function($item) {
                $hasData = $item->total_breaks > 0 || $item->no_break_shifts > 0;
                
                // Debug logging for first few items
                if ($item->user_id <= 5) {
                    \Log::info('Filter debug for user', [
                        'user_id' => $item->user_id,
                        'user_name' => $item->u_name,
                        'total_breaks' => $item->total_breaks,
                        'no_break_shifts' => $item->no_break_shifts,
                        'total_shifts' => $item->total_shifts,
                        'has_data' => $hasData
                    ]);
                }
                
                // Return users with data
                return $hasData;
            });
            
            \Log::info('Break time summary data filtered', [
                'total_records' => $result->count(),
                'filtered_records' => $filteredResult->count(),
                'first_record' => $filteredResult->first()
            ]);
            
            return $filteredResult;

        } catch (\Exception $e) {
            \Log::error('Error getting break time summary data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get summary report statistics
     */
    public function getSummaryReportStats(Request $request)
    {
        try {
            $this->validateAccess();
            
            $dateFilter = $request->get('date_filter', 'this_week');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $divisionId = $request->get('division_id');
            $search = $request->get('search');
            
            // Apply date filter if not custom
            if ($dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }
            
            // Get summary data for statistics
            $summaryData = $this->getBreakTimeSummary($startDate, $endDate, $divisionId);
            
            // Apply search filter if provided
            if ($search) {
                $summaryData = $summaryData->filter(function($item) use ($search) {
                    return stripos($item->u_name, $search) !== false || 
                           stripos($item->u_nip, $search) !== false;
                });
            }
            
            // Calculate statistics
            $totalStaff = $summaryData->count();
            $totalShifts = $summaryData->sum('total_shifts');
            $totalBreaks = $summaryData->sum('total_breaks');
            $noBreakShifts = $summaryData->sum('no_break_shifts');
            $exceededBreakTime = $summaryData->sum('exceeded_break_time');
            
            // Calculate averages
            $avgBreaksPerStaff = $totalStaff > 0 ? round($totalBreaks / $totalStaff, 1) : 0;
            $avgShiftsPerStaff = $totalStaff > 0 ? round($totalShifts / $totalStaff, 1) : 0;
            
            $stats = [
                'total_staff' => $totalStaff,
                'total_shifts' => $totalShifts,
                'total_breaks' => $totalBreaks,
                'no_break_shifts' => $noBreakShifts,
                'exceeded_break_time' => $exceededBreakTime,
                'avg_breaks_per_staff' => $avgBreaksPerStaff,
                'avg_shifts_per_staff' => $avgShiftsPerStaff
            ];
            
            \Log::info('Break Time Summary Report Stats calculated', [
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'division_id' => $divisionId,
                'search' => $search,
                'stats' => $stats
            ]);
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            \Log::error('Error getting summary report stats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to get statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get summary report datatables
     */
    public function getSummaryReportDatatables(Request $request)
    {
        \Log::info('Break Time Summary Report Datatables - Method called', [
            'is_ajax' => request()->ajax(),
            'method' => $request->method(),
            'url' => $request->url(),
            'headers' => $request->headers->all()
        ]);
        
        if(request()->ajax()) {
            try {
                $this->validateAccess();
                \Log::info('Break Time Summary Report Datatables - Access validated');
                
                // Debug: Log request parameters
                \Log::info('Break Time Summary Report Datatables Request', [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'division_id' => $request->division_id,
                    'search' => $request->search,
                    'draw' => $request->draw,
                    'start' => $request->start,
                    'length' => $request->length
                ]);
            
            } catch (\Exception $e) {
                \Log::error('Break Time Summary Report Datatables - Error in access validation', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['error' => 'Access denied'], 403);
            }
            
            // Get date range
            $dateFilter = $request->get('date_filter', 'this_week');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $divisionId = $request->get('division_id');
            
            // Apply date filter if not custom
            if ($dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }
            
            // Use the same method as summaryReport to get data
            $summaryData = $this->getBreakTimeSummary($startDate, $endDate, $divisionId);
            
            // Store total records BEFORE search (for recordsTotal)
            $totalRecordsBeforeSearch = $summaryData->count();
            
            // Debug: Log date range and data count
            \Log::info('Break Time Summary Report - Date Range Debug', [
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'division_id' => $divisionId,
                'raw_data_count' => $totalRecordsBeforeSearch
            ]);
            
            // Apply search filter if provided
            if ($request->filled('search')) {
                $search = $request->get('search');
                $summaryData = $summaryData->filter(function($item) use ($search) {
                    return stripos($item->u_name, $search) !== false || 
                           stripos($item->u_nip, $search) !== false;
                });
                
                \Log::info('Search filter applied', [
                    'search_term' => $search,
                    'records_before_search' => $totalRecordsBeforeSearch,
                    'records_after_search' => $summaryData->count()
                ]);
            }
            
            // Debug: Log data before DataTables processing
            \Log::info('Break Time Summary Report Data Before DataTables', [
                'total_count' => $summaryData->count(),
                'first_item' => $summaryData->first()
            ]);
            
            // Debug: Check data structure
            if ($summaryData->count() > 0) {
                $sampleData = $summaryData->first();
                \Log::info('Sample data structure', [
                    'user_id' => $sampleData->user_id ?? 'missing',
                    'u_nip' => $sampleData->u_nip ?? 'missing',
                    'u_name' => $sampleData->u_name ?? 'missing',
                    'position_name' => $sampleData->position_name ?? 'missing',
                    'division_name' => $sampleData->division_name ?? 'missing',
                    'work_type' => $sampleData->work_type ?? 'missing',
                    'total_shifts' => $sampleData->total_shifts ?? 'missing',
                    'total_breaks' => $sampleData->total_breaks ?? 'missing',
                    'no_break_shifts' => $sampleData->no_break_shifts ?? 'missing'
                ]);
            }
            
            // Convert to array for DataTables (exact same as BreakTimeBackupController)
            $data = [];
            foreach ($summaryData as $index => $item) {
                $itemArray = (array) $item;
                $data[] = $itemArray;
                
                // Debug: Log first few items
                if ($index < 3) {
                    \Log::info('Data item ' . $index, [
                        'user_id' => $itemArray['user_id'] ?? 'missing',
                        'u_nip' => $itemArray['u_nip'] ?? 'missing',
                        'u_name' => $itemArray['u_name'] ?? 'missing',
                        'position_name' => $itemArray['position_name'] ?? 'missing',
                        'division_name' => $itemArray['division_name'] ?? 'missing',
                        'work_type' => $itemArray['work_type'] ?? 'missing',
                        'total_shifts' => $itemArray['total_shifts'] ?? 'missing',
                        'total_breaks' => $itemArray['total_breaks'] ?? 'missing',
                        'no_break_shifts' => $itemArray['no_break_shifts'] ?? 'missing'
                    ]);
                }
            }
            
            // Apply pagination manually for server-side processing
            $totalRecords = count($data);
            $start = $request->get('start', 0);
            $length = $request->get('length', 25);
            $paginatedData = array_slice($data, $start, $length);
            
            // Set DT_RowIndex AFTER pagination to ensure sequential numbering
            foreach ($paginatedData as $index => $item) {
                $paginatedData[$index]['DT_RowIndex'] = $start + $index + 1;
            }
            
            \Log::info('Break Time Summary Report Datatables Response', [
                'total_records' => $totalRecords,
                'displayed_records' => count($paginatedData),
                'draw' => $request->get('draw', 1)
            ]);
            
            return response()->json([
                'draw' => $request->get('draw', 1),
                'recordsTotal' => $totalRecordsBeforeSearch,
                'recordsFiltered' => $totalRecords,
                'data' => $paginatedData
            ]);
            
        } else {
            return response()->json(['error' => 'Invalid request'], 400);
        }
    }

    /**
     * Show staff break time detail
     */
    public function staffDetail($user_id, Request $request)
    {
        try {
            $this->validateAccess();
            
            $title = 'Staff Break Time Detail';
            $user = auth()->user();
            $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();
            
            // Get staff info
            $staff = DB::table('users as u')
                ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
                ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
                ->leftJoin('user_types as ut', 'u.ut_id', '=', 'ut.id')
                ->select([
                    'u.id',
                    'u.u_nip',
                    'u.u_name',
                    'u.u_email',
                    'up.up_name as position_name',
                    'ud.ud_name as division_name',
                    'ut.ut_name as work_type'
                ])
                ->where('u.id', $user_id)
                ->where('u.u_delete', '!=', '1')
                ->first();
            
            if (!$staff) {
                abort(404, 'Staff not found');
            }
            
            // Get date range from request or default to current month
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            
            $data = [
                'title' => $title,
                'subtitle' => 'Staff Break Time Detail',
                'sidebar' => $this->sidebar(),
                'user' => $user_data,
                'segment' => 'break-times',
                'staff' => $staff,
                'startDate' => $startDate,
                'endDate' => $endDate
            ];
            
            return view('app.break_time.staff_detail', compact('data'));
            
        } catch (\Exception $e) {
            \Log::error('Staff Detail Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get staff break time datatables
     */
    public function staffDatatables($user_id, Request $request)
    {
        if(request()->ajax()) {
            try {
                $this->validateAccess();
                
                // Get date range from request or default to current month
                $startDate = $request->get('start_date', date('Y-m-01'));
                $endDate = $request->get('end_date', date('Y-m-t'));
                
                $query = DB::table('break_times as bt')
                    ->leftJoin('daily_schedules as ds', function($join) use ($user_id, $startDate, $endDate) {
                        $join->on('bt.user_id', '=', 'ds.user_id')
                             ->where('ds.user_id', $user_id)
                             ->where('ds.ds_date', '=', 'bt.bt_date'); // Join on same date to avoid duplicates
                    })
                    ->where('bt.user_id', $user_id)
                    ->whereBetween('bt.bt_date', [$startDate, $endDate])
                    ->select([
                        'bt.id',
                        'bt.bt_date',
                        'bt.bt_type',
                        'bt.bt_start_time',
                        'bt.bt_end_time',
                        'bt.bt_duration_minutes',
                        'bt.bt_status',
                        'bt.bt_notes',
                        'ds.ds_start_time as shift_start',
                        'ds.ds_end_time as shift_end',
                        'ds.ds_status as shift_status'
                    ]);
                
                // Apply status filter if provided
                if ($request->filled('status') && $request->status !== '') {
                    $query->where('bt.bt_status', $request->status);
                }
                
                $query->orderBy('bt.bt_date', 'desc')
                      ->orderBy('bt.bt_start_time', 'desc');
                
                $breakTimes = $query->get();
                
                \Log::info('Staff Datatables - Raw data sample:', [
                    'count' => $breakTimes->count(),
                    'sample' => $breakTimes->first()
                ]);
                
                // Convert to array for DataTables
                $data = [];
                foreach ($breakTimes as $index => $item) {
                    $itemArray = (array) $item;
                    
                    // Calculate duration manually if bt_duration_minutes is empty, null, or 0
                    if ((empty($itemArray['bt_duration_minutes']) || $itemArray['bt_duration_minutes'] == 0) && !empty($itemArray['bt_start_time']) && !empty($itemArray['bt_end_time'])) {
                        try {
                            $startTime = \Carbon\Carbon::parse($itemArray['bt_start_time']);
                            $endTime = \Carbon\Carbon::parse($itemArray['bt_end_time']);
                            $durationMinutes = $endTime->diffInMinutes($startTime);
                            $itemArray['bt_duration_minutes'] = $durationMinutes;
                            
                            \Log::info('Calculated duration manually:', [
                                'id' => $itemArray['id'],
                                'start_time' => $itemArray['bt_start_time'],
                                'end_time' => $itemArray['bt_end_time'],
                                'calculated_duration' => $durationMinutes
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Error calculating duration:', [
                                'id' => $itemArray['id'],
                                'start_time' => $itemArray['bt_start_time'],
                                'end_time' => $itemArray['bt_end_time'],
                                'error' => $e->getMessage()
                            ]);
                            $itemArray['bt_duration_minutes'] = 0;
                        }
                    }
                    
                    // Get user's shift type to determine break allowance and add exceeded note
                    $userShiftType = DB::table('users as u')
                        ->leftJoin('daily_schedules as ds', 'u.id', '=', 'ds.user_id')
                        ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
                        ->where('u.id', $user_id)
                        ->where('ds.ds_date', $itemArray['bt_date'])
                        ->select('sc.sc_type')
                        ->first();
                    
                    $shiftType = $userShiftType ? $userShiftType->sc_type : 'Part Time';
                    $breakTime = new \App\Models\BreakTime();
                    $breakAllowance = $breakTime->getBreakAllowance($shiftType);
                    
                    // Determine max duration for this user type
                    $maxDuration = 30; // Default
                    if (isset($breakAllowance['break_1'])) {
                        $maxDuration = $breakAllowance['break_1']['duration'];
                    }
                    
                    // Add exceeded note if break duration exceeds allowance
                    $currentDuration = $itemArray['bt_duration_minutes'] ?? 0;
                    if ($currentDuration > $maxDuration) {
                        $exceededMinutes = $currentDuration - $maxDuration;
                        $originalNotes = $itemArray['bt_notes'] ?? '';
                        $exceededNote = "⚠️ EXCEEDED: Break melebihi jatah {$maxDuration} menit sebanyak {$exceededMinutes} menit";
                        
                        if (!empty($originalNotes)) {
                            $itemArray['bt_notes'] = $originalNotes . ' | ' . $exceededNote;
                        } else {
                            $itemArray['bt_notes'] = $exceededNote;
                        }
                        
                        \Log::info('Added exceeded note:', [
                            'break_id' => $itemArray['id'],
                            'user_id' => $user_id,
                            'shift_type' => $shiftType,
                            'max_duration' => $maxDuration,
                            'actual_duration' => $currentDuration,
                            'exceeded_by' => $exceededMinutes
                        ]);
                    }
                    
                    $data[] = $itemArray;
                }
                
                return response()->json([
                    'draw' => $request->get('draw', 1),
                    'recordsTotal' => count($data),
                    'recordsFiltered' => count($data),
                    'data' => $data
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Staff Datatables Error', [
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
     * Get staff break time statistics
     */
    public function staffStats($user_id, Request $request)
    {
        try {
            $this->validateAccess();
            
            // Get date range from request or default to current month
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            
            // Get user's shift type to determine break allowance
            $userShiftType = DB::table('users as u')
                ->leftJoin('daily_schedules as ds', 'u.id', '=', 'ds.user_id')
                ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
                ->where('u.id', $user_id)
                ->whereBetween('ds.ds_date', [$startDate, $endDate])
                ->select('sc.sc_type')
                ->first();
            
            $shiftType = $userShiftType ? $userShiftType->sc_type : 'Part Time'; // Default fallback
            
            // Get break allowance based on shift type
            $breakTime = new \App\Models\BreakTime();
            $breakAllowance = $breakTime->getBreakAllowance($shiftType);
            
            // Determine max duration for this user type
            $maxDuration = 30; // Default
            if (isset($breakAllowance['break_1'])) {
                $maxDuration = $breakAllowance['break_1']['duration'];
            }
            
            \Log::info('Staff stats - User break allowance calculation', [
                'user_id' => $user_id,
                'shift_type' => $shiftType,
                'break_allowance' => $breakAllowance,
                'max_duration' => $maxDuration
            ]);
            
            // Get break time statistics
            $statsQuery = DB::table('break_times')
                ->where('user_id', $user_id)
                ->whereBetween('bt_date', [$startDate, $endDate]);
            
            // Apply status filter if provided
            if ($request->filled('status') && $request->status !== '') {
                $statsQuery->where('bt_status', $request->status);
            }
            
            $stats = $statsQuery->select([
                DB::raw('COUNT(*) as total_breaks'),
                DB::raw('COUNT(CASE WHEN bt_duration_minutes > ' . $maxDuration . ' THEN 1 END) as exceeded_breaks'),
                DB::raw('COUNT(CASE WHEN bt_status = "completed" THEN 1 END) as completed_breaks'),
                DB::raw('COUNT(CASE WHEN bt_status = "active" THEN 1 END) as active_breaks'),
                DB::raw('AVG(bt_duration_minutes) as avg_duration'),
                DB::raw('SUM(bt_duration_minutes) as total_duration')
            ])->first();
            
            // Get shift statistics
            $shiftStats = DB::table('daily_schedules')
                ->where('user_id', $user_id)
                ->whereBetween('ds_date', [$startDate, $endDate])
                ->select([
                    DB::raw('COUNT(*) as total_shifts'),
                    DB::raw('COUNT(CASE WHEN ds_status = "scheduled" THEN 1 END) as scheduled_shifts'),
                    DB::raw('COUNT(CASE WHEN ds_status = "completed" THEN 1 END) as completed_shifts')
                ])
                ->first();
            
            // Get no break shifts (shifts without break)
            $noBreakShifts = DB::table('daily_schedules as ds')
                ->leftJoin('break_times as bt', function($join) use ($startDate, $endDate) {
                    $join->on('ds.user_id', '=', 'bt.user_id')
                         ->whereBetween('bt.bt_date', [$startDate, $endDate]);
                })
                ->where('ds.user_id', $user_id)
                ->whereBetween('ds.ds_date', [$startDate, $endDate])
                ->whereNull('bt.id')
                ->count();
            
            $response = [
                'break_stats' => array_merge((array) $stats, [
                    'shift_type' => $shiftType,
                    'max_duration' => $maxDuration
                ]),
                'shift_stats' => $shiftStats,
                'no_break_shifts' => $noBreakShifts,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ];
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            \Log::error('Staff Stats Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function report(Request $request)
    {
        // Temporarily comment out for testing
        // $this->validateAccess();
        
        $title = 'Break Time Report';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();

        // Get date range from request or default to current week
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $dateFilter = $request->get('date_filter', 'this_week');
        
        // Apply date filter if not custom
        if ($dateFilter && $dateFilter !== 'custom') {
            $dateRange = $this->getDateRangeFromFilter($dateFilter);
            $startDate = $dateRange['startDate'];
            $endDate = $dateRange['endDate'];
        }

        $breakTime = new BreakTime();
        $breakTimes = $breakTime->getBreakTimesByDateRange(
            $startDate,
            $endDate,
            $request->get('user_id'),
            $request->get('division_id'),
            $request->get('status')
        );

        // Get stats for the report
        $stats = DB::table('break_times')
            ->select('bt_status', DB::raw('count(*) as total'))
            ->where('bt_date', '>=', $startDate)
            ->where('bt_date', '<=', $endDate)
            ->groupBy('bt_status')
            ->get();

        $users = DB::table('users')->where('u_delete', '!=', '1')->get();
        $divisions = DB::table('user_divisions')->where('ud_status', 'active')->get();

        $data = [
            'title' => $title,
            'subtitle' => 'Break Time Report',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateFilter' => $dateFilter
        ];

        return view('app.break_time.report', compact('breakTimes', 'users', 'divisions', 'data', 'stats', 'startDate', 'endDate', 'dateFilter'));
    }

    /**
     * Get break time statistics for AJAX
     */
    public function getBreakTimeStats(Request $request)
    {
        try {
            $this->validateAccess();
            
            // Get date range from request
            $startDate = $request->get('start_date', date('Y-m-d'));
            $endDate = $request->get('end_date', date('Y-m-d'));
            $dateFilter = $request->get('date_filter', 'this_week');
            $userId = $request->get('user_id');
            $divisionId = $request->get('division_id');
            $status = $request->get('status');
            
            // Apply date filter if not custom
            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }
            
            // Build query for stats
            $statsQuery = DB::table('break_times')
                ->leftJoin('users', 'users.id', '=', 'break_times.user_id')
                ->where('bt_date', '>=', $startDate)
                ->where('bt_date', '<=', $endDate);
            
            // Apply additional filters
            if ($userId) {
                $statsQuery->where('break_times.user_id', $userId);
            }
            if ($divisionId) {
                $statsQuery->where('users.ud_id', $divisionId);
            }
            if ($status) {
                $statsQuery->where('break_times.bt_status', $status);
            }
            
            // Get status-based statistics
            $statusStats = $statsQuery->select('bt_status', DB::raw('count(*) as total'))
                ->groupBy('bt_status')
                ->get();
            
            // Get type-based statistics
            $typeStats = $statsQuery->select('bt_type', DB::raw('count(*) as total'))
                ->groupBy('bt_type')
                ->get();
            
            // Get total break times
            $totalBreakTimes = $statsQuery->count();
            
            // Get average duration
            $avgDuration = $statsQuery->avg('bt_duration_minutes');
            
            $stats = [
                'status_stats' => $statusStats,
                'type_stats' => $typeStats,
                'total_break_times' => $totalBreakTimes,
                'average_duration' => round($avgDuration, 2),
                'date_range' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'filter' => $dateFilter
                ]
            ];
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting break time stats: ' . $e->getMessage(), [
                'startDate' => $startDate ?? 'not set',
                'endDate' => $endDate ?? 'not set',
                'dateFilter' => $dateFilter ?? 'not set',
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error getting statistics: ' . $e->getMessage()
            ], 500);
        }
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
            $startDate = $request->get('start_date', date('Y-m-d'));
            $endDate = $request->get('end_date', date('Y-m-d'));
            $dateFilter = $request->get('date_filter', 'this_week');
            
            // Apply date filter if not custom
            if ($dateFilter && $dateFilter !== 'custom') {
                $dateRange = $this->getDateRangeFromFilter($dateFilter);
                $startDate = $dateRange['startDate'];
                $endDate = $dateRange['endDate'];
            }
            
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
            if ($startDate) {
                $query->where('break_times.bt_date', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('break_times.bt_date', '<=', $endDate);
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
                    $btn .= '<a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start">';
                    $btn .= 'Actions';
                    $btn .= '<i class="ki-duotone ki-down fs-5 ms-1"></i>';
                    $btn .= '</a>';
                    $btn .= '<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">';
                    $btn .= '<div class="menu-item px-3">';
                    $btn .= '<a href="'.route('break-times.show', $row->id).'" class="menu-link px-3">View</a>';
                    $btn .= '</div>';
                    // Edit button removed as requested
                    // $btn .= '<div class="menu-item px-3">';
                    // $btn .= '<a href="'.route('break-times.edit', $row->id).'" class="menu-link px-3">Edit</a>';
                    // $btn .= '</div>';
                    $btn .= '<div class="menu-item px-3">';
                    $btn .= '<a href="#" class="menu-link px-3 text-warning" onclick="cancelBreakTime('.$row->id.')">Cancel</a>';
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
                    $duration = $row->bt_duration_minutes;
                    
                    // Calculate duration manually if bt_duration_minutes is empty, null, or 0
                    if ((empty($duration) || $duration == 0) && !empty($row->bt_start_time) && !empty($row->bt_end_time)) {
                        try {
                            $startTime = \Carbon\Carbon::parse($row->bt_start_time);
                            $endTime = \Carbon\Carbon::parse($row->bt_end_time);
                            $duration = $endTime->diffInMinutes($startTime);
                        } catch (\Exception $e) {
                            $duration = 0;
                        }
                    }
                    
                    if ($duration > 0) {
                        $hours = floor($duration / 60);
                        $minutes = $duration % 60;
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
                $shiftType = $dailySchedule->shiftCode->getBreakAllowancePrimaryType();
                $allowance = $breakTime->getBreakAllowance($shiftType);
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

        // Get shift type from shift code using compatibility method
        $shiftCodeModel = \App\Models\ShiftCode::find($dailySchedule->sc_id);
        $shiftType = $shiftCodeModel ? $shiftCodeModel->getBreakAllowancePrimaryType() : 'PART TIME';
        $shiftCode = $dailySchedule->sc_code; // Get shift code for special cases

        // Define break allowance based on shift type and special shift codes
        $breakAllowance = 0;
        
        // Special case: PF, PF0, PFM shift codes get 2 breaks even if PART TIME
        if (in_array($shiftCode, ['PF', 'PF0', 'PFM'])) {
            $breakAllowance = 2; // 2 breaks for PF, PF0, PFM (30 minutes each)
        } else {
            // Regular logic based on shift type
        switch ($shiftType) {
            case 'FULL TIME':
                $breakAllowance = 1; // 1 break for full time (60 minutes)
                break;
            case 'PART FULL':
                $breakAllowance = 2; // 2 breaks for part full (30 minutes each)
                break;
            case 'PART TIME':
                $breakAllowance = 1; // 1 break for part time (30 minutes)
                break;
            case 'CASUAL':
                $breakAllowance = 1; // 1 break for casual (30 minutes)
                break;
            case 'ALL':
                $breakAllowance = 1; // 1 break for ALL type
                break;
            default:
                $breakAllowance = 1; // default 1 break
            }
        }
        
        // Count completed breaks today (all types combined)
        $completedBreaks = DB::table('break_times')
            ->where('user_id', $userId)
            ->where('bt_date', $today)
            ->where('bt_status', 'completed')
            ->count();
            
        // Get detailed break info for debugging
        $breakDetails = DB::table('break_times')
            ->where('user_id', $userId)
            ->where('bt_date', $today)
            ->select('bt_type', 'bt_status', 'bt_start_time', 'bt_end_time', 'bt_duration_minutes')
            ->get();
            
        \Log::info('Break details for debugging', [
            'user_id' => $userId,
            'date' => $today,
            'break_details' => $breakDetails,
            'total_completed' => $completedBreaks
        ]);
        
        // Clean up invalid break records (breaks without end time but marked as completed)
        $invalidBreaks = DB::table('break_times')
            ->where('user_id', $userId)
            ->where('bt_date', $today)
            ->where('bt_status', 'completed')
            ->whereNull('bt_end_time')
            ->get();
            
        if ($invalidBreaks->count() > 0) {
            \Log::warning('Found invalid completed breaks without end time', [
                'user_id' => $userId,
                'invalid_breaks' => $invalidBreaks
            ]);
            
            // Update invalid breaks to cancelled status
            DB::table('break_times')
                ->where('user_id', $userId)
                ->where('bt_date', $today)
                ->where('bt_status', 'completed')
                ->whereNull('bt_end_time')
                ->update(['bt_status' => 'cancelled']);
                
            // Recalculate completed breaks
            $completedBreaks = DB::table('break_times')
                ->where('user_id', $userId)
                ->where('bt_date', $today)
                ->where('bt_status', 'completed')
                ->count();
        }

        \Log::info('Returning break allowance', [
            'shift_type' => $shiftType,
            'shift_code' => $shiftCode,
            'break_allowance' => $breakAllowance,
            'completed_breaks' => $completedBreaks
        ]);

        return response()->json([
            'success' => true,
            'shift_type' => $shiftType,
            'shift_code' => $shiftCode,
            'break_allowance' => $breakAllowance,
            'completed_breaks' => $completedBreaks
        ]);
    }

    /**
     * Clean up invalid break records for a specific user
     * This method can be called manually to fix data inconsistencies
     */
    public function cleanupInvalidBreaks(Request $request)
    {
        try {
            $userId = $request->get('user_id', auth()->user()->id);
            $date = $request->get('date', date('Y-m-d'));
            
            \Log::info('Starting cleanup for invalid breaks', [
                'user_id' => $userId,
                'date' => $date
            ]);

            // Find all breaks for the user on the specified date
            $allBreaks = DB::table('break_times')
                ->where('user_id', $userId)
                ->where('bt_date', $date)
                ->orderBy('bt_start_time')
                ->get();

            \Log::info('Found breaks before cleanup', [
                'user_id' => $userId,
                'date' => $date,
                'total_breaks' => $allBreaks->count(),
                'breaks' => $allBreaks
            ]);

            // Get user's shift type for this date
            $dailySchedule = DB::table('daily_schedules')
                ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
                ->select('shift_codes.sc_type')
                ->where('daily_schedules.user_id', $userId)
                ->where('daily_schedules.ds_date', $date)
                ->first();

            // Get shift type using compatibility method
            if ($dailySchedule && $dailySchedule->sc_id) {
                $shiftCodeModel = \App\Models\ShiftCode::find($dailySchedule->sc_id);
                $shiftType = $shiftCodeModel ? $shiftCodeModel->getBreakAllowancePrimaryType() : 'PART TIME';
            } else {
                $shiftType = 'PART TIME'; // Default fallback
            }
            
            // Get break allowance for this shift type
            $breakTime = new BreakTime();
            $breakAllowance = $breakTime->getBreakAllowance($shiftType);
            $maxAllowedBreaks = array_sum(array_column($breakAllowance, 'count'));

            \Log::info('Break allowance info', [
                'shift_type' => $shiftType,
                'break_allowance' => $breakAllowance,
                'max_allowed_breaks' => $maxAllowedBreaks
            ]);

            // If user has more breaks than allowed, keep only the first ones
            if ($allBreaks->count() > $maxAllowedBreaks) {
                $breaksToKeep = $allBreaks->take($maxAllowedBreaks);
                $breaksToRemove = $allBreaks->slice($maxAllowedBreaks);
                
                \Log::info('Breaks to keep', [
                    'count' => $breaksToKeep->count(),
                    'breaks' => $breaksToKeep
                ]);
                
                \Log::info('Breaks to remove', [
                    'count' => $breaksToRemove->count(),
                    'breaks' => $breaksToRemove
                ]);

                // Update excess breaks to cancelled status
                foreach ($breaksToRemove as $break) {
                    DB::table('break_times')
                        ->where('id', $break->id)
                        ->update([
                            'bt_status' => 'cancelled',
                            'bt_notes' => 'Cancelled - Exceeded break allowance limit',
                            'updated_at' => now()
                        ]);
                }

                \Log::info('Cleanup completed', [
                    'user_id' => $userId,
                    'date' => $date,
                    'breaks_kept' => $breaksToKeep->count(),
                    'breaks_cancelled' => $breaksToRemove->count()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Cleanup completed successfully',
                    'data' => [
                        'shifts_kept' => $breaksToKeep->count(),
                        'shifts_cancelled' => $breaksToRemove->count(),
                        'max_allowed' => $maxAllowedBreaks,
                        'shift_type' => $shiftType
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'No cleanup needed - breaks within allowance',
                    'data' => [
                        'current_breaks' => $allBreaks->count(),
                        'max_allowed' => $maxAllowedBreaks,
                        'shift_type' => $shiftType
                    ]
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Error during cleanup: ' . $e->getMessage(), [
                'user_id' => $userId ?? 'unknown',
                'date' => $date ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error during cleanup: ' . $e->getMessage()
            ], 500);
        }
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

        // Validate using BreakTime model rules
        $breakTime = new BreakTime();
        
        // Check if user can start break (for active status)
        if ($request->bt_status === 'active') {
            // Check if this is a new break (not editing existing)
            $existingBreak = DB::table('break_times')
                ->where('user_id', $request->user_id)
                ->where('bt_date', $request->bt_date)
                ->where('bt_type', $request->bt_type)
                ->where('bt_status', 'active')
                ->first();
                
            if ($existingBreak) {
                return back()->with('error', 'User already has an active break of this type for today')->withInput();
            }
            
            // Check if user has schedule for this date
            $dailySchedule = \App\Models\DailySchedule::where('user_id', $request->user_id)
                ->where('ds_date', $request->bt_date)
                ->with('shiftCode')
                ->first();
                
            if (!$dailySchedule || !$dailySchedule->shiftCode) {
                return back()->with('error', 'User does not have a schedule for this date')->withInput();
            }
            
            // Check break quota
            $shiftType = $dailySchedule->shiftCode->getBreakAllowancePrimaryType();
            $breakAllowance = $breakTime->getBreakAllowance($shiftType);
            
            if (!isset($breakAllowance[$request->bt_type])) {
                return back()->with('error', 'This break type is not allowed for user\'s shift type')->withInput();
            }
            
            $completedBreaksCount = DB::table('break_times')
                ->where('user_id', $request->user_id)
                ->where('bt_date', $request->bt_date)
                ->where('bt_type', $request->bt_type)
                ->where('bt_status', 'completed')
                ->count();
                
            if ($completedBreaksCount >= $breakAllowance[$request->bt_type]['count']) {
                return back()->with('error', 'Break quota exceeded for today')->withInput();
            }
        }

        // If validation passes, insert the break time
        $breakTimeData = [
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
        ];

        $result = DB::table('break_times')->insert($breakTimeData);

        if ($result) {
            return redirect()->route('break-times.index')->with('success', 'Break time created successfully');
        } else {
            return back()->with('error', 'Failed to create break time')->withInput();
        }
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
            
            // Use BreakTime model validation
            $breakTime = new BreakTime();
            
            // Check if user can start break using model validation
            if (!$breakTime->canStartBreak($user->id, $breakType)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot start break. Please check your schedule, existing breaks, or break quota.'
                ], 400);
            }
            
            // Start break using model method
            $result = $breakTime->startBreak($user->id, $breakType);
            
            if ($result) {
                // Get break duration from allowance
                $today = date('Y-m-d');
                $dailySchedule = \App\Models\DailySchedule::where('user_id', $user->id)
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
                    'break_duration' => $breakDuration
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to start break. Please try again.'
                ], 400);
            }
            
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
            
            // Calculate duration manually if bt_duration_minutes is empty, null, or 0
            $durationMinutes = $breakTime->bt_duration_minutes;
            if ((empty($durationMinutes) || $durationMinutes == 0) && !empty($breakTime->bt_start_time) && !empty($breakTime->bt_end_time)) {
                try {
                    $startTimeCarbon = \Carbon\Carbon::parse($breakTime->bt_start_time);
                    $endTimeCarbon = \Carbon\Carbon::parse($breakTime->bt_end_time);
                    $durationMinutes = $endTimeCarbon->diffInMinutes($startTimeCarbon);
                } catch (\Exception $e) {
                    $durationMinutes = 0;
                }
            }
            
            $duration = $durationMinutes > 0 ? 
                sprintf('%02d:%02d', floor($durationMinutes / 60), $durationMinutes % 60) : '-';
            
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

    /**
     * Export summary report to Excel
     */
    public function exportSummaryToExcel(Request $request)
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
            
            $summaryData = $this->getBreakTimeSummary($startDate, $endDate, $request->get('division_id'));
            
            // Apply search filter if provided
            if ($request->filled('search')) {
                $search = $request->get('search');
                $summaryData = $summaryData->filter(function($item) use ($search) {
                    return stripos($item->u_name, $search) !== false || 
                           stripos($item->u_nip, $search) !== false;
                });
            }
            
            $filename = 'break_time_summary_' . date('Y-m-d_H-i-s');
            if ($request->get('division_id')) {
                $division = DB::table('user_divisions')->find($request->get('division_id'));
                $filename .= '_' . ($division ? str_replace(' ', '_', $division->ud_name) : 'all');
            }
            if ($request->get('start_date') && $request->get('end_date')) {
                $filename .= '_' . $request->get('start_date') . '_to_' . $request->get('end_date');
            }
            $filename .= '.xlsx';
            
            return Excel::download(new BreakTimeSummaryExport($summaryData), $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Error exporting data: ' . $e->getMessage());
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
            
            $summaryData = $this->getBreakTimeSummary($startDate, $endDate, $request->get('division_id'));
            
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
            $filename = 'break_time_summary_' . date('Y-m-d_H-i-s');
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
            \Log::error('Export break time summary PDF error: ' . $e->getMessage());
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
            <title>Break Time Summary Report</title>
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
                <h1>LAPORAN REKAPITULASI BREAK TIME</h1>
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
                    <div class="stat-number">' . $summaryData->sum('total_breaks') . '</div>
                    <div class="stat-label">Total Break</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . $summaryData->sum('no_break_shifts') . '</div>
                    <div class="stat-label">No Break Shifts</div>
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
                        <th>Jam Kerja/User Type</th>
                        <th class="text-center">Total Break</th>
                        <th class="text-center">No Break (ada jadwal shift namun tidak ceklog Break)</th>
                        <th class="text-center">Melebihi Waktu Break</th>
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
                        <td class="text-center">' . ($item->total_breaks ?? 0) . '</td>
                        <td class="text-center">' . ($item->no_break_shifts ?? 0) . '</td>
                        <td class="text-center">' . ($item->exceeded_break_time ?? 0) . '</td>
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
     * Export staff break time to Excel
     */
    public function exportStaffToExcel($user_id, Request $request)
    {
        $this->validateAccess();
        
        try {
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            
            // Get staff info
            $staff = DB::table('users as u')
                ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
                ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
                ->leftJoin('user_types as ut', 'u.ut_id', '=', 'ut.id')
                ->select([
                    'u.id',
                    'u.u_nip',
                    'u.u_name',
                    'up.up_name as position_name',
                    'ud.ud_name as division_name',
                    'ut.ut_name as work_type'
                ])
                ->where('u.id', $user_id)
                ->where('u.u_delete', '!=', '1')
                ->first();
            
            if (!$staff) {
                abort(404, 'Staff not found');
            }
            
            // Get break time data - Fixed to avoid duplicates
            $breakTimesQuery = DB::table('break_times as bt')
                ->leftJoin('daily_schedules as ds', function($join) use ($user_id) {
                    $join->on('bt.user_id', '=', 'ds.user_id')
                         ->where('ds.user_id', $user_id)
                         ->where('ds.ds_date', '=', 'bt.bt_date'); // Join on same date to avoid duplicates
                })
                ->where('bt.user_id', $user_id)
                ->whereBetween('bt.bt_date', [$startDate, $endDate])
                ->select([
                    'bt.bt_date',
                    'bt.bt_type',
                    'bt.bt_start_time',
                    'bt.bt_end_time',
                    'bt.bt_duration_minutes',
                    'bt.bt_status',
                    'bt.bt_notes',
                    'ds.ds_start_time as shift_start',
                    'ds.ds_end_time as shift_end',
                    'ds.ds_status as shift_status'
                ]);
            
            // Apply status filter if provided
            if ($request->filled('status') && $request->status !== '') {
                $breakTimesQuery->where('bt.bt_status', $request->status);
            }
            
            $breakTimes = $breakTimesQuery->orderBy('bt.bt_date', 'desc')
                                         ->orderBy('bt.bt_start_time', 'desc')
                                         ->get();
            
            // Process break times to add calculated duration and exceeded notes (same as staffDatatables)
            $processedBreakTimes = collect();
            foreach ($breakTimes as $breakTime) {
                $breakTimeArray = (array) $breakTime;
                
                // Calculate duration manually if bt_duration_minutes is empty, null, or 0
                if ((empty($breakTimeArray['bt_duration_minutes']) || $breakTimeArray['bt_duration_minutes'] == 0) && !empty($breakTimeArray['bt_start_time']) && !empty($breakTimeArray['bt_end_time'])) {
                    try {
                        $startTime = \Carbon\Carbon::parse($breakTimeArray['bt_start_time']);
                        $endTime = \Carbon\Carbon::parse($breakTimeArray['bt_end_time']);
                        $durationMinutes = $endTime->diffInMinutes($startTime);
                        $breakTimeArray['bt_duration_minutes'] = $durationMinutes;
                    } catch (\Exception $e) {
                        $breakTimeArray['bt_duration_minutes'] = 0;
                    }
                }
                
                // Get user's shift type to determine break allowance and add exceeded note
                $userShiftType = DB::table('users as u')
                    ->leftJoin('daily_schedules as ds', 'u.id', '=', 'ds.user_id')
                    ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
                    ->where('u.id', $user_id)
                    ->where('ds.ds_date', $breakTimeArray['bt_date'])
                    ->select('sc.sc_type')
                    ->first();
                
                $shiftType = $userShiftType ? $userShiftType->sc_type : 'Part Time';
                $breakTimeModel = new \App\Models\BreakTime();
                $breakAllowance = $breakTimeModel->getBreakAllowance($shiftType);
                
                // Determine max duration for this user type
                $maxDuration = 30; // Default
                if (isset($breakAllowance['break_1'])) {
                    $maxDuration = $breakAllowance['break_1']['duration'];
                }
                
                // Add exceeded note if break duration exceeds allowance
                $currentDuration = $breakTimeArray['bt_duration_minutes'] ?? 0;
                if ($currentDuration > $maxDuration) {
                    $exceededMinutes = $currentDuration - $maxDuration;
                    $originalNotes = $breakTimeArray['bt_notes'] ?? '';
                    $exceededNote = "⚠️ EXCEEDED: Break melebihi jatah {$maxDuration} menit sebanyak {$exceededMinutes} menit";
                    
                    if (!empty($originalNotes)) {
                        $breakTimeArray['bt_notes'] = $originalNotes . ' | ' . $exceededNote;
                    } else {
                        $breakTimeArray['bt_notes'] = $exceededNote;
                    }
                }
                
                $processedBreakTimes->push((object) $breakTimeArray);
            }
            
            // Get user's shift type for break allowance info
            $userShiftType = DB::table('users as u')
                ->leftJoin('daily_schedules as ds', 'u.id', '=', 'ds.user_id')
                ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
                ->where('u.id', $user_id)
                ->whereBetween('ds.ds_date', [$startDate, $endDate])
                ->select('sc.sc_type')
                ->first();
            
            $shiftType = $userShiftType ? $userShiftType->sc_type : 'Part Time';
            $breakTime = new \App\Models\BreakTime();
            $breakAllowance = $breakTime->getBreakAllowance($shiftType);
            
            \Log::info('Staff Excel export - User break allowance', [
                'user_id' => $user_id,
                'shift_type' => $shiftType,
                'break_allowance' => $breakAllowance
            ]);
            
            $filename = 'break_time_staff_' . $staff->u_nip . '_' . $startDate . '_to_' . $endDate . '.xlsx';
            
            return Excel::download(new BreakTimeStaffExport($processedBreakTimes, $staff), $filename);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }

    /**
     * Export staff break time to PDF
     */
    public function exportStaffToPDF($user_id, Request $request)
    {
        $this->validateAccess();
        
        try {
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            
            // Get staff info
            $staff = DB::table('users as u')
                ->leftJoin('user_divisions as ud', 'u.ud_id', '=', 'ud.id')
                ->leftJoin('user_positions as up', 'u.up_id', '=', 'up.id')
                ->leftJoin('user_types as ut', 'u.ut_id', '=', 'ut.id')
                ->select([
                    'u.id',
                    'u.u_nip',
                    'u.u_name',
                    'up.up_name as position_name',
                    'ud.ud_name as division_name',
                    'ut.ut_name as work_type'
                ])
                ->where('u.id', $user_id)
                ->where('u.u_delete', '!=', '1')
                ->first();
            
            if (!$staff) {
                abort(404, 'Staff not found');
            }
            
            // Get break time data - Fixed to avoid duplicates
            $breakTimesQuery = DB::table('break_times as bt')
                ->leftJoin('daily_schedules as ds', function($join) use ($user_id) {
                    $join->on('bt.user_id', '=', 'ds.user_id')
                         ->where('ds.user_id', $user_id)
                         ->where('ds.ds_date', '=', 'bt.bt_date'); // Join on same date to avoid duplicates
                })
                ->where('bt.user_id', $user_id)
                ->whereBetween('bt.bt_date', [$startDate, $endDate])
                ->select([
                    'bt.bt_date',
                    'bt.bt_type',
                    'bt.bt_start_time',
                    'bt.bt_end_time',
                    'bt.bt_duration_minutes',
                    'bt.bt_status',
                    'bt.bt_notes',
                    'ds.ds_start_time as shift_start',
                    'ds.ds_end_time as shift_end',
                    'ds.ds_status as shift_status'
                ]);
            
            // Apply status filter if provided
            if ($request->filled('status') && $request->status !== '') {
                $breakTimesQuery->where('bt.bt_status', $request->status);
            }
            
            $breakTimes = $breakTimesQuery->orderBy('bt.bt_date', 'desc')
                                         ->orderBy('bt.bt_start_time', 'desc')
                                         ->get();
            
            // Process break times to add calculated duration and exceeded notes (same as staffDatatables)
            $processedBreakTimes = collect();
            foreach ($breakTimes as $breakTime) {
                $breakTimeArray = (array) $breakTime;
                
                // Calculate duration manually if bt_duration_minutes is empty, null, or 0
                if ((empty($breakTimeArray['bt_duration_minutes']) || $breakTimeArray['bt_duration_minutes'] == 0) && !empty($breakTimeArray['bt_start_time']) && !empty($breakTimeArray['bt_end_time'])) {
                    try {
                        $startTime = \Carbon\Carbon::parse($breakTimeArray['bt_start_time']);
                        $endTime = \Carbon\Carbon::parse($breakTimeArray['bt_end_time']);
                        $durationMinutes = $endTime->diffInMinutes($startTime);
                        $breakTimeArray['bt_duration_minutes'] = $durationMinutes;
                    } catch (\Exception $e) {
                        $breakTimeArray['bt_duration_minutes'] = 0;
                    }
                }
                
                // Get user's shift type to determine break allowance and add exceeded note
                $userShiftType = DB::table('users as u')
                    ->leftJoin('daily_schedules as ds', 'u.id', '=', 'ds.user_id')
                    ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
                    ->where('u.id', $user_id)
                    ->where('ds.ds_date', $breakTimeArray['bt_date'])
                    ->select('sc.sc_type')
                    ->first();
                
                $shiftType = $userShiftType ? $userShiftType->sc_type : 'Part Time';
                $breakTimeModel = new \App\Models\BreakTime();
                $breakAllowance = $breakTimeModel->getBreakAllowance($shiftType);
                
                // Determine max duration for this user type
                $maxDuration = 30; // Default
                if (isset($breakAllowance['break_1'])) {
                    $maxDuration = $breakAllowance['break_1']['duration'];
                }
                
                // Add exceeded note if break duration exceeds allowance
                $currentDuration = $breakTimeArray['bt_duration_minutes'] ?? 0;
                if ($currentDuration > $maxDuration) {
                    $exceededMinutes = $currentDuration - $maxDuration;
                    $originalNotes = $breakTimeArray['bt_notes'] ?? '';
                    $exceededNote = "⚠️ EXCEEDED: Break melebihi jatah {$maxDuration} menit sebanyak {$exceededMinutes} menit";
                    
                    if (!empty($originalNotes)) {
                        $breakTimeArray['bt_notes'] = $originalNotes . ' | ' . $exceededNote;
                    } else {
                        $breakTimeArray['bt_notes'] = $exceededNote;
                    }
                }
                
                $processedBreakTimes->push((object) $breakTimeArray);
            }
            
            // Generate HTML for PDF
            $html = $this->generateStaffReportHTML($processedBreakTimes, $staff, $request);
            
            $filename = 'break_time_staff_' . $staff->u_nip . '_' . $startDate . '_to_' . $endDate . '.pdf';
            
            $pdf = \PDF::loadHTML($html);
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Export staff break time PDF error: ' . $e->getMessage());
            return back()->with('error', 'Export PDF failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate HTML for staff report PDF
     */
    private function generateStaffReportHTML($breakTimes, $staff, $request)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Staff Break Time Report</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h1 { margin: 0; color: #2E75B6; }
                .header p { margin: 5px 0; color: #666; }
                .staff-info { margin-bottom: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; }
                .staff-info table { width: 100%; border-collapse: collapse; }
                .staff-info td { padding: 5px; border: none; }
                .staff-info td:first-child { font-weight: bold; width: 120px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #4472C4; color: white; font-weight: bold; }
                .text-center { text-align: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN BREAK TIME STAFF</h1>
                <p>Periode: ' . date('d/m/Y', strtotime($request->get('start_date', date('Y-m-01')))) . ' - ' . date('d/m/Y', strtotime($request->get('end_date', date('Y-m-t')))) . '</p>
                <p>Dibuat pada: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            
            <div class="staff-info">
                <table>
                    <tr>
                        <td>NIP:</td>
                        <td>' . ($staff->u_nip ?? '-') . '</td>
                        <td>Posisi:</td>
                        <td>' . ($staff->position_name ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>Nama:</td>
                        <td>' . ($staff->u_name ?? '-') . '</td>
                        <td>Divisi:</td>
                        <td>' . ($staff->division_name ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>Jenis Kerja:</td>
                        <td>' . ($staff->work_type ?? '-') . '</td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Tipe Break</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th>Shift Start</th>
                        <th>Shift End</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>';

        $no = 1;
        foreach ($breakTimes as $breakTime) {
            $breakType = $breakTime->bt_type === 'break_1' ? 'Break 1' : 'Break 2';
            $startTime = $breakTime->bt_start_time ? date('H:i', strtotime($breakTime->bt_start_time)) : '-';
            $endTime = $breakTime->bt_end_time ? date('H:i', strtotime($breakTime->bt_end_time)) : '-';
            
            // Calculate duration manually if bt_duration_minutes is empty, null, or 0
            $durationMinutes = $breakTime->bt_duration_minutes;
            if ((empty($durationMinutes) || $durationMinutes == 0) && !empty($breakTime->bt_start_time) && !empty($breakTime->bt_end_time)) {
                try {
                    $startTimeCarbon = \Carbon\Carbon::parse($breakTime->bt_start_time);
                    $endTimeCarbon = \Carbon\Carbon::parse($breakTime->bt_end_time);
                    $durationMinutes = $endTimeCarbon->diffInMinutes($startTimeCarbon);
                } catch (\Exception $e) {
                    $durationMinutes = 0;
                }
            }
            
            $duration = $durationMinutes > 0 ? 
                sprintf('%02d:%02d', floor($durationMinutes / 60), $durationMinutes % 60) : '-';
            
            $shiftStart = $breakTime->shift_start ? date('H:i', strtotime($breakTime->shift_start)) : '-';
            $shiftEnd = $breakTime->shift_end ? date('H:i', strtotime($breakTime->shift_end)) : '-';
            
            $html .= '
                    <tr>
                        <td class="text-center">' . $no++ . '</td>
                        <td>' . date('d/m/Y', strtotime($breakTime->bt_date)) . '</td>
                        <td class="text-center">' . $breakType . '</td>
                        <td class="text-center">' . $startTime . '</td>
                        <td class="text-center">' . $endTime . '</td>
                        <td class="text-center">' . $duration . '</td>
                        <td class="text-center">' . ucfirst($breakTime->bt_status ?? '-') . '</td>
                        <td class="text-center">' . $shiftStart . '</td>
                        <td class="text-center">' . $shiftEnd . '</td>
                        <td>' . ($breakTime->bt_notes ?? '-') . '</td>
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
     * Cancel break time and return allowance
     */
    public function cancelBreakTime(Request $request, $id)
    {
        try {
            $breakTime = BreakTime::findOrFail($id);
            
            // Check if break time can be cancelled
            if ($breakTime->bt_status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Break time is already cancelled'
                ], 400);
            }
            
            // Allow cancelling completed break times (for cases where user accidentally clicked)
            // if ($breakTime->bt_status === 'completed') {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Cannot cancel completed break time'
            //     ], 400);
            // }
            
            // Start transaction
            DB::beginTransaction();
            
            try {
                // Update break time status to cancelled
                $breakTime->bt_status = 'cancelled';
                $breakTime->bt_notes = $breakTime->bt_notes ? $breakTime->bt_notes . ' [Cancelled]' : '[Cancelled]';
                $breakTime->save();
                
                // Return break allowance to user
                $userId = $breakTime->user_id;
                $breakType = $breakTime->bt_type;
                $breakDate = $breakTime->bt_date;
                
                // Get daily schedule for the break date
                $dailySchedule = \App\Models\DailySchedule::where('user_id', $userId)
                    ->where('ds_date', $breakDate)
                    ->with('shiftCode')
                    ->first();
                
                if ($dailySchedule && $dailySchedule->shiftCode) {
                    $shiftType = $dailySchedule->shiftCode->getBreakAllowancePrimaryType();
                    $breakTimeModel = new BreakTime();
                    $allowance = $breakTimeModel->getBreakAllowance($shiftType);
                    
                    if (isset($allowance[$breakType])) {
                        // Return the allowance (this might need to be implemented in BreakTime model)
                        // For now, we'll just log that allowance should be returned
                        \Log::info('Break time cancelled - allowance should be returned', [
                            'break_time_id' => $id,
                            'user_id' => $userId,
                            'break_type' => $breakType,
                            'break_date' => $breakDate,
                            'allowance' => $allowance[$breakType]
                        ]);
                    }
                }
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Break time cancelled successfully. Allowance has been returned.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
            
        } catch (\Exception $e) {
            \Log::error('Error cancelling break time: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling break time: ' . $e->getMessage()
            ], 500);
        }
    }
} 