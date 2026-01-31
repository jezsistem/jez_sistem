<!-- Web Confirmation Modal -->
<div id="WebConfirmationModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeWebConfirmationModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
            <form id="f_wbc">
                @csrf
                <input type="hidden" name="_id" id="wbc_modal_id" value="">
                <input type="hidden" name="_mode" id="wbc_modal_mode" value="">
                <input type="hidden" name="pos_invoice" id="wbc_modal_invoice" value="">

                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Pembayaran</h3>
                    <button type="button" onclick="closeWebConfirmationModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="py-4">
                    <!-- Transfer Image -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Bukti Transfer</label>
                        <div class="flex justify-center">
                            <img id="wbc_modal_img" src="" class="max-w-full h-auto rounded-lg border" style="max-height: 300px; display: none;">
                        </div>
                    </div>

                    <!-- Status Select -->
                    <div class="mb-4">
                        <label for="wbc_modal_status" class="block mb-2 text-sm font-medium text-gray-900">Status Konfirmasi</label>
                        <select id="wbc_modal_status" name="cf_status" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">Pilih</option>
                            <option value="1">Terima</option>
                            <option value="2">Tolak</option>
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeWebConfirmationModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Tutup
                    </button>
                    <button type="button" id="delete_wbc_btn" class="hidden px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Hapus
                    </button>
                    <button type="submit" id="save_wbc_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
