<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var external_assignment_type_table = $('#ExternalAssignmenttb').DataTable({
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
                url: "{{ url('external_assignment_type_datatables') }}",
                data: function(d) {
                    d.search = $('#external_assignment_type_search').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'ea_name',
                    name: 'ea_name'
                },
                {
                    data: 'ea_desc',
                    name: 'ea_desc'
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

        external_assignment_type_table.buttons().container().appendTo($('#external_assignment_type_excel_btn'));
        $('#external_assignment_type_search').on('keyup', function() {
            external_assignment_type_table.draw();
        });

        $('#ExternalAssignmenttb tbody').on('click', 'tr', function() {
            var id = external_assignment_type_table.row(this).data().id;
            var ea_name = external_assignment_type_table.row(this).data().ea_name;
            var ea_desc = external_assignment_type_table.row(this).data().ea_desc;
            jQuery.noConflict();
            $('#ExternalAssignmentModal').modal('show');
            $('#ea_name').val(ea_name);
            $('#ea_desc').val(ea_desc);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ($data['user']->delete_access == '1')
                $('#delete_external_assignment_type_btn').show();
            @endif
        });

        $('#ea_name').on('change', function() {
            var ea_name_name = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    _ea_name: ea_name
                },
                dataType: 'json',
                url: "{{ url('check_exists_external_assignment_type') }}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Perusahaan',
                            'Data Perusahaan sudah ada disistem, silahkan ganti dengan yang lain',
                            'warning');
                        $('#ea_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_external_assignment_type_btn').on('click', function() {
            jQuery.noConflict();
            $('#ExternalAssignmentModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_external_assignment_type')[0].reset();
            $('#delete_external_assignment_type_btn').hide();
        });

        $('#f_external_assignment_type').on('submit', function(e) {
            e.preventDefault();
            $("#save_external_assignment_type_btn").html('Proses ..');
            $("#save_external_assignment_type_btn").attr("disabled", true);
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ url('ea_save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_external_assignment_type_btn").html('Simpan');
                    $("#save_external_assignment_type_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ExternalAssignmentModal").modal('hide');
                        toastr.success('Data berhasil disimpan'); // Toastr success message
                        external_assignment_type_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ExternalAssignmentModal").modal('hide');
                        toastr.warning('Data tidak tersimpan'); // Toastr warning message
                    }
                },
                error: function(data) {
                    toastr.error(
                        'Terjadi kesalahan saat menyimpan data'); // Toastr error message
                }
            });
        });

        $(document).delegate('#export_btn', 'click', function(e) {
            e.preventDefault();

            var type = 'npwp';
            window.location.href = "{{ url('export-perusahaan') }}?type=" + type + "";
        });

        $('#delete_external_assignment_type_btn').on('click', function() {
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
                            _id: $('#_id').val(),
                            _item: $('#ea_name').val()
                        },
                        dataType: 'json',
                        url: "{{ url('ea_delete') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                $('#ExternalAssignmentModal').modal('hide');
                                toastr.success('Data berhasil dihapus'); // Change to Toastr success message
                                external_assignment_type_table.ajax.reload();
                            } else {
                                toastr.error('Gagal hapus data'); // Change to Toastr error message
                            }
                        },
                        error: function() {
                            toastr.error(
                            'Terjadi kesalahan saat menghapus data'); // Toastr error for AJAX error
                        }
                    });
                    return false;
                }
            });
        });


    });
</script>