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

        var wt_table = $('#Wttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('wt_datatables')); ?>",
                data : function (d) {
                    d.search = $('#wt_search').val();
                    d.filter = $('#status_filter').val();
                    d.payment = $('#payment_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'created_at_show', name: 'created_at' },
            { data: 'diff_time', name: 'created_at' },
            { data: 'pos_invoice_show', name: 'pos_invoice' },
            { data: 'cust_name', name: 'cust_name' },
            { data: 'pos_real_price', name: 'pos_real_price' },
            { data: 'pos_unique_code', name: 'pos_unique_code' },
            { data: 'pos_courier', name: 'pos_courier' },
            { data: 'pos_shipping', name: 'pos_shipping' },
            { data: 'pos_shipping_number', name: 'pos_shipping_number' },
            { data: 'item', name: 'created_at' },
            { data: 'pos_web_payment', name: 'pos_web_payment' },
            { data: 'pos_status_show', name: 'pos_status' },
            { data: 'pos_note_show', name: 'pos_note' },
            { data: 'pos_web_notif_show', name: 'pos_web_notif' },
            { data: 'cust_first_show', name: 'cust_first' },
            { data: 'cust_second_show', name: 'cust_second' },
            { data: 'cust_third_show', name: 'cust_third' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        wt_table.buttons().container().appendTo($('#wt_excel_btn' ));
        $('#wt_search').on('keyup', function() {
            wt_table.draw();
        });
        
        $('#status_filter, #payment_filter').on('change', function() {
            wt_table.draw();
        });

        $('#Wttb tbody').on('click', 'tr td:not(:nth-child(4))', function () {
            $('#bannerPreview').attr('src', '');
            var id = wt_table.row(this).data().id;
            var pos_status = wt_table.row(this).data().pos_status;
            var pos_invoice = wt_table.row(this).data().pos_invoice;
            if (pos_status == 'PAID') {
                swal({
                    title: "Print..?",
                    text: "Setelah ini anda akan diarahkan ke halaman summary order customer, silahkan klik tombol hijau PRINT INVOICE pada bagian paling atas",
                    icon: "warning",
                    buttons: [
                        'Batalkan',
                        'Print'
                    ],
                    dangerMode: false,
                }).then(function(isConfirm) {
                    if (isConfirm) {
                        $.ajaxSetup({
                            headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "POST",
                            data: {id:id},
                            dataType: 'json',
                            url: "<?php echo e(url('print_web_paid')); ?>",
                            success: function(r) {
                                if (r.status == '200'){
                                    var win = window.open('<?php echo e($data['ecommerce_url']); ?>/customer_paid/order/detail/id/data/'+id, '_blank');
                                    if (win) {
                                        win.focus();
                                    } else {
                                        alert('Please allow popups for this website');
                                    }
                                }
                            }
                        });
                        return false;
                    }
                })
                return false
            } else if (pos_status == 'SHIPPING NUMBER' || pos_status == 'DONE') {
                var win = window.open('<?php echo e(url('/')); ?>/print_invoice/'+pos_invoice, '_blank');
                if (win) {
                    win.focus();
                } else {
                    alert('Please allow popups for this website');
                }
                return false;
            } else if (pos_status == 'CANCEL') {
                swal('Cancel', 'Invoice tidak bisa diubah karena sudah cancel', 'warning');
                return false;
            }
            jQuery.noConflict();
            $('#WebTransactionModal').modal('show');
            $('#pos_status').val(pos_status);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_wt_btn').show();
            <?php endif; ?>
        });

        $('#f_wt').on('submit', function(e) {
            e.preventDefault();
            $("#save_wt_btn").html('Proses ..');
            $("#save_wt_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('wt_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_wt_btn").html('Simpan');
                    $("#save_wt_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#WebTransactionModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        wt_table.draw();
                    } else if (data.status == '400') {
                        $("#WebTransactionModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#pos_status').on('change', function() {
            var status = $(this).val();
            if (status == 'CANCEL') {
                swal({
                    title: "Yakin Cancel",
                    text: "Jika anda cancel, maka stok akan kembali, segala bentuk resiko ditanggung anda atas pembatalan manual invoice ini",
                    icon: "warning",
                    buttons: [
                        'Batalkan',
                        'Lanjut'
                    ],
                    dangerMode: true,
                }).then(function(isConfirm) {
                    if (isConfirm) {
                        $('#pos_status').val('CANCEL').trigger('change');
                    } else {
                        $('#pos_status').val('').trigger('change');
                    }
                })
            }
        });

    });
</script><?php /**PATH C:\laragon\www\jez\jez_sistem\resources\views/app/web_transaction/web_transaction_js.blade.php ENDPATH**/ ?>