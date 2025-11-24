<?php

namespace App\Http\Controllers;

use App\Models\ModalLockConfig;
use Illuminate\Http\Request;

class ModalLockConfigController extends Controller
{
    public function getConfigs()
    {
        $configs = ModalLockConfig::all();
        return response()->json($configs);
    }

    public function storeConfig(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $config = ModalLockConfig::create([
            'name' => $request->name,
            'value' => $request->value,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Configuration saved successfully', 'config' => $config, 'status' => '200'], 201);
    }

    public function getConfigForEdit($id)
    {
        $config = ModalLockConfig::find($id);
        if (!$config) {
            return response()->json(['message' => 'Configuration not found', 'status' => '404'], 404);
        }
        return response()->json(['data' => $config, 'status' => '200'], 200);
    }

    public function updateConfig(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $config = ModalLockConfig::find($id);
        if (!$config) {
            return response()->json(['message' => 'Configuration not found', 'status' => '404'], 404);
        }

        $config->update([
            'name' => $request->name,
            'value' => $request->value,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Configuration updated successfully', 'config' => $config, 'status' => '200'], 200);
    }

    public function deleteConfig($id)
    {
        $config = ModalLockConfig::find($id);
        if (!$config) {
            return response()->json(['message' => 'Configuration not found', 'status' => '404'], 404);
        }

        $config->delete();

        return response()->json(['message' => 'Configuration deleted successfully', 'status' => '200'], 200);
    }

    public function getConfigDatatables()
    {
        if (request()->ajax()) {
            return datatables()->of(ModalLockConfig::select('modal_lock_configs.id', 'name', 'value', 'description')->orderBy('modal_lock_configs.id', 'desc'))
                
                ->addColumn('action', function ($data) {
                    $btn = ' <button class="btn btn-sm btn-warning edit-btn" id="edit_config" data-id="' . $data->id . '" onclick="editConfig(' . $data->id . ')"><i class="fa fa-edit"></i> Edit</button>';
                    $btn .= '<button class="btn btn-sm btn-danger delete-btn ml-3" id="delete_config" data-id="' . $data->id . '" onclick="deleteConfig(' . $data->id . ')"><i class="fa fa-trash"></i> Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }
}
