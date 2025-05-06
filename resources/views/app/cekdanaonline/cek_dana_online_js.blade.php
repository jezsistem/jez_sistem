<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        var cek_dana_online_table = $('#CekDanaOnlinetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false, // nonaktifkan fitur responsive bawaan DataTables
            scrollX: true,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ route('cek_dana_online_datatables') }}",
                data: function(d) {
                    d.search = $('#cek_dana_online_search').val();
                    d.st_id = $('#st_id').val();
                    d.status = $('#filter_status').val();
                    d.date_filter = $('#cek_dana_date').val();
                    d.platform = $('#filter_platform').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                },
                {
                    data: 'st_name',
                    name: 'st_name',
                },
                {
                    data: 'platform_name',
                    name: 'platform_name',
                    defaultContent: '-'
                },
                {
                    data: 'order_number',
                    name: 'order_number',
                },
                {
                    data: 'final_price',
                    name: 'final_price',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                        data));
                    },
                    defaultContent: '-'
                },
                {
                    data: 'seller_voucher_discount',
                    name: 'seller_voucher_discount',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                        data));
                    },
                    defaultContent: '-'
                },
                {
                    data: 'affiliate_cut',
                    name: 'affiliate_cut',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                        data));
                    },
                    defaultContent: '-'
                },
                {
                    data: 'marketplace_commision_fee',
                    name: 'marketplace_commision_fee',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                        data));
                    },
                    defaultContent: '-'
                },
                {
                    data: 'service_fee',
                    name: 'service_fee',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                        data));
                    },
                    defaultContent: '-'
                },
                {
                    data: 'voucher_xtra_service_fee',
                    name: 'voucher_xtra_service_fee',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                        data));
                    },
                    defaultContent: '-'
                },
                {
                    data: 'cashback_service_fee',
                    name: 'cashback_service_fee',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                        data));
                    },
                    defaultContent: '-'
                },
                {
                    data: 'cashout_date',
                    name: 'cashout_date',
                    render: function(data, type, row) {
                        if (!data) {
                            return '-';
                        }
                        var date = new Date(data);
                        var day = String(date.getDate()).padStart(2, '0');
                        var month = String(date.getMonth() + 1).padStart(2, '0');
                        var year = date.getFullYear();
                        return day + '/' + month + '/' + year;
                    },
                    defaultContent: '-'
                },
                {
                    data: 'transaction_date',
                    name: 'transaction_date',
                    render: function(data, type, row) {
                        if (!data) {
                            return '-';
                        }
                        var date = new Date(data);
                        var day = String(date.getDate()).padStart(2, '0');
                        var month = String(date.getMonth() + 1).padStart(2, '0');
                        var year = date.getFullYear();
                        return day + '/' + month + '/' + year;
                    },
                    defaultContent: '-'
                },
                {
                    data: 'admin_persentage',
                    name: 'admin_persentage',
                    defaultContent: '-'
                },

                {
                    data: 'gox_persentage',
                    name: 'gox_persentage',
                    defaultContent: '-'
                },
                {
                    data: 'status',
                    name: 'status',
                    defaultContent: '-'
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            language: {
                "lengthMenu": "_MENU_",
            }
        });

        var detail_table = $('#Detailtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ url('cek_dana_detail_datatables') }}",
                data: function(d) {
                    d.to_id = $('#to_id').val(); // Pass the order_id to the server
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'ps_barcode',
                    name: 'ps_barcode'
                },
                {
                    data: 'sku',
                    name: 'sku'
                },
                {
                    data: 'to_qty',
                    name: 'to_qty'
                },
                {
                    data: 'shopee_price',
                    name: 'shopee_price'
                },
                {
                    data: 'jez_price',
                    name: 'jez_price'
                },
                {
                    data: 'gap_price',
                    name: 'gap_price'
                },
                {
                    data: 'total_discount',
                    name: 'total_discount'
                },
                {
                    data: 'ns_before_admin',
                    name: 'ns_before_admin'
                },
                {
                    data: 'final_price',
                    name: 'final_price'
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "5%"
            }],
            language: {
                "lengthMenu": "_MENU_",
            }
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

        $('#st_id').on('change', function() {
            cek_dana_online_table.draw();
        });

        // Initialize Select2 on the select element
        $('#filter_status').select2({
            width: "200px",
            dropdownParent: $('#filter_status_parent')
        });

        $('#filter_status').on('change', function() {
            cek_dana_online_table.draw();
        });

        // Initialize Select2 on the select element
        $('#filter_platform').select2({
            width: "200px",
            dropdownParent: $('#filter_platform_parent')
        });

        $('#filter_platform').on('change', function() {
            cek_dana_online_table.draw();
        });


        $(document).delegate('#import_modal', 'click', function() {
            $('#ImportModal').modal('show');
        });

        $('#cek_dana_online_search').on('keyup', function() {
            cek_dana_online_table.draw(false);
            console.log($('#cek_dana_online_search').val())
        });

        {{-- $('#f_import').on('submit' , function (e) { --}}
        {{--    e.preventDefault(); --}}
        {{--    jQuery.noConflict(); --}}
        {{--    $('#import_data_btn').html('Proses...'); --}}
        {{--    $('#import_data_btn').attr('disabled', true); --}}
        {{--    var formData = new FormData(this); --}}

        {{--    $.ajax({ --}}
        {{--        type: 'POST', --}}
        {{--        url: "{{ url('transaksi_online_import')}}", --}}
        {{--        data: formData, --}}
        {{--        dataType: 'json', --}}
        {{--        cache:false, --}}
        {{--        contentType: false, --}}
        {{--        processData: false, --}}
        {{--        success: function(data) { --}}
        {{--            // console.log(data.data['missingBarcode']); --}}
        {{--            $("#import_data_btn").html('Import'); --}}
        {{--            $("#import_data_btn").attr("disabled", false); --}}
        {{--            jQuery.noConflict(); --}}
        {{--            if (data.status == '200') { --}}
        {{--                $("#ImportModal").modal('hide'); --}}

        {{--                swal('Berhasil', 'Data berhasil diimport', 'success'); --}}
        {{--                $('#f_import')[0].reset(); --}}
        {{--                excelImportData = data.data['processedData']; --}}
        {{--                console.log(data.data); --}}
        {{--                console.log(data.name); --}}
        {{--                // shopee_tables.draw(); --}}
        {{--                // tiktok_tables.draw(); --}}
        {{--            } else if (data.status == '400') { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--                console.log(data.data) --}}
        {{--                swal('Error', 'File yang anda import kosong atau format tidak tepat', 'warning'); --}}
        {{--            } else { --}}
        {{--                $("#ImportModal").modal('hide'); --}}

        {{--            } --}}
        {{--        }, --}}
        {{--        error: function(data){ --}}
        {{--            swal('Error', data, 'error'); --}}
        {{--        } --}}
        {{--    }); --}}
        {{-- }); --}}

        {{-- $('#f_import').on('submit', function (e) { --}}
        {{--    e.preventDefault(); --}}
        {{--    jQuery.noConflict(); --}}

        {{--    // Show the spinner and disable the button --}}
        {{--    $('#import_data_btn').html('<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...'); --}}
        {{--    $('#import_data_btn').attr('disabled', true); --}}

        {{--    var formData = new FormData(this); --}}

        {{--    // Manually append the value of st_id_form to the FormData --}}
        {{--    var st_id_form_value = $('#st_id_form').val();  // Get the value of the disabled input --}}
        {{--    formData.append('st_id_form', st_id_form_value); // Append it to the FormData --}}
        {{--    --}}
        {{--    console.log($('#st_id_form').val()); --}}

        {{--    $.ajax({ --}}
        {{--        type: 'POST', --}}
        {{--        url: "{{ url('transaksi_online_import')}}", --}}
        {{--        data: formData, --}}
        {{--        dataType: 'json', --}}
        {{--        cache: false, --}}
        {{--        contentType: false, --}}
        {{--        processData: false, --}}
        {{--        success: function (data) { --}}
        {{--            // Hide the spinner and enable the button --}}
        {{--            $('#import_data_btn').html('Import'); --}}
        {{--            $('#import_data_btn').attr("disabled", false); --}}

        {{--            if (data.status == '200') { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--                swal('Berhasil', 'Data berhasil diimport', 'success'); --}}
        {{--                $('#f_import')[0].reset(); --}}
        {{--                excelImportData = data.data['processedData']; --}}
        {{--                console.log(data.data); --}}
        {{--                console.log(data.name); --}}
        {{--            } else if (data.status == '400') { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--                console.log(data.data); --}}
        {{--                swal('Error', 'File yang anda import kosong atau format tidak tepat', 'warning'); --}}
        {{--            } else { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--            } --}}
        {{--        }, --}}
        {{--        error: function (data) { --}}
        {{--            // Hide the spinner in case of error --}}
        {{--            $('#import_data_btn').html('Import'); --}}
        {{--            $('#import_data_btn').attr("disabled", false); --}}

        {{--            swal('Error', data, 'error'); --}}
        {{--        } --}}
        {{--    }); --}}
        {{--    cek_dana_online_table.draw(false); --}}
        {{-- }); --}}

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            jQuery.noConflict();

            // Show the spinner and disable the button
            $('#import_data_btn').html(
                '<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...'
            );
            $('#import_data_btn').attr('disabled', true);

            // Create a new FormData object from the form
            var formData = new FormData(this);

            // Manually append the value of st_id_form to the FormData
            var st_id_form_value = $('#st_id_form').val(); // Get the value of the disabled input
            formData.append('st_id_form', st_id_form_value); // Append it to the FormData

            // formData.forEach((value, key) => {
            //     console.log(key + ': ' + value);
            // });

            $.ajax({
                type: 'POST',
                url: "{{ url('cek_dana_online_import') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    // Hide the spinner and enable the button
                    $('#import_data_btn').html('Import');
                    $('#import_data_btn').attr("disabled", false);

                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        swal('Berhasil', 'Data berhasil diimport', 'success');
                        $('#f_import')[0].reset();
                        excelImportData = data.data['processedData'];
                        console.log(data.data);
                        console.log(data.name);
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        console.log(data.data);
                        swal('Error',
                            'File yang anda import kosong atau format tidak tepat',
                            'warning');
                    } else {
                        $("#ImportModal").modal('hide');
                    }
                },
                error: function(data) {
                    // Hide the spinner in case of error
                    $('#import_data_btn').html('Import');
                    $('#import_data_btn').attr("disabled", false);

                    swal('Error', data, 'error');
                }
            });
            cek_dana_online_table.draw(false);
        });
        {{-- $('#f_import').on('submit', function (e) { --}}
        {{--    e.preventDefault(); --}}
        {{--    jQuery.noConflict(); --}}

        {{--    // Show the spinner and disable the button --}}
        {{--    $('#import_data_btn').html('<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...'); --}}
        {{--    $('#import_data_btn').attr('disabled', true); --}}

        {{--    // Create a new FormData object from the form --}}
        {{--    var formData = new FormData(this); --}}

        {{--    // Manually append the value of st_id_form to the FormData --}}
        {{--    var st_id_form_value = $('#st_id_form').val();  // Get the value of the disabled input --}}
        {{--    formData.append('st_id_form', st_id_form_value); // Append it to the FormData --}}

        {{--    console.log(formData); --}}

        {{--    $.ajax({ --}}
        {{--        type: 'POST', --}}
        {{--        url: "{{ url('transaksi_online_import') }}", --}}
        {{--        data: formData, --}}
        {{--        dataType: 'json', --}}
        {{--        cache: false, --}}
        {{--        contentType: false, --}}
        {{--        processData: false, --}}
        {{--        success: function (data) { --}}
        {{--            // Hide the spinner and enable the button --}}
        {{--            $('#import_data_btn').html('Import'); --}}
        {{--            $('#import_data_btn').attr("disabled", false); --}}

        {{--            if (data.status == '200') { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--                swal('Berhasil', 'Data berhasil diimport', 'success'); --}}
        {{--                $('#f_import')[0].reset(); --}}
        {{--                excelImportData = data.data['processedData']; --}}
        {{--                console.log(data.data); --}}
        {{--                console.log(data.name); --}}
        {{--            } else if (data.status == '400') { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--                console.log(data.data); --}}
        {{--                swal('Error', 'File yang anda import kosong atau format tidak tepat', 'warning'); --}}
        {{--            } else { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--            } --}}
        {{--        }, --}}
        {{--        error: function (data) { --}}
        {{--            // Hide the spinner in case of error --}}
        {{--            $('#import_data_btn').html('Import'); --}}
        {{--            $('#import_data_btn').attr("disabled", false); --}}

        {{--            swal('Error', data, 'error'); --}}
        {{--        } --}}
        {{--    }); --}}
        {{--    cek_dana_online_table.draw(false); --}}
        {{-- }); --}}

        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }



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

            if ((end - start) < 100 || label == 'Hari Ini') {
                title = 'Hari Ini:';
                range = start.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD');
            } else if (label == 'Kemarin') {
                title = 'Kemarin:';
                range = start.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD');
            } else {
                range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }
            $('#cek_dana_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            cek_dana_online_table.draw();
            // article_report_table.draw();
        }

        picker.daterangepicker({
            direction: KTUtil.isRTL(),
            startDate: start,
            endDate: end,
            opens: 'center',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            ranges: {
                'Hari Ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            }
        }, cb);
        cb(start, end, '');

    });
</script>
