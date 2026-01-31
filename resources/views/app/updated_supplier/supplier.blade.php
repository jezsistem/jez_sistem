@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage product supplier information</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Export Dropdown -->
            <div class="relative">
                <button type="button" 
                        onclick="toggleExportMenu()"
                        class="px-4 py-2 bg-green-100 text-green-600 text-sm font-medium rounded-lg hover:bg-green-200 transition-colors flex items-center gap-2">
                    <i class="cft-standard-stroke cft-download"></i>
                    Export
                </button>
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div class="py-1">
                        <a href="#" id="supplier_excel_btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="cft-standard-stroke cft-excel mr-2"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
            </div>
            <!-- Import Button -->
            <button onclick="openImportModal()" 
                    class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-upload"></i>
                Import
            </button>
            <!-- Add Button -->
            <button onclick="openAddModal()" 
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-add"></i>
                Data Baru
            </button>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="cft-standard-stroke cft-search text-gray-400"></i>
            </div>
            <input type="search" 
                   id="supplier_search"
                   class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                   placeholder="Cari nama supplier, email, telp, rekening...">
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden p-5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="Suppliertb">
                <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                    <tr>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Nama Supplier</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">PKP</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Alamat</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">No Telp</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Rekening</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">NPWP</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded by DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="SupplierModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_supplier">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">{{ $data['subtitle'] }}</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeModal()">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label for="ps_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Supplier <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="ps_name" 
                               name="ps_name" 
                               required
                               autocomplete="off"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="ps_pkp" class="block text-sm font-medium text-gray-700 mb-2">
                            PKP
                        </label>
                        <select id="ps_pkp" 
                                name="ps_pkp"
                                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>
                    <div>
                        <label for="ps_due_day" class="block text-sm font-medium text-gray-700 mb-2">
                            Tempo
                        </label>
                        <input type="number" 
                               id="ps_due_day" 
                               name="ps_due_day"
                               autocomplete="off"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="ps_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" 
                               id="ps_email" 
                               name="ps_email"
                               autocomplete="off"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="ps_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            No Telp
                        </label>
                        <input type="text" 
                               id="ps_phone" 
                               name="ps_phone"
                               autocomplete="off"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="ps_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="ps_address" 
                               name="ps_address"
                               required
                               autocomplete="off"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="ps_rekening" class="block text-sm font-medium text-gray-700 mb-2">
                            Rekening
                        </label>
                        <input type="text" 
                               id="ps_rekening" 
                               name="ps_rekening"
                               autocomplete="off"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="ps_npwp" class="block text-sm font-medium text-gray-700 mb-2">
                            NPWP
                        </label>
                        <input type="text" 
                               id="ps_npwp" 
                               name="ps_npwp"
                               autocomplete="off"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="ps_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <input type="text" 
                               id="ps_description" 
                               name="ps_description"
                               autocomplete="off"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="button" 
                            id="delete_supplier_btn" 
                            onclick="deleteSupplier()"
                            style="display:none;"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors">
                        Hapus
                    </button>
                    <button type="submit" 
                            id="save_supplier_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="ImportModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeImportModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Import Data</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeImportModal()">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Download Template <span class="text-red-500">*</span>
                        </label>
                        <a href="{{ asset('upload/template/data_supplier_template.xlsx') }}"
                           class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            Download
                        </a>
                    </div>
                    <div>
                        <label for="ps_template" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih template yang sudah di download dan diisi <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                               id="ps_template" 
                               name="ps_template" 
                               required
                               accept=".xlsx,.xls"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeImportModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="import_data_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script>
let supplier_dataTable;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load data and initialize simple-datatables
    loadSupplierData();

    $('#supplier_search').on('keyup', function() {
        loadSupplierData();
    });

    // Check duplicate supplier name
    $('#ps_name').on('change', function() {
        var ps_name = $(this).val();
        if (!ps_name) return;
        
        $.ajax({
            type: "POST",
            data: {
                _ps_name: ps_name
            },
            dataType: 'json',
            url: "{{ url('check_exists_supplier') }}",
            success: function(r) {
                if (r.status == '200') {
                    showToast('Supplier sudah ada di sistem, silahkan ganti dengan yang lain', 'warning');
                    $('#ps_name').val('');
                }
            }
        });
    });

    // Form submission
    $('#f_supplier').on('submit', function(e) {
        e.preventDefault();
        $("#save_supplier_btn").html('Proses...');
        $("#save_supplier_btn").prop("disabled", true);
        
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('ps_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_supplier_btn").html('Simpan');
                $("#save_supplier_btn").prop("disabled", false);
                if (data.status == '200') {
                    closeModal();
                    loadSupplierData();
                    showToast('Data berhasil disimpan', 'success');
                } else if (data.status == '400') {
                    closeModal();
                    showToast('Data tidak tersimpan', 'error');
                }
            },
            error: function(xhr) {
                $("#save_supplier_btn").html('Simpan');
                $("#save_supplier_btn").prop("disabled", false);
                let errorMsg = 'Terjadi kesalahan saat memproses permintaan';
                if (xhr.responseText) {
                    errorMsg = xhr.responseText;
                }
                showToast(errorMsg, 'error');
            }
        });
    });

    // Import form submission
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        $("#import_data_btn").html('Proses...');
        $("#import_data_btn").prop("disabled", true);
        
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('ps_import') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").prop("disabled", false);
                if (data.status == '200') {
                    closeImportModal();
                    loadSupplierData();
                    showToast('Data berhasil diimport', 'success');
                } else if (data.status == '400') {
                    showToast('Data tidak terimport', 'error');
                }
            },
            error: function(xhr) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").prop("disabled", false);
                let errorMsg = 'Terjadi kesalahan saat memproses permintaan';
                if (xhr.responseText) {
                    errorMsg = xhr.responseText;
                }
                showToast(errorMsg, 'error');
            }
        });
    });
});

function loadSupplierData() {
    $.ajax({
        url: "{{ url('product_supplier_datatables') }}",
        type: 'GET',
        data: {
            search: $('#supplier_search').val(),
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (supplier_dataTable) {
                supplier_dataTable.destroy();
            }
            
            // Clear table body
            $('#Suppliertb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var pkpShow = (row.ps_pkp == '1') ? 'Ya' : '-';
                    var html = '<tr class="hover:bg-gray-50 cursor-pointer">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" data-id="' + row.id + '" data-name="' + escapeHtml(row.ps_name || '') + '" data-pkp="' + (row.ps_pkp || '0') + '" data-due_day="' + (row.ps_due_day || '') + '" data-email="' + escapeHtml(row.ps_email || '') + '" data-phone="' + escapeHtml(row.ps_phone || '') + '" data-address="' + escapeHtml(row.ps_address || '') + '" data-rekening="' + escapeHtml(row.ps_rekening || '') + '" data-npwp="' + escapeHtml(row.ps_npwp || '') + '" data-description="' + escapeHtml(row.ps_description || '') + '">' + escapeHtml(row.ps_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + pkpShow + '</td>';
                    html += '<td class="px-6 py-4 text-sm text-gray-500">' + escapeHtml(row.ps_address || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.ps_phone || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.ps_rekening || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.ps_npwp || '') + '</td>';
                    html += '<td class="px-6 py-4 text-sm text-gray-500">' + escapeHtml(row.ps_description || '') + '</td>';
                    html += '</tr>';
                    $('#Suppliertb tbody').append(html);
                });
            } else {
                $('#Suppliertb tbody').append('<tr><td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("Suppliertb") && typeof simpleDatatables !== 'undefined') {
                supplier_dataTable = new simpleDatatables.DataTable("#Suppliertb", {
                    searchable: true,
                    sortable: true,
                    perPage: 10,
                    perPageSelect: [5, 10, 25, 50, 100],
                    classes: {
                        wrapper: "datatable-wrapper",
                        top: "datatable-top",
                        bottom: "datatable-bottom",
                        search: "datatable-search",
                        selector: "datatable-selector",
                        table: "datatable-table",
                        thead: "",
                        tbody: "",
                        sorter: "datatable-sorter"
                    },
                    labels: {
                        placeholder: "Cari...",
                        perPage: "{select} data per halaman",
                        noRows: "Tidak ada data",
                        info: "Menampilkan {start} sampai {end} dari {rows} data",
                        noResults: "Tidak ada hasil pencarian"
                    }
                });
                
                // Handle row click after datatable is initialized
                setTimeout(function() {
                    $('#Suppliertb tbody').off('click').on('click', 'tr', function() {
                        var $row = $(this);
                        var id = $row.find('td[data-id]').data('id');
                        var name = $row.find('td[data-id]').data('name');
                        var pkp = $row.find('td[data-id]').data('pkp');
                        var due_day = $row.find('td[data-id]').data('due_day');
                        var email = $row.find('td[data-id]').data('email');
                        var phone = $row.find('td[data-id]').data('phone');
                        var address = $row.find('td[data-id]').data('address');
                        var rekening = $row.find('td[data-id]').data('rekening');
                        var npwp = $row.find('td[data-id]').data('npwp');
                        var description = $row.find('td[data-id]').data('description');
                        
                        if (id) {
                            openEditModal(id, name, pkp, due_day, email, phone, address, rekening, npwp, description);
                        }
                    });
                }, 100);
            }
        },
        error: function(xhr) {
            console.error('Error loading data:', xhr);
            showToast('Gagal memuat data', 'error');
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Modal functions
function openAddModal() {
    $('#_id').val('');
    $('#_mode').val('add');
    $('#f_supplier')[0].reset();
    $('#delete_supplier_btn').hide();
    $('#modalTitle').text('Tambah {{ $data["subtitle"] }}');
    openModal();
}

function openEditModal(id, name, pkp, due_day, email, phone, address, rekening, npwp, description) {
    $('#ps_name').val(name);
    $('#ps_pkp').val(pkp || '0');
    $('#ps_due_day').val(due_day || '');
    $('#ps_email').val(email || '');
    $('#ps_phone').val(phone || '');
    $('#ps_address').val(address || '');
    $('#ps_rekening').val(rekening || '');
    $('#ps_npwp').val(npwp || '');
    $('#ps_description').val(description || '');
    $('#_id').val(id);
    $('#_mode').val('edit');
    $('#delete_supplier_btn').show();
    $('#modalTitle').text('Edit {{ $data["subtitle"] }}');
    openModal();
}

function openModal() {
    const modal = document.getElementById('SupplierModal');
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    $('body').addClass('overflow-hidden');
}

function closeModal() {
    const modal = document.getElementById('SupplierModal');
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    $('body').removeClass('overflow-hidden');
}

function openImportModal() {
    $('#f_import')[0].reset();
    const modal = document.getElementById('ImportModal');
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    $('body').addClass('overflow-hidden');
}

function closeImportModal() {
    const modal = document.getElementById('ImportModal');
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    $('body').removeClass('overflow-hidden');
}

// Delete function
function deleteSupplier() {
    if (!confirm('Yakin hapus data ini?')) {
        return;
    }
    
    $.ajax({
        type: "POST",
        data: {
            _id: $('#_id').val(),
            _item: $('#ps_name').val()
        },
        dataType: 'json',
        url: "{{ url('ps_delete') }}",
        success: function(r) {
            if (r.status == '200') {
                showToast('Data berhasil dihapus', 'success');
                closeModal();
                loadSupplierData();
            } else {
                showToast('Data gagal dihapus', 'error');
            }
        },
        error: function() {
            showToast('Data gagal dihapus', 'error');
        }
    });
}

// Export menu toggle
function toggleExportMenu() {
    const menu = document.getElementById('exportMenu');
    menu.classList.toggle('hidden');
}

// Close export menu when clicking outside
$(document).on('click', function(e) {
    if (!$(e.target).closest('[onclick*="toggleExportMenu"]').length && 
        !$(e.target).closest('#exportMenu').length) {
        $('#exportMenu').addClass('hidden');
    }
});

// Toast notification
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    let bgColor, textColor, iconColor, icon;
    switch (type) {
        case 'success':
            bgColor = 'bg-green-50';
            textColor = 'text-green-800';
            iconColor = 'text-green-500';
            icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
            break;
        case 'error':
            bgColor = 'bg-red-50';
            textColor = 'text-red-800';
            iconColor = 'text-red-500';
            icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
            break;
        case 'warning':
            bgColor = 'bg-yellow-50';
            textColor = 'text-yellow-800';
            iconColor = 'text-yellow-500';
            icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
            break;
        default:
            bgColor = 'bg-blue-50';
            textColor = 'text-blue-800';
            iconColor = 'text-blue-500';
            icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
    }
    
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `flex items-center w-full max-w-xs p-4 ${bgColor} ${textColor} rounded-lg shadow-lg border border-gray-200`;
    
    toast.innerHTML = `
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${iconColor}">
            ${icon}
        </div>
        <div class="ml-3 text-sm font-medium flex-1">${message}</div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 ${bgColor} ${textColor} rounded-lg p-1.5 inline-flex h-8 w-8 items-center justify-center" onclick="document.getElementById('${toastId}').remove()">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    `;
    
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        if (document.getElementById(toastId)) {
            document.getElementById(toastId).remove();
        }
    }, 3000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'fixed top-5 right-5 z-[9999] space-y-2';
    document.body.appendChild(container);
    return container;
}
</script>
@endpush
