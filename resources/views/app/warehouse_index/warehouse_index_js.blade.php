<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var warehouse_index_table = $('#WarehouseIndextb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            searching: false,
            ajax: {
                url: "{{ url('warehouse_index_datatables') }}",
                data: function(d) {
                    d.search = $('#warehouse_index_search').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'w_code',
                    name: 'w_code'
                },
                {
                    data: 'st_name',
                    name: 'st_name'
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [
                [0, 'desc']
            ],
        });

        $('#WarehouseIndextb tbody').on('click', 'tr', function() {
            var id = warehouse_index_table.row(this).data().id;
            var w_code = warehouse_index_table.row(this).data().w_code;
            var st_id = warehouse_index_table.row(this).data().st_id;
            jQuery.noConflict();
            $('#WarehouseIndexModal').modal('show');
            $('#w_code').val(w_code);
            $('#st_id').val(st_id).trigger('change');
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ($data['user']->delete_access == '1')
                $('#delete_warehouse_index_btn').show();
            @endif
        });

        $('#add_warehouse_index_btn').on('click', function() {
            jQuery.noConflict();
            $('#WarehouseIndexModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_warehouse_index')[0].reset();
            $('#delete_warehouse_index_btn').hide();
        });

        $('#f_warehouse_index').on('submit', function(e) {
            e.preventDefault();
            $("#save_warehouse_index_btn").html('Proses ..');
            $("#save_warehouse_index_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ url('warehouse_index_save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_warehouse_index_btn").html('Simpan');
                    $("#save_warehouse_index_btn").attr("disabled", false);
                    if (data.status == 'success') {
                        $("#WarehouseIndexModal").modal('hide');
                        toastr.success('Data berhasil disimpan', 'Berhasil');
                        warehouse_index_table.draw();
                    } else {
                        $("#WarehouseIndexModal").modal('hide');
                        toastr.warning('Data tidak tersimpan', 'Gagal');
                    }
                },
                error: function() {
                    toastr.error('Terjadi kesalahan saat menyimpan data', 'Error');
                }
            });
        });


        $('#delete_warehouse_index_btn').on('click', function() {
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
                        data: {
                            _id: $('#_id').val()
                        },
                        dataType: 'json',
                        url: "{{ url('warehouse_index_delete') }}",
                        success: function(r) {
                            if (r.status == 'success') {
                                swal("Berhasil", "Data berhasil dihapus",
                                    "success");
                                $('#WarehouseIndexModal').modal('hide');
                                warehouse_index_table.ajax.reload();
                                toastr.success('Data berhasil dihapus', 'Berhasil');
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                                toastr.error('Data gagal dihapus', 'Gagal');
                            }
                        }
                    });
                    return false;
                }
            });
        });



    });
</script>
