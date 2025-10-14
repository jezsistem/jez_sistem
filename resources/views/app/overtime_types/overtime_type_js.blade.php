<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var overtime_type_table = $('#Overtimettb').DataTable({
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
                url: "{{ url('overtime_type_datatables') }}",
                data: function(d) {
                    d.search = $('#overtime_type_search').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'ot_name',
                    name: 'ot_name'
                },
                {
                    data: 'ot_desc',
                    name: 'ot_desc'
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

        overtime_type_table.buttons().container().appendTo($('#overtime_type_excel_btn'));
        $('#overtime_type_search').on('keyup', function() {
            overtime_type_table.draw();
        });

        $('#ExternalAssignmenttb tbody').on('click', 'tr', function() {
            var id = overtime_type_table.row(this).data().id;
            var ot_name = overtime_type_table.row(this).data().ot_name;
            var ot_desc = overtime_type_table.row(this).data().ot_desc;
            jQuery.noConflict();
            $('#ExternalAssignmentModal').modal('show');
            $('#ot_name').val(ot_name);
            $('#ot_desc').val(ot_desc);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ($data['user']->delete_access == '1')
                $('#delete_overtime_type_btn').show();
            @endif
        });

        $('#ot_name').on('change', function() {
            var ot_name_name = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    _ot_name: ot_name
                },
                dataType: 'json',
                url: "{{ url('check_exists_overtime_type') }}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Perusahaan',
                            'Data Perusahaan sudah ada disistem, silahkan ganti dengan yang lain',
                            'warning');
                        $('#ot_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_overtime_type_btn').on('click', function() {
            jQuery.noConflict();
            $('#ExternalAssignmentModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_overtime_type')[0].reset();
            $('#delete_overtime_type_btn').hide();
        });

        $('#f_overtime_type').on('submit', function(e) {
            e.preventDefault();
            $("#save_overtime_type_btn").html('Proses ..');
            $("#save_overtime_type_btn").attr("disabled", true);
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ url('ot_save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_overtime_type_btn").html('Simpan');
                    $("#save_overtime_type_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ExternalAssignmentModal").modal('hide');
                        toastr.success('Data berhasil disimpan'); // Toastr success message
                        overtime_type_table.ajax.reload();
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

        $('#delete_overtime_type_btn').on('click', function() {
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
                            _item: $('#ot_name').val()
                        },
                        dataType: 'json',
                        url: "{{ url('ot_delete') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                $('#ExternalAssignmentModal').modal('hide');
                                toastr.success('Data berhasil dihapus'); // Change to Toastr success message
                                overtime_type_table.ajax.reload();
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