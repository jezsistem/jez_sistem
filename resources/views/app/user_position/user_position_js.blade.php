<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var userPositionTable = $('#userPositionTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: 'rt<"pagination-class"ip>',
            ajax: {
                url : "{{ url('user-positions/datatables') }}",
                data : function (d) {
                    d.search = $('#user_position_search').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                { data: 'up_code', name: 'up_code', width: '15%' },
                { data: 'up_name', name: 'up_name', width: '20%' },
                { data: 'up_description', name: 'up_description', width: '25%' },
                { data: 'up_level', name: 'up_level', width: '10%' },
                { data: 'up_is_active', name: 'up_is_active', width: '10%' },
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
            }
        });
        
        // Search functionality with debounce
        var searchTimeout;
        $('#user_position_search').on('keyup input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                userPositionTable.draw(false);
            }, 300);
        });
    });

    // Delete user position function
    function deleteUserPosition(id) {
        if (confirm('Are you sure you want to delete this user position?')) {
            $.ajax({
                url: "{{ url('user-positions') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#userPositionTable').DataTable().ajax.reload();
                        alert('User position deleted successfully');
                    } else {
                        alert('Failed to delete user position');
                    }
                },
                error: function() {
                    alert('Error occurred while deleting user position');
                }
            });
        }
    }
</script> 