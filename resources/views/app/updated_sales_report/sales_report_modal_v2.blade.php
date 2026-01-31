<!-- Modal Summary Penjualan Cabang -->
<div id="CabangSummaryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Summary Penjualan Cabang</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="$('#CabangSummaryModal').addClass('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <div id="cabang_content" class="overflow-x-auto">
                <h3 class="text-lg font-semibold mb-4">Omset <span id="cabang_omset_date"></span></h3>
                <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-4 py-2 border">Omset Global</td>
                            <td class="px-4 py-2 border">Rp. <span id="cabang_omset"></span></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border font-bold"><b>Total Debit</b></td>
                            <td class="px-4 py-2 border font-bold"><b>Rp. <span id="cabang_total_debit"></span></b></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">* BCA</td>
                            <td class="px-4 py-2 border">Rp. <span id="cabang_total_debit_bca"></span></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">* QR Code</td>
                            <td class="px-4 py-2 border">Rp. <span id="cabang_total_qr"></span></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">* BRI</td>
                            <td class="px-4 py-2 border">Rp. <span id="cabang_total_debit_bri"></span></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">* BNI</td>
                            <td class="px-4 py-2 border">Rp. <span id="cabang_total_debit_bni"></span></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">* MANDIRI</td>
                            <td class="px-4 py-2 border">Rp. <span id="cabang_total_debit_mandiri"></span></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">* M-Banking / Transfer</td>
                            <td class="px-4 py-2 border">Rp. <span id="cabang_total_transfer"></span></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border font-bold"><b>Setoran</b></td>
                            <td class="px-4 py-2 border font-bold"><b>Rp. <span id="cabang_total_cash"></span></b></td>
                        </tr>
                        <tr id="cross_order_row">
                            <td class="px-4 py-2 border"><a class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 cursor-pointer" id="cabang_cross_order_detail">Cross Order</a></td>
                            <td class="px-4 py-2 border font-bold"><b>Rp. <span id="cabang_cross_order"></span></b></td>
                        </tr>
                    </tbody>
                </table>
                <table class="min-w-full divide-y divide-gray-200 border border-gray-300 mt-4">
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-4 py-2 border">Diverifikasi</td>
                            <td class="px-4 py-2 border">Disetujui</td>
                            <td class="px-4 py-2 border">Diketahui</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border" style="height: 80px;"></td>
                            <td class="px-4 py-2 border" style="height: 80px;"></td>
                            <td class="px-4 py-2 border" style="height: 80px;"></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">Finance</td>
                            <td class="px-4 py-2 border">Head/Kasir</td>
                            <td class="px-4 py-2 border">Head Sales and Marketing</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="button" onclick="cabangPdf()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                    Download PDF
                </button>
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300" onclick="$('#CabangSummaryModal').addClass('hidden')">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Cross Order Detail -->
<div id="CrossModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Cross Order Detail</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="$('#CrossModal').addClass('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <button type="button" data-type="cross" id="export_cross_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 mb-4">
                <i class="fas fa-download mr-2"></i>Export Excel
            </button>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-3 py-3">No</th>
                            <th class="px-3 py-3">Tanggal</th>
                            <th class="px-3 py-3">Invoice</th>
                            <th class="px-3 py-3">Brand</th>
                            <th class="px-3 py-3">Artikel</th>
                            <th class="px-3 py-3">Kategori</th>
                            <th class="px-3 py-3">Sub Kategori</th>
                            <th class="px-3 py-3">Sub Sub Kategori</th>
                            <th class="px-3 py-3">Warna</th>
                            <th class="px-3 py-3">Size</th>
                            <th class="px-3 py-3">Qty</th>
                            <th class="px-3 py-3">Bandrol</th>
                            <th class="px-3 py-3">Harga Jual</th>
                            <th class="px-3 py-3">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody id="cross_report_tbody">
                        <tr>
                            <td colspan="14" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300" onclick="$('#CrossModal').addClass('hidden')">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal HB HJ -->
<div id="HBHJModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Data Artikel</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="$('#HBHJModal').addClass('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 mb-4" id="hbhj_search" placeholder="Cari artikel"/>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-3 py-3">No</th>
                            <th class="px-3 py-3">Brand</th>
                            <th class="px-3 py-3">SKU</th>
                            <th class="px-3 py-3">Artikel</th>
                            <th class="px-3 py-3">Warna</th>
                            <th class="px-3 py-3">Size</th>
                            <th class="px-3 py-3">SubKategori</th>
                            <th class="px-3 py-3">Harga Beli</th>
                            <th class="px-3 py-3">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody id="hbhj_tbody">
                        <tr>
                            <td colspan="9" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4">
                <div id="hbhj_pagination_info" class="text-sm text-gray-600"></div>
                <div id="hbhj_pagination_controls" class="flex items-center gap-2"></div>
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300" onclick="$('#HBHJModal').addClass('hidden')">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Detail Customer -->
<div id="customerDetailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Detail Customer</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="$('#customerDetailModal').addClass('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-4" id="customer-detail-body">
            Loading...
        </div>
        <div class="flex justify-end mt-4">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300" onclick="$('#customerDetailModal').addClass('hidden')">
                Tutup
            </button>
        </div>
    </div>
</div>
