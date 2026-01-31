<script>
var st_id = {{ Auth::user()->st_id }};
var pl_id = [];
var stockDataTable = null;
var stockDataArray = [];
var ccn_number = '';
var currentPage = 1;
var rowsPerPage = 50; // Show 50 rows per page
var filteredData = []; // For search results

function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
}

function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

// Manual pagination functions - MUST be defined before loadStockData
function renderTable() {
    var start = (currentPage - 1) * rowsPerPage;
    var end = start + rowsPerPage;
    var pageData = filteredData.slice(start, end);
    
    var html = '';
    pageData.forEach(function(row, index) {
        var purchase = row.purchase || '0';
        var sell = row.sell || '0';
        html += '<tr class="hover:bg-gray-50">';
        html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">' + (start + index + 1) + '</td>';
        html += '<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">' + escapeHtml(row.pl_code || '') + '</td>';
        html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.br_name || '') + '</td>';
        html += '<td class="px-4 py-3 text-sm text-gray-500">' + escapeHtml(row.p_name || '') + '</td>';
        html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.p_color || '') + '</td>';
        html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.sz_name || '') + '</td>';
        html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.psc_name || '') + '</td>';
        html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.pls_qty || '0') + '</td>';
        html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + addCommas(purchase) + '</td>';
        html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + addCommas(sell) + '</td>';
        html += '</tr>';
    });
    
    if (html === '') {
        html = '<tr><td colspan="10" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>';
    }
    
    $('#Stocktb tbody').html(html);
}

function renderPagination() {
    var totalPages = Math.ceil(filteredData.length / rowsPerPage);
    var html = '';
    
    if (totalPages > 1) {
        html += '<div class="text-sm text-gray-700">';
        html += 'Menampilkan ' + ((currentPage - 1) * rowsPerPage + 1) + ' sampai ' + Math.min(currentPage * rowsPerPage, filteredData.length) + ' dari ' + filteredData.length + ' data';
        html += '</div>';
        
        html += '<div class="flex gap-2">';
        
        // Previous button
        html += '<button onclick="changePage(' + (currentPage - 1) + ')" ' + (currentPage === 1 ? 'disabled' : '') + ' class="px-3 py-1 text-sm border rounded ' + (currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-gray-50') + '">Previous</button>';
        
        // Page numbers
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);
        
        for (var i = startPage; i <= endPage; i++) {
            html += '<button onclick="changePage(' + i + ')" class="px-3 py-1 text-sm border rounded ' + (i === currentPage ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-50') + '">' + i + '</button>';
        }
        
        // Next button
        html += '<button onclick="changePage(' + (currentPage + 1) + ')" ' + (currentPage === totalPages ? 'disabled' : '') + ' class="px-3 py-1 text-sm border rounded ' + (currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-gray-50') + '">Next</button>';
        
        html += '</div>';
    }
    
    $('#pagination-container').html(html);
}

function changePage(page) {
    var totalPages = Math.ceil(filteredData.length / rowsPerPage);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderTable();
    renderPagination();
}

function loadLocation(st_id) {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        type: 'POST',
        url: "{{ url('load_mass_adjustment_location') }}",
        data: {
            st_id: st_id,
            pl_id: pl_id
        },
        dataType: 'json',
        success: function(response) {
            if (response.data && Object.keys(response.data).length > 0) {
                $('#bin_filter').empty();
                $('#bin_filter').append('<option value="">- Pilih Lokasi -</option>');
                Object.entries(response.data).forEach(function([key, value]) {
                    $('#bin_filter').append('<option value="' + key + '">' + value + '</option>');
                });
            } else {
                $('#bin_filter').empty();
                $('#bin_filter').append('<option value="">- Tidak Ada Lokasi Tersedia -</option>');
            }
        },
        error: function() {
            $('#bin_filter').empty();
            $('#bin_filter').append('<option value="">- Gagal Memuat Lokasi -</option>');
        }
    });
}

function create_cycleCounts_header(st_id, pl_id) {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const time = now.getTime();
    const randomText = Math.random().toString(36).substring(2, 6).toUpperCase();
    const ccn_number_generated = `CCN${year}${month}${day}${time}${randomText}`;

    $.ajax({
        type: 'POST',
        url: "{{ url('cycle_counts_insert') }}",
        data: {
            st_id: st_id,
            pl_id: pl_id,
            ccn_number: ccn_number_generated
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
                success: function(res) {
                    if (res.status == '200') {
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Header Cycle Count berhasil dibuat', 'Success');
                        } else {
                            swal('Success', 'Header Cycle Count berhasil dibuat', 'success');
                        }
                ccn_number = res.data.ccn_number;
                $('#ccn_number').val(res.data.ccn_number);
            } else {
                swal('Gagal', res.message || 'Gagal membuat header', 'error');
            }
        },
        error: function(xhr) {
            console.log(xhr.responseText);
            swal('Error', 'Terjadi kesalahan saat membuat cycle count header', 'error');
        }
    });
}

function loadStockData() {
    console.log('loadStockData called - START');
    
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    
    $.ajax({
        url: "{{ url('cycle_count_stock_datatables') }}",
        type: 'GET',
        data: {
            search: $('#stock_search').val(),
            st_id: st_id,
            pl_id: pl_id,
            length: -1
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('AJAX beforeSend - page should still be clickable');
        },
        success: function(response) {
            console.log('AJAX success - received data', response.data ? response.data.length : 0, 'rows');
            
            try {
                // Destroy existing DataTable properly
                if (stockDataTable) {
                    stockDataTable.destroy();
                    stockDataTable = null;
                }
                
                stockDataArray = response.data || [];
                $('#Stocktb tbody').empty();
                
                if (response.data && response.data.length > 0) {
                    console.log('Total data:', response.data.length, 'rows - using manual pagination');
                    
                    filteredData = response.data;
                    currentPage = 1;
                    renderTable();
                    renderPagination();
                } else {
                    $('#Stocktb tbody').html('<tr><td colspan="10" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
                    $('#pagination-container').html('');
                }
                
                console.log('Table populated - loadStockData COMPLETE');
                
            } catch(error) {
                console.error('Error in success callback:', error);
            }
        },
        error: function(xhr) {
            console.error('Error loading data:', xhr);
        },
        complete: function() {
            console.log('AJAX complete - page should be clickable now');
        }
    });
}

// Simple cleanup function
function cleanupOverlays() {
    console.log('cleanupOverlays called');
    
    // Only hide specific known elements
    $('#loader, #loader_download').addClass('hidden').hide();
    $('#ImportModal').addClass('hidden').hide();
    
    /* DISABLED CODE:
    // Close Select2 dropdowns
    if ($('#bin_filter').length && $('#bin_filter').hasClass('select2-hidden-accessible')) {
        try {
            $('#bin_filter').select2('close');
        } catch(e) {}
    }
    
    // Remove Select2 backdrop if exists
    $('.select2-dropdown, .select2-container--open').each(function() {
        if (!$(this).is(':visible')) {
            $(this).remove();
        }
    });
    
    // Hide loaders
    $('#loader, #loader_download').addClass('hidden').hide().css({
        'display': 'none',
        'visibility': 'hidden',
        'pointer-events': 'none',
        'opacity': '0',
        'z-index': '-1'
    });
    
    // Close modals
    $('#ImportModal').addClass('hidden').attr('aria-hidden', 'true').css({
        'display': 'none',
        'visibility': 'hidden',
        'pointer-events': 'none',
        'opacity': '0',
        'z-index': '-1'
    });
    
    // Remove modal backdrops
    $('.modal-backdrop, .fixed.inset-0').not('#loader, #loader_download').each(function() {
        if ($(this).hasClass('bg-gray-900') || $(this).hasClass('bg-opacity-50')) {
            $(this).remove();
        }
    });
    
    // Clean body styles
    document.body.classList.remove('overflow-hidden', 'modal-open');
    document.body.style.overflow = '';
    if (document.body.style.pointerEvents === 'none') {
        document.body.style.pointerEvents = '';
    }
    
    // Remove any invisible overlays
    $('[style*="pointer-events: none"]').filter(function() {
        return $(this).css('position') === 'fixed' && $(this).css('z-index') > 1000;
    }).each(function() {
        if (!$(this).is(':visible') || $(this).hasClass('hidden')) {
            $(this).css('pointer-events', 'none').css('z-index', '-1');
        }
    });
    */
}

function exportTable() {
    cleanupOverlays();
    
    // Trigger download
    window.location.href = "{{ url('export_mass_adjustment_template') }}?st_id=" + st_id + "&psc_id=all&br_id=all&pl_id=&qty_filter=1";
}

// Debug function to check for blocking elements - make it globally accessible
window.debugBlockingElements = function() {
    var blockingElements = [];
    $('*').each(function() {
        var $el = $(this);
        var zIndex = parseInt($el.css('z-index')) || 0;
        var pos = $el.css('position');
        var pointerEvents = $el.css('pointer-events');
        var display = $el.css('display');
        var visibility = $el.css('visibility');
        var width = $el.outerWidth();
        var height = $el.outerHeight();
        var winWidth = $(window).width();
        var winHeight = $(window).height();
        
        // Check for large fixed/absolute elements that might block
        if ((pos === 'fixed' || pos === 'absolute') && zIndex > 500) {
            if (width >= winWidth * 0.8 && height >= winHeight * 0.8) {
                blockingElements.push({
                    element: $el[0],
                    id: $el.attr('id'),
                    class: $el.attr('class'),
                    zIndex: zIndex,
                    pointerEvents: pointerEvents,
                    display: display,
                    visibility: visibility
                });
            }
        }
    });
    
    if (blockingElements.length > 0) {
        console.warn('Found potential blocking elements:', blockingElements);
    }
    
    return blockingElements;
};

$(document).ready(function() {
    console.log('Document ready - initializing cycle counts page');
    
    cleanupOverlays();
    
    // Load location data
    loadLocation(st_id);
    
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    
    // No need for aggressive interval cleanup anymore since backdrop issue is fixed
    console.log('Page initialized successfully');

    // Initialize Select2 for bin_filter - wait a bit to ensure DOM is ready
    setTimeout(function() {
        if ($('#bin_filter').length) {
            if (typeof $.fn.select2 === 'undefined') {
                console.error('Select2 is not loaded');
                return;
            }
            
            // Destroy existing select2 if any
            if ($('#bin_filter').hasClass('select2-hidden-accessible')) {
                $('#bin_filter').select2('destroy');
            }
            
            // Ensure element is visible and clickable
            $('#bin_filter').css({
                'pointer-events': 'auto',
                'cursor': 'pointer'
            });
            
            // Initialize Select2
            try {
                $('#bin_filter').select2({
                    dropdownParent: $('#bin_filter_parent'),
                    allowClear: false,
                    width: '100%'
                });
                
                $('#bin_filter').on('select2:open', function(e) {
                    const evt = "scroll.select2";
                    $(e.target).parents().off(evt);
                    $(window).off(evt);
                });
                
                // Select2 auto-close on outside click
                $(document).off('click.cycleCountSelect2Close').on('click.cycleCountSelect2Close', function(e) {
                    if ($('#bin_filter').hasClass('select2-hidden-accessible') && 
                        $('.select2-container--open').length > 0 &&
                        !$(e.target).closest('#bin_filter_parent, .select2-dropdown, .select2-container').length) {
                        $('#bin_filter').select2('close');
                    }
                });
                
                console.log('Select2 initialized for bin_filter');
            } catch (e) {
                console.error('Error initializing Select2:', e);
            }
        } else {
            console.error('bin_filter element not found');
        }
    }, 100);

    // Bin filter change
    $('#bin_filter').on('change', function() {
        var bin_id = $(this).val();
        // Create cycle count header when bin is selected and header doesn't exist
        if (bin_id && !ccn_number) {
            create_cycleCounts_header(st_id, bin_id ? [bin_id] : []);
        }
        loadStockData();
    });

    // Scan SKU on Enter key
    $(document).on('keypress', '#scan_sku', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            let bin = $('#bin_filter').val();
            let sku = $(this).val();

            if (!sku) {
                swal('Peringatan', 'Silakan masukkan atau scan SKU', 'warning');
                return;
            }

            if (!bin) {
                swal('Peringatan', 'Silakan pilih bin terlebih dahulu', 'warning');
                return;
            }

            if (!ccn_number) {
                swal('Peringatan', 'Cycle count header belum dibuat. Silakan pilih bin terlebih dahulu.', 'warning');
                return;
            }

            $.ajax({
                type: "GET",
                data: {
                    pls_id: bin,
                    sku: sku
                },
                dataType: 'json',
                url: "{{ url('scan_get_item_details') }}",
                success: function(r) {
                    if (r.status == '200') {
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Berhasil mengambil data item', 'Success');
                        } else {
                            swal('Success', 'Berhasil mengambil data item', 'success');
                        }
                        $('#p_name').val(r.data.p_name);
                        $('#sz_name').val(r.data.sz_name);
                        $('#pl_code').val(r.data.pl_code);
                        $('#pls_qty').val(r.data.pls_qty);
                        $('#scan_sku').val('');

                        // Store to cycle_count_details
                        $.ajax({
                            type: "POST",
                            url: "{{ url('cycle_count_detail_store') }}",
                            data: {
                                ccn_number: ccn_number,
                                pls_id: bin,
                                sku: sku
                            },
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                if (res.status == '200') {
                                    if (typeof toastr !== 'undefined') {
                                        toastr.success('Item berhasil disimpan ke cycle_count_details', 'Success');
                                    } else {
                                        swal('Success', 'Item berhasil disimpan ke cycle_count_details', 'success');
                                    }
                                    $('#scan_sku').val('').focus();
                                    loadStockData();
                                } else {
                                    swal('Gagal', res.message || 'Gagal simpan item', 'error');
                                }
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                swal('Error', 'Terjadi kesalahan saat simpan data', 'error');
                            }
                        });
                    } else {
                        swal('Gagal', 'Item tidak ditemukan', 'error');
                    }
                },
                error: function(xhr) {
                    swal('Error', 'Terjadi kesalahan saat mengambil data item', 'error');
                }
            });
        }
    });

    // Reset button
    $('#reset_btn').on('click', function(e) {
        e.preventDefault();
        $('#bin_filter').val('').trigger('change');
        $('#scan_sku').val('');
        $('#p_name').val('');
        $('#sz_name').val('');
        $('#pl_code').val('');
        $('#pls_qty').val('');
        ccn_number = '';
        $('#ccn_number').val('');
    });

    // Export button
    $('#export_btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Ensure no modals are open
        $('#ImportModal').addClass('hidden').attr('aria-hidden', 'true');
        $('#ImportModal').css({
            'display': 'none',
            'visibility': 'hidden',
            'pointer-events': 'none',
            'opacity': '0',
            'z-index': '-1'
        });
        
        // Ensure body is not blocked
        document.body.classList.remove('overflow-hidden');
        document.body.style.overflow = '';
        document.body.style.pointerEvents = '';
        
        // Hide any loaders with force
        $('#loader').addClass('hidden').hide().css({
            'display': 'none',
            'visibility': 'hidden',
            'pointer-events': 'none',
            'opacity': '0'
        });
        $('#loader_download').addClass('hidden').hide().css({
            'display': 'none',
            'visibility': 'hidden',
            'pointer-events': 'none',
            'opacity': '0'
        });
        
        // Small delay to ensure cleanup before redirect
        setTimeout(function() {
            exportTable();
        }, 50);
    });

    // Search handler with pagination
    var searchTimeout;
    $('#stock_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        var query = $(this).val().toLowerCase();
        
        searchTimeout = setTimeout(function() {
            if (query === '') {
                filteredData = stockDataArray;
            } else {
                filteredData = stockDataArray.filter(function(row) {
                    return (row.pl_code || '').toLowerCase().indexOf(query) > -1 ||
                           (row.br_name || '').toLowerCase().indexOf(query) > -1 ||
                           (row.p_name || '').toLowerCase().indexOf(query) > -1 ||
                           (row.p_color || '').toLowerCase().indexOf(query) > -1 ||
                           (row.sz_name || '').toLowerCase().indexOf(query) > -1 ||
                           (row.psc_name || '').toLowerCase().indexOf(query) > -1;
                });
            }
            currentPage = 1;
            renderTable();
            renderPagination();
        }, 300);
    });

    // Import modal
    $(document).delegate('#import_btn', 'click', function(e) {
        e.preventDefault();
        openImportModal();
    });

    // Import form submit
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        if (st_id == 'all') {
            swal('Tentukan Store', 'Silahkan tentukan store terlebih dahulu', 'warning');
            return false;
        }
        
        var formData = new FormData(this);
        formData.append('st_id', st_id);
        formData.append('psc_id', 'all');
        formData.append('br_id', 'all');
        formData.append('pl_id', '');
        formData.append('qty_filter', '1');

        $("#import_data_btn").html('Proses ..');
        $("#import_data_btn").attr("disabled", true);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('import_mass_adjustment_template') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(r) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                if (r.status == '200') {
                    loadStockData();
                    closeImportModal();
                    $('#f_import')[0].reset();
                    swal('Berhasil', 'Adjustment berhasil dicreate', 'success');
                } else if (r.status == '500') {
                    if (r.invalid_skus && r.invalid_skus.length > 0) {
                        Swal.fire({
                            title: 'Perhatian!',
                            html: `
                                <div style="overflow-x:auto;">
                                    <table class="table" style="width:100%; text-align:left; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th style="border: 1px solid #ccc; padding: 8px;">SKU / ID</th>
                                                <th style="border: 1px solid #ccc; padding: 8px;">Qty Export</th>
                                                <th style="border: 1px solid #ccc; padding: 8px;">Qty Sistem</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sku-table-body">
                                        </tbody>
                                    </table>
                                    <br/>
                                    <div style="text-align: center;">
                                        <button id="export_excel" class="swal2-confirm swal2-styled" style="background-color:#28a745; margin-right:10px;">Export ke Excel</button>
                                        <button id="close_alert" class="swal2-cancel swal2-styled" style="background-color:#dc3545;">Tutup</button>
                                    </div>
                                </div>
                            `,
                            icon: 'info',
                            showConfirmButton: false,
                            didOpen: () => {
                                let tbody = document.getElementById('sku-table-body');
                                r.invalid_skus.forEach(function(item) {
                                    let row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td style="border: 1px solid #ccc; padding: 8px;">${item.sku}</td>
                                        <td style="border: 1px solid #ccc; padding: 8px;">${item.qty_export}</td>
                                        <td style="border: 1px solid #ccc; padding: 8px;">${item.qty_system}</td>
                                    `;
                                    tbody.appendChild(row);
                                });

                                document.getElementById('export_excel').addEventListener('click', function() {
                                    let wb = XLSX.utils.book_new();
                                    let ws_data = [
                                        ["SKU / ID", "Qty Export", "Qty Sistem"],
                                        ...r.invalid_skus.map(item => [item.sku, item.qty_export, item.qty_system])
                                    ];
                                    let ws = XLSX.utils.aoa_to_sheet(ws_data);
                                    XLSX.utils.book_append_sheet(wb, ws, "Invalid SKUs");
                                    XLSX.writeFile(wb, "Invalid_SKUs.xlsx");
                                });

                                document.getElementById('close_alert').addEventListener('click', function() {
                                    Swal.close();
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Perhatian!',
                            text: 'Tidak ada SKU yang tidak valid.',
                            icon: 'info',
                            confirmButtonText: 'Oke'
                        });
                    }
                } else {
                    swal('Gagal', 'Adjustment gagal dicreate', 'warning');
                }
            },
            error: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                swal('Error', data, 'error');
            }
        });
    });

    // Load initial stock data
    loadStockData();
});
</script>
