@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage B1G1 locations</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Store</label>
            <select id="st_id" 
                    name="st_id"
                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">- Pilih Store -</option>
                @foreach ($data['st_id'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="cft-standard-stroke cft-search text-gray-400"></i>
                </div>
                <input type="search" 
                       id="b1g1_location_search"
                       class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Cari lokasi produk...">
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden p-5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="BOGOtb">
                <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                    <tr>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Lokasi</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Pilih ?</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded by DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Simple-datatables CSS -->
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.2.0/dist/style.css" rel="stylesheet" />
<!-- jQuery Toast CSS -->
<link href="{{ asset('cdn/jquery.toast.min.css') }}" rel="stylesheet" />
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
let b1g1LocationDataTable;

// Toast function wrapper for jQuery Toast
function toast(title, message, type) {
    if (typeof $.toast !== 'undefined') {
        var iconType = 'success';
        if (type === 'error') {
            iconType = 'error';
        } else if (type === 'warning') {
            iconType = 'warning';
        } else if (type === 'info') {
            iconType = 'info';
        }
        
        $.toast({
            heading: title,
            text: message,
            icon: iconType,
            position: 'top-right',
            stack: false,
            hideAfter: 3000,
            showHideTransition: 'slide'
        });
    } else {
        // Fallback to alert if toast is not available
        alert(title + ': ' + message);
    }
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadB1G1LocationData();

    // Search handler
    $('#b1g1_location_search').on('keyup', function() {
        loadB1G1LocationData();
    });

    // Store filter handler
    $('#st_id').on('change', function() {
        loadB1G1LocationData();
    });

    // Checkbox handler for checked
    $(document).on('click', '#checked', function(e) {
        e.preventDefault();
        var pl_id = $(this).data('pl_id');
        var type = 'checked';
        $.ajax({
            type: "POST",
            data: {
                pl_id: pl_id,
                type: type
            },
            dataType: 'json',
            url: "{{ url('b1g1_update') }}",
            success: function(r) {
                if (r.status == 200) {
                    toast('Berhasil', 'Data berhasil diupdate', 'success');
                    loadB1G1LocationData();
                } else {
                    toast('Gagal', 'Gagal update data', 'error');
                }
            },
            error: function() {
                toast('Error', 'Terjadi kesalahan saat update data', 'error');
            }
        });
    });

    // Checkbox handler for unchecked
    $(document).on('click', '#unchecked', function(e) {
        e.preventDefault();
        var pl_id = $(this).data('pl_id');
        var type = 'unchecked';
        $.ajax({
            type: "POST",
            data: {
                pl_id: pl_id,
                type: type
            },
            dataType: 'json',
            url: "{{ url('b1g1_update') }}",
            success: function(r) {
                if (r.status == 200) {
                    toast('Berhasil', 'Data berhasil diupdate', 'success');
                    loadB1G1LocationData();
                } else {
                    toast('Gagal', 'Gagal update data', 'error');
                }
            },
            error: function() {
                toast('Error', 'Terjadi kesalahan saat update data', 'error');
            }
        });
    });
});

function loadB1G1LocationData() {
    $.ajax({
        url: "{{ url('b1g1_location_datatables') }}",
        type: 'GET',
        data: {
            search: $('#b1g1_location_search').val(),
            st_id: $('#st_id').val(),
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (b1g1LocationDataTable) {
                b1g1LocationDataTable.destroy();
            }
            
            // Clear table body
            $('#BOGOtb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var checkboxHtml = '';
                    if (row.pl_id != null && row.pl_id != '' && row.pl_id != undefined) {
                        checkboxHtml = '<input type="checkbox" data-pl_id="' + row.pl_id + '" id="unchecked" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"/>';
                    } else {
                        checkboxHtml = '<input type="checkbox" data-pl_id="' + row.id + '" id="checked" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"/>';
                    }
                    
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' + escapeHtml(row.pl_code || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + checkboxHtml + '</td>';
                    html += '</tr>';
                    $('#BOGOtb tbody').append(html);
                });
            } else {
                $('#BOGOtb tbody').append('<tr><td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("BOGOtb") && typeof simpleDatatables !== 'undefined') {
                b1g1LocationDataTable = new simpleDatatables.DataTable("#BOGOtb", {
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
