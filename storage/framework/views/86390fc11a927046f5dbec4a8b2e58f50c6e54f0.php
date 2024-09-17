<script src="<?php echo e(asset('app')); ?>/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<script src="<?php echo e(asset('cdn')); ?>/jquery.table2excel.js?v2"></script>
<script src="<?php echo e(asset('cdn/chart.js')); ?>"></script>
<div id="_nettsales"></div>
<div id="_profits"></div>
<div id="_cnettsales"></div>
<div id="_cprofits"></div>
<div id="_purchases"></div>
<div id="_debts"></div>
<div id="_cc_assets"></div>
<div id="_c_assets"></div>
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

    const primary = '#072544';
    const nsChart = "#nettsaleChart";
    var nsChart_options = {
        series: [],
        chart: {
            height: 350,
            type: 'bar',
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        plotOptions: {
        bar: {
            borderRadius: 4,
        }
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        xaxis: {
            categories: ['Jual Bersih'],
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
    var nsChart_render = new ApexCharts(document.querySelector(nsChart), nsChart_options);
    nsChart_render.render();

    const prChart = "#profitChart";
    var prChart_options = {
        series: [],
        chart: {
            height: 350,
            type: 'bar',
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        plotOptions: {
        bar: {
            borderRadius: 4,
        }
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        xaxis: {
            categories: ['Profit'],
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
    var prChart_render = new ApexCharts(document.querySelector(prChart), prChart_options);
    prChart_render.render();

    const cnsChart = "#cnettsaleChart";
    var cnsChart_options = {
        series: [],
        chart: {
            height: 350,
            type: 'bar',
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        plotOptions: {
        bar: {
            borderRadius: 4,
        }
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        xaxis: {
            categories: ['Cross Jual Bersih'],
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
    var cnsChart_render = new ApexCharts(document.querySelector(cnsChart), cnsChart_options);
    cnsChart_render.render();

    const cprChart = "#cprofitChart";
    var cprChart_options = {
        series: [],
        chart: {
            height: 350,
            type: 'bar',
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        plotOptions: {
        bar: {
            borderRadius: 4,
        }
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        xaxis: {
            categories: ['Cross Profit'],
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
    var cprChart_render = new ApexCharts(document.querySelector(cprChart), cprChart_options);
    cprChart_render.render();

    const pcChart = "#purchaseChart";
    var pcChart_options = {
        series: [],
        chart: {
            height: 350,
            type: 'bar',
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        plotOptions: {
        bar: {
            borderRadius: 4,
        }
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        xaxis: {
            categories: ['Pembelian'],
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
    var pcChart_render = new ApexCharts(document.querySelector(pcChart), pcChart_options);
    pcChart_render.render();

    const dbChart = "#debtChart";
    var dbChart_options = {
        series: [],
        chart: {
            height: 350,
            type: 'bar',
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        plotOptions: {
        bar: {
            borderRadius: 4,
        }
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        xaxis: {
            categories: ['Hutang'],
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
    var dbChart_render = new ApexCharts(document.querySelector(dbChart), dbChart_options);
    dbChart_render.render();

    const asChart = "#assetChart";
    var asChart_options = {
        series: [],
        chart: {
            height: 350,
            type: 'bar',
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        plotOptions: {
        bar: {
            borderRadius: 4,
        }
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        xaxis: {
            categories: ['Asset Cash/Credit'],
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
    var asChart_render = new ApexCharts(document.querySelector(asChart), asChart_options);
    asChart_render.render();

    const casChart = "#consignAssetChart";
    var casChart_options = {
        series: [],
        chart: {
            height: 350,
            type: 'bar',
            zoom: {
                enabled: false
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        plotOptions: {
        bar: {
            borderRadius: 4,
        }
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        xaxis: {
            categories: ['Consignment'],
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
    var casChart_render = new ApexCharts(document.querySelector(casChart), casChart_options);
    casChart_render.render();

    function getSalesGraph(div, hidden_range)
    {
        $(div).addClass('d-none');
        $('#ns_loading').removeClass('d-none');
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_range:hidden_range, type:$('#scross_filter').val()},
            dataType: 'html',
            url: "<?php echo e(url('get_sales_graph')); ?>",
            success: function(r) {
                $('#ns_loading').addClass('d-none');
                $('#nettsaleChart').removeClass('d-none');
                $('#_nettsales').html(r);
            }
        });
    }

    $(document).delegate('#ns_show_btn', 'click', function() {
        var hidden_range = $('#dashboard_date').val();
        $('.button-show_ns').addClass('d-none');
        getSalesGraph('#ns_show_btn', hidden_range);
    });

    $(document).delegate('#scross_filter', 'change', function() {
        var hidden_range = $('#dashboard_date').val();
        getSalesGraph('#ns_show_btn', hidden_range);
    });

    function getProfitGraph(div, hidden_range)
    {
        $(div).addClass('d-none');
        $('#pr_loading').removeClass('d-none');
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_range:hidden_range, type:$('#pcross_filter').val()},
            dataType: 'html',
            url: "<?php echo e(url('get_profit_graph')); ?>",
            success: function(r) {
                $('#pr_loading').addClass('d-none');
                $('#profitChart').removeClass('d-none');
                $('#_profits').html(r);
            }
        });
    }

    $(document).delegate('#pr_show_btn', 'click', function() {
        var hidden_range = $('#dashboard_date').val();
        $('.button-show_pr').addClass('d-none');
        getProfitGraph('#pr_show_btn', hidden_range);
    });

    $(document).delegate('#pcross_filter', 'change', function() {
        var hidden_range = $('#dashboard_date').val();
        getProfitGraph('#pr_show_btn', hidden_range);
    });

    function getcSalesGraph(div, hidden_range)
    {
        $(div).addClass('d-none');
        $('#cns_loading').removeClass('d-none');
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_range:hidden_range},
            dataType: 'html',
            url: "<?php echo e(url('get_csales_graph')); ?>",
            success: function(r) {
                $('#cns_loading').addClass('d-none');
                $('#cnettsaleChart').removeClass('d-none');
                $('#_cnettsales').html(r);
            }
        });
    }

    $(document).delegate('#cns_show_btn', 'click', function() {
        var hidden_range = $('#dashboard_date').val();
        $('.button-show_cns').addClass('d-none');
        getcSalesGraph('#cns_show_btn', hidden_range);
    });

    function getcProfitGraph(div, hidden_range)
    {
        $(div).addClass('d-none');
        $('#cpr_loading').removeClass('d-none');
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_range:hidden_range},
            dataType: 'html',
            url: "<?php echo e(url('get_cprofit_graph')); ?>",
            success: function(r) {
                $('#cpr_loading').addClass('d-none');
                $('#cprofitChart').removeClass('d-none');
                $('#_cprofits').html(r);
            }
        });
    }

    $(document).delegate('#cpr_show_btn', 'click', function() {
        var hidden_range = $('#dashboard_date').val();
        $('.button-show_cpr').addClass('d-none');
        getcProfitGraph('#cpr_show_btn', hidden_range);
    });

    function getPurchaseGraph(div, hidden_range)
    {
        $(div).addClass('d-none');
        $('#pc_loading').removeClass('d-none');
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_range:hidden_range},
            dataType: 'html',
            url: "<?php echo e(url('get_purchase_graph')); ?>",
            success: function(r) {
                $('#pc_loading').addClass('d-none');
                $('#purchaseChart').removeClass('d-none');
                $('#_purchases').html(r);
            }
        });
    }

    $(document).delegate('#pc_show_btn', 'click', function() {
        var hidden_range = $('#dashboard_date').val();
        $('.button-show_pc').addClass('d-none');
        getPurchaseGraph('#pc_show_btn', hidden_range);
    });

    function getCCAssetGraph(div, hidden_range)
    {
        $(div).addClass('d-none');
        $('#a_loading').removeClass('d-none');
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_range:hidden_range},
            dataType: 'html',
            url: "<?php echo e(url('get_cc_asset_graph')); ?>",
            success: function(r) {
                $('#a_loading').addClass('d-none');
                $('#assetChart').removeClass('d-none');
                $('#_cc_assets').html(r);
            }
        });
    }

    $(document).delegate('#a_show_btn', 'click', function() {
        var hidden_range = $('#dashboard_date').val();
        $('.button-show_a').addClass('d-none');
        getCCAssetGraph('#a_show_btn', hidden_range);
    });

    function getConsignAssetGraph(div, hidden_range)
    {
        $(div).addClass('d-none');
        $('#ca_loading').removeClass('d-none');
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_range:hidden_range},
            dataType: 'html',
            url: "<?php echo e(url('get_ca_asset_graph')); ?>",
            success: function(r) {
                $('#ca_loading').addClass('d-none');
                $('#consignAssetChart').removeClass('d-none');
                $('#_c_assets').html(r);
            }
        });
    }

    $(document).delegate('#ca_show_btn', 'click', function() {
        var hidden_range = $('#dashboard_date').val();
        $('.button-show_ca').addClass('d-none');
        getConsignAssetGraph('#ca_show_btn', hidden_range);
    });

    function getDebtGraph(div, hidden_range)
    {
        $(div).addClass('d-none');
        $('#d_loading').removeClass('d-none');
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_range:hidden_range},
            dataType: 'html',
            url: "<?php echo e(url('get_debt_graph')); ?>",
            success: function(r) {
                $('#d_loading').addClass('d-none');
                $('#debtChart').removeClass('d-none');
                $('#_debts').html(r);
            }
        });
    }

    $(document).delegate('#d_show_btn', 'click', function() {
        var hidden_range = $('#dashboard_date').val();
        $('.button-show_d').addClass('d-none');
        getDebtGraph('#d_show_btn', hidden_range);
    });

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var brand_value_table = $('#BrandValuetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Blrt<"text-right"ipB>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('brand_value_datatables')); ?>",
                data : function (d) {
                    d.data_type = $('#data_type').val();
                    d.search = $('#brands_value_search').val();
                    d.dashboard_date = $('#dashboard_date').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'br_name', name: 'br_name' },
            { data: 'qty', name: 'qty' },
            { data: 'value', name: 'value' },
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
            order: [[3, 'desc']],
        });

        $('#brands_value_search').on('keyup', function () {
            brand_value_table.draw(false);
        });

        var activity_table = $('#Activitytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('user_activity_datatables')); ?>",
                data : function (d) {
                    d.search = $('#user_activity_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'uaid', searchable: false},
            { data: 'st_name', name: 'st_name' },
            { data: 'u_name', name: 'u_name' },
            { data: 'ua_description', name: 'ua_description' },
            { data: 'ua_created_at_show', name: 'ua_created_at' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        // =================== BY STORE ==================== //

        $('#purchase_label').on('click', function() {
            $('#data_type').val('purchases');
            $('#BrandValueModal').on('show.bs.modal', function(){
                brand_value_table.draw(false);
            }).modal('show');
        });

        $('#debt_label').on('click', function() {
            swal('Dashboard V2', 'Silahkan lihat detail pada dashboard v2', 'warning');
        });

        $('#assets_label').on('click', function() {
            $('#data_type').val('assets');
            jQuery.noConflict();
            $('#BrandProfitModal').modal('show');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {dashboard_date:$('#dashboard_date').val()},
                dataType: 'json',
                url: "<?php echo e(url('load_assets')); ?>",
                success: function(r) {
                    jQuery('#BrandProfittb').find('tr:not(:has(th))').remove();
                    setTimeout(() => {
                        $(r.item).each(function(index, row) {
                            if (row['cc_assets'] > 0) {
                                jQuery('#BrandProfittb tr:last').after("<tr><td>"+row['br_name']+"</td><td>"+addCommas(row['cc_assets'])+"</td></tr>");
                            }
                        });
                    }, 1000);
                }
            });
        });

        $('#consign_assets_label').on('click', function() {
            $('#data_type').val('consign_assets');
            jQuery.noConflict();
            $('#BrandProfitModal').modal('show');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {dashboard_date:$('#dashboard_date').val()},
                dataType: 'json',
                url: "<?php echo e(url('load_cassets')); ?>",
                success: function(r) {
                    jQuery('#BrandProfittb').find('tr:not(:has(th))').remove();
                    setTimeout(() => {
                        $(r.item).each(function(index, row) {
                            if (row['c_assets'] > 0) {
                                jQuery('#BrandProfittb tr:last').after("<tr><td>"+row['br_name']+"</td><td>"+addCommas(row['c_assets'])+"</td></tr>");
                            }
                        });
                    }, 1000);
                }
            });
        });

        $('#nett_sales_label').on('click', function() {
            $('#data_type').val('sales');
            $('#BrandValueModal').on('show.bs.modal', function(){
                brand_value_table.draw(false);
            }).modal('show');
        });

        $('#profit_label').on('click', function() {
            $('#data_type').val('profits');
            jQuery.noConflict();
            $('#BrandProfitModal').modal('show');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {dashboard_date:$('#dashboard_date').val()},
                dataType: 'json',
                url: "<?php echo e(url('load_profit')); ?>",
                success: function(r) {
                    jQuery('#BrandProfittb').find('tr:not(:has(th))').remove();
                    setTimeout(() => {
                        $(r.item).each(function(index, row) {
                            if (row['profits'] > 0) {
                                jQuery('#BrandProfittb tr:last').after("<tr><td>"+row['br_name']+"</td><td>"+addCommas(row['profits'])+"</td></tr>");
                            }
                        });
                    }, 1000);
                }
            });
        });

        $('#cnett_sales_label').on('click', function() {
            $('#data_type').val('csales');
            $('#BrandValueModal').on('show.bs.modal', function(){
                brand_value_table.draw(false);
            }).modal('show');
        });

        $('#cprofit_label').on('click', function() {
            $('#data_type').val('cprofits');
            jQuery.noConflict();
            $('#BrandProfitModal').modal('show');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {dashboard_date:$('#dashboard_date').val()},
                dataType: 'json',
                url: "<?php echo e(url('load_cprofit')); ?>",
                success: function(r) {
                    jQuery('#BrandProfittb').find('tr:not(:has(th))').remove();
                    setTimeout(() => {
                        $(r.item).each(function(index, row) {
                            if (row['cprofits'] > 0) {
                                jQuery('#BrandProfittb tr:last').after("<tr><td>"+row['br_name']+"</td><td>"+addCommas(row['cprofits'])+"</td></tr>");
                            }
                        });
                    }, 1000);
                }
            });
        });

        $(document).delegate('#brand_assets_detail_btn', 'click', function() {
            var br_id = $(this).attr('data-br_id');
            var br_name = $(this).attr('data-br_name');
            $('#_br_id').val(br_id);
            $('#brands_name_label').text(br_name);
            assets_table.draw(false);
            $('#AssetsModal').modal('show');
        });

        $('#user_activity_search').on('keyup', function() {
            activity_table.draw(false);
        });

        $('#brands_search').on('keyup', function() {
            brand_assets_table.draw(false);
        });

        $('#brands_nett_sales_search').on('keyup', function() {
            brand_nett_sales_table.draw(false);
        });

        $('#detail_activity_btn, #detail_activity_second_btn').on('click', function() {
            jQuery.noConflict();
            $('#ActivityModal').modal('show');
            activity_table.draw(false);
        });

        $('#export_excel').on('click', function() {
            jQuery("#ProductRatingtb").table2excel({
                filename: "Laporan Rating Artikel",
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
            var st_id = $('#st_filter').val();
            var pc_id = $('#pc_filter').val();
            var specific = $('#specific_filter').val();

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

            $('#ns_show_btn').removeClass('d-none');
            $('#nettsaleChart').addClass('d-none');
            $('#nett_sales_label_reload').text('');

            $('#pr_show_btn').removeClass('d-none');
            $('#profitChart').addClass('d-none');
            $('#profit_label_reload').text('');

            $('#cns_show_btn').removeClass('d-none');
            $('#cnettsaleChart').addClass('d-none');
            $('#cnett_sales_label_reload').text('');

            $('#cpr_show_btn').removeClass('d-none');
            $('#cprofitChart').addClass('d-none');
            $('#cprofit_label_reload').text('');

            $('#pc_show_btn').removeClass('d-none');
            $('#purchaseChart').addClass('d-none');
            $('#purchase_label_reload').text('');

            $('#d_show_btn').removeClass('d-none');
            $('#debtChart').addClass('d-none');
            $('#debt_label').text('');

            $('#a_show_btn').removeClass('d-none');
            $('#assetChart').addClass('d-none');
            $('#assets_label').text('');

            $('#ca_show_btn').removeClass('d-none');
            $('#consignAssetChart').addClass('d-none');
            $('#consign_assets_label').text('');
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
<?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/dashboard/dashboard_js.blade.php ENDPATH**/ ?>