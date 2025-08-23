<script>
    // Global reference for DataTable
    window.staffAttendanceTable = null;

    // Robust DataTable initialization with retry mechanism
    function initStaffAttendanceTable() {
        if (typeof $.fn.DataTable === 'undefined') {
            console.log('DataTable not available, retrying in 100ms...');
            setTimeout(initStaffAttendanceTable, 100);
            return;
        }

        try {
            console.log('Initializing staff attendance DataTable...');
            
            // Get user_id from URL
            var urlParts = window.location.pathname.split('/');
            var userId = urlParts[urlParts.length - 1];
            
            window.staffAttendanceTable = $('#staffAttendanceTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false, // Set to false to prevent conflicts
                dom: 'rt<"pagination-class"ip>',
                ajax: {
                    url: "/attendance/staff/" + userId + "/datatables",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status').val();
                        
                        console.log('Staff Attendance AJAX Data sent:', d);
                    },
                    dataSrc: function(json) {
                        console.log('Staff Attendance DataTables response:', json);
                        return json.data || [];
                    },
                    error: function(xhr, error, thrown) {
                        console.error('Staff Attendance AJAX Error:', error, thrown);
                        console.log('Response:', xhr.responseText);
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
                },
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
                                statusClass = 'badge-info';
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
            console.log('Staff Attendance DataTable initialized successfully.');
            
            // Initialize dropdown menu system
            initializeSimpleDropdown();
            
            // Auto reload table on page load to show data
            setTimeout(function() {
                window.staffAttendanceTable.draw();
            }, 500);
            
        } catch (error) {
            console.error('Error initializing staff attendance DataTable:', error);
            setTimeout(initStaffAttendanceTable, 100);
        }
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Initialize DataTable on page load
        initStaffAttendanceTable();
        
        // Handle filter form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            // Reload data only if the table is initialized
            if (window.staffAttendanceTable) {
                window.staffAttendanceTable.ajax.reload();
                updateStatistics();
            } else {
                console.warn('Staff attendance table not initialized, cannot filter.');
            }
        });
        
        // Update statistics when page loads
        updateStatistics();
        
        // Function to update statistics
        function updateStatistics() {
            // Get user_id from URL
            var urlParts = window.location.pathname.split('/');
            var userId = urlParts[urlParts.length - 1];
            
            $.ajax({
                url: "/attendance/staff/" + userId + "/stats",
                type: 'GET',
                data: {
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    status: $('#status').val()
                },
                success: function(response) {
                    console.log('Statistics response:', response);
                    if (response.stats) {
                        $('#total_shifts').text(response.stats.total_shifts || 0);
                        $('#present_days').text(response.stats.present_days || 0);
                        $('#sick_days').text(response.stats.sick_days || 0);
                        $('#leave_days').text(response.stats.leave_days || 0);
                        $('#late_days').text(response.stats.late_days || 0);
                        $('#alpha_days').text(response.stats.alpha_days || 0);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading statistics:', error);
                    console.log('Response:', xhr.responseText);
                }
            });
        }
    });
    
    // Initialize simple dropdown menu system
    function initializeSimpleDropdown() {
        console.log('Initializing simple dropdown for staff attendance...');
        
        // Check if Metronic components are available
        if (typeof KT !== 'undefined' && typeof KT.Menu !== 'undefined') {
            console.log('Metronic KT.Menu found, initializing...');
            try {
                KT.Menu.init();
                console.log('Metronic menu initialized successfully');
            } catch (e) {
                console.log('Error initializing Metronic menu:', e);
                initializeFallbackMenu();
            }
        } else {
            console.log('Metronic components not found, using fallback menu');
            initializeFallbackMenu();
        }
    }
    
    // Fallback menu system for when Metronic is not available
    function initializeFallbackMenu() {
        console.log('Initializing fallback dropdown menu...');
        
        // Remove existing event listeners to prevent duplicates
        $(document).off('click', '[data-kt-menu-trigger="click"]');
        $(document).off('click', '.menu-link');
        $(document).off('click', 'body');
        
        // Toggle dropdown on button click
        $(document).on('click', '[data-kt-menu-trigger="click"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $dropdown = $(this).closest('.dropdown');
            var $menu = $dropdown.find('.menu');
            
            // Close other open dropdowns
            $('.menu').not($menu).removeClass('show');
            
            // Toggle current dropdown
            $menu.toggleClass('show');
            
            console.log('Dropdown toggled:', $menu.hasClass('show'));
        });
        
        // Close dropdown when clicking on menu items
        $(document).on('click', '.menu-link', function(e) {
            var $dropdown = $(this).closest('.dropdown');
            $dropdown.find('.menu').removeClass('show');
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', 'body', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.menu').removeClass('show');
            }
        });
        
        console.log('Fallback dropdown menu initialized');
    }

    // Date filter functionality for staff detail
    function handleDateFilterChange(value) {
        const startDateContainer = document.getElementById('start_date_container');
        const endDateContainer = document.getElementById('end_date_container');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        if (value === 'custom') {
            startDateContainer.style.display = 'block';
            endDateContainer.style.display = 'block';
        } else {
            startDateContainer.style.display = 'none';
            endDateContainer.style.display = 'none';
            
            // Set default dates based on filter
            const today = new Date();
            let startDate, endDate;
            
            switch (value) {
                case 'this_week':
                    startDate = new Date(today.getTime());
                    startDate.setDate(today.getDate() - today.getDay() + 1); // Monday
                    endDate = new Date(today.getTime());
                    endDate.setDate(today.getDate() - today.getDay() + 7); // Sunday
                    break;
                case 'past_week':
                    startDate = new Date(today.getTime());
                    startDate.setDate(today.getDate() - today.getDay() - 6); // Last Monday
                    endDate = new Date(today.getTime());
                    endDate.setDate(today.getDate() - today.getDay()); // Last Sunday
                    break;
                case 'this_month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1); // First day of month
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of month
                    break;
                case 'last_month':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1); // First day of last month
                    endDate = new Date(today.getFullYear(), today.getMonth(), 0); // Last day of last month
                    break;
            }
            
            // Format dates for hidden inputs
            if (startDate && endDate) {
                const formatDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };
                
                startDateInput.value = formatDate(startDate);
                endDateInput.value = formatDate(endDate);
            }
        }
    }

    // Initialize date filter on page load for staff detail
    document.addEventListener('DOMContentLoaded', function() {
        const dateFilter = document.getElementById('date_filter');
        if (dateFilter) {
            handleDateFilterChange(dateFilter.value);
        }
    });

    // Export functions for staff attendance
    function exportStaffToExcel() {
        const url = new URL('{{ route("attendance.staff-export-excel", ":user_id") }}'.replace(':user_id', '{{ $staff->id }}'));
        
        // Add current filters to URL
        const dateFilter = document.getElementById('date_filter').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const status = document.getElementById('status').value;
        
        if (dateFilter && dateFilter !== 'custom') {
            url.searchParams.append('date_filter', dateFilter);
        }
        if (startDate) {
            url.searchParams.append('start_date', startDate);
        }
        if (endDate) {
            url.searchParams.append('end_date', endDate);
        }
        if (status) {
            url.searchParams.append('status', status);
        }
        
        // Create download link
        const link = document.createElement('a');
        link.href = url.toString();
        link.download = 'staff_attendance_{{ $staff->u_nip }}.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportStaffToPDF() {
        const url = new URL('{{ route("attendance.staff-export-pdf", ":user_id") }}'.replace(':user_id', '{{ $staff->id }}'));
        
        // Add current filters to URL
        const dateFilter = document.getElementById('date_filter').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const status = document.getElementById('status').value;
        
        if (dateFilter && dateFilter !== 'custom') {
            url.searchParams.append('date_filter', dateFilter);
        }
        if (startDate) {
            url.searchParams.append('start_date', startDate);
        }
        if (endDate) {
            url.searchParams.append('end_date', endDate);
        }
        if (status) {
            url.searchParams.append('status', status);
        }
        
        // Create download link
        const link = document.createElement('a');
        link.href = url.toString();
        link.download = 'staff_attendance_{{ $staff->u_nip }}.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
