<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_location_table = $('#ProductLocationtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('product_location_datatables')); ?>",
                data : function (d) {
                    d.search = $('#product_location_search').val();
                    d.st_id = $('#st_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pl_id', searchable: false},
            { data: 'st_name', name: 'st_name' },
            { data: 'pl_code', name: 'pl_code' },
            { data: 'pl_name', name: 'pl_name' },
            { data: 'pl_description', name: 'pl_description' },
            { data: 'pl_default_show', name: 'pl_default' },
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

        product_location_table.buttons().container().appendTo($('#product_location_excel_btn' ));
        $('#product_location_search').on('keyup', function() {
            product_location_table.draw();
        });

        $('#st_id').select2({
            width: "100%",
            dropdownParent: $('#st_id_parent')
        });
        $('#st_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_id').on('change', function() {
            var label = $('#st_id option:selected').text();
            if (label != '- Pilih -') {
                $('#store_selected_label').text(label);
                $('#product_location_display').fadeIn();
                product_location_table.draw();
            } else {
                $('#store_selected_label').text('');
                $('#product_location_display').fadeOut();
            }
        });

        $('#ProductLocationtb tbody').on('click', 'tr', function () {
            var id = product_location_table.row(this).data().pl_id;
            var pl_code = product_location_table.row(this).data().pl_code;
            var pl_name = product_location_table.row(this).data().pl_name;
            var pl_description = product_location_table.row(this).data().pl_description;
            var pl_default = product_location_table.row(this).data().pl_default;
            jQuery.noConflict();
            $('#ProductCategoryModal').modal('show');
            $('#pl_code').val(pl_code);
            $('#pl_name').val(pl_name);
            $('#pl_description').val(pl_description);
            $('#pl_default').val(pl_default);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_product_location_btn').show();
            <?php endif; ?>
        });

        $('#add_product_location_btn').on('click', function() {
            jQuery.noConflict();
            $('#ProductCategoryModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_product_location')[0].reset();
            $('#delete_product_location_btn').hide();
        });

        $('#pl_code').on('change', function() {
            var pl_code = $(this).val();
            var st_id = $('#st_id').val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_pl_code:pl_code, _st_id:st_id},
                dataType: 'json',
                url: "<?php echo e(url('pl_code_check_data')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        swal("Sudah ada", "Kode sudah ada", "warning");
                        $('#pl_code').val('');
                    }
                }
            });
            return false;
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('pl_import')); ?>",
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
                        product_location_table.ajax.reload();
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

        $('#f_product_location').on('submit', function(e) {
            e.preventDefault();
            $("#save_product_location_btn").html('Proses ..');
            $("#save_product_location_btn").attr("disabled", true);
            var formData = new FormData(this);
            formData.append('st_id', $('#st_id').val());
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('pl_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_product_location_btn").html('Simpan');
                    $("#save_product_location_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ProductCategoryModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        product_location_table.ajax.reload();
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

        $('#delete_product_location_btn').on('click', function(){
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
                        url: "<?php echo e(url('pl_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#ProductCategoryModal').modal('hide');
                                product_location_table.ajax.reload();
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
</script><?php /**PATH /home/dev.jez.co.id/public_html/resources/views/app/product_location/product_location_js.blade.php ENDPATH**/ ?>