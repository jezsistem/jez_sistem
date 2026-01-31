@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
var currentPage = 1;
var perPage = 25;
var totalRecords = 0;
var totalPages = 0;
var poDate = '';

// ApexCharts initialization
const primary = '#072544';
const provinceChart = "#province_chart";
const dateChart = "#date_chart";

var provinceChart_options = {
    series: [],
    chart: {
        height: 350,
        type: 'area',
        zoom: { enabled: true }
    },
    dataLabels: { enabled: false },
    plotOptions: {
        bar: {
            borderRadius: 4,
            horizontal: true,
        }
    },
    grid: {
        row: {
            colors: ['#f3f3f3', 'transparent'],
            opacity: 0.5
        },
    },
    yaxis: {
        labels: {
            formatter: function(val) { return addCommas(val); },
            show: false
        },
    },
    colors: [primary]
};

var dateChart_options = {
    series: [],
    chart: {
        height: 350,
        type: 'area',
        zoom: { enabled: true }
    },
    dataLabels: { enabled: false },
    plotOptions: {
        bar: {
            borderRadius: 4,
            horizontal: true,
        }
    },
    grid: {
        row: {
            colors: ['#f3f3f3', 'transparent'],
            opacity: 0.5
        },
    },
    yaxis: {
        labels: {
            formatter: function(val) { return addCommas(val); },
            show: false
        },
    },
    colors: [primary]
};

var provinceChart_render = null;
var dateChart_render = null;

function initCharts() {
    if (document.querySelector(provinceChart)) {
        provinceChart_render = new ApexCharts(document.querySelector(provinceChart), provinceChart_options);
        provinceChart_render.render();
    }
    if (document.querySelector(dateChart)) {
        dateChart_render = new ApexCharts(document.querySelector(dateChart), dateChart_options);
        dateChart_render.render();
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

// ==================== CUSTOMER DATA ====================
function loadCustomerData(page = 1) {
    currentPage = page;
    
    $('#CustomerTableBody').html(`
        <tr>
            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                <div class="flex flex-col items-center">
                    <i class="fa fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i>
                    <span>Memuat data...</span>
                </div>
            </td>
        </tr>
    `);
    
    $.ajax({
        url: "{{ url('customer_datatables_simple') }}",
        type: "GET",
        data: {
            page: page,
            per_page: $('#per_page').val(),
            search: $('#customer_search').val(),
            po_date: poDate,
            stt_filter: $('#stt_filter').val(),
            cust_type_filter: $('#cust_type_filter').val(),
            date_filter: $('#date_filter').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                toastr.error(response.error, 'Error');
                $('#CustomerTableBody').html(`
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fa fa-exclamation-triangle text-3xl text-red-400 mb-2"></i>
                                <span>${response.error}</span>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }
            
            totalRecords = response.total;
            totalPages = response.total_pages;
            currentPage = response.current_page;
            perPage = response.per_page;
            
            renderCustomerTable(response.data);
            updatePagination();
        },
        error: function(xhr) {
            toastr.error('Gagal memuat data', 'Error');
            $('#CustomerTableBody').html(`
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fa fa-exclamation-triangle text-3xl text-red-400 mb-2"></i>
                            <span>Gagal memuat data</span>
                        </div>
                    </td>
                </tr>
            `);
        }
    });
}

function renderCustomerTable(data) {
    if (data.length === 0) {
        $('#CustomerTableBody').html(`
            <tr>
                <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i class="fa fa-inbox text-3xl text-gray-400 mb-2"></i>
                        <span>Tidak ada data customer</span>
                    </div>
                </td>
            </tr>
        `);
        return;
    }
    
    var html = '';
    data.forEach(function(row) {
        html += `
            <tr class="hover:bg-gray-50 cursor-pointer customer-row" 
                data-id="${row.cid}"
                data-ct_id="${row.ct_id || ''}"
                data-cust_name="${escapeHtml(row.cust_name || '')}"
                data-cust_store="${escapeHtml(row.cust_store || '')}"
                data-cust_phone="${escapeHtml(row.cust_phone || '')}"
                data-cust_email="${escapeHtml(row.cust_email || '')}"
                data-cust_username="${escapeHtml(row.cust_username || '')}"
                data-cust_province="${row.cust_province || ''}"
                data-cust_city="${row.cust_city || ''}"
                data-cust_subdistrict="${row.cust_subdistrict || ''}"
                data-cust_address="${escapeHtml(row.cust_address || '')}">
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${row.no}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${escapeHtml(row.cust_name)}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">${escapeHtml(row.ct_name)}</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">${escapeHtml(row.cust_store)}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">${escapeHtml(row.cust_phone)}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">${escapeHtml(row.cust_email)}</td>
                <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate" title="${escapeHtml(row.cust_address)}">${escapeHtml(row.cust_address)}</td>
                <td class="px-4 py-3 whitespace-nowrap shopping-cell">
                    <button class="px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shopping-btn" data-cust_id="${row.cid}">
                        ${row.cust_shopping}
                    </button>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">${row.cust_created}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Rp ${row.total_pembelian}</td>
            </tr>
        `;
    });
    
    $('#CustomerTableBody').html(html);
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '-';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

function updatePagination() {
    var start = ((currentPage - 1) * perPage) + 1;
    var end = Math.min(currentPage * perPage, totalRecords);
    
    if (totalRecords === 0) {
        start = 0;
        end = 0;
    }
    
    $('#showing_start').text(start);
    $('#showing_end').text(end);
    $('#total_records').text(totalRecords);
    
    // Generate numbered pagination
    var container = $('#pagination_container');
    container.empty();
    
    if (totalPages <= 1) return;
    
    // Previous button
    var prevBtn = $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (currentPage === 1 ? 'border-gray-200 text-gray-400 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .html('‹')
        .prop('disabled', currentPage === 1)
        .on('click', function() {
            if (currentPage > 1) loadCustomerData(currentPage - 1);
        });
    container.append(prevBtn);
    
    // Page numbers
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, currentPage + 2);
    
    // First page
    if (startPage > 1) {
        container.append(createPageBtn(1));
        if (startPage > 2) {
            container.append($('<span>').addClass('px-2 text-sm text-gray-500').text('...'));
        }
    }
    
    // Middle pages
    for (var i = startPage; i <= endPage; i++) {
        container.append(createPageBtn(i));
    }
    
    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            container.append($('<span>').addClass('px-2 text-sm text-gray-500').text('...'));
        }
        container.append(createPageBtn(totalPages));
    }
    
    // Next button
    var nextBtn = $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (currentPage === totalPages ? 'border-gray-200 text-gray-400 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .html('›')
        .prop('disabled', currentPage === totalPages)
        .on('click', function() {
            if (currentPage < totalPages) loadCustomerData(currentPage + 1);
        });
    container.append(nextBtn);
}

function createPageBtn(page) {
    var isActive = page === currentPage;
    return $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            loadCustomerData(page);
        });
}

// ==================== PROVINCE & CITY TABLES ====================
function loadProvinceData() {
    $.ajax({
        url: "{{ url('province_datatables') }}",
        type: "GET",
        data: {
            stt_filter: $('#stt_filter').val(),
            cust_type_filter: $('#cust_type_filter').val(),
            date_filter: $('#date_filter').val(),
            date: poDate
        },
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var html = '';
                response.data.forEach(function(row, idx) {
                    html += `
                        <tr class="hover:bg-gray-50 cursor-pointer province-row" data-kode="${row.kode}">
                            <td class="px-3 py-2">${idx + 1}</td>
                            <td class="px-3 py-2">
                                <button class="text-blue-600 hover:underline province-detail-btn" data-kode="${row.kode}">${row.nama}</button>
                            </td>
                            <td class="px-3 py-2">
                                <button class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded customer-detail-btn" data-kode="${row.kode}" data-type="province">${row.customer}</button>
                            </td>
                        </tr>
                    `;
                });
                $('#ProvinceTableBody').html(html);
            } else {
                $('#ProvinceTableBody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            }
        },
        error: function() {
            $('#ProvinceTableBody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">Gagal memuat</td></tr>');
        }
    });
}

function loadCityRankData() {
    $.ajax({
        url: "{{ url('city_rank_datatables') }}",
        type: "GET",
        data: {
            stt_filter: $('#stt_filter').val(),
            cust_type_filter: $('#cust_type_filter').val(),
            date_filter: $('#date_filter').val(),
            date: poDate
        },
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var html = '';
                response.data.forEach(function(row, idx) {
                    html += `
                        <tr class="hover:bg-gray-50 cursor-pointer">
                            <td class="px-3 py-2">${idx + 1}</td>
                            <td class="px-3 py-2">
                                <button class="text-blue-600 hover:underline city-detail-btn" data-kode="${row.kode}">${row.nama}</button>
                            </td>
                            <td class="px-3 py-2">
                                <button class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded customer-detail-btn" data-kode="${row.kode}" data-type="city">${row.customer}</button>
                            </td>
                        </tr>
                    `;
                });
                $('#CityRankTableBody').html(html);
            } else {
                $('#CityRankTableBody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            }
        },
        error: function() {
            $('#CityRankTableBody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">Gagal memuat</td></tr>');
        }
    });
}

// ==================== CUSTOMER TYPE ====================
function loadCustomerTypeData() {
    $.ajax({
        url: "{{ url('customer_type_datatables') }}",
        type: "GET",
        data: {
            search: $('#customer_type_search').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var html = '';
                response.data.forEach(function(row, idx) {
                    html += `
                        <tr class="hover:bg-gray-50 cursor-pointer customer-type-row" data-id="${row.id}" data-ct_name="${escapeHtml(row.ct_name)}" data-ct_description="${escapeHtml(row.ct_description || '')}">
                            <td class="px-4 py-3 text-sm">${idx + 1}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">${escapeHtml(row.ct_name)}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(row.ct_description || '-')}</td>
                        </tr>
                    `;
                });
                $('#CustomerTypeTableBody').html(html);
            } else {
                $('#CustomerTypeTableBody').html('<tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            }
        },
        error: function() {
            $('#CustomerTypeTableBody').html('<tr><td colspan="3" class="px-4 py-4 text-center text-red-500">Gagal memuat</td></tr>');
        }
    });
}

function reloadCustomerType() {
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "{{ url('reload_customer_type') }}",
        success: function(r) {
            $('#ct_id').html(r);
        }
    });
}

// ==================== CITY & SUBDISTRICT HELPERS ====================
function reloadCity(province) {
    $.ajax({
        type: "GET",
        data: { _province: province },
        dataType: 'html',
        url: "{{ url('reload_city') }}",
        success: function(r) {
            $('#cust_city').html(r);
        }
    });
}

function reloadSubdistrict(city) {
    $.ajax({
        type: "GET",
        data: { _city: city },
        dataType: 'html',
        url: "{{ url('reload_subdistrict') }}",
        success: function(r) {
            $('#cust_subdistrict').html(r);
        }
    });
}

// ==================== GRAPH ====================
function loadGraph(stt_label, ct_id, date) {
    $('#GraphModal').removeClass('hidden');
    $('#chart').html('<div class="text-center py-8"><i class="fa fa-spinner fa-spin text-3xl text-gray-400"></i><p class="mt-2 text-gray-500">Memuat grafik...</p></div>');
    
    // Initialize charts after modal is visible (small delay to ensure DOM is ready)
    setTimeout(function() {
        if (!provinceChart_render && document.querySelector(provinceChart)) {
            provinceChart_render = new ApexCharts(document.querySelector(provinceChart), provinceChart_options);
            provinceChart_render.render();
        }
        if (!dateChart_render && document.querySelector(dateChart)) {
            dateChart_render = new ApexCharts(document.querySelector(dateChart), dateChart_options);
            dateChart_render.render();
        }
        
        $.ajax({
            type: 'POST',
            url: "{{ url('get_customer_graph') }}",
            data: {
                stt_label: stt_label,
                ct_id: ct_id,
                date: date
            },
            dataType: 'html',
            success: function(r) {
                $('#chart').html(r);
                window.dispatchEvent(new Event('resize'));
            },
            error: function() {
                $('#chart').html('<div class="text-center py-8 text-red-500">Gagal memuat grafik</div>');
            }
        });
    }, 100);
}

// ==================== DATE RANGE PICKER ====================
function initDateRangePicker() {
    var picker = $('#kt_dashboard_daterangepicker');
    if (picker.length == 0) {
        return;
    }
    
    var start = moment();
    var end = moment();

    function cb(start, end, label) {
        var title = '';
        var range = '';

        if ((end - start) < 100 || label == 'Hari Ini') {
            title = 'Hari Ini:';
            range = start.format('DD MMM YYYY');
            poDate = start.format('YYYY-MM-DD');
        } else if (label == 'Kemarin') {
            title = 'Kemarin:';
            range = start.format('DD MMM YYYY');
            poDate = start.format('YYYY-MM-DD');
        } else {
            title = '';
            range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
            poDate = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
        }

        $('#kt_dashboard_daterangepicker_date').html(range);
        $('#kt_dashboard_daterangepicker_title').html(title);
        $('#po_date').val(poDate);
        
        // Reload all data
        loadCustomerData(1);
        loadProvinceData();
        loadCityRankData();
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

    // Initialize with today's date
    cb(start, end, '');
}

// ==================== DOCUMENT READY ====================
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Initialize date range picker
    if (typeof $.fn.daterangepicker === 'function') {
        initDateRangePicker();
    } else {
        loadCustomerData(1);
        loadProvinceData();
        loadCityRankData();
    }
    
    // Load customer type data initially
    loadCustomerTypeData();
    
    // Search with debounce
    var searchTimeout;
    $('#customer_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadCustomerData(1);
        }, 300);
    });
    
    $('#customer_type_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadCustomerTypeData();
        }, 300);
    });
    
    // Per page change
    $('#per_page').on('change', function() {
        perPage = $(this).val();
        loadCustomerData(1);
    });
    
    // Filter changes
    $('#stt_filter, #cust_type_filter, #date_filter').on('change', function() {
        loadCustomerData(1);
        loadProvinceData();
        loadCityRankData();
    });
    
    // Toggle customer type section
    $('#toggle_customer_type_btn').on('click', function() {
        $('#customerTypeSection').toggleClass('hidden');
    });
    
    // Add customer button
    $('#add_customer_btn').on('click', function() {
        $('#_id').val('');
        $('#_mode').val('add');
        $('#f_customer')[0].reset();
        $('#delete_customer_btn').addClass('hidden');
        $('#CustomerModal').removeClass('hidden');
    });
    
    // Add customer type button
    $('#add_customer_type_btn').on('click', function() {
        $('#_id_ct').val('');
        $('#_mode_ct').val('add');
        $('#f_customer_type')[0].reset();
        $('#delete_customer_type_btn').addClass('hidden');
        $('#CustomerTypeModal').removeClass('hidden');
    });
    
    // Import button
    $('#import_btn').on('click', function() {
        $('#ImportModal').removeClass('hidden');
    });
    
    // Graph button
    $('#graph_btn').on('click', function(e) {
        e.preventDefault();
        if ($('#date_filter').val() == '0') {
            Swal.fire('Filter Tanggal', 'Silahkan aktifkan terlebih dahulu filter tanggal untuk menggunakan fitur ini', 'info');
            return false;
        }
        loadGraph($('#stt_filter').val(), $('#cust_type_filter').val(), poDate);
    });
    
    // Customer row click - open edit modal
    $(document).on('click', '.customer-row td:not(.shopping-cell)', function() {
        var row = $(this).closest('tr');
        var id = row.data('id');
        var ct_id = row.data('ct_id');
        var cust_name = row.data('cust_name');
        var cust_store = row.data('cust_store');
        var cust_phone = row.data('cust_phone');
        var cust_email = row.data('cust_email');
        var cust_username = row.data('cust_username');
        var cust_province = row.data('cust_province');
        var cust_city = row.data('cust_city');
        var cust_subdistrict = row.data('cust_subdistrict');
        var cust_address = row.data('cust_address');
        
        $('#_id').val(id);
        $('#_mode').val('edit');
        $('#ct_id').val(ct_id);
        $('#cust_name').val(cust_name);
        $('#cust_store').val(cust_store);
        $('#cust_phone').val(cust_phone);
        $('#cust_email').val(cust_email);
        $('#cust_username').val(cust_username);
        $('#cust_province').val(cust_province);
        
        if (cust_province) {
            reloadCity(cust_province);
            setTimeout(function() { $('#cust_city').val(cust_city); }, 500);
        }
        
        if (cust_city) {
            reloadSubdistrict(cust_city);
            setTimeout(function() { $('#cust_subdistrict').val(cust_subdistrict); }, 1000);
        }
        
        $('#cust_address').val(cust_address);
        
        @if ($data['user']->delete_access == '1')
            $('#delete_customer_btn').removeClass('hidden');
        @endif
        
        $('#CustomerModal').removeClass('hidden');
    });
    
    // Customer type row click
    $(document).on('click', '.customer-type-row', function() {
        var id = $(this).data('id');
        var ct_name = $(this).data('ct_name');
        var ct_description = $(this).data('ct_description');
        
        $('#_id_ct').val(id);
        $('#_mode_ct').val('edit');
        $('#ct_name_input').val(ct_name);
        $('#ct_description_input').val(ct_description);
        
        @if ($data['user']->g_name == 'administrator')
            $('#delete_customer_type_btn').removeClass('hidden');
        @endif
        
        $('#CustomerTypeModal').removeClass('hidden');
    });
    
    // Shopping button click
    $(document).on('click', '.shopping-btn', function(e) {
        e.stopPropagation();
        var custId = $(this).data('cust_id');
        $('#_cust_id').val(custId);
        loadCustomerTransactions(custId);
        $('#CustomerTransactionModal').removeClass('hidden');
    });
    
    // Province detail button
    $(document).on('click', '.province-detail-btn', function(e) {
        e.stopPropagation();
        var code = $(this).data('kode');
        $('#_province_code').val(code);
        loadCityByProvince(code);
        $('#CustomerCityModal').removeClass('hidden');
    });
    
    // City detail button
    $(document).on('click', '.city-detail-btn', function(e) {
        e.stopPropagation();
        var code = $(this).data('kode');
        $('#_city_code').val(code);
        loadSubdistrictByCity(code);
        $('#CustomerSubdistrictModal').removeClass('hidden');
    });
    
    // Customer detail button (by location)
    $(document).on('click', '.customer-detail-btn', function(e) {
        e.stopPropagation();
        var code = $(this).data('kode');
        var type = $(this).data('type');
        $('#_code').val(code);
        $('#_code_type').val(type);
        loadCustomerDetail(code, type);
        $('#CustomerDetailModal').removeClass('hidden');
    });
    
    // Province change
    $('#cust_province').on('change', function() {
        var province = $(this).val();
        if (province) reloadCity(province);
    });
    
    // City change
    $('#cust_city').on('change', function() {
        var city = $(this).val();
        if (city) reloadSubdistrict(city);
    });
    
    // Phone check
    $('#cust_phone').on('change', function() {
        var phone = $(this).val();
        var mode = $('#_mode').val();
        if (mode == 'add' && phone) {
            $.ajax({
                type: "POST",
                data: { _cust_phone: phone },
                dataType: 'json',
                url: "{{ url('check_exists_customer') }}",
                success: function(r) {
                    if (r.status == '200') {
                        Swal.fire('No Telepon', 'Nomor telepon sudah ada di sistem, silahkan ganti dengan yang lain', 'warning');
                        $('#cust_phone').val('');
                    }
                }
            });
        }
    });
    
    // Save customer form
    $('#f_customer').on('submit', function(e) {
        e.preventDefault();
        $("#save_customer_btn").html('<i class="fa fa-spinner fa-spin mr-2"></i>Proses...');
        $("#save_customer_btn").prop("disabled", true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('cust_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_customer_btn").html('Simpan');
                $("#save_customer_btn").prop("disabled", false);
                
                if (data.status == '200') {
                    $('#CustomerModal').addClass('hidden');
                    $('#f_customer')[0].reset();
                    toastr.success("Data berhasil disimpan", "Berhasil");
                    loadCustomerData(currentPage);
                } else {
                    toastr.warning("Data tidak tersimpan", "Gagal");
                }
            },
            error: function(xhr) {
                $("#save_customer_btn").html('Simpan');
                $("#save_customer_btn").prop("disabled", false);
                toastr.error("Terjadi kesalahan saat memproses permintaan", "Error");
            }
        });
    });
    
    // Save customer type form
    $('#f_customer_type').on('submit', function(e) {
        e.preventDefault();
        $("#save_customer_type_btn").html('<i class="fa fa-spinner fa-spin mr-2"></i>Proses...');
        $("#save_customer_type_btn").prop("disabled", true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('ct_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_customer_type_btn").html('Simpan');
                $("#save_customer_type_btn").prop("disabled", false);
                
                if (data.status == '200') {
                    $('#CustomerTypeModal').addClass('hidden');
                    $('#f_customer_type')[0].reset();
                    toastr.success("Data berhasil disimpan", "Berhasil");
                    loadCustomerTypeData();
                    reloadCustomerType();
                } else {
                    toastr.warning("Data tidak tersimpan", "Gagal");
                }
            },
            error: function() {
                $("#save_customer_type_btn").html('Simpan');
                $("#save_customer_type_btn").prop("disabled", false);
                toastr.error("Terjadi kesalahan saat memproses permintaan", "Error");
            }
        });
    });
    
    // Import form
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        $("#import_data_btn").html('<i class="fa fa-spinner fa-spin mr-2"></i>Proses...');
        $("#import_data_btn").prop("disabled", true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('cust_import') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").prop("disabled", false);
                
                if (data.status == '200') {
                    $('#ImportModal').addClass('hidden');
                    $('#f_import')[0].reset();
                    toastr.success("Data berhasil diimport", "Berhasil");
                    loadCustomerData(1);
                } else {
                    toastr.warning("Data gagal diimport", "Gagal");
                }
            },
            error: function() {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").prop("disabled", false);
                toastr.error("Terjadi kesalahan saat memproses permintaan", "Error");
            }
        });
    });
    
    // Delete customer
    $('#delete_customer_btn').on('click', function() {
        Swal.fire({
            title: 'Hapus..?',
            text: 'Yakin hapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: { _id: $('#_id').val() },
                    dataType: 'json',
                    url: "{{ url('cust_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toastr.success("Data berhasil dihapus", "Berhasil");
                            $('#CustomerModal').addClass('hidden');
                            loadCustomerData(currentPage);
                        } else {
                            toastr.error("Gagal menghapus data", "Gagal");
                        }
                    },
                    error: function() {
                        toastr.error("Terjadi kesalahan saat menghapus data", "Error");
                    }
                });
            }
        });
    });
    
    // Delete customer type
    $('#delete_customer_type_btn').on('click', function() {
        Swal.fire({
            title: 'Hapus..?',
            text: 'Yakin hapus tipe customer ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: { _id: $('#_id_ct').val() },
                    dataType: 'json',
                    url: "{{ url('ct_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toastr.success("Data berhasil dihapus", "Berhasil");
                            $('#CustomerTypeModal').addClass('hidden');
                            loadCustomerTypeData();
                            reloadCustomerType();
                        } else {
                            toastr.error("Gagal menghapus data", "Gagal");
                        }
                    },
                    error: function() {
                        toastr.error("Terjadi kesalahan saat menghapus data", "Error");
                    }
                });
            }
        });
    });
    
    // Close modal buttons
    $(document).on('click', '.close-modal', function() {
        $(this).closest('.modal-overlay').addClass('hidden');
    });
    
    // Close modal on overlay click
    $(document).on('click', '.modal-overlay', function(e) {
        if (e.target === this) {
            $(this).addClass('hidden');
        }
    });
    
    // Sales item detail button click
    $(document).on('click', '.sales-item-detail-btn', function(e) {
        e.stopPropagation();
        var ptId = $(this).data('pt_id');
        var invoice = $(this).text();
        $('#pt_id').val(ptId);
        $('#sales_item_detail_label').text(invoice);
        loadSalesItemDetail(ptId);
        $('#SalesItemDetailModal').removeClass('hidden');
    });
});

// ==================== ADDITIONAL LOAD FUNCTIONS ====================
function loadCustomerTransactions(custId) {
    $('#TransactionTableBody').html('<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat...</td></tr>');
    
    $.ajax({
        url: "{{ url('customer_transaction_datatables') }}",
        type: "GET",
        data: { cust_id: custId },
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var html = '';
                response.data.forEach(function(row, idx) {
                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">${idx + 1}</td>
                            <td class="px-4 py-3 text-sm">${row.pos_created}</td>
                            <td class="px-4 py-3 text-sm">
                                <button class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded sales-item-detail-btn" data-pt_id="${row.pt_id}">${row.pos_invoice}</button>
                            </td>
                            <td class="px-4 py-3 text-sm">${row.qty}</td>
                            <td class="px-4 py-3 text-sm font-medium">Rp ${row.total}</td>
                        </tr>
                    `;
                });
                $('#TransactionTableBody').html(html);
            } else {
                $('#TransactionTableBody').html('<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada transaksi</td></tr>');
            }
        },
        error: function() {
            $('#TransactionTableBody').html('<tr><td colspan="5" class="px-4 py-8 text-center text-red-500">Gagal memuat</td></tr>');
        }
    });
}

function loadCityByProvince(provinceCode) {
    $('#CityTableBody').html('<tr><td colspan="3" class="px-4 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin"></i> Memuat...</td></tr>');
    
    $.ajax({
        url: "{{ url('city_datatables') }}",
        type: "GET",
        data: {
            province_code: provinceCode,
            stt_filter: $('#stt_filter').val(),
            cust_type_filter: $('#cust_type_filter').val(),
            date_filter: $('#date_filter').val(),
            date: poDate
        },
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var html = '';
                response.data.forEach(function(row, idx) {
                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">${idx + 1}</td>
                            <td class="px-4 py-3 text-sm">
                                <button class="text-blue-600 hover:underline city-detail-btn" data-kode="${row.kode}">${row.nama}</button>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <button class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded customer-detail-btn" data-kode="${row.kode}" data-type="city">${row.customer}</button>
                            </td>
                        </tr>
                    `;
                });
                $('#CityTableBody').html(html);
            } else {
                $('#CityTableBody').html('<tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            }
        }
    });
}

function loadSubdistrictByCity(cityCode) {
    $('#SubdistrictTableBody').html('<tr><td colspan="3" class="px-4 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin"></i> Memuat...</td></tr>');
    
    $.ajax({
        url: "{{ url('subdistrict_datatables') }}",
        type: "GET",
        data: {
            city_code: cityCode,
            stt_filter: $('#stt_filter').val(),
            cust_type_filter: $('#cust_type_filter').val(),
            date_filter: $('#date_filter').val(),
            date: poDate
        },
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var html = '';
                response.data.forEach(function(row, idx) {
                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">${idx + 1}</td>
                            <td class="px-4 py-3 text-sm">${row.nama}</td>
                            <td class="px-4 py-3 text-sm">
                                <button class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded customer-detail-btn" data-kode="${row.kode}" data-type="subdistrict">${row.customer}</button>
                            </td>
                        </tr>
                    `;
                });
                $('#SubdistrictTableBody').html(html);
            } else {
                $('#SubdistrictTableBody').html('<tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            }
        }
    });
}

function loadCustomerDetail(code, type) {
    $('#CustomerDetailTableBody').html('<tr><td colspan="9" class="px-4 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin"></i> Memuat...</td></tr>');
    
    $.ajax({
        url: "{{ url('customer_detail_datatables') }}",
        type: "GET",
        data: {
            code: code,
            code_type: type,
            stt_filter: $('#stt_filter').val(),
            cust_type_filter: $('#cust_type_filter').val(),
            date_filter: $('#date_filter').val(),
            date: poDate
        },
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var html = '';
                response.data.forEach(function(row, idx) {
                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">${idx + 1}</td>
                            <td class="px-4 py-3 text-sm font-medium">${row.cust_name}</td>
                            <td class="px-4 py-3 text-sm">${row.ct_name || '-'}</td>
                            <td class="px-4 py-3 text-sm">${row.cust_store || '-'}</td>
                            <td class="px-4 py-3 text-sm">${row.cust_phone || '-'}</td>
                            <td class="px-4 py-3 text-sm">${row.cust_email || '-'}</td>
                            <td class="px-4 py-3 text-sm">${row.cust_address_show || '-'}</td>
                            <td class="px-4 py-3 text-sm">${row.cust_shopping_show || '0'}</td>
                            <td class="px-4 py-3 text-sm">${row.cust_created}</td>
                        </tr>
                    `;
                });
                $('#CustomerDetailTableBody').html(html);
            } else {
                $('#CustomerDetailTableBody').html('<tr><td colspan="9" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            }
        }
    });
}

function loadSalesItemDetail(ptId) {
    $('#SalesItemDetailTableBody').html('<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat...</td></tr>');
    
    $.ajax({
        url: "{{ url('sales_item_detail_datatables') }}",
        type: "GET",
        data: { pt_id: ptId },
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var html = '';
                response.data.forEach(function(row, idx) {
                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">${idx + 1}</td>
                            <td class="px-4 py-3 text-sm">${row.article || '-'}</td>
                            <td class="px-4 py-3 text-sm">${row.pos_td_qty}</td>
                            <td class="px-4 py-3 text-sm font-medium">Rp ${row.pos_td_total_price}</td>
                            <td class="px-4 py-3 text-sm">${row.created_at}</td>
                        </tr>
                    `;
                });
                $('#SalesItemDetailTableBody').html(html);
            } else {
                $('#SalesItemDetailTableBody').html('<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td></tr>');
            }
        },
        error: function() {
            $('#SalesItemDetailTableBody').html('<tr><td colspan="5" class="px-4 py-8 text-center text-red-500">Gagal memuat</td></tr>');
        }
    });
}
</script>
@endpush
