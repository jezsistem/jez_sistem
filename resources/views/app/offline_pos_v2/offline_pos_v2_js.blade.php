<script src="{{ asset('pos/js') }}/plugin.bundle.min.js"></script>
<script src="{{ asset('pos/js') }}/bootstrap.bundle.min.js"></script>
<script src="{{ asset('pos/js') }}/jquery.dataTables.min.js"></script>
<script src="{{ asset('pos/js') }}/multiple-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
    integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('pos/js') }}/script.bundle.js"></script>
<script src="{{ asset('cdn/jquery.toast.min.js') }}"></script>
<script src="{{ asset('cdn/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    console.log('offline_pos_v2_js loaded');
    // Helper function to check if we should skip adding Bootstrap rows (offline_pos_v2 uses updateProductTable)
    function shouldSkipRowAddition() {
        // Always skip in offline_pos_v2 - it uses updateProductTable() to render table from orderItems array
        return true;
    }
    
    var b1g1_temp = [];
    var shoes_voucher_temp = [];
    var sell_price_voc = 0;
    var value_price_voc = 0;

    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
            timeoutId = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    }

    function changeQty(row, pst_id, pls_qty) {
        let new_dicount = 0;
        let new_price = 0;
        var orginal_price = 0;

        var item_qty = jQuery('#item_qty' + row).val();

        var selected_discount = jQuery('#discount_selection' + row).val();

        var discount_normal = jQuery('#discount_normal' + row).text().replace(/,/g, '');

        var price_tag = jQuery('#price_tag_item' + row).text().replace(/,/g, '');

        if (selected_discount == 1) {
            orginal_price = price_tag;
        } else {
            originalPrice = jQuery('#discount_selection' + row).data('sellprice');
        }

        var sell_price_item = replaceComma(jQuery('#sell_price_item' + row).text());

        var subtotal_item = replaceComma(jQuery('#subtotal_item' + row).text());
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
                jQuery('#item_qty' + row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs',
                    'warning');
                return false;
            }
        }

        if (jQuery('#orderList' + row).hasClass('b1g1_mode')) {
            if (item_qty > 2) {
                jQuery('#item_qty' + row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs',
                    'warning');
                return false;
            }
            if (item_qty % 2 == 0) {
                item_qty = item_qty / 2;
            } else {
                item_qty = Math.ceil(item_qty / 2);
            }
        }
        var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item))        

        if (parseFloat(item_qty) < 0) {
            jQuery('#subtotal_item' + row).text('-' + addCommas(subtotal));

        } else {
            // If B1G1 mode, use price_tag as subtotal, else use calculated subtotal
            if (jQuery('#orderList' + row).hasClass('b1g1_mode')) {
                jQuery('#subtotal_item' + row).text(addCommas(price_tag));
            } else {
                jQuery('#subtotal_item' + row).text(addCommas(subtotal));
            }
            // jQuery('#sell_price_item' + row).text(addCommas(subtotal));
        }
        var final_price = 0;
        var discount_price = 0;
        var nameset = 0;
        var new_final_price = 0;
        jQuery('#orderTable tr').each(function(index, row) {
            if (jQuery(row).find('.sell_price_item').text() != '') {
                var sbttl = parseFloat(replaceComma(jQuery(row).find('.sell_price_item').text()));

                // var dcttl = parseFloat(replaceComma(jQuery(row).find('.sell_price_item').text())) - sbttl;
                if (typeof sbttl === 'undefined' && sbttl == '') {
                    sbttl = 0;
                }
                // Perubahan QTY Price
                final_price += (sbttl);
            }

            var disc_item = jQuery(row).find('.discount_number').val();
            if (typeof disc_item !== 'undefined' && disc_item != '' || disc_item != 0) {
                discount_price += parseFloat(disc_item);
            }

            var nameset_value = jQuery(row).find('.nameset_price').val();
            if (typeof nameset_value !== 'undefined' && nameset_value != '') {
                nameset += parseFloat(nameset_value);
            }

        });

        new_price = originalPrice * item_qty;
        if (jQuery('#orderList' + row).hasClass('b1g1_mode')) 
        {
            new_discount = 0; 
            originalPrice = price_tag;
        } else {
            new_discount = price_tag - originalPrice; 
        }

        console.log('Ini Log Baru : ', originalPrice, price_tag);

        jQuery('#discount_normal' + row).text(addCommas(new_discount));
        jQuery('#sell_price_item' + row).text(addCommas(originalPrice));

        // jQuery('#total_price_side').text(addCommas(final_price));
        // jQuery('#total_price_side').text(final_price);
        // jQuery('#total_coba').text(addCommas(discount_price));
        // jQuery('#total_final_price_side').text(addCommas(final_price + nameset));

        updateTotalHarga();
        updateTotalDiskon()
        updateGrandTotal()
    }

    function changeReturQty(row, pst_id, qty) {
        jQuery('#item_qty' + row).val(-Math.abs(jQuery('#item_qty' + row).val()));
        var item_qty = jQuery('#item_qty' + row).val();
        var sell_price_item = replaceComma(jQuery('#sell_price_item' + row).text());
        var subtotal_item = replaceComma(jQuery('#subtotal_item' + row).text());
        var total_row = jQuery('tr[data-list-item]').length;
        var total_nameset_side = replaceComma(jQuery('#total_nameset_side').text());
        if (parseInt(Math.abs(item_qty)) > parseInt(qty)) {
            swal('Melebihi Pembelian', 'Jumlah item tidak boleh melebihi jumlah pada pembelian', 'warning');
            jQuery('#item_qty' + row).val(-Math.abs(qty));
            return false;
        }
        var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item))
        if (parseFloat(item_qty) < 0) {
            jQuery('#subtotal_item' + row).text('-' + addCommas(subtotal));
        } else {
            jQuery('#subtotal_item' + row).text(addCommas(subtotal));
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
        jQuery('#total_final_price_side').text(addCommas(final_price + nameset));
    }

    function namesetPrice(index) {
        if (jQuery('#nameset_price' + index).val() < 0) {
            swal('Minus', 'nameset tidak boleh minus', 'warning');
            jQuery('#nameset_price' + index).val('');
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
        updateGrandTotal();
    }

    function discPrice(index) {
        if (jQuery('#d' + index).val() < 0) {
            swal('Minus', 'nameset tidak boleh minus', 'warning');
            jQuery('#nameset_price' + index).val('');
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
        jQuery('#total_final_price_side').text(addCommas(total_nameset + parseFloat(replaceComma(jQuery(
            '#total_final_price_side').text()))));
    }

    function reloadWaitingForCheckout() {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: 'POST',
            url: "{{ url('check_waiting_for_checkout') }}",
            dataType: 'html',
            success: function(r) {
                // SKIP: offline_pos_v2 uses updateProductTable() to render table from orderItems array
                if (!shouldSkipRowAddition()) {
                    jQuery('#orderTable tr:last').after(r)
                }
            },
            error: function(data) {
                swal('Error', data, 'error');
            }
        });
        return false;
    }

    function reloadComplaint() {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: 'POST',
            url: "{{ url('check_offline_complaint') }}",
            dataType: 'html',
            success: function(r) {
                // SKIP: offline_pos_v2 uses updateProductTable() to render table from orderItems array
                if (!shouldSkipRowAddition()) {
                    jQuery('#orderTable tr:last').after(r)
                }
            },
            error: function(data) {
                swal('Error', data, 'error');
            }
        });
        return false;
    }

    function reloadCity(province) {
        jQuery.ajax({
            type: "GET",
            data: {
                _province: province
            },
            dataType: 'html',
            url: "{{ url('reload_city') }}",
            success: function(r) {
                jQuery('#cust_city').html(r);
            }
        });
    }

    function reloadSubdistrict(city) {
        jQuery.ajax({
            type: "GET",
            data: {
                _city: city
            },
            dataType: 'html',
            url: "{{ url('reload_subdistrict') }}",
            success: function(r) {
                jQuery('#cust_subdistrict').html(r);
            }
        });
    }

    // Flowbite Toast Helper Function (same style as pos_v2)
    function showToast(message, type = 'warning') {
        // Type: success, error, warning, info
        const icons = {
            success: '<i class="cft-standard-solid cft-check text-xl"></i>',
            error: '<i class="cft-standard-solid cft-cancel text-xl"></i>',
            warning: '<i class="cft-standard-solid cft-warning text-xl"></i>',
            info: '<i class="cft-standard-solid cft-info text-xl"></i>'
        };
        
        const colors = {
            success: { bg: 'bg-green-100', icon: 'text-green-500', text: 'text-green-800' },
            error: { bg: 'bg-red-100', icon: 'text-red-500', text: 'text-red-800' },
            warning: { bg: 'bg-orange-100', icon: 'text-orange-500', text: 'text-orange-800' },
            info: { bg: 'bg-blue-100', icon: 'text-blue-500', text: 'text-blue-800' }
        };
        
        const color = colors[type] || colors.warning;
        const icon = icons[type] || icons.warning;
        
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-5 right-5 z-50 space-y-4';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `flex items-center p-4 mb-4 w-full max-w-xs text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800`;
        toast.setAttribute('role', 'alert');
        
        toast.innerHTML = `
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${color.icon} ${color.bg} rounded-lg dark:${color.bg} dark:${color.icon}">
                ${icon}
            </div>
            <div class="ml-3 text-sm font-normal ${color.text} dark:text-gray-400">${message.replace(/\n/g, '<br>')}</div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#${toastId}" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.transition = 'opacity 0.3s';
                toast.style.opacity = '0';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
        }, 5000);
        
        // Handle close button
        const closeBtn = toast.querySelector('[data-dismiss-target]');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                toast.style.transition = 'opacity 0.3s';
                toast.style.opacity = '0';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            });
        }
    }
    
    // Backward compatibility: keep old toast function that calls showToast
    function toast(title, subtitle, type) {
        // Map old type to new type
        const typeMap = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        };
        
        const newType = typeMap[type] || 'warning';
        const message = subtitle ? `${title}<br>${subtitle}` : title;
        showToast(message, newType);
    }

    function replaceComma(str) {
        return String(str || '').replace(/,/g, '');
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

    function reloadItemTotal(pt_id) {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: "POST",
            data: {
                _pt_id: pt_id
            },
            dataType: 'json',
            url: "{{ url('reload_item_total') }}",
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

    {{-- function reloadRefund() { --}}
    {{--    jQuery.ajaxSetup({ --}}
    {{--        headers: { --}}
    {{--            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') --}}
    {{--        } --}}
    {{--    }); --}}
    {{--    jQuery.ajax({ --}}
    {{--        type: "GET", --}}
    {{--        dataType: 'html', --}}
    {{--        url: "{{ url('reload_refund_offline') }}", --}}
    {{--        success: function (r) { --}}
    {{--            jQuery('#refund_reload').html(r); --}}
    {{--            toast('Reloaded', 'Refund berhasil direload', 'success'); --}}
    {{--        } --}}
    {{--    }); --}}
    {{-- }; --}}

    function dpInvoiceRefund() {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: "GET",
            dataType: 'html',
            url: "{{ url('reload_dp_offline') }}",
            success: function(r) {
                jQuery('#dp_invoice_reload').html(r);
                toast('Reloaded', 'Invoice DP berhasil direload', 'success');
            }
        });
    };


    function saveItem(row, pst_id, price, plst_id, pl_id) {
        // var sell_price_item = jQuery('#sell_price_item' + row).text();
        var price = jQuery('#price_tag_item' + row).text().replace(/,/g, '');
        var nameset_price = jQuery('#nameset_price' + row).val();
        var subtotal_item = jQuery('#subtotal_item' + row).text().replace(/,/g, '');
        var item_qty = jQuery('#item_qty' + row).val();
        var disc_value = parseFloat(jQuery('#discount_number' + row).val());
        var raw_text = jQuery('#discount_normal' + row).text();
        var safe_text = (typeof raw_text === 'string') ? raw_text : String(raw_text || '0');
        var disc_text = parseFloat(replaceComma(safe_text));
        // console.log("discount_normal text:", jQuery('#discount_normal' + row).text().replace(/,/g, ''));
        var selected_disc_type = parseInt(jQuery('#discount_selection' + row).val(), 10);

        var td_sell_price = subtotal_item / item_qty;

        var discount_number;
        if (selected_disc_type === 1) {
            discount_number = disc_value;
        } else {
            discount_number = disc_value + disc_text;
        }

        // if (b1g1_temp.length > 0) {
        //     price = replaceComma(sell_price_item);
        // }
        var access_code = jQuery('#u_secret_code').val();
        var pt_id_complaint = jQuery('#_pt_id_complaint').val();
        var final_price = jQuery('#total_final_price_side').text();
        var exchange = jQuery('#_exchange').val();
        var voc_pst_id = jQuery('#_voc_pst_id').val();
        var voc_value = jQuery('#_voc_value').val();
        var pt_id = jQuery('#_pt_id').val();
        var st_id = jQuery('#st_id').val();
        var cross = jQuery('#cross_order').val()
        // alert(subtotal_item);
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: 'POST',
            url: "{{ url('save_transaction_detail_offline') }}",
            data: {
                voc_pst_id: voc_pst_id,
                voc_value: value_price_voc,
                _item_qty: item_qty,
                _exchange: exchange,
                _final_price: parseFloat(replaceComma(final_price)),
                _pt_id_complaint: pt_id_complaint,
                _access_code: access_code,
                _pt_id: pt_id,
                _plst_id: plst_id,
                _pst_id: pst_id,
                _price: price,
                _pl_id: pl_id,
                _sell_price_item: replaceComma(price),
                _subtotal_item: td_sell_price,
                _nameset_price: nameset_price,
                _discount_number: replaceComma(discount_number),
                _st_id: st_id,
                _cross: cross,
            },
            dataType: 'json',
            success: function(r) {
                // console.log(r);
                if (r.status == '200') {
                    toast('Saved', 'Saved', 'success');
                } else if (r.status == '400') {
                    toast('Gagal', 'Gagal simpan transaksi', 'warning');
                }
            },
            error: function(data) {
                swal('Error', data, 'error');
            }
        });
    }


    function removeCommasAndConvertToNumber(value) {
        // Remove commas
        var numberWithoutCommas = value.replace(/,/g, '');
        // Convert to a number
        return parseFloat(numberWithoutCommas);
    }

    function deleteItem(pst_id, price, index, pl_id, plst_id, bandrol) {
        if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
            return;
        }
        jQuery(this).html('');
        var subtotal_item = jQuery('#subtotal_item' + index).text();
        var sell_price_item = jQuery('#sell_price_item' + index).text();
        var subdiscount_item = jQuery('#discount_number' + index).val();
        var total_item = jQuery('#total_item_side').text();
        var total_price = jQuery('#total_price_side').text();
        var total_final = jQuery('#total_final_price_side').text();
        var discount_final = replaceComma(jQuery('#total_discount_value_side').text());
        var item_name = jQuery('#item_name' + index).text();
        var mode = 'delete';
        var item_type = jQuery('#item_type option:selected').val();
        var total_row = jQuery('tr[data-list-item]').length;
        var total_nameset = replaceComma(jQuery('#total_nameset_side').text());
        var nameset = jQuery('#nameset_price' + index).val();
        var free_delete = '';
        if (subtotal_item == '') {
            subtotal_item = parseFloat(0);
        } else {
            subtotal_item = parseFloat(replaceComma(subtotal_item));
        }

        if (subdiscount_item == '') {
            subdiscount_item = parseFloat(0);
        } else {
            subdiscount_item = parseFloat(replaceComma(subdiscount_item));
        }
        if (nameset == '' || nameset == '0') {
            nameset = 0;
        }
        if (total_nameset == '' || total_nameset == '0') {
            total_nameset = 0;
        }
        if (discount_final == 'NaN' || discount_final == '0') {
            discount_final = 0;
        }

        var final_nameset = parseFloat(total_nameset) - parseFloat(nameset);
        var final_current_discount = parseFloat(discount_final) - parseFloat(subdiscount_item);
        var final_price = parseFloat(replaceComma(jQuery('#total_price_side').text())) - parseFloat(replaceComma(
            sell_price_item));
        var key = pst_id + '-' + bandrol + '-' + price;
        var total_key = jQuery.grep(shoes_voucher_temp, function(value) {
            return value === key;
        }).length;

        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: 'POST',
            url: "{{ url('change_waiting_status') }}",
            data: {
                _pst_id: pst_id,
                _pl_id: pl_id,
                _mode: mode,
                _item_type: item_type,
                _plst_id: plst_id
            },
            dataType: 'json',
            success: function(r) {
                jQuery.noConflict();
                if (r.status == '200') {
                    jQuery('#cancel_voucher').trigger('click');
                    toast('Dihapus', 'Item berhasil dihapus', 'success');
                    jQuery('#total_item_side').text(parseInt(total_item) - 1);
                    jQuery('#total_nameset_side').text(addCommas(final_nameset));
                    jQuery('#total_price_side').text(addCommas(final_price));
                    jQuery('#total_discount_value_side').text(addCommas(final_current_discount));
                    jQuery('#total_final_price_side').text(addCommas(final_price + final_nameset -
                        final_current_discount));

                    // if(index = 0 || index < 0){
                    //     jQuery('#total_price_side').text(0);
                    // }

                    console.log(index)

                    b1g1_temp = jQuery.grep(b1g1_temp, function(value) {
                        return value != price;
                    });
                    // console      .log(b1g1_temp);
                    shoes_voucher_temp = jQuery.grep(shoes_voucher_temp, function(value) {
                        return value != key;
                    });
                    if (total_key > 1) {
                        for (let i = 1; i < total_key; i++) {
                            shoes_voucher_temp.push(key);
                        }
                    }
                    jQuery('#orderTable tr').each(function(_, rowElement) {
                        // Only trigger change for rows except the one being deleted
                        if (jQuery(rowElement).attr('id') !== 'orderList' + index) {
                            jQuery(rowElement).find('.item_qty').trigger('change');
                        }
                    });
                    // console.log(shoes_voucher_temp);
                    jQuery('#orderList' + index).remove();
                    // Remove from orderItems array to keep in sync
                    if (window.orderItems) {
                        window.orderItems = window.orderItems.filter(item => item.index !== index);
                    }
                    updateTotalHarga();
                    updateTotalDiskon();
                    updateGrandTotal();
                } else if (r.status == '400') {
                    toast('Gagal',
                        'Item gagal dihapus, jika ingin menghapus, pilih terlebih dahulu LOKASI tempat barang diambil, coba kembali',
                        'danger');
                }
            },
            error: function(data) {
                swal('Error', data, 'error');
            }
        });
    }

    function open_modal_kasir() {
        swal({
            title: "Data pembelian sudah sesuai ..?",
            text: "",
            icon: "warning",
            buttons: [
                'Batal',
                'Benar'
            ],
            dangerMode: false,
        }).then(function(isConfirm) {
            if (isConfirm) {
                checkout()
            }
        })
    }

    function send_nota() {
        var nohp = '085649888272'; // nomer wa customer
        var pesan = 'Your message content here'; // Customize your message as needed
        jQuery.ajax({
            type: "GET",
            url: "http://jezdb.com:3000/api",
            data: {
                nohp: nohp,
                pesan: pesan
            },
            success: function(response) {
                console.log("Message sent successfully:", response);
                // You can add additional success handling here if needed
            },
            error: function(xhr, status, error) {
                console.error("Error sending message:", error);
                // You can add error handling here if needed
            }
        });
    }


    function checkout() {
        var payement = document.getElementById('total_payment').value
        // parseInt(payement.replace(/\D/g, ''));
        payement = payement.replace(/Rp|\./g, '').trim();


        var pt_id_complaint = jQuery('#_pt_id_complaint').val();
        var pm_id = jQuery('#pm_id_offline').val();
        var pm_id_two = jQuery('#pm_id_offline_two').val();
        // var sub_payment = jQuery('#sub_payment_offline').val();
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
        var total_payment = jQuery('#total_payment').val().replace(/Rp|,|\./g, '').trim();
        var total_payment_two = jQuery('#total_payment_two').val();
        var exchange = jQuery('#_exchange').val();
        var real_price = jQuery('#payment_total').text();
        var ur_id = jQuery('#free_sock_customer_ur_id').val();
        var unique_code = jQuery('#unique_code').val();
        var another_cost = jQuery('#another_cost').val();
        var admin_cost = jQuery('#admin_cost').val();
        var cr_id = jQuery('#cr_id').val();
        var shipping_cost = jQuery('#shipping_cost').val();
        var dp_checkBox = jQuery('#dp_checkbox').is(':checked');

        var voc_pst_id = jQuery('#_voc_pst_id').val();
        var voc_value = jQuery('#_voc_value').val();
        var voc_id = jQuery('#_voc_id').val();
        var voc_disc_value = jQuery('#_voc_disc_value').val();
        var voucher_disc_total = jQuery('#_voc_total_disc_value').val();
        var total_discount_side = jQuery('#total_discount_value_side').text();
        if (jQuery('#free_sock_customer_mode').val() == '1' && free_sock_cust_id == '') {
            swal('Customer Belum Selesai',
                'Mohon pastikan bahwa customer sudah selesai melakukan input data dan rating', 'warning');
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
            type: 'POST',
            url: "{{ url('save_transaction_offline') }}",
            data: {
                voc_id: voc_id,
                voc_pst_id: voc_pst_id,
                voc_value: value_price_voc,
                _cr_id: cr_id,
                _shipping_cost: shipping_cost,
                _unique_code: unique_code,
                _another_cost: another_cost,
                _admin_cost: admin_cost,
                _ur_id: ur_id,
                _real_price: replaceComma(real_price),
                _exchange: exchange,
                _pt_id_complaint: pt_id_complaint,
                _charge: charge,
                _cp_id: cp_id,
                _cp_id_two: cp_id_two,
                _total_payment: total_payment,
                _total_payment_two: total_payment_two,
                _access_code: access_code,
                _type: type,
                _dp_checkBox: dp_checkBox,
                _pm_id: pm_id,
                _pm_id_two: pm_id_two,
                _sub_payment: jQuery('#sub_payment_offline').val(),
                _std_id: std_id,
                _cust_id: cust_id,
                _note: note,
                _card_number: card_number,
                _ref_number: ref_number,
                _card_number_two: card_number_two,
                _ref_number_two: ref_number_two,
                _pos_total_vouchers: voucher_disc_total,
                _total_discount_side: replaceComma(total_discount_side),
            },
            dataType: 'json',
            success: function(r) {
                jQuery.noConflict();
                // Hide payment modal using Flowbite API
                const paymentModal = document.getElementById('payment-offline-popup');
                if (paymentModal && typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                    const modal = Flowbite.Modal.getInstance(paymentModal);
                    if (modal) modal.hide();
                } else if (paymentModal) {
                    paymentModal.classList.add('hidden');
                }
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
                    jQuery('#sub_payment_offline').val('');
                    jQuery('#_pt_id_complaint').val('');
                    jQuery('#_exchange').val('');
                    jQuery('#discount_total_temporary').val(0);



                    jQuery('#card_provider_content').addClass('hidden');
                    jQuery('#card_number_label').addClass('hidden');
                    jQuery('#ref_number_label').addClass('hidden');
                    jQuery('#charge_label').addClass('hidden');
                    jQuery('#card_provider_content_two').addClass('hidden');
                    jQuery('#card_number_label_two').addClass('hidden');
                    jQuery('#ref_number_label_two').addClass('hidden');
                    jQuery('#total_payment_two_label').addClass('hidden');
                    jQuery('#payment_method_two_section').addClass('hidden');
                    jQuery('#payment_type_content_two').addClass('hidden');
                    jQuery('#total_payment_two').addClass('hidden');

                    jQuery('#note').val('');
                    jQuery('#_pt_id').val('');
                    jQuery('#u_secret_code').val('');
                    // Hide InputCodeModal using Flowbite API
                    const inputCodeModal = document.getElementById('InputCodeModal');
                    if (inputCodeModal && typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                        const modal = Flowbite.Modal.getInstance(inputCodeModal);
                        if (modal) modal.hide();
                    } else if (inputCodeModal) {
                        inputCodeModal.classList.add('hidden');
                    }
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

                    jQuery('#dp_checkbox').prop('checked', false).change();

                    jQuery('#total_discount_value_side').text('0');
                    toast('Berhasil', 'Transaksi Berhasil Disimpan', 'success');

                    var win = window.open('{{ url('') }}/print_offline_invoice/' + r.invoice,
                        '_blank');

                    if (win) {
                        win.focus();
                    } else {
                        alert('Please allow popups for this website');
                    }
                    @php  session()->forget('voc_item') @endphp
                    b1g1_temp = [];
                    // reloadRefund();
                    console.log(r.invoice);
                    // swal('Berhasil', 'Transaksi Berhasil Disimpan', 'success');

                    // print nota off dulu sam e
                    // console.log(st_id);
                    {{-- setTimeout(() => { --}}
                    {{--    if (std_id > '0') { --}}
                    {{--        var win = window.open('{{ url('') }}/print_offline_invoice/' + r --}}
                    {{--            .invoice, '_blank'); --}}
                    {{--    } else { --}}
                    {{--        var win = window.open('{{ url('') }}/print_invoice/' + r.invoice, --}}
                    {{--            '_blank'); --}}
                    {{--    } --}}
                    {{--    if (win) { --}}
                    {{--        win.focus(); --}}
                    {{--    } else { --}}
                    {{--        alert('Please allow popups for this website'); --}}
                    {{--    } --}}
                    {{-- }, 2000); --}}
                } else if (r.status == '400') {
                    swal('Gagal', 'Gagal simpan transaksi', 'warning');
                }
            },
            error: function(data) {
                swal('Error', data, 'error');
            }
        });
    };

    // function changeDiscountPercentage(row, pst_id, pls_qty)
    // {
    //     var item_qty = jQuery('#item_qty'+row).val();
    //     var sell_price_item = replaceComma(jQuery('#sell_price_item'+row).text());
    //     var subtotal_item = replaceComma(jQuery('#subtotal_item'+row).text());
    //     var total_row = jQuery('tr[data-list-item]').length;
    //     var total_nameset_side = replaceComma(jQuery('#total_nameset_side').text());
    //
    //     var b1g1_total_row = b1g1_temp.length;
    //     var b1g1_qty_total = 0;
    //     if (b1g1_total_row > 0) {
    //         jQuery('#orderTable tr').each(function(index, row) {
    //             var b1g1_qty = parseFloat(jQuery(row).find('.item_qty').val());
    //             if (typeof b1g1_qty === 'undefined' || b1g1_qty == '' || isNaN(b1g1_qty)) {
    //                 b1g1_qty = 0;
    //             }
    //             if (jQuery(row).hasClass('b1g1_mode')) {
    //                 b1g1_qty_total += b1g1_qty;
    //             }
    //         });
    //         if (parseFloat(b1g1_qty_total) > 2) {
    //             jQuery('#item_qty'+row).val('1');
    //             swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
    //             return false;
    //         }
    //     }
    //
    //     if (jQuery('#orderList'+row).hasClass('b1g1_mode')) {
    //         if (item_qty > 2){
    //             jQuery('#item_qty'+row).val('1');
    //             swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
    //             return false;
    //         }
    //         if (item_qty%2 == 0) {
    //             item_qty = item_qty/2;
    //         } else {
    //             item_qty = Math.ceil(item_qty/2);
    //         }
    //     }
    //     var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item))
    //     if (parseFloat(item_qty) < 0) {
    //         jQuery('#subtotal_item'+row).text('-'+addCommas(subtotal));
    //     } else {
    //         jQuery('#subtotal_item'+row).text(addCommas(subtotal));
    //     }
    //     var final_price = 0;
    //     var nameset = 0;
    //     jQuery('#orderTable tr').each(function(index, row) {
    //         if (jQuery(row).find('.subtotal_item').text() != '') {
    //             var sbttl = parseFloat(replaceComma(jQuery(row).find('.subtotal_item').text()));
    //             if (typeof sbttl === 'undefined' && sbttl == '') {
    //                 sbttl = 0;
    //             }
    //             final_price += sbttl;
    //         }
    //         var nameset_value = jQuery(row).find('.nameset_price').val();
    //         if (typeof nameset_value !== 'undefined' && nameset_value != '') {
    //             nameset += parseFloat(nameset_value);
    //         }
    //     });
    //     jQuery('#total_price_side').text(addCommas(final_price));
    //     jQuery('#total_final_price_side').text(addCommas(final_price+nameset));
    // }

    //disini diskon totalnya
    function changeDiscountNumber(row, pst_id, pls_qty) {
        var item_qty = jQuery('#item_qty' + row).val();
        // var temporary_discount = jQuery('#discount_total_temporary').val();
        var sell_price_item = replaceComma(jQuery('#sell_price_item' + row).text());
        var subtotal_item = replaceComma(jQuery('#subtotal_item' + row).text());
        var total_row = jQuery('tr[data-list-item]').length;
        var total_nameset_side = replaceComma(jQuery('#total_nameset_side').text());
        var discount_percentage = jQuery('#discount_percentage' + row).val();

        var total_disc_item = 0;
        jQuery('#orderTable tr').each(function(index, row) {
            var disc_item = jQuery(row).find('#discount_number').val();
            if (typeof disc_item !== 'undefined' && disc_item !== 0) {
                total_disc_item += parseFloat(disc_item);
            }

            console.log(disc_item);
        });
        var temporary_discount = parseFloat(jQuery('#discount_total_temporary').val()) || 0;
        console.log('Total Discount Items:', total_disc_item);
        console.log('Temporary Discount:', temporary_discount);

        var total_discount = total_disc_item + temporary_discount;
        jQuery('#total_discount_value_side').text(addCommas(total_discount));

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
                jQuery('#item_qty' + row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs',
                    'warning');
                return false;
            }
        }

        if (jQuery('#orderList' + row).hasClass('b1g1_mode')) {
            if (item_qty > 2) {
                jQuery('#item_qty' + row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs',
                    'warning');
                return false;
            }
            if (item_qty % 2 == 0) {
                item_qty = item_qty / 2;
            } else {
                item_qty = Math.ceil(item_qty / 2);
            }
        }
        var subtotal = parseFloat(item_qty) * (parseFloat(sell_price_item))
        if (parseFloat(item_qty) < 0) {
            jQuery('#subtotal_item' + row).text('-' + addCommas(subtotal));
        } else {
            jQuery('#subtotal_item' + row).text(addCommas(subtotal));
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
        var discount = parseFloat(jQuery('#discount_number' + row).val()) || 0;

        // var discount_with_qty = discount * item_qty;

        var percentage = (discount / sell_price_item) * 100;
        // jQuery('#discount_number' + row).val(discount_with_qty);
        jQuery('#discount_percentage' + row).val(percentage.toFixed(2));
        // Mengurangi diskon dari subtotal
        var subtotal = parseFloat(item_qty) * parseFloat(sell_price_item) - discount;

        if (parseFloat(item_qty) < 0) {
            jQuery('#subtotal_item' + row).text('-' + addCommas(subtotal));
        } else {
            jQuery('#subtotal_item' + row).text(addCommas(subtotal));
        }

        var temp_final = replaceComma(jQuery('#total_price_side').text());

        updateTotalDiskon();
        updateGrandTotal();
    }


    // hitung discount masih salah
    function changeDiscountPercentage(row, pst_id, pls_qty) {
        var item_qty = jQuery('#item_qty' + row).val();
        var sell_price_item = replaceComma(jQuery('#sell_price_item' + row).text());
        var current_discount = replaceComma(jQuery('#total_discount_value_side').text());
        var subtotal_item = replaceComma(jQuery('#subtotal_item' + row).text());
        var total_row = jQuery('tr[data-list-item]').length;
        var total_nameset_side = replaceComma(jQuery('#total_nameset_side').text());

        console.log(current_discount)

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
                jQuery('#item_qty' + row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs',
                    'warning');
                return false;
            }
        }

        if (jQuery('#orderList' + row).hasClass('b1g1_mode')) {
            if (item_qty > 2) {
                jQuery('#item_qty' + row).val('1');
                swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs',
                    'warning');
                return false;
            }
            if (item_qty % 2 == 0) {
                item_qty = item_qty / 2;
            } else {
                item_qty = Math.ceil(item_qty / 2);
            }
        }
        var subtotal = item_qty * parseFloat(sell_price_item)
        if (parseFloat(item_qty) < 0) {
            jQuery('#subtotal_item' + row).text('-' + addCommas(subtotal));
        } else {
            jQuery('#subtotal_item' + row).text(addCommas(subtotal));
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

        // Menghitung subtotal asli tanpa diskon untuk baris saat ini
        var originalSubtotal = parseFloat(item_qty) * parseFloat(sell_price_item);

        // Mendapatkan nilai diskon dari input dengan ID discount_percentage
        var discountPercentage = parseFloat(jQuery('#discount_percentage' + row).val()) || 0;
        var discount = Math.floor((discountPercentage / 100) * originalSubtotal);

        // Menghitung subtotal setelah diskon untuk baris saat ini
        var subtotal = originalSubtotal - discount;

        // Mengupdate nilai subtotal dan diskon di baris saat ini
        jQuery('#subtotal_item' + row).text(addCommas(subtotal));
        jQuery('#discount_number' + row).val(discount);

        var discount_item = jQuery('#discount_number' + row).val();
        var final_disc = parseFloat(current_discount) + parseFloat(discount_item)
        // jQuery('#total_discount_value_side').text(addCommas(final_disc));


        // Menghitung total harga seluruh pesanan setelah diskon diterapkan ke baris saat ini
        var final_price = 0;
        jQuery('#orderTable tr').each(function(index, rowElement) {
            if (jQuery(rowElement).find('.subtotal_item').text() != '') {
                var sbttl = parseFloat(replaceComma(jQuery(rowElement).find('.subtotal_item').text()));
                if (typeof sbttl === 'undefined' || sbttl == '') {
                    sbttl = 0;
                }
                final_price += sbttl;
            }
        });

        updateTotalHarga();
        updateTotalDiskon();
        updateGrandTotal();
    }

    jQuery(document).delegate('#add_to_item_list', 'click', function(e) {
        e.preventDefault();
        jQuery('#product_name_input').val('');
        jQuery('#itemList').html('');
        jQuery('#itemList').fadeOut();
        jQuery('#_exchange').val('true');
        var pt_id = '';
        // var st_id = "{{ $data['user']->st_id }}";
        // create condition for st_id, check from click or auth
        var st_id = jQuery('#st_id').val() ?? "{{ $data['user']->st_id }}";
        if (st_id != "{{ $data['user']->st_id }}") {
            var cross = jQuery(this).attr('data-cross');
            jQuery('#cross_order').val('true');

        }
        var p_name = jQuery(this).attr('data-p_name');
        var pst_id = jQuery(this).attr('data-pst_id');
        var fs = jQuery(this).attr('data-fs');
        var pl_id = jQuery(this).attr('data-pl_id');
        var pls_qty = jQuery(this).attr('data-pls_qty');
        var psc_id = jQuery(this).attr('data-psc_id');
        var plst_id = jQuery(this).attr('data-plst_id');
        var sell_price = jQuery(this).attr('data-sell_price');
        var total_row = parseFloat(jQuery('#total_row').val());
        jQuery('#total_row').val(total_row + 1);
        var pos_item_list = jQuery('.pos_item_list' + pst_id).length;
        var item_type = jQuery('#item_type option:selected').val();
        var mode = 'add';
        var b1g1_id = jQuery(this).attr('data-b1g1_id');
        var b1g1_price = jQuery(this).attr('data-b1g1_price');
        var highlight = '';
        var b1g1_mode = '';
        var bandrol = jQuery(this).attr('data-bandrol');


        console.log(b1g1_id);
        console.log('pt_id: ', pt_id);
        console.log('st_id: ', st_id);
        console.log('p_name: ', p_name);
        console.log('sell_price: ', sell_price);
        console.log('mode: ', mode);
        console.log('pst_id: ', pst_id);
        console.log('pl_id: ', pl_id);
        console.log('psc_id: ', psc_id);
        console.log('plst_id: ', plst_id);
        console.log('pos_item: ', pos_item_list);
        console.log('item_type: ', item_type);
        console.log('st_id: ', st_id);
        console.log('cross: ', cross);

        console.log('B1G1 : ', b1g1_id);

        if (psc_id == '1') {
            shoes_voucher_temp.push(pst_id + '-' + bandrol + '-' + sell_price);
            // console.log(shoes_voucher_temp);
        }

        if (b1g1_id != '' && b1g1_price != '') {
            b1g1_temp.push(b1g1_price);
            highlight = 'background:#ffc107; color:#000; font-weight:bold; border-radius:20px;';
            b1g1_mode = 'b1g1_mode';
            b1g1_temp = b1g1_temp.sort((a, b) => b - a);
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

                            jQuery(row).find('.subtotal_item').text('0');

                            console.log('BOGO CEK BOLO : ', sell_price);
                        } else {
                            sell_price = 0;

                            console.log('BOGO CEK BOLO : ', sell_price);
                        }
                        jQuery(row).find('.item_qty').trigger('change');

                    }
                });
                if (parseFloat(b1g1_qty_total) >= 2) {
                    swal('1 Invoice 1 B1G1',
                        'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
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
            type: 'POST',
            url: "{{ url('change_waiting_status') }}",
            data: {
                _pt_id: pt_id,
                _pst_id: pst_id,
                _pl_id: pl_id,
                _mode: mode,
                _item_type: item_type,
                _plst_id: plst_id,
                _sell_price: sell_price
            },
            dataType: 'json',
            success: function(r) {
                jQuery.noConflict();
                if (r.status == '200') {
                    var discount_normal = bandrol - sell_price;
                    toast('Ditambah', 'Item berhasil ditambah', 'success');
                    jQuery('#total_item_side').text(parseInt(total_item) + 1);
                    jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(
                        total_price)) + parseFloat(sell_price)));
                    jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(
                        total_final_price)) + parseFloat(sell_price)));
                    // Hide autocomplete list after product is selected (regardless of item_type)
                    jQuery('#itemList').addClass('hidden').removeClass('block').fadeOut();
                    jQuery('#product_name_input').val('');
                    
                    if (item_type == 'waiting') {
                        // SKIP: offline_pos_v2 uses updateProductTable() to render table from orderItems array
                        if (shouldSkipRowAddition()) {
                            console.log('⏭️ Skipping row addition in offline_pos_v2_js - will use updateProductTable() instead');
                            // Row will be added by updateProductTable() after AJAX success
                            // Trigger updateProductTable() and updateOrderDisplay() after a short delay to ensure orderItems is updated
                            setTimeout(function() {
                                if (typeof window.updateProductTable === 'function' && window.orderItems && window.orderItems.length > 0) {
                                    console.log('🔄 Calling updateProductTable() after AJAX success');
                                    window.updateProductTable();
                                }
                                if (typeof window.updateOrderDisplay === 'function') {
                                    window.updateOrderDisplay();
                                }
                                if (typeof updateGrandTotal === 'function') {
                                    updateGrandTotal();
                                }
                            }, 100);
                        } else {
                            jQuery('#orderTable tr:last').after(
                            "<tr data-list-item class='pos_item_list mb-2 bg-light-primary " +
                            b1g1_mode + "' id='orderList" + (total_row + 1) + "'>" +
                            " <td style='white-space: nowrap; font-size:14px; " + highlight +
                            "' id='item_name" + (total_row + 1) + "'>" + p_name + "</td>" +
                            " <td>" + (pls_qty) + "</td> " +
                            "<td><input type='number' min='0' style='width: 13rem;' class='form-control border-dark col-5 basicInput2 qty-input" +
                            pst_id + " item_qty' id='item_qty" + (total_row + 1) +
                            "' value='1' onchange='return changeQty(" + (total_row + 1) + ", " +
                            pst_id + ", " + (pls_qty) + ")'></td> " +
                            "<td>" +
                            "<select style='width: 10rem;' data-sellPrice='" + sell_price +
                            "' class='form-control col-10 mr-4' id='discount_selection" + (
                                total_row + 1) + "' onchange='handleSelectChange(" + (
                                total_row + 1) + ",this)'>" +
                            "<option value='0'>Discount Extra</option>" +
                            "<option value='1'>Discount Promo</option>" +
                            "</select>" +
                            "</td>" +
                            "<td><input type='number' style='width: 13rem;' class='form-control border-dark col-5 basicInput2 discount-percent" +
                            pst_id + " discount_percentage' id='discount_percentage" + (
                                total_row + 1) +
                            "' value='0' onchange='return changeDiscountPercentage(" + (
                                total_row + 1) + ", " + pst_id + ", " + (pls_qty) + ")'></td>" +
                            " <td><input type='number' style='width: 13rem;' class='form-control border-dark col-8 basicInput2 discount-number" +
                            pst_id + " discount_number' id='discount_number" + (total_row + 1) +
                            "' value='0' onchange='return changeDiscountNumber(" + (total_row +
                                1) + ", " + pst_id + ", " + (pls_qty) + ")'></td>" +
                            "<td><input type='number' style='width: 13rem;' class='col-8 nameset_price namset-input' id='nameset_price" +
                            (total_row + 1) + "' onchange='return namesetPrice(" + (total_row +
                                1) + ")'/></td> " +
                            "<td><span class='price_tag_item' id='price_tag_item" + (total_row +
                                1) + "'>" + addCommas(bandrol) + "</span></td> " +
                            " <td><span class='sell_price_item' id='sell_price_item" + (
                                total_row + 1) + "'>" + addCommas(sell_price) + "</span></td>" +
                            "<td><span class='discount_normal' style='width: 13rem;' id='discount_normal" +
                            (total_row + 1) + "'>" + addCommas(discount_normal) +
                            "</span></td> " +
                            " <td><span class='subtotal_item' id='subtotal_item" + (total_row +
                                1) + "'>" + addCommas(sell_price) + "</span></td>" +
                            " <td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem" +
                            (total_row + 1) + "' onclick='return saveItem(" + (total_row + 1) +
                            ", " + pst_id + ", " + sell_price + ", " + plst_id + ", " + pl_id +
                            ")'>" +
                            " <i class='fa fa-eye' style='display:none;'></i></a> " +
                            " <a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem(" +
                            pst_id + ", " + sell_price + ", " + (total_row + 1) + ", " + pl_id +
                            ", " + plst_id + ", " + bandrol +
                            ")'><i class='fas fa-trash-alt'></i></a></div></td></tr>");
                        }
                    } else {
                        // SKIP: offline_pos_v2 uses updateProductTable() to render table from orderItems array
                        if (shouldSkipRowAddition()) {
                            console.log('⏭️ Skipping row addition in offline_pos_v2_js (else branch) - will use updateProductTable() instead');
                        } else {
                            jQuery('#orderTable tr:last').after("" +
                            "<tr data-list-item class='pos_item_list mb-2 bg-light-primary " +
                            b1g1_mode + "' id='orderList" + (total_row + 1) + "'>" +
                            "<td style='white-space: nowrap; font-size:14px; " + highlight +
                            "' id='item_name" + (total_row + 1) + "'>" + p_name + "</td>" +
                            "<td>" + (pls_qty) + "</td> " +
                            "<td><input type='number' min='0' style='width: 13rem;' class='form-control border-dark col-5 basicInput2 qty-input" +
                            pst_id + " item_qty' id='item_qty" + (total_row + 1) +
                            "' value='1' onchange='return changeQty(" + (total_row + 1) + ", " +
                            pst_id + ", " + (pls_qty) + ")'></td> " +
                            "<td>" +
                            "<select style='width: 10rem;' data-sellPrice='" + sell_price +
                            "' class='form-control col-10 mr-4' id='discount_selection" + (
                                total_row + 1) + "' onchange='handleSelectChange(" + (
                                total_row + 1) + ",this)'>" +
                            "<option value='0'>Discount Extra</option>" +
                            "<option value='1'>Discount Promo</option>" +
                            "</select>" +
                            "</td>" +
                            "<td><input type='number' style='width: 13rem;' class='form-control border-dark col-5 basicInput2 discount-percent" +
                            pst_id + " discount_percentage' id='discount_percentage" + (
                                total_row + 1) +
                            "' value='0' onchange='return changeDiscountPercentage(" + (
                                total_row + 1) + ", " + pst_id + ", " + (pls_qty) + ")'></td>" +
                            " <td><input type='number' style='width: 13rem;' class='form-control border-dark col-8 basicInput2 discount-number" +
                            pst_id + " discount_number' id='discount_number" + (total_row + 1) +
                            "' value='0' onchange='return changeDiscountNumber(" + (total_row +
                                1) + ", " + pst_id + ", " + (pls_qty) + ")'></td>" +
                            "<td><input type='number' style='width: 13rem;' class='col-8 nameset_price namset-input' id='nameset_price" +
                            (total_row + 1) + "' onchange='return namesetPrice(" + (total_row +
                                1) + ")'/></td> " +
                            "<td><span class='price_tag_item' id='price_tag_item" + (total_row +
                                1) + "'>" + addCommas(bandrol) + "</span></td> " +
                            "<td><span class='sell_price_item' id='sell_price_item" + (
                                total_row + 1) + "'>" + addCommas(sell_price) +
                            "</span></td> " +
                            "<td><span class='discount_normal' style='width: 13rem;' id='discount_normal" +
                            (total_row + 1) + "'>" + addCommas(discount_normal) +
                            "</span></td> " +
                            "<td><span class='subtotal_item' id='subtotal_item" + (total_row +
                                1) + "'>" + addCommas(sell_price) + "</span></td> " +
                            "<td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem" +
                            (total_row + 1) + "' onclick='return saveItem(" + (total_row + 1) +
                            ", " + pst_id + ", " + sell_price + ", " + r.plst_id + ", " +
                            pl_id + ")'><i class='fa fa-eye' style='display:none;'></i></a> " +
                            "<a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem(" +
                            pst_id + ", " + sell_price + ", " + (total_row + 1) + ", " + pl_id +
                            ", " + r.plst_id + ", " + bandrol +
                            ")'><i class='fas fa-trash-alt'></i></a></div></td></tr>");
                    }
                    // console.log(b1g1_temp);
                } else if (r.status == '400') {
                    toast('Gagal', 'Item gagal ditambah', 'danger');
                }
            },
            error: function(data) {
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

    jQuery(document).delegate('#add_custom_amount', 'click', function(e) {
        e.preventDefault();
        var pt_id = '';
        var st_id = "{{ $data['user']->st_id }}";
        var p_name = 'Custom Amount';
        var sell_price = document.getElementsByTagName('p')[0].innerHTML;
        var mode = 'add';
        var pst_id = document.getElementById('pst_custom').value;
        var pl_id = document.getElementById('pl_custom').value;
        var psc_id = document.getElementById('psc_custom').value;
        var plst_id = jQuery(this).attr('data-plst_id');
        var pos_item_list = jQuery('.pos_item_list' + pst_id).length;
        var item_type = jQuery('#item_type option:selected').val();
        var b1g1_id = jQuery(this).attr('data-b1g1_id');
        var b1g1_price = jQuery(this).attr('data-b1g1_price');
        var highlight = '';
        var b1g1_mode = '';
        var total_row = parseFloat(jQuery('#total_row').val());
        jQuery('#total_row').val(total_row + 1);
        var total_item = jQuery('#total_item_side').text();
        var total_price = jQuery('#total_price_side').text();
        var total_final_price = jQuery('#total_final_price_side').text();

        console.log('pt_id: ', pt_id);
        console.log('st_id: ', st_id);
        console.log('p_name: ', p_name);
        console.log('sell_price: ', sell_price);
        console.log('mode: ', mode);
        console.log('pst_id: ', pst_id);
        console.log('pl_id: ', pl_id);
        console.log('psc_id: ', psc_id);
        console.log('plst_id: ', plst_id);
        console.log('pos_item: ', pos_item_list);
        console.log('item_type: ', item_type);


        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: "{{ url('add_custom_amount') }}",
            type: 'POST',
            data: {
                _pt_id: pt_id,
                _pst_id: pst_id,
                _pl_id: pl_id,
                _mode: mode,
                _item_type: item_type,
                _plst_id: plst_id,
                _sell_price: sell_price
            },
            dataType: 'json',
            success: function(r) {
                jQuery.noConflict();
                console.log(r.status);
                if (r.status == '200') {
                    toast('Ditambah', 'Item berhasil ditambah', 'success');
                    jQuery('#total_item_side').text(parseInt(total_item) + 1);
                    jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(
                        total_price)) + parseFloat(sell_price)));
                    jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(
                        total_final_price)) + parseFloat(sell_price)));
                    if (item_type == 'waiting') {
                        // SKIP: offline_pos_v2 uses updateProductTable() to render table from orderItems array
                        if (shouldSkipRowAddition()) {
                            console.log('⏭️ Skipping row addition in offline_pos_v2_js (add_custom_amount) - will use updateProductTable() instead');
                        } else {
                            jQuery('#orderTable tr:last').after(
                            "<tr data-list-item class='pos_item_list mb-2 bg-light-primary " +
                            b1g1_mode + "' id='orderList" + (total_row + 1) + "'>" +
                            "<td style='white-space: nowrap; font-size:14px; " + highlight +
                            "' id='item_name" + (total_row + 1) + "'>" + p_name + "</td>" +
                            " <td>" + (1) + "</td> " +
                            " <td><input type='number' class='form-control border-dark col-5 basicInput2" +
                            pst_id + " item_qty' id='item_qty" + (total_row + 1) +
                            "' value='1' onchange='return changeQty(" + (total_row + 1) + ", " +
                            pst_id + ", " + (1) + ")'></td>" +
                            " <td><input type='number' class='form-control border-dark col-5 basicInput2" +
                            pst_id + " discount_percentage' id='discount_percentage" + (
                                total_row + 1) +
                            "' value='0' onchange='return changeDiscountPercentage(" + (
                                total_row + 1) + ", " + pst_id + ", " + (1) + ")'></td>" +
                            " <td><input type='text' class='form-control border-dark col-8 basicInput2" +
                            pst_id + " discount_number' id='discount_number" + (total_row + 1) +
                            "' value='0' onchange='return changeDiscountNumber(" + (total_row +
                                1) + ", " + pst_id + ", " + (1) + ")'></td>" +
                            " <td><input type='number' class='col-8 nameset_price' id='nameset_price" +
                            (total_row + 1) + "' onchange='return namesetPrice(" + (total_row +
                                1) + ")'/></td>" +
                            " <td><span class='sell_price_item' id='sell_price_item" + (
                                total_row + 1) + "'>" + addCommas(sell_price) + "</span></td>" +
                            " <td><span class='subtotal_item' id='subtotal_item" + (total_row +
                                1) + "'>" + addCommas(sell_price) + "</span></td>" +
                            " <td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem" +
                            (total_row + 1) + "' onclick='return saveItem(" + (total_row + 1) +
                            ", " + pst_id + ", " + sell_price + ", " + r.plst_id + ", " +
                            pl_id +
                            ")'>" +
                            " <i class='fa fa-eye' style='display:none;'></i></a> " +
                            " <a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem(" +
                            pst_id + ", " + sell_price + ", " + (total_row + 1) + ", " + pl_id +
                            ", " + r.plst_id +
                            ")'><i class='fas fa-trash-alt'></i></a></div></td></tr>");
                        }
                    } else {
                        // SKIP: offline_pos_v2 uses updateProductTable() to render table from orderItems array
                        if (shouldSkipRowAddition()) {
                            console.log('⏭️ Skipping row addition in offline_pos_v2_js (add_custom_amount else) - will use updateProductTable() instead');
                        } else {
                            jQuery('#orderTable tr:last').after("" +
                            "<tr data-list-item class='pos_item_list mb-2 bg-light-primary " +
                            b1g1_mode + "' id='orderList" + (total_row + 1) + "'>" +
                            " <td style='white-space: nowrap; font-size:14px; " + highlight +
                            "' id='item_name" + (total_row + 1) + "'>" + p_name + "</td>" +
                            " <td>" + (1) + "</td> " +
                            "<td><input type='number' min='0' style='width: 13rem;' class='form-control border-dark col-5 basicInput2 qty-input" +
                            pst_id + " item_qty' id='item_qty" + (total_row + 1) +
                            "' value='1' onchange='return changeQty(" + (total_row + 1) + ", " +
                            pst_id + ", " + (1) + ")'></td> " +
                            "<td>" +
                            "<select style='width: 10rem;' data-sellPrice='" + sell_price +
                            "' class='form-control col-10 mr-4' id='discount_selection" + (
                                total_row + 1) + "' onchange='handleSelectChange(" + (
                                total_row + 1) + ",this)'>" +
                            "<option value='0'>Discount Extra</option>" +
                            "<option value='1'>Discount Promo</option>" +
                            "</select>" +
                            "</td>" +
                            "<td><input type='number' style='width: 13rem;' class='form-control border-dark col-5 basicInput2 discount-percent" +
                            pst_id + " discount_percentage' id='discount_percentage" + (
                                total_row + 1) +
                            "' value='0' onchange='return changeDiscountPercentage(" + (
                                total_row + 1) + ", " + pst_id + ", " + (1) + ")'></td>" +
                            " <td><input type='number' style='width: 13rem;' class='form-control border-dark col-8 basicInput2 discount-number" +
                            pst_id + " discount_number' id='discount_number" + (total_row + 1) +
                            "' value='0' onchange='return changeDiscountNumber(" + (total_row +
                                1) + ", " + pst_id + ", " + (1) + ")'></td>" +
                            "<td><input type='number' style='width: 13rem;' class='col-8 nameset_price namset-input' id='nameset_price" +
                            (total_row + 1) + "' onchange='return namesetPrice(" + (total_row +
                                1) + ")'/></td> " +
                            "<td><span class='price_tag_item' id='price_tag_item" + (total_row +
                                1) + "'>" + addCommas(sell_price) + "</span></td> " +
                            " <td><span class='sell_price_item' id='sell_price_item" + (
                                total_row + 1) + "'>" + addCommas(sell_price) + "</span></td>" +
                            "<td><span class='discount_normal' style='width: 13rem;' id='discount_normal" +
                            (total_row + 1) + "'>" + 0 + "</span></td> " +
                            " <td><span class='subtotal_item' id='subtotal_item" + (total_row +
                                1) + "'>" + addCommas(sell_price) + "</span></td>" +
                            " <td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem" +
                            (total_row + 1) + "' onclick='return saveItem(" + (total_row + 1) +
                            ", " + pst_id + ", " + sell_price + ", " + r.plst_id + ", " +
                            pl_id +
                            ")'>" +
                            " <i class='fa fa-eye' style='display:none;'></i></a> " +
                            " <a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem(" +
                            pst_id + ", " + sell_price + ", " + (total_row + 1) + ", " + pl_id +
                            ", " + r.plst_id + ", " + sell_price +
                            ")'><i class='fas fa-trash-alt'></i></a></div></td></tr>");
                    }
                } else if (r.status == '400') {
                    toast('Gagal', 'Item gagal ditambah', 'danger');
                }
            },
            error: function(data) {
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
        var inpBarcode = jQuery(this).val();
        var type = jQuery('#std_id option:selected').text();
        var item_type = jQuery('#item_type option:selected').val();
        var std_id = jQuery('#std_id').val();

        var barcode = inpBarcode.replace(/(\r\n|\n|\r)/gm, '');

        jQuery('#barcode_input').val('');
        if (item_type === 'store') {
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: "{{ url('has_waiting_status') }}",
                method: "GET",
                data: {
                    barcode: barcode
                },
                dataType: "json",
                success: function(r) {
                    if (r.status == '200') {
                        // Handle success response
                        console.log('Barcode found in waiting status');
                        if (confirm(r.message)) {
                            // Proceed with barcode scan
                            
                            processBarcodeScan(barcode, type, item_type, std_id);
                        } else {
                            
                            return false;
                        }
                    } else {
                        // Handle other status
                        console.log('Barcode not found or other status');
                        processBarcodeScan(barcode, type, item_type, std_id);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking waiting status:', error);
                }
            });
            return; // Prevent further execution until AJAX completes
        } else {
            
            processBarcodeScan(barcode, type, item_type, std_id);
        }
    });

        

    function processBarcodeScan(barcode, type, item_type, std_id) {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: "{{ url('pos_barcode_scan') }}",
            method: "POST",
            dataType: "json",
            data: {
                barcode: barcode,
                type: type,
                _item_type: item_type,
                _std_id: std_id
            },
            beforeSend: function() {
                // Show the loading indicator
                jQuery('#loader').show();
                jQuery('#barcode_input').prop('disabled', true);
            },
            success: function(r) {
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
                    jQuery('#total_row').val(total_row + 1);
                    var pos_item_list = jQuery('.pos_item_list' + pst_id).length;
                    var item_type = jQuery('#item_type option:selected').val();
                    var mode = 'add';
                    var b1g1_id = r.b1g1_id;
                    var b1g1_price = r.b1g1_price;
                    var highlight = '';
                    var b1g1_mode = '';
                    var total_discount = jQuery('#total_discount_value_side').text();
                    var discount_normal = r.bandrol - r.sell_price;

                    if (psc_id == '1') {
                        shoes_voucher_temp.push(pst_id + '-' + bandrol + '-' + sell_price);
                        // console.log(shoes_voucher_temp);
                    }

                    console.log('B1G1 : ', b1g1_id, 'Price Bogo : ', b1g1_price);

                    if (b1g1_id != '' && b1g1_price != '') {
                        b1g1_temp.push(b1g1_price);
                        highlight =
                            'background:#ffc107; color:#000; font-weight:bold; border-radius:20px;';
                        b1g1_mode = 'b1g1_mode';
                        b1g1_temp = b1g1_temp.sort((a, b) => b - a);
                        var b1g1_total_row = b1g1_temp.length;
                        var b1g1_qty_total = 0;
                        if (b1g1_total_row > 0) {
                            jQuery('#orderTable tr').each(function(index, row) {
                                var b1g1_qty = parseFloat(jQuery(row).find('.item_qty')
                                    .val());
                                if (typeof b1g1_qty === 'undefined' || b1g1_qty == '' ||
                                    isNaN(b1g1_qty)) {
                                    b1g1_qty = 0;
                                }

                                if (jQuery(row).hasClass('b1g1_mode')) {
                                    b1g1_qty_total += b1g1_qty;
                                    if (parseFloat(sell_price) >= parseFloat(b1g1_temp[
                                        0]) || parseFloat(sell_price) == parseFloat(
                                            b1g1_temp[0])) {
                                        sell_price = sell_price;
                                        console.log('row : ', row);
                                        jQuery(row).find('.sell_price_item').text('0');
                                        jQuery(row).find('.subtotal_item').text('0');
                                        var bandrol_price = parseFloat(jQuery(row).find('.price_tag_item').text().replace(/,/g, '')) || 0;
                                        jQuery(row).find('.discount_normal').text(addCommas(bandrol_price));

                                        console.log('BOGO CEK BOLO : ', sell_price);
                                    } else {
                                        discount_normal = sell_price;
                                        sell_price = 0;
                                        jQuery(row).find('.item_qty').trigger('change');
                                    }

                                }
                            });
                            if (parseFloat(b1g1_qty_total) >= 2) {
                                swal({
                                    title: '1 Invoice 1 B1G1',
                                    text: '', // kosongkan text
                                    content: {
                                        element: "p",
                                        attributes: {
                                            innerHTML: 'Maksimal item B1G1 hanya 2 item yaaa......,<br>Silahkan Transaksi di Invoice baru ya 😊😊',
                                            style: "text-align: center;"
                                        }
                                    },
                                    icon: 'warning'
                                });

                                return false;
                            }
                        }
                        //testing
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
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr(
                                'content')
                        }
                    });
                    jQuery.ajax({
                        type: 'POST',
                        url: "{{ url('change_waiting_status') }}",
                        data: {
                            _pt_id: pt_id,
                            _pst_id: pst_id,
                            _pl_id: pl_id,
                            _mode: mode,
                            _item_type: item_type,
                            _plst_id: plst_id,
                            _sell_price: bandrol
                            // _bandrol: bandrol
                        },
                        dataType: 'json',
                        success: function(r) {
                            jQuery.noConflict();
                            if (r.status == '200') {
                                toast('Ditambah', 'Item berhasil ditambah', 'success');
                                jQuery('#total_item_side').text(parseInt(total_item) +
                                    1);
                                console.log('TOTAL PRICE: ', total_price_side);
                                console.log('TOTAL NAMESET: ', total_nameset_side);
                                jQuery('#total_price_side').text(addCommas(parseFloat(
                                    replaceComma(total_price)) + parseFloat(
                                    sell_price)));
                                // jQuery('#total_final_price_side').text(addCommas(parseFloat(Number(replaceComma(total_price_side)) + Number(total_nameset_side)) + parseFloat(sell_price) - parseFloat(replaceComma(total_discount))));

                                // JQuery('#temp_total_side').val(addCommas(parseFloat(Number(replaceComma(total_price)) + Number(total_nameset)) + parseFloat(sell_price) - parseFloat(replaceComma(total_discount))))
                                // Hide autocomplete list after product is selected
                                jQuery('#itemList').addClass('hidden').removeClass('block').fadeOut();
                                jQuery('#product_name_input').val('');
                                
                                if (item_type == 'waiting') {
                                    // SKIP: offline_pos_v2 uses updateProductTable() to render table from orderItems array
                                    if (shouldSkipRowAddition()) {
                                        console.log('⏭️ Skipping row addition in offline_pos_v2_js (change_waiting_status waiting) - will use updateProductTable() instead');
                                        // Trigger updateProductTable() and updateOrderDisplay() after a short delay
                                        setTimeout(function() {
                                            if (typeof window.updateProductTable === 'function' && window.orderItems && window.orderItems.length > 0) {
                                                console.log('🔄 Calling updateProductTable() after change_waiting_status success');
                                                window.updateProductTable();
                                            }
                                            if (typeof window.updateOrderDisplay === 'function') {
                                                window.updateOrderDisplay();
                                            }
                                            if (typeof updateGrandTotal === 'function') {
                                                updateGrandTotal();
                                            }
                                        }, 100);
                                    } else {
                                        jQuery('#orderTable tr:last').after("" +
                                        "<tr data-list-item class='pos_item_list mb-2 bg-light-primary " +
                                        b1g1_mode + "' id='orderList" + (total_row +
                                            1) + "'>" +
                                        "<td style='white-space: nowrap; font-size:14px; " +
                                        highlight + "' id='item_name" + (total_row +
                                            1) + "'>" + p_name + "</td>" +
                                        "<td>" + (pls_qty) + "</td> " +

                                        "<td><input type='number' min='0' style='width: 10rem;' class='form-control border-dark col-5 basicInput2 qty-input" +
                                        pst_id + " item_qty' id='item_qty" + (
                                            total_row + 1) +
                                        "' value='1' onchange='return changeQty(" +
                                        (total_row + 1) + ", " + pst_id + ", " + (
                                            pls_qty) + ")'></td> " +
                                        "<td>" +
                                        "<select style='width: 10rem;' data-sellPrice='" +
                                        sell_price +
                                        "' class='form-control col-10 mr-4' id='discount_selection" +
                                        (total_row + 1) +
                                        "' onchange='handleSelectChange(" + (
                                            total_row + 1) + ",this)'>" +
                                        "<option value='0'>Discount Extra</option>" +
                                        "<option value='1'>Discount Promo</option>" +
                                        "</select>" +
                                        "</td>" +
                                        "<td><input type='number' style='width: 13rem;' class='form-control border-dark col-5 basicInput2 discount-percent" +
                                        pst_id +
                                        " discount_percentage' id='discount_percentage" +
                                        (total_row + 1) +
                                        "' value='0' onchange='return changeDiscountPercentage(" +
                                        (total_row + 1) + ", " + pst_id + ", " + (
                                            pls_qty) + ")'></td>" +
                                        " <td><input type='number' style='width: 13rem;' class='form-control border-dark col-8 basicInput2 discount-number" +
                                        pst_id +
                                        " discount_number' id='discount_number" + (
                                            total_row + 1) +
                                        "' value='' onchange='return changeDiscountNumber(" +
                                        (total_row + 1) + ", " + pst_id + ", " + (
                                            pls_qty) + ")'></td>" +
                                        "<td><input type='number' style='width: 13rem;' class='col-8 nameset_price namset-input' id='nameset_price" +
                                        (total_row + 1) +
                                        "' onchange='return namesetPrice(" + (
                                            total_row + 1) + ")'/></td> " +
                                        "<td><span class='price_tag_item' id='price_tag_item" +
                                        (total_row + 1) + "'>" + addCommas(
                                        bandrol) + "</span></td> " +
                                        "<td><span class='sell_price_item' id='sell_price_item" +
                                        (total_row + 1) + "'>" + addCommas(
                                            sell_price) + "</span></td> " +
                                        "<td><span class='discount_normal' style='width: 13rem;'  id='discount_normal" +
                                        (total_row + 1) + "'>" + addCommas(
                                            discount_normal) + "</span></td> " +
                                        "<td><span class='subtotal_item' id='subtotal_item" +
                                        (total_row + 1) + "'>" + addCommas(
                                            sell_price) + "</span></td> " +
                                        "<td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem" +
                                        (total_row + 1) +
                                        "' onclick='return saveItem(" + (total_row +
                                            1) + ", " + pst_id + ", " + sell_price +
                                        ", " + (plst_id || r.plst_id) + ", " +
                                        pl_id +
                                        ")'><i class='fa fa-eye' style='display:none;'></i></a> " +
                                        "<a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem(" +
                                        pst_id + ", " + sell_price + ", " + (
                                            total_row + 1) + ", " + pl_id + ", " + (
                                            plst_id || r.plst_id) + ", " + bandrol +
                                        ")'><i class='fas fa-trash-alt'></i></a></div></td></tr>"
                                    );
                                    }
                                } else {
                                    // SKIP: offline_pos_v2 uses updateProductTable() to render table from orderItems array
                                    if (shouldSkipRowAddition()) {
                                        console.log('⏭️ Skipping row addition in offline_pos_v2_js (change_waiting_status else) - will use updateProductTable() instead');
                                        // Trigger updateProductTable() and updateOrderDisplay() after a short delay
                                        setTimeout(function() {
                                            if (typeof window.updateProductTable === 'function' && window.orderItems && window.orderItems.length > 0) {
                                                console.log('🔄 Calling updateProductTable() after change_waiting_status success (else)');
                                                window.updateProductTable();
                                            }
                                            if (typeof window.updateOrderDisplay === 'function') {
                                                window.updateOrderDisplay();
                                            }
                                            if (typeof updateGrandTotal === 'function') {
                                                updateGrandTotal();
                                            }
                                        }, 100);
                                    } else {
                                        jQuery('#orderTable tr:last').after("" +
                                        "<tr data-list-item class='pos_item_list mb-2 bg-light-primary " +
                                        b1g1_mode + "' id='orderList" + (total_row +
                                            1) + "'>" +
                                        "<td style='white-space: nowrap; font-size:14px; " +
                                        highlight + "' id='item_name" + (total_row +
                                            1) + "'>" + p_name + "</td>" +
                                        "<td>" + (pls_qty) + "</td> " +

                                        "<td><input type='number' min='0' style='width: 10rem;' class='form-control border-dark col-5 basicInput2 qty-input" +
                                        pst_id + " item_qty' id='item_qty" + (
                                            total_row + 1) +
                                        "' value='1' onchange='return changeQty(" +
                                        (total_row + 1) + ", " + pst_id + ", " + (
                                            pls_qty) + ")'></td> " +
                                        "<td>" +
                                        "<select style='width: 10rem;' data-sellPrice='" +
                                        sell_price +
                                        "' class='form-control col-10 mr-4' id='discount_selection" +
                                        (total_row + 1) +
                                        "' onchange='handleSelectChange(" + (
                                            total_row + 1) + ",this)'>" +
                                        "<option value='0'>Discount Extra</option>" +
                                        "<option value='1'>Discount Promo</option>" +
                                        "</select>" +
                                        "</td>" +
                                        "<td><input type='number' style='width: 13rem;' class='form-control border-dark col-5 basicInput2 discount-percent" +
                                        pst_id +
                                        " discount_percentage' id='discount_percentage" +
                                        (total_row + 1) +
                                        "' value='0' onchange='return changeDiscountPercentage(" +
                                        (total_row + 1) + ", " + pst_id + ", " + (
                                            pls_qty) + ")'></td>" +
                                        " <td><input type='number' style='width: 13rem;' class='form-control border-dark col-8 basicInput2 discount-number" +
                                        pst_id +
                                        " discount_number' id='discount_number" + (
                                            total_row + 1) +
                                        "' value='0' onchange='return changeDiscountNumber(" +
                                        (total_row + 1) + ", " + pst_id + ", " + (
                                            pls_qty) + ")'></td>" +
                                        "<td><input type='number' style='width: 13rem;' class='col-8 nameset_price namset-input' id='nameset_price" +
                                        (total_row + 1) +
                                        "' onchange='return namesetPrice(" + (
                                            total_row + 1) + ")'/></td> " +
                                        // "<td><input type='text' style='width: 13rem;' class='col-8 nameset_price namset-input' value='" + sell_price +"' id='item_price" + (total_row + 1) + "'/></td> " +
                                        "<td><span class='price_tag_item' id='price_tag_item" +
                                        (total_row + 1) + "'>" + addCommas(
                                        bandrol) + "</span></td> " +
                                        "<td><span class='sell_price_item' id='sell_price_item" +
                                        (total_row + 1) + "'>" + addCommas(
                                            sell_price) + "</span></td> " +
                                        "<td><span class='discount_normal' style='width: 13rem;' id='discount_normal" +
                                        (total_row + 1) + "'>" + addCommas(
                                            discount_normal) + "</span></td> " +
                                        "<td><span class='subtotal_item' id='subtotal_item" +
                                        (total_row + 1) + "'>" + addCommas(
                                            sell_price) + "</span></td> " +
                                        "<td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem" +
                                        (total_row + 1) +
                                        "' onclick='return saveItem(" + (total_row +
                                            1) + ", " + pst_id + ", " + sell_price +
                                        ", " + (plst_id || r.plst_id) + ", " +
                                        pl_id +
                                        ")'><i class='fa fa-eye' style='display:none;'></i></a> " +
                                        "<a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem(" +
                                        pst_id + ", " + sell_price + ", " + (
                                            total_row + 1) + ", " + pl_id + ", " + (
                                            plst_id || r.plst_id) + ", " + bandrol +
                                        ")'><i class='fas fa-trash-alt'></i></a></div></td></tr>"
                                    );
                                }
                                var total_nameset_side = replaceComma(jQuery(
                                    '#total_nameset_side').text());
                                var total_price_side = replaceComma(jQuery(
                                    '#total_price_side').text());
                                var total_discount_side = replaceComma(jQuery(
                                    '#total_discount_value_side').text());

                                console.log(Number(total_price_side));
                                console.log(Number(total_nameset_side));
                                console.log(Number(total_discount_side));

                                jQuery('#total_final_price_side').text(
                                    addCommas(parseFloat(Number(total_price_side) +
                                        Number(total_nameset_side) - Number(
                                            total_discount_side)))
                                );
                                updateTotalDiskon();
                                updateTotalHarga();
                                updateGrandTotal();

                            } else if (r.status == '400') {
                                console.log(r.barang);
                                toast('Gagal', 'Item gagal ditambah', 'danger');
                            }
                        },
                        error: function(data) {
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
                    console.log(r.barang);
                    console.log(r.status);
                    toast('Tidak DItemukan', 'Barcode tidak ditemukan', 'warning');
                }

            },
            complete: function() {
                // Hide the loading indicator
                jQuery('#loader').hide();
                jQuery('#barcode_input').prop('disabled', false).focus();
            }

        });
        jQuery('#barcode_input').val('');
        jQuery('#barcode_input').focus();
}

    function handleSelectChange(row, selectElement) {
        const selectedValue = selectElement.value;
        const rowId = jQuery(selectElement).closest('tr').attr('id');
        console.log('Changed value:', selectedValue, 'on row:', rowId);

        // Original sell price from data attribute
        const sellPrice = parseFloat(selectElement.dataset.sellprice);

        // Quantity (if needed for future calc)
        const qty = parseFloat(jQuery('#item_qty1' + row).val()) || 0;

        // Bandrol price from DOM (with comma cleanup)
        let harga_bandrol = jQuery('#price_tag_item' + row).text();
        harga_bandrol = parseFloat(harga_bandrol.toString().replace(/,/g, ''));

        let harga_jual = 0;

        if (selectedValue == '1') {
            // Use harga_bandrol as sell price
            harga_jual = harga_bandrol;

            jQuery('#sell_price_item' + row).text(addCommas(harga_jual));
        } else {
            // Use original sell price
            harga_jual = sellPrice;

            jQuery('#sell_price_item' + row).text(addCommas(harga_jual));
        }

        // Calculate discount
        const discount_normal = harga_bandrol - harga_jual;

        // Set discount and subtotal
        jQuery('#discount_normal' + row).text(addCommas(discount_normal));
        jQuery('#subtotal_item' + row).text(addCommas(harga_jual));

        // Update total
        updateTotalHarga();
        updateTotalDiskon()
        updateGrandTotal()
    }


    // function updateTotalHarga(row) {
    //     let total = 0;
    //     var qty = parseFloat(jQuery('#item_qty' + row).val()) || 0;

    //     console.log('row : ', ro);


    //     jQuery('[id^="price_tag_item"]').each(function () {
    //         let val = jQuery(this).text().replace(/,/g, '');
    //         let num = parseFloat(val) || 0;
    //         total += num;
    //     });

    //     jQuery('#total_price_side').text(addCommas(total));
    // }

    // function updateTotalHarga() {
    //     let total = 0;
    //
    //     // Cek apakah ada B1G1
    //     let hasB1G1 = false;
    //
    //     jQuery('[id^="orderList"]').each(function () {
    //         if (jQuery(this).hasClass('b1g1_mode')) {
    //             hasB1G1 = true;
    //             return false; // break loop
    //         }
    //     });
    //
    //     // Pilih selector yang sesuai
    //     let selector = hasB1G1 ? '[id^="price_tag_item"]' : '[id^="sell_price_item"]';
    //
    //     jQuery(selector).each(function () {
    //         const rowId = jQuery(this).attr('id').replace(selector.includes('price_tag_item') ? 'price_tag_item' : 'sell_price_item', '');
    //
    //         let price = jQuery(this).text().replace(/,/g, '');
    //         price = parseFloat(price) || 0;
    //
    //         let qty = parseFloat(jQuery('#item_qty' + rowId).val()) || 0;
    //
    //         total += price * qty;
    //
    //         console.log(`Row ${rowId} => ${price} x ${qty} = ${price * qty}`);
    //     });
    //
    //     jQuery('#total_price_side').text(addCommas(total));
    // }

    function updateTotalHarga() {
        let total = 0;

        // get original sell price
        // let OriginalPrice = parseFloat(jQuery('#discount_selection' + row).data('sellPrice')) || 0;
        // console.log('Sell price for row ' + row + ':', OriginalPrice);

        // Loop through each price row
        jQuery('[id^="price_tag_item"]').each(function() {
            const rowId = jQuery(this).attr('id').replace('price_tag_item', '');

            // Get price
            let price = jQuery(this).text().replace(/,/g, '');
            price = parseFloat(price) || 0;

            // Get corresponding qty
            let qty = parseFloat(jQuery('#item_qty' + rowId).val()) || 0;

            console.log('Row:', rowId, '| Price:', price, '| Qty:', qty);

            // Add to total
            total += price * qty;

            console.log('asdasd : ', total);

        });

        jQuery('#total_price_side').text(addCommas(total));
    }

    function updateTotalDiskon() {
        let total_disc_normal = 0;
        let total_disc_field = 0;

        // get original sell price 
        // let OriginalPrice = parseFloat(jQuery('#discount_selection' + row).data('sellPrice')) || 0;
        // console.log('Sell price for row ' + row + ':', OriginalPrice);

        jQuery('[id^="discount_normal"]').each(function() {
            const rowId = jQuery(this).attr('id').replace('discount_normal', '');

            let val = jQuery(this).text().replace(/,/g, '');
            let num = parseFloat(val) || 0;

            // Get qty for this row
            let qty = parseFloat(jQuery('#item_qty' + rowId).val()) || 0;

            total_disc_normal += num * qty;
        });


        jQuery('[id^="discount_number"]').each(function() {
            let val = jQuery(this).val().replace(/,/g, '');
            let num = parseFloat(val) || 0;
            total_disc_field += num;
        });

        console.log('Ini diskon diskonan : ', total_disc_normal, total_disc_field);

        jQuery('#total_discount_value_side').text(addCommas(total_disc_field + total_disc_normal));
    }

    function updateGrandTotal() {
        let grand_total = 0;

        // Get values and convert to numbers
        let total = parseFloat(jQuery('#total_price_side').text().replace(/,/g, '')) || 0;
        let total_nameset = parseFloat(jQuery('#total_nameset_side').text().replace(/,/g, '')) || 0;
        let total_voucher = parseFloat(jQuery('#voucher_total_value_side').text().replace(/,/g, '')) || 0;
        let total_discount = parseFloat(jQuery('#total_discount_value_side').text().replace(/,/g, '')) || 0;
        // let qty = jQuery('#item_qty').val();

        // Grand total calculation
        grand_total = total + total_nameset - total_voucher - total_discount;

        // Set to DOM with comma formatting
        jQuery('#total_final_price_side').text(addCommas(grand_total));
    }

    // Initialize Product DataTable for Offline POS V2 (using new JSON endpoint)
    var product = null;
    
    function initializeProductDataTable() {
        if (jQuery('#Ptb').length === 0) {
            console.warn('Table #Ptb not found, will retry when modal opens');
            return;
        }
        
        // Destroy existing instance if any
        if (product) {
            try {
                product.destroy();
            } catch(e) {
                console.warn('Error destroying existing DataTable:', e);
            }
        }
        
        product = jQuery('#Ptb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            language: {
                processing: '<div class="w-full flex items-center justify-center p-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-500"></div><span class="ml-2 text-gray-700">Loading</span></div>',
                emptyTable: 'Tidak ada data',
                zeroRecords: 'Tidak ada data yang sesuai',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                infoFiltered: '(disaring dari _MAX_ total data)',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                }
            },
            dom: '<"flex flex-wrap items-center justify-between mb-4"<"flex items-center"l>>rt<"flex flex-wrap items-center justify-between mt-4"<"flex items-center"i><"flex items-center"p>>',
            // Hide default DataTable search (we use custom search input)
            searching: false,
            ajax: {
                url: "{{ url('scan_adjustment_product_datatables_v2') }}",
                type: 'GET',
                beforeSend: function() {
                    // Show loading indicator
                    jQuery('#p_search_loading').removeClass('hidden');
                },
                data: function(d) {
                    d.search = jQuery('#p_search').val() || '';
                    console.log('DataTable AJAX request with search:', d.search);
                },
                complete: function() {
                    // Hide loading indicator when request completes
                    jQuery('#p_search_loading').addClass('hidden');
                },
                dataSrc: function(json) {
                    console.log('DataTable AJAX response:', json);
                    if (json && json.data) {
                        return json.data;
                    }
                    return [];
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTable AJAX error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        error: error,
                        thrown: thrown,
                        responseText: xhr.responseText
                    });
                    if (typeof showToast === 'function') {
                        showToast('Gagal memuat data produk: ' + error, 'error');
                    }
                }
            },
            columns: [
                {
                data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    title: 'No',
                    searchable: false,
                    orderable: false,
                    className: 'text-center w-16'
            },
            {
                data: 'br_name',
                    name: 'br_name',
                    title: 'Brand',
                    className: 'font-medium'
            },
            {
                data: 'p_name',
                    name: 'p_name',
                    title: 'Artikel',
                    className: 'font-medium'
            },
            {
                data: 'p_color',
                    name: 'p_color',
                    title: 'Warna'
            },
            {
                data: 'sz_name',
                    name: 'sz_name',
                    title: 'Size'
            },
            {
                data: 'ps_barcode_show',
                    name: 'ps_barcode',
                    title: 'Barcode',
                    className: 'font-mono text-sm'
                }
            ],
            columnDefs: [
                {
                    targets: 0,
                    className: 'px-6 py-4 text-center text-gray-900 whitespace-nowrap'
                },
                {
                    targets: [1, 2, 3, 4],
                    className: 'px-6 py-4 text-gray-900 whitespace-nowrap'
                },
                {
                    targets: 5,
                    className: 'px-6 py-4'
                }
            ],
            order: [[1, 'asc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            drawCallback: function(settings) {
                // Add Flowbite table styling after draw
                jQuery('#Ptb tbody tr').removeClass('odd even').addClass('bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
                jQuery('#Ptb tbody td').addClass('px-6 py-4 text-gray-900 dark:text-white');
                // Style barcode input fields
                jQuery('#Ptb .input_barcode_field').addClass('w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white');
            }
        });
        
        // Make product DataTable accessible globally
        window.product = product;
        
        // Attach search handler after DataTable is initialized
        let searchTimeout = null;
        jQuery('#p_search').off('keyup input').on('keyup input', function() {
            clearTimeout(searchTimeout);
            const searchValue = jQuery(this).val();
            const $loading = jQuery('#p_search_loading');
            $loading.removeClass('hidden');
            
            searchTimeout = setTimeout(function() {
                if (window.product && typeof window.product.draw === 'function') {
                    console.log('Search triggered, calling draw:', searchValue);
                    window.product.one('draw', function() {
                        $loading.addClass('hidden');
                    });
                    window.product.draw(false);
                } else if (product && typeof product.draw === 'function') {
                    console.log('Search triggered, calling draw:', searchValue);
                    product.one('draw', function() {
                        $loading.addClass('hidden');
                    });
                    product.draw(false);
                } else {
                    console.warn('Product DataTable not available for search');
                    $loading.addClass('hidden');
                }
            }, 500);
        });
        
        console.log('Product DataTable initialized');
    }
    
    // Make initializeProductDataTable accessible globally
    window.initializeProductDataTable = initializeProductDataTable;
    
    // Don't initialize on page load - table is hidden in modal
    // Will be initialized when modal opens

    // Handle search input with debounce and loading indicator
    let searchTimeout = null;
    jQuery(document).on('keyup input', '#p_search', function() {
        clearTimeout(searchTimeout);
        const searchValue = jQuery(this).val();
        const $loading = jQuery('#p_search_loading');
        
        // Show loading indicator
        $loading.removeClass('hidden');
        
        searchTimeout = setTimeout(function() {
            console.log('Search triggered for:', searchValue);
            
            // Try to get DataTable instance
            let productTable = null;
            if (window.product && typeof window.product.draw === 'function') {
                productTable = window.product;
            } else if (typeof product !== 'undefined' && product && typeof product.draw === 'function') {
                productTable = product;
            } else {
                // Try to get from jQuery DataTable API
                const $ptb = jQuery('#Ptb');
                if ($ptb.length && $ptb.DataTable) {
                    try {
                        productTable = $ptb.DataTable();
                    } catch(e) {
                        console.warn('Could not get DataTable instance:', e);
                    }
                }
            }
            
            if (productTable && typeof productTable.draw === 'function') {
                console.log('Calling productTable.draw(false) for search:', searchValue);
                // Hide loading when draw completes
                productTable.one('draw', function() {
                    $loading.addClass('hidden');
                });
                // Trigger redraw with new search value
                productTable.draw(false);
            } else {
                console.warn('Product DataTable not available for search');
                $loading.addClass('hidden');
            }
        }, 500); // Debounce 500ms
    });
    
    // Handle barcode input change (delegate for dynamically loaded inputs)
    jQuery(document).on('change', '.input_barcode_field', function(e) {
        const $input = jQuery(this);
        const id = $input.data('id');
        const barcode = $input.val();
        
        if (!id) {
            console.warn('No ID found for barcode input');
            return;
        }
        
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        
        jQuery.ajax({
            type: 'POST',
            url: "{{ url('scan_adjustment_barcode_update') }}",
            data: {
                id: id,
                barcode: barcode
            },
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    if (typeof showToast === 'function') {
                        showToast('Barcode berhasil diperbarui', 'success');
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast('Gagal memperbarui barcode', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating barcode:', error);
                if (typeof showToast === 'function') {
                    showToast('Terjadi kesalahan saat memperbarui barcode', 'error');
                }
            }
        });
    });

    // Fallback native handlers: in some environments jQuery delegation can be blocked
    // (e.g. modal layering or event stopPropagation). Add resilient vanilla listeners
    // so clicking + / - always works.
    (function() {
        document.addEventListener('click', function(ev) {
            var t = ev.target;

            // add voucher
            var addVoc = t.closest && t.closest('.add-voucher');
            if (addVoc) {
                ev.preventDefault();
                console.log('[fallback] .add-voucher clicked');
                var newField = '<div class="flex gap-2 mb-3">' +
                    '<input type="text" name="voucher-list[]" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5" placeholder="Kode Voucher" value="">' +
                    '<button type="button" class="add-voucher px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">+</button>' +
                    '<button type="button" class="remove-voucher px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">-</button>' +
                    '</div>';
                var container = document.querySelector('#voucher-container');
                if (container) container.insertAdjacentHTML('beforeend', newField);
                return;
            }

            // remove voucher
            var remVoc = t.closest && t.closest('.remove-voucher');
            if (remVoc) {
                ev.preventDefault();
                var group = remVoc.closest('.input-group');
                if (group) group.parentNode.removeChild(group);
                return;
            }

            // add total discount
            var addDisc = t.closest && t.closest('.add-total-discount');
            if (addDisc) {
                ev.preventDefault();
                console.log('[fallback] .add-total-discount clicked');
                var newField = '<div class="flex gap-2 mb-3">' +
                    '<input type="text" name="total-discount-list[]" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5" placeholder="Diskon" value="">' +
                    '<button type="button" class="add-total-discount px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">+</button>' +
                    '<button type="button" class="remove-total-discount px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">-</button>' +
                    '</div>';
                var container2 = document.querySelector('#total-discount-container');
                if (container2) container2.insertAdjacentHTML('beforeend', newField);
                return;
            }

            // remove total discount
            var remDisc = t.closest && t.closest('.remove-total-discount');
            if (remDisc) {
                ev.preventDefault();
                var group = remDisc.closest('.input-group');
                if (group) {
                    // replicate existing logic for updating totals when removing
                    try {
                        let total_price = document.querySelector("#total_final_price_side").textContent || '0';
                        let total_discount_value_side = document.querySelector("#total_discount_value_side").textContent || '0';
                        let discount = group.querySelector('input') ? group.querySelector('input').value : 0;
                        let discountTypeEl = document.querySelector('select[name="discount-type-list"]');
                        let discountType = discountTypeEl ? discountTypeEl.value : 'nominal';
                        total_price = (total_price || '').replace(/,/g, '');
                        total_discount_value_side = (total_discount_value_side || '').replace(/,/g, '');
                        if (discountType === 'nominal') {
                            let new_total_price = parseFloat(total_price || 0) + parseFloat(discount || 0);
                            let new_total_discount_value_side = parseFloat(total_discount_value_side || 0) - parseFloat(discount || 0);
                            document.querySelector('#total_final_price_side').textContent = (isNaN(new_total_price) ? 0 : new_total_price).toLocaleString();
                            document.querySelector('#total_discount_value_side').textContent = (isNaN(new_total_discount_value_side) ? 0 : new_total_discount_value_side).toLocaleString();
                        } else {
                            let discountAmount = (parseFloat(discount || 0) / 100) * parseFloat(total_price || 0);
                            let new_total_price = parseFloat(total_price || 0) + discountAmount;
                            let new_total_discount_value_side = parseFloat(total_discount_value_side || 0) - discountAmount;
                            document.querySelector('#total_final_price_side').textContent = (isNaN(new_total_price) ? 0 : new_total_price).toLocaleString();
                            document.querySelector('#total_discount_value_side').textContent = (isNaN(new_total_discount_value_side) ? 0 : new_total_discount_value_side).toLocaleString();
                        }
                    } catch (err) {
                        console.warn('Fallback remove-total-discount error', err);
                    }
                    group.parentNode.removeChild(group);
                }
                return;
            }
        });
    })();

    // DISABLED: Handler moved to offline_pos_v2.blade.php to use Flowbite modal
    // jQuery(document).delegate('#product_barcode_btn', 'click', function(e) {
    //     e.preventDefault();
    //     jQuery.noConflict();
    //     jQuery('#ProductBarcodeModal').modal('show');
    //     product.draw(false);
    // });

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
            data: {
                id: id,
                barcode: barcode
            },
            dataType: 'json',
            url: "{{ url('scan_adjustment_barcode_update') }}",
            success: function(r) {
                if (r.status == '200') {
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
                url: "{{ url('check_rating_for_checkout') }}",
                method: "POST",
                dataType: "JSON",
                success: function(r) {
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
            type: 'POST',
            url: "{{ url('delete_rating') }}",
            dataType: 'json',
            success: function(r) {
                jQuery('#waiting_customer_label').addClass('d-none');
                jQuery('#free_sock_customer_panel').addClass('d-none');
                jQuery('#free_sock_customer_label').val('');
                jQuery('#free_sock_customer_id').val('');
                jQuery('#free_sock_customer_mode').val('');
                jQuery('#free_sock_customer_ur_id').val('');
            },
            error: function(data) {
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
            url: "{{ url('get_free_sock') }}",
            method: "POST",
            data: {
                _access_code: access_code
            },
            dataType: "json",
            success: function(r) {
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
        console.log('📋 offline_pos_v2 jQuery(document).ready started - attaching all event handlers');
        console.log('💳💳💳 Payment modal handlers will be registered...');
        try {
            console.log('💳💳💳 EARLY: Registering payment handlers immediately...');
            
            // Define handler function first
            window.handlePaymentOptionChange = function() {
                var payment_option = jQuery('#payment_option').val();
                console.log('💳💳💳 Payment option changed to: ' + payment_option);
                console.log('💳 Element found:', jQuery('#payment_option').length > 0);

                // Clear payment method selections
                jQuery('#pm_id_offline').val('');
                jQuery('#pm_id_offline_two').val('');
                jQuery('#cp_id').val('');
                jQuery('#cp_id_two').val('');

                // Clear input fields
                jQuery('#total_payment').val('');
                jQuery('#total_payment_two').val('');
                jQuery('#return_payment').text('');
                jQuery('#return_payment_label').addClass('hidden');
                
                // Hide all payment method specific fields
                jQuery('#card_provider_content').addClass('hidden');
                jQuery('#card_number_label').addClass('hidden');
                jQuery('#ref_number_label').addClass('hidden');
                jQuery('#charge_label').addClass('hidden');
                jQuery('#sub_payment_offline_content').addClass('hidden');
                jQuery('#card_provider_content_two').addClass('hidden');
                jQuery('#card_number_label_two').addClass('hidden');
                jQuery('#ref_number_label_two').addClass('hidden');
                
                // Set payment total
                var payment_total = jQuery('#total_final_price_side').text();
                var cleanTotal = String(payment_total).replace('Rp. ', '').trim();
                var total = parseFloat(replaceComma(cleanTotal));
                var formattedTotal = addCommas(total.toString());
                jQuery('#payment_total').text('Rp. ' + formattedTotal);
                
                if (payment_option == 'two') {
                    console.log('💳💳💳 Showing payment method 2 section');
                    var section2 = jQuery('#payment_method_two_section');
                    section2.removeClass('hidden');
                    jQuery('#payment_type_content_two').removeClass('hidden');
                    jQuery('#total_payment_two_label').removeClass('hidden');
                    jQuery('#total_payment_two').removeClass('hidden');
                    jQuery('#return_payment_label').addClass('hidden');
                } else {
                    console.log('💳💳💳 Hiding payment method 2');
                    jQuery('#payment_method_two_section').addClass('hidden');
                    jQuery('#payment_type_content_two').addClass('hidden');
                    jQuery('#total_payment_two_label').addClass('hidden');
                    jQuery('#total_payment_two').addClass('hidden');
                    jQuery('#return_payment_label').removeClass('hidden');
                }
            };
            
            // Register handler with event delegation (works even if element doesn't exist)
            jQuery(document).off('change', '#payment_option');
            jQuery(document).on('change', '#payment_option', function(e) {
                console.log('💳💳💳💳💳 Payment option CHANGE EVENT TRIGGERED!');
                if (window.handlePaymentOptionChange) {
                    window.handlePaymentOptionChange();
                }
            });
            
            console.log('✅ Payment option handler registered EARLY with event delegation');
        } catch (error) {
            console.error('❌ Error registering payment handlers:', error);
        }
        
        jQuery('#_pt_id_complaint').val('');
        jQuery('#_exchange').val('');
        reloadWaitingForCheckout();
        reloadComplaint();
        // reloadRefund();
        dpInvoiceRefund();
        
        // Initialize payment form field visibility (Tailwind classes)
        jQuery('#card_provider_content').addClass('hidden');
        jQuery('#card_number_label').addClass('hidden');
        jQuery('#ref_number_label').addClass('hidden');
        jQuery('#charge_label').addClass('hidden');
        jQuery('#card_provider_content_two').addClass('hidden');
        jQuery('#card_number_label_two').addClass('hidden');
        jQuery('#ref_number_label_two').addClass('hidden');
        jQuery('#total_payment_two_label').addClass('hidden');
        jQuery('#payment_type_content_two').addClass('hidden');
        jQuery('#total_payment_two').addClass('hidden');
        jQuery('#sub_payment_offline_content').addClass('hidden');
        jQuery('#return_payment_label').addClass('hidden');

        jQuery('#cust_id_label').on('change', function() {
            var query = jQuery(this).val();
            if (query == '') {
                jQuery('#cust_id').val('1');
                jQuery('#check_customer').attr('data-id', '');
                jQuery('#itemListCust').html('');
                jQuery('#itemListCust').fadeOut();
            }
        });

        jQuery('#cust_id_label').on('focus', function() {
            jQuery(this).val('');
            jQuery('#cust_id').val('1');
        });

        jQuery('#cust_id_label').on('keyup', function() {
            var query = jQuery(this).val();
            var type = 'cust';
            if (jQuery.trim(query) != '' || jQuery.trim(query) != null) {
                if (jQuery.trim(query).length > 3) {
                    jQuery.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        url: "{{ url('autocomplete_customer') }}",
                        method: "POST",
                        data: {
                            query: query,
                            type: type
                        },
                        success: function(data) {
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

        jQuery(document).delegate('#add_new_customer', 'click', function() {
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
                type: 'POST',
                url: "{{ url('check_customer') }}",
                data: {
                    _cust_id: cust_id
                },
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
                        jQuery('#cust_token_active').val(r.cust_token_active);
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
                error: function(data) {
                    swal('Error', data, 'error');
                }
            });
        });

        var detail_shift = jQuery('#current-shift-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('current-shift.data') }}',
                type: 'GET',
            },
            columns: [{
                    data: 'pm_name',
                    name: 'pm_name'
                },
                {
                    data: 'total_pos_real_price',
                    name: 'total_pos_real_price',
                    render: function(data, type, row) {
                        return formatRupiahDetail(data);
                    }
                }
            ],
            order: [
                [0, 'asc'],
                [1, 'asc']
            ],
            paging: false,
            searching: false,
            lengthChange: false,
            "info": false
        });

        function formatRupiahDetail(number) {
            return number.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });
        }

        var refund_retur_table = jQuery('#RefundReturtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('refund_retur_datatables') }}",
                data: function(d) {
                    d.pt_id = jQuery('#refund_retur_pt_id').val();

                    // console.log("test");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'ptd_id',
                    searchable: false
                },
                {
                    data: 'article',
                    name: 'article',
                    orderable: false
                },
                {
                    data: 'datetime',
                    name: 'datetime',
                    orderable: false
                },
                {
                    data: 'qty',
                    name: 'qty',
                    orderable: false
                },
                {
                    data: 'price',
                    name: 'price',
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false
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

        var dp_invoice_table = jQuery('#DpInvoicetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('dp_invoice_datatables') }}",
                data: function(d) {
                    d.pt_id = jQuery('#dp_pt_id').val();

                    // console.log("test");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'ptd_id',
                    searchable: false
                },
                {
                    data: 'article',
                    name: 'article',
                    orderable: false
                },
                {
                    data: 'datetime',
                    name: 'datetime',
                    orderable: false
                },
                {
                    data: 'qty',
                    name: 'qty',
                    orderable: false
                },
                {
                    data: 'price',
                    name: 'price',
                    orderable: false
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

        jQuery('#choosecustomer').on('hide.bs.modal', function() {
            jQuery('#f_customer')[0].reset();
        });

        jQuery('#add_customer_btn').on('click', function() {
            jQuery('#_mode').val('add');
            jQuery('#choosecustomer').modal('show');
        });

        let is_shift = 0;

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
                success: function(response) {
                    if (response.status === '200') {
                        // User has started a shift
                        jQuery('#startShiftButton').hide();
                        jQuery('#stopShiftButton').show();
                        jQuery('#shiftStatus').html('Shift In Progress');
                        jQuery('#shiftEmployeeModal').modal('show');
                        jQuery('#shift_on').modal('show');
                        jQuery('#shift_off').modal('hide');


                        // console.log(response.shiftStatus)
                        is_shift = response.shiftStatus;
                    } else {
                        // User has not started a shift, show the modal with the start button
                        jQuery('#startShiftButton').show();
                        jQuery('#stopShiftButton').hide();
                        jQuery('#shiftEmployeeModal').modal('show');
                        jQuery('#shift_on').modal('hide');
                        jQuery('#shift_off').modal('show');
                        is_shift = response.shiftStatus;
                    }
                },
                error: function(error) {
                    console.error('Error checking user shift:', error);
                }
            });
        });

        // Payment option change handler function
        function handlePaymentOptionChange() {
            var payment_option = jQuery('#payment_option').val();
            console.log('💳💳💳 Payment option changed to: ' + payment_option);
            console.log('💳 Element found:', jQuery('#payment_option').length > 0);

            // Clear payment method selections (without triggering change yet)
            jQuery('#pm_id_offline').val('');
            jQuery('#pm_id_offline_two').val('');
            jQuery('#cp_id').val('');
            jQuery('#cp_id_two').val('');

            // Clear input fields
            jQuery('#total_payment').val('');
            jQuery('#total_payment_two').val('');
            jQuery('#return_payment').text('');
            jQuery('#return_payment_label').addClass('hidden');
            
            // Hide all payment method specific fields for method 1
            jQuery('#card_provider_content').addClass('hidden');
            jQuery('#card_number_label').addClass('hidden');
            jQuery('#ref_number_label').addClass('hidden');
            jQuery('#charge_label').addClass('hidden');
            jQuery('#sub_payment_offline_content').addClass('hidden');
            
            // Hide all payment method specific fields for method 2
            jQuery('#card_provider_content_two').addClass('hidden');
            jQuery('#card_number_label_two').addClass('hidden');
            jQuery('#ref_number_label_two').addClass('hidden');
            
            // Set payment total when option changes
            var payment_total = jQuery('#total_final_price_side').text();
            var cleanTotal = String(payment_total).replace('Rp. ', '').trim();
            var total = parseFloat(replaceComma(cleanTotal));
            var formattedTotal = addCommas(total.toString());
            jQuery('#payment_total').text('Rp. ' + formattedTotal);
            console.log('💳 Payment total set to: Rp. ' + formattedTotal);
            
            if (payment_option == 'two') {
                // Show payment method 2 section
                console.log('💳💳💳 Showing payment method 2 section');
                var section2 = jQuery('#payment_method_two_section');
                var paymentType2 = jQuery('#payment_type_content_two');
                var totalPayment2Label = jQuery('#total_payment_two_label');
                var totalPayment2 = jQuery('#total_payment_two');
                
                console.log('💳 Section 2 element exists:', section2.length > 0);
                console.log('💳 Section 2 current classes:', section2.attr('class'));
                
                section2.removeClass('hidden');
                paymentType2.removeClass('hidden');
                totalPayment2Label.removeClass('hidden');
                totalPayment2.removeClass('hidden');
                jQuery('#return_payment_label').addClass('hidden');
                
                // Verify elements are visible after a short delay
                setTimeout(function() {
                    console.log('💳 Section 2 visible check after timeout:', !section2.hasClass('hidden'));
                    console.log('💳 Section 2 display style:', section2.css('display'));
                    if (section2.hasClass('hidden')) {
                        console.error('❌ Section 2 still has hidden class!');
                        // Force show using inline style as fallback
                        section2.css('display', 'block').removeClass('hidden');
                    }
                }, 100);
            } else if (payment_option == 'one' || payment_option == '') {
                // Hide payment method 2, show return payment label for 1 Metode
                console.log('💳💳💳 Hiding payment method 2, showing return payment');
                jQuery('#payment_method_two_section').addClass('hidden');
                jQuery('#payment_type_content_two').addClass('hidden');
                jQuery('#total_payment_two_label').addClass('hidden');
                jQuery('#total_payment_two').addClass('hidden');
                jQuery('#return_payment_label').removeClass('hidden');
            }
        }

        // Register payment option change handler - using event delegation only to avoid conflicts
        console.log('💳💳💳 Registering payment_option change handlers...');
        console.log('💳 handlePaymentOptionChange function exists:', typeof handlePaymentOptionChange === 'function');
        
        // Use event delegation as primary method (works even if element doesn't exist yet)
        // Remove any existing handlers first to avoid duplicates
        try {
            jQuery(document).off('change', '#payment_option');
            jQuery(document).on('change', '#payment_option', function(e) {
                console.log('💳💳💳💳💳 Payment option change event triggered via delegation!');
                console.log('💳 Event object:', e);
                console.log('💳 Current value:', jQuery(this).val());
                console.log('💳 This element:', this);
                if (typeof handlePaymentOptionChange === 'function') {
                    handlePaymentOptionChange();
                } else {
                    console.error('❌ handlePaymentOptionChange is not a function!');
                }
            });
            console.log('✅ Payment option handler registered with event delegation');
        } catch (error) {
            console.error('❌ Error registering payment_option handler:', error);
        }
        
        // Also try direct binding if element exists
        try {
            if (jQuery('#payment_option').length > 0) {
                console.log('💳 Payment option element exists, adding direct binding');
                jQuery('#payment_option').off('change.payment-direct').on('change.payment-direct', function() {
                    console.log('💳💳💳 Payment option change (direct) triggered!');
                    if (typeof handlePaymentOptionChange === 'function') {
                        handlePaymentOptionChange();
                    }
                });
                console.log('✅ Direct binding added');
            } else {
                console.log('💳 Payment option element does not exist yet (will bind when modal opens)');
            }
        } catch (error) {
            console.error('❌ Error in direct binding:', error);
        }

        // jQuery('#reload_refund_list').on('click', function () {
        //     reloadRefund();
        // });

        jQuery('#reload_dp_list').on('click', function() {
            dpInvoiceRefund();
        });

        function addRefundExchangeList(type, plst_id, pt_id) {
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type: "POST",
                data: {
                    _type: type,
                    _plst_id: plst_id,
                    _pt_id: pt_id
                },
                dataType: 'json',
                url: "{{ url('refund_exchange_list') }}",
                success: function(r) {
                    if (r.status == '200') {
                        toast('Added', 'Added to refund list', 'success');
                    } else {
                        toast('Gagal', 'Gagal refund data', 'warning');
                    }
                }
            });
            return false;
        }

        // jQuery(document).delegate('#select_refund_exchange_item', 'click', function() {
        //     var p_name = jQuery(this).attr('data-p_name');
        //     var pst_id = jQuery(this).attr('data-pst_id');
        //     var pt_id = jQuery(this).attr('data-pt_id');
        //     var pl_id = jQuery(this).attr('data-pl_id');
        //     var plst_id = jQuery(this).attr('data-plst_id');
        //     var sell_price = jQuery(this).attr('data-sell_price');
        //     var total_price_item = jQuery(this).attr('data-total_price');
        //     var nameset_price = jQuery(this).attr('data-nameset_price');
        //     var item_qty = jQuery(this).attr('data-item_qty');
        //     var total_row = parseFloat(jQuery('#total_row').val());
        //     jQuery('#total_row').val(total_row + 1);
        //     var total_item = jQuery('#total_item_side').text();
        //     var total_price = jQuery('#total_price_side').text();
        //     if (jQuery(this).is(':checked')) {
        //         jQuery('#_pt_id_complaint').val(pt_id);
        //         jQuery('#total_item_side').text(parseInt(total_item) - 1);
        //         jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(total_price)) -
        //             parseFloat(total_price_item)));
        //         jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(total_price)) -
        //             parseFloat(total_price_item)));
        //         addRefundExchangeList('add', plst_id, pt_id);
        //         jQuery('#orderTable tr:last').after("" +
        //             "<tr data-list-item class='bg-danger text-white pos_item_list mb-2' id='data_plst" +
        //             plst_id + "'> " +
        //             "<td style='white-space: nowrap;'>" + p_name + "</td> <td>" + item_qty +
        //             "</td> " +
        //             "<td><input type='number' class='form-control border-dark col-4 basicInput2" +
        //             pst_id + "' id='item_qty" + (total_row + 1) + "' value='-" + item_qty +
        //             "' onchange='return changeReturQty(" + (total_row + 1) + ", " + pst_id + ", " +
        //             item_qty + ")'></td> " +
        //             " <td><input type='number' class='form-control border-dark col-5 basicInput2" +
        //             pst_id + " discount_percentage' id='discount_percentage" + (total_row + 1) +
        //             "' value='0' onchange='return changeDiscountPercentage(" + (total_row + 1) +
        //             ", " + pst_id + ", " + (pls_qty) + ")'></td>" +
        //             " <td><input type='text' class='form-control border-dark col-8 basicInput2" +
        //             pst_id + " discount_number' id='discount_number" + (total_row + 1) +
        //             "' value='0' onchange='return changeDiscountNumber(" + (total_row + 1) + ", " +
        //             pst_id + ", " + (pls_qty) + ")'></td>" +
        //             "<td><input type='number' class='col-8 nameset_price' id='nameset_price" + (
        //                 total_row + 1) + "' value='" + nameset_price +
        //             "' onchange='return namesetPrice(" + (total_row + 1) + ")' readonly/></td> " +
        //             "<td><span id='sell_price_item" + (total_row + 1) + "'>-" + addCommas(
        //                 sell_price) + "</span></td> " +
        //             "<td><span class='subtotal_item' id='subtotal_item" + (total_row + 1) + "'>-" +
        //             addCommas(total_price_item) + "</span></td> " +
        //             "<td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem" +
        //             (total_row + 1) + "' onclick='return saveItem(" + (total_row + 1) + ", " +
        //             pst_id + ", " + sell_price + ", " + plst_id + ", " + pl_id + ")'>" +
        //             "<i class='fa fa-eye' style='display:none;'></i></a></div></td></tr>");
        //     } else {
        //         jQuery('#total_item_side').text(parseInt(total_item) + 1);
        //         jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(total_price)) +
        //             parseFloat(total_price_item)));
        //         jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(total_price)) +
        //             parseFloat(total_price_item)));
        //         //alert(plst_id);
        //         addRefundExchangeList('remove', plst_id, pt_id);
        //         jQuery('#orderTable tr#data_plst' + plst_id + '').remove();
        //     }
        // });

        jQuery(document).delegate('#select_refund_exchange_item', 'click', function() {
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
            jQuery('#total_row').val(total_row + 1);
            var total_item = jQuery('#total_item_side').text();
            var total_price = jQuery('#total_price_side').text();
            if (jQuery(this).is(':checked')) {
                jQuery('#_pt_id_complaint').val(pt_id);
                jQuery('#total_item_side').text(parseInt(total_item) - 1);
                jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(total_price)) -
                    parseFloat(total_price_item)));
                jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(
                        total_final_price)) -
                    parseFloat(total_price_item)));
                addRefundExchangeList('add', plst_id, pt_id);
                // SKIP: offline_pos_v2 uses updateProductTable() to render table from orderItems array
                if (!shouldSkipRowAddition()) {
                    jQuery('#orderTable tr:last').after(
                    "<tr data-list-item class='bg-danger text-white pos_item_list mb-2' id='data_plst" +
                    plst_id + "'> <td style='white-space: nowrap;'>" + p_name + "</td> <td>" +
                    item_qty +
                    "</td> <td><input type='number' class='form-control border-dark col-4 basicInput2" +
                    pst_id + "' id='item_qty" + (total_row + 1) + "' value='-" + item_qty +
                    "' onchange='return changeReturQty(" + (total_row + 1) + ", " + pst_id + ", " +
                    item_qty +
                    ")'></td> <td>asdjalsd</td> <td><input type='number' class='col-8 nameset_price' id='nameset_price" +
                    (total_row + 1) + "' value='" + nameset_price +
                    "' onchange='return namesetPrice(" + (total_row + 1) +
                    ")' readonly/></td> <td><span id='sell_price_item" + (total_row + 1) + "'>-" +
                    addCommas(sell_price) +
                    "</span></td> <td><span class='subtotal_item' id='subtotal_item" + (total_row +
                        1) + "'>-" + addCommas(total_price_item) +
                    "</span></td> <td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem" +
                    (total_row + 1) + "' onclick='return saveItem(" + (total_row + 1) + ", " +
                    pst_id + ", " + sell_price + ", " + plst_id + ", " + pl_id +
                    ")'><i class='fa fa-eye' style='display:none;'></i></a></div></td></tr>");
                }
            } else {
                jQuery('#total_item_side').text(parseInt(total_item) + 1);
                jQuery('#total_price_side').text(addCommas(parseFloat(replaceComma(total_price)) +
                    parseFloat(total_price_item)));
                jQuery('#total_final_price_side').text(addCommas(parseFloat(replaceComma(
                        total_final_price)) +
                    parseFloat(total_price_item)));
                //alert(plst_id);
                addRefundExchangeList('remove', plst_id, pt_id);
                jQuery('#orderTable tr#data_plst' + plst_id + '').remove();
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

        jQuery(document).delegate('#dp_invoice', 'change', function(e) {
            e.preventDefault();
            var invoice = jQuery('option:selected', this).text();
            var pt_id = jQuery(this).val();
            //alert(invoice+' '+pt_id);
            jQuery('#dp_invoice_label').text(invoice);
            jQuery('#dp_pt_id').val(pt_id);
            jQuery('#DpExchangeModal').modal('show');
            dp_invoice_table.draw();
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
            console.log('💳💳💳 payment_btn clicked - preparing payment modal');

            jQuery('#another_cost').val('');
            jQuery('#admin_cost').val('');
            jQuery('#unique_code').val('');
            if (b1g1_temp && b1g1_temp.length > 0) {
                jQuery('#note').val('[B1G1]');
            } else {
                jQuery('#note').val('');
            }

            // Reset payment form state
            jQuery('#payment_option').val('one');
            jQuery('#pm_id_offline').val('');
            jQuery('#pm_id_offline_two').val('');
            jQuery('#total_payment').val('');
            jQuery('#total_payment_two').val('');
            jQuery('#return_payment').text('');
            jQuery('#return_payment_label').addClass('hidden');
            
            // Hide all payment method specific fields
            jQuery('#card_provider_content').addClass('hidden');
            jQuery('#card_number_label').addClass('hidden');
            jQuery('#ref_number_label').addClass('hidden');
            jQuery('#charge_label').addClass('hidden');
            jQuery('#sub_payment_offline_content').addClass('hidden');
            jQuery('#card_provider_content_two').addClass('hidden');
            jQuery('#card_number_label_two').addClass('hidden');
            jQuery('#ref_number_label_two').addClass('hidden');
            jQuery('#payment_method_two_section').addClass('hidden');
            jQuery('#total_payment_two_label').addClass('hidden');

            // Get total and display in payment modal
            var total_final_price_side = jQuery('#total_final_price_side').text();
            // Remove 'Rp. ' prefix if present, then remove commas
            var cleanTotal = String(total_final_price_side).replace('Rp. ', '').trim();
            var total = parseFloat(replaceComma(cleanTotal));
            var formattedTotal = addCommas(total.toString());
            jQuery('#payment_total').text('Rp. ' + formattedTotal);
            console.log('💳 Payment total set to: Rp. ' + formattedTotal);
            
            // Show payment modal - trigger Flowbite modal toggle
            console.log('💳 Opening payment modal via Flowbite');
            const paymentModal = document.getElementById('payment-offline-popup');
            if (paymentModal) {
                // Trigger Flowbite modal toggle
                paymentModal.classList.remove('hidden');
                paymentModal.classList.add('flex');
                console.log('💳 Payment modal opened successfully');
                
                // CRITICAL: Bind handlers immediately - use window function to avoid scope issues
                setTimeout(function() {
                    console.log('💳💳💳 Binding payment handlers after modal opens...');
                    var paymentOption = jQuery('#payment_option');
                    
                    console.log('💳 Payment option element found:', paymentOption.length > 0);
                    
                    if (paymentOption.length > 0) {
                        // Use window function if available
                        var handlerFunc = window.handlePaymentOptionChange || handlePaymentOptionChange;
                        
                        // Remove all existing handlers
                        paymentOption.off('change');
                        
                        // Bind with simple direct method
                        paymentOption.on('change', function() {
                            console.log('💳💳💳💳💳💳💳 PAYMENT OPTION CHANGED! Value:', jQuery(this).val());
                            if (handlerFunc) {
                                handlerFunc();
                            } else {
                                console.error('❌ Handler function not found!');
                            }
                        });
                        
                        console.log('✅ Payment option handler bound! Try changing dropdown now.');
                    } else {
                        console.error('❌ Payment option NOT FOUND in modal!');
                    }
                }, 200);
            } else {
                console.error('❌ Payment modal element not found');
            }
        });

        jQuery('#unique_code').on('change', function() {
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
            var final_total = parseFloat(replaceComma(payment_total)) + parseFloat(unique) + parseFloat(
                cost) - parseFloat(admin) + parseFloat(shipping_cost);
            jQuery('#payment_total').text(addCommas(final_total));
        });

        jQuery('#another_cost').on('change', function() {
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
            var final_total_admin = parseFloat(replaceComma(final_total)) + parseFloat(cost) -
                parseFloat(admin) + parseFloat(unique) + parseFloat(shipping_cost);
            jQuery('#payment_total').text(addCommas(final_total_admin));
        });

        jQuery('#admin_cost').on('change', function() {
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
            var final_total_admin = parseFloat(replaceComma(final_total)) + parseFloat(cost) -
                parseFloat(admin) + parseFloat(unique) + parseFloat(shipping_cost);
            jQuery('#payment_total').text(addCommas(final_total_admin));
        });

        jQuery('#shipping_cost').on('change', function() {
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
            var final_total_admin = parseFloat(replaceComma(final_total)) + parseFloat(cost) -
                parseFloat(admin) + parseFloat(unique) + parseFloat(shipping_cost);
            jQuery('#payment_total').text(addCommas(final_total_admin));
        });

        jQuery(document).delegate('#free_sock_no_btn', 'click', function() {
            jQuery('#payment-offline-popup').modal('show');
            var total_final_price_side = jQuery('#total_final_price_side').text();
            var total_nameset_side = jQuery('#total_nameset_side').text();
            var total = parseFloat(replaceComma(total_final_price_side));
            jQuery('#payment_total').text(addCommas(total));
        })

        // jQuery('#total_payment').on('keyup', function (e) {
        //     e.preventDefault();
        //     var total_price = jQuery('#payment_total').text();
        //
        //     var total_payment = jQuery(this).val();
        //     var integerNumber = parseFloat(total_payment.replace(/./g, ''));
        //     var integerTotal = parseFloat(total_price.replace(/,/g, ''));
        //     var return_payment = integerNumber - integerTotal;
        //     var method = jQuery('#payment_option option:selected').val();
        //     if (method == 'two') {
        //         if (total_payment == '') {
        //             jQuery('#total_payment_two').val(total_price);
        //         } else {
        //             jQuery('#total_payment_two').val(integerTotal - integerNumber);
        //         }
        //     } else {
        //         if (total_payment == '') {
        //             jQuery('#return_payment').text('');
        //         } else {
        //             jQuery('#return_payment').text(return_payment);
        //         }
        //     }
        // });


        jQuery('#charge').on('change', function(e) {
            e.preventDefault();
            var charge = jQuery(this).val();
            var payment_total = jQuery('#payment_total').text();
            var total_charge = parseFloat(replaceComma(payment_total)) / 100 * parseFloat(charge);
            jQuery('#charge_total').val(total_charge);
            if (charge != '') {
                jQuery('#payment_total').text(addCommas(parseFloat(replaceComma(payment_total)) +
                    total_charge));
            } else {
                jQuery('#payment_total').text(jQuery('#total_final_price_side').text());
            }
        });
        // Register pm_id_offline handler with event delegation
        console.log('💳💳💳 Registering pm_id_offline change handlers...');
        
        function handlePmIdOfflineChange() {
            var label = jQuery('#pm_id_offline option:selected').text();
            var paymentDisplay = jQuery('#payment_total').text();
            var cleanPayment = String(paymentDisplay).replace('Rp. ', '').trim();
            var paymentValueNumeric = replaceComma(cleanPayment);
            
            console.log('💳💳💳 Payment method 1 changed to: ' + label);
            console.log('💳 Payment value (numeric):', paymentValueNumeric);
            console.log('💳 Handler triggered successfully');
            
            // Helper function to set payment value with proper formatting
            function setPaymentValue(value) {
                if (value && value > 0) {
                    // Set numeric value and trigger input event to format it
                    var totalPaymentInput = document.getElementById('total_payment');
                    if (totalPaymentInput) {
                        totalPaymentInput.value = value.toString();
                        // Trigger input event to format the value
                        var inputEvent = new Event('input', { bubbles: true });
                        totalPaymentInput.dispatchEvent(inputEvent);
                    } else {
                        // Fallback to jQuery if native element not found
                        jQuery('#total_payment').val('Rp. ' + addCommas(value.toString()));
                    }
                } else {
                    jQuery('#total_payment').val('');
                }
            }
            
            if (label == 'DEBIT CARD') {
                jQuery('#card_provider_content').removeClass('hidden');
                jQuery('#card_number_label').removeClass('hidden');
                jQuery('#ref_number_label').removeClass('hidden');
                jQuery('#return_payment').text('');
                jQuery('#charge_label').addClass('hidden');
                jQuery('#sub_payment_offline_content').addClass('hidden');
                jQuery('#charge').val('');
                jQuery('#charge_total').val('');
                setPaymentValue(paymentValueNumeric);
            } else if (label == 'CREDIT CARD') {
                jQuery('#card_provider_content').removeClass('hidden');
                jQuery('#card_number_label').removeClass('hidden');
                jQuery('#ref_number_label').removeClass('hidden');
                jQuery('#return_payment').text('');
                jQuery('#charge_label').removeClass('hidden');
                jQuery('#sub_payment_offline_content').addClass('hidden');
                setPaymentValue(paymentValueNumeric);
            } else if (label == 'CASH') {
                jQuery('#card_provider_content').addClass('hidden');
                jQuery('#card_number_label').addClass('hidden');
                jQuery('#ref_number_label').addClass('hidden');
                jQuery('#sub_payment_offline_content').addClass('hidden');
                jQuery('#charge_label').addClass('hidden');
                jQuery('#charge').val('');
                jQuery('#charge_total').val('');
                setPaymentValue(0);
                jQuery('#return_payment').text('');
            } else if (label.includes('EDC')) {
                jQuery('#sub_payment_offline_content').removeClass('hidden');
                jQuery('#card_number_label').addClass('hidden');
                jQuery('#card_provider_content').addClass('hidden');
                jQuery('#ref_number_label').addClass('hidden');
                jQuery('#charge_label').addClass('hidden');
                jQuery('#charge').val('');
                jQuery('#charge_total').val('');
                setPaymentValue(paymentValueNumeric);
            } else {
                // For all other payment methods (TRANSFER BRI, TRANSFER BNI, TRANSFER BCA, QRIS, etc.)
                jQuery('#card_number_label').addClass('hidden');
                jQuery('#card_provider_content').addClass('hidden');
                jQuery('#ref_number_label').removeClass('hidden');
                jQuery('#return_payment').text('');
                jQuery('#sub_payment_offline_content').addClass('hidden');
                jQuery('#charge_label').addClass('hidden');
                jQuery('#charge').val('');
                jQuery('#charge_total').val('');
                setPaymentValue(paymentValueNumeric);
            }
        }
        
        // Use event delegation as primary method
        // Remove any existing handlers first to avoid duplicates
        jQuery(document).off('change', '#pm_id_offline');
        jQuery(document).on('change', '#pm_id_offline', function(e) {
            e.preventDefault();
            console.log('💳💳💳 pm_id_offline change event triggered!');
            console.log('💳 Event object:', e);
            console.log('💳 Current value:', jQuery(this).val());
            handlePmIdOfflineChange();
        });
        
        console.log('💳 pm_id_offline handler registered with event delegation');
        
        // Also try direct binding if element exists
        if (jQuery('#pm_id_offline').length > 0) {
            console.log('💳 pm_id_offline element exists, adding direct binding');
            jQuery('#pm_id_offline').off('change.payment-direct').on('change.payment-direct', function(e) {
                e.preventDefault();
                console.log('💳💳💳 pm_id_offline change (direct) triggered!');
                handlePmIdOfflineChange();
            });
        }

        jQuery('#pm_id_offline_two').on('change', function(e) {
            e.preventDefault();
            var label = jQuery('#pm_id_offline_two option:selected').text();
            //alert(label);
            if (label == 'DEBIT CARD') {
                jQuery('#card_provider_content_two').removeClass('hidden');
                jQuery('#card_number_label_two').removeClass('hidden');
                jQuery('#ref_number_label_two').removeClass('hidden');
            } else if (label == 'CASH') {
                jQuery('#card_provider_content_two').addClass('hidden');
                jQuery('#card_number_label_two').addClass('hidden');
                jQuery('#ref_number_label_two').addClass('hidden');
            } else {
                jQuery('#card_number_label_two').addClass('hidden');
                jQuery('#card_provider_content_two').addClass('hidden');
                jQuery('#ref_number_label_two').removeClass('hidden');
            }
        });

        jQuery('#checkout_btn').on('click', function(e) {
            e.preventDefault();
            
            var payment_option = jQuery('#payment_option option:selected').val();
            var payment_option_label = jQuery('#payment_option option:selected').text();
            var payment_method = jQuery('#pm_id_offline').val();
            var payment_method_label = jQuery('#pm_id_offline option:selected').text();
            var payment_method_two = jQuery('#pm_id_offline_two').val();
            var payment_method_two_label = jQuery('#pm_id_offline_two option:selected').text();
            var payment_total = parseInt(replaceComma(jQuery('#payment_total').text().replace('Rp. ', '')));
            var bayar = jQuery('#total_payment').val();
            var total_payment = parseFloat(bayar.replace(/\./g, ''));
            var total_payment_two = jQuery('#total_payment_two').val();
            var cp_id = jQuery('#cp_id').val();
            var cp_id_two = jQuery('#cp_id_two').val();

            // Validation
            if (payment_total > 0) {
                if (payment_option == '') {
                    if (typeof swal !== 'undefined') swal("Metode Pembayaran", "Silahkan pilih metode pembayaran", "warning");
                    return false;
                }
                if (payment_option != '' && payment_method == '') {
                    if (typeof swal !== 'undefined') swal("Jenis Pembayaran", "Silahkan pilih jenis pembayaran", "warning");
                    return false;
                }
                if (payment_option_label != '2 Metode' && payment_method_label == 'CASH' && total_payment < payment_total) {
                    if (typeof swal !== 'undefined') swal("Jumlah Dibayar", "Silahkan periksa jumlah dibayar", "warning");
                    return false;
                }
                if ((payment_method_label == 'DEBIT CARD' || payment_method_label == 'CREDIT CARD') && cp_id == '') {
                    if (typeof swal !== 'undefined') swal("Penyedia Kartu", "Silahkan pilih penyedia kartu", "warning");
                    return false;
                }
                if (payment_option_label == '2 Metode' && payment_method_two == '') {
                    if (typeof swal !== 'undefined') swal("Jenis Pembayaran Dua", "Silahkan pilih jenis pembayaran kedua", "warning");
                    return false;
                }
                if ((payment_method_two_label == 'DEBIT CARD' || payment_method_two_label == 'CREDIT CARD') && cp_id_two == '') {
                    if (typeof swal !== 'undefined') swal("Penyedia Kartu Kedua", "Silahkan pilih penyedia kartu kedua", "warning");
                    return false;
                }
                if (payment_method_two != '' && total_payment == '') {
                    if (typeof swal !== 'undefined') swal("Jumlah Dibayar Pertama", "Silahkan masukkan jumlah dibayar pertama", "warning");
                    return false;
                }
                if (payment_method_two != '' && total_payment_two == '') {
                    if (typeof swal !== 'undefined') swal("Jumlah Dibayar Kedua", "Silahkan masukkan jumlah dibayar kedua", "warning");
                    return false;
                }
            }

            // Show InputCodeModal using Flowbite
            const inputCodeModal = document.getElementById('InputCodeModal');
            if (inputCodeModal) {
                if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                    try {
                        let modal = Flowbite.Modal.getInstance(inputCodeModal);
                        if (!modal) {
                            modal = new Flowbite.Modal(inputCodeModal, { backdrop: 'static', closable: true });
                        }
                        modal.show();
                    } catch (err) {
                        console.error('Error opening input code modal:', err);
                        inputCodeModal.classList.remove('hidden');
                    }
                } else {
                    inputCodeModal.classList.remove('hidden');
                }
            }
            
            // Focus on secret code input
            setTimeout(() => {
                jQuery('#u_secret_code').focus();
            }, 300);
        });

        jQuery('#InputCodeModal').on('shown.bs.modal', function() {
            jQuery('#u_secret_code').focus();
        });

        jQuery('#f_access').on('submit', function(e) {
            var sweet_loader = '<div class="sweet_loader"><svg viewBox="0 0 140 140" width="140" height="140"><g class="outline"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="rgba(0,0,0,0.1)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path></g><g class="circle"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="#71BBFF" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dashoffset="200" stroke-dasharray="300"></path></g></svg></div>';

            e.preventDefault();
            var u_secret_code = jQuery('#u_secret_code').val();
            var cust_id_num = jQuery('#cust_id_num').val();
            var type = jQuery('#_type').val();
            
            if (jQuery.trim(u_secret_code) == '') {
                if (typeof swal !== 'undefined') swal("Kode Akses", "Scan Kode Akses Anda", "warning");
                return false;
            } else {
                jQuery.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    type: "POST",
                    data: {
                        _u_secret_code: u_secret_code,
                        _cust_id: cust_id_num
                    },
                    dataType: 'json',
                    url: "{{ url('check_secret_code') }}",
                    beforeSend: function() {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                html: '<h5>Loading...</h5>',
                                showConfirmButton: false,
                                didOpen: function() {
                                    Swal.getHtmlContainer().insertAdjacentHTML('afterbegin', sweet_loader);
                                }
                            });
                        }
                    },
                    success: function(r) {
                        if (r.status == '200') {
                            checkout();
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    html: '<h5>Success!</h5>',
                                    timer: 1000,
                                    showConfirmButton: false
                                });
                            }
                        } else {
                            if (typeof swal !== 'undefined') {
                                swal.fire('Salah', 'Kode salah', 'warning');
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire('Salah', 'Kode salah', 'warning');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error, xhr);
                        if (typeof swal !== 'undefined') {
                            swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
                        }
                    }
                });
            }
            return false;
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
        });

        // Debounced input handler for integer-only inputs
        const debouncedIntegerOnlyHandler = debounce(function(e) {
            let value = e.target.value;
            // Remove any non-digit characters
            value = value.replace(/[^0-9]/g, '');
            e.target.value = value;
        }, 100);

        // Apply integer-only restriction to all number inputs
        // Target all input[type="number"] elements
        jQuery(document).on('input', 'input[type="number"]', debouncedIntegerOnlyHandler);
        
        // Also target specific classes that should be integer-only
        jQuery(document).on('input', '.qty-input, .discount-percent, .discount-number, .namset-input', debouncedIntegerOnlyHandler);
        
        // Specific handlers for individual inputs
        jQuery(document).on('input', '#item_qty, #discount_percentage, #discount_number, #nameset_price', debouncedIntegerOnlyHandler);
        
        jQuery(document).on('paste', 'input[type="number"], .qty-input, .discount-percent, .discount-number, .namset-input', function(e) {
            setTimeout(() => {
                let value = e.target.value;
                value = value.replace(/[^0-9]/g, '');
                e.target.value = value;
            }, 1);
        });
        
        jQuery(document).on('keypress', 'input[type="number"], .qty-input, .discount-percent, .discount-number, .namset-input', function(e) {
            if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        jQuery('#product_name_input').on('keyup', function() {
            var query = jQuery(this).val();
            var type = jQuery('#std_id option:selected').text();
            var item_type = jQuery('#item_type option:selected').val();
            var std_id = jQuery('#std_id').val();
            var st_id = jQuery('#st_id').val();

            if (st_id == '') {
                st_id = {{ Auth::user()->st_id }};
            }
            if (jQuery.trim(query) != '' || jQuery.trim(query) != null) {
                console.log("running fadein")
                if (jQuery.trim(query).length > 2) {
                    jQuery.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        url: "{{ url('autocomplete_by_waiting') }}",
                        method: "POST",
                        data: {
                            query: query,
                            type: type,
                            _item_type: item_type,
                            _std_id: std_id,
                            _st_id: st_id
                        },
                        success: function(data) {
                            jQuery('#itemList').fadeIn();
                            jQuery('#itemList').html(data);
                        }
                    });
                } else {
                    jQuery('#itemList').fadeOut();
                }
            } else {
                console.log("running fadeout");
                jQuery('#itemList').fadeOut();
            }
        });

        jQuery('#invoice_input').on('keyup', function() {
            var query = jQuery(this).val();
            if (jQuery.trim(query) != '' || jQuery.trim(query) != null) {
                if (jQuery.trim(query).length > 4) {
                    jQuery.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        url: "{{ url('autocomplete_invoice_offline') }}",
                        method: "POST",
                        data: {
                            query: query
                        },
                        success: function(data) {
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

        jQuery('#product_name_input').on('change', function() {
            var query = jQuery(this).val();
            console.log(query);
            if (jQuery.trim(query) == '' || jQuery.trim(query) != null) {
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
                type: 'POST',
                url: "{{ url('cust_save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    jQuery("#save_customer_btn").html('Simpan');
                    jQuery("#save_customer_btn").attr("disabled", false);
                    if (data.status == '200') {
                        // console.log(data.new_id);
                        jQuery('#cust_id').val(data.new_id);
                        jQuery('#cust_id_num').val(data.new_id);
                        jQuery('#cust_id_label').val('');
                        // jQuery("#f_customer")[0].reset();
                        jQuery("#choosecustomer").modal('hide');
                        toast('Berhasil', 'Data berhasil disimpan', 'success');
                    } else if (data.status == '400') {
                        jQuery("#choosecustomer").modal('hide');
                        toast('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data) {
                    swal('Error', data, 'error');
                }
            });
        });





        jQuery('#inputKasButton').on('click', function(e) {
            e.preventDefault();
            jQuery.noConflict();
            jQuery('#shiftDetailModal').modal('hide');
            jQuery('#inputKasModal').modal('show');
            product.draw(false);

            var data = user_shift_table.row(this).data().id;
            var st_id = user_shift_table.row(this).data().st_id;
            var start_time_original = user_shift_table.row(this).data().start_time_original;
            var end_time_original = user_shift_table.row(this).data().end_time_original;
            var date = user_shift_table.row(this).data().date;
            var start_time = user_shift_table.row(this).data().start_time;
            var end_time = user_shift_table.row(this).data().end_time;
            var total_pos_real_price = user_shift_table.row(this).data().total_pos_real_price;
            var total_pos_payment_price = user_shift_table.row(this).data().total_pos_payment_price;
            var laba_shift = user_shift_table.row(this).data().laba_shift;
            var difference = user_shift_table.row(this).data().difference;
            var st_name = user_shift_table.row(this).data().st_name;
            var u_name = user_shift_table.row(this).data().u_name;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });



        jQuery(document).delegate('#f_laba_input', 'submit', function(e) {
            e.preventDefault();
            var numericValue = document.getElementById('laba_shift').value
            let laba = parseInt(numericValue.replace(/\D/g, ''));
            console.log(laba);

            if (jQuery.trim(numericValue) == '') {
                swal("Laba Shift", "Silahkan input nominal laba di shift anda", "warning");
                return false;
            } else {
                jQuery.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    type: "POST",
                    data: {
                        _laba_: laba
                    },
                    dataType: 'json',
                    url: "{{ url('user_end_shift') }}",
                    success: function(response) {
                        console.log(response);
                        swal('Berhasil', 'Laporan Shift Berhasil Disimpan', 'success');
                        jQuery('#InputLabaShift').modal('hide');

                        jQuery('#startShiftButton').show();
                        jQuery('#stopShiftButton').hide();

                    },
                    error: function(error) {
                        console.error('Error End shift:', error);
                    }
                });
                location.reload();
                return false;
            }



            // jQuery('#stopShiftButton').on('click', function() {
            //     shiftStarted = false;
            //     jQuery('#shiftStatus').html('Shift Stopped');
            //     jQuery('#startShiftButton').show();
            //     jQuery('#stopShiftButton').hide();
            //
            //     clearInterval(clockInterval);
            //
            //     jQuery.ajaxSetup({
            //         headers: {
            //             'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            //         }
            //     });
            //     jQuery.ajax({
            //         url: '/user_end_shift',
            //         method: 'POST',
            //         success: function(response) {
            //             console.log(response);
            //         },
            //         error: function(error) {
            //             console.error('Error End shift:', error);
            //         }
            //     });
            // });

            {{-- var code = jQuery('#voucher_code').val(); --}}
            {{-- var item = shoes_voucher_temp; --}}
            {{-- var total_final = jQuery('#total_final_price_side').text(); --}}
            {{-- var total = replaceComma(total_final); --}}
            {{-- if (shoes_voucher_temp.length <= 0) { --}}
            {{--    swal('Tidak ada sepatu', 'Tidak ada item yang berupa sepatu, voucher hanya berlaku untuk sepatu', 'warning'); --}}
            {{--    return false; --}}
            {{-- } --}}
            {{-- jQuery.ajaxSetup({ --}}
            {{--    headers: { --}}
            {{--        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') --}}
            {{--    } --}}
            {{-- }); --}}
            {{-- jQuery.ajax({ --}}
            {{--    type: "POST", --}}
            {{--    data: {code:code, item:item}, --}}
            {{--    dataType: 'json', --}}
            {{--    url: "{{ url('verify_voucher')}}", --}}
            {{--    success: function(r) { --}}
            {{--        if (r.status == '200') { --}}
            {{--            // ditemukan --}}
            {{--            var new_total = parseFloat(total) - parseFloat(r.sell) + parseFloat(r.value); --}}
            {{--            jQuery('#_voc_id').val(r.voc_id); --}}
            {{--            jQuery('#_voc_pst_id').val(r.pst_id); --}}
            {{--            jQuery('#_voc_value').val(r.disc_value); --}}
            {{--            jQuery('#_voc_article').text(r.article); --}}
            {{--            jQuery('#_voc_bandrol').text(addCommas(r.bandrol)); --}}
            {{--            jQuery('#_voc_disc').text(addCommas(r.disc)); --}}
            {{--            jQuery('#_voc_disc_type').text(addCommas(r.disc_type)); --}}
            {{--            jQuery('#_voc_disc_value').text('('+addCommas(r.disc_value)+')'); --}}
            {{--            jQuery('#_voc_value_show').text(addCommas(r.value)); --}}
            {{--            jQuery('#total_final_price_side').text(addCommas(new_total)); --}}
            {{--            sell_price_voc = r.sell; --}}
            {{--            value_price_voc = r.value; --}}
            {{--            jQuery('#voucher_information').removeClass("d-none"); --}}
            {{--            jQuery('#voucher_code').prop('disabled', true); --}}
            {{--        } else if (r.status == '201') { --}}
            {{--            // salah --}}
            {{--            alert('beda platform'); --}}
            {{--        } else if (r.status == '202') { --}}
            {{--            // salah --}}
            {{--            alert('sudah pernah dipakai'); --}}
            {{--        } else if (r.status == '203') { --}}
            {{--            // salah --}}
            {{--            alert('sudah pernah dipakai namun belum 1 bulan'); --}}
            {{--        } else if (r.status == '204') { --}}
            {{--            // salah --}}
            {{--            alert('tidak ada item untuk diskon'); --}}
            {{--        } else { --}}
            {{--            alert('kode salah'); --}}
            {{--        } --}}
            {{--    } --}}
            {{-- }); --}}
        });

        document.getElementById('total_payment').addEventListener('input', function(e) {
            const input = e.target;
            let value = input.value;

            // Remove non-digit characters
            value = value.replace(/\D/g, '');

            // Format the number as currency
            const formattedValue = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);

            // Update the input field with the formatted value
            input.value = formattedValue.replace('IDR', 'Rp');
        });

        jQuery('#total_payment').on('keyup', function(e) {
            e.preventDefault();
            var total_price = jQuery('#payment_total').text();
            var total_payment = jQuery(this).val();

            total_payment = total_payment.replace(/Rp|\,/g, '').trim();

            console.log(total_payment); // Log the cleaned value of the currency input
            var return_payment = parseFloat(total_payment.replace(".", "")) - parseFloat(replaceComma(
                total_price));
            var method = jQuery('#payment_option option:selected').val();
            if (method == 'two') {
                if (total_payment == '') {
                    jQuery('#total_payment_two').val(total_price);
                } else {
                    jQuery('#total_payment_two').val(parseFloat(replaceComma(total_price)) -
                        total_payment);
                }
            } else {
                if (total_payment == '') {
                    jQuery('#return_payment').text('');
                } else {
                    jQuery('#return_payment').text(addCommas(return_payment));
                }
            }
        });


        /* Rupiah format */
        // function formatRupiah(angka, prefix) {
        //     var number_string = angka.replace(/[^,\d]/g, '').toString(),
        //         split = number_string.split(','),
        //         sisa = split[0].length % 3,
        //         rupiah = split[0].substr(0, sisa),
        //         ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        //
        //     if (ribuan) {
        //         separator = sisa ? '.' : '';
        //         rupiah += separator + ribuan.join('.');
        //     }
        //
        //     rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        //     return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        // }
        //
        // /* Rupiah format */
        // function formatRupiahTotal(angka, prefix) {
        //     var number_string = angka.replace(/[^,\d]/g, '').toString(),
        //         split   		= number_string.split(','),
        //         sisa     		= split[0].length % 3,
        //         rupiah     		= split[0].substr(0, sisa),
        //         ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
        //
        //     // tambahkan titik jika yang di input sudah menjadi angka ribuan
        //     if(ribuan){
        //         separator = sisa ? '.' : '';
        //         rupiah += separator + ribuan.join('.');
        //     }
        //
        //     rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        //     return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        // }
        
        console.log('✅ offline_pos_v2 jQuery(document).ready finished - all handlers attached');

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
            data: {
                _cust_phone: cust_phone
            },
            dataType: 'json',
            url: "{{ url('check_exists_customer') }}",
            success: function(r) {
                if (r.status == '200') {
                    swal('No Telepon',
                        'Nomor telepon sudah ada disistem, silahkan ganti dengan yang lain',
                        'warning');
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
            swal('Tidak ada sepatu', 'Tidak ada item yang berupa sepatu, voucher hanya berlaku untuk sepatu',
                'warning');
            return false;
        }
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: "POST",
            data: {
                code: code,
                item: item
            },
            dataType: 'json',
            url: "{{ url('verify_voucher') }}",
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
                    jQuery('#_voc_disc_value').text('(' + addCommas(r.disc_value) + ')');
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

    jQuery(document).ready(function() {
        // Event listener to add a new voucher input field
        jQuery(document).on('click', '.add-voucher', function() {
            console.log('[debug] .add-voucher clicked');
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
        jQuery(document).on('click', '.remove-voucher', function() {
            jQuery(this).closest('.input-group').remove();
        });
    });

    jQuery(document).ready(function() {
        // Event listener to add a new discount input field
        jQuery(document).on('click', '.add-total-discount', function() {
            console.log('[debug] .add-total-discount clicked');

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
        jQuery(document).on('click', '.remove-total-discount', function() {
            let total_price = jQuery("#total_final_price_side").text();
            let total_discount_value_side = jQuery("#total_discount_value_side").text();
            let discount = jQuery(this).closest('.input-group').find('input').val();
            let discountType = jQuery('select[name="discount-type-list"]').val();

            // Remove comma from total price and discount
            total_price = replaceComma(total_price);
            total_discount_value_side = replaceComma(total_discount_value_side);

            if (discountType === "nominal") {
                let new_total_price = parseFloat(total_price) + parseFloat(discount);
                let new_total_discount_value_side = parseFloat(total_discount_value_side) - parseFloat(
                    discount);
                jQuery('#total_final_price_side').text(addCommas(new_total_price));
                jQuery('#total_discount_value_side').text(addCommas(new_total_discount_value_side));
            } else if (discountType === "percentage") {
                let discountAmount = (parseFloat(discount) / 100) * parseFloat(total_price);
                let new_total_price = parseFloat(total_price) + discountAmount;
                let new_total_discount_value_side = parseFloat(total_discount_value_side) -
                    discountAmount;
                jQuery('#total_final_price_side').text(addCommas(new_total_price));
                jQuery('#total_discount_value_side').text(addCommas(new_total_discount_value_side));
            }

            jQuery(this).closest('.input-group').remove();
        });


        // Event listener to reset total discount input fields
        jQuery(document).on('click', '#total_discount_reset', function() {
            // Simulate clicking each remove button to trigger the remove event
            jQuery('.remove-total-discount').each(function() {
                jQuery(this).trigger('click');
            });

            let total_price = jQuery("#total_final_price_side").text();
            let total_discount_value_side = jQuery("#total_discount_value_side").text();
            total_price = replaceComma(total_price);
            total_discount_value_side = replaceComma(total_discount_value_side);

            // Get the value of the first discount input field
            let firstDiscount = jQuery('input[name="total-discount-list[]"]').first().val();
            firstDiscount = replaceComma(firstDiscount);

            if (firstDiscount !== '') {
                // Calculate the new total price and total discount value
                let new_total_price = parseFloat(total_price) + parseFloat(firstDiscount);
                let new_total_discount_value_side = parseFloat(total_discount_value_side) - parseFloat(
                    firstDiscount);

                // Update the total final price and total discount value
                jQuery('#total_final_price_side').text(addCommas(new_total_price));
                jQuery('#total_discount_value_side').text(addCommas(new_total_discount_value_side));
            }
            // Remove all discount input fields except the first one
            jQuery('#total-discount-container .input-group:not(:first)').remove();

            // Reset the first discount input field value to ''
            jQuery('input[name="total-discount-list[]"]').first().val('');
        });
    });

    // External jQuery handler disabled to avoid duplicate requests — inline handler in blade handles submission.
    jQuery(document).delegate('#f_add_voucher', 'submit', function(e) {
        e.preventDefault();
        console.warn('[debug] external #f_add_voucher handler disabled to avoid duplicate POST; inline handler should handle submit');
        return false;
    });


    // hitung tambah discount
    jQuery(document).delegate('#f_add_total_discount', 'submit', function(e) {
        e.preventDefault();
        console.log('[debug] #f_add_total_discount submit fired');

        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var temporary_disc = jQuery('#discount_total_temporary').val();

        console.log('TOTAL DISKON :', temporary_disc);

        var new_discount = 0;
        var total_final = jQuery('#total_final_price_side').text();
        var curren_discount = removeCommasAndConvertToNumber(jQuery('#total_discount_value_side').text());
        var total = replaceComma(total_final);
        console.log(total);
        var formData = jQuery('#f_add_total_discount').serializeArray();

        jQuery.ajax({
            type: "POST",
            url: "{{ url('pos-total-discount') }}",
            data: {
                formData: formData,
            },
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    if (r.discountType == 'percentage') {
                        // create count use percentage from total
                        var discount = parseFloat(total) * parseFloat(r.total) / 100;
                        var new_discount = curren_discount + discount;
                        var new_total = parseFloat(total) - parseFloat(discount);
                        jQuery('#total_final_price_side').text(addCommas(new_total));
                        jQuery('#total_discount_value_side').text(addCommas(new_discount));


                        console.log(discount, 'discount hitung persen');
                        console.log(curren_discount, 'current discount');
                        console.log(new_discount, 'diskon baru');
                        var total_temporary = new_discount + temporary_disc;

                        jQuery('#discount_total_temporary').val(total_temporary);
                    }

                    if (r.discountType == 'nominal') {
                        var discount = parseFloat(r.total);
                        var new_discount = curren_discount + discount;
                        var new_total = parseFloat(total) - parseFloat(discount);

                        jQuery('#total_final_price_side').text(addCommas(new_total));
                        jQuery('#total_discount_value_side').text(addCommas(new_discount));

                        console.log(discount, 'discount hitung persen');
                        console.log(curren_discount, 'current discount');
                        console.log(new_discount, 'diskon baru');
                        var total_temporary = new_discount + temporary_disc;

                        jQuery('#discount_total_temporary').val(total_temporary);
                    }
                } else {
                    alert('kode salah');
                }
            }
        });
    });
</script>
