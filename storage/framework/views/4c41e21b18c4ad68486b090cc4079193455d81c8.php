<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var gender_table = $('#Gendertb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('gender_datatables')); ?>",
                data : function (d) {
                    d.search = $('#gender_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'gn_name', name: 'gn_name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' },
            ],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        gender_table.buttons().container().appendTo($('#gender_excel_btn' ));
        $('#gender_search').on('keyup', function() {
            gender_table.draw();
        });

        $('#Gendertb tbody').on('click', 'tr', function () {
            var id = gender_table.row(this).data().id;
            var gn_name = gender_table.row(this).data().gn_name;
            jQuery.noConflict();
            $('#GenderModal').modal('show');
            $('#gn_name').val(gn_name);
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#_old_item').val(gn_name);
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_gender_btn').show();
            <?php endif; ?>
        });

        $('#gn_name').on('change', function() {
            var gn_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_gn_name:gn_name},
                dataType: 'json',
                url: "<?php echo e(url('check_exists_gender')); ?>",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Gender', 'Nama gender sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#gn_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_gender_btn').on('click', function() {
            jQuery.noConflict();
            $('#GenderModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_gender')[0].reset();
            $('#delete_gender_btn').hide();
        });

        $('#f_gender').on('submit', function(e) {
            e.preventDefault();
            $("#save_gender_btn").html('Proses ..');
            $("#save_gender_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('gn_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_gender_btn").html('Simpan');
                    $("#save_gender_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#GenderModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        gender_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#GenderModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_gender_btn').on('click', function(){
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
                        data: {_id:$('#_id').val(),_item:$('#gn_name').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('gn_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#GenderModal').modal('hide');
                                gender_table.ajax.reload();
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
</script><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/gender/gender_js.blade.php ENDPATH**/ ?>