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
    loadWebTransactionData(1);

    // Search handler
    var searchTimeout;
    $('#wt_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadWebTransactionData(1);
        }, 500);
    });

    // Filter handlers
    $('#status_filter, #payment_filter').on('change', function() {
        loadWebTransactionData(1);
    });
});

function loadWebTransactionData(page) {
    currentPage = page;
    var search = $('#wt_search').val();
    var filter = $('#status_filter').val();
    var payment = $('#payment_filter').val();

    $('#wt_tbody').html(`
        <tr>
            <td colspan="18" class="px-3 py-4 text-center text-gray-500">
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
        url: "{{ url('wt_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            filter: filter,
            payment: payment
        },
        success: function(response) {
            if (response.error) {
                $('#wt_tbody').html(`
                    <tr>
                        <td colspan="18" class="px-3 py-4 text-center text-red-500">${response.error}</td>
                    </tr>
                `);
                return;
            }

            ecommerceUrl = response.ecommerce_url || ecommerceUrl;
            renderWebTransactionTable(response.data);
            createCustomPagination('#Wttb', response.total, response.current_page, response.total_pages, response.per_page, loadWebTransactionData);
        },
        error: function(xhr) {
            var errorMsg = 'Terjadi kesalahan saat memuat data';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            $('#wt_tbody').html(`
                <tr>
                    <td colspan="18" class="px-3 py-4 text-center text-red-500">${errorMsg}</td>
                </tr>
            `);
        }
    });
}

function renderWebTransactionTable(data) {
    if (!data || data.length === 0) {
        $('#wt_tbody').html(`
            <tr>
                <td colspan="18" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td>
            </tr>
        `);
        return;
    }

    var html = '';
    data.forEach(function(row) {
        var noteIcon = row.pos_note ? `<i class="fas fa-eye text-gray-600 cursor-pointer" title="${row.pos_note}"></i>` : '-';
        
        var fnBadge = row.pos_web_notif 
            ? '<span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Y</span>'
            : '<span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800">N</span>';
        
        var r1Badge = row.cust_first 
            ? '<span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Y</span>'
            : '<span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800">N</span>';
        
        var r2Badge = row.cust_second 
            ? '<span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Y</span>'
            : '<span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800">N</span>';
        
        var r3Badge = row.cust_third 
            ? '<span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Y</span>'
            : '<span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800">N</span>';

        html += `
            <tr class="bg-white border-b hover:bg-gray-50 cursor-pointer" 
                data-id="${row.id}" 
                data-status="${row.pos_status}" 
                data-invoice="${row.pos_invoice}">
                <td class="px-3 py-3">${row.no}</td>
                <td class="px-3 py-3 whitespace-nowrap">${row.created_at_show}</td>
                <td class="px-3 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded ${row.diff_time_class}">${row.diff_time}</span>
                </td>
                <td class="px-3 py-3">
                    <a href="${row.pos_invoice_link}" target="_blank" class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 hover:bg-blue-200" onclick="event.stopPropagation();">${row.pos_invoice}</a>
                </td>
                <td class="px-3 py-3">${row.cust_name}</td>
                <td class="px-3 py-3 whitespace-nowrap">${row.pos_real_price}</td>
                <td class="px-3 py-3">${row.pos_unique_code}</td>
                <td class="px-3 py-3">${row.pos_courier}</td>
                <td class="px-3 py-3">${row.pos_shipping}</td>
                <td class="px-3 py-3">${row.pos_shipping_number}</td>
                <td class="px-3 py-3">${row.item}</td>
                <td class="px-3 py-3">${row.pos_web_payment}</td>
                <td class="px-3 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded ${row.pos_status_class}">${row.pos_status}</span>
                </td>
                <td class="px-3 py-3">${noteIcon}</td>
                <td class="px-3 py-3">${fnBadge}</td>
                <td class="px-3 py-3">${r1Badge}</td>
                <td class="px-3 py-3">${r2Badge}</td>
                <td class="px-3 py-3">${r3Badge}</td>
            </tr>
        `;
    });
    $('#wt_tbody').html(html);

    // Attach row click handler
    $('#wt_tbody tr').on('click', function(e) {
        if ($(e.target).closest('a').length) return; // Ignore click on invoice link
        
        var id = $(this).data('id');
        var status = $(this).data('status');
        var invoice = $(this).data('invoice');
        
        handleRowClick(id, status, invoice);
    });
}

function handleRowClick(id, status, invoice) {
    if (status == 'PAID') {
        Swal.fire({
            title: 'Print..?',
            text: 'Setelah ini anda akan diarahkan ke halaman summary order customer, silahkan klik tombol hijau PRINT INVOICE pada bagian paling atas',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Print',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    url: "{{ url('print_web_paid')}}",
                    success: function(r) {
                        if (r.status == '200') {
                            var win = window.open(ecommerceUrl + '/customer_paid/order/detail/id/data/' + id, '_blank');
                            if (win) {
                                win.focus();
                            } else {
                                alert('Please allow popups for this website');
                            }
                        }
                    }
                });
            }
        });
        return;
    } else if (status == 'SHIPPING NUMBER' || status == 'DONE') {
        var win = window.open('{{ url('/') }}/print_invoice/' + invoice, '_blank');
        if (win) {
            win.focus();
        } else {
            alert('Please allow popups for this website');
        }
        return;
    } else if (status == 'CANCEL') {
        Swal.fire('Cancel', 'Invoice tidak bisa diubah karena sudah cancel', 'warning');
        return;
    }

    // Show modal for other statuses
    $('#wt_modal_id').val(id);
    $('#wt_modal_mode').val('edit');
    $('#wt_modal_status').val(status);
    
    @if($data['user']->delete_access == '1')
    $('#delete_wt_btn').removeClass('hidden');
    @endif
    
    document.getElementById('WebTransactionModal').classList.remove('hidden');
}

function closeWebTransactionModal() {
    document.getElementById('WebTransactionModal').classList.add('hidden');
    $('#wt_modal_id').val('');
    $('#wt_modal_mode').val('');
    $('#wt_modal_status').val('');
}

// Form submit handler
$(document).on('submit', '#f_wt', function(e) {
    e.preventDefault();
    
    var saveBtn = $('#save_wt_btn');
    saveBtn.html('Proses..').prop('disabled', true);
    
    var formData = new FormData(this);
    
    $.ajax({
        type: 'POST',
        url: "{{ url('wt_save')}}",
        data: formData,
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            saveBtn.html('Simpan').prop('disabled', false);
            if (data.status == '200') {
                closeWebTransactionModal();
                Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                loadWebTransactionData(currentPage);
            } else if (data.status == '400') {
                closeWebTransactionModal();
                Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
            }
        },
        error: function(data) {
            saveBtn.html('Simpan').prop('disabled', false);
            Swal.fire('Error', 'Terjadi kesalahan', 'error');
        }
    });
});

// Status change handler
$(document).on('change', '#wt_modal_status', function() {
    var status = $(this).val();
    if (status == 'CANCEL') {
        Swal.fire({
            title: 'Yakin Cancel',
            text: 'Jika anda cancel, maka stok akan kembali, segala bentuk resiko ditanggung anda atas pembatalan manual invoice ini',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Lanjut',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (!result.isConfirmed) {
                $('#wt_modal_status').val('');
            }
        });
    }
});

function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var infoDiv = document.getElementById('wt_pagination_info');
    var controlsDiv = document.getElementById('wt_pagination_controls');
    
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
