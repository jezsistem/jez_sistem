<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var voucher_table = $('#Vouchertb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('voucher_datatables')); ?>",
                data : function (d) {
                    d.search = $('#voucher_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'vc_code', name: 'vc_code' },
            { data: 'vc_discount', name: 'vc_discount' },
            { data: 'vc_type_show', name: 'vc_type' },
            { data: 'vc_min_order', name: 'vc_min_order' },
            { data: 'vc_reuse_show', name: 'vc_reuse' },
            { data: 'vc_status_show', name: 'vc_status' },
            { data: 'vc_cashback_show', name: 'vc_cashback' },
            { data: 'vc_platform', name: 'vc_platform' },
            { data: 'vc_due_date_show', name: 'vc_due_date' },
            { data: 'item', name: 'vc_type' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        voucher_table.buttons().container().appendTo($('#voucher_excel_btn' ));
        $('#voucher_search').on('keyup', function() {
            voucher_table.draw();
        });

        $('#Vouchertb tbody').on('click', 'tr', function () {
            $('#random_input').addClass('d-none');
            $('#vc_code_input').removeClass('d-none');
            var id = voucher_table.row(this).data().id;
            var vc_code = voucher_table.row(this).data().vc_code;
            var vc_discount = voucher_table.row(this).data().vc_discount;
            var vc_min_order = voucher_table.row(this).data().vc_min_order;
            var vc_type = voucher_table.row(this).data().vc_type;
            var vc_reuse = voucher_table.row(this).data().vc_reuse;
            var vc_status = voucher_table.row(this).data().vc_status;
            var vc_cashback = voucher_table.row(this).data().vc_cashback;
            var vc_platform = voucher_table.row(this).data().vc_platform;
            var vc_due_date = voucher_table.row(this).data().vc_due_date;
            jQuery.noConflict();
            $('#VoucherModal').modal('show');
            $('#vc_code').val(vc_code);
            $('#vc_discount').val(vc_discount);
            $('#vc_min_order').val(vc_min_order);
            $('#vc_type').val(vc_type);
            $('#vc_reuse').val(vc_reuse);
            $('#vc_status').val(vc_status);
            $('#vc_cashback').val(vc_cashback);
            $('#vc_platform').val(vc_platform);
            $('#vc_due_date').val(vc_due_date);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_voucher_btn').show();
            <?php endif; ?>
        });

        $('#vc_code').on('change', function() {
            var vc_code = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {vc_code:vc_code},
                dataType: 'json',
                url: "<?php echo e(url('check_exists_voucher')); ?>",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Kode Voucher', 'Kode voucher sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#vc_code').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_voucher_btn').on('click', function() {
            jQuery.noConflict();
            $('#random_input').removeClass('d-none');
            $('#VoucherModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_voucher')[0].reset();
            $('#delete_voucher_btn').hide();
        });

        $('#f_voucher').on('submit', function(e) {
            e.preventDefault();
            var code = $('#vc_code').val();
            if (parseInt(code.length) < 6) {
                swal('Min 6 Karakter', 'Terdiri dari angka dan huruf', 'warning');
                return false;
            }
            $("#save_voucher_btn").html('Proses ..');
            $("#save_voucher_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('voucher_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_voucher_btn").html('Simpan');
                    $("#save_voucher_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#VoucherModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        voucher_table.draw();
                    } else if (data.status == '400') {
                        $("#VoucherModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_voucher_btn').on('click', function(){
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
                        url: "<?php echo e(url('voucher_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#VoucherModal').modal('hide');
                                voucher_table.draw();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#is_random', 'click', function() {
            $('#vc_qty').val('');
            if ($(this).is(':checked')) {
                $('#vc_qty').removeClass('d-none');
                $('#vc_code').prop('required', false);
                $('#vc_qty').prop('required', true);
                $('#vc_code_input').addClass('d-none');
            } else {
                $('#vc_qty').addClass('d-none');
                $('#vc_code').prop('required', true);
                $('#vc_qty').prop('required', false);
                $('#vc_code_input').removeClass('d-none');
            }
        });

    });
</script><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/voucher/voucher_js.blade.php ENDPATH**/ ?>