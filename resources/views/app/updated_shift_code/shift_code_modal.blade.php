<!-- Shift Code Modal -->
<div id="ShiftCodeModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeShiftCodeModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_shift_code">
                @csrf
                <input type="hidden" name="_id" id="shift_code_modal_id" value="" />
                <input type="hidden" name="_mode" id="shift_code_modal_mode" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900" id="shift_code_modal_title">Shift Code</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeShiftCodeModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="sc_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Shift Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="sc_code" 
                                   name="sc_code" 
                                   required
                                   maxlength="10"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase">
                        </div>
                        <div class="md:col-span-2">
                            <label for="sc_shift_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Shift Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="sc_shift_name" 
                                   name="sc_shift_name" 
                                   required
                                   maxlength="100"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label for="sc_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="sc_description" 
                               name="sc_description" 
                               required
                               maxlength="255"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="sc_start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Start Time
                            </label>
                            <input type="time" 
                                   id="sc_start_time" 
                                   name="sc_start_time" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="sc_end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                End Time
                            </label>
                            <input type="time" 
                                   id="sc_end_time" 
                                   name="sc_end_time" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            User Type <span class="text-red-500">*</span>
                        </label>
                        <!-- Select All Checkbox -->
                        <div class="mb-3 p-3 bg-gray-50 border-2 border-gray-300 rounded-lg">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       id="selectAllUserTypes" 
                                       onchange="toggleAllUserTypes()"
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm font-semibold text-gray-900">Select/Clear All</span>
                            </label>
                            <small class="text-gray-600 block mt-1">
                                <span id="selectedUserTypeCount">0</span> from {{ count($userTypes ?? []) }} User Type Selected
                            </small>
                        </div>
                        
                        <!-- User Types Checkboxes -->
                        <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 max-h-60 overflow-y-auto">
                            @if(isset($userTypes) && count($userTypes) > 0)
                                @foreach($userTypes as $userType)
                                    <div class="mb-2">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   class="user-type-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
                                                   id="user_type_{{ $userType->id }}" 
                                                   name="user_type_ids[]" 
                                                   value="{{ $userType->id }}"
                                                   onchange="updateUserTypeCount()">
                                            <span class="ml-2 text-sm text-gray-700">{{ $userType->ut_name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-500">No user types available</p>
                            @endif
                        </div>
                        <div id="user_type_error" class="text-red-500 text-sm mt-1 hidden">Please select at least one user type</div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeShiftCodeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="save_shift_code_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleAllUserTypes() {
    var selectAllCheckbox = document.getElementById('selectAllUserTypes');
    var checkboxes = document.querySelectorAll('.user-type-checkbox');
    
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateUserTypeCount();
}

function updateUserTypeCount() {
    var checkedCheckboxes = document.querySelectorAll('.user-type-checkbox:checked');
    var selectedCountSpan = document.getElementById('selectedUserTypeCount');
    var selectAllCheckbox = document.getElementById('selectAllUserTypes');
    
    if (selectedCountSpan) {
        selectedCountSpan.textContent = checkedCheckboxes.length;
    }
    
    // Update select all checkbox state
    if (checkboxes.length === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    } else {
        var allCheckboxes = document.querySelectorAll('.user-type-checkbox');
        if (checkedCheckboxes.length === allCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCheckboxes.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    var checkboxes = document.querySelectorAll('.user-type-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateUserTypeCount);
    });
    updateUserTypeCount();
});
</script>
