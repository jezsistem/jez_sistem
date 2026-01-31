<script>
var staffAttendanceCurrentPage = 1;
var perPage = 25;
var userId = {{ $staff->id }};

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Set default dates
    var today = new Date();
    var lastMonth = new Date(today);
    lastMonth.setDate(today.getDate() - 30);
    
    $('#start_date').val(lastMonth.toISOString().split('T')[0]);
    $('#end_date').val(today.toISOString().split('T')[0]);

    // Load initial data
    loadStaffAttendanceData(1);
    updateStaffStats();
    loadAlphaDates();

    // Search with debounce
    var staffAttendanceSearchTimeout;
    $('#staff_attendance_search').on('keyup', function() {
        clearTimeout(staffAttendanceSearchTimeout);
        staffAttendanceSearchTimeout = setTimeout(function() {
            loadStaffAttendanceData(1);
        }, 500);
    });

    // Filter form submit
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadStaffAttendanceData(1);
        updateStaffStats();
        loadAlphaDates();
    });

    // Auto reload on filter change
    $('#date_filter, #status, #start_date, #end_date').on('change', function() {
        loadStaffAttendanceData(1);
        updateStaffStats();
        loadAlphaDates();
    });
});

function loadStaffAttendanceData(page = 1) {
    staffAttendanceCurrentPage = page;
    var search = $('#staff_attendance_search').val();
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var status = $('#status').val();
    
    $.ajax({
        url: "{{ url('attendance/staff_v2') }}/" + userId + "/datatables_simple",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            start_date: startDate,
            end_date: endDate,
            status: status
        },
        success: function(response) {
            if (response.error) {
                $('#staff_attendance_tbody').html('<tr><td colspan="8" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#staff_attendance_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="8" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                var startIndex = (response.current_page - 1) * response.per_page;
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(startIndex + index + 1));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.at_date ? new Date(row.at_date).toLocaleDateString('id-ID') : '-'));
                    
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
                    
                    tr.append($('<td>').addClass('px-3 py-4').text(row.at_notes || '-'));
                    
                    // Action
                    var actionTd = $('<td>').addClass('px-3 py-4 text-center');
                    var actionLink = $('<a>')
                        .attr('href', "{{ url('attendance') }}/" + row.id)
                        .addClass('px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200')
                        .html('<i class="fas fa-eye mr-1"></i>View');
                    actionTd.append(actionLink);
                    tr.append(actionTd);
                    
                    tbody.append(tr);
                });
            }

            updatePagination(response.total, response.current_page, response.per_page, response.last_page, 'staff_attendance');
        },
        error: function() {
            $('#staff_attendance_tbody').html('<tr><td colspan="8" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
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

function updateStaffStats() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    
    $.ajax({
        url: "{{ url('attendance/staff_v2') }}/" + userId + "/stats_simple",
        type: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            $('#total_shifts').text(response.total_shifts || 0);
            $('#present_days').text(response.present_days || 0);
            $('#total_libur').text(response.total_libur || 0);
            $('#sick_days').text(response.sick_days || 0);
            $('#leave_days').text(response.leave_days || 0);
            $('#late_days').text(response.late_days || 0);
            $('#scan_once_days').text(response.scan_once_days || 0);
            $('#alpha_days').text(response.alpha_days || 0);
        },
        error: function() {
            console.error('Error loading stats');
        }
    });
}

function loadAlphaDates() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    
    $.ajax({
        url: "{{ url('attendance/staff_v2') }}/" + userId + "/alpha-dates_simple",
        type: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            var container = $('#alpha_dates_container');
            container.empty();
            
            if (response.dates && response.dates.length > 0) {
                var datesHtml = '<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2">';
                response.dates.forEach(function(date) {
                    datesHtml += '<span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded">' + new Date(date).toLocaleDateString('id-ID') + '</span>';
                });
                datesHtml += '</div>';
                container.html(datesHtml);
            } else {
                container.html('<p class="text-gray-500">Tidak ada alpha dates</p>');
            }
        },
        error: function() {
            console.error('Error loading alpha dates');
        }
    });
}

function toggleAlphaAccordion() {
    $('#alphaAccordionBody').toggleClass('hidden');
    var icon = $('#alphaAccordionIcon');
    if ($('#alphaAccordionBody').hasClass('hidden')) {
        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
    } else {
        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }
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
                if (prefix === 'staff_attendance') loadStaffAttendanceData(currentPage - 1);
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
                if (prefix === 'staff_attendance') loadStaffAttendanceData(currentPage + 1);
            }
        });
    controls.append(nextBtn);
}

function createPageBtn(page, prefix, currentPage) {
    var isActive = page === staffAttendanceCurrentPage;
    return $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'staff_attendance') loadStaffAttendanceData(page);
        });
}

function exportStaffToExcel() {
    const url = new URL('{{ route("attendance.staff-export-excel", ":user_id") }}'.replace(':user_id', userId));
    
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();
    const status = $('#status').val();
    
    if (startDate) url.searchParams.append('start_date', startDate);
    if (endDate) url.searchParams.append('end_date', endDate);
    if (status) url.searchParams.append('status', status);
    
    const link = document.createElement('a');
    link.href = url.toString();
    link.download = 'staff_attendance_export.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportStaffToPDF() {
    const url = new URL('{{ route("attendance.staff-export-pdf", ":user_id") }}'.replace(':user_id', userId));
    
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();
    const status = $('#status').val();
    
    if (startDate) url.searchParams.append('start_date', startDate);
    if (endDate) url.searchParams.append('end_date', endDate);
    if (status) url.searchParams.append('status', status);
    
    window.location.href = url.toString();
}
</script>
