<script>
    $(document).ready(function() {

        $('#OnlineTransactionb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('transaksi_online_datatables') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'to_id', searchable: false},
                { data: 'order_number', name: 'to_order_number' },
                { data: 'no_resi', name: 'no_resi' },
                { data: 'platform_name', name: 'platform_name' },
                { data: 'order_date_created', name: 'order_date_created' },
                { data: 'total_item', name: 'total_item' },
                { data: 'total_payment', name: 'total_payment' },
                { data: 'order_status', name: 'order_status'},
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
        })


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
</script>