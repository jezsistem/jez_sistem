<script>
var staffCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadStaffData(1);

    // Search with debounce
    var staffSearchTimeout;
    $('#staff_search').on('keyup', function() {
        clearTimeout(staffSearchTimeout);
        staffSearchTimeout = setTimeout(function() {
            loadStaffData(1);
        }, 500);
    });

    // Apply filters button
    $('#apply_filters_btn').on('click', function() {
        loadStaffData(1);
    });

    // Position form submit
    $('#f_position').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_position_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('staff') }}/" + $('#position_user_id').val() + "/position",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                user_id: $('#position_user_id').val(),
                up_id: $('#up_id').val()
            },
            dataType: 'json',
            success: function(response) {
                saveBtn.html('Update Position').prop('disabled', false);
                if (response.success) {
                    closePositionModal();
                    Swal.fire('Berhasil', response.message, 'success');
                    loadStaffData(staffCurrentPage);
                } else {
                    Swal.fire('Gagal', response.message || 'Gagal update position', 'error');
                }
            },
            error: function(xhr) {
                saveBtn.html('Update Position').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Division form submit
    $('#f_division').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_division_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('staff') }}/" + $('#division_user_id').val() + "/division",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                user_id: $('#division_user_id').val(),
                ud_id: $('#ud_id').val()
            },
            dataType: 'json',
            success: function(response) {
                saveBtn.html('Update Division').prop('disabled', false);
                if (response.success) {
                    closeDivisionModal();
                    Swal.fire('Berhasil', response.message, 'success');
                    loadStaffData(staffCurrentPage);
                } else {
                    Swal.fire('Gagal', response.message || 'Gagal update division', 'error');
                }
            },
            error: function(xhr) {
                saveBtn.html('Update Division').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // User Type form submit
    $('#f_user_type_staff').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_user_type_staff_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('staff') }}/" + $('#user_type_user_id').val() + "/user-type",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                user_id: $('#user_type_user_id').val(),
                ut_id: $('#ut_id').val()
            },
            dataType: 'json',
            success: function(response) {
                saveBtn.html('Update User Type').prop('disabled', false);
                if (response.success) {
                    closeUserTypeModal();
                    Swal.fire('Berhasil', response.message, 'success');
                    loadStaffData(staffCurrentPage);
                } else {
                    Swal.fire('Gagal', response.message || 'Gagal update user type', 'error');
                }
            },
            error: function(xhr) {
                saveBtn.html('Update User Type').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Leave Balance form submit
    $('#f_leave_balance').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_leave_balance_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('staff') }}/" + $('#leave_balance_user_id').val() + "/leave-balance",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                user_id: $('#leave_balance_user_id').val(),
                lb_remaining_balance: $('#lb_remaining_balance').val()
            },
            dataType: 'json',
            success: function(response) {
                saveBtn.html('Update Leave Balance').prop('disabled', false);
                if (response.success) {
                    closeLeaveBalanceModal();
                    Swal.fire('Berhasil', response.message, 'success');
                    loadStaffData(staffCurrentPage);
                } else {
                    Swal.fire('Gagal', response.message || 'Gagal update leave balance', 'error');
                }
            },
            error: function(xhr) {
                saveBtn.html('Update Leave Balance').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Export handlers
    $('#export_staff_btn').on('click', function() {
        $('#export_staff_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_staff_btn, #export_staff_menu').length) {
            $('#export_staff_menu').addClass('hidden');
        }
    });

    $('#export_staff_excel_btn').on('click', function() {
        jQuery("#StaffTable").table2excel({
            filename: "Staff",
        });
    });
});

// Event delegation for action buttons
$(document).on('click', '.edit-position-btn', function() {
    var id = $(this).data('id');
    var up_id = $(this).data('up_id') || '';
    $('#position_user_id').val(id);
    $('#up_id').val(up_id);
    openPositionModal();
});

$(document).on('click', '.edit-division-btn', function() {
    var id = $(this).data('id');
    var ud_id = $(this).data('ud_id') || '';
    $('#division_user_id').val(id);
    $('#ud_id').val(ud_id);
    openDivisionModal();
});

$(document).on('click', '.edit-user-type-btn', function() {
    var id = $(this).data('id');
    var ut_id = $(this).data('ut_id') || '';
    $('#user_type_user_id').val(id);
    $('#ut_id').val(ut_id);
    openUserTypeModal();
});

$(document).on('click', '.edit-leave-balance-btn', function() {
    var id = $(this).data('id');
    var balance = $(this).data('balance') || 0;
    $('#leave_balance_user_id').val(id);
    $('#lb_remaining_balance').val(balance);
    openLeaveBalanceModal();
});

function loadStaffData(page = 1) {
    staffCurrentPage = page;
    var search = $('#staff_search').val();
    var position_filter = $('#position_filter').val();
    var division_filter = $('#division_filter').val();
    
    $.ajax({
        url: "{{ url('staff_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            position_filter: position_filter,
            division_filter: division_filter
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
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.DT_RowIndex));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_nip || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.up_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ud_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ut_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.lb_remaining_balance || '0'));
                    var actionTd = $('<td>').addClass('px-3 py-4 text-center');
                    actionTd.html(row.action || '');
                    tr.append(actionTd);
                    tbody.append(tr);
                });
            }

            createCustomPagination('#StaffTable', response.total, response.current_page, response.total_pages, response.per_page, loadStaffData, 'staff');
        },
        error: function() {
            $('#staff_tbody').html('<tr><td colspan="8" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function openPositionModal() {
    $('#PositionModal').removeClass('hidden');
}

function closePositionModal() {
    $('#PositionModal').addClass('hidden');
}

function openDivisionModal() {
    $('#DivisionModal').removeClass('hidden');
}

function closeDivisionModal() {
    $('#DivisionModal').addClass('hidden');
}

function openUserTypeModal() {
    $('#UserTypeModal').removeClass('hidden');
}

function closeUserTypeModal() {
    $('#UserTypeModal').addClass('hidden');
}

function openLeaveBalanceModal() {
    $('#LeaveBalanceModal').removeClass('hidden');
}

function closeLeaveBalanceModal() {
    $('#LeaveBalanceModal').addClass('hidden');
}

// Pagination function (reuse from user_position_js)
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction, prefix) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#' + prefix + '_pagination_info');
    var paginationControls = wrapper.querySelector('#' + prefix + '_pagination_controls');
    
    if (!paginationInfo || !paginationControls) return;

    paginationControls.innerHTML = '';

    var start = totalRecords > 0 ? (currentPage - 1) * perPage + 1 : 0;
    var end = Math.min(currentPage * perPage, totalRecords);
    var infoText = totalRecords > 0 ? 'Menampilkan ' + start + ' sampai ' + end + ' dari ' + totalRecords + ' data' : 'Tidak ada data';

    paginationInfo.textContent = infoText;

    if (totalPages > 1) {
        var prevBtn = document.createElement('button');
        prevBtn.className = 'px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100' + (currentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
        prevBtn.textContent = 'Sebelumnya';
        prevBtn.type = 'button';
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (currentPage > 1) {
                loadFunction(currentPage - 1);
            }
        });
        paginationControls.appendChild(prevBtn);

        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            var firstBtn = document.createElement('button');
            firstBtn.className = 'px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-100';
            firstBtn.textContent = '1';
            firstBtn.type = 'button';
            firstBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                loadFunction(1);
            });
            paginationControls.appendChild(firstBtn);
            if (startPage > 2) {
                var ellipsis = document.createElement('span');
                ellipsis.className = 'px-3 py-1 text-sm text-gray-500';
                ellipsis.textContent = '...';
                paginationControls.appendChild(ellipsis);
            }
        }

        for (var i = startPage; i <= endPage; i++) {
            var pageBtn = document.createElement('button');
            pageBtn.className = 'px-3 py-1 text-sm font-medium border border-gray-300 hover:bg-gray-100';
            if (i === currentPage) {
                pageBtn.className += ' text-white bg-blue-600';
            } else {
                pageBtn.className += ' text-gray-500 bg-white';
            }
            pageBtn.textContent = i;
            pageBtn.type = 'button';
            pageBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                loadFunction(parseInt(this.textContent));
            });
            paginationControls.appendChild(pageBtn);
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                var ellipsis = document.createElement('span');
                ellipsis.className = 'px-3 py-1 text-sm text-gray-500';
                ellipsis.textContent = '...';
                paginationControls.appendChild(ellipsis);
            }
            var lastBtn = document.createElement('button');
            lastBtn.className = 'px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-100';
            lastBtn.textContent = totalPages;
            lastBtn.type = 'button';
            lastBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                loadFunction(totalPages);
            });
            paginationControls.appendChild(lastBtn);
        }
        
        var nextBtn = document.createElement('button');
        nextBtn.className = 'px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100';
        nextBtn.textContent = 'Selanjutnya';
        nextBtn.type = 'button';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (currentPage < totalPages) {
                loadFunction(currentPage + 1);
            }
        });
        paginationControls.appendChild(nextBtn);
    }
}
</script>
