<script>
var positionAccessCurrentPage = 1;
var perPage = 25;
var canUpdate = @if(function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'update')) true @else false @endif;
var canDelete = @if(function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'delete')) true @else false @endif;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadPositionAccessData(1);

    // Search with debounce
    var positionAccessSearchTimeout;
    $('#position_access_search').on('keyup', function() {
        clearTimeout(positionAccessSearchTimeout);
        positionAccessSearchTimeout = setTimeout(function() {
            loadPositionAccessData(1);
        }, 500);
    });

    // Add position access button
    $('#add_position_access_btn').on('click', function() {
        openPositionAccessModal();
        $('#position_access_modal_id').val('');
        $('#position_access_modal_mode').val('add');
        $('#f_position_access')[0].reset();
        reloadPosition();
    });

    // Form submit handler
    $('#f_position_access').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_position_access_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('position-access_v2-save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == 200) {
                    closePositionAccessModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadPositionAccessData(positionAccessCurrentPage);
                } else {
                    closePositionAccessModal();
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Toggle switch handler (event delegation for dynamically created elements)
    $(document).on('change', '.switch-access', function() {
        if (!canUpdate) {
            $(this).prop('checked', !$(this).prop('checked'));
            Swal.fire('Error', 'Anda tidak memiliki akses untuk mengubah', 'error');
            return;
        }

        var isChecked = $(this).is(':checked');
        var action = $(this).data('action');
        var positionId = $(this).data('position-id');

        $.ajax({
            type: 'POST',
            url: "{{ url('change_access_v2') }}",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                checked: isChecked,
                action: action,
                position_id: positionId,
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == 200) {
                    loadPositionAccessData(positionAccessCurrentPage);
                } else {
                    Swal.fire('Error', 'Gagal mengubah status akses', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan saat mengubah status akses', 'error');
            }
        });
    });

    // Delete button handler (event delegation)
    $(document).on('click', '.delete-position-btn', function() {
        if (!canDelete) {
            Swal.fire('Error', 'Anda tidak memiliki akses untuk menghapus', 'error');
            return;
        }

        var positionId = $(this).data('position-id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: "{{ url('position-access_v2-delete') }}/" + positionId,
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 200) {
                            Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                            loadPositionAccessData(positionAccessCurrentPage);
                        } else {
                            Swal.fire('Error!', 'Gagal menghapus data.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                    }
                });
            }
        });
    });

    // Export handlers
    $('#export_position_access_btn').on('click', function() {
        $('#export_position_access_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_position_access_btn, #export_position_access_menu').length) {
            $('#export_position_access_menu').addClass('hidden');
        }
    });

    $('#export_position_access_excel_btn').on('click', function() {
        jQuery("#PositionAccessTable").table2excel({
            filename: "Position Access",
        });
    });
});

function loadPositionAccessData(page = 1) {
    positionAccessCurrentPage = page;
    var search = $('#position_access_search').val();
    
    $.ajax({
        url: "{{ url('position-access_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#position_access_tbody').html('<tr><td colspan="4" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#position_access_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="4" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.DT_RowIndex));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.up_name || '-'));
                    var aksesTd = $('<td>').addClass('px-3 py-4');
                    aksesTd.html(row.akses);
                    tr.append(aksesTd);
                    var actionsTd = $('<td>').addClass('px-3 py-4');
                    actionsTd.html(row.actions || '');
                    tr.append(actionsTd);
                    tbody.append(tr);
                });
            }

            createCustomPagination('#PositionAccessTable', response.total, response.current_page, response.total_pages, response.per_page, loadPositionAccessData, 'position_access');
        },
        error: function() {
            $('#position_access_tbody').html('<tr><td colspan="4" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function reloadPosition() {
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "{{ url('reload_position_v2') }}",
        success: function (r) {
            $('#position_access_div').html(r);
        }
    });
}

function openPositionAccessModal() {
    $('#PositionAccessModal').removeClass('hidden');
}

function closePositionAccessModal() {
    $('#PositionAccessModal').addClass('hidden');
}

// Pagination function
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
