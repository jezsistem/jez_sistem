<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UserType;

class UserTypeController extends Controller
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
        
        $title = 'User Types';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();
        
        $userTypes = DB::table('user_types')
            ->orderBy('ut_name')
            ->get();

        $data = [
            'title' => $title,
            'subtitle' => 'User Types',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)
        ];

        return view('app.user_type.index', compact('userTypes', 'data'));
    }

    public function store(Request $request)
    {
        $this->validateAccess();

        $request->validate([
            'ut_code' => 'required|unique:user_types,ut_code',
            'ut_name' => 'required|string|max:255',
            'ut_description' => 'nullable|string',
            'ut_status' => 'required|in:active,inactive'
        ]);

        $data = [
            'ut_code' => strtoupper($request->ut_code),
            'ut_name' => $request->ut_name,
            'ut_description' => $request->ut_description,
            'ut_status' => $request->ut_status,
            'created_by' => auth()->user()->u_name ?? 'system',
            'updated_by' => auth()->user()->u_name ?? 'system',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = DB::table('user_types')->insert($data);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'User type created successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to create user type'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validateAccess();

        $request->validate([
            'ut_code' => 'required|unique:user_types,ut_code,' . $id,
            'ut_name' => 'required|string|max:255',
            'ut_description' => 'nullable|string',
            'ut_status' => 'required|in:active,inactive'
        ]);

        $data = [
            'ut_code' => strtoupper($request->ut_code),
            'ut_name' => $request->ut_name,
            'ut_description' => $request->ut_description,
            'ut_status' => $request->ut_status,
            'updated_by' => auth()->user()->u_name ?? 'system',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = DB::table('user_types')->where('id', $id)->update($data);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'User type updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to update user type'], 500);
        }
    }

    public function destroy($id)
    {
        $this->validateAccess();

        // Check if user type is being used
        $usersCount = DB::table('users')->where('ut_id', $id)->count();
        
        if ($usersCount > 0) {
            return response()->json([
                'success' => false, 
                'message' => "Cannot delete user type. It is being used by {$usersCount} users."
            ], 400);
        }

        $result = DB::table('user_types')->where('id', $id)->delete();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'User type deleted successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete user type'], 500);
        }
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            $query = DB::table('user_types')
                ->select([
                    'id',
                    'ut_code',
                    'ut_name',
                    'ut_description',
                    'ut_status'
                ])
                ->where('ut_status', '!=', 'deleted');
            
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('ut_code', 'like', '%' . $search . '%')
                      ->orWhere('ut_name', 'like', '%' . $search . '%')
                      ->orWhere('ut_description', 'like', '%' . $search . '%');
                });
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('ut_status', function($row) {
                    if ($row->ut_status == 'active') {
                        return '<span class="badge badge-success">Active</span>';
                    } else {
                        return '<span class="badge badge-danger">Inactive</span>';
                    }
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm">';
                    $btn .= '<button type="button" class="btn btn-warning btn-xs" onclick="editUserType('.$row->id.')" title="Edit"><i class="ki-outline ki-pencil"></i></button>';
                    $btn .= '<button type="button" class="btn btn-danger btn-xs" onclick="deleteUserType('.$row->id.')" title="Delete"><i class="ki-outline ki-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['ut_status', 'action'])
                ->make(true);
        }
    }
}
