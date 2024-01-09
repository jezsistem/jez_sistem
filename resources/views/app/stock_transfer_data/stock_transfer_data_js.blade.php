<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var stock_transfer_data_table = $('#StockTransferDatatb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"p>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('transfer_data_datatables') }}",
                data : function (d) {
                    d.search = $('#stock_transfer_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'stf_id', searchable: false},
            { data: 'stf_code_show', name: 'stf_code' },
            { data: 'u_name', name: 'u_name', orderable: false },
            { data: 'qty', name: 'qty', orderable: false },
            { data: 'start_store', name: 'start_store', orderable: false },
            { data: 'end_store', name: 'end_store', orderable: false },
            { data: 'u_name_receive', name: 'u_name_receive', orderable: false },
            { data: 'stf_created', name: 'stf_created' },
            { data: 'stf_status', name: 'stf_status' },
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

        var stock_transfer_data_accept_table = $('#StockTransferDataAccepttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-left"l>rt<"text-right"p>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('transfer_data_accept_datatables') }}",
                data : function (d) {
                    d.stf_code = $('#stf_code_label').val();
                    d.search = $('#stock_transfer_receive_search').val();

                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'stfd_id', searchable: false},
            { data: 'br_name', name: 'br_name' },
            { data: 'article', name: 'p_name' },
            { data: 'stfd_qty', name: 'stfd_qty' },
            { data: 'accept_total', name: 'accept_total', orderable: false },
            { data: 'accept', name: 'accept', orderable: false },
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

        var history_table = $('#Historytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-left"l>Brt<"text-right"p>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('transfer_data_history_datatables') }}",
                data : function (d) {
                    d.search = $('#history_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'stf_code', name: 'stf_code' },
            { data: 'st_start', name: 'st_start', orderable: false },
            { data: 'st_name', name: 'st_name' },
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'p_color', name: 'p_color' },
            { data: 'sz_name', name: 'sz_name' },
            { data: 'stfds_qty', name: 'stfds_qty'},
            { data: 'hb', name: 'hb', orderable: false},
            { data: 'hj', name: 'hj', orderable: false},
            { data: 'u_name', name: 'u_name'},
            { data: 'created_at', name: 'created_at'},
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

        $('#stock_transfer_search').keyup(function() {
            stock_transfer_data_table.draw();
        });

        $('#stock_transfer_receive_search').keyup(function() {
            stock_transfer_data_accept_table.draw();
        });

        $('#history_search').keyup(function() {
            history_table.draw();
        });

        $(document).delegate('#accept_qty', 'change', function() {
            var transfer_qty = $(this).attr('data-stfd_qty');
            var accept_qty = $(this).val();
            if (parseInt(accept_qty) > parseInt(transfer_qty)) {
                $(this).val('');
                swal('Jumlah', 'Jumlah yang diterima tidak boleh melebihi jumlah yang ditransfer', 'error');
                return false;
            }
            if (parseInt(accept_qty) < 0) {
                $(this).val('');
                swal('Minus', 'Jumlah yang diterima tidak boleh kurang dari 0', 'error');
                return false;
            }
        });

        $('#accept_qty_btn').on('click', function() {
            swal({
                title: "Terima..?",
                text: "Yakin jumlah sudah sesuai untuk diterima ?",
                icon: "info",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $(this).addClass('disabled');
                    var arr = [];
                    var i = 0;
                    var stf_id = $('#stf_id').val();
                    var st_id_end = $('#st_id_end').val();
                    $('.accept_qty').each(function() {
                        var stfd_id = $(this).attr('data-stfd_id');
                        var pst_id = $(this).attr('data-pst_id');
                        var accept_qty = $(this).val();
                        if (accept_qty == '') {
                            return true;
                        }
                        arr[i++] = [stfd_id, pst_id, accept_qty];
                    });
                    if (arr.length <= 0) {
                        swal('Qty kosong', 'silahkan tentukan qty yang diterima', 'warning');
                        return false;
                    }
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({    
                        type: "POST",
                        data: {_stf_id:stf_id, _st_id_end:st_id_end, _arr:arr},
                        dataType: 'json',
                        url: "{{ url('stock_transfer_accept')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                stock_transfer_data_table.draw();
                                stock_transfer_data_accept_table.draw();
                                $(this).removeClass('disabled');
                                swal("Berhasil", "Data berhasil diterima", "success");
                            } else {
                                $(this).removeClass('disabled');
                                swal('Gagal', 'Gagal terima data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#StockTransferDatatb tbody').on('click', 'tr', function () {
            var stf_id = stock_transfer_data_table.row(this).data().stf_id;
            var stf_code = stock_transfer_data_table.row(this).data().stf_code;
            var st_id_end = stock_transfer_data_table.row(this).data().st_id_end;
            $('#stf_id').val(stf_id);
            $('#stf_code_label').val(stf_code);
            $('#st_id_end').val(st_id_end);
            jQuery.noConflict();
            $('#StockTransferDataModal').on('show.bs.modal', function() {
                stock_transfer_data_accept_table.draw();
            }).modal('show');
        });
        
        $(document).delegate('#std_export_all_btn', 'click', function(e) {
            e.preventDefault();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            if (start_date == '') {
                swal('Tanggal Awal', 'Tentukan', 'warning');
                return false;
            }
            if (end_date == '') {
                swal('Tanggal Akhir', 'Tentukan', 'warning');
                return false;
            }
            window.location.href = "{{ url('std_export') }}?start="+start_date+"&end="+end_date+"";
        });

        $(document).ready(function () {
            // Open the second modal when the button is clicked
            $("#ImportModalBtn").click(function () {
                $("#ImportModal").modal("show");
            });
        });

        $('#f_import').on('submit' , function (e) {
            e.preventDefault();
            $('#import_data_btn').html('Proses...');
            $('#import_data_btn').attr('disabled', true);
            var formData = new FormData(this);
            var stf_id = $('#stf_id').val();

            $.ajax({
                type: 'POST',
                url: "{{ url('stock_transfer_import')}}",
                data: formData,
                dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {

                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();
                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');

                        swal('Berhasil', 'Data berhasil diimport', 'success');
                        $('#f_import')[0].reset();
                        // stock_transfer_data_accept_table.ajax.reload( function (json) {
                        //     var stf_code = json.stf_code;
                        // });
                        stock_transfer_data_accept_table.rows().every(function(){
                            var rowData = this.data();
                            var stfd_id = rowData.stfd_id;
                            var pst_id = rowData.pst_id;

                            // Check if the current row's pst_id matches the product_stock_id from data.data
                            var matchingData = data.data.find(function(item) {
                                return item.product_stock_id === pst_id;
                            });

                            if (matchingData) {
                                var acceptQtyInput = $('input.accept_qty[data-stfd_id="' + stfd_id + '"]');
                                acceptQtyInput.val(matchingData.qty);
                            }
                        });
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        $('#f_import')[0].reset();
                        swal('File', 'File yang anda import kosong atau format tidak tepat', 'warning');
                    } else {
                        $("#ImportModal").modal('hide');
                        $('#f_import')[0].reset();
                        swal('Gagal', 'Silahkan periksa format input pada template anda, pastikan kolom biru terisi sesuai dengan sistem', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });
    });
</script>