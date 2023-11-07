<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var investor_table = $('#MAtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brtl<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('i_datatables') }}",
                data : function (d) {
                    d.search = $('#investor_search').val();
                    d.st_id = $('#st_id_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'st_name', name: 'st_name' },
            { data: 'i_name', name: 'i_name' },
            { data: 'i_username', name: 'i_username' },
            { data: 'i_phone', name: 'i_phone' },
            { data: 'i_email', name: 'i_email' },
            { data: 'i_address', name: 'i_address' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        investor_table.buttons().container().appendTo($('#investor_excel_btn' ));
        $('#investor_search').on('keyup', function() {
            investor_table.draw();
        });

        $('#st_id_filter').on('change', function() {
            investor_table.draw();
        });

        $('#MAtb tbody').on('click', 'tr', function () {
            var id = investor_table.row(this).data().id;
            var st_id = investor_table.row(this).data().st_id;
            var i_name = investor_table.row(this).data().i_name;
            var i_username = investor_table.row(this).data().i_username;
            var i_phone = investor_table.row(this).data().i_phone;
            var i_email = investor_table.row(this).data().i_emaile;
            var i_address = investor_table.row(this).data().i_address;
            jQuery.noConflict();
            $('#MMModal').modal('show');
            $('#st_id').val(st_id);
            $('#i_name').val(i_name);
            $('#i_username').val(i_username);
            $('#i_phone').val(i_phone);
            $('#i_email').val(i_email);
            $('#i_address').val(i_address);
            $('#password').val('');
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_investor_btn').show();
            @endif
        });

        $('#add_investor_btn').on('click', function() {
            jQuery.noConflict();
            $('#MMModal').modal('show');
            $('#password').val('');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_investor')[0].reset();
            $('#delete_investor_btn').hide();
        });

        $('#f_investor').on('submit', function(e) {
            e.preventDefault();
            $("#save_investor_btn").html('Proses ..');
            $("#save_investor_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('i_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_investor_btn").html('Simpan');
                    $("#save_investor_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#MMModal").modal('hide');
                        investor_table.draw();
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                    } else if (data.status == '400') {
                        $("#MMModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_investor_btn').on('click', function(){
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
                        url: "{{ url('i_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#MMModal').modal('hide');
                                investor_table.draw();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#i_username').on('change', function() {
            var i_username = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {i_username:i_username},
                dataType: 'json',
                url: "{{ url('i_username')}}",
                success: function(r) {
                    if (r.status == '200') {
                        $('#i_username').val('');
                        swal('Username', 'Username sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        return false;
                    }
                }
            });
        });

    });
</script>
