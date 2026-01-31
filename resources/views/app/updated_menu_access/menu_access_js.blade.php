<script>
var menuAccessCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadMenuAccessData(1);

    // Search with debounce
    var menuAccessSearchTimeout;
    $('#menu_access_search').on('keyup', function() {
        clearTimeout(menuAccessSearchTimeout);
        menuAccessSearchTimeout = setTimeout(function() {
            loadMenuAccessData(1);
        }, 500);
    });

    // Filter change handler
    $('#mt_id_filter').on('change', function() {
        loadMenuAccessData(1);
    });

    // Add menu access button
    $('#add_menu_access_btn').on('click', function() {
        openMenuAccessModal();
        $('#menu_access_modal_id').val('');
        $('#menu_access_modal_mode').val('add');
        $('#f_menu_access')[0].reset();
        $('#delete_menu_access_btn').addClass('hidden');
    });

    // Form submit handler
    $('#f_menu_access').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_menu_access_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('ma_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeMenuAccessModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadMenuAccessData(menuAccessCurrentPage);
                } else if (data.status == '400') {
                    closeMenuAccessModal();
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Delete handler
    $(document).on('click', '#delete_menu_access_btn', function() {
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
                    type: "POST",
                    data: {_id: $('#menu_access_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('ma_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeMenuAccessModal();
                            loadMenuAccessData(menuAccessCurrentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Export handlers
    $('#export_menu_access_btn').on('click', function() {
        $('#export_menu_access_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_menu_access_btn, #export_menu_access_menu').length) {
            $('#export_menu_access_menu').addClass('hidden');
        }
    });

    $('#export_menu_access_excel_btn').on('click', function() {
        jQuery("#MenuAccessTable").table2excel({
            filename: "Menu Access",
        });
    });
});

function loadMenuAccessData(page = 1) {
    menuAccessCurrentPage = page;
    var search = $('#menu_access_search').val();
    var mt_id_filter = $('#mt_id_filter').val();
    
    $.ajax({
        url: "{{ url('menu_access_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            mt_id: mt_id_filter
        },
        success: function(response) {
            if (response.error) {
                $('#menu_access_tbody').html('<tr><td colspan="5" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#menu_access_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.DT_RowIndex));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.mt_title || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ma_title || '-').on('click', function() {
                        editMenuAccess(row);
                    }));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ma_sort || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ma_slug || '-'));
                    tbody.append(tr);
                });
            }

            createCustomPagination('#MenuAccessTable', response.total, response.current_page, response.total_pages, response.per_page, loadMenuAccessData, 'menu_access');
        },
        error: function() {
            $('#menu_access_tbody').html('<tr><td colspan="5" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editMenuAccess(row) {
    openMenuAccessModal();
    $('#menu_access_modal_id').val(row.id);
    $('#menu_access_modal_mode').val('edit');
    $('#mt_id').val(row.mt_id);
    $('#ma_title').val(row.ma_title);
    $('#ma_slug').val(row.ma_slug);
    $('#ma_sort').val(row.ma_sort);
    $('#delete_menu_access_btn').removeClass('hidden');
}

function openMenuAccessModal() {
    $('#MenuAccessModal').removeClass('hidden');
}

function closeMenuAccessModal() {
    $('#MenuAccessModal').addClass('hidden');
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
