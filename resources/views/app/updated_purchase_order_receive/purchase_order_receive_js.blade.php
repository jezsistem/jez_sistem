@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
var penerimaanTable = null;
var currentPage = 1;
var perPage = 25;
var totalRecords = 0;
var totalPages = 0;

function loadPenerimaanData(page = 1) {
    console.log('loadPenerimaanData called, page:', page);
    currentPage = page;
    
    $.ajax({
        url: "{{ url('penerimaan_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#penerimaan_search').val() || '',
            st_id: $('#st_id_filter').val() || '',
            po_status: $('#po_status_filter').val() || '',
            filter_dispute: $('#filter_dispute').val() || '',
            filter_delivery_note: $('#filter_delivery_note').val() || '',
            page: page,
            per_page: perPage
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('Loading penerimaan data...');
            $('#Penerimaantb tbody').html('<tr><td colspan="9" class="px-4 py-4 text-center text-sm text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>');
        },
        success: function(response) {
            console.log('Response received:', response);
            if (penerimaanTable) {
                penerimaanTable.destroy();
            }
            
            $('#Penerimaantb tbody').empty();
            
            // Always set totalRecords and totalPages from response
            totalRecords = response.total || 0;
            totalPages = response.total_pages || 1;
            
            if (response && response.data && response.data.length > 0) {
                console.log('Data found:', response.data.length, 'rows of', totalRecords, 'total');
                
                // Render rows
                var fragment = document.createDocumentFragment();
                var tbody = document.querySelector('#Penerimaantb tbody');
                
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
                $('#Penerimaantb tbody').append('<tr><td colspan="9" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Always create pagination even if no data
            if (totalRecords === 0) {
                totalPages = 1;
            }
            
            if (document.getElementById("Penerimaantb") && typeof simpleDatatables !== 'undefined') {
                console.log('Initializing SimpleDatatables...');
                penerimaanTable = new simpleDatatables.DataTable("#Penerimaantb", {
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
                    var simplePagination = document.querySelector('#Penerimaantb').closest('.datatable-wrapper').querySelector('.datatable-pagination');
                    if (simplePagination) {
                        simplePagination.style.display = 'none';
                    }
                    
                    var simpleInfo = document.querySelector('#Penerimaantb').closest('.datatable-wrapper').querySelector('.datatable-info');
                    if (simpleInfo) {
                        simpleInfo.style.display = 'none';
                    }
                    
                    // Create custom pagination
                    createCustomPagination('#Penerimaantb', totalRecords, currentPage, totalPages, perPage);
                    
                    // Handle per page change
                    var perPageSelect = document.querySelector('#Penerimaantb').closest('.datatable-wrapper').querySelector('.datatable-selector');
                    if (perPageSelect) {
                        perPageSelect.addEventListener('change', function() {
                            perPage = parseInt(this.value);
                            loadPenerimaanData(1);
                        });
                    }
                }, 100);
                
                console.log('SimpleDatatables initialized');
            } else {
                console.error('Penerimaantb element not found or simpleDatatables not loaded');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading penerimaan data:', xhr, status, error);
            console.error('Response:', xhr.responseText);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data penerimaan: ' + (xhr.responseJSON?.message || error), 'error');
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
                loadPenerimaanData(currentPage - 1);
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
                loadPenerimaanData(1);
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
                    loadPenerimaanData(page);
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
                loadPenerimaanData(totalPages);
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
                loadPenerimaanData(currentPage + 1);
            }
        });
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
        loadPenerimaanData(1);
    }, 200);
    
    // Search handler
    $('#penerimaan_search').on('keyup', function() {
        loadPenerimaanData(1);
    });
    
    // Filter handlers
    $('#st_id_filter, #po_status_filter, #filter_dispute, #filter_delivery_note').on('change', function() {
        loadPenerimaanData(1);
    });
    
    // Detail button handler
    $(document).on('click', '.detail-btn', function(e) {
        e.stopPropagation();
        var po_id = $(this).data('id');
        if (!po_id) return;
        
        $.ajax({
            type: "POST",
            url: "{{ url('po_receive_detail') }}",
            data: {
                _po_id: po_id
            },
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    $('#PenerimaanModal').removeClass('hidden');
                    $('#po_invoice_label').text(r.po_invoice || '');
                    $('#_po_id').val(r.po_id);
                    $('#po_description').val(r.po_description || '');
                    $('#st_id_modal').val(r.st_id || '').trigger('change');
                    $('#ps_id_modal').val(r.ps_id || '').trigger('change');
                    $('#stkt_id_modal').val(r.stkt_id || '').trigger('change');
                    $('#tax_id_modal').val(r.tax_id || '').trigger('change');
                    $('#acc_id_modal').val(r.acc_id || '').trigger('change');
                    $('#dispute').val(r.dispute || '0');
                    $('#dispute_description').val(r.dispute_description || '');
                    $('#putaway').val(r.putaway || '0');
                    $('#status_dispute').val(r.status_dispute || '0');
                    $('#shipping_cost').val(r.po_shipping_cost || '');
                    $('#_mode').val('edit');
                    
                    // Load detail content
                    loadPenerimaanDetailContent(po_id);
                } else {
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
            }
        });
    });
    
    // Export button handler
    $('#export_btn').on('click', function() {
        var st_id = $('#st_id_filter').val() || '';
        var po_status = $('#po_status_filter').val() || '';
        var filter_dispute = $('#filter_dispute').val() || '';
        var filter_delivery_note = $('#filter_delivery_note').val() || '';
        var search = $('#penerimaan_search').val() || '';
        
        var params = new URLSearchParams({
            st_id: st_id,
            po_status: po_status,
            filter_dispute: filter_dispute,
            filter_delivery_note: filter_delivery_note,
            search: search
        });
        
        window.location.href = "{{ url('por_export') }}?" + params.toString();
    });
    
    // Close modal handler
    $('#close_penerimaan_modal_btn').on('click', function() {
        $('#PenerimaanModal').addClass('hidden');
    });
});

function loadPenerimaanDetailContent(po_id) {
    $.ajax({
        type: "POST",
        url: "{{ url('check_po_receive_detail') }}",
        data: {
            _po_id: po_id
        },
        dataType: 'html',
        success: function(r) {
            $('#penerimaan_detail_content').html(r);
        },
        error: function() {
            $('#penerimaan_detail_content').html('<div class="text-center text-gray-500 p-4">Error loading detail content</div>');
        }
    });
}
</script>
@endpush
