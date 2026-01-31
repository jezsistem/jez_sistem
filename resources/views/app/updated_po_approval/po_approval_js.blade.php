@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script>
var approvalPenerimaanTable = null;
var currentPage = 1;
var perPage = 25;
var totalRecords = 0;
var totalPages = 0;

function loadApprovalPenerimaanData(page = 1) {
    console.log('loadApprovalPenerimaanData called, page:', page);
    currentPage = page;
    
    $.ajax({
        url: "{{ url('approval_penerimaan_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#approval_penerimaan_search').val() || '',
            filter_status: $('#filter_status').val() || '',
            filter_cabang: $('#filter_cabang').val() || '',
            filter_dispute: $('#filter_dispute').val() || '',
            filter_status_dispute: $('#filter_status_dispute').val() || '',
            date: $('#po_date').val() || '',
            page: page,
            per_page: perPage
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('Loading approval penerimaan data...');
            $('#ApprovalPenerimaantb tbody').html('<tr><td colspan="12" class="px-4 py-4 text-center text-sm text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>');
        },
        success: function(response) {
            console.log('Response received:', response);
            if (approvalPenerimaanTable) {
                approvalPenerimaanTable.destroy();
            }
            
            $('#ApprovalPenerimaantb tbody').empty();
            
            // Always set totalRecords and totalPages from response
            totalRecords = response.total || 0;
            totalPages = response.total_pages || 1;
            
            if (response && response.data && response.data.length > 0) {
                console.log('Data found:', response.data.length, 'rows of', totalRecords, 'total');
                
                // Render rows
                var fragment = document.createDocumentFragment();
                var tbody = document.querySelector('#ApprovalPenerimaantb tbody');
                
                response.data.forEach(function(row) {
                    var tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50 cursor-pointer';
                    tr.setAttribute('data-id', row.id || '');
                    tr.setAttribute('data-po-id', row.po_id || '');
                    tr.style.cursor = 'pointer';
                    
                    tr.innerHTML = 
                        '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.no || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.st_name || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.po_invoice || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.ps_name || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-900 font-medium">' + (row.poads_invoice || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.invoice_date || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.arrived_at || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.receive_date || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.qty || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.u_receive || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.created_at || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.action || '') + '</td>';
                    
                    // Add click handler for row
                    tr.addEventListener('click', function(e) {
                        // Don't trigger if clicking on action buttons
                        if (e.target.closest('.detail-btn, .delete-btn, button, a')) {
                            return;
                        }
                        var id = this.getAttribute('data-id');
                        if (id) {
                            $('.detail-btn[data-id="' + id + '"]').click();
                        }
                    });
                    
                    fragment.appendChild(tr);
                });
                
                tbody.appendChild(fragment);
            } else {
                console.log('No data found');
                $('#ApprovalPenerimaantb tbody').append('<tr><td colspan="12" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Always create pagination even if no data
            if (totalRecords === 0) {
                totalPages = 1;
            }
            
            if (document.getElementById("ApprovalPenerimaantb") && typeof simpleDatatables !== 'undefined') {
                console.log('Initializing SimpleDatatables...');
                approvalPenerimaanTable = new simpleDatatables.DataTable("#ApprovalPenerimaantb", {
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
                    var simplePagination = document.querySelector('#ApprovalPenerimaantb').closest('.datatable-wrapper').querySelector('.datatable-pagination');
                    if (simplePagination) {
                        simplePagination.style.display = 'none';
                    }
                    
                    var simpleInfo = document.querySelector('#ApprovalPenerimaantb').closest('.datatable-wrapper').querySelector('.datatable-info');
                    if (simpleInfo) {
                        simpleInfo.style.display = 'none';
                    }
                    
                    // Create custom pagination
                    createCustomPagination('#ApprovalPenerimaantb', totalRecords, currentPage, totalPages, perPage);
                    
                    // Handle per page change
                    var perPageSelect = document.querySelector('#ApprovalPenerimaantb').closest('.datatable-wrapper').querySelector('.datatable-selector');
                    if (perPageSelect) {
                        perPageSelect.addEventListener('change', function() {
                            perPage = parseInt(this.value);
                            loadApprovalPenerimaanData(1);
                        });
                    }
                }, 100);
                
                console.log('SimpleDatatables initialized');
            } else {
                console.error('ApprovalPenerimaantb element not found or simpleDatatables not loaded');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading approval penerimaan data:', xhr, status, error);
            console.error('Response:', xhr.responseText);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data approval penerimaan: ' + (xhr.responseJSON?.message || error), 'error');
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
                loadApprovalPenerimaanData(currentPage - 1);
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
                loadApprovalPenerimaanData(1);
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
                    loadApprovalPenerimaanData(page);
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
                loadApprovalPenerimaanData(totalPages);
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
                loadApprovalPenerimaanData(currentPage + 1);
            }
        });
        paginationDiv.appendChild(nextBtn);
    }
    
    bottom.appendChild(paginationDiv);
}

function initDateRangePicker() {
    var picker = $('#kt_dashboard_daterangepicker');
    var start = moment();
    var end = moment();

    function cb(start, end) {
        $('#kt_dashboard_daterangepicker').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#po_date').val(start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD'));
        loadApprovalPenerimaanData(1);
    }

    $('#kt_dashboard_daterangepicker').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Initialize date range picker
    initDateRangePicker();
    
    // Load data on page load
    setTimeout(function() {
        loadApprovalPenerimaanData(1);
    }, 200);
    
    // Search handler
    $('#approval_penerimaan_search').on('keyup', function() {
        loadApprovalPenerimaanData(1);
    });
    
    // Filter handlers
    $('#filter_status, #filter_cabang, #filter_dispute, #filter_status_dispute').on('change', function() {
        loadApprovalPenerimaanData(1);
    });
    
    // Detail button handler
    $(document).on('click', '.detail-btn', function(e) {
        e.stopPropagation();
        var id = $(this).data('id');
        var po_id = $(this).data('po-id');
        var poads_invoice = $(this).data('poads-invoice');
        if (!id || !po_id || !poads_invoice) return;
        
        $.ajax({
            type: "POST",
            url: "{{ url('po_receive_detail') }}",
            data: {
                _po_id: po_id
            },
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    $('#ApprovalPenerimaanModal').removeClass('hidden');
                    $('#ApprovalPenerimaanModal').data('poads_invoice', poads_invoice);
                    $('#po_invoice_label').text(r.po_invoice || '');
                    $('#_po_id').val(r.po_id);
                    $('#po_description').val(r.po_description || '');
                    
                    // Get store name from row
                    var row = $('.detail-btn[data-id="' + id + '"]').closest('tr');
                    var st_name = row.find('td:eq(1)').text().trim();
                    $('#st_id_modal').val(st_name);
                    
                    // Get supplier name from row
                    var ps_name = row.find('td:eq(3)').text().trim();
                    $('#ps_name_modal').val(ps_name);
                    
                    // Get stock type name - we need to fetch it from detail table
                    $('#stkt_id_modal').val(''); // Will be populated from detail
                    $('#tax_id_modal').val(r.tax_id || '').trigger('change');
                    $('#dispute_modal').val(r.dispute == '1' ? 'Yes' : 'No');
                    $('#dispute_description').val(r.dispute_description || '');
                    $('#shipping_cost').val(r.po_shipping_cost || '');
                    $('#_mode').val('edit');
                    
                    // Load total price
                    $.ajax({
                        type: "GET",
                        url: "{{ url('apd_total_price') }}",
                        data: {
                            invoice: poads_invoice
                        },
                        success: function(totalPrice) {
                            $('#total_approval_price').text(number_format(totalPrice || 0));
                        }
                    });
                    
                    // Load detail content
                    loadApprovalPenerimaanDetailContent(po_id);
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
        var filter_status = $('#filter_status').val() || '';
        var filter_cabang = $('#filter_cabang').val() || '';
        var filter_dispute = $('#filter_dispute').val() || '';
        var filter_status_dispute = $('#filter_status_dispute').val() || '';
        var date = $('#po_date').val() || '';
        var search = $('#approval_penerimaan_search').val() || '';
        
        var params = new URLSearchParams({
            filter_status: filter_status,
            filter_cabang: filter_cabang,
            filter_dispute: filter_dispute,
            filter_status_dispute: filter_status_dispute,
            date: date,
            search: search
        });
        
        window.location.href = "{{ url('apd_export') }}?" + params.toString();
    });
    
    // Close modal handler
    $('#close_approval_penerimaan_modal_btn').on('click', function() {
        $('#ApprovalPenerimaanModal').addClass('hidden');
    });
});

function loadApprovalPenerimaanDetailContent(po_id) {
    // Get poads_invoice from the modal data
    var poads_invoice = $('#ApprovalPenerimaanModal').data('poads_invoice');
    if (!poads_invoice) {
        $('#approval_penerimaan_detail_content').html('<div class="text-center text-gray-500 p-4">Invoice tidak ditemukan</div>');
        return;
    }
    
    $.ajax({
        type: "GET",
        url: "{{ url('apd_datatables') }}",
        data: {
            poads_invoice: poads_invoice
        },
        dataType: 'html',
        success: function(r) {
            $('#approval_penerimaan_detail_content').html(r);
        },
        error: function() {
            $('#approval_penerimaan_detail_content').html('<div class="text-center text-gray-500 p-4">Error loading detail content</div>');
        }
    });
}

function number_format(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}
</script>
@endpush
