<!-- User Division Modal -->
<div id="UserDivisionModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeUserDivisionModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_user_division">
                @csrf
                <input type="hidden" name="_id" id="user_division_modal_id" value="" />
                <input type="hidden" name="_mode" id="user_division_modal_mode" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900" id="user_division_modal_title">User Division</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeUserDivisionModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="ud_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Division Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="ud_code" 
                                   name="ud_code" 
                                   required
                                   maxlength="50"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="ud_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Division Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="ud_name" 
                                   name="ud_name" 
                                   required
                                   maxlength="255"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label for="ud_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="ud_description" 
                                  name="ud_description" 
                                  rows="3"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="lead_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Leader
                            </label>
                            <select id="lead_id" 
                                    name="lead_id" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Leader</option>
                                <div id="leader_options">
                                    <!-- Options will be loaded via AJAX -->
                                </div>
                            </select>
                        </div>
                        <div>
                            <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Manager
                            </label>
                            <select id="manager_id" 
                                    name="manager_id" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Manager</option>
                                <div id="manager_options">
                                    <!-- Options will be loaded via AJAX -->
                                </div>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="ud_status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="ud_status" 
                                name="ud_status" 
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeUserDivisionModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="save_user_division_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
