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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PublicHolidayController extends Controller
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

//        $shiftCode = new ShiftCode();

        $response = Http::get('https://api-harilibur.vercel.app/api');

        $holidays = collect($response->json())
            ->where('is_national_holiday', true)
            ->values();

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title ?? 'Public Holiday',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'holidays' => $holidays
        ];

        return view('app.public_holiday.index', compact( 'data'));
    }

    public function sync()
    {
        $this->validateAccess();

        // Ambil dari API Hari Libur Nasional
        $response = Http::get('https://api-harilibur.vercel.app/api');

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data hari libur'
            ], 500);
        }

        $holidays = collect($response->json())
            ->where('is_national_holiday', true);

        $inserted = 0;
        $updated = 0;

        foreach ($holidays as $holiday) {

            $date = Carbon::parse($holiday['holiday_date']);

            $data = [
                'holiday_date' => $date->format('Y-m-d'),
                'description' => $holiday['holiday_name'],
                'year' => $date->year,
                'is_national' => true,
                'source' => 'api',
            ];

            $ph = PublicHoliday::updateOrCreate(
                ['holiday_date' => $data['holiday_date']],
                $data
            );

            $ph->wasRecentlyCreated ? $inserted++ : $updated++;
        }

        return response()->json([
            'success' => true,
            'message' => 'Sync hari libur nasional berhasil',
            'inserted' => $inserted,
            'updated' => $updated,
        ]);
    }

    public function datatables(Request $request)
    {
        $query = PublicHoliday::query()
            ->select([
                'id',
                'holiday_date',
                'description',
            ])
            ->orderBy('holiday_date', 'asc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                <div class="dropdown">
                    <a href="#" data-kt-menu-trigger="click">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                    <div class="menu menu-sub-dropdown">
                        <div class="menu-item">
                            <a href="javascript:void(0)" 
                               class="menu-link text-danger"
                               onclick="deletePublicHoliday('.$row->id.')">
                               Delete
                            </a>
                        </div>
                    </div>
                </div>
            ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function destroy($id)
    {
        try {
            $holiday = PublicHoliday::findOrFail($id);
            $holiday->delete();

            return response()->json([
                'success' => true,
                'message' => 'Hari libur berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ], 500);
        }
    }

    public function calendar()
    {
        return PublicHoliday::select(
            'description as title',
            'holiday_date as start'
        )->get();
    }

    public function syncToLeaveBalances()
    {
        DB::beginTransaction();

        try {

            $year = now()->year;

            $totalPH = DB::table('public_holidays')
                ->whereYear('holiday_date', $year)
                ->count();

            $users = DB::table('users')
//                ->where('u_delete', false)
                ->select('id')
                ->get();

            foreach ($users as $user) {

                // cek existing leave balance
                $existing = DB::table('leave_balances')
                    ->where('user_id', $user->id)
                    ->where('lb_year', $year)
                    ->first();

                if ($existing) {

                    // UPDATE SAJA (PH ONLY)
                    $remaining = max(
                        0,
                        $totalPH - $existing->lb_ph_used
                    );

                    DB::table('leave_balances')
                        ->where('id', $existing->id)
                        ->update([
                            'lb_initial_ph'   => $totalPH,
                            'lb_ph_remaining' => $remaining,
                            'updated_at'      => now(),
                        ]);

                } else {

                    // INSERT BARU
                    DB::table('leave_balances')->insert([
                        'user_id'              => $user->id,
                        'leave_type_id'        => 1,
                        'lb_year'              => $year,
                        'lb_initial_balance'   => 0,
                        'lb_used_balance'      => 0,
                        'lb_remaining_balance' => 0,
                        'lb_initial_ph'        => $totalPH,
                        'lb_ph_used'           => 0,
                        'lb_ph_remaining'      => $totalPH,
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Leave balance berhasil disinkronkan'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}