<!-- Pickup List Modal -->
<div id="PickupListModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closePickupListModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Pickup List</h3>
                <button type="button" onclick="closePickupListModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 max-h-[80vh] overflow-y-auto">
                <div class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="pick_data_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-blue-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari artikel">
                </div>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="PickupListtb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Storage Area</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 border-t border-gray-200">
                <button type="button" onclick="closePickupListModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Waiting List Modal -->
<div id="WaitingListModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeWaitingListModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Waiting Offline List</h3>
                <button type="button" onclick="closeWaitingListModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 max-h-[80vh] overflow-y-auto">
                <div class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="waiting_data_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-blue-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari artikel">
                </div>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="WaitingListtb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">BIN</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 border-t border-gray-200">
                <button type="button" onclick="closeWaitingListModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filter List Modal -->
<div id="FilterListModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeFilterListModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Filter List Product</h3>
                <button type="button" onclick="closeFilterListModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 max-h-[80vh] overflow-y-auto">
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="FilterListtb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Size</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">HB / HJ</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Area</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">QTY</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 border-t border-gray-200">
                <button type="button" onclick="closeFilterListModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Change Display Modal -->
<div id="ChangeDisplayModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeChangeDisplayModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_ganti_display">
                @csrf
                <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Ganti Display</h3>
                    <button type="button" onclick="closeChangeDisplayModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 space-y-4">
                    <div id="reader_change_display" class="rounded-lg" style="max-width: 400px;"></div>
                    <div id="result_display"></div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SKU <span class="text-red-500">*</span></label>
                        <input type="text" id="sku_display" name="sku" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <p id="product_name" class="text-sm text-gray-600">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Variant</label>
                        <p id="product_variant" class="text-sm text-gray-600">-</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200">
                    <button type="button" onclick="closeChangeDisplayModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPickupListModal() {
    document.getElementById('PickupListModal').classList.remove('hidden');
    document.getElementById('PickupListModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}
function closePickupListModal() {
    document.getElementById('PickupListModal').classList.add('hidden');
    document.getElementById('PickupListModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function openWaitingListModal() {
    document.getElementById('WaitingListModal').classList.remove('hidden');
    document.getElementById('WaitingListModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}
function closeWaitingListModal() {
    document.getElementById('WaitingListModal').classList.add('hidden');
    document.getElementById('WaitingListModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function openFilterListModal() {
    document.getElementById('FilterListModal').classList.remove('hidden');
    document.getElementById('FilterListModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}
function closeFilterListModal() {
    document.getElementById('FilterListModal').classList.add('hidden');
    document.getElementById('FilterListModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function openChangeDisplayModal() {
    document.getElementById('ChangeDisplayModal').classList.remove('hidden');
    document.getElementById('ChangeDisplayModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}
function closeChangeDisplayModal() {
    document.getElementById('ChangeDisplayModal').classList.add('hidden');
    document.getElementById('ChangeDisplayModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}
</script>
