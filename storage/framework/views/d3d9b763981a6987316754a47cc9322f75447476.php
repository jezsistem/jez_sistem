<script>

    $(document).ready(function() {
        setInterval(() => {
            checkConfirmation();
            checkPaid();
        }, 20000);
        
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var wbc_table = $('#Wbctb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('konfirmasi_datatables')); ?>",
                data : function (d) {
                    d.search = $('#wbc_search').val();
                    d.filter = $('#status_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'created_at_show', name: 'created_at' },
            { data: 'pos_invoice_show', name: 'pos_invoice' },
            { data: 'cf_name', name: 'cf_name' },
            { data: 'cf_transfer_show', name: 'cf_transfer' },
            { data: 'cf_bank_transfer', name: 'cf_bank_transfer' },
            { data: 'cf_ip', name: 'cf_ip' },
            { data: 'cf_read_show', name: 'cf_read' },
            { data: 'u_name', name: 'u_name' },
            { data: 'cf_status_show', name: 'cf_status' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        function getRead(id)
        {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_id:id},
                dataType: 'json',
                url: "<?php echo e(url('wbc_read')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        wbc_table.draw();
                    }
                }
            });
            return false;
        }

        wbc_table.buttons().container().appendTo($('#wbc_excel_btn' ));
        $('#wbc_search').on('keyup', function() {
            wbc_table.draw();
        });
        
        $('#status_filter').on('change', function() {
            wbc_table.draw();
        });

        $('#Wbctb tbody').on('click', 'tr td:not(:nth-child(3))', function () {
            $('#bannerPreview').attr('src', '');
            var id = wbc_table.row(this).data().id;
            var cf_status = wbc_table.row(this).data().cf_status;
            var cf_transfer = wbc_table.row(this).data().cf_transfer;
            var pos_invoice = wbc_table.row(this).data().pos_invoice;
            if (cf_status == '1') {
                swal('Status Dibayar', 'Status sudah dikonfirmasi', 'warning');
                return false;
            } else if (cf_status == '2') {
                swal('Status Ditolak', 'Status sudah ditolak', 'warning');
                return false;
            }
            jQuery.noConflict();
            $('#WebConfirmationModal').modal('show');
            $('#cf_status').val(cf_status);
            $('#pos_invoice').val(pos_invoice);
            if (cf_transfer != null) {
                $('#imgPreview').attr('src', "<?php echo e($data['ecommerce_url']); ?>/api/confirmation/600/"+cf_transfer);
            }
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#delete_wbc_btn').show();
            getRead(id);
        });

        $('#f_wbc').on('submit', function(e) {
            e.preventDefault();
            $("#save_wbc_btn").html('Proses ..');
            $("#save_wbc_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('wbc_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_wbc_btn").html('Simpan');
                    $("#save_wbc_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#WebConfirmationModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        wbc_table.draw();
                    } else if (data.status == '400') {
                        $("#WebConfirmationModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_wbc_btn').on('click', function(){
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
                        data: {_id:$('#_id').val(), pos_invoice:$('#pos_invoice').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('wbc_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                $("#WebConfirmationModal").modal('hide');
                                swal('Berhasil', 'Data berhasil dihapus', 'success');
                                wbc_table.draw();
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
<?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/web_confirmation/web_confirmation_js.blade.php ENDPATH**/ ?>