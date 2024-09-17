<!-- DATERANGE -->
<script src="<?php echo e(asset('app')); ?>/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<script>
    function format(d) {
        var str = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;" id="ProductItemtb' + d.pid + '">' +
            '<tr>' +
            '<td style="white-space: nowrap;">Diskon (%) :</td>' +
            '<td><input type="number" id="po_discount' + d.pid + '" value="" onchange="return checkPurchasePrice(' + d.pid + ')"/></td>' +
            '</tr>' +
            '</table>';
        return str;
    }

    function checkPurchasePrice(pid) {
        var discount = parseFloat($('#po_discount' + pid).val());
        var price_tag = parseFloat($('#po_price_tag' + pid).val());
        var purchase_price = price_tag - (price_tag * discount / 100);

        $('input[data-purchase-price=' + pid + ']').val(purchase_price);
        $('#po_discount' + pid).prop('disabled', true);
    }

    function checkTotalItem(pid, index) {
        var qty = parseFloat($('#po_order_qty' + pid + index).val());
        var price_tag = parseFloat($('#po_price_tag' + pid).val());
        var purchase_price = parseFloat($('#po_purchase_price' + pid + index).val());
        // var total_price = 0;
        var total_row = $('tr[data-id-' + pid + ']').length;

        if (isNaN(purchase_price)) {
            var total_item_price = qty * price_tag;
            $('#po_purchase_price' + pid + index).val(price_tag)
        } else {
            var total_item_price = qty * purchase_price;
        }

        $('#po_total_item_price' + pid + index).val(total_item_price);
        $('#po_discount' + pid).prop('disabled', true);
    }

    function reloadArticleDetail(id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            dataType: 'html',
            data: {_po_id: id},
            url: "<?php echo e(url('check_pre_order_detail')); ?>",
            success: function (r) {
                $('#purchase_order_detail_content').html(r);
            }
        });
    }

    function poPurchasePrice(id, index, poad_id, po_id) {
        var qty = $('#poad_qty_' + id + '_' + index).val();
        var purchase_price = $('#poad_purchase_price_' + id + '_' + index).val();
        var total = parseFloat(qty) * parseFloat(purchase_price);
        var total_row = $('span[data-poa-' + id + ']').length;
        var total_price = 0;
        //alert(qty+' '+purchase_price+' '+total);
        $('#total_purchase_price_' + id + '_' + index).val(addCommas(total));
        for (let i = 0; i < total_row; ++i) {
            total_price = total_price + parseFloat(replaceComma($('#total_purchase_price_' + id + '_' + i).val()));
            //alert(total_price);
        }
        $('#poad_total_price_' + id).text(addCommas(total_price));
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_poad_id: poad_id, _qty: qty, _total: total},
            dataType: 'json',
            url: "<?php echo e(url('proad_save_qty_total')); ?>",
            success: function (r) {
                if (r.status == '200') {
                    toast('Disimpan', 'Informasi berhasil disimpan', 'success');
                    reloadPoTotalPrice(po_id);
                } else {
                    toast('Gagal', 'Informasi gagal disimpan', 'warning');
                }
            }
        });
    }

    function deletePoad(id) {
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
                var po_id = $('#_po_id').val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    data: {_id: id},
                    dataType: 'json',
                    url: "<?php echo e(url('proad_delete')); ?>",
                    success: function (r) {
                        if (r.status == '200') {
                            swal("Berhasil", "Data berhasil dihapus", "success");
                            reloadArticleDetail(po_id);
                        } else {
                            swal('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
                return false;
            }
        })
    }

    function deletePoa(id, po_id) {
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
                    data: {_id: id},
                    dataType: 'json',
                    url: "<?php echo e(url('proa_delete')); ?>",
                    success: function (r) {
                        if (r.status == '200') {
                            swal("Berhasil", "Data berhasil dihapus", "success");
                            reloadArticleDetail(po_id);
                        } else {
                            swal('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
                return false;
            }
        })
    }

    // CALCULATION 
    function poadPurchasePrice(id, index, purchase_price) {
        var poad_id = $('#poad_purchase_price_' + id + '_' + index).attr('data-poad-id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_poad_id: poad_id, _purchase_price: purchase_price},
            dataType: 'json',
            url: "<?php echo e(url('proad_save_purchase_price')); ?>",
            success: function (r) {
                if (r.status == '200') {
                    toast('Disimpan', 'Informasi berhasil disimpan', 'success');
                } else {
                    toast('Gagal', 'Informasi gagal disimpan', 'warning');
                }
            }
        });
    }

    function discount(id) {
        var discount = $('#poa_discount' + id).val();
        var extra_discount = $('#poa_extra_discount' + id).val();
        var total_row = $('span[data-poa-' + id + ']').length;
        var total = 0;

        if (discount == '' || discount == 0) {
            $('#poa_extra_discount' + id).val('');
            discount = 0;
            extra_discount = 0;
        }

        if (extra_discount == '' || extra_discount == 0) {
            for (let i = 0; i < total_row; ++i) {
                var price_tag = parseFloat(replaceComma($('#price_tag_' + id + '_' + i).val()));
                total = price_tag - (price_tag / 100 * parseFloat(discount))
                //alert(total);
                $('#poad_purchase_price_' + id + '_' + i).val(addCommas(total));
                $('#poad_qty_' + id + '_' + i).val('');
                $('#total_purchase_price_' + id + '_' + i).val('');
                poadPurchasePrice(id, i, total);
            }
        } else {
            for (let i = 0; i < total_row; ++i) {
                var price_tag = parseFloat(replaceComma($('#price_tag_' + id + '_' + i).val()));
                var subtotal = price_tag - (price_tag / 100 * parseFloat(discount))
                var total = subtotal - (subtotal / 100 * parseFloat(extra_discount))
                //alert(total);
                $('#poad_purchase_price_' + id + '_' + i).val(addCommas(total));
                $('#poad_qty_' + id + '_' + i).val('');
                $('#total_purchase_price_' + id + '_' + i).val('');
                poadPurchasePrice(id, i, total);
            }
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_id: id, _discount: discount},
            dataType: 'json',
            url: "<?php echo e(url('proa_save_discount')); ?>",
            success: function (r) {
                if (r.status == '200') {
                    toast('Disimpan', 'Informasi berhasil disimpan', 'success');
                } else {
                    toast('Gagal', 'Informasi gagal disimpan', 'warning');
                }
            }
        });
    }

    function extraDiscount(id) {
        var discount = $('#poa_discount' + id).val();
        var extra_discount = $('#poa_extra_discount' + id).val();
        var total_row = $('span[data-poa-' + id + ']').length;
        var total = 0;

        if (discount == 0 || discount == null) {
            swal('Diskon', 'Diskon kosong, silahkan isi terlebih dahulu', 'warning');
            $('#poa_extra_discount' + id).val('');
        } else {
            if (extra_discount == '' || extra_discount == 0) {
                extra_discount = 0;
            }
            for (let i = 0; i < total_row; ++i) {
                var price_tag = parseFloat(replaceComma($('#price_tag_' + id + '_' + i).val()));
                var subtotal = price_tag - (price_tag / 100 * parseFloat(discount))
                var total = subtotal - (subtotal / 100 * parseFloat(extra_discount))
                $('#poad_purchase_price_' + id + '_' + i).val(addCommas(total));
                $('#poad_qty_' + id + '_' + i).val('');
                $('#total_purchase_price_' + id + '_' + i).val('');
                poadPurchasePrice(id, i, total);
            }
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_id: id, _extra_discount: extra_discount},
            dataType: 'json',
            url: "<?php echo e(url('proa_save_extra_discount')); ?>",
            success: function (r) {
                if (r.status == '200') {
                    toast('Disimpan', 'Informasi berhasil disimpan', 'success');
                } else {
                    toast('Gagal', 'Informasi gagal disimpan', 'warning');
                }
            }
        });
    }

    // function subDiscount(id)
    // {
    //     var discount = $('#poa_discount'+id).val();
    //     var extra_discount = $('#poa_extra_discount'+id).val();
    //     var sub_discount = $('#poa_sub_discount'+id).val();
    //     var total_row = $('span[data-poa-'+id+']').length;
    //     var total = 0;

    //     if (discount == 0 || discount == null) {
    //         swal('Diskon', 'Diskon kosong, silahkan isi terlebih dahulu', 'warning');
    //         $('#poa_extra_discount'+id).val('');
    //         $('#poa_sub_discount'+id).val('');
    //     } else {
    //         if (extra_discount == '' || extra_discount == 0) {
    //             extra_discount = 0;
    //         }
    //         for (let i = 0; i < total_row; ++i) {
    //             var price_tag = parseFloat(replaceComma($('#price_tag_'+id+'_'+i).val()));
    //             var subtotal = price_tag - (price_tag/100 * parseFloat(discount))
    //             var total = subtotal - (subtotal/100 * parseFloat(extra_discount))
    //             $('#poad_purchase_price_'+id+'_'+i).val(addCommas(total));
    //             $('#poad_qty_'+id+'_'+i).val('');
    //             $('#total_purchase_price_'+id+'_'+i).val('');
    //             poadPurchasePrice(id, i, total);
    //         }
    //     }

    //     $.ajaxSetup({
    //         headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         }
    //     });
    //     $.ajax({
    //         type: "POST",
    //         data: {_id:id, _extra_discount:extra_discount},
    //         dataType: 'json',
    //         url: "<?php echo e(url('poa_save_extra_discount')); ?>",
    //         success: function(r) {
    //             if (r.status == '200'){
    //                 toast('Disimpan', 'Informasi berhasil disimpan' ,'success');
    //             } else {
    //                 toast('Gagal', 'Informasi gagal disimpan' ,'warning');
    //             }
    //         }
    //     });
    // }

    function replaceComma(str) {
        var str_replace = str.replace(/,/g, '');
        return str_replace;
    }

    function addCommas(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    function reloadPoTotalPrice(id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {_po_id: id},
            url: "<?php echo e(url('reload_pre_order_detail')); ?>",
            success: function (r) {
                if (r.status == '200') {
                    $('#poad_total_price').text(addCommas(r.total_po));
                } else {
                    toast('Gagal', 'gagal menampilkan total terbaru', 'warning');
                }
            }
        });
    }

    function orderQty(id, index, poad_id) {
        var po_id = $('#_po_id').val();
        var qty = $('#poad_qty_' + id + '_' + index).val();
        var purchase_price = $('#poad_purchase_price_' + id + '_' + index).val();
        var price_tag = $('#price_tag_' + id + '_' + index).val();
        var total_row = $('span[data-poa-' + id + ']').length;
        var total_price = 0;
        var total = 0;
        if (qty == '') {
            qty = 0;
        }
        if (purchase_price != '') {
            total = parseFloat(qty) * parseFloat(replaceComma(purchase_price));
        } else {
            total = parseFloat(qty) * parseFloat(replaceComma(price_tag));
        }
        $('#total_purchase_price_' + id + '_' + index).val(addCommas(total));
        for (let i = 0; i < total_row; ++i) {
            total_price = total_price + parseFloat(replaceComma($('#total_purchase_price_' + id + '_' + i).val()));
            //alert(total_price);
        }
        $('#poad_total_price_' + id).text(addCommas(total_price));
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_poad_id: poad_id, _qty: qty, _total: total},
            dataType: 'json',
            url: "<?php echo e(url('proad_save_qty_total')); ?>",
            success: function (r) {
                if (r.status == '200') {
                    toast('Disimpan', 'Informasi berhasil disimpan', 'success');
                    reloadPoTotalPrice(po_id);
                } else {
                    toast('Gagal', 'Informasi gagal disimpan', 'warning');
                }
            }
        });
    }

    function saveAllPo() {
        var po_id = $('#_po_id').val();
        swal({
            title: "Simpan..?",
            text: "Yakin simpan data PO ini ?",
            icon: "info",
            buttons: [
                'Batalkan',
                'Simpan'
            ],
            dangerMode: false,
        }).then(function (isConfirm) {
            if (isConfirm) {
                var total_row = $('.order_po_qty').length;
                for (let i = 0; i < total_row; ++i) {
                    $('order_po_qty_row' + i).trigger('change');
                }
                reloadArticleDetail(po_id);
            }
        });
    };

    function reminder(id) {
        var reminder = $('#poa_reminder' + id).val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_id: id, _reminder: reminder},
            dataType: 'json',
            url: "<?php echo e(url('proa_save_reminder')); ?>",
            success: function (r) {
                if (r.status == '200') {
                    toast('Disimpan', 'Informasi berhasil disimpan', 'success');
                } else {
                    toast('Gagal', 'Informasi gagal disimpan', 'warning');
                }
            }
        });
    }

    // CALCULATION

    $(document).delegate('#po_check_item', 'click', function () {
        var pid = $(this).attr('data-id');
        var index = $(this).attr('data-index');
        var total_item_price = $('#po_total_item_price' + pid + index).val();
        var po_total_price = $('#po_total_price' + pid).val();
        if (total_item_price == '' || total_item_price == 0) {
            swal('Order Qty', 'silahkan tentukan jumlah item PO terlebih dahulu', 'warning');
            return false;
        }
        if ($(this).is(':checked')) {
            $('#po_total_price' + pid).val(parseFloat(po_total_price) + parseFloat(total_item_price));
        } else {
            $('#po_total_price' + pid).val(parseFloat(po_total_price) - parseFloat(total_item_price));
        }
        alert(pid + ' || ' + index + ' || ' + po_total_price);
    });

    $(document).delegate('#checkbox_add_item', 'click', function () {
        var poid = $('#_po_id').val();
        var pid = $(this).attr('data-pid');
        var psid = $(this).attr('data-psid');
        var index = $(this).attr('data-index');
        var status = '';
        if ($(this).is(':checked')) {
            status = 'notchecked';
        } else {
            status = 'checked';
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {_poid: poid, _pid: pid, _psid: psid, _status: status},
            url: "<?php echo e(url('create_pre_order_detail')); ?>",
            success: function (r) {
                if (r.status == '200') {

                } else {
                    swal('Sudah ada', 'Produk sudah ada pada list PO', 'warning');
                    $('.checkbox_add_item' + pid + '_' + index).prop('checked', true);
                    $('.checkbox_add_item' + pid + '_' + index).prop('disabled', true);
                }
            }
        });
    });

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var global_po_id;

        var purchase_order_table = $('#PurchaseOrdertb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            searchable: false,
            dom: '<"text-right"l>Brt<"text-right"ip>',
            buttons: [
                {
                    "extend": 'excelHtml5',
                    "text": 'Excel',
                    "className": 'btn btn-primary btn-xs',
                    "exportOptions": {orthogonal: 'export'}
                }
            ],
            ajax: {
                url: "<?php echo e(url('pre_order_datatables')); ?>",
                data: function (d) {
                    d.search = $('#purchase_order_search').val();
                    d.st_id = $('#st_id_filter').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'po_id', searchable: false},
                {data: 'po_created_at_show', name: 'po_created_at'},
                {data: 'st_name', name: 'st_name'},
                {data: 'ps_name', name: 'ps_name'},
                {data: 'pre_order_code', name: 'pre_order_code'},
                {
                    data: 'po_total',
                    name: 'po_total',
                    render: function (data, type, row) {
                        return type === 'export' ?
                            data.replace(/[$,]/g, '') :
                            data;
                    }
                },
                {data: 'po_status', name: 'po_status'},
            ],
            columnDefs: [
                {
                    "targets": 5,
                    "className": "text-center",
                    "orderable": false,
                    "width": "0%"
                },
                {
                    "targets": 6,
                    "className": "text-center",
                    "orderable": false,
                    "width": "0%"
                }
                // {
                //     "orderable": false, "targets": 5
                // },
                // {
                //     "orderable": false, "targets": 6
                // },
                // {
                //     "orderable": false, "targets": 7
                // }
            ],
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
                {"extend": 'excelHtml5', "text": 'Excel', "className": 'btn btn-primary btn-xs'}
            ],
            ajax: {
                url: "<?php echo e(url('product_item_datatables')); ?>",
                data: function (d) {
                    d.search = $('#product_search').val();
                    d.ps_id = $('#ps_id').val();
                    d.br_id_filter = $('#br_id_filter_item').val();
                    d.mc_id_filter = $('#mc_id_filter_item').val();
                    d.psc_id_filter = $('#psc_id_filter_item').val();
                    d.sz_id_filter = $('#sz_id_filter_item').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'pid', searchable: false},
                {data: 'p_name_show', name: 'p_name'},
                {data: 'article_id', name: 'article_id'},
                {data: 'p_color_show', name: 'p_color'},
                {data: 'br_name', name: 'br_name'},
                {data: 'p_action', name: 'p_action', sortable: false},
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "0%"
                }],
            order: [[0, 'desc']],
        });

        $('#Producttb tbody').on('click', '#add_product_size_btn', function () {
            if ($(this).text().indexOf('Batal') >= 0) {
                $(this).prop('disabled', true);
                $(this).html('Tambah Item');
                $(this).removeClass('bg-danger');
                setTimeout(() => {
                    $(this).prop('disabled', false);
                }, 700);
            } else {
                $(this).addClass('bg-danger');
                $(this).html('Batal');
            }
            var tr = $(this).closest('tr');
            var row = product_table.row(tr);
            var pid = row.data().pid;
            var p_price_tag = row.data().p_price_tag;

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_p_id: pid},
                dataType: 'json',
                url: "<?php echo e(url('check_product_stock')); ?>",
                success: function (r) {
                    if (r.data != '400') {
                        var i = 0;
                        $('#ProductItemtb' + pid).after('<tr><td></td><td></td><td></td><td></td><td></td><td></td><td>Total Seluruh Harga: <input type="number" id="po_total_price' + pid + '" value="0" readonly/></td></tr>');
                        $.each($(r.data), function (key, value) {
                            $('#ProductItemtb' + pid).after('<tr data-id-' + pid + i + '><td><input type="checkbox" data-id=' + pid + ' data-index=' + i + ' id="po_check_item"/></td><td>' + value.sz_name + '</td><td>Stok Tersedia: <input type="number" value="' + value.ps_qty + '" readonly/></td><td>Order Qty:<br/><input type="number" id="po_order_qty' + pid + i + '" onchange="return checkTotalItem(' + pid + ', ' + i + ')"/></td><td>Harga Banderol: <input type="number" id="po_price_tag' + pid + '" value="' + p_price_tag + '" readonly/></td><td>Harga Beli: <input type="number" data-purchase-price=' + pid + ' id="po_purchase_price' + pid + i + '" readonly/></td><td>Total Harga: <input type="number" id="po_total_item_price' + pid + i + '" readonly/></td></tr>');
                            i++;
                        });
                    }
                }
            });
        });

        purchase_order_table.buttons().container().appendTo($('#purchase_order_excel_btn'));

        $('#purchase_order_search').on('keyup', function () {
            purchase_order_table.draw();
        });

        $('#product_search').on('keyup', function () {
            product_table.draw();
        });

        $('#br_id_filter_item').on('change', function () {
            product_table.draw();
        });

        $('#mc_id_filter_item').on('change', function () {
            product_table.draw();
        });

        $('#sz_id_filter_item').on('change', function () {
            product_table.draw();
        });

        $('#psc_id_filter_item').on('change', function () {
            product_table.draw();
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

        $('#ss_id').select2({
            width: "100%",
            dropdownParent: $('#ss_id_parent')
        });
        $('#ss_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#br_id').select2({
            width: "100%",
            dropdownParent: $('#br_id_parent')
        });
        $('#br_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_id').select2({
            width: "100%",
            dropdownParent: $('#st_id_parent')
        });
        $('#st_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_id_filter').select2({
            width: "300px",
            dropdownParent: $('#st_id_filter_parent')
        });
        $('#st_id_filter').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_id_filter').on('change', function () {
            purchase_order_table.draw();
        });

        $('#br_id_filter_item').select2({
            width: "150px",
            dropdownParent: $('#br_id_filter_parent_item')
        });
        $('#br_id_filter_item').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#sz_id_filter_item').select2({
            width: "150px",
            dropdownParent: $('#sz_id_filter_parent_item')
        });
        $('#sz_id_filter_item').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#mc_id_filter_item').select2({
            width: "150px",
            dropdownParent: $('#mc_id_filter_parent_item')
        });
        $('#mc_id_filter_item').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#psc_id_filter_item').select2({
            width: "150px",
            dropdownParent: $('#psc_id_filter_parent_item')
        });
        $('#psc_id_filter_item').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#PurchaseOrdertb tbody').on('click', 'tr', function () {
            var po_id = purchase_order_table.row(this).data().po_id;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {_po_id: po_id},
                url: "<?php echo e(url('pre_order_detail')); ?>",
                success: function (r) {
                    if (r.status == '200') {
                        global_po_id = r.po_id;
                        jQuery.noConflict();
                        $('#f_po')[0].reset();
                        $('#PurchaseOrderModal').modal('show');
                        $('#po_invoice_label').text(r.pre_order_code);
                        $('#_mode').val('edit');
                        $('#_po_id').val(r.po_id);
                        $('#po_description').val(r.po_description);
                        jQuery('#st_id').val(r.st_id).trigger('change');
                        jQuery('#ps_id').val(r.ps_id).trigger('change');
                        jQuery('#br_id').val(r.br_id).trigger('change');
                        jQuery('#ss_id').val(r.ss_id).trigger('change');
                        reloadArticleDetail(po_id);
                    } else {
                        console.log(r);
                        swal('Error', 'terjadi kesalahan', 'warning');
                    }
                }
            });
        });

        $('#add_po_btn').on('click', function () {
            $('#add_po_btn').prop('disabled', true);
            $('#purchase_order_detail_content').html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "<?php echo e(url('create_pre_order')); ?>",
                success: function (r) {
                    if (r.status == '200') {
                        jQuery.noConflict();
                        $('#PurchaseOrderModal').modal('show');
                        $('#_po_id').val(r.po_id);
                        $('#po_invoice_label').text(r.pre_order_code);
                        $('#_id').val('');
                        $('#_mode').val('add');
                        $('#f_po')[0].reset();
                        jQuery('#st_id').val('').trigger('change');
                        jQuery('#ps_id').val('').trigger('change');
                        jQuery('#br_id').val('').trigger('change');
                        jQuery('#ss_id').val('').trigger('change');
                        $('#add_po_btn').prop('disabled', false);
                    } else if (r.status == '219') {
                        jQuery.noConflict();
                        $('#f_po')[0].reset();
                        $('#PurchaseOrderModal').modal('show');
                        $('#po_invoice_label').text(r.pre_order_code);
                        $('#_mode').val('edit');
                        $('#_po_id').val(r.po_id);
                        jQuery('#st_id').val(r.st_id).trigger('change');
                        jQuery('#ps_id').val(r.ps_id).trigger('change');
                        jQuery('#br_id').val(r.br_id).trigger('change');
                        jQuery('#ss_id').val(r.ss_id).trigger('change');
                        $('#add_po_btn').prop('disabled', false);
                        reloadArticleDetail(r.po_id);
                    } else {
                        swal('Gagal', 'Gagal membuat PO', 'warning');
                    }
                }
            });
        });

        $('#add_item_btn').on('click', function () {
            $('#AddProductModal').modal('hide');
            var id = $('#_po_id').val();
            reloadArticleDetail(id);
        });

        $('#save_purchase_order_btn').on('click', function (e) {
            e.preventDefault();
            $('#PurchaseOrderModal').modal('hide');
            var po_id = $('#_po_id').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {_id: po_id},
                url: "<?php echo e(url('po_save_draft')); ?>",
                success: function (r) {
                    if (r.status == '200') {
                        //swal("Berhasil", "Data berhasil disimpan", "success");
                    } else {
                        //swal('Gagal', 'Gagal simpan data', 'error');
                    }
                }
            });
            purchase_order_table.draw(false);
        });

        $('#add_product_btn').on('click', function () {
            jQuery.noConflict();
            product_table.draw();
            $('#AddProductModal').modal('show');
        });

        $('#st_id').on('change', function () {
            var st_id = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {_po_id: $('#_po_id').val(), _st_id: st_id},
                url: "<?php echo e(url('pre_order_choose_store')); ?>",
                success: function (r) {
                    if (r.status == '200') {

                    } else {
                        //swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });

        $('#ps_id').on('change', function () {
            var ps_id = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {_po_id: $('#_po_id').val(), _ps_id: ps_id},
                url: "<?php echo e(url('pre_order_choose_product_supplier')); ?>",
                success: function (r) {
                    if (r.status == '200') {

                    } else {
                        //swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });

        $('#ss_id').on('change', function () {
            var ss_id = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {_po_id: $('#_po_id').val(), _ss_id: ss_id},
                url: "<?php echo e(url('pre_order_choose_season')); ?>",
                success: function (r) {
                    if (r.status == '200') {

                    } else {
                        //swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });

        $('#br_id').on('change', function () {
            var br_id = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {_po_id: $('#_po_id').val(), _br_id: br_id},
                url: "<?php echo e(url('pre_order_choose_brand')); ?>",
                success: function (r) {
                    if (r.status == '200') {

                    } else {
                        //swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });


        $('#cancel_purchase_order_btn').on('click', function () {
            swal({
                title: "Batal..?",
                text: "Yakin hapus PO ?",
                icon: "warning",
                buttons: [
                    'Jangan Hapus',
                    'Hapus PO'
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
                        data: {_id: $('#_po_id').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('cancel_pre_order')); ?>",
                        success: function (r) {
                            if (r.status == '200') {
                                $('#PurchaseOrderModal').modal('hide');
                                purchase_order_table.ajax.reload();
                            } else {
                                swal('Gagal', 'Gagal batalkan PO', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).ready(function () {
            $("#ImportModalBtn").click(function () {
                $("#ImportModal").modal("show");
            });
        });

        $(document).ready(function () {
            $("#UploadImageInvoiceBtn").click(function () {
                $("#UploadImageInvoiceModal").modal("show");
            });
        });

        $('#f_import').on('submit', function (e) {
            e.preventDefault();
            $('#import_data_btn').html('Proses...');
            $('#import_data_btn').attr('disabled', true);
            var formData = new FormData(this);
            var po_invoice_label = $('#po_invoice_label').text();

            formData.append('_po_invoice_label', po_invoice_label)
            $.ajax({
                type: 'POST',
                url: "<?php echo e(url('po_import')); ?>",
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
                        reloadArticleDetail(data.po_id)
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

        $('#f_upload_invoice_image').on('submit', function (e) {
            e.preventDefault();
            $('#upload_image_invoice_btn').html('Proses...');
            $('#upload_image_invoice_btn').attr('disabled', true);
            var formData = new FormData(this);
            var po_id = $('#_po_id').val();

            formData.append('_po_id', po_id)
            $.ajax({
                type: 'POST',
                url: "<?php echo e(url('po_invoice_image')); ?>",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $("#upload_image_invoice_btn").html('Upload');
                    $("#upload_image_invoice_btn").attr("disabled", false);
                    jQuery.noConflict();
                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        swal('Berhasil', 'Data berhasil diimport', 'success');
                        $('#f_upload_invoice_image')[0].reset();
                        reloadArticleDetail(po_id)
                    } else if (data.status == '400') {
                        $("#UploadImageInvoiceModal").modal('hide');
                        swal('File', 'File yang anda import kosong atau format tidak tepat', 'warning');
                    } else {
                        $("#UploadImageInvoiceModal").modal('hide');
                        swal('Gagal', 'Silahkan periksa format input pada template anda, pastikan kolom biru terisi sesuai dengan sistem', 'warning');
                    }
                },
                error: function (data) {
                    swal('Error', data, 'error');

                }
            });
        });

        $(document).delegate('#ExportArticleData', 'click', function (e) {
            e.preventDefault();
            var po_id = $('#_po_id').val();
            window.location.href = "<?php echo e(url('po_article_export')); ?>?po_id=" + po_id;
        });
    });
</script><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/pre_order/pre_order_js.blade.php ENDPATH**/ ?>