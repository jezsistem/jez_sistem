<script>
var currentPage = 1;
var perPage = 25;

function loadTotalDanaCair() {
    var search = $('#cek_dana_online_search').val();
    var st_id = $('#st_id').val();
    var platform = $('#filter_platform').val();
    var status = $('#filter_status').val();
    var filter_trx_date = $('#use_trx_date_filter').is(':checked') ? ($('#trx_date_start').val() + '|' + $('#trx_date_end').val()) : null;
    var filter_cash_out_date = $('#use_cash_out_date_filter').is(':checked') ? ($('#cash_out_date_start').val() + '|' + $('#cash_out_date_end').val()) : null;

    $.ajax({
        type: "GET",
        dataType: 'json',
        url: "{{ url('cek_dana_online_total_dana_cair') }}",
        data: {
            search: search,
            st_id: st_id,
            platform: platform,
            status: status,
            filter_trx_date: filter_trx_date,
            filter_cash_out_date: filter_cash_out_date
        },
        success: function(response) {
            var formattedTotalDanaCair = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(response.totalDanaCair || 0);
            $('#total_dana_cair').text(formattedTotalDanaCair);
        }
    });
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Set default dates
    var today = new Date().toISOString().split('T')[0];
    $('#trx_date_start').val(today);
    $('#trx_date_end').val(today);
    $('#cash_out_date_start').val(today);
    $('#cash_out_date_end').val(today);

    // Load initial data
    loadCekDanaOnlineData(1);
    loadTotalDanaCair();

    // Search handler
    var searchTimeout;
    $('#cek_dana_online_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadCekDanaOnlineData(1);
            loadTotalDanaCair();
        }, 500);
    });

    // Filter handlers
    $('#st_id, #filter_platform, #filter_status').on('change', function() {
        loadCekDanaOnlineData(1);
        loadTotalDanaCair();
    });

    // Filter button
    $('#filter_btn').on('click', function() {
        loadCekDanaOnlineData(1);
        loadTotalDanaCair();
    });

    // Date filter checkboxes
    $('#use_trx_date_filter, #use_cash_out_date_filter').on('change', function() {
        loadCekDanaOnlineData(1);
        loadTotalDanaCair();
    });

    // Import modal button
    $('#import_modal_btn').on('click', function() {
        openImportModal();
    });

    // Import form submit
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        var importBtn = $('#import_data_btn');
        importBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('cek_dana_online_import') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                importBtn.html('Import').prop('disabled', false);
                if (data.status == '200') {
                    closeImportModal();
                    Swal.fire('Berhasil', 'Data berhasil diimport', 'success');
                    loadCekDanaOnlineData(currentPage);
                    loadTotalDanaCair();
                } else {
                    closeImportModal();
                    Swal.fire('Gagal', 'Data gagal diimport', 'warning');
                }
            },
            error: function(data) {
                importBtn.html('Import').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Export handler
    $('#export_btn').on('click', function() {
        var search = $('#cek_dana_online_search').val();
        var st_id = $('#st_id').val();
        var platform = $('#filter_platform').val();
        var status = $('#filter_status').val();
        var filter_trx_date = $('#use_trx_date_filter').is(':checked') ? ($('#trx_date_start').val() + '|' + $('#trx_date_end').val()) : null;
        var filter_cash_out_date = $('#use_cash_out_date_filter').is(':checked') ? ($('#cash_out_date_start').val() + '|' + $('#cash_out_date_end').val()) : null;

        var params = new URLSearchParams({
            search: search || '',
            st_id: st_id || '',
            platform: platform || '',
            status: status || 0,
            filter_trx_date: filter_trx_date || '',
            filter_cash_out_date: filter_cash_out_date || ''
        });

        window.location.href = "{{ route('export_transaction_settle') }}?" + params.toString();
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
                        $('#check_all_data').prop('checked', false);
                        updateSelectedCount();
                        loadCekDanaOnlineData(currentPage);
                        loadTotalDanaCair();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Terjadi kesalahan saat melakukan settlement', 'error');
                    }
                });
            }
        });
    });
});

function updateSelectedCount() {
    var checkedCount = 0;
    var totalDanaCair = 0;

    $('input.row-checkbox:checked').each(function() {
        checkedCount++;
        var totalSettle = $(this).attr('data-total-settle') || '0';
        totalDanaCair += parseFloat(totalSettle.replace(/,/g, '')) || 0;
    });

    $('#selected').text(checkedCount);

    var formattedDanaCair = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0
    }).format(totalDanaCair);

    $('#selected_dana_cair').text(formattedDanaCair);
}

function resetSelected() {
    $('#check_all_data').prop('checked', false);
    $('input.row-checkbox').prop('checked', false);
    updateSelectedCount();
}

function loadCekDanaOnlineData(page = 1) {
    currentPage = page;
    var search = $('#cek_dana_online_search').val();
    var st_id = $('#st_id').val();
    var platform = $('#filter_platform').val();
    var status = $('#filter_status').val();
    var filter_trx_date = $('#use_trx_date_filter').is(':checked') ? ($('#trx_date_start').val() + '|' + $('#trx_date_end').val()) : null;
    var filter_cash_out_date = $('#use_cash_out_date_filter').is(':checked') ? ($('#cash_out_date_start').val() + '|' + $('#cash_out_date_end').val()) : null;
    
    $.ajax({
        url: "{{ url('cek_dana_online_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            st_id: st_id,
            platform: platform,
            status: status,
            filter_trx_date: filter_trx_date,
            filter_cash_out_date: filter_cash_out_date
        },
        success: function(response) {
            if (response.error) {
                $('#cek_dana_online_tbody').html('<tr><td colspan="18" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#cek_dana_online_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="18" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    var checkbox = $('<input>').attr({
                        type: 'checkbox',
                        class: 'row-checkbox',
                        id: 'check_' + (row.pos_id || ''),
                        'data-is-partial': '0',
                        'data-total-settle': row.total_settle ? row.total_settle.replace(/,/g, '') : '0'
                    });
                    if (!row.pos_id) {
                        checkbox.prop('disabled', true);
                    }
                    tr.append($('<td>').addClass('px-3 py-4 text-center').append(checkbox));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.platform_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.order_number));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.settle_date));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.revenue));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.total_settle));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.seller_discount));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.total_fee));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.fee_persentage));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.seller_voucher_persentage));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.trx_date));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.jezpro_price));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.diff_jezpro_mp));
                    var statusBtn = $('<span>').addClass('px-2 py-1 text-xs rounded ' + (row.status === 'Done' ? 'bg-green-600 text-white' : 'bg-yellow-600 text-white')).text(row.status);
                    tr.append($('<td>').addClass('px-3 py-4').append(statusBtn));
                    var refundBtn = $('<span>').addClass('px-2 py-1 text-xs rounded ' + (row.status_refund === 'Refund' ? 'bg-red-500 text-white' : 'bg-gray-600 text-white')).text(row.status_refund);
                    tr.append($('<td>').addClass('px-3 py-4').append(refundBtn));
                    var settleBtn = $('<span>').addClass('px-2 py-1 text-xs rounded ' + (row.is_settle === 'SETTLED' ? 'bg-green-600 text-white' : 'bg-gray-400 text-white')).text(row.is_settle);
                    tr.append($('<td>').addClass('px-3 py-4').append(settleBtn));
                    tbody.append(tr);
                });
            }

            createCustomPagination('#CekDanaOnlinetb', response.total, response.current_page, response.total_pages, response.per_page, loadCekDanaOnlineData);
        },
        error: function() {
            $('#cek_dana_online_tbody').html('<tr><td colspan="18" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function openImportModal() {
    $('#ImportModal').removeClass('hidden');
}

function closeImportModal() {
    $('#ImportModal').addClass('hidden');
    $('#f_import')[0].reset();
}

// Pagination function
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#cek_dana_online_pagination_info');
    var paginationControls = wrapper.querySelector('#cek_dana_online_pagination_controls');
    
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
