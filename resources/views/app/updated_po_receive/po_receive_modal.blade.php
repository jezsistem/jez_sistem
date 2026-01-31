<!-- PO Detail Modal -->
<div id="PoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="bg-gray-100 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h5 class="text-lg font-semibold text-gray-900">PO <span id="po_invoice_label"></span></h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" id="close_po_modal">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <input type="hidden" id="_po_id" />
                <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-full" id="po_search" placeholder="Cari artikel">
            </div>
            <div class="overflow-x-auto">
                <table id="PoTable" class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3">No</th>
                            <th scope="col" class="px-3 py-3">Artikel</th>
                            <th scope="col" class="px-3 py-3 text-right">Qty PO</th>
                            <th scope="col" class="px-3 py-3 text-right">Qty Terima</th>
                            <th scope="col" class="px-3 py-3 text-right">Total PO</th>
                            <th scope="col" class="px-3 py-3 text-right">Total Terima</th>
                        </tr>
                    </thead>
                    <tbody id="po_tbody">
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-gray-500">
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <div id="po_pagination_info" class="text-sm text-gray-700"></div>
                <div id="po_pagination_controls" class="flex gap-2"></div>
            </div>
        </div>
        <div class="bg-gray-100 px-6 py-4 border-t border-gray-200 flex justify-end">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50" id="close_po_modal_btn">
                Tutup
            </button>
        </div>
    </div>
</div>
