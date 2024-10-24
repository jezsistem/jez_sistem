<!-- DATERANGE -->
<script src="{{ asset('app') }}/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
<script>
    function checkPurchasePrice(pid)
    {
        var discount = parseFloat($('#po_discount'+pid).val());
        var price_tag = parseFloat($('#po_price_tag'+pid).val());
        var purchase_price = price_tag - (price_tag * discount / 100);

        $('input[data-purchase-price='+pid+']').val(purchase_price);
        $('#po_discount'+pid).prop('disabled', true);
    }

    function checkTotalItem(pid, index)
    {
        var qty = parseFloat($('#po_order_qty'+pid+index).val());
        var price_tag = parseFloat($('#po_price_tag'+pid).val());
        var purchase_price = parseFloat($('#po_purchase_price'+pid+index).val());
        var total_row = $('tr[data-id-'+pid+']').length;

        if (isNaN(purchase_price)) {
            var total_item_price = qty * price_tag;
            $('#po_purchase_price'+pid+index).val(price_tag)
        } else {
            var total_item_price = qty * purchase_price;
        }

        $('#po_total_item_price'+pid+index).val(total_item_price);
        $('#po_discount'+pid).prop('disabled', true);
    }

    function reloadArticleDetail(id)
    {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            dataType: 'html',
            data: {_po_id:id},
            url: "{{ url('check_po_receive_detail')}}",
            success: function(r) {
                $('#purchase_order_detail_content').html(r);
            }
        });
    }

    function savePoads(poad_id, poa_id, index, pst_id)
    {
        var receive_date = $('#receive_date').val();
        var receive_invoice = $('#receive_invoice').val();
        var invoice_date = $('#invoice_date').val();
        if (receive_date == '') {
            swal("Tanggal Terima", "Tentukan tanggal terima", "warning");
            return false;
        }
        if (receive_invoice == '') {
            swal("Invoice Terima", "Tentukan invoice terima", "warning");
            return false;
        }
        if (invoice_date == '') {
            swal("Tanggal Invoice", "Tentukan tanggal invoice", "warning");
            return false;
        }
        swal({
            title: "Simpan..?",
            text: "Yakin simpan penerimaan untuk size ini ?",
            icon: "info",
            buttons: [
                'Batalkan',
                'Simpan'
            ],
            dangerMode: false,
        }).then(function(isConfirm) {
            if (isConfirm) {
                var stkt_id = $('#stkt_id').val();
                if (stkt_id == '') {
                    swal("Tipe Stok", "Pilih Tipe Stok", "warning");
                    return false;
                }
                var st_id = $('#st_id').val();
                var tax_id = $('#tax_id').val();
                var po_id = $('#_po_id').val();
                var poads_discount = $('#poa_discount'+poa_id).val();
                var poads_extra_discount = $('#poa_extra_discount'+poa_id).val();
                var poads_purchase_price = replaceComma($('#poad_purchase_price_'+poa_id+'_'+index).val());
                var poads_qty = $('#poads_qty_'+poa_id+'_'+index).val();
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    data: {receive_date:receive_date, receive_invoice:receive_invoice, invoice_date:invoice_date, _st_id:st_id, _stkt_id:stkt_id, _tax_id:tax_id, _pst_id:pst_id, _poad_id:poad_id, _poads_qty:poads_qty, _poads_discount:poads_discount, _poads_extra_discount:poads_extra_discount, _poads_purchase_price:poads_purchase_price},
                    dataType: 'json',
                    url: "{{ url('poads_save')}}",
                    success: function(r) {
                        if (r.status == '200'){
                            $('#receive_date').val('');
                            $('#receive_invoice').val('');
                            $('#invoice_date').val('');
                            reloadArticleDetail(po_id);
                            swal("Berhasil", "Data berhasil disimpan", "success");
                        } else {
                            swal('Gagal', 'Gagal simpan data', 'error');
                        }
                    }
                });
                return false;
            }
        })
    }

    function bulkSavePoads(poad_id, poa_id, index, pst_id)
    {
        var stkt_id = $('#stkt_id').val();
        if (stkt_id == '') {
            swal("Tipe Stok", "Pilih Tipe Stok", "warning");
            return false;
        }
        var st_id = $('#st_id').val();
        var tax_id = $('#tax_id').val();
        var po_id = $('#_po_id').val();
        var poads_discount = $('#poa_discount'+poa_id).val();
        var poads_extra_discount = $('#poa_extra_discount'+poa_id).val();
        var poads_purchase_price = replaceComma($('#poad_purchase_price_'+poa_id+'_'+index).val());
        var poads_qty = $('#poads_qty_'+poa_id+'_'+index).val();
        var receive_date = $('#receive_date').val();
        var receive_invoice = $('#receive_invoice').val();
        var invoice_date = $('#invoice_date').val();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {receive_date:receive_date, receive_invoice:receive_invoice, invoice_date:invoice_date, _st_id:st_id, _stkt_id:stkt_id, _tax_id:tax_id, _pst_id:pst_id, _poad_id:poad_id, _poads_qty:poads_qty, _poads_discount:poads_discount, _poads_extra_discount:poads_extra_discount, _poads_purchase_price:poads_purchase_price},
            dataType: 'json',
            url: "{{ url('poads_save')}}",
            success: function(r) {
                if (r.status == '200'){
                    toast("Berhasil", "Data berhasil disimpan", "success");
                } else {
                    swal('Gagal', 'Gagal simpan data', 'error');
                }
            }
        });
        return false;
    }

    function saveAllPoads() {
        var po_id = $('#_po_id').val();
        var receive_date = $('#receive_date').val();
        var receive_invoice = $('#receive_invoice').val();
        var invoice_date = $('#invoice_date').val();
        if (receive_date == '') {
            swal("Tanggal Terima", "Tentukan tanggal terima", "warning");
            return false;
        }
        if (receive_invoice == '') {
            swal("Invoice Terima", "Tentukan invoice terima", "warning");
            return false;
        }
        if (invoice_date == '') {
            swal("Tanggal Invoice", "Tentukan tanggal invoice", "warning");
            return false;
        }
        swal({
            title: "Simpan..?",
            text: "Yakin simpan data penerimaan ini ?",
            icon: "info",
            buttons: [
                'Batalkan',
                'Simpan'
            ],
            dangerMode: false,
        }).then(function(isConfirm) {
            if (isConfirm) {
                var total_row = $('img[data-img-poads]').length;
                for (let i = 0; i < total_row; ++i) {
                    $('#savePoads'+i).trigger('dblclick');
                }
                reloadArticleDetail(po_id);
            }
        });
    };

    function deletePoad(id)
    {
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
                var po_id = $('#_po_id').val();
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    data: {_id:id},
                    dataType: 'json',
                    url: "{{ url('poad_delete')}}",
                    success: function(r) {
                        if (r.status == '200'){
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

    function deletePoa(id, po_id)
    {
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
                    data: {_id:id},
                    dataType: 'json',
                    url: "{{ url('poa_delete')}}",
                    success: function(r) {
                        if (r.status == '200'){
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
    function poadPurchasePrice(id, index, purchase_price)
    {
        var poad_id = $('#poad_purchase_price_'+id+'_'+index).attr('data-poad-id');
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_poad_id:poad_id, _purchase_price:purchase_price},
            dataType: 'json',
            url: "{{ url('poad_save_purchase_price')}}",
            success: function(r) {
                if (r.status == '200'){

                } else {
                    toast('Gagal', 'Informasi gagal disimpan' ,'warning');
                }
            }
        });
    }

    function discount(id)
    {
        var discount = $('#poa_discount'+id).val();
        var extra_discount = $('#poa_extra_discount'+id).val();
        var total_row = $('span[data-poa-'+id+']').length;
        var total = 0;

        if (discount == '' || discount == 0) {
            $('#poa_extra_discount'+id).val('');
            discount = 0;
            extra_discount = 0;
        }

        if (extra_discount == '' || extra_discount == 0) {
            for (let i = 0; i < total_row; ++i) {
                var price_tag = parseFloat(replaceComma($('#price_tag_'+id+'_'+i).val()));
                total = price_tag - (price_tag/100 * parseFloat(discount))
                $('#poad_purchase_price_'+id+'_'+i).val(addCommas(total));
                $('#poads_qty_'+id+'_'+i).val('');
                $('#total_purchase_price_receive'+id+'_'+i).val('');
            }
        } else {
            for (let i = 0; i < total_row; ++i) {
                var price_tag = parseFloat(replaceComma($('#price_tag_'+id+'_'+i).val()));
                var subtotal = price_tag - (price_tag/100 * parseFloat(discount))
                var total = subtotal - (subtotal/100 * parseFloat(extra_discount))
                $('#poad_purchase_price_'+id+'_'+i).val(addCommas(total));
                $('#poads_qty_'+id+'_'+i).val('');
                $('#total_purchase_price_receive'+id+'_'+i).val('');
            }
        }
    }

    function extraDiscount(id)
    {
        var discount = $('#poa_discount'+id).val();
        var extra_discount = $('#poa_extra_discount'+id).val();
        var total_row = $('span[data-poa-'+id+']').length;
        var total = 0;

        if (discount == 0 || discount == null) {
            swal('Diskon', 'Diskon kosong, silahkan isi terlebih dahulu', 'warning');
            $('#poa_extra_discount'+id).val('');
        } else {
            if (extra_discount == '' || extra_discount == 0) {
                extra_discount = 0;
            }
            for (let i = 0; i < total_row; ++i) {
                var price_tag = parseFloat(replaceComma($('#price_tag_'+id+'_'+i).val()));
                var subtotal = price_tag - (price_tag/100 * parseFloat(discount))
                var total = subtotal - (subtotal/100 * parseFloat(extra_discount))
                $('#poad_purchase_price_'+id+'_'+i).val(addCommas(total));
                $('#poads_qty_'+id+'_'+i).val('');
                $('#total_purchase_price_receive'+id+'_'+i).val('');
            }
        }
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

    function receiveQty(id, index)
    {
        var po_id = $('#_po_id').val();
        var order_qty = $('#poads_qty_remain_'+id+'_'+index).val();
        var qty = $('#poads_qty_'+id+'_'+index).val();
        var purchase_price = $('#poad_purchase_price_'+id+'_'+index).val();
        var price_tag = $('#price_tag_'+id+'_'+index).val();
        var total_purchase_price = $('#total_purchase_price_'+id+'_'+index).val();
        var total_row = $('span[data-poa-'+id+']').length;
        var discount = $('#poa_discount'+id).val();
        var extra_discount = $('#poa_extra_discount'+id).val();
        var total_price = 0;
        var total = 0;

        if (discount == '' || discount == 0) {
            discount = 0;
        }
        price_tag = replaceComma(price_tag);
        if (price_tag == '' || price_tag == 0) {
            price_tag = replaceComma(total_purchase_price) / order_qty;
        }
        purchase_price = price_tag - (price_tag/100 * discount);
        //alert(purchase_price +' '+price_tag+' '+discount);
        $('#poad_purchase_price_'+id+'_'+index).val(addCommas(purchase_price));
        if (qty == '') {
            qty = 0;
        }
        total = parseFloat(qty) * parseFloat(purchase_price);

        if (parseInt(qty) > parseInt(order_qty)) {
            $('#poads_qty_'+id+'_'+index).val('');
            swal('Jumlah Terima', 'Jumlah terima tidak dapat lebih dari jumlah kekurangan', 'warning');
            $('#total_purchase_price_receive'+id+'_'+index).val('')
            return false;
        }
        $('#total_purchase_price_receive'+id+'_'+index).val(addCommas(total));
        for (let i = 0; i < total_row; ++i) {
            total_price = total_price + parseFloat(replaceComma($('#total_purchase_price_receive'+id+'_'+i).val()));
        }
        $('#poad_total_price_receive_'+id).text(addCommas(total_price));
    }

    function receivePurchasePrice(id, index)
    {
        var qty = $('#poads_qty_'+id+'_'+index).val();
        var purchase_price = $('#poad_purchase_price_'+id+'_'+index).val();
        var total = parseFloat(qty) * parseFloat(replaceComma(purchase_price));
        $('#total_purchase_price_receive'+id+'_'+index).val(addCommas(total));
    }

    function reminder(id)
    {
        var reminder = $('#poa_reminder'+id).val();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_id:id, _reminder:reminder},
            dataType: 'json',
            url: "{{ url('poa_save_reminder')}}",
            success: function(r) {
                if (r.status == '200'){
                    toast('Disimpan', 'Informasi berhasil disimpan' ,'success');
                } else {
                    toast('Gagal', 'Informasi gagal disimpan' ,'warning');
                }
            }
        });
    }
    // CALCULATION

    $(document).delegate('#po_check_item', 'click', function() {
        var pid = $(this).attr('data-id');
        var index = $(this).attr('data-index');
        var total_item_price = $('#po_total_item_price'+pid+index).val();
        var po_total_price = $('#po_total_price'+pid).val();
        if (total_item_price == '' || total_item_price == 0) {
            swal('Order Qty', 'silahkan tentukan jumlah item PO terlebih dahulu', 'warning');
            return false;
        }
        if ($(this).is(':checked')) {
            $('#po_total_price'+pid).val(parseFloat(po_total_price)+parseFloat(total_item_price));
        } else {
            $('#po_total_price'+pid).val(parseFloat(po_total_price)-parseFloat(total_item_price));
        }
        alert(pid+' || '+index+' || '+po_total_price);
    });

    $(document).delegate('#checkbox_add_item', 'click', function() {
        var poid = $('#_po_id').val();
        var pid = $(this).attr('data-pid');
        var psid = $(this).attr('data-psid');
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
            data: {_poid:poid, _pid:pid, _psid:psid, _status:status},
            url: "{{ url('create_po_detail')}}",
            success: function(r) {
                if (r.status == '200') {

                } else {
                    swal('Sudah ada', 'Produk sudah ada pada list PO', 'warning');
                    $('.checkbox_add_item'+pid+'').prop('checked', false);
                }
            }
        });
    });

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var purchase_order_table = $('#PurchaseOrdertb').DataTable({
            destroy: true,
            processing: false,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('purchase_order_datatables') }}",
                data : function (d) {
                    d.search = $('#purchase_order_search').val();
                    d.st_id = $('#st_id_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'po_id', searchable: false},
            { data: 'po_created_at_show', name: 'po_created_at' },
            { data: 'st_name', name: 'st_name' },
            { data: 'ps_name', name: 'ps_name' },
            { data: 'po_invoice', name: 'po_invoice' },
            { data: 'po_total', name: 'po_total', orderable: false },
            { data: 'po_status', name: 'po_status', orderable: false },
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
                url : "{{ url('product_item_datatables') }}",
                data : function (d) {
                    d.search = $('#product_search').val();
                    d.ps_id = $('#ps_id').val();
                    d.br_id_filter = $('#br_id_filter_item').val();
                    d.mc_id_filter = $('#mc_id_filter_item').val();
                    d.sz_id_filter = $('#sz_id_filter_item').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pid', searchable: false},
            { data: 'p_image_show', name: 'p_image_show', sortable: false },
            { data: 'p_name_show', name: 'p_name' },
            { data: 'p_color_show', name: 'p_color' },
            { data: 'br_name', name: 'br_name' },
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

        var poads_table = $('#Poadstb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('poads_datatables') }}",
                data : function (d) {
                    d.poad_id = $('#poad_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'poads_id', searchable: false},
            { data: 'created_at_show', name: 'created_at', orderable: false },
            { data: 'stkt_name', name: 'stkt_name', orderable: false },
            { data: 'tx_code', name: 'tx_code', orderable: false },
            { data: 'poads_qty', name: 'poads_qty', orderable: false },
            { data: 'poads_discount', name: 'poads_discount', orderable: false },
            { data: 'poads_extra_discount', name: 'poads_extra_discount', orderable: false },
            { data: 'poads_purchase_price_show', name: 'poads_purchase_price', orderable: false },
            { data: 'poads_total_price_show', name: 'poads_total_price', orderable: false },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        purchase_order_table.buttons().container().appendTo($('#purchase_order_excel_btn' ));
        $('#purchase_order_search').on('keyup', function() {
            purchase_order_table.draw(false);
        });

        $('#product_search').on('keyup', function() {
            product_table.draw(false);
        });

        $('#br_id_filter_item').on('change', function() {
            product_table.draw(false);
        });

        $('#mc_id_filter_item').on('change', function() {
            product_table.draw(false);
        });

        $('#sz_id_filter_item').on('change', function() {
            product_table.draw(false);
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

        $('#stkt_id').select2({
            width: "100%",
            dropdownParent: $('#stkt_id_parent')
        });
        $('#stkt_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#tax_id').select2({
            width: "100%",
            dropdownParent: $('#tax_id_parent')
        });
        $('#tax_id').on('select2:open', function (e) {
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

        $('#st_id_filter').on('change', function() {
            purchase_order_table.draw(false);
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

        $(document).delegate('#receive_history', 'click', function() {
            var poad_id = $(this).attr('data-poad_id');
            var p_name = $(this).attr('data-p_name');
            $('#poad_id').val(poad_id);
            $('#purchase_article_label').text(p_name);
            $('#PurchaseOrderReceiveDetailModal').modal('show');
            poads_table.draw(false);
        });
        
        $(document).delegate('#receive_invoice', 'change', function(e) {
            e.preventDefault();
            var invoice = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {invoice:invoice},
                url: "{{ url('check_po_invoice')}}",
                success: function(r) {
                    if (r.status == '200') {
                        $('#receive_invoice').val('');
                        swal('INV Sudah Ada', 'Invoice sudah pernah diinput', 'warning');
                    }
                }
            });
        });

        $('#Poadstb tbody').on('click', 'tr', function () {
            var id = poads_table.row(this).data().poads_id;
            var stkt_id = poads_table.row(this).data().stkt_id;
            var poads_qty = poads_table.row(this).data().poads_qty;
            var poads_discount = poads_table.row(this).data().poads_discount;
            var poads_extra_discount = poads_table.row(this).data().poads_extra_discount;
            var poads_purchase_price = poads_table.row(this).data().poads_purchase_price;
            var poads_total_price = poads_table.row(this).data().poads_total_price;
            var created_at = poads_table.row(this).data().created_at;
            //alert(stkt_id);
            $('#EditPoadsModal').modal('show');
            $('#_poads_id').val(id);
            $('#created_at').val(created_at);
            $('#stkt_id_edit').val(stkt_id).trigger('changes');
            $('#poads_qty').val(poads_qty);
            $('#poads_discount').val(poads_discount);
            $('#poads_extra_discount').val(poads_extra_discount);
            $('#poads_purchase_price').val(poads_purchase_price);
            $('#poads_total_price').val(poads_total_price);
            @if ( $data['user']->delete_access == '1' )
                $('#delete_poads_btn').show();
            @endif
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
                data: {_po_id:po_id},
                url: "{{ url('po_receive_detail')}}",
                success: function(r) {
                    if (r.status == '200') {
                        jQuery.noConflict();
                        $('#f_po')[0].reset();
                        $('#PurchaseOrderModal').modal('show');
                        $('#po_invoice_label').text(r.po_invoice);
                        $('#_mode').val('edit');
                        $('#_po_id').val(r.po_id);
                        $('#po_description').val(r.po_description);
                        jQuery('#st_id').val(r.st_id).trigger('change');
                        jQuery('#ps_id').val(r.ps_id).trigger('change');
                        jQuery('#stkt_id').val(r.stkt_id).trigger('change');
                        jQuery('#tax_id').val('').trigger('change');
                        reloadArticleDetail(po_id);
                    } else {
                        swal('Error', 'terjadi kesalahan', 'warning');
                    }
                }
            });
        });

        $('#f_poads').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            swal({
                title: "Simpan..?",
                text: "Yakin simpan penerimaan ?",
                icon: "warning",
                buttons: [
                    'Jangan Simpan',
                    'Simpan'
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
                        cache:false,
                        contentType: false,
                        processData: false,
                        url: "{{ url('sv_poads_revision')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                $('#EditPoadsModal').modal('hide');
                                poads_table.draw(false);
                                reloadArticleDetail($('#_po_id').val());
                            } else {
                                swal('Gagal', 'Gagal simpan revisi', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#delete_poads_btn').on('click', function(e) {
            e.preventDefault();
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data penerimaan ini ?",
                icon: "warning",
                buttons: [
                    'Jangan Hapus',
                    'Hapus Penerimaan'
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
                        data: {_id:$('#_poads_id').val()},
                        dataType: 'json',
                        url: "{{ url('dl_poads_revision')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                $('#EditPoadsModal').modal('hide');
                                poads_table.draw(false);
                                reloadArticleDetail($('#_po_id').val());
                            } else {
                                swal('Gagal', 'Gagal hapus penerimaan', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#save_purchase_order_btn').on('click', function(e) {
            e.preventDefault();
            $('#PurchaseOrderModal').modal('hide');
            var po_id = $('#_po_id').val();
            purchase_order_table.draw(false);
        });

        $('#cancel_purchase_order_btn').on('click', function(){
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
                        data: {_id:$('#_po_id').val()},
                        dataType: 'json',
                        url: "{{ url('cancel_po')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                $('#PurchaseOrderModal').modal('hide');
                                purchase_order_table.draw(false);
                            } else {
                                swal('Gagal', 'Gagal batalkan PO', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#export_btn').on('click', function() {
            $('#PoReporttb').find('tr:not(:has(th))').remove();
            var po_date = $('#po_date').val();
            var po_status = $('#po_status_filter').val();
            var st_id = $('#st_id_filter').val();
            var dt = $('#kt_dashboard_daterangepicker_date').text();
            var status = $('#po_status_filter option:selected').text();
            if (po_status == '') {
                swal('Pilih Status', 'Silakan pilih status', 'info');
                return false;
            }
            swal({
                text: "Yakin export laporan penerimaan di tanggal "+dt+" dengan status "+status+" .. ?",
                icon: "info",
                buttons: [
                    'Batal',
                    'Yakin'
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
                        data: {po_date:po_date, po_status:po_status, st_id:st_id},
                        dataType: 'json',
                        url: "{{ url('po_export')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                $('#PoReportModal').modal('show');
                                $(r.data).each(function(index, row) {
                                    jQuery('#PoReporttb tr:last').after("<tr><td>"+(parseInt(index)+parseInt(1))+"</td><td>"+row['po_created']+"</td><td>"+row['po_invoice']+"</td><td>"+row['store']+"</td><td>"+row['brand']+"</td><td>"+row['article']+"</td><td>"+row['color']+"</td><td>"+row['size']+"</td><td>"+row['order']+"</td><td>"+row['receive']+"</td><td>"+row['hpp']+"</td><td>"+row['total']+"</td></tr>");
                                });
                            } else {
                                swal('Gagal', 'Gagal menampilkan data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#excel_report').on('click', function () {
            jQuery("#PoReporttb").table2excel({
                filename: "Laporan Penerimaan Topsystem",
            });
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
                range = start.format('MMM D');
                hidden_range = start.format('YYYY-MM-DD');
            } else if (label == 'Yesterday') {
                title = 'Yesterday:';
                range = start.format('MMM D');
                hidden_range = start.format('YYYY-MM-DD');
            } else {
                range = start.format('MMM D') + ' - ' + end.format('MMM D');
                hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }

            $('#po_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
        }

        picker.daterangepicker({
            direction: KTUtil.isRTL(),
            startDate: start,
            endDate: end,
            opens: 'left',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end, '');

    });
</script>
