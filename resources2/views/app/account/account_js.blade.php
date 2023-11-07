<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var account_table = $('#Accounttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('account_datatables') }}",
                data : function (d) {
                    d.search = $('#account_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'aid', searchable: false},
            { data: 'a_code', name: 'a_code' },
            { data: 'a_name', name: 'a_name' },
            { data: 'at_name', name: 'at_name' },
            { data: 'ac_name', name: 'ac_name' },
            { data: 'a_description', name: 'a_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        account_table.buttons().container().appendTo($('#account_excel_btn' ));
        $('#account_search').on('keyup', function() {
            account_table.draw();
        });

        $('#Accounttb tbody').on('click', 'tr', function () {
            var id = account_table.row(this).data().aid;
            var acid = account_table.row(this).data().acid;
            var a_name = account_table.row(this).data().a_name;
            var a_code = account_table.row(this).data().a_code;
            var a_description = account_table.row(this).data().a_description;
            jQuery.noConflict();
            $('#AccountModal').modal('show');
            $('#a_name').val(a_name);
            $('#a_code').val(a_code);
            $('#a_description').val(a_description);
            $('#ac_id').val(acid).trigger('change');
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_account_btn').show();
            @endif
        });

        $('#a_name').on('change', function() {
            var a_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_a_name:a_name},
                dataType: 'json',
                url: "{{ url('check_exists_account')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Nama Akun', 'Nama akun sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#a_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#a_code').on('change', function() {
            var a_code = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_a_code:a_code},
                dataType: 'json',
                url: "{{ url('check_exists_account_code')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Kode Akun', 'Kode akun sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#a_code').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_account_btn').on('click', function() {
            jQuery.noConflict();
            $('#AccountModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_account')[0].reset();
            $('#delete_account_btn').hide();
        });

        $('#f_account').on('submit', function(e) {
            e.preventDefault();
            $("#save_account_btn").html('Proses ..');
            $("#save_account_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('a_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_account_btn").html('Simpan');
                    $("#save_account_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#AccountModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        account_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#AccountModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_account_btn').on('click', function(){
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
                        url: "{{ url('a_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#AccountModal').modal('hide');
                                account_table.ajax.reload();
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