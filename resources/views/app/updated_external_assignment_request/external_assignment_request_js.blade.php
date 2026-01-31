<script>
var externalAssignmentCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadExternalAssignmentData(1);
    updateStats();

    // Search with debounce
    var externalAssignmentSearchTimeout;
    $('#external_assignment_search').on('keyup', function() {
        clearTimeout(externalAssignmentSearchTimeout);
        externalAssignmentSearchTimeout = setTimeout(function() {
            loadExternalAssignmentData(1);
            updateStats();
        }, 500);
    });

    // Filter form submit
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadExternalAssignmentData(1);
        updateStats();
    });

    // Auto reload on filter change
    $('#date_filter, #user_id, #ea_id, #status, #start_date, #end_date').on('change', function() {
        loadExternalAssignmentData(1);
        updateStats();
    });
});

function loadExternalAssignmentData(page = 1) {
    externalAssignmentCurrentPage = page;
    var search = $('#external_assignment_search').val();
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var userId = $('#user_id').val();
    var status = $('#status').val();
    var eaId = $('#ea_id').val();
    
    $.ajax({
        url: "{{ url('external-assignment_v2/datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            start_date: startDate,
            end_date: endDate,
            user_id: userId,
            status: status,
            ea_id: eaId
        },
        success: function(response) {
            if (response.error) {
                $('#external_assignment_tbody').html('<tr><td colspan="10" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#external_assignment_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="10" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                var startIndex = (response.current_page - 1) * response.per_page;
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(startIndex + index + 1));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.request_date ? new Date(row.request_date).toLocaleDateString('id-ID') : '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text((row.u_name || '-') + (row.u_nip ? ' (' + row.u_nip + ')' : '')));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.division_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.assignment_type || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.assignment_area || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ear_date_start ? new Date(row.ear_date_start).toLocaleDateString('id-ID') : '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ear_date_end ? new Date(row.ear_date_end).toLocaleDateString('id-ID') : '-'));
                    
                    // Status
                    var statusBadge = getStatusBadge(row.ear_status);
                    tr.append($('<td>').addClass('px-3 py-4').html(statusBadge));
                    
                    // Action
                    var actionTd = $('<td>').addClass('px-3 py-4');
                    var actionLink = $('<a>')
                        .attr('href', "{{ url('external-assignment') }}/" + row.id)
                        .addClass('px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200')
                        .html('<i class="fas fa-eye mr-1"></i>View');
                    actionTd.append(actionLink);
                    tr.append(actionTd);
                    
                    tbody.append(tr);
                });
            }

            updatePagination(response.total, response.current_page, response.per_page, response.last_page, 'external_assignment');
        },
        error: function() {
            $('#external_assignment_tbody').html('<tr><td colspan="10" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function getStatusBadge(status) {
    var badges = {
        'Pending Approval': '<span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">Pending Approval</span>',
        'Approved': '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Approved</span>',
        'Rejected': '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Rejected</span>',
        'HR Check': '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">HR Check</span>',
        'Finance Process': '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">Finance Process</span>',
        'DONE': '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Done</span>'
    };
    return badges[status] || '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">' + (status || '-') + '</span>';
}

function updateStats() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var userId = $('#user_id').val();
    var status = $('#status').val();
    var eaId = $('#ea_id').val();
    
    $.ajax({
        url: "{{ url('external-assignment_v2/stats_simple') }}",
        type: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate,
            user_id: userId,
            status: status,
            ea_id: eaId
        },
        success: function(response) {
            $('#total-requests').text(response.total || 0);
            $('#pending-requests').text(response.pending || 0);
            $('#approved-requests').text(response.approved || 0);
            $('#rejected-requests').text(response.rejected || 0);
        },
        error: function() {
            console.error('Error loading stats');
        }
    });
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
                if (prefix === 'external_assignment') loadExternalAssignmentData(currentPage - 1);
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
                if (prefix === 'external_assignment') loadExternalAssignmentData(currentPage + 1);
            }
        });
    controls.append(nextBtn);
}

function createPageBtn(page, prefix, currentPage) {
    var isActive = page === externalAssignmentCurrentPage;
    return $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'external_assignment') loadExternalAssignmentData(page);
        });
}
</script>
