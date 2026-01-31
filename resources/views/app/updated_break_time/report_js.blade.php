<script>
var breakTimeCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadBreakTimeData(1);
    updateBreakTimeStats();

    // Search with debounce
    var searchTimeout;
    $('#break_time_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadBreakTimeData(1);
        }, 500);
    });

    // Filter form submit
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadBreakTimeData(1);
        updateBreakTimeStats();
    });

    // Auto reload on filter change
    $('#date_filter, #user_id, #division_id, #status, #start_date, #end_date').on('change', function() {
        loadBreakTimeData(1);
        updateBreakTimeStats();
    });
});

function loadBreakTimeData(page = 1) {
    breakTimeCurrentPage = page;
    var search = $('#break_time_search').val();
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var dateFilter = $('#date_filter').val();
    var userId = $('#user_id').val();
    var divisionId = $('#division_id').val();
    var status = $('#status').val();
    
    $.ajax({
        url: "{{ route('break-times.datatables_simple_v2') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            start_date: startDate,
            end_date: endDate,
            date_filter: dateFilter,
            user_id: userId,
            division_id: divisionId,
            status: status
        },
        success: function(response) {
            if (response.error) {
                $('#break_time_tbody').html('<tr><td colspan="10" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#break_time_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="10" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                var startIndex = (response.current_page - 1) * response.per_page;
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(startIndex + index + 1));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.bt_date ? new Date(row.bt_date).toLocaleDateString('id-ID') : '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ud_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.bt_start_time ? new Date('1970-01-01T' + row.bt_start_time).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '-'));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.bt_end_time ? new Date('1970-01-01T' + row.bt_end_time).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '-'));
                    
                    // Duration
                    var duration = row.bt_duration_minutes;
                    if ((!duration || duration == 0) && row.bt_start_time && row.bt_end_time) {
                        try {
                            var start = new Date('1970-01-01T' + row.bt_start_time);
                            var end = new Date('1970-01-01T' + row.bt_end_time);
                            duration = Math.floor((end - start) / 60000);
                        } catch(e) {
                            duration = 0;
                        }
                    }
                    var durationText = '-';
                    if (duration > 0) {
                        var hours = Math.floor(duration / 60);
                        var minutes = duration % 60;
                        durationText = sprintf('%02d:%02d', hours, minutes);
                    }
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(durationText));
                    
                    // Type
                    var typeBadge = row.bt_type === 'break_1' 
                        ? '<span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">Break 1</span>'
                        : '<span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded">Break 2</span>';
                    tr.append($('<td>').addClass('px-3 py-4 text-center').html(typeBadge));
                    
                    // Status
                    var statusBadge = getStatusBadge(row.bt_status);
                    tr.append($('<td>').addClass('px-3 py-4 text-center').html(statusBadge));
                    
                    // Action
                    var actionTd = $('<td>').addClass('px-3 py-4 text-center');
                    var actionLink = $('<a>')
                        .attr('href', "{{ url('break-times_v2') }}/" + row.id)
                        .addClass('px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200')
                        .html('<i class="fas fa-eye mr-1"></i>View');
                    actionTd.append(actionLink);
                    tr.append(actionTd);
                    
                    tbody.append(tr);
                });
            }

            updatePagination(response.total, response.current_page, response.per_page, response.last_page, 'break_time');
        },
        error: function() {
            $('#break_time_tbody').html('<tr><td colspan="10" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function getStatusBadge(status) {
    var badges = {
        'active': '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">Active</span>',
        'completed': '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Completed</span>',
        'cancelled': '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Cancelled</span>'
    };
    return badges[status] || '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">' + (status || '-') + '</span>';
}

function handleDateFilterChange(value) {
    const startDateContainer = $('#start_date_container');
    const endDateContainer = $('#end_date_container');
    const startDateInput = $('#start_date');
    const endDateInput = $('#end_date');
    
    if (value === 'custom') {
        startDateContainer.show();
        endDateContainer.show();
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
            case 'this_month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'last_month':
                startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                endDate = new Date(today.getFullYear(), today.getMonth(), 0);
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
    }
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
                if (prefix === 'break_time') loadBreakTimeData(currentPage - 1);
            }
        });
    controls.append(prevBtn);

    // Page numbers
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        controls.append(createPageBtn(1, prefix, currentPage));
        if (startPage > 2) {
            controls.append($('<span>').addClass('px-2 text-sm text-gray-500').text('...'));
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        controls.append(createPageBtn(i, prefix, currentPage));
    }

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
                if (prefix === 'break_time') loadBreakTimeData(currentPage + 1);
            }
        });
    controls.append(nextBtn);
}

function createPageBtn(page, prefix, currentPage) {
    var isActive = page === breakTimeCurrentPage;
    return $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'break_time') loadBreakTimeData(page);
        });
}

function updateBreakTimeStats() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var dateFilter = $('#date_filter').val();
    var userId = $('#user_id').val();
    var divisionId = $('#division_id').val();
    var status = $('#status').val();
    
    $.ajax({
        url: "{{ route('break-times.stats_simple_v2') }}",
        type: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate,
            date_filter: dateFilter,
            user_id: userId,
            division_id: divisionId,
            status: status
        },
        success: function(response) {
            // Update status stats
            response.status_stats.forEach(function(stat) {
                $('#stat-' + stat.bt_status + '-count').text(stat.total);
            });
            
            // Update totals
            $('#stat-total-count').text(response.total || 0);
            $('#stat-break1-count').text(response.break_1 || 0);
            $('#stat-break2-count').text(response.break_2 || 0);
        },
        error: function() {
            console.error('Error loading stats');
        }
    });
}

function exportToExcel() {
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();
    const dateFilter = $('#date_filter').val();
    const userId = $('#user_id').val();
    const divisionId = $('#division_id').val();
    const status = $('#status').val();
    const search = $('#break_time_search').val();
    
    const params = new URLSearchParams();
    if (dateFilter && dateFilter !== 'custom') {
        params.append('date_filter', dateFilter);
    } else if (dateFilter === 'custom') {
        params.append('date_filter', 'custom');
    }
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (userId) params.append('user_id', userId);
    if (divisionId) params.append('division_id', divisionId);
    if (status) params.append('status', status);
    if (search) params.append('search', search);
    
    const url = "{{ route('break-times.export-excel') }}?" + params.toString();
    
    // Show loading indicator
    const exportBtn = event.target;
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
    exportBtn.disabled = true;
    
    // Use fetch to handle the download
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.blob();
        })
        .then(blob => {
            if (blob.size === 0) {
                throw new Error('Empty file received');
            }
            
            // Create download link
            const downloadUrl = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = downloadUrl;
            a.download = 'break_times_report.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(downloadUrl);
            document.body.removeChild(a);
            
            // Reset button
            exportBtn.innerHTML = originalText;
            exportBtn.disabled = false;
        })
        .catch(error => {
            console.error('Error exporting to Excel:', error);
            alert('Error exporting to Excel: ' + error.message);
            
            // Reset button
            exportBtn.innerHTML = originalText;
            exportBtn.disabled = false;
        });
}

function exportToPDF() {
    const url = new URL('{{ route("break-times.export-pdf") }}');
    
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();
    const dateFilter = $('#date_filter').val();
    const userId = $('#user_id').val();
    const divisionId = $('#division_id').val();
    const status = $('#status').val();
    
    if (dateFilter && dateFilter !== 'custom') {
        url.searchParams.append('date_filter', dateFilter);
    } else if (dateFilter === 'custom') {
        url.searchParams.append('date_filter', 'custom');
    }
    if (startDate) url.searchParams.append('start_date', startDate);
    if (endDate) url.searchParams.append('end_date', endDate);
    if (userId) url.searchParams.append('user_id', userId);
    if (divisionId) url.searchParams.append('division_id', divisionId);
    if (status) url.searchParams.append('status', status);
    
    window.location.href = url.toString();
}

function sprintf(format) {
    var args = Array.prototype.slice.call(arguments, 1);
    var i = 0;
    return format.replace(/%[sdj%]/g, function(match) {
        if (match === '%%') return '%';
        var type = match.charAt(1);
        var arg = args[i++];
        if (type === 's') return String(arg);
        if (type === 'd') return Number(arg);
        if (type === 'j') return JSON.stringify(arg);
        return match;
    });
}
</script>
