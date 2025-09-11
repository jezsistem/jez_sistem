<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    var settlement_table = '';
    var selectedRows = [];

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
                pm_id: $('#payment_method_select').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                status_trx: $('#status_trx').val()
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
                pm_id: $('#payment_method_select').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                status_trx: $('#status_trx').val()
            },
            success: function(response) {
                var formattedAmount = new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0
                }).format(response.total_netsales);

                $('#total_netsales').text(formattedAmount);
            }
        });
    }

    function resetSelected() {
        $('#selected').text('0')
        $('#selected_netsales').text('0')
        $('#check_all_data').prop('checked', false);
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        loadPaymentMethods();

        settlement_table = $('#SettlementTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            searching: false,
            ajax: {
                url: "{{ url('settlement_datatables') }}",
                data: function(d) {
                    d.st_id = $('#st_id').val();
                    d.pm_id = $('#payment_method_select').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.status_trx = $('#status_trx').val();
                }
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return '<input type="checkbox" class="row-checkbox" id="check_' + row
                            .id + '">';
                    },
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'pos_invoice',
                    name: 'pos_invoice'
                },
                {
                    data: 'st_name',
                    name: 'st_name'
                },
                {
                    data: 'qty',
                    name: 'qty'
                },
                {
                    data: 'netsales',
                    name: 'netsales'
                },
                {
                    data: 'pm_name',
                    name: 'pm_name'
                },
                {
                    data: 'sub_payment',
                    name: 'sub_payment'
                },
                {
                    data: 'pos_status',
                    name: 'pos_status'
                },
                {
                    data: 'is_settle',
                    name: 'is_settle'
                }
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }, {
                "targets": 1,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            language: {
                "lengthMenu": "_MENU_",
            },
        });

        $(document).ready(function() {
            $('#st_id').select2({
                placeholder: "-- Pilih Payment Method --"
            });
        });

        $(document).ready(function() {
            $('#status_trx').select2({
                placeholder: "-- Pilih Payment Method --"
            });
        });

        $('#check_all_data').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('input[id^="check_"]').prop('checked', isChecked);

            var checkedCount = 0;
            var totalNetsales = 0;

            if (isChecked) {
                $('#SettlementTable tbody input[id^="check_"]:checked').each(function() {
                    checkedCount++;
                    var row = $(this).closest('tr');
                    var netsalesText = row.find('td:eq(5)').text().replace(/Rp\s*/g, '').replace(/\./g, '');
                    var netsalesValue = parseFloat(netsalesText) || 0;
                    totalNetsales += netsalesValue;
                });
            }

            $('#selected').text(checkedCount);

            var formattedNetsales = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(totalNetsales);

            $('#selected_netsales').text(formattedNetsales);
        });

        $('#filter_btn').on('click', function() {
            var stId = $('#st_id').val();
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();

            if (!stId || !startDate || !endDate) {
                toastr.warning('Please select Store, Start Date, and End Date before filtering.');
                return;
            }

            loadTotalNetsales();
            loadNetSalesPerPaymentMethod();
            settlement_table.draw();
            resetSelected();
        });

        $('#reset_btn').on('click', function() {
            $('#st_id').val('');
            $('#payment_method_select').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#status_trx').val('');
            loadPaymentMethods();
            loadTotalNetsales();
            loadNetSalesPerPaymentMethod();
            settlement_table.draw();
            resetSelected();
        });

        $('#SettlementTable tbody').on('click', 'tr', function() {
            var data = settlement_table.row(this).data();
            var id = data.id;

            $.ajax({
                type: "GET",
                url: "{{ url('settlement_detail') }}/" + id,
                success: function(response) {
                    // Handle the response here
                    // You can display the data in a modal, update a section of the page, etc.
                    console.log(response);
                    jQuery.noConflict();
                    $('#SettlementDetailModal').modal('show');
                    $('#transaction_date').text(response.transaction_date);
                    $('#store_name').text(response.store_name);
                    $('#receipt_number').text(response.receipt_number);
                    // Set transaction status with conditional styling
                    var trxStatus = response.trx_status;
                    var trxStatusElement = $('#trx_status');
                    trxStatusElement.text(trxStatus);

                    // Remove existing classes
                    trxStatusElement.removeClass('btn-success btn-warning btn-info');

                    // Add appropriate class based on status
                    if (trxStatus === 'DONE') {
                        trxStatusElement.addClass('btn btn-success');
                    } else if (trxStatus === 'REFUND') {
                        trxStatusElement.addClass('btn btn-warning');
                    } else if (trxStatus === 'DP') {
                        trxStatusElement.addClass('btn btn-info');
                    }

                    $('#order_number').text(response.order_number);
                    // Set payment status with conditional styling
                    var paymentStatus = response.payment_status;
                    var statusElement = $('#payment_status');
                    statusElement.text(paymentStatus);

                    // Remove existing classes
                    statusElement.removeClass('btn-success btn-warning btn-info');

                    if (trxStatus === 'DONE') {
                        statusElement.addClass('btn btn-success');
                    } else if (trxStatus === 'REFUND') {
                        statusElement.addClass('btn btn-warning');
                    } else if (trxStatus === 'DP') {
                        statusElement.addClass('btn btn-info');
                    }
                    $('#outstanding_balance').text('Rp ' + new Intl.NumberFormat('id-ID')
                        .format(response.outstanding_balance));
                    $('#payment_method_1').text(response.payment_method_1);
                    $('#sub_payment_method_1').text(response.sub_payment_method_1);
                    $('#payment_amount_1').text('Rp ' + new Intl.NumberFormat('id-ID')
                        .format(response.payment_amount_1));
                    $('#payment_method_2').text(response.payment_method_2);
                    $('#sub_payment_method_2').text(response.sub_payment_method_2);
                    $('#payment_amount_2').text('Rp ' + new Intl.NumberFormat('id-ID')
                        .format(response.payment_amount_2));
                    $('#down_payment').text('Rp ' + new Intl.NumberFormat('id-ID').format(
                        response.down_payment));
                    $('#gross_sales').text('Rp ' + new Intl.NumberFormat('id-ID').format(
                        response.gross_sales));
                    $('#total_discount').text('- Rp ' + new Intl.NumberFormat('id-ID')
                        .format(response.total_discount));
                    $('#net_sales').text('Rp ' + new Intl.NumberFormat('id-ID').format(
                        response.net_sales));
                    $('#total_payment').text('Rp ' + new Intl.NumberFormat('id-ID').format(
                        response.total_payment || 0));
                    $('#cogs').text('Rp ' + new Intl.NumberFormat('id-ID').format(response
                        .cogs));
                    $('#seller_voucher').text('Rp ' + new Intl.NumberFormat('id-ID').format(
                        response.seller_voucher));
                    $('#total_admin_fee').text('Rp ' + new Intl.NumberFormat('id-ID')
                        .format(response.total_admin_fee || 0));
                    $('#outstanding_balance_summary').text('Rp ' + new Intl.NumberFormat(
                        'id-ID').format(response.outstanding_balance));
                    $('#total_dana_cair').text('Rp ' + new Intl.NumberFormat('id-ID')
                        .format(response.total_dana_cair || 0));
                    $('#gross_margin').text('Rp ' + new Intl.NumberFormat('id-ID').format(
                        response.gross_margin));
                    $('#margin_percentage').text(response.margin_percentage);
                    $('#btn_print_receipt').attr('href', response.print_receipt_url);
                    $('#note').text(response.note || '-');

                    // Clear existing table data
                    $('#SettlementItemsTable tbody').empty();

                    // Populate the items table
                    if (response.items && response.items.length > 0) {
                        response.items.forEach(function(item) {
                            var row = '<tr>' +
                                '<td>' + item.article_id + '</td>' +
                                '<td>' + item.p_name + '</td>' +
                                '<td>' + item.ps_barcode + '</td>' +
                                '<td>' + item.pos_td_qty + '</td>' +
                                '<td>Rp ' + new Intl.NumberFormat('id-ID').format(
                                    item.ps_price_tag) + '</td>' +
                                '<td>' + item.is_nameset + '</td>' +
                                '<td>' + (item.discount ? 'Rp ' + new Intl
                                    .NumberFormat('id-ID').format(item.discount) :
                                    '-') + '</td>' +
                                '<td>Rp ' + new Intl.NumberFormat('id-ID').format(
                                    item.price_after_discount) + '</td>' +
                                '</tr>';
                            $('#SettlementItemsTable tbody').append(row);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error: ' + error);
                }
            });
        });

        // Add stop propagation for checkbox clicks
        $('#SettlementTable tbody').on('click', 'input[id^="check_"]', function(e) {
            e.stopPropagation();
        });

        $('#settlement_btn').on('click', function() {
            var checkedIds = [];
            $('#SettlementTable tbody input[id^="check_"]:checked').each(function() {
                var checkId = $(this).attr('id');
                var numberPart = checkId.replace('check_', '');
                checkedIds.push(numberPart);
            });

            if (checkedIds.length === 0) {
                alert('Please select at least one item to settle.');
                return;
            }

            $.ajax({
                type: "POST",
                url: "{{ url('settlement_bulk_status') }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    checked_ids: checkedIds
                },
                success: function(response) {
                    // Handle success response
                    settlement_table.draw();
                    loadNetSalesPerPaymentMethod();
                    resetSelected();
                    toastr.success('Selected transactions have been settled successfully.');
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.log('Error: ' + error);
                }
            });
        });

        $('#SettlementTable tbody').on('change', 'input[id^="check_"]', function() {
            var checkedCount = 0;
            var totalNetsales = 0;

            $('#SettlementTable tbody input[id^="check_"]:checked').each(function() {
                checkedCount++;
                var row = $(this).closest('tr');
                var netsalesText = row.find('td:eq(5)').text().replace(/Rp\s*/g, '').replace(/\./g, '');
                var netsalesValue = parseFloat(netsalesText) || 0;
                totalNetsales += netsalesValue;
            });

            $('#selected').text(checkedCount);

            var formattedNetsales = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(totalNetsales);

            $('#selected_netsales').text(formattedNetsales);
        });

        $('#export_trx').on('click', function() {
            var url = "{{ url('settlement_export_transaction') }}";
            var params = new URLSearchParams({
                st_id: $('#st_id').val() || '',
                pm_id: $('#payment_method_select').val() || '',
                start_date: $('#start_date').val() || '',
                end_date: $('#end_date').val() || '',
                status_trx: $('#status_trx').val() || ''
            });
            
            window.open(url + '?' + params.toString(), '_blank');
        });
    });
</script>
