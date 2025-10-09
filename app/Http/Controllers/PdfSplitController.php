<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use Smalot\PdfParser\Parser;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

class PdfSplitController extends Controller
{
    public function index()
    {
        return view('pdf_import');
    }

    /**
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws FilterException
     */
    public function split(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:20480',
        ]);

        // Simpan file upload sementara
        $pdf = $request->file('pdf_file');
        $pdfPath = $pdf->storeAs('uploads', 'resi_semua.pdf');

        $fullPath = storage_path('app/' . $pdfPath);

        // Parsing teks untuk mendapatkan nomor resi
        $parser = new Parser();
        $pdfData = $parser->parseFile($fullPath);
        $text = $pdfData->getText();

        // Split PDF berdasarkan halaman
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fullPath);

        $outputDir = storage_path('app/public/split_resi/');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $results = [];
        for ($i = 1; $i <= $pageCount; $i++) {
            $pdf = new Fpdi();
            $pdf->AddPage();
            $pdf->setSourceFile($fullPath);
            $tplIdx = $pdf->importPage($i);
            $pdf->useTemplate($tplIdx, 0, 0, 210);

            // Ambil teks halaman ini
            $pageText = $this->getPageText($text, $i, $pageCount);

            // Cari nama file (berdasarkan No. Pesanan / Order Id / nomor panjang)
            $fileName = $this->extractFileName($pageText);

            $savePath = $outputDir . $fileName . '.pdf';
            $pdf->Output($savePath, 'F');

            $results[] = $fileName . '.pdf';
        }

        return response()->json([
            'status' => 'success',
            'message' => 'PDF berhasil di-split!',
            'files' => $results,
        ]);
    }

    private function getPageText($text, $pageNumber, $totalPages)
    {
        $pages = preg_split("/(?=Pengirim\s*:)/", $text);
        return $pages[$pageNumber - 1] ?? '';
    }

    private function extractFileName($pageText)
    {
        if (preg_match('/No\.?\s*Pesanan\s*[:\-]?\s*([A-Z0-9\-]+)/i', $pageText, $m)) {
            return trim($m[1]);
        }

        if (preg_match('/\b\d{15,20}\b/', $pageText, $m)) {
            return trim($m[0]);
        }

        return 'page_' . time();
    }
}