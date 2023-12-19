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

        $('#imagePreview').on('click', function() {
            $(this).attr('src', '')
            $("#br_image").val('');
        });

        $('#bannerPreview').on('click', function() {
            $(this).attr('src', '')
            $("#br_banner").val('');
        });

        var brand_table = $('#Brandtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('brand_datatables') }}",
                data : function (d) {
                    d.search = $('#brand_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'br_image_show', name: 'br_image' },
            { data: 'br_banner_show', name: 'br_banner' },
            { data: 'br_name', name: 'br_name' },
            { data: 'br_slug', name: 'br_slug' },
            { data: 'br_description', name: 'br_description' },
            { data: 'is_local_show', name: 'is_local' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [[0, 'desc']],
        });

        brand_table.buttons().container().appendTo($('#brand_excel_btn' ));
        $('#brand_search').on('keyup', function() {
            brand_table.draw();
        });

        $('#Brandtb tbody').on('click', 'tr td:not(:nth-child(2), :nth-child(3))', function () {
            $('#imagePreview').attr('src', '');
            $('#bannerPreview').attr('src', '');
            var id = brand_table.row(this).data().id;
            var br_image = brand_table.row(this).data().br_image;
            var br_banner = brand_table.row(this).data().br_banner;
            var br_name = brand_table.row(this).data().br_name;
            var br_slug = brand_table.row(this).data().br_slug;
            var br_description = brand_table.row(this).data().br_description;
            var is_local = brand_table.row(this).data().is_local;
            jQuery.noConflict();
            $('#BrandModal').modal('show');
            $('#br_name').val(br_name);
            $('#br_slug').val(br_slug);
            $('#br_description').val(br_description);
            $('#is_local').val(is_local);
            if (br_image != null) {
                $('#imagePreview').attr('src', "{{ asset('api/brand/') }}/"+br_image);
            }
            if (br_banner != null) {
                $('#bannerPreview').attr('src', "{{ asset('api/brand/banner/') }}/"+br_banner);
            }
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#_image').val(br_image);
            $('#_banner').val(br_banner);
            @if ( $data['user']->delete_access == '1' )
            $('#delete_brand_btn').show();
            @endif
        });

        $('#add_brand_btn').on('click', function() {
            jQuery.noConflict();
            $('#BrandModal').modal('show');
            $('#imagePreview').attr('src', '');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_brand')[0].reset();
            $('#delete_brand_btn').hide();
        });

        $('#br_name').on('change', function() {
            var br_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_br_name:br_name},
                dataType: 'json',
                url: "{{ url('check_exists_brand')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Brand', 'Nama brand sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#br_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('br_import')}}",
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
                        brand_table.draw();
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

        $('#f_brand').on('submit', function(e) {
            e.preventDefault();
            $("#save_brand_btn").html('Proses ..');
            $("#save_brand_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('br_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_brand_btn").html('Simpan');
                    $("#save_brand_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $('#imagePreview').trigger('click');
                        $('#bannerPreview').trigger('click');
                        $("#BrandModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        brand_table.draw(false);
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
                        data: {_id:$('#_id').val()},
                        dataType: 'json',
                        url: "{{ url('br_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#BrandModal').modal('hide');
                                brand_table.draw();
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