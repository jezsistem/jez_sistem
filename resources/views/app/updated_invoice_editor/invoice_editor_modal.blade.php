<!-- Data Modal -->
<div id="DataModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeDataModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <form id="form">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                <div class="flex items-center justify-between p-4 border-b rounded-t bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $data['subtitle'] }}</h3>
                    <button type="button" onclick="closeDataModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label for="st_id" class="block text-sm font-medium text-gray-700 mb-2">Store</label>
                        <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="st_id" name="st_id">
                            <option value="">- Pilih -</option>
                            @foreach ($data['st_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="stt_id" class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                        <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="stt_id" name="stt_id">
                            <option value="">- Pilih -</option>
                            @foreach ($data['stt_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="u_id" class="block text-sm font-medium text-gray-700 mb-2">Penanggung Jawab</label>
                        <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="u_id" name="u_id">
                            <option value="">- Pilih -</option>
                            @foreach ($data['u_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200">
                    <button type="button" onclick="closeDataModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors" id="save_btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openDataModal() {
    var modal = document.getElementById('DataModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
    }
}

function closeDataModal() {
    var modal = document.getElementById('DataModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
    }
}
</script>
