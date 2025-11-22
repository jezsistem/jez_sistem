<?php

namespace App\Http\Controllers;

use App\Models\ModalLockAllowedModel;
use Illuminate\Http\Request;

class ModalLockAllowedModelController extends Controller
{
    public function getAllowedModels()
    {
        // Implementation for fetching allowed models
    }

    public function storeAllowedModel(Request $request)
    {
        $request->validate([
            'model_type' => 'required|string|max:255',
            'identifier' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $allowedModel = ModalLockAllowedModel::create([
            'model_type' => $request->model_type,
            'identifier' => $request->identifier,
            'is_active' => $request->is_active,
        ]);

        return response()->json(['message' => 'Allowed model saved successfully', 'allowed_model' => $allowedModel, 'status' => '200'], 201);
    }

    public function getAllowedModelForEdit($id)
    {
        $allowedModel = ModalLockAllowedModel::find($id);
        if (!$allowedModel) {
            return response()->json(['message' => 'Allowed model not found', 'status' => '404'], 404);
        }
        return response()->json(['data' => $allowedModel, 'status' => '200'], 200);
    }

    public function updateAllowedModel(Request $request, $id)
    {
        $request->validate([
            'model_type' => 'required|string|max:255',
            'identifier' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $allowedModel = ModalLockAllowedModel::find($id);
        if (!$allowedModel) {
            return response()->json(['message' => 'Allowed model not found', 'status' => '404'], 404);
        }

        $allowedModel->update([
            'model_type' => $request->model_type,
            'identifier' => $request->identifier,
            'is_active' => $request->is_active,
        ]);

        return response()->json(['message' => 'Allowed model updated successfully', 'allowed_model' => $allowedModel, 'status' => '200'], 200);
    }

    public function deleteAllowedModel($id)
    {
        $allowedModel = ModalLockAllowedModel::find($id);
        if (!$allowedModel) {
            return response()->json(['message' => 'Allowed model not found', 'status' => '404'], 404);
        }

        $allowedModel->delete();

        return response()->json(['message' => 'Allowed model deleted successfully', 'status' => '200'], 200);
    }

    public function getAllowedModelsDatatables()
    {
        if (request()->ajax()) {
            return datatables()->of(ModalLockAllowedModel::select('id', 'model_type', 'identifier', 'is_active')->orderBy('id', 'desc'))
                ->editColumn('is_active', function ($data) {
                    return $data->is_active ? 'Yes' : 'No';
                })
                ->addColumn('action', function ($data) {
                    $btn = '<button class="btn btn-sm btn-warning edit-btn" id="edit_allowed_model_btn" data-id="' . $data->id . '" onclick="editAllowedModel(' . $data->id . ')"><i class="fa fa-edit"></i> Edit</button> ';
                    $btn .= '<button class="btn btn-sm btn-danger delete-btn" id="delete_allowed_model_btn" data-id="' . $data->id . '" onclick="deleteAllowedModel(' . $data->id . ')"><i class="fa fa-trash"></i> Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }
}
