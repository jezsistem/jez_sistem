<!-- Group Modal -->
<div id="GroupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto" style="padding: 1rem;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-auto my-8">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 border-b border-blue-700 flex justify-between items-center rounded-t-2xl">
            <h5 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-users mr-3"></i>
                User Level
            </h5>
            <button type="button" class="text-white hover:text-gray-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg" id="close_group_btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="f_group">
            @csrf
            <input type="hidden" name="_id_gr" id="_id_gr" value="" />
            <input type="hidden" name="_mode_gr" id="_mode_gr" value="" />
            <div class="p-6 bg-gray-50">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Level <span class="text-red-500">*</span></label>
                    <input type="text" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="gr_name" name="gr_name" required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <input type="text" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="gr_description" name="gr_description" />
                </div>
            </div>
            <div class="bg-gray-100 px-6 py-4 border-t border-gray-200 flex justify-end gap-2 rounded-b-2xl">
                <button type="button" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50" id="close_group_btn_2">
                    Tutup
                </button>
                <button type="button" class="px-6 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 hidden" id="delete_group_btn">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900" id="save_group_btn">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- User Modal -->
<div id="UserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto" style="padding: 1rem;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-auto my-8" style="max-height: 90vh; overflow-y: auto;">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 border-b border-blue-700 flex justify-between items-center rounded-t-2xl">
            <h5 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-user mr-3"></i>
                Data User
            </h5>
            <button type="button" class="text-white hover:text-gray-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg" id="close_user_btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="f_user">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="p-6 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Level <span class="text-red-500">*</span></label>
                        <select class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="gr_id" name="gr_id" required>
                            <option value="">- Pilih -</option>
                            @foreach ($data['gr_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="gr_id_parent"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Akses Hapus Data <span class="text-red-500">*</span></label>
                        <select class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="delete_access" name="delete_access" required>
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Store <span class="text-red-500">*</span></label>
                        <select class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="st_id" name="st_id" required>
                            <option value="">- Pilih -</option>
                            @foreach ($data['st_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="st_id_parent"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Divisi <span class="text-red-500">*</span></label>
                        <select class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="stt_id" name="stt_id" required>
                            <option value="">- Pilih -</option>
                            @foreach ($data['stt_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="stt_id_parent"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                        <input type="text" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="u_name" name="u_name" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                        <input type="text" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="u_nip" name="u_nip" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">KTP</label>
                        <input type="number" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="u_ktp" name="u_ktp" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode</label>
                        <input type="text" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="u_secret_code" name="u_secret_code" placeholder="isi jika sales offline, bisa huruf dan angka" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="u_email" name="u_email" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="u_password" name="u_password" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No Telp <span class="text-red-500">*</span></label>
                        <input type="number" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="u_phone" name="u_phone" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                        <input type="text" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="u_address" name="u_address" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai Bekerja <span class="text-red-500">*</span></label>
                        <input type="date" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="join_date" name="join_date" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Masa Aktif <span class="text-red-500">*</span></label>
                        <input type="date" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="u_active" name="u_active" required />
                    </div>
                </div>
            </div>
            <div class="bg-gray-100 px-6 py-4 border-t border-gray-200 flex justify-end gap-2 rounded-b-2xl">
                <button type="button" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50" id="close_user_btn_2">
                    Tutup
                </button>
                <button type="button" class="px-6 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 hidden" id="delete_user_btn">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900" id="save_user_btn">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Menu Access Modal -->
<div id="MenuAccessModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto" style="padding: 1rem;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full mx-auto my-8" style="max-height: 90vh; overflow-y: auto;">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-5 border-b border-purple-700 flex justify-between items-center rounded-t-2xl">
            <h5 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-list mr-3"></i>
                Menu Access
            </h5>
            <button type="button" class="text-white hover:text-gray-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg" id="close_menu_access_btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 bg-gray-50">
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <div id="menu_panel" class="flex flex-wrap gap-2"></div>
                    <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700" id="add_menu_access_btn">
                        <i class="fas fa-plus mr-2"></i>Tambah
                    </button>
                </div>
                <div class="mb-4">
                    <input type="text" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="ma_id_access" placeholder="Cari menu..." />
                    <input type="hidden" id="ma_id_access_hidden" />
                    <div id="menuList" class="mt-2 hidden"></div>
                </div>
                <div class="mb-4">
                    <input type="text" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="st_id_access" placeholder="Cari store..." />
                    <input type="hidden" id="st_id_access_hidden" />
                    <div id="storeList" class="mt-2 hidden"></div>
                </div>
            </div>
            <div class="mb-4">
                <input type="search" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="menu_search" placeholder="Cari menu access..." />
            </div>
            <div class="overflow-x-auto">
                <table id="MenuAccessTable" class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3">No</th>
                            <th scope="col" class="px-3 py-3">Menu <small class="text-gray-500">* klik nama menu untuk setup default login</small></th>
                            <th scope="col" class="px-3 py-3">Default</th>
                            <th scope="col" class="px-3 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody id="menu_access_tbody">
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-gray-500">
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <div id="menu_access_pagination_info" class="text-sm text-gray-700"></div>
                <div id="menu_access_pagination_controls" class="flex gap-2"></div>
            </div>
        </div>
        <div class="bg-gray-100 px-6 py-4 border-t border-gray-200 flex justify-end rounded-b-2xl">
            <button type="button" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50" id="close_menu_access_btn_2">
                Tutup
            </button>
        </div>
    </div>
</div>
