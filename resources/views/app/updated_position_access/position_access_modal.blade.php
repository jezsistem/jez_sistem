<!-- Position Access Modal -->
<div id="PositionAccessModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closePositionAccessModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_position_access">
                @csrf
                <input type="hidden" name="_id" id="position_access_modal_id" value="" />
                <input type="hidden" name="_mode" id="position_access_modal_mode" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Position Akses</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closePositionAccessModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Position <span class="text-red-500">*</span>
                        </label>
                        <div id="position_access_div">
                            <!-- Position dropdown will be loaded here -->
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closePositionAccessModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="save_position_access_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
