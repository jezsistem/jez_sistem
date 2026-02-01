<?php

namespace App\Http\Controllers;

use App\Models\PublicHoliday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ShiftCode;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use Yajra\DataTables\Facades\DataTables;

class ShiftCodeController extends Controller
{
    protected function validateAccess($slug = null)
    {
        $segment = $slug ?? request()->segment(1);
        $slugToCheck = str_replace('_v2', '', $segment);

        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => $slugToCheck
            ])->exists();
        if (!$validate) {
            dd("Anda tidak memiliki akses ke menu ini, hubungi Administrator");
        }
    }

    protected function sidebar()
    {
        $user_id = auth()->user() ? auth()->user()->id : 1;
        $ma_id = DB::table('user_menu_accesses')->select('ma_id')
            ->where('u_id', $user_id)->get();
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

        $title = 'Shift Codes';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user ? $user->id : 1)->first();

        $shiftCode = new ShiftCode();
        $shiftCodes = $shiftCode->getActiveShiftCodes();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title ?? 'Shift Codes',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.shift_code.index', compact('shiftCodes', 'data'));
    }



    public function create()
    {
//        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;

        // Get user types from database for multiple selection
        $userTypes = DB::table('user_types')
            ->where('ut_status', 'active')
            ->orderBy('ut_name')
            ->get();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        return view('app.shift_code.create', compact('userTypes', 'data'));
    }

    public function store(Request $request)
    {
        // Debug logging
        \Log::info('ShiftCode store method called', [
            'request_data' => $request->all(),
            'user_id' => auth()->user()->id ?? 'not authenticated'
        ]);

        // Get valid user types for validation
        $userTypes = DB::table('user_types')
            ->where('ut_status', 'active')
            ->pluck('id')
            ->toArray();

        \Log::info('Valid user types for validation', ['user_types' => $userTypes]);

        try {
            $request->validate([
                'sc_code' => 'required|string|max:10|unique:shift_codes,sc_code',
                'sc_description' => 'required|string|max:255',
                'sc_shift_name' => 'required|string|max:100',
                'sc_start_time' => 'nullable|date_format:H:i',
                'sc_end_time' => 'nullable|date_format:H:i',
                'user_type_ids' => 'required|array|min:1',
                'user_type_ids.*' => 'exists:user_types,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        $data = [
            'sc_code' => strtoupper($request->sc_code),
            'sc_description' => $request->sc_description,
            'sc_shift_name' => $request->sc_shift_name,
            'sc_start_time' => $request->sc_start_time,
            'sc_end_time' => $request->sc_end_time,
            'sc_type' => 'MULTIPLE', // Set as MULTIPLE since we're using pivot table
            'sc_status' => 'active',
            'created_by' => auth()->user()->u_name ?? 'system',
        ];

        \Log::info('Attempting to store shift code', ['data' => $data]);

        $shiftCode = new ShiftCode();
        $result = $shiftCode->storeData('add', null, $data);

        if ($result) {
            // Attach user types to the shift code
            $shiftCodeModel = ShiftCode::find($result);
            if ($shiftCodeModel) {
                $shiftCodeModel->userTypes()->attach($request->user_type_ids);
                \Log::info('User types attached successfully', [
                    'shift_code_id' => $result,
                    'user_type_ids' => $request->user_type_ids
                ]);
            }

            \Log::info('Shift code stored successfully', ['id' => $result]);
            return redirect()->route('shift-codes.index')->with('success', 'Shift code berhasil ditambahkan');
        } else {
            \Log::error('Failed to store shift code');
            return back()->with('error', 'Gagal menambahkan shift code');
        }
    }

    public function show($id)
    {
//        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $shiftCode = ShiftCode::with(['dailySchedules' => function ($query) {
            $query->whereNotNull('ds_status')->orderBy('ds_date', 'desc');
        }, 'dailySchedules.user', 'userTypes'])->findOrFail($id);

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        return view('app.shift_code.show', compact('shiftCode', 'data'));
    }

    public function edit($id)
    {
//        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $shiftCode = ShiftCode::with('userTypes')->findOrFail($id);

        // Get user types from database for multiple selection
        $userTypes = DB::table('user_types')
            ->where('ut_status', 'active')
            ->orderBy('ut_name')
            ->get();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        return view('app.shift_code.edit', compact('shiftCode', 'userTypes', 'data'));
    }

    public function update(Request $request, $id)
    {
        // Debug logging
        \Log::info('ShiftCode update method called', [
            'shift_code_id' => $id,
            'request_data' => $request->all(),
            'user_id' => auth()->user()->id ?? 'not authenticated'
        ]);

        // Get valid user types for validation
        $userTypes = DB::table('user_types')
            ->where('ut_status', 'active')
            ->pluck('id')
            ->toArray();

        \Log::info('Valid user types for update validation', ['user_types' => $userTypes]);

        try {
            $request->validate([
                'sc_code' => 'required|string|max:10|unique:shift_codes,sc_code,' . $id,
                'sc_description' => 'required|string|max:255',
                'sc_shift_name' => 'required|string|max:100',
                'sc_start_time' => 'nullable|date_format:H:i',
                'sc_end_time' => 'nullable|date_format:H:i',
                'user_type_ids' => 'required|array|min:1',
                'user_type_ids.*' => 'exists:user_types,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Update validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'shift_code_id' => $id
            ]);
            throw $e;
        }

        $data = [
            'sc_code' => strtoupper($request->sc_code),
            'sc_description' => $request->sc_description,
            'sc_shift_name' => $request->sc_shift_name,
            'sc_start_time' => $request->sc_start_time,
            'sc_end_time' => $request->sc_end_time,
            'sc_type' => 'MULTIPLE', // Set as MULTIPLE since we're using pivot table
            'updated_by' => auth()->user()->u_name ?? 'system',
        ];

        \Log::info('Attempting to update shift code', ['data' => $data]);

        $shiftCode = new ShiftCode();
        $result = $shiftCode->storeData('edit', $id, $data);

        if ($result) {
            // Sync user types for the shift code
            $shiftCodeModel = ShiftCode::find($id);
            if ($shiftCodeModel) {
                // Filter out empty values and ensure we have valid IDs
                $userTypeIds = array_filter($request->user_type_ids, function ($id) {
                    return !empty($id) && is_numeric($id);
                });

                if (!empty($userTypeIds)) {
                    $shiftCodeModel->userTypes()->sync($userTypeIds);
                    \Log::info('User types synced successfully', [
                        'shift_code_id' => $id,
                        'user_type_ids' => $userTypeIds
                    ]);
                } else {
                    \Log::warning('No valid user type IDs provided for sync', [
                        'shift_code_id' => $id,
                        'raw_user_type_ids' => $request->user_type_ids
                    ]);
                }
            }

            \Log::info('Shift code updated successfully', ['id' => $id]);
            return redirect()->route('shift-codes.index')->with('success', 'Shift code berhasil diperbarui');
        } else {
            \Log::error('Failed to update shift code', ['id' => $id]);
            return back()->with('error', 'Gagal memperbarui shift code');
        }
    }

    public function destroy($id)
    {
        try {
            $shiftCode = new ShiftCode();
            $result = $shiftCode->deleteData($id);

            if ($result) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Shift code berhasil dihapus'
                    ]);
                }
                return redirect()->route('shift-codes.index')->with('success', 'Shift code berhasil dihapus');
            } else {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menghapus shift code'
                    ]);
                }
                return back()->with('error', 'Gagal menghapus shift code');
            }
        } catch (\Exception $e) {
            \Log::error('Error deleting shift code: ' . $e->getMessage(), [
                'shift_code_id' => $id,
                'user_id' => auth()->user()->id ?? 'not authenticated',
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getShiftCodesByType($type)
    {
//        $this->validateAccess();

        $shiftCodes = DB::table('shift_codes')
            ->where('sc_type', $type)
            ->where('sc_status', '!=', 'deleted')
            ->get();

        return response()->json($shiftCodes);
    }

    public function toggleStatus($id)
    {
        $shiftCode = ShiftCode::findOrFail($id);
        $shiftCode->sc_status = $shiftCode->sc_status === 'active' ? 'inactive' : 'active';
        $shiftCode->updated_by = auth()->user()->u_name ?? 'system';
        $shiftCode->save();

        return redirect()->route('shift-codes.index')->with('success', 'Status shift code berhasil diubah');
    }

    public function getDatatables(Request $request)
    {
        // Temporarily comment out for testing
        // $this->validateAccess();

        \Log::info('ShiftCodeController getDatatables called', [
            'ajax' => request()->ajax(),
            'user' => auth()->user() ? auth()->user()->id : 'not authenticated',
            'session_id' => session()->getId(),
            'headers' => $request->headers->all(),
            'url' => $request->url(),
            'method' => $request->method()
        ]);

        if (request()->ajax()) {
            $query = DB::select('
            SELECT 
                sc.id,
                sc.sc_code,
                sc.sc_description,
                sc.sc_shift_name,
                sc.sc_start_time,
                sc.sc_end_time,
                sc.sc_type,
                sc.sc_status,
                GROUP_CONCAT(ut.ut_name ORDER BY ut.ut_name SEPARATOR ", ") as compatible_user_types
            FROM ts_shift_codes sc
            LEFT JOIN ts_shift_code_user_types scut ON sc.id = scut.shift_code_id
            LEFT JOIN ts_user_types ut ON scut.user_type_id = ut.id
            WHERE sc.sc_status != "deleted"
            GROUP BY sc.id, sc.sc_code, sc.sc_description, sc.sc_shift_name, sc.sc_start_time, sc.sc_end_time, sc.sc_type, sc.sc_status
            ORDER BY sc.sc_code
        ');

            // Add search filter
            if ($request->search) {
                $search = $request->search;
                $query = DB::select('
                    SELECT 
                        sc.id,
                        sc.sc_code,
                        sc.sc_description,
                        sc.sc_shift_name,
                        sc.sc_start_time,
                        sc.sc_end_time,
                        sc.sc_type,
                        sc.sc_status,
                        GROUP_CONCAT(ut.ut_name ORDER BY ut.ut_name SEPARATOR ", ") as compatible_user_types
                    FROM ts_shift_codes sc
                    LEFT JOIN ts_shift_code_user_types scut ON sc.id = scut.shift_code_id
                    LEFT JOIN ts_user_types ut ON scut.user_type_id = ut.id
                    WHERE sc.sc_status != "deleted"
                    AND (
                        sc.sc_code LIKE ? OR 
                        sc.sc_description LIKE ? OR 
                        sc.sc_shift_name LIKE ? OR 
                        sc.sc_type LIKE ? OR 
                        ut.ut_name LIKE ?
                    )
                    GROUP BY sc.id, sc.sc_code, sc.sc_description, sc.sc_shift_name, sc.sc_start_time, sc.sc_end_time, sc.sc_type, sc.sc_status
                    ORDER BY sc.sc_code
                ', ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%']);
            }

            $result = DataTables::of(collect($query))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">';
                    $btn .= '    <!--begin::Toggle-->';
                    $btn .= '    <button type="button" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start">';
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

                    // View → cek akses read
                    if (hasAccess(auth()->user()->up_id, 'read')) {
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="' . route('shift-codes.show', $row->id) . '" class="menu-link px-3">View</a>';
                        $btn .= '        </div>';
                    }

                    // Edit → cek akses update
                    if (hasAccess(auth()->user()->up_id, 'update')) {
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="' . route('shift-codes.edit', $row->id) . '" class="menu-link px-3">Edit</a>';
                        $btn .= '        </div>';
                    }

                    // Delete → cek akses delete
                    if (hasAccess(auth()->user()->up_id, 'delete')) {
                        $btn .= '        <div class="menu-item px-3">';
                        $btn .= '            <a href="javascript:void(0)" onclick="deleteShiftCode(' . $row->id . ')" class="menu-link px-3 text-danger">Delete</a>';
                        $btn .= '        </div>';
                    }

                    $btn .= '    </div>';
                    $btn .= '    <!--end::Menu-->';
                    $btn .= '</div>';

                    return $btn;
                })
                ->editColumn('sc_start_time', function ($row) {
                    return $row->sc_start_time ? date('H:i', strtotime($row->sc_start_time)) : '-';
                })
                ->editColumn('sc_end_time', function ($row) {
                    return $row->sc_end_time ? date('H:i', strtotime($row->sc_end_time)) : '-';
                })
                ->editColumn('sc_status', function ($row) {
                    $statusClass = $row->sc_status === 'active' ? 'badge badge-success' : 'badge badge-danger';
                    $statusText = $row->sc_status === 'active' ? 'Active' : 'Inactive';
                    return '<span class="' . $statusClass . '">' . $statusText . '</span>';
                })
                ->rawColumns(['action', 'sc_status'])
                ->make(true);

            \Log::info('ShiftCodeController getDatatables response', [
                'data_count' => count($result->getData()->data ?? [])
            ]);

            return $result;
        }

        \Log::info('ShiftCodeController getDatatables - not AJAX request');
        return response()->json(['error' => 'Not an AJAX request']);
    }

    public function indexUpdated()
    {
        $this->validateAccess('shift-codes');
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;

        // Get user types from database for multiple selection
        $userTypes = DB::table('user_types')
            ->where('ut_status', 'active')
            ->orderBy('ut_name')
            ->get();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'shift-codes')->first()->ma_title ?? 'Shift Codes',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.updated_shift_code.shift_code', compact('data', 'userTypes'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');

            $query = DB::select('
                SELECT 
                    sc.id,
                    sc.sc_code,
                    sc.sc_description,
                    sc.sc_shift_name,
                    sc.sc_start_time,
                    sc.sc_end_time,
                    sc.sc_type,
                    sc.sc_status,
                    GROUP_CONCAT(ut.ut_name ORDER BY ut.ut_name SEPARATOR ", ") as compatible_user_types
                FROM ts_shift_codes sc
                LEFT JOIN ts_shift_code_user_types scut ON sc.id = scut.shift_code_id
                LEFT JOIN ts_user_types ut ON scut.user_type_id = ut.id
                WHERE sc.sc_status != "deleted"
                ' . ($search ? 'AND (
                    sc.sc_code LIKE ? OR 
                    sc.sc_description LIKE ? OR 
                    sc.sc_shift_name LIKE ? OR 
                    sc.sc_type LIKE ? OR 
                    ut.ut_name LIKE ?
                )' : '') . '
                GROUP BY sc.id, sc.sc_code, sc.sc_description, sc.sc_shift_name, sc.sc_start_time, sc.sc_end_time, sc.sc_type, sc.sc_status
                ORDER BY sc.sc_code
            ', $search ? ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%'] : []);

            $allData = collect($query);
            $total = $allData->count();
            $totalPages = ceil($total / $perPage);

            $data = $allData->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->map(function ($row, $index) use ($page, $perPage) {
                    $row->DT_RowIndex = ($page - 1) * $perPage + $index + 1;
                    $row->sc_start_time_display = $row->sc_start_time ? date('H:i', strtotime($row->sc_start_time)) : '-';
                    $row->sc_end_time_display = $row->sc_end_time ? date('H:i', strtotime($row->sc_end_time)) : '-';
                    $row->sc_status_display = $row->sc_status === 'active' 
                        ? '<span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">Active</span>' 
                        : '<span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">Inactive</span>';
                    
                    $canRead = function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'read');
                    $canUpdate = function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'update');
                    $canDelete = function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'delete');
                    
                    $actionBtns = '<div class="flex items-center space-x-2">';
                    if ($canRead) {
                        $actionBtns .= '<button type="button" class="btn-view px-3 py-1 text-sm font-medium text-blue-600 bg-blue-100 rounded-md hover:bg-blue-200" data-id="' . $row->id . '">View</button>';
                    }
                    if ($canUpdate) {
                        $actionBtns .= '<button type="button" class="btn-edit px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-red-600" data-id="' . $row->id . '">Edit</button>';
                    }
                    if ($canDelete) {
                        $actionBtns .= '<button type="button" class="btn-delete px-3 py-1 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-700" data-id="' . $row->id . '">Delete</button>';
                    }
                    $actionBtns .= '</div>';
                    
                    $row->action = $actionBtns;
                    return $row;
                })
                ->values()
                ->all();

            return response()->json([
                'data' => $data,
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => (int) $page,
                'per_page' => (int) $perPage
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load data: ' . $e->getMessage()], 500);
        }
    }

    public function showForSimple($id)
    {
        $this->validateAccess('shift-codes');
        $shiftCode = ShiftCode::with('userTypes')->findOrFail($id);
        
        // Format the response
        $data = [
            'id' => $shiftCode->id,
            'sc_code' => $shiftCode->sc_code,
            'sc_description' => $shiftCode->sc_description,
            'sc_shift_name' => $shiftCode->sc_shift_name,
            'sc_start_time' => $shiftCode->sc_start_time ? $shiftCode->sc_start_time->format('H:i') : null,
            'sc_end_time' => $shiftCode->sc_end_time ? $shiftCode->sc_end_time->format('H:i') : null,
            'sc_type' => $shiftCode->sc_type,
            'sc_status' => $shiftCode->sc_status,
            'user_types' => $shiftCode->userTypes->map(function($ut) {
                return ['id' => $ut->id, 'ut_name' => $ut->ut_name];
            })->toArray()
        ];
        
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function storeForSimple(Request $request)
    {
        $this->validateAccess('shift-codes');

        // Get valid user types for validation
        $userTypes = DB::table('user_types')
            ->where('ut_status', 'active')
            ->pluck('id')
            ->toArray();

        // Handle user_type_ids - can be array or JSON string
        $userTypeIds = $request->input('user_type_ids', []);
        if (is_string($userTypeIds)) {
            $userTypeIds = json_decode($userTypeIds, true) ?: [];
        }
        if (!is_array($userTypeIds)) {
            $userTypeIds = [];
        }

        try {
            $request->merge(['user_type_ids' => $userTypeIds]);
            $request->validate([
                'sc_code' => 'required|string|max:10|unique:shift_codes,sc_code',
                'sc_description' => 'required|string|max:255',
                'sc_shift_name' => 'required|string|max:100',
                'sc_start_time' => 'nullable|date_format:H:i',
                'sc_end_time' => 'nullable|date_format:H:i',
                'user_type_ids' => 'required|array|min:1',
                'user_type_ids.*' => 'exists:user_types,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $data = [
            'sc_code' => strtoupper($request->sc_code),
            'sc_description' => $request->sc_description,
            'sc_shift_name' => $request->sc_shift_name,
            'sc_start_time' => $request->sc_start_time,
            'sc_end_time' => $request->sc_end_time,
            'sc_type' => 'MULTIPLE',
            'sc_status' => 'active',
            'created_by' => auth()->user()->u_name ?? 'system',
        ];

        $shiftCode = new ShiftCode();
        $result = $shiftCode->storeData('add', null, $data);

        if ($result) {
            $shiftCodeModel = ShiftCode::find($result);
            if ($shiftCodeModel) {
                $shiftCodeModel->userTypes()->attach($userTypeIds);
            }
            return response()->json(['success' => true, 'message' => 'Shift code berhasil ditambahkan']);
        } else {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan shift code'], 500);
        }
    }

    public function updateForSimple(Request $request, $id)
    {
        $this->validateAccess('shift-codes');

        // Get valid user types for validation
        $userTypes = DB::table('user_types')
            ->where('ut_status', 'active')
            ->pluck('id')
            ->toArray();

        // Handle user_type_ids - can be array or JSON string
        $userTypeIds = $request->input('user_type_ids', []);
        if (is_string($userTypeIds)) {
            $userTypeIds = json_decode($userTypeIds, true) ?: [];
        }
        if (!is_array($userTypeIds)) {
            $userTypeIds = [];
        }

        try {
            $request->merge(['user_type_ids' => $userTypeIds]);
            $request->validate([
                'sc_code' => 'required|string|max:10|unique:shift_codes,sc_code,' . $id,
                'sc_description' => 'required|string|max:255',
                'sc_shift_name' => 'required|string|max:100',
                'sc_start_time' => 'nullable|date_format:H:i',
                'sc_end_time' => 'nullable|date_format:H:i',
                'user_type_ids' => 'required|array|min:1',
                'user_type_ids.*' => 'exists:user_types,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $data = [
            'sc_code' => strtoupper($request->sc_code),
            'sc_description' => $request->sc_description,
            'sc_shift_name' => $request->sc_shift_name,
            'sc_start_time' => $request->sc_start_time,
            'sc_end_time' => $request->sc_end_time,
            'sc_type' => 'MULTIPLE',
            'updated_by' => auth()->user()->u_name ?? 'system',
        ];

        $shiftCode = new ShiftCode();
        $result = $shiftCode->storeData('edit', $id, $data);

        if ($result) {
            $shiftCodeModel = ShiftCode::find($id);
            if ($shiftCodeModel) {
                $userTypeIds = array_filter($userTypeIds, function ($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($userTypeIds)) {
                    $shiftCodeModel->userTypes()->sync($userTypeIds);
                }
            }
            return response()->json(['success' => true, 'message' => 'Shift code berhasil diperbarui']);
        } else {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui shift code'], 500);
        }
    }

    public function destroyForSimple($id)
    {
        $this->validateAccess('shift-codes');
        try {
            $shiftCode = new ShiftCode();
            $result = $shiftCode->deleteData($id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Shift code berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus shift code'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 