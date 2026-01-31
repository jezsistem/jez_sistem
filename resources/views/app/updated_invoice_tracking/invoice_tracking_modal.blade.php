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
                    <div>
                        <label for="courier" class="block text-sm font-medium text-gray-700 mb-1">Kurir</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="courier" name="courier" required>
                            <option value="">- Pilih -</option>
                            <option value="jne">JNE</option>
                            <option value="pos">POS Indonesia</option>
                            <option value="jnt">JNT</option>
                            <option value="sicepat">SiCepat</option>
                            <option value="tiki">TIKI</option>
                            <option value="anteraja">Anter Aja</option>
                            <option value="wahana">WAHANA</option>
                            <option value="ninja">Ninja</option>
                            <option value="lion">Lion Parcel</option>
                            <option value="pcp">PCP</option>
                            <option value="jet">JET</option>
                            <option value="rex">REX Express</option>
                            <option value="sap">SAP Express</option>
                            <option value="jxe">JX Express</option>
                            <option value="rpx">RPX Express</option>
                            <option value="first">First Logistics</option>
                            <option value="ide">ID Express</option>
                            <option value="spx">Shopee Express</option>
                            <option value="kgx">KGX Express</option>
                        </select>
                    </div>
                    <div>
                        <label for="pos_shipping_number" class="block text-sm font-medium text-gray-700 mb-1">No Resi</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="pos_shipping_number" name="pos_shipping_number" required />
                    </div>
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar Terkait Resi</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" type="file" name="image" id="image" accept="image/*" onchange="loadFile(event)"/>
                        <img id="imagePreview" style="width:40%; padding-top:10px; cursor: pointer;" class="mt-2"/>
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

<!-- Check Invoice Modal -->
<div id="CheckInvoiceModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeCheckInvoiceModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Periksa Invoice Terkait</h3>
                <button type="button" onclick="closeCheckInvoiceModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex gap-2">
                    <input type="search" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Input invoice exchange / refund" id="complaint_invoice"/>
                    <button type="button" id="search_complaint_btn" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        Cari
                    </button>
                </div>
                <div id="invoice_result"></div>
            </div>
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 bg-gray-50">
                <button type="button" onclick="closeCheckInvoiceModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- DP Payment Modal -->
<div id="DPPaymentModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeDPPaymentModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <form id="f_payment_dp">
                @csrf
                <input type="hidden" name="_pt_id" id="_pt_id" value="" />
                <div class="flex items-center justify-between px-6 py-4 border-b border-blue-600 bg-blue-600">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-money-bill-wave mr-2"></i>DP Payment
                    </h3>
                    <button type="button" onclick="closeDPPaymentModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Payment Summary Section -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h6 class="text-sm font-semibold text-gray-900 mb-4">
                            <i class="fas fa-calculator mr-2"></i>Ringkasan Pembayaran
                        </h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Harga Total:</label>
                                <div class="bg-white p-3 rounded border border-gray-200">
                                    <span class="text-blue-600 font-bold text-lg" id="total_payment_real_price">Rp 0</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Total Pembayaran Pertama:</label>
                                <div class="bg-white p-3 rounded border border-gray-200">
                                    <span class="text-green-600 font-bold text-lg" id="first_payment_amount">Rp 0</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Sisa Pembayaran:</label>
                            <div class="bg-yellow-100 p-3 rounded border border-yellow-300">
                                <span class="text-gray-900 font-bold text-lg" id="difference_payment">Rp 0</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">DP Dilakukan:</label>
                                <div class="bg-white p-2 rounded border border-gray-200">
                                    <span class="text-blue-600 font-medium text-sm" id="dp_date">-</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Method Pembayaran:</label>
                                <div class="bg-white p-2 rounded border border-gray-200">
                                    <span class="text-blue-600 font-medium text-sm" id="dp_method">-</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Catatan:</label>
                                <div class="bg-white p-2 rounded border border-gray-200">
                                    <span class="text-blue-600 font-medium text-sm" id="dp_notes">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form Section -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h6 class="text-sm font-semibold text-gray-900 mb-4">
                            <i class="fas fa-credit-card mr-2"></i>Detail Pembayaran
                        </h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="payment_method" name="payment_method" required>
                                    <option value="">- Pilih Metode Pembayaran -</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sub Payment</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="sub_payment" name="sub_payment">
                                    <option value="">- Pilih Sub Payment -</option>
                                    <option value="3">On Us</option>
                                    <option value="4">Off Us</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran <span class="text-red-500">*</span></label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
                                    <input type="number" class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="payment_dp" name="payment_dp" placeholder="0" required />
                                </div>
                                <small class="text-xs text-gray-500 mt-1">Masukkan jumlah pembayaran yang akan dibayar</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                                <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="payment_dp_date" name="payment_dp_date" required />
                                <small class="text-xs text-gray-500 mt-1">Pilih tanggal pembayaran</small>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="payment_notes" name="payment_notes" rows="3" placeholder="Masukkan catatan pembayaran (opsional)"></textarea>
                            <small class="text-xs text-gray-500 mt-1">Catatan tambahan untuk pembayaran ini</small>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button type="button" onclick="closeDPPaymentModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
                    <button type="submit" id="save_payment_dp_btn" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function closeShippingNumberModal() {
    document.getElementById('ShippingNumberModal').classList.add('hidden');
}

function closeWaybillTrackingModal() {
    document.getElementById('WaybillTrackingModal').classList.add('hidden');
}

function closeCheckInvoiceModal() {
    document.getElementById('CheckInvoiceModal').classList.add('hidden');
}

function closeDPPaymentModal() {
    document.getElementById('DPPaymentModal').classList.add('hidden');
}
</script>
