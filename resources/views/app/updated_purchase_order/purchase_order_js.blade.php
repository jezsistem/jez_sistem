@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
var purchaseOrderTable = null;
var currentPage = 1;
var perPage = 25;
var totalRecords = 0;
var totalPages = 0;

function loadPurchaseOrderData(page = 1) {
    console.log('loadPurchaseOrderData called, page:', page);
    currentPage = page;
    
    $.ajax({
        url: "{{ url('purchase_order_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#purchase_order_search').val() || '',
            st_id: $('#st_id_filter').val() || '',
            filter_delivery_note: $('#filter_delivery_note').val() || '',
            filter_dispute: $('#filter_dispute').val() || '',
            page: page,
            per_page: perPage
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('Loading purchase order data...');
            $('#PurchaseOrdertb tbody').html('<tr><td colspan="10" class="px-4 py-4 text-center text-sm text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>');
        },
        success: function(response) {
            console.log('Response received:', response);
            if (purchaseOrderTable) {
                purchaseOrderTable.destroy();
            }
            
            $('#PurchaseOrdertb tbody').empty();
            
            // Always set totalRecords and totalPages from response
            totalRecords = response.total || 0;
            totalPages = response.total_pages || 1;
            
            if (response && response.data && response.data.length > 0) {
                console.log('Data found:', response.data.length, 'rows of', totalRecords, 'total');
                
                // Render rows
                var fragment = document.createDocumentFragment();
                var tbody = document.querySelector('#PurchaseOrdertb tbody');
                
                response.data.forEach(function(row) {
                    var tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50 cursor-pointer';
                    tr.setAttribute('data-po_id', row.po_id || '');
                    tr.style.cursor = 'pointer';
                    
                    tr.innerHTML = 
                        '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.no || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-900">' + (row.po_invoice || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.st_name || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.ps_name || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.po_description || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.po_total || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.po_status || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.u_receive || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.po_created_at || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.action || '') + '</td>';
                    
                    // Add click handler for row
                    tr.addEventListener('click', function(e) {
                        // Don't trigger if clicking on action buttons
                        if (e.target.closest('.detail-btn, .delete-btn, button, a')) {
                            return;
                        }
                        var po_id = this.getAttribute('data-po_id');
                        if (po_id) {
                            $('.detail-btn[data-id="' + po_id + '"]').click();
                        }
                    });
                    
                    fragment.appendChild(tr);
                });
                
                tbody.appendChild(fragment);
            } else {
                console.log('No data found');
                $('#PurchaseOrdertb tbody').append('<tr><td colspan="10" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Always create pagination even if no data
            if (totalRecords === 0) {
                totalPages = 1;
            }
            
            if (document.getElementById("PurchaseOrdertb") && typeof simpleDatatables !== 'undefined') {
                console.log('Initializing SimpleDatatables...');
                purchaseOrderTable = new simpleDatatables.DataTable("#PurchaseOrdertb", {
                    searchable: false,
                    sortable: false,
                    perPage: perPage,
                    perPageSelect: [10, 25, 50, 100],
                    classes: {
                        wrapper: "datatable-wrapper",
                        top: "datatable-top",
                        bottom: "datatable-bottom",
                        search: "datatable-search",
                        selector: "datatable-selector",
                        table: "datatable-table",
                        sorter: "datatable-sorter"
                    },
                    labels: {
                        placeholder: "Cari...",
                        perPage: "",
                        noRows: "Tidak ada data",
                        info: "Menampilkan {start} sampai {end} dari {rows} data",
                        noResults: "Tidak ada hasil pencarian"
                    }
                });
                
                // Override pagination to use server-side
                setTimeout(function() {
                    // Hide SimpleDatatables pagination and info since we're using server-side
                    var simplePagination = document.querySelector('#PurchaseOrdertb').closest('.datatable-wrapper').querySelector('.datatable-pagination');
                    if (simplePagination) {
                        simplePagination.style.display = 'none';
                    }
                    
                    var simpleInfo = document.querySelector('#PurchaseOrdertb').closest('.datatable-wrapper').querySelector('.datatable-info');
                    if (simpleInfo) {
                        simpleInfo.style.display = 'none';
                    }
                    
                    // Create custom pagination
                    createCustomPagination('#PurchaseOrdertb', totalRecords, currentPage, totalPages, perPage);
                    
                    // Handle per page change
                    var perPageSelect = document.querySelector('#PurchaseOrdertb').closest('.datatable-wrapper').querySelector('.datatable-selector');
                    if (perPageSelect) {
                        perPageSelect.addEventListener('change', function() {
                            perPage = parseInt(this.value);
                            loadPurchaseOrderData(1);
                        });
                    }
                }, 100);
                
                console.log('SimpleDatatables initialized');
            } else {
                console.error('PurchaseOrdertb element not found or simpleDatatables not loaded');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading purchase order data:', xhr, status, error);
            console.error('Response:', xhr.responseText);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data purchase order: ' + (xhr.responseJSON?.message || error), 'error');
        }
    });
}

function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage) {
    var wrapper = document.querySelector(tableId).closest('.datatable-wrapper');
    if (!wrapper) return;
    
    var bottom = wrapper.querySelector('.datatable-bottom');
    if (!bottom) return;
    
    // Remove existing custom pagination if any
    var existingPagination = bottom.querySelector('.custom-pagination');
    if (existingPagination) {
        existingPagination.remove();
    }
    
    // Hide SimpleDatatables pagination and info
    var simplePagination = bottom.querySelector('.datatable-pagination');
    if (simplePagination) {
        simplePagination.style.display = 'none';
    }
    
    var simpleInfo = bottom.querySelector('.datatable-info');
    if (simpleInfo) {
        simpleInfo.style.display = 'none';
    }
    
    // Create custom pagination
    var paginationDiv = document.createElement('div');
    paginationDiv.className = 'custom-pagination flex items-center gap-2';
    
    var start = totalRecords > 0 ? (currentPage - 1) * perPage + 1 : 0;
    var end = Math.min(currentPage * perPage, totalRecords);
    var infoText = totalRecords > 0 ? 'Menampilkan ' + start + ' sampai ' + end + ' dari ' + totalRecords + ' data' : 'Tidak ada data';
    
    // Info text
    var infoSpan = document.createElement('span');
    infoSpan.className = 'text-sm text-gray-600 mr-4';
    infoSpan.textContent = infoText;
    paginationDiv.appendChild(infoSpan);
    
    if (totalPages > 1) {
        // Previous button
        var prevBtn = document.createElement('button');
        prevBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
        prevBtn.textContent = '‹';
        prevBtn.type = 'button';
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (currentPage > 1) {
                loadPurchaseOrderData(currentPage - 1);
            }
        });
        paginationDiv.appendChild(prevBtn);
        
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
                e.stopPropagation();
                loadPurchaseOrderData(1);
            });
            paginationDiv.appendChild(firstBtn);
            if (startPage > 2) {
                var ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-sm text-gray-500';
                ellipsis.textContent = '...';
                paginationDiv.appendChild(ellipsis);
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
                    loadPurchaseOrderData(page);
                });
                paginationDiv.appendChild(pageBtn);
            })(i);
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                var ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-sm text-gray-500';
                ellipsis.textContent = '...';
                paginationDiv.appendChild(ellipsis);
            }
            var lastBtn = document.createElement('button');
            lastBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
            lastBtn.textContent = totalPages;
            lastBtn.type = 'button';
            lastBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                loadPurchaseOrderData(totalPages);
            });
            paginationDiv.appendChild(lastBtn);
        }
        
        // Next button
        var nextBtn = document.createElement('button');
        nextBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
        nextBtn.textContent = '›';
        nextBtn.type = 'button';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (currentPage < totalPages) {
                loadPurchaseOrderData(currentPage + 1);
            }
        });
        paginationDiv.appendChild(nextBtn);
        
        // Next button
        var nextBtn = document.createElement('button');
        nextBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
        nextBtn.textContent = '›';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = function(e) {
            e.preventDefault();
            if (currentPage < totalPages) {
                loadPurchaseOrderData(currentPage + 1);
            }
        };
        paginationDiv.appendChild(nextBtn);
    }
    
    bottom.appendChild(paginationDiv);
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Load data on page load
    setTimeout(function() {
        loadPurchaseOrderData(1);
    }, 200);
    
    // Search handler
    $('#purchase_order_search').on('keyup', function() {
        loadPurchaseOrderData(1);
    });
    
    // Filter handlers
    $('#st_id_filter, #filter_delivery_note, #filter_dispute').on('change', function() {
        loadPurchaseOrderData(1);
    });
    
    // Detail button handler
    $(document).on('click', '.detail-btn', function(e) {
        e.stopPropagation();
        var po_id = $(this).data('id');
        if (!po_id) return;
        
        $.ajax({
            type: "POST",
            url: "{{ url('po_detail') }}",
            data: {
                _po_id: po_id
            },
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    $('#PurchaseOrderModal').removeClass('hidden');
                    $('#po_invoice_label').text(r.po_invoice || '');
                    $('#_po_id').val(r.po_id);
                    $('#po_description').val(r.po_description || '');
                    $('#st_id_modal').val(r.st_id || '').trigger('change');
                    $('#ps_id_modal').val(r.ps_id || '').trigger('change');
                    $('#tax_id_modal').val(r.tax_id || '').trigger('change');
                    $('#dp_id_modal').val(r.dp_id || '').trigger('change');
                    $('#stkt_id_modal').val(r.stkt_id || '').trigger('change');
                    $('#shipping_cost').val(r.shipping_cost || '');
                    $('#acc_id_modal').val(r.acc_id || '').trigger('change');
                    $('#dispute').val(r.dispute || '');
                    $('#dispute_description').val(r.dispute_description || '');
                    $('#status_dispute').val(r.status_dispute || '');
                    $('#pay_date').val(r.pay_date || '');
                    $('#due_date').val(r.due_date || '');
                    $('#_mode').val('edit');
                    
                    // Load detail content
                    loadPurchaseOrderDetailContent(po_id);
                } else {
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
            }
        });
    });
    
    // Add button handler
    $('#add_po_btn').on('click', function() {
        $('#add_po_btn').prop('disabled', true);
        var type = $(this).data('type') || 'with_item';
        $.ajax({
            type: "POST",
            url: "{{ url('create_po') }}",
            data: {
                _type: type
            },
            dataType: 'json',
            success: function(r) {
                $('#add_po_btn').prop('disabled', false);
                if (r.status == '200') {
                    $('#PurchaseOrderModal').removeClass('hidden');
                    $('#po_invoice_label').text(r.po_invoice || '');
                    $('#_po_id').val(r.po_id);
                    $('#_mode').val('add');
                    $('#f_purchase_order_modal')[0].reset();
                    $('#purchase_order_detail_content').html('');
                } else if (r.status == '219') {
                    // Draft exists
                    $('#PurchaseOrderModal').removeClass('hidden');
                    $('#po_invoice_label').text(r.po_invoice || '');
                    $('#_po_id').val(r.po_id);
                    $('#_mode').val('edit');
                    loadPurchaseOrderDetailContent(r.po_id);
                } else {
                    Swal.fire('Gagal', 'Gagal membuat Purchase Order', 'warning');
                }
            },
            error: function() {
                $('#add_po_btn').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan saat membuat Purchase Order', 'error');
            }
        });
    });
    
    // Import button handler
    $('#import_modal_btn').on('click', function() {
        $('#ImportModal').removeClass('hidden');
    });
    
    // Import form handler
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        $('#import_data_btn').html('Proses...');
        $('#import_data_btn').attr('disabled', true);
        var formData = new FormData(this);
        var po_id = $('#_po_id').val();
        if (po_id) {
            formData.append('_po_id', po_id);
        }
        
        $.ajax({
            type: 'POST',
            url: "{{ url('po_import') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                
                if (data.status == '200') {
                    $('#ImportModal').addClass('hidden');
                    Swal.fire('Berhasil', 'Data berhasil diimport', 'success');
                    $('#f_import')[0].reset();
                    if (po_id) {
                        loadPurchaseOrderDetailContent(po_id);
                    }
                    loadPurchaseOrderData(currentPage);
                } else if (data.status == '400') {
                    $('#ImportModal').addClass('hidden');
                    Swal.fire('Gagal', 'File yang anda import kosong atau format tidak tepat', 'warning');
                } else {
                    $('#ImportModal').addClass('hidden');
                    Swal.fire('Gagal', data.message || 'Terjadi kesalahan saat memproses file', 'error');
                }
            },
            error: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                Swal.fire('Error', 'Terjadi kesalahan saat memproses', 'error');
            }
        });
    });
    
    // Save draft button handler
    $('#save_purchase_order_draft_btn').on('click', function() {
        var po_id = $('#_po_id').val();
        if (!po_id) {
            Swal.fire('Error', 'Purchase Order ID tidak ditemukan', 'error');
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "{{ url('po_save_draft') }}",
            data: {
                _id: po_id
            },
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Berhasil', 'Purchase Order berhasil disimpan sebagai draft', 'success');
                    $('#PurchaseOrderModal').addClass('hidden');
                    loadPurchaseOrderData(currentPage);
                } else {
                    Swal.fire('Gagal', 'Gagal menyimpan draft', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan saat memproses', 'error');
            }
        });
    });
    
    // Cancel/Delete button handler
    $('#cancel_purchase_order_btn').on('click', function() {
        var po_id = $('#_po_id').val();
        if (!po_id) {
            Swal.fire('Error', 'Purchase Order ID tidak ditemukan', 'error');
            return;
        }
        
        Swal.fire({
            title: "Hapus..?",
            text: "Yakin hapus Purchase Order ini ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then(function(isConfirm) {
            if (isConfirm.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('cancel_po') }}",
                    data: {
                        _id: po_id
                    },
                    dataType: 'json',
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Purchase Order berhasil dihapus', 'success');
                            $('#PurchaseOrderModal').addClass('hidden');
                            loadPurchaseOrderData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus Purchase Order', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan saat memproses', 'error');
                    }
                });
            }
        });
    });
    
    // Close modal handler
    $('#close_purchase_order_modal_btn').on('click', function() {
        $('#PurchaseOrderModal').addClass('hidden');
    });
    
    // Close import modal handler
    $(document).on('click', '.close-import-modal', function() {
        $('#ImportModal').addClass('hidden');
    });
});

function loadPurchaseOrderDetailContent(po_id) {
    $.ajax({
        type: "POST",
        url: "{{ url('check_po_detail') }}",
        data: {
            _po_id: po_id
        },
        dataType: 'html',
        success: function(r) {
            $('#purchase_order_detail_content').html(r);
        },
        error: function() {
            $('#purchase_order_detail_content').html('<div class="text-center text-gray-500 p-4">Error loading detail content</div>');
        }
    });
}
</script>
@endpush
