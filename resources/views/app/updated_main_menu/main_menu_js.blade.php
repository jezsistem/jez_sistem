<script>
var mainMenuCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadMainMenuData(1);

    // Search with debounce
    var mainMenuSearchTimeout;
    $('#main_menu_search').on('keyup', function() {
        clearTimeout(mainMenuSearchTimeout);
        mainMenuSearchTimeout = setTimeout(function() {
            loadMainMenuData(1);
        }, 500);
    });

    // Add main menu button
    $('#add_main_menu_btn').on('click', function() {
        openMainMenuModal();
        $('#main_menu_modal_id').val('');
        $('#main_menu_modal_mode').val('add');
        $('#f_main_menu')[0].reset();
        $('#delete_main_menu_btn').addClass('hidden');
    });

    // Form submit handler
    $('#f_main_menu').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_main_menu_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('mm_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeMainMenuModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadMainMenuData(mainMenuCurrentPage);
                } else if (data.status == '400') {
                    closeMainMenuModal();
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
    $(document).on('click', '#delete_main_menu_btn', function() {
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
                    data: {_id: $('#main_menu_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('mm_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeMainMenuModal();
                            loadMainMenuData(mainMenuCurrentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Inline sort update handler (event delegation for dynamically created inputs)
    $(document).on('change', '.sort-input', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).attr('data-id');
        var sort = $(this).val();
        
        $.ajax({
            type: "POST",
            data: {
                id: id,
                sort: sort
            },
            dataType: 'json',
            url: "{{ url('mm_update') }}",
            success: function(r) {
                if (r.status == '200') {
                    loadMainMenuData(mainMenuCurrentPage);
                    Swal.fire('Berhasil', 'Data berhasil diupdate', 'success');
                } else {
                    Swal.fire('Gagal', 'Gagal update data', 'error');
                }
            },
            error: function() {
                Swal.fire('Gagal', 'Gagal update data', 'error');
            }
        });
    });

    // Export handlers
    $('#export_main_menu_btn').on('click', function() {
        $('#export_main_menu_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_main_menu_btn, #export_main_menu_menu').length) {
            $('#export_main_menu_menu').addClass('hidden');
        }
    });

    $('#export_main_menu_excel_btn').on('click', function() {
        jQuery("#MainMenuTable").table2excel({
            filename: "Main Menu",
        });
    });
});

function loadMainMenuData(page = 1) {
    mainMenuCurrentPage = page;
    var search = $('#main_menu_search').val();
    
    $.ajax({
        url: "{{ url('main_menu_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#main_menu_tbody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#main_menu_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.DT_RowIndex));
                    tr.append($('<td>').addClass('px-3 py-4 cursor-pointer').text(row.mt_title).on('click', function() {
                        editMainMenu(row);
                    }));
                    var sortTd = $('<td>').addClass('px-3 py-4');
                    sortTd.append($(row.mt_sort_input));
                    tr.append(sortTd);
                    tbody.append(tr);
                });
            }

            createCustomPagination('#MainMenuTable', response.total, response.current_page, response.total_pages, response.per_page, loadMainMenuData, 'main_menu');
        },
        error: function() {
            $('#main_menu_tbody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editMainMenu(row) {
    openMainMenuModal();
    $('#main_menu_modal_id').val(row.id);
    $('#main_menu_modal_mode').val('edit');
    $('#mt_title').val(row.mt_title);
    $('#mt_sort').val(row.mt_sort);
    $('#delete_main_menu_btn').removeClass('hidden');
}

function openMainMenuModal() {
    $('#MainMenuModal').removeClass('hidden');
}

function closeMainMenuModal() {
    $('#MainMenuModal').addClass('hidden');
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
