<script>
    $(document).ready(function() {

        var filter_platform = document.getElementById("filter_platform");
        var filter_status = document.getElementById("filter_status");


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_supplier_table = $('#OnlineTransactionTb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [

            ],
            ajax: {
                url : "{{ url('transaksi_online_datatables') }}",
                data : function (d) {
                    d.search = $('#product_supplier_search').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id', searchable: false}, //0
                { data: 'order_number', name: 'order_number' }, //1
                { data: 'order_status', name: 'order_status' }, //2
                { data: 'reason_cancellation', name: 'reason_cancellation' }, //3
                { data: 'resi_number', name: 'resi_number' }, //4
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
                },
                {
                    "targets": 1,
                    "className": "text-center",
                    "width": "100%"
                },
                {
                    "targets": 2,
                    "className": "text-center",
                    "width": "100%"
                },
                {
                    "targets": 3,
                    "className": "text-center",
                    "width": "100rem"
                },
                {
                    "targets": 4,
                    "className": "text-center",
                    "width": "100%"
                },
                {
                    "targets": 5,
                    "className": "text-center",
                    "width": "100%"
                },
                {
                    "targets": 6,
                    "className": "text-center",
                    "width": "100%"
                },
                {
                    "targets": 7,
                    "className": "text-center",
                    "width": "100%"
                }],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [[0, 'desc']],
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
            console.log('Haloo gais');

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
                        // TODO : buat function buat return alert apabila barcode missing swal('Gagal', 'Silahkan periksa format input pada template anda, pastikan kolom biru terisi sesuai dengan sistem', 'warning');
                        // excelImportData = data.data['processedData'];
                        console.log(data.data);
                        console.log(data.name);
                        product_supplier_table.draw();

                        checkMissingBarcode(data.data['missingBarcode']);

                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        swal('File', 'File yang anda import kosong atau format tidak tepat', 'warning');
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
            var id = product_supplier_table.row(this).data().id;
            var ps_name = product_supplier_table.row(this).data().ps_name;

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
        } else if(select.value=='tiktok'){
            document.getElementById('tiktok_div').style.display = "block";

            document.getElementById('shopee_div').style.display = "none";
        } else {
            document.getElementById('tiktok_div').style.display = "none";
            document.getElementById('shopee_div').style.display = "none";
        }
    }

</script>