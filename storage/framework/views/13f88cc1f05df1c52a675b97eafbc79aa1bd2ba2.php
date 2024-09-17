<script src="https://cdn.tiny.cloud/1/323apjbgqf1hr5qmcz0u8uwvl3oymnrypmtg98wfpvhw0khd/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    const apiUrl = '<?php echo e(url('/')); ?>/api/product/';
    var mainImagesPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            var filesAmount = input.files.length;
            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    $($.parseHTML('<img style="width:300px;" class="p_main_image_item">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    };

    var imagesPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            var filesAmount = input.files.length;
            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    $($.parseHTML('<img style="width:300px; padding:3px;" class="p_image_item">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    };

    var sizeChartPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            var filesAmount = input.files.length;
            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    $($.parseHTML('<img style="width:300px;" class="p_size_chart_item">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    };

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#p_main_image').on('change', function() {
            var pid = $('#img_pid').val();
            var image = $(this).val();
            mainImagesPreview(this, '#p_main_image_preview');
        });

        $('#p_main_image_preview').on('click', function() {
            var pid = $('#img_pid').val();
            var image = $('#_main_image').val();
            swal({
                title: "Hapus Gambar Utama ..?",
                text: "Gambar utama akan terhapus",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Hapus'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {pid:pid, image:image},
                        dataType: 'json',
                        url: "<?php echo e(url('delete_main_image')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                toast("Berhasil", "Gambar berhasil dihapus", "success");
                                $(".p_main_image_item").attr('src', '')
                                $("#p_main_image").val('');
                                $('#_main_image').val('');
                                article_table.draw(false);
                            } else {
                                toast('Gagal', 'Gambar gagal dihapus', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        }); 

        $('#p_image').on('change', function() {
            var pid = $('#img_pid').val();
            var image = $(this).val();
            imagesPreview(this, '#p_image_preview');
        });

        $('#p_image_preview').on('click', function() {
            var pid = $('#img_pid').val();
            var image = $('#_detail_image').val();
            swal({
                title: "Hapus Gambar Detail ..?",
                text: "Semua gambar akan terhapus, dan harus upload ulang semua (sementara)",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Hapus'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {pid:pid, image:image},
                        dataType: 'json',
                        url: "<?php echo e(url('delete_image')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                toast("Berhasil", "Gambar berhasil dihapus", "success");
                                $(".p_image_item").attr('src', '')
                                $("#p_image").val('');
                                $('#_detail_image').val('');
                                article_table.draw(false);
                            } else {
                                toast('Gagal', 'Gambar gagal dihapus', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#p_size_chart').on('change', function() {
            var pid = $('#img_pid').val();
            var image = $(this).val();
            sizeChartPreview(this, '#p_size_chart_preview');
        });

        $('#p_size_chart_preview').on('click', function() {
            var pid = $('#img_pid').val();
            var image = $('#_chart_image').val();
            swal({
                title: "Hapus Size Chart ..?",
                text: "Size chart akan terhapus",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Hapus'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {pid:pid, image:image},
                        dataType: 'json',
                        url: "<?php echo e(url('delete_chart_image')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                toast("Berhasil", "Gambar berhasil dihapus", "success");
                                $(".p_size_chart_item").attr('src', '')
                                $("#p_size_chart").val('');
                                $('#_chart_image').val('');
                                article_table.draw(false);
                            } else {
                                toast('Gagal', 'Gambar gagal dihapus', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#f_article').on('submit', function(e) {
            e.preventDefault();
            $("#save_p_image_btn").html('Proses ..');
            $("#save_p_image_btn").attr("disabled", true);
            var formData = new FormData(this);
            let TotalImages = $('#p_image')[0].files.length;
            let images = $('#p_image')[0];
            for (let i = 0; i < TotalImages; i++) {
                formData.append('p_images' + i, images.files[i]);
            }
            formData.append('TotalImages', TotalImages);
            var pid = $('#img_pid').val();
            formData.append('pid', pid);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('web_article_image_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_p_image_btn").html('Simpan');
                    $("#save_p_image_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ImageModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        article_table.draw(false);
                    } else {
                        $("#ImageModal").modal('hide');
                        // swal('Gagal', 'Data tidak tersimpan', 'warning');
                        article_table.draw(false);
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        tinymce.init({
            selector: '#p_description',
            plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            toolbar_mode: 'floating',
            height : '150px'
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
                url : "<?php echo e(url('web_article_datatables')); ?>",
                data : function (d) {
                    d.search = $('#article_search').val();
                    d.pc_id = $('#pc_id').val();
                    d.img_filter = $('#img_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pid', searchable: false},
            { data: 'article_id', name: 'article_id' },
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'p_color', name: 'p_color' },
            { data: 'p_weight_show', name: 'p_weight' },
            { data: 'p_main_image_show', name: 'p_main_image' },
            { data: 'p_size_chart_show', name: 'p_size_chart' },
            { data: 'p_slug_show', name: 'p_slug' },
            { data: 'p_description_show', name: 'p_description' },
            { data: 'stok', name: 'stok' },
            { data: 'action', name: 'action', orderable: false },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        $('#article_search').on('keyup', function() {
            article_table.draw();
        });

        $('#pc_id, #img_filter').on('change', function() {
            article_table.draw();
        });

        $(document).delegate('#p_slug_input', 'change', function() {
            var pid = $(this).attr('data-id');
            var slug = $(this).val().replace(' ', '-');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {pid:pid, slug:slug},
                dataType: 'json',
                url: "<?php echo e(url('generate_slug')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        toast("Berhasil", "Slug berhasil dibuat", "success");
                        $('.p_slug_input_'+pid).val(slug);
                    } else {
                        toast('Gagal', 'Slug gagal dibuat', 'error');
                    }
                }
            });
            return false;
        });

        $(document).delegate('#p_image_edit', 'click', function() {
            $(".p_main_image_item").attr('src', '');
            $(".p_image_item").attr('src', '');
            $(".p_size_chart_item").attr('src', '');
            $("#p_main_image").val('');
            $("#p_image").val('');
            $("#p_size_chart").val('');
            $('#_main_image').val('');
            $('#_detail_image').val('');
            $('#_chart_image').val('');
            var pid = $(this).attr('data-id');
            var name = $(this).attr('data-name');
            var main_image = $(this).attr('data-main_image');
            var chart_image = $(this).attr('data-chart_image');
            var image = $(this).attr('data-image');
            jQuery.noConflict();
            $('#ImageModal').modal('show');
            $('#image_label').text(name);
            $('#img_pid').val(pid);
            if (main_image != '') {
                $('#p_main_image_preview').append('<img src="'+apiUrl+'300/'+main_image+'" style="width:300px;" class="p_main_image_item"/>');
                $('#_main_image').val(main_image);
            }
            if (chart_image != '') {
                $('#p_size_chart_preview').append('<img src="'+apiUrl+'size_chart/'+chart_image+'" style="width:300px;" class="p_size_chart_item"/>');
                $('#_chart_image').val(chart_image);
            }
            if (image != '' && image != '||||||') {
                var arr = image.split('|');
                $.each( arr, function( index, value ) {
                    $('#p_image_preview').append('<img src="'+apiUrl+'300/'+value+'" style="width:300px; padding:3px;" class="p_image_item"/>');
                });
                $('#_detail_image').val(image);
            }
        });

        $(document).delegate('#p_description_edit', 'click', function() {
            var pid = $(this).attr('data-id');
            var name = $(this).attr('data-name');
            var description = $(this).attr('data-description');
            var video = $(this).attr('data-video');
            jQuery.noConflict();
            $('#DescriptionModal').modal('show');
            $('#description_label').text(name);
            tinyMCE.get('p_description').setContent(description);
            $('#p_video').val(video);
            $('#pid').val(pid);
        });

        $(document).delegate('#save_p_description_btn', 'click', function() {
            var pid = $('#pid').val();
            var description = tinyMCE.get('p_description').getContent();
            var video = $('#p_video').val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {pid:pid, description:description, video:video},
                dataType: 'json',
                url: "<?php echo e(url('save_description')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        toast("Berhasil", "Deskripsi berhasil disimpan", "success");
                        jQuery.noConflict();
                        $('#DescriptionModal').modal('hide');
                        article_table.draw(false);
                    } else {
                        toast('Gagal', 'Deskripsi gagal disimpan', 'error');
                    }
                }
            });
            return false;
        });

        $(document).delegate('#generate_slug_btn', 'click', function() {
            var pid = $(this).attr('data-id');
            var slug = $(this).attr('data-slug').replace('/', '-');
            swal({
                title: "Generate Slug ..?",
                text: "URL slug akan berubah menjadi \n"+slug+"",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Lanjut'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {pid:pid, slug:slug},
                        dataType: 'json',
                        url: "<?php echo e(url('generate_slug')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                toast("Berhasil", "Slug berhasil dibuat", "success");
                                $('.p_slug_input_'+pid).val(slug);
                            } else {
                                toast('Gagal', 'Slug gagal dibuat', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#p_weight', 'change', function() {
            var weight = $(this).val();
            var id = $(this).attr('data-id');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {id:id, weight:weight},
                dataType: 'json',
                url: "<?php echo e(url('save_weight')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        toast("Berhasil", "Berat berhasil disimpan", "success");
                    } else {
                        toast('Gagal', 'Slug gagal dibuat', 'error');
                    }
                }
            });
            return false;
        });

    });
</script><?php /**PATH /home/jezpro.id/public_html/resources/views/app/web_article/web_article_js.blade.php ENDPATH**/ ?>