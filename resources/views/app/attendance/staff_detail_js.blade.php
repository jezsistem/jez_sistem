<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Get user_id from URL
        var urlParts = window.location.pathname.split('/');
        var userId = urlParts[urlParts.length - 1];
        
        var staffAttendanceTable = $('#staffAttendanceTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: 'rt<"pagination-class"ip>',
            ajax: {
                url: "/attendance/staff/" + userId + "/datatables",
                data: function(d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.status = $('#status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                { data: 'at_date', name: 'at_date', width: '12%' },
                { data: 'sc_code', name: 'sc_code', width: '10%' },
                { data: 'at_time_in', name: 'at_time_in', width: '12%' },
                { data: 'at_time_out', name: 'at_time_out', width: '12%' },
                { data: 'at_status', name: 'at_status', width: '15%' },
                { data: 'at_notes', name: 'at_notes', width: '20%' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '14%' },
            ],
            columnDefs: [
                {
                    "targets": [0, 2, 3, 4, 5],
                    "className": "text-center"
                },
                {
                    "targets": 7,
                    "className": "text-center"
                }
            ],
            "createdRow": function(row, data, dataIndex) {
                // Handle status display for leave
                var statusCell = $(row).find('td:eq(5)'); // Status column
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
                            leaveName = 'Cuti Melahirkan';
                            leaveColor = '#e83e8c'; // Pink
                            break;
                        default:
                            leaveName = 'Cuti ' + leaveType;
                            leaveColor = '#007bff'; // Blue
                    }
                    
                    statusCell.html('<span class="badge" style="background-color: ' + leaveColor + '; color: white;">' + leaveName + '</span>');
                } else {
                    // Handle regular status
                    var statusText = '';
                    var statusClass = '';
                    
                    switch(status) {
                        case 'present':
                            statusText = 'Present';
                            statusClass = 'badge-success';
                            break;
                        case 'absent':
                            statusText = 'Absent';
                            statusClass = 'badge-danger';
                            break;
                        case 'late':
                            statusText = 'Late';
                            statusClass = 'badge-warning';
                            break;
                        case 'early_leave':
                            statusText = 'Early Leave';
                            statusClass = 'badge-info';
                            break;
                        case 'scan_once':
                            statusText = 'Scan Once';
                            statusClass = 'badge-secondary';
                            break;
                        default:
                            statusText = status;
                            statusClass = 'badge-dark';
                    }
                    
                    statusCell.html('<span class="badge ' + statusClass + '">' + statusText + '</span>');
                }
            }
        });
        
        // Handle filter form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            staffAttendanceTable.ajax.reload();
            updateStatistics();
        });
        
        // Update statistics when page loads
        updateStatistics();
        
        // Function to update statistics
        function updateStatistics() {
            $.ajax({
                url: "/attendance/staff/" + userId + "/stats",
                type: 'GET',
                data: {
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    status: $('#status').val()
                },
                success: function(response) {
                    if (response.stats) {
                        $('#present_count').text(response.stats.present || 0);
                        $('#late_count').text(response.stats.late || 0);
                        $('#absent_count').text(response.stats.absent || 0);
                        $('#early_leave_count').text(response.stats.early_leave || 0);
                        $('#scan_once_count').text(response.stats.scan_once || 0);
                        $('#total_days').text(response.stats.total || 0);
                        
                        // Update leave statistics
                        $('#leave_annual_count').text(response.stats.leave_annual || 0);
                        $('#leave_sick_count').text(response.stats.leave_sick || 0);
                        $('#leave_maternity_count').text(response.stats.leave_maternity || 0);
                        $('#leave_emergency_count').text(response.stats.leave_emergency || 0);
                        $('#leave_half_day_count').text(response.stats.leave_half_day || 0);
                        $('#leave_special_count').text(response.stats.leave_special || 0);
                    }
                },
                error: function() {
                    console.log('Error loading statistics');
                }
            });
        }
    });
</script>
