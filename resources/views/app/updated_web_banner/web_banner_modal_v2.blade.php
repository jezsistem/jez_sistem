<!-- Banner Modal -->
<div id="WbModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Banner</h3>
            <button type="button" onclick="closeBannerModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_wb" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_id" id="wb_modal_id" value="" />
            <input type="hidden" name="_mode" id="wb_modal_mode" value="" />
            <input type="hidden" name="_image" id="wb_modal_image" value="" />
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="bn_name" name="bn_name" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="bn_slug" name="bn_slug" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar <span class="text-xs text-gray-500">(2000x960px)</span></label>
                    <input type="file" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="bn_image" id="bn_image" accept="image/*" onchange="loadFile(event)" />
                    <div class="mt-2 text-center">
                        <img id="imagePreview" class="max-w-xs mx-auto rounded" style="display:none;" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Is Child</label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="is_child" name="is_child" required>
                        <option value="">- Pilih -</option>
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                    <input type="number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="bn_sort" name="bn_sort" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter</label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="bn_filter" name="bn_filter" required>
                        <option value="">- Pilih -</option>
                        <option value="0">Terbaru</option>
                        <option value="1">Terlaris</option>
                        <option value="2">Termurah</option>
                        <option value="3">Termahal</option>
                        <option value="4">Brand Lokal</option>
                        <option value="5">Topdeals</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeBannerModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="button" id="delete_wb_btn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="submit" id="save_wb_btn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Brand Modal -->
<div id="BrandModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Brand</h3>
            <div class="flex gap-2">
                <button type="button" id="add_brand_btn" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-red-600">
                    <i class="fa fa-plus mr-1"></i>Data Baru
                </button>
                <button type="button" onclick="closeBrandModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <div class="mb-4">
            <input type="search" id="brand_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari brand..." />
        </div>
        <div class="overflow-x-auto">
            <table id="Brandtb" class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 py-3">No</th>
                        <th scope="col" class="px-3 py-3">Brand</th>
                        <th scope="col" class="px-3 py-3">SubSub Kategori</th>
                    </tr>
                </thead>
                <tbody id="brand_tbody">
                    <tr>
                        <td colspan="3" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex justify-between items-center mt-4">
            <div id="brand_pagination_info" class="text-sm text-gray-600"></div>
            <div id="brand_pagination_controls" class="flex items-center gap-2"></div>
        </div>
    </div>
</div>

<!-- Brand Edit Modal -->
<div id="BrandEditModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Brand</h3>
            <button type="button" onclick="closeBrandEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_brand" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_bb_id" id="bb_modal_id" value="" />
            <input type="hidden" name="_bb_mode" id="bb_modal_mode" value="" />
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="br_id" name="br_id" required>
                        <option value="">- Brand -</option>
                        @foreach ($data['br_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeBrandEditModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="button" id="delete_brand_btn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="submit" id="save_brand_btn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Article Modal -->
<div id="ArticleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b mb-4">
            <h3 class="text-lg font-semibold text-gray-900">SubSub Kategori</h3>
            <div class="flex gap-2">
                <button type="button" id="add_article_btn" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-red-600">
                    <i class="fa fa-plus mr-1"></i>Data Baru
                </button>
                <button type="button" onclick="closeArticleModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <div class="mb-4">
            <input type="search" id="article_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari article..." />
        </div>
        <div class="overflow-x-auto">
            <table id="Articletb" class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 py-3">No</th>
                        <th scope="col" class="px-3 py-3">Nama</th>
                    </tr>
                </thead>
                <tbody id="article_tbody">
                    <tr>
                        <td colspan="2" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex justify-between items-center mt-4">
            <div id="article_pagination_info" class="text-sm text-gray-600"></div>
            <div id="article_pagination_controls" class="flex items-center gap-2"></div>
        </div>
    </div>
</div>

<!-- Article Edit Modal -->
<div id="ArticleEditModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Article</h3>
            <button type="button" onclick="closeArticleEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_article" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_bbd_id" id="bbd_modal_id" value="" />
            <input type="hidden" name="_bbd_mode" id="bbd_modal_mode" value="" />
            <div class="mt-4 space-y-4">
                <div id="article_reload"></div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeArticleEditModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="button" id="delete_article_btn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="submit" id="save_article_btn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Simpan</button>
            </div>
        </form>
    </div>
</div>
