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
            responsive: true,
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
                    d.st_id = $('#st_id_filter').val();
                    d.status = $('#filter_status').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'to_id',
                    searchable: false
                },
                {
                    data: 'order_number',
                    name: 'to_order_number'
                },
                {
                    data: 'no_resi',
                    name: 'no_resi'
                },
                {
                    data: 'platform_name',
                    name: 'platform_name'
                },
                {
                    data: 'order_date_created',
                    name: 'order_date_created'
                },
                {
                    data: 'total_item',
                    name: 'total_item'
                },
                {
                    data: 'ns_before_admin',
                    name: 'ns_before_admin',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : formatRupiah(parseInt(data));
                    }
                },
                {
                    data: 'order_status',
                    name: 'order_status'
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

        $('#st_id_filter').on('change', function() {
            let st_id = $(this).val();
            cek_dana_online_table.draw(false);
        });

        // Initialize Select2 on the select element
        $('#filter_status').select2({
            width: "200px",
            dropdownParent: $('#filter_status_parent')
        });

        $('#filter_status').on('change', function() {
            console.log($(this).val()); // Logs the selected value (0 or 1)
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

            console.log(formData);

            $.ajax({
                type: 'POST',
                url: "{{ url('transaksi_online_import') }}",
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


        function cb(start, end, label) {
            var title = '';
            var range = '';
            var hidden_range = '';
            var st_id = $('#st_id_filter').val();

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
            $('#sales_date').val(hidden_range);
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

    }
    });
</script>
