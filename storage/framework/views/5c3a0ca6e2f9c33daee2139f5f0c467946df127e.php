<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var tax_table = $('#Taxtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('tax_datatables')); ?>",
                data : function (d) {
                    d.search = $('#tax_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'tx_code', name: 'tx_code' },
            { data: 'tx_name', name: 'tx_name' },
            { data: 'a_id_purchase_name', name: 'a_id_purchase_name' },
            { data: 'a_id_sell_name', name: 'a_id_sell_name' },
            { data: 'tx_npwp', name: 'tx_npwp' },
            { data: 'tx_non_npwp', name: 'tx_non_npwp' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        tax_table.buttons().container().appendTo($('#tax_excel_btn' ));
        $('#tax_search').on('keyup', function() {
            tax_table.draw();
        });

        $('#Taxtb tbody').on('click', 'tr', function () {
            $('#imagePreview').attr('src', '');
            var id = tax_table.row(this).data().tid;
            var a_id_purchase = tax_table.row(this).data().a_id_purchase;
            var a_id_sell = tax_table.row(this).data().a_id_sell;
            var tx_code = tax_table.row(this).data().tx_code;
            var tx_name = tax_table.row(this).data().tx_name;
            var tx_npwp = tax_table.row(this).data().tx_npwp;
            var tx_non_npwp = tax_table.row(this).data().tx_non_npwp;
            jQuery.noConflict();
            $('#TaxModal').modal('show');
            $('#tx_code').val(tx_code);
            $('#tx_name').val(tx_name);
            $('#tx_npwp').val(tx_npwp);
            $('#tx_non_npwp').val(tx_non_npwp);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_tax_btn').show();
            <?php endif; ?>
        });

        $('#add_tax_btn').on('click', function() {
            jQuery.noConflict();
            $('#TaxModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_tax')[0].reset();
            $('#delete_tax_btn').hide();
        });

        $('#tx_phone').on('change', function() {
            var tx_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_tx_phone:tx_phone},
                dataType: 'json',
                url: "<?php echo e(url('check_exists_customer')); ?>",
                success: function(r) {
                    if (r.status == '200') {
                        swal('No Telepon', 'Nomor telepon sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#tx_phone').val('');
                        return false;
                    }
                }
            });
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('tx_import')); ?>",
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
                        tax_table.ajax.reload();
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

        $('#f_tax').on('submit', function(e) {
            e.preventDefault();
            $("#save_tax_btn").html('Proses ..');
            $("#save_tax_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('tx_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_tax_btn").html('Simpan');
                    $("#save_tax_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#TaxModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        tax_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#TaxModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_tax_btn').on('click', function(){
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
                        url: "<?php echo e(url('tx_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#TaxModal').modal('hide');
                                tax_table.ajax.reload();
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
</script><?php /**PATH /home/jezpro.id/public_html/resources/views/app/tax/tax_js.blade.php ENDPATH**/ ?>