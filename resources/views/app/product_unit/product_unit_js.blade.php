<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_unit_table = $('#ProductUnittb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('product_unit_datatables') }}",
                data : function (d) {
                    d.search = $('#product_unit_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'pu_name', name: 'pu_name' },
            { data: 'pu_description', name: 'pu_description' },
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

        product_unit_table.buttons().container().appendTo($('#product_unit_excel_btn' ));
        $('#product_unit_search').on('keyup', function() {
            product_unit_table.draw();
        });

        $('#ProductUnittb tbody').on('click', 'tr', function () {
            var id = product_unit_table.row(this).data().id;
            var pu_name = product_unit_table.row(this).data().pu_name;
            var pu_description = product_unit_table.row(this).data().pu_description;
            jQuery.noConflict();
            $('#ProductUnitModal').modal('show');
            $('#pu_name').val(pu_name);
            $('#pu_description').val(pu_description);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_product_unit_btn').show();
            @endif
        });

        $('#add_product_unit_btn').on('click', function() {
            jQuery.noConflict();
            $('#ProductUnitModal').modal('show');
            $('#imagePreview').attr('src', '');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_product_unit')[0].reset();
            $('#delete_product_unit_btn').hide();
        });

        $('#pu_name').on('change', function() {
            var pu_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_pu_name:pu_name},
                dataType: 'json',
                url: "{{ url('check_exists_product_unit')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Satuan Produk', 'Nama satuan produk sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#pu_name').val('');
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
                url: "{{ url('pu_import')}}",
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
                        product_unit_table.ajax.reload();
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

        $('#f_product_unit').on('submit', function(e) {
            e.preventDefault();
            $("#save_product_unit_btn").html('Proses ..');
            $("#save_product_unit_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('pu_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_product_unit_btn").html('Simpan');
                    $("#save_product_unit_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $('#imagePreview').trigger('click');
                        $("#ProductUnitModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        product_unit_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ProductUnitModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_product_unit_btn').on('click', function(){
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
                        url: "{{ url('pu_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#ProductUnitModal').modal('hide');
                                product_unit_table.ajax.reload();
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