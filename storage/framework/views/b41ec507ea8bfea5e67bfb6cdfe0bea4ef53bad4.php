<script>
    var sa_id = '';

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var store_aging_table = $('#Datatb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('sta_datatables')); ?>",
                data : function (d) {
                    d.search = $('#data_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'sa_name', name: 'sa_name' },
            { data: 'sa_age', name: 'sa_age' },
            { data: 'store', name: 'store' },
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

        store_aging_table.buttons().container().appendTo($('#store_aging_excel_btn' ));
        $('#data_search').on('keyup', function() {
            store_aging_table.draw();
        });

        var store_aging_detail_table = $('#DataDetailtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('sta_detail_datatables')); ?>",
                data : function (d) {
                    // d.search = $('#data_search').val();
                    d.sa_id = sa_id;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'st_name', name: 'st_name' },
            { data: 'show', name: 'show' },
            { data: 'action', name: 'st_name' },
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

        var oca_table = $('#OCAtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('oca_datatables')); ?>",
                data : function (d) {

                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'st_name', name: 'st_name' },
            { data: 'oca_age', name: 'oca_age' },
            { data: 'action', name: 'st_name' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        $('#Datatb tbody').on('click', 'tr td:not(:nth-child(4))', function () {
            var id = store_aging_table.row(this).data().id;
            var sa_name = store_aging_table.row(this).data().sa_name;
            var sa_age = store_aging_table.row(this).data().sa_age;
            jQuery.noConflict();
            $('#DataModal').modal('show');
            $('#sa_name').val(sa_name);
            $('#sa_age').val(sa_age);
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#delete_btn').show();
        });

        $('#add_btn').on('click', function() {
            jQuery.noConflict();
            $('#DataModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_data')[0].reset();
            $('#delete_btn').hide();
        });

        $('#f_data').on('submit', function(e) {
            e.preventDefault();
            $("#save_btn").html('Proses ..');
            $("#save_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('sta_save')); ?>",
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
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        store_aging_table.draw();
                    } else if (data.status == '400') {
                        $("#DataModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_btn').on('click', function(){
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
                        data: {id:$('#_id').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('sta_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#DataModal').modal('hide');
                                store_aging_table.draw();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#store_detail_btn', 'click', function(e) {
            e.preventDefault();
            sa_id = $(this).attr('data-id');

            jQuery.noConflict();
            $('#DataDetailModal').modal('show');
            store_aging_detail_table.draw();
        });

        $('#f_store').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            formData.append('sa_id', sa_id);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('stas_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    jQuery.noConflict();
                    $("#DataStoreModal").modal('hide');
                    if (data.status == '200') {
                        $('#f_store')[0].reset();
                        store_aging_table.draw();
                        store_aging_detail_table.draw();
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

        $(document).delegate('#delete_detail_btn', 'click', function(e){
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {id:$(this).attr('data-id')},
                dataType: 'json',
                url: "<?php echo e(url('stas_delete')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        store_aging_table.draw();
                        store_aging_detail_table.draw();
                        swal("Berhasil", "Data berhasil dihapus", "success");
                    } else {
                        swal('Gagal', 'Gagal hapus data', 'error');
                    }
                }
            });
            return false;
        });

        $(document).delegate('#y_check', 'click', function(e){
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {id:$(this).attr('data-id'), type:1},
                dataType: 'json',
                url: "<?php echo e(url('sta_checked')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        store_aging_detail_table.draw();
                        toast("Berhasil", "Data berhasil diupdate", "success");
                    } else {
                        toast('Gagal', 'Gagal update data', 'error');
                    }
                }
            });
            return false;
        });

        $(document).delegate('#n_check', 'click', function(e){
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {id:$(this).attr('data-id'), type:0},
                dataType: 'json',
                url: "<?php echo e(url('sta_checked')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        store_aging_detail_table.draw();
                        toast("Berhasil", "Data berhasil diupdate", "success");
                    } else {
                        toast('Gagal', 'Gagal update data', 'error');
                    }
                }
            });
            return false;
        });

        $('#f_oca').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('oca_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    jQuery.noConflict();
                    $("#OCAModal").modal('hide');
                    $('#f_oca')[0].reset();
                    if (data.status == '200') {
                        oca_table.draw();
                        toast('Berhasil', 'Data berhasil disimpan', 'success');
                    } else {
                        toast('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $(document).delegate('#delete_oca_btn', 'click', function(e){
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {id:$(this).attr('data-id')},
                dataType: 'json',
                url: "<?php echo e(url('oca_delete')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        oca_table.draw();
                        toast("Berhasil", "Data berhasil dihapus", "success");
                    } else {
                        toast('Gagal', 'Gagal hapus data', 'error');
                    }
                }
            });
            return false;
        });

    });
</script>
<?php /**PATH /home/jezpro.id/public_html/resources/views/app/store_aging/store_aging_js.blade.php ENDPATH**/ ?>