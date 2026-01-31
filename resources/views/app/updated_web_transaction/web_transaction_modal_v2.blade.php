<!-- Web Transaction Modal -->
<div id="WebTransactionModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeWebTransactionModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
            <form id="f_wt">
                @csrf
                <input type="hidden" name="_id" id="wt_modal_id" value="">
                <input type="hidden" name="_mode" id="wt_modal_mode" value="">

                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Status Transaksi</h3>
                    <button type="button" onclick="closeWebTransactionModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="py-4">
                    <div class="mb-4">
                        <label for="wt_modal_status" class="block mb-2 text-sm font-medium text-gray-900">Status Invoice</label>
                        <select id="wt_modal_status" name="pos_status" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">Pilih</option>
                            <option value="PAID">PAID (Sudah Dibayar)</option>
                            <option value="CANCEL">CANCEL</option>
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeWebTransactionModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Tutup
                    </button>
                    <button type="submit" id="save_wt_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
