<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var data_table = $('#Datatb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('il_datatables')); ?>",
                data : function (d) {
                    d.search = $('#data_search').val();
                    d.st_id = $('#st_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'updated_at', name: 'updated_at' },
            { data: 'st_name', name: 'st_name' },
            { data: 'u_name', name: 'u_name' },
            { data: 'article', name: 'article' },
            { data: 'plst_qty', name: 'plst_qty' },
            { data: 'pl_code', name: 'pl_code' },
            { data: 'approve', name: 'approve', orderable:false },
            { data: 'action', name: 'action', orderable:false },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            order: [[0, 'desc']],
        });

        var history_data_table = $('#HistoryDatatb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('il_history_datatables')); ?>",
                data : function (d) {
                    d.search = $('#history_data_search').val();
                    d.st_id = $('#st_id_history').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'updated_at', name: 'updated_at' },
            { data: 'st_name', name: 'st_name' },
            { data: 'u_name', name: 'u_name' },
            { data: 'article', name: 'article' },
            { data: 'plst_qty', name: 'plst_qty' },
            { data: 'pl_code', name: 'pl_code' },
            { data: 'approve', name: 'approve', orderable:false },
            { data: 'approve_date', name: 'approve_date'},
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            order: [[0, 'desc']],
        });

        $('#data_search').on('keyup', function() {
            data_table.draw();
        });

        $('#st_id').on('change', function() {
            data_table.draw();
        });

        $('#history_data_search').on('keyup', function() {
            history_data_table.draw();
        });

        $('#st_id_history').on('change', function() {
            history_data_table.draw();
        });

        $(document).delegate('#decline', 'click', function(e){
            e.preventDefault();
            var plst_id = $(this).attr('data-plst_id');
            var plst_qty = $(this).attr('data-plst_qty');
            var pls_id = $(this).attr('data-pls_id');
            swal({
                title: "Tolak..?",
                text: "Yakin tolak permintaan instock ini?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $(this).addClass('disabled');
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {plst_id:plst_id, type:'decline', plst_qty:plst_qty, pls_id:pls_id},
                        dataType: 'json',
                        url: "<?php echo e(url('il_save')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                $(this).removeClass('disabled');
                                data_table.draw();
                                history_data_table.draw();
                                toast("Berhasil", "Data berhasil ditolak", "success");
                            } else {
                                toast('Gagal', 'Gagal tolak data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#confirm', 'click', function(e){
            e.preventDefault();
            var plst_id = $(this).attr('data-plst_id');
            var plst_qty = $(this).attr('data-plst_qty');
            var pls_id = $(this).attr('data-pls_id');
            $(this).addClass('disabled');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {plst_id:plst_id, type:'confirm', plst_qty:plst_qty, pls_id:pls_id},
                dataType: 'json',
                url: "<?php echo e(url('il_save')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        $(this).removeClass('disabled');
                        data_table.draw();
                        history_data_table.draw();
                        toast("Berhasil", "Data berhasil dikonfirmasi", "success");
                    } else {
                        toast('Gagal', 'Gagal konfirmasi data', 'error');
                    }
                }
            });
            return false;
        });

    });
</script>
<?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/instock_list/instock_list_js.blade.php ENDPATH**/ ?>