<script>
var userTypeCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadUserTypeData(1);

    // Search with debounce
    var userTypeSearchTimeout;
    $('#user_type_search').on('keyup', function() {
        clearTimeout(userTypeSearchTimeout);
        userTypeSearchTimeout = setTimeout(function() {
            loadUserTypeData(1);
        }, 500);
    });

    // Add button handler
    $('#add_user_type_btn').on('click', function() {
        openUserTypeModal();
        $('#user_type_modal_id').val('');
        $('#user_type_modal_mode').val('add');
        $('#f_user_type')[0].reset();
        $('#user_type_modal_title').text('Tambah User Type');
    });

    // Form submit handler
    $('#f_user_type').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_user_type_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = {
            ut_code: $('#ut_code').val(),
            ut_name: $('#ut_name').val(),
            ut_description: $('#ut_description').val(),
            ut_status: $('#ut_status').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        var mode = $('#user_type_modal_mode').val();
        var id = $('#user_type_modal_id').val();
        
        var url = mode === 'edit' 
            ? "{{ url('user-types') }}/" + id
            : "{{ url('user-types') }}";
        var method = mode === 'edit' ? 'PUT' : 'POST';
        
        if (mode === 'edit') {
            formData._method = 'PUT';
        }
        
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            dataType: 'json',
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.success) {
                    closeUserTypeModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadUserTypeData(userTypeCurrentPage);
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
    $('#export_user_type_btn').on('click', function() {
        $('#export_user_type_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_user_type_btn, #export_user_type_menu').length) {
            $('#export_user_type_menu').addClass('hidden');
        }
    });

    $('#export_user_type_excel_btn').on('click', function() {
        jQuery("#UserTypeTable").table2excel({
            filename: "User Types",
        });
    });
});

// Delete handler
$(document).on('click', '.delete-user-type-btn', function() {
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
                url: "{{ url('user-types') }}/" + id,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(r) {
                    if (r.success) {
                        Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                        loadUserTypeData(userTypeCurrentPage);
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

// Event delegation for detail button
$(document).on('click', '.btn-detail', function() {
    var id = $(this).data('id');
    editUserType(id);
});

function loadUserTypeData(page = 1) {
    userTypeCurrentPage = page;
    var search = $('#user_type_search').val();
    
    $.ajax({
        url: "{{ url('user-types_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#user_type_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#user_type_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.DT_RowIndex));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ut_code || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ut_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ut_description || '-'));
                    var statusTd = $('<td>').addClass('px-3 py-4 text-center');
                    statusTd.html(row.ut_status_display || '-');
                    tr.append(statusTd);
                    var actionTd = $('<td>').addClass('px-3 py-4 text-center');
                    actionTd.html(row.action || '');
                    tr.append(actionTd);
                    tbody.append(tr);
                });
            }

            createCustomPagination('#UserTypeTable', response.total, response.current_page, response.total_pages, response.per_page, loadUserTypeData, 'user_type');
        },
        error: function() {
            $('#user_type_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editUserType(id) {
    $.ajax({
        url: "{{ url('user-types') }}/" + id,
        type: 'GET',
        success: function(response) {
            if (response && response.success && response.data) {
                var ut = response.data;
                openUserTypeModal();
                $('#user_type_modal_id').val(ut.id);
                $('#user_type_modal_mode').val('edit');
                $('#ut_code').val(ut.ut_code || '');
                $('#ut_name').val(ut.ut_name || '');
                $('#ut_description').val(ut.ut_description || '');
                $('#ut_status').val(ut.ut_status || 'active');
                $('#user_type_modal_title').text('Edit User Type');
            } else {
                Swal.fire('Error', 'Data tidak ditemukan', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
        }
    });
}

function openUserTypeModal() {
    $('#UserTypeModal').removeClass('hidden');
}

function closeUserTypeModal() {
    $('#UserTypeModal').addClass('hidden');
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
