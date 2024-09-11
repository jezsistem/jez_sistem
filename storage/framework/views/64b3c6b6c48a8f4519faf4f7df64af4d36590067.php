<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var courier_table = $('#Couriertb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('courier_datatables')); ?>",
                data : function (d) {
                    d.search = $('#courier_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'cr_name', name: 'cr_name' },
            { data: 'cr_description', name: 'cr_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        courier_table.buttons().container().appendTo($('#courier_excel_btn' ));
        $('#courier_search').on('keyup', function() {
            courier_table.draw();
        });

        $('#Couriertb tbody').on('click', 'tr td:not(:nth-child(2))', function () {
            $('#imagePreview').attr('src', '');
            var id = courier_table.row(this).data().id;
            var cr_name = courier_table.row(this).data().cr_name;
            var cr_description = courier_table.row(this).data().cr_description;
            jQuery.noConflict();
            $('#CourierModal').modal('show');
            $('#cr_name').val(cr_name);
            $('#cr_description').val(cr_description);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
                $('#delete_courier_btn').show();
            <?php endif; ?>
        });

        $('#add_courier_btn').on('click', function() {
            jQuery.noConflict();
            $('#CourierModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_courier')[0].reset();
            $('#delete_courier_btn').hide();
        });

        $('#f_courier').on('submit', function(e) {
            e.preventDefault();
            $("#save_courier_btn").html('Proses ..');
            $("#save_courier_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('cr_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_courier_btn").html('Simpan');
                    $("#save_courier_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#CourierModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        courier_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#CourierModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_courier_btn').on('click', function(){
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
                        url: "<?php echo e(url('cr_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#CourierModal').modal('hide');
                                courier_table.ajax.reload();
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
</script><?php /**PATH /home/jezpro.id/public_html/resources/views/app/courier/courier_js.blade.php ENDPATH**/ ?>