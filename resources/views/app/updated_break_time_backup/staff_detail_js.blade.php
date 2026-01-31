<script>
var staffBreakTimeCurrentPage = 1;
var perPage = 25;
var userId = {{ $data['staff']->id }};

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadStaffBreakTimeData(1);
    updateStaffStats();

    // Search with debounce
    var searchTimeout;
    $('#staff_break_time_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadStaffBreakTimeData(1);
        }, 500);
    });

    // Filter form submit
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadStaffBreakTimeData(1);
        updateStaffStats();
    });

    // Auto reload on filter change
    $('#start_date, #end_date, #status').on('change', function() {
        loadStaffBreakTimeData(1);
        updateStaffStats();
    });
});

function loadStaffBreakTimeData(page = 1) {
    staffBreakTimeCurrentPage = page;
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var status = $('#status').val();
    
    $.ajax({
        url: "{{ url('break-times-backup/staff_v2') }}/" + userId + "/datatables_simple",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            start_date: startDate,
            end_date: endDate,
            status: status
        },
        success: function(response) {
            if (response.error) {
                $('#staff_break_time_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#staff_break_time_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="9" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                var startIndex = (response.current_page - 1) * response.per_page;
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(startIndex + index + 1));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.bt_date ? new Date(row.bt_date).toLocaleDateString('id-ID') : '-'));
                    
                    // Type
                    var typeBadge = row.bt_type === 'break_1' 
                        ? '<span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">Break 1</span>'
                        : '<span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded">Break 2</span>';
                    tr.append($('<td>').addClass('px-3 py-4 text-center').html(typeBadge));
                    
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.bt_start_time ? new Date('1970-01-01T' + row.bt_start_time).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '-'));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.bt_end_time ? new Date('1970-01-01T' + row.bt_end_time).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '-'));
                    
                    // Duration
                    var duration = row.bt_duration_minutes || 0;
                    var durationText = '-';
                    if (duration > 0) {
                        var hours = Math.floor(duration / 60);
                        var minutes = duration % 60;
                        durationText = sprintf('%02d:%02d', hours, minutes);
                    }
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(durationText));
                    
                    // Status
                    var statusBadge = getStatusBadge(row.bt_status);
                    tr.append($('<td>').addClass('px-3 py-4 text-center').html(statusBadge));
                    
                    tr.append($('<td>').addClass('px-3 py-4').text(row.bt_notes || '-'));
                    
                    // Action
                    var actionTd = $('<td>').addClass('px-3 py-4 text-center');
                    var actionLink = $('<a>')
                        .attr('href', "{{ url('break-times-backup_v2') }}/" + row.id)
                        .addClass('px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200')
                        .html('<i class="fas fa-eye mr-1"></i>View');
                    actionTd.append(actionLink);
                    tr.append(actionTd);
                    
                    tbody.append(tr);
                });
            }

            updatePagination(response.total, response.current_page, response.per_page, response.last_page, 'staff_break_time');
        },
        error: function() {
            $('#staff_break_time_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
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

function updateStaffStats() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    
    $.ajax({
        url: "{{ url('break-times-backup/staff_v2') }}/" + userId + "/stats_simple",
        type: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            $('#stat-total-breaks').text(response.total_breaks || 0);
            $('#stat-active-breaks').text(response.active_breaks || 0);
            $('#stat-completed-breaks').text(response.completed_breaks || 0);
            $('#stat-cancelled-breaks').text(response.cancelled_breaks || 0);
            $('#stat-break-1-count').text(response.break_1_count || 0);
            $('#stat-break-2-count').text(response.break_2_count || 0);
        },
        error: function() {
            console.error('Error loading stats');
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
                if (prefix === 'staff_break_time') loadStaffBreakTimeData(currentPage - 1);
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
                if (prefix === 'staff_break_time') loadStaffBreakTimeData(currentPage + 1);
            }
        });
    controls.append(nextBtn);
}

function createPageBtn(page, prefix, currentPage) {
    var isActive = page === staffBreakTimeCurrentPage;
    return $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'staff_break_time') loadStaffBreakTimeData(page);
        });
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

function exportToExcel() {
    const url = new URL('{{ route("break-times-backup.staff-export-excel", $data["staff"]->id) }}');
    
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();
    const status = $('#status').val();
    
    if (startDate) {
        url.searchParams.append('start_date', startDate);
    }
    if (endDate) {
        url.searchParams.append('end_date', endDate);
    }
    if (status) {
        url.searchParams.append('status', status);
    }
    
    const link = document.createElement('a');
    link.href = url.toString();
    link.download = 'backup_time_staff_{{ $data["staff"]->u_nip }}_export.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToPDF() {
    const url = new URL('{{ route("break-times-backup.staff-export-pdf", $data["staff"]->id) }}');
    
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();
    const status = $('#status').val();
    
    if (startDate) {
        url.searchParams.append('start_date', startDate);
    }
    if (endDate) {
        url.searchParams.append('end_date', endDate);
    }
    if (status) {
        url.searchParams.append('status', status);
    }
    
    window.location.href = url.toString();
}
</script>
