<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrderFinanceAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderFinanceAttachmentController extends Controller
{
    public function getFinanceAttachmentDatatables(Request $request)
    {
        $po_id = $request->input('_po_id');

        // Fetch finance attachments based on the provided po_id
        $attachments = PurchaseOrderFinanceAttachment::where('po_id', $po_id)
            ->select('id', 'file_path', 'description', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return datatables()->of($attachments)
            ->addIndexColumn()
            ->editColumn('file_name', function ($attachment) {
                $url = Storage::disk('s3')->url($attachment->file_path);
                return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-info">View Attachment</a>';
            })
            ->editColumn('created_at', function ($attachment) {
                return $attachment->created_at->format('d-m-Y H:i:s');
            })
            ->addColumn('action', function ($attachment) {
                return '
                    <button class="btn btn-sm btn-danger delete-finance-attachment" data-id="' . $attachment->id . '" onclick="deleteFinanceAttachment(' . $attachment->id . ')">Delete</button>
                ';
            })
            ->rawColumns(['file_name', 'action'])
            ->make(true);
    }

    public function uploadFinanceAttachment(Request $request)
    {
        $request->validate([
            'po_id_finance_attachment' => 'required|exists:purchase_orders,id',
            'finance_attachment_file' => 'required|file|max:10240', // Max 10MB
            'finance_attachment_description' => 'nullable|string|max:1000',
        ]);

        $bucketName = config('filesystems.disks.s3.bucket');

        // Handle finance attachment upload
        if ($request->hasFile('finance_attachment_file')) {
            $poId = $request->input('po_id_finance_attachment');
            $file = $request->file('finance_attachment_file');
            $fileName = 'finance_attachment_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'purchase_order/' . $poId;

            // Store file on S3
            $storedPath = $file->storeAs($filePath, $fileName, 's3', 'public');

            $attachment = PurchaseOrderFinanceAttachment::create([
                'po_id' => $poId,
                'file_path' => $bucketName . '/' . $storedPath,
                'description' => $request->input('finance_attachment_description'),
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Finance attachment uploaded successfully'
        ]);
    }

    public function deleteFinanceAttachment(Request $request){
        $request->validate([
            'attachment_id' => 'required|exists:purchase_order_finance_attachment,id',
        ]);

        $attachment = PurchaseOrderFinanceAttachment::find($request->input('attachment_id'));

        if ($attachment) {
            // Delete file from S3
            $filePath = str_replace(config('filesystems.disks.s3.bucket') . '/', '', $attachment->file_path);
            Storage::disk('s3')->delete($filePath);

            // Delete record from database
            $attachment->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Finance attachment deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Finance attachment not found'
            ], 404);
        }
    }
}
