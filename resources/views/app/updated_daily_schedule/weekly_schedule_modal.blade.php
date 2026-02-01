<!-- Import Excel Modal -->
<div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="hideImportModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Import Weekly Schedule</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="hideImportModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="import_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Week Start Date (Monday) <span class="text-red-500">*</span>
                        </label>
                        <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="import_start_date" name="import_start_date" value="{{ $startDate }}">
                        <small class="text-gray-500 text-xs mt-1 block">Select Monday to show full week schedule</small>
                    </div>
                </div>
                
                <div>
                    <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Excel File <span class="text-red-500">*</span>
                    </label>
                    <input type="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv">
                    <small class="text-gray-500 text-xs mt-1 block">
                        Format: NIP, Nama, User Type, Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu<br>
                        File size: max 2MB
                    </small>
                    <div class="mt-3">
                        <a href="/Template_Import_Schedule.xlsx" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700" download>
                            <i class="fas fa-download mr-2"></i> Download Template Excel
                        </a>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h6 class="font-semibold text-blue-900 mb-2"><i class="fas fa-info-circle mr-2"></i> Excel Format Requirements:</h6>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li><strong>Column A:</strong> NIP (required)</li>
                        <li><strong>Column B:</strong> Nama Staff</li>
                        <li><strong>Column C:</strong> User Type (can be empty, will auto-detect from database)</li>
                        <li><strong>Column D:</strong> Senin (shift code: PS1, PS2, L, SM1, SM2, etc.)</li>
                        <li><strong>Column E:</strong> Selasa</li>
                        <li><strong>Column F:</strong> Rabu</li>
                        <li><strong>Column G:</strong> Kamis</li>
                        <li><strong>Column H:</strong> Jumat</li>
                        <li><strong>Column I:</strong> Sabtu</li>
                        <li><strong>Column J:</strong> Minggu</li>
                    </ul>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h6 class="font-semibold text-yellow-900 mb-2"><i class="fas fa-exclamation-triangle mr-2"></i> Validation Rules:</h6>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li>Shift codes must exist in the system</li>
                        <li>Shift codes must be compatible with user type</li>
                        <li>User Type column can be empty - system will auto-detect from database</li>
                        <li>Import will process all divisions without filter</li>
                        <li>Existing schedules will be updated</li>
                        <li>Empty cells will be ignored</li>
                    </ul>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                <button type="button" onclick="hideImportModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Cancel
                </button>
                <button type="button" onclick="importExcel()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                    <i class="fas fa-upload mr-2"></i> Import
                </button>
            </div>
        </div>
    </div>
</div>
