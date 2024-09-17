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
            $("#bn_image").val('');
        });

        var wb_table = $('#Wbtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('wb_datatables')); ?>",
                data : function (d) {
                    d.search = $('#wb_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'bn_image_show', name: 'bn_image' },
            { data: 'bn_name', name: 'bn_name' },
            { data: 'bn_slug', name: 'bn_slug' },
            { data: 'is_child_show', name: 'is_child' },
            { data: 'bn_sort', name: 'bn_sort'},
            { data: 'bn_filter_show', name: 'bn_filter'},
            { data: 'brand', name: 'brand', orderable: false },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var brand_table = $('#Brandtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('bb_brand_datatables')); ?>",
                data : function (d) {
                    d.search = $('#brand_search').val();
                    d.bn_id = $('#bn_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'br_name', name: 'br_name' },
            { data: 'article', name: 'article', orderable: false },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var article_table = $('#Articletb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('bb_article_datatables')); ?>",
                data : function (d) {
                    d.search = $('#article_search').val();
                    d.bnb_id = $('#bnb_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'pssc_name', name: 'pssc_name' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        wb_table.buttons().container().appendTo($('#wb_excel_btn' ));
        $('#wb_search').on('keyup', function() {
            wb_table.draw();
        });

        $('#brand_search').on('keyup', function() {
            brand_table.draw();
        });

        $('#article_search').on('keyup', function() {
            article_table.draw();
        });

        $('#Wbtb tbody').on('click', 'tr td:not(:nth-child(8))', function () {
            $('#imagePreview').attr('src', '');
            var id = wb_table.row(this).data().id;
            var bn_image = wb_table.row(this).data().bn_image;
            var bn_name = wb_table.row(this).data().bn_name;
            var bn_slug = wb_table.row(this).data().bn_slug;
            var bn_sort = wb_table.row(this).data().bn_sort;
            var bn_filter = wb_table.row(this).data().bn_filter;
            var is_child = wb_table.row(this).data().is_child;
            jQuery.noConflict();
            $('#WbModal').modal('show');
            $('#bn_name').val(bn_name);
            $('#bn_slug').val(bn_slug);
            $('#bn_sort').val(bn_sort);
            $('#is_child').val(is_child);
            $('#bn_filter').val(bn_filter);
            if (bn_image != null) {
                $('#imagePreview').attr('src', "<?php echo e(asset('api/banner/1905x914')); ?>/"+bn_image);
            }
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#_image').val(bn_image);
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_wb_btn').show();
            <?php endif; ?>
        });

        $(document).delegate('#bb_btn', 'click', function() {
            jQuery.noConflict();
            var id = $(this).attr('data-id');
            $('#bn_id').val(id);
            $('#BrandModal').modal('show');
            brand_table.draw();
        });

        $(document).delegate('#bnb_btn', 'click', function() {
            jQuery.noConflict();
            var id = $(this).attr('data-id');
            var br_id = $(this).attr('data-br_id');
            $('#bnb_id').val(id);
            $('#ArticleModal').modal('show');
            $.ajax({
                type: "get",
                data: {br_id:br_id},
                dataType: 'html',
                url: "<?php echo e(url('reload_article')); ?>",
                success: function(r) {
                    $('#article_reload').html(r);
                }
            });
            article_table.draw();
            return false;
        });

        $('#add_wb_btn').on('click', function() {
            jQuery.noConflict();
            $('#WbModal').modal('show');
            $('#imagePreview').attr('src', '');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_wb')[0].reset();
            $('#delete_wb_btn').hide();
        });

        $('#f_wb').on('submit', function(e) {
            e.preventDefault();
            $("#save_wb_btn").html('Proses ..');
            $("#save_wb_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('wb_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_wb_btn").html('Simpan');
                    $("#save_wb_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $('#imagePreview').trigger('click');
                        $("#WbModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        wb_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#WbModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_wb_btn').on('click', function(){
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
                        url: "<?php echo e(url('wb_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#WbModal').modal('hide');
                                wb_table.ajax.reload();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });


        $('#f_brand').on('submit', function(e) {
            e.preventDefault();
            $("#save_brand_btn").html('Proses ..');
            $("#save_brand_btn").attr("disabled", true);
            var formData = new FormData(this);
            formData.append('bn_id', $('#bn_id').val());
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('bb_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_brand_btn").html('Simpan');
                    $("#save_brand_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#BrandEditModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        brand_table.draw();
                        wb_table.draw();
                    } else if (data.status == '400') {
                        $("#BrandEditModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_brand_btn').on('click', function(){
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
                        data: {_id:$('#_bb_id').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('bb_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#BrandEditModal').modal('hide');
                                brand_table.draw();
                                wb_table.draw();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#Brandtb tbody').on('click', 'tr td:not(:nth-child(3))', function () {
            var id = brand_table.row(this).data().id;
            var br_id = brand_table.row(this).data().br_id;
            jQuery.noConflict();
            $('#BrandEditModal').modal('show');
            jQuery('#br_id').val(br_id).trigger('change');
            $('#_bb_id').val(id);
            $('#_bb_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_brand_btn').show();
            <?php endif; ?>
        });

        $('#add_brand_btn').on('click', function() {
            jQuery.noConflict();
            $('#BrandEditModal').modal('show');
            $('#_bb_id').val('');
            $('#_bb_mode').val('add');
            $('#f_brand')[0].reset();
            $('#delete_brand_btn').hide();
        });

        // =============

        $('#f_article').on('submit', function(e) {
            e.preventDefault();
            $("#save_article_btn").html('Proses ..');
            $("#save_article_btn").attr("disabled", true);
            var formData = new FormData(this);
            formData.append('bnb_id', $('#bnb_id').val());
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('bbd_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_article_btn").html('Simpan');
                    $("#save_article_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ArticleEditModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        brand_table.draw();
                        article_table.draw();
                        wb_table.draw();
                    } else if (data.status == '400') {
                        $("#ArticleEditModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_article_btn').on('click', function(){
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
                        data: {_id:$('#_bbd_id').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('bbd_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#ArticleEditModal').modal('hide');
                                brand_table.draw();
                                article_table.draw();
                                wb_table.draw();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#Articletb tbody').on('click', 'tr td:not(:nth-child(3))', function () {
            var id = article_table.row(this).data().id;
            var pssc_id = article_table.row(this).data().pssc_id;
            jQuery.noConflict();
            $('#ArticleEditModal').modal('show');
            jQuery('#pssc_id').val(pssc_id).trigger('change');
            $('#_bbd_id').val(id);
            $('#_bbd_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_article_btn').show();
            <?php endif; ?>
        });

        $('#add_article_btn').on('click', function() {
            jQuery.noConflict();
            $('#ArticleEditModal').modal('show');
            $('#_bbd_id').val('');
            $('#_bbd_mode').val('add');
            $('#f_brand')[0].reset();
            $('#delete_article_btn').hide();
        });

    });
</script><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/web_banner/web_banner_js.blade.php ENDPATH**/ ?>