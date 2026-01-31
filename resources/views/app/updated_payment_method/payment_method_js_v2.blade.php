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
    loadPaymentMethodData(1);

    // Search handler
    var searchTimeout;
    $('#payment_method_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadPaymentMethodData(1);
        }, 500);
    });

    // Add payment method button
    $('#add_payment_method_btn').on('click', function() {
        openPaymentMethodModal();
        $('#payment_method_modal_id').val('');
        $('#payment_method_modal_mode').val('add');
        $('#f_payment_method')[0].reset();
        $('#st_id').val('').trigger('change');
        $('#delete_payment_method_btn').addClass('hidden');
    });

    // Form submit handler
    $('#f_payment_method').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_payment_method_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('pm_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closePaymentMethodModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadPaymentMethodData(currentPage);
                } else if (data.status == '400') {
                    closePaymentMethodModal();
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
    $(document).on('click', '#delete_payment_method_btn', function() {
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
                    data: {_id: $('#payment_method_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('pm_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closePaymentMethodModal();
                            loadPaymentMethodData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Export handler
    $('#payment_method_export_btn').on('click', function() {
        var allData = [];
        var page = 1;
        var perPage = 1000;
        var search = $('#payment_method_search').val();
        
        function fetchAllData() {
            $.ajax({
                url: "{{ url('payment_method_datatables_simple') }}",
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
                        
                        var csv = 'No,Toko,Metode Pembayaran,Divisi,Relasi Akun,Deskripsi\n';
                        allData.forEach(function(row, index) {
                            csv += (index + 1) + ',"' + (row.st_name || '') + '","' + (row.pm_name || '') + '","' + (row.stt_name || '') + '","' + (row.a_name || '') + '","' + (row.pm_description || '') + '"\n';
                        });
                        
                        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                        var link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = 'Metode Pembayaran - ' + new Date().toISOString().split('T')[0] + '.csv';
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

function loadPaymentMethodData(page = 1) {
    currentPage = page;
    var search = $('#payment_method_search').val();
    
    $.ajax({
        url: "{{ url('payment_method_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#payment_method_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#payment_method_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pm_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.stt_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.a_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pm_description));
                    tr.on('click', function() {
                        editPaymentMethod(row);
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#PaymentMethodtb', response.total, response.current_page, response.total_pages, response.per_page, loadPaymentMethodData);
        },
        error: function() {
            $('#payment_method_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editPaymentMethod(row) {
    openPaymentMethodModal();
    $('#payment_method_modal_id').val(row.pm_id);
    $('#payment_method_modal_mode').val('edit');
    $('#pm_name').val(row.pm_name);
    $('#pm_description').val(row.pm_description);
    $('#stt_id').val(row.stt_id);
    $('#a_id').val(row.a_id);
    $('#st_id').val(row.st_id || '').trigger('change');
    if ($('#delete_access').val() == '1') {
        $('#delete_payment_method_btn').removeClass('hidden');
    }
}

function openPaymentMethodModal() {
    $('#PaymentMethodModal').removeClass('hidden');
}

function closePaymentMethodModal() {
    $('#PaymentMethodModal').addClass('hidden');
}

// Pagination function - same as account_type
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#payment_method_pagination_info');
    var paginationControls = wrapper.querySelector('#payment_method_pagination_controls');
    
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
