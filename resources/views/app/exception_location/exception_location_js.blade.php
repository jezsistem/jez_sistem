<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var exception_location_table = $('#ExceptionLocationtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('exception_location_datatables') }}",
                data : function (d) {
                    d.search = $('#exception_location_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'el_id', searchable: false},
            { data: 'st_name', name: 'st_name' },
            { data: 'pl_code', name: 'pl_code' },
            { data: 'qty', name: 'qty' },
            { data: 'el_description', name: 'el_description' },
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

        exception_location_table.buttons().container().appendTo($('#exception_location_excel_btn' ));
        $('#exception_location_search').on('keyup', function() {
            exception_location_table.draw();
        });

        $('#pl_id').select2({
            width: "100%",
            dropdownParent: $('#pl_id_parent')
        });
        $('#pl_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#ExceptionLocationtb tbody').on('click', 'tr', function () {
            var id = exception_location_table.row(this).data().el_id;
            var pl_id = exception_location_table.row(this).data().pl_id;
            var el_description = exception_location_table.row(this).data().el_description;
            jQuery.noConflict();
            $('#ExceptionLocationModal').modal('show');
            jQuery('#pl_id').val(pl_id).trigger('change');
            $('#el_description').val(el_description);
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#delete_exception_location_btn').show();
        });

        // $('#el_name').on('change', function() {
        //     var el_name = $(this).val();
        //     $.ajaxSetup({
        //         headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });
        //     $.ajax({
        //         type: "POST",
        //         data: {_el_name:el_name},
        //         dataType: 'json',
        //         url: "{{ url('check_exists_exception_location')}}",
        //         success: function(r) {
        //             if (r.status == '200') {
        //                 swal('Kategori', 'Kategori sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
        //                 $('#el_name').val('');
        //                 return false;
        //             }
        //         }
        //     });
        // });

        $('#add_exception_location_btn').on('click', function() {
            jQuery.noConflict();
            $('#ExceptionLocationModal').modal('show');
            jQuery('#pl_id').val('').trigger('change');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_exception_location')[0].reset();
            $('#delete_exception_location_btn').hide();
        });

        $('#f_exception_location').on('submit', function(e) {
            e.preventDefault();
            $("#save_exception_location_btn").html('Proses ..');
            $("#save_exception_location_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('el_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_exception_location_btn").html('Simpan');
                    $("#save_exception_location_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ExceptionLocationModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        exception_location_table.draw();
                    } else if (data.status == '400') {
                        $("#ExceptionLocationModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_exception_location_btn').on('click', function(){
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
                        url: "{{ url('el_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#ExceptionLocationModal').modal('hide');
                                exception_location_table.draw();
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