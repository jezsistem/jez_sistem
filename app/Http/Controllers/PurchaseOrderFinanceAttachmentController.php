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
            ->get();

        return datatables()->of($attachments)
            ->editColumn('file_path', function ($attachment) {
                return '<a href="' . Storage::disk('s3')->url($attachment->file_path) . '" target="_blank">View Attachment</a>';
            })
            ->addColumn('action', function ($attachment) {
                return '
                    <button class="btn btn-sm btn-primary edit-finance-attachment" data-id="' . $attachment->id . '">Edit</button>
                    <button class="btn btn-sm btn-danger delete-finance-attachment" data-id="' . $attachment->id . '">Delete</button>
                ';
            })
            ->make(true);
    }

    public function uploadFinanceAttachment(Request $request)
    {
        $request->validate([
            'po_id_finance_attachment' => 'required|exists:purchase_orders,id',
            'finance_attachment_file' => 'required|file|max:10240', // Max 10MB
            'description' => 'nullable|string|max:1000',
        ]);

        $bucketName = config('filesystems.disks.s3.bucket');

        // Handle finance attachment upload
        if ($request->hasFile('finance_attachment_file')) {
            $poId = $request->input('po_id_finance_attachment');
            $file = $request->file('finance_attachment_file');
            $fileName = 'finance_attachment_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'purchase_order/' . $poId . '/' . $fileName;

            // Store file on S3
            $storedPath = $file->storeAs($filePath, $fileName, 's3', 'public');

            $attachment = PurchaseOrderFinanceAttachment::create([
                'po_id' => $poId,
                'file_path' => $bucketName . '/' . $storedPath,
                'description' => $request->input('description'),
            ]);
        }

        return response()->json(['success' => true, 'attachment' => $attachment]);
    }
}
