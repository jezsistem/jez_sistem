<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var rating_table = $('#Ratingtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Blrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('rating_datatables') }}",
                data : function (d) {
                    d.search = $('#user_search').val();
                    d.rating_date = $('#user_rating_date').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'ur_id', searchable: false},
            { data: 'u_name', name: 'u_name' },
            { data: 'st_name', name: 'st_name' },
            { data: 'stt_name', name: 'stt_name' },
            { data: 'rating_qty', name: 'rating_qty', orderable:false},
            { data: 'rating_total', name: 'rating_total', orderable:false},
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

        var rating_history_table = $('#RatingHistorytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Blrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('rating_history_datatables') }}",
                data : function (d) {
                    d.search = $('#user_history_search').val();
                    d.rating_date = $('#user_rating_date').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'ur_id', searchable: false},
            { data: 'u_name', name: 'u_name' },
            { data: 'st_name', name: 'st_name' },
            { data: 'stt_name', name: 'stt_name' },
            { data: 'pos_invoice', name: 'pos_invoice' },
            { data: 'cust_name', name: 'cust_name' },
            { data: 'ur_value', name: 'ur_value' },
            { data: 'ur_description', name: 'ur_description' },
            { data: 'ur_created', name: 'ur_created' },
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
				url : "{{ url('sales_item_detail_datatables') }}",
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

        $('#user_search').on('keyup', function() {
            rating_table.draw();
        });

        $('#user_history_search').on('keyup', function() {
            rating_history_table.draw();
        });

        $(document).delegate('#sales_item_detail_btn', 'click', function() {
			var pt_id = $(this).attr('data-pt_id');
			var invoice = $(this).text();
            jQuery.noConflict();
			$('#pt_id').val(pt_id);
			$('#sales_item_detail_label').text(invoice);
			$('#SalesItemDetailModal').on('show.bs.modal', function() {
				sales_item_detail_table.draw();
			}).modal('show');
		});

        jQuery.noConflict();
        $('#UrbanPanelModal').on('hide.bs.modal', function(){
            
        }).modal('hide');

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
            //alert(range+' | '+hidden_range);
            $('#user_rating_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            //alert(hidden_range);
            rating_table.draw();
            rating_history_table.draw();
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