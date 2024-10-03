<?php

namespace App\Http\Controllers;

use App\Models\BahanBakuPosV2; // Pastikan Anda membuat model ini
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BahanBakuPosV2Controller extends Controller
{
    protected $bahan;

    public function __construct(BahanBakuPosV2 $bahan)
    {
        $this->bahan = $bahan;
    }

    public function bahan()
    {
        $data_page = [
            'menu'    => 'master',
            'submenu' => 'bahan-baku',
            'title'   => 'Bahan Baku'
        ];

        return view('app.posv2.bahanbaku-posv2', $data_page);
    }

    public function datatable()
    {
        $id_toko = session('id_toko');

        $builder = $this->bahan->where('id_toko', $id_toko)->orderBy('id', 'DESC');

        return DataTables::of($builder)
            ->addIndexColumn()
            ->setSearchable(['LOWER(nama_bahan)'])
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama_bahan . '\')"><i class="fa fa-trash"></i></button>';
            })
            ->addColumn('is_active', function ($row) {
                return '<div class="form-switch">
                            <input type="checkbox" class="form-check-input"  onclick="changeStatus(\'' . $row->id . '\');" id="set_active' . $row->id . '" ' . ($row->status ? 'checked' : '') . '>
                            <label class="form-check-label" for="set_active' . $row->id . '">' . ($row->status ? 'Aktif' : 'Tidak Aktif') . '</label>
                        </div>';
            })
            ->addColumn('harga', function ($row) {
                return 'Rp ' . number_format($row->harga);
            })
            ->make(true);
    }

    public function setStatus(Request $request)
    {
        $bahan = $this->bahan->find($request->input('id'));

        if (!$bahan) {
            return response()->json([
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ]);
        }

        $bahan->status = !$bahan->status;
        $bahan->save();

        return response()->json([
            'status' => true,
        ]);
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric',
        ], [
            'nama.required' => 'Nama kategori harus diisi!',
            'harga.required' => 'Harga harus diisi!',
            'harga.numeric' => 'Harga harus berupa angka!',
        ]);

        $id = $request->input('id');
        $id_toko = session('id_toko');
        $nama = $request->input('nama');
        $harga = $request->input('harga');

        $data = [
            'id_toko' => $id_toko,
            'nama_bahan' => $nama,
            'harga' => $harga,
            'status' => 1
        ];

        if ($id) {
            $this->bahan->where('id', $id)->update($data);
            $notif = "Data berhasil diperbaharui";
        } else {
            $this->bahan->create($data);
            $notif = "Data berhasil ditambahkan";
        }

        return response()->json([
            'status' => true,
            'notif' => $notif
        ]);
    }

    public function getdata(Request $request)
    {
        $id = $request->input('id');

        $data = $this->bahan->find($id);

        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'harga' => 'Rp. ' . number_format($data->harga, 0, ',', '.'),
            ]);
        }

        return response()->json(['status' => false]);
    }

    public function hapus(Request $request)
    {
        $id = $request->input('id');

        try {
            $this->bahan->destroy($id);
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }
}
