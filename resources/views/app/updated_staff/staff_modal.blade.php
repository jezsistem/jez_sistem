<!-- Position Modal -->
<div id="PositionModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closePositionModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_position">
                @csrf
                <input type="hidden" name="user_id" id="position_user_id" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Update Position</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closePositionModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="up_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Position <span class="text-red-500">*</span>
                        </label>
                        <select id="up_id" 
                                name="up_id" 
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Position</option>
                            @foreach($data['positions'] as $position)
                                <option value="{{ $position->id }}">{{ $position->up_code }} - {{ $position->up_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closePositionModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="save_position_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Update Position
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Division Modal -->
<div id="DivisionModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeDivisionModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_division">
                @csrf
                <input type="hidden" name="user_id" id="division_user_id" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Update Division</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeDivisionModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="ud_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Division <span class="text-red-500">*</span>
                        </label>
                        <select id="ud_id" 
                                name="ud_id" 
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Division</option>
                            @foreach($data['divisions'] as $division)
                                <option value="{{ $division->id }}">{{ $division->ud_code }} - {{ $division->ud_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeDivisionModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="save_division_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Update Division
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Type Modal -->
<div id="UserTypeModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeUserTypeModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_user_type_staff">
                @csrf
                <input type="hidden" name="user_id" id="user_type_user_id" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Update User Type</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeUserTypeModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="ut_id" class="block text-sm font-medium text-gray-700 mb-2">
                            User Type <span class="text-red-500">*</span>
                        </label>
                        <select id="ut_id" 
                                name="ut_id" 
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select User Type</option>
                            @foreach($data['userTypes'] as $userType)
                                <option value="{{ $userType->id }}">{{ $userType->ut_code }} - {{ $userType->ut_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeUserTypeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="save_user_type_staff_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Update User Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Leave Balance Modal -->
<div id="LeaveBalanceModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeLeaveBalanceModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_leave_balance">
                @csrf
                <input type="hidden" name="user_id" id="leave_balance_user_id" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Update Annual Leave Balance</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeLeaveBalanceModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="lb_remaining_balance" class="block text-sm font-medium text-gray-700 mb-2">
                            Remaining Days <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="lb_remaining_balance" 
                               name="lb_remaining_balance" 
                               min="0"
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Enter the remaining annual leave days for this year</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeLeaveBalanceModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="save_leave_balance_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Update Leave Balance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
