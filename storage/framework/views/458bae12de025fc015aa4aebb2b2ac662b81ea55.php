    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var size_table = $('#Sizetb').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false,
                dom: 'lBrt<"text-right"ip>',
                buttons: [
                    { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
                ],
                ajax: {
                    url : "<?php echo e(url('size_datatables')); ?>",
                    data : function (d) {
                        d.search = $('#size_search').val();
                        d.sz_id = $('#sz_id_filter').val();
                    }
                },
                columns: [
                { data: 'DT_RowIndex', name: 'sid', searchable: false},
                { data: 'sz_schema', name: 'sz_schema' },
                { data: 'sz_name', name: 'sz_name' },
                { data: 'sz_description', name: 'sz_description' },
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

            size_table.buttons().container().appendTo($('#size_excel_btn' ));
            $('#size_search').on('keyup', function() {
                size_table.draw();
            });

            $('#pc_id').on('change', function() {
                var label = $('#pc_id option:selected').text();
                var pc_id = $('#pc_id').val();
                if (label != '- Pilih -') {
                    $('#product_category_selected_label').text(label);
                    $.ajax({
                        type: "GET",
                        data: {_pc_id:pc_id},
                        dataType: 'html',
                        url: "<?php echo e(url('reload_product_sub_category')); ?>",
                        success: function(r) {
                            $('#psc_id').html(r);
                        }
                    });
                } else {
                    $('#product_category_selected_label').text('');
                    $('#psc_id').html("<select class='form-control' id='psc_id' name='psc_id' required><option value=''>- Pilih -</option></select>");
                }
                $('#product_sub_category_selected_label').text('');
                $('#size_display').fadeOut();
            });

            $('#psc_id').on('change', function() {
                var label = $('#psc_id option:selected').text();
                if (label != '- Pilih -') {
                    $('#product_sub_category_selected_label').text(label);
                    $('#size_display').fadeIn();
                    size_table.draw();
                } else {
                    $('#product_sub_category_selected_label').text('');
                    $('#size_display').fadeOut();
                }
            });

            $('#import_modal_btn').on('click', function() {
                var label = $('#pc_id option:selected').text();
                if (label != '- Pilih -') {
                    jQuery.noConflict();
                    $('#ImportModal').modal('show');
                } else {
                    swal('Pilih Kategori', 'Silahkan pilih kategori utama terlebih dahulu', 'warning');
                    return false;
                }
            });

            $('#Sizetb tbody').on('click', 'tr', function () {
                var id = size_table.row(this).data().sid;
                var sz_name = size_table.row(this).data().sz_name;
                var sz_schema = size_table.row(this).data().sz_schema;
                var sz_description = size_table.row(this).data().sz_description;
                jQuery.noConflict();
                $('#SizeModal').modal('show');
                $('#sz_name').val(sz_name);
                $('#sz_schema').val(sz_schema);
                $('#sz_description').val(sz_description);
                $('#_id').val(id);
                $('#_mode').val('edit');
                <?php if( $data['user']->delete_access == '1' ): ?>
                $('#delete_size_btn').show();
                <?php endif; ?>
            });

            // masih belum digunakan
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            

            $('#add_size_btn').on('click', function() {
                jQuery.noConflict();
                $('#SizeModal').modal('show');
                $('#_id').val('');
                $('#_mode').val('add');
                $('#f_size')[0].reset();
                $('#delete_size_btn').hide();
            });

            $('#f_import').on('submit', function(e) {
                e.preventDefault();
                $("#import_data_btn").html('Proses ..');
                $("#import_data_btn").attr("disabled", true);
                var formData = new FormData(this);
                $.ajax({
                    type:'POST',
                    url: "<?php echo e(url('psc_import')); ?>",
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
                            size_table.ajax.reload();
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

            $('#f_size').on('submit', function(e) {
                e.preventDefault();
                $("#save_size_btn").html('Proses ..');
                $("#save_size_btn").attr("disabled", true);
                var formData = new FormData(this);
                // formData.append('psc_id', $('#psc_id').val());
                $.ajax({
                    type:'POST',
                    url: "<?php echo e(url('sz_save')); ?>",
                    data: formData,
                    dataType: 'json',
                    cache:false,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $("#save_size_btn").html('Simpan');
                        $("#save_size_btn").attr("disabled", false);
                        if (data.status == '200') {
                            $("#SizeModal").modal('hide');
                            swal('Berhasil', 'Data berhasil disimpan', 'success');
                            size_table.ajax.reload();
                        } else if (data.status == '400') {
                            $("#SizeModal").modal('hide');
                            swal('Gagal', 'Data tidak tersimpan', 'warning');
                        }
                    },
                    error: function(data){
                        swal('Error', data, 'error');
                    }
                });
            });

            $('#delete_size_btn').on('click', function(){
                var idd = $('#_id').val();
                swal({
                    title: "Hapus..?",
                    text: "Yakin hapus data ini ?"+idd,
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
                            url: "<?php echo e(url('sz_delete')); ?>",
                            success: function(r) {
                                if (r.status == '200'){
                                    swal("Berhasil", "Data berhasil dihapus", "success");
                                    $('#SizeModal').modal('hide');
                                    size_table.ajax.reload();
                                } else {
                                    swal('Gagal', 'Gagal hapus data', 'error');
                                }
                            }
                        });
                        return false;
                    }
                })
            });

            $('#sz_id_filter').select2({
                width: "300px",
                dropdownParent: $('#sz_id_parent')
            });

            $('#sz_id_filter').on('select2:open', function (e) {
                const evt = "scroll.select2";
                $(e.target).parents().off(evt);
                $(window).off(evt);
            });

            $('#sz_id_filter').on('change', function() {
                size_table.draw();
            });
        });
    </script><?php /**PATH /home/jezpro.id/public_html/resources/views/app/size/size_js.blade.php ENDPATH**/ ?>