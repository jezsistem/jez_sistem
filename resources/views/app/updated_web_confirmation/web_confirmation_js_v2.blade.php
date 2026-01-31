<script>
var currentPage = 1;
var perPage = 25;
var ecommerceUrl = '';

$(document).ready(function() {
    ecommerceUrl = $('#ecommerce_url').val();
    
    setInterval(() => {
        if (typeof checkConfirmation === 'function') checkConfirmation();
        if (typeof checkPaid === 'function') checkPaid();
    }, 20000);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadConfirmationData(1);

    // Search handler
    var searchTimeout;
    $('#wbc_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadConfirmationData(1);
        }, 500);
    });

    // Filter handler
    $('#status_filter').on('change', function() {
        loadConfirmationData(1);
    });
});

function loadConfirmationData(page) {
    currentPage = page;
    var search = $('#wbc_search').val();
    var filter = $('#status_filter').val();

    $('#wbc_tbody').html(`
        <tr>
            <td colspan="10" class="px-3 py-4 text-center text-gray-500">
                <div class="flex justify-center items-center">
                    <svg class="animate-spin h-5 w-5 mr-3 text-blue-500" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memuat data...
                </div>
            </td>
        </tr>
    `);

    $.ajax({
        url: "{{ url('konfirmasi_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            filter: filter
        },
        success: function(response) {
            if (response.error) {
                $('#wbc_tbody').html(`
                    <tr>
                        <td colspan="10" class="px-3 py-4 text-center text-red-500">${response.error}</td>
                    </tr>
                `);
                return;
            }

            ecommerceUrl = response.ecommerce_url || ecommerceUrl;
            renderConfirmationTable(response.data);
            createCustomPagination('#Wbctb', response.total, response.current_page, response.total_pages, response.per_page, loadConfirmationData);
        },
        error: function(xhr) {
            var errorMsg = 'Terjadi kesalahan saat memuat data';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            $('#wbc_tbody').html(`
                <tr>
                    <td colspan="10" class="px-3 py-4 text-center text-red-500">${errorMsg}</td>
                </tr>
            `);
        }
    });
}

function renderConfirmationTable(data) {
    if (!data || data.length === 0) {
        $('#wbc_tbody').html(`
            <tr>
                <td colspan="10" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td>
            </tr>
        `);
        return;
    }

    var html = '';
    data.forEach(function(row) {
        var imgHtml = row.cf_transfer_url 
            ? `<a href="${row.cf_transfer_url}" target="_blank"><img src="${row.cf_transfer_url}" class="w-24 h-auto rounded" /></a>`
            : '-';

        html += `
            <tr class="bg-white border-b hover:bg-gray-50 cursor-pointer" 
                data-id="${row.id}" 
                data-status="${row.cf_status}" 
                data-transfer="${row.cf_transfer || ''}"
                data-invoice="${row.pos_invoice}">
                <td class="px-3 py-3">${row.no}</td>
                <td class="px-3 py-3">${row.created_at_show}</td>
                <td class="px-3 py-3">
                    <a href="${row.pos_invoice_link}" target="_blank" class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 hover:bg-blue-200" onclick="event.stopPropagation();">${row.pos_invoice}</a>
                </td>
                <td class="px-3 py-3">${row.cf_name}</td>
                <td class="px-3 py-3">${imgHtml}</td>
                <td class="px-3 py-3">${row.cf_bank_transfer}</td>
                <td class="px-3 py-3">${row.cf_ip}</td>
                <td class="px-3 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded ${row.cf_read_class}">${row.cf_read_label}</span>
                </td>
                <td class="px-3 py-3">${row.u_name}</td>
                <td class="px-3 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded whitespace-nowrap ${row.cf_status_class}">${row.cf_status_label}</span>
                </td>
            </tr>
        `;
    });
    $('#wbc_tbody').html(html);

    // Attach row click handler
    $('#wbc_tbody tr').on('click', function(e) {
        if ($(e.target).closest('a').length) return; // Ignore click on invoice link
        
        var id = $(this).data('id');
        var status = $(this).data('status');
        var transfer = $(this).data('transfer');
        var invoice = $(this).data('invoice');
        
        handleRowClick(id, status, transfer, invoice);
    });
}

function handleRowClick(id, status, transfer, invoice) {
    if (status == '1') {
        Swal.fire('Status Dibayar', 'Status sudah dikonfirmasi', 'warning');
        return;
    } else if (status == '2') {
        Swal.fire('Status Ditolak', 'Status sudah ditolak', 'warning');
        return;
    }

    // Show modal
    $('#wbc_modal_id').val(id);
    $('#wbc_modal_mode').val('edit');
    $('#wbc_modal_invoice').val(invoice);
    $('#wbc_modal_status').val(status);
    
    if (transfer) {
        $('#wbc_modal_img').attr('src', ecommerceUrl + '/api/confirmation/600/' + transfer).show();
    } else {
        $('#wbc_modal_img').hide();
    }
    
    $('#delete_wbc_btn').removeClass('hidden');
    
    document.getElementById('WebConfirmationModal').classList.remove('hidden');
    
    // Mark as read
    markAsRead(id);
}

function markAsRead(id) {
    $.ajax({
        type: "POST",
        data: {_id: id},
        dataType: 'json',
        url: "{{ url('wbc_read')}}",
        success: function(r) {
            if (r.status == '200') {
                // Optionally refresh data
            }
        }
    });
}

function closeWebConfirmationModal() {
    document.getElementById('WebConfirmationModal').classList.add('hidden');
    $('#wbc_modal_id').val('');
    $('#wbc_modal_mode').val('');
    $('#wbc_modal_invoice').val('');
    $('#wbc_modal_status').val('');
    $('#wbc_modal_img').attr('src', '').hide();
}

// Form submit handler
$(document).on('submit', '#f_wbc', function(e) {
    e.preventDefault();
    
    var saveBtn = $('#save_wbc_btn');
    saveBtn.html('Proses..').prop('disabled', true);
    
    var formData = new FormData(this);
    
    $.ajax({
        type: 'POST',
        url: "{{ url('wbc_save')}}",
        data: formData,
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            saveBtn.html('Simpan').prop('disabled', false);
            if (data.status == '200') {
                closeWebConfirmationModal();
                Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                loadConfirmationData(currentPage);
            } else if (data.status == '400') {
                closeWebConfirmationModal();
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
$(document).on('click', '#delete_wbc_btn', function() {
    Swal.fire({
        title: 'Hapus..?',
        text: 'Yakin hapus data ini ?',
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
                data: {
                    _id: $('#wbc_modal_id').val(), 
                    pos_invoice: $('#wbc_modal_invoice').val()
                },
                dataType: 'json',
                url: "{{ url('wbc_delete')}}",
                success: function(r) {
                    if (r.status == '200') {
                        closeWebConfirmationModal();
                        Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                        loadConfirmationData(currentPage);
                    } else {
                        Swal.fire('Gagal', 'Gagal hapus data', 'error');
                    }
                }
            });
        }
    });
});

function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var infoDiv = document.getElementById('wbc_pagination_info');
    var controlsDiv = document.getElementById('wbc_pagination_controls');
    
    if (!infoDiv || !controlsDiv) return;
    
    // Info text
    var start = totalRecords > 0 ? (currentPage - 1) * perPage + 1 : 0;
    var end = Math.min(currentPage * perPage, totalRecords);
    var infoText = totalRecords > 0 ? 'Menampilkan ' + start + ' sampai ' + end + ' dari ' + totalRecords + ' data' : 'Tidak ada data';
    infoDiv.textContent = infoText;
    
    // Clear controls
    controlsDiv.innerHTML = '';
    
    if (totalPages <= 1) return;
    
    // Previous button
    var prevBtn = document.createElement('button');
    prevBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
    prevBtn.textContent = '‹';
    prevBtn.type = 'button';
    prevBtn.disabled = currentPage === 1;
    prevBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentPage > 1) loadFunction(currentPage - 1);
    });
    controlsDiv.appendChild(prevBtn);
    
    // Page numbers
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        var firstBtn = document.createElement('button');
        firstBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
        firstBtn.textContent = '1';
        firstBtn.type = 'button';
        firstBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loadFunction(1);
        });
        controlsDiv.appendChild(firstBtn);
        if (startPage > 2) {
            var ellipsis = document.createElement('span');
            ellipsis.className = 'px-2 text-sm text-gray-500';
            ellipsis.textContent = '...';
            controlsDiv.appendChild(ellipsis);
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
                loadFunction(page);
            });
            controlsDiv.appendChild(pageBtn);
        })(i);
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            var ellipsis = document.createElement('span');
            ellipsis.className = 'px-2 text-sm text-gray-500';
            ellipsis.textContent = '...';
            controlsDiv.appendChild(ellipsis);
        }
        var lastBtn = document.createElement('button');
        lastBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
        lastBtn.textContent = totalPages;
        lastBtn.type = 'button';
        lastBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loadFunction(totalPages);
        });
        controlsDiv.appendChild(lastBtn);
    }
    
    // Next button
    var nextBtn = document.createElement('button');
    nextBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
    nextBtn.textContent = '›';
    nextBtn.type = 'button';
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentPage < totalPages) loadFunction(currentPage + 1);
    });
    controlsDiv.appendChild(nextBtn);
}
</script>
