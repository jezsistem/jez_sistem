<script>
var date = '';
var stockTrackingDataTable = null;
var stockTrackingDataArray = [];

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

function loadNotice(st_id) {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        type: 'POST',
        url: "{{ url('get_stock_notice') }}",
        data: { st_id: st_id },
        dataType: 'json',
        success: function(r) {
            if (r.status == '200') {
                $('#problem_btn').text(r.problem).attr('data-notice', r.problem_notice);
                $('#waiting_online_btn').text(r.online).attr('data-notice', r.online_notice);
                $('#waiting_offline_btn').text(r.offline).attr('data-notice', r.offline_notice);
            } else {
                $('#problem_btn').text('0').attr('data-notice', '');
                $('#waiting_online_btn').text('0').attr('data-notice', '');
                $('#waiting_offline_btn').text('0').attr('data-notice', '');
            }
        }
    });
}

function loadGraph(st_id, date, label) {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        type: 'POST',
        url: "{{ url('get_stock_graph') }}",
        data: { st_id: st_id, date: date, label: label },
        dataType: 'html',
        success: function(r) {
            $('#chart').html(r);
        }
    });
}

function loadStockTrackingData() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    
    $.ajax({
        url: "{{ url('stock_tracking_datatables') }}",
        type: 'GET',
        data: {
            search: $('#stock_tracking_search').val(),
            status: $('#status_filter').val(),
            psc_id: $('#psc_id').val(),
            std_id: $('#std_id').val(),
            br_id: $('#br_id').val(),
            st_id: $('#st_id_filter').val(),
            date: date,
            length: -1 // Get all data for simple-datatables
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing datatable if exists
            if (stockTrackingDataTable) {
                stockTrackingDataTable.destroy();
            }
            
            // Store data array
            stockTrackingDataArray = response.data || [];
            
            // Clear table body
            $('#StockTrackingtb tbody').empty();
            
            // Populate table with data
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50 cursor-pointer clickable-row" data-plst_id="' + (row.plst_id || '') + '">';
                    html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + (row.datetime || '') + '</td>';
                    html += '<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">' + escapeHtml(row.ps_barcode || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + (row.article || '') + '</td>';
                    html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + (row.invoice || '-') + '</td>';
                    html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.cust_name || '') + '</td>';
                    html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.qty || '0') + '</td>';
                    html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + (row.product_location || '') + '</td>';
                    html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + (row.status || '') + '</td>';
                    html += '</tr>';
                    $('#StockTrackingtb tbody').append(html);
                });
            } else {
                $('#StockTrackingtb tbody').append('<tr><td colspan="9" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize simple-datatables
            if (document.getElementById("StockTrackingtb") && typeof simpleDatatables !== 'undefined') {
                stockTrackingDataTable = new simpleDatatables.DataTable("#StockTrackingtb", {
                    searchable: true,
                    sortable: true,
                    perPage: 25,
                    perPageSelect: [10, 25, 50, 100],
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
                    $('#StockTrackingtb tbody').off('click', 'tr').on('click', 'tr', function() {
                        var $row = $(this);
                        var plst_id = $row.data('plst_id');
                        
                        if (plst_id) {
                            // Find the article button in this row
                            var $articleBtn = $row.find('#user_detail_btn');
                            if ($articleBtn.length > 0) {
                                var article = $articleBtn.text();
                                var invoice = $articleBtn.attr('invoice') || '';
                                var picker = $articleBtn.attr('picker') || '';
                                var helper = $articleBtn.attr('helper') || '';
                                var cashier = $articleBtn.attr('cashier') || '';
                                var packer = $articleBtn.attr('packer') || '';
                                var customer = $articleBtn.attr('customer') || '';
                                var status = $articleBtn.attr('status') || '';
                                var pos_note = $articleBtn.attr('pos_note') || '';
                                var refund_exchange_note = $articleBtn.attr('refund_exchange_note') || '';
                                var updated = $articleBtn.attr('updated') || '';
                                var backend = $articleBtn.attr('backend') || '';

                                $('#article_label').text(article);
                                $('#invoice_label').text(invoice);
                                $('#picker_label').text(picker);
                                $('#helper_label').text(helper);
                                $('#cashier_label').text(cashier);
                                $('#packer_label').text(packer);
                                $('#customer_label').text(customer);
                                $('#status_label').text(status);
                                $('#invoice_note_label').text(pos_note);
                                $('#refund_exchange_note_label').text(refund_exchange_note);
                                $('#last_updated_label').text(updated);
                                
                                if (invoice != '') {
                                    $('#delete_btn').addClass('hidden');
                                } else {
                                    $('#delete_btn').removeClass('hidden').attr('data-id', backend);
                                }
                                
                                openUserModal();
                            }
                        }
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

$(document).ready(function() {
    loadNotice($('#st_id_filter').val());
    
    // Date range picker
    var start = moment();
    var end = moment();
    
    function cb(start, end, label) {
        var title = '';
        var range = '';
        if ((end - start) < 100 || label == 'Hari Ini') {
            title = 'Hari Ini:';
            range = start.format('DD MMM YYYY');
            date = start.format('YYYY-MM-DD');
        } else if (label == 'Kemarin') {
            title = 'Kemarin:';
            range = start.format('DD MMM YYYY');
            date = start.format('YYYY-MM-DD');
        } else {
            range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
            date = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
        }
        $('#kt_dashboard_daterangepicker_date').html(range);
        $('#kt_dashboard_daterangepicker_title').html(title);
        loadStockTrackingData();
        loadNotice($('#st_id_filter').val());
    }
    
    $('#kt_dashboard_daterangepicker').daterangepicker({
        startDate: start,
        endDate: end,
        opens: 'center',
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
    
    // Filter change handlers
    $('#status_filter, #psc_id, #std_id, #br_id').on('change', function() {
        loadStockTrackingData();
    });
    
    $('#st_id_filter').on('change', function() {
        loadStockTrackingData();
        loadNotice($(this).val());
    });
    
    // Search handler
    var searchTimeout;
    $('#stock_tracking_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        var query = $(this).val();
        searchTimeout = setTimeout(function() {
            if (jQuery.trim(query).length > 4 || jQuery.trim(query).length < 1) {
                loadStockTrackingData();
            }
        }, 400);
    });
    
    // Notice button handlers
    $(document).delegate('#waiting_offline_btn', 'click', function(e) {
        e.preventDefault();
        var notice = $(this).attr('data-notice');
        swal('', notice, 'info');
    });
    
    $(document).delegate('#waiting_online_btn', 'click', function(e) {
        e.preventDefault();
        var notice = $(this).attr('data-notice');
        swal('', notice, 'info');
    });
    
    $(document).delegate('#problem_btn', 'click', function(e) {
        e.preventDefault();
        if ($(this).text() != '0') {
            var notice = $(this).attr('data-notice');
            swal('', notice, 'info');
        }
    });
    
    // Graph button
    $(document).delegate('#graph_btn', 'click', function(e) {
        e.preventDefault();
        openGraphModal();
        setTimeout(() => {
            loadGraph($('#st_id_filter').val(), date, $('#chart_filter').val());
        }, 500);
    });
    
    // Chart filter change
    $(document).delegate('#chart_filter', 'change', function(e) {
        e.preventDefault();
        var label = $(this).val();
        loadGraph($('#st_id_filter').val(), date, label);
    });
    
    // Delete button
    $(document).delegate('#delete_btn', 'click', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        swal({
            title: "Hapus..?",
            text: "Yakin hapus data ini ?",
            icon: "warning",
            buttons: ['Batalkan', 'Hapus'],
            dangerMode: true,
        }).then(function(isConfirm) {
            if (isConfirm) {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                });
                $.ajax({
                    type: "POST",
                    data: { id: id },
                    dataType: 'json',
                    url: "{{ url('plst_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            loadStockTrackingData();
                            closeUserModal();
                            swal("Berhasil", "Data berhasil dihapus", "success");
                        } else {
                            swal('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });
    
    // Export button
    $('#export_stock_tracking').on('click', function() {
        const st_id = $('#st_id_filter').val();
        const br_id = $('#br_id').val();
        const psc_id = $('#psc_id').val();
        const std_id = $('#std_id').val();
        const range = date;
        const status = $('#status_filter').val();
        
        const queryParams = new URLSearchParams({
            st_id, br_id, psc_id, std_id, range, status
        });
        
        window.location.href = `/export-stock-tracking?${queryParams.toString()}`;
    });
});
</script>
