<!-- Bank Modal -->
<div id="BankModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Akun Bank</h3>
            <button type="button" onclick="closeBankModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="f_bank" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_id" id="bank_modal_id" value="" />
            <input type="hidden" name="_mode" id="bank_modal_mode" value="" />
            <input type="hidden" name="_image" id="bank_modal_image" value="" />
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank <span class="text-xs text-gray-500">(jgn ubah yang sudah ada, kecuali buat baru)</span></label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="bank_name" name="bank_name" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima</label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="bank_account_name" name="bank_account_name" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="bank_number" name="bank_number" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo Bank</label>
                    <input type="file" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="bank_image" id="bank_image" accept="image/*" onchange="loadFile(event)" />
                    <div class="mt-2 text-center">
                        <img id="imagePreview" class="max-w-xs mx-auto rounded" style="display:none;" />
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeBankModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
                <button type="button" id="delete_bank_btn" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="submit" id="save_bank_btn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Simpan</button>
            </div>
        </form>
    </div>
</div>
