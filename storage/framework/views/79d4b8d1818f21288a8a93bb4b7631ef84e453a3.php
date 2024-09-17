<script>
    var loadFile = function(event) {
        var output = document.getElementById('imagePreview');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
        }
    };

    var loadBanner = function(event) {
        var output = document.getElementById('bannerPreview');
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

        // $('#imagePreview').on('click', function() {
        //     $(this).attr('src', '')
        //     $("#cs_image").val('');
        // });

        // $('#bannerPreview').on('click', function() {
        //     $(this).attr('src', '')
        //     $("#cs_banner").val('');
        // });

        var web_category_table = $('#Wctb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('web_category_datatables')); ?>",
                data : function (d) {
                    d.search = $('#web_category_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'cs_title', name: 'cs_title' },
            { data: 'psc_name', name: 'psc_name' },
            { data: 'cs_slug', name: 'cs_slug' },
            { data: 'cs_image_show', name: 'cs_image' },
            { data: 'cs_banner_show', name: 'cs_banner' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        web_category_table.buttons().container().appendTo($('#web_category_excel_btn' ));
        $('#web_category_search').on('keyup', function() {
            web_category_table.draw();
        });

        $('#Wctb tbody').on('click', 'tr', function () {
            $('#imagePreview').attr('src', '');
            var id = web_category_table.row(this).data().id;
            var cs_image = web_category_table.row(this).data().cs_image;
            var cs_banner = web_category_table.row(this).data().cs_banner;
            var cs_title = web_category_table.row(this).data().cs_title;
            var cs_slug = web_category_table.row(this).data().cs_slug;
            var cs_sub_category = web_category_table.row(this).data().cs_sub_category;
            var psc_id = web_category_table.row(this).data().psc_id;
            jQuery.noConflict();
            $('#WebCategoryModal').modal('show');
            $('#cs_title').val(cs_title);
            $('#cs_slug').val(cs_slug);
            $('#cs_sub_category').val(cs_sub_category);
            $('#psc_id').val(psc_id);
            if (cs_image != null) {
                $('#imagePreview').attr('src', "<?php echo e(asset('api/category_slug/100')); ?>/"+cs_image);
            }
            if (cs_banner != null) {
                $('#bannerPreview').attr('src', "<?php echo e(asset('api/category_slug/banner')); ?>/"+cs_banner);
            }
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#_image').val(cs_image);
            $('#_banner').val(cs_banner);
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_web_category_btn').show();
            <?php endif; ?>
        });

        $('#add_web_category_btn').on('click', function() {
            jQuery.noConflict();
            $('#WebCategoryModal').modal('show');
            $('#imagePreview').attr('src', '');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_web_category')[0].reset();
            $('#delete_web_category_btn').hide();
        });

        $('#f_web_category').on('submit', function(e) {
            e.preventDefault();
            $("#save_web_category_btn").html('Proses ..');
            $("#save_web_category_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('cs_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_web_category_btn").html('Simpan');
                    $("#save_web_category_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $('#imagePreview').trigger('click');
                        $("#WebCategoryModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        web_category_table.draw();
                    } else if (data.status == '400') {
                        $("#WebCategoryModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#bannerPreview').on('click', function() {
            let sid = $('#_id').val();
            let image = $('#_banner').val();

            // console.log(bid);
            // console.log(image);
            swal({
                title: "Hapus Gambar Banner ..?",
                text: "Gambar Banner akan terhapus",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Hapus'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $(this).attr('src', '')
                    $("#psc_banner").val('');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {id:sid, image:image},
                        dataType: 'json',
                        url: "<?php echo e(url('delete_banner_kategori')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                toast("Berhasil", "Banner berhasil dihapus", "success");
                                web_category_table.draw(false);
                            } else {
                                toast('Gagal', 'Banner gagal dihapus', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#imagePreview').on('click', function() {
            let sid = $('#_id').val();
            let image = $('#_banner').val();

            // console.log(bid);
            // console.log(image);
            swal({
                title: "Ingin Hapus Gambar ..?",
                text: "Gambar akan terhapus",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Hapus'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $(this).attr('src', '')
                    $("#psc_banner").val('');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {id:sid, image:image},
                        dataType: 'json',
                        url: "<?php echo e(url('delete_image_kategori')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                toast("Berhasil", "Banner berhasil dihapus", "success");
                                web_category_table.draw(false);
                            } else {
                                toast('Gagal', 'Banner gagal dihapus', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#delete_web_category_btn').on('click', function(){
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
                        url: "<?php echo e(url('cs_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#WebCategoryModal').modal('hide');
                                web_category_table.draw();
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
</script><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/web_category/web_category_js.blade.php ENDPATH**/ ?>