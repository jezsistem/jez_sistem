<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_sub_category_table = $('#ProductSubCategorytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('product_sub_category_datatables') }}",
                data : function (d) {
                    d.search = $('#product_sub_category_search').val();
                    d.pc_id = $('#pc_id').val();

                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'pc_name', name: 'pc_name' },
            { data: 'psc_name', name: 'psc_name' },
            { data: 'psc_description', name: 'psc_description' },
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

        product_sub_category_table.buttons().container().appendTo($('#product_sub_category_excel_btn' ));
        $('#product_sub_category_search').on('keyup', function() {
            product_sub_category_table.draw();
        });

        $('#pc_id').select2({
            width: "100%",
            dropdownParent: $('#pc_id_parent')
        });
        $('#pc_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pc_id').on('change', function() {

            var label = $('#pc_id option:selected').text();
            if (label != '- Pilih -') {
                $('#product_category_selected_label').text(label);
                $('#product_sub_category_display').fadeIn();
                product_sub_category_table.draw();
            } else {
                $('#product_category_selected_label').text('');
                $('#product_sub_category_display').fadeOut();
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

        $('#pc_id').on('change', function() {
            var label = $('#pc_id option:selected').text();
            if (label != '- Pilih -') {
                $('#product_category_selected_label').text(label);
                $('#product_sub_category_display').fadeIn();
            } else {
                $('#product_category_selected_label').text('');
                $('#product_sub_category_display').fadeOut();
            }
        });

        $('#ProductSubCategorytb tbody').on('click', 'tr', function () {
            var id = product_sub_category_table.row(this).data().id;
            var psc_name = product_sub_category_table.row(this).data().psc_name;
            var psc_description = product_sub_category_table.row(this).data().psc_description;
            jQuery.noConflict();
            $('#ProductSubCategoryModal').modal('show');
            $('#psc_name').val(psc_name);
            $('#psc_description').val(psc_description);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_product_sub_category_btn').show();
            @endif
        });

        $('#psc_name').on('change', function() {
            var psc_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_psc_name:psc_name},
                dataType: 'json',
                url: "{{ url('check_exists_product_sub_category')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Sub Kategori', 'Sub kategori sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#psc_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_product_sub_category_btn').on('click', function() {
            jQuery.noConflict();
            $('#ProductSubCategoryModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_product_sub_category')[0].reset();
            $('#delete_product_sub_category_btn').hide();
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
                        product_sub_category_table.ajax.reload();
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

        $('#f_product_sub_category').on('submit', function(e) {
            e.preventDefault();
            $("#save_product_sub_category_btn").html('Proses ..');
            $("#save_product_sub_category_btn").attr("disabled", true);
            var formData = new FormData(this);
            formData.append('pc_id', $('#pc_id').val());
            $.ajax({
                type:'POST',
                url: "{{ url('psc_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_product_sub_category_btn").html('Simpan');
                    $("#save_product_sub_category_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ProductSubCategoryModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        product_sub_category_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ProductSubCategoryModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_product_sub_category_btn').on('click', function(){
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
                        url: "{{ url('psc_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#ProductSubCategoryModal').modal('hide');
                                product_sub_category_table.ajax.reload();
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