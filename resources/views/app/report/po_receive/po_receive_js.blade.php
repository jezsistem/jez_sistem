<script>
    var report_date = '';

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var po_receive_table = $('#PoReceivetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('po_receive_datatables') }}",
                data : function (d) {
                    d.search = $('#po_receive_search').val();
                    d.report_date = report_date;
                    d.status = $('#status_filter').val();
                    d.st_id = $('#st_id_filter').val();
                    d.date_filter = $('#date_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'po_id', searchable: false},
            { data: 'po_invoice', name: 'po_invoice' },
            { data: 'st_name', name: 'st_name' },
            { data: 'po_created_show', name: 'po_created' },
            { data: 'poads_created_show', name: 'poads_created' },
            { data: 'poad_qty_show', name: 'poad_qty' },
            { data: 'poads_qty_show', name: 'poads_qty' },
            { data: 'poad_total_price_show', name: 'poad_total_price' },
            { data: 'poads_total_price_show', name: 'poads_total_price' },
            ], 
            columnDefs: [
            {
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

        var po_table = $('#Potb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('po_receive_detail_datatables') }}",
                data : function (d) {
                    d.search = $('#po_search').val();
                    d.po_id = $('#_po_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'poad_id', searchable: false},
            { data: 'article', name: 'article' },
            { data: 'poad_qty_show', name: 'poad_qty' },
            { data: 'poads_qty_show', name: 'poads_qty' },
            { data: 'poad_total_show', name: 'poad_total' },
            { data: 'poads_total_show', name: 'poads_total' },
            ], 
            columnDefs: [
            {
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

        po_receive_table.buttons().container().appendTo($('#po_receive_excel_btn' ));
        $('#po_receive_search').on('keyup', function() {
            po_receive_table.draw();
        });

        $('#po_search').on('keyup', function() {
            po_table.draw();
        });
        
        $('#st_id_filter, #status_filter, #date_filter').on('change', function() {
            po_receive_table.draw();
        });

        $(document).delegate('#poads_qty_btn', 'click', function() {
            var po_id = $(this).attr('data-po_id');
            var po_invoice = $(this).attr('data-po_invoice');
            $('#_po_id').val(po_id);
            $('#po_invoice_label').text(po_invoice);
            $('#PoModal').modal('show');
            po_table.draw();
        });

        $('#add_po_receive_btn').on('click', function() {
            jQuery.noConflict();
            $('#ProductCategoryModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_po_receive')[0].reset();
            $('#delete_po_receive_btn').hide();
        });

        $(document).delegate('#export_btn', 'click', function(e) {
            e.preventDefault();
            $('#export_btn').text('Mohon Tunggu..');
            $('#export_btn').addClass('disabled');
            var date = report_date;
            var date_filter = $('#date_filter').val();
            var status_filter = $('#status_filter').val();
            var st_id = $('#st_id_filter').val();
            window.location.href = "{{ url('po_receive_export') }}?&st_id="+st_id+"&date_filter="+date_filter+"&status_filter="+status_filter+"&date="+date+"";
            
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
            report_date = hidden_range;
            $('#dashboard_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            po_receive_table.draw();

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
                'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
        cb(start, end, '');

    });
</script>