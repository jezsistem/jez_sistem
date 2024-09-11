<script>
    var date = '';
    // $('body').addClass('kt-primary--minimize aside-minimize');

    const primary = '#072544';
    const xchart = "#chart";

    var xchart_options = {
        series: [],
        chart: {
            height: 350,
            type: 'area',
            zoom: {
                enabled: true
            }
        },
        dataLabels: {
            enabled: false
        },
        plotOptions: {
        bar: {
            borderRadius: 4,
            horizontal: true,
        }
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return addCommas(val)
                },
                show: false
            },
        },
        colors: [primary]
    };
    var xchart_render = new ApexCharts(document.querySelector(xchart), xchart_options);
    xchart_render.render();

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


    function loadGraph(st_id, date, label)
    {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'POST',
            url: "<?php echo e(url('get_stock_graph')); ?>",
            data: {st_id:st_id, date:date, label:label},
            dataType: 'html',
            success: function(r) {
                $('#chart').html(r);
            },
        });
    }

    function loadNotice(st_id)
    {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'POST',
            url: "<?php echo e(url('get_stock_notice')); ?>",
            data: {st_id:st_id},
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    $('#problem_btn').text(r.problem);
                    $('#problem_btn').attr('data-notice', r.problem_notice);
                    $('#waiting_online_btn').text(r.online);
                    $('#waiting_online_btn').attr('data-notice', r.online_notice);
                    $('#waiting_offline_btn').text(r.offline);
                    $('#waiting_offline_btn').attr('data-notice', r.offline_notice);
                } else {
                    $('#problem_btn').text('0');
                    $('#problem_btn').attr('data-notice', '');
                    $('#waiting_online_btn').text('0');
                    $('#waiting_online_btn').attr('data-notice', '');
                    $('#waiting_offline_btn').text('0');
                    $('#waiting_offline_btn').attr('data-notice', '');
                }
            },

        });
    }

    $(document).ready(function() {
        loadNotice($('#st_id_filter').val());
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var stock_tracking_table = $('#StockTrackingtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'B<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('stock_tracking_datatables')); ?>",
                data : function (d) {
                    d.search = $('#stock_tracking_search').val();
                    d.status = $('#status_filter').val();
                    d.psc_id = $('#psc_id').val();
                    d.std_id = $('#std_id').val();
                    d.br_id = $('#br_id').val();
                    d.st_id = $('#st_id_filter').val();
                    d.date = date;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'plst_id', searchable: false},
            { data: 'datetime', name: 'plst_created', searchable: false },
            { data: 'article', name: 'p_name', searchable: false },
            { data: 'invoice', name: 'p_name', searchable: false },
            { data: 'cust_name', name: 'cust_name', searchable: false },
            { data: 'qty', name: 'p_name', searchable: false },
            { data: 'product_location', name: 'pl_code', searchable: false },
            { data: 'status', name: 'plst_status', searchable: false },
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

        $('#status_filter, #psc_id, #std_id, #br_id').on('change', function() {
            stock_tracking_table.draw(false);
        });

        $('#st_id_filter').on('change', function() {
            stock_tracking_table.draw(false);
            loadNotice($(this).val());
        });

        stock_tracking_table.buttons().container().appendTo($('#stock_tracking_excel_btn' ));
        $('#stock_tracking_search').on('keyup', function() {
            var query = $(this).val();
            if (jQuery.trim(query).length > 4) {
                stock_tracking_table.draw(false);
            } else if (jQuery.trim(query).length < 1) {
                stock_tracking_table.draw(false);
            }
        });

        $(document).delegate('#user_detail_btn', 'click', function() {
            var article = $(this).text();
            var invoice = $(this).attr('invoice');
            var picker = $(this).attr('picker');
            var helper = $(this).attr('helper');
            var cashier = $(this).attr('cashier');
            var packer = $(this).attr('packer');
            var customer = $(this).attr('customer');
            var status = $(this).attr('status');
            var pos_note = $(this).attr('pos_note');
            var refund_exchange_note = $(this).attr('refund_exchange_note');
            var last_updated = $(this).attr('updated');

            $('#article_label').text(article);
            $('#invoice_label').text(invoice);
            $('#picker_label').text(picker);
            $('#helper_label').text(helper);
            $('#cashier_label').text(cashier);
            $('#packer_label').text(packer);
            $('#customer_label').text(customer);
            $('#status_label').text(status);
            $('#invoice_note_label').text(pos_note);
            $('#refund_exchange_note_label').text(refund_exchange_note);
            $('#last_updated_label').text(last_updated);
            $('#UserModal').modal('show');
            if (invoice != '') {
                $('#delete_btn').addClass('d-none');
            } else {
                $('#delete_btn').removeClass('d-none');
                $('#delete_btn').attr('data-id', $(this).attr('backend'));
            }
        });

        $(document).delegate('#waiting_offline_btn', 'click', function(e) {
            e.preventDefault();
            var notice = $(this).attr('data-notice');
            swal('', notice, 'info');
        });

        $(document).delegate('#waiting_online_btn', 'click', function(e) {
            e.preventDefault();
            var notice = $(this).attr('data-notice');
            swal('', notice, 'info');
        });
        
        $(document).delegate('#problem_btn', 'click', function(e) {
            e.preventDefault();
            if ($(this).text() != '0') {
                var notice = $(this).attr('data-notice');
                swal('', notice, 'info');
            }
        });

        $(document).delegate('#graph_btn', 'click', function(e) {
            e.preventDefault();
            $('#GraphModal').modal('show');
            setTimeout(() => {
                loadGraph($('#st_id_filter').val(), date, $('#chart_filter').val());
            }, 500);
        });

        $(document).delegate('#chart_filter', 'change', function(e) {
            e.preventDefault();
            var label = $(this).val();
            loadGraph($('#st_id_filter').val(), date, label);
        });

        $(document).delegate('#delete_btn', 'click', function(e){
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
                        data: {id:id},
                        dataType: 'json',
                        url: "<?php echo e(url('plst_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                stock_tracking_table.draw();
                                $('#UserModal').modal('hide');
                                swal("Berhasil", "Data berhasil dihapus", "success");
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
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

            if ((end - start) < 100 || label == 'Hari Ini') {
                title = 'Hari Ini:';
                range = start.format('DD MMM YYYY');
                date = start.format('YYYY-MM-DD');
            } else if (label == 'Kemarin') {
                title = 'Kemarin:';
                range = start.format('DD MMM YYYY');
                date = start.format('YYYY-MM-DD');
            } else {
                range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
                date = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            stock_tracking_table.draw(false);
            loadNotice($('#st_id_filter').val());
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
<?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/stock_tracking/stock_tracking_js.blade.php ENDPATH**/ ?>