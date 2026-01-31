<!-- Sales Detail Modal -->
<div id="SalesDetailModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeSalesDetailModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900" id="sales_detail_label">Detail Penjualan</h3>
                <button type="button" onclick="closeSalesDetailModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <input type="hidden" name="u_id" id="u_id" value=""/>
                <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="SalesDetailtb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by SimpleDatatables -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 bg-gray-50">
                <button type="button" onclick="closeSalesDetailModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sales Item Detail Modal -->
<div id="SalesItemDetailModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeSalesItemDetailModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900" id="sales_item_detail_label">Detail Item Penjualan</h3>
                <button type="button" onclick="closeSalesItemDetailModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <input type="hidden" name="pt_id" id="pt_id" value=""/>
                <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="SalesItemDetailtb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by SimpleDatatables -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 bg-gray-50">
                <button type="button" onclick="closeSalesItemDetailModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function closeSalesDetailModal() {
    document.getElementById('SalesDetailModal').classList.add('hidden');
}

function closeSalesItemDetailModal() {
    document.getElementById('SalesItemDetailModal').classList.add('hidden');
}
</script>
