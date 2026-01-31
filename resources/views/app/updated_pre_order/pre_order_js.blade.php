@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
var preOrderTable = null;
var currentPage = 1;
var perPage = 25;
var totalRecords = 0;
var totalPages = 0;

function loadPreOrderData(page = 1) {
    console.log('loadPreOrderData called, page:', page);
    currentPage = page;
    
    $.ajax({
        url: "{{ url('pre_order_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#pre_order_search').val() || '',
            st_id: $('#st_id_filter').val() || '',
            po_type: $('#po_type_filter').val() || '',
            page: page,
            per_page: perPage
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('Loading pre order data...');
            $('#PreOrdertb tbody').html('<tr><td colspan="10" class="px-4 py-4 text-center text-sm text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>');
        },
        success: function(response) {
            console.log('Response received:', response);
            if (preOrderTable) {
                preOrderTable.destroy();
            }
            
            $('#PreOrdertb tbody').empty();
            
            // Always set totalRecords and totalPages from response
            totalRecords = response.total || 0;
            totalPages = response.total_pages || 1;
            
            if (response && response.data && response.data.length > 0) {
                console.log('Data found:', response.data.length, 'rows of', totalRecords, 'total');
                
                // Render rows
                var fragment = document.createDocumentFragment();
                var tbody = document.querySelector('#PreOrdertb tbody');
                
                response.data.forEach(function(row) {
                    var tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50 cursor-pointer';
                    tr.setAttribute('data-po_id', row.po_id || '');
                    tr.style.cursor = 'pointer';
                    
                    tr.innerHTML = 
                        '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.no || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-900">' + (row.pre_order_code || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.st_name || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.ps_name || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.po_type || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.preorder_description || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.po_total || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.po_status || '') + '</td>' +
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
                $('#PreOrdertb tbody').append('<tr><td colspan="10" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Always create pagination even if no data
            if (totalRecords === 0) {
                totalPages = 1;
            }
            
            if (document.getElementById("PreOrdertb") && typeof simpleDatatables !== 'undefined') {
                console.log('Initializing SimpleDatatables...');
                preOrderTable = new simpleDatatables.DataTable("#PreOrdertb", {
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
                    var simplePagination = document.querySelector('#PreOrdertb').closest('.datatable-wrapper').querySelector('.datatable-pagination');
                    if (simplePagination) {
                        simplePagination.style.display = 'none';
                    }
                    
                    var simpleInfo = document.querySelector('#PreOrdertb').closest('.datatable-wrapper').querySelector('.datatable-info');
                    if (simpleInfo) {
                        simpleInfo.style.display = 'none';
                    }
                    
                    // Create custom pagination
                    createCustomPagination('#PreOrdertb', totalRecords, currentPage, totalPages, perPage);
                    
                    // Handle per page change
                    var perPageSelect = document.querySelector('#PreOrdertb').closest('.datatable-wrapper').querySelector('.datatable-selector');
                    if (perPageSelect) {
                        perPageSelect.addEventListener('change', function() {
                            perPage = parseInt(this.value);
                            loadPreOrderData(1);
                        });
                    }
                }, 100);
                
                console.log('SimpleDatatables initialized');
            } else {
                console.error('PreOrdertb element not found or simpleDatatables not loaded');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading pre order data:', xhr, status, error);
            console.error('Response:', xhr.responseText);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data pre order: ' + (xhr.responseJSON?.message || error), 'error');
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
                loadPreOrderData(currentPage - 1);
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
                loadPreOrderData(1);
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
                    loadPreOrderData(page);
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
                loadPreOrderData(totalPages);
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
                loadPreOrderData(currentPage + 1);
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
                loadPreOrderData(currentPage + 1);
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
        loadPreOrderData(1);
    }, 200);
    
    // Search handler
    $('#pre_order_search').on('keyup', function() {
        loadPreOrderData(1);
    });
    
    // Filter handlers
    $('#st_id_filter, #po_type_filter').on('change', function() {
        loadPreOrderData(1);
    });
    
    // Detail button handler
    $(document).on('click', '.detail-btn', function(e) {
        e.stopPropagation();
        var po_id = $(this).data('id');
        if (!po_id) return;
        
        $.ajax({
            type: "POST",
            url: "{{ url('pre_order_detail') }}",
            data: {
                _po_id: po_id
            },
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    $('#PreOrderModal').removeClass('hidden');
                    $('#po_invoice_label').text(r.pre_order_code || '');
                    $('#_po_id').val(r.po_id);
                    $('#preorder_description').val(r.preorder_description || '');
                    $('#st_id_modal').val(r.st_id || '').trigger('change');
                    $('#ps_id_modal').val(r.ps_id || '').trigger('change');
                    $('#br_id_modal').val(r.br_id || '').trigger('change');
                    $('#ss_id_modal').val(r.ss_id || '').trigger('change');
                    $('#preorder_type_modal').val(r.po_type || '').trigger('change');
                    $('#_mode').val('edit');
                    
                    // Load detail content
                    loadPreOrderDetailContent(po_id);
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
    $('#add_pre_order_btn').on('click', function() {
        $('#add_pre_order_btn').prop('disabled', true);
        $.ajax({
            type: "POST",
            url: "{{ url('create_pre_order') }}",
            dataType: 'json',
            success: function(r) {
                $('#add_pre_order_btn').prop('disabled', false);
                if (r.status == '200') {
                    $('#PreOrderModal').removeClass('hidden');
                    $('#po_invoice_label').text(r.pre_order_code || '');
                    $('#_po_id').val(r.po_id);
                    $('#_mode').val('add');
                    $('#f_pre_order_modal')[0].reset();
                    $('#purchase_order_detail_content').html('');
                } else if (r.status == '219') {
                    // Draft exists
                    $('#PreOrderModal').removeClass('hidden');
                    $('#po_invoice_label').text(r.pre_order_code || '');
                    $('#_po_id').val(r.po_id);
                    $('#_mode').val('edit');
                    loadPreOrderDetailContent(r.po_id);
                } else {
                    Swal.fire('Gagal', 'Gagal membuat Pre Order', 'warning');
                }
            },
            error: function() {
                $('#add_pre_order_btn').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan saat membuat Pre Order', 'error');
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
            url: "{{ url('pre_order_import') }}",
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
                        loadPreOrderDetailContent(po_id);
                    }
                    loadPreOrderData(currentPage);
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
    $('#save_pre_order_draft_btn').on('click', function() {
        var po_id = $('#_po_id').val();
        if (!po_id) {
            Swal.fire('Error', 'Pre Order ID tidak ditemukan', 'error');
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "{{ url('pre_order_save_draft') }}",
            data: {
                _id: po_id
            },
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Berhasil', 'Pre Order berhasil disimpan sebagai draft', 'success');
                    $('#PreOrderModal').addClass('hidden');
                    loadPreOrderData(currentPage);
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
    $('#cancel_pre_order_btn').on('click', function() {
        var po_id = $('#_po_id').val();
        if (!po_id) {
            Swal.fire('Error', 'Pre Order ID tidak ditemukan', 'error');
            return;
        }
        
        Swal.fire({
            title: "Hapus..?",
            text: "Yakin hapus Pre Order ini ?",
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
                    url: "{{ url('cancel_pre_order') }}",
                    data: {
                        _id: po_id
                    },
                    dataType: 'json',
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Pre Order berhasil dihapus', 'success');
                            $('#PreOrderModal').addClass('hidden');
                            loadPreOrderData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus Pre Order', 'error');
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
    $('#close_pre_order_modal_btn').on('click', function() {
        $('#PreOrderModal').addClass('hidden');
    });
    
    // Close import modal handler
    $(document).on('click', '.close-import-modal', function() {
        $('#ImportModal').addClass('hidden');
    });
});

function loadPreOrderDetailContent(po_id) {
    $.ajax({
        type: "POST",
        url: "{{ url('check_pre_order_detail') }}",
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
