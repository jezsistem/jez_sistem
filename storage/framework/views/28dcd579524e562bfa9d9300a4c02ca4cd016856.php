<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var b1g1_location_table = $('#BOGOtb').DataTable({
            destroy: true,
            processing: false,
            serverSide: true,
            responsive: false,
            dom: 'lrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('b1g1_location_datatables')); ?>",
                data : function (d) {
                    d.search = $('#b1g1_location_search').val();
                    d.st_id = $('#st_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'pl_code', name: 'pl_code' },
            { data: 'action', name: 'action', orderable: false},
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

        b1g1_location_table.buttons().container().appendTo($('#b1g1_location_excel_btn' ));
        $('#b1g1_location_search').on('keyup', function() {
            b1g1_location_table.draw();
        });

        $('#st_id').on('change', function() {
            b1g1_location_table.draw();
        });

        $('#pl_id').select2({
            width: "100%",
            dropdownParent: $('#pl_id_parent')
        });
        $('#pl_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#f_b1g1_location').on('submit', function(e) {
            e.preventDefault();
            $("#save_b1g1_location_btn").html('Proses ..');
            $("#save_b1g1_location_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('b1g1_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_b1g1_location_btn").html('Simpan');
                    $("#save_b1g1_location_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#B1g1Modal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        b1g1_location_table.draw();
                    } else if (data.status == '400') {
                        $("#B1g1Modal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_b1g1_location_btn').on('click', function(){
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
                        url: "<?php echo e(url('b1g1_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#B1g1Modal').modal('hide');
                                b1g1_location_table.draw();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#unchecked', 'click', function(e) {
            e.preventDefault();
            var pl_id = $(this).attr('data-pl_id');
            var type = 'unchecked';
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {pl_id:pl_id, type:type},
                dataType: 'json',
                url: "<?php echo e(url('b1g1_update')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        b1g1_location_table.draw(false);
                        toast("Berhasil", "Data berhasil diupdate", "success");
                    } else {
                        toast('Gagal', 'Gagal update data', 'error');
                    }
                }
            });
            return false;
        });

        $(document).delegate('#checked', 'click', function(e) {
            e.preventDefault();
            var pl_id = $(this).attr('data-pl_id');
            var type = 'checked';
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {pl_id:pl_id, type:type},
                dataType: 'json',
                url: "<?php echo e(url('b1g1_update')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        b1g1_location_table.draw(false);
                        toast("Berhasil", "Data berhasil diupdate", "success");
                    } else {
                        toast('Gagal', 'Gagal update data', 'error');
                    }
                }
            });
            return false;
        });

    });
</script><?php /**PATH C:\laragon\www\jez\jez_sistem\resources\views/app/b1g1_location/b1g1_location_js.blade.php ENDPATH**/ ?>