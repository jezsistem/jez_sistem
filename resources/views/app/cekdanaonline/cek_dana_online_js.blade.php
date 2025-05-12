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
                    d.filter_trx_date = $('#trx_date').val();
                    d.filter_cash_out_date = $('#cash_out_date').val();
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
                    data: 'final_price',
                    name: 'final_price',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                        data));
                    },
                    defaultContent: '-'
                },
                {
                    data: 'total_disburshed_amount',
                    name: 'total_disburshed_amount',
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
                    data: 'total_online_cut',
                    name: 'total_online_cut',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                        data));
                    },
                    defaultContent: '-'
                },
               
                {
                    data: 'fee_persentage',
                    name: 'fee_persentage',
                    defaultContent: '-'
                },

                {
                    data: 'seller_voucher_persentage',
                    name: 'seller_voucher_persentage',
                    defaultContent: '-'
                },
                {
                    data: 'jezpro_transaction_date',
                    name: 'jezpro_transaction_date',
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
                    data: 'pos_real_price',
                    name: 'pos_real_price',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                        data));
                    },
                    defaultContent: '-'
                },
                {
                    data: 'diff_jezpro_mp',
                    name: 'diff_jezpro_mp',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                        data));
                    },
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

        $('#CekDanaOnlinetb tbody').on('click', 'tr', function() {
            var data = cek_dana_online_table.row(this).data();
            if (data) {
            var to_id = $('#to_id').val(data.to_id);
            var st_id = $('#st_id_form').val(data.st_id);
            var order_number = $('#order_number').val(data.order_number);
            

            // Fetch data from cek_dana_detail based on to_id
            $.ajax({
                url: "{{ url('cek_dana_detail') }}/" + data.order_number,
                type: "GET",
                success: function(response) {
                if (response) {
                    // Update modal content with the fetched data
                    var modalBody = $('#DetailModal .modal-body tbody');
                    modalBody.find('tr').eq(0).find('td').eq(1).text(response.st_name || '-');
                    modalBody.find('tr').eq(1).find('td').eq(1).text(response.platform_name || '-');
                    modalBody.find('tr').eq(2).find('td').eq(1).text(response.order_number || '-');
                    modalBody.find('tr').eq(3).find('td').eq(1).text(response.jezpro_transaction_date ? new Date(response.jezpro_transaction_date).toLocaleDateString('id-ID') : '-');
                    modalBody.find('tr').eq(4).find('td').eq(1).text(response.cashout_date ? new Date(response.cashout_date).toLocaleDateString('id-ID') : '-');
                    modalBody.find('tr').eq(5).find('td').eq(1).text(response.pos_real_price ? 'Rp ' + formatRupiah(response.pos_real_price) : 'Rp 0');
                    modalBody.find('tr').eq(6).find('td').eq(1).text(response.final_price ? 'Rp ' + formatRupiah(response.final_price) : 'Rp 0');
                    modalBody.find('tr').eq(7).find('td').eq(1).text(response.diff ? 'Rp ' + formatRupiah(response.diff) : 'Rp 0');
                    modalBody.find('tr').eq(8).find('td').eq(1).text(response.seller_voucher_discount ? 'Rp ' + formatRupiah(response.seller_voucher_discount) : 'Rp 0');
                    modalBody.find('tr').eq(9).find('td').eq(1).text(response.seller_voucher_persentage || '0%');
                    modalBody.find('tr').eq(10).find('td').eq(1).text(response.affiliate_commission ? 'Rp ' + formatRupiah(response.affiliate_commission) : 'Rp 0');
                    modalBody.find('tr').eq(11).find('td').eq(1).text(response.marketplace_commission_fee ? 'Rp ' + formatRupiah(response.marketplace_commission_fee) : 'Rp 0');
                    modalBody.find('tr').eq(12).find('td').eq(1).text(response.service_fee ? 'Rp ' + formatRupiah(response.service_fee) : 'Rp 0');
                    modalBody.find('tr').eq(13).find('td').eq(1).text(response.voucher_xtra_service_fee ? 'Rp ' + formatRupiah(response.voucher_xtra_service_fee) : 'Rp 0');
                    modalBody.find('tr').eq(14).find('td').eq(1).text(response.cashback_service_fee ? 'Rp ' + formatRupiah(response.cashback_service_fee) : 'Rp 0');
                    modalBody.find('tr').eq(15).find('td').eq(1).text(response.total_online_cut ? 'Rp ' + formatRupiah(response.total_online_cut) : 'Rp 0');
                    modalBody.find('tr').eq(16).find('td').eq(1).text(response.fee_persentage || '-');
                    modalBody.find('tr').eq(17).find('td').eq(1).text(response.total_disburshed_amount ? 'Rp ' + formatRupiah(response.total_disburshed_amount) : 'Rp 0');
                }
                },
                error: function() {
                console.error("Failed to fetch detail data.");
                }
            });

            $('#DetailModal').modal('show');
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

        let searchTimeout;
        $('#cek_dana_online_search').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
            cek_dana_online_table.draw(false);
            console.log($('#cek_dana_online_search').val());
            }, 1000); // Wait for 1 second
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

        // Initialize the first date picker for trx_date
        var trxPicker = $('#trx_date_picker');
        if (trxPicker.length > 0) {
            var trxStart = moment();
            var trxEnd = moment();

            function trxCb(start, end, label) {
            var startDate = start.format('DD MMM YYYY');
            var endDate = end.format('DD MMM YYYY');
            var range = startDate === endDate ? startDate : startDate + ' - ' + endDate;
            var hiddenRange = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            $('#trx_date').val(hiddenRange);
            $('#trx_date_picker_title').html(label + ' : ' || 'Hari Ini');
            $('#trx_date_picker_date').html(range);
            cek_dana_online_table.draw();
            }

            trxPicker.daterangepicker({
            direction: KTUtil.isRTL(),
            startDate: trxStart,
            endDate: trxEnd,
            opens: 'center',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            ranges: {
                'Hari Ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
            }, trxCb);
            trxCb(trxStart, trxEnd, 'Hari Ini');
        }

        // Initialize the second date picker for cash_out_date
        var cashOutPicker = $('#cash_out_date_picker');
        if (cashOutPicker.length > 0) {
            var cashOutStart = moment();
            var cashOutEnd = moment();

            function cashOutCb(start, end, label) {
            
            var startDate = start.format('DD MMM YYYY');
            var endDate = end.format('DD MMM YYYY');
            var range = startDate === endDate ? startDate : startDate + ' - ' + endDate;
            var hiddenRange = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            $('#cash_out_date').val(hiddenRange);
            $('#cash_out_date_picker_title').html(label + ' : ' || 'Hari Ini');
            $('#cash_out_date_picker_date').html(range);
            cek_dana_online_table.draw();
            }

            cashOutPicker.daterangepicker({
            direction: KTUtil.isRTL(),
            startDate: cashOutStart,
            endDate: cashOutEnd,
            opens: 'center',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            ranges: {
            'Hari Ini': [moment(), moment()],
            'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
            '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
            'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
            'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
            }, cashOutCb);
            cashOutCb(cashOutStart, cashOutEnd, 'Hari Ini');
        }

    });
</script>
