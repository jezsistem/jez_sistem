<!-- Payment Method Modal -->
<div id="PaymentMethodModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Metode Pembayaran</h3>
            <button type="button" onclick="closePaymentMethodModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_payment_method">
            @csrf
            <input type="hidden" name="_id" id="payment_method_modal_id" value="" />
            <input type="hidden" name="_mode" id="payment_method_modal_mode" value="" />
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Divisi <span class="text-red-500">*</span></label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="stt_id" name="stt_id" required>
                        <option value="">- Pilih Divisi -</option>
                        @foreach ($data['stt_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="pm_name" name="pm_name" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Relasi Akun <span class="text-red-500">*</span></label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="a_id" name="a_id" required>
                        <option value="">- Pilih Relasi -</option>
                        @foreach ($data['a_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="pm_description" name="pm_description" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Toko <span class="text-red-500">*</span></label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="st_id" name="st_id[]" multiple required>
                        @foreach ($data['st_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Gunakan Ctrl/Cmd untuk memilih multiple toko</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closePaymentMethodModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="button" id="delete_payment_method_btn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="submit" id="save_payment_method_btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
