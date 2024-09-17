<script>

    function checkItem(pls_id)
    {
        if ($('#check_item_'+pls_id).is(':checked')) {
            $('#article_controller_'+pls_id).show();
        } else {
            $('#article_controller_'+pls_id).hide();
        }
    }

    function mutation(pst_id, pl_id, p_name, p_color, sz_name, pls_qty)
    {
        swal('Fitur Nonaktif', 'Silahkan gunakan setup lokasi stok versi 2', 'warning');
    }

    function addProduct(pst_id, p_name, sz_name, sz_unset)
    {
        //alert(pst_id+' '+p_name+' '+sz_name);
        //alert(sz_unset);
        jQuery('#pl_id_destination_add').prop('disabled', false);
        if (sz_unset == 0) {
            swal('Full / Habis', 'Produk dengan size ini sudah full disetup / stok habis', 'warning');
            return false;
        }
        var pl_id = $('#_pl_id').val();
        //alert(pl_id);
        $('#_pst_id_add').val(pst_id);
        $('#p_name_add').text(p_name);
        $('#sz_name_add').text(sz_name);
        $('#sz_unset').text(sz_unset);
        $('#_unset').val(sz_unset);
        $('#AddProductInLocationModal').on('show.bs.modal', function() {
            $('#f_product_add')[0].reset();
            jQuery('#pl_id_destination_add').val(pl_id).trigger('change');
            jQuery('#pl_id_destination_add').prop('disabled', true);
        }).modal('show');
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_in_location_table = $('#ProductInLocationtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('product_in_location_datatables')); ?>",
                data : function (d) {
                    d.search = $('#product_in_location_search').val();
                    d._pl_id = $('#_pl_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pls_id', searchable: false},
            { data: 'p_name', name: 'p_name' },
            { data: 'p_color', name: 'p_color' },
            { data: 'p_size', name: 'p_size', orderable: false },
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

        var product_location_setup_table = $('#ProductLocationSetuptb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('product_location_setup_datatables')); ?>",
                data : function (d) {
                    d.search = $('#product_location_setup_search').val();
                    d.st_id = $('#st_id_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pl_id', searchable: false},
            { data: 'st_name', name: 'st_name' },   
            { data: 'pl_location', name: 'pl_location', orderable: false },
            { data: 'pl_product', name: 'pl_product', orderable: false },
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

        var product_table = $('#Producttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('product_item_location_datatables')); ?>",
                data : function (d) {
                    d.search = $('#product_search').val();
                    d.br_id_filter = $('#br_id_filter_item').val();
                    d.mc_id_filter = $('#mc_id_filter_item').val();
                    d.sz_id_filter = $('#sz_id_filter_item').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pid', searchable: false},
            { data: 'p_article', name: 'p_name' },
            { data: 'p_action', name: 'p_action', sortable: false },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        product_in_location_table.buttons().container().appendTo($('#product_in_location_excel_btn' ));
        $('#product_in_location_search').on('keyup', function() {
            product_in_location_table.draw();
        });

        product_location_setup_table.buttons().container().appendTo($('#product_location_setup_excel_btn' ));
        $('#product_location_setup_search').on('keyup', function() {
            product_location_setup_table.draw();
        });

        $('#st_id_filter').select2({
            width: "180px",
            dropdownParent: $('#st_id_filter_parent')
        });
        $('#st_id_filter').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pl_id_destination').select2({
            width: "100%",
            dropdownParent: $('#pl_id_destination_parent')
        });
        $('#pl_id_destination').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pl_id_destination').on('change', function() {
            var pl_id_destination = $(this).val();
            var pl_id = $('#_pl_id').val();
            if (pl_id_destination == pl_id) {
                swal('BIN Tujuan', 'BIN tidak boleh sama dengan BIN saat ini', 'warning');
                jQuery('#pl_id_destination').val('').trigger('change');
                return false;
            }
        });

        $('#st_id').on('change', function() {
            var label = $('#st_id option:selected').text();
            if (label != '- Pilih -') {
                $('#store_selected_label').text(label);
                $('#product_location_display').fadeIn();
                product_location_setup_table.draw();
            } else {
                $('#store_selected_label').text('');
                $('#product_location_display').fadeOut();
            }
        });

        $('#st_id_filter').on('change', function() {
            product_location_setup_table.draw();
        });

        $('#product_search').on('keyup', function() {
            product_table.draw();
        });

        $('#br_id_filter_item').on('change', function() {
            product_table.draw();
        });

        $('#mc_id_filter_item').on('change', function() {
            product_table.draw();
        });

        $('#sz_id_filter_item').on('change', function() {
            product_table.draw();
        });

        $('#ProductLocationSetuptb tbody').on('click', 'tr', function () {
            var id = product_location_setup_table.row(this).data().pl_id;
            var pl_location = product_location_setup_table.row(this).data().pl_location_plain;
            jQuery.noConflict();
            $('#_pl_id').val(id);
            $('#ProductLocationSetupModal').on('show.bs.modal', function() {
                product_in_location_table.draw();
            }).modal('show');
            $('#stock_location_label').html(pl_location);
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_pl_id:id},
                dataType: 'html',
                url: "<?php echo e(url('check_product_in_location')); ?>",
                success: function(r) {
                    $('#product_location_detail').html(r);
                }
            });
        });

        $('#add_product_btn').on('click', function() {
            $('#AddProductModal').on('show.bs.modal', function() {
                product_table.draw();
            }).modal('show');
        });

        $('#add_product_finish_btn').on('click', function() {
            $('#AddProductModal').on('hide.bs.modal', function() {
                product_in_location_table.draw();
            }).modal('hide');
        });

        $('#pl_code').on('change', function() {
            var pl_code = $(this).val();
            var st_id = $('#st_id').val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_pl_code:pl_code, _st_id:st_id},
                dataType: 'json',
                url: "<?php echo e(url('pl_code_check_data')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        swal("Sudah ada", "Kode sudah ada", "warning");
                        $('#pl_code').val('');
                    }
                }
            });
            return false;
        });

        $('#f_product_location_setup').on('submit', function(e) {
            e.preventDefault();
            $("#save_product_location_btn").html('Proses ..');
            $("#save_product_location_btn").attr("disabled", true);
            var formData = new FormData(this);
            formData.append('st_id', $('#st_id').val());
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('pl_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_product_location_btn").html('Simpan');
                    $("#save_product_location_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ProductLocationSetupModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        product_location_setup_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ProductLocationSetupModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_product_mutation').on('submit', function(e){
            e.preventDefault();
            var avail_qty = $('#pls_qty_mutation').text();
            var mt_qty = $('#mt_qty').val();
            var formData = new FormData(this);
            if (parseInt(mt_qty) > parseInt(avail_qty)) {
                swal('Jumlah Mutasi', 'Jumlah tidak boleh lebih dari yang tersedia saat ini', 'warning');
                $('#mt_qty').val('');
                return false;
            }
            swal({
                title: "Mutasi..?",
                text: "Yakin data sudah benar ?",
                icon: "info",
                buttons: [
                    'Batalkan',
                    'Mutasi Sekarang'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $('#ProductMutationModal').modal('hide');
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: formData,
                        dataType: 'json',
                        url: "<?php echo e(url('sv_mutation')); ?>",
                        cache:false,
                        contentType: false,
                        processData: false,
                        success: function(r) {
                            if (r.status == '200'){
                                product_in_location_table.ajax.reload();
                                setTimeout(() => {
                                    product_location_setup_table.ajax.reload();
                                }, 200);
                                swal("Berhasil", "Data berhasil dimutasi", "success");
                            } else {
                                swal('Gagal', 'Gagal mutasi data', 'error');
                            }
                        }
                    });
                    setTimeout(() => {
                        product_in_location_table.ajax.reload();
                    }, 200);
                    return false;
                }
            })
        });

        $('#f_product_add').on('submit', function(e){
            e.preventDefault();
            var avail_qty = $('#_unset').val();
            var mt_qty = $('#mt_qty_add').val();
            //alert(mt_qty+' '+avail_qty);
            var formData = new FormData(this);
            formData.append('pl_id_destination_add', $('#_pl_id').val());
            if (parseInt(mt_qty) > parseInt(avail_qty)) {
                swal('Jumlah Setup', 'Jumlah tidak boleh lebih dari yang tersedia saat ini', 'warning');
                $('#mt_qty_add').val('');
                return false;
            }
            swal({
                title: "Setup..?",
                text: "Yakin setup produk pada BIN ini ?",
                icon: "info",
                buttons: [
                    'Batalkan',
                    'Setup Sekarang'
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
                        data: formData,
                        dataType: 'json',
                        url: "<?php echo e(url('sv_setup')); ?>",
                        cache:false,
                        contentType: false,
                        processData: false,
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil disetup", "success");
                                $('#AddProductInLocationModal').modal('hide');
                                product_table.ajax.reload();
                                product_in_location_table.ajax.reload();
                                product_location_setup_table.ajax.reload();
                            } else {
                                swal('Gagal', 'Gagal setup data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });
        
        $(document).delegate('#pl_export', 'click', function(e) {
            e.preventDefault();
            var st_id = $('#st_id_filter').val();
            if (st_id == '') {
                alert('Silahkan pilih store terlebih dahulu');
                return false;
            }
            window.location.href="<?php echo e(url('pl_export')); ?>?st_id="+st_id;
        });

    });
</script>
<?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/product_location_setup/product_location_setup_js.blade.php ENDPATH**/ ?>