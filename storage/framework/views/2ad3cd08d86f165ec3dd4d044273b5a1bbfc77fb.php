<script>
    var loadFile = function(event) {
        var output = document.getElementById('imagePreview');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
        }
    };

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#imagePreview').on('click', function() {
            $(this).attr('src', '')
            $("#bank_image").val('');
        });

        var bank_table = $('#Banktb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('bank_datatables')); ?>",
                data : function (d) {
                    d.search = $('#bank_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'bank_image_show', name: 'bank_image' },
            { data: 'bank_name', name: 'bank_name' },
            { data: 'bank_account_name', name: 'bank_account_name' },
            { data: 'bank_number', name: 'bank_number' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        bank_table.buttons().container().appendTo($('#bank_excel_btn' ));
        $('#bank_search').on('keyup', function() {
            bank_table.draw();
        });

        $('#Banktb tbody').on('click', 'tr td:not(:nth-child(2))', function () {
            $('#imagePreview').attr('src', '');
            var id = bank_table.row(this).data().id;
            var bank_image = bank_table.row(this).data().bank_image;
            var bank_name = bank_table.row(this).data().bank_name;
            var bank_account_name = bank_table.row(this).data().bank_account_name;
            var bank_number = bank_table.row(this).data().bank_number;
            jQuery.noConflict();
            $('#BrandModal').modal('show');
            $('#bank_name').val(bank_name);
            $('#bank_account_name').val(bank_account_name);
            $('#bank_number').val(bank_number);
            if (bank_image != null) {
                $('#imagePreview').attr('src', "<?php echo e(asset('api/bank/100')); ?>/"+bank_image);
            }
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#_image').val(bank_image);
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_bank_btn').show();
            <?php endif; ?>
        });

        $('#add_bank_btn').on('click', function() {
            jQuery.noConflict();
            $('#BrandModal').modal('show');
            $('#imagePreview').attr('src', '');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_bank')[0].reset();
            $('#delete_bank_btn').hide();
        });

        $('#bank_name').on('change', function() {
            var bank_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_bank_name:bank_name},
                dataType: 'json',
                url: "<?php echo e(url('check_exists_bank')); ?>",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Brand', 'Nama bank sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#bank_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#f_bank').on('submit', function(e) {
            e.preventDefault();
            $("#save_bank_btn").html('Proses ..');
            $("#save_bank_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('bank_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_bank_btn").html('Simpan');
                    $("#save_bank_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $('#imagePreview').trigger('click');
                        $("#BrandModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        bank_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#BrandModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_bank_btn').on('click', function(){
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
                        url: "<?php echo e(url('bank_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#BrandModal').modal('hide');
                                bank_table.ajax.reload();
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
</script><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/bank/bank_js.blade.php ENDPATH**/ ?>