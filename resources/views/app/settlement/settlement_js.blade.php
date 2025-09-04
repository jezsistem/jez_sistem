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

    function resetSelected(){
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
                        return '<input type="checkbox" class="row-checkbox" id="check_' + row.id + '">';
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

        $('#check_all_data').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('input[id^="check_"]').prop('checked', isChecked);

            var checkedCount = 0;
            var totalNetsales = 0;

            if (isChecked) {
                $('#SettlementTable tbody input[id^="check_"]:checked').each(function() {
                    checkedCount++;
                    var row = $(this).closest('tr');
                    var netsalesText = row.find('td:eq(5)').text().replace(/[^\d.-]/g, '');
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
            jQuery.noConflict();
            $('#SettlementDetailModal').modal('show');
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
                    resetSelected();
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
                var netsalesText = row.find('td:eq(5)').text().replace(/[^\d.-]/g, '');
                var netsalesValue = parseFloat(netsalesText) || 0;
                totalNetsales += netsalesValue;
            });

            $('#selected').text(checkedCount);

            var formattedNetsales = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(totalNetsales);

            $('#selected_netsales').text(formattedNetsales);
        });
    });
</script>
