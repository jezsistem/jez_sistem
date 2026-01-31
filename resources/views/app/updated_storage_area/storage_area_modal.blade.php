<!-- Add Storage Area Modal -->
<div id="addStorageAreaModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeAddStorageAreaModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="addStorageAreaForm">
                @csrf
                <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Area Penyimpanan Baru</h3>
                    <button type="button" onclick="closeAddStorageAreaModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea id="description" name="description" rows="3" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200">
                    <button type="button" onclick="closeAddStorageAreaModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Storage Area Modal -->
<div id="detailStorageAreaModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeDetailStorageAreaModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <input type="hidden" id="storage_area_id" name="storage_area_id">
            <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900" id="detail_name"></h3>
                <div class="flex items-center gap-2">
                    <button type="button" id="editStorageAreaBtn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Edit Area Penyimpanan
                    </button>
                    <button type="button" id="deleteStorageAreaBtn" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors">
                        Hapus Area Penyimpanan
                    </button>
                    <button type="button" onclick="closeDetailStorageAreaModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-4 max-h-[80vh] overflow-y-auto">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">List Bin Pada Area Ini</label>
                    <div id="bin_list" class="border border-gray-200 rounded-lg p-4 min-h-[100px]"></div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">List Bin Yang Akan Ditambahkan</label>
                    <div id="bin_list_temp" class="border border-gray-200 rounded-lg p-4 min-h-[100px]"></div>
                    <button type="button" id="add_bin_to_area" class="mt-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Save Changes
                    </button>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">List Bin Belum Setup</label>
                    <div id="bin_list_no_area" class="border border-gray-200 rounded-lg p-4 min-h-[100px]"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Storage Area Modal -->
<div id="editStorageAreaModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeEditStorageAreaModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="editStorageAreaForm">
                @csrf
                <input type="hidden" id="edit_storage_area_id" name="storage_area_id">
                <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Area Penyimpanan</h3>
                    <button type="button" onclick="closeEditStorageAreaModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                        <input type="text" id="edit_name" name="edit_name" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea id="edit_description" name="edit_description" rows="3" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200">
                    <button type="button" onclick="closeEditStorageAreaModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                        Update Area
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddStorageAreaModal() {
    document.getElementById('addStorageAreaModal').classList.remove('hidden');
    document.getElementById('addStorageAreaModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}
function closeAddStorageAreaModal() {
    document.getElementById('addStorageAreaModal').classList.add('hidden');
    document.getElementById('addStorageAreaModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function openDetailStorageAreaModal() {
    document.getElementById('detailStorageAreaModal').classList.remove('hidden');
    document.getElementById('detailStorageAreaModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}
function closeDetailStorageAreaModal() {
    document.getElementById('detailStorageAreaModal').classList.add('hidden');
    document.getElementById('detailStorageAreaModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function openEditStorageAreaModal() {
    document.getElementById('editStorageAreaModal').classList.remove('hidden');
    document.getElementById('editStorageAreaModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}
function closeEditStorageAreaModal() {
    document.getElementById('editStorageAreaModal').classList.add('hidden');
    document.getElementById('editStorageAreaModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}
</script>
