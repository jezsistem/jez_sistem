<script>
    // Global reference for DataTable
    window.staffTable = null;

    // Date filter functionality
    function handleDateFilterChange(value) {
        console.log('handleDateFilterChange called with value:', value);
        
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
                case 'next_week':
                    startDate = new Date(today.getTime());
                    startDate.setDate(today.getDate() - today.getDay() + 8); // Next Monday
                    endDate = new Date(today.getTime());
                    endDate.setDate(today.getDate() - today.getDay() + 14); // Next Sunday
                    break;
                case 'this_month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1); // First day of month
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of month
                    break;
                case 'last_month':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1); // First day of last month
                    endDate = new Date(today.getFullYear(), today.getMonth(), 0); // Last day of last month
                    break;
                case 'next_month':
                    startDate = new Date(today.getFullYear(), today.getMonth() + 1, 1); // First day of next month
                    endDate = new Date(today.getFullYear(), today.getMonth() + 2, 0); // Last day of next month
                    break;
            }
            
            // Format dates for input fields
            if (startDate && endDate) {
                const formatDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };
                
                startDateInput.value = formatDate(startDate);
                endDateInput.value = formatDate(endDate);
                
                // Reload DataTable with new filter dates
                if (value !== 'custom' && window.staffTable) {
                    console.log('Reloading DataTable for filter:', value);
                    console.log('Current start_date:', startDateInput.value);
                    console.log('Current end_date:', endDateInput.value);
                    console.log('Current date_filter:', value);
                    
                    // Force DataTable to reload with new parameters
                    window.staffTable.ajax.reload();
                } else if (value !== 'custom') {
                    console.log('DataTable not available yet, waiting for initialization...');
                    // Wait for DataTable to be ready
                    setTimeout(() => {
                        if (window.staffTable) {
                            console.log('DataTable now available, reloading...');
                            window.staffTable.ajax.reload();
                        }
                    }, 500);
                }
            }
        }
    }

    // Export functions
    function exportToExcel() {
        const url = new URL('{{ route("leave-requests.staff-export-excel", $data["user"]->id) }}');
        
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
        
        console.log('Export Excel URL:', url.toString());
        
        // Create download link for Excel
        const link = document.createElement('a');
        link.href = url.toString();
        link.download = 'leave_requests_{{ $data["user"]->u_nip ?? $data["user"]->id }}.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportToPDF() {
        const url = new URL('{{ route("leave-requests.staff-export-pdf", $data["user"]->id) }}');
        
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
        
        console.log('Export PDF URL:', url.toString());
        
        // Create download link for PDF
        const link = document.createElement('a');
        link.href = url.toString();
        link.download = 'leave_requests_{{ $data["user"]->u_nip ?? $data["user"]->id }}.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Update statistics cards based on table data
    function updateStatisticsCards(data) {
        let approvedCount = 0;
        let pendingCount = 0;
        let rejectedCount = 0;
        let totalDays = 0;
        
        data.forEach(function(item) {
            // Count by status
            if (item.status === 'approved') {
                approvedCount++;
            } else if (item.status === 'pending') {
                pendingCount++;
            } else if (item.status === 'rejected') {
                rejectedCount++;
            }
            
            // Calculate total days
            if (item.duration && item.duration_type === 'days') {
                totalDays += parseInt(item.duration);
            } else if (item.duration && item.duration_type === 'hours') {
                // Convert hours to days (assuming 8 hours = 1 day)
                totalDays += Math.ceil(parseInt(item.duration) / 8);
            }
        });
        
        // Update the statistics cards
        document.getElementById('approvedCount').textContent = approvedCount;
        document.getElementById('pendingCount').textContent = pendingCount;
        document.getElementById('rejectedCount').textContent = rejectedCount;
        document.getElementById('totalDays').textContent = totalDays;
        
        console.log('Statistics updated:', {
            approved: approvedCount,
            pending: pendingCount,
            rejected: rejectedCount,
            totalDays: totalDays
        });
    }

    // Load leave statistics
    function loadLeaveStats() {
        const url = new URL('{{ route("leave-requests.staff-stats", $data["user"]->id) }}');
        
        // Add current filters to URL
        const dateFilter = document.getElementById('date_filter').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (dateFilter && dateFilter !== 'custom') {
            url.searchParams.append('date_filter', dateFilter);
        }
        if (startDate) {
            url.searchParams.append('start_date', startDate);
        }
        if (endDate) {
            url.searchParams.append('end_date', endDate);
        }
        
        $.ajax({
            url: url.toString(),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                let statsHtml = '';
                if (data.length > 0) {
                    data.forEach(function(stat) {
                        statsHtml += `
                            <div class="text-muted">
                                <strong>${stat.leave_type_name}:</strong> 
                                ${stat.total_requests} requests, 
                                ${stat.total_days} days, 
                                ${stat.total_hours} hours
                            </div>
                        `;
                    });
                } else {
                    statsHtml = '<div class="text-muted">No leave data found</div>';
                }
                document.getElementById('leaveStats').innerHTML = statsHtml;
            },
            error: function(xhr, status, error) {
                console.error('Error loading leave stats:', error);
                document.getElementById('leaveStats').innerHTML = '<div class="text-muted">Error loading statistics</div>';
            }
        });
    }

    // Robust DataTable initialization with retry mechanism
    function initDataTable() {
        if (typeof $.fn.DataTable === 'undefined') {
            console.log('DataTable not available, retrying in 100ms...');
            setTimeout(initDataTable, 100);
            return;
        }

        try {
            console.log('Initializing staff leave requests DataTable...');
            
            window.staffTable = $('#staffTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false,
                dom: 'rt<"pagination-class"ip>',
                ajax: {
                    url: "{{ route('leave-requests.staff-datatables', $data['user']->id) }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.date_filter = $('#date_filter').val();
                        d.status = $('#status').val();
                        
                        console.log('Staff Datatables AJAX Data sent:', d);
                    },
                    dataSrc: function(json) {
                        console.log('Staff Datatables response:', json);
                        
                        // Update statistics cards based on table data
                        if (json.data && json.data.length > 0) {
                            console.log('Updating statistics cards with data:', json.data);
                            updateStatisticsCards(json.data);
                        } else {
                            console.log('No data to update statistics cards');
                            // Reset statistics to 0 when no data
                            document.getElementById('approvedCount').textContent = '0';
                            document.getElementById('pendingCount').textContent = '0';
                            document.getElementById('rejectedCount').textContent = '0';
                            document.getElementById('totalDays').textContent = '0';
                        }
                        
                        return json.data || [];
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                    { 
                        data: 'start_date', 
                        name: 'start_date', 
                        width: '12%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return new Date(data).toLocaleDateString('id-ID');
                            }
                            return data;
                        }
                    },
                    { 
                        data: 'end_date', 
                        name: 'end_date', 
                        width: '12%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return new Date(data).toLocaleDateString('id-ID');
                            }
                            return data;
                        }
                    },
                    { 
                        data: 'duration', 
                        name: 'duration', 
                        width: '10%',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return data + ' ' + row.duration_type;
                            }
                            return data + ' ' + row.duration_type;
                        }
                    },
                    { data: 'leave_type_name', name: 'leave_type_name', width: '15%' },
                    { 
                        data: 'status', 
                        name: 'status', 
                        width: '10%',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                let badgeClass = 'light-green';
                                if (data === 'approved') badgeClass = 'light-blue';
                                else if (data === 'pending') badgeClass = 'light-yellow';
                                else if (data === 'rejected') badgeClass = 'light-red';
                                return '<span class="badge badge-' + badgeClass + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                            }
                            return data;
                        }
                    },
                    { data: 'reason', name: 'reason', width: '20%' },
                    { 
                        data: 'created_at', 
                        name: 'created_at', 
                        width: '16%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return new Date(data).toLocaleDateString('id-ID') + ' ' + new Date(data).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                            }
                            return data;
                        }
                    }
                ],
                order: [[1, 'desc']], // Sort by start date descending
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                language: {
                    "emptyTable": "No data available in table",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "lengthMenu": "Show _MENU_ entries",
                    "loadingRecords": "Loading...",
                    "processing": "Processing...",
                    "search": "Search:",
                    "zeroRecords": "No matching records found"
                }
            });

            console.log('Staff leave requests DataTable initialized successfully');
            
        } catch (error) {
            console.error('Error initializing staff leave requests DataTable:', error);
            // Retry after a delay
            setTimeout(initDataTable, 500);
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Initialize date filter on page load
        console.log('Document ready - Initializing date filter...');
        const dateFilter = document.getElementById('date_filter');
        if (dateFilter) {
            console.log('Date filter found, current value:', dateFilter.value);
            handleDateFilterChange(dateFilter.value);
        } else {
            console.log('Date filter not found!');
        }
        
        // Initialize DataTable
        initDataTable();
        
        // Load initial statistics
        loadLeaveStats();
        
        // Set initial statistics to 0
        document.getElementById('approvedCount').textContent = '0';
        document.getElementById('pendingCount').textContent = '0';
        document.getElementById('rejectedCount').textContent = '0';
        document.getElementById('totalDays').textContent = '0';
        
        // Handle filter form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault(); // Prevent form submission
            
            // Reload DataTable
            if (window.staffTable) {
                window.staffTable.ajax.reload();
            }
            
            // Reload statistics
            loadLeaveStats();
        });
        
        // Handle individual filter changes
        $('#date_filter, #start_date, #end_date, #status').on('change', function() {
            // Reload DataTable when any filter changes
            if (window.staffTable) {
                window.staffTable.ajax.reload();
            }
        });
    });
</script>
