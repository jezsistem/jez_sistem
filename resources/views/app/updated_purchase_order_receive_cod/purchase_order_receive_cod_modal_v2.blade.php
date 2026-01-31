<!-- Approval COD Main Modal -->
<div id="ApprovalCODModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-7xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <input type="hidden" id="_mode" name="_mode"/>
            <input type="hidden" id="_po_id" name="_po_id"/>
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    #<span id="no_po">Ini nomor PO</span> - <span id="invoice_label"></span>
                </h3>
                <button type="button" id="close_detail" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 space-y-6 max-h-[80vh] overflow-y-auto">
                <!-- Total Price Badge -->
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                        Total
                    </span>
                    <span id="total_approval_price" class="text-lg font-bold text-gray-900">0</span>
                </div>
                
                <!-- Form Fields -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Store</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" id="st_id" name="st_id" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" id="ps_name" name="ps_name" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" name="po_description" id="po_description" rows="2" disabled></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Stok</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" id="stkt_id" name="stkt_id" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pajak</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" id="tax_id" name="tax_id" required disabled>
                            <option value="">- Pajak -</option>
                            @foreach ($data['tax_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Terima</label>
                        <input type="date" id="receive_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" disabled />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ongkos Kirim</label>
                        <input type="number" id="shipping_cost" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" name="shipping_cost" disabled />
                    </div>
                    
                    <!-- Buttons Section -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Gambar Invoice dan Paket</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            <button type="button" id="InvoiceImagesBtn" class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-file-invoice mr-2"></i>
                                Invoice
                            </button>
                            <button type="button" id="pembayaranCodBtn" class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-upload mr-2"></i>
                                Upload Payment
                            </button>
                            <button type="button" id="SuratJalanImageBtn" class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-truck mr-2"></i>
                                Surat Jalan
                            </button>
                            <button type="button" id="BuktitfImagesBtn" class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-receipt mr-2"></i>
                                Bukti Transfer
                            </button>
                            <button type="button" id="DisputeFileBtn" class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                File Dispute
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="acc_id" name="acc_id" required>
                            <option value="">- Payment -</option>
                            @foreach ($data['acc_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                        <input type="date" id="pay_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank General</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="bank_general" name="bank_general">
                            <option value="">- Bank General -</option>
                            <option value="BCA 002">BCA 002</option>
                            <option value="BCA 004">BCA 004</option>
                            <option value="BCA 005">BCA 005</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo</label>
                        <input type="date" id="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" disabled />
                    </div>
                </div>
                
                <!-- Detail Table -->
                <div class="mt-6">
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200" id="CODtb">
                            <thead class="bg-blue-600">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal Terima</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Invoice</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">SKU</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Brand</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Artikel</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Warna</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Size</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tipe</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Qty Terima</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">In Stock</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Harga Beli</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Total</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                <button type="button" id="close_detail" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
                <button type="button" id="approve_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                    Bayar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Images Modal -->
<div id="InvoiceImagesModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-[60] overflow-y-auto bg-gray-900 bg-opacity-50">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Invoice Image</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 close-sub-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table id="InvoiceImagesTb" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Image</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors close-sub-modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Image Transfer Modal -->
<div id="UploadImageTransferModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-[60] overflow-y-auto bg-gray-900 bg-opacity-50">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_upload_transfer_image" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Upload Payment COD</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600 close-sub-modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Pilih Gambar Payment COD <span class="text-red-500">*</span>
                        </label>
                        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" name="imageTransfers[]" id="imageTransfers" multiple required />
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors close-sub-modal">
                        Tutup
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors" id="upload_image_transfer_btn">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bukti Transfer Images Modal -->
<div id="BuktitfImagesModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-[60] overflow-y-auto bg-gray-900 bg-opacity-50">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Bukti Transfer Image</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 close-sub-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table id="BuktitfImagesTb" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Image</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors close-sub-modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- File Dispute Modal -->
<div id="FileDisputeModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-[60] overflow-y-auto bg-gray-900 bg-opacity-50">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">File Dispute</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 close-sub-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table id="FileDisputeTb" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">File Dispute</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors close-sub-modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
