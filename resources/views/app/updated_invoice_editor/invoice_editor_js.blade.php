<script>
var pt_id = '';
var permissionDataTable = null;
var invoiceDataTable = null;
var detailDataTable = null;
var trackingDataTable = null;
var historyDataTable = null;

function checkUnDone() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: "{{ url('ie_permission_check_active_edit') }}",
        success: function(r) {
            if (r.status == '200') {
                $('#pos_invoice').val(r.invoice);
                setTimeout(() => {
                    $('#exec_btn').trigger('click');
                }, 300);
            }
        }
    });
    return false;
}

function loadPermissionData() {
    $.ajax({
        url: "{{ url('ie_permission_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#data_search').val()
        },
        dataType: 'json',
        success: function(response) {
            if (permissionDataTable) {
                permissionDataTable.destroy();
            }
            
            $('#PermissionDatatb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + row.no + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + row.st_name + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.stt_name + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.u_name + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.action + '</td>';
                    html += '</tr>';
                    $('#PermissionDatatb tbody').append(html);
                });
            } else {
                $('#PermissionDatatb tbody').append('<tr><td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("PermissionDatatb") && typeof simpleDatatables !== 'undefined') {
                permissionDataTable = new simpleDatatables.DataTable("#PermissionDatatb", {
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
                        thead: "",
                        tbody: "",
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
            console.error('Error loading permission data:', xhr);
        }
    });
}

function loadInvoiceData() {
    $.ajax({
        url: "{{ url('ie_permission_invoice_datatables_simple') }}",
        type: 'GET',
        data: {
            pt_id: pt_id
        },
        dataType: 'json',
        success: function(response) {
            if (invoiceDataTable) {
                invoiceDataTable.destroy();
            }
            
            $('#Invoicetb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + row.no + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + row.pos_invoice + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.cashier + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.division + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.subdivision + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.method + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.pos_payment + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.method_two + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.pos_payment_partial + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.admin + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.pos_real_price + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.pos_status + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.created_at + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.action + '</td>';
                    html += '</tr>';
                    $('#Invoicetb tbody').append(html);
                });
            } else {
                $('#Invoicetb tbody').append('<tr><td colspan="14" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Invoicetb") && typeof simpleDatatables !== 'undefined') {
                invoiceDataTable = new simpleDatatables.DataTable("#Invoicetb", {
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
        url: "{{ url('ie_permission_detail_datatables_simple') }}",
        type: 'GET',
        data: {
            pt_id: pt_id
        },
        dataType: 'json',
        success: function(response) {
            if (detailDataTable) {
                detailDataTable.destroy();
            }
            
            $('#InvoiceDetailtb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + row.no + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.article + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.pos_td_qty + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.price + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.nameset + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.pos_td_total_price + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.action + '</td>';
                    html += '</tr>';
                    $('#InvoiceDetailtb tbody').append(html);
                });
            } else {
                $('#InvoiceDetailtb tbody').append('<tr><td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("InvoiceDetailtb") && typeof simpleDatatables !== 'undefined') {
                detailDataTable = new simpleDatatables.DataTable("#InvoiceDetailtb", {
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
        url: "{{ url('ie_permission_tracking_datatables_simple') }}",
        type: 'GET',
        data: {
            pt_id: pt_id
        },
        dataType: 'json',
        success: function(response) {
            if (trackingDataTable) {
                trackingDataTable.destroy();
            }
            
            $('#Trackingtb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + row.no + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.pl_code + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.article + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.plst_qty + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.status + '</td>';
                    html += '</tr>';
                    $('#Trackingtb tbody').append(html);
                });
            } else {
                $('#Trackingtb tbody').append('<tr><td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Trackingtb") && typeof simpleDatatables !== 'undefined') {
                trackingDataTable = new simpleDatatables.DataTable("#Trackingtb", {
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
        url: "{{ url('ie_permission_history_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#history_search').val() || ''
        },
        dataType: 'json',
        success: function(response) {
            console.log('History data response:', response);
            
            if (historyDataTable) {
                historyDataTable.destroy();
            }
            
            $('#Historytb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + row.no + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.pos_invoice || '-') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + (row.u_name || '-') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + (row.activity || '-') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + (row.note || '-') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + (row.created_at || '-') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + (row.updated_at || '-') + '</td>';
                    html += '</tr>';
                    $('#Historytb tbody').append(html);
                });
            } else {
                $('#Historytb tbody').append('<tr><td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Historytb") && typeof simpleDatatables !== 'undefined') {
                historyDataTable = new simpleDatatables.DataTable("#Historytb", {
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
                        thead: "",
                        tbody: "",
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
            $('#Historytb tbody').empty();
            $('#Historytb tbody').append('<tr><td colspan="7" class="px-4 py-4 text-center text-sm text-red-500">Error memuat data</td></tr>');
        }
    });
}

$(document).ready(function() {
    checkUnDone();
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial permission data
    loadPermissionData();
    
    // Load initial history data
    loadHistoryData();

    // Search handler for permission
    var searchTimeout;
    $('#data_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        var query = $(this).val();
        searchTimeout = setTimeout(function() {
            loadPermissionData();
        }, 400);
    });

    // Add button handler
    $('#add_btn').on('click', function() {
        openDataModal();
        $('#_id').val('');
        $('#_mode').val('add');
        $('#form')[0].reset();
        $('#delete_btn').hide();
    });

    // Form submit handler
    $('#form').on('submit', function(e) {
        e.preventDefault();
        $("#save_btn").html('Proses ..');
        $("#save_btn").attr("disabled", true);
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('ie_permission_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_btn").html('Simpan');
                $("#save_btn").attr("disabled", false);
                if (data.status == '200') {
                    closeDataModal();
                    loadPermissionData();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else if (data.status == '400') {
                    closeDataModal();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: 'Data tidak tersimpan'
                    });
                }
            },
            error: function(data) {
                $("#save_btn").html('Simpan');
                $("#save_btn").attr("disabled", false);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menyimpan data'
                });
            }
        });
    });

    // Delete button handler
    $(document).on('click', '#delete_btn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        Swal.fire({
            title: 'Hapus..?',
            text: 'Yakin hapus data ini ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: { id: id },
                    dataType: 'json',
                    url: "{{ url('ie_permission_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            loadPermissionData();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data berhasil dihapus',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal hapus data'
                            });
                        }
                    }
                });
            }
        });
    });

    // Check invoice function
    function checkInvoice(invoice) {
        $.ajax({
            type: "POST",
            data: { pos_invoice: invoice },
            dataType: 'json',
            url: "{{ url('ie_permission_invoice') }}",
            success: function(r) {
                if (r.status == '200') {
                    pt_id = r.id;
                    $('.editor_panel').removeClass('hidden');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Invoice berhasil ditemukan',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#pos_invoice').prop('readonly', true);
                    loadInvoiceData();
                    loadDetailData();
                    loadTrackingData();
                } else {
                    pt_id = '';
                    $('.editor_panel').addClass('hidden');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Invoice tidak ditemukan, atau sudah lewat dari 2 hari, hanya administrator yang bisa edit lebih dari 2 hari'
                    });
                }
            }
        });
        return false;
    }

    // Execute button handler
    $(document).on('click', '#exec_btn', function(e) {
        e.preventDefault();
        var inv = $.trim($('#pos_invoice').val());
        if (!inv) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan masukkan nomor invoice'
            });
            return;
        }
        checkInvoice(inv);
    });

    // Done button handler
    $(document).on('click', '#done_btn', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Selesai..?',
            text: 'Yakin selesai edit ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yakin',
            cancelButtonText: 'Belum'
        }).then((result) => {
            if (result.isConfirmed) {
                var a = $('#reason').val();
                var b = $('#note').val().toUpperCase();
                var note = b ? a + " - " + b : a;

                if (!a) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih alasan edit'
                    });
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "{{ url('ie_permission_done_edit') }}",
                    data: {
                        pt_id: pt_id,
                        note: note
                    },
                    dataType: 'json',
                    success: function(r) {
                        if (r.status == 200) {
                            $('.editor_panel').addClass('hidden');
                            $('#pos_invoice').val('');
                            $('#note').val('');
                            $('#reason').val('');
                            $('#pos_invoice').prop('readonly', false);
                            pt_id = '';
                            loadInvoiceData();
                            loadDetailData();
                            loadTrackingData();
                            loadHistoryData();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Edit berhasil diselesaikan',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: r.message || 'Gagal menyelesaikan sesi.'
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 || xhr.status === 400) {
                            let res = JSON.parse(xhr.responseText);
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan',
                                text: res.message || 'Transaksi belum dapat diselesaikan.'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan server.'
                            });
                        }
                    }
                });
            }
        });
    });

    // Do edit function
    function doEdit(type, id, value, pt_id, qty, nameset, price, payment_other) {
        $.ajax({
            type: "POST",
            data: {
                type: type,
                id: id,
                value: value,
                pt_id: pt_id,
                qty: qty,
                nameset: nameset,
                price: price,
                payment_other: payment_other
            },
            dataType: 'json',
            url: "{{ url('ie_permission_do_edit') }}",
            success: function(r) {
                if (r.status == '200') {
                    loadInvoiceData();
                    loadDetailData();
                    loadTrackingData();
                    loadHistoryData();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil diubah',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengubah data'
                });
            }
        });
        return false;
    }

    // Event handlers for editable fields
    $(document).on('change', '#cashier', function(e) {
        e.preventDefault();
        doEdit('cashier', $(this).attr('data-pt_id'), $(this).val(), '', '', '', '', '');
    });

    $(document).on('change', '#division', function(e) {
        e.preventDefault();
        doEdit('division', $(this).attr('data-pt_id'), $(this).val(), '', '', '', '', '');
    });

    $(document).on('change', '#subdivision', function(e) {
        e.preventDefault();
        doEdit('subdivision', $(this).attr('data-pt_id'), $(this).val(), '', '', '', '', '');
    });

    $(document).on('change', '#pos_status_change', function(e) {
        e.preventDefault();
        doEdit('pos_status_change', $(this).attr('data-pt_id'), $(this).val(), '', '', '', '', '');
    });

    $(document).on('change', '#method', function(e) {
        e.preventDefault();
        doEdit('method', $(this).attr('data-pt_id'), $(this).val(), '', '', '', '', '');
    });

    $(document).on('change', '#admin', function(e) {
        e.preventDefault();
        doEdit('admin', $(this).attr('data-pt_id'), $(this).val(), '', '', '', '', '');
    });

    $(document).on('change', '#pos_payment', function(e) {
        e.preventDefault();
        var payment_other = $(this).closest('tr').find('.pos_payment_partial').val();
        doEdit('pos_payment', $(this).attr('data-pt_id'), $(this).val(), '', '', '', '', payment_other);
    });

    $(document).on('change', '#pos_payment_partial', function(e) {
        e.preventDefault();
        var payment_other = $(this).closest('tr').find('.pos_payment').val();
        doEdit('pos_payment_partial', $(this).attr('data-pt_id'), $(this).val(), '', '', '', '', payment_other);
    });

    $(document).on('change', '#date', function(e) {
        e.preventDefault();
        doEdit('date', $(this).attr('data-pt_id'), $(this).val(), '', '', '', '', '');
    });

    $(document).on('change', '#price', function(e) {
        e.preventDefault();
        doEdit('price', $(this).attr('data-ptd_id'), $(this).val(), $(this).attr('data-pt_id'), $(this).attr('data-qty'), $(this).attr('data-nameset'), '', '');
    });

    $(document).on('change', '#nameset', function(e) {
        e.preventDefault();
        doEdit('nameset', $(this).attr('data-ptd_id'), $(this).val(), $(this).attr('data-pt_id'), $(this).attr('data-qty'), '', $(this).attr('data-price'), '');
    });

    $(document).on('change', '#status', function(e) {
        e.preventDefault();
        doEdit('status', $(this).attr('data-id'), $(this).val(), '', '', '', '', '');
    });

    // Cancel item handler
    $(document).on('click', '#cancel_item_btn', function(e) {
        e.preventDefault();
        var ptd_id = $(this).attr('data-ptd_id');
        var pt_id = $(this).attr('data-pt_id');
        var pl_id = $(this).attr('data-pl_id');
        var pst_id = $(this).attr('data-pst_id');
        
        Swal.fire({
            title: 'Cancel Item..?',
            text: 'Jika cancel item akan dihapus dari invoice dan status tracking akan menjadi waiting untuk diinstock..',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Cancel',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: {
                        ptd_id: ptd_id,
                        pt_id: pt_id,
                        pl_id: pl_id,
                        pst_id: pst_id
                    },
                    dataType: 'json',
                    url: "{{ url('ie_permission_cancel_item') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            loadInvoiceData();
                            loadDetailData();
                            loadTrackingData();
                            loadHistoryData();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data berhasil dicancel',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }
                });
            }
        });
    });

    // Cancel invoice handler
    $(document).on('click', '#cancel_btn', function(e) {
        e.preventDefault();
        var pt_id_cancel = $(this).attr('data-pt_id');
        
        Swal.fire({
            title: 'Cancel Invoice..?',
            text: 'Invoice dan item akan dihapus dan seluruh item pada tracking akan menjadi waiting untuk diinstock',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Cancel',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: { pt_id: pt_id_cancel },
                    dataType: 'json',
                    url: "{{ url('ie_permission_cancel_invoice') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            $('.editor_panel').addClass('hidden');
                            $('#pos_invoice').val('');
                            $('#note').val('');
                            $('#reason').val('');
                            $('#pos_invoice').prop('readonly', false);
                            pt_id = '';
                            loadInvoiceData();
                            loadDetailData();
                            loadTrackingData();
                            loadHistoryData();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data berhasil dicancel',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Invoice Terhubung',
                                text: 'Invoice ini sudah pernah di refund / exchange, jika ingin membatalkan silahkan batalkan invoice baru dari invoice ini, yaitu ' + r.invoice
                            });
                        }
                    }
                });
            }
        });
    });

    // History search handler
    var historySearchTimeout;
    $('#history_search').on('keyup', function() {
        clearTimeout(historySearchTimeout);
        var query = $(this).val();
        historySearchTimeout = setTimeout(function() {
            loadHistoryData();
        }, 400);
    });
});
</script>
