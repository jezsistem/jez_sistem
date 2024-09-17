<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function reloadStoreType()
        {
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "<?php echo e(url('reload_store_type')); ?>",
                success: function(r) {
                    $('#stt_id').html(r);
                }
            });
        }

        function reloadWarehouse()
        {
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "<?php echo e(url('reload_warehouse')); ?>",
                success: function(r) {
                    $('#dv_id').html(r);
                }
            });
        }

        var store_type_table = $('#StoreTypetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('store_type_datatables')); ?>",
                data : function (d) {
                    d.search = $('#store_type_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'stt_name', name: 'stt_name' },
            { data: 'stt_description', name: 'stt_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var store_type_division_table = $('#StoreTypeDivisiontb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('store_type_division_datatables')); ?>",
                data : function (d) {
                    d.search = $('#warehouse_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'dvid', searchable: false},
            { data: 'stt_name', name: 'stt_name' },
            { data: 'dv_name', name: 'dv_name' },
            { data: 'dv_description', name: 'dv_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        store_type_table.buttons().container().appendTo($('#store_type_excel_btn' ));
        $('#store_type_search').on('keyup', function() {
            store_type_table.draw();
        });

        store_type_division_table.buttons().container().appendTo($('#store_type_division_excel_btn' ));
        $('#warehouse_search').on('keyup', function() {
            store_type_division_table.draw();
        });

        $('#StoreTypetb tbody').on('click', 'tr', function () {
            var id = store_type_table.row(this).data().id;
            var stt_name = store_type_table.row(this).data().stt_name;
            var stt_description = store_type_table.row(this).data().stt_description;
            jQuery.noConflict();
            $('#StoreTypeModal').modal('show');
            $('#stt_name').val(stt_name);
            $('#stt_description').val(stt_description);
            $('#_id_stt').val(id);
            $('#_mode_stt').val('edit');
            $('#_old_item').val(stt_name);
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_store_type_btn').show();
            <?php endif; ?>
        });

        $('#StoreTypeDivisiontb tbody').on('click', 'tr', function () {
            var id = store_type_division_table.row(this).data().dvid;
            var stt_id = store_type_division_table.row(this).data().stt_id;
            var dv_name = store_type_division_table.row(this).data().dv_name;
            var dv_description = store_type_division_table.row(this).data().dv_description;
            jQuery.noConflict();
            $('#StoreTypeDivision').modal('show');
            jQuery('#stt_id').val(stt_id).trigger('change');
            $('#dv_name').val(dv_name);
            $('#dv_description').val(dv_description);
            $('#_id_std').val(id);
            $('#_mode_std').val('edit');
            <?php if( $data['user']->g_name == 'administrator' ): ?>
            $('#delete_store_type_division_btn').show();
            <?php endif; ?>
        });

        $('#add_store_type_btn').on('click', function() {
            jQuery.noConflict();
            $('#StoreTypeModal').modal('show');
            $('#_id_stt').val('');
            $('#_mode_stt').val('add');
            $('#f_store_type')[0].reset();
            $('#delete_store_type_btn').hide();
        });

        $('#add_store_type_division_btn').on('click', function() {
            jQuery.noConflict();
            $('#StoreTypeDivision').modal('show');
            jQuery('#stt_id').val('').trigger('change');
            $('#_id_std').val('');
            $('#_mode_std').val('add');
            $('#f_store_type_division')[0].reset();
            $('#delete_store_type_division_btn').hide();
        });

        $('#f_store_type').on('submit', function(e) {
            e.preventDefault();
            $("#save_store_type_btn").html('Proses ..');
            $("#save_store_type_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('stt_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_store_type_btn").html('Simpan');
                    $("#save_store_type_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#StoreTypeModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        store_type_table.ajax.reload();
                        reloadStoreType();
                    } else if (data.status == '400') {
                        $("#StoreTypeModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_store_type_division').on('submit', function(e) {
            e.preventDefault();
            $("#save_store_type_division_btn").html('Proses ..');
            $("#save_store_type_division_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('dv_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_store_type_division_btn").html('Simpan');
                    $("#save_store_type_division_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#StoreTypeDivision").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        store_type_division_table.ajax.reload();
                        reloadWarehouse();
                    } else if (data.status == '400') {
                        $("#StoreTypeDivision").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_store_type_btn').on('click', function(){
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
                        data: {_id:$('#_id_stt').val(),_item:$('#stt_name').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('stt_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#StoreTypeModal').modal('hide');
                                store_type_table.ajax.reload();
                                reloadStoreType();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#delete_store_type_division_btn').on('click', function(){
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
                        data: {_id:$('#_id_std').val(),_item:$('#dv_name').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('dv_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#StoreTypeDivision').modal('hide');
                                store_type_division_table.ajax.reload();
                                reloadWarehouse();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        // ======================= STORE ===================== //
        
        $('#stt_id').select2({
            width: "100%",
            dropdownParent: $('#stt_id_parent')
        });
        $('#stt_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#dv_id').select2({
            width: "100%",
            dropdownParent: $('#dv_id_parent')
        });
        $('#dv_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        var store_table = $('#Storetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('store_datatables')); ?>",
                data : function (d) {
                    d.search = $('#store_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'sid', searchable: false},
            { data: 'stt_name', name: 'stt_name' },
            { data: 'dv_name', name: 'dv_name' },
            { data: 'st_name', name: 'st_name' },
            { data: 'st_email', name: 'st_email' },
            { data: 'st_phone', name: 'st_phone' },
            { data: 'st_address', name: 'st_address' },
            { data: 'st_description', name: 'st_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        store_table.buttons().container().appendTo($('#store_excel_btn' ));
        $('#store_search').on('keyup', function() {
            store_table.draw();
        });

        $('#Storetb tbody').on('click', 'tr', function () {
            var sid = store_table.row(this).data().sid;
            var stt_id = store_table.row(this).data().stt_id;
            var dv_id = store_table.row(this).data().dv_id;
            var st_name = store_table.row(this).data().st_name;
            var st_email = store_table.row(this).data().st_email;
            var st_phone = store_table.row(this).data().st_phone;
            var st_address = store_table.row(this).data().st_address;
            var st_description = store_table.row(this).data().st_description;
            jQuery.noConflict();
            $('#StoreModal').modal('show');
            jQuery('#stt_id').val(stt_id).trigger('change');
            jQuery('#dv_id').val(dv_id).trigger('change');
            $('#st_name').val(st_name);
            $('#st_email').val(st_email);
            $('#st_phone').val(st_phone);
            $('#st_address').val(st_address);
            $('#st_description').val(st_description);
            $('#_id').val(sid);
            $('#_mode').val('edit');

            <?php if( $data['user']->g_name == 'administrator' ): ?>
            $('#delete_store_btn').show();
            <?php endif; ?>
        });

        $('#f_store').on('submit', function(e) {
            e.preventDefault();
            $("#save_store_btn").html('Proses ..');
            $("#save_store_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('st_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_store_btn").html('Simpan');
                    $("#save_store_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#StoreModal").modal('hide');
                        jQuery('#stt_id').val('').trigger('change');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        store_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#StoreModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_store_btn').on('click', function(){
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
                        data: {_id:$('#_id').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('st_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#StoreModal').modal('hide');
                                store_table.ajax.reload();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });
    });
</script><?php /**PATH D:\88 Other\Dev\jez_sistem\resources\views/app/store_type_division/store_type_division_js.blade.php ENDPATH**/ ?>