<script>
var overtimeCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadOvertimeData(1);

    // Search with debounce
    var overtimeSearchTimeout;
    $('#overtime_search').on('keyup', function() {
        clearTimeout(overtimeSearchTimeout);
        overtimeSearchTimeout = setTimeout(function() {
            loadOvertimeData(1);
        }, 500);
    });

    // Filter form submit
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadOvertimeData(1);
    });

    // Auto reload on filter change
    $('#status, #start_date, #end_date').on('change', function() {
        loadOvertimeData(1);
    });
});

function loadOvertimeData(page = 1) {
    overtimeCurrentPage = page;
    var search = $('#overtime_search').val();
    var status = $('#status').val();
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    
    $.ajax({
        url: "{{ url('overtime_v2/datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            status: status,
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            if (response.error) {
                $('#overtime_tbody').html('<tr><td colspan="12" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#overtime_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="12" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                var startIndex = (response.current_page - 1) * response.per_page;
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(startIndex + index + 1));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.submission_date ? new Date(row.submission_date).toLocaleDateString('id-ID') : '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.department_name || '-'));
                    
                    // Assigned Staff
                    var staffTd = $('<td>').addClass('px-3 py-4');
                    if (row.assigned_staff_names && row.assigned_staff_names.length > 0) {
                        var staffDiv = $('<div>').addClass('flex flex-wrap gap-1');
                        row.assigned_staff_names.forEach(function(name) {
                            staffDiv.append($('<span>').addClass('px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded').text(name));
                        });
                        staffTd.append(staffDiv);
                    } else {
                        staffTd.text('-');
                    }
                    tr.append(staffTd);
                    
                    // Start
                    var startStr = '-';
                    if (row.start_date && row.start_time) {
                        var startDate = new Date(row.start_date + 'T' + row.start_time);
                        startStr = startDate.toLocaleString('id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                    tr.append($('<td>').addClass('px-3 py-4').text(startStr));
                    
                    // End
                    var endStr = '-';
                    if (row.end_date && row.end_time) {
                        var endDate = new Date(row.end_date + 'T' + row.end_time);
                        endStr = endDate.toLocaleString('id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                    tr.append($('<td>').addClass('px-3 py-4').text(endStr));
                    
                    // Duration
                    var durationStr = '-';
                    if (row.duration_hours > 0 || row.duration_minutes > 0) {
                        durationStr = row.duration_hours + ' jam ' + row.duration_minutes + ' menit';
                    }
                    tr.append($('<td>').addClass('px-3 py-4').text(durationStr));
                    
                    tr.append($('<td>').addClass('px-3 py-4').text(row.claim || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.request_by_name || '-'));
                    
                    // Approver
                    var approverStr = '-';
                    if (row.approved_by) {
                        var approver = row.approved_by_name || 'Unknown';
                        var approvedAt = row.approved_at ? new Date(row.approved_at).toLocaleString('id-ID') : '';
                        approverStr = approver + (approvedAt ? '<br><small class="text-gray-500">' + approvedAt + '</small>' : '');
                    }
                    tr.append($('<td>').addClass('px-3 py-4').html(approverStr));
                    
                    // Status
                    var statusBadge = getStatusBadge(row.status);
                    tr.append($('<td>').addClass('px-3 py-4').html(statusBadge));
                    
                    // Action
                    var actionTd = $('<td>').addClass('px-3 py-4');
                    var actionLink = $('<a>')
                        .attr('href', "{{ url('overtime_v2') }}/" + row.id)
                        .addClass('px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200')
                        .html('<i class="fas fa-eye mr-1"></i>View');
                    actionTd.append(actionLink);
                    tr.append(actionTd);
                    
                    tbody.append(tr);
                });
            }

            updatePagination(response.total, response.current_page, response.per_page, response.last_page, 'overtime');
        },
        error: function() {
            $('#overtime_tbody').html('<tr><td colspan="12" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function getStatusBadge(status) {
    var badges = {
        'Pending': '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">Pending</span>',
        'Approved': '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Approved</span>',
        'Rejected': '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Rejected</span>',
        'HR Check': '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">HR Check</span>',
        'Done': '<span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded">Done</span>'
    };
    return badges[status] || '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">' + (status || '-') + '</span>';
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
                if (prefix === 'overtime') loadOvertimeData(currentPage - 1);
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
                if (prefix === 'overtime') loadOvertimeData(currentPage + 1);
            }
        });
    controls.append(nextBtn);
}

function createPageBtn(page, prefix, currentPage) {
    var isActive = page === overtimeCurrentPage;
    return $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'overtime') loadOvertimeData(page);
        });
}
</script>
