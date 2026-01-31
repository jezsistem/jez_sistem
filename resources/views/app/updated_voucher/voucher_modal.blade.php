<!-- Voucher Modal -->
<div id="VoucherModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeVoucherModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <form id="f_voucher">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                <input type="hidden" name="vc_pst_id" id="vc_pst_id" value="" />
                
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Voucher</h3>
                    <button type="button" onclick="closeVoucherModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div id="vc_code_input">
                        <label for="vc_code" class="block text-sm font-medium text-gray-700 mb-1">Kode *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="vc_code" name="vc_code" required />
                    </div>
                    
                    <div>
                        <label for="vc_discount" class="block text-sm font-medium text-gray-700 mb-1">Discount *</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="vc_discount" name="vc_discount" required />
                    </div>
                    
                    <div>
                        <label for="vc_min_order" class="block text-sm font-medium text-gray-700 mb-1">Min Order *</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="vc_min_order" name="vc_min_order" required />
                    </div>
                    
                    <div>
                        <label for="vc_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="vc_type" name="vc_type" required>
                            <option value="">- Pilih -</option>
                            <option value="amount">Nominal</option>
                            <option value="percent">Persen</option>
                            <option value="gift">Gift</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="vc_reuse" class="block text-sm font-medium text-gray-700 mb-1">Dapat digunakan berkali - kali *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="vc_reuse" name="vc_reuse" required>
                            <option value="">- Pilih -</option>
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                            <option value="2">1 Bulan Sekali</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="vc_status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="vc_status" name="vc_status" required>
                            <option value="">- Pilih -</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="vc_cashback" class="block text-sm font-medium text-gray-700 mb-1">Apakah Cashback ? *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="vc_cashback" name="vc_cashback" required>
                            <option value="">- Pilih -</option>
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="vc_platform" class="block text-sm font-medium text-gray-700 mb-1">Platform *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="vc_platform" name="vc_platform" required>
                            <option value="">- Pilih -</option>
                            <option value="web">Website</option>
                            <option value="all">Semua</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="vc_due_date" class="block text-sm font-medium text-gray-700 mb-1">Batas Berakhir *</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="vc_due_date" name="vc_due_date" required />
                    </div>
                    
                    <div id="random_input" class="hidden">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="is_random" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                            <span class="text-sm font-medium text-gray-700">Buat Banyak dan Random ?</span>
                        </label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mt-2 hidden" id="vc_qty" name="vc_qty" placeholder="berapa banyak ?"/>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button type="button" onclick="closeVoucherModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        Tutup
                    </button>
                    <button type="button" id="delete_voucher_btn" class="hidden px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Hapus
                    </button>
                    <button type="submit" id="save_voucher_btn" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function closeVoucherModal() {
    document.getElementById('VoucherModal').classList.add('hidden');
}
</script>
