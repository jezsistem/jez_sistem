<!-- User Type Modal -->
<div id="UserTypeModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeUserTypeModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_user_type">
                @csrf
                <input type="hidden" name="_id" id="user_type_modal_id" value="" />
                <input type="hidden" name="_mode" id="user_type_modal_mode" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900" id="user_type_modal_title">User Type</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeUserTypeModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="ut_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="ut_code" 
                                   name="ut_code" 
                                   required
                                   maxlength="20"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Unique code (e.g. FT, PT, PF)</p>
                        </div>
                        <div>
                            <label for="ut_status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="ut_status" 
                                    name="ut_status" 
                                    required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="ut_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="ut_name" 
                               name="ut_name" 
                               required
                               maxlength="255"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Full name (e.g. Full Time, Part Time)</p>
                    </div>
                    <div>
                        <label for="ut_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="ut_description" 
                                  name="ut_description" 
                                  rows="3"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeUserTypeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="save_user_type_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
