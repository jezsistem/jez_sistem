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
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return '<input type="checkbox" class="row-checkbox" id="check_' + (meta.row + 1) + '">';
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
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "0%"
                }, {
                    "targets": 1,
                    "className": "text-center",
                    "width": "0%"
                }
            ],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            language: {
                "lengthMenu": "_MENU_",
            },
        });


        $('#pm_id').on('change', function() {
            settlement_table.draw();
            loadNetSalesPerPaymentMethod();
        });

        $('#check_all_data').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('input[id^="check_"]').prop('checked', isChecked);
        });

        $('#start_date').on('change', function() {
            settlement_table.draw();
            loadNetSalesPerPaymentMethod();
        });
        $('#end_date').on('change', function() {
            settlement_table.draw();
            loadNetSalesPerPaymentMethod();
        });

        $('#st_id').on('change', function() {
            settlement_table.draw();
            loadNetSalesPerPaymentMethod();
        });

        $('#status_trx').on('change', function() {
            settlement_table.draw();
            loadNetSalesPerPaymentMethod();
        });

        $('#SettlementTable tbody').on('click', 'tr', function() {
            jQuery.noConflict();
            $('#SettlementDetailModal').modal('show');
        });
    });
</script>
