<script>
    var date = '';
    // $('body').addClass('kt-primary--minimize aside-minimize');

    var loadFile = function(event) {
        var output = document.getElementById('imagePreview');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src)
        }
    };

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#imagePreview').on('click', function() {
            $(this).attr('src', '')
            $("#image").val('');
        });

        var invoice_tracking_table = $('#InvoiceTrackingtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('invoice_tracking_datatables') }}",
                data : function (d) {
                    d.search = $('#invoice_tracking_search').val();
                    d.status = $('#status_filter').val();
                    d.division = $('#std_id').val();
                    d.st_id = $('#st_id_filter').val();
                    d.date = date;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pt_id', searchable: false},
            { data: 'pos_invoice', name: 'pos_invoice' },
            { data: 'u_name', name: 'u_name' },
            { data: 'cust_name', name: 'cust_name' },
            { data: 'dv_name', name: 'dv_name' },
            { data: 'pos_created', name: 'pos_created' },
            { data: 'total_item', name: 'total_item' },
            { data: 'pos_real_price', name: 'pos_real_price' },
            { data: 'pos_shipping_number', name: 'pos_shipping_number' },
            { data: 'pos_shipment', name: 'psi_description' },
            { data: 'pos_status', name: 'pos_status' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [[0, 'desc']],
        });

        $('#status_filter').on('change', function() {
            invoice_tracking_table.draw(false);
        });

        $('#std_id').on('change', function() {
            invoice_tracking_table.draw(false);
        });

        $('#st_id_filter').on('change', function() {
            invoice_tracking_table.draw(false);
        });

        $('#invoice_tracking_search').on('keyup', function() {
            var query = $(this).val();
            if (jQuery.trim(query).length > 3) {
                invoice_tracking_table.draw(false);
            } else if (jQuery.trim(query).length < 1) {
                invoice_tracking_table.draw(false);
            }
        });

        invoice_tracking_table.buttons().container().appendTo($('#stock_tracking_excel_btn' ));
        $('#stock_tracking_search').on('keyup', function() {
            invoice_tracking_table.draw(false);
        });

        $(document).delegate('#shipping_number_btn', 'click', function() {
            var pt_id = $(this).attr('data-pt_id');
            var cust_id = $(this).attr('data-cust_id');
            var image = $(this).attr('data-img');
            $('#imagePreview').attr('src', '');
            $('#_id').val(pt_id);
            $('#_cust_id').val(cust_id);
            $('#ShippingNumberModal').modal('show');
            if (image != '') {
                $('#imagePreview').attr('src', "{{ asset('upload/shipping_img/600x600') }}/"+image);
            }
        });

        $(document).delegate('#dp_payment_btn', 'click', function () {
            var pt_id = $(this).attr('data-pt_id');
            var total_payment_real_price = $(this).attr('data-pos_real_price');
            var pos_payment = $(this).attr('data-pos_payment');
            var difference = total_payment_real_price - pos_payment;
            $('#_pt_id').val(pt_id);
            $('#total_payment_real_price').text(total_payment_real_price);
            $('#difference_payment').text(difference);

            $('#DPPaymentModal').modal('show');
        });

        $('#payment_dp').on('input', function () {
            // Get the values of payment_dp and difference_payment
            var paymentDpValue = parseFloat($(this).val());
            var differencePaymentValue = parseFloat($('#difference_payment').text());

            // Validate the payment_dp value
            if (paymentDpValue > differencePaymentValue || paymentDpValue < 0) {
                // cant be more than difference_payment
                $(this).val(differencePaymentValue);
                swal('Error', 'Pembayaran DP tidak boleh lebih dari sisa pembayaran atau kurang dari 0', 'error');
            }
        });

        $(document).delegate('#waybill_tracking_btn', 'click', function() {
            var waybill_number = $(this).text();
            var pt_id = $(this).attr('data-id');
            $('#waybill_tracking').html('');
            $('#WaybillTrackingModal').modal('show');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "{{ url('waybill_tracking')}}",
                data: {_waybill_number:waybill_number, _id:pt_id},
				dataType: 'html',
                success: function(data) {
                    invoice_tracking_table.draw(false);
                    $('#waybill_tracking').html(data);
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_shipping_number').on('submit', function(e) {
            e.preventDefault();
            $("#save_shipping_number_btn").html('Proses ..');
            $("#save_shipping_number_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "{{ url('shipping_number_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_shipping_number_btn").html('Simpan');
                    $("#save_shipping_number_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $('#imagePreview').trigger('click');
                        $("#ShippingNumberModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        $('#f_shipping_number')[0].reset();
                        invoice_tracking_table.draw(false);
                    } else if (data.status == '400') {
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_payment_dp').on('submit', function(e){
            e.preventDefault();
            $("#save_payment_dp_btn").html('Proses ..');
            $("#save_payment_dp_btn").attr("disabled", true);

            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type:'POST',
                url: "{{ url('invoice_dp_repayment')}}",
                data: formData,
                dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_payment_dp_btn").html('Simpan');
                    $("#save_payment_dp_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#DPPaymentModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        $('#f_payment_dp')[0].reset();
                        invoice_tracking_table.draw(false);
                    } else if (data.status == '400') {
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#check_invoice_btn').on('click', function() {
            jQuery.noConflict();
            $('#CheckInvoiceModal').modal('show');
            $('#complaint_invoice').val('');
            $('#invoice_result').html('');
        });

        $('#search_complaint_btn').on('click', function() {
            var invoice = $('#complaint_invoice').val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "{{ url('search_invoice')}}",
                data: {invoice:invoice},
				dataType: 'json',
                success: function(data) {
                    if (data.status == '200') {
                        swal('Ditemukan', data.invoice, 'success');
                        $('#invoice_result').html('Ditemukan, invoice barunya adalah : '+data.invoice);
                    } else if (data.status == '400') {
                        swal('Tidak Ada', 'Data invoice yang berkaitan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        jQuery.noConflict();

        var picker = $('#kt_dashboard_daterangepicker');
        if ($('#kt_dashboard_daterangepicker').length == 0) {
            return;
        }
        var start = moment();
        var end = moment();

        function cb(start, end, label) {
            var title = '';
            var range = '';

            if ((end - start) < 100 || label == 'Hari Ini') {
                title = 'Hari Ini:';
                range = start.format('DD MMM YYYY');
                date = start.format('YYYY-MM-DD');
            } else if (label == 'Kemarin') {
                title = 'Kemarin:';
                range = start.format('DD MMM YYYY');
                date = start.format('YYYY-MM-DD');
            } else {
                range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
                date = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            invoice_tracking_table.draw(false);
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
