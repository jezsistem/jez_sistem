<!-- Import Modal -->
<div id="ImportModal" class="modal-overlay hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <form id="f_import" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Import Data</h3>
                <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Download Template <span class="text-red-500">*</span></label>
                    <a href="{{ asset('upload/template/supplier_template.xlsx') }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        <i class="fa fa-download mr-2"></i> Download Template
                    </a>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih file template <span class="text-red-500">*</span></label>
                    <input type="file" name="cust_template" id="cust_template" required 
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Tutup</button>
                <button type="submit" id="import_data_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fa fa-upload mr-2"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Customer Type Modal -->
<div id="CustomerTypeModal" class="modal-overlay hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 my-8">
        <form id="f_customer_type">
            @csrf
            <input type="hidden" name="_id_ct" id="_id_ct" value="">
            <input type="hidden" name="_mode_ct" id="_mode_ct" value="">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tipe Customer</h3>
                <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe <span class="text-red-500">*</span></label>
                    <input type="text" name="ct_name" id="ct_name_input" required 
                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <input type="text" name="ct_description" id="ct_description_input" 
                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Tutup</button>
                <button type="button" id="delete_customer_type_btn" class="hidden px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700">
                    <i class="fa fa-trash mr-2"></i> Hapus
                </button>
                <button type="submit" id="save_customer_type_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    <i class="fa fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Customer Modal -->
<div id="CustomerModal" class="modal-overlay hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 my-8">
        <form id="f_customer" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_id" id="_id" value="">
            <input type="hidden" name="_mode" id="_mode" value="">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Customer</h3>
                <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Customer <span class="text-red-500">*</span></label>
                        <select name="ct_id" id="ct_id" required class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">- Pilih -</option>
                            @foreach ($data['ct_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="cust_name" id="cust_name" required 
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Toko</label>
                        <input type="text" name="cust_store" id="cust_store" placeholder="isi jika dropshipper"
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No Telp <span class="text-red-500">*</span></label>
                        <input type="number" name="cust_phone" id="cust_phone" required 
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="cust_email" id="cust_email" 
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="cust_username" id="cust_username" 
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" id="password" 
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi <span class="text-red-500">*</span></label>
                        <select name="cust_province" id="cust_province" required class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">- Pilih -</option>
                            @foreach ($data['cust_province'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kota <span class="text-red-500">*</span></label>
                        <select name="cust_city" id="cust_city" required class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">- Pilih -</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kecamatan <span class="text-red-500">*</span></label>
                        <select name="cust_subdistrict" id="cust_subdistrict" required class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">- Pilih -</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat <span class="text-red-500">*</span></label>
                        <input type="text" name="cust_address" id="cust_address" required 
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Customer</label>
                        <select name="cust_token_active" id="cust_token_active" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="null">-- Silahkan Pilih --</option>
                            <option value="1">Active</option>
                            <option value="0">Non-Active</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Tutup</button>
                <button type="button" id="delete_customer_btn" class="hidden px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700">
                    <i class="fa fa-trash mr-2"></i> Hapus
                </button>
                <button type="submit" id="save_customer_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fa fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Customer City Modal -->
<div id="CustomerCityModal" class="modal-overlay hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-4 my-8">
        <input type="hidden" name="_province_code" id="_province_code" value="0">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Customer by City</h3>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="p-6 max-h-[70vh] overflow-y-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kota</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    </tr>
                </thead>
                <tbody id="CityTableBody" class="bg-white divide-y divide-gray-200">
                    <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td></tr>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200">
            <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Tutup</button>
        </div>
    </div>
</div>

<!-- Customer Subdistrict Modal -->
<div id="CustomerSubdistrictModal" class="modal-overlay hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-4 my-8">
        <input type="hidden" name="_city_code" id="_city_code" value="0">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Customer by Subdistrict</h3>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="p-6 max-h-[70vh] overflow-y-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kecamatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    </tr>
                </thead>
                <tbody id="SubdistrictTableBody" class="bg-white divide-y divide-gray-200">
                    <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td></tr>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200">
            <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Tutup</button>
        </div>
    </div>
</div>

<!-- Customer Detail Modal -->
<div id="CustomerDetailModal" class="modal-overlay hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl mx-4 my-8">
        <input type="hidden" name="_code" id="_code" value="">
        <input type="hidden" name="_code_type" id="_code_type" value="">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detail Customer</h3>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="p-6 max-h-[70vh] overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toko</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Telp</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Belanja</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Join</th>
                    </tr>
                </thead>
                <tbody id="CustomerDetailTableBody" class="bg-white divide-y divide-gray-200">
                    <tr><td colspan="9" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td></tr>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200">
            <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Tutup</button>
        </div>
    </div>
</div>

<!-- Customer Transaction Modal -->
<div id="CustomerTransactionModal" class="modal-overlay hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-4 my-8">
        <input type="hidden" name="_cust_id" id="_cust_id" value="">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Belanja Customer</h3>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="p-6 max-h-[70vh] overflow-y-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody id="TransactionTableBody" class="bg-white divide-y divide-gray-200">
                    <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">Tidak ada transaksi</td></tr>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200">
            <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Tutup</button>
        </div>
    </div>
</div>

<!-- Sales Item Detail Modal -->
<div id="SalesItemDetailModal" class="modal-overlay hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-4 my-8">
        <input type="hidden" name="pt_id" id="pt_id" value="">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900"><span id="sales_item_detail_label"></span></h3>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="p-6 max-h-[70vh] overflow-y-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Artikel</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    </tr>
                </thead>
                <tbody id="SalesItemDetailTableBody" class="bg-white divide-y divide-gray-200">
                    <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td></tr>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200">
            <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Tutup</button>
        </div>
    </div>
</div>

<!-- Graph Modal -->
<div id="GraphModal" class="modal-overlay hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl mx-4 my-8">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Grafik Customer</h3>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <div id="chart"></div>
            <div id="province_chart"></div>
            <div id="date_chart"></div>
        </div>
        <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200">
            <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Tutup</button>
        </div>
    </div>
</div>
