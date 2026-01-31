<!-- Import Modal -->
<div id="ImportModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Import Data</h3>
            <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_import" enctype="multipart/form-data">
            @csrf
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Download Template <span class="text-red-500">*</span></label>
                    <a href="{{ asset('upload/template/debt_template.xlsx') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Download</a>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih template yang sudah di download dan diisi <span class="text-red-500">*</span></label>
                    <input type="file" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="p_template" id="p_template" required/>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="submit" id="import_data_btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Debt List Modal -->
<div id="DebtListModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Hutang</h3>
            <button type="button" onclick="closeDebtListModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_debt">
            @csrf
            <input type="hidden" name="_id" id="debt_list_modal_id" value="" />
            <input type="hidden" name="_mode" id="debt_list_modal_mode" value="" />
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Store <span class="text-red-500">*</span></label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="st_id_data" name="st_id_data" required>
                        <option value="">- Pilih Store -</option>
                        @foreach ($data['st_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="ps_id" name="ps_id" required>
                        <option value="">- Pilih -</option>
                        @foreach ($data['ps_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand <span class="text-red-500">*</span></label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="br_id" name="br_id" required>
                        <option value="">- Pilih -</option>
                        @foreach ($data['br_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Invoice <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="dl_invoice" name="dl_invoice" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="dl_invoice_date" name="dl_invoice_date" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo <span class="text-red-500">*</span></label>
                    <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="dl_invoice_due_date" name="dl_invoice_due_date" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DPP <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="dl_value" name="dl_value" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">VAT</label>
                    <input type="number" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="dl_vat" name="dl_vat"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">TOTAL <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="dl_total" name="dl_total" readonly/>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeDebtListModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="button" id="delete_debt_list_btn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="submit" id="save_debt_list_btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Payment Modal -->
<div id="PaymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Pembayaran</h3>
            <button type="button" onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="mt-4">
            <button id="add_payment_btn" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900 mb-4">
                <i class="fas fa-plus mr-2"></i>Tambah Pembayaran
            </button>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3">No</th>
                            <th scope="col" class="px-3 py-3">Tanggal</th>
                            <th scope="col" class="px-3 py-3">Payment Value</th>
                        </tr>
                    </thead>
                    <tbody id="payment_tbody">
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
            <button type="button" onclick="closePaymentModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div id="AddPaymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Pembayaran</h3>
            <button type="button" onclick="closeAddPaymentModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_payment">
            @csrf
            <input type="hidden" name="_id_payment" id="payment_modal_id" value="" />
            <input type="hidden" name="_mode_payment" id="payment_modal_mode" value="" />
            <input type="hidden" name="dl_id" id="dl_id" value=""/>
            <input type="hidden" name="dl_value_payment" id="dl_value_payment" value=""/>
            <input type="hidden" name="payment" id="payment" value=""/>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="dlp_date" name="dlp_date" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Value <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="dlp_value" name="dlp_value" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kekurangan</label>
                    <input type="number" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="value_remain" name="value_remain" readonly/>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeAddPaymentModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="button" id="delete_payment_btn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="submit" id="save_payment_btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
