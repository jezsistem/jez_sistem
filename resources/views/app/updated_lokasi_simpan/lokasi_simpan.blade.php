@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage product locations</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Export Dropdown -->
            <div class="relative">
                <button type="button" 
                        onclick="toggleExportMenu()"
                        class="px-4 py-2 bg-red-100 text-red-500 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors flex items-center gap-2">
                    <i class="cft-standard-stroke cft-download"></i>
                    Export
                </button>
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div class="py-1">
                        <a href="#" id="product_location_excel_btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="cft-standard-stroke cft-excel mr-2"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
            </div>
            <!-- Add Button -->
            <button onclick="openAddModal()" 
                    id="add_product_location_btn"
                    class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-500 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-add"></i>
                Data Baru
            </button>
        </div>
    </div>

    <!-- Store Filter Section -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Store</label>
            <div id="st_id_parent">
                <select id="st_id" 
                        name="st_id"
                        class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">- Pilih Store -</option>
                    @foreach ($data['st_id'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Table Section (Hidden by default, shown when store is selected) -->
    <div id="product_location_display" class="hidden bg-white rounded-lg border border-gray-200 overflow-hidden p-5">
        <!-- Search Input -->
        <div class="mb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="cft-standard-stroke cft-search text-gray-400"></i>
                </div>
                <input type="search" 
                       id="product_location_search"
                       class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Cari lokasi simpan...">
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="ProductLocationtb">
                <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                    <tr>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Store</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Kode</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Nama Lokasi</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Deskripsi</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Default Filled</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Default Refund</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">BIN Freeze</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Detail</th>
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
<div id="ProductLocationModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeProductLocationModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_product_location">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Lokasi Simpan</h3>
                    <button type="button" 
                            onclick="closeProductLocationModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-4 md:p-5 space-y-4">
                    <div>
                        <label for="pl_code" class="block text-sm font-medium text-gray-700 mb-2">Kode Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="pl_code" 
                               name="pl_code" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                    <div>
                        <label for="pl_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="pl_name" 
                               name="pl_name" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                    <div>
                        <label for="pl_description" class="block text-sm font-medium text-gray-700 mb-2">Nama Kota (eg: MALANG)</label>
                        <input type="text" 
                               id="pl_description" 
                               name="pl_description"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                    <div>
                        <label for="pl_default" class="block text-sm font-medium text-gray-700 mb-2">Default Penerimaan</label>
                        <select id="pl_default" 
                                name="pl_default"
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="0" selected>No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div>
                        <label for="pl_default_refund" class="block text-sm font-medium text-gray-700 mb-2">Default Refund</label>
                        <select id="pl_default_refund" 
                                name="pl_default_refund"
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="0" selected>No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div>
                        <label for="pl_capacity" class="block text-sm font-medium text-gray-700 mb-2">Kapasitas Bin (pcs/pairs)</label>
                        <input type="number" 
                               id="pl_capacity" 
                               name="pl_capacity"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeProductLocationModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="button" 
                            id="delete_product_location_btn"
                            onclick="deleteProductLocation()"
                            style="display:none;"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-500 transition-colors">
                        Hapus
                    </button>
                    <button type="submit" 
                            id="save_product_location_btn"
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
let productLocationDataTable;
let productLocationDataArray = [];

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Select2 for Store
    $('#st_id').select2({
        width: "100%",
        dropdownParent: $('#st_id_parent')
    });
    $('#st_id').on('select2:open', function(e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });

    // Store change handler
    $('#st_id').on('change', function() {
        var label = $('#st_id option:selected').text();
        if (label != '- Pilih Store -' && $(this).val() != '') {
            $('#product_location_display').removeClass('hidden');
            loadProductLocationData();
        } else {
            $('#product_location_display').addClass('hidden');
        }
    });

    // Search handler
    $('#product_location_search').on('keyup', function() {
        loadProductLocationData();
    });

    // Form submit handler
    $('#f_product_location').on('submit', function(e) {
        e.preventDefault();
        $("#save_product_location_btn").html('Proses ..');
        $("#save_product_location_btn").attr("disabled", true);

        var formData = new FormData(this);
        formData.append('st_id', $('#st_id').val());

        $.ajax({
            type: 'POST',
            url: "{{ url('pl_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_product_location_btn").html('Simpan');
                $("#save_product_location_btn").attr("disabled", false);

                if (data.status == '200') {
                    closeProductLocationModal();
                    toast('Berhasil', 'Data berhasil disimpan', 'success');
                    loadProductLocationData();
                } else if (data.status == '400') {
                    closeProductLocationModal();
                    toast('Gagal', 'Data tidak tersimpan', 'error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#save_product_location_btn").html('Simpan');
                $("#save_product_location_btn").attr("disabled", false);
                toast('Error', 'Terjadi kesalahan: ' + errorThrown, 'error');
            }
        });
    });

    // Delete handler
    window.deleteProductLocation = function() {
        swal({
            title: "Hapus..? ",
            text: "Yakin hapus data ini ?",
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
                    url: "{{ url('pl_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toast('Berhasil', 'Data berhasil dihapus', 'success');
                            closeProductLocationModal();
                            loadProductLocationData();
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

    // Code check handler
    $('#pl_code').on('change', function() {
        var pl_code = $(this).val();
        var st_id = $('#st_id').val();
        if (!pl_code || !st_id) return;
        
        $.ajax({
            type: "POST",
            data: {
                _pl_code: pl_code,
                _st_id: st_id
            },
            dataType: 'json',
            url: "{{ url('pl_code_check_data') }}",
            success: function(r) {
                if (r.status == '200') {
                    swal("Sudah ada", "Kode sudah ada", "warning");
                    $('#pl_code').val('');
                }
            }
        });
    });

});

function loadProductLocationData() {
    $.ajax({
        url: "{{ url('product_location_datatables') }}",
        type: 'GET',
        data: {
            search: $('#product_location_search').val(),
            st_id: $('#st_id').val(),
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (productLocationDataTable) {
                productLocationDataTable.destroy();
            }
            
            // Store data array for later use
            productLocationDataArray = response.data || [];
            
            // Clear table body
            $('#ProductLocationtb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.st_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.pl_code || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.pl_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.pl_description || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + escapeHtml(row.pl_default_show || 'No') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + escapeHtml(row.pl_refund || 'No') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.pl_freeze || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.detail || '') + '</td>';
                    html += '</tr>';
                    $('#ProductLocationtb tbody').append(html);
                });
            } else {
                $('#ProductLocationtb tbody').append('<tr><td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("ProductLocationtb") && typeof simpleDatatables !== 'undefined') {
                productLocationDataTable = new simpleDatatables.DataTable("#ProductLocationtb", {
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
                
                // Handle detail button click after datatable is initialized
                setTimeout(function() {
                    $('#ProductLocationtb tbody').off('click', '.btn-detail').on('click', '.btn-detail', function() {
                        let pl_id = $(this).data('plid');
                        showProductLocationModal(pl_id);
                    });
                    
                    // Handle toggle freeze
                    $('#ProductLocationtb tbody').off('change', '.toggle-freeze').on('change', '.toggle-freeze', function() {
                        let plid = $(this).data('id');
                        let isChecked = $(this).is(':checked') ? '1' : '0';
                        $.ajax({
                            url: '{{ url("pl_freeze_status") }}',
                            method: 'POST',
                            data: {
                                plid: plid,
                                pl_freeze: isChecked,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                toast('Berhasil', 'Data berhasil diubah', 'success');
                                loadProductLocationData();
                            },
                            error: function(xhr) {
                                toast('Gagal', 'Gagal mengubah status.', 'error');
                            }
                        });
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

function showProductLocationModal(pl_id) {
    // Find data from stored array
    let rowData = productLocationDataArray.find(row => row.pl_id == pl_id);
    if (rowData) {
        $('#pl_code').val(rowData.pl_code);
        $('#pl_name').val(rowData.pl_name);
        $('#pl_description').val(rowData.pl_description);
        $('#pl_default').val(rowData.pl_default);
        $('#pl_default_refund').val(rowData.pl_default_refund);
        $('#pl_capacity').val(rowData.pl_capacity);
        $('#_id').val(rowData.pl_id);
        $('#_mode').val('edit');
        @if ($data['user']->delete_access == '1')
            $('#delete_product_location_btn').show();
        @endif
        openProductLocationModal();
    }
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

function openAddModal() {
    $('#_id').val('');
    $('#_mode').val('add');
    $('#f_product_location')[0].reset();
    $('#delete_product_location_btn').hide();
    openProductLocationModal();
}

function openProductLocationModal() {
    document.getElementById('ProductLocationModal').classList.remove('hidden');
    document.getElementById('ProductLocationModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeProductLocationModal() {
    document.getElementById('ProductLocationModal').classList.add('hidden');
    document.getElementById('ProductLocationModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function toggleExportMenu() {
    const menu = document.getElementById('exportMenu');
    menu.classList.toggle('hidden');
}

// Close export menu when clicking outside
document.addEventListener('click', function(event) {
    const exportMenu = document.getElementById('exportMenu');
    const exportButton = event.target.closest('[onclick="toggleExportMenu()"]');
    if (!exportButton && !exportMenu.contains(event.target)) {
        exportMenu.classList.add('hidden');
    }
});
</script>
@endpush
