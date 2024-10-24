<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;
use Yajra\DataTables\DataTables; // Assuming you're using Yajra DataTables for Laravel
use App\Models\ProdukbarangPosV2; // Converted Model_barang to Laravel
use App\Models\ProdukKelolastokPosV2; // Converted Model_kelola_stok to Laravel
use App\Models\ProdukStokBarangPosV2; // Converted Model_stok_barang to Laravel
use App\Models\ProdukVarianPosV2;

class ProdukPosV2Controller extends Controller
{
    protected $barang;
    protected $stok_barang;
    protected $varian;
    protected $stok;

    public function __construct()
    {
        $this->barang = new ProdukbarangPosV2(); // Use Laravel model
        $this->stok_barang = new ProdukStokBarangPosV2(); // Use Laravel model
        $this->varian = new ProdukVarianPosV2(); // Use Laravel model
        $this->stok = new ProdukKelolastokPosV2(); // Use Laravel model
    }

    public function produk()
    {
        $id_toko = session('id_toko');
        $kategori = ProdukbarangPosV2::where('id_toko', $id_toko)
            ->where('status', 1)
            ->orderBy('nama_kategori', 'ASC')
            ->get();

        return view('app.posv2.masterdata.produk-posv2', [
            'menu'     => 'master',
            'submenu'  => 'produk',
            'title'    => 'Data Produk',
            'kategori' => $kategori,
        ]);
    }

    public function varian($id)
    {
        $id = base64_decode($id);
        $id_toko = session('id_toko');
        $produk = ProdukbarangPosV2::findOrFail($id);
        $cek = ProdukVarianPosV2::whereBetween('id_satuan', [12, 17])
            ->where('id_barang', $id)
            ->count();

        return view('app.posv2.masterdata.produkvarian-posv2', [
            'menu'     => 'master',
            'submenu'  => 'produk',
            'title'    => 'Varian',
            'produk'   => $produk,
            'cek'      => $cek,
        ]);
    }

    // public function datatable()
    // {
    //     $id_toko = session('id_toko');

    //     $builder = ProdukbarangPosV2::select('id', 'nama_barang', 'status', 'kelola_stok', 'foto', 'id_kategori')
    //         ->where('id_toko', $id_toko)
    //         ->orderBy('id', 'DESC');

    //     return DataTables::of($builder)
    //         ->addIndexColumn()
    //         ->editColumn('action', function ($row) {
    //             $disabled = $row->kelola_stok == 0 ? 'disabled' : '';
    //             return '
    //                 <button type="button" class="btn btn-light ' . $disabled . '" title="Kelola Stok" onclick="stok(\'' . $row->id . '\')" id="btnkel' . $row->id . '"><i class="fa fa-sitemap"></i></button>
    //                 <a href="' . route('produk.varian', base64_encode($row->id)) . '" class="btn btn-light" title="Varian"><i class="fas fa-th"></i></a>
    //                 <button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
    //                 <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama_barang . '\', \'' . $row->foto . '\')"><i class="fa fa-trash"></i></button>
    //             ';
    //         })
    //         ->editColumn('is_active', function ($row) {
    //             return '
    //                 <div class="form-switch">
    //                     <input type="checkbox" class="form-check-input" onclick="changeStatus(\'' . $row->id . '\');" id="set_active' . $row->id . '" ' . isChecked($row->status) . '>
    //                     <label class="form-check-label" for="set_active' . $row->id . '">' . isLabelChecked($row->status) . '</label>
    //                 </div>
    //             ';
    //         })
    //         ->editColumn('foto', function ($row) {
    //             $src = $row->foto ? asset('assets/img/barang/' . $row->foto) : asset('assets/img/noimage.png');
    //             return '<img data-fancybox data-src="' . $src . '" src="' . $src . '" height="70" width="80" style="cursor: zoom-in; border-radius: 5px;"/>';
    //         })
    //         ->editColumn('varian', function ($row) {
    //             $varianCount = ProdukVarianPosV2::where('id_barang', $row->id)->count();
    //             return '<span class="badge bg-success">Total varian: ' . $varianCount . '</span>';
    //         })
    //         ->editColumn('stok', function ($row) {
    //             return '
    //                 <div class="form-switch">
    //                     <input type="checkbox" class="form-check-input" onclick="changeStok(\'' . $row->id . '\');" id="set_active2' . $row->id . '" ' . isChecked($row->kelola_stok) . '>
    //                     <label class="form-check-label" for="set_active2' . $row->id . '">' . isLabelChecked($row->kelola_stok) . '</label>
    //                 </div>
    //             ';
    //         })
    //         ->toJson();
    // }

    // public function datatableVarian(Request $request)
    // {
    //     $builder = ProdukVarianPosV2::select('id', 'nama_varian', 'harga_jual', 'harga_modal', 'kelola_stok', 'status', 'created_at', 'id_satuan')
    //         ->with('satuan')
    //         ->orderBy('id', 'DESC');

    //     return DataTables::of($builder)
    //         ->filter(function ($query) use ($request) {
    //             if ($request->has('id_barang')) {
    //                 $query->where('id_barang', $request->id_barang);
    //             }
    //         })
    //         ->addIndexColumn()
    //         ->editColumn('action', function ($row) {
    //             $disabled = $row->kelola_stok == 0 ? 'disabled' : '';
    //             return '
    //                 <button type="button" class="btn btn-light ' . $disabled . '" title="Kelola Stok" onclick="stok(\'' . $row->id . '\')" id="btnkel' . $row->id . '"><i class="fa fa-sitemap"></i></button>
    //                 <button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
    //                 <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama_varian . '\')"><i class="fa fa-trash"></i></button>
    //             ';
    //         })
    //         ->editColumn('is_active', function ($row) {
    //             return '
    //                 <div class="form-switch">
    //                     <input type="checkbox" class="form-check-input" onclick="changeStatus(\'' . $row->id . '\');" id="set_active' . $row->id . '" ' . isChecked($row->status) . '>
    //                     <label class="form-check-label" for="set_active' . $row->id . '">' . isLabelChecked($row->status) . '</label>
    //                 </div>
    //             ';
    //         })
    //         ->editColumn('stok', function ($row) {
    //             return '
    //                 <div class="form-switch">
    //                     <input type="checkbox" class="form-check-input" onclick="changeStok(\'' . $row->id . '\');" id="set_active2' . $row->id . '" ' . isChecked($row->kelola_stok) . '>
    //                     <label class="form-check-label" for="set_active2' . $row->id . '">' . isLabelChecked($row->kelola_stok) . '</label>
    //                 </div>
    //             ';
    //         })
    //         ->editColumn('harga_jual', function ($row) {
    //             return 'Rp.' . number_format($row->harga_jual);
    //         })
    //         ->editColumn('harga_modal', function ($row) {
    //             return 'Rp.' . number_format($row->harga_modal);
    //         })
    //         ->editColumn('tgl', function ($row) {
    //             return $row->created_at->format('d-m-Y');
    //         })
    //         ->toJson();
    // }

    public function setStatus(Request $request)
    {
        $barang = ProdukbarangPosV2::find($request->input('id'));

        if (!$barang) {
            return response()->json([
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ]);
        }

        $barang->status = !$barang->status; // Toggle status
        $barang->save();

        return response()->json([
            'status' => true,
        ]);
    }

    public function setStokBarang(Request $request)
    {
        $barang = ProdukbarangPosV2::find($request->input('id'));

        if (!$barang) {
            return response()->json([
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ]);
        }

        $barang->kelola_stok = !$barang->kelola_stok; // Toggle kelola_stok
        $barang->save();

        return response()->json([
            'status' => true,
            'data' => ['kelola_stok' => $barang->kelola_stok],
            'id' => $request->input('id')
        ]);
    }

    public function setStatusVarian(Request $request)
    {
        $varian = ProdukVarianPosV2::find($request->input('id'));

        if (!$varian) {
            return response()->json([
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ]);
        }

        $varian->status = !$varian->status; // Toggle status
        $varian->save();

        return response()->json([
            'status' => true,
        ]);
    }

    public function setStok(Request $request)
    {
        $varian = ProdukVarianPosV2::find($request->input('id'));

        if (!$varian) {
            return response()->json([
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ]);
        }

        $varian->kelola_stok = !$varian->kelola_stok; // Toggle kelola_stok
        $varian->save();

        return response()->json([
            'status' => true,
            'data' => ['kelola_stok' => $varian->kelola_stok],
            'id' => $request->input('id')
        ]);
    }

    // public function getStok(Request $request)
    // {
    //     $id = $request->post('id');
    //     $stok = ProdukKelolastokPosV2::where('id_varian', $id)->get();
    //     $sales = DetailPenjualan::with('penjualan')
    //         ->where('id_varian', $id)
    //         ->get(['qty', 'penjualan.tgl']);
        
    //     $html = '';

    //     if ($stok->isNotEmpty()) {
    //         foreach ($stok as $key) {
    //             $date = \Carbon\Carbon::parse($key->tanggal)->format('d F Y, H:i');
    //             $typeText = $key->tipe == 1 ? 'Penambahan Stok' : 'Pengurangan Stok';
    //             $colorClass = $key->tipe == 1 ? 'text-success' : 'text-danger';
    //             $amount = $key->tipe == 1 ? '+' . $key->jumlah : '-' . $key->jumlah;

    //             $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
    //                         <div class="card-body">
    //                             ' . $typeText . ' &nbsp;<span class="' . $colorClass . '">' . $amount . '</span>
    //                             <br>
    //                             <small style="font-size: x-small;">' . $date . '</small>
    //                         </div>
    //                       </div>';
    //         }
    //     } else {
    //         $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;" id="nostok">
    //                         <div class="card-body">
    //                             <p class="text-center mb-0">Tidak ada riwayat pengaturan stok pada varian ini.</p>
    //                         </div>
    //                      </div>';
    //     }

    //     if ($sales->isNotEmpty()) {
    //         foreach ($sales as $key) {
    //             $date = \Carbon\Carbon::parse($key->penjualan->tgl)->format('d F Y, H:i');
    //             $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
    //                         <div class="card-body">
    //                             Penjualan &nbsp;<span class="text-danger">-' . $key->qty . '</span>
    //                             <br>
    //                             <small style="font-size: x-small;">' . $date . '</small>
    //                         </div>
    //                       </div>';
    //         }
    //     }

    //     $varianData = ProdukVarianPosV2::select('id', 'nama_varian', 'stok', 'stok_min')->where('id', $id)->first();

    //     $response = [
    //         'html' => $html,
    //         'data' => $varianData,
    //     ];

    //     return response()->json($response);
    // }

    // public function getStokBarang(Request $request)
    // {
    //     $id = $request->post('id');
    //     $stok = ProdukKelolastokPosV2::where('id_barang', $id)->get();
    //     $sales = DetailPenjualan::with('penjualan')
    //         ->where('id_barang', $id)
    //         ->get(['qty', 'penjualan.tgl']);
        
    //     $html = '';

    //     if ($stok->isNotEmpty()) {
    //         foreach ($stok as $key) {
    //             $date = \Carbon\Carbon::parse($key->tanggal)->format('d F Y, H:i');
    //             $typeText = $key->tipe == 1 ? 'Penambahan Stok' : 'Pengurangan Stok';
    //             $colorClass = $key->tipe == 1 ? 'text-success' : 'text-danger';
    //             $amount = $key->tipe == 1 ? '+' . $key->jumlah : '-' . $key->jumlah;

    //             $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
    //                         <div class="card-body">
    //                             ' . $typeText . ' &nbsp;<span class="' . $colorClass . '">' . $amount . '</span>
    //                             <br>
    //                             <small style="font-size: x-small;">' . $date . '</small>
    //                         </div>
    //                       </div>';
    //         }
    //     } else {
    //         $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;" id="nostok">
    //                         <div class="card-body">
    //                             <p class="text-center mb-0">Tidak ada riwayat pengaturan stok pada varian ini.</p>
    //                         </div>
    //                      </div>';
    //     }

    //     if ($sales->isNotEmpty()) {
    //         foreach ($sales as $key) {
    //             $date = \Carbon\Carbon::parse($key->penjualan->tgl)->format('d F Y, H:i');
    //             $html .= '<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
    //                         <div class="card-body">
    //                             Penjualan &nbsp;<span class="text-danger">-' . $key->qty . '</span>
    //                             <br>
    //                             <small style="font-size: x-small;">' . $date . '</small>
    //                         </div>
    //                       </div>';
    //         }
    //     }

    //     $barangData = ProdukbarangPosV2::select('id', 'nama_barang', 'stok', 'stok_min')->where('id', $id)->first();

    //     $response = [
    //         'html' => $html,
    //         'data' => $barangData,
    //     ];

    //     return response()->json($response);
    // }

    // public function getSatuan(Request $request)
    // {
    //     $id_toko = session('id_toko');
    //     $searchTerm = strtolower($request->get('q'));
    //     $data = \DB::table('satuan')
    //         ->where("LOWER(nama_satuan)", 'like', "%{$searchTerm}%")
    //         ->where("id_toko", $id_toko)
    //         ->where("status", 1)
    //         ->select('id as id', 'nama_satuan as text')
    //         ->orderBy('nama_satuan', 'ASC')
    //         ->get();

    //     return response()->json($data);
    // }

    public function updateStok(Request $request)
    {
        $request->validate([
            'tipe' => 'required',
            'jumlah' => 'required',
        ]);

        $id_varian = $request->post('id_varian');
        $tanggal = now();
        $tipe = $request->post('tipe');
        $jumlah = $request->post('jumlah');

        $data = [
            'id_varian' => $id_varian,
            'tanggal' => $tanggal,
            'jumlah' => $jumlah,
            'tipe' => $tipe,
        ];

        $save = ProdukKelolastokPosV2::create($data);

        if ($save) {
            $stok_varian = ProdukVarianPosV2::find($id_varian);
            $new_stok = $tipe == 1 ? $stok_varian->stok + $jumlah : $stok_varian->stok - $jumlah;

            $stok_varian->update(['stok' => $new_stok]);

            $response = [
                'status' => true,
                'data' => $data,
                'date' => $tanggal->format('d F Y, H:i'),
                'stok' => $new_stok,
            ];
        } else {
            $response = ['status' => false];
        }

        return response()->json($response);
    }

    public function updateStokBarang(Request $request)
    {
        $request->validate([
            'tipe' => 'required',
            'jumlah' => 'required',
        ]);

        $id_barang = $request->post('id_barang');
        $tanggal = now();
        $tipe = $request->post('tipe');
        $jumlah = $request->post('jumlah');

        $data = [
            'id_barang' => $id_barang,
            'tanggal' => $tanggal,
            'jumlah' => $jumlah,
            'tipe' => $tipe,
        ];

        $save = ProdukKelolastokPosV2::create($data);

        if ($save) {
            $stok_barang = ProdukbarangPosV2::find($id_barang);
            $new_stok = $tipe == 1 ? $stok_barang->stok + $jumlah : $stok_barang->stok - $jumlah;

            $stok_barang->update(['stok' => $new_stok]);

            $response = [
                'status' => true,
                'data' => $data,
                'date' => $tanggal->format('d F Y, H:i'),
                'stok' => $new_stok,
            ];
        } else {
            $response = ['status' => false];
        }

        return response()->json($response);
    }

    private function validation()
{
    $stokcheck = request()->input('stokcheck');

    $rules = [
        'harga' => [
            'label' => 'Harga jual',
            'rules' => 'required',
            'messages' => [
                'required' => '{attribute} harus diisi',
            ],
        ],
        'kategori' => [
            'label' => 'Kategori',
            'rules' => 'required',
            'messages' => [
                'required' => '{attribute} harus diisi',
            ],
        ],
        'modal' => [
            'label' => 'Harga modal',
            'rules' => 'required',
            'messages' => [
                'required' => '{attribute} harus diisi',
            ],
        ],
        'nama' => [
            'label' => 'Nama barang',
            'rules' => 'required',
            'messages' => [
                'required' => '{attribute} harus diisi',
            ],
        ],
    ];

    if ($stokcheck == 'on') {
        $rules['stok'] = [
            'label' => 'Stok saat ini',
            'rules' => 'required',
            'messages' => [
                'required' => '{attribute} harus diisi',
            ],
        ];
        $rules['stokmin'] = [
            'label' => 'Stok minimum',
            'rules' => 'required',
            'messages' => [
                'required' => '{attribute} harus diisi',
            ],
        ];
    }

    return $rules;
}

// public function simpan()
// {
//     $validator = Validator::make(request()->all(), $this->validation());

//     if ($validator->fails()) {
//         $errors = $validator->errors()->toArray();

//         $respond = [
//             'status' => false,
//             'errors' => [
//                 'nama' => $errors['nama'][0] ?? null,
//                 'kategori' => $errors['kategori'][0] ?? null,
//                 'modal' => $errors['modal'][0] ?? null,
//                 'harga' => $errors['harga'][0] ?? null,
//                 'stok' => $errors['stok'][0] ?? null,
//                 'stokmin' => $errors['stokmin'][0] ?? null,
//             ],
//         ];
//     } else {
//         $id = request()->input('id');
//         $id_toko = session('id_toko');
//         $nama = request()->input('nama');
//         $harga = request()->input('harga');
//         $modal = request()->input('modal');
//         $barcode = request()->input('barcode');
//         $kategori = request()->input('kategori');
//         $foto = request()->file('foto');
//         $stok = request()->input('stok');
//         $stokmin = request()->input('stokmin');
//         $stokcheck = request()->input('stokcheck');

//         $sc = ($stokcheck == 'on') ? 1 : 0;

//         $data = [
//             'id' => $id,
//             'id_toko' => $id_toko,
//             'id_kategori' => $kategori,
//             'nama_barang' => $nama,
//             'harga_jual' => getAmount($harga),
//             'harga_modal' => getAmount($modal),
//             'kelola_stok' => $sc,
//             'stok' => $stok,
//             'stok_min' => $stokmin,
//             'barcode' => $barcode,
//             'status' => !$id ? 1 : null,
//         ];

//         if ($foto && $foto->isValid()) {
//             $namafile = $foto->store('barang', 'public');
//             if ($id) {
//                 $oldFoto = DB::table('barang')->where('id', $id)->value('foto');
//                 $path = public_path('storage/' . $oldFoto);
//                 if (file_exists($path)) {
//                     @unlink($path);
//                 }
//             }
//             $data['foto'] = $namafile;
//         }

//         $save = ProdukbarangPosV2::updateOrCreate(['id' => $id], $data);

//         if ($save) {
//             $notif = $id ? "Data berhasil diperbaharui" : "Data berhasil ditambahkan";
//             $respond = [
//                 'status' => true,
//                 'notif' => $notif,
//             ];
//         } else {
//             $respond = [
//                 'status' => false,
//             ];
//         }
//     }
//     return response()->json($respond);
// }

private function validationVarian()
{
    $stokcheck = request()->input('stokcheck');

    if ($stokcheck == 'on') {
        $rules = [
            'harga' => [
                'label' => 'Harga jual',
                'rules' => 'required',
                'messages' => [
                    'required' => '{field} harus diisi',
                ],
            ],
            'modal' => [
                'label' => 'Harga modal',
                'rules' => 'required',
                'messages' => [
                    'required' => '{field} harus diisi',
                ],
            ],
            'satuan' => [
                'label' => 'Satuan',
                'rules' => 'required',
                'messages' => [
                    'required' => '{field} harus diisi',
                ],
            ],
            'stok' => [
                'label' => 'Stok saat ini',
                'rules' => 'required',
                'messages' => [
                    'required' => '{field} harus diisi',
                ],
            ],
            'stokmin' => [
                'label' => 'Stok minimum',
                'rules' => 'required',
                'messages' => [
                    'required' => '{field} harus diisi',
                ],
            ],
            'nama' => [
                'label' => 'Nama varian',
                'rules' => 'required',
                'messages' => [
                    'required' => '{field} harus diisi',
                ],
            ],
        ];
    } else {
        $rules = [
            'harga' => [
                'label' => 'Harga jual',
                'rules' => 'required',
                'messages' => [
                    'required' => '{field} harus diisi',
                ],
            ],
            'modal' => [
                'label' => 'Harga modal',
                'rules' => 'required',
                'messages' => [
                    'required' => '{field} harus diisi',
                ],
            ],
            'satuan' => [
                'label' => 'Satuan',
                'rules' => 'required',
                'messages' => [
                    'required' => '{field} harus diisi',
                ],
            ],
            'nama' => [
                'label' => 'Nama varian',
                'rules' => 'required',
                'messages' => [
                    'required' => '{field} harus diisi',
                ],
            ],
        ];
    }

    return $rules;
}

// public function simpanVarian()
// {
//     // Validasi
//     $validator = Validator::make(request()->all(), $this->validationVarian());

//     if ($validator->fails()) {
//         $errors = $validator->errors()->toArray();

//         $respond = [
//             'status' => false,
//             'errors' => $errors,
//         ];
//     } else {
//         $id         = request()->input('id');
//         $id_barang  = request()->input('id_barang');
//         $nama       = request()->input('nama');
//         $harga      = request()->input('harga');
//         $modal      = request()->input('modal');
//         $satuan     = request()->input('satuan');
//         $keterangan = request()->input('keterangan');
//         $stok       = request()->input('stok');
//         $stokmin    = request()->input('stokmin');
//         $stokcheck  = request()->input('stokcheck');

//         // Tentukan nilai kelola_stok
//         $sc = ($stokcheck == 'on') ? 1 : 0;

//         // Persiapkan data untuk disimpan
//         $data = [
//             'id'              => $id,
//             'id_barang'       => $id_barang,
//             'id_satuan'       => $satuan,
//             'nama_varian'     => $nama,
//             'harga_jual'      => getAmount($harga),
//             'harga_modal'     => getAmount($modal),
//             'keterangan'      => $keterangan,
//             'kelola_stok'     => $sc,
//             'stok'            => $stok,
//             'stok_min'        => $stokmin,
//             'status'          => $id ? null : 1, // Status hanya ditambahkan jika id tidak ada
//         ];

//         // Simpan data varian
//         $save = ProdukVarianPosV2::updateOrCreate(
//             ['id' => $id], // Jika id ada, update; jika tidak, buat baru
//             $data
//         );

//         if ($save) {
//             $notif = $id ? "Data berhasil diperbaharui" : "Data berhasil ditambahkan";
//             $respond = [
//                 'status' => true,
//                 'notif'  => $notif,
//             ];
//         } else {
//             $respond = [
//                 'status' => false,
//             ];
//         }
//     }

//     return response()->json($respond);
// }

public function getdata()
{
    $id = request()->input('id');

    // Mengambil data barang berdasarkan ID
    $data = ProdukbarangPosV2::find($id);

    if ($data) {
        $response = [
            'status' => true,
            'data'   => $data,
            'harga'  => 'Rp. ' . number_format($data->harga_jual, 0, ',', '.'),
            'modal'  => 'Rp. ' . number_format($data->harga_modal, 0, ',', '.'),
        ];
    } else {
        $response = [
            'status' => false,
        ];
    }

    return response()->json($response);
}

// public function getdataVarian()
// {
//     $id = request()->input('id');

//     // Mengambil data varian berdasarkan ID dengan join ke tabel satuan
//     $data = DB::table('varian as a')
//         ->select('a.*', 'b.nama_satuan')
//         ->join('satuan as b', 'b.id', '=', 'a.id_satuan')
//         ->where('a.id', $id)
//         ->first();

//     if ($data) {
//         $response = [
//             'status'  => true,
//             'data'    => $data,
//             'harga'   => 'Rp. ' . number_format($data->harga_jual, 0, ',', '.'),
//             'modal'   => 'Rp. ' . number_format($data->harga_modal, 0, ',', '.'),
//         ];
//     } else {
//         $response = [
//             'status' => false,
//         ];
//     }

//     return response()->json($response);
// }

public function hapus()
{
    $id = request()->input('id');
    $foto = request()->input('foto');

    try {
        // Menghapus data barang berdasarkan ID
        $delete = ProdukbarangPosV2::destroy($id);

        // Jika berhasil menghapus, hapus juga file gambar terkait
        if ($delete) {
            $path = public_path('assets/img/barang/');
            $filePath = $path . $foto;

            // Mengecek apakah file foto ada, jika ada hapus
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        return response()->json(['status' => true]);
    } catch (\Illuminate\Database\QueryException $e) {
        $errorMessage = $e->getMessage();

        // Memeriksa apakah ada kesalahan yang berkaitan dengan foreign key constraint
        if (strpos($errorMessage, 'foreign key constraint') !== false) {
            return response()->json(['status' => false, 'message' => 'Data terkait dengan data lain']);
        } else {
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan saat menghapus data']);
        }
    }
}

public function hapusVarian()
{
    $id = request()->input('id');

    try {
        // Menghapus data varian berdasarkan ID
        ProdukVarianPosV2::destroy($id);

        return response()->json(['status' => true]);
    } catch (\Illuminate\Database\QueryException $e) {
        $errorMessage = $e->getMessage();

        // Memeriksa apakah ada kesalahan yang berkaitan dengan foreign key constraint
        if (strpos($errorMessage, 'foreign key constraint') !== false) {
            return response()->json(['status' => false, 'message' => 'Data terkait dengan data lain']);
        } else {
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan saat menghapus data']);
        }
    }
}
 

}
