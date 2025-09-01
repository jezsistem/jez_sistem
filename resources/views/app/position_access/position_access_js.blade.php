<script>
    var position_access_table = '';

    function reloadPosition() {
        $.ajax({
            type: "GET",
            dataType: 'html',
            url: "{{ url('reload_position') }}",
            success: function(r) {
                $('#position_access_div').html(r);
            }
        });
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        position_access_table = $('#PositionAccesstb').DataTable({
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
                url: "{{ url('position-access-datatables') }}",
                data: function(d) {
                    d.search = $('#main_color_search').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'up_name',
                    name: 'up_name'
                },
                {
                    data: 'route',
                    name: 'route'
                },
                {
                    data: 'akses',
                    name: 'akses'
                },
                {
                    data: 'actions',
                    name: 'actions'
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


        $('#add_position_access_btn').on('click', function() {
            jQuery.noConflict();
            $('#MainColorModal').modal('show');
            reloadPosition();
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_position_access')[0].reset();
            $('#delete_main_color_btn').hide();
        });


        $('#f_position_access').on('submit', function(e) {
            e.preventDefault();
            $("#save_position_access_btn").html('Proses ..');
            $("#save_position_access_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ url('position-access-save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_position_access_btn").html('Simpan');
                    if (data.status == '200') {
                        toastr.success('Data berhasil disimpan', 'Berhasil');
                        if (position_access_table) {
                            position_access_table.ajax.reload(null, false);
                        }
                    } else if (data.status == '400') {
                        position_access_table.ajax.reload();
                    } else if (data.status == '400') {
                        toastr.warning('Data tidak tersimpan', 'Gagal');
                    }
                },
                error: function(data) {
                    toastr.error('Terjadi kesalahan saat menyimpan data', 'Error');
                    $("#save_position_access_btn").html('Simpan');
                    $("#save_position_access_btn").attr("disabled", false);
                }
            });
        });


        $(document).on('click', '#deleteBtn', function() {
            var route = $(this).data('route');
            var positionId = $(this).data('position-id');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ url('position-access-delete') }}/" + route + "/" + positionId,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == '200') {
                                Swal.fire(
                                    'Terhapus!',
                                    'Data berhasil dihapus.',
                                    'success'
                                );
                                if (position_access_table) {
                                    position_access_table.ajax.reload(null, false);
                                }
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Gagal menghapus data.',
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus data.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        $(document).on('change', '#switch_access', function() {
            var isChecked = $(this).is(':checked');
            var action = $(this).data('action');
            var positionId = $(this).data('position-id');
            var route = $(this).data('route');
            
            $.ajax({
                type: 'POST',
                url: "{{ url('change_access') }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    checked: isChecked,
                    action: action,
                    position_id: positionId,
                    route: route
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == '200') {
                        toastr.success('Status akses berhasil diubah', 'Berhasil');
                        if (position_access_table) {
                            position_access_table.ajax.reload(null, false);
                        }
                    } else {
                        toastr.error('Gagal mengubah status akses', 'Error');
                    }
                },
                error: function() {
                    toastr.error('Terjadi kesalahan saat mengubah status akses', 'Error');
                }
            });
        });


    });
</script>
