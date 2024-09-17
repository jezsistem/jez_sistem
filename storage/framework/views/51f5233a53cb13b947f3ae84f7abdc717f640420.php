<script src="<?php echo e(asset('app')); ?>/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<script src="<?php echo e(asset('cdn')); ?>/jquery.table2excel.js?v2"></script>
<script>
    const primary = '#6993FF';
    const success = '#1BC5BD';
    const info = '#8950FC';
    const warning = '#FFA800';
    const danger = '#F64E60';

    const offlineChart = "#offline_chart";
    var offline_options = {
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
            categories: ['Target', 'Tercapai'],
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return addCommas(val)
                }
            }
        },
        colors: [primary]
    };
    var offline_chart = new ApexCharts(document.querySelector(offlineChart), offline_options);
    offline_chart.render();

    const onlineChart = "#online_chart";
    var online_options = {
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
            categories: ['Target', 'Tercapai'],
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return addCommas(val)
                }
            }
        },
        colors: [primary]
    };
    var online_chart = new ApexCharts(document.querySelector(onlineChart), online_options);
    online_chart.render();
    
    const crossChart = "#cross_chart";
    var cross_options = {
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
            categories: ['Tercapai'],
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return addCommas(val)
                }
            }
        },
        colors: [primary]
    };
    var cross_chart = new ApexCharts(document.querySelector(crossChart), cross_options);
    cross_chart.render();

    const articleChart = "#article_chart";
    var article_options = {
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
            categories: [],
        },
        yaxis: {
            title: {
                text: '$ (thousands)'
            }
        },
        colors: [primary]
    };
    var article_chart = new ApexCharts(document.querySelector(articleChart), article_options);
    article_chart.render();

    const brandChart = "#brand_chart";
    var brand_options = {
        series: [
            {
                name: ['Adidas', 'Specs'],
                data: ['100', '200'],
                color: success
            }
        ],
        labels: ['Adidas', 'Specs'],
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
            categories: [],
        },
        yaxis: {
            title: {
                text: '$ (thousands)'
            }
        },
        colors: [primary]
    };
    var brand_chart = new ApexCharts(document.querySelector(brandChart), brand_options);
    brand_chart.render();

    function renderCross(range)
    {
		var st_id = $('#st_id_filter').val();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'POST',
            url: "<?php echo e(url('cross_chart')); ?>",
            data: {_range:range, _type:'online',_st_id:st_id},
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    //alert(r.target+' '+r.get);
                    cross_chart.updateSeries([
                        {
                            name: 'Target',
                            data: ['0'],
                            color: success
                        },
                        {
                        name: 'Tercapai',
                        data: [r.get],
                        color: primary
                    }]);
                } else {
                    swal('ERROR', 'ERROR', 'warning');
                }
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
    }

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
    
    function getPower(dashboard_date, st_id, pc_id, specific)
    {   
        $('#ProductRatingtb').find('tr:not(:has(th))').remove();
        if ($('#st_filter').val() != '') {
            $('#article_power_label').text('Mohon ditunggu, sedang menyiapkan data ..');
        }
        if ($('#pc_filter').val() == '') {
            $('#article_power_label').text('Product Rating');
        }   
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {dashboard_date:dashboard_date, st_id:st_id, pc_id:pc_id, specific:specific},
            dataType: 'json',
            url: "<?php echo e(url('get_power')); ?>",
            success: function(r) {
                if (r.status == '200'){
                    $(r.data).each(function(index, row) {
                        var max = r.max;
                        var rating = parseFloat(row['power']) / parseFloat(max) * 100;
                        var power = rating.toFixed();
                        // jQuery('#ProductRatingtb tr:last').after("<tr><td>"+(parseInt(index)+parseInt(1))+"</td><td>"+addCommas(row['power'])+" ("+power+"/100)</td><td>"+row['brand']+"</td><td>"+row['article']+"</td><td>"+row['aging']+"</td><td>"+row['hpp']+"</td><td>"+row['hj']+"</td><td>"+row['profit']+"</td><td>"+row['sold']+"</td><td>"+row['total']+"</td></tr>");
                        jQuery('#ProductRatingtb tr:last').after("<tr><td>"+(parseInt(index)+parseInt(1))+"</td><td>"+addCommas(row['power'])+" ("+power+"/100)</td><td>"+row['brand']+"</td><td>"+row['article']+"</td><td>"+row['aging']+"</td><td>"+row['hj']+"</td><td>"+row['sold']+"</td></tr>");
                    });
                $('#article_power_label').text('Product Rating');
                } else {
                    swal('Gagal', 'Gagal menampilkan data', 'error');
                }
            }
        });
        return false;
    }

    function renderOfflineTarget(range, label)
    {
		var st_id = $('#st_id_filter').val();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'POST',
            url: "<?php echo e(url('target_chart')); ?>",
            data: {_range:range, _type:'offline', _label:label, _st_id:st_id},
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    //alert(r.target+' '+r.get);
                    offline_chart.updateSeries([{
                        name: 'Target',
                        data: [r.target],
                        color: success
                    }, {
                        name: 'Tercapai',
                        data: [r.get],
                        color: primary
                    }]);
                } else {
                    swal('ERROR', 'ERROR', 'warning');
                }
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
    }

    function renderOnlineTarget(range, label)
    {
		var st_id = $('#st_id_filter').val();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'POST',
            url: "<?php echo e(url('target_chart')); ?>",
            data: {_range:range, _type:'online', _label:label, _st_id:st_id},
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    //alert(r.target+' '+r.get);
                    online_chart.updateSeries([{
                        name: 'Target',
                        data: [r.target],
                        color: success
                    }, {
                        name: 'Tercapai',
                        data: [r.get],
                        color: primary
                    }]);
                } else {
                    swal('ERROR', 'ERROR', 'warning');
                }
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
    }

    function renderArticleChart(range, label)
    {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'POST',
            url: "<?php echo e(url('article_chart')); ?>",
            data: {_range:range, _type:'offline', _label:label},
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    //alert(r.target+' '+r.get);
                    article_chart.updateSeries([{
                        name: [r.article],
                        data: [r.total],
                        color: success
                    }]);
                } else {
                    swal('ERROR', 'ERROR', 'warning');
                }
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
    }

    function renderBrandChart(range, label)
    {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'POST',
            url: "<?php echo e(url('brand_chart')); ?>",
            data: {_range:range, _type:'offline', _label:label},
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    //alert(r.target+' '+r.get);
                    brand_chart.updateSeries([{
                        name: [r.brand],
                        data: [r.total],
                        color: success
                    }]);
                } else {
                    swal('ERROR', 'ERROR', 'warning');
                }
            },
            error: function(data){
                swal('Error', data, 'error');
            }
        });
    }
    
    $(document).ready(function() {
        // $('body').addClass('kt-primary--minimize aside-minimize');
        var online_table = $('#Onlinetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('pos_summary_online_datatables')); ?>",
                data : function (d) {
                    d.pos_summary_date = $('#pos_summary_date').val();
                    d.st_id = $('#st_id_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'u_id', searchable: false},
            { data: 'u_name', name: 'u_name'},
            { data: 'sales', name: 'u_name', orderable: false },
            { data: 'sales_total', name: 'u_name', orderable: false },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var offline_table = $('#Offlinetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('pos_summary_offline_datatables')); ?>",
                data : function (d) {
                    d.pos_summary_date = $('#pos_summary_date').val();
                    d.st_id = $('#st_id_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'u_id', searchable: false},
            { data: 'u_name', name: 'u_name'},
            { data: 'sales', name: 'u_name', orderable: false },
            { data: 'sales_total', name: 'u_name', orderable: false },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

		var sales_detail_table = $('#SalesDetailtb').DataTable({
			destroy: true,
			processing: true,
			serverSide: true,
			responsive: false,
			deferRender: false,
			dom: 'rt<"text-right"ip>',
			buttons: [
				{ "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
			],
			ajax: {
				url : "<?php echo e(url('sales_detail_datatables')); ?>",
				data : function (d) {
					d.pos_summary_date = $('#pos_summary_date').val();
					d.u_id = $('#u_id').val();
                    d.st_id = $('#st_id_filter').val();
				}
			},
			columns: [
			{ data: 'DT_RowIndex', name: 'u_id', searchable: false},
			{ data: 'pos_invoice', name: 'pos_invoice'},
			{ data: 'sales', name: 'sales', orderable: false },
			{ data: 'sales_total', name: 'sales_total', orderable: false },
			{ data: 'created_at', name: 'created_at', orderable: false },
			], 
			columnDefs: [
			{
				"targets": 0,
				"className": "text-center",
				"width": "0%"
			}],
			order: [[0, 'desc']],
		});

		var sales_item_detail_table = $('#SalesItemDetailtb').DataTable({
			destroy: true,
			processing: true,
			serverSide: true,
			responsive: false,
			deferRender: false,
			dom: 'rt<"text-right"ip>',
			buttons: [
				{ "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
			],
			ajax: {
				url : "<?php echo e(url('sales_item_detail_datatables')); ?>",
				data : function (d) {
					d.pt_id = $('#pt_id').val();
				}
			},
			columns: [
			{ data: 'DT_RowIndex', name: 'ptd_id', searchable: false},
			{ data: 'article', name: 'article'},
			{ data: 'pos_td_qty', name: 'pos_td_qty', orderable: false },
			{ data: 'pos_td_total_price', name: 'pos_td_total_price', orderable: false },
			{ data: 'created_at', name: 'created_at', orderable: false },
			], 
			columnDefs: [
			{
				"targets": 0,
				"className": "text-center",
				"width": "0%"
			}],
			order: [[0, 'desc']],
		});

		$('#st_id_filter').on('change', function() {
		    var storage = $('#st_id_filter option:selected').text();
            $('.offline_chart').removeClass('col-lg-6');
            $('.online_chart').removeClass('col-lg-6');
            $('.offline_chart').addClass('col-lg-4');
            $('.online_chart').addClass('col-lg-4');
            $('.cross_chart').removeClass('d-none');
			var hidden_range = $('#pos_summary_date').val();
			var label = '';
			online_table.draw();
			offline_table.draw();
            renderOfflineTarget(hidden_range, label);
            renderOnlineTarget(hidden_range, label);
            renderCross(hidden_range);
		});
		
		$('#st_filter').on('change', function() {
            var dashboard_date = $('#pos_summary_date').val();
            var st_id = $(this).val();
            var pc_id = $('#pc_filter').val();
            var specific = $('#specific_filter').val();
            getPower(dashboard_date, st_id, pc_id, specific);
        });

        $('#pc_filter').on('change', function() {
            var dashboard_date = $('#pos_summary_date').val();
            var st_id = $('#st_filter').val();
            var pc_id = $(this).val();
            var specific = $('#specific_filter').val();
            getPower(dashboard_date, st_id, pc_id, specific);
        });

        $('#specific_filter').on('change', function() {
            var dashboard_date = $('#pos_summary_date').val();
            var st_id = $('#st_filter').val();
            var pc_id = $('#pc_filter').val();
            var specific = $(this).val();
            getPower(dashboard_date, st_id, pc_id, specific);
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
            //alert(range+' | '+hidden_range);
            $('#pos_summary_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            renderOfflineTarget(hidden_range, label);
            renderOnlineTarget(hidden_range, label);
            renderCross(hidden_range);
            //alert(hidden_range);
            offline_table.draw();
            online_table.draw();
            getPower(hidden_range, st_id, pc_id, specific);
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

		$(document).delegate('#sales_detail_btn', 'click', function() {
			var u_id = $(this).attr('data-u_id');
			var u_name = $(this).attr('data-u_name');
			$('#u_id').val(u_id);
			$('#sales_detail_label').text(u_name);
			$('#SalesDetailModal').on('show.bs.modal', function() {
				sales_detail_table.draw();
			}).modal('show');
		});

		$(document).delegate('#sales_item_detail_btn', 'click', function() {
			var pt_id = $(this).attr('data-pt_id');
			var invoice = $(this).attr('data-invoice');
			$('#pt_id').val(pt_id);
			$('#sales_item_detail_label').text(invoice);
			$('#SalesItemDetailModal').on('show.bs.modal', function() {
				sales_item_detail_table.draw();
			}).modal('show');
		});
    });
</script><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/pos_summary/pos_summary_js.blade.php ENDPATH**/ ?>