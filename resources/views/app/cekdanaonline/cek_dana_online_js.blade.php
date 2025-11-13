<script>
    var cek_dana_online_table = '';

    function resetSelected() {
        $('#check_all_data').prop('checked', false);
    }

    function loadTotalDanaCair() {
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: "{{ url('cek_dana_online_total_dana_cair') }}",
            data: {
                search: $('#cek_dana_online_search').val(),
                st_id: $('#st_id').val(),
                platform: $('#filter_platform').val(),
                status: $('#filter_status').val(),
                filter_trx_date: $('#use_trx_date_filter').is(':checked') ? $('#trx_date').val() : null,
                filter_cash_out_date: $('#use_cash_out_date_filter').is(':checked') ? $('#cash_out_date').val() : null,
                settle_status: $('#filter_settle_status').val()
            },
            success: function(response) {
                var formattedTotalDanaCair = new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0
                }).format(response.totalDanaCair || 0);

                $('#total_dana_cair').text(formattedTotalDanaCair);
                var formattedTotalNetSalePrice = new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0
                }).format(response.totalNetSalePrice || 0);

                var formattedTotalRevenueMP = new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0
                }).format(response.totalRevenueMP || 0);

                var formattedTotalAdminFee = new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0
                }).format(response.totalAdminFee || 0);

                var formattedTotalSellerDiscount = new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0
                }).format(response.totalSellerDiscount || 0);

                var formattedPercentageFee = response.averageAdminFeePercentage ? response.averageAdminFeePercentage.toFixed(2) + '%' : '0%';
                $('#percentage_fee').text(formattedPercentageFee);

                $('#total_net_sales_jezpro').text(formattedTotalNetSalePrice);
                $('#total_revenue_mp').text(formattedTotalRevenueMP);
                $('#total_admin_fee').text(formattedTotalAdminFee);
                $('#total_seller_voucher').text(formattedTotalSellerDiscount);
            }
        });
    }

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        cek_dana_online_table = $('#CekDanaOnlinetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            scrollX: true,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            ajax: {
                url: "{{ route('cek_dana_online_datatables') }}",
                data: function(d) {
                    d.search = $('#cek_dana_online_search').val();
                    d.st_id = $('#st_id').val();
                    d.status = $('#filter_status').val();
                    if ($('#use_cash_out_date_filter').is(':checked')) {
                        d.filter_cash_out_date = $('#cash_out_date').val();
                    } else {
                        d.filter_cash_out_date = null;
                    }
                    if ($('#use_trx_date_filter').is(':checked')) {
                        d.filter_trx_date = $('#trx_date').val();
                    } else {
                        d.filter_trx_date = null;
                    }
                    d.platform = $('#filter_platform').val();
                    d.settle_status = $('#filter_settle_status').val();
                }
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        var disabled = row.pos_id == null ? 'disabled' : '';
                        return '<input type="checkbox" class="row-checkbox" id="check_' + row
                            .pos_id + '" data-is-partial="' + 0 + '">';
                    },
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                },
                {
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
                    data: 'settle_date',
                    name: 'settle_date',
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
                    data: 'revenue',
                    name: 'revenue',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                            data));
                    },
                    defaultContent: '-'
                },
                {
                    data: 'total_settle',
                    name: 'total_settle',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                            data));
                    },
                    defaultContent: '-'
                },
                {
                    data: 'seller_discount',
                    name: 'seller_discount',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : 'Rp ' + formatRupiah(parseInt(
                            data));
                    },
                    defaultContent: '-'
                },
                {
                    data: 'total_fee',
                    name: 'total_fee',
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
                    data: 'trx_date',
                    name: 'trx_date',
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
                    data: 'jezpro_price',
                    name: 'jezpro_price',
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
                        return !data || isNaN(data) ? 'Rp 0' : 'Rp ' + formatRupiah(parseInt(
                            data));
                    },
                },
                {
                    data: 'status',
                    name: 'status',
                    defaultContent: '-'
                },
                {
                    data: 'status_refund',
                    name: 'status_refund',
                    defaultContent: '-'
                },
                {
                    data: 'is_settle',
                    name: 'is_settle',
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
            },
            // Prevent auto load
            deferLoading: 0
        });

        $('#CekDanaOnlinetb tbody').on('click', 'tr', function() {
            var data = cek_dana_online_table.row(this).data();
            if (data) {
                var to_id = $('#to_id').val(data.to_id);
                var st_id = $('#st_id_form').val(data.st_id);
                var order_number = $('#order_number').val(data.order_number);

                console.log("Selected Row Data:", data);



                // Fetch data from cek_dana_detail based on to_id
                $.ajax({
                    url: "{{ url('cek_dana_detail') }}/" + data.order_number + "/" + data.st_id,
                    type: "GET",
                    success: function(response) {
                        if (response) {
                            // Update modal content with the fetched data
                            var modalBody = $('#DetailModal .modal-body tbody');
                            modalBody.find('tr').eq(0).find('td').eq(1).text(response
                                .st_name || '-');
                            modalBody.find('tr').eq(1).find('td').eq(1).text(response
                                .platform_name || '-');
                            modalBody.find('tr').eq(2).find('td').eq(1).text(response
                                .order_number || '-');
                            modalBody.find('tr').eq(3).find('td').eq(1).text(response
                                .trx_date ? new Date(response.trx_date)
                                .toLocaleDateString('id-ID') : '-');
                            modalBody.find('tr').eq(4).find('td').eq(1).text(response
                                .settle_date ? new Date(response.settle_date)
                                .toLocaleDateString('id-ID') : '-');
                            modalBody.find('tr').eq(5).find('td').eq(1).text(response
                                .jezpro_price ? 'Rp ' + formatRupiah(response
                                    .jezpro_price) : 'Rp 0');
                            modalBody.find('tr').eq(6).find('td').eq(1).text(response
                                .revenue ? 'Rp ' + formatRupiah(response.revenue) :
                                'Rp 0');
                            modalBody.find('tr').eq(7).find('td').eq(1).text(response.diff ?
                                'Rp ' + formatRupiah(response.diff) : 'Rp 0');
                            modalBody.find('tr').eq(8).find('td').eq(1).text(response
                                .seller_discount ? 'Rp ' + formatRupiah(response
                                    .seller_discount) : 'Rp 0');
                            modalBody.find('tr').eq(9).find('td').eq(1).text(response
                                .seller_voucher_persentage || '0%');
                            modalBody.find('tr').eq(10).find('td').eq(1).text(response
                                .affiliate_commission ? 'Rp ' + formatRupiah(response
                                    .affiliate_commission) : 'Rp 0');
                            modalBody.find('tr').eq(11).find('td').eq(1).text(response
                                .marketplace_commission_fee ? 'Rp ' + formatRupiah(
                                    response.marketplace_commission_fee) : 'Rp 0');
                            modalBody.find('tr').eq(12).find('td').eq(1).text(response
                                .service_fee ? 'Rp ' + formatRupiah(response
                                    .service_fee) : 'Rp 0');
                            modalBody.find('tr').eq(13).find('td').eq(1).text(response
                                .dynamic_commission ? 'Rp ' + formatRupiah(response
                                    .dynamic_commission) : 'Rp 0');
                            modalBody.find('tr').eq(14).find('td').eq(1).text(response
                                .voucher_xtra_service_fee ? 'Rp ' + formatRupiah(
                                    response.voucher_xtra_service_fee) : 'Rp 0');
                            modalBody.find('tr').eq(15).find('td').eq(1).text(response
                                .cashback_service_fee ? 'Rp ' + formatRupiah(response
                                    .cashback_service_fee) : 'Rp 0');
                            modalBody.find('tr').eq(16).find('td').eq(1).text(response
                                .total_fee ? 'Rp ' + formatRupiah(response.total_fee) :
                                'Rp 0');
                            modalBody.find('tr').eq(17).find('td').eq(1).text(response
                                .fee_persentage || '-');
                            modalBody.find('tr').eq(18).find('td').eq(1).text(response
                                .total_settle ? 'Rp ' + formatRupiah(response
                                    .total_settle) : 'Rp 0');
                        }
                    },
                    error: function() {
                        console.error("Failed to fetch detail data.");
                    }
                });

                $('#DetailModal').modal('show');
            }
        });

        $('#filter_btn').on('click', function() {
            if ($('#st_id').val()) {
                cek_dana_online_table.draw();
                loadTotalDanaCair();
            } else {
                swal('Error', 'Silakan pilih Toko terlebih dahulu.', 'warning');
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

        // Initialize Select2 on the select element
        $('#filter_status').select2({
            width: "100%",
            dropdownParent: $('#filter_status_parent')
        });

        // Initialize Select2 on the select element
        $('#filter_platform').select2({
            width: "100%",
            dropdownParent: $('#filter_platform_parent')
        });

        $('#filter_settle_status').select2({
            width: "100%",
            dropdownParent: $('#filter_settle_status_parent')
        });


        $(document).delegate('#import_modal', 'click', function() {
            $('#ImportModal').modal('show');
        });

        $(document).delegate('#export_btn', 'click', function() {
            let params = {
                search: $('#cek_dana_online_search').val(),
                st_id: $('#st_id').val(),
                platform: $('#filter_platform').val(),
                status: $('#filter_status').val(),
                filter_trx_date: $('#use_trx_date_filter').is(':checked') ? $('#trx_date').val() :
                    null,
                filter_cash_out_date: $('#use_cash_out_date_filter').is(':checked') ? $(
                    '#cash_out_date').val() : null
            };

            let query = $.param(params);

            window.location.href = "/export_transaction_settle?" + query;
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

        $('#check_all_data').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('input[id^="check_"]').prop('checked', isChecked);

            var checkedCount = 0;
            var totalDanaCair = 0;

            if (isChecked) {
                $('#CekDanaOnlinetb tbody input[id^="check_"]:checked').each(function() {
                    checkedCount++;

                    var row = $(this).closest('tr');
                    var danaCairText = row.find('td:eq(7)').text().replace(/Rp\s*/g, '').replace(
                        /\./g, '');
                    var danaCairValue = parseFloat(danaCairText) || 0;
                    totalDanaCair += danaCairValue;
                });
            }

            $('#selected').text(checkedCount);

            var formattedDanaCair = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(totalDanaCair);

            $('#selected_dana_cair').text(formattedDanaCair);
        });

        $('#CekDanaOnlinetb tbody').on('change', 'input[id^="check_"]', function() {
            var checkedCount = 0;
            var totalDanaCair = 0;

            $('#CekDanaOnlinetb tbody input[id^="check_"]:checked').each(function() {
                checkedCount++;

                var row = $(this).closest('tr');
                var danaCairText = row.find('td:eq(7)').text().replace(/Rp\s*/g, '').replace(
                    /\./g, '');
                var danaCairValue = parseFloat(danaCairText) || 0;
                totalDanaCair += danaCairValue;
            });

            $('#selected').text(checkedCount);

            var formattedDanaCair = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(totalDanaCair);

            $('#selected_dana_cair').text(formattedDanaCair);
        });


        $('#settlement_btn').on('click', function() {
            // Check if items are selected first
            var checkedIds = [];
            $('#CekDanaOnlinetb tbody input[id^="check_"]:checked').each(function() {
                var checkId = $(this).attr('id');
                var is_partial = $(this).attr('data-is-partial');
                var numberPart = checkId.replace('check_', '');
                checkedIds.push({
                    id: numberPart,
                    is_partial: is_partial
                });
            });

            if (checkedIds.length === 0) {
                alert('Please select at least one item to settle.');
                return;
            }

            // Show SweetAlert confirmation
            var selectedCount = $('#selected').text();
            Swal.fire({
                title: 'Confirm Settlement',
                text: `Are you sure you want to settle ${selectedCount} selected transactions?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, settle them!'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                // Execute AJAX only after confirmation
                $.ajax({
                    type: "POST",
                    url: "{{ url('settlement_bulk_status') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        checked_ids: checkedIds
                    },
                    success: function(response) {
                        // Handle success response
                        cek_dana_online_table.draw(false);
                        resetSelected();
                        toastr.success(
                            'Selected transactions have been settled successfully.'
                        );
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.log('Error: ' + error);
                    }
                });
            });
        });

        $('#CekDanaOnlinetb tbody').on('click', 'input[id^="check_"]', function(e) {
            e.stopPropagation();
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
                // cek_dana_online_table.draw();
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
                    'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(
                        1, 'month').endOf('month')]
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
                // cek_dana_online_table.draw();
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
                    'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(
                        1, 'month').endOf('month')]
                }
            }, cashOutCb);
            cashOutCb(cashOutStart, cashOutEnd, 'Hari Ini');
        }

    });
</script>
