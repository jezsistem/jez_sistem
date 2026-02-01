<!-- Import Modal -->
<div id="ImportModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeImportModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Import Data</h3>
                    <button type="button" onclick="closeImportModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>Pastikan Anda menggunakan template import yang hanya berisi dua kolom:</strong>
                            <br>• Kolom kode SKU
                            <br>• Kolom Qty mutasi
                            <br><br>
                            Format file wajib <span class="text-red-500 font-bold">CSV</span>.
                            <br>
                            <span class="text-blue-600">Ekspor file dari Excel dengan memilih <strong>CSV (MS-DOS) (*.csv)</strong> pada saat menyimpan.</span>
                        </p>
                    </div>
                    <a href="{{ asset('upload/template/impot_single_bin_mutasi_template.xlsx') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-download"></i>
                        Download Template
                    </a>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih template yang sudah diisi data <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="importFile" id="importFile" accept=".csv" required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200">
                    <button type="button" onclick="closeImportModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" id="import_data_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mutation Multi Bin Modal -->
<div id="MutationMultiBinModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeMutationMultiBinModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Mutation Multi Bin</h3>
                <div class="flex items-center gap-3">
                    <a href="{{ asset('upload/template/mutation_multi_bin_template.xlsx') }}"
                       class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-download"></i>
                        Download Template
                    </a>
                    <button type="button" id="clearMutationMultiBinBtn"
                            class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors">
                        Clear
                    </button>
                    <button type="button" onclick="closeMutationMultiBinModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-4 space-y-4">
                <form id="mutationMultiBinForm" enctype="multipart/form-data" class="flex items-center gap-3">
                    @csrf
                    <label class="text-sm font-medium text-gray-700">Import Excel</label>
                    <input type="file" name="mutationInput" id="mutationInput"
                           class="flex-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <button type="submit" id="mutationMultiBinSubmitBtn"
                            class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
                        Import
                    </button>
                </form>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="mutationMultiBinTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 200px;">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 200px;">BIN Awal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 200px;">BIN Tujuan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Bin Awal (Current)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Mutasi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Rows will be dynamically added here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200">
                <button type="button" onclick="closeMutationMultiBinModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
                <button type="button" id="saveMutationMultiBinBtn"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                    Mutasi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openImportModal() {
    var modal = document.getElementById('ImportModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }
}

function closeImportModal() {
    var modal = document.getElementById('ImportModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    }
}

function openMutationMultiBinModal() {
    var modal = document.getElementById('MutationMultiBinModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }
}

function closeMutationMultiBinModal() {
    var modal = document.getElementById('MutationMultiBinModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    }
}
</script>
