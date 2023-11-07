<script>
    $('body').addClass('kt-primary--minimize aside-minimize');
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
        var ai_table = $('#AItb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'B<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('ai_datatables') }}",
                data : function (d) {
                    d.search = $('#ai_search').val();
                    d.pc_id = $('#pc_id').val();
                    d.st_id = $('#st_id').val();
                    d.report_date = $('#report_date').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'created_at', name: 'created_at' },
            { data: 'st_name', name: 'st_name' },
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'p_color', name: 'p_color' },
            { data: 'sz_name', name: 'sz_name' },
            { data: 'past_stock', name: 'past_stock', orderable: false },
            { data: 'buy', name: 'buy' },
            { data: 'transfer', name: 'transfer' },
            { data: 'transfer_in', name: 'transfer_in' },
            { data: 'adjustment', name: 'adjustment' },
            { data: 'sales', name: 'sales' },
            { data: 'refund', name: 'refund' },
            { data: 'stock', name: 'stock' },
            { data: 'hpp', name: 'hpp' },
            { data: 'hj', name: 'hj' },
            { data: 'profit', name: 'profit', orderable: false },
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

        var history_table = $('#Historytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'B<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('ai_history_datatables') }}",
                data : function (d) {
                    d.search = $('#history_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'u_name', name: 'u_name' },
            { data: 'activity', name: 'activity' },
            { data: 'created_show', name: 'created_at' },
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

        $('#st_id, #pc_id').on('change', function() {
            ai_table.draw();
        });

        $('#ai_search').on('keyup', function() {
            ai_table.draw();
        });

        $('#history_search').on('keyup', function() {
            history_table.draw();
        });

        $(document).delegate('#update_btn', 'click', function(e) {
            e.preventDefault();
            var st_id = $('#st_id').val();
            if (st_id == '') {
                swal('Store', 'Pilih store dahulu', 'warning');
                return false;
            }
            swal({
                title: "Update ..?",
                text: "Update sesuai kategori dan store per tanggal hari ini?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Update'
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
                        data: {st_id:st_id},
                        dataType: 'json',
                        url: "{{ url('ai_update')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                toast("Berhasil", "Item berhasil diupdate", "success");
                                ai_table.draw();
                                history_table.draw();
                            } else {
                                toast('Gagal', 'Gagal update', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#daily_update_btn', 'click', function() {
            swal({
                title: "Update Seluruh Store dan Kategori ..?",
                text: "Update seluruh Store dan Kategori per hari ini ?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Update Semua'
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
                        dataType: 'json',
                        url: "{{ url('ai_daily_update')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                toast("Berhasil", "Item berhasil diupdate", "success");
                                ai_table.draw();
                                history_table.draw();
                            } else {
                                toast('Gagal', 'Gagal update', 'error');
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
            $('#report_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            ai_table.draw();
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