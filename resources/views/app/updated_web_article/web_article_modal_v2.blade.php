<!-- Image Modal -->
<div id="ImageModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeImageModal()"></div>
        <div class="inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
            <form id="f_article">
                @csrf
                <input type="hidden" id="img_pid" value=""/>
                <input type="hidden" id="_main_image" value=""/>
                <input type="hidden" id="_detail_image" value=""/>
                <input type="hidden" id="_chart_image" value=""/>
                
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">[Gambar] <span id="image_label"></span></h3>
                    <button type="button" onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="py-4">
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Gambar Utama <small class="text-gray-500">* klik gambar jika ingin menghapus</small></label>
                        <div class="mb-2">
                            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" id="p_main_image" name="p_main_image"/>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg" id="p_main_image_preview"></div>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Gambar Detail <small class="text-gray-500">* klik gambar jika ingin menghapus</small></label>
                        <div class="mb-2">
                            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" id="p_image" name="p_image[]" multiple/>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg" id="p_image_preview"></div>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Size Chart <small class="text-gray-500">* klik gambar jika ingin menghapus</small></label>
                        <div class="mb-2">
                            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" id="p_size_chart" name="p_size_chart"/>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg" id="p_size_chart_preview"></div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeImageModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="submit" id="save_p_image_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Description Modal -->
<div id="DescriptionModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeDescriptionModal()"></div>
        <div class="inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">[Deskripsi] <span id="description_label"></span></h3>
                <button type="button" onclick="closeDescriptionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="py-4">
                <input type="hidden" id="pid" value=""/>
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Deskripsi</label>
                    <textarea class="form-control" name="p_description" id="p_description" rows="3"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Script Video Embed</label>
                    <textarea class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" name="p_video" id="p_video" rows="3"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closeDescriptionModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Tutup
                </button>
                <button type="button" id="save_p_description_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
