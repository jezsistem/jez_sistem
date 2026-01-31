<!-- Main Menu Modal -->
<div id="MainMenuModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeMainMenuModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_main_menu">
                @csrf
                <input type="hidden" name="_id" id="main_menu_modal_id" value="" />
                <input type="hidden" name="_mode" id="main_menu_modal_mode" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $data['subtitle'] }}</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeMainMenuModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="mt_title" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $data['subtitle'] }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="mt_title" 
                               name="mt_title" 
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="mt_sort" class="block text-sm font-medium text-gray-700 mb-2">
                            Urutan Menu <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="mt_sort" 
                               name="mt_sort" 
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeMainMenuModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="button" 
                            id="delete_main_menu_btn" 
                            class="hidden px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700">
                        Hapus
                    </button>
                    <button type="submit" 
                            id="save_main_menu_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
