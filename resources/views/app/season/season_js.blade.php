<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var season_table = $('#Seasontb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('season_datatables') }}",
                data : function (d) {
                    d.search = $('#season_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'ss_name', name: 'ss_name' },
            { data: 'ss_description', name: 'ss_description', searchable: false, sortable: false},
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

        season_table.buttons().container().appendTo($('#season_excel_btn' ));
        $('#season_search').on('keyup', function() {
            season_table.draw();
        });

        $('#Seasontb tbody').on('click', 'tr', function () {
            var id = season_table.row(this).data().id;
            var ss_name = season_table.row(this).data().ss_name;
            var ss_start = season_table.row(this).data().ss_start;
            var ss_end = season_table.row(this).data().ss_end;
            jQuery.noConflict();
            $('#SeasonModal').modal('show');
            $('#ss_name').val(ss_name);
            $('#ss_start').val(ss_start).trigger('change');
            $('#ss_end').val(ss_end).trigger('change');
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_season_btn').show();
            @endif
        });

        $('#ss_name').on('change', function() {
            var ss_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_ss_name:ss_name},
                dataType: 'json',
                url: "{{ url('check_exists_season')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Season', 'Season sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#ss_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_season_btn').on('click', function() {
            jQuery.noConflict();
            $('#SeasonModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_season')[0].reset();
            $('#delete_season_btn').hide();
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('ss_import')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();
                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        swal('Berhasil', 'Data berhasil diimport', 'success');
                        $('#f_import')[0].reset();
                        season_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        swal('Gagal', 'Data gagal diimport', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_season').on('submit', function(e) {
            e.preventDefault();
            $("#save_season_btn").html('Proses ..');
            $("#save_season_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('ss_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_season_btn").html('Simpan');
                    $("#save_season_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#SeasonModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        season_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#SeasonModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_season_btn').on('click', function(){
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
                        url: "{{ url('ss_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#SeasonModal').modal('hide');
                                season_table.ajax.reload();
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