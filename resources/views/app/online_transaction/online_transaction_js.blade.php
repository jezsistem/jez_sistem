<script>
    $(document).ready(function() {

        var filter_platform = document.getElementById("filter_platform");
        var filter_status = document.getElementById("filter_status");

        var value = filter_platform.value;

        console.log(value)


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#filter_platform').select2({
            width: "180px",
            dropdownParent: $('#st_id_filter_parent')
        });

        $('#filter_status').select2({
            width: "180px",
            dropdownParent: $('#st_id_filter_parent')
        });

        $('#f_import').on('submit' , function (e) {
            e.preventDefault();
            $('#import_data_btn').html('Proses...');
            $('#import_data_btn').attr('disabled', true);
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ url('transaksi_online_import')}}",
                data: formData,
                dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    // console.log(data.data['missingBarcode']);
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();
                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');

                        swal('Berhasil', 'Data berhasil diimport', 'success');
                        $('#f_import')[0].reset();
                        excelImportData = data.data['processedData'];
                        console.log(data.data);
                        console.log(data.name);
                        // shopee_tables.draw();
                        // tiktok_tables.draw();
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        console.log(data.data)
                        swal('Error', 'File yang anda import kosong atau format tidak tepat', 'warning');
                    } else {
                        $("#ImportModal").modal('hide');

                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        console.log($('#filter_platform').val());

        $('#ProductSuppliertb tbody').on('click', 'tr', function () {
            var id = shopee_tables.row(this).data().id;
            var ps_name = shopee_tables.row(this).data().ps_name;

            jQuery.noConflict();
            $('#ProductSupplierModal').modal('show');
            $('#ps_name').val(ps_name);
            $('#ps_pkp').val(ps_pkp);
            $('#ps_due_day').val(ps_due_day);
            $('#ps_email').val(ps_email);
            $('#ps_phone').val(ps_phone);
            $('#ps_address').val(ps_address);
            $('#ps_description').val(ps_description);
            $('#ps_npwp').val(ps_npwp);
            $('#ps_rekening').val(ps_rekening);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_product_supplier_btn').show();
            @endif
        });

        $('#download_template_shopee').on('click', function () {
            console.log('Halo');
        });

        $('#download_template_tiktok').on('click', function () {
            console.log('Halo tiktok');
        });

        console.log(filter_platform.value)
        if (filter_platform.value != '' && filter_status.value != '') {
            console.log('Halooo')
        }
    });

    function showDiv(select){
        if(select.value=='shopee'){
            document.getElementById('shopee_div').style.display = "block";
            document.getElementById('tiktok_div').style.display = "none";

            var shopee_tables = $('#OnlineTransactionTb').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false,
                dom: 'lBrt<"text-right"ip>',
                buttons: [

                ],
                ajax: {
                    url : "{{ url('transaksi_online_datatables_shopee') }}",
                    data : function (d) {
                        d.search = $('#product_supplier_search').val();
                        d.type = $('#filter_platform').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'id', searchable: false}, //0
                    { data: 'order_number', name: 'order_number' }, //1
                    { data: 'resi_number', name: 'resi_number' }, //4
                    { data: 'order_status', name: 'order_status' }, //2
                    { data: 'reason_cancellation', name: 'reason_cancellation' }, //3
                    { data: 'shipping_method', name: 'shipping_method' }, //5
                    { data: 'ship_deadline', name: 'ship_deadline' }, //6
                    { data: 'order_date_created', name: 'order_date_created' }, //7
                    { data: 'payment_method', name: 'payment_method' }, //8
                    { data: 'payment_date', name: 'payment_date' }, //9
                    { data: 'SKU', name: 'SKU' }, //10
                    { data: 'original_price', name: 'original_price' }, //10
                    { data: 'price_after_discount', name: 'price_after_discount' }, //10
                    { data: 'quantity', name: 'quantity' }, //10
                    { data: 'return_quantity', name: 'return_quantity' }, //10
                    { data: 'total_price', name: 'total_price' }, //10
                    { data: 'total_discount', name: 'total_discount' }, //10
                    { data: 'shipping_fee', name: 'shipping_fee' }, //10
                    { data: 'voucher_seller', name: 'voucher_seller' }, //10
                    { data: 'cashback_coin', name: 'cashback_coin' }, //10
                    { data: 'voucher', name: 'voucher' }, //10
                    { data: 'voucher_platform', name: 'voucher_platform' }, //10
                    { data: 'discount_seller', name: 'discount_seller' }, //10
                    { data: 'discount_platform', name: 'discount_platform' }, //10
                    { data: 'shopee_coin_pieces', name: 'shopee_coin_pieces' }, //10
                    { data: 'credit_card_discounts', name: 'credit_card_discounts' }, //10
                    { data: 'shipping_costs', name: 'shipping_costs' }, //10
                    { data: 'total_payment', name: 'total_payment' }, //10
                    { data: 'city', name: 'city' }, //10
                    { data: 'province', name: 'province' }, //10
                ],
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-center",
                        "width": "0%"
                    }
                ],
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                language: {
                    "lengthMenu": "_MENU_",
                },
                order: [[0, 'desc']],
            });

            $(document).delegate('#transaksi_detail_btn', 'click', function() {
                var waybill_number = $(this).text();
                var to_id = $(this).attr('data-id');
                $('#waybill_tracking').html('');
                $('#TransaksiModal').modal('show');


                console.log('Halo');

                {{--$.ajaxSetup({--}}
                {{--    headers: {--}}
                {{--        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
                {{--    }--}}
                {{--});--}}
                {{--$.ajax({--}}
                {{--    type:'POST',--}}
                {{--    url: "{{ url('waybill_tracking')}}",--}}
                {{--    data: {_waybill_number:waybill_number, _id:to_id},--}}
                {{--    dataType: 'html',--}}
                {{--    success: function(data) {--}}
                {{--        invoice_tracking_table.draw(false);--}}
                {{--        $('#waybill_tracking').html(data);--}}
                {{--    },--}}
                {{--    error: function(data){--}}
                {{--        swal('Error', data, 'error');--}}
                {{--    }--}}
                {{--});--}}
            });

            $('#Customertb tbody').on('click', 'tr td:not(:nth-child(8))', function () {
                $('#imagePreview').attr('src', '');
                var idTrx = shopee_tables.row(this).data().id;
                var order_number = shopee_tables.row(this).data().order_number;

                console.log(order_number);

                jQuery.noConflict();
                $('#TransaksiModal').modal('show');
                // jQuery('#ct_id').val(ct_id).trigger('change');
                // $('#cust_store').val(cust_store);
                // $('#cust_name').val(cust_name);
                // $('#cust_phone').val(cust_phone);
                // $('#cust_username').val(cust_username);
                // $('#cust_email').val(cust_email);
                // $('#cust_province').val(cust_province);
                // reloadCity(cust_province);
                // setTimeout(() => {
                //     $('#cust_city').val(cust_city);
                // }, 1000);
                // reloadSubdistrict(cust_city);
                // setTimeout(() => {
                //     $('#cust_subdistrict').val(cust_subdistrict);
                // }, 2000);
                // $('#cust_address').val(cust_address);
                // $('#_id').val(id);
                // $('#_mode').val('edit');
                // $('#delete_customer_btn').show();
{{--                @endif--}}
            });

        } else if(select.value=='tiktok'){
            document.getElementById('tiktok_div').style.display = "block";
            document.getElementById('shopee_div').style.display = "none";

            var tiktok_tables = $('#OnlineTransactionTikTokTb').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false,
                dom: 'lBrt<"text-right"ip>',
                buttons: [

                ],
                ajax: {
                    url : "{{ url('transaksi_online_datatables_tiktok') }}",
                    data : function (d) {
                        d.search = $('#product_supplier_search').val();
                        d.type = $('#filter_platform').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'id', searchable: false}, //0
                    { data: 'order_number', name: 'order_number' }, //1
                    { data: 'order_status', name: 'order_status' }, //2
                    { data: 'order_date_created', name: 'order_date_created' }, //2

                ],
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-center",
                        "width": "0%"
                    }
                ],
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                language: {
                    "lengthMenu": "_MENU_",
                },
                order: [[0, 'desc']],
            });
        } else {
            document.getElementById('tiktok_div').style.display = "none";
            document.getElementById('shopee_div').style.display = "none";
        }
    }

</script>