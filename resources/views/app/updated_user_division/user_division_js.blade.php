<script>
var userDivisionCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadUserDivisionData(1);

    // Search with debounce
    var userDivisionSearchTimeout;
    $('#user_division_search').on('keyup', function() {
        clearTimeout(userDivisionSearchTimeout);
        userDivisionSearchTimeout = setTimeout(function() {
            loadUserDivisionData(1);
        }, 500);
    });

    // Add button handler
    $('#add_user_division_btn').on('click', function() {
        openUserDivisionModal();
        $('#user_division_modal_id').val('');
        $('#user_division_modal_mode').val('add');
        $('#f_user_division')[0].reset();
        $('#user_division_modal_title').text('Tambah User Division');
        loadLeaderAndManagerOptions();
    });

    // Form submit handler
    $('#f_user_division').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_user_division_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        var mode = $('#user_division_modal_mode').val();
        var id = $('#user_division_modal_id').val();
        
        var url = mode === 'edit' 
            ? "{{ url('user-divisions') }}/" + id
            : "{{ url('user-divisions') }}";
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
                if (data.success) {
                    closeUserDivisionModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadUserDivisionData(userDivisionCurrentPage);
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
    $('#export_user_division_btn').on('click', function() {
        $('#export_user_division_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_user_division_btn, #export_user_division_menu').length) {
            $('#export_user_division_menu').addClass('hidden');
        }
    });

    $('#export_user_division_excel_btn').on('click', function() {
        jQuery("#UserDivisionTable").table2excel({
            filename: "User Divisions",
        });
    });
});

// Delete handler
$(document).on('click', '.delete-user-division-btn', function() {
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
                url: "{{ url('user-divisions') }}/" + id,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(r) {
                    if (r.success) {
                        Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                        loadUserDivisionData(userDivisionCurrentPage);
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
    editUserDivision(id);
});

function loadUserDivisionData(page = 1) {
    userDivisionCurrentPage = page;
    var search = $('#user_division_search').val();
    
    $.ajax({
        url: "{{ url('user-divisions_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#user_division_tbody').html('<tr><td colspan="8" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#user_division_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="8" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.DT_RowIndex));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ud_code || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ud_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ud_description || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.leader_name || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.manager_name || '-'));
                    var statusTd = $('<td>').addClass('px-3 py-4 text-center');
                    statusTd.html(row.ud_status_display || '-');
                    tr.append(statusTd);
                    var actionTd = $('<td>').addClass('px-3 py-4 text-center');
                    actionTd.html(row.action || '');
                    tr.append(actionTd);
                    tbody.append(tr);
                });
            }

            createCustomPagination('#UserDivisionTable', response.total, response.current_page, response.total_pages, response.per_page, loadUserDivisionData, 'user_division');
        },
        error: function() {
            $('#user_division_tbody').html('<tr><td colspan="8" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editUserDivision(id) {
    $.ajax({
        url: "{{ url('user-divisions') }}/" + id,
        type: 'GET',
        success: function(response) {
            if (response && response.division) {
                var div = response.division;
                openUserDivisionModal();
                $('#user_division_modal_id').val(div.id);
                $('#user_division_modal_mode').val('edit');
                $('#ud_code').val(div.ud_code || '');
                $('#ud_name').val(div.ud_name || '');
                $('#ud_description').val(div.ud_description || '');
                $('#ud_status').val(div.ud_status || 'active');
                loadLeaderAndManagerOptions(function() {
                    $('#lead_id').val(div.lead_id || '');
                    $('#manager_id').val(div.manager_id || '');
                });
                $('#user_division_modal_title').text('Edit User Division');
            } else {
                Swal.fire('Error', 'Data tidak ditemukan', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
        }
    });
}

function loadLeaderAndManagerOptions(callback) {
    // Load leader options (SUPERVISOR, DIREKTUR)
    $.ajax({
        url: "{{ url('user-divisions/leader-options') }}",
        type: 'GET',
        success: function(leaders) {
            var leaderHtml = '<option value="">Select Leader</option>';
            if (leaders && leaders.length > 0) {
                leaders.forEach(function(leader) {
                    leaderHtml += '<option value="' + leader.id + '">' + leader.u_name + '</option>';
                });
            }
            $('#lead_id').html(leaderHtml);
            
            // Load manager options (MANAGER, DIREKTUR)
            $.ajax({
                url: "{{ url('user-divisions/manager-options') }}",
                type: 'GET',
                success: function(managers) {
                    var managerHtml = '<option value="">Select Manager</option>';
                    if (managers && managers.length > 0) {
                        managers.forEach(function(manager) {
                            managerHtml += '<option value="' + manager.id + '">' + manager.u_name + '</option>';
                        });
                    }
                    $('#manager_id').html(managerHtml);
                    
                    if (callback) callback();
                },
                error: function() {
                    if (callback) callback();
                }
            });
        },
        error: function() {
            if (callback) callback();
        }
    });
}

function openUserDivisionModal() {
    $('#UserDivisionModal').removeClass('hidden');
}

function closeUserDivisionModal() {
    $('#UserDivisionModal').addClass('hidden');
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
