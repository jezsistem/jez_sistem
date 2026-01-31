<!-- WhatsApp Modal -->
<div id="WaModal" class="modal-overlay hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 my-8">
        <form id="f_whatsapp" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_id" id="_id" value="">
            <input type="hidden" name="_mode" id="_mode" value="">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-green-600 rounded-t-xl">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fa fa-whatsapp mr-2"></i> Pesan Baru
                </h3>
                <button type="button" class="close-modal text-white/80 hover:text-white">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe <span class="text-red-500">*</span></label>
                    <select name="wa_type" id="wa_type" required class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <option value="">- Pilih Tipe -</option>
                        <option value="people">Perorang</option>
                        <option value="all">Semua Customer</option>
                    </select>
                </div>
                <div id="wa_phone_container" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">No WA <span class="text-red-500">*</span></label>
                    <input type="text" name="wa_phone" id="wa_phone" placeholder="08xxxxxxxxxx"
                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    <p class="mt-1 text-xs text-gray-500">Masukkan nomor WhatsApp tujuan</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pesan <span class="text-red-500">*</span></label>
                    <textarea name="wa_message" id="wa_message" rows="4" required placeholder="Ketik pesan Anda di sini..."
                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">Tutup</button>
                <button type="submit" id="save_whatsapp_btn" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                    <i class="fa fa-paper-plane mr-2"></i> Kirim
                </button>
            </div>
        </form>
    </div>
</div>
