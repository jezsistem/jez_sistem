<!-- User Position Modal -->
<div id="UserPositionModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeUserPositionModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_user_position">
                @csrf
                <input type="hidden" name="_id" id="user_position_modal_id" value="" />
                <input type="hidden" name="_mode" id="user_position_modal_mode" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900" id="user_position_modal_title">User Position</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeUserPositionModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="up_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Position Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="up_code" 
                                   name="up_code" 
                                   required
                                   maxlength="50"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="up_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Position Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="up_name" 
                                   name="up_name" 
                                   required
                                   maxlength="255"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label for="up_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="up_description" 
                                  name="up_description" 
                                  rows="3"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="up_level" class="block text-sm font-medium text-gray-700 mb-2">
                                Level <span class="text-red-500">*</span>
                            </label>
                            <select id="up_level" 
                                    name="up_level" 
                                    required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Level</option>
                                <option value="1">1 - Staff</option>
                                <option value="2">2 - Supervisor</option>
                                <option value="3">3 - Manager</option>
                                <option value="4">4 - Director</option>
                            </select>
                        </div>
                        <div>
                            <label for="up_color" class="block text-sm font-medium text-gray-700 mb-2">
                                Color <span class="text-red-500">*</span>
                            </label>
                            <input type="color" 
                                   id="up_color" 
                                   name="up_color" 
                                   value="#3699FF"
                                   required
                                   class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       id="up_can_approve_leave" 
                                       name="up_can_approve_leave" 
                                       value="1"
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Can Approve Leave</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       id="up_is_active" 
                                       name="up_is_active" 
                                       value="1"
                                       checked
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeUserPositionModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="save_user_position_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
