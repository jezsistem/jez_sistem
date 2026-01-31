<script>
var webConfigCurrentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadWebConfigData(1);

    // Search with debounce
    var webConfigSearchTimeout;
    $('#web_config_search').on('keyup', function() {
        clearTimeout(webConfigSearchTimeout);
        webConfigSearchTimeout = setTimeout(function() {
            loadWebConfigData(1);
        }, 500);
    });

    // Form submit handler
    $('#f_web_config').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_web_config_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('perp_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeWebConfigModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadWebConfigData(webConfigCurrentPage);
                } else if (data.status == '400') {
                    closeWebConfigModal();
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Reset ERP button
    $('#reset_erp_btn').on('click', function() {
        Swal.fire({
            title: 'YAKIN RESET ?',
            text: 'SELURUH DATA PADA ERP AKAN DIKOSONGKAN KECUALI menu, menu akses, brand, kategori, ecommerce bank, data accounting, metode pembayaran, pengaturan erp dan tipe stok',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'YAKIN',
            cancelButtonText: 'BATAL'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: {},
                    dataType: 'json',
                    url: "{{ url('reset_erp') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil direset', 'success');
                        } else {
                            Swal.fire('Gagal', 'Gagal reset data', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Gagal reset data', 'error');
                    }
                });
            }
        });
    });

    // Export handlers
    $('#export_web_config_btn').on('click', function() {
        $('#export_web_config_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_web_config_btn, #export_web_config_menu').length) {
            $('#export_web_config_menu').addClass('hidden');
        }
    });

    $('#export_web_config_excel_btn').on('click', function() {
        jQuery("#WebConfigTable").table2excel({
            filename: "Pengaturan ERP",
        });
    });
});

function loadWebConfigData(page = 1) {
    webConfigCurrentPage = page;
    var search = $('#web_config_search').val();
    
    $.ajax({
        url: "{{ url('pengaturan_erp_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#web_config_tbody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#web_config_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.DT_RowIndex));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.config_name).on('click', function() {
                        editWebConfig(row);
                    }));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.config_value || '-').on('click', function() {
                        editWebConfig(row);
                    }));
                    tbody.append(tr);
                });
            }

            createCustomPagination('#WebConfigTable', response.total, response.current_page, response.total_pages, response.per_page, loadWebConfigData, 'web_config');
        },
        error: function() {
            $('#web_config_tbody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editWebConfig(row) {
    openWebConfigModal();
    $('#web_config_modal_id').val(row.id);
    $('#web_config_modal_mode').val('edit');
    $('#config_name').val(row.config_name);
    $('#config_value').val(row.config_value);
    $('#web_config_modal_title').text('Edit ' + row.config_name);
}

function openWebConfigModal() {
    $('#WebConfigModal').removeClass('hidden');
}

function closeWebConfigModal() {
    $('#WebConfigModal').addClass('hidden');
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
