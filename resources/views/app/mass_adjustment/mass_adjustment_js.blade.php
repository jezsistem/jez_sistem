<script src="{{ asset('app') }}/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<script>
    var dashboard_date = '';
    var st_id = $('#st_filter').val();
    var psc_id = $('#psc_filter').val();
    var br_id = $('#br_filter').val();
    var qty_filter = $('#qty_filter').val();
    var pl_id = [];

    function loadLocation(st_id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: "{{ url('load_mass_adjustment_location') }}",
            data: {
                st_id: st_id,
                pl_id: pl_id
            },
            dataType: 'html',
            success: function(r) {
                $('#bin_panel').html(r);
            },

        });
    }

    function addCommas(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    function loadAsset(st_id, psc_id, br_id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: "{{ url('load_mass_asset') }}",
            data: {
                st_id: st_id,
                psc_id: psc_id,
                br_id: br_id,
                pl_id: pl_id,
                qty_filter: qty_filter
            },
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    $('#cc_qty').text(r.cc_qty);
                    $('#c_qty').text(r.c_qty);
                    $('#cc_value').text(r.cc_value);
                    $('#c_value').text(r.c_value);
                }
            },

        });
    }

    function loadApproval() {
        $('#approval_label').attr('data-id', '');
        $('#approval_label').val('');
        $('#mad_panel').addClass('d-none');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: "{{ url('load_mass_approval') }}",
            data: {
                ma_id: $('#ma_code').attr('data-id')
            },
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    $('#mad_panel').removeClass('d-none');
                    $('#approval_label').attr('data-id', r.approval);
                    $('#approval_label').val(r.approval_label);
                }
            },

        });
    }

    function exportTable() {
        var data = new FormData();
        data.append('st_id', st_id);
        data.append('psc_id', psc_id);
        data.append('br_id', br_id);
        data.append('pl_id', pl_id);
        data.append('qty_filter', qty_filter);
        window.location.href = "{{ url('export_mass_adjustment_template') }}?st_id=" + st_id + "&psc_id=" + psc_id +
            "&br_id=" + br_id + "&pl_id=" + pl_id + "&qty_filter=" + qty_filter + "";

        // $.ajaxSetup({
        //     headers: {
        //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     }
        // });
        // $.ajax({
        //     type:'POST',
        //     url: "{{ url('export_mass_adjustment_template') }}",
        //     data: data,
        //     cache:false,
        //     contentType: false,
        //     processData: false,
        //     xhrFields: {
        //         responseType: 'blob'
        //     },
        //     success: function(blob, status, xhr) {
        //         var filename = "";
        //         var disposition = xhr.getResponseHeader('Content-Disposition');
        //         if (disposition && disposition.indexOf('attachment') !== -1) {
        //             var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
        //             var matches = filenameRegex.exec(disposition);
        //             if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
        //         }

        //         if (typeof window.navigator.msSaveBlob !== 'undefined') {
        //             window.navigator.msSaveBlob(blob, filename);
        //         } else {
        //             var URL = window.URL || window.webkitURL;
        //             var downloadUrl = URL.createObjectURL(blob);

        //             if (filename) {
        //                 var a = document.createElement("a");
        //                 if (typeof a.download === 'undefined') {
        //                     window.location.href = downloadUrl;
        //                 } else {
        //                     a.href = downloadUrl;
        //                     a.download = filename;
        //                     document.body.appendChild(a);
        //                     a.click();
        //                 }
        //             } else {
        //                 window.location.href = downloadUrl;
        //             }
        //             setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 10000);
        //         }
        //     },
        // });
    }

    function exportResult() {
        var data = new FormData();
        data.append('ma_id', $('#ma_code').attr('data-id'));
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: "{{ url('export_mass_adjustment_result') }}",
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            xhrFields: {
                responseType: 'blob'
            },
            success: function(blob, status, xhr) {
                var filename = "";
                var disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                }

                if (typeof window.navigator.msSaveBlob !== 'undefined') {
                    window.navigator.msSaveBlob(blob, filename);
                } else {
                    var URL = window.URL || window.webkitURL;
                    var downloadUrl = URL.createObjectURL(blob);

                    if (filename) {
                        var a = document.createElement("a");
                        if (typeof a.download === 'undefined') {
                            window.location.href = downloadUrl;
                        } else {
                            a.href = downloadUrl;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                        }
                    } else {
                        window.location.href = downloadUrl;
                    }
                    setTimeout(function() {
                        URL.revokeObjectURL(downloadUrl);
                    }, 10000);
                }
            },
        });
    }

    $(document).ready(function() {
        loadAsset(st_id, psc_id, br_id);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var stock_table = $('#Stocktb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('mass_stock_datatables') }}",
                data: function(d) {
                    d.search = $('#stock_search').val();
                    d.st_id = st_id;
                    d.psc_id = psc_id;
                    d.br_id = br_id;
                    d.pl_id = pl_id;
                    d.qty_filter = qty_filter;
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'pl_code',
                    name: 'pl_code'
                },
                {
                    data: 'br_name',
                    name: 'br_name'
                },
                {
                    data: 'p_name',
                    name: 'p_name'
                },
                {
                    data: 'p_color',
                    name: 'p_color'
                },
                {
                    data: 'sz_name',
                    name: 'sz_name'
                },
                {
                    data: 'psc_name',
                    name: 'psc_name'
                },
                {
                    data: 'pls_qty',
                    name: 'pls_qty'
                },
                {
                    data: 'purchase',
                    name: 'purchase'
                },
                {
                    data: 'sell',
                    name: 'sell',
                    orderable: false
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [
                [0, 'desc']
            ],
        });

        var mass_adjustment_table = $('#MassAdjustmenttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [{
            "extend": 'excelHtml5',
            "text": 'Excel',
            "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
            url: "{{ url('mass_adjustment_datatables') }}",
            type: 'POST', // Added method POST
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for POST
            },
            data: function(d) {
                d.search = $('#ma_search').val();
                d.filter = $('#filter_status').val();
                d.st_id = st_id; // Include st_id in the request
            }
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'id',
                searchable: false
            },
            {
                data: 'ma_code_show',
                name: 'ma_code'
            },
            {
                data: 'st_name',
                name: 'st_name'
            },
            {
                data: 'u_name',
                name: 'u_name'
            },
            {
                data: 'approve',
                name: 'approve',
                orderable: false
            },
            {
                data: 'executor',
                name: 'executor',
                orderable: false
            },
            {
                data: 'editor',
                name: 'editor',
                orderable: false
            },
            {
                data: 'note',
                name: 'note',
                orderable: false
            },
            {
                data: 'tipe',
                name: 'tipe',
                orderable: false
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: 'updated_at',
                name: 'updated_at'
            },
            {
                data: 'ma_status',
                name: 'ma_status'
            },
            {
                data: 'action',
                name: 'action'
            },
            ],
            columnDefs: [{
            "targets": 0,
            "className": "text-center",
            "width": "0%"
            }],
            order: [
            [0, 'desc']
            ],
        });

        var mass_adjustment_detail_table = $('#MassAdjustmentDetailtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Blrt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('mass_adjustment_detail_datatables') }}",
                data: function(d) {
                    d.search = $('#mad_search').val();
                    d.ma_id = $('#ma_code').attr('data-id');
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'pl_code',
                    name: 'pl_code'
                },
                {
                    data: 'br_name',
                    name: 'br_name'
                },
                {
                    data: 'ps_barcode',
                    name: 'ps_barcode'
                },
                {
                    data: 'p_name',
                    name: 'p_name'
                },
                {
                    data: 'p_color',
                    name: 'p_color'
                },
                {
                    data: 'sz_name',
                    name: 'sz_name'
                },
                {
                    data: 'psc_name',
                    name: 'psc_name'
                },
                {
                    data: 'p_purchase_price',
                    name: 'p_purchase_price',
                    orderable: false
                },
                {
                    data: 'sell',
                    name: 'sell',
                    orderable: false
                },
                {
                    data: 'qty_export',
                    name: 'qty_export'
                },
                {
                    data: 'qty_so',
                    name: 'qty_so'
                },
                {
                    data: 'mad_type',
                    name: 'mad_type'
                },
                {
                    data: 'mad_diff',
                    name: 'mad_diff'
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

        $('#stock_search').on('keyup', function() {
            stock_table.draw();
        });

        $('#ma_search').on('keyup', function() {
            mass_adjustment_table.draw();
        });

        $('#mad_search').on('keyup', function() {
            mass_adjustment_detail_table.draw();
        });

        $(document).delegate('#qty_filter', 'change', function(e) {
            e.preventDefault();
            qty_filter = $(this).val();
            loadAsset(st_id, psc_id, br_id);
            stock_table.draw();
        });

        $(document).delegate('#st_filter', 'change', function(e) {
            e.preventDefault();
            st_id = $(this).val();
            loadLocation(st_id);
            loadAsset(st_id, psc_id, br_id);
            stock_table.draw();
            mass_adjustment_table.draw();
        });

        $(document).delegate('#br_filter', 'change', function(e) {
            e.preventDefault();
            br_id = $(this).val();
            loadAsset(st_id, psc_id, br_id);
            stock_table.draw();
        });

        $(document).delegate('#psc_filter', 'change', function(e) {
            e.preventDefault();
            psc_id = $(this).val();
            loadAsset(st_id, psc_id, br_id);
            stock_table.draw();
        });

        // Initialize Select2 pada elemen select filter_status
        // $('#filter_status').select2({
        //     width: "200px",
        //     dropdownParent: $('#filter_status_parent') // Menentukan parent untuk dropdown
        //     console.log()
        // });

        // Event listener untuk perubahan pada filter_status
        $('#filter_status').on('change', function() {
            console.log($(this).val()); // Log nilai yang dipilih (0 atau 1)
            mass_adjustment_table.draw(); // Memuat ulang tabel sesuai dengan filter status
        });

        $(document).delegate('#export_btn', 'click', function(e) {
            e.preventDefault();
            exportTable();
        });

        // sini
        $(document).delegate('#btn_cancel', 'click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');

            swal({
                title: "Cancel..?",
                text: "Yakin cancel?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Yakin'
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
                        data: {
                            ma_id: $('#ma_code').attr('data-id')
                        },
                        dataType: 'json',
                        url: "{{ url('mass_adjustment_cancel') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                swal("Berhasil", "Berhasil dihapus", "success");
                                loadApproval();
                                mass_adjustment_table.draw(false);
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#export_mad_btn', 'click', function(e) {
            e.preventDefault();
            exportResult();
        });

        $(document).delegate('#bin_filter', 'change', function(e) {
            e.preventDefault();
            var id = $(this).val();
            var label = $('#bin_filter option:selected').text();
            pl_id.push(id);

            console.log(id)
            $('#bin_filter_panel').append(
                "<a class='btn-sm btn-success col-2 mt-1 text-center pl_label" + id +
                "' id='pl_label' data-id='" + id + "'>" + label + "</a>");
            stock_table.draw();
            loadLocation(st_id);
            loadAsset(st_id, psc_id, br_id);
        });

        $(document).delegate('#pl_label', 'click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            pl_id = $.grep(pl_id, function(value) {
                return value != id;
            });
            $('.pl_label' + id).remove();
            stock_table.draw();
            loadLocation(st_id);
            loadAsset(st_id, psc_id, br_id);
        });

        $(document).delegate('#import_btn', 'click', function(e) {
            e.preventDefault();
            jQuery.noConflict();
            $('#ImportModal').modal('show');
        });

        $(document).delegate('#madj_btn', 'click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var code = $(this).text();
            $('#ma_code').attr('data-id', id);
            $('#ma_code').text(code);
            loadApproval();
            mass_adjustment_detail_table.draw(false);
        });

        $(document).delegate('#ma_code', 'click', function(e) {
            e.preventDefault();
            $('#ma_code').attr('data-id', '');
            $('#ma_code').text('');
            loadApproval();
            mass_adjustment_detail_table.draw(false);
        });

        $(document).delegate('#approval_btn', 'click', function(e) {
            e.preventDefault();
            if ($('#approval_label').attr('data-id') != '') {
                swal('Sudah Approve', 'Approval sudah disetujui', 'warning');
                return false;
            }
            swal({
                title: "Approve..?",
                text: "Yakin approve?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Yakin'
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
                        data: {
                            ma_id: $('#ma_code').attr('data-id')
                        },
                        dataType: 'json',
                        url: "{{ url('mass_adjustment_approval') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                swal("Berhasil", "Berhasil approval", "success");
                                loadApproval();
                                mass_adjustment_table.draw(false);
                            } else {
                                swal('Gagal', 'Gagal approval data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#execution_btn', 'click', function(e) {
            e.preventDefault();
            if ($('#approval_label').attr('data-id') == '') {
                swal('Approval', 'Approval masih kosong', 'warning');
                return false;
            }
            swal({
                title: "Eksekusi Penyesuaian..?",
                text: "Yakin eksekusi?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Yakin'
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
                        data: {
                            ma_id: $('#ma_code').attr('data-id')
                        },
                        dataType: 'json',
                        url: "{{ url('mass_adjustment_exec') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                swal("Berhasil", "Berhasil eksekusi", "success");
                                mass_adjustment_table.draw(false);
                            } else {
                                swal('Gagal', 'Gagal eksekusi', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            if (st_id == 'all') {
                swal('Tentukan Store', 'Silahkan tentukan store terlebih dahulu', 'warning');
                return false;
            }
            var formData = new FormData(this);
            formData.append('st_id', st_id);

            // console.log(formData);
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: "{{ url('import_mass_adjustment_template') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(r) {
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    if (r.status == '200') {
                        mass_adjustment_table.draw(false);
                        $('#ma_code').attr('data-id', r.ma_id);
                        $('#ma_code').text(r.ma_code);
                        mass_adjustment_detail_table.draw(false);
                        loadApproval();
                        $('#ImportModal').modal('hide');
                        $('#f_import')[0].reset();
                        swal('Berhasil', 'Adjustment berhasil dicreate', 'success');
                    } else {
                        swal('Gagal', 'Adjustment gagal dicreate', 'warning');
                    }
                },
                error: function(data) {
                    swal('Error', data, 'error');
                }
            });
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

            if ((end - start) < 100 || label == 'Today') {
                title = 'Today:';
                range = start.format('MMM D');
                hidden_range = start.format('YYYY-MM-DD');
            } else if (label == 'Yesterday') {
                title = 'Yesterday:';
                range = start.format('MMM D');
                hidden_range = start.format('YYYY-MM-DD');
            } else {
                range = start.format('MMM D') + ' - ' + end.format('MMM D');
                hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }

            $('#ma_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
        }

        picker.daterangepicker({
            direction: KTUtil.isRTL(),
            startDate: start,
            endDate: end,
            opens: 'left',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            }
        }, cb);

        cb(start, end, '');

        $(document).delegate('#export_by_date', 'click', function(e) {
            e.preventDefault();
            var dt = $('#ma_date').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    ma_date: dt,
                    st_id: st_id,
                    filter: $('#filter_status').val(),
                },
                dataType: 'json',
                url: "{{ url('export_mass_by_date') }}",
                success: function(r) {
                    $('#MassAdjustmentExportModal').modal('show');
                    $('#MassAdjustmentDetailtb tbody').empty(); // Clear existing rows
                    $(r.data).each(function(index, row) {
                        $('#MassAdjustmentDetailtb tbody').append(
                            "<tr><td>" + (index + 1) +
                            "</td><td>" + row.ma_code +
                            "</td><td>" + row.st_name +
                            "</td><td>" + row.pl_code +
                            "</td><td>" + row.br_name +
                            "</td><td>" + row.ps_barcode +
                            "</td><td>" + row.p_name +
                            "</td><td>" + row.p_color +
                            "</td><td>" + row.sz_name +
                            "</td><td>" + row.psc_name +
                            "</td><td>" + (addCommas(Math.round(row
                                .purchase_1)) || addCommas(Math.round(row
                                    .purchase_2)) || addCommas(Math.round(row
                                    .ps_purchase_price)) || addCommas(Math
                                    .round(row.p_purchase_price)) || '-') +
                            "</td><td>" + (addCommas(row.ps_sell_price) ||
                                addCommas(row.p_sell_price) || '-') +
                            "</td><td>" + row.qty_export +
                            "</td><td>" + row.qty_so +
                            "</td><td>" + row.mad_type +
                            "</td><td>" + row.mad_diff +
                            "</td><td>" + row.adjust_note +
                            "</td><td>" + row.adjust_type +
                            "</td></tr>"
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });

        $(document).delegate('#excel_report', 'click', function(e) {
            e.preventDefault();
            var dt = $('#ma_date').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    ma_date: dt,
                    st_id: st_id,
                    filter: $('#filter_status').val(),
                },
                url: "{{ url('export_mass_by_date_excel') }}",
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(blob, status, xhr) {
                    var filename = "exported_data.xlsx";
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        var matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) filename = matches[1].replace(
                            /['"]/g, '');
                    }

                    if (typeof window.navigator.msSaveBlob !== 'undefined') {
                        window.navigator.msSaveBlob(blob, filename);
                    } else {
                        var URL = window.URL || window.webkitURL;
                        var downloadUrl = URL.createObjectURL(blob);

                        if (filename) {
                            var a = document.createElement("a");
                            if (typeof a.download === 'undefined') {
                                window.location.href = downloadUrl;
                            } else {
                                a.href = downloadUrl;
                                a.download = filename;
                                document.body.appendChild(a);
                                a.click();
                            }
                        } else {
                            window.location.href = downloadUrl;
                        }
                        setTimeout(function() {
                            URL.revokeObjectURL(downloadUrl);
                        }, 10000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error exporting data:', error);
                }
            });
        });
    });
</script>
