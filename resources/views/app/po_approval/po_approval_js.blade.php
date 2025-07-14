<script src="{{asset('app') }}/assets/js/modal_lock.js"></script>

<script>
    var approval = '';

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var po_approval_table = $('#APtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('ap_datatables') }}",
                data: function(d) {
                    d.search = $('#po_approval_search').val();
                    d.filter_status = $('#filter_status').val();
                    d.filter_cabang = $('#filter_cabang').val();
                    d.filter_dispute = $('#filter_dispute').val();
                    d.date = $('#po_date').val();

                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'st_name',
                    name: 'st_name'
                },
                {
                    data: 'po_invoice',
                    name: 'po_invoice'
                },
                {
                    data: 'ps_name',
                    name: 'ps_name'
                },
                {
                    data: 'poads_invoice_show',
                    name: 'poads_invoice'
                },
                {
                    data: 'invoice_date_show',
                    name: 'invoice_date'
                },
                {
                    data: 'receive_date_show',
                    name: 'received_date'
                },
                {
                    data: 'u_name',
                    name: 'u_name'
                },
                {
                    data: 'u_receive',
                    name: 'u_receive',
                    orderable: false
                },
                // {
                //     data: 'qty',
                //     name: 'qty'
                // },
            ],
            rowCallback: function(row, data, index) {
                console.log('Dispute:', data.dispute);
                if (data.dispute == 1) {
                    $(row).css('background-color', '#f8d7da'); // Bootstrap's light red alert bg
                }
            },
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            order: [
                [0, 'desc']
            ],
        });

        $('#filter_status').on('change', function() {
            po_approval_table.draw();
        });

        $('#filter_cabang').on('change', function() {
            console.log($('#filter_cabang').val())
            po_approval_table.draw();
        });

        $('#filter_dispute').on('change', function() {
            console.log($('#filter_dispute').val())
            po_approval_table.draw();
        });

        // var po_id = po_approval_table.row(this).data().po_id;


        var purchaseOrderInvoiceTable = $('#InvoiceImagesTb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            ajax: {
                url: "{{ url('po_invoice_image_datatable') }}",
                data: function(d) {
                    d._po_id = $('#_po_id').val();
                },
            },

            columns: [{
                data: 'image',
                name: 'invoice_image',
                searchable: false
            }, ],
            columnDefs: [{
                "targets": [0],
                "className": "text-center",
                "width": "0%"
            }],
            order: [
                [0, 'desc']
            ],
        });

        var purchaseOrderBuktitfTable = $('#BuktitfImagesTb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            ajax: {
                url: "{{ url('po_transfer_image_datatable') }}",
                data: function(d) {
                    d._po_id = $('#_po_id').val();
                },
            },

            columns: [{
                data: 'image',
                name: 'transfer_image',
                searchable: false
            }, ],
            columnDefs: [{
                "targets": [0],
                "className": "text-center",
                "width": "0%"
            }],
            order: [
                [0, 'desc']
            ],
        });

        var PurchaseOrdersFileDispute = $('#FileDisputeTb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            ajax: {
                url: "{{ url('po_dispute_file_datatable') }}",
                data: function(d) {
                    d._po_id = $('#_po_id').val();
                },
            },

            columns: [{
                data: 'file',
                name: 'file_dispute',
                searchable: false
            }, ],
            columnDefs: [{
                "targets": [0],
                "className": "text-center",
                "width": "0%"
            }],
            order: [
                [0, 'desc']
            ],
        });

        var apd_table = $('#APDtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: 'Brtl<"text-right"ip>',
            buttons: [],
            ajax: {
                url: "{{ url('apd_datatables') }}",
                data: function(d) {
                    d.poads_invoice = $('#invoice_label').text();
                    // jQuery('#st_id').val(r.st_id).trigger('change');
                    // jQuery('#ps_id').val(r.ps_id).trigger('change');
                    // jQuery('#stkt_id').val(r.stkt_id).trigger('change');
                    // jQuery('#tax_id').val(r.tax_id).trigger('change');
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'po_id',
                    searchable: false
                },
                {
                    data: 'created_at_show',
                    name: 'created_at'
                },
                {
                    data: 'poads_invoice',
                    name: 'poads_invoice'
                },
                {
                    data: 'ps_barcode',
                    name: 'ps_barcode'
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
                    data: 'stkt_name',
                    name: 'stkt_name'
                },
                {
                    data: 'poads_qty',
                    name: 'poads_qty'
                },
                {
                    data: 'pls_qty_current',
                    name: 'pls_qty_current'
                },
                {
                    data: 'poad_purchase_price',
                    name: 'poad_purchase_price',
                    render: function(data, type, row) {
                        // Ensure the value is treated as a number
                        var price = parseFloat(data);
                        // Format the price as Rupiah
                        var formattedPrice = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(price);
                        // Return the formatted price
                        return formattedPrice;
                    }
                },
                {
                    data: 'poad_total_price',
                    name: 'poad_total_price',
                    render: function(data, type, row) {
                        // Ensure the value is treated as a number
                        var price = parseFloat(data);
                        // Format the price as Rupiah
                        var formattedPrice = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(price);
                        // Return the formatted price
                        return formattedPrice;
                    }
                },
                {
                    data: 'delete',
                    name: 'poads_total_price'
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

        po_approval_table.buttons().container().appendTo($('#po_approval_excel_btn'));
        $('#po_approval_search').on('keyup', function() {
            po_approval_table.draw(false);
        });

        $('#APtb tbody').on('click', 'tr', async function() {

            var id = po_approval_table.row(this).data().id;
            var po_id = po_approval_table.row(this).data().po_id;
            var st_name = po_approval_table.row(this).data().st_name;
            var ps_name = po_approval_table.row(this).data().ps_name;
            // var full_date = po_approval_table.row(this).data().created_at;
            // var tgl_terima = full_date.split(' ')[0];
            var stkt_name = po_approval_table.row(this).data().stkt_name;
            var tax_id = po_approval_table.row(this).data().tax_id;
            var a_name = po_approval_table.row(this).data().a_name;
            var tgl_terima = po_approval_table.row(this).data().received_date || po_approval_table.row(
                this).data().created_at.split(' ')[0];
            var po_description = po_approval_table.row(this).data().po_description;
            var shipping_cost = po_approval_table.row(this).data().po_shipping_cost;
            var poads_invoice = po_approval_table.row(this).data().poads_invoice;
            var u_id_approve = po_approval_table.row(this).data().u_id_approve;
            var po_invoice = po_approval_table.row(this).data().po_invoice;
            var dispute = po_approval_table.row(this).data().dispute;
            var putaway = po_approval_table.row(this).data().putaway;
            var dispute_description = po_approval_table.row(this).data().dispute_description;
            var pay_date = po_approval_table.row(this).data().pay_date;
            var due_date = po_approval_table.row(this).data().due_date;
            approval = po_approval_table.row(this).data().u_receive;
            jQuery.noConflict();

            let dispute_text = '';
            let putaway_text = '';

            if (dispute === 1) {
                dispute_text = 'Yes';
            } else if (dispute === 0) {
                dispute_text = 'No';
            } else {
                dispute_text = 'Empty';
            }

            if (putaway === 1) {
                putaway_text = 'Yes';
            } else if (putaway === 0) {
                putaway_text = 'No';
            } else {
                putaway_text = 'Empty';
            }
 
            // Coba dapatkan lock sebelum buka modal
            const lockResult = await openEditModal('purchase_order', po_id,'approval_penerimaan');
            if (lockResult === false) {
                return;
            }

            // Mulai interval untuk extend lock setiap 60 detik
            if (window.lockExtendInterval) clearInterval(window.lockExtendInterval);
            window.lockExtendInterval = setInterval(function() {
                extendLock('purchase_order', po_id,'approval_penerimaan');
            }, 60000);

            console.log('STORES : ', tgl_terima);
            console.log('POADS ID :', poads_invoice);

            function formatRupiah(number) {
                // Ensure the number is an integer
                var numberString = Math.round(number).toString();

                // Regular expression to add dots as thousand separators
                var formatted = numberString.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                $('#no_po').text(po_invoice);

                return "Rp. " + formatted;
            }


            // call ajax apd_total_price 
            $.ajax({
                type: "GET",
                data: {
                    invoice: poads_invoice
                },
                dataType: 'json',
                url: "{{ url('apd_total_price') }}",
                success: function(r) {
                    console.log(r);
                    console.log(st_name);
                    // Convert 'r' to a number if it's not
                    var priceNumber = typeof r === 'number' ? r : parseFloat(r);
                    if (isNaN(priceNumber)) {
                        console.error("Invalid number for total approval price:", r);
                        priceNumber = 0; // Default to 0 or handle as needed
                    }

                    // Format the number as Rupiah using the custom function
                    var formattedPrice = formatRupiah(priceNumber);

                    // Update the DOM element with the formatted price
                    $('#total_approval_price').text(formattedPrice);

                    $('#st_id').val(st_name);
                    $('#ps_name').val(ps_name);
                    $('#po_description').val(po_description);
                    $('#receive_date').val(tgl_terima).prop('readonly', true);
                    $('#shipping_cost').val(shipping_cost);
                    $('#_po_id').val(po_id);
                    $('#total_approval_price').text(formatRupiah(r));
                    $('#stkt_id').val(stkt_name);
                    $('#tax_id').val(tax_id);
                    $('#a_name').val(a_name);
                    $('#dispute').val(dispute_text);
                    $('#dispute_description').val(dispute_description);
                    $('#pay_date').val(pay_date);
                    $('#due_date').val(due_date);
                    $('#putaway').val(putaway_text);

                    purchaseOrderInvoiceTable.draw();
                    purchaseOrderBuktitfTable.draw();
                    PurchaseOrdersFileDispute.draw();
                }
            });
            $('#ApproveModal').modal('show');
            $('#invoice_label').text(poads_invoice.replace("&amp;", "&"));

            apd_table.draw();


        });

        $(document).delegate('#ExportApprovalBtn', 'click', function() {
            var no_po = $('#no_po').text();
            var no_invoice = $('#invoice_label').text();
            var ps_name = $('#ps_name').val();
            window.location.href = "{{ url('apd_export') }}?no_po=" + no_po + "&ps_name=" + ps_name +
                "&no_invoice=" + no_invoice + "";
        });

        $(document).ready(function() {
            $("#InvoiceImagesBtn").click(function() {
                $("#InvoiceImagesModal").modal("show");
                console.log($('#po_id').val());
            });
        });

        $(document).ready(function() {
            $("#BuktitfImagesBtn").click(function() {
                $("#BuktitfImagesModal").modal("show");
                console.log($('#po_id').val());
            });
        });

        $(document).ready(function() {
            $("#DisputeFileBtn").click(function() {
                $("#FileDisputeModal").modal("show");
                console.log($('#po_id').val());
            });
        });



        $(document).delegate('#delete_poads', 'click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            // console.log(id);
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini ?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Hapus'
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
                            _id: id
                        },
                        dataType: 'json',
                        url: "{{ url('dl_poads_revision') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                apd_table.draw(false);
                                swal("Berhasil", "Data berhasil dihapus",
                                    "success");
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        {{-- $('#approve_btn').on('click', function() { --}}
        {{--    console.log(approval); --}}
        {{--    if (approval != '<span class="badge badge-warning">Menunggu Approval</span>') { --}}
        {{--        swal('Sudah Approve', 'Invoice ini sudah diapprove', 'warning'); --}}
        {{--        return false; --}}
        {{--    } --}}
        {{--    swal({ --}}
        {{--        title: "Approve..?", --}}
        {{--        text: "Yakin approve ?", --}}
        {{--        icon: "warning", --}}
        {{--        buttons: [ --}}
        {{--            'Batalkan', --}}
        {{--            'Approve' --}}
        {{--        ], --}}
        {{--        dangerMode: false, --}}
        {{--    }).then(function(isConfirm) { --}}
        {{--        if (isConfirm) { --}}
        {{--            $.ajaxSetup({ --}}
        {{--                headers: { --}}
        {{--                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') --}}
        {{--                } --}}
        {{--            }); --}}
        {{--            $.ajax({ --}}
        {{--                type: "POST", --}}
        {{--                data: { --}}
        {{--                    invoice: $('#invoice_label').text() --}}
        {{--                }, --}}
        {{--                dataType: 'json', --}}
        {{--                url: "{{ url('apd_approve') }}", --}}
        {{--                success: function(r) { --}}
        {{--                    if (r.status == '200') { --}}
        {{--                        $('#ApproveModal').modal('hide'); --}}
        {{--                        po_approval_table.draw(false); --}}
        {{--                        swal("Berhasil", "Data berhasil diapprove", --}}
        {{--                            "success"); --}}
        {{--                    } else { --}}
        {{--                        swal('Gagal', 'Gagal approve data', 'error'); --}}
        {{--                    } --}}
        {{--                } --}}
        {{--            }); --}}
        {{--            return false; --}}
        {{--        } --}}
        {{--    }) --}}
        {{-- }); --}}

        $('#approve_btn').on('click', function() {
            console.log(approval);
            if (approval != '<span class="badge badge-warning">Menunggu Approval</span>') {
                swal('Sudah Approve', 'Invoice ini sudah diapprove', 'warning');
                return false;
            }
            swal({
                title: "Approve..?",
                text: "Yakin approve ?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Approve'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    // Show loader
                    $('#loader').show();

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {
                            invoice: $('#invoice_label').text()
                        },
                        dataType: 'json',
                        url: "{{ url('apd_approve') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                $('#ApproveModal').modal('hide');
                                po_approval_table.draw(false);
                                swal("Berhasil", "Data berhasil diapprove",
                                    "success");
                            } else {
                                swal('Gagal', r.message, 'error');
                            }
                        },
                        error: function() {
                            swal('Gagal', 'Terjadi kesalahan pada server', 'error');
                        },
                        complete: function() {
                            $('#loader').hide();
                        }
                    });
                    return false;
                }
            })
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
            } else if (label == 'All Days') {
                title = 'All Days';
                hidden_range = '';
            } else {
                range = start.format('MMM D') + ' - ' + end.format('MMM D');
                hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }
            console.log(hidden_range);
            $('#po_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);

            po_approval_table.draw();
        }

        picker.daterangepicker({
            direction: KTUtil.isRTL(),
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
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            }
        }, cb);

        cb(start, end, '');
    });

</script>
