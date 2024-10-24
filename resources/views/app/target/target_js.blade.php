<script>

    function daysInMonth(month, year) {
        return new Date(year, month, 0).getDate();
    }

    var global_tr_id, global_st_id, global_stt_id;

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var target_table = $('#Targettb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('target_datatables') }}",
                data : function (d) {
                    d.search = $('#target_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'tr_id', searchable: false},
            { data: 'tr_date_show', name: 'tr_date' },
            { data: 'tr_amount_show', name: 'tr_amount', orderable: false },
            { data: 'tr_amount_get', name: 'tr_amount_get', orderable: false },
            { data: 'tr_progress', name: 'tr_progress', orderable: false },
            { data: 'tr_total_sale', name: 'tr_total_sale', orderable: false },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var target_detail_table = $('#TargetDetailtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('target_detail_datatables') }}",
                data : function (d) {
                    d.search = $('#target_search').val();
                    d.tr_id = $('#tr_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'str_id', searchable: false},
            { data: 'st_name', name: 'st_name' },
            { data: 'stt_name', name: 'stt_name' },
            { data: 'tr_amount_show', name: 'tr_amount_show', orderable: false },
            { data: 'tr_amount_get', name: 'tr_amount_get', orderable: false },
            { data: 'tr_progress', name: 'tr_progress', orderable: false },
            { data: 'tr_total_sale', name: 'tr_total_sale', orderable: false },
            { data: 'action', name: 'action', orderable: false },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        target_table.buttons().container().appendTo($('#target_excel_btn' ));
        $('#target_search').on('keyup', function() {
            target_table.draw();
        });

        $('#Targettb tbody').on('click', 'tr td:not(:nth-child(6))', function () {
            var id = target_table.row(this).data().tr_id;
            var tr_date = target_table.row(this).data().tr_date;
            jQuery.noConflict();
            $('#TargetModal').modal('show');
            $('#tr_date').val(tr_date);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_target_btn').show();
            @endif
        });

        $('#tr_date').on('change', function() {
            var tr_date = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_tr_date:tr_date},
                dataType: 'json',
                url: "{{ url('check_exists_target')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Target', 'Periode target sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#tr_date').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_target_btn').on('click', function() {
            jQuery.noConflict();
            $('#TargetModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_target')[0].reset();
            $('#delete_target_btn').hide();
        });

        $('#add_target_detail_btn').on('click', function() {
            jQuery.noConflict();
            $('#SubSubTargetModal').modal('show');
            $('#save_target_detail_btn').removeClass('d-none');
            var date = $('#target_date').val();
            var dt = date.split('-');
            var total_row = daysInMonth(dt[0], dt[1]);
            var day = [
                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
            ];
            for (let i = 0; i < total_row; i++) {
                $('#target_amount').append('<input class="form-control pb-1" type="number" data-date="'+dt[1]+'-'+dt[0]+'-'+(i+1)+'" id="target_amount'+(i+1)+'" placeholder="['+day[i+1]+'] Tanggal '+(i+1)+'"/>');
            }
        });

        jQuery.noConflict();
        $('#SubSubTargetModal').on('hide.bs.modal', function() {
            $('#target_amount').html('');
        }).modal('hide');

        $('#save_target_detail_btn').on('click', function() {
            var date = $('#target_date').val();
            var tr_id = $('#tr_id').val();
            var st_id = $('#st_id').val();
            var stt_id = $('#stt_id').val();
            var dt = date.split('-');
            var total_row = daysInMonth(dt[0], dt[1]);
            var arr = [];

            if (st_id == '') {
                swal('Store', 'Store kosong, silahkan diisi dulu', 'warning');
                return false;
            }
            if (stt_id == '') {
                swal('Divisi', 'Divisi kosong, silahkan diisi dulu', 'warning');
                return false;
            }
            for (let i = 0; i < total_row; i++) {
                if ($('#target_amount'+(i+1)+'').val() == '') {
                    swal('Kosong', 'Masih ada target yang kosong, silahkan diisi dulu', 'warning');
                    return false;
                }
                arr[i] = [$('#target_amount'+(i+1)+'').attr('data-date'), $('#target_amount'+(i+1)+'').val()]
            }
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_tr_id:tr_id, _st_id:st_id, _stt_id:stt_id, _arr:arr},
                dataType: 'json',
                url: "{{ url('sv_target_detail')}}",
                success: function(r) {
                    if (r.status == '200'){
                        $('#SubSubTargetModal').modal('hide');
                        target_detail_table.draw();
                        swal("Berhasil", "Data berhasil disimpan", "success");
                    } else {
                        swal('Gagal', 'Gagal simpan data', 'error');
                    }
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            return false;
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);

            var tr_id = $('#tr_id').val();
            var st_id = $('#import_st_id').val();
            var stt_id = $('#import_stt_id').val();

            if (st_id == '') {
                swal('Store', 'Store kosong, silahkan diisi dulu', 'warning');
                return false;
            }
            if (stt_id == '') {
                swal('Divisi', 'Divisi kosong, silahkan diisi dulu', 'warning');
                return false;
            }

            var formData = new FormData(this);
            formData.append('_tr_id', tr_id);
            formData.append('_st_id', st_id);
            formData.append('_stt_id', stt_id);

            $.ajax({
                type:'POST',
                url: "{{ url('sv_target_detail_import')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    console.log(data);
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();
                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        swal('Berhasil', 'Data berhasil diimport', 'success');
                        $('#f_import')[0].reset();
                        target_detail_table.ajax.reload();
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

        $('#f_target').on('submit', function(e) {
            e.preventDefault();
            $("#save_target_btn").html('Proses ..');
            $("#save_target_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('tr_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_target_btn").html('Simpan');
                    $("#save_target_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#TargetModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        target_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#TargetModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_target_btn').on('click', function(){
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
                        url: "{{ url('tr_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#TargetModal').modal('hide');
                                target_table.ajax.reload();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#sub_target_detail', 'click', function() {
            jQuery.noConflict();
            $('#target_date').val($(this).attr('data-target_date'));
            $('#tr_id').val($(this).attr('data-tr_id'));
            $('#SubTargetModal').on('show.bs.modal', function() {
                target_detail_table.draw();
            }).modal('show');
        });

        $(document).delegate('#delete_sub_target', 'click', function() {
            var str_id = $(this).attr('data-str_id');
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
                        data: {_str_id:str_id},
                        dataType: 'json',
                        url: "{{ url('delete_sub_target')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal('Berhasil', 'Berhasil hapus data', 'success');
                                target_detail_table.ajax.reload();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#tr_amount_btn', 'click', function() {
            var str_id = $(this).attr('data-id');
            $('#save_target_detail_btn').addClass('d-none');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_str_id:str_id},
                dataType: 'json',
                url: "{{ url('check_str')}}",
                success: function(r) {
                    if (r.status == '200'){
                        $.each(r.sstr, function (index, value) {
                            jQuery.noConflict();
                            $('#SubSubTargetModal').modal('show');
                            var date = $('#target_date').val();
                            var dt = date.split('-');
                            var day = [
                                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
                                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
                                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
                                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
                                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
                            ];
                            $('#target_amount').append('<input class="form-control pb-1 edit_amount" data-sstr_id="'+value['id']+'" type="number" data-date="'+dt[1]+'-'+dt[0]+'-'+(index+1)+'" id="target_amount'+(index+1)+'" value="'+value['sstr_amount']+'" placeholder="['+day[index+1]+'] Tanggal '+(index+1)+'"/>');
                        });
                    }
                }
            });
            return false;
        });

        $(document).delegate('.edit_amount', 'change', function(){
            var sstr_id = $(this).attr('data-sstr_id');
            var value = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_sstr_id:sstr_id, _value:value},
                dataType: 'json',
                url: "{{ url('edit_target')}}",
                success: function(r) {
                    if (r.status == '200'){
                        toast('Updated!', 'Berhasil diubah', 'success');
                    }
                }
            });
            return false;
        });

        $(document).ready(function () {
            // Open the second modal when the button is clicked
            $("#ImportModalBtn").click(function () {
                $("#ImportModal").modal("show");
            });
        });

    });
</script>