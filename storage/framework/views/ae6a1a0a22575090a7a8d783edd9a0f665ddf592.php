<script>
    var pt_id = '';

    function checkUnDone() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "<?php echo e(url('ie_permission_check_active_edit')); ?>",
            success: function(r) {
                if (r.status == '200'){
                    $('#pos_invoice').val(r.invoice);
                    setTimeout(() => {
                        $('#exec_btn').trigger('click');
                    }, 300);
                }
            }
        });
        return false;
    }

    $(document).ready(function() {
        // $('body').addClass('kt-primary--minimize aside-minimize');
        checkUnDone();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var data_table = $('#PermissionDatatb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('ie_permission_datatables')); ?>",
                data : function (d) {
                    d.search = $('#data_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'st_name', name: 'st_name' },
            { data: 'stt_name', name: 'stt_name' },
            { data: 'u_name', name: 'u_name' },
            { data: 'action', name: 'action', orderable: false},
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        data_table.buttons().container().appendTo($('#export_btn' ));
        $('#data_search').on('keyup', function() {
            data_table.draw();
        });

        $('#add_btn').on('click', function() {
            jQuery.noConflict();
            $('#DataModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#form')[0].reset();
            $('#delete_btn').hide();
        });

        $('#form').on('submit', function(e) {
            e.preventDefault();
            $("#save_btn").html('Proses ..');
            $("#save_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('ie_permission_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_btn").html('Simpan');
                    $("#save_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#DataModal").modal('hide');
                        data_table.draw();
                        toast('Berhasil', 'Data berhasil disimpan', 'success');
                    } else if (data.status == '400') {
                        $("#DataModal").modal('hide');
                        toast('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    toast('Error', data, 'error');
                }
            });
        });

        $(document).delegate('#delete_btn', 'click', function(e){
            e.preventDefault();
            var id = $(this).attr('data-id');
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini ?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Hapus'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {id:id},
                        dataType: 'json',
                        url: "<?php echo e(url('ie_permission_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                data_table.draw();
                                toast("Berhasil", "Data berhasil dihapus", "success");
                            } else {
                                toast('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        // =======================================================================
        
        var invoice_table = $('#Invoicetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            bPaginate: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('ie_permission_invoice_datatables')); ?>",
                data : function (d) {
                    d.pt_id = pt_id;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'pos_invoice', name: 'pos_invoice' },
            { data: 'cashier', name: 'pos_invoice', orderable:false },
            { data: 'division', name: 'pos_invoice', orderable:false },
            { data: 'subdivision', name: 'pos_invoice', orderable:false },
            { data: 'method', name: 'pm_id', orderable:false },
            { data: 'admin', name: 'pos_admin_cost', orderable:false },
            { data: 'pos_real_price', name: 'pos_real_price'},
            { data: 'pos_status', name: 'pos_status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable:false },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var detail_table = $('#InvoiceDetailtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            bPaginate: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('ie_permission_detail_datatables')); ?>",
                data : function (d) {
                    d.pt_id = pt_id;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'article', name: 'article' },
            { data: 'pos_td_qty', name: 'pos_td_qty' },
            { data: 'price', name: 'price', orderable:false},
            { data: 'nameset', name: 'pos_td_nameset'},
            { data: 'pos_td_total_price', name: 'pos_td_total_price'},
            { data: 'action', name: 'action', orderable:false },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var tracking_table = $('#Trackingtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            bPaginate: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('ie_permission_tracking_datatables')); ?>",
                data : function (d) {
                    d.pt_id = pt_id;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'pl_code', name: 'pl_code' },
            { data: 'article', name: 'article' },
            { data: 'plst_qty', name: 'plst_qty' },
            { data: 'status', name: 'plst_status' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var history_table = $('#Historytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            bPaginate: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('ie_permission_history_datatables')); ?>",
                data : function (d) {
                    d.search = $('#history_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'pos_invoice', name: 'pos_invoice' },
            { data: 'u_name', name: 'u_name' },
            { data: 'activity', name: 'activity' },
            { data: 'note', name: 'note' },
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        $('#history_search').on('keyup', function() {
            history_table.draw();
        });

        function checkInvoice(invoice) {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {pos_invoice:invoice},
                dataType: 'json',
                url: "<?php echo e(url('ie_permission_invoice')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        pt_id = r.id;
                        $('.editor_panel').removeClass('d-none');
                        toast("Berhasil", "Invoice berhasil ditemukan", "success");
                        $('#pos_invoice').prop('readonly', true);
                    } else {
                        pt_id = '';
                        $('.editor_panel').addClass('d-none');
                        toast('Gagal', 'Invoice tidak ditemukan, atau sudah lewat dari 2 hari, hanya administrator yang bisa edit lebih dari 2 hari', 'error');
                    }
                    invoice_table.draw();
                    detail_table.draw();
                    tracking_table.draw();
                }
            });
            return false;
        }

        $(document).delegate('#exec_btn', 'click', function(e) {
            e.preventDefault();
            var inv = $.trim($('#pos_invoice').val());
            checkInvoice(inv);
        });

        $(document).delegate('#done_btn', 'click', function(e) {
            e.preventDefault();
            swal({
                title: "Selesai..?",
                text: "Yakin selesai edit ?",
                icon: "warning",
                buttons: [
                    'Belum',
                    'Yakin'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    var note = $('#note').val();
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {pt_id:pt_id, note:note},
                        dataType: 'json',
                        url: "<?php echo e(url('ie_permission_done_edit')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                $('.editor_panel').addClass('d-none');
                                $('#pos_invoice').val('');
                                $('#note').val('');
                                $('#pos_invoice').prop('readonly', false);
                                pt_id = '';
                                invoice_table.draw();
                                detail_table.draw();
                                tracking_table.draw();
                                history_table.draw();
                            }
                        }
                    });
                    return false;
                }
            })
        });

        function doEdit(type, id, value, pt_id, qty, nameset, price) {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {type:type, id:id, value:value, pt_id:pt_id, qty:qty, nameset:nameset, price:price},
                dataType: 'json',
                url: "<?php echo e(url('ie_permission_do_edit')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        invoice_table.draw();
                        detail_table.draw();
                        tracking_table.draw();
                        history_table.draw();
                        toast('Berhasil', 'Data berhasil diubah', 'success');
                    }
                }
            });
            return false;
        }

        $(document).delegate('#cashier', 'change', function(e) {
            e.preventDefault();
            var type = 'cashier';
            var id = $(this).attr('data-pt_id');
            var value = $(this).val();
            doEdit(type, id, value, '', '', '', '');
        });

        $(document).delegate('#division', 'change', function(e) {
            e.preventDefault();
            var type = 'division';
            var id = $(this).attr('data-pt_id');
            var value = $(this).val();
            doEdit(type, id, value, '', '', '', '');
        });

        $(document).delegate('#subdivision', 'change', function(e) {
            e.preventDefault();
            var type = 'subdivision';
            var id = $(this).attr('data-pt_id');
            var value = $(this).val();
            doEdit(type, id, value, '', '', '', '');
        });

        $(document).delegate('#method', 'change', function(e) {
            e.preventDefault();
            var type = 'method';
            var id = $(this).attr('data-pt_id');
            var value = $(this).val();
            doEdit(type, id, value, '', '', '', '');
        });

        $(document).delegate('#admin', 'change', function(e) {
            e.preventDefault();
            var type = 'admin';
            var id = $(this).attr('data-pt_id');
            var value = $(this).val();
            doEdit(type, id, value, '', '', '', '');
        });

        $(document).delegate('#date', 'change', function(e) {
            e.preventDefault();
            var type = 'date';
            var id = $(this).attr('data-pt_id');
            var value = $(this).val();
            doEdit(type, id, value, '', '', '', '');
        });

        $(document).delegate('#price', 'change', function(e) {
            e.preventDefault();
            var type = 'price';
            var id = $(this).attr('data-ptd_id');
            var pt_id = $(this).attr('data-pt_id');
            var qty = $(this).attr('data-qty');
            var nameset = $(this).attr('data-nameset');
            var value = $(this).val();
            doEdit(type, id, value, pt_id, qty, nameset, '');
        });

        $(document).delegate('#nameset', 'change', function(e) {
            e.preventDefault();
            var type = 'nameset';
            var id = $(this).attr('data-ptd_id');
            var pt_id = $(this).attr('data-pt_id');
            var qty = $(this).attr('data-qty');
            var price = $(this).attr('data-price');
            var value = $(this).val();
            doEdit(type, id, value, pt_id, qty, '', price);
        });

        $(document).delegate('#status', 'change', function(e) {
            e.preventDefault();
            var type = 'status';
            var id = $(this).attr('data-id');
            var value = $(this).val();
            doEdit(type, id, value, '', '', '', '');
        });

        $(document).delegate('#cancel_item_btn', 'click', function(e) {
            e.preventDefault();
            var ptd_id = $(this).attr('data-ptd_id');
            var pt_id = $(this).attr('data-pt_id');
            var pl_id = $(this).attr('data-pl_id');
            var pst_id = $(this).attr('data-pst_id');
            swal({
                title: "Cancel Item..?",
                text: "Jika cancel item akan dihapus dari invoice dan status tracking akan menjadi waiting untuk diinstock..",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Cancel'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {ptd_id:ptd_id, pt_id:pt_id, pl_id:pl_id, pst_id:pst_id},
                        dataType: 'json',
                        url: "<?php echo e(url('ie_permission_cancel_item')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                invoice_table.draw();
                                detail_table.draw();
                                tracking_table.draw();
                                history_table.draw();
                                toast('Berhasil', 'Data berhasil dicancel', 'success');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#cancel_btn', 'click', function(e) {
            e.preventDefault();
            var pt_id = $(this).attr('data-pt_id');
            swal({
                title: "Cancel Invoice..?",
                text: "Invoice dan item akan dihapus dan seluruh item pada tracking akan menjadi waiting untuk diinstock",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Cancel'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {pt_id:pt_id},
                        dataType: 'json',
                        url: "<?php echo e(url('ie_permission_cancel_invoice')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                $('.editor_panel').addClass('d-none');
                                $('#pos_invoice').val('');
                                $('#note').val('');
                                $('#pos_invoice').prop('readonly', false);
                                pt_id = '';
                                invoice_table.draw();
                                detail_table.draw();
                                tracking_table.draw();
                                history_table.draw();
                                toast('Berhasil', 'Data berhasil dicancel', 'success');
                            } else {
                                swal('Invoice Terhubung', 'Invoice ini sudah pernah di refund / exchange, jika ingin membatalkan silahkan batalkan invoice baru dari invoice ini, yaitu '+r.invoice, 'warning');
                            }
                        }
                    });
                    return false;
                }
            })
        });

    });
</script>
<?php /**PATH C:\laragon\www\jez\jez_sistem\resources\views/app/invoice_editor/invoice_editor_js.blade.php ENDPATH**/ ?>