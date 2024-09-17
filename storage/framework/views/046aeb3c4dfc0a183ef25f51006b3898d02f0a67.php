<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var main_color_table = $('#MainColortb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('main_color_datatables')); ?>",
                data : function (d) {
                    d.search = $('#main_color_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'mc_name', name: 'mc_name' },
            { data: 'mc_description', name: 'mc_description' },
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

        main_color_table.buttons().container().appendTo($('#main_color_excel_btn' ));
        $('#main_color_search').on('keyup', function() {
            main_color_table.draw();
        });

        $('#MainColortb tbody').on('click', 'tr', function () {
            var id = main_color_table.row(this).data().id;
            var mc_name = main_color_table.row(this).data().mc_name;
            var mc_description = main_color_table.row(this).data().mc_description;
            jQuery.noConflict();
            $('#MainColorModal').modal('show');
            $('#mc_name').val(mc_name);
            $('#mc_description').val(mc_description);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_main_color_btn').show();
            <?php endif; ?>
        });

        $('#mc_name').on('change', function() {
            var mc_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_mc_name:mc_name},
                dataType: 'json',
                url: "<?php echo e(url('check_exists_main_color')); ?>",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Warna', 'Warna sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#mc_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_main_color_btn').on('click', function() {
            jQuery.noConflict();
            $('#MainColorModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_main_color')[0].reset();
            $('#delete_main_color_btn').hide();
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('mc_import')); ?>",
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
                        main_color_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        swal('Gagal', 'Data gagal diimport', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_main_color').on('submit', function(e) {
            e.preventDefault();
            $("#save_product_category_btn").html('Proses ..');
            $("#save_product_category_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('mc_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_product_category_btn").html('Simpan');
                    $("#save_product_category_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#MainColorModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        main_color_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#MainColorModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_main_color_btn').on('click', function(){
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
                        url: "<?php echo e(url('mc_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#MainColorModal').modal('hide');
                                main_color_table.ajax.reload();
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
</script><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/main_color/main_color_js.blade.php ENDPATH**/ ?>