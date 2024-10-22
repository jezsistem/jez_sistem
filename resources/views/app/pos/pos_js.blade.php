<script src="{{ asset('pos/js') }}/plugin.bundle.min.js"></script>
<script src="{{ asset('pos/js') }}/bootstrap.bundle.min.js"></script>
<script src="{{ asset('pos/js') }}/jquery.dataTables.min.js"></script>
<script src="{{ asset('pos/js') }}/multiple-select.min.js"></script>
<script src="{{ asset('pos/js') }}/sweetalert.js"></script>
<script src="{{ asset('pos/js') }}/sweetalert1.js"></script>
<script src="{{ asset('pos/js') }}/script.bundle.js"></script>
<script src="{{ asset('cdn/jquery.toast.min.js') }}"></script>
<script src="{{ asset('cdn/select2.min.js') }}"></script>

<script>
    var shoes_voucher_temp = [];
    var sell_price_voc = 0;
    var value_price_voc = 0;

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
            url: "{{ url('reload_refund')}}",
            success: function(r) {
                jQuery('#refund_reload').html(r);
                toast('Reloaded', 'Refund berhasil direload', 'success');
            }
        });
    };

    function changeQty(row, pst_id)
    {
        console.log("init pst"+pst_id);
        var pl_id = jQuery('#orderList'+row).closest('tr').find("#pl_id option:selected").text();
        var item_qty = jQuery('#item_qty'+row).val();
        var sell_price_item = replaceComma(jQuery('#sell_price_item'+row).text());
        var subtotal_item = replaceComma(jQuery('#subtotal_item'+row).text());
        var total_row = jQuery('tr[data-list-item]').length;
        var shipping_cost_side = replaceComma(jQuery('#shipping_cost_side').text());
        var total_nameset_side = replaceComma(jQuery('#total_nameset_side').text());
        var str = pl_id.replace(/\[|\]/g, '');
        var pls_qty = str.split(' ');
        var discount = jQuery('#reseller_disc'+row).val();
        var discount_number = jQuery('#reseller_disc_number'+row).val();

        if (parseInt(item_qty) > parseInt(pls_qty[1]) || parseInt(item_qty) < 0) {
            swal('Melebihi Stok', 'Jumlah item tidak boleh melebihi atau kurang jumlah pada BIN', 'warning');
            jQuery('#item_qty'+row).val('0');
            jQuery('#subtotal_item'+row).text('');
            jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(jQuery('#total_price_side').text()))-subtotal_item));
            jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(jQuery('#total_price_side').text()))+parseInt(total_nameset_side)+parseInt(shipping_cost_side)));
            return false;
        }

        if (discount == '' || discount == '-') {
            discount = 0;
        }

        // Pemeriksaan apakah discount_number terisi
        if (discount_number !== '' && discount_number !== '-') {
            // Menghitung subtotal dengan discount_number
            var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item) - parseFloat(discount_number));
        } else {
            // Menghitung subtotal dengan menggunakan logika sebelumnya jika discount_number tidak terisi
            var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item) - (parseFloat(sell_price_item)/100 * discount));
        }

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
        jQuery('#total_final_price_side').text(addCommas(final_price+parseFloat(replaceComma(shipping_cost_side))+nameset));
    }
    
    function changeReturQty(row, pst_id, qty)
    {
        jQuery('#item_qty'+row).val(-Math.abs(jQuery('#item_qty'+row).val()));
        var item_qty = jQuery('#item_qty'+row).val();
        var sell_price_item = replaceComma(jQuery('#sell_price_item'+row).text());
        var subtotal_item = replaceComma(jQuery('#subtotal_item'+row).text());
        var total_row = jQuery('tr[data-list-item]').length;
        var shipping_cost_side = replaceComma(jQuery('#shipping_cost_side').text());
        var total_nameset_side = replaceComma(jQuery('#total_nameset_side').text());
        var discount = jQuery('#reseller_disc'+row).val();
        var discount_number = jQuery('#reseller_disc_number'+row).val();

        if (parseInt(Math.abs(item_qty)) > parseInt(qty)) {
            swal('Melebihi Pembelian', 'Jumlah item tidak boleh melebihi jumlah pada pembelian', 'warning');
            jQuery('#item_qty'+row).val(-Math.abs(qty));
            return false;
        }
        if (discount == '' || discount == '-') {
            discount = 0;
        }
        // Pemeriksaan apakah discount_number terisi
        if (discount_number !== '' && discount_number !== '-') {
            // Menghitung subtotal dengan discount_number
            var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item) - parseFloat(discount_number));
        } else {
            // Menghitung subtotal dengan menggunakan logika sebelumnya jika discount_number tidak terisi
            var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item) - (parseFloat(sell_price_item)/100 * discount));
        }

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
        jQuery('#total_final_price_side').text(addCommas(final_price+parseFloat(replaceComma(shipping_cost_side))+nameset));
    }

    function resellerDisc(index)
    {
        if (jQuery('#reseller_disc'+index).val() < 0) {
            swal('Minus', 'diskon reseller tidak boleh minus', 'warning');
            jQuery('#reseller_disc'+index).val('');
            return false;
        }
        var discount = jQuery('#reseller_disc'+index).val();
        var discount_number = jQuery('#reseller_disc_number'+index).val();
        var sell_price_item = jQuery('#sell_price_item'+index).text();
        var subtotal_item = jQuery('#subtotal_item'+index).text();
        var total_price_side = jQuery('#total_price_side').text();
        var shipping_cost_side = jQuery('#shipping_cost_side').text();
        var total_final_price_side = jQuery('#total_final_price_side').text();
        var total_row = jQuery('tr[data-list-item]').length;
        if (jQuery.trim(discount) == '' || jQuery.trim(discount) == 0) {
            var subtotal_after_disc = parseFloat(replaceComma(sell_price_item));

            jQuery('#reseller_disc_number'+index).val('');
        } else {
            var subtotal_after_disc = parseFloat(replaceComma(sell_price_item)) - (parseFloat(replaceComma(sell_price_item))/100 * parseFloat(discount));

            // calcualte how much in nominal and show
            var nominal = parseFloat(replaceComma(sell_price_item))/100 * parseFloat(discount);

            jQuery('#reseller_disc_number'+index).val(nominal);
        }
        jQuery('#item_qty'+index).val('1');
        jQuery('#subtotal_item'+index).text(addCommas(subtotal_after_disc));
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
        jQuery('#total_final_price_side').text(addCommas(final_price+parseFloat(replaceComma(shipping_cost_side))+nameset));
    }

    function resellerDiscNumber(index)
    {
        if (jQuery('#reseller_disc_number'+index).val() < 0) {
            swal('Minus', 'diskon reseller tidak boleh minus', 'warning');
            jQuery('#reseller_disc'+index).val('');
            return false;
        }
        var discount_percentage = jQuery('#reseller_disc'+index).val();
        var discount = jQuery('#reseller_disc_number'+index).val();
        var sell_price_item = jQuery('#sell_price_item'+index).text();
        var subtotal_item = jQuery('#subtotal_item'+index).text();
        var total_price_side = jQuery('#total_price_side').text();
        var shipping_cost_side = jQuery('#shipping_cost_side').text();
        var total_final_price_side = jQuery('#total_final_price_side').text();
        var total_row = jQuery('tr[data-list-item]').length;
        if (jQuery.trim(discount) == '' || jQuery.trim(discount) == 0) {
            var subtotal_after_disc = parseFloat(replaceComma(sell_price_item));

            jQuery('#reseller_disc'+index).val('');
        } else {
            var subtotal_after_disc = parseFloat(replaceComma(sell_price_item)) - discount;

            // calculate how much percentage and change discount_percentage value

            var percentage = (discount/subtotal_after_disc)*100;

            jQuery('#reseller_disc'+index).val(percentage.toFixed(2));
        }
        jQuery('#item_qty'+index).val('1');
        jQuery('#subtotal_item'+index).text(addCommas(subtotal_after_disc));
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
        jQuery('#total_final_price_side').text(addCommas(final_price+parseFloat(replaceComma(shipping_cost_side))+nameset));
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
        jQuery('#total_final_price_side').text(addCommas(total_nameset + parseFloat(replaceComma(jQuery('#total_price_side').text()))));
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
            loader: true,
            loaderBg: '#072544',
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

    function reloadCustomerByDivision(std_id)
    {
        //alert(type);
        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: "GET",
            dataType: 'html',
            data: {_std_id:std_id},
            url: "{{ url('reload_customer_by_division')}}",
            success: function(r) {
                jQuery('#cust_id_reload').html(r);
            }
        });
    };

    function reloadSubCustomerByDivision()
    {
        //alert(type);
        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: "GET",
            dataType: 'html',
            url: "{{ url('reload_sub_customer_by_division')}}",
            success: function(r) {
                jQuery('#sub_cust_id_reload').html(r);
            }
        });
    };

    function reloadComplaint()
    {
        jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
        jQuery.ajax({
            type:'POST',
            url: "{{ url('check_complaint')}}",
            dataType: 'json',
            success: function(r) {
                // jQuery('#orderTable tr:last').after(r)
                if (r.status == '200') {
                    jQuery('#complaint_info').removeClass('d-none');
                    jQuery('#complaint_invoice').html(r.invoice);
                } else {
                    jQuery('#complaint_info').addClass('d-none');
                    jQuery('#complaint_invoice').html('');
                }
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
        return false;
    }

    function saveItem(row, pst_id, price, plst_id, price_item_discount)
    {
        //alert(row, pst_id, price, plst_id);
        var pl_id = jQuery('#orderList'+row).closest('tr').find("#pl_id option:selected").val();
        if (typeof pl_id === 'undefined') {
            pl_id = jQuery('#pl_id'+row).val();
        }
        //alert(pl_id);
        var pt_id = jQuery('#_pt_id').val();
        var pt_id_complaint = jQuery('#_pt_id_complaint').val();
        var discount = jQuery('#reseller_disc'+row).val();
        var discount_number = jQuery('#reseller_disc_number'+row).val();
        var sell_price_item = jQuery('#sell_price_item'+row).text();
        var final_price = jQuery('#total_final_price_side').text();
        var subtotal_item = jQuery('#subtotal_item'+row).text();
        var marketplace_price = jQuery('#marketplace_price'+row).val();
        var nameset_price = jQuery('#nameset_price'+row).val();
        var exchange = jQuery('#_exchange').val();
        var item_qty = jQuery('#item_qty'+row).val();
        var cross = jQuery('#cross_order').val();
        var st_id = jQuery('#st_id').val();
        var voc_pst_id = jQuery('#_voc_pst_id').val();
        var voc_value = jQuery('#_voc_value').val();
        // var subsidi_ongkir = jQuery('#subsidi_ongkir').val(); // Add this line
        // var real_price = jQuery('#real_price').val(); 
        if (st_id == '4') {
            cross = 'false';
        }
        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type:'POST',
            url: "{{ url('save_transaction_detail')}}",
            data: {
                voc_pst_id:voc_pst_id,
                voc_value:value_price_voc,
                _cross:cross,
                _item_qty:item_qty,
                _plst_id:plst_id,
                _pt_id_complaint:pt_id_complaint,
                _final_price:parseFloat(replaceComma(final_price)),
                _exchange:exchange,
                _pt_id:pt_id,
                _pl_id:pl_id,
                _pst_id:pst_id,
                _price:price,
                _discount:discount,
                _discount_number: discount_number,
                _price_item_discount:price_item_discount,
                _marketplace_price:marketplace_price,
                _sell_price_item:replaceComma(sell_price_item),
                _subtotal_item:replaceComma(subtotal_item), _nameset_price:nameset_price},
                // _subsidi_ongkir: subsidi_ongkir,          // Add this line
                // _real_price: parseFloat(replaceComma(real_price))
            dataType: 'json',
            success: function(r) {
                jQuery.noConflict();
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

    function deleteItem(pst_id, price, index)
    {
        var subtotal_item = jQuery('#subtotal_item'+index).text();
        var total_item = jQuery('#total_item_side').text();
        var total_price = jQuery('#total_price_side').text();
        var total_final = jQuery('#total_final_price_side').text();
        var shipping_cost_side = jQuery('#shipping_cost_side').text();
        jQuery('#total_item_side').text(parseInt(total_item) - 1);
        var total_row = jQuery('tr[data-list-item]').length;
        var total_nameset = replaceComma(jQuery('#total_nameset_side').text());
        var nameset = jQuery('#nameset_price'+index).val();
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
        jQuery('#cancel_voucher').trigger('click');
        jQuery('#total_nameset_side').text(addCommas(final_nameset));
        jQuery('#total_price_side').text(addCommas(final_price - subtotal_item));
        jQuery('#total_final_price_side').text(addCommas(final_price + final_nameset - subtotal_item+parseFloat(replaceComma(shipping_cost_side))));
        jQuery('#orderList'+index).remove();
    }

    jQuery(document).delegate('#add_to_item_list', 'click', function(e) {
        e.preventDefault();
        if (jQuery('#cust_id').val() == '') {
            swal('Customer', 'Pilih customer', 'warning');
            jQuery('#product_name_input').val('');
            return false;
        }
        jQuery('#product_name_input').val('');
        jQuery('#itemList').html('');
        jQuery('#itemList').fadeOut();
        jQuery('#_exchange').val('true');
        var p_name = jQuery(this).attr('data-p_name');
        var bin = jQuery(this).attr('data-bin');
        var pst_id = jQuery(this).attr('data-pst_id');
        var ps_qty = jQuery(this).attr('data-ps_qty');
        var pls_qty = jQuery(this).attr('data-pls_qty');
        var psc_id = jQuery(this).attr('data-psc_id');
        var sell_price = jQuery(this).attr('data-sell_price');
        var sell_price_discount = jQuery(this).attr('data-sell_price_discount');
        var total_item = jQuery('#total_item_side').text();
        var total_price = jQuery('#total_price_side').text();
        var total_final_price = jQuery('#total_final_price_side').text();
        var total_row = parseFloat(jQuery('#total_row').val());
        jQuery('#total_row').val(total_row+1);
        var pos_item_list = jQuery('.pos_item_list'+pst_id).length;
        var cross = jQuery(this).attr('data-cross');
        var ok = jQuery(this).attr('data-ok');
        if (cross == 'true') {
            jQuery('#cross_order').val('true');
            if (ok == false) {
                swal('Aging', 'Mohon maaf, aging belum mencukupi', 'warning');
                return false;
            }
        }
        if (psc_id == '1') {
            var bandrol = jQuery(this).attr('data-bandrol');
            shoes_voucher_temp.push(pst_id+'-'+bandrol+'-'+sell_price);
            console.log(shoes_voucher_temp);
        }
        toast('Ditambah', 'Item berhasil ditambah', 'success');
        jQuery('#total_item_side').text(parseInt(total_item) + 1);
        jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + parseFloat(sell_price)));
        jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + parseFloat(sell_price)));
        var type = jQuery('#std_id option:selected').text();
        if (type != 'DROPSHIPPER' && type != 'RESELLER' && type != 'WHATSAPP' && type != 'WEBSITE') {
            jQuery('#orderTable tr:last').after("" +
                " <tr data-list-item class='pos_item_list"+(pst_id)+" mb-2 bg-light-primary' id='orderList"+(total_row+1)+"'>" +
                " <td><span style='font-size:14px; white-space:nowrap;'>"+p_name+"</span></td>" +
                " <td>"+bin+"</td>" +
                " <td><input type='number' class='col-10' id='reseller_disc"+(total_row+1)+"' onchange='return resellerDisc("+(total_row+1)+")'/></td>" +
                " <td><input type='number' class='col-10' id='reseller_disc_number"+(total_row+1)+"' onchange='return resellerDiscNumber("+(total_row+1)+")'/></td>" +
                " <td><input type='number' class='form-control border-dark col-10 basicInput2"+pst_id+"' id='item_qty"+(total_row+1)+"' value='1' onchange='return changeQty("+(total_row+1)+", "+pst_id+")'></td>" +
                " <td><input type='number' class='col-10 nameset_price' id='nameset_price"+(total_row+1)+"' onchange='return namesetPrice("+(total_row+1)+")'/></td> <td><input type='number' class='col-10 marketplace_price' id='marketplace_price"+(total_row+1)+"' onchange='return marketplacePrice("+(total_row+1)+")'/></td>" +
                " <td><span id='sell_price_item"+(total_row+1)+"'>"+addCommas(sell_price)+"</span></td> " +
                " <td><span class='subtotal_item' id='subtotal_item"+(total_row+1)+"'>"+addCommas(sell_price)+"</span></td> " +
                " <td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem"+(total_row+1)+"' onclick='return saveItem("+(total_row+1)+", "+pst_id+", "+sell_price+", 0, "+sell_price_discount+")'>" +
                " <i class='fa fa-eye' style='display:none;'></i></a> " +
                " <a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem("+pst_id+", "+sell_price+", "+(total_row+1)+")'><i class='fas fa-trash-alt'></i></a></div></td></tr>");
        } else {
            jQuery('#orderTable tr:last').after("" +
                "<tr data-list-item class='pos_item_list"+(pst_id)+" mb-2 bg-light-primary' id='orderList"+(total_row+1)+"'> " +
                "<td><span style='font-size:14px; white-space:nowrap;'>"+p_name+"</span></td>" +
                "<td>"+bin+"</td> " +
                "<td><input type='number' class='col-10' id='reseller_disc"+(total_row+1)+"' onchange='return resellerDisc("+(total_row+1)+")'/></td>" +
                "<td><input type='number' class='col-10' id='reseller_disc_number"+(total_row+1)+"' onchange='return resellerDiscNumber("+(total_row+1)+")'/></td>" +
                "<td><input type='number' class='form-control border-dark col-10 basicInput2"+pst_id+"' id='item_qty"+(total_row+1)+"' value='1' onchange='return changeQty("+(total_row+1)+", "+pst_id+")'></td> " +
                "<td><input type='number' class='col-10 nameset_price' id='nameset_price"+(total_row+1)+"' onchange='return namesetPrice("+(total_row+1)+")'/></td> " +
                "<td><input type='number' disabled class='col-10 marketplace_price' id='marketplace_price"+(total_row+1)+"' onchange='return marketplacePrice("+(total_row+1)+")'/></td> " +
                "<td><span id='sell_price_item"+(total_row+1)+"'>"+addCommas(sell_price)+"</span></td> " +
                "<td><span class='subtotal_item' id='subtotal_item"+(total_row+1)+"'>"+addCommas(sell_price)+"</span></td> " +
                "<td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem"+(total_row+1)+"' onclick='return saveItem("+(total_row+1)+", "+pst_id+", "+sell_price+", 0, "+sell_price_discount+")'><i class='fa fa-eye' style='display:none;'></i></a> <a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem("+pst_id+", "+sell_price+", "+(total_row+1)+")'><i class='fas fa-trash-alt'></i></a></div></td></tr>");
        }
        if (jQuery('#voucher_code').val() != '') {
            var code = jQuery('#voucher_code').val();
            jQuery('#cancel_voucher').trigger('click');
            jQuery('#voucher_code').val(code);
            setTimeout(() => {
                jQuery('#f_voucher').trigger('submit');
            }, 300);
        }
        return false;
        //alert(bin);
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

    jQuery(document).delegate('#pl_id', 'change', function() {
        var pl_code = jQuery("option:selected", this).attr('data-pl_code');
        if (pl_code == 'TOKO') {
            swal({
                title: "Yakin..?",
                text: "Yakin pilih dari display ? apakah sudah cek barang ? ",
                icon: "warning",
            }).then(function(isConfirm) {
                if (isConfirm) {

                }
            })
        }
    });

    jQuery(document).on('click', 'body', function(e) {
        jQuery('#itemList').fadeOut();
    })

    setInterval(() => {
        reloadComplaint();
    }, 10000);

    jQuery(document).ready(function(e) {
        jQuery('#_pt_id_complaint').val('');
        jQuery('#_exchange').val('');
        // reloadRefund();
        jQuery('#ref_number_label').addClass('d-none');
        jQuery('#ref_number_label').removeClass('d-flex');
        jQuery('#pm_id').select2({
            width: "100%",
            dropdownParent: jQuery('#payment-online-popup')
        });
        jQuery('#pm_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            jQuery(e.target).parents().off(evt);
            jQuery(window).off(evt);
        });

        jQuery('#payment-online-popup').on('hide.bs.modal', function() {
            //jQuery('#order_code').val('');
            jQuery('#unique_code').val('');
			jQuery('#ref_number').val('');
            jQuery('#final_total_unique_code').val('');
            jQuery('#admin_cost').val('');
            jQuery('#real_price').val('');
            jQuery('#marketplace_side').val('');
            jQuery('#marketplace_real_price').val('');
            jQuery('#discount_seller_side').val('');
            // jQuery('#subsidi_ongkir').val('');
        });

        jQuery('#choosecustomer').on('hide.bs.modal', function() {
            //jQuery('#order_code').val('');
            jQuery('#f_customer')[0].reset();
        });

        jQuery('#add_customer_btn').on('click', function() {
            jQuery('#_mode').val('add');
            jQuery('#choosecustomer').modal('show');
        });

        jQuery('#shiftEmployeeBtn').on('click', function() {

            // jQuery('#shiftEmployeeModal').modal('show');
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: '/check_user_shift',
                method: 'GET',
                success: function (response) {
                    if (response.status === '200') {
                        // User has started a shift
                        jQuery('#startShiftButton').hide();
                        jQuery('#stopShiftButton').show();
                        jQuery('#shiftStatus').html('Shift In Progress');
                        jQuery('#shiftEmployeeModal').modal('show');
                    } else {
                        // User has not started a shift, show the modal with the start button
                        jQuery('#startShiftButton').show();
                        jQuery('#stopShiftButton').hide();
                        jQuery('#shiftEmployeeModal').modal('show');
                    }
                },
                error: function (error) {
                    console.error('Error checking user shift:', error);
                }
            });
        });

		jQuery('#reload_refund_list').on('click', function() {
            reloadRefund();
        });

        jQuery('#st_id').on('change', function() {
            jQuery('#shipping_courier_side').text('');
            jQuery('#shipping_cost_side').text('0');
            jQuery('#total_final_price_side').text('0');
            jQuery('#total_item_side').text('0');
            jQuery('#total_price_side').text('0');
            jQuery('#total_nameset_side').text('0');
            jQuery('#orderTable').find('tr:not(:has(th))').remove();
            jQuery('#note').val('');
            jQuery('#_pt_id').val('');
            jQuery('#unique_code').val('');
            jQuery('#ref_number').val('');
            jQuery('#final_total_unique_code').val('');
            jQuery('#admin_cost').val('');
            jQuery('#another_cost').val('');
            jQuery('#real_price').val('');
            jQuery('#cross_order').val('');
            jQuery('#discount_seller').val('');
            // jQuery('#subsidi_ongkir').val('');
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
            var ps_qty = jQuery(this).attr('data-ps_qty');
            var pt_id = jQuery(this).attr('data-pt_id');
            var pl_id = jQuery(this).attr('data-pl_id');
            var plst_id = jQuery(this).attr('data-plst_id');
            var sell_price = jQuery(this).attr('data-sell_price');
            var sell_price_discount = jQuery(this).attr('data-sell_price');
            var total_price_item = jQuery(this).attr('data-total_price');
            var nameset_price = jQuery(this).attr('data-nameset_price');
            var marketplace_price = jQuery(this).attr('data-marketplace_price');
            var reseller_disc = jQuery(this).attr('data-discount');
            var item_qty = jQuery(this).attr('data-item_qty');
            var total_row = parseFloat(jQuery('#total_row').val());
            jQuery('#total_row').val(total_row+1);
            var total_item = jQuery('#total_item_side').text();
            var total_price = jQuery('#total_price_side').text();
            var price = total_price_item/item_qty;
            //alert(plst_id);
            if (jQuery(this).is(':checked')) {
                jQuery('#_pt_id_complaint').val(pt_id);
                jQuery('#total_item_side').text(parseInt(total_item) - 1);
                jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(total_price)) - parseFloat(total_price_item)));
                jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(total_price)) - parseFloat(total_price_item)));
                addRefundExchangeList('add', plst_id, pt_id);
                jQuery('#orderTable tr:last').after("" +
                    "<tr data-list-item class='bg-danger text-white pos_item_list"+(pst_id)+" mb-2' id='data_plst"+plst_id+"'> " +
                    "<td style='white-space: nowrap;'>"+p_name+"</td> " +
                    "<td><select class='text-white font-weight-bold' id='pl_id"+(total_row+1)+"' style='background-color:#986923; border-radius:10px;'>" +
                    "<option value='"+pl_id+"' selected>DEFAULT</option></select></td> " +
                    "<td><input type='number' class='col-10' id='reseller_disc"+(total_row+1)+"' value='"+reseller_disc+"' onchange='return resellerDisc("+(total_row+1)+")' readonly/></td> " +
                    "<td><input type='number' class='col-10' id='reseller_disc_number"+(total_row+1)+"' onchange='return resellerDisc("+(total_row+1)+")'/></td>" +
                    "<td><input type='number' class='form-control border-dark col-10 basicInput2"+pst_id+"' id='item_qty"+(total_row+1)+"' value='-"+item_qty+"' onchange='return changeReturQty("+(total_row+1)+", "+pst_id+", "+item_qty+")'></td> " +
                    "<td><input type='number' class='col-10 nameset_price' id='nameset_price"+(total_row+1)+"' value='"+nameset_price+"' onchange='return namesetPrice("+(total_row+1)+")' readonly/></td> " +
                    "<td><input type='number' class='col-10 marketplace_price' id='marketplace_price"+(total_row+1)+"' value='-"+marketplace_price+"' onchange='return marketplacePrice("+(total_row+1)+")'/></td> " +
                    "<td><span id='sell_price_item"+(total_row+1)+"'>-"+addCommas(price)+"</span></td> " +
                    "<td><span class='subtotal_item' id='subtotal_item"+(total_row+1)+"'>-"+addCommas(total_price_item)+"</span></td> " +
                    "<td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem"+(total_row+1)+"' onclick='return saveItem("+(total_row+1)+", "+pst_id+", "+sell_price+", "+plst_id+", "+sell_price_discount+")'><i class='fa fa-eye' style='display:none;'></i></a></div></td></tr>");
            } else {
                // jQuery('#_pt_id_complaint').val('');
                addRefundExchangeList('remove', plst_id, pt_id);
                if (total_row > 0) {
                    jQuery('#total_item_side').text(parseInt(total_item) + 1);
                    jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + parseFloat(total_price_item)));
                    jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + parseFloat(total_price_item)));
                    //alert(plst_id);
                    jQuery('#orderTable tr#data_plst'+plst_id+'').remove();
                }
            }
        });

        jQuery(document).delegate('#check_dropshipper', 'click', function() {
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

        jQuery(document).delegate('#add_new_customer', 'click', function () {
            console.log(jQuery('#cust_id_label').val());
            jQuery('#_mode').val('add');
            var custIdLabelValue = jQuery('#cust_id_label').val();
            jQuery('#choosecustomer').modal('show');
            jQuery('#cust_phone').val(custIdLabelValue);
        })

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

        jQuery(document).delegate('#check_sub_customer', 'click', function() {
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

        jQuery('#product_barcode_input').focus();
        @if (strtolower($data['user']->stt_name) == 'offline')
            jQuery('#posContent').show();
            jQuery('.sidebarPOS').show();
            jQuery('#product_barcode_input').focus();
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
            jQuery('#payment_total').text(addCommas(parseFloat(replaceComma(jQuery('#total_final_price_side').text())) ));
            var total_row = jQuery('tr[data-list-item]').length;
            var total_marketplace = 0;
            var total_nameset_side = parseFloat(replaceComma(jQuery('#total_nameset_side').text()));
            var type = jQuery('#std_id option:selected').text();
            if (type != 'DROPSHIPPER' && type != 'RESELLER' && type != 'WHATSAPP' && type != 'WEBSITE') {
                jQuery('#marketplace_total_tr').removeClass('d-none');
                jQuery('#marketplace_total_tr').addClass('d-flex');
                jQuery('#marketplace_selisih_tr').removeClass('d-none');
                jQuery('#marketplace_selisih_tr').addClass('d-flex');
            } else {
                jQuery('#marketplace_total_tr').removeClass('d-flex');
                jQuery('#marketplace_total_tr').addClass('d-none');
                jQuery('#marketplace_selisih_tr').removeClass('d-flex');
                jQuery('#marketplace_selisih_tr').addClass('d-none');
            }
            jQuery('#orderTable tr').each(function(index, row) {
                var marketplace = jQuery(row).find('.marketplace_price').val();
                if (typeof marketplace !== 'undefined' && marketplace != '') {
                    total_marketplace += parseFloat(marketplace);
                }
            });
            jQuery('#marketplace_side').val(addCommas(total_marketplace + total_nameset_side));
            jQuery('#marketplace_sell_price').val(addCommas(total_marketplace+total_nameset_side-parseFloat(replaceComma(jQuery('#payment_total').text()))));
        });

        jQuery('#total_payment').on('keyup', function(e) {
            e.preventDefault();
            var total_price = jQuery('#payment_total').text();
            var total_payment = jQuery(this).val();
            var return_payment = parseFloat(total_payment) - parseFloat(total_price);
            jQuery('#return_payment').text(return_payment);
        });

        jQuery('#pm_id').on('change', function(e) {
            e.preventDefault();
            var label = jQuery('#pm_id option:selected').text();
            //alert(label);
			if (label == 'DEBIT') {
                jQuery('#ref_number_label').removeClass('d-none');
                jQuery('#ref_number_label').addClass('d-flex');
            } else {
                jQuery('#ref_number_label').removeClass('d-flex');
                jQuery('#ref_number_label').addClass('d-none');
            }
        });

        jQuery('#save_transaction').on('click', function(e) {
            e.preventDefault();
			var pt_id_complaint = jQuery('#_pt_id_complaint').val();
            var pm_id = jQuery('#pm_id').val();
            var cp_id = jQuery('#cp_id').val();
            var std_id = jQuery('#std_id').val();
            var type = jQuery('#std_id option:selected').text();
            var cust_id = jQuery('#cust_id').val();
            var sub_cust_id = jQuery('#sub_cust_id').val();
            var note = jQuery('#note').val();
            var unique_code = jQuery('#unique_code').val();
            var shipping_cost = jQuery('#shipping_cost').val();
            var cr_id = jQuery('#courier').val();
            var order_code = jQuery('#order_code').val();
            var ref_number = jQuery('#ref_number').val();
            var admin_cost = jQuery('#admin_cost').val();
            var another_cost = jQuery('#another_cost').val();
            var real_price = jQuery('#real_price').val();
            // var subsidi_ongkir = jQuery('#subsidi_ongkir').val();
            var total_row = jQuery('tr[data-list-item]').length;
            var exchange = jQuery('#_exchange').val();
            var cross = jQuery('#cross_order').val();
            var st_id = jQuery('#st_id').val();
            var voc_pst_id = jQuery('#_voc_pst_id').val();
            var voc_value = jQuery('#_voc_value').val();
            var voc_id = jQuery('#_voc_id').val();
            var total_discount_side = jQuery('#total_discount_value_side').text();
            var discount_seller = jQuery('#discount_seller').val();

            if (std_id == '14' || std_id == '13') {
                if (pm_id == '2' && cp_id == '') {
                    swal('Periksa Rek Tujuan Trf', 'Jika divisi adalah whatsapp / website', 'warning');
                    return false;
                }
            }

            if (real_price == '') {
                swal('Periksa Kode Unik dan Biaya Admin', 'Jika memang tidak ada silahkan input 0', 'warning');
                return false;
            }
            if (type == 'DROPSHIPPER') {
                if (cust_id == '') {
                    swal('Dropshipper', 'silahkan pilih dropshipper', 'warning');
                } else if (sub_cust_id == '') {
                    swal('Customer', 'silahkan pilih customer', 'warning');
                }
            }
            jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type:'POST',
                url: "{{ url('save_transaction')}}",
                data: {
                    voc_id:voc_id,
                    voc_pst_id:voc_pst_id,
                    voc_value:value_price_voc,
                    _cp_id:cp_id,
                    _cross:cross,
                    _st_id:st_id,
                    _pt_id_complaint:pt_id_complaint,
                    _exchange:exchange,
                    _ref_number:ref_number,
                    _type:type,
                    _sub_cust_id:sub_cust_id,
                    _real_price:replaceComma(real_price),
                    _admin_cost:admin_cost,
                    _another_cost:another_cost, _order_code:order_code,
                    _pm_id:pm_id, _std_id:std_id, _cust_id:cust_id,
                    _note:note, _unique_code:unique_code,
                    _shipping_cost:shipping_cost, _cr_id:cr_id,
                    _total_discount_side:replaceComma(total_discount_side),
                    _discount_seller:replaceComma(discount_seller),
                },
                dataType: 'json',
                success: function(r) {
                    jQuery.noConflict();
                    jQuery("#payment-online-popup").modal('hide');
                    if (r.status == '200') {
                        var finish = '';
                        jQuery('#_pt_id').val(r.pt_id);
                        jQuery('#orderTable tr').each(function(index, row) {
                            jQuery(row).find('.saveItem').trigger('click');
                        });
                        // if (finish == 'true') {
                            jQuery('#shipping_courier_side').text('');
                            jQuery('#shipping_cost_side').text('0');
                            jQuery('#total_final_price_side').text('0');
                            jQuery('#total_item_side').text('0');
                            jQuery('#total_price_side').text('0');
                            jQuery('#total_nameset_side').text('0');
                            jQuery('#orderTable').find('tr:not(:has(th))').remove();
                            jQuery('#cust_id').val('');
                            jQuery('#cust_id_label').val('');
                            jQuery('#sub_cust_id').val('');
                            jQuery('#sub_cust_id_label').val('');
                            jQuery('#check_sub_customer').attr('data-id', '');
                            jQuery('#check_customer').attr('data-id', '');
                            jQuery('#refund_invoice_label').val('');
                            jQuery('#refund_retur_invoice_label').text('');
                            jQuery('#refund_retur_pt_id').val('');
                            jQuery('#note').val('');
                            jQuery('#_pt_id').val('');
                            jQuery('#unique_code').val('');
                            jQuery('#ref_number').val('');
                            jQuery('#final_total_unique_code').val('');
                            jQuery('#admin_cost').val('');
                            jQuery('#another_cost').val('');
                            jQuery('#real_price').val('');
                            // jQuery('#subsidi_ongkir').val('');
                            jQuery('#courier').val('');
                            jQuery('#_pt_id_complaint').val('');
                            jQuery('#_exchange').val('');
                            jQuery('#discount_seller').val('');

                            jQuery('#_voc_pst_id').val('');
                            jQuery('#_voc_value').val('');
                            jQuery('#_voc_id').val('');
                            jQuery('#voucher_code').val("");
                            jQuery('#voucher_information').addClass("d-none");
                            jQuery('#discount_seller').val('0');
                            sell_price_voc = 0;
                            value_price_voc = 0;
                            shoes_voucher_temp = [];

                            jQuery('#total_discount_value_side').text('0');
                            @php  session()->forget('voc_item') @endphp
                            if (st_id == '4') {
                                cross = 'false';
                            }
                            swal('Berhasil', 'Transaksi Berhasil Disimpan', 'success');
                            if (cross != 'true') {
                                setTimeout(() => {
                                    var win = window.open('{{ url('/') }}/print_invoice/'+r.invoice, '_blank');
                                    if (win) {
                                        win.focus();
                                    } else {
                                        alert('Please allow popups for this website');
                                    }
                                }, 2000);
                            }
                        // }
                    } else if (r.status == '400') {
                        swal('Gagal', 'Gagal simpan transaksi', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        jQuery('#product_barcode_input').on('change', function() {
            jQuery('#_exchange').val('true');
            var barcode = jQuery(this).val();
            var cust_id = jQuery('#cust_id').val();
            var std_id = jQuery('#std_id').val();
            var type = jQuery('#std_id option:selected').text();
            @if (strtolower($data['user']->stt_name) == 'online')
            if (cust_id == '') {
                swal('Customer', 'silahkan pilih customer', 'warning');
                jQuery('#product_barcode_input').val('');
                return false;
            }
            @endif
            //toast('Ditambah', 'Item berhasil ditambah', 'success');
            jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type: "POST",
                data: {_barcode:barcode, type:type, _std_id:std_id},
                dataType: 'json',
                url: "{{ url('check_barcode')}}",
                success: function(r) {
                    jQuery('#product_barcode_input').val('');
                    if (r.status == '200') {
                        var total_item = jQuery('#total_item_side').text();
                        var total_price = jQuery('#total_price_side').text();
                        var total_final_price = jQuery('#total_final_price_side').text();
                        var total_row = jQuery('tr[data-list-item]').length;
                        var pos_item_list = jQuery('.pos_item_list'+r.pst_id).length;
                        jQuery('#total_item_side').text(parseInt(total_item) + 1);
                        jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + r.sell_price));
                        jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + r.sell_price));
                        toast('Ditambah', 'Item berhasil ditambah', 'success');
                        var type = jQuery('#std_id option:selected').text();
                        if (type != 'DROPSHIPPER' && type != 'RESELLER' && type != 'WHATSAPP' && type != 'WEBSITE') {
                            jQuery('#orderTable tr:last').after("" +
                                "<tr data-list-item class='pos_item_list"+(r.pst_id)+" mb-2 bg-light-primary' " + "id='orderList"+(total_row+1)+"'>" +
                                "<td>"+r.p_name+"</td> " +
                                "<td>"+r.bin+"</td> " +
                                "<td><input type='number' class='col-10' id='reseller_disc"+(total_row+1)+"' onchange='return resellerDisc("+(total_row+1)+")'/></td> " +
                                "<td><input type='number' class='col-10' id='reseller_disc_number"+(total_row+1)+"' onchange='return resellerDisc("+(total_row+1)+")'/></td>" +
                                "<td><input type='number' class='form-control border-dark col-6 basicInput2"+r.pst_id+"' id='item_qty"+(total_row+1)+"' value='1' onchange='return changeQty("+(total_row+1)+", "+r.pst_id+")' readonly></td>" +
                                "<td><input type='number' class='col-10 nameset_price' id='nameset_price"+(total_row+1)+"' onchange='return namesetPrice("+(total_row+1)+")'/></td> " +
                                "<td><input type='number' class='col-10 marketplace_price' id='marketplace_price"+(total_row+1)+"' onchange='return marketplacePrice("+(total_row+1)+")'/></td>" +
                                "<td><span id='sell_price_item"+(total_row+1)+"'>"+addCommas(r.sell_price)+"</span></td>" +
                                "<td><span class='subtotal_item' id='subtotal_item"+(total_row+1)+"'>"+addCommas(r.sell_price)+"</span></td>" +
                                "<td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem"+(total_row+1)+"' onclick='return saveItem("+(total_row+1)+", "+r.pst_id+", "+r.sell_price+", 0, "+sell_price_discount+")' style='display:none;'><i class='fa fa-eye'></i></a> " +
                                "<a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem("+r.pst_id+", "+r.sell_price+", "+(total_row+1)+")'><i class='fas fa-trash-alt'></i></a></div></td></tr>");
                        } else {
                            jQuery('#orderTable tr:last').after("<tr data-list-item class='pos_item_list"+(r.pst_id)+" mb-2 bg-light-primary' id='orderList"+(total_row+1)+"'> <td>"+r.p_name+"</td>" +
                                "<td>"+r.bin+"</td>" +
                                "<td><input type='number' class='col-10' id='reseller_disc"+(total_row+1)+"' onchange='return resellerDisc("+(total_row+1)+")'/></td> " +
                                "<td><input type='number' class='col-10' id='reseller_disc_number"+(total_row+1)+"' onchange='return resellerDisc("+(total_row+1)+")'/></td>" +
                                "<td><input type='number' class='form-control border-dark col-6 basicInput2"+r.pst_id+"' id='item_qty"+(total_row+1)+"' value='1' onchange='return changeQty("+(total_row+1)+", "+r.pst_id+")' readonly></td> " +
                                "<td><input type='number' class='col-10 nameset_price' id='nameset_price"+(total_row+1)+"' onchange='return namesetPrice("+(total_row+1)+")'/></td> " +
                                "<td><input type='number' disabled class='col-10 marketplace_price' id='marketplace_price"+(total_row+1)+"' onchange='return marketplacePrice("+(total_row+1)+")'/></td> " +
                                "<td><span id='sell_price_item"+(total_row+1)+"'>"+addCommas(r.sell_price)+"</span></td>" +
                                "<td><span class='subtotal_item' id='subtotal_item"+(total_row+1)+"'>"+addCommas(r.sell_price)+"</span></td>" +
                                "<td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem"+(total_row+1)+"' onclick='return saveItem("+(total_row+1)+", "+r.pst_id+", "+r.sell_price+", 0, "+sell_price_discount+")' style='display:none;'><i class='fa fa-eye'></i></a> <a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem("+r.pst_id+", "+r.sell_price+", "+(total_row+1)+")'><i class='fas fa-trash-alt'></i></a></div></td></tr>");
                        }
                        return false;
                    } else if (r.status == '419') {
                        swal('Stok habis / Belum Setup', 'Stok habis atau belum disetup pada BIN rak', 'warning');
                        return false;
                    } else {
                        swal('Tidak Ditemukan', 'Produk tidak ditemukan', 'danger');
                        return false;
                    }
                }
            });
        });

        jQuery('#product_name_input').on('keyup', function(){
            var query = jQuery(this).val();
            var type = jQuery('#std_id option:selected').text();
            var st_id = jQuery('#st_id').val();
            var std_id = jQuery('#std_id').val();
            if (st_id == '') {
                swal('Pilih Store', 'Silahkan pilih lokasi store untuk pick artikel', 'warning');
                jQuery(this).val('');
                return false;
            }
            if(jQuery.trim(query) != '' || jQuery.trim(query) != null) {
                if (jQuery.trim(query).length > 2) {
                    jQuery.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        url:"{{  url('autocomplete') }}",
                        method:"POST",
                        data:{query:query, type:type, _std_id:std_id, _st_id:st_id},
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
                        url:"{{  url('autocomplete_invoice') }}",
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

        jQuery('#cust_city').on('chandata_stokge', function() {
            var city = jQuery(this).val();
            reloadSubdistrict(city);
        });

        jQuery('#unique_code').on('change', function(){
            var unique = jQuery(this).val();
            if (unique == '') {
                jQuery(this).val(0);
                unique = 0;
            }
            var payment_total = jQuery('#payment_total').text();
            var marketplace_total = jQuery('#marketplace_side').val();
            var type = jQuery('#std_id option:selected').text();
            if (type != 'DROPSHIPPER' && type != 'RESELLER' && type != 'WHATSAPP' && type != 'WEBSITE') {
                var final_total = parseFloat(replaceComma(marketplace_total)) + parseFloat(unique);
            } else {
                var final_total = parseFloat(replaceComma(payment_total)) + parseFloat(unique);
            }
            //alert(unique+' '+total+' '+final_total);
            jQuery('#final_total_unique_code').val(addCommas(final_total));
            jQuery('#discount_seller').val('');
            jQuery('#admin_cost').val('');
            jQuery('#real_price').val('');
        });

        jQuery('#discount_seller').on('change', function(){
            var discount = jQuery(this).val();
            var unique = jQuery('#unique_code').val();
            var subsidi_ongkir = jQuery('#subsidi_ongkir').val();
            if (unique == '')  {
                jQuery('#unique_code').val(0).trigger('change');
            }
            if (discount == '') {
                discount = 0;
            }
            if (subsidi_ongkir == '') {
                subsidi_ongkir = 0; // Add this to default the value if empty
            }
            var final_total = jQuery('#final_total_unique_code').val();
            var final_total_discount = parseFloat(replaceComma(final_total)) - parseFloat(discount) - parseFloat(subsidi_ongkir);

            jQuery('#real_price').val(addCommas(final_total_discount));
            jQuery('#admin_cost').val('');
        });

        jQuery('#admin_cost').on('change', function(){
            var cost = jQuery(this).val();
            var discount_seller = jQuery('#discount_seller').val();
            var unique = jQuery('#unique_code').val();
            var subsidi_ongkir = jQuery('#subsidi_ongkir').val();
            if (unique == '')  {
                jQuery('#unique_code').val(0).trigger('change');
            }
            if (cost == '') {
                cost = 0;
            }
            if (subsidi_ongkir == '') {
                subsidi_ongkir = 0; // Add this line
            }
            var final_total = jQuery('#final_total_unique_code').val();
            var final_total_admin = parseFloat(replaceComma(final_total)) - parseFloat(cost) - parseFloat(discount_seller) - parseFloat(subsidi_ongkir);
            //alert(unique+' '+total+' '+final_total);
            jQuery('#real_price').val(addCommas(final_total_admin));
        });

        jQuery('#another_cost').on('change', function(){
            var admin_cost = jQuery('#admin_cost').val();
            var cost = jQuery(this).val();
            var unique = jQuery('#unique_code').val();
            var discount_seller = jQuery('#discount_seller').val();
            var subsidi_ongkir = jQuery('#subsidi_ongkir').val();
            if (unique == '')  {
                jQuery('#unique_code').val(0).trigger('change');
            }
            if (cost == '') {
                cost = 0;
            }
            if (subsidi_ongkir == '') {
            subsidi_ongkir = 0;
            }
            var final_total = jQuery('#final_total_unique_code').val();
            var final_total_admin = parseFloat(replaceComma(final_total)) - parseFloat(admin_cost) + parseFloat(cost) - parseFloat(discount_seller) - parseFloat(subsidi_ongkir);
            jQuery('#real_price').val(addCommas(final_total_admin));
        });
        jQuery('#subsidi_ongkir').on('change', function(){
            var subsidi_ongkir = jQuery(this).val();
            var discount_seller = jQuery('#discount_seller').val();
            var unique = jQuery('#unique_code').val();
            var admin_cost = jQuery('#admin_cost').val();
            var another_cost = jQuery('#another_cost').val();
            if (unique == '')  {
                jQuery('#unique_code').val(0).trigger('change');
            }
            if (discount_seller == '') {
                discount_seller = 0;
            }
            if (admin_cost == '') {
                admin_cost = 0;
            }
            if (subsidi_ongkir == '') {
                subsidi_ongkir = 0;
            }
            if (another_cost == '') {
                another_cost = 0;
            }

            var final_total = jQuery('#final_total_unique_code').val();
            var final_total_admin = parseFloat(replaceComma(final_total)) - parseFloat(admin_cost) - parseFloat(discount_seller) - parseFloat(subsidi_ongkir) + parseFloat(another_cost); // Final calculation

            jQuery('#real_price').val(addCommas(final_total_admin));
        });


        jQuery('#f_ongkir').on('submit', function(e) {
            e.preventDefault();
            var shipping_cost = jQuery('#shipping_cost').val();
            var shipping_description = jQuery('#courier option:selected').text();
            if (shipping_description == '- Pilih -') {
                swal('Kurir', 'silahkan pilih kurir', 'warning');
                return false;
            }
            var shipping_value = jQuery('#courier').val();
            var total_price = jQuery('#total_price_side').text();
            var total_nameset_price = jQuery('#total_nameset_side').text();
            jQuery('#shipping_courier_side').text(shipping_description);
            jQuery('#shipping_courier_value').val(shipping_value);
            jQuery('#shipping_cost_side').text(addCommas(shipping_cost));
            if (jQuery.trim(shipping_cost) == '') {
                shipping_cost = 0;
            }
            jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(total_price)) + parseFloat(replaceComma(total_nameset_price)) + parseFloat(shipping_cost)));
            jQuery('#shippingcost').modal('hide');
        });

        jQuery('#f_customer').on('submit', function (e) {
            e.preventDefault();
            jQuery("#save_customer_btn").html('Proses ..');
            jQuery("#save_customer_btn").attr("disabled", true);
            var formData = new FormData(this);
            var std_id = jQuery('#std_id').val();
            if (std_id == '') {
                jQuery("#save_customer_btn").html('Simpan');
                jQuery("#save_customer_btn").attr("disabled", false);
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
                        jQuery("#f_customer")[0].reset();
                        jQuery("#choosecustomer").modal('hide');
                        toast('Berhasil', 'Data berhasil disimpan', 'success');
                        // reloadCustomerByDivision(std_id);
                    } else if (data.status == '400') {
                        jQuery("#choosecustomer").modal('hide');
                        toast('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function (data) {
                    swal('Error', data, 'error');
                }
            });
        });

        var shiftStarted = false;
        var clockInterval;
        jQuery('#startShiftButton').on('click', function() {
            console.log("test");
            shiftStarted = true;
            jQuery('#shiftStatus').html('Shift In Progress');
            jQuery('#startShiftButton').hide();
            jQuery('#stopShiftButton').show();

            var clockElement = jQuery('<div class="clock"></div>');
            jQuery('.modal-body').append(clockElement);

            clockInterval = setInterval(function() {
                var now = new Date();
                var hours = now.getHours();
                var minutes = now.getMinutes();
                var seconds = now.getSeconds();
                var formattedTime = hours + ':' + minutes + ':' + seconds;
                clockElement.html(formattedTime);
            }, 1000);


            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: '/user_start_shift',
                method: 'POST',
                success: function(response) {
                    // handle shift already starter or not, if already starterd change button to stop shift
                    console.log(response);
                },
                error: function(error) {
                    console.error('Error starting shift:', error);
                }
            });
        });

        jQuery('#stopShiftButton').on('click', function() {
            shiftStarted = false;
            jQuery('#shiftStatus').html('Shift Stopped');
            jQuery('#startShiftButton').show();
            jQuery('#stopShiftButton').hide();

            clearInterval(clockInterval);

            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: '/user_end_shift',
                method: 'POST',
                success: function(response) {
                    console.log(response);
                },
                error: function(error) {
                    console.error('Error End shift:', error);
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

    jQuery('#std_id').on('change', function() {
        var std_id = jQuery(this).val();
        jQuery('#orderTable').find('tr:not(:has(th))').remove();
        jQuery('#total_item_side').text('0');
        jQuery('#total_price_side').text('0');
		jQuery('#total_nameset_side').text('0');
        jQuery('#shipping_courier_side').text('');
        jQuery('#shipping_cost_side').text('0');
        jQuery('#total_final_price_side').text('0');
        jQuery('#courier').val('');
        jQuery('#cust_id').val('');
        jQuery('#cust_id_label').val('');
        jQuery('#sub_cust_id').val('');
        jQuery('#sub_cust_id_label').val('');
        jQuery('#check_sub_customer').attr('data-id', '');
        jQuery('#check_customer').attr('data-id', '');
        if (std_id != '') {
            jQuery('#posContent').show();
            jQuery('.sidebarPOS').show();
            jQuery('#product_barcode_input').focus();
        } else {
            jQuery('#posContent').hide();
            jQuery('.sidebarPOS').hide();
        }
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

    jQuery('#cust_id_label').on('change', function(){
        var query = jQuery(this).val();
        if (query == '') {
            jQuery('#cust_id').val('');
            jQuery('#check_customer').attr('data-id', '');
            jQuery('#itemListCust').html('');
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

    jQuery('#sub_cust_id_label').on('keyup', function(){
        var query = jQuery(this).val();
        var type = 'sub_cust';
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
                        jQuery('#itemListSubCust').fadeIn();
                        jQuery('#itemListSubCust').html(data);
                    }
                });
            } else {
                jQuery('#itemListSubCust').fadeOut();
            }
        } else {
            jQuery('#itemListSubCust').fadeOut();
        }
    });

    jQuery(document).delegate('#add_to_item_list_sub_cust', 'click', function() {
        var cust_id = jQuery(this).attr('data-id');
        var cust_name = jQuery(this).text();
        jQuery('#sub_cust_id').val(cust_id);
        jQuery('#sub_cust_id_label').val(cust_name);
        jQuery('#check_sub_customer').attr('data-id', cust_id);
        jQuery('#itemListSubCust').html('');
        jQuery('#itemListSubCust').fadeOut();
    });

    jQuery('#sub_cust_id_label').on('change', function(){
        var query = jQuery(this).val();
        if (query == '') {
            jQuery('#sub_cust_id').val('');
            jQuery('#check_sub_customer').attr('data-id', '');
            jQuery('#itemListSubCust').html('');
            jQuery('#itemListSubCust').fadeOut();
        }
    });

    // REFUND
    jQuery('#refund_invoice_label').on('keyup', function(){
        var query = jQuery(this).val();
        if(jQuery.trim(query) != '' || jQuery.trim(query) != null) {
            if (jQuery.trim(query).length > 5) {
                jQuery.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url:"{{  url('autocomplete_refund_invoice') }}",
                    method:"POST",
                    data:{query:query},
                    success:function(data){
                        jQuery('#itemListRefund').fadeIn();
                        jQuery('#itemListRefund').html(data);
                    }
                });
            } else {
                jQuery('#itemListRefund').fadeOut();
            }
        } else {
            jQuery('#itemListRefund').fadeOut();
        }
    });

    jQuery(document).delegate('#add_to_item_list_refund', 'click', function() {
        var pt_id = jQuery(this).attr('data-id');
        var invoice = jQuery(this).text();
        jQuery('#refund_invoice_label').val(invoice);
        jQuery('#refund_retur_invoice_label').text(invoice);
        jQuery('#refund_retur_pt_id').val(pt_id);
        jQuery('#RefundExchangeModal').modal('show');
        refund_retur_table.draw();
        jQuery('#itemListRefund').html('');
        jQuery('#itemListRefund').fadeOut();
    });

    jQuery('#refund_invoice_label').on('change', function(){
        var query = jQuery(this).val();
        if (query == '') {
            jQuery('#refund_retur_pt_id').val('');
            jQuery('#refund_retur_invoice_label').text('');
            jQuery('#itemListRefund').html('');
            jQuery('#itemListRefund').fadeOut();
        }
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
                <div class="input-group-append ml-3">
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

    jQuery(document).ready(function () {
        // Event listener to add a new discount input field
        jQuery(document).on('click', '.add-total-discount', function () {
            let newField = `
            <div class="input-group mb-3">
                    <input type="text" name="total-discount-list[]" class="form-control" placeholder="Diskon" value="">
                    <div class="input-group-append ml-3">
                        <button class="btn btn-outline-secondary remove-total-discount" type="button">-</button>
                    </div>
                </div>
        `;
            jQuery("#total-discount-container").append(newField);
        });

        // Event listener to remove a discount input field
        jQuery(document).on('click', '.remove-total-discount', function () {
            // if remove button is cliked,subtract the total price
            let total_price = jQuery("#total_final_price_side").text();
            let total_discount_value_side = jQuery("#total_discount_value_side").text();
            let discount = jQuery(this).closest('.input-group').find('input').val();
            //remove comma from total price and discount
            total_price = replaceComma(total_price);
            total_discount_value_side = replaceComma(total_discount_value_side);

            let new_total_price = parseFloat(total_price) + parseFloat(discount);
            let new_total_discount_value_side = parseFloat(total_discount_value_side) - parseFloat(discount);

            jQuery('#total_final_price_side').text(addCommas(new_total_price));
            jQuery('#total_discount_value_side').text(addCommas(new_total_discount_value_side));
            jQuery(this).closest('.input-group').remove();
        });

        jQuery(document).on('click', '#total_discount_reset', function () {
            let total_price = jQuery("#total_final_price_side").text();
            total_price = replaceComma(total_price);
            // Reset each input field in total-discount-list
            let new_total_price= parseFloat(total_price);

            jQuery('input[name="total-discount-list[]"]').each(function () {
                let discount = replaceComma(jQuery(this).val());
                new_total_price += parseFloat(discount);
            });

            jQuery('#total_final_price_side').text(addCommas(new_total_price));

            jQuery('input[name="total-discount-list[]"]').each(function () {
                jQuery(this).val(0);
            });

            // Reset total_discount_value_side to 0
            jQuery('#total_discount_value_side').text('0');
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

    jQuery(document).delegate('#f_add_total_discount', 'submit', function(e) {
        e.preventDefault();

        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var total_final = jQuery('#total_final_price_side').text();
        var total = replaceComma(total_final);

        var formData = jQuery('#f_add_total_discount').serializeArray();

        jQuery.ajax({
            type:"POST",
            url: "{{ url('pos-total-discount')}}",
            data:
                {
                    formData: formData,
                },
            dataType:'json',
            success: function(r) {
                if (r.status == '200') {
                    if (r.discountType == 'percentage') {
                        // create count use percentage from total
                        var discount = parseFloat(total) * parseFloat(r.total) / 100;
                        var new_total = parseFloat(total) - parseFloat(discount);
                        jQuery('#total_final_price_side').text(addCommas(new_total));
                        jQuery('#total_discount_value_side').text(addCommas(discount));
                    }

                    if (r.discountType == 'nominal') {
                        var new_total = parseFloat(total) - parseFloat(r.total);
                        jQuery('#total_final_price_side').text(addCommas(new_total));
                        jQuery('#total_discount_value_side').text(addCommas(r.total));
                    }
                } else {
                    alert('kode salah');
                }
            }
        });
    })
</script>
