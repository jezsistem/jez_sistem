<?php

namespace App\Http\Controllers;

use App\Models\DataUserPOSV2;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DataUserPOSV2Controller extends Controller
{
    public function __construct()
    {
        $this->user = new DataUserPOSV2();
    }

    public function datauser()
    {
        // Dummy data for app menu
        $app_menu = [
            (object) ['id' => 1, 'nama_menu' => 'Menu 1'],
            (object) ['id' => 2, 'nama_menu' => 'Menu 2'],
            // Add more dummy menu items as needed
        ];

        $data_page = [
            'menu'     => 'user',
            'submenu'  => 'user',
            'title'    => 'Data User',
            'app_menu' => $app_menu
        ];

        return view('app.posv2.user.datauser-posv2', $data_page);
    }

    public function datatable()
    {
        // Dummy data for users
        $dummyUsers = [
            (object) ['id' => 1, 'id_toko' => 1, 'nama' => 'User 1', 'status' => 1],
            (object) ['id' => 2, 'id_toko' => 1, 'nama' => 'User 2', 'status' => 0],
            // Add more dummy users as needed
        ];

        return DataTables::of($dummyUsers)
            ->addIndexColumn()
            ->editColumn('action', function ($row) {
                return '
                    <button type="button" class="btn btn-light" title="Edit Akses Menu" onclick="aksesMenu(\'' . $row->id . '\')"><i class="fas fa-tasks"></i></button>
                    <button type="button" class="btn btn-light" title="Edit Data" onclick="edit(\'' . $row->id . '\')"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-light" title="Hapus Data" onclick="hapus(\'' . $row->id . '\', \'' . $row->nama . '\')"><i class="fa fa-trash"></i></button>';
            })
            ->editColumn('is_active', function ($row) {
                return '<div class="form-switch">
                            <input type="checkbox" class="form-check-input"  onclick="changeStatus(\'' . $row->id . '\');" id="set_active' . $row->id . '" ' . ($row->status ? 'checked' : '') . '>
                            <label class="form-check-label" for="set_active' . $row->id . '">' . ($row->status ? 'Active' : 'Inactive') . '</label>
                        </div>';
            })
            ->toJson(true);
    }
    
    // Other methods remain unchanged
}
