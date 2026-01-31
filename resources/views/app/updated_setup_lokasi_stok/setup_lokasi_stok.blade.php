@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage product location setup</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Store</label>
                <select id="st_id_filter" 
                        name="st_id_filter"
                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">- Store -</option>
                    @foreach ($data['st_id'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bin</label>
                <select id="bin_kl_filter" 
                        name="bin_kl_filter"
                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Bin --</option>
                    <option value="-KL">Bin Koli</option>
                    <option value="-KONTAINER">Bin Kontainer</option>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="cft-standard-stroke cft-search text-gray-400"></i>
                </div>
                <input type="search" 
                       id="product_location_setup_search"
                       class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Cari kode bin / artikel...">
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <!-- Export Button -->
                <div class="relative">
                    <button type="button" 
                            onclick="toggleExportDropdown('export-dropdown-pls')"
                            class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2">
                        <i class="cft-standard-stroke cft-download"></i>
                        Export
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div id="export-dropdown-pls" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                        <div class="py-2">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Bentuk File:</div>
                            <a href="#" 
                               id="product_location_setup_excel_btn"
                               onclick="exportProductLocationSetupExcel(); return false;"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                <i class="fas fa-file-excel text-green-600"></i>
                                Excel
                            </a>
                            <a href="#" 
                               id="pl_export"
                               onclick="exportAllProductLocationSetup(); return false;"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                <i class="fas fa-file-excel text-green-600"></i>
                                Export All
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="ProductLocationSetuptb">
                <input type="hidden" id="_pl_id" name="_pl_id"/>
                <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                    <tr>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Store</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center" style="white-space: nowrap;">Lokasi</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Produk</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Kapasitas</th>
                        <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">%</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded by DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Product Location Setup Modal -->
<div id="ProductLocationSetupModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeProductLocationModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-7xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">
                    BIN <span id="stock_location_label" class="text-blue-600"></span>
                </h3>
                <button type="button" 
                        onclick="closeProductLocationModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="relative">
                        <button type="button" 
                                onclick="toggleExportDropdown('export-dropdown-product-in-location')"
                                class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2">
                            <i class="cft-standard-stroke cft-download"></i>
                            Export
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div id="export-dropdown-product-in-location" class="hidden absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                            <div class="py-2">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Bentuk File:</div>
                                <a href="#" 
                                   id="product_in_location_excel_btn"
                                   onclick="exportProductInLocationExcel(); return false;"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                    <i class="fas fa-file-excel text-green-600"></i>
                                    Excel
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="w-full max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="cft-standard-stroke cft-search text-gray-400"></i>
                            </div>
                            <input type="search" 
                                   id="product_in_location_search"
                                   class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Cari artikel / warna / size / brand...">
                        </div>
                    </div>
                </div>
                <div id="product_location_detail" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="ProductInLocationtb">
                        <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                            <tr>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200">Artikel</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200">Warna</th>
                                <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200">Size | Qty | Barcode</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Klik baris untuk melihat detail produk
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-200">
                <button type="button" 
                        onclick="closeProductLocationModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Simple-datatables CSS -->
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.2.0/dist/style.css" rel="stylesheet" />
<style>
    /* Button styles for table buttons (from server HTML) */
    #ProductLocationSetuptb .btn,
    #ProductInLocationtb .btn {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        vertical-align: middle;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 0.25rem;
        transition: all 0.15s ease-in-out;
    }
    #ProductLocationSetuptb .btn-sm,
    #ProductInLocationtb .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    #ProductLocationSetuptb .btn-primary,
    #ProductInLocationtb .btn-primary {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: #fff;
    }
    #ProductLocationSetuptb .btn-primary:hover,
    #ProductInLocationtb .btn-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }
    #ProductLocationSetuptb .btn-info,
    #ProductInLocationtb .btn-info {
        background-color: #06b6d4;
        border-color: #06b6d4;
        color: #fff;
    }
    #ProductLocationSetuptb .btn-info:hover,
    #ProductInLocationtb .btn-info:hover {
        background-color: #0891b2;
        border-color: #0891b2;
        color: #fff;
    }
    #ProductLocationSetuptb .btn-success,
    #ProductInLocationtb .btn-success {
        background-color: #10b981;
        border-color: #10b981;
        color: #fff;
    }
    #ProductLocationSetuptb .btn-success:hover,
    #ProductInLocationtb .btn-success:hover {
        background-color: #059669;
        border-color: #059669;
        color: #fff;
    }
    #ProductLocationSetuptb .btn-warning,
    #ProductInLocationtb .btn-warning {
        background-color: #f59e0b;
        border-color: #f59e0b;
        color: #fff;
    }
    #ProductLocationSetuptb .btn-warning:hover,
    #ProductInLocationtb .btn-warning:hover {
        background-color: #d97706;
        border-color: #d97706;
        color: #fff;
    }
    #ProductLocationSetuptb .btn-light,
    #ProductInLocationtb .btn-light {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #374151;
    }
    #ProductLocationSetuptb .btn-light:hover,
    #ProductInLocationtb .btn-light:hover {
        background-color: #e5e7eb;
        border-color: #9ca3af;
        color: #111827;
    }
    #ProductLocationSetuptb .col-12,
    #ProductInLocationtb .col-12 {
        width: 100%;
    }
    #ProductLocationSetuptb .col-7,
    #ProductInLocationtb .col-7 {
        width: 58.333333%;
    }
</style>
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
let productLocationSetupDataTable;
let productLocationSetupDataArray = [];
let productInLocationTable;
let productInLocationDataArray = [];
let productInLocationSearchTimeout;

// Toast function wrapper
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
        alert(title + ': ' + message);
    }
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Bind row click handler immediately (will work with event delegation)
    bindRowClickHandler();

    // Load initial data
    loadProductLocationSetupData();

    // Search handler
    $('#product_location_setup_search').on('keyup', function() {
        loadProductLocationSetupData();
    });

    // Filter handlers
    $('#st_id_filter').on('change', function() {
        loadProductLocationSetupData();
    });

    $('#bin_kl_filter').on('change', function() {
        loadProductLocationSetupData();
    });

    // Product in location search (modal)
    $('#product_in_location_search').on('keyup', function() {
        clearTimeout(productInLocationSearchTimeout);
        var searchValue = $(this).val();
        productInLocationSearchTimeout = setTimeout(function() {
            var plId = $('#_pl_id').val();
            if (plId) {
                loadProductInLocationData(plId, searchValue);
            }
        }, 300);
    });
});

function toggleExportDropdown(dropdownId) {
    var dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleExportDropdown"]') &&
        !event.target.closest('#export-dropdown-pls') &&
        !event.target.closest('#export-dropdown-product-in-location')) {
        var dropdownPls = document.getElementById('export-dropdown-pls');
        if (dropdownPls) {
            dropdownPls.classList.add('hidden');
        }
        var dropdownProduct = document.getElementById('export-dropdown-product-in-location');
        if (dropdownProduct) {
            dropdownProduct.classList.add('hidden');
        }
    }
});

// Global function to handle row click (called from onclick attribute)
// Make it available on window object to ensure it's accessible
window.handleRowClick = function(pl_id, event) {
    console.log('=== handleRowClick called with pl_id:', pl_id, '===');
    console.log('Event:', event || window.event);
    
    if (!pl_id) {
        console.warn('No pl_id provided');
        return false;
    }
    
    // Get event from parameter or window.event
    var e = event || window.event || null;
    
    // Check if clicked element is a button/link with its own action
    if (e && e.target) {
        var $target = $(e.target).closest('a, button');
        if ($target.length > 0) {
            // Only ignore if the button/link has its own action
            var hasHref = $target.attr('href') && $target.attr('href') !== '#' && $target.attr('href') !== '';
            var hasOnclick = $target.attr('onclick') && $target.attr('onclick') !== '';
            
            if (hasHref || hasOnclick) {
                console.log('Click on button/link with own action, ignoring');
                return false;
            }
            // Otherwise, continue to open modal (buttons are just for display)
            console.log('Click on display button, will open modal');
        }
    }
    
    $('#_pl_id').val(pl_id);
    
    // Get pl_location_plain from data array
    var rowData = productLocationSetupDataArray.find(function(r) {
        return r.pl_id == pl_id || r.pl_id == String(pl_id) || String(r.pl_id) == String(pl_id);
    });
    var pl_location_plain = rowData ? rowData.pl_location_plain : '';
    
    console.log('Found rowData:', rowData);
    console.log('Opening modal with pl_id:', pl_id, 'pl_location_plain:', pl_location_plain);
    
    // Open modal
    openProductLocationModal(pl_id, pl_location_plain);
    
    // Prevent default and stop propagation
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    return false;
};

function bindRowClickHandler() {
    // Also bind jQuery handler as backup
    $(document).off('click', '#ProductLocationSetuptb tbody tr');
    
    $(document).on('click', '#ProductLocationSetuptb tbody tr', function(e) {
        // Prevent double handling if onclick already handled it
        if (e.isDefaultPrevented()) {
            return;
        }
        
        console.log('=== jQuery row click event triggered ===');
        console.log('Clicked element:', e.target);
        
        // Check if clicked element is a button/link with its own action (href or onclick)
        var $clickedElement = $(e.target).closest('a, button');
        if ($clickedElement.length > 0) {
            // Only ignore if the button/link has its own action
            var hasHref = $clickedElement.attr('href') && $clickedElement.attr('href') !== '#' && $clickedElement.attr('href') !== '';
            var hasOnclick = $clickedElement.attr('onclick') && $clickedElement.attr('onclick') !== '';
            
            if (hasHref || hasOnclick) {
                console.log('Click on button/link with own action, ignoring');
                return true; // Let the button's own action handle it
            }
            // Otherwise, continue to open modal (buttons are just for display)
            console.log('Click on display button, will open modal');
        }
        
        // Get the row element (might be clicked element or its parent)
        var $row = $(this);
        // If clicked element is not the row itself, find the parent row
        if (!$row.is('tr')) {
            $row = $(e.target).closest('tr');
        }
        
        // Try multiple ways to get pl_id
        var pl_id = $row.attr('data-pl_id') || $row.data('pl_id');
        
        // If still not found, try to get from clicked element or its parent cell
        if (!pl_id) {
            var $clickedCell = $(e.target).closest('td');
            if ($clickedCell.length > 0) {
                pl_id = $clickedCell.attr('data-pl_id') || $clickedCell.data('pl_id');
            }
        }
        
        // If still not found, try to get from the row's HTML
        if (!pl_id && $row[0]) {
            var rowHtml = $row[0].outerHTML;
            var match = rowHtml.match(/data-pl_id=["']?(\d+)["']?/);
            if (match) {
                pl_id = match[1];
            }
        }
        
        // If still not found, try to get from onclick attribute
        if (!pl_id) {
            var onclickAttr = $row.attr('onclick');
            if (onclickAttr) {
                var onclickMatch = onclickAttr.match(/handleRowClick\((\d+)/);
                if (onclickMatch) {
                    pl_id = onclickMatch[1];
                }
            }
        }
        
        // If still not found, try to get from the first cell or stored data
        if (!pl_id && productLocationSetupDataArray && productLocationSetupDataArray.length > 0) {
            // Try to match row by content (store name)
            var rowIndex = $row.index();
            if (rowIndex >= 0 && rowIndex < productLocationSetupDataArray.length) {
                pl_id = productLocationSetupDataArray[rowIndex].pl_id;
            }
        }
        
        console.log('Row clicked, pl_id:', pl_id);
        console.log('Row element:', $row[0]);
        console.log('Row index:', $row.index());
        console.log('Clicked element:', e.target);
        console.log('Row data attributes:', $row[0] ? Array.from($row[0].attributes).map(attr => attr.name + '=' + attr.value).join(', ') : 'no element');
        
        if (pl_id) {
            handleRowClick(pl_id, e);
        } else {
            console.error('Could not find pl_id in row');
            console.error('Available data array:', productLocationSetupDataArray);
        }
        
        return false;
    });
    
    console.log('Row click handler bound (both onclick and jQuery)');
}

function loadProductLocationSetupData() {
    $.ajax({
        url: "{{ url('product_location_setup_datatables') }}",
        type: 'GET',
        data: {
            search: $('#product_location_setup_search').val(),
            st_id: $('#st_id_filter').val(),
            bin_kl_filter: $('#bin_kl_filter').val(),
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (productLocationSetupDataTable) {
                productLocationSetupDataTable.destroy();
            }
            
            // Store data array for later use (including pl_location_plain)
            productLocationSetupDataArray = response.data || [];
            
            // Clear table body
            $('#ProductLocationSetuptb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var pl_id = row.pl_id || '';
                    // Ensure pl_id is a string for attribute
                    var pl_id_str = String(pl_id);
                    // Add onclick directly to row and ensure data-pl_id is set
                    // Also add data-pl_id to each cell as backup
                    var html = '<tr class="hover:bg-gray-50 cursor-pointer" data-pl_id="' + pl_id_str + '" onclick="return handleRowClick(' + pl_id_str + ', event);">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center" data-pl_id="' + pl_id_str + '">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-pl_id="' + pl_id_str + '">' + escapeHtml(row.st_name || '') + '</td>';
                    // pl_location - button dengan pl_code (HTML dari server, jangan di-escape)
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm" style="white-space: nowrap;" data-pl_id="' + pl_id_str + '">' + (row.pl_location || '') + '</td>';
                    // pl_product - button dengan jumlah produk (HTML dari server, jangan di-escape)
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm" data-pl_id="' + pl_id_str + '">' + (row.pl_product || '') + '</td>';
                    // pl_capacity - button dengan format total/capacity (HTML dari server, jangan di-escape)
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-center" data-pl_id="' + pl_id_str + '">' + (row.pl_capacity || '') + '</td>';
                    // pl_percent - button dengan persentase (HTML dari server, jangan di-escape)
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-center" data-pl_id="' + pl_id_str + '">' + (row.pl_percent || '') + '</td>';
                    html += '</tr>';
                    $('#ProductLocationSetuptb tbody').append(html);
                });
            } else {
                $('#ProductLocationSetuptb tbody').append('<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("ProductLocationSetuptb") && typeof simpleDatatables !== 'undefined') {
                productLocationSetupDataTable = new simpleDatatables.DataTable("#ProductLocationSetuptb", {
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
                
                // Re-bind onclick handlers after datatable renders (simple-datatables may recreate DOM)
                setTimeout(function() {
                    $('#ProductLocationSetuptb tbody tr').each(function() {
                        var $row = $(this);
                        // Try to get pl_id from multiple sources
                        var pl_id = $row.attr('data-pl_id') || $row.data('pl_id');
                        
                        // If not found, try to get from onclick attribute
                        if (!pl_id) {
                            var onclickAttr = $row.attr('onclick');
                            if (onclickAttr) {
                                var match = onclickAttr.match(/handleRowClick\((\d+)/);
                                if (match) {
                                    pl_id = match[1];
                                }
                            }
                        }
                        
                        // If still not found, try to get from row HTML
                        if (!pl_id) {
                            var rowHtml = $row[0] ? $row[0].outerHTML : '';
                            var match = rowHtml.match(/data-pl_id=["']?(\d+)["']?/);
                            if (match) {
                                pl_id = match[1];
                            }
                        }
                        
                        if (pl_id) {
                            // Ensure data-pl_id attribute is set
                            $row.attr('data-pl_id', pl_id);
                            // Remove existing onclick and add new one
                            $row.removeAttr('onclick');
                            $row.attr('onclick', 'return handleRowClick(' + pl_id + ', event);');
                            // Also add click handler via jQuery as backup
                            $row.off('click').on('click', function(e) {
                                // Check if clicked element has its own action
                                var $clickedElement = $(e.target).closest('a, button');
                                if ($clickedElement.length > 0) {
                                    var hasHref = $clickedElement.attr('href') && $clickedElement.attr('href') !== '#' && $clickedElement.attr('href') !== '';
                                    var hasOnclick = $clickedElement.attr('onclick') && $clickedElement.attr('onclick') !== '';
                                    if (hasHref || hasOnclick) {
                                        return true; // Let button's own action handle it
                                    }
                                }
                                // Otherwise open modal
                                handleRowClick(pl_id, e);
                            });
                        } else {
                            console.warn('Could not find pl_id for row:', $row[0]);
                        }
                    });
                    console.log('Re-bound onclick handlers for', $('#ProductLocationSetuptb tbody tr').length, 'rows');
                }, 300);
            }
        },
        error: function(xhr) {
            console.error('Error loading data:', xhr);
            toast('Error', 'Gagal memuat data', 'error');
        }
    });
}

function exportProductLocationSetupExcel() {
    var data = productLocationSetupDataArray || [];
    
    if (data.length === 0) {
        toast('Info', 'Tidak ada data untuk diekspor', 'warning');
        return;
    }
    
    // Create CSV content
    var csvContent = "No,Store,Lokasi,Produk,Kapasitas,%\n";
    data.forEach(function(row, index) {
        csvContent += (index + 1) + ",";
        csvContent += '"' + (row.st_name || '').replace(/"/g, '""') + '",';
        // Extract text from HTML if needed
        var locationText = (row.pl_location || '').replace(/<[^>]*>/g, '').replace(/"/g, '""');
        csvContent += '"' + locationText + '",';
        csvContent += '"' + (row.pl_product || '').replace(/"/g, '""') + '",';
        csvContent += (row.pl_capacity || '0') + ",";
        csvContent += (row.pl_percent || '0') + "\n";
    });
    
    // Create blob and download
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    var url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'product_location_setup_' + new Date().getTime() + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportAllProductLocationSetup() {
    window.location.href = "{{ url('pl_export') }}";
}

function openProductLocationModal(pl_id, pl_location_plain) {
    console.log('Opening modal for pl_id:', pl_id);
    
    // Set pl_id and location label
    $('#_pl_id').val(pl_id);
    if (pl_location_plain) {
        // Extract text from HTML if it's HTML
        var locationText = pl_location_plain.replace(/<[^>]*>/g, '');
        $('#stock_location_label').html(locationText);
    } else {
        // Get from data array if not provided
        var rowData = productLocationSetupDataArray.find(function(r) {
            return r.pl_id == pl_id;
        });
        if (rowData && rowData.pl_location_plain) {
            var locationText = rowData.pl_location_plain.replace(/<[^>]*>/g, '');
            $('#stock_location_label').html(locationText);
        } else if (rowData && rowData.pl_location) {
            var locationText = rowData.pl_location.replace(/<[^>]*>/g, '');
            $('#stock_location_label').html(locationText);
        }
    }
    
    // Reset search input
    $('#product_in_location_search').val('');
    
    // Open modal first (same method as other V2 pages)
    var modal = document.getElementById('ProductLocationSetupModal');
    if (!modal) {
        console.error('Modal element not found');
        toast('Error', 'Modal tidak ditemukan', 'error');
        return;
    }
    
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
    
    // Load product in location data (same source as legacy modal)
    loadProductInLocationData(pl_id, '');
}

function closeProductLocationModal() {
    var modal = document.getElementById('ProductLocationSetupModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    }
}

function loadProductInLocationData(pl_id, search) {
    if (!pl_id) {
        return;
    }

    var tbody = document.querySelector('#ProductInLocationtb tbody');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Memuat data...</td></tr>';
    }

    $.ajax({
        type: "GET",
        data: {
            _pl_id: pl_id,
            search: search || '',
            // Use a high limit to emulate legacy "Semua" option
            length: 10000,
            start: 0,
            draw: 1
        },
        dataType: 'json',
        url: "{{ url('product_in_location_datatables') }}",
        success: function(response) {
            var rows = response && response.data ? response.data : [];
            renderProductInLocationTable(rows);
        },
        error: function(xhr, status, error) {
            console.error('Error loading product location data:', xhr);
            console.error('Status:', status);
            console.error('Error:', error);
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-sm text-red-500">Gagal memuat detail produk. Silakan coba lagi.</td></tr>';
            }
            toast('Error', 'Gagal memuat detail produk: ' + error, 'error');
        }
    });
}

function renderProductInLocationTable(rows) {
    productInLocationDataArray = rows || [];
    var tbody = document.querySelector('#ProductInLocationtb tbody');
    if (!tbody) {
        return;
    }

    if (productInLocationDataArray.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data produk di lokasi ini</td></tr>';
    } else {
        tbody.innerHTML = '';
        productInLocationDataArray.forEach(function(row, index) {
            var tr = document.createElement('tr');
            tr.innerHTML = '<td class="px-6 py-3 text-sm text-gray-900 text-center">' + (index + 1) + '</td>' +
                '<td class="px-6 py-3 text-sm text-gray-900">' + (row.p_name || '') + '</td>' +
                '<td class="px-6 py-3 text-sm text-gray-900">' + (row.p_color || '') + '</td>' +
                '<td class="px-6 py-3 text-sm text-gray-900">' + (row.p_size || '') + '</td>';
            tbody.appendChild(tr);
        });
    }

    if (productInLocationTable) {
        productInLocationTable.destroy();
    }
    productInLocationTable = new simpleDatatables.DataTable('#ProductInLocationtb', {
        searchable: false,
        perPage: 10,
        perPageSelect: [10, 25, 50, 100],
        labels: {
            perPage: '',
            noRows: 'Tidak ada data',
            info: 'Menampilkan {start} sampai {end} dari {rows} data'
        }
    });
}

function exportProductInLocationExcel() {
    var data = productInLocationDataArray || [];
    if (data.length === 0) {
        toast('Info', 'Tidak ada data untuk diekspor', 'warning');
        return;
    }

    var csvContent = "No,Artikel,Warna,Size | Qty | Barcode\n";
    data.forEach(function(row, index) {
        var nameText = (row.p_name || '').replace(/<[^>]*>/g, '').replace(/"/g, '""');
        var colorText = (row.p_color || '').replace(/<[^>]*>/g, '').replace(/"/g, '""');
        var sizeText = (row.p_size || '').replace(/<[^>]*>/g, '').replace(/"/g, '""');
        csvContent += (index + 1) + ",";
        csvContent += '"' + nameText + '",';
        csvContent += '"' + colorText + '",';
        csvContent += '"' + sizeText + "\"\n";
    });

    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    var url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'product_in_location_' + new Date().getTime() + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function mutation() {
    toast('Info', 'Fitur nonaktif, gunakan setup lokasi stok versi 2', 'warning');
    return false;
}

function escapeHtml(text) {
    if (!text) return '';
    // Handle HTML content from server
    if (typeof text === 'string' && text.includes('<')) {
        return text; // Return as-is if it's already HTML
    }
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
