<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var userDivisionTable = $('#userDivisionTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: 'rt<"pagination-class"ip>',
            ajax: {
                url : "{{ url('user-divisions/datatables') }}",
                data : function (d) {
                    d.search = $('#user_division_search').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                { data: 'ud_code', name: 'ud_code', width: '15%' },
                { data: 'ud_name', name: 'ud_name', width: '25%' },
                { data: 'ud_description', name: 'ud_description', width: '30%' },
                { data: 'ud_status', name: 'ud_status', width: '10%' },
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
        
        // Search functionality with debounce
        var searchTimeout;
        $('#user_division_search').on('keyup input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                userDivisionTable.draw(false);
            }, 300);
        });
    });

    // Delete user division function
    function deleteUserDivision(id) {
        if (confirm('Are you sure you want to delete this user division?')) {
            $.ajax({
                url: "{{ url('user-divisions') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#userDivisionTable').DataTable().ajax.reload();
                        alert('User division deleted successfully');
                    } else {
                        alert('Failed to delete user division');
                    }
                },
                error: function() {
                    alert('Error occurred while deleting user division');
                }
            });
        }
    }
</script> 