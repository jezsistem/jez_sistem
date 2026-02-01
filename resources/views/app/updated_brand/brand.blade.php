@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage brand information</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Export Dropdown -->
            <div class="relative">
                <button type="button" 
                        onclick="toggleExportMenu()"
                        class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
                    <i class="cft-standard-stroke cft-download"></i>
                    Export
                </button>
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div class="py-1">
                        <a href="#" id="brand_excel_btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="cft-standard-stroke cft-excel mr-2"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
            </div>
            @if ($data['segment'] != 'web_brand')
            <!-- Import Button -->
            <button onclick="openImportModal()" 
                    class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-upload"></i>
                Import
            </button>
            <!-- Add Button -->
            <button onclick="openAddModal()" 
                    class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-add"></i>
                Data Baru
            </button>
            @endif
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="cft-standard-stroke cft-search text-gray-400"></i>
            </div>
            <input type="search" 
                   id="brand_search"
                   class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                   placeholder="Cari nama brand, deskripsi...">
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden p-5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="Brandtb">
                <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                    <tr>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Logo</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Banner</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Nama Brand</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Slug</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Deskripsi</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Brand Lokal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded by DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($data['segment'] != 'web_brand')
<!-- Add/Edit Modal -->
<div id="BrandModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_brand" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                <input type="hidden" name="_image" id="_image" value="" />
                <input type="hidden" name="_banner" id="_banner" value="" />
                
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
                        <label for="br_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Brand <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="br_name" 
                               name="br_name" 
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="br_slug" class="block text-sm font-medium text-gray-700 mb-2">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="br_slug" 
                               name="br_slug" 
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="br_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <input type="text" 
                               id="br_description" 
                               name="br_description"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="br_image" class="block text-sm font-medium text-gray-700 mb-2">
                            Logo Brand
                        </label>
                        <input type="file" 
                               id="br_image" 
                               name="br_image" 
                               accept="image/*"
                               onchange="loadFile(event)"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="mt-2 text-center">
                            <img id="imagePreview" class="max-w-xs mx-auto" style="display:none; max-width: 200px; padding-top: 10px;"/>
                            <button type="button" id="delete_logo_btn" onclick="deleteLogo()" class="mt-2 px-3 py-1 text-sm text-red-500 hover:text-red-700" style="display:none;">
                                Hapus Logo
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="br_banner" class="block text-sm font-medium text-gray-700 mb-2">
                            Banner
                        </label>
                        <input type="file" 
                               id="br_banner" 
                               name="br_banner" 
                               accept="image/*"
                               onchange="loadBanner(event)"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="mt-2 text-center">
                            <img id="bannerPreview" class="max-w-xs mx-auto" style="display:none; max-width: 300px; padding-top: 10px;"/>
                            <button type="button" id="delete_banner_btn" onclick="deleteBanner()" class="mt-2 px-3 py-1 text-sm text-red-500 hover:text-red-700" style="display:none;">
                                Hapus Banner
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="is_local" class="block text-sm font-medium text-gray-700 mb-2">
                            Brand Lokal ? <span class="text-red-500">*</span>
                        </label>
                        <select id="is_local" 
                                name="is_local" 
                                required
                                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">- Pilih -</option>
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    @if ($data['segment'] != 'web_brand')
                    <button type="button" 
                            id="delete_brand_btn" 
                            onclick="deleteBrand()"
                            style="display:none;"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors">
                        Hapus
                    </button>
                    @endif
                    <button type="submit" 
                            id="save_brand_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600 transition-colors">
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
                           class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600 transition-colors">
                            Download
                        </a>
                    </div>
                    <div>
                        <label for="br_template" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih template yang sudah di download dan diisi <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                               id="br_template" 
                               name="br_template" 
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
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600 transition-colors">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script>
let brand_dataTable;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load data and initialize simple-datatables
    loadBrandData();

    $('#brand_search').on('keyup', function() {
        loadBrandData();
    });

    // Auto-generate slug from brand name
    $('#br_name').on('keyup', function() {
        var name = $(this).val();
        if (name && $('#_mode').val() == 'add') {
            var slug = name.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            $('#br_slug').val(slug);
        }
    });

    @if ($data['segment'] != 'web_brand')
    // Check duplicate brand name
    $('#br_name').on('change', function() {
        var br_name = $(this).val();
        if (!br_name) return;
        
        $.ajax({
            type: "POST",
            data: {
                _br_name: br_name
            },
            dataType: 'json',
            url: "{{ url('check_exists_brand') }}",
            success: function(r) {
                if (r.status == '200') {
                    showToast('Brand sudah ada di sistem, silahkan ganti dengan yang lain', 'warning');
                    $('#br_name').val('');
                    $('#br_slug').val('');
                }
            }
        });
    });

    // Form submission
    $('#f_brand').on('submit', function(e) {
        e.preventDefault();
        $("#save_brand_btn").html('Proses...');
        $("#save_brand_btn").prop("disabled", true);
        
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('br_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_brand_btn").html('Simpan');
                $("#save_brand_btn").prop("disabled", false);
                if (data.status == '200') {
                    closeModal();
                    loadBrandData();
                    showToast('Data berhasil disimpan', 'success');
                } else if (data.status == '400') {
                    closeModal();
                    showToast('Data tidak tersimpan', 'error');
                }
            },
            error: function(xhr) {
                $("#save_brand_btn").html('Simpan');
                $("#save_brand_btn").prop("disabled", false);
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
            url: "{{ url('br_import') }}",
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
                    loadBrandData();
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
    @endif
});

function loadBrandData() {
    $.ajax({
        url: "{{ url('brand_datatables') }}",
        type: 'GET',
        data: {
            search: $('#brand_search').val(),
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (brand_dataTable) {
                brand_dataTable.destroy();
            }
            
            // Clear table body
            $('#Brandtb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var imageHtml = '';
                    if (row.br_image && row.br_image != '') {
                        imageHtml = '<a href="' + "{{ asset('api/brand') }}/" + row.br_image + '" target="_blank"><img src="' + "{{ asset('api/brand/thumbs') }}/" + row.br_image + '" class="w-16 h-16 object-cover rounded" /></a>';
                    } else {
                        imageHtml = '<img src="' + "{{ asset('upload/image/no_image.png') }}" + '" class="w-16 h-16 object-cover rounded" />';
                    }
                    
                    var bannerHtml = '';
                    if (row.br_banner && row.br_banner != '') {
                        bannerHtml = '<a href="' + "{{ asset('api/brand/banner') }}/" + row.br_banner + '" target="_blank"><img src="' + "{{ asset('api/brand/banner') }}/" + row.br_banner + '" class="w-24 h-16 object-cover rounded" /></a>';
                    } else {
                        bannerHtml = '<img src="' + "{{ asset('upload/image/no_image.png') }}" + '" class="w-24 h-16 object-cover rounded" />';
                    }
                    
                    var isLocalShow = (row.is_local == '1') ? 'Ya' : 'Tidak';
                    
                    var html = '<tr class="hover:bg-gray-50 cursor-pointer">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + imageHtml + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + bannerHtml + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" data-id="' + row.id + '" data-name="' + escapeHtml(row.br_name || '') + '" data-slug="' + escapeHtml(row.br_slug || '') + '" data-description="' + escapeHtml(row.br_description || '') + '" data-image="' + (row.br_image || '') + '" data-banner="' + (row.br_banner || '') + '" data-is_local="' + (row.is_local || '0') + '">' + escapeHtml(row.br_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.br_slug || '') + '</td>';
                    html += '<td class="px-6 py-4 text-sm text-gray-500">' + escapeHtml(row.br_description || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + isLocalShow + '</td>';
                    html += '</tr>';
                    $('#Brandtb tbody').append(html);
                });
            } else {
                $('#Brandtb tbody').append('<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("Brandtb") && typeof simpleDatatables !== 'undefined') {
                brand_dataTable = new simpleDatatables.DataTable("#Brandtb", {
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
                
                @if ($data['segment'] != 'web_brand')
                // Handle row click after datatable is initialized
                setTimeout(function() {
                    $('#Brandtb tbody').off('click').on('click', 'tr', function() {
                        var $row = $(this);
                        var id = $row.find('td[data-id]').data('id');
                        var name = $row.find('td[data-id]').data('name');
                        var slug = $row.find('td[data-id]').data('slug');
                        var description = $row.find('td[data-id]').data('description');
                        var image = $row.find('td[data-id]').data('image');
                        var banner = $row.find('td[data-id]').data('banner');
                        var is_local = $row.find('td[data-id]').data('is_local');
                        
                        if (id) {
                            openEditModal(id, name, slug, description, image, banner, is_local);
                        }
                    });
                }, 100);
                @endif
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

@if ($data['segment'] != 'web_brand')
// Modal functions
function openAddModal() {
    $('#_id').val('');
    $('#_mode').val('add');
    $('#f_brand')[0].reset();
    $('#imagePreview').hide();
    $('#bannerPreview').hide();
    $('#delete_logo_btn').hide();
    $('#delete_banner_btn').hide();
    $('#delete_brand_btn').hide();
    $('#modalTitle').text('Tambah {{ $data["subtitle"] }}');
    openModal();
}

function openEditModal(id, name, slug, description, image, banner, is_local) {
    $('#br_name').val(name);
    $('#br_slug').val(slug);
    $('#br_description').val(description || '');
    $('#is_local').val(is_local || '0');
    $('#_id').val(id);
    $('#_mode').val('edit');
    $('#_image').val(image || '');
    $('#_banner').val(banner || '');
    
    if (image && image != '') {
        $('#imagePreview').attr('src', "{{ asset('api/brand/thumbs') }}/" + image).show();
        $('#delete_logo_btn').show();
    } else {
        $('#imagePreview').hide();
        $('#delete_logo_btn').hide();
    }
    
    if (banner && banner != '') {
        $('#bannerPreview').attr('src', "{{ asset('api/brand/banner') }}/" + banner).show();
        $('#delete_banner_btn').show();
    } else {
        $('#bannerPreview').hide();
        $('#delete_banner_btn').hide();
    }
    
    $('#delete_brand_btn').show();
    $('#modalTitle').text('Edit {{ $data["subtitle"] }}');
    openModal();
}

function loadFile(event) {
    var reader = new FileReader();
    reader.onload = function(){
        $('#imagePreview').attr('src', reader.result).show();
    };
    reader.readAsDataURL(event.target.files[0]);
}

function loadBanner(event) {
    var reader = new FileReader();
    reader.onload = function(){
        $('#bannerPreview').attr('src', reader.result).show();
    };
    reader.readAsDataURL(event.target.files[0]);
}

function deleteLogo() {
    if (confirm('Yakin hapus logo?')) {
        $.ajax({
            type: "POST",
            data: {
                _id: $('#_id').val(),
                _image: $('#_image').val()
            },
            dataType: 'json',
            url: "{{ url('delete_logo_brand') }}",
            success: function(r) {
                if (r.status == '200') {
                    $('#imagePreview').hide();
                    $('#delete_logo_btn').hide();
                    $('#br_image').val('');
                    $('#_image').val('');
                    showToast('Logo berhasil dihapus', 'success');
                } else {
                    showToast('Logo gagal dihapus', 'error');
                }
            },
            error: function() {
                showToast('Logo gagal dihapus', 'error');
            }
        });
    }
}

function deleteBanner() {
    if (confirm('Yakin hapus banner?')) {
        $.ajax({
            type: "POST",
            data: {
                _id: $('#_id').val(),
                _banner: $('#_banner').val()
            },
            dataType: 'json',
            url: "{{ url('delete_banner_brand') }}",
            success: function(r) {
                if (r.status == '200') {
                    $('#bannerPreview').hide();
                    $('#delete_banner_btn').hide();
                    $('#br_banner').val('');
                    $('#_banner').val('');
                    showToast('Banner berhasil dihapus', 'success');
                } else {
                    showToast('Banner gagal dihapus', 'error');
                }
            },
            error: function() {
                showToast('Banner gagal dihapus', 'error');
            }
        });
    }
}

function openModal() {
    const modal = document.getElementById('BrandModal');
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    $('body').addClass('overflow-hidden');
}

function closeModal() {
    const modal = document.getElementById('BrandModal');
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
function deleteBrand() {
    if (!confirm('Yakin hapus data ini?')) {
        return;
    }
    
    $.ajax({
        type: "POST",
        data: {
            _id: $('#_id').val(),
            _item: $('#br_name').val()
        },
        dataType: 'json',
        url: "{{ url('br_delete') }}",
        success: function(r) {
            if (r.status == '200') {
                showToast('Data berhasil dihapus', 'success');
                closeModal();
                loadBrandData();
            } else {
                showToast('Data gagal dihapus', 'error');
            }
        },
        error: function() {
            showToast('Data gagal dihapus', 'error');
        }
    });
}
@endif

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
