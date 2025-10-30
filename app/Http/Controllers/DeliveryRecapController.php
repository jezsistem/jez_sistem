<?php

namespace App\Http\Controllers;

use App\Imports\DeliveryRecapImport;
use App\Models\DeliveryRecap;
use App\Models\DeliveryReceipt;
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
use Maatwebsite\Excel\Facades\Excel;
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

//as
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
        DB::beginTransaction();

        try {
            $request->validate([
                'courier_name' => 'required|string|max:255',
                'courier_phone' => 'nullable|string|max:20',
                'expeditions' => 'required',
                'import_file' => 'required|file|mimes:xlsx,xls,csv',
                'signature_pic' => 'required|string',
                'signature_kurir' => 'required|string',
            ]);

            // Pastikan folder signature ada
            if (!Storage::disk('public')->exists('signatures')) {
                Storage::disk('public')->makeDirectory('signatures');
            }

            // === Simpan tanda tangan penyerah (PIC) ===
            $signaturePicName = null;
            if ($request->signature_pic) {
                $signaturePicName = 'signature_pic_' . Str::random(10) . '.png';
                $dataPic = explode(',', $request->signature_pic);
                $decodedPic = base64_decode(end($dataPic));
                Storage::disk('public')->put('signatures/' . $signaturePicName, $decodedPic);
            }

            // === Simpan tanda tangan kurir ===
            $signatureCourierName = null;
            if ($request->signature_kurir) {
                $signatureCourierName = 'signature_courier_' . Str::random(10) . '.png';
                $dataCourier = explode(',', $request->signature_kurir);
                $decodedCourier = base64_decode(end($dataCourier));
                Storage::disk('public')->put('signatures/' . $signatureCourierName, $decodedCourier);
            }

            $store = DB::table('stores')->where('id', Auth::user()->st_id)->first();
            $storeDesc = strtoupper($store->st_code ?? '-');

            $storeCode = 'UNK'; // default jika tidak cocok
            $mapping = [
                'MALANG' => 'MLG',
                'SURABAYA' => 'SBY',
                'KEDIRI' => 'KDR',
                'JEMBER' => 'JBR',
                'SIDOARJO' => 'SDA',
                'SEMARANG' => 'SMG',
            ];

// cari singkatan berdasarkan deskripsi store
            foreach ($mapping as $desc => $code) {
                if (Str::contains($storeDesc, $desc)) {
                    $storeCode = $code;
                    break;
                }
            }

            $courier = DB::table('couriers')->where('id', $request->expeditions)->first();
            $courierCode = strtoupper(substr(preg_replace('/\s+/', '', $courier->cr_name ?? 'UNK'), 0, 3));

            $dateNow = now()->format('Ymd');

            $countToday = DB::table('delivery_recaps')
                    ->whereDate('created_at', now()->toDateString())
                    ->count() + 1;
            $sequence = str_pad($countToday, 3, '0', STR_PAD_LEFT);

            $manifestNumber = "MANIFEST/AMP-{$storeCode}/{$courierCode}/{$dateNow}/{$sequence}";

            $recap = DeliveryRecap::create([
                'manifest_number' => $manifestNumber,
                'courier_name' => $request->courier_name,
                'courier_phone' => $request->courier_phone,
                'expedition_id' => $request->expeditions,
                'signature_pic' => $signaturePicName,
                'signature_courier' => $signatureCourierName,
                'recap_date' => now(),
                'created_by' => auth()->id(),
            ]);

            // === Proses import file Excel ===
            $import = new DeliveryRecapImport();
            Excel::import($import, $request->file('import_file'));

            if (count($import->resis) === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak mengandung data resi yang valid.',
                ], 400);
            }

            $receipts = [];
            $invalidResi = [];

            foreach ($import->resis as $resi) {
                $transaction = OnlineTransactions::where('no_resi', $resi)->first();

                if (!$transaction) {
                    $invalidResi[] = [
                        'resi' => $resi,
                        'status' => 'Tidak ditemukan di database'
                    ];
                    continue;
                }

                if (strtoupper(trim($transaction->internal_order_status)) !== 'DONE ONLINE') {
                    $invalidResi[] = [
                        'resi' => $resi,
                        'status' => $transaction->internal_order_status ?? '-'
                    ];
                    continue;
                }

                $count_qty = OnlineTransactionDetails::where('order_number', $transaction->order_number)->count();

                $receipts[] = [
                    'dr_id' => $recap->id,
                    'resi' => $resi,
                    'marketplace_name' => $transaction->platform_name ?? '-',
                    'item_qty' => $count_qty ?? 0,
                    'city_destinations' => $transaction->city ?? '-',
                    'note' => $transaction->note ?? '-',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // === Jika ada resi invalid, rollback semua ===
            if (count($invalidResi) > 0) {
                DB::rollBack();

                // Hapus recap dan signature supaya tidak ada sisa data
                if ($signaturePicName) Storage::disk('public')->delete('signatures/' . $signaturePicName);
                if ($signatureCourierName) Storage::disk('public')->delete('signatures/' . $signatureCourierName);
                $recap->delete();

                // Buat tabel HTML untuk SweetAlert
                $htmlTable = '
                <table border="1" cellspacing="0" cellpadding="6" style="width:100%;border-collapse:collapse;text-align:left;">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th>No</th>
                            <th>No Resi</th>
                            <th>Status Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody>';
                foreach ($invalidResi as $i => $item) {
                    $htmlTable .= "<tr>
                    <td>".($i+1)."</td>
                    <td>{$item['resi']}</td>
                    <td>{$item['status']}</td>
                </tr>";
                }
                $htmlTable .= '</tbody></table>';

                return response()->json([
                    'success' => false,
                    'title' => 'Import Dibatalkan',
                    'message' => "Beberapa resi belum berstatus <b>DONE ONLINE</b>:<br><br>{$htmlTable}",
                    'list' => $invalidResi
                ], 400);
            }

            // === Insert semua data resi valid ===
            DeliveryReceipt::insert($receipts);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan!',
                'data' => $recap
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getData(Request $request)
    {
        $query = DB::table('delivery_recaps')
            ->leftJoin('delivery_receipts', 'delivery_receipts.dr_id', '=', 'delivery_recaps.id')
            ->leftJoin('couriers', 'couriers.id', '=', 'delivery_recaps.expedition_id')
            ->leftJoin('users', 'users.id', '=', 'delivery_recaps.created_by')
            ->select(
                'delivery_recaps.id',
//                'dr.document_number',
                'delivery_recaps.courier_name',
                'delivery_recaps.courier_phone',
                'couriers.cr_name',
                'users.u_name as pic',
                'delivery_recaps.created_at',
                DB::raw('COUNT(ts_delivery_receipts.resi) as qty_resi')
            )
            ->groupBy(
                'delivery_recaps.id',
//                'delivery_recaps.document_number',
                'delivery_recaps.courier_name',
                'delivery_recaps.courier_phone',
                'couriers.cr_name',
                'pic',
                'delivery_recaps.created_at'
            )
            ->orderByDesc('delivery_recaps.created_at')->get();

//        dd($query);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('qty_resi', function ($row) {
                return $row->delivery_receipts_count ?? 0;
            })
            ->addColumn('action', function ($row) {
                $url = route('manifest.print', $row->id);
                return '<button class="btn btn-sm btn-primary" onclick="window.open(\'' . $url . '\', \'_blank\')">
                            <i class="fas fa-print"></i> Print
                        </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function print($id)
    {
        $st_id = Auth::user()->st_id;

        $header = DB::table('delivery_recaps')
            ->leftJoin('couriers', 'couriers.id', '=', 'delivery_recaps.expedition_id')
            ->leftJoin('users', 'users.id', '=', 'delivery_recaps.created_by')
            ->select(
                'delivery_recaps.id',
                'delivery_recaps.recap_date',
                'delivery_recaps.courier_name',
                'delivery_recaps.courier_phone',
                'delivery_recaps.signature_pic',
                'delivery_recaps.signature_courier',
                'couriers.cr_name as expedition_name',
                'users.u_name as pic_name'
            )
            ->where('delivery_recaps.id', $id)
            ->first();

        if (!$header) {
            abort(404, 'Data manifest tidak ditemukan.');
        }

        $items = DB::table('delivery_receipts')
            ->select(
                'delivery_receipts.resi',
                'delivery_receipts.marketplace_name',
                'delivery_receipts.item_qty',
                'delivery_receipts.city_destinations'
            )
            ->where('delivery_receipts.dr_id', $id)
            ->get();

        if ($items->count() > 0) {
            foreach ($items as $item) {
                DB::table('online_transactions')
                    ->where('no_resi', $item->resi)
                    ->update(['internal_order_status' => 'DONE']);
            }
        }

        // Ubah hasil ke array untuk view
        $itemsArray = $items->map(function ($item) {
            return [
                'resi' => $item->resi,
                'marketplace_name' => $item->marketplace_name,
                'item_qty' => $item->item_qty,
                'city_destinations' => $item->city_destinations,
            ];
        })->toArray();

        $manifest_date = date('Y-m-d');
        $address = DB::table('stores')->where('id', $st_id)->first();
        $user = DB::table('users')->where('id', Auth::user()->id)->first();

        $signature_pic_url = $header->signature_pic
            ? asset('storage/signatures/' . $header->signature_pic)
            : null;

        $signature_courier_url = $header->signature_courier
            ? asset('storage/signatures/' . $header->signature_courier)
            : null;

        return view('app.helper_online.print_manifest', [
            'recap_id' => $header->id,
            'manifest_date' => $manifest_date,
            'pickup_address' => $address->st_address ?? '-',
            'store_name' => $address->st_name ?? '-',
            'pic_seller' => $user->u_name ?? '-',
            'pic_phone' => $user->u_phone ?? '-',
            'recap_date' => $header->recap_date
                ? \Carbon\Carbon::parse($header->recap_date)->format('d/m/Y H:i')
                : '-',
            'courier_name' => $header->courier_name,
            'courier_phone' => $header->courier_phone,
            'expedition_name' => $header->expedition_name ?? '-',
            'pic_name' => $header->pic_name ?? '-',
            'items' => $itemsArray,
            'signature_pic_url' => $signature_pic_url,
            'signature_courier_url' => $signature_courier_url,
        ]);
    }
}
