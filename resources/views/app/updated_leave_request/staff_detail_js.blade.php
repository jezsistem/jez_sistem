<script>
var staffCurrentPage = 1;
var perPage = 25;
var staffUserId = {{ $data['staffUser']->id }};

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadStaffData(1);
    loadStaffStats();

    // Date filter change handler
    $('#date_filter').on('change', function() {
        handleDateFilterChange(this.value);
        if (this.value !== 'custom') {
            loadStaffData(1);
            loadStaffStats();
        }
    });
    
    // Date input change handler
    $('#start_date, #end_date').on('change', function() {
        if ($('#date_filter').val() === 'custom') {
            loadStaffData(1);
            loadStaffStats();
        }
    });

    // Status filter change handler
    $('#status').on('change', function() {
        loadStaffData(1);
        loadStaffStats();
    });

    // Filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadStaffData(1);
        loadStaffStats();
    });
});

function loadStaffData(page = 1) {
    staffCurrentPage = page;
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var dateFilter = $('#date_filter').val();
    var status = $('#status').val();
    
    $.ajax({
        url: "{{ url('leave-requests/staff_v2') }}/" + staffUserId + "/datatables_simple",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            start_date: startDate,
            end_date: endDate,
            date_filter: dateFilter,
            status: status
        },
        success: function(response) {
            if (response.error) {
                $('#staff_tbody').html('<tr><td colspan="8" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#staff_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="8" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                var startIndex = (response.current_page - 1) * response.per_page;
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(startIndex + index + 1));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.start_date || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.end_date || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.duration || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.leave_type_name || '-'));
                    
                    var statusTd = $('<td>').addClass('px-3 py-4 text-center');
                    var statusBadge = $('<span>').addClass('px-2 py-1 text-xs font-semibold rounded-full ' + (row.status_class || 'bg-gray-100 text-gray-800'));
                    statusBadge.text(row.status_display || '-');
                    statusTd.append(statusBadge);
                    tr.append(statusTd);
                    
                    tr.append($('<td>').addClass('px-3 py-4').html('<div class="max-w-xs truncate" title="' + (row.reason || '-') + '">' + (row.reason || '-') + '</div>'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.created_at || '-'));
                    
                    tbody.append(tr);
                });
            }

            updatePagination(response.total, response.current_page, response.per_page, response.last_page, 'staff');
        },
        error: function() {
            $('#staff_tbody').html('<tr><td colspan="8" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function loadStaffStats() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var dateFilter = $('#date_filter').val();
    var status = $('#status').val();
    
    $.ajax({
        url: "{{ url('leave-requests/staff_v2') }}/" + staffUserId + "/stats",
        type: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate,
            date_filter: dateFilter,
            status: status
        },
        success: function(response) {
            $('#approvedCount').text(response.approved || 0);
            $('#pendingCount').text(response.pending || 0);
            $('#rejectedCount').text(response.rejected || 0);
            $('#totalDays').text(response.total_days || 0);
        },
        error: function() {
            $('#approvedCount').text('0');
            $('#pendingCount').text('0');
            $('#rejectedCount').text('0');
            $('#totalDays').text('0');
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
                if (prefix === 'staff') loadStaffData(currentPage - 1);
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
                if (prefix === 'staff') loadStaffData(currentPage + 1);
            }
        });
    controls.append(nextBtn);
}

function createPageBtn(page, prefix, currentPage) {
    var isActive = page === staffCurrentPage;
    var btn = $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'staff') loadStaffData(page);
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

function exportToExcel() {
    var table = $('#StaffTable');
    if (table.length === 0) {
        Swal.fire('Error', 'Tabel tidak ditemukan.', 'error');
        return;
    }
    
    // Check if table2excel is loaded
    if (typeof $.fn.table2excel === 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Export Error',
            text: 'Export library belum dimuat. Silakan refresh halaman dan coba lagi.',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    try {
        table.table2excel({
            exclude: ".no-export",
            name: "Staff Leave Detail",
            filename: "staff_leave_detail_" + new Date().toISOString().split('T')[0],
            fileext: ".xlsx"
        });
    } catch (e) {
        console.error('Export error:', e);
        Swal.fire('Error', 'Terjadi kesalahan saat export: ' + e.message, 'error');
    }
}

function exportToPDF() {
    // Redirect to PDF export route with current filters
    var form = document.getElementById('filterForm');
    var formData = new FormData(form);
    var params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    var url = "{{ route('leave-requests.staff-export-pdf', $data['staffUser']->id) }}";
    if (params.toString()) {
        url += '?' + params.toString();
    }
    window.open(url, '_blank');
}
</script>
