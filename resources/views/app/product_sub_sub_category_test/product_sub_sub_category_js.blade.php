<script>
    function getArticleSubSubCategory(id) {
        $.ajax({
            type: "GET",
            data: { _pssc_id: id },
            url: "{{ url('get_article_sub_sub_category') }}",
            success: function(data) {
                $('#ArticleSubSubCategorytb').DataTable().ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
            }
        });
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_sub_sub_category_table = $('#ProductSubSubCategorytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('product_sub_sub_category_datatables') }}",
                data : function (d) {
                    d.search = $('#product_sub_sub_category_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'pssc_name', name: 'pssc_name' },
            { data: 'pssc_weight', name: 'pssc_weight' },
            { data: 'pssc_description', name: 'pssc_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var articleTable = $('#ArticleSubSubCategorytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            // Add other DataTable options as needed
            ajax: {
                url: "{{ url('get_product_sub_sub_category') }}",
            },
            // add column
            columns: [
                { data: 'DT_RowIndex', name: 'id', searchable: false },
                { data: 'p_name', name: 'p_name' },
                { data: 'p_color', name: 'p_color'}
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "0%"
                }
            ],
            order: [[0, 'desc']],
        });


        product_sub_sub_category_table.buttons().container().appendTo($('#product_sub_sub_category_excel_btn' ));
        $('#product_sub_sub_category_search').on('keyup', function() {
            product_sub_sub_category_table.draw();
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
                    url: "{{ url('reload_product_sub_category')}}",
                    success: function(r) {
                        $('#psc_id').html(r);
                    }
                });
            } else {
                $('#product_category_selected_label').text('');
                $('#psc_id').html("<select class='form-control' id='psc_id' name='psc_id' required><option value=''>- Pilih -</option></select>");
            }
            $('#product_sub_category_selected_label').text('');
            $('#product_sub_sub_category_display').fadeOut();
        });

        $('#psc_id').on('change', function() {
            var label = $('#psc_id option:selected').text();
            if (label != '- Pilih Sub Kategori -') {
                $('#product_sub_category_selected_label').text(label);
                $('#product_sub_sub_category_display').fadeIn();
                product_sub_sub_category_table.draw();
            } else {
                $('#product_sub_category_selected_label').text('');
                $('#product_sub_sub_category_display').fadeOut();
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

        /**
        $('#ProductSubSubCategorytb tbody').on('click', 'tr', function () {
            var id = product_sub_sub_category_table.row(this).data().id;
            var pssc_name = product_sub_sub_category_table.row(this).data().pssc_name;
            var pssc_weight = product_sub_sub_category_table.row(this).data().pssc_weight;
            var pssc_description = product_sub_sub_category_table.row(this).data().pssc_description;
            jQuery.noConflict();
            $('#ProductSubSubCategoryModal').modal('show');
            $('#pssc_name').val(pssc_name);
            $('#pssc_weight').val(pssc_weight);
            $('#pssc_description').val(pssc_description);
            $('#pssc_id').val(id);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_product_sub_sub_category_btn').show();
            @endif
        });
        */
        $('#ProductSubSubCategorytb tbody').on('click', 'tr', function () {
            var id = product_sub_sub_category_table.row(this).data().id;

            jQuery.noConflict();
            $('#ProductSubSubCategoryArticleModal').modal('show');

            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_product_sub_sub_category_btn').show();
            @endif

            // Update the AJAX configuration of articleTable
            articleTable.ajax.url("{{ url('get_product_sub_sub_category')}}?_pssc_id=" + id).load();
        });

        $('#add_product_sub_sub_category_btn').on('click', function() {
            jQuery.noConflict();
            $('#ProductSubSubCategoryModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_product_sub_sub_category')[0].reset();
            $('#delete_product_sub_sub_category_btn').hide();
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('psc_import')}}",
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
                        product_sub_sub_category_table.ajax.reload();
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

        $('#f_product_sub_sub_category').on('submit', function(e) {
            e.preventDefault();
            $("#save_product_sub_sub_category_btn").html('Proses ..');
            $("#save_product_sub_sub_category_btn").attr("disabled", true);
            var formData = new FormData(this);
            formData.append('psc_id', $('#psc_id').val());
            $.ajax({
                type:'POST',
                url: "{{ url('pssc_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_product_sub_sub_category_btn").html('Simpan');
                    $("#save_product_sub_sub_category_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ProductSubSubCategoryModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        product_sub_sub_category_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ProductSubSubCategoryModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_product_sub_sub_category_btn').on('click', function(){
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
                        url: "{{ url('pssc_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#ProductSubSubCategoryModal').modal('hide');
                                product_sub_sub_category_table.ajax.reload();
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