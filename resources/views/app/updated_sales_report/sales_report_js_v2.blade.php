<script>
var invoiceCurrentPage = 1;
var articleCurrentPage = 1;
var perPage = 25;
var autoRefreshInterval = null;

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
        $jq('#sales_date').val(hidden_range);
        $jq('#kt_dashboard_daterangepicker_date').html(range);
        $jq('#kt_dashboard_daterangepicker_title').html(title);
        loadInvoiceData(1);
        loadArticleData(1);
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

function createCustomPagination(paginationInfoId, paginationControlsId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var paginationInfo = document.querySelector('#' + paginationInfoId);
    var paginationControls = document.querySelector('#' + paginationControlsId);
    
    if (!paginationInfo || !paginationControls) return;

    paginationControls.innerHTML = '';

    var start = totalRecords > 0 ? (currentPage - 1) * perPage + 1 : 0;
    var end = Math.min(currentPage * perPage, totalRecords);
    var infoText = totalRecords > 0 ? 'Menampilkan ' + start + ' sampai ' + end + ' dari ' + totalRecords + ' data' : 'Tidak ada data';

    paginationInfo.textContent = infoText;

    if (totalPages > 1) {
        var prevBtn = document.createElement('button');
        prevBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
        prevBtn.textContent = '‹';
        prevBtn.type = 'button';
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (currentPage > 1) {
                loadFunction(currentPage - 1);
            }
        });
        paginationControls.appendChild(prevBtn);

        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            var firstBtn = document.createElement('button');
            firstBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
            firstBtn.textContent = '1';
            firstBtn.type = 'button';
            firstBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                loadFunction(1);
            });
            paginationControls.appendChild(firstBtn);
            if (startPage > 2) {
                var ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-sm text-gray-500';
                ellipsis.textContent = '...';
                paginationControls.appendChild(ellipsis);
            }
        }

        for (var i = startPage; i <= endPage; i++) {
            (function(page) {
                var pageBtn = document.createElement('button');
                pageBtn.className = 'px-3 py-1 text-sm border rounded ' + (page === currentPage ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50');
                pageBtn.textContent = page;
                pageBtn.type = 'button';
                pageBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    loadFunction(page);
                });
                paginationControls.appendChild(pageBtn);
            })(i);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                var ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-sm text-gray-500';
                ellipsis.textContent = '...';
                paginationControls.appendChild(ellipsis);
            }
            var lastBtn = document.createElement('button');
            lastBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
            lastBtn.textContent = totalPages;
            lastBtn.type = 'button';
            lastBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                loadFunction(totalPages);
            });
            paginationControls.appendChild(lastBtn);
        }

        var nextBtn = document.createElement('button');
        nextBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
        nextBtn.textContent = '›';
        nextBtn.type = 'button';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (currentPage < totalPages) {
                loadFunction(currentPage + 1);
            }
        });
        paginationControls.appendChild(nextBtn);
    }
}

function loadInvoiceData(page = 1) {
    invoiceCurrentPage = page;
    var stt_id = $('#stt_id').val();
    var st_id = $('#st_id_filter').val();
    var dp_id = $('#dp_id').val();
    var sales_date = $('#sales_date').val();
    var search = $('#invoice_report_search').val() || '';

    if (!sales_date) {
        $('#invoice_report_tbody').html('<tr><td colspan="32" class="px-3 py-4 text-center text-gray-500">Silakan pilih tanggal terlebih dahulu</td></tr>');
        return;
    }

    $.ajax({
        url: "{{ url('invoice_report_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            stt_id: stt_id,
            st_id: st_id,
            dp_id: dp_id,
            sales_date: sales_date,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#invoice_report_tbody').html('<tr><td colspan="32" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#invoice_report_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="32" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_created));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').html('<span class="px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded cursor-pointer invoice-detail-btn" data-pt-id="' + row.pt_id + '">' + row.pos_invoice + '</span>'));
                    tr.append($('<td>').addClass('px-3 py-4').html(row.cust_id ? '<a class="text-blue-600 hover:underline cursor-pointer customer-detail" data-id="' + row.cust_id + '">' + row.cust_name + '</a>' : row.cust_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.cross));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.dv_name));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.item_qty));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.item_value));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.pos_shipping));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.pos_unique_code));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.pos_admin_cost));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.pos_discount_seller));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.pos_another_cost));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.nameset));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.value_admin));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.total));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.payment_one));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.pos_payment));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_card_number));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_ref_number));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.payment_two));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.pos_payment_partial));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_card_number_two));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_ref_number_two));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_paid_dp));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.sub_payment));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_paid_dp_date));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_order_number));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_status));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_note));
                    tbody.append(tr);
                });
            }

            createCustomPagination('invoice_report_pagination_info', 'invoice_report_pagination_controls', response.total, response.current_page, response.total_pages, response.per_page, loadInvoiceData);
        },
        error: function() {
            $('#invoice_report_tbody').html('<tr><td colspan="32" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function loadArticleData(page = 1) {
    articleCurrentPage = page;
    var stt_id = $('#stt_id').val();
    var st_id = $('#st_id_filter').val();
    var sales_date = $('#sales_date').val();
    var pt_id = $('#pt_id_filter').val();
    var search = $('#article_report_search').val() || '';

    if (!sales_date) {
        $('#article_report_tbody').html('<tr><td colspan="14" class="px-3 py-4 text-center text-gray-500">Silakan pilih tanggal terlebih dahulu</td></tr>');
        return;
    }

    $.ajax({
        url: "{{ url('article_report_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            stt_id: stt_id,
            st_id: st_id,
            sales_date: sales_date,
            pt_id: pt_id,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#article_report_tbody').html('<tr><td colspan="14" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#article_report_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="14" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ptd_created));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_invoice));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.br_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.p_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pc_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.psc_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pssc_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.p_color));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.sz_name));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.pos_td_qty));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.price_tag));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.sell_price));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.total_price));
                    tbody.append(tr);
                });
            }

            createCustomPagination('article_report_pagination_info', 'article_report_pagination_controls', response.total, response.current_page, response.total_pages, response.per_page, loadArticleData);
        },
        error: function() {
            $('#article_report_tbody').html('<tr><td colspan="14" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize date range picker - wait for all dependencies to load
    // Scripts are loaded in order: sweetalert2 -> _partials.js (jQuery) -> moment -> daterangepicker -> this script
    // So we need to wait a bit for daterangepicker to finish loading
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


    // Filter change handlers
    $('#stt_id, #st_id_filter, #dp_id').on('change', function() {
        if ($('#sales_date').val()) {
            loadInvoiceData(1);
            loadArticleData(1);
        }
    });

    // Search handlers
    var invoiceSearchTimeout;
    $('#invoice_report_search').on('keyup', function() {
        clearTimeout(invoiceSearchTimeout);
        invoiceSearchTimeout = setTimeout(function() {
            if ($('#sales_date').val()) {
                loadInvoiceData(1);
            }
        }, 1000);
    });

    var articleSearchTimeout;
    $('#article_report_search').on('keyup', function() {
        clearTimeout(articleSearchTimeout);
        articleSearchTimeout = setTimeout(function() {
            if ($('#sales_date').val()) {
                loadArticleData(1);
            }
        }, 1000);
    });

    // Invoice detail button handler
    $(document).on('click', '.invoice-detail-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var pt_id = $(this).attr('data-pt-id');
        var pt_id_label = $(this).text();
        $('#pt_id_filter').val(pt_id);
        $('#pt_id_filter_label').addClass('px-3 py-1 text-sm font-medium text-white bg-green-600 rounded cursor-pointer');
        $('#pt_id_filter_label').text(pt_id_label);
        loadArticleData(1);
    });

    // Remove invoice filter
    $(document).on('click', '#pt_id_filter_label', function() {
        $('#pt_id_filter').val('');
        $('#pt_id_filter_label').removeClass('px-3 py-1 text-sm font-medium text-white bg-green-600 rounded cursor-pointer');
        $('#pt_id_filter_label').text('');
        loadArticleData(1);
    });

    // Customer detail handler
    $(document).on('click', '.customer-detail', function() {
        var customerId = $(this).data('id');
        $.ajax({
            url: "{{ url('customer_details') }}",
            type: 'GET',
            data: {
                _id: customerId
            },
            success: function(response) {
                $('#customer-detail-body').html(response);
                $('#customerDetailModal').removeClass('hidden');
            },
            error: function(xhr) {
                $('#customer-detail-body').html('Gagal mengambil data.');
            }
        });
    });

    // Export handlers
    $('#export_invoice_btn, #export_article_btn').on('click', function(e) {
        e.preventDefault();
        var type = $(this).attr('data-type');
        var stt_id = $('#stt_id').val();
        var st_id = $('#st_id_filter').val();
        var date = $('#sales_date').val();
        var dp_id = $('#dp_id').val();
        window.location.href = "{{ url('sales_export') }}?type=" + type + "&date=" + date + "&stt_id=" + stt_id + "&st_id=" + st_id + "&dp_id=" + dp_id + "";
    });

    // Summary button handler
    $('#sales_summary_btn').on('click', function() {
        var st_label = $('#st_id_filter option:selected').text();
        var stt_label = $('#stt_id option:selected').text();
        var omset_date = $('#kt_dashboard_daterangepicker_date').text();

        var st_id = $('#st_id_filter option:selected').val();
        var stt_id = $('#stt_id option:selected').val();
        var date = $('#sales_date').val();

        if (st_label == '- Storage -') {
            Swal.fire('Storage', 'Pilih storage terlebih dahulu', 'warning');
            return false;
        }

        $('#CabangSummaryModal').removeClass('hidden');
        $('#cabang_omset_date').text(omset_date);
        cabangSummary(st_id, stt_id, date);
    });

    // Check HB HJ button handler
    $('#check_hb_hj').on('click', function() {
        $('#HBHJModal').removeClass('hidden');
        loadHbhjData(1);
    });

    // Search handler for HBHJ
    var hbhjSearchTimeout;
    $('#hbhj_search').on('keyup', function() {
        clearTimeout(hbhjSearchTimeout);
        hbhjSearchTimeout = setTimeout(function() {
            loadHbhjData(1);
        }, 1000);
    });

    // Autorefresh handler
    $('#autorefresh').on('change', function() {
        if ($(this).val() == 'auto') {
            autoRefreshInterval = setInterval(function() {
                if ($('#sales_date').val()) {
                    loadInvoiceData(invoiceCurrentPage);
                    loadArticleData(articleCurrentPage);
                }
            }, 30000); // Refresh every 30 seconds
        } else {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
        }
    });
});

function cabangSummary(st_id, stt_id, date) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        data: {
            _st_id: st_id,
            _stt_id: stt_id,
            _date: date
        },
        dataType: 'json',
        url: "{{ url('cabang_summary') }}",
        success: function(r) {
            if (r.status == '200') {
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
                $('#cabang_omset').text(addCommas(r.omset_global));
                $('#cabang_target').text(addCommas(r.target));
                $('#cabang_total_debit').text(addCommas(r.total_debit));
                $('#cabang_total_debit_bca').text(addCommas(r.total_debit_bca));
                $('#cabang_total_debit_bri').text(addCommas(r.total_debit_bri));
                $('#cabang_total_debit_bni').text(addCommas(r.total_debit_bni));
                $('#cabang_total_debit_mandiri').text(addCommas(r.total_debit_mandiri));
                $('#cabang_total_qr').text(addCommas(r.total_qr));
                $('#cabang_total_transfer').text(addCommas(r.total_transfer));
                $('#cabang_total_cash').text(addCommas(r.total_cash));
                $('#cabang_cross_order').text(addCommas(r.cross_order));
                Swal.fire('Berhasil', 'Informasi berhasil ditampilkan', 'success');
            } else {
                Swal.fire('Gagal', 'Informasi gagal ditampilan', 'warning');
            }
        }
    });
}

function cabangPdf() {
    // PDF generation function - implement if needed
    Swal.fire('Info', 'Fitur PDF akan segera hadir', 'info');
}

var hbhjCurrentPage = 1;
var hbhjPerPage = 25;

function loadHbhjData(page = 1) {
    hbhjCurrentPage = page;
    var search = $('#hbhj_search').val() || '';

    $.ajax({
        url: "{{ url('check_hb_hj_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: hbhjPerPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#hbhj_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#hbhj_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="9" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.br_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ps_barcode));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.p_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.p_color));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.sz_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.psc_name));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.hb));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.hj));
                    tbody.append(tr);
                });
            }

            // Update pagination if exists
            var paginationInfo = $('#hbhj_pagination_info');
            var paginationControls = $('#hbhj_pagination_controls');
            if (paginationInfo.length && paginationControls.length) {
                createCustomPagination('hbhj_pagination_info', 'hbhj_pagination_controls', response.total, response.current_page, response.total_pages, response.per_page, loadHbhjData);
            }
        },
        error: function() {
            $('#hbhj_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}
</script>
