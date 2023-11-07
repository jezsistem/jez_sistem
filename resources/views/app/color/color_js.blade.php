<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var color_table = $('#Colortb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('color_datatables') }}",
                data : function (d) {
                    d.search = $('#color_search').val();
                    d.mc_id = $('#mc_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'mc_name', name: 'mc_name' },
            { data: 'cl_name', name: 'cl_name' },
            { data: 'cl_description', name: 'cl_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        color_table.buttons().container().appendTo($('#color_excel_btn' ));
        $('#color_search').on('keyup', function() {
            color_table.draw();
        });

        $('#mc_id').select2({
            width: "100%",
            dropdownParent: $('#mc_id_parent')
        });
        $('#mc_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#mc_id').on('change', function() {
            var label = $('#mc_id option:selected').text();
            if (label != '- Pilih -') {
                $('#main_color_selected_label').text(label);
                $('#color_display').fadeIn();
                color_table.draw();
            } else {
                $('#main_color_selected_label').text('');
                $('#color_display').fadeOut();
            }
        });

        $('#import_modal_btn').on('click', function() {
            var label = $('#mc_id option:selected').text();
            if (label != '- Pilih -') {
                jQuery.noConflict();
                $('#ImportModal').modal('show');
            } else {
                swal('Pilih Warna', 'Silahkan pilih warna utama terlebih dahulu', 'warning');
                return false;
            }
        });

        $('#mc_id').on('change', function() {
            var label = $('#mc_id option:selected').text();
            if (label != '- Pilih -') {
                $('#main_color_selected_label').text(label);
                $('#color_display').fadeIn();
            } else {
                $('#main_color_selected_label').text('');
                $('#color_display').fadeOut();
            }
        });

        $('#Colortb tbody').on('click', 'tr', function () {
            var id = color_table.row(this).data().id;
            var cl_name = color_table.row(this).data().cl_name;
            var cl_description = color_table.row(this).data().cl_description;
            jQuery.noConflict();
            $('#ColorModal').modal('show');
            $('#cl_name').val(cl_name);
            $('#cl_description').val(cl_description);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_color_btn').show();
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

        $('#add_color_btn').on('click', function() {
            jQuery.noConflict();
            $('#ColorModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_color')[0].reset();
            $('#delete_color_btn').hide();
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('cl_import')}}",
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
                        color_table.ajax.reload();
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

        $('#f_color').on('submit', function(e) {
            e.preventDefault();
            $("#save_product_sub_category_btn").html('Proses ..');
            $("#save_product_sub_category_btn").attr("disabled", true);
            var formData = new FormData(this);
            formData.append('mc_id', $('#mc_id').val());
            $.ajax({
                type:'POST',
                url: "{{ url('cl_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_product_sub_category_btn").html('Simpan');
                    $("#save_product_sub_category_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ColorModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        color_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ColorModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_color_btn').on('click', function(){
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
                        url: "{{ url('cl_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#ColorModal').modal('hide');
                                color_table.ajax.reload();
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