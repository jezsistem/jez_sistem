<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
<script>
    // $('body').addClass('kt-primary--minimize aside-minimize');
    function addCommas(nStr)
    {
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
    
    function cabangPdf() {
        var pdf = new jsPDF('p', 'pt', 'letter');
        source = $('#cabang_content')[0];
        specialElementHandlers = {
            '#bypassme': function (element, renderer) {
                return true
            }
        };
        margins = {
            top: 80,
            bottom: 60,
            left: 40,
            width: 522
        };
        pdf.fromHTML(
            source, 
            margins.left, 
            margins.top, { 
                'width': margins.width,
                'elementHandlers': specialElementHandlers
            },
            function (dispose) {
                pdf.save('Cabang Offline Omset.pdf');
            }, margins
        );
    }
    
    function cabangSummary(st_id, stt_id, date)
    {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_st_id:st_id, _stt_id:stt_id, _date:date},
            dataType: 'json',
            url: "{{ url('cabang_summary')}}",
            success: function(r) {
                if (r.status == '200'){
                    $('#cabang_omset').text(addCommas(r.omset_global));
                    $('#cabang_target').text(addCommas(r.target));
                    $('#cabang_total_debit').text(addCommas(r.total_debit));
                    $('#cabang_total_debit_bca').text(addCommas(r.total_debit_bca));
                    $('#cabang_total_debit_bri').text(addCommas(r.total_debit_bri));
                    $('#cabang_total_debit_bni').text(addCommas(r.total_debit_bni));
                    $('#cabang_total_debit_mandiri').text(addCommas(r.total_debit_mandiri));
                    $('#cabang_total_qr').text(addCommas(r.total_qr));
                    $('#cabang_total_transfer').text(addCommas(r.total_transfer));
                    $('#cabang_total_cash').text(addCommas(r.total_cash));
                    $('#cabang_cross_order').text(addCommas(r.cross_order));
                    var status = '';
                    toast('Berhasil', 'Informasi berhasil ditampilkan' ,'success');
                } else {
                    toast('Gagal', 'Informasi gagal ditampilan' ,'warning');
                }
            }
        });
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var hbhj_table = $('#HBHJtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'B<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('check_hb_hj') }}",
                data : function (d) {
                    d.search = $('#hbhj_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'p_color', name: 'p_color' },
            { data: 'sz_name', name: 'sz_name' },
            { data: 'psc_name', name: 'psc_name' },
            { data: 'hb', name: 'hb', orderable: false },
            { data: 'hj', name: 'hj', orderable: false },
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

        $(document).delegate('#hbhj_search', 'keyup', function(e) {
            e.preventDefault();
            hbhj_table.draw();
        });

        $(document).delegate('#check_hb_hj', 'click', function(e) {
            e.preventDefault();
            jQuery.noConflict();
            $('#HBHJModal').modal('show');
        });
        
        var invoice_report_table = $('#InvoiceReporttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('invoice_report_datatables') }}",
                data : function (d) {
                    d.search = $('#invoice_report_search').val();
                    d.stt_id = $('#stt_id').val();
                    d.st_id = $('#st_id_filter').val();
                    d.sales_date = $('#sales_date').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pt_id', searchable: false},
            { data: 'pos_created', name: 'pos_created' },
            { data: 'pos_invoice', name: 'pos_invoice' },
            { data: 'cust_name', name: 'cust_name' },
            { data: 'cross', name: 'cross_order' },
            { data: 'u_name', name: 'u_name' },
            { data: 'dv_name', name: 'dv_name' },
            { data: 'item_qty', name: 'item_qty', orderable: false },
            { data: 'item_value', name: 'item_value', orderable: false },
            { data: 'pos_shipping', name: 'pos_shipping' },
            { data: 'pos_unique_code', name: 'pos_unique_code' },
            { data: 'pos_admin_cost', name: 'pos_admin_cost' },
            { data: 'pos_discount_seller', name: 'pos_discount_seller' },
            { data: 'pos_another_cost', name: 'pos_another_cost' },
            { data: 'nameset', name: 'nameset', orderable: false },
            { data: 'value_admin', name: 'value_admin', orderable: false },
            { data: 'total', name: 'total', orderable: false },
            { data: 'payment_one', name: 'pm_id' },
            { data: 'pos_payment', name: 'pos_payment' },
            { data: 'pos_card_number', name: 'pos_card_number' },
            { data: 'pos_ref_number', name: 'pos_ref_number' },
            { data: 'payment_two', name: 'pm_id_partial' },
            { data: 'pos_payment_partial', name: 'pos_payment_partial' },
            { data: 'pos_card_number_two', name: 'pos_card_number_two' },
            { data: 'pos_ref_number_two', name: 'pos_ref_number_two' },
            { data: 'pos_note', name: 'pos_note' },
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

        var article_report_table = $('#ArticleReporttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('article_report_datatables') }}",
                data : function (d) {
                    d.search = $('#article_report_search').val();
                    d.stt_id = $('#stt_id').val();
                    d.st_id = $('#st_id_filter').val();
                    d.sales_date = $('#sales_date').val();
                    d.pt_id = $('#pt_id_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'ptd_id', searchable: false},
            { data: 'ptd_created', name: 'ptd_created' },
            { data: 'pos_invoice', name: 'pos_invoice' },
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'pc_name', name: 'pc_name' },
            { data: 'psc_name', name: 'psc_name' },
            { data: 'pssc_name', name: 'pssc_name' },
            { data: 'p_color', name: 'p_color' },
            { data: 'sz_name', name: 'sz_name' },
            { data: 'pos_td_qty', name: 'pos_td_qty' },
            { data: 'price_tag', name: 'price_tag' },
            { data: 'sell_price', name: 'sell_price' },
            { data: 'total_price', name: 'total_price', orderable: false },
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
            order: [[2, 'desc']],
        });
        
        var cross_report_tb = $('#CrossReporttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('article_cross_report_datatables') }}",
                data : function (d) {
                    d.search = $('#article_report_search').val();
                    d.stt_id = $('#stt_id').val();
                    d.st_id = $('#st_id_filter').val();
                    d.sales_date = $('#sales_date').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'ptd_id', searchable: false},
            { data: 'ptd_created', name: 'ptd_created' },
            { data: 'pos_invoice', name: 'pos_invoice' },
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'pc_name', name: 'pc_name' },
            { data: 'psc_name', name: 'psc_name' },
            { data: 'pssc_name', name: 'pssc_name' },
            { data: 'p_color', name: 'p_color' },
            { data: 'sz_name', name: 'sz_name' },
            { data: 'pos_td_qty', name: 'pos_td_qty' },
            { data: 'price_tag', name: 'price_tag' },
            { data: 'sell_price', name: 'sell_price' },
            { data: 'total_price', name: 'total_price', orderable: false },
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
            order: [[2, 'desc']],
        });

        $('#stt_id, #datetime, #st_id_filter').on('change', function() {
            //sales_report_table.draw();
            invoice_report_table.draw();
            article_report_table.draw();
        });

        $('#article_report_search').on('keyup', function() {
            article_report_table.draw();
        });

        $('#invoice_report_search').on('keyup', function() {
            invoice_report_table.draw();
        });

        $(document).delegate('#sales_summary_btn', 'click', function() {
            var st_label = $('#st_id_filter option:selected').text();
            var stt_label = $('#stt_id option:selected').text();
            var omset_date = $('#kt_dashboard_daterangepicker_date').text();

            var st_id = $('#st_id_filter option:selected').val();
            var stt_id = $('#stt_id option:selected').val();
            var date = $('#sales_date').val();

            if (st_label == '- Storage -') {
                swal('Storage', 'Pilih storage terlebih dahulu', 'warning');
                return false;
            }

            // if (stt_label == '- Divisi -') {
            //     swal('Divisi', 'Pilih divisi terlebih dahulu', 'warning');
            //     return false;
            // }

            $('#CabangSummaryModal').modal('show');
            $('#cabang_omset_date').text(omset_date);
            cabangSummary(st_id, stt_id, date);
        });

        $(document).delegate('#invoice_detail_btn', 'click', function() {
            var pt_id = $(this).attr('data-pt_id');
            var pt_id_label = $(this).text();
            $('#pt_id_filter').val(pt_id);
            $('#pt_id_filter_label').addClass('btn btn-success');
            $('#pt_id_filter_label').text(pt_id_label);
            article_report_table.draw();
        });

        $(document).delegate('#pt_id_filter_label', 'click', function() {
            $('#pt_id_filter').val('');
            $('#pt_id_filter_label').removeClass('btn btn-primary');
            $('#pt_id_filter_label').text('');
            article_report_table.draw();
        });
        
        $(document).delegate('#export_btn', 'click', function(e) {
            e.preventDefault();
            var type = $(this).attr('data-type');
            var stt_id = $('#stt_id').val();
            var st_id = $('#st_id_filter').val();
            var date = $('#sales_date').val();
            window.location.href = "{{ url('sales_export') }}?type="+type+"&date="+date+"&stt_id="+stt_id+"&st_id="+st_id+"";
        });
        
        $(document).delegate('#cabang_cross_order_detail', 'click', function() {
            jQuery.noConflict();
            $('#CrossModal').modal('show');
            cross_report_tb.draw(false);
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
            invoice_report_table.draw();
            article_report_table.draw();
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