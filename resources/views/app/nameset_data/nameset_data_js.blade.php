<script>
    // $('body').addClass('kt-primary--minimize aside-minimize');

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var nameset_table = $('#NamesetDatatb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            deferLoading: 0,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('nameset_datatables') }}",
                data : function (d) {
                    d.search = $('#nameset_search').val();
                    d.status = $('#status_nameset').val();
                    d.trx_date = $('#trx_date').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'ptd_id', searchable: false},
            { data: 'pos_invoice', name: 'pos_invoice' },
            { data: 'stt_name', name: 'stt_name' },
            { data: 'article', name: 'article' },
            { data: 'pos_created', name: 'pos_created' },
            { data: 'pos_note', name: 'pos_note' },
            { data: 'nameset_by', name: 'nameset_by' },
            { data: 'pos_td_nameset_at', name: 'pos_td_nameset_at' },
            { data: 'action', name: 'action' },
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

        $('#nameset_search').on('change', function() {
            nameset_table.draw();
        });

        $('#status_nameset').on('change', function() {
            nameset_table.draw();
        });

        $(document).delegate('#nameset_finish_btn', 'click', function() {
            var ptd_id = $(this).attr('data-ptd_id');
            swal({
                title: "Selesai..?",
                text: "Yakin nameset sudah selesai ?",
                icon: "info",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    jQuery.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        type: "POST",
                        data: {_type:'finish_nameset', _ptd_id:ptd_id},
                        dataType: 'json',
                        url: "{{ url('update_data_nameset') }}",
                        success: function(r) {
                            if (r.status=='200') {
                                nameset_table.draw();
                                toast('Berhasil', 'Berhasil menyelesaikan nameset', 'success');
                            } else {
                                toast('Gagal', 'Gagal menyelesaikan nameset', 'warning');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        nameset_table.buttons().container().appendTo($('#stock_tracking_excel_btn' ));
        $('#nameset_search').on('keyup', function() {
            nameset_table.draw();
        });

        $('#NamesetDatatb tbody').on('click', 'tr', function () {
            var id = nameset_table.row(this).data().plst_id;
            jQuery.noConflict();
            //$('#HistoryModal').modal('show');
        });

        $('#export_nameset_btn').on('click', function() {
            var search = $('#nameset_search').val();
            var status = $('#status_nameset').val();
            var trx_date = $('#trx_date').val();
            var url_export = "{{ route('export.nameset') }}?search="+search+"&status="+status+"&trx_date="+trx_date;
            window.location.href = url_export;
        });

        $(document).delegate('#pos_invoice', 'click', function() {
            var invoice = $(this).text();
            var temp = $('<input>');
            $('body').append(temp);
            temp.val(invoice).select();
            document.execCommand('copy');
            temp.remove();
            toast('Berhasil', 'Invoice berhasil disalin', 'success');
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
                range = start.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD');
            } else if (label == 'Yesterday') {
                title = 'Yesterday:';
                range = start.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD');
            } else if (label == 'All Days') {
                title = 'All Days';
                hidden_range = '';
            } else {
                range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }
            console.log(hidden_range);
            $('#trx_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);

            nameset_table.draw();
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