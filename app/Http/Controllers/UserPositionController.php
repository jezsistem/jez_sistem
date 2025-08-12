<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPosition;
use Illuminate\Support\Facades\DB;

class UserPositionController extends Controller
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
        
        $title = 'User Positions';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        
        $userPosition = new UserPosition();
        $positions = $userPosition->getActivePositions();

        $data = [
            'title' => $title,
            'subtitle' => 'User Positions',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_position.index', compact('positions', 'data'));
    }

    public function create()
    {
        $this->validateAccess();
        
        $title = 'Create User Position';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $data = [
            'title' => $title,
            'subtitle' => 'Create User Position',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_position.create', compact('data'));
    }

    public function store(Request $request)
    {
        $this->validateAccess();

        $request->validate([
            'up_code' => 'required|unique:user_positions,up_code',
            'up_name' => 'required|string|max:255',
            'up_description' => 'nullable|string',
            'up_level' => 'required|integer|min:1|max:10',
            'up_can_approve_leave' => 'boolean',
            'up_is_active' => 'boolean',
            'up_color' => 'required|string|max:7'
        ]);

        try {
            $userPosition = new UserPosition();
            
            // Filter out unwanted fields
            $data = $request->only([
                'up_code', 'up_name', 'up_description', 'up_level',
                'up_can_approve_leave', 'up_is_active', 'up_color'
            ]);
            
            $data['up_can_approve_leave'] = $request->input('up_can_approve_leave', 0) == 1;
            $data['up_is_active'] = $request->input('up_is_active', 0) == 1;

            \Log::info('Creating user position', $data);

            $result = $userPosition->storeData('add', null, $data);

            if ($result) {
                return redirect()->route('user-positions.index')->with('success', 'User position created successfully');
            } else {
                \Log::error('Failed to create user position');
                return back()->with('error', 'Failed to create user position')->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('Error creating user position: ' . $e->getMessage());
            return back()->with('error', 'Error creating user position: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $this->validateAccess();
        
        $title = 'User Position Detail';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $position = UserPosition::findOrFail($id);

        $data = [
            'title' => $title,
            'subtitle' => 'User Position Detail',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_position.show', compact('position', 'data'));
    }

    public function edit($id)
    {
        $this->validateAccess();
        
        $title = 'Edit User Position';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $position = UserPosition::findOrFail($id);

        $data = [
            'title' => $title,
            'subtitle' => 'Edit User Position',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_position.edit', compact('position', 'data'));
    }

    public function update(Request $request, $id)
    {
        $this->validateAccess();

        $request->validate([
            'up_code' => 'required|unique:user_positions,up_code,' . $id,
            'up_name' => 'required|string|max:255',
            'up_description' => 'nullable|string',
            'up_level' => 'required|integer|min:1|max:10',
            'up_can_approve_leave' => 'boolean',
            'up_is_active' => 'boolean',
            'up_color' => 'required|string|max:7'
        ]);

        $userPosition = new UserPosition();
        $data = $request->all();
        $data['up_can_approve_leave'] = $request->has('up_can_approve_leave');
        $data['up_is_active'] = $request->has('up_is_active');

        $result = $userPosition->storeData('edit', $id, $data);

        if ($result) {
            return redirect()->route('user-positions.index')->with('success', 'User position updated successfully');
        } else {
            return back()->with('error', 'Failed to update user position')->withInput();
        }
    }

    public function destroy($id)
    {
        $this->validateAccess();

        $userPosition = new UserPosition();
        $result = $userPosition->deleteData($id);

        if (request()->ajax()) {
            if ($result) {
                return response()->json(['success' => true, 'message' => 'User position deleted successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete user position'], 500);
            }
        } else {
            if ($result) {
                return redirect()->route('user-positions.index')->with('success', 'User position deleted successfully');
            } else {
                return back()->with('error', 'Failed to delete user position');
            }
        }
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            $query = DB::table('user_positions')
                ->select([
                    'id',
                    'up_code',
                    'up_name',
                    'up_description',
                    'up_level',
                    'up_can_approve_leave',
                    'up_is_active'
                ])
                ->where('up_is_active', '!=', 'deleted');
            
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('up_code', 'like', '%' . $search . '%')
                      ->orWhere('up_name', 'like', '%' . $search . '%')
                      ->orWhere('up_description', 'like', '%' . $search . '%');
                });
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('up_is_active', function($row) {
                    if ($row->up_is_active == 1) {
                        return '<span class="badge badge-success">Active</span>';
                    } else {
                        return '<span class="badge badge-danger">Inactive</span>';
                    }
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm">';
                    $btn .= '<a href="'.route('user-positions.show', $row->id).'" class="btn btn-info btn-xs" title="View"><i class="ki-outline ki-eye"></i></a>';
                    $btn .= '<a href="'.route('user-positions.edit', $row->id).'" class="btn btn-warning btn-xs" title="Edit"><i class="ki-outline ki-notepad-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-xs" onclick="deleteUserPosition('.$row->id.')" title="Delete"><i class="ki-outline ki-trash-square"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'up_is_active'])
                ->make(true);
        }
    }
}
