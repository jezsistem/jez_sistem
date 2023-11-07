<script src="{{ asset('app') }}/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
<script src="{{ asset('cdn/chart.js') }}"></script>
<script>
    $('body').addClass('kt-primary--minimize aside-minimize');
    var dashboard_date = '';
    var st_id = '';
    var br_id = '';
    var exception = 'noexcept';

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var article_table = $('#article_table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rtl<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('stc_article_datatables') }}",
                data : function (d) {
                    d.search = $('#article_search').val();
                    d.st_id = st_id;
                    d.br_id = br_id;
                    d.exception = exception;
                    d.date = dashboard_date;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'brand', name: 'brand' },
            { data: 'article', name: 'article' },
            { data: 'color', name: 'color' },
            { data: 'size', name: 'size' },
            { data: 'beginning_stock', name: 'beginning_stock', orderable: false },
            { data: 'purchase', name: 'purchase', orderable: false },
            { data: 'trans_in', name: 'trans_in', orderable: false },
            { data: 'trans_out', name: 'trans_out', orderable: false },
            { data: 'sales', name: 'sales', orderable: false },
            { data: 'refund', name: 'refund', orderable: false },
            { data: 'adj_min', name: 'adj_min', orderable: false },
            { data: 'adj_plus', name: 'adj_plus', orderable: false },
            { data: 'madj_min', name: 'madj_min', orderable: false },
            { data: 'madj_plus', name: 'madj_plus', orderable: false },
            { data: 'sadj_min', name: 'sadj_min', orderable: false },
            { data: 'sadj_plus', name: 'sadj_plus', orderable: false },
            { data: 'waiting', name: 'waiting', orderable: false },
            { data: 'cross_setup_in', name: 'cross_setup_in', orderable: false },
            { data: 'cross_setup_out', name: 'cross_setup_out', orderable: false },
            { data: 'ending_stock', name: 'stock'},
            { data: 'today_exception', name: 'today_exception', orderable: false},
            { data: 'today_stock', name: 'today_stock'},
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

        $('#article_search').on('keyup', function() {
            article_table.draw();
        });

        $(document).delegate('#store_filter', 'change', function(e) {
            e.preventDefault();
            st_id = $(this).val();
        });

        $(document).delegate('#brand_filter', 'change', function(e) {
            e.preventDefault();
            br_id = $(this).val();
        });

        $(document).delegate('#exception_filter', 'change', function(e) {
            e.preventDefault();
            exception = $(this).val();
        });

        function getBeginning() {
            
        }

        $(document).delegate('#exec_btn', 'click', function(e) {
            e.preventDefault();
            if (st_id == '') {
                swal('Tentukan Store', 'Silahkan tentukan store terlebih dahulu', 'warning');
                return false;
            }
            $('#article_panel').removeClass('d-none');
            article_table.draw();
        });

        function getExport() {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ url('stock_report_export') }}",
                xhrFields: {
                    responseType: 'blob' 
                },
                success: function(blob, status, xhr) {
                    $('#export_btn').text('Download Excel');
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
                            // use HTML5 a[download] attribute to specify filename
                            var a = document.createElement("a");
                            // safari doesn't support this yet
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

                        setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 10000); // cleanup
                    }
                    $('#export_btn').text('Download Excel');
                }
            });
            return false;
        }

        function phase2() {
            $('#export_btn').text('Mohon tunggu, memasuki kalkulasi fase 2 ...');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {st_id:st_id, br_id:br_id, date:dashboard_date, exception:exception},
                dataType: "json",
                url: "{{ url('stock_report_phase2') }}",
                success: function(r) {
                    if (r.status == '200') {
                        phase3();
                    }
                }
            });
            return false;
        }

        function phase3() {
            $('#export_btn').text('Mohon tunggu, memasuki kalkulasi fase terakhir, menyiapkan file anda ...');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {st_id:st_id, br_id:br_id, date:dashboard_date, exception:exception},
                dataType: "json",
                url: "{{ url('stock_report_phase3') }}",
                success: function(r) {
                    if (r.status == '200') {
                        getExport();
                    }
                }
            });
            return false;
        }

        $(document).delegate('#export_btn', 'click', function(e){
            e.preventDefault();
            swal({
                title: "Download Excel..?",
                text: "Download data dengan kriteria customize diatas?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Download'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $('#export_btn').text('Mohon tunggu, kalkulasi fase 1 ...');
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {st_id:st_id, br_id:br_id, date:dashboard_date, exception:exception},
                        dataType: "json",
                        url: "{{ url('stock_report_fill_data') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                phase2();
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
            article_table.draw();
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
