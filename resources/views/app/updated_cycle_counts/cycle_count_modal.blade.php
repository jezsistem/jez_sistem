<!-- Import Modal -->
<div id="ImportModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeImportModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Import Data</h3>
                    <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih template yang sudah diisi dari hasil export template <span class="text-red-500">*</span></label>
                        <input type="file" name="template" id="template" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Adjustment <span class="text-red-500">*</span></label>
                        <select name="tipe_adjustment" id="tipe_adjustment" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Tipe Adjustment --</option>
                            <option value="KERUGIAN">KERUGIAN</option>
                            <option value="BELUM TERBAYAR">BELUM TERBAYAR</option>
                            <option value="TERBAYAR">TERBAYAR</option>
                            <option value="BIAYA PROMOSI">BIAYA PROMOSI</option>
                            <option value="BIAYA OPERASIONAL">BIAYA OPERASIONAL</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note Adjustment <span class="text-red-500">*</span></label>
                        <select id="note_adjustment" name="note_adjustment" required class="w-full px-4 py-2.5 border-2 border-black rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Note Adjustment --</option>
                            <option value="STOCK OPNAME">STOCK OPNAME</option>
                            <option value="PARTIAL">PARTIAL</option>
                            <option value="REJECT">REJECT</option>
                            <option value="CACAT">CACAT</option>
                            <option value="PERBAIKAN">PERBAIKAN</option>
                            <option value="PROMOSI">PROMOSI</option>
                            <option value="OPERASIONAL">OPERASIONAL</option>
                            <option value="SSR">SSR</option>
                            <option value="RESELLER">RESELLER</option>
                            <option value="KESALAHAN SYSTEM">KESALAHAN SYSTEM</option>
                            <option value="CYCLE COUNT">CYCLE COUNT</option>
                            <option value="RETUR IN">RETUR IN</option>
                            <option value="RETUR OUT">RETUR OUT</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" id="import_data_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openImportModal() {
    var modal = document.getElementById('ImportModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        // Don't add overflow-hidden to body as it may block clicks
    }
}
function closeImportModal() {
    var modal = document.getElementById('ImportModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        // Ensure overflow-hidden is removed
        document.body.classList.remove('overflow-hidden');
        document.body.style.overflow = '';
    }
}

// Ensure modal closes when clicking outside - modal already has onclick
document.addEventListener('DOMContentLoaded', function() {
    console.log('Modal event listener initialized');
});
</script>
