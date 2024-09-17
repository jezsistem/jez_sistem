<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var qty_exception_table = $('#QEtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('qe_datatables')); ?>",
                data : function (d) {
                    d.search = $('#qty_exception_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'p_color', name: 'p_color' },
            { data: 'sz_name', name: 'sz_name' },
            { data: 'qe_qty', name: 'qe_qty' },
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

        qty_exception_table.buttons().container().appendTo($('#qty_exception_excel_btn' ));
        $('#qty_exception_search').on('keyup', function() {
            qty_exception_table.draw();
        });

        $('#QEtb tbody').on('click', 'tr', function () {
            var id = qty_exception_table.row(this).data().id;
            var pst_id = qty_exception_table.row(this).data().pst_id;
            var br_name = qty_exception_table.row(this).data().br_name;
            var p_name = qty_exception_table.row(this).data().p_name;
            var p_color = qty_exception_table.row(this).data().p_color;
            var sz_name = qty_exception_table.row(this).data().sz_name;
            var qe_qty = qty_exception_table.row(this).data().qe_qty;
            jQuery.noConflict();
            $('#QEModal').modal('show');
            $('#product_name_input').val('['+br_name+'] '+p_name+' '+p_color+' '+sz_name);
            $('#qe_qty').val(qe_qty);
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#_pst_id').val(pst_id);
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_qty_exception_btn').show();
            <?php endif; ?>
        });

        $('#add_qty_exception_btn').on('click', function() {
            jQuery.noConflict();
            $('#QEModal').modal('show');
            $('#_id').val('');
            $('#_pst_id').val('');
            $('#_mode').val('add');
            $('#f_qty_exception')[0].reset();
            $('#delete_qty_exception_btn').hide();
        });

        $('#f_qty_exception').on('submit', function(e) {
            e.preventDefault();
            $("#save_qty_exception_btn").html('Proses ..');
            $("#save_qty_exception_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('qe_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_qty_exception_btn").html('Simpan');
                    $("#save_qty_exception_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#QEModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        qty_exception_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#QEModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_qty_exception_btn').on('click', function(){
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
                        url: "<?php echo e(url('qe_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#QEModal').modal('hide');
                                qty_exception_table.draw();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#product_name_input').on('keyup', function(){ 
            var query = $(this).val();
            if($.trim(query) != '' || $.trim(query) != null) {
                jQuery.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url:"<?php echo e(url('autocomplete_article')); ?>",
                    method:"POST",
                    data:{query:query},
                    success:function(data){
                        $('#itemList').fadeIn();
                        $('#itemList').html(data);
                    }
                });
            } else {
                $('#itemList').fadeOut();
            }
        });

        jQuery(document).on('click', '#add_to_item_list', function(e) {
            var pst_id = $(this).attr('data-pst_id');
            var p_name = $(this).attr('data-p_name');
            //alert(p_name);
            $('#_pst_id').val(pst_id);
            $('#product_name_input').val(p_name);
        });

        jQuery(document).on('click', 'body', function(e) {
            jQuery('#itemList').fadeOut();
        });

    });
</script><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/qty_exception/qty_exception_js.blade.php ENDPATH**/ ?>