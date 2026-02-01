<script>
var ratingCurrentPage = 1;
var ratingHistoryCurrentPage = 1;
var perPage = 25;

// ==================== RATING SUMMARY FUNCTIONS ====================

function loadRatingData(page = 1) {
    ratingCurrentPage = page;
    var search = $('#user_search').val();
    var rating_date = $('#user_rating_date').val();
    
    $.ajax({
        url: "{{ url('rating_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            rating_date: rating_date
        },
        success: function(response) {
            if (response.error) {
                $('#rating_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#rating_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.stt_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.rating_qty));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.rating_total));
                    tbody.append(tr);
                });
            }

            createCustomPagination('#RatingTable', response.total, response.current_page, response.total_pages, response.per_page, loadRatingData, 'rating');
        },
        error: function() {
            $('#rating_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

// ==================== RATING HISTORY FUNCTIONS ====================

function loadRatingHistoryData(page = 1) {
    ratingHistoryCurrentPage = page;
    var search = $('#user_history_search').val();
    var rating_date = $('#user_rating_date').val();
    
    $.ajax({
        url: "{{ url('rating_history_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            rating_date: rating_date
        },
        success: function(response) {
            if (response.error) {
                $('#rating_history_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#rating_history_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="9" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.stt_name));
                    
                    // Invoice button
                    var invoiceBtn = '';
                    if (row.pt_id) {
                        invoiceBtn = $('<button>').addClass('px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-red-600')
                            .text(row.pos_invoice)
                            .attr('data-pt_id', row.pt_id)
                            .addClass('sales_item_detail_btn');
                    } else {
                        invoiceBtn = $('<span>').text(row.pos_invoice);
                    }
                    tr.append($('<td>').addClass('px-3 py-4').append(invoiceBtn));
                    
                    tr.append($('<td>').addClass('px-3 py-4').text(row.cust_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ur_value));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ur_description));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ur_created));
                    tbody.append(tr);
                });
            }

            createCustomPagination('#RatingHistoryTable', response.total, response.current_page, response.total_pages, response.per_page, loadRatingHistoryData, 'rating_history');
        },
        error: function() {
            $('#rating_history_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

// ==================== SALES ITEM DETAIL FUNCTIONS ====================

function loadSalesItemDetailData() {
    var pt_id = $('#pt_id').val();
    if (!pt_id) return;
    
    $.ajax({
        url: "{{ url('sales_item_detail_datatables') }}",
        type: 'GET',
        data: {
            pt_id: pt_id,
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            var tbody = $('#sales_item_detail_tbody');
            tbody.empty();

            if (response.data && response.data.length > 0) {
                var no = 1;
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(no++));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.article || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_td_qty || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_td_total_price || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.created_at || '-'));
                    tbody.append(tr);
                });
            } else {
                tbody.html('<tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            }
        },
        error: function() {
            $('#sales_item_detail_tbody').html('<tr><td colspan="5" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function openSalesItemDetailModal(pt_id, invoice) {
    $('#pt_id').val(pt_id);
    $('#sales_item_detail_label').text(invoice);
    $('#SalesItemDetailModal').removeClass('hidden');
    loadSalesItemDetailData();
}

function closeSalesItemDetailModal() {
    $('#SalesItemDetailModal').addClass('hidden');
}

// ==================== PAGINATION ====================

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

// ==================== DATE RANGE PICKER ====================

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
    
    // Remove existing instance if any
    if (picker.data('daterangepicker')) {
        picker.data('daterangepicker').remove();
    }
    
    if (typeof moment === 'undefined') {
        console.warn('moment.js not available');
        return;
    }
    
    if (typeof $jq.fn.daterangepicker !== 'function') {
        console.warn('daterangepicker not available');
        return;
    }

    try {
        var start = moment();
        var end = moment();

        function cb(start, end, label) {
            var title = '';
            var range = '';
            var hidden_range = '';

            if ((end - start) < 100 || label == 'Hari Ini') {
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

            $jq('#user_rating_date').val(hidden_range);
            $jq('#kt_dashboard_daterangepicker_date').html(range);
            $jq('#kt_dashboard_daterangepicker_title').html(title);
            loadRatingData(1);
            loadRatingHistoryData(1);
        }

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
        
        cb(start, end, '');
    } catch (e) {
        console.error('Error initializing daterangepicker:', e);
    }
}

// ==================== MAIN DOCUMENT READY ====================

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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

    // Load initial data (will be triggered after date picker initializes)
    // loadRatingData(1);
    // loadRatingHistoryData(1);

    // Search with debounce
    var ratingSearchTimeout;
    $('#user_search').on('keyup', function() {
        clearTimeout(ratingSearchTimeout);
        ratingSearchTimeout = setTimeout(function() {
            loadRatingData(1);
        }, 500);
    });

    var historySearchTimeout;
    $('#user_history_search').on('keyup', function() {
        clearTimeout(historySearchTimeout);
        historySearchTimeout = setTimeout(function() {
            loadRatingHistoryData(1);
        }, 500);
    });

    // Sales item detail button handler (event delegation)
    $(document).on('click', '.sales_item_detail_btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var pt_id = $(this).attr('data-pt_id');
        var invoice = $(this).text();
        openSalesItemDetailModal(pt_id, invoice);
    });

    // Export rating
    $('#export_rating_btn').on('click', function(e) {
        e.stopPropagation();
        $('#export_rating_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_rating_btn, #export_rating_menu').length) {
            $('#export_rating_menu').addClass('hidden');
        }
    });

    $('#export_rating_excel_btn').on('click', function() {
        var table = document.getElementById('RatingTable');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
        XLSX.writeFile(wb, "rating_summary_" + new Date().toISOString().split('T')[0] + ".xlsx");
        $('#export_rating_menu').addClass('hidden');
    });

    // Export history
    $('#export_history_btn').on('click', function(e) {
        e.stopPropagation();
        $('#export_history_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_history_btn, #export_history_menu').length) {
            $('#export_history_menu').addClass('hidden');
        }
    });

    $('#export_history_excel_btn').on('click', function() {
        var table = document.getElementById('RatingHistoryTable');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
        XLSX.writeFile(wb, "rating_history_" + new Date().toISOString().split('T')[0] + ".xlsx");
        $('#export_history_menu').addClass('hidden');
    });

    // Close modal
    $('#close_sales_item_detail_btn, #close_sales_item_detail_btn_2').on('click', function() {
        closeSalesItemDetailModal();
    });
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
