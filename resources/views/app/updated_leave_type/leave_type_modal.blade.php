<!-- Leave Type Modal -->
<div id="LeaveTypeModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeLeaveTypeModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_leave_type">
                @csrf
                <input type="hidden" name="_id" id="leave_type_modal_id" value="" />
                <input type="hidden" name="_mode" id="leave_type_modal_mode" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900" id="leave_type_modal_title">Leave Type</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeLeaveTypeModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="lt_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="lt_code" 
                                   name="lt_code" 
                                   required
                                   maxlength="50"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase">
                        </div>
                        <div>
                            <label for="lt_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="lt_name" 
                                   name="lt_name" 
                                   required
                                   maxlength="255"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label for="lt_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="lt_description" 
                                  name="lt_description" 
                                  rows="3"
                                  maxlength="500"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="lt_default_days" class="block text-sm font-medium text-gray-700 mb-2">
                                Default Days
                            </label>
                            <input type="number" 
                                   id="lt_default_days" 
                                   name="lt_default_days" 
                                   min="0"
                                   value="0"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="lt_default_hours" class="block text-sm font-medium text-gray-700 mb-2">
                                Default Hours
                            </label>
                            <input type="number" 
                                   id="lt_default_hours" 
                                   name="lt_default_hours" 
                                   min="0"
                                   value="0"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="lt_unit" class="block text-sm font-medium text-gray-700 mb-2">
                                Unit <span class="text-red-500">*</span>
                            </label>
                            <select id="lt_unit" 
                                    name="lt_unit" 
                                    required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="days">Days</option>
                                <option value="hours">Hours</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Options
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           id="lt_requires_approval" 
                                           name="lt_requires_approval" 
                                           value="1"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Requires Approval</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           id="lt_is_active" 
                                           name="lt_is_active" 
                                           value="1"
                                           checked
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeLeaveTypeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="save_leave_type_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
