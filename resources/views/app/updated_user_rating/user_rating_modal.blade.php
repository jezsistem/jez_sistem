<!-- Sales Item Detail Modal -->
<div id="SalesItemDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto" style="padding: 1rem;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-auto my-8" style="max-height: 90vh; overflow-y: auto;">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 border-b border-blue-700 flex justify-between items-center rounded-t-2xl">
            <h5 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-receipt mr-3"></i>
                <span id="sales_item_detail_label"></span>
            </h5>
            <button type="button" class="text-white hover:text-gray-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg" id="close_sales_item_detail_btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 bg-gray-50">
            <input type="hidden" name="pt_id" id="pt_id" value=""/>
            <div class="overflow-x-auto">
                <table id="SalesItemDetailTable" class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3">No</th>
                            <th scope="col" class="px-3 py-3">Artikel</th>
                            <th scope="col" class="px-3 py-3">Qty</th>
                            <th scope="col" class="px-3 py-3">Total</th>
                            <th scope="col" class="px-3 py-3">Tanggal Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="sales_item_detail_tbody">
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-gray-500">
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-gray-100 px-6 py-4 border-t border-gray-200 flex justify-end rounded-b-2xl">
            <button type="button" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50" id="close_sales_item_detail_btn_2">
                Tutup
            </button>
        </div>
    </div>
</div>
