<script>

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var whatsapp_table = $('#Watb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('whatsapp_datatables')); ?>",
                data : function (d) {
                    d.search = $('#whatsapp_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'wa_receiver', name: 'wa_receiver' },
            { data: 'wa_phone', name: 'wa_phone' },
            { data: 'wa_status', name: 'wa_status' },
            { data: 'created_at_show', name: 'created_at' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        whatsapp_table.buttons().container().appendTo($('#whatsapp_excel_btn' ));
        $('#whatsapp_search').on('keyup', function() {
            whatsapp_table.draw();
        });

        $('#add_whatsapp_btn').on('click', function() {
            jQuery.noConflict();
            $('#WaModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_whatsapp')[0].reset();
        });

        $('#f_whatsapp').on('submit', function(e) {
            e.preventDefault();
            $("#save_whatsapp_btn").html('Proses ..');
            $("#save_whatsapp_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('send_wa')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_whatsapp_btn").html('Simpan');
                    $("#save_whatsapp_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#WaModal").modal('hide');
                        swal('Berhasil', 'Pesan berhasil dikirim', 'success');
                        whatsapp_table.draw();
                    } else if (data.status == '400') {
                        $("#WaModal").modal('hide');
                        swal('Gagal', 'Pesan gagal dikirim', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });
    });
</script><?php /**PATH /home/jezpro.id/public_html/resources/views/app/whatsapp/whatsapp_js.blade.php ENDPATH**/ ?>