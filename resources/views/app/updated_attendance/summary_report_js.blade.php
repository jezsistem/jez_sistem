<script>
var summaryCurrentPage = 1;
var perPage = 25;

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
    $('#search').on('keyup', function() {
        clearTimeout(summarySearchTimeout);
        summarySearchTimeout = setTimeout(function() {
            loadSummaryData(1);
        }, 500);
    });

    // Filter form submit
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadSummaryData(1);
    });

    // Auto reload on filter change
    $('#date_filter, #division_id, #start_date, #end_date').on('change', function() {
        loadSummaryData(1);
    });
});

function loadSummaryData(page = 1) {
    summaryCurrentPage = page;
    var search = $('#search').val();
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var divisionId = $('#division_id').val();
    
    $.ajax({
        url: "{{ url('attendance/summary-report_v2/datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            start_date: startDate,
            end_date: endDate,
            division_id: divisionId
        },
        success: function(response) {
            if (response.error) {
                $('#summary_tbody').html('<tr><td colspan="14" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#summary_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="14" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                var startIndex = (response.current_page - 1) * response.per_page;
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(startIndex + index + 1));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_nip || '-'));
                    
                    // Staff column with link (ke halaman v2)
                    var staffTd = $('<td>').addClass('px-3 py-4');
                    var staffLink = $('<a>')
                        .attr('href', "{{ url('attendance/staff_v2') }}/" + row.user_id)
                        .addClass('text-blue-600 hover:text-blue-800 font-medium')
                        .text(row.u_name || '-');
                    staffTd.append(staffLink);
                    tr.append(staffTd);
                    
                    tr.append($('<td>').addClass('px-3 py-4').text(row.up_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ud_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ut_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.total_shifts || 0));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.present_days || 0));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.day_off || 0));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.leave_days || 0));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.sick_days || 0));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.late_days || 0));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.scan_once_days || 0));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.alpha_days || 0));
                    
                    tbody.append(tr);
                });
            }

            // Update statistics
            updateSummaryStats(response.data);

            updatePagination(response.total, response.current_page, response.per_page, response.last_page, 'summary');
        },
        error: function() {
            $('#summary_tbody').html('<tr><td colspan="14" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function updateSummaryStats(data) {
    var totalStaff = data.length;
    var totalShifts = data.reduce((sum, row) => sum + (parseInt(row.total_shifts) || 0), 0);
    var lateDays = data.reduce((sum, row) => sum + (parseInt(row.late_days) || 0), 0);
    var scanOnceDays = data.reduce((sum, row) => sum + (parseInt(row.scan_once_days) || 0), 0);
    var alphaDays = data.reduce((sum, row) => sum + (parseInt(row.alpha_days) || 0), 0);

    $('#total-staff').text(totalStaff);
    $('#total-shifts').text(totalShifts);
    $('#late-days').text(lateDays);
    $('#scan-once-days').text(scanOnceDays);
    $('#alpha-days').text(alphaDays);
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
    return $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'summary') loadSummaryData(page);
        });
}

function exportToExcel() {
    const url = new URL('{{ route("attendance.summary-report-export-excel") }}');
    
    // Add current filters to URL
    const dateFilter = $('#date_filter').val();
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();
    const divisionId = $('#division_id').val();
    const search = $('#search').val();
    
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
    if (divisionId) {
        url.searchParams.append('division_id', divisionId);
    }
    if (search) {
        url.searchParams.append('search', search);
    }
    
    // Create download link
    const link = document.createElement('a');
    link.href = url.toString();
    link.download = 'attendance_summary_export.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToPDF() {
    window.location.href = "{{ route('attendance.summary-report-export-pdf') }}?start_date=" + $('#start_date').val() + "&end_date=" + $('#end_date').val() + "&division_id=" + $('#division_id').val() + "&search=" + encodeURIComponent($('#search').val());
}
</script>
