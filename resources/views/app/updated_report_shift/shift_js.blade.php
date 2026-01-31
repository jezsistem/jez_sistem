<script>
var userShiftCurrentPage = 1;
var perPage = 25;
var sales_date = '';

function replaceComma(str) {
    var str_replace = str.replace(/,/g, '');
    return str_replace;
}

function loadUserShiftData(page = 1) {
    userShiftCurrentPage = page;
    var search = $('#user_shift_search').val();
    var st_id = $('#st_id_filter').val();
    
    if (!sales_date) {
        $('#user_shift_tbody').html('<tr><td colspan="10" class="px-3 py-4 text-center text-gray-500">Silakan pilih tanggal untuk memuat data</td></tr>');
        return;
    }
    
    $.ajax({
        url: "{{ url('report_shift_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            st_id: st_id
        },
        success: function(response) {
            if (response.error) {
                $('#user_shift_tbody').html('<tr><td colspan="10" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#user_shift_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="10" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.date));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.start_time));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.end_time));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.total_pos_real_price));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.total_pos_payment_price));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.laba_shift));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.difference));
                    tr.on('click', function() {
                        loadShiftDetail(row);
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#UserShiftTable', response.total, response.current_page, response.total_pages, response.per_page, loadUserShiftData, 'user_shift');
        },
        error: function() {
            $('#user_shift_tbody').html('<tr><td colspan="10" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function loadShiftDetail(row) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ url('report_shift_detail') }}",
        type: 'POST',
        data: {
            id: row.id,
            start_time_original: row.start_time_original,
            end_time_original: row.end_time_original,
            date: row.date,
            start_time: row.start_time,
            end_time: row.end_time,
            total_pos_real_price: replaceComma(row.total_pos_real_price),
            total_pos_payment_price: replaceComma(row.total_pos_payment_price),
            laba_shift: replaceComma(row.laba_shift),
            difference: replaceComma(row.difference),
            st_name: row.st_name,
            u_name: row.u_name,
            st_id: row.st_id
        },
        success: function(response) {
            $('#UserShiftModal').removeClass('hidden');
            $('#UserShiftModalBody').html(response);

            // Product sold button handler
            $('#userShiftDetailSoldBtn').on('click', function() {
                $.ajax({
                    url: "{{ url('report_shift_product_sold') }}",
                    type: 'POST',
                    data: {
                        id: row.id,
                        start_time_original: row.start_time_original,
                        end_time_original: row.end_time_original,
                    },
                    success: function(response_detail) {
                        $('#UserShiftDetailSoldModal').removeClass('hidden');
                        $('#UserShiftDetailSoldModalBody').html(response_detail);
                    },
                    error: function(response) {
                        console.log(response);
                        Swal.fire('Error', 'Gagal memuat data produk terjual', 'error');
                    }
                });
            });

            // Product refund button handler
            $('#userShiftDetailRefundBtn').on('click', function() {
                $.ajax({
                    url: "{{ url('report_shift_product_refund') }}",
                    type: 'POST',
                    data: {
                        id: row.id,
                        start_time_original: row.start_time_original,
                        end_time_original: row.end_time_original,
                    },
                    success: function(response_detail) {
                        $('#UserShiftDetailRefundModal').removeClass('hidden');
                        $('#UserShiftDetailRefundModalBody').html(response_detail);
                    },
                    error: function(response) {
                        console.log(response);
                        Swal.fire('Error', 'Gagal memuat data produk refund', 'error');
                    }
                });
            });
        },
        error: function(response) {
            console.log(response);
            Swal.fire('Error', 'Gagal memuat detail shift', 'error');
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

function toggleExportMenu() {
    $('#export_menu').toggleClass('hidden');
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Search handler with debounce
    var userShiftSearchTimeout;
    $('#user_shift_search').on('keyup', function() {
        clearTimeout(userShiftSearchTimeout);
        userShiftSearchTimeout = setTimeout(function() {
            loadUserShiftData(1);
        }, 500);
    });

    // Filter change handler
    $('#st_id_filter').on('change', function() {
        loadUserShiftData(1);
    });

    // Export button handlers
    $('#export_btn').on('click', function(e) {
        e.stopPropagation();
        toggleExportMenu();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_btn, #export_menu').length) {
            $('#export_menu').addClass('hidden');
        }
    });

    $('#export_excel_btn').on('click', function() {
        // Export to Excel functionality
        var table = document.getElementById('UserShiftTable');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
        XLSX.writeFile(wb, "report_shift_" + new Date().toISOString().split('T')[0] + ".xlsx");
        $('#export_menu').addClass('hidden');
    });

    // Close modal handlers
    $('#close_user_shift_btn, #close_user_shift_btn_2').on('click', function() {
        $('#UserShiftModal').addClass('hidden');
    });

    $('#close_user_shift_detail_sold_btn, #close_user_shift_detail_sold_btn_2').on('click', function() {
        $('#UserShiftDetailSoldModal').addClass('hidden');
    });

    $('#close_user_shift_detail_refund_btn, #close_user_shift_detail_refund_btn_2').on('click', function() {
        $('#UserShiftDetailRefundModal').addClass('hidden');
    });

    // Initialize date range picker after scripts are loaded
    setTimeout(function() {
        var checkCount = 0;
        var maxChecks = 100;
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
    }, 500);
});

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
        sales_date = hidden_range;
        $jq('#sales_date').val(hidden_range);
        $jq('#kt_dashboard_daterangepicker_date').html(range);
        $jq('#kt_dashboard_daterangepicker_title').html(title);
        loadUserShiftData(1);
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
