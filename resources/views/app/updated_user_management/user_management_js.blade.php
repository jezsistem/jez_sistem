<script>
var currentPage = 1;
var perPage = 25;

function loadUserData(page = 1) {
    currentPage = page;
    var search = $('#user_search').val();
    
    $.ajax({
        url: "{{ url('um_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#user_tbody').html('<tr><td colspan="11" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#user_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="11" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.g_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.stt_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_nip));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_ktp));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_secret_code));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_phone));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_email));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_address));
                    
                    tr.on('click', function() {
                        openUserModal('edit', row);
                    });
                    
                    tbody.append(tr);
                });
            }

            createCustomPagination('#UserTable', response.total, response.current_page, response.total_pages, response.per_page, loadUserData, 'user');
        },
        error: function() {
            $('#user_tbody').html('<tr><td colspan="11" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function openUserModal(mode, data = null) {
    $('#UserModal').removeClass('hidden');
    if (mode === 'add') {
        $('#_id').val('');
        $('#_mode').val('add');
        $('#f_user')[0].reset();
        $('#st_id').val('').trigger('change');
        $('#delete_user_btn').addClass('hidden');
    } else {
        $('#_id').val(data.uid);
        $('#_mode').val('edit');
        $('#st_id').val(data.st_id).trigger('change');
        $('#u_name').val(data.u_name);
        $('#u_nip').val(data.u_nip);
        $('#u_ktp').val(data.u_ktp);
        $('#u_secret_code').val(data.u_secret_code);
        $('#u_email').val(data.u_email);
        $('#u_phone').val(data.u_phone);
        $('#u_password').val('');
        $('#u_address').val(data.u_address);
        $('#delete_user_btn').removeClass('hidden');
    }
}

function closeUserModal() {
    $('#UserModal').addClass('hidden');
}

function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction, prefix) {
    var infoId = prefix + '_pagination_info';
    var controlsId = prefix + '_pagination_controls';
    
    var info = $('#' + infoId);
    var controls = $('#' + controlsId);
    
    info.empty();
    controls.empty();
    
    var start = (currentPage - 1) * perPage + 1;
    var end = Math.min(currentPage * perPage, totalRecords);
    info.text('Menampilkan ' + start + ' sampai ' + end + ' dari ' + totalRecords + ' data');
    
    if (totalPages <= 1) return;
    
    var prevBtn = $('<button>')
        .addClass('px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100')
        .text('Sebelumnya')
        .prop('disabled', currentPage === 1)
        .on('click', function() {
            if (currentPage > 1) {
                loadFunction(currentPage - 1);
            }
        });
    
    controls.append(prevBtn);
    
    var maxVisible = 5;
    var startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    var endPage = Math.min(totalPages, startPage + maxVisible - 1);
    
    if (startPage > 1) {
        controls.append($('<button>')
            .addClass('px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-100')
            .text('1')
            .on('click', function() { loadFunction(1); }));
        if (startPage > 2) {
            controls.append($('<span>').addClass('px-3 py-1 text-sm text-gray-500').text('...'));
        }
    }
    
    for (var i = startPage; i <= endPage; i++) {
        var pageBtn = $('<button>')
            .addClass('px-3 py-1 text-sm font-medium border border-gray-300 hover:bg-gray-100')
            .text(i);
        
        if (i === currentPage) {
            pageBtn.addClass('text-white bg-blue-600');
        } else {
            pageBtn.addClass('text-gray-500 bg-white');
        }
        
        pageBtn.on('click', function() {
            loadFunction(parseInt($(this).text()));
        });
        
        controls.append(pageBtn);
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            controls.append($('<span>').addClass('px-3 py-1 text-sm text-gray-500').text('...'));
        }
        controls.append($('<button>')
            .addClass('px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-100')
            .text(totalPages)
            .on('click', function() { loadFunction(totalPages); }));
    }
    
    var nextBtn = $('<button>')
        .addClass('px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100')
        .text('Selanjutnya')
        .prop('disabled', currentPage === totalPages)
        .on('click', function() {
            if (currentPage < totalPages) {
                loadFunction(currentPage + 1);
            }
        });
    
    controls.append(nextBtn);
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadUserData(1);

    // Search with debounce
    var searchTimeout;
    $('#user_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadUserData(1);
        }, 500);
    });

    // Add user button
    $('#add_btn').on('click', function() {
        openUserModal('add');
    });

    // User form submit
    $('#f_user').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_user_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('um_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeUserModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadUserData(currentPage);
                } else if (data.status == '400') {
                    closeUserModal();
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Delete user button
    $('#delete_user_btn').on('click', function() {
        Swal.fire({
            title: "Hapus..?",
            text: "Yakin hapus data ini ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: { _id: $('#_id').val() },
                    dataType: 'json',
                    url: "{{ url('um_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeUserModal();
                            loadUserData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Check exists secret code
    $('#u_secret_code').on('change', function() {
        var u_secret_code = $(this).val();
        if (!u_secret_code) return;
        
        $.ajax({
            type: "POST",
            data: { _u_secret_code: u_secret_code },
            dataType: 'json',
            url: "{{ url('check_exists_secret_code') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Kode', 'Kode sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                    $('#u_secret_code').val('');
                }
            }
        });
    });

    // Export
    $('#export_btn').on('click', function(e) {
        e.stopPropagation();
        $('#export_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_btn, #export_menu').length) {
            $('#export_menu').addClass('hidden');
        }
    });

    $('#export_excel_btn').on('click', function() {
        var table = document.getElementById('UserTable');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
        XLSX.writeFile(wb, "user_management_" + new Date().toISOString().split('T')[0] + ".xlsx");
        $('#export_menu').addClass('hidden');
    });

    // Close modal
    $('#close_user_btn, #close_user_btn_2').on('click', function() {
        closeUserModal();
    });
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
