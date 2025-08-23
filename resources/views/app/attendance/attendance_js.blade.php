<script>
    // Global reference for DataTable
    window.attendanceTable = null;

    // Robust DataTable initialization with retry mechanism
    function initDataTable() {
        if (typeof $.fn.DataTable === 'undefined') {
            console.log('DataTable not available, retrying in 100ms...');
            setTimeout(initDataTable, 100);
            return;
        }

        try {
            console.log('Initializing attendance DataTable...');
            
            window.attendanceTable = $('#attendanceTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
                responsive: false, // Set to false to prevent conflicts
            dom: 'rt<"pagination-class"ip>',
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
                    
                        console.log('Status filter value:', $('#status').val());
                        console.log('Status filter element:', $('#status').length);
                    console.log('AJAX Data sent:', d);
                },
                dataSrc: function(json) {
                    console.log('DataTables response:', json);
                    return json.data || [];
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '3%' },
                { data: 'at_date', name: 'at_date', width: '7%' },
                { 
                    data: 'u_name', 
                    name: 'u_name', 
                    width: '14%',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="/attendance/staff/' + row.user_id + '" class="text-primary font-weight-bold" style="cursor: pointer;line-height: 1.2;">' + data + '</a><br><span class="text-muted" style="line-height: 1.8;">' + (row.u_nip || '-') + '</span>';
                        }
                        return data;
                    }
                },
                { data: 'ud_name', name: 'ud_name', width: '10%' },
                { data: 'sc_code', name: 'sc_code', width: '10%' },
                { data: 'at_time_in', name: 'at_time_in', width: '10%' },
                { data: 'at_time_out', name: 'at_time_out', width: '10%' },
                { data: 'at_status', name: 'at_status', width: '10%' },
                { data: 'at_notes', name: 'at_notes', width: '15%' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '17%' },
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
                    "targets": 9,
                    "className": "text-center"
                }
            ],
            "createdRow": function(row, data, dataIndex) {
                // Handle status display for leave
                var statusCell = $(row).find('td:eq(7)'); // Status column (index 7 karena kolom NIP dihilangkan)
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
                    "sUrl":          ""
                }
            });
            
            console.log('Attendance DataTable initialized successfully');
            
            // Initialize dropdown menu system
            initializeSimpleDropdown();
        
        // Search functionality with debounce
        var searchTimeout;
        $('#attendance_search').on('keyup input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                    window.attendanceTable.draw(false);
            }, 300);
        });
        
        // Debug: Check if search input exists
        console.log('Search input element count:', $('#attendance_search').length);
        console.log('Search input value on load:', $('#attendance_search').val());
        
        // Auto reload table on page load to show data with default filters
        // Delay longer to ensure session messages are visible
        setTimeout(function() {
                window.attendanceTable.draw();
        }, 2000);
            
        } catch (error) {
            console.error('Error initializing attendance DataTable:', error);
            setTimeout(initDataTable, 100);
        }
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Initialize DataTable
        initDataTable();
    });

    // Simple working dropdown solution
    function initializeSimpleDropdown() {
        console.log('Initializing simple dropdown system for Attendance');
        
        // Remove any existing event handlers
        $(document).off('click', '[data-kt-menu-trigger="click"]');
        
        // Add click handler for dropdown toggle
        $(document).on('click', '[data-kt-menu-trigger="click"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $this = $(this);
            var $menu = $this.siblings('.menu');
            
            console.log('Dropdown clicked, menu found:', $menu.length);
            
            // Close all other menus first
            $('.menu').not($menu).removeClass('show');
            
            // Toggle current menu
            $menu.toggleClass('show');
            
            console.log('Menu toggled, has show class:', $menu.hasClass('show'));
        });
        
        // Close menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.menu').removeClass('show');
            }
        });
        
        // Close menu when clicking on menu items
        $(document).on('click', '.menu-link', function(e) {
            if ($(this).attr('onclick')) {
                // For buttons with onclick, let the onclick handle it
                return;
            }
            // For other links, close menu after a short delay
            setTimeout(function() {
                $('.menu').removeClass('show');
            }, 100);
        });
        
        console.log('Simple dropdown system initialized for Attendance');
    }

    // Re-initialize dropdown on each draw
    if (window.attendanceTable) {
        window.attendanceTable.on('draw.dt', function() {
            setTimeout(initializeSimpleDropdown, 100);
        });
    }
    
    // Apply filters function
    function applyFilters() {
        if (window.attendanceTable) {
            window.attendanceTable.ajax.reload();
        } else {
        $('#attendanceTable').DataTable().ajax.reload();
        }
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
                        if (window.attendanceTable) {
                            window.attendanceTable.draw();
                        } else {
                        $('#attendanceTable').DataTable().draw();
                        }
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