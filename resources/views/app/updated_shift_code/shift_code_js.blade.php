<script>
var shiftCodeCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadShiftCodeData(1);

    // Search with debounce
    var shiftCodeSearchTimeout;
    $('#shift_code_search').on('keyup', function() {
        clearTimeout(shiftCodeSearchTimeout);
        shiftCodeSearchTimeout = setTimeout(function() {
            loadShiftCodeData(1);
        }, 500);
    });

    // Add button handler
    $('#add_shift_code_btn').on('click', function() {
        openShiftCodeModal();
        $('#shift_code_modal_id').val('');
        $('#shift_code_modal_mode').val('add');
        $('#f_shift_code')[0].reset();
        $('.user-type-checkbox').prop('checked', false);
        $('#selectAllUserTypes').prop('checked', false).prop('indeterminate', false);
        updateUserTypeCount();
        $('#shift_code_modal_title').text('Tambah Shift Code');
        // Auto uppercase for shift code
        $('#sc_code').on('input', function() {
            this.value = this.value.toUpperCase();
        });
    });

    // Form submit handler
    $('#f_shift_code').on('submit', function(e) {
        e.preventDefault();
        
        // Validate user types
        var checkedUserTypes = $('.user-type-checkbox:checked');
        if (checkedUserTypes.length === 0) {
            $('#user_type_error').removeClass('hidden');
            return;
        }
        $('#user_type_error').addClass('hidden');
        
        var saveBtn = $('#save_shift_code_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        var mode = $('#shift_code_modal_mode').val();
        var id = $('#shift_code_modal_id').val();
        
        // Collect user type IDs
        var userTypeIds = [];
        $('.user-type-checkbox:checked').each(function() {
            userTypeIds.push($(this).val());
        });
        
        var url = mode === 'edit' 
            ? "{{ url('shift-codes_v2-update') }}/" + id
            : "{{ url('shift-codes_v2-store') }}";
        
        // Prepare data object
        var dataObj = {
            sc_code: $('#sc_code').val(),
            sc_description: $('#sc_description').val(),
            sc_shift_name: $('#sc_shift_name').val(),
            sc_start_time: $('#sc_start_time').val() || null,
            sc_end_time: $('#sc_end_time').val() || null,
            user_type_ids: userTypeIds,
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
                    closeShiftCodeModal();
                    Swal.fire('Berhasil', response.message || 'Data berhasil disimpan', 'success');
                    loadShiftCodeData(shiftCodeCurrentPage);
                } else {
                    Swal.fire('Gagal', response.message || 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(xhr) {
                saveBtn.html('Simpan').prop('disabled', false);
                var errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).map(e => Array.isArray(e) ? e.join(', ') : e).join('; ');
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        });
    });

    // Export handlers
    $('#export_shift_code_btn').on('click', function() {
        $('#export_shift_code_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_shift_code_btn, #export_shift_code_menu').length) {
            $('#export_shift_code_menu').addClass('hidden');
        }
    });

    $('#export_shift_code_excel_btn').on('click', function() {
        jQuery("#ShiftCodeTable").table2excel({
            filename: "Shift Codes",
        });
    });
});

// Event delegation for buttons
$(document).on('click', '.btn-edit', function() {
    var id = $(this).data('id');
    editShiftCode(id);
});

$(document).on('click', '.btn-view', function() {
    var id = $(this).data('id');
    viewShiftCode(id);
});

$(document).on('click', '.btn-delete', function() {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Hapus..?',
        text: 'Yakin hapus data ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batalkan'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "DELETE",
                url: "{{ url('shift-codes_v2-delete') }}/" + id,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(r) {
                    if (r.success) {
                        Swal.fire('Berhasil', r.message || 'Data berhasil dihapus', 'success');
                        loadShiftCodeData(shiftCodeCurrentPage);
                    } else {
                        Swal.fire('Gagal', r.message || 'Gagal hapus data', 'error');
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Gagal hapus data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Gagal', errorMsg, 'error');
                }
            });
        }
    });
});

function loadShiftCodeData(page = 1) {
    shiftCodeCurrentPage = page;
    var search = $('#shift_code_search').val();
    
    $.ajax({
        url: "{{ url('shift-codes_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#shift_code_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#shift_code_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="9" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.DT_RowIndex));
                    tr.append($('<td>').addClass('px-3 py-4 font-medium').text(row.sc_code || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.sc_description || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.sc_shift_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.sc_start_time_display || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.sc_end_time_display || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.compatible_user_types || '-'));
                    var statusTd = $('<td>').addClass('px-3 py-4 text-center');
                    statusTd.html(row.sc_status_display || '-');
                    tr.append(statusTd);
                    var actionTd = $('<td>').addClass('px-3 py-4 text-center');
                    actionTd.html(row.action || '');
                    tr.append(actionTd);
                    tbody.append(tr);
                });
            }

            updatePagination(response.total, response.current_page, response.per_page, response.total_pages, 'shift_code');
        },
        error: function() {
            $('#shift_code_tbody').html('<tr><td colspan="9" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editShiftCode(id) {
    $.ajax({
        url: "{{ url('shift-codes_v2') }}/" + id,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                var shiftCode = response.data;
                openShiftCodeModal();
                $('#shift_code_modal_id').val(shiftCode.id);
                $('#shift_code_modal_mode').val('edit');
                $('#sc_code').val(shiftCode.sc_code || '');
                $('#sc_description').val(shiftCode.sc_description || '');
                $('#sc_shift_name').val(shiftCode.sc_shift_name || '');
                $('#sc_start_time').val(shiftCode.sc_start_time ? shiftCode.sc_start_time.substring(0, 5) : '');
                $('#sc_end_time').val(shiftCode.sc_end_time ? shiftCode.sc_end_time.substring(0, 5) : '');
                
                // Clear all checkboxes first
                $('.user-type-checkbox').prop('checked', false);
                
                // Check user types
                if (shiftCode.user_types && shiftCode.user_types.length > 0) {
                    shiftCode.user_types.forEach(function(userType) {
                        $('#user_type_' + userType.id).prop('checked', true);
                    });
                }
                
                updateUserTypeCount();
                $('#shift_code_modal_title').text('Edit Shift Code');
                
                // Auto uppercase for shift code
                $('#sc_code').on('input', function() {
                    this.value = this.value.toUpperCase();
                });
            } else {
                Swal.fire('Error', response.message || 'Data tidak ditemukan', 'error');
            }
        },
        error: function(xhr) {
            var errorMsg = 'Terjadi kesalahan saat memuat data';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire('Error', errorMsg, 'error');
        }
    });
}

function viewShiftCode(id) {
    editShiftCode(id); // For now, view uses same modal as edit
}

function openShiftCodeModal() {
    $('#ShiftCodeModal').removeClass('hidden');
}

function closeShiftCodeModal() {
    $('#ShiftCodeModal').addClass('hidden');
    $('#f_shift_code')[0].reset();
    $('.user-type-checkbox').prop('checked', false);
    $('#selectAllUserTypes').prop('checked', false).prop('indeterminate', false);
    updateUserTypeCount();
    $('#user_type_error').addClass('hidden');
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
                if (prefix === 'shift_code') loadShiftCodeData(currentPage - 1);
            }
        });
    controls.append(prevBtn);

    // Page numbers
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, currentPage + 2);

    // First page
    if (startPage > 1) {
        controls.append(createPageBtn(1, prefix));
        if (startPage > 2) {
            controls.append($('<span>').addClass('px-2 text-sm text-gray-500').text('...'));
        }
    }

    // Middle pages
    for (var i = startPage; i <= endPage; i++) {
        controls.append(createPageBtn(i, prefix));
    }

    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            controls.append($('<span>').addClass('px-2 text-sm text-gray-500').text('...'));
        }
        controls.append(createPageBtn(totalPages, prefix));
    }

    // Next button
    var nextBtn = $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (currentPage === totalPages ? 'border-gray-200 text-gray-400 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .html('›')
        .prop('disabled', currentPage === totalPages)
        .on('click', function() {
            if (currentPage < totalPages) {
                if (prefix === 'shift_code') loadShiftCodeData(currentPage + 1);
            }
        });
    controls.append(nextBtn);
}

function createPageBtn(page, prefix) {
    var isActive = page === shiftCodeCurrentPage;
    return $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'shift_code') loadShiftCodeData(page);
        });
}
</script>
