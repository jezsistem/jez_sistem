<script src="https://cdn.tiny.cloud/1/323apjbgqf1hr5qmcz0u8uwvl3oymnrypmtg98wfpvhw0khd/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    $(document).ready(function() {
        tinymce.init({
            selector: '#bcc_content',
            plugins: 'advlist autolink lists link image media charmap print preview hr anchor pagebreak',
            toolbar_mode: 'floating',
        });

        document.addEventListener('focusin', function(e) {
            if (e.target.closest(".mce-window") !== null) {
                e.stopImmediatePropagation();
            }
        });

        var mainImagesPreview = function(input, placeToInsertImagePreview) {
            if (input.files) {
                var filesAmount = input.files.length;
                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        $($.parseHTML('<img style="width:300px;" class="bcc_image_item">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                    }
                    reader.readAsDataURL(input.files[i]);
                }
            }
        };

        $('#bcc_image').on('change', function() {
            var pid = $('#img_pid').val();
            var image = $(this).val();
            mainImagesPreview(this, '#bcc_image_preview');
        });

        $('#bcc_image_preview').on('click', function() {
            $(".bcc_image_item").attr('src', '')
            $("#bcc_image").val('');
            $('#_bcc_image').val('');
        }); 

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var bcc_table = $('#Bcctb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('bcc_datatables') }}",
                data : function (d) {
                    d.bc_id = $('#bc_id_filter').val();
                    d.search = $('#bcc_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'bct_title', name: 'bct_title' },
            { data: 'bct_image_show', name: 'bct_image' },
            { data: 'bct_views', name: 'bct_views' },
            { data: 'action', name: 'action' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        bcc_table.buttons().container().appendTo($('#bcc_excel_btn' ));
        $('#bcc_search').on('keyup', function() {
            bcc_table.draw();
        });

        $(document).delegate('#bc_id_filter', 'change', function() {
            bcc_table.draw();
        });

        $('#Bcctb tbody').on('click', 'tr td:not(:nth-child(5))', function () {
            var id = bcc_table.row(this).data().id;
            var bc_id = bcc_table.row(this).data().bc_id;
            var bcc_title = bcc_table.row(this).data().bct_title;
            var bcc_content = bcc_table.row(this).data().bct_content;
            var bcc_image = bcc_table.row(this).data().bct_image;
            var bcc_keywords = bcc_table.row(this).data().bct_keywords;
            jQuery.noConflict();
            $('#BccModal').modal('show');
            $('#bcc_title').val(bcc_title);
            $('#bc_id').val(bc_id);
            if (bcc_image != '') {
                $('#bcc_image_preview').html('<img src="{{ url('/') }}/api/blog/300/'+bcc_image+'" style="width:300px;" class="bcc_image_item"/>');
                $('#_bcc_image').val(bcc_image);
            }
            tinyMCE.get('bcc_content').setContent(bcc_content);
            $('#bcc_keywords').val(bcc_keywords);
            $('#_bcc_id').val(id);
            $('#_bcc_mode').val('edit');
            $('#delete_bcc_btn').show();
        });

        $('#bcc_title').on('change', function() {
            var bcc_title = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_bcc_title:bcc_title},
                dataType: 'json',
                url: "{{ url('check_exists_mtd')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Kategori', 'Kategori sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#bcc_title').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_bc_btn').on('click', function() {
            jQuery.noConflict();
            $('#BcModal').modal('show');
            $(".bcc_image_item").attr('src', '')
            $("#bcc_image").val('');
            $('#_bcc_image').val('');
            $('#_bc_id').val('');
            $('#_bc_mode').val('add');
            $('#f_bc')[0].reset();
        });

        $('#edit_bc_btn').on('click', function() {
            jQuery.noConflict();
            var id = $('#bc_id').val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_id:id},
                dataType: 'json',
                url: "{{ url('get_bc')}}",
                success: function(r) {
                    if (r.status == '200') {
                        $('#_bc_id').val(id);
                        $('#_bc_mode').val('edit');
                        $('#BcModal').modal('show');
                        $('#bc_name').val(r.bc_name);
                    }
                }
            });
        });

        $('#add_bcc_btn').on('click', function() {
            jQuery.noConflict();
            $('#BccModal').modal('show');
            $(".bcc_image_item").attr('src', '')
            $("#bcc_image").val('');
            $('#_bcc_image').val('');
            $('#_bcc_id').val('');
            $('#_bcc_mode').val('add');
            $('#f_bcc')[0].reset();
            $('#delete_bcc_btn').hide();
        });

        $('#f_bc').on('submit', function(e) {
            e.preventDefault();
            $("#save_bc_btn").html('Proses ..');
            $("#save_bc_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('bc_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_bc_btn").html('Simpan');
                    $("#save_bc_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#BcModal").modal('hide');
                        $('#bc_id_panel').load(' #bc_id_panel');
                        bcc_table.draw();
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        
                    } else if (data.status == '400') {
                        $("#BcModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_bcc').on('submit', function(e) {
            e.preventDefault();
            $("#save_bcc_btn").html('Proses ..');
            $("#save_bcc_btn").attr("disabled", true);
            var content = tinyMCE.get('bcc_content').getContent();
            var formData = new FormData(this);
            if ($('#bc_id').val() == '') {
                $("#save_bcc_btn").html('Simpan');
                $("#save_bcc_btn").attr("disabled", false);
                swal('Blog', 'Silahkan pilin kategori blog terlebih dahulu', 'warning');
                return false;
            }
            formData.append('bc_id', $('#bc_id').val());
            formData.append('bcc_content', content);
            $.ajax({
                type:'POST',
                url: "{{ url('bcc_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_bcc_btn").html('Simpan');
                    $("#save_bcc_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#BccModal").modal('hide');
                        bcc_table.draw();
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                    } else if (data.status == '400') {
                        $("#BccModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_bc_btn').on('click', function(){
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
                        data: {_id:$('#bc_id').val()},
                        dataType: 'json',
                        url: "{{ url('bc_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#bc_id_panel').load(' #bc_id_panel');
                                bcc_table.draw();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#delete_bcc_btn').on('click', function(){
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
                        data: {_id:$('#_bcc_id').val()},
                        dataType: 'json',
                        url: "{{ url('bcc_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#BccModal').modal('hide');
                                bcc_table.draw();
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