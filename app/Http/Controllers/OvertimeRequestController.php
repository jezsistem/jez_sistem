<?php

namespace App\Http\Controllers;

use App\Models\ExternalAssignmentRequest;
use App\Models\LeaveType;
use App\Models\OvertimeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OvertimeRequestController extends Controller
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

        $title = 'External Assignment Requests';
        $user = auth()->user();
        $user_data = DB::table('users')->where('id', $user->id)->first();


        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];

        return view('app.overtime.index', compact('data'));
    }

    public function create()
    {
        $title = 'External Assignment Requests';
        $user = auth()->user();
        $departments = DB::table('user_divisions')->pluck('ud_name');
        $users = DB::table('users')->pluck('u_name', 'id');
        $user_data = DB::table('users')->where('id', $user->id)->first();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];

        return view('app.overtime.create', compact('departments', 'users', 'data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'submission_date' => 'required|date',
            'department' => 'required|string',
            'assigned_staff' => 'required|array',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date',
            'end_time' => 'required',
            'details' => 'required|string',
            'attachment' => 'nullable|file|max:2048',
            'claim' => 'nullable|numeric',
        ]);

        $filePath = null;
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('attachments', 'public');
        }

        OvertimeRequest::create([
            'request_by' => auth()->id(),
            'submission_date' => $request->submission_date,
            'department' => $request->department,
            'assigned_staff' => $request->assigned_staff,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'details' => $request->details,
            'attachment' => $filePath,
            'claim' => $request->claim,
        ]);

        return response()->json(['message' => 'Overtime request submitted successfully!']);
    }

    // ⚡ Server-side DataTables
    public function getData(Request $request)
    {
        $data = OvertimeRequest::with('requester')->select('overtime_requests.*');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('request_by', fn($row) => $row->requester->name ?? '-')
            ->addColumn('assigned_staff', function($row) {
                return collect($row->assigned_staff)->map(fn($s) => "<span class='badge bg-info text-dark'>$s</span>")->implode(' ');
            })
            ->addColumn('attachment', function($row) {
                if ($row->attachment) {
                    $url = asset('storage/' . $row->attachment);
                    return "<a href='$url' target='_blank'>View</a>";
                }
                return '-';
            })
            ->rawColumns(['assigned_staff', 'attachment'])
            ->make(true);
    }
}