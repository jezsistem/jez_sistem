<script>
var poReceiveCurrentPage = 1;
var poCurrentPage = 1;
var perPage = 25;
var report_date = '';

function loadPoReceiveData(page = 1) {
    poReceiveCurrentPage = page;
    var search = $('#po_receive_search').val();
    var status = $('#status_filter').val();
    var st_id = $('#st_id_filter').val();
    var date_filter = $('#date_filter').val();
    
    if (!report_date) {
        $('#po_receive_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-gray-500">Silakan pilih tanggal untuk memuat data</td></tr>');
        return;
    }
    
    $.ajax({
        url: "{{ url('po_receive_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            report_date: report_date,
            status: status,
            st_id: st_id,
            date_filter: date_filter
        },
        success: function(response) {
            if (response.error) {
                $('#po_receive_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#po_receive_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="9" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.po_invoice));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.po_created_show));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.poads_created_show));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').html('<span class="px-2 py-1 text-xs font-semibold rounded ' + row.poad_qty_class + '">' + row.poad_qty + '</span>'));
                    var qtyBtn = $('<span>').addClass('px-2 py-1 text-xs font-semibold rounded cursor-pointer poads_qty_btn ' + row.poads_qty_class)
                        .text(row.poads_qty)
                        .attr('data-po_invoice', row.po_invoice)
                        .attr('data-po_id', row.po_id);
                    tr.append($('<td>').addClass('px-3 py-4 text-right').append(qtyBtn));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.poad_total_price_show));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.poads_total_price_show));
                    tbody.append(tr);
                });
            }

            createCustomPagination('#PoReceiveTable', response.total, response.current_page, response.total_pages, response.per_page, loadPoReceiveData, 'po_receive');
        },
        error: function() {
            $('#po_receive_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function loadPoData(page = 1) {
    poCurrentPage = page;
    var po_id = $('#_po_id').val();
    var search = $('#po_search').val();
    
    if (!po_id) {
        $('#po_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">PO ID tidak ditemukan</td></tr>');
        return;
    }
    
    $.ajax({
        url: "{{ url('po_receive_detail_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            po_id: po_id,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#po_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#po_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.article));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').html('<span class="px-2 py-1 text-xs font-semibold rounded ' + row.poad_qty_class + '">' + row.poad_qty + '</span>'));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').html('<span class="px-2 py-1 text-xs font-semibold rounded ' + row.poads_qty_class + '">' + row.poads_qty + '</span>'));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.poad_total_show));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.poads_total_show));
                    tbody.append(tr);
                });
            }

            createCustomPagination('#PoTable', response.total, response.current_page, response.total_pages, response.per_page, loadPoData, 'po');
        },
        error: function() {
            $('#po_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
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
    var poReceiveSearchTimeout;
    $('#po_receive_search').on('keyup', function() {
        clearTimeout(poReceiveSearchTimeout);
        poReceiveSearchTimeout = setTimeout(function() {
            loadPoReceiveData(1);
        }, 500);
    });

    var poSearchTimeout;
    $('#po_search').on('keyup', function() {
        clearTimeout(poSearchTimeout);
        poSearchTimeout = setTimeout(function() {
            loadPoData(1);
        }, 500);
    });

    // Filter change handlers
    $('#st_id_filter, #status_filter, #date_filter').on('change', function() {
        loadPoReceiveData(1);
    });

    // Export button
    $('#export_btn').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Mohon Tunggu..').prop('disabled', true);
        var date = report_date;
        var date_filter = $('#date_filter').val();
        var status_filter = $('#status_filter').val();
        var st_id = $('#st_id_filter').val();
        window.location.href = "{{ url('po_receive_export') }}?&st_id="+st_id+"&date_filter="+date_filter+"&status_filter="+status_filter+"&date="+date+"";
        
        setTimeout(function() {
            btn.html('<i class="fas fa-download mr-2"></i>Export Data').prop('disabled', false);
        }, 2000);
    });

    // Close modal handlers
    $('#close_po_modal, #close_po_modal_btn').on('click', function() {
        $('#PoModal').addClass('hidden');
    });

    // PO quantity button handler (using event delegation for dynamically created elements)
    $(document).on('click', '.poads_qty_btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var po_id = $(this).attr('data-po_id');
        var po_invoice = $(this).attr('data-po_invoice');
        $('#_po_id').val(po_id);
        $('#po_invoice_label').text(po_invoice);
        $('#PoModal').removeClass('hidden');
        loadPoData(1);
    });

    // Initialize date range picker after scripts are loaded
    // Scripts are loaded in order: sweetalert2 -> moment -> daterangepicker -> _partials.js (jQuery) -> this script
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
        report_date = hidden_range;
        $jq('#report_date').val(hidden_range);
        $jq('#kt_dashboard_daterangepicker_date').html(range);
        $jq('#kt_dashboard_daterangepicker_title').html(title);
        loadPoReceiveData(1);
    }

    try {
        picker.daterangepicker({
            startDate: start,
            endDate: end,
            opens: 'center',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            ranges: {
                'Hari Ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
