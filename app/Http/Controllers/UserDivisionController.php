<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDivisionController extends Controller
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
        
        $title = 'User Divisions';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        
        $divisions = DB::table('user_divisions')
            ->orderBy('ud_name')
            ->get();

        $data = [
            'title' => $title,
            'subtitle' => 'User Divisions',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_division.index', compact('divisions', 'data'));
    }

    public function create()
    {
        $this->validateAccess();
        
        $title = 'Create User Division';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $data = [
            'title' => $title,
            'subtitle' => 'Create User Division',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_division.create', compact('data'));
    }

    public function store(Request $request)
    {
        $this->validateAccess();

        $request->validate([
            'ud_code' => 'required|unique:user_divisions,ud_code',
            'ud_name' => 'required|string|max:255',
            'ud_description' => 'nullable|string',
            'ud_status' => 'required|in:active,inactive'
        ]);

        $data = [
            'ud_code' => strtoupper($request->ud_code),
            'ud_name' => $request->ud_name,
            'ud_description' => $request->ud_description,
            'ud_status' => $request->ud_status,
            'created_by' => auth()->user()->u_name ?? 'system',
            'updated_by' => auth()->user()->u_name ?? 'system',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = DB::table('user_divisions')->insert($data);

        if ($result) {
            return redirect()->route('user-divisions.index')->with('success', 'User division created successfully');
        } else {
            return back()->with('error', 'Failed to create user division')->withInput();
        }
    }

    public function show($id)
    {
        $this->validateAccess();
        
        $title = 'User Division Detail';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $division = DB::table('user_divisions')->where('id', $id)->first();

        if (!$division) {
            return redirect()->route('user-divisions.index')->with('error', 'Division not found');
        }

        $data = [
            'title' => $title,
            'subtitle' => 'User Division Detail',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_division.show', compact('division', 'data'));
    }

    public function edit($id)
    {
        $this->validateAccess();
        
        $title = 'Edit User Division';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $division = DB::table('user_divisions')->where('id', $id)->first();

        if (!$division) {
            return redirect()->route('user-divisions.index')->with('error', 'Division not found');
        }

        $data = [
            'title' => $title,
            'subtitle' => 'Edit User Division',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_division.edit', compact('division', 'data'));
    }

    public function update(Request $request, $id)
    {
        $this->validateAccess();

        $request->validate([
            'ud_code' => 'required|unique:user_divisions,ud_code,' . $id,
            'ud_name' => 'required|string|max:255',
            'ud_description' => 'nullable|string',
            'ud_status' => 'required|in:active,inactive'
        ]);

        $data = [
            'ud_code' => strtoupper($request->ud_code),
            'ud_name' => $request->ud_name,
            'ud_description' => $request->ud_description,
            'ud_status' => $request->ud_status,
            'updated_by' => auth()->user()->u_name ?? 'system',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = DB::table('user_divisions')->where('id', $id)->update($data);

        if ($result) {
            return redirect()->route('user-divisions.index')->with('success', 'User division updated successfully');
        } else {
            return back()->with('error', 'Failed to update user division')->withInput();
        }
    }

    public function destroy($id)
    {
        $this->validateAccess();

        $result = DB::table('user_divisions')->where('id', $id)->delete();

        if (request()->ajax()) {
            if ($result) {
                return response()->json(['success' => true, 'message' => 'User division deleted successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete user division'], 500);
            }
        } else {
            if ($result) {
                return redirect()->route('user-divisions.index')->with('success', 'User division deleted successfully');
            } else {
                return back()->with('error', 'Failed to delete user division');
            }
        }
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            $query = DB::table('user_divisions')
                ->select([
                    'id',
                    'ud_code',
                    'ud_name',
                    'ud_description',
                    'ud_status'
                ])
                ->where('ud_status', '!=', 'deleted');
            
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('ud_code', 'like', '%' . $search . '%')
                      ->orWhere('ud_name', 'like', '%' . $search . '%')
                      ->orWhere('ud_description', 'like', '%' . $search . '%');
                });
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('ud_status', function($row) {
                    if ($row->ud_status == 'active') {
                        return '<span class="badge badge-success">Active</span>';
                    } else {
                        return '<span class="badge badge-danger">Inactive</span>';
                    }
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
                    $btn .= '            <a href="'.route('user-divisions.show', $row->id).'" class="menu-link px-3">View</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';
                    
                    $btn .= '        <!--begin::Menu item-->';
                    $btn .= '        <div class="menu-item px-3">';
                    $btn .= '            <a href="'.route('user-divisions.edit', $row->id).'" class="menu-link px-3">Edit</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';
                    
                    $btn .= '        <!--begin::Menu item-->';
                    $btn .= '        <div class="menu-item px-3">';
                    $btn .= '            <a href="javascript:void(0)" onclick="deleteUserDivision('.$row->id.')" class="menu-link px-3 text-danger">Delete</a>';
                    $btn .= '        </div>';
                    $btn .= '        <!--end::Menu item-->';
                    $btn .= '    </div>';
                    $btn .= '    <!--end::Menu-->';
                    $btn .= '</div>';
                    
                    return $btn;
                })
                ->rawColumns(['action', 'ud_status'])
                ->make(true);
        }
    }
}
