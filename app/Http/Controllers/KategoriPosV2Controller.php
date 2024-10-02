<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriPosV2; // Assuming the model is KategoriPosV2
use DataTables;
use DB;

class KategoriPosV2Controller extends Controller
{
    protected $kategori;

    public function __construct()
    {
        $this->kategori = new KategoriPosV2();
    }

    public function kategori()
    {
        $data_page = [
            'menu'    => 'master',
            'submenu' => 'kategori',
            'title'   => 'Kategori'
        ];

        return view('app.posv2.masterdata.kategori-posv2', $data_page);
    }

    public function datatable(Request $request)
    {
        $id_toko = session('id_toko');

        $builder = DB::table('kategori')->where('id_toko', $id_toko)->orderBy('id', 'DESC');

        return DataTables::of($builder)
            ->addIndexColumn()
            ->filterColumn('nama_kategori', function($query, $keyword) {
                $query->whereRaw('LOWER(nama_kategori) like ?', ["%{$keyword}%"]);
            })
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-light" title="Edit Data" onclick="edit(' . $row->id . ')"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(' . $row->id . ', \'' . $row->nama_kategori . '\')"><i class="fa fa-trash"></i></button>';
            })
            ->addColumn('is_active', function ($row) {
                return '<div class="form-switch">
                            <input type="checkbox" class="form-check-input"  onclick="changeStatus(' . $row->id . ');" id="set_active' . $row->id . '" ' . isChecked($row->status) . '>
                            <label class="form-check-label" for="set_active' . $row->id . '">' . isLabelChecked($row->status) . '</label>
                        </div>';
            })
            ->toJson();
    }

    public function setStatus(Request $request)
    {
        $builder = DB::table('kategori');

        $getData = $builder->where('id', $request->post('id'))->first();

        if (!$getData) {
            $response = [
                'status' => false,
                'errors' => 'Data Tidak Ditemukan.'
            ];
        } else {
            $this->kategori->where('id', $request->post('id'))->update(['status' => ($getData->status) ? 0 : 1]);
            $response = ['status' => true];
        }

        return response()->json($response);
    }

    public function simpan(Request $request)
    {
        $rules = $request->validate([
            'nama' => 'required',
        ], [
            'nama.required' => 'Nama kategori harus diisi!',
        ]);

        if (!$rules) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'nama' => $errors->first('nama')
                ]
            ]);
        } else {
            $id = $request->post('id');
            $id_toko = session('id_toko');
            $nama = $request->post('nama');

            $data = [
                'id'              => $id,
                'id_toko'         => $id_toko,
                'nama_kategori'   => $nama,
                'status'          => 1
            ];

            $save = $this->kategori->updateOrCreate(['id' => $id], $data);

            $notif = $id ? 'Data berhasil diperbaharui' : 'Data berhasil ditambahkan';

            return response()->json([
                'status' => true,
                'notif'  => $notif
            ]);
        }
    }

    public function getdata(Request $request)
    {
        $id = $request->post('id');

        $data = DB::table('kategori')->where('id', $id)->first();

        if ($data) {
            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function hapus(Request $request)
    {
        $id = $request->post('id');

        try {
            $this->kategori->findOrFail($id)->delete();

            return response()->json(['status' => true]);
        } catch (\Illuminate\Database\QueryException $e) {
            $errorMessage = $e->getMessage();

            if (strpos($errorMessage, 'foreign key constraint') !== false) {
                return response()->json(['status' => false]);
            } else {
                return response()->json(['status' => false]);
            }
        }
    }
}
    
