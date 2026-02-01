<!-- Leave Request Modal -->
<div id="LeaveRequestModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeLeaveRequestModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_leave_request" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_id" id="leave_request_modal_id" value="" />
                <input type="hidden" name="_mode" id="leave_request_modal_mode" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900" id="leave_request_modal_title">Leave Request</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeLeaveRequestModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="leave_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Leave Type <span class="text-red-500">*</span>
                            </label>
                            <select id="leave_type_id" name="leave_type_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}">{{ $leaveType->lt_name }} ({{ $leaveType->lt_code }})</option>
                                @endforeach
                            </select>
                            <div id="leaveBalanceInfo" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-gray-700">
                                <p>Select a leave type to see your balance information.</p>
                            </div>
                        </div>
                        <div>
                            <label for="lr_unit" class="block text-sm font-medium text-gray-700 mb-2">
                                Unit <span class="text-red-500">*</span>
                            </label>
                            <select id="lr_unit" name="lr_unit" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="days">Days</option>
                                <option value="hours">Hours</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="lr_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Start Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="lr_start_date" name="lr_start_date" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="lr_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                End Date
                            </label>
                            <input type="date" id="lr_end_date" name="lr_end_date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <div id="time_fields" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: none;">
                        <div>
                            <label for="lr_start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Start Time
                            </label>
                            <input type="time" id="lr_start_time" name="lr_start_time" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="lr_end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                End Time
                            </label>
                            <input type="time" id="lr_end_time" name="lr_end_time" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label for="lr_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea id="lr_reason" name="lr_reason" rows="4" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <div>
                        <label for="lr_attachments" class="block text-sm font-medium text-gray-700 mb-2">
                            Attachments (Optional)
                        </label>
                        <input type="file" id="lr_attachments" name="lr_attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <small class="text-gray-500 text-xs mt-1 block">
                            Supported formats: PDF, JPG, JPEG, PNG, DOC, DOCX (Max: 10MB per file)
                        </small>
                        <div id="attachment-preview" class="mt-3"></div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" onclick="closeLeaveRequestModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="submit" id="save_leave_request_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
