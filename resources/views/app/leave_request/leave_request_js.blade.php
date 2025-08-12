<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var leaveRequestTable = $('#leaveRequestTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: 'rt<"pagination-class"ip>',
            // buttons: [
            //     { 
            //         "extend": 'excelHtml5', 
            //         "text": '<i class="fas fa-file-excel"></i> Excel',
            //         "className": 'btn btn-success btn-sm',
            //         "title": 'Data Leave Requests'
            //     }
            // ],
            ajax: {
                url : "{{ route('leave-requests.datatables') }}",
                data : function (d) {
                    d.search = $('#leave_request_search').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.user_id = $('#user_id').val();
                    d.leave_type_id = $('#leave_type_id').val();
                    d.status = $('#status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                { data: 'lr_date', name: 'lr_date', width: '10%' },
                { data: 'u_name', name: 'u_name', width: '15%' },
                { data: 'ud_name', name: 'ud_name', width: '12%' },
                { data: 'lt_name', name: 'lt_name', width: '12%' },
                { data: 'lr_start_date', name: 'lr_start_date', width: '10%' },
                { data: 'lr_end_date', name: 'lr_end_date', width: '10%' },
                { data: 'lr_status', name: 'lr_status', width: '10%' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '16%' },
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "5%"
                },
                {
                    "targets": [1, 5, 6],
                    "className": "text-center"
                },
                {
                    "targets": 7,
                    "className": "text-center"
                },
                {
                    "targets": 8,
                    "className": "text-center"
                }
            ],
            order: [[1, 'desc']],
            pageLength: 25,
            language: {
                "sProcessing":   "Loading...",
                "sLengthMenu":   "Tampilkan _MENU_ entri",
                "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "sInfoPostFix":  "",
                "sSearch":       "Cari:",
                "sUrl":          "",
                // "oPaginate": {
                //     "sFirst":    "Pertama",
                //     "sPrevious": "Sebelumnya",
                //     "sNext":     "Selanjutnya",
                //     "sLast":     "Terakhir"
                // }
            }
        });
        
        // Refresh table when filter form is submitted
        $('form').on('submit', function() {
            leaveRequestTable.draw();
        });
        
        // Auto refresh table when filter values change
        $('#start_date, #end_date, #user_id, #leave_type_id, #status').on('change', function() {
            leaveRequestTable.draw();
        });
        
        // Search functionality with debounce
        var searchTimeout;
        $('#leave_request_search').on('keyup input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                leaveRequestTable.draw();
            }, 300);
        });
    });
</script> 