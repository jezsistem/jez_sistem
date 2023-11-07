<script src="{{ asset('app') }}/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
<script src="{{ asset('cdn/chart.js') }}"></script>
<script>
    // $('body').addClass('kt-primary--minimize aside-minimize');
    var dashboard_date = '';
    var active_label = '';
    var st_id = [];

    const primary = '#072544';
    const brChart = "#chart";

    var brChart_options = {
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
    var brChart_render = new ApexCharts(document.querySelector(brChart), brChart_options);
    brChart_render.render();

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

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
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
                url : "{{ url('user_activity_datatables') }}",
                data : function (d) {
                    d.search = $('#user_activity_search').val();
                    d.st_id = st_id;
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

        $('#user_activity_search').on('keyup', function() {
            activity_table.draw(false);
        });

        $('#detail_activity_btn').on('click', function() {
            jQuery.noConflict();
            $('#ActivityModal').modal('show');
            activity_table.draw(false);
        });

        $(document).delegate('.st_selection', 'click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            if ($(this).hasClass('btn-primary')) {
                st_id = $.grep(st_id, function(value) {
                    return value != id;
                });
                $(this).removeClass('btn-primary');
                $(this).addClass('btn-light-primary');
            } else {
                st_id.push(id);
                $(this).removeClass('btn-light-primary');
                $(this).addClass('btn-primary');
            }
            getSummary(dashboard_date, st_id, $('#division_filter').val());
            if ($('#info_filter').val() == 'brand') {
                loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
            } else if ($('#info_filter').val() == 'store') {
                loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
            }
        });

        $('#division_filter').on('change', function() {
            getSummary(dashboard_date, st_id, $(this).val());
            if (active_label == 'sales' || active_label == 'profits') {
                if ($('#info_filter').val() == 'brand') {
                    loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
                } else if ($('#info_filter').val() == 'store') {
                    loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
                }
            }
        });

        $('#export_excel').on('click', function() {
            jQuery("#ProductRatingtb").table2excel({
                filename: "Laporan Rating Artikel",
            });
        });

        function exportTable(label, date, store, division)
        {
            var data = new FormData();
            data.append('label', label);
            data.append('date', date);
            data.append('store', store);
            data.append('division', division);
            $.ajax({
                type:'POST',
                url: "{{ url('export_table') }}",
                data: data,
                cache:false,
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
                        setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 10000);
                    }
                },

            });
        }

        function getSummary(date, store, division)
        {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {date:date, store:store, division:division},
                dataType: 'json',
                url: "{{ url('get_summaries')}}",
                success: function(r) {
                    if (r.status == 200){
                        $('#adm_cross_nett_sales_label').text(r.adm_cross_nett_sales);
                        $('#adm_cross_profits_label').text(r.adm_cross_profit);
                        $('#adm_nett_sales_label').text(r.adm_nett_sales);
                        $('#adm_profits_label').text(r.adm_profit);
                        $('#cross_nett_sales_label').text(r.cross_nett_sales);
                        $('#cross_profits_label').text(r.cross_profit);
                        $('#nett_sales_label').text(r.nett_sales);
                        $('#profits_label').text(r.profit);
                        $('#purchases_label').text(r.purchase);
                        $('#cc_assets_label').text(r.cc_assets);
                        $('#c_assets_label').text(r.c_assets);
                        $('#debts_label').text(r.debts);
                        $('#cc_exc_assets_label').text(r.cc_exc_assets);
                        $('#c_exc_assets_label').text(r.c_exc_assets);
                        $('#gid_label').text(r.gid);
                        $('#git_label').text(r.git);
                        setTimeout(() => {
                            $('#LoadingModal').modal('hide');
                        }, 500);
                    } else {
                        swal('Gagal', 'Gagal menampilkan data', 'error');
                    }
                }
            });
            return false;
        }

        function loadTable(label, date, store, division)
        {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "{{ url('load_table') }}",
                data: {label:label, date:date, store:store, division:division},
                dataType: 'html',
                success: function(r) {
                    $('#loadTable').html(r);
                },

            });
        }

        function loadGraph(label, date, store, division)
        {

            $('#graph_panel').removeClass('d-none');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "{{ url('load_graph') }}",
                data: {label:label, date:date, store:store, division:division},
                dataType: 'html',
                success: function(r) {
                    $('#chart').html(r);
                    loadAdminCost(label, date, store, division);
                    loadTable(label, date, store, division);
                },

            });
        }

        function loadStore(label, date, store, division)
        {

            $('#graph_panel').removeClass('d-none');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "{{ url('load_store') }}",
                data: {label:label, date:date, store:store, division:division},
                dataType: 'html',
                success: function(r) {
                    $('#chart').html(r);
                    loadAdminCost(label, date, store, division);
                    loadTable(label, date, store, division);
                },

            });
        }

        function loadAdminCost(label, date, store, division)
        {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "{{ url('load_admin_cost') }}",
                data: {label:label, date:date, store:store, division:division},
                dataType: 'json',
                success: function(r) {
                    if (r.status == '200') {
                        $('#admin_cost_result').text("Rp. "+ r.admin_cost);
                    }
                },

            });
        }

        function highlight(label , status)
        {
            $('#cross_nett_sales_btn').removeClass('btn-success');
            $('#cross_profits_btn').removeClass('btn-success');
            $('#nett_sales_btn').removeClass('btn-success');
            $('#profits_btn').removeClass('btn-success');
            $('#cc_assets_btn').removeClass('btn-success');
            $('#c_assets_btn').removeClass('btn-success');
            $('#purchases_btn').removeClass('btn-success');
            $('#debts_btn').removeClass('btn-success');
            $('#cc_exc_assets_btn').removeClass('btn-success');
            $('#c_exc_assets_btn').removeClass('btn-success');
            $('#detail_activity_btn').removeClass('btn-success');

            $('#cross_nett_sales_btn').addClass('btn-light');
            $('#cross_profits_btn').addClass('btn-light');
            $('#nett_sales_btn').addClass('btn-light');
            $('#profits_btn').addClass('btn-light');
            $('#cc_assets_btn').addClass('btn-light');
            $('#c_assets_btn').addClass('btn-light');
            $('#purchases_btn').addClass('btn-light');
            $('#debts_btn').addClass('btn-light');
            $('#cc_exc_assets_btn').addClass('btn-light');
            $('#c_exc_assets_btn').addClass('btn-light');
            $('#detail_activity_btn').addClass('btn-inventory');

            if (status == 'y') {
                $('#'+label).removeClass('btn-primary');
                $('#'+label).addClass('btn-success');
            }
        }

        $(document).delegate('#info_filter', 'change', function(e) {
            e.preventDefault();
            $('#graph_panel').addClass('d-none');
            if (active_label != '') {
                if ($('#info_filter').val() == 'brand') {
                    loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
                } else {
                    loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
                }
            }
        });

        $(document).delegate('#nett_sales_btn', 'click', function(e) {
            e.preventDefault();
            active_label = 'sales';
            if ($('#info_filter').val() == '') {
                highlight('', 'n');
                swal('Silahkan tentukan info grafik terlebih dahulu');
                return false;
            } else {
                if ($('#info_filter').val() == 'brand') {
                    loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
                } else {
                    loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
                }
            }
            highlight('nett_sales_btn', 'y');
        });

        $(document).delegate('#profits_btn', 'click', function(e) {
            e.preventDefault();
            active_label = 'profits';
            if ($('#info_filter').val() == '') {
                highlight('', 'n');
                swal('Silahkan tentukan info grafik terlebih dahulu');
                return false;
            } else {
                if ($('#info_filter').val() == 'brand') {
                    loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
                } else {
                    loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
                }
            }
            highlight('profits_btn', 'y');
        });

        $(document).delegate('#cross_nett_sales_btn', 'click', function(e) {
            e.preventDefault();
            active_label = 'cross_sales';
            if ($('#info_filter').val() == '') {
                highlight('', 'n');
                swal('Silahkan tentukan info grafik terlebih dahulu');
                return false;
            } else {
                if ($('#info_filter').val() == 'brand') {
                    loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
                } else {
                    loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
                }
            }
            highlight('cross_nett_sales_btn', 'y');
        });

        $(document).delegate('#cross_profits_btn', 'click', function(e) {
            e.preventDefault();
            active_label = 'cross_profits';
            if ($('#info_filter').val() == '') {
                highlight('', 'n');
                swal('Silahkan tentukan info grafik terlebih dahulu');
                return false;
            } else {
                if ($('#info_filter').val() == 'brand') {
                    loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
                } else {
                    loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
                }
            }
            highlight('cross_profits_btn', 'y');
        });

        $(document).delegate('#cc_assets_btn', 'click', function(e) {
            e.preventDefault();
            active_label = 'cc_assets';
            if ($('#info_filter').val() == '') {
                highlight('', 'n');
                swal('Silahkan tentukan info grafik terlebih dahulu');
                return false;
            } else {
                if ($('#info_filter').val() == 'brand') {
                    loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
                } else {
                    loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
                }
            }
            highlight('cc_assets_btn', 'y');
        });

        $(document).delegate('#c_assets_btn', 'click', function(e) {
            e.preventDefault();
            active_label = 'c_assets';
            if ($('#info_filter').val() == '') {
                highlight('', 'n');
                swal('Silahkan tentukan info grafik terlebih dahulu');
                return false;
            } else {
                if ($('#info_filter').val() == 'brand') {
                    loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
                } else {
                    loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
                }
            }
            highlight('c_assets_btn', 'y');
        });

        $(document).delegate('#purchases_btn', 'click', function(e) {
            e.preventDefault();
            active_label = 'purchases';
            if ($('#info_filter').val() == '') {
                highlight('', 'n');
                swal('Silahkan tentukan info grafik terlebih dahulu');
                return false;
            } else {
                if ($('#info_filter').val() == 'brand') {
                    loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
                } else {
                    loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
                }
            }
            highlight('purchases_btn', 'y');
        });

        $(document).delegate('#debts_btn', 'click', function(e) {
            e.preventDefault();
            active_label = 'debts';
            if ($('#info_filter').val() == '') {
                highlight('', 'n');
                swal('Silahkan tentukan info grafik terlebih dahulu');
                return false;
            } else {
                if ($('#info_filter').val() == 'brand') {
                    loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
                } else {
                    loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
                }
            }
            highlight('debts_btn', 'y');
        });

        $(document).delegate('#cc_exc_assets_btn', 'click', function(e) {
            e.preventDefault();
            active_label = 'cc_exc_assets';
            if ($('#info_filter').val() == '') {
                highlight('', 'n');
                swal('Silahkan tentukan info grafik terlebih dahulu');
                return false;
            } else {
                if ($('#info_filter').val() == 'brand') {
                    loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
                } else {
                    loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
                }
            }
            highlight('cc_exc_assets_btn', 'y');
        });

        $(document).delegate('#c_exc_assets_btn', 'click', function(e) {
            e.preventDefault();
            active_label = 'c_exc_assets';
            if ($('#info_filter').val() == '') {
                highlight('', 'n');
                swal('Silahkan tentukan info grafik terlebih dahulu');
                return false;
            } else {
                if ($('#info_filter').val() == 'brand') {
                    loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
                } else {
                    loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
                }
            }
            highlight('c_exc_assets_btn', 'y');
        });

        $(document).delegate('#export', 'click', function(e) {
            e.preventDefault();
            exportTable(active_label, dashboard_date, st_id, $('#division_filter').val());
        });

        $(document).delegate('#detail_activity_btn', 'click', function(e) {
            e.preventDefault();
            highlight('detail_activity_btn', 'y');
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
                dashboard_date = start.format('YYYY-MM-DD');
            } else if (label == 'Kemarin') {
                title = 'Kemarin:';
                range = start.format('DD MMM YYYY');
                dashboard_date = start.format('YYYY-MM-DD');
            } else {
                range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
                dashboard_date = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            getSummary(dashboard_date, st_id, $('#division_filter').val());
            loadAdminCost(active_label, dashboard_date, st_id, $('#division_filter').val());
            if ($('#info_filter').val() == 'brand') {
                loadGraph(active_label, dashboard_date, st_id, $('#division_filter').val());
            } else if ($('#info_filter').val() == 'store') {
                loadStore(active_label, dashboard_date, st_id, $('#division_filter').val());
            }
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
