<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ======================= STORE ===================== //

        var store_table = $('#Storetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('store_datatables') }}",
                data : function (d) {
                    d.search = $('#store_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'sid', searchable: false},
            { data: 'st_name', name: 'st_name' },
            { data: 'st_email', name: 'st_email' },
            { data: 'st_phone', name: 'st_phone' },
            { data: 'st_address', name: 'st_address' },
            { data: 'st_description', name: 'st_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [[0, 'desc']],
        });

        store_table.buttons().container().appendTo($('#store_excel_btn' ));
        $('#store_search').on('keyup', function() {
            store_table.draw();
        });

        $('#Storetb tbody').on('click', 'tr', function () {
            var sid = store_table.row(this).data().sid;
            var st_name = store_table.row(this).data().st_name;
            var st_email = store_table.row(this).data().st_email;
            var st_phone = store_table.row(this).data().st_phone;
            var st_address = store_table.row(this).data().st_address;
            var st_description = store_table.row(this).data().st_description;
            jQuery.noConflict();
            $('#StoreModal').modal('show');
            $('#st_name').val(st_name);
            $('#st_email').val(st_email);
            $('#st_phone').val(st_phone);
            $('#st_address').val(st_address);
            $('#st_description').val(st_description);
            $('#_id').val(sid);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_store_btn').show();
            @endif
        });

        $('#add_store_btn').on('click', function() {
            jQuery.noConflict();
            $('#StoreModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_store')[0].reset();
            $('#delete_store_btn').hide();
        });

        $('#f_store').on('submit', function(e) {
            e.preventDefault();
            $("#save_store_btn").html('Proses ..');
            $("#save_store_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('st_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_store_btn").html('Simpan');
                    $("#save_store_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#StoreModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        store_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#StoreModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_store_btn').on('click', function(){
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
                        url: "{{ url('st_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#StoreModal').modal('hide');
                                store_table.ajax.reload();
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