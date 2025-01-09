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
                url: "{{ url('poc_datatables') }}",
                data: function(d) {
                    d.search = $('#po_approval_search').val();
                    d.filter_status = $('#filter_status').val();
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
                    name: 'po_invoice',
                    searchable: true
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
                    name: 'created_at'
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
            order: [
                [0, 'desc']
            ],
        });

        $('#filter_status').on('change', function() {
            po_approval_table.draw();
        });

        $('#SuratJalanImageBtn').on('click', function() {
            // console.log('kontol');
        });

        $('#f_upload_invoice_image').on('submit', function(e) {
            e.preventDefault();
            $('#upload_image_invoice_btn').html('Proses...');
            $('#upload_image_invoice_btn').attr('disabled', true);
            var formData = new FormData(this);
            var po_id = $('#_po_id').val();

            formData.append('_po_id', po_id);
            // console.log('COD');
            $.ajax({
                type: 'POST',
                url: "{{ url('po_invoice_image_cod') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#upload_image_invoice_btn").html('Upload');
                    $("#upload_image_invoice_btn").attr("disabled", false);
                    jQuery.noConflict();
                    $("#UploadImageInvoiceModal").modal('hide');

                    if (data.status == '200') {
                        toastr.success('Data berhasil diimport', 'Berhasil');
                        $('#f_upload_invoice_image')[0].reset();
                        purchaseOrderInvoiceTable.draw();
                    } else if (data.status == '400') {
                        toastr.warning(
                            'File yang anda import kosong atau format tidak tepat',
                            'File');
                    } else {
                        toastr.warning(
                            'Silahkan periksa format input pada template anda, pastikan kolom biru terisi sesuai dengan sistem',
                            'Gagal');
                    }
                },
                error: function(data) {
                    toastr.error('Terjadi kesalahan saat mengupload data', 'Error');
                }
            });
        });


        var purchaseOrderInvoiceTable = $('#InvoiceImagesTb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            ajax: {
                url: "{{ url('po_invoice_image_datatable_cod') }}",
                data: function(d) {
                    d._po_id = $('#_po_id').val();
                },
            },

            columns: [{
                data: 'image',
                name: 'invoice_image',
                searchable: false
            },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            columnDefs: [{
                "targets": [0, 1],
                "className": "text-center",
                "width": "0%"
            }],
            order: [
                [0, 'desc']
            ],
        });


        var apd_table = $('#CODtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: 'Brtl<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('pocdetail_datatables') }}",
                data: function(d) {
                    d.poads_invoice = $('#invoice_label').text();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
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
                    data: 'ps_qty',
                    name: 'ps_qty'
                }
                ,
                {
                    data: 'poads_purchase_price',
                    name: 'poads_purchase_price',
                    render: function(data) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: 'poads_total_price',
                    name: 'poads_total_price',
                    render: function(data) {
                        return formatRupiah(data);
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
        
        function formatRupiah(data) {
            // Check if data is undefined or null, return 'Rp. 0' if true
            if (data === null || data === undefined || isNaN(data)) {
                return 'Rp. 0';
            }
            
            // Convert the data to integer and format
            var numberString = parseInt(data, 10).toString();
            var formatted = numberString.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            return "Rp. " + formatted;
        }

        po_approval_table.buttons().container().appendTo($('#po_approval_excel_btn'));
        $('#po_approval_search').on('keyup', function() {
            po_approval_table.draw(false);
        });

        $('#APtb tbody').on('click', 'tr', function () {
            
            var id = po_approval_table.row(this).data().id;
            var po_id = po_approval_table.row(this).data().po_id;
            var st_name = po_approval_table.row(this).data().st_name;
            var ps_name = po_approval_table.row(this).data().ps_name;
            // var full_date = po_approval_table.row(this).data().created_at;
            // var tgl_terima = full_date.split(' ')[0];
            var stkt_id = po_approval_table.row(this).data().stkt_id;  // Access stkt_id
            var tax_id = po_approval_table.row(this).data().tax_id;    // Access tax_id
            var stkt_name = po_approval_table.row(this).data().stkt_name; // Access stkt_name
            var tx_name = po_approval_table.row(this).data().tx_name;    // Access tx_name
            var today = new Date();
            var tgl_terima = today.toISOString().split('T')[0]; // Format YYYY-MM-DD
            var po_description = po_approval_table.row(this).data().po_description;
            var shipping_cost = po_approval_table.row(this).data().po_shipping_cost;
            var poads_invoice = po_approval_table.row(this).data().poads_invoice;
            var u_id_approve = po_approval_table.row(this).data().u_id_approve;
            var po_invoice = po_approval_table.row(this).data().po_invoice;
            approval = po_approval_table.row(this).data().u_receive;
            jQuery.noConflict();

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
                success: function (r) {
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
                    $('#stkt_name').val(stkt_name);  // Set stkt_name value
                    $('#tax_name').val(tx_name);

                    purchaseOrderInvoiceTable.draw();
                }
            });
            $('#ApproveModal').modal('show');
            $('#invoice_label').text(poads_invoice.replace("&amp;", "&"));

            apd_table.draw();
        });


        $(document).ready(function() {
            $("#InvoiceImagesBtn").click(function() {
                $("#InvoiceImagesModal").modal("show");
                console.log($('#po_id').val());
            });
        });

        $(document).ready(function() {
            $("#pembayaranCodBtn").click(function() {
                $("#UploadImageInvoiceModal").modal("show");
            });
        });



        $(document).delegate('#delete_poads', 'click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
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

        $('#approve_btn').on('click', function() {
            if (approval != 'Diterima, Belum Dibayar') {
                swal('Sudah Approve', 'Invoice ini sudah dibayar', 'warning');
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
                        url: "{{ url('poc_update_is_paid') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                $('#ApproveModal').modal('hide');
                                po_approval_table.draw(false);
                                swal("Berhasil", "Data berhasil dibayar",
                                    "success");
                            } else {
                                swal('Gagal', 'Gagal approve data', 'error');
                            }
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
            } 
            else if(label == 'All Days') {
                title = 'All Days';
                hidden_range = '';
            }
            else {
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
