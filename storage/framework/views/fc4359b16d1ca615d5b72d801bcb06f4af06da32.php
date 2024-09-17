<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var free_shipping_table = $('#Fstb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('free_shipping_datatables')); ?>",
                data : function (d) {
                    d.search = $('#free_shipping_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'city_name', name: 'city_name' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        free_shipping_table.buttons().container().appendTo($('#free_shipping_excel_btn' ));
        $('#free_shipping_search').on('keyup', function() {
            free_shipping_table.draw();
        });

        $('#Fstb tbody').on('click', 'tr', function () {
            var id = free_shipping_table.row(this).data().id;
            var city_name = free_shipping_table.row(this).data().city_name;
            var city_id = free_shipping_table.row(this).data().city_id;
            jQuery.noConflict();
            $('#FsModal').modal('show');
            $('#city_id').val(city_id);
            $('#city_name').val(city_name);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_free_shipping_btn').show();
            <?php endif; ?>
        });

        $('#add_free_shipping_btn').on('click', function() {
            jQuery.noConflict();
            $('#FsModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_free_shipping')[0].reset();
            $('#delete_free_shipping_btn').hide();
        });

        $('#f_free_shipping').on('submit', function(e) {
            e.preventDefault();
            $("#save_free_shipping_btn").html('Proses ..');
            $("#save_free_shipping_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('fs_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_free_shipping_btn").html('Simpan');
                    $("#save_free_shipping_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#FsModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        free_shipping_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#FsModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    } else {
                        swal('Sudah Ada', 'Data sudah ada', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_free_shipping_btn').on('click', function(){
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
                        url: "<?php echo e(url('fs_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#FsModal').modal('hide');
                                free_shipping_table.ajax.reload();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#city_name').on('keyup', function(){ 
            var query = $(this).val();
            var key = event.keyCode || event.charCode;
            if( key == 8 || key == 46 ) {
                if (query.length > 5) {
                    $(this).val('');
                    $('#city_id').val('');
                }
            }
            if($.trim(query) != '' || $.trim(query) != null) {
                if ($.trim(query).length > 2) {
                    jQuery.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        url:"<?php echo e(url('autocomplete_city')); ?>",
                        method:"POST",
                        data:{query:query},
                        success:function(data){
                            $('#cityList').fadeIn();
                            $('#cityList').html(data);
                        }
                    });
                } else {
                    $('#cityList').fadeOut();
                }
            } else {
                $('#cityList').fadeOut();
            }
        });

        $(document).delegate('body', 'click', function(e) {
            $('#cityList').fadeOut();
        });

        $(document).delegate('#add_to_city_list', 'click', function(e) {
            var nama = $(this).attr('data-nama');
            var city_id_ro = $(this).attr('data-city_ro');
            $('#city_name').val(nama);
            $('#city_id').val(city_id_ro);
            $('#cityList').fadeOut();
        });

    });
</script><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/free_shipping/free_shipping_js.blade.php ENDPATH**/ ?>