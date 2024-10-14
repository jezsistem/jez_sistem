<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var data_table = $('#Datatb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('ia_datatables') }}",
                data : function (d) {
                    d.search = $('#data_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'st_name', name: 'st_name' },
            { data: 'instock_u_name_1', name: 'instock_u_name_1' },
            { data: 'instock_u_name_2', name: 'instock_u_name_2' },
            { data: 'exception_u_name_1', name: 'exception_u_name_1' },
            { data: 'exception_u_name_2', name: 'exception_u_name_2' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            order: [[0, 'desc']],
        });

        data_table.buttons().container().appendTo($('#export_btn' ));
        $('#data_search').on('keyup', function() {
            data_table.draw(false);
        });

        $('#Datatb tbody').on('click', 'tr', function () {
            var id = data_table.row(this).data().id;
            var st_id = data_table.row(this).data().st_id;
            var instock_u_id_1 = data_table.row(this).data().instock_u_id_1;
            var instock_u_id_2 = data_table.row(this).data().instock_u_id_2;
            var exception_u_id_1 = data_table.row(this).data().exception_u_id_1;
            var exception_u_id_2 = data_table.row(this).data().exception_u_id_2;
            jQuery.noConflict();
            $('#DataModal').modal('show');
            $('#st_id').val(st_id).trigger('change');
            $('#instock_u_id_1').val(instock_u_id_1).trigger('change');
            $('#instock_u_id_2').val(instock_u_id_2).trigger('change');
            $('#exception_u_id_1').val(exception_u_id_1).trigger('change');
            $('#exception_u_id_2').val(exception_u_id_2).trigger('change');
            $('#id').val(id);
            $('#mode').val('edit');
            $('#delete_btn').show();
        });

        $('#add_btn').on('click', function() {
            jQuery.noConflict();
            $('#DataModal').modal('show');
            $('#id').val('');
            $('#mode').val('add');
            $('#form')[0].reset();
            $('#delete_btn').hide();
        });

        $('#form').on('submit', function(e) {
            e.preventDefault();
            $("#save_btn").html('Proses ..');
            $("#save_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('ia_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_btn").html('Simpan');
                    $("#save_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#DataModal").modal('hide');
                        data_table.draw(false);
                        toast('Berhasil', 'Data berhasil disimpan', 'success');
                    } else if (data.status == '400') {
                        $("#DataModal").modal('hide');
                        toast('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_btn').on('click', function(){
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
                        data: {id:$('#id').val()},
                        dataType: 'json',
                        url: "{{ url('ia_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                $('#DataModal').modal('hide');
                                data_table.draw(false);
                                toast("Berhasil", "Data berhasil dihapus", "success");
                            } else {
                                toast('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

    });
</script>
