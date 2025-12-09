<script src="{{ asset('app') }}/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
            dataType: 'json',
            success: function(response) {

                if (response.data && Object.keys(response.data).length > 0) {
                    $('#bin_filter').empty(); // Clear existing options
                    $('#bin_filter').append('<option value="">- Pilih Lokasi -</option>');
                    Object.entries(response.data).forEach(function([key, value]) {
                        $('#bin_filter').append('<option value="' + key + '">' + value +
                            '</option>');
                    });

                } else {
                    $('#bin_filter').empty();
                    $('#bin_filter').append('<option value="">- Tidak Ada Lokasi Tersedia -</option>');

                }
            },
            error: function() {
                $('#bin_filter').empty();
                $('#bin_filter').append('<option value="">- Gagal Memuat Lokasi -</option>');
            }
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
                type: 'GET', // Added method POST
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for POST
                },
                data: function(d) {
                    d.search = $('#ma_search').val();
                    d.filter = $('#filter_status').val();
                    d.filter = $('#filter_note').val();
                    d.st_id = st_id;
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
                    data: 'ma_proof_file',
                    name: 'ma_proof_file',
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
                    data: 'purchase',
                    name: 'purchase',
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


        // Initialize Select2 pada elemen select qty_filter
        $('#qty_filter').select2({
            dropdownParent: $('#qty_filter_parent')
        });
        $('#qty_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#qty_filter').on('change', function(e) {
            e.preventDefault();
            qty_filter = $(this).val();
            loadAsset(st_id, psc_id, br_id);
            stock_table.draw();
        });

        // Initialize Select2 pada elemen select st_filter
        $('#st_filter').select2({
            dropdownParent: $('#st_filter_parent')
        });
        $('#st_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_filter').on('change', function() {
            mass_adjustment_table.draw();
            var id = $(this).val();
            console.log(id)
        });

        $(document).delegate('#st_filter', 'change', function(e) {
            e.preventDefault();
            st_id = $(this).val();
            loadLocation(st_id);
            loadAsset(st_id, psc_id, br_id);
            stock_table.draw();
            mass_adjustment_table.draw();
        });

        // Initialize Select2 pada elemen select st_export_filter
        $('#st_export_filter').select2({
            dropdownParent: $('#st_export_filter_parent')
        });
        $('#st_export_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_export_filter').on('change', function() {
            mass_adjustment_table.draw();
            var id = $(this).val();
            console.log(id)
        });

        $(document).delegate('#st_export_filter', 'change', function(e) {
            e.preventDefault();
            st_id = $(this).val();
            loadLocation(st_id);
            loadAsset(st_id, psc_id, br_id);
            stock_table.draw();
            mass_adjustment_table.draw();
        });


        // Initialize Select2 pada elemen select psc_filter
        $('#br_filter').select2({
            dropdownParent: $('#br_filter_parent')
        });
        $('#br_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#br_filter').on('change', function(e) {
            e.preventDefault();
            br_id = $(this).val();
            loadAsset(st_id, psc_id, br_id);
            stock_table.draw();
        });

        // Initialize Select2 pada elemen select psc_filter
        $('#psc_filter').select2({
            dropdownParent: $('#psc_filter_parent')
        });
        $('#psc_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#psc_filter').on('change', function(e) {
            e.preventDefault();
            psc_id = $(this).val();
            loadAsset(st_id, psc_id, br_id);
            stock_table.draw();
        });

        // Initialize Select2 pada elemen select psc_filter
        $('#bin_filter').select2({
            dropdownParent: $('#bin_filter_parent')
        });
        $('#bin_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });


        // Event listener untuk perubahan pada filter_status
        $('#filter_status').on('change', function() {
            console.log($(this).val()); // Log nilai yang dipilih (0 atau 1)
            mass_adjustment_table.draw(); // Memuat ulang tabel sesuai dengan filter status
        });
        $('#filter_note').on('change', function() {
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
                            ma_id: id
                        },
                        dataType: 'json',
                        url: "{{ url('mass_adjustment_cancel') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                swal("Berhasil", "Berhasil dibatalkan", "success");
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
                "<a class='btn-sm btn-success col-2 mt-1 ml-2 text-center pl_label" + id +
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

                            } else if (r.status == '500') {
                                if (r.differences && r.differences.length > 0) {
                                    Swal.fire({
                                        title: 'Perhatian ada Quantity yang berbeda!',
                                        html: `
                                    <div style="overflow-x:auto;">
                                        <table class="table" style="width:100%; text-align:left; border-collapse: collapse;">
                                            <thead>
                                                <tr>
                                                    <th style="border: 1px solid #ccc; padding: 8px;">SKU / ID</th>
                                                    <th style="border: 1px solid #ccc; padding: 8px;">Qty Sistem Eksekusi</th>
                                                    <th style="border: 1px solid #ccc; padding: 8px;">Qty Sistem Real</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sku-table-body">
                                                <!-- Data masuk sini -->
                                            </tbody>
                                        </table>
                                        <br/>
                                        <div style="text-align: center;">
                                            <button id="export_excel" class="swal2-confirm swal2-styled" style="background-color:#28a745; margin-right:10px;">Export ke Excel</button>
                                            <button id="close_alert" class="swal2-cancel swal2-styled" style="background-color:#dc3545;">Tutup</button>
                                        </div>
                                    </div>
                                `,
                                        icon: 'info',
                                        showConfirmButton: false,
                                        didOpen: () => {
                                            let tbody = document
                                                .getElementById(
                                                    'sku-table-body');
                                            r.differences.forEach(
                                                function(item) {
                                                    let row =
                                                        document
                                                        .createElement(
                                                            'tr');
                                                    row.innerHTML = `
                                            <td style="border: 1px solid #ccc; padding: 8px;">${item.sku}</td>
                                            <td style="border: 1px solid #ccc; padding: 8px;">${item.qty_export}</td>
                                            <td style="border: 1px solid #ccc; padding: 8px;">${item.pls_qty}</td>
                                        `;
                                                    tbody
                                                        .appendChild(
                                                            row);
                                                });

                                            // Tombol Export Excel
                                            document.getElementById(
                                                    'export_excel')
                                                .addEventListener(
                                                    'click',
                                                    function() {
                                                        let wb = XLSX
                                                            .utils
                                                            .book_new();
                                                        let ws_data = [
                                                            ["SKU / ID",
                                                                "Qty Sistem Eksekusi",
                                                                "Qty Sistem Real"
                                                            ], // Header
                                                            ...r
                                                            .differences
                                                            .map(
                                                                item => [
                                                                    item
                                                                    .sku,
                                                                    item
                                                                    .qty_export,
                                                                    item
                                                                    .pls_qty
                                                                ])
                                                        ];
                                                        let ws = XLSX
                                                            .utils
                                                            .aoa_to_sheet(
                                                                ws_data
                                                                );
                                                        XLSX.utils
                                                            .book_append_sheet(
                                                                wb, ws,
                                                                "Invalid SKUs Sistem Quantity"
                                                                );
                                                        XLSX.writeFile(
                                                            wb,
                                                            "Invalid_SKUs_Sistem_Quantity.xlsx"
                                                            );
                                                    });

                                            // Tombol Tutup
                                            document.getElementById(
                                                    'close_alert')
                                                .addEventListener(
                                                    'click',
                                                    function() {
                                                        Swal.close();
                                                    });
                                        }
                                    });
                                }
                            } else {
                                swal('Gagal', 'Gagal eksekusi', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#note_adjustment').on('change', function () {
            let val = $(this).val();

            if (val === 'KESALAHAN SYSTEM') {
                $('#bukti_kesalahan_group').removeClass('d-none');
                $('#bukti_kesalahan').attr('required', true);
            } else {
                $('#bukti_kesalahan_group').addClass('d-none');
                $('#bukti_kesalahan').removeAttr('required');
                $('#bukti_kesalahan').val('');
            }
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            if (st_id == 'all') {
                swal('Tentukan Store', 'Silahkan tentukan store terlebih dahulu', 'warning');
                return false;
            }
            var formData = new FormData(this);
            formData.append('st_id', st_id);

            if ($('#proof_file')[0].files.length > 0) {
                formData.append('proof_file', $('#proof_file')[0].files[0]);
            }

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
                    } else if (r.status == '500') {
                        if (r.invalid_skus && r.invalid_skus.length > 0) {
                            Swal.fire({
                                title: 'Perhatian!',
                                html: `
                                    <div style="overflow-x:auto;">
                                        <table class="table" style="width:100%; text-align:left; border-collapse: collapse;">
                                            <thead>
                                                <tr>
                                                    <th style="border: 1px solid #ccc; padding: 8px;">SKU / ID</th>
                                                    <th style="border: 1px solid #ccc; padding: 8px;">Qty Export</th>
                                                    <th style="border: 1px solid #ccc; padding: 8px;">Qty Sistem</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sku-table-body">
                                                <!-- Data masuk sini -->
                                            </tbody>
                                        </table>
                                        <br/>
                                        <div style="text-align: center;">
                                            <button id="export_excel" class="swal2-confirm swal2-styled" style="background-color:#28a745; margin-right:10px;">Export ke Excel</button>
                                            <button id="close_alert" class="swal2-cancel swal2-styled" style="background-color:#dc3545;">Tutup</button>
                                        </div>
                                    </div>
                                `,
                                icon: 'info',
                                showConfirmButton: false,
                                didOpen: () => {
                                    let tbody = document.getElementById(
                                        'sku-table-body');
                                    r.invalid_skus.forEach(function(item) {
                                        let row = document
                                            .createElement('tr');
                                        row.innerHTML = `
                                            <td style="border: 1px solid #ccc; padding: 8px;">${item.sku}</td>
                                            <td style="border: 1px solid #ccc; padding: 8px;">${item.qty_export}</td>
                                            <td style="border: 1px solid #ccc; padding: 8px;">${item.qty_system}</td>
                                        `;
                                        tbody.appendChild(row);
                                    });

                                    // Tombol Export Excel
                                    document.getElementById('export_excel')
                                        .addEventListener('click', function() {
                                            let wb = XLSX.utils.book_new();
                                            let ws_data = [
                                                ["SKU / ID",
                                                    "Qty Export",
                                                    "Qty Sistem"
                                                ], // Header
                                                ...r.invalid_skus.map(
                                                    item => [item.sku,
                                                        item.qty_export,
                                                        item.qty_system
                                                    ])
                                            ];
                                            let ws = XLSX.utils
                                                .aoa_to_sheet(ws_data);
                                            XLSX.utils.book_append_sheet(wb,
                                                ws, "Invalid SKUs");
                                            XLSX.writeFile(wb,
                                                "Invalid_SKUs.xlsx");
                                        });

                                    // Tombol Tutup
                                    document.getElementById('close_alert')
                                        .addEventListener('click', function() {
                                            Swal.close();
                                        });
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Perhatian!',
                                text: 'Tidak ada SKU yang tidak valid.',
                                icon: 'info',
                                confirmButtonText: 'Oke'
                            });
                        }
                    } else {
                        swal('Gagal', 'Adjustment gagal dicreate', 'warning');
                    }
                },
                error: function(data) {
                    swal('Error', data, 'error');
                }
            });
        });


        // Fungsi Export to CSV
        function exportToCSV(data) {
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "SKU / ID,Qty Export,Qty Sistem\n"; // header

            data.forEach(function(item) {
                let row = `${item.sku},${item.qty_export},${item.qty_system}`;
                csvContent += row + "\n";
            });

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "invalid_skus.xlsx");
            document.body.appendChild(link);

            link.click();
            document.body.removeChild(link);
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
            $('#loader').show();

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
                    filter: $('#filter_note').val(),
                },
                dataType: 'json',
                url: "{{ url('export_mass_by_date') }}",
                success: function(r) {
                    console.log(r)
                    $('#loader').hide();
                    $('#MassAdjustmentExportModal').modal('show');
                    $('#MassAdjustmentDetailExporttb tbody').empty(); // Clear existing rows
                    $(r.data).each(function(index, row) {
                        $('#MassAdjustmentDetailExporttb tbody').append(
                            "<tr><td>" + (index + 1) +
                            "</td><td>" + formatTanggal(row.adjustment_date) +
                            "</td><td>" + row.ma_code +
                            "</td><td>" + row.st_name +
                            "</td><td>" + row.pl_code +
                            "</td><td>" + row.br_name +
                            "</td><td>" + row.ps_barcode +
                            "</td><td>" + row.p_name +
                            "</td><td>" + row.p_color +
                            "</td><td>" + row.sz_name +
                            "</td><td>" + row.psc_name +
                            "</td><td>" + (addCommas(Math.round(row.purchase)) || '-') +
                            "</td><td>" + (addCommas(Math.round(row.sell)) || '-') +
                            "</td><td>" + row.qty_export +
                            "</td><td>" + row.qty_so +
                            "</td><td>" + row.mad_type +
                            "</td><td>" + row.mad_diff +
                            "</td><td>" + row.adjust_note_formatted +
                            "</td><td>" + (row.adjust_type || '-') +
                            "</td><td>" + formatTanggal(row.ma_approve_time) +
                            "</td><td>" + formatTanggal(row.ma_executor_time) +
                            "</td></tr>"
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });

        function formatTanggal(tgl) {
            if (!tgl) return '-';
            const date = new Date(tgl);
            const bulanIndo = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];
            const day = date.getDate();
            const month = bulanIndo[date.getMonth()];
            const year = date.getFullYear();
            const jam = String(date.getHours()).padStart(2, '0');
            const menit = String(date.getMinutes()).padStart(2, '0');
            const detik = String(date.getSeconds()).padStart(2, '0');
            return `${day} ${month} ${year} ${jam}:${menit}:${detik}`;
        }


        //export bro
        $(document).delegate('#excel_report', 'click', function(e) {
            e.preventDefault();
            $('#loader_download').show();
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
                    filter: $('#filter_note').val(),
                },
                url: "{{ url('export_mass_by_date_excel') }}",
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(blob, status, xhr) {
                    $('#loader_download').hide();
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
