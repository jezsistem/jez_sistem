<!-- Blog Category Modal -->
<div id="BcModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Kategori Blog</h3>
            <button type="button" onclick="closeBlogCategoryModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_bc">
            @csrf
            <input type="hidden" name="_bc_id" id="bc_modal_id" value="" />
            <input type="hidden" name="_bc_mode" id="bc_modal_mode" value="" />
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Blog <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="bc_name" name="bc_name" required />
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeBlogCategoryModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="submit" id="save_bc_btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Blog Content Modal -->
<div id="BccModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b sticky top-0 bg-white z-10">
            <h3 class="text-lg font-semibold text-gray-900">Blog Konten</h3>
            <button type="button" onclick="closeBlogModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_bcc" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_bcc_id" id="bcc_modal_id" value="" />
            <input type="hidden" name="_bcc_mode" id="bcc_modal_mode" value="" />
            <input type="hidden" name="_bcc_image" id="bcc_modal_image" value="" />
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="bc_id" id="bc_id" required>
                        <option value="">- Pilih Kategori Blog -</option>
                        @foreach ($data['bc_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="bcc_title" name="bcc_title" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Cover</label>
                    <input type="file" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="bcc_image" name="bcc_image" accept="image/*" />
                    <div id="bcc_image_preview" class="mt-2"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konten <span class="text-red-500">*</span></label>
                    <textarea class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="bcc_content" id="bcc_content" rows="20"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="bcc_keywords" name="bcc_keywords" required />
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t sticky bottom-0 bg-white">
                <button type="button" onclick="closeBlogModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="button" id="delete_bcc_btn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="submit" id="save_bcc_btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
