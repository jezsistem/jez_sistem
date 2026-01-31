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
    // Placeholder function
}

function saveMutation(pls_id, index, pst_id, pls_qty, note) {
    var pl_id_end = $('#pl_id_end').val();
    var pmt_qty = $('.mutation_qty' + index).val();
    if (pmt_qty == 0 || pmt_qty == '') {
        return true;
    }
    
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
                // Success
            } else {
                toastr.error('Ada error, info ke programmer', 'Error');
            }
        }
    });
    return false;
}

// Button cancel handler
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
                    if (temp_multibin_data) {
                        temp_multibin_data.draw();
                    }
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
    handleCancel('{{ url('cancel_import') }}', 'Cancel berhasil', 'Terjadi kesalahan saat melakukan cancel');
});

$('#clearMutationMultiBinBtn').on('click', function() {
    handleCancel('{{ url('cancel_import') }}', 'Cancel berhasil', 'Terjadi kesalahan saat melakukan cancel');
});

// Button import
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

            if (data.status == '200') {
                toastr.success('Data berhasil diimport', 'Berhasil');
                $('#f_import')[0].reset();
                closeImportModal();
                start_bin_table.draw();
                checkMissingBarcode(data.data['missingBarcode']);
            } else if (data.status == '400') {
                closeImportModal();
                Swal.fire({
                    title: 'File Error',
                    text: 'File yang anda import kosong atau format tidak tepat',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            } else {
                closeImportModal();
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
            $("#import_data_btn").html('Import');
            $("#import_data_btn").attr("disabled", false);
        }
    });
});

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
                        <tbody id="barcode-table-body"></tbody>
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

                document.getElementById('export_missing_barcode').addEventListener('click', function() {
                    let wb = XLSX.utils.book_new();
                    let ws_data = [
                        ["Bin", "Barcode"],
                        ...missingBarcodeData.map(item => [item[0] || '-', item[1] || '-'])
                    ];
                    let ws = XLSX.utils.aoa_to_sheet(ws_data);
                    XLSX.utils.book_append_sheet(wb, ws, "Missing Barcodes");
                    XLSX.writeFile(wb, "Missing_Barcodes.xlsx");
                });

                document.getElementById('close_missing_alert').addEventListener('click', function() {
                    Swal.close();
                });
            }
        });
    }
}

$('#ImportModalBtn').on('click', function() {
    openImportModal();
});

$(document).ready(function() {
    loadStartEnd();
    
    $(document).ajaxStop(function() {
        $(document).off('ajaxStop');
    });
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Start bin table
    start_bin_table = $('#StartBintb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"flex items-center justify-between mb-4"<"text-sm"l>>rt<"flex items-center justify-between mt-4"<"text-sm text-gray-600"i><"text-sm"p>>',
        ajax: {
            url: "{{ url('start_bin_datatables') }}",
            data: function(d) {
                d.pl_id = $('#pl_id_start').val();
                d.search = $('#article_search').val();
                d.st_id = $('#st_id_filter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'pls_id', searchable: false },
            { data: 'article', name: 'article', orderable: false },
            { data: 'qty', name: 'qty', orderable: false },
            { data: 'action', name: 'action', orderable: false },
        ],
        columnDefs: [
            { "targets": 0, "className": "text-center px-4 py-3 text-sm", "width": "5%" },
            { "targets": 1, "className": "px-4 py-3 text-sm" },
            { "targets": 2, "className": "text-center px-4 py-3 text-sm" },
            { "targets": 3, "className": "text-center px-4 py-3 text-sm" }
        ],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            "lengthMenu": "_MENU_",
            "processing": '<div class="flex items-center justify-center py-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>',
            "emptyTable": "Tidak ada data",
            "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            "infoEmpty": "Tidak ada data",
            "paginate": {
                "first": "<<",
                "last": ">>",
                "next": ">",
                "previous": "<"
            }
        },
        order: [[0, 'desc']],
        drawCallback: function(settings) {
            var api = this.api();
            var data = api.rows({ page: 'current' }).data();
            if (data.length > 0) {
                var firstPlId = data[0].pl_id;
                if (firstPlId) {
                    var $select = $('#pl_id_start');
                    if ($select.length && $select.val() != firstPlId) {
                        $select.val(firstPlId).trigger('change').trigger('select2:select');
                        if (typeof $.fn.select2 !== 'undefined') {
                            $select.select2('close');
                        }
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
        }, 500);
    });

    // End bin table
    end_bin_table = $('#EndBintb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"flex items-center justify-between mb-4"<"text-sm"l>>rt<"flex items-center justify-between mt-4"<"text-sm text-gray-600"i><"text-sm"p>>',
        ajax: {
            url: "{{ url('end_bin_datatables') }}",
            data: function(d) {
                d.pl_id = $('#pl_id_end').val();
                d.search = $('#article_end_search').val();
                d.st_id = $('#st_id_filter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'pls_id', searchable: false },
            { data: 'article', name: 'article', orderable: false },
            { data: 'qty', name: 'qty', orderable: false },
        ],
        columnDefs: [
            { "targets": 0, "className": "text-center px-4 py-3 text-sm", "width": "5%" },
            { "targets": 1, "className": "px-4 py-3 text-sm" },
            { "targets": 2, "className": "text-center px-4 py-3 text-sm" }
        ],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            "lengthMenu": "_MENU_",
            "processing": '<div class="flex items-center justify-center py-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>',
            "emptyTable": "Tidak ada data",
            "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            "infoEmpty": "Tidak ada data",
            "paginate": {
                "first": "<<",
                "last": ">>",
                "next": ">",
                "previous": "<"
            }
        },
        order: [[0, 'desc']],
    });

    $('#article_end_search').on('keyup', function() {
        end_bin_table.draw();
    });

    // History table
    bin_history_table = $('#BinHistorytb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"flex items-center justify-between mb-4"<"text-sm"l>>rt<"flex items-center justify-between mt-4"<"text-sm text-gray-600"i><"text-sm"p>>',
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
        columns: [
            { data: 'DT_RowIndex', name: 'pmt_id', searchable: false },
            { data: 'u_name', name: 'u_name' },
            { data: 'article', name: 'article', orderable: false },
            { data: 'ps_barcode', name: 'ps_barcode', orderable: false },
            { data: 'st_name', name: 'st_name' },
            { data: 'start_bin', name: 'start_bin', orderable: false },
            { data: 'pmt_old_qty', name: 'pmt_old_qty', orderable: false },
            { data: 'pmt_qty', name: 'pmt_qty', orderable: false },
            { data: 'pmt_old_qty_new', name: 'pmt_old_qty_new', orderable: false },
            { data: 'end_bin', name: 'end_bin', orderable: false },
            { data: 'notes', name: 'notes', orderable: false },
            { data: 'pm_created_at', name: 'pm_created_at', orderable: false },
        ],
        columnDefs: [
            { "targets": 0, "className": "text-center px-4 py-3 text-sm", "width": "3%" },
            { "targets": [1,2,3,4,5,6,7,8,9,10,11], "className": "px-4 py-3 text-sm" }
        ],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            "lengthMenu": "_MENU_",
            "processing": '<div class="flex items-center justify-center py-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>',
            "emptyTable": "Tidak ada data",
            "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            "infoEmpty": "Tidak ada data",
            "paginate": {
                "first": "<<",
                "last": ">>",
                "next": ">",
                "previous": "<"
            }
        },
        order: [[0, 'desc']],
    });

    // Multibin table
    temp_multibin_data = $('#mutationMultiBinTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"flex items-center justify-between mb-4"<"text-sm"l>>rt<"flex items-center justify-between mt-4"<"text-sm text-gray-600"i><"text-sm"p>>',
        ajax: {
            url: "{{ url('temp_multibins_datatable') }}",
        },
        columns: [
            { data: 'DT_RowIndex', name: 'pls_id', searchable: false },
            { data: 'ps_barcode', name: 'ps_barcode', orderable: false },
            { data: 'start_bin', name: 'start_bin', orderable: false },
            { data: 'end_bin', name: 'end_bin', orderable: false },
            { data: 'start_qty', name: 'start_qty', orderable: false },
            { data: 'mutation_qty', name: 'mutation_qty', orderable: false },
            { data: 'notes', name: 'notes', defaultContent: '-', orderable: false },
        ],
        columnDefs: [
            { "targets": 0, "className": "text-center px-4 py-3 text-sm", "width": "5%" }
        ],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            "lengthMenu": "_MENU_",
            "processing": '<div class="flex items-center justify-center py-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>',
            "emptyTable": "Tidak ada data",
            "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            "infoEmpty": "Tidak ada data"
        },
        order: [[0, 'desc']],
    });

    $('#history_search').on('keyup', function() {
        bin_history_table.draw();
    });

    $('#st_id_filter').on('change', function() {
        start_bin_table.draw();
        end_bin_table.draw();
        bin_history_table.draw();
    });

    // Mutation qty validation
    $(document).delegate('#mutation_qty', 'change', function(e) {
        var pls_qty = $(this).attr('data-qty');
        var qty = $(this).val();
        if (parseInt(qty) > parseInt(pls_qty)) {
            Swal.fire('Qty', 'Melebihi batas stok', 'warning');
            $(this).val('');
            return false;
        } else if (parseInt(qty) < 0) {
            Swal.fire('Qty', 'Tidak boleh minus', 'warning');
            $(this).val('');
            return false;
        }
    });

    // Multibin form submit
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
                $('#mutationMultiBinSubmitBtn').html('Import');
                $('#mutationMultiBinSubmitBtn').attr('disabled', false);

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
                } else {
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
                $('#mutationMultiBinSubmitBtn').html('Import');
                $('#mutationMultiBinSubmitBtn').attr('disabled', false);
            }
        });
    });

    // Save multibin mutation
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
                $('#saveMutationMultiBinBtn').html('Mutasi');
                $('#saveMutationMultiBinBtn').attr('disabled', false);

                if (data.status == '200') {
                    toastr.success('Mutasi berhasil disimpan', 'Berhasil');
                    temp_multibin_data.draw();
                    start_bin_table.draw();
                    end_bin_table.draw();
                    bin_history_table.draw();
                } else {
                    toastr.error(data.message || 'Terjadi kesalahan saat menyimpan mutasi', 'Error');
                }
            },
            error: function(data) {
                toastr.error('An error occurred while processing your request', 'Error');
                $('#saveMutationMultiBinBtn').html('Mutasi');
                $('#saveMutationMultiBinBtn').attr('disabled', false);
            }
        });
    });

    // Mutation button
    $('#mutation_btn').on('click', function() {
        var hasInvalidQty = false;
        var invalidItems = [];

        $('input[data-mutation-qty]').each(function() {
            var mutationQty = parseInt($(this).val()) || 0;
            var availableQty = parseInt($(this).attr('data-qty')) || 0;

            if (mutationQty > 0 && mutationQty > availableQty) {
                hasInvalidQty = true;
                var ps_barcode = $(this).closest('tr').find('td').eq(2).find('a').eq(2).text();
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
                            <tbody id="invalid-qty-table-body"></tbody>
                        </table>
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
                }
            });
            return false;
        }

        Swal.fire({
            title: 'Mutasi..?',
            text: 'Yakin mutasi data ?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yakin',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).addClass('opacity-50 cursor-not-allowed');
                var total_row = $('input[data-mutation-qty]').length;
                var start_bin = $('#pl_id_start').val();
                var end_bin = $('#pl_id_end').val();
                var finish = '';
                
                if (end_bin == '') {
                    Swal.fire('BIN Tujuan', 'Pilih BIN tujuan', 'warning');
                    $(this).removeClass('opacity-50 cursor-not-allowed');
                    return false;
                }
                if (start_bin == end_bin) {
                    Swal.fire('BIN Sama', 'BIN awal dan BIN tujuan tidak boleh sama, silahkan ganti BIN tujuan', 'warning');
                    $(this).removeClass('opacity-50 cursor-not-allowed');
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
                    $(this).removeClass('opacity-50 cursor-not-allowed');
                    toastr.success('Mutasi berhasil', 'Berhasil');
                }
            }
        });
    });

    // Export history
    $(document).delegate('#export_btn', 'click', function(e) {
        e.preventDefault();
        $('#export_btn').html('<i class="fas fa-spinner fa-spin mr-2"></i>Mohon Tunggu..');
        $('#export_btn').addClass('opacity-50 cursor-not-allowed');
        
        var date = history_date;
        var st_id = $('#st_id_filter').val();
        var search = $('#history_search').val();
        var history_bin_start = $('#history_pl_id_start').val();
        var history_bin_end = $('#history_pl_id_end').val();
        
        window.location.href = "{{ url('history_setup_export') }}?&st_id=" + st_id + "&date=" + date + "&search=" + search + "&history_bin_start=" + history_bin_start + "&history_bin_end=" + history_bin_end;

        setTimeout(function() {
            $('#export_btn').html('<i class="fas fa-file-export mr-2"></i>Export Data');
            $('#export_btn').removeClass('opacity-50 cursor-not-allowed');
        }, 2000);
    });

    // Multibin modal
    $('#multibin_btn').on('click', function() {
        openMutationMultiBinModal();
        temp_multibin_data.draw();
    });

    // Date range picker
    var picker = $('#kt_dashboard_daterangepicker');
    if (picker.length > 0) {
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
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            bin_history_table.draw();
        }

        picker.daterangepicker({
            startDate: start,
            endDate: end,
            opens: 'left',
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
        }, cb);
        
        cb(start, end, '');
    }
});
</script>
