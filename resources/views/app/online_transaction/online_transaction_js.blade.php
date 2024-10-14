<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        var online_transaction_table = $('#OnlineTransactionb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [
                {"extend": 'excelHtml5', "text": 'Excel', "className": 'btn btn-primary btn-xs'}
            ],
            ajax: {
                url: "{{ url('transaksi_online_datatables') }}",
                data: function (d) {
                    d.search = $('#online_transaction_search').val();
                    d.st_id = $('#st_id_filter').val();
                    d.status = $('#filter_status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'to_id', searchable: false},
                {data: 'order_number', name: 'to_order_number'},
                {data: 'no_resi', name: 'no_resi'},
                {data: 'platform_name', name: 'platform_name'},
                {data: 'order_date_created', name: 'order_date_created'},
                {data: 'total_item', name: 'total_item'},
                {data: 'shipping_fee', name: 'shipping_fee'},
                {data: 'total_payment', name: 'total_payment'},
                {data: 'order_status', name: 'order_status'},
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "0%"
                }],
            language: {
                "lengthMenu": "_MENU_",
            }
        });

        $('#st_id_filter').on('change', function () {
            online_transaction_table.draw(false);
        });

        // Initialize Select2 on the select element
        $('#filter_status').select2({
            width: "200px",
            dropdownParent: $('#filter_status_parent')
        });

        $('#filter_status').on('change', function () {
            console.log($(this).val()); // Logs the selected value (0 or 1)
            online_transaction_table.draw();
        });

        $(document).delegate('#import_modal', 'click', function () {
            $('#ImportModal').modal('show');
        });

        $('#online_transaction_search').on('keyup', function () {
            online_transaction_table.draw(false);
            console.log($('#online_transaction_search').val())
        });

        {{--$('#f_import').on('submit' , function (e) {--}}
        {{--    e.preventDefault();--}}
        {{--    jQuery.noConflict();--}}
        {{--    $('#import_data_btn').html('Proses...');--}}
        {{--    $('#import_data_btn').attr('disabled', true);--}}
        {{--    var formData = new FormData(this);--}}

        {{--    $.ajax({--}}
        {{--        type: 'POST',--}}
        {{--        url: "{{ url('transaksi_online_import')}}",--}}
        {{--        data: formData,--}}
        {{--        dataType: 'json',--}}
        {{--        cache:false,--}}
        {{--        contentType: false,--}}
        {{--        processData: false,--}}
        {{--        success: function(data) {--}}
        {{--            // console.log(data.data['missingBarcode']);--}}
        {{--            $("#import_data_btn").html('Import');--}}
        {{--            $("#import_data_btn").attr("disabled", false);--}}
        {{--            jQuery.noConflict();--}}
        {{--            if (data.status == '200') {--}}
        {{--                $("#ImportModal").modal('hide');--}}

        {{--                swal('Berhasil', 'Data berhasil diimport', 'success');--}}
        {{--                $('#f_import')[0].reset();--}}
        {{--                excelImportData = data.data['processedData'];--}}
        {{--                console.log(data.data);--}}
        {{--                console.log(data.name);--}}
        {{--                // shopee_tables.draw();--}}
        {{--                // tiktok_tables.draw();--}}
        {{--            } else if (data.status == '400') {--}}
        {{--                $("#ImportModal").modal('hide');--}}
        {{--                console.log(data.data)--}}
        {{--                swal('Error', 'File yang anda import kosong atau format tidak tepat', 'warning');--}}
        {{--            } else {--}}
        {{--                $("#ImportModal").modal('hide');--}}

        {{--            }--}}
        {{--        },--}}
        {{--        error: function(data){--}}
        {{--            swal('Error', data, 'error');--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}

        {{--$('#f_import').on('submit', function (e) {--}}
        {{--    e.preventDefault();--}}
        {{--    jQuery.noConflict();--}}

        {{--    // Show the spinner and disable the button--}}
        {{--    $('#import_data_btn').html('<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...');--}}
        {{--    $('#import_data_btn').attr('disabled', true);--}}

        {{--    var formData = new FormData(this);--}}

        {{--    // Manually append the value of st_id_form to the FormData--}}
        {{--    var st_id_form_value = $('#st_id_form').val();  // Get the value of the disabled input--}}
        {{--    formData.append('st_id_form', st_id_form_value); // Append it to the FormData--}}
        {{--    --}}
        {{--    console.log($('#st_id_form').val());--}}

        {{--    $.ajax({--}}
        {{--        type: 'POST',--}}
        {{--        url: "{{ url('transaksi_online_import')}}",--}}
        {{--        data: formData,--}}
        {{--        dataType: 'json',--}}
        {{--        cache: false,--}}
        {{--        contentType: false,--}}
        {{--        processData: false,--}}
        {{--        success: function (data) {--}}
        {{--            // Hide the spinner and enable the button--}}
        {{--            $('#import_data_btn').html('Import');--}}
        {{--            $('#import_data_btn').attr("disabled", false);--}}

        {{--            if (data.status == '200') {--}}
        {{--                $("#ImportModal").modal('hide');--}}
        {{--                swal('Berhasil', 'Data berhasil diimport', 'success');--}}
        {{--                $('#f_import')[0].reset();--}}
        {{--                excelImportData = data.data['processedData'];--}}
        {{--                console.log(data.data);--}}
        {{--                console.log(data.name);--}}
        {{--            } else if (data.status == '400') {--}}
        {{--                $("#ImportModal").modal('hide');--}}
        {{--                console.log(data.data);--}}
        {{--                swal('Error', 'File yang anda import kosong atau format tidak tepat', 'warning');--}}
        {{--            } else {--}}
        {{--                $("#ImportModal").modal('hide');--}}
        {{--            }--}}
        {{--        },--}}
        {{--        error: function (data) {--}}
        {{--            // Hide the spinner in case of error--}}
        {{--            $('#import_data_btn').html('Import');--}}
        {{--            $('#import_data_btn').attr("disabled", false);--}}

        {{--            swal('Error', data, 'error');--}}
        {{--        }--}}
        {{--    });--}}
        {{--    online_transaction_table.draw(false);--}}
        {{--});--}}

        $('#f_import').on('submit', function (e) {
            e.preventDefault();
            jQuery.noConflict();

            // Show the spinner and disable the button
            $('#import_data_btn').html('<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...');
            $('#import_data_btn').attr('disabled', true);

            // Create a new FormData object from the form
            var formData = new FormData(this);

            // Manually append the value of st_id_form to the FormData
            var st_id_form_value = $('#st_id_form').val();  // Get the value of the disabled input
            formData.append('st_id_form', st_id_form_value); // Append it to the FormData

            console.log(formData);

            $.ajax({
                type: 'POST',
                url: "{{ url('transaksi_online_import') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    // Hide the spinner and enable the button
                    $('#import_data_btn').html('Import');
                    $('#import_data_btn').attr("disabled", false);

                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        swal('Berhasil', 'Data berhasil diimport', 'success');
                        $('#f_import')[0].reset();
                        excelImportData = data.data['processedData'];
                        console.log(data.data);
                        console.log(data.name);
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        console.log(data.data);
                        swal('Error', 'File yang anda import kosong atau format tidak tepat', 'warning');
                    } else {
                        $("#ImportModal").modal('hide');
                    }
                },
                error: function (data) {
                    // Hide the spinner in case of error
                    $('#import_data_btn').html('Import');
                    $('#import_data_btn').attr("disabled", false);

                    swal('Error', data, 'error');
                }
            });
            online_transaction_table.draw(false);
        });
        {{--$('#f_import').on('submit', function (e) {--}}
        {{--    e.preventDefault();--}}
        {{--    jQuery.noConflict();--}}

        {{--    // Show the spinner and disable the button--}}
        {{--    $('#import_data_btn').html('<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...');--}}
        {{--    $('#import_data_btn').attr('disabled', true);--}}

        {{--    // Create a new FormData object from the form--}}
        {{--    var formData = new FormData(this);--}}

        {{--    // Manually append the value of st_id_form to the FormData--}}
        {{--    var st_id_form_value = $('#st_id_form').val();  // Get the value of the disabled input--}}
        {{--    formData.append('st_id_form', st_id_form_value); // Append it to the FormData--}}

        {{--    console.log(formData);--}}

        {{--    $.ajax({--}}
        {{--        type: 'POST',--}}
        {{--        url: "{{ url('transaksi_online_import') }}",--}}
        {{--        data: formData,--}}
        {{--        dataType: 'json',--}}
        {{--        cache: false,--}}
        {{--        contentType: false,--}}
        {{--        processData: false,--}}
        {{--        success: function (data) {--}}
        {{--            // Hide the spinner and enable the button--}}
        {{--            $('#import_data_btn').html('Import');--}}
        {{--            $('#import_data_btn').attr("disabled", false);--}}

        {{--            if (data.status == '200') {--}}
        {{--                $("#ImportModal").modal('hide');--}}
        {{--                swal('Berhasil', 'Data berhasil diimport', 'success');--}}
        {{--                $('#f_import')[0].reset();--}}
        {{--                excelImportData = data.data['processedData'];--}}
        {{--                console.log(data.data);--}}
        {{--                console.log(data.name);--}}
        {{--            } else if (data.status == '400') {--}}
        {{--                $("#ImportModal").modal('hide');--}}
        {{--                console.log(data.data);--}}
        {{--                swal('Error', 'File yang anda import kosong atau format tidak tepat', 'warning');--}}
        {{--            } else {--}}
        {{--                $("#ImportModal").modal('hide');--}}
        {{--            }--}}
        {{--        },--}}
        {{--        error: function (data) {--}}
        {{--            // Hide the spinner in case of error--}}
        {{--            $('#import_data_btn').html('Import');--}}
        {{--            $('#import_data_btn').attr("disabled", false);--}}

        {{--            swal('Error', data, 'error');--}}
        {{--        }--}}
        {{--    });--}}
        {{--    online_transaction_table.draw(false);--}}
        {{--});--}}


        var detail_table = $('#Detailtb').DataTable({

            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [
                {"extend": 'excelHtml5', "text": 'Excel', "className": 'btn btn-primary btn-xs'}
            ],
            ajax: {
                url: "{{ url('transaksi_online_datatables_detail') }}",
                data: function (d) {
                    d.to_id = $('#to_id').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'to_id'},
                {data: 'article', name: 'article'},
                {data: 'ps_barcode', name: 'ps_barcode'},
                {data: 'sku', name: 'sku'},
                {data: 'to_qty', name: 'to_qty'},
                {data: 'shopee_price', name: 'shopee_price'},
                {data: 'jez_price', name: 'jez_price'},
                {data: 'gap_price', name: 'gap_price'},
                {data: 'total_discount', name: 'total_discount'},
                {data: 'ns_before_admin', name: 'ns_before_admin'},
                {data: 'final_price', name: 'final_price'},
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "0%"
                }],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [[0, 'desc']],
        });
        jQuery.noConflict();

        $(document).delegate('#print_invoice', 'click', function () {
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
                    console.log(numOrder);

                    $.ajax({
                        url: '{{ url('print_online_invoice') }}',
                        method: 'POST',
                        data: {
                            orderNumber: numOrder,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            console.log(response.status);
                            if (response.status == 200) {
                                var printUrl = '{{ url('print_online_nota') }}/' + numOrder;
                                window.open(printUrl, '_blank');
                                online_transaction_table.draw(false);
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Harap melakukan Picker di Data Stok.',
                                    icon: 'error',
                                    confirmButtonColor: '#3085d6'
                                });
                            }

                        },
                        error: function (xhr, status, error) {
                            // Handle errors here
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


        $(document).delegate('#detail_btn', 'click', function () {
            console.log('tes');
            var to_id = $(this).attr('data-to_id');
            var num_order = $(this).attr('data-num_order');
            var status = $(this).attr('data-status');
            $('#to_id').val(to_id);
            $('#num_order').text(num_order);
            $('#status_pesanan').val(status);

            if (status == 'Batal') {
                $('#add_item_detail_btn').hide();
            } else {
                $('#add_item_detail_btn').show();
            }

            $('#DetailModal').on('show.bs.modal', function () {
                detail_table.draw(false);
            }).modal('show');
        });


        $(document).delegate('#add_item_detail_btn', 'click', function () {
            let to_id = $('#to_id').val();
            let no_pesanan = $('#num_order').text();

            console.log(no_pesanan);
            $('#add_item_to_id').val(to_id);
            $('#no_pesanan').val(no_pesanan);
            $('#DetailModal').modal('hide');
            $('#addItemModal').modal('show');
        })

        // $('#sales_online_export').on('click', function () {
        $(document).delegate('#sales_online_export', 'click', function (e) {
            e.preventDefault();
            let date = $('#sales_date').val() //daterange
            let branch_trx = $('#branch_trx').val();
            let status_trx = $('#status_trx').val();
            window.location.href = "{{ url('online_sales_export') }}?branch=" + branch_trx + "&date=" + date + "&status=" + status_trx + "";
        });

        $('#download_template_shopee').on('click', function () {
            console.log('Halo');
        });

        $('#download_template_tiktok').on('click', function () {
            console.log('Halo tiktok');
        });

        var picker = $('#kt_dashboard_daterangepicker');
        if ($('#kt_dashboard_daterangepicker').length == 0) {
            return;
        }
        var start = moment();
        var end = moment();

        function cb(start, end, label) {
            var title = '';
            var range = '';
            var hidden_range = '';
            var st_id = $('#st_id_filter').val();

            if ((end - start) < 100 || label == 'Hari Ini') {
                title = 'Hari Ini:';
                range = start.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD');
            } else if (label == 'Kemarin') {
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
            online_transaction_table.draw();
            // article_report_table.draw();
        }

        picker.daterangepicker({
            direction: KTUtil.isRTL(),
            startDate: start,
            endDate: end,
            opens: 'center',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
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


    });
</script>