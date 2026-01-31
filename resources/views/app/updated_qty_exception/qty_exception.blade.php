@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage qty exceptions</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Export Button -->
            <div class="relative">
                <button type="button" 
                        onclick="toggleExportDropdownQty('export-dropdown-qty')"
                        class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2">
                    <i class="cft-standard-stroke cft-download"></i>
                    Export
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div id="export-dropdown-qty" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div class="py-2">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Bentuk File:</div>
                        <a href="#" 
                           id="qty_exception_excel_btn"
                           onclick="exportQtyExceptionExcel(); return false;"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <i class="fas fa-file-excel text-green-600"></i>
                            Excel
                        </a>
                    </div>
                </div>
            </div>
            <!-- Add Button -->
            <button onclick="openAddModal()" 
                    id="add_qty_exception_btn"
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
                       id="qty_exception_search"
                       class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Cari artikel...">
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden p-5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="QEtb">
                <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                    <tr>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Brand</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Artikel</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Warna</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Size</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Qty</th>
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
<div id="QEModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeQEModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_qty_exception">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                <input type="hidden" name="_pst_id" id="_pst_id" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Web Qty Exception</h3>
                    <button type="button" 
                            onclick="closeQEModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-4 md:p-5 space-y-4">
                    <div>
                        <label for="product_name_input" class="block text-sm font-medium text-gray-700 mb-2">Artikel <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="product_name_input"
                               class="w-full px-4 py-2 border-2 border-gray-900 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="nama size warna brand">
                        <div id="itemList" class="mt-2"></div>
                    </div>
                    <div>
                        <label for="qe_qty" class="block text-sm font-medium text-gray-700 mb-2">Qty</label>
                        <input type="text" 
                               id="qe_qty" 
                               name="qe_qty"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeQEModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="button" 
                            id="delete_qty_exception_btn"
                            onclick="deleteQtyException()"
                            style="display:none;"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-500 transition-colors">
                        Hapus
                    </button>
                    <button type="submit" 
                            id="save_qty_exception_btn"
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
<!-- Simple-datatables CSS -->
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.2.0/dist/style.css" rel="stylesheet" />
@endpush

@push('scripts')
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
let qtyExceptionDataTable;
let qtyExceptionDataArray = [];

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadQtyExceptionData();

    // Search handler
    $('#qty_exception_search').on('keyup', function() {
        loadQtyExceptionData();
    });

    // Product name autocomplete
    $('#product_name_input').on('keyup', function() {
        var query = $(this).val();
        if ($.trim(query) != '' && $.trim(query) != null && query.length >= 2) {
            $.ajax({
                url: "{{ url('autocomplete_article') }}",
                method: "POST",
                data: {
                    query: query
                },
                success: function(data) {
                    $('#itemList').fadeIn();
                    $('#itemList').html(data);
                }
            });
        } else {
            $('#itemList').fadeOut();
        }
    });

    // Click on item list
    $(document).on('click', '#add_to_item_list', function(e) {
        var pst_id = $(this).attr('data-pst_id');
        var p_name = $(this).attr('data-p_name');
        $('#_pst_id').val(pst_id);
        $('#product_name_input').val(p_name);
        $('#itemList').fadeOut();
    });

    // Click outside to close item list
    $(document).on('click', 'body', function(e) {
        if (!$(e.target).closest('#product_name_input, #itemList').length) {
            $('#itemList').fadeOut();
        }
    });

    // Form submit handler
    $('#f_qty_exception').on('submit', function(e) {
        e.preventDefault();
        $("#save_qty_exception_btn").html('Proses ..');
        $("#save_qty_exception_btn").attr("disabled", true);
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('qe_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_qty_exception_btn").html('Simpan');
                $("#save_qty_exception_btn").attr("disabled", false);
                if (data.status == '200') {
                    closeQEModal();
                    toast('Berhasil', 'Data berhasil disimpan', 'success');
                    loadQtyExceptionData();
                } else if (data.status == '400') {
                    closeQEModal();
                    toast('Gagal', 'Data tidak tersimpan', 'error');
                }
            },
            error: function(data) {
                $("#save_qty_exception_btn").html('Simpan');
                $("#save_qty_exception_btn").attr("disabled", false);
                toast('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
            }
        });
    });

    // Delete handler
    window.deleteQtyException = function() {
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
                    url: "{{ url('qe_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toast('Berhasil', 'Data berhasil dihapus', 'success');
                            closeQEModal();
                            loadQtyExceptionData();
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

function loadQtyExceptionData() {
    $.ajax({
        url: "{{ url('qe_datatables') }}",
        type: 'GET',
        data: {
            search: $('#qty_exception_search').val(),
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (qtyExceptionDataTable) {
                qtyExceptionDataTable.destroy();
            }
            
            // Store data array for later use
            qtyExceptionDataArray = response.data || [];
            
            // Clear table body
            $('#QEtb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50 cursor-pointer" data-id="' + row.id + '" data-pst_id="' + (row.pst_id || '') + '" data-br_name="' + escapeHtml(row.br_name || '') + '" data-p_name="' + escapeHtml(row.p_name || '') + '" data-p_color="' + escapeHtml(row.p_color || '') + '" data-sz_name="' + escapeHtml(row.sz_name || '') + '" data-qe_qty="' + (row.qe_qty || '') + '">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.br_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' + escapeHtml(row.p_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.p_color || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.sz_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.qe_qty || '0') + '</td>';
                    html += '</tr>';
                    $('#QEtb tbody').append(html);
                });
            } else {
                $('#QEtb tbody').append('<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("QEtb") && typeof simpleDatatables !== 'undefined') {
                qtyExceptionDataTable = new simpleDatatables.DataTable("#QEtb", {
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
                    $('#QEtb tbody').off('click', 'tr').on('click', 'tr', function() {
                        var $row = $(this);
                        var id = $row.data('id');
                        var pst_id = $row.data('pst_id');
                        var br_name = $row.data('br_name');
                        var p_name = $row.data('p_name');
                        var p_color = $row.data('p_color');
                        var sz_name = $row.data('sz_name');
                        var qe_qty = $row.data('qe_qty');
                        
                        if (id) {
                            $('#_id').val(id);
                            $('#_mode').val('edit');
                            $('#_pst_id').val(pst_id);
                            $('#product_name_input').val('[' + br_name + '] ' + p_name + ' ' + p_color + ' ' + sz_name);
                            $('#qe_qty').val(qe_qty);
                            @if ($data['user']->delete_access == '1')
                                $('#delete_qty_exception_btn').show();
                            @endif
                            openQEModal();
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
    $('#_pst_id').val('');
    $('#f_qty_exception')[0].reset();
    $('#itemList').hide();
    $('#delete_qty_exception_btn').hide();
    openQEModal();
}

function openQEModal() {
    document.getElementById('QEModal').classList.remove('hidden');
    document.getElementById('QEModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeQEModal() {
    document.getElementById('QEModal').classList.add('hidden');
    document.getElementById('QEModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function toggleExportDropdownQty(dropdownId) {
    var dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleExportDropdownQty"]') && !event.target.closest('#export-dropdown-qty')) {
        var dropdown = document.getElementById('export-dropdown-qty');
        if (dropdown) {
            dropdown.classList.add('hidden');
        }
    }
});

function exportQtyExceptionExcel() {
    // Get all data from the table
    var data = qtyExceptionDataArray || [];
    
    if (data.length === 0) {
        toast('Info', 'Tidak ada data untuk diekspor', 'warning');
        return;
    }
    
    // Create CSV content
    var csvContent = "No,Brand,Artikel,Warna,Size,Qty\n";
    data.forEach(function(row, index) {
        csvContent += (index + 1) + ",";
        csvContent += '"' + (row.br_name || '').replace(/"/g, '""') + '",';
        csvContent += '"' + (row.p_name || '').replace(/"/g, '""') + '",';
        csvContent += '"' + (row.p_color || '').replace(/"/g, '""') + '",';
        csvContent += '"' + (row.sz_name || '').replace(/"/g, '""') + '",';
        csvContent += (row.qe_qty || '0') + "\n";
    });
    
    // Create blob and download
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    var url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'qty_exception_' + new Date().getTime() + '.csv');
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
