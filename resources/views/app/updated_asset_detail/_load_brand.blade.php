<!-- Summary Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
        <div class="text-sm font-medium mb-1 opacity-90">Beginning</div>
        <div class="text-lg font-bold" id="beginning_qty">calculating...</div>
        <div class="text-sm font-medium" id="beginning_value">calculating...</div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
        <div class="text-sm font-medium mb-1 opacity-90">COGS</div>
        <div class="text-lg font-bold" id="cogs_qty">calculating...</div>
        <div class="text-sm font-medium" id="cogs_value">calculating...</div>
    </div>
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-4 text-white">
        <div class="text-sm font-medium mb-1 opacity-90">Purchase</div>
        <div class="text-lg font-bold" id="purchase_qty">calculating...</div>
        <div class="text-sm font-medium" id="purchase_value">calculating...</div>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white">
        <div class="text-sm font-medium mb-1 opacity-90">Sales</div>
        <div class="text-lg font-bold" id="sales_qty">calculating...</div>
        <div class="text-sm font-medium" id="sales_value">calculating...</div>
    </div>
    <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg p-4 text-white">
        <div class="text-sm font-medium mb-1 opacity-90">Profit</div>
        <div class="text-lg font-bold" id="profit_qty">calculating...</div>
        <div class="text-sm font-medium" id="profit_value">calculating...</div>
    </div>
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-4 text-white">
        <div class="text-sm font-medium mb-1 opacity-90">Ending</div>
        <div class="text-lg font-bold" id="ending_qty">calculating...</div>
        <div class="text-sm font-medium" id="ending_value">calculating...</div>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg p-4 text-white">
        <div class="text-sm font-medium mb-1 opacity-90">Transfer In</div>
        <div class="text-lg font-bold" id="transin_qty">calculating...</div>
        <div class="text-sm font-medium" id="transin_value">calculating...</div>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-red-500 rounded-lg p-4 text-white">
        <div class="text-sm font-medium mb-1 opacity-90">Transfer Out</div>
        <div class="text-lg font-bold" id="transout_qty">calculating...</div>
        <div class="text-sm font-medium" id="transout_value">calculating...</div>
    </div>
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-4 text-white">
        <div class="text-sm font-medium mb-1 opacity-90">GID</div>
        <div class="text-lg font-bold" id="gid_qty">calculating...</div>
        <div class="text-sm font-medium" id="gid_value">calculating...</div>
    </div>
    <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg p-4 text-white">
        <div class="text-sm font-medium mb-1 opacity-90">GIT</div>
        <div class="text-lg font-bold" id="git_qty">calculating...</div>
        <div class="text-sm font-medium" id="git_value">calculating...</div>
    </div>
</div>

<!-- Search and Export -->
<div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-4">
    <div class="w-full sm:w-1/2">
        <input type="search" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" id="data_search" placeholder="Cari brand..."/>
    </div>
    <div class="flex items-center gap-2">
        <select id="per_page" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            <option value="10">10</option>
            <option value="25" selected>25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <button type="button" id="export_excel_btn" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
            <i class="fas fa-file-excel mr-2"></i>Export Excel
        </button>
    </div>
</div>

<!-- Data Table -->
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="Datatb">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beginning Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beginning</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TransIn Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TransIn</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TransOut Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TransOut</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GID Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GIT Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GIT</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">COGS</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ending Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ending</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="DatatbBody">
                <tr>
                    <td colspan="20" class="px-4 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fa fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i>
                            <span>Memuat data...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="bg-white px-4 py-3 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center">
        <div class="text-sm text-gray-700 mb-2 sm:mb-0">
            Menampilkan <span id="showing_start">0</span> sampai <span id="showing_end">0</span> dari <span id="total_records">0</span> data
        </div>
        <div id="pagination_container" class="flex items-center gap-1"></div>
    </div>
</div>

<script>
    var currentPage = 1;
    var perPage = 25;
    var totalRecords = 0;
    var totalPages = 0;

    function reloadSummary() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {start:'{{ $dt['start'] }}', end:'{{ $dt['end'] }}', st_id:'{{ $dt['st_id'] }}'},
            dataType: 'json',
            url: "{{ url('get_asset_sales_summaries') }}",
            success: function(r) {
                if (r.status == 200){
                    $('#beginning_qty').text(r.beginning_qty);
                    $('#beginning_value').text('Rp ' + r.beginning_value);
                    $('#cogs_qty').text(r.cogs_qty + ' %');
                    $('#cogs_value').text('Rp ' + r.cogs_value);
                    $('#purchase_qty').text(r.purchase_qty);
                    $('#purchase_value').text('Rp ' + r.purchase_value);
                    $('#sales_qty').text(r.sales_qty);
                    $('#sales_value').text('Rp ' + r.sales_value);
                    $('#profit_qty').text(r.profit_qty);
                    $('#profit_value').text('Rp ' + r.profit_value);
                    $('#ending_qty').text(r.ending_qty);
                    $('#ending_value').text('Rp ' + r.ending_value);
                    $('#transin_qty').text(r.transin_qty);
                    $('#transin_value').text('Rp ' + r.transin_value);
                    $('#transout_qty').text(r.transout_qty);
                    $('#transout_value').text('Rp ' + r.transout_value);
                    $('#gid_qty').text(r.gid_qty);
                    $('#gid_value').text('Rp ' + r.gid_value);
                    $('#git_qty').text(r.git_qty);
                    $('#git_value').text('Rp ' + r.git_value);
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat summary',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert('Gagal memuat summary');
                    }
                }
            }
        });
    }

    function loadBrandData(page = 1) {
        currentPage = page;
        
        $('#DatatbBody').html(`
            <tr>
                <td colspan="20" class="px-4 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i class="fa fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i>
                        <span>Memuat data...</span>
                    </div>
                </td>
            </tr>
        `);
        
        $.ajax({
            url: "{{ url('ad_brand_datatables_simple') }}",
            type: "GET",
            data: {
                page: page,
                per_page: $('#per_page').val(),
                search: $('#data_search').val(),
                st_id: '{{ $dt['st_id'] }}',
                starts: '{{ $dt['start'] }}',
                ends: '{{ $dt['end'] }}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    $('#DatatbBody').html(`
                        <tr>
                            <td colspan="20" class="px-4 py-8 text-center text-red-500">
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
                
                renderBrandTable(response.data);
                updatePagination();
            },
            error: function(xhr) {
                var errorMsg = 'Terjadi kesalahan saat memuat data';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                $('#DatatbBody').html(`
                    <tr>
                        <td colspan="20" class="px-4 py-8 text-center text-red-500">
                            <div class="flex flex-col items-center">
                                <i class="fa fa-exclamation-triangle text-3xl text-red-400 mb-2"></i>
                                <span>${errorMsg}</span>
                            </div>
                        </td>
                    </tr>
                `);
            }
        });
    }

    function renderBrandTable(data) {
        if (!data || data.length === 0) {
            $('#DatatbBody').html(`
                <tr>
                    <td colspan="20" class="px-4 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fa fa-inbox text-3xl text-gray-400 mb-2"></i>
                            <span>Tidak ada data</span>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }
        
        var html = '';
        data.forEach(function(row) {
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">${row.no}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${escapeHtml(row.br_name)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatNumber(row.beginning)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatCurrency(row.beginning_value)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatNumber(row.purchase_qty)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatCurrency(row.purchase)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatNumber(row.transin_qty)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatCurrency(row.transin)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatNumber(row.transout_qty)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatCurrency(row.transout)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatNumber(row.gid_qty)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatCurrency(row.gid)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatNumber(row.git_qty)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatCurrency(row.git)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatNumber(row.sales_qty)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatCurrency(row.sales)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatCurrency(row.profit)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatCurrency(row.cogs)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatNumber(row.ending_qty)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-right">${formatCurrency(row.ending)}</td>
                </tr>
            `;
        });
        
        $('#DatatbBody').html(html);
    }

    function escapeHtml(text) {
        if (text === null || text === undefined) return '-';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function formatNumber(num) {
        if (num === null || num === undefined) return '0';
        return parseFloat(num).toLocaleString('id-ID');
    }

    function formatCurrency(num) {
        if (num === null || num === undefined) return 'Rp 0';
        return 'Rp ' + parseFloat(num).toLocaleString('id-ID');
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
        
        var container = $('#pagination_container');
        container.empty();
        
        if (totalPages <= 1) return;
        
        // Previous button
        var prevBtn = $('<button>')
            .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (currentPage === 1 ? 'border-gray-200 text-gray-400 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
            .html('‹')
            .prop('disabled', currentPage === 1)
            .on('click', function() {
                if (currentPage > 1) loadBrandData(currentPage - 1);
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
                if (currentPage < totalPages) loadBrandData(currentPage + 1);
            });
        container.append(nextBtn);
    }

    function createPageBtn(page) {
        var isActive = page === currentPage;
        return $('<button>')
            .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-red-500 text-white border-red-500' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
            .text(page)
            .on('click', function() {
                loadBrandData(page);
            });
    }

    $(document).ready(function() {
        reloadSummary();
        loadBrandData(1);
        
        // Search with debounce
        var searchTimeout;
        $('#data_search').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                loadBrandData(1);
            }, 500);
        });
        
        // Per page change
        $('#per_page').on('change', function() {
            loadBrandData(1);
        });
        
        // Export Excel
        $('#export_excel_btn').on('click', function() {
            var st_id = '{{ $dt['st_id'] }}';
            var data = 'brand';
            var article = '';
            var date = '{{ $dt['start'] }}';
            if ('{{ $dt['end'] }}') {
                date += '|{{ $dt['end'] }}';
            }
            
            var exportUrl = "{{ url('ad_export') }}?st_id=" + st_id + "&data_filter=" + data + "&article_filter=" + article + "&date=" + date;
            window.location.href = exportUrl;
        });
    });
</script>
