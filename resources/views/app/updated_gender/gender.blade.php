@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage gender information</p>
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
                        <a href="#" id="gender_excel_btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
                   id="gender_search"
                   class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                   placeholder="Cari nama gender...">
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden p-5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="Gendertb">
                <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                    <tr>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Gender</th>
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
<div id="GenderModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_gender">
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
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="gn_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="gn_name" 
                               name="gn_name" 
                               required
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
                            id="delete_gender_btn" 
                            onclick="deleteGender()"
                            style="display:none;"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors">
                        Hapus
                    </button>
                    <button type="submit" 
                            id="save_gender_btn"
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
                        <a href="{{ asset('upload/template/supplier_template.xlsx') }}"
                           class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            Download
                        </a>
                    </div>
                    <div>
                        <label for="gn_template" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih template yang sudah di download dan diisi <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                               id="gn_template" 
                               name="gn_template" 
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
let gender_dataTable;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load data and initialize simple-datatables
    loadGenderData();

    $('#gender_search').on('keyup', function() {
        loadGenderData();
    });

    // Check duplicate gender name
    $('#gn_name').on('change', function() {
        var gn_name = $(this).val();
        if (!gn_name) return;
        
        $.ajax({
            type: "POST",
            data: {
                _gn_name: gn_name
            },
            dataType: 'json',
            url: "{{ url('check_exists_gender') }}",
            success: function(r) {
                if (r.status == '200') {
                    showToast('Gender sudah ada di sistem, silahkan ganti dengan yang lain', 'warning');
                    $('#gn_name').val('');
                }
            }
        });
    });

    // Form submission
    $('#f_gender').on('submit', function(e) {
        e.preventDefault();
        $("#save_gender_btn").html('Proses...');
        $("#save_gender_btn").prop("disabled", true);
        
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('gn_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_gender_btn").html('Simpan');
                $("#save_gender_btn").prop("disabled", false);
                if (data.status == '200') {
                    closeModal();
                    loadGenderData();
                    showToast('Data berhasil disimpan', 'success');
                } else if (data.status == '400') {
                    closeModal();
                    showToast('Data tidak tersimpan', 'error');
                }
            },
            error: function(xhr) {
                $("#save_gender_btn").html('Simpan');
                $("#save_gender_btn").prop("disabled", false);
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
            url: "{{ url('gn_import') }}",
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
                    loadGenderData();
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

function loadGenderData() {
    $.ajax({
        url: "{{ url('gender_datatables') }}",
        type: 'GET',
        data: {
            search: $('#gender_search').val(),
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (gender_dataTable) {
                gender_dataTable.destroy();
            }
            
            // Clear table body
            $('#Gendertb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50 cursor-pointer">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" data-id="' + row.id + '" data-name="' + escapeHtml(row.gn_name || '') + '">' + escapeHtml(row.gn_name || '') + '</td>';
                    html += '</tr>';
                    $('#Gendertb tbody').append(html);
                });
            } else {
                $('#Gendertb tbody').append('<tr><td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("Gendertb") && typeof simpleDatatables !== 'undefined') {
                gender_dataTable = new simpleDatatables.DataTable("#Gendertb", {
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
                    $('#Gendertb tbody').off('click').on('click', 'tr', function() {
                        var $row = $(this);
                        var id = $row.find('td[data-id]').data('id');
                        var name = $row.find('td[data-id]').data('name');
                        
                        if (id) {
                            openEditModal(id, name);
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
    $('#f_gender')[0].reset();
    $('#delete_gender_btn').hide();
    $('#modalTitle').text('Tambah {{ $data["subtitle"] }}');
    openModal();
}

function openEditModal(id, name) {
    $('#gn_name').val(name);
    $('#_id').val(id);
    $('#_mode').val('edit');
    $('#delete_gender_btn').show();
    $('#modalTitle').text('Edit {{ $data["subtitle"] }}');
    openModal();
}

function openModal() {
    const modal = document.getElementById('GenderModal');
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    $('body').addClass('overflow-hidden');
}

function closeModal() {
    const modal = document.getElementById('GenderModal');
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
function deleteGender() {
    if (!confirm('Yakin hapus data ini?')) {
        return;
    }
    
    $.ajax({
        type: "POST",
        data: {
            _id: $('#_id').val(),
            _item: $('#gn_name').val()
        },
        dataType: 'json',
        url: "{{ url('gn_delete') }}",
        success: function(r) {
            if (r.status == '200') {
                showToast('Data berhasil dihapus', 'success');
                closeModal();
                loadGenderData();
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

// Toast notification (reuse from supplier)
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
