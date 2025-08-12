<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var leaveTypeTable = $('#leaveTypeTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: 'rt<"pagination-class"ip>',
            // buttons: [
            //     { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            // ],
            ajax: {
                url : "{{ url('leave-types/datatables') }}",
                data : function (d) {
                    d.search = $('#leave_type_search').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                { data: 'lt_code', name: 'lt_code', width: '15%' },
                { data: 'lt_name', name: 'lt_name', width: '20%' },
                { data: 'lt_description', name: 'lt_description', width: '25%' },
                { data: 'lt_duration', name: 'lt_duration', width: '10%' },
                { data: 'lt_status', name: 'lt_status', width: '10%' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '15%' },
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "5%"
                },
                {
                    "targets": 4,
                    "className": "text-center"
                },
                {
                    "targets": 5,
                    "className": "text-center"
                },
                {
                    "targets": 6,
                    "className": "text-center"
                }
            ],
            order: [[0, 'desc']],
            pageLength: 10,
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
        
        // leaveTypeTable.buttons().container().appendTo($('#leave_type_excel_btn'));
        // Search functionality with debounce
        var searchTimeout;
        $('#leave_type_search').on('keyup input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                leaveTypeTable.draw(false);
            }, 300);
        });
    });

    // Delete leave type function
    function deleteLeaveType(id) {
        if (confirm('Are you sure you want to delete this leave type?')) {
            $.ajax({
                url: "{{ url('leave-types') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#leaveTypeTable').DataTable().ajax.reload();
                        alert('Leave type deleted successfully');
                    } else {
                        alert('Failed to delete leave type');
                    }
                },
                error: function() {
                    alert('Error occurred while deleting leave type');
                }
            });
        }
    }
</script> 