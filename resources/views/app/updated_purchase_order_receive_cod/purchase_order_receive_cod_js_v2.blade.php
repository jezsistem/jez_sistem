@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Load moment.js first, then daterangepicker
function loadScript(url) {
    return new Promise(function(resolve, reject) {
        var script = document.createElement('script');
        script.src = url;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

// Load moment.js first, then daterangepicker
loadScript('https://cdn.jsdelivr.net/momentjs/latest/moment.min.js')
    .then(function() {
        return loadScript('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js');
    })
    .then(function() {
        console.log('All scripts loaded');
        if (typeof initPageOnScriptsLoaded === 'function') {
            initPageOnScriptsLoaded();
        }
    })
    .catch(function(err) {
        console.error('Failed to load scripts:', err);
    });
</script>
<script>
var penerimaanCODTable = null;
var currentPage = 1;
var perPage = 25;
var totalRecords = 0;
var totalPages = 0;
var approval = '';

function loadPenerimaanCODData(page = 1) {
    console.log('loadPenerimaanCODData called, page:', page);
    currentPage = page;
    
    $.ajax({
        url: "{{ url('penerimaan_cod_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#po_approval_search').val() || '',
            date: $('#po_date').val() || '',
            page: page,
            per_page: perPage
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('Loading penerimaan COD data...');
            $('#APtb tbody').html('<tr><td colspan="9" class="px-4 py-4 text-center text-sm text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>');
        },
        success: function(response) {
            console.log('Response received:', response);
            if (penerimaanCODTable) {
                penerimaanCODTable.destroy();
            }
            
            $('#APtb tbody').empty();
            
            // Always set totalRecords and totalPages from response
            totalRecords = response.total || 0;
            totalPages = response.total_pages || 1;
            
            if (response && response.data && response.data.length > 0) {
                console.log('Data found:', response.data.length, 'rows of', totalRecords, 'total');
                
                // Render rows
                var fragment = document.createDocumentFragment();
                var tbody = document.querySelector('#APtb tbody');
                
                response.data.forEach(function(row) {
                    var tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50 cursor-pointer';
                    tr.setAttribute('data-row', JSON.stringify(row));
                    tr.style.cursor = 'pointer';
                    
                    tr.innerHTML = 
                        '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.no || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.st_name || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.po_invoice || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.ps_name || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-900 font-medium">' + (row.poads_invoice || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.invoice_date || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.receive_date || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.u_name || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.u_receive || '') + '</td>';
                    
                    // Add click handler for row
                    tr.addEventListener('click', function(e) {
                        var rowData = JSON.parse(this.getAttribute('data-row'));
                        openCODModal(rowData);
                    });
                    
                    fragment.appendChild(tr);
                });
                
                tbody.appendChild(fragment);
            } else {
                console.log('No data found');
                $('#APtb tbody').append('<tr><td colspan="9" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Always create pagination even if no data
            if (totalRecords === 0) {
                totalPages = 1;
            }
            
            if (document.getElementById("APtb") && typeof simpleDatatables !== 'undefined') {
                console.log('Initializing SimpleDatatables...');
                penerimaanCODTable = new simpleDatatables.DataTable("#APtb", {
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
                    var simplePagination = document.querySelector('#APtb').closest('.datatable-wrapper').querySelector('.datatable-pagination');
                    if (simplePagination) {
                        simplePagination.style.display = 'none';
                    }
                    
                    var simpleInfo = document.querySelector('#APtb').closest('.datatable-wrapper').querySelector('.datatable-info');
                    if (simpleInfo) {
                        simpleInfo.style.display = 'none';
                    }
                    
                    // Create custom pagination
                    createCustomPagination('#APtb', totalRecords, currentPage, totalPages, perPage);
                    
                    // Handle per page change
                    var perPageSelect = document.querySelector('#APtb').closest('.datatable-wrapper').querySelector('.datatable-selector');
                    if (perPageSelect) {
                        perPageSelect.addEventListener('change', function() {
                            perPage = parseInt(this.value);
                            loadPenerimaanCODData(1);
                        });
                    }
                }, 100);
                
                console.log('SimpleDatatables initialized');
            } else {
                console.error('APtb element not found or simpleDatatables not loaded');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading penerimaan COD data:', xhr, status, error);
            console.error('Response:', xhr.responseText);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data penerimaan COD: ' + (xhr.responseJSON?.message || error), 'error');
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
                loadPenerimaanCODData(currentPage - 1);
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
                loadPenerimaanCODData(1);
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
                    loadPenerimaanCODData(page);
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
                loadPenerimaanCODData(totalPages);
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
                loadPenerimaanCODData(currentPage + 1);
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

    function cb(start, end, label) {
        var title = '';
        var range = '';
        var hidden_range = '';

        if (label == 'All Days' || !start || !end) {
            title = 'All Days';
            range = '';
            hidden_range = '';
        } else if ((end - start) < 100 || label == 'Today') {
            title = 'Today:';
            range = start.format('MMM D');
            hidden_range = start.format('YYYY-MM-DD');
        } else if (label == 'Yesterday') {
            title = 'Yesterday:';
            range = start.format('MMM D');
            hidden_range = start.format('YYYY-MM-DD');
        } else {
            range = start.format('MMM D') + ' - ' + end.format('MMM D');
            hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
        }
        
        console.log('Date range:', hidden_range);
        $('#po_date').val(hidden_range);
        $('#kt_dashboard_daterangepicker_date').html(range);
        $('#kt_dashboard_daterangepicker_title').html(title);
        
        loadPenerimaanCODData(1);
    }

    picker.daterangepicker({
        startDate: start,
        endDate: end,
        opens: 'left',
        ranges: {
            'All Days': [null, null],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    // Initial load with All Days (no date filter)
    cb(null, null, 'All Days');
}

function openCODModal(rowData) {
    console.log('Row clicked', rowData);
    
    var id = rowData.id;
    var po_id = rowData.po_id;
    var st_name = rowData.st_name;
    var ps_name = rowData.ps_name;
    var stkt_id = rowData.stkt_id;
    var tax_id = rowData.tax_id;
    var stkt_name = rowData.stkt_name;
    var tx_name = rowData.tx_name;
    var tgl_terima = rowData.receive_date;
    var po_description = rowData.po_description;
    var shipping_cost = rowData.po_shipping_cost;
    var poads_invoice = rowData.poads_invoice;
    var u_id_approve = rowData.u_id_approve;
    var po_invoice = rowData.po_invoice;
    var pay_date = rowData.pay_date;
    var due_date = rowData.due_date;
    approval = rowData.u_receive;
    var bank_general = rowData.bank_general;
    var payment = rowData.acc_id;
    
    console.log('STORES : ', tgl_terima);
    console.log('POADS ID :', poads_invoice);
    
    function formatRupiah(number) {
        var numberString = Math.round(number).toString();
        var formatted = numberString.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        $('#no_po').text(po_invoice);
        return "Rp. " + formatted;
    }
    
    // Call ajax apd_total_price 
    $.ajax({
        type: "GET",
        data: {
            invoice: poads_invoice
        },
        dataType: 'json',
        url: "{{ url('apd_total_price') }}",
        success: function(r) {
            console.log(r);
            console.log(st_name);
            var priceNumber = typeof r === 'number' ? r : parseFloat(r);
            if (isNaN(priceNumber)) {
                console.error("Invalid number for total approval price:", r);
                priceNumber = 0;
            }
            
            var formattedPrice = formatRupiah(priceNumber);
            $('#total_approval_price').text(formattedPrice);
            
            $('#st_id').val(st_name);
            $('#ps_name').val(ps_name);
            $('#po_description').val(po_description);
            $('#receive_date').val(tgl_terima).prop('readonly', true);
            $('#shipping_cost').val(shipping_cost);
            $('#_po_id').val(po_id);
            $('#total_approval_price').text(formatRupiah(r));
            $('#stkt_id').val(stkt_name);
            $('#tax_id').val(tax_id);
            $('#stkt_name').val(stkt_name);
            $('#tax_name').val(tx_name);
            $('#pay_date').val(pay_date);
            $('#due_date').val(due_date);
            console.log('BANK GENERAL : ', bank_general, 'PAYMENT : ', payment);
            $('#bank_general').val(bank_general).trigger('change');
            $('#acc_id').val(payment).trigger('change');
            
            if (bank_general != null && String(bank_general).trim() !== "") {
                $('#bank_general').prop('disabled', true);
            } else {
                $('#bank_general').prop('disabled', false);
            }
            if (payment != null && String(payment).trim() !== "") {
                $('#acc_id').prop('disabled', true);
            } else {
                $('#acc_id').prop('disabled', false);
            }
            
            // Load invoice images
            loadInvoiceImages(po_id);
            // Load transfer images
            loadTransferImages(po_id);
            // Load dispute files
            loadDisputeFiles(po_id);
        }
    });
    
    $('#ApprovalCODModal').removeClass('hidden');
    $('#invoice_label').text(poads_invoice.replace("&amp;", "&"));
    
    // Load detail table
    loadCODDetailTable(poads_invoice);
}

function loadCODDetailTable(poads_invoice) {
    $.ajax({
        url: "{{ url('pocdetail_datatables') }}",
        type: 'GET',
        data: {
            poads_invoice: poads_invoice
        },
        success: function(response) {
            if (response && response.data) {
                var tbody = $('#CODtb tbody');
                tbody.empty();
                
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-500">' + (index + 1) + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.created_at_show || '') + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.poads_invoice || '') + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.ps_barcode || '') + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.br_name || '') + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.p_name || '') + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.p_color || '') + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.sz_name || '') + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.stkt_name || '') + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.poads_qty || '') + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.ps_qty || '') + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + formatRupiah(row.poads_purchase_price || 0) + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + formatRupiah(row.poads_total_price || 0) + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.delete || '') + '</td>');
                    tbody.append(tr);
                });
            }
        },
        error: function() {
            $('#CODtb tbody').html('<tr><td colspan="14" class="px-4 py-4 text-center text-sm text-gray-500">Error loading detail</td></tr>');
        }
    });
}

function loadInvoiceImages(po_id) {
    $.ajax({
        url: "{{ url('po_invoice_image_datatable_cod') }}",
        type: 'GET',
        data: { _po_id: po_id },
        success: function(response) {
            var tbody = $('#InvoiceImagesTb tbody');
            tbody.empty();
            if (response && response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    var tr = $('<tr>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.image || '') + '</td>');
                    tbody.append(tr);
                });
            } else {
                tbody.append('<tr><td class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada gambar</td></tr>');
            }
        }
    });
}

function loadTransferImages(po_id) {
    $.ajax({
        url: "{{ url('po_transfer_image_datatable_cod') }}",
        type: 'GET',
        data: { _po_id: po_id },
        success: function(response) {
            var tbody = $('#BuktitfImagesTb tbody');
            tbody.empty();
            if (response && response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    var tr = $('<tr>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.image || '') + '</td>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.action || '') + '</td>');
                    tbody.append(tr);
                });
            } else {
                tbody.append('<tr><td colspan="2" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada gambar</td></tr>');
            }
        }
    });
}

function loadDisputeFiles(po_id) {
    $.ajax({
        url: "{{ url('po_dispute_file_datatable') }}",
        type: 'GET',
        data: { _po_id: po_id },
        success: function(response) {
            var tbody = $('#FileDisputeTb tbody');
            tbody.empty();
            if (response && response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    var tr = $('<tr>');
                    tr.append('<td class="px-4 py-3 text-sm text-gray-700">' + (row.file || '') + '</td>');
                    tbody.append(tr);
                });
            } else {
                tbody.append('<tr><td class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada file</td></tr>');
            }
        }
    });
}

function formatRupiah(data) {
    if (data === null || data === undefined || isNaN(data)) {
        return 'Rp. 0';
    }
    var numberString = parseInt(data, 10).toString();
    var formatted = numberString.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return "Rp. " + formatted;
}

// Global function called when scripts are loaded
function initPageOnScriptsLoaded() {
    console.log('initPageOnScriptsLoaded called');
    if (typeof $.fn.daterangepicker === 'function') {
        console.log('daterangepicker available, initializing...');
        initDateRangePicker();
    } else {
        console.warn('daterangepicker not available after load, loading data without date filter');
        loadPenerimaanCODData(1);
    }
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Check if daterangepicker is already available (in case scripts loaded before document.ready)
    if (typeof $.fn.daterangepicker === 'function') {
        console.log('daterangepicker already available');
        initDateRangePicker();
    } else {
        console.log('Waiting for scripts to load...');
        // If not loaded yet, the initPageOnScriptsLoaded callback will handle it
    }
    
    // Search handler
    $('#po_approval_search').on('keyup', function() {
        loadPenerimaanCODData(1);
    });
    
    // Close modal handlers
    $('#close_approval_cod_modal_btn, #close_detail').on('click', function() {
        $('#ApprovalCODModal').addClass('hidden');
        loadPenerimaanCODData(currentPage);
    });
    
    // Approve button handler
    $('#approve_btn').on('click', function() {
        var approvalText = approval.replace(/<[^>]*>/g, ''); // Strip HTML tags
        if (!approvalText.includes('Belum Dibayar')) {
            Swal.fire('Sudah Approve', 'Invoice ini sudah dibayar', 'warning');
            return false;
        }
        
        Swal.fire({
            title: 'Approve?',
            text: 'Yakin approve?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Approve',
            cancelButtonText: 'Batalkan',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: {
                        invoice: $('#invoice_label').text()
                    },
                    dataType: 'json',
                    url: "{{ url('poc_update_is_paid') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            $('#ApprovalCODModal').addClass('hidden');
                            loadPenerimaanCODData(currentPage);
                            Swal.fire("Berhasil", "Data berhasil dibayar", "success");
                        } else {
                            Swal.fire('Gagal', 'Gagal approve data', 'error');
                        }
                    }
                });
            }
        });
    });
    
    // Upload transfer image form
    $('#f_upload_transfer_image').on('submit', function(e) {
        e.preventDefault();
        $('#upload_image_transfer_btn').html('Proses...');
        $('#upload_image_transfer_btn').attr('disabled', true);
        var formData = new FormData(this);
        var po_id = $('#_po_id').val();
        
        formData.append('_po_id', po_id);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('po_transfer_image_cod') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#upload_image_transfer_btn").html('Upload');
                $("#upload_image_transfer_btn").attr("disabled", false);
                $("#UploadImageTransferModal").addClass('hidden');
                
                if (data.status == '200') {
                    Swal.fire('Berhasil', 'Data berhasil diupload', 'success');
                    $('#f_upload_transfer_image')[0].reset();
                    loadTransferImages(po_id);
                } else if (data.status == '400') {
                    Swal.fire('File', 'File yang anda import kosong atau format tidak tepat', 'warning');
                } else {
                    Swal.fire('Gagal', 'Silahkan periksa format input pada template anda', 'error');
                }
            },
            error: function(data) {
                $("#upload_image_transfer_btn").html('Upload');
                $("#upload_image_transfer_btn").attr("disabled", false);
                Swal.fire('Error', 'Terjadi kesalahan saat mengupload data', 'error');
            }
        });
    });
    
    // Delete transfer image
    $(document).on('click', '#delete-image-transfer', function() {
        var id = $(this).data('id');
        
        if (!id) {
            Swal.fire('Error', 'ID tidak ditemukan', 'error');
            return;
        }
        
        Swal.fire({
            title: 'Hapus?',
            text: 'Yakin hapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('po_transfer_image_delete_cod') }}",
                    data: { id: id },
                    dataType: 'json',
                    success: function(r) {
                        if (r.status === '200') {
                            Swal.fire("Berhasil", "Data berhasil dihapus", "success");
                            loadTransferImages($('#_po_id').val());
                        } else {
                            Swal.fire('Gagal', r.message || 'Gagal hapus data', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Terjadi kesalahan saat menghapus data: ' + error, 'error');
                    }
                });
            }
        });
    });
    
    // Delete POADS
    $(document).on('click', '#delete_poads', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        
        Swal.fire({
            title: 'Hapus?',
            text: 'Yakin hapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: { _id: id },
                    dataType: 'json',
                    url: "{{ url('dl_poads_revision') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            loadCODDetailTable($('#invoice_label').text());
                            Swal.fire("Berhasil", "Data berhasil dihapus", "success");
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });
    
    // Pay date change
    $(document).on('change', '#pay_date', function() {
        var pay_date = $(this).val();
        
        $.ajax({
            type: "POST",
            url: "{{ url('poc_change_pay_date') }}",
            data: {
                pay_date: pay_date,
                po_id: $('#_po_id').val()
            },
            success: function(r) {
                let response = typeof r === "string" ? JSON.parse(r) : r;
                if (response.status == '200') {
                    Swal.fire("Success", "Tanggal bayar berhasil di Update", "success");
                } else {
                    Swal.fire("Error", "Gagal update tanggal bayar", "error");
                    console.log(response);
                }
            },
        });
    });
    
    // Bank general change
    $(document).on('change', '#bank_general', function() {
        var bg_id = $(this).val();
        
        $.ajax({
            type: "POST",
            url: "{{ url('po_change_bank_general') }}",
            data: {
                bg_id: bg_id,
                po_id: $('#_po_id').val()
            },
            success: function(r) {
                let response = typeof r === "string" ? JSON.parse(r) : r;
                if (response.status == '200') {
                    // Success
                } else if (response.status == '500') {
                    Swal.fire('Error', response.message, 'error');
                }
            },
        });
    });
    
    // Show modals
    $("#InvoiceImagesBtn").click(function() {
        $("#InvoiceImagesModal").removeClass('hidden');
    });
    
    $("#pembayaranCodBtn").click(function() {
        $("#UploadImageTransferModal").removeClass('hidden');
    });
    
    $("#BuktitfImagesBtn").click(function() {
        $("#BuktitfImagesModal").removeClass('hidden');
    });
    
    $("#DisputeFileBtn").click(function() {
        $("#FileDisputeModal").removeClass('hidden');
    });
    
    // Close sub modals
    $('.close-sub-modal').on('click', function() {
        $(this).closest('.fixed').addClass('hidden');
    });
});
</script>
@endpush
