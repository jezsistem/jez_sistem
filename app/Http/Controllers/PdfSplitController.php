<?php

namespace App\Http\Controllers;

use App\Models\SplitResiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

class PdfSplitController extends Controller
{
    public function split(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:20480', // maks 20MB
        ]);

        $file = $request->file('pdf_file');
        $originalName = $file->getClientOriginalName();
        $path = $file->storeAs('public/split_resi/original', $originalName);

        $pdfPath = storage_path('app/' . $path);
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $pages = $pdf->getPages();

        $outputDir = storage_path('app/public/split_resi/');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $savedFiles = [];

        foreach ($pages as $index => $page) {
            $text = $page->getText();

            // Cari Order Id, No. Pesanan, atau 15–20 digit angka
            preg_match('/No\.\s*Pesanan\s*[:\-]?\s*([A-Z0-9\-]+)/i', $text, $pesananMatch);
            preg_match('/\b\d{15,20}\b/', $text, $resiMatch);

            $fileName = $orderMatch[1] ?? $pesananMatch[1] ?? $resiMatch[0] ?? ('page_' . ($index + 1));
            $outputFile = $outputDir . $fileName . '.pdf';

            // Split halaman
            $fpdi = new Fpdi();
            $fpdi->AddPage();
            $fpdi->setSourceFile($pdfPath);
            $fpdi->useTemplate($fpdi->importPage($index + 1), 0, 0, 210, 297);
            $fpdi->Output($outputFile, 'F');

            // Update kolom files_resi di table online_transactions
            DB::table('online_transactions')
                ->where('no_resi', $fileName)
                ->update(['files_resi' => 'split_resi/' . $fileName . '.pdf']);

            // Simpan log upload
            SplitResiLog::create([
                'original_file' => $originalName,
                'split_file' => $fileName . '.pdf',
                'uploaded_by' => auth()->id(),
            ]);

            $savedFiles[] = $fileName;
        }

        return response()->json([
            'message' => 'Split berhasil',
            'files' => $savedFiles,
        ]);
    }

    public function getHistory()
    {
        $logs = SplitResiLog::latest()->take(50)->get();

        return view('app.online_transaction.history_upload', compact('logs'));
    }
}
