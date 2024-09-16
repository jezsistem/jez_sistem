<script src="<?php echo e(asset('app')); ?>/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<script src="<?php echo e(asset('cdn')); ?>/jquery.table2excel.js?v2"></script>
<script>
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

    function getSummary(date)
    {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {date:date},
            url: "<?php echo e(url('dashboard_v2_summary')); ?>",
            success: function(r) {
                if (r.status == '200'){
                    $('#nett_sales_label').text(r.nett_sales);
                    $('#profit_label').text(r.profit);
                    $('#purchase_label').text(r.purchase);
                    $('#assets_label').text(r.assets);
                    $('#consign_assets_label').text(r.consign_assets);
                    $('#debt_label').text(r.debt);
                } else {
                    swal('Failed', 'Failed', 'error');
                }
            }
        });
        return false;
    }

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var store_table = $('#Storetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('store_info_datatables')); ?>",
                data : function (d) {
                    d.search = $('#store_search').val();
                    d.dashboard_date = $('#dashboard_date').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'created_at_x', name: 'created_at' },
            { data: 'st_name', name: 'st_name' },
            { data: 'sales', name: 'sales' },
            { data: 'profit', name: 'profit' },
            { data: 'purchase', name: 'purchase' },
            { data: 'cc_asset', name: 'cc_asset' },
            { data: 'con_asset', name: 'con_asset' },
            { data: 'debt', name: 'debt' },
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

        var brand_table = $('#Brandtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('brand_info_datatables')); ?>",
                data : function (d) {
                    d.search = $('#brand_search').val();
                    d.dashboard_date = $('#dashboard_date').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'created_at_x', name: 'created_at' },
            { data: 'br_name', name: 'br_name' },
            { data: 'sales', name: 'sales' },
            { data: 'profit', name: 'profit' },
            { data: 'purchase', name: 'purchase' },
            { data: 'cc_asset', name: 'cc_asset' },
            { data: 'con_asset', name: 'con_asset' },
            { data: 'debt', name: 'debt' },
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

        $('#store_search').on('keyup', function() {
            store_table.draw();
        });
        
        $('#brand_search').on('keyup', function() {
            brand_table.draw();
        });

        $('#store_excel').on('click', function() {
            jQuery("#Storetb").table2excel({
                filename: "Laporan Store",
            });
        });

        $('#brand_excel').on('click', function() {
            jQuery("#Brandtb").table2excel({
                filename: "Laporan Brand",
            });
        });

        $(document).delegate('#synchronize_btn', 'click', function() {
            swal({
                title: "Synchronize new data..?",
                text: "the data will be updated",
                icon: "warning",
                buttons: [
                    'No',
                    'Yes'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $('#WaitingModal').modal('show');
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "GET",
                        dataType: 'json',
                        url: "<?php echo e(url('auto/9999/close_data')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                $('#WaitingModal').modal('hide');
                                swal("Success", "Success", "success");
                                store_table.draw();
                                brand_table.draw();
                                getSummary($('#dashboard_date').val());
                            } else {
                                swal('Failed', 'Failed', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        jQuery.noConflict();
        $('#NotifModal').modal('show');
        setTimeout(() => {
            $('#NotifModal').modal('hide');
        }, 6000);
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
            $('#dashboard_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            store_table.draw();
            brand_table.draw();
            getSummary($('#dashboard_date').val());
        }

        picker.daterangepicker({
            direction: KTUtil.isRTL(),
            startDate: start,
            endDate: end,
            opens: 'center',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            singleDatePicker: true,
            ranges: {
                'Hari Ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]
            }
        }, cb);
        cb(start, end, '');
    });
</script><?php /**PATH /home/jezpro.id/public_html/resources/views/app/dashboard_v2/dashboard_v2_js.blade.php ENDPATH**/ ?>