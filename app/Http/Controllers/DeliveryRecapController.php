<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRecap;
use App\Models\OnlineTransactionDetails;
use App\Models\OnlineTransactions;
use App\Models\Size;
use App\Models\TransaksiOnline;
use App\Models\User;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DeliveryRecapController extends Controller
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

    public function index()
    {
        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1)

        ];
        return view('app.delivery_reca  p.delivery_recap', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = Auth::user()->st_id;
        }
        if (request()->ajax()) {
            return DataTables::of(
                DeliveryRecap::select([
                    'delivery_recap.id as dr_id',
                    'dr_invoice',
                    'courier_name',
                    'expedition',
                    'note',
                    'users.u_name as user_name',
                    'delivery_recap.created_at'
                ])
                    ->leftjoin('users', 'delivery_recap.sender_id', '=', 'users.id')
                    ->where('delivery_recap.st_id', '=', $st_id)
                    ->orderBy('delivery_recap.created_at', 'DESC')
            )
                ->editColumn('dr_invoice', function ($data) {
                    return '<a class="text-white" href="#" data-dr_id="' . $data->dr_id . '"  data-dr_invoice="' . $data->dr_invoice . '" id="detail_btn"><span class="btn btn-sm btn-primary" >' . $data->dr_invoice . '</span></a><br>';
                })

                ->rawColumns(['dr_invoice'])
                ->addIndexColumn()
                ->make(true);
        }
    }


    public function add()
    {
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;

        $expeditions = DB::table('couriers')->orderBy('cr_name', 'ASC')->get();

        $data = [
            'title' => $title,
            'user' => $user_data,
            'expeditions' => $expeditions,
        ];
        return view('app.delivery_recap.add_delivery_recap', compact('data'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'courier_name' => 'required|string|max:255',
                'courier_phone' => 'nullable|string|max:20',
                'expeditions' => 'required',
                'import_file' => 'required|file|mimes:xlsx,xls,csv',
                'signature_pic' => 'required|string',
                'signature_kurir' => 'required|string',
            ]);

            // Pastikan folder storage/app/public/signatures ada
            if (!Storage::disk('public')->exists('signatures')) {
                Storage::disk('public')->makeDirectory('signatures');
            }

            // === Proses tanda tangan penyerah (PIC) ===
            $signaturePicName = null;
            if ($request->signature_pic) {
                $signaturePicName = 'signature_pic_' . Str::random(10) . '.png';
                $dataPic = explode(',', $request->signature_pic);
                $decodedPic = base64_decode(end($dataPic));
                Storage::disk('public')->put('signatures/' . $signaturePicName, $decodedPic);
            }

            // === Proses tanda tangan kurir ===
            $signatureCourierName = null;
            if ($request->signature_courier) {
                $signatureCourierName = 'signature_courier_' . Str::random(10) . '.png';
                $dataCourier = explode(',', $request->signature_courier);
                $decodedCourier = base64_decode(end($dataCourier));
                Storage::disk('public')->put('signatures/' . $signatureCourierName, $decodedCourier);
            }

            // === Simpan ke database ===
            $recap = DeliveryRecap::create([
                'courier_name' => $request->courier_name,
                'courier_phone' => $request->courier_phone,
                'expedition_id' => $request->courier_id,
                'signature_pic' => $signaturePicName,
                'signature_courier' => $signatureCourierName,
                'recap_date' => now(),
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan!',
                'data' => $recap
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
