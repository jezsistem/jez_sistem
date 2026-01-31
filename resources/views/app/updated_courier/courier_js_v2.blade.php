<script>
var currentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadCourierData(1);

    // Search handler
    var searchTimeout;
    $('#courier_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadCourierData(1);
        }, 500);
    });

    // Add courier button
    $('#add_courier_btn').on('click', function() {
        openCourierModal();
        $('#courier_modal_id').val('');
        $('#courier_modal_mode').val('add');
        $('#f_courier')[0].reset();
        $('#delete_courier_btn').addClass('hidden');
    });

    // Form submit handler
    $('#f_courier').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_courier_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('cr_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeCourierModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadCourierData(currentPage);
                } else if (data.status == '400') {
                    closeCourierModal();
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
    $(document).on('click', '#delete_courier_btn', function() {
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
                    data: {_id: $('#courier_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('cr_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeCourierModal();
                            loadCourierData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Export handler
    $('#courier_export_btn').on('click', function() {
        var allData = [];
        var page = 1;
        var perPage = 1000;
        var search = $('#courier_search').val();
        
        function fetchAllData() {
            $.ajax({
                url: "{{ url('courier_datatables_simple') }}",
                type: 'GET',
                data: {
                    page: page,
                    per_page: perPage,
                    search: search
                },
                success: function(response) {
                    if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                        return;
                    }
                    
                    allData = allData.concat(response.data);
                    
                    if (page < response.total_pages) {
                        page++;
                        fetchAllData();
                    } else {
                        if (allData.length === 0) {
                            Swal.fire('Peringatan', 'Tidak ada data untuk diekspor', 'warning');
                            return;
                        }
                        
                        var csv = 'No,Kurir,Deskripsi\n';
                        allData.forEach(function(row, index) {
                            csv += (index + 1) + ',"' + (row.cr_name || '') + '","' + (row.cr_description || '') + '"\n';
                        });
                        
                        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                        var link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = 'Kurir Pengiriman - ' + new Date().toISOString().split('T')[0] + '.csv';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil data untuk export', 'error');
                }
            });
        }
        
        fetchAllData();
    });
});

function loadCourierData(page = 1) {
    currentPage = page;
    var search = $('#courier_search').val();
    
    $.ajax({
        url: "{{ url('courier_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#courier_tbody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#courier_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    var nameTd = $('<td>').addClass('px-3 py-4 font-medium text-gray-900').text(row.cr_name);
                    tr.append(nameTd);
                    tr.append($('<td>').addClass('px-3 py-4').text(row.cr_description));
                    tr.on('click', function(e) {
                        if (!$(e.target).closest('td').is(nameTd)) {
                            editCourier(row);
                        }
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#Couriertb', response.total, response.current_page, response.total_pages, response.per_page, loadCourierData);
        },
        error: function() {
            $('#courier_tbody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editCourier(row) {
    openCourierModal();
    $('#courier_modal_id').val(row.id);
    $('#courier_modal_mode').val('edit');
    $('#cr_name').val(row.cr_name);
    $('#cr_description').val(row.cr_description);
    if ($('#delete_access').val() == '1') {
        $('#delete_courier_btn').removeClass('hidden');
    }
}

function openCourierModal() {
    $('#CourierModal').removeClass('hidden');
}

function closeCourierModal() {
    $('#CourierModal').addClass('hidden');
}

// Pagination function
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#courier_pagination_info');
    var paginationControls = wrapper.querySelector('#courier_pagination_controls');
    
    if (!paginationInfo || !paginationControls) return;

    paginationControls.innerHTML = '';

    var start = totalRecords > 0 ? (currentPage - 1) * perPage + 1 : 0;
    var end = Math.min(currentPage * perPage, totalRecords);
    var infoText = totalRecords > 0 ? 'Menampilkan ' + start + ' sampai ' + end + ' dari ' + totalRecords + ' data' : 'Tidak ada data';

    paginationInfo.textContent = infoText;

    if (totalPages > 1) {
        var prevBtn = document.createElement('button');
        prevBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
        prevBtn.textContent = '‹';
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
            firstBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
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
                ellipsis.className = 'px-2 text-sm text-gray-500';
                ellipsis.textContent = '...';
                paginationControls.appendChild(ellipsis);
            }
        }

        for (var i = startPage; i <= endPage; i++) {
            (function(page) {
                var pageBtn = document.createElement('button');
                pageBtn.className = 'px-3 py-1 text-sm border rounded ' + (page === currentPage ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50');
                pageBtn.textContent = page;
                pageBtn.type = 'button';
                pageBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    loadFunction(page);
                });
                paginationControls.appendChild(pageBtn);
            })(i);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                var ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-sm text-gray-500';
                ellipsis.textContent = '...';
                paginationControls.appendChild(ellipsis);
            }
            var lastBtn = document.createElement('button');
            lastBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
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
        nextBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
        nextBtn.textContent = '›';
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
