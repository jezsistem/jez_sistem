<!-- Approval Modal -->
<div id="ApprovalModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeApprovalModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b">
                <h3 class="text-lg font-semibold text-gray-900" id="approval_modal_title">Approve/Reject Leave Request</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeApprovalModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="f_approval">
                @csrf
                <input type="hidden" name="_id" id="approval_modal_id" value="" />
                <input type="hidden" name="_action" id="approval_modal_action" value="" />
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="lr_admin_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            <span id="approval_notes_label">Admin Notes</span> <span id="approval_notes_required" class="text-red-500"></span>
                        </label>
                        <textarea id="lr_admin_notes" name="lr_admin_notes" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        <small class="text-gray-500 text-xs mt-1 block" id="approval_notes_hint"></small>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" onclick="closeApprovalModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" id="save_approval_btn" class="px-4 py-2 text-sm font-medium text-white rounded-lg">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
