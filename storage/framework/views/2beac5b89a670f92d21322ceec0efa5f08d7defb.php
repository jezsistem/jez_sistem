<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_discount_table = $('#ProductDiscounttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                {"extend": 'excelHtml5', "text": 'Excel', "className": 'btn btn-primary btn-xs'}
            ],
            ajax: {
                url: "<?php echo e(url('product_discount_datatables')); ?>",
                data: function (d) {
                    d.search = $('#product_discount_search').val();
                    d.st_id = $('#st_id_filter').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'pd_id', searchable: false},
                {data: 'pd_name', name: 'pd_name'},
                {data: 'st_id_show', name: 'st_id'},
                {data: 'dv_name', name: 'dv_name'},
                {data: 'pd_type_show', name: 'pd_type'},
                {data: 'pd_value', name: 'pd_value'},
                {data: 'pd_date_show', name: 'pd_date'},
                {data: 'article', name: 'article', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "0%"
                }],
            order: [[0, 'desc']],
        });

        $('#st_id_filter').on('change', function () {
            product_discount_table.draw();
        });

        var product_discount_detail_table = $('#ProductDiscountDetailtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'B<"text-right"l>rt<"text-right"ip>',
            buttons: [
                {"extend": 'excelHtml5', "text": 'Excel', "className": 'btn btn-primary btn-xs'}
            ],
            ajax: {
                url: "<?php echo e(url('product_discount_detail_datatables')); ?>",
                data: function (d) {
                    d.search = $('#product_discount_detail_search').val();
                    d.pd_id = $('#_pd_id').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'pdd_id', searchable: false},
                {data: 'article_id', name: 'article_id'},
                {data: 'article', name: 'p_name'},
                {data: 'p_color', name: 'p_color'},
                {data: 'sz_name', name: 'sz_name'},
                {data: 'sell_price', name: 'sell_price', orderable: false},
                {data: 'sell_price_discount', name: 'sell_price_discount', orderable: false},
                {data: 'action', name: 'action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "0%"
                },
                {
                    "targets": 1,
                    "className": "text-center",
                    "width": "5%"
                },
                {
                    "targets": 2,
                    "width": "50%"
                }],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [[0, 'desc']],
        });

        var article_table = $('#Articletb').DataTable({
            destroy: true,
            processing: false,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                {"extend": 'excelHtml5', "text": 'Excel', "className": 'btn btn-primary btn-xs'}
            ],
            ajax: {
                url: "<?php echo e(url('product_discount_article_datatables')); ?>",
                data: function (d) {
                    d.search = $('#article_search').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'p_id', searchable: false},
                {data: 'br_name', name: 'br_name'},
                {data: 'p_name', name: 'p_name'},
                {data: 'p_color', name: 'p_color'},
                {data: 'sz_name_show', name: 'sz_name_show'},
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "0%"
                }],
            order: [[0, 'desc']],
        });

        product_discount_table.buttons().container().appendTo($('#product_discount_excel_btn'));
        $('#product_discount_search').on('keyup', function () {
            product_discount_table.draw();
        });

        $('#product_discount_detail_search').on('keyup', function () {
            product_discount_detail_table.draw();
        });

        $('#article_search').on('keyup', function () {
            article_table.draw();
        });

        $('#ProductDiscounttb tbody').on('click', 'tr td:not(:nth-child(8))', function () {
            var id = product_discount_table.row(this).data().pd_id;
            var pd_name = product_discount_table.row(this).data().pd_name;
            var st_id = product_discount_table.row(this).data().st_id;
            var std_id = product_discount_table.row(this).data().std_id;
            var pd_type = product_discount_table.row(this).data().pd_type;
            var pd_value = product_discount_table.row(this).data().pd_value;
            var pd_date = product_discount_table.row(this).data().pd_date;
            jQuery.noConflict();
            $('#ProductDiscountModal').modal('show');
            $('#pd_name').val(pd_name);
            $('#pd_type').val(pd_type);
            $('#st_id').val(st_id);
            $('#std_id').val(std_id);
            $('#pd_value').val(pd_value);
            $('#pd_date').val(pd_date);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_product_discount_btn').show();
            <?php endif; ?>
        });

        $(document).delegate('#discount_item_btn', 'click', function (e) {
            e.preventDefault();
            jQuery.noConflict();
            var pd_id = $(this).attr('data-pd_id');
            var pd_name = $(this).attr('data-pd_name');
            $('#_pd_id').val(pd_id);
            $('#product_discount_label').text(pd_name);
            $('#ProductDiscountDetailModal').modal('show');
            product_discount_detail_table.draw();
        });

        $(document).delegate('#delete_discount_item', 'click', function (e) {
            e.preventDefault();
            var pst_id = $(this).attr('data-pst_id');
            var pd_id = $('#_pd_id').val();
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini ?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Hapus'
                ],
                dangerMode: true,
            }).then(function (isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {_pst_id: pst_id, _pd_id: pd_id},
                        dataType: 'json',
                        url: "<?php echo e(url('delete_item_discount')); ?>",
                        success: function (r) {
                            if (r.status == '200') {
                                toast("Berhasil", "Artikel berhasil dihapus dari diskon", "success");
                                article_table.draw();
                                product_discount_detail_table.draw();
                                product_discount_table.draw();
                            } else {
                                swal('Gagal', 'Gagal hapus item', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#add_product_discount_btn').on('click', function () {
            jQuery.noConflict();
            $('#ProductDiscountModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_pd')[0].reset();
            $('#delete_product_discount_btn').hide();
        });

        $('#add_product_discount_detail_btn').on('click', function () {
            jQuery.noConflict();
            $('#ArticleModal').modal('show');
            article_table.draw();
        });

        $(document).delegate('#add_article_to_list', 'click', function (e) {
            e.preventDefault();
            jQuery.noConflict();
            var pst_id = $(this).attr('data-pst_id');
            var pd_id = $('#_pd_id').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_pst_id: pst_id, _pd_id: pd_id},
                dataType: 'json',
                url: "<?php echo e(url('add_item_to_discount')); ?>",
                success: function (r) {
                    if (r.status == '200') {
                        toast("Berhasil", "Artikel berhasil dditambah kedalam diskon", "success");
                        article_table.draw(false);
                        product_discount_detail_table.draw();
                        product_discount_table.draw();
                    } else if (r.status == '400') {
                        swal('Sudah Ada', 'Artikel sudah ada dalam list diskon', 'warning');
                    } else {
                        swal('Error', 'Error', 'error');
                    }
                }
            });
            return false;
        });

        $('#f_pd').on('submit', function (e) {
            e.preventDefault();
            $("#save_product_discount_btn").html('Proses ..');
            $("#save_product_discount_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "<?php echo e(url('pd_save')); ?>",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $("#save_product_discount_btn").html('Simpan');
                    $("#save_product_discount_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ProductDiscountModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        product_discount_table.draw(false);
                    } else if (data.status == '400') {
                        $("#ProductDiscountModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function (data) {
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_product_discount_btn').on('click', function () {
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini ?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Hapus'
                ],
                dangerMode: true,
            }).then(function (isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {_id: $('#_id').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('pd_delete')); ?>",
                        success: function (r) {
                            if (r.status == '200') {
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#ProductDiscountModal').modal('hide');
                                product_discount_table.ajax.reload();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#import_modal_btn').on('click', function () {
            jQuery.noConflict();
            $('#ImportModal').modal('show');
        });

        $('#f_import').on('submit', function (e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "<?php echo e(url('discount_import')); ?>",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();
                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        swal('Berhasil', 'Data berhasil diimport', 'success');
                        $('#f_import')[0].reset();
                        product_discount_table.draw();
                        product_discount_detail_table.draw();
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        swal('File', 'File yang anda import kosong atau format tidak tepat', 'warning');
                    } else {
                        $("#ImportModal").modal('hide');
                        swal('Gagal', 'Silahkan periksa format input pada template anda, pastikan kolom biru terisi sesuai dengan sistem', 'warning');
                    }
                },
                error: function (data) {
                    swal('Error', data, 'error');
                }
            });
        });

    });
</script>
<?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/product_discount/product_discount_js.blade.php ENDPATH**/ ?>