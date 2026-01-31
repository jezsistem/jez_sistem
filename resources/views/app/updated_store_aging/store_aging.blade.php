@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage store aging configurations</p>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- General Stock Colaboration -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">General Stock Colaboration</h3>
                <button onclick="openAddModal()" 
                        id="add_btn"
                        class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors flex items-center gap-2">
                    <i class="cft-standard-stroke cft-add"></i>
                    Data Baru
                </button>
            </div>
            <div class="p-4 md:p-5">
                <!-- Search Input -->
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="cft-standard-stroke cft-search text-gray-400"></i>
                        </div>
                        <input type="search" 
                               id="data_search"
                               class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Cari setup store aging...">
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="Datatb">
                        <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                            <tr>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Nama</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Aging</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Store</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cross Order Online Aging -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Cross Order Online Aging</h3>
                <button onclick="openOCAModal()" 
                        class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors flex items-center gap-2">
                    <i class="cft-standard-stroke cft-add"></i>
                    Data Baru
                </button>
            </div>
            <div class="p-4 md:p-5">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="OCAtb">
                        <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                            <tr>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Store</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Aging</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Action</th>
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

<!-- General Stock Colaboration Modal -->
<div id="DataModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeDataModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_data">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                
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
                        <label for="sa_name" class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="sa_name" 
                               name="sa_name" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                    <div>
                        <label for="sa_age" class="block text-sm font-medium text-gray-700 mb-2">Aging <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="sa_age" 
                               name="sa_age" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
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

<!-- Store Detail Modal -->
<div id="DataDetailModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeDataDetailModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">Detail Store</h3>
                <button type="button" 
                        onclick="closeDataDetailModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-md font-medium text-gray-900">Store List</h4>
                    <button onclick="openDataStoreModal()" 
                            class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors flex items-center gap-2">
                        <i class="cft-standard-stroke cft-add"></i>
                        Data Baru
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="DataDetailtb">
                        <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                            <tr>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Store</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Tampilkan Stok Disini ?</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeDataDetailModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Store Detail Modal -->
<div id="DataStoreModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeDataStoreModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_store">
                @csrf
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Store</h3>
                    <button type="button" 
                            onclick="closeDataStoreModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <div>
                        <label for="store_st_id" class="block text-sm font-medium text-gray-700 mb-2">Store <span class="text-red-500">*</span></label>
                        <select id="store_st_id" 
                                name="st_id"
                                required
                                class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value=''>- Pilih Store -</option>
                            @foreach ($data['st_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeDataStoreModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cross Order Online Aging Modal -->
<div id="OCAModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeOCAModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_oca">
                @csrf
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Cross Order Online Aging</h3>
                    <button type="button" 
                            onclick="closeOCAModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 md:p-5 space-y-4">
                    <div>
                        <label for="oca_st_id" class="block text-sm font-medium text-gray-700 mb-2">Store <span class="text-red-500">*</span></label>
                        <select id="oca_st_id" 
                                name="st_id"
                                required
                                class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value=''>Pilih Store</option>
                            @foreach ($data['st_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="oca_age" class="block text-sm font-medium text-gray-700 mb-2">Aging <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="oca_age" 
                               name="oca_age" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeOCAModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" 
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
let sa_id = '';
let storeAgingTable;
let storeAgingDataTable;
let storeAgingDetailTable;
let storeAgingDetailDataTable;
let ocaTable;
let ocaDataTable;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadStoreAgingData();
    loadOCAData();

    // Search handler
    $('#data_search').on('keyup', function() {
        if (storeAgingDataTable) {
            storeAgingDataTable.search(this.value);
        }
    });

    // Form submit handler - General Stock Colaboration
    $('#f_data').on('submit', function(e) {
        e.preventDefault();
        $("#save_btn").html('Proses ..');
        $("#save_btn").attr("disabled", true);
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('sta_save') }}",
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
                    loadStoreAgingData();
                } else if (data.status == '400') {
                    closeDataModal();
                    toast('Gagal', 'Data tidak tersimpan', 'error');
                }
            },
            error: function(data) {
                toast('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
                $("#save_btn").html('Simpan');
                $("#save_btn").attr("disabled", false);
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
                        id: $('#_id').val()
                    },
                    dataType: 'json',
                    url: "{{ url('sta_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toast('Berhasil', 'Data berhasil dihapus', 'success');
                            closeDataModal();
                            loadStoreAgingData();
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

    // Store detail form submit
    $('#f_store').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('sa_id', sa_id);
        $.ajax({
            type: 'POST',
            url: "{{ url('stas_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                closeDataStoreModal();
                if (data.status == '200') {
                    $('#f_store')[0].reset();
                    loadStoreAgingData();
                    loadStoreAgingDetailData();
                    toast('Berhasil', 'Data berhasil disimpan', 'success');
                } else if (data.status == '400') {
                    toast('Gagal', 'Data tidak tersimpan', 'error');
                }
            },
            error: function() {
                toast('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
            }
        });
    });

    // OCA form submit
    $('#f_oca').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('oca_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                closeOCAModal();
                $('#f_oca')[0].reset();
                if (data.status == '200') {
                    loadOCAData();
                    toast('Berhasil', 'Data berhasil disimpan', 'success');
                } else {
                    toast('Gagal', 'Data tidak tersimpan', 'error');
                }
            },
            error: function(data) {
                swal('Error', data, 'error');
            }
        });
    });

    // Delete detail handler
    $(document).on('click', '#delete_detail_btn', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            data: {
                id: $(this).attr('data-id')
            },
            dataType: 'json',
            url: "{{ url('stas_delete') }}",
            success: function(r) {
                if (r.status == '200') {
                    loadStoreAgingData();
                    loadStoreAgingDetailData();
                    toast('Berhasil', 'Data berhasil dihapus', 'success');
                } else {
                    toast('Gagal', 'Gagal hapus data', 'error');
                }
            },
            error: function() {
                toast('Error', 'Terjadi kesalahan saat menghapus data', 'error');
            }
        });
    });

    // Delete OCA handler
    $(document).on('click', '#delete_oca_btn', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            data: {
                id: $(this).attr('data-id')
            },
            dataType: 'json',
            url: "{{ url('oca_delete') }}",
            success: function(r) {
                if (r.status == '200') {
                    loadOCAData();
                    toast('Berhasil', 'Data berhasil dihapus', 'success');
                } else {
                    toast('Gagal', 'Gagal hapus data', 'error');
                }
            },
            error: function() {
                toast('Error', 'Terjadi kesalahan saat menghapus data', 'error');
            }
        });
    });

    // Check handlers
    $(document).on('click', '#y_check', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            data: {
                id: $(this).attr('data-id'),
                type: 1
            },
            dataType: 'json',
            url: "{{ url('sta_checked') }}",
            success: function(r) {
                if (r.status == '200') {
                    loadStoreAgingDetailData();
                    toast('Berhasil', 'Data berhasil diupdate', 'success');
                } else {
                    toast('Gagal', 'Gagal update data', 'error');
                }
            }
        });
    });

    $(document).on('click', '#n_check', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            data: {
                id: $(this).attr('data-id'),
                type: 0
            },
            dataType: 'json',
            url: "{{ url('sta_checked') }}",
            success: function(r) {
                if (r.status == '200') {
                    loadStoreAgingDetailData();
                    toast('Berhasil', 'Data berhasil diupdate', 'success');
                } else {
                    toast('Gagal', 'Gagal update data', 'error');
                }
            }
        });
    });
});

function loadStoreAgingData() {
    $.ajax({
        url: "{{ url('sta_datatables') }}",
        type: 'GET',
        data: {
            search: $('#data_search').val(),
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (storeAgingDataTable) {
                storeAgingDataTable.destroy();
            }
            
            // Clear table body
            $('#Datatb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50 cursor-pointer" data-id="' + row.id + '" data-sa_name="' + escapeHtml(row.sa_name || '') + '" data-sa_age="' + escapeHtml(row.sa_age || '') + '">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' + escapeHtml(row.sa_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.sa_age || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.store || '') + '</td>';
                    html += '</tr>';
                    $('#Datatb tbody').append(html);
                });
            } else {
                $('#Datatb tbody').append('<tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("Datatb") && typeof simpleDatatables !== 'undefined') {
                storeAgingDataTable = new simpleDatatables.DataTable("#Datatb", {
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
                        var sa_name = $row.data('sa_name');
                        var sa_age = $row.data('sa_age');
                        
                        if (id) {
                            $('#sa_name').val(sa_name);
                            $('#sa_age').val(sa_age);
                            $('#_id').val(id);
                            $('#_mode').val('edit');
                            $('#delete_btn').show();
                            openDataModal();
                        }
                    });
                    
                    // Attach click handler for store detail button
                    $(document).off('click', '#store_detail_btn').on('click', '#store_detail_btn', function(e) {
                        e.preventDefault();
                        sa_id = $(this).attr('data-id');
                        openDataDetailModal();
                        loadStoreAgingDetailData();
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

function loadStoreAgingDetailData() {
    $.ajax({
        url: "{{ url('sta_detail_datatables') }}",
        type: 'GET',
        data: {
            sa_id: sa_id,
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (storeAgingDetailDataTable) {
                storeAgingDetailDataTable.destroy();
            }
            
            // Clear table body
            $('#DataDetailtb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.st_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.show || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.action || '') + '</td>';
                    html += '</tr>';
                    $('#DataDetailtb tbody').append(html);
                });
            } else {
                $('#DataDetailtb tbody').append('<tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("DataDetailtb") && typeof simpleDatatables !== 'undefined') {
                storeAgingDetailDataTable = new simpleDatatables.DataTable("#DataDetailtb", {
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
            console.error('Error loading detail data:', xhr);
            toast('Error', 'Gagal memuat data detail', 'error');
        }
    });
}

function loadOCAData() {
    $.ajax({
        url: "{{ url('oca_datatables') }}",
        type: 'GET',
        data: {
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (ocaDataTable) {
                ocaDataTable.destroy();
            }
            
            // Clear table body
            $('#OCAtb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.st_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.oca_age || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.action || '') + '</td>';
                    html += '</tr>';
                    $('#OCAtb tbody').append(html);
                });
            } else {
                $('#OCAtb tbody').append('<tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("OCAtb") && typeof simpleDatatables !== 'undefined') {
                ocaDataTable = new simpleDatatables.DataTable("#OCAtb", {
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
            console.error('Error loading OCA data:', xhr);
            toast('Error', 'Gagal memuat data OCA', 'error');
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

function openAddModal() {
    $('#_id').val('');
    $('#_mode').val('add');
    $('#f_data')[0].reset();
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

function openDataDetailModal() {
    document.getElementById('DataDetailModal').classList.remove('hidden');
    document.getElementById('DataDetailModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeDataDetailModal() {
    document.getElementById('DataDetailModal').classList.add('hidden');
    document.getElementById('DataDetailModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function openDataStoreModal() {
    document.getElementById('DataStoreModal').classList.remove('hidden');
    document.getElementById('DataStoreModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeDataStoreModal() {
    document.getElementById('DataStoreModal').classList.add('hidden');
    document.getElementById('DataStoreModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function openOCAModal() {
    document.getElementById('OCAModal').classList.remove('hidden');
    document.getElementById('OCAModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeOCAModal() {
    document.getElementById('OCAModal').classList.add('hidden');
    document.getElementById('OCAModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}
</script>
@endpush
