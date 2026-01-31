<!-- Penerimaan Detail Modal -->
<div id="PenerimaanModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_penerimaan_modal" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="_mode" name="_mode"/>
                <input type="hidden" id="_po_id" name="_po_id"/>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Penerimaan PO #<span id="po_invoice_label"></span></h3>
                    <button type="button" id="close_penerimaan_modal_btn" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Store</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100" id="st_id_modal" name="st_id" disabled>
                                <option value="">- Pilih Store -</option>
                                @foreach ($data['st_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100" id="ps_id_modal" name="ps_id" disabled>
                                <option value="">- Pilih Supplier -</option>
                                @foreach ($data['ps_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Type</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="stkt_id_modal" name="stkt_id">
                                <option value="">- Pilih Stock Type -</option>
                                @foreach ($data['stkt_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="tax_id_modal" name="tax_id">
                                <option value="">- Pilih Tax -</option>
                                @foreach ($data['tax_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Cost</label>
                            <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="shipping_cost" name="shipping_cost" placeholder="Shipping Cost">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dispute</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="dispute" name="dispute">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Putaway</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="putaway" name="putaway">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Dispute</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="status_dispute" name="status_dispute">
                                <option value="0">Closed</option>
                                <option value="1">Progress</option>
                            </select>
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
                    <div id="penerimaan_detail_content" class="mt-4">
                        <!-- Detail content will be loaded here -->
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <button type="button" id="close_penerimaan_modal_btn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
