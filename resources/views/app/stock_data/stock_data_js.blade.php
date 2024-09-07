<script>



    function reloadCategory(type, id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_type: type, _id: id},
            dataType: 'html',
            url: "{{ url('stock_data_reload_category')}}",
            success: function (r) {
                $('#pc_id').html(r);
            }
        });
        return false;
    }

    function reloadSubCategory(type, id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_type: type, _id: id},
            dataType: 'html',
            url: "{{ url('stock_data_reload_sub_category')}}",
            success: function (r) {
                $('#psc_id').html(r);
            }
        });
        return false;
    }

    function reloadSubOnSubCategory(type, id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_type: type, _id: id},
            dataType: 'html',
            url: "{{ url('stock_data_reload_sub_sub_category')}}",
            success: function (r) {
                $('#pssc_id').html(r);
            }
        });
        return false;
    }

    function reloadBrand(type, id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_type: type, _id: id},
            dataType: 'html',
            url: "{{ url('stock_data_reload_brand')}}",
            success: function (r) {
                $('#br_id').html(r);
            }
        });
        return false;
    }

    function reloadSize(type, id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_type: type, _id: id},
            dataType: 'html',
            url: "{{ url('stock_data_reload_size')}}",
            success: function (r) {
                $('#sz_id').html(r);
            }
        });
        return false;
    }

    function reloadGender(type, id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_type: type, _id: id},
            dataType: 'html',
            url: "{{ url('stock_data_reload_size')}}",
            success: function (r) {
                $('#sz_id').html(r);
            }
        });
        return false;
    }

    function reloadMainColor(type, id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_type: type, _id: id},
            dataType: 'html',
            url: "{{ url('stock_data_reload_size')}}",
            success: function (r) {
                $('#sz_id').html(r);
            }
        });
        return false;
    }

    $(document).ready(function () {
        $('#br_id, #pc_id, #psc_id, #pssc_id, #sz_id, #gender_id, #main_color_id').val('');
        // $('body').addClass('kt-primary--minimize aside-minimize');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // if ($('stock_data_search').val() != '') {
            var stock_data_table = $('#StockDatatb').DataTable({
                destroy: true,
                processing: false,
                serverSide: true,
                responsive: false,
                dom: 'rt<"text-right"ipl>',
                buttons: [
                    {"extend": 'excelHtml5', "text": 'Excel', "className": 'btn btn-primary btn-xs'}
                ],
                ajax: {
                    url: "{{ url('stock_data_datatables') }}",
                    data: function (d) {
                        d.search = $('#stock_data_search').val();
                        d.search_scan = $('#stock_data_search_scan').val();
                        d.br_id = $('#br_id').val();
                        d.pc_id = $('#pc_id').val();
                        d.psc_id = $('#psc_id').val();
                        d.pssc_id = $('#pssc_id').val();
                        d.sz_id = $('#sz_id').val();
                        d.gender_id = $('#gender_id').val();
                        d.main_color_id = $('#main_color_id').val();
                        d.display_status = $('#display_status').val();
                        d.st_id = $('#st_id_filter').val();
                    }
                },
                columns: [
                    {data: 'article_name', name: 'article_name', orderable: false},
                    {data: 'article_stock', name: 'article_stock', orderable: false},
                ],
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-left",
                        "width": "0%"
                    }],
                rowCallback: function (row, data, index) {
                    if (data.article_stock.indexOf("<table></table>") >= 0) {
                        $(row).hide();
                    }
                },
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                language: {
                    "lengthMenu": "_MENU_",
                },
                order: [[0, 'desc']],
            });
            var oSettings = stock_data_table.settings();
        // }

        const scanner = new Html5QrcodeScanner('reader', {
            // Scanner will be initialized in DOM inside element with id of 'reader'
            qrbox: {
                width: 250,
                height: 250,
            },  // Sets dimensions of scanning box (set relative to reader element width)
            fps: 20, // Frames per second to attempt a scan
        });


        scanner.render(success, error);

        function success(result) {

            var hasil = result;

            if (hasil.startsWith(']C1')) {
                hasil = hasil.replace(']C1', '');
            }

            $('#stock_data_search').val(hasil);
            stock_data_table.draw();

            // let today = new Date().toISOString().slice(0, 10)
            //
            // const date_obj = new Date();
            //
            // time = new Date().toLocaleTimeString();
            //
            // document.getElementById('result').innerHTML = `
            //
            // <div class="card" style="width: 18rem; onload="console_log(${result});">
            //
            // <img src="barcode-scan.gif" class="card-img-top" alt="...">
            //
            // <div class="card-body">
            //
            // <form action="validator.php" method="post">
            //
            // <p style="font-size: 14px;" class="card-text">Bar Code Read Successfully : <span class="badge bg-primary">${result}</span></p>
            //
            // <p style="font-size: 14px;" class="card-text">Date : <span class="badge bg-primary">${today}</span></p>
            //
            // <p style="font-size: 14px;" class="card-text">Capture Time : <span class="badge bg-primary">${time}</span></p>
            //
            // <input type="hidden" name="number_index" value="${result}" id="result">
            //
            // <input type="hidden" name="time_val" value="${time}" id="capture_time">
            //
            // <input type="hidden" name="date_val" value="${today}" id="capture_date">
            //
            // <button type="submit" name="submit" class="btn btn-outline-primary btn-sm">Validation</button>
            //
            // </form>
            //
            // </div>
            //
            // </div>
            // `;
            //
            // // Prints result as a link inside result element
            //
            // scanner.clear();
            // // Clears scanning instance

            // document.getElementById('reader').remove();
            // Removes reader element from DOM since no longer needed

        }

        function error(err) {
            console.error(err);
            // Prints any errors to the console
        }

        function console_log(result)
        {
            console.log(result);
        }
        {{--var aging_table = $('#Agingtb').DataTable({--}}
        {{--    destroy: true,--}}
        {{--    processing: true,--}}
        {{--    serverSide: true,--}}
        {{--    responsive: false,--}}
        {{--    dom: 'B<"text-right"l>rt<"text-right"ip>',--}}
        {{--    buttons: [--}}
        {{--        { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }--}}
        {{--    ],--}}
        {{--    ajax: {--}}
        {{--        url : "{{ url('aging_datatables') }}",--}}
        {{--        data : function (d) {--}}
        {{--            d.search = $('#aging_search').val();--}}
        {{--            d.br_id = $('#br_id').val();--}}
        {{--            d.pc_id = $('#pc_id').val();--}}
        {{--            d.psc_id = $('#psc_id').val();--}}
        {{--            d.pssc_id = $('#pssc_id').val();--}}
        {{--            d.sz_id = $('#sz_id').val();--}}
        {{--            d.st_id = $('#st_id_filter').val();--}}
        {{--        }--}}
        {{--    },--}}
        {{--    columns: [--}}
        {{--    { data: 'DT_RowIndex', name: 'pst_id', searchable: false},--}}
        {{--    { data: 'st_name', name: 'st_name'},--}}
        {{--    { data: 'aging_po', name: 'aging_po', orderable: false},--}}
        {{--    { data: 'aging_tf', name: 'aging_tf', orderable: false},--}}
        {{--    { data: 'pc_name', name: 'pc_name'},--}}
        {{--    { data: 'psc_name', name: 'psc_name'},--}}
        {{--    { data: 'pssc_name', name: 'pssc_name'},--}}
        {{--    { data: 'br_name', name: 'br_name'},--}}
        {{--    { data: 'p_name', name: 'p_name'},--}}
        {{--    { data: 'p_color', name: 'p_color'},--}}
        {{--    { data: 'sz_name', name: 'sz_name'},--}}

        {{--    { data: 'stock', name: 'aging', orderable: false},--}}
        {{--    ],--}}
        {{--    columnDefs: [--}}
        {{--    {--}}
        {{--        "targets": 0,--}}
        {{--        "className": "text-center",--}}
        {{--        "width": "0%"--}}
        {{--    }],--}}
        {{--    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],--}}
        {{--    language: {--}}
        {{--        "lengthMenu": "_MENU_",--}}
        {{--    },--}}
        {{--    order: [[0, 'desc']],--}}
        {{--});--}}

        var pickup_list_table = $('#PickupListtb').DataTable({
            destroy: true,
            processing: false,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            ajax: {
                url: "{{ url('pickup_list_datatables') }}",
                data: function (d) {
                    d.search = $('#pick_data_search').val();
                    d.st_id = "{{ $data['user']->st_id }}";
                }
            },
            columns: [
                {data: 'article', name: 'p_name', orderable: false},
                {data: 'bin', name: 'pl_code, orderable: false'},
                {data: 'datetime', name: 'plst_created', orderable: false},
                {data: 'user', name: 'user', orderable: false},
                {data: 'status', name: 'p_name', orderable: false},
                {data: 'action', name: 'p_name', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-left",
                    "width": "0%"
                }],
            order: [[0, 'desc']],
        });

        function debounce(func, delay) {
            let timeoutId;
            return function (...args) {
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                }, delay);
            };
        }

        stock_data_table.buttons().container().appendTo($('#stock_data_excel_btn'));
        // $(document).ready(function () {
        //     $('#stock_data_search').on('keyup', debounce(function () {
        //         var query = $(this).val();
        //         if ($.trim(query).length > 4 || $.trim(query).length != 0) {
        //             stock_data_table.draw();
        //         }
        //     }, 400));
        // });

        $(document).ready(function () {
            // Block the enter key in the input field
            $('#stock_data_search').on('keydown', function (e) {
                if (e.key === "Enter") {
                    e.preventDefault(); 
                }
            });

            // Debounce function for search
            $('#stock_data_search').on('keyup', debounce(function () {
                var query = $(this).val();
                if ($.trim(query).length > 4 || $.trim(query).length != 0) {
                    stock_data_table.draw();
                }
            }, 400));
        });

        $('#pick_data_search').on('keyup', function () {
            pickup_list_table.draw();
        });

        // $('#aging_search').on('keyup', function() {
        //     aging_table.draw();
        // });

        // $('#aging_btn').on('click', function() {
        //     $('#AgingModal').modal('show');
        //     aging_table.draw();
        // });

        // $('#st_id_filter_aging').on('change', function() {
        //     aging_table.draw();
        // });

        $(document).delegate('#aging_detail', 'click', function (e) {
            // swal($(this).attr('title'));
            alert($(this).attr('title'));
        });

        $(document).delegate('#copy-button', 'click', function (e) {
            // swal($(this).attr('title'));
            var textToCopy = $(this).attr('title');

            // Create a temporary textarea element
            var tempTextarea = $('<textarea>');
            $('body').append(tempTextarea);
            tempTextarea.val(textToCopy).select();

            // Copy the text inside the textarea
            document.execCommand('copy');

            // Remove the temporary textarea
            tempTextarea.remove();

            // Optional: Provide feedback to the user
            swal({
                title: "Copied!",
                text: "Text copied to clipboard: " + textToCopy,
                icon: "success",
            });

        });

        $(document).delegate('#pickup_item', 'click', function (e) {
            e.preventDefault();
            var st_id = $('#st_id_filter').val();

            var pst_id = $(this).attr('data-pst_id');
            var pl_id = $(this).attr('data-pl_id');
            var qty = $(this).attr('data-qty');
            var pls_id = $(this).attr('data-pls_id');
            var p_name = $(this).attr('data-p_name');
            var pl_code = $(this).attr('data-pl_code');
            var bin = $(this).attr('data-bin');


            @if (strtolower($data['user']->stt_name) == 'offline' || strtolower($data['user']->stt_name) == 'online')
            {{--if (st_id == {{ $data['user']->st_id }}) {--}}
            {{--    swal({--}}
            {{--        title: "Pickup..?",--}}
            {{--        text: "Yakin pickup item " + p_name + " dari bin " + bin + " ?",--}}
            {{--        icon: "warning",--}}
            {{--        buttons: [--}}
            {{--            'Batal',--}}
            {{--            'Yakin'--}}
            {{--        ],--}}
            {{--        dangerMode: false,--}}
            {{--    }).then(function (isConfirm) {--}}
            {{--        if (isConfirm) {--}}
            {{--            $.ajaxSetup({--}}
            {{--                headers: {--}}
            {{--                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
            {{--                }--}}
            {{--            });--}}
            {{--            $.ajax({--}}
            {{--                type: "POST",--}}
            {{--                data: {_pls_id: pls_id, _pst_id: pst_id, _pl_id: pl_id, _pl_code: pl_code},--}}
            {{--                dataType: 'json',--}}
            {{--                url: "{{ url('pickup_item')}}",--}}
            {{--                success: function (r) {--}}
            {{--                    if (r.status == '200') {--}}
            {{--                        toast("Berhasil", "Item berhasil dipickup", "success");--}}
            {{--                        stock_data_table.draw();--}}
            {{--                        pickup_list_table.draw();--}}
            {{--                    } else {--}}
            {{--                        toast('Gagal', 'Gagal pickup item', 'error');--}}
            {{--                    }--}}
            {{--                }--}}
            {{--            });--}}
            {{--            return false;--}}
            {{--        }--}}
            {{--    })--}}
            {{--}--}}
            if (st_id == {{ $data['user']->st_id }}) {
                // Fetch the articles_promo data first
                let article_id = $(this).data('p_article');
                let p_name = $(this).data('p_name');
                let bin = $(this).data('bin');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "GET",
                    url: "{{ url('get_articles_promo') }}/" + article_id, // Send article_id to your controller route
                    success: function (response) {
                        // Prepare the content for swal based on response data
                        let promoInfo = '';
                        let promoPrice = '';

                        if (response.data && response.data.length > 0) {
                            promoInfo = '';
                            promoPrice =  '';
                            response.data.forEach(function(promo) {
                                promoInfo += 'Promo: ' + promo.promo_note + '';
                                promoPrice +=  'Discount Price: ' + promo.promo_price + '';
                            });
                            promoInfo += '';
                            promoPrice += '';
                        } else {
                            promoInfo = 'No promo available for this article.';
                            promoPrice = 'Harga Normal guys!'
                        }

                        // Now show the swal with the promo data
                        swal({
                            title: "Pickup..?",
                            text: "Yakin pickup item " + p_name + " dari bin " + bin + " ?\n\nPromotions:\n" + promoInfo + "\n" + promoPrice,
                            icon: "warning",
                            buttons: [
                                'Batal',
                                'Yakin'
                            ],
                            dangerMode: false,
                        }).then(function (isConfirm) {
                            if (isConfirm) {
                                // Proceed with the existing POST request to pickup the item
                                $.ajax({
                                    type: "POST",
                                    data: {_pls_id: pls_id, _pst_id: pst_id, _pl_id: pl_id, _pl_code: pl_code},
                                    dataType: 'json',
                                    url: "{{ url('pickup_item')}}",
                                    success: function (r) {
                                        if (r.status == '200') {
                                            toast("Berhasil", "Item berhasil dipickup", "success");
                                            stock_data_table.draw();
                                            pickup_list_table.draw();
                                        } else {
                                            toast('Gagal', 'Gagal pickup item', 'error');
                                        }
                                    }
                                });
                                return false;
                            }
                        });
                    },
                    error: function() {
                        // Handle error
                        swal('Error', 'Failed to fetch promo data', 'error');
                    }
                });
            }
            else {
                swal({
                    title: "Pickup..?",
                    text: "Yakin pickup item " + p_name + " dari bin " + bin + " ?",
                    icon: "warning",
                    buttons: [
                        'Batal',
                        'Yakin'
                    ],
                    dangerMode: false,
                }).then(function (isConfirm) {
                    if (isConfirm) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "POST",
                            data: {_pls_id: pls_id, _pst_id: pst_id, _pl_id: pl_id, _pl_code: pl_code},
                            dataType: 'json',
                            url: "{{ url('pickup_item')}}",
                            success: function (r) {
                                if (r.status == '200') {
                                    toast("Berhasil", "Item berhasil dipickup", "success");
                                    stock_data_table.draw();
                                    pickup_list_table.draw();
                                } else {
                                    toast('Gagal', 'Gagal pickup item', 'error');
                                }
                            }
                        });
                        return false;
                    }
                })
            }
            @endif
        });

        $(document).delegate('#pickup_approval_item', 'click', function (e) {
            e.preventDefault();
            var st_id = $('#st_id_filter').val();
            var plst_id = $(this).attr('data-plst_id');
            var p_name = $(this).attr('data-p_name');
            var bin = $(this).attr('data-bin');
            @if (strtolower($data['user']->stt_name) == 'offline' AND strtolower($data['user']->g_name) == 'sales')
            if (st_id == {{ $data['user']->st_id }}) {
                swal({
                    title: "Pickup..?",
                    text: "Yakin pickup item " + p_name + " dari bin " + bin + " ?",
                    icon: "warning",
                    buttons: [
                        'Batal',
                        'Yakin'
                    ],
                    dangerMode: false,
                }).then(function (isConfirm) {
                    if (isConfirm) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "POST",
                            data: {plst_id: plst_id},
                            dataType: 'json',
                            url: "{{ url('pickup_approval_item')}}",
                            success: function (r) {
                                if (r.status == '200') {
                                    stock_data_table.draw();
                                    pickup_list_table.draw();
                                    toast("Berhasil", "Item berhasil dipickup", "success");
                                } else {
                                    toast('Gagal', 'Gagal pickup item', 'error');
                                }
                            }
                        });
                        return false;
                    }
                })
            }
            @endif
        });

        jQuery.noConflict();

        $('#reset_btn').on('click', function () {
            $("#br_id").val("");
            $("#br_id").trigger('change');
            $("#pc_id").val("");
            $("#pc_id").trigger('change');
            $("#psc_id").val("");
            $("#psc_id").trigger('change');
            $("#pssc_id").val("");
            $("#pssc_id").trigger('change');
            $("#sz_id").val("");
            $("#sz_id").trigger('change');
            $("#main_color_id").val("");
            $("#main_color_id").trigger('change');
            $("#gender_id").val("");
            $("#gender_id").trigger('change');
            $("#stock_data_search").val("");
            $('#display_status').val('');
            oSettings[0]._iDisplayLength = 10;
            // stock_data_table.draw();
        });

        $('#br_id, #pc_id, #psc_id, #pssc_id, #sz_id, #gender_id, #main_color_id').on('change', function () {
            stock_data_table.draw();
        });

        $('#br_id').select2({
            multiple: true,
            width: "100%",
            dropdownParent: $('#br_id_parent'),
            closeOnSelect: true,
            placeholder: "BRANDS",
            allowClear: true,
        });
        $('#br_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pc_id').select2({
            multiple: true,
            width: "100%",
            dropdownParent: $('#pc_id_parent'),
            closeOnSelect: true,
            placeholder: "KATEGORI",
            allowClear: true,
        });
        $('#pc_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#psc_id').select2({
            multiple: true,
            width: "100%",
            dropdownParent: $('#psc_id_parent'),
            closeOnSelect: true,
            placeholder: "SUB KATEGORI",
            allowClear: true,
        });
        $('#psc_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pssc_id').select2({
            multiple: true,
            width: "100%",
            dropdownParent: $('#pssc_id_parent'),
            closeOnSelect: true,
            placeholder: "SUB ON SUB KATEGORI",
            allowClear: true,
        });
        $('#pssc_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#sz_id').select2({
            multiple: true,
            width: "100%",
            dropdownParent: $('#sz_id_parent'),
            closeOnSelect: true,
            placeholder: "SIZE / UKURAN",
            allowClear: true,
        });
        $('#sz_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#gender_id').select2({
            multiple: true,
            width: "100%",
            dropdownParent: $('#gender_id_parent'),
            closeOnSelect: true,
            placeholder: "GENDER",
            allowClear: true,
        });
        $('#gender_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#main_color_id').select2({
            multiple: true,
            width: "100%",
            dropdownParent: $('#main_color_id_parent'),
            closeOnSelect: true,
            placeholder: "MAIN COLOR",
            allowClear: true,
        });
        $('#main_color_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pc_id').on('change', function () {
            reloadSubCategory('product_category', $(this).val());
        });

        $('#psc_id').on('change', function () {
            reloadSubOnSubCategory('product_sub_category', $(this).val());
        });

        $('#br_id').on('change', function () {
            reloadCategory('brand', $(this).val());
        });

        $('#pickup_list_btn').on('click', function () {
            jQuery.noConflict();
            $('#PickupListModal').on('show.bs.modal', function () {
                pickup_list_table.draw();
            }).modal('show');
        });

        $(document).delegate('#cancel_pickup_btn', 'click', function () {
            var plst_id = $(this).attr('data-plst_id');
            var pls_id = $(this).attr('data-pls_id');
            var pst_id = $(this).attr('data-pst_id');
            var pl_id = $(this).attr('data-pl_id');
            var p_name = $(this).attr('data-p_name');
            var pl_code = $(this).attr('data-pl_code');
            swal({
                title: "Batal..?",
                text: "Yakin batalkan pickup untuk item " + p_name + " ?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: false,
            }).then(function (isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {_plst_id: plst_id, _pls_id: pls_id, _pst_id: pst_id, _pl_id: pl_id, _pl_code: pl_code},
                        dataType: 'json',
                        url: "{{ url('cancel_pickup_item')}}",
                        success: function (r) {
                            if (r.status == '200') {
                                toast("Berhasil", "Pickup item berhasil dibatalkan", "success");
                                pickup_list_table.draw();
                                stock_data_table.draw();
                            } else {
                                toast('Gagal', 'Gagal batalkan pickup', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#st_id_filter').on('change', function () {
            stock_data_table.draw();
        });


        var scanMode = false;
        $('#scan_mode_btn').click(function () {

            scanMode = !scanMode;

            if (scanMode) {
                $('#scan_mode_btn').html('<i class="fa fa-barcode"></i> Manual Mode');

                $('#stock_data_search_scan').toggle();
                $('#stock_data_search_scan').focus();
                $('#stock_data_search').toggle();
            } else {
                $('#scan_mode_btn').html('<i class="fa fa-barcode"></i> Scan Mode');
                $('#stock_data_search_scan').toggle();
                $('#stock_data_search').toggle();
            }

        });

        // $('#stock_data_search_scan').on('keyup', function(event) {
        //     if(event.keyCode === 13) {
        //         var query = jQuery(this).val();
        //         stock_data_table.draw();
        //     }
        // });

        let debounceTimeout;

        // $('#stock_data_search_scan').on('keyup', function(event) {
        //     clearTimeout(debounceTimeout);
        //
        //     debounceTimeout = setTimeout(function() {
        //         if(event.keyCode === 13) {
        //             var query = jQuery('#stock_data_search_scan').val();
        //             stock_data_table.draw();
        //         }
        //     }, 300); // Adjust the delay as needed
        // });
        // Add submit event listener to the form
        $('#f_search').on('submit', function (event) {
            event.preventDefault(); // Prevent the default form submission

            clearTimeout(debounceTimeout);

            debounceTimeout = setTimeout(function () {
                var query = $('#stock_data_search').val();
                stock_data_table.draw();
            }, 300); // Adjust the delay as needed
        });

    });
</script>
