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
    loadBankData(1);

    // Search handler
    var searchTimeout;
    $('#bank_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadBankData(1);
        }, 500);
    });

    // Add bank button
    $('#add_bank_btn').on('click', function() {
        openBankModal();
        $('#bank_modal_id').val('');
        $('#bank_modal_mode').val('add');
        $('#bank_modal_image').val('');
        $('#f_bank')[0].reset();
        $('#imagePreview').attr('src', '');
        $('#delete_bank_btn').addClass('hidden');
    });

    // Form submit handler
    $('#f_bank').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_bank_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('bank_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeBankModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadBankData(currentPage);
                } else if (data.status == '400') {
                    closeBankModal();
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
    $(document).on('click', '#delete_bank_btn', function() {
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
                    data: {_id: $('#bank_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('bank_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeBankModal();
                            loadBankData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Check exists bank
    $('#bank_name').on('change', function() {
        var bank_name = $(this).val();
        $.ajax({
            type: "POST",
            data: {_bank_name: bank_name},
            dataType: 'json',
            url: "{{ url('check_exists_bank') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Peringatan', 'Nama bank sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                    $('#bank_name').val('');
                    return false;
                }
            }
        });
    });

    // Image preview
    var loadFile = function(event) {
        var output = document.getElementById('imagePreview');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src);
        }
    };
    window.loadFile = loadFile;
});

function loadBankData(page = 1) {
    currentPage = page;
    var search = $('#bank_search').val();
    
    $.ajax({
        url: "{{ url('bank_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#bank_tbody').html('<tr><td colspan="5" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#bank_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').html('<img src="' + row.bank_image_url + '" class="w-16 h-16 object-cover rounded" />'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.bank_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.bank_account_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.bank_number));
                    tr.on('click', function() {
                        editBank(row);
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#Banktb', response.total, response.current_page, response.total_pages, response.per_page, loadBankData);
        },
        error: function() {
            $('#bank_tbody').html('<tr><td colspan="5" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editBank(row) {
    openBankModal();
    $('#bank_modal_id').val(row.id);
    $('#bank_modal_mode').val('edit');
    $('#bank_modal_image').val(row.bank_image);
    $('#bank_name').val(row.bank_name);
    $('#bank_account_name').val(row.bank_account_name);
    $('#bank_number').val(row.bank_number);
    if (row.bank_image) {
        $('#imagePreview').attr('src', row.bank_image_url).show();
    }
    if ($('#delete_access').val() == '1') {
        $('#delete_bank_btn').removeClass('hidden');
    }
}

function openBankModal() {
    $('#BankModal').removeClass('hidden');
}

function closeBankModal() {
    $('#BankModal').addClass('hidden');
}

// Pagination function
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#bank_pagination_info');
    var paginationControls = wrapper.querySelector('#bank_pagination_controls');
    
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
