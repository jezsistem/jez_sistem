<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_supplier_table = $('#ProductSuppliertb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('product_supplier_datatables') }}",
                data : function (d) {
                    d.search = $('#product_supplier_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'ps_name', name: 'ps_name' },
            { data: 'ps_pkp_show', name: 'ps_pkp' },
            { data: 'ps_address', name: 'ps_address' },
            { data: 'ps_phone', name: 'ps_phone' },
            { data: 'ps_rekening', name: 'ps_rekening'},
            { data: 'ps_description', name: 'ps_description' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [[0, 'desc']],
        });

        product_supplier_table.buttons().container().appendTo($('#product_supplier_excel_btn' ));
        $('#product_supplier_search').on('keyup', function() {
            product_supplier_table.draw();
        });

        $('#ProductSuppliertb tbody').on('click', 'tr', function () {
            var id = product_supplier_table.row(this).data().id;
            var ps_name = product_supplier_table.row(this).data().ps_name;
            var ps_pkp = product_supplier_table.row(this).data().ps_pkp;
            var ps_due_day = product_supplier_table.row(this).data().ps_due_day;
            var ps_email = product_supplier_table.row(this).data().ps_email;
            var ps_phone = product_supplier_table.row(this).data().ps_phone;
            var ps_address = product_supplier_table.row(this).data().ps_address;
            var ps_npwp = product_supplier_table.row(this).data().ps_npwp;
            var ps_rekening = product_supplier_table.row(this).data().ps_rekening;
            var ps_description = product_supplier_table.row(this).data().ps_description;
            jQuery.noConflict();
            $('#ProductSupplierModal').modal('show');
            $('#ps_name').val(ps_name);
            $('#ps_pkp').val(ps_pkp);
            $('#ps_due_day').val(ps_due_day);
            $('#ps_email').val(ps_email);
            $('#ps_phone').val(ps_phone);
            $('#ps_address').val(ps_address);
            $('#ps_description').val(ps_description);
            $('#ps_npwp').val(ps_npwp);
            $('#ps_rekening').val(ps_rekening);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_product_supplier_btn').show();
            @endif
        });

        $('#add_product_supplier_btn').on('click', function() {
            jQuery.noConflict();
            $('#ProductSupplierModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_product_supplier')[0].reset();
            $('#delete_product_supplier_btn').hide();
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('ps_import')}}",
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
                        product_supplier_table.ajax.reload();
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

        $('#ps_name').on('change', function() {
            var ps_name = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_ps_name:ps_name},
                dataType: 'json',
                url: "{{ url('check_exists_supplier')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Nama', 'Nama supplier sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#ps_name').val('');
                        return false;
                    }
                }
            });
        });

        $('#f_product_supplier').on('submit', function(e) {
            e.preventDefault();
            $("#save_product_supplier_btn").html('Proses ..');
            $("#save_product_supplier_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('ps_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_product_supplier_btn").html('Simpan');
                    $("#save_product_supplier_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ProductSupplierModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        product_supplier_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ProductSupplierModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_product_supplier_btn').on('click', function(){
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
                        url: "{{ url('ps_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#ProductSupplierModal').modal('hide');
                                product_supplier_table.ajax.reload();
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