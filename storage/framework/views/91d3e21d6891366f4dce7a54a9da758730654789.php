<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var account_type_table = $('#AccountTypetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('account_type_datatables')); ?>",
                data : function (d) {
                    d.search = $('#account_type_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'at_name', name: 'at_name' },
            { data: 'at_description', name: 'at_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        account_type_table.buttons().container().appendTo($('#account_type_excel_btn' ));
        $('#account_type_search').on('keyup', function() {
            account_type_table.draw();
        });

        $('#AccountTypetb tbody').on('click', 'tr', function () {
            var id = account_type_table.row(this).data().id;
            var at_name = account_type_table.row(this).data().at_name;
            var at_description = account_type_table.row(this).data().at_description;
            jQuery.noConflict();
            $('#AccountTypeModal').modal('show');
            $('#at_name').val(at_name);
            $('#at_description').val(at_description);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_account_type_btn').show();
            <?php endif; ?>
        });

        $('#at_name').on('change', function() {
            var at_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_at_name:at_name},
                dataType: 'json',
                url: "<?php echo e(url('check_exists_account_type')); ?>",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Jenis Akun', 'Jenis akun sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#at_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_account_type_btn').on('click', function() {
            jQuery.noConflict();
            $('#AccountTypeModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_account_type')[0].reset();
            $('#delete_account_type_btn').hide();
        });

        $('#f_account_type').on('submit', function(e) {
            e.preventDefault();
            $("#save_account_type_btn").html('Proses ..');
            $("#save_account_type_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('at_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_account_type_btn").html('Simpan');
                    $("#save_account_type_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#AccountTypeModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        account_type_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#AccountTypeModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_account_type_btn').on('click', function(){
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
                        url: "<?php echo e(url('at_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#AccountTypeModal').modal('hide');
                                account_type_table.ajax.reload();
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
</script><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/account_type/account_type_js.blade.php ENDPATH**/ ?>