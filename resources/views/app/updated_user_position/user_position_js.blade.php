<script>
var userPositionCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadUserPositionData(1);

    // Search with debounce
    var userPositionSearchTimeout;
    $('#user_position_search').on('keyup', function() {
        clearTimeout(userPositionSearchTimeout);
        userPositionSearchTimeout = setTimeout(function() {
            loadUserPositionData(1);
        }, 500);
    });

    // Add button handler
    $('#add_user_position_btn').on('click', function() {
        openUserPositionModal();
        $('#user_position_modal_id').val('');
        $('#user_position_modal_mode').val('add');
        $('#f_user_position')[0].reset();
        $('#up_color').val('#3699FF');
        $('#up_is_active').prop('checked', true);
        $('#user_position_modal_title').text('Tambah User Position');
    });

    // Form submit handler
    $('#f_user_position').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_user_position_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        var mode = $('#user_position_modal_mode').val();
        var id = $('#user_position_modal_id').val();
        
        // Handle checkboxes
        formData.set('up_can_approve_leave', $('#up_can_approve_leave').is(':checked') ? '1' : '0');
        formData.set('up_is_active', $('#up_is_active').is(':checked') ? '1' : '0');
        
        var url = mode === 'edit' 
            ? "{{ url('user-positions') }}/" + id
            : "{{ url('user-positions') }}";
        var method = mode === 'edit' ? 'PUT' : 'POST';
        
        if (mode === 'edit') {
            formData.append('_method', 'PUT');
        }
        
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.success || (mode === 'edit' && !data.error)) {
                    closeUserPositionModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadUserPositionData(userPositionCurrentPage);
                } else {
                    Swal.fire('Gagal', data.message || 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(xhr) {
                saveBtn.html('Simpan').prop('disabled', false);
                var errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        });
    });

    // Export handlers
    $('#export_user_position_btn').on('click', function() {
        $('#export_user_position_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_user_position_btn, #export_user_position_menu').length) {
            $('#export_user_position_menu').addClass('hidden');
        }
    });

    $('#export_user_position_excel_btn').on('click', function() {
        jQuery("#UserPositionTable").table2excel({
            filename: "User Positions",
        });
    });
});

// Event delegation for detail button
$(document).on('click', '.btn-detail', function() {
    var id = $(this).data('id');
    editUserPosition(id);
});

// Delete handler
$(document).on('click', '.delete-user-position-btn', function() {
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
                url: "{{ url('user-positions') }}/" + id,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(r) {
                    if (r.success) {
                        Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                        loadUserPositionData(userPositionCurrentPage);
                    } else {
                        Swal.fire('Gagal', r.message || 'Gagal hapus data', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Gagal', 'Gagal hapus data', 'error');
                }
            });
        }
    });
});

function loadUserPositionData(page = 1) {
    userPositionCurrentPage = page;
    var search = $('#user_position_search').val();
    
    $.ajax({
        url: "{{ url('user-positions_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#user_position_tbody').html('<tr><td colspan="7" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#user_position_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="7" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.DT_RowIndex));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.up_code || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.up_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.up_description || '-'));
                    tr.append($('<td>').addClass('px-3 py-4 text-center').text(row.up_level || '-'));
                    var statusTd = $('<td>').addClass('px-3 py-4 text-center');
                    statusTd.html(row.up_is_active_display || '-');
                    tr.append(statusTd);
                    var actionTd = $('<td>').addClass('px-3 py-4 text-center');
                    actionTd.html(row.action || '');
                    tr.append(actionTd);
                    tbody.append(tr);
                });
            }

            createCustomPagination('#UserPositionTable', response.total, response.current_page, response.total_pages, response.per_page, loadUserPositionData, 'user_position');
        },
        error: function() {
            $('#user_position_tbody').html('<tr><td colspan="7" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editUserPosition(id) {
    $.ajax({
        url: "{{ url('user-positions') }}/" + id,
        type: 'GET',
        success: function(response) {
            if (response && response.position) {
                var pos = response.position;
                openUserPositionModal();
                $('#user_position_modal_id').val(pos.id);
                $('#user_position_modal_mode').val('edit');
                $('#up_code').val(pos.up_code || '');
                $('#up_name').val(pos.up_name || '');
                $('#up_description').val(pos.up_description || '');
                $('#up_level').val(pos.up_level || '');
                $('#up_color').val(pos.up_color || '#3699FF');
                $('#up_can_approve_leave').prop('checked', pos.up_can_approve_leave == 1);
                $('#up_is_active').prop('checked', pos.up_is_active == 1);
                $('#user_position_modal_title').text('Edit User Position');
            } else {
                Swal.fire('Error', 'Data tidak ditemukan', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
        }
    });
}

function openUserPositionModal() {
    $('#UserPositionModal').removeClass('hidden');
}

function closeUserPositionModal() {
    $('#UserPositionModal').addClass('hidden');
}

// Pagination function (reuse from main_menu_js)
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
