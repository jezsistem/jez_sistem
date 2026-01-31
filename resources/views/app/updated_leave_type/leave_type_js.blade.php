<script>
var leaveTypeCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadLeaveTypeData(1);
    
    // Check if edit parameter exists
    const urlParams = new URLSearchParams(window.location.search);
    const editId = urlParams.get('edit');
    if (editId) {
        // Trigger edit modal
        setTimeout(function() {
            editLeaveType(editId);
        }, 500);
    }

    // Search with debounce
    var leaveTypeSearchTimeout;
    $('#leave_type_search').on('keyup', function() {
        clearTimeout(leaveTypeSearchTimeout);
        leaveTypeSearchTimeout = setTimeout(function() {
            loadLeaveTypeData(1);
        }, 500);
    });

    // Add button handler
    $('#add_leave_type_btn').on('click', function() {
        openLeaveTypeModal();
        $('#leave_type_modal_id').val('');
        $('#leave_type_modal_mode').val('add');
        $('#f_leave_type')[0].reset();
        $('#lt_is_active').prop('checked', true);
        $('#lt_requires_approval').prop('checked', false);
        $('#leave_type_modal_title').text('Tambah Leave Type');
        // Auto uppercase for code
        $('#lt_code').on('input', function() {
            this.value = this.value.toUpperCase();
        });
    });

    // Form submit handler
    $('#f_leave_type').on('submit', function(e) {
        e.preventDefault();
        
        var saveBtn = $('#save_leave_type_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var mode = $('#leave_type_modal_mode').val();
        var id = $('#leave_type_modal_id').val();
        
        var url = mode === 'edit' 
            ? "{{ url('leave-types_v2-update') }}/" + id
            : "{{ url('leave-types_v2-store') }}";
        
        // Prepare data object
        var dataObj = {
            lt_code: $('#lt_code').val(),
            lt_name: $('#lt_name').val(),
            lt_description: $('#lt_description').val() || '',
            lt_default_days: $('#lt_default_days').val() || 0,
            lt_default_hours: $('#lt_default_hours').val() || 0,
            lt_unit: $('#lt_unit').val(),
            lt_requires_approval: $('#lt_requires_approval').is(':checked') ? 1 : 0,
            lt_is_active: $('#lt_is_active').is(':checked') ? 1 : 0,
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        if (mode === 'edit') {
            dataObj._method = 'PUT';
        }
        
        $.ajax({
            type: 'POST',
            url: url,
            data: JSON.stringify(dataObj),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (response.success) {
                    closeLeaveTypeModal();
                    Swal.fire('Berhasil', response.message || 'Data berhasil disimpan', 'success');
                    loadLeaveTypeData(leaveTypeCurrentPage);
                } else {
                    Swal.fire('Gagal', response.message || 'Data tidak tersimpan', 'warning');
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

    // Export menu toggle
    $('#export_leave_type_btn').on('click', function(e) {
        e.stopPropagation();
        $('#export_leave_type_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_leave_type_btn, #export_leave_type_menu').length) {
            $('#export_leave_type_menu').addClass('hidden');
        }
    });

    // Export Excel
    $('#export_leave_type_excel_btn').on('click', function() {
        exportToExcel();
        $('#export_leave_type_menu').addClass('hidden');
    });
});

function loadLeaveTypeData(page = 1) {
    leaveTypeCurrentPage = page;
    var search = $('#leave_type_search').val();
    
    $.ajax({
        url: "{{ url('leave-types_v2/datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#leave_type_tbody').html('<tr><td colspan="7" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#leave_type_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="7" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                var startIndex = (response.current_page - 1) * response.per_page;
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(startIndex + index + 1));
                    tr.append($('<td>').addClass('px-3 py-4 font-medium').text(row.lt_code || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.lt_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.lt_description || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.lt_duration || '-'));
                    
                    var statusTd = $('<td>').addClass('px-3 py-4 text-center');
                    var statusBadge = $('<span>').addClass('px-2 py-1 text-xs font-semibold rounded-full ' + (row.lt_status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'));
                    statusBadge.text(row.lt_status || '-');
                    statusTd.append(statusBadge);
                    tr.append(statusTd);
                    
                    var actionTd = $('<td>').addClass('px-3 py-4 text-center');
                    // Use action HTML from server if available, otherwise build it
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
                                viewLeaveType(row.id);
                            });
                        actionHtml += viewBtn[0].outerHTML;
                        
                        // Edit button
                        var editBtn = $('<button>')
                            .addClass('px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded hover:bg-yellow-200')
                            .text('Edit')
                            .on('click', function() {
                                editLeaveType(row.id);
                            });
                        actionHtml += editBtn[0].outerHTML;
                        
                        // Toggle status button
                        var toggleBtn = $('<button>')
                            .addClass('px-2 py-1 text-xs font-medium rounded ' + (row.lt_is_active == 1 ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200'))
                            .text(row.lt_is_active == 1 ? 'Deactivate' : 'Activate')
                            .on('click', function() {
                                toggleLeaveTypeStatus(row.id);
                            });
                        actionHtml += toggleBtn[0].outerHTML;
                        
                        // Delete button
                        var deleteBtn = $('<button>')
                            .addClass('px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200')
                            .text('Delete')
                            .on('click', function() {
                                deleteLeaveType(row.id);
                            });
                        actionHtml += deleteBtn[0].outerHTML;
                        
                        actionHtml += '</div>';
                        actionTd.html(actionHtml);
                    }
                    tr.append(actionTd);
                    tbody.append(tr);
                });
            }

            updatePagination(response.total, response.current_page, response.per_page, response.last_page, 'leave_type');
        },
        error: function() {
            $('#leave_type_tbody').html('<tr><td colspan="7" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function openLeaveTypeModal() {
    $('#LeaveTypeModal').removeClass('hidden');
}

function closeLeaveTypeModal() {
    $('#LeaveTypeModal').addClass('hidden');
}

function editLeaveType(id) {
    $.ajax({
        url: "{{ url('leave-types_v2/show') }}/" + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var data = response.data;
                $('#leave_type_modal_id').val(data.id);
                $('#leave_type_modal_mode').val('edit');
                $('#lt_code').val(data.lt_code);
                $('#lt_name').val(data.lt_name);
                $('#lt_description').val(data.lt_description || '');
                $('#lt_default_days').val(data.lt_default_days || 0);
                $('#lt_default_hours').val(data.lt_default_hours || 0);
                $('#lt_unit').val(data.lt_unit);
                $('#lt_requires_approval').prop('checked', data.lt_requires_approval == 1);
                $('#lt_is_active').prop('checked', data.lt_is_active == 1);
                $('#leave_type_modal_title').text('Edit Leave Type');
                openLeaveTypeModal();
            } else {
                Swal.fire('Error', response.message || 'Gagal memuat data', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
        }
    });
}

function deleteLeaveType(id) {
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
                url: "{{ url('leave-types_v2-delete') }}/" + id,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil', response.message || 'Data berhasil dihapus', 'success');
                        loadLeaveTypeData(leaveTypeCurrentPage);
                    } else {
                        Swal.fire('Gagal', response.message || 'Data tidak terhapus', 'warning');
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

function toggleLeaveTypeStatus(id) {
    $.ajax({
        url: "{{ url('leave-types_v2/toggle-status') }}/" + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                Swal.fire('Berhasil', response.message || 'Status berhasil diubah', 'success');
                loadLeaveTypeData(leaveTypeCurrentPage);
            } else {
                Swal.fire('Gagal', response.message || 'Status tidak berubah', 'warning');
            }
        },
        error: function(xhr) {
            var errorMessage = 'Terjadi kesalahan saat mengubah status';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            Swal.fire('Error', errorMessage, 'error');
        }
    });
}

function viewLeaveType(id) {
    window.location.href = "{{ url('leave-types_v2/detail') }}/" + id;
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
                if (prefix === 'leave_type') loadLeaveTypeData(currentPage - 1);
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
                if (prefix === 'leave_type') loadLeaveTypeData(currentPage + 1);
            }
        });
    controls.append(nextBtn);
}

function createPageBtn(page, prefix, currentPage) {
    var isActive = page === leaveTypeCurrentPage;
    var btn = $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'leave_type') loadLeaveTypeData(page);
        });
    return btn;
}

function exportToExcel() {
    var table = $('#LeaveTypeTable');
    table.table2excel({
        exclude: ".no-export",
        name: "Leave Types",
        filename: "leave_types_" + new Date().toISOString().split('T')[0],
        fileext: ".xlsx"
    });
}
</script>
