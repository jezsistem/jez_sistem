<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var main_menu_table = $('#MMtb').DataTable({
            destroy: true,
            processing: false,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('mm_datatables')); ?>",
                data : function (d) {
                    d.search = $('#main_menu_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'mt_title', name: 'mt_title' },
            { data: 'mt_sort_show', name: 'mt_sort' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        main_menu_table.buttons().container().appendTo($('#main_menu_excel_btn' ));
        $('#main_menu_search').on('keyup', function() {
            main_menu_table.draw(false);
        });

        $('#MMtb tbody').on('click', 'tr td:not(:nth-child(3))', function () {
            var id = main_menu_table.row(this).data().id;
            var mt_title = main_menu_table.row(this).data().mt_title;
            var mt_sort = main_menu_table.row(this).data().mt_sort;
            jQuery.noConflict();
            $('#MMModal').modal('show');
            $('#mt_title').val(mt_title);
            $('#mt_sort').val(mt_sort);
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#delete_main_menu_btn').show();
        });

        $('#add_main_menu_btn').on('click', function() {
            jQuery.noConflict();
            $('#MMModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_main_menu')[0].reset();
            $('#delete_main_menu_btn').hide();
        });

        $('#f_main_menu').on('submit', function(e) {
            e.preventDefault();
            $("#save_main_menu_btn").html('Proses ..');
            $("#save_main_menu_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('mm_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_main_menu_btn").html('Simpan');
                    $("#save_main_menu_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#MMModal").modal('hide');
                        main_menu_table.draw(false);
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                    } else if (data.status == '400') {
                        $("#MMModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_main_menu_btn').on('click', function(){
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
                        url: "<?php echo e(url('mm_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#MMModal').modal('hide');
                                main_menu_table.draw(false);
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#sort_input', 'change', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var sort = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {id:id, sort:sort},
                dataType: 'json',
                url: "<?php echo e(url('mm_update')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        main_menu_table.draw(false);
                        toast("Berhasil", "Data berhasil diupdate", "success");
                    } else {
                        toast('Gagal', 'Gagal update data', 'error');
                    }
                }
            });
            return false;
        });

    });
</script>
<?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/main_menu/main_menu_js.blade.php ENDPATH**/ ?>