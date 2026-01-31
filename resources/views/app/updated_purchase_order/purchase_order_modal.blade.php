<!-- Purchase Order Detail Modal -->
<div id="PurchaseOrderModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_purchase_order_modal">
                @csrf
                <input type="hidden" id="_mode" name="_mode"/>
                <input type="hidden" id="_po_id" name="_po_id"/>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Purchase Order #<span id="po_invoice_label"></span></h3>
                    <button type="button" id="close_purchase_order_modal_btn" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Store <span class="text-red-500">*</span></label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="st_id_modal" name="st_id" required>
                                <option value="">- Pilih Store -</option>
                                @foreach ($data['st_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="ps_id_modal" name="ps_id" required>
                                <option value="">- Pilih Supplier -</option>
                                @foreach ($data['ps_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="tax_id_modal" name="tax_id">
                                <option value="">- Pilih Tax -</option>
                                @if(isset($data['tax_id']))
                                    @foreach ($data['tax_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">DP</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="dp_id_modal" name="dp_id">
                                <option value="">- Pilih DP -</option>
                                @if(isset($data['dp_id']))
                                    @foreach ($data['dp_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Type</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="stkt_id_modal" name="stkt_id">
                                <option value="">- Pilih Stock Type -</option>
                                @if(isset($data['stkt_id']))
                                    @foreach ($data['stkt_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Cost</label>
                            <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="shipping_cost" name="shipping_cost" placeholder="Shipping Cost">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Account</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="acc_id_modal" name="acc_id">
                                <option value="">- Pilih Account -</option>
                                @if(isset($data['acc_id']))
                                    @foreach ($data['acc_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dispute</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="dispute" name="dispute">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pay Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="pay_date" name="pay_date">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="due_date" name="due_date">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Dispute</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="status_dispute" name="status_dispute" placeholder="Status Dispute">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Description" name="po_description" id="po_description" rows="3"></textarea>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dispute Description</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Dispute Description" name="dispute_description" id="dispute_description" rows="3"></textarea>
                        </div>
                    </div>
                    <div id="purchase_order_detail_content" class="mt-4">
                        <!-- Detail content will be loaded here -->
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <button type="button" id="cancel_purchase_order_btn" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors">
                        Hapus PO
                    </button>
                    <button type="button" id="save_purchase_order_draft_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Simpan Draft
                    </button>
                    <button type="button" id="close_purchase_order_modal_btn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="ImportModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" onclick="closeImportModal()">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all" onclick="event.stopPropagation()">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Import Data</h3>
                    <button type="button" class="close-import-modal text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih file Excel <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="po_file" id="po_file" required accept=".xlsx,.xls" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <button type="button" class="close-import-modal px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" id="import_data_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function closeImportModal() {
    $('#ImportModal').addClass('hidden');
}
</script>
