<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_location_table = $('#ProductLocationtb').DataTable({
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
                url: "{{ url('product_location_datatables') }}",
                data: function(d) {
                    d.search = $('#product_location_search').val();
                    d.st_id = $('#st_id').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'pl_id',
                    searchable: false
                },
                {
                    data: 'st_name',
                    name: 'st_name'
                },
                {
                    data: 'pl_code',
                    name: 'pl_code'
                },
                {
                    data: 'pl_name',
                    name: 'pl_name'
                },
                {
                    data: 'pl_description',
                    name: 'pl_description'
                },
                {
                    data: 'pl_default_show',
                    name: 'pl_default'
                },
                {
                    data: 'pl_refund',
                    name: 'pl_refund'
                },
                {
                    data: 'pl_default_failed_qc',
                    name: 'pl_default_failed_qc'
                },
                {
                    data: 'pl_freeze',
                    name: 'pl_freeze'
                },
                {
                    data: 'pl_offline',
                    name: 'pl_offline'
                },
                {
                    data: 'detail',
                    name: 'detail'
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

        product_location_table.buttons().container().appendTo($('#product_location_excel_btn'));
        $('#product_location_search').on('keyup', function() {
            product_location_table.draw();
        });

        $('#st_id').select2({
            width: "100%",
            dropdownParent: $('#st_id_parent')
        });
        $('#st_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_id').on('change', function() {
            var label = $('#st_id option:selected').text();
            if (label != '- Pilih -') {
                $('#store_selected_label').text(label);
                $('#product_location_display').fadeIn();
                product_location_table.draw();
            } else {
                $('#store_selected_label').text('');
                $('#product_location_display').fadeOut();
            }
        });

        $('#ProductLocationtb tbody').on('click', '.btn-detail', function() {
            let pl_id = $(this).data('plid');
            let data = product_location_table.rows().data().toArray().find(row => row.pl_id == pl_id);
            if (data) {
                showProductLocationModal(data);
            }
        });

        // Function to show the modal
        function showProductLocationModal(data) {
            jQuery.noConflict();
            $('#ProductLocationModal').modal('show');
            $('#pl_code').val(data.pl_code);
            $('#pl_name').val(data.pl_name);
            $('#pl_description').val(data.pl_description);
            $('#pl_default').val(data.pl_default);
            $('#pl_refund').val(data.pl_refund);
            $('#pl_failed_qc').val(data.pl_failed_qc);
            $('#pl_freeze').val(data.pl_freeze);
            $('#pl_capacity').val(data.pl_capacity);
            // jQuery('#pl_freeze').val(data.pl_freeze).trigger('change');
            $('#_id').val(data.pl_id);
            $('#_mode').val('edit');
            @if ($data['user']->delete_access == '1')
                $('#delete_product_location_btn').show();
            @endif
        }
        
        
        // $('#ProductLocationtb tbody').on('click', 'tr', function() {
        //     var id = product_location_table.row(this).data().pl_id;
        //     var pl_code = product_location_table.row(this).data().pl_code;
        //     var pl_name = product_location_table.row(this).data().pl_name;
        //     var pl_description = product_location_table.row(this).data().pl_description;
        //     var pl_default = product_location_table.row(this).data().pl_default;
        //     var pl_default_refund = product_location_table.row(this).data().pl_default_refund;
        //     jQuery.noConflict();
        //     $('#ProductCategoryModal').modal('show');
        //     $('#pl_code').val(pl_code);
        //     $('#pl_name').val(pl_name);
        //     $('#pl_description').val(pl_description);
        //     $('#pl_default').val(pl_default);
        //     $('#pl_default_refund').val(pl_default_refund);
        //     $('#_id').val(id);
        //     $('#_mode').val('edit');
        //     @if ($data['user']->delete_access == '1')
        //         $('#delete_product_location_btn').show();
        //     @endif
        // });

        $('#add_product_location_btn').on('click', function() {
            jQuery.noConflict();
            $('#ProductLocationModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_product_location')[0].reset();
            $('#delete_product_location_btn').hide();
        });

        $('#pl_code').on('change', function() {
            var pl_code = $(this).val();
            var st_id = $('#st_id').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    _pl_code: pl_code,
                    _st_id: st_id
                },
                dataType: 'json',
                url: "{{ url('pl_code_check_data') }}",
                success: function(r) {
                    if (r.status == '200') {
                        swal("Sudah ada", "Kode sudah ada", "warning");
                        $('#pl_code').val('');
                    }
                }
            });
            return false;
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ url('pl_import') }}",
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
                        toastr.success('Data berhasil diimport',
                            'Berhasil'); // Use toastr for success
                        $('#f_import')[0].reset();
                        product_location_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        toastr.warning('Data gagal diimport',
                            'Gagal'); // Use toastr for warning
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    toastr.error('Terjadi kesalahan: ' + errorThrown,
                        'Error'); // Use toastr for error messages
                }
            });
        });


        $('#f_product_location').on('submit', function(e) {
            e.preventDefault();
            $("#save_product_location_btn").html('Proses ..');
            $("#save_product_location_btn").attr("disabled", true);

            var formData = new FormData(this);
            formData.append('st_id', $('#st_id').val());

            $.ajax({
                type: 'POST',
                url: "{{ url('pl_save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_product_location_btn").html('Simpan');
                    $("#save_product_location_btn").attr("disabled", false);

                    if (data.status == '200') {
                        $("#ProductLocationModal").modal('hide');
                        toastr.success('Data berhasil disimpan',
                            'Berhasil');
                        product_location_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ProductLocationModal").modal('hide');
                        toastr.warning('Data tidak tersimpan',
                            'Gagal');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#save_product_location_btn").html('Simpan');
                    $("#save_product_location_btn").attr("disabled", false);
                    toastr.error('Terjadi kesalahan: ' + errorThrown,
                        'Error'); // Use toastr for error
                }
            });
        });


        $('#delete_product_location_btn').on('click', function() {
            swal({
                title: "Hapus..? ",
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
                        data: {
                            _id: $('#_id').val()
                        },
                        dataType: 'json',
                        url: "{{ url('pl_delete') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                toastr.success("Data berhasil dihapus",
                                "Berhasil"); // Use toastr for success
                                $('#ProductLocationModal').modal('hide');
                                product_location_table.ajax.reload();
                            } else {
                                toastr.error('Gagal hapus data',
                                'Gagal'); // Use toastr for error
                            }
                        },
                        error: function() {
                            toastr.error('Terjadi kesalahan saat menghapus data',
                                'Error'); // Handle AJAX error
                        }
                    });
                    return false;
                }
            });
        });

        $(document).on('change', '.toggle-freeze', function() {
            let plid = $(this).data('id');
            let isChecked = $(this).is(':checked') ? '1' : '0';

            $.ajax({
                url: '{{ url("pl_freeze_status") }}',
                method: 'POST',
                data: {
                    plid: plid,
                    pl_freeze: isChecked,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('Data berhasil diubah', 'Berhasil');
                    product_location_table.draw(false);
                },
                error: function(xhr) {
                    toastr.error('Gagal mengubah status.', 'Gagal');
                }
            });
        });

        $(document).on('change', '.toggle-offline', function() {
            let plid = $(this).data('id');
            let isChecked = $(this).is(':checked') ? '1' : '0';

            $.ajax({
                url: '{{ url("pl_offline_status") }}',
                method: 'POST',
                data: {
                    plid: plid,
                    pl_offline: isChecked,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('Data berhasil diubah', 'Berhasil');
                    product_location_table.draw(false);
                },
                error: function(xhr) {
                    toastr.error('Gagal mengubah status.', 'Gagal');
                }
            });
        });

    });
</script>
