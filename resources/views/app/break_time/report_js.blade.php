<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        console.log('Initializing DataTable for #breakTimeTable');
        console.log('Table element exists:', $('#breakTimeTable').length > 0);
        
        try {
            // First, try simple DataTable without serverSide to test
            console.log('Testing simple DataTable first...');
            
            var testTable = $('#breakTimeTable').DataTable({
                destroy: true,
                data: [],
                columns: [
                    { title: "No" },
                    { title: "Tanggal" },
                    { title: "Nama" },
                    { title: "NIP" },
                    { title: "Divisi" },
                    { title: "Tipe" },
                    { title: "Mulai" },
                    { title: "Selesai" },
                    { title: "Durasi" },
                    { title: "Status" },
                    { title: "Aksi" }
                ]
            });
            
            console.log('Simple DataTable created:', testTable);
            testTable.destroy();
            
            // Now try the real serverSide DataTable (simplified like staff)
            var breakTimeTable = $('#breakTimeTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: 'rt<"pagination-class"ip>',
            ajax: {
                url : "{{ url('break-times/datatables') }}",
                data : function (d) {
                    d.search = $('#break_time_search').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.user_id = $('#user_id').val();
                    d.division_id = $('#division_id').val();
                    d.status = $('#status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                { data: 'bt_date', name: 'bt_date', width: '10%' },
                { data: 'u_name', name: 'u_name', width: '18%' },
                { data: 'u_nip', name: 'u_nip', width: '12%' },
                { data: 'ud_name', name: 'ud_name', width: '15%' },
                { data: 'bt_type', name: 'bt_type', width: '10%' },
                { data: 'bt_start_time', name: 'bt_start_time', width: '10%' },
                { data: 'bt_end_time', name: 'bt_end_time', width: '10%' },
                { data: 'bt_duration_minutes', name: 'bt_duration_minutes', width: '10%' },
                { data: 'bt_status', name: 'bt_status', width: '12%' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '8%' },
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "5%"
                },
                {
                    "targets": [1, 6, 7, 8],
                    "className": "text-center"
                },
                {
                    "targets": [5, 9],
                    "className": "text-center"
                },
                {
                    "targets": 10,
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
            }
        });
        
        console.log('DataTable object created:', breakTimeTable);
        
        } catch (error) {
            console.error('Error initializing DataTable:', error);
            console.error('Error stack:', error.stack);
        }
        
        // Refresh table when filter form is submitted
        $('form').on('submit', function() {
            breakTimeTable.draw();
        });
        
        // Auto refresh table when filter values change
        $('#start_date, #end_date, #user_id, #division_id, #status').on('change', function() {
            breakTimeTable.draw();
        });
        
        // Search functionality with debounce
        var searchTimeout;
        $('#break_time_search').on('keyup input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                breakTimeTable.draw(false);
            }, 300);
        });
    });
    
    // Apply filters function
    function applyFilters() {
        $('#breakTimeTable').DataTable().ajax.reload();
    }
    
    // Delete break time function
    function deleteBreakTime(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data break time ini?')) {
            $.ajax({
                url: "{{ route('break-times.destroy', ':id') }}".replace(':id', id),
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#breakTimeTable').DataTable().draw();
                        // Show success message
                        $('<div class="alert alert-success alert-dismissible">' +
                          '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                          'Data break time berhasil dihapus!</div>').insertBefore('#breakTimeTable').delay(3000).fadeOut();
                    } else {
                        alert('Gagal menghapus data break time');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus data');
                }
            });
        }
    }
</script>
