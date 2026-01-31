<script>
var leaveRequestCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadLeaveRequestData(1);
    updateStats();
    
    // Check if edit parameter exists
    const urlParams = new URLSearchParams(window.location.search);
    const editId = urlParams.get('edit');
    if (editId) {
        // Load leave request data and open edit modal
        setTimeout(function() {
            $.ajax({
                url: "{{ url('leave-requests_v2/show') }}/" + editId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        // Check if user can edit (pending and owner)
                        if (data.lr_status === 'pending' && data.user_id == {{ auth()->user()->id ?? 0 }}) {
                            // Open edit modal with data
                            $('#leave_request_modal_id').val(data.id);
                            $('#leave_request_modal_mode').val('edit');
                            $('#leave_type_id').val(data.leave_type_id);
                            $('#lr_start_date').val(data.lr_start_date);
                            $('#lr_end_date').val(data.lr_end_date || '');
                            $('#lr_start_time').val(data.lr_start_time || '');
                            $('#lr_end_time').val(data.lr_end_time || '');
                            $('#lr_unit').val(data.lr_unit);
                            $('#lr_reason').val(data.lr_reason);
                            $('#leave_request_modal_title').text('Edit Leave Request');
                            
                            if (data.lr_unit === 'hours') {
                                $('#time_fields').show();
                            } else {
                                $('#time_fields').hide();
                            }
                            
                            openLeaveRequestModal();
                            
                            // Load leave balance
                            loadLeaveBalance(data.leave_type_id);
                        } else {
                            Swal.fire('Error', 'You can only edit pending leave requests that you own', 'error');
                        }
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to load leave request data', 'error');
                }
            });
        }, 500);
    }

    // Search with debounce
    var leaveRequestSearchTimeout;
    $('#leave_request_search').on('keyup', function() {
        clearTimeout(leaveRequestSearchTimeout);
        leaveRequestSearchTimeout = setTimeout(function() {
            loadLeaveRequestData(1);
        }, 500);
    });

    // Date filter change handler
    $('#date_filter').on('change', function() {
        handleDateFilterChange(this.value);
    });

    // Add button handler
    $('#add_leave_request_btn').on('click', function() {
        openLeaveRequestModal();
        $('#leave_request_modal_id').val('');
        $('#leave_request_modal_mode').val('add');
        $('#f_leave_request')[0].reset();
        $('#leaveBalanceInfo').html('<p>Select a leave type to see your balance information.</p>');
        $('#time_fields').hide();
        $('#leave_request_modal_title').text('Tambah Leave Request');
        
        // Load leave balance when type changes
        $('#leave_type_id').off('change').on('change', function() {
            loadLeaveBalance($(this).val());
        });
        
        // Toggle time fields based on unit
        $('#lr_unit').off('change').on('change', function() {
            if ($(this).val() === 'hours') {
                $('#time_fields').show();
            } else {
                $('#time_fields').hide();
            }
        });
    });

    // Form submit handler
    $('#f_leave_request').on('submit', function(e) {
        e.preventDefault();
        
        var saveBtn = $('#save_leave_request_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        var mode = $('#leave_request_modal_mode').val();
        var id = $('#leave_request_modal_id').val();
        
        var mode = $('#leave_request_modal_mode').val();
        var id = $('#leave_request_modal_id').val();
        
        var url = mode === 'edit' 
            ? "{{ url('leave-requests_v2-update') }}/" + id
            : "{{ url('leave-requests_v2-store') }}";
        
        if (mode === 'edit') {
            formData.append('_method', 'PUT');
        }
        
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (response.success) {
                    closeLeaveRequestModal();
                    Swal.fire('Berhasil', response.message || 'Leave request berhasil disimpan', 'success');
                    loadLeaveRequestData(leaveRequestCurrentPage);
                    updateStats();
                } else {
                    Swal.fire('Gagal', response.message || 'Leave request tidak tersimpan', 'warning');
                }
            },
            error: function(xhr) {
                saveBtn.html('Simpan').prop('disabled', false);
                var errorMessage = 'Terjadi kesalahan saat menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMessage, 'error');
            }
        });
    });

    // Approval form submit handler
    $('#f_approval').on('submit', function(e) {
        e.preventDefault();
        
        var saveBtn = $('#save_approval_btn');
        var action = $('#approval_modal_action').val();
        var id = $('#approval_modal_id').val();
        var notes = $('#lr_admin_notes').val();
        
        if (action === 'reject' && !notes.trim()) {
            Swal.fire('Error', 'Admin notes is required for rejection', 'error');
            return;
        }
        
        saveBtn.html('Proses..').prop('disabled', true);
        
        var url = action === 'approve' 
            ? "{{ url('leave-requests_v2-approve') }}/" + id
            : "{{ url('leave-requests_v2-reject') }}/" + id;
        
        $.ajax({
            type: 'POST',
            url: url,
            data: JSON.stringify({
                lr_admin_notes: notes,
                _token: $('meta[name="csrf-token"]').attr('content')
            }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                saveBtn.html('Confirm').prop('disabled', false);
                if (response.success) {
                    closeApprovalModal();
                    Swal.fire('Berhasil', response.message || 'Leave request berhasil diproses', 'success');
                    loadLeaveRequestData(leaveRequestCurrentPage);
                    updateStats();
                } else {
                    Swal.fire('Gagal', response.message || 'Leave request tidak terproses', 'warning');
                }
            },
            error: function(xhr) {
                saveBtn.html('Confirm').prop('disabled', false);
                var errorMessage = 'Terjadi kesalahan saat memproses data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMessage, 'error');
            }
        });
    });

    // Export menu toggle
    $('#export_leave_request_btn').on('click', function(e) {
        e.stopPropagation();
        $('#export_leave_request_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_leave_request_btn, #export_leave_request_menu').length) {
            $('#export_leave_request_menu').addClass('hidden');
        }
    });

    // Export Excel
    $('#export_leave_request_excel_btn').on('click', function() {
        exportToExcel();
        $('#export_leave_request_menu').addClass('hidden');
    });
});

function loadLeaveRequestData(page = 1) {
    leaveRequestCurrentPage = page;
    var search = $('#leave_request_search').val();
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var dateFilter = $('#date_filter').val();
    var userId = $('#user_id').val();
    var status = $('#status').val();
    var leaveTypeId = $('#leave_type_id').val();
    
    $.ajax({
        url: "{{ url('leave-requests_v2/datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            start_date: startDate,
            end_date: endDate,
            date_filter: dateFilter,
            user_id: userId,
            status: status,
            leave_type_id: leaveTypeId
        },
        success: function(response) {
            if (response.error) {
                $('#leave_request_tbody').html('<tr><td colspan="10" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#leave_request_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="10" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                var startIndex = (response.current_page - 1) * response.per_page;
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(startIndex + index + 1));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.lr_date || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').html('<div class="font-medium">' + (row.u_name || '-') + '</div><div class="text-xs text-gray-500">' + (row.u_nip || '-') + '</div>'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ud_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.lt_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.lr_start_date || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.lr_end_date || '-'));
                    
                    // Attachment column
                    var attachmentTd = $('<td>').addClass('px-3 py-4 text-center');
                    if (row.lr_attachment && row.lr_attachment !== '-') {
                        attachmentTd.html(row.lr_attachment);
                    } else {
                        attachmentTd.html('<span class="text-gray-400">-</span>');
                    }
                    tr.append(attachmentTd);
                    
                    var statusTd = $('<td>').addClass('px-3 py-4 text-center');
                    var statusBadge = $('<span>').addClass('px-2 py-1 text-xs font-semibold rounded-full ' + (row.lr_status_class || 'bg-gray-100 text-gray-800'));
                    statusBadge.text(row.lr_status_display || '-');
                    statusTd.append(statusBadge);
                    tr.append(statusTd);
                    
                    // Action column - use action HTML from server if available
                    var actionTd = $('<td>').addClass('px-3 py-4 text-center');
                    if (row.action) {
                        actionTd.html(row.action);
                    } else {
                        // Fallback: build action buttons client-side
                        var actionHtml = '<div class="flex items-center justify-center gap-2 flex-wrap">';
                        
                        // View button
                        var viewBtn = $('<button>')
                            .addClass('px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200')
                            .text('View')
                            .on('click', function() {
                                viewLeaveRequestDetail(row.id);
                            });
                        actionHtml += viewBtn[0].outerHTML;
                        
                        // Edit button - always show (like old page)
                        var editBtn = $('<button>')
                            .addClass('px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded hover:bg-yellow-200')
                            .text('Edit')
                            .on('click', function() {
                                editLeaveRequest(row.id);
                            });
                        actionHtml += editBtn[0].outerHTML;
                        
                        // Approve/Reject buttons (only for pending)
                        if (row.lr_status === 'pending') {
                            var approveBtn = $('<button>')
                                .addClass('px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded hover:bg-green-200')
                                .text('Approve')
                                .on('click', function() {
                                    showApprovalModal(row.id, 'approve');
                                });
                            actionHtml += approveBtn[0].outerHTML;
                            
                            var rejectBtn = $('<button>')
                                .addClass('px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200')
                                .text('Reject')
                                .on('click', function() {
                                    showApprovalModal(row.id, 'reject');
                                });
                            actionHtml += rejectBtn[0].outerHTML;
                        }
                        
                        // Delete button (only for owner and pending)
                        if (row.lr_status === 'pending' && row.user_id == {{ auth()->user()->id ?? 0 }}) {
                            var deleteBtn = $('<button>')
                                .addClass('px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200')
                                .text('Delete')
                                .on('click', function() {
                                    deleteLeaveRequest(row.id);
                                });
                            actionHtml += deleteBtn[0].outerHTML;
                        }
                        
                        actionHtml += '</div>';
                        actionTd.html(actionHtml);
                    }
                    tr.append(actionTd);
                    tbody.append(tr);
                });
            }

            updatePagination(response.total, response.current_page, response.per_page, response.last_page, 'leave_request');
        },
        error: function() {
            $('#leave_request_tbody').html('<tr><td colspan="10" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
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
        
        loadLeaveRequestData(1);
        updateStats();
    }
}

function updateStats() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var dateFilter = $('#date_filter').val();
    var userId = $('#user_id').val();
    var status = $('#status').val();
    var leaveTypeId = $('#leave_type_id').val();
    
    $.ajax({
        url: "{{ route('leave-requests.stats') }}",
        method: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate,
            date_filter: dateFilter,
            user_id: userId,
            leave_type_id: leaveTypeId,
            status: status
        },
        success: function(response) {
            $('#total-requests').text(response.total || 0);
            $('#pending-requests').text(response.pending || 0);
            $('#approved-requests').text(response.approved || 0);
            $('#rejected-requests').text(response.rejected || 0);
        },
        error: function() {
            console.error('Failed to update stats');
        }
    });
}

function loadLeaveBalance(leaveTypeId) {
    if (!leaveTypeId) {
        $('#leaveBalanceInfo').html('<p>Select a leave type to see your balance information.</p>');
        return;
    }
    
    $('#leaveBalanceInfo').html('<p><i class="fas fa-spinner fa-spin"></i> Loading balance information...</p>');
    
    $.ajax({
        url: "{{ url('leave-requests/balance') }}/" + leaveTypeId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.balance) {
                var balance = response.balance;
                var html = '<div class="space-y-1">';
                html += '<p class="font-semibold">Leave Balance Information:</p>';
                html += '<p>Initial Balance: <strong>' + (balance.lb_initial_balance || 0) + '</strong></p>';
                html += '<p>Used Balance: <strong>' + (balance.lb_used_balance || 0) + '</strong></p>';
                html += '<p>Remaining Balance: <strong class="text-green-600">' + (balance.lb_remaining_balance || 0) + '</strong></p>';
                html += '</div>';
                $('#leaveBalanceInfo').html(html);
            } else {
                $('#leaveBalanceInfo').html('<p class="text-yellow-600">No balance information available for this leave type.</p>');
            }
        },
        error: function() {
            $('#leaveBalanceInfo').html('<p class="text-red-500">Failed to load balance information.</p>');
        }
    });
}

function openLeaveRequestModal() {
    $('#LeaveRequestModal').removeClass('hidden');
}

function closeLeaveRequestModal() {
    $('#LeaveRequestModal').addClass('hidden');
}

function viewLeaveRequestDetail(id) {
    window.location.href = "{{ url('leave-requests_v2/detail') }}/" + id;
}

function editLeaveRequest(id) {
    // Redirect to edit page or open modal
    window.location.href = "{{ url('leave-requests_v2') }}?edit=" + id;
}

function showApprovalModal(id, action) {
    $('#approval_modal_id').val(id);
    $('#approval_modal_action').val(action);
    $('#lr_admin_notes').val('');
    
    if (action === 'approve') {
        $('#approval_modal_title').text('Approve Leave Request');
        $('#approval_notes_label').text('Admin Notes (Optional)');
        $('#approval_notes_required').text('');
        $('#approval_notes_hint').text('Optional notes for approval');
        $('#save_approval_btn').removeClass('bg-red-500 hover:bg-red-700').addClass('bg-green-600 hover:bg-green-700').text('Approve');
    } else {
        $('#approval_modal_title').text('Reject Leave Request');
        $('#approval_notes_label').text('Admin Notes');
        $('#approval_notes_required').text('*');
        $('#approval_notes_hint').text('Required: Please provide reason for rejection');
        $('#save_approval_btn').removeClass('bg-green-600 hover:bg-green-700').addClass('bg-red-500 hover:bg-red-700').text('Reject');
        $('#lr_admin_notes').attr('required', true);
    }
    
    $('#ApprovalModal').removeClass('hidden');
}

function closeApprovalModal() {
    $('#ApprovalModal').addClass('hidden');
    $('#lr_admin_notes').removeAttr('required');
}

function deleteLeaveRequest(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ url('leave-requests_v2-delete') }}/" + id,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil', response.message || 'Leave request berhasil dihapus', 'success');
                        loadLeaveRequestData(leaveRequestCurrentPage);
                        updateStats();
                    } else {
                        Swal.fire('Gagal', response.message || 'Leave request tidak terhapus', 'warning');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Terjadi kesalahan saat menghapus data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', errorMessage, 'error');
                }
            });
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
                if (prefix === 'leave_request') loadLeaveRequestData(currentPage - 1);
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
                if (prefix === 'leave_request') loadLeaveRequestData(currentPage + 1);
            }
        });
    controls.append(nextBtn);
}

function createPageBtn(page, prefix, currentPage) {
    var isActive = page === leaveRequestCurrentPage;
    var btn = $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'leave_request') loadLeaveRequestData(page);
        });
    return btn;
}

function exportToExcel() {
    var table = $('#LeaveRequestTable');
    table.table2excel({
        exclude: ".no-export",
        name: "Leave Requests",
        filename: "leave_requests_" + new Date().toISOString().split('T')[0],
        fileext: ".xlsx"
    });
}
</script>
