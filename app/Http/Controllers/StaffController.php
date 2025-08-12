<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UserPosition;
use App\Models\UserType;
use App\Models\LeaveBalance;
use Yajra\DataTables\Facades\DataTables;

class StaffController extends Controller
{
    protected function validateAccess()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
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
        
        $title = 'Staff Management';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        
        // Get all users with NIP (tanpa join ke position dan division)
        $staff = DB::table('users')
            ->select([
                'users.*',
                DB::raw('NULL as position_name'),
                DB::raw('NULL as position_code'),
                DB::raw('NULL as position_color'),
                DB::raw('NULL as division_name'),
                DB::raw('NULL as division_code'),
                'leave_balances.lb_remaining_balance'
            ])
            ->leftJoin('leave_balances', function($join) {
                $join->on('leave_balances.user_id', '=', 'users.id')
                     ->where('leave_balances.lb_year', '=', date('Y'))
                     ->where('leave_balances.leave_type_id', '=', function($query) {
                         $query->select('id')
                               ->from('leave_types')
                               ->where('lt_code', 'ANNUAL')
                               ->limit(1);
                     });
            })
            ->where('users.u_delete', '0')
            ->whereNotNull('users.u_nip')
            ->orderBy('users.u_name')
            ->get();

        // Get positions, divisions, and user types for dropdowns
        $userPosition = new UserPosition();
        $positions = $userPosition->getActivePositions();
        $divisions = DB::table('user_divisions')
            ->where('ud_status', 'active')
            ->orderBy('ud_name')
            ->get();
        $userType = new UserType();
        $userTypes = $userType->getActiveUserTypes();

        $data = [
            'title' => $title,
            'subtitle' => 'Staff Management',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.staff.index', compact('staff', 'positions', 'divisions', 'userTypes', 'data'));
    }

    public function updatePosition(Request $request)
    {
        $this->validateAccess();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'up_id' => 'required|exists:user_positions,id'
        ]);

        try {
            $result = DB::table('users')
                ->where('id', $request->user_id)
                ->update([
                    'up_id' => $request->up_id,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Position updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update position'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateDivision(Request $request)
    {
        $this->validateAccess();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ud_id' => 'required|exists:user_divisions,id'
        ]);

        try {
            $result = DB::table('users')
                ->where('id', $request->user_id)
                ->update([
                    'ud_id' => $request->ud_id,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Division updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update division'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateUserType(Request $request)
    {
        $this->validateAccess();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ut_id' => 'required|exists:user_types,id'
        ]);

        try {
            $result = DB::table('users')
                ->where('id', $request->user_id)
                ->update([
                    'ut_id' => $request->ut_id,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'User type updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user type'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateLeaveBalance(Request $request)
    {
        $this->validateAccess();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'lb_remaining_balance' => 'required|numeric|min:0'
        ]);

        try {
            // Get annual leave type ID
            $annualLeaveType = DB::table('leave_types')
                ->where('lt_code', 'ANNUAL')
                ->first();

            if (!$annualLeaveType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Annual leave type not found'
                ]);
            }

            // Check if leave balance exists
            $leaveBalance = DB::table('leave_balances')
                ->where('user_id', $request->user_id)
                ->where('leave_type_id', $annualLeaveType->id)
                ->where('lb_year', date('Y'))
                ->first();

            if ($leaveBalance) {
                // Update existing balance
                $result = DB::table('leave_balances')
                    ->where('id', $leaveBalance->id)
                    ->update([
                        'lb_remaining_balance' => $request->lb_remaining_balance,
                        'lb_used_balance' => $leaveBalance->lb_initial_balance - $request->lb_remaining_balance,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            } else {
                // Create new balance
                $result = DB::table('leave_balances')->insert([
                    'user_id' => $request->user_id,
                    'leave_type_id' => $annualLeaveType->id,
                    'lb_year' => date('Y'),
                    'lb_initial_balance' => $request->lb_remaining_balance,
                    'lb_used_balance' => 0,
                    'lb_remaining_balance' => $request->lb_remaining_balance,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Leave balance updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update leave balance'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            \Log::info('Staff datatables request received', [
                'request_data' => $request->all(),
                'user_id' => auth()->user()->id ?? 'not logged in'
            ]);

            $query = DB::table('users')
                ->select([
                    'users.id',
                    'users.u_name',
                    'users.u_nip',
                    'users.up_id',
                    'users.ud_id',
                    'users.ut_id',
                    'user_positions.up_name',
                    'user_divisions.ud_name',
                    'user_types.ut_name',
                    'leave_balances.lb_remaining_balance'
                ])
                ->leftJoin('user_positions', 'user_positions.id', '=', 'users.up_id')
                ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.ut_id')
                ->leftJoin('leave_balances', function($join) {
                    $join->on('leave_balances.user_id', '=', 'users.id')
                         ->where('leave_balances.lb_year', '=', date('Y'))
                         ->where('leave_balances.leave_type_id', '=', function($query) {
                             $query->select('id')
                                   ->from('leave_types')
                                   ->where('lt_code', 'ANNUAL')
                                   ->limit(1);
                         });
                })
                ->where('users.u_delete', '!=', '1')
                ->whereNotNull('users.u_nip');

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('users.u_name', 'like', '%' . $search . '%')
                      ->orWhere('users.u_nip', 'like', '%' . $search . '%');
                });
            }

            // Apply position filter
            if ($request->filled('position_filter')) {
                $query->where('users.up_id', $request->position_filter);
            }

            // Apply division filter
            if ($request->filled('division_filter')) {
                $query->where('users.ud_id', $request->division_filter);
            }

            \Log::info('Staff query built with filters', [
                'search' => $request->search ?? 'none',
                'position_filter' => $request->position_filter ?? 'none',
                'division_filter' => $request->division_filter ?? 'none'
            ]);

            $result = datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('up_name', function($row) {
                    return $row->up_name ?: '-';
                })
                ->editColumn('ud_name', function($row) {
                    return $row->ud_name ?: '-';
                })
                ->editColumn('ut_name', function($row) {
                    return $row->ut_name ?: '-';
                })
                ->editColumn('lb_remaining_balance', function($row) {
                    return $row->lb_remaining_balance ?: '0';
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm">';
                    $btn .= '<button type="button" class="btn btn-warning btn-xs" onclick="editPosition('.$row->id.')" title="Edit Position"><i class="ki-outline ki-user-square"></i></button>';
                    $btn .= '<button type="button" class="btn btn-info btn-xs" onclick="editDivision('.$row->id.')" title="Edit Division"><i class="ki-outline ki-office-bag"></i></button>';
                    $btn .= '<button type="button" class="btn btn-success btn-xs" onclick="editUserType('.$row->id.')" title="Edit User Type"><i class="ki-outline ki-profile-user"></i></button>';
                    $btn .= '<button type="button" class="btn btn-danger btn-xs" onclick="editLeaveBalance('.$row->id.')" title="Edit Leave Balance"><i class="ki-outline ki-calendar-search"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);

            \Log::info('Staff datatables response sent', [
                'records_total' => $result->getData()->recordsTotal ?? 0,
                'records_filtered' => $result->getData()->recordsFiltered ?? 0
            ]);

            return $result;
        }
    }
}
