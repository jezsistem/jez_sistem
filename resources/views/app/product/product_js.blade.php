<script src="https://cdn.tiny.cloud/1/323apjbgqf1hr5qmcz0u8uwvl3oymnrypmtg98wfpvhw0khd/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js" ></script>
<script>
    function generateQR(value)
    {
        jQuery.noConflict();
        jQuery('#product_qr').empty();
        jQuery('#product_qr').css({
            'width' : 128,
            'height' : 128
        })
        jQuery('#product_qr').qrcode({width: 128,height: 128,text: value});
    }

    function activateColumn()
    {
        $('input').prop('disabled', false);
    }

    tinymce.init({
        selector: 'textarea',
        plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
        height : '150px'
    });

    $(document).on('focusin', function(e) {
        if ($(e.target).closest(".mce-window").length) {
            e.stopImmediatePropagation();
        }
    });

    $('.decimal').keypress(function(evt){
        return (/^[0-9]*\.?[0-9]*$/).test($(this).val()+evt.key);
    });

    function getProductSize(id)
    {
        var barcode = $('#product_size_barcode'+id).val();
        var sz_id = $('#product_size_barcode'+id).attr('data-id');
        var sz_barcode = sz_id+'-'+barcode;
        var current_sz_id = $('#_sz_id').val();
        var current_sz_barcode = $('#_sz_barcode').val();
        var price_tag = $('#product_size_price_tag'+id).val();
        var current_sz_price_tag = $('#_sz_price_tag').val();
        var sell_price = $('#product_size_sell_price'+id).val();
        var current_sz_sell_price = $('#_sz_sell_price').val();
        var purchase_price = $('#product_size_purchase_price'+id).val();
        var current_sz_purchase_price = $('#_sz_purchase_price').val();
        var mode = $('#_mode').val();
        if ($('#product_size'+id).is(':checked')) {
            if ($('#_sz_id').val() != '') {
                var current_sz_id = $('#_sz_id').val()+sz_id+'|';
            } else {
                var current_sz_id = sz_id+'|';
            }
            $('#_sz_id').val(current_sz_id);

            if (mode == 'edit') {
                $('#product_size_barcode'+id).show();
                $('#product_size_running_code'+id).show();
                //$('#product_size_barcode'+id).prop('required', true);
                if ($('#product_size_running_code'+id).val() != '') {
                    $('#product_size_barcode'+id).prop('disabled', false);
                } else {
                    $('#product_size_barcode'+id).prop('disabled', true);
                }
                if ($('#product_size_running_code'+id).val() != '') {
                    $('#product_size_purchase_price'+id).show();
                    $('#product_size_purchase_price'+id).prop('disabled', false);
                    $('#product_size_sell_price'+id).show();
                    $('#product_size_sell_price'+id).prop('disabled', false);
                    $('#product_size_price_tag'+id).show();
                    $('#product_size_price_tag'+id).prop('disabled', false);
                } else {
                    $('#product_size_purchase_price'+id).show();
                    $('#product_size_purchase_price'+id).prop('disabled', true);
                    $('#product_size_sell_price'+id).show();
                    $('#product_size_sell_price'+id).prop('disabled', true);
                    $('#product_size_price_tag'+id).show();
                    $('#product_size_price_tag'+id).prop('disabled', true);
                }
                $('#product_size_running_code'+id).prop('disabled', true);
            } else {
                $('#product_size_barcode'+id).show();
                //$('#product_size_barcode'+id).prop('required', true);
                $('#product_size_barcode'+id).prop('disabled', true);
                $('#product_size_barcode'+id).val('');
            }
        } else {
            $('#product_size_purchase_price'+id).hide();
            $('#product_size_sell_price'+id).hide();
            $('#product_size_price_tag'+id).hide();
            if (current_sz_id.indexOf(sz_id) > -1) {
                var new_current_sz_id = current_sz_id.replace(sz_id+'|', '');
                var new_sz_id = new_current_sz_id.replace('||', '|');
                $('#_sz_id').val(new_sz_id);
                if ($('#_sz_id').val() == '|') {
                    $('#_sz_id').val('');
                }
            }
            if (current_sz_barcode.indexOf(sz_barcode) > -1) {
                var new_current_sz_barcode = current_sz_barcode.replace(sz_barcode+'|', '');
                var new_sz_barcode = new_current_sz_barcode.replace('||', '|');
                $('#_sz_barcode').val(new_sz_barcode);
                if ($('#_sz_barcode').val() == '|') {
                    $('#_sz_barcode').val('');
                }
            }
            if (current_sz_sell_price.indexOf(sell_price) > -1) {
                var new_current_sz_sell_price = current_sz_sell_price.replace(sell_price+'|', '');
                var new_sz_sell_price = new_current_sz_sell_price.replace('||', '|');
                $('#_sz_sell_price').val(new_sz_sell_price);
                if ($('#_sz_sell_price').val() == '|') {
                    $('#_sz_sell_price').val('');
                }
            }
            if (current_sz_purchase_price.indexOf(purchase_price) > -1) {
                var new_current_sz_purchase_price = current_sz_purchase_price.replace(purchase_price+'|', '');
                var new_sz_purchase_price = new_current_sz_purchase_price.replace('||', '|');
                $('#_sz_purchase_price').val(new_sz_purchase_price);
                if ($('#_sz_purchase_price').val() == '|') {
                    $('#_sz_purchase_price').val('');
                }
            }
            if (mode == 'edit') {
                $('#product_size_barcode'+id).hide();
                $('#product_size_running_code'+id).hide();
                $('#product_size_barcode'+id).prop('required', false);
            } else {
                $('#product_size_barcode'+id).prop('required', false);
                $('#product_size_barcode'+id).val('');
                $('#product_size_barcode'+id).hide();
            }
        }
        //alert($('#_sz_id').val());
    }

    function getProductSizeBarcode(id)
    {
        var barcode = $('#product_size_barcode'+id).val();
        var running = $('#product_size_running_code'+id).val();
        var sz_id = $('#product_size_barcode'+id).attr('data-id');
        var sz_barcode = sz_id+'-'+barcode;
        if ($('#_sz_barcode').val() != '') {
            var current_sz_barcode = $('#_sz_barcode').val()+sz_barcode+'|';
        } else {
            var current_sz_barcode = sz_barcode+'|';
        }
        
        $('#_sz_barcode').val(current_sz_barcode);
        $('#product_size_barcode'+id).prop('disabled', true);
        //alert(sz_barcode+' ==== '+current_sz_barcode);
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_barcode:barcode},
            dataType: 'json',
            url: "{{ url('check_exists_barcode')}}",
            success: function(r) {
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    data: {_running:running, _barcode:barcode},
                    dataType: 'json',
                    url: "{{ url('update_barcode')}}",
                    success: function(r) {
                        if (r.status == '200') {
                            toast('Tersimpan', 'Barcode berhasil tersimpan', 'success');
                        }
                    }
                });
                // if (r.status == '200') {
                //     swal('Barcode', 'Barcode sudah ada dalam sistem, silahkan ganti dengan yang lain', 'warning');
                //     $('#product_size_barcode'+id).val('');
                //     $('#product_size_barcode'+id).prop('disabled', false);
                //     //alert(r.hasil);
                //     return false;
                // } else {
                //     $.ajaxSetup({
                //         headers: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //         }
                //     });
                //     $.ajax({
                //         type: "POST",
                //         data: {_running:running, _barcode:barcode},
                //         dataType: 'json',
                //         url: "{{ url('update_barcode')}}",
                //         success: function(r) {
                //             if (r.status == '200') {
                //                 toast('Tersimpan', 'Barcode berhasil tersimpan', 'success');
                //             }
                //         }
                //     });
                // }
            }
        });
    }

    function getProductPriceTag(id)
    {
        var price_tag = $('#product_size_price_tag'+id).val();
        var barcode = $('#product_size_running_code'+id).val();
        $.ajax({
            type: "POST",
            data: {_barcode:barcode, _price_tag:price_tag},
            dataType: 'json',
            url: "{{ url('update_price_tag')}}",
            success: function(r) {
                if (r.status == '200') {
                    toast('Diupdate', 'Harga banderol berhasil diupdate', 'success');
                }
            }
        });
    }

    function getProductSellPrice(id)
    {
        var sell_price = $('#product_size_sell_price'+id).val();
        var barcode = $('#product_size_running_code'+id).val();
        $.ajax({
            type: "POST",
            data: {_barcode:barcode, _sell_price:sell_price},
            dataType: 'json',
            url: "{{ url('update_sell_price')}}",
            success: function(r) {
                if (r.status == '200') {
                    toast('Diupdate', 'Harga jual berhasil diupdate', 'success');
                }
            }
        });
    }

    function updateSzIdFilterOptions(selectedValue) {
        try {
            fetchDataBasedOnSchemaId(selectedValue).then(function (newData) {
                $('#sz_id_filter').empty();
                $.each(newData, function (key, value) {
                    $('#sz_id_filter').append('<option value="' + value.value + '">' + value.text + '</option>');
                });
                $('#sz_id_filter').trigger('change');
            }).catch(function (error) {
                console.error(error);
            });
        } catch (error) {
            console.error(error);
        }
    }

    function fetchDataBasedOnSchemaId(schemaId) {
        return new Promise(function (resolve, reject) {
            var dataReturn = [];
            $.ajax({
                type: "GET",
                url: "{{ url('reload_size_schema')}}",
                data: { schema_id: schemaId },
                success: function (data) {
                    $.each(data, function (key, value) {
                        dataReturn.push({
                            text: value.sz_name,
                            value: value.id
                        });
                    });
                    resolve(dataReturn);
                },
                error: function (xhr, status, error) {
                    reject(error);
                }
            });
        });
    }

    function getProductPurchasePrice(id)
    {
        var purchase_price = $('#product_size_purchase_price'+id).val();
        var barcode = $('#product_size_running_code'+id).val();
        $.ajax({
            type: "POST",
            data: {_barcode:barcode, _purchase_price:purchase_price},
            dataType: 'json',
            url: "{{ url('update_purchase_price')}}",
            success: function(r) {
                if (r.status == '200') {
                    toast('Diupdate', 'Harga beli berhasil diupdate', 'success');
                }
            }
        })
    }


    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_table = $('#Producttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs',  "exportOptions": { orthogonal: 'export' } }
            ],
            ajax: {
                url : "{{ url('product_datatables') }}",
                data : function (d) {
                    d.search = $('#product_search').val();
                    d.pc_id = $('#pc_id_filter').val();
                    d.psc_id = $('#psc_id_filter').val();
                    d.pssc_id = $('#pssc_id_filter').val();
                    d.br_id_filter = $('#br_id_filter').val();
                    d.ps_id_filter = $('#ps_id_filter').val();
                    d.mc_id_filter = $('#mc_id_filter').val();
                    d.sz_id_filter = $('#sz_id_filter').val();
                    d.p_active_filter = $('#p_active_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pid', searchable: false},
            { data: 'article_id', name: 'article_id' },
            { data: 'p_name_show', name: 'p_name' },
            { data: 'p_color_show', name: 'p_color' },
            { data: 'br_name', name: 'br_name' },
            { data: 'ps_name_show', name: 'ps_name' },
            { data: 'p_price_tag_show', name: 'p_price_tag', render: function (data, type, row) {
                    return type === 'export' ?
                        data.replace( /[$,]/g, '' ) :
                        data;
                } },
            { data: 'p_purchase_price_show', name: 'p_purchase_price',render: function (data, type, row) {
                    return type === 'export' ?
                        data.replace( /[$,]/g, '' ) :
                        data;
                } },
            { data: 'p_sell_price_show', name: 'p_sell_price', render: function (data, type, row) {
                    return type === 'export' ?
                        data.replace( /[$,]/g, '' ) :
                        data;
                } },
            { data: 'p_active', name: 'p_active' },
            { data: 'p_detail', name: 'p_detail', sortable: false },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            },
            {
                targets: 9,
                render: function (data, type, row) {
                    if (row.p_active == '1') {
                        return '<span class="badge badge-success">Aktif</span>';
                    } else {
                        return '<span class="badge badge-danger">Tidak Aktif</span>';
                    }
                }
            }
            ],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [[0, 'desc']],
        });

        product_table.buttons().container().appendTo($('#product_excel_btn' ));
        $('#product_search').on('keyup', function() {
            product_table.draw();
        });

        $('#br_id_filter, #ps_id_filter, #mc_id_filter, #sz_id_filter, #p_active_filter, #pc_id_filter, #psc_id_filter, #pssc_id_filter').on('change', function() {
            product_table.draw();
        });

        {{--$('#pc_id').on('change', function() {--}}
        {{--    var label = $('#pc_id option:selected').text();--}}
        {{--    var pc_id = $('#pc_id').val();--}}
        {{--    if (label == 'Tampilkan Semua') {--}}
        {{--        // $('#product_display').fadeIn();--}}
        {{--        $('#product_category_selected_label').text('Tampilkan Semua');--}}
        {{--        $('#psc_id').html("<select class='form-control' id='psc_id' name='psc_id' required><option value=''>- Pilih Sub Kategori -</option></select>");--}}
        {{--        $('#pssc_id').html("<select class='form-control' id='pssc_id' name='pssc_id' required><option value=''>- Pilih Sub-Sub Kategori -</option></select>");--}}
        {{--        product_table.draw();--}}
        {{--        return false;--}}
        {{--    } else if (label != '- Pilih Kategori -' && label != 'Tampilkan Semua') {--}}
        {{--        $('#product_category_selected_label').text(label+' |');--}}
        {{--        $.ajax({--}}
        {{--            type: "GET",--}}
        {{--            data: {_pc_id:pc_id},--}}
        {{--            dataType: 'html',--}}
        {{--            url: "{{ url('reload_product_sub_category')}}",--}}
        {{--            success: function(r) {--}}
        {{--                $('#psc_id').html(r);--}}
        {{--            }--}}
        {{--        });--}}
        {{--    } else {--}}
        {{--        $('#product_category_selected_label').text('');--}}
        {{--        $('#psc_id').html("<select class='form-control' id='psc_id' name='psc_id' required><option value=''>- Pilih Sub Kategori -</option></select>");--}}
        {{--    }--}}
        {{--    $('#pssc_id').html("<select class='form-control' id='pssc_id' name='pssc_id' required><option value=''>- Pilih Sub-Sub Kategori -</option></select>");--}}
        {{--    $('#product_sub_category_selected_label').text('');--}}
        {{--    // $('#product_display').fadeOut();--}}
        {{--});--}}

        {{--$('#psc_id').on('change', function() {--}}
        {{--    var label = $('#psc_id option:selected').text();--}}
        {{--    var pc_id = $('#pc_id').val();--}}
        {{--    var psc_id = $('#psc_id').val();--}}
        {{--    if (label != '- Pilih Sub Kategori -' && label != '- Pilih -') {--}}
        {{--        $('#product_sub_category_selected_label').text(label+' |');--}}
        {{--        $.ajax({--}}
        {{--            type: "GET",--}}
        {{--            data: {_psc_id:psc_id},--}}
        {{--            dataType: 'html',--}}
        {{--            url: "{{ url('reload_product_sub_sub_category')}}",--}}
        {{--            success: function(r) {--}}
        {{--                $('#pssc_id').html(r);--}}
        {{--            }--}}
        {{--        });--}}
        {{--        $.ajax({--}}
        {{--            type: "GET",--}}
        {{--            data: {_psc_id:psc_id},--}}
        {{--            dataType: 'html',--}}
        {{--            url: "{{ url('reload_size')}}",--}}
        {{--            success: function(r) {--}}
        {{--                $('#reload_size').html(r);--}}
        {{--            }--}}
        {{--        });--}}
        {{--    } else {--}}
        {{--        $('#product_sub_category_selected_label').text('');--}}
        {{--        $('#pssc_id').html("<select class='form-control' id='pssc_id' name='pssc_id' required><option value=''>- Pilih Sub-Sub Kategori -</option></select>");--}}
        {{--        // $('#product_display').fadeOut();--}}
        {{--    }--}}
        {{--    // $('#product_display').fadeOut();--}}
        {{--});--}}

        // $('#pssc_id').on('change', function() {
        //     var label = $('#pssc_id option:selected').text();
        //     if (label != '- Pilih Sub-Sub Kategori -') {
        //         $('#product_sub_sub_category_selected_label').text(label);
        //         // $('#product_display').fadeIn();
        //         product_table.draw();
        //     } else {
        //         $('#product_sub_sub_category_selected_label').text('');
        //         // $('#product_display').fadeOut();
        //     }
        // });

        {{--$('#_pc_id').on('change', function() {--}}
        {{--    var pc_id = $('#_pc_id').val();--}}
        {{--    $('#_psc_id').prop('disabled', false);--}}
        {{--    $('#_psc_id').html("<select class='form-control' id='psc_id' name='psc_id' required><option value=''>- Pilih Sub Kategori -</option></select>");--}}
        {{--    $('#_pssc_id').html("<select class='form-control' id='pssc_id' name='pssc_id' required><option value=''>- Pilih Sub-Sub Kategori -</option></select>");--}}
        {{--    $.ajax({--}}
        {{--        type: "GET",--}}
        {{--        data: {_pc_id:pc_id},--}}
        {{--        dataType: 'html',--}}
        {{--        url: "{{ url('reload_product_sub_category')}}",--}}
        {{--        success: function(r) {--}}
        {{--            $('#_psc_id').html(r);--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}

        {{--$('#_psc_id').on('change', function() {--}}
        {{--    var psc_id = $('#_psc_id').val();--}}
        {{--    $('#_pssc_id').prop('disabled', false);--}}
        {{--    $('#_pssc_id').html("<select class='form-control' id='pssc_id' name='pssc_id' required><option value=''>- Pilih Sub-Sub Kategori -</option></select>");--}}
        {{--    $.ajax({--}}
        {{--        type: "GET",--}}
        {{--        data: {_psc_id:psc_id},--}}
        {{--        dataType: 'html',--}}
        {{--        url: "{{ url('reload_product_sub_sub_category')}}",--}}
        {{--        success: function(r) {--}}
        {{--            $('#_pssc_id').html(r);--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}

        $('#pc_id_filter').select2({
            width: "300px",
            dropdownParent: $('#pc_id_filter_parent')
        });

        $('#pc_id_filter').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#psc_id_filter').select2({
            width: "300px",
            dropdownParent: $('#psc_id_filter_parent')
        });

        $('#psc_id_filter').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pssc_id_filter').select2({
            width: "300px",
            dropdownParent: $('#pssc_id_filter_parent')
        });

        $('#pssc_id_filter').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#sz_schema_id').select2({
            width: "300px",
            dropdownParent: $('#sz_schema_id_parent')
        });

        $('#sz_schema_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#sz_schema_id').on('change', function (e) {
            // Get the selected value in sz_schema_id
            var selectedValue = $(this).val();

            updateSzIdFilterOptions(selectedValue);
        });

        $('#br_id_filter').select2({
            width: "130px",
            dropdownParent: $('#br_id_filter_parent')
        });
        $('#br_id_filter').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#ps_id_filter').select2({
            width: "130px",
            dropdownParent: $('#ps_id_filter_parent')
        });
        $('#ps_id_filter').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#sz_id_filter').select2({
            width: "130px",
            dropdownParent: $('#sz_id_filter_parent')
        });
        $('#sz_id_filter').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#p_active_filter').select2({
            width: "130px",
        });


        $('#mc_id_filter').select2({
            width: "130px",
            dropdownParent: $('#mc_id_filter_parent')
        });
        $('#mc_id_filter').on('select2:open', function (e) {
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

        $('#gn_id').select2({
            width: "100%",
            dropdownParent: $('#gn_id_parent')
        });
        $('#gn_id').on('select2:open', function (e) {
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

        $('#ps_id').select2({
            width: "100%",
            dropdownParent: $('#ps_id_parent')
        });
        $('#ps_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pu_id').select2({
            width: "100%",
            dropdownParent: $('#pu_id_parent')
        });
        $('#pu_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#mc_id').select2({
            width: "100%",
            dropdownParent: $('#mc_id_parent')
        });
        $('#mc_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#sz_schema_modal_id').select2({
            width: "100%",
            dropdownParent: $('#sz_schema_modal_id_parent')
        });
        $('#sz_schema_modal_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#import_modal_btn').on('click', function() {
            jQuery.noConflict();
            $('#ImportModal').modal('show');
            $('#f_import')[0].reset();
        });

        {{--$('#sz_schema_modal_id').on('change', function (e) {--}}
        {{--    // Get the selected value in sc_schema_modal_id--}}
        {{--    var selectedValue = $(this).val();--}}

        {{--    $.ajax({--}}
        {{--        type: "GET",--}}
        {{--        data: {_sz_schema:selectedValue},--}}
        {{--        dataType: 'html',--}}
        {{--        url: "{{ url('reload_size_schema_modal')}}",--}}
        {{--        success: function(r) {--}}
        {{--            $('#reload_size').html(r);--}}
        {{--            // checkSize(selectedValue);--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}

        function checkSize(id)
        {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_p_id:id},
                dataType: 'json',
                url: "{{ url('check_product_stock')}}",
                success: function(r) {
                    console.log("id checksize : " + id);
                    if (r.data != '400') {
                        var _sz_barcode_edit = '';
                        $.each($(r.data),function(key,value){
                            //alert(value.sz_id+' '+value.ps_barcode+' '+value.ps_running_code);
                            _sz_barcode_edit = _sz_barcode_edit+value.sz_id+'-'+value.ps_barcode+'|';
                            $('input[type="checkbox"][data-id="'+value.sz_id+'"]').prop('checked', true);
                            $('input[type="checkbox"][data-id="'+value.sz_id+'"]').prop('disabled', true);
                            $('input[type="text"][data-barcode="'+value.sz_id+'"]').show();
                            $('input[type="text"][data-barcode="'+value.sz_id+'"]').prop('disabled', true);
                            $('input[type="text"][data-barcode="'+value.sz_id+'"]').val(value.ps_barcode);
                            $('input[type="number"][data-purchase-price="'+value.sz_id+'"]').show();
                            $('input[type="number"][data-purchase-price="'+value.sz_id+'"]').val(value.ps_purchase_price);
                            $('input[type="number"][data-sell-price="'+value.sz_id+'"]').show();
                            $('input[type="number"][data-sell-price="'+value.sz_id+'"]').val(value.ps_sell_price);
                            $('input[type="number"][data-price-tag="'+value.sz_id+'"]').show();
                            $('input[type="number"][data-price-tag="'+value.sz_id+'"]').val(value.ps_price_tag);
                            if ($('input[type="text"][data-barcode="'+value.sz_id+'"]').val() == '') {
                                $('input[type="text"][data-barcode="'+value.sz_id+'"]').prop('disabled', false);
                            } else {
                                $('input[type="text"][data-barcode="'+value.sz_id+'"]').prop('disabled', true);
                            }
                            $('input[type="number"][data-running-code="'+value.sz_id+'"]').show();
                            $('input[type="number"][data-running-code="'+value.sz_id+'"]').prop('disabled', true);
                            $('input[type="number"][data-running-code="'+value.sz_id+'"]').val(value.ps_running_code);
                        });
                        //alert(_sz_barcode_edit);
                        $('#_sz_barcode_edit').val(_sz_barcode_edit);
                    }
                }
            });
        }

        function handleSchemaChange(selectedValue, id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "GET",
                data: {_sz_schema: selectedValue, _id: id}, // Pass id as data parameter
                dataType: 'html',
                url: "{{ url('reload_size_schema_modal')}}",
                success: function(r) {
                    $('#reload_size').html(r);
                    console.log('schema id : ' + id); // Now id should be accessible here
                    checkSize(id);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        $('#Producttb tbody').on('click', 'tr td:not(:nth-child(11))', function () {
            jQuery('#product_qr').empty();
            $('#f_product')[0].reset();
            $('#_sz_barcode').val('');
            $('#_sz_id').val('');
            $('#_sz_sell_price').val('');
            $('input[type="checkbox"]').prop('disabled', false);
            $('.psb_hidden').hide();
            $('#_pc_id').prop('disabled', false);
            var product_label = $('#product_category_component').text();
            var id = product_table.row(this).data().pid;
            var p_name = product_table.row(this).data().p_name;
            var article_id = product_table.row(this).data().article_id;
            var p_description = product_table.row(this).data().p_description;
            var p_aging = product_table.row(this).data().p_aging;
            var p_color = product_table.row(this).data().p_color;
            var p_price_tag = product_table.row(this).data().p_price_tag;
            var p_purchase_price = product_table.row(this).data().p_purchase_price;
            var p_sell_price = product_table.row(this).data().p_sell_price;
            var p_weight = product_table.row(this).data().p_weight;
            var pc_id = product_table.row(this).data().pc_id;
            var psc_id = product_table.row(this).data().psc_id;
            var pssc_id = product_table.row(this).data().pssc_id;
            var br_id = product_table.row(this).data().br_id;
            var ps_id = product_table.row(this).data().ps_id;
            var pu_id = product_table.row(this).data().pu_id;
            var gn_id = product_table.row(this).data().gn_id;
            var ss_id = product_table.row(this).data().ss_id;
            var mc_id = product_table.row(this).data().mc_id;
            var schema_size = product_table.row(this).data().schema_size;
            var check_pc_id = $('#pc_id').val();
            if (check_pc_id == 'all') {
                $('#category_arrow_label').hide();
                {{--$.ajax({--}}
                {{--    type: "GET",--}}
                {{--    data: {_psc_id:psc_id},--}}
                {{--    dataType: 'html',--}}
                {{--    url: "{{ url('reload_size_schema_modal')}}",--}}
                {{--    success: function(r) {--}}
                {{--        $('#reload_size').html(r);--}}
                {{--        checkSize(id);--}}
                {{--    }--}}
                {{--});--}}
            }
            else {
                $('#category_arrow_label').show();
                // console.log('id : '+id);
                // checkSize(id);
            }

            if ($.trim(p_description) != '') {
                tinyMCE.get('p_description').setContent(p_description);
            }

            $('#_pc_id').val(pc_id);
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('psc_reload')}}",
                success: function(r) {
                    $('#_psc_id').html(r);
                    $('#_psc_id').val(psc_id);
                    // $('#_psc_id').prop('disabled', true);
                }
            });
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('pssc_reload')}}",
                success: function(r) {
                    $('#_pssc_id').html(r);
                    $('#_pssc_id').val(pssc_id);
                    // $('#_pssc_id').prop('disabled', true);
                }
            });
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_p_id:id},
                dataType: 'json',
                url: "{{ url('check_product_po')}}",
                success: function(r) {
                    if (r.status == '200') {
                        $('#_pc_id').prop('disabled', true);
                    }
                }
            });



            // $('#sz_schema_modal_id').on('change', function (e) {
            //     // Get the selected value in sc_schema_modal_id
            //     var selectedValue = $('#sz_schema_modal_id').val();
            //     handleSchemaChange(selectedValue, article_id);
            // });
            handleSchemaChange(schema_size, id);
            jQuery.noConflict();
            $('#ProductModal').modal('show');
            $('#product_label_modal').text(product_label);
            $('#pcpscpssc_edit').show();
            $('#barcode_running_label').show();
            $('#p_name').val(p_name);
            $('#article_id').val(article_id);
            $('#p_aging').val(p_aging);
            $('#p_color').val(p_color);
            $('#p_price_tag').val(p_price_tag);
            $('#p_purchase_price').val(p_purchase_price);
            $('#p_sell_price').val(p_sell_price);
            $('#p_weight').val(p_weight);
            $('#_current_pc_id').val(pc_id);
            $('#_current_psc_id').val(psc_id);
            $('#_current_pssc_id').val(pssc_id);
            jQuery('#br_id').val(br_id).trigger('change');
            jQuery('#ps_id').val(ps_id).trigger('change');
            jQuery('#pu_id').val(pu_id).trigger('change');
            jQuery('#gn_id').val(gn_id).trigger('change');
            jQuery('#ss_id').val(ss_id).trigger('change');
            jQuery('#mc_id').val(mc_id).trigger('change');
            jQuery('#sz_schema_modal_id').val(schema_size).trigger('change');
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
            $('#delete_product_btn').show();
            @endif
            generateQR(article_id);
        });

        $('#article_id').on('change', function() {
            var article_id = $(this).val();

            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_article_id:article_id},
                dataType: 'json',
                url: "{{ url('check_exists_article_id')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Kode', 'Article ID sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#article_id').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_product_btn').on('click', function(e) {
            e.preventDefault();
            jQuery('#product_qr').empty();
            var check_pc_id = $('#pc_id').val();
            if (check_pc_id == 'all') {
                swal('Kategori', 'Silahkan tentukan kategori, sub kategori dan sub-sub kategori terlebih dahulu untuk menambah data', 'warning');
                return false;
            }
            jQuery.noConflict();
            $('#ProductModal').modal('show');
            $('#category_arrow_label').show();
            $('#pcpscpssc_edit').show();
            var product_label = $('#product_category_component').text();
            $('#product_label_modal').text(product_label);
            $('input[type="checkbox"]').prop('disabled', false);
            $('input[type="checkbox"]').prop('disabled', false);
            $('#_id').val('');
            $('#_mode').val('add');
            $('#_sz_barcode').val('');
            $('#_sz_id').val('');
            $('#_sz_sell_price').val('');
            $('#f_product')[0].reset();
            $('.psb_hidden').hide();
            $('#barcode_running_label').hide();
            $('#delete_product_btn').hide();
            jQuery('#br_id').val('').trigger('change');
            jQuery('#ps_id').val('').trigger('change');
            jQuery('#pu_id').val('').trigger('change');
            jQuery('#gn_id').val('').trigger('change');
            jQuery('#ss_id').val('').trigger('change');
            jQuery('#mc_id').val('').trigger('change');
            jQuery('#sz_schema_modal_id').val('').trigger('change');
            $('#_pc_id').prop('required', false);
            $('#_psc_id').prop('required', false);
            $('#_pssc_id').prop('required', false);
        });

        $(document).delegate('#product_detail_btn', 'click', function() {
            var id = $(this).attr('data-id');
            jQuery.noConflict();
            $('#ProductDetailModal').modal('show');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_id:id},
                dataType: 'html',
                url: "{{ url('product_detail')}}",
                success: function(r) {
                    $('#productDetailContent').html(r);
                    $.ajax({
                        type: "POST",
                        data: {_p_id:id},
                        dataType: 'json',
                        url: "{{ url('check_product_stock')}}",
                        success: function(r) {
                            if (r.data != '400') {
                                $.each($(r.data),function(key,value){
                                    $('#ProductStockDetailtb tr:last').after("<tr id='ProductStockDetailAppend'><td>"+value.sz_name+"</td><td>"+value.ps_qty+"</td><td>"+value.ps_barcode+"</td><td>"+value.ps_running_code+"</td></tr>");
                                });
                            } else {
                                $('#ProductStockDetailAppend').remove();
                            }
                        }
                    });
                }
            });
        });

        $('#close_import_btn').on('click', function() {
            $("#import_data_btn").html('Import');
            $("#import_data_btn").attr("disabled", false);
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('p_import')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();
                    console.log(data.status)
                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        swal('Berhasil', 'Data berhasil diimport', 'success');
                        $('#f_import')[0].reset();
                        product_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        swal('File', 'File yang anda import kosong atau format tidak tepat', 'warning');
                    } else {

                        if(data.same_article_id.length > 0) {
                            var same_article_id = [];
                            for (var i = 0; i < data.same_article_id.length; i++) {
                               same_article_id.push(data.same_article_id[i]);
                                var same_article_id_string = same_article_id.join(', ');
                            }
                            swal('Gagal', 'Article ID ' + same_article_id_string + ' sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                            return false;
                        }

                        $("#ImportModal").modal('hide');
                        swal('Gagal', 'Silahkan periksa format input pada template anda, pastikan kolom biru terisi sesuai dengan sistem', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_product').on('submit', function(e) {
            e.preventDefault();
            // alert($('#_sz_barcode').val());
            // alert($('#_sz_sell_price').val());
            // return false;
            $("#save_product_btn").html('Proses ..');
            $("#save_product_btn").attr("disabled", true);
            var formData = new FormData(this);
            var check_pc_id = $('#pc_id').val();
            if (check_pc_id == 'all') {
                if ($('#_pc_id').val() == '') {
                    swal('Kategori', 'Silahkan tentukan kategori terlebih dahulu', 'warning');
                    return false;
                } else if ($('#_psc_id').val() == '') {
                    swal('Sub Kategori', 'Silahkan tentukan sub kategori terlebih dahulu', 'warning');
                    return false;
                } else if ($('#_pssc_id').val() == '') {
                    swal('Sub-Sub Kategori', 'Silahkan tentukan sub-sub kategori terlebih dahulu', 'warning');
                    return false;
                }
                // formData.append('pc_id', $('#_pc_id').val());
                // formData.append('psc_id', $('#_psc_id').val());
                // formData.append('pssc_id', $('#_pssc_id').val());
            } else {
                // formData.append('pc_id', $('#pc_id').val());
                // formData.append('psc_id', $('#psc_id').val());
                // formData.append('pssc_id', $('#pssc_id').val());

                formData.append('pc_id', $('#_pc_id').val());
                formData.append('psc_id', $('#_psc_id').val());
                formData.append('pssc_id', $('#_pssc_id').val());

            }
            $.ajax({
                type:'POST',
                url: "{{ url('p_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    //swal('Result',data.result,'success');
                    $("#save_product_btn").html('Simpan');
                    $("#save_product_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ProductModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        product_table.draw(false);
                    } else if (data.status == '400') {
                        $("#ProductModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    } else {
                        $("#ProductModal").modal('hide');
                        swal('Relationship', 'Data gagal disimpan karena ada perubahan data yang terikat ke suatu pencatatan transaksi', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_product_btn').on('click', function(){
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
                        url: "{{ url('p_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#ProductModal').modal('hide');
                                product_table.ajax.reload();
                            } else {

                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });
        
        var product = $('#Ptb').DataTable({
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
                    d.search = $('#p_search').val();
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

        $('#p_search').on('keyup', function() {
            product.draw(false);
        });

        $(document).delegate('#product_barcode_btn', 'click', function(e) {
            e.preventDefault();
            jQuery.noConflict();
            $('#ProductBarcodeModal').modal('show');
            product.draw(false);
        });

        $(document).delegate('#input_barcode', 'change', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var barcode = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
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

    });
</script>