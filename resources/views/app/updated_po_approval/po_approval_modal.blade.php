<!-- Approval Penerimaan Detail Modal -->
<div id="ApprovalPenerimaanModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_approval_penerimaan_modal">
                @csrf
                <input type="hidden" id="_mode" name="_mode"/>
                <input type="hidden" id="_po_id" name="_po_id"/>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Approval Penerimaan PO #<span id="po_invoice_label"></span></h3>
                    <button type="button" id="close_approval_penerimaan_modal_btn" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Store</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" id="st_id_modal" name="st_id" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" id="ps_name_modal" name="ps_name" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Type</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" id="stkt_id_modal" name="stkt_id" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100" id="tax_id_modal" name="tax_id" disabled>
                                <option value="">- Pilih Tax -</option>
                                @foreach ($data['tax_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" id="a_name_modal" name="a_name" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dispute</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" id="dispute_modal" name="dispute" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Terima</label>
                            <input type="date" id="receive_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" max="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Barang Datang</label>
                            <input type="datetime-local" id="arrived_at" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" max="{{ date('Y-m-d\TH:i') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Cost</label>
                            <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" id="shipping_cost" name="shipping_cost" disabled>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" placeholder="Description" name="po_description" id="po_description" rows="3" disabled></textarea>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dispute Description</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" placeholder="Dispute Description" name="dispute_description" id="dispute_description" rows="3" disabled></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                                    <input type="date" id="pay_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" disabled>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo</label>
                                    <input type="date" id="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                            <div class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50">
                                <span id="total_approval_price" class="font-semibold text-gray-900">0</span>
                            </div>
                        </div>
                    </div>
                    <div id="approval_penerimaan_detail_content" class="mt-4">
                        <!-- Detail content will be loaded here -->
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <button type="button" id="close_approval_penerimaan_modal_btn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
