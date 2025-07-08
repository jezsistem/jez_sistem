<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    var history_date = '';
    var start_bin_table = '';
    var end_bin_table = '';
    var bin_history_table = '';
    var temp_multibin_data = '';

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

    function saveMutation(pls_id, index, pst_id, pls_qty, note) {
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
                _pst_id: pst_id,
                _note: note
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
    function handleCancel(url, successMessage, errorMessage) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data import akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            toastr.success(successMessage, 'Berhasil');
                            start_bin_table.draw();
                            temp_multibin_data.draw();
                        } else {
                            toastr.warning(errorMessage, 'Gagal');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        toastr.error('An error occurred while processing your request', 'Error');
                    });
            }
        });
    }

    $('#CancelBtn').on('click', function() {
        handleCancel('{{ url('cancel_import') }}', 'Cancel berhasil',
            'Terjadi kesalahan saat melakukan cancel');
    });

    $('#clearMutationMultiBinBtn').on('click', function() {
        handleCancel('{{ url('cancel_import') }}', 'Cancel berhasil',
            'Terjadi kesalahan saat melakukan cancel');
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
                    toastr.success('Data berhasil diimport', 'Berhasil');
                    $('#f_import')[0].reset();

                    excelImportData = data.data['processedData'];
                    start_bin_table.draw();

                    checkMissingBarcode(data.data['missingBarcode']);

                } else if (data.status == '400') {
                    $("#ImportModal").modal('hide');
                    Swal.fire({
                        title: 'File Error',
                        text: 'File yang anda import kosong atau format tidak tepat',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                } else {
                    $("#ImportModal").modal('hide');
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Terjadi kesalahan saat memproses file',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(data) {
                toastr.error('An error occurred while processing your request', 'Error');
            }
        });
    });

    //button
    function checkMissingBarcode(missingBarcodeData) {
        if (missingBarcodeData && missingBarcodeData.length > 0) {
            Swal.fire({
                title: 'Missing Barcode Data',
                html: `
                    <div style="overflow-x:auto;">
                        <table class="table" style="width:100%; text-align:left; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #ccc; padding: 8px;">Bin</th>
                                    <th style="border: 1px solid #ccc; padding: 8px;">Barcode</th>
                                </tr>
                            </thead>
                            <tbody id="barcode-table-body">
                                <!-- Data masuk sini -->
                            </tbody>
                        </table>
                        <br/>
                        <div style="text-align: center;">
                            <button id="export_missing_barcode" class="swal2-confirm swal2-styled" style="background-color:#28a745; margin-right:10px;">Export ke Excel</button>
                            <button id="close_missing_alert" class="swal2-cancel swal2-styled" style="background-color:#dc3545;">Tutup</button>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showConfirmButton: false,
                didOpen: () => {
                    let tbody = document.getElementById('barcode-table-body');
                    missingBarcodeData.forEach(function(item) {
                        let row = document.createElement('tr');
                        row.innerHTML = `
                            <td style="border: 1px solid #ccc; padding: 8px;">${item[0] || '-'}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">${item[1] || '-'}</td>
                        `;
                        tbody.appendChild(row);
                    });

                    // Tombol Export Excel
                    document.getElementById('export_missing_barcode')
                        .addEventListener('click', function() {
                            let wb = XLSX.utils.book_new();
                            let ws_data = [
                                ["Bin", "Barcode"], // Header
                                ...missingBarcodeData.map(item => [
                                    item[0] || '-',
                                    item[1] || '-'
                                ])
                            ];
                            let ws = XLSX.utils.aoa_to_sheet(ws_data);
                            XLSX.utils.book_append_sheet(wb, ws, "Missing Barcodes");
                            XLSX.writeFile(wb, "Missing_Barcodes.xlsx");
                        });

                    // Tombol Tutup
                    document.getElementById('close_missing_alert')
                        .addEventListener('click', function() {
                            Swal.close();
                        });
                }
            });
        }
    }

    $('#ImportModalBtn').on('click', function() {
        jQuery.noConflict();
        $('#ImportModal').modal('show');
    });



    $(document).ready(function() {
        // $('body').addClass('kt-primary--minimize aside-minimize');
        loadStartEnd();
        $(document).ajaxStop(function() {
            // Continue the rest of the code after all AJAX requests are done
            $(document).off('ajaxStop');
            // Place any code that should run after loadStartEnd() AJAX calls finish here
        });
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
            drawCallback: function(settings) {
                // Get the first pl_id from the data and select it in #pl_id_start
                var api = this.api();
                var data = api.rows({
                    page: 'current'
                }).data();
                if (data.length > 0) {
                    var firstPlId = data[0].pl_id;
                    if (firstPlId) {
                        var $select = $('#pl_id_start');
                        if ($select.length && $select.val() != firstPlId) {
                            $select.val(firstPlId).trigger('change').trigger('select2:select');
                            $select.select2('close');
                        }
                    }
                }
            }
        });

        var searchTimeout;
        $('#article_search').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                start_bin_table.draw();
            }, 2000);
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
                    data: 'notes',
                    name: 'notes',
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

        temp_multibin_data = $('#mutationMultiBinTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"p>',
            buttons: [{}],
            ajax: {
                url: "{{ url('temp_multibins_datatable') }}",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'pls_id',
                    searchable: false
                },
                {
                    data: 'ps_barcode',
                    name: 'ps_barcode',
                    orderable: false,
                },
                {
                    data: 'start_bin',
                    name: 'start_bin',
                    orderable: false,
                },
                {
                    data: 'end_bin',
                    name: 'end_bin',
                    orderable: false
                },
                {
                    data: 'start_qty',
                    name: 'start_qty',
                    orderable: false
                },
                {
                    data: 'mutation_qty',
                    name: 'mutation_qty',
                    orderable: false
                },
                {
                    data: 'notes',
                    name: 'notes',
                    defaultContent: '-',
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

        $('#mutationMultiBinForm').on('submit', function(e) {
            e.preventDefault();
            $('#mutationMultiBinSubmitBtn').html('Proses...');
            $('#mutationMultiBinSubmitBtn').attr('disabled', true);
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ url('stock_location_import_multibins') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#mutationMultiBinSubmitBtn').html('Submit');
                    $('#mutationMultiBinSubmitBtn').attr('disabled', false);
                    jQuery.noConflict();

                    if (data.status == '200') {
                        toastr.success('Data berhasil diimport', 'Berhasil');
                        $('#mutationMultiBinForm')[0].reset();
                        temp_multibin_data.draw();
                        start_bin_table.draw();
                    } else if (data.status == '400') {
                        Swal.fire({
                            title: 'File Error',
                            text: 'File yang anda import kosong atau format tidak tepat',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    } else if (data.status == '406') {
                        Swal.fire({
                            title: 'File Error',
                            text: 'Ada data yang tidak lengkap atau salah format',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });

                        checkMultibinMissingBarcode(data.missingBarcode, function() {
                            checkMissingBins(data.missingBins, function() {
                                checkMissingItemsOnBin(data
                                    .missingItemsOnBin,
                                    function() {
                                        checkQtyInvalid(data.qtyInvalid,
                                            function() {
                                                // All checks done
                                                toastr.info(
                                                    'Semua data telah diperiksa',
                                                    'Info');
                                            });
                                    });
                            });
                        });

                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message ||
                                'Terjadi kesalahan saat memproses file',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(data) {
                    toastr.error('An error occurred while processing your request',
                        'Error');
                }
            });
        });

        function checkMultibinMissingBarcode(missingBarcodeData, callback) {
            if (missingBarcodeData && missingBarcodeData.length > 0) {
                Swal.fire({
                    title: 'Missing Barcode Data',
                    html: `
                <div style="overflow-x:auto;">
                    <table class="table" style="width:100%; text-align:left; border-collapse: collapse;">
                    <thead>
                        <tr>
                        <th style="border: 1px solid #ccc; padding: 8px;">Bin</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Barcode</th>
                        </tr>
                    </thead>
                    <tbody id="barcode-table-body">
                        <!-- Data masuk sini -->
                    </tbody>
                    </table>
                    <br/>
                    <div style="text-align: center;">
                    <button id="export_missing_barcode" class="swal2-confirm swal2-styled" style="background-color:#28a745; margin-right:10px;">Export ke Excel</button>
                    <button id="close_missing_alert" class="swal2-cancel swal2-styled" style="background-color:#dc3545;">Tutup</button>
                    </div>
                </div>
                `,
                    icon: 'warning',
                    showConfirmButton: false,
                    didOpen: () => {
                        let tbody = document.getElementById('barcode-table-body');
                        missingBarcodeData.forEach(function(item) {
                            let row = document.createElement('tr');
                            row.innerHTML = `
                    <td style="border: 1px solid #ccc; padding: 8px;">${item[0] || '-'}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item[1] || '-'}</td>
                    `;
                            tbody.appendChild(row);
                        });

                        document.getElementById('export_missing_barcode')
                            .addEventListener('click', function() {
                                let wb = XLSX.utils.book_new();
                                let ws_data = [
                                    ["Bin", "Barcode"],
                                    ...missingBarcodeData.map(item => [
                                        item[0] || '-',
                                        item[1] || '-'
                                    ])
                                ];
                                let ws = XLSX.utils.aoa_to_sheet(ws_data);
                                XLSX.utils.book_append_sheet(wb, ws, "Missing Barcodes");
                                XLSX.writeFile(wb, "Missing_Barcodes.xlsx");
                            });

                        document.getElementById('close_missing_alert')
                            .addEventListener('click', function() {
                                Swal.close();
                                if (callback) callback();
                            });
                    }
                });
            } else if (callback) {
                callback();
            }
        }

        function checkMissingBins(missingBinsData, callback) {
            if (missingBinsData && missingBinsData.length > 0) {
                Swal.fire({
                    title: 'Missing Bins Data',
                    html: `
                <div style="overflow-x:auto;">
                    <table class="table" style="width:100%; text-align:left; border-collapse: collapse;">
                    <thead>
                        <tr>
                        <th style="border: 1px solid #ccc; padding: 8px;">Bin Code</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Barcode</th>
                        </tr>
                    </thead>
                    <tbody id="missing-bins-table-body">
                        <!-- Data masuk sini -->
                    </tbody>
                    </table>
                    <br/>
                    <div style="text-align: center;">
                    <button id="export_missing_bins" class="swal2-confirm swal2-styled" style="background-color:#28a745; margin-right:10px;">Export ke Excel</button>
                    <button id="close_missing_bins_alert" class="swal2-cancel swal2-styled" style="background-color:#dc3545;">Tutup</button>
                    </div>
                </div>
                `,
                    icon: 'warning',
                    showConfirmButton: false,
                    didOpen: () => {
                        let tbody = document.getElementById('missing-bins-table-body');
                        missingBinsData.forEach(function(item) {
                            let row = document.createElement('tr');
                            row.innerHTML = `
                    <td style="border: 1px solid #ccc; padding: 8px;">${item[0] || '-'}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item[1] || '-'}</td>
                    `;
                            tbody.appendChild(row);
                        });

                        document.getElementById('export_missing_bins')
                            .addEventListener('click', function() {
                                let wb = XLSX.utils.book_new();
                                let ws_data = [
                                    ["Bin Code", "Barcode"],
                                    ...missingBinsData.map(item => [
                                        item[0] || '-',
                                        item[1] || '-'
                                    ])
                                ];
                                let ws = XLSX.utils.aoa_to_sheet(ws_data);
                                XLSX.utils.book_append_sheet(wb, ws, "Missing Bins");
                                XLSX.writeFile(wb, "Missing_Bins.xlsx");
                            });

                        document.getElementById('close_missing_bins_alert')
                            .addEventListener('click', function() {
                                Swal.close();
                                if (callback) callback();
                            });
                    }
                });
            } else if (callback) {
                callback();
            }
        }

        function checkMissingItemsOnBin(missingItemsData, callback) {
            if (missingItemsData && missingItemsData.length > 0) {
                Swal.fire({
                    title: 'Missing Items on Bin',
                    html: `
                <div style="overflow-x:auto;">
                    <table class="table" style="width:100%; text-align:left; border-collapse: collapse;">
                    <thead>
                        <tr>
                        <th style="border: 1px solid #ccc; padding: 8px;">Start Bin</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Barcode</th>
                        </tr>
                    </thead>
                    <tbody id="missing-items-table-body">
                        <!-- Data masuk sini -->
                    </tbody>
                    </table>
                    <br/>
                    <div style="text-align: center;">
                    <button id="export_missing_items" class="swal2-confirm swal2-styled" style="background-color:#28a745; margin-right:10px;">Export ke Excel</button>
                    <button id="close_missing_items_alert" class="swal2-cancel swal2-styled" style="background-color:#dc3545;">Tutup</button>
                    </div>
                </div>
                `,
                    icon: 'warning',
                    showConfirmButton: false,
                    didOpen: () => {
                        let tbody = document.getElementById('missing-items-table-body');
                        missingItemsData.forEach(function(item) {
                            let row = document.createElement('tr');
                            row.innerHTML = `
                    <td style="border: 1px solid #ccc; padding: 8px;">${item[0] || '-'}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item[1] || '-'}</td>
                    `;
                            tbody.appendChild(row);
                        });

                        document.getElementById('export_missing_items')
                            .addEventListener('click', function() {
                                let wb = XLSX.utils.book_new();
                                let ws_data = [
                                    ["Start Bin", "Barcode"],
                                    ...missingItemsData.map(item => [
                                        item[0] || '-',
                                        item[1] || '-'
                                    ])
                                ];
                                let ws = XLSX.utils.aoa_to_sheet(ws_data);
                                XLSX.utils.book_append_sheet(wb, ws, "Missing Items on Bin");
                                XLSX.writeFile(wb, "Missing_Items_On_Bin.xlsx");
                            });

                        document.getElementById('close_missing_items_alert')
                            .addEventListener('click', function() {
                                Swal.close();
                                if (callback) callback();
                            });
                    }
                });
            } else if (callback) {
                callback();
            }
        }

        function checkQtyInvalid(invalidQtyData, callback) {
            if (invalidQtyData && invalidQtyData.length > 0) {
                Swal.fire({
                    title: 'Invalid Quantity Data',
                    html: `
                <div style="overflow-x:auto;">
                    <table class="table" style="width:100%; text-align:left; border-collapse: collapse;">
                    <thead>
                        <tr>
                        <th style="border: 1px solid #ccc; padding: 8px;">Start Bin</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Barcode</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Qty Req</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Qty Sys</th>
                        </tr>
                    </thead>
                    <tbody id="qty-invalid-table-body">
                        <!-- Data masuk sini -->
                    </tbody>
                    </table>
                    <br/>
                    <div style="text-align: center;">
                    <button id="export_qty_invalid" class="swal2-confirm swal2-styled" style="background-color:#28a745; margin-right:10px;">Export ke Excel</button>
                    <button id="close_qty_invalid_alert" class="swal2-cancel swal2-styled" style="background-color:#dc3545;">Tutup</button>
                    </div>
                </div>
                `,
                    icon: 'warning',
                    showConfirmButton: false,
                    didOpen: () => {
                        let tbody = document.getElementById('qty-invalid-table-body');
                        invalidQtyData.forEach(function(item) {
                            let row = document.createElement('tr');
                            row.innerHTML = `
                    <td style="border: 1px solid #ccc; padding: 8px;">${item[0] || '-'}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item[1] || '-'}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item[2] || '-'}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item[3] || '-'}</td>
                    `;
                            tbody.appendChild(row);
                        });

                        document.getElementById('export_qty_invalid')
                            .addEventListener('click', function() {
                                let wb = XLSX.utils.book_new();
                                let ws_data = [
                                    ["Start Bin", "Barcode", "Quantity Req",
                                        "Quantity Sistem"
                                    ], // Header
                                    ...invalidQtyData.map(item => [
                                        item[0] || '-',
                                        item[1] || '-',
                                        item[2] || '-',
                                        item[3] || '-'
                                    ])
                                ];
                                let ws = XLSX.utils.aoa_to_sheet(ws_data);
                                XLSX.utils.book_append_sheet(wb, ws, "Invalid Quantity");
                                XLSX.writeFile(wb, "Invalid_Quantity_Data.xlsx");
                            });

                        document.getElementById('close_qty_invalid_alert')
                            .addEventListener('click', function() {
                                Swal.close();
                                if (callback) callback();
                            });
                    }
                });
            } else if (callback) {
                callback();
            }
        }

        $('#saveMutationMultiBinBtn').on('click', function(e) {
            e.preventDefault();
            $('#saveMutationMultiBinBtn').html('Proses...');
            $('#saveMutationMultiBinBtn').attr('disabled', true);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: "{{ url('multibins_mutation') }}",
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#saveMutationMultiBinBtn').html('Submit');
                    $('#saveMutationMultiBinBtn').attr('disabled', false);

                    if (data.status == '200') {
                        toastr.success('Mutasi berhasil disimpan', 'Berhasil');
                        temp_multibin_data.draw();
                        start_bin_table.draw();
                        end_bin_table.draw();
                        bin_history_table.draw();
                    } else {
                        toastr.error(data.message ||
                            'Terjadi kesalahan saat menyimpan mutasi', 'Error');
                    }
                },
                error: function(data) {
                    toastr.error('An error occurred while processing your request',
                        'Error');
                    $('#saveMutationMultiBinBtn').html('Submit');
                    $('#saveMutationMultiBinBtn').attr('disabled', false);
                }
            });
        });

        //BUTTON  MUTATION DI TABEL A/KIRI
        $('#mutation_btn').on('click', function() {
            // Check if any mutation qty exceeds available stock
            var hasInvalidQty = false;
            var invalidItems = [];

            $('input[data-mutation-qty]').each(function() {
                var mutationQty = parseInt($(this).val()) || 0;
                var availableQty = parseInt($(this).attr('data-qty')) || 0;

                if (mutationQty > 0 && mutationQty > availableQty) {
                    hasInvalidQty = true;
                    var ps_barcode = $(this).closest('tr').find('td').eq(2).find('a').eq(2)
                        .text(); // third row, third <a> element
                    invalidItems.push({
                        ps_barcode: ps_barcode,
                        mutation: mutationQty,
                        available: availableQty
                    });
                }
            });

            if (hasInvalidQty) {
                Swal.fire({
                    title: 'Qty Tidak Valid',
                    html: `
                <div style="overflow-x:auto;">
                    <table class="table" style="width:100%; text-align:left; border-collapse: collapse;">
                    <thead>
                        <tr>
                        <th style="border: 1px solid #ccc; padding: 8px;">SKU</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Qty Sistem</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Qty Mutasi</th>
                        </tr>
                    </thead>
                    <tbody id="invalid-qty-table-body">
                        <!-- Data masuk sini -->
                    </tbody>
                    </table>
                    <br/>
                    <div style="text-align: center;">
                    <button id="export_invalid_qty" class="swal2-confirm swal2-styled" style="background-color:#28a745; margin-right:10px;">Export ke Excel</button>
                    <button id="close_invalid_alert" class="swal2-cancel swal2-styled" style="background-color:#dc3545;">Tutup</button>
                    </div>
                </div>
                `,
                    icon: 'warning',
                    showConfirmButton: false,
                    didOpen: () => {
                        let tbody = document.getElementById('invalid-qty-table-body');
                        invalidItems.forEach(function(item) {
                            let row = document.createElement('tr');
                            row.innerHTML = `
                    <td style="border: 1px solid #ccc; padding: 8px;">${item.ps_barcode}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item.available}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item.mutation}</td>
                    `;
                            tbody.appendChild(row);
                        });

                        // Tombol Export Excel
                        document.getElementById('export_invalid_qty')
                            .addEventListener('click', function() {
                                let wb = XLSX.utils.book_new();
                                let ws_data = [
                                    ["SKU", "Qty Sistem", "Qty Mutasi"], // Header
                                    ...invalidItems.map(item => [
                                        item.ps_barcode,
                                        item.available,
                                        item.mutation
                                    ])
                                ];
                                let ws = XLSX.utils.aoa_to_sheet(ws_data);
                                XLSX.utils.book_append_sheet(wb, ws, "Invalid Qty");
                                XLSX.writeFile(wb, "Invalid_Qty_Data.xlsx");
                            });

                        // Tombol Tutup
                        document.getElementById('close_invalid_alert')
                            .addEventListener('click', function() {
                                Swal.close();
                            });
                    }
                });
                return false;
            }

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
                date + "&search=" + search + "&history_bin_start=" + history_bin_start +
                "&history_bin_end=" + history_bin_end;

            $('#export_btn').text('Export Data');
            $('#export_btn').removeClass('disabled');
        });

        $('#multibin_btn').on('click', function() {
            jQuery.noConflict();
            $('#MutationMultiBinModal').modal('show');
            temp_multibin_data.draw();
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
