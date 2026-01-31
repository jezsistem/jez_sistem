<!-- Web Category Modal -->
<div id="WebCategoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Kategori</h3>
            <button type="button" onclick="closeWebCategoryModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_web_category" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_id" id="web_category_modal_id" value="" />
            <input type="hidden" name="_mode" id="web_category_modal_mode" value="" />
            <input type="hidden" name="_image" id="web_category_modal_image" value="" />
            <input type="hidden" name="_banner" id="web_category_modal_banner" value="" />
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Kategori <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="cs_title" name="cs_title" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="cs_slug" name="cs_slug" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sub Kategori <span class="text-red-500">*</span></label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="psc_id" name="psc_id" required>
                        <option value="">- Sub Kategori -</option>
                        @foreach ($data['psc_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sub Sub Kategori</label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="cs_sub_category" name="cs_sub_category" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
                    <small class="text-gray-500">300x300px</small>
                    <input type="file" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 mt-1" name="cs_image" id="cs_image" accept="image/*" onchange="loadFile(event)" />
                    <div class="mt-2 text-center">
                        <img id="imagePreview" class="max-w-xs mx-auto rounded cursor-pointer" style="display:none;" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Banner</label>
                    <small class="text-gray-500">2000x960px</small>
                    <input type="file" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 mt-1" name="cs_banner" id="cs_banner" accept="image/*" onchange="loadBanner(event)" />
                    <div class="mt-2 text-center">
                        <img id="bannerPreview" class="max-w-xs mx-auto rounded cursor-pointer" style="display:none;" />
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeWebCategoryModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="button" id="delete_web_category_btn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="submit" id="save_web_category_btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
