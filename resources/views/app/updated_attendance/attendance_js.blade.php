<script>
var attendanceCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadAttendanceData(1);
    updateStats();

    // Search with debounce
    var attendanceSearchTimeout;
    $('#attendance_search').on('keyup', function() {
        clearTimeout(attendanceSearchTimeout);
        attendanceSearchTimeout = setTimeout(function() {
            loadAttendanceData(1);
        }, 500);
    });

    // Filter form submit
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadAttendanceData(1);
        updateStats();
    });

    // Auto reload on filter change
    $('#date_filter, #user_id, #division_id, #status, #start_date, #end_date').on('change', function() {
        loadAttendanceData(1);
        updateStats();
    });

    // Update reprocess form dates
    $('#start_date, #end_date').on('change', function() {
        $('#reprocess_start_date').val($('#start_date').val());
        $('#reprocess_end_date').val($('#end_date').val());
    });
});

function loadAttendanceData(page = 1) {
    attendanceCurrentPage = page;
    var search = $('#attendance_search').val();
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var userId = $('#user_id').val();
    var divisionId = $('#division_id').val();
    var status = $('#status').val();
    
    $.ajax({
        url: "{{ url('attendance_v2/datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            start_date: startDate,
            end_date: endDate,
            user_id: userId,
            division_id: divisionId,
            status: status
        },
        success: function(response) {
            if (response.error) {
                $('#attendance_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#attendance_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="9" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                var startIndex = (response.current_page - 1) * response.per_page;
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(startIndex + index + 1));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.at_date ? new Date(row.at_date).toLocaleDateString('id-ID') : '-'));
                    
                    // Staff column with link (ke halaman v2)
                    var staffTd = $('<td>').addClass('px-3 py-4');
                    var staffLink = $('<a>')
                        .attr('href', "{{ url('attendance/staff_v2') }}/" + row.user_id)
                        .addClass('text-blue-600 hover:text-blue-800 font-medium')
                        .text(row.u_name || '-');
                    staffTd.append(staffLink);
                    if (row.u_nip) {
                        staffTd.append($('<br>'));
                        staffTd.append($('<span>').addClass('text-gray-500 text-xs').text(row.u_nip));
                    }
                    tr.append(staffTd);
                    
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ud_name || '-'));
                    
                    // Shift column
                    var shiftText = '-';
                    if (row.sc_code) {
                        shiftText = row.sc_code + (row.sc_shift_name ? ' (' + row.sc_shift_name + ')' : '');
                    }
                    tr.append($('<td>').addClass('px-3 py-4').text(shiftText));
                    
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.at_time_in ? new Date('1970-01-01T' + row.at_time_in).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '-'));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.at_time_out ? new Date('1970-01-01T' + row.at_time_out).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '-'));
                    
                    // Status
                    var statusBadge = getStatusBadge(row.at_status);
                    tr.append($('<td>').addClass('px-3 py-4 text-center').html(statusBadge));
                    
                    // Action
                    var actionTd = $('<td>').addClass('px-3 py-4 text-center');
                    var actionDiv = $('<div>').addClass('flex items-center justify-center gap-2');
                    var viewLink = $('<a>')
                        .attr('href', "{{ url('attendance') }}/" + row.id)
                        .addClass('px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200')
                        .html('<i class="fas fa-eye mr-1"></i>View');
                    actionDiv.append(viewLink);
                    actionTd.append(actionDiv);
                    tr.append(actionTd);
                    
                    tbody.append(tr);
                });
            }

            updatePagination(response.total, response.current_page, response.per_page, response.last_page, 'attendance');
        },
        error: function() {
            $('#attendance_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function getStatusBadge(status) {
    var badges = {
        'present': '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Hadir</span>',
        'late': '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">Terlambat</span>',
        'absent': '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">Tidak Hadir</span>',
        'early_leave': '<span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded">Pulang Awal</span>',
        'scan_once': '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Scan 1 Kali</span>'
    };
    
    if (status && status.startsWith('leave_')) {
        var leaveType = status.replace('leave_', '');
        var leaveNames = {
            'ANNUAL': 'Cuti Tahunan',
            'SICK': 'Cuti Sakit',
            'MATERNITY': 'Cuti Melahirkan',
            'EMERGENCY': 'Cuti Darurat',
            'HALF_DAY': 'Setengah Hari',
            'SPECIAL': 'Cuti Khusus'
        };
        var leaveName = leaveNames[leaveType] || 'Cuti ' + leaveType;
        return '<span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">' + leaveName + '</span>';
    }
    
    return badges[status] || '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">' + (status || '-') + '</span>';
}

function updateStats() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var userId = $('#user_id').val();
    
    $.ajax({
        url: "{{ url('attendance_v2/stats_simple') }}",
        type: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate,
            user_id: userId
        },
        success: function(response) {
            var statsContainer = $('#stats-container');
            statsContainer.empty();
            
            if (response && response.length > 0) {
                response.forEach(function(stat) {
                    var cardClass = '';
                    var iconClass = '';
                    var iconColor = '';
                    var label = '';
                    
                    switch(stat.at_status) {
                        case 'present':
                            cardClass = 'bg-red-50 border-red-200';
                            iconClass = 'fas fa-check-circle';
                            iconColor = 'text-red-500';
                            label = 'Hadir';
                            break;
                        case 'late':
                            cardClass = 'bg-yellow-50 border-yellow-200';
                            iconClass = 'fas fa-clock';
                            iconColor = 'text-yellow-600';
                            label = 'Terlambat';
                            break;
                        case 'absent':
                            cardClass = 'bg-gray-50 border-gray-200';
                            iconClass = 'fas fa-times-circle';
                            iconColor = 'text-gray-600';
                            label = 'Tidak Hadir';
                            break;
                        case 'early_leave':
                            cardClass = 'bg-purple-50 border-purple-200';
                            iconClass = 'fas fa-arrow-left';
                            iconColor = 'text-purple-600';
                            label = 'Pulang Awal';
                            break;
                        case 'scan_once':
                            cardClass = 'bg-green-50 border-green-200';
                            iconClass = 'fas fa-calendar-check';
                            iconColor = 'text-green-600';
                            label = 'Scan 1 Kali';
                            break;
                        default:
                            if (stat.at_status && stat.at_status.startsWith('leave_')) {
                                cardClass = 'bg-blue-50 border-blue-200';
                                iconClass = 'fas fa-calendar';
                                iconColor = 'text-blue-600';
                                var leaveType = stat.at_status.replace('leave_', '');
                                var leaveNames = {
                                    'ANNUAL': 'Cuti Tahunan',
                                    'SICK': 'Cuti Sakit',
                                    'MATERNITY': 'Cuti Melahirkan',
                                    'EMERGENCY': 'Cuti Darurat',
                                    'HALF_DAY': 'Setengah Hari',
                                    'SPECIAL': 'Cuti Khusus'
                                };
                                label = leaveNames[leaveType] || 'Cuti ' + leaveType;
                            } else {
                                cardClass = 'bg-gray-50 border-gray-200';
                                iconClass = 'fas fa-calendar';
                                iconColor = 'text-gray-600';
                                label = stat.at_status || '-';
                            }
                    }
                    
                    var card = $('<div>').addClass('bg-white rounded-lg p-4 border border-gray-200 shadow-sm ' + cardClass);
                    var cardContent = $('<div>').addClass('flex items-center');
                    var iconDiv = $('<div>').addClass('flex-shrink-0');
                    iconDiv.append($('<i>').addClass(iconClass + ' text-2xl ' + iconColor));
                    var textDiv = $('<div>').addClass('ml-4');
                    textDiv.append($('<div>').addClass('text-xl font-semibold text-gray-900').text(stat.total || 0));
                    textDiv.append($('<div>').addClass('text-sm text-gray-600').text(label));
                    cardContent.append(iconDiv);
                    cardContent.append(textDiv);
                    card.append(cardContent);
                    statsContainer.append(card);
                });
            }
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
                if (prefix === 'attendance') loadAttendanceData(currentPage - 1);
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
                if (prefix === 'attendance') loadAttendanceData(currentPage + 1);
            }
        });
    controls.append(nextBtn);
}

function createPageBtn(page, prefix, currentPage) {
    var isActive = page === attendanceCurrentPage;
    return $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'attendance') loadAttendanceData(page);
        });
}

function exportToExcel() {
    const url = new URL('{{ route("attendance.export-excel") }}');
    
    // Add current filters to URL
    const dateFilter = $('#date_filter').val();
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();
    const userId = $('#user_id').val();
    const divisionId = $('#division_id').val();
    const status = $('#status').val();
    
    if (dateFilter && dateFilter !== 'custom') {
        url.searchParams.append('date_filter', dateFilter);
    } else if (dateFilter === 'custom') {
        url.searchParams.append('date_filter', 'custom');
    }
    if (startDate) {
        url.searchParams.append('start_date', startDate);
    }
    if (endDate) {
        url.searchParams.append('end_date', endDate);
    }
    if (userId) {
        url.searchParams.append('user_id', userId);
    }
    if (divisionId) {
        url.searchParams.append('division_id', divisionId);
    }
    if (status) {
        url.searchParams.append('status', status);
    }
    
    // Create download link
    const link = document.createElement('a');
    link.href = url.toString();
    link.download = 'attendance_export.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToPDF() {
    window.location.href = "{{ route('attendance.export-pdf') }}?start_date=" + $('#start_date').val() + "&end_date=" + $('#end_date').val() + "&user_id=" + $('#user_id').val() + "&division_id=" + $('#division_id').val() + "&status=" + $('#status').val();
}
</script>
