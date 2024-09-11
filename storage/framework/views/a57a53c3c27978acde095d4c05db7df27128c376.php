<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var menu_access_table = $('#MAtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brtl<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('ma_datatables')); ?>",
                data : function (d) {
                    d.search = $('#menu_access_search').val();
                    d.mt_id = $('#mt_id_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'mt_title', name: 'mt_title' },
            { data: 'ma_title', name: 'ma_title' },
            { data: 'ma_sort', name: 'ma_sort' },
            { data: 'ma_slug', name: 'ma_slug' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        menu_access_table.buttons().container().appendTo($('#menu_access_excel_btn' ));
        $('#menu_access_search').on('keyup', function() {
            menu_access_table.draw();
        });

        $('#mt_id_filter').on('change', function() {
            menu_access_table.draw();
        });

        $('#MAtb tbody').on('click', 'tr', function () {
            var id = menu_access_table.row(this).data().id;
            var mt_id = menu_access_table.row(this).data().mt_id;
            var ma_title = menu_access_table.row(this).data().ma_title;
            var ma_slug = menu_access_table.row(this).data().ma_slug;
            var ma_sort = menu_access_table.row(this).data().ma_sort;
            jQuery.noConflict();
            $('#MMModal').modal('show');
            $('#mt_id').val(mt_id);
            $('#ma_title').val(ma_title);
            $('#ma_slug').val(ma_slug);
            $('#ma_sort').val(ma_sort);
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#delete_menu_access_btn').show();
        });

        $('#add_menu_access_btn').on('click', function() {
            jQuery.noConflict();
            $('#MMModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_menu_access')[0].reset();
            $('#delete_menu_access_btn').hide();
        });

        $('#f_menu_access').on('submit', function(e) {
            e.preventDefault();
            $("#save_menu_access_btn").html('Proses ..');
            $("#save_menu_access_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('ma_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_menu_access_btn").html('Simpan');
                    $("#save_menu_access_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#MMModal").modal('hide');
                        menu_access_table.draw();
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

        $('#delete_menu_access_btn').on('click', function(){
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
                        url: "<?php echo e(url('ma_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#MMModal').modal('hide');
                                menu_access_table.draw();
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
</script>
<?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/menu_access/menu_access_js.blade.php ENDPATH**/ ?>