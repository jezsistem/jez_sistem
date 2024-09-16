<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var stock_type_table = $('#StockTypetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('stock_type_datatables')); ?>",
                data : function (d) {
                    d.search = $('#stock_type_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'stktid', searchable: false},
            { data: 'stkt_name', name: 'stkt_name' },
            { data: 'a_name', name: 'a_name' },
            { data: 'stkt_description', name: 'stkt_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        stock_type_table.buttons().container().appendTo($('#stock_type_excel_btn' ));
        $('#stock_type_search').on('keyup', function() {
            stock_type_table.draw();
        });

        $('#StockTypetb tbody').on('click', 'tr', function () {
            $('#imagePreview').attr('src', '');
            var id = stock_type_table.row(this).data().stktid;
            var a_id = stock_type_table.row(this).data().a_id;
            var stkt_name = stock_type_table.row(this).data().stkt_name;
            var stkt_description = stock_type_table.row(this).data().stkt_description;
            jQuery.noConflict();
            $('#StockTypeModal').modal('show');
            $('#stkt_name').val(stkt_name);
            $('#stkt_description').val(stkt_description);
            $('#a_id').val(a_id).trigger('change');
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_stock_type_btn').show();
            <?php endif; ?>
        });

        $('#add_stock_type_btn').on('click', function() {
            jQuery.noConflict();
            $('#StockTypeModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_stock_type')[0].reset();
            $('#delete_stock_type_btn').hide();
        });

        $('#stkt_name').on('change', function() {
            var stkt_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_stkt_phone:stkt_phone},
                dataType: 'json',
                url: "<?php echo e(url('check_exists_customer')); ?>",
                success: function(r) {
                    if (r.status == '200') {
                        swal('No Telepon', 'Nomor telepon sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#stkt_phone').val('');
                        return false;
                    }
                }
            });
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('stkt_import')); ?>",
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
                        stock_type_table.ajax.reload();
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

        $('#f_stock_type').on('submit', function(e) {
            e.preventDefault();
            $("#save_stock_type_btn").html('Proses ..');
            $("#save_stock_type_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('stkt_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_stock_type_btn").html('Simpan');
                    $("#save_stock_type_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#StockTypeModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        stock_type_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#StockTypeModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_stock_type_btn').on('click', function(){
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
                        url: "<?php echo e(url('stkt_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#StockTypeModal').modal('hide');
                                stock_type_table.ajax.reload();
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
</script><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/stock_type/stock_type_js.blade.php ENDPATH**/ ?>