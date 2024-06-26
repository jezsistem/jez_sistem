<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var payment_method_table = $('#PaymentMethodtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('payment_method_datatables') }}",
                data : function (d) {
                    d.search = $('#payment_method_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pm_id', searchable: false},
            { data: 'pm_name', name: 'pm_name' },
            { data: 'stt_name', name: 'stt_name' },
            { data: 'a_name', name: 'a_name' },
            { data: 'pm_description', name: 'pm_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        payment_method_table.buttons().container().appendTo($('#payment_method_excel_btn' ));
        $('#payment_method_search').on('keyup', function() {
            payment_method_table.draw();
        });

        $('#PaymentMethodtb tbody').on('click', 'tr', function () {
            var id = payment_method_table.row(this).data().pm_id;
            var pm_name = payment_method_table.row(this).data().pm_name;
            var stt_id = payment_method_table.row(this).data().stt_id;
            var a_id = payment_method_table.row(this).data().a_id;
            var pm_description = payment_method_table.row(this).data().pm_description;
            jQuery.noConflict();
            $('#PaymentMethodModal').modal('show');
            $('#pm_name').val(pm_name);
            jQuery('#stt_id').val(stt_id).trigger('change');
            jQuery('#a_id').val(a_id).trigger('change');
            $('#pm_description').val(pm_description);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_payment_method_btn').show();
            @endif
        });

        $('#pm_name').on('change', function() {
            var pm_name = $(this).val();
            var stt_id = $('#stt_id option:selected').val();
            if (stt_id == '') {
                swal('Divisi', 'Silahkan isi divisi terlebih dahulu', 'warning');
                $('#pm_name').val('');
                return false;
            }
            //alert(pm_name+' '+stt_id);
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_pm_name:pm_name, _stt_id:stt_id},
                dataType: 'json',
                url: "{{ url('check_exists_pm')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Metode Pembayaran', 'metode pembayaran dengan divisi ini sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#pm_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_payment_method_btn').on('click', function() {
            jQuery.noConflict();
            $('#PaymentMethodModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            jQuery('#stt_id').val('').trigger('change');
            jQuery('#a_id').val('').trigger('change');
            $('#f_payment_method')[0].reset();
            $('#delete_payment_method_btn').hide();
        });

        $('#f_payment_method').on('submit', function(e) {
            e.preventDefault();
            $("#save_payment_method_btn").html('Proses ..');
            $("#save_payment_method_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('pm_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_payment_method_btn").html('Simpan');
                    $("#save_payment_method_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#PaymentMethodModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        payment_method_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#PaymentMethodModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_payment_method_btn').on('click', function(){
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
                        url: "{{ url('pm_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#PaymentMethodModal').modal('hide');
                                payment_method_table.ajax.reload();
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