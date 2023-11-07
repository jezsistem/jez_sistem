<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var account_classification_table = $('#AccountClassificationtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('account_classification_datatables') }}",
                data : function (d) {
                    d.search = $('#account_classification_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'acid', searchable: false},
            { data: 'ac_name', name: 'ac_name' },
            { data: 'at_name', name: 'at_name' },
            { data: 'ac_description', name: 'ac_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        account_classification_table.buttons().container().appendTo($('#account_classification_excel_btn' ));
        $('#account_classification_search').on('keyup', function() {
            account_classification_table.draw();
        });

        $('#AccountClassificationtb tbody').on('click', 'tr', function () {
            var id = account_classification_table.row(this).data().acid;
            var atid = account_classification_table.row(this).data().atid;
            var ac_name = account_classification_table.row(this).data().ac_name;
            var ac_description = account_classification_table.row(this).data().ac_description;
            jQuery.noConflict();
            $('#AccountClassificationModal').modal('show');
            $('#ac_name').val(ac_name);
            $('#ac_description').val(ac_description);
            $('#at_id').val(atid).trigger('change');
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_account_classification_btn').show();
            @endif
        });

        $('#ac_name').on('change', function() {
            var ac_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_ac_name:ac_name},
                dataType: 'json',
                url: "{{ url('check_exists_account_classification')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Klasifikasi Akun', 'Klasifikasi akun sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#ac_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_account_classification_btn').on('click', function() {
            jQuery.noConflict();
            $('#AccountClassificationModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_account_classification')[0].reset();
            $('#delete_account_classification_btn').hide();
        });

        $('#f_account_classification').on('submit', function(e) {
            e.preventDefault();
            $("#save_account_classification_btn").html('Proses ..');
            $("#save_account_classification_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('ac_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_account_classification_btn").html('Simpan');
                    $("#save_account_classification_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#AccountClassificationModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        account_classification_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#AccountClassificationModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_account_classification_btn').on('click', function(){
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
                        url: "{{ url('ac_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#AccountClassificationModal').modal('hide');
                                account_classification_table.ajax.reload();
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