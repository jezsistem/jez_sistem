<?php

namespace App\Http\Controllers;

use App\Exports\ProductArticleExport;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Product;
use App\Models\Size;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;
use App\Models\ProductLocationSetup;
use App\Models\ProductUnit;
use App\Models\ProductSupplier;
use App\Models\ProductStock;
use App\Models\Brand;
use Illuminate\Support\Facades\File;
use App\Models\MainColor;
use App\Models\Gender;
use App\Models\Season;
use App\Models\UserActivity;
use App\Imports\ProductImport;
use App\Exports\ProductExport;
use App\Imports\MassUpdateProductImport;
use App\Services\MassUpdateProductService;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\ProcessMassImageImport;
use Svg\Tag\Rect;
use ZipArchive;


class ProductController extends Controller
{

    protected function validateAccess()
    {
        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => request()->segment(1)
            ])->exists();
        if (!$validate) {
            dd("Anda tidak memiliki akses ke menu ini, hubungi Administrator");
        }
    }

    protected function sidebar()
    {
        $ma_id = DB::table('user_menu_accesses')->select('ma_id')
            ->where('u_id', Auth::user()->id)->get();
        $ma_id_arr = array();
        if (!empty($ma_id)) {
            foreach ($ma_id as $row) {
                array_push($ma_id_arr, $row->ma_id);
            }
        }

        $sidebar = array();
        $mt = DB::table('menu_titles')->orderBy('mt_sort')->get();
        if (!empty($mt->first())) {
            foreach ($mt as $row) {
                $ma = DB::table('menu_accesses')
                    ->where('mt_id', '=', $row->id)
                    ->whereIn('id', $ma_id_arr)
                    ->orderBy('ma_sort')->get();
                if (!empty($ma->first())) {
                    $row->ma = $ma;
                    array_push($sidebar, $row);
                }
            }
        }
        return $sidebar;
    }

    protected function UserActivity($u_id, $activity,$key_identifier)
    {
        if (!empty($u_id)) {
            UserActivity::create([
                'user_id' => $u_id,
                'ua_description' => $activity,
                'identifier' => 'data-products',
                'key_identifier' => $key_identifier,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function create()
    {
        // Daftar kolom yang ingin ditampilkan
        $columns = ['ms_best_seller', 'consignment', 'complement', 'mp_stock_masking'];

        // Kirim data ke view
        return view('app.product.product_modal', compact('columns'));
    }

//    protected function UserActivityLogin($u_id, $activity)
//    {
//        if (!empty($u_id)) {
//            UserActivity::create([
//                'user_id' => $u_id,
//                'ua_description' => $activity,
//                'identifier' => 'data-products',
//                'created_at' => date('Y-m-d H:i:s')
//            ]);
//        }
//    }

    public function index()
    {
        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;

        $stt = DB::table('store_types')->where('id', Auth::user()->stt_id)->first()->stt_name;



        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'total_product' => Product::where('p_delete', '!=', '1')->get()->count(),
            'total_footwear' => Product::where('p_delete', '!=', '1')->where('pc_id', '=', '1')->get()->count(),
            'total_apparel' => Product::where('p_delete', '!=', '1')->where('pc_id', '=', '2')->get()->count(),
            'total_accessories' => Product::where('p_delete', '!=', '1')->where('pc_id', '=', '3')->get()->count(),
            'total_others' => Product::where('p_delete', '!=', '1')->where('pc_id', '=', '4')->get()->count(),
            'pc_id' => ProductCategory::where('pc_delete', '!=', '1')->orderByDesc('id')->pluck('pc_name', 'id'),
            'br_id' => Brand::where('br_delete', '!=', '1')->orderBy('br_name', 'asc')->pluck('br_name', 'id'),
            'pu_id' => ProductUnit::where('pu_delete', '!=', '1')->orderByDesc('id')->pluck('pu_name', 'id'),
            'mc_id' => MainColor::where('mc_delete', '!=', '1')->orderBy('mc_name', 'asc')->pluck('mc_name', 'id'),
            'ps_id' => ProductSupplier::where('ps_delete', '!=', '1')->orderBy('ps_name', 'asc')->pluck('ps_name', 'id'),
            'gn_id' => Gender::where('gn_delete', '!=', '1')->orderByDesc('id')->pluck('gn_name', 'id'),
            'p_name' => Product::where('p_delete', '!=', '1')->orderByDesc('id')->pluck('p_name', 'id'),
            'ss_id' => Season::where('ss_delete', '!=', '1')->orderByDesc('id')->pluck('ss_name', 'id'),
            'sz_id' => Size::where('sz_delete', '!=', '1')->orderByDesc('id')->pluck('sz_name', 'id'),
            'stt'   => $stt,
            'sz_schema_id' => Size::where('sz_delete', '!=', '1')->whereNotNull('sz_schema')->orderByDesc('id')->distinct()->pluck('sz_schema'),
            'psc_id' => ProductSubCategory::where('psc_delete', '!=', '1')->orderByDesc('id')->pluck('psc_name', 'id'),
            'pssc_id' => ProductSubSubCategory::where('pssc_delete', '!=', '1')->orderByDesc('id')->pluck('pssc_name', 'id'),

        ];
        return view('app.product.product', compact('data'));
    }

//    public function getSkuAvailable(Request $request){
//
//    }

    public function updateFlag(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $column = $request->column;

        if (in_array($column, ['MP_best_seller', 'MP_stock_masking', 'Complement', 'Consignment'])) {
            $product->$column = !$product->$column; // Toggle antara 0 dan 1
            $product->save();
        }

        return response()->json(['success' => true, 'newValue' => $product->$column]);
    }

    //    public function massImportImg(Request $request)
    //    {
    //        $request->validate([
    //            'p_mass_import' => 'required|file|mimes:zip'
    //        ]);
    //
    //        $file = $request->file('p_mass_import');
    //        $fileName = time() . '_' . $file->getClientOriginalName();
    //        $zipPath = storage_path('app/uploads/' . $fileName);
    //        $file->move(storage_path('app/uploads'), $fileName);
    //
    //        $extractPath = storage_path('app/temp_import_' . time());
    //        File::makeDirectory($extractPath);
    //
    //        $zip = new \ZipArchive;
    //        if ($zip->open($zipPath) === true) {
    //            $zip->extractTo($extractPath);
    //            $zip->close();
    //        } else {
    //            return response()->json(['message' => 'Gagal membuka file ZIP.'], 422);
    //        }
    //
    //        $imported = 0;
    //        $directories = File::directories($extractPath);
    //
    //        foreach ($directories as $dir) {
    //            $articleId = basename($dir);
    //            $product = Product::where('article_id', $articleId)->first();
    //
    //            if (!$product) continue;
    //
    //            $files = File::files($dir);
    //
    //            // Pastikan folder tujuan ada: storage/app/public/image_products/{article_id}
    //            $targetDir = storage_path("app/public/image_products/{$articleId}");
    //            if (!File::exists($targetDir)) {
    //                File::makeDirectory($targetDir, 0755, true);
    //            }
    //
    //            foreach ($files as $file) {
    //                $fileName = $file->getFilename();
    //                $destinationPath = $targetDir . '/' . $fileName;
    //
    //                // Copy file
    //                File::copy($file->getRealPath(), $destinationPath);
    //
    //                // Simpan ke database
    //                \App\Models\ProductImage::create([
    //                    'p_id' => $product->id,
    //                    'file_name' => $fileName,
    //                    'file_path' => "storage/image_products/{$articleId}/{$fileName}",
    //                ]);
    //
    //                $imported++;
    //            }
    //        }
    //
    //        // Cleanup
    //        File::deleteDirectory($extractPath);
    //        File::delete($zipPath);
    //
    //        return response()->json([
    //            'message' => "Berhasil mengimpor {$imported} gambar produk."
    //        ]);
    //    }

    //    public function massImportImg(Request $request)
    //    {
    //        $request->validate([
    //            'p_mass_import' => 'required|file|mimes:zip'
    //        ]);
    //
    //        // Simpan file ZIP sementara
    //        $file = $request->file('p_mass_import');
    //        $fileName = time() . '_' . $file->getClientOriginalName();
    //        $zipPath = storage_path('app/uploads/' . $fileName);
    //        $file->move(storage_path('app/uploads'), $fileName);
    //
    //        // Ekstrak isi ZIP
    //        $extractPath = storage_path('app/temp_import_' . time());
    //        File::makeDirectory($extractPath);
    //
    //        $zip = new \ZipArchive;
    //        if ($zip->open($zipPath) === true) {
    //            $zip->extractTo($extractPath);
    //            $zip->close();
    //        } else {
    //            return response()->json(['message' => 'Gagal membuka file ZIP.'], 422);
    //        }
    //
    //        $imported = 0;
    //        $directories = File::directories($extractPath);
    //
    //        foreach ($directories as $dir) {
    //            $articleId = basename($dir);
    //            $product = Product::where('article_id', $articleId)->first();
    //
    //            if (!$product) continue;
    //
    //            $files = File::files($dir);
    //
    //            foreach ($files as $file) {
    //                $fileName = $file->getFilename();
    //                $fileStream = fopen($file->getRealPath(), 'r');
    //
    //                // Upload ke NEO Object Storage (S3)
    //                $path = "image_products/{$articleId}/{$fileName}";
    //                Storage::disk('s3')->put($path, $fileStream, 'public');
    //
    //                fclose($fileStream);
    //
    //                // Dapatkan URL publik
    //                $url = Storage::disk('s3')->url($path);
    //
    //                // Simpan ke database
    //                ProductImage::create([
    //                    'p_id' => $product->id,
    //                    'file_name' => $fileName,
    //                    'file_path' => $url,
    //                ]);
    //
    //                $imported++;
    //            }
    //        }
    //
    //        // Bersihkan file sementara
    //        File::deleteDirectory($extractPath);
    //        File::delete($zipPath);
    //
    //        return response()->json([
    //            'message' => "Berhasil mengimpor {$imported} gambar produk ke NEO Object Storage."
    //        ]);
    //    }

    public function massImportImg(Request $request)
    {
        $request->validate([
            'p_mass_import' => 'required|file|mimes:zip'
        ]);

        $file = $request->file('p_mass_import');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $zipPath = storage_path('app/uploads/' . $fileName);
        $file->move(storage_path('app/uploads'), $fileName);

        // Ekstrak ZIP ke folder sementara
        $extractPath = storage_path('app/temp_import_' . time());
        File::makeDirectory($extractPath);

        $zip = new \ZipArchive;
        if ($zip->open($zipPath) === true) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return response()->json(['message' => 'Gagal membuka file ZIP.'], 422);
        }

        $directories = File::directories($extractPath);

        ProcessMassImageImport::dispatch($directories);

        File::delete($zipPath);

        $this->UserActivity(Auth::user()->id, 'Melakukan Mass Image Import', null);

        return response()->json([
            'message' => 'File sedang diproses di background. Gambar akan diunggah ke NEO Object Storage.',
        ]);
    }

    public function getImages($articleId)
    {
        $product = Product::with('images')->where('article_id', $articleId)->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan']);
        }

        $images = $product->images->map(function ($img) {
            return [
                'id' => $img->id,
                'file_name' => $img->file_name,
                'file_path' => asset($img->file_path),
            ];
        });

        return response()->json([
            'success' => true,
            'images' => $images,
        ]);
    }

    public function marketplaceDataTables($articleId)
    {
        $product = DB::table('products')->where('article_id', $articleId)->get()->first();
        $product_id = (int)$product->id;

        $data = DB::table('product_links')
            ->select(
                'product_links.*',
                'created_by.u_name as created_by_name',
                'updated_by.u_name as updated_by_name'
            )
            ->leftJoin('users as created_by', 'created_by.id', '=', 'product_links.created_by')
            ->leftJoin('users as updated_by', 'updated_by.id', '=', 'product_links.updated_by')
            ->whereRaw('JSON_CONTAINS(product_id, ?)', json_encode($product_id))
            ->where('type', 'marketplace');

        return datatables()->of($data)
            ->addColumn('action', function ($row) use ($product) {
                return '
                <button class="btn btn-sm btn-info relatedColor" data-id="' . $row->id . '" data-article="' . $product->p_name . '" data-type="Marketplace" data-url="' . $row->url . '" data-location="' . $row->location . '" data-platform="' . $row->platform . '" data-created_by="' . $row->created_by_name . '" data-updated_by="' . $row->updated_by_name . '" data-created_at="' . $row->created_at . '" data-updated_at="' . $row->updated_at . '">Show Details</button>
                <button class="btn btn-sm btn-warning editLink" data-id="' . $row->id . '" data-type="marketplace">Edit</button>
                <button class="btn btn-sm btn-danger deleteLink" data-id="' . $row->id . '" data-type="marketplace">Delete</button>
            ';
            })
            ->make(true);
    }

    public function historyDataTables($articleId)
    {
        $p_id = DB::table('products')->where('article_id', $articleId)->pluck('id');
//        $product = DB::table('user_activities')->where('identifier', 'data-products')->where('key_identifier', $p_id)->get();
//        $product_id = (int)$product->id;

        $data = DB::table('user_activities')
            ->select(
                'users.u_name', 'user_activities.ua_description'
            )
            ->leftJoin('users', 'users.id', '=', 'user_activities.user_id')
            ->leftJoin('products', 'products.id', '=', 'user_activities.key_identifier')
            ->where('key_identifier', $p_id);

        return datatables()->of($data)->make(true);
    }

    public function socialDataTables($articleId)
    {
        $product = DB::table('products')->where('article_id', $articleId)->get()->first();
        $product_id = (int)$product->id;

        $data = DB::table('product_links')
            ->select(
                'product_links.*',
                'created_by.u_name as created_by_name',
                'updated_by.u_name as updated_by_name'
            )
            ->leftJoin('users as created_by', 'created_by.id', '=', 'product_links.created_by')
            ->leftJoin('users as updated_by', 'updated_by.id', '=', 'product_links.updated_by')
            ->whereRaw('JSON_CONTAINS(product_id, ?)', json_encode($product_id))
            ->where('type', 'social');

        return datatables()->of($data)
            ->addColumn('action', function ($row) use ($product) {
                return '
                <button class="btn btn-sm btn-info relatedColor" data-id="' . $row->id . '" data-article="' . $product->p_name . '" data-type="Social Media" data-url="' . $row->url . '" data-location="' . $row->location . '" data-platform="' . $row->platform . '" data-created_by="' . $row->created_by_name . '" data-updated_by="' . $row->updated_by_name . '" data-created_at="' . $row->created_at . '" data-updated_at="' . $row->updated_at . '">Show Details</button>
                <button class="btn btn-sm btn-warning editLink" data-id="' . $row->id . '" data-type="social">Edit</button>
                <button class="btn btn-sm btn-danger deleteLink" data-id="' . $row->id . '" data-type="social">Delete</button>
            ';
            })
            ->make(true);
    }

    public function LinkStore(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:marketplace,social',
            'platform'   => 'required|string',
            'url'        => 'required|string',
            'location'   => 'nullable|string',
        ]);

        $product_id = (int)DB::table('products')->where('article_id', $request->articleId)->value('id');

        // Check if a link already exists with the same product_id, type, platform, and location
        $existingLink = DB::table('product_links')
            ->where('type', $request->type)
            ->where('platform', $request->platform)
            ->where('location', $request->location)
            ->get()
            ->first(function ($link) use ($product_id) {
                $productIds = json_decode($link->product_id, true) ?? [];
                return in_array($product_id, $productIds);
            });

        if ($existingLink) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product link already exists with the same type, platform, and location.'
            ], 422);
        }

        $link_id = DB::table('product_links')->insertGetId([
            'product_id' => json_encode([$product_id]),
            'type'       => $request->type,
            'platform'   => $request->platform,
            'url'        => $request->url,
            'location'   => $request->location,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->UserActivity(Auth::user()->id, 'Menambahkan Product Link', $link_id);

        return response()->json(['status' => 'success']);
    }

    public function getLinks($id)
    {
        $data = DB::table('product_links')
            ->where('id', $id)
            ->first();

        if ($data && $data->product_id) {
            $data->product_id = json_decode($data->product_id);
        }

        return response()->json(['data' => $data]);
    }

    public function destroyLinks($id)
    {
        $link = DB::table('product_links')->where('id', $id)->first();

        if (!$link) {
            return response()->json(['success' => false, 'message' => 'Link tidak ditemukan']);
        }

        DB::table('product_links')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Link berhasil dihapus']);
    }

    public function updateLinks(Request $request, $id)
    {
        $request->validate([
            'type'       => 'required|in:marketplace,social',
            'platform'   => 'required|string',
            'url'        => 'required|string',
            'location'   => 'nullable|string',
        ]);

        // Get the current link being updated
        $currentLink = DB::table('product_links')->where('id', $id)->first();

        if (!$currentLink) {
            return response()->json([
                'status' => 'error',
                'message' => 'Link not found'
            ], 404);
        }

        $currentProductIds = json_decode($currentLink->product_id, true) ?? [];

        // Check if another link exists with the same product_id(s), type, platform, and location
        $existingLink = DB::table('product_links')
            ->where('id', '!=', $id)
            ->where('type', $request->type)
            ->where('platform', $request->platform)
            ->where('location', $request->location)
            ->get()
            ->first(function ($link) use ($currentProductIds) {
                $productIds = json_decode($link->product_id, true) ?? [];
                // Check if there's any overlap between product IDs
                return !empty(array_intersect($currentProductIds, $productIds));
            });

        if ($existingLink) {
            return response()->json([
                'status' => 'error',
                'message' => 'A link with the same product(s), type, platform, and location already exists.'
            ], 422);
        }

        DB::table('product_links')->where('id', $id)->update([
            'type'       => $request->type,
            'platform'   => $request->platform,
            'url'        => $request->url,
            'location'   => $request->location,
            'updated_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['status' => 'success']);
    }

    public function updateLinkContent(Request $request, $id)
    {
        $request->validate([
            'link_content' => 'nullable|string'
        ]);

        $p_id = DB::table('products')->where('article_id', $id)->value('id');

        $product = Product::findOrFail($p_id);
        $product->link_content = $request->link_content;
        $product->save();

        return response()->json(['success' => true]);
    }

    public function relatedDataTables($articleName, Request $request)
    {
        $product_link = DB::table('product_links')
            ->where('id', $request->link_id)
            ->first();

        $product_ids_in_link = json_decode($product_link->product_id, true) ?? [];

        // Get product IDs that should be excluded (already in other links with same platform, type, location)
        $excluded_product_ids = DB::table('product_links')
            ->where('id', '!=', $request->link_id)
            ->where('platform', $product_link->platform)
            ->where('type', $product_link->type)
            ->where('location', $product_link->location)
            ->get()
            ->flatMap(function ($link) {
                return json_decode($link->product_id, true) ?? [];
            })
            ->toArray();

        $products = DB::table('products')
            ->select('products.id', 'products.article_id', 'products.p_color', 'mc_name', 'products.p_active', 'products.p_name')
            ->join('main_colors', 'main_colors.id', '=', 'products.mc_id')
            ->where('p_name', $articleName)
            ->where('p_delete', '!=', '1')
            ->whereNotIn('products.id', $excluded_product_ids)
            ->orderBy('products.created_at', 'asc')
            ->get();

        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada produk terkait ditemukan'
            ]);
        }

        $html = '';

        foreach ($products as $product) {
            $isInLink = in_array((int)$product->id, $product_ids_in_link);
            $checked = $isInLink ? 'checked' : '';

            $html .= "<tr>
            <td>{$product->article_id}</td>
            <td>{$product->p_color} ({$product->mc_name})</td>
            <td class='text-center d-flex justify-content-center align-items-center'>
            <label class='switch switch-sm mb-0'>
            <input type='checkbox' {$checked} data-product_link_id='{$product_link->id}' data-product_id='{$product->id}' data-article_name='{$product->p_name}' class='toggle-color-status' id='related_toggle'>
            <span class='slider round'></span>
            </label>
            </td>
            </tr>";
        }

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function toggleRelatedProduct(Request $request)
    {
        $product_link = DB::table('product_links')
            ->where('id', $request->link_id)
            ->first();

        if (!$product_link) {
            return response()->json([
                'success' => false,
                'message' => 'Link produk tidak ditemukan'
            ]);
        }

        $product_ids = json_decode($product_link->product_id, true) ?? [];
        $product_id = (int)$request->product_id;

        if ($request->status) {
            // Add product to link if not already present
            if (!in_array($product_id, $product_ids)) {
                $product_ids[] = $product_id;
            }
        } else {
            // Remove product from link if present
            if (in_array($product_id, $product_ids)) {
                $product_ids = array_filter($product_ids, function ($id) use ($product_id) {
                    return (int)$id !== $product_id;
                });
            }
        }

        // Update database with JSON integer array
        DB::table('product_links')
            ->where('id', $request->link_id)
            ->update([
                'product_id' => json_encode(array_values($product_ids)),
                'updated_at' => now(),
                'updated_by' => auth()->id(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Status produk terkait berhasil diperbarui'
        ]);
    }

    public function destroyImages($id)
    {
        $image = ProductImage::find($id);

        if (!$image) {
            return response()->json(['success' => false, 'message' => 'Gambar tidak ditemukan']);
        }

        try {
            $baseUrl = rtrim(config('filesystems.disks.s3.url'), '/');

            $relativePath = str_replace($baseUrl . '/', '', $image->file_path);

            $bucket = config('filesystems.disks.s3.bucket');
            $relativePath = preg_replace("#^{$bucket}/#", '', $relativePath);

            if (Storage::disk('s3')->exists($relativePath)) {
                Storage::disk('s3')->delete($relativePath);
            }

            // Hapus record dari database
            $image->delete();

            return response()->json(['success' => true, 'message' => 'Gambar berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage()
            ]);
        }
    }

    public function downloadAll($articleId)
    {
        //        $images = ProductImage::where('article_id', $articleId)->get();

        $images = DB::table('product_images')
            ->join('products', 'products.id', '=', 'product_images.p_id')
            ->where('products.article_id', $articleId)
            ->get();

        if ($images->isEmpty()) {
            return back()->with('error', 'Tidak ada gambar untuk artikel ini.');
        }

        $zipFileName = 'images_' . $articleId . '_' . time() . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {

            $baseUrl = rtrim(config('filesystems.disks.s3.url'), '/');
            $bucket  = config('filesystems.disks.s3.bucket');

            foreach ($images as $img) {
                $relativePath = str_replace($baseUrl . '/', '', $img->file_path);

                $relativePath = preg_replace("#^{$bucket}/#", '', $relativePath);

                if (Storage::disk('s3')->exists($relativePath)) {
                    $fileContent = Storage::disk('s3')->get($relativePath);
                    $zip->addFromString($img->file_name, $fileContent);
                }
            }

            $zip->close();
        } else {
            return back()->with('error', 'Gagal membuat ZIP file.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function showFlags($id)
    {
        $product = Product::select('id', 'MP_best_seller', 'MP_stock_masking', 'Complement', 'Consignment')
            ->where('id', $id)
            ->where('p_delete', '!=', 1)
            ->first();

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        return view('app.product.product_modal', compact('product'));
    }


    // public function searchProduct(Request $request) {
    //     $search = $request->input('search');

    //     // Fetch products based on the search term
    //     $products = Product::where('name', 'LIKE', "%{$search}%")
    //                         ->limit(10) // Limit the number of results to reduce load
    //                         ->get();

    //     return response()->json($products->map(function($product) {
    //         return [
    //             'id' => $product->id,
    //             'name' => $product->name
    //         ];
    //     }));
    // }


    //    public function getDatatables(Request $request)
    //    {
    //        if(request()->ajax()) {
    //            if ($request->pc_id == 'all') {
    //                return datatables()->of(Product::select(
    //                'products.id as pid',
    //                'br_id', 'pc_id',
    //                'psc_id',
    //                'pssc_id',
    //                'mc_id', 'ps_id', 'pu_id', 'gn_id', 'ss_id', 'p_code', 'p_name', 'p_description', 'p_aging', 'p_color', 'mc_name', 'br_name', 'ps_name', 'p_price_tag', 'p_purchase_price', 'p_sell_price', 'p_weight', 'p_active')
    //                ->join('brands', 'brands.id', '=', 'products.br_id')
    //                ->join('main_colors', 'main_colors.id', '=', 'products.mc_id')
    //                ->join('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
    //                ->where('p_delete', '!=', '1'))
    //                ->editColumn('p_name_show', function($data){
    //                    return '<span style="white-space: nowrap;">'.$data->p_name.'</span>';
    //                })
    //                ->editColumn('p_color_show', function($data){
    //                    return '<span style="white-space: nowrap;">'.$data->p_color.' ('.$data->mc_name.')</span>';
    //                })
    //                ->editColumn('ps_name_show', function($data){
    //                    return '<span style="white-space: nowrap;">'.$data->ps_name.'</span>';
    //                })
    //                ->editColumn('p_price_tag_show', function($data){
    //                    return '<span class="float-right">'.number_format($data->p_price_tag,2,",",".").'</span>';
    //                })
    //                ->editColumn('p_purchase_price_show', function($data){
    //                    return '<span class="float-right">'.number_format($data->p_purchase_price,2,",",".").'</span>';
    //                })
    //                ->editColumn('p_sell_price_show', function($data){
    //                    return '<span class="float-right">'.number_format($data->p_sell_price,2,",",".").'</span>';
    //                })
    //                ->editColumn('p_detail', function($data){
    //                    return '<a id="product_detail_btn" data-id="'.$data->pid.'" style="white-space: nowrap;" class="btn btn-sm btn-primary" style>Detail</a>';
    //                })
    //                ->rawColumns(['p_name_show', 'ps_name_show', 'p_color_show', 'p_price_tag_show', 'p_purchase_price_show', 'p_sell_price_show', 'p_detail', 'p_description'])
    //                ->filter(function ($instance) use ($request) {
    //                    if (!empty($request->get('br_id_filter'))) {
    //                        $instance->where(function($w) use($request){
    //                            $br_id = $request->get('br_id_filter');
    //                            $w->orWhere('br_id', '=', $br_id);
    //                        });
    //                    }
    //                    if (!empty($request->get('ps_id_filter'))) {
    //                        $instance->where(function($w) use($request){
    //                            $ps_id = $request->get('ps_id_filter');
    //                            $w->orWhere('ps_id', '=', $ps_id);
    //                        });
    //                    }
    //                    if (!empty($request->get('mc_id_filter'))) {
    //                        $instance->where(function($w) use($request){
    //                            $mc_id = $request->get('mc_id_filter');
    //                            $w->orWhere('mc_id', '=', $mc_id);
    //                        });
    //                    }
    //                    if (!empty($request->get('sz_id_filter'))) {
    //                        $instance->join('product_stocks', 'product_stocks.p_id', '=', 'products.id')
    //                        ->where(function($w) use($request){
    //                            $sz_id = $request->get('sz_id_filter');
    //                            $w->orWhere('sz_id', '=', $sz_id);
    //                        });
    //                    }
    //                    if(!empty($request->get('p_active_filter'))) {
    //                        if($request->get('p_active_filter') == '1') {
    //                            $instance->where('p_active', '=', '1');
    //                        }
    //
    //                        if ($request->get('p_active_filter') == '0') {
    //                            $instance->where('p_active', '=', '0');
    //                        }
    //                    }
    //                    if (!empty($request->get('search'))) {
    //                        $instance->where(function($w) use($request){
    //                            $search = $request->get('search');
    //                            $w->orWhereRaw('CONCAT(p_name," ", p_color) LIKE ?', "%$search%")
    //                            ->orWhere('p_name', 'LIKE', "%$search%")
    //                            ->orWhere('mc_name', 'LIKE', "%$search%")
    //                            ->orWhere('br_name', 'LIKE', "%$search%")
    //                            ->orWhere('ps_name', 'LIKE', "%$search%");
    //                        });
    //                    }
    //                })
    //                ->addIndexColumn()
    //                ->make(true);
    //            } else {
    //                return datatables()->of(Product::select('products.id as pid', 'br_id', 'pc_id', 'psc_id', 'pssc_id', 'mc_id', 'ps_id', 'pu_id', 'gn_id', 'ss_id', 'p_name', 'p_aging', 'p_color', 'mc_name', 'br_name', 'ps_name', 'p_price_tag', 'p_purchase_price', 'p_sell_price', 'p_weight', 'p_active')
    //                ->join('brands', 'brands.id', '=', 'products.br_id')
    //                ->join('main_colors', 'main_colors.id', '=', 'products.mc_id')
    //                ->join('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
    //                ->where('p_delete', '!=', '1')
    //                ->where('pc_id', '=', $request->pc_id)
    //                ->where('psc_id', '=', $request->psc_id)
    //                ->where('pssc_id', '=', $request->pssc_id))
    //                ->editColumn('p_name_show', function($data){
    //                    return '<span style="white-space: nowrap;">'.$data->p_name.'</span>';
    //                })
    //                ->editColumn('p_color_show', function($data){
    //                    return '<span style="white-space: nowrap;">'.$data->p_color.' ('.$data->mc_name.')</span>';
    //                })
    //                ->editColumn('ps_name_show', function($data){
    //                    return '<span style="white-space: nowrap;">'.$data->ps_name.'</span>';
    //                })
    //                ->editColumn('p_price_tag_show', function($data){
    //                    return '<span class="float-right">'.number_format($data->p_price_tag,2,",",".").'</span>';
    //                })
    //                ->editColumn('p_purchase_price_show', function($data){
    //                    return '<span class="float-right">'.number_format($data->p_purchase_price,2,",",".").'</span>';
    //                })
    //                ->editColumn('p_sell_price_show', function($data){
    //                    return '<span class="float-right">'.number_format($data->p_sell_price,2,",",".").'</span>';
    //                })
    //                ->editColumn('p_detail', function($data){
    //                    return '<a id="product_detail_btn" data-id="'.$data->pid.'" style="white-space: nowrap;" class="btn btn-sm btn-primary" style>Detail</a>';
    //                })
    //                ->rawColumns(['p_name_show', 'ps_name_show', 'p_color_show', 'p_price_tag_show', 'p_purchase_price_show', 'p_sell_price_show', 'p_detail', 'p_description'])
    //                ->filter(function ($instance) use ($request) {
    //                    if (!empty($request->get('br_id_filter'))) {
    //                        $instance->where(function($w) use($request){
    //                            $br_id = $request->get('br_id_filter');
    //                            $w->orWhere('br_id', '=', $br_id);
    //                        });
    //                    }
    //                    if (!empty($request->get('ps_id_filter'))) {
    //                        $instance->where(function($w) use($request){
    //                            $ps_id = $request->get('ps_id_filter');
    //                            $w->orWhere('ps_id', '=', $ps_id);
    //                        });
    //                    }
    //                    if (!empty($request->get('mc_id_filter'))) {
    //                        $instance->where(function($w) use($request){
    //                            $mc_id = $request->get('mc_id_filter');
    //                            $w->orWhere('mc_id', '=', $mc_id);
    //                        });
    //                    }
    //                    if (!empty($request->get('sz_id_filter'))) {
    //                        $instance->join('product_stocks', 'product_stocks.p_id', '=', 'products.id')
    //                        ->where(function($w) use($request){
    //                            $sz_id = $request->get('sz_id_filter');
    //                            $w->orWhere('sz_id', '=', $sz_id);
    //                        });
    //                    }
    //                    if(!empty($request->get('p_active_filter'))) {
    //                        if($request->get('p_active_filter') == '1') {
    //                            $instance->where('p_active', '=', '1');
    //                        } elseif($request->get('p_active_filter') == '0') {
    //                            $instance->where('p_active', '=', '0');
    //                        } else {
    //                            $instance->where('p_active', '!=', '1');
    //                            $instance->where('p_active', '!=', '0');
    //                        }
    //                    }
    //                    if (!empty($request->get('search'))) {
    //                        $instance->where(function($w) use($request){
    //                            $search = $request->get('search');
    //                            $w->orWhereRaw('CONCAT(p_name," ", p_color) LIKE ?', "%$search%")
    //                            ->orWhere('p_name', 'LIKE', "%$search%")
    //                            ->orWhere('mc_name', 'LIKE', "%$search%")
    //                            ->orWhere('br_name', 'LIKE', "%$search%")
    //                            ->orWhere('ps_name', 'LIKE', "%$search%");
    //                        });
    //                    }
    //                })
    //                ->addIndexColumn()
    //                ->make(true);
    //            }
    //        }
    //    }

    public function getDatatables(Request $request)
    {
        try {
            if (request()->ajax()) {

                $query = datatables()->of(Product::select(
                    'products.id as pid',
                    'article_id',
                    'br_id',
                    'pc_id',
                    'psc_id',
                    'pssc_id',
                    'mc_id',
                    'ps_id',
                    'pu_id',
                    'gn_id',
                    'ss_id',
                    'p_code',
                    'p_name',
                    'p_description',
                    'p_aging',
                    'p_color',
                    'mc_name',
                    'br_name',
                    'ps_name',
                    'p_price_tag',
                    'p_purchase_price',
                    'p_sell_price',
                    'p_weight',
                    'p_active',
                    'p_delete',
                    'schema_size',
                    'subcategory1',
                    'subcategory2',
                    'complement',
                    'consignment',
                    'mp_best_seller',
                    'mp_stock_masking',
                    'is_everlast',
                    'is_supersale',
                    'is_reguler',
                    'mark_down',
                    'p_turnoverclass',
                    'link_content'
                )
                    ->join('brands', 'brands.id', '=', 'products.br_id')
                    ->join('main_colors', 'main_colors.id', '=', 'products.mc_id')
                    ->join('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
                    ->leftJoin('product_images', 'product_images.p_id', '=', 'products.id')
                    ->where('p_delete', '!=', '1')
                    ->when($request->input('p_photo_status_filter'), function ($query, $p_photo_status_filter) {
                        if ($p_photo_status_filter == '1') {
                            $query->whereNotNull('product_images.id');
                        } elseif ($p_photo_status_filter == '0') {
                            $query->whereNull('product_images.id');
                        }
                    })
                    ->groupBy('products.id'))
                    ->editColumn('p_name_show', function ($data) {
                        return '<span style="white-space: nowrap;">' . $data->p_name . '</span>';
                    })
                    ->editColumn('p_color_show', function ($data) {
                        return '<span style="white-space: nowrap;">' . $data->p_color . ' (' . $data->mc_name . ')</span>';
                    })
                    ->editColumn('ps_name_show', function ($data) {
                        return '<span style="white-space: nowrap;">' . $data->ps_name . '</span>';
                    })
                    ->editColumn('p_price_tag_show', function ($data) {
                        return '<span class="float-right">' . number_format($data->p_price_tag) . '</span>';
                    })
                    ->editColumn('p_purchase_price_show', function ($data) {
                        return '<span class="float-right">' . number_format($data->p_purchase_price) . '</span>';
                    })
                    ->editColumn('p_sell_price_show', function ($data) {
                        return '<span class="float-right">' . number_format($data->p_sell_price) . '</span>';
                    })
                    ->editColumn('p_detail', function ($data) {
                        return '<a id="product_detail_btn" data-id="' . $data->pid . '" data-article="' . $data->article_id . '"  style="white-space: nowrap;" class="btn btn-sm btn-primary" style>Detail</a>';
                    })
                    ->rawColumns(['p_name_show', 'ps_name_show', 'p_color_show', 'p_price_tag_show', 'p_purchase_price_show', 'p_sell_price_show', 'p_detail', 'p_description'])
                    ->filter(function ($instance) use ($request) {

                        if (!empty($request->get('pc_id'))) {
                            $instance->where(function ($w) use ($request) {
                                $pc_id = $request->get('pc_id');
                                $w->orWhere('pc_id', '=', $pc_id);
                            });
                        }

                        if (!empty($request->get('psc_id'))) {
                            $instance->where(function ($w) use ($request) {
                                $psc_id = $request->get('psc_id');
                                $w->orWhere('psc_id', '=', $psc_id);
                            });
                        }

                        if (!empty($request->get('pssc_id'))) {
                            $instance->where(function ($w) use ($request) {
                                $pssc_id = $request->get('pssc_id');
                                $w->orWhere('pssc_id', '=', $pssc_id);
                            });
                        }

                        if (!empty($request->get('br_id_filter'))) {
                            $instance->where(function ($w) use ($request) {
                                $br_id = $request->get('br_id_filter');
                                $w->orWhere('br_id', '=', $br_id);
                            });
                        }
                        if (!empty($request->get('ps_id_filter'))) {
                            $instance->where(function ($w) use ($request) {
                                $ps_id = $request->get('ps_id_filter');
                                $w->orWhere('ps_id', '=', $ps_id);
                            });
                        }
                        if (!empty($request->get('mc_id_filter'))) {
                            $instance->where(function ($w) use ($request) {
                                $mc_id = $request->get('mc_id_filter');
                                $w->orWhere('mc_id', '=', $mc_id);
                            });
                        }
                        if (!empty($request->get('sz_id_filter'))) {
                            $instance->join('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                                ->where(function ($w) use ($request) {
                                    $sz_id = $request->get('sz_id_filter');
                                    $w->orWhere('sz_id', '=', $sz_id);
                                });
                        }
                        if (!empty($request->get('p_active_filter'))) {
                            if ($request->get('p_active_filter') == '1') {
                                $instance->where('p_active', '=', '1');
                            }

                            if ($request->get('p_active_filter') == '0') {
                                $instance->where('p_active', '=', '0');
                            }
                        }
                        if (!empty($request->get('search'))) {
                            $instance->where(function ($w) use ($request) {
                                $search = $request->get('search');
                                $w->orWhereRaw('CONCAT(p_name," ", p_color) LIKE ?', "%$search%")
                                    ->orWhere('p_name', 'LIKE', "%$search%")
                                    ->orWhere('mc_name', 'LIKE', "%$search%")
                                    ->orWhere('br_name', 'LIKE', "%$search%")
                                    ->orWhere('ps_name', 'LIKE', "%$search%")
                                    ->orWhere('article_id', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->addIndexColumn()
                    ->make(true);

                try {
                    return $query;
                } catch (\Exception $e) {
                    return $e;
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDatatablesItem(Request $request)
    {
        if (request()->ajax()) {
            $query = Product::select(
                'products.id as pid',
                'br_id',
                'pc_id',
                'psc_id',
                'pssc_id',
                'mc_id',
                'ps_id',
                'pu_id',
                'gn_id',
                'ss_id',
                'p_name',
                'p_description',
                'p_aging',
                'p_color',
                'mc_name',
                'br_name',
                'ps_name',
                'p_price_tag',
                'p_purchase_price',
                'p_sell_price',
                'p_weight',
                'article_id'
            )
                ->join('brands', 'brands.id', '=', 'products.br_id')
                ->join('main_colors', 'main_colors.id', '=', 'products.mc_id')
                ->join('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
                ->where('p_delete', '!=', '1');

            if (!empty($request->ps_id)) {
                $query->where('ps_id', '=', $request->ps_id);
            }

            return datatables()->of($query)
                ->editColumn('p_name_show', function ($data) {
                    return '<span style="white-space: nowrap;">' . $data->p_name . '</span>';
                })
                ->editColumn('article_id', function ($data) {
                    return '<span style="white-space: nowrap;">' . $data->article_id . '</span>';
                })
                ->editColumn('p_color_show', function ($data) {
                    return '<span style="white-space: nowrap;">' . $data->p_color . ' (' . $data->mc_name . ')</span>';
                })
                ->editColumn('ps_name_show', function ($data) {
                    return '<span style="white-space: nowrap;">' . $data->ps_name . '</span>';
                })
                ->editColumn('p_size', function ($data) {})
                ->editColumn('p_action', function ($data) {
                    $product_stock = new ProductStock;
                    $select = ['product_stocks.id as psid', 'p_id', 'sz_id', 'sz_name', 'ps_qty', 'ps_barcode', 'ps_running_code'];
                    $where = [
                        'p_id' => $data->pid
                    ];
                    $check_data = $product_stock->getAllData($select, $where);
                    if (!empty($check_data->first()->sz_id)) {
                        $check_list = '';
                        $i = 0;
                        foreach ($check_data as $row) {
                            $check_list .= '<span style="white-space: nowrap;"><input type="checkbox" data-index="' . $i . '" class="checkbox_add_item' . $data->pid . '_' . $i . '" id="checkbox_add_item" data-pid="' . $data->pid . '" data-psid="' . $row->psid . '"/> ' . $row->sz_name . ' - ' . $row->ps_barcode . ' (Sisa : ' . $row->ps_qty . ')</span><br/>';
                            $i++;
                        }
                        return $check_list;
                    } else {
                        return 'Size belum disetting untuk produk ini';
                    }
                })
                ->rawColumns(['p_name_show', 'article_id', 'p_color_show', 'p_action'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('br_id_filter'))) {
                        $instance->where(function ($w) use ($request) {
                            $br_id = $request->get('br_id_filter');
                            $w->orWhere('br_id', '=', $br_id);
                        });
                    }
                    if (!empty($request->get('mc_id_filter'))) {
                        $instance->where(function ($w) use ($request) {
                            $mc_id = $request->get('mc_id_filter');
                            $w->orWhere('mc_id', '=', $mc_id);
                        });
                    }
                    if (!empty($request->get('sz_id_filter'))) {
                        $instance->join('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                            ->where(function ($w) use ($request) {
                                $sz_id = $request->get('sz_id_filter');
                                $w->orWhere('sz_id', '=', $sz_id);
                            });
                    }
                    if (!empty($request->get('psc_id_filter'))) {
                        $instance->where(function ($w) use ($request) {
                            $psc_id = $request->get('psc_id_filter');
                            $w->orWhere('psc_id', '=', $psc_id);
                        });
                    }

                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('p_name', 'LIKE', "%$search%")
                                ->orWhere('mc_name', 'LIKE', "%$search%")
                                ->orWhere('br_name', 'LIKE', "%$search%")
                                ->orWhere('ps_name', 'LIKE', "%$search%")
                                ->orWhere('article_id', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getDatatablesLocation(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(Product::select('products.id as pid', 'br_id', 'pc_id', 'psc_id', 'pssc_id', 'mc_id', 'ps_id', 'pu_id', 'gn_id', 'ss_id', 'p_name', 'p_description', 'p_aging', 'p_color', 'mc_name', 'br_name', 'ps_name', 'p_price_tag', 'p_purchase_price', 'p_sell_price', 'p_weight')
                ->join('brands', 'brands.id', '=', 'products.br_id')
                ->join('main_colors', 'main_colors.id', '=', 'products.mc_id')
                ->join('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
                ->where('p_delete', '!=', '1'))
                ->editColumn('p_article', function ($data) {
                    return '<span style="white-space: nowrap;">' . $data->p_name . ' [' . $data->mc_name . ' ' . $data->p_color . '] [' . $data->br_name . ']</span>';
                })
                ->editColumn('p_action', function ($data) {
                    $product_stock = new ProductStock;
                    $select = ['product_stocks.id as psid', 'p_id', 'sz_id', 'sz_name', 'ps_qty', 'ps_barcode', 'ps_running_code'];
                    $where = [
                        'p_id' => $data->pid
                    ];
                    $check_data = $product_stock->getAllData($select, $where);
                    if (!empty($check_data->first()->sz_id)) {
                        $check_list = '';
                        foreach ($check_data as $row) {
                            $setup_qty = 0;
                            $check_setup = ProductLocationSetup::select('pls_qty')->where('pst_id', $row->psid)->exists();
                            if ($check_setup) {
                                $get_setup = ProductLocationSetup::select('pls_qty')->where('pst_id', $row->psid)->get();
                                foreach ($get_setup as $gs_row) {
                                    $setup_qty += $gs_row->pls_qty;
                                }
                            }
                            $unset_qty = $row->ps_qty - $setup_qty;
                            $check_list .= '<span style="white-space: nowrap;"> <a class="btn btn-sm btn-primary col-2" onclick="return addProduct(' . $row->psid . ', \'' . $data->p_name . '\', \'' . $row->sz_name . '\', ' . $unset_qty . ')">' . $row->sz_name . '</a> <a class="btn btn-sm btn-primary col-6" onclick="return addProduct(' . $row->psid . ', \'' . $data->p_name . '\', \'' . $row->sz_name . '\', ' . $unset_qty . ')">(Unset : ' . $unset_qty . ')</a></span><br/>';
                        }
                        return $check_list;
                    } else {
                        return 'Size belum disetting untuk produk ini';
                    }
                })
                ->rawColumns(['p_article', 'p_action'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('br_id_filter'))) {
                        $instance->where(function ($w) use ($request) {
                            $br_id = $request->get('br_id_filter');
                            $w->orWhere('br_id', '=', $br_id);
                        });
                    }
                    if (!empty($request->get('mc_id_filter'))) {
                        $instance->where(function ($w) use ($request) {
                            $mc_id = $request->get('mc_id_filter');
                            $w->orWhere('mc_id', '=', $mc_id);
                        });
                    }
                    if (!empty($request->get('sz_id_filter'))) {
                        $instance->join('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                            ->where(function ($w) use ($request) {
                                $sz_id = $request->get('sz_id_filter');
                                $w->orWhere('sz_id', '=', $sz_id);
                            });
                    }
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('p_name', 'LIKE', "%$search%")
                                ->orWhere('mc_name', 'LIKE', "%$search%")
                                ->orWhere('br_name', 'LIKE', "%$search%")
                                ->orWhere('ps_name', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function generateRunningCode()
    {
        $check = ProductStock::select('ps_running_code')->orderByDesc('ps_running_code')->limit(1)->get()->first();
        if (!empty($check)) {
            $current_running_code = $check->ps_running_code;
            $next_running_code = $current_running_code + 1;
            $running_length = strlen($next_running_code);
            $new_running_code = '';
            if ($running_length == 1) {
                $new_running_code = '000000000000' . $next_running_code;
            } else if ($running_length == 2) {
                $new_running_code = '00000000000' . $next_running_code;
            } else if ($running_length == 3) {
                $new_running_code = '0000000000' . $next_running_code;
            } else if ($running_length == 4) {
                $new_running_code = '000000000' . $next_running_code;
            } else if ($running_length == 5) {
                $new_running_code = '00000000' . $next_running_code;
            } else if ($running_length == 6) {
                $new_running_code = '0000000' . $next_running_code;
            } else if ($running_length == 7) {
                $new_running_code = '000000' . $next_running_code;
            } else if ($running_length == 8) {
                $new_running_code = '00000' . $next_running_code;
            } else if ($running_length == 9) {
                $new_running_code = '0000' . $next_running_code;
            } else if ($running_length == 10) {
                $new_running_code = '000' . $next_running_code;
            } else if ($running_length == 11) {
                $new_running_code = '00' . $next_running_code;
            } else if ($running_length == 12) {
                $new_running_code = '0' . $next_running_code;
            } else if ($running_length == 13) {
                $new_running_code = $next_running_code;
            }
        } else {
            $new_running_code = '0000000000001';
        }

        if ($this->runningCodeExists($new_running_code)) {
            return $this->generateRunningCode();
        }
        return $new_running_code;
    }

    public function runningCodeExists($number)
    {
        return ProductStock::where(['ps_running_code' => $number])->exists();
    }

    public function checkExistsBarcode(Request $request)
    {
        $check = ProductStock::where(['ps_barcode' => $request->_barcode])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsArticleID(Request $request)
    {
        $check = Product::where(['article_id' => $request->_article_id])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function storeData(Request $request)
    {
        //     return json_encode($request->all());

        try {
            $product = new Product;
            $product_stock = new ProductStock;
            $mode = $request->input('_mode');
            $id = $request->input('_id');
            $sz_barcode = $request->input('_sz_barcode');
            $sz_sell_price = $request->input('_sz_sell_price');
            $data = [
                'br_id' => $request->input('br_id'),
                'pc_id' => $request->input('pc_id'),
                'psc_id' => $request->input('psc_id'),
                'pssc_id' => $request->input('pssc_id'),
                'mc_id' => $request->input('mc_id'),
                'ps_id' => $request->input('ps_id'),
                'pu_id' => $request->input('pu_id'),
                'gn_id' => $request->input('gn_id'),
                'ss_id' => $request->input('ss_id'),
                'p_color' => ltrim($request->input('p_color')),
                'p_code' => ltrim($request->input('p_code')),
                'p_name' => ltrim($request->input('p_name')),
                'p_description' => $request->input('p_description'),
                'p_aging' => $request->input('p_aging'),
                'p_price_tag' => $request->input('p_price_tag'),
                'p_purchase_price' => $request->input('p_purchase_price'),
                'p_sell_price' => $request->input('p_sell_price'),
                'p_weight' => $request->input('p_weight'),
                'article_id' => $request->input('article_id'),
                'schema_size' => $request->input('sz_schema_modal_id'),
                'p_delete' => '0',
                'subcategory1' => $request->input('subcatone'),
                'subcategory2' => $request->input('subcattwo'),
                'consignment' => $request->input('consignment'),
                'complement' => $request->input('complement') ?? 0,
                'mp_best_seller' => $request->input('mp_best_seller'),
                'mp_stock_masking' => $request->input('mp_stock_masking'),
                'is_everlast' => $request->input('is_everlast') ?? 0,
                'is_supersale' => $request->input('is_supersale') ?? 0,
                'is_reguler' => $request->input('is_reguler') ?? 0,
                'mark_down' => $request->input('mark_down') ?? 0,
                'p_turnoverclass' => $request->input('p_turnoverclass'),
            ];
            $save = $product->storeData($mode, $id, $data);

            if (!empty($save)) {
                if ($request->input('pc_id') !== $request->input('_current_pc_id')) {
                    DB::table('product_stocks')->where(['p_id' => $id])->delete();
                }

                $exp = explode('|', $request->_sz_id);
                $count = (int)count($exp);
                $barcodeArray = explode('|', rtrim($request->input('_sz_barcode'), '|'));
                for ($i = 0; $i <= $count; $i++) {
                    if (empty($exp[$i])) {
                        continue;
                    }

                    $barcodeItem = $barcodeArray[$i];

                    // Extracting ID and Barcode from the current element
                    list($size_id, $barcode) = explode('-', $barcodeItem);

                    if ($mode == 'add') {
                        ProductStock::create([
                            'p_id' => $save,
                            'sz_id' => $size_id,
                            'ps_barcode' => $barcode,
                            'ps_qty' => '0',
                            'ps_running_code' => $barcode
                        ]);
                    } else {
                        $check_current_size = ProductStock::where(['p_id' => $id, 'sz_id' => $exp[$i]])->exists();
                        if ($check_current_size) {
                            ProductStock::where(['p_id' => $id, 'sz_id' => $exp[$i]])->update(['ps_running_code' => $this->generateRunningCode()]);
                        } else {
                            ProductStock::create([
                                'p_id' => $id,
                                'sz_id' => $size_id,
                                'ps_qty' => '0',
                                'ps_running_code' => $barcode
                            ]);
                        }
                    }
                }
//                if ($mode == 'add') {
//                    $this->UserActivity('menambah data produk ' . strtoupper($request->input('p_name')) . ' ' . strtoupper($request->input('p_color')));
//                } else {
//                    $this->UserActivity('mengubah data produk ' . strtoupper($request->input('p_name')) . ' ' . strtoupper($request->input('p_color')));
//                }
                $r['status'] = '200';
            } else {
                $exp = explode('|', $request->_sz_id);
                $count = (int)count($exp);
                for ($i = 0; $i <= $count; $i++) {
                    if (empty($exp[$i])) {
                        continue;
                    }
                    if ($mode == 'add') {
                        ProductStock::create([
                            'p_id' => $id,
                            'sz_id' => $exp[$i],
                            'ps_qty' => '0',
                            //                            'ps_running_code' => $this->generateRunningCode()
                        ]);
                    } else {
                        $check_current_size = ProductStock::where(['p_id' => $id, 'sz_id' => $exp[$i]])->exists();
                        if ($check_current_size) {
                            ProductStock::where(['p_id' => $id, 'sz_id' => $exp[$i]])->update(['ps_running_code' => $this->generateRunningCode()]);
                        } else {
                            ProductStock::create([
                                'p_id' => $id,
                                'sz_id' => $exp[$i],
                                'ps_qty' => '0',
                            ]);
                        }
                    }
                }

                $r['consignment'] = $data;
                $r['complement'] = $request->input('complement');
                $r['status'] = '200';
            }
            return json_encode($r);
        } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    public function updateBarcode(Request $request)
    {
        $running = $request->_running;
        $barcode = $request->_barcode;
        $id_barcode = $request->_id;
        $p_id = ProductStock::where(['id' => $running])->first()->p_id;
        $ps = ProductStock::where(['id' => $running])->update(['ps_barcode' => $barcode]);
        if (!empty($ps)) {
            $this->UserActivity(Auth::user()->id,'mengubah barcode produk ' . $barcode . ' Berhasil ', $p_id);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        try {
            $product = new Product;
            $product_stock = new ProductStock;
            $id = $request->input('_id');
            $save = DB::table('product_stocks')->where('p_id', $id)->delete();
            if ($save == 0) {
                $item = Product::select('p_name', 'p_color')->where('id', $id)->get()->first();

                if ($item != null) {
                    $item_name = Product::select('p_name', 'p_color')->where('id', $id)->get()->first();
                    $save_product = $product->deleteData($id);
                    if ($save_product) {
//                        $this->UserActivity(A,'menghapus data produk ' . $item_name->p_name . ' ' . $item_name->p_color);
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '400';
                    }
                    return json_encode($r);
                }
            }
            if ($save) {
                $item_name = Product::select('p_name', 'p_color')->where('id', $id)->get()->first();
                $save_product = $product->deleteData($id);
                if ($save_product) {
//                    $this->UserActivity('menghapus data produk ' . $item_name->p_name . ' ' . $item_name->p_color);
                    $r['status'] = '200';
                } else {
                    $r['status'] = '400';
                }
            } else {
                $r['status'] = '400';
            }
            return json_encode($r);
        } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
    }


    public function productDetail(Request $request)
    {
        $product = new Product;
        $id = $request->_id;
        $select = ['br_name', 'pc_name', 'psc_name', 'pssc_name', 'mc_name', 'ps_name', 'pu_name', 'gn_name', 'ss_name', 'p_name', 'p_description', 'p_color', 'p_price_tag', 'p_purchase_price', 'p_sell_price'];
        $where = [
            'products.id' => $id
        ];
        $get_product = $product->getJoinData($select, $where);
        $data = [
            'product' => $get_product,
        ];
        return view('app.product.product_detail', compact('data'));
    }

    public function importData(Request $request)
    {
        try {
            if (request()->hasFile('p_template')) {
                $import = new ProductImport;
                Excel::import($import, request()->file('p_template'));
                if ($import->getRowCount() >= 0) {
                    if (empty($import->getSameArticleId())) {
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '419';
                        $r['same_article_id'] = $import->getSameArticleId();
                    }
                } else {
                    $r['status'] = '400';
                    $r['error_messages'] = $import->getErrorMessages();
                }
            } else {
                $r['status'] = '400';
            }
            return json_encode($r);
        } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    public function exportData()
    {
        set_time_limit(300);
        return Excel::download(new ProductArticleExport, 'product_data.xlsx');
        //  		  return Excel::download(new ProductExport, 'product_data.xlsx');
    }

    public function exportDataBarcode()
    {
        //        set_time_limit(4096);
        return Excel::download(new ProductExport, 'product_data_barcode.xlsx');
    }

    public function massUpdateProductImport(Request $request)
    {
        $update_type = $request->input('update_type');

        $import_data = Excel::toArray(new MassUpdateProductImport, $request->file('p_mass_import'));

        $update_column = $import_data[0][0][1];

        if ($update_type == 'article') {
            $is_allowed = Product::$massUpdateColumns; // Accessing the property as static

            if (!in_array($update_column, $is_allowed) || $import_data[0][0][0] != 'article_id') {
                $r['status'] = '400';
                $r['message'] = 'Kolom yang akan diupdate tidak sesuai.';
                return json_encode($r);
            }

            $massUpdateService = new MassUpdateProductService(); // Instantiate the service
            $error_ids = $massUpdateService->processRowArticleLevel($import_data[0], $update_column); // Call the method on the service with the first array
        } else if ($update_type == 'sku') {
            $is_allowed = Product::$massUpdateSKUColumns; // Accessing the property as static
            if (!in_array($update_column, $is_allowed) || $import_data[0][0][0] != 'ps_barcode') {
                $r['status'] = '400';
                $r['message'] = 'Kolom yang akan diupdate tidak sesuai.';
                return json_encode($r);
            }

            $massUpdateService = new MassUpdateProductService();
            $error_ids = $massUpdateService->processRowSKUlevel($import_data[0], $update_column); // Call the method on the service with the first array
        } else {
            $r['status'] = '400';
            $r['message'] = 'Tipe update tidak sesuai.';
            return json_encode($r);
        }


        if (!$error_ids) {
            $r['status'] = '200';
            $r['message'] = 'Update massal produk berhasil.';
        } else {
            $r['status'] = '400';
            $r['message'] = 'Update massal produk gagal';
            $r['error_ids'] = $error_ids;
        }

        return json_encode($r);
    }

    public function updateProductStockIds()
    {
        try {
            $productLinks = DB::table('product_links')->get();

            if ($productLinks->isEmpty()) {
                return 'No records found';
            }

            $updated = 0;

            foreach ($productLinks as $link) {
                $currentProductId = $link->product_id;

                // Remove quotes and decode if it's a JSON string
                $currentProductId = trim($currentProductId, '"');
                $productIdArray = json_decode($currentProductId, true);

                // If not valid JSON, treat as single integer value
                if (!is_array($productIdArray)) {
                    $productIdArray = [(int)$currentProductId];
                }

                // Ensure all values are integers
                $productIdArray = array_map('intval', $productIdArray);

                DB::table('product_links')
                    ->where('id', $link->id)
                    ->update([
                        'product_id' => json_encode($productIdArray, JSON_NUMERIC_CHECK),
                        'updated_at' => now()
                    ]);

                $updated++;
            }

            return 'Successfully updated ' . $updated . ' records by converting product_id to JSON array format [59582]';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
