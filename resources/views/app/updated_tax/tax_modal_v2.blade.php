<!-- Tax Modal -->
<div id="TaxModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Pajak</h3>
            <button type="button" onclick="closeTaxModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_tax">
            @csrf
            <input type="hidden" name="_id" id="tax_modal_id" value="" />
            <input type="hidden" name="_mode" id="tax_modal_mode" value="" />
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pajak <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="tx_code" name="tx_code" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pajak <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="tx_name" name="tx_name" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Akun Pembelian <span class="text-red-500">*</span></label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="a_id_purchase" name="a_id_purchase" required>
                        <option value="">- Pilih -</option>
                        @foreach ($data['a_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Akun Penjualan <span class="text-red-500">*</span></label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="a_id_sell" name="a_id_sell" required>
                        <option value="">- Pilih -</option>
                        @foreach ($data['a_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Presentase NPWP <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="tx_npwp" name="tx_npwp" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Presentase Non NPWP</label>
                    <input type="number" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="tx_non_npwp" name="tx_non_npwp" />
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeTaxModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="button" id="delete_tax_btn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="submit" id="save_tax_btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
