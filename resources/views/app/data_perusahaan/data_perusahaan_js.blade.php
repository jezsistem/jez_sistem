<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var data_perusahaan_table = $('#DataPerusahaantb').DataTable({
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
                url: "{{ url('data_perusahaan_datatables') }}",
                data: function(d) {
                    d.search = $('#data_perusahaan_search').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'dp_name',
                    name: 'dp_name'
                },
                {
                    data: 'dp_npwp',
                    name: 'dp_npwp'
                },
                {
                    data: 'dp_description',
                    name: 'dp_description'
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

        data_perusahaan_table.buttons().container().appendTo($('#data_perusahaan_excel_btn'));
        $('#data_perusahaan_search').on('keyup', function() {
            data_perusahaan_table.draw();
        });

        $('#DataPerusahaantb tbody').on('click', 'tr', function() {
            var id = data_perusahaan_table.row(this).data().id;
            var dp_name = data_perusahaan_table.row(this).data().dp_name;
            var dp_npwp = data_perusahaan_table.row(this).data().dp_npwp;
            var dp_description = data_perusahaan_table.row(this).data().dp_description;
            jQuery.noConflict();
            $('#DataPerusahaanModal').modal('show');
            $('#dp_name').val(dp_name);
            $('#dp_npwp').val(dp_npwp);
            $('#dp_description').val(dp_description);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ($data['user']->delete_access == '1')
                $('#delete_data_perusahaan_btn').show();
            @endif
        });

        $('#dp_name').on('change', function() {
            var dp_name_name = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    _dp_name: dp_name
                },
                dataType: 'json',
                url: "{{ url('check_exists_data_perusahaan') }}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Perusahaan',
                            'Data Perusahaan sudah ada disistem, silahkan ganti dengan yang lain',
                            'warning');
                        $('#dp_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_data_perusahaan_btn').on('click', function() {
            jQuery.noConflict();
            $('#DataPerusahaanModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_data_perusahaan')[0].reset();
            $('#delete_data_perusahaan_btn').hide();
        });

        $('#f_data_perusahaan').on('submit', function(e) {
            e.preventDefault();
            $("#save_data_perusahaan_btn").html('Proses ..');
            $("#save_data_perusahaan_btn").attr("disabled", true);
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ url('dp_save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_data_perusahaan_btn").html('Simpan');
                    $("#save_data_perusahaan_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#DataPerusahaanModal").modal('hide');
                        toastr.success('Data berhasil disimpan'); // Toastr success message
                        data_perusahaan_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#DataPerusahaanModal").modal('hide');
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

        $('#delete_data_perusahaan_btn').on('click', function() {
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
                            _item: $('#dp_name').val()
                        },
                        dataType: 'json',
                        url: "{{ url('dp_delete') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                $('#data_perusahaanModal').modal('hide');
                                toastr.success(
                                'Data berhasil dihapus'); // Change to Toastr success message
                                data_perusahaan_table.ajax.reload();
                            } else {
                                toastr.error(
                                'Gagal hapus data'); // Change to Toastr error message
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