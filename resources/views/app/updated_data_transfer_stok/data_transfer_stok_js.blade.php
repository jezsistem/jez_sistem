<script>
$(document).ready(function() {
    var modal_opened = null;

    function initializeScanner(elementId) {
        return new Html5QrcodeScanner(elementId, {
            qrbox: { width: 250, height: 250 },
            fps: 30,
        });
    }

    var scanner_tf_receive = initializeScanner('reader_tf_receive');
    var scan_timer = null;

    function success(result) {
        if (scan_timer) {
            clearTimeout(scan_timer);
        }
        scan_timer = setTimeout(function() {
            var hasil = result;
            if (hasil.startsWith(']C1')) {
                hasil = hasil.replace(']C1', '');
            }
            if (modal_opened == 'StockTransferDataModal') {
                $('#scan_result').val(hasil);
                processScanResult(hasil);
                $('#scan_result').val('');
            }
        }, 500);
    }

    function error(err) {
        // console.error(err);
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Stock Transfer Data Table
    var stock_transfer_data_table = $('#StockTransferDatatb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"flex justify-between items-center mb-4"l><"overflow-x-auto"t><"flex justify-between items-center mt-4"ip>',
        buttons: [{
            "extend": 'excelHtml5',
            "text": 'Excel',
            "className": 'btn btn-primary btn-xs'
        }],
        ajax: {
            url: "{{ url('transfer_data_datatables') }}",
            data: function(d) {
                d.search = $('#stock_transfer_search').val();
                d.transfer_receive_date = $('#transfer_receive_date').val();
                d.st_id_start = $('#st_id_start').val();
                d.st_id_end = $('#st_id_end').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'stf_id', searchable: false },
            { data: 'stf_code_show', name: 'stf_code' },
            { data: 'u_name', name: 'u_name', orderable: false },
            { data: 'qty', name: 'qty', orderable: false },
            { data: 'start_store', name: 'start_store', orderable: false },
            { data: 'end_store', name: 'end_store', orderable: false },
            { data: 'u_name_receive', name: 'u_name_receive', orderable: false },
            { data: 'stf_created', name: 'stf_created' },
            { data: 'stf_status', name: 'stf_status' },
        ],
        columnDefs: [{
            "targets": 0,
            "className": "text-center",
            "width": "0%"
        }],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            "lengthMenu": "_MENU_",
            "processing": '<div class="flex justify-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>',
        },
        order: [[0, 'desc']],
    });

    // Stock Transfer Data Accept Table
    var stock_transfer_data_accept_table = $('#StockTransferDataAccepttb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"flex justify-between items-center mb-4"l><"overflow-x-auto"t><"flex justify-between items-center mt-4"ip>',
        buttons: [{
            "extend": 'excelHtml5',
            "text": 'Excel',
            "className": 'btn btn-primary btn-xs'
        }],
        ajax: {
            url: "{{ url('transfer_data_accept_datatables') }}",
            data: function(d) {
                d.stf_code = $('#stf_code_label').val();
                d.search = $('#stock_transfer_receive_search').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'stfd_id', searchable: false },
            { data: 'br_name', name: 'br_name' },
            { data: 'ps_barcode', name: 'ps_barcode' },
            { data: 'article', name: 'p_name' },
            { data: 'stfd_qty', name: 'stfd_qty' },
            { data: 'accept_total', name: 'accept_total', orderable: false },
            { data: 'accept', name: 'accept', orderable: false },
        ],
        columnDefs: [{
            "targets": 0,
            "className": "text-center",
            "width": "0%"
        }],
        pageLength: -1,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            "lengthMenu": "_MENU_",
        },
        order: [[0, 'desc']],
    });

    // History Table
    var history_table = $('#Historytb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"flex justify-between items-center mb-4"lB><"overflow-x-auto"t><"flex justify-between items-center mt-4"ip>',
        buttons: [{
            "extend": 'excelHtml5',
            "text": 'Excel',
            "className": 'btn btn-primary btn-xs'
        }],
        ajax: {
            url: "{{ url('transfer_data_history_datatables') }}",
            data: function(d) {
                d.search = $('#history_search').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false },
            { data: 'stf_code', name: 'stf_code' },
            { data: 'st_start', name: 'st_start', orderable: false },
            { data: 'st_name', name: 'st_name' },
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'p_color', name: 'p_color' },
            { data: 'sz_name', name: 'sz_name' },
            { data: 'stfds_qty', name: 'stfds_qty' },
            { data: 'hb', name: 'hb', orderable: false },
            { data: 'hj', name: 'hj', orderable: false },
            { data: 'u_name', name: 'u_name' },
            { data: 'created_at', name: 'created_at' }
        ],
        columnDefs: [{
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

    // Search handlers
    $('#stock_transfer_search').keyup(function() {
        stock_transfer_data_table.draw();
    });

    $('#stock_transfer_receive_search').keyup(function() {
        stock_transfer_data_accept_table.draw();
    });

    $('#history_search').keyup(function() {
        history_table.draw();
    });

    // Accept Qty Change
    $(document).delegate('#accept_qty', 'change', function() {
        var transfer_qty = $(this).attr('data-stfd_qty');
        var accept_qty = $(this).val();
        if (parseInt(accept_qty) > parseInt(transfer_qty)) {
            $(this).val('');
            swal('Jumlah', 'Jumlah yang diterima tidak boleh melebihi jumlah yang ditransfer', 'error');
            return false;
        }
        if (parseInt(accept_qty) < 0) {
            $(this).val('');
            swal('Minus', 'Jumlah yang diterima tidak boleh kurang dari 0', 'error');
            return false;
        }
    });

    // Export Button
    $('#ExportBtn').on('click', function() {
        var stf_code = $('#stf_code_label').val();
        window.location.href = `/export-stock-transfer/${stf_code}`;
    });

    // Accept Qty Button
    $('#accept_qty_btn').on('click', function() {
        swal({
            title: "Terima..?",
            text: "Yakin jumlah sudah sesuai untuk diterima ?",
            icon: "info",
            buttons: ['Batal', 'Yakin'],
            dangerMode: false,
        }).then(function(isConfirm) {
            if (isConfirm) {
                $(this).addClass('disabled');
                var arr = [];
                var i = 0;
                var stf_id = $('#stf_id').val();
                var st_id_end = $('#st_id_end_modal').val();
                var isValid = true;
                
                $('.accept_qty').each(function() {
                    var stfd_id = $(this).attr('data-stfd_id');
                    var pst_id = $(this).attr('data-pst_id');
                    var accept_qty = $(this).val();
                    if (accept_qty == '') {
                        return true;
                    }
                    arr[i++] = [stfd_id, pst_id, accept_qty];
                    var transfer_qty = $(this).attr('data-stfd_qty');
                    if (parseInt(accept_qty) > parseInt(transfer_qty)) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    swal('Error', 'Jumlah yang diterima harus sama dengan jumlah yang ditransfer', 'error');
                    return false;
                }
                if (arr.length <= 0) {
                    swal('Qty kosong', 'silahkan tentukan qty yang diterima', 'warning');
                    return false;
                }
                
                $.ajax({
                    type: "POST",
                    data: { _stf_id: stf_id, _st_id_end: st_id_end, _arr: arr },
                    dataType: 'json',
                    url: "{{ url('stock_transfer_accept') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            stock_transfer_data_table.draw();
                            stock_transfer_data_accept_table.draw();
                            $(this).removeClass('disabled');
                            swal("Berhasil", "Data berhasil diterima", "success");
                        } else {
                            $(this).removeClass('disabled');
                            swal('Gagal', 'Gagal terima data', 'error');
                        }
                    }
                });
                return false;
            }
        });
    });

    // Row Click Handler
    $('#StockTransferDatatb tbody').on('click', 'tr', function() {
        var rowData = stock_transfer_data_table.row(this).data();
        if (!rowData) return;
        
        var stf_id = rowData.stf_id;
        var stf_code = rowData.stf_code;
        var st_id_end = rowData.st_id_end;
        
        $('#stf_id').val(stf_id);
        $('#stf_code_label').val(stf_code);
        $('#st_id_end_modal').val(st_id_end);
        
        openStockTransferDataModal();
        stock_transfer_data_accept_table.draw();
        modal_opened = 'StockTransferDataModal';
        scanner_tf_receive.render(success, error);
    });

    // Export All Button
    $(document).delegate('#std_export_all_btn', 'click', function(e) {
        e.preventDefault();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        if (start_date == '') {
            swal('Tanggal Awal', 'Tentukan', 'warning');
            return false;
        }
        if (end_date == '') {
            swal('Tanggal Akhir', 'Tentukan', 'warning');
            return false;
        }
        window.location.href = "{{ url('std_export') }}?start=" + start_date + "&end=" + end_date;
    });

    // Import Modal Button
    $("#ImportModalBtn").click(function() {
        openImportModal();
    });

    function handleAcceptQtyChange(element) {
        var stfd_id = $(element).attr('data-stfd_id');
        var stfd_qty = $(element).attr('data-stfd_qty');
        var accept_qty = $(element).val();
        var stf_id = $('#stf_id').val();

        if (parseInt(accept_qty) > parseInt(stfd_qty)) {
            $(element).val('');
            toastr.error('Jumlah yang diterima tidak boleh melebihi jumlah yang ditransfer');
            return false;
        }
        if (parseInt(accept_qty) < 0) {
            $(element).val('');
            toastr.error('Jumlah yang diterima tidak boleh kurang dari 0');
            return false;
        }

        $.ajax({
            type: 'POST',
            url: "{{ url('temp_change_stock_transfer_accept') }}",
            data: {
                _stfd_id: stfd_id,
                _stfd_qty: stfd_qty,
                _stf_id: stf_id,
                _arr: [{ qty: accept_qty }]
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    toastr.success('Data berhasil disimpan sementara');
                } else {
                    toastr.error('Gagal menyimpan data sementara');
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat mengirim data');
            }
        });
    }

    $(document).delegate('input.accept_qty[data-stfd_id]', 'change', function() {
        handleAcceptQtyChange(this);
    });

    $('#scan_result').on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === 'Tab') {
            e.preventDefault();
            processScanResult($(this).val());
            $(this).val('');
        }
    });

    function processScanResult(scanValue) {
        if (scanValue.trim() === '') {
            toastr.warning('Scan result cannot be empty');
            return false;
        }
        var found = false;
        $('#StockTransferDataAccepttb tbody tr').each(function() {
            var rowData = stock_transfer_data_accept_table.row(this).data();
            if (rowData && rowData.ps_barcode === scanValue) {
                var inputField = $(this).find('input.accept_qty[data-stfd_id]');
                if (inputField.length === 0) {
                    toastr.error('Input field not found in the row');
                    return false;
                }
                var currentValue = parseInt(inputField.val()) || 0;
                var maxQty = parseInt(inputField.attr('data-stfd_qty')) || 0;
                if (currentValue + 1 > maxQty) {
                    toastr.error('Jumlah yang diterima tidak boleh melebihi jumlah yang ditransfer');
                    found = true;
                    return false;
                }
                inputField.val(currentValue + 1);
                handleAcceptQtyChange(inputField);
                found = true;
                return false;
            }
        });
        if (!found) {
            toastr.warning('Barcode not found in the table');
        }
    }

    // Import Form Submit
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        $('#import_data_btn').html('Proses...');
        $('#import_data_btn').attr('disabled', true);
        var formData = new FormData(this);
        formData.append('stf_code_label', $('#stf_code_label').val());
        formData.append('stf_id', $('#stf_id').val());

        $.ajax({
            type: 'POST',
            url: "{{ url('stock_transfer_import') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                if (data.status == '200') {
                    closeImportModal();
                    swal('Berhasil', 'Data berhasil diimport', 'success');
                    $('#f_import')[0].reset();
                    stock_transfer_data_accept_table.rows().every(function() {
                        var rowData = this.data();
                        var stfd_id = rowData.stfd_id;
                        var pst_id = rowData.pst_id;
                        var matchingData = data.data.processedData.find(function(item) {
                            return item.product_stock_id === pst_id;
                        });
                        if (matchingData) {
                            var acceptQtyInput = $('input.accept_qty[data-stfd_id="' + stfd_id + '"]');
                            acceptQtyInput.val(matchingData.qty);
                            if (parseInt(matchingData.qty) !== parseInt(rowData.stfd_qty)) {
                                $(this.node()).addClass('table-danger');
                                swal('Warning', 'Ada data yang tidak sesuai', 'warning');
                            } else {
                                $(this.node()).removeClass('table-danger');
                            }
                        }
                    });
                } else if (data.status == '400') {
                    closeImportModal();
                    $('#f_import')[0].reset();
                    swal('File', 'File yang anda import kosong atau format tidak tepat', 'warning');
                } else {
                    closeImportModal();
                    $('#f_import')[0].reset();
                    swal('Gagal', 'Silahkan periksa format input pada template anda, pastikan kolom biru terisi sesuai dengan sistem', 'warning');
                }
            },
            error: function(data) {
                swal('Error', data, 'error');
            }
        });
    });

    // Select2 Initialization
    $('#st_id_start').select2({
        width: "100%",
        dropdownParent: $('#st_id_start_parent')
    });
    $('#st_id_start').on('select2:open', function(e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });

    $('#st_id_end').select2({
        width: "100%",
        dropdownParent: $('#st_id_end_parent')
    });
    $('#st_id_end').on('select2:open', function(e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });

    $('#st_id_start').on('change', function() {
        stock_transfer_data_table.draw();
    });

    $('#st_id_end').on('change', function() {
        stock_transfer_data_table.draw();
    });

    // Date Range Picker
    var picker = $('#kt_dashboard_daterangepicker');
    if ($('#kt_dashboard_daterangepicker').length > 0) {
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
            } else if (label == 'All Days') {
                title = 'All Days';
                hidden_range = '';
            } else {
                range = start.format('MMM D') + ' - ' + end.format('MMM D');
                hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }
            $('#transfer_receive_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            stock_transfer_data_table.draw();
        }

        picker.daterangepicker({
            startDate: start,
            endDate: end,
            opens: 'left',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            ranges: {
                'All Days': [null, null],
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end, '');
    }
});

// Toggle Export Dropdown
function toggleExportDropdown(dropdownId) {
    var dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleExportDropdown"]') && !event.target.closest('#export-dropdown-std')) {
        var dropdown = document.getElementById('export-dropdown-std');
        if (dropdown) {
            dropdown.classList.add('hidden');
        }
    }
});
</script>
