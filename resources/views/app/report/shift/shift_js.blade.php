<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
<!-- DATERANGE -->
<script src="{{ asset('app') }}/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<script>
    function replaceComma(str)
    {
        var str_replace = str.replace(/,/g, '');
        return str_replace;
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var user_shift_table = $('#UserShiftTb').DataTable({
            destroy: true,
            processing: false,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs', "exportOptions": { orthogonal: 'export' } }
            ],
            ajax: {
                url: "{{ url('report_shift_datatables') }}",
                type: 'GET',
                data: function (d) {
                    d.search = $('#user_shift_search').val();
                    d.st_id = $('#st_id_filter').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id', searchable: false},
                {data: 'u_name', name: 'u_name'},
                {data: 'st_name', name: 'st_name'},
                {data: 'date', name: 'date'},
                {data: 'start_time', name: 'start_time'},
                {data: 'end_time', name: 'end_time'},
                {data: 'total_pos_real_price', name: 'total_pos_real_price'},
                {data: 'total_pos_payment_price', name: 'total_pos_payment_price'},
                {data: 'laba_shift', name: 'laba_shift'},
                {data: 'difference', name: 'difference'},
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
            order: [[0, 'asc']]
        });

        user_shift_table.buttons().container().appendTo($('#user_shift_excel_btn' ));

        $('#user_shift_search').on('keyup', function() {
            console.log(user_shift_table.data());
            user_shift_table.draw();
        });

        $('#st_id_filter').select2({
            width: "300px",
            dropdownParent: $('#st_id_filter_parent')
        });
        $('#st_id_filter').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_id_filter').on('change', function() {
            user_shift_table.draw();
        });

        $('#UserShiftTb tbody').on('click', 'tr', function () {
            var data = user_shift_table.row(this).data().id;
            var st_id = user_shift_table.row(this).data().st_id;
            var start_time_original = user_shift_table.row(this).data().start_time_original;
            var end_time_original = user_shift_table.row(this).data().end_time_original;
            var date = user_shift_table.row(this).data().date;
            var start_time = user_shift_table.row(this).data().start_time;
            var end_time = user_shift_table.row(this).data().end_time;
            var total_pos_real_price = user_shift_table.row(this).data().total_pos_real_price;
            var total_pos_payment_price = user_shift_table.row(this).data().total_pos_payment_price;
            var laba_shift = user_shift_table.row(this).data().laba_shift;
            var difference = user_shift_table.row(this).data().difference;
            var st_name = user_shift_table.row(this).data().st_name;
            var u_name = user_shift_table.row(this).data().u_name;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ url('report_shift_detail') }}",
                type: 'post',
                data: {
                    id: data,
                    start_time_original: start_time_original,
                    end_time_original: end_time_original,
                    date: date,
                    start_time: start_time,
                    end_time: end_time,
                    total_pos_real_price: replaceComma(total_pos_real_price),
                    total_pos_payment_price: replaceComma(total_pos_payment_price),
                    laba_shift: replaceComma(laba_shift),
                    difference: replaceComma(difference),
                    st_name: st_name,
                    u_name: u_name,
                    st_id: st_id
                },
                success: function (response) {
                    jQuery.noConflict();
                    $('#UserShiftModal').modal('show');
                    console.log(response);
                    $('#UserShiftModalBody').html(response);

                    $('#userShiftDetailSoldBtn').on('click', function () {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "{{ url('report_shift_product_sold') }}",
                            type: 'post',
                            data: {
                                id: data,
                                start_time_original: start_time_original,
                                end_time_original: end_time_original,
                            },
                            success: function (response_detail) {
                                // console.log(response_detail);
                                jQuery.noConflict();
                                $('#UserShiftDetailSoldModal').modal('show');
                                $('#UserShiftDetailSoldModalBody').html(response_detail);
                            },
                            error: function (response) {
                                console.log(response);
                            }
                        });
                    });

                    $('#userShiftDetailRefundBtn').on('click', function () {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "{{ url('report_shift_product_refund') }}",
                            type: 'post',
                            data: {
                                id: data,
                                start_time_original: start_time_original,
                                end_time_original: end_time_original,
                            },
                            success: function (response_detail) {
                                // console.log(response_detail);
                                jQuery.noConflict();
                                $('#UserShiftDetailRefundModal').modal('show');
                                $('#UserShiftDetailRefundModalBody').html(response_detail);
                            },
                            error: function (response) {
                                console.log(response);
                            }
                        });
                        // $('#UserShiftDetailRefundModal').modal('show');
                        // $('#UserShiftProductRefundModalBody').html(response);
                    });
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });

        $('#close_user_shift_btn').on('click', function(e) {
            e.preventDefault();
            $('#UserShiftModal').modal('hide');
        });

        $('#close_user_shift_detail_sold_btn').on('click', function(e) {
            e.preventDefault();
            $('#UserShiftDetailSoldModal').modal('hide');
        });

        $('#close_user_shift_detail_refund_btn').on('click', function(e) {
            e.preventDefault();
            $('#UserShiftDetailRefundModal').modal('hide');
        });
    });
</script>