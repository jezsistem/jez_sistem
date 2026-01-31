<script>
var onlineTransactionTable = null;
var detailTable = null;

function formatRupiah(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function loadOnlineTransactionData() {
    $.ajax({
        url: "{{ url('transaksi_online_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#online_transaction_search').val(),
            st_id: $('#st_id_filter').val(),
            status: $('#filter_status').val()
        },
        dataType: 'json',
        success: function(response) {
            if (onlineTransactionTable) {
                onlineTransactionTable.destroy();
            }
            
            $('#OnlineTransactionb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.order_number || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.no_resi || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.platform_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.order_date_created || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.total_item || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.shipping_fee || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.total_payment || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.order_status || '') + '</td>';
                    html += '</tr>';
                    $('#OnlineTransactionb tbody').append(html);
                });
            } else {
                $('#OnlineTransactionb tbody').append('<tr><td colspan="9" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("OnlineTransactionb") && typeof simpleDatatables !== 'undefined') {
                onlineTransactionTable = new simpleDatatables.DataTable("#OnlineTransactionb", {
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
            console.error('Error loading transaction data:', xhr);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data transaksi', 'error');
        }
    });
}

function loadDetailData(to_id) {
    $.ajax({
        url: "{{ url('transaksi_online_datatables_detail_simple') }}",
        type: 'GET',
        data: {
            to_id: to_id
        },
        dataType: 'json',
        success: function(response) {
            if (detailTable) {
                detailTable.destroy();
            }
            
            $('#Detailtb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.article || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.ps_barcode || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.sku || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.to_qty || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.shopee_price || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.jez_price || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.gap_price || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.total_discount || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.ns_before_admin || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.final_price || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.status_pick || '') + '</td>';
                    html += '</tr>';
                    $('#Detailtb tbody').append(html);
                });
            } else {
                $('#Detailtb tbody').append('<tr><td colspan="12" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Detailtb") && typeof simpleDatatables !== 'undefined') {
                detailTable = new simpleDatatables.DataTable("#Detailtb", {
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
            console.error('Error loading detail data:', xhr);
        }
    });
}

function initDateRangePicker() {
    var picker = $('#kt_dashboard_daterangepicker');
    if (picker.length === 0) {
        return;
    }
    var start = moment();
    var end = moment();

    function cb(start, end, label) {
        var title = '';
        var range = '';
        var hidden_range = '';

        if ((end - start) < 100 || label === 'Hari Ini') {
            title = 'Hari Ini:';
            range = start.format('DD MMM YYYY');
            hidden_range = start.format('YYYY-MM-DD');
        } else if (label === 'Kemarin') {
            title = 'Kemarin:';
            range = start.format('DD MMM YYYY');
            hidden_range = start.format('YYYY-MM-DD');
        } else {
            range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
            hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
        }

        $('#sales_date').val(hidden_range);
        $('#kt_dashboard_daterangepicker_date').html(range);
        $('#kt_dashboard_daterangepicker_title').html(title);
    }

    picker.daterangepicker({
        startDate: start,
        endDate: end,
        opens: 'center',
        applyClass: 'btn-primary',
        cancelClass: 'btn-light-primary',
        locale: {
            format: 'DD MMM YYYY',
            separator: ' - ',
            applyLabel: 'Terapkan',
            cancelLabel: 'Batal',
            fromLabel: 'Dari',
            toLabel: 'Sampai',
            customRangeLabel: 'Custom',
            weekLabel: 'W',
            daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            firstDay: 1
        },
        ranges: {
            'Hari Ini': [moment(), moment()],
            'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
            '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
            'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
            'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);
    cb(start, end, '');
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    loadOnlineTransactionData();
    initDateRangePicker();
    
    // Filter handlers
    $('#st_id_filter, #filter_status').on('change', function() {
        loadOnlineTransactionData();
    });
    
    // Search handler
    $('#online_transaction_search').on('keyup', function() {
        var query = $(this).val();
        if (jQuery.trim(query).length > 3 || jQuery.trim(query).length < 1) {
            loadOnlineTransactionData();
        }
    });
    
    // Export handler
    $(document).on('click', '#sales_online_export', function(e) {
        e.preventDefault();
        let date = $('#sales_date').val();
        let branch_trx = $('#branch_trx').val();
        let status_trx = $('#status_trx').val();
        let changeplatform = $('#changeplatform').val();
        
        window.location.href = "{{ url('online_sales_export') }}?branch=" + branch_trx + 
                            "&date=" + date + 
                            "&status=" + status_trx + 
                            "&changeplatform=" + changeplatform;
    });
    
    // Detail button handler
    $(document).on('click', '#detail_btn', function(e) {
        e.preventDefault();
        var to_id = $(this).attr('data-to_id');
        var num_order = $(this).attr('data-num_order');
        var status = $(this).attr('data-status');
        
        $('#to_id').val(to_id);
        $('#num_order').text(num_order);
        $('#status_pesanan').val(status);
        
        loadDetailData(to_id);
        document.getElementById('DetailModal').classList.remove('hidden');
    });
    
    // Print invoice handler
    $(document).on('click', '#print_invoice', function() {
        var numOrder = document.getElementById('num_order').textContent;
        
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to print this invoice #" + numOrder + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, print it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url('print_online_invoice') }}',
                    method: 'POST',
                    data: {
                        orderNumber: numOrder,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            var printUrl = '{{ url('print_online_nota') }}/' + numOrder;
                            window.open(printUrl, '_blank');
                            loadOnlineTransactionData();
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Harap melakukan Picker di Data Stok.',
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'There was a problem printing the invoice. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }
        });
    });
    
    // Import modal handler
    $(document).on('click', '#import_modal_btn', function() {
        document.getElementById('ImportModal').classList.remove('hidden');
    });
    
    // Import form handler
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        $('#import_data_btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...');
        $('#import_data_btn').attr('disabled', true);
        
        var formData = new FormData(this);
        var st_id_form_value = $('#st_id_form').val();
        formData.append('st_id_form', st_id_form_value);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('transaksi_online_import') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $('#import_data_btn').html('Import');
                $('#import_data_btn').attr("disabled", false);
                
                if (data.status == '200') {
                    document.getElementById('ImportModal').classList.add('hidden');
                    Swal.fire('Berhasil', 'Data berhasil diimport', 'success');
                    $('#f_import')[0].reset();
                    loadOnlineTransactionData();
                } else if (data.status == '422') {
                    document.getElementById('ImportModal').classList.add('hidden');
                    Swal.fire('Belum dimacro ya jez? 😒😒', data.message || 'File yang anda import kosong atau format tidak tepat', 'warning');
                } else {
                    document.getElementById('ImportModal').classList.add('hidden');
                    Swal.fire('Error', data.message || 'Terjadi kesalahan saat mengimpor data', 'error');
                }
            },
            error: function(data) {
                $('#import_data_btn').html('Import');
                $('#import_data_btn').attr("disabled", false);
                Swal.fire('Error', 'Terjadi kesalahan saat mengimpor data', 'error');
            }
        });
    });
});
</script>
