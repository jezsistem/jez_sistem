<script>
    function checkHistory(plst_id)
    {
        alert(plst_id);
    }
    $('body').addClass('kt-primary--minimize aside-minimize');

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var cross_order_table = $('#CrossOrdertb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('cross_order_datatables') }}",
                data : function (d) {
                    d.search = $('#invoice_tracking_search').val();
                    d.status = $('#status_filter').val();
                    d.st_id = $('#st_id_filter').val();
                    d.division = $('#std_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pt_id', searchable: false},
            { data: 'pos_invoice', name: 'pos_invoice' },
            { data: 'u_name', name: 'u_name' },
            { data: 'cust_name', name: 'cust_name' },
            { data: 'st_name_end', name: 'st_name_end', orderable: false },
            { data: 'u_name_end', name: 'u_name_end', orderable: false },
            { data: 'pos_created', name: 'pos_created' },
            { data: 'total_item', name: 'total_item' },
            { data: 'total_price', name: 'total_price' },
            { data: 'total_item_reject', name: 'total_item_reject', orderable: false },
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

        var confirmation_table = $('#Confirmationtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('confirmation_datatables') }}",
                data : function (d) {
                    d.pt_id = $('#pt_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'ptd_id', searchable: false},
            { data: 'article', name: 'article', orderable:false },
            { data: 'pos_td_qty', name: 'pos_td_qty', orderable:false },
            { data: 'pl_code', name: 'pl_code', orderable:false },
            { data: 'ready', name: 'ready', orderable:false },
            { data: 'note', name: 'note', orderable:false },
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

        var detail_table = $('#Detailtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('detail_datatables') }}",
                data : function (d) {
                    d.pt_id = $('#pt_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'ptd_id', searchable: false},
            { data: 'article', name: 'article', orderable:false },
            { data: 'pos_td_qty', name: 'pos_td_qty', orderable:false },
            { data: 'pl_code', name: 'pl_code', orderable:false },
            { data: 'ready', name: 'ready', orderable:false },
            { data: 'note', name: 'note', orderable:false },
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
            cross_order_table.draw(false);
        });

        $('#std_id').on('change', function() {
            cross_order_table.draw(false);
        });

        $('#st_id_filter').on('change', function() {
            cross_order_table.draw(false);
        });

        $('#std_id').select2({
            width: "200px",
            dropdownParent: $('#std_id_parent')
        });
        $('#std_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#invoice_tracking_search').on('keyup', function() {
            cross_order_table.draw(false);
        });

        cross_order_table.buttons().container().appendTo($('#stock_tracking_excel_btn' ));
        $('#stock_tracking_search').on('keyup', function() {
            cross_order_table.draw(false);
        });

        $('#CrossOrdertb tbody').on('click', 'tr', function () {
            var id = cross_order_table.row(this).data().plst_id;
            jQuery.noConflict();
            //$('#HistoryModal').modal('show');
        });

        $(document).delegate('#shipping_number_btn', 'click', function() {
            var pt_id = $(this).attr('data-pt_id');
            var cust_id = $(this).attr('data-cust_id');
            $('#_id').val(pt_id);
            $('#_cust_id').val(cust_id);
            $('#ShippingNumberModal').modal('show');
        });

        $(document).delegate('#ready_check', 'click', function() {
            var ptd_id = $(this).attr('data-ptd_id');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_ptd_id:ptd_id},
                dataType: 'json',
                url: "{{ url('sv_cross_status')}}",
                success: function(r) {
                    if (r.status == '200'){
                        toast("Berhasil", "Berhasil mengubah status", "success");
                        confirmation_table.draw(false);
                    } else {
                        toast('Gagal', 'Gagal', 'error');
                    }
                }
            });
            return false;
        });

        $(document).delegate('#note', 'change', function() {
            var ptd_id = $(this).attr('data-ptd_id');
            var note = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_ptd_id:ptd_id, _note:note},
                dataType: 'json',
                url: "{{ url('sv_cross_note')}}",
                success: function(r) {
                    if (r.status == '200'){
                        toast("Berhasil", "Berhasil mengubah status", "success");
                        confirmation_table.draw(false);
                    } else {
                        toast('Gagal', 'Gagal', 'error');
                    }
                }
            });
            return false;
        });

        $(document).delegate('#save_confirmation_btn', 'click', function() {
            var pt_id = $('#pt_id').val();
            swal({
                title: "Konfirmasi..?",
                text: "Yakin data sudah benar ?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {_pt_id:pt_id},
                        dataType: 'json',
                        url: "{{ url('sv_cross_order')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                $('#ConfirmationModal').modal('hide');
                                cross_order_table.draw(false);
                                toast("Berhasil", "Invoice berhasil dikonfirmasi", "success");
                            } else {
                                toast('Gagal', 'Gagal konfirmasi', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#print_btn', 'click', function() {
            var pt_id = $(this).attr('data-pt_id');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_pt_id:pt_id},
                dataType: 'json',
                url: "{{ url('print_cross_invoice')}}",
                success: function(r) {
                    if (r.status == '200'){
                        var win = window.open('{{ url('/') }}/cross_invoice/'+r.invoice, '_blank');
                        if (win) {
                            win.focus();
                        } else {
                            alert('Please allow popups for this website');
                        }
                    } else {
                        swal('Belum Diambil', 'Artikel Belum Diambil, silahkan info kepada gudang untuk cek aplikasi dan ambil sesuai invoice', 'warning');
                    }
                }
            });
            return false;
        });

        $(document).delegate('#confirmation_btn', 'click', function() {
            var pt_id = $(this).attr('data-pt_id');
            $('#pt_id').val(pt_id);
            $('#ConfirmationModal').on('show.bs.modal', function() {
                confirmation_table.draw(false);
            }).modal('show');
        });

        $(document).delegate('#detail_btn', 'click', function() {
            var pt_id = $(this).attr('data-pt_id');
            $('#pt_id').val(pt_id);
            $('#DetailModal').on('show.bs.modal', function() {
                detail_table.draw(false);
            }).modal('show');
        });

        $(document).delegate('#waybill_tracking_btn', 'click', function() {
            var waybill_number = $(this).text();
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
                data: {_waybill_number:waybill_number},
				dataType: 'html',
                success: function(data) {
                    cross_order_table.draw(false);
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
                        $("#ShippingNumberModal").modal('hide');
                        $('#f_shipping_number')[0].reset();
                        cross_order_table.draw(false);
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                    } else if (data.status == '400') {
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_product_category').on('submit', function(e) {
            e.preventDefault();
            $("#save_stock_tracking_btn").html('Proses ..');
            $("#save_stock_tracking_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "{{ url('pc_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_stock_tracking_btn").html('Simpan');
                    $("#save_stock_tracking_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#HistoryModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        cross_order_table.draw(false);
                    } else if (data.status == '400') {
                        $("#HistoryModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });
    });
</script>
