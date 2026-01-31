<script>
var summaryCurrentPage = 1;
var perPage = 25;
var leaveTypes = @json($data['leaveTypes']);

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadSummaryData(1);

    // Search with debounce
    var summarySearchTimeout;
    $('#summary_search').on('keyup', function() {
        clearTimeout(summarySearchTimeout);
        summarySearchTimeout = setTimeout(function() {
            loadSummaryData(1);
        }, 500);
    });

    // Date filter change handler - auto-load data
    $('#date_filter').on('change', function() {
        handleDateFilterChange(this.value);
        // Auto-load data when filter changes
        if (this.value !== 'custom') {
            loadSummaryData(1);
        }
    });
    
    // Date input change handler - auto-load data
    $('#start_date, #end_date').on('change', function() {
        if ($('#date_filter').val() === 'custom') {
            loadSummaryData(1);
        }
    });

    // Division filter change handler
    $('#division_id').on('change', function() {
        loadSummaryData(1);
    });
});

function loadSummaryData(page = 1) {
    summaryCurrentPage = page;
    var search = $('#summary_search').val();
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var dateFilter = $('#date_filter').val();
    var divisionId = $('#division_id').val();
    
    $.ajax({
        url: "{{ url('leave-requests/summary-report_v2/datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            start_date: startDate,
            end_date: endDate,
            date_filter: dateFilter,
            division_id: divisionId
        },
        success: function(response) {
            if (response.error) {
                $('#summary_tbody').html('<tr><td colspan="' + (9 + leaveTypes.length) + '" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#summary_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="' + (9 + leaveTypes.length) + '" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                var startIndex = (response.current_page - 1) * response.per_page;
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(startIndex + index + 1));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_nip || '-'));
                    
                    // Name column with clickable link
                    var nameTd = $('<td>').addClass('px-3 py-4');
                    if (row.user_id) {
                        var nameLink = $('<a>')
                            .attr('href', "{{ route('leave-requests_v2.staff-detail', '') }}/" + row.user_id)
                            .addClass('text-blue-700 font-medium hover:text-blue-900 hover:underline cursor-pointer')
                            .text(row.u_name || '-');
                        nameTd.append(nameLink);
                    } else {
                        nameTd.text(row.u_name || '-');
                    }
                    tr.append(nameTd);
                    tr.append($('<td>').addClass('px-3 py-4').text(row.position_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.division_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text((row.total_leave_requests && row.total_leave_requests != 0) ? row.total_leave_requests : '-'));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text((row.total_days && row.total_days != 0) ? row.total_days : '-'));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text((row.total_hours && row.total_hours != 0) ? row.total_hours : '-'));
                    tr.append($('<td>').addClass('px-3 py-4 text-center font-semibold').text((row.annual_leave_balance && row.annual_leave_balance != 0) ? row.annual_leave_balance : '-'));
                    
                    // Add leave type columns
                    leaveTypes.forEach(function(leaveType) {
                        var leaveKey = 'leave_' + leaveType.lt_code.toLowerCase();
                        var value = row[leaveKey] || 0;
                        tr.append($('<td>').addClass('px-3 py-4 text-center').text((value && value != 0) ? value : '-'));
                    });
                    
                    tbody.append(tr);
                });
            }

            updatePagination(response.total, response.current_page, response.per_page, response.last_page, 'summary');
        },
        error: function() {
            $('#summary_tbody').html('<tr><td colspan="' + (9 + leaveTypes.length) + '" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function updatePagination(total, currentPage, perPage, totalPages, prefix) {
    var start = total > 0 ? (currentPage - 1) * perPage + 1 : 0;
    var end = Math.min(currentPage * perPage, total);
    var infoText = total > 0 ? 'Menampilkan ' + start + ' sampai ' + end + ' dari ' + total + ' data' : 'Tidak ada data';

    $('#' + prefix + '_pagination_info').text(infoText);
    
    var controls = $('#' + prefix + '_pagination_controls');
    controls.empty();

    if (totalPages <= 1) return;

    // Previous button
    var prevBtn = $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (currentPage === 1 ? 'border-gray-200 text-gray-400 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .html('‹')
        .prop('disabled', currentPage === 1)
        .on('click', function() {
            if (currentPage > 1) {
                if (prefix === 'summary') loadSummaryData(currentPage - 1);
            }
        });
    controls.append(prevBtn);

    // Page numbers
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, currentPage + 2);

    // First page
    if (startPage > 1) {
        controls.append(createPageBtn(1, prefix, currentPage));
        if (startPage > 2) {
            controls.append($('<span>').addClass('px-2 text-sm text-gray-500').text('...'));
        }
    }

    // Middle pages
    for (var i = startPage; i <= endPage; i++) {
        controls.append(createPageBtn(i, prefix, currentPage));
    }

    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            controls.append($('<span>').addClass('px-2 text-sm text-gray-500').text('...'));
        }
        controls.append(createPageBtn(totalPages, prefix, currentPage));
    }

    // Next button
    var nextBtn = $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (currentPage === totalPages ? 'border-gray-200 text-gray-400 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .html('›')
        .prop('disabled', currentPage === totalPages)
        .on('click', function() {
            if (currentPage < totalPages) {
                if (prefix === 'summary') loadSummaryData(currentPage + 1);
            }
        });
    controls.append(nextBtn);
}

function createPageBtn(page, prefix, currentPage) {
    var isActive = page === summaryCurrentPage;
    var btn = $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'summary') loadSummaryData(page);
        });
    return btn;
}

function handleDateFilterChange(value) {
    const startDateContainer = $('#start_date_container');
    const endDateContainer = $('#end_date_container');
    const startDateInput = $('#start_date');
    const endDateInput = $('#end_date');
    
    if (value === 'custom') {
        startDateContainer.show();
        endDateContainer.show();
        // Don't auto-load for custom, let user select dates first
    } else {
        startDateContainer.hide();
        endDateContainer.hide();
        
        const today = new Date();
        let startDate, endDate;
        
        switch (value) {
            case 'this_week':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - today.getDay() + 1);
                endDate = new Date(today);
                endDate.setDate(today.getDate() - today.getDay() + 7);
                break;
            case 'past_week':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - today.getDay() - 6);
                endDate = new Date(today);
                endDate.setDate(today.getDate() - today.getDay());
                break;
            case 'next_week':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - today.getDay() + 8);
                endDate = new Date(today);
                endDate.setDate(today.getDate() - today.getDay() + 14);
                break;
            case 'this_month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'last_month':
                startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
            case 'next_month':
                startDate = new Date(today.getFullYear(), today.getMonth() + 1, 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 2, 0);
                break;
        }
        
        if (startDate && endDate) {
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            startDateInput.val(formatDate(startDate));
            endDateInput.val(formatDate(endDate));
        }
        
        // Auto-load data when filter changes (not custom)
        // This is already handled in the change event handler above
    }
}

function exportToExcel() {
    console.log('Export Excel clicked');
    const loadingIndicator = document.createElement('div');
    loadingIndicator.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">Loading...</span></div>';
    loadingIndicator.className = 'position-fixed top-50 start-50 translate-middle';
    document.body.appendChild(loadingIndicator);

    const form = document.getElementById('filterForm');
    if (!form) { console.error('Form not found'); return; }
    const formData = new FormData(form);
    let exportUrl = '{{ route("leave-requests.summary-report-export-excel") }}';
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    if (params.toString()) { exportUrl += '?' + params.toString(); }
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `leave_summary_${new Date().toISOString().split('T')[0]}.xlsx`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    setTimeout(() => {
        if (document.body.contains(loadingIndicator)) {
            document.body.removeChild(loadingIndicator);
        }
    }, 1000);
}

function exportToPDF() {
    console.log('Export PDF clicked');
    const loadingIndicator = document.createElement('div');
    loadingIndicator.innerHTML = 'Preparing PDF export...';
    loadingIndicator.style.cssText = 'position:fixed;top:20px;right:20px;background:#17a2b8;color:white;padding:10px;border-radius:5px;z-index:9999;';
    document.body.appendChild(loadingIndicator);
    
    const form = document.getElementById('filterForm');
    if (!form) {
        console.error('Form not found');
        return;
    }
    
    const formData = new FormData(form);
    let exportUrl = '{{ route("leave-requests.summary-report-export-pdf") }}';
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    if (params.toString()) {
        exportUrl += '?' + params.toString();
    }
    
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `leave_summary_${new Date().toISOString().split('T')[0]}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    setTimeout(() => {
        if (document.body.contains(loadingIndicator)) {
            document.body.removeChild(loadingIndicator);
        }
    }, 1000);
}
</script>
