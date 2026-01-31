<script>
var currentPage = 1;
var perPage = 25;

function loadInvestorData(page = 1) {
    currentPage = page;
    var search = $('#investor_search').val();
    var st_id = $('#st_id_filter').val();
    
    $.ajax({
        url: "{{ url('i_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            st_id: st_id
        },
        success: function(response) {
            if (response.error) {
                $('#investor_tbody').html('<tr><td colspan="7" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#investor_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="7" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.i_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.i_username));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.i_phone));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.i_email));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.i_address));
                    
                    tr.on('click', function() {
                        openInvestorModal('edit', row);
                    });
                    
                    tbody.append(tr);
                });
            }

            createCustomPagination('#InvestorTable', response.total, response.current_page, response.total_pages, response.per_page, loadInvestorData, 'investor');
        },
        error: function() {
            $('#investor_tbody').html('<tr><td colspan="7" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function openInvestorModal(mode, data = null) {
    $('#InvestorModal').removeClass('hidden');
    if (mode === 'add') {
        $('#_id').val('');
        $('#_mode').val('add');
        $('#f_investor')[0].reset();
        $('#st_id').val('').trigger('change');
        $('#password').val('');
        $('#delete_investor_btn').addClass('hidden');
    } else {
        $('#_id').val(data.id);
        $('#_mode').val('edit');
        $('#st_id').val(data.st_id).trigger('change');
        $('#i_name').val(data.i_name);
        $('#i_username').val(data.i_username);
        $('#i_phone').val(data.i_phone);
        $('#i_email').val(data.i_email);
        $('#i_address').val(data.i_address);
        $('#password').val('');
        @if ($data['user']->delete_access == '1')
            $('#delete_investor_btn').removeClass('hidden');
        @else
            $('#delete_investor_btn').addClass('hidden');
        @endif
    }
}

function closeInvestorModal() {
    $('#InvestorModal').addClass('hidden');
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
    loadInvestorData(1);

    // Search with debounce
    var searchTimeout;
    $('#investor_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadInvestorData(1);
        }, 500);
    });

    // Filter store change
    $('#st_id_filter').on('change', function() {
        loadInvestorData(1);
    });

    // Add investor button
    $('#add_investor_btn').on('click', function() {
        openInvestorModal('add');
    });

    // Investor form submit
    $('#f_investor').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_investor_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('i_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeInvestorModal();
                    toastr.success('Data berhasil disimpan', 'Berhasil');
                    loadInvestorData(currentPage);
                } else if (data.status == '400') {
                    closeInvestorModal();
                    toastr.warning('Data tidak tersimpan', 'Gagal');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                toastr.error('Terjadi kesalahan', 'Error');
            }
        });
    });

    // Delete investor button
    $('#delete_investor_btn').on('click', function() {
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
                    url: "{{ url('i_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toastr.success('Data berhasil dihapus', 'Berhasil');
                            closeInvestorModal();
                            loadInvestorData(currentPage);
                        } else {
                            toastr.error('Gagal hapus data', 'Gagal');
                        }
                    }
                });
            }
        });
    });

    // Check username exists
    $('#i_username').on('change', function() {
        var i_username = $(this).val();
        if (!i_username) return;
        
        $.ajax({
            type: "POST",
            data: { i_username: i_username },
            dataType: 'json',
            url: "{{ url('i_username') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Username', 'Username sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                    $('#i_username').val('');
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
        var table = document.getElementById('InvestorTable');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
        XLSX.writeFile(wb, "investor_" + new Date().toISOString().split('T')[0] + ".xlsx");
        $('#export_menu').addClass('hidden');
    });

    // Close modal
    $('#close_investor_btn, #close_investor_btn_2').on('click', function() {
        closeInvestorModal();
    });
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
