<!-- Out Modal (Barang Keluar) -->
<div id="OutModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeOutModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Barang Keluar</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeOutModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 max-h-[70vh] overflow-y-auto">
                <div id="pl_id_out_parent" class="mb-4"></div>
                <input type="search" 
                       id="out_search" 
                       placeholder="Cari brand artikel"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4"/>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500" id="Outtb">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3">Artikel</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeOutModal()"
                        id="out_modal_finish"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scan In Modal (Scan Barang Masuk) -->
<div id="ScanInModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeScanInModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Scan Barang Masuk</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeScanInModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="md:col-span-2">
                        <select id="waiting_filter" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm">
                            <option value='WAITING OFFLINE'>Waiting Offline</option>
                            <option value='WAITING ONLINE'>Waiting Online</option>
                            <option value='WAITING FOR CHECKOUT'>Waiting For Checkout</option>
                            <option value='REFUND'>Refund</option>
                            <option value='EXCHANGE'>Exchange</option>
                        </select>
                        <input type="hidden" id="filter_status" value="">
                    </div>
                    <div>
                        <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" id="btn_filter">Submit</button>
                    </div>
                </div>
                <div class="flex justify-center mb-4">
                    <div id="reader_scan_in" class="rounded"></div>
                    <div id="result"></div>
                </div>
                <input type="search" 
                       id="scan_in_search" 
                       placeholder="Cari brand artikel"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4"/>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500" id="ScanIntb">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3">Artikel</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeScanInModal()"
                        id="scan_in_modal_finish"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scan In Refund Modal -->
<div id="ScanInRefundModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeScanInRefundModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Scan Barang Masuk Refund</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeScanInRefundModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="md:col-span-2">
                        <select id="waiting_refund_filter" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm">
                            <option value='REFUND'>Refund</option>
                        </select>
                        <input type="hidden" id="filter_status" value="">
                    </div>
                    <div>
                        <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" id="btn_filter">Submit</button>
                    </div>
                </div>
                <div class="flex justify-center mb-4">
                    <div id="reader_scan_in_refund" class="rounded"></div>
                    <div id="result"></div>
                </div>
                <input type="search" 
                       id="scan_in_refund_search" 
                       placeholder="Cari brand artikel"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4"/>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500" id="ScanInRefundtb">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3">Artikel</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeScanInRefundModal()"
                        id="scan_in_refund_modal_finish"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Keep Online Modal -->
<div id="KeepOnModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeKeepOnModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Scan SKU Keep Online</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeKeepOnModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 max-h-[70vh] overflow-y-auto">
                <select id="waiting_filter_online" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm mb-4">
                    <option value='WAITING ONLINE'>Waiting Online</option>
                </select>
                <input type="search" 
                       id="scan_in_search" 
                       placeholder="Scan SKU" 
                       autofocus
                       autocomplete="off"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4"/>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500" id="ScanInOnlinetb">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3">Artikel</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeKeepOnModal()"
                        id="scan_in_modal_finish"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pick Online Modal -->
<div id="PickOnModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closePickOnModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Scan SKU Pick Online</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closePickOnModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 max-h-[70vh] overflow-y-auto">
                <div class="flex justify-center mb-4">
                    <div id="reader_pick_online" class="rounded"></div>
                    <div id="result"></div>
                </div>
                <select id="waiting_filter_online" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm mb-4">
                    <option value='WAITING ONLINE'>Waiting Online</option>
                </select>
                <input type="search" 
                       id="scan_pick_on_search" 
                       placeholder="Scan SKU" 
                       autofocus
                       autocomplete="off"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4"/>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500" id="ScanInOnlinetb">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3">Artikel</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closePickOnModal()"
                        id="scan_in_modal_finish"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Take Cross Order Modal -->
<div id="TakeCrossOrderModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeTakeCrossOrderModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Ambil Barang</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeTakeCrossOrderModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 max-h-[70vh] overflow-y-auto">
                <div id="cross_invoice_content"></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 d-none" id="TakeCrossOrdertb">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3">No</th>
                                <th scope="col" class="px-6 py-3">Artikel</th>
                                <th scope="col" class="px-6 py-3">Qty</th>
                                <th scope="col" class="px-6 py-3">BIN</th>
                                <th scope="col" class="px-6 py-3">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeTakeCrossOrderModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div id="TransferModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto" data-backdrop="static" data-keyboard="false">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeTransferModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Transfer</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeTransferModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <input type="hidden" id="_transfer_mode"/>
                <input type="hidden" id="transfer_invoice_label" value=""/>
                <div id="transfer_invoice_parent" class="mb-4">
                    <span class="transfer_invoice"></span>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeTransferModal()"
                        id="transfer_modal_finish"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scan Transfer Modal -->
<div id="ScanTransferModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto" data-backdrop="static" data-keyboard="false">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeScanTransferModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Transfer</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeScanTransferModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <input type="hidden" id="_scan_transfer_mode"/>
                <input type="hidden" id="scan_transfer_invoice_label" value=""/>
                <div id="scan_transfer_invoice_parent" class="mb-4">
                    <span class="scan_transfer_invoice"></span>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeScanTransferModal()"
                        id="scan_transfer_modal_finish"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="ChangePasswordModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeChangePasswordModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Ganti Password</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeChangePasswordModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="f_change_password">
                <div class="p-4 md:p-5">
                    <div class="mb-4">
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                    <div class="mb-4">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeChangePasswordModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scan Transfer Detail Modal -->
<div id="ScanTransferDetailModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeScanTransferDetailModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Transfer <span id="scan_transfer_invoice_modal_label"></span></h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeScanTransferDetailModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 max-h-[70vh] overflow-y-auto">
                <input type="search" 
                       id="scan_transfer_search"
                       placeholder="Brand-Artikel-Warna-Size"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4"/>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500" id="ScanTransferListtb">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3">No</th>
                                <th scope="col" class="px-6 py-3">Artikel</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeScanTransferDetailModal()"
                        id="scan_transfer_detail_modal_finish"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Detail Modal -->
<div id="TransferDetailModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeTransferDetailModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Transfer <span id="transfer_invoice_modal_label"></span></h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeTransferDetailModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 max-h-[70vh] overflow-y-auto">
                <input type="search" 
                       id="transfer_search"
                       placeholder="Brand-Artikel-Warna-Size"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4"/>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500" id="TransferListtb">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3">No</th>
                                <th scope="col" class="px-6 py-3">Artikel</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeTransferDetailModal()"
                        id="transfer_detail_modal_finish"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Order List Modal -->
<div id="OrderListModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeOrderListModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Produk No Pesanan <span id="invoice_number"></span></h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeOrderListModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 max-h-[70vh] overflow-y-auto">
                <div id="orderListItem"></div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeOrderListModal()"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- BIN Modal -->
<div id="binModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeBinModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">Pilih BIN <span id="product_name"></span>-<span id="plst_id"></span></h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeBinModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 max-h-[70vh] overflow-y-auto">
                <div id="pl_id_out_parent" class="mb-4"></div>
                <div class="flex justify-center mb-4">
                    <div id="reader_scan_bin_out" class="rounded" style="max-width: 500px;"></div>
                    <div id="result"></div>
                </div>
                <div class="mb-4">
                    <span class="text-sm text-gray-700">Bin Set : </span><br>
                    <span class="text-sm text-gray-700">SKU : </span><span id="sku_selected" class="text-sm font-medium"></span>
                    <input type="hidden" id="sku_send">
                </div>
                <input type="search" 
                       id="bin_out_search" 
                       placeholder="Cari nama bin"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4"/>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 border border-gray-200" id="binTable">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 border border-gray-200">BIN</th>
                                <th scope="col" class="px-6 py-3 border border-gray-200">QTY</th>
                                <th scope="col" class="px-6 py-3 border border-gray-200">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeBinModal()"
                        id="close_scan_out_modal"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Take Transfer Item Modal -->
<div id="TakeTransferItemModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto" data-backdrop="static" data-keyboard="false">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeTakeTransferItemModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-5xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gray-800">
                <h3 class="text-lg font-semibold text-white">Ambil Barang Transfer</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeTakeTransferItemModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 max-h-[70vh] overflow-y-auto">
                <input type="hidden" id="_stfd_id"/>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <strong class="text-red-800">Item:</strong> <span id="take_transfer_p_name_info" class="text-red-700">-</span>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <strong class="text-blue-800">Ambil dari BIN:</strong> <span id="take_transfer_bin_info" class="text-blue-700">-</span> &nbsp;|&nbsp;
                    <strong class="text-blue-800">SKU:</strong> <span id="take_transfer_sku_info" class="text-blue-700">-</span> &nbsp;|&nbsp;
                    <strong class="text-blue-800">Qty Diminta:</strong> <span id="take_transfer_qty_info" class="text-blue-700">-</span>
                </div>
                <div class="flex justify-center mb-4">
                    <div id="reader_take_transfer" class="rounded"></div>
                    <div id="result"></div>
                </div>
                <form id="take_transfer_item_form" class="flex flex-wrap gap-3 mb-4">
                    <input type="text" 
                           id="take_transfer_bin" 
                           placeholder="BIN" 
                           autocomplete="off" 
                           required
                           class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    <input type="text" 
                           id="take_transfer_barcode" 
                           placeholder="Barcode" 
                           autocomplete="off" 
                           required
                           class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    <input type="hidden" id="take_transfer_quantity" value="1">
                    <button type="submit" 
                            id="btn_submit_scan_item_transfer"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Submit
                    </button>
                </form>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 border border-gray-200" id="TakeTransferItemTable">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 border border-gray-200">BIN</th>
                                <th scope="col" class="px-6 py-3 border border-gray-200">SKU</th>
                                <th scope="col" class="px-6 py-3 border border-gray-200">Qty</th>
                                <th scope="col" class="px-6 py-3 border border-gray-200">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeTakeTransferItemModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
                <button type="button" 
                        id="btn_take_transfer_item"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                    Ambil
                </button>
            </div>
        </div>
    </div>
</div>
