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
    loadAccountData(1);

    // Search handler
    var searchTimeout;
    $('#account_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadAccountData(1);
        }, 500);
    });

    // Add account button
    $('#add_account_btn').on('click', function() {
        openAccountModal();
        $('#account_modal_id').val('');
        $('#account_modal_mode').val('add');
        $('#f_account')[0].reset();
        $('#delete_account_btn').addClass('hidden');
    });

    // Form submit handler
    $('#f_account').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_account_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('a_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeAccountModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadAccountData(currentPage);
                } else if (data.status == '400') {
                    closeAccountModal();
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
    $(document).on('click', '#delete_account_btn', function() {
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
                    data: {_id: $('#account_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('a_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeAccountModal();
                            loadAccountData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Check exists account name
    $('#a_name').on('change', function() {
        var a_name = $(this).val();
        if (!a_name) return;
        
        $.ajax({
            type: "POST",
            data: {_a_name: a_name},
            dataType: 'json',
            url: "{{ url('check_exists_account') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Peringatan', 'Nama akun sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                    $('#a_name').val('');
                    return false;
                }
            }
        });
    });

    // Check exists account code
    $('#a_code').on('change', function() {
        var a_code = $(this).val();
        if (!a_code) return;
        
        $.ajax({
            type: "POST",
            data: {_a_code: a_code},
            dataType: 'json',
            url: "{{ url('check_exists_account_code') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Peringatan', 'Kode akun sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                    $('#a_code').val('');
                    return false;
                }
            }
        });
    });

    // Export handler
    $('#account_export_btn').on('click', function() {
        var allData = [];
        var page = 1;
        var perPage = 1000;
        var search = $('#account_search').val();
        
        function fetchAllData() {
            $.ajax({
                url: "{{ url('account_datatables_simple') }}",
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
                        
                        var csv = 'No,Kode Akun,Nama Akun,Jenis,Klasifikasi,Deskripsi\n';
                        allData.forEach(function(row, index) {
                            csv += (index + 1) + ',"' + (row.a_code || '') + '","' + (row.a_name || '') + '","' + (row.at_name || '') + '","' + (row.ac_name || '') + '","' + (row.a_description || '') + '"\n';
                        });
                        
                        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                        var link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = 'Data Akun - ' + new Date().toISOString().split('T')[0] + '.csv';
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

function loadAccountData(page = 1) {
    currentPage = page;
    var search = $('#account_search').val();
    
    $.ajax({
        url: "{{ url('account_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#account_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#account_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.a_code));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.a_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.at_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ac_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.a_description));
                    tr.on('click', function() {
                        editAccount(row);
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#Accounttb', response.total, response.current_page, response.total_pages, response.per_page, loadAccountData);
        },
        error: function() {
            $('#account_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editAccount(row) {
    openAccountModal();
    $('#account_modal_id').val(row.aid);
    $('#account_modal_mode').val('edit');
    $('#a_code').val(row.a_code);
    $('#a_name').val(row.a_name);
    $('#a_description').val(row.a_description);
    $('#ac_id').val(row.acid);
    if ($('#delete_access').val() == '1') {
        $('#delete_account_btn').removeClass('hidden');
    }
}

function openAccountModal() {
    $('#AccountModal').removeClass('hidden');
}

function closeAccountModal() {
    $('#AccountModal').addClass('hidden');
}

// Pagination function
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#account_pagination_info');
    var paginationControls = wrapper.querySelector('#account_pagination_controls');
    
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
