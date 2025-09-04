<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#u_secret_code').on('change', function() {
            var u_secret_code = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_u_secret_code:u_secret_code},
                dataType: 'json',
                url: "{{ url('check_exists_secret_code')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Kode', 'Kode sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#u_secret_code').val('');
                        return false;
                    }
                }
            });
        });

        // ======================= USER ===================== //

        $('#st_id').select2({
            width: "100%",
            dropdownParent: $('#st_id_parent')
        });
        $('#st_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        var user_table = $('#Usertb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('um_datatables') }}",
                data : function (d) {
                    d.search = $('#user_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'uid', searchable: false},
            { data: 'u_name', name: 'u_name' },
            { data: 'g_name', name: 'g_name' },
            { data: 'stt_name', name: 'stt_name' },
            { data: 'st_name', name: 'st_name' },
            { data: 'u_nip', name: 'u_nip' },
            { data: 'u_ktp', name: 'u_ktp' },
            { data: 'u_secret_code', name: 'u_secret_code' },
            { data: 'u_phone', name: 'u_phone' },
            { data: 'u_email', name: 'u_email' },
            { data: 'u_address', name: 'u_address' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        $('#user_search').on('keyup', function() {
            user_table.draw();
        });

        $('#Usertb tbody').on('click', 'tr', function () {
            var uid = user_table.row(this).data().uid;
            var st_id = user_table.row(this).data().st_id;
            var u_name = user_table.row(this).data().u_name;
            var u_nip = user_table.row(this).data().u_nip;
            var u_ktp = user_table.row(this).data().u_ktp;
            var u_secret_code = user_table.row(this).data().u_secret_code;
            var u_email = user_table.row(this).data().u_email;
            var u_phone = user_table.row(this).data().u_phone;
            var u_address = user_table.row(this).data().u_address;
            jQuery.noConflict();
            $('#UserModal').modal('show');
            jQuery('#st_id').val(st_id).trigger('change');
            $('#u_name').val(u_name);
            $('#u_nip').val(u_nip);
            $('#u_ktp').val(u_ktp);
            $('#u_secret_code').val(u_secret_code);
            $('#u_email').val(u_email);
            $('#u_phone').val(u_phone);
            $('#u_password').val('');
            $('#u_address').val(u_address);
            $('#_id').val(uid);
            $('#_mode').val('edit');

            $('#delete_user_btn').show();
        });

        $('#add_btn').on('click', function() {
            jQuery.noConflict();
            $('#UserModal').modal('show');
            $('#_id').val('');
            jQuery('#st_id').val('').trigger('change');
            $('#_mode').val('add');
            $('#f_user')[0].reset();
            $('#delete_user_btn').hide();
        });

        $('#f_user').on('submit', function(e) {
            e.preventDefault();
            $("#save_user_btn").html('Proses ..');
            $("#save_user_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('um_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_user_btn").html('Simpan');
                    $("#save_user_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#UserModal").modal('hide');
                        jQuery('#st_id').val('').trigger('change');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        user_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#UserModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_user_btn').on('click', function(){
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
                        url: "{{ url('um_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#UserModal').modal('hide');
                                user_table.ajax.reload();
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