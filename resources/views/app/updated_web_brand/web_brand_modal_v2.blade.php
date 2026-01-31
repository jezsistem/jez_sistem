<!-- Brand Modal -->
<div id="BrandModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Brand</h3>
            <button type="button" onclick="closeBrandModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_brand" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_id" id="brand_modal_id" value="" />
            <input type="hidden" name="_mode" id="brand_modal_mode" value="" />
            <input type="hidden" name="_image" id="brand_modal_image" value="" />
            <input type="hidden" name="_banner" id="brand_modal_banner" value="" />
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Brand <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="br_name" name="br_name" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="br_slug" name="br_slug" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="br_description" name="br_description" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo Brand</label>
                    <input type="file" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="br_image" id="br_image" accept="image/*" onchange="loadFile(event)" />
                    <div class="mt-2 text-center">
                        <img id="imagePreview" class="max-w-xs mx-auto rounded cursor-pointer" style="display:none;" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Banner</label>
                    <input type="file" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="br_banner" id="br_banner" accept="image/*" onchange="loadBanner(event)" />
                    <div class="mt-2 text-center">
                        <img id="bannerPreview" class="max-w-xs mx-auto rounded cursor-pointer" style="display:none;" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand Lokal ?</label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="is_local" name="is_local" required>
                        <option value="">- Pilih -</option>
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeBrandModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="button" id="delete_brand_btn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="submit" id="save_brand_btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
