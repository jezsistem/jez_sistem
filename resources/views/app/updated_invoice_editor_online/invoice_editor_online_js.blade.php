<script>
var pt_id = '';
var invoiceTable = null;
var detailTable = null;
var trackingTable = null;
var historyTable = null;

function checkUnDone() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: "{{ url('ie_online_permission_check_active_edit') }}",
        success: function(r) {
            if (r.status == '200') {
                $('#pos_invoice_online').val(r.invoice);
                setTimeout(() => {
                    $('#exec_online_btn').trigger('click');
                }, 300);
            }
        }
    });
    return false;
}

function loadInvoiceData() {
    $.ajax({
        url: "{{ url('ie_online_permission_invoice_datatables_simple') }}",
        type: 'GET',
        data: {
            pt_id: pt_id
        },
        dataType: 'json',
        success: function(response) {
            if (invoiceTable) {
                invoiceTable.destroy();
            }
            
            $('#Invoicetb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.order_number || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.no_resi || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.st_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.u_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.order_status || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.platform_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.order_date_created || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.shipping_method || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.shipping_fee || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.payment_method || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.total_payment || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.time_print || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.action || '') + '</td>';
                    html += '</tr>';
                    $('#Invoicetb tbody').append(html);
                });
            } else {
                $('#Invoicetb tbody').append('<tr><td colspan="14" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Invoicetb") && typeof simpleDatatables !== 'undefined') {
                invoiceTable = new simpleDatatables.DataTable("#Invoicetb", {
                    searchable: false,
                    sortable: false,
                    perPage: 10,
                    classes: {
                        wrapper: "datatable-wrapper",
                        top: "datatable-top",
                        bottom: "datatable-bottom",
                        table: "datatable-table"
                    }
                });
            }
        },
        error: function(xhr) {
            console.error('Error loading invoice data:', xhr);
        }
    });
}

function loadDetailData() {
    $.ajax({
        url: "{{ url('ie_online_permission_detail_datatables_simple') }}",
        type: 'GET',
        data: {
            pt_id: pt_id
        },
        dataType: 'json',
        success: function(response) {
            if (detailTable) {
                detailTable.destroy();
            }
            
            $('#InvoiceDetailtb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.article || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.sku || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.qty || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.original_price || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.discount_seller || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.total_discount || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.final_price || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.action || '') + '</td>';
                    html += '</tr>';
                    $('#InvoiceDetailtb tbody').append(html);
                });
            } else {
                $('#InvoiceDetailtb tbody').append('<tr><td colspan="9" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("InvoiceDetailtb") && typeof simpleDatatables !== 'undefined') {
                detailTable = new simpleDatatables.DataTable("#InvoiceDetailtb", {
                    searchable: false,
                    sortable: false,
                    perPage: 10,
                    classes: {
                        wrapper: "datatable-wrapper",
                        top: "datatable-top",
                        bottom: "datatable-bottom",
                        table: "datatable-table"
                    }
                });
            }
        },
        error: function(xhr) {
            console.error('Error loading detail data:', xhr);
        }
    });
}

function loadTrackingData() {
    $.ajax({
        url: "{{ url('ie_online_permission_tracking_datatables_simple') }}",
        type: 'GET',
        data: {
            pt_id: pt_id
        },
        dataType: 'json',
        success: function(response) {
            if (trackingTable) {
                trackingTable.destroy();
            }
            
            $('#Trackingtb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pl_code || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.article || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.plst_qty || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.status || '') + '</td>';
                    html += '</tr>';
                    $('#Trackingtb tbody').append(html);
                });
            } else {
                $('#Trackingtb tbody').append('<tr><td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Trackingtb") && typeof simpleDatatables !== 'undefined') {
                trackingTable = new simpleDatatables.DataTable("#Trackingtb", {
                    searchable: false,
                    sortable: false,
                    perPage: 10,
                    classes: {
                        wrapper: "datatable-wrapper",
                        top: "datatable-top",
                        bottom: "datatable-bottom",
                        table: "datatable-table"
                    }
                });
            }
        },
        error: function(xhr) {
            console.error('Error loading tracking data:', xhr);
        }
    });
}

function loadHistoryData() {
    $.ajax({
        url: "{{ url('ie_online_permission_history_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#history_search').val()
        },
        dataType: 'json',
        success: function(response) {
            if (historyTable) {
                historyTable.destroy();
            }
            
            $('#Historytb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.order_number || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.u_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.activity || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.note || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.created_at || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.updated_at || '') + '</td>';
                    html += '</tr>';
                    $('#Historytb tbody').append(html);
                });
            } else {
                $('#Historytb tbody').append('<tr><td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Historytb") && typeof simpleDatatables !== 'undefined') {
                historyTable = new simpleDatatables.DataTable("#Historytb", {
                    searchable: true,
                    sortable: true,
                    perPage: 25,
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
            }
        },
        error: function(xhr) {
            console.error('Error loading history data:', xhr);
        }
    });
}

function checkInvoice(invoice) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        data: {pos_invoice: invoice},
        dataType: 'json',
        url: "{{ url('ie_online_permission_invoice') }}",
        success: function(r) {
            if (r.status == '200') {
                pt_id = r.id;
                $('.editor_panel').removeClass('hidden');
                Swal.fire('Berhasil', 'Invoice berhasil ditemukan', 'success');
                $('#pos_invoice_online').prop('readonly', true);
            } else {
                pt_id = '';
                $('.editor_panel').addClass('hidden');
                Swal.fire('Gagal', 'Invoice tidak ditemukan, atau sudah lewat dari 7 hari, hanya administrator yang bisa edit lebih dari 2 hari', 'error');
            }
            loadInvoiceData();
            loadDetailData();
            loadTrackingData();
        }
    });
    return false;
}

function doEdit(type, id, value, pt_id, qty, nameset, price, payment_other) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        data: {type: type, id: id, value: value, pt_id: pt_id, qty: qty, nameset: nameset, price: price, payment_other: payment_other},
        dataType: 'json',
        url: "{{ url('ie_online_permission_do_edit') }}",
        success: function(r) {
            if (r.status == '200') {
                loadInvoiceData();
                loadDetailData();
                loadTrackingData();
                loadHistoryData();
                Swal.fire('Berhasil', 'Data berhasil diubah', 'success');
            }
        }
    });
    return false;
}

function editSku(type, id, sku, value) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        data: {type: type, id: id, sku: value},
        dataType: 'json',
        url: "{{ url('ie_online_permission_edit_sku') }}",
        success: function(r) {
            if (r.status == '200') {
                loadInvoiceData();
                loadDetailData();
                loadTrackingData();
                loadHistoryData();
                Swal.fire('Berhasil', 'Data berhasil diubah', 'success');
            }
        }
    });
    return false;
}

$(document).ready(function() {
    checkUnDone();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Load history data on page load (if administrator)
    @if ($data['user']->g_name == 'administrator')
    loadHistoryData();
    @endif
    
    // Execute button
    $(document).on('click', '#exec_online_btn', function(e) {
        e.preventDefault();
        var inv = $.trim($('#pos_invoice_online').val());
        checkInvoice(inv);
    });
    
    // Done button
    $(document).on('click', '#done_btn', function(e) {
        e.preventDefault();
        Swal.fire({
            title: "Selesai..?",
            text: "Yakin selesai edit ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yakin',
            cancelButtonText: 'Belum'
        }).then(function(isConfirm) {
            if (isConfirm.isConfirmed) {
                var note = $('#note').val();
                $.ajax({
                    type: "POST",
                    data: {pt_id: pt_id, note: note},
                    dataType: 'json',
                    url: "{{ url('ie_online_permission_done_edit') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            $('.editor_panel').addClass('hidden');
                            $('#pos_invoice_online').val('');
                            $('#note').val('');
                            $('#pos_invoice_online').prop('readonly', false);
                            pt_id = '';
                            loadInvoiceData();
                            loadDetailData();
                            loadTrackingData();
                            loadHistoryData();
                            Swal.fire('Berhasil', 'Edit selesai', 'success');
                        }
                    }
                });
            }
        });
    });
    
    // SKU change handler
    $(document).on('change', '#sku', function(e) {
        e.preventDefault();
        var type = 'sku';
        var id = $(this).attr('data-tod_id');
        var sku = $(this).attr('data-sku');
        var value = $(this).val();
        editSku(type, id, sku, value);
    });
    
    // Status change handler
    $(document).on('change', '#status', function(e) {
        e.preventDefault();
        var type = 'status';
        var id = $(this).attr('data-id');
        var value = $(this).val();
        doEdit(type, id, value, '', '', '', '', '');
    });
    
    // Cancel item button
    $(document).on('click', '#cancel_item_btn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-tod_id');
        var to_id = $(this).attr('data-to_id');
        
        Swal.fire({
            title: "Cancel Item..?",
            text: "Jika cancel item akan dihapus dari invoice dan status tracking akan menjadi waiting untuk diinstock..",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Cancel',
            cancelButtonText: 'Batal'
        }).then(function(isConfirm) {
            if (isConfirm.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: {tod_id: id, to_id: to_id},
                    dataType: 'json',
                    url: "{{ url('ie_online_permission_cancel_item') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            loadInvoiceData();
                            loadDetailData();
                            loadTrackingData();
                            loadHistoryData();
                            Swal.fire('Berhasil', 'Data berhasil dicancel', 'success');
                        }
                    }
                });
            }
        });
    });
    
    // Cancel invoice button
    $(document).on('click', '#cancel_btn', function(e) {
        e.preventDefault();
        var pt_id_cancel = $(this).attr('data-pt_id');
        
        Swal.fire({
            title: "Cancel Invoice..?",
            text: "Invoice dan item akan dihapus dan seluruh item pada tracking akan menjadi waiting untuk diinstock",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Cancel',
            cancelButtonText: 'Batal'
        }).then(function(isConfirm) {
            if (isConfirm.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: {pt_id: pt_id_cancel},
                    dataType: 'json',
                    url: "{{ url('ie_online_permission_cancel_invoice') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            $('.editor_panel').addClass('hidden');
                            $('#pos_invoice_online').val('');
                            $('#note').val('');
                            $('#pos_invoice_online').prop('readonly', false);
                            pt_id = '';
                            loadInvoiceData();
                            loadDetailData();
                            loadTrackingData();
                            loadHistoryData();
                            Swal.fire('Berhasil', 'Data berhasil dicancel', 'success');
                        } else {
                            Swal.fire('Invoice Terhubung', 'Invoice ini sudah pernah di refund / exchange, jika ingin membatalkan silahkan batalkan invoice baru dari invoice ini, yaitu ' + r.invoice, 'warning');
                        }
                    }
                });
            }
        });
    });
    
    // History search handler
    $('#history_search').on('keyup', function() {
        loadHistoryData();
    });
});
</script>
