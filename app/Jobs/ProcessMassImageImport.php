<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMassImageImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $directories;

    /**
     * Buat job dengan data direktori hasil ekstraksi ZIP.
     *
     * @param  array  $directories
     */
    public function __construct($directories)
    {
        $this->directories = $directories;
    }

    /**
     * Jalankan proses upload ke NEO Object Storage (S3).
     */
    public function handle()
    {
        foreach ($this->directories as $dir) {
            $articleId = basename($dir);
            $product = Product::where('article_id', $articleId)->first();

            if (!$product) continue;

            $files = File::files($dir);

            foreach ($files as $file) {
                $fileName = $file->getFilename();
                $fileStream = fopen($file->getRealPath(), 'r');

                // Upload ke NEO Object Storage
                $path = "image_products/{$articleId}/{$fileName}";
                Storage::disk('s3')->put($path, $fileStream, 'public');
                fclose($fileStream);

                // Dapatkan URL publik
                $url = Storage::disk('s3')->url($path);

                $baseUrl = rtrim(config('filesystems.disks.s3.url'), '/');
                $bucket = env('AWS_BUCKET');

                $urlAccess = $baseUrl . '/' . 'jez-product-images' . '/' . $path;


                // Simpan ke database
                ProductImage::create([
                    'p_id' => $product->id,
                    'u_id' => Auth::user()->id,
                    'file_name' => $fileName,
                    'file_path' => $urlAccess,
                ]);
            }

            // Bersihkan folder sementara
            File::deleteDirectory($dir);
        }
    }
}
