<!-- Topdeals Modal -->
<div id="TopdealsModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeTopdealsModal()"></div>
        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
            <form id="f_topdeals">
                @csrf
                <input type="hidden" name="_id" id="topdeals_modal_id" value="">
                <input type="hidden" name="_mode" id="topdeals_modal_mode" value="">

                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Topdeal</h3>
                    <button type="button" onclick="closeTopdealsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="py-4">
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Nama Topdeals</label>
                        <input type="text" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" id="td_name" name="td_name" required />
                    </div>
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Berakhir</label>
                        <input type="date" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" id="td_due_date" name="td_due_date" required>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Jam Berakhir</label>
                        <input type="text" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" id="td_due_time" name="td_due_time" required placeholder="HH:MM:SS">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                        <select class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" id="td_status" name="td_status" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeTopdealsModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="button" id="delete_topdeals_btn" class="hidden px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700">
                        Hapus
                    </button>
                    <button type="submit" id="save_topdeals_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Article Modal -->
<div id="ArticleModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeArticleModal()"></div>
        <div class="inline-block w-full max-w-6xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Topdeals Artikel</h3>
                <button type="button" onclick="closeArticleModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="py-4">
                <div class="mb-4">
                    <button id="add_topdeals_article_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                        <i class="fas fa-plus mr-2"></i>Data Baru
                    </button>
                </div>

                <input type="hidden" id="_td_id" value="" />
                <div class="overflow-x-auto">
                    <table id="TopdealsArticletb" class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3">No</th>
                                <th scope="col" class="px-3 py-3">Brand</th>
                                <th scope="col" class="px-3 py-3">Artikel</th>
                                <th scope="col" class="px-3 py-3">Warna</th>
                                <th scope="col" class="px-3 py-3"></th>
                            </tr>
                        </thead>
                        <tbody id="topdeals_article_tbody">
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center mt-4">
                    <div id="topdeals_article_pagination_info" class="text-sm text-gray-600"></div>
                    <div id="topdeals_article_pagination_controls" class="flex items-center gap-2"></div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closeArticleModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Article List Modal -->
<div id="ArticleListModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeArticleListModal()"></div>
        <div class="inline-block w-full max-w-6xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Data Artikel</h3>
                <button type="button" onclick="closeArticleListModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="py-4">
                <div class="mb-4">
                    <input type="search" id="article_list_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari article...">
                </div>

                <div class="overflow-x-auto">
                    <table id="ArticleListtb" class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3">No</th>
                                <th scope="col" class="px-3 py-3">Brand</th>
                                <th scope="col" class="px-3 py-3">Artikel</th>
                                <th scope="col" class="px-3 py-3">Warna</th>
                                <th scope="col" class="px-3 py-3"></th>
                            </tr>
                        </thead>
                        <tbody id="article_list_tbody">
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center mt-4">
                    <div id="article_list_pagination_info" class="text-sm text-gray-600"></div>
                    <div id="article_list_pagination_controls" class="flex items-center gap-2"></div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closeArticleListModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
