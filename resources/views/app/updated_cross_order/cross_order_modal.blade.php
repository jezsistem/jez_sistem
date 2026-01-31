<!-- Confirmation Modal -->
<div id="ConfirmationModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeConfirmationModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Konfirmasi</h3>
                <button type="button" onclick="closeConfirmationModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <input type="hidden" id="pt_id" value=""/>
                <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="Confirmationtb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bin</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ada</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by SimpleDatatables -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-200 bg-gray-50">
                <button type="button" onclick="closeConfirmationModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Tutup
                </button>
                <button type="button" id="save_confirmation_btn" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="DetailModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeDetailModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Detail</h3>
                <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <input type="hidden" id="pt_id" value=""/>
                <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="Detailtb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bin</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ada</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by SimpleDatatables -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 bg-gray-50">
                <button type="button" onclick="closeDetailModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Shipping Number Modal -->
<div id="ShippingNumberModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeShippingNumberModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <form id="f_shipping_number" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_cust_id" id="_cust_id" value="" />
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Shipping Number</h3>
                    <button type="button" onclick="closeShippingNumberModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <button type="button" id="check_resi_btn" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Cek Resi
                        </button>
                        <span class="text-xs text-gray-500">Pastikan data resi sudah benar sebelum melanjutkan.</span>
                    </div>
                    <div>
                        <label for="courier" class="block text-sm font-medium text-gray-700 mb-1">Kurir</label>
                        <select name="courier" id="courier" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">- Pilih -</option>
                        </select>
                    </div>
                    <div>
                        <label for="pos_shipping_number" class="block text-sm font-medium text-gray-700 mb-1">No Resi</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="pos_shipping_number" name="pos_shipping_number" required />
                    </div>
                    <div>
                        <label for="imageResi" class="block text-sm font-medium text-gray-700 mb-1">Upload File Resi <span class="text-red-500">*</span></label>
                        <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="imageResi" id="imageResi"/>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button type="button" onclick="closeShippingNumberModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" id="save_shipping_number_btn" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Waybill Tracking Modal -->
<div id="WaybillTrackingModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeWaybillTrackingModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Waybill Tracking</h3>
                <button type="button" onclick="closeWaybillTrackingModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div id="waybill_tracking"></div>
            </div>
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 bg-gray-50">
                <button type="button" onclick="closeWaybillTrackingModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div id="HistoryModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeHistoryModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">History</h3>
                <button type="button" onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="Historytb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipe</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lokasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Waktu</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by SimpleDatatables -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 bg-gray-50">
                <button type="button" onclick="closeHistoryModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Resi PDF Modal -->
<div id="resiModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeResiModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Resi PDF Preview</h3>
                <button type="button" onclick="closeResiModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <iframe id="resiPdfIframe" src="" frameborder="0" class="w-full" style="height: 80vh;"></iframe>
            </div>
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 bg-gray-50">
                <button type="button" onclick="closeResiModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function closeConfirmationModal() {
    document.getElementById('ConfirmationModal').classList.add('hidden');
}

function closeDetailModal() {
    document.getElementById('DetailModal').classList.add('hidden');
}

function closeShippingNumberModal() {
    document.getElementById('ShippingNumberModal').classList.add('hidden');
}

function closeWaybillTrackingModal() {
    document.getElementById('WaybillTrackingModal').classList.add('hidden');
}

function closeHistoryModal() {
    document.getElementById('HistoryModal').classList.add('hidden');
}

function closeResiModal() {
    document.getElementById('resiModal').classList.add('hidden');
}
</script>
