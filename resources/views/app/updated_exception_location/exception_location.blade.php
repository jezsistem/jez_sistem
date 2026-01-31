@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage exception locations</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Export Button -->
            <div class="relative">
                <button type="button" 
                        onclick="toggleExportDropdown('export-dropdown-exception')"
                        class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2">
                    <i class="cft-standard-stroke cft-download"></i>
                    Export
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div id="export-dropdown-exception" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div class="py-2">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Bentuk File:</div>
                        <a href="#" 
                           id="exception_location_excel_btn"
                           onclick="exportExceptionLocationExcel(); return false;"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <i class="fas fa-file-excel text-green-600"></i>
                            Excel
                        </a>
                    </div>
                </div>
            </div>
            <!-- Add Button -->
            <button onclick="openAddModal()" 
                    id="add_exception_location_btn"
                    class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-add"></i>
                Data Baru
            </button>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="mb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="cft-standard-stroke cft-search text-gray-400"></i>
                </div>
                <input type="search" 
                       id="exception_location_search"
                       class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Cari lokasi produk...">
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden p-5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="ExceptionLocationtb">
                <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                    <tr>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Store</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Lokasi</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Qty</th>
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
<div id="ExceptionLocationModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeExceptionLocationModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_exception_location">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Exception Location</h3>
                    <button type="button" 
                            onclick="closeExceptionLocationModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-4 md:p-5 space-y-4">
                    <div>
                        <label for="pl_id" class="block text-sm font-medium text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                        <select id="pl_id" 
                                name="pl_id"
                                required
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">- Pilih Lokasi -</option>
                            @foreach ($data['pl_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="pl_id_parent"></div>
                    </div>
                    <div>
                        <label for="el_description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <input type="text" 
                               id="el_description" 
                               name="el_description"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeExceptionLocationModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="button" 
                            id="delete_exception_location_btn"
                            onclick="deleteExceptionLocation()"
                            style="display:none;"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-500 transition-colors">
                        Hapus
                    </button>
                    <button type="submit" 
                            id="save_exception_location_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Simple-datatables CSS -->
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.2.0/dist/style.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Simple-datatables JS -->
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.2.0/dist/umd/simple-datatables.min.js"></script>
<!-- SweetAlert JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
<!-- jQuery Toast JS -->
<script src="{{ asset('cdn/jquery.toast.min.js') }}"></script>

<script>
let exceptionLocationDataTable;
let exceptionLocationDataArray = [];

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Select2 for location
    $('#pl_id').select2({
        width: "100%",
        dropdownParent: $('#pl_id_parent')
    });

    // Load initial data
    loadExceptionLocationData();

    // Search handler
    $('#exception_location_search').on('keyup', function() {
        loadExceptionLocationData();
    });

    // Form submit handler
    $('#f_exception_location').on('submit', function(e) {
        e.preventDefault();
        $("#save_exception_location_btn").html('Proses ..');
        $("#save_exception_location_btn").attr("disabled", true);
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('el_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_exception_location_btn").html('Simpan');
                $("#save_exception_location_btn").attr("disabled", false);
                if (data.status == '200') {
                    closeExceptionLocationModal();
                    toast('Berhasil', 'Data berhasil disimpan', 'success');
                    loadExceptionLocationData();
                } else if (data.status == '400') {
                    closeExceptionLocationModal();
                    toast('Gagal', 'Data tidak tersimpan', 'error');
                }
            },
            error: function(data) {
                $("#save_exception_location_btn").html('Simpan');
                $("#save_exception_location_btn").attr("disabled", false);
                toast('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
            }
        });
    });

    // Delete handler
    window.deleteExceptionLocation = function() {
        swal({
            title: "Hapus..?",
            text: "Yakin hapus data ini?",
            icon: "warning",
            buttons: [
                'Batalkan',
                'Hapus'
            ],
            dangerMode: true,
        }).then(function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    data: {
                        _id: $('#_id').val()
                    },
                    dataType: 'json',
                    url: "{{ url('el_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toast('Berhasil', 'Data berhasil dihapus', 'success');
                            closeExceptionLocationModal();
                            loadExceptionLocationData();
                        } else {
                            toast('Gagal', 'Gagal hapus data', 'error');
                        }
                    },
                    error: function() {
                        toast('Error', 'Terjadi kesalahan saat menghapus data', 'error');
                    }
                });
            }
        });
    };
});

function loadExceptionLocationData() {
    $.ajax({
        url: "{{ url('exception_location_datatables') }}",
        type: 'GET',
        data: {
            search: $('#exception_location_search').val(),
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (exceptionLocationDataTable) {
                exceptionLocationDataTable.destroy();
            }
            
            // Store data array for later use
            exceptionLocationDataArray = response.data || [];
            
            // Clear table body
            $('#ExceptionLocationtb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50 cursor-pointer" data-id="' + row.el_id + '" data-pl_id="' + (row.pl_id || '') + '" data-el_description="' + escapeHtml(row.el_description || '') + '">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.st_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' + escapeHtml(row.pl_code || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.qty || '0') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.el_description || '') + '</td>';
                    html += '</tr>';
                    $('#ExceptionLocationtb tbody').append(html);
                });
            } else {
                $('#ExceptionLocationtb tbody').append('<tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("ExceptionLocationtb") && typeof simpleDatatables !== 'undefined') {
                exceptionLocationDataTable = new simpleDatatables.DataTable("#ExceptionLocationtb", {
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
                        perPage: "",
                        noRows: "Tidak ada data",
                        info: "Menampilkan {start} sampai {end} dari {rows} data",
                        noResults: "Tidak ada hasil pencarian"
                    }
                });
                
                // Handle row click after datatable is initialized
                setTimeout(function() {
                    $('#ExceptionLocationtb tbody').off('click', 'tr').on('click', 'tr', function() {
                        var $row = $(this);
                        var id = $row.data('id');
                        var pl_id = $row.data('pl_id');
                        var el_description = $row.data('el_description');
                        
                        if (id) {
                            $('#_id').val(id);
                            $('#_mode').val('edit');
                            $('#pl_id').val(pl_id).trigger('change');
                            $('#el_description').val(el_description);
                            $('#delete_exception_location_btn').show();
                            openExceptionLocationModal();
                        }
                    });
                }, 100);
            }
        },
        error: function(xhr) {
            console.error('Error loading data:', xhr);
            toast('Error', 'Gagal memuat data', 'error');
        }
    });
}

function openAddModal() {
    $('#_id').val('');
    $('#_mode').val('add');
    $('#f_exception_location')[0].reset();
    $('#pl_id').val('').trigger('change');
    $('#delete_exception_location_btn').hide();
    openExceptionLocationModal();
}

function openExceptionLocationModal() {
    document.getElementById('ExceptionLocationModal').classList.remove('hidden');
    document.getElementById('ExceptionLocationModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeExceptionLocationModal() {
    document.getElementById('ExceptionLocationModal').classList.add('hidden');
    document.getElementById('ExceptionLocationModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function toggleExportDropdown(dropdownId) {
    var dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleExportDropdown"]') && !event.target.closest('#export-dropdown-exception')) {
        var dropdown = document.getElementById('export-dropdown-exception');
        if (dropdown) {
            dropdown.classList.add('hidden');
        }
    }
});

function exportExceptionLocationExcel() {
    // Get all data from the table
    var data = exceptionLocationDataArray || [];
    
    if (data.length === 0) {
        toast('Info', 'Tidak ada data untuk diekspor', 'warning');
        return;
    }
    
    // Create CSV content
    var csvContent = "No,Store,Lokasi,Qty,Deskripsi\n";
    data.forEach(function(row, index) {
        csvContent += (index + 1) + ",";
        csvContent += '"' + (row.st_name || '').replace(/"/g, '""') + '",';
        csvContent += '"' + (row.pl_code || '').replace(/"/g, '""') + '",';
        csvContent += (row.qty || '0') + ",";
        csvContent += '"' + (row.el_description || '').replace(/"/g, '""') + '"\n';
    });
    
    // Create blob and download
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    var url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'exception_location_' + new Date().getTime() + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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
</script>
@endpush
