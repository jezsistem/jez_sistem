<script>
    var history_date = '';
    var start_bin_table = '';
    var end_bin_table = '';
    var bin_history_table = '';

    function loadStartEnd() {
        $.ajax({
            type: "GET",
            dataType: 'html',
            url: "{{ url('reload_start_bin') }}",
            success: function(r) {
                $("#start").html(r);
            }
        });
        $.ajax({
            type: "GET",
            dataType: 'html',
            url: "{{ url('reload_end_bin') }}",
            success: function(r) {
                $("#end").html(r);
            }
        });
        $.ajax({
            type: "GET",
            dataType: 'html',
            url: "{{ url('reload_history_start_bin') }}",
            success: function(r) {
                $("#history_start").html(r);
            }
        });
        $.ajax({
            type: "GET",
            dataType: 'html',
            url: "{{ url('reload_history_end_bin') }}",
            success: function(r) {
                $("#history_end").html(r);
            }
        });
        return false;
    }

    function mappingQty(pls_id, index, pst_id, pls_qty) {

    }

    function saveMutation(pls_id, index, pst_id, pls_qty) {
        var pl_id_end = $('#pl_id_end').val();
        var pmt_qty = $('.mutation_qty' + index).val();
        if (pmt_qty == 0 || pmt_qty == '') {
            return true;
        }
        //alert(pls_id+' | '+pl_id_end+' | '+pmt_qty);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {
                _pls_id: pls_id,
                _pl_id_end: pl_id_end,
                _pmt_qty: pmt_qty,
                _pmt_old_qty: pls_qty,
                _pst_id: pst_id
            },
            dataType: 'json',
            url: "{{ url('sv_mutation_v2') }}",
            success: function(r) {
                if (r.status == '200') {

                } else {
                    toast('Error', 'Ada error, info ke programmer', 'error');
                }
            }
        });
        return false;
    }

    //button cancel
    $('#CancelBtn').on('click', function() {
        jQuery.noConflict();

        if (confirm('Apakah anda yakin untuk menghapus data import?')) {
            fetch('{{ url('cancel_import') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        toastr.success('Cancel berhasil', 'Berhasil');
                        start_bin_table.draw();
                    } else {
                        toastr.warning('Terjadi kesalahan saat melakukan cancel', 'Gagal');
                    }
                })
                .catch(error => {
                    console.error(error);
                    toastr.error('An error occurred while processing your request', 'Error');
                });
        }
    });



    //button import
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        $('#import_data_btn').html('Proses...');
        $('#import_data_btn').attr('disabled', true);
        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: "{{ url('stock_location_import') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                jQuery.noConflict();

                if (data.status == '200') {
                    $("#ImportModal").modal('hide');
                    toastr.success('Data berhasil diimport', 'Berhasil');
                    $('#f_import')[0].reset();

                    excelImportData = data.data['processedData'];
                    start_bin_table.draw();

                    checkMissingBarcode(data.data['missingBarcode']);

                } else if (data.status == '400') {
                    $("#ImportModal").modal('hide');
                    toastr.warning('File yang anda import kosong atau format tidak tepat', 'File');
                } else {
                    $("#ImportModal").modal('hide');
                    toastr.error('Terjadi kesalahan saat memproses file', 'Error');
                }
            },
            error: function(data) {
                toastr.error('An error occurred while processing your request', 'Error');
            }
        });
    });


    //button
    function checkMissingBarcode(missingBarcodeData) {
        // each from missingBarcodeData array
        var missingBarcode = [];
        for (var i = 0; i < missingBarcodeData.length; i++) {
            missingBarcode.push(missingBarcodeData[i]);
        }

        if (missingBarcode.length > 0) {
            swal('Missing Barcode', 'Barcode yang tidak terdaftar : ' + missingBarcode.join(', '), 'warning');
        }
    }

    $('#ImportModalBtn').on('click', function() {
        jQuery.noConflict();
        $('#ImportModal').modal('show');
    });



    $(document).ready(function() {
        // $('body').addClass('kt-primary--minimize aside-minimize');
        loadStartEnd();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //button untuk memilih jumlah data yang akan di tampilkan kiri
        start_bin_table = $('#StartBintb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            //bPaginate: false,
            dom: '<"text-right"l>rt<"text-right"p>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('start_bin_datatables') }}",
                data: function(d) {
                    d.pl_id = $('#pl_id_start').val();
                    d.search = $('#article_search').val();
                    d.st_id = $('#st_id_filter').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'pls_id',
                    searchable: false
                },
                {
                    data: 'article',
                    name: 'article',
                    orderable: false
                },
                {
                    data: 'qty',
                    name: 'qty',
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
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [
                [0, 'desc']
            ],
        });

        $('#article_search').on('keyup', function() {
            start_bin_table.draw();
        });

        //button untuk memilih jumlah data yang akan di tampilkan kanan
        end_bin_table = $('#EndBintb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            //bPaginate: false,
            dom: '<"text-right"l>rt<"text-right"p>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('end_bin_datatables') }}",
                data: function(d) {
                    d.pl_id = $('#pl_id_end').val();
                    d.search = $('#article_end_search').val();
                    d.st_id = $('#st_id_filter').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'pls_id',
                    searchable: false
                },
                {
                    data: 'article',
                    name: 'article',
                    orderable: false
                },
                {
                    data: 'qty',
                    name: 'qty',
                    orderable: false
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [
                [0, 'desc']
            ],
        });

        $('#article_end_search').on('keyup', function() {
            end_bin_table.draw();
        });



        //ISI TABEL HISTORY SETUP
        bin_history_table = $('#BinHistorytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"p>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('bin_history_datatables') }}",
                data: function(d) {
                    d.search = $('#history_search').val();
                    d.st_id = $('#st_id_filter').val();
                    d.date = history_date;
                    d.history_bin_start = $('#history_pl_id_start').val();
                    d.history_bin_end = $('#history_pl_id_end').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'pmt_id',
                    searchable: false
                },
                {
                    data: 'u_name',
                    name: 'u_name'
                },
                {
                    data: 'article',
                    name: 'article',
                    orderable: false
                },
                {
                    data: 'ps_barcode',
                    name: 'ps_barcode',
                    orderable: false
                },
                {
                    data: 'st_name',
                    name: 'st_name'
                },
                {
                    data: 'start_bin',
                    name: 'start_bin',
                    orderable: false
                },
                {
                    data: 'pmt_old_qty',
                    name: 'pmt_old_qty',
                    orderable: false
                },
                {
                    data: 'pmt_qty',
                    name: 'pmt_qty',
                    orderable: false
                },
                {
                    data: 'pmt_old_qty_new',
                    name: 'pmt_old_qty_new',
                    orderable: false
                },
                {
                    data: 'end_bin',
                    name: 'end_bin',
                    orderable: false
                },
                {
                    data: 'pm_created_at',
                    name: 'pm_created_at',
                    orderable: false
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [
                [0, 'desc']
            ],
        });

        $('#history_search').on('keyup', function() {
            bin_history_table.draw();
        });

        $('#st_id_filter').on('change', function() {
            start_bin_table.draw();
            end_bin_table.draw();
            bin_history_table.draw();
        });

        //KOLOM JIKA INGIN MENGURANGI STOK namun stoknya kebanyakan(dr stok yg dipunya)/minus
        $(document).delegate('#mutation_qty', 'change', function(e) {
            var pls_qty = $(this).attr('data-qty');
            var qty = $(this).val();
            if (parseInt(qty) > parseInt(pls_qty)) {
                swal('Qty', 'Melebihi batas stok', 'warning');
                $(this).val('');
                return false;
            } else if (parseInt(qty) < 0) {
                swal('Qty', 'Tidak boleh minus', 'warning');
                $(this).val('');
                return false;
            }
        });

        //BUTTON  MUTATION DI TABEL A/KIRI
        $('#mutation_btn').on('click', function() {
            swal({
                title: "Mutasi..?",
                text: "Yakin mutasi data ?",
                icon: "info",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $(this).addClass('disabled');
                    var total_row = $('input[data-mutation-qty]').length;
                    var start_bin = $('#pl_id_start').val();
                    var end_bin = $('#pl_id_end').val();
                    var finish = '';
                    if (end_bin == '') {
                        swal("BIN Tujuan", "Pilih BIN tujuan", "warning");
                        $(this).removeClass('disabled');
                        return false;
                    }
                    if (start_bin == end_bin) {
                        swal("BIN Sama",
                            "BIN awal dan BIN tujuan tidak boleh sama, silahkan ganti BIN tujuan",
                            "warning");
                        $(this).removeClass('disabled');
                        return false;
                    }
                    for (let i = 1; i <= total_row; ++i) {
                        $('#saveMutation' + i).trigger('click');
                        if (i == total_row) {
                            finish = 'true';
                        }
                    }
                    if (finish == 'true') {
                        start_bin_table.draw();
                        end_bin_table.draw();
                        bin_history_table.draw();
                        $(this).removeClass('disabled');
                        toast('Berhasil', 'Mutasi berhasil', 'success');
                    }
                }
            })
        });






        //HISTORY SETUP

        $(document).delegate('#export_btn', 'click', function(e) {
            e.preventDefault();
            $('#export_btn').text('Mohon Tunggu..');
            $('#export_btn').addClass('disabled');
            var date = history_date;
            var st_id = $('#st_id_filter').val();
            var search = $('#history_search').val();
            var history_bin_start = $('#history_pl_id_start').val();
            var history_bin_end = $('#history_pl_id_end').val();
            window.location.href = "{{ url('history_setup_export') }}?&st_id=" + st_id + "&date=" +
                date + "&search=" + search + "&history_bin_start=" + history_bin_start + "&history_bin_end=" + history_bin_end;

            $('#export_btn').text('Export Data');
            $('#export_btn').removeClass('disabled');
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
            history_date = hidden_range;
            $('#dashboard_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            bin_history_table.draw();

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
