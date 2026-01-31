<script>
const primary = '#6993FF';
const success = '#1BC5BD';
const info = '#8950FC';
const warning = '#FFA800';
const danger = '#F64E60';

// Chart instances
var offline_chart, online_chart, cross_chart;
var onlineTable = null, offlineTable = null, salesDetailTable = null, salesItemDetailTable = null, productRatingTable = null;

// Initialize charts
function initCharts() {
    const offlineChart = "#offline_chart";
    var offline_options = {
        series: [],
        chart: {
            height: 350,
            type: 'bar',
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'straight' },
        plotOptions: { bar: { borderRadius: 4 } },
        grid: { row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 } },
        xaxis: { categories: ['Target', 'Tercapai'] },
        yaxis: {
            labels: {
                formatter: function (val) { return addCommas(val); }
            }
        },
        colors: [primary]
    };
    if (document.querySelector(offlineChart)) {
        offline_chart = new ApexCharts(document.querySelector(offlineChart), offline_options);
        offline_chart.render();
    }

    const onlineChart = "#online_chart";
    var online_options = {
        series: [],
        chart: {
            height: 350,
            type: 'bar',
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'straight' },
        plotOptions: { bar: { borderRadius: 4 } },
        grid: { row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 } },
        xaxis: { categories: ['Target', 'Tercapai'] },
        yaxis: {
            labels: {
                formatter: function (val) { return addCommas(val); }
            }
        },
        colors: [primary]
    };
    if (document.querySelector(onlineChart)) {
        online_chart = new ApexCharts(document.querySelector(onlineChart), online_options);
        online_chart.render();
    }

    const crossChart = "#cross_chart";
    var cross_options = {
        series: [],
        chart: {
            height: 350,
            type: 'bar',
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'straight' },
        plotOptions: { bar: { borderRadius: 4 } },
        grid: { row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 } },
        xaxis: { categories: ['Tercapai'] },
        yaxis: {
            labels: {
                formatter: function (val) { return addCommas(val); }
            }
        },
        colors: [primary]
    };
    if (document.querySelector(crossChart)) {
        cross_chart = new ApexCharts(document.querySelector(crossChart), cross_options);
        cross_chart.render();
    }
}

function addCommas(nStr) {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

// Load Online Data
function loadOnlineData() {
    $.ajax({
        url: "{{ url('pos_summary_online_datatables') }}",
        type: 'GET',
        data: {
            pos_summary_date: $('#pos_summary_date').val(),
            st_id: $('#st_id_filter').val(),
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (onlineTable) { onlineTable.destroy(); }
            const $tbody = $('#Onlinetb tbody');
            $tbody.empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.u_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.sales || '-') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.sales_total || '-') + '</td>';
                    html += '</tr>';
                    $tbody.append(html);
                });
            } else {
                $tbody.append('<tr><td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Onlinetb") && typeof simpleDatatables !== 'undefined') {
                onlineTable = new simpleDatatables.DataTable("#Onlinetb", {
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
            }
        },
        error: function(xhr) {
            console.error('Error loading online data:', xhr);
        }
    });
}

// Load Offline Data
function loadOfflineData() {
    $.ajax({
        url: "{{ url('pos_summary_offline_datatables') }}",
        type: 'GET',
        data: {
            pos_summary_date: $('#pos_summary_date').val(),
            st_id: $('#st_id_filter').val(),
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (offlineTable) { offlineTable.destroy(); }
            const $tbody = $('#Offlinetb tbody');
            $tbody.empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.u_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.sales || '-') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.sales_total || '-') + '</td>';
                    html += '</tr>';
                    $tbody.append(html);
                });
            } else {
                $tbody.append('<tr><td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Offlinetb") && typeof simpleDatatables !== 'undefined') {
                offlineTable = new simpleDatatables.DataTable("#Offlinetb", {
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
            }
        },
        error: function(xhr) {
            console.error('Error loading offline data:', xhr);
        }
    });
}

// Load Sales Detail Data
function loadSalesDetailData() {
    $.ajax({
        url: "{{ url('sales_detail_datatables') }}",
        type: 'GET',
        data: {
            pos_summary_date: $('#pos_summary_date').val(),
            u_id: $('#u_id').val(),
            st_id: $('#st_id_filter').val(),
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (salesDetailTable) { salesDetailTable.destroy(); }
            const $tbody = $('#SalesDetailtb tbody');
            $tbody.empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.pos_invoice || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.sales || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.sales_total || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.created_at || '') + '</td>';
                    html += '</tr>';
                    $tbody.append(html);
                });
            } else {
                $tbody.append('<tr><td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("SalesDetailtb") && typeof simpleDatatables !== 'undefined') {
                salesDetailTable = new simpleDatatables.DataTable("#SalesDetailtb", {
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
            }
        },
        error: function(xhr) {
            console.error('Error loading sales detail data:', xhr);
        }
    });
}

// Load Sales Item Detail Data
function loadSalesItemDetailData() {
    $.ajax({
        url: "{{ url('sales_item_detail_datatables') }}",
        type: 'GET',
        data: {
            pt_id: $('#pt_id').val(),
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (salesItemDetailTable) { salesItemDetailTable.destroy(); }
            const $tbody = $('#SalesItemDetailtb tbody');
            $tbody.empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.article || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_td_qty || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_td_total_price || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.created_at || '') + '</td>';
                    html += '</tr>';
                    $tbody.append(html);
                });
            } else {
                $tbody.append('<tr><td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("SalesItemDetailtb") && typeof simpleDatatables !== 'undefined') {
                salesItemDetailTable = new simpleDatatables.DataTable("#SalesItemDetailtb", {
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
            }
        },
        error: function(xhr) {
            console.error('Error loading sales item detail data:', xhr);
        }
    });
}

// Load Product Rating Data
function getPower(dashboard_date, st_id, pc_id, specific) {
    $('#ProductRatingtb tbody').empty();
    if ($('#st_filter').val() != '') {
        $('#article_power_label').text('Mohon ditunggu, sedang menyiapkan data ..');
    }
    if ($('#pc_filter').val() == '') {
        $('#article_power_label').text('Product Rating');
    }
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        type: "POST",
        data: {dashboard_date: dashboard_date, st_id: st_id, pc_id: pc_id, specific: specific},
        dataType: 'json',
        url: "{{ url('get_power') }}",
        success: function(r) {
            if (productRatingTable) { productRatingTable.destroy(); }
            const $tbody = $('#ProductRatingtb tbody');
            $tbody.empty();
            
            if (r.status == '200' && r.data && r.data.length > 0) {
                r.data.forEach(function(row, index) {
                    var max = r.max;
                    var rating = parseFloat(row['power']) / parseFloat(max) * 100;
                    var power = rating.toFixed();
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (parseInt(index) + 1) + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + addCommas(row['power']) + ' (' + power + '/100)' + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row['brand'] || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row['article'] || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row['aging'] || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row['hj'] || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row['sold'] || '') + '</td>';
                    html += '</tr>';
                    $tbody.append(html);
                });
                
                if (document.getElementById("ProductRatingtb") && typeof simpleDatatables !== 'undefined') {
                    productRatingTable = new simpleDatatables.DataTable("#ProductRatingtb", {
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
                }
            } else {
                $tbody.append('<tr><td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            $('#article_power_label').text('Product Rating');
        },
        error: function() {
            $('#article_power_label').text('Product Rating');
            Swal.fire('Gagal', 'Gagal menampilkan data', 'error');
        }
    });
    return false;
}

// Chart functions
function renderOfflineTarget(range, label) {
    var st_id = $('#st_id_filter').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        url: "{{ url('target_chart') }}",
        data: {_range: range, _type: 'offline', _label: label, _st_id: st_id},
        dataType: 'json',
        success: function(r) {
            if (r.status == '200' && offline_chart) {
                offline_chart.updateSeries([{
                    name: 'Target',
                    data: [r.target],
                    color: success
                }, {
                    name: 'Tercapai',
                    data: [r.get],
                    color: primary
                }]);
            } else {
                Swal.fire('ERROR', 'ERROR', 'warning');
            }
        },
        error: function(data) {
            Swal.fire('Error', data, 'error');
        }
    });
}

function renderOnlineTarget(range, label) {
    var st_id = $('#st_id_filter').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        url: "{{ url('target_chart') }}",
        data: {_range: range, _type: 'online', _label: label, _st_id: st_id},
        dataType: 'json',
        success: function(r) {
            if (r.status == '200' && online_chart) {
                online_chart.updateSeries([{
                    name: 'Target',
                    data: [r.target],
                    color: success
                }, {
                    name: 'Tercapai',
                    data: [r.get],
                    color: primary
                }]);
            } else {
                Swal.fire('ERROR', 'ERROR', 'warning');
            }
        },
        error: function(data) {
            Swal.fire('Error', data, 'error');
        }
    });
}

function renderCross(range) {
    var st_id = $('#st_id_filter').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        url: "{{ url('cross_chart') }}",
        data: {_range: range, _type: 'online', _st_id: st_id},
        dataType: 'json',
        success: function(r) {
            if (r.status == '200' && cross_chart) {
                cross_chart.updateSeries([
                    {
                        name: 'Target',
                        data: ['0'],
                        color: success
                    },
                    {
                        name: 'Tercapai',
                        data: [r.get],
                        color: primary
                    }
                ]);
            } else {
                Swal.fire('ERROR', 'ERROR', 'warning');
            }
        },
        error: function(data) {
            Swal.fire('Error', data, 'error');
        }
    });
}

$(document).ready(function() {
    // Initialize charts
    initCharts();
    
    // Initialize date range picker
    initDateRangePicker();
    
    // Store filter change
    $('#st_id_filter').on('change', function() {
        var hidden_range = $('#pos_summary_date').val();
        var label = '';
        
        // Show cross chart if store selected
        if ($(this).val() != '') {
            $('.cross_chart').removeClass('hidden');
            $('.grid').removeClass('lg:grid-cols-2');
            $('.grid').addClass('lg:grid-cols-3');
        } else {
            $('.cross_chart').addClass('hidden');
            $('.grid').removeClass('lg:grid-cols-3');
            $('.grid').addClass('lg:grid-cols-2');
        }
        
        loadOnlineData();
        loadOfflineData();
        renderOfflineTarget(hidden_range, label);
        renderOnlineTarget(hidden_range, label);
        renderCross(hidden_range);
    });
    
    // Product rating filters
    $('#st_filter').on('change', function() {
        var dashboard_date = $('#pos_summary_date').val();
        var st_id = $(this).val();
        var pc_id = $('#pc_filter').val();
        var specific = $('#specific_filter').val();
        getPower(dashboard_date, st_id, pc_id, specific);
    });
    
    $('#pc_filter').on('change', function() {
        var dashboard_date = $('#pos_summary_date').val();
        var st_id = $('#st_filter').val();
        var pc_id = $(this).val();
        var specific = $('#specific_filter').val();
        getPower(dashboard_date, st_id, pc_id, specific);
    });
    
    $('#specific_filter').on('change', function() {
        var dashboard_date = $('#pos_summary_date').val();
        var st_id = $('#st_filter').val();
        var pc_id = $('#pc_filter').val();
        var specific = $(this).val();
        getPower(dashboard_date, st_id, pc_id, specific);
    });
    
    // Export Excel
    $('#export_excel').on('click', function() {
        jQuery("#ProductRatingtb").table2excel({
            filename: "Laporan Rating Artikel",
        });
    });
    
    // Date Range Picker - Initialize after document ready
    function initDateRangePicker() {
        var picker = $('#kt_dashboard_daterangepicker');
        if (picker.length > 0 && typeof moment !== 'undefined' && typeof $.fn.daterangepicker !== 'undefined') {
            var start = moment();
            var end = moment();
            
            function cb(start, end, label) {
                var title = '';
                var range = '';
                var hidden_range = '';
                var st_id = $('#st_filter').val();
                var pc_id = $('#pc_filter').val();
                var specific = $('#specific_filter').val();
                
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
                
                $('#pos_summary_date').val(hidden_range);
                $('#kt_dashboard_daterangepicker_date').html(range);
                $('#kt_dashboard_daterangepicker_title').html(title);
                
                renderOfflineTarget(hidden_range, label);
                renderOnlineTarget(hidden_range, label);
                renderCross(hidden_range);
                loadOnlineData();
                loadOfflineData();
                getPower(hidden_range, st_id, pc_id, specific);
            }
            
            picker.daterangepicker({
                startDate: start,
                endDate: end,
                opens: 'center',
                applyClass: 'btn-primary',
                cancelClass: 'btn-light-primary',
                locale: {
                    format: 'DD MMM YYYY',
                    separator: ' - ',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    fromLabel: 'Dari',
                    toLabel: 'Sampai',
                    customRangeLabel: 'Custom',
                    weekLabel: 'W',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    firstDay: 1
                },
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
        } else {
            console.error('Date range picker dependencies not loaded');
        }
    }
    
    // Sales detail modal
    $(document).on('click', '#sales_detail_btn', function() {
        var u_id = $(this).attr('data-u_id');
        var u_name = $(this).attr('data-u_name');
        $('#u_id').val(u_id);
        $('#sales_detail_label').text(u_name);
        document.getElementById('SalesDetailModal').classList.remove('hidden');
        setTimeout(function() {
            loadSalesDetailData();
        }, 100);
    });
    
    // Sales item detail modal
    $(document).on('click', '#sales_item_detail_btn', function() {
        var pt_id = $(this).attr('data-pt_id');
        var invoice = $(this).attr('data-invoice');
        $('#pt_id').val(pt_id);
        $('#sales_item_detail_label').text(invoice);
        document.getElementById('SalesItemDetailModal').classList.remove('hidden');
        setTimeout(function() {
            loadSalesItemDetailData();
        }, 100);
    });
    
    // Initialize on load - wait a bit for date picker to set initial date
    setTimeout(function() {
        loadOnlineData();
        loadOfflineData();
    }, 500);
});
</script>
