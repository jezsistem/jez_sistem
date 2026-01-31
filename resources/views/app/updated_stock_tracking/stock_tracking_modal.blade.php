<!-- Graph Modal -->
<div id="GraphModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeGraphModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Grafik <small class="text-gray-500"><i>* silahkan pilih status grafik pada opsi dibawah</i></small></h3>
                <button type="button" onclick="closeGraphModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <select id="chart_filter" name="chart_filter" class="w-full px-4 py-2.5 mb-4 bg-blue-600 text-white text-sm font-medium rounded-lg border-0 focus:ring-2 focus:ring-blue-500">
                    <option value="sales">Terjual</option>
                    <option value="offline">Waiting Offline</option>
                    <option value="online">Waiting Online</option>
                    <option value="instock">Instock Lihat - lihat</option>
                    <option value="sales_instock">Instock Refund Exchange</option>
                </select>
                <div id="chart"></div>
            </div>
            <div class="flex items-center justify-end p-4 border-t border-gray-200">
                <button type="button" onclick="closeGraphModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- User Detail Modal -->
<div id="UserModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeUserModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900"><span id="article_label"></span></h3>
                <button type="button" onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-700">Invoice</td>
                                <td class="px-4 py-3 text-sm text-gray-900"><span id="invoice_label"></span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-700">Picker</td>
                                <td class="px-4 py-3 text-sm text-gray-900"><span id="picker_label"></span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-700">Cashier</td>
                                <td class="px-4 py-3 text-sm text-gray-900"><span id="cashier_label"></span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-700">Customer</td>
                                <td class="px-4 py-3 text-sm text-gray-900"><span id="customer_label"></span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-700">Helper</td>
                                <td class="px-4 py-3 text-sm text-gray-900"><span id="helper_label"></span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-700">Packer</td>
                                <td class="px-4 py-3 text-sm text-gray-900"><span id="packer_label"></span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-700">Status</td>
                                <td class="px-4 py-3 text-sm text-gray-900"><span id="status_label"></span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-700">Invoice Note</td>
                                <td class="px-4 py-3 text-sm text-gray-900"><span id="invoice_note_label"></span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-700">Refund/Exchange Note</td>
                                <td class="px-4 py-3 text-sm text-gray-900"><span id="refund_exchange_note_label"></span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-700">Pergerakan Terakhir</td>
                                <td class="px-4 py-3 text-sm text-gray-900"><span id="last_updated_label"></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200">
                @if ($data['user']->g_name == 'administrator')
                <button type="button" id="delete_btn" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors" data-id="">Hapus</button>
                @endif
                <button type="button" onclick="closeUserModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openGraphModal() {
    document.getElementById('GraphModal').classList.remove('hidden');
    document.getElementById('GraphModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}
function closeGraphModal() {
    document.getElementById('GraphModal').classList.add('hidden');
    document.getElementById('GraphModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function openUserModal() {
    document.getElementById('UserModal').classList.remove('hidden');
    document.getElementById('UserModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}
function closeUserModal() {
    document.getElementById('UserModal').classList.add('hidden');
    document.getElementById('UserModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}
</script>
