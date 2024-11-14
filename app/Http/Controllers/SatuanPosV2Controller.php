<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SatuanPosV2;
use DataTables;

class SatuanPosV2Controller extends Controller
{

    protected $satuan;

    public function __construct()
    {
        $this->satuan = new SatuanPosV2();
    }
    public function satuan()
    {
        $data_page = [
            'menu'    => 'master',
            'submenu' => 'satuan',
            'title'   => 'Satuan'
        ];

        return view('app.posv2.masterdata.satuan-posv2', $data_page);
    }

    public function datatable(Request $request)
    {
        $id_toko = $request->session()->get('id_toko');
        $builder = SatuanPosV2::where('id_toko', $id_toko)->orderBy('id', 'DESC');

        return DataTables::of($builder)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                return '<button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama_satuan . '\')"><i class="fa fa-trash"></i></button>';
            })
            ->addColumn('is_active', function($row) {
                return '<div class="form-switch">
                            <input type="checkbox" class="form-check-input" onclick="changeStatus(\'' . $row->id . '\');" id="set_active' . $row->id . '" ' . ($row->status ? 'checked' : '') . '>
                            <label class="form-check-label" for="set_active' . $row->id . '">' . ($row->status ? 'Active' : 'Inactive') . '</label>
                        </div>';
            })
            ->toJson();
    }

    public function setStatus(Request $request)
    {
        $satuan = SatuanPosV2::find($request->input('id'));

        if (!$satuan) {
            return response()->json(['status' => false, 'errors' => 'Data Tidak Ditemukan.']);
        }

        $satuan->status = $satuan->status ? 0 : 1;
        $satuan->save();

        return response()->json(['status' => true]);
    }

    public function simpan(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required'
        ], [
            'nama.required' => 'Nama Satuan harus diisi!'
        ]);

        $id = $request->input('id');
        $id_toko = $request->session()->get('id_toko');
        $nama = $request->input('nama');

        $data = [
            'id_toko' => $id_toko,
            'nama_satuan' => $nama,
            'status' => $id ? null : 1
        ];

        $satuan = SatuanPosV2::updateOrCreate(['id' => $id], $data);

        if ($satuan) {
            $notif = $id ? "Data berhasil diperbaharui" : "Data berhasil ditambahkan";
            return response()->json(['status' => true, 'notif' => $notif]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function getData(Request $request)
    {
        $id = $request->input('id');
        $satuan = SatuanPosV2::find($id);

        if ($satuan) {
            return response()->json(['status' => true, 'data' => $satuan]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function hapus(Request $request)
    {
        $id = $request->input('id');
        try {
            SatuanPosV2::destroy($id);
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }
}
