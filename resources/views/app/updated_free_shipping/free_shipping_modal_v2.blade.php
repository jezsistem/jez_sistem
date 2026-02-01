<!-- Free Shipping Modal -->
<div id="FsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Kota Tujuan</h3>
            <button type="button" onclick="closeFreeShippingModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_free_shipping">
            @csrf
            <input type="hidden" name="_id" id="free_shipping_modal_id" value="" />
            <input type="hidden" name="_mode" id="free_shipping_modal_mode" value="" />
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kota Tujuan <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="city_name" name="city_name" required autocomplete="off" />
                    <div id="cityList" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto" style="display:none;"></div>
                    <input type="hidden" id="city_id" name="city_id" value="" />
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeFreeShippingModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="button" id="delete_free_shipping_btn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="submit" id="save_free_shipping_btn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Simpan</button>
            </div>
        </form>
    </div>
</div>
