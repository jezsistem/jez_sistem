<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_category_table = $('#ProductCategorytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('product_category_datatables')); ?>",
                data : function (d) {
                    d.search = $('#product_category_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'pc_name', name: 'pc_name' },
            { data: 'pc_description', name: 'pc_description' },
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

        product_category_table.buttons().container().appendTo($('#product_category_excel_btn' ));
        $('#product_category_search').on('keyup', function() {
            product_category_table.draw();
        });

        $('#ProductCategorytb tbody').on('click', 'tr', function () {
            var id = product_category_table.row(this).data().id;
            var pc_name = product_category_table.row(this).data().pc_name;
            var pc_description = product_category_table.row(this).data().pc_description;
            jQuery.noConflict();
            $('#ProductCategoryModal').modal('show');
            $('#pc_name').val(pc_name);
            $('#pc_description').val(pc_description);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_product_category_btn').show();
            <?php endif; ?>
        });

        $('#pc_name').on('change', function() {
            var pc_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_pc_name:pc_name},
                dataType: 'json',
                url: "<?php echo e(url('check_exists_product_category')); ?>",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Kategori', 'Kategori sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#pc_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_product_category_btn').on('click', function() {
            jQuery.noConflict();
            $('#ProductCategoryModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_product_category')[0].reset();
            $('#delete_product_category_btn').hide();
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('pc_import')); ?>",
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
                        product_category_table.ajax.reload();
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

        $('#f_product_category').on('submit', function(e) {
            e.preventDefault();
            $("#save_product_category_btn").html('Proses ..');
            $("#save_product_category_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('pc_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_product_category_btn").html('Simpan');
                    $("#save_product_category_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ProductCategoryModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        product_category_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ProductCategoryModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_product_category_btn').on('click', function(){
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
                        url: "<?php echo e(url('pc_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#ProductCategoryModal').modal('hide');
                                product_category_table.ajax.reload();
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
</script><?php /**PATH C:\laragon\www\jez\jez_sistem\resources\views/app/product_category/product_category_js.blade.php ENDPATH**/ ?>