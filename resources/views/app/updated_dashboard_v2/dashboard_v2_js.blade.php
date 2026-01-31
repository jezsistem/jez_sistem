<script>
var storeCurrentPage = 1;
var brandCurrentPage = 1;
var perPage = 25;

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

function getSummary(date) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {date: date},
        url: "{{ url('dashboard_v2_summary') }}",
        success: function(r) {
            if (r.status == '200') {
                $('#nett_sales_label').text(r.nett_sales);
                $('#profit_label').text(r.profit);
                $('#purchase_label').text(r.purchase);
                $('#assets_label').text(r.assets);
                $('#consign_assets_label').text(r.consign_assets);
                $('#debt_label').text(r.debt);
            } else {
                Swal.fire('Failed', 'Failed', 'error');
            }
        }
    });
    return false;
}

function loadStoreData(page = 1) {
    storeCurrentPage = page;
    var search = $('#store_search').val();
    var dashboard_date = $('#dashboard_date').val();
    
    if (!dashboard_date) {
        $('#store_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-gray-500">Silakan pilih tanggal untuk memuat data</td></tr>');
        return;
    }
    
    $.ajax({
        url: "{{ url('store_info_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            dashboard_date: dashboard_date
        },
        success: function(response) {
            if (response.error) {
                $('#store_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#store_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="9" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.created_at_x));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.sales));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.profit));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.purchase));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.cc_asset));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.con_asset));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.debt));
                    tbody.append(tr);
                });
            }

            createCustomPagination('#StoreTable', response.total, response.current_page, response.total_pages, response.per_page, loadStoreData, 'store');
        },
        error: function() {
            $('#store_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function loadBrandData(page = 1) {
    brandCurrentPage = page;
    var search = $('#brand_search').val();
    var dashboard_date = $('#dashboard_date').val();
    
    if (!dashboard_date) {
        $('#brand_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-gray-500">Silakan pilih tanggal untuk memuat data</td></tr>');
        return;
    }
    
    $.ajax({
        url: "{{ url('brand_info_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            dashboard_date: dashboard_date
        },
        success: function(response) {
            if (response.error) {
                $('#brand_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#brand_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="9" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.created_at_x));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.br_name));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.sales));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.profit));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.purchase));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.cc_asset));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.con_asset));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.debt));
                    tbody.append(tr);
                });
            }

            createCustomPagination('#BrandTable', response.total, response.current_page, response.total_pages, response.per_page, loadBrandData, 'brand');
        },
        error: function() {
            $('#brand_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction, prefix) {
    var infoId = prefix + '_pagination_info';
    var controlsId = prefix + '_pagination_controls';
    
    var info = $('#' + infoId);
    var controls = $('#' + controlsId);
    
    info.empty();
    controls.empty();
    
    var start = (currentPage - 1) * perPage + 1;
    var end = Math.min(currentPage * perPage, totalRecords);
    info.text('Menampilkan ' + start + ' sampai ' + end + ' dari ' + totalRecords + ' data');
    
    if (totalPages <= 1) return;
    
    var prevBtn = $('<button>')
        .addClass('px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100')
        .text('Sebelumnya')
        .prop('disabled', currentPage === 1)
        .on('click', function() {
            if (currentPage > 1) {
                loadFunction(currentPage - 1);
            }
        });
    
    controls.append(prevBtn);
    
    var maxVisible = 5;
    var startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    var endPage = Math.min(totalPages, startPage + maxVisible - 1);
    
    if (startPage > 1) {
        controls.append($('<button>')
            .addClass('px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-100')
            .text('1')
            .on('click', function() { loadFunction(1); }));
        if (startPage > 2) {
            controls.append($('<span>').addClass('px-3 py-1 text-sm text-gray-500').text('...'));
        }
    }
    
    for (var i = startPage; i <= endPage; i++) {
        var pageBtn = $('<button>')
            .addClass('px-3 py-1 text-sm font-medium border border-gray-300 hover:bg-gray-100')
            .text(i);
        
        if (i === currentPage) {
            pageBtn.addClass('text-white bg-blue-600');
        } else {
            pageBtn.addClass('text-gray-500 bg-white');
        }
        
        pageBtn.on('click', function() {
            loadFunction(parseInt($(this).text()));
        });
        
        controls.append(pageBtn);
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            controls.append($('<span>').addClass('px-3 py-1 text-sm text-gray-500').text('...'));
        }
        controls.append($('<button>')
            .addClass('px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-100')
            .text(totalPages)
            .on('click', function() { loadFunction(totalPages); }));
    }
    
    var nextBtn = $('<button>')
        .addClass('px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100')
        .text('Selanjutnya')
        .prop('disabled', currentPage === totalPages)
        .on('click', function() {
            if (currentPage < totalPages) {
                loadFunction(currentPage + 1);
            }
        });
    
    controls.append(nextBtn);
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Search handlers with debounce
    var storeSearchTimeout;
    $('#store_search').on('keyup', function() {
        clearTimeout(storeSearchTimeout);
        storeSearchTimeout = setTimeout(function() {
            loadStoreData(1);
        }, 500);
    });

    var brandSearchTimeout;
    $('#brand_search').on('keyup', function() {
        clearTimeout(brandSearchTimeout);
        brandSearchTimeout = setTimeout(function() {
            loadBrandData(1);
        }, 500);
    });

    // Export handlers
    $('#store_excel').on('click', function() {
        jQuery("#StoreTable").table2excel({
            filename: "Laporan Store",
        });
    });

    $('#brand_excel').on('click', function() {
        jQuery("#BrandTable").table2excel({
            filename: "Laporan Brand",
        });
    });

    // Synchronize button
    $('#synchronize_btn').on('click', function() {
        Swal.fire({
            title: "Synchronize new data..?",
            text: "the data will be updated",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then(function(isConfirm) {
            if (isConfirm.value) {
                $('#WaitingModal').removeClass('hidden');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "GET",
                    dataType: 'json',
                    url: "{{ url('auto/9999/close_data') }}",
                    success: function(r) {
                        $('#WaitingModal').addClass('hidden');
                        if (r.status == '200') {
                            Swal.fire("Success", "Success", "success");
                            loadStoreData(1);
                            loadBrandData(1);
                            getSummary($('#dashboard_date').val());
                        } else {
                            Swal.fire('Failed', 'Failed', 'error');
                        }
                    },
                    error: function() {
                        $('#WaitingModal').addClass('hidden');
                        Swal.fire('Error', 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    });

    // Show notification modal
    $('#NotifModal').removeClass('hidden');
    setTimeout(function() {
        $('#NotifModal').addClass('hidden');
    }, 6000);

    // Initialize date range picker after scripts are loaded
    // Scripts are loaded in order: sweetalert2 -> _partials.js (jQuery) -> moment -> daterangepicker -> this script
    // So we need to wait a bit for all dependencies to load
    setTimeout(function() {
        var checkCount = 0;
        var maxChecks = 100; // 10 seconds max (100 * 100ms)
        var checkInterval = setInterval(function() {
            checkCount++;
            var $jq = window.jQuery || window.$;
            if (typeof moment !== 'undefined' && $jq && typeof $jq.fn.daterangepicker === 'function') {
                clearInterval(checkInterval);
                initDateRangePicker();
            } else if (checkCount >= maxChecks) {
                clearInterval(checkInterval);
                console.error('Failed to load daterangepicker dependencies after 10 seconds');
            }
        }, 100);
    }, 500); // Wait 500ms for scripts to start loading
});

// Date range picker - initialize after scripts are loaded
function initDateRangePicker() {
    var $jq = window.jQuery || window.$;
    if (!$jq) {
        console.warn('jQuery not available');
        return;
    }
    
    var picker = $jq('#kt_dashboard_daterangepicker');
    if (picker.length == 0) {
        console.warn('Date range picker element not found');
        return;
    }
    
    if (typeof moment === 'undefined') {
        console.warn('moment.js not loaded');
        return;
    }
    
    if (typeof $jq.fn.daterangepicker === 'undefined') {
        console.warn('daterangepicker not loaded');
        return;
    }

    // Remove any existing daterangepicker instance
    if (picker.data('daterangepicker')) {
        picker.data('daterangepicker').remove();
    }

    var start = moment();
    var end = moment();

    function cb(start, end, label) {
        var title = '';
        var range = '';
        var hidden_range = '';

        if ((end - start) < 100 || label == 'Hari Ini' || label == '') {
            title = 'Hari Ini:';
            range = start.format('DD MMM YYYY');
            hidden_range = start.format('YYYY-MM-DD');
        } else if (label == 'Kemarin') {
            title = 'Kemarin:';
            range = start.format('DD MMM YYYY');
            hidden_range = start.format('YYYY-MM-DD');
        } else {
            range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
            hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
        }
        $jq('#dashboard_date').val(hidden_range);
        $jq('#kt_dashboard_daterangepicker_date').html(range);
        $jq('#kt_dashboard_daterangepicker_title').html(title);
        loadStoreData(1);
        loadBrandData(1);
        getSummary(hidden_range);
    }

    try {
        picker.daterangepicker({
            startDate: start,
            endDate: end,
            opens: 'center',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            singleDatePicker: true,
            ranges: {
                'Hari Ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]
            }
        }, cb);

        // Initialize with today's date
        cb(start, end, '');
        console.log('Date range picker initialized successfully');
    } catch (e) {
        console.error('Error initializing daterangepicker:', e);
    }
}
</script>
