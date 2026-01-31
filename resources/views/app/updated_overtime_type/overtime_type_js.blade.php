<script>
var overtimeTypeCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadOvertimeTypeData(1);

    // Search with debounce
    var overtimeTypeSearchTimeout;
    $('#overtime_type_search').on('keyup', function() {
        clearTimeout(overtimeTypeSearchTimeout);
        overtimeTypeSearchTimeout = setTimeout(function() {
            loadOvertimeTypeData(1);
        }, 500);
    });

    // Add button handler
    $('#add_overtime_type_btn').on('click', function() {
        openOvertimeTypeModal();
        $('#_id').val('');
        $('#_mode').val('add');
        $('#f_overtime_type')[0].reset();
        $('#delete_overtime_type_btn').addClass('hidden');
    });

    // Close modal handlers
    $('#close_overtime_type_modal, #cancel_overtime_type_btn').on('click', function() {
        closeOvertimeTypeModal();
    });

    // Form submit handler
    $('#f_overtime_type').on('submit', function(e) {
        e.preventDefault();
        
        var saveBtn = $('#save_overtime_type_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('overtime_type_v2/store') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (response.success) {
                    closeOvertimeTypeModal();
                    Swal.fire('Berhasil', response.message || 'Data berhasil disimpan', 'success');
                    loadOvertimeTypeData(overtimeTypeCurrentPage);
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

    // Delete button handler
    $('#delete_overtime_type_btn').on('click', function() {
        var id = $('#_id').val();
        if (!id) return;

        Swal.fire({
            title: 'Hapus..?',
            text: 'Yakin hapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: "{{ url('overtime_type_v2/delete') }}",
                    data: {
                        _id: id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            closeOvertimeTypeModal();
                            Swal.fire('Berhasil', response.message || 'Data berhasil dihapus', 'success');
                            loadOvertimeTypeData(overtimeTypeCurrentPage);
                        } else {
                            Swal.fire('Gagal', response.message || 'Gagal hapus data', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan saat menghapus data', 'error');
                    }
                });
            }
        });
    });

    // Check exists on name change
    $('#ot_name').on('change', function() {
        var ot_name = $(this).val();
        if (!ot_name) return;

        $.ajax({
            type: 'POST',
            url: "{{ url('overtime_type_v2/check_exists') }}",
            data: {
                _ot_name: ot_name,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.exists) {
                    Swal.fire('Peringatan', 'Data Overtime Type sudah ada di sistem, silahkan ganti dengan yang lain', 'warning');
                    $('#ot_name').val('');
                }
            }
        });
    });

    // Export menu toggle
    $('#export_overtime_type_btn').on('click', function(e) {
        e.stopPropagation();
        $('#export_overtime_type_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_overtime_type_btn, #export_overtime_type_menu').length) {
            $('#export_overtime_type_menu').addClass('hidden');
        }
    });

    // Export Excel
    $('#export_overtime_type_excel_btn').on('click', function() {
        exportToExcel();
        $('#export_overtime_type_menu').addClass('hidden');
    });
});

function loadOvertimeTypeData(page = 1) {
    overtimeTypeCurrentPage = page;
    var search = $('#overtime_type_search').val();
    
    $.ajax({
        url: "{{ url('overtime_type_v2/datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#overtime_type_tbody').html('<tr><td colspan="4" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#overtime_type_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="4" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                var startIndex = (response.current_page - 1) * response.per_page;
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(startIndex + index + 1));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ot_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ot_desc || '-'));
                    
                    // Action column
                    var actionTd = $('<td>').addClass('px-3 py-4');
                    var actionDiv = $('<div>').addClass('flex items-center justify-center gap-2');
                    
                    @php
                        $canUpdate = function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'update');
                        $canDelete = function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'delete');
                    @endphp
                    
                    @if($canUpdate)
                    actionDiv.append($('<button>')
                        .addClass('px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded hover:bg-yellow-200')
                        .text('Edit')
                        .on('click', function() {
                            editOvertimeType(row.id);
                        }));
                    @endif
                    
                    @if($canDelete)
                    actionDiv.append($('<button>')
                        .addClass('px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200')
                        .text('Delete')
                        .on('click', function() {
                            deleteOvertimeType(row.id);
                        }));
                    @endif
                    
                    actionTd.append(actionDiv);
                    tr.append(actionTd);
                    
                    tbody.append(tr);
                });
            }

            updatePagination(response.total, response.current_page, response.per_page, response.last_page, 'overtime_type');
        },
        error: function() {
            $('#overtime_type_tbody').html('<tr><td colspan="4" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editOvertimeType(id) {
    $.ajax({
        url: "{{ url('overtime_type_v2/get') }}/" + id,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                var data = response.data;
                $('#_id').val(data.id);
                $('#_mode').val('edit');
                $('#ot_name').val(data.ot_name || '');
                $('#ot_desc').val(data.ot_desc || '');
                @if($canDelete)
                $('#delete_overtime_type_btn').removeClass('hidden');
                @endif
                openOvertimeTypeModal();
            }
        },
        error: function() {
            Swal.fire('Error', 'Gagal memuat data', 'error');
        }
    });
}

function deleteOvertimeType(id) {
    Swal.fire({
        title: 'Hapus..?',
        text: 'Yakin hapus data ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batalkan'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: "{{ url('overtime_type_v2/delete') }}",
                data: {
                    _id: id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil', response.message || 'Data berhasil dihapus', 'success');
                        loadOvertimeTypeData(overtimeTypeCurrentPage);
                    } else {
                        Swal.fire('Gagal', response.message || 'Gagal hapus data', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan saat menghapus data', 'error');
                }
            });
        }
    });
}

function openOvertimeTypeModal() {
    $('#OvertimeTypeModal').removeClass('hidden');
}

function closeOvertimeTypeModal() {
    $('#OvertimeTypeModal').addClass('hidden');
    $('#f_overtime_type')[0].reset();
    $('#_id').val('');
    $('#_mode').val('');
    $('#delete_overtime_type_btn').addClass('hidden');
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
                if (prefix === 'overtime_type') loadOvertimeTypeData(currentPage - 1);
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
                if (prefix === 'overtime_type') loadOvertimeTypeData(currentPage + 1);
            }
        });
    controls.append(nextBtn);
}

function createPageBtn(page, prefix, currentPage) {
    var isActive = page === overtimeTypeCurrentPage;
    return $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            if (prefix === 'overtime_type') loadOvertimeTypeData(page);
        });
}

function exportToExcel() {
    var table = $('#OvertimeTypeTable');
    if (table.length === 0) {
        Swal.fire('Error', 'Tabel tidak ditemukan.', 'error');
        return;
    }
    
    if (typeof $.fn.table2excel === 'undefined') {
        Swal.fire('Error', 'Export library belum dimuat. Silakan refresh halaman.', 'error');
        return;
    }
    
    table.table2excel({
        exclude: ".no-export",
        name: "Overtime Types",
        filename: "overtime_types_" + new Date().toISOString().split('T')[0],
        fileext: ".xlsx"
    });
}
</script>
