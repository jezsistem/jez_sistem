<!-- Target Modal -->
<div id="TargetModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeTargetModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <form id="f_target">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Target Penjualan</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                        <input type="month" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="tr_date" name="tr_date" required />
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-2">
                    <button type="button" onclick="closeTargetModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Tutup</button>
                    <button type="button" id="delete_target_btn" style="display:none;" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700">Hapus</button>
                    <button type="submit" id="save_target_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sub Target Modal -->
<div id="SubTargetModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeSubTargetModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Data Target</h3>
            </div>
            <div class="px-6 py-4">
                <input type="hidden" id="target_date" value="" />
                <input type="hidden" id="tr_id" value="" />
                <div class="mb-4 flex justify-end gap-2">
                    <button type="button" id="ImportModalBtn" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">Import Data</button>
                    <button type="button" id="add_target_detail_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Tambah Data</button>
                </div>
                <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="TargetDetailtb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Store</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Divisi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Target</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tercapai</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">%</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">[Penjualan] [Item Terjual]</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by SimpleDatatables -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
                <button type="button" onclick="closeSubTargetModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Sub Sub Target Modal -->
<div id="SubSubTargetModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeSubSubTargetModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Data Target</h3>
            </div>
            <div class="px-6 py-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Store</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="st_id" name="st_id" required>
                        <option value="">- Pilih Store -</option>
                        @foreach ($data['st_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="stt_id" name="stt_id" required>
                        <option value="">- Pilih Divisi -</option>
                        @foreach ($data['stt_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <div id="target_amount"></div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-2">
                <button type="button" onclick="closeSubSubTargetModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Tutup</button>
                <button type="button" id="save_target_detail_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<form id="f_import" enctype="multipart/form-data">
    @csrf
    <div id="ImportModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeImportModal()">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Import Data</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Download Template <span class="text-red-500">*</span></label>
                        <a href="{{ asset('upload/template/set_target_template.csv') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 inline-block">Download</a>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Store</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="import_st_id" name="import_st_id" required>
                            <option value="">- Pilih Store -</option>
                            @foreach ($data['st_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="import_stt_id" name="import_stt_id" required>
                            <option value="">- Pilih Divisi -</option>
                            @foreach ($data['stt_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih template yang sudah diisi data <span class="text-red-500">*</span></label>
                        <input type="file" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="importFile" id="importFile" accept=".csv" required />
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-2">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Tutup</button>
                    <button type="submit" id="import_data_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">Import</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function closeTargetModal() {
    document.getElementById('TargetModal').classList.add('hidden');
}
function closeSubTargetModal() {
    document.getElementById('SubTargetModal').classList.add('hidden');
}
function closeSubSubTargetModal() {
    document.getElementById('SubSubTargetModal').classList.add('hidden');
}
function closeImportModal() {
    document.getElementById('ImportModal').classList.add('hidden');
}
</script>
