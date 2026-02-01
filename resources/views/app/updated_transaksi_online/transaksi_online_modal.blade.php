<!-- Import Modal -->
<div id="ImportModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeImportModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Import Data</h3>
                        <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Download Template <span class="text-red-500">*</span>
                            </label>
                            <div class="relative inline-block">
                                <button type="button" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                    Download
                                </button>
                                <div class="hidden absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                    <ul class="py-2">
                                        <li class="px-4 py-2 text-sm text-gray-700 font-semibold border-b border-gray-200">Template File :</li>
                                        <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                            <a href="#" id="download_template_shopee" class="flex items-center text-sm text-gray-700">
                                                <i class="fas fa-download mr-2"></i>
                                                <span>Shopee</span>
                                            </a>
                                        </li>
                                        <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                            <a href="#" id="download_template_tiktok" class="flex items-center text-sm text-gray-700">
                                                <i class="fas fa-download mr-2"></i>
                                                <span>Tiktok</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih template yang sudah di download dan diisi <span class="text-red-500">*</span>
                            </label>
                            <input type="hidden" value="{{ \Illuminate\Support\Facades\Auth::user()->st_id }}" name="st_id_form" id="st_id_form" disabled>
                            <input type="file" name="importFile" id="importFile" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
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

<!-- Detail Modal -->
<div id="DetailModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeDetailModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-7xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Item Pesanan #<span id="num_order"></span></h3>
                    <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <input type="hidden" id="to_id" value=""/>
                <input type="hidden" id="status_pesanan" value=""/>
                <div class="mb-4">
                    <button type="button" id="print_invoice" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h3 class="text-red-800 font-semibold">Important!! Harap perhatikan status pick sebelum cetak nota 🐈🐈</h3>
                </div>
                <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="Detailtb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU MP</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Platform Price</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jez Price</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Diff Price</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Discount Seller</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">N.S Before Admin</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Final Price</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status Pick</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by SimpleDatatables -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                <button type="button" onclick="closeDetailModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function closeImportModal() {
    document.getElementById('ImportModal').classList.add('hidden');
}

function closeDetailModal() {
    document.getElementById('DetailModal').classList.add('hidden');
}
</script>
