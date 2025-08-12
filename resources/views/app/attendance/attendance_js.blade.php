<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var attendanceTable = $('#attendanceTable').DataTable({
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
            //         "title": 'Data Absensi'
            //     }
            // ],
            ajax: {
                url : "{{ route('attendance.datatables') }}",
                data : function (d) {
                    var searchValue = $('#attendance_search').val();
                    console.log('Search input value:', searchValue);
                    console.log('Search input element:', $('#attendance_search').length);
                    
                    d.search = searchValue;
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.user_id = $('#user_id').val();
                    d.division_id = $('#division_id').val();
                    d.status = $('#status').val();
                    
                    console.log('AJAX Data sent:', d);
                },
                dataSrc: function(json) {
                    console.log('DataTables response:', json);
                    return json.data || [];
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                { data: 'at_date', name: 'at_date', width: '10%' },
                { 
                    data: 'u_name', 
                    name: 'u_name', 
                    width: '18%',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="/attendance/staff/' + row.user_id + '" class="text-primary font-weight-bold" style="cursor: pointer;">' + data + '</a>';
                        }
                        return data;
                    }
                },
                { data: 'u_nip', name: 'u_nip', width: '12%' },
                { data: 'ud_name', name: 'ud_name', width: '15%' },
                { data: 'sc_code', name: 'sc_code', width: '10%' },
                { data: 'at_time_in', name: 'at_time_in', width: '10%' },
                { data: 'at_time_out', name: 'at_time_out', width: '10%' },
                { data: 'at_status', name: 'at_status', width: '12%' },
                { data: 'at_notes', name: 'at_notes', width: '15%' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '8%' },
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "5%"
                },
                {
                    "targets": [1, 6, 7],
                    "className": "text-center"
                },
                {
                    "targets": [5, 8],
                    "className": "text-center"
                },
                {
                    "targets": 10,
                    "className": "text-center"
                }
            ],
            "createdRow": function(row, data, dataIndex) {
                // Handle status display for leave
                var statusCell = $(row).find('td:eq(8)'); // Status column
                var status = data.at_status;
                
                if (status && status.startsWith('leave_')) {
                    var leaveType = status.replace('leave_', '');
                    var leaveName = '';
                    var leaveColor = '#007bff'; // Default blue color
                    
                    // Map leave types to names and colors
                    switch(leaveType) {
                        case 'ANNUAL':
                            leaveName = 'Cuti Tahunan';
                            leaveColor = '#28a745'; // Green
                            break;
                        case 'SICK':
                            leaveName = 'Cuti Sakit';
                            leaveColor = '#dc3545'; // Red
                            break;
                        case 'MATERNITY':
                            leaveName = 'Cuti Melahirkan';
                            leaveColor = '#e83e8c'; // Pink
                            break;
                        case 'PATERNITY':
                            leaveName = 'Cuti Ayah';
                            leaveColor = '#17a2b8'; // Cyan
                            break;
                        case 'SPECIAL':
                            leaveName = 'Cuti Khusus';
                            leaveColor = '#ffc107'; // Yellow
                            break;
                        default:
                            leaveName = 'Cuti ' + leaveType;
                            leaveColor = '#6c757d'; // Gray
                    }
                    
                    statusCell.html('<span class="badge" style="background-color: ' + leaveColor + '; color: white;">' + leaveName + '</span>');
                }
            },
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
            attendanceTable.draw();
        });
        
        // Auto refresh table when filter values change
        $('#start_date, #end_date, #user_id, #division_id, #status').on('change', function() {
            attendanceTable.draw();
        });
        
        // Handle form submission to update statistics
        $('form[method="GET"]').on('submit', function(e) {
            // Let the form submit normally to refresh the page with new statistics
            // The table will be refreshed automatically
        });
        
        // Search functionality with debounce
        var searchTimeout;
        $('#attendance_search').on('keyup input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                attendanceTable.draw(false);
            }, 300);
        });
        
        // Debug: Check if search input exists
        console.log('Search input element count:', $('#attendance_search').length);
        console.log('Search input value on load:', $('#attendance_search').val());
        
        // Auto reload table on page load to show data with default filters
        setTimeout(function() {
            attendanceTable.draw();
        }, 500);
    });
    
    // Apply filters function
    function applyFilters() {
        $('#attendanceTable').DataTable().ajax.reload();
    }
    
    // Delete attendance function
    function deleteAttendance(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data absensi ini?')) {
            $.ajax({
                url: "{{ route('attendance.destroy', ':id') }}".replace(':id', id),
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#attendanceTable').DataTable().draw();
                        // Show success message
                        $('<div class="alert alert-success alert-dismissible">' +
                          '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                          'Data absensi berhasil dihapus!</div>').insertBefore('#attendanceTable').delay(3000).fadeOut();
                    } else {
                        alert('Gagal menghapus data absensi');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus data');
                }
            });
        }
    }
</script> 