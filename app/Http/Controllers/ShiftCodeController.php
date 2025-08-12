<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ShiftCode;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use Log;
use Yajra\DataTables\Facades\DataTables;

class ShiftCodeController extends Controller
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
        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $shiftTypes = ['ALL', 'Full Time', 'Part Full', 'Part Time'];

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        return view('app.shift_code.create', compact('shiftTypes', 'data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sc_code' => 'required|string|max:10|unique:shift_codes,sc_code',
            'sc_description' => 'required|string|max:255',
            'sc_shift_name' => 'required|string|max:100',
            'sc_start_time' => 'nullable|date_format:H:i',
            'sc_end_time' => 'nullable|date_format:H:i',
            'sc_type' => 'required|in:ALL,Full Time,Part Full,Part Time',
        ]);

        $data = [
            'sc_code' => strtoupper($request->sc_code),
            'sc_description' => $request->sc_description,
            'sc_shift_name' => $request->sc_shift_name,
            'sc_start_time' => $request->sc_start_time,
            'sc_end_time' => $request->sc_end_time,
            'sc_type' => $request->sc_type,
            'sc_status' => 'active',
            'created_by' => auth()->user()->u_name ?? 'system',
        ];

        $shiftCode = new ShiftCode();
        $result = $shiftCode->storeData('add', null, $data);

        if ($result) {
            return redirect()->route('shift-codes.index')->with('success', 'Shift code berhasil ditambahkan');
        } else {
            return back()->with('error', 'Gagal menambahkan shift code');
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
        $shiftCode = ShiftCode::with(['dailySchedules' => function($query) {
            $query->whereNotNull('ds_status')->orderBy('ds_date', 'desc');
        }, 'dailySchedules.user'])->findOrFail($id);

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
        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $shiftCode = ShiftCode::findOrFail($id);
        $shiftTypes = ['ALL', 'Full Time', 'Part Full', 'Part Time'];

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];
        return view('app.shift_code.edit', compact('shiftCode', 'shiftTypes', 'data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'sc_code' => 'required|string|max:10|unique:shift_codes,sc_code,' . $id,
            'sc_description' => 'required|string|max:255',
            'sc_shift_name' => 'required|string|max:100',
            'sc_start_time' => 'nullable|date_format:H:i',
            'sc_end_time' => 'nullable|date_format:H:i',
            'sc_type' => 'required|in:ALL,Full Time,Part Full,Part Time',
        ]);

        $data = [
            'sc_code' => strtoupper($request->sc_code),
            'sc_description' => $request->sc_description,
            'sc_shift_name' => $request->sc_shift_name,
            'sc_start_time' => $request->sc_start_time,
            'sc_end_time' => $request->sc_end_time,
            'sc_type' => $request->sc_type,
            'updated_by' => auth()->user()->u_name ?? 'system',
        ];

        $shiftCode = new ShiftCode();
        $result = $shiftCode->storeData('edit', $id, $data);

        if ($result) {
            return redirect()->route('shift-codes.index')->with('success', 'Shift code berhasil diperbarui');
        } else {
            return back()->with('error', 'Gagal memperbarui shift code');
        }
    }

    public function destroy($id)
    {
        $shiftCode = new ShiftCode();
        $result = $shiftCode->deleteData($id);

        if ($result) {
            return redirect()->route('shift-codes.index')->with('success', 'Shift code berhasil dihapus');
        } else {
            return back()->with('error', 'Gagal menghapus shift code');
        }
    }

    public function getShiftCodesByType($type)
    {
        $this->validateAccess();
        
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
        
        if(request()->ajax()) {
        $query = DB::table('shift_codes')
            ->select([
                'id',
                'sc_code',
                'sc_description',
                'sc_shift_name',
                'sc_start_time',
                'sc_end_time',
                'sc_type',
                'sc_status'
            ])
            ->where('sc_status', '!=', 'deleted');
            
            // Add search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('sc_code', 'like', '%' . $search . '%')
                      ->orWhere('sc_description', 'like', '%' . $search . '%')
                      ->orWhere('sc_shift_name', 'like', '%' . $search . '%')
                      ->orWhere('sc_type', 'like', '%' . $search . '%');
                });
            }

            $result = DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<a href="'.route('shift-codes.show', $row->id).'" class="btn btn-info btn-sm" title="View"><i class="ki-outline ki-eye"></i></a>';
                    $btn .= '<a href="'.route('shift-codes.edit', $row->id).'" class="btn btn-warning btn-sm" title="Edit"><i class="ki-outline ki-notepad-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteShiftCode('.$row->id.')" title="Delete"><i class="ki-outline ki-trash-square"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->editColumn('sc_start_time', function($row) {
                    return $row->sc_start_time ? date('H:i', strtotime($row->sc_start_time)) : '-';
                })
                ->editColumn('sc_end_time', function($row) {
                    return $row->sc_end_time ? date('H:i', strtotime($row->sc_end_time)) : '-';
                })
                ->editColumn('sc_status', function($row) {
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
} 