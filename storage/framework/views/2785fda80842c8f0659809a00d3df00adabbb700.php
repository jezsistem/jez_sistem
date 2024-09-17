<script>
    function replaceComma(str)
    {
        var str_replace = str.replace(/,/g, '');
        return str_replace;
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var debt_list_table = $('#DebtListtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('debt_list_datatables')); ?>",
                data : function (d) {
                    d.search = $('#debt_list_search').val();
                    d.st_id = $('#st_id option:selected').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'dl_id', searchable: false},
            { data: 'st_name', name: 'st_name' },
            { data: 'ps_name', name: 'ps_name' },
            { data: 'br_name', name: 'br_name' },
            { data: 'dl_invoice', name: 'dl_invoice' },
            { data: 'dl_invoice_date_show', name: 'dl_invoice_date' },
            { data: 'dl_invoice_due_date_show', name: 'dl_invoice_due_date' },
            { data: 'dl_value_show', name: 'dl_value' },
            { data: 'dl_vat', name: 'dl_vat' },
            { data: 'dl_total_show', name: 'dl_total' },
            { data: 'payment_value', name: 'payment_value' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var dlp_table = $('#Paymenttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('payment_datatables')); ?>",
                data : function (d) {
                    d.dl_id = $('#dl_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'dlp_date_show', name: 'dlp_date' },
            { data: 'dlp_value', name: 'dlp_value' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        debt_list_table.buttons().container().appendTo($('#debt_list_excel_btn' ));
        $('#debt_list_search').on('keyup', function() {
            debt_list_table.draw();
        });

        $('#ps_id').select2({
            width: "100%",
            dropdownParent: $('#ps_id_parent')
        });
        $('#ps_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#DebtListtb tbody').on('click', 'tr td:not(:nth-child(11))', function () {
            var id = debt_list_table.row(this).data().dl_id;
            var ps_id = debt_list_table.row(this).data().ps_id;
            var br_id = debt_list_table.row(this).data().br_id;
            var st_id = debt_list_table.row(this).data().st_id;
            var dl_invoice = debt_list_table.row(this).data().dl_invoice;
            var dl_invoice_date = debt_list_table.row(this).data().dl_invoice_date;
            var dl_invoice_due_date = debt_list_table.row(this).data().dl_invoice_due_date;
            var dl_value = debt_list_table.row(this).data().dl_value;
            var dl_vat = debt_list_table.row(this).data().dl_vat;
            var dl_total = debt_list_table.row(this).data().dl_total;
            jQuery.noConflict();
            $('#DebtListModal').modal('show');
            jQuery('#ps_id').val(ps_id).trigger('change');
            jQuery('#br_id').val(br_id).trigger('change');
            jQuery('#st_id_data').val(st_id).trigger('change');
            $('#dl_invoice').val(dl_invoice);
            $('#dl_invoice_date').val(dl_invoice_date);
            $('#dl_invoice_due_date').val(dl_invoice_due_date);
            $('#dl_value').val(dl_value);
            $('#dl_vat').val(dl_vat);
            $('#dl_total').val(dl_total);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
                $('#delete_debt_list_btn').show();
            <?php endif; ?>
        });

        $('#st_id').on('change', function() {
            debt_list_table.draw();
        });

        $('#dl_value').on('keyup', function() {
            var value = $(this).val();
            $('#dl_total').val(value.toFixed());
        });

        $('#dl_vat').on('keyup', function() {
            var vat = $(this).val();
            var dpp = $('#dl_value').val();
            var total = parseFloat(dpp) + parseFloat(dpp)/100 * vat;
            $('#dl_total').val(total.toFixed());
        });

        $('#Paymenttb tbody').on('click', 'tr', function () {
            var id = dlp_table.row(this).data().id;
            var dl_id = dlp_table.row(this).data().dl_id;
            var dlp_value = dlp_table.row(this).data().dlp_value;
            var dlp_date = dlp_table.row(this).data().dlp_date;
            var dl_value = $('#dl_value_payment').val();
            var payment = $('#payment').val();
            if (dl_value == payment) {
                swal('Fullfilled', 'Sisa hutang sudah habis', 'warning');
                return false;
            }
            jQuery.noConflict();
            $('#AddPaymentModal').modal('show');
            $('#dl_id').val(dl_id);
            $('#dlp_value').val(replaceComma(dlp_value));
            $('#dlp_date').val(dlp_date);
            $('#_id_payment').val(id);
            $('#_mode_payment').val('edit');
            <?php if( $data['user']->g_name == 'administrator' ): ?>
                $('#delete_debt_list_btn').show();
            <?php endif; ?>
        });

        $('#add_debt_list_btn').on('click', function() {
            jQuery.noConflict();
            jQuery('#ps_id').val('').trigger('change');
            jQuery('#br_id').val('').trigger('change');
            $('#DebtListModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_debt')[0].reset();
            $('#delete_debt_list_btn').hide();
        });

        $('#import_modal_btn').on('click', function() {
            jQuery.noConflict();
            $('#ImportModal').modal('show');
        });

        $('#add_payment_btn').on('click', function() {
            jQuery.noConflict();
            var dl_value = $('#dl_value_payment').val();
            var payment = $('#payment').val();
            $('#_mode_payment').val('add');
            $('#AddPaymentModal').modal('show');
            $('#f_payment')[0].reset();
            $('#dlp_value').val(parseFloat(dl_value));
            $('#value_remain').val(parseFloat(dl_value) - parseFloat(dl_value));
        });

        $('#dlp_value').on('keyup', function() {
            var dl_value = $('#dl_value_payment').val();
            var payment = $('#payment').val();
            var value = $(this).val();
            if (value != '') {
                $('#value_remain').val(parseFloat(dl_value) - parseFloat(payment) - parseFloat(value));
            } else {
                $('#value_remain').val(parseFloat(dl_value) - parseFloat(payment));
            }
        });

        $(document).delegate('#payment_total_btn', 'click', function() {
            jQuery.noConflict();
            var dl_id = $(this).attr('data-dl_id');
            var dl_value = $(this).attr('data-dl_value');
            var payment = $(this).attr('data-payment');
            $('#dl_id').val(dl_id);
            $('#dl_value_payment').val(dl_value);
            $('#payment').val(payment);
            $('#PaymentModal').modal('show');
            dlp_table.draw();
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('debt_import')); ?>",
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
                        debt_list_table.draw();
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        swal('File', 'File yang anda import kosong atau format tidak tepat', 'warning');
                    } else {
                        $("#ImportModal").modal('hide');
                        swal('Gagal', 'Silahkan periksa format input pada template anda, pastikan kolom biru terisi sesuai dengan sistem', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_debt').on('submit', function(e) {
            e.preventDefault();
            $("#save_debt_list_btn").html('Proses ..');
            $("#save_debt_list_btn").attr("disabled", true);
            var formData = new FormData(this);
            formData.append('st_id', $('#st_id option:selected').val());
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('dl_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_debt_list_btn").html('Simpan');
                    $("#save_debt_list_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#DebtListModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        debt_list_table.draw();
                    } else if (data.status == '400') {
                        $("#DebtListModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_payment').on('submit', function(e) {
            e.preventDefault();
            var dl_value = $('#dl_value_payment').val();
            var payment = $('#payment').val();
            var formData = new FormData(this);
            var remain = $('#value_remain').val();
            if (remain < 0) {
                swal('Minus', 'Sisa hutang tidak boleh minus', 'warning');
                return false;
            }
            if (dl_value == payment) {
                swal('Fullfilled', 'Sisa hutang sudah habis', 'warning');
                return false;
            }
            $("#save_payment_btn").html('Proses ..');
            $("#save_payment_btn").attr("disabled", true);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('dlp_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_payment_btn").html('Simpan');
                    $("#save_payment_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $('#AddPaymentModal').modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        dlp_table.draw();
                        debt_list_table.draw();
                    } else if (data.status == '400') {
                        $("#DebtListModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_debt_list_btn').on('click', function(){
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
                        url: "<?php echo e(url('dl_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#DebtListModal').modal('hide');
                                debt_list_table.ajax.reload();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#delete_payment_btn').on('click', function(){
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
                        data: {_id:$('#_id_payment').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('dlp_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#DebtListModal').modal('hide');
                                dlp_table.draw();
                                debt_list_table.draw();
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
</script><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/debt_list/debt_list_js.blade.php ENDPATH**/ ?>