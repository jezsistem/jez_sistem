<!-- DATERANGE -->
<script src="{{ asset('app') }}/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="{{ asset('app') }}/assets/js/modal_lock.js"></script>
<script>
    function format(d) {
        var str = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;" id="ProductItemtb' + d
            .pid + '">' +
            '<tr>' +
            '<td style="white-space: nowrap;">Diskon (%) :</td>' +
            '<td><input type="number" id="po_discount' + d.pid + '" value="" onchange="return checkPurchasePrice(' + d
            .pid + ')"/></td>' +
            '</tr>' +
            '</table>';
        return str;
    }

    function checkPurchasePrice(pid)

    {
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
            data: {
                _po_id: id
            },
            url: "{{ url('check_po_detail') }}",
            success: function(r) {
                // console.log(r);
                $('#purchase_order_detail_content').html(r);
            }
        });
    }


    //penerimaan kurang ini
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
            data: {
                _poad_id: poad_id,
                _qty: qty,
                _total: total,
                _purchase_price: replaceComma(purchase_price)
            },
            dataType: 'json',
            url: "{{ url('poad_save_qty_total') }}",
            success: function(r) {
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
            text: "Yakin hapus data ini?",
            icon: "warning",
            buttons: [
                'Batalkan',
                'Hapus'
            ],
            dangerMode: true,
        }).then(function(isConfirm) {
            if (isConfirm) {
                var po_id = $('#_po_id').val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    data: {
                        _id: id
                    },
                    dataType: 'json',
                    url: "{{ url('poad_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toastr.success("Data berhasil dihapus", "Berhasil");
                            reloadArticleDetail(po_id);
                        } else {
                            toastr.error('Gagal hapus data', 'Gagal');
                        }
                    },
                    error: function() {
                        toastr.error('Terjadi kesalahan saat menghapus data', 'Error');
                    }
                });
                return false;
            }
        });
    }


    function deletePoa(id, po_id) {
        swal({
            title: "Hapus..?",
            text: "Yakin hapus data ini?",
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
                    data: {
                        _id: id
                    },
                    dataType: 'json',
                    url: "{{ url('poa_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toastr.success("Data berhasil dihapus", "Berhasil");
                            reloadArticleDetail(po_id);
                        } else {
                            toastr.error('Gagal hapus data', 'Gagal');
                        }
                    },
                    error: function() {
                        toastr.error('Terjadi kesalahan saat menghapus data', 'Error');
                    }
                });
                return false;
            }
        });
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
            data: {
                _poad_id: poad_id,
                _purchase_price: purchase_price
            },
            dataType: 'json',
            url: "{{ url('poad_save_purchase_price') }}",
            success: function(r) {
                if (r.status == '200') {
                    toast('Disimpan', 'Informasi berhasil disimpan', 'success');
                } else {
                    toast('Gagal', 'Informasi gagal disimpan', 'warning');
                }
            }
        });
    }

    //hitung cogs baru
    function discount(id) {
        var discount = $('#poa_discount' + id).val();
        var extra_discount = $('#poa_extra_discount' + id).val();
        var sub_discount = $('#poa_sub_discount' + id).val();
        var total_row = $('span[data-poa-' + id + ']').length;
        var poad_total_price = 0;
        var po_id = $('#_po_id').val();

        // Reset discount if empty or 0
        if (discount == '' || discount == 0) {
            $('#poa_extra_discount' + id).val('');
            discount = 0;
            extra_discount = 0;
        }

        // Handle extra discount if empty or 0
        if (extra_discount == '' || extra_discount == 0) extra_discount = 0;

        // Handle sub discount if empty or 0
        if (sub_discount == '' || sub_discount == 0) sub_discount = 0;

        for (let i = 0; i < total_row; ++i) {
            var price_tag = parseFloat(replaceComma($('#price_tag_' + id + '_' + i).val()));
            var qty = $('#poad_qty_' + id + '_' + i).val() || 1; // Default qty to 1 if not entered
            var subtotal = price_tag - (price_tag / 100 * parseFloat(discount));
            var total = subtotal - (subtotal / 100 * parseFloat(extra_discount));
            var final_total = total - (total / 100 * parseFloat(sub_discount));

            // Update purchase price for this row
            $('#poad_purchase_price_' + id + '_' + i).val(addCommas(final_total));

            // Calculate and update total purchase price
            var total_purchase_price = final_total * parseFloat(qty);
            $('#total_purchase_price_' + id + '_' + i).val(addCommas(total_purchase_price));

            poad_total_price += total_purchase_price;

            // Update total for this row
            poadPurchasePrice(id, i, final_total);
        }

        $('#poad_total_price_' + id).text(addCommas(poad_total_price)); // Ensure this updates correctly
        reloadPoTotalPrice(po_id)

        // Send updated discount information via AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            data: {
                _id: id,
                _discount: discount
            },
            dataType: 'json',
            url: "{{ url('poa_save_discount') }}",
            success: function(r) {
                if (r.status == '200') {
                    toast('Disimpan', 'Diskon berhasil disimpan', 'success');
                } else {
                    toast('Gagal', 'Diskon gagal disimpan', 'warning');
                }
            }
        });
    }

    function extraDiscount(id) {
        var discount = $('#poa_discount' + id).val();
        var extra_discount = $('#poa_extra_discount' + id).val();
        var sub_discount = $('#poa_sub_discount' + id).val();
        var total_row = $('span[data-poa-' + id + ']').length;
        var total = 0;
        var poad_total_price = 0;
        var po_id = $('#_po_id').val();

        if (discount == 0 || discount == null) {
            swal('Diskon', 'Diskon kosong, silahkan isi terlebih dahulu', 'warning');
            $('#poa_extra_discount' + id).val('');
        } else {
            if (extra_discount == '' || extra_discount == 0) {
                extra_discount = 0;
            }

            if (sub_discount == '' || sub_discount == 0) {
                sub_discount = 0;
            }

            for (let i = 0; i < total_row; ++i) {
                var price_tag = parseFloat(replaceComma($('#price_tag_' + id + '_' + i).val()));
                var qty = parseFloat($('#poad_qty_' + id + '_' + i).val()) || 0; // Ensure qty is a number
                var subtotal = price_tag - (price_tag / 100 * parseFloat(discount));
                var total = subtotal - (subtotal / 100 * parseFloat(extra_discount));
                var final_total = total - (total / 100 * parseFloat(sub_discount));
                $('#poad_purchase_price_' + id + '_' + i).val(addCommas(final_total));
                $('#total_purchase_price_' + id + '_' + i).val(addCommas(final_total * qty));
                poad_total_price += final_total * qty;
                poadPurchasePrice(id, i, final_total);
            }

            $('#poad_total_price_' + id).text(addCommas(poad_total_price)); // Ensure this updates correctly
            reloadPoTotalPrice(po_id)
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {
                _id: id,
                _extra_discount: extra_discount
            },
            dataType: 'json',
            url: "{{ url('poa_save_extra_discount') }}",
            success: function(r) {
                if (r.status == '200') {
                    toast('Disimpan', 'Diskon Extra berhasil disimpan', 'success');
                } else {
                    toast('Gagal', 'Diskon Extra gagal disimpan', 'warning');
                }
            }
        });
    }

    function subDiscount(id) {
        var discount = $('#poa_discount' + id).val();
        var extra_discount = $('#poa_extra_discount' + id).val();
        var sub_discount = $('#poa_sub_discount' + id).val();
        var total_row = $('span[data-poa-' + id + ']').length;
        var total = 0;
        var poad_total_price = 0;
        var po_id = $('#_po_id').val();

        if (discount == 0 || discount == null) {
            swal('Diskon', 'Diskon kosong, silahkan isi terlebih dahulu', 'warning');
            $('#poa_sub_discount' + id).val('');
        } else {
            if (sub_discount == '' || sub_discount == 0) {
                sub_discount = 0;
            }
            for (let i = 0; i < total_row; ++i) {
                var price_tag = parseFloat(replaceComma($('#price_tag_' + id + '_' + i).val()));
                var qty = parseFloat($('#poad_qty_' + id + '_' + i).val()) || 0; // Ensure qty is a number
                var subtotal = price_tag - (price_tag / 100 * parseFloat(discount));
                var subtotal_after_extra = subtotal - (subtotal / 100 * parseFloat(extra_discount));
                var total = subtotal_after_extra - (subtotal_after_extra / 100 * parseFloat(sub_discount));
                $('#poad_purchase_price_' + id + '_' + i).val(addCommas(total));
                $('#total_purchase_price_' + id + '_' + i).val(addCommas(total * qty));
                poad_total_price += total * qty;
                poadPurchasePrice(id, i, total);
            }

            $('#poad_total_price_' + id).text(addCommas(poad_total_price)); // Ensure this updates correctly
            reloadPoTotalPrice(po_id)
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {
                _id: id,
                _sub_discount: sub_discount
            },
            dataType: 'json',
            url: "{{ url('poa_save_sub_discount') }}",
            success: function(r) {
                if (r.status == '200') {
                    toast('Disimpan', 'Sub Diskon berhasil disimpan', 'success');
                } else {
                    toast('Gagal', 'Sub Diskon gagal disimpan', 'warning');
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
    //         url: "{{ url('poa_save_extra_discount') }}",
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
            data: {
                _po_id: id
            },
            url: "{{ url('reload_po_detail') }}",
            success: function(r) {
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

        // If qty is empty, set to 0
        if (qty == '') {
            qty = 0;
        }

        // Use the purchase price if it is available, otherwise fallback to price tag
        if (purchase_price != '') {
            total = parseFloat(qty) * parseFloat(replaceComma(purchase_price));
        } else {
            total = parseFloat(qty) * parseFloat(replaceComma(price_tag));
        }

        // Set total purchase price for this row
        $('#total_purchase_price_' + id + '_' + index).val(addCommas(total));

        // Recalculate the total price for all rows
        for (let i = 0; i < total_row; ++i) {
            total_price += parseFloat(replaceComma($('#total_purchase_price_' + id + '_' + i).val()));
        }

        // Update the total price on the page
        $('#poad_total_price_' + id).text(addCommas(total_price));

        // Send updated qty and total price via AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            data: {
                _poad_id: poad_id,
                _qty: qty,
                _total: total,
                _purchase_price: replaceComma(purchase_price)
            },
            dataType: 'json',
            url: "{{ url('poad_save_qty_total') }}",
            success: function(r) {
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
        }).then(function(isConfirm) {
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
            data: {
                _id: id,
                _reminder: reminder
            },
            dataType: 'json',
            url: "{{ url('poa_save_reminder') }}",
            success: function(r) {
                if (r.status == '200') {
                    toast('Disimpan', 'Informasi berhasil disimpan', 'success');
                } else {
                    toast('Gagal', 'Informasi gagal disimpan', 'warning');
                }
            }
        });
    }

    function changeFinanceStatus(po_id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {
                _po_id: po_id,
            },
            dataType: 'json',
            url: "{{ url('po_change_finance_status') }}",
            success: function(r) {
                if (r.status == '200') {
                    toast('Disimpan', 'Informasi berhasil disimpan', 'success');
                } else {
                    toast('Gagal', 'Informasi gagal disimpan', 'warning');
                }
            }
        });
    }

    function calcRemainingPayment(total_po_with_item) {

        var total_po_without_item = $('#total_purchase').val();
        var total_payment = $('#payment_amount').val();
        var claim_amount = $('#claim_amount').val() || 0;

        console.log(total_po_without_item, total_po_with_item, total_payment, claim_amount);

        var total_po = (parseFloat(total_po_with_item) || 0) > 0 ? parseFloat(total_po_with_item) : parseFloat(
            total_po_without_item) || 0;
        var sisa_payment = (parseFloat(total_payment) + parseFloat(claim_amount) - parseFloat(total_po));

        $('#remaining_payment').val(sisa_payment);
    }

    // CALCULATION

    $(document).delegate('#po_check_item', 'click', function() {
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

    $(document).delegate('#checkbox_add_item', 'click', function() {
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
            data: {
                _poid: poid,
                _pid: pid,
                _psid: psid,
                _status: status
            },
            url: "{{ url('create_po_detail') }}",
            success: function(r) {
                if (r.status == '200') {

                } else {
                    swal('Sudah ada', 'Produk sudah ada pada list PO', 'warning');
                    $('.checkbox_add_item' + pid + '_' + index).prop('checked', true);
                    $('.checkbox_add_item' + pid + '_' + index).prop('disabled', true);
                }
            }
        });
    });

    $(document).delegate('#pay_date', 'change', function() {
        var pay_date = $(this).val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{ url('po_change_pay_date') }}",
            data: {
                pay_date: pay_date,
                po_id: $('#_po_id').val()
            },
            success: function(r) {
                let response = typeof r === "string" ? JSON.parse(r) : r;
                if (response.status == '200') {} else if (response.status == '500') {
                    swal('Error', response.message);
                } else {

                }
            },
        });
    });

    $(document).delegate('#due_date', 'change', function() {
        var due_date = $(this).val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{ url('po_change_due_date') }}",
            data: {
                due_date: due_date,
                po_id: $('#_po_id').val()
            },
            success: function(r) {
                let response = typeof r === "string" ? JSON.parse(r) : r;
                if (response.status == '200') {

                } else {

                }
            },
        });
    });

    $('#status_dispute').on('change', function() {
        var status_disputeValue = $(this).val();
        var no_order = $('#po_invoice_label').text();

        if (status_disputeValue === "") {
            return;
        }

        $.ajax({
            url: "{{ url('status_dispute_save') }}",
            type: 'POST',
            data: {
                status_dispute: status_disputeValue,
                po_invoice: no_order,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log(response);
                toastr.success("Status Dispute berhasil disimpan", "Berhasil");
            },
            error: function(xhr) {
                console.error(xhr);
                toastr.error("Gagal menyimpan Status Dispute", "Gagal");
            }
        });
    });

    $(document).delegate('#bank_general', 'change', function() {
        var bg_id = $(this).val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{ url('po_change_bank_general') }}",
            data: {
                bg_id: bg_id,
                po_id: $('#_po_id').val()
            },
            success: function(r) {
                let response = typeof r === "string" ? JSON.parse(r) : r;
                if (response.status == '200') {

                } else if (response.status == '500') {
                    swal('Error', response.message);
                } else {

                }
            },
        });
    });

    $(document).ready(function() {
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
            responsive: false,
            dom: '<"text-right"l>Brt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs',
                "exportOptions": {
                    orthogonal: 'export'
                }
            }],
            ajax: {
                url: "{{ url('purchase_order_datatables') }}",
                data: function(d) {
                    d.search = $('#purchase_order_search').val();
                    d.st_id = $('#st_id_filter').val();
                    d.date = $('#po_date').val();
                    d.status_purchase = $('#status_purchase').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'po_id',
                    searchable: false
                },
                {
                    data: 'po_created_at_show',
                    name: 'po_created_at'
                },
                {
                    data: 'st_name',
                    name: 'st_name'
                },
                {
                    data: 'ps_name',
                    name: 'ps_name'
                },
                {
                    data: 'po_invoice',
                    name: 'po_invoice'
                },
                {
                    data: 'po_total',
                    name: 'po_total',
                    render: function(data, type, row) {
                        return type === 'export' ?
                            data.replace(/[$,]/g, '') :
                            data;
                    }
                },
                {
                    data: 'po_status',
                    name: 'po_status'
                },
                {
                    data: 'finance_status',
                    name: 'finance_status'
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [
                [0, 'desc']
            ],
            rowCallback: function(row, data, index) {
                if (data.is_no_item == true) {
                    $(row).css('background-color', '#f8d7da');
                }
            }
        });

        var product_table = $('#Producttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('product_item_datatables') }}",
                data: function(d) {
                    d.search = $('#product_search').val();
                    // d.ps_id = $('#ps_id').val();
                    d.br_id_filter = $('#br_id_filter_item').val();
                    d.mc_id_filter = $('#mc_id_filter_item').val();
                    d.psc_id_filter = $('#psc_id_filter_item').val();
                    d.sz_id_filter = $('#sz_id_filter_item').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'pid',
                    searchable: false
                },
                {
                    data: 'p_name_show',
                    name: 'p_name'
                },
                {
                    data: 'article_id',
                    name: 'article_id'
                },
                {
                    data: 'p_color_show',
                    name: 'p_color'
                },
                {
                    data: 'br_name',
                    name: 'br_name'
                },
                {
                    data: 'p_action',
                    name: 'p_action',
                    sortable: false
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [
                [0, 'desc']
            ],
        });

        var purchaseOrderInvoiceTable = $('#InvoiceImagesTb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            ajax: {
                url: "{{ url('po_invoice_image_datatable') }}",
                data: function(d) {
                    // console.log(d); 
                    d._po_id = $('#_po_id').val();
                },
            },

            columns: [{
                    data: 'image',
                    name: 'invoice_image',
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            columnDefs: [{
                "targets": [0, 1],
                "className": "text-center",
                "width": "0%"
            }],
            order: [
                [0, 'desc']
            ],
        });

        var purchaseOrderBuktitfTable = $('#BuktitfImagesTb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            ajax: {
                url: "{{ url('po_transfer_image_datatable') }}",
                data: function(d) {
                    d._po_id = $('#_po_id').val();
                },
            },

            columns: [{
                    data: 'image',
                    name: 'transfer_image',
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            columnDefs: [{
                "targets": [0, 1],
                "className": "text-center",
                "width": "0%"
            }],
            order: [
                [0, 'desc']
            ],
        });

        var purchaseOrderLogTable = $('#PurchaseOrderLogTb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            deferLoading: 0,
            dom: 'rt<"text-right"ip>',
            ajax: {
                url: "{{ url('po_log_datatables') }}",
                data: function(d) {
                    d.po_id = $('#_po_id').val();
                },
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'u_name',
                    name: 'u_name'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'item_detail',
                    name: 'item_detail',
                    render: function(data, type, row) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'target_column',
                    name: 'target_column'
                },
                {
                    data: 'before',
                    name: 'before',
                    render: function(data, type, row) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'after',
                    name: 'after',
                    render: function(data, type, row) {
                        return data ? data : '-';
                    }
                }
            ],
            columnDefs: [{
                "targets": '_all',
                "className": "text-center"
            }],
            order: [
                [1, 'desc']
            ],
            pageLength: 25
        });

        $('#ChangeLogBtn').on('click', function() {
            purchaseOrderLogTable.draw();
            jQuery.noConflict();
            $('#ChangeLogModal').modal('show');
        });

        $('#BuktitfImagesTb tbody').on('click', '#delete-image-transfer', function() {
            var id = $(this).data('id');

            if (!id) {
                toastr.error('ID tidak ditemukan', 'Error');
                return;
            }

            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Hapus'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url: "{{ url('po_transfer_image_delete') }}",
                        data: {
                            id: id
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function(r) {
                            if (r.status === '200') {
                                toastr.success("Data berhasil dihapus", "Berhasil");
                                purchaseOrderBuktitfTable.draw();
                            } else {
                                toastr.error(r.message || 'Gagal hapus data',
                                    'Gagal');
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error('Terjadi kesalahan saat menghapus data: ' +
                                error, 'Error');
                        }
                    });
                }
            });
        });

        $('#InvoiceImagesTb tbody').on('click', '#delete-image-invoice', function() {
            var id = $(this).data('id');

            if (!id) {
                toastr.error('ID tidak ditemukan', 'Error');
                return;
            }

            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Hapus'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url: "{{ url('po_invoice_image_delete') }}",
                        data: {
                            id: id
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function(r) {
                            if (r.status === '200') {
                                toastr.success("Data berhasil dihapus", "Berhasil");
                                purchaseOrderInvoiceTable.draw();
                            } else {
                                toastr.error(r.message || 'Gagal hapus data',
                                    'Gagal');
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error('Terjadi kesalahan saat menghapus data: ' +
                                error, 'Error');
                        }
                    });
                }
            });
        });

        $('#Producttb tbody').on('click', '#add_product_size_btn', function() {
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
                data: {
                    _p_id: pid
                },
                dataType: 'json',
                url: "{{ url('check_product_stock') }}",
                success: function(r) {
                    if (r.data != '400') {
                        var i = 0;
                        $('#ProductItemtb' + pid).after(
                            '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td>Total Seluruh Harga: <input type="number" id="po_total_price' +
                            pid + '" value="0" readonly/></td></tr>');
                        $.each($(r.data), function(key, value) {
                            $('#ProductItemtb' + pid).after('<tr data-id-' + pid +
                                i + '><td><input type="checkbox" data-id=' +
                                pid + ' data-index=' + i +
                                ' id="po_check_item"/></td><td>' + value
                                .sz_name +
                                '</td><td>Stok Tersedia: <input type="number" value="' +
                                value.ps_qty +
                                '" readonly/></td><td>Order Qty:<br/><input type="number" id="po_order_qty' +
                                pid + i + '" onchange="return checkTotalItem(' +
                                pid + ', ' + i +
                                ')"/></td><td>Harga Banderol: <input type="number" id="po_price_tag' +
                                pid + '" value="' + p_price_tag +
                                '" readonly/></td><td>Harga Beli: <input type="number" data-purchase-price=' +
                                pid + ' id="po_purchase_price' + pid + i +
                                '" readonly/></td><td>Total Harga: <input type="number" id="po_total_item_price' +
                                pid + i + '" readonly/></td></tr>');
                            i++;
                        });
                    }
                }
            });
        });

        purchase_order_table.buttons().container().appendTo($('#purchase_order_excel_btn'));

        $('#purchase_order_search').on('keyup', function() {
            purchase_order_table.draw();
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

        $('#psc_id_filter_item').on('change', function() {
            product_table.draw();
        });

        $('#ps_id').select2({
            width: "100%",
            dropdownParent: $('#ps_id_parent')
        });
        $('#ps_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#stkt_id').select2({
            width: "100%",
            dropdownParent: $('#stkt_id_parent')
        });
        $('#stkt_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#tax_id').select2({
            width: "100%",
            dropdownParent: $('#tax_id_parent')
        });
        $('#tax_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#dp_id').select2({
            width: "100%",
            dropdownParent: $('#dp_id_parent')
        });
        $('#dp_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#acc_id').select2({
            width: "100%",
            dropdownParent: $('#acc_id_parent')
        });
        $('#acc_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_id').select2({
            width: "100%",
            dropdownParent: $('#st_id_parent')
        });
        $('#st_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pro_id').select2({
            width: "100%",
            dropdownParent: $('#pro_id_parent')
        });
        $('#pro_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_id_filter').select2({
            width: "300px",
            dropdownParent: $('#st_id_filter_parent')
        });
        $('#st_id_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_id_filter').on('change', function() {
            purchase_order_table.draw();
        });

        $('#status_purchase').select2({
            width: "200px",
            dropdownParent: $('#status_purchase_parent')
        });

        $('#status_purchase').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#status_purchase').on('change', function() {
            purchase_order_table.draw();
        });

        $('#br_id_filter_item').select2({
            width: "150px",
            dropdownParent: $('#br_id_filter_parent_item')
        });
        $('#br_id_filter_item').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#sz_id_filter_item').select2({
            width: "150px",
            dropdownParent: $('#sz_id_filter_parent_item')
        });
        $('#sz_id_filter_item').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#mc_id_filter_item').select2({
            width: "150px",
            dropdownParent: $('#mc_id_filter_parent_item')
        });
        $('#mc_id_filter_item').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#psc_id_filter_item').select2({
            width: "150px",
            dropdownParent: $('#psc_id_filter_parent_item')
        });
        $('#psc_id_filter_item').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });


        $('#PurchaseOrdertb tbody').on('click', 'tr', async function() {
            var po_id = purchase_order_table.row(this).data().po_id;

            // Coba dapatkan lock sebelum buka modal
            const lockResult = await openEditModal('purchase_order', po_id, 'pembelian');
            if (lockResult === false) {
                return;
            }

            // Mulai interval untuk extend lock setiap 60 detik
            if (window.lockExtendInterval) clearInterval(window.lockExtendInterval);
            window.lockExtendInterval = setInterval(function() {
                extendLock('purchase_order', po_id, 'pembelian');
            }, 60000);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: po_id
                },
                url: "{{ url('po_detail') }}",
                success: function(r) {
                    if (r.status == '200') {
                        global_po_id = r.po_id;
                        let dispute_text = '';
                        jQuery.noConflict();
                        if (r.dispute == 1) {
                            dispute_text = 'Yes';
                        } else {
                            dispute_text = 'No';
                        }
                        $('#f_po')[0].reset();
                        $('#PurchaseOrderModal').modal('show');
                        $('#po_invoice_label').text(r.po_invoice);
                        $('#_mode').val('edit');
                        $('#_po_id').val(r.po_id);
                        $('#po_description').val(r.po_description);
                        $('#shipping_cost').val(r.shipping_cost);
                        $('#dispute').val(dispute_text);
                        $('#dispute_description').val(r.po_dispute_description);
                        jQuery('#st_id').val(r.st_id).trigger('change');
                        jQuery('#ps_id').val(r.ps_id).trigger('change');
                        jQuery('#stkt_id').val(r.stkt_id).trigger('change');
                        jQuery('#pay_date').val(r.pay_date).trigger('change');
                        jQuery('#due_date').val(r.due_date).trigger('change');
                        jQuery('#tax_id').val(r.tax_id).trigger('change');
                        jQuery('#dp_id').val(r.dp_id).trigger('change');
                        jQuery('#acc_id').val(r.acc_id).trigger('change');
                        jQuery('#bank_general').val(r.bank_general).trigger('change');
                        $('#status_dispute').val(r.status_dispute);
                        $('#total_purchase').val(r.po_total_purchase);
                        $('#payment_amount').val(r.po_payment_amount);
                        $('#total_qty').val(r.po_total_qty);
                        jQuery('#is_receivable').val(r.is_receivable);
                        jQuery('#claim_amount').val(r.claim_amount);
                        reloadArticleDetail(po_id);
                        calcRemainingPayment(r.total_po);
                    } else {
                        swal('Error', 'terjadi kesalahan', 'warning');
                    }
                }
            });
        });

        $(document).ready(function() {
            $("#InvoiceImagesBtn").click(function() {
                $("#InvoiceImagesModal").modal("show");
                purchaseOrderInvoiceTable.draw();
                console.log($('#po_id').val());
            });
        });

        $(document).ready(function() {
            $("#BuktitfImagesBtn").click(function() {
                $("#BuktitfImagesModal").modal("show");
                purchaseOrderBuktitfTable.draw();
                console.log($('#po_id').val());
            });
        });

        $('.add_po_btn').on('click', function() {
            type = $(this).data('type');
            if (type == 'with_item') {

            } else if (type == 'without_item') {
                $('#detail_po').addClass('d-none');
                $('.without_item_input').removeClass('d-none');
            }
            $('.add_po_btn').prop('disabled', true);
            $('#purchase_order_detail_content').html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _type: type
                },
                url: "{{ url('create_po') }}",
                success: function(r) {
                    if (r.status == '200') {
                        jQuery.noConflict();
                        $('#PurchaseOrderModal').modal('show');
                        $('#_po_id').val(r.po_id);
                        $('#po_invoice_label').text(r.po_invoice);
                        $('#_id').val('');
                        $('#_mode').val('add');
                        $('#f_po')[0].reset();
                        jQuery('#st_id').val('').trigger('change');
                        jQuery('#ps_id').val('').trigger('change');
                        jQuery('#stkt_id').val('').trigger('change');
                        jQuery('#tax_id').val('').trigger('change');
                        jQuery('#dp_id').val('').trigger('change');
                        jQuery('#acc_id').val('').trigger('change');
                        jQuery('#total_purchase').val('');
                        jQuery('#payment_amount').val('');
                        jQuery('#total_qty').val('');
                        $('.add_po_btn').prop('disabled', false);
                    } else if (r.status == '219') {
                        jQuery.noConflict();
                        $('#f_po')[0].reset();
                        $('#PurchaseOrderModal').modal('show');
                        $('#po_invoice_label').text(r.po_invoice);
                        $('#_mode').val('edit');
                        $('#_po_id').val(r.po_id);
                        jQuery('#st_id').val(r.st_id).trigger('change');
                        jQuery('#ps_id').val(r.ps_id).trigger('change');
                        jQuery('#stkt_id').val(r.stkt_id).trigger('change');
                        $('.add_po_btn').prop('disabled', false);
                        reloadArticleDetail(r.po_id);
                    } else {
                        swal('Gagal', 'Gagal membuat PO', 'warning');
                    }
                }
            });
        });

        $('#add_item_btn').on('click', function() {
            $('#AddProductModal').modal('hide');
            var id = $('#_po_id').val();
            reloadArticleDetail(id);
        });

        function checkRequiredSelects() {
            let tax_id = $('#tax_id').val();
            let dp_id = $('#dp_id').val();
            let acc_id = $('#acc_id').val();

            if (tax_id && dp_id && acc_id) {
                $('#save_purchase_order_btn').prop('disabled', false);
            } else {
                $('#save_purchase_order_btn').prop('disabled', true);
            }
        }

        // panggil saat ganti select
        $('#tax_id, #dp_id, #acc_id').on('change', checkRequiredSelects);

        // panggil awal
        checkRequiredSelects();


        // $('#save_purchase_order_btn').on('click', function(e) {
        //     e.preventDefault();
        //     // alert("Modal ditutup, melepaskan lock...");
        //     var po_id = $('#_po_id').val();
        //     if (po_id) {
        //         closeEditModal('purchase_order', po_id, 'pembelian');
        //     }
        //     // Hentikan interval extend lock
        //     if (window.lockExtendInterval) {
        //         clearInterval(window.lockExtendInterval);
        //         window.lockExtendInterval = null;
        //     }
        //     $('#PurchaseOrderModal').modal('hide');
        //     var po_id = $('#_po_id').val();
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });
        //     $.ajax({
        //         type: "POST",
        //         dataType: 'json',
        //         data: {
        //             _id: po_id
        //         },
        //         url: "{{ url('po_save_draft') }}",
        //         success: function(r) {
        //             if (r.status == '200') {
        //                 //swal("Berhasil", "Data berhasil disimpan", "success");
        //             } else {
        //                 //swal('Gagal', 'Gagal simpan data', 'error');
        //             }
        //         }
        //     });
        //     purchase_order_table.draw(false);

        //     $('#detail_po').removeClass('d-none');
        // });

        $('.close_modal_po').on('click', function(e) {
            e.preventDefault();
            // alert("Modal ditutup, melepaskan lock...");
            var po_id = $('#_po_id').val();
            if (po_id) {
                closeEditModal('purchase_order', po_id, 'pembelian');
            }
            // Hentikan interval extend lock
            if (window.lockExtendInterval) {
                clearInterval(window.lockExtendInterval);
                window.lockExtendInterval = null;
            }
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
                data: {
                    _id: po_id
                },
                url: "{{ url('po_save_draft') }}",
                success: function(r) {
                    if (r.status == '200') {
                        //swal("Berhasil", "Data berhasil disimpan", "success");
                    } else {
                        //swal('Gagal', 'Gagal simpan data', 'error');
                    }
                }
            });
            changeFinanceStatus(po_id);
            purchase_order_table.draw(false);

            $('#detail_po').removeClass('d-none');
        });

        $('#add_product_btn').on('click', function() {
            jQuery.noConflict();
            product_table.draw();
            $('#AddProductModal').modal('show');
        });

        $('#st_id').on('change', function() {
            var st_id = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: $('#_po_id').val(),
                    _st_id: st_id
                },
                url: "{{ url('po_choose_store') }}",
                success: function(r) {
                    if (r.status == '200') {

                    } else {
                        //swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });

        $('#ps_id').on('change', function() {
            var ps_id = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: $('#_po_id').val(),
                    _ps_id: ps_id
                },
                url: "{{ url('po_choose_supplier') }}",
                success: function(r) {
                    if (r.status == '200') {

                    } else {
                        //swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });

        $('#stkt_id').on('change', function() {
            var stkt_id = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: $('#_po_id').val(),
                    _stkt_id: stkt_id
                },
                url: "{{ url('po_choose_stock_type') }}",
                success: function(r) {
                    if (r.status == '200') {

                    } else {
                        //swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });

        $('#tax_id').on('change', function() {
            var tax_id = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: $('#_po_id').val(),
                    _tax_id: tax_id
                },
                url: "{{ url('po_choose_tax') }}",
                success: function(r) {
                    if (r.status == '200') {

                    } else {
                        //swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });

        $('#dp_id').on('change', function() {
            var dp_id = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: $('#_po_id').val(),
                    _dp_id: dp_id
                },
                url: "{{ url('po_choose_data_perusahaan') }}",
                success: function(r) {
                    if (r.status == '200') {

                    } else {
                        //swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });

        $('#acc_id').on('change', function() {
            var acc_id = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: $('#_po_id').val(),
                    _acc_id: acc_id
                },
                url: "{{ url('po_choose_payment') }}",
                success: function(r) {
                    if (r.status == '200') {

                    } else {
                        //swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });

        $('#total_purchase').on('change', function() {
            var total_purchase = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: $('#_po_id').val(),
                    _total_purchase: total_purchase
                },
                url: "{{ url('po_total_purchase') }}",
                success: function(r) {
                    if (r.status == '200') {
                        var total_po = $('#poad_total_price').text().replace(/Rp\.\s*/g, '')
                            .replace(/\./g, '').replace(/,/g, '');
                        calcRemainingPayment(total_po);

                    } else {
                        //swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });

        $('#payment_amount').on('change', function() {
            var payment_amount = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: $('#_po_id').val(),
                    _payment_amount: payment_amount
                },
                url: "{{ url('po_payment_amount') }}",
                success: function(r) {
                    if (r.status == '200') {
                        toastr.success("Jumlah pembayaran berhasil di Update", "Success");
                        var total_po = $('#poad_total_price').text().replace(/Rp\.\s*/g, '')
                            .replace(/\./g, '').replace(/,/g, '');
                        calcRemainingPayment(total_po);
                    } else {}
                }
            });
        });

        $('#total_qty').on('change', function() {
            var total_qty = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: $('#_po_id').val(),
                    _total_qty: total_qty
                },
                url: "{{ url('po_total_qty') }}",
                success: function(r) {
                    if (r.status == '200') {

                    } else {
                        swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });


        $('#po_description').on('change', function() {
            var po_description = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: $('#_po_id').val(),
                    _po_description: po_description
                },
                url: "{{ url('po_description') }}",
                success: function(r) {
                    if (r.status == '200') {

                    } else {
                        swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });

        $('#shipping_cost').on('change', function() {
            var shipping_cost = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: $('#_po_id').val(),
                    _po_shipping_cost: shipping_cost
                },
                url: "{{ url('po_shipping_cost') }}",
                success: function(r) {
                    if (r.status == '200') {

                    } else {
                        swal('Gagal', 'Gagal mengubah data store', 'warning');
                    }
                }
            });
        });

        $('#pro_id').on('change', function() {
            var pro_id = $(this).val();
            var po_id = $('#_po_id').val();

            if (pro_id == '') {
                return
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            console.log([
                'po_id: ' + po_id,
                'pro_id: ' + pro_id
            ])
            // Menampilkan swal sebelum mengirim permintaan AJAX
            swal({
                title: "Apakah Anda yakin?",
                text: "Anda akan mengirim permintaan untuk memeriksa pre-order dan purchase order.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willSend) => {
                if (willSend) {
                    $.ajax({
                        type: "POST",
                        dataType: 'json',
                        data: {
                            _po_id: po_id,
                            _pro_id: pro_id
                        },
                        url: "{{ url('check_pre_order_purchase_order') }}",
                        success: function(r) {
                            // console.log(r);
                            if (r.status == '200') {
                                swal("Berhasil!", "Data berhasil diubah.",
                                    "success");
                                jQuery('#pro_id').val('').trigger('change');
                                reloadArticleDetail(po_id);
                            } else {
                                swal('Gagal', 'Gagal mengubah data store',
                                    'warning');
                            }
                        },
                        error: function() {
                            swal('Gagal',
                                'Terjadi kesalahan saat mengirim permintaan.',
                                'error');
                        }
                    });
                } else {
                    swal("Dibatalkan", "Aksi dibatalkan oleh pengguna.", "info");
                }
            });
        });



        $('#cancel_purchase_order_btn').on('click', function() {
            swal({
                title: "Batal..?",
                text: "Yakin hapus PO ?",
                icon: "warning",
                buttons: [
                    'Jangan Hapus',
                    'Hapus PO'
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
                        data: {
                            _id: $('#_po_id').val()
                        },
                        dataType: 'json',
                        url: "{{ url('cancel_po') }}",
                        success: function(r) {
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

        $(document).ready(function() {
            $("#ImportModalBtn").click(function() {
                $("#ImportModal").modal("show");
            });
        });

        $(document).ready(function() {
            $("#UploadImageInvoiceBtn").click(function() {
                $("#UploadImageInvoiceModal").modal("show");
            });
        });

        $(document).ready(function() {
            $("#UploadImageTransferBtn").click(function() {
                $("#UploadImageTransferModal").modal("show");
            });
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $('#import_data_btn').html('Proses...');
            $('#import_data_btn').attr('disabled', true);
            var formData = new FormData(this);
            var po_invoice_label = $('#po_invoice_label').text();
            var po_id = $('#_po_id').val();

            formData.append('_po_invoice_label', po_invoice_label);

            $.ajax({
                type: 'POST',
                url: "{{ url('po_import') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();
                    $("#ImportModal").modal('hide');
                    console.log(data.status);

                    if (data.status == '200') {
                        toastr.success('Data berhasil diimport', 'Berhasil');
                        $('#f_import')[0].reset();

                        reloadArticleDetail(data.po_id);
                    } else if (data.status == '400') {
                        toastr.warning(
                            'File yang anda import kosong atau format tidak tepat',
                            'File');
                    } else if (data.status == '404') {
                        checkBarcodeImport(po_id, data.process_data.not_found, 'not_found');
                    } else if (data.status == '403') {
                        checkBarcodeImport(po_id, data.process_data.duplicate_items,
                            'duplicate_items');
                    } else {
                        toastr.warning(
                            'Silahkan periksa format input pada template anda, pastikan kolom biru terisi sesuai dengan sistem',
                            'Gagal');
                    }
                },
                error: function(data) {
                    toastr.error('Terjadi kesalahan saat mengimport data', 'Error');
                }
            });
        });

        function checkBarcodeImport(id, excelData, title) {
            excelData = Array.isArray(excelData) ? excelData : Object.values(excelData);

            if (title == 'duplicate_items') {
                swal_title = 'Duplicate Barcode';

            } else if (title == 'not_found') {
                swal_title = 'Barcode Not Found';
            }

            // Check if excelData is not empty and is an array
            if (excelData && Array.isArray(excelData) && excelData.length > 0) {
                // Format data into a table structure
                let tableContent = `
            <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                <th style="border: 1px solid #dddddd; padding: 8px;">SKU</th>
                <th style="border: 1px solid #dddddd; padding: 8px;">Qty</th>
                </tr>
            </thead>
            <tbody>`;

                // Iterate over the excelData array to build rows for the table
                excelData.forEach(item => {
                    tableContent += `
            <tr>
                <td style="border: 1px solid #dddddd; padding: 8px;">${item.sku}</td>
                <td style="border: 1px solid #dddddd; padding: 8px;">${item.qty}</td>
            </tr>`;
                });

                tableContent += `</tbody></table>`;

                swal({
                    title: swal_title,
                    content: {
                        element: "div",
                        attributes: {
                            innerHTML: tableContent
                        },
                    },
                    icon: "warning",
                    buttons: {
                        export: {
                            text: "Export to Excel",
                            value: "export",
                        },
                        ok: {
                            text: "OK",
                            value: true,
                        }
                    }
                }).then((value) => {
                    if (value === "export") {
                        exportToExcel(excelData, swal_title); // Call function to export data to Excel
                    }
                });
            }
        }

        // Function to export the data to Excel
        function exportToExcel(data, title) {
            var po_invoice_label = $('#po_invoice_label').text();
            let formattedData = data.map(item => ({
                SKU: item.sku,
                Quantity: item.qty
            }));

            console.log(formattedData);

            let worksheet = XLSX.utils.json_to_sheet(formattedData);
            let workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, title);

            // Trigger download
            XLSX.writeFile(workbook, `${title}_${po_invoice_label}.xlsx`);
        }

        $('#f_upload_invoice_image').on('submit', function(e) {
            e.preventDefault();
            $('#upload_image_invoice_btn').html('Proses...');
            $('#upload_image_invoice_btn').attr('disabled', true);
            var formData = new FormData(this);
            var po_id = $('#_po_id').val();

            formData.append('_po_id', po_id);
            $.ajax({
                type: 'POST',
                url: "{{ url('po_invoice_image') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#upload_image_invoice_btn").html('Upload');
                    $("#upload_image_invoice_btn").attr("disabled", false);
                    jQuery.noConflict();
                    $("#UploadImageInvoiceModal").modal('hide');

                    if (data.status == '200') {
                        toastr.success('Data berhasil diimport', 'Berhasil');
                        $('#f_upload_invoice_image')[0].reset();
                        reloadArticleDetail(po_id);
                    } else if (data.status == '400') {
                        toastr.warning(
                            'File yang anda import kosong atau format tidak tepat',
                            'File');
                    } else {
                        toastr.warning(
                            'Silahkan periksa format input pada template anda, pastikan kolom biru terisi sesuai dengan sistem',
                            'Gagal');
                    }
                },
                error: function(data) {
                    toastr.error('Terjadi kesalahan saat mengupload data', 'Error');
                }
            });
        });

        $('#f_upload_transfer_image').on('submit', function(e) {
            e.preventDefault();
            $('#upload_image_transfer_btn').html('Proses...');
            $('#upload_image_transfer_btn').attr('disabled', true);
            var formData = new FormData(this);
            var po_id = $('#_po_id').val();
            formData.append('_po_id', po_id);

            $.ajax({
                type: 'POST',
                url: "{{ url('po_transfer_image') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#upload_image_transfer_btn").html('Upload');
                    $("#upload_image_transfer_btn").attr("disabled", false);
                    jQuery.noConflict();
                    $("#UploadImageTransferModal").modal('hide');

                    if (data.status == '200') {
                        toastr.success('Data berhasil diimport', 'Berhasil');
                        $('#f_upload_transfer_image')[0].reset();
                        reloadArticleDetail(po_id);
                    } else if (data.status == '400') {
                        toastr.warning(
                            'File yang anda import kosong atau format tidak tepat',
                            'File');
                    } else {
                        toastr.warning(
                            'Silahkan periksa format input pada template anda, pastikan kolom biru terisi sesuai dengan sistem',
                            'Gagal');
                    }
                },
                error: function(data) {
                    toastr.error('Terjadi kesalahan saat mengupload data', 'Error');
                }
            });
        });

        $('#is_receivable').on('change', function() {
            var is_receivable = $(this).val();
            var po_id = $('#_po_id').val();

            if (is_receivable === '') {
                return;
            }


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: po_id,
                    _is_receivable: is_receivable
                },
                url: "{{ url('po_change_is_receivable') }}",

                success: function(response) {
                    console.log(response);
                    toastr.success("Status Receivable berhasil disimpan", "Berhasil");
                },
                error: function(xhr) {
                    console.error(xhr);
                    toastr.error("Gagal menyimpan Status Receivable", "Gagal");
                }
            });
        });

        $('#claim_amount').on('change', function() {
            var claim_amount = $(this).val();
            var po_id = $('#_po_id').val();

            if (claim_amount === '') {
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    _po_id: po_id,
                    _claim_amount: claim_amount
                },
                url: "{{ url('po_change_claim_amount') }}",
                success: function(response) {
                    console.log(response);
                    toastr.success("Claim Amount berhasil disimpan", "Berhasil");
                    var total_po = $('#poad_total_price').text().replace(/Rp\.\s*/g, '')
                        .replace(/\./g, '').replace(/,/g, '');
                    calcRemainingPayment(total_po);
                },
                error: function(xhr) {
                    console.error(xhr);
                    toastr.error("Gagal menyimpan Claim Amount", "Gagal");
                }
            });
        });


        $(document).delegate('#ExportArticleData', 'click', function(e) {
            e.preventDefault();
            var po_id = $('#_po_id').val();
            var st_id = $('#st_id').val();
            {{-- window.location.href = "{{ url('po_article_export') }}?po_id="+po_id; --}}
            window.location.href = "{{ url('po_article_export') }}?po_id=" + po_id + "&st_id=" + st_id;
        });

        jQuery.noConflict();
        var picker = $('#kt_dashboard_daterangepicker');
        if ($('#kt_dashboard_daterangepicker').length == 0) {
            return;
        }
        var start = moment();
        var end = moment();

        function cb(start, end, label) {
            var title = '';
            var range = '';
            var hidden_range = '';

            if ((end - start) < 100 || label == 'Today') {
                title = 'Today:';
                range = start.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD');
            } else if (label == 'Yesterday') {
                title = 'Yesterday:';
                range = start.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD');
            } else if (label == 'All Days') {
                title = 'All Days';
                hidden_range = '';
            } else {
                range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }
            console.log(hidden_range);
            $('#po_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);

            purchase_order_table.draw();
        }

        picker.daterangepicker({
            direction: KTUtil.isRTL(),
            startDate: start,
            endDate: end,
            opens: 'left',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            ranges: {
                'All Days': [null, null],
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            }
        }, cb);
        cb(start, end, '');
    });
</script>
