@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage instock list and approvals</p>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="space-y-6">
        <!-- Main Table Section -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="p-4 md:p-5 border-b border-gray-200">
                @if ($data['user']->g_name == 'administrator')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Store</label>
                    <select id="st_id" 
                            name="st_id"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="" selected>- Pilih -</option>
                        @foreach ($data['st_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="cft-standard-stroke cft-search text-gray-400"></i>
                        </div>
                        <input type="search" 
                               id="data_search"
                               class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Cari artikel...">
                    </div>
                </div>
            </div>
            <div class="p-4 md:p-5">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="Datatb">
                        <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                            <tr>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Tanggal</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Store</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Helper</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Artikel</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Qty</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">BIN</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Approval</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- History Table Section -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="p-4 md:p-5 border-b border-gray-200">
                @if ($data['user']->g_name == 'administrator')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Store</label>
                    <select id="st_id_history" 
                            name="st_id_history"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="" selected>- Pilih -</option>
                        @foreach ($data['st_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="cft-standard-stroke cft-search text-gray-400"></i>
                        </div>
                        <input type="search" 
                               id="history_data_search"
                               class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Cari artikel...">
                    </div>
                </div>
            </div>
            <div class="p-4 md:p-5">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="HistoryDatatb">
                        <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                            <tr>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Tanggal</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Store</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Helper</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Artikel</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Qty</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">BIN</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Approval</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Tanggal Approval</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
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
let instockListDataTable;
let instockListHistoryDataTable;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadInstockListData();
    loadInstockListHistoryData();

    // Search handlers
    $('#data_search').on('keyup', function() {
        loadInstockListData();
    });

    $('#history_data_search').on('keyup', function() {
        loadInstockListHistoryData();
    });

    // Store filter handlers
    $('#st_id').on('change', function() {
        loadInstockListData();
    });

    $('#st_id_history').on('change', function() {
        loadInstockListHistoryData();
    });

    // Confirm handler
    $(document).on('click', '#confirm', function(e) {
        e.preventDefault();
        var plst_id = $(this).data('plst_id');
        var plst_qty = $(this).data('plst_qty');
        var pls_id = $(this).data('pls_id');
        
        swal({
            title: "Confirm..?",
            text: "Yakin approve data ini?",
            icon: "warning",
            buttons: [
                'Batalkan',
                'Confirm'
            ],
            dangerMode: false,
        }).then(function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    data: {
                        plst_id: plst_id,
                        plst_qty: plst_qty,
                        pls_id: pls_id,
                        type: 'confirm'
                    },
                    dataType: 'json',
                    url: "{{ url('il_save') }}",
                    success: function(r) {
                        if (r.status == 200) {
                            toast('Berhasil', 'Data berhasil di-approve', 'success');
                            loadInstockListData();
                            loadInstockListHistoryData();
                        } else {
                            toast('Gagal', 'Gagal approve data', 'error');
                        }
                    },
                    error: function() {
                        toast('Error', 'Terjadi kesalahan saat approve data', 'error');
                    }
                });
            }
        });
    });

    // Decline handler
    $(document).on('click', '#decline', function(e) {
        e.preventDefault();
        var plst_id = $(this).data('plst_id');
        var plst_qty = $(this).data('plst_qty');
        var pls_id = $(this).data('pls_id');
        
        swal({
            title: "Decline..?",
            text: "Yakin decline data ini?",
            icon: "warning",
            buttons: [
                'Batalkan',
                'Decline'
            ],
            dangerMode: true,
        }).then(function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    data: {
                        plst_id: plst_id,
                        plst_qty: plst_qty,
                        pls_id: pls_id,
                        type: 'decline'
                    },
                    dataType: 'json',
                    url: "{{ url('il_save') }}",
                    success: function(r) {
                        if (r.status == 200) {
                            toast('Berhasil', 'Data berhasil di-decline', 'success');
                            loadInstockListData();
                            loadInstockListHistoryData();
                        } else {
                            toast('Gagal', 'Gagal decline data', 'error');
                        }
                    },
                    error: function() {
                        toast('Error', 'Terjadi kesalahan saat decline data', 'error');
                    }
                });
            }
        });
    });
});

function loadInstockListData() {
    $.ajax({
        url: "{{ url('il_datatables') }}",
        type: 'GET',
        data: {
            search: $('#data_search').val(),
            st_id: $('#st_id').val(),
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (instockListDataTable) {
                instockListDataTable.destroy();
            }
            
            // Clear table body
            $('#Datatb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.updated_at || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.st_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.u_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.article || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.plst_qty || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.pl_code || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.approve || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.action || '') + '</td>';
                    html += '</tr>';
                    $('#Datatb tbody').append(html);
                });
            } else {
                $('#Datatb tbody').append('<tr><td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("Datatb") && typeof simpleDatatables !== 'undefined') {
                instockListDataTable = new simpleDatatables.DataTable("#Datatb", {
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
            }
        },
        error: function(xhr) {
            console.error('Error loading data:', xhr);
            toast('Error', 'Gagal memuat data', 'error');
        }
    });
}

function loadInstockListHistoryData() {
    $.ajax({
        url: "{{ url('il_history_datatables') }}",
        type: 'GET',
        data: {
            search: $('#history_data_search').val(),
            st_id: $('#st_id_history').val(),
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (instockListHistoryDataTable) {
                instockListHistoryDataTable.destroy();
            }
            
            // Clear table body
            $('#HistoryDatatb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.updated_at || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.st_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.u_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.article || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.plst_qty || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.pl_code || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.approve || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.approve_date || '') + '</td>';
                    html += '</tr>';
                    $('#HistoryDatatb tbody').append(html);
                });
            } else {
                $('#HistoryDatatb tbody').append('<tr><td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("HistoryDatatb") && typeof simpleDatatables !== 'undefined') {
                instockListHistoryDataTable = new simpleDatatables.DataTable("#HistoryDatatb", {
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
            }
        },
        error: function(xhr) {
            console.error('Error loading history data:', xhr);
            toast('Error', 'Gagal memuat data history', 'error');
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
</script>
@endpush
