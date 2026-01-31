<!-- Modal -->
<div id="ExternalAssignmentTypeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">External Assignment Type</h3>
                <button type="button" id="close_external_assignment_type_modal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="f_external_assignment_type">
                @csrf
                <input type="hidden" name="_id" id="_id" value="">
                <input type="hidden" name="_mode" id="_mode" value="">
                
                <div class="mb-4">
                    <label for="ea_name" class="block text-sm font-medium text-gray-700 mb-1">Type Name <span class="text-red-500">*</span></label>
                    <input type="text" id="ea_name" name="ea_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>
                
                <div class="mb-4">
                    <label for="ea_desc" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="ea_desc" name="ea_desc" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
                </div>
                
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" id="delete_external_assignment_type_btn" class="hidden px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                    <button type="button" id="cancel_external_assignment_type_btn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" id="save_external_assignment_type_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
