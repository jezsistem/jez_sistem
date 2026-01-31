<script>
var currentPage = 1;
var perPage = 25;
var dashboard_date = '';
var st_id = '';
var br_id = '';
var exception = 'noexcept';

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

        if ((end - start) < 100 || label == 'Hari Ini' || label == '') {
            title = 'Hari Ini:';
            range = start.format('DD MMM YYYY');
            dashboard_date = start.format('YYYY-MM-DD');
        } else if (label == 'Kemarin') {
            title = 'Kemarin:';
            range = start.format('DD MMM YYYY');
            dashboard_date = start.format('YYYY-MM-DD');
        } else {
            range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
            dashboard_date = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
        }
        $jq('#kt_dashboard_daterangepicker_date').html(range);
        $jq('#kt_dashboard_daterangepicker_title').html(title);
        $jq('#dashboard_date').val(dashboard_date);
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

function loadArticleData(page = 1) {
    currentPage = page;
    var search = $('#article_search').val() || '';

    if (!dashboard_date || !st_id) {
        $('#article_tbody').html('<tr><td colspan="16" class="px-3 py-4 text-center text-gray-500">Silakan pilih tanggal dan store terlebih dahulu</td></tr>');
        return;
    }

    $.ajax({
        url: "{{ url('stc_article_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            date: dashboard_date,
            st_id: st_id,
            br_id: br_id,
            exception: exception,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#article_tbody').html('<tr><td colspan="16" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#article_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="16" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.article_id));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.item_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ps_barcode));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.size));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.brand));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.begin_stocks));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.purchase));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.trans_in));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.tf_out));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.sales));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.SO_adjustment_plus));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.SO_adjustment_minus));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.SO_adjustment_diff));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.ending_stocks));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.today_stocks));
                    tbody.append(tr);
                });
            }

            createCustomPagination('article_pagination_info', 'article_pagination_controls', response.total, response.current_page, response.total_pages, response.per_page, loadArticleData);
        },
        error: function() {
            $('#article_tbody').html('<tr><td colspan="16" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function phase2() {
    $('#export_btn').text('Mohon tunggu, memasuki kalkulasi fase 2 ...');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        data: {st_id: st_id, br_id: br_id, date: dashboard_date, exception: exception},
        dataType: "json",
        url: "{{ url('stock_report_phase2') }}",
        success: function (r) {
            if (r.status == '200') {
                phase3();
            }
        }
    });
    return false;
}

function phase3() {
    $('#export_btn').text('Mohon tunggu, memasuki kalkulasi fase terakhir, menyiapkan file anda ...');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        data: {st_id: st_id, br_id: br_id, date: dashboard_date, exception: exception},
        dataType: "json",
        url: "{{ url('stock_report_phase3') }}",
        success: function (r) {
            if (r.status == '200') {
                getExport();
            }
        }
    });
    return false;
}

function getExport() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        url: "{{ url('stock_report_export') }}",
        xhrFields: {
            responseType: 'blob'
        },
        success: function (blob, status, xhr) {
            $('#export_btn').text('Download Excel');
            var filename = "";
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
            }

            if (typeof window.navigator.msSaveBlob !== 'undefined') {
                window.navigator.msSaveBlob(blob, filename);
            } else {
                var URL = window.URL || window.webkitURL;
                var downloadUrl = URL.createObjectURL(blob);

                if (filename) {
                    var a = document.createElement("a");
                    if (typeof a.download === 'undefined') {
                        window.location.href = downloadUrl;
                    } else {
                        a.href = downloadUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                    }
                } else {
                    window.location.href = downloadUrl;
                }

                setTimeout(function () {
                    URL.revokeObjectURL(downloadUrl);
                }, 10000);
            }
            $('#export_btn').text('Download Excel');
        }
    });
    return false;
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
    $('#store_filter').on('change', function() {
        st_id = $(this).val();
    });

    $('#brand_filter').on('change', function() {
        br_id = $(this).val();
    });

    $('#exception_filter').on('change', function() {
        exception = $(this).val();
    });

    // Exec button handler
    $('#exec_btn').on('click', function() {
        if (!dashboard_date || !st_id) {
            Swal.fire('Peringatan', 'Silakan pilih tanggal dan store terlebih dahulu', 'warning');
            return;
        }
        $('#article_panel').show();
        loadArticleData(1);
    });

    // Search handler
    var searchTimeout;
    $('#article_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if (dashboard_date && st_id) {
                loadArticleData(1);
            }
        }, 1000);
    });

    // Export button handler
    $('#export_btn').on('click', function(e) {
        e.preventDefault();
        if (!dashboard_date || !st_id) {
            Swal.fire('Peringatan', 'Silakan pilih tanggal dan store terlebih dahulu', 'warning');
            return;
        }
        Swal.fire({
            title: "Download Excel..?",
            text: "Download data dengan kriteria customize diatas?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: 'Download',
            cancelButtonText: 'Batal'
        }).then(function (isConfirm) {
            if (isConfirm.isConfirmed) {
                $('#export_btn').text('Mohon tunggu, kalkulasi fase 1 ...');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    data: {st_id: st_id, br_id: br_id, date: dashboard_date, exception: exception},
                    dataType: "json",
                    url: "{{ url('stock_report_fill_data') }}",
                    success: function (r) {
                        if (r.status == '200') {
                            phase2();
                        }
                    }
                });
            }
        });
    });

});
</script>
