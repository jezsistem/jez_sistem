@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage instock approval configurations</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Add Button -->
            <button onclick="openAddModal()" 
                    id="add_btn"
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
                       id="data_search"
                       class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Cari store...">
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden p-5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="Datatb">
                <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                    <tr>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Store</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Approval Instock 1</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Approval Instock 2</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Akses Setup Exception 1</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Akses Setup Exception 2</th>
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
<div id="DataModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeDataModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="form">
                @csrf
                <input type="hidden" name="id" id="id" value="" />
                <input type="hidden" name="mode" id="mode" value="" />
                
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $data['subtitle'] }}</h3>
                    <button type="button" 
                            onclick="closeDataModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-4 md:p-5 space-y-4">
                    <div>
                        <label for="st_id" class="block text-sm font-medium text-gray-700 mb-2">Store <span class="text-red-500">*</span></label>
                        <select id="st_id" 
                                name="st_id"
                                required
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">- Pilih -</option>
                            @foreach ($data['st_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="instock_u_id_1" class="block text-sm font-medium text-gray-700 mb-2">User Instock 1 <span class="text-red-500">*</span></label>
                        <select id="instock_u_id_1" 
                                name="instock_u_id_1"
                                required
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">- Pilih -</option>
                            @foreach ($data['u_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="instock_u_id_2" class="block text-sm font-medium text-gray-700 mb-2">User Instock 2</label>
                        <select id="instock_u_id_2" 
                                name="instock_u_id_2"
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">- Pilih -</option>
                            @foreach ($data['u_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="exception_u_id_1" class="block text-sm font-medium text-gray-700 mb-2">User Exception 1 <span class="text-red-500">*</span></label>
                        <select id="exception_u_id_1" 
                                name="exception_u_id_1"
                                required
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">- Pilih -</option>
                            @foreach ($data['u_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="exception_u_id_2" class="block text-sm font-medium text-gray-700 mb-2">User Exception 2</label>
                        <select id="exception_u_id_2" 
                                name="exception_u_id_2"
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">- Pilih -</option>
                            @foreach ($data['u_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeDataModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="button" 
                            id="delete_btn"
                            onclick="deleteData()"
                            style="display:none;"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-500 transition-colors">
                        Hapus
                    </button>
                    <button type="submit" 
                            id="save_btn"
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
let instockApprovalDataTable;
let instockApprovalDataArray = [];

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadInstockApprovalData();

    // Search handler
    $('#data_search').on('keyup', function() {
        loadInstockApprovalData();
    });

    // Form submit handler
    $('#form').on('submit', function(e) {
        e.preventDefault();
        $("#save_btn").html('Proses ..');
        $("#save_btn").attr("disabled", true);
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('ia_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_btn").html('Simpan');
                $("#save_btn").attr("disabled", false);
                if (data.status == '200') {
                    closeDataModal();
                    toast('Berhasil', 'Data berhasil disimpan', 'success');
                    loadInstockApprovalData();
                } else if (data.status == '400') {
                    closeDataModal();
                    toast('Gagal', 'Data tidak tersimpan', 'error');
                }
            },
            error: function(data) {
                $("#save_btn").html('Simpan');
                $("#save_btn").attr("disabled", false);
                toast('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
            }
        });
    });

    // Delete handler
    window.deleteData = function() {
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
                        id: $('#id').val()
                    },
                    dataType: 'json',
                    url: "{{ url('ia_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toast('Berhasil', 'Data berhasil dihapus', 'success');
                            closeDataModal();
                            loadInstockApprovalData();
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

function loadInstockApprovalData() {
    $.ajax({
        url: "{{ url('ia_datatables') }}",
        type: 'GET',
        data: {
            search: $('#data_search').val(),
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (instockApprovalDataTable) {
                instockApprovalDataTable.destroy();
            }
            
            // Store data array for later use
            instockApprovalDataArray = response.data || [];
            
            // Clear table body
            $('#Datatb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50 cursor-pointer" data-id="' + row.id + '" data-st_id="' + (row.st_id || '') + '" data-instock_u_id_1="' + (row.instock_u_id_1 || '') + '" data-instock_u_id_2="' + (row.instock_u_id_2 || '') + '" data-exception_u_id_1="' + (row.exception_u_id_1 || '') + '" data-exception_u_id_2="' + (row.exception_u_id_2 || '') + '">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' + escapeHtml(row.st_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.instock_u_name_1 || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.instock_u_name_2 || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.exception_u_name_1 || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.exception_u_name_2 || '') + '</td>';
                    html += '</tr>';
                    $('#Datatb tbody').append(html);
                });
            } else {
                $('#Datatb tbody').append('<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("Datatb") && typeof simpleDatatables !== 'undefined') {
                instockApprovalDataTable = new simpleDatatables.DataTable("#Datatb", {
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
                    $('#Datatb tbody').off('click', 'tr').on('click', 'tr', function() {
                        var $row = $(this);
                        var id = $row.data('id');
                        var st_id = $row.data('st_id');
                        var instock_u_id_1 = $row.data('instock_u_id_1');
                        var instock_u_id_2 = $row.data('instock_u_id_2');
                        var exception_u_id_1 = $row.data('exception_u_id_1');
                        var exception_u_id_2 = $row.data('exception_u_id_2');
                        
                        if (id) {
                            $('#id').val(id);
                            $('#mode').val('edit');
                            $('#st_id').val(st_id);
                            $('#instock_u_id_1').val(instock_u_id_1);
                            $('#instock_u_id_2').val(instock_u_id_2);
                            $('#exception_u_id_1').val(exception_u_id_1);
                            $('#exception_u_id_2').val(exception_u_id_2);
                            $('#delete_btn').show();
                            openDataModal();
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
    $('#id').val('');
    $('#mode').val('add');
    $('#form')[0].reset();
    $('#delete_btn').hide();
    openDataModal();
}

function openDataModal() {
    document.getElementById('DataModal').classList.remove('hidden');
    document.getElementById('DataModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeDataModal() {
    document.getElementById('DataModal').classList.add('hidden');
    document.getElementById('DataModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
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
