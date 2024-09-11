<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var web_config_table = $('#Wctb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('perp_datatables')); ?>",
                data : function (d) {
                    d.search = $('#web_config_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'config_name', name: 'config_name' },
            { data: 'config_value', name: 'config_value' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        web_config_table.buttons().container().appendTo($('#web_config_excel_btn' ));
        $('#web_config_search').on('keyup', function() {
            web_config_table.draw();
        });

        $('#Wctb tbody').on('click', 'tr', function () {
            $('#imagePreview').attr('src', '');
            var id = web_config_table.row(this).data().id;
            var config_name = web_config_table.row(this).data().config_name;
            var config_value = web_config_table.row(this).data().config_value;
            jQuery.noConflict();
            $('#WebCategoryModal').modal('show');
            $('#config_name').val(config_name);
            $('#config_value').val(config_value);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_web_config_btn').show();
            <?php endif; ?>
        });

        $('#add_web_config_btn').on('click', function() {
            jQuery.noConflict();
            $('#WebCategoryModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_web_config')[0].reset();
            $('#delete_web_config_btn').hide();
        });

        $('#f_web_config').on('submit', function(e) {
            e.preventDefault();
            $("#save_web_config_btn").html('Proses ..');
            $("#save_web_config_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('perp_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_web_config_btn").html('Simpan');
                    $("#save_web_config_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#WebCategoryModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        web_config_table.draw();
                    } else if (data.status == '400') {
                        $("#WebCategoryModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_web_config_btn').on('click', function(){
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
                        url: "<?php echo e(url('perp_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#WebCategoryModal').modal('hide');
                                web_config_table.draw();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#reset_btn').on('click', function(){
            swal({
                title: "YAKIN RESET ?",
                text: "SELURUH DATA PADA ERP AKAN DIKOSONG KAN KECUALI menu, menu akses, brand, kategori, ecommerce bank, data accounting, metode pembayaran, pengaturan erp dan tipe stok",
                icon: "warning",
                buttons: [
                    'BATAL',
                    'YAKIN'
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
                        data: {},
                        dataType: 'json',
                        url: "<?php echo e(url('reset_erp')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil direset", "success");
                                $('#WebCategoryModal').modal('hide');
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
</script><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/web_config/web_config_js.blade.php ENDPATH**/ ?>