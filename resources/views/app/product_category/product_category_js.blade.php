<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_category_table = $('#ProductCategorytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('product_category_datatables') }}",
                data: function(d) {
                    d.search = $('#product_category_search').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'pc_name',
                    name: 'pc_name'
                },
                {
                    data: 'pc_description',
                    name: 'pc_description'
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            order: [
                [0, 'desc']
            ],
        });

        product_category_table.buttons().container().appendTo($('#product_category_excel_btn'));
        $('#product_category_search').on('keyup', function() {
            product_category_table.draw();
        });

        $('#ProductCategorytb tbody').on('click', 'tr', function() {
            var id = product_category_table.row(this).data().id;
            var pc_name = product_category_table.row(this).data().pc_name;
            var pc_description = product_category_table.row(this).data().pc_description;
            jQuery.noConflict();
            $('#ProductCategoryModal').modal('show');
            $('#pc_name').val(pc_name);
            $('#pc_description').val(pc_description);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ($data['user']->delete_access == '1')
                $('#delete_product_category_btn').show();
            @endif
        });

        $('#pc_name').on('change', function() {
            var pc_name = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    _pc_name: pc_name
                },
                dataType: 'json',
                url: "{{ url('check_exists_product_category') }}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Kategori',
                            'Kategori sudah ada disistem, silahkan ganti dengan yang lain',
                            'warning');
                        $('#pc_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_product_category_btn').on('click', function() {
            jQuery.noConflict();
            $('#ProductCategoryModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_product_category')[0].reset();
            $('#delete_product_category_btn').hide();
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ url('pc_import') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();
                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        toastr.success('Data berhasil diimport'); // Toastr success message
                        $('#f_import')[0].reset();
                        product_category_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        toastr.warning('Data gagal diimport'); // Toastr warning message
                    }
                },
                error: function(data) {
                    toastr.error(
                        'Terjadi kesalahan saat mengimport data'); // Toastr error message
                }
            });
        });


        $('#f_product_category').on('submit', function(e) {
            e.preventDefault();
            $("#save_product_category_btn").html('Proses ..');
            $("#save_product_category_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ url('pc_save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_product_category_btn").html('Simpan');
                    $("#save_product_category_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ProductCategoryModal").modal('hide');
                        toastr.success('Data berhasil disimpan'); // Toastr success message
                        product_category_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ProductCategoryModal").modal('hide');
                        toastr.warning('Data tidak tersimpan'); // Toastr warning message
                    }
                },
                error: function(data) {
                    toastr.error(
                        'Terjadi kesalahan saat menyimpan data'); // Toastr error message
                }
            });
        });


        $('#delete_product_category_btn').on('click', function() {
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini?",
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
                        data: {
                            _id: $('#_id').val()
                        },
                        dataType: 'json',
                        url: "{{ url('pc_delete') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                toastr.success(
                                'Data berhasil dihapus'); // Toastr success message
                                $('#ProductCategoryModal').modal('hide');
                                product_category_table.ajax.reload();
                            } else {
                                toastr.error(
                                'Gagal hapus data'); // Toastr error message
                            }
                        },
                        error: function() {
                            toastr.error(
                            'Terjadi kesalahan saat menghapus data'); // Toastr error message
                        }
                    });
                    return false;
                }
            });
        });


    });
</script>
