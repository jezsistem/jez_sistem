<script src="{{ asset('pos/js') }}/plugin.bundle.min.js"></script>
<script src="{{ asset('pos/js') }}/bootstrap.bundle.min.js"></script>
<script src="{{ asset('pos/js') }}/jquery.dataTables.min.js"></script>
<script src="{{ asset('pos/js') }}/multiple-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('pos/js') }}/script.bundle.js"></script>
<script src="{{ asset('cdn/jquery.toast.min.js') }}"></script>
<script src="{{ asset('cdn/select2.min.js') }}"></script>

<script>
    var b1g1_temp = [];
    var shoes_voucher_temp = [];
    var sell_price_voc = 0;
    var value_price_voc = 0;

    function changeQty(row, pst_id, pls_qty)
    {
        var item_qty = jQuery('#item_qty'+row).val();
        var sell_price_item = replaceComma(jQuery('#sell_price_item'+row).text());
        var subtotal_item = replaceComma(jQuery('#subtotal_item'+row).text());
        var total_row = jQuery('tr[data-list-item]').length;
        var total_nameset_side = replaceComma(jQuery('#total_nameset_side').text());

        var b1g1_total_row = b1g1_temp.length;
        var b1g1_qty_total = 0;
        if (b1g1_total_row > 0) {
            jQuery('#orderTable tr').each(function(index, row) {
                var b1g1_qty = parseFloat(jQuery(row).find('.item_qty').val());
                if (typeof b1g1_qty === 'undefined' || b1g1_qty == '' || isNaN(b1g1_qty)) {
                    b1g1_qty = 0;
                }
                if (jQuery(row).hasClass('b1g1_mode')) {
                    b1g1_qty_total += b1g1_qty;
                }
            });
            if (parseFloat(b1g1_qty_total) > 2) {
                jQuery('#item_qty'+row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                return false;
            }
        }

        if (jQuery('#orderList'+row).hasClass('b1g1_mode')) {
            if (item_qty > 2){
                jQuery('#item_qty'+row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                return false;
            }
            if (item_qty%2 == 0) {
                item_qty = item_qty/2;
            } else {
                item_qty = Math.ceil(item_qty/2);
            }
        }
        var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item))
        if (parseFloat(item_qty) < 0) {
            jQuery('#subtotal_item'+row).text('-'+addCommas(subtotal));
        } else {
            jQuery('#subtotal_item'+row).text(addCommas(subtotal));
        }
        var final_price = 0;
        var nameset = 0;
        jQuery('#orderTable tr').each(function(index, row) {
            if (jQuery(row).find('.subtotal_item').text() != '') {
                var sbttl = parseFloat(replaceComma(jQuery(row).find('.subtotal_item').text()));
                if (typeof sbttl === 'undefined' && sbttl == '') {
                    sbttl = 0;
                }
                final_price += sbttl;
            }
            var nameset_value = jQuery(row).find('.nameset_price').val();
            if (typeof nameset_value !== 'undefined' && nameset_value != '') {
                nameset += parseFloat(nameset_value);
            }
        });
        jQuery('#total_price_side').text(addCommas(final_price));
        jQuery('#total_final_price_side').text(addCommas(final_price+nameset));
    }

    function changeReturQty(row, pst_id, qty)
    {
        jQuery('#item_qty'+row).val(-Math.abs(jQuery('#item_qty'+row).val()));
        var item_qty = jQuery('#item_qty'+row).val();
        var sell_price_item = replaceComma(jQuery('#sell_price_item'+row).text());
        var subtotal_item = replaceComma(jQuery('#subtotal_item'+row).text());
        var total_row = jQuery('tr[data-list-item]').length;
        var total_nameset_side = replaceComma(jQuery('#total_nameset_side').text());
        if (parseInt(Math.abs(item_qty)) > parseInt(qty)) {
            swal('Melebihi Pembelian', 'Jumlah item tidak boleh melebihi jumlah pada pembelian', 'warning');
            jQuery('#item_qty'+row).val(-Math.abs(qty));
            return false;
        }
        var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item))
        if (parseFloat(item_qty) < 0) {
            jQuery('#subtotal_item'+row).text('-'+addCommas(subtotal));
        } else {
            jQuery('#subtotal_item'+row).text(addCommas(subtotal));
        }
        var final_price = 0;
        var nameset = 0;
        jQuery('#orderTable tr').each(function(index, row) {
            if (jQuery(row).find('.subtotal_item').text() != '') {
                var sbttl = parseFloat(replaceComma(jQuery(row).find('.subtotal_item').text()));
                if (typeof sbttl === 'undefined' && sbttl == '') {
                    sbttl = 0;
                }
                final_price += sbttl;
            }
            var nameset_value = jQuery(row).find('.nameset_price').val();
            if (typeof nameset_value !== 'undefined' && nameset_value != '') {
                nameset += parseFloat(nameset_value);
            }
        });
        jQuery('#total_price_side').text(addCommas(final_price));
        jQuery('#total_final_price_side').text(addCommas(final_price+nameset));
    }

    function namesetPrice(index)
    {
        if (jQuery('#nameset_price'+index).val() < 0) {
            swal('Minus', 'nameset tidak boleh minus', 'warning');
            jQuery('#nameset_price'+index).val('');
            return false;
        }
        var total_nameset = 0;
        jQuery('#orderTable tr').each(function(index, row) {
            var nameset = jQuery(row).find('.nameset_price').val();
            if (typeof nameset !== 'undefined' && nameset != '') {
                total_nameset += parseFloat(nameset);
            }
        });
        jQuery('#total_nameset_side').text(addCommas(total_nameset));
        jQuery('#total_final_price_side').text(addCommas(total_nameset + parseFloat(replaceComma(jQuery('#total_final_price_side').text()))));
    }

    function reloadWaitingForCheckout()
    {
        jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
        jQuery.ajax({
            type:'POST',
            url: "{{ url('check_waiting_for_checkout')}}",
            dataType: 'html',
            success: function(r) {
                jQuery('#orderTable tr:last').after(r)
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
        return false;
    }

    function reloadComplaint()
    {
        jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
        jQuery.ajax({
            type:'POST',
            url: "{{ url('check_offline_complaint')}}",
            dataType: 'html',
            success: function(r) {
                jQuery('#orderTable tr:last').after(r)
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
        return false;
    }

    function reloadCity(province)
    {
        jQuery.ajax({
            type: "GET",
            data: {_province:province},
            dataType: 'html',
            url: "{{ url('reload_city')}}",
            success: function(r) {
                jQuery('#cust_city').html(r);
            }
        });
    }

    function reloadSubdistrict(city)
    {
        jQuery.ajax({
            type: "GET",
            data: {_city:city},
            dataType: 'html',
            url: "{{ url('reload_subdistrict')}}",
            success: function(r) {
                jQuery('#cust_subdistrict').html(r);
            }
        });
    }

    function toast(title, subtitle, type)
    {
        jQuery.toast({
            heading: title,
            text: subtitle,
            icon: type,
            loader: true,        // Change it to false to disable loader
            loaderBg: '#072544',  // To change the background
            position: 'top-right',
            stack: false,
            hideAfter: 1000
        });
    }

    function replaceComma(str)
    {
        var str_replace = str.replace(/,/g, '');
        return str_replace;
    }

    function addCommas(nStr)
    {
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

    function reloadItemTotal(pt_id)
    {
        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: "POST",
            data: {_pt_id:pt_id},
            dataType: 'json',
            url: "{{ url('reload_item_total')}}",
            success: function(r) {
                if (r.status == '200') {
                    jQuery('#total_item_side').text(r.total_item);
                    jQuery('#total_price_side').text(r.total_price);
                    jQuery('#total_final_price_side').text(parseFloat(r.total_price));
                } else {
                    jQuery('#total_item_side').text(0);
                    jQuery('#total_price_side').text(0);
                    jQuery('#total_final_price_side').text(0);
                }
            }
        });
    };

    function reloadRefund()
    {
        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: "GET",
            dataType: 'html',
            url: "{{ url('reload_refund_offline')}}",
            success: function(r) {
                jQuery('#refund_reload').html(r);
                toast('Reloaded', 'Refund berhasil direload', 'success');
            }
        });
    };


    function saveItem(row, pst_id, price, plst_id, pl_id)
    {
        var sell_price_item = jQuery('#sell_price_item'+row).text();
        var nameset_price = jQuery('#nameset_price'+row).val();
        var subtotal_item = jQuery('#subtotal_item'+row).text();
        var item_qty = jQuery('#item_qty'+row).val();
        var discount_number = jQuery('#discount_number'+row).val();
        if (b1g1_temp.length > 0) {
            price = replaceComma(sell_price_item);
        }
        var access_code = jQuery('#u_secret_code').val();
        var pt_id_complaint = jQuery('#_pt_id_complaint').val();
        var final_price = jQuery('#total_final_price_side').text();
        var exchange = jQuery('#_exchange').val();
        var voc_pst_id = jQuery('#_voc_pst_id').val();
        var voc_value = jQuery('#_voc_value').val();
        var pt_id = jQuery('#_pt_id').val();
        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type:'POST',
            url: "{{ url('save_transaction_detail_offline')}}",
            data: {
                voc_pst_id:voc_pst_id,
                voc_value:value_price_voc,
                _item_qty:item_qty,
                _exchange:exchange,
                _final_price:parseFloat(replaceComma(final_price)),
                _pt_id_complaint:pt_id_complaint,
                _access_code:access_code,
                _pt_id:pt_id,
                _plst_id:plst_id,
                _pst_id:pst_id,
                _price:price,
                _pl_id:pl_id,
                _sell_price_item:replaceComma(sell_price_item),
                _subtotal_item:replaceComma(subtotal_item),
                _nameset_price:nameset_price,
                _discount_number:discount_number
            },
            dataType: 'json',
            success: function(r) {
                console.log(r);
                if (r.status == '200') {
                    toast('Saved', 'Saved', 'success');
                } else if (r.status == '400') {
                    toast('Gagal', 'Gagal simpan transaksi', 'warning');
                }
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
    }

    function deleteItem(pst_id, price, index, pl_id, plst_id, bandrol)
    {
        jQuery(this).html('');
        var subtotal_item = jQuery('#subtotal_item'+index).text();
        var total_item = jQuery('#total_item_side').text();
        var total_price = jQuery('#total_price_side').text();
        var total_final = jQuery('#total_final_price_side').text();
        var item_name = jQuery('#item_name'+index).text();
        var mode = 'delete';
        var item_type = jQuery('#item_type option:selected').val();
        var total_row = jQuery('tr[data-list-item]').length;
        var total_nameset = replaceComma(jQuery('#total_nameset_side').text());
        var nameset = jQuery('#nameset_price'+index).val();
        var free_delete = '';
        if (subtotal_item == '') {
            subtotal_item = parseFloat(0);
        } else {
            subtotal_item = parseFloat(replaceComma(subtotal_item));
        }
        if (nameset == '' || nameset == '0') {
            nameset = 0;
        }
        if (total_nameset == '' || total_nameset == '0') {
            total_nameset = 0;
        }
        var final_nameset = parseFloat(total_nameset) - parseFloat(nameset);
        var final_price = parseFloat(replaceComma(jQuery('#total_price_side').text()));
        var key = pst_id+'-'+bandrol+'-'+price;
        var total_key = jQuery.grep(shoes_voucher_temp, function (value) {
            return value === key;
        }).length;

        jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
        jQuery.ajax({
            type:'POST',
            url: "{{ url('change_waiting_status')}}",
            data: { _pst_id:pst_id, _pl_id:pl_id, _mode:mode, _item_type:item_type, _plst_id:plst_id},
            dataType: 'json',
            success: function(r) {
                jQuery.noConflict();
                if (r.status == '200') {
                    jQuery('#cancel_voucher').trigger('click');
                    toast('Dihapus', 'Item berhasil dihapus', 'success');
                    jQuery('#total_item_side').text(parseInt(total_item) - 1);
                    jQuery('#total_nameset_side').text(addCommas(final_nameset));
                    jQuery('#total_price_side').text(addCommas(final_price - subtotal_item));
                    jQuery('#total_final_price_side').text(addCommas(final_price + final_nameset - subtotal_item));
                    b1g1_temp = jQuery.grep(b1g1_temp, function(value) {
                        return value != price;
                    });
                    console.log(b1g1_temp);
                    shoes_voucher_temp = jQuery.grep(shoes_voucher_temp, function(value) {
                        return value != key;
                    });
                    if (total_key > 1) {
                        for (let i = 1 ; i < total_key ; i++) {
                            shoes_voucher_temp.push(key);
                        }
                    }
                    console.log(shoes_voucher_temp);
                    jQuery('#orderList'+index).remove();
                } else if (r.status == '400') {
                    toast('Gagal', 'Item gagal dihapus, jika ingin menghapus, pilih terlebih dahulu LOKASI tempat barang diambil, coba kembali', 'danger');
                }
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
    }

    function checkout()
    {
        var pt_id_complaint = jQuery('#_pt_id_complaint').val();
        var pm_id = jQuery('#pm_id_offline').val();
        var pm_id_two = jQuery('#pm_id_offline_two').val();
        var std_id = jQuery('#std_id').val();
        var type = jQuery('#std_id option:selected').text();
        var cust_id = jQuery('#cust_id').val();
        var free_sock_cust_id = jQuery('#free_sock_customer_id').val();
        var note = jQuery('#note').val();
        var cp_id = jQuery('#cp_id').val();
        var card_number = jQuery('#card_number').val();
        var ref_number = jQuery('#ref_number').val();
        var cp_id_two = jQuery('#cp_id_two').val();
        var card_number_two = jQuery('#card_number_two').val();
        var ref_number_two = jQuery('#ref_number_two').val();
        var total_row = jQuery('tr[data-list-item]').length;
        var access_code = jQuery('#u_secret_code').val();
        var charge = jQuery('#charge').val();
        var total_payment = jQuery('#total_payment').val();
        var total_payment_two = jQuery('#total_payment_two').val();
        var exchange = jQuery('#_exchange').val();
        var real_price = jQuery('#payment_total').text();
        var ur_id = jQuery('#free_sock_customer_ur_id').val();
        var unique_code = jQuery('#unique_code').val();
        var another_cost = jQuery('#another_cost').val();
        var admin_cost = jQuery('#admin_cost').val();
        var cr_id = jQuery('#cr_id').val();
        var shipping_cost = jQuery('#shipping_cost').val();

        var voc_pst_id = jQuery('#_voc_pst_id').val();
        var voc_value = jQuery('#_voc_value').val();
        var voc_id = jQuery('#_voc_id').val();
        if (jQuery('#free_sock_customer_mode').val() == '1' && free_sock_cust_id == '') {
            swal('Customer Belum Selesai', 'Mohon pastikan bahwa customer sudah selesai melakukan input data dan rating', 'warning');
            return false;
        }
        if (free_sock_cust_id != '') {
            cust_id = free_sock_cust_id;
        }
        if (unique_code == '') {
            unique_code = 0;
        }
        if (another_cost == '') {
            another_cost = 0;
        }
        if (admin_cost == '') {
            admin_cost = 0;
        }
        if (shipping_cost == '') {
            shipping_cost = 0;
        }
        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type:'POST',
            url: "{{ url('save_transaction_offline')}}",
            data: {
                voc_id:voc_id,
                voc_pst_id:voc_pst_id,
                voc_value:value_price_voc,
                _cr_id:cr_id,
                _shipping_cost:shipping_cost,
                _unique_code:unique_code,
                _another_cost:another_cost,
                _admin_cost:admin_cost,
                _ur_id:ur_id,
                _real_price:replaceComma(real_price),
                _exchange:exchange,
                _pt_id_complaint:pt_id_complaint,
                _charge:charge,
                _cp_id:cp_id,
                _cp_id_two:cp_id_two,
                _total_payment:total_payment,
                _total_payment_two:total_payment_two,
                _access_code:access_code,
                _type:type,
                _pm_id:pm_id,
                _pm_id_two:pm_id_two, _std_id:std_id,
                _cust_id:cust_id, _note:note,
                _card_number:card_number, _ref_number:ref_number,
                _card_number_two:card_number_two, _ref_number_two:ref_number_two},
            dataType: 'json',
            success: function(r) {
                jQuery.noConflict();
                jQuery("#payment-offline-popup").modal('hide');
                if (r.status == '200') {
                    var finish = '';
                    jQuery('#_pt_id').val(r.pt_id);
                    jQuery('#orderTable tr').each(function(index, row) {
                        jQuery(row).find('.saveItem').trigger('click');
                    });
                    jQuery('#cr_id').val('');
                    jQuery('#shipping_cost').val('');
                    jQuery('#total_final_price_side').text('0');
                    jQuery('#total_item_side').text('0');
                    jQuery('#total_price_side').text('0');
                    jQuery('#total_nameset_side').text('0');
                    jQuery('#return_payment').text('');
                    jQuery('#orderTable').find('tr:not(:has(th))').remove();
                    jQuery('#cust_id').val(1);
                    jQuery('#cust_id_label').val('');
                    jQuery('#total_payment').val('');
                    jQuery('#total_payment_two').val('');
                    jQuery('#payment_option').val('');
                    jQuery('#cp_id').val('');
                    jQuery('#card_number').val('');
                    jQuery('#ref_number').val('');
                    jQuery('#charge').val('');
                    jQuery('#charge_total').val('');
                    jQuery('#cp_id_two').val('');
                    jQuery('#card_number_two').val('');
                    jQuery('#ref_number_two').val('');
                    jQuery('#pm_id_offline').val('');
                    jQuery('#pm_id_offline_two').val('');
                    jQuery('#_pt_id_complaint').val('');
                    jQuery('#_exchange').val('');

                    jQuery('#card_provider_content').addClass('d-none');
                    jQuery('#card_provider_content').removeClass('d-flex');
                    jQuery('#card_number_label').addClass('d-none');
                    jQuery('#card_number_label').removeClass('d-flex');
                    jQuery('#ref_number_label').addClass('d-none');
                    jQuery('#ref_number_label').removeClass('d-flex');
                    jQuery('#charge_label').addClass('d-none');
                    jQuery('#charge_label').removeClass('d-flex');

                    jQuery('#card_provider_content_two').addClass('d-none');
                    jQuery('#card_provider_content_two').removeClass('d-flex');
                    jQuery('#card_number_label_two').addClass('d-none');
                    jQuery('#card_number_label_two').removeClass('d-flex');
                    jQuery('#ref_number_label_two').addClass('d-none');
                    jQuery('#ref_number_label_two').removeClass('d-flex');
                    jQuery('#total_payment_two_label').addClass('d-none');
                    jQuery('#total_payment_two_label').removeClass('d-flex');
                    jQuery('#payment_type_content_two').addClass('d-none');
                    jQuery('#payment_type_content_two').removeClass('d-flex');
                    jQuery('#total_payment_two').addClass('d-none');
                    jQuery('#total_payment_two').removeClass('d-flex');

                    jQuery('#note').val('');
                    jQuery('#_pt_id').val('');
                    jQuery('#u_secret_code').val('');
                    jQuery('#InputCodeModal').modal('hide');
                    jQuery('#free_sock_customer_panel').addClass('d-none');
                    jQuery('#free_sock_customer_label').val('');
                    jQuery('#free_sock_customer_id').val('');
                    jQuery('#free_sock_customer_mode').val('');
                    jQuery('#free_sock_customer_ur_id').val('');

                    jQuery('#_voc_pst_id').val('');
                    jQuery('#_voc_value').val('');
                    jQuery('#_voc_id').val('');
                    jQuery('#voucher_code').val("");
                    jQuery('#voucher_information').addClass("d-none");
                    sell_price_voc = 0;
                    value_price_voc = 0;
                    shoes_voucher_temp = [];
                    @php  session()->forget('voc_item') @endphp
                    b1g1_temp = [];
                    swal('Berhasil', 'Transaksi Berhasil Disimpan', 'success');
                    setTimeout(() => {
                        if (std_id == '17') {
                            var win = window.open('{{ url('') }}/print_offline_invoice/'+r.invoice, '_blank');
                        } else {
                            var win = window.open('{{ url('') }}/print_invoice/'+r.invoice, '_blank');
                        }
                        if (win) {
                            win.focus();
                        } else {
                            alert('Please allow popups for this website');
                        }
                    }, 2000);
                } else if (r.status == '400') {
                    swal('Gagal', 'Gagal simpan transaksi', 'warning');
                }
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
    };

    function changeDiscountPercentage(row, pst_id, pls_qty)
    {
        var item_qty = jQuery('#item_qty'+row).val();
        var sell_price_item = replaceComma(jQuery('#sell_price_item'+row).text());
        var subtotal_item = replaceComma(jQuery('#subtotal_item'+row).text());
        var total_row = jQuery('tr[data-list-item]').length;
        var total_nameset_side = replaceComma(jQuery('#total_nameset_side').text());

        var b1g1_total_row = b1g1_temp.length;
        var b1g1_qty_total = 0;
        if (b1g1_total_row > 0) {
            jQuery('#orderTable tr').each(function(index, row) {
                var b1g1_qty = parseFloat(jQuery(row).find('.item_qty').val());
                if (typeof b1g1_qty === 'undefined' || b1g1_qty == '' || isNaN(b1g1_qty)) {
                    b1g1_qty = 0;
                }
                if (jQuery(row).hasClass('b1g1_mode')) {
                    b1g1_qty_total += b1g1_qty;
                }
            });
            if (parseFloat(b1g1_qty_total) > 2) {
                jQuery('#item_qty'+row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                return false;
            }
        }

        if (jQuery('#orderList'+row).hasClass('b1g1_mode')) {
            if (item_qty > 2){
                jQuery('#item_qty'+row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                return false;
            }
            if (item_qty%2 == 0) {
                item_qty = item_qty/2;
            } else {
                item_qty = Math.ceil(item_qty/2);
            }
        }
        var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item))
        if (parseFloat(item_qty) < 0) {
            jQuery('#subtotal_item'+row).text('-'+addCommas(subtotal));
        } else {
            jQuery('#subtotal_item'+row).text(addCommas(subtotal));
        }
        var final_price = 0;
        var nameset = 0;
        jQuery('#orderTable tr').each(function(index, row) {
            if (jQuery(row).find('.subtotal_item').text() != '') {
                var sbttl = parseFloat(replaceComma(jQuery(row).find('.subtotal_item').text()));
                if (typeof sbttl === 'undefined' && sbttl == '') {
                    sbttl = 0;
                }
                final_price += sbttl;
            }
            var nameset_value = jQuery(row).find('.nameset_price').val();
            if (typeof nameset_value !== 'undefined' && nameset_value != '') {
                nameset += parseFloat(nameset_value);
            }
        });
        jQuery('#total_price_side').text(addCommas(final_price));
        jQuery('#total_final_price_side').text(addCommas(final_price+nameset));
    }

    function changeDiscountNumber(row, pst_id, pls_qty)
    {
        var item_qty = jQuery('#item_qty'+row).val();
        var sell_price_item = replaceComma(jQuery('#sell_price_item'+row).text());
        var subtotal_item = replaceComma(jQuery('#subtotal_item'+row).text());
        var total_row = jQuery('tr[data-list-item]').length;
        var total_nameset_side = replaceComma(jQuery('#total_nameset_side').text());

        var b1g1_total_row = b1g1_temp.length;
        var b1g1_qty_total = 0;
        if (b1g1_total_row > 0) {
            jQuery('#orderTable tr').each(function(index, row) {
                var b1g1_qty = parseFloat(jQuery(row).find('.item_qty').val());
                if (typeof b1g1_qty === 'undefined' || b1g1_qty == '' || isNaN(b1g1_qty)) {
                    b1g1_qty = 0;
                }
                if (jQuery(row).hasClass('b1g1_mode')) {
                    b1g1_qty_total += b1g1_qty;
                }
            });
            if (parseFloat(b1g1_qty_total) > 2) {
                jQuery('#item_qty'+row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                return false;
            }
        }

        if (jQuery('#orderList'+row).hasClass('b1g1_mode')) {
            if (item_qty > 2){
                jQuery('#item_qty'+row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                return false;
            }
            if (item_qty%2 == 0) {
                item_qty = item_qty/2;
            } else {
                item_qty = Math.ceil(item_qty/2);
            }
        }
        var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item))
        if (parseFloat(item_qty) < 0) {
            jQuery('#subtotal_item'+row).text('-'+addCommas(subtotal));
        } else {
            jQuery('#subtotal_item'+row).text(addCommas(subtotal));
        }
        var final_price = 0;
        var nameset = 0;
        jQuery('#orderTable tr').each(function(index, row) {
            if (jQuery(row).find('.subtotal_item').text() != '') {
                var sbttl = parseFloat(replaceComma(jQuery(row).find('.subtotal_item').text()));
                if (typeof sbttl === 'undefined' && sbttl == '') {
                    sbttl = 0;
                }
                final_price += sbttl;
            }
            var nameset_value = jQuery(row).find('.nameset_price').val();
            if (typeof nameset_value !== 'undefined' && nameset_value != '') {
                nameset += parseFloat(nameset_value);
            }
        });

        // Memperoleh nilai diskon dari input dengan ID discount_number
        var discount = parseFloat(jQuery('#discount_number'+row).val()) || 0;

        // Mengurangi diskon dari subtotal
        var subtotal = parseFloat(item_qty) * parseFloat(sell_price_item) - discount;

        if (parseFloat(item_qty) < 0) {
            jQuery('#subtotal_item' + row).text('-' + addCommas(subtotal));
        } else {
            jQuery('#subtotal_item' + row).text(addCommas(subtotal));
        }

        jQuery('#total_price_side').text(addCommas(final_price - discount));

        jQuery('#total_final_price_side').text(addCommas(final_price + nameset - discount));
    }

    function changeDiscountPercentage(row, pst_id, pls_qty)
    {
        var item_qty = jQuery('#item_qty'+row).val();
        var sell_price_item = replaceComma(jQuery('#sell_price_item'+row).text());
        var subtotal_item = replaceComma(jQuery('#subtotal_item'+row).text());
        var total_row = jQuery('tr[data-list-item]').length;
        var total_nameset_side = replaceComma(jQuery('#total_nameset_side').text());

        var b1g1_total_row = b1g1_temp.length;
        var b1g1_qty_total = 0;
        if (b1g1_total_row > 0) {
            jQuery('#orderTable tr').each(function(index, row) {
                var b1g1_qty = parseFloat(jQuery(row).find('.item_qty').val());
                if (typeof b1g1_qty === 'undefined' || b1g1_qty == '' || isNaN(b1g1_qty)) {
                    b1g1_qty = 0;
                }
                if (jQuery(row).hasClass('b1g1_mode')) {
                    b1g1_qty_total += b1g1_qty;
                }
            });
            if (parseFloat(b1g1_qty_total) > 2) {
                jQuery('#item_qty'+row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                return false;
            }
        }

        if (jQuery('#orderList'+row).hasClass('b1g1_mode')) {
            if (item_qty > 2){
                jQuery('#item_qty'+row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                return false;
            }
            if (item_qty%2 == 0) {
                item_qty = item_qty/2;
            } else {
                item_qty = Math.ceil(item_qty/2);
            }
        }
        var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item))
        if (parseFloat(item_qty) < 0) {
            jQuery('#subtotal_item'+row).text('-'+addCommas(subtotal));
        } else {
            jQuery('#subtotal_item'+row).text(addCommas(subtotal));
        }
        var final_price = 0;
        var nameset = 0;
        jQuery('#orderTable tr').each(function(index, row) {
            if (jQuery(row).find('.subtotal_item').text() != '') {
                var sbttl = parseFloat(replaceComma(jQuery(row).find('.subtotal_item').text()));
                if (typeof sbttl === 'undefined' && sbttl == '') {
                    sbttl = 0;
                }
                final_price += sbttl;
            }
            var nameset_value = jQuery(row).find('.nameset_price').val();
            if (typeof nameset_value !== 'undefined' && nameset_value != '') {
                nameset += parseFloat(nameset_value);
            }
        });

        // Memperoleh nilai diskon dari input dengan ID discount_number
        var discountPercentage = parseFloat(jQuery('#discount_percentage'+row).val()) || 0;
        var totalBeforeDiscount = final_price + nameset;
        var discount = parseFloat((discountPercentage/100) * totalBeforeDiscount);

        // Mengurangi diskon dari subtotal
        var subtotal = parseFloat(item_qty) * parseFloat(sell_price_item) - discount;

        if (parseFloat(item_qty) < 0) {
            jQuery('#subtotal_item' + row).text('-' + addCommas(subtotal));
        } else {
            jQuery('#subtotal_item' + row).text(addCommas(subtotal));
        }

        jQuery('#total_price_side').text(addCommas(final_price - discount));

        jQuery('#total_final_price_side').text(addCommas(totalBeforeDiscount - discount));
    }

    jQuery(document).delegate('#add_to_item_list', 'click', function(e) {
        e.preventDefault();
        jQuery('#product_name_input').val('');
        jQuery('#itemList').html('');
        jQuery('#itemList').fadeOut();
        jQuery('#_exchange').val('true');
        var pt_id = '';
        var st_id = "{{ $data['user']->st_id }}";
        var p_name = jQuery(this).attr('data-p_name');
        var pst_id = jQuery(this).attr('data-pst_id');
        var fs = jQuery(this).attr('data-fs');
        var pl_id = jQuery(this).attr('data-pl_id');
        var pls_qty = jQuery(this).attr('data-pls_qty');
        var psc_id = jQuery(this).attr('data-psc_id');
        var plst_id = jQuery(this).attr('data-plst_id');
        var sell_price = jQuery(this).attr('data-sell_price');
        var total_row = parseFloat(jQuery('#total_row').val());
        jQuery('#total_row').val(total_row+1);
        var pos_item_list = jQuery('.pos_item_list'+pst_id).length;
        var item_type = jQuery('#item_type option:selected').val();
        var mode = 'add';
        var b1g1_id = jQuery(this).attr('data-b1g1_id');
        var b1g1_price = jQuery(this).attr('data-b1g1_price');
        var highlight = '';
        var b1g1_mode = '';

        if (psc_id == '1') {
            var bandrol = jQuery(this).attr('data-bandrol');
            shoes_voucher_temp.push(pst_id+'-'+bandrol+'-'+sell_price);
            // console.log(shoes_voucher_temp);
        }

        if (b1g1_id != '' && b1g1_price != '') {
            b1g1_temp.push(b1g1_price);
            highlight = 'background:#ffc107; color:#000; font-weight:bold; border-radius:20px;';
            b1g1_mode = 'b1g1_mode';
            b1g1_temp = b1g1_temp.sort((a, b) => b-a);
            var b1g1_total_row = b1g1_temp.length;
            var b1g1_qty_total = 0;
            if (b1g1_total_row > 0) {
                jQuery('#orderTable tr').each(function(index, row) {
                    var b1g1_qty = parseFloat(jQuery(row).find('.item_qty').val());
                    if (typeof b1g1_qty === 'undefined' || b1g1_qty == '' || isNaN(b1g1_qty)) {
                        b1g1_qty = 0;
                    }

                    if (jQuery(row).hasClass('b1g1_mode')) {
                        b1g1_qty_total += b1g1_qty;
                        if (parseFloat(sell_price) >= parseFloat(b1g1_temp[0])) {
                            sell_price = sell_price;
                            jQuery(row).find('.sell_price_item').text('0');
                        } else {
                            sell_price = 0;
                        }
                        jQuery(row).find('.item_qty').trigger('change');

                    }
                });
                if (parseFloat(b1g1_qty_total) >= 2) {
                    swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                    return false;
                }
            }

        }
        var total_item = jQuery('#total_item_side').text();
        var total_price = jQuery('#total_price_side').text();
        var total_final_price = jQuery('#total_final_price_side').text();

        if (jQuery('#cust_id').val() == '') {
            swal('Customer', 'Pilih customer', 'warning');
            jQuery('#product_name_input').val('');
            return false;
        }
        jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
        jQuery.ajax({
            type:'POST',
            url: "{{ url('change_waiting_status')}}",
            data: {_pt_id:pt_id, _pst_id:pst_id, _pl_id:pl_id, _mode:mode, _item_type:item_type, _plst_id:plst_id, _sell_price:sell_price},
            dataType: 'json',
            success: function(r) {
                jQuery.noConflict();
                if (r.status == '200') {
                    toast('Ditambah', 'Item berhasil ditambah', 'success');
                    jQuery('#total_item_side').text(parseInt(total_item) + 1);
                    jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + parseFloat(sell_price)));
                    jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + parseFloat(sell_price)));
                    if (item_type == 'waiting') {
                        jQuery('#orderTable tr:last').after(
                            "<tr data-list-item class='pos_item_list mb-2 bg-light-primary "+b1g1_mode+"' id='orderList"+(total_row+1)+"'>" +
                            " <td style='white-space: nowrap; font-size:14px; "+highlight+"' id='item_name"+(total_row+1)+"'>"+p_name+"</td>" +
                            " <td>"+(pls_qty)+"</td> " +
                            " <td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" item_qty' id='item_qty"+(total_row+1)+"' value='1' onchange='return changeQty("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")' readonly></td>" +
                            " <td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" discount_percentage' id='discount_percentage"+(total_row+1)+"' value='0' onchange='return changeDiscountPercentage("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")'></td>" +
                            " <td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" discount_number' id='discount_number"+(total_row+1)+"' value='0' onchange='return changeDiscountNumber("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")'></td>" +
                            " <td><input type='number' class='col-8 nameset_price' id='nameset_price"+(total_row+1)+"' onchange='return namesetPrice("+(total_row+1)+")'/></td>" +
                            " <td><span class='sell_price_item' id='sell_price_item"+(total_row+1)+"'>"+addCommas(sell_price)+"</span></td>" +
                            " <td><span class='subtotal_item' id='subtotal_item"+(total_row+1)+"'>"+addCommas(sell_price)+"</span></td>" +
                            " <td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem"+(total_row+1)+"' onclick='return saveItem("+(total_row+1)+", "+pst_id+", "+sell_price+", "+plst_id+", "+pl_id+")'>" +
                            " <i class='fa fa-eye' style='display:none;'></i></a> " +
                            " <a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem("+pst_id+", "+sell_price+", "+(total_row+1)+", "+pl_id+", "+plst_id+", "+bandrol+")'><i class='fas fa-trash-alt'></i></a></div></td></tr>");
                    } else {
                        jQuery('#orderTable tr:last').after("" +
                            "<tr data-list-item class='pos_item_list mb-2 bg-light-primary "+b1g1_mode+"' id='orderList"+(total_row+1)+"'>" +
                            "<td style='white-space: nowrap; font-size:14px; "+highlight+"' id='item_name"+(total_row+1)+"'>"+p_name+"</td>" +
                            "<td>"+(pls_qty)+"</td> " +
                            "<td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" item_qty' id='item_qty"+(total_row+1)+"' value='1' onchange='return changeQty("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")'></td> " +
                            "<td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" discount_percentage' id='discount_percentage"+(total_row+1)+"' value='0' onchange='return changeDiscountPercentage("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")'></td>" +
                            " <td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" discount_number' id='discount_number"+(total_row+1)+"' value='0' onchange='return changeDiscountNumber("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")'></td>" +
                            "<td><input type='number' class='col-8 nameset_price' id='nameset_price"+(total_row+1)+"' onchange='return namesetPrice("+(total_row+1)+")'/></td> " +
                            "<td><span class='sell_price_item' id='sell_price_item"+(total_row+1)+"'>"+addCommas(sell_price)+"</span></td> " +
                            "<td><span class='subtotal_item' id='subtotal_item"+(total_row+1)+"'>"+addCommas(sell_price)+"</span></td> " +
                            "<td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem"+(total_row+1)+"' onclick='return saveItem("+(total_row+1)+", "+pst_id+", "+sell_price+", "+r.plst_id+", "+pl_id+")'><i class='fa fa-eye' style='display:none;'></i></a> " +
                            "<a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem("+pst_id+", "+sell_price+", "+(total_row+1)+", "+pl_id+", "+r.plst_id+", "+bandrol+")'><i class='fas fa-trash-alt'></i></a></div></td></tr>");
                    }
                    console.log(b1g1_temp);
                } else if (r.status == '400') {
                    toast('Gagal', 'Item gagal ditambah', 'danger');
                }
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
        if (jQuery('#voucher_code').val() != '') {
            var code = jQuery('#voucher_code').val();
            jQuery('#cancel_voucher').trigger('click');
            jQuery('#voucher_code').val(code);
            setTimeout(() => {
                jQuery('#f_voucher').trigger('submit');
            }, 300);
        }
        return false;
    });

    jQuery(document).delegate('#barcode_input', 'change', function(e) {
        e.preventDefault();
        var barcode = jQuery(this).val();
        var type = jQuery('#std_id option:selected').text();
        var item_type = jQuery('#item_type option:selected').val();
        var std_id = jQuery('#std_id').val();

        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url:"{{  url('pos_barcode_scan') }}",
            method:"POST",
            dataType:"json",
            data:{barcode:barcode, type:type, _item_type:item_type, _std_id:std_id},
            success:function(r){
                if (r.status == '200') {
                    var pt_id = '';
                    var st_id = "{{ $data['user']->st_id }}";
                    var p_name = r.p_name;
                    var pst_id = r.pst_id;
                    var fs = r.fs;
                    var pl_id = r.pl_id;
                    var pls_qty = r.pls_qty;
                    var psc_id = r.psc_id;
                    var plst_id = r.plst_id;
                    var sell_price = r.sell_price;
                    var bandrol = r.bandrol;
                    var total_row = parseFloat(jQuery('#total_row').val());
                    jQuery('#total_row').val(total_row+1);
                    var pos_item_list = jQuery('.pos_item_list'+pst_id).length;
                    var item_type = jQuery('#item_type option:selected').val();
                    var mode = 'add';
                    var b1g1_id = r.b1g1_id;
                    var b1g1_price = r.b1g1_price;
                    var highlight = '';
                    var b1g1_mode = '';

                    if (psc_id == '1') {
                        shoes_voucher_temp.push(pst_id+'-'+bandrol+'-'+sell_price);
                        // console.log(shoes_voucher_temp);
                    }

                    if (b1g1_id != '' && b1g1_price != '') {
                        b1g1_temp.push(b1g1_price);
                        highlight = 'background:#ffc107; color:#000; font-weight:bold; border-radius:20px;';
                        b1g1_mode = 'b1g1_mode';
                        b1g1_temp = b1g1_temp.sort((a, b) => b-a);
                        var b1g1_total_row = b1g1_temp.length;
                        var b1g1_qty_total = 0;
                        if (b1g1_total_row > 0) {
                            jQuery('#orderTable tr').each(function(index, row) {
                                var b1g1_qty = parseFloat(jQuery(row).find('.item_qty').val());
                                if (typeof b1g1_qty === 'undefined' || b1g1_qty == '' || isNaN(b1g1_qty)) {
                                    b1g1_qty = 0;
                                }

                                if (jQuery(row).hasClass('b1g1_mode')) {
                                    b1g1_qty_total += b1g1_qty;
                                    if (parseFloat(sell_price) >= parseFloat(b1g1_temp[0])) {
                                        sell_price = sell_price;
                                        jQuery(row).find('.sell_price_item').text('0');
                                    } else {
                                        sell_price = 0;
                                    }
                                    jQuery(row).find('.item_qty').trigger('change');

                                }
                            });
                            if (parseFloat(b1g1_qty_total) >= 2) {
                                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                                return false;
                            }
                        }

                    }
                    var total_item = jQuery('#total_item_side').text();
                    var total_price = jQuery('#total_price_side').text();
                    var total_final_price = jQuery('#total_final_price_side').text();

                    if (jQuery('#cust_id').val() == '') {
                        swal('Customer', 'Pilih customer', 'warning');
                        jQuery('#product_name_input').val('');
                        return false;
                    }
                    jQuery.ajaxSetup({
                            headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                            }
                        });
                    jQuery.ajax({
                        type:'POST',
                        url: "{{ url('change_waiting_status')}}",
                        data: {_pt_id:pt_id, _pst_id:pst_id, _pl_id:pl_id, _mode:mode, _item_type:item_type, _plst_id:plst_id, _sell_price:sell_price},
                        dataType: 'json',
                        success: function(r) {
                            jQuery.noConflict();
                            if (r.status == '200') {
                                toast('Ditambah', 'Item berhasil ditambah', 'success');
                                jQuery('#total_item_side').text(parseInt(total_item) + 1);
                                jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + parseFloat(sell_price)));
                                jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + parseFloat(sell_price)));
                                if (item_type == 'waiting') {
                                    jQuery('#orderTable tr:last').after("" +
                                        "<tr data-list-item class='pos_item_list mb-2 bg-light-primary "+b1g1_mode+"' id='orderList"+(total_row+1)+"'> " +
                                        "<td style='white-space: nowrap; font-size:14px; "+highlight+"' id='item_name"+(total_row+1)+"'>"+p_name+"</td> " +
                                        "<td>"+(pls_qty)+"</td> " +
                                        "<td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" item_qty' id='item_qty"+(total_row+1)+"' value='1' onchange='return changeQty("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")' readonly></td> " +
                                        " <td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" discount_percentage' id='discount_percentage"+(total_row+1)+"' value='0' onchange='return changeDiscountPercentage("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")'></td>" +
                                        " <td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" discount_number' id='discount_number"+(total_row+1)+"' value='0' onchange='return changeDiscountNumber("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")'></td>" +
                                        "<td><input type='number' class='col-8 nameset_price' id='nameset_price"+(total_row+1)+"' onchange='return namesetPrice("+(total_row+1)+")'/></td> " +
                                        "<td><span class='sell_price_item' id='sell_price_item"+(total_row+1)+"'>"+addCommas(sell_price)+"</span></td> " +
                                        "<td><span class='subtotal_item' id='subtotal_item"+(total_row+1)+"'>"+addCommas(sell_price)+"</span></td> " +
                                        "<td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem"+(total_row+1)+"' onclick='return saveItem("+(total_row+1)+", "+pst_id+", "+sell_price+", "+plst_id+", "+pl_id+")'><i class='fa fa-eye' style='display:none;'></i></a> " +
                                        "<a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem("+pst_id+", "+sell_price+", "+(total_row+1)+", "+pl_id+", "+plst_id+", "+bandrol+")'><i class='fas fa-trash-alt'></i></a></div></td></tr>");
                                } else {
                                    jQuery('#orderTable tr:last').after("" +
                                        "<tr data-list-item class='pos_item_list mb-2 bg-light-primary "+b1g1_mode+"' id='orderList"+(total_row+1)+"'>" +
                                        "<td style='white-space: nowrap; font-size:14px; "+highlight+"' id='item_name"+(total_row+1)+"'>"+p_name+"</td>" +
                                        "<td>"+(pls_qty)+"</td>" +
                                        "<td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" item_qty' id='item_qty"+(total_row+1)+"' value='1' onchange='return changeQty("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")'></td> " +
                                        " <td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" discount_percentage' id='discount_percentage"+(total_row+1)+"' value='0' onchange='return changeDiscountPercentage("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")'></td>" +
                                        " <td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" discount_number' id='discount_number"+(total_row+1)+"' value='0' onchange='return changeDiscountNumber("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")'></td>" +
                                        "<td><input type='number' class='col-8 nameset_price' id='nameset_price"+(total_row+1)+"' onchange='return namesetPrice("+(total_row+1)+")'/></td> " +
                                        "<td><span class='sell_price_item' id='sell_price_item"+(total_row+1)+"'>"+addCommas(sell_price)+"</span></td> " +
                                        "<td><span class='subtotal_item' id='subtotal_item"+(total_row+1)+"'>"+addCommas(sell_price)+"</span></td> " +
                                        "<td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem"+(total_row+1)+"' onclick='return saveItem("+(total_row+1)+", "+pst_id+", "+sell_price+", "+r.plst_id+", "+pl_id+")'>" +
                                        "<i class='fa fa-eye' style='display:none;'></i></a> <a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem("+pst_id+", "+sell_price+", "+(total_row+1)+", "+pl_id+", "+r.plst_id+", "+bandrol+")'><i class='fas fa-trash-alt'></i></a></div></td></tr>");
                                }
                                console.log(b1g1_temp);
                            } else if (r.status == '400') {
                                toast('Gagal', 'Item gagal ditambah', 'danger');
                            }
                        },
                        error: function(data){
                            swal('Error', data, 'error');
                        }
                    });
                    if (jQuery('#voucher_code').val() != '') {
                        var code = jQuery('#voucher_code').val();
                        jQuery('#cancel_voucher').trigger('click');
                        jQuery('#voucher_code').val(code);
                        setTimeout(() => {
                            jQuery('#f_voucher').trigger('submit');
                        }, 300);
                    }
                } else {
                    toast('Tidak DItemukan', 'Barcode tidak ditemukan', 'warning');
                }
            }
        });
        jQuery('#barcode_input').val('');
        jQuery('#barcode_input').focus();
    });

    var product = jQuery('#Ptb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: 'rt<"text-right"ip>',
        buttons: [
            { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
        ],
        ajax: {
            url : "{{ url('scan_adjustment_product_datatables') }}",
            data : function (d) {
                d.search = jQuery('#p_search').val();
            }
        },
        columns: [
        { data: 'DT_RowIndex', name: 'id', searchable: false},
        { data: 'br_name', name: 'br_name' },
        { data: 'p_name', name: 'p_name' },
        { data: 'p_color', name: 'p_color' },
        { data: 'sz_name', name: 'sz_name' },
        { data: 'ps_barcode_show', name: 'ps_barcode' },
        ],
        columnDefs: [
        {
            "targets": 0,
            "className": "text-center",
            "width": "0%"
        }],
        order: [[0, 'desc']],
    });

    jQuery('#p_search').on('keyup', function() {
        product.draw(false);
    });

    jQuery(document).delegate('#product_barcode_btn', 'click', function(e) {
        e.preventDefault();
        jQuery.noConflict();
        jQuery('#ProductBarcodeModal').modal('show');
        product.draw(false);
    });

    jQuery(document).delegate('#input_barcode', 'change', function(e) {
        e.preventDefault();
        var id = jQuery(this).attr('data-id');
        var barcode = jQuery(this).val();
        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: "POST",
            data: {id:id, barcode:barcode},
            dataType: 'json',
            url: "{{ url('scan_adjustment_barcode_update')}}",
            success: function(r) {
                if (r.status == '200'){
                    swal("Berhasil", "Data berhasil diupdate", "success");
                    product.draw(false);
                } else {
                    swal('Gagal', 'Gagal update data', 'error');
                }
            }
        });
        return false;
    });

    jQuery(document).on('body', 'click', function(e) {
        jQuery('#itemList').fadeOut();
    });

    setInterval(() => {
        var free_sock_mode = jQuery('#free_sock_customer_mode').val();
        if (free_sock_mode == '1') {
            jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                url:"{{  url('check_rating_for_checkout') }}",
                method:"POST",
                dataType:"JSON",
                success:function(r){
                    if (r.status == '200') {
                        jQuery('#waiting_customer_label').addClass('d-none');
                        jQuery('#free_sock_customer_panel').removeClass('d-none');
                        jQuery('#free_sock_customer_label').val(r.cust_name);
                        jQuery('#free_sock_customer_id').val(r.cust_id);
                        jQuery('#free_sock_customer_ur_id').val(r.ur_id);
                    } else {
                        jQuery('#free_sock_customer_panel').addClass('d-none');
                        jQuery('#free_sock_customer_label').val('');
                        jQuery('#free_sock_customer_id').val('');
                        jQuery('#free_sock_customer_ur_id').val('');
                    }
                }
            });
        }
    }, 3000);


    jQuery(document).delegate('#cancel_rating_btn', 'click', function() {
        jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
        jQuery.ajax({
            type:'POST',
            url: "{{ url('delete_rating')}}",
            dataType: 'json',
            success: function(r) {
                jQuery('#waiting_customer_label').addClass('d-none');
                jQuery('#free_sock_customer_panel').addClass('d-none');
                jQuery('#free_sock_customer_label').val('');
                jQuery('#free_sock_customer_id').val('');
                jQuery('#free_sock_customer_mode').val('');
                jQuery('#free_sock_customer_ur_id').val('');
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
    });

    jQuery(document).delegate('#free_sock_btn', 'click', function(e) {
        var access_code = jQuery('#free_sock_access_code').val();
        if (access_code == '') {
            swal('Input Kode', 'Silahkan input kode akses kasir', 'warning');
            return false;
        }
        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url:"{{  url('get_free_sock') }}",
            method:"POST",
            data:{_access_code:access_code},
            dataType:"json",
            success:function(r){
                if (r.status == '200') {
                    jQuery('#OfferModal').modal('hide');
                    jQuery('#free_sock_customer_mode').val('1');
                    jQuery('#free_sock_access_code').val('');
                    jQuery('#waiting_customer_label').removeClass('d-none');
                } else if (r.status == '419') {
                    swal('Salah', 'kode akses salah', 'warning');
                } else {
                    swal('Habis', 'free kaos kaki habis', 'warning');
                }
            }
        });
    });

    jQuery(document).ready(function(e) {
        jQuery('#_pt_id_complaint').val('');
        jQuery('#_exchange').val('');
        reloadWaitingForCheckout();
        reloadComplaint();
        reloadRefund();
        jQuery('#card_provider_content').addClass('d-none');
        jQuery('#card_provider_content').removeClass('d-flex');
        jQuery('#card_number_label').addClass('d-none');
        jQuery('#card_number_label').removeClass('d-flex');
        jQuery('#ref_number_label').addClass('d-none');
        jQuery('#ref_number_label').removeClass('d-flex');
        jQuery('#charge_label').addClass('d-none');
        jQuery('#charge_label').removeClass('d-flex');

        jQuery('#card_provider_content_two').addClass('d-none');
        jQuery('#card_provider_content_two').removeClass('d-flex');
        jQuery('#card_number_label_two').addClass('d-none');
        jQuery('#card_number_label_two').removeClass('d-flex');
        jQuery('#ref_number_label_two').addClass('d-none');
        jQuery('#ref_number_label_two').removeClass('d-flex');
        jQuery('#total_payment_two_label').addClass('d-none');
        jQuery('#total_payment_two_label').removeClass('d-flex');
        jQuery('#payment_type_content_two').addClass('d-none');
        jQuery('#payment_type_content_two').removeClass('d-flex');
        jQuery('#total_payment_two').addClass('d-none');
        jQuery('#total_payment_two').removeClass('d-flex');

        jQuery('#cust_id_label').on('change', function(){
            var query = jQuery(this).val();
            if (query == '') {
                jQuery('#cust_id').val('1');
                jQuery('#check_customer').attr('data-id', '');
                jQuery('#itemListCust').html('');
                jQuery('#itemListCust').fadeOut();
            }
        });

        jQuery('#cust_id_label').on('focus', function(){
            jQuery(this).val('');
            jQuery('#cust_id').val('1');
        });

        jQuery('#cust_id_label').on('keyup', function(){
            var query = jQuery(this).val();
            var type = 'cust';
            if(jQuery.trim(query) != '' || jQuery.trim(query) != null) {
                if (jQuery.trim(query).length > 3) {
                    jQuery.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        url:"{{  url('autocomplete_customer') }}",
                        method:"POST",
                        data:{query:query, type:type},
                        success:function(data){
                            jQuery('#itemListCust').fadeIn();
                            jQuery('#itemListCust').html(data);
                        }
                    });
                } else {
                    jQuery('#itemListCust').fadeOut();
                }
            } else {
                jQuery('#itemListCust').fadeOut();
            }
        });

        jQuery(document).delegate('#add_to_item_list_cust', 'click', function() {
            var cust_id = jQuery(this).attr('data-id');
            var cust_name = jQuery(this).text();
            jQuery('#cust_id').val(cust_id);
            jQuery('#cust_id_label').val(cust_name);
            jQuery('#check_customer').attr('data-id', cust_id);
            jQuery('#itemListCust').html('');
            jQuery('#itemListCust').fadeOut();
        });

        jQuery(document).delegate('#check_customer', 'click', function() {
            jQuery('#_mode').val('edit');
            var cust_id = jQuery(this).attr('data-id');
            jQuery('#_id').val(cust_id);
            jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type:'POST',
                url: "{{ url('check_customer')}}",
                data: {_cust_id:cust_id},
                dataType: 'json',
                success: function(r) {
                    if (r.status == '200') {
                        jQuery('#choosecustomer').modal('show');
                        jQuery('#ct_id').val(r.ct_id);
                        //jQuery("#ct_id option[value='" + r.ct_id + "']").prop("selected", true);
                        jQuery('#cust_name').val(r.cust_name);
                        jQuery('#cust_store').val(r.cust_store);
                        jQuery('#cust_phone').val(r.cust_phone);
                        jQuery('#cust_email').val(r.cust_email);
                        jQuery('#cust_province').val(r.cust_province);
                        reloadCity(r.cust_province);
                        setTimeout(() => {
                            jQuery('#cust_city').val(r.cust_city);
                        }, 500);
                        reloadSubdistrict(r.cust_city);
                        setTimeout(() => {
                            jQuery('#cust_subdistrict').val(r.cust_subdistrict);
                        }, 1000);
                        jQuery('#cust_address').val(r.cust_address);
                    } else if (r.status == '400') {
                        swal('Gagal', 'Gagal menampilkan detail', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        var refund_retur_table = jQuery('#RefundReturtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('refund_retur_datatables') }}",
                data : function (d) {
                    d.pt_id = jQuery('#refund_retur_pt_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'ptd_id', searchable: false},
            { data: 'article', name: 'article', orderable: false },
            { data: 'datetime', name: 'datetime', orderable: false },
            { data: 'qty', name: 'qty', orderable: false },
            { data: 'price', name: 'price', orderable: false },
            { data: 'action', name: 'action', orderable: false },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        jQuery('#choosecustomer').on('hide.bs.modal', function() {
            jQuery('#f_customer')[0].reset();
        });

        jQuery('#add_customer_btn').on('click', function() {
            jQuery('#_mode').val('add');
            jQuery('#choosecustomer').modal('show');
        });

        jQuery('#payment_option').on('change', function() {
            var payment_option = jQuery(this).val();

            jQuery('#pm_id_offline').val('').trigger('change');
            jQuery('#pm_id_offline_two').val('').trigger('change');
            jQuery('#cp_id').val('').trigger('change');
            jQuery('#cp_id_two').val('').trigger('change');

            jQuery('#total_payment').val('');
            jQuery('#total_payment_two').val('');
            jQuery('#return_payment').text('');
            if (payment_option == 'two') {
                jQuery('#payment_type_content_two').removeClass('d-none');
                jQuery('#payment_type_content_two').addClass('d-flex');
                jQuery('#total_payment_two_content').removeClass('d-none');
                jQuery('#total_payment_two_content').addClass('d-flex');
                jQuery('#total_payment_two_label').addClass('d-flex');
                jQuery('#total_payment_two_label').removeClass('d-none');
                jQuery('#total_payment_two').removeClass('d-none');
                jQuery('#total_payment_two').addClass('d-flex');
                jQuery('#return_payment_label').removeClass('d-flex');
                jQuery('#return_payment_label').addClass('d-none');
            } else {
                jQuery('#payment_type_content_two').addClass('d-none');
                jQuery('#payment_type_content_two').removeClass('d-flex');
                jQuery('#total_payment_two_content').addClass('d-none');
                jQuery('#total_payment_two_content').removeClass('d-flex');
                jQuery('#total_payment_two_label').addClass('d-none');
                jQuery('#total_payment_two_label').removeClass('d-flex');
                jQuery('#total_payment_two').addClass('d-none');
                jQuery('#total_payment_two').removeClass('d-flex');
                jQuery('#return_payment_label').removeClass('d-none');
                jQuery('#return_payment_label').addClass('d-flex');
                jQuery('#card_provider_content_two').addClass('d-none');
                jQuery('#card_provider_content_two').removeClass('d-flex');
                jQuery('#card_number_label_two').addClass('d-none');
                jQuery('#card_number_label_two').removeClass('d-flex');
                jQuery('#ref_number_label_two').addClass('d-none');
                jQuery('#ref_number_label_two').removeClass('d-flex');
            }
        });

        jQuery('#reload_refund_list').on('click', function() {
            reloadRefund();
        });

        function addRefundExchangeList(type, plst_id, pt_id)
        {
            jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type: "POST",
                data: {_type:type, _plst_id:plst_id, _pt_id:pt_id},
                dataType: 'json',
                url: "{{ url('refund_exchange_list') }}",
                success: function(r) {
                    if (r.status=='200') {
                        toast('Added', 'Added to refund list', 'success');
                    } else {
                        toast('Gagal', 'Gagal refund data', 'warning');
                    }
                }
            });
            return false;
        }

        jQuery(document).delegate('#select_refund_exchange_item', 'click', function()
        {
            var p_name = jQuery(this).attr('data-p_name');
            var pst_id = jQuery(this).attr('data-pst_id');
            var pt_id = jQuery(this).attr('data-pt_id');
            var pl_id = jQuery(this).attr('data-pl_id');
            var plst_id = jQuery(this).attr('data-plst_id');
            var sell_price = jQuery(this).attr('data-sell_price');
            var total_price_item = jQuery(this).attr('data-total_price');
            var nameset_price = jQuery(this).attr('data-nameset_price');
            var item_qty = jQuery(this).attr('data-item_qty');
            var total_row = parseFloat(jQuery('#total_row').val());
            jQuery('#total_row').val(total_row+1);
            var total_item = jQuery('#total_item_side').text();
            var total_price = jQuery('#total_price_side').text();
            if (jQuery(this).is(':checked')) {
                jQuery('#_pt_id_complaint').val(pt_id);
                jQuery('#total_item_side').text(parseInt(total_item) - 1);
                jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(total_price)) - parseFloat(total_price_item)));
                jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(total_price)) - parseFloat(total_price_item)));
                addRefundExchangeList('add', plst_id, pt_id);
                jQuery('#orderTable tr:last').after("" +
                    "<tr data-list-item class='bg-danger text-white pos_item_list mb-2' id='data_plst"+plst_id+"'> " +
                    "<td style='white-space: nowrap;'>"+p_name+"</td> <td>"+item_qty+"</td> " +
                    "<td><input type='number' class='form-control border-dark col-4 basicInput2"+pst_id+"' id='item_qty"+(total_row+1)+"' value='-"+item_qty+"' onchange='return changeReturQty("+(total_row+1)+", "+pst_id+", "+item_qty+")'></td> " +
                    " <td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" discount_percentage' id='discount_percentage"+(total_row+1)+"' value='0' onchange='return changeDiscountPercentage("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")'></td>" +
                    " <td><input type='number' class='form-control border-dark col-5 basicInput2"+pst_id+" discount_number' id='discount_number"+(total_row+1)+"' value='0' onchange='return changeDiscountNumber("+(total_row+1)+", "+pst_id+", "+(pls_qty)+")'></td>" +
                    "<td><input type='number' class='col-8 nameset_price' id='nameset_price"+(total_row+1)+"' value='"+nameset_price+"' onchange='return namesetPrice("+(total_row+1)+")' readonly/></td> " +
                    "<td><span id='sell_price_item"+(total_row+1)+"'>-"+addCommas(sell_price)+"</span></td> " +
                    "<td><span class='subtotal_item' id='subtotal_item"+(total_row+1)+"'>-"+addCommas(total_price_item)+"</span></td> " +
                    "<td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem"+(total_row+1)+"' onclick='return saveItem("+(total_row+1)+", "+pst_id+", "+sell_price+", "+plst_id+", "+pl_id+")'>" +
                    "<i class='fa fa-eye' style='display:none;'></i></a></div></td></tr>");
            } else {
                jQuery('#total_item_side').text(parseInt(total_item) + 1);
                jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + parseFloat(total_price_item)));
                jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + parseFloat(total_price_item)));
                //alert(plst_id);
                addRefundExchangeList('remove', plst_id, pt_id);
                jQuery('#orderTable tr#data_plst'+plst_id+'').remove();
            }
        });

        jQuery(document).delegate('#refund_invoice', 'change', function(e) {
            e.preventDefault();
            var invoice = jQuery('option:selected', this).text();
            var pt_id = jQuery(this).val();
            //alert(invoice+' '+pt_id);
            jQuery('#refund_retur_invoice_label').text(invoice);
            jQuery('#refund_retur_pt_id').val(pt_id);
            jQuery('#RefundExchangeModal').modal('show');
            refund_retur_table.draw();
        });

        @if (strtolower($data['user']->stt_name) == 'offline')
            jQuery('#posContent').show();
            jQuery('.sidebarPOS').show();
        @else
            jQuery('#posContent').hide();
            jQuery('.sidebarPOS').hide();
        @endif

        jQuery('#filter_product_btn').on('click', function(e) {
            e.preventDefault();
            jQuery('#FilterProductModal').modal('show');
        });

        jQuery('#payment_btn').on('click', function(e) {
            e.preventDefault();
            jQuery('#another_cost').val('');
            jQuery('#admin_cost').val('');
            jQuery('#unique_code').val('');
            if (b1g1_temp.length > 0) {
                jQuery('#note').val('[B1G1]');
            }
            if (jQuery('#free_sock_customer_mode').val() == '1') {
                jQuery('#payment-offline-popup').modal('show');
                var total_final_price_side = jQuery('#total_final_price_side').text();
                var total_nameset_side = jQuery('#total_nameset_side').text();
                var total = parseFloat(replaceComma(total_final_price_side));
                jQuery('#payment_total').text(addCommas(total));
            } else {
                jQuery('#OfferModal').modal('show');
            }
        });

        jQuery('#unique_code').on('change', function(){
            var unique = jQuery(this).val();
            var cost = jQuery('#another_cost').val();
            var admin = jQuery('#admin_cost').val();
            var shipping_cost = jQuery('#shipping_cost').val();
            if (cost == '') {
                jQuery('#another_cost').val(0);
                cost = 0;
            }
            if (admin == '') {
                jQuery('#admin_cost').val(0);
                admin = 0;
            }
            if (unique == '') {
                jQuery(this).val(0);
                unique = 0;
            }
            if (shipping_cost == '') {
                jQuery('#shipping_cost').val(0);
                shipping_cost = 0;
            }
            var payment_total = jQuery('#total_final_price_side').text();
            var final_total = parseFloat(replaceComma(payment_total)) + parseFloat(unique) + parseFloat(cost) - parseFloat(admin) + parseFloat(shipping_cost);
            jQuery('#payment_total').text(addCommas(final_total));
        });

        jQuery('#another_cost').on('change', function(){
            var cost = jQuery(this).val();
            var admin = jQuery('#admin_cost').val();
            var unique = jQuery('#unique_code').val();
            var shipping_cost = jQuery('#shipping_cost').val();
            if (unique == '') {
                jQuery('#unique_code').val(0);
                unique = 0;
            }
            if (cost == '') {
                jQuery(this).val(0);
                cost = 0;
            }
            if (admin == '') {
                jQuery('#admin_cost').val(0);
                admin = 0;
            }
            if (shipping_cost == '') {
                jQuery('#shipping_cost').val(0);
                shipping_cost = 0;
            }
            var final_total = jQuery('#total_final_price_side').text();
            var final_total_admin = parseFloat(replaceComma(final_total)) + parseFloat(cost) - parseFloat(admin) + parseFloat(unique) + parseFloat(shipping_cost);
            jQuery('#payment_total').text(addCommas(final_total_admin));
        });

        jQuery('#admin_cost').on('change', function(){
            var admin = jQuery(this).val();
            var cost = jQuery('#another_cost').val();
            var unique = jQuery('#unique_code').val();
            var shipping_cost = jQuery('#shipping_cost').val();
            if (unique == '') {
                jQuery('#unique_code').val(0);
                unique = 0;
            }
            if (admin == '') {
                jQuery(this).val(0);
                admin = 0;
            }
            if (cost == '') {
                jQuery('#another_cost').val(0);
                cost = 0;
            }
            if (shipping_cost == '') {
                jQuery('#shipping_cost').val(0);
                shipping_cost = 0;
            }
            var final_total = jQuery('#total_final_price_side').text();
            var final_total_admin = parseFloat(replaceComma(final_total)) + parseFloat(cost) - parseFloat(admin) + parseFloat(unique) + parseFloat(shipping_cost);
            jQuery('#payment_total').text(addCommas(final_total_admin));
        });

        jQuery('#shipping_cost').on('change', function(){
            var shipping_cost = jQuery(this).val();
            var cost = jQuery('#another_cost').val();
            var admin = jQuery('#admin_cost').val();
            var unique = jQuery('#unique_code').val();
            if (unique == '') {
                jQuery('#unique_code').val(0);
                unique = 0;
            }
            if (cost == '') {
                jQuery('#another_cost').val(0);
                cost = 0;
            }
            if (admin == '') {
                jQuery('#admin_cost').val(0);
                admin = 0;
            }
            if (shipping_cost == '') {
                jQuery(this).val(0);
                shipping_cost = 0;
            }
            var final_total = jQuery('#total_final_price_side').text();
            var final_total_admin = parseFloat(replaceComma(final_total)) + parseFloat(cost) - parseFloat(admin) + parseFloat(unique) + parseFloat(shipping_cost);
            jQuery('#payment_total').text(addCommas(final_total_admin));
        });

        jQuery(document).delegate('#free_sock_no_btn', 'click', function() {
            jQuery('#payment-offline-popup').modal('show');
            var total_final_price_side = jQuery('#total_final_price_side').text();
            var total_nameset_side = jQuery('#total_nameset_side').text();
            var total = parseFloat(replaceComma(total_final_price_side));
            jQuery('#payment_total').text(addCommas(total));
        })

        jQuery('#total_payment').on('keyup', function(e) {
            e.preventDefault();
            var total_price = jQuery('#payment_total').text();
            var total_payment = jQuery(this).val();
            var return_payment = parseFloat(total_payment) - parseFloat(replaceComma(total_price));
            var method = jQuery('#payment_option option:selected').val();
            if (method == 'two') {
                if (total_payment == '') {
                    jQuery('#total_payment_two').val(total_price);
                } else {
                    jQuery('#total_payment_two').val(parseFloat(replaceComma(total_price)) - total_payment);
                }
            } else {
                if (total_payment == '') {
                    jQuery('#return_payment').text('');
                } else {
                    jQuery('#return_payment').text(addCommas(return_payment));
                }
            }
        });

        jQuery('#charge').on('change', function(e) {
            e.preventDefault();
            var charge = jQuery(this).val();
            var payment_total = jQuery('#payment_total').text();
            var total_charge = parseFloat(replaceComma(payment_total))/100 * parseFloat(charge);
            jQuery('#charge_total').val(total_charge);
            if (charge != '') {
                jQuery('#payment_total').text(addCommas(parseFloat(replaceComma(payment_total)) + total_charge));
            } else {
                jQuery('#payment_total').text(jQuery('#total_final_price_side').text());
            }
        });

        jQuery('#pm_id_offline').on('change', function(e) {
            e.preventDefault();
            var label = jQuery('#pm_id_offline option:selected').text();
            if (label == 'DEBIT CARD') {
                jQuery('#card_provider_content').removeClass('d-none');
                jQuery('#card_provider_content').addClass('d-flex');
                jQuery('#card_number_label').removeClass('d-none');
                jQuery('#card_number_label').addClass('d-flex');
                jQuery('#ref_number_label').removeClass('d-none');
                jQuery('#ref_number_label').addClass('d-flex');
                jQuery('#return_payment').text('');
                jQuery('#charge_label').addClass('d-none');
                jQuery('#charge_label').removeClass('d-flex');
                jQuery('#charge').val('');
                jQuery('#charge_total').val('');
            } else if (label == 'CREDIT CARD') {
                jQuery('#card_provider_content').removeClass('d-none');
                jQuery('#card_provider_content').addClass('d-flex');
                jQuery('#card_number_label').removeClass('d-none');
                jQuery('#card_number_label').addClass('d-flex');
                jQuery('#ref_number_label').removeClass('d-none');
                jQuery('#ref_number_label').addClass('d-flex');
                jQuery('#return_payment').text('');
                jQuery('#charge_label').addClass('d-flex');
                jQuery('#charge_label').removeClass('d-none');
            } else if (label == 'CASH') {
                jQuery('#card_provider_content').addClass('d-none');
                jQuery('#card_provider_content').removeClass('d-flex');
                jQuery('#card_number_label').addClass('d-none');
                jQuery('#card_number_label').removeClass('d-flex');
                jQuery('#ref_number_label').addClass('d-none');
                jQuery('#ref_number_label').removeClass('d-flex');
                jQuery('#charge_label').addClass('d-none');
                jQuery('#charge_label').removeClass('d-flex');
                jQuery('#charge').val('');
                jQuery('#charge_total').val('');
            } else {
                jQuery('#card_number_label').addClass('d-none');
                jQuery('#card_number_label').removeClass('d-flex');
                jQuery('#card_provider_content').addClass('d-none');
                jQuery('#card_provider_content').removeClass('d-flex');
                jQuery('#ref_number_label').removeClass('d-none');
                jQuery('#ref_number_label').addClass('d-flex');
                jQuery('#return_payment').text('');
                jQuery('#charge_label').addClass('d-none');
                jQuery('#charge_label').removeClass('d-flex');
                jQuery('#charge').val('');
                jQuery('#charge_total').val('');
            }
        });

        jQuery('#pm_id_offline_two').on('change', function(e) {
            e.preventDefault();
            var label = jQuery('#pm_id_offline_two option:selected').text();
            //alert(label);
            if (label == 'DEBIT CARD') {
                jQuery('#card_provider_content_two').removeClass('d-none');
                jQuery('#card_provider_content_two').addClass('d-flex');
                jQuery('#card_number_label_two').removeClass('d-none');
                jQuery('#card_number_label_two').addClass('d-flex');
                jQuery('#ref_number_label_two').removeClass('d-none');
                jQuery('#ref_number_label_two').addClass('d-flex');
            } else if (label == 'CASH') {
                jQuery('#card_provider_content_two').addClass('d-none');
                jQuery('#card_provider_content_two').removeClass('d-flex');
                jQuery('#card_number_label_two').addClass('d-none');
                jQuery('#card_number_label_two').removeClass('d-flex');
                jQuery('#ref_number_label_two').addClass('d-none');
                jQuery('#ref_number_label_two').removeClass('d-flex');
            } else {
                jQuery('#card_number_label_two').addClass('d-none');
                jQuery('#card_number_label_two').removeClass('d-flex');
                jQuery('#card_provider_content_two').addClass('d-none');
                jQuery('#card_provider_content_two').removeClass('d-flex');
                jQuery('#ref_number_label_two').removeClass('d-none');
                jQuery('#ref_number_label_two').addClass('d-flex');
            }
        });

        jQuery('#checkout_btn').on('click', function(e) {
            var payment_option = jQuery('#payment_option option:selected').val();
            var payment_option_label = jQuery('#payment_option option:selected').text();
            var payment_method = jQuery('#pm_id_offline').val();
            var payment_method_label = jQuery('#pm_id_offline option:selected').text();
            var payment_method_two = jQuery('#pm_id_offline_two').val();
            var payment_method_two_label = jQuery('#pm_id_offline_two option:selected').text();
            var payment_total = parseInt(replaceComma(jQuery('#payment_total').text()));
            var total_payment = jQuery('#total_payment').val();
            var total_payment_two = jQuery('#total_payment_two').val();
            var cp_id = jQuery('#cp_id').val();
            var cp_id_two = jQuery('#cp_id_two').val();
            if (payment_total > 0) {
                if (payment_option == '') {
                    swal("Metode Pembayaran","Silahkan pilih metode pembayaran", "warning");
                    return false;
                }
                if (payment_option != '' && payment_method == '') {
                    swal("Jenis Pembayaran","Silahkan pilih jenis pembayaran", "warning");
                    return false;
                }
                if (payment_option_label != '2 Metode' && payment_method_label == 'CASH' && total_payment < payment_total) {
                    swal("Jumlah Dibayar","Silahkan periksa jumlah dibayar", "warning");
                    return false;
                }
                if (payment_method_label == 'DEBIT CARD' && cp_id == '' || payment_method_label == 'CREDIT CARD' && cp_id == '') {
                    swal("Penyedia Kartu","Silahkan pilih penyedia kartu", "warning");
                    return false;
                }
                if (payment_option_label == '2 Metode' && payment_method_two == '') {
                    swal("Jenis Pembayaran Dua","Silahkan pilih jenis pembayaran kedua", "warning");
                    return false;
                }
                if (payment_method_two_label == 'DEBIT CARD' && cp_id_two == '' || payment_method_two_label == 'CREDIT CARD' && cp_id_two == '') {
                    swal("Penyedia Kartu Kedua","Silahkan pilih penyedia kartu kedua", "warning");
                    return false;
                }
                if (payment_method_two != '' && total_payment == '') {
                    swal("Jumlah Dibayar Pertama","Silahkan jumlah dibayar pertama", "warning");
                    return false;
                }
                if (payment_method_two != '' && total_payment_two == '') {
                    swal("Jumlah Dibayar Kedua","Silahkan jumlah dibayar kedua", "warning");
                    return false;
                }
                jQuery("#InputCodeModal").modal('show');
            } else {
                jQuery("#InputCodeModal").modal('show');
            }
        });

        jQuery('#f_access').on('submit', function(e) {
            e.preventDefault();
            var u_secret_code = jQuery('#u_secret_code').val();
            var type = jQuery('#_type').val();
            if (jQuery.trim(u_secret_code)=='') {
                swal("Kode Akses","Silahkan input kode akses anda, jika lupa tanya administrator", "warning");
                return false;
            } else {
                jQuery.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    type: "POST",
                    data: {_u_secret_code:u_secret_code},
                    dataType: 'json',
                    url: "{{ url('check_secret_code') }}",
                    success: function(r) {
                        if (r.status=='200') {
                            checkout();
                        } else {
                            swal('Salah', 'Kode salah', 'warning');
                        }
                    }
                });
                return false;
            }
        });

        jQuery('#product_name_input').on('keyup', function(){
            var query = jQuery(this).val();
            var type = jQuery('#std_id option:selected').text();
            var item_type = jQuery('#item_type option:selected').val();
            var std_id = jQuery('#std_id').val();
            if(jQuery.trim(query) != '' || jQuery.trim(query) != null) {
                if (jQuery.trim(query).length > 2) {
                    jQuery.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        url:"{{  url('autocomplete_by_waiting') }}",
                        method:"POST",
                        data:{query:query, type:type, _item_type:item_type, _std_id:std_id},
                        success:function(data){
                            jQuery('#itemList').fadeIn();
                            jQuery('#itemList').html(data);
                        }
                    });
                } else {
                    jQuery('#itemList').fadeOut();
                }
            } else {
                jQuery('#itemList').fadeOut();
            }
        });

        jQuery('#invoice_input').on('keyup', function(){
            var query = jQuery(this).val();
            if(jQuery.trim(query) != '' || jQuery.trim(query) != null) {
                if (jQuery.trim(query).length > 4) {
                    jQuery.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        url:"{{  url('autocomplete_invoice_offline') }}",
                        method:"POST",
                        data:{query:query},
                        success:function(data){
                            jQuery('#itemList').fadeIn();
                            jQuery('#itemList').html(data);
                        }
                    });
                } else {
                    jQuery('#itemList').fadeOut();
                }
            } else {
                jQuery('#itemList').fadeOut();
            }
        });

        jQuery('#product_name_input').on('change', function(){
            var query = jQuery(this).val();
            if(jQuery.trim(query) == '' || jQuery.trim(query) != null) {
                jQuery('#itemList').fadeOut();
            }
        });

        jQuery('#cust_province').on('change', function() {
            var province = jQuery(this).val();
            reloadCity(province);
        });

        jQuery('#cust_city').on('change', function() {
            var city = jQuery(this).val();
            reloadSubdistrict(city);
        });

        jQuery('#f_customer').on('submit', function(e) {
            e.preventDefault();
            jQuery("#save_customer_btn").html('Proses ..');
            jQuery("#save_customer_btn").attr("disabled", true);
            var formData = new FormData(this);
            var std_id = jQuery('#std_id').val();
            if (std_id == '') {
                swal('Pilih Divisi', 'Silahkan pilih divisi terlebih dahulu', 'warning');
                return false;
            }
            //alert(formData);
            jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type:'POST',
                url: "{{ url('cust_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    jQuery("#save_customer_btn").html('Simpan');
                    jQuery("#save_customer_btn").attr("disabled", false);
                    if (data.status == '200') {
                        jQuery('#cust_id').val(1);
                        jQuery('#cust_id_label').val('');
                        jQuery("#f_customer")[0].reset();
                        jQuery("#choosecustomer").modal('hide');
                        toast('Berhasil', 'Data berhasil disimpan', 'success');
                    } else if (data.status == '400') {
                        jQuery("#choosecustomer").modal('hide');
                        toast('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });
    });

    jQuery('#cust_phone').on('change', function() {
        var cust_phone = jQuery(this).val();
        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: "POST",
            data: {_cust_phone:cust_phone},
            dataType: 'json',
            url: "{{ url('check_exists_customer')}}",
            success: function(r) {
                if (r.status == '200') {
                    swal('No Telepon', 'Nomor telepon sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                    jQuery('#cust_phone').val('');
                    return false;
                }
            }
        });
    });

    jQuery(document).delegate('#f_voucher', 'submit', function(e) {
        e.preventDefault();
        var code = jQuery('#voucher_code').val();
        var item = shoes_voucher_temp;
        var total_final = jQuery('#total_final_price_side').text();
        var total = replaceComma(total_final);
        if (shoes_voucher_temp.length <= 0) {
            swal('Tidak ada sepatu', 'Tidak ada item yang berupa sepatu, voucher hanya berlaku untuk sepatu', 'warning');
            return false;
        }
        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: "POST",
            data: {code:code, item:item},
            dataType: 'json',
            url: "{{ url('verify_voucher')}}",
            success: function(r) {
                if (r.status == '200') {
                    // ditemukan
                    var new_total = parseFloat(total) - parseFloat(r.sell) + parseFloat(r.value);
                    jQuery('#_voc_id').val(r.voc_id);
                    jQuery('#_voc_pst_id').val(r.pst_id);
                    jQuery('#_voc_value').val(r.disc_value);
                    jQuery('#_voc_article').text(r.article);
                    jQuery('#_voc_bandrol').text(addCommas(r.bandrol));
                    jQuery('#_voc_disc').text(addCommas(r.disc));
                    jQuery('#_voc_disc_type').text(addCommas(r.disc_type));
                    jQuery('#_voc_disc_value').text('('+addCommas(r.disc_value)+')');
                    jQuery('#_voc_value_show').text(addCommas(r.value));
                    jQuery('#total_final_price_side').text(addCommas(new_total));
                    sell_price_voc = r.sell;
                    value_price_voc = r.value;
                    jQuery('#voucher_information').removeClass("d-none");
                    jQuery('#voucher_code').prop('disabled', true);
                } else if (r.status == '201') {
                    // salah
                    alert('beda platform');
                } else if (r.status == '202') {
                    // salah
                    alert('sudah pernah dipakai');
                } else if (r.status == '203') {
                    // salah
                    alert('sudah pernah dipakai namun belum 1 bulan');
                } else if (r.status == '204') {
                    // salah
                    alert('tidak ada item untuk diskon');
                } else {
                    alert('kode salah');
                }
            }
        });
    });

    jQuery(document).delegate('#cancel_voucher', 'click', function(e) {
        e.preventDefault();
        var total_final = jQuery('#total_final_price_side').text();
        var total = replaceComma(total_final);
        var new_total = parseFloat(total) - parseFloat(value_price_voc) + parseFloat(sell_price_voc);
        jQuery('#_voc_pst_id').val('');
        jQuery('#_voc_value').val('');
        jQuery('#_voc_id').val('');
        jQuery('#voucher_code').val("");
        jQuery('#voucher_information').addClass("d-none");
        sell_price_voc = 0;
        value_price_voc = 0;
        jQuery('#total_final_price_side').text(addCommas(new_total));
        jQuery('#voucher_code').prop('disabled', false);
    });

    jQuery(document).ready(function () {
        // Event listener to add a new voucher input field
        jQuery(document).on('click', '.add-voucher', function () {
            let newField = `
            <div class="input-group mb-3">
                <input type="text" name="voucher-list[]" class="form-control" placeholder="Kode Voucher" value="">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary remove-voucher" type="button">-</button>
                </div>
            </div>
        `;
            jQuery("#voucher-container").append(newField);
        });

        // Event listener to remove a voucher input field
        jQuery(document).on('click', '.remove-voucher', function () {
            jQuery(this).closest('.input-group').remove();
        });
    });

    jQuery(document).delegate('#f_add_voucher', 'submit', function(e) {
        e.preventDefault();
        var cust_phone = jQuery(this).val();

        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        var code = jQuery('#voucher_code').val();
        var item = shoes_voucher_temp;
        var total_final = jQuery('#total_final_price_side').text();
        var total = replaceComma(total_final);

        var formData = jQuery('#f_add_voucher').serializeArray();

        jQuery.ajax({
            type:"POST",
            url: "{{ url('verify-vouchers')}}",
            data:
                {
                    formData: formData,
                    item: item,
                },
            dataType:'json',
            success: function(r) {
                console.log(r.status);
                if (r.status == '200') {
                    // ditemukan
                    var new_total = parseFloat(total) - parseFloat(r.sell) + parseFloat(r.value);
                    jQuery('#_voc_id').val(r.voc_id);
                    jQuery('#_voc_pst_id').val(r.pst_id);
                    jQuery('#_voc_value').val(r.disc_value);
                    jQuery('#_voc_article').text(r.article);
                    jQuery('#_voc_bandrol').text(addCommas(r.bandrol));
                    jQuery('#_voc_disc').text(addCommas(r.disc));
                    jQuery('#_voc_disc_type').text(addCommas(r.disc_type));
                    jQuery('#_voc_disc_value').text('('+addCommas(r.disc_value)+')');
                    jQuery('#_voc_value_show').text(addCommas(r.value));
                    jQuery('#total_final_price_side').text(addCommas(new_total));
                    sell_price_voc = r.sell;
                    value_price_voc = r.value;
                    jQuery('#voucher_information').removeClass("d-none");
                    jQuery('#voucher_code').prop('disabled', true);
                } else if (r.status == '201') {
                    // salah
                    alert('beda platform');
                } else if (r.status == '202') {
                    // salah
                    alert('sudah pernah dipakai');
                } else if (r.status == '203') {
                    // salah
                    alert('sudah pernah dipakai namun belum 1 bulan');
                } else if (r.status == '204') {
                    // salah
                    alert('tidak ada item untuk diskon');
                } else {
                    alert('kode salah');
                }
            }
        });
    });
</script>
