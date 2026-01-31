<script>
var currentPage = 1;
var perPage = 25;

function toggleExportMenu(menuId) {
    var menu = document.getElementById(menuId);
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

// Close export menu when clicking outside
document.addEventListener('click', function(event) {
    var exportMenus = document.querySelectorAll('[id$="ExportMenu"]');
    exportMenus.forEach(function(menu) {
        if (!menu.contains(event.target) && !event.target.closest('[onclick*="toggleExportMenu"]')) {
            menu.classList.add('hidden');
        }
    });
});

function loadPaymentMethods() {
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "{{ url('settlement_reload_payment_method') }}",
        data: {
            st_id: $('#st_id').val()
        },
        success: function(r) {
            $("#payment_method").html(r);
        }
    });
}

function loadNetSalesPerPaymentMethod() {
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "{{ url('settlement_netsales_per_payment_method') }}",
        data: {
            st_id: $('#st_id').val(),
            pm_id: $('#payment_method_select').val() || 0,
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            status_trx: $('#status_trx').val(),
            status_settle: $('#status_settle').val(),
            status_cogs: $('#status_cogs').val(),
            sub_payment: $('#sub_payment_filter').val()
        },
        success: function(response) {
            $('#payment_calc_cards').html(response);
        }
    });
}

function loadTotalNetsales() {
    $.ajax({
        type: "GET",
        dataType: 'json',
        url: "{{ url('settlement_total_netsales') }}",
        data: {
            st_id: $('#st_id').val(),
            pm_id: $('#payment_method_select').val() || 0,
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            status_trx: $('#status_trx').val(),
            status_settle: $('#status_settle').val(),
            status_cogs: $('#status_cogs').val(),
            sub_payment: $('#sub_payment_filter').val()
        },
        success: function(response) {
            var formattedNetSales = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(response.total_netsales || 0);
            var formattedCOGS = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(response.total_cogs || 0);
            var formattedMargin = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(response.total_margin || 0);
            var formattedDanaCair = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(response.total_dana_cair || 0);

            $('#total_netsales').text(formattedNetSales);
            $('#total_cogs').text(formattedCOGS);
            $('#total_margin').text(formattedMargin);
            $('#margin_percentage').text(response.margin_percentage || '0%');
            $('#total_dana_cair').text(formattedDanaCair);
        }
    });
}

function resetSelected() {
    $('#selected').text('0');
    $('#selected_netsales').text('0');
    $('#check_all_data').prop('checked', false);
    $('input.row-checkbox').prop('checked', false);
}

function updateSelectedCount() {
    var checkedCount = 0;
    var totalNetsales = 0;

    $('input.row-checkbox:checked').each(function() {
        checkedCount++;
        var netsalesRaw = $(this).attr('data-netsales') || '0';
        totalNetsales += parseFloat(netsalesRaw.replace(/,/g, '')) || 0;
    });

    $('#selected').text(checkedCount);

    var formattedNetsales = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0
    }).format(totalNetsales);

    $('#selected_netsales').text(formattedNetsales);
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    loadPaymentMethods();

    // Search handler
    var searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadSettlementData(1);
            resetSelected();
        }, 1000);
    });

    // Filter button
    $('#filter_btn').on('click', function() {
        var stId = $('#st_id').val();
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (stId == 0 || !startDate || !endDate) {
            Swal.fire('Peringatan', 'Silakan pilih Outlet, Tanggal Mulai, dan Tanggal Akhir sebelum filtering', 'warning');
            return;
        }

        loadTotalNetsales();
        loadNetSalesPerPaymentMethod();
        loadSettlementData(1);
        resetSelected();
    });

    // Reset button
    $('#reset_btn').on('click', function() {
        $('#st_id').val('0');
        $('#payment_method_select').val('0');
        $('#start_date').val('');
        $('#end_date').val('');
        $('#status_trx').val('0');
        $('#status_settle').val('0');
        $('#status_cogs').val('0');
        $('#sub_payment_filter').val('0');
        $('#search').val('');

        loadPaymentMethods();
        loadTotalNetsales();
        loadNetSalesPerPaymentMethod();
        loadSettlementData(1);
        resetSelected();
    });

    // Check all handler
    $('#check_all_data').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('input.row-checkbox').prop('checked', isChecked);
        updateSelectedCount();
    });

    // Individual checkbox handler
    $(document).on('change', 'input.row-checkbox', function(e) {
        e.stopPropagation();
        updateSelectedCount();
    });

    // Settlement button handler
    $('#settlement_btn').on('click', function() {
        var checkedIds = [];
        $('input.row-checkbox:checked').each(function() {
            var checkId = $(this).attr('id');
            var is_partial = $(this).attr('data-is-partial') || '0';
            var numberPart = checkId.replace('check_', '');
            if (numberPart && numberPart !== '') {
                checkedIds.push({
                    id: numberPart,
                    is_partial: is_partial
                });
            }
        });

        if (checkedIds.length === 0) {
            Swal.fire('Peringatan', 'Silakan pilih minimal satu item untuk disettlement', 'warning');
            return;
        }

        var selectedCount = $('#selected').text();
        Swal.fire({
            title: 'Konfirmasi Settlement',
            text: 'Apakah Anda yakin ingin menyettlement ' + selectedCount + ' transaksi yang dipilih?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, settlement!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('settlement_bulk_status') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        checked_ids: checkedIds
                    },
                    success: function(response) {
                        Swal.fire('Berhasil', 'Transaksi yang dipilih berhasil disettlement', 'success');
                        loadSettlementData(currentPage);
                        loadNetSalesPerPaymentMethod();
                        loadTotalNetsales();
                        resetSelected();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Terjadi kesalahan saat melakukan settlement', 'error');
                    }
                });
            }
        });
    });

    // Calc Cogs & Price Tag button handler
    $('#calc_cogs_tag_btn').on('click', function() {
        var checkedIds = [];
        $('input.row-checkbox:checked').each(function() {
            var checkId = $(this).attr('id');
            var numberPart = checkId.replace('check_', '');
            if (numberPart && numberPart !== '') {
                checkedIds.push(numberPart);
            }
        });

        if (checkedIds.length === 0) {
            Swal.fire('Peringatan', 'Silakan pilih minimal satu item untuk dihitung', 'warning');
            return;
        }

        var selectedCount = $('#selected').text();
        Swal.fire({
            title: 'Konfirmasi Calculation',
            text: 'Apakah Anda yakin ingin menghitung COGS dan Price Tag untuk ' + selectedCount + ' transaksi yang dipilih?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hitung!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('settlement_calc_cogs_price_tag') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        checked_ids: checkedIds
                    },
                    success: function(response) {
                        Swal.fire('Berhasil', 'COGS dan Price Tag berhasil dihitung', 'success');
                        loadSettlementData(currentPage);
                        loadNetSalesPerPaymentMethod();
                        loadTotalNetsales();
                        resetSelected();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Terjadi kesalahan saat menghitung COGS dan Price Tag', 'error');
                    }
                });
            }
        });
    });

    // Export handlers
    $('#export_trx').on('click', function(e) {
        e.preventDefault();
        var url = "{{ url('settlement_export_transaction') }}";
        var params = new URLSearchParams({
            st_id: $('#st_id').val() || 0,
            pm_id: $('#payment_method_select').val() || 0,
            start_date: $('#start_date').val() || '',
            end_date: $('#end_date').val() || '',
            status_trx: $('#status_trx').val() || '',
            status_settle: $('#status_settle').val() || 0,
            status_cogs: $('#status_cogs').val() || 0,
            sub_payment: $('#sub_payment_filter').val() || 0
        });

        window.open(url + '?' + params.toString(), '_blank');
    });

    $('#export_detail_trx').on('click', function(e) {
        e.preventDefault();
        var url = "{{ url('settlement_export_transaction_detail') }}";
        var params = new URLSearchParams({
            st_id: $('#st_id').val() || 0,
            pm_id: $('#payment_method_select').val() || 0,
            start_date: $('#start_date').val() || '',
            end_date: $('#end_date').val() || '',
            status_trx: $('#status_trx').val() || '',
            status_settle: $('#status_settle').val() || 0,
            status_cogs: $('#status_cogs').val() || 0,
            sub_payment: $('#sub_payment_filter').val() || 0
        });

        window.open(url + '?' + params.toString(), '_blank');
    });

    // Payment method change handler
    $(document).on('change', '#payment_method_select', function() {
        // Only load data if filter has been applied (start_date and end_date are set)
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        var stId = $('#st_id').val();
        
        if (startDate && endDate && stId && stId != '0') {
            loadSettlementData(1);
            loadNetSalesPerPaymentMethod();
            loadTotalNetsales();
            resetSelected();
        }
    });

    // Initial load - don't load data until filter is applied
    // Table will show "Silakan pilih Tanggal Mulai dan Tanggal Akhir" message
    $('#settlement_tbody').html('<tr><td colspan="11" class="px-3 py-4 text-center text-gray-500">Silakan pilih Tanggal Mulai dan Tanggal Akhir, lalu klik Filter</td></tr>');
});

var isLoadingSettlementData = false;

function loadSettlementData(page = 1) {
    // Prevent multiple simultaneous requests
    if (isLoadingSettlementData) {
        return;
    }
    
    currentPage = page;
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    var st_id = $('#st_id').val() || 0;
    var pm_id = $('#payment_method_select').val() || 0;
    var status_trx = $('#status_trx').val() || '';
    var status_settle = $('#status_settle').val();
    var status_cogs = $('#status_cogs').val();
    var search = $('#search').val() || '';
    var sub_payment = $('#sub_payment_filter').val() || 0;

    if (!start_date || !end_date) {
        $('#settlement_tbody').html('<tr><td colspan="11" class="px-3 py-4 text-center text-gray-500">Silakan pilih Tanggal Mulai dan Tanggal Akhir</td></tr>');
        return;
    }

    isLoadingSettlementData = true;
    $('#settlement_tbody').html('<tr><td colspan="11" class="px-3 py-4 text-center text-gray-500"><div class="flex justify-center items-center"><svg class="animate-spin h-5 w-5 mr-3 text-blue-500" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memuat data...</div></td></tr>');

    $.ajax({
        url: "{{ url('settlement_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            start_date: start_date,
            end_date: end_date,
            st_id: st_id,
            pm_id: pm_id,
            status_trx: status_trx,
            status_settle: status_settle,
            status_cogs: status_cogs,
            search: search,
            sub_payment: sub_payment
        },
        success: function(response) {
            if (response.error) {
                $('#settlement_tbody').html('<tr><td colspan="11" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#settlement_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="11" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    var checkbox = $('<input>').attr({
                        type: 'checkbox',
                        class: 'row-checkbox',
                        id: 'check_' + row.id,
                        'data-is-partial': row.is_partial || '0',
                        'data-netsales': row.netsales_raw || '0'
                    });
                    tr.append($('<td>').addClass('px-3 py-4 text-center').append(checkbox));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.date));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pos_invoice));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.qty));
                    tr.append($('<td>').addClass('px-3 py-4').text('Rp ' + row.netsales));
                    tr.append($('<td>').addClass('px-3 py-4').text('Rp ' + row.total_cogs));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pm_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.sub_payment));
                    var statusBtn = $('<span>').addClass('px-2 py-1 text-xs rounded');
                    if (row.pos_status === 'DONE') {
                        statusBtn.addClass('bg-green-600 text-white').text('DONE');
                    } else if (row.pos_status === 'Refund') {
                        statusBtn.addClass('bg-red-500 text-white').text('Refund');
                    } else if (row.pos_status === 'DP') {
                        statusBtn.addClass('bg-blue-600 text-white').text('DP');
                    } else {
                        statusBtn.addClass('bg-yellow-600 text-white').text(row.pos_status);
                    }
                    tr.append($('<td>').addClass('px-3 py-4').append(statusBtn));
                    var settleBtn = $('<span>').addClass('px-2 py-1 text-xs rounded ' + (row.is_settle === 'SETTLED' ? 'bg-green-600 text-white' : 'bg-gray-400 text-white')).text(row.is_settle);
                    tr.append($('<td>').addClass('px-3 py-4').append(settleBtn));
                    tr.on('click', function(e) {
                        if (!$(e.target).is('input[type="checkbox"]')) {
                            openSettlementDetail(row.id, row.is_partial);
                        }
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#SettlementTable', response.total, response.current_page, response.total_pages, response.per_page, loadSettlementData);
            isLoadingSettlementData = false;
        },
        error: function(xhr, status, error) {
            $('#settlement_tbody').html('<tr><td colspan="11" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
            isLoadingSettlementData = false;
        }
    });
}

function openSettlementDetail(id, is_partial) {
    $.ajax({
        type: "GET",
        url: "{{ url('settlement_detail') }}/" + id + "/" + (is_partial || 0),
        success: function(response) {
            $('#SettlementDetailModal').removeClass('hidden');
            $('#transaction_date').text(response.transaction_date || '-');
            $('#store_name').text(response.store_name || '-');
            $('#receipt_number').text(response.receipt_number || '-');
            
            var trxStatus = response.trx_status || '-';
            var trxStatusElement = $('#trx_status');
            trxStatusElement.text(trxStatus);
            trxStatusElement.removeClass('bg-green-600 bg-red-500 bg-blue-600 bg-yellow-600');
            if (trxStatus === 'DONE') {
                trxStatusElement.addClass('px-2 py-1 text-xs rounded bg-green-600 text-white');
            } else if (trxStatus === 'REFUND') {
                trxStatusElement.addClass('px-2 py-1 text-xs rounded bg-red-500 text-white');
            } else if (trxStatus === 'DP') {
                trxStatusElement.addClass('px-2 py-1 text-xs rounded bg-blue-600 text-white');
            } else {
                trxStatusElement.addClass('px-2 py-1 text-xs rounded bg-yellow-600 text-white');
            }

            $('#order_number').text(response.order_number || '-');
            
            var paymentStatus = response.payment_status || '-';
            var statusElement = $('#payment_status');
            statusElement.text(paymentStatus);
            statusElement.removeClass('bg-green-600 bg-yellow-600 bg-blue-600');
            if (trxStatus === 'DONE') {
                statusElement.addClass('px-2 py-1 text-xs rounded bg-green-600 text-white');
            } else if (trxStatus === 'REFUND') {
                statusElement.addClass('px-2 py-1 text-xs rounded bg-yellow-600 text-white');
            } else if (trxStatus === 'DP') {
                statusElement.addClass('px-2 py-1 text-xs rounded bg-blue-600 text-white');
            }

            $('#outstanding_balance').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.outstanding_balance || 0));
            $('#payment_method_1').text(response.payment_method_1 || '-');
            $('#sub_payment_method_1').text(response.sub_payment_method_1 || '-');
            $('#payment_amount_1').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.payment_amount_1 || 0));
            $('#payment_method_2').text(response.payment_method_2 || '-');
            $('#sub_payment_method_2').text(response.sub_payment_method_2 || '-');
            $('#payment_amount_2').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.payment_amount_2 || 0));
            $('#down_payment').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.down_payment || 0));
            $('#gross_sales').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.gross_sales || 0));
            $('#total_discount').text('- Rp ' + new Intl.NumberFormat('id-ID').format(response.total_discount || 0));
            $('#net_sales').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.net_sales || 0));
            $('#total_payment').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.total_payment || 0));
            $('#cogs').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.cogs || 0));
            $('#seller_voucher').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.seller_voucher || 0));
            $('#total_admin_fee').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.total_admin_fee || 0));
            $('#outstanding_balance_summary').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.outstanding_balance || 0));
            $('#dana_cair').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.total_dana_cair || 0));
            $('#gross_margin').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.gross_margin || 0));
            $('#margin_percentage_detail').text(response.margin_percentage || '0%');
            $('#btn_print_receipt').attr('href', response.print_receipt_url || '#');
            $('#note').text(response.note || '-');
            $('#note_settlement').val(response.note_settlement || '').attr('data-id', response.id);
            $('#note_settlement').attr('data-is-partial', is_partial || 0);
            $('#note_dp').text(response.note_dp || '-');

            // Clear and populate items table
            $('#SettlementItemsTable tbody').empty();
            if (response.items && response.items.length > 0) {
                response.items.forEach(function(item) {
                    var row = '<tr class="bg-white border-b hover:bg-gray-50">' +
                        '<td class="px-3 py-4">' + (item.article_id || '-') + '</td>' +
                        '<td class="px-3 py-4">' + (item.p_name || '-') + '</td>' +
                        '<td class="px-3 py-4">' + (item.ps_barcode || '-') + '</td>' +
                        '<td class="px-3 py-4">' + (item.pos_td_qty || 0) + '</td>' +
                        '<td class="px-3 py-4">Rp ' + new Intl.NumberFormat('id-ID').format(item.pos_td_item_price_tag || 0) + '</td>' +
                        '<td class="px-3 py-4">' + (item.is_nameset || '-') + '</td>' +
                        '<td class="px-3 py-4">' + (item.discount ? 'Rp ' + new Intl.NumberFormat('id-ID').format(item.discount) : '-') + '</td>' +
                        '<td class="px-3 py-4">Rp ' + new Intl.NumberFormat('id-ID').format(item.price_after_discount || 0) + '</td>' +
                        '</tr>';
                    $('#SettlementItemsTable tbody').append(row);
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Terjadi kesalahan saat memuat detail transaksi', 'error');
        }
    });
}

function closeSettlementDetail() {
    $('#SettlementDetailModal').addClass('hidden');
}

// Auto-save notes settlement
var noteSettlementTimeout;
$(document).on('input', '#note_settlement', function() {
    var id = $(this).attr('data-id');
    var is_partial = $(this).attr('data-is-partial');
    if (!id) {
        return;
    }

    clearTimeout(noteSettlementTimeout);
    noteSettlementTimeout = setTimeout(function() {
        var note_settlement = $('#note_settlement').val() || '';

        $.ajax({
            type: "POST",
            url: "{{ url('settlement_update_note') }}",
            data: {
                id: id,
                is_partial: is_partial,
                note_settlement: note_settlement,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Silent success
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Gagal menyimpan notes settlement', 'error');
            }
        });
    }, 2000);
});

// Pagination function
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#settlement_pagination_info');
    var paginationControls = wrapper.querySelector('#settlement_pagination_controls');
    
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
