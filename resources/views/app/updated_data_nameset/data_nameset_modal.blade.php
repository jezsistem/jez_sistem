<!-- History Modal (if needed) -->
<div id="HistoryModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeHistoryModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">History</h3>
                <button type="button" onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <div id="history_content">
                    <!-- History content will be loaded here -->
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200">
                <button type="button" onclick="closeHistoryModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openHistoryModal() {
    var modal = document.getElementById('HistoryModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
    }
}

function closeHistoryModal() {
    var modal = document.getElementById('HistoryModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
    }
}
</script>
