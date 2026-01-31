<!-- Investor Modal -->
<div id="InvestorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto" style="padding: 1rem;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-auto my-8" style="max-height: 90vh; overflow-y: auto;">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 border-b border-blue-700 flex justify-between items-center rounded-t-2xl">
            <h5 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-user-tie mr-3"></i>
                {{ $data['subtitle'] }}
            </h5>
            <button type="button" class="text-white hover:text-gray-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg" id="close_investor_btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="f_investor">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="p-6 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Store Access <span class="text-red-500">*</span></label>
                        <select class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="st_id" name="st_id" required>
                            <option value="">- Pilih -</option>
                            @foreach ($data['st_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="st_id_parent"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                        <input type="text" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="i_name" name="i_name" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                        <input type="text" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="i_username" name="i_username" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="i_email" name="i_email" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No Telp</label>
                        <input type="number" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="i_phone" name="i_phone" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                        <input type="text" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="i_address" name="i_address" />
                    </div>
                </div>
            </div>
            <div class="bg-gray-100 px-6 py-4 border-t border-gray-200 flex justify-end gap-2 rounded-b-2xl">
                <button type="button" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50" id="close_investor_btn_2">
                    Tutup
                </button>
                <button type="button" class="px-6 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 hidden" id="delete_investor_btn">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900" id="save_investor_btn">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
