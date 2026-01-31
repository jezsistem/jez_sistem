<!-- Stock Transfer Data Modal -->
<div id="StockTransferDataModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeStockTransferDataModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-5xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Terima Transfer</h3>
                <div class="flex items-center gap-2">
                    <button type="button" id="ExportBtn" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors flex items-center gap-2">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                    <button type="button" id="ImportModalBtn" class="px-4 py-2 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors flex items-center gap-2 border border-blue-200">
                        <i class="fas fa-file-import"></i>
                        Import
                    </button>
                    <button type="button" onclick="closeStockTransferDataModal()" class="text-gray-400 hover:text-gray-600 transition-colors ml-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-4 space-y-4 max-h-[80vh] overflow-y-auto">
                <!-- Scanner Section -->
                <div class="flex justify-center">
                    <div id="reader_tf_receive" class="rounded-lg" style="max-width: 500px;"></div>
                </div>
                <div id="result"></div>
                
                <!-- Scan Result Input -->
                <div class="flex justify-center">
                    <input type="text" id="scan_result" class="w-full max-w-md px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center" placeholder="Hasil Scan">
                </div>
                
                <!-- Search Input -->
                <div class="flex justify-center">
                    <div class="relative w-full max-w-md">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="search" id="stock_transfer_receive_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari artikel">
                    </div>
                </div>
                
                <!-- Accept Button -->
                <div class="flex justify-end mb-4">
                    <button type="button" id="accept_qty_btn" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Terima
                    </button>
                </div>
                
                <!-- Hidden Fields -->
                <input type="hidden" id="stf_id" value="">
                <input type="hidden" id="stf_code_label" value="">
                <input type="hidden" id="st_id_end_modal" value="">
                
                <!-- Accept Table -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="StockTransferDataAccepttb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Brand</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Diterima</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Terima</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200">
                <button type="button" onclick="closeStockTransferDataModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="ImportModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-[60] overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeImportModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih template yang sudah diisi data <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="importFile" id="importFile" accept=".csv" required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
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
function openStockTransferDataModal() {
    var modal = document.getElementById('StockTransferDataModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }
}

function closeStockTransferDataModal() {
    var modal = document.getElementById('StockTransferDataModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    }
}

function openImportModal() {
    var modal = document.getElementById('ImportModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
    }
}

function closeImportModal() {
    var modal = document.getElementById('ImportModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
    }
}
</script>
