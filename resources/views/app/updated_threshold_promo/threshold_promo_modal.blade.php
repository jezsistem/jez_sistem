<!-- Import Modal -->
<div id="ImportModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeImportModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Import Data</h3>
                    <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Download Template <span class="text-red-500">*</span>
                        </label>
                        <a href="{{ asset('upload/template/template_recom_promo.xlsx') }}" class="inline-block px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>Download
                        </a>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih template yang sudah di download dan diisi <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="promo_recommendation_template" id="promo_recommendation_template" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
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

<!-- Promo Recommendation Detail Modal -->
<div id="PromoRecommendationDetailModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closePromoRecommendationDetailModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Detail Threshold Promo <span class="pr_code"></span></h3>
                <div class="flex items-center gap-3">
                    <button type="button" id="ExportArticleData" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>Export
                    </button>
                    <button type="button" onclick="closePromoRecommendationDetailModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="PromoRecommendationDetailtb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Article ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Article Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Promo Disc (%)</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Price Tag</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Price Discount</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Note Promo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by SimpleDatatables -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                @if (Auth::user()->delete_access == '1')
                    <button type="button" id="delete_promo_recommendation" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors">
                        Hapus
                    </button>
                @endif
                <button type="button" onclick="closePromoRecommendationDetailModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Threshold Promo Modal -->
<div id="EditThresholdPromoModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeEditThresholdPromoModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <form id="edit_threshold_promo_form">
                @csrf
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Threshold Promo</h3>
                    <button type="button" onclick="closeEditThresholdPromoModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Diskon (%) <span class="text-red-500">*</span>
                        </label>
                        <input type="hidden" id="edit_threshold_promo_id" name="edit_threshold_promo_id">
                        <input type="number" id="threshold_discount" name="threshold_discount" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <button type="button" onclick="closeEditThresholdPromoModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                    <button type="button" id="save_edit_threshold_promo_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function closeImportModal() {
    document.getElementById('ImportModal').classList.add('hidden');
}

function closePromoRecommendationDetailModal() {
    document.getElementById('PromoRecommendationDetailModal').classList.add('hidden');
}

function closeEditThresholdPromoModal() {
    document.getElementById('EditThresholdPromoModal').classList.add('hidden');
}
</script>
